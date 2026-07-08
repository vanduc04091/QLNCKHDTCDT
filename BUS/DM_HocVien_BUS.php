<?php
require_once __DIR__ . '/../DAL/DM_HocVien_DAL.php';
require_once __DIR__ . '/../DAL/DM_NhanVien_DAL.php';
require_once __DIR__ . '/../DAL/DM_NhatKyHeThong_DAL.php';
require_once __DIR__ . '/../DAL/DM_DoiTuongHocVien_DAL.php';
require_once __DIR__ . '/../DAL/DT_KhoaHocChuongTrinh_DAL.php';
require_once __DIR__ . '/../DAL/DT_HocVienLop_DAL.php';

class DM_HocVien_BUS
{
    const MODULE_KEY = 'DM_HocVien';
    const AVATAR_MAX_SIZE = 3145728; // 3MB
    const AVATAR_ALLOWED = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    public static function insert(DM_HocVien_PUBLIC $e): array
    {
        $v = self::validate($e);
        if (!$v['success']) return $v;

        if (DM_HocVien_DAL::checkMaExists($e->ma_hv)) {
            return ['success' => false, 'message' => 'Mã học viên đã tồn tại'];
        }
        $dup = DM_HocVien_DAL::findDuplicate($e->cccd, $e->dien_thoai);
        if ($dup) {
            $by = ($e->cccd && $dup['cccd'] === trim((string)$e->cccd)) ? 'CCCD' : 'số điện thoại';
            return ['success' => false, 'message' => "Học viên đã tồn tại (trùng {$by}): {$dup['ho_ten']} — {$dup['ma_hv']}"];
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
        $dup = DM_HocVien_DAL::findDuplicate($e->cccd, $e->dien_thoai, $e->id);
        if ($dup) {
            $by = ($e->cccd && $dup['cccd'] === trim((string)$e->cccd)) ? 'CCCD' : 'số điện thoại';
            return ['success' => false, 'message' => "Học viên khác đã dùng {$by} này: {$dup['ho_ten']} — {$dup['ma_hv']}"];
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
        // la_nhan_vien chỉ là cờ phân biệt, không gắn với nhân viên cụ thể
        $e->nhan_vien_id = null;
        if ($e->ma_hv === '') {
            $e->ma_hv = 'HV' . date('ymd') . substr((string)microtime(true), -4);
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

    public static function getPaged(int $page, int $pageSize, string $search = '', int $daXoa = 0, int $doiTuongId = 0, int $laNhanVien = -1, string $tuNgay = '', string $denNgay = ''): array
    {
        return DM_HocVien_DAL::getPaged($page, $pageSize, $search, $daXoa, $doiTuongId, $laNhanVien, $tuNgay, $denNgay);
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
     * Import học viên từ file Excel theo mẫu "Danh sách nhập thông tin học viên".
     * Cột (0-based): 0 TT, 1 Họ tên, 2 Trạng thái, 3 Ngày sinh, 4 Giới tính,
     * 5 Trình độ CM, 6 Đối tượng, 7 Điện thoại, 8 Email, 9 CCCD, 10 Ngày cấp,
     * 11 Nơi cấp, 12 Đơn vị, 13 Địa chỉ, 14 Trường ĐT, 15 Năm TN,
     * 16 Khóa học, 17 CTĐT, 18 Ngày BĐ, 19 Ngày KT, 20 Địa điểm.
     *
     * Trả về: ['success'=>bool, 'message'=>str, 'data'=>[
     *    'created'=>int, 'skipped'=>int, 'enrolled'=>int,
     *    'rows'=>[ ['stt','ho_ten','status'=>'created|skipped|error','enroll'=>'ok|none|notfound','message'] ] ]]
     */
    public static function importExcel(string $filePath, int $userId): array
    {
        try {
            $rows = ExcelHelper::readRows($filePath);
        } catch (Throwable $ex) {
            return ['success' => false, 'message' => 'Không đọc được file: ' . $ex->getMessage()];
        }
        if (count($rows) < 3) {
            return ['success' => false, 'message' => 'File không có dữ liệu học viên (kiểm tra đúng mẫu).'];
        }

        // Bỏ 2 dòng đầu (tiêu đề lớn + header). Nhận diện header động để linh hoạt.
        $startIdx = 0;
        foreach ($rows as $i => $r) {
            $joined = mb_strtolower(implode('|', $r));
            if (strpos($joined, 'họ và tên') !== false || strpos($joined, 'ho va ten') !== false) {
                $startIdx = $i + 1;
                break;
            }
        }
        if ($startIdx === 0) $startIdx = 2; // fallback: mẫu chuẩn

        // Lookup maps
        $doiTuongMap = [];
        foreach (DM_DoiTuongHocVien_DAL::getCombo() as $d) {
            $doiTuongMap[self::norm($d['ten_doi_tuong'])] = (int)$d['id'];
            $doiTuongMap[self::norm($d['ma_doi_tuong'])] = (int)$d['id'];
        }
        $khctMap = []; // mã khóa|mã ctđt => khct.id
        foreach (DT_KhoaHocChuongTrinh_DAL::getCombo() as $k) {
            $key = self::norm($k['ma_khoa_hoc']) . '|' . self::norm($k['ma_chuong_trinh']);
            $khctMap[$key] = (int)$k['id'];
        }

        $created = 0; $skipped = 0; $enrolled = 0; $report = [];
        $db = Database::getConnection();

        for ($i = $startIdx; $i < count($rows); $i++) {
            $r = $rows[$i];
            $get = fn($idx) => isset($r[$idx]) ? trim((string)$r[$idx]) : '';

            $hoTen = $get(1);
            if ($hoTen === '') continue; // bỏ dòng trống
            $stt = $get(0) ?: (string)($i - $startIdx + 1);

            $cccd = $get(9);
            $sdt  = $get(7);

            // Trùng?
            $dup = DM_HocVien_DAL::findDuplicate($cccd, $sdt);
            if ($dup) {
                $by = ($cccd !== '' && $dup['cccd'] === $cccd) ? 'CCCD' : 'SĐT';
                $skipped++;
                $report[] = ['stt' => $stt, 'ho_ten' => $hoTen, 'status' => 'skipped', 'enroll' => 'none',
                    'message' => "Đã tồn tại (trùng {$by}): {$dup['ho_ten']} — {$dup['ma_hv']}"];
                continue;
            }

            $e = new DM_HocVien_PUBLIC();
            $e->ho_ten             = $hoTen;
            $e->ngay_sinh          = self::parseDate($get(3));
            $e->gioi_tinh          = self::normGioiTinh($get(4));
            $e->trinh_do_chuyen_mon = $get(5) ?: null;
            $e->doi_tuong_id       = $doiTuongMap[self::norm($get(6))] ?? null;
            $e->dien_thoai         = $sdt ?: null;
            $e->email              = $get(8) ?: null;
            $e->cccd               = $cccd ?: null;
            $e->cccd_ngay_cap      = self::parseDate($get(10));
            $e->cccd_noi_cap       = $get(11) ?: null;
            $e->don_vi_cong_tac    = $get(12) ?: null;
            $e->dia_chi            = $get(13) ?: null;
            $e->truong_dao_tao     = $get(14) ?: null;
            $e->nam_tot_nghiep     = ctype_digit($get(15)) ? (int)$get(15) : null;
            $e->trang_thai         = self::normTrangThai($get(2));
            $e->la_nhan_vien       = 0;
            $e->nguoi_tao          = $userId;
            $e->ma_hv              = 'HV' . date('ymd') . str_pad((string)($i), 3, '0', STR_PAD_LEFT) . substr((string)microtime(true), -3);

            // Email/SĐT không hợp lệ -> vẫn nhập nhưng bỏ giá trị sai (tránh chặn cả dòng)
            if ($e->email && !Helper::isEmail($e->email)) $e->email = null;

            // Ghi danh: tìm cặp (Khóa, CTĐT) theo mã
            $maKhoa = self::extractCode($get(16));
            $maCt   = self::extractCode($get(17));
            $khctId = 0;
            if ($maKhoa !== '' && $maCt !== '') {
                $khctId = $khctMap[self::norm($maKhoa) . '|' . self::norm($maCt)] ?? 0;
            }
            $enrollState = 'none';

            try {
                $db->beginTransaction();
                $hvId = DM_HocVien_DAL::insert($e);

                if ($maKhoa !== '' || $maCt !== '') {
                    // Có yêu cầu ghi danh
                    if ($khctId > 0) {
                        if (!DT_HocVienLop_DAL::checkExists($khctId, $hvId)) {
                            $hvl = new DT_HocVienLop_PUBLIC();
                            $hvl->khoa_hoc_chuong_trinh_id = $khctId;
                            $hvl->hoc_vien_id  = $hvId;
                            $hvl->ngay_ghi_danh = date('Y-m-d');
                            $hvl->ngay_bat_dau  = self::parseDate($get(18));
                            $hvl->ngay_ket_thuc = self::parseDate($get(19));
                            $hvl->trang_thai    = 1;
                            $hvl->nguoi_tao     = $userId;
                            DT_HocVienLop_DAL::insert($hvl);
                            $enrolled++;
                            $enrollState = 'ok';
                        } else {
                            $enrollState = 'ok';
                        }
                    } else {
                        $enrollState = 'notfound';
                    }
                }
                $db->commit();
            } catch (Throwable $ex) {
                if ($db->inTransaction()) $db->rollBack();
                $report[] = ['stt' => $stt, 'ho_ten' => $hoTen, 'status' => 'error', 'enroll' => 'none',
                    'message' => 'Lỗi lưu: ' . $ex->getMessage()];
                continue;
            }

            $created++;
            $msg = '';
            if ($enrollState === 'notfound') {
                $msg = 'Chưa ghi danh: không tìm thấy khóa/CTĐT "' . trim($get(16) . ' / ' . $get(17), ' /') . '"';
            } elseif ($enrollState === 'ok') {
                $msg = 'Đã tạo & ghi danh';
            } else {
                $msg = 'Đã tạo';
            }
            $report[] = ['stt' => $stt, 'ho_ten' => $hoTen, 'status' => 'created', 'enroll' => $enrollState, 'message' => $msg];
        }

        MemcachedHelper::deleteByPrefix('dm_hoc_vien:');
        DM_NhatKyHeThong_DAL::log($userId, Constants::MODULE_HE_THONG,
            "Import HV: tạo {$created}, bỏ qua {$skipped}, ghi danh {$enrolled}", 'DM_HOC_VIEN', 0);

        return ['success' => true,
            'message' => "Hoàn tất: tạo {$created}, bỏ qua {$skipped}, ghi danh {$enrolled}.",
            'data' => ['created' => $created, 'skipped' => $skipped, 'enrolled' => $enrolled, 'rows' => $report]];
    }

    /** Chuẩn hóa chuỗi để so khớp: bỏ khoảng trắng thừa + lowercase (giữ dấu). */
    private static function norm(?string $s): string
    {
        return mb_strtolower(trim(preg_replace('/\s+/u', ' ', (string)$s)));
    }

    /** Lấy phần mã đứng trước " - " trong "MÃ - Tên...". */
    private static function extractCode(string $s): string
    {
        $s = trim($s);
        if ($s === '') return '';
        // Tách theo " - " (dấu gạch có khoảng trắng 2 bên) hoặc ký tự gạch đầu tiên
        if (preg_match('/^(.*?)\s+[-–]\s+/u', $s, $m)) return trim($m[1]);
        return $s;
    }

    /** Parse ngày d/m/Y hoặc Y-m-d -> 'Y-m-d' (null nếu rỗng/không hợp lệ). */
    private static function parseDate(string $s): ?string
    {
        $s = trim($s);
        if ($s === '') return null;
        if (preg_match('#^(\d{1,2})[/.-](\d{1,2})[/.-](\d{4})$#', $s, $m)) {
            $d = (int)$m[1]; $mo = (int)$m[2]; $y = (int)$m[3];
            if (checkdate($mo, $d, $y)) return sprintf('%04d-%02d-%02d', $y, $mo, $d);
            return null;
        }
        if (preg_match('#^(\d{4})-(\d{1,2})-(\d{1,2})$#', $s, $m)) {
            if (checkdate((int)$m[2], (int)$m[3], (int)$m[1])) return sprintf('%04d-%02d-%02d', $m[1], $m[2], $m[3]);
        }
        return null;
    }

    /** "Nam"/"Nữ"/"M"/"F" -> 'Nam'/'Nữ' (null nếu không rõ). */
    private static function normGioiTinh(string $s): ?string
    {
        $s = self::norm($s);
        if ($s === '') return null;
        if (in_array($s, ['nam', 'm', 'male'], true)) return 'Nam';
        if (in_array($s, ['nữ', 'nu', 'f', 'female'], true)) return 'Nữ';
        return null;
    }

    /** Trạng thái text -> 1 (hoạt động) / 0 (ngừng). Mặc định 1. */
    private static function normTrangThai(string $s): int
    {
        $s = self::norm($s);
        if ($s === '') return 1;
        if (strpos($s, 'ngừng') !== false || strpos($s, 'ngung') !== false
            || strpos($s, 'khóa') !== false || strpos($s, 'khoa') !== false
            || $s === '0') return 0;
        return 1;
    }

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
