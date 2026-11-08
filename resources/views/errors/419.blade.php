@extends('layouts.app')

@section('title', 'Sesi Kedaluwarsa')

@section('content')
    <div class="empty-state error-page">
        <div class="error-code"><span>4</span><span>1</span><span>9</span></div>
        <h3>Sesi formulir kedaluwarsa</h3>
        <p>Kembali, muat ulang halamannya, lalu coba lagi.</p>
        <a href="{{ url()->previous() }}" class="btn btn-primary"><i class="fas fa-rotate-right"></i> Kembali</a>
    </div>
@endsection
