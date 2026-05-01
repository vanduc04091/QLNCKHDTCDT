<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../BUS/NCKH_NhacViec_BUS.php';
require_once __DIR__ . '/../../BUS/NCKH_DeTai_BUS.php';
require_once __DIR__ . '/../../BUS/DM_NhanVien_BUS.php';

Helper::requireAjaxCsrf();
$action = Helper::post('action', '');
$u = SessionHelper::userId();
$MODULE = 'NCKH_NhacViec';

try {
    switch ($action) {
        case 'getPaged':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XEM);
            $page = Helper::postInt('page', 1);
            $size = Helper::postInt('pageSize', AppConfig::DEFAULT_PAGE_SIZE);
            $daGui = Helper::post('da_gui', '');
            $daGui = $daGui === '' ? -1 : (int)$daGui;
            $daXoa = Helper::postInt('da_xoa', 0);
            $res = NCKH_NhacViec_BUS::getPaged($page, $size, $daGui, $daXoa);
            ResponseHelper::paged($res['data'], $page, $size, $res['totalRecords']);
            break;

        case 'getById':
            $e = NCKH_NhacViec_BUS::getById(Helper::postInt('id'));
            $e ? ResponseHelper::success('OK', $e) : ResponseHelper::error('Không tìm thấy');
            break;

        case 'insert':
        case 'update':
            $isU = $action === 'update';
            PhanQuyenHelper::requireQuyen($MODULE, $isU ? PhanQuyenHelper::QUYEN_SUA : PhanQuyenHelper::QUYEN_THEM);
            $e = new NCKH_NhacViec_PUBLIC();
            if ($isU) $e->id = Helper::postInt('id');
            $e->de_tai_id = Helper::postInt('de_tai_id');
            $e->loai_nhac = Helper::postStr('loai_nhac') ?: 'TienDo';
            $e->tieu_de = Helper::postStr('tieu_de');
            $e->noi_dung = Helper::postStr('noi_dung') ?: null;
            $e->ngay_nhac = Helper::postStr('ngay_nhac');
            $e->nguoi_nhan_id = Helper::postInt('nguoi_nhan_id') ?: null;
            if ($isU) { $e->nguoi_cap_nhat = $u; $res = NCKH_NhacViec_BUS::update($e); }
            else      { $e->nguoi_tao = $u;     $res = NCKH_NhacViec_BUS::insert($e); }
            $res['success'] ? ResponseHelper::success($res['message'], $res['data'] ?? null) : ResponseHelper::error($res['message']);
            break;

        case 'trash':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XOA);
            $res = NCKH_NhacViec_BUS::trash(Helper::postInt('id'), $u);
            ResponseHelper::success($res['message']);
            break;

        case 'getComboDeTai':
            $stmt = Database::getConnection()->query("SELECT id, ma_de_tai, ten_de_tai FROM NCKH_DE_TAI WHERE da_xoa=0 ORDER BY nam DESC, id DESC LIMIT 500");
            ResponseHelper::success('OK', $stmt->fetchAll());
            break;

        case 'getComboNhanVien':
            $kw = Helper::postStr('kw');
            $r = DM_NhanVien_BUS::getPaged(1, 50, $kw, 0, 0);
            ResponseHelper::success('OK', $r['data']);
            break;

        default:
            ResponseHelper::error('Action không hợp lệ');
    }
} catch (Throwable $ex) {
    ResponseHelper::error(AppConfig::APP_DEBUG ? $ex->getMessage() : 'Lỗi hệ thống', 500);
}
