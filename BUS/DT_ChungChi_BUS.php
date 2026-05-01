<?php
require_once __DIR__ . '/../DAL/DT_ChungChi_DAL.php';
require_once __DIR__ . '/../DAL/DM_NhatKyHeThong_DAL.php';

class DT_ChungChi_BUS
{
    const MODULE_KEY = 'DT_ChungChi';
    const MAX_SIZE   = 20 * 1024 * 1024; // 20 MB
    const ALLOWED_EXT = ['pdf', 'jpg', 'jpeg', 'png'];

    // Trạng thái chứng chỉ
    const TT_NHAP    = 0;
    const TT_DA_CAP  = 1;
    const TT_THU_HOI = 2;

    public static function uploadDir(): string
    {
        return __DIR__ . '/../assets/uploads/chungchi/';
    }

    public static function insert(DT_ChungChi_PUBLIC $e, ?array $file = null): array
    {
        $v = self::validate($e);
        if (!$v['success']) return $v;

        if (DT_ChungChi_DAL::checkSoExists($e->so_chung_chi)) {
            return ['success' => false, 'message' => 'Số chứng chỉ đã tồn tại trong hệ thống'];
        }

        if ($file && !empty($file['tmp_name'])) {
            $up = self::handleUpload($file);
            if (!$up['success']) return $up;
            $e->duong_dan_file = $up['data']['file_name'];
        }

        $id = DT_ChungChi_DAL::insert($e);
        DM_NhatKyHeThong_DAL::log(
            $e->nguoi_tao ?? 0, Constants::MODULE_DAO_TAO,
            "Thêm chứng chỉ: {$e->so_chung_chi} - {$e->ten_chung_chi}", 'DT_CHUNG_CHI', $id
        );
        return ['success' => true, 'message' => 'Thêm chứng chỉ thành công', 'data' => ['id' => $id]];
    }

    public static function update(DT_ChungChi_PUBLIC $e, ?array $file = null): array
    {
        if (!$e->id) return ['success' => false, 'message' => 'Thiếu ID'];
        $v = self::validate($e);
        if (!$v['success']) return $v;

        if (DT_ChungChi_DAL::checkSoExists($e->so_chung_chi, $e->id)) {
            return ['success' => false, 'message' => 'Số chứng chỉ đã tồn tại trong hệ thống'];
        }

        $current = DT_ChungChi_DAL::getById($e->id);
        if (!$current) return ['success' => false, 'message' => 'Không tìm thấy chứng chỉ'];

        if ($file && !empty($file['tmp_name'])) {
            $up = self::handleUpload($file);
            if (!$up['success']) return $up;
            // Xóa file cũ
            if ($current->duong_dan_file) {
                $old = self::uploadDir() . $current->duong_dan_file;
                if (is_file($old)) @unlink($old);
            }
            $e->duong_dan_file = $up['data']['file_name'];
        } else {
            $e->duong_dan_file = $current->duong_dan_file;
        }

        DT_ChungChi_DAL::update($e);
        DM_NhatKyHeThong_DAL::log(
            $e->nguoi_cap_nhat ?? 0, Constants::MODULE_DAO_TAO,
            "Sửa chứng chỉ id={$e->id}: {$e->so_chung_chi}", 'DT_CHUNG_CHI', $e->id
        );
        return ['success' => true, 'message' => 'Cập nhật chứng chỉ thành công'];
    }

    public static function trash(int $id, int $u): array
    {
        DT_ChungChi_DAL::trash($id, $u);
        DM_NhatKyHeThong_DAL::log($u, Constants::MODULE_DAO_TAO, "Xóa tạm chứng chỉ id={$id}", 'DT_CHUNG_CHI', $id);
        return ['success' => true, 'message' => 'Đã chuyển vào thùng rác'];
    }

    public static function restore(int $id, int $u): array
    {
        DT_ChungChi_DAL::restore($id, $u);
        return ['success' => true, 'message' => 'Đã khôi phục chứng chỉ'];
    }

    public static function delete(int $id): array
    {
        $cc = DT_ChungChi_DAL::getById($id);
        if ($cc && $cc->duong_dan_file) {
            $path = self::uploadDir() . $cc->duong_dan_file;
            if (is_file($path)) @unlink($path);
        }
        DT_ChungChi_DAL::delete($id);
        return ['success' => true, 'message' => 'Đã xóa vĩnh viễn chứng chỉ'];
    }

