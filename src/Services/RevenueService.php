<?php

namespace App\Services;

use App\Database\DB;
use PDO;

class RevenueService
{
    public static function getDashboardStats(?string $startDate = null, ?string $endDate = null): array
    {
        $db = DB::getInstance();

        // Base Date Filter SQL
        $whereSql = "WHERE status IN ('served', 'paid')";
        $params = [];

        if ($startDate && $endDate) {
            $whereSql .= " AND DATE(created_at) BETWEEN ? AND ?";
            $params[] = $startDate;
            $params[] = $endDate;
        }

        // 1. Overall Summary Metrics
        $stmt = $db->prepare("
            SELECT 
                COUNT(*) as total_orders, 
                COALESCE(SUM(total_amount), 0) as total_revenue, 
                COALESCE(AVG(total_amount), 0) as avg_order_value 
            FROM orders {$whereSql}
        ");
        $stmt->execute($params);
        $rangeStats = $stmt->fetch();

        // 2. Today's Revenue & Orders
        $todayDate = date('Y-m-d');
        $stmtToday = $db->prepare("
            SELECT COALESCE(SUM(total_amount), 0) as rev_today, COUNT(*) as orders_today 
            FROM orders 
            WHERE status IN ('served', 'paid') AND (DATE(created_at) = ? OR DATE(created_at) = CURRENT_DATE)
        ");
        $stmtToday->execute([$todayDate]);
        $todayStats = $stmtToday->fetch();

        // 3. Weekly Revenue (Last 7 Days)
        $stmtWeek = $db->query("
            SELECT COALESCE(SUM(total_amount), 0) as rev_week, COUNT(*) as orders_week 
            FROM orders 
            WHERE status IN ('served', 'paid') AND created_at >= DATE('now', '-7 days')
        ");
        $weekStats = $stmtWeek->fetch();

        // 4. Daily Revenue Trend Data for Chart
        $trendWhere = "WHERE status IN ('served', 'paid')";
        $trendParams = [];
        if ($startDate && $endDate) {
            $trendWhere .= " AND DATE(created_at) BETWEEN ? AND ?";
            $trendParams[] = $startDate;
            $trendParams[] = $endDate;
        } else {
            // Default to last 14 days if no filter
            $trendWhere .= " AND created_at >= DATE('now', '-14 days')";
        }

        $stmtTrend = $db->prepare("
            SELECT 
                DATE(created_at) as order_date, 
                COUNT(*) as orders_count, 
                COALESCE(SUM(total_amount), 0) as daily_revenue
            FROM orders 
            {$trendWhere}
            GROUP BY DATE(created_at)
            ORDER BY order_date ASC
        ");
        $stmtTrend->execute($trendParams);
        $trendRows = $stmtTrend->fetchAll();

        $dailyTrend = [];
        foreach ($trendRows as $row) {
            $dailyTrend[] = [
                'date' => $row['order_date'],
                'label' => date('M j', strtotime($row['order_date'])),
                'orders' => (int)$row['orders_count'],
                'revenue' => (float)$row['daily_revenue']
            ];
        }

        // 5. Category Performance & Revenue Share
        $itemWhere = "WHERE o.status IN ('served', 'paid')";
        $itemParams = [];
        if ($startDate && $endDate) {
            $itemWhere .= " AND DATE(o.created_at) BETWEEN ? AND ?";
            $itemParams[] = $startDate;
            $itemParams[] = $endDate;
        }

        $stmtCat = $db->prepare("
            SELECT 
                d.category, 
                COUNT(DISTINCT d.id) as dish_count,
                COALESCE(SUM(oi.quantity), 0) as items_sold, 
                COALESCE(SUM(oi.quantity * oi.price_at_order), 0) as category_revenue
            FROM order_items oi
            JOIN orders o ON oi.order_id = o.id
            JOIN dishes d ON oi.dish_id = d.id
            {$itemWhere}
            GROUP BY d.category
            ORDER BY category_revenue DESC
        ");
        $stmtCat->execute($itemParams);
        $categoryBreakdown = $stmtCat->fetchAll();

        $totalRev = (float)($rangeStats['total_revenue'] ?? 0);
        foreach ($categoryBreakdown as &$cat) {
            $cat['items_sold'] = (int)$cat['items_sold'];
            $cat['category_revenue'] = (float)$cat['category_revenue'];
            $cat['percentage'] = $totalRev > 0 ? round(($cat['category_revenue'] / $totalRev) * 100, 1) : 0;
        }

        // 6. Best Selling Dishes (Top 6)
        $stmtBest = $db->prepare("
            SELECT 
                d.id, d.name, d.category, d.price, d.image_url,
                SUM(oi.quantity) as total_quantity, 
                SUM(oi.quantity * oi.price_at_order) as total_revenue
            FROM order_items oi
            JOIN orders o ON oi.order_id = o.id
            JOIN dishes d ON oi.dish_id = d.id
            {$itemWhere}
            GROUP BY d.id, d.name, d.category, d.price, d.image_url
            ORDER BY total_quantity DESC, total_revenue DESC
            LIMIT 6
        ");
        $stmtBest->execute($itemParams);
        $bestSelling = $stmtBest->fetchAll();

        // 7. Hourly Peak Traffic Distribution
        $stmtHourly = $db->prepare("
            SELECT 
                STRFTIME('%H', created_at) as hour_code, 
                COUNT(*) as orders_count, 
                COALESCE(SUM(total_amount), 0) as hourly_revenue
            FROM orders 
            {$trendWhere}
            GROUP BY hour_code
            ORDER BY hour_code ASC
        ");
        $stmtHourly->execute($trendParams);
        $hourlyRows = $stmtHourly->fetchAll();

        $hourlyPeak = [];
        foreach ($hourlyRows as $h) {
            $hourNum = (int)$h['hour_code'];
            $formattedHour = date('g A', strtotime("{$hourNum}:00"));
            $hourlyPeak[] = [
                'hour' => $formattedHour,
                'orders' => (int)$h['orders_count'],
                'revenue' => (float)$h['hourly_revenue']
            ];
        }

        // 8. Recent Orders Stream (Top 25) with Dish Items
        $stmtRecent = $db->query("
            SELECT o.*, t.table_number 
            FROM orders o 
            JOIN tables t ON o.table_id = t.id 
            ORDER BY o.id DESC 
            LIMIT 25
        ");
        $recentOrders = $stmtRecent->fetchAll();

        foreach ($recentOrders as &$ord) {
            $stmtItems = $db->prepare("
                SELECT oi.quantity, oi.price_at_order, oi.notes, d.name, d.category
                FROM order_items oi
                JOIN dishes d ON oi.dish_id = d.id
                WHERE oi.order_id = ?
            ");
            $stmtItems->execute([$ord['id']]);
            $ord['items'] = $stmtItems->fetchAll();
        }

        // 9. Table Occupancy Metrics
        $stmtTableStats = $db->query("
            SELECT 
                COUNT(*) as total_tables,
                SUM(CASE WHEN status = 'occupied' THEN 1 ELSE 0 END) as occupied_tables,
                SUM(CASE WHEN bill_requested = 1 THEN 1 ELSE 0 END) as bills_requested
            FROM tables
        ");
        $tableMetrics = $stmtTableStats->fetch();

        // 10. Formatted Period Label
        if ($startDate && $endDate) {
            if ($startDate === $endDate) {
                $periodLabel = date('d-m-Y', strtotime($startDate));
            } else {
                $periodLabel = date('d-m-Y', strtotime($startDate)) . ' to ' . date('d-m-Y', strtotime($endDate));
            }
        } else {
            $periodLabel = date('F Y'); // e.g. "September 2026"
        }

        return [
            'total_revenue' => (float)($rangeStats['total_revenue'] ?? 0),
            'total_orders' => (int)($rangeStats['total_orders'] ?? 0),
            'avg_order_value' => (float)($rangeStats['avg_order_value'] ?? 0),
            'revenue_today' => (float)($todayStats['rev_today'] ?? 0),
            'orders_today' => (int)($todayStats['orders_today'] ?? 0),
            'revenue_week' => (float)($weekStats['rev_week'] ?? 0),
            'orders_week' => (int)($weekStats['orders_week'] ?? 0),
            'daily_trend' => $dailyTrend,
            'category_breakdown' => $categoryBreakdown,
            'best_selling' => $bestSelling,
            'hourly_peak' => $hourlyPeak,
            'recent_orders' => $recentOrders,
            'table_metrics' => [
                'total_tables' => (int)($tableMetrics['total_tables'] ?? 0),
                'occupied_tables' => (int)($tableMetrics['occupied_tables'] ?? 0),
                'bills_requested' => (int)($tableMetrics['bills_requested'] ?? 0),
                'occupancy_rate' => ($tableMetrics['total_tables'] > 0) ? round(($tableMetrics['occupied_tables'] / $tableMetrics['total_tables']) * 100) : 0
            ],
            'period_label' => $periodLabel,
            'start_date' => $startDate,
            'end_date' => $endDate
        ];
    }
}
