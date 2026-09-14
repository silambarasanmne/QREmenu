<?php

namespace App\Models;

use App\Database\DB;
use PDO;

class Dish
{
    public static function all(bool $availableOnly = false): array
    {
        $db = DB::getInstance();
        $sql = "SELECT * FROM dishes";
        if ($availableOnly) {
            $sql .= " WHERE is_available = 1";
        }
        $sql .= " ORDER BY category ASC, name ASC";
        $stmt = $db->query($sql);
        return $stmt->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $db = DB::getInstance();
        $stmt = $db->prepare("SELECT * FROM dishes WHERE id = ?");
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public static function groupedByCategory(bool $availableOnly = true): array
    {
        $dishes = self::all($availableOnly);
        $grouped = [];
        foreach ($dishes as $dish) {
            $cat = $dish['category'] ?? 'General';
            if (!isset($grouped[$cat])) {
                $grouped[$cat] = [];
            }
            $grouped[$cat][] = $dish;
        }
        return $grouped;
    }

    public static function create(array $data): int
    {
        $db = DB::getInstance();
        $stmt = $db->prepare("INSERT INTO dishes (name, description, price, category, image_url, is_available) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $data['name'],
            $data['description'] ?? '',
            $data['price'],
            $data['category'],
            $data['image_url'] ?? null,
            isset($data['is_available']) ? (int)$data['is_available'] : 1
        ]);
        return (int)$db->lastInsertId();
    }

    public static function update(int $id, array $data): bool
    {
        $db = DB::getInstance();
        $stmt = $db->prepare("UPDATE dishes SET name = ?, description = ?, price = ?, category = ?, image_url = ?, is_available = ? WHERE id = ?");
        return $stmt->execute([
            $data['name'],
            $data['description'] ?? '',
            $data['price'],
            $data['category'],
            $data['image_url'] ?? null,
            isset($data['is_available']) ? (int)$data['is_available'] : 1,
            $id
        ]);
    }

    public static function toggleAvailability(int $id): bool
    {
        $db = DB::getInstance();
        $stmt = $db->prepare("UPDATE dishes SET is_available = CASE WHEN is_available = 1 THEN 0 ELSE 1 END WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public static function delete(int $id): bool
    {
        $db = DB::getInstance();
        $stmt = $db->prepare("DELETE FROM dishes WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
