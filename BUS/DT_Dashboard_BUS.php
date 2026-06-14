<?php
/**
 * DT_Dashboard_BUS - Aggregator cho dashboard đào tạo.
 * Đọc dữ liệu cross-module: học viên / khóa / lớp / lịch / đăng ký / chứng chỉ / đối tượng.
 */
require_once __DIR__ . '/../DAL/database.php';

class DT_Dashboard_BUS
{
    /** KPI cards */
    public static function getKpis(): array
    {
        $pdo = Database::getConnection();

        $hv = (int)$pdo->query("SELECT COUNT(*) FROM DM_HOC_VIEN WHERE da_xoa=0 AND trang_thai=1")->fetchColumn();
        $khoa = (int)$pdo->query("SELECT COUNT(*) FROM DT_KHOA_HOC WHERE da_xoa=0 AND trang_thai=1")->fetchColumn();
        $lop = (int)$pdo->query("SELECT COUNT(*) FROM DT_CHUONG_TRINH WHERE da_xoa=0")->fetchColumn();

        // Đăng ký chờ duyệt
        $dkCho = 0;
        try {
            $dkCho = (int)$pdo->query("SELECT COUNT(*) FROM DT_DANG_KY_KHOA_HOC WHERE da_xoa=0 AND trang_thai=0")->fetchColumn();
        } catch (Throwable $ex) { /* bảng chưa migrate */ }

        // Chứng chỉ tháng này (đã cấp)
        $ccThangNay = 0;
        try {
            $ccThangNay = (int)$pdo->query(
                "SELECT COUNT(*) FROM DT_CHUNG_CHI
                 WHERE da_xoa=0 AND trang_thai=1
                   AND YEAR(ngay_cap)=YEAR(CURDATE()) AND MONTH(ngay_cap)=MONTH(CURDATE())"
            )->fetchColumn();
        } catch (Throwable $ex) { /* skip */ }

        // Buổi học 7 ngày tới
        $buoiToi = 0;
        try {
            $stmt = $pdo->query(
                "SELECT COUNT(*) FROM DT_LICH_HOC
                 WHERE da_xoa=0
                   AND ngay_hoc BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)"
            );
            $buoiToi = (int)$stmt->fetchColumn();
        } catch (Throwable $ex) { /* skip */ }

        return [
            'hoc_vien'     => $hv,
            'khoa_hoc'     => $khoa,
            'lop_hoc'      => $lop,
            'dk_cho'       => $dkCho,
            'cc_thang_nay' => $ccThangNay,
            'buoi_7_ngay'  => $buoiToi,
        ];
    }

    /** Lịch học 7 ngày tới (10 buổi gần nhất). */
    public static function getUpcomingSchedule(int $limit = 10): array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare(
            "SELECT lh.id, lh.ngay_hoc, lh.gio_bat_dau, lh.gio_ket_thuc,
                    lh.buoi_thu, lh.tieu_de, lh.phong_hoc, lh.giang_vien_ngoai,
                    lop.ma_chuong_trinh AS ma_lop, lop.ten_chuong_trinh AS ten_lop,
                    mh.ten_mon_hoc,
                    gv.ho_ten AS ten_giang_vien
             FROM DT_LICH_HOC lh
             LEFT JOIN DT_KHOA_HOC_CHUONG_TRINH khct ON khct.id = lh.khoa_hoc_chuong_trinh_id
             LEFT JOIN DT_CHUONG_TRINH lop ON lop.id = khct.chuong_trinh_id
             LEFT JOIN DT_MON_HOC mh ON mh.id = lh.mon_hoc_id
             LEFT JOIN DM_NHAN_VIEN gv ON gv.id = lh.giang_vien_id
             WHERE lh.da_xoa=0
               AND lh.ngay_hoc BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)
             ORDER BY lh.ngay_hoc ASC, lh.gio_bat_dau ASC
             LIMIT :lim"
        );
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /** Top 5 đơn đăng ký chờ duyệt mới nhất. */
    public static function getPendingRegistrations(int $limit = 5): array
    {
        $pdo = Database::getConnection();
        try {
            $stmt = $pdo->prepare(
                "SELECT dk.id, dk.ho_ten, dk.cccd, dk.email, dk.dien_thoai,
                        dk.ngay_tao, dk.ma_tra_cuu,
                        kh.ma_khoa_hoc, kh.ten_khoa_hoc
                 FROM DT_DANG_KY_KHOA_HOC dk
                 LEFT JOIN DT_KHOA_HOC kh ON kh.id = dk.khoa_hoc_id
                 WHERE dk.da_xoa=0 AND dk.trang_thai=0
                 ORDER BY dk.id DESC
                 LIMIT :lim"
            );
            $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (Throwable $ex) {
            return [];
        }
    }

    /** Top 5 lớp đang học gần đầy nhất (theo % HV / max). */
    public static function getTopFullClasses(int $limit = 5): array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare(
            "SELECT lop.id, lop.ma_chuong_trinh AS ma_lop, lop.ten_chuong_trinh AS ten_lop, lop.so_luong_toi_da,
                    (SELECT COUNT(*) FROM DT_HOC_VIEN_LOP hvl
                       JOIN DT_KHOA_HOC_CHUONG_TRINH k ON k.id = hvl.khoa_hoc_chuong_trinh_id
                       WHERE k.chuong_trinh_id=lop.id AND hvl.da_xoa=0) AS so_hv,
                    NULL AS ten_khoa_hoc
             FROM DT_CHUONG_TRINH lop
             WHERE lop.da_xoa=0
             ORDER BY (
                CASE WHEN lop.so_luong_toi_da > 0
                  THEN (SELECT COUNT(*) FROM DT_HOC_VIEN_LOP hvl
                          JOIN DT_KHOA_HOC_CHUONG_TRINH k ON k.id = hvl.khoa_hoc_chuong_trinh_id
                          WHERE k.chuong_trinh_id=lop.id AND hvl.da_xoa=0) / lop.so_luong_toi_da
                  ELSE 0 END
             ) DESC, lop.id DESC
             LIMIT :lim"
        );
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /** Phân bố HV theo đối tượng (cho bar mini). */
    public static function getHocVienByDoiTuong(): array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->query(
            "SELECT COALESCE(dt.ten_doi_tuong, '(Chưa phân loại)') AS ten,
                    COUNT(hv.id) AS so_luong
             FROM DM_HOC_VIEN hv
             LEFT JOIN DM_DOI_TUONG_HOC_VIEN dt ON dt.id = hv.doi_tuong_id
             WHERE hv.da_xoa=0 AND hv.trang_thai=1
             GROUP BY hv.doi_tuong_id, dt.ten_doi_tuong
             ORDER BY so_luong DESC LIMIT 8"
        );
        return $stmt->fetchAll();
    }

    /** Đăng ký 30 ngày qua (cho line bar - mỗi ngày 1 cột). */
    public static function getRegistrationTrend(): array
    {
        $pdo = Database::getConnection();
        try {
            $stmt = $pdo->query(
                "SELECT DATE(ngay_tao) AS ngay, COUNT(*) AS so_luong
                 FROM DT_DANG_KY_KHOA_HOC
                 WHERE da_xoa=0 AND ngay_tao >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                 GROUP BY DATE(ngay_tao) ORDER BY ngay ASC"
            );
            return $stmt->fetchAll();
        } catch (Throwable $ex) {
            return [];
        }
    }
}
