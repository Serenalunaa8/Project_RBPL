<?php
/**
 * susun_laporan_mingguan.php — Susun Laporan Mingguan
 * Sistem Pengawasan Proyek — Koordinator Pengawas
 */
$activePage = 'susun';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Susun Laporan Mingguan – Koordinator Pengawas</title>
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
      <a href="Tinjau_laporan_harian.php" class="notif-btn">
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
  </div>

  <!-- STEP 1: Pilih Periode -->
  <div class="panel fade-up" style="animation-delay:.05s">
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
        <input type="text" value="Proyek Konstruksi Gedung A" readonly style="opacity:.7;">
      </div>
    </div>
  </div>

  <!-- STEP 2: Laporan Harian Tervalidasi -->
  <div class="panel fade-up" style="animation-delay:.1s">
    <div class="panel-title">2. Laporan Harian Tervalidasi (Periode Ini)</div>
    <div class="table-wrap" style="border:none;">
      <table>
        <thead>
          <tr>
            <th>Tanggal</th><th>Pengawas</th>
            <th>Progres Pekerjaan</th><th>TK</th><th>Status</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>01 Apr 2026</td><td>Pengawas A</td>
            <td>Pengecoran kolom Lt.3</td><td>12</td>
            <td><span class="badge badge-done">Tervalidasi</span></td>
          </tr>
          <tr>
            <td>31 Mar 2026</td><td>Pengawas B</td>
            <td>Pemasangan bekisting</td><td>9</td>
            <td><span class="badge badge-done">Tervalidasi</span></td>
          </tr>
          <tr>
            <td>30 Mar 2026</td><td>Pengawas A</td>
            <td>Penulangan plat lantai</td><td>14</td>
            <td><span class="badge badge-done">Tervalidasi</span></td>
          </tr>
          <tr>
            <td>28 Mar 2026</td><td>Pengawas B</td>
            <td>Pemasangan pondasi</td><td>10</td>
            <td><span class="badge badge-done">Tervalidasi</span></td>
          </tr>
          <tr>
            <td>27 Mar 2026</td><td>Pengawas A</td>
            <td>Pemadatan tanah urug</td><td>8</td>
            <td><span class="badge badge-done">Tervalidasi</span></td>
          </tr>
        </tbody>
      </table>
    </div>
    <div style="margin-top:12px;padding:10px 14px;background:rgba(255,193,7,.06);
                border:1px solid rgba(255,193,7,.15);border-radius:8px;
                font-size:12px;color:var(--muted);display:flex;align-items:center;gap:8px;">
      <span style="color:var(--gold);">ℹ</span>
      Total 5 laporan tervalidasi · 53 Tenaga Kerja · Periode 25 Mar – 01 Apr 2026
    </div>
  </div>

  <!-- STEP 3: Form Susun -->
  <div class="panel fade-up" style="animation-delay:.15s">
    <div class="panel-title">3. Susun Ringkasan Laporan Mingguan</div>

    <div class="form-row">
      <div class="form-group">
        <label>Judul Laporan *</label>
        <input type="text" id="judul-laporan" placeholder="cth: Laporan Minggu 11 – April 2026">
      </div>
      <div class="form-group">
        <label>Periode *</label>
        <input type="text" id="periode-laporan" value="25 Mar – 01 Apr 2026">
      </div>
    </div>

    <div class="form-row full">
      <div class="form-group">
        <label>Ringkasan Progres Pekerjaan *</label>
        <textarea id="ringkasan"
          placeholder="Tuliskan ringkasan kemajuan pekerjaan selama minggu ini...&#10;cth: Minggu ini pengerjaan telah mencapai tahap pengecoran kolom lantai 3, pemasangan bekisting, dan penulangan plat lantai..."></textarea>
      </div>
    </div>

    <div class="form-row full">
      <div class="form-group">
        <label>Temuan Pengawasan</label>
        <textarea id="temuan"
          placeholder="Kendala, permasalahan, atau hal penting yang ditemukan di lapangan..."></textarea>
      </div>
    </div>

    <div class="form-row">
      <div class="form-group">
        <label>Analisis Pencapaian</label>
        <textarea id="pencapaian" placeholder="Apa yang berhasil dicapai minggu ini?"></textarea>
      </div>
      <div class="form-group">
        <label>Kendala &amp; Rekomendasi</label>
        <textarea id="kendala"
          placeholder="Kendala utama dan rekomendasi untuk minggu berikutnya..."></textarea>
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

    <!-- Progress preview -->
    <div style="margin-bottom:16px;">
      <div style="font-size:12px;color:var(--muted);margin-bottom:6px;">
        Preview Progress Fisik
      </div>
      <div class="progress-wrap">
        <div class="progress-bar" id="progress-preview" style="width:42%;"></div>
      </div>
      <div style="font-size:11px;color:var(--muted);margin-top:4px;" id="progress-label">42%</div>
    </div>

    <div style="display:flex;gap:10px;justify-content:flex-end;">
      <button class="btn btn-outline" onclick="pratinjau()">👁 Pratinjau</button>
      <button class="btn btn-gold" onclick="simpanLaporan()">💾 Simpan Laporan Mingguan</button>
    </div>
  </div>

