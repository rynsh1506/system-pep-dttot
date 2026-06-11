<?php
require_once 'config/database.php';
require_once 'config/sqlserver.php';
require_once 'config/mailer.php';

include 'layout/header.php';

$success = false;
$isVerify = false;
$results_dttot = [];
$input_data = [];
$is_ppatk_terindikasi = false;

// PHASE 1: Handle Initial Check Request
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['cek_dulu'])) {
    $isVerify = true;
    $input_data = [
        'kategori' => 'Calon Debitur',
        'nama_cadeb' => trim($_POST['nama_cadeb']),
        'nik' => trim($_POST['nik'])
    ];

    // Perform Similarity Search in DTTOT (Internal DB)
    $results_dttot = [];
    $nama = $input_data['nama_cadeb'];
    $nik = $input_data['nik'];
    
    if (!empty($nama) || !empty($nik)) {
        $query = "SELECT * FROM terduga WHERE deleted_at IS NULL AND (";
        $params = [];
        $conditions = [];
        
        if (!empty($nama)) {
            $conditions[] = "(nama LIKE ? OR deskripsi LIKE ?)";
            $params[] = "%" . $nama . "%";
            $params[] = "%" . $nama . "%";
        }
        
        if (!empty($nik)) {
            $conditions[] = "deskripsi LIKE ?";
            $params[] = "%" . $nik . "%";
        }
        
        $query .= implode(" OR ", $conditions) . ")";
        $stmtSearch = $pdo->prepare($query);
        $stmtSearch->execute($params);
        $results_dttot = $stmtSearch->fetchAll();
    }

}

// PHASE 3: Handle Final Submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_hasil'])) {
    $tanggal = date('Y-m-d');
    $nama_cadeb = trim($_POST['nama_cadeb']);
    $nik = trim($_POST['nik']);
    $kategori = 'Calon Debitur';
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
            $new_filename = uniqid('bukti_manual_', true) . '.' . $ext;
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

        // --- START SQL SERVER SUBMISSION ---
        // config/sqlserver.php already required at top
        if (isset($pdo_sqlsrv) && $pdo_sqlsrv !== null) {
            try {
                $is_proceed = 0; // Default 0
                $username = $_SESSION['username'] ?? 'unknown';

                $sql_insert = "INSERT INTO HasilPengecekan 
                (id_pengecekan, Nama_Cadeb, NIK, HasilDtot, Keterangan, DiperiksaOleh, WaktuPeriksa, IsProceed, Hasilpep) 
                VALUES (?, ?, ?, ?, ?, ?, GETDATE(), ?, ?)";
                $stmt_insert = $pdo_sqlsrv->prepare($sql_insert);
                $stmt_insert->execute([
                    $new_id,
                    $nama_cadeb,
                    $nik,
                    $hasil_dtot,
                    $keterangan,
                    $username,
                    $is_proceed,
                    $hasil_pep
                ]);
            } catch (PDOException $e) {
                // Log error but don't stop the process
                error_log("Gagal submit ke SQL Server (Manual): " . $e->getMessage());
            }
        }
        // --- END SQL SERVER SUBMISSION ---
    } catch (PDOException $e) {
        die("SQL Error: " . $e->getMessage());
    }

    if ($new_id) {
        sendAlertEmail($nama_cadeb, $nik, $hasil_dtot, $hasil_pep, "Manual Input ($kategori)");
    }

    if ($success) {
        echo "<script>alert('Pengecekan Berhasil disimpan!'); window.location.href='pengajuan_tambah_manual.php';</script>";
        exit;
    }
}

// Fetch History for non-HRD categories
$history = $pdo->query("SELECT p.*, u.full_name as checker_name FROM pengajuan_dtot p LEFT JOIN users u ON p.checked_by = u.id WHERE p.kategori = 'Calon Debitur' ORDER BY p.created_at DESC LIMIT 20")->fetchAll();
?>

