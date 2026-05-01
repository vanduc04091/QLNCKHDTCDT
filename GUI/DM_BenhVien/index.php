<?php
require_once __DIR__ . '/../../bootstrap.php';

Helper::requireLogin();
if (!PhanQuyenHelper::hasQuyen('DM_BenhVien', PhanQuyenHelper::QUYEN_XEM)) {
    echo 'Bạn không có quyền truy cập chức năng này.'; exit;
}

$canAdd = PhanQuyenHelper::hasQuyen('DM_BenhVien', PhanQuyenHelper::QUYEN_THEM);
$canEdit = PhanQuyenHelper::hasQuyen('DM_BenhVien', PhanQuyenHelper::QUYEN_SUA);
$canDel = PhanQuyenHelper::hasQuyen('DM_BenhVien', PhanQuyenHelper::QUYEN_XOA);

$capList = Constants::getCapBenhVienList();

$pageTitle = 'Quản lý bệnh viện';
$activeMenu = 'DM_BenhVien';
require __DIR__ . '/../layouts/header.php';
?>

<div class="breadcrumb">
    <a href="<?= AppConfig::baseUrl('GUI/dashboard/index.php') ?>">Trang chủ</a>
    <span class="sep">›</span> Danh mục
    <span class="sep">›</span> <span>Bệnh viện</span>
</div>

<div class="card">
    <div class="toolbar">
        <div class="left">
            <input type="text" id="search" class="form-control" placeholder="Tìm mã, tên BV, địa chỉ..." style="max-width:320px">
            <select id="filterCap" class="form-select" style="max-width:180px">
                <option value="">-- Tất cả tuyến --</option>
                <?php foreach ($capList as $k => $v): ?>
                    <option value="<?= Helper::h($k) ?>"><?= Helper::h($v) ?></option>
                <?php endforeach; ?>
            </select>
            <select id="filterDaXoa" class="form-select" style="max-width:160px">
                <option value="0">Đang hoạt động</option>
                <option value="1">Thùng rác</option>
            </select>
        </div>
        <div class="right">
            <?php if ($canAdd): ?>
                <button type="button" class="btn btn-primary" onclick="openCreate()">+ Thêm bệnh viện</button>
            <?php endif; ?>
        </div>
    </div>
    <div class="table-wrap" id="tableWrap" style="position:relative;min-height:200px">
        <table class="table">
            <thead>
                <tr>
                    <th style="width:50px" class="text-center">#</th>
                    <th style="width:120px">Mã</th>
                    <th>Tên bệnh viện</th>
                    <th class="text-center" style="width:130px">Tuyến</th>
                    <th>Địa chỉ</th>
                    <th>Giám đốc</th>
                    <th class="text-center" style="width:80px">Nhân viên</th>
                    <th class="text-center" style="width:90px">Trạng thái</th>
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

<div class="modal-backdrop" id="modalForm">
    <div class="modal" style="max-width:820px">
        <div class="modal-header">
            <h3 id="modalTitle">Thêm bệnh viện</h3>
            <button type="button" class="close" onclick="closeModal()">&times;</button>
        </div>
        <form id="formBV">
            <div class="modal-body">
                <input type="hidden" name="id" id="f_id">
                <div class="form-row">
                    <div class="form-group">
                        <label>Mã BV <span class="required">*</span></label>
                        <input type="text" name="ma_benh_vien" id="f_ma" class="form-control" required maxlength="50">
                    </div>
                    <div class="form-group">
                        <label>Tên bệnh viện <span class="required">*</span></label>
                        <input type="text" name="ten_benh_vien" id="f_ten" class="form-control" required maxlength="300">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Cấp / Tuyến <span class="required">*</span></label>
                        <select name="cap_benh_vien" id="f_cap" class="form-select" required>
                            <?php foreach ($capList as $k => $v): ?>
                                <option value="<?= Helper::h($k) ?>"><?= Helper::h($v) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Hạng bệnh viện</label>
                        <input type="text" name="hang_benh_vien" id="f_hang" class="form-control" maxlength="50" placeholder="VD: Hạng I, II, III">
                    </div>
                    <div class="form-group">
                        <label>Trạng thái</label>
                        <select name="trang_thai" id="f_trang_thai" class="form-select">
                            <option value="1">Hoạt động</option>
                            <option value="0">Ngưng</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label>Địa chỉ</label>
                    <input type="text" name="dia_chi" id="f_dia_chi" class="form-control" maxlength="500">
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
                        <label>Giám đốc</label>
                        <input type="text" name="giam_doc" id="f_giam_doc" class="form-control" maxlength="100">
                    </div>
                    <div class="form-group">
                        <label>SĐT Giám đốc</label>
                        <input type="text" name="dien_thoai_giam_doc" id="f_dtgd" class="form-control" maxlength="20">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Ngày ký hợp tác</label>
                        <input type="date" name="ngay_ky_hop_tac" id="f_ngay_ky" class="form-control">
                    </div>
                    <div class="form-group">
                        <label style="display:flex;align-items:center;gap:8px;margin-top:28px">
                            <input type="checkbox" id="f_chinh_chk"> <strong>Là bệnh viện chính</strong>
                        </label>
                        <input type="hidden" name="la_benh_vien_chinh" id="f_chinh" value="0">
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

<script>
var URL = APP_BASE + 'GUI/DM_BenhVien/ajax_handler.php';
var CAN_EDIT = <?= $canEdit?'true':'false' ?>;
var CAN_DEL = <?= $canDel?'true':'false' ?>;
var CAP_MAP = <?= json_encode($capList) ?>;
var state = { page: 1, pageSize: 20, search: '', daXoa: 0, cap: '' };

