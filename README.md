<p align="center"><strong>Family Album</strong> ? galeri keluarga berbasis Laravel + Inertia + Vue.</p>

## Ringkasan
- Akses hanya setelah login; halaman root langsung diarahkan ke login.
- Buat/kelola album, unggah foto, pratinjau, dan hapus dengan konfirmasi.
- Tema gelap modern dengan masonry grid dan tombol aksi mengambang.

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
4) Buat symlink storage untuk akses file upload:
   ```bash
   php artisan storage:link
   ```

## Migrasi & Seeder
Jalankan migrasi dan seeder untuk membuat tabel serta akun awal:
```bash
php artisan migrate --seed
```

## Menjalankan Aplikasi
- Backend: `php artisan serve`
- Frontend (dev): `npm run dev`
- Build produksi (pakai aset build): `npm run build`

## Struktur Singkat
- `resources/js/Pages/Albums` ? halaman daftar dan detail album.
- `resources/js/Components` ? komponen UI (mis. `TextInput.vue`, `PrimaryButton.vue`).
- `database/seeders/DatabaseSeeder.php` ? akun bawaan untuk login.
- `routes/web.php` ? rute, proteksi auth, token terenkripsi untuk detail album.

## Deploy Cepat
```bash
composer install --optimize-autoloader --no-dev
php artisan migrate --force --seed
php artisan storage:link
php artisan config:cache
php artisan route:cache
npm run build
```

## Lisensi
MIT.
