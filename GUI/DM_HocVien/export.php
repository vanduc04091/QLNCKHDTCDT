<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../BUS/DM_HocVien_BUS.php';
require_once __DIR__ . '/../../BUS/DT_HocVienLop_BUS.php';

Helper::requireLogin();
if (!PhanQuyenHelper::hasQuyen('DM_HocVien', PhanQuyenHelper::QUYEN_XEM)) {
    http_response_code(403); echo 'Không có quyền'; exit;
}

// Lọc theo cùng tham số với màn danh sách (truyền qua query string)
$search = Helper::get('search', '');
$daXoa  = (int)Helper::get('da_xoa', 0);
$dtId   = (int)Helper::get('doi_tuong_id', 0);
$lnv    = ($v = Helper::get('la_nhan_vien', '')) !== '' ? (int)$v : -1;
$tuNgay = Helper::get('tu_ngay', '');
$denNgay = Helper::get('den_ngay', '');

$res = ['data' => ExportHelper::fetchAll(fn($__p, $__s) => DM_HocVien_BUS::getPaged($__p, $__s, $search, $daXoa, $dtId, $lnv, $tuNgay, $denNgay))];
$hocViens = $res['data'];

// Ghi danh (khóa/CTĐT/ngày/địa điểm) của tất cả HV — gộp 1 truy vấn
$hvIds = array_map(fn($r) => (int)$r['id'], $hocViens);
$enrollMap = DT_HocVienLop_BUS::getEnrollmentsForExport($hvIds);

$gt = ['M' => 'Nam', 'F' => 'Nữ', 'Nam' => 'Nam', 'Nữ' => 'Nữ'];
$fmt = fn($d) => !empty($d) ? date('d/m/Y', strtotime($d)) : '';
// Ghép "MÃ - Tên" (đúng định dạng mẫu, để file xuất ra có thể import lại)
$codeName = fn($ma, $ten) => trim(($ma ?? '') . (($ma && $ten) ? ' - ' : '') . ($ten ?? ''));

// Cấu trúc trùng file mẫu "Danh sách nhập thông tin học viên" (21 cột)
$headers = ['TT', 'Họ và tên', 'Trạng thái', 'Ngày sinh', 'Giới tính',
            'Trình độ chuyên môn', 'Đối tượng học viên', 'Điện thoại', 'Email',
            'Căn cước công dân', 'Ngày cấp', 'Nơi cấp', 'Đơn vị công tác',
            'Địa chỉ thường trú', 'Trường đào tạo', 'Năm tốt nghiệp',
            'Khóa học', 'Chương trình đào tạo', 'Ngày bắt đầu học',
            'Ngày kết thúc', 'Địa điểm học'];

$rows = [];
$stt = 0;
foreach ($hocViens as $r) {
    $stt++;
    // Cột thông tin cá nhân (giống nhau cho mọi dòng ghi danh của HV này)
    $base = [
        $stt,
        $r['ho_ten'] ?? '',
        (int)($r['trang_thai'] ?? 1) === 1 ? 'Hoạt động' : 'Ngừng',
        $fmt($r['ngay_sinh'] ?? ''),
        $gt[$r['gioi_tinh'] ?? ''] ?? ($r['gioi_tinh'] ?? ''),
        $r['trinh_do_chuyen_mon'] ?? '',
        $r['ten_doi_tuong'] ?? '',
        $r['dien_thoai'] ?? '',
        $r['email'] ?? '',
        $r['cccd'] ?? '',
        $fmt($r['cccd_ngay_cap'] ?? ''),
        $r['cccd_noi_cap'] ?? '',
        $r['don_vi_cong_tac'] ?? '',
        $r['dia_chi'] ?? '',
        $r['truong_dao_tao'] ?? '',
        $r['nam_tot_nghiep'] ?? '',
    ];

    $enrolls = $enrollMap[(int)$r['id']] ?? [];
    if (!$enrolls) {
        // HV chưa ghi danh -> 1 dòng, để trống phần học vụ
        $rows[] = array_merge($base, ['', '', '', '', '']);
        continue;
    }
    // Mỗi ghi danh 1 dòng (giống mẫu: 1 HV có thể lặp nhiều dòng theo CTĐT)
    foreach ($enrolls as $en) {
        $rows[] = array_merge($base, [
            $codeName($en['ma_khoa_hoc'] ?? '', $en['ten_khoa_hoc'] ?? ''),
            $codeName($en['ma_chuong_trinh'] ?? '', $en['ten_chuong_trinh'] ?? ''),
            $fmt($en['ngay_bat_dau'] ?? ''),
            $fmt($en['ngay_ket_thuc'] ?? ''),
            $en['dia_diem'] ?? '',
        ]);
    }
}

ExcelHelper::download('danh-sach-hoc-vien-' . date('Ymd') . '.xlsx', [[
    'name'    => 'Danh sách HV',
    'title'   => 'DANH SÁCH NHẬP THÔNG TIN HỌC VIÊN',
    'headers' => $headers,
    'rows'    => $rows,
]]);
