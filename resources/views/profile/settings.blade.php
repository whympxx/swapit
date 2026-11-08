@extends('layouts.app')

@section('title', 'Pengaturan')

@section('content')
    <div class="page-header">
        <h1><span class="ph-icon"><i class="fas fa-gear"></i></span> Pengaturan</h1>
    </div>

    <div class="form-container reveal">
        <form method="POST" action="{{ route('settings.update') }}">
            @csrf
            @method('PUT')
            <h3><i class="fas fa-user text-lime"></i> Profil</h3>

            <div class="form-row">
                <div class="form-group">
                    <label for="username"><i class="fas fa-at"></i> Username</label>
                    <input type="text" id="username" value="{{ $user->username }}" disabled>
                    <small>Username tidak bisa diubah</small>
                </div>
                <div class="form-group">
                    <label for="email"><i class="fas fa-envelope"></i> Email</label>
                    <input type="email" id="email" value="{{ $user->email }}" disabled>
                    <small>Email tidak bisa diubah</small>
                </div>
            </div>

            <div class="form-group">
                <label for="full_name"><i class="fas fa-id-card"></i> Nama Lengkap *</label>
                <input type="text" id="full_name" name="full_name" required maxlength="100" value="{{ old('full_name', $user->full_name) }}">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="phone"><i class="fab fa-whatsapp"></i> Nomor WhatsApp</label>
                    <input type="tel" id="phone" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="081234567890">
                    <small>Hanya terlihat oleh partner barter setelah penawaran diterima.</small>
                </div>
                <div class="form-group">
                    <label for="university"><i class="fas fa-building-columns"></i> Kampus</label>
                    <input type="text" id="university" name="university" maxlength="100" value="{{ old('university', $user->university) }}">
                </div>
            </div>

            <div class="form-actions">
                <a href="{{ route('profile') }}" class="btn">Batal</a>
                <button type="submit" class="btn btn-primary"><i class="fas fa-floppy-disk"></i> Simpan Profil</button>
            </div>
        </form>
    </div>

    <div class="form-container reveal">
        <form method="POST" action="{{ route('settings.password') }}">
            @csrf
            @method('PUT')
            <h3><i class="fas fa-shield-halved text-lime"></i> Keamanan</h3>

            <div class="form-group">
                <label for="current_password"><i class="fas fa-lock"></i> Password Lama</label>
                <input type="password" id="current_password" name="current_password" required autocomplete="current-password">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="password"><i class="fas fa-key"></i> Password Baru</label>
                    <div class="password-field">
                        <input type="password" id="password" name="password" required minlength="6" autocomplete="new-password" data-strength="pwStrength">
                        <button type="button" class="password-toggle" data-target="password" aria-label="Tampilkan password"><i class="fas fa-eye"></i></button>
                    </div>
                    <small id="pwStrength" class="password-strength"></small>
                </div>
                <div class="form-group">
                    <label for="password_confirmation"><i class="fas fa-key"></i> Konfirmasi</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required autocomplete="new-password">
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary"><i class="fas fa-key"></i> Ubah Password</button>
            </div>
        </form>
    </div>
@endsection
