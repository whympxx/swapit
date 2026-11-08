<form method="POST" action="{{ route($route, $offer) }}" class="inline-form" onsubmit="return confirm(@js($confirm))">
    @csrf
    <button type="submit" class="btn {{ $class }} btn-sm"><i class="fas fa-{{ $icon }}"></i> {{ $label }}</button>
</form>
