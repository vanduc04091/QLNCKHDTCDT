<?php
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/../PUBLIC/Entities/NCKH_ThanhVien_DTO.php';

class NCKH_ThanhVien_DAL
{
    private static function selectSql(): string
    {
        return "SELECT tv.*,
                       nv.ho_ten AS ho_ten_nv, nv.ma_nv, nv.chuc_danh,
                       kp.ten_khoa AS ten_khoa_phong
                FROM NCKH_THANH_VIEN tv
                LEFT JOIN DM_NHAN_VIEN nv ON nv.id = tv.nhan_vien_id
                LEFT JOIN DM_KHOA_PHONG kp ON kp.id = nv.khoa_phong_id";
    }

    public static function insert(NCKH_ThanhVien_PUBLIC $e): int
    {
        $sql = "INSERT INTO NCKH_THANH_VIEN
                (de_tai_id, nhan_vien_id, ho_ten_ngoai, don_vi_ngoai, vai_tro, ma_nv_text,
                 phan_tram_dong_gop, ghi_chu, ngay_tao, ngay_cap_nhat, nguoi_tao, nguoi_cap_nhat, da_xoa)
                VALUES (:dt, :nv, :hn, :dn, :vt, :mnv, :pt, :gc, NOW(), NOW(), :u1, :u2, 0)";
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute([
            ':dt' => $e->de_tai_id, ':nv' => $e->nhan_vien_id,
            ':hn' => $e->ho_ten_ngoai, ':dn' => $e->don_vi_ngoai,
            ':vt' => $e->vai_tro, ':mnv' => $e->ma_nv_text,
            ':pt' => $e->phan_tram_dong_gop, ':gc' => $e->ghi_chu,
            ':u1' => $e->nguoi_tao ?? 0, ':u2' => $e->nguoi_tao ?? 0,
        ]);
        return (int)Database::getConnection()->lastInsertId();
    }

    public static function update(NCKH_ThanhVien_PUBLIC $e): int
    {
        $sql = "UPDATE NCKH_THANH_VIEN SET
                nhan_vien_id=:nv, ho_ten_ngoai=:hn, don_vi_ngoai=:dn,
                vai_tro=:vt, ma_nv_text=:mnv, phan_tram_dong_gop=:pt, ghi_chu=:gc,
                ngay_cap_nhat=NOW(), nguoi_cap_nhat=:u
                WHERE id=:id AND da_xoa=0";
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute([
            ':nv' => $e->nhan_vien_id,
            ':hn' => $e->ho_ten_ngoai, ':dn' => $e->don_vi_ngoai,
            ':vt' => $e->vai_tro, ':mnv' => $e->ma_nv_text,
            ':pt' => $e->phan_tram_dong_gop, ':gc' => $e->ghi_chu,
            ':u' => $e->nguoi_cap_nhat ?? 0, ':id' => $e->id,
        ]);
        return $stmt->rowCount();
    }

    public static function trash(int $id, int $u): int
    {
        $stmt = Database::getConnection()->prepare("UPDATE NCKH_THANH_VIEN SET da_xoa=1, ngay_cap_nhat=NOW(), nguoi_cap_nhat=:u WHERE id=:id");
        $stmt->execute([':u' => $u, ':id' => $id]);
        return $stmt->rowCount();
    }

    public static function delete(int $id): int
    {
        $stmt = Database::getConnection()->prepare("DELETE FROM NCKH_THANH_VIEN WHERE id=:id");
        $stmt->execute([':id' => $id]);
        return $stmt->rowCount();
    }

    public static function getById(int $id): ?NCKH_ThanhVien_DTO
    {
        $stmt = Database::getConnection()->prepare(self::selectSql() . " WHERE tv.id=:id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ? Database::hydrate($row, 'NCKH_ThanhVien_DTO') : null;
    }

    public static function getByDeTai(int $deTaiId): array
    {
        $stmt = Database::getConnection()->prepare(self::selectSql() . " WHERE tv.de_tai_id=:d AND tv.da_xoa=0 ORDER BY tv.id ASC");
        $stmt->execute([':d' => $deTaiId]);
        return $stmt->fetchAll();
    }
}
