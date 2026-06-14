<?php
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/../PUBLIC/Entities/DT_KetQuaHocTap_DTO.php';

class DT_KetQuaHocTap_DAL
{
    private static function selectSql(): string
    {
        return "SELECT kq.*,
                       hvl.hoc_vien_id, hvl.khoa_hoc_chuong_trinh_id AS lop_hoc_id,
                       hv.ma_hv, hv.ho_ten, hv.avatar,
                       lop.ma_chuong_trinh AS ma_lop, lop.ten_chuong_trinh AS ten_lop,
                       mh.ma_mon_hoc, mh.ten_mon_hoc
                FROM DT_KET_QUA_HOC_TAP kq
                INNER JOIN DT_HOC_VIEN_LOP hvl ON hvl.id = kq.hoc_vien_lop_id
                INNER JOIN DM_HOC_VIEN hv ON hv.id = hvl.hoc_vien_id
                LEFT JOIN DT_KHOA_HOC_CHUONG_TRINH khct ON khct.id = hvl.khoa_hoc_chuong_trinh_id
                LEFT JOIN DT_CHUONG_TRINH lop ON lop.id = khct.chuong_trinh_id
                LEFT JOIN DT_MON_HOC mh ON mh.id = kq.mon_hoc_id";
    }

    public static function getById(int $id): ?DT_KetQuaHocTap_DTO
    {
        $stmt = Database::getConnection()->prepare(self::selectSql() . " WHERE kq.id=:id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ? Database::hydrate($row, 'DT_KetQuaHocTap_DTO') : null;
    }

    /**
     * Lấy bảng điểm theo lớp: tất cả học viên × tất cả môn của khóa (kể cả chưa có điểm).
     * Trả về dạng LEFT JOIN để UI dễ render grid.
     */
    public static function getByLop(int $lopId): array
    {
        $sql = "SELECT hvl.id AS hoc_vien_lop_id, hvl.hoc_vien_id,
                       hv.ma_hv, hv.ho_ten, hv.avatar,
                       hvl.diem_tong_ket AS diem_lop, hvl.xep_loai AS xep_loai_lop,
                       kq.id AS kq_id, kq.mon_hoc_id,
                       kq.diem_thuong_xuyen, kq.diem_giua_ky, kq.diem_cuoi_ky,
                       kq.diem_tong_ket, kq.xep_loai, kq.dat, kq.nhan_xet
                FROM DT_HOC_VIEN_LOP hvl
                INNER JOIN DM_HOC_VIEN hv ON hv.id = hvl.hoc_vien_id
                LEFT JOIN DT_KET_QUA_HOC_TAP kq
                  ON kq.hoc_vien_lop_id = hvl.id AND kq.da_xoa = 0
                WHERE hvl.khoa_hoc_chuong_trinh_id=:lop AND hvl.da_xoa=0
                ORDER BY hv.ho_ten ASC, kq.mon_hoc_id ASC";
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute([':lop' => $lopId]);
        return $stmt->fetchAll();
    }

    /** Bài học của chương trình (theo ngữ cảnh khct → CTĐT → dt_mon_hoc.chuong_trinh_id) */
    public static function getMonHocByLop(int $khctId): array
    {
        $sql = "SELECT mh.id, mh.ma_mon_hoc, mh.ten_mon_hoc, mh.so_tin_chi
                FROM DT_KHOA_HOC_CHUONG_TRINH khct
                INNER JOIN DT_MON_HOC mh ON mh.chuong_trinh_id = khct.chuong_trinh_id AND mh.da_xoa=0
                WHERE khct.id=:khct AND khct.da_xoa=0
                ORDER BY mh.thu_tu ASC, mh.id ASC";
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute([':khct' => $khctId]);
        return $stmt->fetchAll();
    }

    public static function upsert(DT_KetQuaHocTap_PUBLIC $e, int $userId): int
    {
        // Tìm record hiện có
        $find = Database::getConnection()->prepare(
            "SELECT id FROM DT_KET_QUA_HOC_TAP
             WHERE hoc_vien_lop_id=:h AND (mon_hoc_id <=> :m) AND da_xoa=0 LIMIT 1"
        );
        $find->execute([':h' => $e->hoc_vien_lop_id, ':m' => $e->mon_hoc_id]);
        $existingId = $find->fetchColumn();

        if ($existingId) {
            $sql = "UPDATE DT_KET_QUA_HOC_TAP SET
                      diem_thuong_xuyen=:dtx, diem_giua_ky=:dgk, diem_cuoi_ky=:dck,
                      diem_tong_ket=:dtk, xep_loai=:xl, dat=:dat, nhan_xet=:nx,
                      ngay_cap_nhat=NOW(), nguoi_cap_nhat=:u
                    WHERE id=:id";
            $stmt = Database::getConnection()->prepare($sql);
            $stmt->execute([
                ':dtx' => $e->diem_thuong_xuyen, ':dgk' => $e->diem_giua_ky,
                ':dck' => $e->diem_cuoi_ky, ':dtk' => $e->diem_tong_ket,
                ':xl' => $e->xep_loai, ':dat' => $e->dat, ':nx' => $e->nhan_xet,
                ':u' => $userId, ':id' => (int)$existingId,
            ]);
            return (int)$existingId;
        }

        $sql = "INSERT INTO DT_KET_QUA_HOC_TAP
                (hoc_vien_lop_id, mon_hoc_id,
                 diem_thuong_xuyen, diem_giua_ky, diem_cuoi_ky, diem_tong_ket,
                 xep_loai, dat, nhan_xet,
                 ngay_tao, ngay_cap_nhat, nguoi_tao, nguoi_cap_nhat, da_xoa)
                VALUES (:h, :m, :dtx, :dgk, :dck, :dtk, :xl, :dat, :nx,
                        NOW(), NOW(), :u1, :u2, 0)";
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute([
            ':h' => $e->hoc_vien_lop_id, ':m' => $e->mon_hoc_id,
            ':dtx' => $e->diem_thuong_xuyen, ':dgk' => $e->diem_giua_ky,
            ':dck' => $e->diem_cuoi_ky, ':dtk' => $e->diem_tong_ket,
            ':xl' => $e->xep_loai, ':dat' => $e->dat, ':nx' => $e->nhan_xet,
            ':u1' => $userId, ':u2' => $userId,
        ]);
        return (int)Database::getConnection()->lastInsertId();
    }

    public static function delete(int $id): int
    {
        $stmt = Database::getConnection()->prepare("UPDATE DT_KET_QUA_HOC_TAP SET da_xoa=1 WHERE id=:id");
        $stmt->execute([':id' => $id]);
        return $stmt->rowCount();
    }

    /** Cập nhật điểm tổng kết + xếp loại vào DT_HOC_VIEN_LOP (TB cộng các môn) */
    public static function syncTongKetHvl(int $lopId, int $userId): int
    {
        $sql = "UPDATE DT_HOC_VIEN_LOP hvl
                LEFT JOIN (
                    SELECT hoc_vien_lop_id, ROUND(AVG(diem_tong_ket), 1) AS diem_tb
                    FROM DT_KET_QUA_HOC_TAP
                    WHERE da_xoa=0 AND diem_tong_ket IS NOT NULL
                    GROUP BY hoc_vien_lop_id
                ) sub ON sub.hoc_vien_lop_id = hvl.id
                SET hvl.diem_tong_ket = sub.diem_tb,
                    hvl.xep_loai = CASE
                        WHEN sub.diem_tb IS NULL THEN hvl.xep_loai
                        WHEN sub.diem_tb >= 9.0 THEN 'Xuất sắc'
                        WHEN sub.diem_tb >= 8.0 THEN 'Giỏi'
                        WHEN sub.diem_tb >= 6.5 THEN 'Khá'
                        WHEN sub.diem_tb >= 5.0 THEN 'Trung bình'
                        WHEN sub.diem_tb >= 3.5 THEN 'Yếu'
                        ELSE 'Kém'
                    END,
                    hvl.ngay_cap_nhat=NOW(), hvl.nguoi_cap_nhat=:u
                WHERE hvl.khoa_hoc_chuong_trinh_id=:lop AND hvl.da_xoa=0";
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute([':u' => $userId, ':lop' => $lopId]);
        return $stmt->rowCount();
    }

    public static function statsByLop(int $lopId): array
    {
        $sql = "SELECT
                    COUNT(DISTINCT hvl.id) AS so_hoc_vien,
                    COUNT(DISTINCT kq.id) AS so_ban_ghi,
                    ROUND(AVG(kq.diem_tong_ket),1) AS diem_tb,
                    SUM(CASE WHEN kq.dat=1 THEN 1 ELSE 0 END) AS so_dat,
                    SUM(CASE WHEN kq.dat=0 THEN 1 ELSE 0 END) AS so_khong_dat,
                    SUM(CASE WHEN kq.xep_loai='Xuất sắc' THEN 1 ELSE 0 END) AS xs,
                    SUM(CASE WHEN kq.xep_loai='Giỏi' THEN 1 ELSE 0 END) AS gioi,
                    SUM(CASE WHEN kq.xep_loai='Khá' THEN 1 ELSE 0 END) AS kha,
                    SUM(CASE WHEN kq.xep_loai='Trung bình' THEN 1 ELSE 0 END) AS tb,
                    SUM(CASE WHEN kq.xep_loai IN ('Yếu','Kém') THEN 1 ELSE 0 END) AS yeu
                FROM DT_HOC_VIEN_LOP hvl
                LEFT JOIN DT_KET_QUA_HOC_TAP kq ON kq.hoc_vien_lop_id=hvl.id AND kq.da_xoa=0
                WHERE hvl.khoa_hoc_chuong_trinh_id=:lop AND hvl.da_xoa=0";
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute([':lop' => $lopId]);
        return $stmt->fetch() ?: [];
    }
}
