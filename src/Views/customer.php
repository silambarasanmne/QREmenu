<?php
use App\Services\AuthService;
use App\Models\Table;

$title = "Agam - " . htmlspecialchars($table['table_number']);
$bodyClass = "customer-page";

$customerSession = AuthService::getCustomerSession();
$isLoggedIn = $customerSession !== null;
$customerMobile = $customerSession['mobile'] ?? '';

$hasActiveOrder = !empty($activeOrder['has_active_order']);
$allTables = Table::all();

$getCatSlug = function($name) {
    $slug = preg_replace('~[^\pL\d]+~u', '-', $name);
    $slug = trim($slug, '-');
    $slug = strtolower($slug);
    return empty($slug) ? 'general' : $slug;
};

$headerRightHtml = '
    <div class="header-right-badges">
        ' . ($isLoggedIn ? '<span class="mobile-session-badge"><i class="fa-solid fa-phone"></i> +91 ' . htmlspecialchars($customerMobile) . '</span>' : '') . '
        <div class="table-badge"><i class="fa-solid fa-chair"></i> ' . htmlspecialchars($table['table_number']) . '</div>
    </div>
';

ob_start();

?>

<!-- Customer Mobile OTP Verification Overlay (Appears if not logged in) -->
<div id="customer-otp-modal" class="modal-overlay <?= $isLoggedIn ? 'hidden' : '' ?>">
    <div class="modal-content customer-otp-card">
        <div class="modal-header">
            <h3><i class="fa-solid fa-mobile-screen-button"></i> Mobile OTP Verification</h3>
        </div>
        <div class="modal-body">
            <div class="otp-brand-icon">
                <i class="fa-solid fa-shield-halved"></i>
            </div>
            
            <p class="text-center text-muted" style="font-size:0.85rem; margin-bottom:16px;">
                Welcome to <strong>Agam Restaurant</strong>! Enter your mobile number to receive an OTP & view the menu for <strong><?= htmlspecialchars($table['table_number']) ?></strong>.
            </p>

            <!-- STEP 1: Enter Mobile Number -->
            <form id="form-send-otp" class="otp-form-step">
                <div class="form-group">
                    <label for="mobile-number-input"><i class="fa-solid fa-phone"></i> Mobile Number (+91)</label>
                    <div class="input-with-icon">
                        <i class="fa-solid fa-mobile"></i>
                        <input type="tel" id="mobile-number-input" name="mobile" placeholder="Enter 10-digit mobile number..." maxlength="10" required autofocus class="form-control">
                    </div>
                </div>
                <button type="submit" id="btn-send-otp" class="btn btn-primary btn-block btn-lg">
                    Send OTP <i class="fa-solid fa-paper-plane"></i>
                </button>
            </form>

            <!-- STEP 2: Enter OTP Code & Table Number Confirmation -->
            <form id="form-verify-otp" class="otp-form-step hidden" style="margin-top:14px;">
                <div class="alert alert-info" style="background:#eff6ff; color:#1e40af; border:1px solid #dbeafe; padding:8px 12px; border-radius:8px; font-size:0.8rem; margin-bottom:12px; text-align:center;">
                    <i class="fa-solid fa-circle-info"></i> Demo OTP Sent: <strong style="font-size:1rem; color:#1d4ed8;">1234</strong>
                </div>

                <div class="form-group">
                    <label for="otp-code-input"><i class="fa-solid fa-key"></i> 4-Digit OTP Code</label>
                    <input type="text" id="otp-code-input" name="otp" placeholder="Enter 1234" maxlength="4" required class="form-control" style="text-align:center; font-size:1.4rem; letter-spacing:8px; font-weight:800;">
                </div>

                <div class="form-group">
                    <label for="select-table-id"><i class="fa-solid fa-chair"></i> Confirm Table Number</label>
                    <select id="select-table-id" name="table_id" class="form-control" required>
                        <?php foreach ($allTables as $t): ?>
                            <option value="<?= $t['id'] ?>" <?= $t['id'] == $table['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($t['table_number']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <button type="submit" id="btn-verify-otp" class="btn btn-success btn-block btn-lg">
                    <i class="fa-solid fa-circle-check"></i> Verify OTP & View Menu
                </button>
            </form>
        </div>
    </div>
</div>

<div class="customer-container">
    <!-- Hero Promo Banner (Inspired by Reference Image Burger Promo) -->
    <div class="hero-promo-banner">
        <div class="promo-content">
            <span class="promo-badge"><i class="fa-solid fa-fire"></i> Agam Special</span>
            <h2 class="promo-title">Paneer Tikka & Biryani Combo</h2>
            <p class="promo-subtitle">Get 50% OFF on all gourmet combos ordered today from Table <strong><?= htmlspecialchars($table['table_number']) ?></strong>.</p>
        </div>
        <img src="https://images.unsplash.com/photo-1563379091339-03b21ab4a4f8?auto=format&fit=crop&w=400&q=80" alt="Special Biryani" class="promo-image">
    </div>

    <!-- Quick Action Grid (Matching Reference App 6-Icon Navigation Grid) -->
    <div class="quick-actions-grid">
        <button class="action-card-btn" data-action="menu">
            <div class="action-card-icon"><i class="fa-solid fa-utensils"></i></div>
            <span class="action-card-label">Menu</span>
        </button>

        <button class="action-card-btn" data-action="today-special">
            <div class="action-card-icon"><i class="fa-solid fa-star"></i></div>
            <span class="action-card-label">Today Special</span>
        </button>

        <button class="action-card-btn" data-action="chef-special">
            <div class="action-card-icon"><i class="fa-solid fa-user-chef"></i></div>
            <span class="action-card-label">Chef Special</span>
        </button>

        <button class="action-card-btn" data-action="audio">
            <div class="action-card-icon"><i class="fa-solid fa-headphones"></i></div>
            <span class="action-card-label">Audio Guide</span>
        </button>

        <button class="action-card-btn" data-action="video">
            <div class="action-card-icon"><i class="fa-solid fa-circle-play"></i></div>
            <span class="action-card-label">Kitchen Video</span>
        </button>

        <button class="action-card-btn" data-action="about">
            <div class="action-card-icon"><i class="fa-solid fa-circle-info"></i></div>
            <span class="action-card-label">About Agam</span>
        </button>
    </div>

    <!-- Active Order Tracker Section (Real-time live status updates) -->
    <div id="order-tracker-container" class="order-tracker-container <?= $hasActiveOrder ? '' : 'hidden' ?>">
        <div class="card order-tracker-card">
            <div class="tracker-header">
                <div class="tracker-title">
                    <i class="fa-solid fa-clock-rotate-left pulse-icon"></i>
                    <span>Live Order Tracker</span>
                </div>
                <span id="tracker-order-id" class="order-number">Order #<?= $hasActiveOrder ? $activeOrder['id'] : '' ?></span>
            </div>

            <!-- 4-Step Progress Bar -->
            <div class="stepper-wrapper">
                <div id="step-placed" class="stepper-item <?= $hasActiveOrder && in_array($activeOrder['status'], ['placed', 'accepted', 'ready', 'served']) ? 'completed active' : '' ?>">
                    <div class="step-counter"><i class="fa-solid fa-paper-plane"></i></div>
                    <div class="step-name">Placed</div>
                </div>
                <div id="step-accepted" class="stepper-item <?= $hasActiveOrder && in_array($activeOrder['status'], ['accepted', 'ready', 'served']) ? 'completed active' : '' ?>">
                    <div class="step-counter"><i class="fa-solid fa-fire-burner"></i></div>
                    <div class="step-name">Preparing</div>
                </div>
                <div id="step-ready" class="stepper-item <?= $hasActiveOrder && in_array($activeOrder['status'], ['ready', 'served']) ? 'completed active' : '' ?>">
                    <div class="step-counter"><i class="fa-solid fa-bell"></i></div>
                    <div class="step-name">Ready</div>
                </div>
                <div id="step-served" class="stepper-item <?= $hasActiveOrder && $activeOrder['status'] === 'served' ? 'completed active' : '' ?>">
                    <div class="step-counter"><i class="fa-solid fa-utensils"></i></div>
                    <div class="step-name">Served</div>
                </div>
            </div>

            <div id="tracker-status-message" class="status-message">
                <?php
                if ($hasActiveOrder) {
                    switch ($activeOrder['status']) {
                        case 'placed':
                            echo '<i class="fa-solid fa-spinner fa-spin"></i> Order received! Waiting for kitchen to accept...';
                            break;
                        case 'accepted':
                            echo '<i class="fa-solid fa-utensils"></i> Agam Chef is preparing your delicious meal!';
                            break;
                        case 'ready':
                            echo '<i class="fa-solid fa-check-circle pulse-text"></i> Order is ready! Waiter is bringing it to your table.';
                            break;
                        case 'served':
                            echo '<i class="fa-solid fa-heart"></i> Served! You can order more dishes anytime or request the bill from waiter.';
                            break;
                    }
                }
                ?>
            </div>

            <!-- Active Order Items Details Accordion -->
            <details class="order-details-accordion">
                <summary>View Active Order Items & Running Bill Summary</summary>
                <div id="tracker-items-list" class="tracker-items-list">
                    <?php if ($hasActiveOrder && !empty($activeOrder['items'])): ?>
                        <?php foreach ($activeOrder['items'] as $it): ?>
                            <div class="tracker-item-row">
                                <span class="qty-badge"><?= $it['quantity'] ?>x</span>
                                <span class="item-name"><?= htmlspecialchars($it['dish_name']) ?></span>
                                <span class="item-price">₹<?= number_format($it['price_at_order'] * $it['quantity'], 2) ?></span>
                            </div>
                        <?php endforeach; ?>
                        <div class="tracker-total-row">
                            <span>Running Session Total</span>
                            <strong>₹<?= number_format($activeOrder['session_total'] ?? $activeOrder['total_amount'], 2) ?></strong>
                        </div>
                    <?php endif; ?>
                </div>
            </details>

            <div class="tracker-actions" style="margin-top:12px; display:flex; gap:10px;">
                <button id="btn-open-request-bill-modal" class="btn btn-warning btn-block">
                    <i class="fa-solid fa-receipt"></i> View Session Bill & Request Waiter
                </button>
            </div>
        </div>
    </div>

    <!-- Category Filter Tabs Navigation -->
    <div class="category-tabs-wrapper">
        <div class="category-tabs">
            <button class="category-tab active" data-category="all">
                <i class="fa-solid fa-border-all"></i> All Menu
            </button>
            <?php foreach (array_keys($categories) as $catName): ?>
                <button class="category-tab" data-category="<?= htmlspecialchars($getCatSlug($catName)) ?>">
                    <?= htmlspecialchars($catName) ?>
                </button>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Menu Items List Grouped by Category (Horizontal card layout matching reference) -->
    <div class="menu-grid">
        <?php foreach ($categories as $catName => $dishList): ?>
            <div class="category-section" data-cat-section="<?= htmlspecialchars($getCatSlug($catName)) ?>">

                <h3 class="category-title"><?= htmlspecialchars($catName) ?></h3>
                <div class="dishes-container">
                    <?php foreach ($dishList as $dish): ?>
                        <div class="dish-card" data-dish-id="<?= $dish['id'] ?>" data-name="<?= htmlspecialchars($dish['name']) ?>" data-price="<?= $dish['price'] ?>">
                            <div class="dish-image-wrapper">
                                <img src="<?= !empty($dish['image_url']) ? htmlspecialchars($dish['image_url']) : 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=400&q=80' ?>" alt="<?= htmlspecialchars($dish['name']) ?>" loading="lazy">
                            </div>
                            <div class="dish-details">
                                <div class="dish-header">
                                    <h4 class="dish-name"><?= htmlspecialchars($dish['name']) ?></h4>
                                    <span class="dish-price">₹<?= number_format($dish['price'], 2) ?></span>
                                </div>
                                <p class="dish-desc"><?= htmlspecialchars($dish['description']) ?></p>
                                
                                <div class="dish-action-row">
                                    <div class="quantity-control" data-dish-id="<?= $dish['id'] ?>">
                                        <button class="qty-btn btn-minus" disabled><i class="fa-solid fa-minus"></i></button>
                                        <span class="qty-count">0</span>
                                        <button class="qty-btn btn-plus"><i class="fa-solid fa-plus"></i></button>
                                    </div>
                                    <button class="btn btn-add-cart" data-dish-id="<?= $dish['id'] ?>" title="Add to Order">
                                        <i class="fa-solid fa-cart-shopping"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Floating Bottom Bar -->
<div id="cart-floating-bar" class="cart-floating-bar hidden">
    <div class="cart-summary-info">
        <span id="cart-item-count" class="cart-badge">0 items</span>
        <div class="cart-price-group">
            <span class="total-label">Cart Subtotal:</span>
            <span id="cart-total-price" class="total-amount">₹0.00</span>
        </div>
    </div>
    <button id="btn-open-cart" class="btn btn-view-cart">
        View Order <i class="fa-solid fa-arrow-right"></i>
    </button>
</div>

<!-- Cart Modal Overlay -->
<div id="cart-modal" class="modal-overlay hidden">
    <div class="modal-content cart-modal-content">
        <div class="modal-header">
            <h3><i class="fa-solid fa-basket-shopping"></i> View Order Summary</h3>
            <button id="btn-close-cart" class="close-btn"><i class="fa-solid fa-xmark"></i></button>
        </div>

        <div class="modal-body">
            <div class="cart-table-info">
                <i class="fa-solid fa-chair"></i> Order for <?= htmlspecialchars($table['table_number']) ?>
            </div>
            
            <div id="cart-items-container" class="cart-items-container">
                <!-- Injected dynamically by JS -->
            </div>
        </div>

        <div class="modal-footer">
            <div class="cart-summary-line">
                <span>Total Amount</span>
                <span id="modal-cart-total">₹0.00</span>
            </div>
            <button id="btn-place-order" class="btn btn-primary btn-block btn-lg">
                <i class="fa-solid fa-paper-plane"></i> Send Order to Kitchen
            </button>
        </div>
    </div>
</div>

<!-- Customer Request Bill Modal Overlay -->
<div id="customer-bill-modal" class="modal-overlay hidden">
    <div class="modal-content">
        <div class="modal-header" style="background:var(--emenu-charcoal); color:#fff;">
            <h3><i class="fa-solid fa-receipt"></i> Dining Session Bill Summary</h3>
            <button id="btn-close-customer-bill-modal" class="close-btn"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="modal-body">
            <div class="cart-table-info" style="margin-bottom:12px; font-weight:700;">
                <i class="fa-solid fa-chair"></i> Table Session: <?= htmlspecialchars($table['table_number']) ?>
            </div>
            <div id="customer-bill-items-list" class="cart-items-container" style="max-height:220px; overflow-y:auto;">
                <!-- Injected by JS -->
            </div>
            <div style="display:flex; justify-content:space-between; margin-top:16px; font-weight:800; font-size:1.2rem; color:var(--primary-gold); padding:10px 0; border-top:2px dashed #cbd5e1;">
                <span>Total Payable:</span>
                <span id="customer-bill-total-price">₹0.00</span>
            </div>
            <div id="bill-request-status-badge" class="alert alert-info hidden" style="text-align:center; font-weight:700; margin-top:10px;">
                <i class="fa-solid fa-bell pulse-icon"></i> Waiter Intimated! Waiter will bring bill / payment terminal to your table.
            </div>
        </div>
        <div class="modal-footer">
            <button id="btn-submit-bill-request" class="btn btn-warning btn-block btn-lg">
                <i class="fa-solid fa-bell"></i> Intimate Waiter for Bill & Payment
            </button>
        </div>
    </div>
</div>

<!-- Customer Thank You Farewell Greeting Overlay -->
<div id="customer-thankyou-modal" class="modal-overlay hidden">
    <div class="modal-content" style="text-align:center; padding:30px 20px; max-width:440px;">
        <div class="thankyou-icon" style="font-size:4.5rem; color:#10b981; margin-bottom:14px; animation: bounce 1.5s infinite;">
            <i class="fa-solid fa-circle-check"></i>
        </div>
        <h2 style="font-family:'Outfit',sans-serif; color:var(--emenu-charcoal); margin-bottom:8px; font-weight:800;">
            Thank You for Dining with Us!
        </h2>
        <h4 style="color:var(--emenu-gold); margin-bottom:16px; font-weight:700;">
            Visit Chennai Vasantha Bhavan Again! 🙏
        </h4>
        <p style="color:var(--text-muted); font-size:0.9rem; margin-bottom:20px;">
            Your payment was successfully collected by waiter. We hope you enjoyed our authentic vegetarian delicacies!
        </p>

        <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px; padding:16px; margin-bottom:20px;">
            <span style="font-size:0.85rem; color:var(--text-muted); display:block;">Paid Total Amount</span>
            <h3 id="thankyou-paid-total" style="font-size:1.6rem; color:#1e293b; margin:4px 0 0 0;">₹0.00</h3>
        </div>

        <button id="btn-restart-dining-session" class="btn btn-primary btn-block btn-lg">
            <i class="fa-solid fa-rotate"></i> Start New Order / Table Session
        </button>
    </div>
</div>

<!-- Info Feature Modals -->
<div id="info-modal" class="modal-overlay hidden">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="info-modal-title"><i class="fa-solid fa-star"></i> Feature Highlight</h3>
            <button id="btn-close-info-modal" class="close-btn"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="modal-body" id="info-modal-body" style="text-align: center; padding: 30px;">
            <!-- Dynamic content -->
        </div>
    </div>
</div>

<script>
    window.TABLE_ID = <?= (int)$table['id'] ?>;
    window.CURRENT_ORDER_ID = <?= $hasActiveOrder ? (int)$activeOrder['id'] : 'null' ?>;
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/layout.php';
?>
