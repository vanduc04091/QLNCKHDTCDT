<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../BUS/DT_Cme_BUS.php';

Helper::requireLogin();
if (!PhanQuyenHelper::hasQuyen('DT_CME', PhanQuyenHelper::QUYEN_XEM)) { http_response_code(403); echo 'Không có quyền'; exit; }

$opts = [
    'search'        => Helper::get('search', ''),
    'nam'           => (int)Helper::get('nam', 0),
    'khoa_phong_id' => (int)Helper::get('khoa_phong_id', 0),
    'nhom_id'       => (int)Helper::get('nhom_id', 0),
];
$daXoa = (int)Helper::get('da_xoa', 0);
$res = DT_Cme_BUS::ghiNhanGetPaged(1, 100000, $opts, $daXoa);

$kieu = ['theo_tiet' => 'Theo tiết', 'co_dinh' => 'Cố định', 'theo_nam' => 'Theo năm'];
$fmt = fn($d) => !empty($d) ? date('d/m/Y', strtotime($d)) : '';

$headers = ['STT', 'Mã NV', 'Họ tên', 'Khoa/Phòng', 'Năm', 'Nhóm hình thức', 'Loại hình thức',
            'Hoạt động', 'Vai trò', 'Số lượng', 'Giờ tín chỉ', 'Từ ngày', 'Đến ngày', 'Minh chứng', 'Ghi chú'];
$rows = [];
$i = 0;
foreach ($res['data'] as $r) {
    $i++;
    $rows[] = [
        $i,
        $r['ma_nv'] ?? '',
        $r['ho_ten_nhan_vien'] ?? '',
        $r['ten_khoa_phong'] ?? '',
        $r['nam'] ?? '',
        $r['ten_nhom'] ?? '',
        $r['ten_loai'] ?? '',
        $r['ten_hoat_dong'] ?? '',
        $r['vai_tro'] ?? '',
        (float)($r['so_luong'] ?? 0),
        (float)($r['gio_tin_chi'] ?? 0),
        $fmt($r['ngay_bat_dau'] ?? ''),
        $fmt($r['ngay_ket_thuc'] ?? ''),
        !empty($r['minh_chung']) ? ($r['minh_chung_goc'] ?: 'Có') : '',
        $r['ghi_chu'] ?? '',
    ];
}
$namTxt = $opts['nam'] ? (' năm ' . $opts['nam']) : '';
ExcelHelper::download('theo-doi-tin-chi-cme-' . date('Ymd') . '.xlsx', [[
    'name' => 'Ghi nhận CME', 'title' => 'SỔ THEO DÕI TÍN CHỈ CME' . $namTxt,
    'headers' => $headers, 'rows' => $rows,
]]);
