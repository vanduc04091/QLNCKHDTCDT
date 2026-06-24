<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../BUS/DM_LoaiHinhDaoTao_BUS.php';
require_once __DIR__ . '/../../BUS/DM_HinhThucHoc_BUS.php';
require_once __DIR__ . '/../../BUS/DM_DoiTuongHocVien_BUS.php';
require_once __DIR__ . '/../../BUS/DT_MonHoc_BUS.php';
require_once __DIR__ . '/../../BUS/DT_DotDangKy_BUS.php';

Helper::requireLogin();
if (!PhanQuyenHelper::hasQuyen('DT_KhoaHoc', PhanQuyenHelper::QUYEN_XEM)) {
    echo 'Bạn không có quyền truy cập chức năng này.'; exit;
}

$canAdd = PhanQuyenHelper::hasQuyen('DT_KhoaHoc', PhanQuyenHelper::QUYEN_THEM);
$canEdit = PhanQuyenHelper::hasQuyen('DT_KhoaHoc', PhanQuyenHelper::QUYEN_SUA);
$canDel = PhanQuyenHelper::hasQuyen('DT_KhoaHoc', PhanQuyenHelper::QUYEN_XOA);

// Quyền xem chương trình đào tạo của khóa (đọc danh sách CTĐT áp dụng)
$canKhmView = PhanQuyenHelper::hasQuyen('DT_ChuongTrinh', PhanQuyenHelper::QUYEN_XEM);
$canKhmAdd  = false;
$canKhmEdit = false;
$canKhmDel  = false;

$loaiHinhCombo = DM_LoaiHinhDaoTao_BUS::getCombo();
$hinhThucCombo = DM_HinhThucHoc_BUS::getCombo();
$doiTuongCombo = DM_DoiTuongHocVien_BUS::getCombo();
$dotCombo = DT_DotDangKy_BUS::getCombo(false);
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
                <button type="button" class="btn" onclick="exportExcel()" title="Xuất Excel"><?= IconHelper::svg('download','16') ?> Xuất Excel</button>
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
                    <th class="text-center" style="width:110px">Bắt đầu</th>
                    <th class="text-center" style="width:110px">Kết thúc</th>
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
                        <label>Đợt đăng ký</label>
                        <select name="dot_dang_ky_id" id="f_dot" class="form-select">
                            <option value="">-- Không gán đợt (không nhận đăng ký công khai) --</option>
                            <?php foreach ($dotCombo as $r): ?>
                                <option value="<?= $r['id'] ?>"><?= Helper::h($r['ten_dot'] . ' (' . $r['nam'] . ')') ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Ngày bắt đầu</label>
                        <input type="date" name="ngay_bat_dau" id="f_nbd" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Ngày kết thúc</label>
                        <input type="date" name="ngay_ket_thuc" id="f_nkt" class="form-control">
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
    </div>

    <div class="drawer-body">
        <?php if ($canEdit): ?>
        <div style="background:#f8fafc;padding:12px;border-radius:8px;margin-bottom:14px;border:1px solid var(--gray-200)">
            <div style="font-weight:600;margin-bottom:8px;font-size:13.5px">Gắn chương trình đào tạo vào khóa học</div>
            <div style="display:flex;gap:8px;flex-wrap:wrap;align-items:center">
                <select id="ctAddSelect" class="form-select" style="flex:1;min-width:220px">
                    <option value="">-- Chọn chương trình đào tạo --</option>
                </select>
                <input type="date" id="ctAddNbd" class="form-control" style="width:150px" title="Ngày bắt đầu">
                <input type="date" id="ctAddNkt" class="form-control" style="width:150px" title="Ngày kết thúc">
            </div>
            <div style="display:flex;gap:8px;flex-wrap:wrap;align-items:center;margin-top:8px">
                <input type="text" id="ctAddDiaDiem" class="form-control" style="flex:1;min-width:180px" placeholder="Địa điểm (tùy chọn)">
                <select id="ctAddTrangThai" class="form-select" style="width:160px">
                    <option value="0">Chờ khai giảng</option>
                    <option value="1">Đang học</option>
                    <option value="2">Đã kết thúc</option>
                    <option value="3">Đã hủy</option>
                </select>
                <button type="button" class="btn btn-primary btn-sm" id="btnAddCt">Gắn CTĐT</button>
            </div>
        </div>
        <?php endif; ?>
        <div class="text-muted" style="font-size:12.5px;margin-bottom:10px">
            Quản lý bài học của từng CTĐT tại menu <strong>Chương trình đào tạo</strong>.
        </div>
        <div id="ctTableWrap" style="position:relative;min-height:200px">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width:50px" class="text-center">#</th>
                        <th style="width:130px">Mã CTĐT</th>
                        <th>Tên chương trình đào tạo</th>
                        <th class="text-center" style="width:110px">Trạng thái</th>
                        <?php if ($canEdit): ?><th class="text-right" style="width:80px"></th><?php endif; ?>
                    </tr>
                </thead>
                <tbody id="ctTbody"></tbody>
            </table>
        </div>
    </div>

    <div class="drawer-footer">
        <button type="button" class="btn" onclick="closeChuongTrinh()">Đóng</button>
    </div>
</aside>

