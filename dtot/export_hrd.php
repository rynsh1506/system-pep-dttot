<?php
require 'vendor/autoload.php';
require_once 'config/database.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

// 1. Get Filters
$start_date = $_GET['start_date'] ?? date('Y-m-d', strtotime('-1 month'));
$end_date = $_GET['end_date'] ?? date('Y-m-d');
$hasil = $_GET['hasil'] ?? 'All';
$kategori_filter = $_GET['kategori_filter'] ?? 'All';

// 2. Query Data
$query = "SELECT p.*, u.full_name as checker_name 
          FROM pengajuan_dtot p 
          LEFT JOIN users u ON p.checked_by = u.id 
          WHERE p.kategori IN ('Karyawan', 'Vendor')
          AND DATE(p.tanggal) BETWEEN ? AND ?";
$params = [$start_date, $end_date];

if ($hasil !== 'All') {
    $query .= " AND p.hasil_pengecekan = ?";
    $params[] = $hasil;
}

if ($kategori_filter !== 'All') {
    $query .= " AND p.kategori = ?";
    $params[] = $kategori_filter;
}

$query .= " ORDER BY p.tanggal DESC, p.created_at DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$data = $stmt->fetchAll();

// 3. Create Spreadsheet
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// 4. Set Header Logic
$headers = [
    'A1' => 'No',
    'B1' => 'Tanggal Pengajuan',
    'C1' => 'Kategori',
    'D1' => 'Nama Karyawan/Vendor',
    'E1' => 'NIK / Identitas',
    'F1' => 'Hasil DTTOT',
    'G1' => 'Hasil PEP',
    'H1' => 'Keterangan',
    'I1' => 'Bukti',
    'J1' => 'Pemeriksa',
    'K1' => 'Waktu Cek'
];

foreach ($headers as $cell => $text) {
    $sheet->setCellValue($cell, $text);
    $sheet->getStyle($cell)->getFont()->setBold(true);
    $sheet->getStyle($cell)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
}

// 5. Populate Data
$row = 2;
$no = 1;
foreach ($data as $d) {
    $sheet->setCellValue('A' . $row, $no++);
    $sheet->setCellValue('B' . $row, date('d/m/Y', strtotime($d['tanggal'])));
    $sheet->setCellValue('C' . $row, $d['kategori']);
    $sheet->setCellValue('D' . $row, $d['nama_cadeb']);
    $sheet->setCellValue('E' . $row, $d['nik']);
    $sheet->setCellValue('F' . $row, $d['hasil_pengecekan']);
    $sheet->setCellValue('G' . $row, $d['hasil_pep'] ?: 'Belum Dicek');
    $sheet->setCellValue('H' . $row, $d['keterangan']);
    
    // Embed Image for 'Bukti'
    if (!empty($d['bukti_ss']) && file_exists('uploads/' . $d['bukti_ss'])) {
        $drawing = new Drawing();
        $drawing->setName('Bukti Screenshot');
        $drawing->setDescription('Bukti SS');
        $drawing->setPath('uploads/' . $d['bukti_ss']);
        $drawing->setHeight(80); // Pixel height
        $drawing->setCoordinates('I' . $row);
        $drawing->setWorksheet($sheet);
        
        // Adjust row height to fit image
        $sheet->getRowDimension($row)->setRowHeight(70); // Point height
    } else {
        $sheet->setCellValue('I' . $row, '-');
    }

    $sheet->setCellValue('J' . $row, $d['checker_name'] ?: '-');
    $sheet->setCellValue('K' . $row, $d['checked_at'] ? date('d/m/Y H:i', strtotime($d['checked_at'])) : '-');
    $row++;
}

// 6. Formatting
foreach (range('A', 'K') as $col) {
    if ($col == 'I') {
        $sheet->getColumnDimension($col)->setWidth(30);
    } else {
        $sheet->getColumnDimension($col)->setAutoSize(true);
    }
}

// 7. Output File
$filename = 'Laporan_Hasil_Cek_HRD_' . date('Ymd_His') . '.xlsx';

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $filename . '"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
