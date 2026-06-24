<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../BUS/DT_DangKyKhoaHoc_BUS.php';

Helper::requireLogin();
if (!PhanQuyenHelper::hasQuyen('DT_DangKyKhoaHoc', PhanQuyenHelper::QUYEN_XEM)) { http_response_code(403); echo 'Không có quyền'; exit; }

$opts = [
    'search'      => Helper::get('search', ''),
    'khoa_hoc_id' => (int)Helper::get('khoa_hoc_id', 0),
    'trang_thai'  => ($v = Helper::get('trang_thai', '')) !== '' ? (int)$v : -1,
];
$res = DT_DangKyKhoaHoc_BUS::getPaged(1, 100000, $opts, (int)Helper::get('da_xoa', 0));
$ttLbl = [0 => 'Chờ duyệt', 1 => 'Đã duyệt', 2 => 'Từ chối'];
$gt = ['M' => 'Nam', 'F' => 'Nữ', 'Nam' => 'Nam', 'Nữ' => 'Nữ'];
$fd = fn($d) => !empty($d) ? date('d/m/Y', strtotime($d)) : '';

$headers = ['STT', 'Mã tra cứu', 'Họ tên', 'Ngày sinh', 'Giới tính', 'CCCD', 'Điện thoại', 'Email', 'Đơn vị', 'Khóa học', 'Ngày ĐK', 'Trạng thái'];
$rows = []; $i = 0;
foreach ($res['data'] as $r) {
    $i++;
    $rows[] = [$i, $r['ma_tra_cuu'] ?? '', $r['ho_ten'] ?? '', $fd($r['ngay_sinh'] ?? ''),
               $gt[$r['gioi_tinh'] ?? ''] ?? ($r['gioi_tinh'] ?? ''), $r['cccd'] ?? '', $r['dien_thoai'] ?? '',
               $r['email'] ?? '', $r['don_vi_cong_tac'] ?? '', $r['ten_khoa_hoc'] ?? '',
               $fd($r['ngay_tao'] ?? ''), $ttLbl[(int)($r['trang_thai'] ?? 0)] ?? ''];
}
ExcelHelper::downloadOne('danh-sach-dang-ky-' . date('Ymd') . '.xlsx', 'Đăng ký khóa học', $headers, $rows);
