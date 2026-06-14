<?php
require_once __DIR__ . '/../DAL/DT_ChuongTrinhMonHoc_DAL.php';
require_once __DIR__ . '/../DAL/DT_ChuongTrinh_DAL.php';
require_once __DIR__ . '/../DAL/DT_MonHoc_DAL.php';
require_once __DIR__ . '/../DAL/DM_NhatKyHeThong_DAL.php';

class DT_ChuongTrinhMonHoc_BUS
{
    const MODULE_KEY = 'DT_ChuongTrinhMonHoc';

    public static function listByChuongTrinh(int $ctId): array
    {
        if ($ctId <= 0) return ['items' => [], 'summary' => self::emptySummary(), 'chuong_trinh' => null];
        $ct = DT_ChuongTrinh_DAL::getById($ctId);
        return [
            'items' => DT_ChuongTrinhMonHoc_DAL::listByChuongTrinh($ctId),
            'summary' => DT_ChuongTrinhMonHoc_DAL::summaryByChuongTrinh($ctId),
            'chuong_trinh' => $ct,
        ];
    }

    /** Danh sách CTĐT có gắn 1 môn học (chiều ngược, dùng cho UI Môn học). */
    public static function listByMonHoc(int $monHocId): array
    {
        if ($monHocId <= 0) return [];
        return DT_ChuongTrinhMonHoc_DAL::listByMonHoc($monHocId);
    }

    private static function emptySummary(): array
    {
        return ['so_mon' => 0, 'tong_tiet' => 0, 'tong_tin_chi' => 0, 'so_bat_buoc' => 0];
    }

    public static function addMonHoc(int $ctId, int $monHocId, int $batBuoc, int $u): array
    {
        if ($ctId <= 0 || $monHocId <= 0) {
            return ['success' => false, 'message' => 'Thiếu dữ liệu chương trình hoặc môn học'];
        }
        $ct = DT_ChuongTrinh_DAL::getById($ctId);
        if (!$ct || $ct->da_xoa == 1) return ['success' => false, 'message' => 'Chương trình không hợp lệ'];
        $mh = DT_MonHoc_DAL::getById($monHocId);
        if (!$mh || $mh->da_xoa == 1) return ['success' => false, 'message' => 'Môn học không hợp lệ'];
        if (DT_ChuongTrinhMonHoc_DAL::exists($ctId, $monHocId)) {
            return ['success' => false, 'message' => 'Môn học này đã có trong chương trình'];
        }
        $e = new DT_ChuongTrinhMonHoc_PUBLIC();
        $e->chuong_trinh_id = $ctId;
        $e->mon_hoc_id = $monHocId;
        $e->bat_buoc = $batBuoc ? 1 : 0;
        $e->trang_thai = 1;
        $e->thu_tu = DT_ChuongTrinhMonHoc_DAL::getNextThuTu($ctId);
        $e->nguoi_tao = $u;
        $id = DT_ChuongTrinhMonHoc_DAL::insert($e);
        DM_NhatKyHeThong_DAL::log(
            $u, self::MODULE_KEY,
            "Thêm môn '{$mh->ten_mon_hoc}' vào CTĐT '{$ct->ten_chuong_trinh}'",
            'DT_CHUONG_TRINH_MON_HOC', $id
        );
        return ['success' => true, 'message' => 'Đã thêm môn vào chương trình', 'data' => ['id' => $id]];
    }

    public static function toggleBatBuoc(int $id, int $batBuoc, int $u): array
    {
        $row = DT_ChuongTrinhMonHoc_DAL::getById($id);
        if (!$row) return ['success' => false, 'message' => 'Không tìm thấy bản ghi'];
        DT_ChuongTrinhMonHoc_DAL::updateBatBuoc($id, $batBuoc ? 1 : 0, $u);
        return ['success' => true, 'message' => $batBuoc ? 'Đã đặt bắt buộc' : 'Đã đặt tự chọn'];
    }

    public static function move(int $id, string $dir, int $u): array
    {
        if (!in_array($dir, ['up', 'down'], true)) return ['success' => false, 'message' => 'Hướng không hợp lệ'];
        $cur = DT_ChuongTrinhMonHoc_DAL::getById($id);
        if (!$cur) return ['success' => false, 'message' => 'Không tìm thấy'];
        $nb = DT_ChuongTrinhMonHoc_DAL::getNeighbor((int)$cur['chuong_trinh_id'], (int)$cur['thu_tu'], $dir);
        if (!$nb) return ['success' => false, 'message' => 'Đã ở vị trí ngoài cùng'];
        try {
            Database::beginTransaction();
            DT_ChuongTrinhMonHoc_DAL::updateThuTu((int)$cur['id'], (int)$nb['thu_tu'], $u);
            DT_ChuongTrinhMonHoc_DAL::updateThuTu((int)$nb['id'], (int)$cur['thu_tu'], $u);
            Database::commit();
        } catch (Throwable $ex) {
            Database::rollBack();
            return ['success' => false, 'message' => 'Lỗi đổi thứ tự: ' . $ex->getMessage()];
        }
        return ['success' => true, 'message' => 'Đã đổi thứ tự'];
    }

    public static function remove(int $id, int $u): array
    {
        $row = DT_ChuongTrinhMonHoc_DAL::getById($id);
        if (!$row) return ['success' => false, 'message' => 'Không tìm thấy'];
        DT_ChuongTrinhMonHoc_DAL::remove($id);
        DM_NhatKyHeThong_DAL::log($u, self::MODULE_KEY, "Gỡ môn #{$row['mon_hoc_id']} khỏi CTĐT #{$row['chuong_trinh_id']}", 'DT_CHUONG_TRINH_MON_HOC', $id);
        return ['success' => true, 'message' => 'Đã gỡ môn khỏi chương trình'];
    }
}
