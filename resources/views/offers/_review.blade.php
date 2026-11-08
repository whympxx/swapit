@php $uid = 'r' . $offer->id; @endphp
<details class="review-box" {{ $open ?? false ? 'open' : '' }}>
    <summary><i class="fas fa-star"></i> Beri ulasan untuk {{ '@' . $partner->username }}</summary>
    <form method="POST" action="{{ route('reviews.store', $offer) }}">
        @csrf
        <div class="star-input" role="radiogroup" aria-label="Rating">
            @for ($s = 5; $s >= 1; $s--)
                <input type="radio" id="{{ $uid }}-{{ $s }}" name="rating" value="{{ $s }}" required>
                <label for="{{ $uid }}-{{ $s }}" title="{{ $s }} bintang"><i class="fas fa-star"></i></label>
            @endfor
        </div>
        <textarea name="comment" maxlength="500" placeholder="Gimana pengalaman barter-nya? (opsional)"></textarea>
        <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-paper-plane"></i> Kirim Ulasan</button>
    </form>
</details>
