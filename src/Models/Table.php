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

    public static function create(string $tableNumber): int
    {
        $db = DB::getInstance();
        $stmt = $db->prepare("INSERT INTO tables (table_number, status) VALUES (?, 'available')");
        $stmt->execute([$tableNumber]);
        return (int)$db->lastInsertId();
    }

    public static function delete(int $id): bool
    {
        $db = DB::getInstance();
        $stmt = $db->prepare("DELETE FROM tables WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
