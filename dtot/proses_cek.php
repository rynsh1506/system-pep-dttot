<?php
require_once 'config/database.php';
require_once 'config/mailer.php';
include 'layout/header.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    echo "<script>alert('ID Pengajuan tidak ditemukan!'); window.location.href='pengajuan_cek.php';</script>";
    exit;
}

// Fetch identifying data from pengajuan_dtot
$stmt = $pdo->prepare("SELECT * FROM pengajuan_dtot WHERE id = ?");
$stmt->execute([$id]);
$pengajuan = $stmt->fetch();

if (!$pengajuan) {
    echo "<script>alert('Data pengajuan tidak ditemukan!'); window.location.href='pengajuan_cek.php';</script>";
    exit;
}

$search_name = $pengajuan['nama_cadeb'];
$search_nik = $pengajuan['nik'];

// Handle Save Result
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_result'])) {
    $result = $_POST['hasil_pengecekan'];
    $resultpep = $_POST['hasil_pep'];
    $keterangan = $_POST['keterangan'];
    $user_id = $_SESSION['user_id'];
    $bukti_ss = $pengajuan['bukti_ss']; // Default to existing value

    // Handle File Upload
    if (isset($_FILES['bukti_ss']) && $_FILES['bukti_ss']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['bukti_ss']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if (in_array($ext, $allowed)) {
            $new_filename = uniqid('bukti_', true) . '.' . $ext;
            $upload_path = 'uploads/' . $new_filename;
            
            if (move_uploaded_file($_FILES['bukti_ss']['tmp_name'], $upload_path)) {
                // Delete old file if exists
                if ($bukti_ss && file_exists('uploads/' . $bukti_ss)) {
                    unlink('uploads/' . $bukti_ss);
                }
                $bukti_ss = $new_filename;
            }
        }
    }

    $stmtUpdate = $pdo->prepare("UPDATE pengajuan_dtot SET hasil_pengecekan = ?, hasil_pep = ?, keterangan = ?, bukti_ss = ?, checked_by = ?, checked_at = NOW() WHERE id = ?");
    if ($stmtUpdate->execute([$result, $resultpep, $keterangan, $bukti_ss, $user_id, $id])) {

        // --- START SQL SERVER SUBMISSION ---
        require_once 'config/sqlserver.php';
        if (isset($pdo_sqlsrv) && $pdo_sqlsrv !== null) {
            try {
                // Insert hasil pengecekan ke tabel HasilPengecekan di SQL Server
                $is_proceed = 0; // Default 0
                $username = $_SESSION['username'] ?? 'unknown';

                /*
                $sql_sqlsrv = "UPDATE temp_mobile 
               SET hasildttot = ?, hasilpep = ?, tanggalcek = GETDATE()
               WHERE (isflag = 0 OR isflag IS NULL) AND IDKTP = ?";
                $stmt_sqlsrv = $pdo_sqlsrv->prepare($sql_sqlsrv);
                $stmt_sqlsrv->execute([
                    $result,
                    $resultpep,
                    $search_nik   // binds to IDKTP
                ]);
                */

                $sql_insert = "INSERT INTO HasilPengecekan 
                (id_pengecekan, Nama_Cadeb, NIK, HasilDtot, Keterangan, DiperiksaOleh, WaktuPeriksa, IsProceed, Hasilpep) 
                VALUES (?, ?, ?, ?, ?, ?, GETDATE(), ?, ?)";
                $stmt_insert = $pdo_sqlsrv->prepare($sql_insert);
                $stmt_insert->execute([
                    $id,
                    $search_name,
                    $search_nik,
                    $result,
                    $keterangan,
                    $username,
                    $is_proceed,
                    $resultpep
                ]);
            } catch (PDOException $e) {
                // Log error tapi jangan hentikan proses jika SQL Server gagal
                error_log("Gagal submit ke SQL Server: " . $e->getMessage());
            }
        }
        // --- END SQL SERVER SUBMISSION ---

        // --- SEND EMAIL ALERT IF TERINDIKASI ---
        $msg_email = sendAlertEmail($search_name, $search_nik, $result, $resultpep, 'Pengajuan Cek Debitur');

        echo "<script>alert('Hasil pengecekan berhasil disimpan" . addslashes($msg_email) . "!'); window.location.href='pengajuan_cek.php';</script>";
        exit;
    }
}

