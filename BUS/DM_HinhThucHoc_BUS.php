<?php
require_once __DIR__ . '/../DAL/DM_HinhThucHoc_DAL.php';
require_once __DIR__ . '/../DAL/DM_NhatKyHeThong_DAL.php';

class DM_HinhThucHoc_BUS
{
    const MODULE_KEY = 'DM_HinhThucHoc';

    public static function insert(DM_HinhThucHoc_PUBLIC $e): array
    {
        $e->ma_hinh_thuc = trim($e->ma_hinh_thuc);
        $e->ten_hinh_thuc = trim($e->ten_hinh_thuc);
        if ($e->ma_hinh_thuc === '' || $e->ten_hinh_thuc === '') {
            return ['success' => false, 'message' => 'Mã và tên hình thức không được để trống'];
        }
        if (DM_HinhThucHoc_DAL::checkMaExists($e->ma_hinh_thuc)) {
            return ['success' => false, 'message' => 'Mã hình thức đã tồn tại'];
        }
        $id = DM_HinhThucHoc_DAL::insert($e);
        MemcachedHelper::deleteByPrefix('dm_hinh_thuc_hoc:');
        DM_NhatKyHeThong_DAL::log($e->nguoi_tao ?? 0, self::MODULE_KEY, "Thêm hình thức: {$e->ten_hinh_thuc}", 'DM_HINH_THUC_HOC', $id);
        return ['success' => true, 'message' => 'Thêm thành công', 'data' => ['id' => $id]];
    }

    public static function update(DM_HinhThucHoc_PUBLIC $e): array
    {
        if (!$e->id) return ['success' => false, 'message' => 'Thiếu ID'];
        if (DM_HinhThucHoc_DAL::checkMaExists($e->ma_hinh_thuc, $e->id)) {
            return ['success' => false, 'message' => 'Mã hình thức đã tồn tại'];
        }
        DM_HinhThucHoc_DAL::update($e);
        MemcachedHelper::deleteByPrefix('dm_hinh_thuc_hoc:');
        DM_NhatKyHeThong_DAL::log($e->nguoi_cap_nhat ?? 0, self::MODULE_KEY, "Cập nhật hình thức: {$e->ten_hinh_thuc}", 'DM_HINH_THUC_HOC', $e->id);
        return ['success' => true, 'message' => 'Cập nhật thành công'];
    }

    public static function trash(int $id, int $u): array
    {
        DM_HinhThucHoc_DAL::trash($id, $u);
        MemcachedHelper::deleteByPrefix('dm_hinh_thuc_hoc:');
        return ['success' => true, 'message' => 'Đã chuyển vào thùng rác'];
    }

    public static function restore(int $id, int $u): array
    {
        DM_HinhThucHoc_DAL::restore($id, $u);
        MemcachedHelper::deleteByPrefix('dm_hinh_thuc_hoc:');
        return ['success' => true, 'message' => 'Đã khôi phục'];
    }

    public static function delete(int $id): array
    {
        DM_HinhThucHoc_DAL::delete($id);
        MemcachedHelper::deleteByPrefix('dm_hinh_thuc_hoc:');
        return ['success' => true, 'message' => 'Đã xóa vĩnh viễn'];
    }

    public static function getById(int $id): ?DM_HinhThucHoc_DTO
    {
        return DM_HinhThucHoc_DAL::getById($id);
    }

    public static function getPaged(int $page, int $pageSize, string $search = '', int $daXoa = 0): array
    {
        return DM_HinhThucHoc_DAL::getPaged($page, $pageSize, $search, $daXoa);
    }

    public static function getCombo(): array
    {
        return DM_HinhThucHoc_DAL::getCombo();
    }
}
