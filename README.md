<p align="center">
  <h1 align="center">PEP & DTTOT Verification System</h1>
  <p align="center">Sistem Terintegrasi Pengecekan CADEB terhadap Database DTTOT dan Portal PEP PPATK.</p>
</p>

## 📋 Tentang Sistem

Aplikasi ini adalah hasil migrasi dan modernisasi dari sistem *legacy* CodeIgniter 3 ke ekosistem **Laravel 11**. Sistem ini dirancang khusus untuk memenuhi standar kepatuhan (*compliance*) AML/CFT (Anti-Money Laundering and Combating the Financing of Terrorism) dengan memverifikasi calon debitur (CADEB) atau karyawan.

### Fitur Utama:
- **Manajemen DTTOT:** Upload masif, pencarian terintegrasi, dan sistem persetujuan (*Maker-Checker*).
- **Pengecekan CADEB:** Pencocokan *real-time* ke database DTTOT lokal dan *scrapping* otomatis ke portal PEP eksternal.
- **Reksaloan (HRD):** Verifikasi data karyawan yang terhubung langsung dengan *view* database eksternal perusahaan.
- **Laporan & Ekspor:** *Filter* lanjutan berdasarkan status terindikasi DTTOT/PEP dan ekspor data CSV.

---

## 🚀 Teknologi yang Digunakan

- **Backend:** Laravel 11 (PHP 8.2)
- **Frontend:** Livewire 3 + Alpine.js
- **Styling:** Tailwind CSS + DaisyUI (Glassmorphism UI)
- **Database:** MariaDB / MySQL 8.0
- **Deployment:** Docker & Nginx Alpine

---

## ⚙️ Persyaratan Sistem (Prerequisites)

Sebelum menginstal, pastikan mesin Anda telah memiliki perangkat lunak berikut:
1. **Docker Desktop / Docker Engine** (versi terbaru)
2. **Docker Compose**
3. **Database Server Lokal (XAMPP / Laragon / MariaDB native)** - *Jika tidak menggunakan container DB.*

---

## 🛠️ Instalasi & Setup Lokal (Docker)

Ikuti langkah-langkah di bawah ini untuk menjalankan sistem di lingkungan pengembangan lokal Anda.

### 1. Kloning Repositori
```bash
git clone https://github.com/rynsh1506/system-pep-dttot.git
cd system-pep-dttot
```

### 2. Persiapkan Konfigurasi Environment (`.env`)
Salin file konfigurasi bawaan.
```bash
cp .env.example .env
```
Buka file `.env` dan pastikan konfigurasi database sudah mengarah ke server MariaDB lokal Anda (biasanya `172.17.0.1` jika dari dalam Docker ke Host).
```env
DB_DTOT_CONNECTION=mariadb
DB_DTOT_HOST=172.17.0.1
DB_DTOT_PORT=3306
DB_DTOT_DATABASE=db_dtot
DB_DTOT_USERNAME=root
DB_DTOT_PASSWORD=

DB_CADEB_CONNECTION=mariadb
DB_CADEB_HOST=172.17.0.1
DB_CADEB_PORT=3306
DB_CADEB_DATABASE=cadeb_db
DB_CADEB_USERNAME=root
DB_CADEB_PASSWORD=
```

### 3. Build & Jalankan Docker Containers
```bash
docker-compose up -d --build
```
*Perintah ini akan menjalankan dua container: `system-pep-dttot-app-1` (PHP 8.2 FPM) dan webserver Nginx.*

### 4. Instalasi Dependensi PHP (Composer)
```bash
docker exec system-pep-dttot-app-1 composer install
```

### 5. Instalasi Dependensi Node.js & Build Aset
```bash
docker exec system-pep-dttot-app-1 npm install
docker exec system-pep-dttot-app-1 npm run build
```

### 6. Generate Application Key
```bash
docker exec system-pep-dttot-app-1 php artisan key:generate
```

### 7. Migrasi Database & Seeding Data Awal
```bash
docker exec system-pep-dttot-app-1 php artisan migrate --seed
```

---

## 🌐 Mengakses Aplikasi

Setelah semua langkah selesai, aplikasi dapat diakses melalui peramban (browser) di:
**http://localhost:8000**

### Akun Uji Coba (Bawaan Seeder)
Gunakan akun berikut untuk menguji *Role-Based Access Control* (RBAC) pada sistem:

- **Super Admin**
  - Email: `superadmin@example.com`
  - Password: `password`
- **Admin**
  - Email: `admin@example.com`
  - Password: `password`

---

## 🖥️ Command Berguna Tambahan

Jika Anda melakukan perubahan pada antarmuka Livewire atau *view* Blade, Anda mungkin perlu membersihkan *cache*:
```bash
# Membersihkan Cache View
docker exec system-pep-dttot-app-1 php artisan view:clear

# Menjalankan Vite Hot Reloading (saat development CSS/JS)
docker exec system-pep-dttot-app-1 npm run dev
```

---

## 📝 Catatan Migrasi Legacy
- **Data Session / Draft:** Proses input pencarian sudah dilengkapi sistem *persistence*. Data Anda tidak akan hilang jika secara tak sengaja berpindah halaman.
- **Scrapper NIK:** API scrapper lama ke PPATK berjalan menggunakan Javascript murni di latar belakang demi menjaga stabilitas sesi Livewire. JANGAN hapus script integrasi di dalam form pencarian.
