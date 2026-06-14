<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../DAL/DM_NhatKyHeThong_DAL.php';

Helper::requireAjaxCsrf();

$action = Helper::post('action', '');
$u = SessionHelper::userId();
const MODULE_KEY = 'DM_XoaCache';

try {
    switch ($action) {
        case 'clearAll':
            PhanQuyenHelper::requireQuyen(MODULE_KEY, PhanQuyenHelper::QUYEN_XOA);
            MemcachedHelper::flush();
            DM_NhatKyHeThong_DAL::log($u, Constants::MODULE_HE_THONG, 'Xóa toàn bộ cache hệ thống', 'CACHE', 0);
            ResponseHelper::success('Đã xóa toàn bộ cache');
            break;

        case 'clearPhanQuyen':
            PhanQuyenHelper::requireQuyen(MODULE_KEY, PhanQuyenHelper::QUYEN_XOA);
            PhanQuyenHelper::clearCache();
            DM_NhatKyHeThong_DAL::log($u, Constants::MODULE_HE_THONG, 'Xóa cache phân quyền', 'CACHE', 0);
            ResponseHelper::success('Đã xóa cache phân quyền');
            break;

        case 'clearCombo':
            PhanQuyenHelper::requireQuyen(MODULE_KEY, PhanQuyenHelper::QUYEN_XOA);
            // Các combo danh mục dùng prefix riêng — xóa hết các prefix combo đã biết.
            foreach ([
                'dt_mon_hoc:', 'dt_chuong_trinh:', 'dm_doi_tuong_hoc_vien:',
                'dm_khoa_phong:', 'dm_nhan_vien:', 'dt_khoa_hoc:',
            ] as $prefix) {
                MemcachedHelper::deleteByPrefix($prefix);
            }
            DM_NhatKyHeThong_DAL::log($u, Constants::MODULE_HE_THONG, 'Xóa cache danh mục/combo', 'CACHE', 0);
            ResponseHelper::success('Đã xóa cache danh mục');
            break;

        default:
            ResponseHelper::error('Action không hợp lệ');
    }
} catch (Throwable $ex) {
    ResponseHelper::error(AppConfig::APP_DEBUG ? $ex->getMessage() : 'Lỗi hệ thống', 500);
}
