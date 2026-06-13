<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use App\RiwayatMingguanService;
use Exception;

// Fake Result Set untuk simulasi pembacaan database baris per baris
class FakeRiwayatResult {
    protected $data;
    protected $index = 0;

    public function __construct(array $data) {
        $this->data = $data;
    }

    public function fetch_assoc() {
        if (isset($this->data[$this->index])) {
            return $this->data[$this->index++];
        }
        return null;
    }
}

// Fake Statement khusus untuk riwayat laporan
class FakeRiwayatStmt {
    public function bind_param(...$args) { return true; }
    public function execute() { return true; }
    public function close() { return true; }
    
    public function get_result() {
        // Mengembalikan 2 baris data riwayat minggu ke-1 dan minggu ke-2
        return new FakeRiwayatResult([
            [
                'id_riwayat' => 1,
                'form_id' => 11,
                'minggu_ke' => 1,
                'akumulasi_progres' => 25.5,
                'tanggal_cetak' => '2026-06-05 16:00:00'
            ],
            [
                'id_riwayat' => 2,
                'form_id' => 11,
                'minggu_ke' => 2,
                'akumulasi_progres' => 50.0,
                'tanggal_cetak' => '2026-06-12 16:00:00'
            ]
        ]);
    }
}

class FakeRiwayatMysqli {
    public function prepare($query) {
        return new FakeRiwayatStmt();
    }
}

class RiwayatMingguanServiceTest extends TestCase
{
    protected $fakeDb;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fakeDb = new FakeRiwayatMysqli();
    }

    // --- TEST PENYIMPANAN ---
    public function testSimpanRiwayatSucceedsWithValidData()
    {
        $dataValid = [
            'form_id' => 11,
            'minggu_ke' => 3,
            'akumulasi_progres' => 75.2
        ];

        $service = new RiwayatMingguanService($this->fakeDb);
        $result = $service->simpanRiwayat($dataValid);

        $this->assertTrue($result['success']);
        $this->assertEquals('Riwayat laporan mingguan berhasil diarsipkan.', $result['message']);
    }

    public function testSimpanRiwayatFailsWhenDataIncomplete()
    {
        $dataInvalid = [
            'form_id' => 11,
            'minggu_ke' => '', // Kosong membuat validasi menolak
            'akumulasi_progres' => 75.2
        ];

        $service = new RiwayatMingguanService($this->fakeDb);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Data riwayat laporan mingguan tidak lengkap.");

        $service->simpanRiwayat($dataInvalid);
    }

    // --- TEST TAMPILAN RIWAYAT ---
    public function testGetRiwayatByFormIdReturnsArrayOfHistory()
    {
        $service = new RiwayatMingguanService($this->fakeDb);
        $result = $service->getRiwayatByFormId(11);

        // Memastikan data riwayat berhasil ditarik dan diformat ke array dashboard
        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        
        // Cek data minggu ke-1
        $this->assertEquals(1, $result[0]['minggu_ke']);
        $this->assertEquals(25.5, $result[0]['akumulasi_progres']);

        // Cek data minggu ke-2
        $this->assertEquals(2, $result[1]['minggu_ke']);
        $this->assertEquals(50.0, $result[1]['akumulasi_progres']);
    }
}