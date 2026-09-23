<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Services\AuthService;
use App\Models\Order;
use App\Models\Table;

class WaiterController
{
    public function index(Request $request, Response $response): Response
    {
        if (!AuthService::checkSession('waiter')) {
            return $response->withHeader('Location', '/login?role=waiter')->withStatus(302);
        }

        $tables = Order::getWaiterOverview();

        ob_start();
        include __DIR__ . '/../Views/waiter.php';
        $html = ob_get_clean();

        $response->getBody()->write($html);
        return $response;
    }

    public function markServed(Request $request, Response $response, array $args): Response
    {
        if (!AuthService::checkSession('waiter')) {
            $response->getBody()->write(json_encode(['success' => false, 'error' => 'Unauthorized']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }

        $orderId = (int)$args['id'];
        $success = Order::updateStatus($orderId, 'served');

        $response->getBody()->write(json_encode([
            'success' => $success,
            'order_id' => $orderId,
            'status' => 'served',
            'message' => 'Order marked as served'
        ]));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function closeBill(Request $request, Response $response, array $args): Response
    {
        if (!AuthService::checkSession('waiter')) {
            $response->getBody()->write(json_encode(['success' => false, 'error' => 'Unauthorized']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }

        $tableId = (int)$args['id'];

        // Enforce rule: Close bill is only allowed after kitchen marks dishes ready & waiter marks served
        $overview = Order::getWaiterOverview();
        $targetTable = null;
        foreach ($overview as $t) {
            if ($t['id'] === $tableId) {
                $targetTable = $t;
                break;
            }
        }

        if ($targetTable && empty($targetTable['can_close_bill'])) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'error' => 'Cannot close bill yet! Kitchen is preparing food or dishes are waiting to be marked served.'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        $body = $request->getParsedBody() ?? json_decode($request->getBody()->getContents(), true);
        $paymentMethod = $body['payment_method'] ?? 'cash';

        $success = Order::closeTableBill($tableId, $paymentMethod);

        $response->getBody()->write(json_encode([
            'success' => $success,
            'table_id' => $tableId,
            'status' => 'available',
            'message' => 'Bill closed successfully and table freed'
        ]));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function freeTable(Request $request, Response $response, array $args): Response
    {
        if (!AuthService::checkSession('waiter')) {
            $response->getBody()->write(json_encode(['success' => false, 'error' => 'Unauthorized']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }

        $tableId = (int)$args['id'];
        $success = Table::updateStatus($tableId, 'available');

        $response->getBody()->write(json_encode([
            'success' => $success,
            'table_id' => $tableId,
            'status' => 'available',
            'message' => 'Table marked as available'
        ]));
        return $response->withHeader('Content-Type', 'application/json');
    }
}
