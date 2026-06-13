<?php

namespace App;

use Exception;

class DokumentasiLapanganService
{
    // Mengizinkan ekstensi gambar standar lapangan
    protected array $allowedExtensions = ['jpg', 'jpeg', 'png'];
    // Maksimal ukuran file 2 MB (2 * 1024 * 1024 byte)
    protected int $maxFileSize = 2097152; 

    public function validasiDanProsesUnggah(array $fileInfo): array
    {
        // 1. Validasi apakah ada error bawaan PHP $_FILES
        if (!isset($fileInfo['name']) || !isset($fileInfo['tmp_name']) || !isset($fileInfo['size']) || $fileInfo['error'] !== 0) {
            throw new Exception("Gagal mengunggah file atau file rusak.");
        }

        // 2. Validasi Ukuran File
        if ($fileInfo['size'] > $this->maxFileSize) {
            throw new Exception("Ukuran file terlalu besar. Maksimal adalah 2 MB.");
        }

        // 3. Validasi Ekstensi File
        $ext = strtolower(pathinfo($fileInfo['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $this->allowedExtensions)) {
            throw new Exception("Ekstensi file tidak diizinkan. Hanya menerima JPG, JPEG, dan PNG.");
        }

        // 4. Standarisasi enkripsi nama file baru untuk mencegah duplikasi/karakter aneh
        $randomName = time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;

        return [
            'success' => true,
            'nama_file_baru' => $randomName,
            'message' => 'Validasi dokumentasi berhasil, file siap dipindahkan.'
        ];
    }
}