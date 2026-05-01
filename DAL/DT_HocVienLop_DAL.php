<?php
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/../PUBLIC/Entities/DT_HocVienLop_DTO.php';

class DT_HocVienLop_DAL
{
    private static function selectSql(): string
    {
        return "SELECT hvl.*,
                       hv.ma_hv, hv.ho_ten, hv.gioi_tinh, hv.avatar, hv.la_nhan_vien,
                       hv.don_vi_cong_tac,
                       nv.ma_nv,
                       dt.ten_doi_tuong,
                       lh.ma_lop, lh.ten_lop
                FROM DT_HOC_VIEN_LOP hvl
                INNER JOIN DM_HOC_VIEN hv ON hv.id = hvl.hoc_vien_id
                LEFT JOIN DM_NHAN_VIEN nv ON nv.id = hv.nhan_vien_id
                LEFT JOIN DM_DOI_TUONG_HOC_VIEN dt ON dt.id = hv.doi_tuong_id
                LEFT JOIN DT_LOP_HOC lh ON lh.id = hvl.lop_hoc_id";
    }

    public static function insert(DT_HocVienLop_PUBLIC $e): int
    {
        $sql = "INSERT INTO DT_HOC_VIEN_LOP
                (lop_hoc_id, hoc_vien_id, ngay_ghi_danh, trang_thai, diem_tong_ket, xep_loai, ghi_chu,
                 ngay_tao, ngay_cap_nhat, nguoi_tao, nguoi_cap_nhat, da_xoa)
                VALUES (:lop, :hv, :ngd, :tt, :d, :xl, :gc, NOW(), NOW(), :u1, :u2, 0)";
        $stmt = Database::getConnection()->prepare($sql);
        $u = $e->nguoi_tao ?? 0;
        $stmt->execute([
            ':lop' => $e->lop_hoc_id, ':hv' => $e->hoc_vien_id,
            ':ngd' => $e->ngay_ghi_danh ?: null, ':tt' => $e->trang_thai,
            ':d' => $e->diem_tong_ket, ':xl' => $e->xep_loai, ':gc' => $e->ghi_chu,
            ':u1' => $u, ':u2' => $u,
        ]);
        return (int)Database::getConnection()->lastInsertId();
    }

    public static function update(DT_HocVienLop_PUBLIC $e): int
    {
        $sql = "UPDATE DT_HOC_VIEN_LOP SET
                ngay_ghi_danh=:ngd, trang_thai=:tt,
                diem_tong_ket=:d, xep_loai=:xl, ghi_chu=:gc,
                ngay_cap_nhat=NOW(), nguoi_cap_nhat=:u
                WHERE id=:id AND da_xoa=0";
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute([
            ':ngd' => $e->ngay_ghi_danh ?: null, ':tt' => $e->trang_thai,
            ':d' => $e->diem_tong_ket, ':xl' => $e->xep_loai, ':gc' => $e->ghi_chu,
            ':u' => $e->nguoi_cap_nhat ?? 0, ':id' => $e->id,
        ]);
        return $stmt->rowCount();
    }

    public static function delete(int $id): int
    {
        $stmt = Database::getConnection()->prepare("DELETE FROM DT_HOC_VIEN_LOP WHERE id=:id");
        $stmt->execute([':id' => $id]);
        return $stmt->rowCount();
    }

