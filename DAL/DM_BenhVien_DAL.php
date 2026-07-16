<?php
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/../PUBLIC/Entities/DM_BenhVien_DTO.php';

class DM_BenhVien_DAL
{
    private static function selectSql(): string
    {
        return "SELECT bv.*,
                       (SELECT COUNT(*) FROM DM_NHAN_VIEN n WHERE n.benh_vien_id = bv.id AND n.da_xoa = 0) AS so_nhan_vien,
                       u1.tai_khoan AS tai_khoan_nguoi_tao,
                       u2.tai_khoan AS tai_khoan_nguoi_cap_nhat
                FROM DM_BENH_VIEN bv
                LEFT JOIN DM_NGUOI_DUNG u1 ON u1.id = bv.nguoi_tao
                LEFT JOIN DM_NGUOI_DUNG u2 ON u2.id = bv.nguoi_cap_nhat";
    }

    public static function insert(DM_BenhVien_PUBLIC $e): int
    {
        $sql = "INSERT INTO DM_BENH_VIEN
                (ma_benh_vien, ten_benh_vien, dia_chi, dien_thoai, email, cap_benh_vien, hang_benh_vien,
                 giam_doc, dien_thoai_giam_doc, la_benh_vien_chinh, ngay_ky_hop_tac, trang_thai,
                 ngay_tao, ngay_cap_nhat, nguoi_tao, nguoi_cap_nhat, da_xoa)
                VALUES (:ma, :ten, :dc, :dt, :em, :cap, :hang, :gd, :dtgd, :chinh, :nkht, :tt, NOW(), NOW(), :u1, :u2, 0)";
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute([
            ':ma' => $e->ma_benh_vien, ':ten' => $e->ten_benh_vien,
            ':dc' => $e->dia_chi, ':dt' => $e->dien_thoai, ':em' => $e->email,
            ':cap' => $e->cap_benh_vien, ':hang' => $e->hang_benh_vien,
            ':gd' => $e->giam_doc, ':dtgd' => $e->dien_thoai_giam_doc,
            ':chinh' => $e->la_benh_vien_chinh, ':nkht' => $e->ngay_ky_hop_tac,
            ':tt' => $e->trang_thai, ':u1' => $e->nguoi_tao, ':u2' => $e->nguoi_tao,
        ]);
        return (int)Database::getConnection()->lastInsertId();
    }

    public static function update(DM_BenhVien_PUBLIC $e): int
    {
        $sql = "UPDATE DM_BENH_VIEN SET
                ma_benh_vien=:ma, ten_benh_vien=:ten, dia_chi=:dc, dien_thoai=:dt, email=:em,
                cap_benh_vien=:cap, hang_benh_vien=:hang, giam_doc=:gd, dien_thoai_giam_doc=:dtgd,
                la_benh_vien_chinh=:chinh, ngay_ky_hop_tac=:nkht, trang_thai=:tt,
                ngay_cap_nhat=NOW(), nguoi_cap_nhat=:u
                WHERE id=:id AND da_xoa=0";
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute([
            ':ma' => $e->ma_benh_vien, ':ten' => $e->ten_benh_vien,
            ':dc' => $e->dia_chi, ':dt' => $e->dien_thoai, ':em' => $e->email,
            ':cap' => $e->cap_benh_vien, ':hang' => $e->hang_benh_vien,
            ':gd' => $e->giam_doc, ':dtgd' => $e->dien_thoai_giam_doc,
            ':chinh' => $e->la_benh_vien_chinh, ':nkht' => $e->ngay_ky_hop_tac,
            ':tt' => $e->trang_thai, ':u' => $e->nguoi_cap_nhat, ':id' => $e->id,
        ]);
        return $stmt->rowCount();
    }

    public static function trash(int $id, int $u): int
    {
        $stmt = Database::getConnection()->prepare("UPDATE DM_BENH_VIEN SET da_xoa=1, ngay_cap_nhat=NOW(), nguoi_cap_nhat=:u WHERE id=:id");
        $stmt->execute([':u' => $u, ':id' => $id]);
        return $stmt->rowCount();
    }

    public static function restore(int $id, int $u): int
    {
        $stmt = Database::getConnection()->prepare("UPDATE DM_BENH_VIEN SET da_xoa=0, ngay_cap_nhat=NOW(), nguoi_cap_nhat=:u WHERE id=:id");
        $stmt->execute([':u' => $u, ':id' => $id]);
        return $stmt->rowCount();
    }

    public static function delete(int $id): int
    {
        $stmt = Database::getConnection()->prepare("DELETE FROM DM_BENH_VIEN WHERE id=:id");
        $stmt->execute([':id' => $id]);
        return $stmt->rowCount();
    }

    public static function getById(int $id): ?DM_BenhVien_DTO
    {
        $stmt = Database::getConnection()->prepare(self::selectSql() . " WHERE bv.id=:id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ? Database::hydrate($row, 'DM_BenhVien_DTO') : null;
    }

    public static function getPaged(int $page, int $pageSize, string $search = '', int $daXoa = 0, string $cap = ''): array
    {
        [$page, $pageSize, $offset] = PaginationHelper::normalize($page, $pageSize);
        $where = " WHERE bv.da_xoa=:dx ";
        $params = [':dx' => $daXoa];
        if ($search !== '') {
            $where .= " AND (bv.ma_benh_vien LIKE :s1 OR bv.ten_benh_vien LIKE :s2 OR bv.dia_chi LIKE :s3) ";
            $kw = "%{$search}%";
            $params[':s1'] = $kw; $params[':s2'] = $kw; $params[':s3'] = $kw;
        }
        if ($cap !== '') {
            $where .= " AND bv.cap_benh_vien=:cap ";
            $params[':cap'] = $cap;
        }

        $stmt = Database::getConnection()->prepare("SELECT COUNT(*) FROM DM_BENH_VIEN bv" . $where);
        $stmt->execute($params);
        $total = (int)$stmt->fetchColumn();

        $sql = self::selectSql() . $where . " ORDER BY bv.la_benh_vien_chinh DESC, bv.id DESC LIMIT {$pageSize} OFFSET {$offset}";
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
        $stmt = Database::getConnection()->prepare("SELECT COUNT(*) FROM DM_BENH_VIEN WHERE ma_benh_vien=:m AND da_xoa=0 AND id<>:id");
        $stmt->execute([':m' => $ma, ':id' => $excludeId]);
        return (int)$stmt->fetchColumn() > 0;
    }

    public static function getCombo(): array
    {
        $key = 'dm_benh_vien:combo';
        $cached = MemcachedHelper::get($key);
        if ($cached !== null) return $cached;
        $sql = "SELECT id, ma_benh_vien, ten_benh_vien FROM DM_BENH_VIEN WHERE da_xoa=0 AND trang_thai=1 ORDER BY la_benh_vien_chinh DESC, ten_benh_vien";
        $data = Database::getConnection()->query($sql)->fetchAll();
        MemcachedHelper::set($key, $data, Constants::CACHE_TTL_COMBO);
        return $data;
    }
}
