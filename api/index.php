<?php

declare(strict_types=1);

require_once __DIR__ . '/../App/bootstrap.php';

use App\Application\UseCase\Auth\AuthenticateUserUseCase;
use App\Application\UseCase\Product\ListProductsUseCase;
use App\Infrastructure\Config\Config;
use App\Infrastructure\Database\ConnectionFactory;
use App\Infrastructure\Persistence\Product\MysqliProductRepository;
use App\Infrastructure\Persistence\User\MysqliUserRepository;
use App\Infrastructure\Security\JwtService;
use App\Infrastructure\Security\RootUserProvisioner;
use App\Presentation\API\Controllers\AuthController;
use App\Presentation\API\Controllers\ProductController;
use App\Presentation\API\Http\Request;
use App\Presentation\API\Middleware\AuthMiddleware;
use App\Presentation\API\Router;
use App\Presentation\API\Http\Response;

try {
    $mysqli = ConnectionFactory::get();
} catch (Throwable $e) {
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode(['error' => $e->getMessage()]);
    exit;
}

RootUserProvisioner::enforce($mysqli);

$jwtSecret = Config::get('JWT_SECRET', 'changeme');
$jwtTtl = (int) (Config::get('JWT_TTL', 3600));
$jwtService = new JwtService($jwtSecret, $jwtTtl);

$userRepository = new MysqliUserRepository($mysqli);
$productRepository = new MysqliProductRepository($mysqli);

$authController = new AuthController(new AuthenticateUserUseCase($userRepository, $jwtService));
$productController = new ProductController(new ListProductsUseCase($productRepository));
$authMiddleware = new AuthMiddleware($jwtService);

$router = new Router();
$router->add('GET', '/', fn($request) => new Response(['status' => 'ok']));
$router->add('GET', '/v1/docs', fn() => new Response(buildSwaggerSpec($jwtTtl)));
$router->add('POST', '/v1/auth/login', [$authController, 'login']);
$router->add('GET', '/v1/auth/me', [$authController, 'me'], [$authMiddleware]);
$router->add('GET', '/v1/products', [$productController, 'index'], [$authMiddleware]);

$request = Request::fromGlobals();
$response = $router->dispatch($request);
$response->send();

function buildSwaggerSpec(int $jwtTtl): array
{
    return [
        'openapi' => '3.0.3',
        'info' => [
            'title' => 'EcoPrima API',
            'version' => '1.0.0',
            'description' => "API REST stateless de EcoPrima. Usa las credenciales existentes en la tabla `usuarios` (por ejemplo root@example.com) para autenticarse mediante `/v1/auth/login` y luego invocar endpoints protegidos con el token JWT obtenido.",
        ],
        'servers' => [
            ['url' => '/api/index.php', 'description' => 'Front controller local'],
        ],
        'paths' => [
            '/' => [
                'get' => [
                    'summary' => 'Healthcheck',
                    'responses' => [
                        '200' => [
                            'description' => 'API operativa',
                            'content' => [
                                'application/json' => [
                                    'example' => ['status' => 'ok'],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            '/v1/auth/login' => [
                'post' => [
                    'summary' => 'Autenticar usuario',
                    'description' => 'Envía correo y contraseña; devuelve un JWT para usar en el header Authorization.',
                    'requestBody' => [
                        'required' => true,
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'email' => ['type' => 'string'],
                                        'password' => ['type' => 'string'],
                                    ],
                                    'required' => ['email', 'password'],
                                ],
                                'example' => [
                                    'email' => 'root@example.com',
                                    'password' => 'root',
                                ],
                            ],
                        ],
                    ],
                    'responses' => [
                        '200' => [
                            'description' => 'Login válido',
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'token' => ['type' => 'string'],
                                            'user' => [
                                                'type' => 'object',
                                                'properties' => [
                                                    'id' => ['type' => 'integer'],
                                                    'email' => ['type' => 'string'],
                                                    'rol' => ['type' => 'string'],
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        '401' => [
                            'description' => 'Credenciales inválidas',
                        ],
                    ],
                ],
            ],
            '/v1/auth/me' => [
                'get' => [
                    'summary' => 'Perfil del token actual',
                    'security' => [['bearerAuth' => []]],
                    'responses' => [
                        '200' => [
                            'description' => 'Datos básicos del usuario autenticado.',
                        ],
                        '401' => ['description' => 'Token ausente o inválido'],
                    ],
                ],
            ],
            '/v1/products' => [
                'get' => [
                    'summary' => 'Listado de productos activos',
                    'security' => [['bearerAuth' => []]],
                    'responses' => [
                        '200' => [
                            'description' => 'Listado en formato JSON',
                        ],
                        '401' => ['description' => 'Token ausente o inválido'],
                    ],
                ],
            ],
        ],
        'components' => [
            'securitySchemes' => [
                'bearerAuth' => [
                    'type' => 'http',
                    'scheme' => 'bearer',
                    'bearerFormat' => 'JWT',
                ],
            ],
            'schemas' => [
                'Token' => [
                    'type' => 'object',
                    'properties' => [
                        'token' => ['type' => 'string'],
                        'exp_in' => ['type' => 'integer', 'example' => $jwtTtl],
                    ],
                ],
            ],
        ],
    ];
}
