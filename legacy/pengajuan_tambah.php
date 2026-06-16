<?php
require_once 'config/db_dtot.php';
require_once 'config/sqlserver.php';
require_once 'config/mailer.php';

include 'layout/header.php';

$success = false;
$isVerify = false;
$results_dttot = [];
$input_data = [];

// PHASE 1: Handle Initial Check Request
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['cek_dulu'])) {
    $isVerify = true;
    $input_data = [
        'kategori' => $_POST['kategori'],
        'nama_cadeb' => trim($_POST['nama_cadeb']),
        'nik' => trim($_POST['nik'])
    ];

    // Perform Similarity Search in DTTOT
    $searchTerm = "%" . $input_data['nama_cadeb'] . "%";
    $stmtSearch = $pdo->prepare("SELECT * FROM terduga WHERE deleted_at IS NULL AND (nama LIKE ? OR deskripsi LIKE ?)");
    $stmtSearch->execute([$searchTerm, $searchTerm]);
    $results_dttot = $stmtSearch->fetchAll();
}

// PHASE 3: Handle Final Submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_hasil'])) {
    $tanggal = date('Y-m-d');
    $nama_cadeb = trim($_POST['nama_cadeb']);
    $nik = trim($_POST['nik']);
    $kategori = $_POST['kategori'];
    $hasil_dtot = $_POST['hasil_pengecekan'];
    $hasil_pep = $_POST['hasil_pep'];
    $keterangan = trim($_POST['keterangan'] ?? '');
    $user_id = $_SESSION['user_id'];
    $bukti_ss = null;

    // Handle File Upload
    if (isset($_FILES['bukti_ss']) && $_FILES['bukti_ss']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['bukti_ss']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if (in_array($ext, $allowed)) {
            $new_filename = uniqid('bukti_hrd_', true) . '.' . $ext;
            $upload_path = 'uploads/' . $new_filename;
            if (move_uploaded_file($_FILES['bukti_ss']['tmp_name'], $upload_path)) {
                $bukti_ss = $new_filename;
            }
        }
    }

    // Simpan ke MySQL (pengajuan_dtot)
    $new_id = null;
    try {
        $stmtInsert = $pdo->prepare("INSERT INTO pengajuan_dtot 
            (tanggal, nama_cadeb, nik, kategori, hasil_pengecekan, hasil_pep, keterangan, bukti_ss, checked_by, checked_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
        echo "<pre>";
        print_r([
            $tanggal,
            $nama_cadeb,
            $nik,
            $kategori,
            $hasil_dtot,
            $hasil_pep,
            $keterangan,
            $bukti_ss,
            $user_id
        ]);
        echo "</pre>";
        $stmtInsert->execute([
            $tanggal,
            $nama_cadeb,
            $nik,
            $kategori,
            $hasil_dtot,
            $hasil_pep,
            $keterangan,
            $bukti_ss,
            $user_id
        ]);
        $new_id = $pdo->lastInsertId();
        $success = true;
    } catch (PDOException $e) {
        die("SQL Error: " . $e->getMessage());
    }

    if ($new_id) {
        sendAlertEmail($nama_cadeb, $nik, $hasil_dtot, $hasil_pep, "Pengajuan Cek HRD ($kategori)");
    }

    if ($success) {
        echo "<script>alert('Pengecekan HRD berhasil disimpan!'); window.location.href='pengajuan_tambah.php';</script>";
        exit;
    }
}

// Fetch History
$history = $pdo->query("SELECT p.*, u.full_name as checker_name FROM pengajuan_dtot p LEFT JOIN users u ON p.checked_by = u.id WHERE p.kategori IN ('Karyawan', 'Vendor') ORDER BY p.created_at DESC LIMIT 20")->fetchAll();
?>

<style>
    /* Premium UI Styles for HRD Input */
    .step-container {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 15px;
        margin-bottom: 2.5rem;
        background: #fff;
        padding: 1.2rem;
        border-radius: 12px;
        box-shadow: var(--shadow-soft);
        border: 1px solid var(--border-color);
    }

    .step {
        display: flex;
        align-items: center;
        gap: 8px;
        font-weight: 600;
        color: var(--text-secondary);
        transition: all 0.3s;
        padding: 6px 15px;
        border-radius: 6px;
    }

    .step.active {
        color: var(--accent-color);
        background: rgba(78, 115, 223, 0.08);
        font-weight: 700;
    }

    .step-number {
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        border: 1px solid;
        font-size: 0.75rem;
    }

    .step.active .step-number {
        border-color: var(--accent-color);
    }

    .step-divider {
        font-size: 1.1rem;
        color: #d1d3e2;
    }

    .custom-card {
        background: #fff;
        border-radius: 15px;
        box-shadow: var(--shadow-md);
        border: none;
        overflow: hidden;
        height: 100%;
        transition: all 0.3s ease;
    }

    .card-header {
        padding: 1.25rem 1.5rem;
        background: #fff;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    }

    .card-title {
        margin: 0;
        font-size: 1rem;
        font-weight: 700;
        color: var(--primary-color);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .card-body {
        padding: 1.5rem;
    }

    .data-summary-box {
        background: linear-gradient(135deg, #f8f9fc 0%, #edf1f7 100%);
        border-radius: 12px;
        padding: 1.2rem;
        margin-bottom: 1.5rem;
        border-left: 4px solid var(--accent-color);
        position: relative;
    }

    .data-label {
        font-size: 0.7rem;
        font-weight: 700;
        color: var(--text-secondary);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .data-value {
        font-size: 1.1rem;
        font-weight: 800;
        color: #2e3440;
        margin-top: 5px;
    }

    .data-kategori {
        position: absolute;
        top: 1.2rem;
        right: 1.2rem;
        font-size: 0.7rem;
        font-weight: 800;
        background: rgba(78, 115, 223, 0.1);
        color: var(--accent-color);
        padding: 4px 10px;
        border-radius: 20px;
    }

    .pep-btn {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        text-decoration: none !important;
        background: #fff;
        border: 1px solid #e3e6f0;
        padding: 8px 16px;
        border-radius: 10px;
        font-weight: 700;
        color: #4e73df;
        font-size: 0.75rem;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: var(--shadow-soft);
    }

    .pep-btn:hover {
        background: #4e73df;
        color: #fff;
        transform: translateY(-3px);
        box-shadow: 0 8px 15px rgba(78, 115, 223, 0.25);
        border-color: #4e73df;
    }

    .pep-btn i {
        font-size: 1rem;
    }

    .pep-btn img {
        height: 18px;
        border-radius: 3px;
    }

    .form-group-label {
        display: block;
        margin-bottom: 8px;
        font-weight: 700;
        color: #4a5568;
        font-size: 0.85rem;
    }

    .custom-input {
        width: 100%;
        height: 45px;
        padding: 0 15px;
        border: 1.5px solid #e2e8f0;
        border-radius: 10px;
        font-size: 0.95rem;
        transition: all 0.2s;
        background: #fff;
    }

    .custom-input:focus {
        border-color: var(--accent-color);
        box-shadow: 0 0 0 3px rgba(78, 115, 223, 0.1);
        outline: none;
    }

    .custom-textarea {
        width: 100%;
        padding: 12px 15px;
        border: 1.5px solid #e2e8f0;
        border-radius: 10px;
        font-size: 0.95rem;
        transition: all 0.2s;
        outline: none;
        resize: vertical;
    }

    .custom-textarea:focus {
        border-color: var(--accent-color);
        box-shadow: 0 0 0 3px rgba(78, 115, 223, 0.1);
    }

    .btn-primary-card {
        background: #4e73df;
        color: #fff;
        border: none;
        padding: 12px 24px;
        border-radius: 10px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.3s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        width: 100%;
        font-size: 0.95rem;
        box-shadow: 0 4px 12px rgba(78, 115, 223, 0.2);
    }

    .btn-primary-card:hover {
        background: #2e59d9;
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(78, 115, 223, 0.3);
    }

    .empty-state {
        text-align: center;
        padding: 3rem 1rem;
        color: var(--text-secondary);
        font-style: italic;
        font-size: 0.9rem;
    }

    /* Custom scrollbar for table */
    .table-responsive {
        max-height: 400px;
        overflow-y: auto;
        padding-right: 5px;
    }

    .table-responsive::-webkit-scrollbar {
        width: 6px;
    }

    .table-responsive::-webkit-scrollbar-thumb {
        background-color: #d1d3e2;
        border-radius: 10px;
    }
</style>

<div class="dashboard-header" style="margin-bottom: 2rem;">
    <h2 style="font-weight: 700; color: var(--primary-color);">Input Pengajuan Cek Karyawan / Vendor</h2>
</div>

<!-- STEP INDICATOR -->
<div class="step-container">
    <div class="step <?php echo !$isVerify ? 'active' : ''; ?>">
        <div class="step-number">1</div> Input Data
    </div>
    <div class="step-divider"><i class="fas fa-chevron-right"></i></div>
    <div class="step <?php echo $isVerify ? 'active' : ''; ?>">
        <div class="step-number">2</div> Verifikasi & Simpan
    </div>
</div>

<?php if (!$isVerify): ?>
    <!-- STEP 1: INITIAL INPUT FORM -->
    <div style="display: flex; justify-content: center; margin-bottom: 3.5rem;">
        <div style="flex: 0 0 550px;">
            <div class="custom-card">
                <div class="card-header">
                    <h5 class="card-title"><i class="fas fa-id-card"></i> Informasi Karyawan / Vendor</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="">
                        <div style="margin-bottom: 1.25rem;">
                            <label class="form-group-label">KATEGORI <span style="color: #e53e3e;">*</span></label>
                            <select name="kategori" class="custom-input" required>
                                <option value="">-- Pilih Kategori --</option>
                                <option value="Karyawan">Karyawan</option>
                                <option value="Vendor">Vendor</option>
                            </select>
                        </div>
                        <div style="margin-bottom: 1.25rem;">
                            <label class="form-group-label">NAMA LENGKAP <span style="color: #e53e3e;">*</span></label>
                            <input type="text" name="nama_cadeb" class="custom-input" placeholder="Masukkan nama..."
                                required>
                        </div>
                        <div style="margin-bottom: 2rem;">
                            <label class="form-group-label">NIK / IDENTITAS <span style="color: #e53e3e;">*</span></label>
                            <input type="text" name="nik" class="custom-input" placeholder="Masukkan NIK 16 digit..."
                                required>
                        </div>
                        <button type="submit" name="cek_dulu" class="btn-primary-card">
                            <i class="fas fa-search"></i> Cari & Cek Data Similar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php else: ?>
    <!-- STEP 2: REVIEW & VERIFY -->
    <div class="row" style="display: flex; gap: 1.5rem; align-items: stretch; margin-bottom: 3.5rem;">
        <!-- LEFT: Final Results Form -->
        <div style="flex: 1; min-width: 400px;">
            <div class="custom-card">
                <div class="card-header">
                    <h5 class="card-title"><i class="fas fa-check-circle"></i> Finalisasi Hasil Pengecekan</h5>
                </div>
                <div class="card-body">
                    <!-- High-quality data box -->
                    <div class="data-summary-box">
                        <div class="data-kategori"><?php echo htmlspecialchars($input_data['kategori']); ?></div>
                        <div class="data-label">Nama & NIK Terdaftar:</div>
                        <div class="data-value"><?php echo htmlspecialchars($input_data['nama_cadeb']); ?></div>
                        <div
                            style="font-size: 0.9rem; color: var(--text-secondary); margin-top: 4px; font-family: monospace; font-weight: 600;">
                            <?php echo htmlspecialchars($input_data['nik']); ?>
                        </div>
                    </div>

                    <form method="POST" action="" enctype="multipart/form-data">
                        <input type="hidden" name="nama_cadeb"
                            value="<?php echo htmlspecialchars($input_data['nama_cadeb']); ?>">
                        <input type="hidden" name="nik" value="<?php echo htmlspecialchars($input_data['nik']); ?>">
                        <input type="hidden" name="kategori"
                            value="<?php echo htmlspecialchars($input_data['kategori']); ?>">

                        <div style="margin-bottom: 1.2rem;">
                            <label class="form-group-label">Hasil Pengecekan DTTOT</label>
                            <select name="hasil_pengecekan" class="custom-input" required>
                                <option value="">-- Hasil Manual DTOT --</option>
                                <option value="Tidak Terindikasi">Tidak Terindikasi</option>
                                <option value="Terindikasi">Terindikasi</option>
                            </select>
                        </div>
                        <div style="margin-bottom: 1.2rem;">
                            <label class="form-group-label">Hasil Pengecekan PEP</label>
                            <select name="hasil_pep" class="custom-input" required>
                                <option value="">-- Hasil Manual PEP --</option>
                                <option value="Tidak Terindikasi">Tidak Terindikasi</option>
                                <option value="Terindikasi">Terindikasi</option>
                            </select>
                        </div>
                        <div style="margin-bottom: 1.2rem;">
                            <label class="form-group-label">Catatan Pemeriksaan</label>
                            <textarea name="keterangan" class="custom-textarea" rows="3"
                                placeholder="Tulis catatan jika diperlukan..."></textarea>
                        </div>
                        <div style="margin-bottom: 1.8rem;">
                            <label class="form-group-label">Upload Bukti Screenshot</label>
                            <input type="file" name="bukti_ss" class="custom-input" accept="image/*"
                                style="padding-top: 8px;">
                        </div>

                        <div style="display: flex; gap: 12px;">
                            <a href="pengajuan_tambah.php"
                                style="flex: 1; text-align: center; background: #edeff2; color: #4a5568; text-decoration: none; padding: 12px; border-radius: 10px; font-weight: 700; transition: 0.2s;">Kembali</a>
                            <button type="submit" name="submit_hasil" class="btn-primary-card" style="flex: 2;">
                                <i class="fas fa-save"></i> Simpan Hasil Pengecekan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- RIGHT: Similarity Table & PEP Action -->
        <div style="flex: 1.5; min-width: 500px;">
            <div class="custom-card">
                <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                    <h5 class="card-title"><i class="fas fa-database"></i> Database DTTOT Matches</h5>
                    <a href="https://pep.ppatk.go.id/admin/user/login" target="_blank" class="pep-btn">
                        <img src="assets/images/iconpep.png" alt=""> Buka Portal PEP Official
                    </a>
                </div>
                <div class="card-body">
                    <p style="font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 1.2rem;">
                        Pencarian data yang mirip dengan <strong
                            style="color: var(--primary-color);">"<?php echo htmlspecialchars($input_data['nama_cadeb']); ?>"</strong>.
                    </p>

                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th style="background: #f8f9fc;">NAMA LENGKAP</th>
                                    <th style="background: #f8f9fc;">TIPE</th>
                                    <th style="background: #f8f9fc;">DESKRIPSI / IDENTITAS</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($results_dttot)): ?>
                                    <tr>
                                        <td colspan="3">
                                            <div class="empty-state">Data tidak ditemukan di database DTTOT.</div>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($results_dttot as $item): ?>
                                        <tr style="background-color: rgba(231, 74, 59, 0.04);">
                                            <td style="font-weight: 700; color: #e74a3b;">
                                                <?php echo htmlspecialchars($item['nama']); ?>
                                            </td>
                                            <td><?php echo htmlspecialchars($item['terduga_type']); ?></td>
                                            <td style="font-size: 0.8rem; line-height: 1.4;">
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
    </div>
<?php endif; ?>

<!-- HISTORY TABLE SECTION -->
<div class="dashboard-header"
    style="margin-top: 1rem; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 10px;">
    <h3 style="font-weight: 700; color: var(--text-primary); font-size: 1.2rem;">Daftar Pengajuan Terbaru (HRD)</h3>
    <span
        style="background: #eaecf4; color: #4e73df; font-size: 0.75rem; font-weight: 800; padding: 3px 10px; border-radius: 20px;">20
        Data Terakhir</span>
</div>

<div class="data-table-container">
    <table class="data-table">
        <thead>
            <tr>
                <th>TANGGAL</th>
                <th>KATEGORI</th>
                <th>NAMA</th>
                <th>NIK / IDENTITAS</th>
                <th>DTTOT</th>
                <th>PEP</th>
                <th>PEMERIKSA</th>
                <th style="text-align: center;">BUKTI</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($history as $row): ?>
                <tr>
                    <td style="font-size: 0.8rem;"><?php echo date('d/m/Y', strtotime($row['tanggal'])); ?></td>
                    <td>
                        <span
                            style="font-size: 0.7rem; font-weight: 800; background: #f0f2f5; padding: 3px 8px; border-radius: 5px; color: #4e73df;">
                            <?php echo strtoupper(htmlspecialchars($row['kategori'])); ?>
                        </span>
                    </td>
                    <td style="font-weight: 700; color: var(--primary-color);">
                        <?php echo htmlspecialchars($row['nama_cadeb']); ?>
                    </td>
                    <td style="font-family: monospace; font-size: 0.85rem; color: var(--text-secondary);">
                        <?php echo htmlspecialchars($row['nik']); ?>
                    </td>
                    <td>
                        <span
                            class="status-badge <?php echo ($row['hasil_pengecekan'] == 'Terindikasi') ? 'status-rejected' : 'status-approved'; ?>">
                            <?php echo $row['hasil_pengecekan']; ?>
                        </span>
                    </td>
                    <td>
                        <span
                            class="status-badge <?php echo ($row['hasil_pep'] == 'Terindikasi') ? 'status-rejected' : 'status-approved'; ?>">
                            <?php echo $row['hasil_pep'] ?: 'N/A'; ?>
                        </span>
                    </td>
                    <td style="font-size: 0.85rem;"><?php echo htmlspecialchars($row['checker_name'] ?: '-'); ?></td>
                    <td style="text-align: center;">
                        <?php if ($row['bukti_ss']): ?>
                            <a href="uploads/<?php echo $row['bukti_ss']; ?>" target="_blank"
                                style="color: #4e73df; transition: 0.2s;">
                                <i class="fas fa-images"></i>
                            </a>
                        <?php else: ?>
                            <span style="color: #d1d3e2;">-</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include 'layout/footer.php'; ?>