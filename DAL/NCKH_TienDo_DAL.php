<?php
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/../PUBLIC/Entities/NCKH_TienDo_DTO.php';

class NCKH_TienDo_DAL
{
    private static function selectSql(): string
    {
        return "SELECT td.*,
                       dt.ma_de_tai, dt.ten_de_tai,
                       nv.ho_ten AS ho_ten_nguoi_bao_cao
                FROM NCKH_TIEN_DO td
                LEFT JOIN NCKH_DE_TAI  dt ON dt.id = td.de_tai_id
                LEFT JOIN DM_NHAN_VIEN nv ON nv.id = td.nguoi_bao_cao_id";
    }

    public static function insert(NCKH_TienDo_PUBLIC $e): int
    {
        $sql = "INSERT INTO NCKH_TIEN_DO
                (de_tai_id, ky_bao_cao, ngay_bao_cao, phan_tram_hoan_thanh,
                 cong_viec_da_lam, cong_viec_tiep_theo, kho_khan_vuong_mac, nguoi_bao_cao_id,
                 ngay_tao, ngay_cap_nhat, nguoi_tao, nguoi_cap_nhat, da_xoa)
                VALUES (:dt, :ky, :ng, :pt, :cl, :ct, :kk, :nbc, NOW(), NOW(), :u1, :u2, 0)";
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute([
            ':dt' => $e->de_tai_id, ':ky' => $e->ky_bao_cao, ':ng' => $e->ngay_bao_cao,
            ':pt' => $e->phan_tram_hoan_thanh,
            ':cl' => $e->cong_viec_da_lam, ':ct' => $e->cong_viec_tiep_theo, ':kk' => $e->kho_khan_vuong_mac,
            ':nbc' => $e->nguoi_bao_cao_id,
            ':u1' => $e->nguoi_tao ?? 0, ':u2' => $e->nguoi_tao ?? 0,
        ]);
        return (int)Database::getConnection()->lastInsertId();
    }

    public static function update(NCKH_TienDo_PUBLIC $e): int
    {
        $sql = "UPDATE NCKH_TIEN_DO SET
                ky_bao_cao=:ky, ngay_bao_cao=:ng, phan_tram_hoan_thanh=:pt,
                cong_viec_da_lam=:cl, cong_viec_tiep_theo=:ct, kho_khan_vuong_mac=:kk,
                nguoi_bao_cao_id=:nbc,
                ngay_cap_nhat=NOW(), nguoi_cap_nhat=:u
                WHERE id=:id AND da_xoa=0";
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute([
            ':ky' => $e->ky_bao_cao, ':ng' => $e->ngay_bao_cao,
            ':pt' => $e->phan_tram_hoan_thanh,
            ':cl' => $e->cong_viec_da_lam, ':ct' => $e->cong_viec_tiep_theo, ':kk' => $e->kho_khan_vuong_mac,
            ':nbc' => $e->nguoi_bao_cao_id,
            ':u' => $e->nguoi_cap_nhat ?? 0, ':id' => $e->id,
        ]);
        return $stmt->rowCount();
    }

    public static function trash(int $id, int $u): int
    {
        $stmt = Database::getConnection()->prepare("UPDATE NCKH_TIEN_DO SET da_xoa=1, ngay_cap_nhat=NOW(), nguoi_cap_nhat=:u WHERE id=:id");
        $stmt->execute([':u' => $u, ':id' => $id]);
        return $stmt->rowCount();
    }

    public static function delete(int $id): int
    {
        $stmt = Database::getConnection()->prepare("DELETE FROM NCKH_TIEN_DO WHERE id=:id");
        $stmt->execute([':id' => $id]);
        return $stmt->rowCount();
    }

    public static function getById(int $id): ?NCKH_TienDo_DTO
    {
        $stmt = Database::getConnection()->prepare(self::selectSql() . " WHERE td.id=:id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ? Database::hydrate($row, 'NCKH_TienDo_DTO') : null;
    }

    public static function getByDeTai(int $deTaiId): array
    {
        $stmt = Database::getConnection()->prepare(self::selectSql() . " WHERE td.de_tai_id=:d AND td.da_xoa=0 ORDER BY td.ngay_bao_cao DESC, td.id DESC");
        $stmt->execute([':d' => $deTaiId]);
        return $stmt->fetchAll();
    }

    /** % hoàn thành mới nhất của 1 đề tài */
    public static function getLatestPercent(int $deTaiId): int
    {
        $stmt = Database::getConnection()->prepare(
            "SELECT phan_tram_hoan_thanh FROM NCKH_TIEN_DO
             WHERE de_tai_id=:d AND da_xoa=0
             ORDER BY ngay_bao_cao DESC, id DESC LIMIT 1"
        );
        $stmt->execute([':d' => $deTaiId]);
        return (int)($stmt->fetchColumn() ?: 0);
    }

    /** Đề tài đang thực hiện nhưng chưa có báo cáo tiến độ trong N ngày qua */
    public static function getOverdueReports(int $days = 90): array
    {
        $stmt = Database::getConnection()->prepare(
            "SELECT dt.id, dt.ma_de_tai, dt.ten_de_tai, dt.chu_nhiem_id,
                    nv.ho_ten AS ho_ten_chu_nhiem, nv.email AS email_chu_nhiem,
                    (SELECT MAX(ngay_bao_cao) FROM NCKH_TIEN_DO WHERE de_tai_id=dt.id AND da_xoa=0) AS lan_cuoi
             FROM NCKH_DE_TAI dt
             LEFT JOIN DM_NHAN_VIEN nv ON nv.id = dt.chu_nhiem_id
             WHERE dt.da_xoa=0 AND dt.trang_thai=1
             HAVING lan_cuoi IS NULL OR lan_cuoi < DATE_SUB(CURDATE(), INTERVAL :d DAY)
             ORDER BY lan_cuoi ASC"
        );
        $stmt->bindValue(':d', $days, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
