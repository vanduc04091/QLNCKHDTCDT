<?php
require_once __DIR__ . '/../../bootstrap.php';

Helper::requireLogin();
if (!PhanQuyenHelper::hasQuyen('NCKH_DeTai', PhanQuyenHelper::QUYEN_XEM)) {
    echo 'Bạn không có quyền truy cập chức năng này.'; exit;
}

$canAdd = PhanQuyenHelper::hasQuyen('NCKH_DeTai', PhanQuyenHelper::QUYEN_THEM);
$canEdit = PhanQuyenHelper::hasQuyen('NCKH_DeTai', PhanQuyenHelper::QUYEN_SUA);
$canDel = PhanQuyenHelper::hasQuyen('NCKH_DeTai', PhanQuyenHelper::QUYEN_XOA);

$pageTitle = 'Đề tài NCKH';
$activeMenu = 'NCKH_DeTai';
require __DIR__ . '/../layouts/header.php';
?>
<style>
.dt-drawer { position:fixed; top:0; right:-960px; width:min(960px, 96vw); height:100vh; background:#fff; box-shadow:-8px 0 32px rgba(0,0,0,.15); transition:right .3s ease; z-index:80; display:flex; flex-direction:column; }
.dt-drawer.open { right:0; }
.dt-drawer-head { padding:16px 20px; border-bottom:1px solid #e2e8f0; display:flex; justify-content:space-between; align-items:center; gap:12px; }
.dt-drawer-head h3 { margin:0; font-size:16px; }
.dt-drawer-tabs { display:flex; gap:2px; padding:0 12px; border-bottom:1px solid #e2e8f0; background:#f8fafc; }
.dt-drawer-tab { padding:10px 16px; cursor:pointer; border:none; background:none; font-size:13px; color:#64748b; border-bottom:3px solid transparent; }
.dt-drawer-tab.active { color:#2563eb; border-bottom-color:#2563eb; font-weight:600; }
.dt-drawer-body { flex:1; overflow:auto; padding:16px 20px; }
.dt-tabpane { display:none; }
.dt-tabpane.active { display:block; }
.dt-info-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(280px,1fr)); gap:10px 20px; }
.dt-info-grid .row { display:flex; gap:6px; padding:6px 0; border-bottom:1px dashed #e2e8f0; }
.dt-info-grid .row .lbl { color:#64748b; min-width:130px; font-size:13px; }
.dt-info-grid .row .val { color:#0f172a; flex:1; font-size:13px; }
.dt-progress { background:#e2e8f0; border-radius:8px; height:10px; overflow:hidden; }
.dt-progress > span { display:block; height:100%; background:linear-gradient(90deg,#2563eb,#22c55e); transition:width .3s; }
.tt-badge { display:inline-block; padding:3px 10px; border-radius:99px; font-size:12px; font-weight:600; }
.tt-0 { background:#fef3c7; color:#92400e; }
.tt-1 { background:#dbeafe; color:#1e40af; }
.tt-2 { background:#dcfce7; color:#166534; }
.tt-3 { background:#e2e8f0; color:#475569; }
.tt-4 { background:#fee2e2; color:#991b1b; }
.dt-sub-card { background:#f8fafc; padding:12px; border-radius:8px; margin-bottom:10px; border:1px solid #e2e8f0; }
.dt-sub-card .head { display:flex; justify-content:space-between; align-items:start; margin-bottom:6px; }
.dt-sub-card .title { font-weight:600; color:#0f172a; }
.dt-sub-card .meta { font-size:12px; color:#64748b; margin-top:2px; }
.dt-sub-card .body { font-size:13px; color:#334155; margin-top:6px; line-height:1.5; }
</style>

<div class="breadcrumb">
    <a href="<?= AppConfig::baseUrl('GUI/dashboard/index.php') ?>">Trang chủ</a>
    <span class="sep">›</span> Nghiên cứu khoa học
    <span class="sep">›</span> <span>Đề tài / Sáng kiến</span>
</div>

<div class="card">
    <div class="toolbar" style="flex-wrap:wrap;gap:8px">
        <div class="left" style="flex-wrap:wrap;gap:8px;display:flex">
            <input type="text" id="search" class="form-control" placeholder="Tìm mã, tên, từ khóa, chủ nhiệm..." style="min-width:280px">
            <select id="fNam" class="form-select" style="width:110px"></select>
            <select id="fCapDo" class="form-select" style="width:140px"><option value="">- Cấp độ -</option></select>
            <select id="fTheLoai" class="form-select" style="width:160px"><option value="">- Thể loại -</option></select>
            <select id="fKhoaPhong" class="form-select" style="width:170px"><option value="">- Khoa/Phòng -</option></select>
            <select id="fTrangThai" class="form-select" style="width:150px">
                <option value="">- Trạng thái -</option>
                <option value="0">Đề xuất</option>
                <option value="1">Đang thực hiện</option>
                <option value="2">Hoàn thành</option>
                <option value="3">Tạm dừng</option>
                <option value="4">Hủy</option>
            </select>
            <select id="fDaXoa" class="form-select" style="width:140px">
                <option value="0">Đang hoạt động</option>
                <option value="1">Thùng rác</option>
            </select>
        </div>
        <div class="right">
            <?php if ($canAdd): ?>
                <button type="button" class="btn btn-primary" onclick="openCreate()">+ Thêm đề tài</button>
            <?php endif; ?>
        </div>
    </div>
    <div class="table-wrap" id="tableWrap" style="position:relative;min-height:200px">
        <table class="table">
            <thead>
                <tr>
                    <th style="width:48px" class="text-center">#</th>
                    <th style="width:110px">Mã</th>
                    <th>Tên đề tài</th>
                    <th style="width:70px" class="text-center">Năm</th>
                    <th style="width:110px">Cấp độ</th>
                    <th style="width:140px">Thể loại</th>
                    <th style="width:170px">Chủ nhiệm</th>
                    <th style="width:130px" class="text-center">Trạng thái</th>
                    <th style="width:140px" class="text-right">Hành động</th>
                </tr>
            </thead>
            <tbody id="tbody"></tbody>
        </table>
    </div>
    <div class="pagination-wrap">
        <div id="pageInfo" class="text-muted">-</div>
        <div id="pageNav"></div>
    </div>
</div>

<!-- ============ MODAL FORM ĐỀ TÀI ============ -->
<div class="modal-backdrop" id="modalForm">
    <div class="modal" style="max-width:1000px">
        <div class="modal-header">
            <h3 id="modalTitle">Thêm đề tài</h3>
            <button type="button" class="close" onclick="closeModal()">&times;</button>
        </div>
        <form id="formMain">
            <div class="modal-body" style="max-height:78vh;overflow:auto">
                <input type="hidden" name="id" id="f_id">

                <h4 style="color:#2563eb;margin:6px 0 10px">Thông tin chung</h4>
                <div class="form-row">
                    <div class="form-group">
                        <label>Mã đề tài <span class="required">*</span></label>
                        <input type="text" name="ma_de_tai" id="f_ma" class="form-control" required maxlength="50">
                    </div>
                    <div class="form-group">
                        <label>Năm <span class="required">*</span></label>
                        <input type="number" name="nam" id="f_nam" class="form-control" required min="2000" max="2100" value="<?= date('Y') ?>">
                    </div>
                    <div class="form-group">
                        <label>Trạng thái</label>
                        <select name="trang_thai" id="f_trang_thai" class="form-select">
                            <option value="0">Đề xuất</option>
                            <option value="1">Đang thực hiện</option>
                            <option value="2">Hoàn thành</option>
                            <option value="3">Tạm dừng</option>
                            <option value="4">Hủy</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label>Tên đề tài <span class="required">*</span></label>
                    <input type="text" name="ten_de_tai" id="f_ten" class="form-control" required maxlength="500">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Cấp độ <span class="required">*</span></label>
                        <select name="cap_do_id" id="f_capdo" class="form-select" required></select>
                    </div>
                    <div class="form-group">
                        <label>Thể loại <span class="required">*</span></label>
                        <select name="the_loai_id" id="f_theloai" class="form-select" required></select>
                    </div>
                    <div class="form-group">
                        <label>Khoa/Phòng</label>
                        <select name="khoa_phong_id" id="f_khoa" class="form-select"><option value="">-</option></select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Chủ nhiệm <span class="required">*</span></label>
                        <select name="chu_nhiem_id" id="f_cn" class="form-select" required></select>
                    </div>
                    <div class="form-group">
                        <label>Thư ký</label>
                        <select name="thu_ky_id" id="f_tk" class="form-select"><option value="">-</option></select>
                    </div>
                </div>

                <div class="form-group">
                    <label>Mục tiêu</label>
                    <textarea name="muc_tieu" id="f_muctieu" class="form-control" rows="2"></textarea>
                </div>
                <div class="form-group">
                    <label>Tóm tắt</label>
                    <textarea name="tom_tat" id="f_tomtat" class="form-control" rows="3"></textarea>
                </div>
                <div class="form-group">
                    <label>Từ khóa</label>
                    <input type="text" name="tu_khoa" id="f_tukhoa" class="form-control" maxlength="255" placeholder="VD: cao huyết áp, rối loạn nhịp tim,...">
                </div>

                <h4 style="color:#2563eb;margin:18px 0 10px">Thời gian & Kinh phí</h4>
                <div class="form-row">
                    <div class="form-group">
                        <label>Ngày bắt đầu</label>
                        <input type="date" name="ngay_bat_dau" id="f_nbd" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Dự kiến kết thúc</label>
                        <input type="date" name="ngay_ket_thuc_du_kien" id="f_nkt" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Ngày nghiệm thu</label>
                        <input type="date" name="ngay_nghiem_thu" id="f_nnt" class="form-control">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Kinh phí dự toán (VNĐ)</label>
                        <input type="number" step="1000" name="kinh_phi_du_toan" id="f_kpd" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Kinh phí thực tế (VNĐ)</label>
                        <input type="number" step="1000" name="kinh_phi_thuc_te" id="f_kpt" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Nguồn kinh phí</label>
                        <input type="text" name="nguon_kinh_phi" id="f_nkp" class="form-control" maxlength="150">
                    </div>
                </div>

                <h4 style="color:#2563eb;margin:18px 0 10px">Quyết định & Nghiệm thu</h4>
                <div class="form-row">
                    <div class="form-group">
                        <label>Số quyết định</label>
                        <input type="text" name="quyet_dinh_phe_duyet" id="f_qd" class="form-control" maxlength="100">
                    </div>
                    <div class="form-group">
                        <label>Ngày quyết định</label>
                        <input type="date" name="ngay_quyet_dinh" id="f_nqd" class="form-control">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Xếp loại</label>
                        <select name="ket_qua_xep_loai" id="f_xl" class="form-select">
                            <option value="">- Chưa nghiệm thu -</option>
                            <option value="XuatSac">Xuất sắc</option>
                            <option value="Gioi">Giỏi</option>
                            <option value="Kha">Khá</option>
                            <option value="TrungBinhKha">Trung bình khá</option>
                            <option value="Dat">Đạt</option>
                            <option value="KhongDat">Không đạt</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Điểm số</label>
                        <input type="number" step="0.01" max="10" min="0" name="diem_so" id="f_ds" class="form-control">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Số QĐ công nhận</label>
                        <input type="text" name="quyet_dinh_cong_nhan" id="f_qdcn" class="form-control" maxlength="100">
                    </div>
                    <div class="form-group">
                        <label>Ngày QĐ công nhận</label>
                        <input type="date" name="ngay_quyet_dinh_cong_nhan" id="f_nqdcn" class="form-control">
                    </div>
                </div>
                <div class="form-group">
                    <label>Giá trị ứng dụng thực tiễn</label>
                    <textarea name="noi_dung_ung_dung" id="f_ndud" class="form-control" rows="2"></textarea>
                </div>

                <h4 style="color:#2563eb;margin:18px 0 10px">Lịch bảo vệ đề cương</h4>
                <div class="form-row">
                    <div class="form-group">
                        <label>Phiên</label>
                        <input type="text" name="phien_bao_ve" id="f_pbv" class="form-control" maxlength="50" placeholder="VD: Phiên 1 - Từ 8h00">
                    </div>
                    <div class="form-group">
                        <label>Địa điểm</label>
                        <input type="text" name="dia_diem_bao_ve" id="f_ddbv" class="form-control" maxlength="255">
                    </div>
                    <div class="form-group">
                        <label>Ngày bảo vệ</label>
                        <input type="date" name="ngay_bao_ve" id="f_ngbv" class="form-control">
                    </div>
                </div>
                <div class="form-group">
                    <label>Tên khoa/phòng/TT (text gốc)</label>
                    <input type="text" name="ten_khoa_text" id="f_tkt" class="form-control" maxlength="255" placeholder="Ghi tên khoa nếu chưa có trong DM Khoa/Phòng">
                </div>

                <div id="bbao_section" style="display:none">
                    <h4 style="color:#2563eb;margin:18px 0 10px">Thông tin bài báo</h4>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Tên tạp chí</label>
                            <input type="text" name="ten_tap_chi" id="f_ttc" class="form-control" maxlength="255">
                        </div>
                        <div class="form-group">
                            <label>Số tạp chí</label>
                            <input type="text" name="so_tap_chi" id="f_stc" class="form-control" maxlength="50">
                        </div>
                        <div class="form-group">
                            <label>Năm xuất bản</label>
                            <input type="number" name="nam_xuat_ban" id="f_nxb" class="form-control" min="1900" max="2100">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>ISSN / DOI</label>
                            <input type="text" name="issn_doi" id="f_issn" class="form-control" maxlength="100">
                        </div>
                        <div class="form-group">
                            <label>Link bài báo</label>
                            <input type="url" name="link_bai_bao" id="f_lnk" class="form-control" maxlength="500">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn" onclick="closeModal()">Hủy</button>
                <button type="submit" class="btn btn-primary">Lưu</button>
            </div>
        </form>
    </div>
</div>

<!-- ============ DRAWER CHI TIẾT ============ -->
<div class="dt-drawer" id="drawer">
    <div class="dt-drawer-head">
        <div>
            <h3 id="dr_title">Chi tiết đề tài</h3>
            <div id="dr_subtitle" style="color:#64748b;font-size:12px;margin-top:2px"></div>
        </div>
        <button type="button" class="btn" onclick="closeDrawer()">×</button>
    </div>
    <div class="dt-drawer-tabs">
        <button class="dt-drawer-tab active" data-tab="info">Thông tin</button>
        <button class="dt-drawer-tab" data-tab="tv">Thành viên</button>
        <button class="dt-drawer-tab" data-tab="hd">Hội đồng</button>
        <button class="dt-drawer-tab" data-tab="td">Tiến độ</button>
        <button class="dt-drawer-tab" data-tab="tl">Tài liệu</button>
    </div>
    <div class="dt-drawer-body">
        <div class="dt-tabpane active" id="pane-info"></div>
        <div class="dt-tabpane" id="pane-tv"></div>
        <div class="dt-tabpane" id="pane-hd"></div>
        <div class="dt-tabpane" id="pane-td"></div>
        <div class="dt-tabpane" id="pane-tl"></div>
    </div>
</div>

<!-- ============ MODAL THÀNH VIÊN ============ -->
<div class="modal-backdrop" id="modalTV">
    <div class="modal" style="max-width:600px">
        <div class="modal-header">
            <h3 id="tvTitle">Thêm thành viên</h3>
            <button type="button" class="close" onclick="$('#modalTV').removeClass('open')">&times;</button>
        </div>
        <form id="formTV">
            <div class="modal-body">
                <input type="hidden" name="id" id="tv_id">
                <input type="hidden" name="de_tai_id" id="tv_dt">
                <div class="form-group">
                    <label>Nhân viên (chọn nếu là người trong bệnh viện)</label>
                    <select name="nhan_vien_id" id="tv_nv" class="form-select"><option value="">- Để trống nếu là người ngoài -</option></select>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Họ tên (người ngoài)</label>
                        <input type="text" name="ho_ten_ngoai" id="tv_hn" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Đơn vị (người ngoài)</label>
                        <input type="text" name="don_vi_ngoai" id="tv_dn" class="form-control">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Vai trò</label>
                        <input type="text" name="vai_tro" id="tv_vt" class="form-control" value="Thành viên" maxlength="100">
                    </div>
                    <div class="form-group">
                        <label>Mã NV (text)</label>
                        <input type="text" name="ma_nv_text" id="tv_mnv" class="form-control" maxlength="50" placeholder="Mã NV gốc nếu chưa có DM">
                    </div>
                    <div class="form-group">
                        <label>% đóng góp</label>
                        <input type="number" step="0.01" min="0" max="100" name="phan_tram_dong_gop" id="tv_pt" class="form-control">
                    </div>
                </div>
                <div class="form-group">
                    <label>Ghi chú</label>
                    <textarea name="ghi_chu" id="tv_gc" class="form-control" rows="2"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn" onclick="$('#modalTV').removeClass('open')">Hủy</button>
                <button type="submit" class="btn btn-primary">Lưu</button>
            </div>
        </form>
    </div>
</div>

<!-- ============ MODAL HỘI ĐỒNG ============ -->
<div class="modal-backdrop" id="modalHD">
    <div class="modal" style="max-width:620px">
        <div class="modal-header">
            <h3 id="hdTitle">Thêm thành viên hội đồng</h3>
            <button type="button" class="close" onclick="$('#modalHD').removeClass('open')">&times;</button>
        </div>
        <form id="formHD">
            <div class="modal-body">
                <input type="hidden" name="id" id="hd_id">
                <input type="hidden" name="de_tai_id" id="hd_dt">
                <div class="form-row">
                    <div class="form-group">
                        <label>Học vị / Chức danh</label>
                        <input type="text" name="chuc_danh_hoc_vi" id="hd_cd" class="form-control" maxlength="50" placeholder="VD: BSCKII., ThS., TS....">
                    </div>
                    <div class="form-group">
                        <label>Họ tên <span class="required">*</span></label>
                        <input type="text" name="ho_ten" id="hd_ht" class="form-control" required maxlength="150">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Vai trò trong hội đồng <span class="required">*</span></label>
                        <select name="vai_tro_hd" id="hd_vt" class="form-select" required>
                            <option value="ChuTich">Chủ tịch</option>
                            <option value="ThuKy">Thư ký</option>
                            <option value="PhanBien1">Phản biện 1</option>
                            <option value="PhanBien2">Phản biện 2</option>
                            <option value="ThanhVien" selected>Thành viên</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Thứ tự</label>
                        <input type="number" name="thu_tu" id="hd_tu" class="form-control" value="0">
                    </div>
                </div>
                <div class="form-group">
                    <label>Khoa/Phòng (nếu có DM)</label>
                    <select name="khoa_phong_id" id="hd_kp" class="form-select"><option value="">-</option></select>
                </div>
                <div class="form-group">
                    <label>Tên khoa (text gốc)</label>
                    <input type="text" name="ten_khoa_text" id="hd_tkt" class="form-control" maxlength="255">
                </div>
                <div class="form-group">
                    <label>Liên kết nhân viên (nếu có)</label>
                    <select name="nhan_vien_id" id="hd_nv" class="form-select"><option value="">-</option></select>
                </div>
                <div class="form-group">
                    <label>Ghi chú</label>
                    <textarea name="ghi_chu" id="hd_gc" class="form-control" rows="2"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn" onclick="$('#modalHD').removeClass('open')">Hủy</button>
                <button type="submit" class="btn btn-primary">Lưu</button>
            </div>
        </form>
    </div>
</div>

<!-- ============ MODAL TIẾN ĐỘ ============ -->
<div class="modal-backdrop" id="modalTD">
    <div class="modal" style="max-width:700px">
        <div class="modal-header">
            <h3 id="tdTitle">Thêm báo cáo tiến độ</h3>
            <button type="button" class="close" onclick="$('#modalTD').removeClass('open')">&times;</button>
        </div>
        <form id="formTD">
            <div class="modal-body">
                <input type="hidden" name="id" id="td_id">
                <input type="hidden" name="de_tai_id" id="td_dt">
                <div class="form-row">
                    <div class="form-group">
                        <label>Kỳ báo cáo <span class="required">*</span></label>
                        <input type="text" name="ky_bao_cao" id="td_ky" class="form-control" required placeholder="VD: Q1/2026">
                    </div>
                    <div class="form-group">
                        <label>Ngày báo cáo <span class="required">*</span></label>
                        <input type="date" name="ngay_bao_cao" id="td_ng" class="form-control" required value="<?= date('Y-m-d') ?>">
                    </div>
                    <div class="form-group">
                        <label>% hoàn thành</label>
                        <input type="number" name="phan_tram_hoan_thanh" id="td_pt" class="form-control" min="0" max="100" value="0">
                    </div>
                </div>
                <div class="form-group">
                    <label>Người báo cáo</label>
                    <select name="nguoi_bao_cao_id" id="td_nbc" class="form-select"><option value="">-</option></select>
                </div>
                <div class="form-group">
                    <label>Công việc đã làm</label>
                    <textarea name="cong_viec_da_lam" id="td_cl" class="form-control" rows="3"></textarea>
                </div>
                <div class="form-group">
                    <label>Công việc tiếp theo</label>
                    <textarea name="cong_viec_tiep_theo" id="td_ct" class="form-control" rows="2"></textarea>
                </div>
                <div class="form-group">
                    <label>Khó khăn / Vướng mắc</label>
                    <textarea name="kho_khan_vuong_mac" id="td_kk" class="form-control" rows="2"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn" onclick="$('#modalTD').removeClass('open')">Hủy</button>
                <button type="submit" class="btn btn-primary">Lưu</button>
            </div>
        </form>
    </div>
</div>

<!-- ============ MODAL TÀI LIỆU UPLOAD ============ -->
<div class="modal-backdrop" id="modalTL">
    <div class="modal" style="max-width:600px">
        <div class="modal-header">
            <h3>Tải tài liệu lên</h3>
            <button type="button" class="close" onclick="$('#modalTL').removeClass('open')">&times;</button>
        </div>
        <form id="formTL" enctype="multipart/form-data">
            <div class="modal-body">
                <input type="hidden" name="de_tai_id" id="tl_dt">
                <div class="form-row">
                    <div class="form-group">
                        <label>Loại tài liệu</label>
                        <select name="loai_tai_lieu" id="tl_loai" class="form-select">
                            <option value="DeCuong">Đề cương</option>
                            <option value="QuyetDinh">Quyết định</option>
                            <option value="BienBan">Biên bản</option>
                            <option value="BaoCao">Báo cáo</option>
                            <option value="FileGoc">File gốc</option>
                            <option value="Khac" selected>Khác</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Tên hiển thị</label>
                        <input type="text" name="ten_tai_lieu" id="tl_ten" class="form-control" placeholder="Để trống = lấy theo tên file">
                    </div>
                </div>
                <div class="form-group">
                    <label>File <span class="required">*</span></label>
                    <input type="file" name="file" id="tl_file" class="form-control" required>
                    <div class="text-muted" style="font-size:12px;margin-top:4px">Tối đa 20MB. Cho phép: pdf, doc(x), xls(x), ppt(x), jpg, png, zip, rar, 7z.</div>
                </div>
                <div class="form-group">
                    <label>Mô tả</label>
                    <textarea name="mo_ta" id="tl_mt" class="form-control" rows="2"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn" onclick="$('#modalTL').removeClass('open')">Hủy</button>
                <button type="submit" class="btn btn-primary">Tải lên</button>
            </div>
        </form>
    </div>
</div>

<script>
var URL = APP_BASE + 'GUI/NCKH_DeTai/ajax_handler.php';
var DOWNLOAD_URL = APP_BASE + 'GUI/NCKH_DeTai/download.php';
var CAN_EDIT = <?= $canEdit?'true':'false' ?>;
var CAN_DEL = <?= $canDel?'true':'false' ?>;
var ICON_EYE = '<svg class="icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>';
var ICON_EDIT = '<svg class="icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>';
var ICON_TRASH = '<svg class="icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>';

var TT_NAMES = {0:'Đề xuất', 1:'Đang thực hiện', 2:'Hoàn thành', 3:'Tạm dừng', 4:'Hủy'};
var XL_NAMES = {XuatSac:'Xuất sắc', Gioi:'Giỏi', Kha:'Khá', TrungBinhKha:'Trung bình khá', Dat:'Đạt', KhongDat:'Không đạt'};
var HD_VAITRO = {ChuTich:'Chủ tịch', ThuKy:'Thư ký', PhanBien1:'Phản biện 1', PhanBien2:'Phản biện 2', ThanhVien:'Thành viên'};
var LOAI_TL_NAMES = {DeCuong:'Đề cương', QuyetDinh:'Quyết định', BienBan:'Biên bản', BaoCao:'Báo cáo', FileGoc:'File gốc', Khac:'Khác'};

var state = { page: 1, pageSize: 20, search: '', nam: 0, capDoId: 0, theLoaiId: 0, khoaPhongId: 0, trangThai: '', daXoa: 0 };
var currentDeTaiId = 0;
var COMBO = { capDo: [], theLoai: [], khoaPhong: [], nhanVien: [] };

/* ===== INIT ===== */
function fillYears() {
    var $f = $('#fNam'), y = new Date().getFullYear();
    $f.append('<option value="">- Năm -</option>');
    for (var i = y + 1; i >= y - 6; i--) $f.append('<option value="' + i + '">' + i + '</option>');
    $f.val('');
}

function loadCombos() {
    return $.when(
        APP.ajax(URL, {action:'getComboCapDo'}),
        APP.ajax(URL, {action:'getComboTheLoai'}),
        APP.ajax(URL, {action:'getComboKhoaPhong'}),
        APP.ajax(URL, {action:'getComboNhanVien', kw:''})
    ).done(function (cd, tl, kp, nv) {
        COMBO.capDo = cd[0].data || []; COMBO.theLoai = tl[0].data || [];
        COMBO.khoaPhong = kp[0].data || []; COMBO.nhanVien = nv[0].data || [];

        $.each(COMBO.capDo, function (_, x) {
            $('#fCapDo').append('<option value="' + x.id + '">' + APP.escape(x.ten_cap_do) + '</option>');
            $('#f_capdo').append('<option value="' + x.id + '">' + APP.escape(x.ten_cap_do) + '</option>');
        });
        $.each(COMBO.theLoai, function (_, x) {
            $('#fTheLoai').append('<option value="' + x.id + '">' + APP.escape(x.ten_the_loai) + '</option>');
            $('#f_theloai').append('<option value="' + x.id + '" data-ma="' + x.ma_the_loai + '">' + APP.escape(x.ten_the_loai) + '</option>');
        });
        $.each(COMBO.khoaPhong, function (_, x) {
            var t = APP.escape(x.ten_khoa);
            $('#fKhoaPhong').append('<option value="' + x.id + '">' + t + '</option>');
            $('#f_khoa').append('<option value="' + x.id + '">' + t + '</option>');
            $('#hd_kp').append('<option value="' + x.id + '">' + t + '</option>');
        });
        $.each(COMBO.nhanVien, function (_, x) {
            var t = APP.escape((x.ma_nv ? '[' + x.ma_nv + '] ' : '') + x.ho_ten);
            $('#f_cn').append('<option value="' + x.id + '">' + t + '</option>');
            $('#f_tk').append('<option value="' + x.id + '">' + t + '</option>');
            $('#tv_nv').append('<option value="' + x.id + '">' + t + '</option>');
            $('#hd_nv').append('<option value="' + x.id + '">' + t + '</option>');
            $('#td_nbc').append('<option value="' + x.id + '">' + t + '</option>');
        });
    });
}

/* ===== LIST ===== */
function load() {
    APP.showLoading('#tableWrap');
    APP.ajax(URL, {
        action:'getPaged', page:state.page, pageSize:state.pageSize,
        search:state.search, nam:state.nam, cap_do_id:state.capDoId, the_loai_id:state.theLoaiId,
        khoa_phong_id:state.khoaPhongId, trang_thai:state.trangThai, da_xoa:state.daXoa
    }).done(function (res) {
        APP.hideLoading('#tableWrap');
        if (!res.success) { APP.toast(res.message,'error'); return; }
        renderRows(res.data); renderInfo(res.pagination);
    });
}

function renderRows(rows) {
    var $tb = $('#tbody').empty();
    if (!rows.length) { $tb.append('<tr><td colspan="9"><div class="empty-state">Không có dữ liệu</div></td></tr>'); return; }
    var stt = (state.page - 1) * state.pageSize;
    rows.forEach(function (r) {
        stt++;
        var tt = '<span class="tt-badge tt-' + r.trang_thai + '">' + TT_NAMES[r.trang_thai] + '</span>';
        var actions = '<button class="btn btn-sm" title="Xem" onclick="openDrawer(' + r.id + ')">' + ICON_EYE + '</button>';
        if (state.daXoa == 0) {
            if (CAN_EDIT) actions += '<button class="btn btn-sm" title="Sửa" onclick="openEdit(' + r.id + ')">' + ICON_EDIT + '</button>';
            if (CAN_DEL) actions += '<button class="btn btn-sm btn-danger" title="Xóa" onclick="trashItem(' + r.id + ')">' + ICON_TRASH + '</button>';
        } else {
            if (CAN_EDIT) actions += '<button class="btn btn-sm btn-success" onclick="restoreItem(' + r.id + ')">↺</button>';
            if (CAN_DEL) actions += '<button class="btn btn-sm btn-danger" onclick="deleteItem(' + r.id + ')">✕</button>';
        }
        $tb.append(
            '<tr>' +
                '<td class="text-center">' + stt + '</td>' +
                '<td><strong>' + APP.escape(r.ma_de_tai) + '</strong></td>' +
                '<td><a href="javascript:;" onclick="openDrawer(' + r.id + ')" style="color:#0f172a;font-weight:500;text-decoration:none">' + APP.escape(r.ten_de_tai) + '</a></td>' +
                '<td class="text-center">' + r.nam + '</td>' +
                '<td>' + APP.escape(r.ten_cap_do || '-') + '</td>' +
                '<td>' + APP.escape(r.ten_the_loai || '-') + '</td>' +
                '<td>' + APP.escape(r.ho_ten_chu_nhiem || '-') + '</td>' +
                '<td class="text-center">' + tt + '</td>' +
                '<td><div class="actions">' + actions + '</div></td>' +
            '</tr>'
        );
    });
}

function renderInfo(p) {
    var from = (p.currentPage - 1) * p.pageSize + 1;
    var to = Math.min(from + p.pageSize - 1, p.totalRecords);
    $('#pageInfo').text(p.totalRecords ? 'Hiển thị ' + from + '-' + to + ' / ' + p.totalRecords : 'Không có bản ghi');
    $('#pageNav').html(APP.renderPagination(p));
}

$('#pageNav').on('click', 'button[data-p]', function () { var p=parseInt($(this).data('p'),10); if(!p||p===state.page)return; state.page=p; load(); });
$('#search').on('input', APP.debounce(function () { state.search=$(this).val(); state.page=1; load(); }, 400));
$('#fNam').on('change', function () { state.nam = parseInt(this.value,10) || 0; state.page=1; load(); });
$('#fCapDo').on('change', function () { state.capDoId = parseInt(this.value,10) || 0; state.page=1; load(); });
$('#fTheLoai').on('change', function () { state.theLoaiId = parseInt(this.value,10) || 0; state.page=1; load(); });
$('#fKhoaPhong').on('change', function () { state.khoaPhongId = parseInt(this.value,10) || 0; state.page=1; load(); });
$('#fTrangThai').on('change', function () { state.trangThai = this.value; state.page=1; load(); });
$('#fDaXoa').on('change', function () { state.daXoa = parseInt(this.value,10) || 0; state.page=1; load(); });

/* ===== FORM ĐỀ TÀI ===== */
function openCreate() {
    $('#modalTitle').text('Thêm đề tài');
    $('#formMain')[0].reset(); $('#f_id').val(''); $('#f_nam').val(new Date().getFullYear());
    $('#bbao_section').hide();
    $('#modalForm').addClass('open');
}
function openEdit(id) {
    APP.ajax(URL, {action:'getById', id:id}).done(function (res) {
        if (!res.success) { APP.toast(res.message,'error'); return; }
        var e = res.data;
        $('#modalTitle').text('Sửa đề tài: ' + e.ma_de_tai);
        $('#f_id').val(e.id); $('#f_ma').val(e.ma_de_tai); $('#f_ten').val(e.ten_de_tai);
        $('#f_nam').val(e.nam); $('#f_capdo').val(e.cap_do_id); $('#f_theloai').val(e.the_loai_id);
        $('#f_khoa').val(e.khoa_phong_id || ''); $('#f_cn').val(e.chu_nhiem_id); $('#f_tk').val(e.thu_ky_id || '');
        $('#f_muctieu').val(e.muc_tieu || ''); $('#f_tomtat').val(e.tom_tat || ''); $('#f_tukhoa').val(e.tu_khoa || '');
        $('#f_nbd').val(e.ngay_bat_dau || ''); $('#f_nkt').val(e.ngay_ket_thuc_du_kien || ''); $('#f_nnt').val(e.ngay_nghiem_thu || '');
        $('#f_kpd').val(e.kinh_phi_du_toan || ''); $('#f_kpt').val(e.kinh_phi_thuc_te || ''); $('#f_nkp').val(e.nguon_kinh_phi || '');
        $('#f_qd').val(e.quyet_dinh_phe_duyet || ''); $('#f_nqd').val(e.ngay_quyet_dinh || '');
        $('#f_xl').val(e.ket_qua_xep_loai || ''); $('#f_ds').val(e.diem_so || ''); $('#f_ndud').val(e.noi_dung_ung_dung || '');
        $('#f_qdcn').val(e.quyet_dinh_cong_nhan || ''); $('#f_nqdcn').val(e.ngay_quyet_dinh_cong_nhan || '');
        $('#f_ttc').val(e.ten_tap_chi || ''); $('#f_stc').val(e.so_tap_chi || ''); $('#f_nxb').val(e.nam_xuat_ban || '');
        $('#f_issn').val(e.issn_doi || ''); $('#f_lnk').val(e.link_bai_bao || '');
        $('#f_pbv').val(e.phien_bao_ve || ''); $('#f_ddbv').val(e.dia_diem_bao_ve || ''); $('#f_ngbv').val(e.ngay_bao_ve || '');
        $('#f_tkt').val(e.ten_khoa_text || '');
        $('#f_trang_thai').val(e.trang_thai);
        toggleBaiBaoSection();
        $('#modalForm').addClass('open');
    });
}
$('#f_theloai').on('change', toggleBaiBaoSection);
function toggleBaiBaoSection() {
    var ma = $('#f_theloai option:selected').data('ma');
    if (ma === 'BAIBAO') $('#bbao_section').show(); else $('#bbao_section').hide();
}
function closeModal() { $('#modalForm').removeClass('open'); }

$('#formMain').on('submit', function (e) {
    e.preventDefault();
    var data = $(this).serializeArray();
    data.push({name:'action', value: $('#f_id').val() ? 'update' : 'insert'});
    APP.ajax(URL, data).done(function (res) {
        if (res.success) { APP.toast(res.message,'success'); closeModal(); load(); }
        else APP.toast(res.message,'error');
    });
});

function trashItem(id) { APP.confirm('Chuyển vào thùng rác?', function () { APP.ajax(URL,{action:'trash', id:id}).done(function(r){ r.success?(APP.toast(r.message,'success'),load()):APP.toast(r.message,'error'); }); }); }
function restoreItem(id) { APP.ajax(URL,{action:'restore', id:id}).done(function(r){ r.success?(APP.toast(r.message,'success'),load()):APP.toast(r.message,'error'); }); }
function deleteItem(id) { APP.confirm('Xóa VĨNH VIỄN?', function(){ APP.ajax(URL,{action:'delete', id:id}).done(function(r){ r.success?(APP.toast(r.message,'success'),load()):APP.toast(r.message,'error'); }); }, {yesText:'Xóa vĩnh viễn'}); }

/* ===== DRAWER ===== */
function openDrawer(id) {
    currentDeTaiId = id;
    $('#drawer').addClass('open');
    $('.dt-drawer-tab').removeClass('active'); $('.dt-drawer-tab[data-tab="info"]').addClass('active');
    $('.dt-tabpane').removeClass('active'); $('#pane-info').addClass('active');
    loadDetail();
}
function closeDrawer() { $('#drawer').removeClass('open'); currentDeTaiId = 0; }
$('.dt-drawer-tabs').on('click', '.dt-drawer-tab', function () {
    var t = $(this).data('tab');
    $('.dt-drawer-tab').removeClass('active'); $(this).addClass('active');
    $('.dt-tabpane').removeClass('active'); $('#pane-' + t).addClass('active');
});

function loadDetail() {
    APP.ajax(URL, {action:'getDetail', id:currentDeTaiId}).done(function (res) {
        if (!res.success) { APP.toast(res.message,'error'); return; }
        var d = res.data, e = d.de_tai;
        $('#dr_title').text(e.ten_de_tai);
        $('#dr_subtitle').html('<strong>' + APP.escape(e.ma_de_tai) + '</strong> &middot; ' + e.nam + ' &middot; ' + APP.escape(e.ten_cap_do || '') + ' &middot; ' + APP.escape(e.ten_the_loai || ''));
        renderInfoPane(e, d.phan_tram);
        renderTVPane(d.thanh_vien);
        renderHDPane(d.hoi_dong || []);
        renderTDPane(d.tien_do);
        renderTLPane(d.tai_lieu);
    });
}

function renderInfoPane(e, pct) {
    var html = '<div class="dt-info-grid">' +
        row('Mã đề tài', '<strong>' + APP.escape(e.ma_de_tai) + '</strong>') +
        row('Năm', e.nam) +
        row('Cấp độ', APP.escape(e.ten_cap_do || '-')) +
        row('Thể loại', APP.escape(e.ten_the_loai || '-')) +
        row('Khoa/Phòng', APP.escape(e.ten_khoa || e.ten_khoa_text || '-')) +
        row('Chủ nhiệm', APP.escape(e.ho_ten_chu_nhiem || '-')) +
        row('Thư ký', APP.escape(e.ho_ten_thu_ky || '-')) +
        row('Trạng thái', '<span class="tt-badge tt-' + e.trang_thai + '">' + TT_NAMES[e.trang_thai] + '</span>') +
        row('Ngày bắt đầu', APP.formatDate(e.ngay_bat_dau)) +
        row('Dự kiến KT', APP.formatDate(e.ngay_ket_thuc_du_kien)) +
        row('Nghiệm thu', APP.formatDate(e.ngay_nghiem_thu)) +
        row('Phiên bảo vệ', APP.escape(e.phien_bao_ve || '-')) +
        row('Địa điểm BV', APP.escape(e.dia_diem_bao_ve || '-')) +
        row('Ngày bảo vệ', APP.formatDate(e.ngay_bao_ve)) +
        row('Số QĐ phê duyệt', APP.escape(e.quyet_dinh_phe_duyet || '-')) +
        row('Ngày QĐ phê duyệt', APP.formatDate(e.ngay_quyet_dinh)) +
        row('QĐ công nhận', APP.escape(e.quyet_dinh_cong_nhan || '-')) +
        row('Ngày QĐ công nhận', APP.formatDate(e.ngay_quyet_dinh_cong_nhan)) +
        row('Kinh phí dự toán', e.kinh_phi_du_toan ? formatCurrency(e.kinh_phi_du_toan) : '-') +
        row('Kinh phí thực tế', e.kinh_phi_thuc_te ? formatCurrency(e.kinh_phi_thuc_te) : '-') +
        row('Nguồn kinh phí', APP.escape(e.nguon_kinh_phi || '-')) +
        row('Xếp loại', e.ket_qua_xep_loai ? '<strong>' + (XL_NAMES[e.ket_qua_xep_loai] || e.ket_qua_xep_loai) + '</strong>' : '-') +
        row('Điểm số', e.diem_so || '-') +
    '</div>';
    html += '<div style="margin-top:14px"><div style="font-size:13px;color:#64748b;margin-bottom:6px">Tiến độ mới nhất: <strong>' + pct + '%</strong></div><div class="dt-progress"><span style="width:' + pct + '%"></span></div></div>';
    if (e.muc_tieu) html += sec('Mục tiêu', APP.escape(e.muc_tieu));
    if (e.tom_tat) html += sec('Tóm tắt', APP.escape(e.tom_tat));
    if (e.tu_khoa) html += sec('Từ khóa', APP.escape(e.tu_khoa));
    if (e.noi_dung_ung_dung) html += sec('Giá trị ứng dụng', APP.escape(e.noi_dung_ung_dung));
    if (e.ten_tap_chi || e.link_bai_bao) {
        html += sec('Bài báo', APP.escape(e.ten_tap_chi || '') + (e.so_tap_chi ? ' - Số ' + APP.escape(e.so_tap_chi) : '') + (e.nam_xuat_ban ? ' (' + e.nam_xuat_ban + ')' : '') + (e.issn_doi ? '<br>ISSN/DOI: ' + APP.escape(e.issn_doi) : '') + (e.link_bai_bao ? '<br><a href="' + APP.escape(e.link_bai_bao) + '" target="_blank">' + APP.escape(e.link_bai_bao) + '</a>' : ''));
    }
    $('#pane-info').html(html);
}
function row(l, v) { return '<div class="row"><div class="lbl">' + l + '</div><div class="val">' + v + '</div></div>'; }
function sec(title, body) { return '<h4 style="color:#2563eb;margin:18px 0 6px;font-size:14px">' + title + '</h4><div style="white-space:pre-wrap;font-size:13px;color:#334155;line-height:1.5">' + body + '</div>'; }
function formatCurrency(v) { return Number(v).toLocaleString('vi-VN') + ' đ'; }

/* ===== TAB THÀNH VIÊN ===== */
function renderTVPane(arr) {
    var html = '';
    if (CAN_EDIT) html += '<button class="btn btn-primary" style="margin-bottom:12px" onclick="openTV()">+ Thêm thành viên</button>';
    if (!arr.length) { html += '<div class="empty-state">Chưa có thành viên</div>'; }
    arr.forEach(function (t) {
        var ten = t.ho_ten_nv || t.ho_ten_ngoai || '(Không tên)';
        var donVi = t.nhan_vien_id ? (t.ten_khoa_phong || t.chuc_danh || '') : (t.don_vi_ngoai || 'Người ngoài');
        var maHienThi = t.ma_nv || t.ma_nv_text || '';
        var btn = '';
        if (CAN_EDIT) btn = '<button class="btn btn-sm" onclick="editTV(' + t.id + ')">' + ICON_EDIT + '</button>' +
                            '<button class="btn btn-sm btn-danger" onclick="delTV(' + t.id + ')">' + ICON_TRASH + '</button>';
        html += '<div class="dt-sub-card"><div class="head"><div><div class="title">' + APP.escape(ten) + (maHienThi ? ' <span class="text-muted">[' + APP.escape(maHienThi) + ']</span>' : '') + '</div><div class="meta">' + APP.escape(donVi) + ' &middot; ' + APP.escape(t.vai_tro) + (t.phan_tram_dong_gop ? ' &middot; ' + t.phan_tram_dong_gop + '%' : '') + '</div>' + (t.ghi_chu ? '<div class="body">' + APP.escape(t.ghi_chu) + '</div>' : '') + '</div><div>' + btn + '</div></div></div>';
    });
    $('#pane-tv').html(html);
}
function openTV() {
    $('#tvTitle').text('Thêm thành viên'); $('#formTV')[0].reset(); $('#tv_id').val(''); $('#tv_dt').val(currentDeTaiId);
    $('#modalTV').addClass('open');
}
function editTV(id) {
    APP.ajax(URL, {action:'tv_getById', id:id}).done(function (res) {
        if (!res.success) return; var t = res.data;
        $('#tvTitle').text('Sửa thành viên'); $('#tv_id').val(t.id); $('#tv_dt').val(t.de_tai_id);
        $('#tv_nv').val(t.nhan_vien_id || ''); $('#tv_hn').val(t.ho_ten_ngoai || ''); $('#tv_dn').val(t.don_vi_ngoai || '');
        $('#tv_vt').val(t.vai_tro); $('#tv_mnv').val(t.ma_nv_text || '');
        $('#tv_pt').val(t.phan_tram_dong_gop || ''); $('#tv_gc').val(t.ghi_chu || '');
        $('#modalTV').addClass('open');
    });
}
function delTV(id) { APP.confirm('Xóa thành viên?', function(){ APP.ajax(URL,{action:'tv_delete', id:id}).done(function(r){ APP.toast(r.message,'success'); loadDetail(); }); }); }
$('#formTV').on('submit', function (e) {
    e.preventDefault();
    var data = $(this).serializeArray();
    data.push({name:'action', value: $('#tv_id').val() ? 'tv_update' : 'tv_insert'});
    APP.ajax(URL, data).done(function (r) {
        if (r.success) { APP.toast(r.message,'success'); $('#modalTV').removeClass('open'); loadDetail(); }
        else APP.toast(r.message,'error');
    });
});

/* ===== TAB HỘI ĐỒNG ===== */
function renderHDPane(arr) {
    var html = '';
    if (CAN_EDIT) html += '<button class="btn btn-primary" style="margin-bottom:12px" onclick="openHD()">+ Thêm thành viên hội đồng</button>';
    if (!arr.length) { html += '<div class="empty-state">Chưa có hội đồng</div>'; $('#pane-hd').html(html); return; }
    arr.forEach(function (h) {
        var ten = (h.chuc_danh_hoc_vi ? h.chuc_danh_hoc_vi + ' ' : '') + h.ho_ten;
        var donVi = h.ten_khoa_phong || h.ten_khoa_text || '';
        var btn = '';
        if (CAN_EDIT) btn = '<button class="btn btn-sm" onclick="editHD(' + h.id + ')">' + ICON_EDIT + '</button>' +
                            '<button class="btn btn-sm btn-danger" onclick="delHD(' + h.id + ')">' + ICON_TRASH + '</button>';
        html += '<div class="dt-sub-card"><div class="head"><div>' +
                '<div class="title">' + APP.escape(ten) + '</div>' +
                '<div class="meta"><span class="badge">' + (HD_VAITRO[h.vai_tro_hd] || h.vai_tro_hd) + '</span>' +
                (donVi ? ' &middot; ' + APP.escape(donVi) : '') + '</div>' +
                (h.ghi_chu ? '<div class="body">' + APP.escape(h.ghi_chu) + '</div>' : '') +
                '</div><div>' + btn + '</div></div></div>';
    });
    $('#pane-hd').html(html);
}
function openHD() {
    $('#hdTitle').text('Thêm thành viên hội đồng'); $('#formHD')[0].reset(); $('#hd_id').val(''); $('#hd_dt').val(currentDeTaiId);
    $('#hd_vt').val('ThanhVien');
    $('#modalHD').addClass('open');
}
function editHD(id) {
    APP.ajax(URL, {action:'hd_getById', id:id}).done(function (res) {
        if (!res.success) return; var h = res.data;
        $('#hdTitle').text('Sửa thành viên hội đồng'); $('#hd_id').val(h.id); $('#hd_dt').val(h.de_tai_id);
        $('#hd_cd').val(h.chuc_danh_hoc_vi || ''); $('#hd_ht').val(h.ho_ten);
        $('#hd_vt').val(h.vai_tro_hd); $('#hd_tu').val(h.thu_tu || 0);
        $('#hd_kp').val(h.khoa_phong_id || ''); $('#hd_tkt').val(h.ten_khoa_text || '');
        $('#hd_nv').val(h.nhan_vien_id || ''); $('#hd_gc').val(h.ghi_chu || '');
        $('#modalHD').addClass('open');
    });
}
function delHD(id) { APP.confirm('Xóa thành viên hội đồng?', function(){ APP.ajax(URL,{action:'hd_delete', id:id}).done(function(r){ APP.toast(r.message,'success'); loadDetail(); }); }); }
$('#formHD').on('submit', function (e) {
    e.preventDefault();
    var data = $(this).serializeArray();
    data.push({name:'action', value: $('#hd_id').val() ? 'hd_update' : 'hd_insert'});
    APP.ajax(URL, data).done(function (r) {
        if (r.success) { APP.toast(r.message,'success'); $('#modalHD').removeClass('open'); loadDetail(); }
        else APP.toast(r.message,'error');
    });
});

/* ===== TAB TIẾN ĐỘ ===== */
function renderTDPane(arr) {
    var html = '';
    if (CAN_EDIT) html += '<button class="btn btn-primary" style="margin-bottom:12px" onclick="openTD()">+ Thêm báo cáo tiến độ</button>';
    if (!arr.length) { html += '<div class="empty-state">Chưa có báo cáo tiến độ</div>'; $('#pane-td').html(html); return; }
    arr.forEach(function (t) {
        var btn = '';
        if (CAN_EDIT) btn = '<button class="btn btn-sm" onclick="editTD(' + t.id + ')">' + ICON_EDIT + '</button>' +
                            '<button class="btn btn-sm btn-danger" onclick="delTD(' + t.id + ')">' + ICON_TRASH + '</button>';
        html += '<div class="dt-sub-card">' +
                '<div class="head"><div><div class="title">' + APP.escape(t.ky_bao_cao) + ' &middot; <span class="text-muted">' + APP.formatDate(t.ngay_bao_cao) + '</span></div>' +
                '<div class="meta">' + APP.escape(t.ho_ten_nguoi_bao_cao || '-') + '</div>' +
                '<div style="margin-top:8px"><div class="dt-progress" style="height:8px"><span style="width:' + t.phan_tram_hoan_thanh + '%"></span></div><div class="text-muted" style="font-size:11px;margin-top:3px">' + t.phan_tram_hoan_thanh + '% hoàn thành</div></div>' +
                '</div><div>' + btn + '</div></div>' +
                (t.cong_viec_da_lam ? '<div class="body"><strong>Đã làm:</strong> ' + APP.escape(t.cong_viec_da_lam) + '</div>' : '') +
                (t.cong_viec_tiep_theo ? '<div class="body"><strong>Tiếp theo:</strong> ' + APP.escape(t.cong_viec_tiep_theo) + '</div>' : '') +
                (t.kho_khan_vuong_mac ? '<div class="body" style="color:#b91c1c"><strong>Khó khăn:</strong> ' + APP.escape(t.kho_khan_vuong_mac) + '</div>' : '') +
                '</div>';
    });
    $('#pane-td').html(html);
}
function openTD() {
    $('#tdTitle').text('Thêm báo cáo tiến độ'); $('#formTD')[0].reset(); $('#td_id').val('');
    $('#td_dt').val(currentDeTaiId); $('#td_ng').val(new Date().toISOString().slice(0,10)); $('#td_pt').val(0);
    $('#modalTD').addClass('open');
}
function editTD(id) {
    APP.ajax(URL, {action:'td_getById', id:id}).done(function (res) {
        if (!res.success) return; var t = res.data;
        $('#tdTitle').text('Sửa báo cáo tiến độ'); $('#td_id').val(t.id); $('#td_dt').val(t.de_tai_id);
        $('#td_ky').val(t.ky_bao_cao); $('#td_ng').val(t.ngay_bao_cao); $('#td_pt').val(t.phan_tram_hoan_thanh);
        $('#td_nbc').val(t.nguoi_bao_cao_id || '');
        $('#td_cl').val(t.cong_viec_da_lam || ''); $('#td_ct').val(t.cong_viec_tiep_theo || ''); $('#td_kk').val(t.kho_khan_vuong_mac || '');
        $('#modalTD').addClass('open');
    });
}
function delTD(id) { APP.confirm('Xóa báo cáo tiến độ?', function(){ APP.ajax(URL,{action:'td_delete', id:id}).done(function(r){ APP.toast(r.message,'success'); loadDetail(); }); }); }
$('#formTD').on('submit', function (e) {
    e.preventDefault();
    var data = $(this).serializeArray();
    data.push({name:'action', value: $('#td_id').val() ? 'td_update' : 'td_insert'});
    APP.ajax(URL, data).done(function (r) {
        if (r.success) { APP.toast(r.message,'success'); $('#modalTD').removeClass('open'); loadDetail(); }
        else APP.toast(r.message,'error');
    });
});

/* ===== TAB TÀI LIỆU ===== */
function renderTLPane(arr) {
    var html = '';
    if (CAN_EDIT) html += '<button class="btn btn-primary" style="margin-bottom:12px" onclick="openTL()">+ Tải tài liệu lên</button>';
    if (!arr.length) { html += '<div class="empty-state">Chưa có tài liệu</div>'; $('#pane-tl').html(html); return; }
    arr.forEach(function (t) {
        var size = t.kich_thuoc ? (t.kich_thuoc/1024/1024).toFixed(2) + ' MB' : '';
        var btn = '<a class="btn btn-sm" target="_blank" href="' + DOWNLOAD_URL + '?id=' + t.id + '&inline=1">Xem</a>' +
                  '<a class="btn btn-sm" href="' + DOWNLOAD_URL + '?id=' + t.id + '">Tải</a>';
        if (CAN_EDIT) btn += '<button class="btn btn-sm btn-danger" onclick="delTL(' + t.id + ')">' + ICON_TRASH + '</button>';
        html += '<div class="dt-sub-card"><div class="head"><div><div class="title">' + APP.escape(t.ten_tai_lieu) + '</div>' +
                '<div class="meta"><span class="badge">' + (LOAI_TL_NAMES[t.loai_tai_lieu] || t.loai_tai_lieu) + '</span> ' +
                APP.escape(t.ten_file_goc || '') + (size ? ' &middot; ' + size : '') + ' &middot; ' + APP.formatDate(t.ngay_tao) + '</div>' +
                (t.mo_ta ? '<div class="body">' + APP.escape(t.mo_ta) + '</div>' : '') +
                '</div><div>' + btn + '</div></div></div>';
    });
    $('#pane-tl').html(html);
}
function openTL() {
    $('#formTL')[0].reset(); $('#tl_dt').val(currentDeTaiId);
    $('#modalTL').addClass('open');
}
function delTL(id) { APP.confirm('Xóa tài liệu?', function(){ APP.ajax(URL,{action:'tl_delete', id:id}).done(function(r){ APP.toast(r.message,'success'); loadDetail(); }); }); }
$('#formTL').on('submit', function (e) {
    e.preventDefault();
    var fd = new FormData(this); fd.append('action', 'tl_upload');
    APP.showLoading('#modalTL .modal-body');
    $.ajax({url:URL, data:fd, processData:false, contentType:false, type:'POST', dataType:'json', headers: window.CSRF_TOKEN ? {'X-CSRF-Token': window.CSRF_TOKEN} : {}})
        .done(function (r) {
            APP.hideLoading('#modalTL .modal-body');
            if (r.success) { APP.toast(r.message,'success'); $('#modalTL').removeClass('open'); loadDetail(); }
            else APP.toast(r.message,'error');
        })
        .fail(function () { APP.hideLoading('#modalTL .modal-body'); APP.toast('Upload thất bại','error'); });
});

/* ===== INIT ===== */
fillYears();
loadCombos().done(function () { load(); });
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
