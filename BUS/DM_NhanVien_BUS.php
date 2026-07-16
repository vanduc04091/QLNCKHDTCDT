<?php
require_once __DIR__ . '/../DAL/DM_NhanVien_DAL.php';
require_once __DIR__ . '/../DAL/DM_NhatKyHeThong_DAL.php';

class DM_NhanVien_BUS
{
    const MODULE_KEY = 'DM_NhanVien';

    public static function insert(DM_NhanVien_PUBLIC $e): array
    {
        $e->ma_nv = trim($e->ma_nv);
        $e->ho_ten = trim($e->ho_ten);
        if ($e->ma_nv === '' || $e->ho_ten === '') {
            return ['success' => false, 'message' => 'Mã NV và họ tên không được để trống'];
        }
        if ($e->benh_vien_id <= 0) return ['success' => false, 'message' => 'Chưa chọn bệnh viện'];
        if ($e->email && !Helper::isEmail($e->email)) {
            return ['success' => false, 'message' => 'Email không hợp lệ'];
        }
        if (DM_NhanVien_DAL::checkMaExists($e->benh_vien_id, $e->ma_nv)) {
            return ['success' => false, 'message' => 'Mã nhân viên đã tồn tại trong bệnh viện này'];
        }
        $id = DM_NhanVien_DAL::insert($e);
        MemcachedHelper::deleteByPrefix('dm_nhan_vien:');
        DM_NhatKyHeThong_DAL::log($e->nguoi_tao ?? 0, Constants::MODULE_HE_THONG, "Thêm NV: {$e->ho_ten}", 'DM_NHAN_VIEN', $id);
        return ['success' => true, 'message' => 'Thêm nhân viên thành công', 'data' => ['id' => $id]];
    }

    public static function update(DM_NhanVien_PUBLIC $e): array
    {
        if (!$e->id) return ['success' => false, 'message' => 'Thiếu ID'];
        if ($e->email && !Helper::isEmail($e->email)) {
            return ['success' => false, 'message' => 'Email không hợp lệ'];
        }
        if (DM_NhanVien_DAL::checkMaExists($e->benh_vien_id, $e->ma_nv, $e->id)) {
            return ['success' => false, 'message' => 'Mã nhân viên đã tồn tại'];
        }
        DM_NhanVien_DAL::update($e);
        MemcachedHelper::deleteByPrefix('dm_nhan_vien:');
        return ['success' => true, 'message' => 'Cập nhật thành công'];
    }

    public static function trash(int $id, int $u): array
    {
        DM_NhanVien_DAL::trash($id, $u);
        MemcachedHelper::deleteByPrefix('dm_nhan_vien:');
        DM_NhatKyHeThong_DAL::log($u, Constants::MODULE_HE_THONG, "Xóa tạm NV id={$id}", 'DM_NHAN_VIEN', $id);
        return ['success' => true, 'message' => 'Đã chuyển vào thùng rác'];
    }

    public static function restore(int $id, int $u): array
    {
        DM_NhanVien_DAL::restore($id, $u);
        MemcachedHelper::deleteByPrefix('dm_nhan_vien:');
        return ['success' => true, 'message' => 'Đã khôi phục'];
    }

    public static function delete(int $id): array
    {
        try {
            DM_NhanVien_DAL::delete($id);
            MemcachedHelper::deleteByPrefix('dm_nhan_vien:');
            return ['success' => true, 'message' => 'Đã xóa vĩnh viễn'];
        } catch (Throwable $ex) {
            return ['success' => false, 'message' => 'Không thể xóa: dữ liệu đang được tham chiếu'];
        }
    }

    public static function getById(int $id): ?DM_NhanVien_DTO
    {
        return DM_NhanVien_DAL::getById($id);
    }

    public static function getPaged(int $page, int $pageSize, string $search = '', int $daXoa = 0, int $khoaPhongId = 0): array
    {
        return DM_NhanVien_DAL::getPaged($page, $pageSize, $search, $daXoa, $khoaPhongId);
    }

    public static function getCombo(int $khoaPhongId = 0): array
    {
        return DM_NhanVien_DAL::getCombo($khoaPhongId);
    }

