<?php
/**
 * download.php - Stream file đính kèm của đơn đăng ký (CCCD scan / bằng cấp).
 * URL: download.php?id=X&kind=cccd|bc[&inline=1]
 */
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../BUS/DT_DangKyKhoaHoc_BUS.php';

Helper::requireLogin();
if (!PhanQuyenHelper::hasQuyen('DT_DangKyKhoaHoc', PhanQuyenHelper::QUYEN_XEM)) {
    http_response_code(403); echo 'Không có quyền'; exit;
}

$id     = (int)($_GET['id'] ?? 0);
$kind   = $_GET['kind'] ?? '';
$inline = !empty($_GET['inline']);
$dk = DT_DangKyKhoaHoc_BUS::getById($id);
if (!$dk) { http_response_code(404); echo 'Không tìm thấy'; exit; }

$file = $kind === 'cccd' ? $dk->cccd_file : ($kind === 'bc' ? $dk->bang_cap_file : null);
if (!$file) { http_response_code(404); echo 'Không có file'; exit; }

$path = DT_DangKyKhoaHoc_BUS::uploadDir() . $file;
if (!is_file($path)) { http_response_code(404); echo 'File không tồn tại trên server'; exit; }

$ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
$mime = ['pdf'=>'application/pdf','png'=>'image/png','jpg'=>'image/jpeg','jpeg'=>'image/jpeg'][$ext] ?? 'application/octet-stream';

header('Content-Type: ' . $mime);
header('Content-Length: ' . filesize($path));
header('Content-Disposition: ' . ($inline ? 'inline' : 'attachment') . '; filename="' . rawurlencode($file) . '"');
header('X-Content-Type-Options: nosniff');
header('Cache-Control: private, max-age=300');
readfile($path);
exit;
