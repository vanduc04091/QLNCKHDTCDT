<?php
require_once __DIR__ . '/../DAL/DM_NCKH_TheLoai_DAL.php';
require_once __DIR__ . '/../DAL/DM_NhatKyHeThong_DAL.php';

class DM_NCKH_TheLoai_BUS
{
    const MODULE_KEY = 'DM_NCKH_TheLoai';

    public static function insert(DM_NCKH_TheLoai_PUBLIC $e): array
    {
        $e->ma_the_loai = trim($e->ma_the_loai);
        $e->ten_the_loai = trim($e->ten_the_loai);
        if ($e->ma_the_loai === '' || $e->ten_the_loai === '') {
            return ['success' => false, 'message' => 'Mã và tên thể loại không được để trống'];
        }
        if (DM_NCKH_TheLoai_DAL::checkMaExists($e->ma_the_loai)) {
            return ['success' => false, 'message' => 'Mã thể loại đã tồn tại'];
        }
        $id = DM_NCKH_TheLoai_DAL::insert($e);
        MemcachedHelper::deleteByPrefix('dm_nckh_the_loai:');
        DM_NhatKyHeThong_DAL::log($e->nguoi_tao ?? 0, self::MODULE_KEY, "Thêm thể loại NCKH: {$e->ten_the_loai}", 'DM_NCKH_THE_LOAI', $id);
        return ['success' => true, 'message' => 'Thêm thể loại thành công', 'data' => ['id' => $id]];
    }

    public static function update(DM_NCKH_TheLoai_PUBLIC $e): array
    {
        if (!$e->id) return ['success' => false, 'message' => 'Thiếu ID'];
        if (DM_NCKH_TheLoai_DAL::checkMaExists($e->ma_the_loai, $e->id)) {
            return ['success' => false, 'message' => 'Mã thể loại đã tồn tại'];
        }
        DM_NCKH_TheLoai_DAL::update($e);
        MemcachedHelper::deleteByPrefix('dm_nckh_the_loai:');
        DM_NhatKyHeThong_DAL::log($e->nguoi_cap_nhat ?? 0, self::MODULE_KEY, "Cập nhật thể loại: {$e->ten_the_loai}", 'DM_NCKH_THE_LOAI', $e->id);
        return ['success' => true, 'message' => 'Cập nhật thành công'];
    }

    public static function trash(int $id, int $u): array { DM_NCKH_TheLoai_DAL::trash($id, $u); MemcachedHelper::deleteByPrefix('dm_nckh_the_loai:'); return ['success' => true, 'message' => 'Đã chuyển vào thùng rác']; }
    public static function restore(int $id, int $u): array { DM_NCKH_TheLoai_DAL::restore($id, $u); MemcachedHelper::deleteByPrefix('dm_nckh_the_loai:'); return ['success' => true, 'message' => 'Đã khôi phục']; }
    public static function delete(int $id): array { DM_NCKH_TheLoai_DAL::delete($id); MemcachedHelper::deleteByPrefix('dm_nckh_the_loai:'); return ['success' => true, 'message' => 'Đã xóa vĩnh viễn']; }
    public static function getById(int $id): ?DM_NCKH_TheLoai_DTO { return DM_NCKH_TheLoai_DAL::getById($id); }
    public static function getPaged(int $page, int $pageSize, string $search = '', int $daXoa = 0): array { return DM_NCKH_TheLoai_DAL::getPaged($page, $pageSize, $search, $daXoa); }
    public static function getCombo(): array { return DM_NCKH_TheLoai_DAL::getCombo(); }
}
