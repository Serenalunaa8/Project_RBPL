<?php
session_start();

// Jalur koneksi yang lebih fleksibel
if (file_exists("../config/koneksi.php")) {
    include "../config/koneksi.php";
} elseif (file_exists("../koneksi.php")) {
    include "../koneksi.php";
} elseif (file_exists("koneksi.php")) {
    include "koneksi.php";
} else {
    die("Error: File koneksi.php tidak ditemukan.");
}

// Proteksi halaman - Koordinator & Pengawas
if (!isset($_SESSION['role']) || ($_SESSION['role'] != "pengawas" && $_SESSION['role'] != "koordinator")) {
    header("Location: ../login.php");
    exit();
}

$msg = "";
$username = $_SESSION['username'];

// Proses Simpan Laporan Harian
if (isset($_POST['simpan_laporan'])) {
    $id_pekerjaan = mysqli_real_escape_string($koneksi, $_POST['id_pekerjaan']);
    $cuaca        = mysqli_real_escape_string($koneksi, $_POST['cuaca']);
    $progres      = mysqli_real_escape_string($koneksi, $_POST['progres']);
    $tenaga_kerja = mysqli_real_escape_string($koneksi, $_POST['tenaga_kerja']);
    $kendala      = mysqli_real_escape_string($koneksi, $_POST['kendala']);

    // PERBAIKAN: Menggunakan form_izin_id sesuai struktur database Anda
    $query = "INSERT INTO laporan_harian (form_izin_id, cuaca, progres_fisik, tenaga_kerja, kendala, pelapor, tanggal) 
              VALUES ('$id_pekerjaan', '$cuaca', '$progres', '$tenaga_kerja', '$kendala', '$username', NOW())";
    
    if (mysqli_query($koneksi, $query)) {
        $msg = "Laporan harian berhasil disimpan!";
    } else {
        $msg = "Gagal menyimpan: " . mysqli_error($koneksi);
    }
}

// Ambil Pekerjaan yang Aktif (Sudah Disetujui)
$sql_aktif = "SELECT id, jenis_pekerjaan, lokasi FROM form_izin_pekerjaan 
              WHERE status = 'Disetujui Pengawas' ORDER BY updated_at DESC";
$res_aktif = mysqli_query($koneksi, $sql_aktif);

// PERBAIKAN QUERY: Mengubah l.id_izin menjadi l.form_izin_id untuk mengatasi Unknown Column
$sql_riwayat = "SELECT l.*, f.jenis_pekerjaan 
                FROM laporan_harian l 
                JOIN form_izin_pekerjaan f ON l.form_izin_id = f.id 
                ORDER BY l.tanggal DESC LIMIT 10";
