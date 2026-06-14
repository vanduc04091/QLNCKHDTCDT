<?php
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/../PUBLIC/Entities/DM_KhoaPhong_DTO.php';

class DM_KhoaPhong_DAL
{
    private static function selectSql(): string
    {
        return "SELECT kp.*,
                       nv.ho_ten AS ten_truong_khoa,
                       (SELECT COUNT(*) FROM DM_NHAN_VIEN n WHERE n.khoa_phong_id = kp.id AND n.da_xoa = 0) AS so_nhan_vien,
                       u1.tai_khoan AS tai_khoan_nguoi_tao,
                       u2.tai_khoan AS tai_khoan_nguoi_cap_nhat
                FROM DM_KHOA_PHONG kp
                LEFT JOIN DM_NHAN_VIEN nv ON nv.id = kp.truong_khoa_id
                LEFT JOIN DM_NGUOI_DUNG u1 ON u1.id = kp.nguoi_tao
                LEFT JOIN DM_NGUOI_DUNG u2 ON u2.id = kp.nguoi_cap_nhat";
    }

    public static function insert(DM_KhoaPhong_PUBLIC $e): int
    {
        $sql = "INSERT INTO DM_KHOA_PHONG
                (ma_khoa, ten_khoa, loai_don_vi, truong_khoa_id, dien_thoai, email, chuyen_khoa, so_giuong, trang_thai,
                 ngay_tao, ngay_cap_nhat, nguoi_tao, nguoi_cap_nhat, da_xoa)
                VALUES (:ma, :ten, :loai, :tk, :dt, :em, :ck, :sg, :tt, NOW(), NOW(), :u1, :u2, 0)";
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute([
            ':ma' => $e->ma_khoa, ':ten' => $e->ten_khoa, ':loai' => $e->loai_don_vi,
            ':tk' => $e->truong_khoa_id, ':dt' => $e->dien_thoai, ':em' => $e->email,
            ':ck' => $e->chuyen_khoa, ':sg' => $e->so_giuong, ':tt' => $e->trang_thai,
            ':u1' => $e->nguoi_tao, ':u2' => $e->nguoi_tao,
        ]);
        return (int)Database::getConnection()->lastInsertId();
    }

    public static function update(DM_KhoaPhong_PUBLIC $e): int
    {
        $sql = "UPDATE DM_KHOA_PHONG SET
                ma_khoa=:ma, ten_khoa=:ten, loai_don_vi=:loai, truong_khoa_id=:tk,
                dien_thoai=:dt, email=:em, chuyen_khoa=:ck, so_giuong=:sg, trang_thai=:tt,
                ngay_cap_nhat=NOW(), nguoi_cap_nhat=:u
                WHERE id=:id AND da_xoa=0";
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute([
            ':ma' => $e->ma_khoa, ':ten' => $e->ten_khoa, ':loai' => $e->loai_don_vi,
            ':tk' => $e->truong_khoa_id, ':dt' => $e->dien_thoai, ':em' => $e->email,
            ':ck' => $e->chuyen_khoa, ':sg' => $e->so_giuong, ':tt' => $e->trang_thai,
            ':u' => $e->nguoi_cap_nhat, ':id' => $e->id,
        ]);
        return $stmt->rowCount();
    }

    public static function trash(int $id, int $u): int
    {
        $stmt = Database::getConnection()->prepare("UPDATE DM_KHOA_PHONG SET da_xoa=1, ngay_cap_nhat=NOW(), nguoi_cap_nhat=:u WHERE id=:id");
        $stmt->execute([':u' => $u, ':id' => $id]);
        return $stmt->rowCount();
    }

    public static function restore(int $id, int $u): int
    {
        $stmt = Database::getConnection()->prepare("UPDATE DM_KHOA_PHONG SET da_xoa=0, ngay_cap_nhat=NOW(), nguoi_cap_nhat=:u WHERE id=:id");
        $stmt->execute([':u' => $u, ':id' => $id]);
        return $stmt->rowCount();
    }

    public static function delete(int $id): int
    {
        $stmt = Database::getConnection()->prepare("DELETE FROM DM_KHOA_PHONG WHERE id=:id");
        $stmt->execute([':id' => $id]);
        return $stmt->rowCount();
    }

    public static function getById(int $id): ?DM_KhoaPhong_DTO
    {
        $stmt = Database::getConnection()->prepare(self::selectSql() . " WHERE kp.id=:id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ? Database::hydrate($row, 'DM_KhoaPhong_DTO') : null;
    }

    public static function getPaged(int $page, int $pageSize, string $search = '', int $daXoa = 0, string $loai = ''): array
    {
        [$page, $pageSize, $offset] = PaginationHelper::normalize($page, $pageSize);
        $where = " WHERE kp.da_xoa=:dx ";
        $params = [':dx' => $daXoa];
        if ($search !== '') {
            $where .= " AND (kp.ma_khoa LIKE :s1 OR kp.ten_khoa LIKE :s2 OR kp.chuyen_khoa LIKE :s3) ";
            $kw = "%{$search}%";
            $params[':s1'] = $kw; $params[':s2'] = $kw; $params[':s3'] = $kw;
        }
        if ($loai !== '') {
            $where .= " AND kp.loai_don_vi=:lo ";
            $params[':lo'] = $loai;
        }

        $countSql = "SELECT COUNT(*) FROM DM_KHOA_PHONG kp" . $where;
        $stmt = Database::getConnection()->prepare($countSql);
        $stmt->execute($params);
        $total = (int)$stmt->fetchColumn();

        $sql = self::selectSql() . $where . " ORDER BY kp.id DESC LIMIT {$pageSize} OFFSET {$offset}";
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
        $stmt = Database::getConnection()->prepare("SELECT COUNT(*) FROM DM_KHOA_PHONG WHERE ma_khoa=:m AND da_xoa=0 AND id<>:id");
        $stmt->execute([':m' => $ma, ':id' => $excludeId]);
        return (int)$stmt->fetchColumn() > 0;
    }

    public static function getCombo(string $loai = ''): array
    {
        $key = 'dm_khoa_phong:combo:' . $loai;
        $cached = MemcachedHelper::get($key);
        if ($cached !== null) return $cached;
        $sql = "SELECT id, ma_khoa, ten_khoa, loai_don_vi FROM DM_KHOA_PHONG WHERE da_xoa=0 AND trang_thai=1";
        $params = [];
        if ($loai !== '') { $sql .= " AND loai_don_vi=:lo"; $params[':lo'] = $loai; }
        $sql .= " ORDER BY ten_khoa";
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute($params);
        $data = $stmt->fetchAll();
        MemcachedHelper::set($key, $data, Constants::CACHE_TTL_COMBO);
        return $data;
    }
}
