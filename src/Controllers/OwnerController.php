<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Services\AuthService;
use App\Services\RevenueService;
use App\Services\QrCodeService;
use App\Models\Dish;
use App\Models\Table;

class OwnerController
{
    public function index(Request $request, Response $response): Response
    {
        if (!AuthService::checkSession('owner')) {
            return $response->withHeader('Location', '/login?role=owner')->withStatus(302);
        }

        $params = $request->getQueryParams();
        $startDate = $params['start_date'] ?? null;
        $endDate = $params['end_date'] ?? null;

        $stats = RevenueService::getDashboardStats($startDate, $endDate);
        $dishes = Dish::all(false);
        $tables = Table::all();

        // Generate QR Code data URIs for each table
        $serverParams = $request->getServerParams();
        $scheme = (!empty($serverParams['HTTPS']) && $serverParams['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $serverParams['HTTP_HOST'] ?? '127.0.0.1:8000';
        $baseUrl = "{$scheme}://{$host}";

        foreach ($tables as &$t) {
            $t['qr_url'] = "{$baseUrl}/table/{$t['id']}";
            $t['qr_svg'] = QrCodeService::generateSvgDataUri($t['qr_url']);
        }

        ob_start();
        include __DIR__ . '/../Views/owner.php';
        $html = ob_get_clean();

        $response->getBody()->write($html);
        return $response;
    }

    public function saveDish(Request $request, Response $response): Response
    {
        if (!AuthService::checkSession('owner')) {
            return $response->withHeader('Location', '/login?role=owner')->withStatus(302);
        }

        $body = $request->getParsedBody();
        $id = !empty($body['id']) ? (int)$body['id'] : null;

        $dishData = [
            'name' => trim($body['name'] ?? ''),
            'description' => trim($body['description'] ?? ''),
            'price' => (float)($body['price'] ?? 0),
            'category' => trim($body['category'] ?? 'General'),
            'image_url' => trim($body['image_url'] ?? ''),
            'is_available' => isset($body['is_available']) ? 1 : 0
        ];

        if ($id) {
            Dish::update($id, $dishData);
        } else {
            Dish::create($dishData);
        }

        return $response->withHeader('Location', '/owner#dishes')->withStatus(302);
    }

    public function toggleDishAvailability(Request $request, Response $response, array $args): Response
    {
        if (!AuthService::checkSession('owner')) {
            $response->getBody()->write(json_encode(['success' => false, 'error' => 'Unauthorized']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }

        $id = (int)$args['id'];
        Dish::toggleAvailability($id);

        $response->getBody()->write(json_encode(['success' => true]));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function deleteDish(Request $request, Response $response, array $args): Response
    {
        if (!AuthService::checkSession('owner')) {
            $response->getBody()->write(json_encode(['success' => false, 'error' => 'Unauthorized']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }

        $id = (int)$args['id'];
        Dish::delete($id);

        $response->getBody()->write(json_encode(['success' => true]));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function createTable(Request $request, Response $response): Response
    {
        if (!AuthService::checkSession('owner')) {
            return $response->withHeader('Location', '/login?role=owner')->withStatus(302);
        }

        $body = $request->getParsedBody();
        $tableNumber = trim($body['table_number'] ?? '');
        $count = isset($body['count']) ? max(1, (int)$body['count']) : 1;
        $prefix = trim($body['prefix'] ?? 'Table');

        if ($count > 1) {
            Table::createMultiple($count, $prefix);
        } elseif (!empty($tableNumber)) {
            if (str_contains($tableNumber, ',')) {
                $names = explode(',', $tableNumber);
                foreach ($names as $name) {
                    $clean = trim($name);
                    if (!empty($clean)) {
                        try { Table::create($clean); } catch (\Exception $e) {}
                    }
                }
            } else {
                Table::create($tableNumber);
            }
        }

        return $response->withHeader('Location', '/owner#tables')->withStatus(302);
    }

    public function deleteTable(Request $request, Response $response, array $args): Response
    {
        if (!AuthService::checkSession('owner')) {
            $response->getBody()->write(json_encode(['success' => false, 'error' => 'Unauthorized']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }

        $id = (int)$args['id'];
        Table::delete($id);

        $response->getBody()->write(json_encode(['success' => true]));
        return $response->withHeader('Content-Type', 'application/json');
    }
}
