<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../BUS/DT_ChungChi_BUS.php';
require_once __DIR__ . '/../../BUS/DM_HocVien_BUS.php';
require_once __DIR__ . '/../../BUS/DT_KhoaHocChuongTrinh_BUS.php';

Helper::requireAjaxCsrf();

$action = Helper::post('action', '');
$u      = SessionHelper::userId();
$MODULE = DT_ChungChi_BUS::MODULE_KEY;

try {
    switch ($action) {
        case 'getPaged':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XEM);
            $opts = [
                'search'        => Helper::postStr('search'),
                'loai_chung_chi'=> Helper::postStr('loai_chung_chi'),
                'hoc_vien_id'   => Helper::postInt('hoc_vien_id'),
                'lop_hoc_id'    => Helper::postInt('lop_hoc_id'),
                'trang_thai'    => Helper::postStr('trang_thai'),
            ];
            $res = DT_ChungChi_BUS::getPaged(
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
            $e = DT_ChungChi_BUS::getById(Helper::postInt('id'));
            if (!$e) ResponseHelper::error('Không tìm thấy chứng chỉ');
            ResponseHelper::success('OK', $e);
            break;

        case 'getStats':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XEM);
            ResponseHelper::success('OK', DT_ChungChi_BUS::getStats());
            break;

        case 'getByHocVien':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XEM);
            ResponseHelper::success('OK', DT_ChungChi_BUS::getByHocVien(Helper::postInt('hoc_vien_id')));
            break;

        case 'getComboHocVien':
            ResponseHelper::success('OK', DM_HocVien_BUS::getCombo());
            break;

        case 'getComboLop':
            $rows = DT_KhoaHocChuongTrinh_BUS::getCombo();
            ResponseHelper::success('OK', $rows);
            break;

        case 'insert':
        case 'update':
            $isUpdate = ($action === 'update');
            PhanQuyenHelper::requireQuyen($MODULE, $isUpdate ? PhanQuyenHelper::QUYEN_SUA : PhanQuyenHelper::QUYEN_THEM);

            $e = new DT_ChungChi_PUBLIC();
            if ($isUpdate) $e->id = Helper::postInt('id');
            $e->hoc_vien_id        = Helper::postInt('hoc_vien_id');
            $e->lop_hoc_id         = Helper::postInt('lop_hoc_id');
            $e->so_chung_chi       = Helper::postStr('so_chung_chi');
            $e->ten_chung_chi      = Helper::postStr('ten_chung_chi');
            $e->loai_chung_chi     = Helper::postStr('loai_chung_chi') ?: 'Chứng chỉ';
            $e->xep_loai_tot_nghiep = Helper::postStr('xep_loai_tot_nghiep') ?: null;
            $dtb = Helper::postStr('diem_trung_binh');
            $e->diem_trung_binh    = ($dtb !== '' && $dtb !== null) ? (float)$dtb : null;
            $e->ngay_cap           = Helper::postStr('ngay_cap');
            $e->ngay_het_han       = Helper::postStr('ngay_het_han') ?: null;
            $e->nguoi_ky           = Helper::postStr('nguoi_ky') ?: null;
            $e->chuc_vu_nguoi_ky   = Helper::postStr('chuc_vu_nguoi_ky') ?: null;
            $e->noi_cap            = Helper::postStr('noi_cap') ?: null;
            $e->ghi_chu            = Helper::postStr('ghi_chu') ?: null;
            $e->trang_thai         = Helper::postInt('trang_thai', 0);

            $file = $_FILES['chung_chi_file'] ?? null;

            if ($isUpdate) {
                $e->nguoi_cap_nhat = $u;
                $res = DT_ChungChi_BUS::update($e, $file);
            } else {
                $e->nguoi_tao = $u;
                $res = DT_ChungChi_BUS::insert($e, $file);
            }
            $res['success']
                ? ResponseHelper::success($res['message'], $res['data'] ?? null)
                : ResponseHelper::error($res['message']);
            break;

        case 'trash':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XOA);
            $res = DT_ChungChi_BUS::trash(Helper::postInt('id'), $u);
            $res['success'] ? ResponseHelper::success($res['message']) : ResponseHelper::error($res['message']);
            break;

        case 'restore':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_SUA);
            $res = DT_ChungChi_BUS::restore(Helper::postInt('id'), $u);
            $res['success'] ? ResponseHelper::success($res['message']) : ResponseHelper::error($res['message']);
            break;

        case 'delete':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XOA);
            $res = DT_ChungChi_BUS::delete(Helper::postInt('id'));
            $res['success'] ? ResponseHelper::success($res['message']) : ResponseHelper::error($res['message']);
            break;

        case 'capChungChi':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_SUA);
            $res = DT_ChungChi_BUS::capChungChi(Helper::postInt('id'), $u);
            $res['success'] ? ResponseHelper::success($res['message']) : ResponseHelper::error($res['message']);
            break;

        case 'thuHoiChungChi':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_SUA);
            $res = DT_ChungChi_BUS::thuHoiChungChi(Helper::postInt('id'), $u);
            $res['success'] ? ResponseHelper::success($res['message']) : ResponseHelper::error($res['message']);
            break;

        default:
            ResponseHelper::error('Action không hợp lệ');
    }
} catch (Throwable $ex) {
    ResponseHelper::error(AppConfig::APP_DEBUG ? $ex->getMessage() : 'Lỗi hệ thống', 500);
}
