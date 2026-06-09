<?php
require_once 'auth.php';
require_once 'db.php';

if ($_SESSION['level'] < 2) {
    header('Location: index.php');
    exit;
}

$user_level = $_SESSION['level'];
$user_id = $_SESSION['user_id'];

// Handle Approval Action
if (isset($_POST['action'])) {
    $request_id = $_POST['request_id'];
    $action = $_POST['action']; // 'APPROVED' or 'REJECTED'
    $notes = $_POST['notes'] ?? '';

    if ($user_level == 2) {
        $stmt = $pdo->prepare("UPDATE approval_requests SET l2_status = ?, l2_approver_id = ?, l2_notes = ? WHERE id = ?");
        $stmt->execute([$action, $user_id, $notes, $request_id]);

        if ($action == 'REJECTED') {
            $pdo->prepare("UPDATE approval_requests SET final_status = 'REJECTED' WHERE id = ?")->execute([$request_id]);
        }
    } elseif ($user_level == 3) {
        $stmt = $pdo->prepare("UPDATE approval_requests SET l3_status = ?, l3_approver_id = ?, l3_notes = ? WHERE id = ?");
        $stmt->execute([$action, $user_id, $notes, $request_id]);

        if ($action == 'REJECTED') {
            $pdo->prepare("UPDATE approval_requests SET final_status = 'REJECTED' WHERE id = ?")->execute([$request_id]);
        } else {
            // Level 3 APPROVED -> Final Execution
            $qReq = $pdo->prepare("SELECT * FROM approval_requests WHERE id = ?");
            $qReq->execute([$request_id]);
            $request = $qReq->fetch();

            if ($request['type'] == 'EDIT') {
                $new_data = json_decode($request['new_data'], true);
                $stmt = $pdo->prepare("UPDATE candidates SET nama_cadeb = ?, no_identitas = ?, nama_pasangan = ?, no_identitas_pasangan = ?, keterangan_pep = ?, go_live = ? WHERE id = ?");
                $stmt->execute([
                    $new_data['nama_cadeb'],
                    $new_data['no_identitas'],
                    $new_data['nama_pasangan'],
                    $new_data['no_identitas_pasangan'],
                    $new_data['keterangan_pep'],
                    $new_data['go_live'],
                    $request['candidate_id']
                ]);
            } elseif ($request['type'] == 'DELETE') {
                $stmt = $pdo->prepare("DELETE FROM candidates WHERE id = ?");
                $stmt->execute([$request['candidate_id']]);
            }

            // Mark as COMPLETED
            $pdo->prepare("UPDATE approval_requests SET final_status = 'COMPLETED' WHERE id = ?")->execute([$request_id]);
        }
    }
    header('Location: approvals.php?msg=Request Updated');
    exit;
}

