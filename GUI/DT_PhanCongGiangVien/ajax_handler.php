<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../BUS/DT_PhanCongGiangVien_BUS.php';
require_once __DIR__ . '/../../BUS/DM_GiangVien_BUS.php';
require_once __DIR__ . '/../../BUS/DT_KhoaHocChuongTrinh_BUS.php';
require_once __DIR__ . '/../../DAL/DT_KetQuaHocTap_DAL.php';

Helper::requireAjaxCsrf();

$action = Helper::post('action', '');
$u = SessionHelper::userId();
$MODULE = DT_PhanCongGiangVien_BUS::MODULE_KEY;

try {
    switch ($action) {
        case 'getList':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XEM);
            $opts = [
                'lop_hoc_id' => Helper::postInt('lop_hoc_id'),
                'giang_vien_id' => Helper::postInt('giang_vien_id'),
                'mon_hoc_id' => Helper::postInt('mon_hoc_id'),
                'vai_tro' => isset($_POST['vai_tro']) ? Helper::postStr('vai_tro') : '',
                'trang_thai' => isset($_POST['trang_thai']) && $_POST['trang_thai'] !== '' ? Helper::postInt('trang_thai', -1) : -1,
                'search' => Helper::postStr('search'),
            ];
            ResponseHelper::success('OK', DT_PhanCongGiangVien_BUS::getList($opts));
            break;

        case 'getById':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XEM);
            $e = DT_PhanCongGiangVien_BUS::getById(Helper::postInt('id'));
            if (!$e) ResponseHelper::error('Không tìm thấy');
            ResponseHelper::success('OK', $e);
            break;

        case 'getStats':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XEM);
            ResponseHelper::success('OK', DT_PhanCongGiangVien_BUS::getStats());
            break;

        case 'getComboGV':
            ResponseHelper::success('OK', DM_GiangVien_BUS::getCombo());
            break;

        case 'getComboLop':
            ResponseHelper::success('OK', DT_KhoaHocChuongTrinh_BUS::getCombo());
            break;

        case 'getMonHocByLop':
            $lopId = Helper::postInt('lop_hoc_id');
            ResponseHelper::success('OK', DT_KetQuaHocTap_DAL::getMonHocByLop($lopId));
            break;

        case 'insert':
        case 'update':
            $isUpdate = $action === 'update';
            PhanQuyenHelper::requireQuyen($MODULE, $isUpdate ? PhanQuyenHelper::QUYEN_SUA : PhanQuyenHelper::QUYEN_THEM);
            $e = new DT_PhanCongGiangVien_PUBLIC();
            if ($isUpdate) $e->id = Helper::postInt('id');
            $e->giang_vien_id = Helper::postInt('giang_vien_id');
            $e->lop_hoc_id = Helper::postInt('lop_hoc_id');
            $e->mon_hoc_id = Helper::postInt('mon_hoc_id') ?: null;
            $e->vai_tro = Helper::postInt('vai_tro', 1);
            $e->so_tiet_phan_cong = isset($_POST['so_tiet_phan_cong']) && $_POST['so_tiet_phan_cong'] !== '' ? (int)$_POST['so_tiet_phan_cong'] : null;
            $e->tu_ngay = Helper::postStr('tu_ngay') ?: null;
            $e->den_ngay = Helper::postStr('den_ngay') ?: null;
            $e->trang_thai = Helper::postInt('trang_thai', 0);
            $e->ghi_chu = Helper::postStr('ghi_chu') ?: null;
            $force = Helper::postInt('force_conflict', 0) === 1;
            if ($isUpdate) { $e->nguoi_cap_nhat = $u; $res = DT_PhanCongGiangVien_BUS::update($e, $force); }
            else { $e->nguoi_tao = $u; $res = DT_PhanCongGiangVien_BUS::insert($e, $force); }
            if ($res['success']) ResponseHelper::success($res['message'], $res['data'] ?? null);
            else ResponseHelper::error($res['message'], 400, !empty($res['data']) ? ['data' => $res['data']] : []);
            break;

        case 'bulkAssign':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_THEM);
            $monIds = isset($_POST['mon_hoc_ids']) && is_array($_POST['mon_hoc_ids']) ? $_POST['mon_hoc_ids'] : [];
            $res = DT_PhanCongGiangVien_BUS::bulkAssign(
                Helper::postInt('giang_vien_id'),
                Helper::postInt('lop_hoc_id'),
                $monIds,
                Helper::postInt('vai_tro', 1),
                $u
            );
            $res['success'] ? ResponseHelper::success($res['message'], $res['data'] ?? null) : ResponseHelper::error($res['message']);
            break;

        case 'delete':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XOA);
            $res = DT_PhanCongGiangVien_BUS::delete(Helper::postInt('id'));
            $res['success'] ? ResponseHelper::success($res['message']) : ResponseHelper::error($res['message']);
            break;

        default:
            ResponseHelper::error('Action không hợp lệ');
    }
} catch (Throwable $ex) {
    ResponseHelper::error(AppConfig::APP_DEBUG ? $ex->getMessage() : 'Lỗi hệ thống', 500);
}
