<?php
require_once __DIR__ . '/../DAL/DT_BaiKiemTra_DAL.php';
require_once __DIR__ . '/../DAL/DM_NhatKyHeThong_DAL.php';

class DT_BaiKiemTra_BUS
{
    const MODULE_KEY = 'DT_BaiKiemTra';
    const MAX_SIZE = 30 * 1024 * 1024; // 30 MB
    const ALLOWED_EXT = ['pdf','doc','docx','ppt','pptx','xls','xlsx','txt','zip','rar','png','jpg','jpeg'];

    public static function uploadDir(): string
    {
        return __DIR__ . '/../assets/uploads/baikiemtra/';
    }

    public static function insert(DT_BaiKiemTra_PUBLIC $e, ?array $deFile = null, ?array $apFile = null): array
    {
        $v = self::validate($e);
        if (!$v['success']) return $v;
        if (DT_BaiKiemTra_DAL::checkMaExists($e->ma_bkt)) {
            return ['success' => false, 'message' => 'Mã bài kiểm tra đã tồn tại'];
        }

        if ($deFile && !empty($deFile['tmp_name'])) {
            $up = self::handleUpload($deFile);
            if (!$up['success']) return $up;
            $e->de_file_name = $up['data']['file_name'];
            $e->de_file_goc = $up['data']['file_goc'];
            $e->de_file_size = $up['data']['file_size'];
        }
        if ($apFile && !empty($apFile['tmp_name'])) {
            $up = self::handleUpload($apFile);
            if (!$up['success']) return $up;
            $e->dap_an_file_name = $up['data']['file_name'];
            $e->dap_an_file_goc = $up['data']['file_goc'];
            $e->dap_an_file_size = $up['data']['file_size'];
        }

        $id = DT_BaiKiemTra_DAL::insert($e);
        DM_NhatKyHeThong_DAL::log($e->nguoi_tao ?? 0, Constants::MODULE_HE_THONG,
            "Thêm bài kiểm tra: {$e->tieu_de}", 'DT_BAI_KIEM_TRA', $id);
        return ['success' => true, 'message' => 'Thêm bài kiểm tra thành công', 'data' => ['id' => $id]];
    }

    public static function update(DT_BaiKiemTra_PUBLIC $e, ?array $deFile = null, ?array $apFile = null): array
    {
        if (!$e->id) return ['success' => false, 'message' => 'Thiếu ID'];
        $v = self::validate($e);
        if (!$v['success']) return $v;
        if (DT_BaiKiemTra_DAL::checkMaExists($e->ma_bkt, $e->id)) {
            return ['success' => false, 'message' => 'Mã bài kiểm tra đã tồn tại'];
        }
        $current = DT_BaiKiemTra_DAL::getById($e->id);
        if (!$current) return ['success' => false, 'message' => 'Không tìm thấy bài kiểm tra'];

        if ($deFile && !empty($deFile['tmp_name'])) {
            $up = self::handleUpload($deFile);
            if (!$up['success']) return $up;
            self::removePhysical($current->de_file_name);
            $e->de_file_name = $up['data']['file_name'];
            $e->de_file_goc = $up['data']['file_goc'];
            $e->de_file_size = $up['data']['file_size'];
        } else {
            $e->de_file_name = $current->de_file_name;
            $e->de_file_goc = $current->de_file_goc;
            $e->de_file_size = $current->de_file_size;
        }
        if ($apFile && !empty($apFile['tmp_name'])) {
            $up = self::handleUpload($apFile);
            if (!$up['success']) return $up;
            self::removePhysical($current->dap_an_file_name);
            $e->dap_an_file_name = $up['data']['file_name'];
            $e->dap_an_file_goc = $up['data']['file_goc'];
            $e->dap_an_file_size = $up['data']['file_size'];
        } else {
            $e->dap_an_file_name = $current->dap_an_file_name;
            $e->dap_an_file_goc = $current->dap_an_file_goc;
            $e->dap_an_file_size = $current->dap_an_file_size;
        }

        DT_BaiKiemTra_DAL::update($e);
        return ['success' => true, 'message' => 'Cập nhật bài kiểm tra thành công'];
    }

    public static function clearFile(int $id, string $field, int $u): array
    {
        $current = DT_BaiKiemTra_DAL::getById($id);
        if (!$current) return ['success' => false, 'message' => 'Không tìm thấy bài kiểm tra'];
        $fname = $field === 'de' ? $current->de_file_name : ($field === 'dap_an' ? $current->dap_an_file_name : null);
        if (!$fname) return ['success' => false, 'message' => 'Không có file để xóa'];
        self::removePhysical($fname);
        DT_BaiKiemTra_DAL::clearFile($id, $field, $u);
        return ['success' => true, 'message' => 'Đã gỡ file'];
    }

