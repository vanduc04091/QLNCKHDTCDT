<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../BUS/DT_HoSoHocVien_BUS.php';
require_once __DIR__ . '/../../BUS/DM_HocVien_BUS.php';

Helper::requireAjaxCsrf();

$action = Helper::post('action', '');
$u      = SessionHelper::userId();
$MODULE = DT_HoSoHocVien_BUS::MODULE_KEY;

try {
    switch ($action) {
        case 'getPaged':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XEM);
            $opts = [
                'search'      => Helper::postStr('search'),
                'loai_ho_so'  => Helper::postStr('loai_ho_so'),
                'hoc_vien_id' => Helper::postInt('hoc_vien_id'),
                'trang_thai'  => Helper::postStr('trang_thai'),
            ];
            $res = DT_HoSoHocVien_BUS::getPaged(
                Helper::postInt('page', 1),
                Helper::postInt('pageSize', AppConfig::DEFAULT_PAGE_SIZE),
                $opts,
                Helper::postInt('da_xoa', 0)
            );
            ResponseHelper::paged(
                $res['data'],
                Helper::postInt('page', 1),
                Helper::postInt('pageSize', AppConfig::DEFAULT_PAGE_SIZE),
                $res['totalRecords']
            );
            break;

        case 'getById':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XEM);
            $e = DT_HoSoHocVien_BUS::getById(Helper::postInt('id'));
            if (!$e) ResponseHelper::error('Không tìm thấy hồ sơ');
            ResponseHelper::success('OK', $e);
            break;

        case 'getByHocVien':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XEM);
            $rows = DT_HoSoHocVien_BUS::getByHocVien(Helper::postInt('hoc_vien_id'));
            ResponseHelper::success('OK', $rows);
            break;

        case 'getStats':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XEM);
            ResponseHelper::success('OK', DT_HoSoHocVien_BUS::getStats());
            break;

        case 'getComboHocVien':
            ResponseHelper::success('OK', DM_HocVien_BUS::getCombo());
            break;

        case 'getComboLoai':
            ResponseHelper::success('OK', DT_HoSoHocVien_BUS::getComboLoai());
            break;

        case 'insert':
        case 'update':
            $isUpdate = ($action === 'update');
            PhanQuyenHelper::requireQuyen($MODULE, $isUpdate ? PhanQuyenHelper::QUYEN_SUA : PhanQuyenHelper::QUYEN_THEM);

            $e = new DT_HoSoHocVien_PUBLIC();
            if ($isUpdate) $e->id = Helper::postInt('id');
            $e->hoc_vien_id  = Helper::postInt('hoc_vien_id');
            $e->loai_ho_so   = Helper::postStr('loai_ho_so');
            $e->ten_ho_so    = Helper::postStr('ten_ho_so');
            $e->so_hieu      = Helper::postStr('so_hieu') ?: null;
            $e->ngay_cap     = Helper::postStr('ngay_cap') ?: null;
            $e->noi_cap      = Helper::postStr('noi_cap') ?: null;
            $e->ngay_het_han = Helper::postStr('ngay_het_han') ?: null;
            $e->ghi_chu      = Helper::postStr('ghi_chu') ?: null;
            $e->trang_thai   = Helper::postInt('trang_thai', 1);

            $file = $_FILES['ho_so_file'] ?? null;

            if ($isUpdate) {
                $e->nguoi_cap_nhat = $u;
                $res = DT_HoSoHocVien_BUS::update($e, $file);
            } else {
                $e->nguoi_tao = $u;
                $res = DT_HoSoHocVien_BUS::insert($e, $file);
            }
            $res['success']
                ? ResponseHelper::success($res['message'], $res['data'] ?? null)
                : ResponseHelper::error($res['message']);
            break;

        case 'trash':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XOA);
            $res = DT_HoSoHocVien_BUS::trash(Helper::postInt('id'), $u);
            $res['success'] ? ResponseHelper::success($res['message']) : ResponseHelper::error($res['message']);
            break;

        case 'restore':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_SUA);
            $res = DT_HoSoHocVien_BUS::restore(Helper::postInt('id'), $u);
            $res['success'] ? ResponseHelper::success($res['message']) : ResponseHelper::error($res['message']);
            break;

        case 'delete':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XOA);
            $res = DT_HoSoHocVien_BUS::delete(Helper::postInt('id'));
            $res['success'] ? ResponseHelper::success($res['message']) : ResponseHelper::error($res['message']);
            break;

        default:
            ResponseHelper::error('Action không hợp lệ');
    }
} catch (Throwable $ex) {
    ResponseHelper::error(AppConfig::APP_DEBUG ? $ex->getMessage() : 'Lỗi hệ thống', 500);
}
