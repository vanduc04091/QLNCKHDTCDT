<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../BUS/DT_ChuongTrinhMonHoc_BUS.php';

Helper::requireAjaxCsrf();

$action = Helper::post('action', '');
$u = SessionHelper::userId();
$MODULE = DT_ChuongTrinhMonHoc_BUS::MODULE_KEY;

try {
    switch ($action) {
        case 'list':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XEM);
            $ctId = Helper::postInt('chuong_trinh_id');
            $data = DT_ChuongTrinhMonHoc_BUS::listByChuongTrinh($ctId);
            ResponseHelper::success('OK', $data);
            break;

        case 'add':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_THEM);
            $res = DT_ChuongTrinhMonHoc_BUS::addMonHoc(
                Helper::postInt('chuong_trinh_id'),
                Helper::postInt('mon_hoc_id'),
                Helper::postInt('bat_buoc', 1),
                $u
            );
            $res['success'] ? ResponseHelper::success($res['message'], $res['data'] ?? null) : ResponseHelper::error($res['message']);
            break;

        case 'toggleBatBuoc':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_SUA);
            $res = DT_ChuongTrinhMonHoc_BUS::toggleBatBuoc(Helper::postInt('id'), Helper::postInt('bat_buoc'), $u);
            $res['success'] ? ResponseHelper::success($res['message']) : ResponseHelper::error($res['message']);
            break;

        case 'move':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_SUA);
            $res = DT_ChuongTrinhMonHoc_BUS::move(Helper::postInt('id'), Helper::postStr('dir'), $u);
            $res['success'] ? ResponseHelper::success($res['message']) : ResponseHelper::error($res['message']);
            break;

        case 'remove':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XOA);
            $res = DT_ChuongTrinhMonHoc_BUS::remove(Helper::postInt('id'), $u);
            $res['success'] ? ResponseHelper::success($res['message']) : ResponseHelper::error($res['message']);
            break;

        default:
            ResponseHelper::error('Action không hợp lệ');
    }
} catch (Throwable $ex) {
    ResponseHelper::error(AppConfig::APP_DEBUG ? $ex->getMessage() : 'Lỗi hệ thống', 500);
}
