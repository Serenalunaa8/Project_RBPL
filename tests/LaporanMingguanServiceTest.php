<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use App\LaporanMingguanService;
use Exception;

class FakeMingguanStmt {
    public function bind_param(...$args) { return true; }
    public function execute() { return true; }
    public function close() { return true; }
}

class FakeMingguanMysqli {
    public function prepare($query) {
        return new FakeMingguanStmt();
    }
}

class LaporanMingguanServiceTest extends TestCase
{
    protected $fakeDb;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fakeDb = new FakeMingguanMysqli();
    }

    public function testSusunLaporanMingguanSucceedsAndCalculatesAverageCorrectly()
    {
        $dataValid = [
            'form_id' => 11,
            'minggu_ke' => 2,
            'catatan_mingguan' => 'Pekerjaan struktur lantai 1 selesai tepat waktu'
        ];

        // Simulasi 4 laporan harian dalam minggu tersebut: 40%, 45%, 50%, 65%
        // Total = 200 / 4 = Rata-rata harus 50%
        $progresHarian = [40, 45, 50, 65];

        $service = new LaporanMingguanService($this->fakeDb);
        $result = $service->susunLaporanMingguan($dataValid, $progresHarian);

        $this->assertTrue($result['success']);
        $this->assertEquals(2, $result['minggu_ke']);
        $this->assertEquals(50, $result['progres_akumulasi']); // Memastikan kalkulasi rata-rata tepat
        $this->assertEquals('Laporan mingguan berhasil disusun.', $result['message']);
    }

    public function testSusunLaporanMingguanFailsWhenMingguKeIsZeroOrNegative()
    {
        $dataInvalid = [
            'form_id' => 11,
            'minggu_ke' => -1, // Tidak valid!
            'catatan_mingguan' => 'Catatan teknis'
        ];
        $progresHarian = [50, 60];

        $service = new LaporanMingguanService($this->fakeDb);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Nomor minggu harus lebih besar dari nol.");

        $service->susunLaporanMingguan($dataInvalid, $progresHarian);
    }

    public function testSusunLaporanMingguanFailsWhenProgresDataEmpty()
    {
        $dataValid = [
            'form_id' => 11,
            'minggu_ke' => 3,
            'catatan_mingguan' => 'Catatan'
        ];
        $progresHarian = []; // Gagal karena tidak ada data harian untuk dirangkum

        $service = new LaporanMingguanService($this->fakeDb);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Data penyusunan laporan mingguan tidak lengkap.");

        $service->susunLaporanMingguan($dataValid, $progresHarian);
    }
}