<?php

namespace App\Http\Controllers;

use App\Models\Offer;
use App\Models\Review;
use App\Models\UserNotification;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /** Ulasan untuk partner barter, hanya setelah barter selesai dan sekali per orang. */
    public function store(Request $request, Offer $offer)
    {
        $user = $request->user();
        abort_unless($offer->involves($user), 403);

        if (!$offer->canBeReviewedBy($user)) {
            return back()->with('error', 'Ulasan hanya bisa diberikan sekali setelah barter selesai.');
        }

        $data = $request->validate([
            'rating' => ['required', 'integer', 'between:1,5'],
            'comment' => ['nullable', 'string', 'max:500'],
        ], ['rating.required' => 'Pilih jumlah bintang dulu ya.']);

        $offer->loadMissing(['sender', 'receiver', 'wantedItem', 'offeredItem']);
        $partner = $offer->otherParty($user);

        Review::create([
            'offer_id' => $offer->id,
            'reviewer_id' => $user->id,
            'reviewee_id' => $partner->id,
            'rating' => $data['rating'],
            'comment' => trim((string) ($data['comment'] ?? '')) ?: null,
        ]);

        UserNotification::send($partner->id, 'Ulasan Baru ⭐',
            $user->username . ' memberi Anda ' . $data['rating'] . ' bintang untuk barter ' . $offer->dealLabel() . '.',
            'users/' . $partner->id);

        return back()->with('success', 'Terima kasih! Ulasan Anda membantu komunitas jadi lebih terpercaya.');
    }
}
