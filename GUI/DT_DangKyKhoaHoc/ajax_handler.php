<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../BUS/DT_DangKyKhoaHoc_BUS.php';
require_once __DIR__ . '/../../BUS/DT_KhoaHocChuongTrinh_BUS.php';

Helper::requireAjaxCsrf();

$action = Helper::post('action', '');
$u      = SessionHelper::userId();
$MODULE = DT_DangKyKhoaHoc_BUS::MODULE_KEY;

try {
    switch ($action) {
        case 'getPaged':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XEM);
            $opts = [
                'search'      => Helper::postStr('search'),
                'trang_thai'  => Helper::postStr('trang_thai'),
                'khoa_hoc_id' => Helper::postInt('khoa_hoc_id'),
            ];
            $res = DT_DangKyKhoaHoc_BUS::getPaged(
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
            $e = DT_DangKyKhoaHoc_BUS::getById(Helper::postInt('id'));
            if (!$e) ResponseHelper::error('Không tìm thấy');
            ResponseHelper::success('OK', $e);
            break;

        case 'getStats':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XEM);
            ResponseHelper::success('OK', DT_DangKyKhoaHoc_BUS::getStats());
            break;

        case 'getLopByKhoa':
            // Lấy danh sách CTĐT gắn với 1 khóa học (cho dropdown khi duyệt). value = khct.id
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_SUA);
            $khoaHocId = Helper::postInt('khoa_hoc_id');
            ResponseHelper::success('OK', DT_KhoaHocChuongTrinh_BUS::getByKhoaHoc($khoaHocId));
            break;

        case 'approve':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_SUA);
            $id          = Helper::postInt('id');
            $lopId       = Helper::postInt('lop_hoc_id') ?: null;
            $note        = Helper::postStr('ghi_chu') ?: null;
            $existingHv  = Helper::postInt('existing_hv_id');
            $laNv        = Helper::postInt('la_nhan_vien') === 1;
            $nvId        = Helper::postInt('nhan_vien_id');
            $res = DT_DangKyKhoaHoc_BUS::approve($id, $u, $lopId, $note, $existingHv, $laNv, $nvId);
            $res['success']
                ? ResponseHelper::success($res['message'], $res['data'] ?? null)
                : ResponseHelper::error($res['message']);
            break;

        case 'scanDuplicates':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_SUA);
            $res = DT_DangKyKhoaHoc_BUS::scanDuplicates(Helper::postInt('id'));
            $res['success'] ? ResponseHelper::success('OK', $res['data']) : ResponseHelper::error($res['message']);
            break;

        case 'getNhanVienCombo':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_SUA);
            require_once __DIR__ . '/../../BUS/DM_NhanVien_BUS.php';
            // Trả combo NV để admin chọn khi flag "là nhân viên"
            $rows = DM_NhanVien_BUS::getCombo();
            ResponseHelper::success('OK', $rows);
            break;

        case 'reject':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_SUA);
            $id   = Helper::postInt('id');
            $note = Helper::postStr('ly_do');
            $res = DT_DangKyKhoaHoc_BUS::reject($id, $u, $note);
            $res['success']
                ? ResponseHelper::success($res['message'])
                : ResponseHelper::error($res['message']);
            break;

        case 'trash':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XOA);
            $res = DT_DangKyKhoaHoc_BUS::trash(Helper::postInt('id'), $u);
            $res['success'] ? ResponseHelper::success($res['message']) : ResponseHelper::error($res['message']);
            break;

        default:
            ResponseHelper::error('Action không hợp lệ');
    }
} catch (Throwable $ex) {
    ResponseHelper::error(AppConfig::APP_DEBUG ? $ex->getMessage() : 'Lỗi hệ thống', 500);
}
