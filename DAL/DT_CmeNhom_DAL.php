<?php
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/../PUBLIC/Entities/DT_CmeNhom_DTO.php';

class DT_CmeNhom_DAL
{
    private static function selectSql(): string
    {
        return "SELECT n.*,
                       (SELECT COUNT(*) FROM DT_CME_LOAI l WHERE l.nhom_id = n.id AND l.da_xoa = 0) AS so_loai,
                       u1.tai_khoan AS tai_khoan_nguoi_tao,
                       u2.tai_khoan AS tai_khoan_nguoi_cap_nhat
                FROM DT_CME_NHOM n
                LEFT JOIN DM_NGUOI_DUNG u1 ON u1.id = n.nguoi_tao
                LEFT JOIN DM_NGUOI_DUNG u2 ON u2.id = n.nguoi_cap_nhat";
    }

    public static function insert(DT_CmeNhom_PUBLIC $e): int
    {
        $sql = "INSERT INTO DT_CME_NHOM (ma_nhom, ten_nhom, thu_tu, ngay_tao, ngay_cap_nhat, nguoi_tao, nguoi_cap_nhat, da_xoa)
                VALUES (:ma, :ten, :tt, NOW(), NOW(), :u1, :u2, 0)";
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute([
            ':ma' => $e->ma_nhom, ':ten' => $e->ten_nhom, ':tt' => $e->thu_tu,
            ':u1' => $e->nguoi_tao ?? 0, ':u2' => $e->nguoi_tao ?? 0,
        ]);
        return (int)Database::getConnection()->lastInsertId();
    }

    public static function update(DT_CmeNhom_PUBLIC $e): int
    {
        $sql = "UPDATE DT_CME_NHOM SET ma_nhom=:ma, ten_nhom=:ten, thu_tu=:tt,
                       ngay_cap_nhat=NOW(), nguoi_cap_nhat=:u
                WHERE id=:id AND da_xoa=0";
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute([
            ':ma' => $e->ma_nhom, ':ten' => $e->ten_nhom, ':tt' => $e->thu_tu,
            ':u' => $e->nguoi_cap_nhat ?? 0, ':id' => $e->id,
        ]);
        return $stmt->rowCount();
    }

    public static function trash(int $id, int $u): int
    {
        $stmt = Database::getConnection()->prepare("UPDATE DT_CME_NHOM SET da_xoa=1, ngay_cap_nhat=NOW(), nguoi_cap_nhat=:u WHERE id=:id");
        $stmt->execute([':u' => $u, ':id' => $id]);
        return $stmt->rowCount();
    }

    public static function restore(int $id, int $u): int
    {
        $stmt = Database::getConnection()->prepare("UPDATE DT_CME_NHOM SET da_xoa=0, ngay_cap_nhat=NOW(), nguoi_cap_nhat=:u WHERE id=:id");
        $stmt->execute([':u' => $u, ':id' => $id]);
        return $stmt->rowCount();
    }

    public static function delete(int $id): int
    {
        $stmt = Database::getConnection()->prepare("DELETE FROM DT_CME_NHOM WHERE id=:id");
        $stmt->execute([':id' => $id]);
        return $stmt->rowCount();
    }

    public static function getById(int $id): ?DT_CmeNhom_DTO
    {
        $stmt = Database::getConnection()->prepare(self::selectSql() . " WHERE n.id=:id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ? Database::hydrate($row, 'DT_CmeNhom_DTO') : null;
    }

    public static function getPaged(int $page, int $pageSize, string $search = '', int $daXoa = 0): array
    {
        [$page, $pageSize, $offset] = PaginationHelper::normalize($page, $pageSize);
        $where = " WHERE n.da_xoa=:dx ";
        $params = [':dx' => $daXoa];
        if ($search !== '') {
            $where .= " AND (n.ma_nhom LIKE :s1 OR n.ten_nhom LIKE :s2) ";
            $kw = "%{$search}%";
            $params[':s1'] = $kw; $params[':s2'] = $kw;
        }
        $countSql = "SELECT COUNT(*) FROM DT_CME_NHOM n" . $where;
        $stmt = Database::getConnection()->prepare($countSql);
        $stmt->execute($params);
        $total = (int)$stmt->fetchColumn();

        $sql = self::selectSql() . $where . " ORDER BY n.thu_tu ASC, n.id ASC LIMIT {$pageSize} OFFSET {$offset}";
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
        $stmt = Database::getConnection()->prepare("SELECT COUNT(*) FROM DT_CME_NHOM WHERE ma_nhom=:m AND da_xoa=0 AND id<>:id");
        $stmt->execute([':m' => $ma, ':id' => $excludeId]);
        return (int)$stmt->fetchColumn() > 0;
    }

    /** Combo nhóm cho select. */
    public static function getCombo(): array
    {
        $stmt = Database::getConnection()->query(
            "SELECT id, ma_nhom, ten_nhom FROM DT_CME_NHOM WHERE da_xoa=0 ORDER BY thu_tu ASC, id ASC"
        );
        return $stmt->fetchAll() ?: [];
    }
}
