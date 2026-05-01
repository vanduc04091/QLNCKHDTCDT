<?php
require_once __DIR__ . '/../DAL/DT_HocVienLop_DAL.php';
require_once __DIR__ . '/../DAL/DT_LopHoc_DAL.php';

class DT_HocVienLop_BUS
{
    const MODULE_KEY = 'DT_HocVienLop';

    public static function insert(DT_HocVienLop_PUBLIC $e): array
    {
        if ($e->lop_hoc_id <= 0 || $e->hoc_vien_id <= 0) {
            return ['success' => false, 'message' => 'Thiếu lớp hoặc học viên'];
        }
        if (DT_HocVienLop_DAL::checkExists($e->lop_hoc_id, $e->hoc_vien_id)) {
            return ['success' => false, 'message' => 'Học viên đã có trong lớp này'];
        }
        if (!self::canAddMore($e->lop_hoc_id, 1)) {
            return ['success' => false, 'message' => 'Lớp đã đủ số lượng tối đa'];
        }
        $id = DT_HocVienLop_DAL::insert($e);
        return ['success' => true, 'message' => 'Đã ghi danh', 'data' => ['id' => $id]];
    }

    public static function bulkAdd(int $lopId, array $hocVienIds, int $userId): array
    {
        $ids = array_values(array_unique(array_filter(array_map('intval', $hocVienIds))));
        if (!$ids) return ['success' => false, 'message' => 'Chưa chọn học viên nào'];
        $lop = DT_LopHoc_DAL::getById($lopId);
        if (!$lop) return ['success' => false, 'message' => 'Không tìm thấy lớp'];
        $dangCo = DT_HocVienLop_DAL::getCountByLop($lopId);
        $conTrong = max(0, $lop->so_luong_toi_da - $dangCo);
        if ($conTrong <= 0) return ['success' => false, 'message' => 'Lớp đã đủ số lượng'];
        if (count($ids) > $conTrong) {
            return ['success' => false, 'message' => "Chỉ còn {$conTrong} chỗ trống (bạn chọn " . count($ids) . ")"];
        }
        $count = DT_HocVienLop_DAL::bulkInsert($lopId, $ids, $userId);
        return ['success' => true, 'message' => "Đã ghi danh {$count} học viên", 'data' => ['count' => $count]];
    }

    public static function update(DT_HocVienLop_PUBLIC $e): array
    {
        if (!$e->id) return ['success' => false, 'message' => 'Thiếu ID'];
        if ($e->diem_tong_ket !== null && ($e->diem_tong_ket < 0 || $e->diem_tong_ket > 10)) {
            return ['success' => false, 'message' => 'Điểm phải trong khoảng 0-10'];
        }
        DT_HocVienLop_DAL::update($e);
        return ['success' => true, 'message' => 'Cập nhật thành công'];
    }

    public static function delete(int $id): array
    {
        DT_HocVienLop_DAL::delete($id);
        return ['success' => true, 'message' => 'Đã xóa khỏi lớp'];
    }

    public static function getById(int $id): ?DT_HocVienLop_DTO { return DT_HocVienLop_DAL::getById($id); }
    public static function getByLop(int $lopId, string $q = ''): array { return DT_HocVienLop_DAL::getByLop($lopId, $q); }
    public static function getByHocVien(int $hocVienId): array { return DT_HocVienLop_DAL::getByHocVien($hocVienId); }

