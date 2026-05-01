<?php
require_once __DIR__ . '/../DAL/DM_NguoiDung_DAL.php';
require_once __DIR__ . '/../DAL/DM_NhatKyHeThong_DAL.php';

class DM_NguoiDung_BUS
{
    const MODULE_KEY = 'DM_NguoiDung';

    public static function insert(DM_NguoiDung_PUBLIC $e, string $matKhauThuong): array
    {
        $e->tai_khoan = trim($e->tai_khoan);
        if ($e->tai_khoan === '') return ['success' => false, 'message' => 'Tài khoản không được để trống'];
        if (!preg_match('/^[a-zA-Z0-9._]{3,50}$/', $e->tai_khoan)) {
            return ['success' => false, 'message' => 'Tài khoản chỉ gồm chữ, số, dấu chấm/gạch dưới (3-50 ký tự)'];
        }
        if (strlen($matKhauThuong) < 6) return ['success' => false, 'message' => 'Mật khẩu tối thiểu 6 ký tự'];
        if (DM_NguoiDung_DAL::checkTaiKhoanExists($e->tai_khoan)) {
            return ['success' => false, 'message' => 'Tài khoản đã tồn tại'];
        }
        if ((int)$e->nhom_tai_khoan_id <= 0) return ['success' => false, 'message' => 'Chưa chọn nhóm tài khoản'];

        $e->mat_khau = password_hash($matKhauThuong, AppConfig::PASSWORD_ALGO, ['cost' => AppConfig::PASSWORD_COST]);

        try {
            $id = DM_NguoiDung_DAL::insert($e);
            MemcachedHelper::deleteByPrefix('dm_nguoi_dung:');
            DM_NhatKyHeThong_DAL::log($e->nguoi_tao ?? 0, Constants::MODULE_HE_THONG, "Thêm người dùng: {$e->tai_khoan}", 'DM_NGUOI_DUNG', $id);
            return ['success' => true, 'message' => 'Thêm người dùng thành công', 'data' => ['id' => $id]];
        } catch (Throwable $ex) {
            return ['success' => false, 'message' => 'Lỗi: ' . $ex->getMessage()];
        }
    }

    public static function update(DM_NguoiDung_PUBLIC $e): array
    {
        if (!$e->id) return ['success' => false, 'message' => 'Thiếu ID'];
        $e->tai_khoan = trim($e->tai_khoan);
        if ($e->tai_khoan === '') return ['success' => false, 'message' => 'Tài khoản không được để trống'];
        if (DM_NguoiDung_DAL::checkTaiKhoanExists($e->tai_khoan, $e->id)) {
            return ['success' => false, 'message' => 'Tài khoản đã tồn tại'];
        }
        try {
            DM_NguoiDung_DAL::update($e);
            MemcachedHelper::deleteByPrefix('dm_nguoi_dung:');
            PhanQuyenHelper::clearCache();
            DM_NhatKyHeThong_DAL::log($e->nguoi_cap_nhat ?? 0, Constants::MODULE_HE_THONG, "Sửa người dùng: {$e->tai_khoan}", 'DM_NGUOI_DUNG', $e->id);
            return ['success' => true, 'message' => 'Cập nhật thành công'];
        } catch (Throwable $ex) {
            return ['success' => false, 'message' => 'Lỗi: ' . $ex->getMessage()];
        }
    }

    public static function trash(int $id, int $u): array
    {
        if ($id === 1) return ['success' => false, 'message' => 'Không thể xóa tài khoản admin gốc'];
        DM_NguoiDung_DAL::trash($id, $u);
        MemcachedHelper::deleteByPrefix('dm_nguoi_dung:');
        DM_NhatKyHeThong_DAL::log($u, Constants::MODULE_HE_THONG, "Xóa tạm người dùng id={$id}", 'DM_NGUOI_DUNG', $id);
        return ['success' => true, 'message' => 'Đã chuyển vào thùng rác'];
    }

    public static function restore(int $id, int $u): array
    {
        DM_NguoiDung_DAL::restore($id, $u);
        MemcachedHelper::deleteByPrefix('dm_nguoi_dung:');
        return ['success' => true, 'message' => 'Đã khôi phục'];
    }

