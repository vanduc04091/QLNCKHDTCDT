<?php
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/../PUBLIC/Entities/DT_BaiKiemTra_DTO.php';

class DT_BaiKiemTra_DAL
{
    private static function selectSql(): string
    {
        return "SELECT bkt.*,
                       lop.ma_lop, lop.ten_lop,
                       mh.ma_mon_hoc, mh.ten_mon_hoc,
                       u.tai_khoan AS tai_khoan_nguoi_tao
                FROM DT_BAI_KIEM_TRA bkt
                LEFT JOIN DT_LOP_HOC lop ON lop.id = bkt.lop_hoc_id
                LEFT JOIN DT_MON_HOC mh ON mh.id = bkt.mon_hoc_id
                LEFT JOIN DM_NGUOI_DUNG u ON u.id = bkt.nguoi_tao";
    }

    public static function insert(DT_BaiKiemTra_PUBLIC $e): int
    {
        $u = $e->nguoi_tao ?? 0;
        $sql = "INSERT INTO DT_BAI_KIEM_TRA
                (ma_bkt, tieu_de, mo_ta, loai_bkt, lop_hoc_id, mon_hoc_id,
                 ngay_kiem_tra, thoi_gian_lam_bai,
                 de_file_name, de_file_goc, de_file_size,
                 dap_an_file_name, dap_an_file_goc, dap_an_file_size,
                 cong_khai_dap_an, trang_thai, ghi_chu,
                 ngay_tao, ngay_cap_nhat, nguoi_tao, nguoi_cap_nhat, da_xoa)
                VALUES (:ma, :td, :mt, :loai, :lop, :mon,
                        :nkt, :tglb,
                        :df, :dfg, :dfs,
                        :af, :afg, :afs,
                        :ck, :tt, :gc,
                        NOW(), NOW(), :u1, :u2, 0)";
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute([
            ':ma' => $e->ma_bkt, ':td' => $e->tieu_de, ':mt' => $e->mo_ta,
            ':loai' => $e->loai_bkt, ':lop' => $e->lop_hoc_id, ':mon' => $e->mon_hoc_id,
            ':nkt' => $e->ngay_kiem_tra ?: null, ':tglb' => $e->thoi_gian_lam_bai,
            ':df' => $e->de_file_name, ':dfg' => $e->de_file_goc, ':dfs' => $e->de_file_size,
            ':af' => $e->dap_an_file_name, ':afg' => $e->dap_an_file_goc, ':afs' => $e->dap_an_file_size,
            ':ck' => $e->cong_khai_dap_an, ':tt' => $e->trang_thai, ':gc' => $e->ghi_chu,
            ':u1' => $u, ':u2' => $u,
        ]);
        return (int)Database::getConnection()->lastInsertId();
    }

    public static function update(DT_BaiKiemTra_PUBLIC $e): int
    {
        $sql = "UPDATE DT_BAI_KIEM_TRA SET
                ma_bkt=:ma, tieu_de=:td, mo_ta=:mt, loai_bkt=:loai,
                lop_hoc_id=:lop, mon_hoc_id=:mon,
                ngay_kiem_tra=:nkt, thoi_gian_lam_bai=:tglb,
                de_file_name=:df, de_file_goc=:dfg, de_file_size=:dfs,
                dap_an_file_name=:af, dap_an_file_goc=:afg, dap_an_file_size=:afs,
                cong_khai_dap_an=:ck, trang_thai=:tt, ghi_chu=:gc,
                ngay_cap_nhat=NOW(), nguoi_cap_nhat=:u
                WHERE id=:id AND da_xoa=0";
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute([
            ':ma' => $e->ma_bkt, ':td' => $e->tieu_de, ':mt' => $e->mo_ta, ':loai' => $e->loai_bkt,
            ':lop' => $e->lop_hoc_id, ':mon' => $e->mon_hoc_id,
            ':nkt' => $e->ngay_kiem_tra ?: null, ':tglb' => $e->thoi_gian_lam_bai,
            ':df' => $e->de_file_name, ':dfg' => $e->de_file_goc, ':dfs' => $e->de_file_size,
            ':af' => $e->dap_an_file_name, ':afg' => $e->dap_an_file_goc, ':afs' => $e->dap_an_file_size,
            ':ck' => $e->cong_khai_dap_an, ':tt' => $e->trang_thai, ':gc' => $e->ghi_chu,
            ':u' => $e->nguoi_cap_nhat ?? 0, ':id' => $e->id,
        ]);
        return $stmt->rowCount();
    }

