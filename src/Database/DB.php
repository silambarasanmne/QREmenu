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
            foreach (['Table 1', 'Table 2', 'Table 3', 'Table 4', 'Table 5'] as $tName) {
                $stmtTable->execute([$tName]);
            }
        }

        // Seed default dishes (Indian Rupee ₹ Pricing)
        $checkDishes = $pdo->query("SELECT COUNT(*) FROM dishes")->fetchColumn();
        if ($checkDishes == 0) {
            $dishes = [
                ['Paneer Tikka Starter', 'Tender paneer cubes marinated in rich Indian spices and grilled in a clay tandoor oven.', 220.00, 'Starters', 'https://images.unsplash.com/photo-1567188040759-fb8a883dc6d8?auto=format&fit=crop&w=400&q=80', 1],
                ['Crispy Calamari Rings', 'Golden fried squid rings served with spicy garlic aioli and lemon wedges.', 280.00, 'Starters', 'https://images.unsplash.com/photo-1599487488170-d11ec9c172f0?auto=format&fit=crop&w=400&q=80', 1],
                ['Tomato Basil Soup', 'Velvety roasted tomato soup garnished with cream and served with buttered croutons.', 140.00, 'Starters', 'https://images.unsplash.com/photo-1547592166-23ac45744acd?auto=format&fit=crop&w=400&q=80', 1],
                ['Butter Chicken Special', 'Succulent chicken tikka pieces cooked in a rich, buttery tomato gravy with fresh cream.', 340.00, 'Mains', 'https://images.unsplash.com/photo-1588166524941-3bf61a9c41db?auto=format&fit=crop&w=400&q=80', 1],
                ['Classic Dum Biryani', 'Fragrant Basmati rice slow-cooked with aromatic spices, fresh mint, and tender meat or veg.', 290.00, 'Mains', 'https://images.unsplash.com/photo-1563379091339-03b21ab4a4f8?auto=format&fit=crop&w=400&q=80', 1],
                ['Paneer Butter Masala', 'Fresh cottage cheese cubes simmered in a mildly spicy tomato-cashew nut gravy.', 260.00, 'Mains', 'https://images.unsplash.com/photo-1631452180519-c014fe946bc7?auto=format&fit=crop&w=400&q=80', 1],
                ['Gulab Jamun with Ice Cream', 'Warm golden milk dumplings served with chilled vanilla bean ice cream.', 120.00, 'Desserts', 'https://images.unsplash.com/photo-1571877227200-a0d98ea607e9?auto=format&fit=crop&w=400&q=80', 1],
                ['Saffron Rasmalai', 'Soft cottage cheese patties soaked in chilled saffron-infused milk and cardamom.', 130.00, 'Desserts', 'https://images.unsplash.com/photo-1533134242443-d4fd215305ad?auto=format&fit=crop&w=400&q=80', 1],
                ['Mango Lassi', 'Chilled sweet yogurt smoothie blended with ripe Alphonso mango puree.', 90.00, 'Beverages', 'https://images.unsplash.com/photo-1513558161293-cdaf765ed2fd?auto=format&fit=crop&w=400&q=80', 1],
                ['Masala Chai', 'Traditional Indian spiced tea brewed with fresh ginger, cardamom, and milk.', 50.00, 'Beverages', 'https://images.unsplash.com/photo-1534778101976-62847782c213?auto=format&fit=crop&w=400&q=80', 1]
            ];

            $stmt = $pdo->prepare("INSERT INTO dishes (name, description, price, category, image_url, is_available) VALUES (?, ?, ?, ?, ?, ?)");
            foreach ($dishes as $d) {
                $stmt->execute($d);
            }
        }
    }
}
