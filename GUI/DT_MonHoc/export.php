<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../BUS/DT_MonHoc_BUS.php';

Helper::requireLogin();
if (!PhanQuyenHelper::hasQuyen('DT_MonHoc', PhanQuyenHelper::QUYEN_XEM)) { http_response_code(403); echo 'Không có quyền'; exit; }

$search = Helper::get('search', '');
$daXoa  = (int)Helper::get('da_xoa', 0);
$tt     = ($v = Helper::get('trang_thai', '')) !== '' ? (int)$v : -1;
$ct     = (int)Helper::get('chuong_trinh_id', 0);

$res = DT_MonHoc_BUS::getPaged(1, 100000, $search, $daXoa, $tt, $ct);
$ttLbl = [1 => 'Hoạt động', 0 => 'Khóa'];

$headers = ['STT', 'Mã bài', 'Tên bài học', 'Chương trình đào tạo', 'LT', 'TH', 'Tổng tiết', 'Tín chỉ', 'Trạng thái'];
$rows = []; $i = 0;
foreach ($res['data'] as $r) {
    $i++;
    $dsCt = array_map(fn($s) => explode('::', $s)[0], array_filter(explode('||', $r['ds_chuong_trinh'] ?? '')));
    $rows[] = [$i, $r['ma_mon_hoc'] ?? '', $r['ten_mon_hoc'] ?? '', implode(', ', $dsCt),
               (int)($r['so_tiet_ly_thuyet'] ?? 0), (int)($r['so_tiet_thuc_hanh'] ?? 0),
               (int)($r['tong_so_tiet'] ?? 0), (float)($r['so_tin_chi'] ?? 0),
               $ttLbl[(int)($r['trang_thai'] ?? 1)] ?? ''];
}
ExcelHelper::downloadOne('danh-sach-bai-hoc-' . date('Ymd') . '.xlsx', 'Bài học', $headers, $rows);