<style>
    /* Premium UI Styles */
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
    <h2 style="font-weight: 700; color: var(--primary-color);">Input Pengajuan Cek Calon Debitur</h2>
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
                    <h5 class="card-title"><i class="fas fa-id-card"></i> Informasi Calon Debitur</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="">
                        <div style="margin-bottom: 1.25rem;">
                            <label class="form-group-label">KATEGORI</label>
                            <input type="text" class="custom-input" value="Calon Debitur" disabled style="background-color: #f8f9fc;">
                            <input type="hidden" name="kategori" value="Calon Debitur">
                        </div>
                        <div style="margin-bottom: 1.25rem;">
                            <label class="form-group-label">NIK / IDENTITAS <span style="color: #e53e3e;">*</span></label>
                            <input type="text" name="nik" class="custom-input" placeholder="Masukkan NIK 16 digit..."
                                required>
                        </div>
                        <div style="margin-bottom: 2rem;">
                            <label class="form-group-label">NAMA LENGKAP</label>
                            <input type="text" name="nama_cadeb" class="custom-input" placeholder="Masukkan nama (opsional)...">
                        </div>
                        <div style="display: flex; gap: 12px;">
                            <a href="pengajuan_cek.php" style="flex: 1; text-align: center; background: #edeff2; color: #4a5568; text-decoration: none; padding: 12px; border-radius: 10px; font-weight: 700; transition: 0.2s;">Batal</a>
                            <button type="submit" name="cek_dulu" class="btn-primary-card" style="flex: 2;">
                                <i class="fas fa-search"></i> Cari & Cek Data Similar
                            </button>
                        </div>
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
                    <!-- Alert has been removed -->

                    <div class="data-summary-box">
                        <div class="data-kategori"><?php echo htmlspecialchars($input_data['kategori']); ?></div>
                        <div class="data-label">Nama & NIK Terdaftar:</div>
                        <div class="data-value" id="display-nama-cadeb"><?php echo htmlspecialchars($input_data['nama_cadeb']); ?></div>
                        <div
                            style="font-size: 0.9rem; color: var(--text-secondary); margin-top: 4px; font-family: monospace; font-weight: 600;">
                            <?php echo htmlspecialchars($input_data['nik']); ?>
                        </div>
                    </div>

                    <form method="POST" action="" enctype="multipart/form-data">
                        <input type="hidden" name="nama_cadeb" id="input-nama-cadeb"
                            value="<?php echo htmlspecialchars($input_data['nama_cadeb']); ?>">
                        <input type="hidden" name="nik" value="<?php echo htmlspecialchars($input_data['nik']); ?>">
                        <input type="hidden" name="kategori"
                            value="<?php echo htmlspecialchars($input_data['kategori']); ?>">

                        <div style="margin-bottom: 1.2rem;">
                            <label class="form-group-label">Hasil Pengecekan DTTOT</label>
                            <?php $auto_dttot = (!empty($results_dttot)) ? 'Terindikasi' : 'Tidak Terindikasi'; ?>
                            <select name="hasil_pengecekan" class="custom-input" required>
                                <option value="">-- Hasil Manual DTOT --</option>
                                <option value="Tidak Terindikasi" <?php echo $auto_dttot == 'Tidak Terindikasi' ? 'selected' : ''; ?>>Tidak Terindikasi</option>
                                <option value="Terindikasi" <?php echo $auto_dttot == 'Terindikasi' ? 'selected' : ''; ?>>Terindikasi</option>
                            </select>
                        </div>
                        <div style="margin-bottom: 1.2rem;">
                            <label class="form-group-label">Hasil Pengecekan PEP</label>
                            <select name="hasil_pep" class="custom-input" required>
                                <option value="">-- Hasil Manual PEP --</option>
                                <option value="Tidak Terindikasi">Tidak Terindikasi</option>
                                <option value="Terindikasi">Terindikasi</option>
                            </select>

                            <!-- NEW BIG LOADING BLOCK -->
                            <div id="pep-loading-block" style="text-align: center; padding: 1.5rem; background: #f8f9fc; border: 1px dashed #d1d3e2; border-radius: 10px; margin-top: 15px;">
                                <i class="fas fa-spinner fa-spin fa-2x" style="color: #4e73df; margin-bottom: 10px;"></i>
                                <p style="color: #4a5568; font-weight: 700; margin: 0;">Memeriksa ke Server PPATK...</p>
                                <p style="color: #858796; font-size: 0.75rem; margin-top: 5px; margin-bottom: 0;">Sistem sedang melakukan sinkronisasi live.</p>
                            </div>
                            <!-- NEW RESULT BLOCK -->
                            <div id="pep-result-block" style="display: none; text-align: center; padding: 1.5rem; border-radius: 10px; margin-top: 15px; font-weight: 600;"></div>
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
                            <a href="pengajuan_tambah_manual.php"
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
                        Pencarian data yang mirip dengan <strong id="display-nama-dttot"
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
    <h3 style="font-weight: 700; color: var(--text-primary); font-size: 1.2rem;">Daftar Pengajuan Terbaru (Manual Input)</h3>
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

