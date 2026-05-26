<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== "teamleader") {
    header("Location: ../login.php");
    exit;
}

include "../koneksi.php";
$username = $_SESSION['username'];

$archive_query = "SELECT lb.*, u.username AS koordinator_name
FROM laporan_bulanan lb
LEFT JOIN users u ON lb.koordinator_id = u.id
WHERE lb.status = 'Disetujui'
ORDER BY lb.updated_at DESC";
$archive_result = mysqli_query($koneksi, $archive_query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Laporan Bulanan | Team Leader</title>
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
            <a href="evaluasi_laporan_bulanan.php">
                <span class="nav-icon">📊</span> Evaluasi Laporan
            </a>
            <a href="riwayat_laporan_bulanan.php" class="active">
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
                <h1>Riwayat Arsip Laporan Bulanan</h1>
                <p>Daftar laporan yang telah disetujui dan tersimpan sebagai arsip resmi.</p>
            </div>
            <span class="role-badge">TEAM LEADER</span>
        </header>

        <section class="table-card">
            <div class="table-card-header">
                <h2>Arsip Laporan Bulanan Disetujui</h2>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Periode</th>
                        <th>Judul</th>
                        <th>Koordinator</th>
                        <th>Catatan Evaluasi</th>
                        <th>Disetujui Pada</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($archive_result && mysqli_num_rows($archive_result) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($archive_result)): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['periode']) ?></td>
                                <td><?= htmlspecialchars($row['judul']) ?></td>
                                <td><?= htmlspecialchars($row['koordinator_name'] ?: '-') ?></td>
                                <td><?= nl2br(htmlspecialchars($row['catatan_eval'] ?: '-')) ?></td>
                                <td><?= htmlspecialchars(date('d M Y H:i', strtotime($row['updated_at']))) ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" style="text-align:center;">Belum ada laporan bulanan yang disetujui dan diarsipkan.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>
    </main>
</div>
</body>
</html>
