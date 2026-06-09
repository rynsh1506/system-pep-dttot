<?php
require_once 'config/database.php'; // Koneksi MySQL (dtot)
require_once 'config/sqlserver.php'; // Koneksi SQL Server (reksaloan)

// --- Ambil parameter filter ---
$branch_filter = $_GET['branch_id'] ?? '';
$bulan_filter = $_GET['bulan'] ?? '';
$tahun_filter = $_GET['tahun'] ?? '';

if ($branch_filter === '' || $bulan_filter === '' || $tahun_filter === '') {
    die("Error: Branch, Month, and Year must be selected for export.");
}

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

$data = [];
if ($pdo_sqlsrv) {
    try {
        $where_clauses = [
            "a.ContractStatus = 'LIV'",
            "MONTH(a.GoliveDate) = ?",
            "YEAR(a.GoliveDate) = ?"
        ];
        $params = [$bulan_filter, $tahun_filter];

        if ($branch_filter !== 'ALL') {
            array_unshift($where_clauses, "a.BranchID = ?");
            array_unshift($params, $branch_filter);
        }

        $where_sql = implode(" AND ", $where_clauses);

        // Limit lebih besar untuk export
        $tsql = "SELECT TOP 10000 
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
                 WHERE $where_sql
                 ORDER BY a.GoliveDate DESC";

        $stmt = $pdo_sqlsrv->prepare($tsql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // --- Cek terindikasi ---
        foreach ($rows as &$row) {
            $ktp = trim($row['ktp'] ?? '');
            $row['terindikasi_dtot'] = 'TIDAK';
            $row['terindikasi_pep'] = 'TIDAK';

            if ($ktp !== '') {
                // DTOT
                $stmtDtot = $pdo->prepare("SELECT COUNT(*) FROM terduga WHERE deleted_at IS NULL AND deskripsi LIKE ?");
                $stmtDtot->execute(["%$ktp%"]);
                if ($stmtDtot->fetchColumn() > 0) $row['terindikasi_dtot'] = 'YA';

                // PEP
                if ($pdo_pep) {
                    $stmtPep = $pdo_pep->prepare("SELECT COUNT(*) FROM candidates WHERE no_identitas = ? OR no_identitas_pasangan = ?");
                    $stmtPep->execute([$ktp, $ktp]);
                    if ($stmtPep->fetchColumn() > 0) $row['terindikasi_pep'] = 'YA';
                }
            }
        }
        $data = $rows;
    } catch (PDOException $e) {
        die("Query Error: " . $e->getMessage());
    }
} else {
    die("Gagal menyambung ke database REKSALOAN.");
}

// --- Generate CSV Download ---
$filename = "Export_Reksaloan_LIV_" . date('Ymd_His') . ".csv";

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=' . $filename);

$output = fopen('php://output', 'w');

// Output header kolom
$csv_header = ['NAMA DEBITUR', 'NOMOR KTP', 'NOMOR KONTRAK', 'GOLIVE DATE', 'STATUS KONTRAK', 'TERINDIKASI DTOT', 'TERINDIKASI PEP'];
if ($branch_filter == 'ALL') {
    array_unshift($csv_header, 'CABANG');
}
fputcsv($output, $csv_header);

// Output data
foreach ($data as $row) {
    $csv_row = [
        $row['nama'],
        "'" . $row['ktp'], // Pake single quote biar KTP gak jadi scientific notation di Excel
        $row['no_kontrak'],
        $row['GoliveDate'] ? date('d/m/Y', strtotime($row['GoliveDate'])) : '-',
        $row['status'],
        $row['terindikasi_dtot'],
        $row['terindikasi_pep']
    ];
    if ($branch_filter == 'ALL') {
        array_unshift($csv_row, $row['cabang']);
    }
    fputcsv($output, $csv_row);
}

fclose($output);
exit;
