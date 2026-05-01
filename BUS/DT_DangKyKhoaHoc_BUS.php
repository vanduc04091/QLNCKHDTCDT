<?php
require_once __DIR__ . '/../DAL/DT_DangKyKhoaHoc_DAL.php';
require_once __DIR__ . '/../DAL/DT_KhoaHoc_DAL.php';
require_once __DIR__ . '/../DAL/DM_HocVien_DAL.php';
require_once __DIR__ . '/../DAL/DT_HocVienLop_DAL.php';
require_once __DIR__ . '/../DAL/DM_NhatKyHeThong_DAL.php';
require_once __DIR__ . '/DM_HocVien_BUS.php';
require_once __DIR__ . '/DT_HocVienLop_BUS.php';
require_once __DIR__ . '/../PUBLIC/Common/MailHelper.php';

class DT_DangKyKhoaHoc_BUS
{
    const MODULE_KEY = 'DT_DangKyKhoaHoc';
    const MAX_SIZE = 5 * 1024 * 1024;
    const ALLOWED = ['pdf', 'jpg', 'jpeg', 'png'];

    const TT_CHO     = 0;
    const TT_DA_DUYET = 1;
    const TT_TU_CHOI = 2;

    public static function uploadDir(): string
    {
        return __DIR__ . '/../assets/uploads/dangky/';
    }

    public static function genMaTraCuu(): string
    {
        // 12 ký tự hex + 4 số = 16 ký tự dễ đọc
        return strtoupper(bin2hex(random_bytes(6))) . sprintf('%04d', random_int(0, 9999));
    }

    /** Public: Tạo đăng ký (gọi từ form công khai). */
    public static function publicRegister(array $input, ?array $cccdFile, ?array $bcFile, ?string $ip): array
    {
        // Validate cơ bản
        $e = new DT_DangKyKhoaHoc_PUBLIC();
        $e->ho_ten          = trim($input['ho_ten'] ?? '');
        $e->ngay_sinh       = ($input['ngay_sinh'] ?? '') ?: null;
        $e->gioi_tinh       = ($input['gioi_tinh'] ?? '') ?: null;
        $e->cccd            = trim($input['cccd'] ?? '');
        $e->dien_thoai      = trim($input['dien_thoai'] ?? '') ?: null;
        $e->email           = trim($input['email'] ?? '');
        $e->dia_chi         = trim($input['dia_chi'] ?? '') ?: null;
        $e->don_vi_cong_tac = trim($input['don_vi_cong_tac'] ?? '') ?: null;
        $e->chuc_vu         = trim($input['chuc_vu'] ?? '') ?: null;
        $e->khoa_hoc_id     = (int)($input['khoa_hoc_id'] ?? 0);
        $e->ly_do_dang_ky   = trim($input['ly_do_dang_ky'] ?? '') ?: null;
        $e->ip_dang_ky      = $ip;

        if ($e->ho_ten === '')   return ['success' => false, 'message' => 'Vui lòng nhập họ tên'];
        if ($e->cccd === '' || !preg_match('/^\d{9,12}$/', $e->cccd)) {
            return ['success' => false, 'message' => 'CCCD không hợp lệ (9-12 chữ số)'];
        }
        if ($e->email === '' || !filter_var($e->email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'Email không hợp lệ'];
        }
        if ($e->khoa_hoc_id <= 0) return ['success' => false, 'message' => 'Vui lòng chọn khóa học'];

        // Khóa học phải đang active
        $kh = DT_KhoaHoc_DAL::getById($e->khoa_hoc_id);
        if (!$kh || $kh->da_xoa || (int)$kh->trang_thai !== 1) {
            return ['success' => false, 'message' => 'Khóa học không tồn tại hoặc không nhận đăng ký'];
        }

        // Anti-spam: chặn submit lặp 1 giờ
        if (DT_DangKyKhoaHoc_DAL::checkRecentByEmailOrCccd($e->email, $e->cccd, 1)) {
            return ['success' => false, 'message' => 'Bạn vừa đăng ký gần đây. Vui lòng kiểm tra email hoặc đợi 1 giờ trước khi đăng ký lại.'];
        }

        // Upload (optional)
        if ($cccdFile && !empty($cccdFile['tmp_name'])) {
            $up = self::handleUpload($cccdFile, 'cccd');
            if (!$up['success']) return $up;
            $e->cccd_file = $up['data']['file_name'];
        }
        if ($bcFile && !empty($bcFile['tmp_name'])) {
            $up = self::handleUpload($bcFile, 'bc');
            if (!$up['success']) return $up;
            $e->bang_cap_file = $up['data']['file_name'];
        }

        // Sinh mã tra cứu duy nhất
        do {
            $e->ma_tra_cuu = self::genMaTraCuu();
        } while (DT_DangKyKhoaHoc_DAL::getByMaTraCuu($e->ma_tra_cuu) !== null);

        $id = DT_DangKyKhoaHoc_DAL::insert($e);

        // Gửi mail xác nhận
        self::sendConfirmationMail($e, $kh);

        return ['success' => true, 'message' => 'Đăng ký thành công', 'data' => [
            'id' => $id,
            'ma_tra_cuu' => $e->ma_tra_cuu,
            'email' => $e->email,
        ]];
    }

