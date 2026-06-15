<?php
require_once __DIR__ . '/../DAL/DT_HocVienLop_DAL.php';

class DT_HocVienLop_BUS
{
    const MODULE_KEY = 'DT_HocVienLop';

    public static function insert(DT_HocVienLop_PUBLIC $e): array
    {
        if ($e->khoa_hoc_chuong_trinh_id <= 0 || $e->hoc_vien_id <= 0) {
            return ['success' => false, 'message' => 'Thiếu khóa/chương trình hoặc học viên'];
        }
        if (DT_HocVienLop_DAL::checkExists($e->khoa_hoc_chuong_trinh_id, $e->hoc_vien_id)) {
            return ['success' => false, 'message' => 'Học viên đã có trong chương trình này'];
        }
        if (!self::canAddMore($e->khoa_hoc_chuong_trinh_id, 1)) {
            return ['success' => false, 'message' => 'Chương trình đã đủ số lượng tối đa'];
        }
        $id = DT_HocVienLop_DAL::insert($e);
        return ['success' => true, 'message' => 'Đã ghi danh', 'data' => ['id' => $id]];
    }

    public static function bulkAdd(int $khctId, array $hocVienIds, int $userId): array
    {
        $ids = array_values(array_unique(array_filter(array_map('intval', $hocVienIds))));
        if (!$ids) return ['success' => false, 'message' => 'Chưa chọn học viên nào'];
        $max = DT_HocVienLop_DAL::getSoLuongToiDaByKhct($khctId);
        if ($max === null) return ['success' => false, 'message' => 'Không tìm thấy chương trình'];
        $dangCo = DT_HocVienLop_DAL::getCountByKhct($khctId);
        $conTrong = max(0, $max - $dangCo);
        if ($conTrong <= 0) return ['success' => false, 'message' => 'Chương trình đã đủ số lượng'];
        if (count($ids) > $conTrong) {
            return ['success' => false, 'message' => "Chỉ còn {$conTrong} chỗ trống (bạn chọn " . count($ids) . ")"];
        }
        $count = DT_HocVienLop_DAL::bulkInsert($khctId, $ids, $userId);
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
        return ['success' => true, 'message' => 'Đã xóa khỏi chương trình'];
    }

    /** Cập nhật ngày ghi danh + khoảng ngày học của 1 ghi danh. */
    public static function updateNgay(int $id, ?string $ngayGhiDanh, ?string $nbd, ?string $nkt, int $userId): array
    {
        if (!$id) return ['success' => false, 'message' => 'Thiếu ID'];
        if ($nbd && $nkt && $nbd > $nkt) {
            return ['success' => false, 'message' => 'Ngày học "từ" phải trước "đến"'];
        }
        DT_HocVienLop_DAL::updateNgay($id, $ngayGhiDanh, $nbd, $nkt, $userId);
        return ['success' => true, 'message' => 'Đã cập nhật ngày'];
    }

    public static function getById(int $id): ?DT_HocVienLop_DTO { return DT_HocVienLop_DAL::getById($id); }
    public static function getByKhct(int $khctId, string $q = ''): array { return DT_HocVienLop_DAL::getByKhct($khctId, $q); }
    public static function getByHocVien(int $hocVienId): array { return DT_HocVienLop_DAL::getByHocVien($hocVienId); }

