<?php
session_start();
include "koneksi.php";

$username = $_POST['username'];
$password = $_POST['password'];

$query = mysqli_query($koneksi, "SELECT * FROM users WHERE username='$username'");
$data = mysqli_fetch_assoc($query);

if ($data) {
    if ($password == $data['password']) {

        $_SESSION['id'] = $data['id'];
        $_SESSION['username'] = $data['username'];
        $_SESSION['role'] = $data['role'];

        // dashboard berdasarkan role
        if ($data['role'] == "kontraktor") {
            header("Location: dashboard/kontraktor.php");
        } elseif ($data['role'] == "pengawas") {
            header("Location: dashboard/pengawas_lapangan.php");
        } elseif ($data['role'] == "koordinator") {
            header("Location: dashboard/koordinator_pengawas.php");
        } elseif ($data['role'] == "teamleader") {
            header("Location: dashboard/teamleader.php");
        } elseif ($data['role'] == "admin") {
            header("Location: dashboard/admin.php");
        }

    } else {
        echo "Password salah!";
    }
} else {
    echo "Username tidak ditemukan!";
}
?>
