<?php
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/../PUBLIC/Entities/DT_DangKyKhoaHoc_DTO.php';

class DT_DangKyKhoaHoc_DAL
{
    private static function selectSql(): string
    {
        return "SELECT dk.*,
                       dk.khoa_hoc_chuong_trinh_id AS lop_hoc_id,
                       kh.ma_khoa_hoc, kh.ten_khoa_hoc,
                       hv.ma_hv, hv.ho_ten AS ho_ten_hoc_vien,
                       lop.ma_chuong_trinh AS ma_lop, lop.ten_chuong_trinh AS ten_lop,
                       u.tai_khoan AS tai_khoan_nguoi_xu_ly
                FROM DT_DANG_KY_KHOA_HOC dk
                LEFT JOIN DT_KHOA_HOC kh ON kh.id = dk.khoa_hoc_id
                LEFT JOIN DM_HOC_VIEN hv ON hv.id = dk.hoc_vien_id
                LEFT JOIN DT_KHOA_HOC_CHUONG_TRINH khct ON khct.id = dk.khoa_hoc_chuong_trinh_id
                LEFT JOIN DT_CHUONG_TRINH lop ON lop.id = khct.chuong_trinh_id
                LEFT JOIN DM_NGUOI_DUNG u ON u.id = dk.nguoi_xu_ly";
    }

    public static function insert(DT_DangKyKhoaHoc_PUBLIC $e): int
    {
        $sql = "INSERT INTO DT_DANG_KY_KHOA_HOC
                (ma_tra_cuu, khoa_hoc_id, ho_ten, ngay_sinh, gioi_tinh, cccd, dien_thoai, email,
                 dia_chi, don_vi_cong_tac, chuc_vu, cccd_file, bang_cap_file, ly_do_dang_ky,
                 trang_thai, ip_dang_ky, ngay_tao, ngay_cap_nhat, da_xoa)
                VALUES
                (:ma, :kh, :ht, :ns, :gt, :cccd, :dt, :em,
                 :dc, :dv, :cv, :ccf, :bcf, :ldd,
                 0, :ip, NOW(), NOW(), 0)";
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute([
            ':ma'   => $e->ma_tra_cuu,
            ':kh'   => $e->khoa_hoc_id,
            ':ht'   => $e->ho_ten,
            ':ns'   => $e->ngay_sinh,
            ':gt'   => $e->gioi_tinh,
            ':cccd' => $e->cccd,
            ':dt'   => $e->dien_thoai,
            ':em'   => $e->email,
            ':dc'   => $e->dia_chi,
            ':dv'   => $e->don_vi_cong_tac,
            ':cv'   => $e->chuc_vu,
            ':ccf'  => $e->cccd_file,
            ':bcf'  => $e->bang_cap_file,
            ':ldd'  => $e->ly_do_dang_ky,
            ':ip'   => $e->ip_dang_ky,
        ]);
        return (int)Database::getConnection()->lastInsertId();
    }

    public static function approve(int $id, int $userId, int $hocVienId, ?int $lopId, ?string $note): int
    {
        $sql = "UPDATE DT_DANG_KY_KHOA_HOC
                SET trang_thai=1, hoc_vien_id=:hv, khoa_hoc_chuong_trinh_id=:lop,
                    ly_do_xu_ly=:note, ngay_xu_ly=NOW(), nguoi_xu_ly=:u,
                    ngay_cap_nhat=NOW(), nguoi_cap_nhat=:u2
                WHERE id=:id AND da_xoa=0";
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute([
            ':hv'   => $hocVienId,
            ':lop'  => $lopId,
            ':note' => $note,
            ':u'    => $userId,
            ':u2'   => $userId,
            ':id'   => $id,
        ]);
        return $stmt->rowCount();
    }

    public static function reject(int $id, int $userId, string $note): int
    {
        $sql = "UPDATE DT_DANG_KY_KHOA_HOC
                SET trang_thai=2, ly_do_xu_ly=:note,
                    ngay_xu_ly=NOW(), nguoi_xu_ly=:u,
                    ngay_cap_nhat=NOW(), nguoi_cap_nhat=:u2
                WHERE id=:id AND da_xoa=0";
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute([
            ':note' => $note, ':u' => $userId, ':u2' => $userId, ':id' => $id,
        ]);
        return $stmt->rowCount();
    }

