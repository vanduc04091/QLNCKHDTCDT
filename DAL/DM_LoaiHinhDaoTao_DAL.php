<?php
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/../PUBLIC/Entities/DM_LoaiHinhDaoTao_DTO.php';

class DM_LoaiHinhDaoTao_DAL
{
    private static function selectSql(): string
    {
        return "SELECT lh.*,
                       u1.tai_khoan AS tai_khoan_nguoi_tao,
                       u2.tai_khoan AS tai_khoan_nguoi_cap_nhat
                FROM DM_LOAI_HINH_DAO_TAO lh
                LEFT JOIN DM_NGUOI_DUNG u1 ON u1.id = lh.nguoi_tao
                LEFT JOIN DM_NGUOI_DUNG u2 ON u2.id = lh.nguoi_cap_nhat";
    }

    public static function insert(DM_LoaiHinhDaoTao_PUBLIC $e): int
    {
        $sql = "INSERT INTO DM_LOAI_HINH_DAO_TAO (ma_loai_hinh, ten_loai_hinh, mo_ta, thu_tu, trang_thai,
                                                  ngay_tao, ngay_cap_nhat, nguoi_tao, nguoi_cap_nhat, da_xoa)
                VALUES (:ma, :ten, :mt, :tt2, :tt, NOW(), NOW(), :u1, :u2, 0)";
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute([
            ':ma' => $e->ma_loai_hinh, ':ten' => $e->ten_loai_hinh, ':mt' => $e->mo_ta,
            ':tt2' => $e->thu_tu, ':tt' => $e->trang_thai,
            ':u1' => $e->nguoi_tao ?? 0, ':u2' => $e->nguoi_tao ?? 0,
        ]);
        return (int)Database::getConnection()->lastInsertId();
    }

    public static function update(DM_LoaiHinhDaoTao_PUBLIC $e): int
    {
        $sql = "UPDATE DM_LOAI_HINH_DAO_TAO SET ma_loai_hinh=:ma, ten_loai_hinh=:ten, mo_ta=:mt,
                       thu_tu=:tt2, trang_thai=:tt, ngay_cap_nhat=NOW(), nguoi_cap_nhat=:u
                WHERE id=:id AND da_xoa=0";
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute([
            ':ma' => $e->ma_loai_hinh, ':ten' => $e->ten_loai_hinh, ':mt' => $e->mo_ta,
            ':tt2' => $e->thu_tu, ':tt' => $e->trang_thai,
            ':u' => $e->nguoi_cap_nhat ?? 0, ':id' => $e->id,
        ]);
        return $stmt->rowCount();
    }

    public static function trash(int $id, int $u): int
    {
        $stmt = Database::getConnection()->prepare("UPDATE DM_LOAI_HINH_DAO_TAO SET da_xoa=1, ngay_cap_nhat=NOW(), nguoi_cap_nhat=:u WHERE id=:id");
        $stmt->execute([':u' => $u, ':id' => $id]);
        return $stmt->rowCount();
    }

    public static function restore(int $id, int $u): int
    {
        $stmt = Database::getConnection()->prepare("UPDATE DM_LOAI_HINH_DAO_TAO SET da_xoa=0, ngay_cap_nhat=NOW(), nguoi_cap_nhat=:u WHERE id=:id");
        $stmt->execute([':u' => $u, ':id' => $id]);
        return $stmt->rowCount();
    }

    public static function delete(int $id): int
    {
        $stmt = Database::getConnection()->prepare("DELETE FROM DM_LOAI_HINH_DAO_TAO WHERE id=:id");
        $stmt->execute([':id' => $id]);
        return $stmt->rowCount();
    }

    public static function getById(int $id): ?DM_LoaiHinhDaoTao_DTO
    {
        $stmt = Database::getConnection()->prepare(self::selectSql() . " WHERE lh.id=:id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ? Database::hydrate($row, 'DM_LoaiHinhDaoTao_DTO') : null;
    }

    public static function getPaged(int $page, int $pageSize, string $search = '', int $daXoa = 0): array
    {
        [$page, $pageSize, $offset] = PaginationHelper::normalize($page, $pageSize);
        $where = " WHERE lh.da_xoa=:dx ";
        $params = [':dx' => $daXoa];
        if ($search !== '') {
            $where .= " AND (lh.ma_loai_hinh LIKE :s1 OR lh.ten_loai_hinh LIKE :s2) ";
            $kw = "%{$search}%";
            $params[':s1'] = $kw; $params[':s2'] = $kw;
        }

        $countSql = "SELECT COUNT(*) FROM DM_LOAI_HINH_DAO_TAO lh" . $where;
        $stmt = Database::getConnection()->prepare($countSql);
        $stmt->execute($params);
        $total = (int)$stmt->fetchColumn();

        $sql = self::selectSql() . $where . " ORDER BY lh.thu_tu ASC, lh.id ASC LIMIT {$pageSize} OFFSET {$offset}";
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
        $stmt = Database::getConnection()->prepare("SELECT COUNT(*) FROM DM_LOAI_HINH_DAO_TAO WHERE ma_loai_hinh=:m AND da_xoa=0 AND id<>:id");
        $stmt->execute([':m' => $ma, ':id' => $excludeId]);
        return (int)$stmt->fetchColumn() > 0;
    }

    public static function getCombo(): array
    {
        $key = 'dm_loai_hinh_dao_tao:combo';
        $cached = MemcachedHelper::get($key);
        if ($cached !== null) return $cached;
        $stmt = Database::getConnection()->query("SELECT id, ma_loai_hinh, ten_loai_hinh FROM DM_LOAI_HINH_DAO_TAO WHERE da_xoa=0 AND trang_thai=1 ORDER BY thu_tu ASC, ten_loai_hinh ASC");
        $data = $stmt->fetchAll();
        MemcachedHelper::set($key, $data, Constants::CACHE_TTL_COMBO);
        return $data;
    }
}
