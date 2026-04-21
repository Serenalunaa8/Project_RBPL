<?php
session_start();
include "../koneksi.php";

// Proteksi Role
if (!isset($_SESSION['role']) || $_SESSION['role'] != "koordinator") {
    header("Location: ../login.php");
    exit;
}

// Ambil statistik dinamis dari database
$stats_query = "SELECT 
    COUNT(*) as total_laporan,
    SUM(CASE WHEN status = 'Menunggu' THEN 1 ELSE 0 END) as menunggu,
    SUM(CASE WHEN status = 'Tervalidasi' THEN 1 ELSE 0 END) as tervalidasi
FROM laporan_harian";

$stats_result = mysqli_query($koneksi, $stats_query);
$stats = mysqli_fetch_assoc($stats_result);

$total_laporan = $stats['total_laporan'] ?? 0;
$menunggu = $stats['menunggu'] ?? 0;
$tervalidasi = $stats['tervalidasi'] ?? 0;

// Ambil laporan harian terbaru
$laporan_query = "SELECT lh.*, p.username as pengawas_nama
FROM laporan_harian lh
LEFT JOIN users p ON lh.pengawas_id = p.id
ORDER BY lh.tanggal DESC
LIMIT 10";

$laporan_result = mysqli_query($koneksi, $laporan_query);
$laporan_data = mysqli_fetch_all($laporan_result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Koordinator Pengawas | CV Cipta Manunggal</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #111111;
            color: #ffffff;
        }

        .dashboard-container {
            display: flex;
            min-height: 100vh;
        }

        /* ── SIDEBAR ── */
        .sidebar {
            width: 260px;
            background: #1a1a1a;
            padding: 30px 20px;
            border-right: 1px solid rgba(255,255,255,0.05);
            position: sticky;
            top: 0;
            height: 100vh;
            overflow-y: auto;
        }

        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 40px;
        }

        .logo-arch {
            width: 38px;
            height: 38px;
            stroke: #ffc107;
            stroke-width: 4;
            fill: none;
        }

        .sidebar h2 {
            font-size: 16px;
        }

        .sidebar span {
            color: #ffc107;
        }

        .sidebar nav {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .sidebar nav a {
            text-decoration: none;
            color: #cccccc;
            padding: 10px;
            border-radius: 6px;
            transition: 0.3s;
            font-size: 14px;
        }

        .sidebar nav a:hover,
        .sidebar nav a.active {
            background: #ffc107;
            color: #111;
        }

        .logout {
            margin-top: 30px;
            background: #2a2a2a;
        }

        /* ── MAIN ── */
        .main-content {
            flex: 1;
            padding: 50px;
            overflow-y: auto;
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
        }

        .topbar h1 {
            font-size: 28px;
            font-weight: 700;
        }

        .topbar p {
            font-size: 14px;
            color: #888;
            margin-top: 4px;
        }

        .role-badge {
            background: #ffc107;
            color: #111;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        /* ── STATS ── */
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }

        .stat-card {
            background: #1c1c1c;
            padding: 28px;
            border-radius: 12px;
            border: 1px solid rgba(255,255,255,0.05);
            transition: 0.3s;
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, #ffc107, #ffb300);
        }

        .stat-card:hover {
            border-color: #ffc107;
            transform: translateY(-4px);
            box-shadow: 0 8px 24px rgba(255, 193, 7, 0.15);
        }

        .stat-card.warning::before {
            background: linear-gradient(90deg, #ff9800, #ff7500);
        }

        .stat-card.success::before {
            background: linear-gradient(90deg, #22c55e, #16a34a);
        }

        .stat-card h3 {
            font-size: 36px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .stat-card h3 {
            color: #ffc107;
        }

        .stat-card.warning h3 {
            color: #ff9800;
        }

        .stat-card.success h3 {
            color: #22c55e;
        }

        .stat-card p {
            font-size: 13px;
            color: #888;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* ── GRID SECTION ── */
        .grid-section {
            display: grid;
            grid-template-columns: 1fr;
            gap: 24px;
            margin-bottom: 40px;
        }

        /* ── TABLE CARD ── */
        .activity-card {
            background: #1c1c1c;
            border: 1px solid rgba(255,255,255,0.05);
            border-radius: 14px;
            padding: 28px;
            overflow: hidden;
        }

        .activity-card h3 {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .activity-card h3::before {
            content: '';
            display: inline-block;
            width: 3px;
            height: 16px;
            background: #ffc107;
            border-radius: 2px;
        }

        .table-wrapper {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background: #0f0f0f;
        }

        th {
            text-align: left;
            color: #aaa;
            padding: 14px 16px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid rgba(255,255,255,0.05);
        }

        td {
            padding: 14px 16px;
            border-bottom: 1px solid rgba(255,255,255,0.03);
            font-size: 13px;
        }

        tbody tr:hover {
            background: rgba(255, 193, 7, 0.02);
        }

        /* ── STATUS BADGE ── */
        .badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .badge-menunggu {
            background: rgba(255, 152, 0, 0.2);
            color: #ff9800;
        }

        .badge-tervalidasi {
            background: rgba(34, 197, 94, 0.2);
            color: #22c55e;
        }

        /* ── BUTTON ── */
        .btn-tinjau {
            background: transparent;
            color: #60a5fa;
            padding: 6px 12px;
            border-radius: 6px;
            border: 1px solid #60a5fa;
            cursor: pointer;
            font-weight: 500;
            font-size: 11px;
            transition: 0.3s;
            font-family: 'Inter', sans-serif;
            text-decoration: none;
            display: inline-block;
        }

        .btn-tinjau:hover {
            background: rgba(96, 165, 250, 0.1);
            border-color: #8bb9fc;
            color: #8bb9fc;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #666;
        }

        /* ── RESPONSIVE ── */
        @media (max-width: 1024px) {
            .sidebar {
                width: 220px;
            }

            .main-content {
                padding: 30px;
            }

            .stats {
                grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            }
        }

        @media (max-width: 768px) {
            .dashboard-container {
                flex-direction: column;
            }

            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
                border-right: none;
                border-bottom: 1px solid rgba(255,255,255,0.05);
                padding: 20px;
            }

            .sidebar nav {
                flex-direction: row;
                gap: 10px;
                flex-wrap: wrap;
            }

            .sidebar nav a {
                flex: 1;
                min-width: 80px;
                text-align: center;
            }

            .main-content {
                padding: 20px;
            }

            .topbar {
                flex-direction: column;
                gap: 16px;
                align-items: flex-start;
            }

            .stats {
                grid-template-columns: repeat(2, 1fr);
                gap: 12px;
            }

            .stat-card {
                padding: 18px;
            }

            .stat-card h3 {
                font-size: 26px;
            }

            table {
                font-size: 12px;
            }

            th, td {
                padding: 10px;
            }
        }
    </style>
</head>
<body>

<div class="dashboard-container">
    <!-- SIDEBAR -->
    <aside class="sidebar">
        <div class="sidebar-brand">
            <svg viewBox="0 0 120 120" class="logo-arch">
                <rect x="10" y="10" width="100" height="100"/>
                <path d="M35 80 V40 H60"/>
                <path d="M60 40 L75 60 L90 40 V80"/>
            </svg>
            <h2>CIPTA<span>MANUNGGAL</span></h2>
        </div>

        <nav>
            <a href="koordinator_pengawas.php" class="active">Dashboard</a>
            <a href="Tinjau_laporan_harian.php">Tinjau Laporan Harian</a>
            <a href="susun_laporan_mingguan.php">Susun Laporan Mingguan</a>
            <a href="riwayat_laporan_mingguan.php">Riwayat Laporan</a>
            <a href="../logout.php" class="logout">Logout</a>
        </nav>
    </aside>

    <!-- MAIN -->
    <main class="main-content">

        <!-- TOPBAR -->
        <header class="topbar">
            <div>
                <h1>Dashboard Koordinator</h1>
                <p>Pantau laporan harian dan verifikasi progres pekerjaan</p>
            </div>
            <div class="role-badge">KOORDINATOR PENGAWAS</div>
        </header>

        <!-- STATS -->
        <section class="stats">
            <div class="stat-card">
                <h3><?= $total_laporan ?></h3>
                <p>Total Laporan Harian</p>
            </div>
            <div class="stat-card warning">
                <h3><?= $menunggu ?></h3>
                <p>Menunggu Pengesahan</p>
            </div>
            <div class="stat-card success">
                <h3><?= $tervalidasi ?></h3>
                <p>Tervalidasi</p>
            </div>
        </section>

        <!-- TABLE -->
        <section class="grid-section">
            <div class="activity-card">
                <h3>Laporan Harian Terbaru</h3>

                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Pengawas Lapangan</th>
                                <th>Progres Pekerjaan</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (mysqli_num_rows($laporan_result) > 0): ?>
                                <?php foreach ($laporan_data as $row): 
                                    $status_class = (strtolower($row['status']) === 'menunggu') ? 'badge-menunggu' : 'badge-tervalidasi';
                                ?>
                                <tr>
                                    <td><?= date('d M Y', strtotime($row['tanggal'])) ?></td>
                                    <td><?= htmlspecialchars($row['pengawas_nama'] ?? '-') ?></td>
                                    <td><?= htmlspecialchars($row['progres_pekerjaan'] ?? '-') ?></td>
                                    <td>
                                        <span class="badge <?= $status_class ?>">
                                            <?= htmlspecialchars($row['status']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="Tinjau_laporan_harian.php?id=<?= $row['id'] ?>" class="btn-tinjau">
                                            Tinjau
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="empty-state">
                                        <strong>Belum ada laporan harian</strong>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

            </div>
        </section>

    </main>
</div>

</body>
</html>