// Perform Search against 'terduga' table
// Based on image: Search by Name and Type (Orang/Korporasi)
// Note: We'll search by name primarily.
$searchTerm = "%$search_name%";
$stmtSearch = $pdo->prepare("SELECT * FROM terduga WHERE deleted_at IS NULL AND (nama LIKE ? OR deskripsi LIKE ?)");
$stmtSearch->execute([$searchTerm, $searchTerm]);
$results = $stmtSearch->fetchAll();
?>

<div class="dashboard-header" style="margin-bottom: 2rem;">
    <h2 style="font-weight: 700; color: var(--primary-color);">Proses Cek DTOT & PEP</h2>
    <p style="color: var(--text-secondary); font-size: 0.9rem;">Membandingkan data CADEB dengan database DTTOT.</p>
</div>

<div class="row">
    <div class="col-md-5">
        <div class="card" style="margin-bottom: 2rem;">
            <h4
                style="margin-bottom: 1.5rem; color: var(--primary-color); border-bottom: 1px solid var(--border-color); padding-bottom: 10px;">
                Data CADEB</h4>
            <div style="margin-bottom: 1rem;">
                <label style="display: block; font-weight: 600; color: var(--text-secondary); font-size: 0.8rem;">NAMA
                    CADEB</label>
                <div style="font-size: 1.1rem; font-weight: 700; color: var(--text-primary);">
                    <?php echo htmlspecialchars($search_name); ?>
                </div>
            </div>
            <div style="margin-bottom: 1rem;">
                <label
                    style="display: block; font-weight: 600; color: var(--text-secondary); font-size: 0.8rem;">NIK</label>
                <div style="font-size: 1.1rem; font-weight: 700; color: var(--text-primary);">
                    <?php echo htmlspecialchars($search_nik); ?>
                </div>
            </div>
            <div style="margin-bottom: 1.5rem;">
                <label
                    style="display: block; font-weight: 600; color: var(--text-secondary); font-size: 0.8rem;">TANGGAL
                    PENGAJUAN</label>
                <div style="color: var(--text-primary);">
                    <?php echo date('d/m/Y', strtotime($pengajuan['tanggal'])); ?>
                </div>
            </div>

            <form method="POST" enctype="multipart/form-data">
                <div style="margin-bottom: 1rem;">
                    <label
                        style="display: block; margin-bottom: 5px; font-weight: 600; color: var(--text-secondary);">Hasil
                        Pengecekan DTTOT</label>
                    <select name="hasil_pengecekan" class="form-control" required
                        style="width: 100%; height: 42px; padding: 0 12px; border: 1px solid #d1d3e2; border-radius: 5px;">
                        <option value="">Pilih</option>
                        <option value="Tidak Terindikasi" <?php echo $pengajuan['hasil_pengecekan'] == 'Tidak Terindikasi' ? 'selected' : ''; ?>>Tidak Terindikasi</option>
                        <option value="Terindikasi" <?php echo $pengajuan['hasil_pengecekan'] == 'Terindikasi' ? 'selected' : ''; ?>>Terindikasi</option>
                    </select>
                </div>
                <div style="margin-bottom: 1rem;">
                    <label
                        style="display: block; margin-bottom: 5px; font-weight: 600; color: var(--text-secondary);">Hasil
                        Pengecekan PEP</label>
                    <select name="hasil_pep" class="form-control" required
                        style="width: 100%; height: 42px; padding: 0 12px; border: 1px solid #d1d3e2; border-radius: 5px;">
                        <option value="">Pilih</option>
                        <option value="Tidak Terindikasi" <?php echo $pengajuan['hasil_pep'] == 'Tidak Terindikasi' ? 'selected' : ''; ?>>Tidak Terindikasi</option>
                        <option value="Terindikasi" <?php echo $pengajuan['hasil_pep'] == 'Terindikasi' ? 'selected' : ''; ?>>Terindikasi</option>
                    </select>
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <label
                        style="display: block; margin-bottom: 5px; font-weight: 600; color: var(--text-secondary);">Keterangan
                        Tambahan</label>
                    <textarea name="keterangan" class="form-control" rows="3"
                        style="width: 100%; border: 1px solid #d1d3e2; border-radius: 5px;"><?php echo htmlspecialchars($pengajuan['keterangan'] ?? ''); ?></textarea>
                </div>
                <div style="margin-bottom: 1.5rem;">
                    <label
                        style="display: block; margin-bottom: 5px; font-weight: 600; color: var(--text-secondary);">Bukti Screenshot (Gambar)</label>
                    <?php if (!empty($pengajuan['bukti_ss'])): ?>
                        <div style="margin-bottom: 10px;">
                            <img src="uploads/<?php echo htmlspecialchars($pengajuan['bukti_ss']); ?>" alt="Bukti SS" style="max-width: 100%; border-radius: 5px; border: 1px solid #ddd;">
                            <p style="font-size: 0.75rem; color: var(--text-secondary); margin-top: 5px;">Ganti gambar dengan memilih file baru di bawah.</p>
                        </div>
                    <?php endif; ?>
                    <input type="file" name="bukti_ss" class="form-control" accept="image/*"
                           style="width: 100%; padding: 8px; border: 1px solid #d1d3e2; border-radius: 5px;">
                </div>
                <button type="submit" name="save_result" class="btn-upload"
                    style="width: 100%; height: 45px; background: var(--primary-color); color: #fff; font-weight: 700; border-radius: 8px;">
                    <i class="fas fa-save" style="margin-right: 8px;"></i> Simpan & Selesai
                </button>
            </form>
        </div>
    </div>

    <div class="col-md-7">
        <div class="card pep-link-card">
            <a href="https://pep.ppatk.go.id/admin/user/login" target="_blank" class="pep-link-icon" title="Cek PEP (Official Website)">
                <img src="assets/images/iconpep.png" alt="PEP">
                <span class="pep-link-text">CEK PEP</span>
            </a>
            <h4 style="margin-bottom: 1rem; color: var(--text-secondary);">Hasil Pencarian di Database DTTOT</h4>
            <p style="font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 1.5rem;">
                Menampilkan data yang mirip dengan nama <strong>"
                    <?php echo htmlspecialchars($search_name); ?>"
                </strong>
            </p>

            <div class="data-table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Nama Terduga</th>
                            <th>Tipe</th>
                            <th>Keterangan/NIK di DB</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($results)): ?>
                            <tr>
                                <td colspan="3" style="text-align: center; padding: 2rem;">Tidak ada data yang cocok di
                                    database DTTOT.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($results as $item): ?>
                                <tr style="background-color: rgba(231, 74, 59, 0.05);">
                                    <td style="font-weight: 700; color: #e74a3b;">
                                        <?php echo htmlspecialchars($item['nama']); ?>
                                    </td>
                                    <td>
                                        <?php echo htmlspecialchars($item['terduga_type']); ?>
                                    </td>
                                    <td style="font-size: 0.85rem;">
                                        <?php echo htmlspecialchars($item['deskripsi']); ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    .row {
        display: flex;
        gap: 2rem;
        flex-wrap: wrap;
    }

    .col-md-5 {
        flex: 1;
        min-width: 350px;
    }

    .col-md-7 {
        flex: 1.5;
        min-width: 450px;
    }

    .pep-link-card {
        position: relative;
    }

    .pep-link-icon {
        position: absolute;
        top: 1.5rem;
        right: 1.5rem;
        background: #f8f9fc;
        padding: 6px 12px;
        border-radius: 6px;
        display: flex;
        align-items: center;
        gap: 10px;
        text-decoration: none !important;
        border: 1px solid #e3e6f0;
        transition: all 0.2s ease-in-out;
        z-index: 5;
    }

    .pep-link-icon:hover {
        background: #fff;
        border-color: var(--accent-color);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        transform: translateY(-2px);
    }

    .pep-link-icon img {
        height: 20px;
        width: auto;
        border-radius: 2px;
    }

    .pep-link-text {
        font-size: 0.7rem;
        font-weight: 700;
        color: #4e73df;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
</style>

<?php include 'layout/footer.php'; ?>