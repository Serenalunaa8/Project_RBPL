<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../koneksi.php';

/* ====== CEK LOGIN & ROLE ====== */
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'kontraktor') {
    header("Location: ../login.php");
    exit();
}

if (!isset($_SESSION['id'])) {
    die("Session ID tidak ditemukan. Pastikan login menyimpan session id.");
}

$notifikasi = "";

/* ====== DROPDOWN DATA ====== */
$jenis_pekerjaan_options = [
    'Pekerjaan Persiapan',
    'Pekerjaan Tanah',
    'Pekerjaan Pondasi',
    'Pekerjaan Struktur Beton',
    'Pekerjaan Dinding',
    'Pekerjaan Plafon',
    'Pekerjaan Atap',
    'Pekerjaan Kusen & Pintu',
    'Pekerjaan Finishing',
    'Pekerjaan Landscape'
];

$material_options = [
    'Beton Ready Mix K-225',
    'Beton Ready Mix K-250',
    'Beton Ready Mix K-300',
    'Bata Merah',
    'Bata Ringan (Hebel)',
    'Baja Ringan',
    'Besi Beton',
    'Keramik 60x60',
    'Cat Interior',
    'Plafon Gypsum'
];

$lokasi_options = ['Lantai 1','Lantai 2','Area Parkir','Area Landscape','Area MEP'];
$metode_kerja_options = ['Manual','Semi Mekanis','Mekanis','Precast','Konvensional'];
$satuan_options = ['m²','m³','unit','kg','ton','meter','liter'];

/* ====== PROSES SUBMIT ====== */
if (isset($_POST['submit'])) {

    $id = $_SESSION['id'];

    $jenis = mysqli_real_escape_string($koneksi, $_POST['jenis']);
    $volume_angka = $_POST['volume'];
    $satuan = $_POST['satuan'];
    $material = mysqli_real_escape_string($koneksi, $_POST['material']);
    $lokasi = mysqli_real_escape_string($koneksi, $_POST['lokasi']);
    $metode = mysqli_real_escape_string($koneksi, $_POST['metode']);
    $mulai = $_POST['mulai'];
    $selesai = $_POST['selesai'];

    /* VALIDASI */
    if (
        empty($jenis) || empty($volume_angka) || empty($satuan) ||
        empty($material) || empty($lokasi) ||
        empty($metode) || empty($mulai) || empty($selesai)
    ) {
        $notifikasi = "❌ Semua field wajib diisi.";
    }
    elseif (!is_numeric($volume_angka)) {
        $notifikasi = "❌ Volume harus berupa angka.";
    }
    elseif ($selesai < $mulai) {
        $notifikasi = "❌ Tanggal selesai tidak boleh sebelum tanggal mulai.";
    }
    else {

        $volume = $volume_angka . " " . $satuan;

        $query = "INSERT INTO form_izin_pekerjaan
        (kontraktor_id, jenis_pekerjaan, volume, material, lokasi, metode_kerja, tanggal_mulai, tanggal_selesai, status)
        VALUES
        ('$id','$jenis','$volume','$material','$lokasi','$metode','$mulai','$selesai','Menunggu Review Pengawas')";

        if (mysqli_query($koneksi, $query)) {
            $notifikasi = "✅ Izin pekerjaan berhasil dikirim.";
        } else {
            $notifikasi = "❌ Error Database: " . mysqli_error($koneksi);
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Form Izin Pekerjaan</title>
</head>
<body>

<h2>Form Izin Pekerjaan</h2>

<?php if($notifikasi != "") echo "<p>$notifikasi</p>"; ?>

<form method="POST">

    <label>Jenis Pekerjaan</label><br>
    <select name="jenis" required>
        <option value="">-- Pilih --</option>
        <?php foreach ($jenis_pekerjaan_options as $j): ?>
            <option value="<?= $j ?>"><?= $j ?></option>
        <?php endforeach; ?>
    </select>
    <br><br>

    <label>Volume</label><br>
    <input type="number" name="volume" step="0.01" required>
    <select name="satuan" required>
        <option value="">-- Satuan --</option>
        <?php foreach ($satuan_options as $s): ?>
            <option value="<?= $s ?>"><?= $s ?></option>
        <?php endforeach; ?>
    </select>
    <br><br>

    <label>Material</label><br>
    <select name="material" required>
        <option value="">-- Pilih --</option>
        <?php foreach ($material_options as $m): ?>
            <option value="<?= $m ?>"><?= $m ?></option>
        <?php endforeach; ?>
    </select>
    <br><br>

    <label>Lokasi</label><br>
    <select name="lokasi" required>
        <option value="">-- Pilih --</option>
        <?php foreach ($lokasi_options as $l): ?>
            <option value="<?= $l ?>"><?= $l ?></option>
        <?php endforeach; ?>
    </select>
    <br><br>

    <label>Metode Kerja</label><br>
    <select name="metode" required>
        <option value="">-- Pilih --</option>
        <?php foreach ($metode_kerja_options as $mk): ?>
            <option value="<?= $mk ?>"><?= $mk ?></option>
        <?php endforeach; ?>
    </select>
    <br><br>

    <label>Tanggal Mulai</label><br>
    <input type="date" name="mulai" required>
    <br><br>

    <label>Tanggal Selesai</label><br>
    <input type="date" name="selesai" required>
    <br><br>

    <button type="submit" name="submit">Kirim</button>

</form>

</body>
</html>