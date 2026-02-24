<?php
session_start();
include '../koneksi.php';

/* ====== CEK ROLE ====== */
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'kontraktor') {
    header("Location: ../login.php");
    exit();
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
    'Kayu Kamper',
    'Keramik 40x40',
    'Keramik 60x60',
    'Cat Interior',
    'Cat Eksterior',
    'Plafon Gypsum',
    'Genteng Metal',
    'Genteng Tanah Liat'
];

$lokasi_options = [
    'Lantai 1',
    'Lantai 2',
    'Area Parkir',
    'Area Landscape',
    'Area MEP'
];

$metode_kerja_options = [
    'Manual',
    'Semi Mekanis',
    'Mekanis',
    'Precast',
    'Konvensional'
];

$satuan_options = ['m²','m³','unit','kg','ton','meter','liter'];


/* ====== PROSES SUBMIT ====== */
if (isset($_POST['submit'])) {

    $id = $_SESSION['id'];

    $jenis = mysqli_real_escape_string($conn, $_POST['jenis']);
    $volume_angka = $_POST['volume'];
    $satuan = $_POST['satuan'];
    $material = mysqli_real_escape_string($conn, $_POST['material']);
    $lokasi = mysqli_real_escape_string($conn, $_POST['lokasi']);
    $metode = mysqli_real_escape_string($conn, $_POST['metode']);
    $mulai = $_POST['mulai'];
    $selesai = $_POST['selesai'];
    $catatan = mysqli_real_escape_string($conn, $_POST['catatan']);

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
        (kontraktor_id, jenis_pekerjaan, volume, material, lokasi, metode_kerja, tanggal_mulai, tanggal_selesai, catatan, status)
        VALUES
        ('$id','$jenis','$volume','$material','$lokasi','$metode','$mulai','$selesai','$catatan','Menunggu Review Pengawas')";

        if (mysqli_query($conn, $query)) {
            $notifikasi = "✅ Izin pekerjaan berhasil dikirim.";
            $_POST = [];
        } else {
            $notifikasi = "❌ Error Database: " . mysqli_error($conn);
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
            <option value="<?php echo $j; ?>"><?php echo $j; ?></option>
        <?php endforeach; ?>
    </select>
    <br><br>

    <label>Volume</label><br>
    <input type="number" name="volume" step="0.01" required>

    <select name="satuan" required>
        <option value="">-- Satuan --</option>
        <?php foreach ($satuan_options as $s): ?>
            <option value="<?php echo $s; ?>"><?php echo $s; ?></option>
        <?php endforeach; ?>
    </select>
    <br><br>

    <label>Material</label><br>
    <select name="material" required>
        <option value="">-- Pilih --</option>
        <?php foreach ($material_options as $m): ?>
            <option value="<?php echo $m; ?>"><?php echo $m; ?></option>
        <?php endforeach; ?>
    </select>
    <br><br>

    <label>Lokasi</label><br>
    <select name="lokasi" required>
        <option value="">-- Pilih --</option>
        <?php foreach ($lokasi_options as $l): ?>
            <option value="<?php echo $l; ?>"><?php echo $l; ?></option>
        <?php endforeach; ?>
    </select>
    <br><br>

    <label>Metode Kerja</label><br>
    <select name="metode" required>
        <option value="">-- Pilih --</option>
        <?php foreach ($metode_kerja_options as $mk): ?>
            <option value="<?php echo $mk; ?>"><?php echo $mk; ?></option>
        <?php endforeach; ?>
    </select>
    <br><br>

    <label>Tanggal Mulai</label><br>
    <input type="date" name="mulai" required>
    <br><br>

    <label>Tanggal Selesai</label><br>
    <input type="date" name="selesai" required>
    <br><br>

    <label>Catatan</label><br>
    <textarea name="catatan"></textarea>
    <br><br>

    <button type="submit" name="submit">Kirim</button>

</form>

</body>
</html>