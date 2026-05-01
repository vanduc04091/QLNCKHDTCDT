<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../BUS/DM_LoaiHinhDaoTao_BUS.php';
require_once __DIR__ . '/../../BUS/DM_HinhThucHoc_BUS.php';
require_once __DIR__ . '/../../BUS/DM_DoiTuongHocVien_BUS.php';
require_once __DIR__ . '/../../BUS/DT_MonHoc_BUS.php';

Helper::requireLogin();
if (!PhanQuyenHelper::hasQuyen('DT_KhoaHoc', PhanQuyenHelper::QUYEN_XEM)) {
    echo 'Bạn không có quyền truy cập chức năng này.'; exit;
}

$canAdd = PhanQuyenHelper::hasQuyen('DT_KhoaHoc', PhanQuyenHelper::QUYEN_THEM);
$canEdit = PhanQuyenHelper::hasQuyen('DT_KhoaHoc', PhanQuyenHelper::QUYEN_SUA);
$canDel = PhanQuyenHelper::hasQuyen('DT_KhoaHoc', PhanQuyenHelper::QUYEN_XOA);

// Quyền liên kết môn học (module DT_KhoaHocMonHoc)
$canKhmView = PhanQuyenHelper::hasQuyen('DT_KhoaHocMonHoc', PhanQuyenHelper::QUYEN_XEM);
$canKhmAdd  = PhanQuyenHelper::hasQuyen('DT_KhoaHocMonHoc', PhanQuyenHelper::QUYEN_THEM);
$canKhmEdit = PhanQuyenHelper::hasQuyen('DT_KhoaHocMonHoc', PhanQuyenHelper::QUYEN_SUA);
$canKhmDel  = PhanQuyenHelper::hasQuyen('DT_KhoaHocMonHoc', PhanQuyenHelper::QUYEN_XOA);

$loaiHinhCombo = DM_LoaiHinhDaoTao_BUS::getCombo();
$hinhThucCombo = DM_HinhThucHoc_BUS::getCombo();
$doiTuongCombo = DM_DoiTuongHocVien_BUS::getCombo();
$monHocCombo   = DT_MonHoc_BUS::getCombo();

$pageTitle = 'Quản lý khóa học';
$activeMenu = 'DT_KhoaHoc';
require __DIR__ . '/../layouts/header.php';
?>

<div class="breadcrumb">
    <a href="<?= AppConfig::baseUrl('GUI/dashboard/index.php') ?>">Trang chủ</a>
    <span class="sep">›</span> Đào tạo
    <span class="sep">›</span> <span>Khóa học</span>
</div>

