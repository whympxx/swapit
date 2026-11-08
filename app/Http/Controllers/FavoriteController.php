<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use App\Models\Item;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function index(Request $request)
    {
        $favorites = Favorite::with('item.user')
            ->where('user_id', $request->user()->id)
            ->whereHas('item')
            ->latest('created_at')
            ->get();

        return view('favorites.index', ['favorites' => $favorites]);
    }

    /** Simpan / hapus barang dari favorit. Mengembalikan JSON untuk tombol hati. */
    public function toggle(Request $request, Item $item)
    {
        $user = $request->user();

        if ($item->user_id === $user->id) {
            $message = 'Barang sendiri tidak bisa difavoritkan.';
            return $request->expectsJson()
                ? response()->json(['success' => false, 'message' => $message], 422)
                : back()->with('error', $message);
        }

        $existing = Favorite::where('user_id', $user->id)->where('item_id', $item->id)->first();
        if ($existing) {
            $existing->delete();
            $favorited = false;
        } else {
            Favorite::create(['user_id' => $user->id, 'item_id' => $item->id]);
            $favorited = true;
        }

        $message = $favorited ? 'Disimpan ke Favorit ❤️' : 'Dihapus dari Favorit';

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'favorited' => $favorited,
                'count' => $item->favorites()->count(),
                'message' => $message,
            ]);
        }

        return back()->with('success', $message);
    }
}
