<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Offer;
use App\Models\UserNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class OfferController extends Controller
{
    private const ORDER = "FIELD(status, 'pending', 'accepted', 'completed', 'rejected', 'cancelled')";

    public function index(Request $request)
    {
        $user = $request->user();
        $with = ['sender', 'receiver', 'wantedItem', 'offeredItem', 'reviews'];

        $received = Offer::with($with)->where('receiver_id', $user->id)
            ->orderByRaw(self::ORDER)->latest('updated_at')->get();
        $sent = Offer::with($with)->where('sender_id', $user->id)
            ->orderByRaw(self::ORDER)->latest('updated_at')->get();

        return view('offers.index', [
            'received' => $received,
            'sent' => $sent,
            'tab' => $request->query('tab') === 'sent' ? 'sent' : 'received',
            'receivedPending' => $received->where('status', Offer::PENDING)->count(),
            'sentActive' => $sent->whereIn('status', [Offer::PENDING, Offer::ACCEPTED])->count(),
        ]);
    }

    public function create(Request $request, Item $item)
    {
        if ($redirect = $this->guardOfferable($request, $item)) {
            return $redirect;
        }

        $myItems = $request->user()->items()->available()->latest()->get();

        return view('offers.create', compact('item', 'myItems'));
    }

    public function store(Request $request, Item $item)
    {
        if ($redirect = $this->guardOfferable($request, $item)) {
            return $redirect;
        }

        $user = $request->user();
        $data = $request->validate([
            'offered_item_id' => ['required', 'integer', Rule::exists('items', 'id')
                ->where('user_id', $user->id)->where('status', 'available')],
            'message' => ['nullable', 'string', 'max:1000'],
        ], [
            'offered_item_id.required' => 'Pilih barang yang ingin Anda tawarkan!',
            'offered_item_id.exists' => 'Barang yang dipilih tidak valid atau sudah tidak tersedia.',
        ]);

        $offer = Offer::create([
            'sender_id' => $user->id,
            'receiver_id' => $item->user_id,
            'item_wanted_id' => $item->id,
            'item_offered_id' => $data['offered_item_id'],
            'message' => $data['message'] ?? null,
        ]);

        UserNotification::send($item->user_id, 'Penawaran Barter Baru',
            $user->username . ' menawarkan "' . $offer->offeredItem->title . '" untuk ditukar dengan "' . $item->title . '"',
            'offers');

        return redirect()->route('offers.index', ['tab' => 'sent'])->with('success', 'Penawaran berhasil dikirim!');
    }

    /** Penerima menerima penawaran: barang jadi "Dalam Proses", penawaran lain atas barang yang sama dibatalkan. */
    public function accept(Request $request, Offer $offer)
    {
        $user = $request->user();
        abort_unless($offer->receiver_id === $user->id, 403);
        if ($offer->status !== Offer::PENDING) {
            return back()->with('error', 'Penawaran ini tidak bisa diterima.');
        }

        $itemIds = [$offer->item_wanted_id, $offer->item_offered_id];

        $ok = DB::transaction(function () use ($offer, $itemIds, $user) {
            $items = Item::whereIn('id', $itemIds)->lockForUpdate()->get();
            if ($items->count() !== 2 || $items->contains(fn ($i) => $i->status !== 'available')) {
                $offer->update(['status' => Offer::CANCELLED]);
                return false;
            }

            $offer->update(['status' => Offer::ACCEPTED]);
            Item::whereIn('id', $itemIds)->update(['status' => 'pending']);

            // Satu barang tidak boleh berada di dua barter sekaligus
            $others = Offer::where('status', Offer::PENDING)->whereKeyNot($offer->id)
                ->where(fn ($q) => $q->whereIn('item_wanted_id', $itemIds)->orWhereIn('item_offered_id', $itemIds))
                ->get();

            foreach ($others as $other) {
                $other->update(['status' => Offer::CANCELLED]);
                foreach ([$other->sender_id, $other->receiver_id] as $party) {
                    if ($party !== $user->id) {
                        UserNotification::send($party, 'Penawaran Dibatalkan Otomatis',
                            'Salah satu barang dalam penawaran Anda sudah masuk proses barter lain.', 'offers');
                    }
                }
            }

            UserNotification::send($offer->sender_id, 'Penawaran Diterima',
                $user->username . ' menerima penawaran ' . $offer->dealLabel() . '. Silakan chat untuk mengatur pertemuan.',
                'chat/' . $offer->id);

            return true;
        });

        if (!$ok) {
            return back()->with('error', 'Salah satu barang sudah tidak tersedia, penawaran otomatis dibatalkan.');
        }

        return redirect()->route('chat.show', $offer)->with('success', 'Penawaran diterima! Silakan chat untuk mengatur pertemuan.');
    }

    public function reject(Request $request, Offer $offer)
    {
        $user = $request->user();
        abort_unless($offer->receiver_id === $user->id, 403);
        if ($offer->status !== Offer::PENDING) {
            return back()->with('error', 'Penawaran ini tidak bisa ditolak.');
        }

        $offer->update(['status' => Offer::REJECTED]);
        UserNotification::send($offer->sender_id, 'Penawaran Ditolak',
            $user->username . ' menolak penawaran ' . $offer->dealLabel() . '. Coba tawarkan barang lain!', 'offers?tab=sent');

        return back()->with('success', 'Penawaran ditolak.');
    }

    /** Pengirim menarik penawaran yang masih menunggu. */
    public function cancel(Request $request, Offer $offer)
    {
        $user = $request->user();
        abort_unless($offer->sender_id === $user->id, 403);
        if ($offer->status !== Offer::PENDING) {
            return back()->with('error', 'Penawaran ini tidak bisa dibatalkan.');
        }

        $offer->update(['status' => Offer::CANCELLED]);
        UserNotification::send($offer->receiver_id, 'Penawaran Ditarik',
            $user->username . ' menarik penawaran ' . $offer->dealLabel() . '.', 'offers');

        return redirect()->route('offers.index', ['tab' => 'sent'])->with('success', 'Penawaran berhasil dibatalkan.');
    }

    /** Salah satu pihak menandai barter selesai: barang jadi "Sudah Ditukar". */
    public function complete(Request $request, Offer $offer)
    {
        $user = $request->user();
        abort_unless($offer->involves($user), 403);
        if ($offer->status !== Offer::ACCEPTED) {
            return back()->with('error', 'Hanya barter yang sedang berjalan yang bisa diselesaikan.');
        }

        DB::transaction(function () use ($offer, $user) {
            $offer->update(['status' => Offer::COMPLETED]);
            Item::whereIn('id', [$offer->item_wanted_id, $offer->item_offered_id])->update(['status' => 'traded']);
            UserNotification::send($offer->otherParty($user)->id, 'Barter Selesai 🎉',
                $user->username . ' menandai barter ' . $offer->dealLabel() . ' sebagai selesai. Terima kasih sudah berbagi!',
                'chat/' . $offer->id);
        });

        return back()->with('success', 'Barter ditandai selesai. Terima kasih sudah ikut mengurangi sampah! 🌱');
    }

    /** Salah satu pihak membatalkan barter yang sudah diterima: barang kembali "Tersedia". */
    public function cancelDeal(Request $request, Offer $offer)
    {
        $user = $request->user();
        abort_unless($offer->involves($user), 403);
        if ($offer->status !== Offer::ACCEPTED) {
            return back()->with('error', 'Barter ini tidak bisa dibatalkan.');
        }

        DB::transaction(function () use ($offer, $user) {
            $offer->update(['status' => Offer::CANCELLED]);
            Item::whereIn('id', [$offer->item_wanted_id, $offer->item_offered_id])
                ->where('status', 'pending')->update(['status' => 'available']);
            UserNotification::send($offer->otherParty($user)->id, 'Barter Dibatalkan',
                $user->username . ' membatalkan barter ' . $offer->dealLabel() . '. Barang Anda kembali tersedia.', 'offers');
        });

        return back()->with('success', 'Barter dibatalkan. Kedua barang kembali tersedia.');
    }

    /** Pastikan barang bisa ditawar; kembalikan redirect jika tidak. */
    private function guardOfferable(Request $request, Item $item)
    {
        $user = $request->user();

        if ($item->user_id === $user->id) {
            return redirect()->route('items.show', $item)->with('error', 'Anda tidak bisa menawar barang sendiri.');
        }
        if (!$item->isAvailable()) {
            return redirect()->route('items.show', $item)->with('error', 'Barang ini sedang tidak menerima penawaran.');
        }

        $exists = Offer::where('sender_id', $user->id)->where('item_wanted_id', $item->id)
            ->where('status', Offer::PENDING)->exists();
        if ($exists) {
            return redirect()->route('offers.index', ['tab' => 'sent'])
                ->with('info', 'Anda sudah mengirim penawaran untuk barang ini. Tunggu respon pemilik.');
        }

        return null;
    }
}
