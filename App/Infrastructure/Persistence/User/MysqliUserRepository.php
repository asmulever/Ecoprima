<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\User;

use App\Domain\Entity\User;
use App\Domain\Repository\UserRepositoryInterface;
use mysqli;

class MysqliUserRepository implements UserRepositoryInterface
{
    public function __construct(private mysqli $conn)
    {
    }

    public function findByEmail(string $email): ?User
    {
        $stmt = $this->conn->prepare('SELECT id, email, password_hash, rol, estado FROM usuarios WHERE email=? LIMIT 1');
        if (!$stmt) {
            return null;
        }

        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();

        if (!$row) {
            return null;
        }

        return new User(
            (int) $row['id'],
            $row['email'],
            $row['password_hash'],
            $row['rol'],
            $row['estado']
        );
    }

    public function save(User $user): User
    {
        $stmt = $this->conn->prepare('INSERT INTO usuarios (email, password_hash, rol, estado) VALUES (?, ?, ?, ?)');
        if (!$stmt) {
            return $user;
        }

        $email = $user->getEmail();
        $hash = $user->getPasswordHash();
        $rol = $user->getRol();
        $estado = $user->getEstado();

        $stmt->bind_param('ssss', $email, $hash, $rol, $estado);
        $stmt->execute();
        $stmt->close();

        return new User((int) $this->conn->insert_id, $email, $hash, $rol, $estado);
    }

    public function updatePasswordAndStatus(int $id, string $hash, string $estado, string $rol): void
    {
        $stmt = $this->conn->prepare('UPDATE usuarios SET password_hash=?, estado=?, rol=? WHERE id=?');
        if (!$stmt) {
            return;
        }

        $stmt->bind_param('sssi', $hash, $estado, $rol, $id);
        $stmt->execute();
        $stmt->close();
    }
}
