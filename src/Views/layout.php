<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Agam eMenu — Smart Digital QR Ordering Platform for restaurants. Contactless menu, real-time kitchen display, waiter dashboard & owner analytics.">
    <meta name="theme-color" content="#0f172a">
    <title><?= htmlspecialchars($title ?? 'Agam eMenu — Digital QR Ordering') ?></title>
    <!-- Favicon -->
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🍽️</text></svg>" type="image/svg+xml">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Application CSS -->
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body class="<?= htmlspecialchars($bodyClass ?? '') ?>">
    <!-- Top Navigation Bar -->
    <header class="app-header">
        <div class="header-container">
            <a href="/" class="brand-logo">
                <span class="logo-icon"><i class="fa-solid fa-utensils"></i></span>
                <span class="brand-name">Agam<span class="accent">eMenu</span></span>
            </a>
            <?php if (isset($headerRightHtml)): ?>
                <div class="header-right">
                    <?= $headerRightHtml ?>
                </div>
            <?php endif; ?>
        </div>
    </header>

    <!-- Main Content Area -->
    <main class="main-content">
        <?= $content ?>
    </main>

    <!-- Global Audio Ping for Kitchen & Waiter Notifications -->
    <audio id="notification-sound" preload="auto">
        <source src="https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3" type="audio/mpeg">
    </audio>

    <!-- Toast Notification Container -->
    <div id="toast-container"></div>

    <!-- Application JS -->
    <script src="/assets/js/app.js"></script>
    <?php if (isset($extraJs)): ?>
        <?= $extraJs ?>
    <?php endif; ?>
</body>
</html>
