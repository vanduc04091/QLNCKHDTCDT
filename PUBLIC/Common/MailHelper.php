<?php
/**
 * MailHelper - Gửi mail HTML (SMTP qua fsockopen, không cần PHPMailer).
 * Cấu hình lấy từ bảng DM_CAU_HINH (xem upgrade_dangkykhoahoc.php).
 * Khi MAIL_ENABLED=0: log vào assets/uploads/maillog.txt thay vì gửi.
 */
class MailHelper
{
    public static function send(string $to, string $toName, string $subject, string $htmlBody): bool
    {
        $cfg = self::loadConfig();
        if (!filter_var($to, FILTER_VALIDATE_EMAIL)) return false;

        if ((int)($cfg['MAIL_ENABLED'] ?? 0) !== 1) {
            self::log("[DISABLED] To: {$to} | Subject: {$subject}\n{$htmlBody}\n");
            return true;  // không lỗi để flow tiếp tục
        }

        try {
            $ok = self::smtpSend($cfg, $to, $toName, $subject, $htmlBody);
            self::log(($ok ? '[OK] ' : '[FAIL] ') . "To: {$to} | Subject: {$subject}");
            return $ok;
        } catch (Throwable $ex) {
            self::log("[ERROR] {$to} | {$subject} | " . $ex->getMessage());
            return false;
        }
    }

    private static function loadConfig(): array
    {
        $stmt = Database::getConnection()->query("SELECT ma_cau_hinh, gia_tri FROM DM_CAU_HINH");
        $rows = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
        return $rows ?: [];
    }

    private static function logFile(): string
    {
        return __DIR__ . '/../../assets/uploads/maillog.txt';
    }

    private static function log(string $msg): void
    {
        $line = '[' . date('Y-m-d H:i:s') . '] ' . $msg . "\n" . str_repeat('-', 60) . "\n";
        @file_put_contents(self::logFile(), $line, FILE_APPEND);
    }

    /**
     * SMTP gửi mail trực tiếp qua socket (PLAIN auth, STARTTLS hoặc SSL).
     * Không dùng PHPMailer để tránh dependency.
     */
    private static function smtpSend(array $cfg, string $to, string $toName, string $subject, string $body): bool
    {
        $host   = $cfg['SMTP_HOST'] ?? '';
        $port   = (int)($cfg['SMTP_PORT'] ?? 587);
        $secure = strtolower($cfg['SMTP_SECURE'] ?? 'tls');
        $user   = $cfg['SMTP_USER'] ?? '';
        $pass   = $cfg['SMTP_PASS'] ?? '';
        $from   = $user;
        $fromName = $cfg['SMTP_FROM_NAME'] ?? AppConfig::APP_NAME;

        if ($host === '' || $user === '') {
            throw new RuntimeException('SMTP chưa cấu hình');
        }

        $remote = ($secure === 'ssl') ? "ssl://{$host}:{$port}" : "{$host}:{$port}";
        $sock = @stream_socket_client($remote, $errno, $errstr, 15);
        if (!$sock) throw new RuntimeException("Connect SMTP fail: {$errstr}");
        stream_set_timeout($sock, 15);

        $read = function() use ($sock) {
            $out = '';
            while (($line = fgets($sock, 515)) !== false) {
                $out .= $line;
                if (isset($line[3]) && $line[3] === ' ') break;
            }
            return $out;
        };
        $send = function(string $cmd) use ($sock) { fwrite($sock, $cmd . "\r\n"); };

        $read();
        $send("EHLO " . ($_SERVER['SERVER_NAME'] ?? 'localhost')); $read();

        if ($secure === 'tls') {
            $send("STARTTLS"); $read();
            if (!stream_socket_enable_crypto($sock, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
                fclose($sock); throw new RuntimeException('TLS fail');
            }
            $send("EHLO " . ($_SERVER['SERVER_NAME'] ?? 'localhost')); $read();
        }

        $send("AUTH LOGIN"); $read();
        $send(base64_encode($user)); $read();
        $send(base64_encode($pass));
        $resp = $read();
        if (strpos($resp, '235') !== 0) { fclose($sock); throw new RuntimeException('Auth fail: ' . trim($resp)); }

        $send("MAIL FROM: <{$from}>"); $read();
        $send("RCPT TO: <{$to}>"); $read();
        $send("DATA"); $read();

        $headers = "From: =?UTF-8?B?" . base64_encode($fromName) . "?= <{$from}>\r\n"
                 . "To: =?UTF-8?B?" . base64_encode($toName) . "?= <{$to}>\r\n"
                 . "Subject: =?UTF-8?B?" . base64_encode($subject) . "?=\r\n"
                 . "MIME-Version: 1.0\r\n"
                 . "Content-Type: text/html; charset=UTF-8\r\n"
                 . "Content-Transfer-Encoding: base64\r\n"
                 . "Date: " . date('r') . "\r\n";

        $payload = $headers . "\r\n" . chunk_split(base64_encode($body)) . "\r\n.";
        fwrite($sock, $payload . "\r\n");
        $resp = $read();

        $send("QUIT"); fclose($sock);
        return strpos($resp, '250') === 0;
    }
}
