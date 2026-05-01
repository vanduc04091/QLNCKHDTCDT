<?php
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/../PUBLIC/Entities/DM_NCKH_TheLoai_DTO.php';

class DM_NCKH_TheLoai_DAL
{
    private static function selectSql(): string
    {
        return "SELECT tl.*,
                       u1.tai_khoan AS tai_khoan_nguoi_tao,
                       u2.tai_khoan AS tai_khoan_nguoi_cap_nhat
                FROM DM_NCKH_THE_LOAI tl
                LEFT JOIN DM_NGUOI_DUNG u1 ON u1.id = tl.nguoi_tao
                LEFT JOIN DM_NGUOI_DUNG u2 ON u2.id = tl.nguoi_cap_nhat";
    }

    public static function insert(DM_NCKH_TheLoai_PUBLIC $e): int
    {
        $sql = "INSERT INTO DM_NCKH_THE_LOAI (ma_the_loai, ten_the_loai, mo_ta, thu_tu, trang_thai,
                                              ngay_tao, ngay_cap_nhat, nguoi_tao, nguoi_cap_nhat, da_xoa)
                VALUES (:ma, :ten, :mt, :tt2, :tt, NOW(), NOW(), :u1, :u2, 0)";
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute([
            ':ma' => $e->ma_the_loai, ':ten' => $e->ten_the_loai, ':mt' => $e->mo_ta,
            ':tt2' => $e->thu_tu, ':tt' => $e->trang_thai,
            ':u1' => $e->nguoi_tao ?? 0, ':u2' => $e->nguoi_tao ?? 0,
        ]);
        return (int)Database::getConnection()->lastInsertId();
    }

    public static function update(DM_NCKH_TheLoai_PUBLIC $e): int
    {
        $sql = "UPDATE DM_NCKH_THE_LOAI SET ma_the_loai=:ma, ten_the_loai=:ten, mo_ta=:mt,
                       thu_tu=:tt2, trang_thai=:tt, ngay_cap_nhat=NOW(), nguoi_cap_nhat=:u
                WHERE id=:id AND da_xoa=0";
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute([
            ':ma' => $e->ma_the_loai, ':ten' => $e->ten_the_loai, ':mt' => $e->mo_ta,
            ':tt2' => $e->thu_tu, ':tt' => $e->trang_thai,
            ':u' => $e->nguoi_cap_nhat ?? 0, ':id' => $e->id,
        ]);
        return $stmt->rowCount();
    }

    public static function trash(int $id, int $u): int
    {
        $stmt = Database::getConnection()->prepare("UPDATE DM_NCKH_THE_LOAI SET da_xoa=1, ngay_cap_nhat=NOW(), nguoi_cap_nhat=:u WHERE id=:id");
        $stmt->execute([':u' => $u, ':id' => $id]);
        return $stmt->rowCount();
    }

    public static function restore(int $id, int $u): int
    {
        $stmt = Database::getConnection()->prepare("UPDATE DM_NCKH_THE_LOAI SET da_xoa=0, ngay_cap_nhat=NOW(), nguoi_cap_nhat=:u WHERE id=:id");
        $stmt->execute([':u' => $u, ':id' => $id]);
        return $stmt->rowCount();
    }

    public static function delete(int $id): int
    {
        $stmt = Database::getConnection()->prepare("DELETE FROM DM_NCKH_THE_LOAI WHERE id=:id");
        $stmt->execute([':id' => $id]);
        return $stmt->rowCount();
    }

    public static function getById(int $id): ?DM_NCKH_TheLoai_DTO
    {
        $stmt = Database::getConnection()->prepare(self::selectSql() . " WHERE tl.id=:id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ? Database::hydrate($row, 'DM_NCKH_TheLoai_DTO') : null;
    }

    public static function getPaged(int $page, int $pageSize, string $search = '', int $daXoa = 0): array
    {
        [$page, $pageSize, $offset] = PaginationHelper::normalize($page, $pageSize);
        $where = " WHERE tl.da_xoa=:dx ";
        $params = [':dx' => $daXoa];
        if ($search !== '') {
            $where .= " AND (tl.ma_the_loai LIKE :s OR tl.ten_the_loai LIKE :s) ";
            $params[':s'] = "%{$search}%";
        }
        $countSql = "SELECT COUNT(*) FROM DM_NCKH_THE_LOAI tl" . $where;
        $stmt = Database::getConnection()->prepare($countSql);
        $stmt->execute($params);
        $total = (int)$stmt->fetchColumn();

        $sql = self::selectSql() . $where . " ORDER BY tl.thu_tu ASC, tl.id ASC LIMIT {$pageSize} OFFSET {$offset}";
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
        $stmt = Database::getConnection()->prepare("SELECT COUNT(*) FROM DM_NCKH_THE_LOAI WHERE ma_the_loai=:m AND da_xoa=0 AND id<>:id");
        $stmt->execute([':m' => $ma, ':id' => $excludeId]);
        return (int)$stmt->fetchColumn() > 0;
    }

    public static function getCombo(): array
    {
        $key = 'dm_nckh_the_loai:combo';
        $cached = MemcachedHelper::get($key);
        if ($cached !== null) return $cached;
        $stmt = Database::getConnection()->query("SELECT id, ma_the_loai, ten_the_loai FROM DM_NCKH_THE_LOAI WHERE da_xoa=0 AND trang_thai=1 ORDER BY thu_tu ASC, ten_the_loai ASC");
        $data = $stmt->fetchAll();
        MemcachedHelper::set($key, $data, Constants::CACHE_TTL_COMBO);
        return $data;
    }
}
