# 🚀 Petunjuk Setup Aplikasi Swap It

## 📋 Prasyarat

Sebelum menjalankan aplikasi, pastikan Anda sudah menginstall:

1. **Flutter SDK** (versi 3.0 atau lebih baru)
   - Download dari: https://flutter.dev/docs/get-started/install
   - Ikuti instruksi instalasi sesuai sistem operasi Anda

2. **Android Studio** atau **VS Code**
   - Android Studio: https://developer.android.com/studio
   - VS Code: https://code.visualstudio.com/

3. **Git** (optional, untuk version control)
   - Download dari: https://git-scm.com/

## 🔧 Langkah Instalasi

### 1. Verifikasi Instalasi Flutter

Buka terminal/command prompt dan jalankan:

```bash
flutter doctor
```

Pastikan semua checklist ✓ sudah terpenuhi. Jika ada warning, ikuti instruksi yang diberikan.

### 2. Install Dependencies

Di direktori project `C:\xampp\htdocs\swapit`, jalankan:

```bash
flutter pub get
```

Command ini akan mengunduh semua package yang dibutuhkan aplikasi.

### 3. Setup Android/iOS

#### Untuk Android:
- Pastikan Android SDK sudah terinstall
- Buat emulator atau hubungkan device fisik
- Enable USB Debugging di device

#### Untuk iOS (Mac only):
```bash
cd ios
pod install
cd ..
```

### 4. Cek Device yang Tersedia

```bash
flutter devices
```

Pastikan ada device yang terdeteksi (emulator atau physical device).

## ▶️ Menjalankan Aplikasi

### Development Mode

```bash
flutter run
```

Atau untuk memilih device tertentu:

```bash
flutter run -d <device_id>
```

### Hot Reload

Saat aplikasi berjalan, tekan:
- `r` untuk hot reload
- `R` untuk hot restart
- `q` untuk quit

## 📦 Build Aplikasi

### Build APK (Android)

Debug APK:
```bash
flutter build apk --debug
```

Release APK:
```bash
flutter build apk --release
```

APK akan tersimpan di: `build/app/outputs/flutter-apk/`

### Build AAB (Android App Bundle)

```bash
flutter build appbundle --release
```

### Build iOS (Mac only)

```bash
flutter build ios --release
```

## 🐛 Troubleshooting

### Error: "Gradle build failed"

Solusi:
```bash
cd android
./gradlew clean
cd ..
flutter clean
flutter pub get
flutter run
```

### Error: "Pub get failed"

Solusi:
```bash
flutter pub cache repair
flutter pub get
```

### Error: "No connected devices"

Solusi:
- Pastikan emulator sudah running
- Untuk physical device, enable USB Debugging
- Cek dengan `flutter devices`

### Error: "SDK version mismatch"

Update Flutter SDK:
```bash
flutter upgrade
```

## 📱 Struktur File Project

```
swapit/
├── lib/
│   ├── main.dart                    # Entry point
│   ├── models/                      # Data models
│   │   ├── user.dart
│   │   ├── item.dart
│   │   ├── offer.dart
│   │   └── message.dart
│   ├── providers/                   # State management
│   │   ├── auth_provider.dart
│   │   └── item_provider.dart
│   └── screens/                     # UI Screens
│       ├── login_screen.dart
│       ├── register_screen.dart
│       ├── home_screen.dart
│       └── ... (dan lainnya)
├── assets/
│   └── images/                      # Folder untuk gambar
├── pubspec.yaml                     # Dependencies
├── README.md                        # Dokumentasi
└── analysis_options.yaml            # Linter rules
```

## 🔑 Testing Akun

Untuk testing, gunakan kredensial berikut:

- **Email**: apapun@email.com (email valid apapun)
- **Password**: minimum 6 karakter

Contoh:
- Email: test@student.ac.id
- Password: 123456

## 🌟 Fitur yang Sudah Diimplementasi

✅ Login & Register  
✅ Home dengan grid barang  
✅ Search & filter kategori  
✅ Profile mahasiswa  
✅ Posting barang baru  
✅ Detail barang  
✅ Buat penawaran  
✅ List penawaran  
✅ Barang saya  
✅ Messaging/Chat  
✅ Pengaturan akun  
✅ Logout  

## 📝 Catatan Penting

1. **Data Dummy**: Aplikasi menggunakan data dummy untuk simulasi. Belum terkoneksi ke backend.

2. **Image Upload**: Fitur upload gambar menggunakan placeholder. Perlu integrasi dengan storage service untuk produksi.

3. **Authentication**: Menggunakan local storage (SharedPreferences). Untuk produksi, perlu implementasi API authentication.

4. **Real-time Chat**: Chat menggunakan state management lokal. Untuk produksi, perlu WebSocket atau Firebase.

## 🔄 Langkah Selanjutnya (Development)

1. **Backend Integration**
   - Setup REST API
   - Database design & implementation
   - JWT authentication

2. **Real-time Features**
   - Implementasi WebSocket untuk chat
   - Push notifications
   - Real-time offer updates

3. **Enhancement**
   - Rating system
   - Image optimization
   - Caching strategy
   - Offline mode

## 💬 Support

Jika mengalami masalah:

1. Cek error message di console
2. Lihat troubleshooting di atas
3. Jalankan `flutter doctor -v` untuk detail
4. Baca dokumentasi di README.md

## 📄 License

© 2024 Swap It. All rights reserved.

---

**Selamat coding! 🎉**

Jika ada pertanyaan, hubungi developer atau baca dokumentasi Flutter:
https://flutter.dev/docs
