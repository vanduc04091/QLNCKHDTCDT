<?php
require_once __DIR__ . '/../DAL/DT_TaiLieu_DAL.php';
require_once __DIR__ . '/../DAL/DM_NhatKyHeThong_DAL.php';

class DT_TaiLieu_BUS
{
    const MODULE_KEY = 'DT_TaiLieu';
    const MAX_SIZE = 50 * 1024 * 1024; // 50 MB
    const ALLOWED_EXT = ['pdf','doc','docx','ppt','pptx','xls','xlsx','txt','zip','rar','png','jpg','jpeg','gif','mp4','mp3','webm'];

    public static function uploadDir(): string
    {
        return __DIR__ . '/../assets/uploads/tailieu/';
    }

    public static function insert(DT_TaiLieu_PUBLIC $e, ?array $file = null): array
    {
        $v = self::validate($e);
        if (!$v['success']) return $v;
        if (DT_TaiLieu_DAL::checkMaExists($e->ma_tai_lieu)) {
            return ['success' => false, 'message' => 'Mã tài liệu đã tồn tại'];
        }
        // Upload file (nếu có)
        if ($file && !empty($file['tmp_name'])) {
            $up = self::handleUpload($file);
            if (!$up['success']) return $up;
            $e->file_name = $up['data']['file_name'];
            $e->file_goc = $up['data']['file_goc'];
            $e->file_size = $up['data']['file_size'];
            $e->dinh_dang = $up['data']['ext'];
        } elseif ($e->link_ngoai) {
            // Detect format từ URL
            $e->dinh_dang = self::detectLinkFormat($e->link_ngoai);
        }

        // Phải có ít nhất 1 nguồn
        if (!$e->file_name && !$e->link_ngoai) {
            return ['success' => false, 'message' => 'Phải upload file hoặc nhập link ngoài'];
        }

        $id = DT_TaiLieu_DAL::insert($e);
        DM_NhatKyHeThong_DAL::log($e->nguoi_tao ?? 0, Constants::MODULE_HE_THONG,
            "Thêm tài liệu: {$e->tieu_de}", 'DT_TAI_LIEU', $id);
        return ['success' => true, 'message' => 'Thêm tài liệu thành công', 'data' => ['id' => $id]];
    }

    public static function update(DT_TaiLieu_PUBLIC $e, ?array $file = null): array
    {
        if (!$e->id) return ['success' => false, 'message' => 'Thiếu ID'];
        $v = self::validate($e);
        if (!$v['success']) return $v;
        if (DT_TaiLieu_DAL::checkMaExists($e->ma_tai_lieu, $e->id)) {
            return ['success' => false, 'message' => 'Mã tài liệu đã tồn tại'];
        }

        $current = DT_TaiLieu_DAL::getById($e->id);
        if (!$current) return ['success' => false, 'message' => 'Không tìm thấy tài liệu'];

        // Xử lý upload file mới
        if ($file && !empty($file['tmp_name'])) {
            $up = self::handleUpload($file);
            if (!$up['success']) return $up;
            // Xóa file cũ
            if ($current->file_name) {
                $oldPath = self::uploadDir() . $current->file_name;
                if (is_file($oldPath)) @unlink($oldPath);
            }
            $e->file_name = $up['data']['file_name'];
            $e->file_goc = $up['data']['file_goc'];
            $e->file_size = $up['data']['file_size'];
            $e->dinh_dang = $up['data']['ext'];
        } else {
            // Giữ file cũ nếu không upload mới
            $e->file_name = $current->file_name;
            $e->file_goc = $current->file_goc;
            $e->file_size = $current->file_size;
            // Định dạng: ưu tiên link mới nếu có, không thì giữ cũ
            if ($e->link_ngoai && $e->link_ngoai !== $current->link_ngoai) {
                $e->dinh_dang = self::detectLinkFormat($e->link_ngoai);
            } else {
                $e->dinh_dang = $current->dinh_dang;
            }
        }

        if (!$e->file_name && !$e->link_ngoai) {
            return ['success' => false, 'message' => 'Phải có file hoặc link ngoài'];
        }

        DT_TaiLieu_DAL::update($e);
        return ['success' => true, 'message' => 'Cập nhật tài liệu thành công'];
    }

