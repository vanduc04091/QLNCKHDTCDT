<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../BUS/DM_NhanVien_BUS.php';

Helper::requireLogin();
if (!PhanQuyenHelper::hasQuyen('DM_NhanVien', PhanQuyenHelper::QUYEN_XEM)) { http_response_code(403); echo 'Không có quyền'; exit; }

$search = Helper::get('search', '');
$daXoa  = (int)Helper::get('da_xoa', 0);
$kp     = (int)Helper::get('khoa_phong_id', 0);

// Lấy TOÀN BỘ (lặp từng trang 500 — vì phân trang kẹp pageSize ở 500)
$data = ExportHelper::fetchAll(fn($page, $size) => DM_NhanVien_BUS::getPaged($page, $size, $search, $daXoa, $kp));
$fmt = fn($d) => !empty($d) ? date('d/m/Y', strtotime($d)) : '';

// Cấu trúc trùng file import "Danh sách người hành nghề toàn viện" (13 cột)
// + bổ sung các cột quản lý khác ở cuối để xem đầy đủ.
$headers = ['TT', 'MNV', 'Họ và tên', 'K/P/TT', 'Văn bằng chuyên môn', 'Ngày tháng năm sinh',
            'Phạm vi hành nghề', 'Số CCHN', 'Ngày cấp chứng chỉ hành nghề',
            'Quyết định bổ sung phạm vi', 'Điều chỉnh phạm vi HĐCM trong CCHN',
            'Ngày điều chỉnh phạm vi', 'Chuyên khoa cần cập nhật kiến thức y khoa liên tục',
            'Giới tính', 'Chức danh', 'Điện thoại', 'Email', 'Địa chỉ', 'Trạng thái'];

$gt = ['M' => 'Nam', 'F' => 'Nữ'];
$tt = [1 => 'Đang làm', 0 => 'Nghỉ việc'];

$rows = [];
$i = 0;
foreach ($data as $r) {
    $i++;
    // Khoa/phòng: ưu tiên tên khoa đã map, nếu chưa map thì lấy tên gốc từ file import
    $khoa = $r['ten_khoa_phong'] ?? '';
    if ($khoa === '' && !empty($r['khoa_phong_text'])) $khoa = $r['khoa_phong_text'];

    $rows[] = [
        $i,
        $r['ma_nv'] ?? '',
        $r['ho_ten'] ?? '',
        $khoa,
        $r['trinh_do'] ?? '',                    // Văn bằng chuyên môn
        $fmt($r['ngay_sinh'] ?? ''),
        $r['pham_vi_hanh_nghe'] ?? '',
        $r['so_cchn'] ?? '',
        $fmt($r['ngay_cap_cchn'] ?? ''),
        $r['qd_bo_sung_pham_vi'] ?? '',
        $r['dieu_chinh_pham_vi'] ?? '',
        $fmt($r['ngay_dieu_chinh'] ?? ''),
        $r['chuyen_khoa_cap_nhat'] ?? '',
        // Cột quản lý bổ sung
        $gt[$r['gioi_tinh'] ?? ''] ?? ($r['gioi_tinh'] ?? ''),
        $r['chuc_danh'] ?? '',
        $r['dien_thoai'] ?? '',
        $r['email'] ?? '',
        $r['dia_chi'] ?? '',
        $tt[(int)($r['trang_thai'] ?? 1)] ?? '',
    ];
}

ExcelHelper::download('danh-sach-nguoi-hanh-nghe-' . date('Ymd') . '.xlsx', [[
    'name'    => 'Người hành nghề',
    'title'   => 'DANH SÁCH NGƯỜI HÀNH NGHỀ TOÀN BỆNH VIỆN',
    'headers' => $headers,
    'rows'    => $rows,
]]);
