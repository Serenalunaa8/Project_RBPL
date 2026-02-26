<?php
session_start();
include "../koneksi.php";

/* =============================
   CEK LOGIN & ROLE
============================= */
if (!isset($_SESSION['role']) || $_SESSION['role'] != "koordinator") {
    header("Location: ../login.php");
    exit;
}

/* =============================
   AMBIL DATA STATISTIK
============================= */
$total_laporan = 0;
$laporan_hari_ini = 0;

/* Cek apakah tabel ada */
$cek = mysqli_query($koneksi, "SHOW TABLES LIKE 'laporan_harian'");
if(mysqli_num_rows($cek) > 0){

    $q1 = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM laporan_harian");
    if($q1){
        $d1 = mysqli_fetch_assoc($q1);
        $total_laporan = $d1['total'];
    }

    $today = date('Y-m-d');
    $q2 = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM laporan_harian WHERE DATE(tanggal)='$today'");
    if($q2){
        $d2 = mysqli_fetch_assoc($q2);
        $laporan_hari_ini = $d2['total'];
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard Koordinator</title>
    <meta charset="UTF-8">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">

<style>
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}

body{
    font-family:'Inter',sans-serif;
    background:#0f0f0f;
    color:#ffffff;
    overflow-x:hidden;
}

/* Glow Background */
body::before{
    content:"";
    position:fixed;
    width:700px;
    height:700px;
    background:radial-gradient(circle,
        rgba(255,193,7,0.15) 0%,
        rgba(15,15,15,0.95) 70%);
    top:50%;
    left:50%;
    transform:translate(-50%,-50%);
    filter:blur(100px);
    z-index:-1;
}

/* Layout */
.dashboard-container{
    display:flex;
    min-height:100vh;
}

/* Sidebar */
.sidebar{
    width:270px;
    background:#1a1a1a;
    padding:40px 25px;
    border-right:1px solid rgba(255,255,255,0.05);
    animation:slideLeft 0.8s ease;
}

/* Logo */
.sidebar-brand{
    display:flex;
    align-items:center;
    gap:12px;
    margin-bottom:50px;
}

.logo-arch{
    width:42px;
    height:42px;
    stroke:#ffc107;
    stroke-width:4;
    fill:none;
    animation:rotateLogo 6s linear infinite;
}

.sidebar h2{
    font-size:17px;
}

.sidebar span{
    color:#ffc107;
}

/* Nav */
.sidebar nav{
    display:flex;
    flex-direction:column;
    gap:18px;
}

.sidebar nav a{
    text-decoration:none;
    color:#cccccc;
    padding:12px 14px;
    border-radius:8px;
    transition:0.4s;
    position:relative;
}

.sidebar nav a::before{
    content:"";
    position:absolute;
    left:0;
    top:0;
    width:0%;
    height:100%;
    background:#ffc107;
    border-radius:8px;
    transition:0.4s;
    z-index:-1;
}

.sidebar nav a:hover::before{
    width:100%;
}

.sidebar nav a:hover{
    color:#111;
    transform:translateX(6px);
}

.logout{
    margin-top:40px;
    background:#222;
}

/* Main */
.main-content{
    flex:1;
    padding:60px;
    animation:fadeUp 1s ease;
}

.topbar{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:50px;
}

.role-badge{
    background:#ffc107;
    color:#111;
    padding:8px 18px;
    border-radius:20px;
    font-size:12px;
    font-weight:600;
    box-shadow:0 0 20px rgba(255,193,7,0.5);
}

/* Stats */
.stats{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(220px,1fr));
    gap:25px;
}

.stat-card{
    background:#1c1c1c;
    padding:35px;
    border-radius:18px;
    border:1px solid rgba(255,255,255,0.05);
    transition:0.4s;
}

.stat-card:hover{
    border:1px solid #ffc107;
    transform:translateY(-8px) scale(1.02);
    box-shadow:0 25px 50px rgba(0,0,0,0.6);
}

.stat-card h3{
    font-size:34px;
    color:#ffc107;
    margin-top:15px;
}

/* Animations */
@keyframes fadeUp{
    from{opacity:0; transform:translateY(30px);}
    to{opacity:1; transform:translateY(0);}
}

@keyframes slideLeft{
    from{opacity:0; transform:translateX(-40px);}
    to{opacity:1; transform:translateX(0);}
}

@keyframes rotateLogo{
    from{transform:rotate(0deg);}
    to{transform:rotate(360deg);}
}
</style>
</head>

<body>

<div class="dashboard-container">

    <!-- Sidebar -->
    <div class="sidebar">

        <div class="sidebar-brand">
            <svg class="logo-arch" viewBox="0 0 100 100">
                <path d="M20 80 L50 20 L80 80" />
                <path d="M35 60 L65 60" />
            </svg>
            <h2>Sistem <span>Pengawasan</span></h2>
        </div>

        <nav>
            <a href="koordinator_pengawas.php">🏠 Dashboard</a>
            <a href="koordinator_tinjau_laporan.php">📋 Tinjau Laporan</a>
            <a href="koordinator_susun_laporan.php">📝 Susun Laporan</a>
            <a href="koordinator_riwayat_laporan.php">📁 Riwayat Laporan</a>
            <a href="../logout.php" class="logout">🚪 Logout</a>
        </nav>

    </div>

    <!-- Main -->
    <div class="main-content">

        <div class="topbar">
            <h1>Dashboard Koordinator</h1>
            <div class="role-badge">
                <?= strtoupper($_SESSION['username']); ?>
            </div>
        </div>

        <div class="stats">

            <div class="stat-card">
                <p>Total Semua Laporan</p>
                <h3><?= $total_laporan; ?></h3>
            </div>

            <div class="stat-card">
                <p>Laporan Hari Ini</p>
                <h3><?= $laporan_hari_ini; ?></h3>
            </div>

            <div class="stat-card">
                <p>Role Aktif</p>
                <h3><?= strtoupper($_SESSION['role']); ?></h3>
            </div>

        </div>

    </div>

</div>

</body>
</html>