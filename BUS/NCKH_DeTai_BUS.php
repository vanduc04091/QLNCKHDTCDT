<?php
require_once __DIR__ . '/../DAL/NCKH_DeTai_DAL.php';
require_once __DIR__ . '/../DAL/NCKH_ThanhVien_DAL.php';
require_once __DIR__ . '/../DAL/NCKH_TienDo_DAL.php';
require_once __DIR__ . '/../DAL/NCKH_TaiLieu_DAL.php';
require_once __DIR__ . '/../DAL/NCKH_HoiDong_DAL.php';
require_once __DIR__ . '/../DAL/DM_NhatKyHeThong_DAL.php';

class NCKH_DeTai_BUS
{
    const MODULE_KEY = 'NCKH_DeTai';

    public static function insert(NCKH_DeTai_PUBLIC $e): array
    {
        $e->ma_de_tai = trim($e->ma_de_tai);
        $e->ten_de_tai = trim($e->ten_de_tai);
        if ($e->ma_de_tai === '' || $e->ten_de_tai === '') return ['success' => false, 'message' => 'Mã và tên đề tài không được để trống'];
        if ($e->nam < 2000 || $e->nam > 2100) return ['success' => false, 'message' => 'Năm không hợp lệ'];
        if ($e->cap_do_id <= 0) return ['success' => false, 'message' => 'Vui lòng chọn cấp độ'];
        if ($e->the_loai_id <= 0) return ['success' => false, 'message' => 'Vui lòng chọn thể loại'];
        if ($e->chu_nhiem_id <= 0) return ['success' => false, 'message' => 'Vui lòng chọn chủ nhiệm'];
        if (NCKH_DeTai_DAL::checkMaExists($e->ma_de_tai)) return ['success' => false, 'message' => 'Mã đề tài đã tồn tại'];

        $id = NCKH_DeTai_DAL::insert($e);
        DM_NhatKyHeThong_DAL::log($e->nguoi_tao ?? 0, self::MODULE_KEY, "Thêm đề tài: {$e->ten_de_tai}", 'NCKH_DE_TAI', $id);
        return ['success' => true, 'message' => 'Thêm đề tài thành công', 'data' => ['id' => $id]];
    }

    public static function update(NCKH_DeTai_PUBLIC $e): array
    {
        if (!$e->id) return ['success' => false, 'message' => 'Thiếu ID'];
        if ($e->nam < 2000 || $e->nam > 2100) return ['success' => false, 'message' => 'Năm không hợp lệ'];
        if ($e->cap_do_id <= 0) return ['success' => false, 'message' => 'Vui lòng chọn cấp độ'];
        if ($e->the_loai_id <= 0) return ['success' => false, 'message' => 'Vui lòng chọn thể loại'];
        if ($e->chu_nhiem_id <= 0) return ['success' => false, 'message' => 'Vui lòng chọn chủ nhiệm'];
        if (NCKH_DeTai_DAL::checkMaExists($e->ma_de_tai, $e->id)) return ['success' => false, 'message' => 'Mã đề tài đã tồn tại'];

        NCKH_DeTai_DAL::update($e);
        DM_NhatKyHeThong_DAL::log($e->nguoi_cap_nhat ?? 0, self::MODULE_KEY, "Cập nhật đề tài: {$e->ten_de_tai}", 'NCKH_DE_TAI', $e->id);
        return ['success' => true, 'message' => 'Cập nhật thành công'];
    }

    public static function trash(int $id, int $u): array
    {
        NCKH_DeTai_DAL::trash($id, $u);
        DM_NhatKyHeThong_DAL::log($u, self::MODULE_KEY, "Xóa mềm đề tài #{$id}", 'NCKH_DE_TAI', $id);
        return ['success' => true, 'message' => 'Đã chuyển vào thùng rác'];
    }

    public static function restore(int $id, int $u): array
    {
        NCKH_DeTai_DAL::restore($id, $u);
        return ['success' => true, 'message' => 'Đã khôi phục'];
    }

    public static function delete(int $id): array
    {
        NCKH_DeTai_DAL::delete($id);
        return ['success' => true, 'message' => 'Đã xóa vĩnh viễn'];
    }

    public static function getById(int $id): ?NCKH_DeTai_DTO { return NCKH_DeTai_DAL::getById($id); }

    public static function getPaged(int $page, int $pageSize, array $filters = [], int $daXoa = 0): array
    {
        return NCKH_DeTai_DAL::getPaged($page, $pageSize, $filters, $daXoa);
    }

    /** Tổng hợp 1 đề tài cho drawer */
    public static function getDetail(int $id): array
    {
        $deTai = NCKH_DeTai_DAL::getById($id);
        if (!$deTai) return ['success' => false, 'message' => 'Không tìm thấy đề tài'];
        return [
            'success' => true,
            'data' => [
                'de_tai'     => $deTai,
                'thanh_vien' => NCKH_ThanhVien_DAL::getByDeTai($id),
                'hoi_dong'   => NCKH_HoiDong_DAL::getByDeTai($id),
                'tien_do'    => NCKH_TienDo_DAL::getByDeTai($id),
                'tai_lieu'   => NCKH_TaiLieu_DAL::getByDeTai($id),
                'phan_tram'  => NCKH_TienDo_DAL::getLatestPercent($id),
            ],
        ];
    }

