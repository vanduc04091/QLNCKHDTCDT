<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../BUS/DT_Cme_BUS.php';

Helper::requireLogin();
if (!PhanQuyenHelper::hasQuyen('DT_CME', PhanQuyenHelper::QUYEN_XEM)) { http_response_code(403); echo 'Không có quyền'; exit; }

$loai = Helper::get('loai', 'nv');
$nam  = (int)Helper::get('nam', 0);
$khoa = (int)Helper::get('khoa_phong_id', 0);
$today = date('Ymd');
$namTxt = $nam ? (' năm ' . $nam) : '';
$fmtN = fn($x) => (float)$x;

if ($loai === 'nhom') {
    $data = DT_Cme_BUS::tongToanVienTheoNhom($nam);
    $headers = ['STT', 'Nhóm hình thức', 'Số bản ghi', 'Tổng giờ tín chỉ'];
    $rows = []; $i = 0;
    foreach ($data as $r) { $i++; $rows[] = [$i, $r['ten_nhom'], (int)$r['so_ban_ghi'], $fmtN($r['tong_gio'])]; }
    ExcelHelper::download('bao-cao-cme-theo-nhom-' . $today . '.xlsx', [[
        'name' => 'Theo nhóm', 'title' => 'BÁO CÁO CME THEO NHÓM HÌNH THỨC' . $namTxt,
        'headers' => $headers, 'rows' => $rows,
    ]]);
}

if ($loai === 'khoa') {
    $data = DT_Cme_BUS::tongTheoKhoaPhong($nam);
    $headers = ['STT', 'Khoa / Phòng', 'Số nhân viên', 'Số bản ghi', 'Tổng giờ tín chỉ'];
    $rows = []; $i = 0;
    foreach ($data as $r) { $i++; $rows[] = [$i, $r['ten_khoa'] ?: '(Chưa gán khoa)', (int)$r['so_nhan_vien'], (int)$r['so_ban_ghi'], $fmtN($r['tong_gio'])]; }
    ExcelHelper::download('bao-cao-cme-theo-khoa-' . $today . '.xlsx', [[
        'name' => 'Theo khoa', 'title' => 'BÁO CÁO CME THEO KHOA / PHÒNG' . $namTxt,
        'headers' => $headers, 'rows' => $rows,
    ]]);
}

// mặc định: theo nhân viên — liệt kê CHI TIẾT từng bản ghi, gộp theo NV + dòng tổng mỗi NV + tổng cuối
$ng = DT_Cme_BUS::getNguong();
$data = DT_Cme_BUS::chiTietTheoNhanVien(['nam' => $nam, 'khoa_phong_id' => $khoa]);
$headers = ['STT', 'Mã NV', 'Họ tên', 'Khoa / Phòng', 'Năm', 'Nhóm hình thức', 'Loại hình thức',
            'Hoạt động', 'Vai trò', 'Số lượng', 'Giờ tín chỉ', 'Từ ngày', 'Đến ngày'];

$rows = [];
$stt = 0;
$tongToan = 0.0;
$curNv = null; $tongNv = 0.0; $tenNv = '';
$flushNv = function () use (&$rows, &$tongNv, &$tenNv, &$curNv, $ng) {
    if ($curNv === null) return;
    $dat = ($tongNv >= $ng['gio']) ? 'Đạt' : 'Chưa đạt';
    // Dòng tổng của NV
    $rows[] = ['', '', 'TỔNG ' . $tenNv, '', '', '', '', '', '', '', round($tongNv, 2),
               'Ngưỡng ' . $ng['gio'] . ' → ' . $dat, ''];
};

foreach ($data as $r) {
    $nvId = (int)$r['nhan_vien_id'];
    if ($curNv !== null && $nvId !== $curNv) { $flushNv(); $tongNv = 0.0; }
    $curNv = $nvId; $tenNv = $r['ho_ten'];
    $stt++;
    $gio = (float)$r['gio_tin_chi'];
    $tongNv += $gio; $tongToan += $gio;
    $rows[] = [
        $stt, $r['ma_nv'], $r['ho_ten'], $r['ten_khoa_phong'] ?? '', $r['nam'],
        $r['ten_nhom'] ?? '', $r['ten_loai'] ?? '',
        $r['ten_hoat_dong'] ?? '', $r['vai_tro'] ?? '',
        $fmtN($r['so_luong']), $gio,
        !empty($r['ngay_bat_dau']) ? date('d/m/Y', strtotime($r['ngay_bat_dau'])) : '',
        !empty($r['ngay_ket_thuc']) ? date('d/m/Y', strtotime($r['ngay_ket_thuc'])) : '',
    ];
}
$flushNv(); // tổng NV cuối

// Tổng toàn báo cáo
$rows[] = ['', '', 'TỔNG CỘNG TOÀN BÁO CÁO', '', '', '', '', '', '', '', round($tongToan, 2), '', ''];

ExcelHelper::download('bao-cao-cme-chi-tiet-theo-nhan-vien-' . $today . '.xlsx', [[
    'name' => 'Chi tiết theo NV', 'title' => 'BÁO CÁO CME CHI TIẾT THEO NHÂN VIÊN' . $namTxt,
    'headers' => $headers, 'rows' => $rows,
]]);
