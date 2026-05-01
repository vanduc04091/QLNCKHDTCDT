<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../BUS/DT_LichHoc_BUS.php';
require_once __DIR__ . '/../../BUS/DT_LopHoc_BUS.php';
require_once __DIR__ . '/../../BUS/DM_NhanVien_BUS.php';
require_once __DIR__ . '/../../DAL/DT_MonHoc_DAL.php';

Helper::requireAjaxCsrf();

$action = Helper::post('action', '');
$u = SessionHelper::userId();
$MODULE = DT_LichHoc_BUS::MODULE_KEY;

try {
    switch ($action) {
        case 'getRange':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XEM);
            $opts = [
                'lop_hoc_id' => Helper::postInt('lop_hoc_id'),
                'giang_vien_id' => Helper::postInt('giang_vien_id'),
                'trang_thai' => isset($_POST['trang_thai']) && $_POST['trang_thai'] !== '' ? Helper::postInt('trang_thai', -1) : -1,
                'search' => Helper::postStr('search'),
            ];
            $data = DT_LichHoc_BUS::getByRange(Helper::postStr('from'), Helper::postStr('to'), $opts);
            ResponseHelper::success('OK', $data);
            break;

        case 'getPaged':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XEM);
            $opts = [
                'lop_hoc_id' => Helper::postInt('lop_hoc_id'),
                'giang_vien_id' => Helper::postInt('giang_vien_id'),
                'trang_thai' => isset($_POST['trang_thai']) && $_POST['trang_thai'] !== '' ? Helper::postInt('trang_thai', -1) : -1,
                'from' => Helper::postStr('from') ?: null,
                'to' => Helper::postStr('to') ?: null,
                'search' => Helper::postStr('search'),
            ];
            $res = DT_LichHoc_BUS::getPaged(
                Helper::postInt('page', 1),
                Helper::postInt('pageSize', AppConfig::DEFAULT_PAGE_SIZE),
                $opts,
                Helper::postInt('da_xoa', 0)
            );
            ResponseHelper::paged($res['data'], Helper::postInt('page', 1), Helper::postInt('pageSize', AppConfig::DEFAULT_PAGE_SIZE), $res['totalRecords']);
            break;

        case 'getById':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XEM);
            $e = DT_LichHoc_BUS::getById(Helper::postInt('id'));
            if (!$e) ResponseHelper::error('Không tìm thấy');
            ResponseHelper::success('OK', $e);
            break;

        case 'getStats':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XEM);
            ResponseHelper::success('OK', DT_LichHoc_BUS::getStats(Helper::postStr('from'), Helper::postStr('to')));
            break;

        case 'getComboLop':
            ResponseHelper::success('OK', DT_LopHoc_BUS::getPaged(1, 500, '', 0, 0, -1)['data']);
            break;

        case 'getComboNhanVien':
            ResponseHelper::success('OK', DM_NhanVien_BUS::getCombo());
            break;

        case 'getComboMonHoc':
            // Danh sách môn học cơ bản (không phân trang)
            $stmt = Database::getConnection()->prepare("SELECT id, ma_mon_hoc, ten_mon_hoc FROM DT_MON_HOC WHERE da_xoa=0 ORDER BY ten_mon_hoc ASC");
            $stmt->execute();
            ResponseHelper::success('OK', $stmt->fetchAll());
            break;

        case 'insert':
        case 'update':
            $isUpdate = $action === 'update';
            PhanQuyenHelper::requireQuyen($MODULE, $isUpdate ? PhanQuyenHelper::QUYEN_SUA : PhanQuyenHelper::QUYEN_THEM);
            $e = new DT_LichHoc_PUBLIC();
            if ($isUpdate) $e->id = Helper::postInt('id');
            $e->lop_hoc_id = Helper::postInt('lop_hoc_id');
            $e->buoi_thu = Helper::postInt('buoi_thu', 0);
            $e->tieu_de = Helper::postStr('tieu_de');
            $e->noi_dung = Helper::postStr('noi_dung') ?: null;
            $e->mon_hoc_id = Helper::postInt('mon_hoc_id') ?: null;
            $e->ngay_hoc = Helper::postStr('ngay_hoc');
            $e->gio_bat_dau = Helper::postStr('gio_bat_dau');
            $e->gio_ket_thuc = Helper::postStr('gio_ket_thuc');
            $e->phong_hoc = Helper::postStr('phong_hoc') ?: null;
            $e->giang_vien_id = Helper::postInt('giang_vien_id') ?: null;
            $e->giang_vien_ngoai = Helper::postStr('giang_vien_ngoai') ?: null;
            $e->trang_thai = Helper::postInt('trang_thai', 0);
            $e->ghi_chu = Helper::postStr('ghi_chu') ?: null;
            $force = Helper::postInt('force_conflict', 0) === 1;
            if ($isUpdate) { $e->nguoi_cap_nhat = $u; $res = DT_LichHoc_BUS::update($e, $force); }
            else { $e->nguoi_tao = $u; $res = DT_LichHoc_BUS::insert($e, $force); }
            if ($res['success']) ResponseHelper::success($res['message'], $res['data'] ?? null);
            else ResponseHelper::error($res['message'], 400, !empty($res['data']) ? ['data' => $res['data']] : []);
            break;

        case 'updateTrangThai':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_SUA);
            $res = DT_LichHoc_BUS::updateTrangThai(Helper::postInt('id'), Helper::postInt('trang_thai'), $u);
            $res['success'] ? ResponseHelper::success($res['message']) : ResponseHelper::error($res['message']);
            break;

        case 'bulkGenerate':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_THEM);
            $weekdays = isset($_POST['weekdays']) && is_array($_POST['weekdays'])
                ? array_map('intval', $_POST['weekdays']) : [];
            $tpl = [
                'tieu_de' => Helper::postStr('tieu_de'),
                'noi_dung' => Helper::postStr('noi_dung'),
                'gio_bat_dau' => Helper::postStr('gio_bat_dau'),
                'gio_ket_thuc' => Helper::postStr('gio_ket_thuc'),
                'phong_hoc' => Helper::postStr('phong_hoc'),
                'giang_vien_id' => Helper::postInt('giang_vien_id') ?: null,
                'giang_vien_ngoai' => Helper::postStr('giang_vien_ngoai'),
                'mon_hoc_id' => Helper::postInt('mon_hoc_id') ?: null,
            ];
            $res = DT_LichHoc_BUS::bulkGenerate(
                Helper::postInt('lop_hoc_id'),
                $tpl,
                Helper::postStr('from'), Helper::postStr('to'),
                Helper::postStr('pattern'), $weekdays,
                $u,
                Helper::postInt('force_conflict', 0) === 1
            );
            $res['success'] ? ResponseHelper::success($res['message'], $res['data'] ?? null) : ResponseHelper::error($res['message']);
            break;

        case 'trash':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XOA);
            $res = DT_LichHoc_BUS::trash(Helper::postInt('id'), $u);
            $res['success'] ? ResponseHelper::success($res['message']) : ResponseHelper::error($res['message']);
            break;

        case 'restore':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_SUA);
            $res = DT_LichHoc_BUS::restore(Helper::postInt('id'), $u);
            $res['success'] ? ResponseHelper::success($res['message']) : ResponseHelper::error($res['message']);
            break;

        case 'delete':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XOA);
            $res = DT_LichHoc_BUS::delete(Helper::postInt('id'));
            $res['success'] ? ResponseHelper::success($res['message']) : ResponseHelper::error($res['message']);
            break;

        default:
            ResponseHelper::error('Action không hợp lệ');
    }
} catch (Throwable $ex) {
    ResponseHelper::error(AppConfig::APP_DEBUG ? $ex->getMessage() : 'Lỗi hệ thống', 500);
}