    public static function trash(int $id, int $u): array
    {
        DT_TaiLieu_DAL::trash($id, $u);
        DM_NhatKyHeThong_DAL::log($u, Constants::MODULE_HE_THONG, "Xóa tạm tài liệu id={$id}", 'DT_TAI_LIEU', $id);
        return ['success' => true, 'message' => 'Đã chuyển vào thùng rác'];
    }

    public static function restore(int $id, int $u): array
    {
        DT_TaiLieu_DAL::restore($id, $u);
        return ['success' => true, 'message' => 'Đã khôi phục'];
    }

    public static function delete(int $id): array
    {
        $tl = DT_TaiLieu_DAL::getById($id);
        if ($tl && $tl->file_name) {
            $path = self::uploadDir() . $tl->file_name;
            if (is_file($path)) @unlink($path);
        }
        DT_TaiLieu_DAL::delete($id);
        return ['success' => true, 'message' => 'Đã xóa vĩnh viễn'];
    }

    public static function getById(int $id): ?DT_TaiLieu_DTO { return DT_TaiLieu_DAL::getById($id); }

    public static function getPaged(int $p, int $s, array $opts = [], int $dx = 0): array
    {
        return DT_TaiLieu_DAL::getPaged($p, $s, $opts, $dx);
    }

    public static function getStats(): array { return DT_TaiLieu_DAL::getStats(); }
    public static function incView(int $id): void { DT_TaiLieu_DAL::incView($id); }
    public static function incDownload(int $id): void { DT_TaiLieu_DAL::incDownload($id); }

    // ============ Upload helper ============
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
            'file_name' => $newName,
            'file_goc' => $origName,
            'file_size' => (int)$file['size'],
            'ext' => $ext,
        ]];
    }

    public static function detectLinkFormat(string $url): string
    {
        $u = strtolower($url);
        if (strpos($u, 'youtube.com') !== false || strpos($u, 'youtu.be') !== false) return 'youtube';
        if (strpos($u, 'drive.google.com') !== false) return 'gdrive';
        if (strpos($u, 'docs.google.com') !== false) return 'gdocs';
        if (preg_match('/\.(pdf|docx?|pptx?|xlsx?|mp4|mp3|zip)$/i', $u, $m)) return strtolower($m[1]);
        return 'link';
    }

    private static function validate(DT_TaiLieu_PUBLIC $e): array
    {
        $e->ma_tai_lieu = trim($e->ma_tai_lieu);
        $e->tieu_de = trim($e->tieu_de);
        if ($e->ma_tai_lieu === '' || $e->tieu_de === '') {
            return ['success' => false, 'message' => 'Mã và tiêu đề không được để trống'];
        }
        if ($e->loai_tai_lieu < 1 || $e->loai_tai_lieu > 6) {
            return ['success' => false, 'message' => 'Loại tài liệu không hợp lệ'];
        }
        if ($e->nam_xuat_ban !== null && ($e->nam_xuat_ban < 1900 || $e->nam_xuat_ban > 2100)) {
            return ['success' => false, 'message' => 'Năm xuất bản không hợp lệ'];
        }
        if ($e->link_ngoai && !filter_var($e->link_ngoai, FILTER_VALIDATE_URL)) {
            return ['success' => false, 'message' => 'Link ngoài không hợp lệ'];
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
        $units = ['B','KB','MB','GB'];
        $i = 0;
        $b = (float)$bytes;
        while ($b >= 1024 && $i < count($units)-1) { $b /= 1024; $i++; }
        return number_format($b, $b < 10 && $i > 0 ? 1 : 0) . ' ' . $units[$i];
    }
}
