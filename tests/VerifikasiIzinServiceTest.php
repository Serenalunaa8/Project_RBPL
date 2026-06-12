<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use App\VerifikasiIzinService;
use Exception;

class PureFakeVerifikasiStmt {
    public function bind_param(...$args) { return true; }
    public function execute() { return true; }
    public function close() { return true; }
}

class PureFakeVerifikasiMysqli {
    public function prepare($query) {
        return new PureFakeVerifikasiStmt();
    }
}

class VerifikasiIzinServiceTest extends TestCase
{
    protected $fakeDb;
    protected $dummyIzin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fakeDb = new PureFakeVerifikasiMysqli();
        $this->dummyIzin = [
            'kontraktor_id' => 5,
            'jenis_pekerjaan' => 'Pemasangan Paving Block'
        ];
    }

    public function testProsesVerifikasiApproveSucceeds()
    {
        $service = new VerifikasiIzinService($this->fakeDb);
        $result = $service->prosesVerifikasi(10, 'approve', 'Sesuai dengan spesifikasi', $this->dummyIzin);

        $this->assertTrue($result['success']);
        $this->assertEquals('Disetujui Pengawas', $result['status_baru']);
        $this->assertTrue($result['notifikasi_terkirim']);
    }

    public function testProsesVerifikasiRejectSucceeds()
    {
        $service = new VerifikasiIzinService($this->fakeDb);
        $result = $service->prosesVerifikasi(10, 'reject', 'Volume tidak sesuai berkas', $this->dummyIzin);

        $this->assertTrue($result['success']);
        $this->assertEquals('Ditolak', $result['status_baru']);
        $this->assertTrue($result['notifikasi_terkirim']);
    }

    public function testProsesVerifikasiRevisiSucceedsWithCatatan()
    {
        $service = new VerifikasiIzinService($this->fakeDb);
        $result = $service->prosesVerifikasi(10, 'revisi', 'Perbaiki metode kerja landasan', $this->dummyIzin);

        $this->assertTrue($result['success']);
        $this->assertEquals('Revisi', $result['status_baru']);
        $this->assertTrue($result['notifikasi_terkirim']);
    }

    public function testProsesVerifikasiRevisiFailsWhenCatatanEmpty()
    {
        $service = new VerifikasiIzinService($this->fakeDb);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Catatan teknis wajib diisi untuk permintaan revisi.");

        $service->prosesVerifikasi(10, 'revisi', '   ', $this->dummyIzin);
    }

    public function testProsesVerifikasiFailsWithInvalidAction()
    {
        $service = new VerifikasiIzinService($this->fakeDb);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Aksi verifikasi tidak valid.");

        $service->prosesVerifikasi(10, 'invalid_action', 'Catatan', $this->dummyIzin);
    }
}