<?php
/**
 * Constants - Hằng số hệ thống
 */
class Constants
{
    // === Response Codes ===
    const SUCCESS = 1;
    const FAIL = 0;

    // === Soft Delete ===
    const NOT_DELETED = 0;
    const DELETED = 1;

    // === Trạng thái chung ===
    const TRANG_THAI_NGUNG = 0;
    const TRANG_THAI_HOAT_DONG = 1;

    // === Trạng thái người dùng ===
    const NGUOI_DUNG_KHOA = 0;
    const NGUOI_DUNG_HOAT_DONG = 1;

    // === Quyền ===
    const QUYEN_KHONG = 0;
    const QUYEN_CO = 1;

    // === Loại đơn vị (Khoa/Phòng) ===
    const LOAI_KHOA = 'Khoa';
    const LOAI_PHONG = 'Phong';
    const LOAI_TRUNG_TAM = 'TrungTam';

    public static function getLoaiDonViList(): array
    {
        return [
            self::LOAI_KHOA => 'Khoa',
            self::LOAI_PHONG => 'Phòng',
            self::LOAI_TRUNG_TAM => 'Trung tâm',
        ];
    }

    // === Giới tính ===
    const GIOI_TINH_NAM = 'Nam';
    const GIOI_TINH_NU = 'Nu';
    const GIOI_TINH_KHAC = 'Khac';

    public static function getGioiTinhList(): array
    {
        return [
            self::GIOI_TINH_NAM => 'Nam',
            self::GIOI_TINH_NU => 'Nữ',
            self::GIOI_TINH_KHAC => 'Khác',
        ];
    }

    // === Cấp bệnh viện ===
    const CAP_BV_TW = 'TuyenTW';
    const CAP_BV_TINH = 'TuyenTinh';
    const CAP_BV_HUYEN = 'TuyenHuyen';
    const CAP_BV_XA = 'TuyenXa';

    public static function getCapBenhVienList(): array
    {
        return [
            self::CAP_BV_TW => 'Tuyến Trung ương',
            self::CAP_BV_TINH => 'Tuyến Tỉnh',
            self::CAP_BV_HUYEN => 'Tuyến Huyện',
            self::CAP_BV_XA => 'Tuyến Xã',
        ];
    }

    // === Module nhật ký ===
    const MODULE_NCKH = 'NCKH';
    const MODULE_DAO_TAO = 'DaoTao';
    const MODULE_CHI_DAO_TUYEN = 'ChiDaoTuyen';
    const MODULE_HE_THONG = 'HeThong';

    // === Cache TTL (giây) ===
    const CACHE_TTL_LIST = 300;       // 5 phút
    const CACHE_TTL_DETAIL = 600;     // 10 phút
    const CACHE_TTL_COMBO = 1800;     // 30 phút
    const CACHE_TTL_PHAN_QUYEN = 900; // 15 phút
    const CACHE_TTL_DASHBOARD = 120;  // 2 phút
}
