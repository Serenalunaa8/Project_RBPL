<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard Koordinator – Sistem Pengawasan Proyek</title>
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
<style>
:root {
  --gold: #ffc107;
  --gold-dim: #e6a800;
  --gold-glow: rgba(255,193,7,0.18);
  --bg: #0d0d0d;
  --surface: #141414;
  --surface2: #1a1a1a;
  --surface3: #222222;
  --border: rgba(255,255,255,0.06);
  --border-gold: rgba(255,193,7,0.25);
  --text: #f0f0f0;
  --muted: #888;
  --danger: #ff4d4d;
  --success: #22c55e;
  --info: #38bdf8;
  --warn: #f59e0b;
}
*{margin:0;padding:0;box-sizing:border-box;}
body{font-family:'DM Sans',sans-serif;background:var(--bg);color:var(--text);display:flex;min-height:100vh;overflow-x:hidden;}

── AMBIENT GLOW ──
body::before{content:"";position:fixed;width:600px;height:600px;
  background:radial-gradient(circle,rgba(255,193,7,0.07) 0%,transparent 70%);
  top:30%;left:15%;filter:blur(80px);z-index:0;pointer-events:none;}

/* ── SIDEBAR ── */
.sidebar{
  width:260px;min-width:260px;background:var(--surface);
  border-right:1px solid var(--border);
  display:flex;flex-direction:column;padding:28px 0;
  position:fixed;top:0;left:0;height:100vh;z-index:100;
  animation:slideLeft .6s ease both;
}
@keyframes slideLeft{from{opacity:0;transform:translateX(-30px);}to{opacity:1;transform:translateX(0);}}

/* ── LOGO ── */
.brand{padding:0 22px 28px;border-bottom:1px solid var(--border);}
.brand-inner{display:flex;align-items:center;gap:12px;}
.logo-svg{flex-shrink:0;}
.brand-text h1{font-family:'Syne',sans-serif;font-size:14px;font-weight:700;letter-spacing:.5px;line-height:1.3;}
.brand-text h1 span{color:var(--gold);}
.brand-text p{font-size:10px;color:var(--muted);letter-spacing:2px;text-transform:uppercase;margin-top:2px;}

