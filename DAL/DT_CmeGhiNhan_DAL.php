<?php
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/../PUBLIC/Entities/DT_CmeGhiNhan_DTO.php';

class DT_CmeGhiNhan_DAL
{
    private static function selectSql(): string
    {
        return "SELECT g.*,
                       nv.ma_nv, nv.ho_ten AS ho_ten_nhan_vien,
                       kp.ten_khoa AS ten_khoa_phong,
                       l.ma_loai, l.ten_loai, l.kieu_quy_doi, l.don_vi_tinh, l.nhom_id,
                       n.ten_nhom,
                       u1.tai_khoan AS tai_khoan_nguoi_tao,
                       u2.tai_khoan AS tai_khoan_nguoi_cap_nhat
                FROM DT_CME_GHI_NHAN g
                INNER JOIN DM_NHAN_VIEN nv ON nv.id = g.nhan_vien_id
                LEFT JOIN DM_KHOA_PHONG kp ON kp.id = nv.khoa_phong_id
                LEFT JOIN DT_CME_LOAI l ON l.id = g.loai_id
                LEFT JOIN DT_CME_NHOM n ON n.id = l.nhom_id
                LEFT JOIN DM_NGUOI_DUNG u1 ON u1.id = g.nguoi_tao
                LEFT JOIN DM_NGUOI_DUNG u2 ON u2.id = g.nguoi_cap_nhat";
    }

