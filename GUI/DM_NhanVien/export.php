<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../BUS/DM_NhanVien_BUS.php';

Helper::requireLogin();
if (!PhanQuyenHelper::hasQuyen('DM_NhanVien', PhanQuyenHelper::QUYEN_XEM)) { http_response_code(403); echo 'Không có quyền'; exit; }

$search = Helper::get('search', '');
$daXoa  = (int)Helper::get('da_xoa', 0);
$kp = (int)Helper::get('khoa_phong_id', 0);

$res = DM_NhanVien_BUS::getPaged(1, 100000, $search, $daXoa, $kp);
$gt = ['M' => 'Nam', 'F' => 'Nữ', 'Nam' => 'Nam', 'Nữ' => 'Nữ'];
$tt = [1 => 'Đang làm', 0 => 'Nghỉ'];

$headers = ['STT', 'Mã NV', 'Họ tên', 'Ngày sinh', 'Giới tính', 'Chức danh', 'Khoa/Phòng', 'Trình độ', 'Điện thoại', 'Email', 'Trạng thái'];
$rows = []; $i = 0;
foreach ($res['data'] as $r) {
    $i++;
    $rows[] = [$i, $r['ma_nv'] ?? '', $r['ho_ten'] ?? '',
               !empty($r['ngay_sinh']) ? date('d/m/Y', strtotime($r['ngay_sinh'])) : '',
               $gt[$r['gioi_tinh'] ?? ''] ?? ($r['gioi_tinh'] ?? ''),
               $r['chuc_danh'] ?? '', $r['ten_khoa_phong'] ?? '', $r['trinh_do'] ?? '',
               $r['dien_thoai'] ?? '', $r['email'] ?? '', $tt[(int)($r['trang_thai'] ?? 1)] ?? ''];
}
ExcelHelper::downloadOne('danh-sach-nhan-vien-' . date('Ymd') . '.xlsx', 'Nhân viên', $headers, $rows);
