<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../BUS/NCKH_DeTai_BUS.php';
require_once __DIR__ . '/../../BUS/NCKH_ThanhVien_BUS.php';
require_once __DIR__ . '/../../BUS/NCKH_HoiDong_BUS.php';
require_once __DIR__ . '/../../BUS/NCKH_TienDo_BUS.php';
require_once __DIR__ . '/../../BUS/NCKH_TaiLieu_BUS.php';
require_once __DIR__ . '/../../BUS/DM_NCKH_CapDo_BUS.php';
require_once __DIR__ . '/../../BUS/DM_NCKH_TheLoai_BUS.php';
require_once __DIR__ . '/../../BUS/DM_KhoaPhong_BUS.php';
require_once __DIR__ . '/../../BUS/DM_NhanVien_BUS.php';

Helper::requireAjaxCsrf();

$action = Helper::post('action', '');
$u = SessionHelper::userId();
$MODULE = NCKH_DeTai_BUS::MODULE_KEY;

$UPLOAD_DIR = __DIR__ . '/../../assets/uploads/nckh';

try {
    switch ($action) {
        /* ===== LIST + DETAIL ===== */
        case 'getPaged':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XEM);
            $page = Helper::postInt('page', 1);
            $size = Helper::postInt('pageSize', AppConfig::DEFAULT_PAGE_SIZE);
            $filters = [
                'search'        => Helper::postStr('search'),
                'nam'           => Helper::postInt('nam'),
                'cap_do_id'     => Helper::postInt('cap_do_id'),
                'the_loai_id'   => Helper::postInt('the_loai_id'),
                'khoa_phong_id' => Helper::postInt('khoa_phong_id'),
                'chu_nhiem_id'  => Helper::postInt('chu_nhiem_id'),
                'trang_thai'    => Helper::post('trang_thai', ''),
                // Module admin chính chỉ hiển thị đề tài đã duyệt (đề tài cũ đã backfill DaDuyet)
                'trang_thai_duyet' => 'DaDuyet',
            ];
            $daXoa = Helper::postInt('da_xoa', 0);
            $res = NCKH_DeTai_BUS::getPaged($page, $size, $filters, $daXoa);
            ResponseHelper::paged($res['data'], $page, $size, $res['totalRecords']);
            break;

        case 'getById':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XEM);
            $e = NCKH_DeTai_BUS::getById(Helper::postInt('id'));
            if (!$e) ResponseHelper::error('Không tìm thấy');
            ResponseHelper::success('OK', $e);
            break;

        case 'getDetail':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XEM);
            $res = NCKH_DeTai_BUS::getDetail(Helper::postInt('id'));
            $res['success'] ? ResponseHelper::success('OK', $res['data']) : ResponseHelper::error($res['message']);
            break;

        /* ===== CRUD ĐỀ TÀI ===== */
        case 'insert':
        case 'update':
            $isUpdate = $action === 'update';
            PhanQuyenHelper::requireQuyen($MODULE, $isUpdate ? PhanQuyenHelper::QUYEN_SUA : PhanQuyenHelper::QUYEN_THEM);
            $e = new NCKH_DeTai_PUBLIC();
            if ($isUpdate) $e->id = Helper::postInt('id');
            $e->ma_de_tai   = Helper::postStr('ma_de_tai');
            $e->ten_de_tai  = Helper::postStr('ten_de_tai');
            $e->nam         = Helper::postInt('nam', (int)date('Y'));
            $e->cap_do_id   = Helper::postInt('cap_do_id');
            $e->the_loai_id = Helper::postInt('the_loai_id');
            $e->khoa_phong_id = Helper::postInt('khoa_phong_id') ?: null;
            $e->chu_nhiem_id = Helper::postInt('chu_nhiem_id');
            $e->thu_ky_id    = Helper::postInt('thu_ky_id') ?: null;
            $e->muc_tieu     = Helper::postStr('muc_tieu') ?: null;
            $e->tom_tat      = Helper::postStr('tom_tat') ?: null;
            $e->tu_khoa      = Helper::postStr('tu_khoa') ?: null;
            $e->ngay_bat_dau         = Helper::postStr('ngay_bat_dau') ?: null;
            $e->ngay_ket_thuc_du_kien = Helper::postStr('ngay_ket_thuc_du_kien') ?: null;
            $e->ngay_nghiem_thu      = Helper::postStr('ngay_nghiem_thu') ?: null;
            $e->kinh_phi_du_toan = Helper::post('kinh_phi_du_toan') !== '' ? (float)Helper::post('kinh_phi_du_toan') : null;
            $e->kinh_phi_thuc_te = Helper::post('kinh_phi_thuc_te') !== '' ? (float)Helper::post('kinh_phi_thuc_te') : null;
            $e->nguon_kinh_phi   = Helper::postStr('nguon_kinh_phi') ?: null;
            $e->quyet_dinh_phe_duyet = Helper::postStr('quyet_dinh_phe_duyet') ?: null;
            $e->ngay_quyet_dinh   = Helper::postStr('ngay_quyet_dinh') ?: null;
            $e->ket_qua_xep_loai  = Helper::postStr('ket_qua_xep_loai') ?: null;
            $e->diem_so           = Helper::post('diem_so') !== '' ? (float)Helper::post('diem_so') : null;
            $e->noi_dung_ung_dung = Helper::postStr('noi_dung_ung_dung') ?: null;
            $e->ten_tap_chi  = Helper::postStr('ten_tap_chi')  ?: null;
            $e->so_tap_chi   = Helper::postStr('so_tap_chi')   ?: null;
            $e->nam_xuat_ban = Helper::postInt('nam_xuat_ban') ?: null;
            $e->issn_doi     = Helper::postStr('issn_doi')     ?: null;
            $e->link_bai_bao = Helper::postStr('link_bai_bao') ?: null;
            $e->phien_bao_ve     = Helper::postStr('phien_bao_ve') ?: null;
            $e->dia_diem_bao_ve  = Helper::postStr('dia_diem_bao_ve') ?: null;
            $e->ngay_bao_ve      = Helper::postStr('ngay_bao_ve') ?: null;
            $e->quyet_dinh_cong_nhan      = Helper::postStr('quyet_dinh_cong_nhan') ?: null;
            $e->ngay_quyet_dinh_cong_nhan = Helper::postStr('ngay_quyet_dinh_cong_nhan') ?: null;
            $e->ten_khoa_text    = Helper::postStr('ten_khoa_text') ?: null;
            $e->trang_thai   = Helper::postInt('trang_thai', 0);

            if ($isUpdate) { $e->nguoi_cap_nhat = $u; $res = NCKH_DeTai_BUS::update($e); }
            else           { $e->nguoi_tao = $u;     $res = NCKH_DeTai_BUS::insert($e); }
            $res['success'] ? ResponseHelper::success($res['message'], $res['data'] ?? null) : ResponseHelper::error($res['message']);
            break;

        case 'trash':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XOA);
            $res = NCKH_DeTai_BUS::trash(Helper::postInt('id'), $u);
            $res['success'] ? ResponseHelper::success($res['message']) : ResponseHelper::error($res['message']);
            break;

        case 'restore':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_SUA);
            $res = NCKH_DeTai_BUS::restore(Helper::postInt('id'), $u);
            $res['success'] ? ResponseHelper::success($res['message']) : ResponseHelper::error($res['message']);
            break;

        case 'delete':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XOA);
            $res = NCKH_DeTai_BUS::delete(Helper::postInt('id'));
            $res['success'] ? ResponseHelper::success($res['message']) : ResponseHelper::error($res['message']);
            break;

        /* ===== SUB: THÀNH VIÊN ===== */
        case 'tv_insert':
        case 'tv_update':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_SUA);
            $isU = $action === 'tv_update';
            $tv = new NCKH_ThanhVien_PUBLIC();
            if ($isU) $tv->id = Helper::postInt('id');
            $tv->de_tai_id   = Helper::postInt('de_tai_id');
            $tv->nhan_vien_id = Helper::postInt('nhan_vien_id') ?: null;
            $tv->ho_ten_ngoai = Helper::postStr('ho_ten_ngoai') ?: null;
            $tv->don_vi_ngoai = Helper::postStr('don_vi_ngoai') ?: null;
            $tv->vai_tro     = Helper::postStr('vai_tro') ?: 'Thành viên';
            $tv->ma_nv_text  = Helper::postStr('ma_nv_text') ?: null;
            $tv->phan_tram_dong_gop = Helper::post('phan_tram_dong_gop') !== '' ? (float)Helper::post('phan_tram_dong_gop') : null;
            $tv->ghi_chu = Helper::postStr('ghi_chu') ?: null;
            if ($isU) { $tv->nguoi_cap_nhat = $u; $res = NCKH_ThanhVien_BUS::update($tv); }
            else      { $tv->nguoi_tao = $u;     $res = NCKH_ThanhVien_BUS::insert($tv); }
            $res['success'] ? ResponseHelper::success($res['message'], $res['data'] ?? null) : ResponseHelper::error($res['message']);
            break;

        case 'tv_delete':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_SUA);
            $res = NCKH_ThanhVien_BUS::trash(Helper::postInt('id'), $u);
            ResponseHelper::success($res['message']);
            break;

        case 'tv_getById':
            $tv = NCKH_ThanhVien_BUS::getById(Helper::postInt('id'));
            $tv ? ResponseHelper::success('OK', $tv) : ResponseHelper::error('Không tìm thấy');
            break;

        /* ===== SUB: HỘI ĐỒNG ===== */
        case 'hd_insert':
        case 'hd_update':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_SUA);
            $isU = $action === 'hd_update';
            $hd = new NCKH_HoiDong_PUBLIC();
            if ($isU) $hd->id = Helper::postInt('id');
            $hd->de_tai_id = Helper::postInt('de_tai_id');
            $hd->ho_ten = Helper::postStr('ho_ten');
            $hd->chuc_danh_hoc_vi = Helper::postStr('chuc_danh_hoc_vi') ?: null;
            $hd->nhan_vien_id = Helper::postInt('nhan_vien_id') ?: null;
            $hd->ten_khoa_text = Helper::postStr('ten_khoa_text') ?: null;
            $hd->khoa_phong_id = Helper::postInt('khoa_phong_id') ?: null;
            $hd->vai_tro_hd = Helper::postStr('vai_tro_hd') ?: 'ThanhVien';
            $hd->thu_tu = Helper::postInt('thu_tu', 0);
            $hd->ghi_chu = Helper::postStr('ghi_chu') ?: null;
            if ($isU) { $hd->nguoi_cap_nhat = $u; $res = NCKH_HoiDong_BUS::update($hd); }
            else      { $hd->nguoi_tao = $u;     $res = NCKH_HoiDong_BUS::insert($hd); }
            $res['success'] ? ResponseHelper::success($res['message'], $res['data'] ?? null) : ResponseHelper::error($res['message']);
            break;

        case 'hd_delete':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_SUA);
            $res = NCKH_HoiDong_BUS::trash(Helper::postInt('id'), $u);
            ResponseHelper::success($res['message']);
            break;

        case 'hd_getById':
            $hd = NCKH_HoiDong_BUS::getById(Helper::postInt('id'));
            $hd ? ResponseHelper::success('OK', $hd) : ResponseHelper::error('Không tìm thấy');
            break;

        /* ===== SUB: TIẾN ĐỘ ===== */
        case 'td_insert':
        case 'td_update':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_SUA);
            $isU = $action === 'td_update';
            $td = new NCKH_TienDo_PUBLIC();
            if ($isU) $td->id = Helper::postInt('id');
            $td->de_tai_id    = Helper::postInt('de_tai_id');
            $td->ky_bao_cao   = Helper::postStr('ky_bao_cao');
            $td->ngay_bao_cao = Helper::postStr('ngay_bao_cao');
            $td->phan_tram_hoan_thanh = Helper::postInt('phan_tram_hoan_thanh', 0);
            $td->cong_viec_da_lam     = Helper::postStr('cong_viec_da_lam') ?: null;
            $td->cong_viec_tiep_theo  = Helper::postStr('cong_viec_tiep_theo') ?: null;
            $td->kho_khan_vuong_mac   = Helper::postStr('kho_khan_vuong_mac') ?: null;
            $td->nguoi_bao_cao_id     = Helper::postInt('nguoi_bao_cao_id') ?: null;
            if ($isU) { $td->nguoi_cap_nhat = $u; $res = NCKH_TienDo_BUS::update($td); }
            else      { $td->nguoi_tao = $u;     $res = NCKH_TienDo_BUS::insert($td); }
            $res['success'] ? ResponseHelper::success($res['message'], $res['data'] ?? null) : ResponseHelper::error($res['message']);
            break;

        case 'td_delete':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_SUA);
            $res = NCKH_TienDo_BUS::trash(Helper::postInt('id'), $u);
            ResponseHelper::success($res['message']);
            break;

        case 'td_getById':
            $td = NCKH_TienDo_BUS::getById(Helper::postInt('id'));
            $td ? ResponseHelper::success('OK', $td) : ResponseHelper::error('Không tìm thấy');
            break;

        /* ===== SUB: TÀI LIỆU (upload) ===== */
        case 'tl_upload':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_SUA);
            $deTaiId = Helper::postInt('de_tai_id');
            if ($deTaiId <= 0) ResponseHelper::error('Thiếu đề tài');
            $loai = Helper::postStr('loai_tai_lieu') ?: 'Khac';
            $tenTl = Helper::postStr('ten_tai_lieu');
            $moTa = Helper::postStr('mo_ta') ?: null;

            if (empty($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
                ResponseHelper::error('Vui lòng chọn file');
            }
            $file = $_FILES['file'];
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, NCKH_TaiLieu_BUS::ALLOWED_EXT)) {
                ResponseHelper::error('Định dạng không được phép. Chỉ cho phép: ' . implode(', ', NCKH_TaiLieu_BUS::ALLOWED_EXT));
            }
            if ($file['size'] > NCKH_TaiLieu_BUS::MAX_SIZE) {
                ResponseHelper::error('File vượt quá 20MB');
            }
            if (!is_dir($UPLOAD_DIR)) @mkdir($UPLOAD_DIR, 0755, true);
            $newName = 'nckh_' . $deTaiId . '_' . date('YmdHis') . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
            $dest = $UPLOAD_DIR . '/' . $newName;
            if (!@move_uploaded_file($file['tmp_name'], $dest)) {
                ResponseHelper::error('Không lưu được file');
            }

            $tl = new NCKH_TaiLieu_PUBLIC();
            $tl->de_tai_id = $deTaiId;
            $tl->loai_tai_lieu = $loai;
            $tl->ten_tai_lieu = $tenTl ?: $file['name'];
            $tl->ten_file_goc = $file['name'];
            $tl->ten_file_luu = $newName;
            $tl->kich_thuoc = $file['size'];
            $tl->mime_type  = $file['type'] ?? null;
            $tl->mo_ta = $moTa;
            $tl->nguoi_tao = $u;
            $res = NCKH_TaiLieu_BUS::insert($tl);
            if (!$res['success']) { @unlink($dest); ResponseHelper::error($res['message']); }
            ResponseHelper::success($res['message'], $res['data']);
            break;

        case 'tl_update':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_SUA);
            $tl = new NCKH_TaiLieu_PUBLIC();
            $tl->id = Helper::postInt('id');
            $tl->loai_tai_lieu = Helper::postStr('loai_tai_lieu') ?: 'Khac';
            $tl->ten_tai_lieu = Helper::postStr('ten_tai_lieu');
            $tl->mo_ta = Helper::postStr('mo_ta') ?: null;
            $tl->nguoi_cap_nhat = $u;
            $res = NCKH_TaiLieu_BUS::update($tl);
            $res['success'] ? ResponseHelper::success($res['message']) : ResponseHelper::error($res['message']);
            break;

        case 'tl_delete':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_SUA);
            $res = NCKH_TaiLieu_BUS::trash(Helper::postInt('id'), $u, $UPLOAD_DIR);
            ResponseHelper::success($res['message']);
            break;

        /* ===== COMBO ===== */
        case 'getComboCapDo':
            ResponseHelper::success('OK', DM_NCKH_CapDo_BUS::getCombo());
            break;
        case 'getComboTheLoai':
            ResponseHelper::success('OK', DM_NCKH_TheLoai_BUS::getCombo());
            break;
        case 'getComboKhoaPhong':
            ResponseHelper::success('OK', DM_KhoaPhong_BUS::getCombo());
            break;
        case 'getComboNhanVien':
            $kw = Helper::postStr('kw');
            $res = DM_NhanVien_BUS::getPaged(1, 50, $kw, 0, 0);
            ResponseHelper::success('OK', $res['data']);
            break;

        default:
            ResponseHelper::error('Action không hợp lệ');
    }
} catch (Throwable $ex) {
    ResponseHelper::error(AppConfig::APP_DEBUG ? $ex->getMessage() : 'Lỗi hệ thống', 500);
}
