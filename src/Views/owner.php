<?php
$title = "Owner Dashboard & Analytics";
$bodyClass = "owner-page";
$headerRightHtml = '<a href="/api/logout" class="btn btn-danger btn-sm"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>';

$maxTableNum = 0;
if (!empty($tables)) {
    foreach ($tables as $t) {
        if (preg_match('/(?:Table\s*)?(\d+)/i', $t['table_number'], $m)) {
            $num = (int)$m[1];
            if ($num > $maxTableNum) $maxTableNum = $num;
        }
    }
}
$nextTableStart = $maxTableNum + 1;

// Pass Chart.js library to layout
$extraJs = '<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>';

ob_start();

?>

<div class="owner-container">
    <!-- Owner Top Navigation Tabs -->
    <div class="owner-tabs">
        <button class="owner-tab active" data-target="tab-analytics">
            <i class="fa-solid fa-chart-pie"></i> Revenue & Analytics
        </button>
        <button class="owner-tab" data-target="tab-dishes">
            <i class="fa-solid fa-utensils"></i> Menu Management
        </button>
        <button class="owner-tab" data-target="tab-tables">
            <i class="fa-solid fa-qrcode"></i> Tables & QR Codes
        </button>
    </div>

    <!-- TAB 1: Revenue & Analytics -->
    <div id="tab-analytics" class="owner-tab-content active">
        <!-- Analytics Header & Action Bar -->
        <div class="analytics-header-card card">
            <div class="analytics-header-left">
                <h2><i class="fa-solid fa-chart-line text-primary"></i> Revenue & Business Analytics</h2>
                <p class="text-muted">Real-time financial performance and live order activity.</p>
            </div>
            
            <div class="analytics-header-right">
                <button type="button" class="btn btn-primary btn-sm" id="btn-print-report" onclick="window.printReport()">
                    <i class="fa-solid fa-file-pdf"></i> Print / Download PDF
                </button>
            </div>
        </div>

        <!-- Date Range Presets & Compact Filter Bar -->
        <div class="card filter-card compact-filter-card">
            <form method="GET" action="/owner" class="date-filter-form compact-form" id="owner-date-filter-form">
                <div class="filter-row-compact">
                    <div class="filter-presets-group">
                        <span class="filter-label"><i class="fa-solid fa-calendar-days"></i> Quick Presets:</span>
                        <a href="/owner" class="preset-btn <?= (empty($stats['start_date']) && empty($stats['end_date'])) ? 'active' : '' ?>">All Time</a>
                        <a href="/owner?start_date=<?= date('Y-m-d') ?>&end_date=<?= date('Y-m-d') ?>" class="preset-btn <?= ($stats['start_date'] === date('Y-m-d') && $stats['end_date'] === date('Y-m-d')) ? 'active' : '' ?>">Today</a>
                        <a href="/owner?start_date=<?= date('Y-m-d', strtotime('-7 days')) ?>&end_date=<?= date('Y-m-d') ?>" class="preset-btn">Last 7 Days</a>
                        <a href="/owner?start_date=<?= date('Y-m-01') ?>&end_date=<?= date('Y-m-t') ?>" class="preset-btn">This Month</a>
                    </div>

                    <div class="filter-custom-group">
                        <span class="filter-label">Custom:</span>
                        <input type="date" name="start_date" value="<?= htmlspecialchars($stats['start_date'] ?? '') ?>" class="form-control form-control-sm date-input-sm">
                        <span>to</span>
                        <input type="date" name="end_date" value="<?= htmlspecialchars($stats['end_date'] ?? '') ?>" class="form-control form-control-sm date-input-sm">
                        <button type="submit" class="btn btn-primary btn-sm btn-filter-sm"><i class="fa-solid fa-filter"></i> Filter</button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Metrics Summary Cards Grid -->
        <div class="stats-cards-grid">
            <div class="stat-card primary">
                <div class="stat-icon"><i class="fa-solid fa-indian-rupee-sign"></i></div>
                <div class="stat-info">
                    <span class="stat-label">Total Revenue</span>
                    <h3 class="stat-value" id="owner-total-revenue">₹<?= number_format($stats['total_revenue'], 2) ?></h3>
                    <small class="stat-subtext"><i class="fa-solid fa-circle-check text-success"></i> Served & Paid Sales</small>
                </div>
            </div>

            <div class="stat-card success">
                <div class="stat-icon"><i class="fa-solid fa-receipt"></i></div>
                <div class="stat-info">
                    <span class="stat-label">Total Paid Orders</span>
                    <h3 class="stat-value" id="owner-total-orders"><?= $stats['total_orders'] ?></h3>
                    <small class="stat-subtext">Completed transactions</small>
                </div>
            </div>

            <div class="stat-card info">
                <div class="stat-icon"><i class="fa-solid fa-calculator"></i></div>
                <div class="stat-info">
                    <span class="stat-label">Avg Order Value (AOV)</span>
                    <h3 class="stat-value" id="owner-avg-order-value">₹<?= number_format($stats['avg_order_value'], 2) ?></h3>
                    <small class="stat-subtext">Average spend per table</small>
                </div>
            </div>

            <div class="stat-card warning">
                <div class="stat-icon"><i class="fa-solid fa-sun"></i></div>
                <div class="stat-info">
                    <span class="stat-label">Revenue Today</span>
                    <h3 class="stat-value" id="owner-revenue-today">₹<?= number_format($stats['revenue_today'], 2) ?></h3>
                    <small id="owner-orders-today" class="stat-subtext"><?= $stats['orders_today'] ?> orders today</small>
                </div>
            </div>

            <div class="stat-card accent">
                <div class="stat-icon"><i class="fa-solid fa-chair"></i></div>
                <div class="stat-info">
                    <span class="stat-label">Table Occupancy</span>
                    <h3 class="stat-value" id="owner-table-occupancy"><?= $stats['table_metrics']['occupancy_rate'] ?>%</h3>
                    <small id="owner-table-occupancy-sub" class="stat-subtext"><?= $stats['table_metrics']['occupied_tables'] ?> of <?= $stats['table_metrics']['total_tables'] ?> tables occupied</small>
                </div>
            </div>
        </div>

        <!-- Live Recent Orders Stream with Mini Scrollbar -->
        <div style="margin-top: 24px;">
            <div class="card analytics-card" id="owner-recent-orders-container">
                <div class="card-header-flex">
                    <h3><i class="fa-solid fa-clock-rotate-left text-primary"></i> Live Recent Orders</h3>
                    <span class="badge badge-success"><i class="fa-solid fa-rotate"></i> Auto Refresh</span>
                </div>

                <?php if (!empty($stats['recent_orders'])): ?>
                    <div class="recent-orders-scroll-container" id="recent-orders-scroll-box">
                        <div class="recent-orders-list">
                            <?php foreach ($stats['recent_orders'] as $ord): ?>
                                <div class="recent-order-item">
                                    <div class="ro-header">
                                        <span class="ro-table"><i class="fa-solid fa-chair"></i> <?= htmlspecialchars($ord['table_number']) ?></span>
                                        <span class="badge badge-status-<?= $ord['status'] ?>"><?= strtoupper($ord['status']) ?></span>
                                    </div>

                                    <?php if (!empty($ord['items'])): ?>
                                        <div class="ro-items-summary">
                                            <?php foreach ($ord['items'] as $item): ?>
                                                <div class="ro-item-line">
                                                    <span class="ro-item-name">• <?= htmlspecialchars($item['name']) ?></span>
                                                    <span class="ro-item-qty">x<?= $item['quantity'] ?></span>
                                                    <span class="ro-item-price">₹<?= number_format($item['price_at_order'] * $item['quantity'], 2) ?></span>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>

                                    <div class="ro-meta">
                                        <span>Order #<?= $ord['id'] ?></span> &bull;
                                        <strong>Total: ₹<?= number_format($ord['total_amount'], 2) ?></strong> &bull;
                                        <small><?= date('M j, g:i a', strtotime($ord['created_at'])) ?></small>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php else: ?>
                    <p class="text-muted p-4">No orders found.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Hidden Printable Report Template for PDF / Print -->
    <div id="printable-report-template" class="print-only-container">
        <div class="print-report-header">
            <div class="print-logo-row">
                <div class="print-brand">
                    <div class="print-logo-icon"><i class="fa-solid fa-utensils"></i></div>
                    <div class="print-brand-title">Agam <span>eMenu</span></div>
                </div>
                <div class="print-company-details">
                    <h3>Hotel Agam Restaurant</h3>
                    <p>Fine Dining & QR Digital Ordering System</p>
                    <p>Contact: +91 9876543210 | info@agamrestaurant.com</p>
                </div>
            </div>
            <hr class="print-hr">
            <div class="print-report-meta">
                <h2>REVENUE & CATEGORY SALES FINANCIAL REPORT</h2>
                <div class="print-meta-grid">
                    <div><strong>Report Period:</strong> <?= htmlspecialchars($stats['period_label']) ?></div>
                    <div><strong>Generated At:</strong> <?= date('F j, Y - g:i A') ?></div>
                </div>
            </div>
        </div>

        <!-- Print Summary Overview Cards -->
        <div class="print-summary-box">
            <div class="print-stat-item">
                <span class="lbl">Total Revenue</span>
                <span class="val">₹<?= number_format($stats['total_revenue'], 2) ?></span>
            </div>
            <div class="print-stat-item">
                <span class="lbl">Total Paid Orders</span>
                <span class="val"><?= $stats['total_orders'] ?></span>
            </div>
            <div class="print-stat-item">
                <span class="lbl">Avg Order Value (AOV)</span>
                <span class="val">₹<?= number_format($stats['avg_order_value'], 2) ?></span>
            </div>
            <div class="print-stat-item">
                <span class="lbl">Revenue Today</span>
                <span class="val">₹<?= number_format($stats['revenue_today'], 2) ?></span>
            </div>
        </div>

        <!-- Category-Wise Amount & Total Breakdown Table -->
        <div class="print-section">
            <h3>Category-Wise Revenue & Sales Breakdown</h3>
            <table class="print-table">
                <thead>
                    <tr>
                        <th style="width: 5%;">#</th>
                        <th style="width: 45%;">Menu Category</th>
                        <th style="width: 20%; text-align: center;">Total Items Sold</th>
                        <th style="width: 15%; text-align: right;">Revenue Share (%)</th>
                        <th style="width: 15%; text-align: right;">Total Amount (₹)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $grandItems = 0;
                    $grandTotal = 0;
                    if (!empty($stats['category_breakdown'])):
                        foreach ($stats['category_breakdown'] as $idx => $cat): 
                            $grandItems += $cat['items_sold'];
                            $grandTotal += $cat['category_revenue'];
                    ?>
                        <tr>
                            <td><?= $idx + 1 ?></td>
                            <td><strong><?= htmlspecialchars($cat['category']) ?></strong></td>
                            <td style="text-align: center;"><?= number_format($cat['items_sold']) ?></td>
                            <td style="text-align: right;"><?= number_format($cat['percentage'], 1) ?>%</td>
                            <td style="text-align: right; font-weight: 700;">₹<?= number_format($cat['category_revenue'], 2) ?></td>
                        </tr>
                    <?php 
                        endforeach; 
                    else:
                    ?>
                        <tr><td colspan="5" style="text-align:center;">No category records found for this period.</td></tr>
                    <?php endif; ?>
                </tbody>
                <tfoot>
                    <tr class="print-total-row">
                        <td colspan="2">GRAND TOTAL AMOUNT</td>
                        <td style="text-align: center; font-weight: 800;"><?= number_format($grandItems) ?> items</td>
                        <td style="text-align: right; font-weight: 800;">100.0%</td>
                        <td style="text-align: right; font-weight: 900; font-size: 1.1rem;">₹<?= number_format($grandTotal, 2) ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="print-footer">
            <p>Report generated automatically by Agam eMenu Restaurant Management System &bull; Page 1 of 1</p>
        </div>
    </div>

    <!-- TAB 2: Menu Management -->
    <div id="tab-dishes" class="owner-tab-content">
        <div class="tab-header-action">
            <h3><i class="fa-solid fa-utensils"></i> Menu Items Directory</h3>
            <button id="btn-open-dish-modal" class="btn btn-primary">
                <i class="fa-solid fa-plus"></i> Add New Dish
            </button>
        </div>

        <div class="card">
            <table class="data-table responsive-table">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Price (₹)</th>
                        <th>Available</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($dishes as $d): ?>
                        <tr>
                            <td>
                                <img src="<?= !empty($d['image_url']) ? htmlspecialchars($d['image_url']) : 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=400&q=80' ?>" class="table-img" alt="dish">
                            </td>
                            <td>
                                <strong><?= htmlspecialchars($d['name']) ?></strong>
                                <p class="text-muted small"><?= htmlspecialchars($d['description']) ?></p>
                            </td>
                            <td><span class="badge badge-outline"><?= htmlspecialchars($d['category']) ?></span></td>
                            <td>₹<?= number_format($d['price'], 2) ?></td>
                            <td>
                                <label class="switch">
                                    <input type="checkbox" class="toggle-availability" data-id="<?= $d['id'] ?>" <?= $d['is_available'] ? 'checked' : '' ?>>
                                    <span class="slider round"></span>
                                </label>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-outline btn-edit-dish" data-dish='<?= json_encode($d, JSON_HEX_APOS) ?>'>
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>
                                <button class="btn btn-sm btn-danger btn-delete-dish" data-id="<?= $d['id'] ?>">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- TAB 3: Table & QR Code Manager -->
    <div id="tab-tables" class="owner-tab-content">
        <div class="tab-header-action" style="flex-wrap:wrap; gap:12px;">
            <h3><i class="fa-solid fa-qrcode"></i> Tables & QR Code Generator</h3>
            
            <div style="display:flex; gap:10px; flex-wrap:wrap;">
                <form action="/owner/table/create" method="POST" class="form-inline">
                    <input type="text" name="table_number" placeholder="Table Name (e.g. Table 6, Table 7)" required class="form-control" style="min-width:230px;">
                    <button type="submit" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Add Table(s)</button>
                </form>

                <button id="btn-open-batch-table-modal" class="btn btn-warning">
                    <i class="fa-solid fa-layer-group"></i> ⚡ Batch Add Multiple Tables
                </button>
            </div>
        </div>

        <div class="qr-cards-grid">
            <?php foreach ($tables as $t): ?>
                <?php $slug = strtolower(str_replace(' ', '_', $t['table_number'])); ?>
                <div class="card qr-card" id="qr-card-<?= $t['id'] ?>">
                    <div class="qr-header">
                        <h4><?= htmlspecialchars($t['table_number']) ?></h4>
                        <span class="badge badge-<?= $t['status'] === 'available' ? 'success' : 'warning' ?>">
                            <?= strtoupper($t['status']) ?>
                        </span>
                    </div>

                    <div class="qr-image-container">
                        <img src="<?= $t['qr_svg'] ?>" alt="QR Code <?= htmlspecialchars($t['table_number']) ?>" class="qr-code-img">
                    </div>

                    <div class="qr-url-text">
                        <a href="<?= htmlspecialchars($t['qr_url']) ?>" target="_blank" class="qr-link">
                            <?= htmlspecialchars($t['qr_url']) ?>
                        </a>
                    </div>

                    <div class="qr-actions">
                        <a href="/qr_codes/<?= $slug ?>_qr.png" download="<?= $slug ?>_qr.png" class="btn btn-sm btn-outline" title="Download PNG">
                            <i class="fa-solid fa-download"></i> PNG
                        </a>
                        <a href="/qr_codes/<?= $slug ?>_qr.svg" download="<?= $slug ?>_qr.svg" class="btn btn-sm btn-outline" title="Download SVG">
                            <i class="fa-solid fa-file-code"></i> SVG
                        </a>
                        <button class="btn btn-sm btn-primary btn-print-qr" data-table="<?= htmlspecialchars($t['table_number']) ?>" data-qr="<?= htmlspecialchars($t['qr_svg']) ?>" data-url="<?= htmlspecialchars($t['qr_url']) ?>">
                            <i class="fa-solid fa-print"></i> Print
                        </button>
                        <button class="btn btn-sm btn-danger btn-delete-table" data-id="<?= $t['id'] ?>">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Pass initial stats to JavaScript -->
