<?php

declare(strict_types=1);

namespace Tests;

use App\IzinPekerjaanService;
use PDO;
use PDOStatement;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;

#[AllowMockObjectsWithoutExpectations]
class IzinPekerjaanServiceTest extends TestCase
{
    private function validData(): array
    {
        return [
            'jenis_pekerjaan'  => 'Struktur',
            'material'         => 'Beton',
            'volume'           => '10',
            'satuan'           => 'm³',
            'lokasi'           => 'Lantai 1',
            'metode_kerja'     => 'Manual',
            'tanggal_mulai'    => '2026-06-15',
            'tanggal_selesai'  => '2026-06-20',
            'catatan'          => 'Pekerjaan tahap awal',
        ];
    }

    private function makeService(?PDOStatement $stmt = null): IzinPekerjaanService
    {
        $pdo = $this->createMock(PDO::class);

        if ($stmt !== null) {
            $pdo->method('prepare')->willReturn($stmt);
        }

        return new IzinPekerjaanService($pdo);
    }

    // ---------- VALIDASI ----------

    public function testValidateReturnsNoErrorsForValidData(): void
    {
        $service = $this->makeService();

        $errors = $service->validate($this->validData());

        $this->assertSame([], $errors);
    }

    #[DataProvider('requiredFieldProvider')]
    public function testValidateFailsWhenRequiredFieldMissing(string $field): void
    {
        $service = $this->makeService();

        $data = $this->validData();
        unset($data[$field]);

        $errors = $service->validate($data);

        $this->assertNotEmpty($errors);
    }

    public static function requiredFieldProvider(): array
    {
        return [
            'jenis_pekerjaan'  => ['jenis_pekerjaan'],
            'material'         => ['material'],
            'volume'           => ['volume'],
            'satuan'           => ['satuan'],
            'lokasi'           => ['lokasi'],
            'metode_kerja'     => ['metode_kerja'],
            'tanggal_mulai'    => ['tanggal_mulai'],
            'tanggal_selesai'  => ['tanggal_selesai'],
        ];
    }

    public function testValidateFailsWhenVolumeIsNotNumeric(): void
    {
        $service = $this->makeService();

        $data = $this->validData();
        $data['volume'] = 'sepuluh';

        $errors = $service->validate($data);

        $this->assertContains('Volume harus berupa angka lebih dari 0.', $errors);
    }

    public function testValidateFailsWhenVolumeIsZeroOrNegative(): void
    {
        $service = $this->makeService();

        $data = $this->validData();
        $data['volume'] = '0';

        $errors = $service->validate($data);

        $this->assertContains('Volume harus berupa angka lebih dari 0.', $errors);
    }

    public function testValidateFailsWhenTanggalSelesaiBeforeTanggalMulai(): void
    {
        $service = $this->makeService();

        $data = $this->validData();
        $data['tanggal_mulai']   = '2026-06-20';
        $data['tanggal_selesai'] = '2026-06-15';

        $errors = $service->validate($data);

        $this->assertContains(
            'Tanggal selesai tidak boleh sebelum tanggal mulai.',
            $errors
        );
    }

    public function testValidatePassesWhenTanggalSelesaiSameAsTanggalMulai(): void
    {
        $service = $this->makeService();

        $data = $this->validData();
        $data['tanggal_mulai']   = '2026-06-15';
        $data['tanggal_selesai'] = '2026-06-15';

        $errors = $service->validate($data);

        $this->assertSame([], $errors);
    }

    // ---------- VALIDASI FILE ----------

    #[DataProvider('validFileExtensionProvider')]
    public function testIsValidFileExtensionReturnsTrueForAllowedTypes(string $fileName): void
    {
        $service = $this->makeService();

        $this->assertTrue($service->isValidFileExtension($fileName));
    }

    public static function validFileExtensionProvider(): array
    {
        return [
            'pdf'  => ['dokumen.pdf'],
            'doc'  => ['dokumen.doc'],
            'docx' => ['dokumen.docx'],
            'jpg'  => ['foto.jpg'],
            'png'  => ['foto.png'],
        ];
    }

    public function testIsValidFileExtensionReturnsFalseForDisallowedType(): void
    {
        $service = $this->makeService();

        $this->assertFalse($service->isValidFileExtension('virus.exe'));
    }

    public function testGenerateFileNameKeepsOriginalExtension(): void
    {
        $service = $this->makeService();

        $generated = $service->generateFileName('laporan.PDF');

        $this->assertStringEndsWith('.pdf', $generated);
    }

    // ---------- CREATE IZIN ----------

    public function testCreateIzinFailsWhenDataInvalid(): void
    {
        $service = $this->makeService();

        $data = $this->validData();
        unset($data['jenis_pekerjaan']);

        $result = $service->createIzin($data, 1);

        $this->assertFalse($result['success']);
        $this->assertNotEmpty($result['errors']);
        $this->assertNull($result['id']);
    }

    public function testCreateIzinSucceedsWithValidData(): void
    {
        $stmt = $this->createMock(PDOStatement::class);
        $stmt->method('execute')->willReturn(true);

        $pdo = $this->createMock(PDO::class);
        $pdo->method('prepare')->willReturn($stmt);
        $pdo->method('lastInsertId')->willReturn('5');

        $service = new IzinPekerjaanService($pdo);

        $result = $service->createIzin($this->validData(), 1, '1234567890_abc.pdf');

        $this->assertTrue($result['success']);
        $this->assertSame([], $result['errors']);
        $this->assertSame(5, $result['id']);
    }

    // ---------- APPROVE / REJECT ----------

    public function testApproveIzinReturnsTrueWhenRowAffected(): void
    {
        $stmt = $this->createMock(PDOStatement::class);
        $stmt->method('execute')->willReturn(true);
        $stmt->method('rowCount')->willReturn(1);

        $service = $this->makeService($stmt);

        $this->assertTrue($service->approveIzin(1));
    }

    public function testRejectIzinReturnsTrueWhenRowAffected(): void
    {
        $stmt = $this->createMock(PDOStatement::class);
        $stmt->method('execute')->willReturn(true);
        $stmt->method('rowCount')->willReturn(1);

        $service = $this->makeService($stmt);

        $this->assertTrue($service->rejectIzin(2));
    }

    public function testApproveIzinReturnsFalseWhenNoRowAffected(): void
    {
        $stmt = $this->createMock(PDOStatement::class);
        $stmt->method('execute')->willReturn(true);
        $stmt->method('rowCount')->willReturn(0);

        $service = $this->makeService($stmt);

        $this->assertFalse($service->approveIzin(999));
    }

    // ---------- GET IZIN BY ID ----------

    public function testGetIzinByIdReturnsArrayWhenFound(): void
    {
        $izinRow = [
            'id'              => 1,
            'jenis_pekerjaan' => 'Struktur',
            'status'          => IzinPekerjaanService::STATUS_MENUNGGU_REVIEW,
        ];

        $stmt = $this->createMock(PDOStatement::class);
        $stmt->method('execute')->willReturn(true);
        $stmt->method('fetch')->willReturn($izinRow);

        $service = $this->makeService($stmt);

        $result = $service->getIzinById(1);

        $this->assertIsArray($result);
        $this->assertSame('Struktur', $result['jenis_pekerjaan']);
    }

    public function testGetIzinByIdReturnsNullWhenNotFound(): void
    {
        $stmt = $this->createMock(PDOStatement::class);
        $stmt->method('execute')->willReturn(true);
        $stmt->method('fetch')->willReturn(false);

        $service = $this->makeService($stmt);

        $this->assertNull($service->getIzinById(999));
    }
}