<?php
/**
 * download.php - Stream file chứng chỉ (force download hoặc inline preview).
 * URL: download.php?id=X[&inline=1]
 */
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../BUS/DT_ChungChi_BUS.php';

Helper::requireLogin();
if (!PhanQuyenHelper::hasQuyen('DT_ChungChi', PhanQuyenHelper::QUYEN_XEM)) {
    http_response_code(403); echo 'Không có quyền'; exit;
}

$id = (int)($_GET['id'] ?? 0);
$inline = !empty($_GET['inline']);
$cc = DT_ChungChi_BUS::getById($id);
if (!$cc || !$cc->duong_dan_file) { http_response_code(404); echo 'Không tìm thấy file'; exit; }

$path = DT_ChungChi_BUS::uploadDir() . $cc->duong_dan_file;
if (!is_file($path)) { http_response_code(404); echo 'File không tồn tại'; exit; }

$ext = strtolower(pathinfo($cc->duong_dan_file, PATHINFO_EXTENSION));
$mime = [
    'pdf'  => 'application/pdf',
    'png'  => 'image/png',
    'jpg'  => 'image/jpeg', 'jpeg' => 'image/jpeg',
][$ext] ?? 'application/octet-stream';

header('Content-Type: ' . $mime);
header('Content-Length: ' . filesize($path));
$dispositionName = ($cc->so_chung_chi ? $cc->so_chung_chi . '.' . $ext : $cc->duong_dan_file);
$disposition = $inline ? 'inline' : 'attachment';
header('Content-Disposition: ' . $disposition . '; filename="' . rawurlencode($dispositionName) . '"');
header('X-Content-Type-Options: nosniff');
header('Cache-Control: private, max-age=300');
readfile($path);
exit;
