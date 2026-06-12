<?php

namespace App;

use Exception;

class LaporanHarianService
{
    protected $koneksi;

    public function __construct($koneksi)
    {
        $this->koneksi = $koneksi;
    }

    public function catatLaporan(array $data): array
    {
        // 1. Validasi Input Utama
        if (empty($data['form_id']) || !isset($data['progres']) || empty($data['cuaca']) || empty($data['foto'])) {
            throw new Exception("Data laporan harian tidak lengkap.");
        }

        // 2. Validasi Batasan Logika Angka Progres (0 - 100)
        $progres = (int)$data['progres'];
        if ($progres < 0 || $progres > 100) {
            throw new Exception("Persentase progres harus berada di antara 0% hingga 100%.");
        }

        // 3. Simpan ke database laporan_harian
        $query = "INSERT INTO laporan_harian (id, tanggal, progres, cuaca, foto) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->koneksi->prepare($query);
        
        if (!$stmt) {
            throw new Exception("Gagal menyiapkan statement laporan harian.");
        }

        $tanggal = $data['tanggal'] ?? date('Y-m-d');
        
        $stmt->bind_param("isiss", $data['form_id'], $tanggal, $progres, $data['cuaca'], $data['foto']);
        $success = $stmt->execute();
        $stmt->close();

        if (!$success) {
            throw new Exception("Gagal menyimpan laporan harian ke database.");
        }

        return [
            'success' => true,
            'message' => 'Laporan harian berhasil dicatat.'
        ];
    }
}