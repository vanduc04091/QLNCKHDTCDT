<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../BUS/DM_GiangVien_BUS.php';
require_once __DIR__ . '/../../BUS/DM_NhanVien_BUS.php';

Helper::requireAjaxCsrf();

$action = Helper::post('action', '');
$u = SessionHelper::userId();
$MODULE = DM_GiangVien_BUS::MODULE_KEY;

try {
    switch ($action) {
        case 'getPaged':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XEM);
            $res = DM_GiangVien_BUS::getPaged(
                Helper::postInt('page', 1),
                Helper::postInt('pageSize', AppConfig::DEFAULT_PAGE_SIZE),
                Helper::postStr('search'),
                Helper::postInt('da_xoa', 0),
                Helper::postInt('loai_gv', 0),
                isset($_POST['trang_thai']) && $_POST['trang_thai'] !== '' ? Helper::postInt('trang_thai', -1) : -1
            );
            ResponseHelper::paged($res['data'], Helper::postInt('page', 1), Helper::postInt('pageSize', AppConfig::DEFAULT_PAGE_SIZE), $res['totalRecords']);
            break;

        case 'getById':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XEM);
            $e = DM_GiangVien_BUS::getById(Helper::postInt('id'));
            if (!$e) ResponseHelper::error('Không tìm thấy');
            ResponseHelper::success('OK', $e);
            break;

        case 'getDetail':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XEM);
            $gv = DM_GiangVien_BUS::getById(Helper::postInt('id'));
            if (!$gv) ResponseHelper::error('Không tìm thấy');
            ResponseHelper::success('OK', [
                'gv' => $gv,
                'phan_cong' => DM_GiangVien_BUS::getPhanCongByGV($gv->id),
            ]);
            break;

        case 'getStats':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XEM);
            ResponseHelper::success('OK', DM_GiangVien_BUS::getStats());
            break;

        case 'getComboNhanVien':
            ResponseHelper::success('OK', DM_NhanVien_BUS::getCombo());
            break;

        case 'insert':
        case 'update':
            $isUpdate = $action === 'update';
            PhanQuyenHelper::requireQuyen($MODULE, $isUpdate ? PhanQuyenHelper::QUYEN_SUA : PhanQuyenHelper::QUYEN_THEM);
            $e = new DM_GiangVien_PUBLIC();
            if ($isUpdate) $e->id = Helper::postInt('id');
            $e->ma_gv = Helper::postStr('ma_gv');
            $e->ho_ten = Helper::postStr('ho_ten');
            $e->ngay_sinh = Helper::postStr('ngay_sinh') ?: null;
            $e->gioi_tinh = Helper::postStr('gioi_tinh') ?: null;
            $e->email = Helper::postStr('email') ?: null;
            $e->dien_thoai = Helper::postStr('dien_thoai') ?: null;
            $e->avatar = Helper::postStr('avatar') ?: null;
            $e->hoc_vi = Helper::postStr('hoc_vi') ?: null;
            $e->hoc_ham = Helper::postStr('hoc_ham') ?: null;
            $e->chuyen_mon = Helper::postStr('chuyen_mon') ?: null;
            $e->nhan_vien_id = Helper::postInt('nhan_vien_id') ?: null;
            $e->don_vi_cong_tac = Helper::postStr('don_vi_cong_tac') ?: null;
            $e->loai_gv = Helper::postInt('loai_gv', 1);
            $e->trang_thai = Helper::postInt('trang_thai', 1);
            $e->ghi_chu = Helper::postStr('ghi_chu') ?: null;
            if ($isUpdate) { $e->nguoi_cap_nhat = $u; $res = DM_GiangVien_BUS::update($e); }
            else { $e->nguoi_tao = $u; $res = DM_GiangVien_BUS::insert($e); }
            $res['success'] ? ResponseHelper::success($res['message'], $res['data'] ?? null) : ResponseHelper::error($res['message']);
            break;

        case 'trash':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XOA);
            $res = DM_GiangVien_BUS::trash(Helper::postInt('id'), $u);
            $res['success'] ? ResponseHelper::success($res['message']) : ResponseHelper::error($res['message']);
            break;

        case 'restore':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_SUA);
            $res = DM_GiangVien_BUS::restore(Helper::postInt('id'), $u);
            $res['success'] ? ResponseHelper::success($res['message']) : ResponseHelper::error($res['message']);
            break;

        case 'delete':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XOA);
            $res = DM_GiangVien_BUS::delete(Helper::postInt('id'));
            $res['success'] ? ResponseHelper::success($res['message']) : ResponseHelper::error($res['message']);
            break;

        default:
            ResponseHelper::error('Action không hợp lệ');
    }
} catch (Throwable $ex) {
    ResponseHelper::error(AppConfig::APP_DEBUG ? $ex->getMessage() : 'Lỗi hệ thống', 500);
}
