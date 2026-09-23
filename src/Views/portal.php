<?php
$title = "Agam eMenu — Digital QR Ordering Platform";
ob_start();
?>
<div class="portal-container">
    <div class="portal-hero">
        <div class="portal-brand">
            <div class="portal-logo-circle">
                <i class="fa-solid fa-utensils"></i>
            </div>
            <h1 class="portal-title">Agam<span class="accent">eMenu</span></h1>
            <p class="portal-subtitle">Smart Digital QR Ordering Platform</p>
        </div>
    </div>

    <div class="portal-grid">
        <a href="/table/1" class="portal-card portal-card--customer">
            <div class="portal-card-icon">
                <i class="fa-solid fa-mobile-screen-button"></i>
            </div>
            <h2 class="portal-card-title">Customer Menu</h2>
            <p class="portal-card-desc">Scan QR, browse menu & place orders</p>
            <div class="portal-card-qr">
                <?php 
                $qrUri = \App\Services\QrCodeService::generateSvgDataUri('http://' . $_SERVER['HTTP_HOST'] . '/table/1');
                echo '<img src="' . $qrUri . '" alt="Scan for Table 1">';
                ?>
            </div>
            <span class="portal-card-badge">Table 1 Demo</span>
        </a>

        <a href="/kitchen" class="portal-card portal-card--kitchen">
            <div class="portal-card-icon">
                <i class="fa-solid fa-fire-burner"></i>
            </div>
            <h2 class="portal-card-title">Kitchen Display</h2>
            <p class="portal-card-desc">Real-time order tracking for chefs</p>
            <span class="portal-card-pin"><i class="fa-solid fa-key"></i> PIN: 1234</span>
        </a>

        <a href="/waiter" class="portal-card portal-card--waiter">
            <div class="portal-card-icon">
                <i class="fa-solid fa-bell-concierge"></i>
            </div>
            <h2 class="portal-card-title">Waiter Dashboard</h2>
            <p class="portal-card-desc">Serve dishes, manage tables & close bills</p>
            <span class="portal-card-pin"><i class="fa-solid fa-key"></i> PIN: 1234</span>
        </a>

        <a href="/owner" class="portal-card portal-card--owner">
            <div class="portal-card-icon">
                <i class="fa-solid fa-chart-line"></i>
            </div>
            <h2 class="portal-card-title">Owner Analytics</h2>
            <p class="portal-card-desc">Revenue insights, dish management & reports</p>
            <span class="portal-card-pin"><i class="fa-solid fa-key"></i> PIN: admin123</span>
        </a>
    </div>

    <div class="portal-footer">
        <p>© <?= date('Y') ?> <strong>Agam</strong> — All rights reserved. Powered by Agam eMenu.</p>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/layout.php';
?>
