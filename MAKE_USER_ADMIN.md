# Cara Membuat User Pertama Menjadi Admin

## Metode 1: Via MySQL Command (Recommended)

SSH ke VPS dan jalankan:

```bash
# Login ke MySQL
mysql -u laravel_user -p
# Masukkan password database Anda
```

Setelah masuk ke MySQL, jalankan query ini:

```sql
-- Gunakan database yang benar
USE laravel_app;

-- Lihat semua user
SELECT id, name, email, role FROM users;

-- Ubah user pertama (ID=1) menjadi admin
UPDATE users SET role = 'admin' WHERE id = 1;

-- Atau ubah berdasarkan email
UPDATE users SET role = 'admin' WHERE email = 'email@anda.com';

-- Verifikasi perubahan
SELECT id, name, email, role FROM users;

-- Keluar dari MySQL
EXIT;
```

## Metode 2: Via Artisan Tinker

SSH ke VPS dan jalankan:

```bash
cd /var/www/app.narasumberhukum.online
php artisan tinker
```

Di dalam Tinker, jalankan:

```php
// Cari user pertama
$user = \App\Models\User::first();

// Lihat informasi user
echo $user->name . " - " . $user->email . " - " . $user->role;

// Ubah role menjadi admin
$user->role = 'admin';
$user->save();

// Verifikasi
echo "Role sekarang: " . $user->role;

// Keluar dari Tinker
exit
```

## Metode 3: Via Artisan Command (Berdasarkan Email)

SSH ke VPS dan jalankan:

```bash
cd /var/www/app.narasumberhukum.online

# Ubah user menjadi admin berdasarkan email
php artisan tinker --execute="App\Models\User::where('email', 'email@anda.com')->update(['role' => 'admin']);"
```

Ganti `email@anda.com` dengan email user yang ingin dijadikan admin.

## Metode 4: Buat Seeder (Untuk Future Use)

Jika Anda ingin membuat admin otomatis saat setup awal, buat seeder:

**Di komputer lokal**, buat file baru:

`database/seeders/AdminSeeder.php`:

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Ubah email ini dengan email admin Anda
        $adminEmail = 'admin@narasumberhukum.online';
        
        $user = User::where('email', $adminEmail)->first();
        
        if ($user) {
            $user->role = 'admin';
            $user->save();
            $this->command->info("User {$user->email} is now an admin!");
        } else {
            $this->command->error("User with email {$adminEmail} not found!");
        }
    }
}
```

Upload file ini ke VPS, lalu jalankan:

```bash
cd /var/www/app.narasumberhukum.online
php artisan db:seed --class=AdminSeeder
```

## Verifikasi

Setelah mengubah role, logout dari aplikasi dan login kembali. User tersebut seharusnya sudah memiliki akses admin.

Untuk mengecek di database:

```bash
mysql -u laravel_user -p laravel_app -e "SELECT id, name, email, role FROM users;"
```

---

## Troubleshooting

### Jika lupa password MySQL:
Gunakan metode Artisan Tinker (Metode 2).

### Jika tidak tahu email user pertama:
```bash
mysql -u laravel_user -p laravel_app -e "SELECT id, name, email, role FROM users ORDER BY id LIMIT 1;"
```

### Membuat multiple admin sekaligus:
```sql
UPDATE users SET role = 'admin' WHERE email IN ('email1@example.com', 'email2@example.com');
```
