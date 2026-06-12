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
                <p>Kontraktor</p>
            </div>

        </div>
    </div>

    <nav class="nav-section" style="flex:1;overflow-y:auto;">

        <div class="nav-label">
            Menu Utama
        </div>

        <a class="nav-item <?= $active_page=='dashboard' ? 'active' : '' ?>"
           href="dashboard.php">

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

        <a class="nav-item <?= $active_page=='ajukan' ? 'active' : '' ?>"
           href="AjukanIzin.php">

            <svg class="nav-icon"
                 viewBox="0 0 16 16"
                 fill="none"
                 stroke="currentColor"
                 stroke-width="1.5">

                <path d="M8 3v10"/>
                <path d="M3 8h10"/>
            </svg>

            Ajukan Izin
        </a>

        <a class="nav-item <?= $active_page=='status' ? 'active' : '' ?>"
           href="LihatStatus.php">

            <svg class="nav-icon"
                 viewBox="0 0 16 16"
                 fill="none"
                 stroke="currentColor"
                 stroke-width="1.5">

                <circle cx="8" cy="8" r="6"/>
                <path d="M8 5v3.5l2 1.5"/>
            </svg>

            Status Izin

            <?php if(isset($revisi) && $revisi > 0): ?>
                <span class="nav-badge">
                    <?= $revisi ?>
                </span>
            <?php endif; ?>
        </a>
    </nav>

    <div class="sidebar-footer">

        <div class="user-card">
            <div class="avatar">
                <?= strtoupper(substr($_SESSION['username'],0,1)) ?>
            </div>

            <div class="user-info">
                <p><?= htmlspecialchars($_SESSION['username']) ?></p>
                <span>Kontraktor Aktif</span>
            </div>
        </div>

        <a href="../logout.php"
           class="logout-btn">

            <svg width="14"
                 height="14"
                 viewBox="0 0 16 16"
                 fill="none"
                 stroke="currentColor"
                 stroke-width="1.5">

                <path d="M11 5l3 3-3 3"/>
                <path d="M7 8h7"/>
                <path d="M7 2H3a1 1 0 00-1 1v10a1 1 0 001 1h4"/>
            </svg>

            Logout
        </a>

    </div>

</aside>