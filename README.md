# Agam - Hotel & Restaurant QR Menu Ordering System

**Agam** is a lightweight, mobile-first **Hotel & Restaurant QR Menu ordering system** built in **PHP 8+** using **Slim Framework 4**. It features 4 interconnected real-time role-based portals (Customer, Kitchen KDS, Waiter Floor & Billing, Owner Analytics) operating seamlessly via lightweight JSON short-polling without manual page reloads.

---

## 🚀 Tech Stack & Framework Details

- **Backend Framework:** **Slim Framework 4** (`slim/slim: ^4.12`, `slim/psr7: ^1.8`) - Ultra-fast, lightweight PSR-7 compliant micro-framework.
- **Database Layer:** **PDO (PHP Data Objects)** supporting dual database modes:
  - **MySQL / MariaDB** (Production SQL script: `db/schema.sql`)
  - **SQLite** (Auto-initializing zero-config database: `db/database.sqlite`)
- **Frontend Architecture:** Modern Vanilla HTML5 + CSS3 (Custom **eMenu Gold & Charcoal** design system inspired by mobile ordering apps) + Vanilla JS (Fetch API with 3s polling engine & Web Audio API chime notifications).
- **QR Code Library:** `endroid/qr-code: ^5.1` with `bacon/bacon-qr-code` for dynamic table QR code SVG/PNG generation and disk file export.
- **Currency Support:** **Indian Rupees (₹)**.

---

## 📋 Application Features & Role Portals

### 1. 📱 Customer Portal (`/table/{table_id}`)
- **Mobile OTP Authentication:** Customers scan the table QR code, input their 10-digit mobile number (`+91`), receive a 4-digit OTP (*Demo OTP: `1234`*), confirm their table number, and unlock the menu.
- **Compact Multi-Column Box Grid:** Dishes are rendered as ultra-compact visual box cards (2 columns on mobile, 3–5 columns on desktop) displaying 8–10 dishes at a glance.
- **Interactive Cart & Special Notes:** Item quantity selectors, special preparation instructions, sticky floating cart bar, and slide-over order summary modal.
- **Live 4-Step Order Tracker:** Real-time visual progress indicator updating automatically:
  1. `placed` ➔ *"Order placed & sent to kitchen"*
  2. `accepted` ➔ *"Chef is preparing your meal"*
  3. `ready` ➔ *"Ready to serve! Waiter is bringing it to your table"*
  4. `served` ➔ *"Order served. Enjoy your meal!"*
- **Multi-Order Session Support:** Customers can place additional orders for drinks or extra dishes during their dining session.

### 2. 👨‍🍳 Kitchen Display System - KDS (`/kitchen`)
- **Real-Time KDS Board:** Incoming orders update every 3 seconds via JSON polling.
- **Audio Notification Chime:** Web Audio API sound alert plays automatically when a new order arrives.
- **Timer Counter:** Live timer badges show minutes elapsed since each order was placed.
- **Kitchen Actions:**
  - **Accept Order** (`placed` ➔ `accepted`): Notifies customer immediately.
  - **Mark Ready to Serve** (`accepted` ➔ `ready`): Triggers high-priority alert on Waiter screen.

### 3. 🛎️ Waiter Floor & Billing Dashboard (`/waiter`)
- **Table Floor Matrix:** Grid showing real-time table status (`available` [Green] vs `occupied` [Orange]).
- **Ready Order Alert Banners:** Blinking notification banners with chime sound when dishes are prepared.
- **Cumulative Session Billing:** Aggregates all orders placed during an occupied table's dining session into a running itemized bill in **₹**.
- **Close Bill & Payment Workflow:**
  - Clicking **Close Bill & Pay** opens an invoice modal displaying the session's cumulative dish list, total amount in **₹**, and payment method selector (*Cash*, *UPI / QR Scanner*, *Card*).
  - **Table Release Rule:** The table is set to `available` **only after the waiter confirms payment**.

### 4. 📊 Owner Console & Analytics (`/owner`)
- **Revenue Dashboard:** Filter by date range (Today, This Week, Custom). Displays Total Revenue in **₹**, Total Orders, Average Order Value, and Best-Selling Dishes table.
- **Menu Items Directory (CRUD):** Add, edit, delete dishes, select categories, update prices in **₹**, upload images, and toggle live availability.
- **Table & QR Code Manager:** View tables, download static **PNG** and **SVG** QR code image files, and print high-resolution physical table cards.

---

## 📁 Directory Structure

```text
EMenu/
├── config/
│   └── database.php             # Database configuration (MySQL/SQLite driver toggle)
├── db/
│   ├── schema.sql               # MySQL relational schema & Indian Rupee seed data
│   └── database.sqlite          # Auto-created SQLite database file
├── public/
│   ├── assets/
│   │   ├── css/style.css        # eMenu Gold mobile-first responsive design system
│   │   └── js/app.js            # Real-time fetch polling engine & UI logic
│   ├── qr_codes/                # Web-accessible generated PNG & SVG QR code files
│   └── index.php                # Slim 4 single entry point & route definitions
├── qr_codes/                    # Static generated QR code files in root
├── scripts/
│   └── generate_qr_codes.php    # CLI script to generate QR codes for all tables
├── src/
│   ├── Controllers/             # Customer, Kitchen, Waiter, Owner, Api controllers
│   ├── Database/                # PDO Singleton manager with SQLite auto-seed
│   ├── Models/                  # Table, Dish, Order data models
│   ├── Services/                # AuthService (OTP/PIN), QrCodeService, RevenueService
│   └── Views/                   # HTML view templates (customer, kitchen, waiter, owner, login)
├── tests/
│   └── test_flow.php            # Automated CLI end-to-end integration test suite
├── composer.json                # Composer package definition & PSR-4 autoloading
└── README.md                    # Project documentation
```

---

## 🗄️ Database Schema & Data Models

### Tables:
1. **`tables`**: `id`, `table_number`, `status` (`available` / `occupied`), `created_at`
2. **`dishes`**: `id`, `name`, `description`, `price`, `category`, `image_url`, `is_available`, `created_at`
3. **`orders`**: `id`, `table_id`, `status` (`placed`, `accepted`, `ready`, `served`), `total_amount`, `created_at`, `updated_at`
4. **`order_items`**: `id`, `order_id`, `dish_id`, `quantity`, `price_at_order`, `notes`

---

## ⚡ Installation & Execution Guide

### 1. Install Dependencies
```bash
php composer.phar install
```

### 2. Run Automated Test Suite
```bash
php tests/test_flow.php
```

### 3. Batch Generate Table QR Codes
```bash
php scripts/generate_qr_codes.php
```

### 4. Start Local Development Server
```bash
php -S 127.0.0.1:8000 -t public
```

---

## 🔑 Access Credentials & Localhost URLs

| Role / Page | Localhost URL | Access Credentials |
| :--- | :--- | :--- |
| **Customer Menu (Table 1)** | `http://127.0.0.1:8000/table/1` | Mobile OTP (*Demo OTP: `1234`*) |
| **Customer Menu (Table 2)** | `http://127.0.0.1:8000/table/2` | Mobile OTP (*Demo OTP: `1234`*) |
| **Kitchen Display System** | `http://127.0.0.1:8000/kitchen` | PIN: `1234` |
| **Waiter Floor & Billing** | `http://127.0.0.1:8000/waiter` | PIN: `1234` |
| **Owner Console & Analytics** | `http://127.0.0.1:8000/owner` | Password: `admin123` |

---

## 🛡️ License

This project is open-source under the [MIT License](LICENSE).
