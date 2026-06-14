<?php
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/../PUBLIC/Entities/DM_GiangVien_DTO.php';

class DM_GiangVien_DAL
{
    private static function selectSql(): string
    {
        return "SELECT gv.*,
                       nv.ma_nv,
                       kp.ten_khoa AS ten_khoa_phong,
                       (SELECT COUNT(*) FROM DT_PHAN_CONG_GIANG_VIEN pc
                          WHERE pc.giang_vien_id = gv.id AND pc.da_xoa = 0) AS so_lop_phan_cong,
                       (SELECT COUNT(*) FROM DT_LICH_HOC lh
                          WHERE lh.giang_vien_id = gv.nhan_vien_id AND lh.trang_thai = 1 AND lh.da_xoa = 0) AS so_buoi_da_day,
                       u.tai_khoan AS tai_khoan_nguoi_tao
                FROM DM_GIANG_VIEN gv
                LEFT JOIN DM_NHAN_VIEN nv ON nv.id = gv.nhan_vien_id
                LEFT JOIN DM_KHOA_PHONG kp ON kp.id = nv.khoa_phong_id
                LEFT JOIN DM_NGUOI_DUNG u ON u.id = gv.nguoi_tao";
    }

    public static function insert(DM_GiangVien_PUBLIC $e): int
    {
        $u = $e->nguoi_tao ?? 0;
        $sql = "INSERT INTO DM_GIANG_VIEN
                (ma_gv, ho_ten, ngay_sinh, gioi_tinh, email, dien_thoai, avatar,
                 hoc_vi, hoc_ham, chuyen_mon, nhan_vien_id, don_vi_cong_tac,
                 loai_gv, trang_thai, ghi_chu,
                 ngay_tao, ngay_cap_nhat, nguoi_tao, nguoi_cap_nhat, da_xoa)
                VALUES (:ma, :ho, :ns, :gt, :em, :dt, :av,
                        :hv, :hh, :cm, :nv, :dv,
                        :lg, :tt, :gc,
                        NOW(), NOW(), :u1, :u2, 0)";
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute([
            ':ma' => $e->ma_gv, ':ho' => $e->ho_ten,
            ':ns' => $e->ngay_sinh ?: null, ':gt' => $e->gioi_tinh ?: null,
            ':em' => $e->email, ':dt' => $e->dien_thoai, ':av' => $e->avatar,
            ':hv' => $e->hoc_vi, ':hh' => $e->hoc_ham, ':cm' => $e->chuyen_mon,
            ':nv' => $e->nhan_vien_id, ':dv' => $e->don_vi_cong_tac,
            ':lg' => $e->loai_gv, ':tt' => $e->trang_thai, ':gc' => $e->ghi_chu,
            ':u1' => $u, ':u2' => $u,
        ]);
        return (int)Database::getConnection()->lastInsertId();
    }

    public static function update(DM_GiangVien_PUBLIC $e): int
    {
        $sql = "UPDATE DM_GIANG_VIEN SET
                ma_gv=:ma, ho_ten=:ho, ngay_sinh=:ns, gioi_tinh=:gt,
                email=:em, dien_thoai=:dt, avatar=:av,
                hoc_vi=:hv, hoc_ham=:hh, chuyen_mon=:cm,
                nhan_vien_id=:nv, don_vi_cong_tac=:dv,
                loai_gv=:lg, trang_thai=:tt, ghi_chu=:gc,
                ngay_cap_nhat=NOW(), nguoi_cap_nhat=:u
                WHERE id=:id AND da_xoa=0";
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute([
            ':ma' => $e->ma_gv, ':ho' => $e->ho_ten,
            ':ns' => $e->ngay_sinh ?: null, ':gt' => $e->gioi_tinh ?: null,
            ':em' => $e->email, ':dt' => $e->dien_thoai, ':av' => $e->avatar,
            ':hv' => $e->hoc_vi, ':hh' => $e->hoc_ham, ':cm' => $e->chuyen_mon,
            ':nv' => $e->nhan_vien_id, ':dv' => $e->don_vi_cong_tac,
            ':lg' => $e->loai_gv, ':tt' => $e->trang_thai, ':gc' => $e->ghi_chu,
            ':u' => $e->nguoi_cap_nhat ?? 0, ':id' => $e->id,
        ]);
        return $stmt->rowCount();
    }

    public static function trash(int $id, int $u): int
    {
        $stmt = Database::getConnection()->prepare("UPDATE DM_GIANG_VIEN SET da_xoa=1, ngay_cap_nhat=NOW(), nguoi_cap_nhat=:u WHERE id=:id");
        $stmt->execute([':u' => $u, ':id' => $id]);
        return $stmt->rowCount();
    }

    public static function restore(int $id, int $u): int
    {
        $stmt = Database::getConnection()->prepare("UPDATE DM_GIANG_VIEN SET da_xoa=0, ngay_cap_nhat=NOW(), nguoi_cap_nhat=:u WHERE id=:id");
        $stmt->execute([':u' => $u, ':id' => $id]);
        return $stmt->rowCount();
    }

