<?php
require_once __DIR__ . '/../DAL/NCKH_HoiDong_DAL.php';

class NCKH_HoiDong_BUS
{
    public static function insert(NCKH_HoiDong_PUBLIC $e): array
    {
        if ($e->de_tai_id <= 0) return ['success' => false, 'message' => 'Thiếu đề tài'];
        if (trim($e->ho_ten) === '') return ['success' => false, 'message' => 'Họ tên không được để trống'];
        $id = NCKH_HoiDong_DAL::insert($e);
        return ['success' => true, 'message' => 'Đã thêm thành viên hội đồng', 'data' => ['id' => $id]];
    }

    public static function update(NCKH_HoiDong_PUBLIC $e): array
    {
        if (!$e->id) return ['success' => false, 'message' => 'Thiếu ID'];
        if (trim($e->ho_ten) === '') return ['success' => false, 'message' => 'Họ tên không được để trống'];
        NCKH_HoiDong_DAL::update($e);
        return ['success' => true, 'message' => 'Cập nhật thành công'];
    }

    public static function trash(int $id, int $u): array
    {
        NCKH_HoiDong_DAL::trash($id, $u);
        return ['success' => true, 'message' => 'Đã xóa'];
    }

    public static function getById(int $id): ?NCKH_HoiDong_DTO { return NCKH_HoiDong_DAL::getById($id); }
    public static function getByDeTai(int $deTaiId): array { return NCKH_HoiDong_DAL::getByDeTai($deTaiId); }

    public static function vaiTroText(string $code): string
    {
        return [
            'ChuTich'   => 'Chủ tịch',
            'ThuKy'     => 'Thư ký',
            'PhanBien1' => 'Phản biện 1',
            'PhanBien2' => 'Phản biện 2',
            'ThanhVien' => 'Thành viên',
        ][$code] ?? $code;
    }
}