function load() {
    APP.showLoading('#tableWrap');
    APP.ajax(URL, {
        action: 'getPaged',
        page: state.page, pageSize: state.pageSize,
        search: state.search, da_xoa: state.daXoa,
        cap_benh_vien: state.cap
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
        $tb.append('<tr><td colspan="9"><div class="empty-state"><div class="icon"><svg class="icon icon-empty" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="8"></circle><path d="m21 21-4.35-4.35"></path></svg></div>Không có dữ liệu</div></td></tr>');
        return;
    }
    var stt = (state.page - 1) * state.pageSize;
    rows.forEach(function (r) {
        stt++;
        var tt = r.trang_thai == 1
            ? '<span class="badge badge-success">Hoạt động</span>'
            : '<span class="badge badge-danger">Ngưng</span>';
        var cap = CAP_MAP[r.cap_benh_vien] || r.cap_benh_vien;
        var tenBv = APP.escape(r.ten_benh_vien);
        if (r.la_benh_vien_chinh == 1) tenBv = '⭐ ' + tenBv;
        var isChinh = (r.id == 1);
        var actions = '';
        if (state.daXoa == 0) {
            if (CAN_EDIT) actions += '<button class="btn btn-sm" title="Sửa" onclick="openEdit(' + r.id + ')"><svg class="icon icon-edit" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg></button>';
            if (CAN_DEL && !isChinh) actions += '<button class="btn btn-sm btn-danger" title="Xóa" onclick="trashItem(' + r.id + ')"><svg class="icon icon-trash" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg></button>';
        } else {
            if (CAN_EDIT) actions += '<button class="btn btn-sm btn-success" onclick="restoreItem(' + r.id + ')">↺ Khôi phục</button>';
            if (CAN_DEL) actions += '<button class="btn btn-sm btn-danger" onclick="deleteItem(' + r.id + ')">Xóa</button>';
        }
        $tb.append(
            '<tr>' +
                '<td class="text-center">' + stt + '</td>' +
                '<td><strong>' + APP.escape(r.ma_benh_vien) + '</strong></td>' +
                '<td>' + tenBv + (r.hang_benh_vien ? ' <small class="text-muted">(' + APP.escape(r.hang_benh_vien) + ')</small>' : '') + '</td>' +
                '<td class="text-center"><span class="badge">' + APP.escape(cap) + '</span></td>' +
                '<td>' + APP.escape(r.dia_chi || '-') + '</td>' +
                '<td>' + APP.escape(r.giam_doc || '-') + '</td>' +
                '<td class="text-center">' + (r.so_nhan_vien || 0) + '</td>' +
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

$('#search').on('input', APP.debounce(function () {
    state.search = $(this).val(); state.page = 1; load();
}, 400));

$('#filterCap').on('change', function () { state.cap = this.value; state.page = 1; load(); });
$('#filterDaXoa').on('change', function () { state.daXoa = parseInt(this.value, 10) || 0; state.page = 1; load(); });

$('#f_chinh_chk').on('change', function () {
    $('#f_chinh').val(this.checked ? 1 : 0);
});

function openCreate() {
    $('#modalTitle').text('Thêm bệnh viện');
    $('#formBV')[0].reset();
    $('#f_id').val(''); $('#f_chinh').val(0); $('#f_chinh_chk').prop('checked', false);
    $('#modalForm').addClass('open');
}
function openEdit(id) {
    APP.ajax(URL, {action: 'getById', id: id}).done(function (res) {
        if (!res.success) { APP.toast(res.message, 'error'); return; }
        var e = res.data;
        $('#modalTitle').text('Sửa bệnh viện');
        $('#f_id').val(e.id);
        $('#f_ma').val(e.ma_benh_vien);
        $('#f_ten').val(e.ten_benh_vien);
        $('#f_cap').val(e.cap_benh_vien);
        $('#f_hang').val(e.hang_benh_vien || '');
        $('#f_dia_chi').val(e.dia_chi || '');
        $('#f_dien_thoai').val(e.dien_thoai || '');
        $('#f_email').val(e.email || '');
        $('#f_giam_doc').val(e.giam_doc || '');
        $('#f_dtgd').val(e.dien_thoai_giam_doc || '');
        $('#f_ngay_ky').val(e.ngay_ky_hop_tac || '');
        $('#f_trang_thai').val(e.trang_thai);
        var chinh = parseInt(e.la_benh_vien_chinh, 10) === 1;
        $('#f_chinh_chk').prop('checked', chinh);
        $('#f_chinh').val(chinh ? 1 : 0);
        $('#modalForm').addClass('open');
    });
}
function closeModal() { $('#modalForm').removeClass('open'); }

$('#formBV').on('submit', function (e) {
    e.preventDefault();
    var data = $(this).serializeArray();
    data.push({name: 'action', value: $('#f_id').val() ? 'update' : 'insert'});
    APP.ajax(URL, data).done(function (res) {
        if (res.success) { APP.toast(res.message, 'success'); closeModal(); load(); }
        else APP.toast(res.message, 'error');
    });
});

function trashItem(id) {
    APP.confirm('Chuyển bệnh viện này vào thùng rác?', function () {
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
    APP.confirm('Xóa VĨNH VIỄN bệnh viện này?', function () {
        APP.ajax(URL, {action: 'delete', id: id}).done(function (res) {
            res.success ? (APP.toast(res.message, 'success'), load()) : APP.toast(res.message, 'error');
        });
    }, {yesText: 'Xóa vĩnh viễn'});
}

load();
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
