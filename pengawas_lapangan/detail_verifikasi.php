<?php
session_start();
include "../koneksi.php";

/* ================== CEK ROLE ================== */
if (!isset($_SESSION['role']) || $_SESSION['role'] != "pengawas") {
    header("Location: ../login.php");
    exit;
}

/* ================== AMBIL ID ================== */
if (!isset($_GET['id'])) {
    die("ID tidak ditemukan");
}

$id = $_GET['id'];

/* ================== AMBIL DATA ================== */
$query = mysqli_query($koneksi, "
    SELECT f.*, u.username 
    FROM form_izin_pekerjaan f
    JOIN users u ON f.id = u.id
    WHERE f.id='$id'
");

$data = mysqli_fetch_assoc($query);

if (!$data) {
    die("Data tidak ditemukan");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Detail Izin</title>

<style>
body {
    font-family: 'Segoe UI', sans-serif;
    background: #111;
    color: #fff;
    margin: 0;
}

.container {
    padding: 40px;
}

/* HEADER */
.header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
}

.btn-back {
    background: #2a2a2a;
    color: #fff;
    padding: 8px 14px;
    border-radius: 6px;
    text-decoration: none;
}

.btn-back:hover {
    background: #ffc107;
    color: #000;
}

/* CARD */
.card {
    background: #1c1c1c;
    padding: 25px;
    border-radius: 12px;
    border: 1px solid rgba(255,255,255,0.05);
}

/* GRID */
.grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

.field {
    margin-bottom: 15px;
}

.field label {
    display: block;
    font-size: 12px;
    color: #888;
    margin-bottom: 5px;
}

.field div {
    font-size: 14px;
}

/* FULL WIDTH */
.full {
    grid-column: span 2;
}

/* BUTTON */
.actions {
    margin-top: 30px;
    display: flex;
    gap: 15px;
}

.btn-approve {
    background: #22c55e;
    border: none;
    padding: 10px 18px;
    border-radius: 6px;
    color: #fff;
    cursor: pointer;
}

.btn-reject {
    background: #ef4444;
    border: none;
    padding: 10px 18px;
    border-radius: 6px;
    color: #fff;
    cursor: pointer;
}

textarea {
    width: 100%;
    height: 80px;
    border-radius: 6px;
    border: none;
    padding: 10px;
    background: #2a2a2a;
    color: #fff;
}
</style>

</head>
<body>

<div class="container">

    <div class="header">
        <h2>📄 Detail Izin Pekerjaan</h2>
        <a href="verifikasi_Lapangan.php" class="btn-back">← Kembali</a>
    </div>

    <div class="card">

        <div class="grid">

            <div class="field">
                <label>Jenis Pekerjaan</label>
                <div><?php echo $data['jenis_pekerjaan']; ?></div>
            </div>

            <div class="field">
                <label>Kontraktor</label>
                <div><?php echo $data['username']; ?></div>
            </div>

            <div class="field">
                <label>Volume</label>
                <div><?php echo $data['volume']; ?></div>
            </div>

            <div class="field">
                <label>Lokasi</label>
                <div><?php echo $data['lokasi']; ?></div>
            </div>

            <div class="field">
                <label>Material</label>
                <div><?php echo $data['material']; ?></div>
            </div>

            <div class="field">
                <label>Metode Kerja</label>
                <div><?php echo $data['metode_kerja']; ?></div>
            </div>

            <div class="field">
                <label>Tanggal Mulai</label>
                <div><?php echo $data['tanggal_mulai']; ?></div>
            </div>

            <div class="field">
                <label>Tanggal Selesai</label>
                <div><?php echo $data['tanggal_selesai']; ?></div>
            </div>

            <div class="field full">
                <label>Catatan Kontraktor</label>
                <div><?php echo $data['catatan']; ?></div>
            </div>

            <div class="field full">
                <label>Dokumen</label>
                <?php if ($data['dokumen']) { ?>
                    <a href="../uploads/<?php echo $data['dokumen']; ?>" target="_blank" style="color:#ffc107;">
                        Lihat Dokumen
                    </a>
                <?php } else { ?>
                    <div>Tidak ada dokumen</div>
                <?php } ?>
            </div>

        </div>

        <!-- FORM AKSI -->
        <form method="POST" action="proses_verifikasi.php">

            <input type="hidden" name="id" value="<?php echo $data['id']; ?>">

            <div class="field full">
                <label>Catatan Pengawas</label>
                <textarea name="catatan_pengawas"></textarea>
            </div>

            <div class="actions">
                <button type="submit" name="aksi" value="setuju" class="btn-approve">
                    ✔ Setujui
                </button>

                <button type="submit" name="aksi" value="tolak" class="btn-reject">
                    ✖ Tolak
                </button>
            </div>

        </form>

    </div>

</div>

</body>
</html>