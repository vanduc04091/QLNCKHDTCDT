<?php
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/../PUBLIC/Entities/NCKH_TaiLieu_DTO.php';

class NCKH_TaiLieu_DAL
{
    private static function selectSql(): string
    {
        return "SELECT tl.*,
                       dt.ma_de_tai, dt.ten_de_tai,
                       u1.tai_khoan AS tai_khoan_nguoi_tao
                FROM NCKH_TAI_LIEU tl
                LEFT JOIN NCKH_DE_TAI    dt ON dt.id = tl.de_tai_id
                LEFT JOIN DM_NGUOI_DUNG  u1 ON u1.id = tl.nguoi_tao";
    }

    public static function insert(NCKH_TaiLieu_PUBLIC $e): int
    {
        $sql = "INSERT INTO NCKH_TAI_LIEU
                (de_tai_id, loai_tai_lieu, ten_tai_lieu, ten_file_goc, ten_file_luu,
                 kich_thuoc, mime_type, mo_ta,
                 ngay_tao, ngay_cap_nhat, nguoi_tao, nguoi_cap_nhat, da_xoa)
                VALUES (:dt, :lo, :ten, :fg, :fl, :kt, :mt, :md, NOW(), NOW(), :u1, :u2, 0)";
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute([
            ':dt' => $e->de_tai_id, ':lo' => $e->loai_tai_lieu,
            ':ten' => $e->ten_tai_lieu, ':fg' => $e->ten_file_goc, ':fl' => $e->ten_file_luu,
            ':kt' => $e->kich_thuoc, ':mt' => $e->mime_type, ':md' => $e->mo_ta,
            ':u1' => $e->nguoi_tao ?? 0, ':u2' => $e->nguoi_tao ?? 0,
        ]);
        return (int)Database::getConnection()->lastInsertId();
    }

    public static function update(NCKH_TaiLieu_PUBLIC $e): int
    {
        $sql = "UPDATE NCKH_TAI_LIEU SET
                loai_tai_lieu=:lo, ten_tai_lieu=:ten, mo_ta=:md,
                ngay_cap_nhat=NOW(), nguoi_cap_nhat=:u
                WHERE id=:id AND da_xoa=0";
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute([
            ':lo' => $e->loai_tai_lieu, ':ten' => $e->ten_tai_lieu, ':md' => $e->mo_ta,
            ':u' => $e->nguoi_cap_nhat ?? 0, ':id' => $e->id,
        ]);
        return $stmt->rowCount();
    }

    public static function trash(int $id, int $u): int
    {
        $stmt = Database::getConnection()->prepare("UPDATE NCKH_TAI_LIEU SET da_xoa=1, ngay_cap_nhat=NOW(), nguoi_cap_nhat=:u WHERE id=:id");
        $stmt->execute([':u' => $u, ':id' => $id]);
        return $stmt->rowCount();
    }

    public static function delete(int $id): int
    {
        $stmt = Database::getConnection()->prepare("DELETE FROM NCKH_TAI_LIEU WHERE id=:id");
        $stmt->execute([':id' => $id]);
        return $stmt->rowCount();
    }

    public static function getById(int $id): ?NCKH_TaiLieu_DTO
    {
        $stmt = Database::getConnection()->prepare(self::selectSql() . " WHERE tl.id=:id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ? Database::hydrate($row, 'NCKH_TaiLieu_DTO') : null;
    }

    public static function getByDeTai(int $deTaiId, string $loai = ''): array
    {
        $sql = self::selectSql() . " WHERE tl.de_tai_id=:d AND tl.da_xoa=0";
        $params = [':d' => $deTaiId];
        if ($loai !== '') { $sql .= " AND tl.loai_tai_lieu=:lo"; $params[':lo'] = $loai; }
        $sql .= " ORDER BY tl.id DESC";
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
