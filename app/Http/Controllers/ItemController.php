<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Offer;
use App\Models\UserNotification;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ItemController extends Controller
{
    public function mine(Request $request)
    {
        $status = array_key_exists((string) $request->query('status'), Item::STATUS_LABELS)
            ? $request->query('status') : 'all';

        $base = $request->user()->items();
        $counts = (clone $base)->selectRaw('status, COUNT(*) as c')->groupBy('status')->pluck('c', 'status')->all();
        $counts['all'] = array_sum($counts);

        $items = $base
            ->withCount(['incomingOffers as pending_offers_count' => fn ($q) => $q->where('status', Offer::PENDING)])
            ->when($status !== 'all', fn ($q) => $q->where('status', $status))
            ->latest()
            ->get();

        return view('items.mine', compact('items', 'status', 'counts'));
    }

    public function show(Request $request, Item $item)
    {
        $item->load('user');
        $user = $request->user();
        $isOwner = $user && $user->id === $item->user_id;

        // Hitung "dilihat" sekali per sesi, tidak termasuk pemilik
        $seen = $request->session()->get('viewed_items', []);
        if (!$isOwner && !in_array($item->id, $seen, true)) {
            $item->increment('views');
            $request->session()->put('viewed_items', array_slice(array_merge($seen, [$item->id]), -200));
        }

        $myPendingOffer = $user && !$isOwner
            ? Offer::where('sender_id', $user->id)->where('item_wanted_id', $item->id)->where('status', Offer::PENDING)->first()
            : null;

        $pendingOfferCount = $isOwner
            ? $item->incomingOffers()->where('status', Offer::PENDING)->count()
            : 0;

        $otherItems = Item::available()->where('user_id', $item->user_id)->whereKeyNot($item->id)
            ->latest()->limit(4)->get();

        return view('items.show', [
            'item' => $item,
            'isOwner' => $isOwner,
            'myPendingOffer' => $myPendingOffer,
            'pendingOfferCount' => $pendingOfferCount,
            'ownerTrades' => $item->user->completedTradesCount(),
            'ownerRating' => $item->user->ratingSummary(),
            'favoriteCount' => $item->favorites()->count(),
            'isFavorited' => $user && $item->favorites()->where('user_id', $user->id)->exists(),
            'otherItems' => $otherItems,
        ]);
    }

    public function create()
    {
        return view('items.create');
    }

    public function store(Request $request)
    {
        $data = $this->validated($request, true);
        $data['photo'] = $this->storePhoto($request->file('photo'));
        $data['user_id'] = $request->user()->id;

        $item = Item::create($data);

        return redirect()->route('items.mine')->with('success', 'Barang "' . $item->title . '" berhasil diposting!');
    }

    public function edit(Request $request, Item $item)
    {
        $this->authorizeOwner($request, $item);
        if ($item->status === 'traded') {
            return redirect()->route('items.mine')->with('error', 'Barang yang sudah ditukar tidak bisa diedit.');
        }

        return view('items.edit', compact('item'));
    }

    public function update(Request $request, Item $item)
    {
        $this->authorizeOwner($request, $item);
        if ($item->status === 'traded') {
            return redirect()->route('items.mine')->with('error', 'Barang yang sudah ditukar tidak bisa diedit.');
        }

        $data = $this->validated($request, false);
        $oldPhoto = null;

        if ($request->hasFile('photo')) {
            $oldPhoto = $item->photo;
            $data['photo'] = $this->storePhoto($request->file('photo'));
        }

        $item->update($data);

        // Foto lama dihapus setelah data berhasil disimpan
        if ($oldPhoto) {
            $this->deletePhoto($oldPhoto);
        }

        return redirect()->route('items.mine')->with('success', 'Barang berhasil diupdate!');
    }

    public function destroy(Request $request, Item $item)
    {
        $this->authorizeOwner($request, $item);

        // Barang dalam proses / sudah ditukar menyimpan riwayat chat pihak lain
        if ($item->status === 'pending') {
            return back()->with('error', 'Barang sedang dalam proses barter. Selesaikan atau batalkan barter terlebih dahulu.');
        }
        if ($item->status === 'traded') {
            return back()->with('error', 'Barang yang sudah ditukar disimpan sebagai riwayat dan tidak bisa dihapus.');
        }

        // Kabari pihak lain di penawaran yang masih menunggu
        Offer::where('status', Offer::PENDING)
            ->where(fn ($q) => $q->where('item_wanted_id', $item->id)->orWhere('item_offered_id', $item->id))
            ->get()
            ->each(function (Offer $offer) use ($request, $item) {
                $other = $offer->sender_id === $request->user()->id ? $offer->receiver_id : $offer->sender_id;
                UserNotification::send($other, 'Penawaran Dibatalkan',
                    'Barang "' . $item->title . '" telah dihapus oleh pemiliknya, sehingga penawaran terkait dibatalkan.',
                    'offers');
            });

        $photo = $item->photo;
        $item->delete();
        $this->deletePhoto($photo);

        return redirect()->route('items.mine')->with('success', 'Barang berhasil dihapus!');
    }

    private function validated(Request $request, bool $photoRequired): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'category' => ['required', Rule::in(Item::CATEGORIES)],
            'condition_item' => ['required', Rule::in(Item::CONDITIONS)],
            'description' => ['required', 'string', 'max:5000'],
            'photo' => [$photoRequired ? 'required' : 'nullable', 'image', 'mimes:jpg,jpeg,png,gif,webp', 'max:5120'],
        ]);
    }

    private function storePhoto(UploadedFile $file): string
    {
        $name = Str::uuid() . '.' . $file->extension();
        $file->move(public_path('uploads/items'), $name);
        return $name;
    }

    private function deletePhoto(?string $photo): void
    {
        if (!$photo) {
            return;
        }
        $path = public_path('uploads/items/' . basename($photo));
        if (is_file($path)) {
            @unlink($path);
        }
    }

    private function authorizeOwner(Request $request, Item $item): void
    {
        abort_unless($request->user()->id === $item->user_id, 403, 'Ini bukan barang Anda.');
    }
}
