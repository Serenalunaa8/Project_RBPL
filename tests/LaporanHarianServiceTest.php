<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use App\LaporanHarianService;
use Exception;

class FakeLaporanStmt {
    public function bind_param(...$args) { return true; }
    public function execute() { return true; }
    public function close() { return true; }
}

class FakeLaporanMysqli {
    public function prepare($query) {
        return new FakeLaporanStmt();
    }
}

class LaporanHarianServiceTest extends TestCase
{
    protected $fakeDb;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fakeDb = new FakeLaporanMysqli();
    }

    public function testCatatLaporanSucceedsWithValidData()
    {
        $dataValid = [
            'form_id' => 11,
            'tanggal' => '2026-06-12',
            'progres' => 55, // 55%
            'cuaca' => 'Cerah Berawan',
            'foto' => 'laporan_11_foto.jpg'
        ];

        $service = new LaporanHarianService($this->fakeDb);
        $result = $service->catatLaporan($dataValid);

        $this->assertTrue($result['success']);
        $this->assertEquals('Laporan harian berhasil dicatat.', $result['message']);
    }

    public function testCatatLaporanFailsWhenProgresOverOneHundred()
    {
        $dataInvalid = [
            'form_id' => 11,
            'tanggal' => '2026-06-12',
            'progres' => 120, // Tidak valid karena di atas 100%
            'cuaca' => 'Hujan Gerimis',
            'foto' => 'laporan_11_foto.jpg'
        ];

        $service = new LaporanHarianService($this->fakeDb);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Persentase progres harus berada di antara 0% hingga 100%.");

        $service->catatLaporan($dataInvalid);
    }

    public function testCatatLaporanFailsWhenProgresIsNegative()
    {
        $dataInvalid = [
            'form_id' => 11,
            'tanggal' => '2026-06-12',
            'progres' => -5, // Tidak valid karena negatif
            'cuaca' => 'Cerah',
            'foto' => 'laporan_11_foto.jpg'
        ];

        $service = new LaporanHarianService($this->fakeDb);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Persentase progres harus berada di antara 0% hingga 100%.");

        $service->catatLaporan($dataInvalid);
    }

    public function testCatatLaporanFailsWhenDataMissing()
    {
        $dataMissing = [
            'form_id' => 11,
            'progres' => 50,
            'cuaca' => '', // Kosong!
            'foto' => 'foto.jpg'
        ];

        $service = new LaporanHarianService($this->fakeDb);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Data laporan harian tidak lengkap.");

        $service->catatLaporan($dataMissing);
    }
}