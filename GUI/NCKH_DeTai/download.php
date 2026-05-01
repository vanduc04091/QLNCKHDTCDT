<?php
/**
 * download.php - Stream file tài liệu NCKH cho user (force download).
 * URL: download.php?id=X[&inline=1]
 */
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../BUS/NCKH_TaiLieu_BUS.php';

Helper::requireLogin();
if (!PhanQuyenHelper::hasQuyen('NCKH_DeTai', PhanQuyenHelper::QUYEN_XEM)) {
    http_response_code(403); echo 'Không có quyền'; exit;
}

$id = (int)($_GET['id'] ?? 0);
$inline = !empty($_GET['inline']);
$tl = NCKH_TaiLieu_BUS::getById($id);
if (!$tl || !$tl->ten_file_luu) { http_response_code(404); echo 'Không tìm thấy'; exit; }

$path = __DIR__ . '/../../assets/uploads/nckh/' . $tl->ten_file_luu;
if (!is_file($path)) { http_response_code(404); echo 'File không tồn tại'; exit; }

$ext = strtolower(pathinfo($tl->ten_file_luu, PATHINFO_EXTENSION));
$mime = [
    'pdf' => 'application/pdf',
    'doc' => 'application/msword',
    'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    'ppt' => 'application/vnd.ms-powerpoint',
    'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
    'xls' => 'application/vnd.ms-excel',
    'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    'png' => 'image/png',
    'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg',
    'zip' => 'application/zip',
    'rar' => 'application/vnd.rar',
    '7z'  => 'application/x-7z-compressed',
][$ext] ?? 'application/octet-stream';

header('Content-Type: ' . $mime);
header('Content-Length: ' . filesize($path));
$dispositionName = $tl->ten_file_goc ?: $tl->ten_file_luu;
$disposition = $inline ? 'inline' : 'attachment';
header('Content-Disposition: ' . $disposition . '; filename="' . rawurlencode($dispositionName) . '"');
header('X-Content-Type-Options: nosniff');
header('Cache-Control: private, max-age=300');
readfile($path);
exit;
