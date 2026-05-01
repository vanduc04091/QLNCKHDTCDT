<?php
require_once __DIR__ . '/../DAL/NCKH_ThanhVien_DAL.php';

class NCKH_ThanhVien_BUS
{
    public static function insert(NCKH_ThanhVien_PUBLIC $e): array
    {
        if ($e->de_tai_id <= 0) return ['success' => false, 'message' => 'Thiếu đề tài'];
        if (!$e->nhan_vien_id && !$e->ho_ten_ngoai) {
            return ['success' => false, 'message' => 'Chọn nhân viên hoặc nhập họ tên người ngoài'];
        }
        if ($e->phan_tram_dong_gop !== null && ($e->phan_tram_dong_gop < 0 || $e->phan_tram_dong_gop > 100)) {
            return ['success' => false, 'message' => '% đóng góp phải từ 0 đến 100'];
        }
        $id = NCKH_ThanhVien_DAL::insert($e);
        return ['success' => true, 'message' => 'Đã thêm thành viên', 'data' => ['id' => $id]];
    }

    public static function update(NCKH_ThanhVien_PUBLIC $e): array
    {
        if (!$e->id) return ['success' => false, 'message' => 'Thiếu ID'];
        NCKH_ThanhVien_DAL::update($e);
        return ['success' => true, 'message' => 'Cập nhật thành công'];
    }

    public static function trash(int $id, int $u): array
    {
        NCKH_ThanhVien_DAL::trash($id, $u);
        return ['success' => true, 'message' => 'Đã xóa thành viên'];
    }

    public static function getById(int $id): ?NCKH_ThanhVien_DTO { return NCKH_ThanhVien_DAL::getById($id); }
    public static function getByDeTai(int $deTaiId): array { return NCKH_ThanhVien_DAL::getByDeTai($deTaiId); }
}
