<?php
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/../PUBLIC/Entities/DT_ChuongTrinh_DTO.php';

class DT_ChuongTrinh_DAL
{
    private static function selectSql(): string
    {
        return "SELECT ct.*,
                       kp.ten_khoa AS ten_khoa_phong,
                       dt.ten_doi_tuong,
                       (SELECT COUNT(*) FROM DT_HOC_VIEN_LOP hvl
                          JOIN DT_KHOA_HOC_CHUONG_TRINH k2 ON k2.id = hvl.khoa_hoc_chuong_trinh_id
                          WHERE k2.chuong_trinh_id = ct.id AND hvl.da_xoa = 0) AS so_hoc_vien,
                       (SELECT COUNT(*) FROM DT_KHOA_HOC_CHUONG_TRINH khct
                          WHERE khct.chuong_trinh_id = ct.id AND khct.da_xoa = 0) AS so_khoa_hoc,
                       (SELECT COUNT(*) FROM DT_CHUONG_TRINH_MON_HOC mhc
                          WHERE mhc.chuong_trinh_id = ct.id AND mhc.da_xoa = 0) AS so_mon_hoc,
                       u1.tai_khoan AS tai_khoan_nguoi_tao,
                       u2.tai_khoan AS tai_khoan_nguoi_cap_nhat
                FROM DT_CHUONG_TRINH ct
                LEFT JOIN DM_KHOA_PHONG kp ON kp.id = ct.khoa_phong_id
                LEFT JOIN DM_DOI_TUONG_HOC_VIEN dt ON dt.id = ct.doi_tuong_id
                LEFT JOIN DM_NGUOI_DUNG u1 ON u1.id = ct.nguoi_tao
                LEFT JOIN DM_NGUOI_DUNG u2 ON u2.id = ct.nguoi_cap_nhat";
    }

    public static function insert(DT_ChuongTrinh_PUBLIC $e): int
    {
        $sql = "INSERT INTO DT_CHUONG_TRINH
                (ma_chuong_trinh, ten_chuong_trinh, thu_tu, thoi_luong, khoa_phong_id,
                 doi_tuong_id, so_luong_toi_da, mo_ta,
                 ngay_tao, ngay_cap_nhat, nguoi_tao, nguoi_cap_nhat, da_xoa)
                VALUES (:ma, :ten, :thutu, :tl, :kp, :dt, :sl, :mt,
                        NOW(), NOW(), :u1, :u2, 0)";
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute([
            ':ma' => $e->ma_chuong_trinh, ':ten' => $e->ten_chuong_trinh, ':thutu' => $e->thu_tu,
            ':tl' => $e->thoi_luong, ':kp' => $e->khoa_phong_id,
            ':dt' => $e->doi_tuong_id, ':sl' => $e->so_luong_toi_da, ':mt' => $e->mo_ta,
            ':u1' => $e->nguoi_tao ?? 0, ':u2' => $e->nguoi_tao ?? 0,
        ]);
        return (int)Database::getConnection()->lastInsertId();
    }

    public static function update(DT_ChuongTrinh_PUBLIC $e): int
    {
        $sql = "UPDATE DT_CHUONG_TRINH SET
                ma_chuong_trinh=:ma, ten_chuong_trinh=:ten, thu_tu=:thutu, thoi_luong=:tl, khoa_phong_id=:kp,
                doi_tuong_id=:dt, so_luong_toi_da=:sl, mo_ta=:mt,
                ngay_cap_nhat=NOW(), nguoi_cap_nhat=:u
                WHERE id=:id AND da_xoa=0";
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute([
            ':ma' => $e->ma_chuong_trinh, ':ten' => $e->ten_chuong_trinh, ':thutu' => $e->thu_tu,
            ':tl' => $e->thoi_luong, ':kp' => $e->khoa_phong_id,
            ':dt' => $e->doi_tuong_id, ':sl' => $e->so_luong_toi_da, ':mt' => $e->mo_ta,
            ':u' => $e->nguoi_cap_nhat ?? 0, ':id' => $e->id,
        ]);
        return $stmt->rowCount();
    }

    public static function trash(int $id, int $u): int
    {
        $stmt = Database::getConnection()->prepare("UPDATE DT_CHUONG_TRINH SET da_xoa=1, ngay_cap_nhat=NOW(), nguoi_cap_nhat=:u WHERE id=:id");
        $stmt->execute([':u' => $u, ':id' => $id]);
        return $stmt->rowCount();
    }

    public static function restore(int $id, int $u): int
    {
        $stmt = Database::getConnection()->prepare("UPDATE DT_CHUONG_TRINH SET da_xoa=0, ngay_cap_nhat=NOW(), nguoi_cap_nhat=:u WHERE id=:id");
        $stmt->execute([':u' => $u, ':id' => $id]);
        return $stmt->rowCount();
    }

