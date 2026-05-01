<?php
require_once __DIR__ . '/../DAL/DM_LoaiHinhDaoTao_DAL.php';
require_once __DIR__ . '/../DAL/DM_NhatKyHeThong_DAL.php';

class DM_LoaiHinhDaoTao_BUS
{
    const MODULE_KEY = 'DM_LoaiHinhDaoTao';

    public static function insert(DM_LoaiHinhDaoTao_PUBLIC $e): array
    {
        $e->ma_loai_hinh = trim($e->ma_loai_hinh);
        $e->ten_loai_hinh = trim($e->ten_loai_hinh);
        if ($e->ma_loai_hinh === '' || $e->ten_loai_hinh === '') {
            return ['success' => false, 'message' => 'Mã và tên loại hình không được để trống'];
        }
        if (DM_LoaiHinhDaoTao_DAL::checkMaExists($e->ma_loai_hinh)) {
            return ['success' => false, 'message' => 'Mã loại hình đã tồn tại'];
        }
        $id = DM_LoaiHinhDaoTao_DAL::insert($e);
        MemcachedHelper::deleteByPrefix('dm_loai_hinh_dao_tao:');
        DM_NhatKyHeThong_DAL::log($e->nguoi_tao ?? 0, self::MODULE_KEY, "Thêm loại hình: {$e->ten_loai_hinh}", 'DM_LOAI_HINH_DAO_TAO', $id);
        return ['success' => true, 'message' => 'Thêm loại hình thành công', 'data' => ['id' => $id]];
    }

    public static function update(DM_LoaiHinhDaoTao_PUBLIC $e): array
    {
        if (!$e->id) return ['success' => false, 'message' => 'Thiếu ID'];
        if (DM_LoaiHinhDaoTao_DAL::checkMaExists($e->ma_loai_hinh, $e->id)) {
            return ['success' => false, 'message' => 'Mã loại hình đã tồn tại'];
        }
        DM_LoaiHinhDaoTao_DAL::update($e);
        MemcachedHelper::deleteByPrefix('dm_loai_hinh_dao_tao:');
        DM_NhatKyHeThong_DAL::log($e->nguoi_cap_nhat ?? 0, self::MODULE_KEY, "Cập nhật loại hình: {$e->ten_loai_hinh}", 'DM_LOAI_HINH_DAO_TAO', $e->id);
        return ['success' => true, 'message' => 'Cập nhật thành công'];
    }

    public static function trash(int $id, int $u): array
    {
        DM_LoaiHinhDaoTao_DAL::trash($id, $u);
        MemcachedHelper::deleteByPrefix('dm_loai_hinh_dao_tao:');
        return ['success' => true, 'message' => 'Đã chuyển vào thùng rác'];
    }

    public static function restore(int $id, int $u): array
    {
        DM_LoaiHinhDaoTao_DAL::restore($id, $u);
        MemcachedHelper::deleteByPrefix('dm_loai_hinh_dao_tao:');
        return ['success' => true, 'message' => 'Đã khôi phục'];
    }

    public static function delete(int $id): array
    {
        DM_LoaiHinhDaoTao_DAL::delete($id);
        MemcachedHelper::deleteByPrefix('dm_loai_hinh_dao_tao:');
        return ['success' => true, 'message' => 'Đã xóa vĩnh viễn'];
    }

    public static function getById(int $id): ?DM_LoaiHinhDaoTao_DTO
    {
        return DM_LoaiHinhDaoTao_DAL::getById($id);
    }

    public static function getPaged(int $page, int $pageSize, string $search = '', int $daXoa = 0): array
    {
        return DM_LoaiHinhDaoTao_DAL::getPaged($page, $pageSize, $search, $daXoa);
    }

    public static function getCombo(): array
    {
        return DM_LoaiHinhDaoTao_DAL::getCombo();
    }
}
