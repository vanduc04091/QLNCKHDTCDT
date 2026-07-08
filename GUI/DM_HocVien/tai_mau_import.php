<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../BUS/DM_HocVien_BUS.php';

Helper::requireLogin();
if (!PhanQuyenHelper::hasQuyen('DM_HocVien', PhanQuyenHelper::QUYEN_THEM)) {
    http_response_code(403); echo 'Không có quyền'; exit;
}

$headers = [
    'TT', 'Họ và tên (*)', 'Trạng thái (*)', 'Ngày sinh (*)', 'Giới tính (*)',
    'Trình độ chuyên môn', 'Đối tượng học viên (*)', 'Điện thoại (*)', 'Email',
    'Căn cước công dân (*)', 'Ngày cấp (*)', 'Nơi cấp (*)', 'Đơn vị công tác',
    'Địa chỉ thường trú (*)', 'Trường đào tạo', 'Năm tốt nghiệp',
    'Khóa học (*)', 'Chương trình đào tạo (*)', 'Ngày bắt đầu học (*)',
    'Ngày kết thúc (*)', 'Địa điểm học (*)',
];

// 1 dòng ví dụ minh họa định dạng
$example = [
    '1', 'Nguyễn Văn A', 'Hoạt động', '23/07/1991', 'Nam',
    'Bác sĩ y khoa', 'Bác sĩ', '0900000000', '',
    '040091000000', '10/08/2021', 'Bộ Công an', '',
    'Số 1, phường X, tỉnh Nghệ An', 'Trường ĐH Y khoa Vinh', '2026',
    'MÃ_KHÓA - Tên khóa', 'MÃ_CTĐT - Tên chương trình', '06/07/2026',
    '06/07/2027', 'Tại các khoa...',
];

ExcelHelper::download('mau-danh-sach-hoc-vien.xlsx', [[
    'name'    => 'Danh sách HV',
    'title'   => 'DANH SÁCH NHẬP THÔNG TIN HỌC VIÊN',
    'headers' => $headers,
    'rows'    => [$example],
]]);
