<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../BUS/DT_ChuongTrinh_BUS.php';

Helper::requireLogin();
if (!PhanQuyenHelper::hasQuyen('DT_ChuongTrinh', PhanQuyenHelper::QUYEN_XEM)) { http_response_code(403); echo 'Không có quyền'; exit; }

$search = Helper::get('search', '');
$daXoa  = (int)Helper::get('da_xoa', 0);
$kh = (int)Helper::get('khoa_hoc_id', 0);
$dt = (int)Helper::get('doi_tuong_id', 0);

$res = DT_ChuongTrinh_BUS::getPaged(1, 100000, $search, $daXoa, $kh, $dt);

$headers = ['STT', 'TT', 'Mã CTĐT', 'Tên chương trình', 'Thời lượng', 'Khoa phụ trách', 'Đối tượng', 'Số khóa', 'Số bài', 'Số HV'];
$rows = []; $i = 0;
foreach ($res['data'] as $r) {
    $i++;
    $rows[] = [$i, $r['thu_tu'] ?? 0, $r['ma_chuong_trinh'] ?? '', $r['ten_chuong_trinh'] ?? '', $r['thoi_luong'] ?? '',
               $r['ten_khoa_phong'] ?? '', $r['ten_doi_tuong'] ?? '',
               (int)($r['so_khoa_hoc'] ?? 0), (int)($r['so_mon_hoc'] ?? 0), (int)($r['so_hoc_vien'] ?? 0)];
}
ExcelHelper::downloadOne('danh-sach-ctdt-' . date('Ymd') . '.xlsx', 'Chương trình đào tạo', $headers, $rows);
