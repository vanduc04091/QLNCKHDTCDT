<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../BUS/DT_BaoCao_BUS.php';
require_once __DIR__ . '/../../BUS/DT_KhoaHocChuongTrinh_BUS.php';

Helper::requireAjaxCsrf();
$action = Helper::post('action', '');
$MODULE = DT_BaoCao_BUS::MODULE_KEY;

try {
    switch ($action) {
        case 'theoKhoa':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XEM);
            ResponseHelper::success('OK', DT_BaoCao_BUS::theoKhoaCtdt(
                Helper::postInt('khoa_hoc_id', 0), Helper::postStr('from'), Helper::postStr('to')));
            break;

        case 'ctTheoKhoa':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XEM);
            ResponseHelper::success('OK', DT_KhoaHocChuongTrinh_BUS::getByKhoaHoc(Helper::postInt('khoa_hoc_id')));
            break;

        case 'dsHv':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XEM);
            ResponseHelper::success('OK', DT_BaoCao_BUS::dsHocVienKetQua(
                Helper::postInt('khct_id'), Helper::postStr('from'), Helper::postStr('to')));
            break;

        case 'thongKe':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XEM);
            ResponseHelper::success('OK', DT_BaoCao_BUS::thongKeTong(Helper::postStr('from'), Helper::postStr('to')));
            break;

        default:
            ResponseHelper::error('Action không hợp lệ');
    }
} catch (Throwable $ex) {
    ResponseHelper::error(AppConfig::APP_DEBUG ? $ex->getMessage() : 'Lỗi hệ thống', 500);
}
