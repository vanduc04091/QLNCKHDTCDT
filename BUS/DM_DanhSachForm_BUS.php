<?php
require_once __DIR__ . '/../DAL/DM_DanhSachForm_DAL.php';
require_once __DIR__ . '/../DAL/DM_NhatKyHeThong_DAL.php';

class DM_DanhSachForm_BUS
{
    const MODULE_KEY = 'DM_DanhSachForm';

    public static function insert(DM_DanhSachForm_PUBLIC $e): array
    {
        $e->modules_tuong_ung = trim($e->modules_tuong_ung);
        $e->ten_form = trim($e->ten_form);
        if ($e->modules_tuong_ung === '' || $e->ten_form === '') {
            return ['success' => false, 'message' => 'Module và tên form không được để trống'];
        }
        if (DM_DanhSachForm_DAL::checkModuleExists($e->modules_tuong_ung)) {
            return ['success' => false, 'message' => 'Module này đã tồn tại'];
        }
        $id = DM_DanhSachForm_DAL::insert($e);
        MemcachedHelper::deleteByPrefix('dm_danh_sach_form:');
        PhanQuyenHelper::clearCache();
        DM_NhatKyHeThong_DAL::log($e->nguoi_tao ?? 0, Constants::MODULE_HE_THONG, "Thêm form: {$e->ten_form}", 'DM_DANH_SACH_FORM', $id);
        return ['success' => true, 'message' => 'Thêm form thành công', 'data' => ['id' => $id]];
    }

    public static function update(DM_DanhSachForm_PUBLIC $e): array
    {
        if (!$e->id) return ['success' => false, 'message' => 'Thiếu ID'];
        if (DM_DanhSachForm_DAL::checkModuleExists($e->modules_tuong_ung, $e->id)) {
            return ['success' => false, 'message' => 'Module này đã tồn tại'];
        }
        DM_DanhSachForm_DAL::update($e);
        MemcachedHelper::deleteByPrefix('dm_danh_sach_form:');
        PhanQuyenHelper::clearCache();
        return ['success' => true, 'message' => 'Cập nhật thành công'];
    }

    public static function trash(int $id, int $u): array
    {
        DM_DanhSachForm_DAL::trash($id, $u);
        MemcachedHelper::deleteByPrefix('dm_danh_sach_form:');
        PhanQuyenHelper::clearCache();
        return ['success' => true, 'message' => 'Đã chuyển vào thùng rác'];
    }

    public static function restore(int $id, int $u): array
    {
        DM_DanhSachForm_DAL::restore($id, $u);
        MemcachedHelper::deleteByPrefix('dm_danh_sach_form:');
        PhanQuyenHelper::clearCache();
        return ['success' => true, 'message' => 'Đã khôi phục'];
    }

    public static function delete(int $id): array
    {
        try {
            DM_DanhSachForm_DAL::delete($id);
            MemcachedHelper::deleteByPrefix('dm_danh_sach_form:');
            PhanQuyenHelper::clearCache();
            return ['success' => true, 'message' => 'Đã xóa vĩnh viễn'];
        } catch (Throwable $ex) {
            return ['success' => false, 'message' => 'Không thể xóa: form đang được phân quyền'];
        }
    }

    public static function getById(int $id): ?DM_DanhSachForm_DTO
    {
        return DM_DanhSachForm_DAL::getById($id);
    }

    public static function getAll(int $daXoa = 0): array
    {
        return DM_DanhSachForm_DAL::getAll($daXoa);
    }

    public static function getPaged(int $page, int $pageSize, string $search = '', int $daXoa = 0): array
    {
        return DM_DanhSachForm_DAL::getPaged($page, $pageSize, $search, $daXoa);
    }
}
