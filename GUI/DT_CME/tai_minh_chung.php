<?php
/**
 * Tải / xem file minh chứng của 1 bản ghi CME.
 * Kiểm tra đăng nhập + quyền XEM; trả file với tên gốc.
 */
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../BUS/DT_Cme_BUS.php';

Helper::requireLogin();
if (!PhanQuyenHelper::hasQuyen('DT_CME', PhanQuyenHelper::QUYEN_XEM)) {
    http_response_code(403); echo 'Không có quyền'; exit;
}

$id = (int)Helper::get('id', 0);
$e = DT_Cme_BUS::ghiNhanGetById($id);
if (!$e || empty($e->minh_chung)) { http_response_code(404); echo 'Không tìm thấy file minh chứng'; exit; }

// Chống path traversal: chỉ lấy basename trong thư mục cme
$path = DT_Cme_BUS::minhChungDir() . basename($e->minh_chung);
if (!is_file($path)) { http_response_code(404); echo 'File không tồn tại trên server'; exit; }

$ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
$mime = ['pdf' => 'application/pdf', 'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png'][$ext] ?? 'application/octet-stream';

$tenGoc = $e->minh_chung_goc ?: basename($e->minh_chung);
// inline để xem trực tiếp PDF/ảnh trên trình duyệt; ?tai=1 để tải về
$disp = ((int)Helper::get('tai', 0) === 1) ? 'attachment' : 'inline';

header('Content-Type: ' . $mime);
header('Content-Disposition: ' . $disp . '; filename="' . preg_replace('/[^\w.\-]+/u', '_', $tenGoc) . '"');
header('Content-Length: ' . filesize($path));
header('X-Content-Type-Options: nosniff');
readfile($path);
