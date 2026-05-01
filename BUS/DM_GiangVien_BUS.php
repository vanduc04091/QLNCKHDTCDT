<?php
require_once __DIR__ . '/../DAL/DM_GiangVien_DAL.php';
require_once __DIR__ . '/../DAL/DM_NhatKyHeThong_DAL.php';

class DM_GiangVien_BUS
{
    const MODULE_KEY = 'DM_GiangVien';

    public static function insert(DM_GiangVien_PUBLIC $e): array
    {
        $v = self::validate($e);
        if (!$v['success']) return $v;
        if (DM_GiangVien_DAL::checkMaExists($e->ma_gv)) {
            return ['success' => false, 'message' => 'Mã giảng viên đã tồn tại'];
        }
        $id = DM_GiangVien_DAL::insert($e);
        DM_NhatKyHeThong_DAL::log($e->nguoi_tao ?? 0, Constants::MODULE_HE_THONG,
            "Thêm giảng viên: {$e->ho_ten}", 'DM_GIANG_VIEN', $id);
        return ['success' => true, 'message' => 'Thêm giảng viên thành công', 'data' => ['id' => $id]];
    }

    public static function update(DM_GiangVien_PUBLIC $e): array
    {
        if (!$e->id) return ['success' => false, 'message' => 'Thiếu ID'];
        $v = self::validate($e);
        if (!$v['success']) return $v;
        if (DM_GiangVien_DAL::checkMaExists($e->ma_gv, $e->id)) {
            return ['success' => false, 'message' => 'Mã giảng viên đã tồn tại'];
        }
        DM_GiangVien_DAL::update($e);
        return ['success' => true, 'message' => 'Cập nhật thành công'];
    }

    public static function trash(int $id, int $u): array
    {
        DM_GiangVien_DAL::trash($id, $u);
        DM_NhatKyHeThong_DAL::log($u, Constants::MODULE_HE_THONG, "Xóa tạm GV id={$id}", 'DM_GIANG_VIEN', $id);
        return ['success' => true, 'message' => 'Đã chuyển vào thùng rác'];
    }

    public static function restore(int $id, int $u): array
    {
        DM_GiangVien_DAL::restore($id, $u);
        return ['success' => true, 'message' => 'Đã khôi phục'];
    }

    public static function delete(int $id): array
    {
        try {
            DM_GiangVien_DAL::delete($id);
            return ['success' => true, 'message' => 'Đã xóa vĩnh viễn'];
        } catch (Throwable $ex) {
            return ['success' => false, 'message' => 'Không thể xóa: giảng viên đang được phân công'];
        }
    }

    public static function getById(int $id): ?DM_GiangVien_DTO { return DM_GiangVien_DAL::getById($id); }
    public static function getPaged(int $p, int $s, string $q = '', int $dx = 0, int $loai = 0, int $tt = -1): array
    {
        return DM_GiangVien_DAL::getPaged($p, $s, $q, $dx, $loai, $tt);
    }
    public static function getStats(): array { return DM_GiangVien_DAL::getStats(); }
    public static function getCombo(int $loai = 0): array { return DM_GiangVien_DAL::getCombo($loai); }
    public static function getPhanCongByGV(int $gvId): array { return DM_GiangVien_DAL::getPhanCongByGV($gvId); }

    private static function validate(DM_GiangVien_PUBLIC $e): array
    {
        $e->ma_gv = trim($e->ma_gv);
        $e->ho_ten = trim($e->ho_ten);
        if ($e->ma_gv === '' || $e->ho_ten === '') {
            return ['success' => false, 'message' => 'Mã và họ tên không được để trống'];
        }
        if ($e->loai_gv < 1 || $e->loai_gv > 3) {
            return ['success' => false, 'message' => 'Loại giảng viên không hợp lệ'];
        }
        if ($e->email && !Helper::isEmail($e->email)) {
            return ['success' => false, 'message' => 'Email không hợp lệ'];
        }
        // Loại 1 = cơ hữu thì nên có nhan_vien_id (cảnh báo nhẹ, không bắt buộc)
        return ['success' => true];
    }
}
