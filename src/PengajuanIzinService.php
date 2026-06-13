<?php

namespace App;

use Exception;

class PengajuanIzinService
{
    protected $koneksi;

    // Perbaikan: Hapus kata 'mysqli' di depan $koneksi agar bisa menerima objek tiruan murni
    public function __construct($koneksi)
    {
        $this->koneksi = $koneksi;
    }

    public function simpanDanNotifikasi(array $data): array
    {
        if (empty($data['kontraktor_id']) || empty($data['jenis_pekerjaan']) || empty($data['lokasi'])) {
            throw new Exception("Data pengajuan tidak lengkap");
        }

        $query = "INSERT INTO form_izin_pekerjaan (kontraktor_id, jenis_pekerjaan, volume, satuan, material, lokasi, metode_kerja, tanggal_mulai, tanggal_selesai, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'Menunggu Review')";
        
        $stmt = $this->koneksi->prepare($query);
        if (!$stmt) {
            throw new Exception("Gagal menyiapkan statement database");
        }

        $volume = $data['volume'] ?? 0;
        $satuan = $data['satuan'] ?? '';
        $material = $data['material'] ?? '';
        $metode_kerja = $data['metode_kerja'] ?? '';
        $tanggal_mulai = $data['tanggal_mulai'] ?? null;
        $tanggal_selesai = $data['tanggal_selesai'] ?? null;

        $stmt->bind_param(
            "issssssss",
            $data['kontraktor_id'],
            $data['jenis_pekerjaan'],
            $volume,
            $satuan,
            $material,
            $data['lokasi'],
            $metode_kerja,
            $tanggal_mulai,
            $tanggal_selesai
        );

        if (!$stmt->execute()) {
            throw new Exception("Gagal menyimpan pengajuan izin pekerjaan");
        }

        $idPengajuan = $this->koneksi->insert_id;
        $stmt->close(); // <--- Baris ini yang tadinya memicu error closed

        $notifikasiSent = $this->kirimNotifikasiDashboardPengawas($idPengajuan, $data['jenis_pekerjaan']);

        return [
            'success' => true,
            'id_pengajuan' => $idPengajuan,
            'notifikasi_terkirim' => $notifikasiSent
        ];
    }

    public function kirimNotifikasiDashboardPengawas(int $idPengajuan, string $jenisPekerjaan): bool
    {
        $pesan = "Pengajuan baru #" . $idPengajuan . " - " . $jenisPekerjaan . " memerlukan review Anda.";
        $query = "INSERT INTO notifikasi (pesan, untuk_role, status_baca) VALUES (?, 'pengawas', 0)";
        
        $stmt = $this->koneksi->prepare($query);
        if ($stmt) {
            $stmt->bind_param("s", $pesan);
            $success = $stmt->execute();
            $stmt->close();
            return $success;
        }
        
        return false;
    }
}