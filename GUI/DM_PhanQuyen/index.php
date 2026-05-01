<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../BUS/DM_NhomTaiKhoan_BUS.php';

Helper::requireLogin();
if (!PhanQuyenHelper::hasQuyen('DM_PhanQuyen', PhanQuyenHelper::QUYEN_XEM)) {
    echo 'Bạn không có quyền truy cập chức năng này.'; exit;
}

$canEdit = PhanQuyenHelper::hasQuyen('DM_PhanQuyen', PhanQuyenHelper::QUYEN_SUA);
$nhomCombo = DM_NhomTaiKhoan_BUS::getCombo();

$pageTitle = 'Phân quyền';
$activeMenu = 'DM_PhanQuyen';
require __DIR__ . '/../layouts/header.php';
?>

<div class="breadcrumb">
    <a href="<?= AppConfig::baseUrl('GUI/dashboard/index.php') ?>">Trang chủ</a>
    <span class="sep">›</span> Hệ thống
    <span class="sep">›</span> <span>Phân quyền</span>
</div>

<div class="card">
    <div class="toolbar">
        <div class="left">
            <label style="display:flex;align-items:center;gap:8px;margin:0">
                <strong>Nhóm tài khoản:</strong>
                <select id="selNhom" class="form-select" style="min-width:240px">
                    <option value="0">-- Chọn nhóm --</option>
                    <?php foreach ($nhomCombo as $n): ?>
                        <option value="<?= $n['id'] ?>"><?= Helper::h($n['ten_nhom']) ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
        </div>
        <div class="right" id="btnBar" style="display:none">
            <?php if ($canEdit): ?>
                <button type="button" class="btn btn-success" onclick="grantAll()"><svg class="icon icon-checkmark" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"></polyline></svg> Cấp toàn quyền</button>
                <button type="button" class="btn btn-primary" onclick="saveMatrix()"><svg class="icon icon-save" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg> Lưu phân quyền</button>
            <?php endif; ?>
        </div>
    </div>

    <div id="adminWarn" class="alert" style="display:none;padding:12px 16px;background:#fef3c7;border-left:4px solid #f59e0b;margin:12px 0;border-radius:6px">
        <div class="card-body alert alert-warning" role="alert">
            <svg class="icon icon-warning" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" style="vertical-align: middle; margin-right: 8px;"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3.05h16.94a2 2 0 0 0 1.71-3.05L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
            Nhóm <strong>Admin</strong> (id=1) mặc định có toàn quyền với mọi chức năng — không cần/không nên sửa đổi.
        </div>
    </div>

    <div class="table-wrap" id="tableWrap" style="position:relative;min-height:200px">
        <table class="table" id="tblMatrix">
            <thead>
                <tr>
                    <th style="width:50px" class="text-center">#</th>
                    <th>Module</th>
                    <th>Tên form</th>
                    <th class="text-center" style="width:90px"><svg class="icon icon-eye" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg> Xem<br><label style="font-size:11px;font-weight:400"><input type="checkbox" class="col-all" data-col="xem"> tất cả</label></th>
                    <th class="text-center" style="width:90px"><svg class="icon icon-plus" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg> Thêm<br><label style="font-size:11px;font-weight:400"><input type="checkbox" class="col-all" data-col="them"> tất cả</label></th>
                    <th class="text-center" style="width:90px"><svg class="icon icon-edit" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg> Sửa<br><label style="font-size:11px;font-weight:400"><input type="checkbox" class="col-all" data-col="sua"> tất cả</label></th>
                    <th class="text-center" style="width:90px"><svg class="icon icon-trash" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg> Xóa<br><label style="font-size:11px;font-weight:400"><input type="checkbox" class="col-all" data-col="xoa"> tất cả</label></th>
                </tr>
            </thead>
            <tbody id="tbody">
                <tr><td colspan="7"><div class="empty-state"><div class="icon"><svg class="icon icon-key" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="7.5" cy="15.5" r="5.5"></circle><path d="M21 2l-9.6 9.6"></path><path d="m15.5 7l3.5-3.5"></path></svg></div>Chọn nhóm tài khoản để xem phân quyền</div></td></tr>
            </tbody>
        </table>
    </div>
</div>

<script>
var URL = APP_BASE + 'GUI/DM_PhanQuyen/ajax_handler.php';
var CAN_EDIT = <?= $canEdit?'true':'false' ?>;
var currentNhom = 0;

