# Rencana Migrasi Sistem PEP & DTTOT ke TALL Stack (Laravel)

Dokumen ini berisi panduan teknis yang sangat detail untuk memigrasikan *legacy code* PHP native ke framework modern menggunakan **TALL Stack** (Tailwind CSS, Alpine.js, Laravel, Livewire). Dokumen ini disusun sedemikian rupa agar mudah diikuti tahap demi tahap oleh *Junior Programmer* maupun *AI Assistant* yang bertugas melakukan implementasi.

---

## 1. Persiapan Infrastruktur (Docker & Ekstensi)

Aplikasi harus berjalan di atas container Docker. **PENTING**: Jangan buat container untuk database, karena sistem akan "menembak" ke database eksternal yang sudah ada.

**Tugas Dockerfile & docker-compose.yml:**
1. Gunakan base image resmi PHP-FPM (misal: `php:8.2-fpm`).
2. Instal ekstensi standar PHP yang dibutuhkan Laravel (`pdo_mysql`, `mbstring`, `xml`, dll).
3. **Instalasi Driver ODBC SQL Server**:
   - Di dalam Dockerfile, wajib menambahkan perintah untuk mengunduh dan menginstal **Microsoft ODBC Driver for SQL Server** (misalnya `msodbcsql17` atau `msodbcsql18`).
   - Instal dependensi pendukung: `apt-get install unixodbc-dev`.
   - Instal ekstensi PHP untuk SQL Server via PECL: `pecl install sqlsrv pdo_sqlsrv` dan *enable* ekstensinya.
4. **Instalasi Composer**: Tambahkan *command* untuk menyalin binary Composer terbaru ke dalam container (`COPY --from=composer:latest /usr/bin/composer /usr/bin/composer`).
5. Setup `docker-compose.yml` hanya berisi 2 service: `app` (PHP-FPM) dan `web` (Nginx/Apache). Map port lokal ke container web.

---

## 2. Inisialisasi TALL Stack (Laravel, Livewire, Tailwind, Alpine)

1. Lakukan instalasi proyek Laravel kosong di dalam direktori kerja.
2. Konfigurasi `composer.json` dan jalankan instalasi **Livewire**: `composer require livewire/livewire`.
3. Lakukan inisialisasi frontend dengan Vite:
   - `npm install -D tailwindcss postcss autoprefixer alpinejs`
   - Buat konfigurasi Tailwind (`npx tailwindcss init -p`).
4. Pastikan Alpine.js di-*load* di dalam file konfigurasi JavaScript utama (biasanya `resources/js/app.js`).
5. Buat kerangka layout utama (`app.blade.php`) yang menyertakan `@livewireStyles` dan `@livewireScripts`.

---

## 3. Sistem Desain Berbasis Golden Ratio (1.618)

Desain antarmuka harus terlihat modern, ringan, tapi tetap memiliki struktur visual yang sangat presisi. Seluruh skala baik untuk warna maupun tipografi harus berpedoman pada **Golden Ratio (1.618)**.

**Langkah Konfigurasi `tailwind.config.js`:**
1. **Tipografi (Typographic Scale)**:
   Buat skala ukuran font (Text Sizes) di dalam tema Tailwind dengan basis `1rem` (16px) yang dikalikan/dibagi dengan `1.618`.
   *Contoh implementasi skala:*
   - `xs`: `16px / 1.618` ≈ `0.618rem`
   - `base` (default): `1rem`
   - `md`: `1rem * 1.618` ≈ `1.618rem`
   - `lg`: `1rem * 1.618^2` ≈ `2.618rem`
   - `xl`: `1rem * 1.618^3` ≈ `4.236rem`
   *(Masukkan nilai-nilai ini di bagian `theme.fontSize` pada tailwind config).*

2. **Warna & Tema (Light & Dark Mode)**:
   - Wajib mendefinisikan 2 buah tema (Gelap dan Terang). Gunakan strategi *CSS Variables* atau fitur `darkMode: 'class'` bawaan Tailwind.
   - Buat skala *lightness/luminance* dari warna utama (*Primary Color*) menggunakan pembagi 1.618. 
   - Konfigurasikan warna-warna skala tersebut ke dalam `tailwind.config.js` agar developer tinggal memanggil class seperti `bg-primary-dark`, `text-primary-light`, dsb.

---

## 4. Pengaturan Konfigurasi (Environment Variables)

Pada *legacy code*, pengaturan koneksi database (dtot & cadeb) tersebar di folder `/config`. Dalam Laravel:
1. Pindahkan **semua kredensial** tersebut murni ke file `.env` root.
   - Contoh: `DB_CADEB_HOST`, `DB_CADEB_DATABASE`, `DB_DTOT_HOST`, `DB_DTOT_DATABASE`, dll.
2. Di file `config/database.php` Laravel, daftarkan koneksi tersebut di dalam array `connections`. 
3. *Strict rule*: Tidak boleh ada kredensial atau IP server *hardcoded* di dalam file PHP mana pun. Semua harus merujuk ke fungsi `env('KUNCI_ENV')`.

---

## 5. Implementasi Frontend Modern & Ringan (SPA-like)

1. **Gunakan Livewire untuk Transisi Halaman**:
   Untuk membuat pengalaman aplikasi ala SPA (*Single Page Application*) tanpa beban Javascript framework yang berat, gunakan fitur **Livewire Navigate** (tambahkan atribut `wire:navigate` pada tag `<a>` untuk pindah halaman tanpa *full-page reload*).
2. **Alpine.js untuk Interaktivitas**:
   Gunakan murni Alpine.js (`x-data`, `x-show`, `x-on:click`) untuk fitur modal, *dropdown*, peringatan (alert), dan panel samping (sidebar). Hindari penulisan Vanilla JS atau jQuery terpisah.

---

## 6. Migrasi Logika & Keamanan Query (Anti-Spaghetti SQL)

1. Hilangkan seluruh metode eksekusi PDO mentah (*raw query*) yang ada di kode lama.
2. Buat **Model Eloquent** untuk setiap tabel yang ada.
   - Gunakan fitur koneksi spesifik pada Model (misal: `protected $connection = 'cadeb';`) jika Model tersebut milik database khusus.
3. Ubah logika CRUD menggunakan standar Eloquent (`User::create()`, `Terduga::where()->paginate()`). Ini akan otomatis menutup celah *SQL Injection* dan menyederhanakan kode.

---

## 7. Catatan Keamanan File (Gitignore)

Pada saat proses pembaruan (commit & push) ke repositori kontrol versi, dokumentasi internal tidak boleh diunggah:
- File `promt.txt` dan `issue.md` ini wajib ditambahkan ke dalam `.gitignore`.

**Target Akhir:**
Sistem berjalan mulus di Docker dengan koneksi SQL Server (ODBC) dan MySQL yang aman (via `env`), memiliki UI TALL Stack super ringan dengan presisi estetika *Golden Ratio*, memiliki 2 mode tema, dan tanpa adanya barisan *raw* SQL query yang berantakan.
