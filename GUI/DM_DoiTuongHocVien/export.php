<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../BUS/DM_DoiTuongHocVien_BUS.php';

Helper::requireLogin();
if (!PhanQuyenHelper::hasQuyen('DM_DoiTuongHocVien', PhanQuyenHelper::QUYEN_XEM)) { http_response_code(403); echo 'Không có quyền'; exit; }

$res = DM_DoiTuongHocVien_BUS::getPaged(1, 100000, Helper::get('search', ''), (int)Helper::get('da_xoa', 0));
$ttLbl = [1 => 'Hoạt động', 0 => 'Ngừng'];

$headers = ['STT', 'Mã', 'Tên đối tượng', 'Mô tả', 'Thứ tự', 'Trạng thái'];
$rows = []; $i = 0;
foreach ($res['data'] as $r) {
    $i++;
    $rows[] = [$i, $r['ma_doi_tuong'] ?? '', $r['ten_doi_tuong'] ?? '', $r['mo_ta'] ?? '',
               (int)($r['thu_tu'] ?? 0), $ttLbl[(int)($r['trang_thai'] ?? 1)] ?? ''];
}
ExcelHelper::downloadOne('danh-sach-doi-tuong-hv-' . date('Ymd') . '.xlsx', 'Đối tượng học viên', $headers, $rows);