$('#selNhom').on('change', function () {
    currentNhom = parseInt(this.value, 10) || 0;
    if (!currentNhom) {
        $('#tbody').html('<tr><td colspan="7"><div class="empty-state"><div class="icon"><svg class="icon icon-key" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="7.5" cy="15.5" r="5.5"></circle><path d="M21 2l-9.6 9.6"></path><path d="m15.5 7l3.5-3.5"></path></svg></div>Chọn nhóm tài khoản để xem phân quyền</div></td></tr>');
        $('#btnBar').hide();
        $('#adminWarn').hide();
        return;
    }
    $('#adminWarn').toggle(currentNhom === 1);
    $('#btnBar').show();
    loadMatrix();
});

function loadMatrix() {
    APP.showLoading('#tableWrap');
    APP.ajax(URL, {action: 'getMatrix', nhom_tai_khoan_id: currentNhom}).done(function (res) {
        APP.hideLoading('#tableWrap');
        if (!res.success) { APP.toast(res.message, 'error'); return; }
        renderMatrix(res.data.forms, res.data.permissions);
    });
}

function renderMatrix(forms, perms) {
    var $tb = $('#tbody').empty();
    if (!forms.length) {
        $tb.append('<tr><td colspan="7"><div class="empty-state"><div class="icon"><svg class="icon icon-empty" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="8"></circle><path d="m21 21-4.35-4.35"></path></svg></div>Chưa có form nào</div></td></tr>');
        return;
    }
    forms.forEach(function (f, i) {
        var p = perms[f.id] || {xem: 0, them: 0, sua: 0, xoa: 0};
        var dis = CAN_EDIT ? '' : 'disabled';
        $tb.append(
            '<tr>' +
                '<td class="text-center">' + (i + 1) + '</td>' +
                '<td><code>' + APP.escape(f.modules_tuong_ung) + '</code></td>' +
                '<td>' + APP.escape(f.ten_form) + '</td>' +
                cb(f.id, 'xem', p.xem, dis) +
                cb(f.id, 'them', p.them, dis) +
                cb(f.id, 'sua', p.sua, dis) +
                cb(f.id, 'xoa', p.xoa, dis) +
            '</tr>'
        );
    });
}

function cb(formId, q, val, dis) {
    return '<td class="text-center"><input type="checkbox" class="pq-cb" data-form="' + formId + '" data-q="' + q + '" ' +
           (val ? 'checked' : '') + ' ' + dis + '></td>';
}

$('#tbody').on('change', '.pq-cb', function () {
    var q = $(this).data('q');
    var checked = this.checked;
    var $row = $(this).closest('tr');
    // Auto-tick "xem" khi tick them/sua/xoa
    if (checked && q !== 'xem') {
        $row.find('.pq-cb[data-q="xem"]').prop('checked', true);
    }
    // Nếu bỏ tick "xem" thì bỏ luôn them/sua/xoa
    if (!checked && q === 'xem') {
        $row.find('.pq-cb[data-q="them"],.pq-cb[data-q="sua"],.pq-cb[data-q="xoa"]').prop('checked', false);
    }
});

$('.col-all').on('change', function () {
    var col = $(this).data('col');
    var checked = this.checked;
    $('#tbody .pq-cb[data-q="' + col + '"]').each(function () {
        if (this.disabled) return;
        this.checked = checked;
        $(this).trigger('change');
    });
});

function saveMatrix() {
    if (!currentNhom) return;
    var data = {action: 'save', nhom_tai_khoan_id: currentNhom};
    $('#tbody .pq-cb').each(function () {
        var $cb = $(this);
        var formId = $cb.data('form');
        var q = $cb.data('q');
        data['permissions[' + formId + '][' + q + ']'] = this.checked ? 1 : 0;
    });
    APP.ajax(URL, data).done(function (res) {
        res.success ? APP.toast(res.message, 'success') : APP.toast(res.message, 'error');
    });
}

function grantAll() {
    if (!currentNhom) return;
    APP.confirm('Cấp TOÀN QUYỀN (xem/thêm/sửa/xóa mọi form) cho nhóm này?', function () {
        APP.ajax(URL, {action: 'grantAll', nhom_tai_khoan_id: currentNhom}).done(function (res) {
            if (res.success) { APP.toast(res.message, 'success'); loadMatrix(); }
            else APP.toast(res.message, 'error');
        });
    });
}
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
