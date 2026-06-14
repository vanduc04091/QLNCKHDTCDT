<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../BUS/DT_DiemDanh_BUS.php';
require_once __DIR__ . '/../../BUS/DT_KhoaHocChuongTrinh_BUS.php';
require_once __DIR__ . '/../../BUS/DT_KhoaHoc_BUS.php';

Helper::requireAjaxCsrf();

$action = Helper::post('action', '');
$u = SessionHelper::userId();
$MODULE = DT_DiemDanh_BUS::MODULE_KEY;

try {
    switch ($action) {
        case 'getComboLop':
            ResponseHelper::success('OK', DT_KhoaHocChuongTrinh_BUS::getCombo());
            break;

        case 'getComboKhoaHoc':
            ResponseHelper::success('OK', DT_KhoaHoc_BUS::getCombo());
            break;

        case 'getChuongTrinhTheoKhoa':
            ResponseHelper::success('OK', DT_KhoaHocChuongTrinh_BUS::getByKhoaHoc(Helper::postInt('khoa_hoc_id')));
            break;

        case 'lichByLop':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XEM);
            $data = DT_DiemDanh_BUS::lichByLop(
                Helper::postInt('lop_hoc_id'),
                Helper::postStr('from'),
                Helper::postStr('to')
            );
            ResponseHelper::success('OK', $data);
            break;

        case 'openSession':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XEM);
            $res = DT_DiemDanh_BUS::openSession(Helper::postInt('lich_hoc_id'), $u);
            $res['success'] ? ResponseHelper::success($res['message'], $res['data']) : ResponseHelper::error($res['message']);
            break;

        case 'list':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XEM);
            $data = DT_DiemDanh_BUS::listByLich(Helper::postInt('lich_hoc_id'), Helper::postStr('search'));
            ResponseHelper::success('OK', $data);
            break;

        case 'saveBulk':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_SUA);
            $items = isset($_POST['items']) && is_array($_POST['items']) ? $_POST['items'] : [];
            $res = DT_DiemDanh_BUS::saveBulk(Helper::postInt('lich_hoc_id'), $items, $u);
            $res['success'] ? ResponseHelper::success($res['message'], $res['data'] ?? null) : ResponseHelper::error($res['message']);
            break;

        case 'markAllPresent':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_SUA);
            $res = DT_DiemDanh_BUS::markAllPresent(Helper::postInt('lich_hoc_id'), $u);
            $res['success'] ? ResponseHelper::success($res['message']) : ResponseHelper::error($res['message']);
            break;

        case 'historyByHvl':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XEM);
            ResponseHelper::success('OK', DT_DiemDanh_BUS::historyByHvl(Helper::postInt('hvl_id')));
            break;

        default:
            ResponseHelper::error('Action không hợp lệ');
    }
} catch (Throwable $ex) {
    ResponseHelper::error(AppConfig::APP_DEBUG ? $ex->getMessage() : 'Lỗi hệ thống', 500);
}
