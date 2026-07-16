<?php
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/../PUBLIC/Entities/DT_KhoaHoc_DTO.php';

class DT_KhoaHoc_DAL
{
    private static function selectSql(): string
    {
        return "SELECT kh.*,
                       lh.ten_loai_hinh,
                       ht.ten_hinh_thuc,
                       dt.ten_doi_tuong,
                       dd.ten_dot, dd.tu_ngay AS dot_tu_ngay, dd.den_ngay AS dot_den_ngay,
                       u1.tai_khoan AS tai_khoan_nguoi_tao,
                       u2.tai_khoan AS tai_khoan_nguoi_cap_nhat
                FROM DT_KHOA_HOC kh
                LEFT JOIN DM_LOAI_HINH_DAO_TAO lh ON lh.id = kh.loai_hinh_dao_tao_id
                LEFT JOIN DM_HINH_THUC_HOC ht ON ht.id = kh.hinh_thuc_hoc_id
                LEFT JOIN DM_DOI_TUONG_HOC_VIEN dt ON dt.id = kh.doi_tuong_hoc_vien_id
                LEFT JOIN DT_DOT_DANG_KY dd ON dd.id = kh.dot_dang_ky_id
                LEFT JOIN DM_NGUOI_DUNG u1 ON u1.id = kh.nguoi_tao
                LEFT JOIN DM_NGUOI_DUNG u2 ON u2.id = kh.nguoi_cap_nhat";
    }

    public static function insert(DT_KhoaHoc_PUBLIC $e): int
    {
        $sql = "INSERT INTO DT_KHOA_HOC
                (ma_khoa_hoc, ten_khoa_hoc, mo_ta, muc_tieu, loai_hinh_dao_tao_id, hinh_thuc_hoc_id,
                 doi_tuong_hoc_vien_id, dot_dang_ky_id, dieu_kien, ngay_bat_dau, ngay_ket_thuc,
                 trang_thai, ngay_tao, ngay_cap_nhat, nguoi_tao, nguoi_cap_nhat, da_xoa)
                VALUES (:ma, :ten, :mt, :muc, :lh, :ht, :dt, :dot, :dk, :nbd, :nkt, :tt,
                        NOW(), NOW(), :u1, :u2, 0)";
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute([
            ':ma' => $e->ma_khoa_hoc, ':ten' => $e->ten_khoa_hoc, ':mt' => $e->mo_ta, ':muc' => $e->muc_tieu,
            ':lh' => $e->loai_hinh_dao_tao_id, ':ht' => $e->hinh_thuc_hoc_id, ':dt' => $e->doi_tuong_hoc_vien_id,
            ':dot' => $e->dot_dang_ky_id,
            ':dk' => $e->dieu_kien,
            ':nbd' => $e->ngay_bat_dau ?: null, ':nkt' => $e->ngay_ket_thuc ?: null,
            ':tt' => $e->trang_thai,
            ':u1' => $e->nguoi_tao ?? 0, ':u2' => $e->nguoi_tao ?? 0,
        ]);
        return (int)Database::getConnection()->lastInsertId();
    }

    public static function update(DT_KhoaHoc_PUBLIC $e): int
    {
        $sql = "UPDATE DT_KHOA_HOC SET
                ma_khoa_hoc=:ma, ten_khoa_hoc=:ten, mo_ta=:mt, muc_tieu=:muc,
                loai_hinh_dao_tao_id=:lh, hinh_thuc_hoc_id=:ht, doi_tuong_hoc_vien_id=:dt, dot_dang_ky_id=:dot,
                dieu_kien=:dk, ngay_bat_dau=:nbd, ngay_ket_thuc=:nkt,
                trang_thai=:tt, ngay_cap_nhat=NOW(), nguoi_cap_nhat=:u
                WHERE id=:id AND da_xoa=0";
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute([
            ':ma' => $e->ma_khoa_hoc, ':ten' => $e->ten_khoa_hoc, ':mt' => $e->mo_ta, ':muc' => $e->muc_tieu,
            ':lh' => $e->loai_hinh_dao_tao_id, ':ht' => $e->hinh_thuc_hoc_id, ':dt' => $e->doi_tuong_hoc_vien_id,
            ':dot' => $e->dot_dang_ky_id,
            ':dk' => $e->dieu_kien,
            ':nbd' => $e->ngay_bat_dau ?: null, ':nkt' => $e->ngay_ket_thuc ?: null,
            ':tt' => $e->trang_thai,
            ':u' => $e->nguoi_cap_nhat ?? 0, ':id' => $e->id,
        ]);
        return $stmt->rowCount();
    }

