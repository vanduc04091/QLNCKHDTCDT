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

// mặc định: theo nhân viên — MỖI NV 1 DÒNG, chi tiết hoạt động gộp vào cột Ghi chú
$ng = DT_Cme_BUS::getNguong();
$data = DT_Cme_BUS::chiTietTheoNhanVien(['nam' => $nam, 'khoa_phong_id' => $khoa]);
$headers = ['STT', 'Mã NV', 'Họ tên', 'Khoa / Phòng', 'Số hoạt động', 'Tổng giờ tín chỉ',
            'Ngưỡng', 'Đạt?', 'Ghi chú (chi tiết hoạt động)'];

// Gom bản ghi theo nhân viên
$nvMap = [];
foreach ($data as $r) {
    $id = (int)$r['nhan_vien_id'];
    if (!isset($nvMap[$id])) {
        $nvMap[$id] = [
            'ma_nv'   => $r['ma_nv'],
            'ho_ten'  => $r['ho_ten'],
            'khoa'    => $r['ten_khoa_phong'] ?? '',
            'tong'    => 0.0,
            'hoat_dong' => [],
        ];
    }
    $gio = (float)$r['gio_tin_chi'];
    $nvMap[$id]['tong'] += $gio;

    // 1 dòng mô tả: "• Tên hoạt động [vai trò] — N giờ"
    // Loại hình thức bỏ phần sau dấu "—" cho gọn (VD "Hội nghị, hội thảo — Chủ trì" -> "Hội nghị, hội thảo")
    $mo = trim($r['ten_hoat_dong'] ?? '') ?: ($r['ten_loai'] ?? 'Hoạt động');
    $loaiGon = trim(preg_split('/\s+[—-]\s+/u', (string)($r['ten_loai'] ?? ''))[0]);
    $vt = trim((string)($r['vai_tro'] ?? ''));

    $phu = [];
    if ($loaiGon !== '') $phu[] = $loaiGon;
    // chỉ thêm vai trò nếu chưa trùng với tên loại
    if ($vt !== '' && mb_stripos($r['ten_loai'] ?? '', $vt, 0, 'UTF-8') === false) $phu[] = $vt;

    $line = '• ' . $mo;
    if ($phu) $line .= ' [' . implode(' · ', $phu) . ']';
    $line .= ' — ' . $fmtN($gio) . ' giờ';
    $nvMap[$id]['hoat_dong'][] = $line;
}

$rows = [];
$stt = 0;
$tongToan = 0.0;
foreach ($nvMap as $nv) {
    $stt++;
    $tongToan += $nv['tong'];
    $dat = ($nv['tong'] >= $ng['gio']) ? 'Đạt' : 'Chưa đạt';
    $rows[] = [
        $stt,
        $nv['ma_nv'],
        $nv['ho_ten'],
        $nv['khoa'],
        count($nv['hoat_dong']),
        round($nv['tong'], 2),
        $ng['gio'],
        $dat,
        implode("\n", $nv['hoat_dong']),   // chi tiết trong 1 ô, xuống dòng
    ];
}

// Tổng toàn báo cáo
$rows[] = ['', '', 'TỔNG CỘNG TOÀN BÁO CÁO', '', '', round($tongToan, 2), '', '', ''];

ExcelHelper::download('bao-cao-cme-theo-nhan-vien-' . $today . '.xlsx', [[
    'name' => 'Theo nhân viên', 'title' => 'BÁO CÁO CME THEO NHÂN VIÊN' . $namTxt,
    'headers' => $headers, 'rows' => $rows,
]]);
