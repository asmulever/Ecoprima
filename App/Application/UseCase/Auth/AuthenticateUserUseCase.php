<?php

declare(strict_types=1);

namespace App\Application\UseCase\Auth;

use App\Application\DTO\Auth\LoginRequestDTO;
use App\Application\DTO\Auth\LoginResponseDTO;
use App\Domain\Repository\UserRepositoryInterface;
use App\Infrastructure\Security\JwtService;
use RuntimeException;

class AuthenticateUserUseCase
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private JwtService $jwtService
    ) {
    }

    public function execute(LoginRequestDTO $request): LoginResponseDTO
    {
        $user = $this->userRepository->findByEmail($request->getEmail());
        if (!$user || !$user->isActive()) {
            throw new RuntimeException('Credenciales inválidas');
        }

        if (!password_verify($request->getPassword(), $user->getPasswordHash())) {
            throw new RuntimeException('Credenciales inválidas');
        }

        $token = $this->jwtService->generate([
            'sub' => $user->getId(),
            'email' => $user->getEmail(),
            'rol' => $user->getRol(),
        ]);

        return new LoginResponseDTO($token, $user);
    }
}
