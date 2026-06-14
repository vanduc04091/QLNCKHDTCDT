<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../BUS/DT_KhoaHoc_BUS.php';
require_once __DIR__ . '/../../BUS/DT_KhoaHocChuongTrinh_BUS.php';
require_once __DIR__ . '/../../BUS/DT_ChuongTrinh_BUS.php';

Helper::requireAjaxCsrf();

$action = Helper::post('action', '');
$u = SessionHelper::userId();
$MODULE = DT_KhoaHoc_BUS::MODULE_KEY;

try {
    switch ($action) {
        case 'listChuongTrinh':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XEM);
            ResponseHelper::success('OK', DT_KhoaHocChuongTrinh_BUS::getByKhoaHoc(Helper::postInt('khoa_hoc_id')));
            break;

        case 'getComboChuongTrinh':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XEM);
            ResponseHelper::success('OK', DT_ChuongTrinh_BUS::getCombo());
            break;

        case 'ct_add':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_SUA);
            $info = [
                'ngay_bat_dau'    => Helper::postStr('ngay_bat_dau') ?: null,
                'ngay_ket_thuc'   => Helper::postStr('ngay_ket_thuc') ?: null,
                'dia_diem'        => Helper::postStr('dia_diem') ?: null,
                'giao_vien_id'    => Helper::postInt('giao_vien_id') ?: null,
                'giao_vien_ngoai' => Helper::postStr('giao_vien_ngoai') ?: null,
                'trang_thai'      => Helper::postInt('trang_thai', 0),
            ];
            $res = DT_KhoaHocChuongTrinh_BUS::add(Helper::postInt('khoa_hoc_id'), Helper::postInt('chuong_trinh_id'), $u, $info);
            $res['success'] ? ResponseHelper::success($res['message'], $res['data'] ?? null) : ResponseHelper::error($res['message']);
            break;

        case 'ct_remove':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_SUA);
            $res = DT_KhoaHocChuongTrinh_BUS::remove(Helper::postInt('id'), $u);
            $res['success'] ? ResponseHelper::success($res['message']) : ResponseHelper::error($res['message']);
            break;

        case 'getPaged':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XEM);
            $page = Helper::postInt('page', 1);
            $size = Helper::postInt('pageSize', AppConfig::DEFAULT_PAGE_SIZE);
            $search = Helper::postStr('search');
            $daXoa = Helper::postInt('da_xoa', 0);
            $lh = Helper::postInt('loai_hinh_dao_tao_id', 0);
            $ht = Helper::postInt('hinh_thuc_hoc_id', 0);
            $dt = Helper::postInt('doi_tuong_hoc_vien_id', 0);
            $res = DT_KhoaHoc_BUS::getPaged($page, $size, $search, $daXoa, $lh, $ht, $dt);
            ResponseHelper::paged($res['data'], $page, $size, $res['totalRecords']);
            break;

        case 'getById':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XEM);
            $e = DT_KhoaHoc_BUS::getById(Helper::postInt('id'));
            if (!$e) ResponseHelper::error('Không tìm thấy');
            ResponseHelper::success('OK', $e);
            break;

        case 'insert':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_THEM);
            $e = new DT_KhoaHoc_PUBLIC();
            $e->ma_khoa_hoc = Helper::postStr('ma_khoa_hoc');
            $e->ten_khoa_hoc = Helper::postStr('ten_khoa_hoc');
            $e->mo_ta = Helper::postStr('mo_ta');
            $e->muc_tieu = Helper::postStr('muc_tieu');
            $e->loai_hinh_dao_tao_id = Helper::postInt('loai_hinh_dao_tao_id') ?: null;
            $e->hinh_thuc_hoc_id = Helper::postInt('hinh_thuc_hoc_id') ?: null;
            $e->doi_tuong_hoc_vien_id = Helper::postInt('doi_tuong_hoc_vien_id') ?: null;
            $e->dot_dang_ky_id = Helper::postInt('dot_dang_ky_id') ?: null;
            $e->dieu_kien = Helper::postStr('dieu_kien');
            $e->ngay_bat_dau = Helper::postStr('ngay_bat_dau') ?: null;
            $e->ngay_ket_thuc = Helper::postStr('ngay_ket_thuc') ?: null;
            $e->trang_thai = Helper::postInt('trang_thai', 1);
            $e->nguoi_tao = $u;
            $res = DT_KhoaHoc_BUS::insert($e);
            $res['success'] ? ResponseHelper::success($res['message'], $res['data'] ?? null) : ResponseHelper::error($res['message']);
            break;

        case 'update':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_SUA);
            $e = new DT_KhoaHoc_PUBLIC();
            $e->id = Helper::postInt('id');
            $e->ma_khoa_hoc = Helper::postStr('ma_khoa_hoc');
            $e->ten_khoa_hoc = Helper::postStr('ten_khoa_hoc');
            $e->mo_ta = Helper::postStr('mo_ta');
            $e->muc_tieu = Helper::postStr('muc_tieu');
            $e->loai_hinh_dao_tao_id = Helper::postInt('loai_hinh_dao_tao_id') ?: null;
            $e->hinh_thuc_hoc_id = Helper::postInt('hinh_thuc_hoc_id') ?: null;
            $e->doi_tuong_hoc_vien_id = Helper::postInt('doi_tuong_hoc_vien_id') ?: null;
            $e->dot_dang_ky_id = Helper::postInt('dot_dang_ky_id') ?: null;
            $e->dieu_kien = Helper::postStr('dieu_kien');
            $e->ngay_bat_dau = Helper::postStr('ngay_bat_dau') ?: null;
            $e->ngay_ket_thuc = Helper::postStr('ngay_ket_thuc') ?: null;
            $e->trang_thai = Helper::postInt('trang_thai', 1);
            $e->nguoi_cap_nhat = $u;
            $res = DT_KhoaHoc_BUS::update($e);
            $res['success'] ? ResponseHelper::success($res['message']) : ResponseHelper::error($res['message']);
            break;

        case 'trash':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XOA);
            $res = DT_KhoaHoc_BUS::trash(Helper::postInt('id'), $u);
            $res['success'] ? ResponseHelper::success($res['message']) : ResponseHelper::error($res['message']);
            break;

        case 'restore':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_SUA);
            $res = DT_KhoaHoc_BUS::restore(Helper::postInt('id'), $u);
            $res['success'] ? ResponseHelper::success($res['message']) : ResponseHelper::error($res['message']);
            break;

        case 'delete':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XOA);
            $res = DT_KhoaHoc_BUS::delete(Helper::postInt('id'));
            $res['success'] ? ResponseHelper::success($res['message']) : ResponseHelper::error($res['message']);
            break;

        default:
            ResponseHelper::error('Action không hợp lệ');
    }
} catch (Throwable $ex) {
    ResponseHelper::error(AppConfig::APP_DEBUG ? $ex->getMessage() : 'Lỗi hệ thống', 500);
}
