
<aside class="sidebar">
  <div class="brand">
    <div class="brand-inner">
      <svg class="logo-svg" width="44" height="44" viewBox="0 0 44 44" fill="none" xmlns="http://www.w3.org/2000/svg">
        <circle cx="22" cy="22" r="21" stroke="#ffc107" stroke-width="0.6" stroke-opacity="0.3"/>
        <rect x="9" y="17" width="20" height="20" rx="1.5" fill="#1f1f1f" stroke="#ffc107" stroke-width="0.8" stroke-opacity="0.5"/>
        <polygon points="19,7 8,17 30,17" fill="#ffc107" opacity="0.85"/>
        <line x1="19" y1="2" x2="19" y2="8" stroke="#ffc107" stroke-width="1.6" stroke-linecap="round"/>
        <circle cx="19" cy="1.5" r="1.5" fill="#ffc107" opacity="0.8"/>
        <rect x="12" y="20" width="4" height="4" rx="0.8" fill="#ffc107" opacity="0.9"/>
        <rect x="18" y="20" width="4" height="4" rx="0.8" fill="#ffc107" opacity="0.4"/>
        <rect x="24" y="20" width="4" height="4" rx="0.8" fill="#ffc107" opacity="0.8"/>
        <rect x="12" y="27" width="4" height="4" rx="0.8" fill="#ffc107" opacity="0.5"/>
        <rect x="18" y="27" width="4" height="4" rx="0.8" fill="#ffc107" opacity="1"/>
        <rect x="24" y="27" width="4" height="4" rx="0.8" fill="#ffc107" opacity="0.6"/>
        <rect x="15" y="32" width="7" height="5" rx="1" fill="#ffc107" opacity="0.65"/>
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
    <a class="nav-item <?= $active_page==='dashboard'?'active':'' ?>" href="koordinator_pengawas.php">
      <svg class="nav-icon" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="1" y="1" width="6" height="6" rx="1.2"/><rect x="9" y="1" width="6" height="6" rx="1.2"/><rect x="1" y="9" width="6" height="6" rx="1.2"/><rect x="9" y="9" width="6" height="6" rx="1.2"/></svg>
      Dashboard
    </a>
    <a class="nav-item <?= $active_page==='tinjau'?'active':'' ?>" href="tinjau_laporan_harian.php">
      <svg class="nav-icon" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M2 4h12M2 8h9M2 12h6"/><circle cx="13" cy="11" r="2.5"/><path d="M15 13l1 1"/></svg>
      Tinjau Laporan Harian
    </a>
    <a class="nav-item <?= $active_page==='susun'?'active':'' ?>" href="susun_laporan_mingguan.php">
      <svg class="nav-icon" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M3 2h10v12H3z"/><path d="M6 5h4M6 8h4M6 11h2"/></svg>
      Susun Laporan Mingguan
    </a>
    <a class="nav-item <?= $active_page==='riwayat'?'active':'' ?>" href="riwayat_laporan_mingguan.php">
      <svg class="nav-icon" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="8" cy="8" r="6"/><path d="M8 5v3.5l2 1.5"/></svg>
      Riwayat Laporan
    </a>
  </nav>

  <div class="sidebar-footer">
    <div class="user-card">
      <div class="avatar">KP</div>
      <div class="user-info">
        <p>Koordinator</p>
        <span>Pengawas Aktif</span>
      </div>
    </div>
    <a href="../logout.php" class="logout-btn">
      <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M11 5l3 3-3 3M7 8h7"/><path d="M7 2H3a1 1 0 00-1 1v10a1 1 0 001 1h4"/></svg>
      Logout
    </a>
  </div>
</aside>