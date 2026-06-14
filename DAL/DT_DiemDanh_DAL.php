<?php
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/../PUBLIC/Entities/DT_DiemDanh_DTO.php';

class DT_DiemDanh_DAL
{
    private static function selectSql(): string
    {
        return "SELECT dd.*,
                       hvl.hoc_vien_id, hvl.khoa_hoc_chuong_trinh_id AS lop_hoc_id,
                       hv.ma_hv, hv.ho_ten, hv.avatar, hv.la_nhan_vien,
                       nv.ma_nv,
                       dt.ten_doi_tuong,
                       lh.tieu_de AS tieu_de_buoi, lh.ngay_hoc,
                       lh.gio_bat_dau AS gio_bat_dau_buoi, lh.gio_ket_thuc AS gio_ket_thuc_buoi, lh.buoi_thu,
                       lop.ma_chuong_trinh AS ma_lop, lop.ten_chuong_trinh AS ten_lop
                FROM DT_DIEM_DANH dd
                INNER JOIN DT_HOC_VIEN_LOP hvl ON hvl.id = dd.hoc_vien_lop_id
                INNER JOIN DM_HOC_VIEN hv ON hv.id = hvl.hoc_vien_id
                LEFT JOIN DM_NHAN_VIEN nv ON nv.id = hv.nhan_vien_id
                LEFT JOIN DM_DOI_TUONG_HOC_VIEN dt ON dt.id = hv.doi_tuong_id
                LEFT JOIN DT_LICH_HOC lh ON lh.id = dd.lich_hoc_id
                LEFT JOIN DT_KHOA_HOC_CHUONG_TRINH khct ON khct.id = hvl.khoa_hoc_chuong_trinh_id
                LEFT JOIN DT_CHUONG_TRINH lop ON lop.id = khct.chuong_trinh_id";
    }

    /**
     * Khởi tạo (hoặc trả về) danh sách điểm danh cho 1 buổi: mỗi học viên trong lớp 1 dòng.
     * Nếu chưa có → INSERT IGNORE bản ghi mặc định trạng thái=1 (có mặt) cho nhanh.
     */
    public static function ensureForLich(int $lichHocId, int $lopHocId, int $userId): int
    {
        $sql = "INSERT IGNORE INTO DT_DIEM_DANH
                (lich_hoc_id, hoc_vien_lop_id, trang_thai,
                 ngay_tao, ngay_cap_nhat, nguoi_tao, nguoi_cap_nhat, da_xoa)
                SELECT :lich, hvl.id, 1, NOW(), NOW(), :u1, :u2, 0
                FROM DT_HOC_VIEN_LOP hvl
                WHERE hvl.khoa_hoc_chuong_trinh_id=:lop AND hvl.da_xoa=0";
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute([':lich' => $lichHocId, ':lop' => $lopHocId, ':u1' => $userId, ':u2' => $userId]);
        return $stmt->rowCount();
    }

