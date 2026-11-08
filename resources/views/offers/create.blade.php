@extends('layouts.app')

@section('title', 'Buat Penawaran')

@section('content')
    <div class="page-header">
        <h1><span class="ph-icon"><i class="fas fa-arrow-right-arrow-left"></i></span> Ajukan Barter</h1>
        <p>Pilih salah satu barangmu untuk ditukar.</p>
    </div>

    <div class="offer-container">
        <div class="wanted-item-section">
            <div class="block-label">Barang incaranmu</div>
            <div class="item-card-horizontal" data-tilt="6">
                <img src="{{ $item->photo_url }}" alt="{{ $item->title }}">
                <div class="item-info">
                    <h4>{{ $item->title }}</h4>
                    <div class="item-tags">
                        <span class="tag tag-lime"><i class="fas fa-{{ $item->category_icon }}"></i> {{ $item->category }}</span>
                        <span class="tag"><i class="fas fa-wand-magic-sparkles"></i> {{ $item->condition_item }}</span>
                    </div>
                    <a class="item-owner" href="{{ route('profile.show', $item->user) }}">
                        @include('partials.avatar', ['user' => $item->user, 'class' => 'avatar-sm'])
                        <span>{{ $item->user->username }}</span>
                    </a>
                </div>
            </div>
        </div>

        <div class="offer-form-section panel">
            @if ($myItems->isEmpty())
                <div class="empty-state" style="border: 0; background: none; padding: 2rem 1rem;">
                    <i class="fas fa-box-open"></i>
                    <h3>Kamu belum punya barang tersedia</h3>
                    <p>Posting dulu barang yang mau kamu tukar, lalu kembali ke sini.</p>
                    <a href="{{ route('items.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Posting Barang</a>
                </div>
            @else
                <form method="POST" action="{{ route('offers.store', $item) }}">
                    @csrf
                    <div class="block-label">Pilih barangmu</div>

                    <div class="my-items-selection">
                        @foreach ($myItems as $mine)
                            <label class="item-selection-card">
                                <input type="radio" name="offered_item_id" value="{{ $mine->id }}" required
                                       @checked((int) old('offered_item_id') === $mine->id)>
                                <div class="selection-content">
                                    <img src="{{ $mine->photo_url }}" alt="{{ $mine->title }}">
                                    <div class="selection-info">
                                        <h4>{{ $mine->title }}</h4>
                                        <p>{{ $mine->category }} · {{ $mine->condition_item }}</p>
                                    </div>
                                </div>
                            </label>
                        @endforeach
                    </div>

                    <div class="form-group">
                        <label for="message"><i class="fas fa-comment"></i> Pesan untuk pemilik (opsional)</label>
                        <textarea id="message" name="message" rows="3" maxlength="1000" data-counter="msgCounter"
                                  placeholder="Halo! Aku tertarik banget sama barangmu. Bisa COD di kampus?">{{ old('message') }}</textarea>
                        <small id="msgCounter" class="char-counter"></small>
                    </div>

                    <div class="form-actions">
                        <a href="{{ route('items.show', $item) }}" class="btn">Batal</a>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane"></i> Kirim Penawaran</button>
                    </div>
                </form>
            @endif
        </div>
    </div>
@endsection
