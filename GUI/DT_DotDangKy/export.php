<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../BUS/DT_DotDangKy_BUS.php';

Helper::requireLogin();
if (!PhanQuyenHelper::hasQuyen('DT_DotDangKy', PhanQuyenHelper::QUYEN_XEM)) { http_response_code(403); echo 'Không có quyền'; exit; }

$opts = ['search' => Helper::get('search', ''), 'nam' => (int)Helper::get('nam', 0)];
$res = DT_DotDangKy_BUS::getPaged(1, 100000, $opts);
$ttLbl = [1 => 'Đang mở', 0 => 'Đóng'];
$fd = fn($d) => !empty($d) ? date('d/m/Y', strtotime($d)) : '';

$headers = ['STT', 'Tên đợt', 'Năm', 'Từ ngày', 'Đến ngày', 'Số giai đoạn', 'Số khóa học', 'Trạng thái'];
$rows = []; $i = 0;
foreach ($res['data'] as $r) {
    $i++;
    $rows[] = [$i, $r['ten_dot'] ?? '', $r['nam'] ?? '', $fd($r['tu_ngay'] ?? ''), $fd($r['den_ngay'] ?? ''),
               (int)($r['so_giai_doan'] ?? 0), (int)($r['so_khoa_hoc'] ?? 0), $ttLbl[(int)($r['trang_thai'] ?? 0)] ?? ''];
}
ExcelHelper::downloadOne('danh-sach-dot-dang-ky-' . date('Ymd') . '.xlsx', 'Đợt đăng ký', $headers, $rows);
