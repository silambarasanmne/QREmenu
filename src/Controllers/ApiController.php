<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\Order;
use App\Models\Table;
use App\Services\AuthService;
use App\Services\RevenueService;

class ApiController
{
    public function getOwnerStats(Request $request, Response $response): Response
    {
        if (!AuthService::checkSession('owner')) {
            $response->getBody()->write(json_encode(['success' => false, 'error' => 'Unauthorized']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }

        $params = $request->getQueryParams();
        $startDate = $params['start_date'] ?? null;
        $endDate = $params['end_date'] ?? null;

        $stats = RevenueService::getDashboardStats($startDate, $endDate);

        $response->getBody()->write(json_encode(['success' => true, 'stats' => $stats]));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function sendOtp(Request $request, Response $response): Response
    {
        $body = $request->getParsedBody() ?? json_decode($request->getBody()->getContents(), true);
        $mobile = $body['mobile'] ?? '';

        $result = AuthService::sendCustomerOtp($mobile);
        $status = $result['success'] ? 200 : 400;

        $response->getBody()->write(json_encode($result));
        return $response->withHeader('Content-Type', 'application/json')->withStatus($status);
    }

    public function verifyOtp(Request $request, Response $response): Response
    {
        $body = $request->getParsedBody() ?? json_decode($request->getBody()->getContents(), true);
        $mobile = $body['mobile'] ?? '';
        $otp = $body['otp'] ?? '';
        $tableId = isset($body['table_id']) ? (int)$body['table_id'] : null;

        $result = AuthService::verifyCustomerOtp($mobile, $otp, $tableId);
        $status = $result['success'] ? 200 : 400;

        $response->getBody()->write(json_encode($result));
        return $response->withHeader('Content-Type', 'application/json')->withStatus($status);
    }

    public function getCustomerOrderStatus(Request $request, Response $response, array $args): Response
    {
        $tableId = (int)$args['table_id'];
        $activeOrder = Order::getActiveOrderForTable($tableId);

        $hasActiveOrder = !empty($activeOrder['has_active_order']);

        $data = [
            'has_active_order' => $hasActiveOrder,
            'order' => $activeOrder,
            'table_status' => $activeOrder['table_status'] ?? 'available',
            'bill_requested' => !empty($activeOrder['bill_requested'])
        ];

        $response->getBody()->write(json_encode($data));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function requestBill(Request $request, Response $response, array $args): Response
    {
        $tableId = (int)$args['table_id'];
        $success = Table::requestBill($tableId);

        $response->getBody()->write(json_encode([
            'success' => $success,
            'message' => 'Bill request intimate sent to waiter'
        ]));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function getKitchenOrders(Request $request, Response $response): Response
    {
        $orders = Order::getKitchenOrders();
        $response->getBody()->write(json_encode(['success' => true, 'orders' => $orders]));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function getWaiterStatus(Request $request, Response $response): Response
    {
        $tables = Order::getWaiterOverview();
        $readyOrdersCount = 0;

        foreach ($tables as $t) {
            if (isset($t['active_order']) && $t['active_order']['status'] === 'ready') {
                $readyOrdersCount++;
            }
        }

        $data = [
            'success' => true,
            'tables' => $tables,
            'ready_alerts' => $readyOrdersCount
        ];

        $response->getBody()->write(json_encode($data));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function login(Request $request, Response $response): Response
    {
        $body = $request->getParsedBody() ?? json_decode($request->getBody()->getContents(), true);
        $role = $body['role'] ?? '';
        $pin = $body['pin'] ?? '';

        if (AuthService::login($role, $pin)) {
            $redirectUrl = ($role === 'kitchen') ? '/kitchen' : (($role === 'waiter') ? '/waiter' : '/owner');
            $response->getBody()->write(json_encode(['success' => true, 'redirect' => $redirectUrl]));
            return $response->withHeader('Content-Type', 'application/json');
        }

        $response->getBody()->write(json_encode(['success' => false, 'error' => 'Invalid PIN or password']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
    }

    public function unlockAnalytics(Request $request, Response $response): Response
    {
        $body = $request->getParsedBody() ?? json_decode($request->getBody()->getContents(), true);
        $password = $body['password'] ?? '';

        if (AuthService::unlockAnalytics($password)) {
            $response->getBody()->write(json_encode(['success' => true, 'message' => 'Analytics unlocked successfully']));
            return $response->withHeader('Content-Type', 'application/json');
        }

        $response->getBody()->write(json_encode(['success' => false, 'error' => 'Invalid Owner Password']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
    }

    public function logout(Request $request, Response $response): Response
    {
        AuthService::logout();
        return $response->withHeader('Location', '/login')->withStatus(302);
    }
}
