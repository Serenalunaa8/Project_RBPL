<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard – Koordinator Pengawas</title>
  <link rel="stylesheet" href="asset/koordinator.css">
</head>
<body>

<?php include 'sidebar.php'; ?>

<main class="main">
  <!-- TOPBAR -->
  <div class="topbar">
    <h2>Dashboard</h2>
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

  <!-- STAT CARDS -->
  <div class="stats-grid fade-up">
    <div class="stat-card">
      <div class="stat-label">Total Laporan Harian</div>
      <div class="stat-val">38</div>
      <div class="stat-sub">Bulan ini: 12 laporan</div>
      <div class="stat-icon">📋</div>
    </div>
    <div class="stat-card">
      <div class="stat-label">Menunggu Pengesahan</div>
      <div class="stat-val" style="color:#f59e0b">4</div>
      <div class="stat-sub">Perlu ditinjau hari ini</div>
      <div class="stat-icon">⏳</div>
    </div>
    <div class="stat-card">
      <div class="stat-label">Laporan Mingguan Tersimpan</div>
      <div class="stat-val">9</div>
      <div class="stat-icon">📁</div>
    </div>
    <div class="stat-card">
      <div class="stat-label">Progress Proyek</div>
      <div class="stat-val" style="color:#22c55e">42%</div>
      <div class="stat-icon">📈</div>
    </div>
  </div>

  <!-- Chart + Status Panel -->
  <div style="display:grid;grid-template-columns:1.3fr 1fr;gap:20px;margin-bottom:20px;">

    <!-- Bar Chart -->
    <div class="panel fade-up" style="animation-delay:.1s">
      <div class="panel-title">Laporan Harian per Minggu</div>
      <div style="display:grid;grid-template-columns:repeat(7,1fr);gap:6px;">
        <?php
        $days  = ['Sn','Sl','Rb','Km','Jm','Sb','Mg'];
        $fills = [60, 85, 45, 100, 70, 30, 10];
        foreach ($days as $i => $d): ?>
        <div>
          <div class="chart-bar-wrap">
            <div class="chart-bar" style="width:100%;height:100%;">
              <div class="bar-fill" style="height:<?= $fills[$i] ?>%;"></div>
            </div>
          </div>
          <div class="chart-label"><?= $d ?></div>
        </div>
        <?php endforeach; ?>
      </div>
      <div style="margin-top:16px;">
        <div style="font-size:12px;color:var(--muted);margin-bottom:8px;">Progress Laporan Keseluruhan</div>
        <div class="progress-wrap"><div class="progress-bar" style="width:42%;"></div></div>
        <div style="display:flex;justify-content:space-between;font-size:11px;color:var(--muted);margin-top:5px;">
          <span>0%</span><span>42%</span><span>100%</span>
        </div>
      </div>
    </div>

    <!-- Status Timeline -->
    <div class="panel fade-up" style="animation-delay:.2s">
      <div class="panel-title">Status Laporan Minggu Ini</div>
      <div class="timeline">
        <div class="tl-item">
          <div class="tl-dot done">✓</div>
          <div class="tl-content">
            <p>LH-036 Tervalidasi</p>
            <span>Penulangan plat lantai · 30 Mar</span>
          </div>
        </div>
        <div class="tl-item">
          <div class="tl-dot done">✓</div>
          <div class="tl-content">
            <p>LH-033 Tervalidasi</p>
            <span>Pemadatan tanah urug · 27 Mar</span>
          </div>
        </div>
        <div class="tl-item">
          <div class="tl-dot" style="color:var(--warn)">⏳</div>
          <div class="tl-content">
            <p>LH-038 Menunggu</p>
            <span>Pengecoran kolom Lt.3 · 01 Apr</span>
          </div>
        </div>
        <div class="tl-item">
          <div class="tl-dot" style="color:var(--warn)">⏳</div>
          <div class="tl-content">
            <p>LH-037 Menunggu</p>
            <span>Pemasangan bekisting · 31 Mar</span>
          </div>
        </div>
        <div class="tl-item">
          <div class="tl-dot" style="color:var(--warn)">⏳</div>
          <div class="tl-content">
            <p>LH-035 Menunggu</p>
            <span>Galian pondasi · 29 Mar</span>
          </div>
        </div>
        <div class="tl-item">
          <div class="tl-dot" style="color:var(--warn)">⏳</div>
          <div class="tl-content">
            <p>LH-034 Menunggu</p>
            <span>Pondasi batu kali · 28 Mar</span>
          </div>
        </div>
      </div>
    </div>

  </div>

  <!-- Recent Laporan Harian -->
  <div class="section-header fade-up" style="animation-delay:.3s">
    <div>
      <div class="section-title">Laporan Harian Terbaru</div>
      <div class="section-sub">Menunggu tinjauan &amp; pengesahan</div>
    </div>
    <a href="Tinjau_laporan_harian.php" class="btn btn-outline btn-sm">Lihat Semua →</a>
  </div>

  <div class="table-wrap fade-up" style="animation-delay:.35s">
    <table>
      <thead>
        <tr>
          <th>#</th><th>Tanggal</th><th>Pengawas Lapangan</th>
          <th>Progres Pekerjaan</th><th>Status</th><th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td style="color:var(--muted)">LH-038</td>
          <td>01 Apr 2026</td>
          <td>Pengawas A</td>
          <td>Pengecoran kolom Lt.3</td>
          <td><span class="badge badge-wait">Menunggu</span></td>
          <td>
            <button class="btn btn-gold btn-sm"
              onclick="openModal('LH-038','01 Apr 2026','Pengawas A','Pengecoran kolom Lt.3, 12 TK, cuaca cerah, 3 alat berat','Menunggu')">
              Tinjau
            </button>
          </td>
        </tr>
        <tr>
          <td style="color:var(--muted)">LH-037</td>
          <td>31 Mar 2026</td>
          <td>Pengawas B</td>
          <td>Pemasangan bekisting</td>
          <td><span class="badge badge-wait">Menunggu</span></td>
          <td>
            <button class="btn btn-gold btn-sm"
              onclick="openModal('LH-037','31 Mar 2026','Pengawas B','Pemasangan bekisting area selatan, 9 TK, cuaca berawan','Menunggu')">
              Tinjau
            </button>
          </td>
        </tr>
        <tr>
          <td style="color:var(--muted)">LH-036</td>
          <td>30 Mar 2026</td>
          <td>Pengawas A</td>
          <td>Penulangan plat lantai</td>
          <td><span class="badge badge-done">Tervalidasi</span></td>
          <td>
            <button class="btn btn-outline btn-sm"
              onclick="openModal('LH-036','30 Mar 2026','Pengawas A','Penulangan plat lantai 3, 14 TK, cuaca cerah, 2 alat','Tervalidasi')">
              Detail
            </button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>

