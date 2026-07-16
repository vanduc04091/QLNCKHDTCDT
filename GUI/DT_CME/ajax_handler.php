<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../BUS/DT_Cme_BUS.php';
require_once __DIR__ . '/../../BUS/DM_NhanVien_BUS.php';
require_once __DIR__ . '/../../BUS/DM_KhoaPhong_BUS.php';

Helper::requireAjaxCsrf();
$action = Helper::post('action', '');
$u = SessionHelper::userId();
$MODULE = DT_Cme_BUS::MODULE_GHI_NHAN;

try {
    switch ($action) {
        case 'getPaged':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XEM);
            $page = Helper::postInt('page', 1);
            $size = Helper::postInt('pageSize', AppConfig::DEFAULT_PAGE_SIZE);
            $opts = [
                'search'        => Helper::postStr('search'),
                'nam'           => Helper::postInt('nam', 0),
                'khoa_phong_id' => Helper::postInt('khoa_phong_id', 0),
                'nhom_id'       => Helper::postInt('nhom_id', 0),
                'nhan_vien_id'  => Helper::postInt('nhan_vien_id', 0),
            ];
            $res = DT_Cme_BUS::ghiNhanGetPaged($page, $size, $opts, Helper::postInt('da_xoa', 0));
            ResponseHelper::paged($res['data'], $page, $size, $res['totalRecords']);
            break;

        case 'getById':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XEM);
            $e = DT_Cme_BUS::ghiNhanGetById(Helper::postInt('id'));
            $e ? ResponseHelper::success('OK', $e) : ResponseHelper::error('Không tìm thấy');
            break;

        case 'getStats':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XEM);
            ResponseHelper::success('OK', DT_Cme_BUS::getStats(Helper::postInt('nam', 0)));
            break;

        case 'insert':
        case 'update':
            $isU = $action === 'update';
            PhanQuyenHelper::requireQuyen($MODULE, $isU ? PhanQuyenHelper::QUYEN_SUA : PhanQuyenHelper::QUYEN_THEM);
            $e = new DT_CmeGhiNhan_PUBLIC();
            if ($isU) $e->id = Helper::postInt('id');
            $e->nhan_vien_id = Helper::postInt('nhan_vien_id');
            $e->loai_id = Helper::postInt('loai_id');
            $e->nam = Helper::postInt('nam', (int)date('Y'));
            $e->ten_hoat_dong = Helper::postStr('ten_hoat_dong') ?: null;
            $e->vai_tro = Helper::postStr('vai_tro') ?: null;
            $e->so_luong = (float)Helper::post('so_luong', 1);
            $e->ngay_bat_dau = Helper::postStr('ngay_bat_dau') ?: null;
            $e->ngay_ket_thuc = Helper::postStr('ngay_ket_thuc') ?: null;
            $e->ghi_chu = Helper::postStr('ghi_chu') ?: null;

            // ----- Minh chứng (PDF/ảnh chứng chỉ) -----
            $newFile = null;
            if (!empty($_FILES['minh_chung_file']['name'])) {
                $up = DT_Cme_BUS::uploadMinhChung($_FILES['minh_chung_file']);
                if (!$up['success']) ResponseHelper::error($up['message']);
                $newFile = $up['data'];
            }
            $goMinhChung = Helper::postInt('remove_minh_chung', 0) === 1;

            if ($isU) {
                $e->nguoi_cap_nhat = $u;
                $old = DT_Cme_BUS::ghiNhanGetById($e->id);
                if ($newFile) {
                    if ($old && $old->minh_chung) DT_Cme_BUS::xoaMinhChungFile($old->minh_chung);
                    $e->minh_chung = $newFile['file_name'];
                    $e->minh_chung_goc = $newFile['file_goc'];
                    $e->minh_chung_size = $newFile['file_size'];
                } elseif ($goMinhChung) {
                    if ($old && $old->minh_chung) DT_Cme_BUS::xoaMinhChungFile($old->minh_chung);
                    $e->minh_chung = '';   // '' = gỡ file
                } else {
                    $e->minh_chung = null; // null = giữ nguyên
                }
                $res = DT_Cme_BUS::ghiNhanUpdate($e);
            } else {
                $e->nguoi_tao = $u;
                if ($newFile) {
                    $e->minh_chung = $newFile['file_name'];
                    $e->minh_chung_goc = $newFile['file_goc'];
                    $e->minh_chung_size = $newFile['file_size'];
                }
                $res = DT_Cme_BUS::ghiNhanInsert($e);
            }
            // Nếu lưu DB thất bại mà vừa upload file mới -> dọn file để không rác
            if (!$res['success'] && $newFile) DT_Cme_BUS::xoaMinhChungFile($newFile['file_name']);
            $res['success'] ? ResponseHelper::success($res['message'], $res['data'] ?? null) : ResponseHelper::error($res['message']);
            break;

        // Đính kèm / gỡ minh chứng nhanh từ danh sách
        case 'capNhatMinhChung':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_SUA);
            $res = DT_Cme_BUS::capNhatMinhChung(
                Helper::postInt('id'),
                !empty($_FILES['minh_chung_file']['name']) ? $_FILES['minh_chung_file'] : null,
                Helper::postInt('go', 0) === 1,
                $u
            );
            $res['success'] ? ResponseHelper::success($res['message']) : ResponseHelper::error($res['message']);
            break;

        case 'trash':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XOA);
            $res = DT_Cme_BUS::ghiNhanTrash(Helper::postInt('id'), $u);
            $res['success'] ? ResponseHelper::success($res['message']) : ResponseHelper::error($res['message']);
            break;

        case 'restore':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_SUA);
            $res = DT_Cme_BUS::ghiNhanRestore(Helper::postInt('id'), $u);
            $res['success'] ? ResponseHelper::success($res['message']) : ResponseHelper::error($res['message']);
            break;

        // Tính thử giờ tín chỉ (realtime preview trên form)
        case 'tinhThu':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XEM);
            $loai = DT_Cme_BUS::loaiGetById(Helper::postInt('loai_id'));
            if (!$loai) ResponseHelper::error('Loại không tồn tại');
            $gio = DT_Cme_BUS::tinhGioTinChi($loai->kieu_quy_doi, (float)$loai->gia_tri_quy_doi, (float)Helper::post('so_luong', 1));
            ResponseHelper::success('OK', [
                'gio_tin_chi' => $gio,
                'kieu_quy_doi' => $loai->kieu_quy_doi,
                'don_vi_tinh' => $loai->don_vi_tinh,
                'gia_tri_quy_doi' => (float)$loai->gia_tri_quy_doi,
            ]);
            break;

        // Sổ theo dõi 1 nhân viên
        case 'soTheoDoi':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XEM);
            $nvId = Helper::postInt('nhan_vien_id');
            $nam  = Helper::postInt('nam', (int)date('Y'));
            $nv   = DM_NhanVien_BUS::getById($nvId);
            if (!$nv) ResponseHelper::error('Không tìm thấy nhân viên');
            $data = DT_Cme_BUS::soTheoDoiNhanVien($nvId, $nam);
            $data['nhan_vien'] = $nv;
            ResponseHelper::success('OK', $data);
            break;

        // Tổng quan
        case 'tongQuan':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XEM);
            ResponseHelper::success('OK', DT_Cme_BUS::tongQuan(Helper::postInt('nam', (int)date('Y'))));
            break;

        // Cảnh báo NV chưa đạt ngưỡng (có phân trang)
        case 'canhBao':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XEM);
            $page = max(1, Helper::postInt('page', 1));
            $size = Helper::postInt('pageSize', AppConfig::DEFAULT_PAGE_SIZE);
            ResponseHelper::success('OK', DT_Cme_BUS::canhBaoChuaDat(
                Helper::postInt('nam', (int)date('Y')),
                [
                    'khoa_phong_id' => Helper::postInt('khoa_phong_id', 0),
                    'search'        => Helper::postStr('search'),
                    'trang_thai'    => Helper::postStr('trang_thai'),
                ],
                $page, $size
            ));
            break;

        // Báo cáo
        case 'baoCaoTheoNhom':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XEM);
            ResponseHelper::success('OK', DT_Cme_BUS::tongToanVienTheoNhom(Helper::postInt('nam', 0)));
            break;
        case 'baoCaoTheoKhoa':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XEM);
            ResponseHelper::success('OK', DT_Cme_BUS::tongTheoKhoaPhong(Helper::postInt('nam', 0)));
            break;
        case 'baoCaoTheoNhanVien':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XEM);
            ResponseHelper::success('OK', DT_Cme_BUS::baoCaoTheoNhanVien([
                'nam' => Helper::postInt('nam', 0),
                'khoa_phong_id' => Helper::postInt('khoa_phong_id', 0),
            ]));
            break;

        // Combos
        case 'searchNhanVien':
            // Typeahead tìm nhân viên theo mã/tên (+ lọc khoa)
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XEM);
            ResponseHelper::success('OK', DM_NhanVien_BUS::search(
                Helper::postStr('q'), Helper::postInt('khoa_phong_id', 0), 20));
            break;
        case 'getNhanVienBrief':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XEM);
            $nv = DM_NhanVien_BUS::getBrief(Helper::postInt('id'));
            $nv ? ResponseHelper::success('OK', $nv) : ResponseHelper::error('Không tìm thấy');
            break;
        case 'getNhanVienCombo':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XEM);
            ResponseHelper::success('OK', DM_NhanVien_BUS::getCombo(Helper::postInt('khoa_phong_id', 0)));
            break;
        case 'getLoaiCombo':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XEM);
            ResponseHelper::success('OK', DT_Cme_BUS::loaiGetCombo(Helper::postInt('nhom_id', 0)));
            break;
        case 'getNhomCombo':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XEM);
            ResponseHelper::success('OK', DT_Cme_BUS::nhomGetCombo());
            break;
        case 'getKhoaPhongCombo':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XEM);
            ResponseHelper::success('OK', DM_KhoaPhong_BUS::getCombo());
            break;
        case 'getNamCombo':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XEM);
            ResponseHelper::success('OK', DT_Cme_BUS::getNamCombo());
            break;

        default:
            ResponseHelper::error('Action không hợp lệ');
    }
} catch (Throwable $ex) {
    ResponseHelper::error(AppConfig::APP_DEBUG ? $ex->getMessage() : 'Lỗi hệ thống', 500);
}
