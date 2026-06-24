<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../BUS/DT_TaiLieu_BUS.php';

Helper::requireLogin();
if (!PhanQuyenHelper::hasQuyen('DT_TaiLieu', PhanQuyenHelper::QUYEN_XEM)) { http_response_code(403); echo 'Không có quyền'; exit; }

$opts = [
    'search'     => Helper::get('search', ''),
    'lop_hoc_id' => (int)Helper::get('lop_hoc_id', 0),
    'mon_hoc_id' => (int)Helper::get('mon_hoc_id', 0),
    'loai'       => Helper::get('loai_tai_lieu', ''),
    'trang_thai' => ($v = Helper::get('trang_thai', '')) !== '' ? (int)$v : -1,
];
$res = DT_TaiLieu_BUS::getPaged(1, 100000, $opts, (int)Helper::get('da_xoa', 0));
$ttLbl = [1 => 'Hiển thị', 0 => 'Ẩn'];

$headers = ['STT', 'Mã', 'Tiêu đề', 'Loại', 'Định dạng', 'Khóa học', 'Chương trình', 'Bài học', 'Tác giả', 'Công khai', 'Lượt tải', 'Trạng thái'];
$rows = []; $i = 0;
foreach ($res['data'] as $r) {
    $i++;
    $rows[] = [$i, $r['ma_tai_lieu'] ?? '', $r['tieu_de'] ?? '', $r['loai_tai_lieu'] ?? '', $r['dinh_dang'] ?? '',
               $r['ma_khoa_hoc'] ?? '', $r['ma_lop'] ?? '', $r['ten_mon_hoc'] ?? '', $r['tac_gia'] ?? '',
               (int)($r['cong_khai'] ?? 0) === 1 ? 'Có' : '', (int)($r['luot_tai'] ?? 0),
               $ttLbl[(int)($r['trang_thai'] ?? 1)] ?? ''];
}
ExcelHelper::downloadOne('danh-sach-tai-lieu-' . date('Ymd') . '.xlsx', 'Tài liệu', $headers, $rows);
