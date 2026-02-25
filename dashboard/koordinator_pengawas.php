<?php
session_start();
include "../koneksi.php";

/* ===============================
   CEK LOGIN & ROLE
=================================*/
if (!isset($_SESSION['role']) || $_SESSION['role'] != "koordinator") {
    header("Location: ../login.php");
    exit;
}

/* ===============================
   AMBIL DATA STATISTIK (AMAN)
=================================*/

// Total semua laporan
$total_semua = 0;
$q_total = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM laporan_harian");
if ($q_total) {
    $data_total = mysqli_fetch_assoc($q_total);
    $total_semua = $data_total['total'];
}

// Total laporan hari ini (jika ada kolom tanggal)
$total_today = 0;
$today = date('Y-m-d');
$q_today = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM laporan_harian WHERE DATE(tanggal)='$today'");
if ($q_today) {
    $data_today = mysqli_fetch_assoc($q_today);
    $total_today = $data_today['total'];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard Koordinator</title>
    <meta charset="UTF-8">
    <style>
        *{
            margin:0;
            padding:0;
            box-sizing:border-box;
            font-family: Arial, sans-serif;
        }

        body{
            background:#121212;
            color:white;
        }

        .container{
            display:flex;
            min-height:100vh;
        }

        /* SIDEBAR */
        .sidebar{
            width:250px;
            background:#1e1e1e;
            padding:20px;
        }

        .sidebar h2{
            color:#ffc107;
            margin-bottom:30px;
        }

        .sidebar a{
            display:block;
            color:#ccc;
            text-decoration:none;
            padding:10px;
            margin-bottom:10px;
            border-radius:6px;
            transition:0.3s;
        }

        .sidebar a:hover{
            background:#ffc107;
            color:black;
        }

        /* CONTENT */
        .content{
            flex:1;
            padding:40px;
        }

        .topbar{
            display:flex;
            justify-content:space-between;
            align-items:center;
            margin-bottom:40px;
        }

        .badge{
            background:#ffc107;
            color:black;
            padding:8px 15px;
            border-radius:20px;
            font-size:12px;
            font-weight:bold;
        }

        .cards{
            display:grid;
            grid-template-columns:repeat(auto-fit,minmax(220px,1fr));
            gap:20px;
        }

        .card{
            background:#1f1f1f;
            padding:25px;
            border-radius:10px;
        }

        .card h3{
            margin-top:10px;
            font-size:28px;
            color:#ffc107;
        }
    </style>
</head>

<body>

<div class="container">

    <!-- SIDEBAR -->
    <div class="sidebar">
        <h2>Koordinator</h2>

        <a href="koordinator_pengawas.php">📋 Tinjau Laporan</a>
        <a href="susun_laporan.php">📝 Susun Laporan</a>
        <a href="riwayat_laporan.php">📁 Riwayat Laporan</a>
        <a href="../logout.php">🚪 Logout</a>
    </div>

    <!-- CONTENT -->
    <div class="content">

        <div class="topbar">
            <h1>Dashboard Koordinator</h1>
            <div class="badge">
                Login sebagai: <?php echo $_SESSION['username']; ?>
            </div>
        </div>

        <div class="cards">

            <div class="card">
                <p>Total Semua Laporan</p>
                <h3><?php echo $total_semua; ?></h3>
            </div>

            <div class="card">
                <p>Laporan Hari Ini</p>
                <h3><?php echo $total_today; ?></h3>
            </div>

            <div class="card">
                <p>Role</p>
                <h3><?php echo $_SESSION['role']; ?></h3>
            </div>

        </div>

    </div>

</div>

</body>
</html>