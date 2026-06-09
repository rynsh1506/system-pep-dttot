<?php
include 'layout/header.php';
require_once 'config/sqlserver.php';
require_once 'config/database.php';
// --- Ambil parameter filter ---
$branch_filter = $_GET['branch_id'] ?? '';
$bulan_filter = $_GET['bulan'] ?? date('m');
$tahun_filter = $_GET['tahun'] ?? date('Y');
$q_nama = $_GET['q_nama'] ?? '';
$q_nik = $_GET['q_nik'] ?? '';
$q_kontrak = $_GET['q_kontrak'] ?? '';



$branches = [];
$data = [];

if ($pdo_sqlsrv) {
    try {
        // --- Fetch list of branches ---
        $stmtBranches = $pdo_sqlsrv->query("SELECT BranchID, BranchFullName FROM Branch ORDER BY BranchFullName ASC");
        $branches = $stmtBranches->fetchAll(PDO::FETCH_ASSOC);

        // --- Main query runs if branch is selected (even if 'ALL') ---
        if ($branch_filter !== '') {
            $where_clauses = [
                "a.ContractStatus = 'LIV'",
                "MONTH(a.GoliveDate) = ?",
                "YEAR(a.GoliveDate) = ?"
            ];
            $params = [$bulan_filter, $tahun_filter];

            if ($branch_filter !== 'ALL') {
                $where_clauses[] = "a.BranchID = ?";
                $params[] = $branch_filter;
            }

            if ($q_nama !== '') {
                $where_clauses[] = "b.Name LIKE ?";
                $params[] = "%$q_nama%";
            }
            if ($q_nik !== '') {
                $where_clauses[] = "c.IDNumber LIKE ?";
                $params[] = "%$q_nik%";
            }
            if ($q_kontrak !== '') {
                $where_clauses[] = "a.AgreementNo LIKE ?";
                $params[] = "%$q_kontrak%";
            }

            $where_sql = implode(" AND ", $where_clauses);

            $tsql = "SELECT TOP 1500 
                        b.Name as nama, 
                        c.IDNumber as ktp, 
                        a.AgreementNo as no_kontrak, 
                        a.ContractStatus as status,
                        a.GoliveDate,
                        d.BranchFullName as cabang,
                        tj.Description as pekerjaan
                     FROM Agreement a 
                     INNER JOIN Customer b ON a.CustomerID = b.CustomerID
                     INNER JOIN PersonalCustomer c ON b.CustomerID = c.CustomerID
                     INNER JOIN TblJobList tj ON c.JobList = tj.id
                     LEFT JOIN Branch d ON a.BranchID = d.BranchID
                     WHERE $where_sql
                     ORDER BY a.GoliveDate DESC";

            $stmt = $pdo_sqlsrv->prepare($tsql);
            $stmt->execute($params);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // --- Fetch Last Check data from cekreksaloan ---
            $contract_nos = array_column($rows, 'no_kontrak');
            $checks = [];
            if (!empty($contract_nos)) {
                $placeholders = implode(',', array_fill(0, count($contract_nos), '?'));
                $stmtCheck = $pdo->prepare("SELECT no_kontrak, id, nama_debitur, nik, hasil_dtot, hasil_pep, keterangan, bukti_ss, checked_by, checked_at FROM cekreksaloan WHERE no_kontrak IN ($placeholders)");
                $stmtCheck->execute($contract_nos);
                $checks = $stmtCheck->fetchAll(PDO::FETCH_UNIQUE | PDO::FETCH_ASSOC);
            }

            // --- Assign last_check data from cekreksaloan ---
            foreach ($rows as &$row) {
                $no_kontrak = $row['no_kontrak'];
                $row['last_check'] = $checks[$no_kontrak] ?? null;
            }
            unset($row); // break reference
            $data = $rows;
        }
    } catch (PDOException $e) {
        echo "<div class='alert alert-danger'>Query Error: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
} else {
    echo "<div class='alert alert-danger'>Gagal menyambung ke database REKSALOAN. Periksa konfigurasi SQL Server.</div>";
}

$months = [
    '01' => 'Januari',
    '02' => 'Februari',
    '03' => 'Maret',
    '04' => 'April',
    '05' => 'Mei',
    '06' => 'Juni',
    '07' => 'Juli',
    '08' => 'Agustus',
    '09' => 'September',
    '10' => 'Oktober',
    '11' => 'November',
    '12' => 'Desember'
];
?>

<style>
    .filter-section {
        background: #fff;
        padding: 1.5rem;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        margin-bottom: 2rem;
    }

    .filter-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 1rem;
        align-items: end;
    }

    .form-group label {
        display: block;
        font-size: 0.8rem;
        font-weight: 600;
        color: var(--text-secondary);
        margin-bottom: 0.5rem;
    }

    .form-control {
        width: 100%;
        padding: 0.6rem 1rem;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        font-size: 0.9rem;
    }

    .btn-filter {
        padding: 0.6rem 1.5rem;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        border: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }

    .btn-primary-custom {
        background: var(--primary-color);
        color: #fff;
    }

    .btn-success-custom {
        background: #1cc88a;
        color: #fff;
    }

    .btn-filter:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
        background: #fff;
        border-radius: 12px;
        border: 2px dashed #e0e0e0;
    }

    .empty-state i {
        font-size: 3rem;
        color: #d1d3e2;
        margin-bottom: 1rem;
    }

    .empty-state h3 {
        color: var(--text-secondary);
        font-weight: 600;
        margin-bottom: 0.5rem;
    }

    .btn-cek {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.6rem;
        background: var(--primary-color);
        color: #fff !important;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        text-decoration: none !important;
        font-size: 0.85rem;
        font-weight: 600;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: none;
        white-space: nowrap;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .btn-cek:hover {
        background: var(--accent-color);
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(78, 115, 223, 0.25);
    }

    .btn-cek i {
        font-size: 0.9rem;
    }

    .status-check {
        display: flex;
        flex-direction: column;
        gap: 5px;
        min-width: 100px;
    }

    .check-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #fdfdfd;
        padding: 3px 8px;
        border-radius: 4px;
        border: 1px solid #eee;
        font-size: 0.7rem;
    }

    .check-label {
        font-weight: 600;
        color: #858796;
        text-transform: uppercase;
        font-size: 0.65rem;
    }

    .check-value {
        font-weight: 800;
    }

    .check-value.ya { color: #e74a3b; }
    .check-value.tidak { color: #1cc88a; }
</style>

<div class="dashboard-header" style="margin-bottom: 2rem;">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h2 style="font-weight: 700; color: var(--primary-color);">Cek Data Reksaloan</h2>
            <p style="color: var(--text-secondary); font-size: 0.9rem;">Integrasi data debitur (Status LIV) berdasarkan
                Cabang dan Periode GoliveDate.</p>
        </div>
        <?php if ($branch_filter !== '' && !empty($data)): ?>
            <a href="export_reksaloan.php?branch_id=<?php echo urlencode($branch_filter); ?>&bulan=<?php echo urlencode($bulan_filter); ?>&tahun=<?php echo urlencode($tahun_filter); ?>"
                class="btn-filter btn-success-custom" style="text-decoration: none;">
                <i class="fas fa-file-excel"></i> Export Excel
            </a>
        <?php endif; ?>
    </div>
</div>

<!-- Filter Section -->
<div class="filter-section">
    <form method="GET" action="" class="filter-grid">
        <div class="form-group">
            <label>BRANCH / CABANG <span style="color: red;">*</span></label>
            <select name="branch_id" class="form-control" required>
                <option value="">-- Pilih Branch --</option>
                <option value="ALL" <?php echo $branch_filter == 'ALL' ? 'selected' : ''; ?>>-- SEMUA CABANG --</option>
                <?php foreach ($branches as $br): ?>
                    <option value="<?php echo htmlspecialchars($br['BranchID']); ?>" <?php echo $branch_filter == $br['BranchID'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($br['BranchFullName']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label>PERIODE BULAN</label>
            <select name="bulan" class="form-control">
                <?php foreach ($months as $num => $name): ?>
                    <option value="<?php echo $num; ?>" <?php echo $bulan_filter == $num ? 'selected' : ''; ?>>
                        <?php echo $name; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label>TAHUN</label>
            <select name="tahun" class="form-control">
                <?php
                $currentYear = date('Y');
                for ($y = $currentYear; $y >= $currentYear - 3; $y--): ?>
                    <option value="<?php echo $y; ?>" <?php echo $tahun_filter == $y ? 'selected' : ''; ?>>
                        <?php echo $y; ?>
                    </option>
                <?php endfor; ?>
            </select>
        </div>
        <div class="form-group">
            <label>NAMA DEBITUR</label>
            <input type="text" name="q_nama" class="form-control" value="<?php echo htmlspecialchars($q_nama); ?>" placeholder="Cari Nama...">
        </div>
        <div class="form-group">
            <label>NIK / KTP</label>
            <input type="text" name="q_nik" class="form-control" value="<?php echo htmlspecialchars($q_nik); ?>" placeholder="Cari NIK...">
        </div>
        <div class="form-group">
            <label>NO KONTRAK</label>
            <input type="text" name="q_kontrak" class="form-control" value="<?php echo htmlspecialchars($q_kontrak); ?>" placeholder="Cari Kontrak...">
        </div>
        <div class="form-group" style="display: flex; gap: 0.5rem; grid-column: span 1;">
            <button type="submit" class="btn-filter btn-primary-custom" style="width: 100%;">
                <i class="fas fa-search"></i> Tampilkan
            </button>
            <a href="reksaloan.php" class="btn-filter"
                style="background: #f8f9fc; color: #4e73df; text-decoration: none; text-align: center; border: 1px solid #e0e0e0; width: 100%;">
                <i class="fas fa-undo"></i> Reset
            </a>
        </div>
    </form>
</div>

<?php if ($branch_filter === ''): ?>
    <div class="empty-state">
        <i class="fas fa-building"></i>
        <h3>Silakan Pilih Branch Terlebih Dahulu</h3>
        <p>Pilih salah satu cabang atau "Semua Cabang" dari dropdown di atas untuk menampilkan data.</p>
    </div>
<?php elseif (empty($data)): ?>
    <div class="empty-state">
        <i class="fas fa-search"></i>
        <h3>Data Tidak Ditemukan</h3>
        <p>Tidak ada data debitur dengan status LIV pada periode yang dipilih.</p>
    </div>
<?php else: ?>
    <div class="data-table-container">
        <div style="overflow-x: auto; max-height: 70vh; overflow-y: auto;">
            <table style="position: relative;">
                <thead style="position: sticky; top: 0; z-index: 10;">
                    <tr>
                        <?php if ($branch_filter == 'ALL'): ?>
                            <th style="background: #fdfdfd;">CABANG</th>
                        <?php endif; ?>
                        <th style="background: #fdfdfd;">NAMA DEBITUR</th>
                        <th style="background: #fdfdfd;">NOMOR KTP</th>
                        <th style="background: #fdfdfd;">NOMOR KONTRAK</th>
                        <th style="background: #fdfdfd;">PEKERJAAN</th>
                        <th style="background: #fdfdfd;">GOLIVE DATE</th>
                        <th style="background: #fdfdfd;">STATUS KONTRAK</th>
                        <th style="background: #fdfdfd;">SISTEM CEK (DTOT/PEP)</th>
                        <th style="background: #fdfdfd;">LAST CEK (MANUAL)</th>
                        <th style="background: #fdfdfd; text-align: center;">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data as $row): ?>
                        <tr>
                            <?php if ($branch_filter == 'ALL'): ?>
                                <td style="font-size: 0.8rem; font-weight: 600; vertical-align: middle;">
                                    <?php echo htmlspecialchars($row['cabang'] ?? '-'); ?>
                                </td>
                            <?php endif; ?>
                            <td style="font-weight: 600; color: var(--primary-color); vertical-align: middle;">
                                <?php echo htmlspecialchars($row['nama']); ?>
                            </td>
                            <td style="vertical-align: middle;">
                                <?php echo htmlspecialchars($row['ktp']); ?>
                            </td>
                            <td style="vertical-align: middle;">
                                <?php echo htmlspecialchars($row['no_kontrak']); ?>
                            </td>
                            <td style="font-size: 0.85rem; vertical-align: middle;">
                                <?php echo htmlspecialchars($row['pekerjaan'] ?? '-'); ?>
                            </td>
                            <td style="font-size: 0.85rem; vertical-align: middle;">
                                <?php echo $row['GoliveDate'] ? date('d/m/Y', strtotime($row['GoliveDate'])) : '-'; ?>
                            </td>
                            <td style="vertical-align: middle;">
                                <?php
                                $status_color = $row['status'] == 'LIV' ? '#4e73df' : ($row['status'] == 'EXP' ? '#1cc88a' : '#e74a3b');
                                ?>
                                <span class="badge"
                                    style="background: <?php echo $status_color; ?>1a; color: <?php echo $status_color; ?>; font-weight: 600;">
                                    <?php echo $row['status']; ?>
                                </span>
                            </td>
                            <td style="vertical-align: middle;">
                                <?php if ($row['last_check']): ?>
                                    <div class="status-check">
                                        <div class="check-item">
                                            <span class="check-label">DTTOT</span>
                                            <span class="check-value <?php echo $row['last_check']['hasil_dtot'] == 'Terindikasi' ? 'ya' : 'tidak'; ?>">
                                                <?php echo $row['last_check']['hasil_dtot']; ?>
                                            </span>
                                        </div>
                                        <div class="check-item">
                                            <span class="check-label">PEP</span>
                                            <span class="check-value <?php echo $row['last_check']['hasil_pep'] == 'Terindikasi' ? 'ya' : 'tidak'; ?>">
                                                <?php echo $row['last_check']['hasil_pep']; ?>
                                            </span>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <span style="color: #d1d3e2; font-style: italic; font-size: 0.8rem;">Belum dicek</span>
                                <?php endif; ?>
                            </td>
                            <td style="vertical-align: middle;">
                                <?php if ($row['last_check']): ?>
                                    <div style="font-size: 0.8rem; font-weight: 600;">
                                        <?php echo date('d/m/y H:i', strtotime($row['last_check']['checked_at'])); ?>
                                    </div>
                                    <div style="font-size: 0.75rem;">
                                       DTOT: <?php echo $row['last_check']['hasil_dtot']; ?><br>
                                       PEP: <?php echo $row['last_check']['hasil_pep']; ?>
                                    </div>
                                <?php else: ?>
                                    <span style="color: #d1d3e2; font-style: italic; font-size: 0.8rem;">Belum ada riwayat</span>
                                <?php endif; ?>
                            </td>
                            <td style="text-align: center; vertical-align: middle;">
                                <a href="proses_cek_reksaloan.php?kontrak=<?php echo urlencode($row['no_kontrak']); ?>" 
                                   class="btn-cek" title="Cek & Simpan Hasil">
                                    <i class="fas fa-clipboard-check"></i>
                                    <span>Cek</span>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>

<?php include 'layout/footer.php'; ?>