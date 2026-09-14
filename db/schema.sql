-- Database Schema for Hotel QR Menu ordering system (Indian Rupee Pricing)

CREATE TABLE IF NOT EXISTS `tables` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `table_number` VARCHAR(50) NOT NULL UNIQUE,
    `status` ENUM('available', 'occupied') DEFAULT 'available',
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS `dishes` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(150) NOT NULL,
    `description` TEXT,
    `price` DECIMAL(10,2) NOT NULL,
    `category` VARCHAR(50) NOT NULL,
    `image_url` VARCHAR(255) DEFAULT NULL,
    `is_available` TINYINT(1) DEFAULT 1,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS `orders` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `table_id` INT NOT NULL,
    `status` ENUM('placed', 'accepted', 'ready', 'served') DEFAULT 'placed',
    `total_amount` DECIMAL(10,2) DEFAULT 0.00,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`table_id`) REFERENCES `tables`(`id`) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS `order_items` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `order_id` INT NOT NULL,
    `dish_id` INT NOT NULL,
    `quantity` INT NOT NULL DEFAULT 1,
    `price_at_order` DECIMAL(10,2) NOT NULL,
    `notes` VARCHAR(255) DEFAULT NULL,
    FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`dish_id`) REFERENCES `dishes`(`id`) ON DELETE CASCADE
);

-- Seed Data: Tables
INSERT INTO `tables` (`table_number`, `status`) VALUES
('Table 1', 'available'),
('Table 2', 'available'),
('Table 3', 'available'),
('Table 4', 'available'),
('Table 5', 'available');

-- Seed Data: Dishes (Indian Rupee ₹ Pricing)
INSERT INTO `dishes` (`name`, `description`, `price`, `category`, `image_url`, `is_available`) VALUES
('Paneer Tikka Starter', 'Tender paneer cubes marinated in rich Indian spices and grilled in a clay tandoor oven.', 220.00, 'Starters', 'https://images.unsplash.com/photo-1567188040759-fb8a883dc6d8?auto=format&fit=crop&w=400&q=80', 1),
('Crispy Calamari Rings', 'Golden fried squid rings served with spicy garlic aioli and lemon wedges.', 280.00, 'Starters', 'https://images.unsplash.com/photo-1599487488170-d11ec9c172f0?auto=format&fit=crop&w=400&q=80', 1),
('Tomato Basil Soup', 'Velvety roasted tomato soup garnished with cream and served with buttered croutons.', 140.00, 'Starters', 'https://images.unsplash.com/photo-1547592166-23ac45744acd?auto=format&fit=crop&w=400&q=80', 1),
('Butter Chicken Special', 'Succulent chicken tikka pieces cooked in a rich, buttery tomato gravy with fresh cream.', 340.00, 'Mains', 'https://images.unsplash.com/photo-1588166524941-3bf61a9c41db?auto=format&fit=crop&w=400&q=80', 1),
('Classic Dum Biryani', 'Fragrant Basmati rice slow-cooked with aromatic spices, fresh mint, and tender meat or veg.', 290.00, 'Mains', 'https://images.unsplash.com/photo-1563379091339-03b21ab4a4f8?auto=format&fit=crop&w=400&q=80', 1),
('Paneer Butter Masala', 'Fresh cottage cheese cubes simmered in a mildly spicy tomato-cashew nut gravy.', 260.00, 'Mains', 'https://images.unsplash.com/photo-1631452180519-c014fe946bc7?auto=format&fit=crop&w=400&q=80', 1),
('Gulab Jamun with Ice Cream', 'Warm golden milk dumplings served with chilled vanilla bean ice cream.', 120.00, 'Desserts', 'https://images.unsplash.com/photo-1571877227200-a0d98ea607e9?auto=format&fit=crop&w=400&q=80', 1),
('Saffron Rasmalai', 'Soft cottage cheese patties soaked in chilled saffron-infused milk and cardamom.', 130.00, 'Desserts', 'https://images.unsplash.com/photo-1533134242443-d4fd215305ad?auto=format&fit=crop&w=400&q=80', 1),
('Mango Lassi', 'Chilled sweet yogurt smoothie blended with ripe Alphonso mango puree.', 90.00, 'Beverages', 'https://images.unsplash.com/photo-1513558161293-cdaf765ed2fd?auto=format&fit=crop&w=400&q=80', 1),
('Masala Chai', 'Traditional Indian spiced tea brewed with fresh ginger, cardamom, and milk.', 50.00, 'Beverages', 'https://images.unsplash.com/photo-1534778101976-62847782c213?auto=format&fit=crop&w=400&q=80', 1);
