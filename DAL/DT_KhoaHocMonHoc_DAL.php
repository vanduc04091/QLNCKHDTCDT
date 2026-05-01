<?php
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/../PUBLIC/Entities/DT_KhoaHocMonHoc_DTO.php';

class DT_KhoaHocMonHoc_DAL
{
    private static function selectSql(): string
    {
        return "SELECT km.*,
                       mh.ma_mon_hoc, mh.ten_mon_hoc,
                       mh.so_tiet_ly_thuyet, mh.so_tiet_thuc_hanh, mh.tong_so_tiet,
                       mh.so_tin_chi, mh.trang_thai AS mon_trang_thai
                FROM DT_KHOA_HOC_MON_HOC km
                INNER JOIN DT_MON_HOC mh ON mh.id = km.mon_hoc_id";
    }

    public static function listByKhoaHoc(int $khoaHocId): array
    {
        $stmt = Database::getConnection()->prepare(
            self::selectSql() . " WHERE km.khoa_hoc_id=:kh AND km.da_xoa=0 ORDER BY km.thu_tu ASC, km.id ASC"
        );
        $stmt->execute([':kh' => $khoaHocId]);
        return $stmt->fetchAll();
    }

    /** Lấy danh sách khóa học có gắn 1 môn học (chiều ngược). */
    public static function listByMonHoc(int $monHocId): array
    {
        $stmt = Database::getConnection()->prepare(
            "SELECT km.id, km.thu_tu, km.bat_buoc, km.ngay_tao,
                    kh.id AS khoa_hoc_id, kh.ma_khoa_hoc, kh.ten_khoa_hoc, kh.trang_thai AS khoa_trang_thai
             FROM DT_KHOA_HOC_MON_HOC km
             INNER JOIN DT_KHOA_HOC kh ON kh.id = km.khoa_hoc_id
             WHERE km.mon_hoc_id=:mh AND km.da_xoa=0 AND kh.da_xoa=0
             ORDER BY kh.ten_khoa_hoc ASC"
        );
        $stmt->execute([':mh' => $monHocId]);
        return $stmt->fetchAll();
    }

    public static function summaryByKhoaHoc(int $khoaHocId): array
    {
        $stmt = Database::getConnection()->prepare(
            "SELECT
                COUNT(*) AS so_mon,
                COALESCE(SUM(mh.tong_so_tiet), 0) AS tong_tiet,
                COALESCE(SUM(mh.so_tin_chi), 0) AS tong_tin_chi,
                COALESCE(SUM(CASE WHEN km.bat_buoc = 1 THEN 1 ELSE 0 END), 0) AS so_bat_buoc
             FROM DT_KHOA_HOC_MON_HOC km
             INNER JOIN DT_MON_HOC mh ON mh.id = km.mon_hoc_id
             WHERE km.khoa_hoc_id=:kh AND km.da_xoa=0"
        );
        $stmt->execute([':kh' => $khoaHocId]);
        $row = $stmt->fetch();
        return [
            'so_mon' => (int)($row['so_mon'] ?? 0),
            'tong_tiet' => (int)($row['tong_tiet'] ?? 0),
            'tong_tin_chi' => (float)($row['tong_tin_chi'] ?? 0),
            'so_bat_buoc' => (int)($row['so_bat_buoc'] ?? 0),
        ];
    }

    public static function exists(int $khoaHocId, int $monHocId): bool
    {
        $stmt = Database::getConnection()->prepare(
            "SELECT COUNT(*) FROM DT_KHOA_HOC_MON_HOC WHERE khoa_hoc_id=:kh AND mon_hoc_id=:mh AND da_xoa=0"
        );
        $stmt->execute([':kh' => $khoaHocId, ':mh' => $monHocId]);
        return (int)$stmt->fetchColumn() > 0;
    }

    public static function getNextThuTu(int $khoaHocId): int
    {
        $stmt = Database::getConnection()->prepare(
            "SELECT COALESCE(MAX(thu_tu), 0) + 1 FROM DT_KHOA_HOC_MON_HOC WHERE khoa_hoc_id=:kh AND da_xoa=0"
        );
        $stmt->execute([':kh' => $khoaHocId]);
        return (int)$stmt->fetchColumn();
    }

    public static function insert(DT_KhoaHocMonHoc_PUBLIC $e): int
    {
        $sql = "INSERT INTO DT_KHOA_HOC_MON_HOC
                (khoa_hoc_id, mon_hoc_id, thu_tu, bat_buoc, trang_thai,
                 ngay_tao, ngay_cap_nhat, nguoi_tao, nguoi_cap_nhat, da_xoa)
                VALUES (:kh, :mh, :tt, :bb, :ts, NOW(), NOW(), :u1, :u2, 0)";
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute([
            ':kh' => $e->khoa_hoc_id, ':mh' => $e->mon_hoc_id,
            ':tt' => $e->thu_tu, ':bb' => $e->bat_buoc, ':ts' => $e->trang_thai,
            ':u1' => $e->nguoi_tao ?? 0, ':u2' => $e->nguoi_tao ?? 0,
        ]);
        return (int)Database::getConnection()->lastInsertId();
    }

    public static function updateBatBuoc(int $id, int $batBuoc, int $u): int
    {
        $stmt = Database::getConnection()->prepare(
            "UPDATE DT_KHOA_HOC_MON_HOC SET bat_buoc=:bb, ngay_cap_nhat=NOW(), nguoi_cap_nhat=:u WHERE id=:id"
        );
        $stmt->execute([':bb' => $batBuoc, ':u' => $u, ':id' => $id]);
        return $stmt->rowCount();
    }

    public static function updateThuTu(int $id, int $thuTu, int $u): int
    {
        $stmt = Database::getConnection()->prepare(
            "UPDATE DT_KHOA_HOC_MON_HOC SET thu_tu=:tt, ngay_cap_nhat=NOW(), nguoi_cap_nhat=:u WHERE id=:id"
        );
        $stmt->execute([':tt' => $thuTu, ':u' => $u, ':id' => $id]);
        return $stmt->rowCount();
    }

    public static function getById(int $id): ?array
    {
        $stmt = Database::getConnection()->prepare(
            "SELECT * FROM DT_KHOA_HOC_MON_HOC WHERE id=:id"
        );
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function getNeighbor(int $khoaHocId, int $thuTu, string $dir): ?array
    {
        if ($dir === 'up') {
            $stmt = Database::getConnection()->prepare(
                "SELECT * FROM DT_KHOA_HOC_MON_HOC
                 WHERE khoa_hoc_id=:kh AND da_xoa=0 AND thu_tu < :tt
                 ORDER BY thu_tu DESC LIMIT 1"
            );
        } else {
            $stmt = Database::getConnection()->prepare(
                "SELECT * FROM DT_KHOA_HOC_MON_HOC
                 WHERE khoa_hoc_id=:kh AND da_xoa=0 AND thu_tu > :tt
                 ORDER BY thu_tu ASC LIMIT 1"
            );
        }
        $stmt->execute([':kh' => $khoaHocId, ':tt' => $thuTu]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function remove(int $id): int
    {
        $stmt = Database::getConnection()->prepare("DELETE FROM DT_KHOA_HOC_MON_HOC WHERE id=:id");
        $stmt->execute([':id' => $id]);
        return $stmt->rowCount();
    }
}
