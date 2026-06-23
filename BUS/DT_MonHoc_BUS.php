<?php
require_once __DIR__ . '/../DAL/DT_MonHoc_DAL.php';
require_once __DIR__ . '/../DAL/DT_ChuongTrinhMonHoc_DAL.php';
require_once __DIR__ . '/../DAL/DM_NhatKyHeThong_DAL.php';

class DT_MonHoc_BUS
{
    const MODULE_KEY = 'DT_MonHoc';

    /** Đồng bộ tập CTĐT của 1 bài học (thêm cặp mới, gỡ cặp bỏ chọn). */
    private static function syncChuongTrinh(int $monHocId, array $ctIds, int $u): void
    {
        $ctIds = array_values(array_unique(array_filter(array_map('intval', $ctIds))));
        $hienCo = DT_MonHoc_DAL::getChuongTrinhIds($monHocId); // [ctId,...]
        // Thêm cặp mới
        foreach (array_diff($ctIds, $hienCo) as $ctId) {
            $e = new DT_ChuongTrinhMonHoc_PUBLIC();
            $e->chuong_trinh_id = $ctId;
            $e->mon_hoc_id = $monHocId;
            $e->thu_tu = DT_ChuongTrinhMonHoc_DAL::getNextThuTu($ctId);
            $e->bat_buoc = 1;
            $e->trang_thai = 1;
            $e->nguoi_tao = $u;
            DT_ChuongTrinhMonHoc_DAL::insert($e);
        }
        // Gỡ cặp không còn chọn
        foreach (array_diff($hienCo, $ctIds) as $ctId) {
            $row = DT_ChuongTrinhMonHoc_DAL::findByPair($ctId, $monHocId);
            if ($row) DT_ChuongTrinhMonHoc_DAL::remove((int)$row['id']);
        }
    }

    public static function insert(DT_MonHoc_PUBLIC $e, array $chuongTrinhIds = []): array
    {
        $e->ma_mon_hoc = trim($e->ma_mon_hoc);
        $e->ten_mon_hoc = trim($e->ten_mon_hoc);
        if ($e->ma_mon_hoc === '' || $e->ten_mon_hoc === '') {
            return ['success' => false, 'message' => 'Mã và tên bài học không được để trống'];
        }
        if (DT_MonHoc_DAL::checkMaExists($e->ma_mon_hoc)) {
            return ['success' => false, 'message' => 'Mã bài học đã tồn tại'];
        }
        $e->tong_so_tiet = $e->so_tiet_ly_thuyet + $e->so_tiet_thuc_hanh;
        $id = DT_MonHoc_DAL::insert($e);
        self::syncChuongTrinh($id, $chuongTrinhIds, $e->nguoi_tao ?? 0);
        MemcachedHelper::deleteByPrefix('dt_mon_hoc:');
        DM_NhatKyHeThong_DAL::log($e->nguoi_tao ?? 0, self::MODULE_KEY, "Thêm bài học: {$e->ten_mon_hoc}", 'DT_MON_HOC', $id);
        return ['success' => true, 'message' => 'Thêm bài học thành công', 'data' => ['id' => $id]];
    }

    public static function update(DT_MonHoc_PUBLIC $e, array $chuongTrinhIds = []): array
    {
        if (!$e->id) return ['success' => false, 'message' => 'Thiếu ID'];
        $e->ma_mon_hoc = trim($e->ma_mon_hoc);
        $e->ten_mon_hoc = trim($e->ten_mon_hoc);
        if ($e->ma_mon_hoc === '' || $e->ten_mon_hoc === '') {
            return ['success' => false, 'message' => 'Mã và tên bài học không được để trống'];
        }
        if (DT_MonHoc_DAL::checkMaExists($e->ma_mon_hoc, $e->id)) {
            return ['success' => false, 'message' => 'Mã bài học đã tồn tại'];
        }
        $e->tong_so_tiet = $e->so_tiet_ly_thuyet + $e->so_tiet_thuc_hanh;
        DT_MonHoc_DAL::update($e);
        self::syncChuongTrinh($e->id, $chuongTrinhIds, $e->nguoi_cap_nhat ?? 0);
        MemcachedHelper::deleteByPrefix('dt_mon_hoc:');
        DM_NhatKyHeThong_DAL::log($e->nguoi_cap_nhat ?? 0, self::MODULE_KEY, "Cập nhật bài học: {$e->ten_mon_hoc}", 'DT_MON_HOC', $e->id);
        return ['success' => true, 'message' => 'Cập nhật thành công'];
    }

    public static function trash(int $id, int $u): array
    {
        DT_MonHoc_DAL::trash($id, $u);
        MemcachedHelper::deleteByPrefix('dt_mon_hoc:');
        DM_NhatKyHeThong_DAL::log($u, self::MODULE_KEY, "Chuyển môn học #{$id} vào thùng rác", 'DT_MON_HOC', $id);
        return ['success' => true, 'message' => 'Đã chuyển vào thùng rác'];
    }

    public static function restore(int $id, int $u): array
    {
        DT_MonHoc_DAL::restore($id, $u);
        MemcachedHelper::deleteByPrefix('dt_mon_hoc:');
        return ['success' => true, 'message' => 'Đã khôi phục'];
    }

