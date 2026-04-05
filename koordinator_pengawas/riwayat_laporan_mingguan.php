<?php
/**
 * riwayat_laporan_mingguan.php — Riwayat Laporan Mingguan
 * Sistem Pengawasan Proyek — Koordinator Pengawas
 */
$activePage = 'riwayat';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Riwayat Laporan Mingguan – Koordinator Pengawas</title>
  <link rel="stylesheet" href="asset/koordinator.css">
</head>
<body>

<?php include 'sidebar.php'; ?>

<main class="main">
  <!-- TOPBAR -->
  <div class="topbar">
    <h2>Riwayat Laporan Mingguan</h2>
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
      <div class="section-title">Riwayat Laporan Mingguan</div>
      <div class="section-sub">Arsip laporan yang telah tersimpan</div>
    </div>
    <a href="susun_laporan_mingguan.php" class="btn btn-gold btn-sm">+ Susun Baru</a>
  </div>

  <!-- SUMMARY CHIPS -->
  <div style="display:flex;gap:10px;margin-bottom:20px;" class="fade-up" style="animation-delay:.08s">
    <div style="background:rgba(34,197,94,.1);border:1px solid rgba(34,197,94,.2);border-radius:20px;
                padding:6px 16px;font-size:12px;color:#22c55e;">
      ✓ Tersimpan: <strong>3</strong>
    </div>
    <div style="background:rgba(56,189,248,.1);border:1px solid rgba(56,189,248,.2);border-radius:20px;
                padding:6px 16px;font-size:12px;color:#38bdf8;">
      🔍 Evaluasi TL: <strong>1</strong>
    </div>
    <div style="background:rgba(255,255,255,.05);border:1px solid var(--border);border-radius:20px;
                padding:6px 16px;font-size:12px;color:var(--muted);">
      Total: <strong>4</strong>
    </div>
  </div>

  <!-- TABLE -->
  <div class="table-wrap fade-up" style="animation-delay:.12s">
    <table>
      <thead>
        <tr>
          <th>ID</th><th>Judul</th><th>Periode</th>
          <th>Progress</th><th>TK Total</th><th>Status</th><th>Aksi</th>
        </tr>
      </thead>
      <tbody>

        <tr>
          <td style="color:var(--muted);font-weight:600;">LM-010</td>
          <td style="font-weight:500;">Laporan Minggu 10 – Mar 2026</td>
          <td style="color:var(--muted);">18–24 Mar 2026</td>
          <td>
            <div style="display:flex;align-items:center;gap:8px;">
              <div class="progress-wrap" style="width:100px;">
                <div class="progress-bar" style="width:38%;"></div>
              </div>
              <span style="font-size:11px;color:var(--muted);">38%</span>
            </div>
          </td>
          <td>47</td>
          <td><span class="badge badge-done">Tersimpan</span></td>
          <td style="display:flex;gap:6px;">
            <button class="btn btn-outline btn-sm" onclick="openDetail('LM-010')">Lihat</button>
            <button class="btn btn-outline btn-sm" onclick="cetakLaporan('LM-010')"
              style="color:var(--info);border-color:rgba(56,189,248,.3);">Cetak</button>
          </td>
        </tr>

        <tr>
          <td style="color:var(--muted);font-weight:600;">LM-009</td>
          <td style="font-weight:500;">Laporan Minggu 9 – Mar 2026</td>
          <td style="color:var(--muted);">11–17 Mar 2026</td>
          <td>
            <div style="display:flex;align-items:center;gap:8px;">
              <div class="progress-wrap" style="width:100px;">
                <div class="progress-bar" style="width:33%;"></div>
              </div>
              <span style="font-size:11px;color:var(--muted);">33%</span>
            </div>
          </td>
          <td>52</td>
          <td><span class="badge badge-done">Tersimpan</span></td>
          <td style="display:flex;gap:6px;">
            <button class="btn btn-outline btn-sm" onclick="openDetail('LM-009')">Lihat</button>
            <button class="btn btn-outline btn-sm" onclick="cetakLaporan('LM-009')"
              style="color:var(--info);border-color:rgba(56,189,248,.3);">Cetak</button>
          </td>
        </tr>

        <tr>
          <td style="color:var(--muted);font-weight:600;">LM-008</td>
          <td style="font-weight:500;">Laporan Minggu 8 – Mar 2026</td>
          <td style="color:var(--muted);">4–10 Mar 2026</td>
          <td>
            <div style="display:flex;align-items:center;gap:8px;">
              <div class="progress-wrap" style="width:100px;">
                <div class="progress-bar" style="width:28%;"></div>
              </div>
              <span style="font-size:11px;color:var(--muted);">28%</span>
            </div>
          </td>
          <td>44</td>
          <td><span class="badge badge-review">Evaluasi TL</span></td>
          <td style="display:flex;gap:6px;">
            <button class="btn btn-outline btn-sm" onclick="openDetail('LM-008')">Lihat</button>
            <button class="btn btn-outline btn-sm" onclick="cetakLaporan('LM-008')"
              style="color:var(--info);border-color:rgba(56,189,248,.3);">Cetak</button>
          </td>
        </tr>

        <tr>
          <td style="color:var(--muted);font-weight:600;">LM-007</td>
          <td style="font-weight:500;">Laporan Minggu 7 – Feb 2026</td>
          <td style="color:var(--muted);">25 Feb – 3 Mar 2026</td>
          <td>
            <div style="display:flex;align-items:center;gap:8px;">
              <div class="progress-wrap" style="width:100px;">
                <div class="progress-bar" style="width:22%;"></div>
              </div>
              <span style="font-size:11px;color:var(--muted);">22%</span>
            </div>
          </td>
          <td>39</td>
          <td><span class="badge badge-done">Disetujui TL</span></td>
          <td style="display:flex;gap:6px;">
            <button class="btn btn-outline btn-sm" onclick="openDetail('LM-007')">Lihat</button>
            <button class="btn btn-outline btn-sm" onclick="cetakLaporan('LM-007')"
              style="color:var(--info);border-color:rgba(56,189,248,.3);">Cetak</button>
          </td>
        </tr>

      </tbody>
    </table>
  </div>

