<?php
require_once __DIR__ . '/../DAL/DM_DoiTuongHocVien_DAL.php';
require_once __DIR__ . '/../DAL/DM_NhatKyHeThong_DAL.php';

class DM_DoiTuongHocVien_BUS
{
    const MODULE_KEY = 'DM_DoiTuongHocVien';

    public static function insert(DM_DoiTuongHocVien_PUBLIC $e): array
    {
        $e->ma_doi_tuong = trim($e->ma_doi_tuong);
        $e->ten_doi_tuong = trim($e->ten_doi_tuong);
        if ($e->ma_doi_tuong === '' || $e->ten_doi_tuong === '') {
            return ['success' => false, 'message' => 'Mã và tên đối tượng không được để trống'];
        }
        if (DM_DoiTuongHocVien_DAL::checkMaExists($e->ma_doi_tuong)) {
            return ['success' => false, 'message' => 'Mã đối tượng đã tồn tại'];
        }
        $id = DM_DoiTuongHocVien_DAL::insert($e);
        MemcachedHelper::deleteByPrefix('dm_doi_tuong_hoc_vien:');
        DM_NhatKyHeThong_DAL::log($e->nguoi_tao ?? 0, self::MODULE_KEY, "Thêm đối tượng: {$e->ten_doi_tuong}", 'DM_DOI_TUONG_HOC_VIEN', $id);
        return ['success' => true, 'message' => 'Thêm thành công', 'data' => ['id' => $id]];
    }

    public static function update(DM_DoiTuongHocVien_PUBLIC $e): array
    {
        if (!$e->id) return ['success' => false, 'message' => 'Thiếu ID'];
        if (DM_DoiTuongHocVien_DAL::checkMaExists($e->ma_doi_tuong, $e->id)) {
            return ['success' => false, 'message' => 'Mã đối tượng đã tồn tại'];
        }
        DM_DoiTuongHocVien_DAL::update($e);
        MemcachedHelper::deleteByPrefix('dm_doi_tuong_hoc_vien:');
        DM_NhatKyHeThong_DAL::log($e->nguoi_cap_nhat ?? 0, self::MODULE_KEY, "Cập nhật đối tượng: {$e->ten_doi_tuong}", 'DM_DOI_TUONG_HOC_VIEN', $e->id);
        return ['success' => true, 'message' => 'Cập nhật thành công'];
    }

    public static function trash(int $id, int $u): array
    {
        DM_DoiTuongHocVien_DAL::trash($id, $u);
        MemcachedHelper::deleteByPrefix('dm_doi_tuong_hoc_vien:');
        return ['success' => true, 'message' => 'Đã chuyển vào thùng rác'];
    }

    public static function restore(int $id, int $u): array
    {
        DM_DoiTuongHocVien_DAL::restore($id, $u);
        MemcachedHelper::deleteByPrefix('dm_doi_tuong_hoc_vien:');
        return ['success' => true, 'message' => 'Đã khôi phục'];
    }

    public static function delete(int $id): array
    {
        DM_DoiTuongHocVien_DAL::delete($id);
        MemcachedHelper::deleteByPrefix('dm_doi_tuong_hoc_vien:');
        return ['success' => true, 'message' => 'Đã xóa vĩnh viễn'];
    }

    public static function getById(int $id): ?DM_DoiTuongHocVien_DTO
    {
        return DM_DoiTuongHocVien_DAL::getById($id);
    }

    public static function getPaged(int $page, int $pageSize, string $search = '', int $daXoa = 0): array
    {
        return DM_DoiTuongHocVien_DAL::getPaged($page, $pageSize, $search, $daXoa);
    }

    public static function getCombo(): array
    {
        return DM_DoiTuongHocVien_DAL::getCombo();
    }
}
