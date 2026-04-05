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
    <title>Tinjau Laporan Harian | CV Cipta Manunggal</title>
    <link rel="stylesheet" href="../kontraktor/asset/kontraktordash.css">
    <style>
        .badge-notif { background: #ffc107; color: #000; padding: 2px 8px; border-radius: 10px; font-size: 12px; margin-left: 5px; font-weight: bold; }
        .btn-detail { background: transparent; border: 1px solid rgba(255,255,255,0.2); color: #fff; padding: 8px 14px; border-radius: 6px; cursor: pointer; font-weight: 600; font-size: 13px; transition: 0.3s; min-width: 90px; display: inline-flex; align-items: center; justify-content: center; }
        .btn-detail:hover { border-color: #ffc107; color: #ffc107; }
        .btn-outline, .btn-detail, .btn-gold { display: inline-flex; align-items: center; justify-content: center; gap: 6px; }
        .btn-outline { background: transparent; border: 1px solid rgba(255,255,255,0.2); color: #ccc; padding: 8px 14px; border-radius: 6px; cursor: pointer; font-weight: 500; font-size: 13px; transition: 0.3s; min-width: 90px; }
        .btn-outline:hover { border-color: #ffc107; color: #ffc107; }
        .btn-gold { background: #ffc107; color: #111; padding: 8px 14px; border-radius: 6px; cursor: pointer; font-weight: bold; font-size: 13px; border: none; transition: 0.3s; min-width: 90px; }
        .btn-gold:hover { background: #e0a800; }
        .action-group { display: flex; gap: 10px; flex-wrap: wrap; align-items: center; }
        .activity-card table th, .activity-card table td { padding: 14px 16px; }
        .activity-card table tr:hover { background: rgba(255,255,255,0.04); }
        .modal-overlay { display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.7); z-index: 1000; align-items: center; justify-content: center; }
        .modal-overlay.open { display: flex; }
        .modal { background: #1a1a1a; border-radius: 8px; padding: 30px; min-width: 400px; max-width: 600px; border: 1px solid rgba(255,193,7,0.2); }
        .modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .modal-header h3 { margin: 0; font-size: 18px; }
        .modal-close { background: none; border: none; color: #888; font-size: 28px; cursor: pointer; }
        .modal-close:hover { color: #fff; }
        .detail-row { display: flex; justify-content: space-between; align-items: center; padding: 12px 0; border-bottom: 1px solid rgba(255,255,255,0.05); }
        .detail-row .key { color: #888; font-size: 12px; }
        .modal-actions { display: flex; gap: 10px; margin-top: 20px; justify-content: flex-end; }
        .badge { padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: 600; display: inline-block; }
        .badge-wait { background: rgba(245,158,11,0.2); color: #f59e0b; }
        .badge-done { background: rgba(34,197,94,0.2); color: #22c55e; }
        .toast { position: fixed; bottom: 20px; right: 20px; background: #1a1a1a; color: #fff; padding: 12px 16px; border-radius: 6px; border-left: 4px solid #ffc107; display: none; z-index: 2000; }
        .toast.show { display: block; }
        .toast.success { border-left-color: #22c55e; }
        .search-box { background: #1c1c1c; border: 1px solid rgba(255,255,255,0.1); border-radius: 6px; padding: 8px 12px; color: #fff; width: 180px; }
        .search-box:focus { outline: none; border-color: #ffc107; }
        .filter-select { background: #1c1c1c; border: 1px solid rgba(255,255,255,0.1); border-radius: 6px; padding: 8px 12px; color: #fff; }
        .filter-select:focus { outline: none; border-color: #ffc107; }
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
            <a href="Tinjau_laporan_harian.php" class="active">Tinjau Laporan Harian</a>
            <a href="susun_laporan_mingguan.php">Susun Laporan Mingguan</a>
            <a href="riwayat_laporan_mingguan.php">Riwayat Laporan</a>
            <a href="../logout.php" class="logout">Logout</a>
        </nav>
    </aside>

    <main class="main-content">
        <header class="topbar">
            <div>
                <h1>Tinjau Laporan Harian</h1>
                <p>Tinjauan & pengesahan laporan dari Pengawas Lapangan</p>
            </div>
            <div class="role-badge">KOORDINATOR PENGAWAS</div>
        </header>

        <section style="margin-bottom: 20px;">
            <div style="display: flex; gap: 10px; margin-bottom: 15px;">
                <input type="text" id="search-input" class="search-box" placeholder="Cari laporan..." oninput="filterTable()">
                <select id="status-filter" class="filter-select" onchange="filterTable()">
                    <option value="">Semua Status</option>
                    <option value="Menunggu">Menunggu</option>
                    <option value="Tervalidasi">Tervalidasi</option>
                </select>
            </div>
        </section>

        <section class="grid-section">
            <div class="activity-card" style="flex: 1;">
                <h3>Laporan Harian</h3>
                <table width="100%" style="margin-top: 15px; color: #fff; border-collapse: collapse;">
                    <thead>
                        <tr style="text-align: left; border-bottom: 1px solid #444; color: #888;">
                            <th style="padding: 10px;">ID</th>
                            <th>Tanggal</th>
                            <th>Pengawas</th>
                            <th>Progres</th>
                            <th>TK</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tinjau-tbody">

                        <tr data-status="Menunggu" data-search="lh-038 pengawas a pengecoran">
                            <td style="padding: 15px; color: #888;">LH-038</td>
                            <td>01 Apr 2026</td>
                            <td>Pengawas A</td>
                            <td>Pengecoran kolom Lt.3</td>
                            <td>12</td>
                            <td><span class="badge badge-wait">Menunggu</span></td>
                            <td>
                                <div class="action-group">
                                    <button class="btn-detail" onclick="openModal('LH-038','01 Apr 2026','Pengawas A','Pengecoran kolom Lt.3, 12 TK, cerah','Menunggu')">Detail</button>
                                    <button class="btn-outline" onclick="sahkan('LH-038', this)">Sahkan</button>
                                </div>
                            </td>
                        </tr>
                        <tr data-status="Menunggu" data-search="lh-037 pengawas b bekisting">
                            <td style="padding: 15px; color: #888;">LH-037</td>
                            <td>31 Mar 2026</td>
                            <td>Pengawas B</td>
                            <td>Pemasangan bekisting</td>
                            <td>9</td>
                            <td><span class="badge badge-wait">Menunggu</span></td>
                            <td>
                                <div class="action-group">
                                    <button class="btn-detail" onclick="openModal('LH-037','31 Mar 2026','Pengawas B','Pemasangan bekisting area selatan, 9 TK','Menunggu')">Detail</button>
                                    <button class="btn-outline" onclick="sahkan('LH-037', this)">Sahkan</button>
                                </div>
                            </td>
                        </tr>
                        <tr data-status="Tervalidasi" data-search="lh-036 pengawas a penulangan">
                            <td style="padding: 15px; color: #888;">LH-036</td>
                            <td>30 Mar 2026</td>
                            <td>Pengawas A</td>
                            <td>Penulangan plat lantai</td>
                            <td>14</td>
                            <td><span class="badge badge-done">Tervalidasi</span></td>
                            <td>
                                <div class="action-group">
                                    <button class="btn-detail">Detail</button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</div>

<!-- MODAL -->
<div class="modal-overlay" id="modal">
    <div class="modal">
        <div class="modal-header">
            <h3 id="modal-title">Detail Laporan</h3>
            <button class="modal-close" onclick="closeModal()">✕</button>
        </div>
        <div id="modal-body"></div>
        <div class="modal-actions">
            <button class="btn-outline" onclick="closeModal()">Tutup</button>
            <button class="btn-gold" id="modal-btn-sahkan" onclick="sahkanModal()" style="display:none;">Sahkan</button>
        </div>
    </div>
</div>

<!-- TOAST -->
<div class="toast" id="toast"></div>

<script>
function filterTable(){
    const search = document.getElementById('search-input').value.toLowerCase();
    const status = document.getElementById('status-filter').value;
    document.querySelectorAll('#tinjau-tbody tr').forEach(row => {
        const matchSearch = !search || row.dataset.search.includes(search);
        const matchStatus = !status || row.dataset.status === status;
        row.style.display = (matchSearch && matchStatus) ? '' : 'none';
    });
}

function sahkan(id, btn){
    const row = btn.closest('tr');
    row.dataset.status = 'Tervalidasi';
    btn.parentElement.innerHTML = '<button class="btn-outline">Detail</button>';
    const badge = row.querySelector('.badge');
    badge.className = 'badge badge-done';
    badge.textContent = 'Tervalidasi';
    showToast('Laporan ' + id + ' berhasil disahkan ✓','success');
}

function openModal(id, tgl, pengawas, deskripsi, status){
    document.getElementById('modal-title').textContent = 'Detail Laporan ' + id;
    let html = `<div class="detail-row"><span class="key">ID</span><span>${id}</span></div>
                <div class="detail-row"><span class="key">Tanggal</span><span>${tgl}</span></div>
                <div class="detail-row"><span class="key">Pengawas</span><span>${pengawas}</span></div>
                <div class="detail-row"><span class="key">Deskripsi</span><span>${deskripsi}</span></div>
                <div class="detail-row"><span class="key">Status</span><span class="badge ${status==='Tervalidasi'?'badge-done':'badge-wait'}">${status}</span></div>`;
    document.getElementById('modal-body').innerHTML = html;
    document.getElementById('modal-btn-sahkan').style.display = (status==='Menunggu')?'block':'none';
    document.getElementById('modal').classList.add('open');
}

function closeModal(){
    document.getElementById('modal').classList.remove('open');
}

function sahkanModal(){
    closeModal();
    showToast('Laporan berhasil disahkan ✓','success');
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