<?php
require_once __DIR__ . '/../DAL/DT_CmeNhom_DAL.php';
require_once __DIR__ . '/../DAL/DT_CmeLoai_DAL.php';
require_once __DIR__ . '/../DAL/DT_CmeGhiNhan_DAL.php';
require_once __DIR__ . '/../DAL/DM_CauHinh_DAL.php';
require_once __DIR__ . '/../DAL/DM_NhatKyHeThong_DAL.php';

/**
 * DT_Cme_BUS — Nghiệp vụ Theo dõi tín chỉ CME (cập nhật kiến thức y khoa liên tục).
 * Gồm: danh mục quy đổi (nhóm/loại) + sổ ghi nhận + logic quy đổi giờ tín chỉ.
 */
class DT_Cme_BUS
{
    const MODULE_DANH_MUC = 'DT_CME_DanhMuc';
    const MODULE_GHI_NHAN = 'DT_CME';

    const KIEU_HOP_LE = ['theo_tiet', 'co_dinh', 'theo_nam'];

    // Cấu hình ngưỡng (DM_CAU_HINH)
    const CFG_NGUONG_GIO  = 'CME_NGUONG_GIO';   // số giờ tín chỉ tối thiểu / chu kỳ
    const CFG_CHU_KY_NAM  = 'CME_CHU_KY_NAM';   // số năm 1 chu kỳ
    const DEFAULT_NGUONG  = 48;
    const DEFAULT_CHU_KY  = 2;

    // ================= QUY ĐỔI =================

    /**
     * Tính giờ tín chỉ theo loại + số lượng.
     *  - theo_tiet: số tiết × hệ số
     *  - co_dinh  : số lượng × giờ mỗi đơn vị
     *  - theo_nam : khoán theo năm (bỏ qua số lượng)
     */
    public static function tinhGioTinChi(string $kieu, float $giaTri, float $soLuong): float
    {
        switch ($kieu) {
            case 'theo_tiet':
            case 'co_dinh':
                $gio = $soLuong * $giaTri;
                break;
            case 'theo_nam':
                $gio = $giaTri;
                break;
            default:
                $gio = 0;
        }
        return round($gio, 2);
    }

    // ================= NHÓM =================

    public static function nhomInsert(DT_CmeNhom_PUBLIC $e): array
    {
        $e->ten_nhom = trim($e->ten_nhom);
        $e->ma_nhom = trim($e->ma_nhom);
        if ($e->ten_nhom === '') return ['success' => false, 'message' => 'Tên nhóm không được để trống'];
        if ($e->ma_nhom === '') $e->ma_nhom = 'CME_' . strtoupper(substr(md5(microtime()), 0, 6));
        if (DT_CmeNhom_DAL::checkMaExists($e->ma_nhom)) return ['success' => false, 'message' => 'Mã nhóm đã tồn tại'];
        $id = DT_CmeNhom_DAL::insert($e);
        return ['success' => true, 'message' => 'Đã thêm nhóm', 'data' => ['id' => $id]];
    }

    public static function nhomUpdate(DT_CmeNhom_PUBLIC $e): array
    {
        if (!$e->id) return ['success' => false, 'message' => 'Thiếu ID'];
        $e->ten_nhom = trim($e->ten_nhom);
        if ($e->ten_nhom === '') return ['success' => false, 'message' => 'Tên nhóm không được để trống'];
        if (DT_CmeNhom_DAL::checkMaExists($e->ma_nhom, $e->id)) return ['success' => false, 'message' => 'Mã nhóm đã tồn tại'];
        DT_CmeNhom_DAL::update($e);
        return ['success' => true, 'message' => 'Đã cập nhật nhóm'];
    }

    public static function nhomTrash(int $id, int $u): array
    {
        DT_CmeNhom_DAL::trash($id, $u);
        return ['success' => true, 'message' => 'Đã xóa nhóm'];
    }
    public static function nhomRestore(int $id, int $u): array
    {
        DT_CmeNhom_DAL::restore($id, $u);
        return ['success' => true, 'message' => 'Đã khôi phục'];
    }
    public static function nhomGetById(int $id): ?DT_CmeNhom_DTO { return DT_CmeNhom_DAL::getById($id); }
    public static function nhomGetPaged(int $p, int $s, string $q = '', int $dx = 0): array { return DT_CmeNhom_DAL::getPaged($p, $s, $q, $dx); }
    public static function nhomGetCombo(): array { return DT_CmeNhom_DAL::getCombo(); }

    // ================= LOẠI =================

    private static function validateLoai(DT_CmeLoai_PUBLIC $e): array
    {
        $e->ten_loai = trim($e->ten_loai);
        $e->ma_loai = trim($e->ma_loai);
        if ($e->nhom_id <= 0) return ['success' => false, 'message' => 'Chưa chọn nhóm hình thức'];
        if ($e->ten_loai === '') return ['success' => false, 'message' => 'Tên loại không được để trống'];
        if (!in_array($e->kieu_quy_doi, self::KIEU_HOP_LE, true)) return ['success' => false, 'message' => 'Kiểu quy đổi không hợp lệ'];
        if ($e->gia_tri_quy_doi < 0) return ['success' => false, 'message' => 'Giá trị quy đổi phải ≥ 0'];
        return ['success' => true];
    }

