<?php
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/../PUBLIC/Entities/DM_NhanVien_DTO.php';

class DM_NhanVien_DAL
{
    private static function selectSql(): string
    {
        return "SELECT nv.*,
                       bv.ten_benh_vien,
                       kp.ten_khoa AS ten_khoa_phong, kp.loai_don_vi,
                       u1.tai_khoan AS tai_khoan_nguoi_tao,
                       u2.tai_khoan AS tai_khoan_nguoi_cap_nhat
                FROM DM_NHAN_VIEN nv
                LEFT JOIN DM_BENH_VIEN bv ON bv.id = nv.benh_vien_id
                LEFT JOIN DM_KHOA_PHONG kp ON kp.id = nv.khoa_phong_id
                LEFT JOIN DM_NGUOI_DUNG u1 ON u1.id = nv.nguoi_tao
                LEFT JOIN DM_NGUOI_DUNG u2 ON u2.id = nv.nguoi_cap_nhat";
    }

    public static function insert(DM_NhanVien_PUBLIC $e): int
    {
        $sql = "INSERT INTO DM_NHAN_VIEN
                (benh_vien_id, ma_nv, ho_ten, ngay_sinh, gioi_tinh, chuc_danh, khoa_phong_id, khoa_phong_text, trinh_do, chuyen_khoa,
                 pham_vi_hanh_nghe, so_cchn, ngay_cap_cchn, qd_bo_sung_pham_vi, dieu_chinh_pham_vi, ngay_dieu_chinh, chuyen_khoa_cap_nhat,
                 dien_thoai, email, dia_chi, trang_thai, ngay_tao, ngay_cap_nhat, nguoi_tao, nguoi_cap_nhat, da_xoa)
                VALUES (:bv, :ma, :ht, :ns, :gt, :cd, :kp, :kpt, :td, :ck,
                        :pvhn, :cchn, :ncc, :qdbs, :dcpv, :ndc, :ckcn,
                        :dt, :em, :dc, :tt, NOW(), NOW(), :u1, :u2, 0)";
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute([
            ':bv' => $e->benh_vien_id, ':ma' => $e->ma_nv, ':ht' => $e->ho_ten,
            ':ns' => $e->ngay_sinh ?: null, ':gt' => $e->gioi_tinh, ':cd' => $e->chuc_danh,
            ':kp' => $e->khoa_phong_id, ':kpt' => $e->khoa_phong_text, ':td' => $e->trinh_do, ':ck' => $e->chuyen_khoa,
            ':pvhn' => $e->pham_vi_hanh_nghe, ':cchn' => $e->so_cchn, ':ncc' => $e->ngay_cap_cchn ?: null,
            ':qdbs' => $e->qd_bo_sung_pham_vi, ':dcpv' => $e->dieu_chinh_pham_vi,
            ':ndc' => $e->ngay_dieu_chinh ?: null, ':ckcn' => $e->chuyen_khoa_cap_nhat,
            ':dt' => $e->dien_thoai, ':em' => $e->email, ':dc' => $e->dia_chi,
            ':tt' => $e->trang_thai, ':u1' => $e->nguoi_tao, ':u2' => $e->nguoi_tao,
        ]);
        return (int)Database::getConnection()->lastInsertId();
    }

    public static function update(DM_NhanVien_PUBLIC $e): int
    {
        $sql = "UPDATE DM_NHAN_VIEN SET
                benh_vien_id=:bv, ma_nv=:ma, ho_ten=:ht, ngay_sinh=:ns, gioi_tinh=:gt, chuc_danh=:cd,
                khoa_phong_id=:kp, khoa_phong_text=:kpt, trinh_do=:td, chuyen_khoa=:ck,
                pham_vi_hanh_nghe=:pvhn, so_cchn=:cchn, ngay_cap_cchn=:ncc, qd_bo_sung_pham_vi=:qdbs,
                dieu_chinh_pham_vi=:dcpv, ngay_dieu_chinh=:ndc, chuyen_khoa_cap_nhat=:ckcn,
                dien_thoai=:dt, email=:em, dia_chi=:dc,
                trang_thai=:tt, ngay_cap_nhat=NOW(), nguoi_cap_nhat=:u
                WHERE id=:id AND da_xoa=0";
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute([
            ':bv' => $e->benh_vien_id, ':ma' => $e->ma_nv, ':ht' => $e->ho_ten,
            ':ns' => $e->ngay_sinh ?: null, ':gt' => $e->gioi_tinh, ':cd' => $e->chuc_danh,
            ':kp' => $e->khoa_phong_id, ':kpt' => $e->khoa_phong_text, ':td' => $e->trinh_do, ':ck' => $e->chuyen_khoa,
            ':pvhn' => $e->pham_vi_hanh_nghe, ':cchn' => $e->so_cchn, ':ncc' => $e->ngay_cap_cchn ?: null,
            ':qdbs' => $e->qd_bo_sung_pham_vi, ':dcpv' => $e->dieu_chinh_pham_vi,
            ':ndc' => $e->ngay_dieu_chinh ?: null, ':ckcn' => $e->chuyen_khoa_cap_nhat,
            ':dt' => $e->dien_thoai, ':em' => $e->email, ':dc' => $e->dia_chi,
            ':tt' => $e->trang_thai, ':u' => $e->nguoi_cap_nhat, ':id' => $e->id,
        ]);
        return $stmt->rowCount();
    }

    public static function trash(int $id, int $u): int
    {
        $stmt = Database::getConnection()->prepare("UPDATE DM_NHAN_VIEN SET da_xoa=1, ngay_cap_nhat=NOW(), nguoi_cap_nhat=:u WHERE id=:id");
        $stmt->execute([':u' => $u, ':id' => $id]);
        return $stmt->rowCount();
    }

