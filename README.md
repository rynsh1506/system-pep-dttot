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

## Penjelasan "Tetek Bengek" Tailwind, Vite, JS & Manifest di CI4

Aplikasi ini menggunakan **Tailwind CSS** dan **JS modern** yang di-build menggunakan **Vite**. 
Meskipun menggunakan framework CodeIgniter 4 (bukan Laravel), konsep build assets-nya tetap sama.

### Alur Kerjanya (Kenapa kadang tampilan hancur/nggak geser?):
1. File asli CSS dan JS Anda berada di dalam folder `resources/css/app.css` dan `resources/js/app.js`.
2. Saat Anda menjalankan `npm run build`, Vite akan membaca file-file tersebut, meng-compile Tailwind CSS, dan membuat file hasil build (ter-minifikasi) ke dalam folder `public/build/assets/`.
3. Vite juga membuat sebuah file **Manifest** di `public/build/.vite/manifest.json`. File manifest ini berisi pemetaan (mapping) nama file asli ke nama file hasil build (contoh: `app.css` dipetakan menjadi `app-DUAPY3vp.css`).
4. Di dalam CodeIgniter (tepatnya di `app/Views/layouts/main.php`), kita membaca file `manifest.json` tersebut untuk mengetahui **nama file CSS dan JS terbaru** yang harus diload ke HTML.

### Penyebab Tampilan Hancur (Manifest tidak sesuai):
- Anda melakukan perubahan di `resources/css/app.css` atau class Tailwind di file `.php`, tetapi **LUPA** menjalankan `npm run build`. Akibatnya class baru tidak ter-generate di file CSS yang ada di `public/build`.
- Folder `public/build` terhapus (seperti saat kita menjalankan `sudo rm -rf public/build` sebelumnya), sehingga `manifest.json` hilang dan CI4 tidak meload CSS/JS sama sekali.
- Anda mem-push ke server, tapi lupa menjalankan `npm run build` di server. (Catatan: Secara default, folder `public/build` diabaikan oleh `.gitignore` sehingga tidak ikut ter-push ke GitHub).

### Solusi & Cara Menangani Front-end:

**Saat Proses Development (Koding Lokal):**
Jalankan perintah ini agar Vite memantau perubahan file secara realtime:
```bash
npm run dev
```

**Saat Mau Deploy ke Server / Production:**
Anda WAJIB mem-build ulang asset CSS & JS agar ter-generate versi terbarunya:
```bash
npm install
npm run build
```
*(Jika di server menggunakan Docker, masuk dulu ke dalam container PHP/Node lalu jalankan perintah di atas).*
