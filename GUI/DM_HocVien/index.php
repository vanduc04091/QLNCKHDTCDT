<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../BUS/DM_DoiTuongHocVien_BUS.php';

Helper::requireLogin();
if (!PhanQuyenHelper::hasQuyen('DM_HocVien', PhanQuyenHelper::QUYEN_XEM)) {
    echo 'Bạn không có quyền truy cập chức năng này.'; exit;
}

$canAdd = PhanQuyenHelper::hasQuyen('DM_HocVien', PhanQuyenHelper::QUYEN_THEM);
$canEdit = PhanQuyenHelper::hasQuyen('DM_HocVien', PhanQuyenHelper::QUYEN_SUA);
$canDel = PhanQuyenHelper::hasQuyen('DM_HocVien', PhanQuyenHelper::QUYEN_XOA);

$doiTuongCombo = DM_DoiTuongHocVien_BUS::getCombo();

$pageTitle = 'Quản lý học viên';
$activeMenu = 'DM_HocVien';
$uploadUrl = AppConfig::baseUrl('assets/uploads/hocvien/');
require __DIR__ . '/../layouts/header.php';
?>

<div class="breadcrumb">
    <a href="<?= AppConfig::baseUrl('GUI/dashboard/index.php') ?>">Trang chủ</a>
    <span class="sep">›</span> Danh mục
    <span class="sep">›</span> <span>Học viên</span>
</div>

<!-- Stats -->
<div class="hv-stats" id="statsBar">
    <div class="hv-stat">
        <div class="hv-stat-icon hv-stat-blue">
            <?= IconHelper::svg('users', '22') ?>
        </div>
        <div>
            <div class="hv-stat-label">Tổng học viên</div>
            <div class="hv-stat-value" id="stTotal">—</div>
        </div>
    </div>
    <div class="hv-stat">
        <div class="hv-stat-icon hv-stat-green">
            <?= IconHelper::svg('check-circle', '22') ?>
        </div>
        <div>
            <div class="hv-stat-label">Đang hoạt động</div>
            <div class="hv-stat-value" id="stActive">—</div>
        </div>
    </div>
    <div class="hv-stat">
        <div class="hv-stat-icon hv-stat-purple">
            <?= IconHelper::svg('building-2', '22') ?>
        </div>
        <div>
            <div class="hv-stat-label">Là nhân viên</div>
            <div class="hv-stat-value" id="stLaNV">—</div>
        </div>
    </div>
    <div class="hv-stat">
        <div class="hv-stat-icon hv-stat-orange">
            <?= IconHelper::svg('user-check', '22') ?>
        </div>
        <div>
            <div class="hv-stat-label">Ngoài cơ quan</div>
            <div class="hv-stat-value" id="stNgoai">—</div>
        </div>
    </div>
</div>

<div class="card">
    <div class="toolbar">
        <div class="left">
            <input type="text" id="search" class="form-control" placeholder="Tìm mã, họ tên, SĐT, email, đơn vị..." style="max-width:320px">
            <select id="filterDoiTuong" class="form-select" style="max-width:220px">
                <option value="0">-- Tất cả đối tượng --</option>
                <?php foreach ($doiTuongCombo as $d): ?>
                    <option value="<?= $d['id'] ?>"><?= Helper::h($d['ten_doi_tuong']) ?></option>
                <?php endforeach; ?>
            </select>
            <select id="filterLoai" class="form-select" style="max-width:170px">
                <option value="">Tất cả học viên</option>
                <option value="1">Là nhân viên</option>
                <option value="0">Ngoài cơ quan</option>
            </select>
            <select id="filterDaXoa" class="form-select" style="max-width:160px">
                <option value="0">Đang hoạt động</option>
                <option value="1">Thùng rác</option>
            </select>
        </div>
        <div class="right">
            <div class="hv-view-toggle" role="tablist">
                <button type="button" class="hv-view-btn" data-view="grid" title="Dạng thẻ">
                    <?= IconHelper::svg('grid', '16') ?>
                </button>
                <button type="button" class="hv-view-btn active" data-view="table" title="Dạng bảng">
                    <?= IconHelper::svg('list', '16') ?>
                </button>
            </div>
            <?php if ($canAdd): ?>
                <button type="button" class="btn btn-primary" onclick="openCreate()">+ Thêm học viên</button>
            <?php endif; ?>
        </div>
    </div>

    <div id="viewWrap" style="position:relative;min-height:240px">
        <!-- Grid view -->
        <div id="gridView" class="hv-grid" style="display:none"></div>
        <!-- Table view -->
        <div id="tableView" class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width:64px"></th>
                        <th style="width:110px">Mã</th>
                        <th>Họ tên</th>
                        <th>Đối tượng</th>
                        <th>Đơn vị</th>
                        <th>SĐT</th>
                        <th>Email</th>
                        <th class="text-center" style="width:110px">Trạng thái</th>
                        <th class="text-right" style="width:130px">Hành động</th>
                    </tr>
                </thead>
                <tbody id="tbody"></tbody>
            </table>
        </div>
    </div>
    <div class="pagination-wrap">
        <div id="pageInfo" class="text-muted">-</div>
        <div id="pageNav"></div>
    </div>
</div>

