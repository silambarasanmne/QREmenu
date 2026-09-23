<?php
use App\Models\Table;
use App\Models\Dish;
use App\Services\QrCodeService;

$title = "Agam eMenu — Digital QR Ordering Platform Hub";
$tables = Table::all();
$dishCount = count(Dish::all(true));

$selectedTableId = (int)($_GET['table'] ?? 1);
$currentTable = Table::find($selectedTableId) ?: ($tables[0] ?? ['id' => 1, 'table_number' => 'Table 1']);

$host = $_SERVER['HTTP_HOST'] ?? '127.0.0.1:8000';
$tableUrl = 'http://' . $host . '/table/' . $currentTable['id'];
$qrUri = QrCodeService::generateSvgDataUri($tableUrl);

ob_start();
?>

<div class="portal-wrapper">
    <!-- Hero Section with Glowing Brand & System Status -->
    <header class="portal-hero-card">
        <div class="hero-glowing-bg"></div>
        <div class="hero-content">
            <div class="hero-brand-group">
                <div class="hero-logo-box">
                    <i class="fa-solid fa-utensils logo-icon"></i>
                </div>
                <div class="hero-titles">
                    <h1 class="brand-name">Agam<span class="brand-gold">eMenu</span></h1>
                    <p class="brand-tagline">Smart Digital QR Ordering & Restaurant Operations Platform</p>
                </div>
            </div>

            <div class="hero-status-bar">
                <span class="status-pill status-pill--live">
                    <span class="pulse-dot"></span> System Engine Live
                </span>
                <span class="status-pill">
                    <i class="fa-solid fa-chair text-gold"></i> <?= count($tables) ?> Tables Active
                </span>
                <span class="status-pill">
                    <i class="fa-solid fa-bowl-food text-gold"></i> <?= $dishCount ?> Menu Dishes
                </span>
                <span class="status-pill">
                    <i class="fa-solid fa-indian-rupee-sign text-gold"></i> INR ₹ Currency
                </span>
            </div>
        </div>
    </header>

    <!-- Main Role Cards Grid -->
    <main class="portal-cards-grid">
        
        <!-- CARD 1: Customer Digital QR Menu -->
        <article class="role-card role-card--customer">
            <div class="card-accent-bar"></div>
            <div class="card-header">
                <div class="role-icon-box role-icon-box--customer">
                    <i class="fa-solid fa-qrcode"></i>
                </div>
                <div class="role-title-group">
                    <h2>Customer Menu & QR</h2>
                    <span class="role-badge">Touchless Ordering</span>
                </div>
            </div>

            <div class="card-body">
                <p class="card-description">
                    Customers scan table QR code to view live menu, customize dishes, track order status in real-time, and request bills.
                </p>

                <!-- Interactive Table Selector Chips -->
                <div class="table-selector-box">
                    <span class="selector-label"><i class="fa-solid fa-chair"></i> Select Demo Table:</span>
                    <div class="table-chips">
                        <?php foreach ($tables as $t): ?>
                            <a href="/?table=<?= $t['id'] ?>" class="table-chip <?= $t['id'] == $currentTable['id'] ? 'active' : '' ?>">
                                <?= htmlspecialchars($t['table_number']) ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Dynamic QR Code Preview Box -->
                <div class="qr-preview-box">
                    <div class="qr-image-wrapper">
                        <img src="<?= $qrUri ?>" alt="QR Code for <?= htmlspecialchars($currentTable['table_number']) ?>" class="qr-img">
                    </div>
                    <div class="qr-info">
                        <strong><?= htmlspecialchars($currentTable['table_number']) ?> QR Code</strong>
                        <small class="text-muted">Scan with phone camera or click to open</small>
                        <code class="qr-url-code"><?= htmlspecialchars($tableUrl) ?></code>
                    </div>
                </div>

                <!-- Feature Highlights List -->
                <ul class="role-features-list">
                    <li><i class="fa-solid fa-check text-success"></i> Mobile OTP Login Verification</li>
                    <li><i class="fa-solid fa-check text-success"></i> Categorized Indian Menu (₹)</li>
                    <li><i class="fa-solid fa-check text-success"></i> 4-Step Live Kitchen Tracker</li>
                    <li><i class="fa-solid fa-check text-success"></i> View Session Bill & Request Waiter</li>
                </ul>
            </div>

            <div class="card-footer">
                <a href="/table/<?= $currentTable['id'] ?>" class="btn-role-action btn-role-action--customer">
                    Launch <?= htmlspecialchars($currentTable['table_number']) ?> Menu <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>
        </article>

        <!-- CARD 2: Kitchen Display System (KDS) -->
        <article class="role-card role-card--kitchen">
            <div class="card-accent-bar"></div>
            <div class="card-header">
                <div class="role-icon-box role-icon-box--kitchen">
                    <i class="fa-solid fa-fire-burner"></i>
                </div>
                <div class="role-title-group">
                    <h2>Kitchen Display (KDS)</h2>
                    <span class="role-badge role-badge--orange">Chef Portal</span>
                </div>
            </div>

            <div class="card-body">
                <p class="card-description">
                    Real-time KDS screen for kitchen staff to receive instant customer orders, manage food preparation stages, and alert waiters when dishes are ready.
                </p>

                <div class="pin-display-box">
                    <i class="fa-solid fa-shield-halved"></i> Access PIN: <strong class="pin-code">1234</strong>
                </div>

                <!-- Workflow Step Pipeline Preview -->
                <div class="workflow-preview-box">
                    <div class="wf-step wf-step--placed">
                        <i class="fa-solid fa-paper-plane"></i>
                        <span>New Order</span>
                    </div>
                    <i class="fa-solid fa-chevron-right wf-arrow"></i>
                    <div class="wf-step wf-step--accepted">
                        <i class="fa-solid fa-fire-burner"></i>
                        <span>Preparing</span>
                    </div>
                    <i class="fa-solid fa-chevron-right wf-arrow"></i>
                    <div class="wf-step wf-step--ready">
                        <i class="fa-solid fa-bell"></i>
                        <span>Dish Ready</span>
                    </div>
                </div>

                <ul class="role-features-list">
                    <li><i class="fa-solid fa-check text-success"></i> Auto Live Polling (Every 4s)</li>
                    <li><i class="fa-solid fa-check text-success"></i> Visual Flashing Order Cards</li>
                    <li><i class="fa-solid fa-check text-success"></i> Table Number & Items Summary</li>
                    <li><i class="fa-solid fa-check text-success"></i> One-Click Accept & Ready Actions</li>
                </ul>
            </div>

            <div class="card-footer">
                <a href="/kitchen" class="btn-role-action btn-role-action--kitchen">
                    Open Kitchen Screen <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>
        </article>

        <!-- CARD 3: Waiter Floor Operations -->
        <article class="role-card role-card--waiter">
            <div class="card-accent-bar"></div>
            <div class="card-header">
                <div class="role-icon-box role-icon-box--waiter">
                    <i class="fa-solid fa-bell-concierge"></i>
                </div>
                <div class="role-title-group">
                    <h2>Waiter Floor Console</h2>
                    <span class="role-badge role-badge--green">Floor Staff</span>
                </div>
            </div>

            <div class="card-body">
                <p class="card-description">
                    Floor management dashboard for waiters to get instant alerts for kitchen-ready dishes, serve food to tables, handle bill intimations, and close customer sessions.
                </p>

                <div class="pin-display-box">
                    <i class="fa-solid fa-shield-halved"></i> Access PIN: <strong class="pin-code">1234</strong>
                </div>

                <!-- Waiter Alert Highlights -->
                <div class="waiter-preview-box">
                    <div class="waiter-alert-chip alert-chip--ready">
                        <i class="fa-solid fa-bell pulse-icon"></i> Ready Dishes Alert
                    </div>
                    <div class="waiter-alert-chip alert-chip--bill">
                        <i class="fa-solid fa-receipt"></i> Bill Requested Intimation
                    </div>
                </div>

                <ul class="role-features-list">
                    <li><i class="fa-solid fa-check text-success"></i> Floor Table Grid Status (Available/Occupied)</li>
                    <li><i class="fa-solid fa-check text-success"></i> Mark Dishes Served to Table</li>
                    <li><i class="fa-solid fa-check text-success"></i> Cumulative Running Session Bill Summary</li>
                    <li><i class="fa-solid fa-check text-success"></i> Close Bill & Clear Table Session</li>
                </ul>
            </div>

            <div class="card-footer">
                <a href="/waiter" class="btn-role-action btn-role-action--waiter">
                    Open Waiter Screen <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>
        </article>

        <!-- CARD 4: Owner Business Intelligence -->
        <article class="role-card role-card--owner">
            <div class="card-accent-bar"></div>
            <div class="card-header">
                <div class="role-icon-box role-icon-box--owner">
                    <i class="fa-solid fa-chart-line"></i>
                </div>
                <div class="role-title-group">
                    <h2>Owner & Analytics</h2>
                    <span class="role-badge role-badge--purple">Management</span>
                </div>
            </div>

            <div class="card-body">
                <p class="card-description">
                    Comprehensive executive dashboard for revenue analytics, sales trends, menu dish pricing & availability management, table QR code generation, and PDF report downloads.
                </p>

                <div class="pin-display-box">
                    <i class="fa-solid fa-shield-halved"></i> Access PIN: <strong class="pin-code">admin123</strong>
                </div>

                <!-- Metrics Preview Pill Grid -->
                <div class="metrics-preview-grid">
                    <div class="metric-mini-pill">
                        <i class="fa-solid fa-indian-rupee-sign text-gold"></i> Total Sales Revenue
                    </div>
                    <div class="metric-mini-pill">
                        <i class="fa-solid fa-calculator text-primary"></i> AOV & Order Analytics
                    </div>
                    <div class="metric-mini-pill">
                        <i class="fa-solid fa-file-pdf text-danger"></i> Agam PDF Report Generator
                    </div>
                </div>

                <ul class="role-features-list">
                    <li><i class="fa-solid fa-check text-success"></i> Real-Time Sales Metrics & Charts</li>
                    <li><i class="fa-solid fa-check text-success"></i> Date Range Presets (Today, Month, Custom)</li>
                    <li><i class="fa-solid fa-check text-success"></i> Full Menu Item CRUD & Price Control</li>
                    <li><i class="fa-solid fa-check text-success"></i> Printable PDF Business Report</li>
                </ul>
            </div>

            <div class="card-footer">
                <a href="/owner" class="btn-role-action btn-role-action--owner">
                    Open Owner Dashboard <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>
        </article>

    </main>

    <!-- Bottom Feature Highlights & Tech Stack Footer -->
    <footer class="portal-bottom-bar">
        <div class="highlights-grid">
            <div class="highlight-item">
                <div class="highlight-icon"><i class="fa-solid fa-bolt"></i></div>
                <div class="highlight-text">
                    <h4>Instant Real-Time Polling</h4>
                    <p>4-second automated synchronization between customer, kitchen, and waiter screens.</p>
                </div>
            </div>

            <div class="highlight-item">
                <div class="highlight-icon"><i class="fa-solid fa-shield-check"></i></div>
                <div class="highlight-text">
                    <h4>Role Security</h4>
                    <p>OTP mobile verification for guests, secure PIN logins for kitchen, waiter, and owner staff.</p>
                </div>
            </div>

            <div class="highlight-item">
                <div class="highlight-icon"><i class="fa-solid fa-mobile-retro"></i></div>
                <div class="highlight-text">
                    <h4>Fully Responsive</h4>
                    <p>Engineered for smartphones, tablets, handheld POS devices, and high-res desktop screens.</p>
                </div>
            </div>
        </div>

        <div class="portal-copyright-row">
            <p>© <?= date('Y') ?> <strong>Agam</strong>. All rights reserved. Powered by <strong>Agam eMenu Platform</strong>.</p>
        </div>
    </footer>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/layout.php';
?>
