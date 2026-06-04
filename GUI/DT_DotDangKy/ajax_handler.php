<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../BUS/DT_DotDangKy_BUS.php';
require_once __DIR__ . '/../../BUS/DT_DotGiaiDoan_BUS.php';

Helper::requireAjaxCsrf();

$action = Helper::post('action', '');
$u = SessionHelper::userId();
$MODULE = DT_DotDangKy_BUS::MODULE_KEY;

try {
    switch ($action) {
        case 'getPaged':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XEM);
            $page = Helper::postInt('page', 1);
            $size = Helper::postInt('pageSize', AppConfig::DEFAULT_PAGE_SIZE);
            $opts = [
                'kw' => Helper::postStr('kw'),
                'nam' => Helper::postInt('nam', 0) ?: null,
                'trang_thai' => Helper::post('trang_thai', ''),
            ];
            $res = DT_DotDangKy_BUS::getPaged($page, $size, array_filter($opts, function ($v) { return $v !== '' && $v !== null; }));
            ResponseHelper::paged($res['data'], $page, $size, $res['totalRecords']);
            break;

        case 'getById':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XEM);
            $e = DT_DotDangKy_BUS::getById(Helper::postInt('id'));
            if (!$e) ResponseHelper::error('Không tìm thấy');
            ResponseHelper::success('OK', $e);
            break;

        case 'getCombo':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XEM);
            $only = Helper::postInt('only_active', 1) === 1;
            ResponseHelper::success('OK', DT_DotDangKy_BUS::getCombo($only));
            break;

        case 'insert':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_THEM);
            $e = new DT_DotDangKy_PUBLIC();
            $e->ten_dot = Helper::postStr('ten_dot');
            $e->nam = Helper::postInt('nam', (int)date('Y'));
            $e->tu_ngay = Helper::postStr('tu_ngay');
            $e->den_ngay = Helper::postStr('den_ngay');
            $e->mo_ta = Helper::postStr('mo_ta') ?: null;
            $e->trang_thai = Helper::postInt('trang_thai', 1);
            $e->nguoi_tao = $u;
            $res = DT_DotDangKy_BUS::insert($e);
            $res['success'] ? ResponseHelper::success($res['message'], $res['data'] ?? null) : ResponseHelper::error($res['message']);
            break;

        case 'update':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_SUA);
            $e = new DT_DotDangKy_PUBLIC();
            $e->id = Helper::postInt('id');
            $e->ten_dot = Helper::postStr('ten_dot');
            $e->nam = Helper::postInt('nam', (int)date('Y'));
            $e->tu_ngay = Helper::postStr('tu_ngay');
            $e->den_ngay = Helper::postStr('den_ngay');
            $e->mo_ta = Helper::postStr('mo_ta') ?: null;
            $e->trang_thai = Helper::postInt('trang_thai', 1);
            $e->nguoi_cap_nhat = $u;
            $res = DT_DotDangKy_BUS::update($e);
            $res['success'] ? ResponseHelper::success($res['message']) : ResponseHelper::error($res['message']);
            break;

        case 'trash':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XOA);
            $res = DT_DotDangKy_BUS::trash(Helper::postInt('id'), $u);
            $res['success'] ? ResponseHelper::success($res['message']) : ResponseHelper::error($res['message']);
            break;

        case 'getPhases':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XEM);
            ResponseHelper::success('OK', DT_DotGiaiDoan_BUS::getByDot(Helper::postInt('dot_id')));
            break;

        case 'getPhaseById':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XEM);
            $gd = DT_DotGiaiDoan_BUS::getById(Helper::postInt('id'));
            if (!$gd) ResponseHelper::error('Không tìm thấy giai đoạn');
            ResponseHelper::success('OK', $gd);
            break;

        case 'insertPhase':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_THEM);
            $e = new DT_DotGiaiDoan_PUBLIC();
            $e->dot_id = Helper::postInt('dot_id');
            $e->ten_giai_doan = Helper::postStr('ten_giai_doan');
            $e->hanh_vi = Helper::postStr('hanh_vi');
            $e->tu_ngay = str_replace('T', ' ', Helper::postStr('tu_ngay'));
            $e->den_ngay = str_replace('T', ' ', Helper::postStr('den_ngay'));
            $e->thu_tu = Helper::postInt('thu_tu', 0);
            $e->ghi_chu = Helper::postStr('ghi_chu') ?: null;
            $e->nguoi_tao = $u;
            $res = DT_DotGiaiDoan_BUS::insert($e);
            $res['success'] ? ResponseHelper::success($res['message'], $res['data'] ?? null) : ResponseHelper::error($res['message']);
            break;

        case 'updatePhase':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_SUA);
            $e = new DT_DotGiaiDoan_PUBLIC();
            $e->id = Helper::postInt('id');
            $e->dot_id = Helper::postInt('dot_id');
            $e->ten_giai_doan = Helper::postStr('ten_giai_doan');
            $e->hanh_vi = Helper::postStr('hanh_vi');
            $e->tu_ngay = str_replace('T', ' ', Helper::postStr('tu_ngay'));
            $e->den_ngay = str_replace('T', ' ', Helper::postStr('den_ngay'));
            $e->thu_tu = Helper::postInt('thu_tu', 0);
            $e->ghi_chu = Helper::postStr('ghi_chu') ?: null;
            $e->nguoi_cap_nhat = $u;
            $res = DT_DotGiaiDoan_BUS::update($e);
            $res['success'] ? ResponseHelper::success($res['message']) : ResponseHelper::error($res['message']);
            break;

        case 'trashPhase':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XOA);
            $res = DT_DotGiaiDoan_BUS::trash(Helper::postInt('id'), $u);
            $res['success'] ? ResponseHelper::success($res['message']) : ResponseHelper::error($res['message']);
            break;

        default:
            ResponseHelper::error('Action không hợp lệ');
    }
} catch (Throwable $ex) {
    ResponseHelper::error(AppConfig::APP_DEBUG ? $ex->getMessage() : 'Lỗi hệ thống', 500);
}
