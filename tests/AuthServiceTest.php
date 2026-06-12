<?php

declare(strict_types=1);

namespace Tests;

use App\AuthService;
use PDO;
use PDOStatement;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;

#[AllowMockObjectsWithoutExpectations]
class AuthServiceTest extends TestCase
{
    private function makeServiceWithFetchResult(array|false $fetchResult): AuthService
    {
        $stmt = $this->createMock(PDOStatement::class);
        $stmt->method('execute')->willReturn(true);
        $stmt->method('fetch')->willReturn($fetchResult);

        $pdo = $this->createMock(PDO::class);
        $pdo->method('prepare')->willReturn($stmt);

        return new AuthService($pdo);
    }

    // ---------- LOGIN ----------

    public function testLoginSuccessReturnsUserArray(): void
    {
        $userRow = [
            'id'       => 1,
            'username' => 'budi',
            'password' => 'rahasia123',
            'role'     => 'kontraktor',
        ];

        $service = $this->makeServiceWithFetchResult($userRow);

        $result = $service->login('budi', 'rahasia123');

        $this->assertIsArray($result);
        $this->assertSame('budi', $result['username']);
        $this->assertSame('kontraktor', $result['role']);
    }

    public function testLoginFailsWhenUserNotFound(): void
    {
        $service = $this->makeServiceWithFetchResult(false);

        $result = $service->login('tidakada', 'apapun');

        $this->assertNull($result);
    }

    public function testLoginFailsWhenPasswordWrong(): void
    {
        $userRow = [
            'id'       => 2,
            'username' => 'ani',
            'password' => 'passwordbenar',
            'role'     => 'pengawas',
        ];

        $service = $this->makeServiceWithFetchResult($userRow);

        $result = $service->login('ani', 'passwordsalah');

        $this->assertNull($result);
    }

    // ---------- ROLE-BASED ACCESS ----------

    #[DataProvider('validRoleProvider')]
    public function testGetRedirectByRoleReturnsCorrectPath(string $role, string $expectedPath): void
    {
        $service = $this->makeServiceWithFetchResult(false);

        $this->assertSame($expectedPath, $service->getRedirectByRole($role));
    }

    public static function validRoleProvider(): array
    {
        return [
            'kontraktor'  => ['kontraktor', 'kontraktor/dashboard.php'],
            'pengawas'    => ['pengawas', 'pengawas_lapangan/pengawas_lapangan.php'],
            'koordinator' => ['koordinator', 'koordinator_pengawas/koordinator_pengawas.php'],
            'teamleader'  => ['teamleader', 'team_leader/teamleader.php'],
        ];
    }

    public function testGetRedirectByRoleReturnsNullForUnknownRole(): void
    {
        $service = $this->makeServiceWithFetchResult(false);

        $this->assertNull($service->getRedirectByRole('admin_super'));
    }

    #[DataProvider('validRoleNameProvider')]
    public function testIsValidRoleReturnsTrueForKnownRoles(string $role): void
    {
        $service = $this->makeServiceWithFetchResult(false);

        $this->assertTrue($service->isValidRole($role));
    }

    public static function validRoleNameProvider(): array
    {
        return [
            ['kontraktor'],
            ['pengawas'],
            ['koordinator'],
            ['teamleader'],
        ];
    }

    public function testIsValidRoleReturnsFalseForUnknownRole(): void
    {
        $service = $this->makeServiceWithFetchResult(false);

        $this->assertFalse($service->isValidRole('superadmin'));
        $this->assertFalse($service->isValidRole(''));
    }
}