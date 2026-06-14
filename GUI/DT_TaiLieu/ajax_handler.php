<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../BUS/DT_TaiLieu_BUS.php';
require_once __DIR__ . '/../../BUS/DT_KhoaHoc_BUS.php';
require_once __DIR__ . '/../../BUS/DT_KhoaHocChuongTrinh_BUS.php';

Helper::requireAjaxCsrf();

$action = Helper::post('action', '');
$u = SessionHelper::userId();
$MODULE = DT_TaiLieu_BUS::MODULE_KEY;

try {
    switch ($action) {
        case 'getPaged':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XEM);
            $opts = [
                'search' => Helper::postStr('search'),
                'loai_tai_lieu' => Helper::postInt('loai_tai_lieu'),
                'dinh_dang' => Helper::postStr('dinh_dang'),
                'khoa_hoc_id' => Helper::postInt('khoa_hoc_id'),
                'lop_hoc_id' => Helper::postInt('lop_hoc_id'),
                'mon_hoc_id' => Helper::postInt('mon_hoc_id'),
                'bat_buoc' => Helper::postInt('bat_buoc'),
                'cong_khai' => Helper::postInt('cong_khai'),
                'sort_by' => Helper::postStr('sort_by'),
            ];
            $res = DT_TaiLieu_BUS::getPaged(
                Helper::postInt('page', 1),
                Helper::postInt('pageSize', AppConfig::DEFAULT_PAGE_SIZE),
                $opts,
                Helper::postInt('da_xoa', 0)
            );
            ResponseHelper::paged($res['data'], Helper::postInt('page', 1), Helper::postInt('pageSize', AppConfig::DEFAULT_PAGE_SIZE), $res['totalRecords']);
            break;

        case 'getById':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XEM);
            $e = DT_TaiLieu_BUS::getById(Helper::postInt('id'));
            if (!$e) ResponseHelper::error('Không tìm thấy');
            DT_TaiLieu_BUS::incView($e->id);
            ResponseHelper::success('OK', $e);
            break;

        case 'getStats':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XEM);
            ResponseHelper::success('OK', DT_TaiLieu_BUS::getStats());
            break;

        case 'getComboKhoaHoc':
            ResponseHelper::success('OK', DT_KhoaHoc_BUS::getCombo());
            break;

        case 'getComboLop':
            ResponseHelper::success('OK', DT_KhoaHocChuongTrinh_BUS::getCombo());
            break;

        case 'getComboMonHoc':
            $stmt = Database::getConnection()->prepare("SELECT id, ma_mon_hoc, ten_mon_hoc FROM DT_MON_HOC WHERE da_xoa=0 ORDER BY ten_mon_hoc ASC");
            $stmt->execute();
            ResponseHelper::success('OK', $stmt->fetchAll());
            break;

        case 'insert':
        case 'update':
            $isUpdate = $action === 'update';
            PhanQuyenHelper::requireQuyen($MODULE, $isUpdate ? PhanQuyenHelper::QUYEN_SUA : PhanQuyenHelper::QUYEN_THEM);
            $e = new DT_TaiLieu_PUBLIC();
            if ($isUpdate) $e->id = Helper::postInt('id');
            $e->ma_tai_lieu = Helper::postStr('ma_tai_lieu');
            $e->tieu_de = Helper::postStr('tieu_de');
            $e->mo_ta = Helper::postStr('mo_ta') ?: null;
            $e->loai_tai_lieu = Helper::postInt('loai_tai_lieu', 1);
            $e->link_ngoai = Helper::postStr('link_ngoai') ?: null;
            $e->tac_gia = Helper::postStr('tac_gia') ?: null;
            $e->nam_xuat_ban = Helper::postInt('nam_xuat_ban') ?: null;
            $e->nha_xuat_ban = Helper::postStr('nha_xuat_ban') ?: null;
            $e->khoa_hoc_id = Helper::postInt('khoa_hoc_id') ?: null;
            $e->lop_hoc_id = Helper::postInt('lop_hoc_id') ?: null;
            $e->mon_hoc_id = Helper::postInt('mon_hoc_id') ?: null;
            $e->cong_khai = Helper::postInt('cong_khai', 0) ? 1 : 0;
            $e->bat_buoc = Helper::postInt('bat_buoc', 0) ? 1 : 0;
            $e->trang_thai = Helper::postInt('trang_thai', 1);
            $e->ghi_chu = Helper::postStr('ghi_chu') ?: null;

            $file = $_FILES['tai_lieu_file'] ?? null;

            if ($isUpdate) { $e->nguoi_cap_nhat = $u; $res = DT_TaiLieu_BUS::update($e, $file); }
            else { $e->nguoi_tao = $u; $res = DT_TaiLieu_BUS::insert($e, $file); }
            $res['success'] ? ResponseHelper::success($res['message'], $res['data'] ?? null) : ResponseHelper::error($res['message']);
            break;

        case 'trash':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XOA);
            $res = DT_TaiLieu_BUS::trash(Helper::postInt('id'), $u);
            $res['success'] ? ResponseHelper::success($res['message']) : ResponseHelper::error($res['message']);
            break;

        case 'restore':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_SUA);
            $res = DT_TaiLieu_BUS::restore(Helper::postInt('id'), $u);
            $res['success'] ? ResponseHelper::success($res['message']) : ResponseHelper::error($res['message']);
            break;

        case 'delete':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XOA);
            $res = DT_TaiLieu_BUS::delete(Helper::postInt('id'));
            $res['success'] ? ResponseHelper::success($res['message']) : ResponseHelper::error($res['message']);
            break;

        case 'incDownload':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XEM);
            DT_TaiLieu_BUS::incDownload(Helper::postInt('id'));
            ResponseHelper::success('OK');
            break;

        default:
            ResponseHelper::error('Action không hợp lệ');
    }
} catch (Throwable $ex) {
    ResponseHelper::error(AppConfig::APP_DEBUG ? $ex->getMessage() : 'Lỗi hệ thống', 500);
}
