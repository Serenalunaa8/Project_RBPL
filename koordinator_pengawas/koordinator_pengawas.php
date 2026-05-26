<?php
session_start();
require_once "../koneksi.php";
if (!isset($koneksi) || !$koneksi) {
    die("Koneksi gagal: file koneksi tidak ditemukan atau koneksi database bermasalah.");
}

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
$laporan_query = "SELECT lh.*, p.username as kontraktor_nama
FROM laporan_harian lh
LEFT JOIN users p ON lh.kontraktor_id = p.id
ORDER BY lh.tanggal DESC
LIMIT 10";

$laporan_result = mysqli_query($koneksi, $laporan_query);
$laporan_data = mysqli_fetch_all($laporan_result, MYSQLI_ASSOC);
?>

<?php
/**
 * koordinator_pengawas.php — Dashboard Koordinator Pengawas
 * Sistem Pengawasan Proyek — Koordinator
 */
$active_page = 'dashboard';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Koordinator Pengawas | CV Cipta Manunggal</title>
    <link rel="stylesheet" href="asset/koordinator.css">
</head>
<body>

<?php include 'sidebar.php'; ?>

<main class="main">
    <!-- TOPBAR -->
    <div class="topbar">
        <h2>Dashboard</h2>
        <div class="topbar-right">
            <div class="date-chip" id="date-chip"></div>
            <a href="Tinjau_laporan_harian.php" class="notif-btn">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="#aaa" stroke-width="1.5">
                    <path d="M8 1a5 5 0 015 5v3l1 2H2l1-2V6a5 5 0 015-5z"/>
                    <path d="M6.5 13.5a1.5 1.5 0 003 0"/>
                </svg>
                <div class="notif-dot"></div>
            </a>
        </div>
    </div>

    <!-- SECTION HEADER -->
    <div class="section-header fade-up">
        <div>
            <div class="section-title">Ringkasan Aktivitas</div>
            <div class="section-sub">Pantau laporan harian dan verifikasi progres pekerjaan</div>
        </div>
    </div>

    <!-- STATS -->
    <div class="stats-grid fade-up" style="animation-delay:.04s">
        <div class="stat-card">
            <div class="stat-label">Total Laporan</div>
            <div class="stat-val"><?= $total_laporan ?></div>
            <div class="stat-sub">Laporan harian</div>
            <div class="stat-icon">📊</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Menunggu Verifikasi</div>
            <div class="stat-val" style="color: #f59e0b;"><?= $menunggu ?></div>
            <div class="stat-sub">Perlu tinjauan</div>
            <div class="stat-icon">⏳</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Tervalidasi</div>
            <div class="stat-val" style="color: #22c55e;"><?= $tervalidasi ?></div>
            <div class="stat-sub">Sudah diverifikasi</div>
            <div class="stat-icon">✅</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Persentase</div>
            <div class="stat-val" style="color: #38bdf8;">
                <?= $total_laporan > 0 ? round(($tervalidasi / $total_laporan) * 100) : 0 ?>%
            </div>
            <div class="stat-sub">Tervalidasi</div>
            <div class="stat-icon">📈</div>
        </div>
    </div>

    <!-- ACTIVITY CARD -->
    <div class="panel fade-up" style="animation-delay:.08s">
        <div class="panel-title">
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5">
                <circle cx="8" cy="8" r="6"/>
                <path d="M8 5v3.5l2 1.5"/>
            </svg>
            Laporan Harian Terbaru
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Kontraktor</th>
                        <th>Progres Pekerjaan</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($laporan_result) > 0): ?>
                        <?php foreach ($laporan_data as $row): 
                            $status_class = (strtolower($row['status']) === 'menunggu') ? 'badge-wait' : 'badge-done';
                        ?>
                        <tr>
                            <td><?= date('d M Y', strtotime($row['tanggal'])) ?></td>
                            <td><?= htmlspecialchars($row['kontraktor_nama'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($row['progres'] ?? '-') ?></td>
                            <td>
                                <span class="badge <?= $status_class ?>">
                                    <?= htmlspecialchars($row['status']) ?>
                                </span>
                            </td>
                            <td>
                                <a href="Tinjau_laporan_harian.php?id=<?= $row['id'] ?>" class="btn btn-outline btn-sm">
                                    Tinjau
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" style="text-align: center; color: var(--muted); padding: 40px;">
                                Belum ada laporan harian
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<script>
// Update date chip
function updateDateChip() {
    const now = new Date();
    const options = { 
        weekday: 'short', 
        year: 'numeric', 
        month: 'short', 
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    };
    document.getElementById('date-chip').textContent = now.toLocaleDateString('id-ID', options);
}
updateDateChip();
setInterval(updateDateChip, 60000); // Update every minute
</script>

</body>
</html>
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
<script>
// Update date chip
function updateDateChip() {
    const now = new Date();
    const options = { 
        weekday: 'short', 
        year: 'numeric', 
        month: 'short', 
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    };
    document.getElementById('date-chip').textContent = now.toLocaleDateString('id-ID', options);
}
updateDateChip();
setInterval(updateDateChip, 60000); // Update every minute
</script>

</body>
</html>