<script>
    window.INITIAL_STATS = <?= json_encode($stats) ?>;
</script>

<!-- Add / Edit Dish Modal -->
<div id="dish-modal" class="modal-overlay hidden">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="modal-dish-title"><i class="fa-solid fa-utensils"></i> Add New Dish</h3>
            <button id="btn-close-dish-modal" class="close-btn"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form action="/owner/dish/save" method="POST">
            <div class="modal-body">
                <input type="hidden" name="id" id="dish-id">

                <div class="form-group">
                    <label>Dish Name</label>
                    <input type="text" name="name" id="dish-name" required class="form-control">
                </div>

                <div class="form-group">
                    <label><i class="fa-solid fa-list"></i> Category</label>
                    <select name="category" id="dish-category" required class="form-control">
                        <option value="Soups">Soups</option>
                        <option value="Noodles">Noodles</option>
                        <option value="Special Dosa">Special Dosa</option>
                        <option value="Breakfast & Tiffin">Breakfast & Tiffin</option>
                        <option value="Dosai Varieties">Dosai Varieties</option>
                        <option value="Gravy & Curries">Gravy & Curries</option>
                        <option value="Starters">Starters</option>
                        <option value="Rice & Pulav">Rice & Pulav</option>
                        <option value="Roti & Breads">Roti & Breads</option>
                        <option value="Meals & Variety Rice">Meals & Variety Rice</option>
                        <option value="Biryani">Biryani</option>
                        <option value="Evening & Day Specials">Evening & Day Specials</option>
                    </select>
                </div>

                <div class="form-group">
                    <label><i class="fa-solid fa-indian-rupee-sign"></i> Price (₹)</label>
                    <input type="number" step="0.01" name="price" id="dish-price" placeholder="e.g. 150.00" required class="form-control" style="font-size:1.1rem; font-weight:700;">
                </div>

                <div class="form-group">
                    <label><i class="fa-solid fa-align-left"></i> Description</label>
                    <textarea name="description" id="dish-description" rows="3" placeholder="Enter dish description..." class="form-control"></textarea>
                </div>

                <div class="form-group">
                    <label><i class="fa-solid fa-image"></i> Dish Picture / Image URL</label>
                    <input type="url" name="image_url" id="dish-image-url" placeholder="https://images.unsplash.com/..." class="form-control">
                    <div style="margin-top:8px; display:flex; gap:6px; flex-wrap:wrap; align-items:center;">
                        <small class="text-muted" style="width:100%; display:block; font-weight:600;">Sample Food Image Presets:</small>
                        <button type="button" class="btn btn-sm btn-outline btn-preset-img" data-url="https://images.unsplash.com/photo-1668236543090-82eba5ee5976?auto=format&fit=crop&w=400&q=80">Dosa</button>
                        <button type="button" class="btn btn-sm btn-outline btn-preset-img" data-url="https://images.unsplash.com/photo-1589301760014-d929f3979dbc?auto=format&fit=crop&w=400&q=80">Idli / Vada</button>
                        <button type="button" class="btn btn-sm btn-outline btn-preset-img" data-url="https://images.unsplash.com/photo-1563379091339-03b21ab4a4f8?auto=format&fit=crop&w=400&q=80">Biryani</button>
                        <button type="button" class="btn btn-sm btn-outline btn-preset-img" data-url="https://images.unsplash.com/photo-1631452180519-c014fe946bc7?auto=format&fit=crop&w=400&q=80">Paneer Curry</button>
                        <button type="button" class="btn btn-sm btn-outline btn-preset-img" data-url="https://images.unsplash.com/photo-1534778101976-62847782c213?auto=format&fit=crop&w=400&q=80">Filter Coffee</button>
                    </div>
                </div>

                <div class="form-group form-checkbox">
                    <label>
                        <input type="checkbox" name="is_available" id="dish-is-available" value="1" checked> Available for Ordering
                    </label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-success btn-block btn-lg">
                    <i class="fa-solid fa-check"></i> Save Dish Details & Price
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Batch Add Multiple Tables Modal -->
<div id="batch-table-modal" class="modal-overlay hidden">
    <div class="modal-content" style="max-width:480px;">
        <div class="modal-header">
            <h3><i class="fa-solid fa-layer-group"></i> Create Multiple Tables</h3>
            <button id="btn-close-batch-table-modal" class="close-btn"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form action="/owner/table/create" method="POST">
            <div class="modal-body">
                <div class="alert alert-info" style="font-size:0.85rem; margin-bottom:16px;">
                    <i class="fa-solid fa-circle-info"></i> <strong>Smart Auto-Naming:</strong> Currently you have <strong><?= count($tables) ?> tables</strong> (highest number: Table <?= $maxTableNum ?>). Creating new tables will start automatically from <strong>Table <?= $nextTableStart ?></strong>.
                </div>

                <div class="form-group">
                    <label for="table-prefix"><i class="fa-solid fa-font"></i> Table Name Prefix</label>
                    <input type="text" id="table-prefix" name="prefix" value="Table" required class="form-control" placeholder="e.g. Table, VIP, AC Hall">
                </div>

                <div class="form-group">
                    <label for="table-count"><i class="fa-solid fa-hashtag"></i> How Many Tables to Create?</label>
                    <div style="display:flex; gap:10px; align-items:center;">
                        <input type="number" id="table-count" name="count" value="5" min="1" max="50" required class="form-control" style="font-size:1.3rem; font-weight:800; text-align:center;">
                        <span style="font-weight:700; color:var(--text-muted);">tables</span>
                    </div>
                </div>

                <div class="form-group">
                    <label><i class="fa-solid fa-bolt"></i> Quick Quantity Presets:</label>
                    <div style="display:flex; gap:8px; flex-wrap:wrap;">
                        <button type="button" class="btn btn-sm btn-outline btn-count-preset" data-count="3">+3 Tables</button>
                        <button type="button" class="btn btn-sm btn-outline btn-count-preset" data-count="5">+5 Tables</button>
                        <button type="button" class="btn btn-sm btn-outline btn-count-preset" data-count="10">+10 Tables</button>
                        <button type="button" class="btn btn-sm btn-outline btn-count-preset" data-count="15">+15 Tables</button>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-warning btn-block btn-lg">
                    <i class="fa-solid fa-circle-plus"></i> Generate & Save Multiple Tables
                </button>
            </div>
        </form>
    </div>
</div>


<?php
$content = ob_get_clean();
include __DIR__ . '/layout.php';
?>
