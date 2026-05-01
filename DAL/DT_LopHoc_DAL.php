<?php
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/../PUBLIC/Entities/DT_LopHoc_DTO.php';

class DT_LopHoc_DAL
{
    private static function selectSql(): string
    {
        return "SELECT lh.*,
                       kh.ma_khoa_hoc, kh.ten_khoa_hoc,
                       nv.ma_nv AS ma_giao_vien, nv.ho_ten AS ten_giao_vien,
                       (SELECT COUNT(*) FROM DT_HOC_VIEN_LOP hvl WHERE hvl.lop_hoc_id = lh.id AND hvl.da_xoa = 0) AS so_hoc_vien,
                       u1.tai_khoan AS tai_khoan_nguoi_tao,
                       u2.tai_khoan AS tai_khoan_nguoi_cap_nhat
                FROM DT_LOP_HOC lh
                LEFT JOIN DT_KHOA_HOC kh ON kh.id = lh.khoa_hoc_id
                LEFT JOIN DM_NHAN_VIEN nv ON nv.id = lh.giao_vien_id
                LEFT JOIN DM_NGUOI_DUNG u1 ON u1.id = lh.nguoi_tao
                LEFT JOIN DM_NGUOI_DUNG u2 ON u2.id = lh.nguoi_cap_nhat";
    }

    public static function insert(DT_LopHoc_PUBLIC $e): int
    {
        $sql = "INSERT INTO DT_LOP_HOC
                (ma_lop, ten_lop, khoa_hoc_id, ngay_bat_dau, ngay_ket_thuc,
                 so_luong_toi_da, dia_diem, giao_vien_id, giao_vien_ngoai, mo_ta,
                 trang_thai, ngay_tao, ngay_cap_nhat, nguoi_tao, nguoi_cap_nhat, da_xoa)
                VALUES (:ma, :ten, :kh, :nbd, :nkt, :sl, :dd, :gv, :gvn, :mt,
                        :tt, NOW(), NOW(), :u, :u, 0)";
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute([
            ':ma' => $e->ma_lop, ':ten' => $e->ten_lop, ':kh' => $e->khoa_hoc_id,
            ':nbd' => $e->ngay_bat_dau ?: null, ':nkt' => $e->ngay_ket_thuc ?: null,
            ':sl' => $e->so_luong_toi_da, ':dd' => $e->dia_diem,
            ':gv' => $e->giao_vien_id, ':gvn' => $e->giao_vien_ngoai, ':mt' => $e->mo_ta,
            ':tt' => $e->trang_thai, ':u' => $e->nguoi_tao ?? 0,
        ]);
        return (int)Database::getConnection()->lastInsertId();
    }

    public static function update(DT_LopHoc_PUBLIC $e): int
    {
        $sql = "UPDATE DT_LOP_HOC SET
                ma_lop=:ma, ten_lop=:ten, khoa_hoc_id=:kh,
                ngay_bat_dau=:nbd, ngay_ket_thuc=:nkt,
                so_luong_toi_da=:sl, dia_diem=:dd,
                giao_vien_id=:gv, giao_vien_ngoai=:gvn, mo_ta=:mt,
                trang_thai=:tt, ngay_cap_nhat=NOW(), nguoi_cap_nhat=:u
                WHERE id=:id AND da_xoa=0";
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute([
            ':ma' => $e->ma_lop, ':ten' => $e->ten_lop, ':kh' => $e->khoa_hoc_id,
            ':nbd' => $e->ngay_bat_dau ?: null, ':nkt' => $e->ngay_ket_thuc ?: null,
            ':sl' => $e->so_luong_toi_da, ':dd' => $e->dia_diem,
            ':gv' => $e->giao_vien_id, ':gvn' => $e->giao_vien_ngoai, ':mt' => $e->mo_ta,
            ':tt' => $e->trang_thai,
            ':u' => $e->nguoi_cap_nhat ?? 0, ':id' => $e->id,
        ]);
        return $stmt->rowCount();
    }

