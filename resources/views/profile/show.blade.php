@extends('layouts.app')

@section('title', $isMe ? 'Profil Saya' : $user->full_name)

@section('content')
    <section class="profile-hero reveal">
        <div class="profile-cover"></div>
        <div class="profile-body">
            @include('partials.avatar', ['user' => $user, 'class' => 'avatar-xl'])

            <div class="profile-info">
                <h2>{{ $user->full_name }}</h2>
                <p class="username">{{ '@' . $user->username }}</p>
                <div class="profile-details">
                    @if ($rating['count'])
                        <span class="rating-inline">@include('partials.stars', ['value' => $rating['avg']]) <strong>{{ number_format($rating['avg'], 1) }}</strong> ({{ $rating['count'] }})</span>
                    @endif
                    @if ($user->university)
                        <span><i class="fas fa-building-columns"></i>{{ $user->university }}</span>
                    @endif
                    <span><i class="far fa-calendar"></i>Gabung {{ $user->created_at?->translatedFormat('M Y') }}</span>
                    @if ($isMe)
                        <span><i class="far fa-envelope"></i>{{ $user->email }}</span>
                        @if ($user->phone)
                            <span><i class="fas fa-phone"></i>{{ $user->phone }}</span>
                        @endif
                    @endif
                </div>
            </div>

            <div class="profile-actions">
                @if ($isMe)
                    <a href="{{ route('settings') }}" class="btn"><i class="fas fa-pen"></i> Edit Profil</a>
                    <a href="{{ route('items.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Posting</a>
                @else
                    <button type="button" class="btn" data-share="{{ route('profile.show', $user) }}" data-share-title="{{ $user->full_name }} di SWAP-IT">
                        <i class="fas fa-share-nodes"></i> Bagikan
                    </button>
                @endif
            </div>
        </div>
    </section>

    <div class="profile-stats">
        <div class="stat-tile accent reveal" data-tilt="8">
            <i class="fas fa-trophy"></i>
            <div class="stat-value" data-count="{{ $stats['completed'] }}">{{ $stats['completed'] }}</div>
            <div class="stat-label">Barter Sukses</div>
        </div>
        <div class="stat-tile reveal" data-tilt="8" style="--d: 60ms">
            <i class="fas fa-box-open"></i>
            <div class="stat-value" data-count="{{ $stats['available'] }}">{{ $stats['available'] }}</div>
            <div class="stat-label">Barang Tersedia</div>
        </div>
        <div class="stat-tile reveal" data-tilt="8" style="--d: 120ms">
            <i class="fas fa-star"></i>
            <div class="stat-value">{{ $rating['avg'] !== null ? number_format($rating['avg'], 1) : '–' }}</div>
            <div class="stat-label">Rating</div>
        </div>
        <div class="stat-tile reveal" data-tilt="8" style="--d: 180ms">
            @if ($isMe)
                <i class="fas fa-heart"></i>
                <div class="stat-value" data-count="{{ $stats['favorites'] }}">{{ $stats['favorites'] }}</div>
                <div class="stat-label"><a href="{{ route('favorites.index') }}">Favorit Saya</a></div>
            @else
                <i class="fas fa-handshake"></i>
                <div class="stat-value" data-count="{{ $stats['offers'] }}">{{ $stats['offers'] }}</div>
                <div class="stat-label">Penawaran</div>
            @endif
        </div>
    </div>

    <section class="related-section">
        <h2>Barang tersedia <span class="count-pill">{{ $stats['available'] }}</span></h2>
        @if ($items->isEmpty())
            <div class="empty-state">
                <i class="fas fa-box-open"></i>
                <h3>{{ $isMe ? 'Kamu belum punya barang tersedia' : 'Belum ada barang tersedia' }}</h3>
                @if ($isMe)
                    <p>Posting barang pertamamu sekarang.</p>
                    <a href="{{ route('items.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Posting Barang</a>
                @else
                    <p>Cek lagi nanti ya.</p>
                @endif
            </div>
        @else
            <div class="items-grid">
                @foreach ($items as $item)
                    @include('partials.item-card', ['item' => $item->setRelation('user', $user)])
                @endforeach
            </div>
        @endif
    </section>

    <div class="two-col">
        <section class="related-section">
            <h2><i class="fas fa-star text-lime"></i> Ulasan</h2>
            @if ($reviews->isEmpty())
                <p class="muted-text">Belum ada ulasan. Ulasan muncul setelah barter selesai.</p>
            @else
                <div class="review-list">
                    @foreach ($reviews as $review)
                        <div class="review-item reveal" style="--d: {{ $loop->index * 50 }}ms">
                            @include('partials.avatar', ['user' => $review->reviewer])
                            <div>
                                <div class="head">
                                    <a href="{{ route('profile.show', $review->reviewer) }}"><strong>{{ $review->reviewer->username }}</strong></a>
                                    @include('partials.stars', ['value' => $review->rating])
                                    <small>{{ $review->created_at?->diffForHumans() }}</small>
                                </div>
                                @if ($review->comment)
                                    <p>{{ $review->comment }}</p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </section>

        @if ($isMe)
            <section class="related-section">
                <h2><i class="fas fa-clock-rotate-left text-lime"></i> Riwayat Barter</h2>
                @if ($history->isEmpty())
                    <p class="muted-text">Belum ada barter yang selesai.</p>
                @else
                    <div class="history-list">
                        @foreach ($history as $deal)
                            <a href="{{ route('chat.show', $deal) }}" class="history-item">
                                <i class="fas fa-circle-check"></i>
                                <span>{{ $deal->offeredItem->title }} ⇄ {{ $deal->wantedItem->title }}</span>
                                <small>dengan {{ $deal->otherParty($user)->username }} · {{ $deal->updated_at->translatedFormat('d M Y') }}</small>
                            </a>
                        @endforeach
                    </div>
                @endif
            </section>
        @endif
    </div>
@endsection
