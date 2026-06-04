<?php
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/../PUBLIC/Entities/DT_DotGiaiDoan_DTO.php';

class DT_DotGiaiDoan_DAL
{
    private static function selectSql(): string
    {
        return "SELECT gd.*, d.ten_dot
                FROM DT_DOT_GIAI_DOAN gd
                LEFT JOIN DT_DOT_DANG_KY d ON d.id = gd.dot_id";
    }

    public static function insert(DT_DotGiaiDoan_PUBLIC $e): int
    {
        $sql = "INSERT INTO DT_DOT_GIAI_DOAN
                (dot_id, ten_giai_doan, hanh_vi, tu_ngay, den_ngay, thu_tu, ghi_chu,
                 ngay_tao, ngay_cap_nhat, nguoi_tao, nguoi_cap_nhat, da_xoa)
                VALUES (:d, :tn, :hv, :tu, :dn, :th, :gc, NOW(), NOW(), :u1, :u2, 0)";
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute([
            ':d' => $e->dot_id, ':tn' => $e->ten_giai_doan, ':hv' => $e->hanh_vi,
            ':tu' => $e->tu_ngay, ':dn' => $e->den_ngay, ':th' => $e->thu_tu, ':gc' => $e->ghi_chu,
            ':u1' => $e->nguoi_tao ?? 0, ':u2' => $e->nguoi_tao ?? 0,
        ]);
        return (int)Database::getConnection()->lastInsertId();
    }

    public static function update(DT_DotGiaiDoan_PUBLIC $e): int
    {
        $sql = "UPDATE DT_DOT_GIAI_DOAN SET
                ten_giai_doan=:tn, hanh_vi=:hv, tu_ngay=:tu, den_ngay=:dn, thu_tu=:th, ghi_chu=:gc,
                ngay_cap_nhat=NOW(), nguoi_cap_nhat=:u
                WHERE id=:id AND da_xoa=0";
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute([
            ':tn' => $e->ten_giai_doan, ':hv' => $e->hanh_vi,
            ':tu' => $e->tu_ngay, ':dn' => $e->den_ngay, ':th' => $e->thu_tu, ':gc' => $e->ghi_chu,
            ':u' => $e->nguoi_cap_nhat ?? 0, ':id' => $e->id,
        ]);
        return $stmt->rowCount();
    }

    public static function trash(int $id, int $u): int
    {
        $stmt = Database::getConnection()->prepare("UPDATE DT_DOT_GIAI_DOAN SET da_xoa=1, ngay_cap_nhat=NOW(), nguoi_cap_nhat=:u WHERE id=:id");
        $stmt->execute([':u' => $u, ':id' => $id]);
        return $stmt->rowCount();
    }

    public static function getById(int $id): ?DT_DotGiaiDoan_DTO
    {
        $stmt = Database::getConnection()->prepare(self::selectSql() . " WHERE gd.id=:id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ? Database::hydrate($row, 'DT_DotGiaiDoan_DTO') : null;
    }

    public static function getByDot(int $dotId): array
    {
        $stmt = Database::getConnection()->prepare(self::selectSql() . " WHERE gd.dot_id=:d AND gd.da_xoa=0 ORDER BY gd.thu_tu ASC, gd.tu_ngay ASC, gd.id ASC");
        $stmt->execute([':d' => $dotId]);
        return $stmt->fetchAll();
    }

    public static function getActivePhase(int $dotId, string $hanhVi): ?array
    {
        $sql = "SELECT * FROM DT_DOT_GIAI_DOAN
                WHERE dot_id=:d AND hanh_vi=:hv AND da_xoa=0
                  AND tu_ngay <= NOW() AND den_ngay >= NOW()
                ORDER BY tu_ngay DESC LIMIT 1";
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute([':d' => $dotId, ':hv' => $hanhVi]);
        $row = $stmt->fetch();
        return $row ?: null;
    }
}
