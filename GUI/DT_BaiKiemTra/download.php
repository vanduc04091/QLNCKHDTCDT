<?php
/**
 * download.php - Stream file đề / đáp án.
 * URL: download.php?id=X&kind=de|dap_an
 */
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../BUS/DT_BaiKiemTra_BUS.php';

Helper::requireLogin();
if (!PhanQuyenHelper::hasQuyen('DT_BaiKiemTra', PhanQuyenHelper::QUYEN_XEM)) {
    http_response_code(403); echo 'Không có quyền'; exit;
}

$id = (int)($_GET['id'] ?? 0);
$kind = $_GET['kind'] ?? 'de';
if (!in_array($kind, ['de','dap_an'], true)) { http_response_code(400); echo 'Tham số không hợp lệ'; exit; }

$bkt = DT_BaiKiemTra_BUS::getById($id);
if (!$bkt) { http_response_code(404); echo 'Không tìm thấy'; exit; }

$fname = $kind === 'de' ? $bkt->de_file_name : $bkt->dap_an_file_name;
$forig = $kind === 'de' ? $bkt->de_file_goc : $bkt->dap_an_file_goc;
if (!$fname) { http_response_code(404); echo 'Chưa có file'; exit; }

// Quyền xem đáp án: nếu cong_khai_dap_an=0 thì chỉ admin (group 1) hoặc người có quyền SUA
if ($kind === 'dap_an' && (int)$bkt->cong_khai_dap_an === 0) {
    $isAdmin = SessionHelper::nhomTaiKhoanId() === 1;
    if (!$isAdmin && !PhanQuyenHelper::hasQuyen('DT_BaiKiemTra', PhanQuyenHelper::QUYEN_SUA)) {
        http_response_code(403); echo 'Đáp án chưa công khai'; exit;
    }
}

$path = DT_BaiKiemTra_BUS::uploadDir() . $fname;
if (!is_file($path)) { http_response_code(404); echo 'File không tồn tại'; exit; }

$ext = strtolower(pathinfo($fname, PATHINFO_EXTENSION));
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
    'zip' => 'application/zip',
    'rar' => 'application/vnd.rar',
][$ext] ?? 'application/octet-stream';

header('Content-Type: ' . $mime);
header('Content-Length: ' . filesize($path));
header('Content-Disposition: attachment; filename="' . rawurlencode($forig ?: $fname) . '"');
header('X-Content-Type-Options: nosniff');
header('Cache-Control: private, max-age=0');
readfile($path);
exit;
