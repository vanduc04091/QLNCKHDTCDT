<?php
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/../PUBLIC/Entities/DT_PhanCongGiangVien_DTO.php';

class DT_PhanCongGiangVien_DAL
{
    private static function selectSql(): string
    {
        return "SELECT pc.*,
                       pc.khoa_hoc_chuong_trinh_id AS lop_hoc_id,
                       gv.ma_gv, gv.ho_ten AS ho_ten_gv, gv.avatar AS avatar_gv,
                       gv.hoc_vi, gv.hoc_ham, gv.loai_gv,
                       lop.ma_chuong_trinh AS ma_lop, lop.ten_chuong_trinh AS ten_lop,
                       kh.ma_khoa_hoc, kh.ten_khoa_hoc,
                       mh.ma_mon_hoc, mh.ten_mon_hoc
                FROM DT_PHAN_CONG_GIANG_VIEN pc
                INNER JOIN DM_GIANG_VIEN gv ON gv.id = pc.giang_vien_id
                INNER JOIN DT_KHOA_HOC_CHUONG_TRINH khct ON khct.id = pc.khoa_hoc_chuong_trinh_id
                INNER JOIN DT_CHUONG_TRINH lop ON lop.id = khct.chuong_trinh_id
                LEFT JOIN DT_KHOA_HOC kh ON kh.id = khct.khoa_hoc_id
                LEFT JOIN DT_MON_HOC mh ON mh.id = pc.mon_hoc_id";
    }

    public static function insert(DT_PhanCongGiangVien_PUBLIC $e): int
    {
        $u = $e->nguoi_tao ?? 0;
        $sql = "INSERT INTO DT_PHAN_CONG_GIANG_VIEN
                (giang_vien_id, khoa_hoc_chuong_trinh_id, mon_hoc_id, vai_tro, so_tiet_phan_cong,
                 tu_ngay, den_ngay, trang_thai, ghi_chu,
                 ngay_tao, ngay_cap_nhat, nguoi_tao, nguoi_cap_nhat, da_xoa)
                VALUES (:gv, :lop, :mon, :vt, :st, :tn, :dn, :tt, :gc,
                        NOW(), NOW(), :u1, :u2, 0)";
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute([
            ':gv' => $e->giang_vien_id, ':lop' => $e->lop_hoc_id,
            ':mon' => $e->mon_hoc_id, ':vt' => $e->vai_tro, ':st' => $e->so_tiet_phan_cong,
            ':tn' => $e->tu_ngay ?: null, ':dn' => $e->den_ngay ?: null,
            ':tt' => $e->trang_thai, ':gc' => $e->ghi_chu,
            ':u1' => $u, ':u2' => $u,
        ]);
        return (int)Database::getConnection()->lastInsertId();
    }

    public static function update(DT_PhanCongGiangVien_PUBLIC $e): int
    {
        $sql = "UPDATE DT_PHAN_CONG_GIANG_VIEN SET
                giang_vien_id=:gv, khoa_hoc_chuong_trinh_id=:lop, mon_hoc_id=:mon,
                vai_tro=:vt, so_tiet_phan_cong=:st,
                tu_ngay=:tn, den_ngay=:dn,
                trang_thai=:tt, ghi_chu=:gc,
                ngay_cap_nhat=NOW(), nguoi_cap_nhat=:u
                WHERE id=:id AND da_xoa=0";
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute([
            ':gv' => $e->giang_vien_id, ':lop' => $e->lop_hoc_id,
            ':mon' => $e->mon_hoc_id, ':vt' => $e->vai_tro, ':st' => $e->so_tiet_phan_cong,
            ':tn' => $e->tu_ngay ?: null, ':dn' => $e->den_ngay ?: null,
            ':tt' => $e->trang_thai, ':gc' => $e->ghi_chu,
            ':u' => $e->nguoi_cap_nhat ?? 0, ':id' => $e->id,
        ]);
        return $stmt->rowCount();
    }

    public static function delete(int $id): int
    {
        $stmt = Database::getConnection()->prepare("UPDATE DT_PHAN_CONG_GIANG_VIEN SET da_xoa=1 WHERE id=:id");
        $stmt->execute([':id' => $id]);
        return $stmt->rowCount();
    }

