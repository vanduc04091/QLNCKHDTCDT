<?php
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/../PUBLIC/Entities/DT_ChungChi_DTO.php';

class DT_ChungChi_DAL
{
    private static function selectSql(): string
    {
        return "SELECT cc.*,
                       cc.khoa_hoc_chuong_trinh_id AS lop_hoc_id,
                       hv.ma_hv, hv.ho_ten AS ho_ten_hoc_vien, hv.don_vi_cong_tac,
                       lop.ma_chuong_trinh AS ma_lop, lop.ten_chuong_trinh AS ten_lop,
                       kh.ma_khoa_hoc, kh.ten_khoa_hoc,
                       u1.tai_khoan AS tai_khoan_nguoi_tao,
                       u2.tai_khoan AS tai_khoan_nguoi_cap_nhat
                FROM DT_CHUNG_CHI cc
                LEFT JOIN DM_HOC_VIEN hv  ON hv.id  = cc.hoc_vien_id
                LEFT JOIN DT_KHOA_HOC_CHUONG_TRINH khct ON khct.id = cc.khoa_hoc_chuong_trinh_id
                LEFT JOIN DT_CHUONG_TRINH lop ON lop.id = khct.chuong_trinh_id
                LEFT JOIN DT_KHOA_HOC kh  ON kh.id  = khct.khoa_hoc_id
                LEFT JOIN DM_NGUOI_DUNG u1 ON u1.id = cc.nguoi_tao
                LEFT JOIN DM_NGUOI_DUNG u2 ON u2.id = cc.nguoi_cap_nhat";
    }

    public static function insert(DT_ChungChi_PUBLIC $e): int
    {
        $u = $e->nguoi_tao ?? 0;
        $sql = "INSERT INTO DT_CHUNG_CHI
                (hoc_vien_id, khoa_hoc_chuong_trinh_id, so_chung_chi, ten_chung_chi, loai_chung_chi,
                 xep_loai_tot_nghiep, diem_trung_binh, ngay_cap, ngay_het_han,
                 nguoi_ky, chuc_vu_nguoi_ky, noi_cap, duong_dan_file, ghi_chu, trang_thai,
                 ngay_tao, ngay_cap_nhat, nguoi_tao, nguoi_cap_nhat, da_xoa)
                VALUES
                (:hv, :lop, :so, :ten, :loai,
                 :xl, :dtb, :ncap, :hhan,
                 :nky, :cvnky, :noi, :file, :gc, :tt,
                 NOW(), NOW(), :u1, :u2, 0)";
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute([
            ':hv'    => $e->hoc_vien_id,
            ':lop'   => $e->lop_hoc_id,
            ':so'    => $e->so_chung_chi,
            ':ten'   => $e->ten_chung_chi,
            ':loai'  => $e->loai_chung_chi,
            ':xl'    => $e->xep_loai_tot_nghiep,
            ':dtb'   => $e->diem_trung_binh,
            ':ncap'  => $e->ngay_cap,
            ':hhan'  => $e->ngay_het_han,
            ':nky'   => $e->nguoi_ky,
            ':cvnky' => $e->chuc_vu_nguoi_ky,
            ':noi'   => $e->noi_cap,
            ':file'  => $e->duong_dan_file,
            ':gc'    => $e->ghi_chu,
            ':tt'    => $e->trang_thai,
            ':u1'    => $u,
            ':u2'    => $u,
        ]);
        return (int)Database::getConnection()->lastInsertId();
    }

    public static function update(DT_ChungChi_PUBLIC $e): int
    {
        $sql = "UPDATE DT_CHUNG_CHI SET
                hoc_vien_id=:hv, khoa_hoc_chuong_trinh_id=:lop, so_chung_chi=:so, ten_chung_chi=:ten, loai_chung_chi=:loai,
                xep_loai_tot_nghiep=:xl, diem_trung_binh=:dtb, ngay_cap=:ncap, ngay_het_han=:hhan,
                nguoi_ky=:nky, chuc_vu_nguoi_ky=:cvnky, noi_cap=:noi, duong_dan_file=:file,
                ghi_chu=:gc, trang_thai=:tt,
                ngay_cap_nhat=NOW(), nguoi_cap_nhat=:u
                WHERE id=:id AND da_xoa=0";
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute([
            ':hv'    => $e->hoc_vien_id,
            ':lop'   => $e->lop_hoc_id,
            ':so'    => $e->so_chung_chi,
            ':ten'   => $e->ten_chung_chi,
            ':loai'  => $e->loai_chung_chi,
            ':xl'    => $e->xep_loai_tot_nghiep,
            ':dtb'   => $e->diem_trung_binh,
            ':ncap'  => $e->ngay_cap,
            ':hhan'  => $e->ngay_het_han,
            ':nky'   => $e->nguoi_ky,
            ':cvnky' => $e->chuc_vu_nguoi_ky,
            ':noi'   => $e->noi_cap,
            ':file'  => $e->duong_dan_file,
            ':gc'    => $e->ghi_chu,
            ':tt'    => $e->trang_thai,
            ':u'     => $e->nguoi_cap_nhat ?? 0,
            ':id'    => $e->id,
        ]);
        return $stmt->rowCount();
    }

