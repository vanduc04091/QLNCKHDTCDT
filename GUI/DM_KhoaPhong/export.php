<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../BUS/DM_KhoaPhong_BUS.php';

Helper::requireLogin();
if (!PhanQuyenHelper::hasQuyen('DM_KhoaPhong', PhanQuyenHelper::QUYEN_XEM)) { http_response_code(403); echo 'Không có quyền'; exit; }

$search = Helper::get('search', '');
$daXoa  = (int)Helper::get('da_xoa', 0);
$loai   = Helper::get('loai_don_vi', '');

$res = ['data' => ExportHelper::fetchAll(fn($__p, $__s) => DM_KhoaPhong_BUS::getPaged($__p, $__s, $search, $daXoa, $loai))];
$tt = [1 => 'Hoạt động', 0 => 'Ngừng'];

$headers = ['STT', 'Mã', 'Tên khoa/phòng', 'Loại đơn vị', 'Trưởng khoa', 'Chuyên khoa', 'Số giường', 'Điện thoại', 'Số nhân viên', 'Trạng thái'];
$rows = []; $i = 0;
foreach ($res['data'] as $r) {
    $i++;
    $rows[] = [$i, $r['ma_khoa'] ?? '', $r['ten_khoa'] ?? '', $r['loai_don_vi'] ?? '', $r['ten_truong_khoa'] ?? '',
               $r['chuyen_khoa'] ?? '', $r['so_giuong'] ?? '', $r['dien_thoai'] ?? '',
               (int)($r['so_nhan_vien'] ?? 0), $tt[(int)($r['trang_thai'] ?? 1)] ?? ''];
}
ExcelHelper::downloadOne('danh-sach-khoa-phong-' . date('Ymd') . '.xlsx', 'Khoa - Phòng', $headers, $rows);
