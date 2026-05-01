<?php
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/../PUBLIC/Entities/DT_HoSoHocVien_DTO.php';

class DT_HoSoHocVien_DAL
{
    private static function selectSql(): string
    {
        return "SELECT hs.*,
                       hv.ma_hv, hv.ho_ten AS ho_ten_hoc_vien, hv.don_vi_cong_tac,
                       u1.tai_khoan AS tai_khoan_nguoi_tao,
                       u2.tai_khoan AS tai_khoan_nguoi_cap_nhat
                FROM DT_HO_SO_HOC_VIEN hs
                LEFT JOIN DM_HOC_VIEN hv ON hv.id = hs.hoc_vien_id
                LEFT JOIN DM_NGUOI_DUNG u1 ON u1.id = hs.nguoi_tao
                LEFT JOIN DM_NGUOI_DUNG u2 ON u2.id = hs.nguoi_cap_nhat";
    }

    public static function insert(DT_HoSoHocVien_PUBLIC $e): int
    {
        $u = $e->nguoi_tao ?? 0;
        $sql = "INSERT INTO DT_HO_SO_HOC_VIEN
                (hoc_vien_id, loai_ho_so, ten_ho_so, so_hieu, ngay_cap, noi_cap,
                 ngay_het_han, duong_dan, kich_thuoc, ghi_chu, trang_thai,
                 ngay_tao, ngay_cap_nhat, nguoi_tao, nguoi_cap_nhat, da_xoa)
                VALUES
                (:hv, :loai, :ten, :so, :ncap, :noi,
                 :hhan, :dd, :kz, :gc, :tt,
                 NOW(), NOW(), :u1, :u2, 0)";
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute([
            ':hv'   => $e->hoc_vien_id,
            ':loai' => $e->loai_ho_so,
            ':ten'  => $e->ten_ho_so,
            ':so'   => $e->so_hieu,
            ':ncap' => $e->ngay_cap,
            ':noi'  => $e->noi_cap,
            ':hhan' => $e->ngay_het_han,
            ':dd'   => $e->duong_dan,
            ':kz'   => $e->kich_thuoc,
            ':gc'   => $e->ghi_chu,
            ':tt'   => $e->trang_thai,
            ':u1'   => $u,
            ':u2'   => $u,
        ]);
        return (int)Database::getConnection()->lastInsertId();
    }

    public static function update(DT_HoSoHocVien_PUBLIC $e): int
    {
        $sql = "UPDATE DT_HO_SO_HOC_VIEN SET
                hoc_vien_id=:hv, loai_ho_so=:loai, ten_ho_so=:ten, so_hieu=:so,
                ngay_cap=:ncap, noi_cap=:noi, ngay_het_han=:hhan,
                duong_dan=:dd, kich_thuoc=:kz, ghi_chu=:gc, trang_thai=:tt,
                ngay_cap_nhat=NOW(), nguoi_cap_nhat=:u
                WHERE id=:id AND da_xoa=0";
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute([
            ':hv'   => $e->hoc_vien_id,
            ':loai' => $e->loai_ho_so,
            ':ten'  => $e->ten_ho_so,
            ':so'   => $e->so_hieu,
            ':ncap' => $e->ngay_cap,
            ':noi'  => $e->noi_cap,
            ':hhan' => $e->ngay_het_han,
            ':dd'   => $e->duong_dan,
            ':kz'   => $e->kich_thuoc,
            ':gc'   => $e->ghi_chu,
            ':tt'   => $e->trang_thai,
            ':u'    => $e->nguoi_cap_nhat ?? 0,
            ':id'   => $e->id,
        ]);
        return $stmt->rowCount();
    }

    public static function trash(int $id, int $u): int
    {
        $stmt = Database::getConnection()->prepare(
            "UPDATE DT_HO_SO_HOC_VIEN SET da_xoa=1, ngay_cap_nhat=NOW(), nguoi_cap_nhat=:u WHERE id=:id"
        );
        $stmt->execute([':u' => $u, ':id' => $id]);
        return $stmt->rowCount();
    }

    public static function restore(int $id, int $u): int
    {
        $stmt = Database::getConnection()->prepare(
            "UPDATE DT_HO_SO_HOC_VIEN SET da_xoa=0, ngay_cap_nhat=NOW(), nguoi_cap_nhat=:u WHERE id=:id"
        );
        $stmt->execute([':u' => $u, ':id' => $id]);
        return $stmt->rowCount();
    }