    /** Tổng hợp dữ liệu xem nhanh của 1 học viên cho drawer admin/portal. */
    public static function getOverview(int $hocVienId): array
    {
        $hvls = DT_HocVienLop_DAL::getByHocVien($hocVienId);
        $hvlIds = array_map(fn($r) => (int)$r['id'], $hvls);
        $khctIds = array_unique(array_map(fn($r) => (int)$r['khoa_hoc_chuong_trinh_id'], $hvls));

        $pdo = Database::getConnection();

        // Lịch học (qua các ngữ cảnh khct HV đang ghi danh)
        $lichRows = [];
        if ($khctIds) {
            $in = implode(',', array_map('intval', $khctIds));
            $stmt = $pdo->query(
                "SELECT lh.id, lh.khoa_hoc_chuong_trinh_id, lh.ngay_hoc, lh.gio_bat_dau, lh.gio_ket_thuc,
                        lh.buoi_thu, lh.tieu_de, lh.phong_hoc, lh.giang_vien_ngoai,
                        ct.ma_chuong_trinh AS ma_lop, ct.ten_chuong_trinh AS ten_lop,
                        kh.ma_khoa_hoc, kh.ten_khoa_hoc,
                        mh.ten_mon_hoc,
                        gv.ho_ten AS ten_giang_vien
                 FROM DT_LICH_HOC lh
                 INNER JOIN DT_KHOA_HOC_CHUONG_TRINH khct ON khct.id = lh.khoa_hoc_chuong_trinh_id
                 INNER JOIN DT_CHUONG_TRINH ct ON ct.id = khct.chuong_trinh_id
                 LEFT JOIN DT_KHOA_HOC kh ON kh.id = khct.khoa_hoc_id
                 LEFT JOIN DT_MON_HOC mh ON mh.id = lh.mon_hoc_id
                 LEFT JOIN DM_NHAN_VIEN gv ON gv.id = lh.giang_vien_id
                 WHERE lh.khoa_hoc_chuong_trinh_id IN ({$in}) AND lh.da_xoa=0
                 ORDER BY kh.ma_khoa_hoc, ct.thu_tu, lh.ngay_hoc DESC, lh.gio_bat_dau ASC LIMIT 200"
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
                        lh.buoi_thu, lh.tieu_de, mh.ten_mon_hoc,
                        ct.ma_chuong_trinh AS ma_lop, ct.ten_chuong_trinh AS ten_lop,
                        kh.ma_khoa_hoc, kh.ten_khoa_hoc
                 FROM DT_DIEM_DANH dd
                 LEFT JOIN DT_LICH_HOC lh ON lh.id = dd.lich_hoc_id
                 LEFT JOIN DT_MON_HOC mh ON mh.id = lh.mon_hoc_id
                 LEFT JOIN DT_KHOA_HOC_CHUONG_TRINH khct ON khct.id = lh.khoa_hoc_chuong_trinh_id
                 LEFT JOIN DT_CHUONG_TRINH ct ON ct.id = khct.chuong_trinh_id
                 LEFT JOIN DT_KHOA_HOC kh ON kh.id = khct.khoa_hoc_id
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
                        ct.ma_chuong_trinh AS ma_lop, ct.ten_chuong_trinh AS ten_lop,
                        kh.ma_khoa_hoc, kh.ten_khoa_hoc
                 FROM DT_KET_QUA_HOC_TAP kq
                 LEFT JOIN DT_HOC_VIEN_LOP hvl ON hvl.id = kq.hoc_vien_lop_id
                 LEFT JOIN DT_KHOA_HOC_CHUONG_TRINH khct ON khct.id = hvl.khoa_hoc_chuong_trinh_id
                 LEFT JOIN DT_CHUONG_TRINH ct ON ct.id = khct.chuong_trinh_id
                 LEFT JOIN DT_KHOA_HOC kh ON kh.id = khct.khoa_hoc_id
                 WHERE kq.hoc_vien_lop_id IN ({$in}) AND kq.da_xoa=0
                 ORDER BY kh.ma_khoa_hoc, ct.thu_tu"
            );
            $ketQuaRows = $stmt->fetchAll();
        }

        // Khóa học (đúng ngữ cảnh ghi danh: khóa của từng khct)
        $khoaHoc = [];
        if ($khctIds) {
            $in = implode(',', array_map('intval', $khctIds));
            $stmt = $pdo->query(
                "SELECT DISTINCT kh.id, kh.ma_khoa_hoc, kh.ten_khoa_hoc,
                        kh.ngay_bat_dau, kh.ngay_ket_thuc
                 FROM DT_KHOA_HOC kh
                 INNER JOIN DT_KHOA_HOC_CHUONG_TRINH khct ON khct.khoa_hoc_id = kh.id
                 WHERE khct.id IN ({$in}) AND kh.da_xoa=0
                 ORDER BY kh.ten_khoa_hoc"
            );
            $khoaHoc = $stmt->fetchAll();
        }

        // Môn học (theo CTĐT của các ngữ cảnh ghi danh)
        $monHoc = [];
        if ($khctIds) {
            $in = implode(',', array_map('intval', $khctIds));
            $stmt = $pdo->query(
                "SELECT DISTINCT mh.id, mh.ma_mon_hoc, mh.ten_mon_hoc, mh.thu_tu, mh.tong_so_tiet, mh.so_tin_chi,
                        ct.ma_chuong_trinh, ct.ten_chuong_trinh
                 FROM DT_KHOA_HOC_CHUONG_TRINH khct
                 INNER JOIN DT_CHUONG_TRINH ct ON ct.id = khct.chuong_trinh_id
                 INNER JOIN DT_MON_HOC mh ON mh.chuong_trinh_id = ct.id AND mh.da_xoa=0
                 WHERE khct.id IN ({$in})
                 ORDER BY ct.thu_tu, mh.thu_tu, mh.id"
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

    public static function getHocVienChuaGhiDanh(int $khctId, string $q = '', int $limit = 50): array
    {
        return DT_HocVienLop_DAL::getHocVienChuaGhiDanh($khctId, $q, $limit);
    }

    private static function canAddMore(int $khctId, int $count): bool
    {
        $max = DT_HocVienLop_DAL::getSoLuongToiDaByKhct($khctId);
        if ($max === null) return false;
        $dangCo = DT_HocVienLop_DAL::getCountByKhct($khctId);
        return ($dangCo + $count) <= $max;
    }
}
