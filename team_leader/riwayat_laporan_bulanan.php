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
    <title>Riwayat Evaluasi Bulanan | Cipta Manunggal</title>
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
            <a href="review_laporan_mingguan.php">
                <span class="nav-icon">📋</span> Review Laporan Mingguan
            </a>
            <a href="evaluasi_laporan_bulanan.php">
                <span class="nav-icon">📊</span> Evaluasi Laporan Bulanan
            </a>
            <a href="riwayat_laporan_bulanan.php" class="active">
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
                <h1>Riwayat Evaluasi Bulanan</h1>
                <p>Arsip resmi seluruh laporan bulanan yang telah dievaluasi.</p>
            </div>
            <span class="role-badge">TEAM LEADER</span>
        </header>
        <section class="stats">
            <div class="stat-card">
                <h3>6</h3>
                <p>Total Evaluasi</p>
            </div>
            <div class="stat-card">
                <h3>5</h3>
                <p>Disetujui</p>
            </div>
            <div class="stat-card">
                <h3>1</h3>
                <p>Pernah Direvisi</p>
            </div>
            <div class="stat-card">
                <h3>0</h3>
                <p>Menunggu</p>
            </div>
        </section>

        <!-- FILTER -->
        <div class="filter-bar">
            <select>
                <option>Semua Status</option>
                <option>Disetujui</option>
                <option>Pernah Direvisi</option>
            </select>
            <select>
                <option>Semua Tahun</option>
                <option>2026</option>
                <option>2025</option>
            </select>
            <input type="text" placeholder="Cari periode atau koordinator...">
        </div>

        <!-- TABLE -->
        <div class="table-card">
            <div class="table-card-header">
                <h2>Arsip Evaluasi Laporan Bulanan</h2>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Periode</th>
                        <th>Koordinator</th>
                        <th>Lap. Mingguan</th>
                        <th>Tgl Evaluasi</th>
                        <th>Keputusan</th>
                        <th>Dievaluasi Oleh</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong style="color:var(--text-primary)">Februari 2026</strong></td>
                        <td>Koordinator A</td>
                        <td>4 laporan</td>
                        <td>03 Mar 2026</td>
                        <td><span class="badge badge-approved">Disetujui</span></td>
                        <td><?php echo htmlspecialchars($username); ?></td>
                        <td>
                            <button class="btn btn-secondary btn-sm" onclick="showDetail('feb26')">Detail</button>
                        </td>
                    </tr>
                    <tr>
                        <td><strong style="color:var(--text-primary)">Januari 2026</strong></td>
                        <td>Koordinator B</td>
                        <td>4 laporan</td>
                        <td>04 Feb 2026</td>
                        <td><span class="badge badge-revision">Pernah Revisi</span></td>
                        <td><?php echo htmlspecialchars($username); ?></td>
                        <td>
                            <button class="btn btn-secondary btn-sm" onclick="showDetail('jan26')">Detail</button>
                        </td>
                    </tr>
                    <tr>
                        <td><strong style="color:var(--text-primary)">Desember 2025</strong></td>
                        <td>Koordinator A</td>
                        <td>5 laporan</td>
                        <td>05 Jan 2026</td>
                        <td><span class="badge badge-approved">Disetujui</span></td>
                        <td><?php echo htmlspecialchars($username); ?></td>
                        <td>
                            <button class="btn btn-secondary btn-sm" onclick="showDetail('des25')">Detail</button>
                        </td>
                    </tr>
                    <tr>
                        <td><strong style="color:var(--text-primary)">November 2025</strong></td>
                        <td>Koordinator A, B</td>
                        <td>4 laporan</td>
                        <td>03 Des 2025</td>
                        <td><span class="badge badge-approved">Disetujui</span></td>
                        <td><?php echo htmlspecialchars($username); ?></td>
                        <td>
                            <button class="btn btn-secondary btn-sm" onclick="showDetail('nov25')">Detail</button>
                        </td>
                    </tr>
                    <tr>
                        <td><strong style="color:var(--text-primary)">Oktober 2025</strong></td>
                        <td>Koordinator B</td>
                        <td>4 laporan</td>
                        <td>04 Nov 2025</td>
                        <td><span class="badge badge-approved">Disetujui</span></td>
                        <td><?php echo htmlspecialchars($username); ?></td>
                        <td>
                            <button class="btn btn-secondary btn-sm" onclick="showDetail('okt25')">Detail</button>
                        </td>
                    </tr>
                    <tr>
                        <td><strong style="color:var(--text-primary)">September 2025</strong></td>
                        <td>Koordinator A</td>
                        <td>4 laporan</td>
                        <td>01 Okt 2025</td>
                        <td><span class="badge badge-approved">Disetujui</span></td>
                        <td><?php echo htmlspecialchars($username); ?></td>
                        <td>
                            <button class="btn btn-secondary btn-sm" onclick="showDetail('sep25')">Detail</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- DETAIL PANEL -->
        <div id="detailPanel" class="eval-panel" style="display:none;">
            <h3 id="detailTitle">📄 Detail Evaluasi</h3>
            <div id="detailContent" class="detail-grid" style="margin-bottom: 20px;">
                <!-- diisi via JS -->
            </div>
            <div style="border-top: 1px solid var(--border); padding-top: 16px; display:flex; gap:10px;">
                <button class="btn btn-secondary btn-sm" onclick="document.getElementById('detailPanel').style.display='none'">Tutup</button>
            </div>
        </div>

    </main>
