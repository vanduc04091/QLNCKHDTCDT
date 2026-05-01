<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../BUS/DM_LoaiHinhDaoTao_BUS.php';

Helper::requireAjaxCsrf();

$action = Helper::post('action', '');
$u = SessionHelper::userId();
$MODULE = DM_LoaiHinhDaoTao_BUS::MODULE_KEY;

try {
    switch ($action) {
        case 'getPaged':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XEM);
            $page = Helper::postInt('page', 1);
            $size = Helper::postInt('pageSize', AppConfig::DEFAULT_PAGE_SIZE);
            $search = Helper::postStr('search');
            $daXoa = Helper::postInt('da_xoa', 0);
            $res = DM_LoaiHinhDaoTao_BUS::getPaged($page, $size, $search, $daXoa);
            ResponseHelper::paged($res['data'], $page, $size, $res['totalRecords']);
            break;

        case 'getById':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XEM);
            $e = DM_LoaiHinhDaoTao_BUS::getById(Helper::postInt('id'));
            if (!$e) ResponseHelper::error('Không tìm thấy');
            ResponseHelper::success('OK', $e);
            break;

        case 'insert':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_THEM);
            $e = new DM_LoaiHinhDaoTao_PUBLIC();
            $e->ma_loai_hinh = Helper::postStr('ma_loai_hinh');
            $e->ten_loai_hinh = Helper::postStr('ten_loai_hinh');
            $e->mo_ta = Helper::postStr('mo_ta');
            $e->thu_tu = Helper::postInt('thu_tu', 0);
            $e->trang_thai = Helper::postInt('trang_thai', 1);
            $e->nguoi_tao = $u;
            $res = DM_LoaiHinhDaoTao_BUS::insert($e);
            $res['success'] ? ResponseHelper::success($res['message'], $res['data'] ?? null) : ResponseHelper::error($res['message']);
            break;

        case 'update':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_SUA);
            $e = new DM_LoaiHinhDaoTao_PUBLIC();
            $e->id = Helper::postInt('id');
            $e->ma_loai_hinh = Helper::postStr('ma_loai_hinh');
            $e->ten_loai_hinh = Helper::postStr('ten_loai_hinh');
            $e->mo_ta = Helper::postStr('mo_ta');
            $e->thu_tu = Helper::postInt('thu_tu', 0);
            $e->trang_thai = Helper::postInt('trang_thai', 1);
            $e->nguoi_cap_nhat = $u;
            $res = DM_LoaiHinhDaoTao_BUS::update($e);
            $res['success'] ? ResponseHelper::success($res['message']) : ResponseHelper::error($res['message']);
            break;

        case 'trash':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XOA);
            $res = DM_LoaiHinhDaoTao_BUS::trash(Helper::postInt('id'), $u);
            $res['success'] ? ResponseHelper::success($res['message']) : ResponseHelper::error($res['message']);
            break;

        case 'restore':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_SUA);
            $res = DM_LoaiHinhDaoTao_BUS::restore(Helper::postInt('id'), $u);
            $res['success'] ? ResponseHelper::success($res['message']) : ResponseHelper::error($res['message']);
            break;

        case 'delete':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XOA);
            $res = DM_LoaiHinhDaoTao_BUS::delete(Helper::postInt('id'));
            $res['success'] ? ResponseHelper::success($res['message']) : ResponseHelper::error($res['message']);
            break;

        default:
            ResponseHelper::error('Action không hợp lệ');
    }
} catch (Throwable $ex) {
    ResponseHelper::error(AppConfig::APP_DEBUG ? $ex->getMessage() : 'Lỗi hệ thống', 500);
}
