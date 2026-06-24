<?php
/**
 * cron_nckh_nhac_viec.php - Gửi mail cho các nhắc việc NCKH đến hạn chưa gửi.
 *
 * Cau hinh chay:
 *   - Linux cron: moi 15 phut goi /usr/bin/php <duong-dan>/cron_nckh_nhac_viec.php
 *   - Windows Task Scheduler: chay moi 15-30 phut
 *   - Hoac goi qua URL bang webhook: php-cli khuyen nghi (URL chi cho phep tu IP noi bo).
 *
 * Lenh ngoai cron se thoat ngay neu MAIL_ENABLED=0 trong DM_CAU_HINH.
 */
require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/BUS/NCKH_NhacViec_BUS.php';
require_once __DIR__ . '/DAL/DM_CauHinh_DAL.php';
require_once __DIR__ . '/PUBLIC/Common/MailHelper.php';

$isCli = (PHP_SAPI === 'cli');
header('Content-Type: text/plain; charset=utf-8');

$logLine = function (string $msg) use ($isCli) {
    $line = '[' . date('Y-m-d H:i:s') . '] ' . $msg;
    echo $line . ($isCli ? "\n" : "<br>");
};

// Bảo vệ: nếu chạy qua URL, yêu cầu token đơn giản hoặc chỉ cho IP local
if (!$isCli) {
    $allowed = ['127.0.0.1', '::1'];
    $token = $_GET['token'] ?? '';
    $cfgToken = DM_CauHinh_DAL::get('CRON_TOKEN', '');
    if (!in_array($_SERVER['REMOTE_ADDR'] ?? '', $allowed, true) && ($cfgToken === '' || $token !== $cfgToken)) {
        http_response_code(403);
        echo "Forbidden. Cấu hình DM_CAU_HINH.CRON_TOKEN rồi gọi ?token=...";
        exit;
    }
}

$mailEnabled = DM_CauHinh_DAL::get('MAIL_ENABLED', '0') === '1';
if (!$mailEnabled) {
    $logLine("⚠ MAIL_ENABLED=0 — bỏ qua gửi mail. Bật trong DM_CAU_HINH để chạy thật.");
}

$items = NCKH_NhacViec_BUS::getDueUnsent(50);
$logLine("Tìm thấy " . count($items) . " nhắc việc đến hạn.");

$sent = 0; $failed = 0; $skipped = 0;
foreach ($items as $r) {
    $to = $r['email_nguoi_nhan'] ?? '';
    $name = $r['ho_ten_nguoi_nhan'] ?? '';
    if (!$to || !filter_var($to, FILTER_VALIDATE_EMAIL)) {
        NCKH_NhacViec_BUS::markSent((int)$r['id'], 'Bỏ qua: thiếu email người nhận');
        $skipped++;
        $logLine("- #{$r['id']} bỏ qua: không có email người nhận");
        continue;
    }

    $subject = '[NCKH] ' . $r['tieu_de'];
    $body = '<div style="font-family:Segoe UI,Arial,sans-serif;max-width:620px;margin:0 auto;padding:20px;background:#f5f7fb;color:#1e293b">'
        . '<div style="background:#fff;padding:24px;border-radius:10px;box-shadow:0 1px 3px rgba(0,0,0,.06)">'
        . '<h2 style="color:#2563eb;margin-top:0">Nhắc việc NCKH</h2>'
        . '<p>Kính gửi <strong>' . htmlspecialchars($name) . '</strong>,</p>'
        . '<p>Hệ thống xin nhắc việc liên quan đến đề tài:</p>'
        . '<div style="background:#f1f5f9;padding:12px 16px;border-radius:8px;margin:12px 0">'
        . '<div><strong>Đề tài:</strong> [' . htmlspecialchars($r['ma_de_tai']) . '] ' . htmlspecialchars($r['ten_de_tai']) . '</div>'
        . '<div><strong>Tiêu đề nhắc:</strong> ' . htmlspecialchars($r['tieu_de']) . '</div>'
        . '<div><strong>Loại:</strong> ' . htmlspecialchars($r['loai_nhac']) . '</div>'
        . '<div><strong>Hẹn lúc:</strong> ' . date('d/m/Y H:i', strtotime($r['ngay_nhac'])) . '</div>'
        . '</div>'
        . ($r['noi_dung'] ? '<p>' . nl2br(htmlspecialchars($r['noi_dung'])) . '</p>' : '')
        . '<p style="margin-top:24px;color:#64748b;font-size:12px">Email được gửi tự động. Vui lòng đăng nhập hệ thống để xem chi tiết.</p>'
        . '</div></div>';

    $ok = false;
    if ($mailEnabled) {
        try { $ok = MailHelper::send($to, $name, $subject, $body); }
        catch (Throwable $ex) { $logLine("- #{$r['id']} lỗi gửi: " . $ex->getMessage()); }
    } else {
        $ok = true; // dry-run
    }

    if ($ok) {
        NCKH_NhacViec_BUS::markSent((int)$r['id'], $mailEnabled ? "Gửi tới {$to}" : "Dry-run (mail OFF) tới {$to}");
        $sent++;
        $logLine("✓ #{$r['id']} → {$to}");
    } else {
        $failed++;
        $logLine("✗ #{$r['id']} → {$to} thất bại");
    }
}

$logLine("=== Kết quả: gửi {$sent}, lỗi {$failed}, bỏ qua {$skipped} ===");
