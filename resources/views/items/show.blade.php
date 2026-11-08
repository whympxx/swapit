@extends('layouts.app')

@section('title', $item->title)

@section('content')
    <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('home') . '#items' }}" class="back-link"><i class="fas fa-arrow-left"></i> Kembali</a>

    <div class="item-detail-container">
        <div class="item-detail-visual reveal">
            <div class="item-detail-image" data-tilt="8">
                <img src="{{ $item->photo_url }}" alt="{{ $item->title }}">
                <span class="item-status status-{{ $item->status }}">{{ $item->status_label }}</span>
            </div>
        </div>

        <div class="item-detail-info">
            <div class="item-detail-header reveal">
                <div class="item-tags">
                    <a href="{{ route('home', ['category' => $item->category]) }}#items" class="tag tag-lime"><i class="fas fa-{{ $item->category_icon }}"></i> {{ $item->category }}</a>
                    <span class="tag"><i class="fas fa-wand-magic-sparkles"></i> {{ $item->condition_item }}</span>
                </div>
                <h1>{{ $item->title }}</h1>
                <div class="item-detail-sub">
                    <span><i class="far fa-clock"></i> Diposting {{ $item->created_at->diffForHumans() }}</span>
                    <span><i class="far fa-eye"></i> {{ $item->views }}x dilihat</span>
                    <span><i class="far fa-heart"></i> <span data-fav-count>{{ $favoriteCount }}</span> favorit</span>
                </div>
            </div>

            <div class="item-detail-meta reveal" style="--d: 80ms">
                <div class="meta-tile"><i class="fas fa-{{ $item->category_icon }}"></i><small>Kategori</small><strong>{{ $item->category }}</strong></div>
                <div class="meta-tile"><i class="fas fa-wand-magic-sparkles"></i><small>Kondisi</small><strong>{{ $item->condition_item }}</strong></div>
                <div class="meta-tile"><i class="fas fa-circle-check"></i><small>Status</small><strong>{{ $item->status_label }}</strong></div>
            </div>

            <div class="item-detail-description reveal" style="--d: 120ms">
                <div class="block-label">Deskripsi</div>
                <p class="preserve-lines">{{ $item->description }}</p>
            </div>

            <div class="item-detail-owner reveal" style="--d: 160ms">
                <div class="block-label">Pemilik</div>
                <a class="owner-info" href="{{ route('profile.show', $item->user) }}">
                    @include('partials.avatar', ['user' => $item->user, 'class' => 'avatar-lg'])
                    <div>
                        <strong>{{ $item->user->full_name }}</strong>
                        <p>{{ '@' . $item->user->username }}@if ($item->user->university) · {{ $item->user->university }}@endif</p>
                        <p class="rating-inline">
                            @if ($ownerRating['count'])
                                @include('partials.stars', ['value' => $ownerRating['avg']])
                                <strong>{{ number_format($ownerRating['avg'], 1) }}</strong> ({{ $ownerRating['count'] }} ulasan) ·
                            @endif
                            {{ $ownerTrades }} barter sukses
                        </p>
                    </div>
                    <i class="fas fa-chevron-right"></i>
                </a>
            </div>

            <div class="item-detail-actions reveal" style="--d: 200ms">
                @if ($isOwner)
                    @if ($pendingOfferCount > 0)
                        <a href="{{ route('offers.index') }}" class="btn btn-primary btn-lg btn-block"><i class="fas fa-handshake"></i> {{ $pendingOfferCount }} Penawaran Masuk</a>
                    @endif
                    @if ($item->status !== 'traded')
                        <a href="{{ route('items.edit', $item) }}" class="btn btn-lg"><i class="fas fa-pen"></i> Edit</a>
                    @endif
                    @if ($item->isAvailable())
                        <form method="POST" action="{{ route('items.destroy', $item) }}" class="inline-form"
                              onsubmit="return confirm('Yakin ingin menghapus barang ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-lg"><i class="fas fa-trash"></i> Hapus</button>
                        </form>
                    @endif
                @elseif ($myPendingOffer)
                    <div class="alert alert-info" data-persist>
                        <i class="fas fa-info-circle"></i> Kamu sudah mengirim penawaran untuk barang ini. Tunggu respon pemilik ya.
                    </div>
                    <a href="{{ route('offers.index', ['tab' => 'sent']) }}" class="btn btn-lg btn-block"><i class="fas fa-paper-plane"></i> Lihat Penawaranku</a>
                @elseif (auth()->check() && $item->isAvailable())
                    <a href="{{ route('offers.create', $item) }}" class="btn btn-primary btn-lg btn-block"><i class="fas fa-arrow-right-arrow-left"></i> Tawarkan Barter</a>
                @elseif (auth()->guest())
                    <a href="{{ route('login') }}" class="btn btn-primary btn-lg btn-block"><i class="fas fa-right-to-bracket"></i> Masuk untuk Menawar</a>
                @else
                    <div class="alert alert-warning" data-persist><i class="fas fa-lock"></i> Barang ini sedang tidak menerima penawaran.</div>
                @endif

                @if (!$isOwner)
                    @auth
                        <button type="button" class="fav-btn fav-btn-lg {{ $isFavorited ? 'active' : '' }}" data-fav-url="{{ route('favorites.toggle', $item) }}"
                                aria-label="{{ $isFavorited ? 'Hapus dari favorit' : 'Simpan ke favorit' }}" aria-pressed="{{ $isFavorited ? 'true' : 'false' }}" title="Favorit">
                            <i class="fa-regular fa-heart"></i><i class="fa-solid fa-heart"></i>
                        </button>
                    @endauth
                @endif
                <button type="button" class="btn btn-lg" data-share="{{ route('items.show', $item) }}" data-share-title="{{ $item->title }} · SWAP-IT" title="Bagikan">
                    <i class="fas fa-share-nodes"></i>
                </button>
            </div>
        </div>
    </div>

    @if ($otherItems->isNotEmpty())
        <section class="related-section">
            <h2>Barang lain dari {{ '@' . $item->user->username }}</h2>
            <div class="related-grid">
                @foreach ($otherItems as $other)
                    <a class="related-card reveal" href="{{ route('items.show', $other) }}" style="--d: {{ $loop->index * 60 }}ms">
                        <img src="{{ $other->photo_url }}" alt="{{ $other->title }}" loading="lazy">
                        <div>
                            <strong>{{ $other->title }}</strong>
                            <span>{{ $other->category }} · {{ $other->condition_item }}</span>
                        </div>
                    </a>
                @endforeach
            </div>
        </section>
    @endif
@endsection
