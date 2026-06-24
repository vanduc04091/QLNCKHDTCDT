<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../BUS/DT_BaoCao_BUS.php';

Helper::requireLogin();
if (!PhanQuyenHelper::hasQuyen('DT_BaoCao', PhanQuyenHelper::QUYEN_XEM)) { http_response_code(403); echo 'Không có quyền'; exit; }

$loai = Helper::get('loai', 'khoa');
$from = Helper::get('from', '');
$to   = Helper::get('to', '');
$today = date('Ymd');
$kyText = ($from || $to) ? (' (từ ' . ($from ? date('d/m/Y', strtotime($from)) : '…') . ' đến ' . ($to ? date('d/m/Y', strtotime($to)) : '…') . ')') : '';

if ($loai === 'tong') {
    $tk = DT_BaoCao_BUS::thongKeTong($from, $to);
    $rows = [
        ['Tổng học viên', $tk['hoc_vien']],
        ['Tổng khóa học', $tk['khoa_hoc']],
        ['Tổng chương trình đào tạo', $tk['ctdt']],
        ['Tổng bài học', $tk['bai_hoc']],
        ['Lượt ghi danh', $tk['ghi_danh']],
        ['Buổi học đã lên lịch', $tk['lich_hoc']],
        ['Chứng chỉ đã cấp', $tk['chung_chi']],
        ['Đơn đăng ký', $tk['dang_ky']],
    ];
    ExcelHelper::download('bao-cao-thong-ke-tong-' . $today . '.xlsx', [[
        'name' => 'Thống kê tổng', 'title' => 'BÁO CÁO THỐNG KÊ TỔNG HỢP ĐÀO TẠO' . $kyText,
        'headers' => ['Chỉ tiêu', 'Số lượng'], 'rows' => $rows,
    ]]);
}

if ($loai === 'hv') {
    $khct = (int)Helper::get('khct_id', 0);
    $tt = DT_BaoCao_BUS::thongTinKhct($khct);
    $data = DT_BaoCao_BUS::dsHocVienKetQua($khct, $from, $to);
    $gt = ['M' => 'Nam', 'F' => 'Nữ', 'Nam' => 'Nam', 'Nữ' => 'Nữ'];
    $f = fn($x) => ($x === null || $x === '') ? '' : (float)$x;
    $headers = ['STT', 'Mã HV', 'Họ tên', 'Ngày sinh', 'Giới tính', 'Đơn vị công tác',
                'TX', 'GK', 'CK', 'Tổng kết', 'Xếp loại', 'Chuyên cần (%)', 'Đạt'];
    $rows = []; $i = 0;
    foreach ($data as $r) {
        $i++;
        $cc = ((int)$r['tong_buoi'] > 0) ? round($r['co_mat'] / $r['tong_buoi'] * 100) : '';
        $rows[] = [$i, $r['ma_hv'], $r['ho_ten'],
            !empty($r['ngay_sinh']) ? date('d/m/Y', strtotime($r['ngay_sinh'])) : '',
            $gt[$r['gioi_tinh'] ?? ''] ?? ($r['gioi_tinh'] ?? ''),
            $r['don_vi_cong_tac'] ?? '',
            $f($r['diem_thuong_xuyen']), $f($r['diem_giua_ky']), $f($r['diem_cuoi_ky']), $f($r['diem_tong_ket']),
            $r['xep_loai'] ?? '', $cc,
            $r['dat'] === null ? '' : ((int)$r['dat'] === 1 ? 'Đạt' : 'Chưa đạt')];
    }
    $tieude = 'BẢNG ĐIỂM & KẾT QUẢ';
    if ($tt) $tieude .= ' — ' . $tt['ma_khoa_hoc'] . ' | ' . $tt['ma_chuong_trinh'] . ' - ' . $tt['ten_chuong_trinh'];
    $tieude .= $kyText;
    ExcelHelper::download('bao-cao-ds-hv-ketqua-' . $today . '.xlsx', [[
        'name' => 'DS HV - Kết quả', 'title' => $tieude, 'headers' => $headers, 'rows' => $rows,
    ]]);
}

// mặc định: theo khóa/CTĐT
$khoaHocId = (int)Helper::get('khoa_hoc_id', 0);
$data = DT_BaoCao_BUS::theoKhoaCtdt($khoaHocId, $from, $to);
$headers = ['STT', 'Mã khóa', 'Tên khóa học', 'Mã CTĐT', 'Tên chương trình', 'Số HV', 'Đạt', 'Không đạt', 'Điểm TB', 'Chứng chỉ'];
$rows = []; $i = 0;
foreach ($data as $r) {
    $i++;
    $rows[] = [$i, $r['ma_khoa_hoc'], $r['ten_khoa_hoc'], $r['ma_chuong_trinh'], $r['ten_chuong_trinh'],
               (int)$r['so_hv'], (int)$r['so_dat'], (int)$r['so_khong_dat'],
               $r['diem_tb'] !== null ? (float)$r['diem_tb'] : '', (int)$r['so_chung_chi']];
}
ExcelHelper::download('bao-cao-theo-khoa-ctdt-' . $today . '.xlsx', [[
    'name' => 'Theo khóa - CTĐT', 'title' => 'BÁO CÁO ĐÀO TẠO THEO KHÓA / CHƯƠNG TRÌNH' . $kyText,
    'headers' => $headers, 'rows' => $rows,
]]);
