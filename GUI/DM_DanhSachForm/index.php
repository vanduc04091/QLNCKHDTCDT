<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../BUS/DM_DanhSachForm_BUS.php';

Helper::requireLogin();
if (!PhanQuyenHelper::hasQuyen('DM_DanhSachForm', PhanQuyenHelper::QUYEN_XEM)) {
    echo 'Bạn không có quyền truy cập chức năng này.'; exit;
}

$canAdd = PhanQuyenHelper::hasQuyen('DM_DanhSachForm', PhanQuyenHelper::QUYEN_THEM);
$canEdit = PhanQuyenHelper::hasQuyen('DM_DanhSachForm', PhanQuyenHelper::QUYEN_SUA);
$canDel = PhanQuyenHelper::hasQuyen('DM_DanhSachForm', PhanQuyenHelper::QUYEN_XOA);

$allForms = DM_DanhSachForm_BUS::getAll(0);

$pageTitle = 'Danh sách form';
$activeMenu = 'DM_DanhSachForm';
require __DIR__ . '/../layouts/header.php';
?>

<div class="breadcrumb">
    <a href="<?= AppConfig::baseUrl('GUI/dashboard/index.php') ?>">Trang chủ</a>
    <span class="sep">›</span> Hệ thống
    <span class="sep">›</span> <span>Danh sách form</span>
</div>

<div class="card">
    <div class="toolbar">
        <div class="left">
            <input type="text" id="search" class="form-control" placeholder="Tìm module, tên form..." style="max-width:340px">
            <select id="filterDaXoa" class="form-select" style="max-width:160px">
                <option value="0">Đang hoạt động</option>
                <option value="1">Thùng rác</option>
            </select>
        </div>
        <div class="right">
            <?php if ($canAdd): ?>
                <button type="button" class="btn btn-primary" onclick="openCreate()">+ Thêm form</button>
            <?php endif; ?>
        </div>
    </div>
    <div class="table-wrap" id="tableWrap" style="position:relative;min-height:200px">
        <table class="table">
            <thead>
                <tr>
                    <th style="width:50px" class="text-center">#</th>
                    <th style="width:220px">Module</th>
                    <th>Tên form</th>
                    <th>Form cha</th>
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

