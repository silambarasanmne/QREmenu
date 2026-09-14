<?php
$title = "Agam - Portal";
ob_start();
?>
<div class="portal-container" style="max-width: 800px; margin: 40px auto; text-align: center; padding: 20px;">
    <h1 style="color: var(--primary-color); font-size: 2.5rem; margin-bottom: 10px;">Agam QR Menu</h1>
    <p style="color: #666; margin-bottom: 40px;">Select a portal to access the system.</p>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 40px;">
        <a href="/table/1" style="text-decoration: none;">
            <div class="card" style="padding: 30px; border-radius: 15px; background: white; box-shadow: 0 10px 30px rgba(0,0,0,0.05); transition: transform 0.3s ease;">
                <h2 style="color: #333; margin-bottom: 15px;">📱 Customer Menu</h2>
                <p style="color: #777; font-size: 0.9rem;">View menu and place orders (Table 1)</p>
                <div style="margin-top: 20px;">
                    <?php 
                    $qrUri = \App\Services\QrCodeService::generateSvgDataUri('http://' . $_SERVER['HTTP_HOST'] . '/table/1');
                    echo '<img src="' . $qrUri . '" alt="Customer QR" style="width: 150px; height: 150px; border-radius: 10px; border: 1px solid #eee; padding: 10px;">';
                    ?>
                </div>
            </div>
        </a>

        <a href="/waiter" style="text-decoration: none;">
            <div class="card" style="padding: 30px; border-radius: 15px; background: white; box-shadow: 0 10px 30px rgba(0,0,0,0.05); transition: transform 0.3s ease;">
                <h2 style="color: #333; margin-bottom: 15px;">🤵 Waiter Dashboard</h2>
                <p style="color: #777; font-size: 0.9rem;">Serve dishes, close bills, and manage tables.</p>
                <div style="margin-top: 20px; color: var(--primary-color); font-weight: bold;">PIN: 1234</div>
            </div>
        </a>

        <a href="/kitchen" style="text-decoration: none;">
            <div class="card" style="padding: 30px; border-radius: 15px; background: white; box-shadow: 0 10px 30px rgba(0,0,0,0.05); transition: transform 0.3s ease;">
                <h2 style="color: #333; margin-bottom: 15px;">👨‍🍳 Kitchen Display</h2>
                <p style="color: #777; font-size: 0.9rem;">Real-time order tracking for chefs.</p>
                <div style="margin-top: 20px; color: var(--primary-color); font-weight: bold;">PIN: 1234</div>
            </div>
        </a>

        <a href="/owner" style="text-decoration: none;">
            <div class="card" style="padding: 30px; border-radius: 15px; background: white; box-shadow: 0 10px 30px rgba(0,0,0,0.05); transition: transform 0.3s ease;">
                <h2 style="color: #333; margin-bottom: 15px;">📈 Owner Dashboard</h2>
                <p style="color: #777; font-size: 0.9rem;">Analytics, dish management, and settings.</p>
                <div style="margin-top: 20px; color: var(--primary-color); font-weight: bold;">PIN: 1234</div>
            </div>
        </a>
    </div>
</div>

<style>
.card:hover { transform: translateY(-5px); box-shadow: 0 15px 35px rgba(230, 57, 70, 0.15) !important; }
</style>

<?php
$content = ob_get_clean();
include __DIR__ . '/layout.php';
?>
