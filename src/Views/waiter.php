<?php
$title = "Waiter Dashboard & Table Billing";
$bodyClass = "waiter-page";
$headerRightHtml = '
    <button id="btn-toggle-audio" class="btn btn-outline btn-sm"><i class="fa-solid fa-volume-high"></i> Sound On</button>
    <a href="/api/logout" class="btn btn-danger btn-sm"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
';

ob_start();
?>

<div class="waiter-container">
    <div class="waiter-top-bar">
        <div class="waiter-title">
            <h2><i class="fa-solid fa-bell-concierge"></i> Waiter Floor & Billing Dashboard</h2>
            <span class="live-indicator"><span class="pulse-dot"></span> REALTIME FLOOR MONITOR</span>
        </div>
        <div class="waiter-summary">
            <span class="badge badge-success"><i class="fa-solid fa-circle-check"></i> <span id="waiter-available-count">0</span> Available</span>
            <span class="badge badge-warning"><i class="fa-solid fa-user-group"></i> <span id="waiter-occupied-count">0</span> Occupied</span>
            <span class="badge badge-danger alert-badge"><i class="fa-solid fa-bell"></i> <span id="waiter-ready-count">0</span> Ready to Serve</span>
        </div>
    </div>

    <!-- Ready Orders High-Priority Alert Banner Container -->
    <div id="waiter-alerts-container" class="waiter-alerts-container">
        <!-- Injected by JS when orders are status='ready' -->
    </div>

    <!-- Table Matrix Floor Grid -->
    <div id="tables-grid" class="tables-grid">
        <!-- Injected dynamically by JS via 3s polling -->
    </div>
</div>

<!-- Close Bill & Payment Modal -->
<div id="close-bill-modal" class="modal-overlay hidden">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="bill-modal-table-title"><i class="fa-solid fa-receipt"></i> Close Bill & Settle Payment</h3>
            <button id="btn-close-bill-modal" class="close-btn"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="modal-body">
            <div id="bill-modal-items-list" class="cart-items-container" style="margin-bottom: 16px;">
                <!-- Injected by JS -->
            </div>

            <div class="form-group" style="margin-top: 14px;">
                <label for="payment-method-select"><i class="fa-solid fa-credit-card"></i> Payment Method</label>
                <select id="payment-method-select" class="form-control">
                    <option value="cash">💵 Cash Payment</option>
                    <option value="upi">📱 UPI / QR Scanner (GPay / PhonePe / Paytm)</option>
                    <option value="card">💳 Credit / Debit Card</option>
                </select>
            </div>
        </div>
        <div class="modal-footer">
            <div class="cart-summary-line">
                <span>Total Bill Payable</span>
                <strong id="bill-modal-total-price" style="color:#d97706; font-size:1.3rem;">₹0.00</strong>
            </div>
            <button id="btn-confirm-close-bill" class="btn btn-success btn-block btn-lg">
                <i class="fa-solid fa-check-circle"></i> Confirm Payment & Free Table
            </button>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/layout.php';
?>
