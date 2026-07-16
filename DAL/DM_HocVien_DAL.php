<?php
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/../PUBLIC/Entities/DM_HocVien_DTO.php';

class DM_HocVien_DAL
{
    private static function selectSql(): string
    {
        return "SELECT hv.*,
                       dt.ten_doi_tuong, dt.ma_doi_tuong,
                       nv.ma_nv,
                       kp.ten_khoa AS ten_khoa_phong,
                       bv.ten_benh_vien,
                       (SELECT COUNT(*) FROM DT_HOC_VIEN_LOP hvl
                          WHERE hvl.hoc_vien_id = hv.id AND hvl.da_xoa = 0) AS so_ghi_danh,
                       u1.tai_khoan AS tai_khoan_nguoi_tao,
                       u2.tai_khoan AS tai_khoan_nguoi_cap_nhat
                FROM DM_HOC_VIEN hv
                LEFT JOIN DM_DOI_TUONG_HOC_VIEN dt ON dt.id = hv.doi_tuong_id
                LEFT JOIN DM_NHAN_VIEN nv ON nv.id = hv.nhan_vien_id
                LEFT JOIN DM_KHOA_PHONG kp ON kp.id = nv.khoa_phong_id
                LEFT JOIN DM_BENH_VIEN bv ON bv.id = nv.benh_vien_id
                LEFT JOIN DM_NGUOI_DUNG u1 ON u1.id = hv.nguoi_tao
                LEFT JOIN DM_NGUOI_DUNG u2 ON u2.id = hv.nguoi_cap_nhat";
    }

    public static function insert(DM_HocVien_PUBLIC $e): int
    {
        $sql = "INSERT INTO DM_HOC_VIEN
                (ma_hv, ho_ten, ngay_sinh, gioi_tinh, trinh_do_chuyen_mon, dien_thoai, email,
                 cccd, cccd_ngay_cap, cccd_noi_cap, dia_chi, truong_dao_tao, nam_tot_nghiep,
                 don_vi_cong_tac, chuc_vu, doi_tuong_id, la_nhan_vien, nhan_vien_id, avatar, ghi_chu,
                 trang_thai, ngay_tao, ngay_cap_nhat, nguoi_tao, nguoi_cap_nhat, da_xoa)
                VALUES (:ma, :ht, :ns, :gt, :tdcm, :dt, :em,
                        :cccd, :ccngc, :ccnc, :dc, :truong, :namtn,
                        :dv, :cv, :dti, :lnv, :nvi, :av, :gc,
                        :tt, NOW(), NOW(), :u1, :u2, 0)";
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute([
            ':ma' => $e->ma_hv, ':ht' => $e->ho_ten,
            ':ns' => $e->ngay_sinh ?: null, ':gt' => $e->gioi_tinh, ':tdcm' => $e->trinh_do_chuyen_mon,
            ':dt' => $e->dien_thoai, ':em' => $e->email,
            ':cccd' => $e->cccd ?: null, ':ccngc' => $e->cccd_ngay_cap ?: null, ':ccnc' => $e->cccd_noi_cap,
            ':dc' => $e->dia_chi, ':truong' => $e->truong_dao_tao, ':namtn' => $e->nam_tot_nghiep ?: null,
            ':dv' => $e->don_vi_cong_tac, ':cv' => $e->chuc_vu,
            ':dti' => $e->doi_tuong_id, ':lnv' => $e->la_nhan_vien, ':nvi' => $e->nhan_vien_id,
            ':av' => $e->avatar, ':gc' => $e->ghi_chu,
            ':tt' => $e->trang_thai, ':u1' => $e->nguoi_tao, ':u2' => $e->nguoi_tao,
        ]);
        return (int)Database::getConnection()->lastInsertId();
    }

