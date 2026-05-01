<?php
require_once __DIR__ . '/../DAL/DM_HocVien_DAL.php';
require_once __DIR__ . '/../DAL/DM_NhanVien_DAL.php';
require_once __DIR__ . '/../DAL/DM_NhatKyHeThong_DAL.php';

class DM_HocVien_BUS
{
    const MODULE_KEY = 'DM_HocVien';
    const AVATAR_MAX_SIZE = 3145728; // 3MB
    const AVATAR_ALLOWED = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    public static function insert(DM_HocVien_PUBLIC $e): array
    {
        $v = self::validate($e);
        if (!$v['success']) return $v;

        // Nếu là nhân viên và chưa có mã HV → tự sinh từ mã NV
        if ($e->la_nhan_vien && $e->nhan_vien_id && $e->ma_hv === '') {
            $nv = DM_NhanVien_DAL::getById($e->nhan_vien_id);
            if ($nv) $e->ma_hv = 'HV-' . $nv->ma_nv;
        }

        if (DM_HocVien_DAL::checkMaExists($e->ma_hv)) {
            return ['success' => false, 'message' => 'Mã học viên đã tồn tại'];
        }
        if ($e->la_nhan_vien && $e->nhan_vien_id && DM_HocVien_DAL::checkNhanVienExists($e->nhan_vien_id)) {
            return ['success' => false, 'message' => 'Nhân viên này đã có hồ sơ học viên — không thể thêm lại'];
        }
        $id = DM_HocVien_DAL::insert($e);
        MemcachedHelper::deleteByPrefix('dm_hoc_vien:');
        DM_NhatKyHeThong_DAL::log($e->nguoi_tao ?? 0, Constants::MODULE_HE_THONG, "Thêm HV: {$e->ho_ten}", 'DM_HOC_VIEN', $id);
        return ['success' => true, 'message' => 'Thêm học viên thành công', 'data' => ['id' => $id]];
    }

    public static function update(DM_HocVien_PUBLIC $e): array
    {
        if (!$e->id) return ['success' => false, 'message' => 'Thiếu ID'];
        $v = self::validate($e);
        if (!$v['success']) return $v;

        if (DM_HocVien_DAL::checkMaExists($e->ma_hv, $e->id)) {
            return ['success' => false, 'message' => 'Mã học viên đã tồn tại'];
        }
        if ($e->la_nhan_vien && $e->nhan_vien_id && DM_HocVien_DAL::checkNhanVienExists($e->nhan_vien_id, $e->id)) {
            return ['success' => false, 'message' => 'Nhân viên này đã có hồ sơ học viên khác'];
        }
        DM_HocVien_DAL::update($e);
        MemcachedHelper::deleteByPrefix('dm_hoc_vien:');
        DM_NhatKyHeThong_DAL::log($e->nguoi_cap_nhat ?? 0, Constants::MODULE_HE_THONG, "Sửa HV: {$e->ho_ten}", 'DM_HOC_VIEN', $e->id);
        return ['success' => true, 'message' => 'Cập nhật thành công'];
    }

    private static function validate(DM_HocVien_PUBLIC $e): array
    {
        $e->ho_ten = trim($e->ho_ten);
        $e->ma_hv = trim($e->ma_hv);
        if ($e->ho_ten === '') return ['success' => false, 'message' => 'Họ tên không được để trống'];
        if ($e->la_nhan_vien) {
            if (!$e->nhan_vien_id) return ['success' => false, 'message' => 'Đã chọn "là nhân viên" nhưng chưa chọn nhân viên'];
        } else {
            $e->nhan_vien_id = null;
        }
        if ($e->ma_hv === '') {
            // Auto mã khi bỏ trống mà không phải là NV
            if (!$e->la_nhan_vien) {
                $e->ma_hv = 'HV' . date('ymd') . substr((string)microtime(true), -4);
            }
        }
        if ($e->email && !Helper::isEmail($e->email)) {
            return ['success' => false, 'message' => 'Email không hợp lệ'];
        }
        if ($e->dien_thoai && !Helper::isPhone($e->dien_thoai)) {
            return ['success' => false, 'message' => 'Số điện thoại không hợp lệ'];
        }
        return ['success' => true];
    }

