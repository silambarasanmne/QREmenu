<?php

namespace App\Services;

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Writer\SvgWriter;

class QrCodeService
{
    public static function generateSvgDataUri(string $url, ?string $filename = null): string
    {
        try {
            if (class_exists(QrCode::class) && class_exists(SvgWriter::class)) {
                $qrCode = QrCode::create($url)->setSize(250)->setMargin(10);
                $writer = new SvgWriter();
                $result = $writer->write($qrCode);

                if ($filename) {
                    self::saveToFile($filename, $result->getString(), 'svg');
                }

                return $result->getDataUri();
            }
        } catch (\Throwable $e) {
            // Fallback
        }

        return self::fallbackSvgUri($url);
    }

    public static function generatePngDataUri(string $url, ?string $filename = null): string
    {
        try {
            if (class_exists(QrCode::class) && class_exists(PngWriter::class)) {
                $qrCode = QrCode::create($url)->setSize(300)->setMargin(10);
                $writer = new PngWriter();
                $result = $writer->write($qrCode);

                if ($filename) {
                    self::saveToFile($filename, $result->getString(), 'png');
                }

                return $result->getDataUri();
            }
        } catch (\Throwable $e) {
            // Fallback
        }

        return self::fallbackSvgUri($url);
    }

    private static function saveToFile(string $filename, string $data, string $ext): void
    {
        if (isset($_SERVER['VERCEL']) || getenv('VERCEL')) {
            return; // Vercel has a read-only filesystem, skip file creation
        }

        $dirs = [
            __DIR__ . '/../../public/qr_codes',
            __DIR__ . '/../../qr_codes'
        ];

        foreach ($dirs as $d) {
            if (!is_dir($d)) {
                @mkdir($d, 0777, true);
            }
            @file_put_contents("{$d}/{$filename}.{$ext}", $data);
        }
    }

    private static function fallbackSvgUri(string $url): string
    {
        $encoded = urlencode($url);
        return "https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=" . $encoded;
    }
}
