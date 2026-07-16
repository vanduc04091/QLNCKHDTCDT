<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../BUS/DM_HinhThucHoc_BUS.php';

Helper::requireLogin();
if (!PhanQuyenHelper::hasQuyen('DM_HinhThucHoc', PhanQuyenHelper::QUYEN_XEM)) { http_response_code(403); echo 'Không có quyền'; exit; }

$res = ['data' => ExportHelper::fetchAll(fn($__p, $__s) => DM_HinhThucHoc_BUS::getPaged($__p, $__s, Helper::get('search', ''), (int)Helper::get('da_xoa', 0)))];
$ttLbl = [1 => 'Hoạt động', 0 => 'Ngừng'];
$headers = ['STT', 'Mã', 'Tên hình thức', 'Mô tả', 'Thứ tự', 'Trạng thái'];
$rows = []; $i = 0;
foreach ($res['data'] as $r) {
    $i++;
    $rows[] = [$i, $r['ma_hinh_thuc'] ?? '', $r['ten_hinh_thuc'] ?? '', $r['mo_ta'] ?? '',
               (int)($r['thu_tu'] ?? 0), $ttLbl[(int)($r['trang_thai'] ?? 1)] ?? ''];
}
ExcelHelper::downloadOne('hinh-thuc-hoc-' . date('Ymd') . '.xlsx', 'Hình thức học', $headers, $rows);
