<?php
require_once __DIR__ . '/../DAL/DM_KhoaPhong_DAL.php';
require_once __DIR__ . '/../DAL/DM_NhatKyHeThong_DAL.php';

class DM_KhoaPhong_BUS
{
    const MODULE_KEY = 'DM_KhoaPhong';

    private static function validateLoai(string $loai): bool
    {
        return in_array($loai, [Constants::LOAI_KHOA, Constants::LOAI_PHONG, Constants::LOAI_TRUNG_TAM], true);
    }

    public static function insert(DM_KhoaPhong_PUBLIC $e): array
    {
        $e->ma_khoa = trim($e->ma_khoa);
        $e->ten_khoa = trim($e->ten_khoa);
        if ($e->ma_khoa === '' || $e->ten_khoa === '') {
            return ['success' => false, 'message' => 'Mã và tên không được để trống'];
        }
        if (!self::validateLoai($e->loai_don_vi)) {
            return ['success' => false, 'message' => 'Loại đơn vị không hợp lệ'];
        }
        if (DM_KhoaPhong_DAL::checkMaExists($e->ma_khoa)) {
            return ['success' => false, 'message' => 'Mã khoa/phòng đã tồn tại'];
        }
        if ($e->email && !Helper::isEmail($e->email)) {
            return ['success' => false, 'message' => 'Email không hợp lệ'];
        }
        $id = DM_KhoaPhong_DAL::insert($e);
        MemcachedHelper::deleteByPrefix('dm_khoa_phong:');
        DM_NhatKyHeThong_DAL::log($e->nguoi_tao ?? 0, Constants::MODULE_HE_THONG, "Thêm khoa/phòng: {$e->ten_khoa}", 'DM_KHOA_PHONG', $id);
        return ['success' => true, 'message' => 'Thêm khoa/phòng thành công', 'data' => ['id' => $id]];
    }

    public static function update(DM_KhoaPhong_PUBLIC $e): array
    {
        if (!$e->id) return ['success' => false, 'message' => 'Thiếu ID'];
        if (!self::validateLoai($e->loai_don_vi)) {
            return ['success' => false, 'message' => 'Loại đơn vị không hợp lệ'];
        }
        if (DM_KhoaPhong_DAL::checkMaExists($e->ma_khoa, $e->id)) {
            return ['success' => false, 'message' => 'Mã khoa/phòng đã tồn tại'];
        }
        DM_KhoaPhong_DAL::update($e);
        MemcachedHelper::deleteByPrefix('dm_khoa_phong:');
        return ['success' => true, 'message' => 'Cập nhật thành công'];
    }

    public static function trash(int $id, int $u): array
    {
        DM_KhoaPhong_DAL::trash($id, $u);
        MemcachedHelper::deleteByPrefix('dm_khoa_phong:');
        return ['success' => true, 'message' => 'Đã chuyển vào thùng rác'];
    }

    public static function restore(int $id, int $u): array
    {
        DM_KhoaPhong_DAL::restore($id, $u);
        MemcachedHelper::deleteByPrefix('dm_khoa_phong:');
        return ['success' => true, 'message' => 'Đã khôi phục'];
    }

    public static function delete(int $id): array
    {
        try {
            DM_KhoaPhong_DAL::delete($id);
            MemcachedHelper::deleteByPrefix('dm_khoa_phong:');
            return ['success' => true, 'message' => 'Đã xóa vĩnh viễn'];
        } catch (Throwable $ex) {
            return ['success' => false, 'message' => 'Không thể xóa: đang có nhân viên thuộc khoa/phòng này'];
        }
    }

    public static function getById(int $id): ?DM_KhoaPhong_DTO
    {
        return DM_KhoaPhong_DAL::getById($id);
    }

    public static function getPaged(int $page, int $pageSize, string $search = '', int $daXoa = 0, string $loai = ''): array
    {
        return DM_KhoaPhong_DAL::getPaged($page, $pageSize, $search, $daXoa, $loai);
    }

    public static function getCombo(string $loai = ''): array
    {
        return DM_KhoaPhong_DAL::getCombo($loai);
    }
}
