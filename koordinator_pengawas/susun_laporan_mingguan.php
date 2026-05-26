<?php
session_start();
include "../koneksi.php";

// Proteksi Role
if (!isset($_SESSION['role']) || $_SESSION['role'] != "koordinator") {
    header("Location: ../login.php");
    exit;
}
?>

<?php
/**
 * susun_laporan_mingguan.php — Susun Laporan Mingguan
 * Sistem Pengawasan Proyek — Koordinator
 */
$active_page = 'susun';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Susun Laporan Mingguan | CV Cipta Manunggal</title>
    <link rel="stylesheet" href="asset/koordinator.css">
</head>
<body>

<?php include 'sidebar.php'; ?>

<main class="main">
    <!-- TOPBAR -->
    <div class="topbar">
        <h2>Susun Laporan Mingguan</h2>
        <div class="topbar-right">
            <div class="date-chip" id="date-chip"></div>
            <a href="riwayat_laporan_mingguan.php" class="notif-btn">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="#aaa" stroke-width="1.5">
                    <path d="M8 1a5 5 0 015 5v3l1 2H2l1-2V6a5 5 0 015-5z"/>
                    <path d="M6.5 13.5a1.5 1.5 0 003 0"/>
                </svg>
                <div class="notif-dot"></div>
            </a>
        </div>
    </div>

    <!-- SECTION HEADER -->
    <div class="section-header fade-up">
        <div>
            <div class="section-title">Susun Laporan Mingguan</div>
            <div class="section-sub">Rekap laporan harian tervalidasi menjadi laporan mingguan</div>
        </div>
        <a href="riwayat_laporan_mingguan.php" class="btn btn-gold btn-sm">📋 Riwayat Laporan</a>
    </div>

    <!-- STEP 1: Pilih Periode -->
    <div class="panel fade-up" style="animation-delay:.04s">
        <div class="panel-title">
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5">
                <rect x="1" y="1" width="6" height="6" rx="1.2"/>
                <rect x="9" y="1" width="6" height="6" rx="1.2"/>
                <rect x="1" y="9" width="6" height="6" rx="1.2"/>
                <rect x="9" y="9" width="6" height="6" rx="1.2"/>
            </svg>
            1. Pilih Periode Minggu
        </div>
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
    <div class="panel fade-up" style="animation-delay:.08s">
        <div class="panel-title">
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5">
                <path d="M2 4h12M2 8h9M2 12h6"/><circle cx="13" cy="11" r="2.5"/><path d="M15 13l1 1"/>
            </svg>
            2. Laporan Harian Tervalidasi (Periode Ini)
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Pengawas</th>
                        <th>Progres Pekerjaan</th>
                        <th>TK</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>01 Apr 2026</td>
                        <td>Pengawas A</td>
                        <td>Pengecoran kolom Lt.3</td>
                        <td>12</td>
                        <td><span class="badge badge-done">Tervalidasi</span></td>
                    </tr>
                    <tr>
                        <td>31 Mar 2026</td>
                        <td>Pengawas B</td>
                        <td>Pemasangan bekisting</td>
                        <td>9</td>
                        <td><span class="badge badge-done">Tervalidasi</span></td>
                    </tr>
                    <tr>
                        <td>30 Mar 2026</td>
                        <td>Pengawas A</td>
                        <td>Penulangan plat lantai</td>
                        <td>14</td>
                        <td><span class="badge badge-done">Tervalidasi</span></td>
                    </tr>
                    <tr>
                        <td>28 Mar 2026</td>
                        <td>Pengawas B</td>
                        <td>Pemasangan pondasi</td>
                        <td>10</td>
                        <td><span class="badge badge-done">Tervalidasi</span></td>
                    </tr>
                    <tr>
                        <td>27 Mar 2026</td>
                        <td>Pengawas A</td>
                        <td>Pemadatan tanah urug</td>
                        <td>8</td>
                        <td><span class="badge badge-done">Tervalidasi</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- STEP 3: Form Susun -->
    <form id="weekly-form" method="post">
        <div class="panel fade-up" style="animation-delay:.12s">
            <div class="panel-title">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M3 2h10v12H3z"/><path d="M6 5h4M6 8h4M6 11h2"/>
                </svg>
                3. Susun Ringkasan Laporan Mingguan
            </div>

            <?php if ($success): ?>
                <div class="alert alert-success"><?= safe($success) ?></div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="alert alert-danger"><?= safe($error) ?></div>
            <?php endif; ?>

            <div class="form-row">
                <div class="form-group">
                    <label>Judul Laporan *</label>
                    <input type="text" id="judul-laporan" name="judul" placeholder="cth: Laporan Minggu 11 – April 2026" value="<?= safe($_POST['judul'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label>Periode *</label>
                    <select id="minggu-ke" name="periode" onchange="updatePeriode(this.value)">
                        <option value="25 Mar – 01 Apr 2026">Minggu 11 (25 Mar – 01 Apr 2026)</option>
                        <option value="18 – 24 Mar 2026">Minggu 10 (18 – 24 Mar 2026)</option>
                        <option value="11 – 17 Mar 2026">Minggu 9 (11 – 17 Mar 2026)</option>
                    </select>
                    <input type="hidden" id="periode-laporan" name="periode_hidden" value="25 Mar – 01 Apr 2026">
                </div>
            </div>

            <div class="form-row full">
                <div class="form-group">
                    <label>Ringkasan Progres Pekerjaan *</label>
                    <textarea id="ringkasan" name="ringkasan" placeholder="Tuliskan ringkasan kemajuan pekerjaan selama minggu ini..."><?= safe($_POST['ringkasan'] ?? '') ?></textarea>
                </div>
            </div>

            <div class="form-row full">
                <div class="form-group">
                    <label>Temuan Pengawasan</label>
                    <textarea id="temuan" name="temuan" placeholder="Kendala, permasalahan, atau hal penting yang ditemukan di lapangan..."><?= safe($_POST['temuan'] ?? '') ?></textarea>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Analisis Pencapaian</label>
                    <textarea id="pencapaian" name="pencapaian" placeholder="Apa yang berhasil dicapai minggu ini?"><?= safe($_POST['pencapaian'] ?? '') ?></textarea>
                </div>
                <div class="form-group">
                    <label>Kendala & Rekomendasi</label>
                    <textarea id="kendala" name="kendala" placeholder="Kendala utama dan rekomendasi untuk minggu berikutnya..."><?= safe($_POST['kendala'] ?? '') ?></textarea>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Progress Fisik (%)</label>
                    <input type="number" id="progress-pct" name="progress" min="0" max="100" placeholder="42" value="<?= safe($_POST['progress'] ?? '42') ?>">
                </div>
                <div class="form-group">
                    <label>Total Tenaga Kerja Minggu Ini</label>
                    <input type="number" id="total-tk" name="total_tk" placeholder="53" value="<?= safe($_POST['total_tk'] ?? '53') ?>">
                </div>
            </div>

            <div style="margin-bottom: 16px;">
                <div style="font-size: 12px; color: var(--muted); margin-bottom: 6px;">Preview Progress Fisik</div>
                <div style="background: var(--surface2); padding: 8px; border-radius: 6px; overflow: hidden;">
                    <div style="background: var(--gold); height: 4px; width: <?= safe($_POST['progress'] ?? '42') ?>%; border-radius: 2px;" id="progress-preview"></div>
                </div>
                <div style="font-size: 11px; color: var(--muted); margin-top: 4px;" id="progress-label"><?= safe($_POST['progress'] ?? '42') ?>%</div>
            </div>

            <div style="display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" class="btn btn-outline" onclick="pratinjau()">👁 Pratinjau</button>
                <button type="submit" class="btn btn-gold">💾 Simpan Laporan</button>
            </div>
        </div>
    </form>
