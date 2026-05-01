<?php
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/../PUBLIC/Entities/NCKH_HoiDong_DTO.php';

class NCKH_HoiDong_DAL
{
    private static function selectSql(): string
    {
        return "SELECT hd.*,
                       nv.ho_ten AS ho_ten_nv, nv.ma_nv, nv.chuc_danh AS chuc_danh_nv,
                       kp.ten_khoa AS ten_khoa_phong
                FROM NCKH_HOI_DONG hd
                LEFT JOIN DM_NHAN_VIEN  nv ON nv.id = hd.nhan_vien_id
                LEFT JOIN DM_KHOA_PHONG kp ON kp.id = hd.khoa_phong_id";
    }

    public static function insert(NCKH_HoiDong_PUBLIC $e): int
    {
        $sql = "INSERT INTO NCKH_HOI_DONG
                (de_tai_id, ho_ten, chuc_danh_hoc_vi, nhan_vien_id, ten_khoa_text, khoa_phong_id,
                 vai_tro_hd, thu_tu, ghi_chu, ngay_tao, ngay_cap_nhat, nguoi_tao, nguoi_cap_nhat, da_xoa)
                VALUES (:dt, :ht, :cd, :nv, :tkt, :kp, :vt, :tu, :gc, NOW(), NOW(), :u1, :u2, 0)";
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute([
            ':dt' => $e->de_tai_id, ':ht' => $e->ho_ten, ':cd' => $e->chuc_danh_hoc_vi,
            ':nv' => $e->nhan_vien_id, ':tkt' => $e->ten_khoa_text, ':kp' => $e->khoa_phong_id,
            ':vt' => $e->vai_tro_hd, ':tu' => $e->thu_tu, ':gc' => $e->ghi_chu,
            ':u1' => $e->nguoi_tao ?? 0, ':u2' => $e->nguoi_tao ?? 0,
        ]);
        return (int)Database::getConnection()->lastInsertId();
    }

    public static function update(NCKH_HoiDong_PUBLIC $e): int
    {
        $sql = "UPDATE NCKH_HOI_DONG SET
                ho_ten=:ht, chuc_danh_hoc_vi=:cd, nhan_vien_id=:nv,
                ten_khoa_text=:tkt, khoa_phong_id=:kp,
                vai_tro_hd=:vt, thu_tu=:tu, ghi_chu=:gc,
                ngay_cap_nhat=NOW(), nguoi_cap_nhat=:u
                WHERE id=:id AND da_xoa=0";
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute([
            ':ht' => $e->ho_ten, ':cd' => $e->chuc_danh_hoc_vi, ':nv' => $e->nhan_vien_id,
            ':tkt' => $e->ten_khoa_text, ':kp' => $e->khoa_phong_id,
            ':vt' => $e->vai_tro_hd, ':tu' => $e->thu_tu, ':gc' => $e->ghi_chu,
            ':u' => $e->nguoi_cap_nhat ?? 0, ':id' => $e->id,
        ]);
        return $stmt->rowCount();
    }

    public static function trash(int $id, int $u): int
    {
        $stmt = Database::getConnection()->prepare("UPDATE NCKH_HOI_DONG SET da_xoa=1, ngay_cap_nhat=NOW(), nguoi_cap_nhat=:u WHERE id=:id");
        $stmt->execute([':u' => $u, ':id' => $id]);
        return $stmt->rowCount();
    }

    public static function delete(int $id): int
    {
        $stmt = Database::getConnection()->prepare("DELETE FROM NCKH_HOI_DONG WHERE id=:id");
        $stmt->execute([':id' => $id]);
        return $stmt->rowCount();
    }

    public static function getById(int $id): ?NCKH_HoiDong_DTO
    {
        $stmt = Database::getConnection()->prepare(self::selectSql() . " WHERE hd.id=:id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ? Database::hydrate($row, 'NCKH_HoiDong_DTO') : null;
    }

    public static function getByDeTai(int $deTaiId): array
    {
        $stmt = Database::getConnection()->prepare(self::selectSql() . " WHERE hd.de_tai_id=:d AND hd.da_xoa=0 ORDER BY hd.thu_tu ASC, hd.id ASC");
        $stmt->execute([':d' => $deTaiId]);
        return $stmt->fetchAll();
    }
}
