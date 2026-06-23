<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../BUS/DT_ChuongTrinh_BUS.php';
require_once __DIR__ . '/../../BUS/DT_KhoaHocChuongTrinh_BUS.php';
require_once __DIR__ . '/../../BUS/DT_ChuongTrinhMonHoc_BUS.php';
require_once __DIR__ . '/../../BUS/DT_KhoaHoc_BUS.php';
require_once __DIR__ . '/../../BUS/DT_MonHoc_BUS.php';
require_once __DIR__ . '/../../BUS/DM_NhanVien_BUS.php';
require_once __DIR__ . '/../../BUS/DM_KhoaPhong_BUS.php';
require_once __DIR__ . '/../../BUS/DM_DoiTuongHocVien_BUS.php';

Helper::requireAjaxCsrf();

$action = Helper::post('action', '');
$u = SessionHelper::userId();
$MODULE = DT_ChuongTrinh_BUS::MODULE_KEY;
$MODULE_CTMH = DT_ChuongTrinhMonHoc_BUS::MODULE_KEY;

try {
    switch ($action) {
        // ====== Chương trình đào tạo ======
        case 'getPaged':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XEM);
            $res = DT_ChuongTrinh_BUS::getPaged(
                Helper::postInt('page', 1),
                Helper::postInt('pageSize', AppConfig::DEFAULT_PAGE_SIZE),
                Helper::postStr('search'),
                Helper::postInt('da_xoa', 0),
                Helper::postInt('khoa_hoc_id', 0),
                Helper::postInt('doi_tuong_id', 0)
            );
            ResponseHelper::paged($res['data'], Helper::postInt('page', 1), Helper::postInt('pageSize', AppConfig::DEFAULT_PAGE_SIZE), $res['totalRecords']);
            break;

        case 'getById':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XEM);
            $e = DT_ChuongTrinh_BUS::getById(Helper::postInt('id'));
            if (!$e) ResponseHelper::error('Không tìm thấy');
            ResponseHelper::success('OK', $e);
            break;

        case 'getStats':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XEM);
            ResponseHelper::success('OK', DT_ChuongTrinh_BUS::getStats());
            break;

        case 'getComboKhoaHoc':
            ResponseHelper::success('OK', DT_KhoaHoc_BUS::getCombo());
            break;

        case 'getComboMonHoc':
            ResponseHelper::success('OK', DT_MonHoc_BUS::getCombo());
            break;

        case 'getComboNhanVien':
            ResponseHelper::success('OK', DM_NhanVien_BUS::getCombo());
            break;

        case 'getComboKhoaPhong':
            ResponseHelper::success('OK', DM_KhoaPhong_BUS::getCombo());
            break;

        case 'getComboDoiTuong':
            ResponseHelper::success('OK', DM_DoiTuongHocVien_BUS::getCombo());
            break;

        case 'insert':
        case 'update':
            $isUpdate = $action === 'update';
            PhanQuyenHelper::requireQuyen($MODULE, $isUpdate ? PhanQuyenHelper::QUYEN_SUA : PhanQuyenHelper::QUYEN_THEM);
            $e = new DT_ChuongTrinh_PUBLIC();
            if ($isUpdate) $e->id = Helper::postInt('id');
            $e->ma_chuong_trinh = Helper::postStr('ma_chuong_trinh');
            $e->ten_chuong_trinh = Helper::postStr('ten_chuong_trinh');
            $e->thu_tu = Helper::postInt('thu_tu', 0);
            $e->thoi_luong = Helper::postStr('thoi_luong') ?: null;
            $e->khoa_phong_id = Helper::postInt('khoa_phong_id') ?: null;
            $e->doi_tuong_id = Helper::postInt('doi_tuong_id') ?: null;
            $e->so_luong_toi_da = Helper::postInt('so_luong_toi_da', 30);
            $e->mo_ta = Helper::postStr('mo_ta') ?: null;
            if ($isUpdate) { $e->nguoi_cap_nhat = $u; $res = DT_ChuongTrinh_BUS::update($e); }
            else { $e->nguoi_tao = $u; $res = DT_ChuongTrinh_BUS::insert($e); }
            $res['success'] ? ResponseHelper::success($res['message'], $res['data'] ?? null) : ResponseHelper::error($res['message']);
            break;

        case 'trash':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XOA);
            $res = DT_ChuongTrinh_BUS::trash(Helper::postInt('id'), $u);
            $res['success'] ? ResponseHelper::success($res['message']) : ResponseHelper::error($res['message']);
            break;

        case 'restore':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_SUA);
            $res = DT_ChuongTrinh_BUS::restore(Helper::postInt('id'), $u);
            $res['success'] ? ResponseHelper::success($res['message']) : ResponseHelper::error($res['message']);
            break;

        case 'delete':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XOA);
            $res = DT_ChuongTrinh_BUS::delete(Helper::postInt('id'));
            $res['success'] ? ResponseHelper::success($res['message']) : ResponseHelper::error($res['message']);
            break;

        // ====== Khóa học gắn vào CTĐT (N:N) ======
        case 'khoa_list':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XEM);
            ResponseHelper::success('OK', DT_KhoaHocChuongTrinh_BUS::getByChuongTrinh(Helper::postInt('chuong_trinh_id')));
            break;

        case 'khoa_add':
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

        case 'khoa_update':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_SUA);
            $info = [
                'ngay_bat_dau'    => Helper::postStr('ngay_bat_dau') ?: null,
                'ngay_ket_thuc'   => Helper::postStr('ngay_ket_thuc') ?: null,
                'dia_diem'        => Helper::postStr('dia_diem') ?: null,
                'giao_vien_id'    => Helper::postInt('giao_vien_id') ?: null,
                'giao_vien_ngoai' => Helper::postStr('giao_vien_ngoai') ?: null,
                'trang_thai'      => Helper::postInt('trang_thai', 0),
            ];
            $res = DT_KhoaHocChuongTrinh_BUS::updateInfo(Helper::postInt('id'), $info, $u);
            $res['success'] ? ResponseHelper::success($res['message']) : ResponseHelper::error($res['message']);
            break;

        case 'khoa_remove':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_SUA);
            $res = DT_KhoaHocChuongTrinh_BUS::remove(Helper::postInt('id'), $u);
            $res['success'] ? ResponseHelper::success($res['message']) : ResponseHelper::error($res['message']);
            break;

        // ====== Bài học thuộc CTĐT (1 CTĐT : N bài) ======
        case 'mon_list':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XEM);
            ResponseHelper::success('OK', DT_MonHoc_BUS::getByChuongTrinh(Helper::postInt('chuong_trinh_id')));
            break;

        case 'mon_move':
            PhanQuyenHelper::requireQuyen('DT_MonHoc', PhanQuyenHelper::QUYEN_SUA);
            $res = DT_MonHoc_BUS::move(Helper::postInt('id'), Helper::postStr('dir'), $u);
            $res['success'] ? ResponseHelper::success($res['message']) : ResponseHelper::error($res['message']);
            break;

        case 'mon_combo':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XEM);
            ResponseHelper::success('OK', DT_MonHoc_BUS::getChuaGanCombo(Helper::postInt('chuong_trinh_id')));
            break;

        case 'mon_add':
            PhanQuyenHelper::requireQuyen('DT_MonHoc', PhanQuyenHelper::QUYEN_SUA);
            $res = DT_MonHoc_BUS::assignToChuongTrinh(Helper::postInt('mon_hoc_id'), Helper::postInt('chuong_trinh_id'), $u);
            $res['success'] ? ResponseHelper::success($res['message']) : ResponseHelper::error($res['message']);
            break;

        case 'mon_remove':
            PhanQuyenHelper::requireQuyen('DT_MonHoc', PhanQuyenHelper::QUYEN_SUA);
            $res = DT_MonHoc_BUS::unassign(Helper::postInt('id'), $u);
            $res['success'] ? ResponseHelper::success($res['message']) : ResponseHelper::error($res['message']);
            break;

        default:
            ResponseHelper::error('Action không hợp lệ');
    }
} catch (Throwable $ex) {
    ResponseHelper::error(AppConfig::APP_DEBUG ? $ex->getMessage() : 'Lỗi hệ thống', 500);
}