    public static function search(string $keyword = '', int $khoaPhongId = 0, int $limit = 20): array
    {
        return DM_NhanVien_DAL::search($keyword, $khoaPhongId, $limit);
    }

    public static function getBrief(int $id): ?array
    {
        return DM_NhanVien_DAL::getBrief($id);
    }

    // ================= IMPORT EXCEL =================

    /** Chuẩn hóa tên khoa để so khớp: bỏ "khoa/phòng/trung tâm", dấu, ký tự thừa. */
    private static function normKhoa(string $s): string
    {
        $s = mb_strtolower(trim($s), 'UTF-8');
        $s = preg_replace('/^(khoa|phòng|phong|trung tâm|trung tam|tt|đơn vị|don vi|ban)\s+/u', '', $s);
        $s = preg_replace('/\s*-\s*trung tâm.*$/u', '', $s); // bỏ đuôi "- Trung tâm xét nghiệm"
        $s = preg_replace('/[^\p{L}\p{N}]+/u', ' ', $s);
        return trim(preg_replace('/\s+/u', ' ', $s));
    }

    /** dd/mm/yyyy hoặc dd-mm-yyyy -> Y-m-d; rỗng/không hợp lệ -> null. */
    private static function parseDate(?string $s): ?string
    {
        $s = trim((string)$s);
        if ($s === '') return null;
        if (preg_match('#^(\d{1,2})[/-](\d{1,2})[/-](\d{4})$#', $s, $m)) {
            $d = sprintf('%04d-%02d-%02d', $m[3], $m[2], $m[1]);
            return checkdate((int)$m[2], (int)$m[1], (int)$m[3]) ? $d : null;
        }
        if (preg_match('#^\d{4}-\d{2}-\d{2}$#', $s)) return $s;
        return null;
    }

