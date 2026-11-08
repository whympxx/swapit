@php
    $mode = $mode ?? 'browse';           // browse | mine
    $isMine = auth()->id() === $item->user_id;
    $alreadyOffered = $alreadyOffered ?? false;
    $favorited = $favorited ?? false;
@endphp
<article class="item-card reveal" data-tilt style="--d: {{ ($loop->index ?? 0) % 4 * 70 }}ms">
    <a class="item-image" href="{{ route('items.show', $item) }}">
        <img src="{{ $item->photo_url }}" alt="{{ $item->title }}" loading="lazy">
        <span class="item-category"><i class="fas fa-{{ $item->category_icon }}"></i>{{ $item->category }}</span>
        @if ($mode === 'mine')
            <span class="item-status status-{{ $item->status }}">{{ $item->status_label }}</span>
        @elseif ($isMine)
            <span class="item-status status-available">Barangmu</span>
        @elseif ($alreadyOffered)
            <span class="item-status status-pending">Sudah Ditawar</span>
        @elseif ($item->status !== 'available')
            <span class="item-status status-{{ $item->status }}">{{ $item->status_label }}</span>
        @endif
    </a>

    @if (!$isMine)
        @auth
            <button type="button" class="fav-btn {{ $favorited ? 'active' : '' }}" data-fav-url="{{ route('favorites.toggle', $item) }}"
                    aria-label="{{ $favorited ? 'Hapus dari favorit' : 'Simpan ke favorit' }}" aria-pressed="{{ $favorited ? 'true' : 'false' }}">
                <i class="fa-regular fa-heart"></i><i class="fa-solid fa-heart"></i>
            </button>
        @else
            <a href="{{ route('login') }}" class="fav-btn" aria-label="Masuk untuk menyimpan favorit">
                <i class="fa-regular fa-heart"></i>
            </a>
        @endauth
    @endif

    <div class="item-content">
        <h3 class="item-title"><a href="{{ route('items.show', $item) }}">{{ $item->title }}</a></h3>
        <div class="item-tags">
            <span class="tag tag-lime"><i class="fas fa-wand-magic-sparkles"></i> {{ $item->condition_item }}</span>
            @if (($item->favorites_count ?? 0) > 0)
                <span class="tag"><i class="fas fa-heart"></i> {{ $item->favorites_count }}</span>
            @endif
        </div>
        <p class="item-description">{{ Str::limit($item->description, 110) }}</p>

        <div class="item-meta">
            @if ($mode === 'mine')
                <span><i class="far fa-clock"></i> {{ $item->created_at->diffForHumans() }}</span>
                @if (($item->pending_offers_count ?? 0) > 0)
                    <a class="offer-count-link" href="{{ route('offers.index') }}"><i class="fas fa-handshake"></i> {{ $item->pending_offers_count }} tawaran</a>
                @else
                    <span class="item-stats"><span><i class="far fa-eye"></i> {{ $item->views }}</span></span>
                @endif
            @else
                <a class="item-owner" href="{{ route('profile.show', $item->user_id) }}">
                    @include('partials.avatar', ['user' => $item->user, 'class' => 'avatar-sm'])
                    <span>{{ $item->user->username }}</span>
                </a>
                <span class="item-stats">
                    <span><i class="far fa-eye"></i> {{ $item->views }}</span>
                    <span><i class="far fa-clock"></i> {{ $item->created_at->diffForHumans(null, true, true) }}</span>
                </span>
            @endif
        </div>

        @if ($mode === 'mine')
            <div class="item-actions">
                @if ($item->status !== 'traded')
                    <a href="{{ route('items.edit', $item) }}" class="btn btn-secondary btn-sm"><i class="fas fa-pen"></i> Edit</a>
                @endif
                @if ($item->isAvailable())
                    <form method="POST" action="{{ route('items.destroy', $item) }}" class="inline-form"
                          onsubmit="return confirm('Yakin ingin menghapus barang ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i> Hapus</button>
                    </form>
                @endif
            </div>
        @else
            @auth
                @if (!$isMine && !$alreadyOffered && $item->isAvailable())
                    <div class="item-actions">
                        <a href="{{ route('offers.create', $item) }}" class="btn btn-primary btn-sm"><i class="fas fa-arrow-right-arrow-left"></i> Tawarkan Barter</a>
                    </div>
                @endif
            @endauth
        @endif
    </div>
</article>
