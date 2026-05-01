<?php
require_once __DIR__ . '/database.php';

/**
 * DM_CauHinh_DAL - Quản lý key-value config trong bảng DM_CAU_HINH.
 * Schema: ma_cau_hinh PK, gia_tri TEXT, mo_ta VARCHAR, ngay_cap_nhat DATETIME.
 */
class DM_CauHinh_DAL
{
    public static function getAll(): array
    {
        $rows = Database::getConnection()
            ->query("SELECT ma_cau_hinh, gia_tri, mo_ta, ngay_cap_nhat FROM DM_CAU_HINH ORDER BY ma_cau_hinh ASC")
            ->fetchAll();
        return $rows ?: [];
    }

    public static function getMap(): array
    {
        $rows = Database::getConnection()
            ->query("SELECT ma_cau_hinh, gia_tri FROM DM_CAU_HINH")
            ->fetchAll(PDO::FETCH_KEY_PAIR);
        return $rows ?: [];
    }

    public static function get(string $key, ?string $default = null): ?string
    {
        $stmt = Database::getConnection()->prepare("SELECT gia_tri FROM DM_CAU_HINH WHERE ma_cau_hinh=:k");
        $stmt->execute([':k' => $key]);
        $v = $stmt->fetchColumn();
        return $v !== false ? $v : $default;
    }

    public static function set(string $key, ?string $value, ?string $mota = null): void
    {
        $sql = "INSERT INTO DM_CAU_HINH (ma_cau_hinh, gia_tri, mo_ta, ngay_cap_nhat)
                VALUES (:k, :v, :m, NOW())
                ON DUPLICATE KEY UPDATE gia_tri=VALUES(gia_tri),
                    mo_ta=COALESCE(VALUES(mo_ta), mo_ta),
                    ngay_cap_nhat=NOW()";
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute([':k' => $key, ':v' => $value, ':m' => $mota]);
    }

    public static function setMany(array $kv): int
    {
        $count = 0;
        foreach ($kv as $k => $v) {
            self::set((string)$k, $v === null ? null : (string)$v);
            $count++;
        }
        return $count;
    }

    public static function delete(string $key): int
    {
        $stmt = Database::getConnection()->prepare("DELETE FROM DM_CAU_HINH WHERE ma_cau_hinh=:k");
        $stmt->execute([':k' => $key]);
        return $stmt->rowCount();
    }
}