// Fetch Pending Requests
if ($user_level == 2) {
    // Level 2 sees all PENDING at Level 2
    $q = $pdo->prepare("SELECT a.*, u.nama_lengkap as requester_name FROM approval_requests a JOIN users u ON a.requester_id = u.id WHERE a.l2_status = 'PENDING' AND a.final_status = 'PENDING' ORDER BY a.created_at DESC");
} else {
    // Level 3 sees only those APPROVED by L2 and PENDING at Level 3
    $q = $pdo->prepare("SELECT a.*, u.nama_lengkap as requester_name FROM approval_requests a JOIN users u ON a.requester_id = u.id WHERE a.l2_status = 'APPROVED' AND a.l3_status = 'PENDING' AND a.final_status = 'PENDING' ORDER BY a.created_at DESC");
}
$q->execute();
$requests = $q->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Persetujuan - PEP System</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="container">
        <header>
            <div>
                <h1>Antrean Approval (L<?= $user_level ?>)</h1>
                <p style="color: var(--text-muted); font-size: 0.875rem;">Harap tinjau permintaan perubahan data di
                    bawah ini.</p>
            </div>
            <a href="index.php" class="btn btn-warning">Kembali ke Dashboard</a>
        </header>

        <div class="card">
            <?php if (empty($requests)): ?>
                <div style="padding: 3rem; text-align: center; color: var(--text-muted);">
                    🎉 Tidak ada permintaan yang menunggu approval Anda.
                </div>
            <?php else: ?>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Tipe</th>
                                <th>Request By</th>
                                <th>Detail Perubahan</th>
                                <th>Waktu</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($requests as $req): ?>
                                <?php
                                $old = json_decode($req['old_data'], true);
                                $new = json_decode($req['new_data'], true);
                                ?>
                                <tr>
                                    <td>
                                        <span class="badge <?= $req['type'] == 'EDIT' ? 'badge-both' : 'badge-danger' ?>">
                                            <?= $req['type'] ?>
                                        </span>
                                    </td>
                                    <td><?= htmlspecialchars($req['requester_name']) ?></td>
                                    <td>
                                        <?php if ($req['type'] == 'EDIT'): ?>
                                            <div style="font-size: 0.85rem; line-height: 1.6;">
                                                <strong style="color: var(--primary);">📋 Target:
                                                    <?= htmlspecialchars($old['nama_cadeb']) ?></strong>
                                                <div
                                                    style="margin-top: 8px; padding: 8px; background: rgba(76, 175, 80, 0.05); border-left: 3px solid var(--success); border-radius: 4px;">
                                                    <strong style="color: var(--success);">✏️ Perubahan Data:</strong>
                                                    <table
                                                        style="width: 100%; margin-top: 6px; font-size: 0.8rem; border-collapse: collapse;">
                                                        <?php
                                                        $field_labels = [
                                                            'nama_cadeb' => 'Nama Debitur',
                                                            'no_identitas' => 'No. KTP',
                                                            'nama_pasangan' => 'Nama Pasangan',
                                                            'no_identitas_pasangan' => 'No. KTP Pasangan',
                                                            'keterangan_pep' => 'Keterangan PEP',
                                                            'go_live' => 'Go Live'
                                                        ];

                                                        $has_changes = false;
                                                        foreach ($field_labels as $field => $label) {
                                                            if (isset($old[$field]) && isset($new[$field]) && $old[$field] != $new[$field]) {
                                                                $has_changes = true;
                                                                echo "<tr style='border-bottom: 1px solid rgba(0,0,0,0.05);'>";
                                                                echo "<td style='padding: 4px 8px 4px 0; font-weight: 600; color: var(--text-primary); width: 35%;'>{$label}:</td>";
                                                                echo "<td style='padding: 4px 4px; color: var(--danger); text-decoration: line-through; opacity: 0.7;'>" . htmlspecialchars($old[$field]) . "</td>";
                                                                echo "<td style='padding: 4px 4px; text-align: center; color: var(--success);'>→</td>";
                                                                echo "<td style='padding: 4px 4px; color: var(--success); font-weight: 600;'>" . htmlspecialchars($new[$field]) . "</td>";
                                                                echo "</tr>";
                                                            }
                                                        }

                                                        if (!$has_changes) {
                                                            echo "<tr><td colspan='4' style='padding: 4px; color: var(--text-muted); font-style: italic;'>Tidak ada perubahan terdeteksi</td></tr>";
                                                        }
                                                        ?>
                                                    </table>
                                                </div>
                                            </div>
                                        <?php else: ?>
                                            <div style="font-size: 0.85rem; line-height: 1.6;">
                                                <strong style="color: var(--primary);">📋 Target:
                                                    <?= htmlspecialchars($old['nama_cadeb']) ?></strong>
                                                <div
                                                    style="margin-top: 8px; padding: 8px; background: rgba(244, 67, 54, 0.05); border-left: 3px solid var(--danger); border-radius: 4px;">
                                                    <strong style="color: var(--danger);">🗑️ Permohonan Hapus Permanen</strong>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td style="font-size: 0.8rem; color: var(--text-muted);"><?= $req['created_at'] ?></td>
                                    <td>
                                        <form method="POST" style="display: flex; gap: 0.5rem; align-items: center;">
                                            <input type="hidden" name="request_id" value="<?= $req['id'] ?>">
                                            <input type="text" name="notes" placeholder="Catatan (Opsional)"
                                                class="form-control" style="font-size: 0.75rem; padding: 0.4rem; width: 150px;">
                                            <button type="submit" name="action" value="APPROVED" class="btn btn-primary"
                                                style="padding: 0.4rem 0.8rem; font-size: 0.75rem; background: var(--success);">Approve</button>
                                            <button type="submit" name="action" value="REJECTED" class="btn btn-danger"
                                                style="padding: 0.4rem 0.8rem; font-size: 0.75rem;">Reject</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>