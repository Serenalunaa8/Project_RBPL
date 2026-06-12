<?php

namespace App;

use Exception;

class VerifikasiIzinService
{
    protected $koneksi;

    public function __construct($koneksi)
    {
        $this->koneksi = $koneksi;
    }

    public function prosesVerifikasi(int $id, string $action, string $catatan, array $dataIzin): array
    {
        // 1. Tentukan status baru berdasarkan aksi pengawas
        if ($action === 'approve') {
            $newStatus = 'Disetujui Pengawas';
        } elseif ($action === 'reject') {
            $newStatus = 'Ditolak';
        } elseif ($action === 'revisi') {
            // Permintaan revisi wajib menyertakan catatan teknis
            if (empty(trim($catatan))) {
                throw new Exception("Catatan teknis wajib diisi untuk permintaan revisi.");
            }
            $newStatus = 'Revisi';
        } else {
            throw new Exception("Aksi verifikasi tidak valid.");
        }

        // 2. Update status dan catatan di tabel form_izin_pekerjaan
        $queryUpdate = "UPDATE form_izin_pekerjaan SET status = ?, catatan = ? WHERE id = ?";
        $stmtUpdate = $this->koneksi->prepare($queryUpdate);
        
        if (!$stmtUpdate) {
            throw new Exception("Gagal mempersiapkan update status.");
        }

        $stmtUpdate->bind_param("ssi", $newStatus, $catatan, $id);
        $executeUpdate = $stmtUpdate->execute();
        $stmtUpdate->close();

        if (!$executeUpdate) {
            throw new Exception("Gagal memperbarui status di database.");
        }

        // 3. Simpan pesan notifikasi untuk kontraktor terkait
        $kontraktorId = (int)($dataIzin['kontraktor_id'] ?? 0);
        $jenisPekerjaan = $dataIzin['jenis_pekerjaan'] ?? '';
        $pesanNotif = "Status izin pekerjaan '$jenisPekerjaan' telah diperbarui menjadi '$newStatus'.";

        $queryNotif = "INSERT INTO notifikasi (user_id, pesan, form_id) VALUES (?, ?, ?)";
        $stmtNotif = $this->koneksi->prepare($queryNotif);
        
        $notifikasiTerkirim = false;
        if ($stmtNotif) {
            $stmtNotif->bind_param("isi", $kontraktorId, $pesanNotif, $id);
            $notifikasiTerkirim = $stmtNotif->execute();
            $stmtNotif->close();
        }

        return [
            'success' => true,
            'status_baru' => $newStatus,
            'notifikasi_terkirim' => $notifikasiTerkirim
        ];
    }
}