</div>

<script>
const evalData = {
    feb26: {
        title: 'Evaluasi — Februari 2026',
        periode: 'Februari 2026 (01 — 28 Feb 2026)',
        koordinator: 'Koordinator A',
        tanggal: '03 Maret 2026',
        keputusan: '<span class="badge badge-approved">Disetujui</span>',
        penilaian: 'Kinerja bulan Februari sangat baik. Seluruh target pekerjaan struktural terpenuhi sesuai jadwal rencana.',
        capaian: 'Penyelesaian kolom Lt.2 (100%), balok induk Lt.2 (100%), plat lantai Lt.2 (85%).',
        kendala: 'Cuaca hujan pada minggu ke-3 menyebabkan penundaan 1 hari. Telah diatasi dengan lembur terkontrol.',
        catatan_revisi: '-'
    },
    jan26: {
        title: 'Evaluasi — Januari 2026',
        periode: 'Januari 2026 (01 — 31 Jan 2026)',
        koordinator: 'Koordinator B',
        tanggal: '04 Februari 2026',
        keputusan: '<span class="badge badge-revision">Pernah Direvisi → Disetujui</span>',
        penilaian: 'Kinerja bulan Januari cukup baik namun terdapat beberapa ketidaksesuaian dokumentasi yang perlu diperbaiki.',
        capaian: 'Pekerjaan pondasi selesai 100%. Erection kolom Lt.1 mencapai 70%.',
        kendala: 'Keterlambatan material beton precast dari supplier menyebabkan penundaan 3 hari kerja.',
        catatan_revisi: 'Lengkapi dokumentasi foto harian untuk minggu ke-2 dan ke-3. Perbaiki format laporan tenaga kerja.'
    }
};

function showDetail(id) {
    const data = evalData[id];
    if (!data) {
        // generic fallback
        document.getElementById('detailTitle').textContent = '📄 Detail Evaluasi';
        document.getElementById('detailContent').innerHTML = '<p style="color:var(--text-muted); grid-column:span 2;">Data detail tidak tersedia untuk periode ini.</p>';
        document.getElementById('detailPanel').style.display = 'block';
        document.getElementById('detailPanel').scrollIntoView({ behavior: 'smooth' });
        return;
    }

    document.getElementById('detailTitle').textContent = '📄 ' + data.title;
    document.getElementById('detailContent').innerHTML = `
        <div class="detail-item">
            <label>Periode</label>
            <p>${data.periode}</p>
        </div>
        <div class="detail-item">
            <label>Koordinator Pengawas</label>
            <p>${data.koordinator}</p>
        </div>
        <div class="detail-item">
            <label>Tanggal Evaluasi</label>
            <p>${data.tanggal}</p>
        </div>
        <div class="detail-item">
            <label>Keputusan</label>
            <p>${data.keputusan}</p>
        </div>
        <div class="detail-item" style="grid-column:span 2;">
            <label>Deskripsi Penilaian</label>
            <p>${data.penilaian}</p>
        </div>
        <div class="detail-item">
            <label>Capaian Utama</label>
            <p>${data.capaian}</p>
        </div>
        <div class="detail-item">
            <label>Kendala & Tindak Lanjut</label>
            <p>${data.kendala}</p>
        </div>
        <div class="detail-item" style="grid-column:span 2;">
            <label>Catatan Revisi</label>
            <p>${data.catatan_revisi}</p>
        </div>
    `;

    const panel = document.getElementById('detailPanel');
    panel.style.display = 'block';
    panel.scrollIntoView({ behavior: 'smooth', block: 'start' });
}
</script>

</body>
</html>