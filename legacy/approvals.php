<?php
require_once 'config/db_dtot.php';
include 'layout/header.php';

// Only Lv 2, 3, 4 can access
if ($_SESSION['role_level'] < 2) {
    echo "<div class='alert alert-danger'>Akses ditolak. Anda tidak memiliki izin untuk halaman ini.</div>";
    include 'layout/footer.php';
    exit;
}

// Logic: Process Approval
if (isset($_POST['action']) && isset($_POST['request_id'])) {
    $action = $_POST['action'];
    $req_id = $_POST['request_id'];
    $role = $_SESSION['role_level'];

    try {
        $stmtReq = $pdo->prepare("SELECT * FROM change_requests WHERE id = ?");
        $stmtReq->execute([$req_id]);
        $request = $stmtReq->fetch();

        if ($request) {
            if ($action === 'approve') {
                if ($role == 2) { // Supervisor
                    // Forward to Manager
                    $stmtUpd = $pdo->prepare("UPDATE change_requests SET status = 'PENDING_MANAGER', approver_id = ? WHERE id = ?");
                    $stmtUpd->execute([$_SESSION['user_id'], $req_id]);
                    $success_msg = "Permintaan disetujui dan diteruskan ke Manager.";
                } elseif ($role == 3 || $role == 4) { // Manager / Admin
                    // Final Approval
                    $data = json_decode($request['data_json'], true);

                    if ($request['request_type'] === 'ADD') {
                        // Should not happen for Add, but handled just in case
                        $sql = "INSERT INTO terduga (nama, terduga_type, kode_densus, tempat_lahir, tanggal_lahir, wn_asal_negara, deskripsi, alamat, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";
                        $stmt = $pdo->prepare($sql);
                        $stmt->execute(array_values($data));
                    } elseif ($request['request_type'] === 'EDIT') {
                        $sql = "UPDATE terduga SET nama=?, terduga_type=?, kode_densus=?, tempat_lahir=?, tanggal_lahir=?, wn_asal_negara=?, deskripsi=?, alamat=?, is_pending=0 WHERE id=?";
                        $stmt = $pdo->prepare($sql);
                        $stmt->execute([
                            $data['nama'],
                            $data['terduga_type'],
                            $data['kode_densus'],
                            $data['tempat_lahir'],
                            $data['tanggal_lahir'],
                            $data['wn_asal_negara'],
                            $data['deskripsi'],
                            $data['alamat'],
                            $request['target_id']
                        ]);
                    } elseif ($request['request_type'] === 'DELETE') {
                        $stmt = $pdo->prepare("UPDATE terduga SET deleted_at = NOW(), is_pending=0 WHERE id = ?");
                        $stmt->execute([$request['target_id']]);
                    }

                    // Update Request Status
                    $stmtEnd = $pdo->prepare("UPDATE change_requests SET status = 'APPROVED', approver_id = ?, processed_at = NOW() WHERE id = ?");
                    $stmtEnd->execute([$_SESSION['user_id'], $req_id]);
                    $success_msg = "Permintaan telah disetujui sepenuhnya.";
                }
            } else {
                // Reject
                if ($request['target_id']) {
                    $stmtClear = $pdo->prepare("UPDATE terduga SET is_pending = 0 WHERE id = ?");
                    $stmtClear->execute([$request['target_id']]);
                }
                $stmtEnd = $pdo->prepare("UPDATE change_requests SET status = 'REJECTED', approver_id = ?, processed_at = NOW() WHERE id = ?");
                $stmtEnd->execute([$_SESSION['user_id'], $req_id]);
                $success_msg = "Permintaan ditolak.";
            }
        }
    } catch (Exception $e) {
        $error_msg = "Error: " . $e->getMessage();
    }
}

// Fetch Pending Requests based on Role
$status_filter = "";
if ($_SESSION['role_level'] == 2) {
    $status_filter = "cr.status = 'PENDING_SPV'";
} elseif ($_SESSION['role_level'] == 3) {
    $status_filter = "cr.status = 'PENDING_MANAGER'";
} else {
    $status_filter = "cr.status IN ('PENDING_SPV', 'PENDING_MANAGER')"; // Admin sees all
}

$sql = "SELECT cr.*, u.nama_lengkap as requester_name, t.nama as target_name, 
        t.nama as t_nama, t.terduga_type as t_terduga_type, t.kode_densus as t_kode_densus, t.tempat_lahir as t_tempat_lahir, 
        t.tanggal_lahir as t_tanggal_lahir, t.wn_asal_negara as t_wn_asal_negara, t.deskripsi as t_deskripsi, t.alamat as t_alamat
        FROM change_requests cr 
        JOIN cadeb_db.users u ON cr.requester_id = u.id 
        LEFT JOIN terduga t ON cr.target_id = t.id 
        WHERE $status_filter 
        ORDER BY cr.created_at ASC";
$stmtRequests = $pdo->query($sql);
$requests = $stmtRequests->fetchAll();
?>

