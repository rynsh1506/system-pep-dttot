<?php
require_once 'config/db_dtot.php';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM terduga WHERE id = ?");
$stmt->execute([$id]);
$data = $stmt->fetch();

if (!$data) {
    die("Data tidak ditemukan.");
}

include 'layout/header.php';
?>

<div class="dashboard-header" style="margin-bottom: 2rem;">
    <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 0.5rem;">
        <a href="detail.php?id=<?php echo $id; ?>" style="color: var(--text-secondary); text-decoration: none;"><i
                class="fas fa-arrow-left"></i> Kembali</a>
        <h2 style="font-weight: 700; color: var(--primary-color);">Edit Data Terduga</h2>
    </div>
    <p style="color: var(--text-secondary); font-size: 0.9rem;">Ubah informasi subjek DTTOT.</p>
</div>

<div class="upload-container" style="padding: 2.5rem;">
    <form action="update_data.php" method="POST">
        <input type="hidden" name="id" value="<?php echo $data['id']; ?>">

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
            <div class="form-group">
                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: var(--text-primary);">Nama
                    Lengkap / Korporasi</label>
                <input type="text" name="nama" value="<?php echo htmlspecialchars($data['nama']); ?>" required
                    style="width: 100%; padding: 0.8rem; border: 1px solid var(--border-color); border-radius: 8px;">
            </div>
            <div class="form-group">
                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: var(--text-primary);">Tipe
                    Terduga</label>
                <select name="terduga_type" required
                    style="width: 100%; padding: 0.8rem; border: 1px solid var(--border-color); border-radius: 8px;">
                    <option value="Orang" <?php echo $data['terduga_type'] == 'Orang' ? 'selected' : ''; ?>>Orang
                    </option>
                    <option value="Korporasi" <?php echo $data['terduga_type'] == 'Korporasi' ? 'selected' : ''; ?>>
                        Korporasi</option>
                </select>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
            <div class="form-group">
                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: var(--text-primary);">Kode
                    Densus / Khusus</label>
                <input type="text" name="kode_densus" value="<?php echo htmlspecialchars($data['kode_densus']); ?>"
                    style="width: 100%; padding: 0.8rem; border: 1px solid var(--border-color); border-radius: 8px;">
            </div>
            <div class="form-group">
                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: var(--text-primary);">WN /
                    Asal Negara</label>
                <input type="text" name="wn_asal_negara"
                    value="<?php echo htmlspecialchars($data['wn_asal_negara']); ?>" required
                    style="width: 100%; padding: 0.8rem; border: 1px solid var(--border-color); border-radius: 8px;">
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
            <div class="form-group">
                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: var(--text-primary);">Tempat
                    Lahir</label>
                <input type="text" name="tempat_lahir" value="<?php echo htmlspecialchars($data['tempat_lahir']); ?>"
                    style="width: 100%; padding: 0.8rem; border: 1px solid var(--border-color); border-radius: 8px;">
            </div>
            <div class="form-group">
                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: var(--text-primary);">Tanggal
                    Lahir (YYYY-MM-DD)</label>
                <input type="date" name="tanggal_lahir" value="<?php echo $data['tanggal_lahir']; ?>"
                    style="width: 100%; padding: 0.8rem; border: 1px solid var(--border-color); border-radius: 8px;">
            </div>
        </div>

        <div class="form-group" style="margin-bottom: 1.5rem;">
            <label style="display: block; margin-bottom: 8px; font-weight: 600; color: var(--text-primary);">Deskripsi /
                Keterangan</label>
            <textarea name="deskripsi" rows="4" required
                style="width: 100%; padding: 0.8rem; border: 1px solid var(--border-color); border-radius: 8px;"><?php echo htmlspecialchars($data['deskripsi']); ?></textarea>
        </div>

        <div class="form-group" style="margin-bottom: 2rem;">
            <label
                style="display: block; margin-bottom: 8px; font-weight: 600; color: var(--text-primary);">Alamat</label>
            <textarea name="alamat" rows="3"
                style="width: 100%; padding: 0.8rem; border: 1px solid var(--border-color); border-radius: 8px;"><?php echo htmlspecialchars($data['alamat']); ?></textarea>
        </div>

        <div style="text-align: right; border-top: 1px solid var(--border-color); padding-top: 1.5rem;">
            <a href="detail.php?id=<?php echo $id; ?>"
                style="text-decoration: none; color: var(--text-secondary); margin-right: 2rem;">Batal</a>
            <button type="submit" class="btn-upload" style="margin-top: 0;">
                <i class="fas fa-save" style="margin-right: 8px;"></i> Simpan Perubahan
            </button>
        </div>
    </form>
</div>

<?php include 'layout/footer.php'; ?>