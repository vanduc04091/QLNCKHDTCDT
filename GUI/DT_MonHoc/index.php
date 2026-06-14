<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../BUS/DT_ChuongTrinh_BUS.php';
require_once __DIR__ . '/../../BUS/DT_KhoaHoc_BUS.php';

Helper::requireLogin();
if (!PhanQuyenHelper::hasQuyen('DT_MonHoc', PhanQuyenHelper::QUYEN_XEM)) {
    echo 'Bạn không có quyền truy cập chức năng này.'; exit;
}

$ctCombo = DT_ChuongTrinh_BUS::getCombo();
$khoaList = DT_KhoaHoc_BUS::getCombo();

$canAdd = PhanQuyenHelper::hasQuyen('DT_MonHoc', PhanQuyenHelper::QUYEN_THEM);
$canEdit = PhanQuyenHelper::hasQuyen('DT_MonHoc', PhanQuyenHelper::QUYEN_SUA);
$canDel = PhanQuyenHelper::hasQuyen('DT_MonHoc', PhanQuyenHelper::QUYEN_XOA);

$pageTitle = 'Quản lý bài học';
$activeMenu = 'DT_MonHoc';
require __DIR__ . '/../layouts/header.php';
?>

<div class="breadcrumb">
    <a href="<?= AppConfig::baseUrl('GUI/dashboard/index.php') ?>">Trang chủ</a>
    <span class="sep">›</span> Đào tạo
    <span class="sep">›</span> <span>Bài học</span>
</div>

<!-- STATS -->
<div class="stats-row" id="statsRow">
    <div class="stat-card">
        <div class="stat-icon"><?= IconHelper::svg('dashboard', '22') ?></div>
        <div>
            <div class="stat-label">Tổng bài học</div>
            <div class="stat-value" id="stTotal">—</div>
        </div>
    </div>
    <div class="stat-card success">
        <div class="stat-icon"><?= IconHelper::svg('check-circle', '22') ?></div>
        <div>
            <div class="stat-label">Đang hoạt động</div>
            <div class="stat-value" id="stActive">—</div>
        </div>
    </div>
    <div class="stat-card info">
        <div class="stat-icon"><?= IconHelper::svg('users', '22') ?></div>
        <div>
            <div class="stat-label">Đã dùng trong khóa học</div>
            <div class="stat-value" id="stInKhoa">—</div>
        </div>
    </div>
    <div class="stat-card warning">
        <div class="stat-icon"><?= IconHelper::svg('trash', '22') ?></div>
        <div>
            <div class="stat-label">Trong thùng rác</div>
            <div class="stat-value" id="stTrash">—</div>
        </div>
    </div>
</div>

<div class="card">
    <div class="toolbar">
        <div class="left">
            <div style="position:relative">
                <input type="text" id="search" class="form-control" placeholder="Tìm mã, tên bài học..." style="max-width:300px;padding-left:34px" aria-label="Tìm bài học">
                <span style="position:absolute;left:10px;top:50%;transform:translateY(-50%);color:var(--gray-400)"><?= IconHelper::svg('search', '16') ?></span>
            </div>
            <select id="filterTrangThai" class="form-select" style="max-width:160px" aria-label="Lọc trạng thái">
                <option value="-1">Tất cả trạng thái</option>
                <option value="1">Hoạt động</option>
                <option value="0">Khóa</option>
            </select>
            <select id="filterKhoa" class="form-select" style="max-width:220px" aria-label="Lọc theo khóa học">
                <option value="0">-- Chọn khóa học --</option>
                <?php foreach ($khoaList as $k): ?>
                    <option value="<?= $k['id'] ?>"><?= Helper::h(($k['ma_khoa_hoc'] ? $k['ma_khoa_hoc'].' - ' : '').$k['ten_khoa_hoc']) ?></option>
                <?php endforeach; ?>
            </select>
            <select id="filterChuongTrinh" class="form-select" style="max-width:240px" aria-label="Lọc theo chương trình" disabled>
                <option value="0">-- Chọn chương trình --</option>
            </select>
            <select id="filterDaXoa" class="form-select" style="max-width:150px" aria-label="Lọc thùng rác">
                <option value="0">Đang hoạt động</option>
                <option value="1">Thùng rác</option>
            </select>
        </div>
        <div class="right">
            <?php if ($canAdd): ?>
                <button type="button" class="btn btn-primary" onclick="openCreate()" aria-label="Thêm bài học mới">
                    <?= IconHelper::svg('plus', '16') ?> Thêm bài học
                </button>
            <?php endif; ?>
        </div>
    </div>
    <div class="table-wrap" id="tableWrap" style="position:relative;min-height:220px">
        <table class="table">
            <thead>
                <tr>
                    <th style="width:50px" class="text-center">#</th>
                    <th style="width:60px" class="text-center">TT</th>
                    <th style="width:120px">Mã bài</th>
                    <th>Tên bài học</th>
                    <th>Chương trình</th>
                    <th class="text-center" style="width:80px">LT</th>
                    <th class="text-center" style="width:80px">TH</th>
                    <th class="text-center" style="width:80px">Tổng</th>
                    <th class="text-center" style="width:70px">TC</th>
                    <th class="text-center" style="width:120px">Trạng thái</th>
                    <th style="width:130px" class="text-right">Hành động</th>
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