</main>

<!-- MODAL DETAIL RIWAYAT -->
<div class="modal-overlay" id="modal" onclick="if(event.target===this)closeModal()">
  <div class="modal" style="width:580px;">
    <div class="modal-header">
      <h3 id="modal-title">Detail Laporan Mingguan</h3>
      <button class="modal-close" onclick="closeModal()">✕</button>
    </div>
    <div class="modal-body" id="modal-body"></div>
    <div class="modal-actions">
      <button class="btn btn-outline" onclick="closeModal()">Tutup</button>
      <button class="btn btn-outline" style="color:var(--info);border-color:rgba(56,189,248,.3);"
        onclick="showToast('Mencetak laporan...','')">
        🖨 Cetak
      </button>
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

// Riwayat data
const riwayatData = {
  'LM-010': {
    judul: 'Laporan Minggu 10 – Mar 2026',
    periode: '18–24 Mar 2026',
    progress: 38,
    tk: 47,
    status: 'Tersimpan',
    ringkasan: 'Minggu ke-10 mencakup pekerjaan pemadatan tanah, galian pondasi zona B dan C, serta awal pemasangan pondasi batu kali.',
    temuan: 'Cuaca mendung pada hari Rabu memperlambat pekerjaan galian. Material batu kali datang terlambat 1 hari.',
    rekomendasi: 'Koordinasi lebih awal dengan supplier material agar tidak mengganggu jadwal.'
  },
  'LM-009': {
    judul: 'Laporan Minggu 9 – Mar 2026',
    periode: '11–17 Mar 2026',
    progress: 33,
    tk: 52,
    status: 'Tersimpan',
    ringkasan: 'Pekerjaan persiapan lahan dan mobilisasi alat berat selesai. Pemasangan patok dan bowplank selesai 100%.',
    temuan: 'Tidak ada kendala berarti selama minggu ini. Cuaca cerah mendukung produktivitas.',
    rekomendasi: 'Percepat pengiriman material cor untuk minggu berikutnya.'
  },
  'LM-008': {
    judul: 'Laporan Minggu 8 – Mar 2026',
    periode: '4–10 Mar 2026',
    progress: 28,
    tk: 44,
    status: 'Evaluasi TL',
    ringkasan: 'Pekerjaan pembersihan lokasi dan pengukuran awal selesai. Mobilisasi peralatan 80% selesai.',
    temuan: 'Terdapat hambatan akses jalan masuk. Diperlukan perbaikan jalan sementara.',
    rekomendasi: 'Segera perbaiki akses jalan sebelum mobilisasi alat berat utama dilakukan.'
  },
  'LM-007': {
    judul: 'Laporan Minggu 7 – Feb 2026',
    periode: '25 Feb – 3 Mar 2026',
    progress: 22,
    tk: 39,
    status: 'Disetujui TL',
    ringkasan: 'Kick-off proyek dan penyerahan lahan. Pembersihan lokasi dimulai. Tim inti telah hadir seluruhnya.',
    temuan: 'Kondisi lahan sesuai dengan data awal. Tidak ada temuan signifikan.',
    rekomendasi: 'Mulai pengukuran detail dan pemasangan papan nama proyek segera.'
  }
};

function openDetail(id){
  const r = riwayatData[id];
  if(!r) return;
  document.getElementById('modal-title').textContent = 'Detail — ' + r.judul;
  document.getElementById('modal-body').innerHTML = `
    <div style="background:var(--surface2);border:1px solid var(--border-gold);border-radius:10px;
                padding:14px;margin-bottom:16px;">
      <div style="font-family:'Syne',sans-serif;font-size:15px;font-weight:700;">${r.judul}</div>
      <div style="font-size:12px;color:var(--muted);margin-top:4px;">
        Periode: ${r.periode} &nbsp;|&nbsp; Progress: ${r.progress}% &nbsp;|&nbsp; TK: ${r.tk} orang
      </div>
      <div class="progress-wrap" style="margin-top:10px;">
        <div class="progress-bar" style="width:${r.progress}%;"></div>
      </div>
    </div>
    <div class="detail-row">
      <span class="key">Status</span>
      <span class="badge ${r.status==='Disetujui TL'||r.status==='Tersimpan'?'badge-done':'badge-review'}">${r.status}</span>
    </div>
    <div class="detail-row">
      <span class="key">Ringkasan Progres</span>
      <span style="text-align:right;max-width:300px;font-size:12px;">${r.ringkasan}</span>
    </div>
    <div class="detail-row">
      <span class="key">Temuan Lapangan</span>
      <span style="text-align:right;max-width:300px;font-size:12px;">${r.temuan}</span>
    </div>
    <div class="detail-row">
      <span class="key">Rekomendasi</span>
      <span style="text-align:right;max-width:300px;font-size:12px;">${r.rekomendasi}</span>
    </div>`;
  document.getElementById('modal').classList.add('open');
}
function closeModal(){ document.getElementById('modal').classList.remove('open'); }

function cetakLaporan(id){
  showToast('Mencetak laporan ' + id + '...', '');
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