$res_riwayat = mysqli_query($koneksi, $sql_riwayat);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Harian | CV Cipta Manunggal</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body { margin: 0; font-family: 'Inter', sans-serif; background: #0d0d0e; color: #f3f4f6; }
        .navbar { background: #111111; border-bottom: 1px solid rgba(255,255,255,0.05); padding: 18px 0; position: sticky; top: 0; z-index: 100; }
        .nav-container { max-width: 1200px; margin: auto; display: flex; justify-content: space-between; align-items: center; padding: 0 30px; }
        .logo { font-weight: 600; font-size: 16px; letter-spacing: 1px; color: #fff; text-decoration: none; display: flex; align-items: center; gap: 10px; }
        .logo span { color: #f59e0b; }
        .nav-menu a { color: #d1d5db; text-decoration: none; margin-left: 25px; font-size: 14px; }
        .nav-menu a:hover, .nav-menu a.active { color: #f59e0b; }

        .container { max-width: 1200px; margin: 40px auto; padding: 0 30px; }
        .grid-main { display: grid; grid-template-columns: 1.2fr 1.8fr; gap: 30px; }

        .card { background: #171717; border-radius: 18px; border: 1px solid rgba(255,255,255,0.05); padding: 30px; height: fit-content; }
        .card-title { font-size: 18px; font-weight: 600; color: #f59e0b; margin-bottom: 25px; display: flex; align-items: center; gap: 10px; }

        label { display: block; font-size: 12px; color: #9ca3af; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.5px; }
        .input-dark { 
            width: 100%; background: #0d0d0e; border: 1px solid #262626; color: #fff; 
            padding: 12px; border-radius: 10px; margin-bottom: 20px; font-family: inherit; box-sizing: border-box;
        }
        .input-dark:focus { border-color: #f59e0b; outline: none; }

        .btn-gold { 
            width: 100%; background: #f59e0b; color: #000; border: none; padding: 14px; 
            border-radius: 10px; font-weight: 700; cursor: pointer; transition: 0.3s;
        }
        .btn-gold:hover { background: #fbbf24; box-shadow: 0 0 20px rgba(245, 158, 11, 0.3); }

        .history-item { 
            background: #0d0d0e; border-radius: 12px; padding: 20px; margin-bottom: 15px; 
            border-left: 4px solid #f59e0b; 
        }
        .history-header { display: flex; justify-content: space-between; margin-bottom: 10px; }
        .history-tag { font-size: 11px; background: rgba(245, 158, 11, 0.1); color: #f59e0b; padding: 4px 8px; border-radius: 4px; margin-right: 5px; }
        .history-progres { font-size: 14px; color: #d1d5db; line-height: 1.5; }

        @media (max-width: 992px) { .grid-main { grid-template-columns: 1fr; } }
    </style>
</head>
<body>

<nav class="navbar">
    <div class="nav-container">
        <a href="pengawas_dashboard.php" class="logo">
            <svg style="width:24px; height:24px; stroke:#f59e0b; fill:none; stroke-width:3;" viewBox="0 0 50 50">
                <path d="M10 40 L25 10 L40 40 M15 30 L35 30" />
            </svg>
            CIPTA <span>MANUNGGAL</span>
        </a>
        <div class="nav-menu">
            <a href="pengawas_dashboard.php">Dashboard</a>
            <a href="verifikasi_izin.php">Daftar Izin</a>
            <a href="laporan_harian.php" class="active">Laporan Harian</a>
            <a href="../logout.php" style="color: #ef4444; margin-left: 25px;">Keluar</a>
        </div>
    </div>
</nav>

<div class="container">
    <div style="margin-bottom: 30px;">
        <h1 style="margin: 0; font-size: 26px;">Laporan Harian Lapangan</h1>
        <p style="color: #9ca3af; font-size: 14px;">Dokumentasikan progres fisik dan kendala setiap hari.</p>
    </div>

    <?php if($msg): ?>
        <div style="background: rgba(16, 185, 129, 0.1); border: 1px solid #10b981; color: #10b981; padding: 15px; border-radius: 10px; margin-bottom: 30px;">
            <?php echo $msg; ?>
        </div>
    <?php endif; ?>

    <div class="grid-main">
        <div class="card">
            <div class="card-title">Input Laporan</div>
            <form method="POST">
                <label>Pilih Pekerjaan Aktif</label>
                <select name="id_pekerjaan" class="input-dark" required>
                    <option value="">-- Pilih Proyek --</option>
                    <?php if($res_aktif): ?>
                        <?php while($row = mysqli_fetch_assoc($res_aktif)): ?>
                            <option value="<?php echo $row['id']; ?>"><?php echo $row['jenis_pekerjaan']; ?> (<?php echo $row['lokasi']; ?>)</option>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </select>

                <label>Kondisi Cuaca</label>
                <select name="cuaca" class="input-dark">
                    <option value="Cerah">☀️ Cerah</option>
                    <option value="Berawan">☁️ Berawan</option>
                    <option value="Hujan Ringan">🌦️ Hujan Ringan</option>
                    <option value="Hujan Lebat">⛈️ Hujan Lebat/Berhenti</option>
                </select>

                <label>Tenaga Kerja</label>
                <input type="text" name="tenaga_kerja" class="input-dark" placeholder="Contoh: 5 Tukang, 10 Kenek">

                <label>Uraian Progres Fisik</label>
                <textarea name="progres" class="input-dark" rows="4" placeholder="Apa yang diselesaikan hari ini?" required></textarea>

                <label>Kendala</label>
                <textarea name="kendala" class="input-dark" rows="2" placeholder="Masalah di lapangan..."></textarea>

                <button type="submit" name="simpan_laporan" class="btn-gold">POSTING LAPORAN</button>
            </form>
        </div>

        <div class="card">
            <div class="card-title">Riwayat Laporan Terbaru</div>
            
            <?php if($res_riwayat && mysqli_num_rows($res_riwayat) > 0): ?>
                <?php while($item = mysqli_fetch_assoc($res_riwayat)): ?>
                    <div class="history-item">
                        <div class="history-header">
                            <span style="font-weight: 700; color: #fff; font-size: 14px;"><?php echo $item['jenis_pekerjaan']; ?></span>
                            <span style="font-size: 12px; color: #666;"><?php echo date('d M Y', strtotime($item['tanggal'])); ?></span>
                        </div>
                        <div style="margin-bottom: 10px;">
                            <span class="history-tag"><?php echo $item['cuaca']; ?></span>
                            <span class="history-tag" style="background: rgba(255,255,255,0.05); color: #9ca3af; border: 1px solid #333;">👷 <?php echo $item['tenaga_kerja']; ?></span>
                        </div>
                        <div class="history-progres"><?php echo nl2br(htmlspecialchars($item['progres_fisik'])); ?></div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div style="text-align: center; color: #444; padding: 40px;">Belum ada laporan atau terjadi kesalahan pada database.</div>
            <?php endif; ?>
        </div>
    </div>
</div>

</body>
</html>