<?php
session_start();
if ($_SESSION['role'] != "koordinator") {
    header("Location: ../login.php");
}
?>

<h2>Dashboard Koordinator Pengawas</h2>
<p>Selamat datang, <?php echo $_SESSION['username']; ?></p>

<hr>

<h3>Menu Koordinator:</h3>
<ul>
    <li>Tinjau Laporan Harian</li>
    <li>Susun Laporan Mingguan</li>
    <li>Riwayat Laporan Mingguan</li>
</ul>

<a href="../logout.php">Logout</a>
