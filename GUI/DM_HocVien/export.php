<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../BUS/DM_HocVien_BUS.php';

Helper::requireLogin();
if (!PhanQuyenHelper::hasQuyen('DM_HocVien', PhanQuyenHelper::QUYEN_XEM)) {
    http_response_code(403); echo 'Không có quyền'; exit;
}

// Lọc theo cùng tham số với màn danh sách (truyền qua query string)
$search = Helper::get('search', '');
$daXoa  = (int)Helper::get('da_xoa', 0);
$dtId   = (int)Helper::get('doi_tuong_id', 0);
$lnv    = ($v = Helper::get('la_nhan_vien', '')) !== '' ? (int)$v : -1;

$res = DM_HocVien_BUS::getPaged(1, 100000, $search, $daXoa, $dtId, $lnv);
$gt = ['M' => 'Nam', 'F' => 'Nữ', 'Nam' => 'Nam', 'Nữ' => 'Nữ'];

$headers = ['STT', 'Mã HV', 'Họ tên', 'Ngày sinh', 'Giới tính', 'Điện thoại', 'Email', 'CCCD',
            'Đối tượng', 'Đơn vị công tác', 'Chức vụ', 'Là nhân viên', 'Trạng thái'];
$rows = [];
$i = 0;
foreach ($res['data'] as $r) {
    $i++;
    $rows[] = [
        $i,
        $r['ma_hv'] ?? '',
        $r['ho_ten'] ?? '',
        !empty($r['ngay_sinh']) ? date('d/m/Y', strtotime($r['ngay_sinh'])) : '',
        $gt[$r['gioi_tinh'] ?? ''] ?? ($r['gioi_tinh'] ?? ''),
        $r['dien_thoai'] ?? '',
        $r['email'] ?? '',
        $r['cccd'] ?? '',
        $r['ten_doi_tuong'] ?? '',
        $r['don_vi_cong_tac'] ?? '',
        $r['chuc_vu'] ?? '',
        (int)($r['la_nhan_vien'] ?? 0) === 1 ? 'Có' : '',
        (int)($r['trang_thai'] ?? 1) === 1 ? 'Hoạt động' : 'Ngừng',
    ];
}

ExcelHelper::downloadOne('danh-sach-hoc-vien-' . date('Ymd') . '.xlsx', 'Học viên', $headers, $rows);
