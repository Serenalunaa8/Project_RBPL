<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use App\DokumentasiLapanganService;
use Exception;

class DokumentasiLapanganServiceTest extends TestCase
{
    protected $uploadService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->uploadService = new DokumentasiLapanganService();
    }

    public function testValidasiDanProsesUnggahSucceedsWithValidImage()
    {
        $dummyFile = [
            'name' => 'progres_pondasi.jpg',
            'tmp_name' => '/tmp/phpYg83x',
            'size' => 500000, // 500 KB (Valid)
            'error' => 0
        ];

        $result = $this->uploadService->validasiDanProsesUnggah($dummyFile);

        $this->assertTrue($result['success']);
        $this->assertStringEndsWith('.jpg', $result['nama_file_baru']);
        $this->assertEquals('Validasi dokumentasi berhasil, file siap dipindahkan.', $result['message']);
    }

    public function testValidasiDanProsesUnggahFailsWhenSizeTooLarge()
    {
        $dummyFile = [
            'name' => 'foto_lapangan_hd.png',
            'tmp_name' => '/tmp/phpYg83x',
            'size' => 3000000, // 3 MB (Melebihi batas 2 MB)
            'error' => 0
        ];

        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Ukuran file terlalu besar. Maksimal adalah 2 MB.");

        $this->uploadService->validasiDanProsesUnggah($dummyFile);
    }

    public function testValidasiDanProsesUnggahFailsForDisallowedExtension()
    {
        $dummyFile = [
            'name' => 'dokumen_palsu.pdf', // PDF dilarang di modul dokumentasi foto harian
            'tmp_name' => '/tmp/phpYg83x',
            'size' => 400000,
            'error' => 0
        ];

        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Ekstensi file tidak diizinkan. Hanya menerima JPG, JPEG, dan PNG.");

        $this->uploadService->validasiDanProsesUnggah($dummyFile);
    }

    public function testValidasiDanProsesUnggahFailsWhenFileErrorOccurred()
    {
        $dummyFile = [
            'name' => 'foto_rusak.png',
            'tmp_name' => '',
            'size' => 0,
            'error' => 3 // UPLOAD_ERR_PARTIAL (File terunggah sebagian)
        ];

        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Gagal mengunggah file atau file rusak.");

        $this->uploadService->validasiDanProsesUnggah($dummyFile);
    }
}