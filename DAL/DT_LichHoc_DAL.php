<?php
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/../PUBLIC/Entities/DT_LichHoc_DTO.php';

class DT_LichHoc_DAL
{
    private static function selectSql(): string
    {
        return "SELECT lh.*,
                       lh.khoa_hoc_chuong_trinh_id AS lop_hoc_id,
                       ct.ma_chuong_trinh AS ma_lop, ct.ten_chuong_trinh AS ten_lop,
                       khct.khoa_hoc_id, kh.ma_khoa_hoc, kh.ten_khoa_hoc,
                       mh.ma_mon_hoc, mh.ten_mon_hoc,
                       nv.ma_nv AS ma_giang_vien, nv.ho_ten AS ten_giang_vien,
                       u.tai_khoan AS tai_khoan_nguoi_tao
                FROM DT_LICH_HOC lh
                LEFT JOIN DT_KHOA_HOC_CHUONG_TRINH khct ON khct.id = lh.khoa_hoc_chuong_trinh_id
                LEFT JOIN DT_CHUONG_TRINH ct ON ct.id = khct.chuong_trinh_id
                LEFT JOIN DT_KHOA_HOC kh ON kh.id = khct.khoa_hoc_id
                LEFT JOIN DT_MON_HOC mh  ON mh.id  = lh.mon_hoc_id
                LEFT JOIN DM_NHAN_VIEN nv ON nv.id = lh.giang_vien_id
                LEFT JOIN DM_NGUOI_DUNG u ON u.id  = lh.nguoi_tao";
    }

    public static function insert(DT_LichHoc_PUBLIC $e): int
    {
        $u = $e->nguoi_tao ?? 0;
        $sql = "INSERT INTO DT_LICH_HOC
                (khoa_hoc_chuong_trinh_id, buoi_thu, tieu_de, noi_dung, mon_hoc_id,
                 ngay_hoc, gio_bat_dau, gio_ket_thuc,
                 phong_hoc, giang_vien_id, giang_vien_ngoai,
                 trang_thai, ghi_chu,
                 ngay_tao, ngay_cap_nhat, nguoi_tao, nguoi_cap_nhat, da_xoa)
                VALUES (:lop, :buoi, :tieu_de, :noi_dung, :mon,
                        :ngay, :gbd, :gkt,
                        :phong, :gv, :gvn,
                        :tt, :gc,
                        NOW(), NOW(), :u1, :u2, 0)";
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute([
            ':lop' => $e->lop_hoc_id, ':buoi' => $e->buoi_thu,
            ':tieu_de' => $e->tieu_de, ':noi_dung' => $e->noi_dung,
            ':mon' => $e->mon_hoc_id,
            ':ngay' => $e->ngay_hoc, ':gbd' => $e->gio_bat_dau, ':gkt' => $e->gio_ket_thuc,
            ':phong' => $e->phong_hoc,
            ':gv' => $e->giang_vien_id, ':gvn' => $e->giang_vien_ngoai,
            ':tt' => $e->trang_thai, ':gc' => $e->ghi_chu,
            ':u1' => $u, ':u2' => $u,
        ]);
        return (int)Database::getConnection()->lastInsertId();
    }

    public static function update(DT_LichHoc_PUBLIC $e): int
    {
        $sql = "UPDATE DT_LICH_HOC SET
                khoa_hoc_chuong_trinh_id=:lop, buoi_thu=:buoi, tieu_de=:tieu_de, noi_dung=:noi_dung,
                mon_hoc_id=:mon,
                ngay_hoc=:ngay, gio_bat_dau=:gbd, gio_ket_thuc=:gkt,
                phong_hoc=:phong, giang_vien_id=:gv, giang_vien_ngoai=:gvn,
                trang_thai=:tt, ghi_chu=:gc,
                ngay_cap_nhat=NOW(), nguoi_cap_nhat=:u
                WHERE id=:id AND da_xoa=0";
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute([
            ':lop' => $e->lop_hoc_id, ':buoi' => $e->buoi_thu,
            ':tieu_de' => $e->tieu_de, ':noi_dung' => $e->noi_dung,
            ':mon' => $e->mon_hoc_id,
            ':ngay' => $e->ngay_hoc, ':gbd' => $e->gio_bat_dau, ':gkt' => $e->gio_ket_thuc,
            ':phong' => $e->phong_hoc, ':gv' => $e->giang_vien_id, ':gvn' => $e->giang_vien_ngoai,
            ':tt' => $e->trang_thai, ':gc' => $e->ghi_chu,
            ':u' => $e->nguoi_cap_nhat ?? 0, ':id' => $e->id,
        ]);
        return $stmt->rowCount();
    }

