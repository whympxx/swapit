# Swap It - Aplikasi Tukar Barang untuk Mahasiswa

Aplikasi mobile untuk mahasiswa yang memudahkan proses tukar menukar barang antar mahasiswa menggunakan Flutter.

## Fitur Aplikasi

### ✅ Autentikasi
- Login dengan email dan password
- Registrasi akun baru dengan NIM
- Logout

### ✅ Halaman Utama (Home)
- Tampilan grid barang yang tersedia
- Pencarian barang
- Filter berdasarkan kategori (Elektronik, Fashion, Buku, Olahraga, Lainnya)
- Bottom navigation untuk navigasi cepat

### ✅ Profile
- Tampilan informasi mahasiswa (Nama, NIM, Email, Telepon, Alamat)
- Quick action untuk menghubungi via email/telepon
- Link ke Barang Saya

### ✅ Posting Barang
- Form lengkap untuk posting barang
- Upload foto (multiple)
- Input judul, deskripsi, kategori, kondisi
- Validasi form

### ✅ Detail Barang
- Tampilan detail lengkap barang
- Informasi pemilik
- Tombol untuk membuat penawaran
- Tombol untuk mengirim pesan

### ✅ Penawaran
- Daftar penawaran yang diterima/dikirim
- Status penawaran (Pending, Accepted, Rejected)
- Form buat penawaran baru

### ✅ Barang Saya
- Daftar barang yang sudah diposting
- Edit/Hapus barang
- Quick add button

### ✅ Messaging
- List percakapan dengan user lain
- Chat real-time interface
- Badge notifikasi pesan baru

### ✅ Pengaturan
- Edit profile
- Ubah password
- Pengaturan notifikasi
- Pengaturan bahasa
- Bantuan
- Tentang aplikasi
- Logout

## Teknologi

- **Framework**: Flutter 3.0+
- **State Management**: Provider
- **Package Dependencies**:
  - `google_fonts` - Typography
  - `image_picker` - Upload gambar
  - `shared_preferences` - Local storage
  - `intl` - Format tanggal
  - `provider` - State management
  - `url_launcher` - Launch external URLs
  - `cached_network_image` - Image caching

## Struktur Proyek

```
lib/
├── main.dart                 # Entry point aplikasi
├── models/                   # Data models
│   ├── user.dart
│   ├── item.dart
│   ├── offer.dart
│   └── message.dart
├── providers/                # State management
│   ├── auth_provider.dart
│   └── item_provider.dart
└── screens/                  # UI Screens
    ├── login_screen.dart
    ├── register_screen.dart
    ├── home_screen.dart
    ├── profile_screen.dart
    ├── item_detail_screen.dart
    ├── posting_screen.dart
    ├── penawaran_screen.dart
    ├── buat_penawaran_screen.dart
    ├── barang_saya_screen.dart
    ├── pesan_screen.dart
    ├── list_pesan_screen.dart
    └── pengaturan_screen.dart
```

## Cara Menjalankan

### Prerequisites
- Flutter SDK (3.0 atau lebih baru)
- Android Studio / VS Code
- Emulator Android / iOS atau Physical Device

### Instalasi

1. Clone repository atau ekstrak file

2. Install dependencies:
```bash
flutter pub get
```

3. Jalankan aplikasi:
```bash
flutter run
```

### Build APK
```bash
flutter build apk --release
```

### Build iOS
```bash
flutter build ios --release
```

## Catatan Pengembangan

- Aplikasi ini menggunakan data dummy untuk simulasi
- Untuk implementasi produksi, perlu menghubungkan ke backend API
- Autentikasi menggunakan simulasi lokal, perlu implementasi OAuth/JWT
- Upload gambar saat ini menggunakan placeholder
- Messaging perlu implementasi real-time dengan WebSocket/Firebase

## Kredensial Login (Demo)
- Email: apapun@email.com
- Password: minimum 6 karakter

## Screenshot

Aplikasi memiliki desain modern dengan:
- Warna tema: Purple (#5B4DFF)
- UI/UX yang user-friendly
- Responsive design
- Material Design 3

## Pengembangan Selanjutnya

1. **Backend Integration**
   - REST API untuk semua operasi
   - Database (MySQL/PostgreSQL/MongoDB)
   - Authentication dengan JWT

2. **Real-time Features**
   - WebSocket untuk messaging
   - Push notifications
   - Real-time updates penawaran

3. **Enhanced Features**
   - Rating & review system
   - Swap history
   - Wishlist
   - Report abuse
   - Advanced search & filters

4. **Payment Integration** (optional)
   - Jika ada fee swap
   - Virtual wallet

## Lisensi

© 2024 Swap It. All rights reserved.

## Kontak

Untuk pertanyaan dan dukungan, hubungi:
- Email: support@swapit.com
- Website: www.swapit.com

---

Dibuat dengan ❤️ menggunakan Flutter
