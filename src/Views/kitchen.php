<?php
$title = "Kitchen Display System (KDS)";
$bodyClass = "kitchen-page";
$headerRightHtml = '
    <button id="btn-toggle-audio" class="btn btn-outline btn-sm"><i class="fa-solid fa-volume-high"></i> Sound On</button>
    <a href="/api/logout" class="btn btn-danger btn-sm"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
';

ob_start();
?>

<div class="kitchen-container">
    <div class="kds-top-bar">
        <div class="kds-title">
            <h2><i class="fa-solid fa-fire-burner"></i> Kitchen Display System</h2>
            <span class="live-indicator"><span class="pulse-dot"></span> LIVE POLLING</span>
        </div>
        <div class="kds-stats">
            <span class="stat-pill"><strong id="kds-pending-count">0</strong> Pending</span>
            <span class="stat-pill warning"><strong id="kds-cooking-count">0</strong> In Prep</span>
        </div>
    </div>

    <!-- Kitchen Orders Grid Container -->
    <div id="kitchen-orders-grid" class="kitchen-orders-grid">
        <div class="empty-kds-message">
            <i class="fa-solid fa-utensils fa-3x"></i>
            <p>No active orders right now. Waiting for new orders...</p>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/layout.php';
?>