    /** Tổng hợp dữ liệu xem nhanh của 1 học viên cho drawer admin/portal. */
    public static function getOverview(int $hocVienId): array
    {
        $hvls = DT_HocVienLop_DAL::getByHocVien($hocVienId);
        $hvlIds = array_map(fn($r) => (int)$r['id'], $hvls);
        $lopIds = array_unique(array_map(fn($r) => (int)$r['lop_hoc_id'], $hvls));

        $pdo = Database::getConnection();

        // Lịch học (qua các lớp HV đang ghi danh)
        $lichRows = [];
        if ($lopIds) {
            $in = implode(',', array_map('intval', $lopIds));
            $stmt = $pdo->query(
                "SELECT lh.id, lh.lop_hoc_id, lh.ngay_hoc, lh.gio_bat_dau, lh.gio_ket_thuc,
                        lh.buoi_thu, lh.tieu_de, lh.phong_hoc, lh.giang_vien_ngoai,
                        lop.ma_lop, lop.ten_lop,
                        mh.ten_mon_hoc,
                        gv.ho_ten AS ten_giang_vien
                 FROM DT_LICH_HOC lh
                 INNER JOIN DT_LOP_HOC lop ON lop.id = lh.lop_hoc_id
                 LEFT JOIN DT_MON_HOC mh ON mh.id = lh.mon_hoc_id
                 LEFT JOIN DM_GIANG_VIEN gv ON gv.id = lh.giang_vien_id
                 WHERE lh.lop_hoc_id IN ({$in}) AND lh.da_xoa=0
                 ORDER BY lh.ngay_hoc DESC, lh.gio_bat_dau ASC LIMIT 200"
            );
            $lichRows = $stmt->fetchAll();
        }

        // Điểm danh (qua hvl)
        $diemDanhStats = ['tong' => 0, 'co_mat' => 0, 'vang_cp' => 0, 'vang_kp' => 0, 'muon' => 0];
        $diemDanhDetail = [];
        if ($hvlIds) {
            $in = implode(',', array_map('intval', $hvlIds));
            $stmt = $pdo->query(
                "SELECT
                    COUNT(*) AS tong,
                    SUM(CASE WHEN trang_thai=1 THEN 1 ELSE 0 END) AS co_mat,
                    SUM(CASE WHEN trang_thai=3 THEN 1 ELSE 0 END) AS vang_cp,
                    SUM(CASE WHEN trang_thai=0 THEN 1 ELSE 0 END) AS vang_kp,
                    SUM(CASE WHEN trang_thai=2 THEN 1 ELSE 0 END) AS muon
                 FROM DT_DIEM_DANH WHERE hoc_vien_lop_id IN ({$in}) AND da_xoa=0"
            );
            $diemDanhStats = $stmt->fetch() ?: $diemDanhStats;

            $stmt = $pdo->query(
                "SELECT lh.ngay_hoc, dd.trang_thai, dd.ghi_chu, dd.gio_vao,
                        lh.buoi_thu, lh.tieu_de, mh.ten_mon_hoc, lop.ma_lop
                 FROM DT_DIEM_DANH dd
                 LEFT JOIN DT_LICH_HOC lh ON lh.id = dd.lich_hoc_id
                 LEFT JOIN DT_MON_HOC mh ON mh.id = lh.mon_hoc_id
                 LEFT JOIN DT_LOP_HOC lop ON lop.id = lh.lop_hoc_id
                 WHERE dd.hoc_vien_lop_id IN ({$in}) AND dd.da_xoa=0
                 ORDER BY lh.ngay_hoc DESC LIMIT 100"
            );
            $diemDanhDetail = $stmt->fetchAll();
        }

        // Bảng điểm
        $ketQuaRows = [];
        if ($hvlIds) {
            $in = implode(',', array_map('intval', $hvlIds));
            $stmt = $pdo->query(
                "SELECT kq.diem_thuong_xuyen, kq.diem_giua_ky, kq.diem_cuoi_ky, kq.diem_tong_ket,
                        kq.xep_loai, kq.dat, kq.nhan_xet,
                        mh.ma_mon_hoc, mh.ten_mon_hoc,
                        lop.ma_lop, lop.ten_lop
                 FROM DT_KET_QUA_HOC_TAP kq
                 LEFT JOIN DT_MON_HOC mh ON mh.id = kq.mon_hoc_id
                 LEFT JOIN DT_HOC_VIEN_LOP hvl ON hvl.id = kq.hoc_vien_lop_id
                 LEFT JOIN DT_LOP_HOC lop ON lop.id = hvl.lop_hoc_id
                 WHERE kq.hoc_vien_lop_id IN ({$in}) AND kq.da_xoa=0
                 ORDER BY lop.ma_lop, mh.ten_mon_hoc"
            );
            $ketQuaRows = $stmt->fetchAll();
        }

        // Khóa học (distinct qua các lớp đã ghi danh) + môn học của khóa
        $khoaHoc = [];
        if ($lopIds) {
            $in = implode(',', array_map('intval', $lopIds));
            $stmt = $pdo->query(
                "SELECT DISTINCT kh.id, kh.ma_khoa_hoc, kh.ten_khoa_hoc,
                        kh.tong_so_tiet, kh.so_tin_chi
                 FROM DT_KHOA_HOC kh
                 INNER JOIN DT_LOP_HOC lop ON lop.khoa_hoc_id = kh.id
                 WHERE lop.id IN ({$in}) AND kh.da_xoa=0
                 ORDER BY kh.ten_khoa_hoc"
            );
            $khoaHoc = $stmt->fetchAll();
        }

        // Môn học (distinct qua các khóa)
        $monHoc = [];
        if ($khoaHoc) {
            $khIds = implode(',', array_map(fn($k) => (int)$k['id'], $khoaHoc));
            $stmt = $pdo->query(
                "SELECT DISTINCT mh.id, mh.ma_mon_hoc, mh.ten_mon_hoc, mh.tong_so_tiet, mh.so_tin_chi,
                        km.bat_buoc, kh.ten_khoa_hoc
                 FROM DT_KHOA_HOC_MON_HOC km
                 INNER JOIN DT_MON_HOC mh ON mh.id = km.mon_hoc_id
                 INNER JOIN DT_KHOA_HOC kh ON kh.id = km.khoa_hoc_id
                 WHERE km.khoa_hoc_id IN ({$khIds}) AND km.da_xoa=0 AND mh.da_xoa=0
                 ORDER BY kh.ten_khoa_hoc, km.thu_tu, mh.ten_mon_hoc"
            );
            $monHoc = $stmt->fetchAll();
        }

        return [
            'lich_hoc'         => $lichRows,
            'diem_danh_stats'  => $diemDanhStats,
            'diem_danh_detail' => $diemDanhDetail,
            'ket_qua'          => $ketQuaRows,
            'khoa_hoc'         => $khoaHoc,
            'mon_hoc'          => $monHoc,
        ];
    }
    public static function getHocVienChuaGhiDanh(int $lopId, string $q = '', int $limit = 50): array
    {
        return DT_HocVienLop_DAL::getHocVienChuaGhiDanh($lopId, $q, $limit);
    }

    private static function canAddMore(int $lopId, int $count): bool
    {
        $lop = DT_LopHoc_DAL::getById($lopId);
        if (!$lop) return false;
        $dangCo = DT_HocVienLop_DAL::getCountByLop($lopId);
        return ($dangCo + $count) <= $lop->so_luong_toi_da;
    }
}
