<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../BUS/DM_KhoaPhong_BUS.php';

Helper::requireLogin();
if (!PhanQuyenHelper::hasQuyen('DM_NhanVien', PhanQuyenHelper::QUYEN_XEM)) {
    echo 'Bạn không có quyền truy cập chức năng này.'; exit;
}

$canAdd = PhanQuyenHelper::hasQuyen('DM_NhanVien', PhanQuyenHelper::QUYEN_THEM);
$canEdit = PhanQuyenHelper::hasQuyen('DM_NhanVien', PhanQuyenHelper::QUYEN_SUA);
$canDel = PhanQuyenHelper::hasQuyen('DM_NhanVien', PhanQuyenHelper::QUYEN_XOA);

$khoaCombo = DM_KhoaPhong_BUS::getCombo();

$pageTitle = 'Quản lý nhân viên';
$activeMenu = 'DM_NhanVien';
require __DIR__ . '/../layouts/header.php';
?>

<div class="breadcrumb">
    <a href="<?= AppConfig::baseUrl('GUI/dashboard/index.php') ?>">Trang chủ</a>
    <span class="sep">›</span> Danh mục
    <span class="sep">›</span> <span>Nhân viên</span>
</div>

<div class="card">
    <div class="toolbar">
        <div class="left">
            <input type="text" id="search" class="form-control" placeholder="Tìm mã NV, họ tên, SĐT..." style="max-width:320px">
            <select id="filterKhoa" class="form-select" style="max-width:220px">
                <option value="0">-- Tất cả khoa/phòng --</option>
                <?php foreach ($khoaCombo as $k): ?>
                    <option value="<?= $k['id'] ?>"><?= Helper::h($k['ten_khoa']) ?></option>
                <?php endforeach; ?>
            </select>
            <select id="filterDaXoa" class="form-select" style="max-width:160px">
                <option value="0">Đang hoạt động</option>
                <option value="1">Thùng rác</option>
            </select>
        </div>
        <div class="right">
            <?php if ($canAdd): ?>
                <button type="button" class="btn btn-primary" onclick="openCreate()">+ Thêm nhân viên</button>
            <?php endif; ?>
        </div>
    </div>
    <div class="table-wrap" id="tableWrap" style="position:relative;min-height:200px">
        <table class="table">
            <thead>
                <tr>
                    <th style="width:50px" class="text-center">#</th>
                    <th style="width:110px">Mã NV</th>
                    <th>Họ tên</th>
                    <th>Chức danh</th>
                    <th>Khoa/Phòng</th>
                    <th>Điện thoại</th>
                    <th>Email</th>
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
    <div class="modal" style="max-width:820px">
        <div class="modal-header">
            <h3 id="modalTitle">Thêm nhân viên</h3>
            <button type="button" class="close" onclick="closeModal()">&times;</button>
        </div>
        <form id="formNV">
            <div class="modal-body">
                <input type="hidden" name="id" id="f_id">
                <input type="hidden" name="benh_vien_id" value="1">
                <div class="form-row">
                    <div class="form-group">
                        <label>Mã NV <span class="required">*</span></label>
                        <input type="text" name="ma_nv" id="f_ma_nv" class="form-control" required maxlength="50">
                    </div>
                    <div class="form-group">
                        <label>Họ tên <span class="required">*</span></label>
                        <input type="text" name="ho_ten" id="f_ho_ten" class="form-control" required maxlength="200">
                    </div>
                </div>
                <div class="form-row">
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
                        <label>Trạng thái</label>
                        <select name="trang_thai" id="f_trang_thai" class="form-select">
                            <option value="1">Đang làm</option>
                            <option value="0">Nghỉ việc</option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Khoa/Phòng</label>
                        <select name="khoa_phong_id" id="f_khoa_phong_id" class="form-select">
                            <option value="">-- Chọn --</option>
                            <?php foreach ($khoaCombo as $k): ?>
                                <option value="<?= $k['id'] ?>"><?= Helper::h($k['ten_khoa']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Chức danh</label>
                        <input type="text" name="chuc_danh" id="f_chuc_danh" class="form-control" maxlength="100">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Trình độ</label>
                        <input type="text" name="trinh_do" id="f_trinh_do" class="form-control" maxlength="100">
                    </div>
                    <div class="form-group">
                        <label>Chuyên khoa</label>
                        <input type="text" name="chuyen_khoa" id="f_chuyen_khoa" class="form-control" maxlength="200">
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
                    <label>Địa chỉ</label>
                    <input type="text" name="dia_chi" id="f_dia_chi" class="form-control" maxlength="250">
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
var URL = APP_BASE + 'GUI/DM_NhanVien/ajax_handler.php';
var CAN_EDIT = <?= $canEdit?'true':'false' ?>;
var CAN_DEL = <?= $canDel?'true':'false' ?>;
var state = { page: 1, pageSize: 20, search: '', daXoa: 0, khoaId: 0 };

var ICON_EDIT = '<?= addslashes(IconHelper::svg('edit', '18')) ?>';
var ICON_TRASH = '<?= addslashes(IconHelper::svg('trash', '18')) ?>';
var ICON_EMPTY = '<?= addslashes(IconHelper::svg('dashboard', '40')) ?>';

function load() {
    APP.showLoading('#tableWrap');
    APP.ajax(URL, {
        action: 'getPaged',
        page: state.page, pageSize: state.pageSize,
        search: state.search, da_xoa: state.daXoa,
        khoa_phong_id: state.khoaId
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
        $tb.append('<tr><td colspan="9"><div class="empty-state"><div class="icon">' + ICON_EMPTY + '</div>Không có dữ liệu</div></td></tr>');
        return;
    }
    var stt = (state.page - 1) * state.pageSize;
    rows.forEach(function (r) {
        stt++;
        var tt = r.trang_thai == 1
            ? '<span class="badge badge-success">Đang làm</span>'
            : '<span class="badge badge-danger">Nghỉ việc</span>';
        var actions = '';
        if (state.daXoa == 0) {
            if (CAN_EDIT) actions += '<button class="btn btn-sm" title="Sửa" onclick="openEdit(' + r.id + ')">' + ICON_EDIT + '</button>';
            if (CAN_DEL) actions += '<button class="btn btn-sm btn-danger" title="Xóa" onclick="trashItem(' + r.id + ')">' + ICON_TRASH + '</button>';
        } else {
            if (CAN_EDIT) actions += '<button class="btn btn-sm btn-success" onclick="restoreItem(' + r.id + ')">↺ Khôi phục</button>';
            if (CAN_DEL) actions += '<button class="btn btn-sm btn-danger" onclick="deleteItem(' + r.id + ')">Xóa</button>';
        }
        $tb.append(
            '<tr>' +
                '<td class="text-center">' + stt + '</td>' +
                '<td><strong>' + APP.escape(r.ma_nv) + '</strong></td>' +
                '<td>' + APP.escape(r.ho_ten) + '</td>' +
                '<td>' + APP.escape(r.chuc_danh || '-') + '</td>' +
                '<td>' + APP.escape(r.ten_khoa_phong || '-') + '</td>' +
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

$('#search').on('input', APP.debounce(function () {
    state.search = $(this).val(); state.page = 1; load();
}, 400));

$('#filterKhoa').on('change', function () { state.khoaId = parseInt(this.value, 10) || 0; state.page = 1; load(); });
$('#filterDaXoa').on('change', function () { state.daXoa = parseInt(this.value, 10) || 0; state.page = 1; load(); });

function openCreate() {
    $('#modalTitle').text('Thêm nhân viên');
    $('#formNV')[0].reset();
    $('#f_id').val('');
    $('#modalForm').addClass('open');
}
function openEdit(id) {
    APP.ajax(URL, {action: 'getById', id: id}).done(function (res) {
        if (!res.success) { APP.toast(res.message, 'error'); return; }
        var e = res.data;
        $('#modalTitle').text('Sửa nhân viên');
        $('#f_id').val(e.id);
        $('#f_ma_nv').val(e.ma_nv);
        $('#f_ho_ten').val(e.ho_ten);
        $('#f_ngay_sinh').val(e.ngay_sinh || '');
        $('#f_gioi_tinh').val(e.gioi_tinh || '');
        $('#f_chuc_danh').val(e.chuc_danh || '');
        $('#f_khoa_phong_id').val(e.khoa_phong_id || '');
        $('#f_trinh_do').val(e.trinh_do || '');
        $('#f_chuyen_khoa').val(e.chuyen_khoa || '');
        $('#f_dien_thoai').val(e.dien_thoai || '');
        $('#f_email').val(e.email || '');
        $('#f_dia_chi').val(e.dia_chi || '');
        $('#f_trang_thai').val(e.trang_thai);
        $('#modalForm').addClass('open');
    });
}
function closeModal() { $('#modalForm').removeClass('open'); }

$('#formNV').on('submit', function (e) {
    e.preventDefault();
    var data = $(this).serializeArray();
    data.push({name: 'action', value: $('#f_id').val() ? 'update' : 'insert'});
    APP.ajax(URL, data).done(function (res) {
        if (res.success) { APP.toast(res.message, 'success'); closeModal(); load(); }
        else APP.toast(res.message, 'error');
    });
});

function trashItem(id) {
    APP.confirm('Chuyển nhân viên này vào thùng rác?', function () {
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
    APP.confirm('Xóa VĨNH VIỄN nhân viên này?', function () {
        APP.ajax(URL, {action: 'delete', id: id}).done(function (res) {
            res.success ? (APP.toast(res.message, 'success'), load()) : APP.toast(res.message, 'error');
        });
    }, {yesText: 'Xóa vĩnh viễn'});
}

load();
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