</main>

<script>
// Update date chip
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
    document.getElementById('date-chip').textContent = now.toLocaleDateString('id-ID', options);
}
updateDateChip();
setInterval(updateDateChip, 60000);

// Update progress preview
document.getElementById('progress-pct').addEventListener('input', function() {
    const pct = this.value;
    document.getElementById('progress-preview').style.width = pct + '%';
    document.getElementById('progress-label').textContent = pct + '%';
});

// Update periode
function updatePeriode(value) {
    document.getElementById('periode-laporan').value = value;
}

// Pratinjau
function pratinjau() {
    const judul = document.getElementById('judul-laporan').value;
    const ringkasan = document.getElementById('ringkasan').value;
    const progress = document.getElementById('progress-pct').value;
    
    if (!judul || !ringkasan) {
        alert('Judul dan ringkasan harus diisi!');
        return;
    }
    
    alert(`Pratinjau Laporan:\n\nJudul: ${judul}\nProgress: ${progress}%\nRingkasan: ${ringkasan.substring(0, 100)}...`);
}

// Simpan laporan
function simpanLaporan() {
    const judul = document.getElementById('judul-laporan').value;
    const ringkasan = document.getElementById('ringkasan').value;
    const progress = document.getElementById('progress-pct').value;
    
    if (!judul || !ringkasan) {
        alert('Judul dan ringkasan harus diisi!');
        return;
    }
    
    // Simulate saving
    alert('Laporan berhasil disimpan!');
    // In real implementation, this would send data to server
}
</script>

</body>
</html>
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
