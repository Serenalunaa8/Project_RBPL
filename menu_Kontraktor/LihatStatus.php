<?php
session_start();
include '../koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'kontraktor') {
    header("Location: ../login.php");
    exit();
}

$id_kontraktor = $_SESSION['id'];

/* ================= FILTER ================= */
$status_filter = "";
$where_status = "";

if (isset($_GET['status']) && $_GET['status'] != "") {
    $status_filter = $_GET['status'];
    $where_status = "AND status = '$status_filter'";
}

/* ================= HITUNG TOTAL DATA ================= */
$total_query = mysqli_query($koneksi,
    "SELECT COUNT(*) as total FROM form_izin_pekerjaan 
     WHERE kontraktor_id='$id_kontraktor' $where_status");

$total_data = mysqli_fetch_assoc($total_query)['total'];

/* ================= PAGINATION ================= */
$limit = 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

if ($page < 1) {
    $page = 1;
}

$total_page = ($total_data > 0) ? ceil($total_data / $limit) : 1;

$start = ($page - 1) * $limit;

$query = "SELECT * FROM form_izin_pekerjaan
          WHERE kontraktor_id='$id_kontraktor' $where_status
          ORDER BY created_at DESC
          LIMIT $start, $limit";

$result = mysqli_query($koneksi, $query);
?>

<h2>Status Izin Pekerjaan</h2>

<form method="GET">
    <select name="status">
        <option value="">Semua Status</option>
        <option value="Menunggu Review Pengawas">Menunggu</option>
        <option value="Revisi">Revisi</option>
        <option value="Disetujui Pengawas">Disetujui</option>
        <option value="Ditolak">Ditolak</option>
    </select>
    <button type="submit">Filter</button>
</form>

<br>

<?php if ($total_data == 0) { ?>

    <p style="color:gray;">Belum ada pengajuan izin pekerjaan.</p>

<?php } else { ?>

<table border="1" cellpadding="8">
<tr>
    <th>Jenis</th>
    <th>Lokasi</th>
    <th>Tanggal</th>
    <th>Status</th>
    <th>Catatan Kontraktor</th>
    <th>Catatan Pengawas</th>
</tr>

<?php while($row = mysqli_fetch_assoc($result)) { ?>
<tr>
    <td><?php echo $row['jenis_pekerjaan']; ?></td>
    <td><?php echo $row['lokasi']; ?></td>
    <td><?php echo $row['tanggal_mulai']." s/d ".$row['tanggal_selesai']; ?></td>
    <td><?php echo $row['status']; ?></td>
    <td><?php echo $row['catatan_pengawas'] ?: "-"; ?></td>
</tr>
<?php } ?>
</table>

<br>

<!-- PAGINATION hanya muncul jika data > limit -->
<?php if ($total_data > $limit) { ?>
    <?php for ($i = 1; $i <= $total_page; $i++) { ?>
        <a href="?page=<?php echo $i; ?>&status=<?php echo $status_filter; ?>">
            <?php echo $i; ?>
        </a>
    <?php } ?>
<?php } ?>

<?php } ?>

<br><br>
<a href="../dashboard/kontraktor.php">Kembali ke Dashboard</a>