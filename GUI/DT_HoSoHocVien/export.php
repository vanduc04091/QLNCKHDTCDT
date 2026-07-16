<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../BUS/DT_HoSoHocVien_BUS.php';

Helper::requireLogin();
if (!PhanQuyenHelper::hasQuyen('DT_HoSoHocVien', PhanQuyenHelper::QUYEN_XEM)) { http_response_code(403); echo 'Không có quyền'; exit; }

$opts = [
    'search'      => Helper::get('search', ''),
    'hoc_vien_id' => (int)Helper::get('hoc_vien_id', 0),
    'loai'        => Helper::get('loai_ho_so', ''),
    'trang_thai'  => ($v = Helper::get('trang_thai', '')) !== '' ? (int)$v : -1,
];
$res = ['data' => ExportHelper::fetchAll(fn($__p, $__s) => DT_HoSoHocVien_BUS::getPaged($__p, $__s, $opts, (int)Helper::get('da_xoa', 0)))];
$ttLbl = [1 => 'Hợp lệ', 0 => 'Chờ duyệt', 2 => 'Không hợp lệ'];
$fd = fn($d) => !empty($d) ? date('d/m/Y', strtotime($d)) : '';

$headers = ['STT', 'Học viên', 'Đơn vị', 'Loại hồ sơ', 'Tên hồ sơ', 'Số hiệu', 'Ngày cấp', 'Nơi cấp', 'Trạng thái'];
$rows = []; $i = 0;
foreach ($res['data'] as $r) {
    $i++;
    $rows[] = [$i, ($r['ma_hv'] ?? '') . ' - ' . ($r['ho_ten_hoc_vien'] ?? ''), $r['don_vi_cong_tac'] ?? '',
               $r['loai_ho_so'] ?? '', $r['ten_ho_so'] ?? '', $r['so_hieu'] ?? '',
               $fd($r['ngay_cap'] ?? ''), $r['noi_cap'] ?? '', $ttLbl[(int)($r['trang_thai'] ?? 0)] ?? ''];
}
ExcelHelper::downloadOne('danh-sach-ho-so-hv-' . date('Ymd') . '.xlsx', 'Hồ sơ học viên', $headers, $rows);
