<?php
session_start();
require_once "../koneksi.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] != "pengawas") {
    header("Location: ../login.php");
    exit;
}

// Pastikan koneksi database tersedia
if (!isset($koneksi) || !$koneksi) {
    die("Koneksi database gagal. Periksa file koneksi.php dan server MySQL.");
}

// Get ID dari URL jika ada, untuk filter
$filter_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Query utama - mengambil semua data dengan join yang benar
$query = "
    SELECT f.id, f.jenis_pekerjaan, f.volume, f.lokasi, f.status, f.created_at, f.kontraktor_id, u.username
    FROM form_izin_pekerjaan f
    LEFT JOIN users u ON f.kontraktor_id = u.id
    WHERE 1=1
";

// Jika ada filter ID tertentu
if ($filter_id > 0) {
    $query .= " AND f.id = $filter_id";
}

// Order berdasarkan priority dan tanggal
$query .= " ORDER BY 
    CASE WHEN f.status = 'Menunggu Review' THEN 1
         WHEN f.status = 'Dalam Verifikasi' THEN 2
         WHEN f.status = 'Disetujui' THEN 3
         WHEN f.status = 'Ditolak' THEN 4
         ELSE 5
    END,
    f.created_at DESC";

$result = mysqli_query($koneksi, $query);
if (!$result) {
    die("Query error: " . mysqli_error($koneksi));
}

// Hitung statistik untuk highlight
$query_count = "SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN status = 'Menunggu Review' THEN 1 ELSE 0 END) as menunggu,
    SUM(CASE WHEN status = 'Disetujui' THEN 1 ELSE 0 END) as disetujui,
    SUM(CASE WHEN status = 'Ditolak' THEN 1 ELSE 0 END) as ditolak,
    SUM(CASE WHEN status = 'Dalam Verifikasi' THEN 1 ELSE 0 END) as verifikasi
FROM form_izin_pekerjaan";

$count_result = mysqli_query($koneksi, $query_count);
if (!$count_result) {
    die("Count query error: " . mysqli_error($koneksi));
}

$stats = mysqli_fetch_assoc($count_result);
$total_pengajuan = $stats['total'] ?? 0;
$jumlah_antrean = $stats['menunggu'] ?? 0;
$jumlah_approved = $stats['disetujui'] ?? 0;
$jumlah_ditolak = $stats['ditolak'] ?? 0;
$jumlah_verifikasi = $stats['verifikasi'] ?? 0;

$active_page = 'verifikasi';

