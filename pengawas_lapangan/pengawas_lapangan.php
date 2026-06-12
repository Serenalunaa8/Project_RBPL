<?php
session_start();
include "../koneksi.php";

/* ================== CEK ROLE ================== */
if (!isset($_SESSION['role']) || $_SESSION['role'] != "pengawas") {
    header("Location: ../login.php");
    exit;
}

if (!isset($koneksi) || !$koneksi) {
    die("Koneksi database gagal. Periksa file koneksi.php dan jalankan MySQL.");
}

/* ================== STATISTIK ================== */
$stats_query = "
    SELECT 
        COUNT(*) as total,
        SUM(status='Menunggu Review') as menunggu,
        SUM(status='Disetujui') as disetujui,
        SUM(status='Ditolak') as ditolak,
        SUM(status='Dalam Verifikasi') as verifikasi
    FROM form_izin_pekerjaan
";
$stats_result = mysqli_query($koneksi, $stats_query);
if (!$stats_result) {
    die("Query statistik gagal: " . mysqli_error($koneksi));
}
$stats = mysqli_fetch_assoc($stats_result);

$total = $stats['total'] ?? 0;
$menunggu = $stats['menunggu'] ?? 0;
$disetujui = $stats['disetujui'] ?? 0;
$ditolak = $stats['ditolak'] ?? 0;
$verifikasi = $stats['verifikasi'] ?? 0;

$active_page = 'dashboard';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pengawas Lapangan | CV Cipta Manunggal Konsultan</title>
    <link rel="stylesheet" href="pengawas.css">
</head>
<body>

<?php include 'sidebar.php'; ?>

<main class="main">

    <!-- TOPBAR -->
    <div class="topbar">
        <div>
            <h2>Dashboard Pengawas Lapangan</h2>
            <div class="section-sub">Pantau dan kelola verifikasi izin pekerjaan lapangan</div>
        </div>
    </div>

    <!-- SECTION HEADER -->
    <div class="section-header fade-up">
        <div>
            <div class="section-title">Ringkasan Aktivitas</div>
            <div class="section-sub">Verifikasi pengajuan izin dan laporan pekerjaan lapangan</div>
        </div>
    </div>

    <!-- STATS -->
    <div class="stats-grid fade-up" style="animation-delay:.04s; display: grid; grid-template-columns: repeat(5, 1fr); gap: 20px;">
        <div class="stat-card">
            <div class="stat-label">Total Pengajuan</div>
            <div class="stat-val"><?= $total ?></div>
            <div class="stat-sub">Semua izin pekerjaan</div>
            <div class="stat-icon">📋</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Menunggu Verifikasi</div>
            <div class="stat-val" style="color: #f59e0b;"><?= $menunggu ?></div>
            <div class="stat-sub">Perlu ditinjau</div>
            <div class="stat-icon">⏳</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Dalam Verifikasi</div>
            <div class="stat-val" style="color: #60a5fa;"><?= $verifikasi ?></div>
            <div class="stat-sub">Sedang diproses</div>
            <div class="stat-icon">🔄</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Disetujui</div>
            <div class="stat-val" style="color: #22c55e;"><?= $disetujui ?></div>
            <div class="stat-sub">Sudah diverifikasi</div>
            <div class="stat-icon">✅</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Ditolak</div>
            <div class="stat-val" style="color: #ef4444;"><?= $ditolak ?></div>
            <div class="stat-sub">Tidak disetujui</div>
            <div class="stat-icon">❌</div>
        </div>
    </div>

     <!-- GRID IZIN -->
        <section class="grid-section">
            <h2 style="font-size: 18px; font-weight: 600; margin-bottom: 24px; display: flex; align-items: center; gap: 10px;">
                <span style="display: inline-block; width: 3px; height: 18px; background: #ffc107; border-radius: 2px;"></span>
                Daftar Izin
            </h2>
            <div class="card-grid">

                <?php
                $q = mysqli_query($koneksi,"SELECT f.*, u.username 
                FROM form_izin_pekerjaan f 
                LEFT JOIN users u ON f.kontraktor_id=u.id
                ORDER BY 
                    CASE WHEN f.status = 'Menunggu Review' THEN 1
                         WHEN f.status = 'Dalam Verifikasi' THEN 2
                         WHEN f.status = 'Disetujui' THEN 3
                         WHEN f.status = 'Ditolak' THEN 4
                         ELSE 5
                    END,
                    f.created_at DESC LIMIT 10");

                if(mysqli_num_rows($q) > 0) {
                    while($d = mysqli_fetch_assoc($q)):
                        $status_lower = strtolower($d['status']);
                        $status_class = '';
                        
                       
if(strpos($status_lower, 'menunggu') !== false) {
    $status_class = 'menunggu';
} elseif(strpos($status_lower, 'disetujui') !== false) {
    $status_class = 'disetujui';
} elseif(strpos($status_lower, 'ditolak') !== false) {
    $status_class = 'ditolak';
} elseif(strpos($status_lower, 'revisi') !== false) {
    $status_class = 'revisi';
}
                ?>

                <div class="izin-card">
                    <h3><?= htmlspecialchars($d['jenis_pekerjaan']) ?></h3>
                    <p><b>Kontraktor:</b> <span><?= htmlspecialchars($d['username'] ?? '-') ?></span></p>
                    <p><b>Lokasi:</b> <span><?= htmlspecialchars($d['lokasi']) ?></span></p>
                    <p><b>Volume:</b> <span><?= htmlspecialchars($d['volume']) ?></span></p>
                    <p style="margin-bottom: 16px;"><b>Tanggal:</b> <span><?= date('d M Y', strtotime($d['created_at'])) ?></span></p>

                    <span class="status <?= $status_class ?>">
                        <?= htmlspecialchars($d['status']) ?>
                    </span>
                </div>

                <?php endwhile;
                } else {
                    echo '<div style="grid-column: 1/-1; text-align: center; padding: 40px; color: #666;">Tidak ada data izin pekerjaan</div>';
                }
                ?>

            </div>
        </section>

    </main>
</div>

<script>
setTimeout(()=>{
    const n = document.getElementById("notif");
    if(n) n.style.display="none";
},5000);
</script>

</body>
</html>