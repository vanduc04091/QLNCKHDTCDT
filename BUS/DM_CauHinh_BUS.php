<?php
require_once __DIR__ . '/../DAL/DM_CauHinh_DAL.php';
require_once __DIR__ . '/../DAL/DM_NhatKyHeThong_DAL.php';
require_once __DIR__ . '/../PUBLIC/Common/MailHelper.php';

/**
 * DM_CauHinh_BUS - Quản lý các nhóm cấu hình.
 * Định nghĩa nhóm + field cố định để render form (admin không tự thêm key bừa bãi).
 */
class DM_CauHinh_BUS
{
    const MODULE_KEY = 'DM_CauHinh';

    /**
     * Schema mô tả các nhóm cấu hình + field.
     * type: text|password|number|select|textarea|toggle
     * options: cho select
     * sensitive: true → giá trị che trong API (admin phải nhập lại để đổi)
     */
    public static function schema(): array
    {
        return [
            'app' => [
                'label' => 'Thông tin ứng dụng',
                'desc'  => 'Tên hiển thị, URL công khai cho link đăng ký/tra cứu trong email.',
                'fields' => [
                    'PUBLIC_BASE_URL' => [
                        'label' => 'URL công khai',
                        'type'  => 'text',
                        'placeholder' => 'http://qldt.bv',
                        'help' => 'URL gốc của hệ thống. Được dùng để sinh link tra cứu trong email gửi học viên.',
                    ],
                    'SMTP_FROM_NAME' => [
                        'label' => 'Tên người gửi (mail)',
                        'type'  => 'text',
                        'placeholder' => AppConfig::APP_NAME,
                        'help' => 'Tên hiển thị trên trường "From" của email.',
                    ],
                ],
            ],
            'cme' => [
                'label' => 'Đào tạo y khoa liên tục (CME)',
                'desc'  => 'Ngưỡng giờ tín chỉ tối thiểu để nhân viên được coi là ĐẠT. Dùng ở Sổ theo dõi, Tổng quan và Báo cáo CME.',
                'fields' => [
                    'CME_NGUONG_GIO' => [
                        'label' => 'Ngưỡng giờ tín chỉ / chu kỳ',
                        'type'  => 'number',
                        'placeholder' => '24',
                        'help' => 'Số giờ tín chỉ tối thiểu phải tích lũy trong 1 chu kỳ. Mặc định: 24 giờ.',
                    ],
                    'CME_CHU_KY_NAM' => [
                        'label' => 'Số năm mỗi chu kỳ',
                        'type'  => 'number',
                        'placeholder' => '1',
                        'help' => 'Chu kỳ tính ngưỡng (năm). Mặc định 1 năm — khi xem năm N, hệ thống cộng giờ từ năm (N - chu kỳ + 1) đến N.',
                    ],
                ],
            ],
            'smtp' => [
                'label' => 'Cấu hình SMTP gửi mail',
                'desc'  => 'Bật MAIL_ENABLED để gửi mail thật. Khi tắt, mail được ghi vào assets/uploads/maillog.txt để debug.',
                'fields' => [
                    'MAIL_ENABLED' => [
                        'label' => 'Bật gửi mail',
                        'type'  => 'toggle',
                        'help' => 'Tắt = chỉ ghi log, không gửi thật.',
                    ],
                    'SMTP_HOST' => [
                        'label' => 'SMTP host',
                        'type'  => 'text',
                        'placeholder' => 'smtp.gmail.com',
                    ],
                    'SMTP_PORT' => [
                        'label' => 'Port',
                        'type'  => 'number',
                        'placeholder' => '587',
                        'help' => '587 cho TLS, 465 cho SSL.',
                    ],
                    'SMTP_SECURE' => [
                        'label' => 'Phương thức bảo mật',
                        'type'  => 'select',
                        'options' => ['tls' => 'TLS (STARTTLS)', 'ssl' => 'SSL'],
                    ],
                    'SMTP_USER' => [
                        'label' => 'Tài khoản (email gửi)',
                        'type'  => 'text',
                        'placeholder' => 'your@gmail.com',
                    ],
                    'SMTP_PASS' => [
                        'label' => 'Mật khẩu / App password',
                        'type'  => 'password',
                        'sensitive' => true,
                        'help' => 'Với Gmail: tạo App Password tại myaccount.google.com/apppasswords.',
                    ],
                ],
            ],
        ];
    }

