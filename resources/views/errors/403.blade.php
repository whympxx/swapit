@extends('layouts.app')

@section('title', 'Akses Ditolak')

@section('content')
    <div class="empty-state error-page">
        <div class="error-code"><span>4</span><span>0</span><span>3</span></div>
        <h3>Kamu nggak punya akses ke sini</h3>
        <p>{{ $exception->getMessage() ?: 'Halaman ini milik pengguna lain.' }}</p>
        <a href="{{ route('home') }}" class="btn btn-primary"><i class="fas fa-house"></i> Ke Beranda</a>
    </div>
@endsection
