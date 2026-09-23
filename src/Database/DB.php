<?php

namespace App\Database;

use PDO;
use PDOException;

class DB
{
    private static ?PDO $instance = null;

    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            $config = require __DIR__ . '/../../config/database.php';
            $driver = $config['driver'] ?? 'sqlite';

            if ($driver === 'sqlite') {
                $dbPath = $config['sqlite']['path'];
                
                // Vercel read-only filesystem workaround for SQLite
                if (isset($_SERVER['VERCEL']) || getenv('VERCEL')) {
                    $dbPath = '/tmp/database.sqlite';
                }

                $dbDir = dirname($dbPath);
                if (!is_dir($dbDir)) {
                    mkdir($dbDir, 0777, true);
                }

                try {
                    self::$instance = new PDO("sqlite:" . $dbPath);
                    self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    self::$instance->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

                    // SQLite performance & reliability PRAGMAs
                    self::$instance->exec("PRAGMA journal_mode=WAL");
                    self::$instance->exec("PRAGMA foreign_keys=ON");
                    self::$instance->exec("PRAGMA busy_timeout=5000");

                    // Always ensure tables and seed data exist (handles cold starts, partial inits, ephemeral /tmp)
                    self::initSqliteTables(self::$instance);
                } catch (PDOException $e) {
                    error_log("SQLite Database Error: " . $e->getMessage());
                    throw new \RuntimeException("Database connection failed. Please try again.");
                }
            } else {
                $mc = $config['mysql'];
                $dsn = sprintf("mysql:host=%s;port=%s;dbname=%s;charset=%s", $mc['host'], $mc['port'], $mc['dbname'], $mc['charset']);
                try {
                    self::$instance = new PDO($dsn, $mc['user'], $mc['pass'], [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    ]);
                } catch (PDOException $e) {
                    error_log("MySQL Database Error: " . $e->getMessage());
                    throw new \RuntimeException("Database connection failed. Please try again.");
                }
            }
        }

        return self::$instance;
    }

    private static function initSqliteTables(PDO $pdo): void
    {
        $queries = [
            "CREATE TABLE IF NOT EXISTS tables (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                table_number TEXT NOT NULL UNIQUE,
                status TEXT DEFAULT 'available',
                bill_requested INTEGER DEFAULT 0,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );",

            "CREATE TABLE IF NOT EXISTS dishes (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL,
                description TEXT,
                price REAL NOT NULL,
                category TEXT NOT NULL,
                image_url TEXT,
                is_available INTEGER DEFAULT 1,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );",

            "CREATE TABLE IF NOT EXISTS orders (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                table_id INTEGER NOT NULL,
                status TEXT DEFAULT 'placed',
                total_amount REAL DEFAULT 0.00,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (table_id) REFERENCES tables(id) ON DELETE CASCADE
            );",

            "CREATE TABLE IF NOT EXISTS order_items (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                order_id INTEGER NOT NULL,
                dish_id INTEGER NOT NULL,
                quantity INTEGER NOT NULL DEFAULT 1,
                price_at_order REAL NOT NULL,
                notes TEXT,
                FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
                FOREIGN KEY (dish_id) REFERENCES dishes(id) ON DELETE CASCADE
            );"
        ];

        foreach ($queries as $sql) {
            $pdo->exec($sql);
        }

        // Ensure bill_requested column exists (migration for older DB versions)
        try {
            $pdo->exec("ALTER TABLE tables ADD COLUMN bill_requested INTEGER DEFAULT 0");
        } catch (PDOException $ex) {
            // Column already exists — safe to ignore
        }

        // Seed default tables
        $check = $pdo->query("SELECT COUNT(*) FROM tables")->fetchColumn();
        if ($check == 0) {
            $stmtTable = $pdo->prepare("INSERT INTO tables (table_number, status) VALUES (?, 'available')");
            foreach (SeedData::getTables() as $tName) {
                $stmtTable->execute([$tName]);
            }
        }

        // Seed default dishes (All 96 dishes across 12 categories)
        $checkDishes = $pdo->query("SELECT COUNT(*) FROM dishes")->fetchColumn();
        if ($checkDishes < 50) {
            $pdo->exec("DELETE FROM dishes");
            $stmt = $pdo->prepare("INSERT INTO dishes (name, description, price, category, image_url, is_available) VALUES (?, ?, ?, ?, ?, ?)");
            foreach (SeedData::getDishes() as $d) {
                $stmt->execute([
                    $d['name'],
                    $d['description'],
                    $d['price'],
                    $d['category'],
                    $d['image_url'] ?? null,
                    $d['is_available'] ?? 1
                ]);
            }
        }
    }
}