    /** Lấy toàn bộ giá trị, che key sensitive trước khi trả ra UI. */
    public static function getAllForUi(): array
    {
        $map = DM_CauHinh_DAL::getMap();
        $schema = self::schema();
        $out = [];
        foreach ($schema as $groupKey => $group) {
            foreach ($group['fields'] as $fieldKey => $fieldDef) {
                $val = $map[$fieldKey] ?? '';
                if (!empty($fieldDef['sensitive']) && $val !== '') {
                    $out[$fieldKey] = '••••••••';
                } else {
                    $out[$fieldKey] = $val;
                }
            }
        }
        return $out;
    }

    /**
     * Lưu cấu hình. Bỏ qua field sensitive nếu giá trị nhận được rỗng hoặc giữ nguyên placeholder.
     */
    public static function save(array $input, int $userId): array
    {
        $schema = self::schema();
        $allKeys = [];
        foreach ($schema as $g) {
            foreach ($g['fields'] as $k => $def) $allKeys[$k] = $def;
        }

        $changed = [];
        foreach ($allKeys as $key => $def) {
            // Toggle truyền '0'/'1', số/chuỗi truyền nguyên dạng
            $raw = $input[$key] ?? null;

            if ($def['type'] === 'toggle') {
                $val = (string)((int)(!empty($raw) && $raw !== '0' && $raw !== 'false'));
            } else {
                $val = $raw === null ? null : trim((string)$raw);
            }

            // Field sensitive: rỗng → bỏ qua (giữ giá trị cũ); placeholder bullets → bỏ qua
            if (!empty($def['sensitive']) && ($val === '' || $val === null || strpos((string)$val, '••') !== false)) {
                continue;
            }

            // Chỉ lưu nếu thực sự thay đổi
            $current = DM_CauHinh_DAL::get($key);
            if ((string)$current !== (string)$val) {
                DM_CauHinh_DAL::set($key, $val);
                $changed[] = $key;
            }
        }

        if ($changed) {
            DM_NhatKyHeThong_DAL::log($userId, Constants::MODULE_HE_THONG,
                'Cập nhật cấu hình: ' . implode(', ', $changed), 'DM_CAU_HINH', 0);
        }
        return ['success' => true, 'message' => 'Đã lưu cấu hình' . ($changed ? ' (' . count($changed) . ' thay đổi)' : ' (không thay đổi)')];
    }

    /** Test gửi mail tới địa chỉ chỉ định. Yêu cầu MAIL_ENABLED=1 và SMTP_HOST/USER. */
    public static function testMail(string $toEmail, string $toName = 'Admin'): array
    {
        if (!filter_var($toEmail, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'Email nhận không hợp lệ'];
        }

        $enabled = (int)(DM_CauHinh_DAL::get('MAIL_ENABLED') ?? '0');
        if ($enabled !== 1) {
            return ['success' => false, 'message' => 'MAIL_ENABLED đang TẮT. Bật bộ nhớ rồi lưu trước khi test.'];
        }
        if (!DM_CauHinh_DAL::get('SMTP_HOST') || !DM_CauHinh_DAL::get('SMTP_USER')) {
            return ['success' => false, 'message' => 'Chưa nhập SMTP host hoặc user'];
        }

        $subject = '[Test] Cấu hình SMTP - ' . AppConfig::APP_NAME;
        $body = '<h3 style="color:#2563eb">Kiểm tra cấu hình SMTP thành công</h3>'
              . '<p>Đây là email kiểm tra được gửi từ hệ thống <strong>' . htmlspecialchars(AppConfig::APP_NAME) . '</strong>.</p>'
              . '<p>Nếu bạn nhận được email này, cấu hình SMTP đã hoạt động đúng.</p>'
              . '<p><small>Thời gian: ' . date('Y-m-d H:i:s') . '</small></p>';

        $ok = MailHelper::send($toEmail, $toName, $subject, $body);
        if ($ok) {
            return ['success' => true, 'message' => 'Đã gửi mail test tới ' . $toEmail . '. Kiểm tra hộp thư (kèm Spam) trong vài phút.'];
        }
        return ['success' => false, 'message' => 'Gửi mail thất bại. Xem chi tiết tại assets/uploads/maillog.txt'];
    }
}