    public static function delete(int $id): int
    {
        $stmt = Database::getConnection()->prepare("DELETE FROM DT_HO_SO_HOC_VIEN WHERE id=:id");
        $stmt->execute([':id' => $id]);
        return $stmt->rowCount();
    }

    public static function getById(int $id): ?DT_HoSoHocVien_DTO
    {
        $stmt = Database::getConnection()->prepare(self::selectSql() . " WHERE hs.id=:id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ? Database::hydrate($row, 'DT_HoSoHocVien_DTO') : null;
    }

    public static function getPaged(int $page, int $pageSize, array $opts = [], int $daXoa = 0): array
    {
        [$page, $pageSize, $offset] = PaginationHelper::normalize($page, $pageSize);
        $where = " WHERE hs.da_xoa=:dx ";
        $params = [':dx' => $daXoa];

        if (!empty($opts['search'])) {
            $kw = '%' . $opts['search'] . '%';
            $where .= " AND (hs.ten_ho_so LIKE :s1 OR hs.so_hieu LIKE :s2 OR hv.ho_ten LIKE :s3 OR hv.ma_hv LIKE :s4) ";
            $params[':s1'] = $kw; $params[':s2'] = $kw; $params[':s3'] = $kw; $params[':s4'] = $kw;
        }
        if (!empty($opts['loai_ho_so'])) {
            $where .= " AND hs.loai_ho_so=:loai ";
            $params[':loai'] = $opts['loai_ho_so'];
        }
        if (!empty($opts['hoc_vien_id'])) {
            $where .= " AND hs.hoc_vien_id=:hv ";
            $params[':hv'] = (int)$opts['hoc_vien_id'];
        }
        if (!empty($opts['trang_thai']) && $opts['trang_thai'] !== '') {
            $where .= " AND hs.trang_thai=:tt ";
            $params[':tt'] = (int)$opts['trang_thai'];
        }

        $countSql = "SELECT COUNT(*) FROM DT_HO_SO_HOC_VIEN hs
                     LEFT JOIN DM_HOC_VIEN hv ON hv.id = hs.hoc_vien_id" . $where;
        $stmt = Database::getConnection()->prepare($countSql);
        $stmt->execute($params);
        $total = (int)$stmt->fetchColumn();

        $sql = self::selectSql() . $where . " ORDER BY hs.id DESC LIMIT {$pageSize} OFFSET {$offset}";
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute($params);
        return [
            'data'         => $stmt->fetchAll(),
            'totalRecords' => $total,
            'totalPages'   => PaginationHelper::totalPages($total, $pageSize),
        ];
    }

    public static function getByHocVien(int $hocVienId): array
    {
        $stmt = Database::getConnection()->prepare(
            self::selectSql() . " WHERE hs.hoc_vien_id=:hv AND hs.da_xoa=0 ORDER BY hs.loai_ho_so ASC, hs.id DESC"
        );
        $stmt->execute([':hv' => $hocVienId]);
        return $stmt->fetchAll();
    }

    public static function getStats(): array
    {
        $sql = "SELECT
                  COUNT(*) AS total,
                  SUM(CASE WHEN trang_thai=1 THEN 1 ELSE 0 END) AS so_hoat_dong,
                  SUM(CASE WHEN ngay_het_han IS NOT NULL AND ngay_het_han < CURDATE() THEN 1 ELSE 0 END) AS so_het_han,
                  SUM(CASE WHEN duong_dan IS NOT NULL AND duong_dan<>'' THEN 1 ELSE 0 END) AS so_co_file,
                  COUNT(DISTINCT hoc_vien_id) AS so_hoc_vien
                FROM DT_HO_SO_HOC_VIEN WHERE da_xoa=0";
        return Database::getConnection()->query($sql)->fetch() ?: [];
    }

    public static function getComboLoai(): array
    {
        $stmt = Database::getConnection()->query(
            "SELECT DISTINCT loai_ho_so FROM DT_HO_SO_HOC_VIEN WHERE da_xoa=0 ORDER BY loai_ho_so ASC"
        );
        return $stmt->fetchAll(\PDO::FETCH_COLUMN) ?: [];
    }
}
