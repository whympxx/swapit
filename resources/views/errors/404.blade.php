@extends('layouts.app')

@section('title', 'Tidak Ditemukan')

@section('content')
    <div class="empty-state error-page">
        <div class="error-code"><span>4</span><span>0</span><span>4</span></div>
        <h3>Ups, halaman atau barangnya nggak ada</h3>
        <p>Mungkin barangnya sudah ditukar, dihapus, atau link-nya salah.</p>
        <a href="{{ route('home') }}" class="btn btn-primary"><i class="fas fa-house"></i> Ke Beranda</a>
    </div>
@endsection