</main>

<!-- PRATINJAU MODAL -->
<div class="modal-overlay" id="modal-pratinjau" onclick="if(event.target===this)closePratinjau()">
  <div class="modal" style="width:600px;">
    <div class="modal-header">
      <h3>Pratinjau Laporan Mingguan</h3>
      <button class="modal-close" onclick="closePratinjau()">✕</button>
    </div>
    <div class="modal-body" id="pratinjau-body"></div>
    <div class="modal-actions">
      <button class="btn btn-outline" onclick="closePratinjau()">Tutup</button>
      <button class="btn btn-gold" onclick="closePratinjau();simpanLaporan()">Simpan Sekarang</button>
    </div>
  </div>
</div>

<!-- TOAST -->
<div class="toast" id="toast"></div>

<script>
// Date chip
const d = new Date();
document.getElementById('date-chip').textContent =
  d.toLocaleDateString('id-ID',{weekday:'long',day:'numeric',month:'long',year:'numeric'});

// Update periode when minggu changes
function updatePeriode(val){
  document.getElementById('periode-laporan').value = val;
}

// Live progress preview
document.getElementById('progress-pct').addEventListener('input', function(){
  const v = Math.min(100, Math.max(0, this.value || 0));
  document.getElementById('progress-preview').style.width = v + '%';
  document.getElementById('progress-label').textContent = v + '%';
});

// Pratinjau
function pratinjau(){
  const judul     = document.getElementById('judul-laporan').value || '(Belum diisi)';
  const periode   = document.getElementById('periode-laporan').value;
  const ringkasan = document.getElementById('ringkasan').value || '-';
  const temuan    = document.getElementById('temuan').value || '-';
  const pencapaian= document.getElementById('pencapaian').value || '-';
  const kendala   = document.getElementById('kendala').value || '-';
  const pct       = document.getElementById('progress-pct').value;
  const tk        = document.getElementById('total-tk').value;

  document.getElementById('pratinjau-body').innerHTML = `
    <div style="border:1px solid var(--border-gold);border-radius:10px;padding:16px;
                margin-bottom:14px;background:var(--surface2);">
      <div style="font-family:'Syne',sans-serif;font-size:16px;font-weight:700;
                  margin-bottom:4px;">${judul}</div>
      <div style="font-size:12px;color:var(--muted);">
        Periode: ${periode} &nbsp;|&nbsp; Progress: ${pct}% &nbsp;|&nbsp; TK: ${tk} orang
      </div>
      <div class="progress-wrap" style="margin-top:10px;">
        <div class="progress-bar" style="width:${pct}%;"></div>
      </div>
    </div>
    <div class="detail-row">
      <span class="key">Ringkasan Progres</span>
      <span style="text-align:right;max-width:300px;font-size:12px;">${ringkasan}</span>
    </div>
    <div class="detail-row">
      <span class="key">Temuan Pengawasan</span>
      <span style="text-align:right;max-width:300px;font-size:12px;">${temuan}</span>
    </div>
    <div class="detail-row">
      <span class="key">Analisis Pencapaian</span>
      <span style="text-align:right;max-width:300px;font-size:12px;">${pencapaian}</span>
    </div>
    <div class="detail-row">
      <span class="key">Kendala &amp; Rekomendasi</span>
      <span style="text-align:right;max-width:300px;font-size:12px;">${kendala}</span>
    </div>
    <div class="detail-row">
      <span class="key">Laporan Harian Terkait</span>
      <span>5 laporan tervalidasi</span>
    </div>`;
  document.getElementById('modal-pratinjau').classList.add('open');
}
function closePratinjau(){ document.getElementById('modal-pratinjau').classList.remove('open'); }

// Simpan
function simpanLaporan(){
  const judul    = document.getElementById('judul-laporan').value;
  const ringkasan= document.getElementById('ringkasan').value;
  if(!judul.trim()){ showToast('Judul laporan wajib diisi!',''); return; }
  if(!ringkasan.trim()){ showToast('Ringkasan progres wajib diisi!',''); return; }
  showToast('Laporan mingguan berhasil disimpan ✓','success');
  setTimeout(()=>{ window.location.href = 'riwayat_laporan_mingguan.php'; }, 800);
}

function showToast(msg, type){
  const t = document.getElementById('toast');
  t.textContent = msg;
  t.className = 'toast show' + (type?' '+type:'');
  setTimeout(()=>t.className='toast', 2800);
}
</script>
</body>
</html>