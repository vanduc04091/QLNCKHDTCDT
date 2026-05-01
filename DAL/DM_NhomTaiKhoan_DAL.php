<?php
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/../PUBLIC/Entities/DM_NhomTaiKhoan_DTO.php';

class DM_NhomTaiKhoan_DAL
{
    private static function selectSql(): string
    {
        return "SELECT nt.*,
                       (SELECT COUNT(*) FROM DM_NGUOI_DUNG nd WHERE nd.nhom_tai_khoan_id = nt.id AND nd.da_xoa = 0) AS so_nguoi_dung,
                       u1.tai_khoan AS tai_khoan_nguoi_tao,
                       u2.tai_khoan AS tai_khoan_nguoi_cap_nhat
                FROM DM_NHOM_TAI_KHOAN nt
                LEFT JOIN DM_NGUOI_DUNG u1 ON u1.id = nt.nguoi_tao
                LEFT JOIN DM_NGUOI_DUNG u2 ON u2.id = nt.nguoi_cap_nhat";
    }

    public static function insert(DM_NhomTaiKhoan_PUBLIC $e): int
    {
        $sql = "INSERT INTO DM_NHOM_TAI_KHOAN (ma_nhom, ten_nhom, mo_ta, trang_thai, ngay_tao, ngay_cap_nhat, nguoi_tao, nguoi_cap_nhat, da_xoa)
                VALUES (:ma, :ten, :mt, :tt, NOW(), NOW(), :u1, :u2, 0)";
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute([':ma' => $e->ma_nhom, ':ten' => $e->ten_nhom, ':mt' => $e->mo_ta, ':tt' => $e->trang_thai, ':u1' => $e->nguoi_tao ?? 0, ':u2' => $e->nguoi_tao ?? 0]);
        return (int)Database::getConnection()->lastInsertId();
    }

    public static function update(DM_NhomTaiKhoan_PUBLIC $e): int
    {
        $sql = "UPDATE DM_NHOM_TAI_KHOAN SET ma_nhom=:ma, ten_nhom=:ten, mo_ta=:mt, trang_thai=:tt, ngay_cap_nhat=NOW(), nguoi_cap_nhat=:u WHERE id=:id AND da_xoa=0";
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute([':ma' => $e->ma_nhom, ':ten' => $e->ten_nhom, ':mt' => $e->mo_ta, ':tt' => $e->trang_thai, ':u' => $e->nguoi_cap_nhat ?? 0, ':id' => $e->id]);
        return $stmt->rowCount();
    }

    public static function trash(int $id, int $u): int
    {
        $stmt = Database::getConnection()->prepare("UPDATE DM_NHOM_TAI_KHOAN SET da_xoa=1, ngay_cap_nhat=NOW(), nguoi_cap_nhat=:u WHERE id=:id");
        $stmt->execute([':u' => $u, ':id' => $id]);
        return $stmt->rowCount();
    }

    public static function restore(int $id, int $u): int
    {
        $stmt = Database::getConnection()->prepare("UPDATE DM_NHOM_TAI_KHOAN SET da_xoa=0, ngay_cap_nhat=NOW(), nguoi_cap_nhat=:u WHERE id=:id");
        $stmt->execute([':u' => $u, ':id' => $id]);
        return $stmt->rowCount();
    }

    public static function delete(int $id): int
    {
        $stmt = Database::getConnection()->prepare("DELETE FROM DM_NHOM_TAI_KHOAN WHERE id=:id");
        $stmt->execute([':id' => $id]);
        return $stmt->rowCount();
    }

    public static function getById(int $id): ?DM_NhomTaiKhoan_DTO
    {
        $stmt = Database::getConnection()->prepare(self::selectSql() . " WHERE nt.id=:id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ? Database::hydrate($row, 'DM_NhomTaiKhoan_DTO') : null;
    }

    public static function getAll(int $daXoa = 0): array
    {
        $stmt = Database::getConnection()->prepare(self::selectSql() . " WHERE nt.da_xoa=:dx ORDER BY nt.id ASC");
        $stmt->execute([':dx' => $daXoa]);
        return $stmt->fetchAll();
    }

    public static function getCombo(): array
    {
        $key = 'dm_nhom_tai_khoan:combo';
        $cached = MemcachedHelper::get($key);
        if ($cached !== null) return $cached;
        $stmt = Database::getConnection()->query("SELECT id, ma_nhom, ten_nhom FROM DM_NHOM_TAI_KHOAN WHERE da_xoa=0 ORDER BY ten_nhom");
        $data = $stmt->fetchAll();
        MemcachedHelper::set($key, $data, Constants::CACHE_TTL_COMBO);
        return $data;
    }

    public static function getPaged(int $page, int $pageSize, string $search = '', int $daXoa = 0): array
    {
        [$page, $pageSize, $offset] = PaginationHelper::normalize($page, $pageSize);
        $where = " WHERE nt.da_xoa=:dx ";
        $params = [':dx' => $daXoa];
        if ($search !== '') {
            $where .= " AND (nt.ma_nhom LIKE :s OR nt.ten_nhom LIKE :s) ";
            $params[':s'] = "%{$search}%";
        }
        $countSql = "SELECT COUNT(*) FROM DM_NHOM_TAI_KHOAN nt" . $where;
        $stmt = Database::getConnection()->prepare($countSql);
        $stmt->execute($params);
        $total = (int)$stmt->fetchColumn();

        $sql = self::selectSql() . $where . " ORDER BY nt.id ASC LIMIT {$pageSize} OFFSET {$offset}";
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
        $stmt = Database::getConnection()->prepare("SELECT COUNT(*) FROM DM_NHOM_TAI_KHOAN WHERE ma_nhom=:m AND da_xoa=0 AND id<>:id");
        $stmt->execute([':m' => $ma, ':id' => $excludeId]);
        return (int)$stmt->fetchColumn() > 0;
    }
}
