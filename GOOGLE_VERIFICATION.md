# Mengatasi Peringatan "Google Belum Memverifikasi Aplikasi"

## Mengapa Muncul Peringatan Ini?

Peringatan "Google hasn't verified this app" muncul karena aplikasi Anda menggunakan OAuth 2.0 untuk mengakses Google Drive dan layanan Google lainnya, tetapi belum melalui proses verifikasi resmi dari Google.

![Google Verification Warning](/home/niko/.gemini/antigravity/brain/0bc4598b-ba17-416b-b08b-76a5f868e9db/uploaded_media_0_1770722077760.png)

## Apakah Aman untuk Dilanjutkan?

**YA, aman untuk dilanjutkan** jika:
- Anda adalah developer/pemilik aplikasi ini
- Anda mempercayai kode yang Anda deploy
- Aplikasi hanya digunakan internal oleh tim/organisasi Anda

## Cara Melewati Peringatan (Untuk Pengguna Internal)

1. Klik **"Advanced"** atau **"Lanjutan"** di halaman peringatan
2. Klik **"Go to [nama aplikasi] (unsafe)"** atau **"Buka [nama aplikasi] (tidak aman)"**
3. Berikan izin yang diminta aplikasi

## Cara Menghilangkan Peringatan Secara Permanen

### Opsi 1: Verifikasi Aplikasi di Google Cloud Console (Untuk Produksi)

Jika aplikasi akan digunakan oleh banyak pengguna eksternal, Anda perlu memverifikasi aplikasi:

1. **Buka Google Cloud Console**
   - Kunjungi: https://console.cloud.google.com
   - Pilih project Anda

2. **Lengkapi OAuth Consent Screen**
   - Navigasi ke: `APIs & Services` → `OAuth consent screen`
   - Isi semua informasi yang diperlukan:
     - App name
     - User support email
     - Developer contact information
     - App logo (opsional tapi direkomendasikan)
     - Privacy policy URL
     - Terms of service URL

3. **Submit untuk Verifikasi**
   - Klik **"Submit for Verification"**
   - Ikuti proses review dari Google
   - Proses ini bisa memakan waktu beberapa hari hingga minggu

### Opsi 2: Gunakan Internal User Type (Untuk Organisasi)

Jika aplikasi hanya untuk organisasi Google Workspace Anda:

1. Buka `OAuth consent screen` di Google Cloud Console
2. Pilih **"Internal"** sebagai User Type
3. Hanya pengguna dalam organisasi Google Workspace Anda yang bisa login
4. **Tidak perlu verifikasi** dari Google

### Opsi 3: Tambahkan Test Users (Untuk Development)

Untuk testing dengan jumlah pengguna terbatas (maksimal 100):

1. Buka `OAuth consent screen` di Google Cloud Console
2. Di bagian **"Test users"**, klik **"Add Users"**
3. Tambahkan email pengguna yang akan mengakses aplikasi
4. Pengguna yang terdaftar tidak akan melihat peringatan

## Persyaratan Verifikasi Google

Untuk mendapatkan verifikasi, aplikasi Anda harus memenuhi:

1. **Domain Verification**: Domain aplikasi harus terverifikasi
2. **Privacy Policy**: URL privacy policy yang valid dan accessible
3. **Terms of Service**: URL terms of service (opsional tapi direkomendasikan)
4. **Scope Justification**: Penjelasan mengapa aplikasi memerlukan akses tertentu
5. **Video Demo**: Video yang menunjukkan cara kerja aplikasi (untuk sensitive scopes)

## Scopes yang Digunakan Aplikasi Ini

Aplikasi ini menggunakan scope berikut:
- `https://www.googleapis.com/auth/drive` - Akses penuh ke Google Drive untuk upload/download file
- `https://www.googleapis.com/auth/userinfo.email` - Mendapatkan email pengguna
- `https://www.googleapis.com/auth/userinfo.profile` - Mendapatkan profil pengguna

## Rekomendasi

### Untuk Development/Internal Use:
✅ Gunakan **Test Users** atau **Internal User Type**
- Tidak perlu verifikasi
- Cepat dan mudah
- Cocok untuk tim kecil

### Untuk Production/Public Use:
✅ Submit untuk **Verifikasi Resmi**
- Membangun kepercayaan pengguna
- Tidak ada batasan jumlah pengguna
- Tampilan profesional

## Troubleshooting

### Peringatan Masih Muncul Setelah Menambahkan Test User
- Pastikan email yang login sudah terdaftar di Test Users
- Logout dan login ulang
- Clear browser cache

### Verifikasi Ditolak
- Periksa kembali semua persyaratan
- Pastikan Privacy Policy dan ToS accessible
- Berikan penjelasan yang jelas tentang penggunaan scopes
- Revisi dan submit ulang

## Referensi

- [Google OAuth 2.0 Documentation](https://developers.google.com/identity/protocols/oauth2)
- [OAuth Consent Screen Configuration](https://support.google.com/cloud/answer/10311615)
- [OAuth Verification Requirements](https://support.google.com/cloud/answer/9110914)
