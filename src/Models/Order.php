<?php

namespace App\Models;

use App\Database\DB;
use PDO;

class Order
{
    public static function find(int $id): ?array
    {
        $db = DB::getInstance();
        $stmt = $db->prepare("SELECT o.*, t.table_number FROM orders o JOIN tables t ON o.table_id = t.id WHERE o.id = ?");
        $stmt->execute([$id]);
        $order = $stmt->fetch();
        if (!$order) return null;

        $order['items'] = self::getItems($id);
        return $order;
    }

    public static function getItems(int $orderId): array
    {
        $db = DB::getInstance();
        $stmt = $db->prepare("
            SELECT oi.*, d.name as dish_name, d.category as dish_category, d.image_url
            FROM order_items oi
            JOIN dishes d ON oi.dish_id = d.id
            WHERE oi.order_id = ?
        ");
        $stmt->execute([$orderId]);
        return $stmt->fetchAll();
    }

    public static function createOrder(int $tableId, array $items): int
    {
        $db = DB::getInstance();
        $db->beginTransaction();

        try {
            $total = 0.00;
            $itemsData = [];

            foreach ($items as $item) {
                $dish = Dish::find((int)$item['dish_id']);
                if ($dish) {
                    $qty = max(1, (int)$item['quantity']);
                    $price = (float)$dish['price'];
                    $subtotal = $price * $qty;
                    $total += $subtotal;
                    $itemsData[] = [
                        'dish_id' => $dish['id'],
                        'quantity' => $qty,
                        'price_at_order' => $price,
                        'notes' => $item['notes'] ?? null
                    ];
                }
            }

            if (empty($itemsData)) {
                throw new \Exception("Cart is empty or items invalid.");
            }

            // Create Order
            $stmtOrder = $db->prepare("INSERT INTO orders (table_id, status, total_amount, created_at, updated_at) VALUES (?, 'placed', ?, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)");
            $stmtOrder->execute([$tableId, $total]);
            $orderId = (int)$db->lastInsertId();

            // Insert Order Items
            $stmtItem = $db->prepare("INSERT INTO order_items (order_id, dish_id, quantity, price_at_order, notes) VALUES (?, ?, ?, ?, ?)");
            foreach ($itemsData as $it) {
                $stmtItem->execute([
                    $orderId,
                    $it['dish_id'],
                    $it['quantity'],
                    $it['price_at_order'],
                    $it['notes']
                ]);
            }

            // Mark table occupied automatically
            Table::updateStatus($tableId, 'occupied');

            $db->commit();
            return $orderId;
        } catch (\Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    public static function updateStatus(int $orderId, string $status): bool
    {
        $db = DB::getInstance();
        $stmt = $db->prepare("UPDATE orders SET status = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
        return $stmt->execute([$status, $orderId]);
    }

    public static function getActiveOrderForTable(int $tableId): ?array
    {
        $db = DB::getInstance();

        // Fetch table info
        $stmtT = $db->prepare("SELECT * FROM tables WHERE id = ?");
        $stmtT->execute([$tableId]);
        $tableData = $stmtT->fetch();

        // Return latest active order that is not fully completed or billed
        $stmt = $db->prepare("SELECT * FROM orders WHERE table_id = ? AND status IN ('placed', 'accepted', 'ready', 'served') ORDER BY id DESC LIMIT 1");
        $stmt->execute([$tableId]);
        $order = $stmt->fetch();

        if (!$order) {
            return [
                'has_active_order' => false,
                'table_status' => $tableData['status'] ?? 'available',
                'bill_requested' => (int)($tableData['bill_requested'] ?? 0)
            ];
        }

        $order['has_active_order'] = true;
        $order['items'] = self::getItems($order['id']);
        $order['bill_requested'] = (int)($tableData['bill_requested'] ?? 0);
        $order['table_status'] = $tableData['status'] ?? 'occupied';

        // Calculate cumulative table total and fetch session items across all unbilled orders
        $stmtSessionOrders = $db->prepare("SELECT id, status, total_amount FROM orders WHERE table_id = ? AND status IN ('placed', 'accepted', 'ready', 'served') ORDER BY id ASC");
        $stmtSessionOrders->execute([$tableId]);
        $allSessionOrders = $stmtSessionOrders->fetchAll();

        $sessionTotal = 0.0;
        $allSessionItems = [];

        foreach ($allSessionOrders as $sOrd) {
            $sessionTotal += (float)$sOrd['total_amount'];
            $sItems = self::getItems($sOrd['id']);
            foreach ($sItems as $si) {
                $allSessionItems[] = $si;
            }
        }

        $order['session_total'] = $sessionTotal;
        $order['session_items'] = $allSessionItems;

        return $order;
    }

    public static function getKitchenOrders(): array
    {
        $db = DB::getInstance();
        $stmt = $db->query("
            SELECT o.*, t.table_number 
            FROM orders o 
            JOIN tables t ON o.table_id = t.id 
            WHERE o.status IN ('placed', 'accepted', 'ready') 
            ORDER BY 
                CASE o.status 
                    WHEN 'placed' THEN 1 
                    WHEN 'accepted' THEN 2 
                    WHEN 'ready' THEN 3 
                END ASC, 
                o.created_at ASC
        ");
        $orders = $stmt->fetchAll();

        foreach ($orders as &$ord) {
            $ord['items'] = self::getItems($ord['id']);
        }

        return $orders;
    }

    public static function getWaiterOverview(): array
    {
        $db = DB::getInstance();
        $tables = Table::all();

        foreach ($tables as &$table) {
            if ($table['status'] === 'occupied') {
                // Fetch all unbilled orders for this table session
                $stmt = $db->prepare("
                    SELECT o.* FROM orders o 
                    WHERE o.table_id = ? AND o.status IN ('placed', 'accepted', 'ready', 'served') 
                    ORDER BY o.id ASC
                ");
                $stmt->execute([$table['id']]);
                $sessionOrders = $stmt->fetchAll();

                $aggregatedItems = [];
                $sessionTotal = 0.0;
                $hasUnservedOrders = false;
                $hasReadyOrders = false;

                foreach ($sessionOrders as $ord) {
                    $sessionTotal += (float)$ord['total_amount'];
                    if (in_array($ord['status'], ['placed', 'accepted'])) {
                        $hasUnservedOrders = true;
                    }
                    if ($ord['status'] === 'ready') {
                        $hasReadyOrders = true;
                    }

                    $items = self::getItems($ord['id']);
                    foreach ($items as $it) {
                        $dishId = $it['dish_id'];
                        if (!isset($aggregatedItems[$dishId])) {
                            $aggregatedItems[$dishId] = [
                                'dish_id' => $dishId,
                                'dish_name' => $it['dish_name'],
                                'category' => $it['dish_category'],
                                'quantity' => 0,
                                'price_at_order' => (float)$it['price_at_order'],
                                'subtotal' => 0.0,
                                'notes' => []
                            ];
                        }
                        $aggregatedItems[$dishId]['quantity'] += (int)$it['quantity'];
                        $aggregatedItems[$dishId]['subtotal'] += ((float)$it['price_at_order'] * (int)$it['quantity']);
                        if (!empty($it['notes'])) {
                            $aggregatedItems[$dishId]['notes'][] = $it['notes'];
                        }
                    }
                }

                $table['session_orders'] = $sessionOrders;
                $table['session_items'] = array_values($aggregatedItems);
                $table['session_total'] = $sessionTotal;
                $table['can_close_bill'] = !$hasUnservedOrders && !$hasReadyOrders && count($sessionOrders) > 0;
                $table['has_ready_order'] = $hasReadyOrders;
                $table['bill_requested'] = (int)($table['bill_requested'] ?? 0);
            } else {
                $table['session_orders'] = [];
                $table['session_items'] = [];
                $table['session_total'] = 0.0;
                $table['can_close_bill'] = false;
                $table['has_ready_order'] = false;
                $table['bill_requested'] = 0;
            }
        }

        return $tables;
    }

    public static function closeTableBill(int $tableId, string $paymentMethod = 'cash'): bool
    {
        $db = DB::getInstance();
        $db->beginTransaction();

        try {
            // Set all active unbilled orders for table as paid
            $stmt = $db->prepare("UPDATE orders SET status = 'paid', updated_at = CURRENT_TIMESTAMP WHERE table_id = ? AND status IN ('placed', 'accepted', 'ready', 'served')");
            $stmt->execute([$tableId]);

            // Clear bill_requested flag and set table status to available
            $stmtT = $db->prepare("UPDATE tables SET status = 'available', bill_requested = 0 WHERE id = ?");
            $stmtT->execute([$tableId]);

            $db->commit();
            return true;
        } catch (\Exception $e) {
            $db->rollBack();
            return false;
        }
    }
}
