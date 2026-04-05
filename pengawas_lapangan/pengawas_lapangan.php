<?php
session_start();
include "../koneksi.php"; //

// Proteksi Role
if (!isset($_SESSION['role']) || $_SESSION['role'] != "pengawas") {
    header("Location: ../login.php");
    exit;
}

// PBI-013: Hitung jumlah antrean untuk notifikasi
$query_count = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM form_izin_pekerjaan WHERE status='Menunggu Verifikasi'");
$data_count = mysqli_fetch_assoc($query_count);
$jumlah_antrean = $data_count['total'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Pengawas | CV Cipta Manunggal</title>
    <link rel="stylesheet" href="../kontraktor/asset/kontraktordash.css"> <style>
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
            <a href="pengawas_lapangan.php" class="active">Dashboard</a>
            <a href="verifikasi_Lapangan.php">Verifikasi Izin Pekerjaan <?php if($jumlah_antrean > 0) echo "<span class='badge-notif'>$jumlah_antrean</span>"; ?></a>
            <a href="#">Laporan Harian</a>
            <a href="../logout.php" class="logout">Logout</a>
        </nav>
    </aside>

    <main class="main-content">
        <header class="topbar">
            <div>
                <h1>Halo, <?php echo $_SESSION['username']; ?></h1>
                <p>Pantau progres dan verifikasi izin kerja hari ini.</p>
            </div>
            <div class="role-badge">PENGAWAS LAPANGAN</div>
        </header>

        <section class="stats">
            <div class="stat-card">
                <h3><?php echo $jumlah_antrean; ?></h3>
                <p>Izin Menunggu</p>
            </div>
            <div class="stat-card">
                <h3>0</h3> <p>Pekerjaan Berjalan</p>
            </div>
            <div class="stat-card">
                <h3>0</h3>
                <p>Total Pengajuan</p>
            </div>
        </section>

        <section class="grid-section">
            <div class="activity-card" style="flex: 2;">
                <h3>Antrean Verifikasi</h3>
                <table width="100%" style="margin-top: 15px; color: #fff; border-collapse: collapse;">
                    <thead>
                        <tr style="text-align: left; border-bottom: 1px solid #444; color: #888;">
                            <th style="padding: 10px;">Item Pekerjaan</th>
                            <th>Kontraktor</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $izin_query = mysqli_query($koneksi, "SELECT f.*, u.username FROM form_izin_pekerjaan f 
                                                            JOIN users u ON f.kontraktor_id = u.id 
                                                            WHERE f.status='Menunggu Verifikasi' ORDER BY f.id DESC");
                        if(mysqli_num_rows($izin_query) > 0) {
                            while($row = mysqli_fetch_assoc($izin_query)) {
                                echo "<tr style='border-bottom: 1px solid #333;'>
                                        <td style='padding: 15px;'>{$row['jenis_pekerjaan']}</td>
                                        <td>{$row['username']}</td>
                                        <td><a href='proses_verifikasi.php?id={$row['id']}' class='btn-tinjau'>Tinjau</a></td>
                                      </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='3' style='padding: 20px; text-align: center; color: #666;'>Tidak ada data masuk</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</div>
</body>
</html>