    public static function restore(int $id, int $u): int
    {
        $stmt = Database::getConnection()->prepare("UPDATE DM_NHAN_VIEN SET da_xoa=0, ngay_cap_nhat=NOW(), nguoi_cap_nhat=:u WHERE id=:id");
        $stmt->execute([':u' => $u, ':id' => $id]);
        return $stmt->rowCount();
    }

    public static function delete(int $id): int
    {
        $stmt = Database::getConnection()->prepare("DELETE FROM DM_NHAN_VIEN WHERE id=:id");
        $stmt->execute([':id' => $id]);
        return $stmt->rowCount();
    }

    public static function getById(int $id): ?DM_NhanVien_DTO
    {
        $stmt = Database::getConnection()->prepare(self::selectSql() . " WHERE nv.id=:id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ? Database::hydrate($row, 'DM_NhanVien_DTO') : null;
    }

    public static function getPaged(int $page, int $pageSize, string $search = '', int $daXoa = 0, int $khoaPhongId = 0): array
    {
        [$page, $pageSize, $offset] = PaginationHelper::normalize($page, $pageSize);
        $where = " WHERE nv.da_xoa=:dx ";
        $params = [':dx' => $daXoa];
        if ($search !== '') {
            $kw = "%{$search}%";
            $where .= " AND (nv.ma_nv LIKE :s1 OR nv.ho_ten LIKE :s2 OR nv.email LIKE :s3 OR nv.dien_thoai LIKE :s4) ";
            $params[':s1'] = $kw; $params[':s2'] = $kw; $params[':s3'] = $kw; $params[':s4'] = $kw;
        }
        if ($khoaPhongId > 0) {
            $where .= " AND nv.khoa_phong_id=:kp ";
            $params[':kp'] = $khoaPhongId;
        }

        $countSql = "SELECT COUNT(*) FROM DM_NHAN_VIEN nv" . $where;
        $stmt = Database::getConnection()->prepare($countSql);
        $stmt->execute($params);
        $total = (int)$stmt->fetchColumn();

        $sql = self::selectSql() . $where . " ORDER BY nv.id DESC LIMIT {$pageSize} OFFSET {$offset}";
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute($params);
        return [
            'data' => $stmt->fetchAll(),
            'totalRecords' => $total,
            'totalPages' => PaginationHelper::totalPages($total, $pageSize),
        ];
    }

    public static function checkMaExists(int $benhVienId, string $ma, int $excludeId = 0): bool
    {
        $stmt = Database::getConnection()->prepare("SELECT COUNT(*) FROM DM_NHAN_VIEN WHERE benh_vien_id=:bv AND ma_nv=:m AND da_xoa=0 AND id<>:id");
        $stmt->execute([':bv' => $benhVienId, ':m' => $ma, ':id' => $excludeId]);
        return (int)$stmt->fetchColumn() > 0;
    }

    /**
     * Tìm nhân viên theo từ khóa (mã/họ tên) + lọc khoa, giới hạn số kết quả — cho typeahead.
     * @return array [{id, ma_nv, ho_ten, ten_khoa_phong}]
     */
    public static function search(string $keyword = '', int $khoaPhongId = 0, int $limit = 20): array
    {
        $limit = max(1, min(50, $limit));
        $sql = "SELECT nv.id, nv.ma_nv, nv.ho_ten, kp.ten_khoa AS ten_khoa_phong
                FROM DM_NHAN_VIEN nv
                LEFT JOIN DM_KHOA_PHONG kp ON kp.id = nv.khoa_phong_id
                WHERE nv.da_xoa=0 AND nv.trang_thai=1";
        $params = [];
        $kw = trim($keyword);
        if ($kw !== '') {
            $sql .= " AND (nv.ma_nv LIKE :k1 OR nv.ho_ten LIKE :k2)";
            $params[':k1'] = "%{$kw}%"; $params[':k2'] = "%{$kw}%";
        }
        if ($khoaPhongId > 0) { $sql .= " AND nv.khoa_phong_id=:kp"; $params[':kp'] = $khoaPhongId; }
        $sql .= " ORDER BY nv.ho_ten ASC LIMIT {$limit}";
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll() ?: [];
    }

    /** Lấy 1 nhân viên (id, mã, tên, khoa) cho hiển thị chip đã chọn. */
    public static function getBrief(int $id): ?array
    {
        $stmt = Database::getConnection()->prepare(
            "SELECT nv.id, nv.ma_nv, nv.ho_ten, kp.ten_khoa AS ten_khoa_phong
             FROM DM_NHAN_VIEN nv LEFT JOIN DM_KHOA_PHONG kp ON kp.id = nv.khoa_phong_id
             WHERE nv.id=:id AND nv.da_xoa=0"
        );
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function getCombo(int $khoaPhongId = 0): array
    {
        $key = 'dm_nhan_vien:combo:' . $khoaPhongId;
        $cached = MemcachedHelper::get($key);
        if ($cached !== null) return $cached;
        $sql = "SELECT id, ma_nv, ho_ten FROM DM_NHAN_VIEN WHERE da_xoa=0 AND trang_thai=1";
        $params = [];
        if ($khoaPhongId > 0) { $sql .= " AND khoa_phong_id=:kp"; $params[':kp'] = $khoaPhongId; }
        $sql .= " ORDER BY ho_ten";
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute($params);
        $data = $stmt->fetchAll();
        MemcachedHelper::set($key, $data, Constants::CACHE_TTL_COMBO);
        return $data;
    }
}
