<?php
require_once __DIR__ . '/../DAL/DM_HocVien_DAL.php';
require_once __DIR__ . '/../DAL/DT_HocVienLop_DAL.php';

/**
 * DT_PhieuIn_BUS — Quản lý & điền các mẫu phiếu in cho học viên.
 * Mẫu là file HTML trong folder mps/ với placeholder {{key}}. Khai báo ở mps/manifest.php.
 */
class DT_PhieuIn_BUS
{
    const MODULE_KEY = 'DM_HocVien'; // dùng chung quyền với module học viên
    const MPS_DIR = __DIR__ . '/../mps';

    /** Danh sách mẫu phiếu (từ manifest). */
    public static function getTemplates(): array
    {
        $file = self::MPS_DIR . '/manifest.php';
        if (!is_file($file)) return [];
        $list = require $file;
        if (!is_array($list)) return [];
        // Chỉ trả mẫu có file thật
        return array_values(array_filter($list, function ($t) {
            return !empty($t['file']) && is_file(self::MPS_DIR . '/' . basename($t['file']));
        }));
    }

    private static function findTemplate(string $key): ?array
    {
        foreach (self::getTemplates() as $t) {
            if (($t['key'] ?? '') === $key) return $t;
        }
        return null;
    }

    /**
     * Render 1 phiếu đã điền dữ liệu.
     * @return array ['success'=>bool, 'html'=>string, 'message'=>string]
     */
    public static function render(string $templateKey, int $hocVienId, int $khctId = 0): array
    {
        $tpl = self::findTemplate($templateKey);
        if (!$tpl) return ['success' => false, 'message' => 'Không tìm thấy mẫu phiếu'];

        $hv = DM_HocVien_DAL::getById($hocVienId);
        if (!$hv) return ['success' => false, 'message' => 'Không tìm thấy học viên'];

        // Ghi danh được chọn (nếu có) để lấy khóa/CTĐT/thời gian/địa điểm
        $enroll = null;
        if ($khctId > 0) {
            foreach (DT_HocVienLop_DAL::getByHocVien($hocVienId) as $e) {
                if ((int)$e['khoa_hoc_chuong_trinh_id'] === $khctId) { $enroll = $e; break; }
            }
        }

        $path = self::MPS_DIR . '/' . basename($tpl['file']);
        $html = file_get_contents($path);
        if ($html === false) return ['success' => false, 'message' => 'Không đọc được mẫu phiếu'];

        $data = self::buildData($hv, $enroll);
        $html = self::fill($html, $data);
        return ['success' => true, 'html' => $html];
    }

