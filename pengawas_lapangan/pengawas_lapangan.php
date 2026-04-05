<?php
session_start();
include "../koneksi.php";

/* ================== CEK ROLE ================== */
if (!isset($_SESSION['role']) || $_SESSION['role'] != "pengawas") {
    header("Location: ../login.php");
    exit;
}

/* ================== HITUNG NOTIF ================== */
$query_count = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM form_izin_pekerjaan WHERE status='Menunggu Review'");
$data_count = mysqli_fetch_assoc($query_count);
$jumlah_antrean = $data_count['total'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Dashboard Pengawas</title>

<link rel="stylesheet" href="../kontraktor/asset/kontraktordash.css">

<style>
/* ===== NOTIF POPUP ===== */
.notif-popup {
    position: fixed;
    top: 20px;
    right: 20px;
    background: #1c1c1c;
    border-left: 4px solid #ffc107;
    padding: 16px 20px;
    border-radius: 10px;
    color: #fff;
    width: 280px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.4);
    animation: slideIn 0.4s ease;
    z-index: 999;
}

@keyframes slideIn {
    from { transform: translateX(120%); opacity: 0; }
    to   { transform: translateX(0); opacity: 1; }
}

.notif-popup h4 {
    margin: 0;
    font-size: 14px;
    color: #ffc107;
}

.notif-popup p {
    font-size: 12px;
    margin-top: 5px;
    color: #ccc;
}

.notif-close {
    position: absolute;
    top: 8px;
    right: 10px;
    cursor: pointer;
    font-size: 14px;
    color: #aaa;
}

.notif-close:hover {
    color: #fff;
}

/* ===== TABLE ===== */
.table-izin {
    width: 100%;
    border-collapse: collapse;
    margin-top: 15px;
}

.table-izin th {
    text-align: left;
    color: #888;
    border-bottom: 1px solid #333;
    padding: 10px;
}

.table-izin td {
    padding: 12px 10px;
    border-bottom: 1px solid #222;
}

.btn-tinjau {
    background: #ffc107;
    color: #000;
    padding: 6px 12px;
    border-radius: 5px;
    text-decoration: none;
    font-size: 12px;
    font-weight: bold;
}

.btn-tinjau:hover {
    background: #e0a800;
}
</style>

</head>
<body>

<!-- ===== NOTIF ===== -->
<?php if ($jumlah_antrean > 0): ?>
<div class="notif-popup" id="notifBox">
    <span class="notif-close" onclick="closeNotif()">✖</span>
    <h4>🔔 Notifikasi Baru</h4>
    <p><?php echo $jumlah_antrean; ?> izin pekerjaan menunggu verifikasi</p>
</div>
<?php endif; ?>

<div class="dashboard-container">

    <!-- SIDEBAR -->
    <aside class="sidebar">
        <div class="sidebar-brand">
            <h2>CIPTA<span>MANUNGGAL</span></h2>
        </div>

        <nav>
            <a href="pengawas_lapangan.php" class="active">Dashboard</a>
            <a href="verifikasi_lapangan.php">
                Verifikasi
                <?php if($jumlah_antrean > 0): ?>
                    <span class="badge-notif"><?php echo $jumlah_antrean; ?></span>
                <?php endif; ?>
            </a>
            <a href="#">Laporan Harian</a>
            <a href="../logout.php" class="logout">Logout</a>
        </nav>
    </aside>

    <!-- MAIN -->
    <main class="main-content">

        <!-- TOP -->
        <header class="topbar">
            <div>
                <h1>Halo, <?php echo $_SESSION['username']; ?></h1>
                <p>Pantau verifikasi izin pekerjaan</p>
            </div>
            <div class="role-badge">PENGAWAS</div>
        </header>

        <!-- STATS -->
        <section class="stats">
            <div class="stat-card">
                <h3><?php echo $jumlah_antrean; ?></h3>
                <p>Izin Menunggu</p>
            </div>
        </section>

        <!-- TABLE DASHBOARD -->
        <section class="grid-section">
            <div class="activity-card" style="flex:2;">
                <h3>Antrean Verifikasi</h3>

                <table class="table-izin">
                    <thead>
                        <tr>
                            <th>Pekerjaan</th>
                            <th>Kontraktor</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $izin_query = mysqli_query($koneksi,
                            "SELECT f.*, u.username
                             FROM form_izin_pekerjaan f
                             JOIN users u ON f.kontraktor_id = u.id
                             WHERE f.status='Menunggu Review'
                             ORDER BY f.id DESC");

                        if(mysqli_num_rows($izin_query) > 0){
                            while($row = mysqli_fetch_assoc($izin_query)){
                                echo "
                                <tr>
                                    <td>{$row['jenis_pekerjaan']}</td>
                                    <td>{$row['username']}</td>
                                    <td>
                                        <a href='proses_verifikasi.php?id={$row['id']}' class='btn-tinjau'>
                                            Tinjau
                                        </a>
                                    </td>
                                </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='3' style='text-align:center; color:#666;'>Tidak ada data</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>

            </div>
        </section>

    </main>
</div>

<script>
function closeNotif(){
    document.getElementById("notifBox").style.display = "none";
}
</script>

</body>
</html>