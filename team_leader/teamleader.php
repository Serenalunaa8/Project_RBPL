<?php
session_start();
if ($_SESSION['role'] != "teamleader") {
    header("Location: ../login.php");
}
?>

<h2>Dashboard Team Leader</h2>
<p>Selamat datang, <?php echo $_SESSION['username']; ?></p>

<hr>

<h3>Menu Team Leader:</h3>
<ul>
    <li>Review Laporan Mingguan</li>
    <li>Evaluasi Laporan Bulanan</li>
    <li>Riwayat Evaluasi</li>
</ul>

<a href="logout.php">Logout</a>
