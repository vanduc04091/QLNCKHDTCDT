<?php
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/../PUBLIC/Entities/DT_MonHoc_DTO.php';

class DT_MonHoc_DAL
{
    private static function selectSql(): string
    {
        return "SELECT mh.*,
                       u1.tai_khoan AS tai_khoan_nguoi_tao,
                       u2.tai_khoan AS tai_khoan_nguoi_cap_nhat,
                       (SELECT COUNT(*) FROM DT_CHUONG_TRINH_MON_HOC km
                          WHERE km.mon_hoc_id = mh.id AND km.da_xoa = 0) AS so_khoa_hoc_su_dung,
                       (SELECT GROUP_CONCAT(CONCAT(ct.ma_chuong_trinh, '::', ct.ten_chuong_trinh) ORDER BY ct.thu_tu, ct.id SEPARATOR '||')
                          FROM DT_CHUONG_TRINH_MON_HOC km2
                          JOIN DT_CHUONG_TRINH ct ON ct.id = km2.chuong_trinh_id AND ct.da_xoa = 0
                          WHERE km2.mon_hoc_id = mh.id AND km2.da_xoa = 0) AS ds_chuong_trinh
                FROM DT_MON_HOC mh
                LEFT JOIN DM_NGUOI_DUNG u1 ON u1.id = mh.nguoi_tao
                LEFT JOIN DM_NGUOI_DUNG u2 ON u2.id = mh.nguoi_cap_nhat";
    }

    public static function insert(DT_MonHoc_PUBLIC $e): int
    {
        $sql = "INSERT INTO DT_MON_HOC
                (ma_mon_hoc, ten_mon_hoc, mo_ta, so_tiet_ly_thuyet, so_tiet_thuc_hanh,
                 tong_so_tiet, so_tin_chi, trang_thai, ngay_tao, ngay_cap_nhat,
                 nguoi_tao, nguoi_cap_nhat, da_xoa)
                VALUES (:ma, :ten, :mt, :slt, :sth, :tst, :stc, :tt, NOW(), NOW(), :u1, :u2, 0)";
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute([
            ':ma' => $e->ma_mon_hoc, ':ten' => $e->ten_mon_hoc, ':mt' => $e->mo_ta,
            ':slt' => $e->so_tiet_ly_thuyet, ':sth' => $e->so_tiet_thuc_hanh, ':tst' => $e->tong_so_tiet,
            ':stc' => $e->so_tin_chi, ':tt' => $e->trang_thai,
            ':u1' => $e->nguoi_tao ?? 0, ':u2' => $e->nguoi_tao ?? 0,
        ]);
        return (int)Database::getConnection()->lastInsertId();
    }

    public static function update(DT_MonHoc_PUBLIC $e): int
    {
        $sql = "UPDATE DT_MON_HOC SET
                ma_mon_hoc=:ma, ten_mon_hoc=:ten, mo_ta=:mt,
                so_tiet_ly_thuyet=:slt, so_tiet_thuc_hanh=:sth, tong_so_tiet=:tst,
                so_tin_chi=:stc, trang_thai=:tt,
                ngay_cap_nhat=NOW(), nguoi_cap_nhat=:u
                WHERE id=:id AND da_xoa=0";
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute([
            ':ma' => $e->ma_mon_hoc, ':ten' => $e->ten_mon_hoc, ':mt' => $e->mo_ta,
            ':slt' => $e->so_tiet_ly_thuyet, ':sth' => $e->so_tiet_thuc_hanh, ':tst' => $e->tong_so_tiet,
            ':stc' => $e->so_tin_chi, ':tt' => $e->trang_thai,
            ':u' => $e->nguoi_cap_nhat ?? 0, ':id' => $e->id,
        ]);
        return $stmt->rowCount();
    }

    public static function trash(int $id, int $u): int
    {
        $stmt = Database::getConnection()->prepare("UPDATE DT_MON_HOC SET da_xoa=1, ngay_cap_nhat=NOW(), nguoi_cap_nhat=:u WHERE id=:id");
        $stmt->execute([':u' => $u, ':id' => $id]);
        return $stmt->rowCount();
    }

    public static function restore(int $id, int $u): int
    {
        $stmt = Database::getConnection()->prepare("UPDATE DT_MON_HOC SET da_xoa=0, ngay_cap_nhat=NOW(), nguoi_cap_nhat=:u WHERE id=:id");
        $stmt->execute([':u' => $u, ':id' => $id]);
        return $stmt->rowCount();
    }

    public static function delete(int $id): int
    {
        $stmt = Database::getConnection()->prepare("DELETE FROM DT_MON_HOC WHERE id=:id");
        $stmt->execute([':id' => $id]);
        return $stmt->rowCount();
    }

