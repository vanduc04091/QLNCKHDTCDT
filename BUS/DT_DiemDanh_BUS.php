<?php
require_once __DIR__ . '/../DAL/DT_DiemDanh_DAL.php';
require_once __DIR__ . '/../DAL/DT_LichHoc_DAL.php';
require_once __DIR__ . '/../DAL/DM_NhatKyHeThong_DAL.php';

class DT_DiemDanh_BUS
{
    const MODULE_KEY = 'DT_DiemDanh';

    /** Mở phiên: đảm bảo đã có bản ghi cho mọi học viên của lớp, trả về danh sách + thống kê */
    public static function openSession(int $lichHocId, int $userId): array
    {
        $lich = DT_LichHoc_DAL::getById($lichHocId);
        if (!$lich) return ['success' => false, 'message' => 'Không tìm thấy buổi học'];
        $created = DT_DiemDanh_DAL::ensureForLich($lichHocId, $lich->lop_hoc_id, $userId);
        $list = DT_DiemDanh_DAL::getByLich($lichHocId);
        $stats = DT_DiemDanh_DAL::countByLich($lichHocId);
        if ($created > 0) {
            DM_NhatKyHeThong_DAL::log($userId, Constants::MODULE_HE_THONG,
                "Khởi tạo điểm danh buổi id={$lichHocId} ({$created} hv)", 'DT_DIEM_DANH', $lichHocId);
        }
        return [
            'success' => true,
            'message' => 'OK',
            'data' => [
                'lich' => $lich,
                'items' => $list,
                'stats' => $stats,
                'created' => $created,
            ],
        ];
    }

    public static function listByLich(int $lichHocId, string $search = ''): array
    {
        return DT_DiemDanh_DAL::getByLich($lichHocId, $search);
    }

    public static function saveBulk(int $lichHocId, array $items, int $userId): array
    {
        if (!$items) return ['success' => false, 'message' => 'Không có dữ liệu'];
        // Validate item
        foreach ($items as $it) {
            $tt = (int)($it['trang_thai'] ?? 1);
            if ($tt < 0 || $tt > 3) return ['success' => false, 'message' => 'Trạng thái không hợp lệ'];
        }
        $n = DT_DiemDanh_DAL::updateStatusBulk($lichHocId, $items, $userId);
        DM_NhatKyHeThong_DAL::log($userId, Constants::MODULE_HE_THONG,
            "Lưu điểm danh buổi id={$lichHocId} ({$n} dòng cập nhật)", 'DT_DIEM_DANH', $lichHocId);
        return ['success' => true, 'message' => "Đã lưu {$n} dòng", 'data' => ['updated' => $n]];
    }

    public static function markAllPresent(int $lichHocId, int $userId): array
    {
        $n = DT_DiemDanh_DAL::markAllPresent($lichHocId, $userId);
        return ['success' => true, 'message' => "Đã đánh dấu {$n} học viên có mặt"];
    }

    public static function statsByLich(int $lichHocId): array
    {
        return DT_DiemDanh_DAL::countByLich($lichHocId);
    }

    public static function historyByHvl(int $hvlId): array
    {
        return [
            'items' => DT_DiemDanh_DAL::getByHocVienLop($hvlId),
            'stats' => DT_DiemDanh_DAL::statsByHvl($hvlId),
        ];
    }

    public static function lichByLop(int $lopId, string $from = '', string $to = ''): array
    {
        return DT_DiemDanh_DAL::getLichByLop($lopId, $from, $to);
    }
}
