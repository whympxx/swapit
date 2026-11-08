@extends('layouts.app')

@section('title', 'Chat')

@section('content')
    <div class="page-header">
        <h1><span class="ph-icon"><i class="fas fa-comment-dots"></i></span> Chat</h1>
    </div>

    <div class="conversations-container">
        @forelse ($conversations as $conv)
            @php $other = $conv->otherParty(auth()->user()); @endphp
            <a href="{{ route('chat.show', $conv) }}" class="conversation-card reveal {{ $conv->unread_count > 0 ? 'unread' : '' }}" style="--d: {{ min($loop->index, 6) * 50 }}ms">
                <div class="conversation-avatar">
                    @include('partials.avatar', ['user' => $other, 'class' => 'avatar-lg'])
                    @if ($conv->unread_count > 0)
                        <span class="unread-badge">{{ $conv->unread_count }}</span>
                    @endif
                </div>

                <div class="conversation-content">
                    <div class="conversation-header">
                        <strong>
                            {{ $other->full_name }}
                            @if ($conv->status !== 'accepted')
                                <span class="offer-status status-{{ $conv->status }} status-mini">{{ $conv->status_label }}</span>
                            @endif
                        </strong>
                        <span class="conversation-time">
                            {{ $conv->last_message_at ? \Carbon\Carbon::parse($conv->last_message_at)->diffForHumans() : 'Belum ada pesan' }}
                        </span>
                    </div>

                    <div class="conversation-deal">
                        <i class="fas fa-arrow-right-arrow-left"></i> {{ $conv->offeredItem->title }} ⇄ {{ $conv->wantedItem->title }}
                    </div>

                    @if ($conv->lastMessage)
                        <div class="conversation-preview">
                            @if ($conv->lastMessage->sender_id === auth()->id())<i class="fas fa-reply"></i> Kamu: @endif
                            {{ Str::limit($conv->lastMessage->message, 80) }}
                        </div>
                    @endif
                </div>

                <div class="conversation-arrow"><i class="fas fa-chevron-right"></i></div>
            </a>
        @empty
            <div class="empty-state">
                <i class="fas fa-comment-dots"></i>
                <h3>Belum ada percakapan</h3>
                <p>Chat terbuka otomatis setelah penawaran diterima.</p>
                <a href="{{ route('offers.index') }}" class="btn btn-primary"><i class="fas fa-handshake"></i> Lihat Penawaran</a>
            </div>
        @endforelse
    </div>
@endsection