</main>

<!-- MODAL DETAIL -->
<div class="modal-overlay" id="modal" onclick="if(event.target===this)closeModal()">
  <div class="modal">
    <div class="modal-header">
      <h3 id="modal-title">Detail Laporan Harian</h3>
      <button class="modal-close" onclick="closeModal()">✕</button>
    </div>
    <div class="modal-body" id="modal-body"></div>
    <div class="modal-actions" id="modal-actions"></div>
  </div>
</div>

<!-- TOAST -->
<div class="toast" id="toast"></div>

<script>
// Date chip
const d = new Date();
document.getElementById('date-chip').textContent =
  d.toLocaleDateString('id-ID',{weekday:'long',day:'numeric',month:'long',year:'numeric'});

// Modal
function openModal(id, tgl, pengawas, deskripsi, status){
  document.getElementById('modal-title').textContent = 'Detail Laporan ' + id;
  document.getElementById('modal-body').innerHTML = `
    <div class="detail-row"><span class="key">ID Laporan</span><span>${id}</span></div>
    <div class="detail-row"><span class="key">Tanggal</span><span>${tgl}</span></div>
    <div class="detail-row"><span class="key">Pengawas Lapangan</span><span>${pengawas}</span></div>
    <div class="detail-row"><span class="key">Deskripsi Pekerjaan</span><span style="text-align:right;max-width:280px;">${deskripsi}</span></div>
    <div class="detail-row"><span class="key">Status</span>
      <span class="badge ${status==='Tervalidasi'?'badge-done':'badge-wait'}">${status}</span>
    </div>
    <div class="detail-row"><span class="key">Catatan Koordinator</span>
      <textarea placeholder="Tambahkan catatan jika diperlukan..."
        style="background:var(--surface2);border:1px solid var(--border);border-radius:6px;
               padding:8px;color:var(--text);font-size:12px;width:200px;resize:vertical;
               outline:none;font-family:inherit;"></textarea>
    </div>`;
  const act = document.getElementById('modal-actions');
  if(status === 'Menunggu'){
    act.innerHTML = `
      <button class="btn btn-outline" onclick="closeModal()">Tutup</button>
      <button class="btn btn-outline btn-sm" style="color:#ff4d4d;border-color:rgba(255,77,77,.3);"
        onclick="closeModal();showToast('Laporan ${id} dikembalikan ke Pengawas','')">
        Kembalikan
      </button>
      <button class="btn btn-gold" onclick="closeModal();showToast('Laporan ${id} berhasil disahkan ✓','success')">
        Sahkan Laporan
      </button>`;
  } else {
    act.innerHTML = `<button class="btn btn-outline" onclick="closeModal()">Tutup</button>`;
  }
  document.getElementById('modal').classList.add('open');
}
function closeModal(){ document.getElementById('modal').classList.remove('open'); }

function showToast(msg, type){
  const t = document.getElementById('toast');
  t.textContent = msg;
  t.className = 'toast show' + (type?' '+type:'');
  setTimeout(()=>t.className='toast', 2800);
}
</script>
</body>
</html>