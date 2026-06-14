<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../BUS/DT_KhoaHoc_BUS.php';

Helper::requireLogin();
if (!PhanQuyenHelper::hasQuyen('DT_LopHoc', PhanQuyenHelper::QUYEN_XEM)) {
    echo 'Bạn không có quyền truy cập chức năng này.'; exit;
}

$canAdd = PhanQuyenHelper::hasQuyen('DT_LopHoc', PhanQuyenHelper::QUYEN_THEM);
$canEdit = PhanQuyenHelper::hasQuyen('DT_LopHoc', PhanQuyenHelper::QUYEN_SUA);
$canDel = PhanQuyenHelper::hasQuyen('DT_LopHoc', PhanQuyenHelper::QUYEN_XOA);
$canHvlAdd = PhanQuyenHelper::hasQuyen('DT_HocVienLop', PhanQuyenHelper::QUYEN_THEM);
$canHvlEdit = PhanQuyenHelper::hasQuyen('DT_HocVienLop', PhanQuyenHelper::QUYEN_SUA);
$canHvlDel = PhanQuyenHelper::hasQuyen('DT_HocVienLop', PhanQuyenHelper::QUYEN_XOA);

$khoaCombo = DT_KhoaHoc_BUS::getCombo();

$pageTitle = 'Quản lý lớp học';
$activeMenu = 'DT_LopHoc';
$avatarUrl = AppConfig::baseUrl('assets/uploads/hocvien/');
require __DIR__ . '/../layouts/header.php';
?>

<div class="breadcrumb">
    <a href="<?= AppConfig::baseUrl('GUI/dashboard/index.php') ?>">Trang chủ</a>
    <span class="sep">›</span> Đào tạo
    <span class="sep">›</span> <span>Lớp học</span>
</div>

<!-- Stats -->
<div class="hv-stats">
    <div class="hv-stat">
        <div class="hv-stat-icon hv-stat-blue">
            <?= IconHelper::svg('school', '22') ?>
        </div>
        <div><div class="hv-stat-label">Tổng lớp học</div><div class="hv-stat-value" id="stTotal">—</div></div>
    </div>
    <div class="hv-stat">
        <div class="hv-stat-icon hv-stat-orange">
            <?= IconHelper::svg('clock', '22') ?>
        </div>
        <div><div class="hv-stat-label">Chờ khai giảng</div><div class="hv-stat-value" id="stChoMo">—</div></div>
    </div>
    <div class="hv-stat">
        <div class="hv-stat-icon hv-stat-green">
            <?= IconHelper::svg('play-circle', '22') ?>
        </div>
        <div><div class="hv-stat-label">Đang học</div><div class="hv-stat-value" id="stDangHoc">—</div></div>
    </div>
    <div class="hv-stat">
        <div class="hv-stat-icon hv-stat-purple">
            <?= IconHelper::svg('check-circle', '22') ?>
        </div>
        <div><div class="hv-stat-label">Đã kết thúc</div><div class="hv-stat-value" id="stKetThuc">—</div></div>
    </div>
</div>

