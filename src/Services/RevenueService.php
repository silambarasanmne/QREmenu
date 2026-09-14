<?php

namespace App\Services;

use App\Database\DB;
use PDO;

class RevenueService
{
    public static function getDashboardStats(?string $startDate = null, ?string $endDate = null): array
    {
        $db = DB::getInstance();

        // Date conditions
        $whereSql = "WHERE status = 'served'";
        $params = [];

        if ($startDate && $endDate) {
            $whereSql .= " AND DATE(created_at) BETWEEN ? AND ?";
            $params[] = $startDate;
            $params[] = $endDate;
        }

        // Total Revenue & Orders in filtered range
        $stmt = $db->prepare("SELECT COUNT(*) as total_orders, COALESCE(SUM(total_amount), 0) as total_revenue, COALESCE(AVG(total_amount), 0) as avg_order_value FROM orders {$whereSql}");
        $stmt->execute($params);
        $rangeStats = $stmt->fetch();

        // Revenue Today
        $stmtToday = $db->query("SELECT COALESCE(SUM(total_amount), 0) as rev_today, COUNT(*) as orders_today FROM orders WHERE status = 'served' AND DATE(created_at) = CURRENT_DATE");
        $todayStats = $stmtToday->fetch();

        // Revenue This Week (Last 7 Days)
        $stmtWeek = $db->query("SELECT COALESCE(SUM(total_amount), 0) as rev_week, COUNT(*) as orders_week FROM orders WHERE status = 'served' AND created_at >= DATE('now', '-7 days')");
        $weekStats = $stmtWeek->fetch();

        // Best Selling Dishes
        $itemWhere = "WHERE o.status = 'served'";
        $itemParams = [];
        if ($startDate && $endDate) {
            $itemWhere .= " AND DATE(o.created_at) BETWEEN ? AND ?";
            $itemParams[] = $startDate;
            $itemParams[] = $endDate;
        }

        $stmtBest = $db->prepare("
            SELECT d.id, d.name, d.category, d.price, SUM(oi.quantity) as total_quantity, SUM(oi.quantity * oi.price_at_order) as total_revenue
            FROM order_items oi
            JOIN orders o ON oi.order_id = o.id
            JOIN dishes d ON oi.dish_id = d.id
            {$itemWhere}
            GROUP BY d.id, d.name, d.category, d.price
            ORDER BY total_quantity DESC, total_revenue DESC
            LIMIT 5
        ");
        $stmtBest->execute($itemParams);
        $bestSelling = $stmtBest->fetchAll();

        // Recent Orders List
        $stmtRecent = $db->prepare("
            SELECT o.*, t.table_number 
            FROM orders o 
            JOIN tables t ON o.table_id = t.id 
            ORDER BY o.id DESC 
            LIMIT 10
        ");
        $stmtRecent->execute();
        $recentOrders = $stmtRecent->fetchAll();

        return [
            'total_revenue' => (float)($rangeStats['total_revenue'] ?? 0),
            'total_orders' => (int)($rangeStats['total_orders'] ?? 0),
            'avg_order_value' => (float)($rangeStats['avg_order_value'] ?? 0),
            'revenue_today' => (float)($todayStats['rev_today'] ?? 0),
            'orders_today' => (int)($todayStats['orders_today'] ?? 0),
            'revenue_week' => (float)($weekStats['rev_week'] ?? 0),
            'orders_week' => (int)($weekStats['orders_week'] ?? 0),
            'best_selling' => $bestSelling,
            'recent_orders' => $recentOrders,
            'start_date' => $startDate,
            'end_date' => $endDate
        ];
    }
}