    public static function trash(int $id, int $u): int
    {
        $stmt = Database::getConnection()->prepare(
            "UPDATE DT_DANG_KY_KHOA_HOC SET da_xoa=1, ngay_cap_nhat=NOW(), nguoi_cap_nhat=:u WHERE id=:id"
        );
        $stmt->execute([':u' => $u, ':id' => $id]);
        return $stmt->rowCount();
    }

    public static function getById(int $id): ?DT_DangKyKhoaHoc_DTO
    {
        $stmt = Database::getConnection()->prepare(self::selectSql() . " WHERE dk.id=:id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ? Database::hydrate($row, 'DT_DangKyKhoaHoc_DTO') : null;
    }

    public static function getByMaTraCuu(string $ma): ?DT_DangKyKhoaHoc_DTO
    {
        $stmt = Database::getConnection()->prepare(self::selectSql() . " WHERE dk.ma_tra_cuu=:ma AND dk.da_xoa=0");
        $stmt->execute([':ma' => $ma]);
        $row = $stmt->fetch();
        return $row ? Database::hydrate($row, 'DT_DangKyKhoaHoc_DTO') : null;
    }

    public static function getPaged(int $page, int $pageSize, array $opts = [], int $daXoa = 0): array
    {
        [$page, $pageSize, $offset] = PaginationHelper::normalize($page, $pageSize);
        $where = " WHERE dk.da_xoa=:dx ";
        $params = [':dx' => $daXoa];

        if (!empty($opts['search'])) {
            $kw = '%' . $opts['search'] . '%';
            $where .= " AND (dk.ho_ten LIKE :s1 OR dk.email LIKE :s2 OR dk.cccd LIKE :s3 OR dk.ma_tra_cuu LIKE :s4) ";
            $params[':s1'] = $kw; $params[':s2'] = $kw; $params[':s3'] = $kw; $params[':s4'] = $kw;
        }
        if (isset($opts['trang_thai']) && $opts['trang_thai'] !== '' && $opts['trang_thai'] !== null) {
            $where .= " AND dk.trang_thai=:tt ";
            $params[':tt'] = (int)$opts['trang_thai'];
        }
        if (!empty($opts['khoa_hoc_id'])) {
            $where .= " AND dk.khoa_hoc_id=:kh ";
            $params[':kh'] = (int)$opts['khoa_hoc_id'];
        }

        $countSql = "SELECT COUNT(*) FROM DT_DANG_KY_KHOA_HOC dk" . $where;
        $stmt = Database::getConnection()->prepare($countSql);
        $stmt->execute($params);
        $total = (int)$stmt->fetchColumn();

        $sql = self::selectSql() . $where . " ORDER BY dk.id DESC LIMIT {$pageSize} OFFSET {$offset}";
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute($params);
        return [
            'data'         => $stmt->fetchAll(),
            'totalRecords' => $total,
            'totalPages'   => PaginationHelper::totalPages($total, $pageSize),
        ];
    }

    public static function getStats(): array
    {
        $sql = "SELECT
                  COUNT(*) AS total,
                  SUM(CASE WHEN trang_thai=0 THEN 1 ELSE 0 END) AS so_cho,
                  SUM(CASE WHEN trang_thai=1 THEN 1 ELSE 0 END) AS so_duyet,
                  SUM(CASE WHEN trang_thai=2 THEN 1 ELSE 0 END) AS so_tu_choi
                FROM DT_DANG_KY_KHOA_HOC WHERE da_xoa=0";
        return Database::getConnection()->query($sql)->fetch() ?: [];
    }

    public static function checkRecentByEmailOrCccd(string $email, string $cccd, int $hours = 1): bool
    {
        $stmt = Database::getConnection()->prepare(
            "SELECT COUNT(*) FROM DT_DANG_KY_KHOA_HOC
             WHERE (email=:em OR cccd=:cccd)
             AND ngay_tao > DATE_SUB(NOW(), INTERVAL :h HOUR)
             AND da_xoa=0 AND trang_thai=0"
        );
        $stmt->execute([':em' => $email, ':cccd' => $cccd, ':h' => $hours]);
        return (int)$stmt->fetchColumn() > 0;
    }
}
