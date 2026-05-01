<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../BUS/DM_KhoaPhong_BUS.php';
require_once __DIR__ . '/../../BUS/DM_NhanVien_BUS.php';

Helper::requireAjaxCsrf();

$action = Helper::post('action', '');
$u = SessionHelper::userId();
$MODULE = DM_KhoaPhong_BUS::MODULE_KEY;

try {
    switch ($action) {
        case 'getPaged':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XEM);
            $page = Helper::postInt('page', 1);
            $size = Helper::postInt('pageSize', AppConfig::DEFAULT_PAGE_SIZE);
            $search = Helper::postStr('search');
            $daXoa = Helper::postInt('da_xoa', 0);
            $loai = Helper::postStr('loai_don_vi');
            $res = DM_KhoaPhong_BUS::getPaged($page, $size, $search, $daXoa, $loai);
            ResponseHelper::paged($res['data'], $page, $size, $res['totalRecords']);
            break;

        case 'getById':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XEM);
            $id = Helper::postInt('id');
            $e = DM_KhoaPhong_BUS::getById($id);
            if (!$e) ResponseHelper::error('Không tìm thấy');
            ResponseHelper::success('OK', $e);
            break;

        case 'insert':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_THEM);
            $e = new DM_KhoaPhong_PUBLIC();
            $e->ma_khoa = Helper::postStr('ma_khoa');
            $e->ten_khoa = Helper::postStr('ten_khoa');
            $e->loai_don_vi = Helper::postStr('loai_don_vi', Constants::LOAI_KHOA);
            $e->truong_khoa_id = Helper::postInt('truong_khoa_id') ?: null;
            $e->dien_thoai = Helper::postStr('dien_thoai') ?: null;
            $e->email = Helper::postStr('email') ?: null;
            $e->chuyen_khoa = Helper::postStr('chuyen_khoa') ?: null;
            $e->so_giuong = Helper::postInt('so_giuong') ?: null;
            $e->trang_thai = Helper::postInt('trang_thai', 1);
            $e->nguoi_tao = $u;
            $res = DM_KhoaPhong_BUS::insert($e);
            $res['success'] ? ResponseHelper::success($res['message'], $res['data'] ?? null) : ResponseHelper::error($res['message']);
            break;

        case 'update':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_SUA);
            $e = new DM_KhoaPhong_PUBLIC();
            $e->id = Helper::postInt('id');
            $e->ma_khoa = Helper::postStr('ma_khoa');
            $e->ten_khoa = Helper::postStr('ten_khoa');
            $e->loai_don_vi = Helper::postStr('loai_don_vi', Constants::LOAI_KHOA);
            $e->truong_khoa_id = Helper::postInt('truong_khoa_id') ?: null;
            $e->dien_thoai = Helper::postStr('dien_thoai') ?: null;
            $e->email = Helper::postStr('email') ?: null;
            $e->chuyen_khoa = Helper::postStr('chuyen_khoa') ?: null;
            $e->so_giuong = Helper::postInt('so_giuong') ?: null;
            $e->trang_thai = Helper::postInt('trang_thai', 1);
            $e->nguoi_cap_nhat = $u;
            $res = DM_KhoaPhong_BUS::update($e);
            $res['success'] ? ResponseHelper::success($res['message']) : ResponseHelper::error($res['message']);
            break;

        case 'trash':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XOA);
            $res = DM_KhoaPhong_BUS::trash(Helper::postInt('id'), $u);
            $res['success'] ? ResponseHelper::success($res['message']) : ResponseHelper::error($res['message']);
            break;

        case 'restore':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_SUA);
            $res = DM_KhoaPhong_BUS::restore(Helper::postInt('id'), $u);
            $res['success'] ? ResponseHelper::success($res['message']) : ResponseHelper::error($res['message']);
            break;

        case 'delete':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XOA);
            $res = DM_KhoaPhong_BUS::delete(Helper::postInt('id'));
            $res['success'] ? ResponseHelper::success($res['message']) : ResponseHelper::error($res['message']);
            break;

        case 'getComboNV':
            $kp = Helper::postInt('khoa_phong_id', 0);
            ResponseHelper::success('OK', DM_NhanVien_BUS::getCombo($kp));
            break;

        default:
            ResponseHelper::error('Action không hợp lệ');
    }
} catch (Throwable $ex) {
    ResponseHelper::error(AppConfig::APP_DEBUG ? $ex->getMessage() : 'Lỗi hệ thống', 500);
}
