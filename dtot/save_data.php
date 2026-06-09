<?php
/**
 * Process Upload Logic
 * DTOT System
 */

session_name('pep');
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

use PhpOffice\PhpSpreadsheet\IOFactory;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['excel_file'])) {
    $file = $_FILES['excel_file'];

    // Validate File
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed = ['xlsx', 'csv'];

    if (!in_array($extension, $allowed)) {
        die("Format file tidak didukung. Gunakan .xlsx atau .csv");
    }

    try {
        $spreadsheet = IOFactory::load($file['tmp_name']);
        $worksheet = $spreadsheet->getActiveSheet();
        $rows = $worksheet->toArray();

        // Remove Header Row
        $header = array_shift($rows);

        /**
         * Mapping (asumsi urutan kolom di Excel sesuai screenshot):
         * 0: Nama
         * 1: Deskripsi
         * 2: Terduga (Orang/Korporasi)
         * 3: Kode Densus
         * 4: Tempat Lahir
         * 5: Tanggal Lahir (DD/MM/YYYY)
         * 6: WN/Asal Negara
         * 7: Alamat
         */

        $pdo->beginTransaction();

        $sql = "INSERT INTO terduga (nama, deskripsi, terduga_type, kode_densus, tempat_lahir, tanggal_lahir, wn_asal_negara, alamat) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);

        $count = 0;
        foreach ($rows as $row) {
            if (empty($row[0]))
                continue; // Skip empty rows

            // Format Tanggal (DD/MM/YYYY to YYYY-MM-DD)
            $tanggal_lahir = null;
            if (!empty($row[5]) && $row[5] !== '-') {
                // Mencoba parse tanggal (asumsi Excel date atau string DD/MM/YYYY)
                $d = DateTime::createFromFormat('d/m/Y', $row[5]);
                if ($d) {
                    $tanggal_lahir = $d->format('Y-m-d');
                } else {
                    // Coba strtotime jika string bebas
                    $ts = strtotime($row[5]);
                    if ($ts)
                        $tanggal_lahir = date('Y-m-d', $ts);
                }
            }

            $stmt->execute([
                $row[0], // Nama
                $row[1], // Deskripsi
                in_array($row[2], ['Orang', 'Korporasi']) ? $row[2] : 'Orang', // Default to Orang
                $row[3], // Kode Densus
                $row[4] === '-' ? '' : $row[4], // Tempat Lahir
                $tanggal_lahir,
                $row[6], // WN
                $row[7] === 'N/A' || $row[7] === '-' ? '' : $row[7] // Alamat
            ]);
            $count++;
        }

        $pdo->commit();

        header("Location: index.php?status=success&count=$count");
        exit;

    } catch (Exception $e) {
        if ($pdo->inTransaction())
            $pdo->rollBack();
        die("Error memproses file: " . $e->getMessage());
    }
} else {
    header("Location: upload.php");
    exit;
}
