<?php
require_once __DIR__ . '/../DAL/NCKH_NhacViec_DAL.php';
require_once __DIR__ . '/../DAL/DM_CauHinh_DAL.php';

class NCKH_NhacViec_BUS
{
    public static function insert(NCKH_NhacViec_PUBLIC $e): array
    {
        if ($e->de_tai_id <= 0) return ['success' => false, 'message' => 'Thiếu đề tài'];
        if (trim($e->tieu_de) === '') return ['success' => false, 'message' => 'Tiêu đề không được để trống'];
        if ($e->ngay_nhac === '') return ['success' => false, 'message' => 'Ngày nhắc không được để trống'];
        $id = NCKH_NhacViec_DAL::insert($e);
        return ['success' => true, 'message' => 'Đã tạo nhắc việc', 'data' => ['id' => $id]];
    }

    public static function update(NCKH_NhacViec_PUBLIC $e): array
    {
        if (!$e->id) return ['success' => false, 'message' => 'Thiếu ID'];
        NCKH_NhacViec_DAL::update($e);
        return ['success' => true, 'message' => 'Cập nhật thành công'];
    }

    public static function trash(int $id, int $u): array
    {
        NCKH_NhacViec_DAL::trash($id, $u);
        return ['success' => true, 'message' => 'Đã xóa nhắc việc'];
    }

    public static function getById(int $id): ?NCKH_NhacViec_DTO { return NCKH_NhacViec_DAL::getById($id); }
    public static function getByDeTai(int $deTaiId): array { return NCKH_NhacViec_DAL::getByDeTai($deTaiId); }
    public static function getDueUnsent(int $limit = 50): array { return NCKH_NhacViec_DAL::getDueUnsent($limit); }
    public static function markSent(int $id, string $ketQua): int { return NCKH_NhacViec_DAL::markSent($id, $ketQua); }
    public static function getPaged(int $page, int $pageSize, int $daGui = -1, int $daXoa = 0): array { return NCKH_NhacViec_DAL::getPaged($page, $pageSize, $daGui, $daXoa); }
}
