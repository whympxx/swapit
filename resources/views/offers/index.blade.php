@extends('layouts.app')

@section('title', 'Penawaran')

@section('content')
    <div class="page-header">
        <h1><span class="ph-icon"><i class="fas fa-handshake"></i></span> Penawaran</h1>
    </div>

    <div class="offers-tabs" role="tablist">
        <button class="tab-btn {{ $tab === 'received' ? 'active' : '' }}" data-tab="received" type="button" role="tab">
            <i class="fas fa-inbox"></i> Masuk
            @if ($receivedPending > 0)<span class="badge">{{ $receivedPending }}</span>@endif
        </button>
        <button class="tab-btn {{ $tab === 'sent' ? 'active' : '' }}" data-tab="sent" type="button" role="tab">
            <i class="fas fa-paper-plane"></i> Terkirim
            @if ($sentActive > 0)<span class="badge">{{ $sentActive }}</span>@endif
        </button>
    </div>

    <div class="tab-content {{ $tab === 'received' ? 'active' : '' }}" id="received-tab">
        @forelse ($received as $offer)
            @include('offers._card', ['offer' => $offer, 'type' => 'received'])
        @empty
            <div class="empty-state">
                <i class="fas fa-inbox"></i>
                <h3>Belum ada penawaran masuk</h3>
                <p>Makin banyak barang yang kamu posting, makin besar peluang dapat tawaran.</p>
                <a href="{{ route('items.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Posting Barang</a>
            </div>
        @endforelse
    </div>

    <div class="tab-content {{ $tab === 'sent' ? 'active' : '' }}" id="sent-tab">
        @forelse ($sent as $offer)
            @include('offers._card', ['offer' => $offer, 'type' => 'sent'])
        @empty
            <div class="empty-state">
                <i class="fas fa-paper-plane"></i>
                <h3>Belum ada penawaran terkirim</h3>
                <p>Temukan barang incaran dan tawarkan barangmu.</p>
                <a href="{{ route('home') }}#items" class="btn btn-primary"><i class="fas fa-compass"></i> Jelajahi Barang</a>
            </div>
        @endforelse
    </div>
@endsection
