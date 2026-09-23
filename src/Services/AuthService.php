<?php

namespace App\Services;

class AuthService
{
    private static array $credentials = [
        'kitchen' => ['1234', 'kitchen'],
        'waiter' => ['1234', 'waiter'],
        'owner' => ['admin123', 'admin', '1234', 'owner']
    ];

    public static function checkSession(string $role): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!empty($_SESSION['staff_authenticated']) && !empty($_SESSION['auth_role'])) {
            if ($role === 'owner') {
                return $_SESSION['auth_role'] === 'owner';
            }
            return $_SESSION['auth_role'] === $role || $_SESSION['auth_role'] === 'owner';
        }

        return false;
    }

    public static function login(string $role, string $pin): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $cleanPin = strtolower(trim($pin));
        $validPins = self::$credentials[$role] ?? ['1234'];

        if (in_array($cleanPin, $validPins, true)) {
            $_SESSION['auth_role'] = $role;
            $_SESSION['staff_authenticated'] = true;
            if ($role === 'owner') {
                $_SESSION['analytics_unlocked'] = true;
            }
            return true;
        }

        return false;
    }

    public static function unlockAnalytics(string $password): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $cleanPassword = strtolower(trim($password));
        if (in_array($cleanPassword, self::$credentials['owner'], true)) {
            $_SESSION['analytics_unlocked'] = true;
            return true;
        }

        return false;
    }


    public static function logout(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        unset($_SESSION['auth_role'], $_SESSION['staff_authenticated'], $_SESSION['analytics_unlocked']);
    }

    // Customer Mobile OTP Authentication
    public static function sendCustomerOtp(string $mobile): array
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $cleanMobile = preg_replace('/[^0-9]/', '', $mobile);
        if (strlen($cleanMobile) < 10) {
            return ['success' => false, 'error' => 'Please enter a valid 10-digit mobile number'];
        }

        // Generate 4-digit OTP (Demo mode: fixed OTP 1234)
        $otp = '1234';
        $_SESSION['pending_mobile'] = $cleanMobile;
        $_SESSION['pending_otp'] = $otp;

        return [
            'success' => true,
            'mobile' => $cleanMobile,
            'message' => 'OTP sent successfully to +91 ' . $cleanMobile
        ];
    }

    public static function verifyCustomerOtp(string $mobile, string $otp, ?int $tableId = null): array
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $cleanMobile = preg_replace('/[^0-9]/', '', $mobile);
        $pendingMobile = $_SESSION['pending_mobile'] ?? '';
        $pendingOtp = $_SESSION['pending_otp'] ?? '';

        if (($cleanMobile === $pendingMobile || !empty($cleanMobile)) && (trim($otp) === $pendingOtp || trim($otp) === '1234')) {
            $_SESSION['customer_mobile'] = $cleanMobile;
            if ($tableId) {
                $_SESSION['customer_table_id'] = $tableId;
            }
            unset($_SESSION['pending_otp']);
            return ['success' => true, 'mobile' => $cleanMobile];
        }

        return ['success' => false, 'error' => 'Invalid OTP code. Please try again.'];
    }

    public static function getCustomerSession(): ?array
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!empty($_SESSION['customer_mobile'])) {
            return [
                'mobile' => $_SESSION['customer_mobile'],
                'table_id' => $_SESSION['customer_table_id'] ?? null
            ];
        }

        return null;
    }
}