    public static function delete(int $id): int
    {
        $stmt = Database::getConnection()->prepare("DELETE FROM DT_CHUONG_TRINH WHERE id=:id");
        $stmt->execute([':id' => $id]);
        return $stmt->rowCount();
    }

    public static function getById(int $id): ?DT_ChuongTrinh_DTO
    {
        $stmt = Database::getConnection()->prepare(self::selectSql() . " WHERE ct.id=:id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ? Database::hydrate($row, 'DT_ChuongTrinh_DTO') : null;
    }

    public static function getPaged(int $page, int $pageSize, string $search = '', int $daXoa = 0, int $khoaHocId = 0, int $doiTuongId = 0): array
    {
        [$page, $pageSize, $offset] = PaginationHelper::normalize($page, $pageSize);
        $where = " WHERE ct.da_xoa=:dx ";
        $params = [':dx' => $daXoa];
        if ($search !== '') {
            $where .= " AND (ct.ma_chuong_trinh LIKE :s1 OR ct.ten_chuong_trinh LIKE :s2 OR ct.thoi_luong LIKE :s3) ";
            $kw = "%{$search}%";
            $params[':s1'] = $kw; $params[':s2'] = $kw; $params[':s3'] = $kw;
        }
        if ($khoaHocId > 0) {
            $where .= " AND EXISTS (SELECT 1 FROM DT_KHOA_HOC_CHUONG_TRINH khf
                                    WHERE khf.chuong_trinh_id = ct.id AND khf.khoa_hoc_id = :kh AND khf.da_xoa = 0) ";
            $params[':kh'] = $khoaHocId;
        }
        if ($doiTuongId > 0) {
            $where .= " AND ct.doi_tuong_id=:dtid ";
            $params[':dtid'] = $doiTuongId;
        }

        $countSql = "SELECT COUNT(*) FROM DT_CHUONG_TRINH ct" . $where;
        $stmt = Database::getConnection()->prepare($countSql);
        $stmt->execute($params);
        $total = (int)$stmt->fetchColumn();

        $sql = self::selectSql() . $where . " ORDER BY ct.thu_tu ASC, ct.id ASC LIMIT {$pageSize} OFFSET {$offset}";
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute($params);
        return [
            'data' => $stmt->fetchAll(),
            'totalRecords' => $total,
            'totalPages' => PaginationHelper::totalPages($total, $pageSize),
        ];
    }

    public static function checkMaExists(string $ma, int $excludeId = 0): bool
    {
        $stmt = Database::getConnection()->prepare("SELECT COUNT(*) FROM DT_CHUONG_TRINH WHERE ma_chuong_trinh=:m AND da_xoa=0 AND id<>:id");
        $stmt->execute([':m' => $ma, ':id' => $excludeId]);
        return (int)$stmt->fetchColumn() > 0;
    }

    public static function getStats(): array
    {
        $sql = "SELECT
                  COUNT(*) AS total,
                  SUM(CASE WHEN EXISTS (SELECT 1 FROM DT_KHOA_HOC_CHUONG_TRINH k WHERE k.chuong_trinh_id=ct.id AND k.da_xoa=0) THEN 1 ELSE 0 END) AS co_khoa,
                  SUM(CASE WHEN EXISTS (SELECT 1 FROM DT_CHUONG_TRINH_MON_HOC m WHERE m.chuong_trinh_id=ct.id AND m.da_xoa=0) THEN 1 ELSE 0 END) AS co_mon
                FROM DT_CHUONG_TRINH ct WHERE ct.da_xoa=0";
        return Database::getConnection()->query($sql)->fetch() ?: ['total'=>0,'co_khoa'=>0,'co_mon'=>0];
    }

    public static function countHocVien(int $ctId): int
    {
        $stmt = Database::getConnection()->prepare(
            "SELECT COUNT(*) FROM DT_HOC_VIEN_LOP hvl
             JOIN DT_KHOA_HOC_CHUONG_TRINH k ON k.id = hvl.khoa_hoc_chuong_trinh_id
             WHERE k.chuong_trinh_id=:id AND hvl.da_xoa=0");
        $stmt->execute([':id' => $ctId]);
        return (int)$stmt->fetchColumn();
    }

    /** Combo CTĐT (cho các select đơn giản). */
    public static function getCombo(): array
    {
        $stmt = Database::getConnection()->query(
            "SELECT id, ma_chuong_trinh, ten_chuong_trinh, thu_tu
             FROM DT_CHUONG_TRINH WHERE da_xoa=0 ORDER BY thu_tu ASC, id ASC"
        );
        return $stmt->fetchAll();
    }
}
