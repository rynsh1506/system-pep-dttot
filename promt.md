# BUSINESS REQUIREMENTS DOCUMENT (BRD)

**Information Technology Division**

## 1. INFORMASI DOKUMEN

- **Kode Dokumen:** BRD-IT-2026-001
- **Versi:** 1
- **Tanggal:** Februari 2026
- **Dibuat Oleh:** Divisi Teknologi Informasi

## 2. RINGKASAN PROYEK

- **Nama Aplikasi:** Sistem DTTOT (Daftar Terduga Teroris dan Organisasi Teroris)
- **Ringkasan:** Dokumen ini merupakan Business Requirements Document (BRD) untuk pengembangan Sistem DTTOT — sebuah aplikasi terpusat yang digunakan untuk mengelola daftar individu atau entitas yang terindikasi berisiko tinggi terhadap operasional perusahaan. Saat ini pengelolaan data DTTOT dilakukan secara manual dan tersebar, sehingga tidak efisien dan rawan kesalahan. Sistem ini akan menjadi single source of truth bagi seluruh unit bisnis dalam proses verifikasi dan pengambilan keputusan.
- **Tujuan:**
    - Mengelola data DTTOT secara terpusat, aman, dan terstandarisasi.
    - Mempercepat proses verifikasi calon debitur dalam pengajuan kredit (pinjaman).
    - Mendukung kepatuhan terhadap regulasi (OJK, PPATK, UU PDP).

## 3. DATA DAN STRUKTUR SISTEM

Berikut adalah field data yang dibutuhkan:

1. **Nama:** Tipe Varchar (1500). Nama lengkap individu/entitas sesuai dokumen identitas resmi. (Wajib)
2. **Deskripsi:** Tipe Varchar (1000). Uraian lengkap alasan masuk daftar negatif, kronologi, dan informasi relevan lainnya. (Wajib)
3. **Terduga:** Tipe Varchar (20). Status: Terduga / Terkonfirmasi / Dalam Investigasi. Menunjukkan tingkat kepastian data. (Wajib)
4. **Kode Densus:** Tipe Varchar (20). Kode unik sistem. (Wajib)
5. **Tempat Lahir:** Tipe Varchar (100). Kota/kabupaten tempat lahir sesuai dokumen identitas. (Wajib)
6. **Tanggal Lahir:** Tipe Varchar (100). Tanggal lahir sesuai dokumen identitas resmi. Gunakan date picker pada UI. (Wajib)
7. **Alamat:** Tipe Varchar (1000). Alamat lengkap terakhir. (Wajib)
8. **WN / Asal Negara:** Tipe Varchar (100). Kewarganegaraan atau negara asal. (Wajib)

## 4. METADATA SISTEM (OTOMATIS)

- **Kode Khusus:** Auto-increment, format NC-[YYYY]-[NNNNNN], unique index di database.
- **Tanggal & Waktu Input (Upload):** Timestamp saat data pertama kali disimpan (upload).
- **Dibuat Oleh:** User ID dan nama pengguna yang melakukan input (upload).
- **Sumber Data:** Asal informasi (Input).
- **Nomor Referensi:** Nomor surat/keputusan yang menjadi dasar pencatatan (Input).

## 5. KEBUTUHAN FUNGSIONAL

### A. Pengecekan PEP untuk Calon Debitur

- Ketika CMO menginput data calon debitur di Mobile Marketing, mobile marketing memanggil API system internal PEP.
- API system internal PEP mentrigger pengecekan ke website PEP PPATK melalui API/RPA(※1).
- Hasil pengecekan diinput secara otomatis di satu field mobile marketing (tidak bisa diubah) dan diteruskan ke proses berikutnya.
- Apabila hasil pengecekannya ‘terduga’/’terkonfirmasi’/’dalam investigasi’, maka nomor KTP tersebut disimpan di database internal PEP (untuk digunakan Ketika pengecekan ke Website PPATK gagal).
- (※1) Ada fungsi timeout: Ketika mengecek lewat dari 5 menit, System akan otomatis melakukan pengecekan menggunakan data yang ada di database PEP internal.
- Hasil dari pengecekan akan disimpan di salah satu field di table customer dan ditampilkan di layer approval Reksaloan.

