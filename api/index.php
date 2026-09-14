<?php
// Vercel serverless environment fixes
if (isset($_SERVER['VERCEL']) || getenv('VERCEL')) {
    session_save_path('/tmp');
}
require __DIR__ . '/../public/index.php';
