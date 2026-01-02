<p align="center"><strong>Family Album</strong> – aplikasi galeri keluarga berbasis Laravel + Inertia + Vue.</p>

## Ringkasan
- Kelola album dan unggah foto setelah login.
- Tampilan modern dengan tema gelap.
- Akses dibatasi: menu daftar (registrasi) nonaktif, hanya login.

## Persyaratan
- PHP 8.1+, Composer
- Node.js 18+, npm
- Database MySQL/MariaDB (atau driver lain yang didukung Laravel)

## Instalasi & Konfigurasi
1) Clone proyek lalu pasang dependensi:
   ```bash
   composer install
   npm install
   ```
2) Salin konfigurasi lingkungan:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
3) Atur koneksi database di `.env` (DB_HOST, DB_DATABASE, DB_USERNAME, DB_PASSWORD).

## Migrasi & Seeder
Jalankan migrasi dan seeder untuk membuat tabel serta akun admin default:
```bash
php artisan migrate --seed
```

## Menjalankan Aplikasi
- Backend: `php artisan serve`
- Frontend (dev): `npm run dev`
- Build produksi (diperlukan setelah perubahan frontend bila memakai aset build): `npm run build`

## Struktur Singkat
- `resources/js/Pages/Albums` – halaman daftar & detail album.
- `resources/js/Components` – komponen UI (mis. `TextInput.vue`).
- `database/seeders/DatabaseSeeder.php` – seeder akun admin.

## Deploy Cepat
```bash
composer install --optimize-autoloader --no-dev
php artisan config:cache
php artisan route:cache
npm run build
```

## Lisensi
MIT.
