<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Offer;
use App\Models\User;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    private const SORTS = [
        'newest' => ['Terbaru', 'created_at', 'desc'],
        'popular' => ['Paling Populer', 'views', 'desc'],
        'oldest' => ['Terlama', 'created_at', 'asc'],
        'title' => ['Nama A-Z', 'title', 'asc'],
    ];

    public function index(Request $request)
    {
        $search = trim((string) $request->query('search', ''));
        $category = in_array($request->query('category'), Item::CATEGORIES, true) ? $request->query('category') : '';
        $condition = in_array($request->query('condition'), Item::CONDITIONS, true) ? $request->query('condition') : '';
        $sort = array_key_exists((string) $request->query('sort'), self::SORTS) ? $request->query('sort') : 'newest';

        [, $sortColumn, $sortDir] = self::SORTS[$sort];

        $items = Item::with('user')
            ->withCount('favorites')
            ->available()
            ->when($search !== '', function ($q) use ($search) {
                $like = '%' . addcslashes($search, '%_\\') . '%';
                $q->where(fn ($w) => $w->where('title', 'like', $like)->orWhere('description', 'like', $like));
            })
            ->when($category, fn ($q) => $q->where('category', $category))
            ->when($condition, fn ($q) => $q->where('condition_item', $condition))
            ->orderBy($sortColumn, $sortDir)
            ->orderByDesc('id')
            ->paginate(12)
            ->withQueryString()
            ->fragment('items');

        $stats = [
            'items' => Item::available()->count(),
            'users' => User::count(),
            'trades' => Offer::where('status', Offer::COMPLETED)->count(),
        ];

        // Barang yang sudah ditawar user (penawaran masih menunggu)
        $offeredIds = $request->user()
            ? Offer::where('sender_id', $request->user()->id)->where('status', Offer::PENDING)
                ->pluck('item_wanted_id')->flip()->all()
            : [];

        $favoriteIds = $request->user()
            ? $request->user()->favorites()->pluck('item_id')->flip()->all()
            : [];

        // Jumlah barang per kategori untuk kartu kategori
        $categoryCounts = Item::available()->selectRaw('category, COUNT(*) AS c')
            ->groupBy('category')->pluck('c', 'category')->all();

        // Foto untuk adegan 3D di hero
        $heroPhotos = Item::available()->latest()->limit(6)->get()->map(fn ($i) => $i->photo_url)->values();

        return view('home', [
            'items' => $items,
            'favoriteIds' => $favoriteIds,
            'categoryCounts' => $categoryCounts,
            'heroPhotos' => $heroPhotos,
            'members' => User::latest()->limit(5)->get(),
            'stats' => $stats,
            'offeredIds' => $offeredIds,
            'filters' => compact('search', 'category', 'condition', 'sort'),
            'sortOptions' => array_map(fn ($s) => $s[0], self::SORTS),
            'isFiltered' => $search !== '' || $category || $condition || $sort !== 'newest',
        ]);
    }
}
