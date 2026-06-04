<?php
/**
 * PhanQuyenHelper - Kiểm tra quyền truy cập theo nhóm tài khoản & form
 */
class PhanQuyenHelper
{
    const QUYEN_XEM = 'quyen_xem';
    const QUYEN_THEM = 'quyen_them';
    const QUYEN_SUA = 'quyen_sua';
    const QUYEN_XOA = 'quyen_xoa';
    const QUYEN_DUYET = 'quyen_duyet';

    /**
     * Lấy ma trận quyền của 1 nhóm tài khoản.
     * Return: [ modules_tuong_ung => ['quyen_xem'=>1,...] ]
     */
    public static function getMatrixByNhom(int $nhomTaiKhoanId): array
    {
        if ($nhomTaiKhoanId <= 0) return [];
        $key = "phan_quyen:nhom:{$nhomTaiKhoanId}";
        $cached = MemcachedHelper::get($key);
        if ($cached !== null) return $cached;

        $sql = "SELECT f.modules_tuong_ung, pq.quyen_xem, pq.quyen_them, pq.quyen_sua, pq.quyen_xoa, pq.quyen_duyet
                FROM DM_PHAN_QUYEN pq
                INNER JOIN DM_DANH_SACH_FORM f ON f.id = pq.danh_sach_form_id
                WHERE pq.nhom_tai_khoan_id = :id AND f.da_xoa = 0";
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute([':id' => $nhomTaiKhoanId]);
        $rows = $stmt->fetchAll();

        $matrix = [];
        foreach ($rows as $r) {
            $matrix[$r['modules_tuong_ung']] = [
                self::QUYEN_XEM => (int)$r['quyen_xem'],
                self::QUYEN_THEM => (int)$r['quyen_them'],
                self::QUYEN_SUA => (int)$r['quyen_sua'],
                self::QUYEN_XOA => (int)$r['quyen_xoa'],
                self::QUYEN_DUYET => (int)$r['quyen_duyet'],
            ];
        }
        MemcachedHelper::set($key, $matrix, Constants::CACHE_TTL_PHAN_QUYEN);
        return $matrix;
    }

    /**
     * Kiểm tra nhóm có cờ la_admin (full quyền) không. Có cache.
     */
    public static function isAdminNhom(int $nhomTaiKhoanId): bool
    {
        if ($nhomTaiKhoanId <= 0) return false;
        $key = "phan_quyen:admin:{$nhomTaiKhoanId}";
        $cached = MemcachedHelper::get($key);
        if ($cached !== null) return (bool)$cached;

        $sql = "SELECT la_admin FROM dm_nhom_tai_khoan WHERE id = :id AND da_xoa = 0";
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute([':id' => $nhomTaiKhoanId]);
        $isAdmin = (int)($stmt->fetchColumn() ?: 0) === 1;
        MemcachedHelper::set($key, $isAdmin ? 1 : 0, Constants::CACHE_TTL_PHAN_QUYEN);
        return $isAdmin;
    }

    public static function hasQuyen(string $moduleKey, string $quyen): bool
    {
        $nhomId = SessionHelper::nhomTaiKhoanId();
        if (self::isAdminNhom($nhomId)) return true;
        $matrix = self::getMatrixByNhom($nhomId);
        return !empty($matrix[$moduleKey][$quyen]);
    }

    public static function requireQuyen(string $moduleKey, string $quyen): void
    {
        if (!self::hasQuyen($moduleKey, $quyen)) {
            ResponseHelper::error('Bạn không có quyền thực hiện thao tác này', 403);
        }
    }

    public static function clearCache(int $nhomTaiKhoanId = 0): void
    {
        if ($nhomTaiKhoanId > 0) {
            MemcachedHelper::delete("phan_quyen:nhom:{$nhomTaiKhoanId}");
        } else {
            MemcachedHelper::deleteByPrefix('phan_quyen:');
        }
    }
}