    public static function trash(int $id, int $u): int
    {
        $stmt = Database::getConnection()->prepare("UPDATE DT_KHOA_HOC SET da_xoa=1, ngay_cap_nhat=NOW(), nguoi_cap_nhat=:u WHERE id=:id");
        $stmt->execute([':u' => $u, ':id' => $id]);
        return $stmt->rowCount();
    }

    public static function restore(int $id, int $u): int
    {
        $stmt = Database::getConnection()->prepare("UPDATE DT_KHOA_HOC SET da_xoa=0, ngay_cap_nhat=NOW(), nguoi_cap_nhat=:u WHERE id=:id");
        $stmt->execute([':u' => $u, ':id' => $id]);
        return $stmt->rowCount();
    }

    public static function delete(int $id): int
    {
        $stmt = Database::getConnection()->prepare("DELETE FROM DT_KHOA_HOC WHERE id=:id");
        $stmt->execute([':id' => $id]);
        return $stmt->rowCount();
    }

    public static function getById(int $id): ?DT_KhoaHoc_DTO
    {
        $stmt = Database::getConnection()->prepare(self::selectSql() . " WHERE kh.id=:id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ? Database::hydrate($row, 'DT_KhoaHoc_DTO') : null;
    }

    public static function getPaged(int $page, int $pageSize, string $search = '', int $daXoa = 0,
                                    int $loaiHinhId = 0, int $hinhThucId = 0, int $doiTuongId = 0): array
    {
        [$page, $pageSize, $offset] = PaginationHelper::normalize($page, $pageSize);
        $where = " WHERE kh.da_xoa=:dx ";
        $params = [':dx' => $daXoa];
        if ($search !== '') {
            $where .= " AND (kh.ma_khoa_hoc LIKE :s1 OR kh.ten_khoa_hoc LIKE :s2) ";
            $kw = "%{$search}%";
            $params[':s1'] = $kw; $params[':s2'] = $kw;
        }
        if ($loaiHinhId > 0) { $where .= " AND kh.loai_hinh_dao_tao_id=:lh "; $params[':lh'] = $loaiHinhId; }
        if ($hinhThucId > 0) { $where .= " AND kh.hinh_thuc_hoc_id=:ht "; $params[':ht'] = $hinhThucId; }
        if ($doiTuongId > 0) { $where .= " AND kh.doi_tuong_hoc_vien_id=:dt "; $params[':dt'] = $doiTuongId; }

        $countSql = "SELECT COUNT(*) FROM DT_KHOA_HOC kh" . $where;
        $stmt = Database::getConnection()->prepare($countSql);
        $stmt->execute($params);
        $total = (int)$stmt->fetchColumn();

        $sql = self::selectSql() . $where . " ORDER BY kh.id DESC LIMIT {$pageSize} OFFSET {$offset}";
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
        $stmt = Database::getConnection()->prepare("SELECT COUNT(*) FROM DT_KHOA_HOC WHERE ma_khoa_hoc=:m AND da_xoa=0 AND id<>:id");
        $stmt->execute([':m' => $ma, ':id' => $excludeId]);
        return (int)$stmt->fetchColumn() > 0;
    }

    public static function getCombo(): array
    {
        $key = 'dt_khoa_hoc:combo';
        $cached = MemcachedHelper::get($key);
        if ($cached !== null) return $cached;
        $stmt = Database::getConnection()->query("SELECT id, ma_khoa_hoc, ten_khoa_hoc FROM DT_KHOA_HOC WHERE da_xoa=0 AND trang_thai=1 ORDER BY ten_khoa_hoc");
        $data = $stmt->fetchAll();
        MemcachedHelper::set($key, $data, Constants::CACHE_TTL_COMBO);
        return $data;
    }

    /**
     * Lấy các khóa học đang mở phase Submit cho form đăng ký công khai.
     * Điều kiện: khóa active, gắn đợt active, có giai đoạn Submit nằm trong NOW().
     */
    public static function getComboOpenForRegistration(): array
    {
        $sql = "SELECT DISTINCT kh.id, kh.ma_khoa_hoc, kh.ten_khoa_hoc, dd.ten_dot, dd.den_ngay AS dot_den_ngay
                FROM DT_KHOA_HOC kh
                INNER JOIN DT_DOT_DANG_KY dd ON dd.id = kh.dot_dang_ky_id AND dd.da_xoa = 0 AND dd.trang_thai = 1
                INNER JOIN DT_DOT_GIAI_DOAN gd ON gd.dot_id = dd.id AND gd.da_xoa = 0
                    AND gd.hanh_vi = 'Submit' AND gd.tu_ngay <= NOW() AND gd.den_ngay >= NOW()
                WHERE kh.da_xoa = 0 AND kh.trang_thai = 1
                ORDER BY kh.ten_khoa_hoc";
        return Database::getConnection()->query($sql)->fetchAll();
    }
}