### B. Pengecekan PEP untuk Debitur Existing

- Setiap tanggal XX setiap bulannya, system internal PEP secara otomatis menarik data debitur dari Reksaloan, dan menarik daftar PEP dengan menggunakan API/RPA dari website PPATK, dan mengupdate database PEP internal dengan daftar tersebut.
- Apabila waktu pengecekan ke website PPATK terjadi timeout, maka sistem mencoba lagi setelah 1 jam sampai 3 kali percobaan.
- Apabila sampai 3x masih timeout, maka system mengirim notifikasi ke ACO untuk mengupdate database PEP internal secara manual, dan menginfokan kalau database PEP internal sudah diupdate.
- Info dari ACO diatas mentrigger ulang pengecekan PEP terhadap data debitur existing terhadap database PEP internal.
- Hasil dari pengecekan akan disimpan di salah satu field di table customer dan ditampilkan di layer approval Reksaloan.

### C. Pengecekan DTTOT untuk Calon Debitur

- Ketika CMO menginput data calon debitur di Mobile Marketing, mobile marketing memanggil API system internal DTTOT.
- API system internal DTTOT mentrigger pengecekan terhadap database DTTOT Internal.
- Hasil pengecekan diinput secara otomatis di satu field mobile marketing (tidak bisa diubah) dan diteruskan ke proses berikutnya.
- **Mekanisme Pengecekan (Contoh Nama di DTTOT List: ALEX JORDAN BROWN):**
    - **Scenario-1:** Debitur "ALEX JORDAN BROWN" (Masuk ke daftar untuk dicek oleh RC satu-satu, karena mengandung salah satu dari ALEX, JORDAN, BROWN).
    - **Scenario-2:** Debitur "ALEX SANDRA" (Masuk ke daftar untuk dicek oleh RC satu-satu, karena mengandung satu-satu dari kata ALEX, JORDAN, BROWN).
    - **Scenario-3:** Debitur "JOHN MCALISSON" (OK: Langsung maju ke proses berikutnya karena tidak mengandung kata ALEX, JORDAN dan BROWN).
- Apabila masuk Scenario-1 dan Scenario-2, maka system mengirim notifikasi ke ACO untuk melakukan pengecekan terhadap debitur yang bersangkutan di system DTTOT Internal.
- ACO melakukan cek terhadap debitur tsb di system DTTOT internal, dan hasilnya di simpan di database.
- CMO merefresh tampilkan di Mobile Marketing untuk mendapatkan hasil pengecekan.
- Apabila hasilnya ‘terindikasi’ maka aplikasi tidak bisa jalan ke Langkah berikutnya di Mobile Marketing.

### D. Pengecekan DTTOT untuk Debitur Existing

- Setiap tanggal XX setiap bulannya, system internal DTTOT secara otomatis menarik data debitur dari Reksaloan, dan melakukan pengecekan DTTOT terhadap database DTTOT Internal.

### E. Update Database DTTOT Internal

- Setiap tanggal XX-1 sistem mengirimkan notifikasi ke ACO untuk mengecek apakah ada nama baru yang perlu dimasukan di DTTOT Internal.
- Jika ada nama baru, maka ACO menginput nama tersebut di web system DTTOT Internal.

## 6. PANDUAN TEKNIS

- **Frontend:** PHP/Java Script (React.js Vue.js)
- **Database Utama:** PostgreSQL /MySQL (Versi terkini)
- **Autentikasi:** Login Captcha/Puzzle
- **Email Service:** SMTP Internal Perusahaan (Untuk notifikasi dan alert).

## 7. MANAJEMEN PENGGUNA

- **Level 1 - Staf:** Input sistem, View
- **Level 2 - Supervisor:** Review, Approval
- **Level 3 - Manager:** Review, Approval
- **Level 4 - Super Admin:** Akses penuh: kelola user, konfigurasi sistem, semua operasi data