<!-- Modal Form -->
<div class="modal-backdrop" id="modalForm">
    <div class="modal" style="max-width:900px">
        <div class="modal-header">
            <h3 id="modalTitle">Thêm học viên</h3>
            <button type="button" class="close" onclick="closeModal()">&times;</button>
        </div>
        <form id="formHV" enctype="multipart/form-data">
            <div class="modal-body">
                <input type="hidden" name="id" id="f_id">
                <input type="hidden" name="remove_avatar" id="f_remove_avatar" value="0">

                <div class="hv-form-top">
                    <!-- Avatar uploader -->
                    <div class="hv-avatar-uploader">
                        <input type="file" name="avatar_file" id="f_avatar" accept="image/*" style="display:none">
                        <div class="hv-avatar-preview" id="avatarPreview" title="Click để đổi ảnh">
                            <span id="avatarInitials">HV</span>
                            <div class="hv-avatar-overlay">
                                <?= IconHelper::svg('camera', '24') ?>
                                <span>Đổi ảnh</span>
                            </div>
                        </div>
                        <div class="hv-avatar-actions">
                            <button type="button" class="btn btn-sm" id="btnPickAvatar">
                                <?= IconHelper::svg('upload', '14') ?>
                                Chọn ảnh
                            </button>
                            <button type="button" class="btn btn-sm btn-danger" id="btnRemoveAvatar" style="display:none" onclick="removeAvatar()">Xóa ảnh</button>
                            <div class="hv-avatar-hint">JPG/PNG ≤ 3MB</div>
                        </div>
                    </div>

                    <!-- Toggle: là nhân viên? -->
                    <div class="hv-toggle-card">
                        <label class="hv-toggle">
                            <input type="checkbox" id="f_la_nhan_vien" name="la_nhan_vien" value="1">
                            <span class="hv-toggle-slider"></span>
                            <span class="hv-toggle-label">
                                <strong>Học viên là nhân viên của cơ quan</strong>
                                <small>Bật nếu học viên là nhân viên đang làm việc — tránh lặp hồ sơ 2 lần.</small>
                            </span>
                        </label>
                        <div id="nvSelectWrap" class="form-group" style="display:none;margin-top:10px;margin-bottom:0">
                            <label>Chọn nhân viên <span class="required">*</span></label>
                            <select name="nhan_vien_id" id="f_nhan_vien_id" class="form-select"></select>
                            <div class="form-error" id="nvLinkedHint" style="display:none">⚠ Nhân viên này đã có hồ sơ học viên. Vui lòng chọn nhân viên khác.</div>
                        </div>
                    </div>
                </div>

                <div class="form-row-3">
                    <div class="form-group">
                        <label>Mã học viên</label>
                        <input type="text" name="ma_hv" id="f_ma_hv" class="form-control" maxlength="50" placeholder="Để trống → tự sinh">
                    </div>
                    <div class="form-group">
                        <label>Họ tên <span class="required">*</span></label>
                        <input type="text" name="ho_ten" id="f_ho_ten" class="form-control" required maxlength="200">
                    </div>
                    <div class="form-group">
                        <label>Trạng thái</label>
                        <select name="trang_thai" id="f_trang_thai" class="form-select">
                            <option value="1">Hoạt động</option>
                            <option value="0">Ngừng</option>
                        </select>
                    </div>
                </div>

                <div class="form-row-3">
                    <div class="form-group">
                        <label>Ngày sinh</label>
                        <input type="date" name="ngay_sinh" id="f_ngay_sinh" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Giới tính</label>
                        <select name="gioi_tinh" id="f_gioi_tinh" class="form-select">
                            <option value="">--</option>
                            <option value="Nam">Nam</option>
                            <option value="Nữ">Nữ</option>
                            <option value="Khác">Khác</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Đối tượng học viên</label>
                        <select name="doi_tuong_id" id="f_doi_tuong_id" class="form-select">
                            <option value="">-- Chọn --</option>
                            <?php foreach ($doiTuongCombo as $d): ?>
                                <option value="<?= $d['id'] ?>"><?= Helper::h($d['ten_doi_tuong']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Điện thoại</label>
                        <input type="text" name="dien_thoai" id="f_dien_thoai" class="form-control" maxlength="20">
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" id="f_email" class="form-control" maxlength="100">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>CCCD</label>
                        <input type="text" name="cccd" id="f_cccd" class="form-control" pattern="\d{9,12}" maxlength="12" placeholder="9-12 chữ số">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Đơn vị công tác</label>
                        <input type="text" name="don_vi_cong_tac" id="f_don_vi_cong_tac" class="form-control" maxlength="200">
                    </div>
                    <div class="form-group">
                        <label>Chức vụ</label>
                        <input type="text" name="chuc_vu" id="f_chuc_vu" class="form-control" maxlength="100">
                    </div>
                </div>

                <div class="form-group">
                    <label>Địa chỉ</label>
                    <input type="text" name="dia_chi" id="f_dia_chi" class="form-control" maxlength="250">
                </div>

                <div class="form-group">
                    <label>Ghi chú</label>
                    <textarea name="ghi_chu" id="f_ghi_chu" class="form-control" rows="2" maxlength="500"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn" onclick="closeModal()">Hủy</button>
                <button type="submit" class="btn btn-primary">Lưu</button>
            </div>
        </form>
    </div>
</div>

<!-- Drawer: Xem nhanh học viên (tabs) -->
<div class="drawer-backdrop" id="drawerLop">
    <div class="drawer" style="max-width:760px">
        <div class="drawer-header">
            <div>
                <h3 id="lopDrwTitle" style="margin:0">Học viên</h3>
                <div id="lopDrwSub" class="text-muted" style="font-size:12.5px;margin-top:2px"></div>
            </div>
            <button type="button" class="close" onclick="closeLopDrawer()">&times;</button>
        </div>
        <div class="drawer-body" style="padding:0">
            <div class="hvtab-bar" id="hvtabBar">
                <button type="button" class="hvtab active" data-tab="lop">Chương trình đào tạo</button>
                <button type="button" class="hvtab" data-tab="mon">Bài học</button>
                <button type="button" class="hvtab" data-tab="lich">Lịch học</button>
                <button type="button" class="hvtab" data-tab="dd">Điểm danh</button>
                <button type="button" class="hvtab" data-tab="diem">Bảng điểm</button>
                <button type="button" class="hvtab" data-tab="hoso">Hồ sơ</button>
                <button type="button" class="hvtab" data-tab="cc">Chứng chỉ</button>
            </div>

            <div class="hvtab-content">
                <!-- Chương trình đào tạo (form ghi danh) -->
                <div class="hvtab-pane active" data-pane="lop">
                    <div style="background:#f8fafc;padding:12px;border-radius:8px;margin-bottom:14px;border:1px solid var(--gray-200)">
                        <div style="font-weight:600;margin-bottom:8px;font-size:13.5px">Ghi danh vào chương trình đào tạo</div>
                        <div style="display:flex;gap:8px;flex-wrap:wrap;align-items:center">
                            <select id="lopDrwKhoa" class="form-select" style="flex:1;min-width:180px">
                                <option value="">-- Chọn khóa học --</option>
                            </select>
                            <select id="lopDrwSelect" class="form-select" style="flex:1;min-width:180px" disabled>
                                <option value="">-- Chọn chương trình --</option>
                            </select>
                            <input type="date" id="lopDrwNgay" class="form-control" style="width:160px">
                            <button type="button" class="btn btn-primary btn-sm" id="btnGhiDanh">Ghi danh</button>
                        </div>
                        <div class="text-muted" style="font-size:11.5px;margin-top:6px">Chọn khóa học trước, sau đó chọn chương trình đào tạo thuộc khóa đó.</div>
                    </div>
                    <div id="lopDrwList" style="display:flex;flex-direction:column;gap:8px"></div>
                    <div id="lopDrwEmpty" class="hv-empty" style="display:none">Học viên chưa ghi danh vào chương trình nào.</div>
                </div>

                <div class="hvtab-pane" data-pane="mon"><div id="paneMon" class="hv-pane-loading">Chọn tab để tải...</div></div>
                <div class="hvtab-pane" data-pane="lich"><div id="paneLich" class="hv-pane-loading">Chọn tab để tải...</div></div>
                <div class="hvtab-pane" data-pane="dd"><div id="paneDD" class="hv-pane-loading">Chọn tab để tải...</div></div>
                <div class="hvtab-pane" data-pane="diem"><div id="paneDiem" class="hv-pane-loading">Chọn tab để tải...</div></div>
                <div class="hvtab-pane" data-pane="hoso"><div id="paneHoSo" class="hv-pane-loading">Chọn tab để tải...</div></div>
                <div class="hvtab-pane" data-pane="cc"><div id="paneCC" class="hv-pane-loading">Chọn tab để tải...</div></div>
            </div>
        </div>
    </div>
</div>

<style>
    .hv-lop-row { display:flex; align-items:center; gap:10px; padding:10px 12px; border:1px solid var(--gray-200); border-radius:8px; background:#fff; }
    .hv-lop-row:hover { border-color: var(--primary); }
    .hv-lop-info { flex:1; min-width:0; }
    .hv-lop-name { font-weight:600; color: var(--gray-800); }
    .hv-lop-code { font-family: monospace; font-size:11.5px; color: var(--gray-500); margin-top:2px; }
    .hv-lop-meta { font-size:11.5px; color: var(--gray-500); margin-top:3px; }
    .hv-lop-tt { font-size:11px; padding:2px 8px; border-radius:10px; font-weight:600; white-space:nowrap; }
    .hv-lop-tt.t0 { background:#fef3c7; color:#92400e; }
    .hv-lop-tt.t1 { background:#dbeafe; color:#1e40af; }
    .hv-lop-tt.t2 { background:#dcfce7; color:#166534; }
    .hv-lop-tt.t3 { background:#e2e8f0; color:#475569; }

    /* Tabs trong drawer */
    .hvtab-bar { display:flex; gap:0; border-bottom: 2px solid var(--gray-200); padding: 0 16px; overflow-x:auto; position:sticky; top:0; background:#fff; z-index:1; }
    .hvtab { padding: 11px 14px; background:transparent; border:0; border-bottom:2px solid transparent; cursor:pointer; font-size:13px; font-weight:600; color:var(--gray-500); margin-bottom:-2px; white-space:nowrap; }
    .hvtab:hover { color: var(--gray-700); }
    .hvtab.active { color: var(--primary); border-bottom-color: var(--primary); }
    .hvtab-content { padding: 16px; }
    .hvtab-pane { display:none; }
    .hvtab-pane.active { display:block; }
    .hv-pane-loading { padding: 30px 16px; text-align:center; color:#000; font-size:13px; }
    .hv-empty { padding: 30px 16px; text-align:center; color:var(--gray-500); font-size:13px; }

    .hv-stat-mini { display:grid; grid-template-columns: repeat(auto-fit, minmax(110px, 1fr)); gap:8px; margin-bottom:14px; }
    .hv-stat-cell { background:var(--gray-50); padding:10px; border-radius:6px; border-left:3px solid var(--primary); }
    .hv-stat-cell .num { font-size:20px; font-weight:800; font-variant-numeric:tabular-nums; }
    .hv-stat-cell .lbl { font-size:11.5px; color:var(--gray-500); }

    .hv-tbl { width:100%; border-collapse:collapse; font-size:13px; }
    .hv-tbl th { text-align:left; padding:7px 8px; background:var(--gray-50); font-weight:600; font-size:12px; color:var(--gray-600); text-transform:uppercase; letter-spacing:.3px; }
    .hv-tbl td { padding:8px; border-bottom:1px solid var(--gray-100); }
    .hv-tbl tr:hover td { background:#fafbfc; }
</style>

<script>
var URL = APP_BASE + 'GUI/DM_HocVien/ajax_handler.php';
var UPLOAD_URL = <?= json_encode($uploadUrl) ?>;
var CAN_EDIT = <?= $canEdit?'true':'false' ?>;
var CAN_DEL = <?= $canDel?'true':'false' ?>;
var CAN_ADD = <?= $canAdd?'true':'false' ?>;
var state = { page: 1, pageSize: 20, search: '', daXoa: 0, doiTuongId: 0, laNhanVien: '', view: 'table' };
var nhanVienList = [];
var nhanVienLoaded = false;

var ICON_EDIT = '<?= addslashes(IconHelper::svg('edit', '16')) ?>';
var ICON_TRASH = '<?= addslashes(IconHelper::svg('trash', '16')) ?>';
var ICON_RESTORE = '<?= addslashes(IconHelper::svg('refresh', '16')) ?>';
var ICON_EMPTY = '<?= addslashes(IconHelper::svg('dashboard', '40')) ?>';
var ICON_PHONE = '<?= addslashes(IconHelper::svg('phone', '14')) ?>';
var ICON_MAIL = '<?= addslashes(IconHelper::svg('mail', '14')) ?>';
var ICON_BUILDING = '<?= addslashes(IconHelper::svg('building', '14')) ?>';
var ICON_CAMERA = '<?= addslashes(IconHelper::svg('camera', '24')) ?>';
var ICON_USERS = '<?= addslashes(IconHelper::svg('users', '22')) ?>';
var ICON_CHECK_CIRCLE = '<?= addslashes(IconHelper::svg('check-circle', '22')) ?>';
var ICON_BUILDING_2 = '<?= addslashes(IconHelper::svg('building-2', '22')) ?>';
var ICON_USER_CHECK = '<?= addslashes(IconHelper::svg('user-check', '22')) ?>';
var ICON_GRID = '<?= addslashes(IconHelper::svg('grid', '16')) ?>';
var ICON_LIST = '<?= addslashes(IconHelper::svg('list', '16')) ?>';
var ICON_UPLOAD = '<?= addslashes(IconHelper::svg('upload', '14')) ?>';
var ICON_GRAD = '<?= addslashes(IconHelper::svg('graduation-cap', '14')) ?>';
var ICON_PLUS = '<?= addslashes(IconHelper::svg('plus', '14')) ?>';
var ICON_TRASH_SM = '<?= addslashes(IconHelper::svg('trash', '13')) ?>';

function loadStats() {
    APP.ajax(URL, {action: 'getStats'}).done(function (res) {
        if (!res.success) return;
        $('#stTotal').text(res.data.total || 0);
        $('#stActive').text(res.data.active || 0);
        $('#stLaNV').text(res.data.la_nv || 0);
        $('#stNgoai').text(res.data.ngoai || 0);
    });
}

function load() {
    APP.showLoading('#viewWrap');
    APP.ajax(URL, {
        action: 'getPaged',
        page: state.page, pageSize: state.pageSize,
        search: state.search, da_xoa: state.daXoa,
        doi_tuong_id: state.doiTuongId,
        la_nhan_vien: state.laNhanVien
    }).done(function (res) {
        APP.hideLoading('#viewWrap');
        if (!res.success) { APP.toast(res.message, 'error'); return; }
        if (state.view === 'grid') renderCards(res.data); else renderRows(res.data);
        renderInfo(res.pagination);
    });
}

function initials(name) {
    if (!name) return 'HV';
    var parts = name.trim().split(/\s+/);
    if (parts.length === 1) return parts[0].substr(0, 2).toUpperCase();
    return (parts[parts.length - 2].charAt(0) + parts[parts.length - 1].charAt(0)).toUpperCase();
}

function colorFromName(name) {
    var colors = ['#2563eb', '#7c3aed', '#db2777', '#dc2626', '#ea580c', '#d97706', '#16a34a', '#0891b2', '#4f46e5', '#0284c7'];
    var h = 0;
    for (var i = 0; i < (name || '').length; i++) h = (h * 31 + name.charCodeAt(i)) & 0xffff;
    return colors[h % colors.length];
}

function avatarHtml(r, size) {
    size = size || 56;
    if (r.avatar) {
        return '<div class="hv-av" style="width:' + size + 'px;height:' + size + 'px">' +
               '<img src="' + UPLOAD_URL + APP.escape(r.avatar) + '" alt="' + APP.escape(r.ho_ten) + '">' +
               '</div>';
    }
    var bg = colorFromName(r.ho_ten);
    return '<div class="hv-av hv-av-initials" style="width:' + size + 'px;height:' + size + 'px;background:' + bg + '">' + APP.escape(initials(r.ho_ten)) + '</div>';
}

function renderCards(rows) {
    var $g = $('#gridView').empty();
    if (!rows.length) {
        $g.html('<div class="empty-state" style="grid-column:1/-1;padding:60px 20px"><div class="icon">' + ICON_EMPTY + '</div>Không có học viên phù hợp</div>');
        return;
    }
    rows.forEach(function (r) {
        var nvBadge = r.la_nhan_vien == 1
            ? '<span class="hv-chip hv-chip-blue" title="Là nhân viên cơ quan">NV ' + APP.escape(r.ma_nv || '') + '</span>'
            : '<span class="hv-chip hv-chip-gray">Ngoài</span>';
        var statusDot = r.trang_thai == 1
            ? '<span class="hv-dot hv-dot-active" title="Hoạt động"></span>'
            : '<span class="hv-dot hv-dot-inactive" title="Ngừng"></span>';
        var dt = r.ten_doi_tuong ? '<span class="hv-chip hv-chip-purple">' + APP.escape(r.ten_doi_tuong) + '</span>' : '';

        var donVi = r.la_nhan_vien == 1
            ? (r.ten_khoa_phong || r.ten_benh_vien || '—')
            : (r.don_vi_cong_tac || '—');

        var contact = '';
        if (r.dien_thoai) contact += '<div class="hv-meta-line">' + ICON_PHONE + APP.escape(r.dien_thoai) + '</div>';
        if (r.email) contact += '<div class="hv-meta-line">' + ICON_MAIL + APP.escape(r.email) + '</div>';

        var actions = '';
        if (state.daXoa == 0) {
            actions += '<button class="btn btn-sm" title="Chương trình đã ghi danh" onclick="openLopDrawer(' + r.id + ', \'' + APP.escape(r.ho_ten).replace(/'/g, "\\\'") + '\', \'' + APP.escape(r.ma_hv || '') + '\')">' + ICON_GRAD + '</button>';
            if (CAN_EDIT) actions += '<button class="btn btn-sm" title="Sửa" onclick="openEdit(' + r.id + ')">' + ICON_EDIT + '</button>';
            if (CAN_DEL) actions += '<button class="btn btn-sm btn-danger" title="Xóa" onclick="trashItem(' + r.id + ')">' + ICON_TRASH + '</button>';
        } else {
            if (CAN_EDIT) actions += '<button class="btn btn-sm btn-success" onclick="restoreItem(' + r.id + ')">' + ICON_RESTORE + '</button>';
            if (CAN_DEL) actions += '<button class="btn btn-sm btn-danger" onclick="deleteItem(' + r.id + ')">Xóa vĩnh viễn</button>';
        }

        var html =
            '<div class="hv-card">' +
                '<div class="hv-card-head">' + avatarHtml(r, 60) +
                    '<div class="hv-card-ident">' +
                        '<div class="hv-card-name">' + statusDot + APP.escape(r.ho_ten) + '</div>' +
                        '<div class="hv-card-code">' + APP.escape(r.ma_hv) + (r.chuc_vu ? ' · ' + APP.escape(r.chuc_vu) : '') + '</div>' +
                        '<div class="hv-chips">' + nvBadge + dt + '</div>' +
                    '</div>' +
                '</div>' +
                '<div class="hv-card-body">' +
                    '<div class="hv-meta-line hv-meta-strong">' + ICON_BUILDING + APP.escape(donVi) + '</div>' +
                    contact +
                '</div>' +
                '<div class="hv-card-actions">' + actions + '</div>' +
            '</div>';
        $g.append(html);
    });
}

function renderRows(rows) {
    var $tb = $('#tbody').empty();
    if (!rows.length) {
        $tb.append('<tr><td colspan="9"><div class="empty-state"><div class="icon">' + ICON_EMPTY + '</div>Không có dữ liệu</div></td></tr>');
        return;
    }
    rows.forEach(function (r) {
        var tt = r.trang_thai == 1
            ? '<span class="badge badge-success">Hoạt động</span>'
            : '<span class="badge badge-danger">Ngừng</span>';
        var nvBadge = r.la_nhan_vien == 1 ? ' <span class="hv-chip hv-chip-blue" style="font-size:10.5px">NV</span>' : '';
        var actions = '';
        if (state.daXoa == 0) {
            actions += '<button class="btn btn-sm" title="Chương trình đào tạo" onclick="openLopDrawer(' + r.id + ', \'' + APP.escape(r.ho_ten).replace(/'/g, "\\\'") + '\', \'' + APP.escape(r.ma_hv || '') + '\')">' + ICON_GRAD + '</button>';
            if (CAN_EDIT) actions += '<button class="btn btn-sm" title="Sửa" onclick="openEdit(' + r.id + ')">Sửa</button>';
            if (CAN_DEL) actions += '<button class="btn btn-sm btn-danger" title="Xóa" onclick="trashItem(' + r.id + ')">Xóa</button>';
        } else {
            if (CAN_EDIT) actions += '<button class="btn btn-sm btn-success" onclick="restoreItem(' + r.id + ')">↺</button>';
            if (CAN_DEL) actions += '<button class="btn btn-sm btn-danger" onclick="deleteItem(' + r.id + ')">Xóa</button>';
        }
        var donVi = r.la_nhan_vien == 1 ? (r.ten_khoa_phong || '-') : (r.don_vi_cong_tac || '-');
        $tb.append(
            '<tr>' +
                '<td>' + avatarHtml(r, 36) + '</td>' +
                '<td><strong>' + APP.escape(r.ma_hv) + '</strong></td>' +
                '<td>' + APP.escape(r.ho_ten) + nvBadge + '</td>' +
                '<td>' + APP.escape(r.ten_doi_tuong || '-') + '</td>' +
                '<td>' + APP.escape(donVi) + '</td>' +
                '<td>' + APP.escape(r.dien_thoai || '-') + '</td>' +
                '<td>' + APP.escape(r.email || '-') + '</td>' +
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

$('#pageNav').on('click', 'button[data-p]', function () {
    var p = parseInt($(this).data('p'), 10);
    if (!p || p === state.page) return;
    state.page = p; load();
});

$('#search').on('input', APP.debounce(function () { state.search = $(this).val(); state.page = 1; load(); }, 400));
$('#filterDoiTuong').on('change', function () { state.doiTuongId = parseInt(this.value, 10) || 0; state.page = 1; load(); });
$('#filterLoai').on('change', function () { state.laNhanVien = this.value; state.page = 1; load(); });
$('#filterDaXoa').on('change', function () { state.daXoa = parseInt(this.value, 10) || 0; state.page = 1; load(); });

// View toggle
$('.hv-view-btn').on('click', function () {
    $('.hv-view-btn').removeClass('active');
    $(this).addClass('active');
    state.view = $(this).data('view');
    $('#gridView').toggle(state.view === 'grid');
    $('#tableView').toggle(state.view === 'table');
    load();
});

// --- Modal ---
function overlayHtml() {
    return '<div class="hv-avatar-overlay">' + ICON_CAMERA + '<span>Đổi ảnh</span></div>';
}

function resetForm() {
    $('#formHV')[0].reset();
    $('#f_id').val('');
    $('#f_remove_avatar').val('0');
    $('#avatarPreview').html('<span id="avatarInitials">HV</span>' + overlayHtml()).css('background', '').css('color', '');
    $('#btnRemoveAvatar').hide();
    $('#f_la_nhan_vien').prop('checked', false);
    $('#nvSelectWrap').hide();
    $('#nvLinkedHint').hide();
}

function ensureNhanVienCombo(cb) {
    if (nhanVienLoaded) { cb && cb(); return; }
    APP.ajax(URL, {action: 'getComboNhanVien'}).done(function (res) {
        if (res.success) {
            nhanVienList = res.data || [];
            var $s = $('#f_nhan_vien_id').empty().append('<option value="">-- Chọn nhân viên --</option>');
            nhanVienList.forEach(function (n) {
                $s.append('<option value="' + n.id + '">' + APP.escape(n.ma_nv) + ' - ' + APP.escape(n.ho_ten) + '</option>');
            });
            nhanVienLoaded = true;
            cb && cb();
        }
    });
}

function openCreate() {
    resetForm();
    $('#modalTitle').text('Thêm học viên');
    ensureNhanVienCombo();
    $('#modalForm').addClass('open');
}

function openEdit(id) {
    resetForm();
    ensureNhanVienCombo(function () {
        APP.ajax(URL, {action: 'getById', id: id}).done(function (res) {
            if (!res.success) { APP.toast(res.message, 'error'); return; }
            var e = res.data;
            $('#modalTitle').text('Sửa học viên');
            $('#f_id').val(e.id);
            $('#f_ma_hv').val(e.ma_hv);
            $('#f_ho_ten').val(e.ho_ten);
            $('#f_ngay_sinh').val(e.ngay_sinh || '');
            $('#f_gioi_tinh').val(e.gioi_tinh || '');
            $('#f_dien_thoai').val(e.dien_thoai || '');
            $('#f_email').val(e.email || '');
            $('#f_cccd').val(e.cccd || '');
            $('#f_dia_chi').val(e.dia_chi || '');
            $('#f_don_vi_cong_tac').val(e.don_vi_cong_tac || '');
            $('#f_chuc_vu').val(e.chuc_vu || '');
            $('#f_doi_tuong_id').val(e.doi_tuong_id || '');
            $('#f_trang_thai').val(e.trang_thai);
            $('#f_ghi_chu').val(e.ghi_chu || '');
            // Set NV mà KHÔNG autofill (giữ nguyên dữ liệu đã nhập)
            suppressNVAutofill = true;
            $('#f_la_nhan_vien').prop('checked', e.la_nhan_vien == 1).trigger('change');
            $('#f_nhan_vien_id').val(e.nhan_vien_id || '');
            suppressNVAutofill = false;
            if (e.avatar) {
                $('#avatarPreview').html('<img src="' + UPLOAD_URL + APP.escape(e.avatar) + '">' + overlayHtml()).css('background', '').css('color', '');
                $('#btnRemoveAvatar').show();
            } else {
                $('#avatarPreview').html('<span>' + APP.escape(initials(e.ho_ten)) + '</span>' + overlayHtml()).css('background', colorFromName(e.ho_ten)).css('color', '#fff');
            }
            $('#modalForm').addClass('open');
        });
    });
}

function closeModal() { $('#modalForm').removeClass('open'); }

$('#f_la_nhan_vien').on('change', function () {
    var on = this.checked;
    $('#nvSelectWrap').toggle(on);
    if (on) {
        // Nếu đang có giá trị sẵn từ combo, autofill thông tin từ NV
        $('#f_nhan_vien_id').trigger('change');
    } else {
        $('#f_nhan_vien_id').val('');
    }
});

// Auto-fill họ tên + mã HV MỖI KHI đổi nhân viên (kể cả chọn lại).
// Có cờ riêng để không ghi đè khi người dùng đang edit bản ghi có sẵn.
var suppressNVAutofill = false;
$('#f_nhan_vien_id').on('change', function () {
    if (suppressNVAutofill) return;
    var id = parseInt(this.value, 10) || 0;
    if (!id) return;
    var nv = nhanVienList.find(function (x) { return x.id == id; });
    if (!nv) return;
    $('#f_ho_ten').val(nv.ho_ten).trigger('input');
    $('#f_ma_hv').val('HV-' + nv.ma_nv);
});

$('#f_ho_ten').on('input', function () {
    // Cập nhật preview initials nếu chưa có ảnh thật
    if (!$('#avatarPreview img').length) {
        var name = $(this).val();
        $('#avatarPreview').html('<span>' + (initials(name) || 'HV') + '</span><div class="hv-avatar-overlay">' + ICON_CAMERA + '<span>Đổi ảnh</span></div>').css('background', colorFromName(name)).css('color', '#fff');
    }
});

// Trigger file picker
$('#btnPickAvatar, #avatarPreview').on('click', function (e) {
    e.preventDefault();
    $('#f_avatar').trigger('click');
});

$('#f_avatar').on('change', function (e) {
    var file = e.target.files[0];
    if (!file) return;
    if (file.size > 3145728) { APP.toast('Ảnh vượt quá 3MB', 'error'); this.value=''; return; }
    var reader = new FileReader();
    reader.onload = function (ev) {
        $('#avatarPreview').html('<img src="' + ev.target.result + '"><div class="hv-avatar-overlay">' + ICON_CAMERA + '<span>Đổi ảnh</span></div>').css('background', '').css('color', '');
        $('#btnRemoveAvatar').show();
        $('#f_remove_avatar').val('0');
    };
    reader.readAsDataURL(file);
});

function removeAvatar() {
    $('#f_avatar').val('');
    $('#f_remove_avatar').val('1');
    var name = $('#f_ho_ten').val();
    $('#avatarPreview').text(initials(name) || 'HV').css('background', colorFromName(name)).css('color', '#fff');
    $('#btnRemoveAvatar').hide();
}

$('#formHV').on('submit', function (e) {
    e.preventDefault();
    var fd = new FormData(this);
    fd.append('action', $('#f_id').val() ? 'update' : 'insert');
    $.ajax({
        url: URL, type: 'POST', data: fd,
        processData: false, contentType: false, dataType: 'json',
        headers: window.CSRF_TOKEN ? {'X-CSRF-Token': window.CSRF_TOKEN} : {}
    }).done(function (res) {
        if (res.success) { APP.toast(res.message, 'success'); closeModal(); load(); loadStats(); }
        else APP.toast(res.message, 'error');
    }).fail(function () { APP.toast('Lỗi máy chủ', 'error'); });
});

function trashItem(id) {
    APP.confirm('Chuyển học viên này vào thùng rác?', function () {
        APP.ajax(URL, {action: 'trash', id: id}).done(function (res) {
            res.success ? (APP.toast(res.message, 'success'), load(), loadStats()) : APP.toast(res.message, 'error');
        });
    });
}
function restoreItem(id) {
    APP.ajax(URL, {action: 'restore', id: id}).done(function (res) {
        res.success ? (APP.toast(res.message, 'success'), load(), loadStats()) : APP.toast(res.message, 'error');
    });
}
function deleteItem(id) {
    APP.confirm('Xóa VĨNH VIỄN học viên này (kèm ảnh)?', function () {
        APP.ajax(URL, {action: 'delete', id: id}).done(function (res) {
            res.success ? (APP.toast(res.message, 'success'), load(), loadStats()) : APP.toast(res.message, 'error');
        });
    }, {yesText: 'Xóa vĩnh viễn'});
}

// ====== Drawer: xem nhanh học viên (tabs) ======
var lopDrw = { hocVienId: 0, hocVienTen: '', hocVienMa: '', khoaCombo: null, overview: null, overviewLoaded: false };

function openLopDrawer(hvId, hoTen, maHv) {
    lopDrw.hocVienId = hvId;
    lopDrw.hocVienTen = hoTen;
    lopDrw.hocVienMa = maHv;
    lopDrw.overview = null;
    lopDrw.overviewLoaded = false;
    $('#lopDrwTitle').text(hoTen);
    $('#lopDrwSub').text(maHv || '');
    $('#lopDrwList').html('');
    $('#lopDrwEmpty').hide();
    $('#lopDrwNgay').val(new Date().toISOString().substring(0, 10));

    // Reset tabs về Lớp học
    $('.hvtab').removeClass('active');
    $('.hvtab[data-tab="lop"]').addClass('active');
    $('.hvtab-pane').removeClass('active');
    $('.hvtab-pane[data-pane="lop"]').addClass('active');
    $('.hvtab-content .hv-pane-loading').text('Chọn tab để tải...');

    // Reset 2 combo ghi danh
    $('#lopDrwKhoa').val('');
    $('#lopDrwSelect').empty().append('<option value="">-- Chọn chương trình --</option>').prop('disabled', true);

    $('#drawerLop').addClass('open').find('.drawer').addClass('open');
    loadLopCuaHv();
    ensureKhoaCombo();
}

// Tab switching: lazy load overview lần đầu, sau đó chỉ render pane
$(document).on('click', '.hvtab', function () {
    var tab = $(this).data('tab');
    $('.hvtab').removeClass('active');
    $(this).addClass('active');
    $('.hvtab-pane').removeClass('active');
    $('.hvtab-pane[data-pane="' + tab + '"]').addClass('active');
    if (tab === 'lop') return;  // Lớp dùng API riêng, đã load
    if (lopDrw.overviewLoaded) {
        renderPane(tab);
    } else {
        loadOverview(function () { renderPane(tab); });
    }
});

function loadOverview(cb) {
    APP.ajax(URL, { action: 'getOverview', hoc_vien_id: lopDrw.hocVienId }).done(function (res) {
        if (!res.success) { APP.toast(res.message, 'error'); return; }
        lopDrw.overview = res.data;
        lopDrw.overviewLoaded = true;
        cb && cb();
    });
}

function renderPane(tab) {
    var ov = lopDrw.overview || {};
    if (tab === 'mon') renderMon(ov.mon_hoc || []);
    else if (tab === 'lich') renderLich(ov.lich_hoc || []);
    else if (tab === 'dd')   renderDD(ov.diem_danh_stats || {}, ov.diem_danh_detail || []);
    else if (tab === 'diem') renderDiem(ov.ket_qua || []);
    else if (tab === 'hoso') renderHoSo(ov.ho_so || []);
    else if (tab === 'cc')   renderCC(ov.chung_chi || []);
}

function renderMon(rows) {
    var $p = $('#paneMon');
    if (!rows.length) { $p.html('<div class="hv-empty">Chưa có bài học</div>'); return; }
    var h = '<table class="hv-tbl"><thead><tr><th>Chương trình đào tạo</th><th>Mã</th><th>Tên bài</th><th class="text-center">Tiết</th><th class="text-center">TC</th></tr></thead><tbody>';
    rows.forEach(function (r) {
        h += '<tr><td class="text-muted">' + APP.escape((r.ma_chuong_trinh ? r.ma_chuong_trinh + ' - ' : '') + (r.ten_chuong_trinh || '')) + '</td>'
           + '<td><code>' + APP.escape(r.ma_mon_hoc || '') + '</code></td>'
           + '<td>' + APP.escape(r.ten_mon_hoc || '') + '</td>'
           + '<td class="text-center">' + (r.tong_so_tiet || 0) + '</td>'
           + '<td class="text-center">' + (r.so_tin_chi || 0) + '</td></tr>';
    });
    $p.html(h + '</tbody></table>');
}

function renderLich(rows) {
    var $p = $('#paneLich');
    if (!rows.length) { $p.html('<div class="hv-empty">Chưa có lịch học</div>'); return; }
    var h = '<table class="hv-tbl"><thead><tr><th>Khóa / CTĐT</th><th>Buổi</th><th>Ngày</th><th>Giờ</th><th>Bài</th><th>GV</th><th>Phòng</th></tr></thead><tbody>';
    rows.forEach(function (r) {
        var gv = r.ten_giang_vien || r.giang_vien_ngoai || '-';
        var time = (r.gio_bat_dau || '').substring(0, 5) + ' - ' + (r.gio_ket_thuc || '').substring(0, 5);
        var ctx = '<strong>' + APP.escape(r.ma_lop || '') + '</strong>'
                + (r.ten_khoa_hoc ? '<div class="text-muted" style="font-size:11px">' + APP.escape(r.ma_khoa_hoc || '') + '</div>' : '');
        h += '<tr><td>' + ctx + '</td>'
           + '<td>#' + (r.buoi_thu || '-') + '</td>'
           + '<td>' + APP.escape(r.ngay_hoc || '-') + '</td>'
           + '<td>' + APP.escape(time) + '</td>'
           + '<td>' + APP.escape(r.tieu_de || r.ten_mon_hoc || '-') + '</td>'
           + '<td>' + APP.escape(gv) + '</td>'
           + '<td>' + APP.escape(r.phong_hoc || '-') + '</td></tr>';
    });
    $p.html(h + '</tbody></table>');
}

function renderDD(st, detail) {
    var $p = $('#paneDD');
    var h = '<div class="hv-stat-mini">'
          + '<div class="hv-stat-cell" style="border-left-color:#16a34a"><div class="num">' + (parseInt(st.co_mat,10)||0) + '</div><div class="lbl">Có mặt</div></div>'
          + '<div class="hv-stat-cell" style="border-left-color:#0891b2"><div class="num">' + (parseInt(st.muon,10)||0) + '</div><div class="lbl">Đi muộn</div></div>'
          + '<div class="hv-stat-cell" style="border-left-color:#ca8a04"><div class="num">' + (parseInt(st.vang_cp,10)||0) + '</div><div class="lbl">Vắng có phép</div></div>'
          + '<div class="hv-stat-cell" style="border-left-color:#dc2626"><div class="num">' + (parseInt(st.vang_kp,10)||0) + '</div><div class="lbl">Vắng không phép</div></div>'
          + '<div class="hv-stat-cell"><div class="num">' + (parseInt(st.tong,10)||0) + '</div><div class="lbl">Tổng buổi</div></div>'
          + '</div>';
    if (!detail.length) { h += '<div class="hv-empty">Chưa có chi tiết điểm danh</div>'; $p.html(h); return; }
    var labels = {0:['Vắng KP','#dc2626'], 1:['Có mặt','#16a34a'], 2:['Muộn','#0891b2'], 3:['Vắng CP','#ca8a04']};
    h += '<table class="hv-tbl"><thead><tr><th>Khóa / CTĐT</th><th>Ngày</th><th>Buổi</th><th>Trạng thái</th><th>Giờ vào</th></tr></thead><tbody>';
    detail.forEach(function (r) {
        var lbl = labels[parseInt(r.trang_thai,10)] || ['?', '#64748b'];
        var ctx = '<strong>' + APP.escape(r.ma_lop || '') + '</strong>'
                + (r.ma_khoa_hoc ? '<div class="text-muted" style="font-size:11px">' + APP.escape(r.ma_khoa_hoc) + '</div>' : '');
        h += '<tr><td>' + ctx + '</td>'
           + '<td>' + APP.escape(r.ngay_hoc || '-') + '</td>'
           + '<td>#' + (r.buoi_thu || '-') + '</td>'
           + '<td><span style="color:' + lbl[1] + ';font-weight:600">' + lbl[0] + '</span></td>'
           + '<td class="text-muted">' + ((r.gio_vao || '').substring(0,5) || '-') + '</td></tr>';
    });
    $p.html(h + '</tbody></table>');
}

function renderDiem(rows) {
    var $p = $('#paneDiem');
    if (!rows.length) { $p.html('<div class="hv-empty">Chưa có bảng điểm</div>'); return; }
    var h = '<table class="hv-tbl"><thead><tr><th>Khóa học</th><th>Chương trình đào tạo</th><th>TX</th><th>GK</th><th>CK</th><th>TK</th><th>Xếp loại</th><th>Đạt</th></tr></thead><tbody>';
    rows.forEach(function (r) {
        var dat = r.dat === null || r.dat === '' ? '-'
                : (parseInt(r.dat,10) === 1 ? '<span style="color:#16a34a;font-weight:600">Đạt</span>' : '<span style="color:#dc2626;font-weight:600">Chưa đạt</span>');
        var fmt = function (x) { return x !== null && x !== undefined && x !== '' ? parseFloat(x).toFixed(1) : '-'; };
        h += '<tr><td class="text-muted">' + APP.escape((r.ma_khoa_hoc ? r.ma_khoa_hoc + ' - ' : '') + (r.ten_khoa_hoc || '')) + '</td>'
           + '<td><code>' + APP.escape(r.ma_lop || '') + '</code> ' + APP.escape(r.ten_lop || '') + '</td>'
           + '<td>' + fmt(r.diem_thuong_xuyen) + '</td>'
           + '<td>' + fmt(r.diem_giua_ky) + '</td>'
           + '<td>' + fmt(r.diem_cuoi_ky) + '</td>'
           + '<td><strong>' + fmt(r.diem_tong_ket) + '</strong></td>'
           + '<td>' + APP.escape(r.xep_loai || '-') + '</td>'
           + '<td>' + dat + '</td></tr>';
    });
    $p.html(h + '</tbody></table>');
}

function renderHoSo(rows) {
    var $p = $('#paneHoSo');
    if (!rows.length) { $p.html('<div class="hv-empty">Chưa có hồ sơ nào</div>'); return; }
    var h = '<table class="hv-tbl"><thead><tr><th>Loại hồ sơ</th><th>Tên</th><th>Số hiệu</th><th>Ngày cấp</th><th>Hết hạn</th><th>File</th></tr></thead><tbody>';
    rows.forEach(function (r) {
        var hetHan = r.ngay_het_han && new Date(r.ngay_het_han) < new Date();
        var fileLink = r.duong_dan
            ? '<a href="' + APP_BASE + 'GUI/DT_HoSoHocVien/download.php?id=' + r.id + '" target="_blank" class="btn btn-sm btn-primary">Tải</a>'
            : '-';
        h += '<tr><td><span class="hv-lop-tt t1">' + APP.escape(r.loai_ho_so || '') + '</span></td>'
           + '<td>' + APP.escape(r.ten_ho_so || '') + '</td>'
           + '<td class="text-muted">' + APP.escape(r.so_hieu || '-') + '</td>'
           + '<td class="text-muted">' + (r.ngay_cap ? APP.formatDate(r.ngay_cap) : '-') + '</td>'
           + '<td class="' + (hetHan ? 'text-danger' : 'text-muted') + '">' + (r.ngay_het_han ? APP.formatDate(r.ngay_het_han) : '-') + '</td>'
           + '<td>' + fileLink + '</td></tr>';
    });
    $p.html(h + '</tbody></table>');
}

function renderCC(rows) {
    var $p = $('#paneCC');
    if (!rows.length) { $p.html('<div class="hv-empty">Chưa có chứng chỉ</div>'); return; }
    var h = '<table class="hv-tbl"><thead><tr><th>Số CC</th><th>Tên</th><th>Lớp</th><th>Xếp loại</th><th>Điểm TB</th><th>Ngày cấp</th></tr></thead><tbody>';
    rows.forEach(function (r) {
        h += '<tr><td><code>' + APP.escape(r.so_chung_chi || '') + '</code></td>'
           + '<td>' + APP.escape(r.ten_chung_chi || '') + '</td>'
           + '<td class="text-muted">' + APP.escape(r.ma_lop || '-') + '</td>'
           + '<td>' + APP.escape(r.xep_loai_tot_nghiep || '-') + '</td>'
           + '<td>' + (r.diem_trung_binh !== null && r.diem_trung_binh !== undefined ? '<strong>' + parseFloat(r.diem_trung_binh).toFixed(1) + '</strong>' : '-') + '</td>'
           + '<td class="text-muted">' + (r.ngay_cap ? APP.formatDate(r.ngay_cap) : '-') + '</td></tr>';
    });
    $p.html(h + '</tbody></table>');
}

function closeLopDrawer() {
    $('#drawerLop').removeClass('open').find('.drawer').removeClass('open');
}

function lopTtTag(t) {
    var labels = {0: 'Chờ KG', 1: 'Đang học', 2: 'Tạm hoãn', 3: 'Kết thúc'};
    return '<span class="hv-lop-tt t' + t + '">' + (labels[t] || '?') + '</span>';
}

function loadLopCuaHv() {
    APP.ajax(URL, {action: 'listLopCuaHocVien', hoc_vien_id: lopDrw.hocVienId}).done(function (res) {
        if (!res.success) { APP.toast(res.message, 'error'); return; }
        var $l = $('#lopDrwList').empty();
        var rows = res.data || [];
        if (!rows.length) { $('#lopDrwEmpty').show(); return; }
        $('#lopDrwEmpty').hide();
        rows.forEach(function (r) {
            var thoiGian = (r.ngay_bat_dau ? APP.formatDate(r.ngay_bat_dau) : '?') + ' → ' + (r.ngay_ket_thuc ? APP.formatDate(r.ngay_ket_thuc) : '?');
            $l.append(
                '<div class="hv-lop-row">' +
                    '<div class="hv-lop-info">' +
                        '<div class="hv-lop-name">' + APP.escape(r.ten_chuong_trinh || '') + '</div>' +
                        '<div class="hv-lop-code">' + APP.escape(r.ma_chuong_trinh || '') +
                            (r.ten_khoa_hoc ? ' · ' + APP.escape(r.ten_khoa_hoc) : '') + '</div>' +
                        '<div class="hv-lop-meta">' + thoiGian +
                            (r.ngay_ghi_danh ? ' · Ghi danh: ' + APP.formatDate(r.ngay_ghi_danh) : '') + '</div>' +
                    '</div>' +
                    lopTtTag(parseInt(r.lop_trang_thai, 10)) +
                    '<button type="button" class="btn btn-sm btn-danger" title="Hủy ghi danh" onclick="huyGhiDanh(' + r.id + ')">' + ICON_TRASH_SM + '</button>' +
                '</div>'
            );
        });
    });
}

function ensureKhoaCombo() {
    if (lopDrw.khoaCombo) { renderKhoaCombo(); return; }
    APP.ajax(URL, {action: 'getKhoaHocCombo'}).done(function (res) {
        if (res.success) {
            lopDrw.khoaCombo = res.data || [];
            renderKhoaCombo();
        }
    });
}

function renderKhoaCombo() {
    var $s = $('#lopDrwKhoa').empty().append('<option value="">-- Chọn khóa học --</option>');
    (lopDrw.khoaCombo || []).forEach(function (k) {
        $s.append('<option value="' + k.id + '">' + APP.escape((k.ma_khoa_hoc ? k.ma_khoa_hoc + ' - ' : '') + (k.ten_khoa_hoc || '')) + '</option>');
    });
}

// Khi chọn khóa học -> nạp các CTĐT thuộc khóa đó vào combo thứ 2
function loadChuongTrinhTheoKhoa(khoaId) {
    var $ct = $('#lopDrwSelect').empty().append('<option value="">-- Chọn chương trình --</option>').prop('disabled', true);
    if (!khoaId) return;
    APP.ajax(URL, {action: 'getChuongTrinhTheoKhoa', khoa_hoc_id: khoaId}).done(function (res) {
        if (!res.success) return;
        var rows = res.data || [];
        if (!rows.length) {
            $ct.append('<option value="" disabled>(Khóa này chưa có chương trình)</option>');
            return;
        }
        rows.forEach(function (c) {
            $ct.append('<option value="' + c.id + '">' + APP.escape((c.ma_chuong_trinh ? c.ma_chuong_trinh + ' - ' : '') + (c.ten_chuong_trinh || '')) + '</option>');
        });
        $ct.prop('disabled', false);
    });
}
$('#lopDrwKhoa').on('change', function () {
    loadChuongTrinhTheoKhoa(parseInt(this.value, 10) || 0);
});

$('#btnGhiDanh').on('click', function () {
    if (!parseInt($('#lopDrwKhoa').val(), 10)) { APP.toast('Chọn khóa học trước', 'error'); return; }
    var lopId = parseInt($('#lopDrwSelect').val(), 10);
    if (!lopId) { APP.toast('Chọn chương trình đào tạo', 'error'); return; }
    var ngay = $('#lopDrwNgay').val();
    APP.ajax(URL, {
        action: 'ghiDanhLop',
        hoc_vien_id: lopDrw.hocVienId,
        lop_hoc_id: lopId,
        ngay_ghi_danh: ngay
    }).done(function (res) {
        if (res.success) {
            APP.toast(res.message, 'success');
            $('#lopDrwKhoa').val('');
            $('#lopDrwSelect').empty().append('<option value="">-- Chọn chương trình --</option>').prop('disabled', true);
            loadLopCuaHv();
        } else APP.toast(res.message, 'error');
    });
});

function huyGhiDanh(id) {
    APP.confirm('Hủy ghi danh học viên khỏi chương trình này?', function () {
        APP.ajax(URL, {action: 'huyGhiDanh', id: id}).done(function (res) {
            if (res.success) {
                APP.toast(res.message, 'success');
                loadLopCuaHv();
            } else APP.toast(res.message, 'error');
        });
    });
}

load();
loadStats();
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
