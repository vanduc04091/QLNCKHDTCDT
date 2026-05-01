<?php
require_once __DIR__ . '/../DAL/NCKH_TienDo_DAL.php';
require_once __DIR__ . '/../DAL/DM_NhatKyHeThong_DAL.php';

class NCKH_TienDo_BUS
{
    const MODULE_KEY = 'NCKH_TienDo';

    public static function insert(NCKH_TienDo_PUBLIC $e): array
    {
        if ($e->de_tai_id <= 0) return ['success' => false, 'message' => 'Thiếu đề tài'];
        if (trim($e->ky_bao_cao) === '') return ['success' => false, 'message' => 'Kỳ báo cáo không được để trống'];
        if ($e->ngay_bao_cao === '') return ['success' => false, 'message' => 'Ngày báo cáo không được để trống'];
        if ($e->phan_tram_hoan_thanh < 0 || $e->phan_tram_hoan_thanh > 100) {
            return ['success' => false, 'message' => '% hoàn thành phải từ 0 đến 100'];
        }
        $id = NCKH_TienDo_DAL::insert($e);
        DM_NhatKyHeThong_DAL::log($e->nguoi_tao ?? 0, self::MODULE_KEY, "Thêm tiến độ {$e->ky_bao_cao} (đề tài #{$e->de_tai_id})", 'NCKH_TIEN_DO', $id);
        return ['success' => true, 'message' => 'Đã ghi nhận báo cáo tiến độ', 'data' => ['id' => $id]];
    }

    public static function update(NCKH_TienDo_PUBLIC $e): array
    {
        if (!$e->id) return ['success' => false, 'message' => 'Thiếu ID'];
        if ($e->phan_tram_hoan_thanh < 0 || $e->phan_tram_hoan_thanh > 100) {
            return ['success' => false, 'message' => '% hoàn thành phải từ 0 đến 100'];
        }
        NCKH_TienDo_DAL::update($e);
        return ['success' => true, 'message' => 'Cập nhật thành công'];
    }

    public static function trash(int $id, int $u): array
    {
        NCKH_TienDo_DAL::trash($id, $u);
        return ['success' => true, 'message' => 'Đã xóa báo cáo'];
    }

    public static function getById(int $id): ?NCKH_TienDo_DTO { return NCKH_TienDo_DAL::getById($id); }
    public static function getByDeTai(int $deTaiId): array { return NCKH_TienDo_DAL::getByDeTai($deTaiId); }
    public static function getOverdue(int $days = 90): array { return NCKH_TienDo_DAL::getOverdueReports($days); }
}