<!-- MODAL FORM -->
<div class="modal-backdrop" id="modalForm" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
    <div class="modal modal-lg">
        <div class="modal-header">
            <h3 id="modalTitle">Thêm bài học</h3>
            <button type="button" class="close" onclick="closeModal()" aria-label="Đóng">&times;</button>
        </div>
        <form id="formMain">
            <div class="modal-body">
                <input type="hidden" name="id" id="f_id">
                <div class="form-row">
                    <div class="form-group">
                        <label for="f_ma">Mã bài học <span class="required">*</span></label>
                        <input type="text" name="ma_mon_hoc" id="f_ma" class="form-control" required maxlength="50" autocomplete="off">
                        <div class="form-error" id="err_ma"></div>
                    </div>
                    <div class="form-group">
                        <label for="f_trang_thai">Trạng thái</label>
                        <select name="trang_thai" id="f_trang_thai" class="form-select">
                            <option value="1">Hoạt động</option>
                            <option value="0">Khóa</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="f_ten">Tên bài học <span class="required">*</span></label>
                    <input type="text" name="ten_mon_hoc" id="f_ten" class="form-control" required maxlength="200" autocomplete="off">
                </div>
                <div class="form-row-3">
                    <div class="form-group">
                        <label for="f_slt">Số tiết lý thuyết</label>
                        <input type="number" name="so_tiet_ly_thuyet" id="f_slt" class="form-control" value="0" min="0" inputmode="numeric">
                    </div>
                    <div class="form-group">
                        <label for="f_sth">Số tiết thực hành</label>
                        <input type="number" name="so_tiet_thuc_hanh" id="f_sth" class="form-control" value="0" min="0" inputmode="numeric">
                    </div>
                    <div class="form-group">
                        <label for="f_tst">Tổng số tiết</label>
                        <input type="number" id="f_tst" class="form-control" value="0" readonly aria-describedby="f_tst_help">
                        <div class="form-error" id="f_tst_help" style="display:block;color:var(--gray-500)">Tự tính từ lý thuyết + thực hành</div>
                    </div>
                </div>
                <div class="form-row-3">
                    <div class="form-group">
                        <label for="f_stc">Số tín chỉ</label>
                        <input type="number" step="0.5" name="so_tin_chi" id="f_stc" class="form-control" value="0" min="0" inputmode="decimal">
                    </div>
                    <div class="form-group">
                        <label for="f_thu_tu">Thứ tự</label>
                        <input type="number" name="thu_tu" id="f_thu_tu" class="form-control" value="0" min="0" inputmode="numeric">
                        <div class="form-error" style="display:block;color:var(--gray-500)">Để 0 sẽ tự xếp cuối chương trình</div>
                    </div>
                    <div class="form-group">
                        <label for="f_chuong_trinh">Thuộc chương trình đào tạo</label>
                        <select name="chuong_trinh_id" id="f_chuong_trinh" class="form-select">
                            <option value="">-- Không thuộc chương trình --</option>
                            <?php foreach ($ctCombo as $ct): ?>
                                <option value="<?= $ct['id'] ?>"><?= Helper::h($ct['ma_chuong_trinh'] . ' - ' . $ct['ten_chuong_trinh']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="f_mo_ta">Mô tả</label>
                    <textarea name="mo_ta" id="f_mo_ta" class="form-control" rows="3" placeholder="Nội dung, phạm vi, ghi chú bài học..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn" onclick="closeModal()">Hủy</button>
                <button type="submit" class="btn btn-primary" id="btnSave">
                    <?= IconHelper::svg('save', '16') ?> Lưu
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Drawer: Khóa học gắn với môn này -->
<div class="drawer-backdrop" id="drawerKhoa">
    <div class="drawer">
        <div class="drawer-header">
            <div>
                <h3 id="drwTitle" style="margin:0">Khóa học gắn với bài</h3>
                <div id="drwSub" class="text-muted" style="font-size:12.5px;margin-top:2px"></div>
            </div>
            <button type="button" class="close" onclick="closeKhoaDrawer()">&times;</button>
        </div>
        <div class="drawer-body">
            <!-- Form thêm môn vào khóa -->
            <div style="background:#f8fafc;padding:12px;border-radius:8px;margin-bottom:14px;border:1px solid var(--gray-200)">
                <div style="font-weight:600;margin-bottom:8px;font-size:13.5px">Thêm bài này vào khóa học khác</div>
                <div style="display:flex;gap:8px;flex-wrap:wrap;align-items:center">
                    <select id="drwKhoaSelect" class="form-select" style="flex:1;min-width:200px"></select>
                    <label style="display:inline-flex;gap:6px;align-items:center;font-size:13px;white-space:nowrap">
                        <input type="checkbox" id="drwBatBuoc" checked> Bắt buộc
                    </label>
                    <button type="button" class="btn btn-primary btn-sm" id="btnAddToKhoa">+ Thêm</button>
                </div>
            </div>

            <div id="drwList" style="display:flex;flex-direction:column;gap:8px"></div>
            <div id="drwEmpty" style="display:none;text-align:center;color:var(--gray-500);padding:30px 16px;font-size:13px">
                Bài này chưa gắn với khóa học nào.
            </div>
        </div>
    </div>
</div>

<style>
    .btn-link-chip { border:0; cursor:pointer; }
    .btn-link-chip:hover { filter: brightness(0.95); }
    .mh-khoa-row { display:flex; align-items:center; gap:10px; padding:10px 12px; border:1px solid var(--gray-200); border-radius:8px; background:#fff; }
    .mh-khoa-row:hover { border-color: var(--primary); }
    .mh-khoa-info { flex:1; min-width:0; }
    .mh-khoa-name { font-weight:600; color: var(--gray-800); }
    .mh-khoa-code { font-family: monospace; font-size:11.5px; color: var(--gray-500); margin-top:2px; }
    .mh-khoa-tag { font-size:11px; padding:2px 8px; border-radius:10px; font-weight:600; }
    .mh-khoa-tag.bb { background:#fef3c7; color:#92400e; }
    .mh-khoa-tag.tc { background:#e0f2fe; color:#075985; }
</style>

<script>
var URL = APP_BASE + 'GUI/DT_MonHoc/ajax_handler.php';
var CAN_EDIT = <?= $canEdit ? 'true' : 'false' ?>;
var CAN_DEL = <?= $canDel ? 'true' : 'false' ?>;
var state = { page: 1, pageSize: 20, search: '', daXoa: 0, trangThai: -1, chuongTrinhId: 0 };

var ICON_EDIT = '<?= addslashes(IconHelper::svg('edit', '14')) ?>';
var ICON_TRASH = '<?= addslashes(IconHelper::svg('trash', '14')) ?>';
var ICON_RESTORE = '<?= addslashes(IconHelper::svg('refresh', '14')) ?>';
var ICON_EMPTY = '<?= addslashes(IconHelper::svg('dashboard', '28')) ?>';
var ICON_BOOK_OPEN = '<?= addslashes(IconHelper::svg('book-open', '14')) ?>';
var ICON_PLUS = '<?= addslashes(IconHelper::svg('plus', '14')) ?>';

function loadStats() {
    APP.ajax(URL, {action: 'getStats'}).done(function (res) {
        if (!res.success) return;
        $('#stTotal').text(res.data.total);
        $('#stActive').text(res.data.active);
        $('#stInKhoa').text(res.data.in_khoa);
        $('#stTrash').text(res.data.trash);
    });
}

function load() {
    APP.showLoading('#tableWrap');
    APP.ajax(URL, {
        action: 'getPaged', page: state.page, pageSize: state.pageSize,
        search: state.search, da_xoa: state.daXoa, trang_thai: state.trangThai,
        chuong_trinh_id: state.chuongTrinhId
    }).done(function (res) {
        APP.hideLoading('#tableWrap');
        if (!res.success) { APP.toast(res.message, 'error'); return; }
        renderRows(res.data);
        renderInfo(res.pagination);
    });
}

function renderRows(rows) {
    var $tb = $('#tbody').empty();
    if (!rows.length) {
        $tb.append(
            '<tr><td colspan="11">' +
                '<div class="empty-state-pro">' +
                    '<div class="empty-icon">' + ICON_EMPTY + '</div>' +
                    '<h4>Chưa có bài học nào</h4>' +
                    '<p>' + (state.daXoa == 1 ? 'Thùng rác trống.' : (state.search ? 'Không khớp từ khóa "' + APP.escape(state.search) + '".' : 'Bắt đầu bằng cách thêm bài học đầu tiên.')) + '</p>' +
                    (state.daXoa == 0 && CAN_EDIT ? '<button class="btn btn-primary" onclick="openCreate()">+ Thêm bài học</button>' : '') +
                '</div>' +
            '</td></tr>'
        );
        return;
    }
    var stt = (state.page - 1) * state.pageSize;
    rows.forEach(function (r) {
        stt++;
        var tt = r.trang_thai == 1
            ? '<span class="chip chip-success"><span class="dot"></span>Hoạt động</span>'
            : '<span class="chip chip-muted"><span class="dot"></span>Khóa</span>';
        var ctTxt = r.ten_chuong_trinh
            ? '<span class="chip chip-primary" title="' + APP.escape(r.ma_chuong_trinh || '') + '">' + APP.escape(r.ten_chuong_trinh) + '</span>'
            : '<span class="text-muted" style="font-size:12px">—</span>';
        var actions = '';
        if (state.daXoa == 0) {
            if (CAN_EDIT) actions += '<button class="btn btn-sm" title="Sửa" aria-label="Sửa bài học" onclick="openEdit(' + r.id + ')">' + ICON_EDIT + '</button>';
            if (CAN_DEL) actions += '<button class="btn btn-sm btn-danger" title="Chuyển thùng rác" aria-label="Chuyển vào thùng rác" onclick="trashItem(' + r.id + ')">' + ICON_TRASH + '</button>';
        } else {
            if (CAN_EDIT) actions += '<button class="btn btn-sm btn-success" onclick="restoreItem(' + r.id + ')">' + ICON_RESTORE + ' Khôi phục</button>';
            if (CAN_DEL) actions += '<button class="btn btn-sm btn-danger" onclick="deleteItem(' + r.id + ')">Xóa</button>';
        }
        $tb.append(
            '<tr>' +
                '<td class="text-center text-muted">' + stt + '</td>' +
                '<td class="text-center" style="font-variant-numeric:tabular-nums;font-weight:600">' + (r.thu_tu || 0) + '</td>' +
                '<td><strong style="color:var(--gray-900)">' + APP.escape(r.ma_mon_hoc) + '</strong></td>' +
                '<td>' + APP.escape(r.ten_mon_hoc) +
                    (r.mo_ta ? '<div class="text-muted" style="font-size:12px;margin-top:2px">' + APP.escape((r.mo_ta || '').substring(0, 80)) + (r.mo_ta.length > 80 ? '…' : '') + '</div>' : '') +
                '</td>' +
                '<td>' + ctTxt + '</td>' +
                '<td class="text-center" style="font-variant-numeric:tabular-nums">' + (r.so_tiet_ly_thuyet || 0) + '</td>' +
                '<td class="text-center" style="font-variant-numeric:tabular-nums">' + (r.so_tiet_thuc_hanh || 0) + '</td>' +
                '<td class="text-center" style="font-variant-numeric:tabular-nums;font-weight:600">' + (r.tong_so_tiet || 0) + '</td>' +
                '<td class="text-center" style="font-variant-numeric:tabular-nums">' + (r.so_tin_chi || 0) + '</td>' +
                '<td class="text-center">' + tt + '</td>' +
                '<td><div class="actions">' + actions + '</div></td>' +
            '</tr>'
        );
    });
}

function renderInfo(p) {
    var from = (p.currentPage - 1) * p.pageSize + 1;
    var to = Math.min(from + p.pageSize - 1, p.totalRecords);
    $('#pageInfo').text(p.totalRecords ? 'Hiển thị ' + from + '–' + to + ' / ' + p.totalRecords : 'Không có bản ghi');
    $('#pageNav').html(APP.renderPagination(p));
}

$('#pageNav').on('click', 'button[data-p]', function () {
    var p = parseInt($(this).data('p'), 10);
    if (!p || p === state.page) return;
    state.page = p; load();
});

$('#search').on('input', APP.debounce(function () {
    state.search = $(this).val(); state.page = 1; load();
}, 400));

$('#filterTrangThai').on('change', function () { state.trangThai = parseInt(this.value, 10); state.page = 1; load(); });
$('#filterKhoa').on('change', function () {
    var kh = parseInt(this.value, 10) || 0;
    var $ct = $('#filterChuongTrinh').empty().append('<option value="0">-- Chọn chương trình --</option>').prop('disabled', true);
    state.chuongTrinhId = 0; state.page = 1; load();
    if (!kh) return;
    APP.ajax(URL, {action: 'getChuongTrinhTheoKhoa', khoa_hoc_id: kh}).done(function (res) {
        if (!res.success) return;
        var rows = res.data || [];
        if (!rows.length) { $ct.append('<option value="" disabled>(Khóa này chưa có chương trình)</option>'); return; }
        rows.forEach(function (c) { $ct.append('<option value="'+c.chuong_trinh_id+'">'+APP.escape((c.ma_chuong_trinh?c.ma_chuong_trinh+' - ':'')+(c.ten_chuong_trinh||''))+'</option>'); });
        $ct.prop('disabled', false);
    });
});
$('#filterChuongTrinh').on('change', function () { state.chuongTrinhId = parseInt(this.value, 10) || 0; state.page = 1; load(); });
$('#filterDaXoa').on('change', function () { state.daXoa = parseInt(this.value, 10) || 0; state.page = 1; load(); });

function recalcTongTiet() {
    var slt = parseInt($('#f_slt').val(), 10) || 0;
    var sth = parseInt($('#f_sth').val(), 10) || 0;
    $('#f_tst').val(slt + sth);
}
$('#f_slt, #f_sth').on('input', recalcTongTiet);

function openCreate() {
    $('#modalTitle').text('Thêm bài học');
    $('#formMain')[0].reset(); $('#f_id').val(''); $('#f_tst').val(0);
    $('#modalForm').addClass('open');
    setTimeout(function () { $('#f_ma').trigger('focus'); }, 50);
}

function openEdit(id) {
    APP.ajax(URL, {action: 'getById', id: id}).done(function (res) {
        if (!res.success) { APP.toast(res.message, 'error'); return; }
        var e = res.data;
        $('#modalTitle').text('Sửa bài học');
        $('#f_id').val(e.id);
        $('#f_ma').val(e.ma_mon_hoc);
        $('#f_ten').val(e.ten_mon_hoc);
        $('#f_slt').val(e.so_tiet_ly_thuyet || 0);
        $('#f_sth').val(e.so_tiet_thuc_hanh || 0);
        $('#f_stc').val(e.so_tin_chi || 0);
        $('#f_thu_tu').val(e.thu_tu || 0);
        $('#f_chuong_trinh').val(e.chuong_trinh_id || '');
        $('#f_mo_ta').val(e.mo_ta || '');
        $('#f_trang_thai').val(e.trang_thai);
        recalcTongTiet();
        $('#modalForm').addClass('open');
        setTimeout(function () { $('#f_ten').trigger('focus'); }, 50);
    });
}

function closeModal() { $('#modalForm').removeClass('open'); }

// ESC to close modal
$(document).on('keydown', function (e) {
    if (e.key === 'Escape' && $('#modalForm').hasClass('open')) closeModal();
});

$('#formMain').on('submit', function (e) {
    e.preventDefault();
    var $btn = $('#btnSave').prop('disabled', true);
    var data = $(this).serializeArray();
    data.push({name: 'action', value: $('#f_id').val() ? 'update' : 'insert'});
    APP.ajax(URL, data).done(function (res) {
        $btn.prop('disabled', false);
        if (res.success) { APP.toast(res.message, 'success'); closeModal(); load(); loadStats(); }
        else APP.toast(res.message, 'error');
    }).fail(function () { $btn.prop('disabled', false); });
});

function trashItem(id) {
    APP.confirm('Chuyển bài học này vào thùng rác?', function () {
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
    APP.confirm('Xóa VĨNH VIỄN bài học này? Hành động không thể hoàn tác.', function () {
        APP.ajax(URL, {action: 'delete', id: id}).done(function (res) {
            res.success ? (APP.toast(res.message, 'success'), load(), loadStats()) : APP.toast(res.message, 'error');
        });
    }, {yesText: 'Xóa vĩnh viễn'});
}

// ====== Drawer: khóa học gắn với môn này ======
var drwState = { monHocId: 0, monHocTen: '', khoaCombo: null };

function openKhoaDrawer(monHocId, monHocTen) {
    drwState.monHocId = monHocId;
    drwState.monHocTen = monHocTen;
    $('#drwTitle').text('Khóa học gắn với bài');
    $('#drwSub').text(monHocTen);
    $('#drwList').html('');
    $('#drwEmpty').hide();
    $('#drawerKhoa').addClass('open').find('.drawer').addClass('open');
    loadKhoaCuaMon();
    ensureKhoaCombo();
}

function closeKhoaDrawer() {
    $('#drawerKhoa').removeClass('open').find('.drawer').removeClass('open');
}

function loadKhoaCuaMon() {
    APP.ajax(URL, {action: 'listKhoaCuaMon', mon_hoc_id: drwState.monHocId}).done(function (res) {
        if (!res.success) { APP.toast(res.message, 'error'); return; }
        var $l = $('#drwList').empty();
        var rows = res.data || [];
        if (!rows.length) { $('#drwEmpty').show(); return; }
        $('#drwEmpty').hide();
        rows.forEach(function (r) {
            $l.append(
                '<div class="mh-khoa-row">' +
                    '<div class="mh-khoa-info">' +
                        '<div class="mh-khoa-name">' + APP.escape(r.ten_chuong_trinh || '') + '</div>' +
                        '<div class="mh-khoa-code">' + APP.escape(r.ma_chuong_trinh || '') + ' · #' + r.thu_tu + '</div>' +
                    '</div>' +
                    '<span class="mh-khoa-tag ' + (parseInt(r.bat_buoc, 10) === 1 ? 'bb' : 'tc') + '">' +
                        (parseInt(r.bat_buoc, 10) === 1 ? 'Bắt buộc' : 'Tự chọn') + '</span>' +
                    '<button type="button" class="btn btn-sm btn-danger" title="Gỡ khỏi khóa" onclick="removeFromKhoa(' + r.id + ')">' + ICON_TRASH + '</button>' +
                '</div>'
            );
        });
    });
}

function ensureKhoaCombo() {
    if (drwState.khoaCombo) { renderKhoaCombo(); return; }
    APP.ajax(URL, {action: 'getKhoaCombo'}).done(function (res) {
        if (res.success) {
            drwState.khoaCombo = res.data || [];
            renderKhoaCombo();
        }
    });
}
function renderKhoaCombo() {
    var $s = $('#drwKhoaSelect').empty().append('<option value="">-- Chọn chương trình --</option>');
    (drwState.khoaCombo || []).forEach(function (k) {
        $s.append('<option value="' + k.id + '">' + APP.escape(k.ma_chuong_trinh + ' - ' + k.ten_chuong_trinh) + '</option>');
    });
}

$('#btnAddToKhoa').on('click', function () {
    var khoaId = parseInt($('#drwKhoaSelect').val(), 10);
    if (!khoaId) { APP.toast('Chọn chương trình', 'error'); return; }
    var bb = $('#drwBatBuoc').is(':checked') ? 1 : 0;
    APP.ajax(URL, {
        action: 'addMonToKhoa',
        mon_hoc_id: drwState.monHocId,
        chuong_trinh_id: khoaId,
        bat_buoc: bb
    }).done(function (res) {
        if (res.success) {
            APP.toast(res.message, 'success');
            $('#drwKhoaSelect').val('');
            loadKhoaCuaMon();
            load();  // refresh chip "X khóa" trong table
        } else APP.toast(res.message, 'error');
    });
});

function removeFromKhoa(id) {
    APP.confirm('Gỡ bài này khỏi khóa học?', function () {
        APP.ajax(URL, {action: 'removeMonKhoiKhoa', id: id}).done(function (res) {
            if (res.success) {
                APP.toast(res.message, 'success');
                loadKhoaCuaMon();
                load();
            } else APP.toast(res.message, 'error');
        });
    });
}

loadStats();
load();
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