    /**
     * Scan trùng: tìm HV chính thức có CCCD/SĐT trùng với đơn đăng ký.
     * Trả về mảng candidates để admin chọn link/tạo mới.
     */
    public static function scanDuplicates(int $dkId): array
    {
        $dk = DT_DangKyKhoaHoc_DAL::getById($dkId);
        if (!$dk) return ['success' => false, 'message' => 'Không tìm thấy đăng ký'];

        $byCccd = $dk->cccd ? DM_HocVien_BUS::findByCccd($dk->cccd) : [];
        $bySdt  = $dk->dien_thoai ? DM_HocVien_BUS::findByDienThoai($dk->dien_thoai) : [];

        // Merge unique theo id, ghi rõ matched_by
        $merged = [];
        foreach ($byCccd as $hv) {
            $merged[(int)$hv['id']] = ['hv' => $hv, 'matched_by' => ['cccd']];
        }
        foreach ($bySdt as $hv) {
            $id = (int)$hv['id'];
            if (isset($merged[$id])) $merged[$id]['matched_by'][] = 'sdt';
            else $merged[$id] = ['hv' => $hv, 'matched_by' => ['sdt']];
        }
        return ['success' => true, 'data' => [
            'dang_ky' => $dk,
            'matches' => array_values($merged),
        ]];
    }

