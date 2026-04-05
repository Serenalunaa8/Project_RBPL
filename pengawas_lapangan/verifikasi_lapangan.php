<?php
session_start();
include "../koneksi.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] != "pengawas") {
    header("Location: ../login.php");
    exit;
}

$query = mysqli_query($koneksi, "
    SELECT f.*, u.username 
    FROM form_izin_pekerjaan f
    JOIN users u ON f.id = u.id
    WHERE f.status='Menunggu Review'
    ORDER BY f.id DESC
");

// Hitung jumlah antrean untuk badge
$query_count = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM form_izin_pekerjaan WHERE status='Menunggu Review'");
$data_count = mysqli_fetch_assoc($query_count);
$jumlah_antrean = $data_count['total'];

// Hitung total pengajuan (semua status)
$query_total = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM form_izin_pekerjaan");
$data_total = mysqli_fetch_assoc($query_total);
$total_pengajuan = $data_total['total'];

// Hitung yang sudah disetujui
$query_approved = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM form_izin_pekerjaan WHERE status='Disetujui'");
$data_approved = mysqli_fetch_assoc($query_approved);
$jumlah_approved = $data_approved['total'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta http-equiv="refresh" content="10">
    <meta charset="UTF-8">
    <title>Verifikasi Izin Pekerjaan | CV Cipta Manunggal</title>
    <link rel="stylesheet" href="../kontraktor/asset/kontraktordash.css"> 
    <style>
        .badge-notif { background: #ffc107; color: #000; padding: 2px 8px; border-radius: 10px; font-size: 12px; margin-left: 5px; font-weight: bold; }
        .btn-tinjau { background: #ffc107; color: #000; padding: 6px 12px; border-radius: 4px; text-decoration: none; font-weight: bold; font-size: 13px; }
        .btn-tinjau:hover { background: #e0a800; }
        .table-izin { width: 100%; border-collapse: collapse; margin-top: 15px; color: #fff; }
        .table-izin th { text-align: left; color: #888; border-bottom: 1px solid #444; padding: 10px; }
        .table-izin td { padding: 12px 10px; border-bottom: 1px solid #333; }
        .empty { text-align: center; padding: 20px; color: #666; }
    </style>
</head>
<body>

<div class="dashboard-container">
    <aside class="sidebar">
        <div class="sidebar-brand">
            <svg viewBox="0 0 120 120" class="logo-arch">
                <rect x="10" y="10" width="100" height="100" stroke="#ffc107" stroke-width="3" fill="none"/>
                <path d="M35 80 V40 H60" stroke="#ffc107" stroke-width="4" fill="none"/>
                <path d="M60 40 L75 60 L90 40 V80" stroke="#ffc107" stroke-width="4" fill="none"/>
            </svg>
            <h2>CIPTA<span>MANUNGGAL</span></h2>
        </div>
        <nav>
            <a href="pengawas_lapangan.php">Dashboard</a>
            <a href="verifikasi_lapangan.php" class="active">Verifikasi Izin Pekerjaan <?php if($jumlah_antrean > 0) echo "<span class='badge-notif'>$jumlah_antrean</span>"; ?></a>
            <a href="#">Laporan Harian</a>
            <a href="../logout.php" class="logout">Logout</a>
        </nav>
    </aside>

    <main class="main-content">
        <header class="topbar">
            <div>
                <h1>Verifikasi Izin Pekerjaan</h1>
                <p>Tinjau dan verifikasi izin pekerjaan dari kontraktor.</p>
            </div>
            <div class="role-badge">PENGAWAS LAPANGAN</div>
        </header>

        <section class="stats">
            <div class="stat-card">
                <h3><?php echo $jumlah_antrean; ?></h3>
                <p>Izin Menunggu</p>
            </div>
            <div class="stat-card">
                <h3><?php echo $jumlah_approved; ?></h3>
                <p>Izin Disetujui</p>
            </div>
            <div class="stat-card">
                <h3><?php echo $total_pengajuan; ?></h3>
                <p>Total Pengajuan</p>
            </div>
        </section>

        <section class="grid-section">
            <div class="activity-card" style="flex: 1;">
                <h3>📋 Daftar Izin Menunggu Verifikasi</h3>
                <table class="table-izin">
                    <thead>
                        <tr>
                            <th>Jenis Pekerjaan</th>
                            <th>Volume</th>
                            <th>Lokasi</th>
                            <th>Kontraktor</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(mysqli_num_rows($query) > 0): ?>
                            <?php while($row = mysqli_fetch_assoc($query)): ?>
                                <tr>
                                    <td><?php echo $row['jenis_pekerjaan']; ?></td>
                                    <td><?php echo $row['volume']; ?></td>
                                    <td><?php echo $row['lokasi']; ?></td>
                                    <td><?php echo $row['username']; ?></td>
                                    <td>
                                        <a href="detail_verifikasi.php?id=<?php echo $row['id']; ?>" class="btn-tinjau">
                                            Tinjau
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="empty">Tidak ada data izin menunggu verifikasi</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</div>

</body>
</html>