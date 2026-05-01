<?php
/**
 * download.php - Stream file tài liệu cho user (force download hoặc inline preview).
 * URL: download.php?id=X[&inline=1]
 */
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../BUS/DT_TaiLieu_BUS.php';

Helper::requireLogin();
if (!PhanQuyenHelper::hasQuyen('DT_TaiLieu', PhanQuyenHelper::QUYEN_XEM)) {
    http_response_code(403); echo 'Không có quyền'; exit;
}

$id = (int)($_GET['id'] ?? 0);
$inline = !empty($_GET['inline']);
$tl = DT_TaiLieu_BUS::getById($id);
if (!$tl || !$tl->file_name) { http_response_code(404); echo 'Không tìm thấy'; exit; }

$path = DT_TaiLieu_BUS::uploadDir() . $tl->file_name;
if (!is_file($path)) { http_response_code(404); echo 'File không tồn tại'; exit; }

if (!$inline) DT_TaiLieu_BUS::incDownload($id);

// MIME types
$ext = strtolower(pathinfo($tl->file_name, PATHINFO_EXTENSION));
$mime = [
    'pdf' => 'application/pdf',
    'doc' => 'application/msword',
    'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    'ppt' => 'application/vnd.ms-powerpoint',
    'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
    'xls' => 'application/vnd.ms-excel',
    'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    'txt' => 'text/plain',
    'png' => 'image/png',
    'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg',
    'gif' => 'image/gif',
    'mp4' => 'video/mp4',
    'mp3' => 'audio/mpeg',
    'webm' => 'video/webm',
    'zip' => 'application/zip',
    'rar' => 'application/vnd.rar',
][$ext] ?? 'application/octet-stream';

header('Content-Type: ' . $mime);
header('Content-Length: ' . filesize($path));
$dispositionName = $tl->file_goc ?: $tl->file_name;
$disposition = $inline ? 'inline' : 'attachment';
header('Content-Disposition: ' . $disposition . '; filename="' . rawurlencode($dispositionName) . '"');
header('X-Content-Type-Options: nosniff');
header('Cache-Control: private, max-age=300');
readfile($path);
exit;
