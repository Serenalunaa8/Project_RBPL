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
   FILTER (AMAN)
=================================*/
$where = "1=1";

if (!empty($_GET['status'])) {
    $status = mysqli_real_escape_string($koneksi, $_GET['status']);
    $where .= " AND status='$status'";
}

if (!empty($_GET['tanggal'])) {
    $tanggal = mysqli_real_escape_string($koneksi, $_GET['tanggal']);
    $where .= " AND DATE(tanggal)='$tanggal'";
}

/* ===============================
   QUERY TANPA ORDER BY ID
=================================*/
$query = mysqli_query($koneksi, "SELECT * FROM laporan_harian WHERE $where");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Riwayat Laporan - Koordinator</title>
    <meta charset="UTF-8">
    <style>
        *{margin:0;padding:0;box-sizing:border-box;font-family:Arial;}
        body{background:#121212;color:white;}
        .container{display:flex;min-height:100vh;}

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

        h1{margin-bottom:20px;}

        .filter-box{
            background:#1f1f1f;
            padding:20px;
            border-radius:8px;
            margin-bottom:20px;
        }

        select,input{
            padding:8px;
            border-radius:6px;
            border:none;
            margin-right:10px;
        }

        button{
            background:#ffc107;
            border:none;
            padding:8px 15px;
            border-radius:6px;
            font-weight:bold;
            cursor:pointer;
        }

        table{
            width:100%;
            border-collapse:collapse;
            background:#1f1f1f;
        }

        th,td{
            padding:12px;
            border:1px solid #333;
        }

        th{
            background:#ffc107;
            color:black;
        }

        .badge{
            padding:4px 8px;
            border-radius:12px;
            font-size:11px;
            font-weight:bold;
        }

        .menunggu{background:orange;color:black;}
        .disetujui{background:green;}
        .ditolak{background:red;}
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

        <h1>📁 Riwayat Laporan</h1>

        <!-- FILTER -->
        <div class="filter-box">
            <form method="GET">
                <select name="status">
                    <option value="">-- Semua Status --</option>
                    <option value="menunggu">Menunggu</option>
                    <option value="disetujui">Disetujui</option>
                    <option value="ditolak">Ditolak</option>
                </select>

                <input type="date" name="tanggal">

                <button type="submit">Filter</button>
            </form>
        </div>

        <!-- TABEL -->
        <table>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Isi Laporan</th>
                <th>Status</th>
            </tr>

            <?php
            $no = 1;

            if ($query && mysqli_num_rows($query) > 0) {
                while ($row = mysqli_fetch_assoc($query)) {
            ?>
                <tr>
                    <td><?= $no++; ?></td>
                    <td><?= isset($row['tanggal']) ? $row['tanggal'] : '-'; ?></td>
                    <td><?= isset($row['isi_laporan']) ? $row['isi_laporan'] : '-'; ?></td>
                    <td>
                        <?php if(isset($row['status'])) { ?>
                            <span class="badge <?= $row['status']; ?>">
                                <?= $row['status']; ?>
                            </span>
                        <?php } else { echo "-"; } ?>
                    </td>
                </tr>
            <?php
                }
            } else {
                echo "<tr><td colspan='4'>Belum ada data laporan</td></tr>";
            }
            ?>
        </table>

    </div>

</div>

</body>
</html>