    public static function delete(int $id): int
    {
        $stmt = Database::getConnection()->prepare("DELETE FROM DM_GIANG_VIEN WHERE id=:id");
        $stmt->execute([':id' => $id]);
        return $stmt->rowCount();
    }

    public static function getById(int $id): ?DM_GiangVien_DTO
    {
        $stmt = Database::getConnection()->prepare(self::selectSql() . " WHERE gv.id=:id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ? Database::hydrate($row, 'DM_GiangVien_DTO') : null;
    }

    public static function getPaged(int $page, int $pageSize, string $search = '', int $daXoa = 0, int $loaiGv = 0, int $trangThai = -1): array
    {
        [$page, $pageSize, $offset] = PaginationHelper::normalize($page, $pageSize);
        $where = " WHERE gv.da_xoa=:dx ";
        $params = [':dx' => $daXoa];
        if ($search !== '') {
            $where .= " AND (gv.ma_gv LIKE :s1 OR gv.ho_ten LIKE :s2 OR gv.email LIKE :s3 OR gv.dien_thoai LIKE :s4 OR gv.chuyen_mon LIKE :s5) ";
            $kw = "%{$search}%";
            $params[':s1'] = $kw; $params[':s2'] = $kw; $params[':s3'] = $kw; $params[':s4'] = $kw; $params[':s5'] = $kw;
        }
        if ($loaiGv > 0) { $where .= " AND gv.loai_gv=:lg "; $params[':lg'] = $loaiGv; }
        if ($trangThai >= 0) { $where .= " AND gv.trang_thai=:tt "; $params[':tt'] = $trangThai; }

        $countSql = "SELECT COUNT(*) FROM DM_GIANG_VIEN gv" . $where;
        $stmt = Database::getConnection()->prepare($countSql);
        $stmt->execute($params);
        $total = (int)$stmt->fetchColumn();

        $sql = self::selectSql() . $where . " ORDER BY gv.id DESC LIMIT {$pageSize} OFFSET {$offset}";
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
        $stmt = Database::getConnection()->prepare("SELECT COUNT(*) FROM DM_GIANG_VIEN WHERE ma_gv=:m AND da_xoa=0 AND id<>:id");
        $stmt->execute([':m' => $ma, ':id' => $excludeId]);
        return (int)$stmt->fetchColumn() > 0;
    }

    public static function getStats(): array
    {
        $sql = "SELECT
                  COUNT(*) AS total,
                  SUM(CASE WHEN loai_gv=1 THEN 1 ELSE 0 END) AS co_huu,
                  SUM(CASE WHEN loai_gv=2 THEN 1 ELSE 0 END) AS thinh_giang,
                  SUM(CASE WHEN loai_gv=3 THEN 1 ELSE 0 END) AS khach_moi,
                  SUM(CASE WHEN trang_thai=1 THEN 1 ELSE 0 END) AS hoat_dong
                FROM DM_GIANG_VIEN WHERE da_xoa=0";
        return Database::getConnection()->query($sql)->fetch() ?: ['total'=>0,'co_huu'=>0,'thinh_giang'=>0,'khach_moi'=>0,'hoat_dong'=>0];
    }

    public static function getCombo(int $loaiGv = 0): array
    {
        $sql = "SELECT id, ma_gv, ho_ten, hoc_vi, hoc_ham, loai_gv, avatar
                FROM DM_GIANG_VIEN WHERE da_xoa=0 AND trang_thai=1";
        $params = [];
        if ($loaiGv > 0) { $sql .= " AND loai_gv=:lg"; $params[':lg'] = $loaiGv; }
        $sql .= " ORDER BY ho_ten ASC";
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /** Phân công của 1 giảng viên (cho drawer detail) */
    public static function getPhanCongByGV(int $gvId): array
    {
        $sql = "SELECT pc.id, pc.khoa_hoc_chuong_trinh_id AS lop_hoc_id, pc.mon_hoc_id, pc.vai_tro, pc.so_tiet_phan_cong,
                       pc.tu_ngay, pc.den_ngay, pc.trang_thai,
                       lop.ma_chuong_trinh AS ma_lop, lop.ten_chuong_trinh AS ten_lop,
                       mh.ma_mon_hoc, mh.ten_mon_hoc
                FROM DT_PHAN_CONG_GIANG_VIEN pc
                INNER JOIN DT_KHOA_HOC_CHUONG_TRINH khct ON khct.id = pc.khoa_hoc_chuong_trinh_id
                INNER JOIN DT_CHUONG_TRINH lop ON lop.id = khct.chuong_trinh_id
                LEFT JOIN DT_MON_HOC mh ON mh.id = pc.mon_hoc_id
                WHERE pc.giang_vien_id=:gv AND pc.da_xoa=0
                ORDER BY pc.tu_ngay DESC, pc.id DESC";
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute([':gv' => $gvId]);
        return $stmt->fetchAll();
    }
}
