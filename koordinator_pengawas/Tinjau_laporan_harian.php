<?php
/**
 * Tinjau_laporan_harian.php — Tinjau Laporan Harian
 * Sistem Pengawasan Proyek — Koordinator Pengawas
 */
$activePage = 'tinjau';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tinjau Laporan Harian – Koordinator Pengawas</title>
  <link rel="stylesheet" href="asset/koordinator.css">
</head>
<body>

<?php include 'sidebar.php'; ?>

<main class="main">
  <!-- TOPBAR -->
  <div class="topbar">
    <h2>Tinjau Laporan Harian</h2>
    <div class="topbar-right">
      <div class="date-chip" id="date-chip"></div>
      <button class="notif-btn">
        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="#aaa" stroke-width="1.5">
          <path d="M8 1a5 5 0 015 5v3l1 2H2l1-2V6a5 5 0 015-5z"/>
          <path d="M6.5 13.5a1.5 1.5 0 003 0"/>
        </svg>
        <div class="notif-dot"></div>
      </button>
    </div>
  </div>

  <!-- SECTION HEADER + FILTER -->
  <div class="section-header fade-up">
    <div>
      <div class="section-title">Tinjauan &amp; Pengesahan Laporan</div>
      <div class="section-sub">Tinjauan &amp; pengesahan laporan dari Pengawas Lapangan</div>
    </div>
    <div style="display:flex;gap:8px;align-items:center;">
      <!-- Search -->
      <div style="position:relative;">
        <svg style="position:absolute;left:10px;top:50%;transform:translateY(-50%);opacity:.4;"
          width="13" height="13" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5">
          <circle cx="6.5" cy="6.5" r="5"/><path d="M11 11l3 3"/>
        </svg>
        <input type="text" id="search-input"
          placeholder="Cari laporan..."
          oninput="filterTable()"
          style="background:var(--surface2);border:1px solid var(--border);border-radius:8px;
                 padding:7px 12px 7px 30px;color:var(--text);font-size:12.5px;
                 outline:none;width:180px;font-family:'DM Sans',sans-serif;transition:.2s;"
          onfocus="this.style.borderColor='var(--gold)'"
          onblur="this.style.borderColor='var(--border)'">
      </div>
      <!-- Filter Status -->
      <select id="status-filter"
        style="background:var(--surface2);border:1px solid var(--border);border-radius:8px;
               padding:7px 12px;color:var(--text);font-size:12.5px;outline:none;
               font-family:'DM Sans',sans-serif;transition:.2s;"
        onchange="filterTable()"
        onfocus="this.style.borderColor='var(--gold)'"
        onblur="this.style.borderColor='var(--border)'">
        <option value="">Semua Status</option>
        <option value="Menunggu">Menunggu</option>
        <option value="Tervalidasi">Tervalidasi</option>
        <option value="Ditolak">Ditolak</option>
      </select>
    </div>
  </div>

  <!-- SUMMARY CHIPS -->
  <div style="display:flex;gap:10px;margin-bottom:20px;" class="fade-up" style="animation-delay:.1s">
    <div style="background:rgba(245,158,11,.1);border:1px solid rgba(245,158,11,.2);border-radius:20px;
                padding:6px 16px;font-size:12px;color:#f59e0b;">
      ⏳ Menunggu: <strong>4</strong>
    </div>
    <div style="background:rgba(34,197,94,.1);border:1px solid rgba(34,197,94,.2);border-radius:20px;
                padding:6px 16px;font-size:12px;color:#22c55e;">
      ✓ Tervalidasi: <strong>2</strong>
    </div>
    <div style="background:rgba(255,255,255,.05);border:1px solid var(--border);border-radius:20px;
                padding:6px 16px;font-size:12px;color:var(--muted);">
      Total: <strong>6</strong>
    </div>
  </div>

  <!-- TABLE -->
  <div class="table-wrap fade-up" style="animation-delay:.15s">
    <table>
      <thead>
        <tr>
          <th>ID</th><th>Tanggal</th><th>Pengawas</th>
          <th>Progres</th><th>TK</th><th>Cuaca</th>
          <th>Foto</th><th>Status</th><th>Aksi</th>
        </tr>
      </thead>
      <tbody id="tinjau-tbody">

        <tr data-status="Menunggu" data-search="lh-038 pengawas a pengecoran kolom">
          <td style="color:var(--muted);font-weight:600;">LH-038</td>
          <td>01 Apr 2026</td>
          <td>Pengawas A</td>
          <td>Pengecoran kolom Lt.3</td>
          <td>12</td>
          <td>☀️ Cerah</td>
          <td><span class="badge badge-done">3 foto</span></td>
          <td><span class="badge badge-wait">Menunggu</span></td>
          <td style="display:flex;gap:6px;flex-wrap:wrap;">
            <button class="btn btn-gold btn-sm"
              onclick="openModal('LH-038','01 Apr 2026','Pengawas A',
                'Pengecoran kolom Lt.3, 12 TK, cuaca cerah, 3 alat berat. Tidak ada kendala.','Menunggu')">
              Detail
            </button>
            <button class="btn btn-outline btn-sm" style="color:#22c55e;border-color:rgba(34,197,94,.3);"
              onclick="sahkan('LH-038', this)">Sahkan</button>
          </td>
        </tr>

        <tr data-status="Menunggu" data-search="lh-037 pengawas b bekisting">
          <td style="color:var(--muted);font-weight:600;">LH-037</td>
          <td>31 Mar 2026</td>
          <td>Pengawas B</td>
          <td>Pemasangan bekisting</td>
          <td>9</td>
          <td>🌤 Berawan</td>
          <td><span class="badge badge-done">2 foto</span></td>
          <td><span class="badge badge-wait">Menunggu</span></td>
          <td style="display:flex;gap:6px;flex-wrap:wrap;">
            <button class="btn btn-gold btn-sm"
              onclick="openModal('LH-037','31 Mar 2026','Pengawas B',
                'Pemasangan bekisting area selatan, 9 TK, cuaca berawan, 1 alat crane. Kendala: material terlambat 2 jam.','Menunggu')">
              Detail
            </button>
            <button class="btn btn-outline btn-sm" style="color:#22c55e;border-color:rgba(34,197,94,.3);"
              onclick="sahkan('LH-037', this)">Sahkan</button>
          </td>
        </tr>

        <tr data-status="Menunggu" data-search="lh-035 pengawas a galian pondasi">
          <td style="color:var(--muted);font-weight:600;">LH-035</td>
          <td>29 Mar 2026</td>
          <td>Pengawas A</td>
          <td>Pekerjaan galian pondasi</td>
          <td>18</td>
          <td>⛅ Mendung</td>
          <td><span class="badge badge-reject">0 foto</span></td>
          <td><span class="badge badge-wait">Menunggu</span></td>
          <td style="display:flex;gap:6px;flex-wrap:wrap;">
            <button class="btn btn-gold btn-sm"
              onclick="openModal('LH-035','29 Mar 2026','Pengawas A',
                'Galian pondasi zona B, 18 TK, cuaca mendung, 2 excavator. Foto belum diunggah.','Menunggu')">
              Detail
            </button>
            <button class="btn btn-outline btn-sm" style="color:#22c55e;border-color:rgba(34,197,94,.3);"
              onclick="sahkan('LH-035', this)">Sahkan</button>
          </td>
        </tr>

        <tr data-status="Menunggu" data-search="lh-034 pengawas b pondasi batu kali">
          <td style="color:var(--muted);font-weight:600;">LH-034</td>
          <td>28 Mar 2026</td>
          <td>Pengawas B</td>
          <td>Pemasangan pondasi batu kali</td>
          <td>10</td>
          <td>☀️ Cerah</td>
          <td><span class="badge badge-done">5 foto</span></td>
          <td><span class="badge badge-wait">Menunggu</span></td>
          <td style="display:flex;gap:6px;flex-wrap:wrap;">
            <button class="btn btn-gold btn-sm"
              onclick="openModal('LH-034','28 Mar 2026','Pengawas B',
                'Pemasangan pondasi batu kali zona A, 10 TK, cerah, tanpa kendala.','Menunggu')">
              Detail
            </button>
            <button class="btn btn-outline btn-sm" style="color:#22c55e;border-color:rgba(34,197,94,.3);"
              onclick="sahkan('LH-034', this)">Sahkan</button>
          </td>
        </tr>

        <tr data-status="Tervalidasi" data-search="lh-036 pengawas a penulangan plat">
          <td style="color:var(--muted);font-weight:600;">LH-036</td>
          <td>30 Mar 2026</td>
          <td>Pengawas A</td>
          <td>Penulangan plat lantai</td>
          <td>14</td>
          <td>☀️ Cerah</td>
          <td><span class="badge badge-done">4 foto</span></td>
          <td><span class="badge badge-done">Tervalidasi</span></td>
          <td>
            <button class="btn btn-outline btn-sm"
              onclick="openModal('LH-036','30 Mar 2026','Pengawas A',
                'Penulangan plat lantai 3, 14 TK, cerah, 2 alat. Disahkan 30/3/2026.','Tervalidasi')">
              Detail
            </button>
          </td>
        </tr>

        <tr data-status="Tervalidasi" data-search="lh-033 pengawas a pemadatan tanah">
          <td style="color:var(--muted);font-weight:600;">LH-033</td>
          <td>27 Mar 2026</td>
          <td>Pengawas A</td>
          <td>Pemadatan tanah urug</td>
          <td>8</td>
          <td>☀️ Cerah</td>
          <td><span class="badge badge-done">3 foto</span></td>
          <td><span class="badge badge-done">Tervalidasi</span></td>
          <td>
            <button class="btn btn-outline btn-sm"
              onclick="openModal('LH-033','27 Mar 2026','Pengawas A',
                'Pemadatan tanah urug zone C, 8 TK, cerah.','Tervalidasi')">
              Detail
            </button>
          </td>
        </tr>

      </tbody>
    </table>
  </div>

  <!-- Empty state (hidden by default) -->
  <div id="empty-state" style="display:none;text-align:center;padding:60px 20px;color:var(--muted);">
    <div style="font-size:40px;margin-bottom:12px;">🔍</div>
    <div style="font-size:14px;">Tidak ada laporan ditemukan</div>
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

