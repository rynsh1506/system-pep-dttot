<?php
header('Content-Type: application/json');

require_once 'config/database.php'; // Koneksi MySQL (dtot)
require_once 'config/sqlserver.php'; // Koneksi SQL Server (reksaloan)

// --- Ambil parameter periode ---
$bulan = $_POST['bulan'] ?? $_GET['bulan'] ?? date('m');
$tahun = $_POST['tahun'] ?? $_GET['tahun'] ?? date('Y');

if (!$bulan || !$tahun) {
    echo json_encode(['success' => false, 'message' => 'Bulan dan Tahun harus diisi.']);
    exit;
}

// Format bulan agar selalu 2 digit untuk nama folder/file
$bulan_str = str_pad($bulan, 2, '0', STR_PAD_LEFT);

// --- Koneksi PEP (cadeb_db) ---
try {
    $pdo_pep = new PDO("mysql:host=localhost;dbname=cadeb_db;charset=utf8mb4", "root", "", [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    error_log("Koneksi PEP DB Gagal: " . $e->getMessage());
    $pdo_pep = null;
}

if (!$pdo_sqlsrv) {
    echo json_encode(['success' => false, 'message' => 'Gagal koneksi ke SQL Server.']);
    exit;
}

try {
    // 1. Fetch Data dari SQL Server (LIV Only for that period)
    $tsql = "SELECT 
                b.Name as nama, 
                c.IDNumber as ktp, 
                a.AgreementNo as no_kontrak, 
                a.ContractStatus as status,
                a.GoliveDate,
                d.BranchFullName as cabang
             FROM Agreement a 
             INNER JOIN Customer b ON a.CustomerID = b.CustomerID
             INNER JOIN PersonalCustomer c ON b.CustomerID = c.CustomerID
             LEFT JOIN Branch d ON a.BranchID = d.BranchID
             WHERE a.ContractStatus = 'LIV' 
               AND MONTH(a.GoliveDate) = ? 
               AND YEAR(a.GoliveDate) = ?
             ORDER BY a.BranchID ASC, a.GoliveDate ASC";

    $stmt = $pdo_sqlsrv->prepare($tsql);
    $stmt->execute([(int)$bulan, (int)$tahun]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($rows)) {
        echo json_encode(['success' => false, 'message' => "Tidak ada data LIV untuk periode $bulan_str/$tahun."]);
        exit;
    }

    // 2. Lakukan Pengecekan DTTO & PEP
    foreach ($rows as &$row) {
        $ktp = trim($row['ktp'] ?? '');
        $row['terindikasi_dtot'] = 'TIDAK';
        $row['terindikasi_pep'] = 'TIDAK';

        if ($ktp !== '') {
            // Cek DTTOT
            $stmtDtot = $pdo->prepare("SELECT COUNT(*) FROM terduga WHERE deleted_at IS NULL AND deskripsi LIKE ?");
            $stmtDtot->execute(["%$ktp%"]);
            if ($stmtDtot->fetchColumn() > 0) $row['terindikasi_dtot'] = 'YA';

            // Cek PEP
            if ($pdo_pep) {
                $stmtPep = $pdo_pep->prepare("SELECT COUNT(*) FROM candidates WHERE no_identitas = ? OR no_identitas_pasangan = ?");
                $stmtPep->execute([$ktp, $ktp]);
                if ($stmtPep->fetchColumn() > 0) $row['terindikasi_pep'] = 'YA';
            }
        }
    }
    unset($row);

    // 3. Persiapkan Folder
    $base_dir = "exports_monthly";
    $year_dir = "$base_dir/$tahun";
    $month_dir = "$year_dir/$bulan_str";

    if (!is_dir($month_dir)) {
        mkdir($month_dir, 0777, true);
    }

    // 4. Tulis ke CSV
    $filename = "DTTO_PEP_Result_All_Branches.csv";
    $filepath = "$month_dir/$filename";
    
    $fp = fopen($filepath, 'w');
    // Header
    fputcsv($fp, ['CABANG', 'NAMA DEBITUR', 'NOMOR KTP', 'NOMOR KONTRAK', 'GOLIVE DATE', 'STATUS KONTRAK', 'TERINDIKASI DTOT', 'TERINDIKASI PEP']);
    
    // Data
    foreach ($rows as $row) {
        fputcsv($fp, [
            $row['cabang'] ?? '-',
            $row['nama'],
            "'" . $row['ktp'],
            $row['no_kontrak'],
            $row['GoliveDate'] ? date('d/m/Y', strtotime($row['GoliveDate'])) : '-',
            $row['status'],
            $row['terindikasi_dtot'],
            $row['terindikasi_pep']
        ]);
    }
    fclose($fp);

    echo json_encode([
        'success' => true, 
        'message' => "Laporan periode $bulan_str/$tahun berhasil dibuat.",
        'path' => $filepath,
        'count' => count($rows)
    ]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => "Terjadi kesalahan: " . $e->getMessage()]);
}