<div class="modal-backdrop" id="modalForm">
    <div class="modal">
        <div class="modal-header">
            <h3 id="modalTitle">Thêm form</h3>
            <button type="button" class="close" onclick="closeModal()">&times;</button>
        </div>
        <form id="formF">
            <div class="modal-body">
                <input type="hidden" name="id" id="f_id">
                <div class="form-group">
                    <label>Module (key) <span class="required">*</span></label>
                    <input type="text" name="modules_tuong_ung" id="f_module" class="form-control" required maxlength="100" placeholder="VD: DM_NhanVien">
                    <small class="text-muted">Key duy nhất dùng trong code để check quyền</small>
                </div>
                <div class="form-group">
                    <label>Tên form <span class="required">*</span></label>
                    <input type="text" name="ten_form" id="f_ten_form" class="form-control" required maxlength="200">
                </div>
                <div class="form-group">
                    <label>Form cha</label>
                    <select name="form_cha_id" id="f_form_cha_id" class="form-select">
                        <option value="0">-- Không có (Root) --</option>
                        <?php foreach ($allForms as $f): ?>
                            <option value="<?= $f['id'] ?>"><?= Helper::h($f['ten_form']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn" onclick="closeModal()">Hủy</button>
                <button type="submit" class="btn btn-primary">Lưu</button>
            </div>
        </form>
    </div>
</div>

<script>
var URL = APP_BASE + 'GUI/DM_DanhSachForm/ajax_handler.php';
var CAN_EDIT = <?= $canEdit?'true':'false' ?>;
var CAN_DEL = <?= $canDel?'true':'false' ?>;
var FORM_MAP = {};
<?php foreach ($allForms as $f): ?>
FORM_MAP[<?= (int)$f['id'] ?>] = <?= json_encode($f['ten_form']) ?>;
<?php endforeach; ?>
var state = { page: 1, pageSize: 20, search: '', daXoa: 0 };

function load() {
    APP.showLoading('#tableWrap');
    APP.ajax(URL, {
        action: 'getPaged',
        page: state.page, pageSize: state.pageSize,
        search: state.search, da_xoa: state.daXoa
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
        $tb.append('<tr><td colspan="5"><div class="empty-state"><div class="icon"><svg class="icon icon-empty" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="8"></circle><path d="m21 21-4.35-4.35"></path></svg></div>Không có dữ liệu</div></td></tr>');
        return;
    }
    var stt = (state.page - 1) * state.pageSize;
    rows.forEach(function (r) {
        stt++;
        var chaName = r.ten_form_cha || (r.form_cha_id ? FORM_MAP[r.form_cha_id] : '') || '-';
        var actions = '';
        if (state.daXoa == 0) {
            if (CAN_EDIT) actions += '<button class="btn btn-sm" title="Sửa" onclick="openEdit(' + r.id + ')"><svg class="icon icon-edit" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg></button>';
            if (CAN_DEL) actions += '<button class="btn btn-sm btn-danger" title="Xóa" onclick="trashItem(' + r.id + ')"><svg class="icon icon-trash" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg></button>';
        } else {
            if (CAN_EDIT) actions += '<button class="btn btn-sm btn-success" onclick="restoreItem(' + r.id + ')">↺ Khôi phục</button>';
            if (CAN_DEL) actions += '<button class="btn btn-sm btn-danger" onclick="deleteItem(' + r.id + ')">Xóa</button>';
        }
        $tb.append(
            '<tr>' +
                '<td class="text-center">' + stt + '</td>' +
                '<td><code>' + APP.escape(r.modules_tuong_ung) + '</code></td>' +
                '<td>' + APP.escape(r.ten_form) + '</td>' +
                '<td>' + APP.escape(chaName) + '</td>' +
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

$('#search').on('input', APP.debounce(function () {
    state.search = $(this).val(); state.page = 1; load();
}, 400));

$('#filterDaXoa').on('change', function () { state.daXoa = parseInt(this.value, 10) || 0; state.page = 1; load(); });

function openCreate() {
    $('#modalTitle').text('Thêm form');
    $('#formF')[0].reset();
    $('#f_id').val(''); $('#f_form_cha_id').val('0');
    $('#modalForm').addClass('open');
}
function openEdit(id) {
    APP.ajax(URL, {action: 'getById', id: id}).done(function (res) {
        if (!res.success) { APP.toast(res.message, 'error'); return; }
        var e = res.data;
        $('#modalTitle').text('Sửa form');
        $('#f_id').val(e.id);
        $('#f_module').val(e.modules_tuong_ung);
        $('#f_ten_form').val(e.ten_form);
        $('#f_form_cha_id').val(e.form_cha_id || 0);
        $('#modalForm').addClass('open');
    });
}
function closeModal() { $('#modalForm').removeClass('open'); }

$('#formF').on('submit', function (e) {
    e.preventDefault();
    var data = $(this).serializeArray();
    data.push({name: 'action', value: $('#f_id').val() ? 'update' : 'insert'});
    APP.ajax(URL, data).done(function (res) {
        if (res.success) { APP.toast(res.message, 'success'); closeModal(); load(); }
        else APP.toast(res.message, 'error');
    });
});

function trashItem(id) {
    APP.confirm('Chuyển form này vào thùng rác?', function () {
        APP.ajax(URL, {action: 'trash', id: id}).done(function (res) {
            res.success ? (APP.toast(res.message, 'success'), load()) : APP.toast(res.message, 'error');
        });
    });
}
function restoreItem(id) {
    APP.ajax(URL, {action: 'restore', id: id}).done(function (res) {
        res.success ? (APP.toast(res.message, 'success'), load()) : APP.toast(res.message, 'error');
    });
}
function deleteItem(id) {
    APP.confirm('Xóa VĨNH VIỄN form này?', function () {
        APP.ajax(URL, {action: 'delete', id: id}).done(function (res) {
            res.success ? (APP.toast(res.message, 'success'), load()) : APP.toast(res.message, 'error');
        });
    }, {yesText: 'Xóa vĩnh viễn'});
}

load();
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
