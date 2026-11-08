@extends('layouts.auth')

@section('title', 'Masuk')

@section('content')
    <h2>Welcome back 👋</h2>
    <p class="auth-sub">Masuk untuk lanjut barter dan cek tawaran terbaru.</p>

    <form method="POST" action="{{ route('login') }}">
        @csrf
        <div class="form-group">
            <label for="email"><i class="fas fa-envelope"></i> Email</label>
            <input type="email" id="email" name="email" required autofocus autocomplete="email"
                   value="{{ old('email') }}" placeholder="kamu@email.com">
        </div>

        <div class="form-group">
            <label for="password"><i class="fas fa-lock"></i> Password</label>
            <div class="password-field">
                <input type="password" id="password" name="password" required autocomplete="current-password" placeholder="••••••••">
                <button type="button" class="password-toggle" data-target="password" aria-label="Tampilkan password"><i class="fas fa-eye"></i></button>
            </div>
        </div>

        <div class="form-row-between">
            <label class="checkbox-label"><input type="checkbox" name="remember" value="1"> Ingat saya</label>
            <a href="{{ route('password.request') }}" class="link-small">Lupa password?</a>
        </div>

        <button type="submit" class="btn btn-primary btn-lg btn-block">Masuk <i class="fas fa-arrow-right"></i></button>
    </form>

    <div class="auth-footer">
        <p>Belum punya akun? <a href="{{ route('register') }}">Daftar gratis</a></p>
    </div>
@endsection
