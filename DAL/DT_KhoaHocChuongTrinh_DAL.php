<?php
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/../PUBLIC/Entities/DT_KhoaHocChuongTrinh_PUBLIC.php';

/**
 * Bảng nối N:N Khóa học <-> Chương trình đào tạo (dt_khoa_hoc_chuong_trinh).
 * Mỗi dòng (id) là "ngữ cảnh học vụ" mà các bảng lịch/ghi danh/chứng chỉ... trỏ vào.
 */
class DT_KhoaHocChuongTrinh_DAL
{
    /** Combo "Khóa — CTĐT" cho các select học vụ. value = khct.id */
    public static function getCombo(): array
    {
        $stmt = Database::getConnection()->query(
            "SELECT khct.id,
                    kh.ma_khoa_hoc, kh.ten_khoa_hoc,
                    ct.ma_chuong_trinh, ct.ten_chuong_trinh,
                    CONCAT(kh.ma_khoa_hoc, ' - ', kh.ten_khoa_hoc, '  |  ', ct.ma_chuong_trinh, ' - ', ct.ten_chuong_trinh) AS label
             FROM DT_KHOA_HOC_CHUONG_TRINH khct
             JOIN DT_KHOA_HOC kh ON kh.id = khct.khoa_hoc_id AND kh.da_xoa=0
             JOIN DT_CHUONG_TRINH ct ON ct.id = khct.chuong_trinh_id AND ct.da_xoa=0
             WHERE khct.da_xoa=0
             ORDER BY kh.ten_khoa_hoc, ct.thu_tu ASC, ct.id ASC"
        );
        return $stmt->fetchAll();
    }

    /** Danh sách khóa học đã gắn vào 1 CTĐT (kèm thông tin học vụ của từng cặp). */
    public static function getByChuongTrinh(int $chuongTrinhId): array
    {
        $stmt = Database::getConnection()->prepare(
            "SELECT khct.id, khct.khoa_hoc_id, kh.ma_khoa_hoc, kh.ten_khoa_hoc,
                    khct.ngay_bat_dau, khct.ngay_ket_thuc, khct.dia_diem,
                    khct.giao_vien_id, khct.giao_vien_ngoai, khct.trang_thai,
                    gv.ho_ten AS ten_giao_vien
             FROM DT_KHOA_HOC_CHUONG_TRINH khct
             JOIN DT_KHOA_HOC kh ON kh.id = khct.khoa_hoc_id AND kh.da_xoa=0
             LEFT JOIN DM_NHAN_VIEN gv ON gv.id = khct.giao_vien_id
             WHERE khct.chuong_trinh_id=:ct AND khct.da_xoa=0
             ORDER BY kh.ten_khoa_hoc"
        );
        $stmt->execute([':ct' => $chuongTrinhId]);
        return $stmt->fetchAll();
    }

    /** Danh sách CTĐT đã gắn vào 1 khóa học. */
    public static function getByKhoaHoc(int $khoaHocId): array
    {
        $stmt = Database::getConnection()->prepare(
            "SELECT khct.id, khct.chuong_trinh_id, ct.ma_chuong_trinh, ct.ten_chuong_trinh,
                    khct.trang_thai AS ct_trang_thai, ct.thoi_luong,
                    khct.ngay_bat_dau, khct.ngay_ket_thuc, khct.dia_diem
             FROM DT_KHOA_HOC_CHUONG_TRINH khct
             JOIN DT_CHUONG_TRINH ct ON ct.id = khct.chuong_trinh_id AND ct.da_xoa=0
             WHERE khct.khoa_hoc_id=:kh AND khct.da_xoa=0
             ORDER BY ct.thu_tu ASC, ct.id ASC"
        );
        $stmt->execute([':kh' => $khoaHocId]);
        return $stmt->fetchAll();
    }

