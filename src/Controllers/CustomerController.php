<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\Table;
use App\Models\Dish;
use App\Models\Order;

class CustomerController
{
    public function showMenu(Request $request, Response $response, array $args): Response
    {
        $tableId = (int)$args['table_id'];
        $table = Table::find($tableId);

        if (!$table) {
            $response->getBody()->write("Invalid Table QR Code.");
            return $response->withStatus(404);
        }

        $categories = Dish::groupedByCategory(true);
        $activeOrder = Order::getActiveOrderForTable($tableId);

        ob_start();
        include __DIR__ . '/../Views/customer.php';
        $html = ob_get_clean();

        $response->getBody()->write($html);
        return $response;
    }

    public function placeOrder(Request $request, Response $response, array $args): Response
    {
        $tableId = (int)$args['table_id'];
        $table = Table::find($tableId);

        if (!$table) {
            $response->getBody()->write(json_encode(['success' => false, 'error' => 'Invalid table']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }

        $parsedBody = $request->getParsedBody() ?? json_decode($request->getBody()->getContents(), true);
        $items = $parsedBody['items'] ?? [];

        if (empty($items)) {
            $response->getBody()->write(json_encode(['success' => false, 'error' => 'Cart is empty']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        try {
            $orderId = Order::createOrder($tableId, $items);
            $response->getBody()->write(json_encode([
                'success' => true,
                'order_id' => $orderId,
                'message' => 'Order placed successfully!'
            ]));
            return $response->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['success' => false, 'error' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }
}
