<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use App\EvaluasiBulananService;
use Exception;

class FakeEvaluasiStmt {
    public function bind_param(...$args) { return true; }
    public function execute() { return true; }
    public function close() { return true; }
}

class FakeEvaluasiMysqli {
    public function prepare($query) {
        return new FakeEvaluasiStmt();
    }
}

class EvaluasiBulananServiceTest extends TestCase
{
    protected $fakeDb;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fakeDb = new FakeEvaluasiMysqli();
    }

    // Skenario 1: Realisasi memenuhi target (Deviasi Positif / Nol)
    public function testEvaluasiBulananSucceedsWithOnTimeStatus()
    {
        $data = [
            'form_id' => 11,
            'bulan_ke' => 1,
            'target_progres' => 25.0,
            'realisasi_progres' => 27.5, // Lebih tinggi dari target
            'catatan_evaluasi' => 'Performa pengerjaan sangat baik'
        ];

        $service = new EvaluasiBulananService($this->fakeDb);
        $result = $service->evaluasiProgresBulanan($data);

        $this->assertTrue($result['success']);
        $this->assertEquals(2.5, $result['deviasi']);
        $this->assertEquals('Tepat Waktu / Melebihi Target', $result['status_performa']);
    }

    // Skenario 2: Realisasi sedikit di bawah target (Keterlambatan Ringan, deviasi antara 0 s/d -5)
    public function testEvaluasiBulananSucceedsWithDelayedStatus()
    {
        $data = [
            'form_id' => 11,
            'bulan_ke' => 1,
            'target_progres' => 30.0,
            'realisasi_progres' => 28.0, // Deviasi -2.0%
            'catatan_evaluasi' => 'Kendala cuaca hujan ringan'
        ];

        $service = new EvaluasiBulananService($this->fakeDb);
        $result = $service->evaluasiProgresBulanan($data);

        $this->assertTrue($result['success']);
        $this->assertEquals(-2.0, $result['deviasi']);
        $this->assertEquals('Terlambat (Slightly Delayed)', $result['status_performa']);
    }

    // Skenario 3: Realisasi jauh di bawah target (Status Kritis, deviasi di bawah -5)
    public function testEvaluasiBulananSucceedsWithCriticalStatus()
    {
        $data = [
            'form_id' => 11,
            'bulan_ke 2' => 2,
            'target_progres' => 60.0,
            'realisasi_progres' => 52.0, // Deviasi -8.0% (Kritis)
            'catatan_evaluasi' => 'Kekurangan material semen di lapangan'
        ];

        // Memanipulasi key bulan_ke agar sesuai penamaan array validasi
        $data['bulan_ke'] = 2; 

        $service = new EvaluasiBulananService($this->fakeDb);
        $result = $service->evaluasiProgresBulanan($data);

        $this->assertTrue($result['success']);
        $this->assertEquals(-8.0, $result['deviasi']);
        $this->assertEquals('Kritis (Keterlambatan Signifikan)', $result['status_performa']);
    }

    // Skenario 4: Gagal validasi jika parameter ada yang kosong
    public function testEvaluasiBulananFailsWhenDataIncomplete()
    {
        $dataIncomplete = [
            'form_id' => 11,
            'bulan_ke' => 1,
            'target_progres' => 25.0,
            'realisasi_progres' => '' // Kosong!
        ];

        $service = new EvaluasiBulananService($this->fakeDb);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Data evaluasi laporan bulanan tidak lengkap.");

        $service->evaluasiProgresBulanan($dataIncomplete);
    }
}