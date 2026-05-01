<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../BUS/DM_NhanVien_BUS.php';

Helper::requireLogin();
if (!PhanQuyenHelper::hasQuyen('DM_KhoaPhong', PhanQuyenHelper::QUYEN_XEM)) {
    echo 'Bạn không có quyền truy cập chức năng này.'; exit;
}

$canAdd = PhanQuyenHelper::hasQuyen('DM_KhoaPhong', PhanQuyenHelper::QUYEN_THEM);
$canEdit = PhanQuyenHelper::hasQuyen('DM_KhoaPhong', PhanQuyenHelper::QUYEN_SUA);
$canDel = PhanQuyenHelper::hasQuyen('DM_KhoaPhong', PhanQuyenHelper::QUYEN_XOA);

$nvCombo = DM_NhanVien_BUS::getCombo();
$loaiList = Constants::getLoaiDonViList();

$pageTitle = 'Quản lý khoa / phòng';
$activeMenu = 'DM_KhoaPhong';
require __DIR__ . '/../layouts/header.php';
?>

<div class="breadcrumb">
    <a href="<?= AppConfig::baseUrl('GUI/dashboard/index.php') ?>">Trang chủ</a>
    <span class="sep">›</span> Danh mục
    <span class="sep">›</span> <span>Khoa / Phòng</span>
</div>