    public static function update(DM_HocVien_PUBLIC $e): int
    {
        // Nếu avatar=null thì giữ nguyên; nếu rỗng "" thì xóa; nếu có value thì cập nhật
        $avatarClause = '';
        if ($e->avatar !== null) $avatarClause = ', avatar=:av';

        $sql = "UPDATE DM_HOC_VIEN SET
                ma_hv=:ma, ho_ten=:ht, ngay_sinh=:ns, gioi_tinh=:gt, trinh_do_chuyen_mon=:tdcm,
                dien_thoai=:dt, email=:em, cccd=:cccd, cccd_ngay_cap=:ccngc, cccd_noi_cap=:ccnc,
                dia_chi=:dc, truong_dao_tao=:truong, nam_tot_nghiep=:namtn,
                don_vi_cong_tac=:dv, chuc_vu=:cv,
                doi_tuong_id=:dti, la_nhan_vien=:lnv, nhan_vien_id=:nvi,
                ghi_chu=:gc, trang_thai=:tt, ngay_cap_nhat=NOW(), nguoi_cap_nhat=:u
                {$avatarClause}
                WHERE id=:id AND da_xoa=0";
        $stmt = Database::getConnection()->prepare($sql);
        $params = [
            ':ma' => $e->ma_hv, ':ht' => $e->ho_ten,
            ':ns' => $e->ngay_sinh ?: null, ':gt' => $e->gioi_tinh, ':tdcm' => $e->trinh_do_chuyen_mon,
            ':dt' => $e->dien_thoai, ':em' => $e->email,
            ':cccd' => $e->cccd ?: null, ':ccngc' => $e->cccd_ngay_cap ?: null, ':ccnc' => $e->cccd_noi_cap,
            ':dc' => $e->dia_chi, ':truong' => $e->truong_dao_tao, ':namtn' => $e->nam_tot_nghiep ?: null,
            ':dv' => $e->don_vi_cong_tac, ':cv' => $e->chuc_vu,
            ':dti' => $e->doi_tuong_id, ':lnv' => $e->la_nhan_vien, ':nvi' => $e->nhan_vien_id,
            ':gc' => $e->ghi_chu, ':tt' => $e->trang_thai,
            ':u' => $e->nguoi_cap_nhat, ':id' => $e->id,
        ];
        if ($e->avatar !== null) $params[':av'] = $e->avatar ?: null;
        $stmt->execute($params);
        return $stmt->rowCount();
    }

    public static function trash(int $id, int $u): int
    {
        $stmt = Database::getConnection()->prepare("UPDATE DM_HOC_VIEN SET da_xoa=1, ngay_cap_nhat=NOW(), nguoi_cap_nhat=:u WHERE id=:id");
        $stmt->execute([':u' => $u, ':id' => $id]);
        return $stmt->rowCount();
    }

    public static function restore(int $id, int $u): int
    {
        $stmt = Database::getConnection()->prepare("UPDATE DM_HOC_VIEN SET da_xoa=0, ngay_cap_nhat=NOW(), nguoi_cap_nhat=:u WHERE id=:id");
        $stmt->execute([':u' => $u, ':id' => $id]);
        return $stmt->rowCount();
    }

    public static function delete(int $id): int
    {
        $stmt = Database::getConnection()->prepare("DELETE FROM DM_HOC_VIEN WHERE id=:id");
        $stmt->execute([':id' => $id]);
        return $stmt->rowCount();
    }

    /** Tìm HV theo CCCD (không bị xóa). Trả mảng (có thể nhiều). */
    public static function findByCccd(string $cccd): array
    {
        if ($cccd === '') return [];
        $stmt = Database::getConnection()->prepare(
            self::selectSql() . " WHERE hv.cccd=:c AND hv.da_xoa=0"
        );
        $stmt->execute([':c' => $cccd]);
        return $stmt->fetchAll();
    }

    /** Tìm HV theo SĐT (không bị xóa). Trả mảng. */
    public static function findByDienThoai(string $sdt): array
    {
        if ($sdt === '') return [];
        $stmt = Database::getConnection()->prepare(
            self::selectSql() . " WHERE hv.dien_thoai=:p AND hv.da_xoa=0"
        );
        $stmt->execute([':p' => $sdt]);
        return $stmt->fetchAll();
    }

