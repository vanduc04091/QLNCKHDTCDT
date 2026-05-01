<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../BUS/DM_CauHinh_BUS.php';

Helper::requireAjaxCsrf();

$action = Helper::post('action', '');
$u      = SessionHelper::userId();
$MODULE = DM_CauHinh_BUS::MODULE_KEY;

try {
    switch ($action) {
        case 'getAll':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XEM);
            ResponseHelper::success('OK', DM_CauHinh_BUS::getAllForUi());
            break;

        case 'save':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_SUA);
            $res = DM_CauHinh_BUS::save($_POST, $u);
            $res['success'] ? ResponseHelper::success($res['message']) : ResponseHelper::error($res['message']);
            break;

        case 'testMail':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_SUA);
            $email = Helper::postStr('email');
            $res = DM_CauHinh_BUS::testMail($email);
            $res['success'] ? ResponseHelper::success($res['message']) : ResponseHelper::error($res['message']);
            break;

        default:
            ResponseHelper::error('Action không hợp lệ');
    }
} catch (Throwable $ex) {
    ResponseHelper::error(AppConfig::APP_DEBUG ? $ex->getMessage() : 'Lỗi hệ thống', 500);
}
