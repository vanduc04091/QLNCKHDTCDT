<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../BUS/DM_LoaiHinhDaoTao_BUS.php';

Helper::requireLogin();
if (!PhanQuyenHelper::hasQuyen('DM_LoaiHinhDaoTao', PhanQuyenHelper::QUYEN_XEM)) { http_response_code(403); echo 'Không có quyền'; exit; }

$res = ['data' => ExportHelper::fetchAll(fn($__p, $__s) => DM_LoaiHinhDaoTao_BUS::getPaged($__p, $__s, Helper::get('search', ''), (int)Helper::get('da_xoa', 0)))];
$ttLbl = [1 => 'Hoạt động', 0 => 'Ngừng'];
$headers = ['STT', 'Mã', 'Tên loại hình', 'Mô tả', 'Thứ tự', 'Trạng thái'];
$rows = []; $i = 0;
foreach ($res['data'] as $r) {
    $i++;
    $rows[] = [$i, $r['ma_loai_hinh'] ?? '', $r['ten_loai_hinh'] ?? '', $r['mo_ta'] ?? '',
               (int)($r['thu_tu'] ?? 0), $ttLbl[(int)($r['trang_thai'] ?? 1)] ?? ''];
}
ExcelHelper::downloadOne('loai-hinh-dao-tao-' . date('Ymd') . '.xlsx', 'Loại hình đào tạo', $headers, $rows);
