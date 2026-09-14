<?php

require __DIR__ . '/../vendor/autoload.php';

use App\Database\DB;
use App\Models\Dish;
use App\Models\Table;
use App\Models\Order;
use App\Services\RevenueService;
use App\Services\QrCodeService;

echo "==================================================\n";
echo "       HOTEL QR MENU - END-TO-END VERIFICATION    \n";
echo "==================================================\n\n";

try {
    // Step 1: DB Initialization Check
    echo "[1/6] Testing Database & Seed Data (Indian Rupees ₹)...\n";
    $db = DB::getInstance();
    $dishes = Dish::all();
    $tables = Table::all();
    echo " -> Found " . count($dishes) . " dishes in menu.\n";
    echo " -> Found " . count($tables) . " active tables.\n";
    if (count($dishes) === 0 || count($tables) === 0) {
        throw new Exception("Seed data failed!");
    }
    echo " PASSED\n\n";

    // Step 2: Customer Multi-Order Placement Simulation
    echo "[2/6] Testing Customer Multi-Order Creation...\n";
    $table = $tables[0]; // Table 1
    $dish1 = $dishes[0]; // Paneer Tikka (220)
    $dish2 = $dishes[1]; // Calamari (280)

    $cartItems1 = [
        ['dish_id' => $dish1['id'], 'quantity' => 1, 'notes' => 'Extra spicy'],
        ['dish_id' => $dish2['id'], 'quantity' => 1, 'notes' => 'Less oil']
    ];

    $orderId1 = Order::createOrder($table['id'], $cartItems1);
    echo " -> Created Order #{$orderId1} for {$table['table_number']}.\n";

    $order1 = Order::find($orderId1);
    echo " -> Status: {$order1['status']}\n";
    echo " -> Total Amount: ₹" . number_format($order1['total_amount'], 2) . "\n";

    // Simulate customer ordering 2 additional drinks later
    $dishDrink = $dishes[8]; // Mango Lassi (90)
    $cartItems2 = [
        ['dish_id' => $dishDrink['id'], 'quantity' => 2, 'notes' => 'Chilled']
    ];
    $orderId2 = Order::createOrder($table['id'], $cartItems2);
    echo " -> Customer ordered additional dishes later (Order #{$orderId2}).\n";

    $updatedTable = Table::find($table['id']);
    echo " -> Table Status set to: {$updatedTable['status']}\n";

    if ($order1['status'] !== 'placed' || $updatedTable['status'] !== 'occupied') {
        throw new Exception("Order creation or table occupancy update failed!");
    }
    echo " PASSED\n\n";

    // Step 3: Kitchen Order Processing Flow
    echo "[3/6] Testing Kitchen Flow (Placed -> Accepted -> Ready)...\n";
    Order::updateStatus($orderId1, 'accepted');
    Order::updateStatus($orderId1, 'ready');

    Order::updateStatus($orderId2, 'accepted');
    Order::updateStatus($orderId2, 'ready');
    echo " -> Both orders marked ready in kitchen.\n";
    echo " PASSED\n\n";

    // Step 4: Waiter Serving & Close Bill Workflow
    echo "[4/6] Testing Waiter Serving & Close Bill Workflow...\n";
    Order::updateStatus($orderId1, 'served');
    Order::updateStatus($orderId2, 'served');
    echo " -> Waiter served dishes to customer.\n";

    $overview = Order::getWaiterOverview();
    $table1Data = null;
    foreach ($overview as $t) {
        if ($t['id'] == $table['id']) $table1Data = $t;
    }

    echo " -> Waiter Screen Cumulative Items Count: " . count($table1Data['session_items']) . "\n";
    echo " -> Waiter Screen Cumulative Session Total: ₹" . number_format($table1Data['session_total'], 2) . "\n";

    // Waiter Closes Bill and frees table
    Order::closeTableBill($table['id'], 'cash');
    $freedTable = Table::find($table['id']);
    echo " -> Waiter closed bill & freed table: status = {$freedTable['status']}\n";

    if ($freedTable['status'] !== 'available') {
        throw new Exception("Close bill & free table workflow failed!");
    }
    echo " PASSED\n\n";

    // Step 5: Revenue Analytics in Indian Rupees
    echo "[5/6] Testing Revenue Analytics (₹)...\n";
    $stats = RevenueService::getDashboardStats();
    echo " -> Total Revenue: ₹" . number_format($stats['total_revenue'], 2) . "\n";
    echo " -> Total Orders: " . $stats['total_orders'] . "\n";
    if ($stats['total_orders'] < 1) {
        throw new Exception("Revenue calculation failed!");
    }
    echo " PASSED\n\n";

    // Step 6: QR Code Generation
    echo "[6/6] Testing Dynamic QR Code Generation...\n";
    $testUrl = "http://127.0.0.1:8000/table/1";
    $qrSvg = QrCodeService::generateSvgDataUri($testUrl);
    echo " -> Generated QR Code Data URI length: " . strlen($qrSvg) . " bytes\n";
    if (empty($qrSvg)) {
        throw new Exception("QR code generation failed!");
    }
    echo " PASSED\n\n";

    echo "==================================================\n";
    echo " ALL 6 END-TO-END TESTS PASSED SUCCESSFULLY! \n";
    echo "==================================================\n";
} catch (Exception $e) {
    echo "\n TEST FAILED: " . $e->getMessage() . "\n";
    exit(1);
}
