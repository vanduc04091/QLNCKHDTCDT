<?php
require_once __DIR__ . '/../../bootstrap.php';

Helper::requireLogin();
if (!PhanQuyenHelper::hasQuyen('NCKH_NhacViec', PhanQuyenHelper::QUYEN_XEM)) {
    echo 'Bạn không có quyền truy cập chức năng này.'; exit;
}
$canAdd = PhanQuyenHelper::hasQuyen('NCKH_NhacViec', PhanQuyenHelper::QUYEN_THEM);
$canEdit = PhanQuyenHelper::hasQuyen('NCKH_NhacViec', PhanQuyenHelper::QUYEN_SUA);
$canDel = PhanQuyenHelper::hasQuyen('NCKH_NhacViec', PhanQuyenHelper::QUYEN_XOA);

$pageTitle = 'Nhắc việc NCKH';
$activeMenu = 'NCKH_NhacViec';
require __DIR__ . '/../layouts/header.php';
?>
<div class="breadcrumb">
    <a href="<?= AppConfig::baseUrl('GUI/dashboard/index.php') ?>">Trang chủ</a>
    <span class="sep">›</span> Nghiên cứu khoa học
    <span class="sep">›</span> <span>Nhắc việc</span>
</div>

<div class="card">
    <div class="toolbar">
        <div class="left" style="gap:8px;display:flex">
            <select id="fGui" class="form-select" style="width:160px">
                <option value="">Tất cả</option>
                <option value="0" selected>Chưa gửi</option>
                <option value="1">Đã gửi</option>
            </select>
        </div>
        <div class="right">
            <?php if ($canAdd): ?>
                <button type="button" class="btn btn-primary" onclick="openCreate()">+ Thêm nhắc việc</button>
            <?php endif; ?>
        </div>
    </div>
    <div class="table-wrap" id="tableWrap" style="position:relative;min-height:200px">
        <table class="table">
            <thead>
                <tr>
                    <th style="width:50px" class="text-center">#</th>
                    <th style="width:140px">Loại</th>
                    <th>Tiêu đề</th>
                    <th style="width:200px">Đề tài</th>
                    <th style="width:170px">Người nhận</th>
                    <th style="width:140px">Ngày nhắc</th>
                    <th style="width:110px" class="text-center">Trạng thái</th>
                    <th style="width:120px" class="text-right">Hành động</th>
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
    <div class="modal" style="max-width:700px">
        <div class="modal-header">
            <h3 id="modalTitle">Thêm nhắc việc</h3>
            <button type="button" class="close" onclick="closeModal()">&times;</button>
        </div>
        <form id="formMain">
            <div class="modal-body">
                <input type="hidden" name="id" id="f_id">
                <div class="form-row">
                    <div class="form-group">
                        <label>Đề tài <span class="required">*</span></label>
                        <select name="de_tai_id" id="f_dt" class="form-select" required></select>
                    </div>
                    <div class="form-group">
                        <label>Loại nhắc</label>
                        <select name="loai_nhac" id="f_loai" class="form-select">
                            <option value="TienDo">Tiến độ</option>
                            <option value="DeadLine">Deadline</option>
                            <option value="NghiemThu">Nghiệm thu</option>
                            <option value="Khac">Khác</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label>Tiêu đề <span class="required">*</span></label>
                    <input type="text" name="tieu_de" id="f_td" class="form-control" required maxlength="255">
                </div>
                <div class="form-group">
                    <label>Nội dung</label>
                    <textarea name="noi_dung" id="f_nd" class="form-control" rows="3"></textarea>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Ngày giờ nhắc <span class="required">*</span></label>
                        <input type="datetime-local" name="ngay_nhac" id="f_ng" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Người nhận</label>
                        <select name="nguoi_nhan_id" id="f_nn" class="form-select"><option value="">-</option></select>
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
var URL = APP_BASE + 'GUI/NCKH_NhacViec/ajax_handler.php';
var CAN_EDIT = <?= $canEdit?'true':'false' ?>;
var CAN_DEL = <?= $canDel?'true':'false' ?>;
var ICON_EDIT = <?= json_encode(IconHelper::svg('edit', 18, 'icon', 'currentColor')) ?>;
var ICON_TRASH = <?= json_encode(IconHelper::svg('trash', 18, 'icon', 'currentColor')) ?>;
var LOAI_NAMES = {TienDo:'Tiến độ', DeadLine:'Deadline', NghiemThu:'Nghiệm thu', Khac:'Khác'};
var state = { page:1, pageSize:20, daGui:'0' };