    public static function trash(int $id, int $u): array
    {
        DT_BaiKiemTra_DAL::trash($id, $u);
        DM_NhatKyHeThong_DAL::log($u, Constants::MODULE_HE_THONG, "Xóa tạm BKT id={$id}", 'DT_BAI_KIEM_TRA', $id);
        return ['success' => true, 'message' => 'Đã chuyển vào thùng rác'];
    }

    public static function restore(int $id, int $u): array
    {
        DT_BaiKiemTra_DAL::restore($id, $u);
        return ['success' => true, 'message' => 'Đã khôi phục'];
    }

    public static function delete(int $id): array
    {
        $bkt = DT_BaiKiemTra_DAL::getById($id);
        if ($bkt) {
            self::removePhysical($bkt->de_file_name);
            self::removePhysical($bkt->dap_an_file_name);
        }
        DT_BaiKiemTra_DAL::delete($id);
        return ['success' => true, 'message' => 'Đã xóa vĩnh viễn'];
    }

    public static function getById(int $id): ?DT_BaiKiemTra_DTO { return DT_BaiKiemTra_DAL::getById($id); }
    public static function getPaged(int $p, int $s, array $opts = [], int $dx = 0): array
    {
        return DT_BaiKiemTra_DAL::getPaged($p, $s, $opts, $dx);
    }
    public static function getStats(): array { return DT_BaiKiemTra_DAL::getStats(); }

    // ============ Helpers ============
    private static function handleUpload(array $file): array
    {
        if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'message' => 'Lỗi upload: ' . self::uploadErrorText($file['error'] ?? -1)];
        }
        if ($file['size'] <= 0) return ['success' => false, 'message' => 'File rỗng'];
        if ($file['size'] > self::MAX_SIZE) {
            return ['success' => false, 'message' => 'File quá lớn (tối đa ' . self::formatBytes(self::MAX_SIZE) . ')'];
        }
        $origName = $file['name'];
        $ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
        if (!in_array($ext, self::ALLOWED_EXT, true)) {
            return ['success' => false, 'message' => 'Định dạng không cho phép. Cho phép: ' . implode(', ', self::ALLOWED_EXT)];
        }
        $dir = self::uploadDir();
        if (!is_dir($dir)) @mkdir($dir, 0755, true);
        $newName = date('Ymd_His') . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
        $dest = $dir . $newName;
        if (!@move_uploaded_file($file['tmp_name'], $dest)) {
            return ['success' => false, 'message' => 'Không lưu được file lên server'];
        }
        return ['success' => true, 'data' => [
            'file_name' => $newName, 'file_goc' => $origName, 'file_size' => (int)$file['size'],
        ]];
    }

    private static function removePhysical(?string $fname): void
    {
        if (!$fname) return;
        $path = self::uploadDir() . $fname;
        if (is_file($path)) @unlink($path);
    }

    private static function validate(DT_BaiKiemTra_PUBLIC $e): array
    {
        $e->ma_bkt = trim($e->ma_bkt);
        $e->tieu_de = trim($e->tieu_de);
        if ($e->ma_bkt === '' || $e->tieu_de === '') {
            return ['success' => false, 'message' => 'Mã và tiêu đề không được để trống'];
        }
        if ($e->loai_bkt < 1 || $e->loai_bkt > 4) {
            return ['success' => false, 'message' => 'Loại bài kiểm tra không hợp lệ'];
        }
        if ($e->thoi_gian_lam_bai !== null && $e->thoi_gian_lam_bai < 0) {
            return ['success' => false, 'message' => 'Thời gian làm bài không hợp lệ'];
        }
        return ['success' => true];
    }

    private static function uploadErrorText(int $code): string
    {
        switch ($code) {
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE: return 'File vượt quá giới hạn server';
            case UPLOAD_ERR_PARTIAL: return 'File chỉ upload một phần';
            case UPLOAD_ERR_NO_FILE: return 'Chưa chọn file';
            case UPLOAD_ERR_NO_TMP_DIR: return 'Thiếu thư mục tạm';
            case UPLOAD_ERR_CANT_WRITE: return 'Không ghi được file';
            default: return 'Lỗi không xác định';
        }
    }

    public static function formatBytes(int $bytes): string
    {
        $units = ['B','KB','MB','GB']; $i = 0; $b = (float)$bytes;
        while ($b >= 1024 && $i < count($units)-1) { $b /= 1024; $i++; }
        return number_format($b, $b < 10 && $i > 0 ? 1 : 0) . ' ' . $units[$i];
    }
}
