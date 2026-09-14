<?php

use Slim\Factory\AppFactory;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require __DIR__ . '/../vendor/autoload.php';

// Serve static assets directly if using built-in PHP web server
if (php_sapi_name() === 'cli-server') {
    $file = __DIR__ . $_SERVER['REQUEST_URI'];
    if (is_file($file)) {
        return false;
    }
}

// Ensure session started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$app = AppFactory::create();

// Add Error Middleware
$app->addErrorMiddleware(true, true, true);

// Add Body Parsing Middleware
$app->addBodyParsingMiddleware();

// -------------------------------------------------------------------
// Routes Definition
// -------------------------------------------------------------------

// Home Redirect
$app->get('/', function (Request $request, Response $response) {
    return $response->withHeader('Location', '/table/1')->withStatus(302);
});

// Page 1: Customer Page
$app->get('/table/{table_id}', [\App\Controllers\CustomerController::class, 'showMenu']);
$app->post('/table/{table_id}/order', [\App\Controllers\CustomerController::class, 'placeOrder']);

// Customer OTP Auth API Routes
$app->post('/api/customer/send-otp', [\App\Controllers\ApiController::class, 'sendOtp']);
$app->post('/api/customer/verify-otp', [\App\Controllers\ApiController::class, 'verifyOtp']);

// Page 2: Kitchen Page
$app->get('/kitchen', [\App\Controllers\KitchenController::class, 'index']);
$app->post('/api/kitchen/orders/{id}/status', [\App\Controllers\KitchenController::class, 'updateOrderStatus']);

// Page 3: Waiter Page
$app->get('/waiter', [\App\Controllers\WaiterController::class, 'index']);
$app->post('/waiter/orders/{id}/serve', [\App\Controllers\WaiterController::class, 'markServed']);
$app->post('/waiter/tables/{id}/close-bill', [\App\Controllers\WaiterController::class, 'closeBill']);
$app->post('/waiter/tables/{id}/free', [\App\Controllers\WaiterController::class, 'freeTable']);

// Page 4: Owner Page
$app->get('/owner', [\App\Controllers\OwnerController::class, 'index']);
$app->post('/owner/dish/save', [\App\Controllers\OwnerController::class, 'saveDish']);
$app->post('/owner/dish/{id}/toggle', [\App\Controllers\OwnerController::class, 'toggleDishAvailability']);
$app->post('/owner/dish/{id}/delete', [\App\Controllers\OwnerController::class, 'deleteDish']);
$app->post('/owner/table/create', [\App\Controllers\OwnerController::class, 'createTable']);
$app->post('/owner/table/{id}/delete', [\App\Controllers\OwnerController::class, 'deleteTable']);

// Auth & Login
$app->get('/login', function (Request $request, Response $response) {
    ob_start();
    include __DIR__ . '/../src/Views/login.php';
    $html = ob_get_clean();
    $response->getBody()->write($html);
    return $response;
});
$app->post('/api/login', [\App\Controllers\ApiController::class, 'login']);
$app->get('/api/logout', [\App\Controllers\ApiController::class, 'logout']);

// Real-Time Polling APIs
$app->get('/api/table/{table_id}/status', [\App\Controllers\ApiController::class, 'getCustomerOrderStatus']);
$app->get('/api/kitchen/orders', [\App\Controllers\ApiController::class, 'getKitchenOrders']);
$app->get('/api/waiter/status', [\App\Controllers\ApiController::class, 'getWaiterStatus']);

$app->run();
