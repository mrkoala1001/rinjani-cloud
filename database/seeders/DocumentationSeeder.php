<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Documentation;

class DocumentationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $content = <<<'EOT'
# Panduan Deployment HOT POT ke VPS (Metode Manual)

Panduan ini akan membantu Anda mengupload dan menjalanan aplikasi HOT POT di Virtual Private Server (VPS) menggunakan metode upload manual (tanpa Git).

## Prasyarat Server
Pastikan VPS Anda sudah terinstall:
- **Web Server**: Nginx (Direkomendasikan) atau Apache
- **PHP**: Versi 8.1 atau lebih baru
- **Database**: MySQL 8.0+ atau MariaDB 10.6+
- **Composer** (PHP Dependency Manager)
- **Node.js & NPM** (Untuk build aset frontend)
- **Unzip**: Aplikasi untuk mengekstrak file (`sudo apt install unzip`)

## Quick Setup (Ubuntu 22.04/24.04)
Berikut adalah perintah lengkap untuk menginstall seluruh kebutuhan server dalam satu kali jalan. Copy dan paste perintah ini di terminal VPS Anda (sebagai root):

```bash
# 1. Update & Upgrade Sistem
apt update && apt upgrade -y

# 2. Install Nginx, MySQL, Unzip, & Git
apt install -y nginx mariadb-server unzip git curl

# 3. Install PHP 8.2 dan Ekstensi yang dibutuhkan
apt install -y software-properties-common
add-apt-repository ppa:ondrej/php -y
apt update
apt install -y php8.2 php8.2-cli php8.2-fpm php8.2-mysql php8.2-curl php8.2-gd php8.2-mbstring php8.2-xml php8.2-zip php8.2-bcmath

# 4. Install Composer (PHP Dependency Manager)
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer

# 5. Install Node.js (Versi 20 LTS)
curl -fsSL https://deb.nodesource.com/setup_20.x | bash -
apt install -y nodejs

# 6. Secure Database (Jawab Y untuk semua, set password root database)
mysql_secure_installation

# Cek Instalasi
php -v
composer -V
node -v
npm -v
nginx -v
mysql --version
```

## Langkah-langkah Deployment

### 1. Persiapan File di Komputer Lokal
Sebelum mengupload, kita perlu menyiapkan file project agar bersih dan ringan.

1.  Pastikan Anda berada di folder project HOT POT.
2.  Hapus folder `vendor` dan `node_modules` (jika ada) untuk mengurangi ukuran upload. Kita akan menginstallnya ulang di server.
3.  Compress/Zip seluruh folder project Anda menjadi `hotpot.zip`.
    *   **Windows**: Klik kanan folder project -> Send to -> Compressed (zipped) folder.
    *   **Mac/Linux**: Gunakan terminal atau klik kanan -> Compress.

### 2. Upload File ke VPS
Anda bisa menggunakan aplikasi seperti **FileZilla** (GUI) atau perintah `scp` (Terminal).

**Opsi A: Menggunakan FileZilla (Mudah)**
1.  Buka FileZilla, masukkan IP VPS, Username (biasanya `root`), Password, dan Port (22).
2.  Navigasi panel kanan (Remote Site) ke folder `/var/www`.
3.  Drag & drop file `hotpot.zip` dari komputer Anda ke panel kanan.

**Opsi B: Menggunakan SCP (Terminal)**
```bash
scp hotpot.zip root@ip-vps-anda:/var/www/
```

### 3. Ekstrak dan Setup Folder
Login ke VPS Anda via SSH (Putty atau Terminal):
```bash
ssh root@ip-vps-anda
```

Jalankan perintah berikut:
```bash
cd /var/www
unzip hotpot.zip -d hotpot
cd hotpot
```

### 4. Install Dependensi di VPS
Karena kita tidak mengupload folder `vendor`, kita perlu menginstallnya di VPS.

Install PHP Dependencies:
```bash
composer install --optimize-autoloader --no-dev
```

Install Frontend Dependencies:
```bash
npm install
npm run build
```

### 5. Konfigurasi Environment (.env)
Copy file contoh konfigurasi:
```bash
cp .env.example .env
nano .env
```
Edit konfigurasi berikut:
- `APP_URL=https://domain-anda.com`
- `APP_ENV=production`
- `APP_DEBUG=false`
- `DB_...` (Sesuaikan dengan kredensial database VPS Anda)

Simpan (Ctrl+O, Enter) dan Keluar (Ctrl+X).

Generate Key:
```bash
php artisan key:generate
```

### 6. Setup Database & Permission
Jalankan migrasi database:
```bash
php artisan migrate --seed --force
```

Setup Permission Folder (Wajib agar tidak error permission denied):
```bash
# Ganti www-data dengan user webserver Anda (nginx/apache biasanya www-data)
chown -R www-data:www-data /var/www/hotpot
chmod -R 775 /var/www/hotpot/storage
chmod -R 775 /var/www/hotpot/bootstrap/cache
php artisan storage:link
```

### 7. Konfigurasi Nginx
Buat konfigurasi server block:
```bash
nano /etc/nginx/sites-available/hotpot
```

Isi dengan (sesuaikan domain):
```nginx
server {
    listen 80;
    server_name domain-anda.com;
    root /var/www/hotpot/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;
    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock; # Sesuaikan versi PHP Anda
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

Aktifkan dan restart Nginx:
```bash
ln -s /etc/nginx/sites-available/hotpot /etc/nginx/sites-enabled/
nginx -t
systemctl restart nginx
```

### 8. Finalisasi
Selesai! Akses domain Anda di browser. Jika ingin HTTPS, jalankan Certbot:
```bash
apt install certbot python3-certbot-nginx
certbot --nginx -d domain-anda.com
```
EOT;

        Documentation::updateOrCreate(
            ['slug' => 'deployment-vps'],
            [
                'title' => 'Panduan Deployment ke VPS (Manual)',
                'content' => $content,
                'order' => 1,
                'is_published' => true,
            ]
        );
    }
}
