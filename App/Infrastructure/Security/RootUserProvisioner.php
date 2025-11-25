<?php

declare(strict_types=1);

namespace App\Infrastructure\Security;

use App\Infrastructure\Config\Config;
use mysqli;

class RootUserProvisioner
{
    public static function enforce(mysqli $conn): void
    {
        $email = Config::get('ROOT_EMAIL');
        $password = Config::get('ROOT_PASSWORD');
        $enabled = Config::bool('ENABLE_ROOT_USER', false);

        if (!$email || !$password) {
            return;
        }

        $stmt = $conn->prepare('SELECT id, estado FROM usuarios WHERE email=? LIMIT 1');
        if (!$stmt) {
            return;
        }

        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->bind_result($id, $estado);
        $exists = $stmt->fetch();
        $stmt->close();

        if ($enabled) {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            if (!$exists) {
                $insert = $conn->prepare("INSERT INTO usuarios (email, password_hash, rol, estado) VALUES (?, ?, 'admin', 'activo')");
                if ($insert) {
                    $insert->bind_param('ss', $email, $hash);
                    $insert->execute();
                    $insert->close();
                }
            } else {
                $update = $conn->prepare("UPDATE usuarios SET estado='activo', password_hash=?, rol='admin' WHERE id=?");
                if ($update) {
                    $update->bind_param('si', $hash, $id);
                    $update->execute();
                    $update->close();
                }
            }
        } elseif ($exists && $estado !== 'inactivo') {
            $deactivate = $conn->prepare('UPDATE usuarios SET estado=\'inactivo\' WHERE id=?');
            if ($deactivate) {
                $deactivate->bind_param('i', $id);
                $deactivate->execute();
                $deactivate->close();
            }
        }
    }
}
