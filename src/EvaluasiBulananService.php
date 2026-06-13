<?php

namespace App;

use Exception;

class EvaluasiBulananService
{
    protected $koneksi;

    public function __construct($koneksi)
    {
        $this->koneksi = $koneksi;
    }

    public function evaluasiProgresBulanan(array $data): array
    {
        // 1. Validasi Input Utama
        if (empty($data['form_id']) || empty($data['bulan_ke']) || empty($data['realisasi_progres']) || empty($data['target_progres'])) {
    throw new Exception("Data evaluasi laporan bulanan tidak lengkap.");
}

        $realisasi = (float)$data['realisasi_progres'];
        $target = (float)$data['target_progres'];

        // 2. Logika Bisnis: Hitung Deviasi Progres
        $deviasi = $realisasi - $target;

        // 3. Logika Bisnis: Klasifikasi Status Performa Proyek secara otomatis
        if ($deviasi >= 0) {
            $statusPerforma = "Tepat Waktu / Melebihi Target";
        } elseif ($deviasi < 0 && $deviasi >= -5) {
            $statusPerforma = "Terlambat (Slightly Delayed)";
        } else {
            $statusPerforma = "Kritis (Keterlambatan Signifikan)";
        }

        // 4. Simpan ke database evaluasi_bulanan
        $query = "INSERT INTO evaluasi_bulanan (form_id, bulan_ke, target_progres, realisasi_progres, deviasi, status_performa, catatan_evaluasi) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->koneksi->prepare($query);

        if (!$stmt) {
            throw new Exception("Gagal menyiapkan statement evaluasi bulanan.");
        }

        $catatan = $data['catatan_evaluasi'] ?? '-';

        $stmt->bind_param("iidddss", $data['form_id'], $data['bulan_ke'], $target, $realisasi, $deviasi, $statusPerforma, $catatan);
        $success = $stmt->execute();
        $stmt->close();

        if (!$success) {
            throw new Exception("Gagal menyimpan evaluasi bulanan ke database.");
        }

        return [
            'success' => true,
            'deviasi' => $deviasi,
            'status_performa' => $statusPerforma,
            'message' => 'Evaluasi laporan bulanan berhasil diproses.'
        ];
    }
}