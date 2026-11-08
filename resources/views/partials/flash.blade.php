@php
    $flashIcons = ['success' => 'check-circle', 'error' => 'exclamation-circle', 'info' => 'info-circle', 'warning' => 'exclamation-triangle'];
@endphp
@if (session()->hasAny(array_keys($flashIcons)) || $errors->any())
    <div class="container flash-container">
        @foreach ($flashIcons as $type => $icon)
            @if (session($type))
                <div class="alert alert-{{ $type }}"><i class="fas fa-{{ $icon }}"></i> {{ session($type) }}</div>
            @endif
        @endforeach
        @if ($errors->any())
            <div class="alert alert-error" data-persist>
                <i class="fas fa-exclamation-circle"></i>
                <div>
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
@endif
