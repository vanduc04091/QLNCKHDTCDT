<?php
/**
 * download.php - Stream file hồ sơ học viên (force download hoặc inline preview).
 * URL: download.php?id=X[&inline=1]
 */
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../BUS/DT_HoSoHocVien_BUS.php';

Helper::requireLogin();
if (!PhanQuyenHelper::hasQuyen('DT_HoSoHocVien', PhanQuyenHelper::QUYEN_XEM)) {
    http_response_code(403); echo 'Không có quyền'; exit;
}

$id = (int)($_GET['id'] ?? 0);
$inline = !empty($_GET['inline']);
$hs = DT_HoSoHocVien_BUS::getById($id);
if (!$hs || !$hs->duong_dan) { http_response_code(404); echo 'Không tìm thấy file'; exit; }

$path = DT_HoSoHocVien_BUS::uploadDir() . $hs->duong_dan;
if (!is_file($path)) { http_response_code(404); echo 'File không tồn tại'; exit; }

$ext = strtolower(pathinfo($hs->duong_dan, PATHINFO_EXTENSION));
$mime = [
    'pdf'  => 'application/pdf',
    'doc'  => 'application/msword',
    'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    'png'  => 'image/png',
    'jpg'  => 'image/jpeg', 'jpeg' => 'image/jpeg',
    'gif'  => 'image/gif',
    'zip'  => 'application/zip',
    'rar'  => 'application/vnd.rar',
][$ext] ?? 'application/octet-stream';

header('Content-Type: ' . $mime);
header('Content-Length: ' . filesize($path));
$disposition = $inline ? 'inline' : 'attachment';
header('Content-Disposition: ' . $disposition . '; filename="' . rawurlencode($hs->duong_dan) . '"');
header('X-Content-Type-Options: nosniff');
header('Cache-Control: private, max-age=300');
readfile($path);
exit;