    public static function loaiInsert(DT_CmeLoai_PUBLIC $e): array
    {
        $v = self::validateLoai($e);
        if (!$v['success']) return $v;
        if ($e->ma_loai === '') $e->ma_loai = 'CMEL_' . strtoupper(substr(md5(microtime()), 0, 6));
        if (DT_CmeLoai_DAL::checkMaExists($e->ma_loai)) return ['success' => false, 'message' => 'Mã loại đã tồn tại'];
        $id = DT_CmeLoai_DAL::insert($e);
        return ['success' => true, 'message' => 'Đã thêm loại hình thức', 'data' => ['id' => $id]];
    }

    public static function loaiUpdate(DT_CmeLoai_PUBLIC $e): array
    {
        if (!$e->id) return ['success' => false, 'message' => 'Thiếu ID'];
        $v = self::validateLoai($e);
        if (!$v['success']) return $v;
        if (DT_CmeLoai_DAL::checkMaExists($e->ma_loai, $e->id)) return ['success' => false, 'message' => 'Mã loại đã tồn tại'];
        DT_CmeLoai_DAL::update($e);
        return ['success' => true, 'message' => 'Đã cập nhật loại hình thức'];
    }

    public static function loaiTrash(int $id, int $u): array
    {
        DT_CmeLoai_DAL::trash($id, $u);
        return ['success' => true, 'message' => 'Đã xóa loại'];
    }
    public static function loaiRestore(int $id, int $u): array
    {
        DT_CmeLoai_DAL::restore($id, $u);
        return ['success' => true, 'message' => 'Đã khôi phục'];
    }
    public static function loaiGetById(int $id): ?DT_CmeLoai_DTO { return DT_CmeLoai_DAL::getById($id); }
    public static function loaiGetPaged(int $p, int $s, string $q = '', int $dx = 0, int $nhomId = 0): array { return DT_CmeLoai_DAL::getPaged($p, $s, $q, $dx, $nhomId); }
    public static function loaiGetCombo(int $nhomId = 0): array { return DT_CmeLoai_DAL::getCombo($nhomId); }

    // ================= GHI NHẬN =================

    private static function chuanBiGhiNhan(DT_CmeGhiNhan_PUBLIC $e): array
    {
        if ($e->nhan_vien_id <= 0) return ['success' => false, 'message' => 'Chưa chọn nhân viên'];
        if ($e->loai_id <= 0) return ['success' => false, 'message' => 'Chưa chọn loại hình thức'];
        if ($e->nam < 2000 || $e->nam > 2100) return ['success' => false, 'message' => 'Năm không hợp lệ'];
        $loai = DT_CmeLoai_DAL::getById($e->loai_id);
        if (!$loai) return ['success' => false, 'message' => 'Loại hình thức không tồn tại'];
        if ($e->so_luong < 0) return ['success' => false, 'message' => 'Số lượng phải ≥ 0'];
        if ($e->ngay_bat_dau && $e->ngay_ket_thuc && $e->ngay_bat_dau > $e->ngay_ket_thuc) {
            return ['success' => false, 'message' => 'Ngày bắt đầu phải trước ngày kết thúc'];
        }
        // Tính giờ tín chỉ (snapshot)
        $e->gio_tin_chi = self::tinhGioTinChi($loai->kieu_quy_doi, (float)$loai->gia_tri_quy_doi, (float)$e->so_luong);
        return ['success' => true];
    }

    public static function ghiNhanInsert(DT_CmeGhiNhan_PUBLIC $e): array
    {
        $v = self::chuanBiGhiNhan($e);
        if (!$v['success']) return $v;
        $id = DT_CmeGhiNhan_DAL::insert($e);
        DM_NhatKyHeThong_DAL::log($e->nguoi_tao ?? 0, Constants::MODULE_DAO_TAO,
            "Thêm ghi nhận CME (NV #{$e->nhan_vien_id}, {$e->gio_tin_chi} giờ)", 'DT_CME_GHI_NHAN', $id);
        return ['success' => true, 'message' => 'Đã ghi nhận (' . self::fmt($e->gio_tin_chi) . ' giờ tín chỉ)', 'data' => ['id' => $id]];
    }

    public static function ghiNhanUpdate(DT_CmeGhiNhan_PUBLIC $e): array
    {
        if (!$e->id) return ['success' => false, 'message' => 'Thiếu ID'];
        $v = self::chuanBiGhiNhan($e);
        if (!$v['success']) return $v;
        DT_CmeGhiNhan_DAL::update($e);
        return ['success' => true, 'message' => 'Đã cập nhật (' . self::fmt($e->gio_tin_chi) . ' giờ tín chỉ)'];
    }

