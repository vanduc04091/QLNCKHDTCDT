<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../BUS/DT_BaiKiemTra_BUS.php';
require_once __DIR__ . '/../../BUS/DT_LopHoc_BUS.php';

Helper::requireAjaxCsrf();

$action = Helper::post('action', '');
$u = SessionHelper::userId();
$MODULE = DT_BaiKiemTra_BUS::MODULE_KEY;

try {
    switch ($action) {
        case 'getPaged':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XEM);
            $opts = [
                'search' => Helper::postStr('search'),
                'lop_hoc_id' => Helper::postInt('lop_hoc_id'),
                'mon_hoc_id' => Helper::postInt('mon_hoc_id'),
                'loai_bkt' => Helper::postInt('loai_bkt'),
                'trang_thai' => isset($_POST['trang_thai']) && $_POST['trang_thai'] !== '' ? Helper::postInt('trang_thai', -1) : -1,
            ];
            $res = DT_BaiKiemTra_BUS::getPaged(
                Helper::postInt('page', 1),
                Helper::postInt('pageSize', AppConfig::DEFAULT_PAGE_SIZE),
                $opts,
                Helper::postInt('da_xoa', 0)
            );
            ResponseHelper::paged($res['data'], Helper::postInt('page', 1), Helper::postInt('pageSize', AppConfig::DEFAULT_PAGE_SIZE), $res['totalRecords']);
            break;

        case 'getById':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XEM);
            $e = DT_BaiKiemTra_BUS::getById(Helper::postInt('id'));
            if (!$e) ResponseHelper::error('Không tìm thấy');
            ResponseHelper::success('OK', $e);
            break;

        case 'getStats':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XEM);
            ResponseHelper::success('OK', DT_BaiKiemTra_BUS::getStats());
            break;

        case 'getComboLop':
            ResponseHelper::success('OK', DT_LopHoc_BUS::getPaged(1, 500, '', 0, 0, -1)['data']);
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
            $e = new DT_BaiKiemTra_PUBLIC();
            if ($isUpdate) $e->id = Helper::postInt('id');
            $e->ma_bkt = Helper::postStr('ma_bkt');
            $e->tieu_de = Helper::postStr('tieu_de');
            $e->mo_ta = Helper::postStr('mo_ta') ?: null;
            $e->loai_bkt = Helper::postInt('loai_bkt', 1);
            $e->lop_hoc_id = Helper::postInt('lop_hoc_id') ?: null;
            $e->mon_hoc_id = Helper::postInt('mon_hoc_id') ?: null;
            $e->ngay_kiem_tra = Helper::postStr('ngay_kiem_tra') ?: null;
            $e->thoi_gian_lam_bai = isset($_POST['thoi_gian_lam_bai']) && $_POST['thoi_gian_lam_bai'] !== '' ? (int)$_POST['thoi_gian_lam_bai'] : null;
            $e->cong_khai_dap_an = Helper::postInt('cong_khai_dap_an', 0) ? 1 : 0;
            $e->trang_thai = Helper::postInt('trang_thai', 1);
            $e->ghi_chu = Helper::postStr('ghi_chu') ?: null;

            $deFile = $_FILES['de_file'] ?? null;
            $apFile = $_FILES['dap_an_file'] ?? null;

            if ($isUpdate) { $e->nguoi_cap_nhat = $u; $res = DT_BaiKiemTra_BUS::update($e, $deFile, $apFile); }
            else { $e->nguoi_tao = $u; $res = DT_BaiKiemTra_BUS::insert($e, $deFile, $apFile); }
            $res['success'] ? ResponseHelper::success($res['message'], $res['data'] ?? null) : ResponseHelper::error($res['message']);
            break;

        case 'clearFile':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_SUA);
            $res = DT_BaiKiemTra_BUS::clearFile(Helper::postInt('id'), Helper::postStr('field'), $u);
            $res['success'] ? ResponseHelper::success($res['message']) : ResponseHelper::error($res['message']);
            break;

        case 'trash':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XOA);
            $res = DT_BaiKiemTra_BUS::trash(Helper::postInt('id'), $u);
            $res['success'] ? ResponseHelper::success($res['message']) : ResponseHelper::error($res['message']);
            break;

        case 'restore':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_SUA);
            $res = DT_BaiKiemTra_BUS::restore(Helper::postInt('id'), $u);
            $res['success'] ? ResponseHelper::success($res['message']) : ResponseHelper::error($res['message']);
            break;

        case 'delete':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XOA);
            $res = DT_BaiKiemTra_BUS::delete(Helper::postInt('id'));
            $res['success'] ? ResponseHelper::success($res['message']) : ResponseHelper::error($res['message']);
            break;

        default:
            ResponseHelper::error('Action không hợp lệ');
    }
} catch (Throwable $ex) {
    ResponseHelper::error(AppConfig::APP_DEBUG ? $ex->getMessage() : 'Lỗi hệ thống', 500);
}
