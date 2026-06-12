<?php
if (!isset($active_page)) {
    $active_page = '';
}
?>

<aside class="sidebar">

    <div class="brand">
        <div class="brand-inner">

            <svg class="logo-svg logo-arch"
                 width="38"
                 height="38"
                 viewBox="0 0 120 120"
                 fill="none">

                <rect x="10"
                      y="10"
                      width="100"
                      height="100"
                      stroke="#ffc107"
                      stroke-width="3"/>

                <path d="M35 80 V40 H60"
                      stroke="#ffc107"
                      stroke-width="4"/>

                <path d="M60 40 L75 60 L90 40 V80"
                      stroke="#ffc107"
                      stroke-width="4"/>
            </svg>

            <div class="brand-text">
                <h1>CIPTA<span>MANUNGGAL</span></h1>
                <p>Pengawas Lapangan</p>
            </div>

        </div>
    </div>

    <nav class="nav-section" style="flex:1;overflow-y:auto;">

        <div class="nav-label">
            Menu Utama
        </div>

        <a class="nav-item <?= $active_page=='dashboard' ? 'active' : '' ?>"
           href="pengawas_lapangan.php">

            <svg class="nav-icon"
                 viewBox="0 0 16 16"
                 fill="none"
                 stroke="currentColor"
                 stroke-width="1.5">

                <rect x="1" y="1" width="6" height="6" rx="1.2"/>
                <rect x="9" y="1" width="6" height="6" rx="1.2"/>
                <rect x="1" y="9" width="6" height="6" rx="1.2"/>
                <rect x="9" y="9" width="6" height="6" rx="1.2"/>
            </svg>

            Dashboard
        </a>

        <a class="nav-item <?= $active_page=='verifikasi' ? 'active' : '' ?>"
           href="verifikasi_lapangan.php">

            <svg class="nav-icon"
                 viewBox="0 0 16 16"
                 fill="none"
                 stroke="currentColor"
                 stroke-width="1.5">

                <path d="M2 8l3 3 8-8"/>
            </svg>

            Verifikasi Lapangan
        </a>

        <a class="nav-item <?= $active_page=='laporan' ? 'active' : '' ?>"
           href="laporan_harian.php">

            <svg class="nav-icon"
                 viewBox="0 0 16 16"
                 fill="none"
                 stroke="currentColor"
                 stroke-width="1.5">

                <path d="M3 2h10v12H3z"/>
                <path d="M6 5h4M6 8h4M6 11h2"/>
            </svg>

            Laporan Harian
        </a>

        <a class="nav-item <?= $active_page=='laporan_verifikasi' ? 'active' : '' ?>"
           href="laporan_verifikasi.php">

            <svg class="nav-icon"
                 viewBox="0 0 16 16"
                 fill="none"
                 stroke="currentColor"
                 stroke-width="1.5">

                <circle cx="8" cy="8" r="6"/>
                <path d="M8 5v3.5l2 1.5"/>
            </svg>

            Laporan Verifikasi
        </a>

    </nav>

    <div class="sidebar-footer">
        <div class="user-card">
            <div class="avatar">PL</div>
            <div class="user-info">
                <p>Pengawas Lapangan</p>
                <span>Aktif</span>
            </div>
        </div>
        <a href="../logout.php" class="logout-btn">
            <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M11 5l3 3-3 3M7 8h7"/><path d="M7 2H3a1 1 0 00-1 1v10a1 1 0 001 1h4"/></svg>
            Logout
        </a>
    </div>
</aside>
