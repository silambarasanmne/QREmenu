<?php
$title = "Owner Dashboard & Analytics";
$bodyClass = "owner-page";
$headerRightHtml = '<a href="/api/logout" class="btn btn-danger btn-sm"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>';

ob_start();
?>

<div class="owner-container">
    <!-- Owner Top Navigation Tabs -->
    <div class="owner-tabs">
        <button class="owner-tab active" data-target="tab-analytics">
            <i class="fa-solid fa-chart-line"></i> Revenue & Analytics
        </button>
        <button class="owner-tab" data-target="tab-dishes">
            <i class="fa-solid fa-utensils"></i> Menu Management
        </button>
        <button class="owner-tab" data-target="tab-tables">
            <i class="fa-solid fa-qrcode"></i> Table & QR Codes
        </button>
    </div>

    <!-- TAB 1: Revenue & Analytics -->
    <div id="tab-analytics" class="owner-tab-content active">
        <!-- Date Range Filter Form -->
        <div class="card filter-card">
            <form method="GET" action="/owner" class="date-filter-form">
                <div class="form-group-inline">
                    <label><i class="fa-solid fa-calendar-day"></i> Filter Date Range:</label>
                    <input type="date" name="start_date" value="<?= htmlspecialchars($stats['start_date'] ?? '') ?>" class="form-control">
                    <span>to</span>
                    <input type="date" name="end_date" value="<?= htmlspecialchars($stats['end_date'] ?? '') ?>" class="form-control">
                    <button type="submit" class="btn btn-primary btn-sm"><i class="fa-solid fa-filter"></i> Apply Filter</button>
                    <a href="/owner" class="btn btn-outline btn-sm">Reset</a>
                </div>
            </form>
        </div>

        <!-- Metrics Summary Cards -->
        <div class="stats-cards-grid">
            <div class="stat-card primary">
                <div class="stat-icon"><i class="fa-solid fa-indian-rupee-sign"></i></div>
                <div class="stat-info">
                    <span class="stat-label">Total Revenue</span>
                    <h3 class="stat-value">₹<?= number_format($stats['total_revenue'], 2) ?></h3>
                </div>
            </div>

            <div class="stat-card success">
                <div class="stat-icon"><i class="fa-solid fa-receipt"></i></div>
                <div class="stat-info">
                    <span class="stat-label">Served Orders</span>
                    <h3 class="stat-value"><?= $stats['total_orders'] ?></h3>
                </div>
            </div>

            <div class="stat-card info">
                <div class="stat-icon"><i class="fa-solid fa-calculator"></i></div>
                <div class="stat-info">
                    <span class="stat-label">Avg Order Value</span>
                    <h3 class="stat-value">₹<?= number_format($stats['avg_order_value'], 2) ?></h3>
                </div>
            </div>

            <div class="stat-card warning">
                <div class="stat-icon"><i class="fa-solid fa-sun"></i></div>
                <div class="stat-info">
                    <span class="stat-label">Revenue Today</span>
                    <h3 class="stat-value">₹<?= number_format($stats['revenue_today'], 2) ?></h3>
                    <small><?= $stats['orders_today'] ?> orders today</small>
                </div>
            </div>
        </div>

        <div class="analytics-split">
            <!-- Best Selling Dishes Table / Chart -->
            <div class="card analytics-card">
                <h3><i class="fa-solid fa-trophy"></i> Best Selling Dishes</h3>
                <?php if (!empty($stats['best_selling'])): ?>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Dish</th>
                                <th>Category</th>
                                <th>Price</th>
                                <th>Items Sold</th>
                                <th>Revenue</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($stats['best_selling'] as $dish): ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($dish['name']) ?></strong></td>
                                    <td><span class="badge badge-outline"><?= htmlspecialchars($dish['category']) ?></span></td>
                                    <td>₹<?= number_format($dish['price'], 2) ?></td>
                                    <td><span class="badge badge-success"><?= $dish['total_quantity'] ?> sold</span></td>
                                    <td><strong>₹<?= number_format($dish['total_revenue'], 2) ?></strong></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p class="text-muted">No sales data recorded yet.</p>
                <?php endif; ?>
            </div>

            <!-- Recent Orders List -->
            <div class="card analytics-card">
                <h3><i class="fa-solid fa-clock-rotate-left"></i> Recent Orders</h3>
                <?php if (!empty($stats['recent_orders'])): ?>
                    <div class="recent-orders-list">
                        <?php foreach ($stats['recent_orders'] as $ord): ?>
                            <div class="recent-order-item">
                                <div class="ro-header">
                                    <span class="ro-table"><?= htmlspecialchars($ord['table_number']) ?></span>
                                    <span class="badge badge-status-<?= $ord['status'] ?>"><?= strtoupper($ord['status']) ?></span>
                                </div>
                                <div class="ro-meta">
                                    <span>Order #<?= $ord['id'] ?></span> &bull;
                                    <span>₹<?= number_format($ord['total_amount'], 2) ?></span> &bull;
                                    <small><?= date('M j, g:i a', strtotime($ord['created_at'])) ?></small>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="text-muted">No orders found.</p>
                <?php endif; ?>
            </div>
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
        <div class="tab-header-action">
            <h3><i class="fa-solid fa-qrcode"></i> Tables & QR Code Generator</h3>
            <form action="/owner/table/create" method="POST" class="form-inline">
                <input type="text" name="table_number" placeholder="New Table Name (e.g. Table 6)" required class="form-control">
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Add Table</button>
            </form>
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
                    <label>Category</label>
                    <select name="category" id="dish-category" required class="form-control">
                        <option value="Starters">Starters</option>
                        <option value="Mains">Mains</option>
                        <option value="Desserts">Desserts</option>
                        <option value="Beverages">Beverages</option>
                        <option value="Specials">Specials</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Price (₹)</label>
                    <input type="number" step="0.01" name="price" id="dish-price" required class="form-control">
                </div>

                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" id="dish-description" rows="3" class="form-control"></textarea>
                </div>

                <div class="form-group">
                    <label>Image URL</label>
                    <input type="url" name="image_url" id="dish-image-url" placeholder="https://..." class="form-control">
                </div>

                <div class="form-group form-checkbox">
                    <label>
                        <input type="checkbox" name="is_available" id="dish-is-available" value="1" checked> Available for Ordering
                    </label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-success btn-block">Save Dish</button>
            </div>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/layout.php';
?>
