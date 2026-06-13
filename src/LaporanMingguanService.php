<?php

namespace App;

use Exception;

class LaporanMingguanService
{
    protected $koneksi;

    public function __construct($koneksi)
    {
        $this->koneksi = $koneksi;
    }

    public function susunLaporanMingguan(array $data, array $daftarProgresHarian): array
    {
        // 1. Validasi Input Dasar
        if (empty($data['form_id']) || empty($data['minggu_ke']) || empty($daftarProgresHarian)) {
            throw new Exception("Data penyusunan laporan mingguan tidak lengkap.");
        }

        // 2. Validasi Logika Minggu Ke
        if ($data['minggu_ke'] <= 0) {
            throw new Exception("Nomor minggu harus lebih besar dari nol.");
        }

        // 3. Logika Bisnis: Menghitung Rata-rata Progres dari Harian dalam 1 Minggu
        $totalProgres = 0;
        foreach ($daftarProgresHarian as $progres) {
            $totalProgres += (int)$progres;
        }
        $rataRataProgres = $totalProgres / count($daftarProgresHarian);

        // 4. Simpan ke database laporan_mingguan
        $query = "INSERT INTO laporan_mingguan (form_id, minggu_ke, akumulasi_progres, catatan_mingguan) VALUES (?, ?, ?, ?)";
        $stmt = $this->koneksi->prepare($query);
        
        if (!$stmt) {
            throw new Exception("Gagal menyiapkan statement laporan mingguan.");
        }

        $catatan = $data['catatan_mingguan'] ?? '-';
        
        $stmt->bind_param("iids", $data['form_id'], $data['minggu_ke'], $rataRataProgres, $catatan);
        $success = $stmt->execute();
        $stmt->close();

        if (!$success) {
            throw new Exception("Gagal menyimpan laporan mingguan ke database.");
        }

        return [
            'success' => true,
            'minggu_ke' => $data['minggu_ke'],
            'progres_akumulasi' => $rataRataProgres,
            'message' => 'Laporan mingguan berhasil disusun.'
        ];
    }
}