    public static function ghiNhanTrash(int $id, int $u): array
    {
        DT_CmeGhiNhan_DAL::trash($id, $u);
        return ['success' => true, 'message' => 'Đã chuyển vào thùng rác'];
    }
    public static function ghiNhanRestore(int $id, int $u): array
    {
        DT_CmeGhiNhan_DAL::restore($id, $u);
        return ['success' => true, 'message' => 'Đã khôi phục'];
    }
    public static function ghiNhanGetById(int $id): ?DT_CmeGhiNhan_DTO { return DT_CmeGhiNhan_DAL::getById($id); }
    public static function ghiNhanGetPaged(int $p, int $s, array $opts = [], int $dx = 0): array { return DT_CmeGhiNhan_DAL::getPaged($p, $s, $opts, $dx); }
    public static function getNamCombo(): array { return DT_CmeGhiNhan_DAL::getNamCombo(); }
    public static function getStats(int $nam = 0): array { return DT_CmeGhiNhan_DAL::getStats($nam); }
    public static function baoCaoTheoNhanVien(array $opts = []): array { return DT_CmeGhiNhan_DAL::baoCaoTheoNhanVien($opts); }
    public static function chiTietTheoNhanVien(array $opts = []): array { return DT_CmeGhiNhan_DAL::chiTietTheoNhanVien($opts); }
    public static function tongToanVienTheoNhom(int $nam = 0): array { return DT_CmeGhiNhan_DAL::tongToanVienTheoNhom($nam); }
    public static function tongTheoKhoaPhong(int $nam = 0): array { return DT_CmeGhiNhan_DAL::tongTheoKhoaPhong($nam); }
    public static function topNhanVien(int $limit = 10, int $nam = 0): array { return DT_CmeGhiNhan_DAL::topNhanVien($limit, $nam); }

    /** Dữ liệu tổng hợp cho trang Tổng quan CME. */
    public static function tongQuan(int $nam): array
    {
        $stats = DT_CmeGhiNhan_DAL::getStats($nam);
        $ng = self::getNguong();
        // Đếm số NV đạt ngưỡng trong chu kỳ kết thúc ở $nam
        $tuNam = $nam - $ng['chu_ky_nam'] + 1;
        $bc = DT_CmeGhiNhan_DAL::baoCaoTheoNhanVien(['tu_nam' => $tuNam, 'den_nam' => $nam]);
        $soDat = 0;
        foreach ($bc as $r) if ((float)$r['tong_gio'] >= $ng['gio']) $soDat++;
        return [
            'nam'         => $nam,
            'nguong'      => $ng,
            'tu_nam'      => $tuNam,
            'stats'       => $stats,
            'theo_nhom'   => DT_CmeGhiNhan_DAL::tongToanVienTheoNhom($nam),
            'theo_khoa'   => DT_CmeGhiNhan_DAL::tongTheoKhoaPhong($nam),
            'top_nv'      => DT_CmeGhiNhan_DAL::topNhanVien(10, $nam),
            'so_nv_chu_ky'=> count($bc),
            'so_nv_dat'   => $soDat,
        ];
    }

    // ================= NGƯỠNG / SỔ THEO DÕI =================

    public static function getNguong(): array
    {
        $gio = (float)(DM_CauHinh_DAL::get(self::CFG_NGUONG_GIO, (string)self::DEFAULT_NGUONG) ?? self::DEFAULT_NGUONG);
        $chuKy = (int)(DM_CauHinh_DAL::get(self::CFG_CHU_KY_NAM, (string)self::DEFAULT_CHU_KY) ?? self::DEFAULT_CHU_KY);
        if ($chuKy < 1) $chuKy = 1;
        return ['gio' => $gio, 'chu_ky_nam' => $chuKy];
    }

    /**
     * Sổ theo dõi của 1 nhân viên trong 1 năm: tổng giờ, tách nhóm, tiến độ so ngưỡng (chu kỳ tính lùi từ năm đó).
     */
    public static function soTheoDoiNhanVien(int $nhanVienId, int $nam): array
    {
        $ng = self::getNguong();
        $tuNam = $nam - $ng['chu_ky_nam'] + 1;
        $tongChuKy = DT_CmeGhiNhan_DAL::tongGioNhanVien($nhanVienId, $tuNam, $nam);
        $tongNam   = DT_CmeGhiNhan_DAL::tongGioNhanVien($nhanVienId, $nam, $nam);
        $theoNhom  = DT_CmeGhiNhan_DAL::tongTheoNhom($nhanVienId, $nam);
        $hoatDong  = DT_CmeGhiNhan_DAL::getByNhanVien($nhanVienId, $nam);
        $phanTram  = $ng['gio'] > 0 ? min(100, round($tongChuKy / $ng['gio'] * 100)) : 0;
        return [
            'nam'          => $nam,
            'nguong'       => $ng,
            'tu_nam'       => $tuNam,
            'den_nam'      => $nam,
            'tong_gio_nam' => $tongNam,
            'tong_gio_chu_ky' => $tongChuKy,
            'phan_tram'    => $phanTram,
            'dat'          => $tongChuKy >= $ng['gio'],
            'theo_nhom'    => $theoNhom,
            'hoat_dong'    => $hoatDong,
        ];
    }

    private static function fmt(float $n): string
    {
        return rtrim(rtrim(number_format($n, 2, '.', ''), '0'), '.');
    }
}