    /** Map enum xếp loại sang text (6 mức) */
    public static function xepLoaiText(?string $code): string
    {
        return [
            'XuatSac'      => 'Xuất sắc',
            'Gioi'         => 'Giỏi',
            'Kha'          => 'Khá',
            'TrungBinhKha' => 'Trung bình khá',
            'Dat'          => 'Đạt',
            'KhongDat'     => 'Không đạt',
        ][$code ?? ''] ?? '';
    }

    public static function trangThaiText(int $tt): string
    {
        return [0 => 'Đề xuất', 1 => 'Đang thực hiện', 2 => 'Hoàn thành', 3 => 'Tạm dừng', 4 => 'Hủy'][$tt] ?? '';
    }

    public static function trangThaiDuyetText(string $code): string
    {
        return [
            'Nhap'     => 'Nháp',
            'ChoDuyet' => 'Chờ duyệt',
            'DaDuyet'  => 'Đã duyệt',
            'TuChoi'   => 'Từ chối',
        ][$code] ?? $code;
    }

    /** Nhân viên có sửa được đề tài không?
     *  - phải là nguoi_tao
     *  - trang_thai_duyet phải là Nhap hoặc TuChoi
     */
    public static function canEditByOwner(int $deTaiId, int $userId): bool
    {
        $row = Database::getConnection()->prepare(
            "SELECT nguoi_tao, trang_thai_duyet FROM NCKH_DE_TAI WHERE id=:id AND da_xoa=0"
        );
        $row->execute([':id' => $deTaiId]);
        $r = $row->fetch();
        if (!$r) return false;
        return (int)$r['nguoi_tao'] === $userId
            && in_array($r['trang_thai_duyet'], ['Nhap', 'TuChoi'], true);
    }

    /** Nhân viên gửi duyệt */
    public static function submitForReview(int $deTaiId, int $userId): array
    {
        if (!self::canEditByOwner($deTaiId, $userId)) {
            return ['success' => false, 'message' => 'Đề tài không tồn tại hoặc bạn không có quyền'];
        }
        // Validate trước khi gửi: tối thiểu phải có chủ nhiệm + tên + cấp + thể loại + năm
        $dt = NCKH_DeTai_DAL::getById($deTaiId);
        $missing = [];
        if (!$dt->ten_de_tai) $missing[] = 'Tên đề tài';
        if (!$dt->cap_do_id) $missing[] = 'Cấp độ';
        if (!$dt->the_loai_id) $missing[] = 'Thể loại';
        if (!$dt->chu_nhiem_id) $missing[] = 'Chủ nhiệm';
        if (!$dt->nam) $missing[] = 'Năm';
        if ($missing) return ['success' => false, 'message' => 'Còn thiếu: ' . implode(', ', $missing)];

        $rc = NCKH_DeTai_DAL::setSubmitted($deTaiId, $userId);
        if ($rc === 0) return ['success' => false, 'message' => 'Không gửi được — kiểm tra trạng thái hiện tại'];
        DM_NhatKyHeThong_DAL::log($userId, self::MODULE_KEY, "Gửi duyệt đề tài: {$dt->ten_de_tai}", 'NCKH_DE_TAI', $deTaiId);
        return ['success' => true, 'message' => 'Đã gửi đề tài cho quản trị viên duyệt'];
    }

    /** Admin duyệt */
    public static function approveSubmission(int $deTaiId, int $adminId): array
    {
        $rc = NCKH_DeTai_DAL::setApproved($deTaiId, $adminId);
        if ($rc === 0) return ['success' => false, 'message' => 'Đề tài không ở trạng thái Chờ duyệt'];
        $dt = NCKH_DeTai_DAL::getById($deTaiId);
        DM_NhatKyHeThong_DAL::log($adminId, self::MODULE_KEY, "Duyệt đề tài: {$dt->ten_de_tai}", 'NCKH_DE_TAI', $deTaiId);
        return ['success' => true, 'message' => 'Đã duyệt đề tài'];
    }

    /** Admin từ chối */
    public static function rejectSubmission(int $deTaiId, int $adminId, string $lyDo): array
    {
        $lyDo = trim($lyDo);
        if ($lyDo === '') return ['success' => false, 'message' => 'Vui lòng nhập lý do từ chối'];
        $rc = NCKH_DeTai_DAL::setRejected($deTaiId, $adminId, $lyDo);
        if ($rc === 0) return ['success' => false, 'message' => 'Đề tài không ở trạng thái Chờ duyệt'];
        $dt = NCKH_DeTai_DAL::getById($deTaiId);
        DM_NhatKyHeThong_DAL::log($adminId, self::MODULE_KEY, "Từ chối đề tài: {$dt->ten_de_tai}. Lý do: {$lyDo}", 'NCKH_DE_TAI', $deTaiId);
        return ['success' => true, 'message' => 'Đã từ chối đề tài'];
    }

    public static function countPending(): int { return NCKH_DeTai_DAL::countPending(); }
}
