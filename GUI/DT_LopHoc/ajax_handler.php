<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../BUS/DT_LopHoc_BUS.php';
require_once __DIR__ . '/../../BUS/DT_HocVienLop_BUS.php';
require_once __DIR__ . '/../../BUS/DT_KhoaHoc_BUS.php';
require_once __DIR__ . '/../../BUS/DM_NhanVien_BUS.php';

Helper::requireAjaxCsrf();

$action = Helper::post('action', '');
$u = SessionHelper::userId();
$MODULE = DT_LopHoc_BUS::MODULE_KEY;
$MODULE_HVL = DT_HocVienLop_BUS::MODULE_KEY;

try {
    switch ($action) {
        // ====== Lớp học ======
        case 'getPaged':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XEM);
            $res = DT_LopHoc_BUS::getPaged(
                Helper::postInt('page', 1),
                Helper::postInt('pageSize', AppConfig::DEFAULT_PAGE_SIZE),
                Helper::postStr('search'),
                Helper::postInt('da_xoa', 0),
                Helper::postInt('khoa_hoc_id', 0),
                isset($_POST['trang_thai']) && $_POST['trang_thai'] !== '' ? Helper::postInt('trang_thai', -1) : -1
            );
            ResponseHelper::paged($res['data'], Helper::postInt('page', 1), Helper::postInt('pageSize', AppConfig::DEFAULT_PAGE_SIZE), $res['totalRecords']);
            break;

        case 'getById':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XEM);
            $e = DT_LopHoc_BUS::getById(Helper::postInt('id'));
            if (!$e) ResponseHelper::error('Không tìm thấy');
            ResponseHelper::success('OK', $e);
            break;

        case 'getStats':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XEM);
            ResponseHelper::success('OK', DT_LopHoc_BUS::getStats());
            break;

        case 'getComboKhoaHoc':
            ResponseHelper::success('OK', DT_KhoaHoc_BUS::getCombo());
            break;

        case 'getComboNhanVien':
            ResponseHelper::success('OK', DM_NhanVien_BUS::getCombo());
            break;

        case 'insert':
        case 'update':
            $isUpdate = $action === 'update';
            PhanQuyenHelper::requireQuyen($MODULE, $isUpdate ? PhanQuyenHelper::QUYEN_SUA : PhanQuyenHelper::QUYEN_THEM);
            $e = new DT_LopHoc_PUBLIC();
            if ($isUpdate) $e->id = Helper::postInt('id');
            $e->ma_lop = Helper::postStr('ma_lop');
            $e->ten_lop = Helper::postStr('ten_lop');
            $e->khoa_hoc_id = Helper::postInt('khoa_hoc_id');
            $e->ngay_bat_dau = Helper::postStr('ngay_bat_dau') ?: null;
            $e->ngay_ket_thuc = Helper::postStr('ngay_ket_thuc') ?: null;
            $e->so_luong_toi_da = Helper::postInt('so_luong_toi_da', 30);
            $e->dia_diem = Helper::postStr('dia_diem') ?: null;
            $e->giao_vien_id = Helper::postInt('giao_vien_id') ?: null;
            $e->giao_vien_ngoai = Helper::postStr('giao_vien_ngoai') ?: null;
            $e->mo_ta = Helper::postStr('mo_ta') ?: null;
            $e->trang_thai = Helper::postInt('trang_thai', 0);
            if ($isUpdate) { $e->nguoi_cap_nhat = $u; $res = DT_LopHoc_BUS::update($e); }
            else { $e->nguoi_tao = $u; $res = DT_LopHoc_BUS::insert($e); }
            $res['success'] ? ResponseHelper::success($res['message'], $res['data'] ?? null) : ResponseHelper::error($res['message']);
            break;

        case 'trash':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XOA);
            $res = DT_LopHoc_BUS::trash(Helper::postInt('id'), $u);
            $res['success'] ? ResponseHelper::success($res['message']) : ResponseHelper::error($res['message']);
            break;

        case 'restore':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_SUA);
            $res = DT_LopHoc_BUS::restore(Helper::postInt('id'), $u);
            $res['success'] ? ResponseHelper::success($res['message']) : ResponseHelper::error($res['message']);
            break;

        case 'delete':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XOA);
            $res = DT_LopHoc_BUS::delete(Helper::postInt('id'));
            $res['success'] ? ResponseHelper::success($res['message']) : ResponseHelper::error($res['message']);
            break;

        // ====== Học viên - Lớp ======
        case 'hvl_list':
            PhanQuyenHelper::requireQuyen($MODULE_HVL, PhanQuyenHelper::QUYEN_XEM);
            $lopId = Helper::postInt('lop_hoc_id');
            $data = DT_HocVienLop_BUS::getByLop($lopId, Helper::postStr('search'));
            ResponseHelper::success('OK', $data);
            break;

        case 'hvl_available':
            PhanQuyenHelper::requireQuyen($MODULE_HVL, PhanQuyenHelper::QUYEN_THEM);
            $data = DT_HocVienLop_BUS::getHocVienChuaGhiDanh(
                Helper::postInt('lop_hoc_id'),
                Helper::postStr('search'),
                100
            );
            ResponseHelper::success('OK', $data);
            break;

        case 'hvl_bulk_add':
            PhanQuyenHelper::requireQuyen($MODULE_HVL, PhanQuyenHelper::QUYEN_THEM);
            $ids = isset($_POST['hoc_vien_ids']) && is_array($_POST['hoc_vien_ids']) ? $_POST['hoc_vien_ids'] : [];
            $res = DT_HocVienLop_BUS::bulkAdd(Helper::postInt('lop_hoc_id'), $ids, $u);
            $res['success'] ? ResponseHelper::success($res['message'], $res['data'] ?? null) : ResponseHelper::error($res['message']);
            break;

        case 'hvl_getById':
            PhanQuyenHelper::requireQuyen($MODULE_HVL, PhanQuyenHelper::QUYEN_XEM);
            $e = DT_HocVienLop_BUS::getById(Helper::postInt('id'));
            if (!$e) ResponseHelper::error('Không tìm thấy');
            ResponseHelper::success('OK', $e);
            break;

        case 'hvl_update':
            PhanQuyenHelper::requireQuyen($MODULE_HVL, PhanQuyenHelper::QUYEN_SUA);
            $e = new DT_HocVienLop_PUBLIC();
            $e->id = Helper::postInt('id');
            $e->ngay_ghi_danh = Helper::postStr('ngay_ghi_danh') ?: null;
            $e->trang_thai = Helper::postInt('trang_thai', 1);
            $e->diem_tong_ket = isset($_POST['diem_tong_ket']) && $_POST['diem_tong_ket'] !== '' ? (float)$_POST['diem_tong_ket'] : null;
            $e->xep_loai = Helper::postStr('xep_loai') ?: null;
            $e->ghi_chu = Helper::postStr('ghi_chu') ?: null;
            $e->nguoi_cap_nhat = $u;
            $res = DT_HocVienLop_BUS::update($e);
            $res['success'] ? ResponseHelper::success($res['message']) : ResponseHelper::error($res['message']);
            break;

        case 'hvl_delete':
            PhanQuyenHelper::requireQuyen($MODULE_HVL, PhanQuyenHelper::QUYEN_XOA);
            $res = DT_HocVienLop_BUS::delete(Helper::postInt('id'));
            $res['success'] ? ResponseHelper::success($res['message']) : ResponseHelper::error($res['message']);
            break;

        default:
            ResponseHelper::error('Action không hợp lệ');
    }
} catch (Throwable $ex) {
    ResponseHelper::error(AppConfig::APP_DEBUG ? $ex->getMessage() : 'Lỗi hệ thống', 500);
}