    public static function insert(DT_CmeGhiNhan_PUBLIC $e): int
    {
        $sql = "INSERT INTO DT_CME_GHI_NHAN (nhan_vien_id, loai_id, nam, ten_hoat_dong, vai_tro,
                                             so_luong, gio_tin_chi, ngay_bat_dau, ngay_ket_thuc, ghi_chu,
                                             minh_chung, minh_chung_goc, minh_chung_size,
                                             ngay_tao, ngay_cap_nhat, nguoi_tao, nguoi_cap_nhat, da_xoa)
                VALUES (:nv, :loai, :nam, :hd, :vt, :sl, :gtc, :nbd, :nkt, :gc,
                        :mc, :mcg, :mcs, NOW(), NOW(), :u1, :u2, 0)";
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute([
            ':nv' => $e->nhan_vien_id, ':loai' => $e->loai_id, ':nam' => $e->nam,
            ':hd' => $e->ten_hoat_dong, ':vt' => $e->vai_tro,
            ':sl' => $e->so_luong, ':gtc' => $e->gio_tin_chi,
            ':nbd' => $e->ngay_bat_dau ?: null, ':nkt' => $e->ngay_ket_thuc ?: null,
            ':gc' => $e->ghi_chu,
            ':mc' => $e->minh_chung ?: null, ':mcg' => $e->minh_chung_goc ?: null,
            ':mcs' => $e->minh_chung_size ?: null,
            ':u1' => $e->nguoi_tao ?? 0, ':u2' => $e->nguoi_tao ?? 0,
        ]);
        return (int)Database::getConnection()->lastInsertId();
    }

    /**
     * Cập nhật. Quy ước minh_chung (giống avatar học viên):
     *   null = giữ nguyên file cũ | '' = gỡ file | có giá trị = thay file mới
     */
    public static function update(DT_CmeGhiNhan_PUBLIC $e): int
    {
        $mcClause = '';
        if ($e->minh_chung !== null) {
            $mcClause = ', minh_chung=:mc, minh_chung_goc=:mcg, minh_chung_size=:mcs';
        }
        $sql = "UPDATE DT_CME_GHI_NHAN SET nhan_vien_id=:nv, loai_id=:loai, nam=:nam,
                       ten_hoat_dong=:hd, vai_tro=:vt, so_luong=:sl, gio_tin_chi=:gtc,
                       ngay_bat_dau=:nbd, ngay_ket_thuc=:nkt, ghi_chu=:gc,
                       ngay_cap_nhat=NOW(), nguoi_cap_nhat=:u
                       {$mcClause}
                WHERE id=:id AND da_xoa=0";
        $stmt = Database::getConnection()->prepare($sql);
        $params = [
            ':nv' => $e->nhan_vien_id, ':loai' => $e->loai_id, ':nam' => $e->nam,
            ':hd' => $e->ten_hoat_dong, ':vt' => $e->vai_tro, ':sl' => $e->so_luong, ':gtc' => $e->gio_tin_chi,
            ':nbd' => $e->ngay_bat_dau ?: null, ':nkt' => $e->ngay_ket_thuc ?: null, ':gc' => $e->ghi_chu,
            ':u' => $e->nguoi_cap_nhat ?? 0, ':id' => $e->id,
        ];
        if ($e->minh_chung !== null) {
            $params[':mc']  = $e->minh_chung ?: null;
            $params[':mcg'] = $e->minh_chung ? $e->minh_chung_goc : null;
            $params[':mcs'] = $e->minh_chung ? $e->minh_chung_size : null;
        }
        $stmt->execute($params);
        return $stmt->rowCount();
    }

    /** Chỉ cập nhật cột minh chứng (đính kèm nhanh). Truyền null cho cả 3 = gỡ file. */
    public static function updateMinhChung(int $id, ?string $file, ?string $goc, ?int $size, int $userId): bool
    {
        $stmt = Database::getConnection()->prepare(
            "UPDATE DT_CME_GHI_NHAN SET minh_chung=:mc, minh_chung_goc=:mcg, minh_chung_size=:mcs,
                    ngay_cap_nhat=NOW(), nguoi_cap_nhat=:u
             WHERE id=:id AND da_xoa=0"
        );
        return $stmt->execute([
            ':mc' => $file, ':mcg' => $goc, ':mcs' => $size, ':u' => $userId, ':id' => $id,
        ]);
    }

    /** Lấy tên file minh chứng để xóa file vật lý. */
    public static function getMinhChungFile(int $id): ?string
    {
        $stmt = Database::getConnection()->prepare("SELECT minh_chung FROM DT_CME_GHI_NHAN WHERE id=:id");
        $stmt->execute([':id' => $id]);
        $v = $stmt->fetchColumn();
        return $v ?: null;
    }

    public static function trash(int $id, int $u): int
    {
        $stmt = Database::getConnection()->prepare("UPDATE DT_CME_GHI_NHAN SET da_xoa=1, ngay_cap_nhat=NOW(), nguoi_cap_nhat=:u WHERE id=:id");
        $stmt->execute([':u' => $u, ':id' => $id]);
        return $stmt->rowCount();
    }

    public static function restore(int $id, int $u): int
    {
        $stmt = Database::getConnection()->prepare("UPDATE DT_CME_GHI_NHAN SET da_xoa=0, ngay_cap_nhat=NOW(), nguoi_cap_nhat=:u WHERE id=:id");
        $stmt->execute([':u' => $u, ':id' => $id]);
        return $stmt->rowCount();
    }

    public static function delete(int $id): int
    {
        $stmt = Database::getConnection()->prepare("DELETE FROM DT_CME_GHI_NHAN WHERE id=:id");
        $stmt->execute([':id' => $id]);
        return $stmt->rowCount();
    }

    public static function getById(int $id): ?DT_CmeGhiNhan_DTO
    {
        $stmt = Database::getConnection()->prepare(self::selectSql() . " WHERE g.id=:id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ? Database::hydrate($row, 'DT_CmeGhiNhan_DTO') : null;
    }

    /**
     * @param array $opts search, nam, khoa_phong_id, nhan_vien_id, nhom_id
     */
    public static function getPaged(int $page, int $pageSize, array $opts = [], int $daXoa = 0): array
    {
        [$page, $pageSize, $offset] = PaginationHelper::normalize($page, $pageSize);
        $where = " WHERE g.da_xoa=:dx ";
        $params = [':dx' => $daXoa];

        if (!empty($opts['search'])) {
            $kw = '%' . $opts['search'] . '%';
            $where .= " AND (nv.ma_nv LIKE :s1 OR nv.ho_ten LIKE :s2 OR g.ten_hoat_dong LIKE :s3 OR l.ten_loai LIKE :s4) ";
            $params[':s1'] = $kw; $params[':s2'] = $kw; $params[':s3'] = $kw; $params[':s4'] = $kw;
        }
        if (!empty($opts['nam'])) {
            $where .= " AND g.nam=:nam ";
            $params[':nam'] = (int)$opts['nam'];
        }
        if (!empty($opts['nhan_vien_id'])) {
            $where .= " AND g.nhan_vien_id=:nv ";
            $params[':nv'] = (int)$opts['nhan_vien_id'];
        }
        if (!empty($opts['khoa_phong_id'])) {
            $where .= " AND nv.khoa_phong_id=:kp ";
            $params[':kp'] = (int)$opts['khoa_phong_id'];
        }
        if (!empty($opts['nhom_id'])) {
            $where .= " AND l.nhom_id=:nhom ";
            $params[':nhom'] = (int)$opts['nhom_id'];
        }

        $countSql = "SELECT COUNT(*) FROM DT_CME_GHI_NHAN g
                     INNER JOIN DM_NHAN_VIEN nv ON nv.id = g.nhan_vien_id
                     LEFT JOIN DT_CME_LOAI l ON l.id = g.loai_id" . $where;
        $stmt = Database::getConnection()->prepare($countSql);
        $stmt->execute($params);
        $total = (int)$stmt->fetchColumn();

        $sql = self::selectSql() . $where . " ORDER BY g.nam DESC, g.id DESC LIMIT {$pageSize} OFFSET {$offset}";
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute($params);
        return [
            'data' => $stmt->fetchAll(),
            'totalRecords' => $total,
            'totalPages' => PaginationHelper::totalPages($total, $pageSize),
        ];
    }

    /** Tổng giờ tín chỉ của 1 nhân viên trong khoảng năm [tuNam..denNam]. */
    public static function tongGioNhanVien(int $nhanVienId, int $tuNam, int $denNam): float
    {
        $stmt = Database::getConnection()->prepare(
            "SELECT COALESCE(SUM(gio_tin_chi),0) FROM DT_CME_GHI_NHAN
             WHERE nhan_vien_id=:nv AND da_xoa=0 AND nam BETWEEN :tu AND :den"
        );
        $stmt->execute([':nv' => $nhanVienId, ':tu' => $tuNam, ':den' => $denNam]);
        return (float)$stmt->fetchColumn();
    }

    /** Danh sách hoạt động của 1 nhân viên (theo năm, mới nhất trước). */
    public static function getByNhanVien(int $nhanVienId, int $nam = 0): array
    {
        $sql = self::selectSql() . " WHERE g.nhan_vien_id=:nv AND g.da_xoa=0";
        $params = [':nv' => $nhanVienId];
        if ($nam > 0) { $sql .= " AND g.nam=:nam"; $params[':nam'] = $nam; }
        $sql .= " ORDER BY g.nam DESC, g.id DESC";
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll() ?: [];
    }

    /** Tổng giờ theo nhóm hình thức của 1 nhân viên trong 1 năm. */
    public static function tongTheoNhom(int $nhanVienId, int $nam): array
    {
        $stmt = Database::getConnection()->prepare(
            "SELECT n.id AS nhom_id, n.ten_nhom, COALESCE(SUM(g.gio_tin_chi),0) AS gio, COUNT(*) AS so_ban_ghi
             FROM DT_CME_GHI_NHAN g
             INNER JOIN DT_CME_LOAI l ON l.id = g.loai_id
             INNER JOIN DT_CME_NHOM n ON n.id = l.nhom_id
             WHERE g.nhan_vien_id=:nv AND g.nam=:nam AND g.da_xoa=0
             GROUP BY n.id, n.ten_nhom
             ORDER BY n.thu_tu ASC, n.id ASC"
        );
        $stmt->execute([':nv' => $nhanVienId, ':nam' => $nam]);
        return $stmt->fetchAll() ?: [];
    }

    /**
     * Báo cáo tổng hợp theo nhân viên (mỗi NV 1 dòng: tổng giờ trong năm/khoảng năm).
     * @param array $opts nam | tu_nam | den_nam | khoa_phong_id
     */
    public static function baoCaoTheoNhanVien(array $opts = []): array
    {
        $where = " WHERE g.da_xoa=0 ";
        $params = [];
        if (!empty($opts['tu_nam']) && !empty($opts['den_nam'])) {
            $where .= " AND g.nam BETWEEN :tu AND :den ";
            $params[':tu'] = (int)$opts['tu_nam']; $params[':den'] = (int)$opts['den_nam'];
        } elseif (!empty($opts['nam'])) {
            $where .= " AND g.nam=:nam ";
            $params[':nam'] = (int)$opts['nam'];
        }
        $havingKp = '';
        if (!empty($opts['khoa_phong_id'])) {
            $where .= " AND nv.khoa_phong_id=:kp ";
            $params[':kp'] = (int)$opts['khoa_phong_id'];
        }
        $sql = "SELECT nv.id AS nhan_vien_id, nv.ma_nv, nv.ho_ten,
                       kp.ten_khoa AS ten_khoa_phong,
                       COUNT(*) AS so_ban_ghi,
                       COALESCE(SUM(g.gio_tin_chi),0) AS tong_gio
                FROM DT_CME_GHI_NHAN g
                INNER JOIN DM_NHAN_VIEN nv ON nv.id = g.nhan_vien_id
                LEFT JOIN DM_KHOA_PHONG kp ON kp.id = nv.khoa_phong_id
                {$where}
                GROUP BY nv.id, nv.ma_nv, nv.ho_ten, kp.ten_khoa
                ORDER BY tong_gio DESC, nv.ho_ten ASC";
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll() ?: [];
    }

    /**
     * Danh sách chi tiết từng bản ghi, sắp theo nhân viên → phục vụ export báo cáo theo NV.
     * @param array $opts nam | khoa_phong_id
     */
    public static function chiTietTheoNhanVien(array $opts = []): array
    {
        $where = " WHERE g.da_xoa=0 ";
        $params = [];
        if (!empty($opts['nam'])) { $where .= " AND g.nam=:nam "; $params[':nam'] = (int)$opts['nam']; }
        if (!empty($opts['khoa_phong_id'])) { $where .= " AND nv.khoa_phong_id=:kp "; $params[':kp'] = (int)$opts['khoa_phong_id']; }
        $sql = "SELECT nv.id AS nhan_vien_id, nv.ma_nv, nv.ho_ten, kp.ten_khoa AS ten_khoa_phong,
                       g.nam, g.ten_hoat_dong, g.vai_tro, g.so_luong, g.gio_tin_chi,
                       g.ngay_bat_dau, g.ngay_ket_thuc,
                       n.ten_nhom, l.ten_loai
                FROM DT_CME_GHI_NHAN g
                INNER JOIN DM_NHAN_VIEN nv ON nv.id = g.nhan_vien_id
                LEFT JOIN DM_KHOA_PHONG kp ON kp.id = nv.khoa_phong_id
                LEFT JOIN DT_CME_LOAI l ON l.id = g.loai_id
                LEFT JOIN DT_CME_NHOM n ON n.id = l.nhom_id
                {$where}
                ORDER BY nv.ho_ten ASC, nv.id ASC, g.nam ASC, g.id ASC";
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll() ?: [];
    }

    /**
     * Danh sách nhân viên CHƯA ĐẠT ngưỡng giờ tín chỉ trong khoảng năm (CÓ PHÂN TRANG).
     * Bắt đầu từ dm_nhan_vien + LEFT JOIN nên bao gồm cả NV có 0 giờ (chưa ghi nhận gì).
     *
     * @param float $nguong  số giờ tối thiểu
     * @param int   $tuNam   năm đầu chu kỳ
     * @param int   $denNam  năm cuối chu kỳ
     * @param array $opts    khoa_phong_id | search | trang_thai (chua_ghi_nhan|thieu_nhieu|sap_dat)
     * @param int   $page    trang (0 = lấy tất cả, dùng cho export)
     * @param int   $pageSize
     * @return array ['data'=>[], 'totalRecords'=>int, 'totalPages'=>int]
     */
    public static function nhanVienChuaDat(float $nguong, int $tuNam, int $denNam, array $opts = [],
                                           int $page = 0, int $pageSize = 20): array
    {
        $where = " WHERE nv.da_xoa=0 AND nv.trang_thai=1 ";
        $params = [':tu' => $tuNam, ':den' => $denNam, ':ng' => $nguong];

        if (!empty($opts['khoa_phong_id'])) {
            $where .= " AND nv.khoa_phong_id=:kp ";
            $params[':kp'] = (int)$opts['khoa_phong_id'];
        }
        if (!empty($opts['search'])) {
            $kw = '%' . $opts['search'] . '%';
            $where .= " AND (nv.ma_nv LIKE :s1 OR nv.ho_ten LIKE :s2) ";
            $params[':s1'] = $kw; $params[':s2'] = $kw;
        }

        // Lọc theo trạng thái (áp lên giá trị tổng hợp => dùng HAVING)
        $having = " HAVING COALESCE(SUM(g.gio_tin_chi), 0) < :ng ";
        switch ($opts['trang_thai'] ?? '') {
            case 'chua_ghi_nhan':   // 0 giờ
                $having .= " AND COALESCE(SUM(g.gio_tin_chi), 0) = 0 ";
                break;
            case 'thieu_nhieu':     // > 0 nhưng < 50% ngưỡng
                $having .= " AND COALESCE(SUM(g.gio_tin_chi), 0) > 0
                             AND COALESCE(SUM(g.gio_tin_chi), 0) < :nua1 ";
                $params[':nua1'] = $nguong / 2;
                break;
            case 'sap_dat':         // >= 50% ngưỡng
                $having .= " AND COALESCE(SUM(g.gio_tin_chi), 0) >= :nua2 ";
                $params[':nua2'] = $nguong / 2;
                break;
        }

        // Chỉ tính các ghi nhận trong khoảng năm (điều kiện đặt ở ON để giữ NV 0 giờ)
        $from = "FROM DM_NHAN_VIEN nv
                 LEFT JOIN DM_KHOA_PHONG kp ON kp.id = nv.khoa_phong_id
                 LEFT JOIN DT_CME_GHI_NHAN g
                        ON g.nhan_vien_id = nv.id AND g.da_xoa = 0
                       AND g.nam BETWEEN :tu AND :den
                 {$where}
                 GROUP BY nv.id, nv.ma_nv, nv.ho_ten, nv.trinh_do, kp.ten_khoa
                 {$having}";

        // Đếm tổng (bọc subquery vì có GROUP BY + HAVING)
        $countSql = "SELECT COUNT(*) FROM (SELECT nv.id " . $from . ") t";
        $stmt = Database::getConnection()->prepare($countSql);
        $stmt->execute($params);
        $total = (int)$stmt->fetchColumn();

        $sql = "SELECT nv.id AS nhan_vien_id, nv.ma_nv, nv.ho_ten, nv.trinh_do,
                       kp.ten_khoa AS ten_khoa_phong,
                       COALESCE(SUM(g.gio_tin_chi), 0) AS tong_gio,
                       COUNT(g.id) AS so_ban_ghi
                " . $from . "
                ORDER BY tong_gio ASC, nv.ho_ten ASC";

        if ($page > 0) {
            [$page, $pageSize, $offset] = PaginationHelper::normalize($page, $pageSize);
            $sql .= " LIMIT {$pageSize} OFFSET {$offset}";
        }
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute($params);

        return [
            'data'         => $stmt->fetchAll() ?: [],
            'totalRecords' => $total,
            'totalPages'   => $page > 0 ? PaginationHelper::totalPages($total, $pageSize) : 1,
        ];
    }

    /** Thống kê nhanh cho màn cảnh báo: tổng NV đang làm / đạt / chưa đạt. */
    public static function thongKeCanhBao(float $nguong, int $tuNam, int $denNam, int $khoaPhongId = 0): array
    {
        $where = " WHERE nv.da_xoa=0 AND nv.trang_thai=1 ";
        $params = [':tu' => $tuNam, ':den' => $denNam];
        if ($khoaPhongId > 0) { $where .= " AND nv.khoa_phong_id=:kp "; $params[':kp'] = $khoaPhongId; }

        $sql = "SELECT
                  COUNT(*) AS tong_nv,
                  SUM(CASE WHEN t.gio >= :ng1 THEN 1 ELSE 0 END) AS so_dat,
                  SUM(CASE WHEN t.gio <  :ng2 THEN 1 ELSE 0 END) AS so_chua_dat,
                  SUM(CASE WHEN t.gio = 0 THEN 1 ELSE 0 END) AS so_chua_ghi_nhan
                FROM (
                  SELECT nv.id, COALESCE(SUM(g.gio_tin_chi),0) AS gio
                  FROM DM_NHAN_VIEN nv
                  LEFT JOIN DT_CME_GHI_NHAN g
                         ON g.nhan_vien_id = nv.id AND g.da_xoa = 0
                        AND g.nam BETWEEN :tu AND :den
                  {$where}
                  GROUP BY nv.id
                ) t";
        $params[':ng1'] = $nguong; $params[':ng2'] = $nguong;
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch() ?: ['tong_nv'=>0,'so_dat'=>0,'so_chua_dat'=>0,'so_chua_ghi_nhan'=>0];
    }

    /** Các năm có dữ liệu (cho combo lọc). */
    public static function getNamCombo(): array
    {
        $stmt = Database::getConnection()->query(
            "SELECT DISTINCT nam FROM DT_CME_GHI_NHAN WHERE da_xoa=0 ORDER BY nam DESC"
        );
        return $stmt->fetchAll(PDO::FETCH_COLUMN) ?: [];
    }

    /** Tổng giờ + số bản ghi theo nhóm hình thức (toàn viện, theo năm nếu có). */
    public static function tongToanVienTheoNhom(int $nam = 0): array
    {
        $where = "g.da_xoa=0" . ($nam > 0 ? " AND g.nam=:nam" : "");
        $sql = "SELECT n.id AS nhom_id, n.ten_nhom,
                       COUNT(*) AS so_ban_ghi, COALESCE(SUM(g.gio_tin_chi),0) AS tong_gio
                FROM DT_CME_GHI_NHAN g
                INNER JOIN DT_CME_LOAI l ON l.id = g.loai_id
                INNER JOIN DT_CME_NHOM n ON n.id = l.nhom_id
                WHERE {$where}
                GROUP BY n.id, n.ten_nhom
                ORDER BY n.thu_tu ASC, n.id ASC";
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute($nam > 0 ? [':nam' => $nam] : []);
        return $stmt->fetchAll() ?: [];
    }

    /** Tổng giờ + số NV theo khoa/phòng (theo năm nếu có). */
    public static function tongTheoKhoaPhong(int $nam = 0): array
    {
        $where = "g.da_xoa=0" . ($nam > 0 ? " AND g.nam=:nam" : "");
        $sql = "SELECT kp.id AS khoa_phong_id, kp.ten_khoa,
                       COUNT(DISTINCT g.nhan_vien_id) AS so_nhan_vien,
                       COUNT(*) AS so_ban_ghi,
                       COALESCE(SUM(g.gio_tin_chi),0) AS tong_gio
                FROM DT_CME_GHI_NHAN g
                INNER JOIN DM_NHAN_VIEN nv ON nv.id = g.nhan_vien_id
                LEFT JOIN DM_KHOA_PHONG kp ON kp.id = nv.khoa_phong_id
                WHERE {$where}
                GROUP BY kp.id, kp.ten_khoa
                ORDER BY tong_gio DESC";
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute($nam > 0 ? [':nam' => $nam] : []);
        return $stmt->fetchAll() ?: [];
    }

    /** Top N nhân viên theo tổng giờ (theo năm nếu có). */
    public static function topNhanVien(int $limit = 10, int $nam = 0): array
    {
        $limit = max(1, min(100, $limit));
        $where = "g.da_xoa=0" . ($nam > 0 ? " AND g.nam=:nam" : "");
        $sql = "SELECT nv.id AS nhan_vien_id, nv.ma_nv, nv.ho_ten, kp.ten_khoa AS ten_khoa_phong,
                       COALESCE(SUM(g.gio_tin_chi),0) AS tong_gio
                FROM DT_CME_GHI_NHAN g
                INNER JOIN DM_NHAN_VIEN nv ON nv.id = g.nhan_vien_id
                LEFT JOIN DM_KHOA_PHONG kp ON kp.id = nv.khoa_phong_id
                WHERE {$where}
                GROUP BY nv.id, nv.ma_nv, nv.ho_ten, kp.ten_khoa
                ORDER BY tong_gio DESC, nv.ho_ten ASC
                LIMIT {$limit}";
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute($nam > 0 ? [':nam' => $nam] : []);
        return $stmt->fetchAll() ?: [];
    }

    public static function getStats(int $nam = 0): array
    {
        $where = "da_xoa=0" . ($nam > 0 ? " AND nam=:nam" : "");
        $sql = "SELECT COUNT(*) AS so_ban_ghi,
                       COUNT(DISTINCT nhan_vien_id) AS so_nhan_vien,
                       COALESCE(SUM(gio_tin_chi),0) AS tong_gio
                FROM DT_CME_GHI_NHAN WHERE {$where}";
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute($nam > 0 ? [':nam' => $nam] : []);
        return $stmt->fetch() ?: ['so_ban_ghi' => 0, 'so_nhan_vien' => 0, 'tong_gio' => 0];
    }
}