    public static function getById(int $id): ?DT_PhanCongGiangVien_DTO
    {
        $stmt = Database::getConnection()->prepare(self::selectSql() . " WHERE pc.id=:id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ? Database::hydrate($row, 'DT_PhanCongGiangVien_DTO') : null;
    }

    /** Lấy danh sách phân công với filter linh hoạt theo lớp / GV */
    public static function getList(array $opts = []): array
    {
        $where = " WHERE pc.da_xoa=0";
        $params = [];
        if (!empty($opts['lop_hoc_id'])) { $where .= " AND pc.khoa_hoc_chuong_trinh_id=:lop"; $params[':lop'] = (int)$opts['lop_hoc_id']; }
        if (!empty($opts['giang_vien_id'])) { $where .= " AND pc.giang_vien_id=:gv"; $params[':gv'] = (int)$opts['giang_vien_id']; }
        if (!empty($opts['mon_hoc_id'])) { $where .= " AND pc.mon_hoc_id=:mon"; $params[':mon'] = (int)$opts['mon_hoc_id']; }
        if (isset($opts['vai_tro']) && $opts['vai_tro'] !== '' && (int)$opts['vai_tro'] > 0) {
            $where .= " AND pc.vai_tro=:vt";
            $params[':vt'] = (int)$opts['vai_tro'];
        }
        if (isset($opts['trang_thai']) && $opts['trang_thai'] !== '' && (int)$opts['trang_thai'] >= 0) {
            $where .= " AND pc.trang_thai=:tt";
            $params[':tt'] = (int)$opts['trang_thai'];
        }
        if (!empty($opts['search'])) {
            $where .= " AND (gv.ma_gv LIKE :s1 OR gv.ho_ten LIKE :s2 OR lop.ma_chuong_trinh LIKE :s3 OR lop.ten_chuong_trinh LIKE :s4)";
            $kw = '%' . $opts['search'] . '%';
            $params[':s1'] = $kw; $params[':s2'] = $kw; $params[':s3'] = $kw; $params[':s4'] = $kw;
        }
        $sql = self::selectSql() . $where . " ORDER BY khct.ngay_bat_dau DESC, lop.id DESC, mh.ten_mon_hoc ASC, pc.vai_tro ASC";
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /** Conflict: cùng lớp + môn + vai trò chính → đã có ai đó chưa */
    public static function findConflicts(int $gvId, int $lopId, ?int $monId, int $vaiTro, ?int $excludeId = null): array
    {
        $sql = self::selectSql() . " WHERE pc.da_xoa=0
                AND pc.khoa_hoc_chuong_trinh_id=:lop
                AND (pc.mon_hoc_id <=> :mon)
                AND pc.vai_tro=:vt
                AND pc.giang_vien_id<>:gv";
        $params = [':lop' => $lopId, ':mon' => $monId, ':vt' => $vaiTro, ':gv' => $gvId];
        if ($excludeId) { $sql .= " AND pc.id<>:ex"; $params[':ex'] = $excludeId; }
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /** Đếm phân công của 1 lớp theo môn (để hiển thị status trong list) */
    public static function countByLopMon(int $lopId): array
    {
        $sql = "SELECT mon_hoc_id, vai_tro, COUNT(*) AS cnt
                FROM DT_PHAN_CONG_GIANG_VIEN
                WHERE khoa_hoc_chuong_trinh_id=:lop AND da_xoa=0
                GROUP BY mon_hoc_id, vai_tro";
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute([':lop' => $lopId]);
        return $stmt->fetchAll();
    }

    public static function getStats(): array
    {
        $sql = "SELECT
                  COUNT(*) AS total,
                  SUM(CASE WHEN trang_thai=0 THEN 1 ELSE 0 END) AS du_kien,
                  SUM(CASE WHEN trang_thai=1 THEN 1 ELSE 0 END) AS dang_day,
                  SUM(CASE WHEN trang_thai=2 THEN 1 ELSE 0 END) AS hoan_thanh,
                  SUM(CASE WHEN trang_thai=3 THEN 1 ELSE 0 END) AS huy,
                  COUNT(DISTINCT giang_vien_id) AS so_gv,
                  COUNT(DISTINCT khoa_hoc_chuong_trinh_id) AS so_lop
                FROM DT_PHAN_CONG_GIANG_VIEN WHERE da_xoa=0";
        return Database::getConnection()->query($sql)->fetch() ?: [];
    }
}
