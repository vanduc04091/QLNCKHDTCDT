<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../BUS/DT_Cme_BUS.php';

Helper::requireLogin();
if (!PhanQuyenHelper::hasQuyen('DT_CME', PhanQuyenHelper::QUYEN_XEM)) { http_response_code(403); echo 'Không có quyền'; exit; }

$nam  = (int)Helper::get('nam', (int)date('Y'));
$khoa = (int)Helper::get('khoa_phong_id', 0);
$search = Helper::get('search', '');
$trangThai = Helper::get('trang_thai', '');

// page = 0 => lấy toàn bộ (không phân trang)
$res = DT_Cme_BUS::canhBaoChuaDat($nam, [
    'khoa_phong_id' => $khoa, 'search' => $search, 'trang_thai' => $trangThai,
], 0);
$ng = $res['nguong'];

$headers = ['STT', 'Mã NV', 'Họ tên', 'Khoa / Phòng', 'Văn bằng / Trình độ',
            'Số hoạt động', 'Giờ tín chỉ đạt', 'Ngưỡng', 'Còn thiếu', 'Tỷ lệ (%)', 'Tình trạng'];
$rows = [];
$i = 0;
foreach ($res['data'] as $r) {
    $i++;
    $rows[] = [
        $i,
        $r['ma_nv'] ?? '',
        $r['ho_ten'] ?? '',
        $r['ten_khoa_phong'] ?? '',
        $r['trinh_do'] ?? '',
        (int)$r['so_ban_ghi'],
        (float)$r['tong_gio'],
        $ng['gio'],
        (float)$r['con_thieu'],
        (int)$r['phan_tram'],
        ((int)$r['so_ban_ghi'] === 0) ? 'Chưa ghi nhận hoạt động nào' : 'Chưa đạt ngưỡng',
    ];
}

$chuKy = $res['tu_nam'] === $res['den_nam'] ? ('năm ' . $res['nam']) : ('chu kỳ ' . $res['tu_nam'] . '–' . $res['den_nam']);
ExcelHelper::download('canh-bao-cme-chua-dat-' . date('Ymd') . '.xlsx', [[
    'name'    => 'Chưa đạt ngưỡng',
    'title'   => 'DANH SÁCH NHÂN VIÊN CHƯA ĐẠT NGƯỠNG TÍN CHỈ CME — ' . $chuKy
                 . ' (tối thiểu ' . $ng['gio'] . ' giờ)',
    'headers' => $headers,
    'rows'    => $rows,
]]);