<div class="card">
    <div class="toolbar">
        <div class="left">
            <input type="text" id="search" class="form-control" placeholder="Tìm mã lớp, tên lớp, địa điểm..." style="max-width:320px">
            <select id="filterKhoa" class="form-select" style="max-width:260px">
                <option value="0">-- Tất cả khóa học --</option>
                <?php foreach ($khoaCombo as $k): ?>
                    <option value="<?= $k['id'] ?>"><?= Helper::h($k['ma_khoa_hoc'] . ' - ' . $k['ten_khoa_hoc']) ?></option>
                <?php endforeach; ?>
            </select>
            <select id="filterTrangThai" class="form-select" style="max-width:180px">
                <option value="">Tất cả trạng thái</option>
                <option value="0">Chờ khai giảng</option>
                <option value="1">Đang học</option>
                <option value="2">Đã kết thúc</option>
                <option value="3">Đã hủy</option>
            </select>
            <select id="filterDaXoa" class="form-select" style="max-width:160px">
                <option value="0">Đang hoạt động</option>
                <option value="1">Thùng rác</option>
            </select>
        </div>
        <div class="right">
            <?php if ($canAdd): ?>
                <button type="button" class="btn btn-primary" onclick="openCreate()">+ Thêm lớp học</button>
            <?php endif; ?>
        </div>
    </div>
    <div class="table-wrap" id="tableWrap" style="position:relative;min-height:240px">
        <table class="table">
            <thead>
                <tr>
                    <th style="width:50px" class="text-center">#</th>
                    <th style="width:130px">Mã lớp</th>
                    <th>Tên lớp</th>
                    <th>Khóa học</th>
                    <th>Thời gian</th>
                    <th style="width:180px">Học viên</th>
                    <th class="text-center" style="width:130px">Trạng thái</th>
                    <th class="text-right" style="width:170px">Hành động</th>
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

