<?php
session_start();
if ($_SESSION['role'] != "teamleader") {
    header("Location: ../login.php");
    exit;
}
$username = $_SESSION['username'] ?? 'Team Leader';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Review Laporan Mingguan | Cipta Manunggal</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="asset/teamleader.css">
</head>
<body>

<div class="dashboard-container">

    <!-- SIDEBAR -->
    <aside class="sidebar">
        <div class="sidebar-brand">
            <svg viewBox="0 0 120 120" class="logo-arch">
                <rect x="10" y="10" width="100" height="100"/>
                <path d="M35 80 V40 H60"/>
                <path d="M60 40 L75 60 L90 40 V80"/>
            </svg>
            <div class="brand-name">CIPTA<br><span>MANUNGGAL</span></div>
        </div>

        <nav>
            <a href="teamleader.php">
                <span class="nav-icon">⊞</span> Dashboard
            </a>
            <a href="review_laporan_mingguan.php" class="active">
                <span class="nav-icon">📋</span> Review Laporan Mingguan
            </a>
            <a href="evaluasi_laporan_bulanan.php">
                <span class="nav-icon">📊</span> Evaluasi Laporan Bulanan
            </a>
            <a href="riwayat_laporan_bulanan.php">
                <span class="nav-icon">🗂</span> Riwayat Evaluasi
            </a>
        </nav>

        <div class="logout-link">
            <a href="../logout.php">
                <span class="nav-icon">↩</span> Logout
            </a>
        </div>
    </aside>

    <!-- MAIN -->
    <main class="main-content">

        <header class="topbar">
            <div class="topbar-left">
                <h1>Halo, <?php echo htmlspecialchars($username); ?></h1>
                <p>Tinjau dan validasi laporan mingguan dari Koordinator Pengawas.</p>
            </div>
            <span class="role-badge">TEAM LEADER</span>
        </header>
        <section class="stats">
            <div class="stat-card">
                <h3>12</h3>
                <p>Total Laporan Mingguan</p>
            </div>
            <div class="stat-card">
                <h3>3</h3>
                <p>Menunggu Review</p>
            </div>
            <div class="stat-card">
                <h3>8</h3>
                <p>Sudah Ditinjau</p>
            </div>
            <div class="stat-card">
                <h3>1</h3>
                <p>Diminta Revisi</p>
            </div>
        </section>

        <!-- FILTER -->
        <div class="filter-bar">
            <select>
                <option>Semua Status</option>
                <option>Menunggu Review</option>
                <option>Sudah Ditinjau</option>
                <option>Perlu Revisi</option>
            </select>
            <select>
                <option>Semua Minggu</option>
                <option>Minggu ke-1 April 2026</option>
                <option>Minggu ke-4 Maret 2026</option>
                <option>Minggu ke-3 Maret 2026</option>
            </select>
            <input type="text" placeholder="Cari koordinator...">
        </div>

        <!-- TABLE -->
        <div class="table-card">
            <div class="table-card-header">
                <h2>Daftar Laporan Mingguan</h2>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Periode</th>
                        <th>Koordinator</th>
                        <th>Ringkasan Progres</th>
                        <th>Tgl Dibuat</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Minggu ke-1 Apr 2026</td>
                        <td>Koordinator A</td>
                        <td>Pengecoran kolom Lt.3 selesai 80%</td>
                        <td>02 Apr 2026</td>
                        <td><span class="badge badge-pending">Menunggu</span></td>
                        <td>
                            <a href="#" class="btn btn-primary btn-sm" onclick="showDetail(this)">Tinjau</a>
                        </td>
                    </tr>
                    <tr>
                        <td>Minggu ke-4 Mar 2026</td>
                        <td>Koordinator B</td>
                        <td>Pemasangan bekisting dan tulangan plat</td>
                        <td>29 Mar 2026</td>
                        <td><span class="badge badge-pending">Menunggu</span></td>
                        <td>
                            <a href="#" class="btn btn-primary btn-sm" onclick="showDetail(this)">Tinjau</a>
                        </td>
                    </tr>
                    <tr>
                        <td>Minggu ke-3 Mar 2026</td>
                        <td>Koordinator A</td>
                        <td>Penulangan plat lantai 2 selesai 100%</td>
                        <td>22 Mar 2026</td>
                        <td><span class="badge badge-approved">Ditinjau</span></td>
                        <td>
                            <a href="#" class="btn btn-secondary btn-sm">Detail</a>
                        </td>
                    </tr>
                    <tr>
                        <td>Minggu ke-2 Mar 2026</td>
                        <td>Koordinator B</td>
                        <td>Erection kolom dan balok lantai 2</td>
                        <td>15 Mar 2026</td>
                        <td><span class="badge badge-revision">Perlu Revisi</span></td>
                        <td>
                            <a href="#" class="btn btn-secondary btn-sm">Detail</a>
                        </td>
                    </tr>
                    <tr>
                        <td>Minggu ke-1 Mar 2026</td>
                        <td>Koordinator A</td>
                        <td>Pekerjaan pondasi dan galian tanah</td>
                        <td>08 Mar 2026</td>
                        <td><span class="badge badge-approved">Ditinjau</span></td>
                        <td>
                            <a href="#" class="btn btn-secondary btn-sm">Detail</a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- DETAIL PANEL (toggled) -->
        <div id="detailPanel" class="eval-panel" style="display:none;">
            <h3>📋 Detail Laporan Mingguan</h3>
            <div class="detail-grid" style="margin-bottom: 24px;">
                <div class="detail-item">
                    <label>Periode</label>
                    <p>Minggu ke-1 April 2026</p>
                </div>
                <div class="detail-item">
                    <label>Koordinator Pengawas</label>
                    <p>Koordinator A</p>
                </div>
                <div class="detail-item">
                    <label>Tanggal Dibuat</label>
                    <p>02 April 2026</p>
                </div>
                <div class="detail-item">
                    <label>Status Saat Ini</label>
                    <p><span class="badge badge-pending">Menunggu Review</span></p>
                </div>
                <div class="detail-item" style="grid-column:span 2;">
                    <label>Ringkasan Progres Pekerjaan</label>
                    <p>Pengecoran kolom Lt.3 telah mencapai 80% dari target mingguan. Pemasangan bekisting berjalan sesuai jadwal. Tenaga kerja aktif rata-rata 24 orang per hari.</p>
                </div>
                <div class="detail-item" style="grid-column:span 2;">
                    <label>Temuan Pengawasan</label>
                    <p>Ditemukan retak minor pada bekisting kolom C4, telah dilakukan perbaikan pada hari yang sama. Cuaca hujan 2 hari menyebabkan penundaan 0,5 hari kerja.</p>
                </div>
                <div class="detail-item" style="grid-column:span 2;">
                    <label>Analisis Pencapaian & Kendala</label>
                    <p>Secara keseluruhan pencapaian minggu ini memenuhi 90% dari target sprint. Kendala utama adalah keterlambatan pengiriman semen dari supplier, telah dikomunikasikan ke manajemen.</p>
                </div>
            </div>

            <div style="border-top: 1px solid var(--border); padding-top: 20px;">
                <p style="font-size:13px; color: var(--text-muted); margin-bottom: 16px;">Berikan catatan evaluasi untuk laporan ini sebelum meneruskan ke evaluasi bulanan:</p>
                <div class="form-group" style="margin-bottom: 16px;">
                    <label>Catatan Team Leader (opsional)</label>
                    <textarea placeholder="Tulis catatan atau rekomendasi tindak lanjut..."></textarea>
                </div>
                <div style="display:flex; gap:10px;">
                    <button class="btn btn-primary" onclick="alert('Laporan berhasil ditandai Sudah Ditinjau.')">✓ Tandai Sudah Ditinjau</button>
                    <button class="btn btn-danger" onclick="alert('Laporan dikirim kembali untuk revisi.')">↩ Minta Revisi</button>
                    <button class="btn btn-secondary" onclick="document.getElementById('detailPanel').style.display='none'">Tutup</button>
                </div>
            </div>
        </div>

    </main>
</div>

<script>
function showDetail(btn) {
    event.preventDefault();
    const panel = document.getElementById('detailPanel');
    panel.style.display = panel.style.display === 'none' ? 'block' : 'none';
    if (panel.style.display === 'block') {
        panel.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
}
</script>

</body>
</html>