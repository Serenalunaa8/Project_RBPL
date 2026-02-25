<?php
include "../config/koneksi.php";
?>

<!DOCTYPE html>
<html>
<head>
    <title>Verifikasi Izin</title>
    <link rel="stylesheet" href="../styling/css/dashboard.css">
</head>
<body>

<div class="content">
    <h2>Verifikasi Izin Pekerjaan</h2>

    <table>
        <tr>
            <th>Nama Pekerjaan</th>
            <th>Lokasi</th>
            <th>Tanggal</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>

        <?php
        $data = mysqli_query($conn,"SELECT * FROM izin_pekerjaan");
        while($d = mysqli_fetch_array($data)){
        ?>
        <tr>
            <td><?= $d['nama_pekerjaan']; ?></td>
            <td><?= $d['lokasi']; ?></td>
            <td><?= $d['tanggal']; ?></td>
            <td><?= $d['status']; ?></td>
            <td>
                <a class="btn-approve" href="proses_verifikasi.php?id=<?= $d['id_izin']; ?>&aksi=Disetujui">Setujui</a>
                <a class="btn-reject" href="proses_verifikasi.php?id=<?= $d['id_izin']; ?>&aksi=Ditolak">Tolak</a>
            </td>
        </tr>
        <?php } ?>
    </table>

</div>

</body>
</html>