    public static function getById(int $id): ?array
    {
        $stmt = Database::getConnection()->prepare("SELECT * FROM DT_KHOA_HOC_CHUONG_TRINH WHERE id=:id AND da_xoa=0");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function exists(int $khoaHocId, int $chuongTrinhId): bool
    {
        $stmt = Database::getConnection()->prepare(
            "SELECT COUNT(*) FROM DT_KHOA_HOC_CHUONG_TRINH WHERE khoa_hoc_id=:kh AND chuong_trinh_id=:ct AND da_xoa=0"
        );
        $stmt->execute([':kh' => $khoaHocId, ':ct' => $chuongTrinhId]);
        return (int)$stmt->fetchColumn() > 0;
    }

    /** Lấy id ngữ cảnh (tạo mới nếu chưa có). */
    public static function findOrCreate(int $khoaHocId, int $chuongTrinhId, int $u): int
    {
        $stmt = Database::getConnection()->prepare(
            "SELECT id FROM DT_KHOA_HOC_CHUONG_TRINH WHERE khoa_hoc_id=:kh AND chuong_trinh_id=:ct AND da_xoa=0"
        );
        $stmt->execute([':kh' => $khoaHocId, ':ct' => $chuongTrinhId]);
        $id = $stmt->fetchColumn();
        if ($id) return (int)$id;
        return self::insert($khoaHocId, $chuongTrinhId, $u);
    }

    public static function insert(int $khoaHocId, int $chuongTrinhId, int $u, array $info = []): int
    {
        $stmt = Database::getConnection()->prepare(
            "INSERT INTO DT_KHOA_HOC_CHUONG_TRINH
                (khoa_hoc_id, chuong_trinh_id, ngay_bat_dau, ngay_ket_thuc, dia_diem,
                 giao_vien_id, giao_vien_ngoai, trang_thai,
                 ngay_tao, ngay_cap_nhat, nguoi_tao, nguoi_cap_nhat, da_xoa)
             VALUES (:kh, :ct, :nbd, :nkt, :dd, :gv, :gvn, :tt, NOW(), NOW(), :u1, :u2, 0)"
        );
        $stmt->execute([
            ':kh' => $khoaHocId, ':ct' => $chuongTrinhId,
            ':nbd' => $info['ngay_bat_dau'] ?? null, ':nkt' => $info['ngay_ket_thuc'] ?? null,
            ':dd' => $info['dia_diem'] ?? null,
            ':gv' => $info['giao_vien_id'] ?? null, ':gvn' => $info['giao_vien_ngoai'] ?? null,
            ':tt' => $info['trang_thai'] ?? 0,
            ':u1' => $u, ':u2' => $u,
        ]);
        return (int)Database::getConnection()->lastInsertId();
    }

    /** Cập nhật thông tin học vụ của 1 cặp (khóa+CTĐT). */
    public static function updateInfo(int $id, array $info, int $u): int
    {
        $stmt = Database::getConnection()->prepare(
            "UPDATE DT_KHOA_HOC_CHUONG_TRINH SET
                ngay_bat_dau=:nbd, ngay_ket_thuc=:nkt, dia_diem=:dd,
                giao_vien_id=:gv, giao_vien_ngoai=:gvn, trang_thai=:tt,
                ngay_cap_nhat=NOW(), nguoi_cap_nhat=:u
             WHERE id=:id AND da_xoa=0"
        );
        $stmt->execute([
            ':nbd' => $info['ngay_bat_dau'] ?? null, ':nkt' => $info['ngay_ket_thuc'] ?? null,
            ':dd' => $info['dia_diem'] ?? null,
            ':gv' => $info['giao_vien_id'] ?? null, ':gvn' => $info['giao_vien_ngoai'] ?? null,
            ':tt' => $info['trang_thai'] ?? 0,
            ':u' => $u, ':id' => $id,
        ]);
        return $stmt->rowCount();
    }

    /** Số bản ghi học vụ tham chiếu ngữ cảnh này (để chặn gỡ khi đang dùng). */
    public static function countReferences(int $khctId): int
    {
        $pdo = Database::getConnection();
        $tables = ['DT_HOC_VIEN_LOP', 'DT_LICH_HOC', 'DT_PHAN_CONG_GIANG_VIEN', 'DT_BAI_KIEM_TRA', 'DT_CHUNG_CHI', 'DT_TAI_LIEU', 'DT_DANG_KY_KHOA_HOC'];
        $total = 0;
        foreach ($tables as $t) {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM {$t} WHERE khoa_hoc_chuong_trinh_id=:id AND da_xoa=0");
            $stmt->execute([':id' => $khctId]);
            $total += (int)$stmt->fetchColumn();
        }
        return $total;
    }

    public static function softDelete(int $id, int $u): int
    {
        $stmt = Database::getConnection()->prepare(
            "UPDATE DT_KHOA_HOC_CHUONG_TRINH SET da_xoa=1, ngay_cap_nhat=NOW(), nguoi_cap_nhat=:u WHERE id=:id"
        );
        $stmt->execute([':u' => $u, ':id' => $id]);
        return $stmt->rowCount();
    }
}
