<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../BUS/DT_KhoaHoc_BUS.php';

Helper::requireLogin();
if (!PhanQuyenHelper::hasQuyen('DT_KhoaHoc', PhanQuyenHelper::QUYEN_XEM)) { http_response_code(403); echo 'Không có quyền'; exit; }

$search = Helper::get('search', '');
$daXoa  = (int)Helper::get('da_xoa', 0);
$lh = (int)Helper::get('loai_hinh_dao_tao_id', 0);
$ht = (int)Helper::get('hinh_thuc_hoc_id', 0);
$dt = (int)Helper::get('doi_tuong_hoc_vien_id', 0);

$res = ['data' => ExportHelper::fetchAll(fn($__p, $__s) => DT_KhoaHoc_BUS::getPaged($__p, $__s, $search, $daXoa, $lh, $ht, $dt))];
$tt = [1 => 'Hoạt động', 0 => 'Ngừng'];
$fd = fn($d) => !empty($d) ? date('d/m/Y', strtotime($d)) : '';

$headers = ['STT', 'Mã khóa', 'Tên khóa học', 'Loại hình', 'Hình thức', 'Đối tượng', 'Ngày bắt đầu', 'Ngày kết thúc', 'Trạng thái'];
$rows = []; $i = 0;
foreach ($res['data'] as $r) {
    $i++;
    $rows[] = [$i, $r['ma_khoa_hoc'] ?? '', $r['ten_khoa_hoc'] ?? '', $r['ten_loai_hinh'] ?? '', $r['ten_hinh_thuc'] ?? '',
               $r['ten_doi_tuong'] ?? '', $fd($r['ngay_bat_dau'] ?? ''), $fd($r['ngay_ket_thuc'] ?? ''),
               $tt[(int)($r['trang_thai'] ?? 1)] ?? ''];
}
ExcelHelper::downloadOne('danh-sach-khoa-hoc-' . date('Ymd') . '.xlsx', 'Khóa học', $headers, $rows);
