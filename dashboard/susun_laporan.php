<?php
session_start();
include "../koneksi.php";

/* ===============================
   CEK ROLE
=================================*/
if (!isset($_SESSION['role']) || $_SESSION['role'] != "koordinator") {
    header("Location: ../login.php");
    exit;
}

/* ===============================
   PROSES SIMPAN LAPORAN
=================================*/
if (isset($_POST['submit'])) {

    $tanggal = date('Y-m-d');
    $isi = $_POST['isi_laporan'];

    mysqli_query($koneksi, "INSERT INTO laporan_harian (tanggal, isi_laporan, status) 
                            VALUES ('$tanggal', '$isi', 'menunggu')");

    header("Location: koordinator_pengawas.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Susun Laporan - Koordinator</title>
    <meta charset="UTF-8">
    <style>
        *{margin:0;padding:0;box-sizing:border-box;font-family:Arial;}

        body{
            background:#121212;
            color:white;
        }

        .container{
            display:flex;
            min-height:100vh;
        }

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

        .content{
            flex:1;
            padding:40px;
        }

        h1{
            margin-bottom:30px;
        }

        form{
            background:#1f1f1f;
            padding:30px;
            border-radius:10px;
            width:100%;
            max-width:600px;
        }

        textarea{
            width:100%;
            height:150px;
            padding:10px;
            border:none;
            border-radius:6px;
            margin-bottom:20px;
        }

        button{
            background:#ffc107;
            color:black;
            border:none;
            padding:10px 20px;
            border-radius:6px;
            font-weight:bold;
            cursor:pointer;
        }

        button:hover{
            background:#e0a800;
        }
    </style>
</head>

<body>

<div class="container">

    <!-- SIDEBAR -->
    <div class="sidebar">
        <h2>Koordinator</h2>

        <a href="koordinator_pengawas.php">🏠 Dashboard</a>
        <a href="koordinator_tinjau_laporan.php">📋 Tinjau Laporan</a>
        <a href="koordinator_susun_laporan.php">📝 Susun Laporan</a>
        <a href="koordinator_riwayat_laporan.php">📁 Riwayat Laporan</a>
        <a href="../logout.php">🚪 Logout</a>
    </div>

    <!-- CONTENT -->
    <div class="content">

        <h1>📝 Susun Laporan Baru</h1>

        <form method="POST">
            <label>Isi Laporan:</label><br><br>
            <textarea name="isi_laporan" required></textarea>

            <button type="submit" name="submit">Simpan Laporan</button>
        </form>

    </div>

</div>

</body>
</html>