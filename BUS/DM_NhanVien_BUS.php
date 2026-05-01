<?php
require_once __DIR__ . '/../DAL/DM_NhanVien_DAL.php';
require_once __DIR__ . '/../DAL/DM_NhatKyHeThong_DAL.php';

class DM_NhanVien_BUS
{
    const MODULE_KEY = 'DM_NhanVien';

    public static function insert(DM_NhanVien_PUBLIC $e): array
    {
        $e->ma_nv = trim($e->ma_nv);
        $e->ho_ten = trim($e->ho_ten);
        if ($e->ma_nv === '' || $e->ho_ten === '') {
            return ['success' => false, 'message' => 'Mã NV và họ tên không được để trống'];
        }
        if ($e->benh_vien_id <= 0) return ['success' => false, 'message' => 'Chưa chọn bệnh viện'];
        if ($e->email && !Helper::isEmail($e->email)) {
            return ['success' => false, 'message' => 'Email không hợp lệ'];
        }
        if (DM_NhanVien_DAL::checkMaExists($e->benh_vien_id, $e->ma_nv)) {
            return ['success' => false, 'message' => 'Mã nhân viên đã tồn tại trong bệnh viện này'];
        }
        $id = DM_NhanVien_DAL::insert($e);
        MemcachedHelper::deleteByPrefix('dm_nhan_vien:');
        DM_NhatKyHeThong_DAL::log($e->nguoi_tao ?? 0, Constants::MODULE_HE_THONG, "Thêm NV: {$e->ho_ten}", 'DM_NHAN_VIEN', $id);
        return ['success' => true, 'message' => 'Thêm nhân viên thành công', 'data' => ['id' => $id]];
    }

    public static function update(DM_NhanVien_PUBLIC $e): array
    {
        if (!$e->id) return ['success' => false, 'message' => 'Thiếu ID'];
        if ($e->email && !Helper::isEmail($e->email)) {
            return ['success' => false, 'message' => 'Email không hợp lệ'];
        }
        if (DM_NhanVien_DAL::checkMaExists($e->benh_vien_id, $e->ma_nv, $e->id)) {
            return ['success' => false, 'message' => 'Mã nhân viên đã tồn tại'];
        }
        DM_NhanVien_DAL::update($e);
        MemcachedHelper::deleteByPrefix('dm_nhan_vien:');
        return ['success' => true, 'message' => 'Cập nhật thành công'];
    }

    public static function trash(int $id, int $u): array
    {
        DM_NhanVien_DAL::trash($id, $u);
        MemcachedHelper::deleteByPrefix('dm_nhan_vien:');
        DM_NhatKyHeThong_DAL::log($u, Constants::MODULE_HE_THONG, "Xóa tạm NV id={$id}", 'DM_NHAN_VIEN', $id);
        return ['success' => true, 'message' => 'Đã chuyển vào thùng rác'];
    }

    public static function restore(int $id, int $u): array
    {
        DM_NhanVien_DAL::restore($id, $u);
        MemcachedHelper::deleteByPrefix('dm_nhan_vien:');
        return ['success' => true, 'message' => 'Đã khôi phục'];
    }

    public static function delete(int $id): array
    {
        try {
            DM_NhanVien_DAL::delete($id);
            MemcachedHelper::deleteByPrefix('dm_nhan_vien:');
            return ['success' => true, 'message' => 'Đã xóa vĩnh viễn'];
        } catch (Throwable $ex) {
            return ['success' => false, 'message' => 'Không thể xóa: dữ liệu đang được tham chiếu'];
        }
    }

    public static function getById(int $id): ?DM_NhanVien_DTO
    {
        return DM_NhanVien_DAL::getById($id);
    }

    public static function getPaged(int $page, int $pageSize, string $search = '', int $daXoa = 0, int $khoaPhongId = 0): array
    {
        return DM_NhanVien_DAL::getPaged($page, $pageSize, $search, $daXoa, $khoaPhongId);
    }

    public static function getCombo(int $khoaPhongId = 0): array
    {
        return DM_NhanVien_DAL::getCombo($khoaPhongId);
    }
}
