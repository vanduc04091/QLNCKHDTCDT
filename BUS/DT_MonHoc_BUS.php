<?php
require_once __DIR__ . '/../DAL/DT_MonHoc_DAL.php';
require_once __DIR__ . '/../DAL/DM_NhatKyHeThong_DAL.php';

class DT_MonHoc_BUS
{
    const MODULE_KEY = 'DT_MonHoc';

    public static function insert(DT_MonHoc_PUBLIC $e): array
    {
        $e->ma_mon_hoc = trim($e->ma_mon_hoc);
        $e->ten_mon_hoc = trim($e->ten_mon_hoc);
        if ($e->ma_mon_hoc === '' || $e->ten_mon_hoc === '') {
            return ['success' => false, 'message' => 'Mã và tên môn học không được để trống'];
        }
        if (DT_MonHoc_DAL::checkMaExists($e->ma_mon_hoc)) {
            return ['success' => false, 'message' => 'Mã môn học đã tồn tại'];
        }
        $e->tong_so_tiet = $e->so_tiet_ly_thuyet + $e->so_tiet_thuc_hanh;
        $id = DT_MonHoc_DAL::insert($e);
        MemcachedHelper::deleteByPrefix('dt_mon_hoc:');
        DM_NhatKyHeThong_DAL::log($e->nguoi_tao ?? 0, self::MODULE_KEY, "Thêm môn học: {$e->ten_mon_hoc}", 'DT_MON_HOC', $id);
        return ['success' => true, 'message' => 'Thêm môn học thành công', 'data' => ['id' => $id]];
    }

    public static function update(DT_MonHoc_PUBLIC $e): array
    {
        if (!$e->id) return ['success' => false, 'message' => 'Thiếu ID'];
        $e->ma_mon_hoc = trim($e->ma_mon_hoc);
        $e->ten_mon_hoc = trim($e->ten_mon_hoc);
        if ($e->ma_mon_hoc === '' || $e->ten_mon_hoc === '') {
            return ['success' => false, 'message' => 'Mã và tên môn học không được để trống'];
        }
        if (DT_MonHoc_DAL::checkMaExists($e->ma_mon_hoc, $e->id)) {
            return ['success' => false, 'message' => 'Mã môn học đã tồn tại'];
        }
        $e->tong_so_tiet = $e->so_tiet_ly_thuyet + $e->so_tiet_thuc_hanh;
        DT_MonHoc_DAL::update($e);
        MemcachedHelper::deleteByPrefix('dt_mon_hoc:');
        DM_NhatKyHeThong_DAL::log($e->nguoi_cap_nhat ?? 0, self::MODULE_KEY, "Cập nhật môn học: {$e->ten_mon_hoc}", 'DT_MON_HOC', $e->id);
        return ['success' => true, 'message' => 'Cập nhật thành công'];
    }

    public static function trash(int $id, int $u): array
    {
        $used = DT_MonHoc_DAL::isUsedInKhoaHoc($id);
        if ($used > 0) {
            return ['success' => false, 'message' => "Môn học đang được sử dụng trong {$used} khóa học. Hãy gỡ khỏi các khóa học trước khi xóa."];
        }
        DT_MonHoc_DAL::trash($id, $u);
        MemcachedHelper::deleteByPrefix('dt_mon_hoc:');
        DM_NhatKyHeThong_DAL::log($u, self::MODULE_KEY, "Chuyển môn học #{$id} vào thùng rác", 'DT_MON_HOC', $id);
        return ['success' => true, 'message' => 'Đã chuyển vào thùng rác'];
    }

    public static function restore(int $id, int $u): array
    {
        DT_MonHoc_DAL::restore($id, $u);
        MemcachedHelper::deleteByPrefix('dt_mon_hoc:');
        return ['success' => true, 'message' => 'Đã khôi phục'];
    }

    public static function delete(int $id): array
    {
        $used = DT_MonHoc_DAL::isUsedInKhoaHoc($id);
        if ($used > 0) {
            return ['success' => false, 'message' => "Không thể xóa vĩnh viễn: môn đang liên kết với {$used} khóa học."];
        }
        DT_MonHoc_DAL::delete($id);
        MemcachedHelper::deleteByPrefix('dt_mon_hoc:');
        return ['success' => true, 'message' => 'Đã xóa vĩnh viễn'];
    }

    public static function getById(int $id): ?DT_MonHoc_DTO
    {
        return DT_MonHoc_DAL::getById($id);
    }

    public static function getPaged(int $page, int $pageSize, string $search = '', int $daXoa = 0, int $trangThai = -1): array
    {
        return DT_MonHoc_DAL::getPaged($page, $pageSize, $search, $daXoa, $trangThai);
    }

    public static function getStats(): array
    {
        return DT_MonHoc_DAL::getStats();
    }

    public static function getCombo(): array
    {
        return DT_MonHoc_DAL::getCombo();
    }
}
