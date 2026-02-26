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
    $isi = mysqli_real_escape_string($koneksi, $_POST['isi_laporan']);

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
    color:white;
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

.container{
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

.sidebar nav{
    display:flex;
    flex-direction:column;
    gap:18px;
}

.sidebar nav a{
    text-decoration:none;
    color:#ccc;
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

/* Main */
.content{
    flex:1;
    padding:60px;
    animation:fadeUp 1s ease;
}

h1{
    margin-bottom:40px;
}

/* Form Card */
.form-card{
    background:#1c1c1c;
    padding:40px;
    border-radius:20px;
    max-width:650px;
    box-shadow:0 20px 50px rgba(0,0,0,0.6);
    border:1px solid rgba(255,255,255,0.05);
    transition:0.4s;
}

.form-card:hover{
    border:1px solid #ffc107;
}

textarea{
    width:100%;
    height:180px;
    padding:15px;
    border:none;
    border-radius:10px;
    margin-bottom:25px;
    resize:none;
    font-family:'Inter',sans-serif;
    font-size:14px;
    outline:none;
    background:#2a2a2a;
    color:white;
    transition:0.3s;
}

textarea:focus{
    box-shadow:0 0 0 2px #ffc107;
}

/* Button */
button{
    background:#ffc107;
    color:black;
    border:none;
    padding:12px 25px;
    border-radius:10px;
    font-weight:600;
    cursor:pointer;
    transition:0.3s;
}

button:hover{
    transform:translateY(-3px);
    box-shadow:0 10px 25px rgba(255,193,7,0.4);
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

<div class="container">

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
            <a href="../logout.php">🚪 Logout</a>
        </nav>

    </div>

    <!-- Content -->
    <div class="content">

        <h1>📝 Susun Laporan Baru</h1>

        <div class="form-card">
            <form method="POST">

                <label>Isi Laporan:</label><br><br>

                <textarea name="isi_laporan" placeholder="Tulis laporan harian di sini..." required></textarea>

                <button type="submit" name="submit">
                    Simpan Laporan
                </button>

            </form>
        </div>

    </div>

</div>

</body>
</html>