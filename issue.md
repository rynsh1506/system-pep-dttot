# Panduan Migrasi Logika: Dari Legacy PHP ke Laravel TALL Stack

Dokumen ini disusun sebagai panduan teknis yang sangat mendetail untuk memigrasikan fitur-fitur dari *legacy code* PHP Native (yang saat ini berada di folder `legacy/`) ke sistem berbasis Laravel TALL Stack. 

Ikuti panduan ini langkah demi langkah. Jika ada *error*, kerjakan secara bertahap dan jangan berpindah ke fase berikutnya jika fase sebelumnya belum tuntas.

---

## Fase 1: Migrasi Otentikasi & Model User

Fase ini bertujuan untuk mengaktifkan sistem Login agar user dapat masuk ke dalam sistem dengan koneksi database `cadeb_db` yang sudah dikonfigurasi di file `.env`.

### 1.1 Membuat Model User Kustom
1. Jalankan perintah: `php artisan make:model User` (Timpa file bawaan jika sudah ada).
2. Di dalam file `app/Models/User.php`, tambahkan property koneksi database:
   ```php
   protected $connection = 'cadeb';
   protected $table = 'users';
   ```
3. Sesuaikan array `$fillable` dengan struktur tabel yang ada di file `legacy/users.php` (misal: `username`, `password`, `nama_lengkap`, `level`).
4. Matikan *timestamps* (tambahkan `public $timestamps = false;`) jika tabel `users` lama tidak memiliki kolom `created_at` dan `updated_at`.

### 1.2 Konfigurasi Laravel Auth
1. Buka `config/auth.php`.
2. Pastikan `providers.users.driver` bernilai `eloquent` dan modelnya diarahkan ke `App\Models\User::class`.
3. Cek kembali fungsi `password_verify` di kode legacy (`legacy/login.php`). Karena *password* sudah menggunakan fungsi standar `password_hash()`, otentikasi bawaan `Auth::attempt()` di Laravel otomatis kompatibel.

### 1.3 Membuat Livewire Login Component
1. Jalankan `php artisan make:livewire Auth/Login`.
2. Pada komponen Livewire (`app/Livewire/Auth/Login.php`), buat properti `$username` dan `$password`.
3. Buat metode `login()`. Logika sederhananya:
   - Gunakan `Auth::attempt(['username' => $this->username, 'password' => $this->password])`.
   - Jika berhasil, simpan session penting yang sering dipakai aplikasi legacy:
     `session(['role_level' => Auth::user()->level, 'full_name' => Auth::user()->nama_lengkap]);`
   - Lalu *redirect* menggunakan `return $this->redirect('/', navigate: true);`.
4. Di file *view* login (`resources/views/livewire/auth/login.blade.php`), bangun form login menggunakan komponen Tailwind & DaisyUI (contoh: `<input class="input input-bordered w-full" wire:model="username">`).

---

## Fase 2: Layout Utama (Master Blade) & Dashboard

Setelah berhasil login, langkah selanjutnya adalah menyiapkan "Rumah" untuk fitur-fitur yang lain.

### 2.1 Pembuatan Layout Component
1. Pindahkan struktur HTML dasar yang ada di `legacy/layout/header.php` dan `legacy/layout/footer.php` menjadi satu file utuh di `resources/views/components/layouts/app.blade.php`.
2. Desain ulang menggunakan *utility class* Tailwind CSS (misalnya membuat susunan grid untuk Sidebar di kiri dan Content di kanan).
3. Untuk Sidebar dan Topbar, manfaatkan *DaisyUI Components* (seperti `drawer` dan `navbar`).
4. Ubah logika PHP lama yang mengecek session (`if($_SESSION['role_level'] == 4)`) menjadi sintaks Blade: `@if(session('role_level') == 4)`.

### 2.2 Migrasi Dashboard Utama
1. Jalankan `php artisan make:livewire Dashboard`.
2. Lihat kode `legacy/index.php`. Disana ada pengambilan total *Terduga*, *Orang*, dan *Korporasi*.
3. Buat **Model Eloquent** untuk tabel terkait: `php artisan make:model Terduga`.
   - Pastikan model ini mengarah ke default connection (`dtot`).
   - Ubah logika *query mentah* `$pdo->query("SELECT COUNT(*) ...")` menjadi `Terduga::whereNull('deleted_at')->count();` di dalam komponen Livewire Dashboard.
4. Tampilkan data statistik tersebut di UI *Dashboard* menggunakan *Card* dari DaisyUI (`<div class="card bg-base-100 shadow-xl">`).

---

## Fase 3: Migrasi Core Logic (Data & Pengajuan)

Fitur utama aplikasi ini adalah manajemen data (PEP/DTTOT) dan alur persetujuan (Approvals). 

### 3.1 Manajemen Data (Add / Edit / Delete)
1. Tinjau file `legacy/add_data.php` dan `legacy/save_data.php`.
2. Buat komponen Livewire baru: `php artisan make:livewire Terduga/Create`.
3. Terjemahkan validasi manual (`if(empty($_POST['nama']))`) menjadi fitur validasi Livewire menggunakan properti `#[Validate('required')]` di atas setiap deklarasi variabel.
4. Simpan data menggunakan `Terduga::create([...])`.
5. Ubah semua peringatan *alert javascript* (`echo "<script>alert('Sukses')</script>"`) menjadi *Flash Session* (`session()->flash('message', 'Sukses');`) dan tampilkan secara elegan di UI.

### 3.2 Proses Persetujuan (Approval Flow)
1. Buat Model `ChangeRequest`.
2. Tinjau `legacy/approvals.php`. Pada versi lama, aksi ubah status (Setujui/Tolak) dikirim via parameter GET/POST.
3. Di dalam komponen Livewire *Approval*, tangani tombol *Approve* dengan *method* `public function approve($id)`. 
4. Di dalam metode tersebut, jalankan proses transaksi database:
   ```php
   DB::transaction(function () use ($id) {
       $request = ChangeRequest::find($id);
       $request->update(['status' => 'APPROVED_SPV']);
       // Trigger pengiriman email notifikasi di sini.
   });
   ```

---

## Fase 4: Migrasi User Management & Export

### 4.1 User Management
1. Tinjau `legacy/users.php`.
2. Buat komponen Livewire `User/Index` untuk menampilkan tabel daftar pengguna dari database `cadeb_db`.
3. Jadikan fitur *Add/Edit/Delete* sebagai Modal DaisyUI (gunakan Alpine.js `@click="modal_add.showModal()"`).

### 4.2 Laporan / Export Data
1. Karena kode lama mengandalkan *library* PhpSpreadsheet mentah, direkomendasikan untuk beralih ke *package* **Laravel Excel** (`maatwebsite/excel`).
2. Instal package: `composer require maatwebsite/excel`.
3. Ubah alur rumit di `legacy/export_excel.php` dengan membuat *Class Export* khusus: `php artisan make:export TerdugaExport`.
4. Tarik data via Eloquent, petakan kolom yang diinginkan di fungsi `map()`, dan kembalikan *download response* lewat Controller.

---

**Catatan untuk Implementator:**
Setiap kali menyelesaikan satu *task* (contoh: Selesai Fase 1.1), **selalu lakukan Git Commit** secara mandiri agar riwayat perubahan rapi dan dapat di-*rollback* jika terjadi kesalahan teknis.
