<?php
session_start();
include "../koneksi.php";

// Proteksi Role
if (!isset($_SESSION['role']) || $_SESSION['role'] != "koordinator") {
    header("Location: ../login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Koordinator Pengawas | CV Cipta Manunggal</title>
    <link rel="stylesheet" href="../kontraktor/asset/kontraktordash.css">
    <style>
        .badge-notif { background: #ffc107; color: #000; padding: 2px 8px; border-radius: 10px; font-size: 12px; margin-left: 5px; font-weight: bold; }
        .btn-tinjau { background: #ffc107; color: #000; padding: 6px 12px; border-radius: 4px; text-decoration: none; font-weight: bold; font-size: 13px; }
        .btn-tinjau:hover { background: #e0a800; }
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
            <a href="koordinator_pengawas.php" class="active">Dashboard</a>
            <a href="Tinjau_laporan_harian.php">Tinjau Laporan Harian</a>
            <a href="susun_laporan_mingguan.php">Susun Laporan Mingguan</a>
            <a href="riwayat_laporan_mingguan.php">Riwayat Laporan</a>
            <a href="../logout.php" class="logout">Logout</a>
        </nav>
    </aside>

    <main class="main-content">
        <header class="topbar">
            <div>
                <h1>Halo, <?php echo $_SESSION['username'] ?? 'Koordinator'; ?></h1>
                <p>Pantau laporan harian dan verifikasi progres pekerjaan.</p>
            </div>
            <div class="role-badge">KOORDINATOR PENGAWAS</div>
        </header>

        <section class="stats">
            <div class="stat-card">
                <h3>0</h3>
                <p>Total Laporan Harian</p>
            </div>
            <div class="stat-card">
                <h3>0</h3>
                <p>Menunggu Pengesahan</p>
            </div>
            <div class="stat-card">
                <h3>0</h3>

                <p>Laporan Mingguan Tersimpan</p>
            </div>
        </section>

        <section class="grid-section">
            <div class="activity-card" style="flex: 2;">
                <h3>Laporan Harian Terbaru</h3>
                <table width="100%" style="margin-top: 15px; color: #fff; border-collapse: collapse;">
                    <thead>
                        <tr style="text-align: left; border-bottom: 1px solid #444; color: #888;">
                            <th style="padding: 10px;">Tanggal</th>
                            <th>Pengawas Lapangan</th>
                            <th>Progres Pekerjaan</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr style='border-bottom: 1px solid #333;'>
                            <td style='padding: 15px;'>01 Apr 2026</td>
                            <td>Pengawas A</td>
                            <td>Pengecoran kolom Lt.3</td>
                            <td><span class='badge-notif' style='background: #f59e0b;'>Menunggu</span></td>
                            <td><a href='Tinjau_laporan_harian.php' class='btn-tinjau'>Tinjau</a></td>
                        </tr>
                        <tr style='border-bottom: 1px solid #333;'>
                            <td style='padding: 15px;'>31 Mar 2026</td>
                            <td>Pengawas B</td>
                            <td>Pemasangan bekisting</td>
                            <td><span class='badge-notif' style='background: #f59e0b;'>Menunggu</span></td>
                            <td><a href='Tinjau_laporan_harian.php' class='btn-tinjau'>Tinjau</a></td>
                        </tr>
                        <tr style='border-bottom: 1px solid #333;'>
                            <td style='padding: 15px;'>30 Mar 2026</td>
                            <td>Pengawas A</td>
                            <td>Penulangan plat lantai</td>
                            <td><span class='badge-notif' style='background: #22c55e;'>Tervalidasi</span></td>
                            <td><a href='Tinjau_laporan_harian.php' class='btn-tinjau'>Detail</a></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</div>

</body>
</html>