<div class="card">
    <div class="toolbar">
        <div class="left">
            <input type="text" id="search" class="form-control" placeholder="Tìm mã, tên khóa học..." style="max-width:280px">
            <select id="filterLoaiHinh" class="form-select" style="max-width:180px">
                <option value="0">-- Loại hình --</option>
                <?php foreach ($loaiHinhCombo as $r): ?>
                    <option value="<?= $r['id'] ?>"><?= Helper::h($r['ten_loai_hinh']) ?></option>
                <?php endforeach; ?>
            </select>
            <select id="filterHinhThuc" class="form-select" style="max-width:160px">
                <option value="0">-- Hình thức --</option>
                <?php foreach ($hinhThucCombo as $r): ?>
                    <option value="<?= $r['id'] ?>"><?= Helper::h($r['ten_hinh_thuc']) ?></option>
                <?php endforeach; ?>
            </select>
            <select id="filterDoiTuong" class="form-select" style="max-width:180px">
                <option value="0">-- Đối tượng --</option>
                <?php foreach ($doiTuongCombo as $r): ?>
                    <option value="<?= $r['id'] ?>"><?= Helper::h($r['ten_doi_tuong']) ?></option>
                <?php endforeach; ?>
            </select>
            <select id="filterDaXoa" class="form-select" style="max-width:140px">
                <option value="0">Đang hoạt động</option>
                <option value="1">Thùng rác</option>
            </select>
        </div>
        <div class="right">
            <?php if ($canAdd): ?>
                <button type="button" class="btn btn-primary" onclick="openCreate()">+ Thêm khóa học</button>
            <?php endif; ?>
        </div>
    </div>
    <div class="table-wrap" id="tableWrap" style="position:relative;min-height:200px">
        <table class="table">
            <thead>
                <tr>
                    <th style="width:50px" class="text-center">#</th>
                    <th style="width:120px">Mã</th>
                    <th>Tên khóa học</th>
                    <th>Loại hình</th>
                    <th>Hình thức</th>
                    <th>Đối tượng</th>
                    <th class="text-center" style="width:80px">Tổng tiết</th>
                    <th class="text-center" style="width:80px">Tín chỉ</th>
                    <th class="text-center" style="width:110px">Trạng thái</th>
                    <th style="width:180px" class="text-right">Hành động</th>
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
    <div class="modal modal-lg">
        <div class="modal-header">
            <h3 id="modalTitle">Thêm khóa học</h3>
            <button type="button" class="close" onclick="closeModal()">&times;</button>
        </div>
        <form id="formMain">
            <div class="modal-body">
                <input type="hidden" name="id" id="f_id">
                <div class="form-row">
                    <div class="form-group">
                        <label>Mã khóa học <span class="required">*</span></label>
                        <input type="text" name="ma_khoa_hoc" id="f_ma" class="form-control" required maxlength="50">
                    </div>
                    <div class="form-group">
                        <label>Trạng thái</label>
                        <select name="trang_thai" id="f_trang_thai" class="form-select">
                            <option value="1">Hoạt động</option>
                            <option value="0">Khóa</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label>Tên khóa học <span class="required">*</span></label>
                    <input type="text" name="ten_khoa_hoc" id="f_ten" class="form-control" required maxlength="200">
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Loại hình đào tạo</label>
                        <select name="loai_hinh_dao_tao_id" id="f_loai_hinh" class="form-select">
                            <option value="">-- Chọn --</option>
                            <?php foreach ($loaiHinhCombo as $r): ?>
                                <option value="<?= $r['id'] ?>"><?= Helper::h($r['ten_loai_hinh']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Hình thức học</label>
                        <select name="hinh_thuc_hoc_id" id="f_hinh_thuc" class="form-select">
                            <option value="">-- Chọn --</option>
                            <?php foreach ($hinhThucCombo as $r): ?>
                                <option value="<?= $r['id'] ?>"><?= Helper::h($r['ten_hinh_thuc']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Đối tượng học viên</label>
                        <select name="doi_tuong_hoc_vien_id" id="f_doi_tuong" class="form-select">
                            <option value="">-- Chọn --</option>
                            <?php foreach ($doiTuongCombo as $r): ?>
                                <option value="<?= $r['id'] ?>"><?= Helper::h($r['ten_doi_tuong']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Số tiết lý thuyết</label>
                        <input type="number" name="so_tiet_ly_thuyet" id="f_slt" class="form-control" value="0" min="0">
                    </div>
                    <div class="form-group">
                        <label>Số tiết thực hành</label>
                        <input type="number" name="so_tiet_thuc_hanh" id="f_sth" class="form-control" value="0" min="0">
                    </div>
                    <div class="form-group">
                        <label>Tổng số tiết</label>
                        <input type="number" id="f_tst" class="form-control" value="0" readonly>
                    </div>
                    <div class="form-group">
                        <label>Số tín chỉ</label>
                        <input type="number" step="0.5" name="so_tin_chi" id="f_stc" class="form-control" value="0" min="0">
                    </div>
                </div>
                <div class="form-group">
                    <label>Điều kiện</label>
                    <input type="text" name="dieu_kien" id="f_dieu_kien" class="form-control" maxlength="200">
                </div>
                <div class="form-group">
                    <label>Mục tiêu</label>
                    <textarea name="muc_tieu" id="f_muc_tieu" class="form-control" rows="2"></textarea>
                </div>
                <div class="form-group">
                    <label>Mô tả</label>
                    <textarea name="mo_ta" id="f_mo_ta" class="form-control" rows="3"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn" onclick="closeModal()">Hủy</button>
                <button type="submit" class="btn btn-primary">Lưu</button>
            </div>
        </form>
    </div>
</div>

<!-- DRAWER: Chương trình học (Khóa học ↔ Môn học) -->
<div class="drawer-backdrop" id="drawerBackdrop" onclick="closeChuongTrinh()"></div>
<aside class="drawer" id="drawerCT" role="dialog" aria-modal="true" aria-labelledby="ctTitle">
    <div class="drawer-header">
        <div class="drawer-title-row">
            <div>
                <h3 id="ctTitle">Chương trình học</h3>
                <div class="subtitle" id="ctSubtitle">
                    <span id="ctMaKh">—</span>
                    <span>·</span>
                    <span id="ctTenKh" style="color:var(--gray-700);font-weight:500">—</span>
                </div>
            </div>
            <button type="button" class="close" onclick="closeChuongTrinh()" aria-label="Đóng">&times;</button>
        </div>
        <!-- Summary row -->
        <div class="stats-row" style="margin:14px 0 0;grid-template-columns:repeat(4,1fr);gap:8px">
            <div class="stat-card" style="padding:10px 12px;border:1px solid var(--gray-200)">
                <div>
                    <div class="stat-label">Số môn</div>
                    <div class="stat-value" id="sumSoMon" style="font-size:16px">0</div>
                </div>
            </div>
            <div class="stat-card success" style="padding:10px 12px">
                <div>
                    <div class="stat-label">Bắt buộc</div>
                    <div class="stat-value" id="sumBatBuoc" style="font-size:16px">0</div>
                </div>
            </div>
            <div class="stat-card info" style="padding:10px 12px">
                <div>
                    <div class="stat-label">Tổng tiết (môn)</div>
                    <div class="stat-value" id="sumTongTiet" style="font-size:16px">0</div>
                    <div class="stat-sub" id="sumTongTietKh" style="font-size:11px">/ 0 KH</div>
                </div>
            </div>
            <div class="stat-card warning" style="padding:10px 12px">
                <div>
                    <div class="stat-label">Tín chỉ (môn)</div>
                    <div class="stat-value" id="sumTongTinChi" style="font-size:16px">0</div>
                    <div class="stat-sub" id="sumTongTinChiKh" style="font-size:11px">/ 0 KH</div>
                </div>
            </div>
        </div>
        <!-- Progress: môn tiết so với khóa tiết -->
        <div style="margin-top:10px">
            <div style="display:flex;justify-content:space-between;font-size:11.5px;color:var(--gray-500);margin-bottom:4px">
                <span>Tiến độ phủ tiết học</span>
                <span id="progressLabel">0%</span>
            </div>
            <div class="progress" role="progressbar" aria-labelledby="progressLabel">
                <div class="progress-bar" id="progressBar" style="width:0%"></div>
            </div>
        </div>
    </div>

    <div class="drawer-body">
        <?php if ($canKhmAdd): ?>
        <div class="inline-add">
            <select id="ctSelectMon" class="form-select" aria-label="Chọn môn học để thêm">
                <option value="">-- Chọn môn học để thêm --</option>
                <?php foreach ($monHocCombo as $m): ?>
                    <option value="<?= (int)$m['id'] ?>"
                            data-tiet="<?= (int)($m['tong_so_tiet'] ?? 0) ?>"
                            data-tin="<?= (float)($m['so_tin_chi'] ?? 0) ?>">
                        <?= Helper::h($m['ma_mon_hoc']) ?> — <?= Helper::h($m['ten_mon_hoc']) ?>
                        (<?= (int)($m['tong_so_tiet'] ?? 0) ?> tiết · <?= (float)($m['so_tin_chi'] ?? 0) ?> TC)
                    </option>
                <?php endforeach; ?>
            </select>
            <label class="icon-label" style="font-size:12.5px;color:var(--gray-700)">
                <input type="checkbox" id="ctBatBuoc" checked> Bắt buộc
            </label>
            <button type="button" class="btn btn-primary" id="btnAddMon">+ Thêm môn</button>
        </div>
        <?php endif; ?>
        <div id="ctTableWrap" style="position:relative;min-height:200px">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width:64px" class="text-center">Thứ tự</th>
                        <th style="width:110px">Mã môn</th>
                        <th>Tên môn học</th>
                        <th class="text-center" style="width:70px">Tiết</th>
                        <th class="text-center" style="width:60px">TC</th>
                        <th class="text-center" style="width:90px">Bắt buộc</th>
                        <th style="width:60px" class="text-right"></th>
                    </tr>
                </thead>
                <tbody id="ctTbody"></tbody>
            </table>
        </div>
    </div>

    <div class="drawer-footer">
        <div class="text-muted" style="font-size:12.5px">
            <?= $canKhmEdit ? 'Nhấn ↑/↓ để thay đổi thứ tự môn' : 'Chỉ xem · không có quyền chỉnh sửa' ?>
        </div>
        <button type="button" class="btn" onclick="closeChuongTrinh()">Đóng</button>
    </div>
</aside>

<script>
var URL = APP_BASE + 'GUI/DT_KhoaHoc/ajax_handler.php';
var URL_KHM = APP_BASE + 'GUI/DT_KhoaHocMonHoc/ajax_handler.php';
var CAN_EDIT = <?= $canEdit?'true':'false' ?>;
var CAN_DEL = <?= $canDel?'true':'false' ?>;
var CAN_KHM_VIEW = <?= $canKhmView?'true':'false' ?>;
var CAN_KHM_ADD  = <?= $canKhmAdd ?'true':'false' ?>;
var CAN_KHM_EDIT = <?= $canKhmEdit?'true':'false' ?>;
var ICON_EDIT = '<?= addslashes(IconHelper::svg('edit', '18')) ?>';
var ICON_TRASH = '<?= addslashes(IconHelper::svg('trash', '18')) ?>';
var ICON_BOOK = '<?= addslashes(IconHelper::svg('book', '18')) ?>';
var ICON_EMPTY = '<?= addslashes(IconHelper::svg('search', '40')) ?>';
var CAN_KHM_DEL  = <?= $canKhmDel ?'true':'false' ?>;
var state = { page: 1, pageSize: 20, search: '', daXoa: 0, lh: 0, ht: 0, dt: 0 };
var CT_state = { khoaHocId: 0 };

function load() {
    APP.showLoading('#tableWrap');
    APP.ajax(URL, {
        action: 'getPaged', page: state.page, pageSize: state.pageSize,
        search: state.search, da_xoa: state.daXoa,
        loai_hinh_dao_tao_id: state.lh,
        hinh_thuc_hoc_id: state.ht,
        doi_tuong_hoc_vien_id: state.dt
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
        $tb.append('<tr><td colspan="10"><div class="empty-state"><div class="icon">' + ICON_EMPTY + '</div>Không có dữ liệu</div></td></tr>');
        return;
    }
    var stt = (state.page - 1) * state.pageSize;
    rows.forEach(function (r) {
        stt++;
        var tt = r.trang_thai == 1
            ? '<span class="badge badge-success">Hoạt động</span>'
            : '<span class="badge badge-danger">Khóa</span>';
        var actions = '';
        if (state.daXoa == 0) {
            if (CAN_KHM_VIEW) actions += '<button class="btn btn-sm" title="Chương trình học" onclick="openChuongTrinh(' + r.id + ')">' + ICON_BOOK + '</button>';
            if (CAN_EDIT) actions += '<button class="btn btn-sm" title="Sửa" onclick="openEdit(' + r.id + ')">' + ICON_EDIT + '</button>';
            if (CAN_DEL) actions += '<button class="btn btn-sm btn-danger" title="Xóa" onclick="trashItem(' + r.id + ')">' + ICON_TRASH + '</button>';
        } else {
            if (CAN_EDIT) actions += '<button class="btn btn-sm btn-success" onclick="restoreItem(' + r.id + ')">↺ Khôi phục</button>';
            if (CAN_DEL) actions += '<button class="btn btn-sm btn-danger" onclick="deleteItem(' + r.id + ')">Xóa</button>';
        }
        $tb.append(
            '<tr>' +
                '<td class="text-center">' + stt + '</td>' +
                '<td><strong>' + APP.escape(r.ma_khoa_hoc) + '</strong></td>' +
                '<td>' + APP.escape(r.ten_khoa_hoc) + '</td>' +
                '<td>' + APP.escape(r.ten_loai_hinh || '-') + '</td>' +
                '<td>' + APP.escape(r.ten_hinh_thuc || '-') + '</td>' +
                '<td>' + APP.escape(r.ten_doi_tuong || '-') + '</td>' +
                '<td class="text-center">' + (r.tong_so_tiet || 0) + '</td>' +
                '<td class="text-center">' + (r.so_tin_chi || 0) + '</td>' +
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

$('#filterLoaiHinh').on('change', function () { state.lh = parseInt(this.value, 10) || 0; state.page = 1; load(); });
$('#filterHinhThuc').on('change', function () { state.ht = parseInt(this.value, 10) || 0; state.page = 1; load(); });
$('#filterDoiTuong').on('change', function () { state.dt = parseInt(this.value, 10) || 0; state.page = 1; load(); });
$('#filterDaXoa').on('change', function () { state.daXoa = parseInt(this.value, 10) || 0; state.page = 1; load(); });

function recalcTongTiet() {
    var slt = parseInt($('#f_slt').val(), 10) || 0;
    var sth = parseInt($('#f_sth').val(), 10) || 0;
    $('#f_tst').val(slt + sth);
}
$('#f_slt, #f_sth').on('input', recalcTongTiet);

function openCreate() {
    $('#modalTitle').text('Thêm khóa học');
    $('#formMain')[0].reset(); $('#f_id').val(''); $('#f_tst').val(0);
    $('#modalForm').addClass('open');
}
function openEdit(id) {
    APP.ajax(URL, {action: 'getById', id: id}).done(function (res) {
        if (!res.success) { APP.toast(res.message, 'error'); return; }
        var e = res.data;
        $('#modalTitle').text('Sửa khóa học');
        $('#f_id').val(e.id);
        $('#f_ma').val(e.ma_khoa_hoc);
        $('#f_ten').val(e.ten_khoa_hoc);
        $('#f_loai_hinh').val(e.loai_hinh_dao_tao_id || '');
        $('#f_hinh_thuc').val(e.hinh_thuc_hoc_id || '');
        $('#f_doi_tuong').val(e.doi_tuong_hoc_vien_id || '');
        $('#f_slt').val(e.so_tiet_ly_thuyet || 0);
        $('#f_sth').val(e.so_tiet_thuc_hanh || 0);
        $('#f_stc').val(e.so_tin_chi || 0);
        $('#f_dieu_kien').val(e.dieu_kien || '');
        $('#f_muc_tieu').val(e.muc_tieu || '');
        $('#f_mo_ta').val(e.mo_ta || '');
        $('#f_trang_thai').val(e.trang_thai);
        recalcTongTiet();
        $('#modalForm').addClass('open');
    });
}
function closeModal() { $('#modalForm').removeClass('open'); }

$('#formMain').on('submit', function (e) {
    e.preventDefault();
    var data = $(this).serializeArray();
    data.push({name: 'action', value: $('#f_id').val() ? 'update' : 'insert'});
    APP.ajax(URL, data).done(function (res) {
        if (res.success) { APP.toast(res.message, 'success'); closeModal(); load(); }
        else APP.toast(res.message, 'error');
    });
});

function trashItem(id) {
    APP.confirm('Chuyển khóa học vào thùng rác?', function () {
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
    APP.confirm('Xóa VĨNH VIỄN khóa học này?', function () {
        APP.ajax(URL, {action: 'delete', id: id}).done(function (res) {
            res.success ? (APP.toast(res.message, 'success'), load()) : APP.toast(res.message, 'error');
        });
    }, {yesText: 'Xóa vĩnh viễn'});
}

load();

/* ========================================================================
 * CHƯƠNG TRÌNH HỌC (Khóa học ↔ Môn học)  —  Drawer UI
 * ====================================================================== */
function openChuongTrinh(khoaHocId) {
    CT_state.khoaHocId = khoaHocId;
    $('#drawerBackdrop').addClass('open');
    $('#drawerCT').addClass('open');
    loadChuongTrinh();
}
function closeChuongTrinh() {
    $('#drawerBackdrop').removeClass('open');
    $('#drawerCT').removeClass('open');
    CT_state.khoaHocId = 0;
}
$(document).on('keydown', function (e) {
    if (e.key === 'Escape' && $('#drawerCT').hasClass('open')) closeChuongTrinh();
});

function loadChuongTrinh() {
    if (!CT_state.khoaHocId) return;
    APP.showLoading('#ctTableWrap');
    APP.ajax(URL_KHM, {action: 'list', khoa_hoc_id: CT_state.khoaHocId}).done(function (res) {
        APP.hideLoading('#ctTableWrap');
        if (!res.success) { APP.toast(res.message, 'error'); return; }
        renderCTHeader(res.data);
        renderCTRows(res.data.items || []);
    });
}

function renderCTHeader(d) {
    var kh = d.khoa_hoc || {};
    var s = d.summary || {};
    $('#ctMaKh').text(kh.ma_khoa_hoc || '—');
    $('#ctTenKh').text(kh.ten_khoa_hoc || '—');
    $('#sumSoMon').text(s.so_mon || 0);
    $('#sumBatBuoc').text(s.so_bat_buoc || 0);
    $('#sumTongTiet').text(s.tong_tiet || 0);
    $('#sumTongTinChi').text(s.tong_tin_chi || 0);
    var khTiet = parseInt(kh.tong_so_tiet || 0, 10);
    var khTC = parseFloat(kh.so_tin_chi || 0);
    $('#sumTongTietKh').text('/ ' + khTiet + ' (KH)');
    $('#sumTongTinChiKh').text('/ ' + khTC + ' (KH)');
    var pct = khTiet > 0 ? Math.min(100, Math.round((s.tong_tiet / khTiet) * 100)) : 0;
    var $bar = $('#progressBar').css('width', pct + '%');
    $bar.removeClass('full over');
    if (pct >= 100 && s.tong_tiet === khTiet) $bar.addClass('full');
    else if (s.tong_tiet > khTiet) $bar.addClass('over');
    $('#progressLabel').text(pct + '%');
}

function renderCTRows(rows) {
    var $tb = $('#ctTbody').empty();
    if (!rows.length) {
        $tb.append(
            '<tr><td colspan="7">' +
                '<div class="empty-state-pro">' +
                    '<h4>Khóa học chưa có môn nào</h4>' +
                    '<p>' + (CAN_KHM_ADD ? 'Chọn môn học từ ô phía trên và nhấn <strong>Thêm môn</strong>.' : 'Liên hệ quản trị để thêm môn.') + '</p>' +
                '</div>' +
            '</td></tr>'
        );
        return;
    }
    var last = rows.length - 1;
    rows.forEach(function (r, idx) {
        var toggle = '';
        if (CAN_KHM_EDIT) {
            toggle = '<label class="switch" title="Bắt buộc/Tự chọn">' +
                '<input type="checkbox" ' + (r.bat_buoc == 1 ? 'checked' : '') + ' onchange="toggleBatBuoc(' + r.id + ', this.checked ? 1 : 0)">' +
                '<span class="switch-slider"></span></label>';
        } else {
            toggle = r.bat_buoc == 1
                ? '<span class="chip chip-success">Bắt buộc</span>'
                : '<span class="chip chip-muted">Tự chọn</span>';
        }
        var orderCell = CAN_KHM_EDIT
            ? '<span class="order-cell">' +
                '<button class="order-btn" ' + (idx === 0 ? 'disabled' : '') + ' onclick="moveItem(' + r.id + ', \'up\')" aria-label="Lên">▲</button>' +
                '<button class="order-btn" ' + (idx === last ? 'disabled' : '') + ' onclick="moveItem(' + r.id + ', \'down\')" aria-label="Xuống">▼</button>' +
              '</span>'
            : (r.thu_tu || (idx + 1));
        var removeBtn = CAN_KHM_DEL
            ? '<button class="btn btn-sm btn-danger" title="Gỡ khỏi khóa học" onclick="removeItem(' + r.id + ')">✕</button>'
            : '';
        var monInactive = r.mon_trang_thai == 0 ? ' <span class="chip chip-warning">Môn bị khóa</span>' : '';
        $tb.append(
            '<tr>' +
                '<td class="text-center">' + orderCell + '</td>' +
                '<td><strong>' + APP.escape(r.ma_mon_hoc || '') + '</strong></td>' +
                '<td>' + APP.escape(r.ten_mon_hoc || '') + monInactive + '</td>' +
                '<td class="text-center" style="font-variant-numeric:tabular-nums">' + (r.tong_so_tiet || 0) + '</td>' +
                '<td class="text-center" style="font-variant-numeric:tabular-nums">' + (r.so_tin_chi || 0) + '</td>' +
                '<td class="text-center">' + toggle + '</td>' +
                '<td class="text-right">' + removeBtn + '</td>' +
            '</tr>'
        );
    });
}

$('#btnAddMon').on('click', function () {
    var monId = parseInt($('#ctSelectMon').val(), 10) || 0;
    if (!monId) { APP.toast('Chọn môn học để thêm', 'warning'); return; }
    var bb = $('#ctBatBuoc').is(':checked') ? 1 : 0;
    var $btn = $(this).prop('disabled', true);
    APP.ajax(URL_KHM, {action: 'add', khoa_hoc_id: CT_state.khoaHocId, mon_hoc_id: monId, bat_buoc: bb}).done(function (res) {
        $btn.prop('disabled', false);
        if (res.success) {
            APP.toast(res.message, 'success');
            $('#ctSelectMon').val('');
            loadChuongTrinh();
        } else APP.toast(res.message, 'error');
    }).fail(function () { $btn.prop('disabled', false); });
});

function toggleBatBuoc(id, val) {
    APP.ajax(URL_KHM, {action: 'toggleBatBuoc', id: id, bat_buoc: val}).done(function (res) {
        if (res.success) { APP.toast(res.message, 'success'); loadChuongTrinh(); }
        else { APP.toast(res.message, 'error'); loadChuongTrinh(); }
    });
}

function moveItem(id, dir) {
    APP.ajax(URL_KHM, {action: 'move', id: id, dir: dir}).done(function (res) {
        if (res.success) loadChuongTrinh();
        else APP.toast(res.message, 'error');
    });
}

function removeItem(id) {
    APP.confirm('Gỡ môn học này khỏi khóa?', function () {
        APP.ajax(URL_KHM, {action: 'remove', id: id}).done(function (res) {
            if (res.success) { APP.toast(res.message, 'success'); loadChuongTrinh(); }
            else APP.toast(res.message, 'error');
        });
    });
}
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