    public static function getById(int $id): ?DM_HocVien_DTO
    {
        $stmt = Database::getConnection()->prepare(self::selectSql() . " WHERE hv.id=:id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ? Database::hydrate($row, 'DM_HocVien_DTO') : null;
    }

    public static function getPaged(int $page, int $pageSize, string $search = '', int $daXoa = 0, int $doiTuongId = 0, int $laNhanVien = -1, string $tuNgay = '', string $denNgay = ''): array
    {
        [$page, $pageSize, $offset] = PaginationHelper::normalize($page, $pageSize);
        $where = " WHERE hv.da_xoa=:dx ";
        $params = [':dx' => $daXoa];
        if ($search !== '') {
            $where .= " AND (hv.ma_hv LIKE :s1 OR hv.ho_ten LIKE :s2 OR hv.email LIKE :s3 OR hv.dien_thoai LIKE :s4 OR hv.don_vi_cong_tac LIKE :s5) ";
            $kw = "%{$search}%";
            $params[':s1'] = $kw; $params[':s2'] = $kw; $params[':s3'] = $kw; $params[':s4'] = $kw; $params[':s5'] = $kw;
        }
        if ($doiTuongId > 0) {
            $where .= " AND hv.doi_tuong_id=:dti ";
            $params[':dti'] = $doiTuongId;
        }
        if ($laNhanVien === 0 || $laNhanVien === 1) {
            $where .= " AND hv.la_nhan_vien=:lnv ";
            $params[':lnv'] = $laNhanVien;
        }
        // Lọc theo ngày tạo (theo phần ngày, bao gồm cả 2 mốc)
        if ($tuNgay !== '') {
            $where .= " AND hv.ngay_tao >= :tuNgay ";
            $params[':tuNgay'] = $tuNgay . ' 00:00:00';
        }
        if ($denNgay !== '') {
            $where .= " AND hv.ngay_tao <= :denNgay ";
            $params[':denNgay'] = $denNgay . ' 23:59:59';
        }

        $countSql = "SELECT COUNT(*) FROM DM_HOC_VIEN hv" . $where;
        $stmt = Database::getConnection()->prepare($countSql);
        $stmt->execute($params);
        $total = (int)$stmt->fetchColumn();

        $sql = self::selectSql() . $where . " ORDER BY hv.id DESC LIMIT {$pageSize} OFFSET {$offset}";
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
        $stmt = Database::getConnection()->prepare("SELECT COUNT(*) FROM DM_HOC_VIEN WHERE ma_hv=:m AND da_xoa=0 AND id<>:id");
        $stmt->execute([':m' => $ma, ':id' => $excludeId]);
        return (int)$stmt->fetchColumn() > 0;
    }

    public static function checkNhanVienExists(int $nhanVienId, int $excludeId = 0): bool
    {
        $stmt = Database::getConnection()->prepare("SELECT COUNT(*) FROM DM_HOC_VIEN WHERE nhan_vien_id=:nv AND da_xoa=0 AND id<>:id");
        $stmt->execute([':nv' => $nhanVienId, ':id' => $excludeId]);
        return (int)$stmt->fetchColumn() > 0;
    }

    /**
     * Tìm học viên trùng CCCD hoặc SĐT (loại trừ chính bản ghi đang sửa).
     * Trả về dòng đầu tiên trùng hoặc null.
     */
    public static function findDuplicate(?string $cccd, ?string $sdt, int $excludeId = 0): ?array
    {
        $cccd = trim((string)$cccd);
        $sdt = trim((string)$sdt);
        if ($cccd === '' && $sdt === '') return null;

        $conds = [];
        $params = [':id' => $excludeId];
        if ($cccd !== '') { $conds[] = 'cccd = :cccd'; $params[':cccd'] = $cccd; }
        if ($sdt !== '')  { $conds[] = 'dien_thoai = :sdt'; $params[':sdt'] = $sdt; }

        $sql = "SELECT id, ma_hv, ho_ten, cccd, dien_thoai FROM DM_HOC_VIEN
                WHERE da_xoa=0 AND id<>:id AND (" . implode(' OR ', $conds) . ") LIMIT 1";
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute($params);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function getAvatarPath(int $id): ?string
    {
        $stmt = Database::getConnection()->prepare("SELECT avatar FROM DM_HOC_VIEN WHERE id=:id");
        $stmt->execute([':id' => $id]);
        $v = $stmt->fetchColumn();
        return $v ?: null;
    }

    public static function getStats(): array
    {
        $sql = "SELECT
                  COUNT(*) AS total,
                  SUM(CASE WHEN trang_thai=1 THEN 1 ELSE 0 END) AS active,
                  SUM(CASE WHEN la_nhan_vien=1 THEN 1 ELSE 0 END) AS la_nv,
                  SUM(CASE WHEN la_nhan_vien=0 THEN 1 ELSE 0 END) AS ngoai
                FROM DM_HOC_VIEN WHERE da_xoa=0";
        return Database::getConnection()->query($sql)->fetch() ?: ['total'=>0,'active'=>0,'la_nv'=>0,'ngoai'=>0];
    }

    public static function getCombo(): array
    {
        $stmt = Database::getConnection()->query(
            "SELECT id, ma_hv AS ma_hoc_vien, ho_ten FROM DM_HOC_VIEN WHERE da_xoa=0 AND trang_thai=1 ORDER BY ho_ten ASC"
        );
        return $stmt->fetchAll() ?: [];
    }
}
