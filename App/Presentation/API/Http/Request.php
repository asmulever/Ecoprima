<?php

declare(strict_types=1);

namespace App\Presentation\API\Http;

class Request
{
    private array $attributes = [];

    public function __construct(
        private string $method,
        private string $path,
        private array $queryParams,
        private array $headers,
        private array $body
    ) {
    }

    public static function fromGlobals(): self
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $pathInfo = $_SERVER['PATH_INFO'] ?? null;
        $path = $pathInfo ?: parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';

        $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
        if ($pathInfo === null && $scriptName && str_starts_with($path, $scriptName)) {
            $path = substr($path, strlen($scriptName));
        } elseif ($scriptName) {
            $scriptDir = rtrim(str_replace('\\', '/', dirname($scriptName)), '/');
            if ($scriptDir && str_starts_with($path, $scriptDir)) {
                $path = substr($path, strlen($scriptDir));
            }
        }

        if ($path === '' || $path === false) {
            $path = '/';
        }

        $headers = function_exists('getallheaders') ? getallheaders() : [];
        $bodyContent = file_get_contents('php://input') ?: '';
        $body = json_decode($bodyContent, true) ?? [];

        return new self($method, $path, $_GET, $headers, $body);
    }

    public function getMethod(): string
    {
        return strtoupper($this->method);
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getBody(): array
    {
        return $this->body;
    }

    public function getHeader(string $name): ?string
    {
        $normalized = strtolower($name);
        foreach ($this->headers as $key => $value) {
            if (strtolower($key) === $normalized) {
                return $value;
            }
        }

        return null;
    }

    public function setAttribute(string $key, $value): void
    {
        $this->attributes[$key] = $value;
    }

    public function getAttribute(string $key)
    {
        return $this->attributes[$key] ?? null;
    }
}
