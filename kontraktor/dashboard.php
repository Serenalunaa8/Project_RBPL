<?php
session_start();
if ($_SESSION['role'] != "kontraktor") {
    header("Location: ../login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Kontraktor | CV Cipta Manunggal Konsultan</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../kontraktor/asset/kontraktordash.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
            <a href="#">Dashboard</a>
            <a href="../kontraktor/AjukanIzin.php">Ajukan Izin</a>
            <a href="../kontraktor/LihatStatus.php">Status Izin</a>
            <a href="#">Riwayat</a>
            <a href="../logout.php" class="logout">Logout</a>
        </nav>

    </aside>


    <!-- MAIN CONTENT -->
    <main class="main-content">

        <header class="topbar">
            <div>
                <h1>Dashboard Kontraktor</h1>
                <p>Selamat datang, <strong><?php echo $_SESSION['username']; ?></strong></p>
            </div>

            <div class="role-badge">
                KONTRAKTOR
            </div>
        </header>


        <!-- STATISTICS -->
        <section class="stats">

            <div class="stat-card">
                <h3>24</h3>
                <p>Total Pengajuan</p>
            </div>

            <div class="stat-card">
                <h3>8</h3>
                <p>Pending</p>
            </div>

            <div class="stat-card">
                <h3>14</h3>
                <p>Disetujui</p>
            </div>

            <div class="stat-card">
                <h3>2</h3>
                <p>Ditolak</p>
            </div>

        </section>


        <!-- GRAPH + ACTIVITY -->
        <section class="grid-section">

            <div class="chart-card">
                <h3>Progres Persetujuan Izin</h3>
                <canvas id="izinChart"></canvas>
            </div>

            <div class="activity-card">
                <h3>Aktivitas Terbaru</h3>
                <ul>
                    <li>Izin pekerjaan Proyek A disetujui.</li>
                    <li>Pengajuan baru untuk Proyek B.</li>
                    <li>Izin pekerjaan Proyek C dalam review.</li>
                    <li>Revisi dokumen Proyek D diminta.</li>
                </ul>
            </div>

        </section>

    </main>

</div>


<script>
const ctx = document.getElementById('izinChart');
new Chart(ctx, {
    type: 'doughnut',
    data: {
        labels: ['Disetujui', 'Pending', 'Ditolak'],
        datasets: [{
            data: [14, 8, 2],
            backgroundColor: ['#ffc107', '#444', '#888'],
            borderWidth: 0
        }]
    },
    options: {
        plugins:{
            legend:{
                labels:{ color:'#fff' }
            }
        }
    }
});
</script>

</body>
</html>