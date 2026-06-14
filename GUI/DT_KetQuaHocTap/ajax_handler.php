<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../BUS/DT_KetQuaHocTap_BUS.php';
require_once __DIR__ . '/../../BUS/DT_KhoaHocChuongTrinh_BUS.php';

Helper::requireAjaxCsrf();

$action = Helper::post('action', '');
$u = SessionHelper::userId();
$MODULE = DT_KetQuaHocTap_BUS::MODULE_KEY;

try {
    switch ($action) {
        case 'getComboLop':
            ResponseHelper::success('OK', DT_KhoaHocChuongTrinh_BUS::getCombo());
            break;

        case 'load':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XEM);
            $lopId = Helper::postInt('lop_hoc_id');
            ResponseHelper::success('OK', [
                // Điểm tính theo CTĐT/khóa của học viên (mỗi HV 1 điểm tổng), không tách theo bài học
                'mon_hoc' => [],
                'rows' => DT_KetQuaHocTap_BUS::getByLop($lopId),
                'stats' => DT_KetQuaHocTap_BUS::statsByLop($lopId),
            ]);
            break;

        case 'saveOne':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_SUA);
            $input = [
                'hoc_vien_lop_id' => Helper::postInt('hoc_vien_lop_id'),
                'mon_hoc_id' => Helper::postInt('mon_hoc_id') ?: null,
                'diem_thuong_xuyen' => $_POST['diem_thuong_xuyen'] ?? null,
                'diem_giua_ky' => $_POST['diem_giua_ky'] ?? null,
                'diem_cuoi_ky' => $_POST['diem_cuoi_ky'] ?? null,
                'nhan_xet' => Helper::postStr('nhan_xet'),
            ];
            $res = DT_KetQuaHocTap_BUS::saveOne($input, $u);
            $res['success'] ? ResponseHelper::success($res['message'], $res['data'] ?? null) : ResponseHelper::error($res['message']);
            break;

        case 'saveBulk':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_SUA);
            $rows = isset($_POST['rows']) && is_array($_POST['rows']) ? $_POST['rows'] : [];
            $res = DT_KetQuaHocTap_BUS::saveBulk($rows, $u);
            $res['success'] ? ResponseHelper::success($res['message']) : ResponseHelper::error($res['message']);
            break;

        case 'recalc':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_SUA);
            $res = DT_KetQuaHocTap_BUS::recalc(Helper::postInt('lop_hoc_id'), $u);
            $res['success'] ? ResponseHelper::success($res['message']) : ResponseHelper::error($res['message']);
            break;

        case 'delete':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XOA);
            $res = DT_KetQuaHocTap_BUS::delete(Helper::postInt('id'));
            $res['success'] ? ResponseHelper::success($res['message']) : ResponseHelper::error($res['message']);
            break;

        default:
            ResponseHelper::error('Action không hợp lệ');
    }
} catch (Throwable $ex) {
    ResponseHelper::error(AppConfig::APP_DEBUG ? $ex->getMessage() : 'Lỗi hệ thống', 500);
}
