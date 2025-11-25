<?php

declare(strict_types=1);

namespace App\Presentation\API\Middleware;

use App\Infrastructure\Security\JwtService;
use App\Presentation\API\Http\Request;
use App\Presentation\API\Http\Response;
use RuntimeException;

class AuthMiddleware
{
    public function __construct(private JwtService $jwtService)
    {
    }

    public function handle(Request $request, callable $next): Response
    {
        $header = $request->getHeader('Authorization');
        if (!$header || stripos($header, 'Bearer ') !== 0) {
            return new Response(['error' => 'Token requerido'], 401);
        }

        $token = trim(substr($header, 7));

        try {
            $payload = $this->jwtService->validate($token);
        } catch (RuntimeException $e) {
            return new Response(['error' => $e->getMessage()], 401);
        }

        $request->setAttribute('auth_user', $payload);

        return $next($request);
    }
}