    /**
     * Admin duyệt đăng ký:
     * @param int  $existingHvId    - Nếu >0 thì link vào HV cũ thay vì tạo mới
     * @param bool $laNhanVien      - Tạo HV với cờ "là nhân viên"
     * @param int  $nhanVienId      - Khi laNhanVien=true, link vào DM_NHAN_VIEN
     */
    public static function approve(
        int $id, int $userId, ?int $lopId, ?string $note,
        int $existingHvId = 0, bool $laNhanVien = false, int $nhanVienId = 0
    ): array {
        $dk = DT_DangKyKhoaHoc_DAL::getById($id);
        if (!$dk) return ['success' => false, 'message' => 'Không tìm thấy đăng ký'];
        if ($dk->trang_thai === self::TT_DA_DUYET) {
            return ['success' => false, 'message' => 'Đăng ký đã được duyệt trước đó'];
        }
        if ($laNhanVien && !$nhanVienId && $existingHvId === 0) {
            return ['success' => false, 'message' => 'Đã chọn "là nhân viên" nhưng chưa chọn nhân viên cụ thể'];
        }

        $hocVienId = 0;

        try {
            Database::beginTransaction();

            if ($existingHvId > 0) {
                $existing = DM_HocVien_BUS::getById($existingHvId);
                if (!$existing) {
                    Database::rollBack();
                    return ['success' => false, 'message' => 'Học viên được liên kết không tồn tại'];
                }
                $hocVienId = $existingHvId;
                $note = ($note ? $note . ' | ' : '') . "Liên kết với HV có sẵn: {$existing->ma_hv}";
            } else {
                $hv = new DM_HocVien_PUBLIC();
                $hv->ma_hv          = '';
                $hv->ho_ten         = $dk->ho_ten;
                $hv->ngay_sinh      = $dk->ngay_sinh;
                $hv->gioi_tinh      = $dk->gioi_tinh;
                $hv->dien_thoai     = $dk->dien_thoai;
                $hv->email          = $dk->email;
                $hv->cccd           = $dk->cccd;
                $hv->dia_chi        = $dk->dia_chi;
                $hv->don_vi_cong_tac = $dk->don_vi_cong_tac;
                $hv->chuc_vu        = $dk->chuc_vu;
                $hv->la_nhan_vien   = $laNhanVien ? 1 : 0;
                $hv->nhan_vien_id   = $laNhanVien && $nhanVienId > 0 ? $nhanVienId : null;
                $hv->trang_thai     = 1;
                $hv->nguoi_tao      = $userId;

                $hvRes = DM_HocVien_BUS::insert($hv);
                if (!$hvRes['success']) {
                    Database::rollBack();
                    return ['success' => false, 'message' => 'Không tạo được học viên: ' . $hvRes['message']];
                }
                $hocVienId = (int)$hvRes['data']['id'];
            }

            if ($lopId) {
                $hvl = new DT_HocVienLop_PUBLIC();
                $hvl->lop_hoc_id = $lopId;
                $hvl->hoc_vien_id = $hocVienId;
                $hvl->ngay_ghi_danh = date('Y-m-d');
                $hvl->trang_thai = 1;
                $hvl->nguoi_tao = $userId;
                $r = DT_HocVienLop_BUS::insert($hvl);
                if (!$r['success']) {
                    $note = ($note ? $note . ' | ' : '') . 'Cảnh báo ghi danh lớp: ' . $r['message'];
                    $lopId = null;
                }
            }

            DT_DangKyKhoaHoc_DAL::approve($id, $userId, $hocVienId, $lopId, $note);

            Database::commit();
        } catch (Throwable $ex) {
            Database::rollBack();
            return ['success' => false, 'message' => 'Lỗi duyệt đăng ký: ' . $ex->getMessage()];
        }

        // Side effects ngoài transaction (log + mail không cần atomic)
        DM_NhatKyHeThong_DAL::log($userId, Constants::MODULE_DAO_TAO,
            "Duyệt đăng ký id={$id}: {$dk->ho_ten}" . ($existingHvId ? " (link HV #{$existingHvId})" : ' (tạo HV mới)'),
            'DT_DANG_KY_KHOA_HOC', $id);
        self::sendApprovalMail($dk, $hocVienId);

        return ['success' => true, 'message' => $existingHvId
            ? 'Đã duyệt đăng ký và liên kết với học viên có sẵn'
            : 'Đã duyệt đăng ký và tạo học viên mới',
            'data' => ['hoc_vien_id' => $hocVienId, 'linked' => $existingHvId > 0],
        ];
    }

    public static function reject(int $id, int $userId, string $note): array
    {
        if (trim($note) === '') return ['success' => false, 'message' => 'Vui lòng nhập lý do từ chối'];
        $dk = DT_DangKyKhoaHoc_DAL::getById($id);
        if (!$dk) return ['success' => false, 'message' => 'Không tìm thấy đăng ký'];
        DT_DangKyKhoaHoc_DAL::reject($id, $userId, $note);
        DM_NhatKyHeThong_DAL::log($userId, Constants::MODULE_DAO_TAO,
            "Từ chối đăng ký id={$id}", 'DT_DANG_KY_KHOA_HOC', $id);
        self::sendRejectionMail($dk, $note);
        return ['success' => true, 'message' => 'Đã từ chối đăng ký'];
    }

    public static function trash(int $id, int $u): array
    {
        DT_DangKyKhoaHoc_DAL::trash($id, $u);
        return ['success' => true, 'message' => 'Đã chuyển vào thùng rác'];
    }

    public static function getById(int $id): ?DT_DangKyKhoaHoc_DTO { return DT_DangKyKhoaHoc_DAL::getById($id); }
    public static function getByMaTraCuu(string $ma): ?DT_DangKyKhoaHoc_DTO { return DT_DangKyKhoaHoc_DAL::getByMaTraCuu($ma); }
    public static function getPaged(int $p, int $s, array $opts = [], int $dx = 0): array { return DT_DangKyKhoaHoc_DAL::getPaged($p, $s, $opts, $dx); }
    public static function getStats(): array { return DT_DangKyKhoaHoc_DAL::getStats(); }

