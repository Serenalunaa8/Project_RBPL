<?php
session_start();
$koneksi = null;
require_once '../koneksi.php';

if (!isset($koneksi) || !$koneksi) {
    die('Koneksi database gagal: ' . mysqli_connect_error());
}

/* ================= VALIDASI LOGIN ================= */
if (!isset($_SESSION['role']) || $_SESSION['role'] != "kontraktor") {
    header("Location: ../login.php");
    exit;
}

$kontraktor_id = (int)$_SESSION['id'];
$active_page = 'dashboard';

/* ================= MARK AS READ ================= */
mysqli_query($koneksi, "
    UPDATE notifikasi SET status = 'read' 
    WHERE user_id = $kontraktor_id AND status = 'unread'
");

/* ================= HITUNG IZIN MENUNGGU ================= */
$jumlah_notif = mysqli_num_rows(mysqli_query($koneksi, "
    SELECT * FROM form_izin_pekerjaan 
    WHERE kontraktor_id = $kontraktor_id AND status = 'Menunggu Review'
"));

/* ================= AMBIL NOTIF TERBARU ================= */
$notif_query = mysqli_query($koneksi, "
    SELECT *
    FROM notifikasi
    WHERE user_id = $kontraktor_id
    ORDER BY created_at DESC
    LIMIT 5
");

/* ================= STATISTIK ================= */
$stats = mysqli_fetch_assoc(mysqli_query($koneksi, "
    SELECT 
        COUNT(*) as total,
        SUM(status = 'Menunggu Review') as menunggu,
        SUM(status = 'Revisi') as revisi,
        SUM(status = 'Disetujui Pengawas') as disetujui,
        SUM(status = 'Ditolak') as ditolak
    FROM form_izin_pekerjaan
    WHERE kontraktor_id = $kontraktor_id
");
if ($statsQuery) {
    $stats = mysqli_fetch_assoc($statsQuery) ?: $stats;
}

$total     = $stats['total'] ?? 0;
$menunggu  = $stats['menunggu'] ?? 0;
$revisi    = $stats['revisi'] ?? 0;
$disetujui = $stats['disetujui'] ?? 0;
$ditolak   = $stats['ditolak'] ?? 0;

/* ================= ACTIVITY ================= */
$activity = mysqli_query($koneksi, "
    SELECT jenis_pekerjaan, status, created_at
    FROM form_izin_pekerjaan
    WHERE kontraktor_id = $kontraktor_id
    ORDER BY created_at DESC
    LIMIT 5
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Kontraktor | CV Cipta Manunggal</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- CSS KOORDINATOR -->
    <link rel="stylesheet" href="../koordinator_pengawas/asset/koordinator.css">
</head>
<body>

<?php include 'sidebar.php'; ?>

<main class="main">

    <!-- TOPBAR -->
    <div class="topbar">
        <div>
            <h2>Dashboard Kontraktor</h2>
            <div class="section-sub">
                Halo, <?= htmlspecialchars($_SESSION['username']) ?> — Pantau status pengajuan izin pekerjaan Anda
            </div>
        </div>

        <div class="topbar-right">
            <div class="date-chip" id="date-chip"></div>
        </div>
    </div>

    <!-- HEADER -->
    <div class="section-header fade-up">
        <div>
            <div class="section-title">Ringkasan Pengajuan Izin</div>
            <div class="section-sub">
                Monitoring status pengajuan izin pekerjaan kontraktor
            </div>
        </div>
    </div>

    <!-- STATISTIK -->
    <div class="stats-grid fade-up">

        <div class="stat-card">
            <div class="stat-label">Total Pengajuan</div>
            <div class="stat-val"><?= $total ?></div>
            <div class="stat-sub">Semua izin pekerjaan</div>
            <div class="stat-icon">📄</div>
        </div>

        <div class="stat-card">
            <div class="stat-label">Menunggu Review</div>
            <div class="stat-val" style="color:#f59e0b;">
                <?= $menunggu ?>
            </div>
            <div class="stat-sub">Belum ditinjau</div>
            <div class="stat-icon">⏳</div>
        </div>

        <div class="stat-card">
            <div class="stat-label">Perlu Revisi</div>
            <div class="stat-val" style="color:#f97316;">
                <?= $revisi ?>
            </div>
            <div class="stat-sub">Butuh perbaikan</div>
            <div class="stat-icon">✏️</div>
        </div>

        <div class="stat-card">
            <div class="stat-label">Disetujui</div>
            <div class="stat-val" style="color:#22c55e;">
                <?= $disetujui ?>
            </div>
            <div class="stat-sub">Izin diterima</div>
            <div class="stat-icon">✅</div>
        </div>

    </div>

    <!-- AKTIVITAS TERBARU -->
    <div class="panel fade-up">

        <div class="panel-title">
            <svg width="16"
                 height="16"
                 viewBox="0 0 16 16"
                 fill="none"
                 stroke="currentColor"
                 stroke-width="1.5">
                <circle cx="8" cy="8" r="6"/>
                <path d="M8 5v3.5l2 1.5"/>
            </svg>

            Aktivitas Pengajuan Terbaru
        </div>

        <div class="timeline">

            <?php
            $has_activity = false;

            while($row = mysqli_fetch_assoc($activity)):
                $has_activity = true;

                $status = strtolower($row['status']);

                $badge = "badge-wait";
                $dot   = "";

                if(strpos($status,'revisi') !== false){
                    $badge = "badge-review";
                }

                if(strpos($status,'disetujui') !== false){
                    $badge = "badge-done";
                    $dot   = "done";
                }

                if(strpos($status,'ditolak') !== false){
                    $badge = "badge-reject";
                }
            ?>

            <div class="tl-item">

                <div class="tl-dot <?= $dot ?>">
                    •
                </div>

                <div class="tl-content">

                    <p>
                        <?= htmlspecialchars($row['jenis_pekerjaan']) ?>
                    </p>

                    <span>
                        <?= date('d M Y H:i', strtotime($row['created_at'])) ?>
                    </span>

                    <br><br>

                    <span class="badge <?= $badge ?>">
                        <?= htmlspecialchars($row['status']) ?>
                    </span>

                </div>

            </div>

            <?php endwhile; ?>

            <?php if(!$has_activity): ?>
                <div style="text-align:center;color:var(--muted);padding:20px;">
                    Belum ada aktivitas pengajuan izin pekerjaan
                </div>
            <?php endif; ?>

        </div>

    </div>

    <!-- NOTIFIKASI TERBARU -->
    <div class="panel fade-up">

        <div class="panel-title">
            Notifikasi Terbaru
        </div>

        <div class="timeline">

            <?php
            mysqli_data_seek($notif_query, 0);

            if(mysqli_num_rows($notif_query) > 0):

                while($notif = mysqli_fetch_assoc($notif_query)):
            ?>

                <div class="tl-item">

                    <div class="tl-dot">
                        🔔
                    </div>

                    <div class="tl-content">

                        <p>
                            <?= htmlspecialchars($notif['pesan']) ?>
                        </p>

                        <span>
                            <?= date('d M Y H:i', strtotime($notif['created_at'])) ?>
                        </span>

                    </div>

                </div>

            <?php
                endwhile;
            else:
            ?>

                <div style="text-align:center;color:var(--muted);padding:20px;">
                    Tidak ada notifikasi terbaru
                </div>

            <?php endif; ?>

        </div>

    </div>

</main>

<script>
function updateDateChip() {
    const now = new Date();

    const options = {
        weekday: 'short',
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    };

    document.getElementById('date-chip').textContent =
        now.toLocaleDateString('id-ID', options);
}

updateDateChip();
setInterval(updateDateChip, 60000);
</script>

</body>
</html>