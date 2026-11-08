@extends('layouts.app')

@section('title', 'Barangku')

@section('content')
    <div class="page-header">
        <h1><span class="ph-icon"><i class="fas fa-box-open"></i></span> Barangku</h1>
        <a href="{{ route('items.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Posting Barang</a>
    </div>

    <div class="filter-chips">
        @foreach (['all' => 'Semua'] + \App\Models\Item::STATUS_LABELS as $key => $label)
            <a href="{{ route('items.mine', ['status' => $key]) }}" class="chip {{ $status === $key ? 'active' : '' }}">
                {{ $label }} <span>{{ $counts[$key] ?? 0 }}</span>
            </a>
        @endforeach
    </div>

    <div class="items-grid">
        @forelse ($items as $item)
            @include('partials.item-card', ['item' => $item, 'mode' => 'mine'])
        @empty
            <div class="empty-state">
                <i class="fas fa-box-open"></i>
                <h3>{{ $status === 'all' ? 'Belum ada barang' : 'Tidak ada barang dengan status ini' }}</h3>
                <p>Posting barang yang udah nggak kepakai, siapa tahu jadi incaran orang.</p>
                <a href="{{ route('items.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Posting Barang</a>
            </div>
        @endforelse
    </div>
@endsection
