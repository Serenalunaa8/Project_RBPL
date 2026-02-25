<?php
session_start();

// Jalur koneksi (Otomatis mendeteksi lokasi file koneksi)
if (file_exists("../config/koneksi.php")) {
    include "../config/koneksi.php";
} elseif (file_exists("../koneksi.php")) {
    include "../koneksi.php";
} elseif (file_exists("koneksi.php")) {
    include "koneksi.php";
} else {
    $koneksi = false;
}

// Proteksi halaman
if (!isset($_SESSION['role']) || ($_SESSION['role'] != "pengawas" && $_SESSION['role'] != "koordinator")) {
    // header("Location: ../login.php"); exit();
}

$msg = "";
$username = isset($_SESSION['username']) ? $_SESSION['username'] : "Supervisor";

// Logika 1: Proses Verifikasi Izin
if ($koneksi && isset($_POST['proses_verifikasi'])) {
    $id_izin = mysqli_real_escape_string($koneksi, $_POST['id_izin']);
    $status  = mysqli_real_escape_string($koneksi, $_POST['status']);
    $catatan = mysqli_real_escape_string($koneksi, $_POST['catatan']);

    $query_update = "UPDATE form_izin_pekerjaan SET 
                    status = '$status', 
                    catatan_pengawas = '$catatan',
                    updated_at = NOW() 
                    WHERE id = '$id_izin'";
    
    if (mysqli_query($koneksi, $query_update)) {
        $msg = "Verifikasi ID #$id_izin berhasil diperbarui.";
    }
}

// Data Statistik
$count_pending = 0; $count_approved = 0; $count_all = 0;
$res_izin = [];

