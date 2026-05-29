<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== "teamleader") {
    header("Location: ../login.php");
    exit;
}

include "../koneksi.php";
$username = $_SESSION['username'];
$message = $_SESSION['message'] ?? '';
unset($_SESSION['message']);
$error = $_SESSION['error'] ?? '';
unset($_SESSION['error']);

$reports_result = false;
if (!isset($koneksi) || !$koneksi) {
    $error = 'Koneksi database gagal. Silakan periksa konfigurasi koneksi atau nyalakan server MySQL.';
} else {
    $reports_query = "SELECT lb.*, u.username AS koordinator_name
FROM laporan_bulanan lb
LEFT JOIN users u ON lb.koordinator_id = u.id
ORDER BY lb.created_at DESC";
    $reports_result = mysqli_query($koneksi, $reports_query);
    if ($reports_result === false) {
        $error = 'Query gagal: ' . mysqli_error($koneksi);
    }
}

function statusBadgeClass($status) {
    if ($status === 'Disetujui') return 'badge-approved';
    if ($status === 'Diminta Revisi') return 'badge-revision';
    return 'badge-pending';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Evaluasi Laporan Bulanan | Team Leader</title>
    <link rel="stylesheet" href="asset/teamleader.css">
</head>
<body>
<div class="dashboard-container">
    <aside class="sidebar">
        <div class="sidebar-brand">
            <svg viewBox="0 0 120 120" class="logo-arch">
                <rect x="10" y="10" width="100" height="100"/>
                <path d="M35 80 V40 H60"/>
                <path d="M60 40 L75 60 L90 40 V80"/>
            </svg>
            <div class="brand-name">CIPTA<br><span>MANUNGGAL</span></div>
        </div>
        <nav>
            <a href="teamleader.php">
                <span class="nav-icon">⊞</span> Dashboard
            </a>
            <a href="evaluasi_laporan_bulanan.php" class="active">
                <span class="nav-icon">📊</span> Evaluasi Laporan
            </a>
            <a href="riwayat_laporan_bulanan.php">
                <span class="nav-icon">🗂</span> Riwayat Laporan
            </a>
        </nav>
        <div class="logout-link">
            <a href="../logout.php">
                <span class="nav-icon">↩</span> Logout
            </a>
        </div>
    </aside>
    <main class="main-content">
        <header class="topbar">
            <div class="topbar-left">
                <h1>Evaluasi Laporan Bulanan</h1>
                <p>Kelola evaluasi, setujui laporan bulanan, atau minta revisi ke Koordinator Pengawas.</p>
            </div>
            <span class="role-badge">TEAM LEADER</span>
        </header>

        <?php if ($message): ?>
            <div class="form-section" style="border-color:#22c55e; color:#d1fae5; background:rgba(34,197,94,0.08);">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="form-section" style="border-color:#ef4444; color:#fee2e2; background:rgba(239,68,68,0.08);">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <section class="table-card">
            <div class="table-card-header">
                <h2>Daftar Laporan Bulanan</h2>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Periode</th>
                        <th>Judul</th>
                        <th>Koordinator</th>
                        <th>Status</th>
                        <th>Catatan Evaluasi</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($reports_result && mysqli_num_rows($reports_result) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($reports_result)): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['periode']) ?></td>
                                <td><?= htmlspecialchars($row['judul']) ?></td>
                                <td><?= htmlspecialchars($row['koordinator_name'] ?: '-') ?></td>
                                <td><span class="badge <?= statusBadgeClass($row['status']) ?>"><?= htmlspecialchars($row['status']) ?></span></td>
                                <td><?= nl2br(htmlspecialchars($row['catatan_eval'] ?: '-')) ?></td>
                                <td>
                                    <?php if ($row['status'] !== 'Disetujui'): ?>
                                        <form method="post" action="proses_evaluasi_laporan.php">
                                            <input type="hidden" name="laporan_id" value="<?= $row['id'] ?>">
                                            <textarea name="catatan" placeholder="Catatan untuk koordinator atau ringkas evaluasi..." style="width:100%; min-height:84px; margin-bottom:10px; background:rgba(255,255,255,0.05); border:1px solid rgba(255,255,255,0.08); border-radius:10px; color:#f7f7f7; padding:10px;"></textarea>
                                            <div style="display:flex; gap:8px; flex-wrap:wrap;">
                                                <button type="submit" name="action" value="approve" class="btn btn-primary btn-sm">Setujui</button>
                                                <button type="submit" name="action" value="revision" class="btn btn-secondary btn-sm">Minta Revisi</button>
                                            </div>
                                        </form>
                                    <?php else: ?>
                                        <span style="color: #a3a3a3;">Tidak ada aksi</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align:center;">Belum ada laporan bulanan yang dapat dievaluasi.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>
    </main>
</div>
</body>
</html>
