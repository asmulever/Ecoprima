<?php

declare(strict_types=1);

namespace App\Infrastructure\Security;

use RuntimeException;

class JwtService
{
    private string $secret;
    private int $ttl;

    public function __construct(string $secret, int $ttlSeconds = 3600)
    {
        $this->secret = $secret;
        $this->ttl = $ttlSeconds;
    }

    public function generate(array $payload, ?int $ttlSeconds = null): string
    {
        $header = ['alg' => 'HS256', 'typ' => 'JWT'];
        $ttl = $ttlSeconds ?? $this->ttl;

        $payload['iat'] = time();
        $payload['exp'] = $payload['iat'] + $ttl;

        $segments = [
            $this->base64UrlEncode(json_encode($header)),
            $this->base64UrlEncode(json_encode($payload)),
        ];

        $signature = hash_hmac('sha256', implode('.', $segments), $this->secret, true);
        $segments[] = $this->base64UrlEncode($signature);

        return implode('.', $segments);
    }

    public function validate(string $token): array
    {
        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            throw new RuntimeException('Token inválido');
        }

        [$headerB64, $payloadB64, $signatureB64] = $parts;
        $header = json_decode($this->base64UrlDecode($headerB64), true);
        $payload = json_decode($this->base64UrlDecode($payloadB64), true);

        if (!$header || !$payload || ($header['alg'] ?? '') !== 'HS256') {
            throw new RuntimeException('Token inválido');
        }

        $expected = $this->base64UrlEncode(
            hash_hmac('sha256', "{$headerB64}.{$payloadB64}", $this->secret, true)
        );

        if (!hash_equals($expected, $signatureB64)) {
            throw new RuntimeException('Firma inválida');
        }

        if (($payload['exp'] ?? 0) < time()) {
            throw new RuntimeException('Token expirado');
        }

        return $payload;
    }

    private function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private function base64UrlDecode(string $data): string
    {
        $remainder = strlen($data) % 4;
        if ($remainder) {
            $data .= str_repeat('=', 4 - $remainder);
        }
        return base64_decode(strtr($data, '-_', '+/'));
    }
}
