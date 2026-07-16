<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../BUS/DM_GiangVien_BUS.php';

Helper::requireLogin();
if (!PhanQuyenHelper::hasQuyen('DM_GiangVien', PhanQuyenHelper::QUYEN_XEM)) { http_response_code(403); echo 'Không có quyền'; exit; }

$search = Helper::get('search', '');
$daXoa  = (int)Helper::get('da_xoa', 0);
$loai   = (int)Helper::get('loai_gv', 0);
$tt     = ($v = Helper::get('trang_thai', '')) !== '' ? (int)$v : -1;

$res = ['data' => ExportHelper::fetchAll(fn($__p, $__s) => DM_GiangVien_BUS::getPaged($__p, $__s, $search, $daXoa, $loai, $tt))];
$ttLbl = [1 => 'Hoạt động', 0 => 'Ngừng'];
$loaiLbl = [1 => 'Trong cơ quan', 2 => 'Ngoài'];

$headers = ['STT', 'Mã GV', 'Họ tên', 'Học vị', 'Học hàm', 'Chuyên môn', 'Đơn vị công tác', 'Loại GV', 'Điện thoại', 'Email', 'Trạng thái'];
$rows = []; $i = 0;
foreach ($res['data'] as $r) {
    $i++;
    $rows[] = [$i, $r['ma_gv'] ?? '', $r['ho_ten'] ?? '', $r['hoc_vi'] ?? '', $r['hoc_ham'] ?? '',
               $r['chuyen_mon'] ?? '', $r['don_vi_cong_tac'] ?? ($r['ten_khoa_phong'] ?? ''),
               $loaiLbl[(int)($r['loai_gv'] ?? 0)] ?? '', $r['dien_thoai'] ?? '', $r['email'] ?? '',
               $ttLbl[(int)($r['trang_thai'] ?? 1)] ?? ''];
}
ExcelHelper::downloadOne('danh-sach-giang-vien-' . date('Ymd') . '.xlsx', 'Giảng viên', $headers, $rows);
