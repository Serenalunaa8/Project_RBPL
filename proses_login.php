<?php
session_start();
include "koneksi.php";

$username = $_POST['username'];
$password = $_POST['password'];

$query = mysqli_query($koneksi, "SELECT * FROM users WHERE username='$username'");
$data = mysqli_fetch_assoc($query);

if ($data) {
    if ($password == $data['password']) {

        $_SESSION['kontraktor_id'] = $data['id']; // Simpan ID kontraktor di session
        $_SESSION['username'] = $data['username'];
        $_SESSION['role'] = $data['role'];

        // dashboard berdasarkan role
        if ($data['role'] == "kontraktor") {
            header("Location: kontraktor/dashboard.php");
        } elseif ($data['role'] == "pengawas") {
            header("Location: pengawas_lapangan/pengawas_lapangan.php");
        } elseif ($data['role'] == "koordinator") {
            header("Location: koordinator_pengawas/koordinator_pengawas.php");
        } elseif ($data['role'] == "teamleader") {
            header("Location: team_leader/teamleader.php");
    } else {
        echo "Password salah!";
    }
} else {
    echo "Username tidak ditemukan!";
}}
?>
