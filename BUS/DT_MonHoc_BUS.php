<?php
require_once __DIR__ . '/../DAL/DT_MonHoc_DAL.php';
require_once __DIR__ . '/../DAL/DM_NhatKyHeThong_DAL.php';

class DT_MonHoc_BUS
{
    const MODULE_KEY = 'DT_MonHoc';

    public static function insert(DT_MonHoc_PUBLIC $e): array
    {
        $e->ma_mon_hoc = trim($e->ma_mon_hoc);
        $e->ten_mon_hoc = trim($e->ten_mon_hoc);
        if ($e->ma_mon_hoc === '' || $e->ten_mon_hoc === '') {
            return ['success' => false, 'message' => 'Mã và tên môn học không được để trống'];
        }
        if (DT_MonHoc_DAL::checkMaExists($e->ma_mon_hoc)) {
            return ['success' => false, 'message' => 'Mã bài học đã tồn tại'];
        }
        $e->tong_so_tiet = $e->so_tiet_ly_thuyet + $e->so_tiet_thuc_hanh;
        if (!$e->chuong_trinh_id) $e->chuong_trinh_id = null;
        // Tự gán thứ tự kế tiếp trong CTĐT nếu chưa nhập
        if ($e->thu_tu <= 0 && $e->chuong_trinh_id) {
            $e->thu_tu = DT_MonHoc_DAL::getMaxThuTuByChuongTrinh($e->chuong_trinh_id) + 1;
        }
        $id = DT_MonHoc_DAL::insert($e);
        MemcachedHelper::deleteByPrefix('dt_mon_hoc:');
        DM_NhatKyHeThong_DAL::log($e->nguoi_tao ?? 0, self::MODULE_KEY, "Thêm môn học: {$e->ten_mon_hoc}", 'DT_MON_HOC', $id);
        return ['success' => true, 'message' => 'Thêm môn học thành công', 'data' => ['id' => $id]];
    }

    public static function update(DT_MonHoc_PUBLIC $e): array
    {
        if (!$e->id) return ['success' => false, 'message' => 'Thiếu ID'];
        $e->ma_mon_hoc = trim($e->ma_mon_hoc);
        $e->ten_mon_hoc = trim($e->ten_mon_hoc);
        if ($e->ma_mon_hoc === '' || $e->ten_mon_hoc === '') {
            return ['success' => false, 'message' => 'Mã và tên môn học không được để trống'];
        }
        if (DT_MonHoc_DAL::checkMaExists($e->ma_mon_hoc, $e->id)) {
            return ['success' => false, 'message' => 'Mã bài học đã tồn tại'];
        }
        $e->tong_so_tiet = $e->so_tiet_ly_thuyet + $e->so_tiet_thuc_hanh;
        if (!$e->chuong_trinh_id) $e->chuong_trinh_id = null;
        if ($e->thu_tu <= 0 && $e->chuong_trinh_id) {
            $e->thu_tu = DT_MonHoc_DAL::getMaxThuTuByChuongTrinh($e->chuong_trinh_id) + 1;
        }
        DT_MonHoc_DAL::update($e);
        MemcachedHelper::deleteByPrefix('dt_mon_hoc:');
        DM_NhatKyHeThong_DAL::log($e->nguoi_cap_nhat ?? 0, self::MODULE_KEY, "Cập nhật môn học: {$e->ten_mon_hoc}", 'DT_MON_HOC', $e->id);
        return ['success' => true, 'message' => 'Cập nhật thành công'];
    }

    public static function trash(int $id, int $u): array
    {
        $used = DT_MonHoc_DAL::isUsedInKhoaHoc($id);
        if ($used > 0) {
            return ['success' => false, 'message' => "Bài học đang thuộc một chương trình đào tạo. Hãy bỏ khỏi chương trình (sửa bài, để trống CTĐT) trước khi xóa."];
        }
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
            return ['success' => false, 'message' => "Không thể xóa vĩnh viễn: bài đang thuộc một chương trình đào tạo."];
        }
        DT_MonHoc_DAL::delete($id);
        MemcachedHelper::deleteByPrefix('dt_mon_hoc:');
        return ['success' => true, 'message' => 'Đã xóa vĩnh viễn'];
    }

    public static function getById(int $id): ?DT_MonHoc_DTO
    {
        return DT_MonHoc_DAL::getById($id);
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

    /** Bài học của 1 CTĐT (theo thứ tự) — cho tab Bài học ở màn CTĐT. */
    public static function getByChuongTrinh(int $chuongTrinhId): array
    {
        return DT_MonHoc_DAL::getByChuongTrinh($chuongTrinhId);
    }

    /** Combo bài học chưa thuộc CTĐT nào. */
    public static function getChuaGanCombo(): array
    {
        return DT_MonHoc_DAL::getChuaGanCombo();
    }

    /** Gán 1 bài vào CTĐT. */
    public static function assignToChuongTrinh(int $monHocId, int $chuongTrinhId, int $u): array
    {
        if ($monHocId <= 0 || $chuongTrinhId <= 0) {
            return ['success' => false, 'message' => 'Thiếu bài học hoặc chương trình'];
        }
        $mh = DT_MonHoc_DAL::getById($monHocId);
        if (!$mh) return ['success' => false, 'message' => 'Không tìm thấy bài học'];
        if ($mh->chuong_trinh_id) {
            return ['success' => false, 'message' => 'Bài học đã thuộc một chương trình khác'];
        }
        DT_MonHoc_DAL::assignToChuongTrinh($monHocId, $chuongTrinhId, $u);
        MemcachedHelper::deleteByPrefix('dt_mon_hoc:');
        return ['success' => true, 'message' => 'Đã thêm bài học vào chương trình'];
    }

    /** Bỏ 1 bài khỏi CTĐT. */
    public static function unassign(int $monHocId, int $u): array
    {
        DT_MonHoc_DAL::unassign($monHocId, $u);
        MemcachedHelper::deleteByPrefix('dt_mon_hoc:');
        return ['success' => true, 'message' => 'Đã bỏ bài học khỏi chương trình'];
    }

    /** Đổi thứ tự lên/xuống trong cùng CTĐT (hoán đổi thu_tu với bài liền kề). */
    public static function move(int $id, string $dir, int $u): array
    {
        $cur = DT_MonHoc_DAL::getById($id);
        if (!$cur || !$cur->chuong_trinh_id) {
            return ['success' => false, 'message' => 'Bài học không thuộc chương trình nào'];
        }
        $list = DT_MonHoc_DAL::getByChuongTrinh($cur->chuong_trinh_id);
        $idx = null;
        foreach ($list as $i => $r) { if ((int)$r['id'] === $id) { $idx = $i; break; } }
        if ($idx === null) return ['success' => false, 'message' => 'Không tìm thấy bài học'];
        $swap = $dir === 'up' ? $idx - 1 : $idx + 1;
        if ($swap < 0 || $swap >= count($list)) {
            return ['success' => false, 'message' => 'Đã ở vị trí đầu/cuối'];
        }
        $a = $list[$idx]; $b = $list[$swap];
        DT_MonHoc_DAL::updateThuTu((int)$a['id'], (int)$b['thu_tu'], $u);
        DT_MonHoc_DAL::updateThuTu((int)$b['id'], (int)$a['thu_tu'], $u);
        MemcachedHelper::deleteByPrefix('dt_mon_hoc:');
        return ['success' => true, 'message' => 'Đã đổi thứ tự'];
    }
}
