<?php
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/../PUBLIC/Entities/NCKH_DotDangKy_DTO.php';

class NCKH_DotDangKy_DAL
{
    private static function selectSql(): string
    {
        return "SELECT d.*,
                       (SELECT COUNT(*) FROM NCKH_DOT_GIAI_DOAN gd WHERE gd.dot_id=d.id AND gd.da_xoa=0) AS so_giai_doan,
                       (SELECT COUNT(*) FROM NCKH_DE_TAI dt WHERE dt.dot_dang_ky_id=d.id AND dt.da_xoa=0) AS so_de_tai
                FROM NCKH_DOT_DANG_KY d";
    }

    public static function insert(NCKH_DotDangKy_PUBLIC $e): int
    {
        $sql = "INSERT INTO NCKH_DOT_DANG_KY
                (ten_dot, nam, tu_ngay, den_ngay, mo_ta, trang_thai, ngay_tao, ngay_cap_nhat, nguoi_tao, nguoi_cap_nhat, da_xoa)
                VALUES (:t, :nam, :tn, :dn, :mt, :tt, NOW(), NOW(), :u1, :u2, 0)";
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute([
            ':t' => $e->ten_dot, ':nam' => $e->nam, ':tn' => $e->tu_ngay, ':dn' => $e->den_ngay,
            ':mt' => $e->mo_ta, ':tt' => $e->trang_thai,
            ':u1' => $e->nguoi_tao ?? 0, ':u2' => $e->nguoi_tao ?? 0,
        ]);
        return (int)Database::getConnection()->lastInsertId();
    }

    public static function update(NCKH_DotDangKy_PUBLIC $e): int
    {
        $sql = "UPDATE NCKH_DOT_DANG_KY SET
                ten_dot=:t, nam=:nam, tu_ngay=:tn, den_ngay=:dn, mo_ta=:mt, trang_thai=:tt,
                ngay_cap_nhat=NOW(), nguoi_cap_nhat=:u
                WHERE id=:id AND da_xoa=0";
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute([
            ':t' => $e->ten_dot, ':nam' => $e->nam, ':tn' => $e->tu_ngay, ':dn' => $e->den_ngay,
            ':mt' => $e->mo_ta, ':tt' => $e->trang_thai,
            ':u' => $e->nguoi_cap_nhat ?? 0, ':id' => $e->id,
        ]);
        return $stmt->rowCount();
    }

    public static function trash(int $id, int $u): int
    {
        $stmt = Database::getConnection()->prepare("UPDATE NCKH_DOT_DANG_KY SET da_xoa=1, ngay_cap_nhat=NOW(), nguoi_cap_nhat=:u WHERE id=:id");
        $stmt->execute([':u' => $u, ':id' => $id]);
        return $stmt->rowCount();
    }

    public static function getById(int $id): ?NCKH_DotDangKy_DTO
    {
        $stmt = Database::getConnection()->prepare(self::selectSql() . " WHERE d.id=:id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ? Database::hydrate($row, 'NCKH_DotDangKy_DTO') : null;
    }

    public static function getPaged(int $page, int $pageSize, array $opts = []): array
    {
        [$page, $pageSize, $offset] = PaginationHelper::normalize($page, $pageSize);
        $where = " WHERE d.da_xoa = 0 ";
        $params = [];
        if (!empty($opts['kw'])) {
            $where .= " AND d.ten_dot LIKE :kw ";
            $params[':kw'] = '%' . $opts['kw'] . '%';
        }
        if (!empty($opts['nam'])) {
            $where .= " AND d.nam = :nam ";
            $params[':nam'] = (int)$opts['nam'];
        }
        if (isset($opts['trang_thai']) && $opts['trang_thai'] !== '') {
            $where .= " AND d.trang_thai = :tt ";
            $params[':tt'] = (int)$opts['trang_thai'];
        }

        $stmt = Database::getConnection()->prepare("SELECT COUNT(*) FROM NCKH_DOT_DANG_KY d" . $where);
        $stmt->execute($params);
        $total = (int)$stmt->fetchColumn();

        $sql = self::selectSql() . $where . " ORDER BY d.nam DESC, d.id DESC LIMIT {$pageSize} OFFSET {$offset}";
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute($params);
        return [
            'data' => $stmt->fetchAll(),
            'totalRecords' => $total,
            'totalPages' => PaginationHelper::totalPages($total, $pageSize),
        ];
    }

    /**
     * Combo: lấy các đợt đang hoạt động + chưa kết thúc (cho NCKH_DeTaiCuaToi chọn).
     */
    public static function getCombo(bool $onlyActive = true): array
    {
        $where = " WHERE d.da_xoa = 0 ";
        if ($onlyActive) {
            $where .= " AND d.trang_thai = 1 AND d.den_ngay >= CURDATE() ";
        }
        $sql = "SELECT d.id, d.ten_dot, d.nam, d.tu_ngay, d.den_ngay, d.trang_thai
                FROM NCKH_DOT_DANG_KY d" . $where . " ORDER BY d.nam DESC, d.id DESC";
        return Database::getConnection()->query($sql)->fetchAll();
    }
}
