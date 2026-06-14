<?php
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/../PUBLIC/Entities/DT_TaiLieu_DTO.php';

class DT_TaiLieu_DAL
{
    private static function selectSql(): string
    {
        return "SELECT tl.*,
                       tl.khoa_hoc_chuong_trinh_id AS lop_hoc_id,
                       kh.ma_khoa_hoc, kh.ten_khoa_hoc,
                       lop.ma_chuong_trinh AS ma_lop, lop.ten_chuong_trinh AS ten_lop,
                       mh.ma_mon_hoc, mh.ten_mon_hoc,
                       u.tai_khoan AS tai_khoan_nguoi_tao
                FROM DT_TAI_LIEU tl
                LEFT JOIN DT_KHOA_HOC kh ON kh.id = tl.khoa_hoc_id
                LEFT JOIN DT_KHOA_HOC_CHUONG_TRINH khct ON khct.id = tl.khoa_hoc_chuong_trinh_id
                LEFT JOIN DT_CHUONG_TRINH lop ON lop.id = khct.chuong_trinh_id
                LEFT JOIN DT_MON_HOC mh ON mh.id = tl.mon_hoc_id
                LEFT JOIN DM_NGUOI_DUNG u ON u.id = tl.nguoi_tao";
    }

    public static function insert(DT_TaiLieu_PUBLIC $e): int
    {
        $u = $e->nguoi_tao ?? 0;
        $sql = "INSERT INTO DT_TAI_LIEU
                (ma_tai_lieu, tieu_de, mo_ta, loai_tai_lieu, dinh_dang,
                 file_name, file_goc, file_size, link_ngoai,
                 tac_gia, nam_xuat_ban, nha_xuat_ban,
                 khoa_hoc_id, khoa_hoc_chuong_trinh_id, mon_hoc_id,
                 cong_khai, bat_buoc, trang_thai, ghi_chu,
                 ngay_tao, ngay_cap_nhat, nguoi_tao, nguoi_cap_nhat, da_xoa)
                VALUES (:ma, :td, :mt, :loai, :df,
                        :fn, :fg, :fs, :ln,
                        :tg, :nxb, :nhaxb,
                        :kh, :lop, :mon,
                        :ck, :bb, :tt, :gc,
                        NOW(), NOW(), :u1, :u2, 0)";
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute([
            ':ma' => $e->ma_tai_lieu, ':td' => $e->tieu_de, ':mt' => $e->mo_ta,
            ':loai' => $e->loai_tai_lieu, ':df' => $e->dinh_dang,
            ':fn' => $e->file_name, ':fg' => $e->file_goc, ':fs' => $e->file_size,
            ':ln' => $e->link_ngoai,
            ':tg' => $e->tac_gia, ':nxb' => $e->nam_xuat_ban, ':nhaxb' => $e->nha_xuat_ban,
            ':kh' => $e->khoa_hoc_id, ':lop' => $e->lop_hoc_id, ':mon' => $e->mon_hoc_id,
            ':ck' => $e->cong_khai, ':bb' => $e->bat_buoc, ':tt' => $e->trang_thai,
            ':gc' => $e->ghi_chu,
            ':u1' => $u, ':u2' => $u,
        ]);
        return (int)Database::getConnection()->lastInsertId();
    }

    public static function update(DT_TaiLieu_PUBLIC $e): int
    {
        $sql = "UPDATE DT_TAI_LIEU SET
                ma_tai_lieu=:ma, tieu_de=:td, mo_ta=:mt,
                loai_tai_lieu=:loai, dinh_dang=:df,
                file_name=:fn, file_goc=:fg, file_size=:fs, link_ngoai=:ln,
                tac_gia=:tg, nam_xuat_ban=:nxb, nha_xuat_ban=:nhaxb,
                khoa_hoc_id=:kh, khoa_hoc_chuong_trinh_id=:lop, mon_hoc_id=:mon,
                cong_khai=:ck, bat_buoc=:bb, trang_thai=:tt, ghi_chu=:gc,
                ngay_cap_nhat=NOW(), nguoi_cap_nhat=:u
                WHERE id=:id AND da_xoa=0";
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute([
            ':ma' => $e->ma_tai_lieu, ':td' => $e->tieu_de, ':mt' => $e->mo_ta,
            ':loai' => $e->loai_tai_lieu, ':df' => $e->dinh_dang,
            ':fn' => $e->file_name, ':fg' => $e->file_goc, ':fs' => $e->file_size,
            ':ln' => $e->link_ngoai,
            ':tg' => $e->tac_gia, ':nxb' => $e->nam_xuat_ban, ':nhaxb' => $e->nha_xuat_ban,
            ':kh' => $e->khoa_hoc_id, ':lop' => $e->lop_hoc_id, ':mon' => $e->mon_hoc_id,
            ':ck' => $e->cong_khai, ':bb' => $e->bat_buoc, ':tt' => $e->trang_thai,
            ':gc' => $e->ghi_chu,
            ':u' => $e->nguoi_cap_nhat ?? 0, ':id' => $e->id,
        ]);
        return $stmt->rowCount();
    }

    public static function trash(int $id, int $u): int
    {
        $stmt = Database::getConnection()->prepare("UPDATE DT_TAI_LIEU SET da_xoa=1, ngay_cap_nhat=NOW(), nguoi_cap_nhat=:u WHERE id=:id");
        $stmt->execute([':u' => $u, ':id' => $id]);
        return $stmt->rowCount();
    }

