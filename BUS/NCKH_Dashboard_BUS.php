<?php
/**
 * NCKH_Dashboard_BUS - Aggregator cho dashboard NCKH theo năm.
 */
require_once __DIR__ . '/../DAL/NCKH_DeTai_DAL.php';
require_once __DIR__ . '/../DAL/NCKH_TienDo_DAL.php';

class NCKH_Dashboard_BUS
{
    public static function getKpis(int $nam): array
    {
        $stats = NCKH_DeTai_DAL::statsByYear($nam);
        return [
            'tong'           => (int)($stats['tong'] ?? 0),
            'de_xuat'        => (int)($stats['de_xuat'] ?? 0),
            'dang_thuc_hien' => (int)($stats['dang_thuc_hien'] ?? 0),
            'hoan_thanh'     => (int)($stats['hoan_thanh'] ?? 0),
            'tam_dung'       => (int)($stats['tam_dung'] ?? 0),
            'huy'            => (int)($stats['huy'] ?? 0),
        ];
    }

    public static function statsByCapDo(int $nam): array { return NCKH_DeTai_DAL::statsByCapDo($nam); }
    public static function statsByTheLoai(int $nam): array { return NCKH_DeTai_DAL::statsByTheLoai($nam); }
    public static function statsByKhoaPhong(int $nam, int $limit = 10): array { return NCKH_DeTai_DAL::statsByKhoaPhong($nam, $limit); }

    public static function getUpcomingDeadlines(int $limit = 10): array { return NCKH_DeTai_DAL::getUpcomingDeadlines($limit); }
    public static function getOverdueReports(int $days = 90): array { return NCKH_TienDo_DAL::getOverdueReports($days); }

    /** Phân bố đề tài 5 năm gần nhất */
    public static function trend5Years(): array
    {
        $stmt = Database::getConnection()->query(
            "SELECT nam, COUNT(*) AS so_luong
             FROM NCKH_DE_TAI
             WHERE da_xoa=0 AND nam >= YEAR(CURDATE()) - 4
             GROUP BY nam ORDER BY nam ASC"
        );
        return $stmt->fetchAll();
    }
}