    public static function capChungChi(int $id, int $u): array
    {
        $cc = DT_ChungChi_DAL::getById($id);
        if (!$cc) return ['success' => false, 'message' => 'Không tìm thấy chứng chỉ'];
        if ($cc->trang_thai === self::TT_DA_CAP) return ['success' => false, 'message' => 'Chứng chỉ đã được cấp'];

        $cc->trang_thai      = self::TT_DA_CAP;
        $cc->nguoi_cap_nhat  = $u;
        DT_ChungChi_DAL::update($cc);
        DM_NhatKyHeThong_DAL::log($u, Constants::MODULE_DAO_TAO, "Cấp chứng chỉ id={$id}: {$cc->so_chung_chi}", 'DT_CHUNG_CHI', $id);
        return ['success' => true, 'message' => 'Đã cấp chứng chỉ thành công'];
    }

    public static function thuHoiChungChi(int $id, int $u): array
    {
        $cc = DT_ChungChi_DAL::getById($id);
        if (!$cc) return ['success' => false, 'message' => 'Không tìm thấy chứng chỉ'];

        $cc->trang_thai     = self::TT_THU_HOI;
        $cc->nguoi_cap_nhat = $u;
        DT_ChungChi_DAL::update($cc);
        DM_NhatKyHeThong_DAL::log($u, Constants::MODULE_DAO_TAO, "Thu hồi chứng chỉ id={$id}: {$cc->so_chung_chi}", 'DT_CHUNG_CHI', $id);
        return ['success' => true, 'message' => 'Đã thu hồi chứng chỉ'];
    }

    public static function getById(int $id): ?DT_ChungChi_DTO    { return DT_ChungChi_DAL::getById($id); }
    public static function getPaged(int $p, int $s, array $opts = [], int $dx = 0): array { return DT_ChungChi_DAL::getPaged($p, $s, $opts, $dx); }
    public static function getStats(): array                       { return DT_ChungChi_DAL::getStats(); }
    public static function getByHocVien(int $hvId): array          { return DT_ChungChi_DAL::getByHocVien($hvId); }

    // ============ Upload helper ============
    private static function handleUpload(array $file): array
    {
        if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'message' => 'Lỗi upload file chứng chỉ'];
        }
        if ($file['size'] <= 0) return ['success' => false, 'message' => 'File rỗng'];
        if ($file['size'] > self::MAX_SIZE) return ['success' => false, 'message' => 'File quá lớn (tối đa 20MB)'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, self::ALLOWED_EXT, true)) {
            return ['success' => false, 'message' => 'Chỉ cho phép: ' . implode(', ', self::ALLOWED_EXT)];
        }
        $dir = self::uploadDir();
        if (!is_dir($dir)) @mkdir($dir, 0755, true);
        $newName = date('Ymd_His') . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
        if (!@move_uploaded_file($file['tmp_name'], $dir . $newName)) {
            return ['success' => false, 'message' => 'Không lưu được file lên server'];
        }
        return ['success' => true, 'data' => ['file_name' => $newName]];
    }

    private static function validate(DT_ChungChi_PUBLIC $e): array
    {
        $e->so_chung_chi  = trim($e->so_chung_chi);
        $e->ten_chung_chi = trim($e->ten_chung_chi);
        if (!$e->hoc_vien_id) return ['success' => false, 'message' => 'Thiếu thông tin học viên'];
        if (!$e->lop_hoc_id)  return ['success' => false, 'message' => 'Thiếu thông tin lớp học'];
        if ($e->so_chung_chi === '')  return ['success' => false, 'message' => 'Số chứng chỉ không được để trống'];
        if ($e->ten_chung_chi === '') return ['success' => false, 'message' => 'Tên chứng chỉ không được để trống'];
        if ($e->ngay_cap === '')      return ['success' => false, 'message' => 'Ngày cấp không được để trống'];
        if ($e->diem_trung_binh !== null && ($e->diem_trung_binh < 0 || $e->diem_trung_binh > 10)) {
            return ['success' => false, 'message' => 'Điểm trung bình phải từ 0 đến 10'];
        }
        return ['success' => true];
    }
}
