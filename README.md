<p align="center"><strong>Family Album</strong> - galeri keluarga berbasis Laravel + Inertia + Vue.</p>

## 🎯 Fitur Utama
- **Autentikasi**: Akses hanya setelah login dengan redirect otomatis
- **Manajemen Album**: Buat, edit, dan hapus album dengan konfirmasi
- **Upload Foto**: Unggah multiple foto dengan metadata (judul, caption, tanggal)
- **Lazy Loading**: Loading foto yang smooth dengan skeleton dan error handling
- **Responsive Design**: Tema gelap modern dengan masonry grid layout
- **Real-time UI**: Loading states dan feedback yang responsif

## 🛠 Tech Stack
- **Backend**: Laravel 10 + PHP 8.1+
- **Frontend**: Vue 3 + Inertia.js + Tailwind CSS
- **Database**: MySQL/MariaDB
- **Build Tool**: Vite

## 📋 Persyaratan
- PHP 8.1+, Composer
- Node.js 18+, npm
- Database MySQL/MariaDB

## 🚀 Instalasi

### 1. Clone & Install Dependencies
```bash
git clone <repository-url>
cd family_albums
composer install
npm install
```

### 2. Environment Setup
```bash
cp .env.example .env
php artisan key:generate
```

### 3. Database Configuration
Edit `.env` file:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=family_album
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 4. Storage Setup
```bash
php artisan storage:link
```

### 5. Database Migration
```bash
php artisan migrate --seed
```

## 🏃‍♂️ Menjalankan Aplikasi

### Development
```bash
# Backend server
php artisan serve

# Frontend development (hot reload)
npm run dev
```

### Production Build
```bash
npm run build
```

## 📁 Struktur Project
```
resources/js/
├── Components/
│   ├── LazyImage.vue      # Komponen loading foto
│   ├── Loading.vue        # Global loading overlay
│   └── ...                # UI components lainnya
├── Pages/Albums/
│   ├── Index.vue          # Daftar album
│   └── Show.vue           # Detail album & foto
├── Layouts/
│   └── AlbumLayout.vue    # Layout utama
└── composables/
    └── useLoading.js      # Loading state management
```

## 🎨 Komponen Utama

### LazyImage Component
- Skeleton loading dengan spinner
- Error handling dengan fallback
- Lazy loading untuk performa optimal
- Fixed dimensions untuk layout stability

### Loading States
- Global loading overlay untuk operasi CRUD
- Per-image loading dengan smooth transitions
- Error states dengan retry options

## 🚀 Deploy Production

```bash
# Optimize dependencies
composer install --optimize-autoloader --no-dev

# Database setup
php artisan migrate --force --seed
php artisan storage:link

# Cache optimization
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Build assets
npm run build

# Set permissions
chmod -R 755 storage bootstrap/cache
```

## 🔧 Konfigurasi Server

### Apache
```apache
<VirtualHost *:80>
    DocumentRoot /path/to/family_albums/public
    <Directory /path/to/family_albums/public>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

### Nginx
```nginx
server {
    listen 80;
    root /path/to/family_albums/public;
    index index.php;
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_index index.php;
        include fastcgi_params;
    }
}
```

## 📝 Default Login
Setelah seeder dijalankan:
- **Email**: test@example.com
- **Password**: password

## 🤝 Contributing
1. Fork repository
2. Create feature branch
3. Commit changes
4. Push to branch
5. Create Pull Request

## 📄 License
MIT License