function loadCombos() {
    return $.when(
        APP.ajax(URL,{action:'getComboDeTai'}),
        APP.ajax(URL,{action:'getComboNhanVien', kw:''})
    ).done(function (dt, nv) {
        $.each(dt[0].data || [], function (_, x) { $('#f_dt').append('<option value="' + x.id + '">' + APP.escape(x.ma_de_tai + ' - ' + x.ten_de_tai) + '</option>'); });
        $.each(nv[0].data || [], function (_, x) { $('#f_nn').append('<option value="' + x.id + '">' + APP.escape((x.ma_nv?'['+x.ma_nv+'] ':'') + x.ho_ten) + '</option>'); });
    });
}

function load() {
    APP.showLoading('#tableWrap');
    APP.ajax(URL, {action:'getPaged', page:state.page, pageSize:state.pageSize, da_gui:state.daGui}).done(function (res) {
        APP.hideLoading('#tableWrap');
        if (!res.success) { APP.toast(res.message,'error'); return; }
        renderRows(res.data); renderInfo(res.pagination);
    });
}

function renderRows(rows) {
    var $tb = $('#tbody').empty();
    if (!rows.length) { $tb.append('<tr><td colspan="8"><div class="empty-state">Không có nhắc việc</div></td></tr>'); return; }
    var stt = (state.page - 1) * state.pageSize;
    rows.forEach(function (r) {
        stt++;
        var tt = r.da_gui ? '<span class="badge badge-success">Đã gửi</span>' : '<span class="badge">Chưa gửi</span>';
        var actions = '';
        if (CAN_EDIT) actions += '<button class="btn btn-sm" onclick="openEdit(' + r.id + ')">' + ICON_EDIT + '</button>';
        if (CAN_DEL) actions += '<button class="btn btn-sm btn-danger" onclick="trashItem(' + r.id + ')">' + ICON_TRASH + '</button>';
        $tb.append(
            '<tr>' +
                '<td class="text-center">' + stt + '</td>' +
                '<td><span class="badge">' + (LOAI_NAMES[r.loai_nhac] || r.loai_nhac) + '</span></td>' +
                '<td>' + APP.escape(r.tieu_de) + (r.noi_dung ? '<div class="text-muted" style="font-size:12px">' + APP.escape(r.noi_dung) + '</div>' : '') + '</td>' +
                '<td><div style="font-size:12px"><strong>' + APP.escape(r.ma_de_tai || '') + '</strong></div><div class="text-muted" style="font-size:12px">' + APP.escape(r.ten_de_tai || '') + '</div></td>' +
                '<td>' + APP.escape(r.ho_ten_nguoi_nhan || '-') + (r.email_nguoi_nhan ? '<div class="text-muted" style="font-size:11px">' + APP.escape(r.email_nguoi_nhan) + '</div>' : '') + '</td>' +
                '<td>' + APP.formatDateTime(r.ngay_nhac) + '</td>' +
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
$('#fGui').on('change', function () { state.daGui = this.value; state.page = 1; load(); });

function openCreate() {
    $('#modalTitle').text('Thêm nhắc việc'); $('#formMain')[0].reset(); $('#f_id').val('');
    $('#modalForm').addClass('open');
}
function openEdit(id) {
    APP.ajax(URL, {action:'getById', id:id}).done(function (res) {
        if (!res.success) return; var e = res.data;
        $('#modalTitle').text('Sửa nhắc việc');
        $('#f_id').val(e.id); $('#f_dt').val(e.de_tai_id); $('#f_loai').val(e.loai_nhac);
        $('#f_td').val(e.tieu_de); $('#f_nd').val(e.noi_dung || '');
        $('#f_ng').val((e.ngay_nhac || '').replace(' ','T').slice(0,16));
        $('#f_nn').val(e.nguoi_nhan_id || '');
        $('#modalForm').addClass('open');
    });
}
function closeModal() { $('#modalForm').removeClass('open'); }

$('#formMain').on('submit', function (e) {
    e.preventDefault();
    var data = $(this).serializeArray();
    data.push({name:'action', value: $('#f_id').val() ? 'update' : 'insert'});
    APP.ajax(URL, data).done(function (r) {
        if (r.success) { APP.toast(r.message,'success'); closeModal(); load(); }
        else APP.toast(r.message,'error');
    });
});

function trashItem(id) { APP.confirm('Xóa nhắc việc?', function () { APP.ajax(URL,{action:'trash', id:id}).done(function(r){ APP.toast(r.message,'success'); load(); }); }); }

loadCombos().done(function(){ load(); });
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