    public static function trash(int $id, int $u): int
    {
        $stmt = Database::getConnection()->prepare("UPDATE DT_BAI_KIEM_TRA SET da_xoa=1, ngay_cap_nhat=NOW(), nguoi_cap_nhat=:u WHERE id=:id");
        $stmt->execute([':u' => $u, ':id' => $id]);
        return $stmt->rowCount();
    }

    public static function restore(int $id, int $u): int
    {
        $stmt = Database::getConnection()->prepare("UPDATE DT_BAI_KIEM_TRA SET da_xoa=0, ngay_cap_nhat=NOW(), nguoi_cap_nhat=:u WHERE id=:id");
        $stmt->execute([':u' => $u, ':id' => $id]);
        return $stmt->rowCount();
    }

    public static function delete(int $id): int
    {
        $stmt = Database::getConnection()->prepare("DELETE FROM DT_BAI_KIEM_TRA WHERE id=:id");
        $stmt->execute([':id' => $id]);
        return $stmt->rowCount();
    }

    public static function getById(int $id): ?DT_BaiKiemTra_DTO
    {
        $stmt = Database::getConnection()->prepare(self::selectSql() . " WHERE bkt.id=:id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ? Database::hydrate($row, 'DT_BaiKiemTra_DTO') : null;
    }

    public static function clearFile(int $id, string $field, int $u): void
    {
        $allow = ['de','dap_an'];
        if (!in_array($field, $allow, true)) return;
        $sql = "UPDATE DT_BAI_KIEM_TRA SET
                  {$field}_file_name=NULL, {$field}_file_goc=NULL, {$field}_file_size=NULL,
                  ngay_cap_nhat=NOW(), nguoi_cap_nhat=:u
                WHERE id=:id";
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute([':u' => $u, ':id' => $id]);
    }

    public static function getPaged(int $page, int $pageSize, array $opts = [], int $daXoa = 0): array
    {
        [$page, $pageSize, $offset] = PaginationHelper::normalize($page, $pageSize);
        $where = " WHERE bkt.da_xoa=:dx ";
        $params = [':dx' => $daXoa];
        if (!empty($opts['search'])) {
            $where .= " AND (bkt.ma_bkt LIKE :s1 OR bkt.tieu_de LIKE :s2) ";
            $kw = '%' . $opts['search'] . '%';
            $params[':s1'] = $kw; $params[':s2'] = $kw;
        }
        if (!empty($opts['lop_hoc_id'])) { $where .= " AND bkt.lop_hoc_id=:lop "; $params[':lop'] = (int)$opts['lop_hoc_id']; }
        if (!empty($opts['mon_hoc_id'])) { $where .= " AND bkt.mon_hoc_id=:mon "; $params[':mon'] = (int)$opts['mon_hoc_id']; }
        if (!empty($opts['loai_bkt']))   { $where .= " AND bkt.loai_bkt=:loai "; $params[':loai'] = (int)$opts['loai_bkt']; }
        if (isset($opts['trang_thai']) && $opts['trang_thai'] !== '' && (int)$opts['trang_thai'] >= 0) {
            $where .= " AND bkt.trang_thai=:tt ";
            $params[':tt'] = (int)$opts['trang_thai'];
        }

        $countSql = "SELECT COUNT(*) FROM DT_BAI_KIEM_TRA bkt" . $where;
        $stmt = Database::getConnection()->prepare($countSql);
        $stmt->execute($params);
        $total = (int)$stmt->fetchColumn();

        $sql = self::selectSql() . $where . " ORDER BY bkt.id DESC LIMIT {$pageSize} OFFSET {$offset}";
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
        $stmt = Database::getConnection()->prepare("SELECT COUNT(*) FROM DT_BAI_KIEM_TRA WHERE ma_bkt=:m AND da_xoa=0 AND id<>:id");
        $stmt->execute([':m' => $ma, ':id' => $excludeId]);
        return (int)$stmt->fetchColumn() > 0;
    }

    public static function getStats(): array
    {
        $sql = "SELECT
                  COUNT(*) AS total,
                  SUM(CASE WHEN loai_bkt=1 THEN 1 ELSE 0 END) AS thuong_xuyen,
                  SUM(CASE WHEN loai_bkt=2 THEN 1 ELSE 0 END) AS giua_ky,
                  SUM(CASE WHEN loai_bkt=3 THEN 1 ELSE 0 END) AS cuoi_ky,
                  SUM(CASE WHEN loai_bkt=4 THEN 1 ELSE 0 END) AS on_tap,
                  SUM(CASE WHEN dap_an_file_name IS NOT NULL THEN 1 ELSE 0 END) AS co_dap_an
                FROM DT_BAI_KIEM_TRA WHERE da_xoa=0";
        return Database::getConnection()->query($sql)->fetch() ?: [];
    }
}
