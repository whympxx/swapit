<?php

namespace App\Http\Controllers;

use App\Models\Offer;
use App\Models\User;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function me(Request $request)
    {
        return $this->show($request, $request->user());
    }

    /** Profil publik: info pengguna + barang yang tersedia */
    public function show(Request $request, User $user)
    {
        $isMe = $request->user()?->id === $user->id;

        return view('profile.show', [
            'user' => $user,
            'isMe' => $isMe,
            'stats' => [
                'items' => $user->items()->count(),
                'available' => $user->items()->available()->count(),
                'offers' => Offer::involving($user->id)->count(),
                'completed' => $user->completedTradesCount(),
                'favorites' => $isMe ? $user->favorites()->count() : null,
            ],
            'rating' => $user->ratingSummary(),
            'reviews' => $user->reviewsReceived()->with('reviewer')->latest('created_at')->limit(10)->get(),
            'items' => $user->items()->available()->latest()->get(),
            'history' => $isMe
                ? Offer::with(['sender', 'receiver', 'wantedItem', 'offeredItem'])->involving($user->id)
                    ->where('status', Offer::COMPLETED)->latest('updated_at')->limit(10)->get()
                : collect(),
        ]);
    }

    public function edit(Request $request)
    {
        return view('profile.settings', ['user' => $request->user()]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'full_name' => ['required', 'string', 'max:100'],
            'phone' => ['nullable', 'regex:/^\+?[0-9\s\-]{8,20}$/'],
            'university' => ['nullable', 'string', 'max:100'],
        ], ['phone.regex' => 'Nomor HP tidak valid.']);

        $request->user()->update($data);

        return back()->with('success', 'Profil berhasil diupdate!');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:6', 'confirmed', 'different:current_password'],
        ], [
            'current_password.current_password' => 'Password lama salah!',
            'password.different' => 'Password baru harus berbeda dari password lama.',
        ]);

        $user = $request->user();
        $user->password = $request->input('password');
        $user->save();

        return back()->with('success', 'Password berhasil diubah!');
    }
}
