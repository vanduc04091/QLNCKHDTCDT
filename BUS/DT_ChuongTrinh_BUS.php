<?php
require_once __DIR__ . '/../DAL/DT_ChuongTrinh_DAL.php';
require_once __DIR__ . '/../DAL/DM_NhatKyHeThong_DAL.php';

class DT_ChuongTrinh_BUS
{
    const MODULE_KEY = 'DT_ChuongTrinh';

    public static function insert(DT_ChuongTrinh_PUBLIC $e): array
    {
        $v = self::validate($e);
        if (!$v['success']) return $v;
        if (DT_ChuongTrinh_DAL::checkMaExists($e->ma_chuong_trinh)) {
            return ['success' => false, 'message' => 'Mã chương trình đã tồn tại'];
        }
        $id = DT_ChuongTrinh_DAL::insert($e);
        MemcachedHelper::deleteByPrefix('dt_chuong_trinh:');
        DM_NhatKyHeThong_DAL::log($e->nguoi_tao ?? 0, Constants::MODULE_HE_THONG, "Thêm chương trình: {$e->ten_chuong_trinh}", 'DT_CHUONG_TRINH', $id);
        return ['success' => true, 'message' => 'Thêm chương trình đào tạo thành công', 'data' => ['id' => $id]];
    }

    public static function update(DT_ChuongTrinh_PUBLIC $e): array
    {
        if (!$e->id) return ['success' => false, 'message' => 'Thiếu ID'];
        $v = self::validate($e);
        if (!$v['success']) return $v;
        if (DT_ChuongTrinh_DAL::checkMaExists($e->ma_chuong_trinh, $e->id)) {
            return ['success' => false, 'message' => 'Mã chương trình đã tồn tại'];
        }
        DT_ChuongTrinh_DAL::update($e);
        MemcachedHelper::deleteByPrefix('dt_chuong_trinh:');
        return ['success' => true, 'message' => 'Cập nhật thành công'];
    }

    private static function validate(DT_ChuongTrinh_PUBLIC $e): array
    {
        $e->ma_chuong_trinh = trim($e->ma_chuong_trinh);
        $e->ten_chuong_trinh = trim($e->ten_chuong_trinh);
        if ($e->ma_chuong_trinh === '' || $e->ten_chuong_trinh === '') {
            return ['success' => false, 'message' => 'Mã và tên chương trình không được để trống'];
        }
        if ($e->so_luong_toi_da < 1) return ['success' => false, 'message' => 'Số lượng tối đa phải ≥ 1'];
        return ['success' => true];
    }

    public static function trash(int $id, int $u): array
    {
        if (DT_ChuongTrinh_DAL::countHocVien($id) > 0) {
            return ['success' => false, 'message' => 'Chương trình đã có học viên — vui lòng xóa học viên khỏi chương trình trước'];
        }
        DT_ChuongTrinh_DAL::trash($id, $u);
        MemcachedHelper::deleteByPrefix('dt_chuong_trinh:');
        DM_NhatKyHeThong_DAL::log($u, Constants::MODULE_HE_THONG, "Xóa tạm chương trình id={$id}", 'DT_CHUONG_TRINH', $id);
        return ['success' => true, 'message' => 'Đã chuyển vào thùng rác'];
    }

    public static function restore(int $id, int $u): array
    {
        DT_ChuongTrinh_DAL::restore($id, $u);
        MemcachedHelper::deleteByPrefix('dt_chuong_trinh:');
        return ['success' => true, 'message' => 'Đã khôi phục'];
    }

    public static function delete(int $id): array
    {
        try {
            DT_ChuongTrinh_DAL::delete($id);
            MemcachedHelper::deleteByPrefix('dt_chuong_trinh:');
            return ['success' => true, 'message' => 'Đã xóa vĩnh viễn'];
        } catch (Throwable $ex) {
            return ['success' => false, 'message' => 'Không thể xóa: dữ liệu đang được tham chiếu'];
        }
    }

    public static function getById(int $id): ?DT_ChuongTrinh_DTO { return DT_ChuongTrinh_DAL::getById($id); }
    public static function getPaged(int $p, int $s, string $q = '', int $dx = 0, int $kh = 0, int $dt = 0): array
    {
        return DT_ChuongTrinh_DAL::getPaged($p, $s, $q, $dx, $kh, $dt);
    }
    public static function getStats(): array { return DT_ChuongTrinh_DAL::getStats(); }
    public static function getCombo(): array { return DT_ChuongTrinh_DAL::getCombo(); }
}
