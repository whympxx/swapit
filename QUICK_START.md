# ⚡ QUICK START - Swap It

## 🚨 LANGKAH PENTING SEBELUM RUNNING

Karena project ini dibuat manual tanpa `flutter create`, Anda perlu menginisialisasi project Flutter terlebih dahulu.

### Metode 1: Inisialisasi dengan Flutter Create (RECOMMENDED)

1. **Backup folder lib, assets, dan file-file dokumentasi**
   ```bash
   # Buat folder backup
   mkdir ..\swapit_backup
   
   # Copy file-file penting
   xcopy /E /I lib ..\swapit_backup\lib
   xcopy /E /I assets ..\swapit_backup\assets
   copy pubspec.yaml ..\swapit_backup\
   copy README.md ..\swapit_backup\
   copy SETUP_INSTRUCTIONS.md ..\swapit_backup\
   copy API_DOCUMENTATION.md ..\swapit_backup\
   copy analysis_options.yaml ..\swapit_backup\
   copy .gitignore ..\swapit_backup\
   ```

2. **Buat project Flutter baru**
   ```bash
   # Keluar ke parent directory
   cd ..
   
   # Buat project baru
   flutter create swapit_new
   
   # Pindah ke project baru
   cd swapit_new
   ```

3. **Replace dengan file-file kita**
   ```bash
   # Hapus folder lib default
   rmdir /S /Q lib
   
   # Copy folder lib kita
   xcopy /E /I ..\swapit_backup\lib lib
   
   # Copy assets
   xcopy /E /I ..\swapit_backup\assets assets
   
   # Copy pubspec.yaml
   copy ..\swapit_backup\pubspec.yaml pubspec.yaml
   
   # Copy file dokumentasi
   copy ..\swapit_backup\README.md README.md
   copy ..\swapit_backup\SETUP_INSTRUCTIONS.md SETUP_INSTRUCTIONS.md
   copy ..\swapit_backup\API_DOCUMENTATION.md API_DOCUMENTATION.md
   copy ..\swapit_backup\analysis_options.yaml analysis_options.yaml
   copy ..\swapit_backup\.gitignore .gitignore
   ```

4. **Install dependencies**
   ```bash
   flutter pub get
   ```

5. **Run aplikasi**
   ```bash
   flutter run
   ```

### Metode 2: Inisialisasi Manual (Alternatif)

Jika Metode 1 terlalu ribet, gunakan cara ini:

1. **Download Flutter SDK**
   - Download dari: https://docs.flutter.dev/get-started/install/windows
   - Extract ke `C:\flutter`
   - Add ke PATH: `C:\flutter\bin`

2. **Install Android Studio**
   - Download dari: https://developer.android.com/studio
   - Install Android SDK
   - Setup Android Emulator

3. **Verifikasi instalasi**
   ```bash
   flutter doctor
   ```
   Pastikan semua checklist hijau

4. **Buat project baru di lokasi berbeda**
   ```bash
   cd C:\
   flutter create swapit_app
   ```

5. **Copy semua file dari `C:\xampp\htdocs\swapit` ke `C:\swapit_app`**
   - Copy folder `lib` (overwrite)
   - Copy folder `assets` (overwrite)  
   - Copy file `pubspec.yaml` (overwrite)
   - Copy file dokumentasi (README.md, etc.)

6. **Install dependencies & run**
   ```bash
   cd C:\swapit_app
   flutter pub get
   flutter run
   ```

### Metode 3: Menggunakan Flutter CLI di Folder Ini

Jika folder android, ios, dll sudah ada dari `flutter create`, langsung:

```bash
# Di folder C:\xampp\htdocs\swapit
flutter pub get
flutter run
```

## 🔧 Troubleshooting

### Error: "Flutter SDK not found"
**Solusi:**
1. Install Flutter SDK dari https://flutter.dev
2. Add Flutter ke PATH
3. Restart terminal

### Error: "Android toolchain not found"
**Solusi:**
1. Install Android Studio
2. Install Android SDK via Android Studio
3. Run: `flutter doctor --android-licenses`

### Error: "No devices found"
**Solusi:**
1. Buka Android Studio
2. Tools > AVD Manager
3. Create Virtual Device
4. Start emulator
5. Check dengan: `flutter devices`

### Error: "Gradle build failed"
**Solusi:**
```bash
cd android
.\gradlew clean
cd ..
flutter clean
flutter pub get
flutter run
```

### Error: "Package not found"
**Solusi:**
```bash
flutter pub cache repair
flutter pub get
```

## 📱 Running di Physical Device

### Android:
1. Enable Developer Options di HP
2. Enable USB Debugging
3. Hubungkan HP ke laptop via USB
4. Allow USB debugging
5. Run: `flutter run`

### iOS (Mac only):
1. Hubungkan iPhone
2. Trust komputer
3. Run: `flutter run`

## 🏗️ Build APK

```bash
# Debug APK
flutter build apk --debug

# Release APK (production)
flutter build apk --release

# Split APK per ABI (ukuran lebih kecil)
flutter build apk --split-per-abi --release
```

APK ada di: `build\app\outputs\flutter-apk\`

## ✅ Checklist Sebelum Running

- [ ] Flutter SDK sudah terinstall
- [ ] `flutter doctor` tidak ada error critical
- [ ] Android SDK/Emulator sudah siap
- [ ] Folder `android`, `ios`, `web` sudah ada (dari flutter create)
- [ ] File `pubspec.yaml` sudah benar
- [ ] Dependencies sudah diinstall (`flutter pub get`)
- [ ] Ada device yang terdeteksi (`flutter devices`)

## 🎯 Test Run Cepat

Jika semua sudah siap:

```bash
# 1. Check Flutter
flutter doctor -v

# 2. Check devices
flutter devices

# 3. Install dependencies
flutter pub get

# 4. Run
flutter run

# Atau langsung ke device tertentu
flutter run -d chrome          # Web
flutter run -d emulator-5554   # Android emulator
flutter run -d <device-id>     # Specific device
```

## 📦 Dependencies yang Dibutuhkan

Pastikan semua ini ada di `pubspec.yaml`:

```yaml
dependencies:
  flutter:
    sdk: flutter
  google_fonts: ^6.1.0
  image_picker: ^1.0.4
  shared_preferences: ^2.2.2
  intl: ^0.18.1
  provider: ^6.1.1
  http: ^1.1.0
  cached_network_image: ^3.3.0
  url_launcher: ^6.2.1
```

## 🚀 Yang Sudah Siap

✅ Semua kode Dart (lib folder) - 100% siap
✅ Models (User, Item, Offer, Message)
✅ Providers (Auth, Item)
✅ 12 Screens lengkap
✅ Navigation & routing
✅ UI/UX sesuai desain
✅ State management
✅ Form validation
✅ Assets folder

## 📝 Catatan

- Aplikasi menggunakan data dummy (simulasi)
- Untuk production perlu backend API
- Images menggunakan placeholder
- Real-time chat perlu WebSocket/Firebase

## 💡 Tips

1. **Gunakan Hot Reload**: Tekan `r` saat development
2. **Hot Restart**: Tekan `R` untuk restart penuh
3. **Debug Console**: Lihat errors di VS Code/Android Studio
4. **Flutter DevTools**: Untuk debugging advanced

---

**Need Help?**

Jika masih ada error, cek:
1. Error message di console
2. Run `flutter doctor -v`
3. Baca SETUP_INSTRUCTIONS.md
4. Cek Flutter documentation: https://docs.flutter.dev

**Ready to code! 🎉**
