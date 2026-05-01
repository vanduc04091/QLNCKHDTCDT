<?php
require_once __DIR__ . '/../DAL/DT_HoSoHocVien_DAL.php';
require_once __DIR__ . '/../DAL/DM_NhatKyHeThong_DAL.php';

class DT_HoSoHocVien_BUS
{
    const MODULE_KEY = 'DT_HoSoHocVien';
    const MAX_SIZE   = 20 * 1024 * 1024; // 20 MB
    const ALLOWED_EXT = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png', 'gif', 'zip', 'rar'];

    public static function uploadDir(): string
    {
        return __DIR__ . '/../assets/uploads/hoso_hocvien/';
    }

    public static function insert(DT_HoSoHocVien_PUBLIC $e, ?array $file = null): array
    {
        $v = self::validate($e);
        if (!$v['success']) return $v;

        if ($file && !empty($file['tmp_name'])) {
            $up = self::handleUpload($file);
            if (!$up['success']) return $up;
            $e->duong_dan  = $up['data']['file_name'];
            $e->kich_thuoc = $up['data']['file_size'];
        }

        $id = DT_HoSoHocVien_DAL::insert($e);
        DM_NhatKyHeThong_DAL::log(
            $e->nguoi_tao ?? 0, Constants::MODULE_DAO_TAO,
            "Thêm hồ sơ học viên: {$e->ten_ho_so}", 'DT_HO_SO_HOC_VIEN', $id
        );
        return ['success' => true, 'message' => 'Thêm hồ sơ thành công', 'data' => ['id' => $id]];
    }

    public static function update(DT_HoSoHocVien_PUBLIC $e, ?array $file = null): array
    {
        if (!$e->id) return ['success' => false, 'message' => 'Thiếu ID'];
        $v = self::validate($e);
        if (!$v['success']) return $v;

        $current = DT_HoSoHocVien_DAL::getById($e->id);
        if (!$current) return ['success' => false, 'message' => 'Không tìm thấy hồ sơ'];

        if ($file && !empty($file['tmp_name'])) {
            $up = self::handleUpload($file);
            if (!$up['success']) return $up;
            // Xóa file cũ
            if ($current->duong_dan) {
                $old = self::uploadDir() . $current->duong_dan;
                if (is_file($old)) @unlink($old);
            }
            $e->duong_dan  = $up['data']['file_name'];
            $e->kich_thuoc = $up['data']['file_size'];
        } else {
            $e->duong_dan  = $current->duong_dan;
            $e->kich_thuoc = $current->kich_thuoc;
        }

        DT_HoSoHocVien_DAL::update($e);
        DM_NhatKyHeThong_DAL::log(
            $e->nguoi_cap_nhat ?? 0, Constants::MODULE_DAO_TAO,
            "Sửa hồ sơ học viên id={$e->id}: {$e->ten_ho_so}", 'DT_HO_SO_HOC_VIEN', $e->id
        );
        return ['success' => true, 'message' => 'Cập nhật hồ sơ thành công'];
    }

    public static function trash(int $id, int $u): array
    {
        DT_HoSoHocVien_DAL::trash($id, $u);
        DM_NhatKyHeThong_DAL::log($u, Constants::MODULE_DAO_TAO, "Xóa tạm hồ sơ id={$id}", 'DT_HO_SO_HOC_VIEN', $id);
        return ['success' => true, 'message' => 'Đã chuyển vào thùng rác'];
    }

    public static function restore(int $id, int $u): array
    {
        DT_HoSoHocVien_DAL::restore($id, $u);
        return ['success' => true, 'message' => 'Đã khôi phục hồ sơ'];
    }

    public static function delete(int $id): array
    {
        $hs = DT_HoSoHocVien_DAL::getById($id);
        if ($hs && $hs->duong_dan) {
            $path = self::uploadDir() . $hs->duong_dan;
            if (is_file($path)) @unlink($path);
        }
        DT_HoSoHocVien_DAL::delete($id);
        return ['success' => true, 'message' => 'Đã xóa vĩnh viễn'];
    }

    public static function getById(int $id): ?DT_HoSoHocVien_DTO
    {
        return DT_HoSoHocVien_DAL::getById($id);
    }

    public static function getPaged(int $p, int $s, array $opts = [], int $dx = 0): array
    {
        return DT_HoSoHocVien_DAL::getPaged($p, $s, $opts, $dx);
    }

    public static function getByHocVien(int $hocVienId): array
    {
        return DT_HoSoHocVien_DAL::getByHocVien($hocVienId);
    }

    public static function getStats(): array
    {
        return DT_HoSoHocVien_DAL::getStats();
    }

    public static function getComboLoai(): array
    {
        return DT_HoSoHocVien_DAL::getComboLoai();
    }

    // ============ Upload helper ============
    private static function handleUpload(array $file): array
    {
        if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'message' => 'Lỗi upload file'];
        }
        if ($file['size'] <= 0) return ['success' => false, 'message' => 'File rỗng'];
        if ($file['size'] > self::MAX_SIZE) {
            return ['success' => false, 'message' => 'File quá lớn (tối đa 20MB)'];
        }
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, self::ALLOWED_EXT, true)) {
            return ['success' => false, 'message' => 'Định dạng không cho phép. Cho phép: ' . implode(', ', self::ALLOWED_EXT)];
        }
        $dir = self::uploadDir();
        if (!is_dir($dir)) @mkdir($dir, 0755, true);
        $newName = date('Ymd_His') . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
        if (!@move_uploaded_file($file['tmp_name'], $dir . $newName)) {
            return ['success' => false, 'message' => 'Không lưu được file lên server'];
        }
        return ['success' => true, 'data' => [
            'file_name' => $newName,
            'file_size' => (int)$file['size'],
        ]];
    }

    private static function validate(DT_HoSoHocVien_PUBLIC $e): array
    {
        $e->loai_ho_so = trim($e->loai_ho_so);
        $e->ten_ho_so  = trim($e->ten_ho_so);
        if (!$e->hoc_vien_id) return ['success' => false, 'message' => 'Thiếu thông tin học viên'];
        if ($e->loai_ho_so === '') return ['success' => false, 'message' => 'Loại hồ sơ không được để trống'];
        if ($e->ten_ho_so === '') return ['success' => false, 'message' => 'Tên hồ sơ không được để trống'];
        return ['success' => true];
    }

    public static function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0; $b = (float)$bytes;
        while ($b >= 1024 && $i < count($units) - 1) { $b /= 1024; $i++; }
        return number_format($b, $b < 10 && $i > 0 ? 1 : 0) . ' ' . $units[$i];
    }
}