    public static function delete(int $id): array
    {
        $used = DT_MonHoc_DAL::isUsedInKhoaHoc($id);
        if ($used > 0) {
            return ['success' => false, 'message' => "Không thể xóa vĩnh viễn: bài đang thuộc {$used} chương trình đào tạo. Hãy gỡ khỏi các CTĐT trước."];
        }
        DT_MonHoc_DAL::delete($id);
        MemcachedHelper::deleteByPrefix('dt_mon_hoc:');
        return ['success' => true, 'message' => 'Đã xóa vĩnh viễn'];
    }

    public static function getById(int $id): ?DT_MonHoc_DTO
    {
        return DT_MonHoc_DAL::getById($id);
    }

    /** Danh sách CTĐT (id) mà 1 bài đang gắn — preset multi-select khi sửa. */
    public static function getChuongTrinhIds(int $monHocId): array
    {
        return DT_MonHoc_DAL::getChuongTrinhIds($monHocId);
    }

    public static function getPaged(int $page, int $pageSize, string $search = '', int $daXoa = 0, int $trangThai = -1, int $chuongTrinhId = 0): array
    {
        return DT_MonHoc_DAL::getPaged($page, $pageSize, $search, $daXoa, $trangThai, $chuongTrinhId);
    }

    public static function getStats(): array
    {
        return DT_MonHoc_DAL::getStats();
    }

    public static function getCombo(): array
    {
        return DT_MonHoc_DAL::getCombo();
    }

    /** Bài học của 1 CTĐT (theo thứ tự của bảng nối) — cho tab Bài học ở màn CTĐT. */
    public static function getByChuongTrinh(int $chuongTrinhId): array
    {
        return DT_ChuongTrinhMonHoc_DAL::listByChuongTrinh($chuongTrinhId);
    }

    /** Combo bài học chưa gắn vào 1 CTĐT cụ thể (để thêm vào CTĐT đó). */
    public static function getChuaGanCombo(int $chuongTrinhId = 0): array
    {
        $all = DT_MonHoc_DAL::getCombo();
        if ($chuongTrinhId <= 0) return $all;
        $da = [];
        foreach (DT_ChuongTrinhMonHoc_DAL::listByChuongTrinh($chuongTrinhId) as $r) {
            $da[(int)$r['mon_hoc_id']] = true;
        }
        return array_values(array_filter($all, fn($m) => empty($da[(int)$m['id']])));
    }

    /** Gắn 1 bài vào 1 CTĐT (xếp cuối). */
    public static function assignToChuongTrinh(int $monHocId, int $chuongTrinhId, int $u): array
    {
        if ($monHocId <= 0 || $chuongTrinhId <= 0) {
            return ['success' => false, 'message' => 'Thiếu bài học hoặc chương trình'];
        }
        if (DT_ChuongTrinhMonHoc_DAL::exists($chuongTrinhId, $monHocId)) {
            return ['success' => false, 'message' => 'Bài học đã có trong chương trình này'];
        }
        $e = new DT_ChuongTrinhMonHoc_PUBLIC();
        $e->chuong_trinh_id = $chuongTrinhId;
        $e->mon_hoc_id = $monHocId;
        $e->thu_tu = DT_ChuongTrinhMonHoc_DAL::getNextThuTu($chuongTrinhId);
        $e->bat_buoc = 1;
        $e->trang_thai = 1;
        $e->nguoi_tao = $u;
        DT_ChuongTrinhMonHoc_DAL::insert($e);
        MemcachedHelper::deleteByPrefix('dt_mon_hoc:');
        return ['success' => true, 'message' => 'Đã thêm bài học vào chương trình'];
    }

    /** Bỏ 1 cặp (CTĐT, bài) theo id bảng nối. */
    public static function unassign(int $kmId, int $u): array
    {
        DT_ChuongTrinhMonHoc_DAL::remove($kmId);
        MemcachedHelper::deleteByPrefix('dt_mon_hoc:');
        return ['success' => true, 'message' => 'Đã bỏ bài học khỏi chương trình'];
    }

    /** Đổi thứ tự lên/xuống trong 1 CTĐT (theo id bảng nối). */
    public static function move(int $kmId, string $dir, int $u): array
    {
        $cur = DT_ChuongTrinhMonHoc_DAL::getById($kmId);
        if (!$cur) return ['success' => false, 'message' => 'Không tìm thấy liên kết'];
        $neighbor = DT_ChuongTrinhMonHoc_DAL::getNeighbor((int)$cur['chuong_trinh_id'], (int)$cur['thu_tu'], $dir);
        if (!$neighbor) return ['success' => false, 'message' => 'Đã ở vị trí đầu/cuối'];
        DT_ChuongTrinhMonHoc_DAL::updateThuTu((int)$cur['id'], (int)$neighbor['thu_tu'], $u);
        DT_ChuongTrinhMonHoc_DAL::updateThuTu((int)$neighbor['id'], (int)$cur['thu_tu'], $u);
        MemcachedHelper::deleteByPrefix('dt_mon_hoc:');
        return ['success' => true, 'message' => 'Đã đổi thứ tự'];
    }
}
