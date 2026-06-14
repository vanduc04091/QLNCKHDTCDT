<?php
require_once __DIR__ . '/../DAL/DT_KhoaHoc_DAL.php';
require_once __DIR__ . '/../DAL/DM_NhatKyHeThong_DAL.php';

class DT_KhoaHoc_BUS
{
    const MODULE_KEY = 'DT_KhoaHoc';

    public static function insert(DT_KhoaHoc_PUBLIC $e): array
    {
        $e->ma_khoa_hoc = trim($e->ma_khoa_hoc);
        $e->ten_khoa_hoc = trim($e->ten_khoa_hoc);
        if ($e->ma_khoa_hoc === '' || $e->ten_khoa_hoc === '') {
            return ['success' => false, 'message' => 'Mã và tên khóa học không được để trống'];
        }
        if (DT_KhoaHoc_DAL::checkMaExists($e->ma_khoa_hoc)) {
            return ['success' => false, 'message' => 'Mã khóa học đã tồn tại'];
        }
        if ($e->ngay_bat_dau && $e->ngay_ket_thuc && $e->ngay_bat_dau > $e->ngay_ket_thuc) {
            return ['success' => false, 'message' => 'Ngày bắt đầu phải trước ngày kết thúc'];
        }
        $id = DT_KhoaHoc_DAL::insert($e);
        MemcachedHelper::deleteByPrefix('dt_khoa_hoc:');
        DM_NhatKyHeThong_DAL::log($e->nguoi_tao ?? 0, self::MODULE_KEY, "Thêm khóa học: {$e->ten_khoa_hoc}", 'DT_KHOA_HOC', $id);
        return ['success' => true, 'message' => 'Thêm khóa học thành công', 'data' => ['id' => $id]];
    }

    public static function update(DT_KhoaHoc_PUBLIC $e): array
    {
        if (!$e->id) return ['success' => false, 'message' => 'Thiếu ID'];
        if (DT_KhoaHoc_DAL::checkMaExists($e->ma_khoa_hoc, $e->id)) {
            return ['success' => false, 'message' => 'Mã khóa học đã tồn tại'];
        }
        if ($e->ngay_bat_dau && $e->ngay_ket_thuc && $e->ngay_bat_dau > $e->ngay_ket_thuc) {
            return ['success' => false, 'message' => 'Ngày bắt đầu phải trước ngày kết thúc'];
        }
        DT_KhoaHoc_DAL::update($e);
        MemcachedHelper::deleteByPrefix('dt_khoa_hoc:');
        DM_NhatKyHeThong_DAL::log($e->nguoi_cap_nhat ?? 0, self::MODULE_KEY, "Cập nhật khóa học: {$e->ten_khoa_hoc}", 'DT_KHOA_HOC', $e->id);
        return ['success' => true, 'message' => 'Cập nhật thành công'];
    }

    public static function trash(int $id, int $u): array
    {
        DT_KhoaHoc_DAL::trash($id, $u);
        MemcachedHelper::deleteByPrefix('dt_khoa_hoc:');
        return ['success' => true, 'message' => 'Đã chuyển vào thùng rác'];
    }

    public static function restore(int $id, int $u): array
    {
        DT_KhoaHoc_DAL::restore($id, $u);
        MemcachedHelper::deleteByPrefix('dt_khoa_hoc:');
        return ['success' => true, 'message' => 'Đã khôi phục'];
    }

    public static function delete(int $id): array
    {
        DT_KhoaHoc_DAL::delete($id);
        MemcachedHelper::deleteByPrefix('dt_khoa_hoc:');
        return ['success' => true, 'message' => 'Đã xóa vĩnh viễn'];
    }

    public static function getById(int $id): ?DT_KhoaHoc_DTO
    {
        return DT_KhoaHoc_DAL::getById($id);
    }

    public static function getPaged(int $page, int $pageSize, string $search = '', int $daXoa = 0,
                                    int $loaiHinhId = 0, int $hinhThucId = 0, int $doiTuongId = 0): array
    {
        return DT_KhoaHoc_DAL::getPaged($page, $pageSize, $search, $daXoa, $loaiHinhId, $hinhThucId, $doiTuongId);
    }

    public static function getCombo(): array
    {
        return DT_KhoaHoc_DAL::getCombo();
    }
}
