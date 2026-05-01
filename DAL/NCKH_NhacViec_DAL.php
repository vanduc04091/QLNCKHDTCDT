<?php
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/../PUBLIC/Entities/NCKH_NhacViec_DTO.php';

class NCKH_NhacViec_DAL
{
    private static function selectSql(): string
    {
        return "SELECT nv.*,
                       dt.ma_de_tai, dt.ten_de_tai,
                       n.ho_ten AS ho_ten_nguoi_nhan, n.email AS email_nguoi_nhan
                FROM NCKH_NHAC_VIEC nv
                LEFT JOIN NCKH_DE_TAI  dt ON dt.id = nv.de_tai_id
                LEFT JOIN DM_NHAN_VIEN n  ON n.id  = nv.nguoi_nhan_id";
    }

    public static function insert(NCKH_NhacViec_PUBLIC $e): int
    {
        $sql = "INSERT INTO NCKH_NHAC_VIEC
                (de_tai_id, loai_nhac, tieu_de, noi_dung, ngay_nhac, nguoi_nhan_id,
                 da_gui, ngay_tao, ngay_cap_nhat, nguoi_tao, nguoi_cap_nhat, da_xoa)
                VALUES (:dt, :lo, :td, :nd, :ng, :nn, 0, NOW(), NOW(), :u1, :u2, 0)";
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute([
            ':dt' => $e->de_tai_id, ':lo' => $e->loai_nhac, ':td' => $e->tieu_de,
            ':nd' => $e->noi_dung, ':ng' => $e->ngay_nhac, ':nn' => $e->nguoi_nhan_id,
            ':u1' => $e->nguoi_tao ?? 0, ':u2' => $e->nguoi_tao ?? 0,
        ]);
        return (int)Database::getConnection()->lastInsertId();
    }

    public static function update(NCKH_NhacViec_PUBLIC $e): int
    {
        $sql = "UPDATE NCKH_NHAC_VIEC SET
                loai_nhac=:lo, tieu_de=:td, noi_dung=:nd, ngay_nhac=:ng, nguoi_nhan_id=:nn,
                ngay_cap_nhat=NOW(), nguoi_cap_nhat=:u
                WHERE id=:id AND da_xoa=0";
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute([
            ':lo' => $e->loai_nhac, ':td' => $e->tieu_de, ':nd' => $e->noi_dung,
            ':ng' => $e->ngay_nhac, ':nn' => $e->nguoi_nhan_id,
            ':u' => $e->nguoi_cap_nhat ?? 0, ':id' => $e->id,
        ]);
        return $stmt->rowCount();
    }

    public static function markSent(int $id, string $ketQua): int
    {
        $stmt = Database::getConnection()->prepare(
            "UPDATE NCKH_NHAC_VIEC SET da_gui=1, ngay_gui=NOW(), ket_qua_gui=:k WHERE id=:id"
        );
        $stmt->execute([':k' => $ketQua, ':id' => $id]);
        return $stmt->rowCount();
    }

    public static function trash(int $id, int $u): int
    {
        $stmt = Database::getConnection()->prepare("UPDATE NCKH_NHAC_VIEC SET da_xoa=1, ngay_cap_nhat=NOW(), nguoi_cap_nhat=:u WHERE id=:id");
        $stmt->execute([':u' => $u, ':id' => $id]);
        return $stmt->rowCount();
    }

    public static function delete(int $id): int
    {
        $stmt = Database::getConnection()->prepare("DELETE FROM NCKH_NHAC_VIEC WHERE id=:id");
        $stmt->execute([':id' => $id]);
        return $stmt->rowCount();
    }

    public static function getById(int $id): ?NCKH_NhacViec_DTO
    {
        $stmt = Database::getConnection()->prepare(self::selectSql() . " WHERE nv.id=:id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ? Database::hydrate($row, 'NCKH_NhacViec_DTO') : null;
    }

    public static function getByDeTai(int $deTaiId): array
    {
        $stmt = Database::getConnection()->prepare(self::selectSql() . " WHERE nv.de_tai_id=:d AND nv.da_xoa=0 ORDER BY nv.ngay_nhac ASC");
        $stmt->execute([':d' => $deTaiId]);
        return $stmt->fetchAll();
    }

    /** Lấy nhắc việc đến hạn chưa gửi (cho cron) */
    public static function getDueUnsent(int $limit = 50): array
    {
        $stmt = Database::getConnection()->prepare(
            self::selectSql() . " WHERE nv.da_xoa=0 AND nv.da_gui=0 AND nv.ngay_nhac <= NOW()
                                  ORDER BY nv.ngay_nhac ASC LIMIT :lim"
        );
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function getPaged(int $page, int $pageSize, int $daGui = -1, int $daXoa = 0): array
    {
        [$page, $pageSize, $offset] = PaginationHelper::normalize($page, $pageSize);
        $where = " WHERE nv.da_xoa=:dx ";
        $params = [':dx' => $daXoa];
        if ($daGui >= 0) { $where .= " AND nv.da_gui=:gui "; $params[':gui'] = $daGui; }

        $countSql = "SELECT COUNT(*) FROM NCKH_NHAC_VIEC nv" . $where;
        $stmt = Database::getConnection()->prepare($countSql);
        $stmt->execute($params);
        $total = (int)$stmt->fetchColumn();

        $sql = self::selectSql() . $where . " ORDER BY nv.ngay_nhac DESC LIMIT {$pageSize} OFFSET {$offset}";
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute($params);
        return [
            'data' => $stmt->fetchAll(),
            'totalRecords' => $total,
            'totalPages' => PaginationHelper::totalPages($total, $pageSize),
        ];
    }
}
