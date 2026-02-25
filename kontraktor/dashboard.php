<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != "kontraktor") {
    header("Location: ../login.php");
    exit;
}

require_once '../koneksi.php';

if (!isset($_SESSION['kontraktor_id'])) {
    die("Session kontraktor tidak ditemukan. Silakan login ulang.");
}

$kontraktor_id = $_SESSION['kontraktor_id'];

// Ambil statistik
$stats_query = "SELECT 
                   COUNT(*) as total,
                   SUM(CASE WHEN status LIKE '%Disetujui%' OR status LIKE '%Approved%' THEN 1 ELSE 0 END) as disetujui,
                   SUM(CASE WHEN status LIKE '%Review%' THEN 1 ELSE 0 END) as review,
                   SUM(CASE WHEN status LIKE '%Ditolak%' OR status LIKE '%Reject%' THEN 1 ELSE 0 END) as ditolak,
                   SUM(CASE WHEN status NOT LIKE '%Disetujui%' AND status NOT LIKE '%Approved%' AND status NOT LIKE '%Review%' AND status NOT LIKE '%Ditolak%' AND status NOT LIKE '%Reject%' THEN 1 ELSE 0 END) as pending
                FROM form_izin_pekerjaan 
                WHERE kontraktor_id = ?";

$stmt = mysqli_prepare($koneksi, $stats_query);
mysqli_stmt_bind_param($stmt, "i", $kontraktor_id);
mysqli_stmt_execute($stmt);
$stats_result = mysqli_stmt_get_result($stmt);
$stats = mysqli_fetch_assoc($stats_result);
mysqli_stmt_close($stmt);

$total = $stats['total'] ?? 0;
$disetujui = $stats['disetujui'] ?? 0;
$review = $stats['review'] ?? 0;
$ditolak = $stats['ditolak'] ?? 0;
$pending = $stats['pending'] ?? 0;

// Ambil 5 izin terbaru untuk activity
$activity_query = "SELECT id, jenis_pekerjaan, status, created_at, tanggal_mulai 
                   FROM form_izin_pekerjaan 
                   WHERE kontraktor_id = ? 
                   ORDER BY created_at DESC 
                   LIMIT 5";

$stmt = mysqli_prepare($koneksi, $activity_query);
mysqli_stmt_bind_param($stmt, "i", $kontraktor_id);
mysqli_stmt_execute($stmt);
$activity_result = mysqli_stmt_get_result($stmt);
$activities = mysqli_fetch_all($activity_result, MYSQLI_ASSOC);
mysqli_stmt_close($stmt);

// Ambil data last 30 days untuk chart trend
$trend_query = "SELECT DATE_FORMAT(created_at, '%Y-%m-%d') as tanggal,
                       SUM(CASE WHEN status LIKE '%Disetujui%' OR status LIKE '%Approved%' THEN 1 ELSE 0 END) as disetujui,
                       SUM(CASE WHEN status LIKE '%Ditolak%' OR status LIKE '%Reject%' THEN 1 ELSE 0 END) as ditolak,
                       COUNT(*) as total
                FROM form_izin_pekerjaan 
                WHERE kontraktor_id = ? AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                GROUP BY DATE_FORMAT(created_at, '%Y-%m-%d')
                ORDER BY tanggal ASC";

$stmt = mysqli_prepare($koneksi, $trend_query);
mysqli_stmt_bind_param($stmt, "i", $kontraktor_id);
mysqli_stmt_execute($stmt);
$trend_result = mysqli_stmt_get_result($stmt);
$trend_data = mysqli_fetch_all($trend_result, MYSQLI_ASSOC);
mysqli_stmt_close($stmt);

// Prepare chart data
$chart_dates = [];
$chart_disetujui = [];
$chart_ditolak = [];
$chart_total = [];

foreach ($trend_data as $row) {
    $chart_dates[] = date('d M', strtotime($row['tanggal']));
    $chart_disetujui[] = (int)$row['disetujui'];
    $chart_ditolak[] = (int)$row['ditolak'];
    $chart_total[] = (int)$row['total'];
}

