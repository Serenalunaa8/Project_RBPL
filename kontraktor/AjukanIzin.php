<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Ajukan Izin | CV Cipta Manunggal Konsultan</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../kontraktor/asset/izinkontraktor.css">
</head>
<body>

<section class="form-section">
    <div class="form-card">

        <form method="POST" action="prosesIzin.php" enctype="multipart/form-data">

            <div class="field">
                <label>Jenis Pekerjaan</label>
                <select name="jenis_pekerjaan" required>
                    <option value="">Pilih jenis pekerjaan</option>
                    <option>Pekerjaan Persiapan</option>
                    <option>Pekerjaan Struktur</option>
                    <option>Pekerjaan Finishing</option>
                    <option>Pekerjaan Landscape</option>
                </select>
            </div>

            <div class="grid-2">
                <div>
                    <label>Volume</label>
                    <input type="number" name="volume" required>
                </div>
                <div>
                    <label>Satuan</label>
                    <select name="satuan" required>
                        <option value="">Pilih satuan</option>
                        <option>m²</option>
                        <option>m³</option>
                        <option>unit</option>
                        <option>kg</option>
                        <option>meter</option>
                    </select>
                </div>
            </div>

            <div class="field">
                <label>Material</label>
                <select name="material" required>
                    <option>Beton Ready Mix K-225</option>
                    <option>Besi Beton</option>
                    <option>Bata Ringan</option>
                    <option>Keramik</option>
                    <option>Cat Interior</option>
                </select>
            </div>

            <div class="field">
                <label>Lokasi Pekerjaan</label>
                <select name="lokasi" required>
                    <option>Lantai 1</option>
                    <option>Lantai 2</option>
                    <option>Area Parkir</option>
                    <option>Area Landscape</option>
                </select>
            </div>

            <div class="field">
                <label>Metode Kerja</label>
                <select name="metode_kerja" required>
                    <option>Manual</option>
                    <option>Semi Mekanis</option>
                    <option>Mekanis</option>
                    <option>Precast</option>
                </select>
            </div>

            <div class="grid-2">
                <div>
                    <label>Tanggal Mulai</label>
                    <input type="date" name="tanggal_mulai" required>
                </div>
                <div>
                    <label>Tanggal Selesai</label>
                    <input type="date" name="tanggal_selesai" required>
                </div>
            </div>

            <div class="field">
                <label>Upload Dokumen (Opsional)</label>
                <input type="file" name="dokumen" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
            </div>

            <div class="field">
                <label>Catatan Tambahan</label>
            <textarea 
            name="catatan" 
            rows="4" 
            placeholder="Tuliskan keterangan tambahan terkait pekerjaan (opsional)...">
            </textarea>
            </div>

            <button type="submit" name="submit">Ajukan Izin</button>

        </form>

    </div>
</section>

</body>
</html>