    public static function getById(int $id): ?DT_MonHoc_DTO
    {
        $stmt = Database::getConnection()->prepare(self::selectSql() . " WHERE mh.id=:id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ? Database::hydrate($row, 'DT_MonHoc_DTO') : null;
    }

    public static function getPaged(int $page, int $pageSize, string $search = '', int $daXoa = 0, int $trangThai = -1, int $chuongTrinhId = 0): array
    {
        [$page, $pageSize, $offset] = PaginationHelper::normalize($page, $pageSize);
        $where = " WHERE mh.da_xoa=:dx ";
        $params = [':dx' => $daXoa];
        if ($search !== '') {
            $where .= " AND (mh.ma_mon_hoc LIKE :s1 OR mh.ten_mon_hoc LIKE :s2) ";
            $kw = "%{$search}%";
            $params[':s1'] = $kw; $params[':s2'] = $kw;
        }
        if ($trangThai >= 0) {
            $where .= " AND mh.trang_thai=:tt ";
            $params[':tt'] = $trangThai;
        }
        if ($chuongTrinhId > 0) {
            $where .= " AND EXISTS (SELECT 1 FROM DT_CHUONG_TRINH_MON_HOC km
                                    WHERE km.mon_hoc_id = mh.id AND km.chuong_trinh_id=:ctf AND km.da_xoa=0) ";
            $params[':ctf'] = $chuongTrinhId;
        }

        $countSql = "SELECT COUNT(*) FROM DT_MON_HOC mh" . $where;
        $stmt = Database::getConnection()->prepare($countSql);
        $stmt->execute($params);
        $total = (int)$stmt->fetchColumn();

        $sql = self::selectSql() . $where . " ORDER BY mh.id DESC LIMIT {$pageSize} OFFSET {$offset}";
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute($params);
        return [
            'data' => $stmt->fetchAll(),
            'totalRecords' => $total,
            'totalPages' => PaginationHelper::totalPages($total, $pageSize),
        ];
    }

    public static function getStats(): array
    {
        $pdo = Database::getConnection();
        $total = (int)$pdo->query("SELECT COUNT(*) FROM DT_MON_HOC WHERE da_xoa=0")->fetchColumn();
        $active = (int)$pdo->query("SELECT COUNT(*) FROM DT_MON_HOC WHERE da_xoa=0 AND trang_thai=1")->fetchColumn();
        $inKhoa = (int)$pdo->query("SELECT COUNT(DISTINCT mon_hoc_id) FROM DT_CHUONG_TRINH_MON_HOC WHERE da_xoa=0")->fetchColumn();
        $trash = (int)$pdo->query("SELECT COUNT(*) FROM DT_MON_HOC WHERE da_xoa=1")->fetchColumn();
        return [
            'total' => $total,
            'active' => $active,
            'in_khoa' => $inKhoa,
            'trash' => $trash,
        ];
    }

    public static function checkMaExists(string $ma, int $excludeId = 0): bool
    {
        $stmt = Database::getConnection()->prepare("SELECT COUNT(*) FROM DT_MON_HOC WHERE ma_mon_hoc=:m AND da_xoa=0 AND id<>:id");
        $stmt->execute([':m' => $ma, ':id' => $excludeId]);
        return (int)$stmt->fetchColumn() > 0;
    }

    public static function getCombo(): array
    {
        $key = 'dt_mon_hoc:combo';
        $cached = MemcachedHelper::get($key);
        if ($cached !== null) return $cached;
        $stmt = Database::getConnection()->query(
            "SELECT id, ma_mon_hoc, ten_mon_hoc, so_tiet_ly_thuyet, so_tiet_thuc_hanh, tong_so_tiet, so_tin_chi
             FROM DT_MON_HOC WHERE da_xoa=0 AND trang_thai=1 ORDER BY ten_mon_hoc ASC"
        );
        $data = $stmt->fetchAll();
        MemcachedHelper::set($key, $data, Constants::CACHE_TTL_COMBO);
        return $data;
    }

    /** Số CTĐT mà bài học đang thuộc (chặn xóa khi còn gắn). */
    public static function isUsedInKhoaHoc(int $monHocId): int
    {
        $stmt = Database::getConnection()->prepare(
            "SELECT COUNT(*) FROM DT_CHUONG_TRINH_MON_HOC WHERE mon_hoc_id=:id AND da_xoa=0"
        );
        $stmt->execute([':id' => $monHocId]);
        return (int)$stmt->fetchColumn();
    }

    /** Danh sách CTĐT (id) mà 1 bài đang gắn — để preset multi-select khi sửa bài. */
    public static function getChuongTrinhIds(int $monHocId): array
    {
        $stmt = Database::getConnection()->prepare(
            "SELECT chuong_trinh_id FROM DT_CHUONG_TRINH_MON_HOC WHERE mon_hoc_id=:id AND da_xoa=0"
        );
        $stmt->execute([':id' => $monHocId]);
        return array_map('intval', $stmt->fetchAll(PDO::FETCH_COLUMN));
    }
}
