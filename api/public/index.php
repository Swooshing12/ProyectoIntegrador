<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Tuupola\Middleware\CorsMiddleware;
use App\Controllers\AuthController;
use App\Controllers\HistorialController;
use App\Controllers\CitasController;
use App\Controllers\DoctoresApiController;
use App\Utils\ResponseUtil;

require __DIR__ . '/../../vendor/autoload.php';

// Configuración de BD
if (file_exists(__DIR__ . '/../config/database.php')) {
    require_once __DIR__ . '/../config/database.php';
}

// Autoloader para nuestras clases
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/../src/';
    
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    
    if (file_exists($file)) {
        require $file;
    }
});

$app = AppFactory::create();

// CONFIGURAR BASE PATH PARA SUBDIRECTORIOS
$app->setBasePath('/MenuDinamico/api');

// Middlewares
$app->addBodyParsingMiddleware();
$app->add(new CorsMiddleware([
    "origin" => ["*"],
    "methods" => ["GET", "POST", "PUT", "DELETE", "OPTIONS"],
    "headers.allow" => ["Content-Type", "Authorization", "X-Requested-With"],
    "credentials" => true,
]));
$app->addRoutingMiddleware();
$app->addErrorMiddleware(true, true, true);

// OPTIONS para CORS
$app->options('/{routes:.*}', function (Request $request, Response $response) {
    return $response;
});

// ===== RUTAS USANDO LOS CONTROLADORES QUE CREAMOS =====

// Ruta de prueba
$app->get('/test', function (Request $request, Response $response) {
    $data = [
        'success' => true,
        'message' => 'API MenuDinamico funcionando ✅',
        'timestamp' => date('Y-m-d H:i:s'),
        'estructura_funcionando' => [
            'controllers' => file_exists(__DIR__ . '/../src/Controllers/AuthController.php') ? '✅' : '❌',
            'validators' => file_exists(__DIR__ . '/../src/Validators/CedulaValidator.php') ? '✅' : '❌',
            'utils' => file_exists(__DIR__ . '/../src/Utils/ResponseUtil.php') ? '✅' : '❌'
        ]
    ];
    
    $response->getBody()->write(json_encode($data, JSON_PRETTY_PRINT));
    return $response->withHeader('Content-Type', 'application/json');
});

// PUNTOS 1-2: Autenticación (usando AuthController)
$app->post('/auth/login', [AuthController::class, 'login']);
$app->post('/auth/change-password', [AuthController::class, 'changePassword']);


$app->run();
?>