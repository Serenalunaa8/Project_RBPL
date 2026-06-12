<?php

namespace App;

use Exception;

class NotifikasiService
{
    protected $koneksi;

    public function __construct($koneksi)
    {
        $this->koneksi = $koneksi;
    }

    // 1. Ambil notifikasi berdasarkan user_id (untuk tampilan di halaman kontraktor)
    public function getNotifikasiByUser(int $userId): array
    {
        $query = "SELECT id, user_id, pesan, form_id, created_at FROM notifikasi WHERE user_id = ? ORDER BY id DESC";
        $stmt = $this->koneksi->prepare($query);
        
        if (!$stmt) {
            throw new Exception("Gagal mempersiapkan query notifikasi.");
        }

        $stmt->bind_param("i", $userId);
        $stmt->execute();
        
        $result = $stmt->get_result();
        $listNotif = [];
        
        while ($row = $result->fetch_assoc()) {
            $listNotif[] = $row;
        }
        
        $stmt->close();
        return $listNotif;
    }

    // 2. Kirim notifikasi baru (bisa dipanggil dari modul lain)
    public function kirimNotifikasi(int $userId, string $pesan, int $formId): bool
    {
        if (empty(trim($pesan))) {
            throw new Exception("Pesan notifikasi tidak boleh kosong.");
        }

        $query = "INSERT INTO notifikasi (user_id, pesan, form_id) VALUES (?, ?, ?)";
        $stmt = $this->koneksi->prepare($query);
        
        if (!$stmt) {
            return false;
        }

        $stmt->bind_param("isi", $userId, $pesan, $formId);
        $success = $stmt->execute();
        $stmt->close();
        
        return $success;
    }
}