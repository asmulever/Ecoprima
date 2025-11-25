<?php

declare(strict_types=1);

namespace App\Presentation\API\Controllers;

use App\Application\DTO\Auth\LoginRequestDTO;
use App\Application\UseCase\Auth\AuthenticateUserUseCase;
use App\Presentation\API\Http\Request;
use App\Presentation\API\Http\Response;
use RuntimeException;

class AuthController
{
    public function __construct(private AuthenticateUserUseCase $authenticateUser)
    {
    }

    public function login(Request $request): Response
    {
        $data = $request->getBody();
        if (empty($data['email']) || empty($data['password'])) {
            return new Response(['error' => 'Email y contraseña son requeridos'], 422);
        }

        try {
            $dto = new LoginRequestDTO($data['email'], $data['password']);
            $result = $this->authenticateUser->execute($dto);
        } catch (RuntimeException $e) {
            return new Response(['error' => $e->getMessage()], 401);
        }

        return new Response($result->getPayload());
    }

    public function me(Request $request): Response
    {
        $user = $request->getAttribute('auth_user');
        return new Response(['user' => $user]);
    }
}
