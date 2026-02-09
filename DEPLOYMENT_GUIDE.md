# Panduan Deployment ke VPS & Konfigurasi Google Login

Panduan ini akan membantu Anda mengupload project Laravel Anda ke VPS dan mengatur agar "Login dengan Google" berfungsi untuk semua pengguna.

## Bagian 1: Persiapan Server (VPS)

### 1. Masuk ke VPS
Gunakan terminal atau PuTTY untuk SSH ke VPS Anda:
```bash
ssh root@IP_ADDRESS_VPS_ANDA
```

### 2. Jalankan Script Setup Otomatis
Saya telah membuatkan script `deploy_setup.sh` di dalam folder project ini.
**Cara menggunakan:**
1. Upload file `deploy_setup.sh` ke VPS Anda (bisa pakai `scp` atau copy-paste isinya).
2. Beri izin eksekusi dan jalankan:
   ```bash
   chmod +x deploy_setup.sh
   ./deploy_setup.sh
   ```
3. Script ini akan menginstall Nginx, PHP, MySQL, dan menyiapkan konfigurasi server untuk domain `app.narasumberhukum.online`.
4. **PENTING:** Catat password database yang muncul di akhir proses script!

### 3. Setup HTTPS (SSL)
Setelah script selesai, jalankan perintah ini untuk mengaktifkan HTTPS gratis dari Let's Encrypt:
```bash
sudo certbot --nginx -d app.narasumberhukum.online
```
Pilih opsi '2' (Redirect) jika ditanya, agar semua traffik otomatis ke HTTPS.

---

## Bagian 2: Upload Code & Instalasi

### 1. Upload Project
Anda bisa menggunakan **FileZilla** atau `scp` untuk mengupload semua file project dari komputer Anda ke folder `/var/www/app.narasumberhukum.online` di VPS.
*Kecuali folder: `node_modules` dan `vendor`.*

### 2. Install Dependencies di VPS
Masuk ke folder project di VPS:
```bash
cd /var/www/app.narasumberhukum.online
```

Install library PHP dan Node.js:
```bash
composer install --optimize-autoloader --no-dev
npm install
npm run build
```

### 3. Konfigurasi .env
Copy file `.env.example` menjadi `.env` dan edit:
```bash
cp .env.example .env
nano .env
```
Ubah bagian ini:
```ini
APP_ENV=production
APP_DEBUG=false
APP_URL=https://app.narasumberhukum.online

DB_DATABASE=laravel_app
DB_USERNAME=laravel_user
DB_PASSWORD=PASTE_PASSWORD_DATABASE_DARI_SCRIPT_TADI
```

### 4. Finalisasi Laravel
Jalankan perintah berikut untuk menyiapkan database dan permission:
```bash
php artisan key:generate
php artisan migrate --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Set permissions agar server bisa menulis ke folder storage
sudo chown -R www-data:www-data storage bootstrap/cache
```

---

## Bagian 3: Konfigurasi Google Login (PENTING)

Agar **semua akun Google** bisa login (tidak error "Access blocked: This app requires you to verify..."), Anda harus mengubah setting di Google Cloud Console.

### 1. Buka Google Cloud Console
Buka: [https://console.cloud.google.com/](https://console.cloud.google.com/)

### 2. Edit OAuth Consent Screen
1. Cari menu **"APIs & Services"** > **"OAuth consent screen"**.
2. Lihat bagian **"Publishing status"**.
   - Jika statusnya **"Testing"**, klik tombol **"PUBLISH APP"**.
   - Konfirmasi dengan menekan **"Confirm"**.
   - Status harus berubah menjadi **"In production"**.
   > **Kenapa ini penting?**
   > Dalam mode "Testing", hanya email yang Anda daftarkan secara manual yang bisa login.
   > Dalam mode "Production", **siapapun** dengan akun Google bisa login.

### 3. Update Credentials
1. Masuk ke menu **"Credentials"**.
2. Klik pada **"OAuth 2.0 Client IDs"** yang sudah Anda buat sebelumnya.
3. Update bagian **"Authorized JavaScript origins"**:
   - Tambahkan: `https://app.narasumberhukum.online`
4. Update bagian **"Authorized redirect URIs"**:
   - Tambahkan: `https://app.narasumberhukum.online/auth/google/callback`
5. Klik **SAVE**.

### 4. Update .env di VPS
Pastikan Client ID dan Secret di file `.env` VPS Anda sudah benar:
```ini
GOOGLE_CLIENT_ID=isi_client_id_anda
GOOGLE_CLIENT_SECRET=isi_client_secret_anda
GOOGLE_REDIRECT_URL=https://app.narasumberhukum.online/auth/google/callback
```
*Jika Anda mengubah .env, jangan lupa jalankan `php artisan config:clear`.*

---

## Selesai!
Sekarang website Anda harusnya sudah bisa diakses di [https://app.narasumberhukum.online](https://app.narasumberhukum.online) dan login Google berfungsi normal untuk semua orang.
