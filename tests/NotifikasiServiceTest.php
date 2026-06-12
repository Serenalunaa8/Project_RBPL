<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use App\NotifikasiService;
use Exception;

// Fake Result Set untuk menyimulasikan hasil data baris dari database
class FakeMysqliResult {
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

// Fake Statement khusus Notifikasi
class FakeNotifStmt {
    protected $query;
    public function __construct($query) {
        $this->query = $query;
    }
    public function bind_param(...$args) { return true; }
    public function execute() { return true; }
    public function close() { return true; }
    
    public function get_result() {
        // Simulasikan mengembalikan 2 data notifikasi dummy
        return new FakeMysqliResult([
            [
                'id' => 1,
                'user_id' => 5,
                'pesan' => "Status izin pekerjaan 'Pemasangan Paving' telah diperbarui menjadi 'Disetujui Pengawas'.",
                'form_id' => 10,
                'created_at' => '2026-06-12 10:00:00'
            ],
            [
                'id' => 2,
                'user_id' => 5,
                'pesan' => "Status izin pekerjaan 'Galian Selokan' telah diperbarui menjadi 'Revisi'.",
                'form_id' => 11,
                'created_at' => '2026-06-12 11:00:00'
            ]
        ]);
    }
}

// Fake Mysqli Utama
class FakeNotifMysqli {
    public function prepare($query) {
        return new FakeNotifStmt($query);
    }
}

class NotifikasiServiceTest extends TestCase
{
    protected $fakeDb;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fakeDb = new FakeNotifMysqli();
    }

    public function testGetNotifikasiByUserReturnsArrayOfData()
    {
        $service = new NotifikasiService($this->fakeDb);
        $result = $service->getNotifikasiByUser(5);

        // Assertions untuk memastikan data berhasil ditarik dan tampil
        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        $this->assertEquals(10, $result[0]['form_id']);
        $this->stringContains("Disetujui Pengawas", $result[0]['pesan']);
    }

    public function testKirimNotifikasiSucceedsWithValidData()
    {
        $service = new NotifikasiService($this->fakeDb);
        $success = $service->kirimNotifikasi(5, "Izin baru telah diajukan", 12);

        $this->assertTrue($success);
    }

    public function testKirimNotifikasiFailsWhenPesanEmpty()
    {
        $service = new NotifikasiService($this->fakeDb);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Pesan notifikasi tidak boleh kosong.");

        $service->kirimNotifikasi(5, "   ", 12);
    }
}