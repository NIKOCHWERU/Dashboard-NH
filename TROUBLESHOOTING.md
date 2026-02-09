# Troubleshooting Deployment

## Masalah: Halaman Default Laravel Muncul

### Langkah 1: Cek Apakah File Sudah Terupload Semua
SSH ke VPS dan cek:
```bash
cd /var/www/app.narasumberhukum.online
ls -la
```

Pastikan ada folder: `app`, `config`, `database`, `public`, `resources`, `routes`, `storage`, `vendor`

### Langkah 2: Cek File .env
```bash
cat /var/www/app.narasumberhukum.online/.env
```

Pastikan ada dan berisi konfigurasi yang benar:
- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_URL=https://app.narasumberhukum.online`
- Database credentials yang benar

**Jika file .env tidak ada atau kosong:**
```bash
cd /var/www/app.narasumberhukum.online
cp .env.example .env
nano .env
```
Edit sesuai dengan konfigurasi VPS Anda.

### Langkah 3: Install Dependencies
```bash
cd /var/www/app.narasumberhukum.online
composer install --optimize-autoloader --no-dev
npm install
npm run build
```

### Langkah 4: Generate Application Key
```bash
php artisan key:generate
```

### Langkah 5: Jalankan Migrasi Database
```bash
php artisan migrate --force
```

### Langkah 6: Setup Storage & Cache
```bash
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Langkah 7: Set Permissions
```bash
sudo chown -R www-data:www-data /var/www/app.narasumberhukum.online/storage
sudo chown -R www-data:www-data /var/www/app.narasumberhukum.online/bootstrap/cache
sudo chmod -R 775 /var/www/app.narasumberhukum.online/storage
sudo chmod -R 775 /var/www/app.narasumberhukum.online/bootstrap/cache
```

### Langkah 8: Cek Konfigurasi Nginx
```bash
sudo cat /etc/nginx/sites-available/app.narasumberhukum.online
```

Pastikan `root` mengarah ke:
```
root /var/www/app.narasumberhukum.online/public;
```

**Jika salah, edit:**
```bash
sudo nano /etc/nginx/sites-available/app.narasumberhukum.online
```

Setelah edit, restart Nginx:
```bash
sudo nginx -t
sudo systemctl reload nginx
```

### Langkah 9: Setup SSL (HTTPS)
```bash
sudo certbot --nginx -d app.narasumberhukum.online
```

Pilih opsi **2** (Redirect) agar otomatis redirect ke HTTPS.

### Langkah 10: Restart Semua Service
```bash
sudo systemctl restart nginx
sudo systemctl restart php8.2-fpm
```

---

## Cek Error Log

Jika masih ada masalah, cek log error:

### Laravel Error Log:
```bash
tail -f /var/www/app.narasumberhukum.online/storage/logs/laravel.log
```

### Nginx Error Log:
```bash
sudo tail -f /var/nginx/error.log
```

### PHP-FPM Error Log:
```bash
sudo tail -f /var/log/php8.2-fpm.log
```

---

## Masalah Umum & Solusi

### 1. "500 Internal Server Error"
- Cek permissions folder `storage` dan `bootstrap/cache`
- Cek file `.env` sudah ada dan valid
- Jalankan `php artisan config:clear`

### 2. "403 Forbidden"
- Cek ownership: `sudo chown -R www-data:www-data /var/www/app.narasumberhukum.online`
- Cek permissions: `sudo chmod -R 755 /var/www/app.narasumberhukum.online`

### 3. "Database Connection Error"
- Cek kredensial database di `.env`
- Test koneksi: `php artisan migrate:status`

### 4. Assets (CSS/JS) Tidak Load
- Pastikan sudah jalankan `npm run build`
- Cek folder `public/build` ada dan berisi file
- Clear cache: `php artisan config:clear && php artisan cache:clear`

### 5. Google Login Error
- Pastikan `GOOGLE_REDIRECT_URL` di `.env` menggunakan HTTPS
- Update di Google Cloud Console sesuai panduan di `DEPLOYMENT_GUIDE.md`