/* ── NAV ── */
.nav-section{padding:20px 14px 0;}
.nav-label{font-size:10px;color:var(--muted);letter-spacing:2px;text-transform:uppercase;padding:0 8px;margin-bottom:8px;}
.nav-item{
  display:flex;align-items:center;gap:10px;
  padding:10px 12px;border-radius:8px;cursor:pointer;
  color:#aaa;font-size:13.5px;font-weight:400;
  transition:all .25s;position:relative;border:none;background:none;width:100%;text-align:left;
}
.nav-item:hover{background:rgba(255,193,7,.07);color:#ddd;}
.nav-item.active{background:rgba(255,193,7,.12);color:var(--gold);font-weight:500;}
.nav-item.active::before{content:"";position:absolute;left:0;top:20%;bottom:20%;width:3px;background:var(--gold);border-radius:0 3px 3px 0;}
.nav-icon{width:16px;height:16px;opacity:.7;}
.nav-item.active .nav-icon{opacity:1;}
.nav-badge{margin-left:auto;background:var(--danger);color:#fff;font-size:10px;padding:1px 6px;border-radius:10px;font-weight:600;}
.nav-badge.gold{background:var(--gold);color:#111;}

.sidebar-footer{margin-top:auto;padding:16px 14px;border-top:1px solid var(--border);}
.user-card{display:flex;align-items:center;gap:10px;padding:10px 12px;border-radius:8px;background:var(--surface2);}
.avatar{width:34px;height:34px;border-radius:50%;background:var(--gold);display:flex;align-items:center;justify-content:center;font-family:'Syne',sans-serif;font-size:13px;font-weight:700;color:#111;flex-shrink:0;}
.user-info p{font-size:13px;font-weight:500;}
.user-info span{font-size:11px;color:var(--muted);}
.logout-btn{display:flex;align-items:center;gap:8px;width:100%;padding:9px 12px;margin-top:8px;background:none;border:1px solid var(--border);border-radius:8px;color:#888;font-size:13px;cursor:pointer;transition:.2s;}
.logout-btn:hover{border-color:var(--danger);color:var(--danger);}

/* ── MAIN ── */
.main{margin-left:260px;flex:1;padding:36px 40px;position:relative;z-index:1;}
.topbar{display:flex;align-items:center;justify-content:space-between;margin-bottom:36px;}
.topbar h2{font-family:'Syne',sans-serif;font-size:22px;font-weight:700;}
.topbar-right{display:flex;align-items:center;gap:12px;}
.date-chip{font-size:12px;color:var(--muted);background:var(--surface2);padding:6px 14px;border-radius:20px;border:1px solid var(--border);}
.notif-btn{width:36px;height:36px;border-radius:50%;background:var(--surface2);border:1px solid var(--border);display:flex;align-items:center;justify-content:center;cursor:pointer;position:relative;transition:.2s;}
.notif-btn:hover{border-color:var(--gold);}
.notif-dot{position:absolute;top:6px;right:6px;width:7px;height:7px;background:var(--danger);border-radius:50%;border:1.5px solid var(--bg);}

/* ── PAGES ── */
.page{display:none;animation:fadeUp .4s ease both;}
.page.active{display:block;}
@keyframes fadeUp{from{opacity:0;transform:translateY(16px);}to{opacity:1;transform:translateY(0);}}

/* ── STAT CARDS ── */
.stats-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:28px;}
.stat-card{background:var(--surface);border:1px solid var(--border);border-radius:14px;padding:22px;position:relative;overflow:hidden;transition:all .3s;cursor:default;}
.stat-card:hover{border-color:var(--border-gold);transform:translateY(-3px);}
.stat-card::after{content:"";position:absolute;bottom:0;left:0;right:0;height:2px;background:linear-gradient(90deg,transparent,var(--gold),transparent);opacity:0;transition:.3s;}
.stat-card:hover::after{opacity:1;}
.stat-label{font-size:11.5px;color:var(--muted);text-transform:uppercase;letter-spacing:1px;margin-bottom:10px;}
.stat-val{font-family:'Syne',sans-serif;font-size:32px;font-weight:700;color:var(--gold);line-height:1;}
.stat-sub{font-size:11.5px;color:var(--muted);margin-top:6px;}
.stat-icon{position:absolute;top:20px;right:20px;opacity:.15;font-size:28px;}

/* ── SECTION HEADER ── */
.section-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;}
.section-title{font-family:'Syne',sans-serif;font-size:15px;font-weight:700;}
.section-sub{font-size:12px;color:var(--muted);}
.btn{padding:8px 16px;border-radius:8px;font-size:12.5px;font-weight:500;cursor:pointer;transition:.2s;border:none;font-family:'DM Sans',sans-serif;}
.btn-gold{background:var(--gold);color:#111;}
.btn-gold:hover{background:#ffca2c;transform:translateY(-1px);}
.btn-outline{background:transparent;border:1px solid var(--border);color:#aaa;}
.btn-outline:hover{border-color:var(--gold);color:var(--gold);}
.btn-sm{padding:5px 12px;font-size:11.5px;}

/* ── TABLE ── */
.table-wrap{background:var(--surface);border:1px solid var(--border);border-radius:14px;overflow:hidden;}
table{width:100%;border-collapse:collapse;}
thead tr{background:var(--surface2);border-bottom:1px solid var(--border);}
th{padding:12px 16px;text-align:left;font-size:11px;text-transform:uppercase;letter-spacing:1px;color:var(--muted);font-weight:500;}
td{padding:13px 16px;font-size:13px;border-bottom:1px solid var(--border);}
tr:last-child td{border-bottom:none;}
tr:hover td{background:rgba(255,255,255,.02);}

/* ── BADGES ── */
.badge{display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:500;}
.badge-wait{background:rgba(245,158,11,.15);color:#f59e0b;}
.badge-done{background:rgba(34,197,94,.15);color:#22c55e;}
.badge-reject{background:rgba(255,77,77,.15);color:#ff4d4d;}
.badge-review{background:rgba(56,189,248,.15);color:#38bdf8;}
.badge-draft{background:rgba(255,255,255,.08);color:#aaa;}

/* ── CARD PANEL ── */
.panel{background:var(--surface);border:1px solid var(--border);border-radius:14px;padding:24px;margin-bottom:20px;}
.panel-title{font-family:'Syne',sans-serif;font-size:14px;font-weight:700;margin-bottom:16px;display:flex;align-items:center;gap:8px;}
.panel-title::before{content:"";width:3px;height:16px;background:var(--gold);border-radius:2px;}

/* ── FORM ── */
.form-row{display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;}
.form-row.full{grid-template-columns:1fr;}
.form-group{display:flex;flex-direction:column;gap:6px;}
.form-group label{font-size:12px;color:var(--muted);text-transform:uppercase;letter-spacing:.8px;}
.form-group input,.form-group textarea,.form-group select{
  background:var(--surface2);border:1px solid var(--border);
  border-radius:8px;padding:10px 14px;color:var(--text);font-size:13px;
  font-family:'DM Sans',sans-serif;outline:none;transition:.2s;
}
.form-group input:focus,.form-group textarea:focus,.form-group select:focus{border-color:var(--gold);}
.form-group textarea{resize:vertical;min-height:80px;}
.form-group select option{background:var(--surface2);}

/* ── DETAIL MODAL ── */
.modal-overlay{position:fixed;inset:0;background:rgba(0,0,0,.7);z-index:200;display:none;align-items:center;justify-content:center;backdrop-filter:blur(4px);}
.modal-overlay.open{display:flex;}
.modal{background:var(--surface);border:1px solid var(--border-gold);border-radius:16px;width:560px;max-width:94vw;max-height:85vh;overflow-y:auto;animation:fadeUp .3s ease;}
.modal-header{padding:22px 24px 16px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;}
.modal-header h3{font-family:'Syne',sans-serif;font-size:16px;font-weight:700;}
.modal-close{background:none;border:none;color:var(--muted);font-size:20px;cursor:pointer;padding:4px;line-height:1;}
.modal-close:hover{color:var(--text);}
.modal-body{padding:22px 24px;}
.detail-row{display:flex;justify-content:space-between;align-items:flex-start;padding:9px 0;border-bottom:1px solid var(--border);font-size:13px;}
.detail-row:last-child{border-bottom:none;}
.detail-row .key{color:var(--muted);min-width:160px;}
.modal-actions{padding:16px 24px;border-top:1px solid var(--border);display:flex;gap:10px;justify-content:flex-end;}

/* ── PROGRESS BAR ── */
.progress-wrap{background:var(--surface3);border-radius:4px;height:6px;overflow:hidden;}
.progress-bar{height:100%;background:linear-gradient(90deg,var(--gold-dim),var(--gold));border-radius:4px;transition:width .6s ease;}

/* ── TIMELINE ── */
.timeline{display:flex;flex-direction:column;gap:0;}
.tl-item{display:flex;gap:14px;position:relative;}
.tl-item::before{content:"";position:absolute;left:14px;top:28px;bottom:-1px;width:1px;background:var(--border);}
.tl-item:last-child::before{display:none;}
.tl-dot{width:28px;height:28px;border-radius:50%;background:var(--surface2);border:1px solid var(--border-gold);display:flex;align-items:center;justify-content:center;font-size:11px;flex-shrink:0;margin-top:2px;}
.tl-dot.done{background:rgba(34,197,94,.15);border-color:rgba(34,197,94,.4);}
.tl-content{padding-bottom:18px;}
.tl-content p{font-size:13px;font-weight:500;}
.tl-content span{font-size:11.5px;color:var(--muted);}

/* ── CHART BARS ── */
.chart-bar-wrap{display:flex;align-items:flex-end;gap:6px;height:100px;padding:8px 0;}
.chart-bar{flex:1;background:var(--surface3);border-radius:4px 4px 0 0;position:relative;transition:.5s ease;cursor:default;}
.chart-bar:hover{filter:brightness(1.2);}
.chart-bar .bar-fill{position:absolute;bottom:0;left:0;right:0;background:linear-gradient(to top,var(--gold-dim),var(--gold));border-radius:4px 4px 0 0;transition:height .6s ease;}
.chart-label{text-align:center;font-size:10px;color:var(--muted);margin-top:6px;}

/* ── TOAST ── */
.toast{position:fixed;bottom:28px;right:28px;background:var(--surface);border:1px solid var(--border-gold);border-radius:10px;padding:12px 20px;font-size:13px;color:var(--text);z-index:999;transform:translateY(80px);opacity:0;transition:.3s;pointer-events:none;}
.toast.show{transform:translateY(0);opacity:1;}
.toast.success{border-color:rgba(34,197,94,.4);color:var(--success);}

/* RESPONSIVE */
@media(max-width:900px){
  .stats-grid{grid-template-columns:1fr 1fr;}
  .form-row{grid-template-columns:1fr;}
}
</style>
</head>
<body>

<!-- ══════════ SIDEBAR ══════════ -->
<aside class="sidebar">
  <div class="brand">
    <div class="brand-inner">
      <!-- LOGO SVG -->
      <svg class="logo-svg" width="44" height="44" viewBox="0 0 44 44" fill="none" xmlns="http://www.w3.org/2000/svg">
        <!-- outer ring accent -->
        <circle cx="22" cy="22" r="21" stroke="#ffc107" stroke-width="0.6" stroke-opacity="0.3"/>
        <!-- building body -->
        <rect x="9" y="17" width="20" height="20" rx="1.5" fill="#1f1f1f" stroke="#ffc107" stroke-width="0.8" stroke-opacity="0.5"/>
        <!-- crown / gable -->
        <polygon points="19,7 8,17 30,17" fill="#ffc107" opacity="0.85"/>
        <!-- spire -->
        <line x1="19" y1="2" x2="19" y2="8" stroke="#ffc107" stroke-width="1.6" stroke-linecap="round"/>
        <circle cx="19" cy="1.5" r="1.5" fill="#ffc107" opacity="0.8"/>
        <!-- windows row 1 -->
        <rect x="12" y="20" width="4" height="4" rx="0.8" fill="#ffc107" opacity="0.9"/>
        <rect x="18" y="20" width="4" height="4" rx="0.8" fill="#ffc107" opacity="0.4"/>
        <rect x="24" y="20" width="4" height="4" rx="0.8" fill="#ffc107" opacity="0.8"/>
        <!-- windows row 2 -->
        <rect x="12" y="27" width="4" height="4" rx="0.8" fill="#ffc107" opacity="0.5"/>
        <rect x="18" y="27" width="4" height="4" rx="0.8" fill="#ffc107" opacity="1"/>
        <rect x="24" y="27" width="4" height="4" rx="0.8" fill="#ffc107" opacity="0.6"/>
        <!-- door -->
        <rect x="15" y="32" width="7" height="5" rx="1" fill="#ffc107" opacity="0.65"/>
        <!-- corner accent -->
        <circle cx="35" cy="12" r="5" fill="none" stroke="#ffc107" stroke-width="0.7" stroke-opacity="0.5"/>
        <circle cx="35" cy="12" r="2" fill="#ffc107" opacity="0.25"/>
      </svg>
      <div class="brand-text">
        <h1>Sistem <span>Pengawasan</span></h1>
        <p>Koordinator</p>
      </div>
    </div>
  </div>

  <nav class="nav-section" style="flex:1;overflow-y:auto;">
    <div class="nav-label" style="margin-top:4px;">Menu Utama</div>
    <button class="nav-item active" onclick="showPage('dashboard',this)">
      <svg class="nav-icon" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="1" y="1" width="6" height="6" rx="1.2"/><rect x="9" y="1" width="6" height="6" rx="1.2"/><rect x="1" y="9" width="6" height="6" rx="1.2"/><rect x="9" y="9" width="6" height="6" rx="1.2"/></svg>
      Dashboard
    </button>
    <button class="nav-item" onclick="showPage('tinjau',this)">
      <svg class="nav-icon" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M2 4h12M2 8h9M2 12h6"/><circle cx="13" cy="11" r="2.5"/><path d="M15 13l1 1"/></svg>
      Tinjau Laporan Harian
    </button>
    <button class="nav-item" onclick="showPage('susun',this)">
      <svg class="nav-icon" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M3 2h10v12H3z"/><path d="M6 5h4M6 8h4M6 11h2"/></svg>
      Susun Laporan Mingguan
    </button>
    <button class="nav-item" onclick="showPage('riwayat',this)">
      <svg class="nav-icon" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="8" cy="8" r="6"/><path d="M8 5v3.5l2 1.5"/></svg>
      Riwayat Laporan
    </button>
  </nav>

  <div class="sidebar-footer">
    <div class="user-card">
      <div class="avatar">KP</div>
      <div class="user-info">
        <p>Koordinator</p>
        <span>Pengawas Aktif</span>
      </div>
    </div>
    <button class="logout-btn" onclick="showToast('Sesi diakhiri. Redirect ke login...','')">
      <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M11 5l3 3-3 3M7 8h7"/><path d="M7 2H3a1 1 0 00-1 1v10a1 1 0 001 1h4"/></svg>
      Logout
    </button>
  </div>
</aside>

<!-- ══════════ MAIN ══════════ -->
<main class="main">

  <!-- TOPBAR -->
  <div class="topbar">
    <h2 id="page-title">Dashboard</h2>
    <div class="topbar-right">
      <div class="date-chip" id="date-chip"></div>
      <button class="notif-btn" onclick="showPage('tinjau', document.querySelector('.nav-item:nth-child(2)'))">
        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="#aaa" stroke-width="1.5"><path d="M8 1a5 5 0 015 5v3l1 2H2l1-2V6a5 5 0 015-5z"/><path d="M6.5 13.5a1.5 1.5 0 003 0"/></svg>
        <div class="notif-dot"></div>
      </button>
    </div>
  </div>

  <!-- ══ PAGE: DASHBOARD ══ -->
  <div class="page active" id="page-dashboard">
    <div class="stats-grid">
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

    <!-- Chart + Recent -->
    <div style="display:grid;grid-template-columns:1.3fr 1fr;gap:20px;margin-bottom:20px;">
      <!-- Bar chart -->
      <div class="panel">
        <div class="panel-title">Laporan Harian per Minggu</div>
        <div style="display:grid;grid-template-columns:repeat(7,1fr);gap:6px;">
          <div>
            <div class="chart-bar-wrap"><div class="chart-bar" style="width:100%;height:100%;"><div class="bar-fill" style="height:60%;"></div></div></div>
            <div class="chart-label">Sn</div>
          </div>
          <div>
            <div class="chart-bar-wrap"><div class="chart-bar" style="width:100%;height:100%;"><div class="bar-fill" style="height:85%;"></div></div></div>
            <div class="chart-label">Sl</div>
          </div>
          <div>
            <div class="chart-bar-wrap"><div class="chart-bar" style="width:100%;height:100%;"><div class="bar-fill" style="height:45%;"></div></div></div>
            <div class="chart-label">Rb</div>
          </div>
          <div>
            <div class="chart-bar-wrap"><div class="chart-bar" style="width:100%;height:100%;"><div class="bar-fill" style="height:100%;"></div></div></div>
            <div class="chart-label">Km</div>
          </div>
          <div>
            <div class="chart-bar-wrap"><div class="chart-bar" style="width:100%;height:100%;"><div class="bar-fill" style="height:70%;"></div></div></div>
            <div class="chart-label">Jm</div>
          </div>
          <div>
            <div class="chart-bar-wrap"><div class="chart-bar" style="width:100%;height:100%;"><div class="bar-fill" style="height:30%;"></div></div></div>
            <div class="chart-label">Sb</div>
          </div>
          <div>
            <div class="chart-bar-wrap"><div class="chart-bar" style="width:100%;height:100%;"><div class="bar-fill" style="height:10%;"></div></div></div>
            <div class="chart-label">Mg</div>
          </div>
        </div>
        <div style="margin-top:16px;">
          <div style="font-size:12px;color:var(--muted);margin-bottom:8px;">Progress Laporan Keseluruhan</div>
          <div class="progress-wrap"><div class="progress-bar" style="width:42%;"></div></div>
          <div style="display:flex;justify-content:space-between;font-size:11px;color:var(--muted);margin-top:5px;"></div>
        </div>
      </div>

      <!-- Sprint Status -->
      <div class="panel">
        <div class="panel-title">Status Laporan</div>
        <div class="timeline">
          <div class="tl-item">
          </div>
          <div class="tl-item">
          </div>
          <div class="tl-item">
          </div>
          <div class="tl-item">
          </div>
          <div class="tl-item">
          </div>
          <div class="tl-item">
          </div>
        </div>
      </div>
    </div>

    <!-- Recent laporan harian -->
    <div class="section-header">
      <div><div class="section-title">Laporan Harian Terbaru</div><div class="section-sub">Menunggu tinjauan & pengesahan</div></div>
      <button class="btn btn-outline btn-sm" onclick="showPage('tinjau', document.querySelectorAll('.nav-item')[1])">Lihat Semua →</button>
    </div>
    <div class="table-wrap">
      <table>
        <thead><tr><th>#</th><th>Tanggal</th><th>Pengawas Lapangan</th><th>Progres Pekerjaan</th><th>Status</th><th>Aksi</th></tr></thead>
        <tbody>
          <tr>
            <td style="color:var(--muted)">LH-038</td><td>01 Apr 2026</td><td>Pengawas A</td><td>Pengecoran kolom Lt.3</td>
            <td><span class="badge badge-wait">Menunggu</span></td>
            <td><button class="btn btn-gold btn-sm" onclick="openModal('LH-038','01 Apr 2026','Pengawas A','Pengecoran kolom Lt.3, 12 TK, cuaca cerah, 3 alat berat','Menunggu')">Tinjau</button></td>
          </tr>
          <tr>
            <td style="color:var(--muted)">LH-037</td><td>31 Mar 2026</td><td>Pengawas B</td><td>Pemasangan bekisting</td>
            <td><span class="badge badge-wait">Menunggu</span></td>
            <td><button class="btn btn-gold btn-sm" onclick="openModal('LH-037','31 Mar 2026','Pengawas B','Pemasangan bekisting area selatan, 9 TK, cuaca berawan','Menunggu')">Tinjau</button></td>
          </tr>
          <tr>
            <td style="color:var(--muted)">LH-036</td><td>30 Mar 2026</td><td>Pengawas A</td><td>Penulangan plat lantai</td>
            <td><span class="badge badge-done">Tervalidasi</span></td>
            <td><button class="btn btn-outline btn-sm" onclick="openModal('LH-036','30 Mar 2026','Pengawas A','Penulangan plat lantai 3, 14 TK, cuaca cerah, 2 alat','Tervalidasi')">Detail</button></td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

  <!-- ══ PAGE: TINJAU ══ -->
  <div class="page" id="page-tinjau">
    <div class="section-header">
      <div><div class="section-title">Tinjau Laporan Harian</div><div class="section-sub">Tinjauan & pengesahan laporan dari Pengawas Lapangan</div></div>
      <div style="display:flex;gap:8px;">
        <select style="background:var(--surface2);border:1px solid var(--border);border-radius:8px;padding:7px 12px;color:var(--text);font-size:12.5px;outline:none;" onchange="filterStatus(this.value)">
          <option value="">Semua Status</option>
          <option value="Menunggu">Menunggu</option>
          <option value="Tervalidasi">Tervalidasi</option>
          <option value="Ditolak">Ditolak</option>
        </select>
      </div>
    </div>
    <div class="table-wrap" id="tinjau-table">
      <table>
        <thead><tr><th>ID</th><th>Tanggal</th><th>Pengawas</th><th>Progres</th><th>TK</th><th>Cuaca</th><th>Foto</th><th>Status</th><th>Aksi</th></tr></thead>
        <tbody id="tinjau-tbody">
          <tr data-status="Menunggu">
            <td style="color:var(--muted)">LH-038</td><td>01 Apr 2026</td><td>Pengawas A</td><td>Pengecoran kolom Lt.3</td><td>12</td><td>☀️ Cerah</td><td><span class="badge badge-done">3 foto</span></td>
            <td><span class="badge badge-wait">Menunggu</span></td>
            <td style="display:flex;gap:6px;flex-wrap:wrap;">
              <button class="btn btn-gold btn-sm" onclick="openModal('LH-038','01 Apr 2026','Pengawas A','Pengecoran kolom Lt.3, 12 TK, cuaca cerah, 3 alat berat. Tidak ada kendala.','Menunggu')">Detail</button>
              <button class="btn btn-outline btn-sm" style="color:#22c55e;border-color:rgba(34,197,94,.3);" onclick="sahkan('LH-038',this)">Sahkan</button>
            </td>
          </tr>
          <tr data-status="Menunggu">
            <td style="color:var(--muted)">LH-037</td><td>31 Mar 2026</td><td>Pengawas B</td><td>Pemasangan bekisting</td><td>9</td><td>🌤 Berawan</td><td><span class="badge badge-done">2 foto</span></td>
            <td><span class="badge badge-wait">Menunggu</span></td>
            <td style="display:flex;gap:6px;flex-wrap:wrap;">
              <button class="btn btn-gold btn-sm" onclick="openModal('LH-037','31 Mar 2026','Pengawas B','Pemasangan bekisting area selatan, 9 TK, cuaca berawan, 1 alat crane. Kendala: material terlambat 2 jam.','Menunggu')">Detail</button>
              <button class="btn btn-outline btn-sm" style="color:#22c55e;border-color:rgba(34,197,94,.3);" onclick="sahkan('LH-037',this)">Sahkan</button>
            </td>
          </tr>
          <tr data-status="Menunggu">
            <td style="color:var(--muted)">LH-035</td><td>29 Mar 2026</td><td>Pengawas A</td><td>Pekerjaan galian pondasi</td><td>18</td><td>⛅ Mendung</td><td><span class="badge badge-reject">0 foto</span></td>
            <td><span class="badge badge-wait">Menunggu</span></td>
            <td style="display:flex;gap:6px;flex-wrap:wrap;">
              <button class="btn btn-gold btn-sm" onclick="openModal('LH-035','29 Mar 2026','Pengawas A','Galian pondasi zona B, 18 TK, cuaca mendung, 2 excavator. Foto belum diunggah.','Menunggu')">Detail</button>
              <button class="btn btn-outline btn-sm" style="color:#22c55e;border-color:rgba(34,197,94,.3);" onclick="sahkan('LH-035',this)">Sahkan</button>
            </td>
          </tr>
          <tr data-status="Menunggu">
            <td style="color:var(--muted)">LH-034</td><td>28 Mar 2026</td><td>Pengawas B</td><td>Pemasangan pondasi batu kali</td><td>10</td><td>☀️ Cerah</td><td><span class="badge badge-done">5 foto</span></td>
            <td><span class="badge badge-wait">Menunggu</span></td>
            <td style="display:flex;gap:6px;flex-wrap:wrap;">
              <button class="btn btn-gold btn-sm" onclick="openModal('LH-034','28 Mar 2026','Pengawas B','Pemasangan pondasi batu kali zona A, 10 TK, cerah, tanpa kendala.','Menunggu')">Detail</button>
              <button class="btn btn-outline btn-sm" style="color:#22c55e;border-color:rgba(34,197,94,.3);" onclick="sahkan('LH-034',this)">Sahkan</button>
            </td>
          </tr>
          <tr data-status="Tervalidasi">
            <td style="color:var(--muted)">LH-036</td><td>30 Mar 2026</td><td>Pengawas A</td><td>Penulangan plat lantai</td><td>14</td><td>☀️ Cerah</td><td><span class="badge badge-done">4 foto</span></td>
            <td><span class="badge badge-done">Tervalidasi</span></td>
            <td><button class="btn btn-outline btn-sm" onclick="openModal('LH-036','30 Mar 2026','Pengawas A','Penulangan plat lantai 3, 14 TK, cerah, 2 alat. Disahkan 30/3/2026.','Tervalidasi')">Detail</button></td>
          </tr>
          <tr data-status="Tervalidasi">
            <td style="color:var(--muted)">LH-033</td><td>27 Mar 2026</td><td>Pengawas A</td><td>Pemadatan tanah urug</td><td>8</td><td>☀️ Cerah</td><td><span class="badge badge-done">3 foto</span></td>
            <td><span class="badge badge-done">Tervalidasi</span></td>
            <td><button class="btn btn-outline btn-sm" onclick="openModal('LH-033','27 Mar 2026','Pengawas A','Pemadatan tanah urug zone C, 8 TK, cerah.','Tervalidasi')">Detail</button></td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

  <!-- ══ PAGE: SUSUN LAPORAN MINGGUAN ══ -->
  <div class="page" id="page-susun">
    <div class="section-header">
      <div><div class="section-title">Susun Laporan Mingguan</div><div class="section-sub">Rekap laporan harian tervalidasi menjadi laporan mingguan</div></div>
    </div>

    <!-- Pilih periode -->
    <div class="panel">
      <div class="panel-title">1. Pilih Periode Minggu</div>
      <div class="form-row">
        <div class="form-group">
          <label>Minggu Ke</label>
          <select id="minggu-ke">
            <option>Minggu 11 (25 Mar – 01 Apr 2026)</option>
            <option>Minggu 10 (18 – 24 Mar 2026)</option>
            <option>Minggu 9 (11 – 17 Mar 2026)</option>
          </select>
        </div>
        <div class="form-group">
          <label>Nama Proyek</label>
          <input type="text" value="Proyek Konstruksi Gedung A" readonly style="opacity:.7;">
        </div>
      </div>
    </div>

    <!-- Laporan harian tervalidasi untuk periode ini -->
    <div class="panel">
      <div class="panel-title">2. Laporan Harian Tervalidasi (Periode Ini)</div>
      <div class="table-wrap" style="border:none;">
        <table>
          <thead><tr><th>Tanggal</th><th>Pengawas</th><th>Progres Pekerjaan</th><th>TK</th><th>Status</th></tr></thead>
          <tbody>
            <tr><td>01 Apr 2026</td><td>Pengawas A</td><td>Pengecoran kolom Lt.3</td><td>12</td><td><span class="badge badge-done">Tervalidasi</span></td></tr>
            <tr><td>31 Mar 2026</td><td>Pengawas B</td><td>Pemasangan bekisting</td><td>9</td><td><span class="badge badge-done">Tervalidasi</span></td></tr>
            <tr><td>30 Mar 2026</td><td>Pengawas A</td><td>Penulangan plat lantai</td><td>14</td><td><span class="badge badge-done">Tervalidasi</span></td></tr>
            <tr><td>28 Mar 2026</td><td>Pengawas B</td><td>Pemasangan pondasi</td><td>10</td><td><span class="badge badge-done">Tervalidasi</span></td></tr>
            <tr><td>27 Mar 2026</td><td>Pengawas A</td><td>Pemadatan tanah urug</td><td>8</td><td><span class="badge badge-done">Tervalidasi</span></td></tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Form susun -->
    <div class="panel">
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
          <textarea id="ringkasan" placeholder="Tuliskan ringkasan kemajuan pekerjaan selama minggu ini...&#10;cth: Minggu ini pengerjaan telah mencapai tahap pengecoran kolom lantai 3, pemasangan bekisting, dan penulangan plat lantai..."></textarea>
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

      <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:8px;">
        <button class="btn btn-outline" onclick="pratinjau()">👁 Pratinjau</button>
        <button class="btn btn-gold" onclick="simpanLaporan()">💾 Simpan Laporan Mingguan</button>
      </div>
    </div>
  </div>

  <!-- ══ PAGE: RIWAYAT ══ -->
  <div class="page" id="page-riwayat">
    <div class="section-header">
      <div><div class="section-title">Riwayat Laporan Mingguan</div><div class="section-sub">Arsip laporan yang telah tersimpan</div></div>
    </div>
    <div class="table-wrap">
      <table>
        <thead><tr><th>ID</th><th>Judul</th><th>Periode</th><th>Progress</th><th>TK Total</th><th>Status</th><th>Aksi</th></tr></thead>
        <tbody>
          <tr>
            <td style="color:var(--muted)">LM-010</td>
            <td>Laporan Minggu 10 – Mar 2026</td>
            <td>18–24 Mar 2026</td>
            <td><div class="progress-wrap" style="width:120px;"><div class="progress-bar" style="width:38%;"></div></div><span style="font-size:11px;color:var(--muted);">38%</span></td>
            <td>47</td>
            <td><span class="badge badge-done">Tersimpan</span></td>
            <td><button class="btn btn-outline btn-sm" onclick="openRiwayat('LM-010')">Lihat</button></td>
          </tr>
          <tr>
            <td style="color:var(--muted)">LM-009</td>
            <td>Laporan Minggu 9 – Mar 2026</td>
            <td>11–17 Mar 2026</td>
            <td><div class="progress-wrap" style="width:120px;"><div class="progress-bar" style="width:33%;"></div></div><span style="font-size:11px;color:var(--muted);">33%</span></td>
            <td>52</td>
            <td><span class="badge badge-done">Tersimpan</span></td>
            <td><button class="btn btn-outline btn-sm" onclick="openRiwayat('LM-009')">Lihat</button></td>
          </tr>
          <tr>
            <td style="color:var(--muted)">LM-008</td>
            <td>Laporan Minggu 8 – Mar 2026</td>
            <td>4–10 Mar 2026</td>
            <td><div class="progress-wrap" style="width:120px;"><div class="progress-bar" style="width:28%;"></div></div><span style="font-size:11px;color:var(--muted);">28%</span></td>
            <td>44</td>
            <td><span class="badge badge-review">Evaluasi TL</span></td>
            <td><button class="btn btn-outline btn-sm" onclick="openRiwayat('LM-008')">Lihat</button></td>
          </tr>
          <tr>
            <td style="color:var(--muted)">LM-007</td>
            <td>Laporan Minggu 7 – Feb 2026</td>
            <td>25 Feb – 3 Mar 2026</td>
            <td><div class="progress-wrap" style="width:120px;"><div class="progress-bar" style="width:22%;"></div></div><span style="font-size:11px;color:var(--muted);">22%</span></td>
            <td>39</td>
            <td><span class="badge badge-done">Disetujui TL</span></td>
            <td><button class="btn btn-outline btn-sm" onclick="openRiwayat('LM-007')">Lihat</button></td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

  <!-- ══ PAGE: PROFIL ══ -->
  <div class="page" id="page-profil">
    <div class="panel" style="max-width:540px;">
      <div class="panel-title">Informasi Akun</div>
      <div style="display:flex;align-items:center;gap:16px;margin-bottom:20px;">
        <div class="avatar" style="width:60px;height:60px;font-size:20px;">KP</div>
        <div>
          <div style="font-family:'Syne',sans-serif;font-size:18px;font-weight:700;">Koordinator Pengawas</div>
          <div style="font-size:13px;color:var(--muted);margin-top:3px;">Sistem Pengawasan Proyek</div>
          <span class="badge badge-done" style="margin-top:6px;">Role Aktif</span>
        </div>
      </div>
      <div class="form-row full"><div class="form-group"><label>Username</label><input type="text" value="koordinator" readonly style="opacity:.6;"></div></div>
      <div class="form-row full"><div class="form-group"><label>Role</label><input type="text" value="Koordinator Pengawas" readonly style="opacity:.6;"></div></div>
      <div class="form-row full"><div class="form-group"><label>Hak Akses</label><input type="text" value="Tinjau Laporan Harian · Susun Laporan Mingguan · Simpan Laporan Mingguan" readonly style="opacity:.6;font-size:12px;"></div></div>
      <div style="margin-top:8px;padding-top:16px;border-top:1px solid var(--border);font-size:12px;color:var(--muted);">
        Sesuai FR-008, FR-009, FR-010 — Koordinator Pengawas bertugas mengesahkan laporan harian dan menyusun laporan mingguan.
      </div>
    </div>
  </div>

</main><!-- end main -->

<!-- ══ MODAL DETAIL ══ -->
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

<!-- ══ PRATINJAU MODAL ══ -->
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
// ── Date ──
const d = new Date();
document.getElementById('date-chip').textContent = d.toLocaleDateString('id-ID',{weekday:'long',day:'numeric',month:'long',year:'numeric'});

// ── Page Navigation ──
const pageTitles = {
  dashboard: 'Dashboard',
  tinjau: 'Tinjau Laporan Harian',
  susun: 'Susun Laporan Mingguan',
  riwayat: 'Riwayat Laporan',
  sprint: 'Sprint Backlog',
  profil: 'Profil & Pengaturan'
};
function showPage(id, btn){
  document.querySelectorAll('.page').forEach(p=>p.classList.remove('active'));
  document.getElementById('page-'+id).classList.add('active');
  document.querySelectorAll('.nav-item').forEach(n=>n.classList.remove('active'));
  if(btn) btn.classList.add('active');
  document.getElementById('page-title').textContent = pageTitles[id]||id;
}

// ── Modal ──
function openModal(id, tgl, pengawas, deskripsi, status){
  document.getElementById('modal-title').textContent = 'Detail Laporan ' + id;
  document.getElementById('modal-body').innerHTML = `
    <div class="detail-row"><span class="key">ID Laporan</span><span>${id}</span></div>
    <div class="detail-row"><span class="key">Tanggal</span><span>${tgl}</span></div>
    <div class="detail-row"><span class="key">Pengawas Lapangan</span><span>${pengawas}</span></div>
    <div class="detail-row"><span class="key">Deskripsi Pekerjaan</span><span style="text-align:right;max-width:280px;">${deskripsi}</span></div>
    <div class="detail-row"><span class="key">Status</span><span class="badge ${status==='Tervalidasi'?'badge-done':'badge-wait'}">${status}</span></div>
    <div class="detail-row"><span class="key">Catatan Koordinator</span>
      <textarea placeholder="Tambahkan catatan jika diperlukan..." style="background:var(--surface2);border:1px solid var(--border);border-radius:6px;padding:8px;color:var(--text);font-size:12px;width:200px;resize:vertical;outline:none;font-family:inherit;"></textarea>
    </div>`;
  const act = document.getElementById('modal-actions');
  if(status === 'Menunggu'){
    act.innerHTML = `
      <button class="btn btn-outline" onclick="closeModal()">Tutup</button>
      <button class="btn btn-outline btn-sm" style="color:#ff4d4d;border-color:rgba(255,77,77,.3);" onclick="closeModal();showToast('Laporan ${id} dikembalikan ke Pengawas','')">Kembalikan</button>
      <button class="btn btn-gold" onclick="closeModal();sahkanById('${id}')">Sahkan Laporan</button>`;
  } else {
    act.innerHTML = `<button class="btn btn-outline" onclick="closeModal()">Tutup</button>`;
  }
  document.getElementById('modal').classList.add('open');
}
function closeModal(){ document.getElementById('modal').classList.remove('open'); }

function sahkan(id, btn){
  const row = btn.closest('tr');
  row.querySelector('.badge').className = 'badge badge-done';
  row.querySelector('.badge').textContent = 'Tervalidasi';
  const aksiCell = row.querySelector('td:last-child');
  aksiCell.innerHTML = `<button class="btn btn-outline btn-sm" onclick="openModal('${id}','','','','Tervalidasi')">Detail</button>`;
  showToast('Laporan '+id+' berhasil disahkan ✓','success');
}
function sahkanById(id){
  showToast('Laporan '+id+' berhasil disahkan ✓','success');
}

// ── Filter ──
function filterStatus(val){
  document.querySelectorAll('#tinjau-tbody tr').forEach(r=>{
    r.style.display = (!val || r.dataset.status===val) ? '' : 'none';
  });
}

// ── Pratinjau ──
function pratinjau(){
  const judul = document.getElementById('judul-laporan').value||'(Belum diisi)';
  const periode = document.getElementById('periode-laporan').value;
  const ringkasan = document.getElementById('ringkasan').value||'-';
  const temuan = document.getElementById('temuan').value||'-';
  const pencapaian = document.getElementById('pencapaian').value||'-';
  const kendala = document.getElementById('kendala').value||'-';
  const pct = document.getElementById('progress-pct').value;
  const tk = document.getElementById('total-tk').value;

  document.getElementById('pratinjau-body').innerHTML = `
    <div style="border:1px solid var(--border-gold);border-radius:10px;padding:16px;margin-bottom:14px;background:var(--surface2);">
      <div style="font-family:'Syne',sans-serif;font-size:16px;font-weight:700;margin-bottom:4px;">${judul}</div>
      <div style="font-size:12px;color:var(--muted);">Periode: ${periode} &nbsp;|&nbsp; Progress: ${pct}% &nbsp;|&nbsp; TK: ${tk} orang</div>
    </div>
    <div class="detail-row"><span class="key">Ringkasan Progres</span><span style="text-align:right;max-width:300px;font-size:12px;">${ringkasan}</span></div>
    <div class="detail-row"><span class="key">Temuan Pengawasan</span><span style="text-align:right;max-width:300px;font-size:12px;">${temuan}</span></div>
    <div class="detail-row"><span class="key">Analisis Pencapaian</span><span style="text-align:right;max-width:300px;font-size:12px;">${pencapaian}</span></div>
    <div class="detail-row"><span class="key">Kendala & Rekomendasi</span><span style="text-align:right;max-width:300px;font-size:12px;">${kendala}</span></div>
    <div class="detail-row"><span class="key">Laporan Harian Terkait</span><span>5 laporan tervalidasi</span></div>
  `;
  document.getElementById('modal-pratinjau').classList.add('open');
}
function closePratinjau(){ document.getElementById('modal-pratinjau').classList.remove('open'); }

// ── Simpan ──
function simpanLaporan(){
  const judul = document.getElementById('judul-laporan').value;
  if(!judul.trim()){showToast('Judul laporan wajib diisi!','');return;}
  const ringkasan = document.getElementById('ringkasan').value;
  if(!ringkasan.trim()){showToast('Ringkasan progres wajib diisi!','');return;}
  showToast('Laporan mingguan berhasil disimpan ✓','success');
  setTimeout(()=>{ showPage('riwayat', document.querySelectorAll('.nav-item')[3]); },800);
}

// ── Riwayat ──
function openRiwayat(id){
  showToast('Membuka detail laporan '+id+'...','');
}

// ── Toast ──
function showToast(msg, type){
  const t = document.getElementById('toast');
  t.textContent = msg;
  t.className = 'toast show' + (type?' '+type:'');
  setTimeout(()=>t.className='toast',2800);
}
</script>
</body>
</html>