<!-- ================== Modal Form Lớp ================== -->
<div class="modal-backdrop" id="modalForm">
    <div class="modal" style="max-width:820px">
        <div class="modal-header">
            <h3 id="modalTitle">Thêm lớp học</h3>
            <button type="button" class="close" onclick="closeModal()">&times;</button>
        </div>
        <form id="formLop">
            <div class="modal-body">
                <input type="hidden" name="id" id="f_id">

                <div class="form-row">
                    <div class="form-group">
                        <label>Mã lớp <span class="required">*</span></label>
                        <input type="text" name="ma_lop" id="f_ma_lop" class="form-control" required maxlength="50">
                    </div>
                    <div class="form-group">
                        <label>Khóa học <span class="required">*</span></label>
                        <select name="khoa_hoc_id" id="f_khoa_hoc_id" class="form-select" required>
                            <option value="">-- Chọn --</option>
                            <?php foreach ($khoaCombo as $k): ?>
                                <option value="<?= $k['id'] ?>"><?= Helper::h($k['ma_khoa_hoc'] . ' - ' . $k['ten_khoa_hoc']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label>Tên lớp <span class="required">*</span></label>
                    <input type="text" name="ten_lop" id="f_ten_lop" class="form-control" required maxlength="200">
                </div>

                <div class="form-row-3">
                    <div class="form-group">
                        <label>Ngày bắt đầu</label>
                        <input type="date" name="ngay_bat_dau" id="f_ngay_bat_dau" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Ngày kết thúc</label>
                        <input type="date" name="ngay_ket_thuc" id="f_ngay_ket_thuc" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Số lượng tối đa</label>
                        <input type="number" name="so_luong_toi_da" id="f_so_luong_toi_da" class="form-control" min="1" value="30">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Địa điểm</label>
                        <input type="text" name="dia_diem" id="f_dia_diem" class="form-control" maxlength="200">
                    </div>
                    <div class="form-group">
                        <label>Trạng thái</label>
                        <select name="trang_thai" id="f_trang_thai" class="form-select">
                            <option value="0">Chờ khai giảng</option>
                            <option value="1">Đang học</option>
                            <option value="2">Đã kết thúc</option>
                            <option value="3">Đã hủy</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label>Mô tả</label>
                    <textarea name="mo_ta" id="f_mo_ta" class="form-control" rows="2" maxlength="500"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn" onclick="closeModal()">Hủy</button>
                <button type="submit" class="btn btn-primary">Lưu</button>
            </div>
        </form>
    </div>
</div>

<!-- ================== Drawer Học viên của lớp ================== -->
<div class="drawer-backdrop" id="drawerHV">
    <div class="drawer">
        <div class="drawer-header">
            <div>
                <h3 id="drawerLopTen" style="margin:0">Học viên của lớp</h3>
                <div id="drawerLopMeta" class="text-muted" style="font-size:12.5px;margin-top:2px"></div>
            </div>
            <button type="button" class="close" onclick="closeDrawer()">&times;</button>
        </div>
        <div class="drawer-body">
            <div class="d-flex gap-2" style="align-items:center;margin-bottom:12px">
                <input type="text" id="hvlSearch" class="form-control" placeholder="Tìm học viên trong lớp..." style="flex:1">
                <?php if ($canHvlAdd): ?>
                <button type="button" class="btn btn-primary" onclick="openPicker()">+ Thêm học viên</button>
                <?php endif; ?>
            </div>
            <div id="hvlCapacityBar" class="hv-capacity" style="display:none">
                <div class="hv-capacity-fill" style="width:0%"></div>
                <span class="hv-capacity-label">0 / 0</span>
            </div>
            <div id="hvlList" class="hv-student-list" style="position:relative;min-height:200px"></div>
        </div>
    </div>
</div>

<!-- ================== Modal Picker học viên ================== -->
<div class="modal-backdrop" id="modalPicker">
    <div class="modal" style="max-width:760px">
        <div class="modal-header">
            <h3>Thêm học viên vào lớp</h3>
            <button type="button" class="close" onclick="closePicker()">&times;</button>
        </div>
        <div class="modal-body">
            <input type="text" id="pickerSearch" class="form-control mb-2" placeholder="Tìm mã, họ tên, đơn vị...">
            <div id="pickerInfo" class="text-muted" style="font-size:12.5px;margin-bottom:8px">Chọn học viên cần ghi danh.</div>
            <div id="pickerList" class="hv-picker-list" style="max-height:420px;overflow-y:auto"></div>
        </div>
        <div class="modal-footer">
            <div class="text-muted" id="pickerSelCount" style="flex:1;font-size:13px">0 đã chọn</div>
            <button type="button" class="btn" onclick="closePicker()">Hủy</button>
            <button type="button" class="btn btn-primary" onclick="submitPicker()">Ghi danh</button>
        </div>
    </div>
</div>

<!-- ================== Modal Sửa HVL (điểm, xếp loại) ================== -->
<div class="modal-backdrop" id="modalHvlEdit">
    <div class="modal" style="max-width:560px">
        <div class="modal-header">
            <h3>Thông tin ghi danh</h3>
            <button type="button" class="close" onclick="closeHvlEdit()">&times;</button>
        </div>
        <form id="formHvl">
            <div class="modal-body">
                <input type="hidden" name="id" id="hf_id">
                <div id="hf_summary" style="background:var(--gray-50);padding:12px;border-radius:var(--radius);margin-bottom:14px;display:flex;gap:12px;align-items:center"></div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Ngày ghi danh</label>
                        <input type="date" name="ngay_ghi_danh" id="hf_ngay_ghi_danh" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Trạng thái</label>
                        <select name="trang_thai" id="hf_trang_thai" class="form-select">
                            <option value="0">Chờ duyệt</option>
                            <option value="1">Đang học</option>
                            <option value="2">Hoàn thành</option>
                            <option value="3">Bỏ học</option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Điểm tổng kết (0-10)</label>
                        <input type="number" step="0.1" min="0" max="10" name="diem_tong_ket" id="hf_diem" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Xếp loại</label>
                        <select name="xep_loai" id="hf_xep_loai" class="form-select">
                            <option value="">--</option>
                            <option>Xuất sắc</option>
                            <option>Giỏi</option>
                            <option>Khá</option>
                            <option>Trung bình</option>
                            <option>Yếu</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label>Ghi chú</label>
                    <textarea name="ghi_chu" id="hf_ghi_chu" class="form-control" rows="2" maxlength="250"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn" onclick="closeHvlEdit()">Hủy</button>
                <button type="submit" class="btn btn-primary">Lưu</button>
            </div>
        </form>
    </div>
</div>

<script>
var URL = APP_BASE + 'GUI/DT_LopHoc/ajax_handler.php';
var AVATAR_URL = <?= json_encode($avatarUrl) ?>;
var CAN_EDIT = <?= $canEdit?'true':'false' ?>;
var CAN_DEL = <?= $canDel?'true':'false' ?>;
var CAN_HVL_ADD = <?= $canHvlAdd?'true':'false' ?>;
var CAN_HVL_EDIT = <?= $canHvlEdit?'true':'false' ?>;
var CAN_HVL_DEL = <?= $canHvlDel?'true':'false' ?>;
var ICON_EDIT = '<?= addslashes(IconHelper::svg('edit', '16')) ?>';
var ICON_TRASH = '<?= addslashes(IconHelper::svg('trash', '16')) ?>';
var ICON_USERS = '<?= addslashes(IconHelper::svg('users', '16')) ?>';
var ICON_EMPTY = '<?= addslashes(IconHelper::svg('search', '40')) ?>';
var ICON_EMPTY_HVL = '<?= addslashes(IconHelper::svg('users', '40')) ?>';
var state = { page: 1, pageSize: 20, search: '', daXoa: 0, khoaId: 0, trangThai: '' };
var currentLop = null;
var pickerSelected = {};

// ====== Helpers ======
function initials(name) {
    if (!name) return '?';
    var p = name.trim().split(/\s+/);
    return p.length === 1 ? p[0].substr(0,2).toUpperCase() : (p[p.length-2][0] + p[p.length-1][0]).toUpperCase();
}
function colorFromName(name) {
    var c = ['#2563eb','#7c3aed','#db2777','#dc2626','#ea580c','#d97706','#16a34a','#0891b2','#4f46e5','#0284c7'];
    var h = 0; for (var i=0;i<(name||'').length;i++) h = (h*31 + name.charCodeAt(i)) & 0xffff;
    return c[h % c.length];
}
function hvAvatar(hv, size) {
    size = size || 40;
    if (hv.avatar) {
        return '<div class="hv-av" style="width:'+size+'px;height:'+size+'px"><img src="'+AVATAR_URL+APP.escape(hv.avatar)+'"></div>';
    }
    return '<div class="hv-av hv-av-initials" style="width:'+size+'px;height:'+size+'px;background:'+colorFromName(hv.ho_ten)+'">'+APP.escape(initials(hv.ho_ten))+'</div>';
}
function statusBadgeLop(tt) {
    switch (parseInt(tt,10)) {
        case 0: return '<span class="badge badge-warning">Chờ khai giảng</span>';
        case 1: return '<span class="badge badge-success">Đang học</span>';
        case 2: return '<span class="badge badge-info">Đã kết thúc</span>';
        case 3: return '<span class="badge badge-danger">Đã hủy</span>';
        default: return '';
    }
}
function statusBadgeHvl(tt) {
    switch (parseInt(tt,10)) {
        case 0: return '<span class="badge badge-warning">Chờ duyệt</span>';
        case 1: return '<span class="badge badge-success">Đang học</span>';
        case 2: return '<span class="badge badge-info">Hoàn thành</span>';
        case 3: return '<span class="badge badge-danger">Bỏ học</span>';
        default: return '';
    }
}

// ====== Load list lớp ======
function loadStats() {
    APP.ajax(URL, {action:'getStats'}).done(function(res){
        if (!res.success) return;
        $('#stTotal').text(res.data.total||0);
        $('#stChoMo').text(res.data.cho_mo||0);
        $('#stDangHoc').text(res.data.dang_hoc||0);
        $('#stKetThuc').text(res.data.ket_thuc||0);
    });
}

function load() {
    APP.showLoading('#tableWrap');
    APP.ajax(URL, {
        action:'getPaged', page:state.page, pageSize:state.pageSize,
        search:state.search, da_xoa:state.daXoa,
        khoa_hoc_id:state.khoaId, trang_thai:state.trangThai
    }).done(function(res){
        APP.hideLoading('#tableWrap');
        if (!res.success) { APP.toast(res.message,'error'); return; }
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
    rows.forEach(function(r){
        stt++;
        var pct = Math.min(100, Math.round((r.so_hoc_vien / r.so_luong_toi_da) * 100));
        var pctClass = pct >= 100 ? 'full' : (pct >= 80 ? 'warn' : '');
        var tg = '';
        if (r.ngay_bat_dau || r.ngay_ket_thuc) {
            tg = (r.ngay_bat_dau ? APP.formatDate(r.ngay_bat_dau) : '?') + ' → ' + (r.ngay_ket_thuc ? APP.formatDate(r.ngay_ket_thuc) : '?');
        } else tg = '-';

        var actions = '';
        if (state.daXoa == 0) {
            actions += '<button class="btn btn-sm btn-primary" title="Học viên" onclick="openDrawer('+r.id+')">' + ICON_USERS + '</button>';
            if (CAN_EDIT) actions += '<button class="btn btn-sm" title="Sửa" onclick="openEdit('+r.id+')">' + ICON_EDIT + '</button>';
            if (CAN_DEL) actions += '<button class="btn btn-sm btn-danger" title="Xóa" onclick="trashItem('+r.id+')">' + ICON_TRASH + '</button>';
        } else {
            if (CAN_EDIT) actions += '<button class="btn btn-sm btn-success" onclick="restoreItem('+r.id+')">↺ Khôi phục</button>';
            if (CAN_DEL) actions += '<button class="btn btn-sm btn-danger" onclick="deleteItem('+r.id+')">Xóa</button>';
        }

        $tb.append(
            '<tr>'+
                '<td class="text-center">'+stt+'</td>'+
                '<td><strong>'+APP.escape(r.ma_lop)+'</strong></td>'+
                '<td><div style="font-weight:500">'+APP.escape(r.ten_lop)+'</div>'+
                    (r.dia_diem?'<div class="text-muted" style="font-size:12px">📍 '+APP.escape(r.dia_diem)+'</div>':'')+'</td>'+
                '<td><div style="font-size:13px">'+APP.escape(r.ma_khoa_hoc||'')+'</div><div class="text-muted" style="font-size:11.5px">'+APP.escape(r.ten_khoa_hoc||'')+'</div></td>'+
                '<td style="font-size:12.5px">'+tg+'</td>'+
                '<td><div class="lop-capacity '+pctClass+'">'+
                    '<div class="lop-capacity-fill" style="width:'+pct+'%"></div>'+
                    '<span class="lop-capacity-text">'+r.so_hoc_vien+' / '+r.so_luong_toi_da+'</span>'+
                '</div></td>'+
                '<td class="text-center">'+statusBadgeLop(r.trang_thai)+'</td>'+
                '<td><div class="actions">'+actions+'</div></td>'+
            '</tr>'
        );
    });
}

function renderInfo(p) {
    var from = (p.currentPage-1)*p.pageSize + 1;
    var to = Math.min(from+p.pageSize-1, p.totalRecords);
    $('#pageInfo').text(p.totalRecords ? 'Hiển thị '+from+'-'+to+' / '+p.totalRecords : 'Không có bản ghi');
    $('#pageNav').html(APP.renderPagination(p));
}

$('#pageNav').on('click', 'button[data-p]', function(){
    var p = parseInt($(this).data('p'),10); if (!p||p===state.page) return;
    state.page = p; load();
});
$('#search').on('input', APP.debounce(function(){ state.search=$(this).val(); state.page=1; load(); },400));
$('#filterKhoa').on('change', function(){ state.khoaId=parseInt(this.value,10)||0; state.page=1; load(); });
$('#filterTrangThai').on('change', function(){ state.trangThai=this.value; state.page=1; load(); });
$('#filterDaXoa').on('change', function(){ state.daXoa=parseInt(this.value,10)||0; state.page=1; load(); });

// ====== Modal Form Lớp ======
function openCreate() {
    $('#modalTitle').text('Thêm lớp học');
    $('#formLop')[0].reset(); $('#f_id').val('');
    $('#modalForm').addClass('open');
}
function openEdit(id) {
    APP.ajax(URL, {action:'getById', id:id}).done(function(res){
        if (!res.success) { APP.toast(res.message,'error'); return; }
        var e = res.data;
        $('#modalTitle').text('Sửa lớp học');
        $('#f_id').val(e.id);
        $('#f_ma_lop').val(e.ma_lop);
        $('#f_ten_lop').val(e.ten_lop);
        $('#f_khoa_hoc_id').val(e.khoa_hoc_id);
        $('#f_ngay_bat_dau').val(e.ngay_bat_dau || '');
        $('#f_ngay_ket_thuc').val(e.ngay_ket_thuc || '');
        $('#f_so_luong_toi_da').val(e.so_luong_toi_da);
        $('#f_dia_diem').val(e.dia_diem || '');
        $('#f_mo_ta').val(e.mo_ta || '');
        $('#f_trang_thai').val(e.trang_thai);
        $('#modalForm').addClass('open');
    });
}
function closeModal() { $('#modalForm').removeClass('open'); }

$('#formLop').on('submit', function(e){
    e.preventDefault();
    var data = $(this).serializeArray();
    data.push({name:'action', value: $('#f_id').val() ? 'update' : 'insert'});
    APP.ajax(URL, data).done(function(res){
        if (res.success) { APP.toast(res.message,'success'); closeModal(); load(); loadStats(); }
        else APP.toast(res.message,'error');
    });
});

function trashItem(id) {
    APP.confirm('Chuyển lớp này vào thùng rác?', function(){
        APP.ajax(URL, {action:'trash', id:id}).done(function(res){
            res.success ? (APP.toast(res.message,'success'), load(), loadStats()) : APP.toast(res.message,'error');
        });
    });
}
function restoreItem(id) {
    APP.ajax(URL, {action:'restore', id:id}).done(function(res){
        res.success ? (APP.toast(res.message,'success'), load(), loadStats()) : APP.toast(res.message,'error');
    });
}
function deleteItem(id) {
    APP.confirm('Xóa VĨNH VIỄN lớp này?', function(){
        APP.ajax(URL, {action:'delete', id:id}).done(function(res){
            res.success ? (APP.toast(res.message,'success'), load(), loadStats()) : APP.toast(res.message,'error');
        });
    }, {yesText:'Xóa vĩnh viễn'});
}

// ====== Drawer Học viên của lớp ======
function openDrawer(lopId) {
    APP.ajax(URL, {action:'getById', id:lopId}).done(function(res){
        if (!res.success) { APP.toast(res.message,'error'); return; }
        currentLop = res.data;
        $('#drawerLopTen').text(currentLop.ten_lop + ' (' + currentLop.ma_lop + ')');
        var meta = [];
        if (currentLop.ten_khoa_hoc) meta.push('Khóa: ' + currentLop.ten_khoa_hoc);
        meta.push('Tối đa: ' + currentLop.so_luong_toi_da + ' HV');
        $('#drawerLopMeta').text(meta.join(' · '));
        $('#hvlSearch').val('');
        loadHvl();
        $('#drawerHV').addClass('open').find('.drawer').addClass('open');
    });
}
function closeDrawer() { $('#drawerHV').removeClass('open').find('.drawer').removeClass('open'); currentLop = null; load(); loadStats(); }

function loadHvl() {
    if (!currentLop) return;
    APP.showLoading('#hvlList');
    APP.ajax(URL, {action:'hvl_list', lop_hoc_id: currentLop.id, search: $('#hvlSearch').val()}).done(function(res){
        APP.hideLoading('#hvlList');
        if (!res.success) { APP.toast(res.message,'error'); return; }
        renderHvlList(res.data);
        renderCapacity(res.data.length);
    });
}

function renderCapacity(count) {
    if (!currentLop) return;
    var max = currentLop.so_luong_toi_da || 1;
    var pct = Math.min(100, Math.round((count/max)*100));
    var cls = count >= max ? 'full' : (pct >= 80 ? 'warn' : '');
    $('#hvlCapacityBar').show().attr('class', 'hv-capacity ' + cls);
    $('#hvlCapacityBar .hv-capacity-fill').css('width', pct + '%');
    $('#hvlCapacityBar .hv-capacity-label').text(count + ' / ' + max + ' học viên');
}

function renderHvlList(rows) {
    var $w = $('#hvlList').empty();
    if (!rows.length) {
        $w.html('<div class="empty-state" style="padding:40px 20px"><div class="icon">' + ICON_EMPTY_HVL + '</div>Chưa có học viên nào trong lớp</div>');
        return;
    }
    rows.forEach(function(r){
        var nvChip = r.la_nhan_vien == 1 ? '<span class="hv-chip hv-chip-blue">NV '+APP.escape(r.ma_nv||'')+'</span>' : '<span class="hv-chip hv-chip-gray">Ngoài</span>';
        var dtChip = r.ten_doi_tuong ? '<span class="hv-chip hv-chip-purple">'+APP.escape(r.ten_doi_tuong)+'</span>' : '';
        var diem = r.diem_tong_ket !== null && r.diem_tong_ket !== undefined ? '<div class="hv-score">'+parseFloat(r.diem_tong_ket).toFixed(1)+'</div>' : '';
        var xl = r.xep_loai ? '<div class="hv-rank">'+APP.escape(r.xep_loai)+'</div>' : '';
        var actions = '';
        if (CAN_HVL_EDIT) actions += '<button class="btn btn-sm" title="Sửa ghi danh" onclick="openHvlEdit('+r.id+')">Sửa</button>';
        if (CAN_HVL_DEL) actions += '<button class="btn btn-sm btn-danger" title="Xóa khỏi lớp" onclick="removeHvl('+r.id+')">Xóa</button>';

        $w.append(
            '<div class="hv-student-row">'+
                hvAvatar(r, 44) +
                '<div class="hv-student-main">'+
                    '<div class="hv-student-name">'+APP.escape(r.ho_ten)+'</div>'+
                    '<div class="hv-student-code">'+APP.escape(r.ma_hv)+'</div>'+
                    '<div class="hv-chips" style="margin-top:4px">'+nvChip+' '+dtChip+' '+statusBadgeHvl(r.trang_thai)+'</div>'+
                '</div>'+
                '<div class="hv-student-score">'+diem+xl+'</div>'+
                '<div class="actions">'+actions+'</div>'+
            '</div>'
        );
    });
}

$('#hvlSearch').on('input', APP.debounce(loadHvl, 300));

function removeHvl(id) {
    APP.confirm('Xóa học viên này khỏi lớp?', function(){
        APP.ajax(URL, {action:'hvl_delete', id:id}).done(function(res){
            res.success ? (APP.toast(res.message,'success'), loadHvl()) : APP.toast(res.message,'error');
        });
    });
}

// ====== Modal Sửa HVL ======
function openHvlEdit(id) {
    APP.ajax(URL, {action:'hvl_getById', id:id}).done(function(res){
        if (!res.success) { APP.toast(res.message,'error'); return; }
        var e = res.data;
        $('#hf_id').val(e.id);
        $('#hf_ngay_ghi_danh').val(e.ngay_ghi_danh || '');
        $('#hf_trang_thai').val(e.trang_thai);
        $('#hf_diem').val(e.diem_tong_ket === null ? '' : e.diem_tong_ket);
        $('#hf_xep_loai').val(e.xep_loai || '');
        $('#hf_ghi_chu').val(e.ghi_chu || '');
        $('#hf_summary').html(
            hvAvatar(e, 44) +
            '<div><div style="font-weight:600">'+APP.escape(e.ho_ten)+'</div>'+
            '<div class="text-muted" style="font-size:12.5px">'+APP.escape(e.ma_hv)+'</div></div>'
        );
        $('#modalHvlEdit').addClass('open');
    });
}
function closeHvlEdit() { $('#modalHvlEdit').removeClass('open'); }

$('#formHvl').on('submit', function(e){
    e.preventDefault();
    var data = $(this).serializeArray();
    data.push({name:'action', value:'hvl_update'});
    APP.ajax(URL, data).done(function(res){
        if (res.success) { APP.toast(res.message,'success'); closeHvlEdit(); loadHvl(); }
        else APP.toast(res.message,'error');
    });
});

// ====== Modal Picker ======
function openPicker() {
    if (!currentLop) return;
    pickerSelected = {};
    $('#pickerSearch').val('');
    loadPickerList();
    $('#modalPicker').addClass('open');
}
function closePicker() { $('#modalPicker').removeClass('open'); }

function loadPickerList() {
    APP.showLoading('#pickerList');
    APP.ajax(URL, {action:'hvl_available', lop_hoc_id: currentLop.id, search: $('#pickerSearch').val()}).done(function(res){
        APP.hideLoading('#pickerList');
        if (!res.success) { APP.toast(res.message,'error'); return; }
        renderPicker(res.data);
    });
}
$('#pickerSearch').on('input', APP.debounce(loadPickerList, 300));

function renderPicker(rows) {
    var $w = $('#pickerList').empty();
    if (!rows.length) {
        $w.html('<div class="empty-state" style="padding:30px"><div class="icon">🔍</div>Không có học viên phù hợp (có thể tất cả đã được ghi danh)</div>');
        updatePickerCount();
        return;
    }
    rows.forEach(function(r){
        var checked = pickerSelected[r.id] ? 'checked' : '';
        var nvChip = r.la_nhan_vien == 1 ? '<span class="hv-chip hv-chip-blue">NV '+APP.escape(r.ma_nv||'')+'</span>' : '<span class="hv-chip hv-chip-gray">Ngoài</span>';
        var dt = r.ten_doi_tuong ? '<span class="hv-chip hv-chip-purple">'+APP.escape(r.ten_doi_tuong)+'</span>' : '';
        $w.append(
            '<label class="hv-picker-row '+(checked?'selected':'')+'">'+
                '<input type="checkbox" value="'+r.id+'" '+checked+'>'+
                hvAvatar(r, 38) +
                '<div class="hv-picker-main">'+
                    '<div class="hv-picker-name">'+APP.escape(r.ho_ten)+'</div>'+
                    '<div class="hv-picker-meta">'+APP.escape(r.ma_hv)+' · '+nvChip+' '+dt+'</div>'+
                '</div>'+
            '</label>'
        );
    });
    updatePickerCount();
}

$('#pickerList').on('change', 'input[type=checkbox]', function(){
    var id = parseInt(this.value,10);
    if (this.checked) pickerSelected[id] = true; else delete pickerSelected[id];
    $(this).closest('.hv-picker-row').toggleClass('selected', this.checked);
    updatePickerCount();
});

function updatePickerCount() {
    var n = Object.keys(pickerSelected).length;
    $('#pickerSelCount').text(n + ' đã chọn');
}

function submitPicker() {
    var ids = Object.keys(pickerSelected).map(Number);
    if (!ids.length) { APP.toast('Chưa chọn học viên nào','warning'); return; }
    APP.ajax(URL, {action:'hvl_bulk_add', lop_hoc_id: currentLop.id, 'hoc_vien_ids[]': ids}, {traditional:true})
      .done(function(res){
        if (res.success) { APP.toast(res.message,'success'); closePicker(); loadHvl(); }
        else APP.toast(res.message,'error');
    });
}

// Init
load();
loadStats();
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
