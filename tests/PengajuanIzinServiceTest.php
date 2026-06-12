<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use App\PengajuanIzinService;
use Exception;

// 1. Objek statement tiruan murni tanpa ikatan engine PHP
class PureFakeStmt {
    public function bind_param(...$args) { return true; }
    public function execute() { return true; }
    public function close() { return true; }
}

// 2. Objek koneksi database tiruan murni
class PureFakeMysqli {
    public $insert_id = 12;
    public function prepare($query) {
        return new PureFakeStmt();
    }
}

class PengajuanIzinServiceTest extends TestCase
{
    public function testSimpanDanNotifikasiSucceedsWithValidData()
    {
        $dataValid = [
            'kontraktor_id' => 1,
            'jenis_pekerjaan' => 'Pekerjaan Persiapan',
            'volume' => '50 m²',
            'lokasi' => 'Lantai 1'
        ];

        $fakeDb = new PureFakeMysqli();
        $service = new PengajuanIzinService($fakeDb);
        $result = $service->simpanDanNotifikasi($dataValid);

        // Bukti Pengujian (Assertions)
        $this->assertTrue($result['success']);
        $this->assertEquals(12, $result['id_pengajuan']);
        $this->assertTrue($result['notifikasi_terkirim']);
    }

    public function testSimpanDanNotifikasiFailsWhenDataIncomplete()
    {
        $dataInvalid = [
            'kontraktor_id' => 1,
            'jenis_pekerjaan' => '', 
            'lokasi' => 'Lantai 1'
        ];

        $fakeDb = new PureFakeMysqli();
        $service = new PengajuanIzinService($fakeDb);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Data pengajuan tidak lengkap");

        $service->simpanDanNotifikasi($dataInvalid);
    }
}