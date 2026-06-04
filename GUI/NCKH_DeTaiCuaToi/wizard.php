<?php
require_once __DIR__ . '/../../bootstrap.php';

Helper::requireLogin();
if (!PhanQuyenHelper::hasQuyen('NCKH_DeTaiCuaToi', PhanQuyenHelper::QUYEN_XEM)) {
    echo 'Bạn không có quyền truy cập chức năng này.'; exit;
}

$dtId = (int)($_GET['id'] ?? 0);
$viewOnly = !empty($_GET['view']);

$pageTitle = $dtId ? ($viewOnly ? 'Xem đề tài' : 'Soạn đề tài') : 'Tạo đề tài mới';
$activeMenu = 'NCKH_DeTaiCuaToi';
require __DIR__ . '/../layouts/header.php';
?>
<style>
/* Stepper */
.wz-stepper { display:flex; gap:0; margin:0 0 24px; padding:0; list-style:none; counter-reset:step; }
.wz-step { flex:1; position:relative; display:flex; flex-direction:column; align-items:center; gap:6px; cursor:pointer; }
.wz-step:not(:last-child)::after {
    content:''; position:absolute; top:18px; left:50%; right:-50%; height:2px; background:#e2e8f0; z-index:0;
}
.wz-step.done::after { background:#22c55e; }
.wz-step .num { width:36px; height:36px; border-radius:50%; background:#f1f5f9; color:#64748b; display:flex; align-items:center; justify-content:center; font-weight:700; position:relative; z-index:1; transition:all .2s; }
.wz-step.active .num { background:#2563eb; color:#fff; box-shadow:0 0 0 4px #dbeafe; }
.wz-step.done .num { background:#22c55e; color:#fff; }
.wz-step .lbl { font-size:12px; color:#64748b; font-weight:500; text-align:center; }
.wz-step.active .lbl { color:#2563eb; font-weight:600; }
.wz-step.done .lbl { color:#16a34a; }

/* Step container */
.wz-pane { display:none; }
.wz-pane.active { display:block; }
.wz-section-title { font-size:18px; font-weight:600; color:#0f172a; margin:0 0 4px; }
.wz-section-desc { font-size:13px; color:#64748b; margin:0 0 18px; }

/* Sticky footer */
.wz-footer { position:sticky; bottom:0; background:#fff; border-top:1px solid #e2e8f0; padding:14px 0; margin:24px -20px -20px; padding-left:20px; padding-right:20px; display:flex; justify-content:space-between; gap:8px; z-index:10; }
.wz-footer .left, .wz-footer .right { display:flex; gap:8px; align-items:center; }

/* Sub-card thành viên / hội đồng / tài liệu */
.wz-sub-list { display:flex; flex-direction:column; gap:10px; margin-top:14px; }
.wz-sub-item { background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:12px 14px; display:flex; justify-content:space-between; gap:10px; align-items:center; }
.wz-sub-item .info { flex:1; min-width:0; }
.wz-sub-item .info .name { font-weight:600; color:#0f172a; }
.wz-sub-item .info .sub { font-size:12px; color:#64748b; margin-top:2px; }
.wz-sub-item .actions { display:flex; gap:6px; flex-shrink:0; }

.wz-add-btn { width:100%; padding:14px; background:#fff; border:2px dashed #cbd5e1; border-radius:8px; color:#475569; font-weight:500; cursor:pointer; transition:all .2s; }
.wz-add-btn:hover { border-color:#2563eb; color:#2563eb; background:#eff6ff; }

/* Banner trạng thái */
.wz-banner { padding:12px 16px; border-radius:8px; margin-bottom:16px; display:flex; gap:10px; align-items:flex-start; font-size:13px; }
.wz-banner svg { flex:0 0 auto; margin-top:1px; }
.wz-banner.warn { background:#fef3c7; color:#92400e; }
.wz-banner.error { background:#fee2e2; color:#991b1b; }
.wz-banner.info { background:#dbeafe; color:#1e40af; }
.wz-banner.success { background:#dcfce7; color:#166534; }

/* Auto-save indicator */
.wz-autosave { font-size:12px; color:#64748b; display:flex; align-items:center; gap:5px; }
.wz-autosave.saving { color:#2563eb; }
.wz-autosave.saved { color:#16a34a; }

@media (max-width: 768px) {
    .wz-step .lbl { display:none; }
    .wz-footer { flex-direction:column-reverse; align-items:stretch; }
    .wz-footer .left, .wz-footer .right { justify-content:space-between; }
}
</style>

<div class="breadcrumb">
    <a href="<?= AppConfig::baseUrl('GUI/NCKH_DeTaiCuaToi/index.php') ?>">Đề tài của tôi</a>
    <span class="sep">›</span> <span><?= Helper::h($pageTitle) ?></span>
</div>

<div class="card" style="padding:24px 20px">
    <div id="banner-area"></div>

    <ul class="wz-stepper" id="stepper">
        <li class="wz-step active" data-step="1"><div class="num">1</div><div class="lbl">Thông tin chính</div></li>
        <li class="wz-step" data-step="2"><div class="num">2</div><div class="lbl">Nhóm nghiên cứu</div></li>
        <li class="wz-step" data-step="3"><div class="num">3</div><div class="lbl">Hội đồng</div></li>
        <li class="wz-step" data-step="4"><div class="num">4</div><div class="lbl">Tài liệu</div></li>
    </ul>

    <!-- ======= STEP 1: THÔNG TIN ======= -->
    <div class="wz-pane active" id="pane-1">
        <h3 class="wz-section-title">Thông tin chính</h3>
        <p class="wz-section-desc">Các trường có dấu * là bắt buộc.</p>

        <form id="formInfo" autocomplete="off">
            <input type="hidden" name="id" id="f_id" value="<?= $dtId ?>">

            <div class="form-row">
                <div class="form-group">
                    <label>Mã đề tài</label>
                    <input type="text" name="ma_de_tai" id="f_ma" class="form-control" maxlength="50" placeholder="Để trống = tự sinh DT-{năm}-NNN">
                </div>
                <div class="form-group">
                    <label>Năm <span class="required">*</span></label>
                    <input type="number" name="nam" id="f_nam" class="form-control" required min="2000" max="2100" value="<?= date('Y') ?>">
                </div>
            </div>

            <div class="form-group">
                <label>Đợt đăng ký <span class="required">*</span></label>
                <select name="dot_dang_ky_id" id="f_dot" class="form-select" required>
                    <option value="">- Chọn đợt đang mở -</option>
                </select>
                <div id="f_dot_hint" class="text-muted" style="font-size:12px;margin-top:4px"></div>
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
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Khoa/Phòng</label>
                    <select name="khoa_phong_id" id="f_khoa" class="form-select"><option value="">- Chọn từ danh mục -</option></select>
                </div>
                <div class="form-group">
                    <label>Hoặc nhập tên khoa</label>
                    <input type="text" name="ten_khoa_text" id="f_tkt" class="form-control" maxlength="255">
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
                <input type="text" name="tu_khoa" id="f_tukhoa" class="form-control" maxlength="255" placeholder="Cách nhau bằng dấu phẩy">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Ngày bắt đầu</label>
                    <input type="date" name="ngay_bat_dau" id="f_nbd" class="form-control">
                </div>
                <div class="form-group">
                    <label>Dự kiến kết thúc</label>
                    <input type="date" name="ngay_ket_thuc_du_kien" id="f_nkt" class="form-control">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Kinh phí dự toán (VNĐ)</label>
                    <input type="number" step="1000" name="kinh_phi_du_toan" id="f_kpd" class="form-control">
                </div>
                <div class="form-group">
                    <label>Nguồn kinh phí</label>
                    <input type="text" name="nguon_kinh_phi" id="f_nkp" class="form-control" maxlength="150">
                </div>
            </div>

            <div id="bbao_section" style="display:none">
                <h4 style="color:#2563eb;margin:18px 0 10px;font-size:14px">Thông tin bài báo</h4>
                <div class="form-row">
                    <div class="form-group"><label>Tên tạp chí</label><input type="text" name="ten_tap_chi" id="f_ttc" class="form-control" maxlength="255"></div>
                    <div class="form-group"><label>Số tạp chí</label><input type="text" name="so_tap_chi" id="f_stc" class="form-control" maxlength="50"></div>
                    <div class="form-group"><label>Năm xuất bản</label><input type="number" name="nam_xuat_ban" id="f_nxb" class="form-control" min="1900" max="2100"></div>
                </div>
                <div class="form-row">
                    <div class="form-group"><label>ISSN / DOI</label><input type="text" name="issn_doi" id="f_issn" class="form-control" maxlength="100"></div>
                    <div class="form-group"><label>Link bài báo</label><input type="url" name="link_bai_bao" id="f_lnk" class="form-control" maxlength="500"></div>
                </div>
            </div>
        </form>
    </div>

    <!-- ======= STEP 2: NHÓM NGHIÊN CỨU ======= -->
    <div class="wz-pane" id="pane-2">
        <h3 class="wz-section-title">Nhóm nghiên cứu</h3>
        <p class="wz-section-desc">Liệt kê thành viên thực hiện đề tài. Tối thiểu nên có Chủ nhiệm và Thư ký.</p>
        <div id="needSaveTV" class="wz-banner warn" style="display:none"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/></svg> Vui lòng lưu thông tin chính (Bước 1) trước khi thêm thành viên.</div>
        <div id="tvList" class="wz-sub-list"></div>
        <button type="button" class="wz-add-btn" onclick="openTV()" style="margin-top:14px">+ Thêm thành viên</button>
    </div>

    <!-- ======= STEP 3: HỘI ĐỒNG ======= -->
    <div class="wz-pane" id="pane-3">
        <h3 class="wz-section-title">Hội đồng thẩm định</h3>
        <p class="wz-section-desc">Hội đồng có 5 vai trò: Chủ tịch, Thư ký, Phản biện 1, Phản biện 2, Thành viên.</p>
        <div id="needSaveHD" class="wz-banner warn" style="display:none"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/></svg> Vui lòng lưu thông tin chính (Bước 1) trước.</div>
        <div id="hdList" class="wz-sub-list"></div>
        <button type="button" class="wz-add-btn" onclick="openHD()" style="margin-top:14px">+ Thêm thành viên hội đồng</button>
    </div>

    <!-- ======= STEP 4: TÀI LIỆU ======= -->
    <div class="wz-pane" id="pane-4">
        <h3 class="wz-section-title">Tài liệu đính kèm</h3>
        <p class="wz-section-desc">Upload đề cương, file gốc, biên bản, ... Tối đa 20MB / file. Cho phép pdf, doc(x), xls(x), ppt(x), jpg, png, zip, rar, 7z.</p>
        <div id="needSaveTL" class="wz-banner warn" style="display:none"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/></svg> Vui lòng lưu thông tin chính (Bước 1) trước.</div>
        <div id="tlList" class="wz-sub-list"></div>
        <button type="button" class="wz-add-btn" onclick="openTL()" style="margin-top:14px">+ Tải tài liệu lên</button>
    </div>

    <!-- ======= FOOTER NAV ======= -->
    <div class="wz-footer">
        <div class="left">
            <button type="button" class="btn" id="btn-prev" disabled>← Quay lại</button>
            <span class="wz-autosave" id="autosave"></span>
        </div>
        <div class="right">
            <button type="button" class="btn" id="btn-save">Lưu nháp</button>
            <button type="button" class="btn btn-primary" id="btn-next">Tiếp tục →</button>
            <button type="button" class="btn btn-success" id="btn-submit" style="display:none">Gửi cho duyệt</button>
        </div>
    </div>
</div>

<!-- ======= MODAL THÀNH VIÊN ======= -->
<div class="modal-backdrop" id="modalTV">
    <div class="modal" style="max-width:600px">
        <div class="modal-header"><h3 id="tvTitle">Thêm thành viên</h3><button type="button" class="close" onclick="$('#modalTV').removeClass('open')">&times;</button></div>
        <form id="formTV">
            <div class="modal-body">
                <input type="hidden" name="id" id="tv_id">
                <div class="form-group">
                    <label>Nhân viên (nếu là người trong bệnh viện)</label>
                    <select name="nhan_vien_id" id="tv_nv" class="form-select"><option value="">- Để trống nếu là người ngoài -</option></select>
                </div>
                <div class="form-row">
                    <div class="form-group"><label>Họ tên (người ngoài)</label><input type="text" name="ho_ten_ngoai" id="tv_hn" class="form-control"></div>
                    <div class="form-group"><label>Đơn vị (người ngoài)</label><input type="text" name="don_vi_ngoai" id="tv_dn" class="form-control"></div>
                </div>
                <div class="form-row">
                    <div class="form-group"><label>Vai trò</label><input type="text" name="vai_tro" id="tv_vt" class="form-control" value="Thành viên"></div>
                    <div class="form-group"><label>Mã NV (text)</label><input type="text" name="ma_nv_text" id="tv_mnv" class="form-control"></div>
                    <div class="form-group"><label>% đóng góp</label><input type="number" step="0.01" min="0" max="100" name="phan_tram_dong_gop" id="tv_pt" class="form-control"></div>
                </div>
                <div class="form-group"><label>Ghi chú</label><textarea name="ghi_chu" id="tv_gc" class="form-control" rows="2"></textarea></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn" onclick="$('#modalTV').removeClass('open')">Hủy</button>
                <button type="submit" class="btn btn-primary">Lưu</button>
            </div>
        </form>
    </div>
</div>

<!-- ======= MODAL HỘI ĐỒNG ======= -->
<div class="modal-backdrop" id="modalHD">
    <div class="modal" style="max-width:620px">
        <div class="modal-header"><h3 id="hdTitle">Thêm thành viên hội đồng</h3><button type="button" class="close" onclick="$('#modalHD').removeClass('open')">&times;</button></div>
        <form id="formHD">
            <div class="modal-body">
                <input type="hidden" name="id" id="hd_id">
                <div class="form-row">
                    <div class="form-group"><label>Học vị / Chức danh</label><input type="text" name="chuc_danh_hoc_vi" id="hd_cd" class="form-control" placeholder="VD: BSCKII., ThS., TS."></div>
                    <div class="form-group"><label>Họ tên <span class="required">*</span></label><input type="text" name="ho_ten" id="hd_ht" class="form-control" required></div>
                </div>
                <div class="form-row">
                    <div class="form-group"><label>Vai trò trong hội đồng <span class="required">*</span></label>
                        <select name="vai_tro_hd" id="hd_vt" class="form-select" required>
                            <option value="ChuTich">Chủ tịch</option>
                            <option value="ThuKy">Thư ký</option>
                            <option value="PhanBien1">Phản biện 1</option>
                            <option value="PhanBien2">Phản biện 2</option>
                            <option value="ThanhVien" selected>Thành viên</option>
                        </select>
                    </div>
                    <div class="form-group"><label>Thứ tự</label><input type="number" name="thu_tu" id="hd_tu" class="form-control" value="0"></div>
                </div>
                <div class="form-group"><label>Khoa/Phòng (nếu có DM)</label>
                    <select name="khoa_phong_id" id="hd_kp" class="form-select"><option value="">-</option></select>
                </div>
                <div class="form-group"><label>Tên khoa (text gốc)</label><input type="text" name="ten_khoa_text" id="hd_tkt" class="form-control"></div>
                <div class="form-group"><label>Liên kết nhân viên (nếu có)</label>
                    <select name="nhan_vien_id" id="hd_nv" class="form-select"><option value="">-</option></select>
                </div>
                <div class="form-group"><label>Ghi chú</label><textarea name="ghi_chu" id="hd_gc" class="form-control" rows="2"></textarea></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn" onclick="$('#modalHD').removeClass('open')">Hủy</button>
                <button type="submit" class="btn btn-primary">Lưu</button>
            </div>
        </form>
    </div>
</div>

<!-- ======= MODAL TÀI LIỆU ======= -->
<div class="modal-backdrop" id="modalTL">
    <div class="modal" style="max-width:600px">
        <div class="modal-header"><h3>Tải tài liệu lên</h3><button type="button" class="close" onclick="$('#modalTL').removeClass('open')">&times;</button></div>
        <form id="formTL" enctype="multipart/form-data">
            <div class="modal-body">
                <div class="form-row">
                    <div class="form-group"><label>Loại tài liệu</label>
                        <select name="loai_tai_lieu" id="tl_loai" class="form-select">
                            <option value="DeCuong">Đề cương</option>
                            <option value="QuyetDinh">Quyết định</option>
                            <option value="BienBan">Biên bản</option>
                            <option value="BaoCao">Báo cáo</option>
                            <option value="FileGoc">File gốc</option>
                            <option value="Khac" selected>Khác</option>
                        </select>
                    </div>
                    <div class="form-group"><label>Tên hiển thị</label><input type="text" name="ten_tai_lieu" id="tl_ten" class="form-control" placeholder="Để trống = lấy theo tên file"></div>
                </div>
                <div class="form-group"><label>File <span class="required">*</span></label>
                    <input type="file" name="file" id="tl_file" class="form-control" required>
                    <div class="text-muted" style="font-size:12px;margin-top:4px">Tối đa 20MB.</div>
                </div>
                <div class="form-group"><label>Mô tả</label><textarea name="mo_ta" id="tl_mt" class="form-control" rows="2"></textarea></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn" onclick="$('#modalTL').removeClass('open')">Hủy</button>
                <button type="submit" class="btn btn-primary">Tải lên</button>
            </div>
        </form>
    </div>
</div>

<script>
var URL = APP_BASE + 'GUI/NCKH_DeTaiCuaToi/ajax_handler.php';
var DOWNLOAD_URL = APP_BASE + 'GUI/NCKH_DeTai/download.php';
var IS_VIEW = <?= $viewOnly ? 'true' : 'false' ?>;
var INITIAL_ID = <?= (int)$dtId ?>;
var ICON_EDIT = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>';
var ICON_TRASH = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>';
var HD_VAITRO = {ChuTich:'Chủ tịch', ThuKy:'Thư ký', PhanBien1:'Phản biện 1', PhanBien2:'Phản biện 2', ThanhVien:'Thành viên'};
var LOAI_TL = {DeCuong:'Đề cương', QuyetDinh:'Quyết định', BienBan:'Biên bản', BaoCao:'Báo cáo', FileGoc:'File gốc', Khac:'Khác'};

var current = { step: 1, id: INITIAL_ID, ttd: 'Nhap', lyDo: null };
var COMBO = { khoaPhong: [], nhanVien: [] };

/* ===== INIT ===== */
function loadCombos() {
    return $.when(
        APP.ajax(URL,{action:'getComboCapDo'}),
        APP.ajax(URL,{action:'getComboTheLoai'}),
        APP.ajax(URL,{action:'getComboKhoaPhong'}),
        APP.ajax(URL,{action:'getComboNhanVien', kw:''}),
        APP.ajax(URL,{action:'getComboDot'})
    ).done(function (cd, tl, kp, nv, dot) {
        $.each(cd[0].data || [], function (_, x) { $('#f_capdo').append('<option value="' + x.id + '">' + APP.escape(x.ten_cap_do) + '</option>'); });
        $.each(tl[0].data || [], function (_, x) { $('#f_theloai').append('<option value="' + x.id + '" data-ma="' + x.ma_the_loai + '">' + APP.escape(x.ten_the_loai) + '</option>'); });
        COMBO.khoaPhong = kp[0].data || [];
        COMBO.nhanVien = nv[0].data || [];
        $.each(COMBO.khoaPhong, function (_, x) {
            var t = APP.escape(x.ten_khoa);
            $('#f_khoa').append('<option value="' + x.id + '">' + t + '</option>');
            $('#hd_kp').append('<option value="' + x.id + '">' + t + '</option>');
        });
        $.each(COMBO.nhanVien, function (_, x) {
            var t = APP.escape((x.ma_nv ? '[' + x.ma_nv + '] ' : '') + x.ho_ten);
            $('#f_cn').append('<option value="' + x.id + '">' + t + '</option>');
            $('#f_tk').append('<option value="' + x.id + '">' + t + '</option>');
            $('#tv_nv').append('<option value="' + x.id + '">' + t + '</option>');
            $('#hd_nv').append('<option value="' + x.id + '">' + t + '</option>');
        });
        var dots = dot[0].data || [];
        if (!dots.length) {
            $('#f_dot_hint').html('<span style="color:#dc2626">Hiện không có đợt đăng ký nào đang mở. Vui lòng liên hệ quản trị viên.</span>');
        } else {
            $.each(dots, function (_, x) {
                var label = x.ten_dot + ' (' + x.tu_ngay + ' → ' + x.den_ngay + ')';
                $('#f_dot').append('<option value="' + x.id + '">' + APP.escape(label) + '</option>');
            });
        }
    });
}

function loadDetail() {
    if (!current.id) return $.Deferred().resolve().promise();
    return APP.ajax(URL, {action:'getDetail', id:current.id}).done(function (res) {
        if (!res.success) { APP.toast(res.message,'error'); return; }
        var d = res.data, e = d.de_tai;
        current.ttd = e.trang_thai_duyet || 'Nhap';
        current.lyDo = e.ly_do_tu_choi;
        $('#f_ma').val(e.ma_de_tai); $('#f_nam').val(e.nam); $('#f_ten').val(e.ten_de_tai);
        $('#f_capdo').val(e.cap_do_id); $('#f_theloai').val(e.the_loai_id);
        if (e.dot_dang_ky_id) {
            // Đảm bảo option tồn tại (đợt cũ có thể không còn trong combo active)
            if (!$('#f_dot option[value="' + e.dot_dang_ky_id + '"]').length) {
                $('#f_dot').append('<option value="' + e.dot_dang_ky_id + '">' + APP.escape(e.ten_dot || ('Đợt #' + e.dot_dang_ky_id)) + '</option>');
            }
            $('#f_dot').val(e.dot_dang_ky_id);
        }
        $('#f_khoa').val(e.khoa_phong_id || ''); $('#f_tkt').val(e.ten_khoa_text || '');
        $('#f_cn').val(e.chu_nhiem_id); $('#f_tk').val(e.thu_ky_id || '');
        $('#f_muctieu').val(e.muc_tieu || ''); $('#f_tomtat').val(e.tom_tat || ''); $('#f_tukhoa').val(e.tu_khoa || '');
        $('#f_nbd').val(e.ngay_bat_dau || ''); $('#f_nkt').val(e.ngay_ket_thuc_du_kien || '');
        $('#f_kpd').val(e.kinh_phi_du_toan || ''); $('#f_nkp').val(e.nguon_kinh_phi || '');
        $('#f_ttc').val(e.ten_tap_chi || ''); $('#f_stc').val(e.so_tap_chi || ''); $('#f_nxb').val(e.nam_xuat_ban || '');
        $('#f_issn').val(e.issn_doi || ''); $('#f_lnk').val(e.link_bai_bao || '');
        toggleBaiBao();
        renderTV(d.thanh_vien || []);
        renderHD(d.hoi_dong || []);
        renderTL(d.tai_lieu || []);
        renderBanner();
        applyViewMode();
    });
}

function renderBanner() {
    var html = '';
    if (current.ttd === 'TuChoi' && current.lyDo) {
        html = '<div class="wz-banner error"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg><div><strong>Đã bị từ chối — </strong>' + APP.escape(current.lyDo) + '<div style="margin-top:4px;opacity:.85">Bạn có thể chỉnh sửa và gửi lại.</div></div></div>';
    } else if (current.ttd === 'ChoDuyet') {
        html = '<div class="wz-banner info"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>Đề tài đã gửi cho quản trị viên — đang chờ xử lý. Bạn không thể chỉnh sửa cho đến khi có phản hồi.</div>';
    } else if (current.ttd === 'DaDuyet') {
        html = '<div class="wz-banner success"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>Đề tài đã được duyệt — chỉ xem.</div>';
    }
    $('#banner-area').html(html);
}

function applyViewMode() {
    var disabled = IS_VIEW || current.ttd === 'ChoDuyet' || current.ttd === 'DaDuyet';
    $('#formInfo input, #formInfo select, #formInfo textarea').prop('disabled', disabled);
    $('#btn-save').prop('disabled', disabled);
    $('#btn-submit').toggle(!disabled && current.id > 0);
    $('.wz-add-btn').toggle(!disabled);
    if (disabled) $('.wz-sub-item .actions').hide();
}

/* ===== STEPPER ===== */
function goStep(n) {
    if (n < 1 || n > 4) return;
    if (n > 1 && !current.id) { APP.toast('Vui lòng lưu thông tin chính trước', 'error'); return; }
    current.step = n;
    $('.wz-step').removeClass('active done');
    for (var i = 1; i <= 4; i++) {
        if (i < n) $('.wz-step[data-step="' + i + '"]').addClass('done');
        if (i === n) $('.wz-step[data-step="' + i + '"]').addClass('active');
    }
    $('.wz-pane').removeClass('active');
    $('#pane-' + n).addClass('active');
    $('#btn-prev').prop('disabled', n === 1);
    $('#btn-next').toggle(n < 4);
    var canSubmit = n === 4 && current.id > 0 && (current.ttd === 'Nhap' || current.ttd === 'TuChoi') && !IS_VIEW;
    $('#btn-submit').toggle(canSubmit);

    // Hide need-save banners khi đã có id
    if (current.id) { $('#needSaveTV, #needSaveHD, #needSaveTL').hide(); }
    else { $('#needSave' + (n===2?'TV':n===3?'HD':n===4?'TL':'X')).show(); }
}

$('#stepper').on('click', '.wz-step', function () { goStep(parseInt($(this).data('step'),10)); });
$('#btn-prev').on('click', function () { goStep(current.step - 1); });
$('#btn-next').on('click', function () {
    if (current.step === 1) { saveInfo(true); }
    else { goStep(current.step + 1); }
});

/* ===== STEP 1: SAVE INFO ===== */
function setAutosave(state) {
    var $el = $('#autosave');
    if (state === 'saving') $el.attr('class','wz-autosave saving').text('Đang lưu...');
    else if (state === 'saved') $el.attr('class','wz-autosave saved').text('✓ Đã lưu');
    else $el.attr('class','wz-autosave').text('');
}

function saveInfo(thenNext) {
    var data = $('#formInfo').serializeArray();
    if (!current.id) data.push({name:'action', value:'createDraft'});
    else { data.push({name:'action', value:'updateDraft'}); }
    setAutosave('saving');
    APP.ajax(URL, data).done(function (r) {
        if (!r.success) { setAutosave(''); APP.toast(r.message, 'error'); return; }
        setAutosave('saved');
        APP.toast(r.message, 'success');
        if (!current.id && r.data && r.data.id) {
            current.id = r.data.id;
            $('#f_id').val(r.data.id);
            // Update URL without reload
            try { history.replaceState(null, '', '?id=' + r.data.id); } catch (_) {}
        }
        if (thenNext) goStep(2);
        // Refresh detail (nhất là nếu vừa create để có thông tin)
        loadDetail();
    });
}
$('#btn-save').on('click', function () { saveInfo(false); });

$('#f_theloai').on('change', toggleBaiBao);
function toggleBaiBao() {
    var ma = $('#f_theloai option:selected').data('ma');
    $('#bbao_section').toggle(ma === 'BAIBAO');
}

/* ===== STEP 2: TV ===== */
function renderTV(arr) {
    var $list = $('#tvList').empty();
    if (!arr.length) { $list.html('<div class="text-muted" style="padding:14px 0;font-size:13px">Chưa có thành viên nào.</div>'); return; }
    arr.forEach(function (t) {
        var ten = t.ho_ten_nv || t.ho_ten_ngoai || '(Không tên)';
        var ma = t.ma_nv || t.ma_nv_text || '';
        var donVi = t.nhan_vien_id ? (t.ten_khoa_phong || t.chuc_danh || '') : (t.don_vi_ngoai || 'Người ngoài');
        $list.append(
            '<div class="wz-sub-item"><div class="info">' +
                '<div class="name">' + APP.escape(ten) + (ma ? ' <span class="text-muted">[' + APP.escape(ma) + ']</span>' : '') + '</div>' +
                '<div class="sub">' + APP.escape(donVi) + ' · ' + APP.escape(t.vai_tro) + (t.phan_tram_dong_gop ? ' · ' + t.phan_tram_dong_gop + '%' : '') + '</div>' +
            '</div><div class="actions">' +
                '<button class="btn btn-sm" onclick="editTV(' + t.id + ')">' + ICON_EDIT + '</button>' +
                '<button class="btn btn-sm btn-danger" onclick="delTV(' + t.id + ')">' + ICON_TRASH + '</button>' +
            '</div></div>'
        );
    });
}
function openTV() {
    if (!current.id) { APP.toast('Vui lòng lưu Bước 1 trước','error'); return; }
    $('#tvTitle').text('Thêm thành viên'); $('#formTV')[0].reset(); $('#tv_id').val('');
    $('#modalTV').addClass('open');
}
function editTV(id) {
    APP.ajax(URL,{action:'tv_getById', id:id}).done(function (r) {
        if (!r.success) return; var t = r.data;
        $('#tvTitle').text('Sửa thành viên'); $('#tv_id').val(t.id);
        $('#tv_nv').val(t.nhan_vien_id || ''); $('#tv_hn').val(t.ho_ten_ngoai || ''); $('#tv_dn').val(t.don_vi_ngoai || '');
        $('#tv_vt').val(t.vai_tro); $('#tv_mnv').val(t.ma_nv_text || '');
        $('#tv_pt').val(t.phan_tram_dong_gop || ''); $('#tv_gc').val(t.ghi_chu || '');
        $('#modalTV').addClass('open');
    });
}
function delTV(id) {
    APP.confirm('Xóa thành viên này?', function(){ APP.ajax(URL,{action:'tv_delete', id:id}).done(function(r){ APP.toast(r.message,'success'); loadDetail(); }); });
}
$('#formTV').on('submit', function (e) {
    e.preventDefault();
    var data = $(this).serializeArray();
    data.push({name:'action', value:'tv_save'});
    data.push({name:'de_tai_id', value:current.id});
    APP.ajax(URL, data).done(function (r) {
        if (r.success) { APP.toast(r.message,'success'); $('#modalTV').removeClass('open'); loadDetail(); }
        else APP.toast(r.message,'error');
    });
});

/* ===== STEP 3: HD ===== */
function renderHD(arr) {
    var $list = $('#hdList').empty();
    if (!arr.length) { $list.html('<div class="text-muted" style="padding:14px 0;font-size:13px">Chưa có thành viên hội đồng.</div>'); return; }
    arr.forEach(function (h) {
        var ten = (h.chuc_danh_hoc_vi ? h.chuc_danh_hoc_vi + ' ' : '') + h.ho_ten;
        var donVi = h.ten_khoa_phong || h.ten_khoa_text || '';
        $list.append(
            '<div class="wz-sub-item"><div class="info">' +
                '<div class="name">' + APP.escape(ten) + '</div>' +
                '<div class="sub"><span class="badge">' + (HD_VAITRO[h.vai_tro_hd] || h.vai_tro_hd) + '</span>' + (donVi ? ' · ' + APP.escape(donVi) : '') + '</div>' +
            '</div><div class="actions">' +
                '<button class="btn btn-sm" onclick="editHD(' + h.id + ')">' + ICON_EDIT + '</button>' +
                '<button class="btn btn-sm btn-danger" onclick="delHD(' + h.id + ')">' + ICON_TRASH + '</button>' +
            '</div></div>'
        );
    });
}
function openHD() {
    if (!current.id) { APP.toast('Vui lòng lưu Bước 1 trước','error'); return; }
    $('#hdTitle').text('Thêm thành viên hội đồng'); $('#formHD')[0].reset(); $('#hd_id').val(''); $('#hd_vt').val('ThanhVien');
    $('#modalHD').addClass('open');
}
function editHD(id) {
    APP.ajax(URL,{action:'hd_getById', id:id}).done(function (r) {
        if (!r.success) return; var h = r.data;
        $('#hdTitle').text('Sửa thành viên hội đồng'); $('#hd_id').val(h.id);
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
    data.push({name:'action', value:'hd_save'});
    data.push({name:'de_tai_id', value:current.id});
    APP.ajax(URL, data).done(function (r) {
        if (r.success) { APP.toast(r.message,'success'); $('#modalHD').removeClass('open'); loadDetail(); }
        else APP.toast(r.message,'error');
    });
});

/* ===== STEP 4: TL ===== */
function renderTL(arr) {
    var $list = $('#tlList').empty();
    if (!arr.length) { $list.html('<div class="text-muted" style="padding:14px 0;font-size:13px">Chưa có tài liệu.</div>'); return; }
    arr.forEach(function (t) {
        var size = t.kich_thuoc ? (t.kich_thuoc/1024/1024).toFixed(2) + ' MB' : '';
        $list.append(
            '<div class="wz-sub-item"><div class="info">' +
                '<div class="name">' + APP.escape(t.ten_tai_lieu) + '</div>' +
                '<div class="sub"><span class="badge">' + (LOAI_TL[t.loai_tai_lieu] || t.loai_tai_lieu) + '</span> ' + APP.escape(t.ten_file_goc || '') + (size ? ' · ' + size : '') + '</div>' +
            '</div><div class="actions">' +
                '<a class="btn btn-sm" href="' + DOWNLOAD_URL + '?id=' + t.id + '" target="_blank">Tải</a>' +
                '<button class="btn btn-sm btn-danger" onclick="delTL(' + t.id + ')">' + ICON_TRASH + '</button>' +
            '</div></div>'
        );
    });
}
function openTL() {
    if (!current.id) { APP.toast('Vui lòng lưu Bước 1 trước','error'); return; }
    $('#formTL')[0].reset();
    $('#modalTL').addClass('open');
}
function delTL(id) { APP.confirm('Xóa tài liệu?', function(){ APP.ajax(URL,{action:'tl_delete', id:id}).done(function(r){ APP.toast(r.message,'success'); loadDetail(); }); }); }
$('#formTL').on('submit', function (e) {
    e.preventDefault();
    var fd = new FormData(this); fd.append('action','tl_upload'); fd.append('de_tai_id', current.id);
    $.ajax({url:URL, data:fd, processData:false, contentType:false, type:'POST', dataType:'json'})
        .done(function (r) {
            if (r.success) { APP.toast(r.message,'success'); $('#modalTL').removeClass('open'); loadDetail(); }
            else APP.toast(r.message,'error');
        }).fail(function(){ APP.toast('Upload thất bại','error'); });
});

/* ===== SUBMIT ===== */
$('#btn-submit').on('click', function () {
    if (!current.id) return;
    APP.confirm('Gửi đề tài cho quản trị viên duyệt? Sau khi gửi bạn sẽ không sửa được.', function () {
        APP.ajax(URL, {action:'submit', id:current.id}).done(function (r) {
            if (r.success) {
                APP.toast(r.message,'success');
                setTimeout(function(){ window.location.href = APP_BASE + 'GUI/NCKH_DeTaiCuaToi/index.php'; }, 800);
            } else APP.toast(r.message,'error');
        });
    }, {yesText:'Gửi duyệt'});
});

/* ===== INIT ===== */
loadCombos().done(function () {
    if (current.id) loadDetail().done(function () { goStep(1); });
    else goStep(1);
});
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
