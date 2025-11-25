<?php

declare(strict_types=1);

namespace App\Domain\Entity;

class User
{
    public function __construct(
        private int $id,
        private string $email,
        private string $passwordHash,
        private string $rol,
        private string $estado
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPasswordHash(): string
    {
        return $this->passwordHash;
    }

    public function getRol(): string
    {
        return $this->rol;
    }

    public function getEstado(): string
    {
        return $this->estado;
    }

    public function isActive(): bool
    {
        return $this->estado === 'activo';
    }
}
