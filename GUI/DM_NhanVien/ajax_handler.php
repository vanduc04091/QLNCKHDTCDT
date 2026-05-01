<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../BUS/DM_NhanVien_BUS.php';
require_once __DIR__ . '/../../BUS/DM_KhoaPhong_BUS.php';

Helper::requireAjaxCsrf();

$action = Helper::post('action', '');
$u = SessionHelper::userId();
$MODULE = DM_NhanVien_BUS::MODULE_KEY;

try {
    switch ($action) {
        case 'getPaged':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XEM);
            $page = Helper::postInt('page', 1);
            $size = Helper::postInt('pageSize', AppConfig::DEFAULT_PAGE_SIZE);
            $search = Helper::postStr('search');
            $daXoa = Helper::postInt('da_xoa', 0);
            $kpId = Helper::postInt('khoa_phong_id', 0);
            $res = DM_NhanVien_BUS::getPaged($page, $size, $search, $daXoa, $kpId);
            ResponseHelper::paged($res['data'], $page, $size, $res['totalRecords']);
            break;

        case 'getById':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XEM);
            $id = Helper::postInt('id');
            $e = DM_NhanVien_BUS::getById($id);
            if (!$e) ResponseHelper::error('Không tìm thấy');
            ResponseHelper::success('OK', $e);
            break;

        case 'insert':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_THEM);
            $e = new DM_NhanVien_PUBLIC();
            $e->benh_vien_id = Helper::postInt('benh_vien_id', 1);
            $e->ma_nv = Helper::postStr('ma_nv');
            $e->ho_ten = Helper::postStr('ho_ten');
            $e->ngay_sinh = Helper::postStr('ngay_sinh') ?: null;
            $e->gioi_tinh = Helper::postStr('gioi_tinh') ?: null;
            $e->chuc_danh = Helper::postStr('chuc_danh') ?: null;
            $e->khoa_phong_id = Helper::postInt('khoa_phong_id') ?: null;
            $e->trinh_do = Helper::postStr('trinh_do') ?: null;
            $e->chuyen_khoa = Helper::postStr('chuyen_khoa') ?: null;
            $e->dien_thoai = Helper::postStr('dien_thoai') ?: null;
            $e->email = Helper::postStr('email') ?: null;
            $e->dia_chi = Helper::postStr('dia_chi') ?: null;
            $e->trang_thai = Helper::postInt('trang_thai', 1);
            $e->nguoi_tao = $u;
            $res = DM_NhanVien_BUS::insert($e);
            $res['success'] ? ResponseHelper::success($res['message'], $res['data'] ?? null) : ResponseHelper::error($res['message']);
            break;

        case 'update':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_SUA);
            $e = new DM_NhanVien_PUBLIC();
            $e->id = Helper::postInt('id');
            $e->benh_vien_id = Helper::postInt('benh_vien_id', 1);
            $e->ma_nv = Helper::postStr('ma_nv');
            $e->ho_ten = Helper::postStr('ho_ten');
            $e->ngay_sinh = Helper::postStr('ngay_sinh') ?: null;
            $e->gioi_tinh = Helper::postStr('gioi_tinh') ?: null;
            $e->chuc_danh = Helper::postStr('chuc_danh') ?: null;
            $e->khoa_phong_id = Helper::postInt('khoa_phong_id') ?: null;
            $e->trinh_do = Helper::postStr('trinh_do') ?: null;
            $e->chuyen_khoa = Helper::postStr('chuyen_khoa') ?: null;
            $e->dien_thoai = Helper::postStr('dien_thoai') ?: null;
            $e->email = Helper::postStr('email') ?: null;
            $e->dia_chi = Helper::postStr('dia_chi') ?: null;
            $e->trang_thai = Helper::postInt('trang_thai', 1);
            $e->nguoi_cap_nhat = $u;
            $res = DM_NhanVien_BUS::update($e);
            $res['success'] ? ResponseHelper::success($res['message']) : ResponseHelper::error($res['message']);
            break;

        case 'trash':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XOA);
            $res = DM_NhanVien_BUS::trash(Helper::postInt('id'), $u);
            $res['success'] ? ResponseHelper::success($res['message']) : ResponseHelper::error($res['message']);
            break;

        case 'restore':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_SUA);
            $res = DM_NhanVien_BUS::restore(Helper::postInt('id'), $u);
            $res['success'] ? ResponseHelper::success($res['message']) : ResponseHelper::error($res['message']);
            break;

        case 'delete':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XOA);
            $res = DM_NhanVien_BUS::delete(Helper::postInt('id'));
            $res['success'] ? ResponseHelper::success($res['message']) : ResponseHelper::error($res['message']);
            break;

        case 'getComboKhoa':
            ResponseHelper::success('OK', DM_KhoaPhong_BUS::getCombo());
            break;

        default:
            ResponseHelper::error('Action không hợp lệ');
    }
} catch (Throwable $ex) {
    ResponseHelper::error(AppConfig::APP_DEBUG ? $ex->getMessage() : 'Lỗi hệ thống', 500);
}
