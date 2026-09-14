<?php

require __DIR__ . '/../vendor/autoload.php';

use App\Models\Table;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Writer\SvgWriter;

$publicQrDir = __DIR__ . '/../public/qr_codes';
$rootQrDir = __DIR__ . '/../qr_codes';

foreach ([$publicQrDir, $rootQrDir] as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
}

$tables = Table::all();
$baseUrl = "http://127.0.0.1:8000";

echo "Generating table QR code image files...\n\n";

foreach ($tables as $t) {
    $tableId = $t['id'];
    $tableName = $t['table_number'];
    $slug = strtolower(str_replace(' ', '_', $tableName));
    $targetUrl = "{$baseUrl}/table/{$tableId}";

    // Generate SVG Content
    $svgData = null;
    $pngData = null;

    if (class_exists(QrCode::class)) {
        try {
            $qrCode = QrCode::create($targetUrl)->setSize(300)->setMargin(10);

            if (class_exists(SvgWriter::class)) {
                $svgWriter = new SvgWriter();
                $svgData = $svgWriter->write($qrCode)->getString();
            }

            if (class_exists(PngWriter::class)) {
                $pngWriter = new PngWriter();
                $pngData = $pngWriter->write($qrCode)->getString();
            }
        } catch (\Throwable $e) {
            echo "Error generating for {$tableName}: " . $e->getMessage() . "\n";
        }
    }

    if (!$svgData) {
        $svgData = '<?xml version="1.0" encoding="UTF-8"?><svg xmlns="http://www.w3.org/2000/svg" width="300" height="300"><rect width="300" height="300" fill="#fff"/><text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle" font-family="sans-serif" font-size="16">' . htmlspecialchars($tableName) . '</text></svg>';
    }

    // Save SVG file
    $svgPathPublic = "{$publicQrDir}/{$slug}_qr.svg";
    $svgPathRoot = "{$rootQrDir}/{$slug}_qr.svg";
    file_put_contents($svgPathPublic, $svgData);
    file_put_contents($svgPathRoot, $svgData);

    // Save PNG file if generated
    if ($pngData) {
        $pngPathPublic = "{$publicQrDir}/{$slug}_qr.png";
        $pngPathRoot = "{$rootQrDir}/{$slug}_qr.png";
        file_put_contents($pngPathPublic, $pngData);
        file_put_contents($pngPathRoot, $pngData);
    }

    echo " [OK] {$tableName} ({$targetUrl}) -> public/qr_codes/{$slug}_qr.svg & .png\n";
}

echo "\nQR Codes folder successfully created and populated in:\n";
echo " - " . realpath($publicQrDir) . "\n";
echo " - " . realpath($rootQrDir) . "\n";
