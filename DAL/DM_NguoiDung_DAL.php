<?php
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/../PUBLIC/Entities/DM_NguoiDung_DTO.php';

class DM_NguoiDung_DAL
{
    const TABLE = 'DM_NGUOI_DUNG';

    private static function selectSql(): string
    {
        return "SELECT nd.*, nv.ho_ten, nv.ma_nv, nv.chuc_danh,
                       kp.ten_khoa AS khoa_phong_text,
                       nt.ten_nhom, nt.ma_nhom AS ma_nhom,
                       nt1.tai_khoan AS tai_khoan_nguoi_tao,
                       nt2.tai_khoan AS tai_khoan_nguoi_cap_nhat
                FROM DM_NGUOI_DUNG nd
                LEFT JOIN DM_NHAN_VIEN nv ON nv.id = nd.nhan_vien_id
                LEFT JOIN DM_KHOA_PHONG kp ON kp.id = nv.khoa_phong_id
                LEFT JOIN DM_NHOM_TAI_KHOAN nt ON nt.id = nd.nhom_tai_khoan_id
                LEFT JOIN DM_NGUOI_DUNG nt1 ON nt1.id = nd.nguoi_tao
                LEFT JOIN DM_NGUOI_DUNG nt2 ON nt2.id = nd.nguoi_cap_nhat";
    }

    public static function insert(DM_NguoiDung_PUBLIC $e): int
    {
        $sql = "INSERT INTO DM_NGUOI_DUNG (nhan_vien_id, tai_khoan, mat_khau, nhom_tai_khoan_id, trang_thai,
                                          ngay_tao, ngay_cap_nhat, nguoi_tao, nguoi_cap_nhat, da_xoa)
                VALUES (:nhan_vien_id, :tai_khoan, :mat_khau, :nhom, :tt, NOW(), NOW(), :nt1, :nt2, 0)";
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute([
            ':nhan_vien_id' => $e->nhan_vien_id,
            ':tai_khoan' => $e->tai_khoan,
            ':mat_khau' => $e->mat_khau,
            ':nhom' => $e->nhom_tai_khoan_id,
            ':tt' => $e->trang_thai,
            ':nt1' => $e->nguoi_tao,
            ':nt2' => $e->nguoi_tao,
        ]);
        return (int)Database::getConnection()->lastInsertId();
    }

    public static function update(DM_NguoiDung_PUBLIC $e): int
    {
        $sql = "UPDATE DM_NGUOI_DUNG
                SET nhan_vien_id = :nhan_vien_id,
                    tai_khoan = :tai_khoan,
                    nhom_tai_khoan_id = :nhom,
                    trang_thai = :tt,
                    ngay_cap_nhat = NOW(),
                    nguoi_cap_nhat = :ncn
                WHERE id = :id AND da_xoa = 0";
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute([
            ':nhan_vien_id' => $e->nhan_vien_id,
            ':tai_khoan' => $e->tai_khoan,
            ':nhom' => $e->nhom_tai_khoan_id,
            ':tt' => $e->trang_thai,
            ':ncn' => $e->nguoi_cap_nhat,
            ':id' => $e->id,
        ]);
        return $stmt->rowCount();
    }

    public static function updatePassword(int $id, string $hashedPass, int $nguoiCapNhat): int
    {
        $sql = "UPDATE DM_NGUOI_DUNG SET mat_khau=:p, ngay_cap_nhat=NOW(), nguoi_cap_nhat=:u WHERE id=:id";
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute([':p' => $hashedPass, ':u' => $nguoiCapNhat, ':id' => $id]);
        return $stmt->rowCount();
    }

    public static function trash(int $id, int $nguoiCapNhat): int
    {
        $sql = "UPDATE DM_NGUOI_DUNG SET da_xoa=1, ngay_cap_nhat=NOW(), nguoi_cap_nhat=:u WHERE id=:id";
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute([':u' => $nguoiCapNhat, ':id' => $id]);
        return $stmt->rowCount();
    }

    public static function restore(int $id, int $nguoiCapNhat): int
    {
        $sql = "UPDATE DM_NGUOI_DUNG SET da_xoa=0, ngay_cap_nhat=NOW(), nguoi_cap_nhat=:u WHERE id=:id";
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute([':u' => $nguoiCapNhat, ':id' => $id]);
        return $stmt->rowCount();
    }

    public static function delete(int $id): int
    {
        $stmt = Database::getConnection()->prepare("DELETE FROM DM_NGUOI_DUNG WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->rowCount();
    }

    public static function getById(int $id): ?DM_NguoiDung_DTO
    {
        $sql = self::selectSql() . " WHERE nd.id = :id";
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ? Database::hydrate($row, 'DM_NguoiDung_DTO') : null;
    }

    public static function getByTaiKhoan(string $taiKhoan): ?DM_NguoiDung_DTO
    {
        $sql = self::selectSql() . " WHERE nd.tai_khoan = :tk AND nd.da_xoa = 0";
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute([':tk' => $taiKhoan]);
        $row = $stmt->fetch();
        return $row ? Database::hydrate($row, 'DM_NguoiDung_DTO') : null;
    }

    public static function getPaged(int $page, int $pageSize, string $search = '', int $daXoa = 0, int $nhomId = 0): array
    {
        [$page, $pageSize, $offset] = PaginationHelper::normalize($page, $pageSize);
        $where = ' WHERE nd.da_xoa = :dx ';
        $params = [':dx' => $daXoa];
        if ($search !== '') {
            $where .= ' AND (nd.tai_khoan LIKE :s OR nv.ho_ten LIKE :s OR nv.ma_nv LIKE :s) ';
            $params[':s'] = "%{$search}%";
        }
        if ($nhomId > 0) {
            $where .= ' AND nd.nhom_tai_khoan_id = :nhom ';
            $params[':nhom'] = $nhomId;
        }

        $countSql = "SELECT COUNT(*) FROM DM_NGUOI_DUNG nd
                     LEFT JOIN DM_NHAN_VIEN nv ON nv.id = nd.nhan_vien_id" . $where;
        $stmt = Database::getConnection()->prepare($countSql);
        $stmt->execute($params);
        $total = (int)$stmt->fetchColumn();

        $sql = self::selectSql() . $where . " ORDER BY nd.id DESC LIMIT {$pageSize} OFFSET {$offset}";
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute($params);
        $data = $stmt->fetchAll();

        return [
            'data' => $data,
            'totalRecords' => $total,
            'totalPages' => PaginationHelper::totalPages($total, $pageSize),
        ];
    }

    public static function checkTaiKhoanExists(string $taiKhoan, int $excludeId = 0): bool
    {
        $sql = "SELECT COUNT(*) FROM DM_NGUOI_DUNG WHERE tai_khoan = :tk AND da_xoa = 0 AND id <> :id";
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute([':tk' => $taiKhoan, ':id' => $excludeId]);
        return (int)$stmt->fetchColumn() > 0;
    }

    public static function updateLastLogin(int $id): void
    {
        $stmt = Database::getConnection()->prepare("UPDATE DM_NGUOI_DUNG SET lan_dang_nhap_cuoi = NOW() WHERE id = :id");
        $stmt->execute([':id' => $id]);
    }
}