    /** Bảng key => giá trị đã escape để chèn vào HTML. */
    private static function buildData($hv, ?array $enroll): array
    {
        $now = time();
        $d = [
            // Thông tin người thực hành (học viên)
            'ho_ten'            => $hv->ho_ten,
            'ngay_sinh'         => self::fmtDate($hv->ngay_sinh),
            'gioi_tinh'         => $hv->gioi_tinh,
            'cccd'              => $hv->cccd,
            'cccd_ngay_cap'     => self::fmtDate($hv->cccd_ngay_cap),
            'cccd_noi_cap'      => $hv->cccd_noi_cap,
            'dia_chi'           => $hv->dia_chi,
            'don_vi_cong_tac'   => $hv->don_vi_cong_tac,
            'chuc_vu'           => $hv->chuc_vu,
            'trinh_do_chuyen_mon' => $hv->trinh_do_chuyen_mon,
            'truong_dao_tao'    => $hv->truong_dao_tao,
            'nam_tot_nghiep'    => $hv->nam_tot_nghiep,
            'doi_tuong'         => $hv->ten_doi_tuong ?? '',
            'ma_hv'             => $hv->ma_hv,
            'email'             => $hv->email,
            'dien_thoai'        => $hv->dien_thoai,
            // URL ảnh đại diện (rỗng nếu chưa có ảnh)
            'avatar_url'        => $hv->avatar ? AppConfig::baseUrl('assets/uploads/hocvien/' . $hv->avatar) : '',

            // Thời điểm in
            'ngay'  => date('d', $now),
            'thang' => date('m', $now),
            'nam'   => date('Y', $now),

            // Ô cần điền tay (để trống)
            'co_quan_chu_quan'  => '',
            'co_so_kbcb'        => 'Bệnh viện Hữu Nghị Đa Khoa Nghệ An',
            'dia_danh'          => 'Vinh Phú',
            'so_phieu'          => '',
            'nguoi_huong_dan'   => '',
            'cchn_nguoi_huong_dan' => '',
            'pham_vi_hd_nguoi_huong_dan' => '',
            'khoa_nguoi_huong_dan' => '',
            'nang_luc'          => '',
            'y_thuc'            => '',
        ];

        // Trường theo ghi danh (khóa/CTĐT). Nếu chưa chọn ghi danh -> để trống.
        if ($enroll) {
            $d['ngay_bat_dau']  = self::fmtDate($enroll['ngay_bat_dau'] ?? null);
            $d['ngay_ket_thuc'] = self::fmtDate($enroll['ngay_ket_thuc'] ?? null);
            $d['dia_diem']      = $enroll['dia_diem'] ?? '';
            $d['ma_khoa_hoc']   = $enroll['ma_khoa_hoc'] ?? '';
            $d['ten_khoa_hoc']  = $enroll['ten_khoa_hoc'] ?? '';
            $d['ma_chuong_trinh']  = $enroll['ma_chuong_trinh'] ?? '';
            $d['ten_chuong_trinh'] = $enroll['ten_chuong_trinh'] ?? '';
            // Chuyên khoa đăng ký = CTĐT
            $d['chuyen_khoa']   = trim(($enroll['ma_chuong_trinh'] ?? '') . ' - ' . ($enroll['ten_chuong_trinh'] ?? ''), ' -');
        } else {
            foreach (['ngay_bat_dau','ngay_ket_thuc','dia_diem','ma_khoa_hoc','ten_khoa_hoc',
                      'ma_chuong_trinh','ten_chuong_trinh','chuyen_khoa'] as $k) {
                $d[$k] = '';
            }
        }

        // Escape toàn bộ cho an toàn khi chèn vào HTML
        foreach ($d as $k => $v) {
            $d[$k] = htmlspecialchars((string)($v ?? ''), ENT_QUOTES, 'UTF-8');
        }

        // avatar_html: ảnh nếu có, không thì khối chữ cái đầu (đã escape ở trên nên tự dựng HTML sau)
        if ($d['avatar_url'] !== '') {
            $d['avatar_html'] = '<img src="' . $d['avatar_url'] . '" alt="Ảnh học viên">';
        } else {
            $ini = htmlspecialchars(self::initials($hv->ho_ten), ENT_QUOTES, 'UTF-8');
            $d['avatar_html'] = '<div class="thv-noimg">' . $ini . '</div>';
        }

        return $d;
    }

    /** Lấy 2 chữ cái đầu của họ tên (fallback ảnh thẻ). */
    private static function initials(?string $name): string
    {
        $name = trim(preg_replace('/\s+/u', ' ', (string)$name));
        if ($name === '') return 'HV';
        $parts = explode(' ', $name);
        if (count($parts) === 1) return mb_strtoupper(mb_substr($parts[0], 0, 2, 'UTF-8'), 'UTF-8');
        $a = mb_substr($parts[count($parts) - 2], 0, 1, 'UTF-8');
        $b = mb_substr($parts[count($parts) - 1], 0, 1, 'UTF-8');
        return mb_strtoupper($a . $b, 'UTF-8');
    }

    /** Thay {{key}} bằng giá trị; key không có -> để trống. */
    private static function fill(string $html, array $data): string
    {
        return preg_replace_callback('/\{\{\s*([a-zA-Z0-9_]+)\s*\}\}/', function ($m) use ($data) {
            return $data[$m[1]] ?? '';
        }, $html);
    }

    private static function fmtDate(?string $d): string
    {
        if (empty($d)) return '';
        $ts = strtotime($d);
        return $ts ? date('d/m/Y', $ts) : '';
    }
}
