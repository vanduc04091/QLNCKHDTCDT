<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../BUS/DM_BenhVien_BUS.php';

Helper::requireAjaxCsrf();

$action = Helper::post('action', '');
$u = SessionHelper::userId();
$MODULE = DM_BenhVien_BUS::MODULE_KEY;

try {
    switch ($action) {
        case 'getPaged':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XEM);
            $page = Helper::postInt('page', 1);
            $size = Helper::postInt('pageSize', AppConfig::DEFAULT_PAGE_SIZE);
            $search = Helper::postStr('search');
            $daXoa = Helper::postInt('da_xoa', 0);
            $cap = Helper::postStr('cap_benh_vien');
            $res = DM_BenhVien_BUS::getPaged($page, $size, $search, $daXoa, $cap);
            ResponseHelper::paged($res['data'], $page, $size, $res['totalRecords']);
            break;

        case 'getById':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XEM);
            $id = Helper::postInt('id');
            $e = DM_BenhVien_BUS::getById($id);
            if (!$e) ResponseHelper::error('Không tìm thấy');
            ResponseHelper::success('OK', $e);
            break;

        case 'insert':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_THEM);
            $e = new DM_BenhVien_PUBLIC();
            $e->ma_benh_vien = Helper::postStr('ma_benh_vien');
            $e->ten_benh_vien = Helper::postStr('ten_benh_vien');
            $e->dia_chi = Helper::postStr('dia_chi') ?: null;
            $e->dien_thoai = Helper::postStr('dien_thoai') ?: null;
            $e->email = Helper::postStr('email') ?: null;
            $e->cap_benh_vien = Helper::postStr('cap_benh_vien', Constants::CAP_BV_TINH);
            $e->hang_benh_vien = Helper::postStr('hang_benh_vien') ?: null;
            $e->giam_doc = Helper::postStr('giam_doc') ?: null;
            $e->dien_thoai_giam_doc = Helper::postStr('dien_thoai_giam_doc') ?: null;
            $e->la_benh_vien_chinh = Helper::postInt('la_benh_vien_chinh', 0);
            $e->ngay_ky_hop_tac = Helper::postStr('ngay_ky_hop_tac') ?: null;
            $e->trang_thai = Helper::postInt('trang_thai', 1);
            $e->nguoi_tao = $u;
            $res = DM_BenhVien_BUS::insert($e);
            $res['success'] ? ResponseHelper::success($res['message'], $res['data'] ?? null) : ResponseHelper::error($res['message']);
            break;

        case 'update':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_SUA);
            $e = new DM_BenhVien_PUBLIC();
            $e->id = Helper::postInt('id');
            $e->ma_benh_vien = Helper::postStr('ma_benh_vien');
            $e->ten_benh_vien = Helper::postStr('ten_benh_vien');
            $e->dia_chi = Helper::postStr('dia_chi') ?: null;
            $e->dien_thoai = Helper::postStr('dien_thoai') ?: null;
            $e->email = Helper::postStr('email') ?: null;
            $e->cap_benh_vien = Helper::postStr('cap_benh_vien', Constants::CAP_BV_TINH);
            $e->hang_benh_vien = Helper::postStr('hang_benh_vien') ?: null;
            $e->giam_doc = Helper::postStr('giam_doc') ?: null;
            $e->dien_thoai_giam_doc = Helper::postStr('dien_thoai_giam_doc') ?: null;
            $e->la_benh_vien_chinh = Helper::postInt('la_benh_vien_chinh', 0);
            $e->ngay_ky_hop_tac = Helper::postStr('ngay_ky_hop_tac') ?: null;
            $e->trang_thai = Helper::postInt('trang_thai', 1);
            $e->nguoi_cap_nhat = $u;
            $res = DM_BenhVien_BUS::update($e);
            $res['success'] ? ResponseHelper::success($res['message']) : ResponseHelper::error($res['message']);
            break;

        case 'trash':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XOA);
            $res = DM_BenhVien_BUS::trash(Helper::postInt('id'), $u);
            $res['success'] ? ResponseHelper::success($res['message']) : ResponseHelper::error($res['message']);
            break;

        case 'restore':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_SUA);
            $res = DM_BenhVien_BUS::restore(Helper::postInt('id'), $u);
            $res['success'] ? ResponseHelper::success($res['message']) : ResponseHelper::error($res['message']);
            break;

        case 'delete':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XOA);
            $res = DM_BenhVien_BUS::delete(Helper::postInt('id'));
            $res['success'] ? ResponseHelper::success($res['message']) : ResponseHelper::error($res['message']);
            break;

        default:
            ResponseHelper::error('Action không hợp lệ');
    }
} catch (Throwable $ex) {
    ResponseHelper::error(AppConfig::APP_DEBUG ? $ex->getMessage() : 'Lỗi hệ thống', 500);
}
