<?php
require_once __DIR__ . '/../DAL/database.php';

/**
 * DT_BaoCao_BUS - Tổng hợp số liệu cho các báo cáo đào tạo.
 */
class DT_BaoCao_BUS
{
    const MODULE_KEY = 'DT_BaoCao';

    /**
     * Báo cáo theo từng ngữ cảnh (khóa + CTĐT): số HV, đạt/không đạt, điểm TB, chứng chỉ.
     */
    public static function theoKhoaCtdt(int $khoaHocId = 0, string $from = '', string $to = ''): array
    {
        $pdo = Database::getConnection();
        $where = " WHERE khct.da_xoa=0 ";
        $params = [];
        if ($khoaHocId > 0) { $where .= " AND khct.khoa_hoc_id=:kh "; $params[':kh'] = $khoaHocId; }
        // Lọc theo thời gian khóa diễn ra (ngày bắt đầu của cặp khóa+CTĐT)
        if ($from !== '') { $where .= " AND khct.ngay_bat_dau >= :from "; $params[':from'] = $from; }
        if ($to !== '')   { $where .= " AND khct.ngay_bat_dau <= :to "; $params[':to'] = $to; }

        $sql = "SELECT khct.id AS khct_id,
                       kh.ma_khoa_hoc, kh.ten_khoa_hoc,
                       ct.ma_chuong_trinh, ct.ten_chuong_trinh,
                       (SELECT COUNT(*) FROM DT_HOC_VIEN_LOP hvl WHERE hvl.khoa_hoc_chuong_trinh_id=khct.id AND hvl.da_xoa=0) AS so_hv,
                       (SELECT COUNT(*) FROM DT_KET_QUA_HOC_TAP kq
                          JOIN DT_HOC_VIEN_LOP hvl ON hvl.id=kq.hoc_vien_lop_id
                          WHERE hvl.khoa_hoc_chuong_trinh_id=khct.id AND kq.da_xoa=0 AND kq.dat=1) AS so_dat,
                       (SELECT COUNT(*) FROM DT_KET_QUA_HOC_TAP kq
                          JOIN DT_HOC_VIEN_LOP hvl ON hvl.id=kq.hoc_vien_lop_id
                          WHERE hvl.khoa_hoc_chuong_trinh_id=khct.id AND kq.da_xoa=0 AND kq.dat=0) AS so_khong_dat,
                       (SELECT ROUND(AVG(kq.diem_tong_ket),1) FROM DT_KET_QUA_HOC_TAP kq
                          JOIN DT_HOC_VIEN_LOP hvl ON hvl.id=kq.hoc_vien_lop_id
                          WHERE hvl.khoa_hoc_chuong_trinh_id=khct.id AND kq.da_xoa=0 AND kq.diem_tong_ket IS NOT NULL) AS diem_tb,
                       (SELECT COUNT(*) FROM DT_CHUNG_CHI cc WHERE cc.khoa_hoc_chuong_trinh_id=khct.id AND cc.da_xoa=0) AS so_chung_chi
                FROM DT_KHOA_HOC_CHUONG_TRINH khct
                JOIN DT_KHOA_HOC kh ON kh.id=khct.khoa_hoc_id AND kh.da_xoa=0
                JOIN DT_CHUONG_TRINH ct ON ct.id=khct.chuong_trinh_id AND ct.da_xoa=0
                {$where}
                ORDER BY kh.ma_khoa_hoc, ct.thu_tu, ct.id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Danh sách học viên + kết quả của 1 ngữ cảnh (khóa+CTĐT).
     */
    public static function dsHocVienKetQua(int $khctId, string $from = '', string $to = ''): array
    {
        $pdo = Database::getConnection();
        $w = ''; $params = [':khct' => $khctId];
        if ($from !== '') { $w .= " AND hvl.ngay_ghi_danh >= :from "; $params[':from'] = $from; }
        if ($to !== '')   { $w .= " AND hvl.ngay_ghi_danh <= :to "; $params[':to'] = $to; }
        $sql = "SELECT hv.ma_hv, hv.ho_ten, hv.ngay_sinh, hv.gioi_tinh, hv.don_vi_cong_tac,
                       hvl.ngay_ghi_danh,
                       kq.diem_thuong_xuyen, kq.diem_giua_ky, kq.diem_cuoi_ky, kq.diem_tong_ket,
                       kq.xep_loai, kq.dat,
                       (SELECT COUNT(*) FROM DT_DIEM_DANH dd WHERE dd.hoc_vien_lop_id=hvl.id AND dd.da_xoa=0) AS tong_buoi,
                       (SELECT COUNT(*) FROM DT_DIEM_DANH dd WHERE dd.hoc_vien_lop_id=hvl.id AND dd.da_xoa=0 AND dd.trang_thai=1) AS co_mat
                FROM DT_HOC_VIEN_LOP hvl
                JOIN DM_HOC_VIEN hv ON hv.id=hvl.hoc_vien_id
                LEFT JOIN DT_KET_QUA_HOC_TAP kq ON kq.hoc_vien_lop_id=hvl.id AND kq.da_xoa=0
                WHERE hvl.khoa_hoc_chuong_trinh_id=:khct AND hvl.da_xoa=0 {$w}
                ORDER BY hv.ho_ten";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /** Thông tin 1 ngữ cảnh (khóa+CTĐT) để in tiêu đề báo cáo. */
    public static function thongTinKhct(int $khctId): ?array
    {
        $stmt = Database::getConnection()->prepare(
            "SELECT khct.id, kh.ma_khoa_hoc, kh.ten_khoa_hoc, ct.ma_chuong_trinh, ct.ten_chuong_trinh
             FROM DT_KHOA_HOC_CHUONG_TRINH khct
             JOIN DT_KHOA_HOC kh ON kh.id=khct.khoa_hoc_id
             JOIN DT_CHUONG_TRINH ct ON ct.id=khct.chuong_trinh_id
             WHERE khct.id=:id"
        );
        $stmt->execute([':id' => $khctId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * Thống kê tổng. Nếu truyền from/to: các chỉ tiêu có tính thời gian
     * (ghi danh, chứng chỉ, đăng ký, lịch học) lọc trong khoảng; danh mục giữ tổng tồn.
     */
    public static function thongKeTong(string $from = '', string $to = ''): array
    {
        $pdo = Database::getConnection();
        $one = fn($sql) => (int)$pdo->query($sql)->fetchColumn();
        // Điều kiện khoảng theo cột ngày tương ứng
        $rng = function (string $col) use ($from, $to, $pdo): string {
            $c = '';
            if ($from !== '') $c .= " AND {$col} >= " . $pdo->quote($from);
            if ($to !== '')   $c .= " AND {$col} <= " . $pdo->quote($to);
            return $c;
        };
        return [
            'hoc_vien'   => $one("SELECT COUNT(*) FROM DM_HOC_VIEN WHERE da_xoa=0"),
            'khoa_hoc'   => $one("SELECT COUNT(*) FROM DT_KHOA_HOC WHERE da_xoa=0"),
            'ctdt'       => $one("SELECT COUNT(*) FROM DT_CHUONG_TRINH WHERE da_xoa=0"),
            'bai_hoc'    => $one("SELECT COUNT(*) FROM DT_MON_HOC WHERE da_xoa=0"),
            'ghi_danh'   => $one("SELECT COUNT(*) FROM DT_HOC_VIEN_LOP WHERE da_xoa=0" . $rng('ngay_ghi_danh')),
            'chung_chi'  => $one("SELECT COUNT(*) FROM DT_CHUNG_CHI WHERE da_xoa=0" . $rng('ngay_cap')),
            'dang_ky'    => $one("SELECT COUNT(*) FROM DT_DANG_KY_KHOA_HOC WHERE da_xoa=0" . $rng('DATE(ngay_tao)')),
            'lich_hoc'   => $one("SELECT COUNT(*) FROM DT_LICH_HOC WHERE da_xoa=0" . $rng('ngay_hoc')),
        ];
    }
}
