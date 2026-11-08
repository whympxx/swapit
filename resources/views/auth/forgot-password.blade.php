@extends('layouts.auth')

@section('title', 'Lupa Password')

@section('content')
    <h2>Lupa password? 🔑</h2>
    <p class="auth-sub">Santai. Masukkan email akunmu, kami kirim link untuk bikin password baru.</p>

    @if (session('dev_reset_link'))
        <div class="alert alert-info" data-persist>
            <i class="fas fa-screwdriver-wrench"></i>
            <div>
                <strong>Mode pengembangan:</strong> email belum dikonfigurasi, pakai link ini:<br>
                <a href="{{ session('dev_reset_link') }}" class="break-all">{{ session('dev_reset_link') }}</a>
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf
        <div class="form-group">
            <label for="email"><i class="fas fa-envelope"></i> Email</label>
            <input type="email" id="email" name="email" required value="{{ old('email') }}" placeholder="kamu@email.com">
        </div>

        <button type="submit" class="btn btn-primary btn-lg btn-block"><i class="fas fa-paper-plane"></i> Kirim Link Reset</button>
    </form>

    <div class="auth-footer">
        <p><a href="{{ route('login') }}"><i class="fas fa-arrow-left"></i> Kembali ke halaman masuk</a></p>
    </div>
@endsection
