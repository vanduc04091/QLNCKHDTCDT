<?php
/**
 * CaptchaHelper - Math captcha (5 + 3 = ?) lưu trong session.
 * Dùng cho form public (không cần Google reCAPTCHA, không cần GD).
 */
class CaptchaHelper
{
    const SESSION_KEY = '_captcha_answer';
    const TS_KEY      = '_captcha_ts';
    const HONEYPOT    = 'website_url';   // tên field bẫy

    /**
     * Sinh phép tính mới và lưu đáp án vào session.
     * Trả về chuỗi câu hỏi (vd: "5 + 3 = ?").
     */
    public static function generate(): string
    {
        SessionHelper::start();
        $a = random_int(1, 9);
        $b = random_int(1, 9);
        $ops = [
            ['+', $a + $b, "{$a} + {$b}"],
            ['×', $a * $b, "{$a} × {$b}"],
        ];
        // 70% phép cộng, 30% phép nhân
        $pick = (random_int(1, 10) <= 7) ? $ops[0] : $ops[1];
        $_SESSION[self::SESSION_KEY] = (string)$pick[1];
        $_SESSION[self::TS_KEY]      = time();
        return $pick[2] . ' = ?';
    }

    /**
     * Kiểm tra đáp án + honeypot. Đáp án 1 captcha dùng được 1 lần.
     */
    public static function verify(string $answer, string $honeypot = ''): bool
    {
        SessionHelper::start();

        // Honeypot: nếu bot điền vào thì fail luôn
        if ($honeypot !== '') return false;

        // Hết hạn 10 phút
        $ts = (int)($_SESSION[self::TS_KEY] ?? 0);
        if ($ts === 0 || time() - $ts > 600) {
            unset($_SESSION[self::SESSION_KEY], $_SESSION[self::TS_KEY]);
            return false;
        }

        $expected = $_SESSION[self::SESSION_KEY] ?? null;
        // Xóa ngay để chống replay
        unset($_SESSION[self::SESSION_KEY], $_SESSION[self::TS_KEY]);
        if ($expected === null) return false;

        return trim($answer) === $expected;
    }

    public static function honeypotName(): string
    {
        return self::HONEYPOT;
    }
}
