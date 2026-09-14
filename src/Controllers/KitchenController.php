<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Services\AuthService;
use App\Models\Order;

class KitchenController
{
    public function index(Request $request, Response $response): Response
    {
        if (!AuthService::checkSession('kitchen')) {
            return $response->withHeader('Location', '/login?role=kitchen')->withStatus(302);
        }

        $orders = Order::getKitchenOrders();

        ob_start();
        include __DIR__ . '/../Views/kitchen.php';
        $html = ob_get_clean();

        $response->getBody()->write($html);
        return $response;
    }

    public function updateOrderStatus(Request $request, Response $response, array $args): Response
    {
        if (!AuthService::checkSession('kitchen')) {
            $response->getBody()->write(json_encode(['success' => false, 'error' => 'Unauthorized']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }

        $orderId = (int)$args['id'];
        $body = $request->getParsedBody() ?? json_decode($request->getBody()->getContents(), true);
        $newStatus = $body['status'] ?? '';

        if (!in_array($newStatus, ['accepted', 'ready'])) {
            $response->getBody()->write(json_encode(['success' => false, 'error' => 'Invalid status transition']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        $success = Order::updateStatus($orderId, $newStatus);

        $response->getBody()->write(json_encode([
            'success' => $success,
            'order_id' => $orderId,
            'status' => $newStatus,
            'message' => "Order marked as {$newStatus}"
        ]));
        return $response->withHeader('Content-Type', 'application/json');
    }
}
