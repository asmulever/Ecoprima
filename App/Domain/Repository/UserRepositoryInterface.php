<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Entity\User;

interface UserRepositoryInterface
{
    public function findByEmail(string $email): ?User;

    public function save(User $user): User;

    public function updatePasswordAndStatus(int $id, string $hash, string $estado, string $rol): void;
}
