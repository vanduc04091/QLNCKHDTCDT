<?php
require_once __DIR__ . '/../DAL/DM_BenhVien_DAL.php';
require_once __DIR__ . '/../DAL/DM_NhatKyHeThong_DAL.php';

class DM_BenhVien_BUS
{
    const MODULE_KEY = 'DM_BenhVien';

    public static function insert(DM_BenhVien_PUBLIC $e): array
    {
        $e->ma_benh_vien = trim($e->ma_benh_vien);
        $e->ten_benh_vien = trim($e->ten_benh_vien);
        if ($e->ma_benh_vien === '' || $e->ten_benh_vien === '') {
            return ['success' => false, 'message' => 'Mã và tên bệnh viện không được để trống'];
        }
        if (DM_BenhVien_DAL::checkMaExists($e->ma_benh_vien)) {
            return ['success' => false, 'message' => 'Mã bệnh viện đã tồn tại'];
        }
        if ($e->email && !Helper::isEmail($e->email)) {
            return ['success' => false, 'message' => 'Email không hợp lệ'];
        }
        $id = DM_BenhVien_DAL::insert($e);
        MemcachedHelper::deleteByPrefix('dm_benh_vien:');
        DM_NhatKyHeThong_DAL::log($e->nguoi_tao ?? 0, Constants::MODULE_HE_THONG, "Thêm BV: {$e->ten_benh_vien}", 'DM_BENH_VIEN', $id);
        return ['success' => true, 'message' => 'Thêm bệnh viện thành công', 'data' => ['id' => $id]];
    }

    public static function update(DM_BenhVien_PUBLIC $e): array
    {
        if (!$e->id) return ['success' => false, 'message' => 'Thiếu ID'];
        if (DM_BenhVien_DAL::checkMaExists($e->ma_benh_vien, $e->id)) {
            return ['success' => false, 'message' => 'Mã bệnh viện đã tồn tại'];
        }
        if ($e->email && !Helper::isEmail($e->email)) {
            return ['success' => false, 'message' => 'Email không hợp lệ'];
        }
        DM_BenhVien_DAL::update($e);
        MemcachedHelper::deleteByPrefix('dm_benh_vien:');
        return ['success' => true, 'message' => 'Cập nhật thành công'];
    }

    public static function trash(int $id, int $u): array
    {
        if ($id === 1) return ['success' => false, 'message' => 'Không thể xóa bệnh viện gốc'];
        DM_BenhVien_DAL::trash($id, $u);
        MemcachedHelper::deleteByPrefix('dm_benh_vien:');
        return ['success' => true, 'message' => 'Đã chuyển vào thùng rác'];
    }

    public static function restore(int $id, int $u): array
    {
        DM_BenhVien_DAL::restore($id, $u);
        MemcachedHelper::deleteByPrefix('dm_benh_vien:');
        return ['success' => true, 'message' => 'Đã khôi phục'];
    }

    public static function delete(int $id): array
    {
        if ($id === 1) return ['success' => false, 'message' => 'Không thể xóa bệnh viện gốc'];
        try {
            DM_BenhVien_DAL::delete($id);
            MemcachedHelper::deleteByPrefix('dm_benh_vien:');
            return ['success' => true, 'message' => 'Đã xóa vĩnh viễn'];
        } catch (Throwable $ex) {
            return ['success' => false, 'message' => 'Không thể xóa: đang có nhân viên thuộc bệnh viện này'];
        }
    }

    public static function getById(int $id): ?DM_BenhVien_DTO
    {
        return DM_BenhVien_DAL::getById($id);
    }

    public static function getPaged(int $page, int $pageSize, string $search = '', int $daXoa = 0, string $cap = ''): array
    {
        return DM_BenhVien_DAL::getPaged($page, $pageSize, $search, $daXoa, $cap);
    }

    public static function getCombo(): array
    {
        return DM_BenhVien_DAL::getCombo();
    }
}