    public static function trash(int $id, int $u): int
    {
        $stmt = Database::getConnection()->prepare(
            "UPDATE DT_CHUNG_CHI SET da_xoa=1, ngay_cap_nhat=NOW(), nguoi_cap_nhat=:u WHERE id=:id"
        );
        $stmt->execute([':u' => $u, ':id' => $id]);
        return $stmt->rowCount();
    }

    public static function restore(int $id, int $u): int
    {
        $stmt = Database::getConnection()->prepare(
            "UPDATE DT_CHUNG_CHI SET da_xoa=0, ngay_cap_nhat=NOW(), nguoi_cap_nhat=:u WHERE id=:id"
        );
        $stmt->execute([':u' => $u, ':id' => $id]);
        return $stmt->rowCount();
    }

    public static function delete(int $id): int
    {
        $stmt = Database::getConnection()->prepare("DELETE FROM DT_CHUNG_CHI WHERE id=:id");
        $stmt->execute([':id' => $id]);
        return $stmt->rowCount();
    }

    public static function getById(int $id): ?DT_ChungChi_DTO
    {
        $stmt = Database::getConnection()->prepare(self::selectSql() . " WHERE cc.id=:id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ? Database::hydrate($row, 'DT_ChungChi_DTO') : null;
    }

    public static function getPaged(int $page, int $pageSize, array $opts = [], int $daXoa = 0): array
    {
        [$page, $pageSize, $offset] = PaginationHelper::normalize($page, $pageSize);
        $where  = " WHERE cc.da_xoa=:dx ";
        $params = [':dx' => $daXoa];

        if (!empty($opts['search'])) {
            $kw = '%' . $opts['search'] . '%';
            $where .= " AND (cc.so_chung_chi LIKE :s1 OR cc.ten_chung_chi LIKE :s2 OR hv.ho_ten LIKE :s3 OR hv.ma_hv LIKE :s4) ";
            $params[':s1'] = $kw; $params[':s2'] = $kw; $params[':s3'] = $kw; $params[':s4'] = $kw;
        }
        if (!empty($opts['loai_chung_chi'])) {
            $where .= " AND cc.loai_chung_chi=:loai ";
            $params[':loai'] = $opts['loai_chung_chi'];
        }
        if (!empty($opts['hoc_vien_id'])) {
            $where .= " AND cc.hoc_vien_id=:hv ";
            $params[':hv'] = (int)$opts['hoc_vien_id'];
        }
        if (!empty($opts['lop_hoc_id'])) {
            $where .= " AND cc.khoa_hoc_chuong_trinh_id=:lop ";
            $params[':lop'] = (int)$opts['lop_hoc_id'];
        }
        if (isset($opts['trang_thai']) && $opts['trang_thai'] !== '' && $opts['trang_thai'] !== null) {
            $where .= " AND cc.trang_thai=:tt ";
            $params[':tt'] = (int)$opts['trang_thai'];
        }

        $countSql = "SELECT COUNT(*) FROM DT_CHUNG_CHI cc
                     LEFT JOIN DM_HOC_VIEN hv ON hv.id = cc.hoc_vien_id" . $where;
        $stmt = Database::getConnection()->prepare($countSql);
        $stmt->execute($params);
        $total = (int)$stmt->fetchColumn();

        $sql = self::selectSql() . $where . " ORDER BY cc.ngay_cap DESC, cc.id DESC LIMIT {$pageSize} OFFSET {$offset}";
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute($params);
        return [
            'data'         => $stmt->fetchAll(),
            'totalRecords' => $total,
            'totalPages'   => PaginationHelper::totalPages($total, $pageSize),
        ];
    }

    public static function checkSoExists(string $so, int $excludeId = 0): bool
    {
        $stmt = Database::getConnection()->prepare(
            "SELECT COUNT(*) FROM DT_CHUNG_CHI WHERE so_chung_chi=:so AND da_xoa=0 AND id<>:id"
        );
        $stmt->execute([':so' => $so, ':id' => $excludeId]);
        return (int)$stmt->fetchColumn() > 0;
    }

    public static function getStats(): array
    {
        $sql = "SELECT
                  COUNT(*) AS total,
                  SUM(CASE WHEN trang_thai=1 THEN 1 ELSE 0 END) AS so_da_cap,
                  SUM(CASE WHEN trang_thai=0 THEN 1 ELSE 0 END) AS so_nhap,
                  SUM(CASE WHEN trang_thai=2 THEN 1 ELSE 0 END) AS so_thu_hoi,
                  COUNT(DISTINCT hoc_vien_id) AS so_hoc_vien
                FROM DT_CHUNG_CHI WHERE da_xoa=0";
        return Database::getConnection()->query($sql)->fetch() ?: [];
    }

    public static function getByHocVien(int $hocVienId): array
    {
        $stmt = Database::getConnection()->prepare(
            self::selectSql() . " WHERE cc.hoc_vien_id=:hv AND cc.da_xoa=0 ORDER BY cc.ngay_cap DESC"
        );
        $stmt->execute([':hv' => $hocVienId]);
        return $stmt->fetchAll();
    }
}
