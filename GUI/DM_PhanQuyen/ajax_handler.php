<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../BUS/DM_PhanQuyen_BUS.php';
require_once __DIR__ . '/../../BUS/DM_NhomTaiKhoan_BUS.php';
require_once __DIR__ . '/../../BUS/DM_DanhSachForm_BUS.php';

Helper::requireAjaxCsrf();

$action = Helper::post('action', '');
$u = SessionHelper::userId();
$MODULE = DM_PhanQuyen_BUS::MODULE_KEY;

try {
    switch ($action) {
        case 'getMatrix':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XEM);
            $nhomId = Helper::postInt('nhom_tai_khoan_id');
            $forms = DM_DanhSachForm_BUS::getAll(0);
            $matrix = DM_PhanQuyen_BUS::getMatrixByNhom($nhomId);
            $map = [];
            foreach ($matrix as $m) {
                $map[(int)$m['form_id']] = [
                    'xem' => (int)$m['quyen_xem'],
                    'them' => (int)$m['quyen_them'],
                    'sua' => (int)$m['quyen_sua'],
                    'xoa' => (int)$m['quyen_xoa'],
                ];
            }
            ResponseHelper::success('OK', ['forms' => $forms, 'permissions' => $map]);
            break;

        case 'save':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_SUA);
            $nhomId = Helper::postInt('nhom_tai_khoan_id');
            $perms = $_POST['permissions'] ?? [];
            if (!is_array($perms)) $perms = [];
            $res = DM_PhanQuyen_BUS::saveMatrix($nhomId, $perms, $u);
            $res['success'] ? ResponseHelper::success($res['message']) : ResponseHelper::error($res['message']);
            break;

        case 'grantAll':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_SUA);
            $nhomId = Helper::postInt('nhom_tai_khoan_id');
            $res = DM_PhanQuyen_BUS::grantAllToNhom($nhomId, $u);
            $res['success'] ? ResponseHelper::success($res['message']) : ResponseHelper::error($res['message']);
            break;

        default:
            ResponseHelper::error('Action không hợp lệ');
    }
} catch (Throwable $ex) {
    ResponseHelper::error(AppConfig::APP_DEBUG ? $ex->getMessage() : 'Lỗi hệ thống', 500);
}
