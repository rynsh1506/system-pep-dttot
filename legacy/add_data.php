<?php
require_once 'config/db_dtot.php';

include 'layout/header.php';
?>

<div class="dashboard-header" style="margin-bottom: 2rem;">
    <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 0.5rem;">
        <a href="index.php" style="color: var(--text-secondary); text-decoration: none;"><i
                class="fas fa-arrow-left"></i> Kembali</a>
        <h2 style="font-weight: 700; color: var(--primary-color);">Tambah Data Terduga</h2>
    </div>
    <p style="color: var(--text-secondary); font-size: 0.9rem;">Input data terduga teroris baru secara manual ke dalam
        sistem.</p>
</div>

<div class="upload-container" style="padding: 2.5rem;">
    <form action="save_single.php" method="POST">

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
            <div class="form-group">
                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: var(--text-primary);">Nama
                    Lengkap / Korporasi</label>
                <input type="text" name="nama" placeholder="Masukkan nama lengkap..." required
                    style="width: 100%; padding: 0.8rem; border: 1px solid var(--border-color); border-radius: 8px;">
            </div>
            <div class="form-group">
                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: var(--text-primary);">Tipe
                    Terduga</label>
                <select name="terduga_type" required
                    style="width: 100%; padding: 0.8rem; border: 1px solid var(--border-color); border-radius: 8px;">
                    <option value="Orang">Orang</option>
                    <option value="Korporasi">Korporasi</option>
                    <option value="Tidak Terduga">Tidak Terduga</option>
                </select>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
            <div class="form-group">
                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: var(--text-primary);">Kode
                    Densus / Khusus</label>
                <input type="text" name="kode_densus" placeholder="Contoh: IDD-XXX"
                    style="width: 100%; padding: 0.8rem; border: 1px solid var(--border-color); border-radius: 8px;">
            </div>
            <div class="form-group">
                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: var(--text-primary);">WN /
                    Asal Negara</label>
                <input type="text" name="wn_asal_negara" placeholder="Contoh: INDONESIA" required
                    style="width: 100%; padding: 0.8rem; border: 1px solid var(--border-color); border-radius: 8px;">
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
            <div class="form-group">
                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: var(--text-primary);">Tempat
                    Lahir</label>
                <input type="text" name="tempat_lahir" placeholder="Masukkan tempat lahir..."
                    style="width: 100%; padding: 0.8rem; border: 1px solid var(--border-color); border-radius: 8px;">
            </div>
            <div class="form-group">
                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: var(--text-primary);">Tanggal
                    Lahir</label>
                <input type="date" name="tanggal_lahir"
                    style="width: 100%; padding: 0.8rem; border: 1px solid var(--border-color); border-radius: 8px;">
            </div>
        </div>

        <div class="form-group" style="margin-bottom: 1.5rem;">
            <label style="display: block; margin-bottom: 8px; font-weight: 600; color: var(--text-primary);">Deskripsi /
                Keterangan</label>
            <textarea name="deskripsi" rows="4" placeholder="Keterangan tambahan mengenai subjek..." required
                style="width: 100%; padding: 0.8rem; border: 1px solid var(--border-color); border-radius: 8px;"></textarea>
        </div>

        <div class="form-group" style="margin-bottom: 2rem;">
            <label
                style="display: block; margin-bottom: 8px; font-weight: 600; color: var(--text-primary);">Alamat</label>
            <textarea name="alamat" rows="3" placeholder="Alamat terakhir yang diketahui..."
                style="width: 100%; padding: 0.8rem; border: 1px solid var(--border-color); border-radius: 8px;"></textarea>
        </div>

        <div style="text-align: right; border-top: 1px solid var(--border-color); padding-top: 1.5rem;">
            <a href="index.php"
                style="text-decoration: none; color: var(--text-secondary); margin-right: 2rem;">Batal</a>
            <button type="submit" class="btn-upload" style="margin-top: 0;">
                <i class="fas fa-plus" style="margin-right: 8px;"></i> Simpan Data Baru
            </button>
        </div>
    </form>
</div>

<?php include 'layout/footer.php'; ?>