# Sistem Pengecekan PEP & DTTOT (CodeIgniter 4)

Sistem ini digunakan untuk mengecek kesesuaian data nasabah / calon debitur terhadap daftar Terduga Teroris (DTTOT) dan Politically Exposed Persons (PEP). Sistem ini dibangun menggunakan CodeIgniter 4.

## Persyaratan Server
- Docker & Docker Compose
- Lingkungan server wajib mendukung eksekusi container.

---

## Panduan Instalasi & Deployment ke Server (KHUSUS DOCKER)

Karena aplikasi ini dijalankan di dalam container, **semua perintah instalasi, update, dan build HARUS dijalankan dari dalam Docker Container** untuk mencegah bentrok hak akses (permission denied) dengan sistem operasi induk (host).

Asumsi: Nama container PHP/App Anda adalah `pep_test-app-1` (sesuaikan jika berbeda).

### 1. Menjalankan Docker
Setelah clone repository, jalankan container:
```bash
docker-compose up -d --build
```

### 2. Install Dependencies (Composer)
Masuk ke container atau eksekusi perintah composer langsung ke dalam container:
```bash
docker exec -it pep_test-app-1 composer install --no-dev --optimize-autoloader
```

### 3. Konfigurasi Environment
Pastikan file `.env` sudah terbuat dan dikonfigurasi (Database, Base URL, Email).
```bash
cp env .env
```

### 4. Masalah Permissions (Hak Akses `chmod` & `chown`) - PENTING!
CodeIgniter 4 memerlukan hak akses tulis (write) pada folder `writable/`. Karena kita menggunakan Docker, web server di dalam container (biasanya berjalan sebagai user `www-data` atau `root`) harus memiliki izin tersebut.

Jalankan perintah ini **ke dalam container** untuk menyetel permission dengan aman:
```bash
# Ubah kepemilikan file ke user web server di dalam container
docker exec -it pep_test-app-1 chown -R www-data:www-data .

# Pastikan folder writable bisa ditulis
docker exec -it pep_test-app-1 chmod -R 775 writable
docker exec -it pep_test-app-1 chmod -R 775 public/uploads
```

### 5. Mengatasi Error "Permission Denied" Saat Git Pull di Host
Jika Anda terpaksa melakukan `git pull` dari terminal Host/Server (di luar docker) dan mendapat pesan error *Permission Denied* pada folder seperti `vendor/` atau `public/build/`, itu karena folder tersebut di-generate oleh Docker (sebagai root) sehingga user lokal Anda tidak punya akses.

Solusinya, hapus folder tersebut dengan `sudo` di luar docker, lalu jalankan instalasi ulang di dalam docker:
```bash
# Di terminal Host/Luar Docker:
sudo rm -rf vendor public/build

# Tarik kode terbaru:
git pull origin main

# Di dalam Docker (Install ulang):
docker exec -it pep_test-app-1 composer install
docker exec -it pep_test-app-1 npm install
docker exec -it pep_test-app-1 npm run build
```

---

## Penjelasan "Tetek Bengek" Tailwind, Vite, JS & Manifest di CI4 (Khusus Docker)

Aplikasi ini menggunakan **Tailwind CSS** dan **JS modern** yang di-build menggunakan **Vite**. 

### Alur Kerjanya (Kenapa kadang tampilan hancur/nggak geser?):
1. File asli CSS dan JS Anda berada di dalam folder `resources/css/app.css` dan `resources/js/app.js`.
2. Saat perintah `npm run build` dijalankan, Vite akan meng-compile Tailwind CSS dan meminifikasi JS, lalu menyimpannya di folder `public/build/assets/`.
3. Vite juga membuar file **Manifest** di `public/build/.vite/manifest.json`. File manifest ini memetakan nama file asli ke file hasil kompilasi (misal: `app.css` menjadi `app-xyz.css`).
4. Di dalam CI4 (`app/Views/layouts/main.php`), kita memanggil `manifest.json` ini untuk mengetahui file mana yang harus di-load.

### Penyebab Tampilan Hancur:
- Lupa melakukan `npm run build` setelah mengubah kode CSS/Tailwind.
- Folder `public/build` terhapus sehingga manifest hilang.
- Anda melakukan `git pull` di server tapi tidak menjalankan build ulang (karena `public/build` sengaja diabaikan di `.gitignore` agar tidak membebani repository).

### Solusi & Cara Menangani Front-end via Docker:

**A. Saat Koding / Development Lokal:**
Anda bisa membiarkan Vite berjalan di background untuk memantau perubahan secara realtime di dalam container:
```bash
docker exec -it pep_test-app-1 npm run dev
```

**B. Saat Deploy ke Server / Production:**
Setiap ada update kode ke server, **WAJIB** mem-build ulang asset CSS & JS dari dalam container:
```bash
docker exec -it pep_test-app-1 npm install
docker exec -it pep_test-app-1 npm run build
```

---
## API Documentation
Dokumentasi API Swagger tersedia dan dapat diakses pada rute:
`/api/docs` (Membutuhkan akses login / token yang valid).
