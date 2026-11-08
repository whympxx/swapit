@extends('layouts.auth')

@section('title', 'Daftar')

@section('content')
    <h2>Gabung SWAP-IT ✨</h2>
    <p class="auth-sub">Gratis, cepat, dan langsung bisa barter.</p>

    <form method="POST" action="{{ route('register') }}">
        @csrf
        <div class="form-row">
            <div class="form-group">
                <label for="full_name"><i class="fas fa-id-card"></i> Nama Lengkap *</label>
                <input type="text" id="full_name" name="full_name" required maxlength="100" value="{{ old('full_name') }}" placeholder="Nama kamu">
            </div>
            <div class="form-group">
                <label for="username"><i class="fas fa-at"></i> Username *</label>
                <input type="text" id="username" name="username" required pattern="[A-Za-z0-9_.]{3,30}" value="{{ old('username') }}" placeholder="huruf, angka, _ ." >
            </div>
        </div>

        <div class="form-group">
            <label for="email"><i class="fas fa-envelope"></i> Email *</label>
            <input type="email" id="email" name="email" required value="{{ old('email') }}" placeholder="kamu@email.com">
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="phone"><i class="fab fa-whatsapp"></i> WhatsApp</label>
                <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" placeholder="0812…">
            </div>
            <div class="form-group">
                <label for="university"><i class="fas fa-building-columns"></i> Kampus</label>
                <input type="text" id="university" name="university" maxlength="100" value="{{ old('university') }}" placeholder="Nama kampus">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="password"><i class="fas fa-lock"></i> Password *</label>
                <div class="password-field">
                    <input type="password" id="password" name="password" required minlength="6" placeholder="Min. 6 karakter" data-strength="pwStrength">
                    <button type="button" class="password-toggle" data-target="password" aria-label="Tampilkan password"><i class="fas fa-eye"></i></button>
                </div>
                <small id="pwStrength" class="password-strength"></small>
            </div>
            <div class="form-group">
                <label for="password_confirmation"><i class="fas fa-lock"></i> Konfirmasi *</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required placeholder="Ulangi password">
            </div>
        </div>

        <button type="submit" class="btn btn-primary btn-lg btn-block">Buat Akun <i class="fas fa-arrow-right"></i></button>
    </form>

    <div class="auth-footer">
        <p>Sudah punya akun? <a href="{{ route('login') }}">Masuk</a></p>
    </div>
@endsection
