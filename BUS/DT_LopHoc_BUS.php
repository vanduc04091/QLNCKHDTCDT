<?php
require_once __DIR__ . '/../DAL/DT_LopHoc_DAL.php';
require_once __DIR__ . '/../DAL/DM_NhatKyHeThong_DAL.php';

class DT_LopHoc_BUS
{
    const MODULE_KEY = 'DT_LopHoc';

    public static function insert(DT_LopHoc_PUBLIC $e): array
    {
        $v = self::validate($e);
        if (!$v['success']) return $v;
        if (DT_LopHoc_DAL::checkMaExists($e->ma_lop)) {
            return ['success' => false, 'message' => 'Mã lớp đã tồn tại'];
        }
        $id = DT_LopHoc_DAL::insert($e);
        MemcachedHelper::deleteByPrefix('dt_lop_hoc:');
        DM_NhatKyHeThong_DAL::log($e->nguoi_tao ?? 0, Constants::MODULE_HE_THONG, "Thêm lớp: {$e->ten_lop}", 'DT_LOP_HOC', $id);
        return ['success' => true, 'message' => 'Thêm lớp học thành công', 'data' => ['id' => $id]];
    }

    public static function update(DT_LopHoc_PUBLIC $e): array
    {
        if (!$e->id) return ['success' => false, 'message' => 'Thiếu ID'];
        $v = self::validate($e);
        if (!$v['success']) return $v;
        if (DT_LopHoc_DAL::checkMaExists($e->ma_lop, $e->id)) {
            return ['success' => false, 'message' => 'Mã lớp đã tồn tại'];
        }
        DT_LopHoc_DAL::update($e);
        MemcachedHelper::deleteByPrefix('dt_lop_hoc:');
        return ['success' => true, 'message' => 'Cập nhật thành công'];
    }

    private static function validate(DT_LopHoc_PUBLIC $e): array
    {
        $e->ma_lop = trim($e->ma_lop);
        $e->ten_lop = trim($e->ten_lop);
        if ($e->ma_lop === '' || $e->ten_lop === '') {
            return ['success' => false, 'message' => 'Mã lớp và tên lớp không được để trống'];
        }
        if ($e->khoa_hoc_id <= 0) return ['success' => false, 'message' => 'Chưa chọn khóa học'];
        if ($e->so_luong_toi_da < 1) return ['success' => false, 'message' => 'Số lượng tối đa phải ≥ 1'];
        if ($e->ngay_bat_dau && $e->ngay_ket_thuc && $e->ngay_bat_dau > $e->ngay_ket_thuc) {
            return ['success' => false, 'message' => 'Ngày bắt đầu phải trước ngày kết thúc'];
        }
        return ['success' => true];
    }

    public static function trash(int $id, int $u): array
    {
        if (DT_LopHoc_DAL::countHocVienByLop($id) > 0) {
            return ['success' => false, 'message' => 'Lớp đã có học viên — vui lòng xóa học viên khỏi lớp trước'];
        }
        DT_LopHoc_DAL::trash($id, $u);
        MemcachedHelper::deleteByPrefix('dt_lop_hoc:');
        DM_NhatKyHeThong_DAL::log($u, Constants::MODULE_HE_THONG, "Xóa tạm lớp id={$id}", 'DT_LOP_HOC', $id);
        return ['success' => true, 'message' => 'Đã chuyển vào thùng rác'];
    }

    public static function restore(int $id, int $u): array
    {
        DT_LopHoc_DAL::restore($id, $u);
        MemcachedHelper::deleteByPrefix('dt_lop_hoc:');
        return ['success' => true, 'message' => 'Đã khôi phục'];
    }

    public static function delete(int $id): array
    {
        try {
            DT_LopHoc_DAL::delete($id);
            MemcachedHelper::deleteByPrefix('dt_lop_hoc:');
            return ['success' => true, 'message' => 'Đã xóa vĩnh viễn'];
        } catch (Throwable $ex) {
            return ['success' => false, 'message' => 'Không thể xóa: dữ liệu đang được tham chiếu'];
        }
    }

    public static function getById(int $id): ?DT_LopHoc_DTO { return DT_LopHoc_DAL::getById($id); }
    public static function getPaged(int $p, int $s, string $q = '', int $dx = 0, int $kh = 0, int $tt = -1): array
    {
        return DT_LopHoc_DAL::getPaged($p, $s, $q, $dx, $kh, $tt);
    }
    public static function getStats(): array { return DT_LopHoc_DAL::getStats(); }
}
