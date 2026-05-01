<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../BUS/DM_NhomTaiKhoan_BUS.php';
require_once __DIR__ . '/../../BUS/DM_NhanVien_BUS.php';

Helper::requireLogin();
if (!PhanQuyenHelper::hasQuyen('DM_NguoiDung', PhanQuyenHelper::QUYEN_XEM)) {
    echo 'Bạn không có quyền truy cập chức năng này.'; exit;
}

$canAdd = PhanQuyenHelper::hasQuyen('DM_NguoiDung', PhanQuyenHelper::QUYEN_THEM);
$canEdit = PhanQuyenHelper::hasQuyen('DM_NguoiDung', PhanQuyenHelper::QUYEN_SUA);
$canDel = PhanQuyenHelper::hasQuyen('DM_NguoiDung', PhanQuyenHelper::QUYEN_XOA);

$nhomCombo = DM_NhomTaiKhoan_BUS::getCombo();
$nvCombo = DM_NhanVien_BUS::getCombo();

$pageTitle = 'Quản lý người dùng';
$activeMenu = 'DM_NguoiDung';
require __DIR__ . '/../layouts/header.php';
?>

<div class="breadcrumb">
    <a href="<?= AppConfig::baseUrl('GUI/dashboard/index.php') ?>">Trang chủ</a>
    <span class="sep">›</span> Hệ thống
    <span class="sep">›</span> <span>Người dùng</span>
</div>

<div class="card">
    <div class="toolbar">
        <div class="left">
            <input type="text" id="search" class="form-control" placeholder="Tìm tài khoản, họ tên, mã NV..." style="max-width:340px">
            <select id="filterNhom" class="form-select" style="max-width:200px">
                <option value="0">-- Tất cả nhóm --</option>
                <?php foreach ($nhomCombo as $n): ?>
                    <option value="<?= $n['id'] ?>"><?= Helper::h($n['ten_nhom']) ?></option>
                <?php endforeach; ?>
            </select>
            <select id="filterDaXoa" class="form-select" style="max-width:160px">
                <option value="0">Đang hoạt động</option>
                <option value="1">Thùng rác</option>
            </select>
        </div>
        <div class="right">
            <?php if ($canAdd): ?>
                <button type="button" class="btn btn-primary" onclick="openCreate()">+ Thêm mới</button>
            <?php endif; ?>
        </div>
    </div>
    <div class="table-wrap" id="tableWrap" style="position:relative;min-height:200px">
        <table class="table">
            <thead>
                <tr>
                    <th style="width:50px" class="text-center">#</th>
                    <th>Tài khoản</th>
                    <th>Họ tên (NV)</th>
                    <th>Nhóm</th>
                    <th>Khoa/Phòng</th>
                    <th>Đăng nhập cuối</th>
                    <th class="text-center">Trạng thái</th>
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