    private static function handleUpload(array $file, string $prefix): array
    {
        if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'message' => 'Lỗi upload file'];
        }
        if ($file['size'] <= 0) return ['success' => false, 'message' => 'File rỗng'];
        if ($file['size'] > self::MAX_SIZE) {
            return ['success' => false, 'message' => 'File quá lớn (tối đa 5MB)'];
        }
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, self::ALLOWED, true)) {
            return ['success' => false, 'message' => 'Định dạng không cho phép. Chỉ: ' . implode(', ', self::ALLOWED)];
        }
        $dir = self::uploadDir();
        if (!is_dir($dir)) @mkdir($dir, 0755, true);
        $newName = $prefix . '_' . date('Ymd_His') . '_' . bin2hex(random_bytes(5)) . '.' . $ext;
        if (!@move_uploaded_file($file['tmp_name'], $dir . $newName)) {
            return ['success' => false, 'message' => 'Không lưu được file'];
        }
        return ['success' => true, 'data' => ['file_name' => $newName]];
    }

    // ============ Mail ============
    private static function publicBaseUrl(): string
    {
        $v = self::config('PUBLIC_BASE_URL');
        return $v ?: AppConfig::APP_URL;
    }

    private static function config(string $key): ?string
    {
        $stmt = Database::getConnection()->prepare("SELECT gia_tri FROM DM_CAU_HINH WHERE ma_cau_hinh=:k");
        $stmt->execute([':k' => $key]);
        $v = $stmt->fetchColumn();
        return $v !== false ? $v : null;
    }

    private static function sendConfirmationMail(DT_DangKyKhoaHoc_PUBLIC $e, $kh): void
    {
        $base = rtrim(self::publicBaseUrl(), '/');
        $link = $base . '/GUI/public/tra_cuu.php?ma=' . urlencode($e->ma_tra_cuu);
        $subject = 'Xác nhận đăng ký khóa học';
        $body = "<p>Xin chào <strong>" . htmlspecialchars($e->ho_ten) . "</strong>,</p>"
              . "<p>Bạn đã đăng ký khóa học <strong>" . htmlspecialchars($kh->ten_khoa_hoc) . "</strong> thành công.</p>"
              . "<p>Mã tra cứu: <strong style=\"font-size:18px;color:#2563eb\">{$e->ma_tra_cuu}</strong></p>"
              . "<p>Vui lòng <strong>lưu lại mã này</strong> để theo dõi trạng thái xét duyệt.</p>"
              . "<p>Tra cứu trạng thái: <a href=\"{$link}\">{$link}</a> (cần CCCD để xác thực)</p>"
              . "<p>Trân trọng.</p>";
        MailHelper::send($e->email, $e->ho_ten, $subject, $body);
    }

    private static function sendApprovalMail(DT_DangKyKhoaHoc_DTO $dk, int $hocVienId): void
    {
        $base = rtrim(self::publicBaseUrl(), '/');
        $link = $base . '/GUI/public/tra_cuu.php?ma=' . urlencode($dk->ma_tra_cuu);
        $subject = 'Đăng ký được duyệt - ' . ($dk->ten_khoa_hoc ?? '');
        $body = "<p>Xin chào <strong>" . htmlspecialchars($dk->ho_ten) . "</strong>,</p>"
              . "<p>Đăng ký khóa học <strong>" . htmlspecialchars($dk->ten_khoa_hoc ?? '') . "</strong> của bạn đã được <strong style=\"color:#16a34a\">DUYỆT</strong>.</p>"
              . "<p>Bạn có thể tra cứu lịch học, điểm danh, kết quả học tập và chứng chỉ tại đây:<br>"
              . "<a href=\"{$link}\">{$link}</a></p>"
              . "<p>Mã tra cứu: <strong>{$dk->ma_tra_cuu}</strong> (cần kết hợp với CCCD)</p>"
              . "<p>Trân trọng.</p>";
        MailHelper::send($dk->email, $dk->ho_ten, $subject, $body);
    }

    private static function sendRejectionMail(DT_DangKyKhoaHoc_DTO $dk, string $reason): void
    {
        $subject = 'Đăng ký không được chấp nhận - ' . ($dk->ten_khoa_hoc ?? '');
        $body = "<p>Xin chào <strong>" . htmlspecialchars($dk->ho_ten) . "</strong>,</p>"
              . "<p>Rất tiếc, đăng ký khóa học <strong>" . htmlspecialchars($dk->ten_khoa_hoc ?? '') . "</strong> của bạn không được chấp nhận.</p>"
              . "<p><strong>Lý do:</strong> " . nl2br(htmlspecialchars($reason)) . "</p>"
              . "<p>Bạn có thể liên hệ với chúng tôi để biết thêm chi tiết.</p>";
        MailHelper::send($dk->email, $dk->ho_ten, $subject, $body);
    }
}
