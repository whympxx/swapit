<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Offer;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function index(Request $request)
    {
        $uid = $request->user()->id;

        $conversations = Offer::with(['sender', 'receiver', 'wantedItem', 'offeredItem'])
            ->involving($uid)
            ->where(fn ($q) => $q->whereIn('status', [Offer::ACCEPTED, Offer::COMPLETED])
                ->orWhere(fn ($c) => $c->where('status', Offer::CANCELLED)->has('messages')))
            ->withCount(['messages as unread_count' => fn ($q) => $q->where('receiver_id', $uid)->where('is_read', false)])
            ->withMax('messages as last_message_at', 'created_at')
            ->get()
            ->each(fn (Offer $o) => $o->setRelation('lastMessage', $o->messages()->latest('id')->first()))
            ->sortByDesc(fn (Offer $o) => [
                $o->status === Offer::ACCEPTED ? 1 : 0,
                $o->last_message_at ?? $o->updated_at?->toDateTimeString(),
            ])
            ->values();

        return view('chat.index', compact('conversations'));
    }

    public function show(Request $request, Offer $offer)
    {
        $user = $request->user();
        abort_unless($offer->involves($user), 403);

        // Chat hanya untuk penawaran yang sudah diterima (riwayat tetap bisa dibaca)
        if (!in_array($offer->status, [Offer::ACCEPTED, Offer::COMPLETED, Offer::CANCELLED], true)
            || ($offer->status === Offer::CANCELLED && !$offer->messages()->exists())) {
            return redirect()->route('offers.index')->with('info', 'Chat tersedia setelah penawaran diterima.');
        }

        $offer->load(['sender', 'receiver', 'wantedItem', 'offeredItem', 'reviews']);
        $offer->messages()->where('receiver_id', $user->id)->where('is_read', false)->update(['is_read' => true]);

        $other = $offer->otherParty($user);
        $canSend = $offer->status === Offer::ACCEPTED;

        return view('chat.show', [
            'offer' => $offer,
            'other' => $other,
            'canSend' => $canSend,
            'canReview' => $offer->canBeReviewedBy($user),
            'waNumber' => $canSend ? $other->whatsappNumber() : null,
            'messages' => $offer->messages()->with('sender')->orderBy('id')->get(),
        ]);
    }

    /** Polling pesan baru (JSON) */
    public function messages(Request $request, Offer $offer)
    {
        $user = $request->user();
        abort_unless($offer->involves($user), 403);

        $offer->messages()->where('receiver_id', $user->id)->where('is_read', false)->update(['is_read' => true]);

        $messages = $offer->messages()->with('sender')
            ->where('id', '>', max(0, (int) $request->query('last_id', 0)))
            ->orderBy('id')
            ->get()
            ->map(fn (Message $m) => $m->toChatArray($user->id));

        return response()->json([
            'success' => true,
            'messages' => $messages,
            'offer_status' => $offer->status,
        ]);
    }

    public function send(Request $request, Offer $offer)
    {
        $user = $request->user();
        abort_unless($offer->involves($user), 403);

        if ($offer->status !== Offer::ACCEPTED) {
            return response()->json(['success' => false, 'message' => 'Barter ini sudah tidak aktif, pesan tidak bisa dikirim'], 422);
        }

        $data = $request->validate(['message' => ['required', 'string', 'max:2000']]);
        $text = trim($data['message']);
        if ($text === '') {
            return response()->json(['success' => false, 'message' => 'Pesan tidak boleh kosong'], 422);
        }

        $message = $offer->messages()->create([
            'sender_id' => $user->id,
            'receiver_id' => $offer->otherParty($user)->id,
            'message' => $text,
        ])->load('sender');

        return response()->json(['success' => true, 'message' => $message->toChatArray($user->id)]);
    }
}
