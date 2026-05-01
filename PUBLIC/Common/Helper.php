<?php
/**
 * Helper - Hàm tiện ích dùng chung
 */
class Helper
{
    /**
     * Sanitize input string
     */
    public static function sanitize(?string $value): string
    {
        if ($value === null) return '';
        return trim(strip_tags($value));
    }

    /**
     * Escape output HTML
     */
    public static function h($value): string
    {
        return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Format datetime dd/mm/Y H:i
     */
    public static function formatDateTime(?string $datetime, string $format = 'd/m/Y H:i'): string
    {
        if (empty($datetime) || $datetime === '0000-00-00 00:00:00') return '';
        $ts = strtotime($datetime);
        return $ts ? date($format, $ts) : '';
    }

    /**
     * Format date dd/mm/Y
     */
    public static function formatDate(?string $date, string $format = 'd/m/Y'): string
    {
        return self::formatDateTime($date, $format);
    }

    /**
     * Validate email
     */
    public static function isEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Validate phone (VN)
     */
    public static function isPhone(string $phone): bool
    {
        return preg_match('/^[0-9+\-\s()]{8,20}$/', $phone) === 1;
    }

    /**
     * Sinh chuỗi ngẫu nhiên
     */
    public static function randomString(int $length = 16): string
    {
        return bin2hex(random_bytes((int)($length / 2)));
    }

    /**
     * Lấy IP client
     */
    public static function getClientIp(): string
    {
        foreach (['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR'] as $key) {
            if (!empty($_SERVER[$key])) {
                $ip = explode(',', $_SERVER[$key])[0];
                return trim($ip);
            }
        }
        return '0.0.0.0';
    }

    /**
     * Get POST / GET an toàn
     */
    public static function post(string $key, $default = '')
    {
        return $_POST[$key] ?? $default;
    }

    public static function get(string $key, $default = '')
    {
        return $_GET[$key] ?? $default;
    }

    public static function postInt(string $key, int $default = 0): int
    {
        return isset($_POST[$key]) ? (int)$_POST[$key] : $default;
    }

    public static function postStr(string $key, string $default = ''): string
    {
        return isset($_POST[$key]) ? self::sanitize((string)$_POST[$key]) : $default;
    }

    /**
     * Trả về trạng thái badge HTML
     */
    public static function badgeTrangThai(int $trangThai): string
    {
        return $trangThai == 1
            ? '<span class="badge badge-success">Hoạt động</span>'
            : '<span class="badge badge-danger">Ngừng</span>';
    }

    /**
     * Require login (redirect nếu chưa đăng nhập)
     */
    public static function requireLogin(): void
    {
        if (!SessionHelper::isLoggedIn()) {
            header('Location: ' . AppConfig::baseUrl('GUI/auth/login.php'));
            exit;
        }
    }

    /**
     * Require AJAX login
     */
    public static function requireAjaxLogin(): void
    {
        if (!SessionHelper::isLoggedIn()) {
            ResponseHelper::error('Phiên đăng nhập đã hết hạn', 401);
        }
    }

    /**
     * Require AJAX login + verify CSRF token.
     * Token được đọc từ header X-CSRF-Token (mặc định trong APP.ajax) hoặc POST _csrf_token.
     */
    public static function requireAjaxCsrf(): void
    {
        self::requireAjaxLogin();
        $token = $_SERVER['HTTP_X_CSRF_TOKEN']
            ?? ($_POST[AppConfig::CSRF_TOKEN_NAME] ?? ($_POST['_csrf'] ?? ''));
        if (!SessionHelper::verifyCsrf((string)$token)) {
            ResponseHelper::error('Phiên làm việc đã hết hạn. Vui lòng tải lại trang.', 419);
        }
    }
}
