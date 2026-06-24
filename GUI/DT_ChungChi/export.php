<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../BUS/DT_ChungChi_BUS.php';

Helper::requireLogin();
if (!PhanQuyenHelper::hasQuyen('DT_ChungChi', PhanQuyenHelper::QUYEN_XEM)) { http_response_code(403); echo 'Không có quyền'; exit; }

$opts = [
    'search'     => Helper::get('search', ''),
    'lop_hoc_id' => (int)Helper::get('lop_hoc_id', 0),
    'loai'       => Helper::get('loai', ''),
    'trang_thai' => ($v = Helper::get('trang_thai', '')) !== '' ? (int)$v : -1,
];
$res = DT_ChungChi_BUS::getPaged(1, 100000, $opts, (int)Helper::get('da_xoa', 0));
$ttLbl = [1 => 'Đã cấp', 0 => 'Nháp', 2 => 'Thu hồi'];
$fd = fn($d) => !empty($d) ? date('d/m/Y', strtotime($d)) : '';

$headers = ['STT', 'Số chứng chỉ', 'Học viên', 'Đơn vị', 'Khóa học', 'Chương trình', 'Tên chứng chỉ', 'Loại', 'Xếp loại', 'Điểm TB', 'Ngày cấp', 'Trạng thái'];
$rows = []; $i = 0;
foreach ($res['data'] as $r) {
    $i++;
    $rows[] = [$i, $r['so_chung_chi'] ?? '', ($r['ma_hoc_vien'] ?? '') . ' - ' . ($r['ho_ten_hoc_vien'] ?? ''),
               $r['don_vi_cong_tac'] ?? '', $r['ma_khoa_hoc'] ?? '', $r['ma_lop'] ?? '',
               $r['ten_chung_chi'] ?? '', $r['loai_chung_chi'] ?? '', $r['xep_loai_tot_nghiep'] ?? '',
               $r['diem_trung_binh'] !== null ? (float)$r['diem_trung_binh'] : '',
               $fd($r['ngay_cap'] ?? ''), $ttLbl[(int)($r['trang_thai'] ?? 0)] ?? ''];
}
ExcelHelper::downloadOne('danh-sach-chung-chi-' . date('Ymd') . '.xlsx', 'Chứng chỉ', $headers, $rows);
