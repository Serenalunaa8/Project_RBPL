<?php

declare(strict_types=1);

namespace App;

use PDO;

class IzinPekerjaanService
{
    public const ALLOWED_FILE_EXTENSIONS = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'];

    public const STATUS_MENUNGGU_REVIEW = 'Menunggu Review';
    public const STATUS_DISETUJUI       = 'Disetujui';
    public const STATUS_DITOLAK         = 'Ditolak';

    private const REQUIRED_FIELDS = [
        'jenis_pekerjaan',
        'material',
        'volume',
        'satuan',
        'lokasi',
        'metode_kerja',
        'tanggal_mulai',
        'tanggal_selesai',
    ];

    public function __construct(private PDO $pdo)
    {
    }

    /**
     * Validasi data form pengajuan izin.
     *
     * @param array<string, mixed> $data
     * @return string[] Daftar pesan error. Kosong berarti valid.
     */
    public function validate(array $data): array
    {
        $errors = [];

        foreach (self::REQUIRED_FIELDS as $field) {
            if (!isset($data[$field]) || $data[$field] === '' || $data[$field] === null) {
                $errors[] = "Field '{$field}' wajib diisi.";
            }
        }

        // Validasi volume harus numerik dan lebih dari 0
        if (isset($data['volume']) && $data['volume'] !== '') {
            if (!is_numeric($data['volume']) || (float) $data['volume'] <= 0) {
                $errors[] = "Volume harus berupa angka lebih dari 0.";
            }
        }

        // Validasi tanggal selesai tidak boleh sebelum tanggal mulai
        if (!empty($data['tanggal_mulai']) && !empty($data['tanggal_selesai'])) {
            $mulai   = strtotime((string) $data['tanggal_mulai']);
            $selesai = strtotime((string) $data['tanggal_selesai']);

            if ($mulai !== false && $selesai !== false && $selesai < $mulai) {
                $errors[] = "Tanggal selesai tidak boleh sebelum tanggal mulai.";
            }
        }

        return $errors;
    }

    /**
     * Cek apakah ekstensi file yang diupload diizinkan.
     */
    public function isValidFileExtension(string $fileName): bool
    {
        $ext = strtolower((string) pathinfo($fileName, PATHINFO_EXTENSION));

        return in_array($ext, self::ALLOWED_FILE_EXTENSIONS, true);
    }

    /**
     * Buat nama file unik berbasis timestamp untuk menghindari duplikasi.
     */
    public function generateFileName(string $originalFileName): string
    {
        $ext = strtolower((string) pathinfo($originalFileName, PATHINFO_EXTENSION));

        return time() . '_' . uniqid() . '.' . $ext;
    }

    /**
     * Simpan pengajuan izin pekerjaan baru.
     *
     * @param array<string, mixed> $data
     * @return array{success: bool, errors: string[], id: int|null}
     */
    public function createIzin(array $data, int $kontraktorId, ?string $storedFileName = null): array
    {
        $errors = $this->validate($data);

        if (!empty($errors)) {
            return [
                'success' => false,
                'errors'  => $errors,
                'id'      => null,
            ];
        }

        $stmt = $this->pdo->prepare(
            'INSERT INTO form_izin_pekerjaan
                (kontraktor_id, jenis_pekerjaan, volume, satuan, material,
                 lokasi, metode_kerja, tanggal_mulai, tanggal_selesai,
                 dokumen, status, catatan)
             VALUES
                (:kontraktor_id, :jenis_pekerjaan, :volume, :satuan, :material,
                 :lokasi, :metode_kerja, :tanggal_mulai, :tanggal_selesai,
                 :dokumen, :status, :catatan)'
        );

        $stmt->execute([
            ':kontraktor_id'    => $kontraktorId,
            ':jenis_pekerjaan'  => $data['jenis_pekerjaan'],
            ':volume'           => $data['volume'],
            ':satuan'           => $data['satuan'],
            ':material'         => $data['material'],
            ':lokasi'           => $data['lokasi'],
            ':metode_kerja'     => $data['metode_kerja'],
            ':tanggal_mulai'    => $data['tanggal_mulai'],
            ':tanggal_selesai'  => $data['tanggal_selesai'],
            ':dokumen'          => $storedFileName,
            ':status'           => self::STATUS_MENUNGGU_REVIEW,
            ':catatan'          => $data['catatan'] ?? null,
        ]);

        return [
            'success' => true,
            'errors'  => [],
            'id'      => (int) $this->pdo->lastInsertId(),
        ];
    }

    /**
     * Setujui pengajuan izin (oleh pengawas).
     */
    public function approveIzin(int $izinId): bool
    {
        return $this->updateStatus($izinId, self::STATUS_DISETUJUI);
    }

    /**
     * Tolak pengajuan izin (oleh pengawas).
     */
    public function rejectIzin(int $izinId): bool
    {
        return $this->updateStatus($izinId, self::STATUS_DITOLAK);
    }

    private function updateStatus(int $izinId, string $status): bool
    {
        $stmt = $this->pdo->prepare(
            'UPDATE izin_pekerjaan SET status = :status WHERE id = :id'
        );

        $stmt->execute([
            ':status' => $status,
            ':id'     => $izinId,
        ]);

        return $stmt->rowCount() > 0;
    }

    /**
     * Ambil data izin berdasarkan ID.
     */
    public function getIzinById(int $izinId): array|null
    {
        $stmt = $this->pdo->prepare('SELECT * FROM form_izin_pekerjaan WHERE id = :id');
        $stmt->execute([':id' => $izinId]);

        $result = $stmt->fetch();

        return $result === false ? null : $result;
    }
}