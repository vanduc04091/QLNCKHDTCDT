<?php
require_once __DIR__ . '/../DAL/DT_KhoaHocChuongTrinh_DAL.php';
require_once __DIR__ . '/../DAL/DM_NhatKyHeThong_DAL.php';

class DT_KhoaHocChuongTrinh_BUS
{
    const MODULE_KEY = 'DT_ChuongTrinh';

    public static function getCombo(): array { return DT_KhoaHocChuongTrinh_DAL::getCombo(); }
    public static function getByChuongTrinh(int $ctId): array { return DT_KhoaHocChuongTrinh_DAL::getByChuongTrinh($ctId); }
    public static function getByKhoaHoc(int $khId): array { return DT_KhoaHocChuongTrinh_DAL::getByKhoaHoc($khId); }

    /** Gắn 1 khóa học vào CTĐT (kèm thông tin học vụ của cặp). */
    public static function add(int $khoaHocId, int $chuongTrinhId, int $u, array $info = []): array
    {
        if ($khoaHocId <= 0 || $chuongTrinhId <= 0) {
            return ['success' => false, 'message' => 'Thiếu khóa học hoặc chương trình'];
        }
        if (DT_KhoaHocChuongTrinh_DAL::exists($khoaHocId, $chuongTrinhId)) {
            return ['success' => false, 'message' => 'Khóa học này đã gắn vào chương trình'];
        }
        if (!empty($info['ngay_bat_dau']) && !empty($info['ngay_ket_thuc'])
            && $info['ngay_bat_dau'] > $info['ngay_ket_thuc']) {
            return ['success' => false, 'message' => 'Ngày bắt đầu phải trước ngày kết thúc'];
        }
        $id = DT_KhoaHocChuongTrinh_DAL::insert($khoaHocId, $chuongTrinhId, $u, $info);
        DM_NhatKyHeThong_DAL::log($u, self::MODULE_KEY, "Gắn khóa #{$khoaHocId} vào CTĐT #{$chuongTrinhId}", 'DT_KHOA_HOC_CHUONG_TRINH', $id);
        return ['success' => true, 'message' => 'Đã gắn khóa học', 'data' => ['id' => $id]];
    }

    /** Cập nhật thông tin học vụ của 1 cặp (khóa+CTĐT). */
    public static function updateInfo(int $id, array $info, int $u): array
    {
        $row = DT_KhoaHocChuongTrinh_DAL::getById($id);
        if (!$row) return ['success' => false, 'message' => 'Không tìm thấy liên kết'];
        if (!empty($info['ngay_bat_dau']) && !empty($info['ngay_ket_thuc'])
            && $info['ngay_bat_dau'] > $info['ngay_ket_thuc']) {
            return ['success' => false, 'message' => 'Ngày bắt đầu phải trước ngày kết thúc'];
        }
        DT_KhoaHocChuongTrinh_DAL::updateInfo($id, $info, $u);
        return ['success' => true, 'message' => 'Đã cập nhật thông tin'];
    }

    /** Gỡ 1 cặp (khóa, CTĐT). */
    public static function remove(int $id, int $u): array
    {
        $row = DT_KhoaHocChuongTrinh_DAL::getById($id);
        if (!$row) return ['success' => false, 'message' => 'Không tìm thấy liên kết'];
        if (DT_KhoaHocChuongTrinh_DAL::countReferences($id) > 0) {
            return ['success' => false, 'message' => 'Đang có dữ liệu học vụ (học viên/lịch/chứng chỉ…) — không thể gỡ'];
        }
        DT_KhoaHocChuongTrinh_DAL::softDelete($id, $u);
        DM_NhatKyHeThong_DAL::log($u, self::MODULE_KEY, "Gỡ liên kết khóa-CTĐT #{$id}", 'DT_KHOA_HOC_CHUONG_TRINH', $id);
        return ['success' => true, 'message' => 'Đã gỡ khóa học khỏi chương trình'];
    }
}
