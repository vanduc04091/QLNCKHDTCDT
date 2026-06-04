<?php
require_once __DIR__ . '/../DAL/DT_DotGiaiDoan_DAL.php';

class DT_DotGiaiDoan_BUS
{
    public static function getByDot(int $dotId): array { return DT_DotGiaiDoan_DAL::getByDot($dotId); }
    public static function getById(int $id): ?DT_DotGiaiDoan_DTO { return DT_DotGiaiDoan_DAL::getById($id); }

    public static function insert(DT_DotGiaiDoan_PUBLIC $e): array
    {
        $v = self::validate($e); if (!$v['success']) return $v;
        $id = DT_DotGiaiDoan_DAL::insert($e);
        return ['success' => true, 'message' => 'Đã thêm giai đoạn', 'data' => ['id' => $id]];
    }

    public static function update(DT_DotGiaiDoan_PUBLIC $e): array
    {
        if (!$e->id) return ['success' => false, 'message' => 'Thiếu ID'];
        $v = self::validate($e); if (!$v['success']) return $v;
        DT_DotGiaiDoan_DAL::update($e);
        return ['success' => true, 'message' => 'Đã cập nhật giai đoạn'];
    }

    public static function trash(int $id, int $u): array
    {
        DT_DotGiaiDoan_DAL::trash($id, $u);
        return ['success' => true, 'message' => 'Đã xóa giai đoạn'];
    }

    private static function validate(DT_DotGiaiDoan_PUBLIC $e): array
    {
        $e->ten_giai_doan = trim($e->ten_giai_doan);
        if ($e->dot_id <= 0) return ['success' => false, 'message' => 'Thiếu đợt'];
        if ($e->ten_giai_doan === '') return ['success' => false, 'message' => 'Tên giai đoạn không được trống'];
        if (!in_array($e->hanh_vi, ['Submit', 'Review'], true)) {
            return ['success' => false, 'message' => 'Hành vi không hợp lệ'];
        }
        if (!$e->tu_ngay || !$e->den_ngay) return ['success' => false, 'message' => 'Thiếu thời gian giai đoạn'];
        if (strtotime($e->tu_ngay) >= strtotime($e->den_ngay)) {
            return ['success' => false, 'message' => 'Thời điểm bắt đầu phải trước thời điểm kết thúc'];
        }
        return ['success' => true];
    }
}
