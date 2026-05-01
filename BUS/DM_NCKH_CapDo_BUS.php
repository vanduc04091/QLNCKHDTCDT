<?php
require_once __DIR__ . '/../DAL/DM_NCKH_CapDo_DAL.php';
require_once __DIR__ . '/../DAL/DM_NhatKyHeThong_DAL.php';

class DM_NCKH_CapDo_BUS
{
    const MODULE_KEY = 'DM_NCKH_CapDo';

    public static function insert(DM_NCKH_CapDo_PUBLIC $e): array
    {
        $e->ma_cap_do = trim($e->ma_cap_do);
        $e->ten_cap_do = trim($e->ten_cap_do);
        if ($e->ma_cap_do === '' || $e->ten_cap_do === '') {
            return ['success' => false, 'message' => 'Mã và tên cấp độ không được để trống'];
        }
        if (DM_NCKH_CapDo_DAL::checkMaExists($e->ma_cap_do)) {
            return ['success' => false, 'message' => 'Mã cấp độ đã tồn tại'];
        }
        $id = DM_NCKH_CapDo_DAL::insert($e);
        MemcachedHelper::deleteByPrefix('dm_nckh_cap_do:');
        DM_NhatKyHeThong_DAL::log($e->nguoi_tao ?? 0, self::MODULE_KEY, "Thêm cấp độ NCKH: {$e->ten_cap_do}", 'DM_NCKH_CAP_DO', $id);
        return ['success' => true, 'message' => 'Thêm cấp độ thành công', 'data' => ['id' => $id]];
    }

    public static function update(DM_NCKH_CapDo_PUBLIC $e): array
    {
        if (!$e->id) return ['success' => false, 'message' => 'Thiếu ID'];
        if (DM_NCKH_CapDo_DAL::checkMaExists($e->ma_cap_do, $e->id)) {
            return ['success' => false, 'message' => 'Mã cấp độ đã tồn tại'];
        }
        DM_NCKH_CapDo_DAL::update($e);
        MemcachedHelper::deleteByPrefix('dm_nckh_cap_do:');
        DM_NhatKyHeThong_DAL::log($e->nguoi_cap_nhat ?? 0, self::MODULE_KEY, "Cập nhật cấp độ: {$e->ten_cap_do}", 'DM_NCKH_CAP_DO', $e->id);
        return ['success' => true, 'message' => 'Cập nhật thành công'];
    }

    public static function trash(int $id, int $u): array
    {
        DM_NCKH_CapDo_DAL::trash($id, $u);
        MemcachedHelper::deleteByPrefix('dm_nckh_cap_do:');
        return ['success' => true, 'message' => 'Đã chuyển vào thùng rác'];
    }

    public static function restore(int $id, int $u): array
    {
        DM_NCKH_CapDo_DAL::restore($id, $u);
        MemcachedHelper::deleteByPrefix('dm_nckh_cap_do:');
        return ['success' => true, 'message' => 'Đã khôi phục'];
    }

    public static function delete(int $id): array
    {
        DM_NCKH_CapDo_DAL::delete($id);
        MemcachedHelper::deleteByPrefix('dm_nckh_cap_do:');
        return ['success' => true, 'message' => 'Đã xóa vĩnh viễn'];
    }

    public static function getById(int $id): ?DM_NCKH_CapDo_DTO { return DM_NCKH_CapDo_DAL::getById($id); }
    public static function getPaged(int $page, int $pageSize, string $search = '', int $daXoa = 0): array { return DM_NCKH_CapDo_DAL::getPaged($page, $pageSize, $search, $daXoa); }
    public static function getCombo(): array { return DM_NCKH_CapDo_DAL::getCombo(); }
}
