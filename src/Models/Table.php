<?php

namespace App\Models;

use App\Database\DB;
use PDO;

class Table
{
    public static function all(): array
    {
        $db = DB::getInstance();
        $stmt = $db->query("SELECT * FROM tables ORDER BY id ASC");
        return $stmt->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $db = DB::getInstance();
        $stmt = $db->prepare("SELECT * FROM tables WHERE id = ?");
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public static function updateStatus(int $id, string $status): bool
    {
        $db = DB::getInstance();
        $stmt = $db->prepare("UPDATE tables SET status = ? WHERE id = ?");
        return $stmt->execute([$status, $id]);
    }

    public static function requestBill(int $id): bool
    {
        $db = DB::getInstance();
        $stmt = $db->prepare("UPDATE tables SET bill_requested = 1 WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public static function clearBillRequest(int $id): bool
    {
        $db = DB::getInstance();
        $stmt = $db->prepare("UPDATE tables SET bill_requested = 0 WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public static function create(string $tableNumber): int
    {
        $db = DB::getInstance();
        $stmt = $db->prepare("INSERT INTO tables (table_number, status) VALUES (?, 'available')");
        $stmt->execute([$tableNumber]);
        return (int)$db->lastInsertId();
    }

    public static function createMultiple(int $count, string $prefix = 'Table'): int
    {
        $db = DB::getInstance();
        $existing = self::all();
        $maxNum = 0;

        foreach ($existing as $t) {
            if (preg_match('/(?:Table\s*)?(\d+)/i', $t['table_number'], $matches)) {
                $num = (int)$matches[1];
                if ($num > $maxNum) {
                    $maxNum = $num;
                }
            }
        }

        $prefix = trim($prefix) ?: 'Table';
        $created = 0;
        $stmt = $db->prepare("INSERT INTO tables (table_number, status) VALUES (?, 'available')");

        for ($i = 1; $i <= $count; $i++) {
            $nextNum = $maxNum + $i;
            $name = "{$prefix} {$nextNum}";
            try {
                $stmt->execute([$name]);
                $created++;
            } catch (\Exception $e) {
                // Ignore duplicates
            }
        }

        return $created;
    }

    public static function delete(int $id): bool
    {
        $db = DB::getInstance();
        $stmt = $db->prepare("DELETE FROM tables WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
