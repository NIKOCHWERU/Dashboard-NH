# Fix Error 500 Setelah Login Google

## Langkah Troubleshooting

### 1. Cek Laravel Error Log
SSH ke VPS dan jalankan:
```bash
cd /var/www/app.narasumberhukum.online
tail -100 storage/logs/laravel.log
```

Cari error terakhir yang muncul. Biasanya akan ada pesan error yang jelas.

### 2. Kemungkinan Penyebab & Solusi

#### A. Storage Permission Error
Error: `The stream or file could not be opened`

**Solusi:**
```bash
cd /var/www/app.narasumberhukum.online
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

#### B. Database Connection Error
Error: `SQLSTATE[HY000] [2002] Connection refused`

**Solusi:**
Cek kredensial database di `.env`:
```bash
nano .env
```

Pastikan:
```ini
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel_app
DB_USERNAME=laravel_user
DB_PASSWORD=password_yang_benar
```

Test koneksi:
```bash
php artisan migrate:status
```

#### C. Table 'users' Doesn't Exist
Error: `SQLSTATE[42S02]: Base table or view not found`

**Solusi:**
```bash
php artisan migrate --force
```

#### D. Google Drive API Error
Error: `Client is unauthorized to retrieve access tokens`

**Solusi:**
Pastikan Google Drive API sudah diaktifkan di Google Cloud Console:
1. Buka [Google Cloud Console](https://console.cloud.google.com/)
2. Pilih project Anda
3. Masuk ke **APIs & Services** → **Library**
4. Cari "Google Drive API"
5. Klik **ENABLE**

#### E. Session/Cache Error
Error: `Failed to create session`

**Solusi:**
```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
php artisan config:cache
```

### 3. Enable Debug Mode (Sementara)

Untuk melihat error detail, edit `.env`:
```bash
nano .env
```

Ubah:
```ini
APP_DEBUG=true
```

Setelah itu:
```bash
php artisan config:clear
```

Coba login lagi dan lihat error message yang muncul. **JANGAN LUPA** ubah kembali ke `APP_DEBUG=false` setelah selesai troubleshooting!

### 4. Cek Nginx Error Log
```bash
sudo tail -50 /var/log/nginx/error.log
```

### 5. Cek PHP-FPM Error Log
```bash
sudo tail -50 /var/log/php8.2-fpm.log
```

### 6. Restart Services
Setelah melakukan perubahan:
```bash
sudo systemctl restart php8.2-fpm
sudo systemctl restart nginx
```

---

## Langkah Cepat (Jalankan Semua)

Jika tidak yakin, jalankan semua perintah ini:

```bash
cd /var/www/app.narasumberhukum.online

# Fix permissions
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache

# Clear cache
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Rebuild cache
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Restart services
sudo systemctl restart php8.2-fpm
sudo systemctl restart nginx
```

Setelah itu, coba akses lagi website Anda.

---

## Jika Masih Error

Kirimkan output dari perintah ini:
```bash
tail -100 /var/www/app.narasumberhukum.online/storage/logs/laravel.log
```

Saya akan bantu analisa error spesifiknya.
