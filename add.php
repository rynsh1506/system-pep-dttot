<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';
require_once 'auth.php';
require_once 'db.php';

if ($_SESSION['level'] != 1) {
    header('Location: index.php?msg=Hanya Staff (Level 1) yang dapat menambah data.');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_cadeb = $_POST['nama_cadeb'];
    $no_identitas = $_POST['no_identitas'];
    $nama_pasangan = $_POST['nama_pasangan'];
    $no_identitas_pasangan = $_POST['no_identitas_pasangan'];
    $kategori = $_POST['kategori'];
    $keterangan_pep = $_POST['keterangan_pep'];

    $sql = "INSERT INTO candidates (nama_cadeb, no_identitas, nama_pasangan, no_identitas_pasangan, kategori,
keterangan_pep, go_live)
VALUES (?, ?, ?, ?, ?, ?, '')";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$nama_cadeb, $no_identitas, $nama_pasangan, $no_identitas_pasangan, $kategori, $keterangan_pep]);

    // Auto Email logic to Div RC/HRD using PHPMailer
    $mail = new PHPMailer(true);
    try {
        // Server settings
// TODO: GANTI PENGATURAN SMTP DI BAWAH INI SESUAI EMAIL ANDA
        $mail->isSMTP();
        $mail->SMTPAuth = true;
        // $mail->SMTPDebug = 2;
        $mail->Debugoutput = 'html';

        $mail->SMTPSecure = 'ssl';
        $mail->Host = gethostbyname('mail.reksafinance.com');
        // $mail->SMTPDebug = 1;	
        $mail->Port = 465;
        $mail->Username = '10271927@reksafinance.com';
        $mail->Password = 'Rf2024!@@';
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );
        $mail->IsHTML(true);
        $mail->SetFrom('10271927@reksafinance.com', 'Sistem PEP');
        $mail->addAddress('adwin.bhaskoro@reksafinance.com', 'Adwin Bhaskoro');

        // Content
        $mail->isHTML(false);
        $mail->Subject = 'Hasil Pengecekan PEP Baru: ' . $nama_cadeb;
        $mail->Body = "Telah diinput hasil pengecekan PEP baru.\n\n" .
            "Nama: $nama_cadeb\n" .
            "KTP: $no_identitas\n" .
            "Kategori: $kategori\n" .
            "Keterangan PEP: " . ($keterangan_pep !== 'Tidak Ada Indikasi' ? "Terindikasi $keterangan_pep" : $keterangan_pep) . "\n\n" .
            "Sistem PEP.";

        $mail->send();
        $msg_email = " & email notifikasi terkirim.";
    } catch (Exception $e) {
        $msg_email = " namun GAGAL mengirim email: {$mail->ErrorInfo}";
    }

    header('Location: index.php?msg=' . urlencode('Data berhasil disimpan' . $msg_email));
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Data CADEB</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="container" style="max-width: 600px;">
        <header>
            <h1>Tambah Data Candidate</h1>
            <a href="index.php" class="btn btn-warning">Kembali</a>
        </header>

        <div class="card">
            <div class="modal-content">
                <form method="POST">
                    <div class="form-group">
                        <label>Nama Cadeb</label>
                        <input type="text" name="nama_cadeb" class="form-control" required
                            placeholder="Contoh: SEPTA AGUNG KURNIAWAN">
                    </div>
                    <div class="form-group">
                        <label>No Identitas (KTP)</label>
                        <input type="text" name="no_identitas" class="form-control" required
                            placeholder="Masukkan nomor identitas">
                    </div>
                    <div class="form-group">
                        <label>Nama Pasangan Cadeb (Opsional)</label>
                        <input type="text" name="nama_pasangan" class="form-control"
                            placeholder="Biarkan kosong jika tidak ada">
                    </div>
                    <div class="form-group">
                        <label>No Identitas Pasangan (Opsional)</label>
                        <input type="text" name="no_identitas_pasangan" class="form-control"
                            placeholder="Biarkan kosong jika tidak ada">
                    </div>
                    <div class="form-group">
                        <label>Kategori</label>
                        <select name="kategori" class="form-control" required>
                            <option value="Cadeb">Cadeb</option>
                            <option value="Debitur Existing">Debitur Existing</option>
                            <option value="Karyawan /New">Karyawan /New</option>
                            <option value="Rekanan Existing/New">Rekanan Existing/New</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Keterangan PEP</label>
                        <select name="keterangan_pep" class="form-control" required>
                            <option value="Tidak Ada Indikasi">Tidak Ada Indikasi</option>
                            <option value="Cadeb">Cadeb</option>
                            <option value="Pasangan Cadeb">Pasangan Cadeb</option>
                            <option value="Cadeb & Pasangan">Cadeb & Pasangan</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center;">Simpan
                        Data</button>
                </form>
            </div>
        </div>
    </div>
</body>

</html>