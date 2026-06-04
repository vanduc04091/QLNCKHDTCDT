<?php
require_once __DIR__ . '/../../bootstrap.php';

Helper::requireLogin();
if (!PhanQuyenHelper::hasQuyen('NCKH_DotDangKy', PhanQuyenHelper::QUYEN_XEM)) {
    echo 'Bạn không có quyền truy cập chức năng này.'; exit;
}
$canAdd  = PhanQuyenHelper::hasQuyen('NCKH_DotDangKy', PhanQuyenHelper::QUYEN_THEM);
$canEdit = PhanQuyenHelper::hasQuyen('NCKH_DotDangKy', PhanQuyenHelper::QUYEN_SUA);
$canDel  = PhanQuyenHelper::hasQuyen('NCKH_DotDangKy', PhanQuyenHelper::QUYEN_XOA);

$pageTitle = 'Đợt đăng ký đề tài NCKH';
$activeMenu = 'NCKH_DotDangKy';
require __DIR__ . '/../layouts/header.php';
?>
<style>
.dot-drawer { position: fixed; top: 0; right: 0; width: 760px; max-width: 95vw; height: 100vh; background: #fff;
              box-shadow: -2px 0 24px rgba(0,0,0,.18); transform: translateX(100%); transition: transform .25s ease;
              z-index: 80; display: flex; flex-direction: column; }
