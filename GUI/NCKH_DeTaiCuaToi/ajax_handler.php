<?php
/**
 * Ajax handler — Module "Đề tài của tôi" cho nhân viên.
 * Permission: NCKH_DeTaiCuaToi (đã cấp cho mọi nhóm).
 * Tất cả thao tác giới hạn ở đề tài do user tạo + trạng thái Nhap/TuChoi.
 */
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../BUS/NCKH_DeTai_BUS.php';
require_once __DIR__ . '/../../BUS/NCKH_ThanhVien_BUS.php';
require_once __DIR__ . '/../../BUS/NCKH_HoiDong_BUS.php';
require_once __DIR__ . '/../../BUS/NCKH_TaiLieu_BUS.php';
require_once __DIR__ . '/../../BUS/DM_NCKH_CapDo_BUS.php';
require_once __DIR__ . '/../../BUS/DM_NCKH_TheLoai_BUS.php';
require_once __DIR__ . '/../../BUS/DM_KhoaPhong_BUS.php';
require_once __DIR__ . '/../../BUS/DM_NhanVien_BUS.php';
require_once __DIR__ . '/../../BUS/NCKH_DotDangKy_BUS.php';

Helper::requireAjaxCsrf();
$action = Helper::post('action', '');
$u = SessionHelper::userId();
$MODULE = 'NCKH_DeTaiCuaToi';
PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XEM);

$UPLOAD_DIR = __DIR__ . '/../../assets/uploads/nckh';

/* Helper: chỉ cho thao tác nếu user là nguoi_tao và đề tài đang Nhap/TuChoi */
function ensureMine(int $deTaiId, int $userId): void {
    if (!NCKH_DeTai_BUS::canEditByOwner($deTaiId, $userId)) {
        ResponseHelper::error('Đề tài không tồn tại hoặc đã gửi duyệt — không thể chỉnh sửa', 403);
    }
}

/**
 * Kiểm tra phase Submit hoặc Edit của đợt phải đang mở.
 * - Đề tài chưa có dot_dang_ky_id: bỏ qua check (legacy data).
 * - Đề tài Nhap: chấp nhận phase Submit hoặc Edit.
 * - Đề tài TuChoi: chỉ chấp nhận phase Edit.
 */
function ensurePhaseEditable(int $deTaiId): void {
    $dt = NCKH_DeTai_BUS::getById($deTaiId);
    if (!$dt || !$dt->dot_dang_ky_id) return;
    // Thử phase Edit trước
    $check = NCKH_DotDangKy_BUS::checkPhaseOpen((int)$dt->dot_dang_ky_id, NCKH_DotDangKy_BUS::HV_EDIT);
    if ($check['ok']) return;
    // Nếu đề tài còn Nhap thì cũng chấp nhận phase Submit
    if ($dt->trang_thai_duyet === 'Nhap') {
        $checkS = NCKH_DotDangKy_BUS::checkPhaseOpen((int)$dt->dot_dang_ky_id, NCKH_DotDangKy_BUS::HV_SUBMIT);
        if ($checkS['ok']) return;
    }
    ResponseHelper::error($check['message'] ?: 'Hiện không có giai đoạn chỉnh sửa nào đang mở', 423);
}