<script>
var URL = APP_BASE + 'GUI/DT_KhoaHoc/ajax_handler.php';
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
function exportExcel(){ var p=new URLSearchParams({search:state.search||'',da_xoa:state.daXoa||0,loai_hinh_dao_tao_id:state.lh||0,hinh_thuc_hoc_id:state.ht||0,doi_tuong_hoc_vien_id:state.dt||0}); window.location=APP_BASE+'GUI/DT_KhoaHoc/export.php?'+p.toString(); }
var CT_state = { khoaHocId: 0, comboLoaded: false };

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
            if (CAN_KHM_VIEW) actions += '<button class="btn btn-sm" title="Chương trình đào tạo áp dụng" onclick="openChuongTrinh(' + r.id + ', \'' + APP.escape(r.ma_khoa_hoc||'').replace(/\x27/g,"\\\x27") + '\', \'' + APP.escape(r.ten_khoa_hoc||'').replace(/\x27/g,"\\\x27") + '\')">' + ICON_BOOK + '</button>';
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
                '<td class="text-center">' + (r.ngay_bat_dau ? APP.formatDate(r.ngay_bat_dau) : '—') + '</td>' +
                '<td class="text-center">' + (r.ngay_ket_thuc ? APP.formatDate(r.ngay_ket_thuc) : '—') + '</td>' +
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

function openCreate() {
    $('#modalTitle').text('Thêm khóa học');
    $('#formMain')[0].reset(); $('#f_id').val('');
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
        $('#f_dot').val(e.dot_dang_ky_id || '');
        $('#f_nbd').val(e.ngay_bat_dau || '');
        $('#f_nkt').val(e.ngay_ket_thuc || '');
        $('#f_dieu_kien').val(e.dieu_kien || '');
        $('#f_muc_tieu').val(e.muc_tieu || '');
        $('#f_mo_ta').val(e.mo_ta || '');
        $('#f_trang_thai').val(e.trang_thai);
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
function openChuongTrinh(khoaHocId, maKh, tenKh) {
    CT_state.khoaHocId = khoaHocId;
    $('#ctMaKh').text(maKh || '—');
    $('#ctTenKh').text(tenKh || '—');
    $('#drawerBackdrop').addClass('open');
    $('#drawerCT').addClass('open');
    if (CAN_EDIT) ensureCtCombo();
    loadChuongTrinh();
}
function ensureCtCombo() {
    if (CT_state.comboLoaded) return;
    APP.ajax(URL, {action: 'getComboChuongTrinh'}).done(function (res) {
        if (!res.success) return;
        var $s = $('#ctAddSelect').empty().append('<option value="">-- Chọn chương trình đào tạo --</option>');
        (res.data || []).forEach(function (c) {
            $s.append('<option value="' + c.id + '">' + APP.escape((c.ma_chuong_trinh ? c.ma_chuong_trinh + ' - ' : '') + (c.ten_chuong_trinh || '')) + '</option>');
        });
        CT_state.comboLoaded = true;
    });
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
    APP.ajax(URL, {action: 'listChuongTrinh', khoa_hoc_id: CT_state.khoaHocId}).done(function (res) {
        APP.hideLoading('#ctTableWrap');
        if (!res.success) { APP.toast(res.message, 'error'); return; }
        renderCTRows(res.data || []);
    });
}

function renderCTRows(rows) {
    var colspan = CAN_EDIT ? 5 : 4;
    var $tb = $('#ctTbody').empty();
    if (!rows.length) {
        $tb.append(
            '<tr><td colspan="' + colspan + '">' +
                '<div class="empty-state-pro">' +
                    '<h4>Khóa học chưa có chương trình đào tạo nào</h4>' +
                    '<p>Dùng form phía trên để gắn CTĐT vào khóa này.</p>' +
                '</div>' +
            '</td></tr>'
        );
        return;
    }
    rows.forEach(function (r, idx) {
        var tt = parseInt(r.ct_trang_thai, 10);
        var ttTxt = ({0:'Chờ khai giảng',1:'Đang học',2:'Đã kết thúc',3:'Đã hủy'})[tt] || '';
        $tb.append(
            '<tr>' +
                '<td class="text-center">' + (idx + 1) + '</td>' +
                '<td><strong>' + APP.escape(r.ma_chuong_trinh || '') + '</strong></td>' +
                '<td>' + APP.escape(r.ten_chuong_trinh || '') + '</td>' +
                '<td class="text-center"><span class="chip">' + APP.escape(ttTxt) + '</span></td>' +
                (CAN_EDIT ? '<td class="text-right"><button class="btn btn-sm btn-danger" title="Gỡ khỏi khóa" onclick="removeCt(' + r.id + ')">' + ICON_TRASH + '</button></td>' : '') +
            '</tr>'
        );
    });
}

$('#btnAddCt').on('click', function () {
    var ctId = parseInt($('#ctAddSelect').val(), 10);
    if (!ctId) { APP.toast('Chọn chương trình đào tạo', 'error'); return; }
    APP.ajax(URL, {
        action: 'ct_add',
        khoa_hoc_id: CT_state.khoaHocId,
        chuong_trinh_id: ctId,
        ngay_bat_dau: $('#ctAddNbd').val(),
        ngay_ket_thuc: $('#ctAddNkt').val(),
        dia_diem: $('#ctAddDiaDiem').val(),
        trang_thai: $('#ctAddTrangThai').val()
    }).done(function (res) {
        if (res.success) {
            APP.toast(res.message, 'success');
            $('#ctAddSelect').val(''); $('#ctAddNbd').val(''); $('#ctAddNkt').val(''); $('#ctAddDiaDiem').val(''); $('#ctAddTrangThai').val('0');
            loadChuongTrinh(); load();
        } else APP.toast(res.message, 'error');
    });
});

function removeCt(id) {
    APP.confirm('Gỡ chương trình đào tạo này khỏi khóa học?', function () {
        APP.ajax(URL, {action: 'ct_remove', id: id}).done(function (res) {
            res.success ? (APP.toast(res.message, 'success'), loadChuongTrinh(), load()) : APP.toast(res.message, 'error');
        });
    });
}

// Drawer "Chương trình đào tạo của khóa" giờ chỉ xem (read-only).
// Quản lý gắn môn/khóa thực hiện tại menu Chương trình đào tạo.
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