    public static function updateTrangThai(int $id, int $tt, int $u): int
    {
        $stmt = Database::getConnection()->prepare(
            "UPDATE DT_LICH_HOC SET trang_thai=:tt, ngay_cap_nhat=NOW(), nguoi_cap_nhat=:u WHERE id=:id AND da_xoa=0"
        );
        $stmt->execute([':tt' => $tt, ':u' => $u, ':id' => $id]);
        return $stmt->rowCount();
    }

    public static function trash(int $id, int $u): int
    {
        $stmt = Database::getConnection()->prepare("UPDATE DT_LICH_HOC SET da_xoa=1, ngay_cap_nhat=NOW(), nguoi_cap_nhat=:u WHERE id=:id");
        $stmt->execute([':u' => $u, ':id' => $id]);
        return $stmt->rowCount();
    }

    public static function restore(int $id, int $u): int
    {
        $stmt = Database::getConnection()->prepare("UPDATE DT_LICH_HOC SET da_xoa=0, ngay_cap_nhat=NOW(), nguoi_cap_nhat=:u WHERE id=:id");
        $stmt->execute([':u' => $u, ':id' => $id]);
        return $stmt->rowCount();
    }

    public static function delete(int $id): int
    {
        $stmt = Database::getConnection()->prepare("DELETE FROM DT_LICH_HOC WHERE id=:id");
        $stmt->execute([':id' => $id]);
        return $stmt->rowCount();
    }