    public static function trash(int $id, int $u): array
    {
        DM_HocVien_DAL::trash($id, $u);
        MemcachedHelper::deleteByPrefix('dm_hoc_vien:');
        DM_NhatKyHeThong_DAL::log($u, Constants::MODULE_HE_THONG, "Xóa tạm HV id={$id}", 'DM_HOC_VIEN', $id);
        return ['success' => true, 'message' => 'Đã chuyển vào thùng rác'];
    }

    public static function restore(int $id, int $u): array
    {
        DM_HocVien_DAL::restore($id, $u);
        MemcachedHelper::deleteByPrefix('dm_hoc_vien:');
        return ['success' => true, 'message' => 'Đã khôi phục'];
    }

    public static function delete(int $id): array
    {
        try {
            $avatar = DM_HocVien_DAL::getAvatarPath($id);
            DM_HocVien_DAL::delete($id);
            if ($avatar) {
                $path = __DIR__ . '/../assets/uploads/hocvien/' . $avatar;
                if (is_file($path)) @unlink($path);
            }
            MemcachedHelper::deleteByPrefix('dm_hoc_vien:');
            return ['success' => true, 'message' => 'Đã xóa vĩnh viễn'];
        } catch (Throwable $ex) {
            return ['success' => false, 'message' => 'Không thể xóa: dữ liệu đang được tham chiếu'];
        }
    }

    public static function getById(int $id): ?DM_HocVien_DTO
    {
        return DM_HocVien_DAL::getById($id);
    }

    public static function getPaged(int $page, int $pageSize, string $search = '', int $daXoa = 0, int $doiTuongId = 0, int $laNhanVien = -1): array
    {
        return DM_HocVien_DAL::getPaged($page, $pageSize, $search, $daXoa, $doiTuongId, $laNhanVien);
    }

    public static function getStats(): array
    {
        return DM_HocVien_DAL::getStats();
    }

    public static function getCombo(): array
    {
        return DM_HocVien_DAL::getCombo();
    }

    public static function findByCccd(string $cccd): array { return DM_HocVien_DAL::findByCccd($cccd); }
    public static function findByDienThoai(string $sdt): array { return DM_HocVien_DAL::findByDienThoai($sdt); }

    /**
     * Upload avatar, return filename stored in DB (relative).
     * Trả về: ['success'=>true,'filename'=>...] hoặc ['success'=>false,'message'=>...]
     */
    public static function uploadAvatar(array $file, int $u): array
    {
        if (!isset($file['tmp_name']) || $file['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'message' => 'Upload không thành công'];
        }
        if ($file['size'] > self::AVATAR_MAX_SIZE) {
            return ['success' => false, 'message' => 'Ảnh quá lớn (tối đa 3MB)'];
        }
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, self::AVATAR_ALLOWED, true)) {
            return ['success' => false, 'message' => 'Định dạng ảnh không hỗ trợ (jpg, png, gif, webp)'];
        }
        // Sanity check MIME
        $info = @getimagesize($file['tmp_name']);
        if (!$info) return ['success' => false, 'message' => 'Tệp không phải ảnh hợp lệ'];

        $dir = __DIR__ . '/../assets/uploads/hocvien';
        if (!is_dir($dir)) @mkdir($dir, 0777, true);

        $filename = 'hv_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
        $dest = $dir . '/' . $filename;
        if (!move_uploaded_file($file['tmp_name'], $dest)) {
            return ['success' => false, 'message' => 'Không ghi được ảnh lên server'];
        }
        return ['success' => true, 'filename' => $filename];
    }

    public static function removeAvatarFile(?string $filename): void
    {
        if (!$filename) return;
        $path = __DIR__ . '/../assets/uploads/hocvien/' . $filename;
        if (is_file($path)) @unlink($path);
    }
}
