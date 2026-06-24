<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../BUS/DT_KetQuaHocTap_BUS.php';
require_once __DIR__ . '/../../BUS/DT_BaoCao_BUS.php';

Helper::requireLogin();
if (!PhanQuyenHelper::hasQuyen('DT_KetQuaHocTap', PhanQuyenHelper::QUYEN_XEM)) { http_response_code(403); echo 'Không có quyền'; exit; }

$khct = (int)Helper::get('lop_hoc_id', 0);
if ($khct <= 0) { echo 'Vui lòng chọn chương trình đào tạo trước khi xuất.'; exit; }

$data = DT_KetQuaHocTap_BUS::getByLop($khct);
$tt = DT_BaoCao_BUS::thongTinKhct($khct);
$f = fn($x) => ($x === null || $x === '') ? '' : (float)$x;

$headers = ['STT', 'Mã HV', 'Họ tên', 'TX', 'GK', 'CK', 'Tổng kết', 'Xếp loại', 'Đạt'];
$rows = []; $i = 0;
foreach ($data as $r) {
    $i++;
    $rows[] = [$i, $r['ma_hv'] ?? '', $r['ho_ten'] ?? '',
               $f($r['diem_thuong_xuyen']), $f($r['diem_giua_ky']), $f($r['diem_cuoi_ky']), $f($r['diem_tong_ket']),
               $r['xep_loai'] ?? '', $r['dat'] === null ? '' : ((int)$r['dat'] === 1 ? 'Đạt' : 'Chưa đạt')];
}
$tieude = 'BẢNG ĐIỂM';
if ($tt) $tieude .= ' — ' . $tt['ma_khoa_hoc'] . ' | ' . $tt['ma_chuong_trinh'] . ' - ' . $tt['ten_chuong_trinh'];
ExcelHelper::download('bang-diem-' . date('Ymd') . '.xlsx', [[
    'name' => 'Bảng điểm', 'title' => $tieude, 'headers' => $headers, 'rows' => $rows,
]]);
