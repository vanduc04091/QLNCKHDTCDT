<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../BUS/DT_Cme_BUS.php';
require_once __DIR__ . '/../../BUS/DM_KhoaPhong_BUS.php';

Helper::requireAjaxCsrf();
$action = Helper::post('action', '');
$u = SessionHelper::userId();
$MODULE = DT_Cme_BUS::MODULE_DANH_MUC;

try {
    switch ($action) {
        // ---------- NHÓM ----------
        case 'nhomGetPaged':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XEM);
            $page = Helper::postInt('page', 1);
            $size = Helper::postInt('pageSize', AppConfig::DEFAULT_PAGE_SIZE);
            $res = DT_Cme_BUS::nhomGetPaged($page, $size, Helper::postStr('search'), Helper::postInt('da_xoa', 0));
            ResponseHelper::paged($res['data'], $page, $size, $res['totalRecords']);
            break;

        case 'nhomGetById':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XEM);
            $e = DT_Cme_BUS::nhomGetById(Helper::postInt('id'));
            $e ? ResponseHelper::success('OK', $e) : ResponseHelper::error('Không tìm thấy');
            break;

        case 'nhomInsert':
        case 'nhomUpdate':
            $isU = $action === 'nhomUpdate';
            PhanQuyenHelper::requireQuyen($MODULE, $isU ? PhanQuyenHelper::QUYEN_SUA : PhanQuyenHelper::QUYEN_THEM);
            $e = new DT_CmeNhom_PUBLIC();
            if ($isU) $e->id = Helper::postInt('id');
            $e->ma_nhom = Helper::postStr('ma_nhom');
            $e->ten_nhom = Helper::postStr('ten_nhom');
            $e->thu_tu = Helper::postInt('thu_tu', 0);
            if ($isU) { $e->nguoi_cap_nhat = $u; $res = DT_Cme_BUS::nhomUpdate($e); }
            else      { $e->nguoi_tao = $u;      $res = DT_Cme_BUS::nhomInsert($e); }
            $res['success'] ? ResponseHelper::success($res['message'], $res['data'] ?? null) : ResponseHelper::error($res['message']);
            break;

        case 'nhomTrash':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XOA);
            $res = DT_Cme_BUS::nhomTrash(Helper::postInt('id'), $u);
            $res['success'] ? ResponseHelper::success($res['message']) : ResponseHelper::error($res['message']);
            break;

        case 'nhomRestore':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_SUA);
            $res = DT_Cme_BUS::nhomRestore(Helper::postInt('id'), $u);
            $res['success'] ? ResponseHelper::success($res['message']) : ResponseHelper::error($res['message']);
            break;

        // ---------- LOẠI ----------
        case 'loaiGetPaged':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XEM);
            $page = Helper::postInt('page', 1);
            $size = Helper::postInt('pageSize', AppConfig::DEFAULT_PAGE_SIZE);
            $res = DT_Cme_BUS::loaiGetPaged($page, $size, Helper::postStr('search'), Helper::postInt('da_xoa', 0), Helper::postInt('nhom_id', 0));
            ResponseHelper::paged($res['data'], $page, $size, $res['totalRecords']);
            break;

        case 'loaiGetById':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XEM);
            $e = DT_Cme_BUS::loaiGetById(Helper::postInt('id'));
            $e ? ResponseHelper::success('OK', $e) : ResponseHelper::error('Không tìm thấy');
            break;

        case 'loaiInsert':
        case 'loaiUpdate':
            $isU = $action === 'loaiUpdate';
            PhanQuyenHelper::requireQuyen($MODULE, $isU ? PhanQuyenHelper::QUYEN_SUA : PhanQuyenHelper::QUYEN_THEM);
            $e = new DT_CmeLoai_PUBLIC();
            if ($isU) $e->id = Helper::postInt('id');
            $e->nhom_id = Helper::postInt('nhom_id');
            $e->ma_loai = Helper::postStr('ma_loai');
            $e->ten_loai = Helper::postStr('ten_loai');
            $e->kieu_quy_doi = Helper::postStr('kieu_quy_doi') ?: 'co_dinh';
            $e->gia_tri_quy_doi = (float)Helper::post('gia_tri_quy_doi', 1);
            $e->don_vi_tinh = Helper::postStr('don_vi_tinh') ?: null;
            $e->khoa_phong_id = Helper::postInt('khoa_phong_id') ?: null;
            $e->thu_tu = Helper::postInt('thu_tu', 0);
            if ($isU) { $e->nguoi_cap_nhat = $u; $res = DT_Cme_BUS::loaiUpdate($e); }
            else      { $e->nguoi_tao = $u;      $res = DT_Cme_BUS::loaiInsert($e); }
            $res['success'] ? ResponseHelper::success($res['message'], $res['data'] ?? null) : ResponseHelper::error($res['message']);
            break;

        case 'loaiTrash':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XOA);
            $res = DT_Cme_BUS::loaiTrash(Helper::postInt('id'), $u);
            $res['success'] ? ResponseHelper::success($res['message']) : ResponseHelper::error($res['message']);
            break;

        case 'loaiRestore':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_SUA);
            $res = DT_Cme_BUS::loaiRestore(Helper::postInt('id'), $u);
            $res['success'] ? ResponseHelper::success($res['message']) : ResponseHelper::error($res['message']);
            break;

        // ---------- COMBO ----------
        case 'getNhomCombo':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XEM);
            ResponseHelper::success('OK', DT_Cme_BUS::nhomGetCombo());
            break;

        case 'getKhoaPhongCombo':
            PhanQuyenHelper::requireQuyen($MODULE, PhanQuyenHelper::QUYEN_XEM);
            ResponseHelper::success('OK', DM_KhoaPhong_BUS::getCombo());
            break;

        default:
            ResponseHelper::error('Action không hợp lệ');
    }
} catch (Throwable $ex) {
    ResponseHelper::error(AppConfig::APP_DEBUG ? $ex->getMessage() : 'Lỗi hệ thống', 500);
}