    public static function getByLich(int $lichHocId, string $search = ''): array
    {
        $sql = self::selectSql() . " WHERE dd.lich_hoc_id=:lich AND dd.da_xoa=0";
        $params = [':lich' => $lichHocId];
        if ($search !== '') {
            $sql .= " AND (hv.ma_hv LIKE :s1 OR hv.ho_ten LIKE :s2)";
            $kw = "%{$search}%";
            $params[':s1'] = $kw; $params[':s2'] = $kw;
        }
        $sql .= " ORDER BY hv.ho_ten ASC";
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function getByHocVienLop(int $hvlId): array
    {
        $sql = self::selectSql() . " WHERE dd.hoc_vien_lop_id=:h AND dd.da_xoa=0 ORDER BY lh.ngay_hoc ASC, lh.gio_bat_dau ASC";
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute([':h' => $hvlId]);
        return $stmt->fetchAll();
    }

    public static function updateStatusBulk(int $lichHocId, array $items, int $userId): int
    {
        if (!$items) return 0;
        $sql = "UPDATE DT_DIEM_DANH SET trang_thai=:tt, gio_vao=:gv, gio_ra=:gr, ghi_chu=:gc,
                ngay_cap_nhat=NOW(), nguoi_cap_nhat=:u
                WHERE lich_hoc_id=:lich AND hoc_vien_lop_id=:hvl AND da_xoa=0";
        $stmt = Database::getConnection()->prepare($sql);
        $count = 0;
        foreach ($items as $it) {
            $stmt->execute([
                ':tt' => (int)($it['trang_thai'] ?? 1),
                ':gv' => !empty($it['gio_vao']) ? $it['gio_vao'] : null,
                ':gr' => !empty($it['gio_ra']) ? $it['gio_ra'] : null,
                ':gc' => isset($it['ghi_chu']) && $it['ghi_chu'] !== '' ? $it['ghi_chu'] : null,
                ':u' => $userId,
                ':lich' => $lichHocId,
                ':hvl' => (int)($it['hoc_vien_lop_id'] ?? 0),
            ]);
            $count += $stmt->rowCount();
        }
        return $count;
    }

    public static function markAllPresent(int $lichHocId, int $userId): int
    {
        $stmt = Database::getConnection()->prepare(
            "UPDATE DT_DIEM_DANH SET trang_thai=1, ngay_cap_nhat=NOW(), nguoi_cap_nhat=:u
             WHERE lich_hoc_id=:lich AND da_xoa=0"
        );
        $stmt->execute([':u' => $userId, ':lich' => $lichHocId]);
        return $stmt->rowCount();
    }

    public static function countByLich(int $lichHocId): array
    {
        $stmt = Database::getConnection()->prepare(
            "SELECT
               SUM(CASE WHEN trang_thai=1 THEN 1 ELSE 0 END) AS co_mat,
               SUM(CASE WHEN trang_thai=2 THEN 1 ELSE 0 END) AS muon,
               SUM(CASE WHEN trang_thai=3 THEN 1 ELSE 0 END) AS vang_phep,
               SUM(CASE WHEN trang_thai=0 THEN 1 ELSE 0 END) AS vang,
               COUNT(*) AS total
             FROM DT_DIEM_DANH WHERE lich_hoc_id=:lich AND da_xoa=0"
        );
        $stmt->execute([':lich' => $lichHocId]);
        return $stmt->fetch() ?: ['co_mat'=>0,'muon'=>0,'vang_phep'=>0,'vang'=>0,'total'=>0];
    }

    public static function statsByHvl(int $hvlId): array
    {
        $stmt = Database::getConnection()->prepare(
            "SELECT
               SUM(CASE WHEN trang_thai=1 THEN 1 ELSE 0 END) AS co_mat,
               SUM(CASE WHEN trang_thai=2 THEN 1 ELSE 0 END) AS muon,
               SUM(CASE WHEN trang_thai=3 THEN 1 ELSE 0 END) AS vang_phep,
               SUM(CASE WHEN trang_thai=0 THEN 1 ELSE 0 END) AS vang,
               COUNT(*) AS total
             FROM DT_DIEM_DANH WHERE hoc_vien_lop_id=:h AND da_xoa=0"
        );
        $stmt->execute([':h' => $hvlId]);
        return $stmt->fetch() ?: ['co_mat'=>0,'muon'=>0,'vang_phep'=>0,'vang'=>0,'total'=>0];
    }

    /** Lịch buổi có trong ngày: để user chọn phiên điểm danh từ lớp */
    public static function getLichByLop(int $lopId, string $from = '', string $to = ''): array
    {
        $sql = "SELECT lh.id, lh.buoi_thu, lh.tieu_de, lh.ngay_hoc, lh.gio_bat_dau, lh.gio_ket_thuc, lh.trang_thai,
                       (SELECT COUNT(*) FROM DT_DIEM_DANH dd WHERE dd.lich_hoc_id=lh.id AND dd.da_xoa=0) AS so_diem_danh
                FROM DT_LICH_HOC lh
                WHERE lh.khoa_hoc_chuong_trinh_id=:lop AND lh.da_xoa=0";
        $params = [':lop' => $lopId];
        if ($from !== '') { $sql .= " AND lh.ngay_hoc >= :f"; $params[':f'] = $from; }
        if ($to !== '')   { $sql .= " AND lh.ngay_hoc <= :t"; $params[':t'] = $to; }
        $sql .= " ORDER BY lh.ngay_hoc DESC, lh.gio_bat_dau ASC";
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
