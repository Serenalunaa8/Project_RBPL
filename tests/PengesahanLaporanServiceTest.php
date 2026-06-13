<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use App\PengesahanLaporanService;
use Exception;

class FakePengesahanStmt {
    public function bind_param(...$args) { return true; }
    public function execute() { return true; }
    public function close() { return true; }
}

class FakePengesahanMysqli {
    public function prepare($query) {
        return new FakePengesahanStmt();
    }
}

class PengesahanLaporanServiceTest extends TestCase
{
    protected $fakeDb;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fakeDb = new FakePengesahanMysqli();
    }

    // --- UJI PENYIMPANAN ---
    public function testSimpanLaporanHarianSucceedsWithValidData()
    {
        $dataValid = [
            'form_id' => 11,
            'tanggal' => '2026-06-12',
            'progres' => 75,
            'cuaca' => 'Cerah',
            'foto' => 'bukti_paving.png'
        ];

        $service = new PengesahanLaporanService($this->fakeDb);
        $result = $service->simpanLaporanHarian($dataValid);

        $this->assertTrue($result['success']);
        $this->assertEquals('Laporan harian berhasil disimpan.', $result['message']);
    }

    public function testSimpanLaporanHarianFailsWhenDataMissing()
    {
        $dataMissing = [
            'form_id' => 11,
            'progres' => 75,
            'cuaca' => '', // Gagal karena kosong
            'foto' => 'bukti_paving.png'
        ];

        $service = new PengesahanLaporanService($this->fakeDb);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Data laporan harian tidak lengkap.");

        $service->simpanLaporanHarian($dataMissing);
    }

    // --- UJI PENGESAHAN ---
    public function testSahkanLaporanHarianApproveSucceeds()
    {
        $service = new PengesahanLaporanService($this->fakeDb);
        $result = $service->sahkanLaporanHarian(1, 'Disahkan', 'Pekerjaan rapi sesuai spek');

        $this->assertTrue($result['success']);
        $this->assertEquals('Disahkan', $result['status_akhir']);
    }

    public function testSahkanLaporanHarianRejectSucceeds()
    {
        $service = new PengesahanLaporanService($this->fakeDb);
        $result = $service->sahkanLaporanHarian(1, 'Ditolak', 'Foto tidak sesuai kondisi lapangan');

        $this->assertTrue($result['success']);
        $this->assertEquals('Ditolak', $result['status_akhir']);
    }

    public function testSahkanLaporanHarianFailsWithInvalidStatus()
    {
        $service = new PengesahanLaporanService($this->fakeDb);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Status pengesahan tidak valid.");

        // Status ngawur 'OkeSip' harusnya memicu error exception
        $service->sahkanLaporanHarian(1, 'OkeSip', 'Catatan');
    }
}