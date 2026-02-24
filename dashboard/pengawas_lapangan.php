<?php
session_start();
if ($_SESSION['role'] != "pengawas") {
    header("Location: ../login.php");
}
?>

<h2>Dashboard Pengawas Lapangan</h2>
<p>Selamat datang, <?php echo $_SESSION['username']; ?></p>

<hr>

<h3>Menu Pengawas:</h3>
<ul>
    <li>Verifikasi Izin Pekerjaan</li>
    <li>Catat Laporan Harian</li>
    <li>Unggah Dokumentasi Lapangan</li>
</ul>

<a href="../logout.php">Logout</a>
