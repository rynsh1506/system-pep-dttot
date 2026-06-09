<?php
require_once 'config/database.php';

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
        <a href="index.php" style="color: var(--text-secondary); text-decoration: none;"><i
                class="fas fa-arrow-left"></i> Kembali</a>
        <h2 style="font-weight: 700; color: var(--primary-color);">Detail Terduga</h2>
    </div>
    <p style="color: var(--text-secondary); font-size: 0.9rem;">Informasi lengkap mengenai subjek terpilih.</p>
</div>

<?php if (isset($_GET['status'])): ?>
    <?php if ($_GET['status'] === 'pending'): ?>
        <div
            style="background: rgba(246, 194, 62, 0.1); color: #f6c23e; padding: 1rem; border-radius: 10px; margin-bottom: 2rem; border: 1px solid rgba(246, 194, 62, 0.2);">
            <i class="fas fa-clock"></i> <strong>Menunggu Persetujuan:</strong> Perubahan data sedang diproses.
            <?php
            // Optional: Fetch exact status if needed, but generic pending msg is fine too. 
            // Or we could be specific:
            ?>
            (Status: Menunggu review berjenjang)
        </div>
    <?php elseif ($_GET['status'] === 'pending_delete'): ?>
        <div
            style="background: rgba(231, 74, 59, 0.1); color: #e74a3b; padding: 1rem; border-radius: 10px; margin-bottom: 2rem; border: 1px solid rgba(231, 74, 59, 0.2);">
            <i class="fas fa-trash-alt"></i> <strong>Permintaan Penghapusan:</strong> Subjek ini sedang dalam proses persetujuan
            penghapusan.
        </div>
    <?php endif; ?>
<?php endif; ?>

<div class="data-table-container" style="padding: 2rem;">
    <div
        style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 2rem; border-bottom: 1px solid var(--border-color); padding-bottom: 1rem;">
        <div>
            <h3 style="color: var(--primary-color); margin-bottom: 5px; font-size: 1.5rem;">
                <?php echo htmlspecialchars($data['nama']); ?>
                <?php if ($data['is_pending']): ?>
                    <span class="badge"
                        style="background: #f6c23e; color: #fff; font-size: 0.7rem; vertical-align: middle;">PENDING</span>
                <?php endif; ?>
            </h3>
            <span class="badge"
                style="background: rgba(78, 115, 223, 0.1); color: var(--accent-color); font-size: 0.9rem;">ID:
                <?php echo htmlspecialchars($data['kode_densus'] ?: '-'); ?>
            </span>
        </div>
        <div style="display: flex; gap: 10px;">
            <?php if ($_SESSION['role_level'] == 1 || $_SESSION['role_level'] == 4): ?>
                <a href="edit.php?id=<?php echo $data['id']; ?>" class="btn-upload"
                    style="text-decoration: none; display: inline-block; background: var(--secondary-color);">
                    <i class="fas fa-edit"></i> Edit Data
                </a>
                <a href="delete.php?id=<?php echo $data['id']; ?>" class="btn-upload"
                    style="text-decoration: none; display: inline-block; background: #e74a3b;"
                    onclick="return confirm('Apakah Anda yakin ingin mengajukan penghapusan subjek ini?')">
                    <i class="fas fa-trash"></i> Delete
                </a>
            <?php endif; ?>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem;">
        <div class="info-group">
            <h4
                style="color: var(--accent-color); margin-bottom: 1rem; border-left: 3px solid var(--accent-color); padding-left: 10px;">
                ID & Tipe</h4>
            <table style="width: 100%;">
                <tr>
                    <td style="color: var(--text-secondary); font-weight: 500; width: 140px; border: none;">Kategori
                    </td>
                    <td style="border: none;">:
                        <?php echo htmlspecialchars($data['terduga_type']); ?>
                    </td>
                </tr>
                <tr>
                    <td style="color: var(--text-secondary); font-weight: 500; border: none;">Kode Khusus</td>
                    <td style="border: none;">:
                        <?php echo htmlspecialchars($data['kode_densus'] ?: '-'); ?>
                    </td>
                </tr>
                <tr>
                    <td style="color: var(--text-secondary); font-weight: 500; border: none;">Dibuat Pada</td>
                    <td style="border: none;">:
                        <?php echo date('d M Y, H:i', strtotime($data['created_at'])); ?>
                    </td>
                </tr>
            </table>
        </div>

        <div class="info-group">
            <h4
                style="color: var(--accent-color); margin-bottom: 1rem; border-left: 3px solid var(--accent-color); padding-left: 10px;">
                Biodata</h4>
            <table style="width: 100%;">
                <tr>
                    <td style="color: var(--text-secondary); font-weight: 500; width: 140px; border: none;">Tempat Lahir
                    </td>
                    <td style="border: none;">:
                        <?php echo htmlspecialchars($data['tempat_lahir'] ?: '-'); ?>
                    </td>
                </tr>
                <tr>
                    <td style="color: var(--text-secondary); font-weight: 500; border: none;">Tanggal Lahir</td>
                    <td style="border: none;">:
                        <?php echo $data['tanggal_lahir'] ? date('d/m/Y', strtotime($data['tanggal_lahir'])) : '-'; ?>
                    </td>
                </tr>
                <tr>
                    <td style="color: var(--text-secondary); font-weight: 500; border: none;">Warga Negara</td>
                    <td style="border: none;">:
                        <?php echo htmlspecialchars($data['wn_asal_negara']); ?>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <div style="margin-top: 2rem;">
        <h4
            style="color: var(--accent-color); margin-bottom: 1rem; border-left: 3px solid var(--accent-color); padding-left: 10px;">
            Deskripsi & Riwayat</h4>
        <div
            style="background: #f8f9fc; padding: 1.5rem; border-radius: 12px; line-height: 1.6; color: var(--text-primary);">
            <?php echo nl2br(htmlspecialchars($data['deskripsi'])); ?>
        </div>
    </div>

    <div style="margin-top: 2rem;">
        <h4
            style="color: var(--accent-color); margin-bottom: 1rem; border-left: 3px solid var(--accent-color); padding-left: 10px;">
            Alamat Terakhir</h4>
        <div
            style="background: #f8f9fc; padding: 1.5rem; border-radius: 12px; line-height: 1.6; color: var(--text-primary);">
            <?php echo nl2br(htmlspecialchars($data['alamat'] ?: 'Tidak ada informasi alamat.')); ?>
        </div>
    </div>
</div>

<?php include 'layout/footer.php'; ?>