<?php
session_start();
include "../koneksi.php";

if (!isset($_GET['id'])) {
    die("ID laporan tidak ditemukan");
}

$laporan_id = intval($_GET['id']);

/* ================= HAPUS FOTO ================= */
if(isset($_GET['hapus'])){
    $id = intval($_GET['hapus']);

    $f = mysqli_fetch_assoc(mysqli_query($koneksi,"SELECT * FROM dokumentasi_lapangan WHERE id='$id'"));
    if($f){
        @unlink("../uploads/".$f['file_path']);
        mysqli_query($koneksi,"DELETE FROM dokumentasi_lapangan WHERE id='$id'");
    }
    header("Location: ?laporan_id=".$laporan_id);
    exit;
}

/* ================= UPLOAD ================= */
if(isset($_FILES['file'])){
    $allowed = ['jpg','jpeg','png'];

    foreach($_FILES['file']['name'] as $i => $name){

        $tmp = $_FILES['file']['tmp_name'][$i];
        $size = $_FILES['file']['size'][$i];

        if(!$tmp) continue;

        $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
        if(!in_array($ext,$allowed)) continue;
        if($size > 2*1024*1024) continue;
        if(!getimagesize($tmp)) continue;

        $new = uniqid().".".$ext;
        move_uploaded_file($tmp,"../uploads/".$new);

        mysqli_query($koneksi,"
        INSERT INTO dokumentasi_lapangan(laporan_id,file_path)
        VALUES('$laporan_id','$new')
        ");
    }

    echo "success";
    exit;
}

/* ================= DATA ================= */
$laporan = mysqli_fetch_assoc(mysqli_query($koneksi,"
SELECT * FROM laporan_harian WHERE id='$laporan_id'
"));

$fotos = mysqli_query($koneksi,"
SELECT * FROM dokumentasi_lapangan WHERE laporan_id='$laporan_id'
ORDER BY id DESC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Dokumentasi Laporan | CV Cipta Manunggal Konsultan</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
* { margin: 0; padding: 0; box-sizing: border-box; }

body {
    font-family: 'Inter', sans-serif;
    background: #111111;
    color: #ffffff;
}

.dashboard-container {
    display: flex;
    min-height: 100vh;
}

/* ── SIDEBAR ── */
.sidebar {
    width: 260px;
    background: #1a1a1a;
    padding: 30px 20px;
    border-right: 1px solid rgba(255,255,255,0.05);
    position: sticky;
    top: 0;
    height: 100vh;
    overflow-y: auto;
}

.sidebar-brand {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 40px;
}

.logo-arch {
    width: 38px;
    height: 38px;
    stroke: #ffc107;
    stroke-width: 4;
    fill: none;
}

.sidebar h2 { font-size: 16px; }
.sidebar span { color: #ffc107; }

.sidebar nav {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.sidebar nav a {
    text-decoration: none;
    color: #cccccc;
    padding: 10px;
    border-radius: 6px;
    transition: 0.3s;
    font-size: 14px;
}

.sidebar nav a:hover,
.sidebar nav a.active {
    background: #ffc107;
    color: #111;
}

.logout {
    margin-top: 30px;
    background: #2a2a2a;
}

/* ── MAIN ── */
.main-content {
    flex: 1;
    padding: 50px;
    overflow-y: auto;
}

/* ── TOPBAR ── */
.topbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 40px;
}

.topbar h1 {
    font-size: 28px;
    font-weight: 700;
}

.topbar p {
    font-size: 14px;
    color: #888;
    margin-top: 4px;
}

.role-badge {
    background: #ffc107;
    color: #111;
    padding: 8px 16px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
}

/* ── CARD ── */
.card {
    background: #1c1c1c;
    border: 1px solid rgba(255,255,255,0.05);
    border-radius: 12px;
    padding: 32px;
    margin-bottom: 28px;
}

.section-title {
    font-size: 15px;
    font-weight: 600;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
    padding-bottom: 12px;
    border-bottom: 1px solid rgba(255,255,255,0.05);
}

.section-title::before {
    content: '';
    display: inline-block;
    width: 3px;
    height: 15px;
    background: #ffc107;
    border-radius: 2px;
}

/* ── FORM ── */
.form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 24px;
}

.form-field {
    display: flex;
    flex-direction: column;
}

.form-field label {
    font-size: 12px;
    color: #888;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 6px;
}

.form-field input,
.form-field textarea,
.form-field select {
    background: #0f0f0f;
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 8px;
    padding: 12px 14px;
    color: #fff;
    font-family: 'Inter', sans-serif;
    font-size: 13px;
    transition: 0.3s;
}

.form-field input:focus,
.form-field textarea:focus,
.form-field select:focus {
    outline: none;
    border-color: #ffc107;
    box-shadow: 0 0 8px rgba(255, 193, 7, 0.2);
}

.form-field textarea {
    min-height: 80px;
    resize: vertical;
}

.full-width {
    grid-column: span 2;
}

.button-group {
    display: flex;
    gap: 12px;
    margin-top: 24px;
}

.btn-submit {
    background: #22c55e;
    color: #fff;
    padding: 12px 24px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 600;
    font-size: 13px;
    transition: 0.3s;
    font-family: 'Inter', sans-serif;
}

.btn-submit:hover {
    background: #16a34a;
    transform: translateY(-2px);
}

/* ── TABLE ── */
.table-container {
    overflow-x: auto;
}

table {
    width: 100%;
    border-collapse: collapse;
}

th, td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid rgba(255,255,255,0.05);
}

th {
    color: #888;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

td {
    color: #e0e0e0;
}

/* ── ALERT ── */
.alert {
    padding: 12px 16px;
    border-radius: 8px;
    margin-bottom: 20px;
    font-size: 14px;
}

.alert-error {
    background: rgba(239, 68, 68, 0.1);
    color: #ef4444;
}

.alert-success {
    background: rgba(34, 197, 94, 0.1);
    color: #22c55e;
}

/* ── RESPONSIVE ── */
@media (max-width: 1024px) {
    .sidebar { width: 220px; }
    .main-content { padding: 30px; }
    .form-grid { grid-template-columns: 1fr; }
    .full-width { grid-column: span 1; }
}

@media (max-width: 768px) {
    .dashboard-container { flex-direction: column; }
    .sidebar {
        width: 100%;
        height: auto;
        position: relative;
        border-right: none;
        border-bottom: 1px solid rgba(255,255,255,0.05);
        padding: 20px;
    }
    .sidebar nav {
        flex-direction: row;
        gap: 10px;
        flex-wrap: wrap;
    }
    .sidebar nav a {
        flex: 1;
        min-width: 80px;
        text-align: center;
    }
    .main-content { padding: 20px; }
    .topbar {
        flex-direction: column;
        gap: 16px;
        align-items: flex-start;
    }
    .card { padding: 20px; }
}
</style>
</head>

<body>
<div class="dashboard-container">

    <!-- SIDEBAR -->
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
            <a href="pengawas_lapangan.php">Dashboard</a>
            <a href="verifikasi_lapangan.php">Verifikasi</a>
            <a href="laporan_harian.php" class="active">Laporan Harian</a>
            <a href="../logout.php" class="logout">Logout</a>
        </nav>
    </aside>

    <!-- MAIN -->
    <main class="main-content">

        <!-- TOPBAR -->
        <header class="topbar">
            <div>
                <h1>Dokumentasi Laporan</h1>
                <p>Upload dan kelola foto dokumentasi laporan harian.</p>
            </div>
            <div class="role-badge">PENGAWAS</div>
        </header>

        <!-- INFO LAPORAN -->
        <div class="card">
            <h2 class="section-title">Informasi Laporan</h2>
            <p><strong>Tanggal:</strong> <?= date('d M Y', strtotime($laporan['tanggal'])) ?></p>
            <p><strong>Progress:</strong> <?= $laporan['progres'] ?>%</p>
            <p><strong>Cuaca:</strong> <?= htmlspecialchars($laporan['cuaca']) ?></p>
            <p><strong>Catatan:</strong> <?= htmlspecialchars($laporan['catatan'] ?: 'Tidak ada') ?></p>
        </div>

        <!-- UPLOAD AREA -->
        <div class="card">
            <h2 class="section-title">Upload Dokumentasi</h2>
            <form id="uploadForm" enctype="multipart/form-data">
                <div class="drop-area" id="dropArea">
                    <p>📸 Klik atau drag foto di sini</p>
                    <p style="font-size: 12px; color: #888;">Maksimal 2MB per foto (JPG, PNG)</p>
                    <input type="file" id="fileInput" name="file[]" multiple accept="image/*" style="display: none;">
                </div>
                <div class="progress" id="progressContainer" style="display: none;">
                    <div class="progress-bar" id="progressBar"></div>
                </div>
            </form>
        </div>

        <!-- GALLERY -->
        <div class="card">
            <h2 class="section-title">Galeri Dokumentasi (<?= mysqli_num_rows($fotos) ?> foto)</h2>
            <div class="gallery" id="gallery">
                <?php while($f = mysqli_fetch_assoc($fotos)): ?>
                    <div class="gallery-item">
                        <img src="../uploads/<?= $f['file_path'] ?>" onclick="window.open(this.src)">
                        <button class="delete-btn" onclick="hapusFoto(<?= $f['id'] ?>)">✕</button>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>

    </main>
</div>

<script>
function hapusFoto(id) {
    if(confirm('Yakin hapus foto ini?')) {
        window.location = '?id=<?= $laporan_id ?>&hapus=' + id;
    }
}

// DRAG & DROP
const dropArea = document.getElementById('dropArea');
const fileInput = document.getElementById('fileInput');
const progressContainer = document.getElementById('progressContainer');
const progressBar = document.getElementById('progressBar');
const gallery = document.getElementById('gallery');

dropArea.addEventListener('click', () => fileInput.click());

dropArea.addEventListener('dragover', (e) => {
    e.preventDefault();
    dropArea.style.background = 'rgba(255, 193, 7, 0.1)';
});

dropArea.addEventListener('dragleave', () => {
    dropArea.style.background = 'rgba(255, 193, 7, 0.05)';
});

dropArea.addEventListener('drop', (e) => {
    e.preventDefault();
    dropArea.style.background = 'rgba(255, 193, 7, 0.05)';
    const files = Array.from(e.dataTransfer.files);
    uploadFiles(files);
});

fileInput.addEventListener('change', (e) => {
    const files = Array.from(e.target.files);
    uploadFiles(files);
});

function uploadFiles(files) {
    const formData = new FormData();
    files.forEach(file => formData.append('file[]', file));

    progressContainer.style.display = 'block';
    progressBar.style.width = '0%';

    fetch('', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(result => {
        if(result === 'success') {
            progressBar.style.width = '100%';
            setTimeout(() => location.reload(), 500);
        }
    })
    .catch(error => console.error('Error:', error));
}
</script>

</body>
</html>