    public static function restore(int $id, int $u): int
    {
        $stmt = Database::getConnection()->prepare("UPDATE DT_TAI_LIEU SET da_xoa=0, ngay_cap_nhat=NOW(), nguoi_cap_nhat=:u WHERE id=:id");
        $stmt->execute([':u' => $u, ':id' => $id]);
        return $stmt->rowCount();
    }

    public static function delete(int $id): int
    {
        $stmt = Database::getConnection()->prepare("DELETE FROM DT_TAI_LIEU WHERE id=:id");
        $stmt->execute([':id' => $id]);
        return $stmt->rowCount();
    }

    public static function getById(int $id): ?DT_TaiLieu_DTO
    {
        $stmt = Database::getConnection()->prepare(self::selectSql() . " WHERE tl.id=:id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ? Database::hydrate($row, 'DT_TaiLieu_DTO') : null;
    }

    public static function getPaged(int $page, int $pageSize, array $opts = [], int $daXoa = 0): array
    {
        [$page, $pageSize, $offset] = PaginationHelper::normalize($page, $pageSize);
        $where = " WHERE tl.da_xoa=:dx ";
        $params = [':dx' => $daXoa];
        if (!empty($opts['search'])) {
            $where .= " AND (tl.ma_tai_lieu LIKE :s1 OR tl.tieu_de LIKE :s2 OR tl.mo_ta LIKE :s3 OR tl.tac_gia LIKE :s4) ";
            $kw = '%' . $opts['search'] . '%';
            $params[':s1'] = $kw; $params[':s2'] = $kw; $params[':s3'] = $kw; $params[':s4'] = $kw;
        }
        if (!empty($opts['loai_tai_lieu'])) { $where .= " AND tl.loai_tai_lieu=:loai "; $params[':loai'] = (int)$opts['loai_tai_lieu']; }
        if (!empty($opts['dinh_dang']))    { $where .= " AND tl.dinh_dang=:df "; $params[':df'] = $opts['dinh_dang']; }
        if (!empty($opts['khoa_hoc_id']))  { $where .= " AND tl.khoa_hoc_id=:kh "; $params[':kh'] = (int)$opts['khoa_hoc_id']; }
        if (!empty($opts['lop_hoc_id']))   { $where .= " AND tl.khoa_hoc_chuong_trinh_id=:lop "; $params[':lop'] = (int)$opts['lop_hoc_id']; }
        if (!empty($opts['mon_hoc_id']))   { $where .= " AND tl.mon_hoc_id=:mon "; $params[':mon'] = (int)$opts['mon_hoc_id']; }
        if (!empty($opts['bat_buoc']))     { $where .= " AND tl.bat_buoc=1 "; }
        if (!empty($opts['cong_khai']))    { $where .= " AND tl.cong_khai=1 "; }

        $countSql = "SELECT COUNT(*) FROM DT_TAI_LIEU tl" . $where;
        $stmt = Database::getConnection()->prepare($countSql);
        $stmt->execute($params);
        $total = (int)$stmt->fetchColumn();

        $sortBy = $opts['sort_by'] ?? 'newest';
        $orderBy = "tl.id DESC";
        if ($sortBy === 'name') $orderBy = "tl.tieu_de ASC";
        elseif ($sortBy === 'download') $orderBy = "tl.luot_tai DESC, tl.id DESC";
        elseif ($sortBy === 'view') $orderBy = "tl.luot_xem DESC, tl.id DESC";

        $sql = self::selectSql() . $where . " ORDER BY {$orderBy} LIMIT {$pageSize} OFFSET {$offset}";
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
        $stmt = Database::getConnection()->prepare("SELECT COUNT(*) FROM DT_TAI_LIEU WHERE ma_tai_lieu=:m AND da_xoa=0 AND id<>:id");
        $stmt->execute([':m' => $ma, ':id' => $excludeId]);
        return (int)$stmt->fetchColumn() > 0;
    }

    public static function incView(int $id): void
    {
        Database::getConnection()->prepare("UPDATE DT_TAI_LIEU SET luot_xem=luot_xem+1 WHERE id=:id")->execute([':id' => $id]);
    }
    public static function incDownload(int $id): void
    {
        Database::getConnection()->prepare("UPDATE DT_TAI_LIEU SET luot_tai=luot_tai+1 WHERE id=:id")->execute([':id' => $id]);
    }

    public static function getStats(): array
    {
        $sql = "SELECT
                  COUNT(*) AS total,
                  SUM(CASE WHEN file_name IS NOT NULL AND file_name<>'' THEN 1 ELSE 0 END) AS so_file,
                  SUM(CASE WHEN link_ngoai IS NOT NULL AND link_ngoai<>'' THEN 1 ELSE 0 END) AS so_link,
                  SUM(CASE WHEN bat_buoc=1 THEN 1 ELSE 0 END) AS so_bat_buoc,
                  SUM(luot_tai) AS tong_tai,
                  SUM(luot_xem) AS tong_xem,
                  SUM(file_size) AS tong_dung_luong
                FROM DT_TAI_LIEU WHERE da_xoa=0";
        return Database::getConnection()->query($sql)->fetch() ?: [];
    }
}
