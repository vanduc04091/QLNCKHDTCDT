<?php
/**
 * Ajax handler — Module "Duyệt đề tài NCKH" cho quản trị viên.
 */
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../BUS/NCKH_DeTai_BUS.php';

Helper::requireAjaxCsrf();
$action = Helper::post('action', '');
$u = SessionHelper::userId();
$MODULE = 'NCKH_DuyetDeTai';
PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XEM);

try {
    switch ($action) {
        case 'getQueue':
            $page = Helper::postInt('page', 1);
            $size = Helper::postInt('pageSize', AppConfig::DEFAULT_PAGE_SIZE);
            $tab  = Helper::postStr('tab') ?: 'cho';
            $tabMap = [
                'cho'    => ['ChoDuyet'],
                'duyet'  => ['DaDuyet'],
                'tuchoi' => ['TuChoi'],
                'all'    => ['ChoDuyet','DaDuyet','TuChoi'],
            ];
            $filters = [
                'search'             => Helper::postStr('search'),
                'nam'                => Helper::postInt('nam'),
                'trang_thai_duyet_in' => $tabMap[$tab] ?? $tabMap['cho'],
            ];
            $res = NCKH_DeTai_BUS::getPaged($page, $size, $filters, 0);
            ResponseHelper::paged($res['data'], $page, $size, $res['totalRecords']);
            break;

        case 'getCounts':
            $pdo = Database::getConnection();
            $rows = $pdo->query("SELECT trang_thai_duyet, COUNT(*) AS sl FROM NCKH_DE_TAI WHERE da_xoa=0 AND trang_thai_duyet IN ('ChoDuyet','DaDuyet','TuChoi') GROUP BY trang_thai_duyet")->fetchAll();
            $counts = ['ChoDuyet'=>0,'DaDuyet'=>0,'TuChoi'=>0];
            foreach ($rows as $r) $counts[$r['trang_thai_duyet']] = (int)$r['sl'];
            ResponseHelper::success('OK', $counts);
            break;

        case 'getDetail':
            $id = Helper::postInt('id');
            $res = NCKH_DeTai_BUS::getDetail($id);
            $res['success'] ? ResponseHelper::success('OK', $res['data']) : ResponseHelper::error($res['message']);
            break;

        case 'approve':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_SUA);
            $id = Helper::postInt('id');
            $res = NCKH_DeTai_BUS::approveSubmission($id, $u);
            $res['success'] ? ResponseHelper::success($res['message']) : ResponseHelper::error($res['message']);
            break;

        case 'reject':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_SUA);
            $id = Helper::postInt('id');
            $lyDo = Helper::postStr('ly_do');
            $res = NCKH_DeTai_BUS::rejectSubmission($id, $u, $lyDo);
            $res['success'] ? ResponseHelper::success($res['message']) : ResponseHelper::error($res['message']);
            break;

        default:
            ResponseHelper::error('Action không hợp lệ');
    }
} catch (Throwable $ex) {
    ResponseHelper::error(AppConfig::APP_DEBUG ? $ex->getMessage() : 'Lỗi hệ thống', 500);
}
