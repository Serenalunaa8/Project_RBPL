<?php
include "../koneksi.php";

header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=riwayat.xls");

$query = mysqli_query($koneksi, "SELECT * FROM form_izin_pekerjaan");

echo "<table border='1'>";
echo "<tr><th>Jenis</th><th>Lokasi</th><th>Status</th></tr>";

while($row = mysqli_fetch_assoc($query)){
    echo "<tr>
        <td>{$row['jenis_pekerjaan']}</td>
        <td>{$row['lokasi']}</td>
        <td>{$row['status']}</td>
    </tr>";
}
echo "</table>";
?>