    public static function trash(int $id, int $u): int
    {
        $stmt = Database::getConnection()->prepare("UPDATE DT_LOP_HOC SET da_xoa=1, ngay_cap_nhat=NOW(), nguoi_cap_nhat=:u WHERE id=:id");
        $stmt->execute([':u' => $u, ':id' => $id]);
        return $stmt->rowCount();
    }

    public static function restore(int $id, int $u): int
    {
        $stmt = Database::getConnection()->prepare("UPDATE DT_LOP_HOC SET da_xoa=0, ngay_cap_nhat=NOW(), nguoi_cap_nhat=:u WHERE id=:id");
        $stmt->execute([':u' => $u, ':id' => $id]);
        return $stmt->rowCount();
    }

    public static function delete(int $id): int
    {
        $stmt = Database::getConnection()->prepare("DELETE FROM DT_LOP_HOC WHERE id=:id");
        $stmt->execute([':id' => $id]);
        return $stmt->rowCount();
    }

    public static function getById(int $id): ?DT_LopHoc_DTO
    {
        $stmt = Database::getConnection()->prepare(self::selectSql() . " WHERE lh.id=:id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ? Database::hydrate($row, 'DT_LopHoc_DTO') : null;
    }

    public static function getPaged(int $page, int $pageSize, string $search = '', int $daXoa = 0, int $khoaHocId = 0, int $trangThai = -1): array
    {
        [$page, $pageSize, $offset] = PaginationHelper::normalize($page, $pageSize);
        $where = " WHERE lh.da_xoa=:dx ";
        $params = [':dx' => $daXoa];
        if ($search !== '') {
            $where .= " AND (lh.ma_lop LIKE :s OR lh.ten_lop LIKE :s OR lh.dia_diem LIKE :s) ";
            $params[':s'] = "%{$search}%";
        }
        if ($khoaHocId > 0) {
            $where .= " AND lh.khoa_hoc_id=:kh ";
            $params[':kh'] = $khoaHocId;
        }
        if ($trangThai >= 0) {
            $where .= " AND lh.trang_thai=:tt ";
            $params[':tt'] = $trangThai;
        }

        $countSql = "SELECT COUNT(*) FROM DT_LOP_HOC lh" . $where;
        $stmt = Database::getConnection()->prepare($countSql);
        $stmt->execute($params);
        $total = (int)$stmt->fetchColumn();

        $sql = self::selectSql() . $where . " ORDER BY lh.id DESC LIMIT {$pageSize} OFFSET {$offset}";
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
        $stmt = Database::getConnection()->prepare("SELECT COUNT(*) FROM DT_LOP_HOC WHERE ma_lop=:m AND da_xoa=0 AND id<>:id");
        $stmt->execute([':m' => $ma, ':id' => $excludeId]);
        return (int)$stmt->fetchColumn() > 0;
    }

    public static function getStats(): array
    {
        $sql = "SELECT
                  COUNT(*) AS total,
                  SUM(CASE WHEN trang_thai=0 THEN 1 ELSE 0 END) AS cho_mo,
                  SUM(CASE WHEN trang_thai=1 THEN 1 ELSE 0 END) AS dang_hoc,
                  SUM(CASE WHEN trang_thai=2 THEN 1 ELSE 0 END) AS ket_thuc
                FROM DT_LOP_HOC WHERE da_xoa=0";
        return Database::getConnection()->query($sql)->fetch() ?: ['total'=>0,'cho_mo'=>0,'dang_hoc'=>0,'ket_thuc'=>0];
    }

    public static function countHocVienByLop(int $lopId): int
    {
        $stmt = Database::getConnection()->prepare("SELECT COUNT(*) FROM DT_HOC_VIEN_LOP WHERE lop_hoc_id=:id AND da_xoa=0");
        $stmt->execute([':id' => $lopId]);
        return (int)$stmt->fetchColumn();
    }
}
