# Fix: Missing Google Service Account File

## Masalah
Error: `file "/var/www/app.narasumberhukum.online/storage/app/google-drive-service-account.json" does not exist`

## Solusi

### Opsi 1: Upload via SCP (Recommended)

Dari komputer lokal Anda, jalankan:

```bash
scp /home/niko/Desktop/Laravel/Dokumentasi/storage/app/google-drive-service-account.json kantor@IP_VPS_ANDA:/var/www/app.narasumberhukum.online/storage/app/
```

Ganti `IP_VPS_ANDA` dengan IP address VPS Anda.

### Opsi 2: Upload via FileZilla/SFTP

1. Buka FileZilla
2. Connect ke VPS Anda
3. Navigate ke folder lokal: `/home/niko/Desktop/Laravel/Dokumentasi/storage/app/`
4. Navigate ke folder remote: `/var/www/app.narasumberhukum.online/storage/app/`
5. Upload file `google-drive-service-account.json`

### Opsi 3: Copy-Paste Manual

**Di komputer lokal:**
```bash
cat /home/niko/Desktop/Laravel/Dokumentasi/storage/app/google-drive-service-account.json
```

Copy semua isi file yang muncul.

**Di VPS (via SSH):**
```bash
cd /var/www/app.narasumberhukum.online/storage/app
nano google-drive-service-account.json
```

Paste isi file yang sudah di-copy, lalu save (Ctrl+O, Enter, Ctrl+X).

### Setelah Upload

Set permission yang benar:
```bash
cd /var/www/app.narasumberhukum.online
sudo chown www-data:www-data storage/app/google-drive-service-account.json
sudo chmod 644 storage/app/google-drive-service-account.json
```

### Verifikasi

Cek apakah file sudah ada:
```bash
ls -la /var/www/app.narasumberhukum.online/storage/app/google-drive-service-account.json
```

Seharusnya muncul output seperti:
```
-rw-r--r-- 1 www-data www-data 2345 Feb  9 16:30 google-drive-service-account.json
```

### Test Aplikasi

Setelah file terupload, coba akses lagi website Anda dan login dengan Google. Seharusnya sudah berfungsi normal!

---

## File Lain yang Mungkin Perlu Diupload

Jika masih ada error terkait Google Drive, upload juga file ini (jika ada):

```bash
# Cek di lokal apakah ada file ini
ls -la /home/niko/Desktop/Laravel/Dokumentasi/storage/app/google-drive-token.json
```

Jika ada, upload juga ke VPS dengan cara yang sama.
