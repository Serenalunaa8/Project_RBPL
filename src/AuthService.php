<?php
namespace App;
use PDO;

class AuthService {
    private PDO $pdo;
    public function __construct(PDO $pdo) { $this->pdo = $pdo; }

    public function login(string $username, string $password): ?array {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$user) return null;
        if ($password !== $user['password']) return null;
        return $user;
    }

    public function getRedirectByRole(string $role): ?string {
        $routes = [
            'kontraktor'  => 'kontraktor/dashboard.php',
            'pengawas'    => 'pengawas_lapangan/pengawas_lapangan.php',
            'koordinator' => 'koordinator_pengawas/koordinator_pengawas.php',
            'teamleader'  => 'team_leader/teamleader.php',
        ];
        return $routes[$role] ?? null;
    }

    public function isValidRole(string $role): bool {
        return in_array($role, ['kontraktor', 'pengawas', 'koordinator', 'teamleader']);
    }
}