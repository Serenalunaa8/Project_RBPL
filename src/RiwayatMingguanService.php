<?php

namespace App;

use Exception;

class RiwayatMingguanService
{
    protected $koneksi;

    public function __construct($koneksi)
    {
        $this->koneksi = $koneksi;
    }

    // 1. Fungsi Menyimpan Riwayat Rekap Mingguan
    public function simpanRiwayat(array $data): array
    {
        if (empty($data['form_id']) || empty($data['minggu_ke']) || !isset($data['akumulasi_progres'])) {
            throw new Exception("Data riwayat laporan mingguan tidak lengkap.");
        }

        $query = "INSERT INTO riwayat_laporan_mingguan (form_id, minggu_ke, akumulasi_progres, tanggal_cetak) VALUES (?, ?, ?, ?)";
        $stmt = $this->koneksi->prepare($query);

        if (!$stmt) {
            throw new Exception("Gagal menyiapkan statement penyimpanan riwayat.");
        }

        $tanggalCetak = $data['tanggal_cetak'] ?? date('Y-m-d H:i:s');

        $stmt->bind_param("iids", $data['form_id'], $data['minggu_ke'], $data['akumulasi_progres'], $tanggalCetak);
        $success = $stmt->execute();
        $stmt->close();

        if (!$success) {
            throw new Exception("Gagal menyimpan riwayat laporan ke database.");
        }

        return [
            'success' => true,
            'message' => 'Riwayat laporan mingguan berhasil diarsipkan.'
        ];
    }

    // 2. Fungsi Mengambil Riwayat Tampilan (Read)
    public function getRiwayatByFormId(int $formId): array
    {
        $query = "SELECT id_riwayat, form_id, minggu_ke, akumulasi_progres, tanggal_cetak FROM riwayat_laporan_mingguan WHERE form_id = ? ORDER BY minggu_ke ASC";
        $stmt = $this->koneksi->prepare($query);

        if (!$stmt) {
            throw new Exception("Gagal mempersiapkan query riwayat laporan.");
        }

        $stmt->bind_param("i", $formId);
        $stmt->execute();

        $result = $stmt->get_result();
        $riwayatList = [];

        while ($row = $result->fetch_assoc()) {
            $riwayatList[] = $row;
        }

        $stmt->close();
        return $riwayatList;
    }
}