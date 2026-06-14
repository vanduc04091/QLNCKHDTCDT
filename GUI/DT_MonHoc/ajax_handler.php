<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../BUS/DT_MonHoc_BUS.php';

Helper::requireAjaxCsrf();

$action = Helper::post('action', '');
$u = SessionHelper::userId();
$MODULE = DT_MonHoc_BUS::MODULE_KEY;

try {
    switch ($action) {
        case 'getPaged':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XEM);
            $page = Helper::postInt('page', 1);
            $size = Helper::postInt('pageSize', AppConfig::DEFAULT_PAGE_SIZE);
            $search = Helper::postStr('search');
            $daXoa = Helper::postInt('da_xoa', 0);
            $trangThai = Helper::postInt('trang_thai', -1);
            $chuongTrinhId = Helper::postInt('chuong_trinh_id', 0);
            $res = DT_MonHoc_BUS::getPaged($page, $size, $search, $daXoa, $trangThai, $chuongTrinhId);
            ResponseHelper::paged($res['data'], $page, $size, $res['totalRecords']);
            break;

        case 'getStats':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XEM);
            ResponseHelper::success('OK', DT_MonHoc_BUS::getStats());
            break;

        case 'getById':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XEM);
            $e = DT_MonHoc_BUS::getById(Helper::postInt('id'));
            if (!$e) ResponseHelper::error('Không tìm thấy');
            ResponseHelper::success('OK', $e);
            break;

        case 'insert':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_THEM);
            $e = new DT_MonHoc_PUBLIC();
            $e->ma_mon_hoc = Helper::postStr('ma_mon_hoc');
            $e->ten_mon_hoc = Helper::postStr('ten_mon_hoc');
            $e->mo_ta = Helper::postStr('mo_ta');
            $e->so_tiet_ly_thuyet = Helper::postInt('so_tiet_ly_thuyet', 0);
            $e->so_tiet_thuc_hanh = Helper::postInt('so_tiet_thuc_hanh', 0);
            $e->so_tin_chi = (float)Helper::postStr('so_tin_chi', '0');
            $e->thu_tu = Helper::postInt('thu_tu', 0);
            $e->chuong_trinh_id = Helper::postInt('chuong_trinh_id') ?: null;
            $e->trang_thai = Helper::postInt('trang_thai', 1);
            $e->nguoi_tao = $u;
            $res = DT_MonHoc_BUS::insert($e);
            $res['success'] ? ResponseHelper::success($res['message'], $res['data'] ?? null) : ResponseHelper::error($res['message']);
            break;

        case 'update':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_SUA);
            $e = new DT_MonHoc_PUBLIC();
            $e->id = Helper::postInt('id');
            $e->ma_mon_hoc = Helper::postStr('ma_mon_hoc');
            $e->ten_mon_hoc = Helper::postStr('ten_mon_hoc');
            $e->mo_ta = Helper::postStr('mo_ta');
            $e->so_tiet_ly_thuyet = Helper::postInt('so_tiet_ly_thuyet', 0);
            $e->so_tiet_thuc_hanh = Helper::postInt('so_tiet_thuc_hanh', 0);
            $e->so_tin_chi = (float)Helper::postStr('so_tin_chi', '0');
            $e->thu_tu = Helper::postInt('thu_tu', 0);
            $e->chuong_trinh_id = Helper::postInt('chuong_trinh_id') ?: null;
            $e->trang_thai = Helper::postInt('trang_thai', 1);
            $e->nguoi_cap_nhat = $u;
            $res = DT_MonHoc_BUS::update($e);
            $res['success'] ? ResponseHelper::success($res['message']) : ResponseHelper::error($res['message']);
            break;

        case 'trash':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XOA);
            $res = DT_MonHoc_BUS::trash(Helper::postInt('id'), $u);
            $res['success'] ? ResponseHelper::success($res['message']) : ResponseHelper::error($res['message']);
            break;

        case 'restore':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_SUA);
            $res = DT_MonHoc_BUS::restore(Helper::postInt('id'), $u);
            $res['success'] ? ResponseHelper::success($res['message']) : ResponseHelper::error($res['message']);
            break;

        case 'delete':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XOA);
            $res = DT_MonHoc_BUS::delete(Helper::postInt('id'));
            $res['success'] ? ResponseHelper::success($res['message']) : ResponseHelper::error($res['message']);
            break;

        default:
            ResponseHelper::error('Action không hợp lệ');
    }
} catch (Throwable $ex) {
    ResponseHelper::error(AppConfig::APP_DEBUG ? $ex->getMessage() : 'Lỗi hệ thống', 500);
}
