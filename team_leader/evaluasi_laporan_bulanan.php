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
    <title>Evaluasi Laporan Bulanan | Cipta Manunggal</title>
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
            <a href="evaluasi_laporan_bulanan.php" class="active">
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
                <h1>Evaluasi Laporan Bulanan</h1>
                <p>Nilai kinerja proyek dan berikan keputusan persetujuan atau revisi.</p>
            </div>
            <span class="role-badge">TEAM LEADER</span>
        </header>

        <!-- DAFTAR LAPORAN BULANAN -->
        <div class="table-card" style="margin-bottom: 28px;">
            <div class="table-card-header">
                <h2>Laporan Bulanan Tersedia</h2>
                <div style="display:flex; gap:10px;">
                    <select style="background:rgba(255,255,255,0.05); border:1px solid var(--border); border-radius:8px; padding:7px 12px; color:#ccc; font-size:13px;">
                        <option>Semua Bulan</option>
                        <option>Maret 2026</option>
                        <option>Februari 2026</option>
                    </select>
                </div>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Periode Bulanan</th>
                        <th>Laporan Mingguan</th>
                        <th>Koordinator</th>
                        <th>Tgl Dibuat</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr id="row-mar26">
                        <td><strong style="color:var(--text-primary);">Maret 2026</strong></td>
                        <td>4 laporan mingguan</td>
                        <td>Koordinator A, B</td>
                        <td>01 Apr 2026</td>
                        <td><span class="badge badge-pending">Belum Dievaluasi</span></td>
                        <td>
                            <button class="btn btn-primary btn-sm" onclick="toggleForm('mar26')">Evaluasi</button>
                        </td>
                    </tr>
                    <tr>
                        <td><strong style="color:var(--text-primary);">Februari 2026</strong></td>
                        <td>4 laporan mingguan</td>
                        <td>Koordinator A</td>
                        <td>01 Mar 2026</td>
                        <td><span class="badge badge-approved">Disetujui</span></td>
                        <td>
                            <a href="riwayat_laporan_bulanan.php" class="btn btn-secondary btn-sm">Lihat Arsip</a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- FORM EVALUASI (toggled) -->
        <div id="formEval-mar26" class="eval-panel" style="display:none;">
            <h3>📊 Form Evaluasi — Maret 2026</h3>

            <!-- Rekap laporan mingguan -->
            <div class="alert alert-info" style="margin-bottom: 20px;">
                <span>ℹ</span>
                Laporan ini merangkum <strong>4 laporan mingguan</strong> periode Maret 2026 yang telah ditinjau.
            </div>

            <div class="detail-grid" style="margin-bottom: 24px; padding-bottom: 20px; border-bottom: 1px solid var(--border);">
                <div class="detail-item">
                    <label>Periode Evaluasi</label>
                    <p>Maret 2026 (01 — 31 Maret 2026)</p>
                </div>
                <div class="detail-item">
                    <label>Jumlah Laporan Harian</label>
                    <p>23 laporan harian tervalidasi</p>
                </div>
                <div class="detail-item">
                    <label>Rata-rata Tenaga Kerja / Hari</label>
                    <p>22 orang</p>
                </div>
                <div class="detail-item">
                    <label>Total Hari Kerja Efektif</label>
                    <p>23 dari 26 hari kerja</p>
                </div>
            </div>

            <!-- Form input evaluasi -->
            <form onsubmit="submitEval(event)">
                <div class="form-grid" style="margin-bottom: 20px;">
                    <div class="form-group">
                        <label>Judul / Periode Evaluasi</label>
                        <input type="text" value="Evaluasi Bulanan — Maret 2026" placeholder="Masukkan judul evaluasi">
                    </div>
                    <div class="form-group">
                        <label>Keputusan Evaluasi</label>
                        <select id="keputusan" onchange="toggleCatatanWajib()">
                            <option value="">-- Pilih Keputusan --</option>
                            <option value="disetujui">✓ Setujui Laporan Bulanan</option>
                            <option value="revisi">↩ Minta Revisi</option>
                        </select>
                    </div>
                    <div class="form-group span-2">
                        <label>Deskripsi Penilaian Kinerja</label>
                        <textarea placeholder="Deskripsikan penilaian umum terhadap kinerja proyek bulan ini..."></textarea>
                    </div>
                    <div class="form-group">
                        <label>Capaian Utama Bulan Ini</label>
                        <textarea placeholder="Tuliskan pencapaian-pencapaian signifikan..." style="min-height:80px;"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Kendala & Tindak Lanjut</label>
                        <textarea placeholder="Tuliskan kendala yang ditemukan dan rekomendasi tindak lanjut..." style="min-height:80px;"></textarea>
                    </div>
                    <div class="form-group span-2" id="catatanRevisiGroup" style="display:none;">
                        <label>Catatan Revisi <span style="color:#ef4444;">*</span></label>
                        <textarea id="catatanRevisi" placeholder="Wajib diisi: tuliskan poin-poin yang perlu direvisi oleh Koordinator Pengawas..." style="border-color: rgba(239,68,68,0.4);"></textarea>
                    </div>
                </div>

                <div style="display:flex; gap:10px; flex-wrap:wrap;">
                    <button type="submit" class="btn btn-primary">Simpan & Kirim Evaluasi</button>
                    <button type="button" class="btn btn-secondary" onclick="toggleForm('mar26')">Batal</button>
                </div>
            </form>
        </div>

    </main>
</div>

<script>
function toggleForm(id) {
    const panel = document.getElementById('formEval-' + id);
    panel.style.display = panel.style.display === 'none' ? 'block' : 'none';
    if (panel.style.display === 'block') {
        panel.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
}

function toggleCatatanWajib() {
    const val = document.getElementById('keputusan').value;
    const group = document.getElementById('catatanRevisiGroup');
    group.style.display = val === 'revisi' ? 'block' : 'none';
}

function submitEval(e) {
    e.preventDefault();
    const keputusan = document.getElementById('keputusan').value;
    if (!keputusan) {
        alert('Silakan pilih keputusan evaluasi terlebih dahulu.');
        return;
    }
    if (keputusan === 'revisi') {
        const catatan = document.getElementById('catatanRevisi').value.trim();
        if (!catatan) {
            alert('Catatan revisi wajib diisi saat memilih Minta Revisi.');
            return;
        }
    }
    const msg = keputusan === 'disetujui'
        ? 'Laporan Bulanan Maret 2026 telah DISETUJUI dan tersimpan sebagai arsip resmi.'
        : 'Permintaan revisi telah dikirim kepada Koordinator Pengawas.';
    alert(msg);
    document.getElementById('formEval-mar26').style.display = 'none';
}
</script>

</body>
</html>