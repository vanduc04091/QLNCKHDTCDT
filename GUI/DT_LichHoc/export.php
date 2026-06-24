<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../BUS/DT_LichHoc_BUS.php';

Helper::requireLogin();
if (!PhanQuyenHelper::hasQuyen('DT_LichHoc', PhanQuyenHelper::QUYEN_XEM)) { http_response_code(403); echo 'Không có quyền'; exit; }

$opts = [
    'lop_hoc_id'    => (int)Helper::get('lop_hoc_id', 0),
    'giang_vien_id' => (int)Helper::get('giang_vien_id', 0),
    'trang_thai'    => ($v = Helper::get('trang_thai', '')) !== '' ? (int)$v : -1,
    'from'          => Helper::get('from', '') ?: null,
    'to'            => Helper::get('to', '') ?: null,
    'search'        => Helper::get('search', ''),
];
$res = DT_LichHoc_BUS::getPaged(1, 100000, $opts, (int)Helper::get('da_xoa', 0));
$ttLbl = [0 => 'Dự kiến', 1 => 'Đã dạy', 2 => 'Đã hủy'];
$fd = fn($d) => !empty($d) ? date('d/m/Y', strtotime($d)) : '';
$fg = fn($t) => $t ? substr($t, 0, 5) : '';

$headers = ['STT', 'Buổi', 'Ngày học', 'Giờ BĐ', 'Giờ KT', 'Khóa học', 'Chương trình', 'Bài học', 'Giảng viên', 'Phòng', 'Trạng thái'];
$rows = []; $i = 0;
foreach ($res['data'] as $r) {
    $i++;
    $rows[] = [$i, $r['buoi_thu'] ?? '', $fd($r['ngay_hoc'] ?? ''), $fg($r['gio_bat_dau'] ?? ''), $fg($r['gio_ket_thuc'] ?? ''),
               $r['ma_khoa_hoc'] ?? '', $r['ma_lop'] ?? '', $r['ten_mon_hoc'] ?? ($r['tieu_de'] ?? ''),
               $r['ten_giang_vien'] ?? ($r['giang_vien_ngoai'] ?? ''), $r['phong_hoc'] ?? '',
               $ttLbl[(int)($r['trang_thai'] ?? 0)] ?? ''];
}
ExcelHelper::downloadOne('danh-sach-lich-hoc-' . date('Ymd') . '.xlsx', 'Lịch học', $headers, $rows);