// Filter (search + status)
function filterTable(){
  const search = document.getElementById('search-input').value.toLowerCase();
  const status = document.getElementById('status-filter').value;
  let visible = 0;
  document.querySelectorAll('#tinjau-tbody tr').forEach(row => {
    const matchSearch = !search || row.dataset.search.includes(search);
    const matchStatus = !status || row.dataset.status === status;
    const show = matchSearch && matchStatus;
    row.style.display = show ? '' : 'none';
    if(show) visible++;
  });
  document.getElementById('empty-state').style.display = visible === 0 ? 'block' : 'none';
}

// Sahkan
function sahkan(id, btn){
  const row = btn.closest('tr');
  row.dataset.status = 'Tervalidasi';
  row.querySelector('.badge.badge-wait').className = 'badge badge-done';
  row.querySelector('.badge.badge-done').textContent = 'Tervalidasi';
  const aksiCell = row.querySelector('td:last-child');
  aksiCell.style.display = '';
  aksiCell.innerHTML = `<button class="btn btn-outline btn-sm"
    onclick="openModal('${id}','','','','Tervalidasi')">Detail</button>`;
  showToast('Laporan ' + id + ' berhasil disahkan ✓','success');
}

// Modal
function openModal(id, tgl, pengawas, deskripsi, status){
  document.getElementById('modal-title').textContent = 'Detail Laporan ' + id;
  document.getElementById('modal-body').innerHTML = `
    <div class="detail-row"><span class="key">ID Laporan</span><span>${id}</span></div>
    <div class="detail-row"><span class="key">Tanggal</span><span>${tgl}</span></div>
    <div class="detail-row"><span class="key">Pengawas Lapangan</span><span>${pengawas}</span></div>
    <div class="detail-row"><span class="key">Deskripsi Pekerjaan</span>
      <span style="text-align:right;max-width:280px;">${deskripsi}</span></div>
    <div class="detail-row"><span class="key">Status</span>
      <span class="badge ${status==='Tervalidasi'?'badge-done':'badge-wait'}">${status}</span></div>
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
      <button class="btn btn-gold"
        onclick="closeModal();showToast('Laporan ${id} berhasil disahkan ✓','success')">
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