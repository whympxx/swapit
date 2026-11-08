@php $value = (float) ($value ?? 0); @endphp
<span class="stars" aria-label="{{ number_format($value, 1) }} dari 5 bintang">
    @for ($i = 1; $i <= 5; $i++)
        @if ($value >= $i)
            <i class="fas fa-star"></i>
        @elseif ($value >= $i - 0.5)
            <i class="fas fa-star-half-stroke"></i>
        @else
            <i class="fas fa-star empty"></i>
        @endif
    @endfor
</span>
