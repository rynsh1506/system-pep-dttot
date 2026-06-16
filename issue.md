# Issue: Redesign Search Page & Table UI

## Latar Belakang

Setelah halaman dashboard diperbarui, halaman Search Data (`resources/views/livewire/search-data.blade.php`) terlihat masih sangat generik (bawaan DaisyUI).
Masalah utama yang dilaporkan pengguna:
1. **Form pencarian dan tombol** masih terlihat polos/standar.
2. **Icon Edit** terlihat jelek dan kurang jelas (kurang premium).
3. **Tabel dan Pagination** kurang memiliki garis pembatas yang tegas, sehingga baris data sulit dibedakan satu sama lain.

Issue ini bertujuan untuk memperbaiki tampilan halaman Search Data agar sama modernnya dengan komponen Dashboard, serta memperjelas tampilan tabel.

---

## File yang Harus Dikerjakan

| File | Jenis Perubahan |
|---|---|
| `resources/views/livewire/search-data.blade.php` | Redesign UI form, tabel, icon aksi, dan garis batas tabel |
| `resources/views/livewire/dashboard.blade.php` | Sinkronisasi icon aksi (View & Edit) agar seragam dengan Search Data |

---

## Panduan Implementasi Detail

### Tahap 1: Redesign Form Pencarian

**File:** `resources/views/livewire/search-data.blade.php`

- Form pencarian yang saat ini menggunakan label atas (`label-text`) perlu dibuat lebih compact.
- Ubah grid layout input pencarian. Tambahkan icon di dalam input (left icon) untuk memperjelas fungsinya (menggunakan flex-wrapper input seperti pada form Login).
- **Tombol Export Excel:** Ganti class button dari `btn-success` menjadi button outline yang lebih rapi dengan icon Excel hijau, atau button yang lebih premium dengan shadow.

Contoh struktur input dengan icon:
```html
<div class="relative w-full">
  <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-base-content/50">
    [Icon Search Heroicons]
  </div>
  <input type="text" class="input input-bordered w-full pl-10 bg-base-100/50 focus:bg-base-100 transition-colors" placeholder="Cari nama subjek...">
</div>
```

### Tahap 2: Perbaikan Garis Batas Tabel (Borders)

- **Tabel:** Hapus class `table-zebra` jika dirasa kurang jelas, dan ganti dengan styling garis batas baris yang tegas.
- Gunakan kombinasi `divide-y divide-base-200/60` pada `<tbody>` atau tambahkan border bottom pada setiap baris `border-b border-base-200/80` agar batas antar data sangat kentara.
- Tambahkan efek `hover:bg-base-200/40` pada baris agar pengguna tahu data mana yang sedang ditunjuk kursor.

### Tahap 3: Perbaikan Icon Aksi (View & Edit)

Icon bawaan saat ini (pensil dan mata) masih terlalu standar.
- Ganti dengan icon SVG Heroicons berukuran lebih ideal (`w-4 h-4` atau `w-5 h-5`).
- Beri background pill / square yang halus pada icon tersebut.
- **Icon Edit:** Ganti dengan icon *Pencil Square* atau *Pencil* dari Heroicons yang lebih tebal.
- Terapkan perbaikan icon ini tidak hanya di `search-data.blade.php`, tapi juga di `dashboard.blade.php` pada kolom aksi agar konsisten.

Contoh Icon Edit modern:
```html
<a href="..." class="p-1.5 rounded-lg text-base-content/50 hover:text-info hover:bg-info/10 transition-colors" title="Edit">
  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">
    <path d="M2.695 14.763l-1.262 3.154a.5.5 0 00.65.65l3.155-1.262a4 4 0 001.343-.885L17.5 5.5a2.121 2.121 0 00-3-3L3.58 13.42a4 4 0 00-.885 1.343z" />
  </svg>
</a>
```

### Tahap 4: Kustomisasi Pagination

- Jika `$data->links()` saat ini menggunakan tampilan bawaan Tailwind yang membosankan atau ukurannya terlalu besar, pastikan komponen pagination dibungkus dalam container dengan padding yang tepat.
- Tambahkan garis pembatas atas (border-top) di atas pagination untuk memisahkannya secara tegas dari baris tabel terakhir.

---

## Verifikasi & Testing

1. **Test Visual:**
   - [ ] Form pencarian terlihat padat, modern, dan tidak memakan terlalu banyak ruang vertikal.
   - [ ] Ada border / garis horizontal yang tegas memisahkan setiap baris data di tabel.
   - [ ] Saat di-hover, baris tabel memberikan feedback visual yang jelas.
   - [ ] Icon Aksi (Detail dan Edit) terlihat lebih premium (tidak sekadar icon SVG polosan, melainkan ada padding / hover background).
2. **Kesesuaian:** Pastikan kolom Aksi di Dashboard (`dashboard.blade.php`) dan di Search Data (`search-data.blade.php`) sama persis.
3. **Teknis:** Dikerjakan di branch baru dan dibuatkan PR sebelum di-merge.
