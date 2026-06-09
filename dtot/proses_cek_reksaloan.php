<?php
require_once 'config/database.php';
require_once 'config/mailer.php';
require_once 'config/sqlserver.php';
include 'layout/header.php';

$no_kontrak = $_GET['kontrak'] ?? null;
if (!$no_kontrak) {
    echo "<script>alert('Nomor Kontrak tidak ditemukan!'); window.location.href='reksaloan.php';</script>";
    exit;
}

// Fetch data from SQL Server
$debitur = null;
if ($pdo_sqlsrv) {
    $tsql = "SELECT b.Name as nama, c.IDNumber as ktp, a.AgreementNo as no_kontrak, tj.Description as pekerjaan
             FROM Agreement a 
             INNER JOIN Customer b ON a.CustomerID = b.CustomerID
             INNER JOIN PersonalCustomer c ON b.CustomerID = c.CustomerID
             INNER JOIN TblJobList tj ON c.JobList = tj.id
             WHERE a.AgreementNo = ?";
    $stmt = $pdo_sqlsrv->prepare($tsql);
    $stmt->execute([$no_kontrak]);
    $debitur = $stmt->fetch(PDO::FETCH_ASSOC);
}

if (!$debitur) {
    echo "<script>alert('Data debitur tidak ditemukan di SQL Server!'); window.location.href='reksaloan.php';</script>";
    exit;
}

$search_name = $debitur['nama'];
$search_nik = $debitur['ktp'];

// Fetch existing check from MySQL
$stmtExist = $pdo->prepare("SELECT * FROM cekreksaloan WHERE no_kontrak = ?");
$stmtExist->execute([$no_kontrak]);
$existing_check = $stmtExist->fetch();

// Handle Save Result
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_result'])) {
    $result_dtot = $_POST['hasil_dtot'];
    $result_pep = $_POST['hasil_pep'];
    $keterangan = $_POST['keterangan'];
    $user_id = $_SESSION['user_id'];
    $bukti_ss = $existing_check['bukti_ss'] ?? null;

    // Handle File Upload
    if (isset($_FILES['bukti_ss']) && $_FILES['bukti_ss']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['bukti_ss']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if (in_array($ext, $allowed)) {
            $new_filename = 'rk_' . uniqid('bukti_', true) . '.' . $ext;
            $upload_path = 'uploads/' . $new_filename;
            
            if (move_uploaded_file($_FILES['bukti_ss']['tmp_name'], $upload_path)) {
                if ($bukti_ss && file_exists('uploads/' . $bukti_ss)) {
                    unlink('uploads/' . $bukti_ss);
                }
                $bukti_ss = $new_filename;
            }
        }
    }

    $sql = "INSERT INTO cekreksaloan (no_kontrak, nama_debitur, nik, hasil_dtot, hasil_pep, keterangan, bukti_ss, checked_by, checked_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ON DUPLICATE KEY UPDATE 
            hasil_dtot = VALUES(hasil_dtot), 
            hasil_pep = VALUES(hasil_pep), 
            keterangan = VALUES(keterangan), 
            bukti_ss = VALUES(bukti_ss), 
            checked_by = VALUES(checked_by), 
            checked_at = NOW()";
            
    $stmtSave = $pdo->prepare($sql);
    if ($stmtSave->execute([$no_kontrak, $search_name, $search_nik, $result_dtot, $result_pep, $keterangan, $bukti_ss, $user_id])) {
        // --- SEND EMAIL ALERT IF TERINDIKASI ---
        $msg_email = sendAlertEmail($search_name, $search_nik, $result_dtot, $result_pep, 'Cek Data Reksaloan', $no_kontrak);

        echo "<script>alert('Hasil pengecekan berhasil disimpan" . addslashes($msg_email) . "!'); window.location.href='reksaloan.php';</script>";
        exit;
    }
}

// Perform Search against DTTOT (local)
$searchTerm = "%$search_name%";
$stmtDtot = $pdo->prepare("SELECT * FROM terduga WHERE deleted_at IS NULL AND (nama LIKE ? OR deskripsi LIKE ?)");
$stmtDtot->execute([$searchTerm, $searchTerm]);
$dtot_matches = $stmtDtot->fetchAll();

?>

<div class="dashboard-header" style="margin-bottom: 2rem;">
    <h2 style="font-weight: 700; color: var(--primary-color);">Proses Cek Reksaloan</h2>
    <p style="color: var(--text-secondary); font-size: 0.9rem;">Pengecekan manual untuk data dari Reksaloan.</p>
</div>

