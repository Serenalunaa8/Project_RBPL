<?php

namespace App;

use Exception;

class PengesahanLaporanService
{
    protected $koneksi;

    public function __construct($koneksi)
    {
        $this->koneksi = $koneksi;
    }

    // 1. Alur Penyimpanan Laporan Harian oleh Kontraktor
    public function simpanLaporanHarian(array $data): array
    {
        if (empty($data['form_id']) || !isset($data['progres']) || empty($data['cuaca']) || empty($data['foto'])) {
            throw new Exception("Data laporan harian tidak lengkap.");
        }

        $query = "INSERT INTO laporan_harian (id, tanggal, progres, cuaca, foto, status_pengesahan) VALUES (?, ?, ?, ?, ?, 'Menunggu Pengesahan')";
        $stmt = $this->koneksi->prepare($query);
        
        if (!$stmt) {
            throw new Exception("Gagal menyiapkan statement penyimpanan laporan.");
        }

        $tanggal = $data['tanggal'] ?? date('Y-m-d');
        $progres = (int)$data['progres'];

        $stmt->bind_param("isiss", $data['form_id'], $tanggal, $progres, $data['cuaca'], $data['foto']);
        $success = $stmt->execute();
        $stmt->close();

        if (!$success) {
            throw new Exception("Gagal menyimpan laporan harian.");
        }

        return [
            'success' => true,
            'message' => 'Laporan harian berhasil disimpan.'
        ];
    }

    // 2. Alur Pengesahan Laporan Harian oleh Pengawas
    public function sahkanLaporanHarian(int $laporanId, string $statusKeputusan, string $catatanPengawas): array
    {
        // Validasi keputusan status
        if (!in_array($statusKeputusan, ['Disahkan', 'Ditolak', 'Perlu Perbaikan'])) {
            throw new Exception("Status pengesahan tidak valid.");
        }

        $query = "UPDATE laporan_harian SET status_pengesahan = ?, catatan_pengawas = ? WHERE id_laporan = ?";
        $stmt = $this->koneksi->prepare($query);

        if (!$stmt) {
            throw new Exception("Gagal menyiapkan statement pengesahan.");
        }

        $stmt->bind_param("ssi", $statusKeputusan, $catatanPengawas, $laporanId);
        $success = $stmt->execute();
        $stmt->close();

        if (!$success) {
            throw new Exception("Gagal memperbarui pengesahan laporan.");
        }

        return [
            'success' => true,
            'status_akhir' => $statusKeputusan
        ];
    }
}