<?php if ($isVerify): ?>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const searchNik = <?php echo json_encode($input_data['nik']); ?>;
    const payload = new URLSearchParams();
    payload.append("nik", searchNik);

    const apiUrl = 'http://10.27.19.243:3000/api/v1/search';

    const controller = new AbortController();
    const timeoutId = setTimeout(() => controller.abort(), 60000); // Batas maksimal 60 detik

    fetch(apiUrl, {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: payload,
        signal: controller.signal
    })
    .then(response => {
        clearTimeout(timeoutId);
        return response.json();
    })
    .then(res => {
        document.getElementById('pep-loading-block').style.display = 'none';
        const resultBlock = document.getElementById('pep-result-block');
        const pepSelect = document.querySelector('select[name="hasil_pep"]');
        
        if(res.success && res.data && res.data.extracted_data) {
            const extracted = res.data.extracted_data;
            const records = extracted.data || [];
            
            if(records.length > 0) {
                // Update Name secara otomatis jika ada
                const firstRow = records[0];
                const namaPpatk = firstRow['Nama'] || firstRow['Nama Lengkap'] || firstRow['NAMA'];
                if (namaPpatk && namaPpatk !== 'Tidak Diketahui') {
                    const displayNama = document.getElementById('display-nama-cadeb');
                    const displayNamaDttot = document.getElementById('display-nama-dttot');
                    const inputNama = document.getElementById('input-nama-cadeb');
                    if (displayNama) displayNama.innerHTML = namaPpatk + ' <span style="font-size:0.65rem; background:#36b9cc; color:white; padding:3px 8px; border-radius:10px; vertical-align:middle; margin-left:5px; font-weight: 700;"><i class="fas fa-check"></i> PPATK</span>';
                    if (displayNamaDttot) displayNamaDttot.innerHTML = '"' + namaPpatk + '"';
                    if (inputNama) inputNama.value = namaPpatk;
                }

                if (pepSelect && !pepSelect.value) pepSelect.value = "Terindikasi";
                resultBlock.style.background = 'rgba(54, 185, 204, 0.1)';
                resultBlock.style.border = '1px solid #36b9cc';
                resultBlock.style.color = '#36b9cc';
                resultBlock.innerHTML = '<i class="fas fa-info-circle fa-2x" style="margin-bottom: 10px;"></i><br><span style="font-size: 1.1rem;">Tercatat dalam Database PEP</span><br><span style="font-size: 0.85rem; font-weight: normal; margin-top: 5px; display: inline-block;">(Menyesuaikan nama secara otomatis)</span>';
                resultBlock.style.display = 'block';
            } else {
                if (pepSelect && !pepSelect.value) pepSelect.value = "Tidak Terindikasi";
                resultBlock.style.background = 'rgba(28, 200, 138, 0.1)';
                resultBlock.style.border = '1px solid #1cc88a';
                resultBlock.style.color = '#1cc88a';
                resultBlock.innerHTML = '<i class="fas fa-check-circle fa-2x" style="margin-bottom: 10px;"></i><br><span style="font-size: 1.1rem;">Tidak Terindikasi</span><br><span style="font-size: 0.85rem; font-weight: normal; margin-top: 5px; display: inline-block;">(Data tidak ditemukan di database PPATK)</span>';
                resultBlock.style.display = 'block';
            }
        } else {
            throw new Error(res.error || res.message || "Sistem PPATK merespon dengan format yang tidak dikenal.");
        }
    })
    .catch(err => {
        document.getElementById('pep-loading-block').style.display = 'none';
        const resultBlock = document.getElementById('pep-result-block');
        resultBlock.style.background = 'rgba(231, 74, 59, 0.1)';
        resultBlock.style.border = '1px solid #e74a3b';
        resultBlock.style.color = '#e74a3b';
        
        let userMessage = "";
        const errMsg = err.message ? err.message.toLowerCase() : "";
        
        if (errMsg.includes("failed to fetch") || errMsg.includes("networkerror")) {
            userMessage = "Service API Internal (Scraper) mati atau tidak bisa dihubungi. Pastikan server Node.js menyala.";
        } else if (errMsg.includes("timeout") || errMsg.includes("exceeded") || errMsg.includes("gagal mengakses") || errMsg.includes("abort") || err.name === 'AbortError') {
            userMessage = "Website PPATK sedang sangat lambat atau Server Down. Sistem menghentikan proses karena melebihi batas waktu (60 detik).";
        } else if (errMsg.includes("captcha")) {
            userMessage = "Sistem gagal menembus perlindungan CAPTCHA PPATK. Ini biasanya terjadi jika IP sedang dibatasi sementara oleh Google.";
        } else if (errMsg.includes("login")) {
            userMessage = "Gagal login otomatis ke sistem PPATK. Cek apakah password berubah atau web PPATK sedang maintenance.";
        } else {
            userMessage = err.message || "Terjadi kesalahan tidak dikenal.";
        }

        resultBlock.innerHTML = `
            <i class="fas fa-server fa-2x" style="margin-bottom: 10px;"></i><br>
            <span style="font-size: 1.1rem;">Pengecekan Gagal / Timeout</span><br>
            <span style="font-size: 0.9rem; font-weight: normal; margin-top: 5px; display: inline-block;">Keterangan: ${userMessage}</span><br>
            <a href="https://pep.ppatk.go.id" target="_blank" style="display: inline-block; margin-top: 15px; padding: 8px 15px; background: #e74a3b; color: white; text-decoration: none; border-radius: 5px; font-weight: bold; font-size: 0.85rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1);"><i class="fas fa-external-link-alt"></i> Cek Manual di Portal PPATK</a>
        `;
        resultBlock.style.display = 'block';
    });
});
</script>
<?php endif; ?>

<?php include 'layout/footer.php'; ?>
