<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != "kontraktor") {
    header("Location: ../login.php");
    exit;
}

require_once '../koneksi.php'; 

if (!isset($_SESSION['id'])) {
    die("Session kontraktor tidak ditemukan. Silakan login ulang.");
}

$kontraktor_id = $_SESSION['id']; 

// Ambil semua pengajuan izin milik kontraktor ini menggunakan MySQLi
$query = "SELECT * FROM form_izin_pekerjaan 
          WHERE id = ? 
          ORDER BY created_at DESC";

$stmt = mysqli_prepare($koneksi, $query);
mysqli_stmt_bind_param($stmt, "i", $kontraktor_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$izin_list = mysqli_fetch_all($result, MYSQLI_ASSOC);
mysqli_stmt_close($stmt);

// Hitung statistik
$total    = count($izin_list);
$disetujui = 0; $pending = 0; $ditolak = 0; $review = 0;
foreach ($izin_list as $row) {
    $s = strtolower($row['status'] ?? '');
    if (str_contains($s, 'setuju') || str_contains($s, 'approved')) $disetujui++;
    elseif (str_contains($s, 'tolak') || str_contains($s, 'reject'))  $ditolak++;
    elseif (str_contains($s, 'review'))                                $review++;
    else                                                               $pending++;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Status Izin | CV Cipta Manunggal Konsultan</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="asset/kontraktordash.css">
</head>
<body>


   <?php
   $active_page = 'status';
include 'sidebar.php';
?>

    <!-- MAIN CONTENT -->
    <main class="main">

        <header class="topbar">
            <div>
                <h1>Status Izin Pekerjaan</h1>
                <p>Pantau semua pengajuan izin yang telah kamu kirimkan</p>
            </div>
            <div class="role-badge">KONTRAKTOR</div>
        </header>

        <!-- STATISTIK -->
        <section class="stats">
            <div class="stat-card">
                <h3><?= $total ?></h3>
                <p>Total Pengajuan</p>
            </div>
            <div class="stat-card">
                <h3><?= $pending ?></h3>
                <p>Menunggu</p>
            </div>
            <div class="stat-card">
                <h3><?= $review ?></h3>
                <p>Dalam Review</p>
            </div>
            <div class="stat-card">
                <h3><?= $disetujui ?></h3>
                <p>Disetujui</p>
            </div>
            <div class="stat-card">
                <h3><?= $ditolak ?></h3>
                <p>Ditolak</p>
            </div>
        </section>

        <!-- FILTER -->
        <div class="filter-bar">
            <input type="text" id="searchInput" placeholder="🔍  Cari pekerjaan, material, lokasi...">
            <select id="filterStatus">
                <option value="">Semua Status</option>
                <option value="disetujui">Disetujui</option>
                <option value="review">Dalam Review</option>
                <option value="pending">Menunggu</option>
                <option value="ditolak">Ditolak</option>
            </select>
        </div>

        <!-- CARDS -->
        <div class="section-title">
            Riwayat Pengajuan <span id="countLabel"><?= $total ?> izin</span>
        </div>

        <?php if (empty($izin_list)): ?>
            <div class="empty-state">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414A1 1 0 0119 9.414V19a2 2 0 01-2 2z"/>
                </svg>
                <p>Belum ada pengajuan izin yang ditemukan.</p>
            </div>
        <?php else: ?>
            <div class="cards-grid" id="cardsGrid">
                <?php foreach ($izin_list as $row):
                    $status_raw = strtolower($row['status'] ?? 'pending');
                    if (str_contains($status_raw, 'setuju') || str_contains($status_raw, 'approved')) {
                        $status_key = 'disetujui'; $badge_class = 'badge-disetujui'; $status_label = 'Disetujui';
                    } elseif (str_contains($status_raw, 'tolak') || str_contains($status_raw, 'reject')) {
                        $status_key = 'ditolak'; $badge_class = 'badge-ditolak'; $status_label = 'Ditolak';
                    } elseif (str_contains($status_raw, 'review')) {
                        $status_key = 'review'; $badge_class = 'badge-review'; $status_label = 'Dalam Review';
                    } else {
                        $status_key = 'pending'; $badge_class = 'badge-pending'; $status_label = 'Menunggu';
                    }

                    $tgl_mulai    = $row['tanggal_mulai']    ? date('d M Y', strtotime($row['tanggal_mulai']))    : '-';
                    $tgl_selesai  = $row['tanggal_selesai']  ? date('d M Y', strtotime($row['tanggal_selesai']))  : '-';
                    $created_at   = $row['created_at']       ? date('d M Y, H:i', strtotime($row['created_at']))  : '-';
                ?>
                <div class="izin-card status-<?= $status_key ?>" data-status="<?= $status_key ?>" data-search="<?= strtolower(htmlspecialchars($row['jenis_pekerjaan'].' '.$row['material'].' '.$row['lokasi'])) ?>">

                    <div class="card-header">
                        <span class="card-id">#IZN-<?= str_pad($row['id'], 4, '0', STR_PAD_LEFT) ?></span>
                        <span class="status-badge <?= $badge_class ?>"><?= $status_label ?></span>
                    </div>

                    <div class="card-title"><?= htmlspecialchars($row['jenis_pekerjaan']) ?></div>

                    <div class="card-meta">
                        <div class="meta-item">
                            <label>Material</label>
                            <span><?= htmlspecialchars($row['material'] ?? '-') ?></span>
                        </div>
                        <div class="meta-item">
                            <label>Lokasi</label>
                            <span><?= htmlspecialchars($row['lokasi'] ?? '-') ?></span>
                        </div>
                        <div class="meta-item">
                            <label>Volume</label>
                            <span><?= htmlspecialchars($row['volume'] ?? '-') ?> <?= htmlspecialchars($row['satuan'] ?? '') ?></span>
                        </div>
                        <div class="meta-item">
                            <label>Metode Kerja</label>
                            <span><?= htmlspecialchars($row['metode_kerja'] ?? '-') ?></span>
                        </div>
                        <div class="meta-item">
                            <label>Mulai</label>
                            <span><?= $tgl_mulai ?></span>
                        </div>
                        <div class="meta-item">
                            <label>Selesai</label>
                            <span><?= $tgl_selesai ?></span>
                        </div>
                    </div>

                    <?php if (isset($row['catatan']) && !empty($row['catatan'])): ?>
                    <div class="catatan-box">
                        <strong>Catatan Pengawas</strong>
                        <?= htmlspecialchars($row['catatan']) ?>
                    </div>
                    <?php endif; ?>

                    <div class="card-footer">
                        <span class="date">Diajukan: <?= $created_at ?></span>
                        <?php if (!empty($row['dokumen'])): ?>
                            <a href="../uploads/<?= htmlspecialchars($row['dokumen']) ?>" target="_blank" class="btn-detail">Lihat Dokumen</a>
                        <?php else: ?>
                            <span style="font-size:12px;color:#444">Tanpa Dokumen</span>
                        <?php endif; ?>
                    </div>

                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </main>
</div>

<script>
    const searchInput  = document.getElementById('searchInput');
    const filterStatus = document.getElementById('filterStatus');
    const cards        = document.querySelectorAll('.izin-card');
    const countLabel   = document.getElementById('countLabel');

    function applyFilter() {
        const q      = searchInput.value.toLowerCase();
        const status = filterStatus.value;
        let visible  = 0;

        cards.forEach(card => {
            const matchSearch = !q || card.dataset.search.includes(q);
            const matchStatus = !status || card.dataset.status === status;
            const show = matchSearch && matchStatus;
            card.style.display = show ? '' : 'none';
            if (show) visible++;
        });

        countLabel.textContent = visible + ' izin';
    }

    searchInput.addEventListener('input', applyFilter);
    filterStatus.addEventListener('change', applyFilter);
</script>

</body>
</html>