<div class="dashboard-header" style="margin-bottom: 2rem;">
    <h2 style="font-weight: 700; color: var(--primary-color);">Pending Approvals</h2>
    <p style="color: var(--text-secondary); font-size: 0.9rem;">
        <?php if ($_SESSION['role_level'] == 2)
            echo "Review permintaan dari Staf sebelum diteruskan ke Manager."; ?>
        <?php if ($_SESSION['role_level'] == 3)
            echo "Review permintaan final yang telah disetujui Supervisor."; ?>
        <?php if ($_SESSION['role_level'] == 4)
            echo "Review semua permintaan pending."; ?>
    </p>
</div>

<?php if (isset($success_msg)): ?>
    <div
        style="background: rgba(28, 200, 138, 0.1); color: #1cc88a; padding: 1rem; border-radius: 10px; margin-bottom: 2rem; border: 1px solid rgba(28, 200, 138, 0.2);">
        <i class="fas fa-check-circle"></i> <?php echo $success_msg; ?>
    </div>
<?php endif; ?>

<div class="data-table-container">
    <table class="data-table">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Pengaju</th>
                <th>Tipe Aksi</th>
                <th>Status</th>
                <th>Subjek</th>
                <th>Detail Perubahan</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($requests)): ?>
                <tr>
                    <td colspan="6" style="text-align: center; padding: 3rem; color: var(--text-secondary);">
                        <i class="fas fa-inbox"
                            style="font-size: 2rem; display: block; margin-bottom: 10px; opacity: 0.3;"></i>
                        Tidak ada permintaan pending untuk Anda.
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($requests as $row): ?>
                    <tr>
                        <td><?php echo date('d/m/Y H:i', strtotime($row['created_at'])); ?></td>
                        <td><strong><?php echo htmlspecialchars($row['requester_name']); ?></strong></td>
                        <td>
                            <?php
                            $type_badges = [
                                'ADD' => ['label' => 'TAMBAH', 'color' => '#4e73df'],
                                'EDIT' => ['label' => 'UPDATE', 'color' => '#f6c23e'],
                                'DELETE' => ['label' => 'HAPUS', 'color' => '#e74a3b']
                            ];
                            $badge = $type_badges[$row['request_type']];
                            ?>
                            <span class="badge"
                                style="background: <?php echo $badge['color']; ?>; color: #fff; font-size: 0.7rem;"><?php echo $badge['label']; ?></span>
                        </td>
                        <td>
                            <?php if ($row['status'] == 'PENDING_SPV'): ?>
                                <span class="badge" style="background: #f6c23e; color: #fff;">Menunggu SPV</span>
                            <?php elseif ($row['status'] == 'PENDING_MANAGER'): ?>
                                <span class="badge" style="background: #36b9cc; color: #fff;">Menunggu Manager</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($row['target_name'] ?: 'Data Baru'); ?></td>
                        <td style="font-size: 0.8rem; line-height: 1.4;">
                            <?php
                            $dataNew = json_decode($row['data_json'], true);
                            if ($row['request_type'] == 'ADD') {
                                echo "Menambah data baru:<br><strong>" . htmlspecialchars($dataNew['nama'] ?? '') . "</strong>";
                            } elseif ($row['request_type'] == 'DELETE') {
                                echo "Permintaan hapus data.";
                            } elseif ($row['request_type'] == 'EDIT') {
                                $changes = [];
                                $fields = [
                                    'nama' => 'Nama',
                                    'terduga_type' => 'Tipe',
                                    'kode_densus' => 'Kode Densus',
                                    'tempat_lahir' => 'Tempat Lahir',
                                    'tanggal_lahir' => 'Tanggal Lahir',
                                    'wn_asal_negara' => 'WN/Negara',
                                    'deskripsi' => 'Deskripsi',
                                    'alamat' => 'Alamat'
                                ];
                                foreach ($fields as $key => $label) {
                                    if (array_key_exists($key, $dataNew) && $dataNew[$key] != $row['t_' . $key]) {
                                        $oldVal = htmlspecialchars($row['t_' . $key] ?: '-');
                                        $newVal = htmlspecialchars($dataNew[$key] ?: '-');
                                        $changes[] = "<strong>$label:</strong> <s>$oldVal</s> &rarr; <span style='color: #1cc88a; font-weight: 600;'>$newVal</span>";
                                    }
                                }
                                if (empty($changes)) {
                                    echo "<em>Tidak ada perubahan pada kolom.</em>";
                                } else {
                                    echo implode("<br>", $changes);
                                }
                            }
                            ?>
                        </td>
                        <td>
                            <div style="display: flex; gap: 10px;">
                                <form method="POST" style="margin: 0;">
                                    <input type="hidden" name="request_id" value="<?php echo $row['id']; ?>">
                                    <button type="submit" name="action" value="approve" class="btn-upload"
                                        style="margin: 0; padding: 5px 12px; font-size: 0.8rem; background: #1cc88a; border-radius: 5px;"
                                        onclick="return confirm('Setujui permintaan ini?')">
                                        <?php echo ($_SESSION['role_level'] == 2) ? 'Teruskan' : 'Approve'; ?>
                                    </button>
                                    <button type="submit" name="action" value="reject" class="btn-upload"
                                        style="margin: 0; padding: 5px 12px; font-size: 0.8rem; background: #e74a3b; border-radius: 5px;"
                                        onclick="return confirm('Tolak permintaan ini?')">
                                        Reject
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include 'layout/footer.php'; ?>