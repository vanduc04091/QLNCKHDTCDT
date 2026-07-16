<?php
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/../PUBLIC/Entities/DT_CmeLoai_DTO.php';

class DT_CmeLoai_DAL
{
    private static function selectSql(): string
    {
        return "SELECT l.*,
                       n.ma_nhom, n.ten_nhom,
                       kp.ten_khoa AS ten_khoa_phong,
                       u1.tai_khoan AS tai_khoan_nguoi_tao,
                       u2.tai_khoan AS tai_khoan_nguoi_cap_nhat
                FROM DT_CME_LOAI l
                LEFT JOIN DT_CME_NHOM n ON n.id = l.nhom_id
                LEFT JOIN DM_KHOA_PHONG kp ON kp.id = l.khoa_phong_id
                LEFT JOIN DM_NGUOI_DUNG u1 ON u1.id = l.nguoi_tao
                LEFT JOIN DM_NGUOI_DUNG u2 ON u2.id = l.nguoi_cap_nhat";
    }

    public static function insert(DT_CmeLoai_PUBLIC $e): int
    {
        $sql = "INSERT INTO DT_CME_LOAI (nhom_id, ma_loai, ten_loai, kieu_quy_doi, gia_tri_quy_doi,
                                         don_vi_tinh, khoa_phong_id, thu_tu,
                                         ngay_tao, ngay_cap_nhat, nguoi_tao, nguoi_cap_nhat, da_xoa)
                VALUES (:nhom, :ma, :ten, :kieu, :gt, :dv, :kp, :tt, NOW(), NOW(), :u1, :u2, 0)";
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute([
            ':nhom' => $e->nhom_id, ':ma' => $e->ma_loai, ':ten' => $e->ten_loai,
            ':kieu' => $e->kieu_quy_doi, ':gt' => $e->gia_tri_quy_doi,
            ':dv' => $e->don_vi_tinh, ':kp' => $e->khoa_phong_id ?: null, ':tt' => $e->thu_tu,
            ':u1' => $e->nguoi_tao ?? 0, ':u2' => $e->nguoi_tao ?? 0,
        ]);
        return (int)Database::getConnection()->lastInsertId();
    }

    public static function update(DT_CmeLoai_PUBLIC $e): int
    {
        $sql = "UPDATE DT_CME_LOAI SET nhom_id=:nhom, ma_loai=:ma, ten_loai=:ten,
                       kieu_quy_doi=:kieu, gia_tri_quy_doi=:gt, don_vi_tinh=:dv,
                       khoa_phong_id=:kp, thu_tu=:tt, ngay_cap_nhat=NOW(), nguoi_cap_nhat=:u
                WHERE id=:id AND da_xoa=0";
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute([
            ':nhom' => $e->nhom_id, ':ma' => $e->ma_loai, ':ten' => $e->ten_loai,
            ':kieu' => $e->kieu_quy_doi, ':gt' => $e->gia_tri_quy_doi, ':dv' => $e->don_vi_tinh,
            ':kp' => $e->khoa_phong_id ?: null, ':tt' => $e->thu_tu,
            ':u' => $e->nguoi_cap_nhat ?? 0, ':id' => $e->id,
        ]);
        return $stmt->rowCount();
    }

    public static function trash(int $id, int $u): int
    {
        $stmt = Database::getConnection()->prepare("UPDATE DT_CME_LOAI SET da_xoa=1, ngay_cap_nhat=NOW(), nguoi_cap_nhat=:u WHERE id=:id");
        $stmt->execute([':u' => $u, ':id' => $id]);
        return $stmt->rowCount();
    }

    public static function restore(int $id, int $u): int
    {
        $stmt = Database::getConnection()->prepare("UPDATE DT_CME_LOAI SET da_xoa=0, ngay_cap_nhat=NOW(), nguoi_cap_nhat=:u WHERE id=:id");
        $stmt->execute([':u' => $u, ':id' => $id]);
        return $stmt->rowCount();
    }

    public static function delete(int $id): int
    {
        $stmt = Database::getConnection()->prepare("DELETE FROM DT_CME_LOAI WHERE id=:id");
        $stmt->execute([':id' => $id]);
        return $stmt->rowCount();
    }

    public static function getById(int $id): ?DT_CmeLoai_DTO
    {
        $stmt = Database::getConnection()->prepare(self::selectSql() . " WHERE l.id=:id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ? Database::hydrate($row, 'DT_CmeLoai_DTO') : null;
    }

    public static function getPaged(int $page, int $pageSize, string $search = '', int $daXoa = 0, int $nhomId = 0): array
    {
        [$page, $pageSize, $offset] = PaginationHelper::normalize($page, $pageSize);
        $where = " WHERE l.da_xoa=:dx ";
        $params = [':dx' => $daXoa];
        if ($search !== '') {
            $where .= " AND (l.ma_loai LIKE :s1 OR l.ten_loai LIKE :s2) ";
            $params[':s1'] = "%{$search}%"; $params[':s2'] = "%{$search}%";
        }
        if ($nhomId > 0) {
            $where .= " AND l.nhom_id=:nhom ";
            $params[':nhom'] = $nhomId;
        }
        $countSql = "SELECT COUNT(*) FROM DT_CME_LOAI l" . $where;
        $stmt = Database::getConnection()->prepare($countSql);
        $stmt->execute($params);
        $total = (int)$stmt->fetchColumn();

        $sql = self::selectSql() . $where . " ORDER BY l.nhom_id ASC, l.thu_tu ASC, l.id ASC LIMIT {$pageSize} OFFSET {$offset}";
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
        $stmt = Database::getConnection()->prepare("SELECT COUNT(*) FROM DT_CME_LOAI WHERE ma_loai=:m AND da_xoa=0 AND id<>:id");
        $stmt->execute([':m' => $ma, ':id' => $excludeId]);
        return (int)$stmt->fetchColumn() > 0;
    }

    /** Combo loại (kèm nhóm + thông tin quy đổi) cho màn ghi nhận. */
    public static function getCombo(int $nhomId = 0): array
    {
        $sql = "SELECT l.id, l.nhom_id, l.ma_loai, l.ten_loai, l.kieu_quy_doi, l.gia_tri_quy_doi, l.don_vi_tinh,
                       n.ten_nhom
                FROM DT_CME_LOAI l
                LEFT JOIN DT_CME_NHOM n ON n.id = l.nhom_id
                WHERE l.da_xoa=0";
        $params = [];
        if ($nhomId > 0) { $sql .= " AND l.nhom_id=:nhom"; $params[':nhom'] = $nhomId; }
        $sql .= " ORDER BY l.nhom_id ASC, l.thu_tu ASC, l.id ASC";
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll() ?: [];
    }
}
