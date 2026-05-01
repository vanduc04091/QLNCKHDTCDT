<?php
require_once __DIR__ . '/../DAL/DM_NhomTaiKhoan_DAL.php';
require_once __DIR__ . '/../DAL/DM_PhanQuyen_DAL.php';
require_once __DIR__ . '/../DAL/DM_NhatKyHeThong_DAL.php';

class DM_NhomTaiKhoan_BUS
{
    const MODULE_KEY = 'DM_NhomTaiKhoan';

    public static function insert(DM_NhomTaiKhoan_PUBLIC $e): array
    {
        $e->ma_nhom = trim($e->ma_nhom);
        $e->ten_nhom = trim($e->ten_nhom);
        if ($e->ma_nhom === '' || $e->ten_nhom === '') {
            return ['success' => false, 'message' => 'Mã và tên nhóm không được để trống'];
        }
        if (DM_NhomTaiKhoan_DAL::checkMaExists($e->ma_nhom)) {
            return ['success' => false, 'message' => 'Mã nhóm đã tồn tại'];
        }
        $id = DM_NhomTaiKhoan_DAL::insert($e);
        MemcachedHelper::deleteByPrefix('dm_nhom_tai_khoan:');
        DM_NhatKyHeThong_DAL::log($e->nguoi_tao ?? 0, Constants::MODULE_HE_THONG, "Thêm nhóm TK: {$e->ten_nhom}", 'DM_NHOM_TAI_KHOAN', $id);
        return ['success' => true, 'message' => 'Thêm nhóm thành công', 'data' => ['id' => $id]];
    }

    public static function update(DM_NhomTaiKhoan_PUBLIC $e): array
    {
        if (!$e->id) return ['success' => false, 'message' => 'Thiếu ID'];
        if (DM_NhomTaiKhoan_DAL::checkMaExists($e->ma_nhom, $e->id)) {
            return ['success' => false, 'message' => 'Mã nhóm đã tồn tại'];
        }
        DM_NhomTaiKhoan_DAL::update($e);
        MemcachedHelper::deleteByPrefix('dm_nhom_tai_khoan:');
        PhanQuyenHelper::clearCache();
        return ['success' => true, 'message' => 'Cập nhật thành công'];
    }

    public static function trash(int $id, int $u): array
    {
        if ($id === 1) return ['success' => false, 'message' => 'Không thể xóa nhóm Admin'];
        DM_NhomTaiKhoan_DAL::trash($id, $u);
        MemcachedHelper::deleteByPrefix('dm_nhom_tai_khoan:');
        PhanQuyenHelper::clearCache($id);
        return ['success' => true, 'message' => 'Đã chuyển vào thùng rác'];
    }

    public static function restore(int $id, int $u): array
    {
        DM_NhomTaiKhoan_DAL::restore($id, $u);
        MemcachedHelper::deleteByPrefix('dm_nhom_tai_khoan:');
        return ['success' => true, 'message' => 'Đã khôi phục'];
    }

    public static function delete(int $id): array
    {
        if ($id === 1) return ['success' => false, 'message' => 'Không thể xóa nhóm Admin'];
        try {
            Database::beginTransaction();
            DM_PhanQuyen_DAL::deleteByNhom($id);
            DM_NhomTaiKhoan_DAL::delete($id);
            Database::commit();
        } catch (Throwable $ex) {
            Database::rollBack();
            return ['success' => false, 'message' => 'Lỗi xóa nhóm: ' . $ex->getMessage()];
        }
        MemcachedHelper::deleteByPrefix('dm_nhom_tai_khoan:');
        PhanQuyenHelper::clearCache($id);
        return ['success' => true, 'message' => 'Đã xóa vĩnh viễn'];
    }

    public static function getById(int $id): ?DM_NhomTaiKhoan_DTO
    {
        return DM_NhomTaiKhoan_DAL::getById($id);
    }

    public static function getPaged(int $page, int $pageSize, string $search = '', int $daXoa = 0): array
    {
        return DM_NhomTaiKhoan_DAL::getPaged($page, $pageSize, $search, $daXoa);
    }

    public static function getCombo(): array
    {
        return DM_NhomTaiKhoan_DAL::getCombo();
    }
}
