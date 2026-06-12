<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != "kontraktor") {
    header("Location: ../login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Ajukan Izin</title>

<meta name="viewport"
      content="width=device-width, initial-scale=1.0">

<link rel="preconnect"
      href="https://fonts.googleapis.com">

<link rel="preconnect"
      href="https://fonts.gstatic.com"
      crossorigin>

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap"
      rel="stylesheet">

<link rel="stylesheet"
      href="asset/kontraktordash.css">
</head>
<body>

<?php include 'sidebar.php'; ?>

<main class="main">

    <header class="topbar">

        <h2>Ajukan Izin Pekerjaan</h2>

        <div class="topbar-right">
            <div class="date-chip">
                <?= date('d F Y') ?>
            </div>
        </div>

    </header>

   <div class="panel fade-up">

    <div class="section-header">
        <div>
            <div class="section-title">
                Form Pengajuan Izin
            </div>

            <div class="section-sub">
                Lengkapi data pekerjaan yang akan diajukan
            </div>
        </div>
    </div>

    <div class="info-banner">
        <strong>Informasi:</strong>
        Pastikan data pekerjaan, volume material,
        dan jadwal pelaksanaan sudah sesuai
        sebelum pengajuan dikirim.
    </div>

   <form method="POST"
      action="prosesIzin.php"
      enctype="multipart/form-data">

    <!-- Informasi Pekerjaan -->
    <div class="form-card">

        <div class="card-title">
            📋 Informasi Pekerjaan
        </div>

        <div class="form-row">

            <div class="form-group">
                <label>Jenis Pekerjaan</label>
                <select name="jenis_pekerjaan" required>
                    <option value="">Pilih Jenis Pekerjaan</option>
                    <option>Pekerjaan Persiapan</option>
                    <option>Pekerjaan Struktur</option>
                    <option>Pekerjaan Finishing</option>
                    <option>Pekerjaan Landscape</option>
                </select>
            </div>

            <div class="form-group">
                <label>Material</label>
                <select name="material" required>
                    <option value="">Pilih Material</option>
                    <option>Beton Ready Mix K-225</option>
                    <option>Besi Beton</option>
                    <option>Bata Ringan</option>
                    <option>Keramik</option>
                    <option>Cat Interior</option>
                </select>
            </div>

        </div>

        <div class="form-row">

            <div class="form-group">
                <label>Volume</label>
                <input type="number"
                       name="volume"
                       placeholder="Masukkan volume"
                       required>
            </div>

            <div class="form-group">
                <label>Satuan</label>
                <select name="satuan" required>
                    <option value="">Pilih Satuan</option>
                    <option>m²</option>
                    <option>m³</option>
                    <option>kg</option>
                    <option>unit</option>
                    <option>meter</option>
                </select>
            </div>

        </div>

    </div>

    <!-- Lokasi -->
    <div class="form-card">

        <div class="card-title">
            📍 Lokasi & Metode Kerja
        </div>

        <div class="form-row">

            <div class="form-group">
                <label>Lokasi Pekerjaan</label>
                <select name="lokasi" required>
                    <option>Lantai 1</option>
                    <option>Lantai 2</option>
                    <option>Area Parkir</option>
                    <option>Area Landscape</option>
                </select>
            </div>

            <div class="form-group">
                <label>Metode Kerja</label>
                <select name="metode_kerja" required>
                    <option>Manual</option>
                    <option>Semi Mekanis</option>
                    <option>Mekanis</option>
                    <option>Precast</option>
                </select>
            </div>

        </div>

    </div>

    <!-- Jadwal -->
    <div class="form-card">

        <div class="card-title">
            📅 Jadwal Pelaksanaan
        </div>

        <div class="form-row">

            <div class="form-group">
                <label>Tanggal Mulai</label>
                <input type="date"
                       name="tanggal_mulai"
                       required>
            </div>

            <div class="form-group">
                <label>Tanggal Selesai</label>
                <input type="date"
                       name="tanggal_selesai"
                       required>
            </div>

        </div>

    </div>

    <!-- Dokumen -->
    <div class="form-card">

        <div class="card-title">
            📄 Dokumen Pendukung
        </div>

        <div class="form-group">

            <input type="file"
                   name="dokumen">

            <small>
                Upload PDF, DOC, JPG atau PNG
            </small>

        </div>

    </div>

    <!-- Catatan -->
    <div class="form-card">

        <div class="card-title">
            📝 Catatan Tambahan
        </div>

        <div class="form-group">

            <textarea
                name="catatan"
                rows="5"
                placeholder="Masukkan catatan tambahan jika diperlukan"></textarea>

        </div>

    </div>

    <button type="submit" class="btn-submit">
        Ajukan Izin Pekerjaan
    </button>

</form>

</div>
</main>

</body>
</html>