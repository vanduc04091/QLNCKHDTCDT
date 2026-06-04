<?php
require_once __DIR__ . '/../DAL/NCKH_DotDangKy_DAL.php';
require_once __DIR__ . '/../DAL/NCKH_DotGiaiDoan_DAL.php';

class NCKH_DotDangKy_BUS
{
    const MODULE_KEY = 'NCKH_DotDangKy';

    const HV_SUBMIT = 'Submit';
    const HV_EDIT   = 'Edit';
    const HV_REVIEW = 'Review';

    public static function hanhViText(string $hv): string
    {
        return [
            self::HV_SUBMIT => 'Đăng ký đề tài',
            self::HV_EDIT   => 'Chỉnh sửa đề tài',
            self::HV_REVIEW => 'Duyệt đề tài',
        ][$hv] ?? $hv;
    }

    public static function getById(int $id): ?NCKH_DotDangKy_DTO
    {
        return NCKH_DotDangKy_DAL::getById($id);
    }

    public static function getPaged(int $page, int $size, array $opts = []): array
    {
        return NCKH_DotDangKy_DAL::getPaged($page, $size, $opts);
    }

    public static function getCombo(bool $onlyActive = true): array
    {
        return NCKH_DotDangKy_DAL::getCombo($onlyActive);
    }

    public static function insert(NCKH_DotDangKy_PUBLIC $e): array
    {
        $v = self::validate($e);
        if (!$v['success']) return $v;
        $id = NCKH_DotDangKy_DAL::insert($e);
        return ['success' => true, 'message' => 'Đã thêm đợt đăng ký', 'data' => ['id' => $id]];
    }

    public static function update(NCKH_DotDangKy_PUBLIC $e): array
    {
        if (!$e->id) return ['success' => false, 'message' => 'Thiếu ID'];
        $v = self::validate($e);
        if (!$v['success']) return $v;
        NCKH_DotDangKy_DAL::update($e);
        return ['success' => true, 'message' => 'Đã cập nhật đợt'];
    }

    public static function trash(int $id, int $u): array
    {
        $dot = NCKH_DotDangKy_DAL::getById($id);
        if (!$dot) return ['success' => false, 'message' => 'Không tìm thấy đợt'];
        if ($dot->so_de_tai > 0) {
            return ['success' => false, 'message' => "Đợt đang có {$dot->so_de_tai} đề tài, không thể xóa"];
        }
        NCKH_DotDangKy_DAL::trash($id, $u);
        return ['success' => true, 'message' => 'Đã chuyển đợt vào thùng rác'];
    }

    private static function validate(NCKH_DotDangKy_PUBLIC $e): array
    {
        $e->ten_dot = trim($e->ten_dot);
        if ($e->ten_dot === '') return ['success' => false, 'message' => 'Tên đợt không được trống'];
        if ($e->nam < 2000 || $e->nam > 2100) return ['success' => false, 'message' => 'Năm không hợp lệ'];
        if (!$e->tu_ngay || !$e->den_ngay) return ['success' => false, 'message' => 'Thiếu thời gian đợt'];
        if (strtotime($e->tu_ngay) > strtotime($e->den_ngay)) {
            return ['success' => false, 'message' => 'Ngày bắt đầu phải trước ngày kết thúc'];
        }
        return ['success' => true];
    }

    // ===== Phase logic =====

    /**
     * Kiểm tra hành vi (Submit|Edit|Review) hiện có giai đoạn mở cho đợt không.
     * Trả ['ok'=>bool, 'message'=>str, 'phase'=>?array]
     */
    public static function checkPhaseOpen(int $dotId, string $hanhVi): array
    {
        if ($dotId <= 0) return ['ok' => false, 'message' => 'Đề tài chưa thuộc đợt nào'];
        $dot = NCKH_DotDangKy_DAL::getById($dotId);
        if (!$dot) return ['ok' => false, 'message' => 'Đợt không tồn tại'];
        if ((int)$dot->trang_thai !== 1) return ['ok' => false, 'message' => 'Đợt đã bị khóa'];
        // Hết thời gian đợt tổng → khóa hết
        if (strtotime($dot->den_ngay . ' 23:59:59') < time()) {
            return ['ok' => false, 'message' => 'Đợt đăng ký đã kết thúc (' . date('d/m/Y', strtotime($dot->den_ngay)) . ')'];
        }
        // Trước thời gian đợt
        if (strtotime($dot->tu_ngay) > time()) {
            return ['ok' => false, 'message' => 'Đợt đăng ký chưa bắt đầu (' . date('d/m/Y', strtotime($dot->tu_ngay)) . ')'];
        }
        $phase = NCKH_DotGiaiDoan_DAL::getActivePhase($dotId, $hanhVi);
        if (!$phase) {
            return ['ok' => false, 'message' => 'Không có giai đoạn "' . self::hanhViText($hanhVi) . '" đang mở'];
        }
        return ['ok' => true, 'phase' => $phase];
    }
}