<div class="card">
    <div class="toolbar">
        <div class="left">
            <input type="text" id="search" class="form-control" placeholder="Tìm mã, tên khoa/phòng..." style="max-width:320px">
            <select id="filterLoai" class="form-select" style="max-width:160px">
                <option value="">-- Tất cả loại --</option>
                <?php foreach ($loaiList as $k => $v): ?>
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
                <button type="button" class="btn btn-primary" onclick="openCreate()">+ Thêm khoa/phòng</button>
            <?php endif; ?>
        </div>
    </div>
    <div class="table-wrap" id="tableWrap" style="position:relative;min-height:200px">
        <table class="table">
            <thead>
                <tr>
                    <th style="width:50px" class="text-center">#</th>
                    <th style="width:120px">Mã</th>
                    <th>Tên khoa/phòng</th>
                    <th class="text-center" style="width:100px">Loại</th>
                    <th>Trưởng khoa</th>
                    <th>Chuyên khoa</th>
                    <th class="text-center" style="width:90px">Số giường</th>
                    <th class="text-center" style="width:110px">Trạng thái</th>
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
    <div class="modal" style="max-width:780px">
        <div class="modal-header">
            <h3 id="modalTitle">Thêm khoa/phòng</h3>
            <button type="button" class="close" onclick="closeModal()">&times;</button>
        </div>
        <form id="formKP">
            <div class="modal-body">
                <input type="hidden" name="id" id="f_id">
                <div class="form-row">
                    <div class="form-group">
                        <label>Mã <span class="required">*</span></label>
                        <input type="text" name="ma_khoa" id="f_ma_khoa" class="form-control" required maxlength="50">
                    </div>
                    <div class="form-group">
                        <label>Loại đơn vị <span class="required">*</span></label>
                        <select name="loai_don_vi" id="f_loai_don_vi" class="form-select" required>
                            <?php foreach ($loaiList as $k => $v): ?>
                                <option value="<?= Helper::h($k) ?>"><?= Helper::h($v) ?></option>
                            <?php endforeach; ?>
                        </select>
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
                    <label>Tên khoa/phòng <span class="required">*</span></label>
                    <input type="text" name="ten_khoa" id="f_ten_khoa" class="form-control" required maxlength="200">
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Trưởng khoa</label>
                        <select name="truong_khoa_id" id="f_truong_khoa_id" class="form-select">
                            <option value="">-- Không gán --</option>
                            <?php foreach ($nvCombo as $nv): ?>
                                <option value="<?= $nv['id'] ?>"><?= Helper::h($nv['ma_nv'] . ' - ' . $nv['ho_ten']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Số giường</label>
                        <input type="number" name="so_giuong" id="f_so_giuong" class="form-control" min="0">
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
                <div class="form-group">
                    <label>Chuyên khoa</label>
                    <input type="text" name="chuyen_khoa" id="f_chuyen_khoa" class="form-control" maxlength="200">
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
var URL = APP_BASE + 'GUI/DM_KhoaPhong/ajax_handler.php';
var CAN_EDIT = <?= $canEdit?'true':'false' ?>;
var CAN_DEL = <?= $canDel?'true':'false' ?>;
var LOAI_MAP = <?= json_encode($loaiList) ?>;
var state = { page: 1, pageSize: 20, search: '', daXoa: 0, loai: '' };

function load() {
    APP.showLoading('#tableWrap');
    APP.ajax(URL, {
        action: 'getPaged',
        page: state.page, pageSize: state.pageSize,
        search: state.search, da_xoa: state.daXoa,
        loai_don_vi: state.loai
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
        var loai = LOAI_MAP[r.loai_don_vi] || r.loai_don_vi;
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
                '<td><strong>' + APP.escape(r.ma_khoa) + '</strong></td>' +
                '<td>' + APP.escape(r.ten_khoa) + '</td>' +
                '<td class="text-center"><span class="badge">' + APP.escape(loai) + '</span></td>' +
                '<td>' + APP.escape(r.ten_truong_khoa || '-') + '</td>' +
                '<td>' + APP.escape(r.chuyen_khoa || '-') + '</td>' +
                '<td class="text-center">' + (r.so_giuong || '-') + '</td>' +
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

$('#filterLoai').on('change', function () { state.loai = this.value; state.page = 1; load(); });
$('#filterDaXoa').on('change', function () { state.daXoa = parseInt(this.value, 10) || 0; state.page = 1; load(); });

function openCreate() {
    $('#modalTitle').text('Thêm khoa/phòng');
    $('#formKP')[0].reset();
    $('#f_id').val('');
    $('#modalForm').addClass('open');
}
function openEdit(id) {
    APP.ajax(URL, {action: 'getById', id: id}).done(function (res) {
        if (!res.success) { APP.toast(res.message, 'error'); return; }
        var e = res.data;
        $('#modalTitle').text('Sửa khoa/phòng');
        $('#f_id').val(e.id);
        $('#f_ma_khoa').val(e.ma_khoa);
        $('#f_ten_khoa').val(e.ten_khoa);
        $('#f_loai_don_vi').val(e.loai_don_vi);
        $('#f_truong_khoa_id').val(e.truong_khoa_id || '');
        $('#f_dien_thoai').val(e.dien_thoai || '');
        $('#f_email').val(e.email || '');
        $('#f_chuyen_khoa').val(e.chuyen_khoa || '');
        $('#f_so_giuong').val(e.so_giuong || '');
        $('#f_trang_thai').val(e.trang_thai);
        $('#modalForm').addClass('open');
    });
}
function closeModal() { $('#modalForm').removeClass('open'); }

$('#formKP').on('submit', function (e) {
    e.preventDefault();
    var data = $(this).serializeArray();
    data.push({name: 'action', value: $('#f_id').val() ? 'update' : 'insert'});
    APP.ajax(URL, data).done(function (res) {
        if (res.success) { APP.toast(res.message, 'success'); closeModal(); load(); }
        else APP.toast(res.message, 'error');
    });
});

function trashItem(id) {
    APP.confirm('Chuyển khoa/phòng này vào thùng rác?', function () {
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
    APP.confirm('Xóa VĨNH VIỄN khoa/phòng này?', function () {
        APP.ajax(URL, {action: 'delete', id: id}).done(function (res) {
            res.success ? (APP.toast(res.message, 'success'), load()) : APP.toast(res.message, 'error');
        });
    }, {yesText: 'Xóa vĩnh viễn'});
}

load();
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