if ($koneksi) {
    $count_pending = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM form_izin_pekerjaan WHERE status = 'Menunggu Review Pengawas'"))['total'];
    $count_approved = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM form_izin_pekerjaan WHERE status = 'Disetujui Pengawas'"))['total'];
    $count_all = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM form_izin_pekerjaan"))['total'];

    $sql_izin = "SELECT f.*, u.username AS nama_kontraktor 
                 FROM form_izin_pekerjaan f 
                 JOIN users u ON f.kontraktor_id = u.id 
                 WHERE f.status = 'Menunggu Review Pengawas' 
                 ORDER BY f.created_at DESC";
    $res_izin = mysqli_query($koneksi, $sql_izin);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Supervisor Dashboard | CV Cipta Manunggal</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            background: #0a0a0a;
            color: #ffffff;
            overflow-x: hidden;
        }

        .dashboard-container { display: flex; min-height: 100vh; }

        /* Sidebar Style */
        .sidebar {
            width: 300px;
            background: #111111;
            padding: 40px 20px;
            border-right: 1px solid rgba(255, 255, 255, 0.05);
            position: fixed;
            height: 100vh;
            z-index: 100;
        }

        /* Logo Brand Container */
        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 50px;
            padding-left: 5px;
        }

        /* Logo SVG - Identik dengan Login Gambar */
        .logo-box {
            width: 32px;
            height: 32px;
            border: 1.5px solid #ffc107;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        .logo-box svg {
            width: 20px;
            height: 20px;
        }

        .brand-text {
            display: flex;
            flex-direction: column;
        }

        .brand-name {
            font-size: 14px;
            font-weight: 700;
            letter-spacing: 0.5px;
            color: #ffffff;
            line-height: 1.2;
        }

        .brand-name span { color: #ffc107; }

        .brand-sub {
            font-size: 10px;
            color: #ffc107;
            font-weight: 600;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        /* Navigation */
        .sidebar nav { display: flex; flex-direction: column; gap: 5px; }

        .sidebar nav a {
            text-decoration: none;
            color: #777;
            padding: 12px 15px;
            border-radius: 6px;
            transition: all 0.3s;
            font-size: 13px;
            font-weight: 500;
        }

        .sidebar nav a:hover, .sidebar nav a.active {
            color: #ffc107;
            background: rgba(255, 193, 7, 0.05);
        }

        .sidebar nav a.active { font-weight: 700; border-left: 3px solid #ffc107; border-radius: 0 6px 6px 0; }

        .logout-link { margin-top: 30px; color: #ff4444 !important; border: 1px solid rgba(255, 68, 68, 0.1); text-align: center; }
        .logout-link:hover { background: #ff4444 !important; color: #fff !important; }

        /* Main Content */
        .main-content {
            flex: 1;
            padding: 40px 60px;
            margin-left: 300px;
            background: radial-gradient(circle at 0% 0%, rgba(255, 193, 7, 0.05) 0%, transparent 50%);
        }

        .header-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
        }

        .welcome-msg h1 { font-size: 24px; font-weight: 700; margin-bottom: 5px; }
        .welcome-msg p { color: #555; font-size: 14px; }

        .role-pill {
            background: rgba(255, 193, 7, 0.1);
            border: 1px solid #ffc107;
            color: #ffc107;
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
        }

        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 40px;
        }

        .card {
            background: #161616;
            padding: 25px;
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.03);
        }

        .stat-label { font-size: 11px; text-transform: uppercase; color: #555; letter-spacing: 1px; margin-bottom: 10px; }
        .stat-value { font-size: 32px; font-weight: 700; color: #ffc107; }

        /* Main Grid */
        .content-grid {
            display: grid;
            grid-template-columns: 1.8fr 1.2fr;
            gap: 30px;
        }

        .section-box {
            background: #161616;
            border-radius: 12px;
            padding: 30px;
            border: 1px solid rgba(255, 255, 255, 0.03);
        }

        .section-header {
            font-size: 14px;
            font-weight: 700;
            color: #ffc107;
            text-transform: uppercase;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .section-header::after { content: ""; flex: 1; height: 1px; background: rgba(255, 255, 255, 0.05); }

        /* Table */
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; padding-bottom: 15px; font-size: 11px; color: #444; text-transform: uppercase; }
        td { padding: 15px 0; border-top: 1px solid rgba(255, 255, 255, 0.03); font-size: 13px; }

        .btn-action {
            background: transparent;
            border: 1px solid #ffc107;
            color: #ffc107;
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: 700;
            cursor: pointer;
            transition: 0.2s;
        }

        .btn-action:hover { background: #ffc107; color: #000; }

        /* Forms */
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; font-size: 11px; color: #555; margin-bottom: 8px; text-transform: uppercase; }
        
        input, select, textarea {
            width: 100%;
            background: #0d0d0d;
            border: 1px solid #222;
            color: #fff;
            padding: 12px;
            border-radius: 6px;
            font-size: 13px;
        }

        input:focus, select:focus, textarea:focus { border-color: #ffc107; outline: none; }

        .btn-submit {
            width: 100%;
            background: #ffc107;
            color: #000;
            border: none;
            padding: 12px;
            border-radius: 6px;
            font-weight: 700;
            cursor: pointer;
            margin-top: 10px;
        }

        @media (max-width: 1100px) {
            .content-grid { grid-template-columns: 1fr; }
            .sidebar { width: 80px; }
            .brand-text, .sidebar nav a span { display: none; }
            .main-content { margin-left: 80px; }
        }
    </style>
</head>
<body>

<div class="dashboard-container">
    <aside class="sidebar">
        <div class="sidebar-brand">
            <!-- Logo Box Minimalis Sesuai Gambar -->
            <div class="logo-box">
                <svg viewBox="0 0 24 24" fill="none" stroke="#ffc107" stroke-width="2">
                    <path d="M4 18V6H8L12 12L16 6H20V18" />
                </svg>
            </div>
            <div class="brand-text">
                <div class="brand-name">CV CIPTA <span>MANUNGGAL</span></div>
                <div class="brand-sub">KONSULTAN</div>
            </div>
        </div>
        
        <nav>
            <a href="#" class="active"><span>Dashboard Utama</span></a>
            <a href="#"><span>Verifikasi Lapangan</span></a>
            <a href="#"><span>Laporan Harian</span></a>
            <a href="#"><span>Manajemen Proyek</span></a>
            <a href="../logout.php" class="logout-link"><span>Logout</span></a>
        </nav>
    </aside>

    <main class="main-content">
        <div class="header-section">
            <div class="welcome-msg">
                <h1>Halo, <?php echo htmlspecialchars($username); ?></h1>
                <p>Pantau progres dan verifikasi izin kerja hari ini.</p>
            </div>
            <div class="role-pill">Pengawas Proyek</div>
        </div>

        <?php if($msg): ?>
            <div style="background: rgba(255, 193, 7, 0.1); border-left: 3px solid #ffc107; padding: 15px; border-radius: 8px; margin-bottom: 25px; font-size: 13px; color: #ffc107;">
                <?php echo $msg; ?>
            </div>
        <?php endif; ?>

        <div class="stats-grid">
            <div class="card">
                <div class="stat-label">Izin Menunggu</div>
                <div class="stat-value"><?php echo $count_pending; ?></div>
            </div>
            <div class="card">
                <div class="stat-label">Pekerjaan Berjalan</div>
                <div class="stat-value"><?php echo $count_approved; ?></div>
            </div>
            <div class="card">
                <div class="stat-label">Total Pengajuan</div>
                <div class="stat-value"><?php echo $count_all; ?></div>
            </div>
        </div>

        <div class="content-grid">
            <!-- Tabel Antrean -->
            <div class="section-box">
                <div class="section-header">Antrean Verifikasi</div>
                <table>
                    <thead>
                        <tr>
                            <th>Item Pekerjaan</th>
                            <th>Kontraktor</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if($koneksi && mysqli_num_rows($res_izin) > 0): ?>
                            <?php while($row = mysqli_fetch_assoc($res_izin)): ?>
                                <tr>
                                    <td>
                                        <div style="font-weight: 600;"><?php echo htmlspecialchars($row['jenis_pekerjaan']); ?></div>
                                        <div style="font-size: 11px; color: #555;"><?php echo htmlspecialchars($row['lokasi']); ?></div>
                                    </td>
                                    <td style="color: #888;"><?php echo htmlspecialchars($row['nama_kontraktor']); ?></td>
                                    <td><button class="btn-action" onclick="showReview(<?php echo $row['id']; ?>)">VERIFIKASI</button></td>
                                </tr>
                                <tr id="rev-<?php echo $row['id']; ?>" style="display:none;">
                                    <td colspan="3" style="background: #0d0d0d; padding: 20px; border-radius: 8px;">
                                        <form method="POST">
                                            <input type="hidden" name="id_izin" value="<?php echo $row['id']; ?>">
                                            <div class="form-group">
                                                <label>Keputusan</label>
                                                <select name="status">
                                                    <option value="Disetujui Pengawas">Setujui</option>
                                                    <option value="Revisi">Kembalikan (Revisi)</option>
                                                    <option value="Ditolak">Tolak</option>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label>Catatan untuk Kontraktor</label>
                                                <textarea name="catatan" rows="2"></textarea>
                                            </div>
                                            <button type="submit" name="proses_verifikasi" class="btn-submit">Simpan Hasil</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="3" style="text-align: center; color: #333; padding: 40px;">Tidak ada data masuk</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Form Laporan Cepat -->
            <div class="section-box">
                <div class="section-header">Laporan Harian Cepat</div>
                <form>
                    <div class="form-group">
                        <label>Target Progres (%)</label>
                        <input type="number" placeholder="Contoh: 75">
                    </div>
                    <div class="form-group">
                        <label>Kendala Lapangan</label>
                        <textarea rows="4" placeholder="Sebutkan kendala jika ada..."></textarea>
                    </div>
                    <button type="button" class="btn-submit" style="background: transparent; border: 1px solid #ffc107; color: #ffc107;">Kirim Update</button>
                </form>
            </div>
        </div>
    </main>
</div>

<script>
    function showReview(id) {
        const el = document.getElementById('rev-' + id);
        el.style.display = (el.style.display === 'none') ? 'table-row' : 'none';
    }
</script>

</body>
</html>