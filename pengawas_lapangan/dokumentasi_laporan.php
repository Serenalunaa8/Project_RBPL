<?php
session_start();
require_once "../koneksi.php";

if (!isset($koneksi) || !$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

if (!isset($_GET['id']) || !ctype_digit($_GET['id'])) {
    http_response_code(400);
    die("ID tidak valid.");
}

$laporan_id = (int)$_GET['id'];

/* ================= HAPUS FOTO ================= */
if (isset($_GET['hapus']) && ctype_digit($_GET['hapus'])) {
    $hapus_id = (int)$_GET['hapus'];

    $stmt = mysqli_prepare($koneksi, "SELECT * FROM dokumentasi_lapangan WHERE id = ?");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $hapus_id);
        mysqli_stmt_execute($stmt);
        $f = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        mysqli_stmt_close($stmt);
    } else {
        $f = false;
    }

    if ($f) {
        $filepath = "uploads/" . $f['file_path'];
        if (file_exists($filepath)) @unlink($filepath);

        $del = mysqli_prepare($koneksi, "DELETE FROM dokumentasi_lapangan WHERE id = ?");
        if ($del) {
            mysqli_stmt_bind_param($del, "i", $hapus_id);
            mysqli_stmt_execute($del);
            mysqli_stmt_close($del);
        }
    }

    header("Location: ?id=" . $laporan_id);
    exit;
}

/* ================= UPLOAD ================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');

    // Pastikan folder uploads ada
    $upload_dir = "uploads/";
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    // Cek apakah ada file yang dikirim
    if (!isset($_FILES['file']) || empty($_FILES['file']['name'][0])) {
        echo json_encode(['status' => 'error', 'message' => 'Tidak ada file yang dikirim']);
        exit;
    }

    $allowed = ['jpg', 'jpeg', 'png'];
    $uploaded = [];
    $errors = [];

    $total = count($_FILES['file']['name']);

    for ($i = 0; $i < $total; $i++) {
        // Skip kalau kosong
        if ($_FILES['file']['error'][$i] !== UPLOAD_ERR_OK) {
            $errors[] = "File ke-" . ($i+1) . " error: " . $_FILES['file']['error'][$i];
            continue;
        }

        $name = $_FILES['file']['name'][$i];
        $tmp  = $_FILES['file']['tmp_name'][$i];
        $size = $_FILES['file']['size'][$i];

        // Validasi ekstensi
        $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed)) {
            $errors[] = "$name: format tidak didukung";
            continue;
        }

        // Validasi ukuran (500MB)
        if ($size > 500 * 1024 * 1024) {
            $errors[] = "$name: ukuran melebihi 500MB";
            continue;
        }

        // Validasi benar-benar gambar
        if (!getimagesize($tmp)) {
            $errors[] = "$name: bukan file gambar valid";
            continue;
        }

        // Simpan file
        $new_name = uniqid('dok_', true) . "." . $ext;
        $dest     = $upload_dir . $new_name;

        if (move_uploaded_file($tmp, $dest)) {
            $stmt = mysqli_prepare($koneksi,
                "INSERT INTO dokumentasi_lapangan (laporan_id, file_path) VALUES (?, ?)"
            );
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "is", $laporan_id, $new_name);
                mysqli_stmt_execute($stmt);
                $insert_id = mysqli_insert_id($koneksi);
                mysqli_stmt_close($stmt);

                $uploaded[] = ['id' => $insert_id, 'file' => $new_name];
            } else {
                $errors[] = "$name: gagal menyimpan metadata file ke database";
            }
        } else {
            $errors[] = "$name: gagal dipindahkan ke folder uploads (cek permission folder)";
        }
    }

    echo json_encode([
        'status'   => count($uploaded) > 0 ? 'success' : 'error',
        'files'    => $uploaded,
        'errors'   => $errors,
        'message'  => count($errors) > 0 ? implode(', ', $errors) : null
    ]);
    exit;
}

/* ================= DATA ================= */
$stmt = mysqli_prepare($koneksi, "SELECT * FROM laporan_harian WHERE id = ?");
if (!$stmt) {
    die("Query gagal: " . mysqli_error($koneksi));
}
mysqli_stmt_bind_param($stmt, "i", $laporan_id);
mysqli_stmt_execute($stmt);
$laporan = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

