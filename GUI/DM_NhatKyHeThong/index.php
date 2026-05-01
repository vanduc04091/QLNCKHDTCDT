<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../BUS/DM_NhatKyHeThong_BUS.php';

Helper::requireLogin();
if (!PhanQuyenHelper::hasQuyen('DM_NhatKyHeThong', PhanQuyenHelper::QUYEN_XEM)) {
    echo 'Bạn không có quyền truy cập chức năng này.'; exit;
}

$canDel = PhanQuyenHelper::hasQuyen('DM_NhatKyHeThong', PhanQuyenHelper::QUYEN_XOA);
$modules = DM_NhatKyHeThong_BUS::getModuleList();

$pageTitle = 'Nhật ký hệ thống';
$activeMenu = 'DM_NhatKyHeThong';
require __DIR__ . '/../layouts/header.php';
?>

<div class="breadcrumb">
    <a href="<?= AppConfig::baseUrl('GUI/dashboard/index.php') ?>">Trang chủ</a>
    <span class="sep">›</span> Nhật ký
    <span class="sep">›</span> <span>Nhật ký hệ thống</span>
</div>

<div class="card">
    <div class="toolbar" style="flex-wrap:wrap;gap:10px">
        <div class="left" style="flex-wrap:wrap;gap:10px">
            <input type="text" id="search" class="form-control" placeholder="Hành động, bảng, IP, tài khoản..." style="max-width:300px">
            <select id="filterModule" class="form-select" style="max-width:180px">
                <option value="">-- Tất cả module --</option>
                <?php foreach ($modules as $m): ?>
                    <option value="<?= Helper::h($m) ?>"><?= Helper::h($m) ?></option>
                <?php endforeach; ?>
            </select>
            <input type="date" id="fromDate" class="form-control" style="max-width:160px" title="Từ ngày">
            <input type="date" id="toDate" class="form-control" style="max-width:160px" title="Đến ngày">
            <button type="button" class="btn" onclick="resetFilter()">↺ Reset</button>
        </div>
        <div class="right">
            <?php if ($canDel): ?>
                <button type="button" class="btn btn-danger" onclick="openPurge()">🧹 Dọn log cũ</button>
            <?php endif; ?>
        </div>
    </div>
    <div class="table-wrap" id="tableWrap" style="position:relative;min-height:200px">
        <table class="table">
            <thead>
                <tr>
                    <th style="width:50px" class="text-center">#</th>
                    <th style="width:160px">Thời gian</th>
                    <th style="width:140px">Tài khoản</th>
                    <th style="width:110px">Module</th>
                    <th>Hành động</th>
                    <th style="width:160px">Bảng liên quan</th>
                    <th style="width:120px">IP</th>
                    <th style="width:70px" class="text-center">Xem</th>
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

<!-- Modal chi tiết -->
<div class="modal-backdrop" id="modalDetail">
    <div class="modal" style="max-width:720px">
        <div class="modal-header">
            <h3>Chi tiết nhật ký</h3>
            <button type="button" class="close" onclick="closeDetail()">&times;</button>
        </div>
        <div class="modal-body" id="detailBody"></div>
        <div class="modal-footer">
            <button type="button" class="btn" onclick="closeDetail()">Đóng</button>
        </div>
    </div>
</div>

<!-- Modal purge -->
<?php if ($canDel): ?>
<div class="modal-backdrop" id="modalPurge">
    <div class="modal" style="max-width:460px">
        <div class="modal-header">
            <h3>Dọn log cũ</h3>
            <button type="button" class="close" onclick="closePurge()">&times;</button>
        </div>
        <div class="modal-body">
            <p>Xóa tất cả log cũ hơn số ngày nhập bên dưới. Thao tác <strong>không thể phục hồi</strong>.</p>
            <div class="form-group">
                <label>Xóa log cũ hơn (ngày)</label>
                <input type="number" id="purgeDays" class="form-control" value="90" min="7" max="3650">
                <small class="text-muted">Tối thiểu 7 ngày</small>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn" onclick="closePurge()">Hủy</button>
            <button type="button" class="btn btn-danger" onclick="doPurge()">Xóa</button>
        </div>
    </div>
</div>
<?php endif; ?>

<script>
var URL = APP_BASE + 'GUI/DM_NhatKyHeThong/ajax_handler.php';
var state = { page: 1, pageSize: 30, search: '', module: '', fromDate: '', toDate: '' };

