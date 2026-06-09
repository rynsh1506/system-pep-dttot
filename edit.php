<?php
require_once 'auth.php';
require_once 'db.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: index.php');
    exit;
}

// Fetch candidate data
$stmt = $pdo->prepare("SELECT * FROM candidates WHERE id = ?");
$stmt->execute([$id]);
$candidate = $stmt->fetch();

if (!$candidate) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newData = [
        'nama_cadeb' => $_POST['nama_cadeb'],
        'no_identitas' => $_POST['no_identitas'],
        'nama_pasangan' => $_POST['nama_pasangan'],
        'no_identitas_pasangan' => $_POST['no_identitas_pasangan'],
        'kategori' => $_POST['kategori'],
        'keterangan_pep' => $_POST['keterangan_pep'],
        'go_live' => $_POST['go_live']
    ];

    if ($_SESSION['level'] == 1) {
        // Create Approval Request
        $qReq = $pdo->prepare("INSERT INTO approval_requests (candidate_id, type, old_data, new_data, requester_id) VALUES (?, 'EDIT', ?, ?, ?)");
        $qReq->execute([
            $id,
            json_encode($candidate),
            json_encode($newData),
            $_SESSION['user_id']
        ]);
        header('Location: index.php?msg=Request Edit Terkirim. Menunggu Approval L2 & L3. *Data otomatis berubah ketika sudah approve final');
    } else {
        // Direct Update for L2/L3 (Optional, usually they just approve)
        $sql = "UPDATE candidates SET nama_cadeb = ?, no_identitas = ?, nama_pasangan = ?, no_identitas_pasangan = ?, kategori = ?, keterangan_pep = ?, go_live = ? 
                WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $newData['nama_cadeb'],
            $newData['no_identitas'],
            $newData['nama_pasangan'],
            $newData['no_identitas_pasangan'],
            $newData['kategori'],
            $newData['keterangan_pep'],
            $newData['go_live'],
            $id
        ]);
        header('Location: index.php?msg=Data Berhasil Diupdate Langsung.');
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Data CADEB</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="container" style="max-width: 600px;">
        <header>
            <h1>Edit Data Candidate</h1>
            <a href="index.php" class="btn btn-warning">Kembali</a>
        </header>

        <div class="card">
            <div class="modal-content">
                <form method="POST">
                    <div class="form-group">
                        <label>Nama Cadeb</label>
                        <input type="text" name="nama_cadeb" class="form-control" required
                            value="<?= htmlspecialchars($candidate['nama_cadeb']) ?>">
                    </div>
                    <div class="form-group">
                        <label>No Identitas (KTP)</label>
                        <input type="text" name="no_identitas" class="form-control" required
                            value="<?= htmlspecialchars($candidate['no_identitas']) ?>">
                    </div>
                    <div class="form-group">
                        <label>Nama Pasangan Cadeb (Opsional)</label>
                        <input type="text" name="nama_pasangan" class="form-control"
                            value="<?= htmlspecialchars($candidate['nama_pasangan']) ?>">
                    </div>
                    <div class="form-group">
                        <label>No Identitas Pasangan (Opsional)</label>
                        <input type="text" name="no_identitas_pasangan" class="form-control"
                            value="<?= htmlspecialchars($candidate['no_identitas_pasangan']) ?>">
                    </div>
                    <div class="form-group">
                        <label>Kategori</label>
                        <select name="kategori" class="form-control" required>
                            <option value="Cadeb" <?= ($candidate['kategori'] ?? '') == 'Cadeb' ? 'selected' : '' ?>>Cadeb
                            </option>
                            <option value="Debitur Existing" <?= ($candidate['kategori'] ?? '') == 'Debitur Existing' ? 'selected' : '' ?>>Debitur Existing</option>
                            <option value="Karyawan /New" <?= ($candidate['kategori'] ?? '') == 'Karyawan /New' ? 'selected' : '' ?>>Karyawan /New</option>
                            <option value="Rekanan Existing/New" <?= ($candidate['kategori'] ?? '') == 'Rekanan Existing/New' ? 'selected' : '' ?>>Rekanan Existing/New</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Keterangan PEP</label>
                        <select name="keterangan_pep" class="form-control" required>
                            <option value="Tidak Ada Indikasi" <?= $candidate['keterangan_pep'] == 'Tidak Ada Indikasi' ? 'selected' : '' ?>>Tidak Ada Indikasi</option>
                            <option value="Cadeb" <?= $candidate['keterangan_pep'] == 'Cadeb' ? 'selected' : '' ?>>Cadeb
                            </option>
                            <option value="Pasangan Cadeb" <?= $candidate['keterangan_pep'] == 'Pasangan Cadeb' ? 'selected' : '' ?>>Pasangan Cadeb</option>
                            <option value="Cadeb & Pasangan" <?= $candidate['keterangan_pep'] == 'Cadeb & Pasangan' ? 'selected' : '' ?>>Cadeb & Pasangan</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Go Live</label>
                        <select name="go_live" class="form-control" required>
                            <option value="Ya" <?= $candidate['go_live'] == 'Ya' ? 'selected' : '' ?>>Ya</option>
                            <option value="Tidak" <?= $candidate['go_live'] == 'Tidak' ? 'selected' : '' ?>>Tidak</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center;">Update
                        Data</button>
                </form>
            </div>
        </div>
    </div>
</body>

</html>