<div class="row" style="display: flex; gap: 2rem; flex-wrap: wrap;">
    <div class="col-md-5" style="flex: 1; min-width: 350px;">
        <div class="card" style="margin-bottom: 2rem;">
            <h4 style="margin-bottom: 1.5rem; color: var(--primary-color); border-bottom: 1px solid var(--border-color); padding-bottom: 10px;">Data Debitur</h4>
            <div style="margin-bottom: 1rem;">
                <label style="display: block; font-weight: 600; color: var(--text-secondary); font-size: 0.8rem;">NAMA</label>
                <div style="font-size: 1.1rem; font-weight: 700; color: var(--text-primary);"><?php echo htmlspecialchars($search_name); ?></div>
            </div>
            <div style="margin-bottom: 1rem;">
                <label style="display: block; font-weight: 600; color: var(--text-secondary); font-size: 0.8rem;">NIK</label>
                <div style="font-size: 1.1rem; font-weight: 700; color: var(--text-primary);"><?php echo htmlspecialchars($search_nik); ?></div>
            </div>
            <div style="margin-bottom: 1rem;">
                <label style="display: block; font-weight: 600; color: var(--text-secondary); font-size: 0.8rem;">KONTRAK</label>
                <div style="color: var(--text-primary); font-weight: 600;"><?php echo htmlspecialchars($no_kontrak); ?></div>
            </div>

            <form method="POST" enctype="multipart/form-data" style="margin-top: 2rem;">
                <div style="margin-bottom: 1rem;">
                    <label style="display: block; margin-bottom: 5px; font-weight: 600; color: var(--text-secondary);">Hasil DTTOT</label>
                    <select name="hasil_dtot" class="form-control" required style="width: 100%; height: 42px; border-radius: 5px; border: 1px solid #d1d3e2;">
                        <option value="">Pilih</option>
                        <option value="Tidak Terindikasi" <?php echo ($existing_check['hasil_dtot'] ?? '') == 'Tidak Terindikasi' ? 'selected' : ''; ?>>Tidak Terindikasi</option>
                        <option value="Terindikasi" <?php echo ($existing_check['hasil_dtot'] ?? '') == 'Terindikasi' ? 'selected' : ''; ?>>Terindikasi</option>
                    </select>
                </div>
                <div style="margin-bottom: 1rem;">
                    <label style="display: block; margin-bottom: 5px; font-weight: 600; color: var(--text-secondary);">Hasil PEP</label>
                    <select name="hasil_pep" class="form-control" required style="width: 100%; height: 42px; border-radius: 5px; border: 1px solid #d1d3e2;">
                        <option value="">Pilih</option>
                        <option value="Tidak Terindikasi" <?php echo ($existing_check['hasil_pep'] ?? '') == 'Tidak Terindikasi' ? 'selected' : ''; ?>>Tidak Terindikasi</option>
                        <option value="Terindikasi" <?php echo ($existing_check['hasil_pep'] ?? '') == 'Terindikasi' ? 'selected' : ''; ?>>Terindikasi</option>
                    </select>
                </div>

                <div style="margin-bottom: 1rem;">
                    <label style="display: block; margin-bottom: 5px; font-weight: 600; color: var(--text-secondary);">Keterangan</label>
                    <textarea name="keterangan" class="form-control" rows="3" style="width: 100%; border-radius: 5px; border: 1px solid #d1d3e2;"><?php echo htmlspecialchars($existing_check['keterangan'] ?? ''); ?></textarea>
                </div>
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; margin-bottom: 5px; font-weight: 600; color: var(--text-secondary);">Bukti (Screenshot)</label>
                    <?php if (!empty($existing_check['bukti_ss'])): ?>
                        <div style="margin-bottom: 10px;">
                            <img src="uploads/<?php echo htmlspecialchars($existing_check['bukti_ss']); ?>" style="max-width: 100%; border-radius: 5px; border: 1px solid #ddd;">
                        </div>
                    <?php endif; ?>
                    <input type="file" name="bukti_ss" class="form-control" accept="image/*" style="width: 100%; padding: 8px;">
                </div>
                <button type="submit" name="save_result" class="btn-upload" style="width: 100%; height: 45px; background: var(--primary-color); color: #fff; font-weight: 700; border-radius: 8px; border: none; cursor: pointer;">
                    <i class="fas fa-save" style="margin-right: 8px;"></i> Simpan Hasil Cek
                </button>
            </form>
        </div>
    </div>

    <div class="col-md-7" style="flex: 1.5; min-width: 450px;">
        <div class="card pep-link-card" style="margin-bottom: 2rem;">
            <a href="https://pep.ppatk.go.id/admin/user/login" target="_blank" class="pep-link-icon" title="Cek PEP (Official Website)">
                <img src="assets/images/iconpep.png" alt="PEP">
                <span class="pep-link-text">CEK PEP</span>
            </a>
            <h4 style="margin-bottom: 1rem; color: var(--text-secondary);">Cocok di Database DTTOT</h4>
            <div class="data-table-container">
                <table class="data-table">
                    <thead>
                        <tr><th>Nama</th><th>Tipe</th><th>Keterangan</th></tr>
                    </thead>
                    <tbody>
                        <?php if (empty($dtot_matches)): ?>
                            <tr><td colspan="3" style="text-align: center; padding: 1.5rem;">Tidak ada kecocokan DTTOT.</td></tr>
                        <?php else: ?>
                            <?php foreach ($dtot_matches as $m): ?>
                                <tr style="background: rgba(231, 74, 59, 0.05);">
                                    <td style="font-weight: 700; color: #e74a3b;"><?php echo htmlspecialchars($m['nama']); ?></td>
                                    <td><?php echo htmlspecialchars($m['terduga_type']); ?></td>
                                    <td style="font-size: 0.8rem;"><?php echo htmlspecialchars($m['deskripsi']); ?></td>
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
