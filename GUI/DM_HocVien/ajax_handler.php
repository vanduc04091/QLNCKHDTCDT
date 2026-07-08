<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../BUS/DM_HocVien_BUS.php';
require_once __DIR__ . '/../../BUS/DM_DoiTuongHocVien_BUS.php';
require_once __DIR__ . '/../../BUS/DM_NhanVien_BUS.php';
require_once __DIR__ . '/../../BUS/DT_HocVienLop_BUS.php';
require_once __DIR__ . '/../../BUS/DT_KhoaHocChuongTrinh_BUS.php';
require_once __DIR__ . '/../../BUS/DT_KhoaHoc_BUS.php';
require_once __DIR__ . '/../../BUS/DT_HoSoHocVien_BUS.php';
require_once __DIR__ . '/../../BUS/DT_ChungChi_BUS.php';
require_once __DIR__ . '/../../BUS/DT_PhieuIn_BUS.php';

Helper::requireAjaxCsrf();

$action = Helper::post('action', '');
$u = SessionHelper::userId();
$MODULE = DM_HocVien_BUS::MODULE_KEY;

try {
    switch ($action) {
        case 'getPaged':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XEM);
            $page = Helper::postInt('page', 1);
            $size = Helper::postInt('pageSize', AppConfig::DEFAULT_PAGE_SIZE);
            $search = Helper::postStr('search');
            $daXoa = Helper::postInt('da_xoa', 0);
            $dtId = Helper::postInt('doi_tuong_id', 0);
            $lnv = isset($_POST['la_nhan_vien']) && $_POST['la_nhan_vien'] !== '' ? Helper::postInt('la_nhan_vien', -1) : -1;
            $tuNgay = Helper::postStr('tu_ngay');
            $denNgay = Helper::postStr('den_ngay');
            $res = DM_HocVien_BUS::getPaged($page, $size, $search, $daXoa, $dtId, $lnv, $tuNgay, $denNgay);
            ResponseHelper::paged($res['data'], $page, $size, $res['totalRecords']);
            break;

        case 'getById':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XEM);
            $id = Helper::postInt('id');
            $e = DM_HocVien_BUS::getById($id);
            if (!$e) ResponseHelper::error('Không tìm thấy');
            ResponseHelper::success('OK', $e);
            break;

        case 'getStats':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XEM);
            ResponseHelper::success('OK', DM_HocVien_BUS::getStats());
            break;

        case 'getComboDoiTuong':
            ResponseHelper::success('OK', DM_DoiTuongHocVien_BUS::getCombo());
            break;

        case 'getComboNhanVien':
            ResponseHelper::success('OK', DM_NhanVien_BUS::getCombo());
            break;

        case 'insert':
        case 'update':
            $isUpdate = $action === 'update';
            PhanQuyenHelper::requireQuyen($MODULE, $isUpdate ? PhanQuyenHelper::QUYEN_SUA : PhanQuyenHelper::QUYEN_THEM);

            $e = new DM_HocVien_PUBLIC();
            if ($isUpdate) $e->id = Helper::postInt('id');
            $e->ma_hv = Helper::postStr('ma_hv');
            $e->ho_ten = Helper::postStr('ho_ten');
            $e->ngay_sinh = Helper::postStr('ngay_sinh') ?: null;
            $e->gioi_tinh = Helper::postStr('gioi_tinh') ?: null;
            $e->trinh_do_chuyen_mon = Helper::postStr('trinh_do_chuyen_mon') ?: null;
            $e->dien_thoai = Helper::postStr('dien_thoai') ?: null;
            $e->email = Helper::postStr('email') ?: null;
            $e->cccd = Helper::postStr('cccd') ?: null;
            $e->cccd_ngay_cap = Helper::postStr('cccd_ngay_cap') ?: null;
            $e->cccd_noi_cap = Helper::postStr('cccd_noi_cap') ?: null;
            $e->dia_chi = Helper::postStr('dia_chi') ?: null;
            $e->truong_dao_tao = Helper::postStr('truong_dao_tao') ?: null;
            $e->nam_tot_nghiep = Helper::postInt('nam_tot_nghiep') ?: null;
            $e->don_vi_cong_tac = Helper::postStr('don_vi_cong_tac') ?: null;
            $e->chuc_vu = Helper::postStr('chuc_vu') ?: null;
            $e->doi_tuong_id = Helper::postInt('doi_tuong_id') ?: null;
            $e->la_nhan_vien = Helper::postInt('la_nhan_vien', 0) ? 1 : 0;
            $e->nhan_vien_id = null; // chỉ dùng cờ la_nhan_vien để phân biệt
            $e->ghi_chu = Helper::postStr('ghi_chu') ?: null;
            $e->trang_thai = Helper::postInt('trang_thai', 1);

            // Upload avatar nếu có file
            $avatarFilename = null;
            $removeAvatar = Helper::postInt('remove_avatar', 0) === 1;
            if (!empty($_FILES['avatar_file']['name'])) {
                $up = DM_HocVien_BUS::uploadAvatar($_FILES['avatar_file'], $u);
                if (!$up['success']) ResponseHelper::error($up['message']);
                $avatarFilename = $up['filename'];
            }
            if ($isUpdate) {
                $e->nguoi_cap_nhat = $u;
                // Logic avatar: upload mới → set; remove → ""; không đổi → null (DAL bỏ qua)
                if ($avatarFilename) {
                    // Xóa avatar cũ
                    $old = DM_HocVien_BUS::getById($e->id);
                    if ($old && $old->avatar) DM_HocVien_BUS::removeAvatarFile($old->avatar);
                    $e->avatar = $avatarFilename;
                } elseif ($removeAvatar) {
                    $old = DM_HocVien_BUS::getById($e->id);
                    if ($old && $old->avatar) DM_HocVien_BUS::removeAvatarFile($old->avatar);
                    $e->avatar = '';
                } else {
                    $e->avatar = null; // DAL giữ nguyên
                }
                $res = DM_HocVien_BUS::update($e);
            } else {
                $e->nguoi_tao = $u;
                $e->avatar = $avatarFilename;
                $res = DM_HocVien_BUS::insert($e);
            }
            $res['success'] ? ResponseHelper::success($res['message'], $res['data'] ?? null) : ResponseHelper::error($res['message']);
            break;

        case 'import':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_THEM);
            if (empty($_FILES['file']['name']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
                ResponseHelper::error('Chưa chọn file hoặc upload lỗi');
            }
            $ext = strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));
            if ($ext !== 'xlsx') ResponseHelper::error('Chỉ hỗ trợ file .xlsx');
            if ($_FILES['file']['size'] > 5 * 1024 * 1024) ResponseHelper::error('File quá lớn (tối đa 5MB)');
            $res = DM_HocVien_BUS::importExcel($_FILES['file']['tmp_name'], $u);
            $res['success'] ? ResponseHelper::success($res['message'], $res['data'] ?? null) : ResponseHelper::error($res['message']);
            break;

        case 'trash':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XOA);
            $res = DM_HocVien_BUS::trash(Helper::postInt('id'), $u);
            $res['success'] ? ResponseHelper::success($res['message']) : ResponseHelper::error($res['message']);
            break;

        case 'restore':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_SUA);
            $res = DM_HocVien_BUS::restore(Helper::postInt('id'), $u);
            $res['success'] ? ResponseHelper::success($res['message']) : ResponseHelper::error($res['message']);
            break;

        case 'delete':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XOA);
            $res = DM_HocVien_BUS::delete(Helper::postInt('id'));
            $res['success'] ? ResponseHelper::success($res['message']) : ResponseHelper::error($res['message']);
            break;

        // ====== Lớp học của học viên (chiều ngược) ======
        case 'listLopCuaHocVien':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XEM);
            ResponseHelper::success('OK', DT_HocVienLop_BUS::getByHocVien(Helper::postInt('hoc_vien_id')));
            break;

        case 'getLopCombo':
            // Lấy danh sách (khóa học + chương trình đào tạo) để ghi danh
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XEM);
            ResponseHelper::success('OK', DT_KhoaHocChuongTrinh_BUS::getCombo());
            break;

        case 'getKhoaHocCombo':
            // Combo khóa học (bước 1 của ghi danh 2 cấp)
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XEM);
            ResponseHelper::success('OK', DT_KhoaHoc_BUS::getCombo());
            break;

        case 'getChuongTrinhTheoKhoa':
            // Các CTĐT (cặp khct) thuộc 1 khóa học (bước 2)
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XEM);
            ResponseHelper::success('OK', DT_KhoaHocChuongTrinh_BUS::getByKhoaHoc(Helper::postInt('khoa_hoc_id')));
            break;

        case 'ghiDanhLop':
            PhanQuyenHelper::requireQuyen('DT_HocVienLop', PhanQuyenHelper::QUYEN_THEM);
            $hvl = new DT_HocVienLop_PUBLIC();
            $hvl->khoa_hoc_chuong_trinh_id = Helper::postInt('lop_hoc_id');
            $hvl->hoc_vien_id = Helper::postInt('hoc_vien_id');
            $hvl->ngay_ghi_danh = Helper::postStr('ngay_ghi_danh') ?: date('Y-m-d');
            $hvl->ngay_bat_dau = Helper::postStr('ngay_bat_dau') ?: null;
            $hvl->ngay_ket_thuc = Helper::postStr('ngay_ket_thuc') ?: null;
            $hvl->trang_thai = 1;
            $hvl->nguoi_tao = $u;
            $res = DT_HocVienLop_BUS::insert($hvl);
            $res['success'] ? ResponseHelper::success($res['message'], $res['data'] ?? null) : ResponseHelper::error($res['message']);
            break;

        case 'huyGhiDanh':
            PhanQuyenHelper::requireQuyen('DT_HocVienLop', PhanQuyenHelper::QUYEN_XOA);
            $res = DT_HocVienLop_BUS::delete(Helper::postInt('id'));
            $res['success'] ? ResponseHelper::success($res['message']) : ResponseHelper::error($res['message']);
            break;

        case 'suaNgayGhiDanh':
            PhanQuyenHelper::requireQuyen('DT_HocVienLop', PhanQuyenHelper::QUYEN_SUA);
            $res = DT_HocVienLop_BUS::updateNgay(
                Helper::postInt('id'),
                Helper::postStr('ngay_ghi_danh') ?: null,
                Helper::postStr('ngay_bat_dau') ?: null,
                Helper::postStr('ngay_ket_thuc') ?: null,
                $u
            );
            $res['success'] ? ResponseHelper::success($res['message']) : ResponseHelper::error($res['message']);
            break;

        case 'getPhieuInfo':
            // Danh sách mẫu phiếu + các ghi danh của HV (để chọn khi in)
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XEM);
            ResponseHelper::success('OK', [
                'templates' => DT_PhieuIn_BUS::getTemplates(),
                'ghi_danh'  => DT_HocVienLop_BUS::getByHocVien(Helper::postInt('hoc_vien_id')),
            ]);
            break;

        case 'getOverview':
            // Tổng hợp dữ liệu xem nhanh: lịch / điểm danh / điểm / khóa / môn / hồ sơ / chứng chỉ
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XEM);
            $hvId = Helper::postInt('hoc_vien_id');
            $hv   = DM_HocVien_BUS::getById($hvId);
            if (!$hv) ResponseHelper::error('Không tìm thấy học viên');
            $overview = DT_HocVienLop_BUS::getOverview($hvId);
            $overview['ho_so']      = DT_HoSoHocVien_BUS::getByHocVien($hvId);
            $overview['chung_chi']  = DT_ChungChi_BUS::getByHocVien($hvId);
            $overview['hoc_vien']   = $hv;
            ResponseHelper::success('OK', $overview);
            break;

        default:
            ResponseHelper::error('Action không hợp lệ');
    }
} catch (Throwable $ex) {
    ResponseHelper::error(AppConfig::APP_DEBUG ? $ex->getMessage() : 'Lỗi hệ thống', 500);
}