.dot-drawer.open { transform: translateX(0); }
.dot-drawer .head { padding: 14px 18px; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center; }
.dot-drawer .body { flex: 1; overflow: auto; padding: 16px 18px; }
.phase-card { border: 1px solid var(--border); border-radius: 8px; padding: 12px 14px; margin-bottom: 10px; background: #fff; }
.phase-card .top { display: flex; justify-content: space-between; align-items: center; gap: 10px; flex-wrap: wrap; }
.phase-card.is-open { border-left: 4px solid #16a34a; background: #f0fdf4; }
.phase-card.is-future { border-left: 4px solid #94a3b8; }
.phase-card.is-past { border-left: 4px solid #cbd5e1; opacity: .75; }
.badge-phase { padding: 2px 8px; border-radius: 999px; font-size: 11px; font-weight: 600; }
.bp-submit { background:#dbeafe; color:#1e40af }
.bp-edit { background:#fef3c7; color:#92400e }
.bp-review { background:#ede9fe; color:#6d28d9 }
.bp-open { background:#dcfce7; color:#15803d }
.bp-future { background:#e2e8f0; color:#475569 }
.bp-past { background:#f1f5f9; color:#64748b }
</style>

<div class="breadcrumb">
    <a href="<?= AppConfig::baseUrl('GUI/dashboard/index.php') ?>">Trang chủ</a>
    <span class="sep">›</span> Nghiên cứu khoa học
    <span class="sep">›</span> <span>Đợt đăng ký đề tài</span>
</div>

<div class="card">
    <div class="toolbar">
        <div class="left" style="gap:8px;display:flex;flex-wrap:wrap">
            <input type="text" id="fKw" class="form-control" placeholder="Tìm tên đợt..." style="width:240px">
            <input type="number" id="fNam" class="form-control" placeholder="Năm" style="width:100px">
            <select id="fTT" class="form-select" style="width:160px">
                <option value="">Mọi trạng thái</option>
                <option value="1">Hoạt động</option>
                <option value="0">Khóa</option>
            </select>
        </div>
        <div class="right">
            <?php if ($canAdd): ?>
                <button type="button" class="btn btn-primary" onclick="openCreate()">+ Thêm đợt</button>
            <?php endif; ?>
        </div>
    </div>
    <div class="table-wrap" id="tableWrap" style="position:relative;min-height:200px">
        <table class="table">
            <thead>
                <tr>
                    <th style="width:50px" class="text-center">#</th>
                    <th>Tên đợt</th>
                    <th style="width:80px" class="text-center">Năm</th>
                    <th style="width:200px">Thời gian</th>
                    <th style="width:90px" class="text-center">Giai đoạn</th>
                    <th style="width:90px" class="text-center">Đề tài</th>
                    <th style="width:110px" class="text-center">Trạng thái</th>
                    <th style="width:170px" class="text-right">Hành động</th>
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

<!-- Modal: Đợt -->
<div class="modal-backdrop" id="modalDot">
    <div class="modal" style="max-width:640px">
        <div class="modal-header">
            <h3 id="modalTitle">Thêm đợt</h3>
            <button type="button" class="close" onclick="closeDot()">&times;</button>
        </div>
        <form id="formDot">
            <div class="modal-body">
                <input type="hidden" name="id" id="f_id">
                <div class="form-group">
                    <label>Tên đợt <span class="required">*</span></label>
                    <input type="text" name="ten_dot" id="f_ten" class="form-control" required maxlength="255">
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Năm <span class="required">*</span></label>
                        <input type="number" name="nam" id="f_nam" class="form-control" required min="2000" max="2100">
                    </div>
                    <div class="form-group">
                        <label>Trạng thái</label>
                        <select name="trang_thai" id="f_tt" class="form-select">
                            <option value="1">Hoạt động</option>
                            <option value="0">Khóa</option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Từ ngày <span class="required">*</span></label>
                        <input type="date" name="tu_ngay" id="f_tn" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Đến ngày <span class="required">*</span></label>
                        <input type="date" name="den_ngay" id="f_dn" class="form-control" required>
                    </div>
                </div>
                <div class="form-group">
                    <label>Mô tả</label>
                    <textarea name="mo_ta" id="f_mt" class="form-control" rows="3"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn" onclick="closeDot()">Hủy</button>
                <button type="submit" class="btn btn-primary">Lưu</button>
            </div>
        </form>
    </div>
</div>

<!-- Drawer: Chi tiết + Giai đoạn -->
<div class="dot-drawer" id="drawer">
    <div class="head">
        <div>
            <h3 id="drTitle" style="margin:0">Chi tiết đợt</h3>
            <div class="text-muted" id="drMeta" style="font-size:12px;margin-top:2px"></div>
        </div>
        <button type="button" class="btn" onclick="closeDrawer()">Đóng</button>
    </div>
    <div class="body">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px">
            <h4 style="margin:0">Các giai đoạn</h4>
            <?php if ($canAdd): ?>
                <button type="button" class="btn btn-primary btn-sm" onclick="openPhase(0)">+ Thêm giai đoạn</button>
            <?php endif; ?>
        </div>
        <div id="phaseList"></div>
    </div>
</div>

<!-- Modal: Giai đoạn -->
<div class="modal-backdrop" id="modalPhase">
    <div class="modal" style="max-width:640px">
        <div class="modal-header">
            <h3 id="phaseTitle">Thêm giai đoạn</h3>
            <button type="button" class="close" onclick="closePhase()">&times;</button>
        </div>
        <form id="formPhase">
            <div class="modal-body">
                <input type="hidden" name="id" id="p_id">
                <input type="hidden" name="dot_id" id="p_dot_id">
                <div class="form-group">
                    <label>Tên giai đoạn <span class="required">*</span></label>
                    <input type="text" name="ten_giai_doan" id="p_ten" class="form-control" required maxlength="255" placeholder="VD: Đợt nộp đề cương lần 1">
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Hành vi <span class="required">*</span></label>
                        <select name="hanh_vi" id="p_hv" class="form-select" required>
                            <option value="Submit">Đăng ký đề tài</option>
                            <option value="Edit">Chỉnh sửa đề tài</option>
                            <option value="Review">Duyệt đề tài</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Thứ tự</label>
                        <input type="number" name="thu_tu" id="p_thu_tu" class="form-control" value="0">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Bắt đầu <span class="required">*</span></label>
                        <input type="datetime-local" name="tu_ngay" id="p_tn" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Kết thúc <span class="required">*</span></label>
                        <input type="datetime-local" name="den_ngay" id="p_dn" class="form-control" required>
                    </div>
                </div>
                <div class="form-group">
                    <label>Ghi chú</label>
                    <textarea name="ghi_chu" id="p_gc" class="form-control" rows="2"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn" onclick="closePhase()">Hủy</button>
                <button type="submit" class="btn btn-primary">Lưu</button>
            </div>
        </form>
    </div>
</div>

<script>
var URL = APP_BASE + 'GUI/NCKH_DotDangKy/ajax_handler.php';
var CAN_EDIT = <?= $canEdit?'true':'false' ?>;
var CAN_DEL = <?= $canDel?'true':'false' ?>;
var ICON_EDIT = <?= json_encode(IconHelper::svg('edit', 18, 'icon', 'currentColor')) ?>;
var ICON_TRASH = <?= json_encode(IconHelper::svg('trash', 18, 'icon', 'currentColor')) ?>;
var ICON_EYE = <?= json_encode(IconHelper::svg('eye', 18, 'icon', 'currentColor')) ?>;

var HV_NAMES = { Submit: 'Đăng ký đề tài', Edit: 'Chỉnh sửa đề tài', Review: 'Duyệt đề tài' };
var HV_BADGE = { Submit: 'bp-submit', Edit: 'bp-edit', Review: 'bp-review' };
var state = { page: 1, pageSize: 20, kw: '', nam: 0, tt: '' };
var currentDot = null;

function load() {
    APP.showLoading('#tableWrap');
    APP.ajax(URL, {action:'getPaged', page:state.page, pageSize:state.pageSize, kw:state.kw, nam:state.nam, trang_thai:state.tt}).done(function (res) {
        APP.hideLoading('#tableWrap');
        if (!res.success) { APP.toast(res.message,'error'); return; }
        renderRows(res.data); renderInfo(res.pagination);
    });
}

function renderRows(rows) {
    var $tb = $('#tbody').empty();
    if (!rows.length) { $tb.append('<tr><td colspan="8"><div class="empty-state">Chưa có đợt nào</div></td></tr>'); return; }
    var stt = (state.page - 1) * state.pageSize;
    rows.forEach(function (r) {
        stt++;
        var tt = r.trang_thai == 1
            ? '<span class="badge badge-success">Hoạt động</span>'
            : '<span class="badge badge-danger">Khóa</span>';
        var actions =
            '<button class="btn btn-sm" title="Xem giai đoạn" onclick="openDrawer(' + r.id + ')">' + ICON_EYE + '</button>';
        if (CAN_EDIT) actions += '<button class="btn btn-sm" title="Sửa" onclick="openEdit(' + r.id + ')">' + ICON_EDIT + '</button>';
        if (CAN_DEL) actions += '<button class="btn btn-sm btn-danger" title="Xóa" onclick="trashItem(' + r.id + ')">' + ICON_TRASH + '</button>';
        $tb.append(
            '<tr>' +
                '<td class="text-center">' + stt + '</td>' +
                '<td><strong>' + APP.escape(r.ten_dot) + '</strong>' + (r.mo_ta ? '<div class="text-muted" style="font-size:12px">' + APP.escape(r.mo_ta) + '</div>' : '') + '</td>' +
                '<td class="text-center">' + r.nam + '</td>' +
                '<td>' + APP.formatDate(r.tu_ngay) + ' → ' + APP.formatDate(r.den_ngay) + '</td>' +
                '<td class="text-center">' + (r.so_giai_doan || 0) + '</td>' +
                '<td class="text-center">' + (r.so_de_tai || 0) + '</td>' +
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
$('#fKw').on('input', APP.debounce(function () { state.kw = $(this).val(); state.page=1; load(); }, 350));
$('#fNam').on('input', APP.debounce(function () { state.nam = parseInt($(this).val(),10) || 0; state.page=1; load(); }, 350));
$('#fTT').on('change', function () { state.tt = this.value; state.page=1; load(); });

function openCreate() {
    $('#modalTitle').text('Thêm đợt');
    $('#formDot')[0].reset();
    $('#f_id').val('');
    $('#f_nam').val(new Date().getFullYear());
    $('#modalDot').addClass('open');
}
function openEdit(id) {
    APP.ajax(URL, {action:'getById', id:id}).done(function (res) {
        if (!res.success) { APP.toast(res.message,'error'); return; }
        var e = res.data;
        $('#modalTitle').text('Sửa đợt');
        $('#f_id').val(e.id);
        $('#f_ten').val(e.ten_dot);
        $('#f_nam').val(e.nam);
        $('#f_tn').val(e.tu_ngay);
        $('#f_dn').val(e.den_ngay);
        $('#f_mt').val(e.mo_ta || '');
        $('#f_tt').val(e.trang_thai);
        $('#modalDot').addClass('open');
    });
}
function closeDot() { $('#modalDot').removeClass('open'); }

$('#formDot').on('submit', function (e) {
    e.preventDefault();
    var data = $(this).serializeArray();
    data.push({name:'action', value: $('#f_id').val() ? 'update' : 'insert'});
    APP.ajax(URL, data).done(function (res) {
        if (res.success) { APP.toast(res.message,'success'); closeDot(); load(); }
        else APP.toast(res.message,'error');
    });
});

function trashItem(id) {
    APP.confirm('Chuyển đợt vào thùng rác?', function () {
        APP.ajax(URL, {action:'trash', id:id}).done(function (res) { res.success ? (APP.toast(res.message,'success'), load()) : APP.toast(res.message,'error'); });
    });
}

// ===== Drawer + Phase =====
function openDrawer(id) {
    APP.ajax(URL, {action:'getById', id:id}).done(function (res) {
        if (!res.success) return;
        currentDot = res.data;
        $('#drTitle').text(currentDot.ten_dot);
        $('#drMeta').html('Năm ' + currentDot.nam + ' &nbsp;·&nbsp; ' + APP.formatDate(currentDot.tu_ngay) + ' → ' + APP.formatDate(currentDot.den_ngay));
        $('#drawer').addClass('open');
        loadPhases();
    });
}
function closeDrawer() { $('#drawer').removeClass('open'); currentDot = null; }

function loadPhases() {
    if (!currentDot) return;
    $('#phaseList').html('<div class="text-muted">Đang tải...</div>');
    APP.ajax(URL, {action:'getPhases', dot_id:currentDot.id}).done(function (res) {
        if (!res.success) { $('#phaseList').text(res.message); return; }
        renderPhases(res.data);
    });
}

function renderPhases(rows) {
    var $list = $('#phaseList').empty();
    if (!rows.length) { $list.html('<div class="empty-state">Chưa có giai đoạn nào. Thêm các giai đoạn để mở/khóa thao tác cho khoa phòng.</div>'); return; }
    var now = new Date();
    rows.forEach(function (r) {
        var tu = new Date(r.tu_ngay.replace(' ','T'));
        var dn = new Date(r.den_ngay.replace(' ','T'));
        var stateCls = '', stateText = '', stateBadge = '';
        if (now < tu)      { stateCls = 'is-future'; stateText = 'Sắp mở'; stateBadge = 'bp-future'; }
        else if (now > dn) { stateCls = 'is-past';   stateText = 'Đã đóng'; stateBadge = 'bp-past'; }
        else               { stateCls = 'is-open';   stateText = 'Đang mở'; stateBadge = 'bp-open'; }
        var actions = '';
        if (CAN_EDIT) actions += '<button class="btn btn-sm" onclick="openPhase(' + r.id + ')">' + ICON_EDIT + '</button>';
        if (CAN_DEL) actions += '<button class="btn btn-sm btn-danger" onclick="trashPhase(' + r.id + ')">' + ICON_TRASH + '</button>';
        $list.append(
            '<div class="phase-card ' + stateCls + '">' +
                '<div class="top">' +
                    '<div>' +
                        '<span class="badge-phase ' + HV_BADGE[r.hanh_vi] + '">' + (HV_NAMES[r.hanh_vi]||r.hanh_vi) + '</span>' +
                        '&nbsp;<strong>' + APP.escape(r.ten_giai_doan) + '</strong>' +
                        '&nbsp;<span class="badge-phase ' + stateBadge + '">' + stateText + '</span>' +
                    '</div>' +
                    '<div class="actions">' + actions + '</div>' +
                '</div>' +
                '<div class="text-muted" style="font-size:12px;margin-top:6px">' +
                    APP.formatDateTime(r.tu_ngay) + ' → ' + APP.formatDateTime(r.den_ngay) +
                    (r.ghi_chu ? ' &nbsp;·&nbsp; ' + APP.escape(r.ghi_chu) : '') +
                '</div>' +
            '</div>'
        );
    });
}

function openPhase(id) {
    if (!currentDot) return;
    $('#formPhase')[0].reset();
    $('#p_dot_id').val(currentDot.id);
    if (id) {
        $('#phaseTitle').text('Sửa giai đoạn');
        APP.ajax(URL, {action:'getPhaseById', id:id}).done(function (res) {
            if (!res.success) { APP.toast(res.message,'error'); return; }
            var e = res.data;
            $('#p_id').val(e.id);
            $('#p_ten').val(e.ten_giai_doan);
            $('#p_hv').val(e.hanh_vi);
            $('#p_thu_tu').val(e.thu_tu || 0);
            $('#p_tn').val((e.tu_ngay || '').replace(' ', 'T').slice(0, 16));
            $('#p_dn').val((e.den_ngay || '').replace(' ', 'T').slice(0, 16));
            $('#p_gc').val(e.ghi_chu || '');
            $('#modalPhase').addClass('open');
        });
    } else {
        $('#phaseTitle').text('Thêm giai đoạn');
        $('#p_id').val('');
        $('#modalPhase').addClass('open');
    }
}
function closePhase() { $('#modalPhase').removeClass('open'); }

$('#formPhase').on('submit', function (e) {
    e.preventDefault();
    var data = $(this).serializeArray();
    data.push({name:'action', value: $('#p_id').val() ? 'updatePhase' : 'insertPhase'});
    APP.ajax(URL, data).done(function (res) {
        if (res.success) { APP.toast(res.message,'success'); closePhase(); loadPhases(); load(); }
        else APP.toast(res.message,'error');
    });
});

function trashPhase(id) {
    APP.confirm('Xóa giai đoạn?', function () {
        APP.ajax(URL, {action:'trashPhase', id:id}).done(function (res) {
            res.success ? (APP.toast(res.message,'success'), loadPhases(), load()) : APP.toast(res.message,'error');
        });
    });
}

load();
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
