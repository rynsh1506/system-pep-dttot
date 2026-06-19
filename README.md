# Sistem Pengecekan PEP & DTTOT (CodeIgniter 4)

Sistem ini digunakan untuk mengecek kesesuaian data nasabah / calon debitur terhadap daftar Terduga Teroris (DTTOT) dan Politically Exposed Persons (PEP). Sistem ini dibangun menggunakan CodeIgniter 4.

## Persyaratan Server
- PHP 8.1 atau lebih tinggi
- Ekstensi PHP: `intl`, `mbstring`, `json`, `curl`, `pdo_sqlsrv`, `sqlsrv` (jika menggunakan SQL Server)
- Composer

## Panduan Instalasi & Deployment ke Server

Berikut adalah langkah-langkah penting saat mendeploy aplikasi ini ke server produksi, terutama untuk menghindari masalah permission (hak akses).

### 1. Clone & Install Dependencies
Setelah melakukan clone atau pull ke server, instal library PHP menggunakan Composer:
```bash
composer install --no-dev --optimize-autoloader
```

### 2. Konfigurasi Environment
Salin file `.env.example` atau `env` bawaan menjadi `.env` lalu sesuaikan dengan konfigurasi server Anda:
```bash
cp env .env
```
Pastikan Anda mengatur konfigurasi database, base URL, dan email tujuan alert di dalam file `.env`.

### 3. Masalah Permissions (Hak Akses) - PENTING!
CodeIgniter 4 memerlukan hak akses tulis (write) pada folder `writable/` untuk menyimpan cache, logs, dan session. Seringkali setelah git pull atau memindahkan file, terjadi error 500 karena masalah permission.

Jalankan perintah berikut di folder root aplikasi untuk mengatur ownership dan permission:

```bash
# Ubah kepemilikan file ke user web server (misalnya www-data untuk Nginx/Apache di Ubuntu)
sudo chown -R www-data:www-data .

# Pastikan folder writable bisa ditulis oleh web server
sudo chmod -R 775 writable
sudo chmod -R 775 public/uploads
```

### 4. Mengatasi Folder yang Terkunci (Permission Denied saat Git Pull)
Jika sebelumnya server menggunakan Docker atau Composer dijalankan sebagai root, folder seperti `vendor/` atau file tertentu bisa terkunci. Jika Anda mengalami error **"Permission denied"** saat menjalankan `git pull` atau pindah branch, hapus paksa folder tersebut dengan `sudo`, lalu jalankan `composer install` ulang:

```bash
# Hapus folder vendor yang terkunci
sudo rm -rf vendor

# Lakukan git pull atau checkout
git pull origin main

# Install ulang dependencies (jangan lupa set chown lagi setelahnya jika perlu)
composer install
sudo chown -R www-data:www-data vendor
```

## Menjalankan dengan Docker
Jika Anda menggunakan Docker (docker-compose), cukup jalankan:
```bash
docker-compose up -d --build
```
Lalu masuk ke container PHP untuk menginstal dependencies:
```bash
docker exec -it <nama_container_app> composer install
```
Penting: Perintah artisan atau composer sebaiknya selalu dijalankan dari dalam container docker untuk menghindari konflik file ownership (root vs user lokal).

## API Documentation
Dokumentasi API Swagger tersedia dan dapat diakses pada rute:
`/api/docs` (Membutuhkan akses login / token yang valid).
