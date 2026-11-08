@extends('layouts.auth')

@section('title', 'Reset Password')

@section('content')
    <h2>Buat Password Baru</h2>
    <p class="auth-sub">Pilih password yang kuat dan gampang kamu ingat.</p>

    <form method="POST" action="{{ route('password.update', $token) }}">
        @csrf
        <div class="form-group">
            <label for="password"><i class="fas fa-key"></i> Password Baru</label>
            <div class="password-field">
                <input type="password" id="password" name="password" required minlength="6"
                       placeholder="Min. 6 karakter" data-strength="pwStrength">
                <button type="button" class="password-toggle" data-target="password" aria-label="Tampilkan password"><i class="fas fa-eye"></i></button>
            </div>
            <small id="pwStrength" class="password-strength"></small>
        </div>

        <div class="form-group">
            <label for="password_confirmation"><i class="fas fa-check-double"></i> Konfirmasi Password</label>
            <input type="password" id="password_confirmation" name="password_confirmation" required placeholder="Ulangi password baru">
        </div>

        <button type="submit" class="btn btn-primary btn-lg btn-block"><i class="fas fa-floppy-disk"></i> Simpan Password</button>
    </form>

    <div class="auth-footer">
        <p><a href="{{ route('login') }}"><i class="fas fa-arrow-left"></i> Kembali ke halaman masuk</a></p>
    </div>
@endsection