if (!$laporan) die("Laporan tidak ditemukan.");

$stmt2 = mysqli_prepare($koneksi, "SELECT * FROM dokumentasi_lapangan WHERE laporan_id = ? ORDER BY id DESC");
if (!$stmt2) {
    die("Query gagal: " . mysqli_error($koneksi));
}
mysqli_stmt_bind_param($stmt2, "i", $laporan_id);
mysqli_stmt_execute($stmt2);
$fotos_result = mysqli_stmt_get_result($stmt2);
$fotos = mysqli_fetch_all($fotos_result, MYSQLI_ASSOC);
mysqli_stmt_close($stmt2);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Dokumentasi Laporan | CV Cipta Manunggal Konsultan</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
body { font-family: 'Inter', sans-serif; background: #111111; color: #ffffff; }
.dashboard-container { display: flex; min-height: 100vh; align-items: flex-start; }

.sidebar {
    width: 260px; background: #1a1a1a; padding: 30px 20px;
    border-right: 1px solid rgba(255,255,255,0.05);
    position: sticky; top: 0; height: 100vh; overflow-y: auto;
}
.sidebar-brand { display: flex; align-items: center; gap: 10px; margin-bottom: 40px; }
.logo-arch { width: 38px; height: 38px; stroke: #ffc107; stroke-width: 4; fill: none; }
.sidebar h2 { font-size: 16px; }
.sidebar span { color: #ffc107; }
.sidebar nav { display: flex; flex-direction: column; gap: 15px; }
.sidebar {
    width: 260px;
    background: #101010;
    padding: 28px 0;
    border-right: 1px solid rgba(255,255,255,0.08);
    position: fixed;
    top: 0;
    left: 0;
    height: 100vh;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    z-index: 10;
    animation: slideLeft .35s ease both;
    box-shadow: 2px 0 24px rgba(0, 0, 0, 0.25);
}

.brand { padding: 0 22px 28px; border-bottom: 1px solid rgba(255,255,255,0.05); }
.brand-inner { display: flex; align-items: center; gap: 12px; }
.logo-svg { width: 38px; height: 38px; flex-shrink: 0; }
.brand-text h1 { font-family: 'Inter', sans-serif; font-size: 14px; font-weight: 700; letter-spacing: 0.5px; line-height: 1.3; }
.brand-text h1 span { color: #ffc107; }
.brand-text p { font-size: 10px; color: #888; letter-spacing: 2px; text-transform: uppercase; margin-top: 2px; }

.nav-section { padding: 20px 14px 0; display: flex; flex-direction: column; gap: 8px; }
.nav-label { font-size: 10px; color: #888; letter-spacing: 2px; text-transform: uppercase; padding: 0 8px; margin-bottom: 8px; }

.nav-item { display: flex; align-items: center; gap: 10px; padding: 10px 12px; border-radius: 8px; color: #ccc; font-size: 13.5px; font-weight: 400; text-decoration: none; transition: all 0.25s; position: relative; width: 100%; }
.nav-item:hover { background: rgba(255,193,7,0.07); color: #ddd; }
.nav-item.active { background: rgba(255,193,7,0.12); color: #ffc107; font-weight: 500; }
.nav-item.active::before { content: ""; position: absolute; left: 0; top: 20%; bottom: 20%; width: 3px; background: #ffc107; border-radius: 0 3px 3px 0; }
.nav-icon { width: 16px; height: 16px; opacity: 0.7; flex-shrink: 0; }
.nav-item.active .nav-icon { opacity: 1; }

.sidebar-footer { margin-top: auto; padding: 18px 18px; border-top: 1px solid rgba(255,255,255,0.08); display: flex; flex-direction: column; gap: 14px; align-items: flex-start; border-radius: 10px; background: #111; }
.user-card { display: flex; align-items: center; gap: 12px; width: 100%; }
.avatar { width: 34px; height: 34px; border-radius: 50%; background: #ffc107; display: flex; align-items: center; justify-content: center; font-family: 'Inter', sans-serif; font-size: 13px; font-weight: 700; color: #111; flex-shrink: 0; }
.user-info p { font-size: 13px; font-weight: 500; margin-bottom: 2px; }
.user-info span { font-size: 11px; color: #888; }
.logout-btn { display: inline-flex; align-items: center; justify-content: center; gap: 8px; width: 100%; padding: 10px 14px; background: #111; border: 1px solid rgba(255,255,255,0.08); border-radius: 10px; color: #ccc; font-size: 13px; text-decoration: none; transition: 0.2s; }
.logout-btn:hover { border-color: #ff4d4d; color: #ff4d4d; }

.main-content {
    flex: 1;
    margin-left: 260px;
    width: min(1080px, calc(100vw - 320px));
    max-width: 1080px;
    margin-right: auto;
    padding: 50px 40px;
    overflow-y: auto;
    animation: slideRight .35s ease both;
}
.nav-icon { width: 18px; height: 18px; margin-right: 8px; display: inline-block; }

.sidebar-footer { margin-top: 30px; }
.user-card { display: flex; gap: 8px; align-items: center; margin-bottom: 8px; }
.avatar { width: 36px; height: 36px; background: #2a2a2a; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #fff; font-weight: 700; }
.logout-btn { display: inline-flex; align-items: center; gap: 8px; padding: 8px 10px; background: #2a2a2a; color: #fff; border-radius: 8px; text-decoration: none; }

.main-content { flex: 1; padding: 50px; overflow-y: auto; }
.topbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 40px; }
.topbar h1 { font-size: 28px; font-weight: 700; }
.topbar p { font-size: 14px; color: #888; margin-top: 4px; }
.role-badge { background: #ffc107; color: #111; padding: 8px 16px; border-radius: 20px; font-size: 12px; font-weight: 600; }

.card { background: #1c1c1c; border: 1px solid rgba(255,255,255,0.05); border-radius: 12px; padding: 32px; margin-bottom: 28px; }

.section-title {
    font-size: 15px; font-weight: 600; margin-bottom: 20px;
    display: flex; align-items: center; gap: 10px;
    padding-bottom: 12px; border-bottom: 1px solid rgba(255,255,255,0.05);
}
.section-title::before {
    content: ''; display: inline-block; width: 3px; height: 15px;
    background: #ffc107; border-radius: 2px;
}

.info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
.info-item { background: #111; padding: 12px 16px; border-radius: 8px; }
.info-item label { display: block; font-size: 11px; color: #888; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px; }
.info-item span { font-size: 14px; color: #fff; font-weight: 500; }

/* DROP AREA */
.drop-area {
    border: 2px dashed rgba(255,193,7,0.3); border-radius: 8px; padding: 40px;
    text-align: center; cursor: pointer; transition: 0.3s;
    background: rgba(255,193,7,0.03);
}
.drop-area:hover, .drop-area.dragover { border-color: #ffc107; background: rgba(255,193,7,0.08); }
.drop-area .icon { font-size: 36px; margin-bottom: 12px; }
.drop-area p { font-size: 14px; color: #ccc; margin-bottom: 4px; }
.drop-area small { font-size: 12px; color: #666; }

/* PROGRESS */
.progress { margin-top: 16px; height: 4px; background: #333; border-radius: 2px; display: none; }
.progress-bar { height: 100%; background: #ffc107; border-radius: 2px; width: 0%; transition: width 0.3s; }

/* ALERT */
.alert { padding: 12px 16px; border-radius: 8px; margin-bottom: 16px; font-size: 13px; display: flex; align-items: center; gap: 8px; }
.alert-success { background: rgba(34,197,94,0.1); color: #22c55e; border: 1px solid rgba(34,197,94,0.2); }
.alert-error   { background: rgba(239,68,68,0.1);  color: #ef4444;  border: 1px solid rgba(239,68,68,0.2); }

/* GALLERY */
.gallery { display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 16px; }
.gallery-item { position: relative; border-radius: 8px; overflow: hidden; background: #0f0f0f; aspect-ratio: 1; }
.gallery-item img { width: 100%; height: 100%; object-fit: cover; cursor: pointer; transition: 0.3s; display: block; }
.gallery-item img:hover { transform: scale(1.05); }
.delete-btn {
    position: absolute; top: 8px; right: 8px;
    background: rgba(239,68,68,0.85); color: #fff; border: none;
    border-radius: 50%; width: 24px; height: 24px; cursor: pointer;
    font-size: 11px; display: flex; align-items: center; justify-content: center;
    transition: 0.3s; opacity: 0;
}
.gallery-item:hover .delete-btn { opacity: 1; }

.gallery-empty { text-align: center; padding: 40px; color: #555; font-size: 14px; }

.foto-count { font-size: 13px; color: #888; font-weight: normal; margin-left: 8px; }

@media (max-width: 768px) {
    .dashboard-container { flex-direction: column; }
    .sidebar { width: 100%; height: auto; position: relative; border-right: none; border-bottom: 1px solid rgba(255,255,255,0.05); padding: 20px; }
    .sidebar nav { flex-direction: row; gap: 8px; flex-wrap: wrap; }
    .sidebar nav a { flex: 1; min-width: 80px; text-align: center; }
    .main-content { padding: 20px; }
    .topbar { flex-direction: column; gap: 16px; align-items: flex-start; }
    .info-grid { grid-template-columns: 1fr; }
    .card { padding: 20px; }
}

@keyframes slideLeft {
    from { opacity: 0; transform: translateX(-24px); }
    to { opacity: 1; transform: translateX(0); }
}

@keyframes slideRight {
    from { opacity: 0; transform: translateX(24px); }
    to { opacity: 1; transform: translateX(0); }
}
</style>
</head>

<body>
<div class="dashboard-container">

    <?php
    // use shared sidebar component and mark the active menu
    $active_page = 'laporan';
    include 'sidebar.php';
    ?>

    <main class="main-content">
        <header class="topbar">
            <div>
                <h1>Dokumentasi Laporan</h1>
                <p>Upload dan kelola foto dokumentasi laporan harian.</p>
            </div>
            <div class="role-badge">PENGAWAS</div>
        </header>

        <div id="alertContainer"></div>

        <!-- INFO LAPORAN -->
        <div class="card">
            <h2 class="section-title">Informasi Laporan</h2>
            <div class="info-grid">
                <div class="info-item">
                    <label>Tanggal</label>
                    <span><?= date('d M Y', strtotime($laporan['tanggal'])) ?></span>
                </div>
                <div class="info-item">
                    <label>Progress</label>
                    <span><?= htmlspecialchars($laporan['progres']) ?>%</span>
                </div>
                <div class="info-item">
                    <label>Cuaca</label>
                    <span><?= htmlspecialchars($laporan['cuaca']) ?></span>
                </div>
                <div class="info-item">
                    <label>Catatan</label>
                    <span><?= htmlspecialchars($laporan['catatan'] ?: 'Tidak ada') ?></span>
                </div>
            </div>
        </div>

        <!-- UPLOAD -->
        <div class="card">
            <h2 class="section-title">Upload Dokumentasi</h2>
            <div class="drop-area" id="dropArea">
                <div class="icon">📸</div>
                <p>Klik atau seret foto ke sini</p>
                <small>Format JPG, PNG — Maksimal 500MB per foto</small>
                <input type="file" id="fileInput" name="file[]" multiple accept="image/jpeg,image/png" style="display:none">
            </div>
            <div class="progress" id="progressWrap">
                <div class="progress-bar" id="progressBar"></div>
            </div>
        </div>

        <!-- GALLERY -->
        <div class="card">
            <h2 class="section-title">
                Galeri Dokumentasi
                <span class="foto-count" id="fotoCount"><?= count($fotos) ?> foto</span>
            </h2>
            <div class="gallery" id="gallery">
                <?php if (count($fotos) > 0): ?>
                    <?php foreach ($fotos as $f): ?>
                        <div class="gallery-item" id="foto-<?= $f['id'] ?>">
                            <img src="uploads/<?= htmlspecialchars($f['file_path']) ?>"
                                 onclick="window.open(this.src)"
                                 alt="Dokumentasi">
                            <button class="delete-btn" onclick="hapusFoto(<?= $f['id'] ?>)" title="Hapus foto">✕</button>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="gallery-empty" id="emptyMsg">Belum ada foto dokumentasi.</p>
                <?php endif; ?>
            </div>
        </div>

    </main>
</div>

<script>
const laporan_id = <?= $laporan_id ?>;
const dropArea    = document.getElementById('dropArea');
const fileInput   = document.getElementById('fileInput');
const progressWrap = document.getElementById('progressWrap');
const progressBar  = document.getElementById('progressBar');
const gallery      = document.getElementById('gallery');
const fotoCount    = document.getElementById('fotoCount');
let currentCount   = <?= count($fotos) ?>;

/* ── CLICK TO BROWSE ── */
dropArea.addEventListener('click', () => fileInput.click());

/* ── DRAG & DROP ── */
dropArea.addEventListener('dragover', e => {
    e.preventDefault();
    dropArea.classList.add('dragover');
});
dropArea.addEventListener('dragleave', () => dropArea.classList.remove('dragover'));
dropArea.addEventListener('drop', e => {
    e.preventDefault();
    dropArea.classList.remove('dragover');
    uploadFiles(Array.from(e.dataTransfer.files));
});

fileInput.addEventListener('change', e => {
    uploadFiles(Array.from(e.target.files));
    fileInput.value = ''; // reset agar file sama bisa diupload lagi
});

/* ── UPLOAD ── */
function uploadFiles(files) {
    if (!files.length) return;

    const formData = new FormData();
    files.forEach(f => formData.append('file[]', f));

    progressWrap.style.display = 'block';
    progressBar.style.width = '30%';

    fetch('?id=' + laporan_id, {
        method: 'POST',
        body: formData
    })
    .then(res => {
        if (!res.ok) throw new Error('HTTP ' + res.status);
        return res.json();
    })
    .then(data => {
        progressBar.style.width = '100%';
        setTimeout(() => { progressWrap.style.display = 'none'; progressBar.style.width = '0%'; }, 600);

        if (data.status === 'success' && data.files.length > 0) {
            showAlert('✅ ' + data.files.length + ' foto berhasil diupload!', 'success');

            // Hapus pesan kosong kalau ada
            const emptyMsg = document.getElementById('emptyMsg');
            if (emptyMsg) emptyMsg.remove();

            data.files.forEach(item => {
                const div = document.createElement('div');
                div.className = 'gallery-item';
                div.id = 'foto-' + item.id;
                div.innerHTML = `
                    <img src="uploads/${item.file}" onclick="window.open(this.src)" alt="Dokumentasi">
                    <button class="delete-btn" onclick="hapusFoto(${item.id})" title="Hapus foto">✕</button>
                `;
                gallery.appendChild(div);
            });

            currentCount += data.files.length;
            fotoCount.textContent = currentCount + ' foto';
        }

        // Tampilkan error per file kalau ada
        if (data.errors && data.errors.length > 0) {
            showAlert('⚠️ ' + data.errors.join(' | '), 'error');
        }
    })
    .catch(err => {
        progressWrap.style.display = 'none';
        showAlert('❌ Gagal upload: ' + err.message, 'error');
        console.error(err);
    });
}

/* ── HAPUS FOTO ── */
function hapusFoto(id) {
    if (!confirm('Yakin hapus foto ini?')) return;
    window.location = '?id=' + laporan_id + '&hapus=' + id;
}

/* ── ALERT ── */
function showAlert(msg, type) {
    const container = document.getElementById('alertContainer');
    const el = document.createElement('div');
    el.className = 'alert alert-' + type;
    el.textContent = msg;
    container.prepend(el);
    setTimeout(() => el.remove(), 5000);
}
</script>
</body>
</html>