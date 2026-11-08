@php
    $received = $type === 'received';
    $other = $received ? $offer->sender : $offer->receiver;
    $mine = $received ? $offer->wantedItem : $offer->offeredItem;
    $theirs = $received ? $offer->offeredItem : $offer->wantedItem;
    $myReview = $offer->status === 'completed' ? $offer->reviews->firstWhere('reviewer_id', auth()->id()) : null;
@endphp
<div class="offer-card reveal" style="--d: {{ min($loop->index ?? 0, 5) * 60 }}ms">
    <div class="offer-header">
        <a class="offer-user" href="{{ route('profile.show', $other) }}">
            @include('partials.avatar', ['user' => $other])
            <div>
                {{ $other->full_name }}
                <small>{{ $received ? 'menawar barangmu' : 'kamu menawar ke' }} · {{ '@' . $other->username }}</small>
            </div>
        </a>
        <span class="offer-status status-{{ $offer->status }}">{{ $offer->status_label }}</span>
    </div>

    <div class="offer-items">
        <a class="offer-item" href="{{ route('items.show', $mine) }}">
            <img src="{{ $mine->photo_url }}" alt="{{ $mine->title }}" loading="lazy">
            <p>Barangmu <strong>{{ $mine->title }}</strong>{{ $mine->condition_item }}</p>
        </a>
        <div class="offer-arrow" aria-hidden="true"><i class="fas fa-arrow-right-arrow-left"></i></div>
        <a class="offer-item" href="{{ route('items.show', $theirs) }}">
            <img src="{{ $theirs->photo_url }}" alt="{{ $theirs->title }}" loading="lazy">
            <p>{{ $received ? 'Ditawarkan' : 'Incaranmu' }} <strong>{{ $theirs->title }}</strong>{{ $theirs->condition_item }}</p>
        </a>
    </div>

    @if ($offer->message)
        <div class="offer-message">
            <i class="fas fa-quote-left"></i>
            <p class="preserve-lines">{{ $offer->message }}</p>
        </div>
    @endif

    <div class="offer-footer">
        <span class="offer-date"><i class="far fa-clock"></i> {{ $offer->created_at->diffForHumans() }}</span>

        <div class="offer-actions">
            @if ($offer->status === 'pending' && $received)
                @include('offers._action', ['route' => 'offers.reject', 'label' => 'Tolak', 'icon' => 'xmark', 'class' => 'btn-danger',
                    'confirm' => 'Tolak penawaran ini?'])
                @include('offers._action', ['route' => 'offers.accept', 'label' => 'Terima', 'icon' => 'check', 'class' => 'btn-primary',
                    'confirm' => 'Terima penawaran ini? Penawaran lain untuk barang yang sama akan dibatalkan otomatis.'])
            @elseif ($offer->status === 'pending')
                @include('offers._action', ['route' => 'offers.cancel', 'label' => 'Tarik Penawaran', 'icon' => 'rotate-left', 'class' => 'btn-secondary',
                    'confirm' => 'Tarik kembali penawaran ini?'])
            @elseif ($offer->status === 'accepted')
                <a href="{{ route('chat.show', $offer) }}" class="btn btn-sm"><i class="fas fa-comment-dots"></i> Chat</a>
                @include('offers._action', ['route' => 'offers.complete', 'label' => 'Barter Selesai', 'icon' => 'check-double', 'class' => 'btn-primary',
                    'confirm' => 'Konfirmasi barang sudah saling ditukar?'])
            @elseif ($offer->status === 'completed')
                <a href="{{ route('chat.show', $offer) }}" class="btn btn-sm"><i class="fas fa-clock-rotate-left"></i> Riwayat Chat</a>
            @endif
        </div>
    </div>

    @if ($offer->status === 'completed')
        @if ($myReview)
            <div class="review-done">
                <i class="fas fa-star"></i> Kamu memberi {{ $myReview->rating }} bintang untuk {{ '@' . $other->username }}.
                @if ($myReview->comment) <em>“{{ Str::limit($myReview->comment, 80) }}”</em> @endif
            </div>
        @else
            @include('offers._review', ['offer' => $offer, 'partner' => $other])
        @endif
    @endif
</div>
