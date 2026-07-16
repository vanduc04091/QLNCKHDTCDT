<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../BUS/DT_BaiKiemTra_BUS.php';

Helper::requireLogin();
if (!PhanQuyenHelper::hasQuyen('DT_BaiKiemTra', PhanQuyenHelper::QUYEN_XEM)) { http_response_code(403); echo 'Không có quyền'; exit; }

$opts = [
    'search'     => Helper::get('search', ''),
    'lop_hoc_id' => (int)Helper::get('lop_hoc_id', 0),
    'loai_bkt'   => (int)Helper::get('loai_bkt', 0),
    'trang_thai' => ($v = Helper::get('trang_thai', '')) !== '' ? (int)$v : -1,
];
$res = ['data' => ExportHelper::fetchAll(fn($__p, $__s) => DT_BaiKiemTra_BUS::getPaged($__p, $__s, $opts, (int)Helper::get('da_xoa', 0)))];
$loaiLbl = [1 => 'Thường xuyên', 2 => 'Giữa kỳ', 3 => 'Cuối kỳ', 4 => 'Ôn tập'];
$ttLbl = [1 => 'Hiển thị', 0 => 'Ẩn'];
$fd = fn($d) => !empty($d) ? date('d/m/Y', strtotime($d)) : '';

$headers = ['STT', 'Mã', 'Tiêu đề', 'Loại', 'Khóa học', 'Chương trình', 'Ngày kiểm tra', 'Thời gian (phút)', 'Trạng thái'];
$rows = []; $i = 0;
foreach ($res['data'] as $r) {
    $i++;
    $rows[] = [$i, $r['ma_bkt'] ?? '', $r['tieu_de'] ?? '', $loaiLbl[(int)($r['loai_bkt'] ?? 1)] ?? '',
               $r['ma_khoa_hoc'] ?? '', $r['ma_lop'] ?? '', $fd($r['ngay_kiem_tra'] ?? ''),
               $r['thoi_gian_lam_bai'] ?? '', $ttLbl[(int)($r['trang_thai'] ?? 1)] ?? ''];
}
ExcelHelper::downloadOne('danh-sach-bai-kiem-tra-' . date('Ymd') . '.xlsx', 'Bài kiểm tra', $headers, $rows);