function load() {
    APP.showLoading('#tableWrap');
    APP.ajax(URL, {
        action: 'getPaged',
        page: state.page, pageSize: state.pageSize,
        search: state.search, module: state.module,
        from_date: state.fromDate, to_date: state.toDate
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
        $tb.append('<tr><td colspan="8"><div class="empty-state"><div class="icon"><svg class="icon icon-empty" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="8"></circle><path d="m21 21-4.35-4.35"></path></svg></div>Không có nhật ký</div></td></tr>');
        return;
    }
    var stt = (state.page - 1) * state.pageSize;
    rows.forEach(function (r) {
        stt++;
        var who = r.tai_khoan ? APP.escape(r.tai_khoan) + (r.ho_ten ? '<br><small class="text-muted">' + APP.escape(r.ho_ten) + '</small>' : '') : '<span class="text-muted">Hệ thống</span>';
        var bang = r.bang_lien_quan ? APP.escape(r.bang_lien_quan) + (r.id_lien_quan ? ' #' + r.id_lien_quan : '') : '-';
        $tb.append(
            '<tr>' +
                '<td class="text-center">' + stt + '</td>' +
                '<td>' + APP.formatDateTime(r.thoi_gian) + '</td>' +
                '<td>' + who + '</td>' +
                '<td>' + (r.module ? '<span class="badge">' + APP.escape(r.module) + '</span>' : '-') + '</td>' +
                '<td>' + APP.escape(r.hanh_dong) + '</td>' +
                '<td><code>' + bang + '</code></td>' +
                '<td><small>' + APP.escape(r.dia_chi_ip || '-') + '</small></td>' +
                '<td class="text-center"><button class="btn btn-sm" onclick="openDetail(' + r.id + ')">👁</button></td>' +
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

$('#filterModule').on('change', function () { state.module = this.value; state.page = 1; load(); });
$('#fromDate, #toDate').on('change', function () {
    state.fromDate = $('#fromDate').val();
    state.toDate = $('#toDate').val();
    state.page = 1; load();
});

function resetFilter() {
    $('#search').val(''); $('#filterModule').val(''); $('#fromDate').val(''); $('#toDate').val('');
    state = { page: 1, pageSize: 30, search: '', module: '', fromDate: '', toDate: '' };
    load();
}

function openDetail(id) {
    APP.ajax(URL, {action: 'getById', id: id}).done(function (res) {
        if (!res.success) { APP.toast(res.message, 'error'); return; }
        var r = res.data;
        var rows = [
            ['Thời gian', APP.formatDateTime(r.thoi_gian)],
            ['Tài khoản', r.tai_khoan ? r.tai_khoan + (r.ho_ten ? ' — ' + r.ho_ten : '') : 'Hệ thống'],
            ['Module', r.module || '-'],
            ['Hành động', r.hanh_dong],
            ['Bảng liên quan', r.bang_lien_quan || '-'],
            ['ID liên quan', r.id_lien_quan || '-'],
            ['IP', r.dia_chi_ip || '-'],
            ['Nội dung thay đổi', r.noi_dung_thay_doi || '<em class="text-muted">(trống)</em>'],
        ];
        var html = '<table class="table">';
        rows.forEach(function (row) {
            html += '<tr><th style="width:160px">' + row[0] + '</th><td>' + (row[0] === 'Nội dung thay đổi' ? '<pre style="white-space:pre-wrap;margin:0">' + APP.escape(String(row[1])) + '</pre>' : APP.escape(String(row[1]))) + '</td></tr>';
        });
        html += '</table>';
        $('#detailBody').html(html);
        $('#modalDetail').addClass('open');
    });
}
function closeDetail() { $('#modalDetail').removeClass('open'); }

<?php if ($canDel): ?>
function openPurge() { $('#modalPurge').addClass('open'); }
function closePurge() { $('#modalPurge').removeClass('open'); }
function doPurge() {
    var days = parseInt($('#purgeDays').val(), 10) || 0;
    if (days < 7) { APP.toast('Tối thiểu 7 ngày', 'error'); return; }
    APP.confirm('Xóa VĨNH VIỄN các log cũ hơn ' + days + ' ngày?', function () {
        APP.ajax(URL, {action: 'purge', days: days}).done(function (res) {
            if (res.success) { APP.toast(res.message, 'success'); closePurge(); load(); }
            else APP.toast(res.message, 'error');
        });
    }, {yesText: 'Xóa'});
}
<?php endif; ?>

load();
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