// Helper function untuk status class
function getStatusClass($status) {
    if($status === 'Menunggu Review'){
        return 'status-menunggu';
    } elseif($status === 'Dalam Verifikasi'){
        return 'status-verifikasi';
    } elseif($status === 'Disetujui'){
        return 'status-disetujui';
    } elseif($status === 'Ditolak'){
        return 'status-ditolak';
    } else {
        return 'status-menunggu';
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Izin Pekerjaan | CV Cipta Manunggal Konsultan</title>
    <link rel="stylesheet" href="../koordinator_pengawas/asset/koordinator.css">
    <style>
        /* ── FILTER CARD ── */
        .filter-section {
            background: #1c1c1c;
            padding: 20px 24px;
            border-radius: 12px;
            border: 1px solid rgba(255,255,255,0.05);
            margin-bottom: 28px;
        }

        .filter-form {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            align-items: center;
        }

        .filter-form select {
            padding: 10px 14px;
            border-radius: 8px;
            border: 1px solid rgba(255,255,255,0.1);
            background: #0f0f0f;
            color: #fff;
            font-family: 'Inter', sans-serif;
            font-size: 13px;
            transition: 0.3s;
        }

        .filter-form select:focus {
            outline: none;
            border-color: #ffc107;
            box-shadow: 0 0 8px rgba(255, 193, 7, 0.2);
        }

        .btn-filter {
            background: #ffc107;
            color: #111;
            padding: 10px 20px;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            font-weight: 600;
            font-size: 13px;
            transition: 0.3s;
            font-family: 'Inter', sans-serif;
        }

        .btn-filter:hover {
            background: #ffb300;
            transform: translateY(-2px);
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

        .table-izin {
            width: 100%;
            border-collapse: collapse;
        }

        .table-izin thead {
            background: #0f0f0f;
        }

        .table-izin th {
            text-align: left;
            color: #aaa;
            padding: 14px 16px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid rgba(255,255,255,0.05);
        }

        .table-izin td {
            padding: 14px 16px;
            border-bottom: 1px solid rgba(255,255,255,0.03);
            font-size: 13px;
        }

        .table-izin tbody tr:hover {
            background: rgba(255, 193, 7, 0.02);
        }

        /* ── STATUS BADGES ── */
        .status-badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .status-menunggu {
            background: rgba(255, 152, 0, 0.2);
            color: #ff9800;
        }

        .status-verifikasi {
            background: rgba(96, 165, 250, 0.2);
            color: #60a5fa;
        }

        .status-disetujui {
            background: rgba(34, 197, 94, 0.2);
            color: #22c55e;
        }

        .status-ditolak {
            background: rgba(239, 68, 68, 0.2);
            color: #ef4444;
        }

        /* ── BUTTONS ── */
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

        .empty {
            text-align: center;
            padding: 60px 20px;
            color: #666;
        }

        /* ── RESPONSIVE ── */
        @media (max-width: 1024px) {
            .main {
                padding: 30px;
            }
        }

        @media (max-width: 768px) {
            .main {
                padding: 20px;
            }

            .filter-form {
                flex-direction: column;
            }

            .filter-form select,
            .btn-filter {
                width: 100%;
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

<?php include 'sidebar.php'; ?>

<main class="main">

    <!-- TOPBAR -->
    <div class="topbar">
        <div>
            <h2>Verifikasi Izin Pekerjaan</h2>
            <div class="section-sub">Tinjau dan verifikasi izin pekerjaan dari kontraktor</div>
        </div>
    </div>

    <!-- STATS -->
    <div class="stats-grid fade-up" style="animation-delay:.04s; display: grid; grid-template-columns: repeat(5, 1fr); gap: 20px;">
        <div class="stat-card">
            <div class="stat-label">Total Pengajuan</div>
            <div class="stat-val"><?php echo $total_pengajuan; ?></div>
            <div class="stat-sub">Semua izin pekerjaan</div>
            <div class="stat-icon">📋</div>
        </div>

        <div class="stat-card">
            <div class="stat-label">Menunggu Verifikasi</div>
            <div class="stat-val" style="color: #f59e0b;"><?php echo $jumlah_antrean; ?></div>
            <div class="stat-sub">Perlu ditinjau</div>
            <div class="stat-icon">⏳</div>
        </div>

        <div class="stat-card">
            <div class="stat-label">Dalam Verifikasi</div>
            <div class="stat-val" style="color: #60a5fa;"><?php echo $jumlah_verifikasi; ?></div>
            <div class="stat-sub">Sedang diproses</div>
            <div class="stat-icon">🔄</div>
        </div>

        <div class="stat-card">
            <div class="stat-label">Disetujui</div>
            <div class="stat-val" style="color: #22c55e;"><?php echo $jumlah_approved; ?></div>
            <div class="stat-sub">Sudah diverifikasi</div>
            <div class="stat-icon">✅</div>
        </div>

        <div class="stat-card">
            <div class="stat-label">Ditolak</div>
            <div class="stat-val" style="color: #ef4444;"><?php echo $jumlah_ditolak; ?></div>
            <div class="stat-sub">Tidak disetujui</div>
            <div class="stat-icon">❌</div>
        </div>
    </div>

        <!-- FILTER -->
        <div class="filter-section">
            <form method="GET" class="filter-form">
                <select name="status">
                    <option value="">- Semua Status -</option>
                    <option value="menunggu" <?php echo (isset($_GET['status']) && $_GET['status'] === 'menunggu') ? 'selected' : ''; ?>>Menunggu Review</option>
                    <option value="verifikasi" <?php echo (isset($_GET['status']) && $_GET['status'] === 'verifikasi') ? 'selected' : ''; ?>>Dalam Verifikasi</option>
                    <option value="disetujui" <?php echo (isset($_GET['status']) && $_GET['status'] === 'disetujui') ? 'selected' : ''; ?>>Disetujui</option>
                    <option value="ditolak" <?php echo (isset($_GET['status']) && $_GET['status'] === 'ditolak') ? 'selected' : ''; ?>>Ditolak</option>
                </select>
                <button type="submit" class="btn-filter">Filter</button>
            </form>
        </div>

        <!-- TABLE -->
        <section class="grid-section">
            <div class="activity-card">
                <h3>Daftar Izin Pekerjaan</h3>

                <div class="table-wrapper">
                    <table class="table-izin">
                        <thead>
                            <tr>
                                <th>Jenis Pekerjaan</th>
                                <th>Volume</th>
                                <th>Lokasi</th>
                                <th>Kontraktor</th>
                                <th>Tanggal Ajuan</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(mysqli_num_rows($result) > 0): ?>
                                <?php while($row = mysqli_fetch_assoc($result)): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['jenis_pekerjaan'] ?? '-'); ?></td>
                                        <td><?php echo htmlspecialchars($row['volume'] ?? '-'); ?></td>
                                        <td><?php echo htmlspecialchars($row['lokasi'] ?? '-'); ?></td>
                                        <td><?php echo htmlspecialchars($row['username'] ?? '-'); ?></td>
                                        <td><?php echo date('d M Y', strtotime($row['created_at'] ?? date('Y-m-d'))); ?></td>
                                        <td>
                                            <span class="status-badge <?php echo getStatusClass($row['status']); ?>">
                                                <?php echo htmlspecialchars($row['status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="detail_verifikasi.php?id=<?php echo $row['id']; ?>" class="btn-tinjau">
                                                Tinjau
                                            </a>
                                            <?php if($row['status'] == 'Disetujui Pengawas' || $row['status'] == 'Dalam Verifikasi'): ?>
                                                <a href="laporan_verifikasi.php?id=<?php echo $row['id']; ?>" class="btn-laporan" style="margin-left: 8px; background: #22c55e; color: white; padding: 6px 12px; border-radius: 6px; text-decoration: none; font-size: 12px;">
                                                    Laporan
                                                </a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="empty">
                                        <strong>Tidak ada data izin pekerjaan</strong>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

            </div>
        </section>

    </main>

</body>
</html>