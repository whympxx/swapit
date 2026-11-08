@extends('layouts.app')

@section('title', 'Favorit')

@section('content')
    <div class="page-header">
        <h1><span class="ph-icon"><i class="fas fa-heart"></i></span> Favorit</h1>
        <p>Barang incaran yang kamu simpan. Tawar sebelum keduluan orang lain!</p>
    </div>

    <div class="items-grid" data-favorites-page>
        @forelse ($favorites as $fav)
            @include('partials.item-card', ['item' => $fav->item, 'favorited' => true])
        @empty
            <div class="empty-state">
                <i class="fas fa-heart"></i>
                <h3>Belum ada favorit</h3>
                <p>Tekan ikon hati di barang yang kamu suka untuk menyimpannya di sini.</p>
                <a href="{{ route('home') }}#items" class="btn btn-primary"><i class="fas fa-compass"></i> Jelajahi Barang</a>
            </div>
        @endforelse
    </div>
@endsection
