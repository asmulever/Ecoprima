<?php

declare(strict_types=1);

namespace App\Presentation\API\Http;

class Response
{
    public function __construct(
        private array $body,
        private int $status = 200,
        private array $headers = ['Content-Type' => 'application/json']
    ) {
    }

    public function send(): void
    {
        http_response_code($this->status);
        foreach ($this->headers as $name => $value) {
            header("{$name}: {$value}");
        }
        echo json_encode($this->body);
    }
}