// Jika belum ada data, buat array dummy
if (empty($chart_dates)) {
    $chart_dates = ['Tidak ada data'];
    $chart_disetujui = [0];
    $chart_ditolak = [0];
    $chart_total = [0];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Kontraktor | CV Cipta Manunggal Konsultan</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

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

        .sidebar h2 { font-size: 16px; }
        .sidebar span { color: #ffc107; }

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

        .topbar h1 { font-size: 28px; font-weight: 700; }
        .topbar p  { font-size: 14px; color: #888; margin-top: 4px; }

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
            background: linearGradient(90deg, #ffc107, #ffb300);
        }

        .stat-card:hover {
            border-color: #ffc107;
            transform: translateY(-4px);
            box-shadow: 0 8px 24px rgba(255, 193, 7, 0.15);
        }

        .stat-card h3 {
            font-size: 36px;
            font-weight: 700;
            color: #ffc107;
            margin-bottom: 8px;
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
            grid-template-columns: 1.5fr 1fr;
            gap: 24px;
            margin-bottom: 40px;
        }

        .chart-card,
        .activity-card {
            background: #1c1c1c;
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 14px;
            padding: 28px;
        }

        .chart-card h3,
        .activity-card h3 {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .chart-card h3::before,
        .activity-card h3::before {
            content: '';
            display: inline-block;
            width: 3px;
            height: 16px;
            background: #ffc107;
            border-radius: 2px;
        }

        .chart-container {
            position: relative;
            height: 280px;
        }

        /* ── ACTIVITY ── */
        .activity-list {
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .activity-item {
            background: rgba(255, 193, 7, 0.05);
            padding: 14px;
            border-radius: 8px;
            border-left: 3px solid #ffc107;
            transition: 0.3s;
        }

        .activity-item:hover {
            background: rgba(255, 193, 7, 0.1);
            transform: translateX(4px);
        }

        .activity-item-title {
            font-size: 13px;
            font-weight: 600;
            color: #e0e0e0;
        }

        .activity-item-date {
            font-size: 11px;
            color: #666;
            margin-top: 4px;
        }

        .activity-item-status {
            display: inline-block;
            margin-top: 6px;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .status-approved { background: rgba(34, 197, 94, 0.2); color: #22c55e; }
        .status-pending  { background: rgba(255, 193, 7, 0.2); color: #ffc107; }
        .status-review   { background: rgba(59, 130, 246, 0.2); color: #60a5fa; }
        .status-rejected { background: rgba(239, 68, 68, 0.2); color: #ef4444; }

        .empty-state {
            text-align: center;
            padding: 40px;
            color: #666;
        }

        /* ── BOTTOM SECTION ── */
        .bottom-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
        }

        .breakdown-card {
            background: #1c1c1c;
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 14px;
            padding: 28px;
        }

        .breakdown-card h3 {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .breakdown-card h3::before {
            content: '';
            display: inline-block;
            width: 3px;
            height: 16px;
            background: #ffc107;
            border-radius: 2px;
        }

        .breakdown-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid rgba(255,255,255,0.05);
        }

        .breakdown-item:last-child {
            border-bottom: none;
        }

        .breakdown-label {
            font-size: 13px;
            color: #aaa;
        }

        .breakdown-value {
            font-size: 18px;
            font-weight: 700;
            color: #ffc107;
        }

        @media (max-width: 1024px) {
            .grid-section {
                grid-template-columns: 1fr;
            }
            .bottom-section {
                grid-template-columns: 1fr;
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
            }
            .main-content {
                padding: 30px 20px;
            }
            .stats {
                grid-template-columns: repeat(2, 1fr);
            }
            .topbar {
                flex-direction: column;
                gap: 15px;
                align-items: flex-start;
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
            <a href="./dashboard.php" class="active">Dashboard</a>
            <a href="./AjukanIzin.php">Ajukan Izin</a>
            <a href="./LihatStatus.php">Status Izin</a>
            <a href="#">Riwayat</a>
            <a href="../logout.php" class="logout">Logout</a>
        </nav>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="main-content">

        <header class="topbar">
            <div>
                <h1>Dashboard Kontraktor</h1>
                <p>Selamat datang, <strong><?php echo htmlspecialchars($_SESSION['username'] ?? 'User'); ?></strong></p>
            </div>
            <div class="role-badge">KONTRAKTOR</div>
        </header>

        <!-- STATISTICS -->
        <section class="stats">
            <div class="stat-card">
                <h3><?php echo $total; ?></h3>
                <p>Total Pengajuan</p>
            </div>

            <div class="stat-card">
                <h3><?php echo $pending; ?></h3>
                <p>Menunggu Review</p>
            </div>

            <div class="stat-card">
                <h3><?php echo $review; ?></h3>
                <p>Dalam Review</p>
            </div>

            <div class="stat-card">
                <h3><?php echo $disetujui; ?></h3>
                <p>Disetujui</p>
            </div>

            <div class="stat-card">
                <h3><?php echo $ditolak; ?></h3>
                <p>Ditolak</p>
            </div>
        </section>

        <!-- GRAPH + ACTIVITY -->
        <section class="grid-section">
            <div class="chart-card">
                <h3>Trend 30 Hari Terakhir</h3>
                <div class="chart-container">
                    <canvas id="trendChart"></canvas>
                </div>
            </div>

            <div class="activity-card">
                <h3>Aktivitas Terbaru</h3>
                <?php if (!empty($activities)): ?>
                    <div class="activity-list">
                        <?php foreach ($activities as $act):
                            $status_raw = strtolower($act['status'] ?? 'pending');
                            if (str_contains($status_raw, 'setuju') || str_contains($status_raw, 'approved')) {
                                $status_class = 'status-approved';
                                $status_text = 'Disetujui';
                            } elseif (str_contains($status_raw, 'tolak') || str_contains($status_raw, 'reject')) {
                                $status_class = 'status-rejected';
                                $status_text = 'Ditolak';
                            } elseif (str_contains($status_raw, 'review')) {
                                $status_class = 'status-review';
                                $status_text = 'Review';
                            } else {
                                $status_class = 'status-pending';
                                $status_text = 'Menunggu';
                            }
                            $created = date('d M Y, H:i', strtotime($act['created_at']));
                        ?>
                            <div class="activity-item">
                                <div class="activity-item-title"><?php echo htmlspecialchars($act['jenis_pekerjaan']); ?></div>
                                <div class="activity-item-date">Diajukan: <?php echo $created; ?></div>
                                <span class="activity-item-status <?php echo $status_class; ?>"><?php echo $status_text; ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <p>Belum ada pengajuan izin</p>
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <!-- BREAKDOWN -->
        <section class="bottom-section">
            <div class="breakdown-card">
                <h3>Breakdown Status</h3>
                <div class="breakdown-item">
                    <span class="breakdown-label">Disetujui</span>
                    <span class="breakdown-value"><?php echo $disetujui; ?></span>
                </div>
                <div class="breakdown-item">
                    <span class="breakdown-label">Dalam Review</span>
                    <span class="breakdown-value"><?php echo $review; ?></span>
                </div>
                <div class="breakdown-item">
                    <span class="breakdown-label">Menunggu Review</span>
                    <span class="breakdown-value"><?php echo $pending; ?></span>
                </div>
                <div class="breakdown-item">
                    <span class="breakdown-label">Ditolak</span>
                    <span class="breakdown-value"><?php echo $ditolak; ?></span>
                </div>
            </div>

            <div class="breakdown-card">
                <h3>Statistik Cepat</h3>
                <div class="breakdown-item">
                    <span class="breakdown-label">Tingkat Persetujuan</span>
                    <span class="breakdown-value"><?php echo $total > 0 ? round(($disetujui / $total) * 100) : 0; ?>%</span>
                </div>
                <div class="breakdown-item">
                    <span class="breakdown-label">Dalam Proses</span>
                    <span class="breakdown-value"><?php echo $pending + $review; ?></span>
                </div>
                <div class="breakdown-item">
                    <span class="breakdown-label">Rata-rata per Bulan</span>
                    <span class="breakdown-value"><?php echo $total > 0 ? round($total / 1) : 0; ?></span>
                </div>
                <div class="breakdown-item">
                    <span class="breakdown-label">Tingkat Penolakan</span>
                    <span class="breakdown-value"><?php echo $total > 0 ? round(($ditolak / $total) * 100) : 0; ?>%</span>
                </div>
            </div>
        </section>

    </main>
</div>

<script>
    // Trend Chart Data
    const trendCtx = document.getElementById('trendChart');
    new Chart(trendCtx, {
        type: 'line',
        data: {
            labels: <?php echo json_encode($chart_dates); ?>,
            datasets: [
                {
                    label: 'Total Pengajuan',
                    data: <?php echo json_encode($chart_total); ?>,
                    borderColor: '#ffc107',
                    backgroundColor: 'rgba(255, 193, 7, 0.1)',
                    tension: 0.4,
                    fill: true,
                    borderWidth: 2,
                    pointBackgroundColor: '#ffc107',
                    pointBorderColor: '#111',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6
                },
                {
                    label: 'Disetujui',
                    data: <?php echo json_encode($chart_disetujui); ?>,
                    borderColor: '#22c55e',
                    backgroundColor: 'rgba(34, 197, 94, 0.1)',
                    tension: 0.4,
                    fill: true,
                    borderWidth: 2,
                    pointBackgroundColor: '#22c55e',
                    pointBorderColor: '#111',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6
                },
                {
                    label: 'Ditolak',
                    data: <?php echo json_encode($chart_ditolak); ?>,
                    borderColor: '#ef4444',
                    backgroundColor: 'rgba(239, 68, 68, 0.1)',
                    tension: 0.4,
                    fill: true,
                    borderWidth: 2,
                    pointBackgroundColor: '#ef4444',
                    pointBorderColor: '#111',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    labels: {
                        color: '#aaa',
                        font: { size: 12 },
                        usePointStyle: true
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(255,255,255,0.05)' },
                    ticks: { color: '#666' }
                },
                x: {
                    grid: { color: 'rgba(255,255,255,0.05)' },
                    ticks: { color: '#666', font: { size: 11 } }
                }
            }
        }
    });
</script>

</body>
</html>