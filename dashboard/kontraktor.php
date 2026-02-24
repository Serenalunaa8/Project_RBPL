<?php
session_start();
if ($_SESSION['role'] != "kontraktor") {
    header("Location: ../login.php");
}
?>

<h2>Dashboard Kontraktor</h2>
<p>Selamat datang, <?php echo $_SESSION['username']; ?></p>

<hr>

<h3>Menu Kontraktor:</h3>
<ul>
    <li><a href="../menu_Kontraktor/AjukanIzin.php">Ajukan Izin Pekerjaan</a></li>
    <li><a href="../menu_Kontraktor/LihatStatus.php">Lihat Status Izin</a></li>
    <li>Riwayat Pengajuan</li>
</ul>

<a href="../logout.php">Logout</a>