    public static function getById(int $id): ?DT_LichHoc_DTO
    {
        $stmt = Database::getConnection()->prepare(self::selectSql() . " WHERE lh.id=:id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ? Database::hydrate($row, 'DT_LichHoc_DTO') : null;
    }

    /**
     * Lấy lịch theo khoảng ngày (cho calendar view). Lọc tùy chọn theo lớp / giảng viên / trạng thái.
     */
    public static function getByRange(string $from, string $to, array $opts = []): array
    {
        $where = " WHERE lh.da_xoa=0 AND lh.ngay_hoc BETWEEN :f AND :t ";
        $params = [':f' => $from, ':t' => $to];
        if (!empty($opts['lop_hoc_id'])) { $where .= " AND lh.khoa_hoc_chuong_trinh_id=:lop "; $params[':lop'] = (int)$opts['lop_hoc_id']; }
        if (!empty($opts['giang_vien_id'])) { $where .= " AND lh.giang_vien_id=:gv "; $params[':gv'] = (int)$opts['giang_vien_id']; }
        if (isset($opts['trang_thai']) && $opts['trang_thai'] !== '' && (int)$opts['trang_thai'] >= 0) {
            $where .= " AND lh.trang_thai=:tt ";
            $params[':tt'] = (int)$opts['trang_thai'];
        }
        if (!empty($opts['search'])) {
            $where .= " AND (lh.tieu_de LIKE :s1 OR lh.phong_hoc LIKE :s2 OR ct.ten_chuong_trinh LIKE :s3 OR ct.ma_chuong_trinh LIKE :s4) ";
            $kw = '%' . $opts['search'] . '%';
            $params[':s1'] = $kw; $params[':s2'] = $kw; $params[':s3'] = $kw; $params[':s4'] = $kw;
        }
        $sql = self::selectSql() . $where . " ORDER BY lh.ngay_hoc ASC, lh.gio_bat_dau ASC";
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Paged list view.
     */
    public static function getPaged(int $page, int $pageSize, array $opts = [], int $daXoa = 0): array
    {
        [$page, $pageSize, $offset] = PaginationHelper::normalize($page, $pageSize);
        $where = " WHERE lh.da_xoa=:dx ";
        $params = [':dx' => $daXoa];
        if (!empty($opts['from'])) { $where .= " AND lh.ngay_hoc >= :f "; $params[':f'] = $opts['from']; }
        if (!empty($opts['to']))   { $where .= " AND lh.ngay_hoc <= :t "; $params[':t'] = $opts['to']; }
        if (!empty($opts['lop_hoc_id'])) { $where .= " AND lh.khoa_hoc_chuong_trinh_id=:lop "; $params[':lop'] = (int)$opts['lop_hoc_id']; }
        if (!empty($opts['giang_vien_id'])) { $where .= " AND lh.giang_vien_id=:gv "; $params[':gv'] = (int)$opts['giang_vien_id']; }
        if (isset($opts['trang_thai']) && $opts['trang_thai'] !== '' && (int)$opts['trang_thai'] >= 0) {
            $where .= " AND lh.trang_thai=:tt ";
            $params[':tt'] = (int)$opts['trang_thai'];
        }
        if (!empty($opts['search'])) {
            $where .= " AND (lh.tieu_de LIKE :s1 OR lh.phong_hoc LIKE :s2 OR ct.ten_chuong_trinh LIKE :s3 OR ct.ma_chuong_trinh LIKE :s4) ";
            $kw = '%' . $opts['search'] . '%';
            $params[':s1'] = $kw; $params[':s2'] = $kw; $params[':s3'] = $kw; $params[':s4'] = $kw;
        }

        $countSql = "SELECT COUNT(*) FROM DT_LICH_HOC lh
                     LEFT JOIN DT_KHOA_HOC_CHUONG_TRINH khct ON khct.id = lh.khoa_hoc_chuong_trinh_id
                     LEFT JOIN DT_CHUONG_TRINH ct ON ct.id = khct.chuong_trinh_id" . $where;
        $stmt = Database::getConnection()->prepare($countSql);
        $stmt->execute($params);
        $total = (int)$stmt->fetchColumn();

        $sql = self::selectSql() . $where . " ORDER BY lh.ngay_hoc DESC, lh.gio_bat_dau ASC LIMIT {$pageSize} OFFSET {$offset}";
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute($params);
        return [
            'data' => $stmt->fetchAll(),
            'totalRecords' => $total,
            'totalPages' => PaginationHelper::totalPages($total, $pageSize),
        ];
    }

    public static function getStats(string $from, string $to): array
    {
        $sql = "SELECT
                  COUNT(*) AS total,
                  SUM(CASE WHEN trang_thai=0 THEN 1 ELSE 0 END) AS ke_hoach,
                  SUM(CASE WHEN trang_thai=1 THEN 1 ELSE 0 END) AS da_day,
                  SUM(CASE WHEN trang_thai=2 THEN 1 ELSE 0 END) AS hoan,
                  SUM(CASE WHEN trang_thai=3 THEN 1 ELSE 0 END) AS huy,
                  SUM(CASE WHEN ngay_hoc BETWEEN :f AND :t THEN 1 ELSE 0 END) AS trong_ky
                FROM DT_LICH_HOC WHERE da_xoa=0";
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute([':f' => $from, ':t' => $to]);
        $row = $stmt->fetch();
        return $row ?: ['total'=>0,'ke_hoach'=>0,'da_day'=>0,'hoan'=>0,'huy'=>0,'trong_ky'=>0];
    }

    /**
     * Kiểm tra trùng lịch: cùng phòng hoặc cùng giảng viên trong cùng khung giờ.
     * Trả về danh sách DTO buổi bị trùng (để BUS cảnh báo).
     */
    public static function findConflicts(string $ngay, string $gbd, string $gkt, ?string $phong, ?int $gvId, ?int $excludeId = null): array
    {
        // Kiểm tra: chỉ xử lý nếu có phòng hoặc giáo viên
        $hasPhong = ($phong !== null && $phong !== '');
        $hasGv = $gvId;
        
        if (!$hasPhong && !$hasGv) return [];
        
        $sql = self::selectSql() . " WHERE lh.da_xoa=0
                AND lh.ngay_hoc=:ngay
                AND lh.trang_thai IN (0,1)
                AND NOT (lh.gio_ket_thuc <= :gbd OR lh.gio_bat_dau >= :gkt)
                AND (";
        
        $params = [
            ':ngay' => $ngay, ':gbd' => $gbd, ':gkt' => $gkt,
        ];
        
        if ($hasPhong && $hasGv) {
            $sql .= "lh.phong_hoc = :phong OR lh.giang_vien_id = :gv";
            $params[':phong'] = $phong;
            $params[':gv'] = $gvId;
        } elseif ($hasPhong) {
            $sql .= "lh.phong_hoc = :phong";
            $params[':phong'] = $phong;
        } else {
            $sql .= "lh.giang_vien_id = :gv";
            $params[':gv'] = $gvId;
        }
        
        $sql .= ")";
        
        if ($excludeId) {
            $sql .= " AND lh.id <> :ex ";
            $params[':ex'] = $excludeId;
        }
        $sql .= " ORDER BY lh.gio_bat_dau ASC";
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /** Thông tin CTĐT (tên) theo dòng bridge khct.id — dùng cho tiêu đề buổi học mặc định. */
    public static function getChuongTrinhTenByKhct(int $khctId): ?string
    {
        $stmt = Database::getConnection()->prepare(
            "SELECT ct.ten_chuong_trinh
             FROM DT_KHOA_HOC_CHUONG_TRINH khct
             JOIN DT_CHUONG_TRINH ct ON ct.id = khct.chuong_trinh_id
             WHERE khct.id=:id AND khct.da_xoa=0"
        );
        $stmt->execute([':id' => $khctId]);
        $v = $stmt->fetchColumn();
        return $v === false ? null : (string)$v;
    }

    public static function getMaxBuoi(int $lopId): int
    {
        $stmt = Database::getConnection()->prepare("SELECT COALESCE(MAX(buoi_thu),0) FROM DT_LICH_HOC WHERE khoa_hoc_chuong_trinh_id=:id AND da_xoa=0");
        $stmt->execute([':id' => $lopId]);
        return (int)$stmt->fetchColumn();
    }
}
