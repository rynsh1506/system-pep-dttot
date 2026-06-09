<?php
require_once 'auth.php';
require_once 'db.php';

if ($_SESSION['level'] != 1) {
    header('Location: index.php?msg=Hanya Staff (Level 1) yang dapat mengupload data.');
    exit;
}

// Download Template
if (isset($_GET['action']) && $_GET['action'] == 'template') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=Template_Upload_Cadeb.csv');
    $output = fopen('php://output', 'w');
    fputcsv($output, ['Nama Cadeb', 'No Identitas KTP', 'Nama Pasangan', 'No Identitas Pasangan', 'Kategori', 'Keterangan PEP']);
    fputcsv($output, ['JOHN DOE', '3201010101010001', 'JANE DOE', '3201010101010002', 'Cadeb', 'Tidak Ada Indikasi']);
    fclose($output);
    exit;
}

$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file_upload'])) {
    $file = $_FILES['file_upload'];

    // Check if file is uploaded and is a CSV
    if ($file['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        if (strtolower($ext) == 'csv') {
            $handle = fopen($file['tmp_name'], 'r');
            // Skip the first row (header)
            fgetcsv($handle);

            $inserted = 0;
            $failed = 0;

            $pdo->beginTransaction();
            try {
                $sql = "INSERT INTO candidates (nama_cadeb, no_identitas, nama_pasangan, no_identitas_pasangan, kategori, keterangan_pep, go_live) 
                        VALUES (?, ?, ?, ?, ?, ?, 'Tidak')";
                $stmt = $pdo->prepare($sql);

                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    if (count($data) >= 6) {
                        $nama_cadeb = trim($data[0]);
                        $no_identitas = trim($data[1]);
                        $nama_pasangan = trim($data[2]);
                        $no_identitas_pasangan = trim($data[3]);
                        $kategori = trim($data[4]);
                        $keterangan_pep = trim($data[5]);

                        // Basic validation
                        if (!empty($nama_cadeb) && !empty($no_identitas) && !empty($keterangan_pep) && !empty($kategori)) {
                            $stmt->execute([$nama_cadeb, $no_identitas, $nama_pasangan, $no_identitas_pasangan, $kategori, $keterangan_pep]);
                            $inserted++;
                        } else {
                            $failed++;
                        }
                    } else {
                        $failed++;
                    }
                }
                $pdo->commit();
                fclose($handle);
                header("Location: index.php?msg=" . urlencode("Upload Berhasil. $inserted baris ditambahkan, $failed baris gagal."));
                exit;
            } catch (Exception $e) {
                $pdo->rollBack();
                $msg = 'Terjadi kesalahan sistem saat menyimpan data: ' . $e->getMessage();
            }
        } else {
            $msg = 'Format file tidak didukung. Harap upload file CSV.';
        }
    } else {
        $msg = 'Gagal mengupload file.';
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Data CADEB</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="container" style="max-width: 600px;">
        <header>
            <h1>Upload Data Candidate</h1>
            <a href="index.php" class="btn btn-warning">Kembali</a>
        </header>

        <?php if ($msg): ?>
            <div
                style="background: rgba(239, 68, 68, 0.1); border: 1px solid var(--danger); color: var(--danger); padding: 1rem; border-radius: 0.5rem; margin-bottom: 2rem; font-size: 0.9rem;">
                ⚠️
                <?= htmlspecialchars($msg) ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="modal-content">
                <div style="margin-bottom: 20px; text-align: center;">
                    <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 10px;">
                        Silakan download template CSV di bawah ini sebelum mengupload data. Pastikan format sesuai
                        dengan template.
                    </p>
                    <a href="upload.php?action=template" class="btn btn-success"
                        style="background: rgba(16, 185, 129, 0.1); border: 1px solid var(--success); color: var(--success);">
                        ⬇️ Download Template CSV
                    </a>
                </div>

                <hr style="border: none; border-top: 1px solid rgba(0,0,0,0.1); margin: 20px 0;">

                <form method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label>Pilih File CSV</label>
                        <input type="file" name="file_upload" class="form-control" accept=".csv" required
                            style="padding-top: 10px;">
                    </div>
                    <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center;">Upload
                        File</button>
                    <p style="text-align: center; margin-top: 10px; font-size: 0.8rem; color: var(--text-muted);">
                        *Data 'Go Live' akan otomatis tersetting ke 'Tidak'
                    </p>
                </form>
            </div>
        </div>
    </div>
</body>

</html>