    public static function delete(int $id): array
    {
        if ($id === 1) return ['success' => false, 'message' => 'Không thể xóa tài khoản admin gốc'];
        DM_NguoiDung_DAL::delete($id);
        MemcachedHelper::deleteByPrefix('dm_nguoi_dung:');
        return ['success' => true, 'message' => 'Đã xóa vĩnh viễn'];
    }

    public static function getById(int $id): ?DM_NguoiDung_DTO
    {
        return DM_NguoiDung_DAL::getById($id);
    }

    public static function getPaged(int $page, int $pageSize, string $search = '', int $daXoa = 0, int $nhomId = 0): array
    {
        return DM_NguoiDung_DAL::getPaged($page, $pageSize, $search, $daXoa, $nhomId);
    }

    public static function login(string $taiKhoan, string $matKhau): array
    {
        $taiKhoan = trim($taiKhoan);
        if ($taiKhoan === '' || $matKhau === '') {
            return ['success' => false, 'message' => 'Vui lòng nhập tài khoản và mật khẩu'];
        }
        $user = DM_NguoiDung_DAL::getByTaiKhoan($taiKhoan);
        if (!$user) return ['success' => false, 'message' => 'Tài khoản hoặc mật khẩu không đúng'];
        if ((int)$user->trang_thai !== 1) return ['success' => false, 'message' => 'Tài khoản đã bị khóa'];
        if (!password_verify($matKhau, $user->mat_khau)) {
            return ['success' => false, 'message' => 'Tài khoản hoặc mật khẩu không đúng'];
        }

        DM_NguoiDung_DAL::updateLastLogin($user->id);
        DM_NhatKyHeThong_DAL::log($user->id, Constants::MODULE_HE_THONG, "Đăng nhập: {$user->tai_khoan}", 'DM_NGUOI_DUNG', $user->id);

        return ['success' => true, 'message' => 'Đăng nhập thành công', 'data' => [
            'id' => $user->id,
            'tai_khoan' => $user->tai_khoan,
            'ho_ten' => $user->ho_ten ?? $user->tai_khoan,
            'nhom_tai_khoan_id' => (int)$user->nhom_tai_khoan_id,
            'nhan_vien_id' => (int)($user->nhan_vien_id ?? 0),
            'ten_nhom' => $user->ten_nhom ?? '',
        ]];
    }

    public static function changePassword(int $id, string $oldPass, string $newPass, string $confirmPass): array
    {
        if ($newPass !== $confirmPass) return ['success' => false, 'message' => 'Mật khẩu xác nhận không khớp'];
        if (strlen($newPass) < 6) return ['success' => false, 'message' => 'Mật khẩu mới tối thiểu 6 ký tự'];

        $user = DM_NguoiDung_DAL::getById($id);
        if (!$user) return ['success' => false, 'message' => 'Không tìm thấy người dùng'];
        if (!password_verify($oldPass, $user->mat_khau)) {
            return ['success' => false, 'message' => 'Mật khẩu hiện tại không đúng'];
        }
        $hash = password_hash($newPass, AppConfig::PASSWORD_ALGO, ['cost' => AppConfig::PASSWORD_COST]);
        DM_NguoiDung_DAL::updatePassword($id, $hash, $id);
        DM_NhatKyHeThong_DAL::log($id, Constants::MODULE_HE_THONG, 'Đổi mật khẩu', 'DM_NGUOI_DUNG', $id);
        return ['success' => true, 'message' => 'Đổi mật khẩu thành công'];
    }

    public static function resetPassword(int $id, string $newPass, int $u): array
    {
        if (strlen($newPass) < 6) return ['success' => false, 'message' => 'Mật khẩu mới tối thiểu 6 ký tự'];
        $hash = password_hash($newPass, AppConfig::PASSWORD_ALGO, ['cost' => AppConfig::PASSWORD_COST]);
        DM_NguoiDung_DAL::updatePassword($id, $hash, $u);
        DM_NhatKyHeThong_DAL::log($u, Constants::MODULE_HE_THONG, "Reset mật khẩu người dùng id={$id}", 'DM_NGUOI_DUNG', $id);
        return ['success' => true, 'message' => 'Reset mật khẩu thành công'];
    }
}
