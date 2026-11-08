@extends('layouts.app')

@section('title', 'Notifikasi')

@section('content')
    <div class="page-header">
        <h1><span class="ph-icon"><i class="fas fa-bell"></i></span> Notifikasi</h1>
        @if ($notifications->getCollection()->contains('is_read', false))
            <form method="POST" action="{{ route('notifications.readAll') }}">
                @csrf
                <button type="submit" class="btn btn-sm"><i class="fas fa-check-double"></i> Tandai semua dibaca</button>
            </form>
        @endif
    </div>

    <div class="notification-list">
        @forelse ($notifications as $notif)
            @php
                $icon = match (true) {
                    str_contains($notif->title, 'Ulasan') => 'star',
                    str_contains($notif->title, 'Diterima') => 'circle-check',
                    str_contains($notif->title, 'Selesai') => 'trophy',
                    str_contains($notif->title, 'Ditolak'), str_contains($notif->title, 'Dibatalkan') => 'circle-xmark',
                    str_contains($notif->title, 'Baru') => 'handshake',
                    default => 'bell',
                };
            @endphp
            <a href="{{ route('notifications.open', $notif) }}" class="notification-item reveal {{ $notif->is_read ? '' : 'unread' }}" style="--d: {{ min($loop->index, 6) * 40 }}ms">
                <div class="notification-icon"><i class="fas fa-{{ $icon }}"></i></div>
                <div class="notification-body">
                    <strong>{{ $notif->title }}</strong>
                    <p>{{ $notif->message }}</p>
                    <span class="notification-time">{{ $notif->created_at?->diffForHumans() }}</span>
                </div>
                @unless ($notif->is_read)
                    <span class="notification-dot" aria-label="Belum dibaca"></span>
                @endunless
            </a>
        @empty
            <div class="empty-state">
                <i class="fas fa-bell-slash"></i>
                <h3>Belum ada notifikasi</h3>
                <p>Kabar soal penawaran, barter, dan ulasan bakal muncul di sini.</p>
            </div>
        @endforelse
    </div>

    {{ $notifications->links('partials.pagination') }}
@endsection
