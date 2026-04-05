<?php
session_start();
include "../koneksi.php";

// Proteksi Role
if (!isset($_SESSION['role']) || $_SESSION['role'] != "koordinator") {
    header("Location: ../login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Susun Laporan Mingguan | CV Cipta Manunggal</title>
    <link rel="stylesheet" href="../kontraktor/asset/kontraktordash.css">
    <style>
        .form-group { display: flex; flex-direction: column; gap: 8px; }
        .form-group label { color: #888; font-size: 12px; font-weight: 600; text-transform: uppercase; }
        .form-group input, .form-group select, .form-group textarea { 
            background: #1c1c1c; border: 1px solid rgba(255,255,255,0.1); 
            color: #fff; padding: 10px 12px; border-radius: 6px; font-family: inherit; font-size: 14px;
        }
        .form-group textarea { min-height: 80px; resize: vertical; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px; }
        .form-row.full { grid-template-columns: 1fr; }
        .form-group input:focus, .form-group select:focus, .form-group textarea:focus {
            outline: none; border-color: #ffc107; background: #222;
        }
        .btn-primary { background: #ffc107; color: #111; padding: 10px 20px; border: none; border-radius: 6px; cursor: pointer; font-weight: bold; }
        .btn-secondary { background: transparent; color: #fff; border: 1px solid rgba(255,255,255,0.2); padding: 10px 20px; border-radius: 6px; cursor: pointer; margin-right: 10px; }
        .btn-secondary:hover { border-color: #888; }
        .badge { padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: 600; display: inline-block; }
        .badge-done { background: rgba(34,197,94,0.2); color: #22c55e; }
        .modal-overlay { display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.7); z-index: 1000; align-items: center; justify-content: center; }
        .modal-overlay.open { display: flex; }
        .modal { background: #1a1a1a; border-radius: 8px; padding: 30px; max-width: 700px; width: 90%; border: 1px solid rgba(255,193,7,0.2); max-height: 80vh; overflow-y: auto; }
        .detail-row { display: flex; justify-content: space-between; align-items: flex-start; padding: 12px 0; border-bottom: 1px solid rgba(255,255,255,0.05); }
        .detail-row .key { color: #888; font-size: 12px; min-width: 120px; }
        .table-wrap { background: #1a1a1a; border: 1px solid rgba(255,255,255,0.05); border-radius: 8px; overflow-x: auto; margin-bottom: 20px; }
        .table-wrap table { width: 100%; border-collapse: collapse; }
        .table-wrap th, .table-wrap td { padding: 12px; text-align: left; border-bottom: 1px solid rgba(255,255,255,0.05); }
        .table-wrap th { background: #111; color: #888; font-weight: 600; }
        .table-wrap tr:last-child td { border-bottom: none; }
        .toast { position: fixed; bottom: 20px; right: 20px; background: #1a1a1a; color: #fff; padding: 12px 16px; border-radius: 6px; border-left: 4px solid #ffc107; display: none; z-index: 2000; }
        .toast.show { display: block; }
        .toast.success { border-left-color: #22c55e; }
        .panel { background: #1a1a1a; border: 1px solid rgba(255,255,255,0.05); border-radius: 8px; padding: 20px; margin-bottom: 20px; }
        .panel-title { font-size: 16px; font-weight: 600; margin-bottom: 16px; }
    </style>
</head>
<body>

<div class="dashboard-container">
    <aside class="sidebar">
        <div class="sidebar-brand">
            <svg viewBox="0 0 120 120" class="logo-arch">
                <rect x="10" y="10" width="100" height="100" stroke="#ffc107" stroke-width="3" fill="none"/>
                <path d="M35 80 V40 H60" stroke="#ffc107" stroke-width="4" fill="none"/>
                <path d="M60 40 L75 60 L90 40 V80" stroke="#ffc107" stroke-width="4" fill="none"/>
            </svg>
            <h2>CIPTA<span>MANUNGGAL</span></h2>
        </div>
        <nav>
            <a href="koordinator_pengawas.php">Dashboard</a>
            <a href="Tinjau_laporan_harian.php">Tinjau Laporan Harian</a>
            <a href="susun_laporan_mingguan.php" class="active">Susun Laporan Mingguan</a>
            <a href="riwayat_laporan_mingguan.php">Riwayat Laporan</a>
            <a href="../logout.php" class="logout">Logout</a>
        </nav>
    </aside>

    <main class="main-content">
        <header class="topbar">
            <div>
                <h1>Susun Laporan Mingguan</h1>
                <p>Rekap laporan harian tervalidasi menjadi laporan mingguan</p>
            </div>
            <div class="role-badge">KOORDINATOR PENGAWAS</div>
        </header>

        <!-- STEP 1: Pilih Periode -->
        <div class="panel">
            <div class="panel-title">1. Pilih Periode Minggu</div>
            <div class="form-row">
                <div class="form-group">
                    <label>Minggu Ke</label>
                    <select id="minggu-ke" onchange="updatePeriode(this.value)">
                        <option value="25 Mar – 01 Apr 2026">Minggu 11 (25 Mar – 01 Apr 2026)</option>
                        <option value="18 – 24 Mar 2026">Minggu 10 (18 – 24 Mar 2026)</option>
                        <option value="11 – 17 Mar 2026">Minggu 9 (11 – 17 Mar 2026)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Nama Proyek</label>
                    <input type="text" value="Proyek Konstruksi Gedung A" readonly>
                </div>
            </div>
        </div>

        <!-- STEP 2: Laporan Harian Tervalidasi -->
        <div class="panel">
            <div class="panel-title">2. Laporan Harian Tervalidasi (Periode Ini)</div>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Tanggal</th><th>Pengawas</th><th>Progres Pekerjaan</th><th>TK</th><th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>01 Apr 2026</td><td>Pengawas A</td><td>Pengecoran kolom Lt.3</td><td>12</td>
                            <td><span class="badge badge-done">Tervalidasi</span></td>
                        </tr>
                        <tr>
                            <td>31 Mar 2026</td><td>Pengawas B</td><td>Pemasangan bekisting</td><td>9</td>
                            <td><span class="badge badge-done">Tervalidasi</span></td>
                        </tr>
                        <tr>
                            <td>30 Mar 2026</td><td>Pengawas A</td><td>Penulangan plat lantai</td><td>14</td>
                            <td><span class="badge badge-done">Tervalidasi</span></td>
                        </tr>
                        <tr>
                            <td>28 Mar 2026</td><td>Pengawas B</td><td>Pemasangan pondasi</td><td>10</td>
                            <td><span class="badge badge-done">Tervalidasi</span></td>
                        </tr>
                        <tr>
                            <td>27 Mar 2026</td><td>Pengawas A</td><td>Pemadatan tanah urug</td><td>8</td>
                            <td><span class="badge badge-done">Tervalidasi</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- STEP 3: Form Susun -->
        <div class="panel">
            <div class="panel-title">3. Susun Ringkasan Laporan Mingguan</div>

            <div class="form-row">
                <div class="form-group">
                    <label>Judul Laporan *</label>
                    <input type="text" id="judul-laporan" placeholder="cth: Laporan Minggu 11 – April 2026">
                </div>
                <div class="form-group">
                    <label>Periode *</label>
                    <input type="text" id="periode-laporan" value="25 Mar – 01 Apr 2026" readonly>
                </div>
            </div>

            <div class="form-row full">
                <div class="form-group">
                    <label>Ringkasan Progres Pekerjaan *</label>
                    <textarea id="ringkasan" placeholder="Tuliskan ringkasan kemajuan pekerjaan selama minggu ini..."></textarea>
                </div>
            </div>

            <div class="form-row full">
                <div class="form-group">
                    <label>Temuan Pengawasan</label>
                    <textarea id="temuan" placeholder="Kendala, permasalahan, atau hal penting yang ditemukan di lapangan..."></textarea>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Analisis Pencapaian</label>
                    <textarea id="pencapaian" placeholder="Apa yang berhasil dicapai minggu ini?"></textarea>
                </div>
                <div class="form-group">
                    <label>Kendala & Rekomendasi</label>
                    <textarea id="kendala" placeholder="Kendala utama dan rekomendasi untuk minggu berikutnya..."></textarea>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Progress Fisik (%)</label>
                    <input type="number" id="progress-pct" min="0" max="100" placeholder="42" value="42">
                </div>
                <div class="form-group">
                    <label>Total Tenaga Kerja Minggu Ini</label>
                    <input type="number" id="total-tk" placeholder="53" value="53">
                </div>
            </div>

            <div style="margin-bottom: 16px;">
                <div style="font-size: 12px; color: #888; margin-bottom: 6px;">Preview Progress Fisik</div>
                <div class="activity-card" style="background: #111; padding: 8px; overflow: hidden;">
                    <div style="background: #ffc107; height: 4px; width: 42%; border-radius: 2px;" id="progress-preview"></div>
                </div>
                <div style="font-size: 11px; color: #888; margin-top: 4px;" id="progress-label">42%</div>
            </div>

            <div style="display: flex; gap: 10px; justify-content: flex-end;">
                <button class="btn-secondary" onclick="pratinjau()">👁 Pratinjau</button>
                <button class="btn-primary" onclick="simpanLaporan()">💾 Simpan Laporan</button>
            </div>
        </div>
    </main>
</div>

<!-- PRATINJAU MODAL -->
<div class="modal-overlay" id="modal-pratinjau">
    <div class="modal">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3 style="margin: 0; font-size: 18px;">Pratinjau Laporan Mingguan</h3>
            <button style="background: none; border: none; color: #888; font-size: 28px; cursor: pointer;" onclick="closePratinjau()">✕</button>
        </div>
        <div id="pratinjau-body"></div>
        <div style="display: flex; gap: 10px; margin-top: 20px; justify-content: flex-end;">
            <button class="btn-secondary" onclick="closePratinjau()">Tutup</button>
            <button class="btn-primary" onclick="closePratinjau();simpanLaporan()">Simpan Sekarang</button>
        </div>
    </div>
</div>

<!-- TOAST -->
<div class="toast" id="toast"></div>

<script>
function updatePeriode(val){
    document.getElementById('periode-laporan').value = val;
}

document.getElementById('progress-pct').addEventListener('input', function(){
    const v = Math.min(100, Math.max(0, this.value || 0));
    document.getElementById('progress-preview').style.width = v + '%';
    document.getElementById('progress-label').textContent = v + '%';
});

function pratinjau(){
    const judul = document.getElementById('judul-laporan').value || '(Belum diisi)';
    const periode = document.getElementById('periode-laporan').value;
    const ringkasan = document.getElementById('ringkasan').value || '(Kosong)';
    const temuan = document.getElementById('temuan').value || '(Kosong)';
    const pencapaian = document.getElementById('pencapaian').value || '(Kosong)';
    const kendala = document.getElementById('kendala').value || '(Kosong)';
    const pct = document.getElementById('progress-pct').value;
    const tk = document.getElementById('total-tk').value;

    let html = `<div style="border: 1px solid #ffc107; border-radius: 6px; padding: 16px; margin-bottom: 14px;">
                    <div style="font-size: 16px; font-weight: 700; margin-bottom: 4px;">${judul}</div>
                    <div style="font-size: 12px; color: #888;">
                        Periode: ${periode} | Progress: ${pct}% | TK: ${tk} orang
                    </div>
                </div>`;
    html += `<div class="detail-row"><span class="key">Ringkasan Progres</span><span>${ringkasan}</span></div>`;
    html += `<div class="detail-row"><span class="key">Temuan Pengawasan</span><span>${temuan}</span></div>`;
    html += `<div class="detail-row"><span class="key">Analisis Pencapaian</span><span>${pencapaian}</span></div>`;
    html += `<div class="detail-row"><span class="key">Kendala & Rekomendasi</span><span>${kendala}</span></div>`;
    
    document.getElementById('pratinjau-body').innerHTML = html;
    document.getElementById('modal-pratinjau').classList.add('open');
}

function closePratinjau(){
    document.getElementById('modal-pratinjau').classList.remove('open');
}

function simpanLaporan(){
    const judul = document.getElementById('judul-laporan').value;
    const ringkasan = document.getElementById('ringkasan').value;
    if(!judul.trim()){ showToast('Judul laporan wajib diisi!', ''); return; }
    if(!ringkasan.trim()){ showToast('Ringkasan progres wajib diisi!', ''); return; }
    showToast('Laporan mingguan berhasil disimpan ✓', 'success');
    setTimeout(()=>{ window.location.href = 'riwayat_laporan_mingguan.php'; }, 800);
}

function showToast(msg, type){
    const t = document.getElementById('toast');
    t.textContent = msg;
    t.className = 'toast show' + (type?' '+type:'');
    setTimeout(()=>{ t.className = 'toast'; }, 2800);
}
</script>

</body>
</html>