    /**
     * Import danh sách người hành nghề từ file .xlsx.
     * Cấu trúc (chốt 16.7.2026): A=TT, B=MNV, C=Họ tên, D=K/P/TT, E=Văn bằng,
     * F=Ngày sinh, G=Phạm vi hành nghề, H=Số CCHN, I=Ngày cấp CCHN,
     * J=QĐ bổ sung, K=Điều chỉnh phạm vi, L=Ngày điều chỉnh, M=Chuyên khoa cập nhật.
     *
     * @return array ['success','message','summary'=>[them,loi,bo_qua,khong_map_khoa], 'rows'=>[...]]
     */
    public static function importExcel(string $path, int $userId): array
    {
        require_once __DIR__ . '/../DAL/DM_KhoaPhong_DAL.php';

        try { $data = ExcelHelper::readRows($path); }
        catch (Throwable $ex) { return ['success' => false, 'message' => 'Không đọc được file: ' . $ex->getMessage()]; }
        if (count($data) < 4) return ['success' => false, 'message' => 'File không có dữ liệu (cần theo mẫu người hành nghề).'];

        // Bảng khoa để match: 2 chỉ mục — chuẩn hóa có dấu-cách, và bỏ hết khoảng trắng
        $khoaMap = []; $khoaMapTight = [];
        foreach (DM_KhoaPhong_DAL::getCombo() as $k) {
            $n = self::normKhoa($k['ten_khoa']);
            $khoaMap[$n] = (int)$k['id'];
            $khoaMapTight[str_replace(' ', '', $n)] = (int)$k['id'];
        }

        $them = 0; $loi = 0; $boqua = 0; $khongMapKhoa = 0;
        $rows = [];
        $seenMa = [];

        // Bỏ 3 dòng đầu (tiêu đề + header). Header ở dòng index 2.
        for ($i = 3; $i < count($data); $i++) {
            $r = $data[$i];
            $maNv  = trim($r[1] ?? '');
            $hoTen = trim($r[2] ?? '');
            if ($maNv === '' && $hoTen === '') continue; // dòng trống
            $stt = ($r[0] ?? '') !== '' ? $r[0] : ($i - 2);

            if ($hoTen === '') { $loi++; $rows[] = ['stt'=>$stt,'ma'=>$maNv,'ten'=>'','trang_thai'=>'loi','ghi_chu'=>'Thiếu họ tên']; continue; }
            if ($maNv === '')  { $maNv = 'NV' . str_pad((string)($i - 2), 5, '0', STR_PAD_LEFT); }

            if (isset($seenMa[$maNv])) { $boqua++; $rows[] = ['stt'=>$stt,'ma'=>$maNv,'ten'=>$hoTen,'trang_thai'=>'bo_qua','ghi_chu'=>'Trùng mã trong file']; continue; }

            $khoaText = trim($r[3] ?? '');
            $khoaId = null; $mapNote = '';
            if ($khoaText !== '') {
                $nk = self::normKhoa($khoaText);
                $khoaId = $khoaMap[$nk] ?? $khoaMapTight[str_replace(' ', '', $nk)] ?? null;
                if (!$khoaId) { $khongMapKhoa++; $mapNote = 'Chưa map được khoa "' . $khoaText . '"'; }
            }

            $e = new DM_NhanVien_PUBLIC();
            $e->benh_vien_id = 1;
            $e->ma_nv = $maNv;
            $e->ho_ten = $hoTen;
            $e->khoa_phong_id = $khoaId;
            $e->khoa_phong_text = $khoaText ?: null;
            $e->trinh_do = trim($r[4] ?? '') ?: null;                 // Văn bằng chuyên môn
            $e->ngay_sinh = self::parseDate($r[5] ?? '');
            $e->pham_vi_hanh_nghe = trim($r[6] ?? '') ?: null;
            $e->so_cchn = trim($r[7] ?? '') ?: null;
            $e->ngay_cap_cchn = self::parseDate($r[8] ?? '');
            $e->qd_bo_sung_pham_vi = trim($r[9] ?? '') ?: null;
            $e->dieu_chinh_pham_vi = trim($r[10] ?? '') ?: null;
            $e->ngay_dieu_chinh = self::parseDate($r[11] ?? '');
            $e->chuyen_khoa_cap_nhat = trim($r[12] ?? '') ?: null;
            $e->chuyen_khoa = $e->chuyen_khoa_cap_nhat;               // để combo/tìm kiếm cũ vẫn có
            $e->trang_thai = 1;
            $e->nguoi_tao = $userId;

            try {
                Database::beginTransaction();
                if (DM_NhanVien_DAL::checkMaExists(1, $maNv)) {
                    Database::rollBack();
                    $boqua++; $rows[] = ['stt'=>$stt,'ma'=>$maNv,'ten'=>$hoTen,'trang_thai'=>'bo_qua','ghi_chu'=>'Mã đã tồn tại trong DB'];
                    continue;
                }
                DM_NhanVien_DAL::insert($e);
                Database::commit();
                $seenMa[$maNv] = true;
                $them++;
                $rows[] = ['stt'=>$stt,'ma'=>$maNv,'ten'=>$hoTen,'trang_thai'=>($khoaId?'ok':'ok_note'),'ghi_chu'=>($mapNote ?: 'Đã thêm')];
            } catch (Throwable $ex) {
                Database::rollBack();
                $loi++;
                $rows[] = ['stt'=>$stt,'ma'=>$maNv,'ten'=>$hoTen,'trang_thai'=>'loi','ghi_chu'=>'Lỗi: ' . $ex->getMessage()];
            }
        }

        MemcachedHelper::deleteByPrefix('dm_nhan_vien:');
        DM_NhatKyHeThong_DAL::log($userId, Constants::MODULE_HE_THONG, "Import NV: thêm {$them}, lỗi {$loi}, bỏ qua {$boqua}", 'DM_NHAN_VIEN', 0);

        return [
            'success' => true,
            'message' => "Đã thêm {$them} nhân viên" . ($khongMapKhoa ? ", {$khongMapKhoa} chưa map được khoa" : '') . ($boqua ? ", bỏ qua {$boqua}" : '') . ($loi ? ", lỗi {$loi}" : ''),
            'summary' => ['them' => $them, 'loi' => $loi, 'bo_qua' => $boqua, 'khong_map_khoa' => $khongMapKhoa],
            'rows' => $rows,
        ];
    }
}
