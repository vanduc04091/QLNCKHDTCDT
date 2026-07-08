<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../BUS/DT_PhieuIn_BUS.php';

Helper::requireLogin();
if (!PhanQuyenHelper::hasQuyen('DM_HocVien', PhanQuyenHelper::QUYEN_XEM)) {
    http_response_code(403); echo 'Không có quyền'; exit;
}

$mau  = Helper::get('mau', '');
$hvId = (int)Helper::get('hoc_vien_id', 0);
$khct = (int)Helper::get('khct_id', 0);
$auto = (int)Helper::get('auto', 1); // 1 = tự mở hộp thoại in

$res = DT_PhieuIn_BUS::render($mau, $hvId, $khct);
if (!$res['success']) {
    http_response_code(404);
    echo '<meta charset="utf-8"><p style="font-family:sans-serif">' . Helper::h($res['message']) . '</p>';
    exit;
}

$html = $res['html'];

// Chèn script tự in (chỉ khi auto=1) trước </body>
if ($auto) {
    $script = "<script>window.addEventListener('load',function(){setTimeout(function(){window.print();},300);});</script>";
    $pos = stripos($html, '</body>');
    $html = $pos !== false ? substr($html, 0, $pos) . $script . substr($html, $pos) : $html . $script;
}

header('Content-Type: text/html; charset=utf-8');
echo $html;
