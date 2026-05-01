<?php
require_once __DIR__ . '/../DAL/DT_KhoaHocMonHoc_DAL.php';
require_once __DIR__ . '/../DAL/DT_KhoaHoc_DAL.php';
require_once __DIR__ . '/../DAL/DT_MonHoc_DAL.php';
require_once __DIR__ . '/../DAL/DM_NhatKyHeThong_DAL.php';

class DT_KhoaHocMonHoc_BUS
{
    const MODULE_KEY = 'DT_KhoaHocMonHoc';

    public static function listByKhoaHoc(int $khoaHocId): array
    {
        if ($khoaHocId <= 0) return ['items' => [], 'summary' => self::emptySummary(), 'khoa_hoc' => null];
        $kh = DT_KhoaHoc_DAL::getById($khoaHocId);
        return [
            'items' => DT_KhoaHocMonHoc_DAL::listByKhoaHoc($khoaHocId),
            'summary' => DT_KhoaHocMonHoc_DAL::summaryByKhoaHoc($khoaHocId),
            'khoa_hoc' => $kh,
        ];
    }

    /** Lấy danh sách khóa học có gắn 1 môn học (chiều ngược, dùng cho UI Môn học). */
    public static function listByMonHoc(int $monHocId): array
    {
        if ($monHocId <= 0) return [];
        return DT_KhoaHocMonHoc_DAL::listByMonHoc($monHocId);
    }

    private static function emptySummary(): array
    {
        return ['so_mon' => 0, 'tong_tiet' => 0, 'tong_tin_chi' => 0, 'so_bat_buoc' => 0];
    }

    public static function addMonHoc(int $khoaHocId, int $monHocId, int $batBuoc, int $u): array
    {
        if ($khoaHocId <= 0 || $monHocId <= 0) {
            return ['success' => false, 'message' => 'Thiếu dữ liệu khóa học hoặc môn học'];
        }
        $kh = DT_KhoaHoc_DAL::getById($khoaHocId);
        if (!$kh || $kh->da_xoa == 1) return ['success' => false, 'message' => 'Khóa học không hợp lệ'];
        $mh = DT_MonHoc_DAL::getById($monHocId);
        if (!$mh || $mh->da_xoa == 1) return ['success' => false, 'message' => 'Môn học không hợp lệ'];
        if (DT_KhoaHocMonHoc_DAL::exists($khoaHocId, $monHocId)) {
            return ['success' => false, 'message' => 'Môn học này đã có trong khóa học'];
        }
        $e = new DT_KhoaHocMonHoc_PUBLIC();
        $e->khoa_hoc_id = $khoaHocId;
        $e->mon_hoc_id = $monHocId;
        $e->bat_buoc = $batBuoc ? 1 : 0;
        $e->trang_thai = 1;
        $e->thu_tu = DT_KhoaHocMonHoc_DAL::getNextThuTu($khoaHocId);
        $e->nguoi_tao = $u;
        $id = DT_KhoaHocMonHoc_DAL::insert($e);
        DM_NhatKyHeThong_DAL::log(
            $u, self::MODULE_KEY,
            "Thêm môn '{$mh->ten_mon_hoc}' vào khóa '{$kh->ten_khoa_hoc}'",
            'DT_KHOA_HOC_MON_HOC', $id
        );
        return ['success' => true, 'message' => 'Đã thêm môn vào khóa học', 'data' => ['id' => $id]];
    }

    public static function toggleBatBuoc(int $id, int $batBuoc, int $u): array
    {
        $row = DT_KhoaHocMonHoc_DAL::getById($id);
        if (!$row) return ['success' => false, 'message' => 'Không tìm thấy bản ghi'];
        DT_KhoaHocMonHoc_DAL::updateBatBuoc($id, $batBuoc ? 1 : 0, $u);
        return ['success' => true, 'message' => $batBuoc ? 'Đã đặt bắt buộc' : 'Đã đặt tự chọn'];
    }

    public static function move(int $id, string $dir, int $u): array
    {
        if (!in_array($dir, ['up', 'down'], true)) return ['success' => false, 'message' => 'Hướng không hợp lệ'];
        $cur = DT_KhoaHocMonHoc_DAL::getById($id);
        if (!$cur) return ['success' => false, 'message' => 'Không tìm thấy'];
        $nb = DT_KhoaHocMonHoc_DAL::getNeighbor((int)$cur['khoa_hoc_id'], (int)$cur['thu_tu'], $dir);
        if (!$nb) return ['success' => false, 'message' => 'Đã ở vị trí ngoài cùng'];
        try {
            Database::beginTransaction();
            DT_KhoaHocMonHoc_DAL::updateThuTu((int)$cur['id'], (int)$nb['thu_tu'], $u);
            DT_KhoaHocMonHoc_DAL::updateThuTu((int)$nb['id'], (int)$cur['thu_tu'], $u);
            Database::commit();
        } catch (Throwable $ex) {
            Database::rollBack();
            return ['success' => false, 'message' => 'Lỗi đổi thứ tự: ' . $ex->getMessage()];
        }
        return ['success' => true, 'message' => 'Đã đổi thứ tự'];
    }

    public static function remove(int $id, int $u): array
    {
        $row = DT_KhoaHocMonHoc_DAL::getById($id);
        if (!$row) return ['success' => false, 'message' => 'Không tìm thấy'];
        DT_KhoaHocMonHoc_DAL::remove($id);
        DM_NhatKyHeThong_DAL::log($u, self::MODULE_KEY, "Gỡ môn #{$row['mon_hoc_id']} khỏi khóa #{$row['khoa_hoc_id']}", 'DT_KHOA_HOC_MON_HOC', $id);
        return ['success' => true, 'message' => 'Đã gỡ môn khỏi khóa học'];
    }
}
