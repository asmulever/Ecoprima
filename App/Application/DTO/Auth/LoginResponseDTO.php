<?php

declare(strict_types=1);

namespace App\Application\DTO\Auth;

use App\Domain\Entity\User;

class LoginResponseDTO
{
    public function __construct(
        private string $token,
        private User $user
    ) {
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function getPayload(): array
    {
        return [
            'token' => $this->token,
            'user' => [
                'id' => $this->user->getId(),
                'email' => $this->user->getEmail(),
                'rol' => $this->user->getRol(),
            ],
        ];
    }
}
