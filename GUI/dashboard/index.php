<?php
/**
 * dashboard/index.php - Trang đích sau đăng nhập.
 * Redirect tự động sang trang phù hợp với quyền của user, không hiển thị home cầu kỳ.
 */
require_once __DIR__ . '/../../bootstrap.php';
Helper::requireLogin();

// Thứ tự ưu tiên redirect:
//   1. Tổng quan NCKH (admin / cán bộ phòng KH)
//   2. Tổng quan đào tạo
//   3. Đề tài của tôi (nhân viên thường)
//   4. Danh sách đề tài
//   5. Fallback: trang đăng nhập (không nên xảy ra nếu requireLogin OK)
$candidates = [
    ['NCKH_Dashboard',  'GUI/dashboard/nckh.php'],
    ['Dashboard',       'GUI/dashboard/dao_tao.php'],
    ['NCKH_DeTaiCuaToi','GUI/NCKH_DeTaiCuaToi/index.php'],
    ['NCKH_DeTai',      'GUI/NCKH_DeTai/index.php'],
];

$target = 'GUI/NCKH_DeTaiCuaToi/index.php'; // fallback an toàn (đã cấp cho mọi nhóm)
foreach ($candidates as [$module, $url]) {
    if (PhanQuyenHelper::hasQuyen($module, PhanQuyenHelper::QUYEN_XEM)) {
        $target = $url;
        break;
    }
}

header('Location: ' . AppConfig::baseUrl($target));
exit;