    public static function getById(int $id): ?DT_HocVienLop_DTO
    {
        $stmt = Database::getConnection()->prepare(self::selectSql() . " WHERE hvl.id=:id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ? Database::hydrate($row, 'DT_HocVienLop_DTO') : null;
    }

    /** Lấy danh sách lớp mà 1 học viên đang ghi danh (chiều ngược, dùng cho UI Học viên). */
    public static function getByHocVien(int $hocVienId): array
    {
        $sql = "SELECT hvl.id, hvl.lop_hoc_id, hvl.ngay_ghi_danh, hvl.trang_thai,
                       hvl.diem_tong_ket, hvl.xep_loai,
                       lh.ma_lop, lh.ten_lop, lh.ngay_bat_dau, lh.ngay_ket_thuc,
                       lh.so_luong_toi_da, lh.trang_thai AS lop_trang_thai,
                       kh.ma_khoa_hoc, kh.ten_khoa_hoc
                FROM DT_HOC_VIEN_LOP hvl
                INNER JOIN DT_LOP_HOC lh ON lh.id = hvl.lop_hoc_id AND lh.da_xoa=0
                LEFT JOIN DT_KHOA_HOC kh ON kh.id = lh.khoa_hoc_id
                WHERE hvl.hoc_vien_id=:hv AND hvl.da_xoa=0
                ORDER BY hvl.id DESC";
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute([':hv' => $hocVienId]);
        return $stmt->fetchAll();
    }

    public static function getByLop(int $lopId, string $search = ''): array
    {
        $sql = self::selectSql() . " WHERE hvl.lop_hoc_id=:lop AND hvl.da_xoa=0";
        $params = [':lop' => $lopId];
        if ($search !== '') {
            $sql .= " AND (hv.ma_hv LIKE :s OR hv.ho_ten LIKE :s OR nv.ma_nv LIKE :s)";
            $params[':s'] = "%{$search}%";
        }
        $sql .= " ORDER BY hvl.id ASC";
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function checkExists(int $lopId, int $hocVienId): bool
    {
        $stmt = Database::getConnection()->prepare("SELECT COUNT(*) FROM DT_HOC_VIEN_LOP WHERE lop_hoc_id=:lop AND hoc_vien_id=:hv AND da_xoa=0");
        $stmt->execute([':lop' => $lopId, ':hv' => $hocVienId]);
        return (int)$stmt->fetchColumn() > 0;
    }

    public static function getCountByLop(int $lopId): int
    {
        $stmt = Database::getConnection()->prepare("SELECT COUNT(*) FROM DT_HOC_VIEN_LOP WHERE lop_hoc_id=:id AND da_xoa=0");
        $stmt->execute([':id' => $lopId]);
        return (int)$stmt->fetchColumn();
    }

    /** Học viên CHƯA ghi danh vào lớp này (để hiển thị trong picker) */
    public static function getHocVienChuaGhiDanh(int $lopId, string $search = '', int $limit = 50): array
    {
        $sql = "SELECT hv.id, hv.ma_hv, hv.ho_ten, hv.avatar, hv.la_nhan_vien,
                       nv.ma_nv, dt.ten_doi_tuong
                FROM DM_HOC_VIEN hv
                LEFT JOIN DM_NHAN_VIEN nv ON nv.id = hv.nhan_vien_id
                LEFT JOIN DM_DOI_TUONG_HOC_VIEN dt ON dt.id = hv.doi_tuong_id
                WHERE hv.da_xoa=0 AND hv.trang_thai=1
                  AND hv.id NOT IN (SELECT hoc_vien_id FROM DT_HOC_VIEN_LOP WHERE lop_hoc_id=:lop AND da_xoa=0)";
        $params = [':lop' => $lopId];
        if ($search !== '') {
            $sql .= " AND (hv.ma_hv LIKE :s OR hv.ho_ten LIKE :s OR nv.ma_nv LIKE :s OR hv.don_vi_cong_tac LIKE :s)";
            $params[':s'] = "%{$search}%";
        }
        $sql .= " ORDER BY hv.ho_ten LIMIT " . (int)$limit;
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function bulkInsert(int $lopId, array $hocVienIds, int $userId): int
    {
        if (!$hocVienIds) return 0;
        
        $hocVienIds = array_values(array_unique(array_filter(array_map('intval', $hocVienIds))));
        if (!$hocVienIds) return 0;
        
        // Xây dựng placeholders
        $placeholders = [];
        $values = [];
        foreach ($hocVienIds as $hvId) {
            $placeholders[] = "(?, ?, CURDATE(), 1, NOW(), NOW(), ?, ?, 0)";
            $values[] = $lopId;
            $values[] = $hvId;
            $values[] = $userId;
            $values[] = $userId;
        }
        
        $sql = "INSERT IGNORE INTO DT_HOC_VIEN_LOP
                (lop_hoc_id, hoc_vien_id, ngay_ghi_danh, trang_thai, ngay_tao, ngay_cap_nhat, nguoi_tao, nguoi_cap_nhat, da_xoa)
                VALUES " . implode(", ", $placeholders);
        
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute($values);
        return $stmt->rowCount();
    }
}