try {
    switch ($action) {
        /* =============== LIST =============== */
        case 'getMyList':
            $page = Helper::postInt('page', 1);
            $size = Helper::postInt('pageSize', AppConfig::DEFAULT_PAGE_SIZE);
            $tab  = Helper::postStr('tab') ?: 'all'; // all | nhap | cho | duyet | tuchoi
            $tabMap = [
                'nhap'   => ['Nhap'],
                'cho'    => ['ChoDuyet'],
                'duyet'  => ['DaDuyet'],
                'tuchoi' => ['TuChoi'],
                'all'    => ['Nhap', 'ChoDuyet', 'DaDuyet', 'TuChoi'],
            ];
            $filters = [
                'search'             => Helper::postStr('search'),
                'nguoi_tao_id'       => $u,
                'trang_thai_duyet_in' => $tabMap[$tab] ?? $tabMap['all'],
            ];
            $res = NCKH_DeTai_BUS::getPaged($page, $size, $filters, 0);
            ResponseHelper::paged($res['data'], $page, $size, $res['totalRecords']);
            break;

        case 'getMyCounts':
            $pdo = Database::getConnection();
            $stm = $pdo->prepare(
                "SELECT trang_thai_duyet, COUNT(*) AS sl
                 FROM NCKH_DE_TAI WHERE da_xoa=0 AND nguoi_tao=:u
                 GROUP BY trang_thai_duyet"
            );
            $stm->execute([':u' => $u]);
            $counts = ['all' => 0, 'Nhap' => 0, 'ChoDuyet' => 0, 'DaDuyet' => 0, 'TuChoi' => 0];
            foreach ($stm->fetchAll() as $r) { $counts[$r['trang_thai_duyet']] = (int)$r['sl']; $counts['all'] += (int)$r['sl']; }
            ResponseHelper::success('OK', $counts);
            break;

        case 'getDetail':
            $id = Helper::postInt('id');
            $dt = NCKH_DeTai_BUS::getById($id);
            if (!$dt || (int)$dt->nguoi_tao !== $u) ResponseHelper::error('Không tìm thấy', 404);
            $res = NCKH_DeTai_BUS::getDetail($id);
            ResponseHelper::success('OK', $res['data']);
            break;

        /* =============== DRAFT CRUD =============== */
        case 'createDraft':
            $nam = Helper::postInt('nam', (int)date('Y'));
            $maGoc = trim(Helper::postStr('ma_de_tai'));
            $tenDeTai = trim(Helper::postStr('ten_de_tai'));
            $dotId = Helper::postInt('dot_dang_ky_id');
            if ($tenDeTai === '') ResponseHelper::error('Vui lòng nhập tên đề tài');
            if ($dotId <= 0) ResponseHelper::error('Vui lòng chọn đợt đăng ký');

            // Phase Submit phải đang mở
            $check = NCKH_DotDangKy_BUS::checkPhaseOpen($dotId, NCKH_DotDangKy_BUS::HV_SUBMIT);
            if (!$check['ok']) ResponseHelper::error($check['message'], 423);

            if ($maGoc === '') {
                $stm = Database::getConnection()->prepare("SELECT COUNT(*) FROM NCKH_DE_TAI WHERE nam=:n AND da_xoa=0");
                $stm->execute([':n' => $nam]);
                $next = (int)$stm->fetchColumn() + 1;
                $maGoc = sprintf('DT-%d-%03d', $nam, $next);
            }

            $e = new NCKH_DeTai_PUBLIC();
            $e->ma_de_tai = $maGoc;
            $e->ten_de_tai = $tenDeTai;
            $e->nam = $nam;
            $e->cap_do_id = Helper::postInt('cap_do_id');
            $e->the_loai_id = Helper::postInt('the_loai_id');
            $e->khoa_phong_id = Helper::postInt('khoa_phong_id') ?: null;
            $e->ten_khoa_text = Helper::postStr('ten_khoa_text') ?: null;
            $e->dot_dang_ky_id = $dotId;
            $e->chu_nhiem_id = Helper::postInt('chu_nhiem_id');
            $e->trang_thai = 0;
            $e->trang_thai_duyet = 'Nhap';
            $e->nguoi_tao = $u;
            $res = NCKH_DeTai_BUS::insert($e);
            $res['success'] ? ResponseHelper::success($res['message'], $res['data']) : ResponseHelper::error($res['message']);
            break;

        case 'updateDraft':
            $id = Helper::postInt('id');
            ensureMine($id, $u);
            ensurePhaseEditable($id);
            $cur = NCKH_DeTai_BUS::getById($id);
            $e = new NCKH_DeTai_PUBLIC();
            // Copy current values then override allowed fields
            foreach (get_object_vars($cur) as $k => $v) {
                if (property_exists($e, $k)) $e->$k = $v;
            }
            $e->ma_de_tai     = Helper::postStr('ma_de_tai') ?: $cur->ma_de_tai;
            $e->ten_de_tai    = Helper::postStr('ten_de_tai');
            $e->nam           = Helper::postInt('nam', $cur->nam);
            $e->cap_do_id     = Helper::postInt('cap_do_id');
            $e->the_loai_id   = Helper::postInt('the_loai_id');
            $e->khoa_phong_id = Helper::postInt('khoa_phong_id') ?: null;
            $e->ten_khoa_text = Helper::postStr('ten_khoa_text') ?: null;
            $e->dot_dang_ky_id = Helper::postInt('dot_dang_ky_id') ?: $cur->dot_dang_ky_id;
            $e->chu_nhiem_id  = Helper::postInt('chu_nhiem_id');
            $e->thu_ky_id     = Helper::postInt('thu_ky_id') ?: null;
            $e->muc_tieu      = Helper::postStr('muc_tieu') ?: null;
            $e->tom_tat       = Helper::postStr('tom_tat') ?: null;
            $e->tu_khoa       = Helper::postStr('tu_khoa') ?: null;
            $e->ngay_bat_dau         = Helper::postStr('ngay_bat_dau') ?: null;
            $e->ngay_ket_thuc_du_kien = Helper::postStr('ngay_ket_thuc_du_kien') ?: null;
            $e->kinh_phi_du_toan = Helper::post('kinh_phi_du_toan') !== '' ? (float)Helper::post('kinh_phi_du_toan') : null;
            $e->nguon_kinh_phi   = Helper::postStr('nguon_kinh_phi') ?: null;
            $e->ten_tap_chi  = Helper::postStr('ten_tap_chi') ?: null;
            $e->so_tap_chi   = Helper::postStr('so_tap_chi') ?: null;
            $e->nam_xuat_ban = Helper::postInt('nam_xuat_ban') ?: null;
            $e->issn_doi     = Helper::postStr('issn_doi') ?: null;
            $e->link_bai_bao = Helper::postStr('link_bai_bao') ?: null;
            $e->nguoi_cap_nhat = $u;
            $res = NCKH_DeTai_BUS::update($e);
            $res['success'] ? ResponseHelper::success($res['message']) : ResponseHelper::error($res['message']);
            break;

        case 'deleteDraft':
            $id = Helper::postInt('id');
            ensureMine($id, $u);
            $res = NCKH_DeTai_BUS::trash($id, $u);
            ResponseHelper::success($res['message']);
            break;

        case 'submit':
            $id = Helper::postInt('id');
            $res = NCKH_DeTai_BUS::submitForReview($id, $u);
            $res['success'] ? ResponseHelper::success($res['message']) : ResponseHelper::error($res['message']);
            break;

        /* =============== THÀNH VIÊN =============== */
        case 'tv_save':
            $id = Helper::postInt('id');
            $deTaiId = Helper::postInt('de_tai_id');
            ensureMine($deTaiId, $u);
            ensurePhaseEditable($deTaiId);
            $tv = new NCKH_ThanhVien_PUBLIC();
            if ($id) $tv->id = $id;
            $tv->de_tai_id = $deTaiId;
            $tv->nhan_vien_id = Helper::postInt('nhan_vien_id') ?: null;
            $tv->ho_ten_ngoai = Helper::postStr('ho_ten_ngoai') ?: null;
            $tv->don_vi_ngoai = Helper::postStr('don_vi_ngoai') ?: null;
            $tv->vai_tro     = Helper::postStr('vai_tro') ?: 'Thành viên';
            $tv->ma_nv_text  = Helper::postStr('ma_nv_text') ?: null;
            $tv->phan_tram_dong_gop = Helper::post('phan_tram_dong_gop') !== '' ? (float)Helper::post('phan_tram_dong_gop') : null;
            $tv->ghi_chu = Helper::postStr('ghi_chu') ?: null;
            if ($id) { $tv->nguoi_cap_nhat = $u; $res = NCKH_ThanhVien_BUS::update($tv); }
            else     { $tv->nguoi_tao = $u;     $res = NCKH_ThanhVien_BUS::insert($tv); }
            $res['success'] ? ResponseHelper::success($res['message'], $res['data'] ?? null) : ResponseHelper::error($res['message']);
            break;

        case 'tv_delete':
            $id = Helper::postInt('id');
            $tv = NCKH_ThanhVien_BUS::getById($id);
            if (!$tv) ResponseHelper::error('Không tìm thấy', 404);
            ensureMine($tv->de_tai_id, $u);
            ensurePhaseEditable($tv->de_tai_id);
            $res = NCKH_ThanhVien_BUS::trash($id, $u);
            ResponseHelper::success($res['message']);
            break;

        case 'tv_getById':
            $id = Helper::postInt('id');
            $tv = NCKH_ThanhVien_BUS::getById($id);
            if (!$tv) ResponseHelper::error('Không tìm thấy', 404);
            ensureMine($tv->de_tai_id, $u);
            ResponseHelper::success('OK', $tv);
            break;

        /* =============== HỘI ĐỒNG =============== */
        case 'hd_save':
            $id = Helper::postInt('id');
            $deTaiId = Helper::postInt('de_tai_id');
            ensureMine($deTaiId, $u);
            ensurePhaseEditable($deTaiId);
            $hd = new NCKH_HoiDong_PUBLIC();
            if ($id) $hd->id = $id;
            $hd->de_tai_id = $deTaiId;
            $hd->ho_ten = Helper::postStr('ho_ten');
            $hd->chuc_danh_hoc_vi = Helper::postStr('chuc_danh_hoc_vi') ?: null;
            $hd->nhan_vien_id = Helper::postInt('nhan_vien_id') ?: null;
            $hd->ten_khoa_text = Helper::postStr('ten_khoa_text') ?: null;
            $hd->khoa_phong_id = Helper::postInt('khoa_phong_id') ?: null;
            $hd->vai_tro_hd = Helper::postStr('vai_tro_hd') ?: 'ThanhVien';
            $hd->thu_tu = Helper::postInt('thu_tu', 0);
            $hd->ghi_chu = Helper::postStr('ghi_chu') ?: null;
            if ($id) { $hd->nguoi_cap_nhat = $u; $res = NCKH_HoiDong_BUS::update($hd); }
            else     { $hd->nguoi_tao = $u;     $res = NCKH_HoiDong_BUS::insert($hd); }
            $res['success'] ? ResponseHelper::success($res['message'], $res['data'] ?? null) : ResponseHelper::error($res['message']);
            break;

        case 'hd_delete':
            $id = Helper::postInt('id');
            $hd = NCKH_HoiDong_BUS::getById($id);
            if (!$hd) ResponseHelper::error('Không tìm thấy', 404);
            ensureMine($hd->de_tai_id, $u);
            ensurePhaseEditable($hd->de_tai_id);
            $res = NCKH_HoiDong_BUS::trash($id, $u);
            ResponseHelper::success($res['message']);
            break;

        case 'hd_getById':
            $id = Helper::postInt('id');
            $hd = NCKH_HoiDong_BUS::getById($id);
            if (!$hd) ResponseHelper::error('Không tìm thấy', 404);
            ensureMine($hd->de_tai_id, $u);
            ResponseHelper::success('OK', $hd);
            break;

        /* =============== TÀI LIỆU UPLOAD =============== */
        case 'tl_upload':
            $deTaiId = Helper::postInt('de_tai_id');
            ensureMine($deTaiId, $u);
            ensurePhaseEditable($deTaiId);
            $loai = Helper::postStr('loai_tai_lieu') ?: 'Khac';
            $tenTl = Helper::postStr('ten_tai_lieu');
            $moTa = Helper::postStr('mo_ta') ?: null;
            if (empty($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
                ResponseHelper::error('Vui lòng chọn file');
            }
            $file = $_FILES['file'];
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, NCKH_TaiLieu_BUS::ALLOWED_EXT)) {
                ResponseHelper::error('Định dạng không được phép. Cho phép: ' . implode(', ', NCKH_TaiLieu_BUS::ALLOWED_EXT));
            }
            if ($file['size'] > NCKH_TaiLieu_BUS::MAX_SIZE) ResponseHelper::error('File vượt quá 20MB');
            if (!is_dir($UPLOAD_DIR)) @mkdir($UPLOAD_DIR, 0755, true);
            $newName = 'nckh_' . $deTaiId . '_' . date('YmdHis') . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
            $dest = $UPLOAD_DIR . '/' . $newName;
            if (!@move_uploaded_file($file['tmp_name'], $dest)) ResponseHelper::error('Không lưu được file');
            $tl = new NCKH_TaiLieu_PUBLIC();
            $tl->de_tai_id = $deTaiId;
            $tl->loai_tai_lieu = $loai;
            $tl->ten_tai_lieu = $tenTl ?: $file['name'];
            $tl->ten_file_goc = $file['name'];
            $tl->ten_file_luu = $newName;
            $tl->kich_thuoc = $file['size'];
            $tl->mime_type = $file['type'] ?? null;
            $tl->mo_ta = $moTa;
            $tl->nguoi_tao = $u;
            $res = NCKH_TaiLieu_BUS::insert($tl);
            if (!$res['success']) { @unlink($dest); ResponseHelper::error($res['message']); }
            ResponseHelper::success($res['message'], $res['data']);
            break;

        case 'tl_delete':
            $id = Helper::postInt('id');
            $tl = NCKH_TaiLieu_BUS::getById($id);
            if (!$tl) ResponseHelper::error('Không tìm thấy', 404);
            ensureMine($tl->de_tai_id, $u);
            ensurePhaseEditable($tl->de_tai_id);
            $res = NCKH_TaiLieu_BUS::trash($id, $u, $UPLOAD_DIR);
            ResponseHelper::success($res['message']);
            break;

        /* =============== COMBO =============== */
        case 'getComboCapDo':    ResponseHelper::success('OK', DM_NCKH_CapDo_BUS::getCombo()); break;
        case 'getComboTheLoai':  ResponseHelper::success('OK', DM_NCKH_TheLoai_BUS::getCombo()); break;
        case 'getComboKhoaPhong': ResponseHelper::success('OK', DM_KhoaPhong_BUS::getCombo()); break;
        case 'getComboDot':      ResponseHelper::success('OK', NCKH_DotDangKy_BUS::getCombo(true)); break;
        case 'getComboNhanVien':
            $kw = Helper::postStr('kw');
            $r = DM_NhanVien_BUS::getPaged(1, 50, $kw, 0, 0);
            ResponseHelper::success('OK', $r['data']);
            break;

        default:
            ResponseHelper::error('Action không hợp lệ');
    }
} catch (Throwable $ex) {
    ResponseHelper::error(AppConfig::APP_DEBUG ? $ex->getMessage() : 'Lỗi hệ thống', 500);
}