<!-- Modal Form -->
<div class="modal-backdrop" id="modalForm">
    <div class="modal">
        <div class="modal-header">
            <h3 id="modalTitle">Thêm người dùng</h3>
            <button type="button" class="close" onclick="closeModal()">&times;</button>
        </div>
        <form id="formUser">
            <div class="modal-body">
                <input type="hidden" name="id" id="f_id">
                <div class="form-row">
                    <div class="form-group">
                        <label>Tài khoản <span class="required">*</span></label>
                        <input type="text" name="tai_khoan" id="f_tai_khoan" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Nhân viên</label>
                        <select name="nhan_vien_id" id="f_nhan_vien_id" class="form-select">
                            <option value="">-- Không gán --</option>
                            <?php foreach ($nvCombo as $nv): ?>
                                <option value="<?= $nv['id'] ?>"><?= Helper::h($nv['ma_nv'] . ' - ' . $nv['ho_ten']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Nhóm tài khoản <span class="required">*</span></label>
                        <select name="nhom_tai_khoan_id" id="f_nhom_tai_khoan_id" class="form-select" required>
                            <option value="">-- Chọn nhóm --</option>
                            <?php foreach ($nhomCombo as $n): ?>
                                <option value="<?= $n['id'] ?>"><?= Helper::h($n['ten_nhom']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Trạng thái</label>
                        <select name="trang_thai" id="f_trang_thai" class="form-select">
                            <option value="1">Hoạt động</option>
                            <option value="0">Khóa</option>
                        </select>
                    </div>
                </div>
                <div class="form-group" id="passGroup">
                    <label>Mật khẩu <span class="required">*</span></label>
                    <input type="password" name="mat_khau" id="f_mat_khau" class="form-control" minlength="6">
                    <small class="text-muted">Tối thiểu 6 ký tự</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn" onclick="closeModal()">Hủy</button>
                <button type="submit" class="btn btn-primary">Lưu</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Reset Password -->
<div class="modal-backdrop" id="modalReset">
    <div class="modal" style="max-width:460px">
        <div class="modal-header">
            <h3>Đặt lại mật khẩu</h3>
            <button type="button" class="close" onclick="closeResetModal()">&times;</button>
        </div>
        <form id="formReset">
            <div class="modal-body">
                <input type="hidden" name="id" id="r_id">
                <div class="form-group">
                    <label>Tài khoản</label>
                    <input type="text" id="r_tk" class="form-control" readonly>
                </div>
                <div class="form-group">
                    <label>Mật khẩu mới <span class="required">*</span></label>
                    <input type="password" name="mat_khau_moi" class="form-control" required minlength="6">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn" onclick="closeResetModal()">Hủy</button>
                <button type="submit" class="btn btn-warning">Đặt lại</button>
            </div>
        </form>
    </div>
</div>

<script>
var URL = APP_BASE + 'GUI/DM_NguoiDung/ajax_handler.php';
var CAN_EDIT = <?= $canEdit?'true':'false' ?>;
var CAN_DEL = <?= $canDel?'true':'false' ?>;
var state = { page: 1, pageSize: 20, search: '', daXoa: 0, nhomId: 0 };

var ICON_EDIT = '<?= addslashes(IconHelper::svg('edit', '18')) ?>';
var ICON_KEY = '<?= addslashes(IconHelper::svg('key', '18')) ?>';
var ICON_TRASH = '<?= addslashes(IconHelper::svg('trash', '18')) ?>';
var ICON_EMPTY = '<?= addslashes(IconHelper::svg('dashboard', '40')) ?>';

function load() {
    APP.showLoading('#tableWrap');
    APP.ajax(URL, {
        action: 'getPaged',
        page: state.page, pageSize: state.pageSize,
        search: state.search, da_xoa: state.daXoa,
        nhom_tai_khoan_id: state.nhomId
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
        $tb.append('<tr><td colspan="8"><div class="empty-state"><div class="icon">' + ICON_EMPTY + '</div>Không có dữ liệu</div></td></tr>');
        return;
    }
    var stt = (state.page - 1) * state.pageSize;
    rows.forEach(function (r, i) {
        stt++;
        var tt = r.trang_thai == 1
            ? '<span class="badge badge-success">Hoạt động</span>'
            : '<span class="badge badge-danger">Khóa</span>';
        var actions = '';
        if (state.daXoa == 0) {
            if (CAN_EDIT) {
                actions += '<button class="btn btn-sm" title="Sửa" onclick="openEdit(' + r.id + ')">' + ICON_EDIT + '</button>';
                actions += '<button class="btn btn-sm btn-warning" title="Reset MK" onclick="openReset(' + r.id + ',\'' + APP.escape(r.tai_khoan) + '\')" >' + ICON_KEY + '</button>';
            }
            if (CAN_DEL) actions += '<button class="btn btn-sm btn-danger" title="Xóa" onclick="trashItem(' + r.id + ')">' + ICON_TRASH + '</button>';
        } else {
            if (CAN_EDIT) actions += '<button class="btn btn-sm btn-success" onclick="restoreItem(' + r.id + ')">↺ Khôi phục</button>';
            if (CAN_DEL) actions += '<button class="btn btn-sm btn-danger" onclick="deleteItem(' + r.id + ')">Xóa vĩnh viễn</button>';
        }
        $tb.append(
            '<tr>' +
                '<td class="text-center">' + stt + '</td>' +
                '<td><strong>' + APP.escape(r.tai_khoan) + '</strong></td>' +
                '<td>' + APP.escape((r.ho_ten || '') + (r.ma_nv ? ' (' + r.ma_nv + ')' : '')) + '</td>' +
                '<td>' + APP.escape(r.ten_nhom || '-') + '</td>' +
                '<td>' + APP.escape(r.khoa_phong_text || '-') + '</td>' +
                '<td>' + (r.lan_dang_nhap_cuoi ? APP.formatDateTime(r.lan_dang_nhap_cuoi) : '<span class="text-muted">Chưa</span>') + '</td>' +
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

$('#filterNhom').on('change', function () { state.nhomId = parseInt(this.value, 10) || 0; state.page = 1; load(); });
$('#filterDaXoa').on('change', function () { state.daXoa = parseInt(this.value, 10) || 0; state.page = 1; load(); });

function openCreate() {
    $('#modalTitle').text('Thêm người dùng');
    $('#formUser')[0].reset();
    $('#f_id').val('');
    $('#passGroup').show();
    $('#f_mat_khau').prop('required', true);
    $('#modalForm').addClass('open');
}
function openEdit(id) {
    APP.ajax(URL, {action: 'getById', id: id}).done(function (res) {
        if (!res.success) { APP.toast(res.message, 'error'); return; }
        var u = res.data;
        $('#modalTitle').text('Sửa người dùng');
        $('#f_id').val(u.id);
        $('#f_tai_khoan').val(u.tai_khoan);
        $('#f_nhan_vien_id').val(u.nhan_vien_id || '');
        $('#f_nhom_tai_khoan_id').val(u.nhom_tai_khoan_id || '');
        $('#f_trang_thai').val(u.trang_thai);
        $('#passGroup').hide();
        $('#f_mat_khau').prop('required', false).val('');
        $('#modalForm').addClass('open');
    });
}
function closeModal() { $('#modalForm').removeClass('open'); }

$('#formUser').on('submit', function (e) {
    e.preventDefault();
    var data = $(this).serializeArray();
    data.push({name: 'action', value: $('#f_id').val() ? 'update' : 'insert'});
    APP.ajax(URL, data).done(function (res) {
        if (res.success) { APP.toast(res.message, 'success'); closeModal(); load(); }
        else APP.toast(res.message, 'error');
    });
});

function trashItem(id) {
    APP.confirm('Chuyển người dùng này vào thùng rác?', function () {
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
    APP.confirm('Xóa VĨNH VIỄN người dùng này? Không thể khôi phục!', function () {
        APP.ajax(URL, {action: 'delete', id: id}).done(function (res) {
            res.success ? (APP.toast(res.message, 'success'), load()) : APP.toast(res.message, 'error');
        });
    }, {yesText: 'Xóa vĩnh viễn'});
}

function openReset(id, tk) {
    $('#r_id').val(id); $('#r_tk').val(tk);
    $('#formReset')[0].reset(); $('#r_id').val(id); $('#r_tk').val(tk);
    $('#modalReset').addClass('open');
}
function closeResetModal() { $('#modalReset').removeClass('open'); }
$('#formReset').on('submit', function (e) {
    e.preventDefault();
    var data = $(this).serializeArray();
    data.push({name: 'action', value: 'resetPassword'});
    APP.ajax(URL, data).done(function (res) {
        if (res.success) { APP.toast(res.message, 'success'); closeResetModal(); }
        else APP.toast(res.message, 'error');
    });
});

load();
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
