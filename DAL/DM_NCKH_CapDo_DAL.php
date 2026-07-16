<?php
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/../PUBLIC/Entities/DM_NCKH_CapDo_DTO.php';

class DM_NCKH_CapDo_DAL
{
    private static function selectSql(): string
    {
        return "SELECT cd.*,
                       u1.tai_khoan AS tai_khoan_nguoi_tao,
                       u2.tai_khoan AS tai_khoan_nguoi_cap_nhat
                FROM DM_NCKH_CAP_DO cd
                LEFT JOIN DM_NGUOI_DUNG u1 ON u1.id = cd.nguoi_tao
                LEFT JOIN DM_NGUOI_DUNG u2 ON u2.id = cd.nguoi_cap_nhat";
    }

    public static function insert(DM_NCKH_CapDo_PUBLIC $e): int
    {
        $sql = "INSERT INTO DM_NCKH_CAP_DO (ma_cap_do, ten_cap_do, mo_ta, thu_tu, trang_thai,
                                            ngay_tao, ngay_cap_nhat, nguoi_tao, nguoi_cap_nhat, da_xoa)
                VALUES (:ma, :ten, :mt, :tt2, :tt, NOW(), NOW(), :u1, :u2, 0)";
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute([
            ':ma' => $e->ma_cap_do, ':ten' => $e->ten_cap_do, ':mt' => $e->mo_ta,
            ':tt2' => $e->thu_tu, ':tt' => $e->trang_thai,
            ':u1' => $e->nguoi_tao ?? 0, ':u2' => $e->nguoi_tao ?? 0,
        ]);
        return (int)Database::getConnection()->lastInsertId();
    }

    public static function update(DM_NCKH_CapDo_PUBLIC $e): int
    {
        $sql = "UPDATE DM_NCKH_CAP_DO SET ma_cap_do=:ma, ten_cap_do=:ten, mo_ta=:mt,
                       thu_tu=:tt2, trang_thai=:tt, ngay_cap_nhat=NOW(), nguoi_cap_nhat=:u
                WHERE id=:id AND da_xoa=0";
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute([
            ':ma' => $e->ma_cap_do, ':ten' => $e->ten_cap_do, ':mt' => $e->mo_ta,
            ':tt2' => $e->thu_tu, ':tt' => $e->trang_thai,
            ':u' => $e->nguoi_cap_nhat ?? 0, ':id' => $e->id,
        ]);
        return $stmt->rowCount();
    }

    public static function trash(int $id, int $u): int
    {
        $stmt = Database::getConnection()->prepare("UPDATE DM_NCKH_CAP_DO SET da_xoa=1, ngay_cap_nhat=NOW(), nguoi_cap_nhat=:u WHERE id=:id");
        $stmt->execute([':u' => $u, ':id' => $id]);
        return $stmt->rowCount();
    }

    public static function restore(int $id, int $u): int
    {
        $stmt = Database::getConnection()->prepare("UPDATE DM_NCKH_CAP_DO SET da_xoa=0, ngay_cap_nhat=NOW(), nguoi_cap_nhat=:u WHERE id=:id");
        $stmt->execute([':u' => $u, ':id' => $id]);
        return $stmt->rowCount();
    }

    public static function delete(int $id): int
    {
        $stmt = Database::getConnection()->prepare("DELETE FROM DM_NCKH_CAP_DO WHERE id=:id");
        $stmt->execute([':id' => $id]);
        return $stmt->rowCount();
    }

    public static function getById(int $id): ?DM_NCKH_CapDo_DTO
    {
        $stmt = Database::getConnection()->prepare(self::selectSql() . " WHERE cd.id=:id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ? Database::hydrate($row, 'DM_NCKH_CapDo_DTO') : null;
    }

    public static function getPaged(int $page, int $pageSize, string $search = '', int $daXoa = 0): array
    {
        [$page, $pageSize, $offset] = PaginationHelper::normalize($page, $pageSize);
        $where = " WHERE cd.da_xoa=:dx ";
        $params = [':dx' => $daXoa];
        if ($search !== '') {
            $where .= " AND (cd.ma_cap_do LIKE :s1 OR cd.ten_cap_do LIKE :s2) ";
            $kw = "%{$search}%";
            $params[':s1'] = $kw; $params[':s2'] = $kw;
        }
        $countSql = "SELECT COUNT(*) FROM DM_NCKH_CAP_DO cd" . $where;
        $stmt = Database::getConnection()->prepare($countSql);
        $stmt->execute($params);
        $total = (int)$stmt->fetchColumn();

        $sql = self::selectSql() . $where . " ORDER BY cd.thu_tu ASC, cd.id ASC LIMIT {$pageSize} OFFSET {$offset}";
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
        $stmt = Database::getConnection()->prepare("SELECT COUNT(*) FROM DM_NCKH_CAP_DO WHERE ma_cap_do=:m AND da_xoa=0 AND id<>:id");
        $stmt->execute([':m' => $ma, ':id' => $excludeId]);
        return (int)$stmt->fetchColumn() > 0;
    }

    public static function getCombo(): array
    {
        $key = 'dm_nckh_cap_do:combo';
        $cached = MemcachedHelper::get($key);
        if ($cached !== null) return $cached;
        $stmt = Database::getConnection()->query("SELECT id, ma_cap_do, ten_cap_do FROM DM_NCKH_CAP_DO WHERE da_xoa=0 AND trang_thai=1 ORDER BY thu_tu ASC, ten_cap_do ASC");
        $data = $stmt->fetchAll();
        MemcachedHelper::set($key, $data, Constants::CACHE_TTL_COMBO);
        return $data;
    }
}
