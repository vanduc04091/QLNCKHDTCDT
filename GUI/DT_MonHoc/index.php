<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../BUS/DT_ChuongTrinh_BUS.php';
require_once __DIR__ . '/../../BUS/DT_KhoaHoc_BUS.php';

Helper::requireLogin();
if (!PhanQuyenHelper::hasQuyen('DT_MonHoc', PhanQuyenHelper::QUYEN_XEM)) {
    echo 'Bạn không có quyền truy cập chức năng này.'; exit;
}

$ctCombo = DT_ChuongTrinh_BUS::getCombo();
$khoaList = DT_KhoaHoc_BUS::getCombo();

$canAdd = PhanQuyenHelper::hasQuyen('DT_MonHoc', PhanQuyenHelper::QUYEN_THEM);
$canEdit = PhanQuyenHelper::hasQuyen('DT_MonHoc', PhanQuyenHelper::QUYEN_SUA);
$canDel = PhanQuyenHelper::hasQuyen('DT_MonHoc', PhanQuyenHelper::QUYEN_XOA);

$pageTitle = 'Quản lý bài học';
$activeMenu = 'DT_MonHoc';
require __DIR__ . '/../layouts/header.php';
?>

<div class="breadcrumb">
    <a href="<?= AppConfig::baseUrl('GUI/dashboard/index.php') ?>">Trang chủ</a>
    <span class="sep">›</span> Đào tạo
    <span class="sep">›</span> <span>Bài học</span>
</div>

<!-- STATS -->
<div class="stats-row" id="statsRow">
    <div class="stat-card">
        <div class="stat-icon"><?= IconHelper::svg('dashboard', '22') ?></div>
        <div>
            <div class="stat-label">Tổng bài học</div>
            <div class="stat-value" id="stTotal">—</div>
        </div>
    </div>
    <div class="stat-card success">
        <div class="stat-icon"><?= IconHelper::svg('check-circle', '22') ?></div>
        <div>
            <div class="stat-label">Đang hoạt động</div>
            <div class="stat-value" id="stActive">—</div>
        </div>
    </div>
    <div class="stat-card info">
        <div class="stat-icon"><?= IconHelper::svg('users', '22') ?></div>
        <div>
            <div class="stat-label">Đã dùng trong khóa học</div>
            <div class="stat-value" id="stInKhoa">—</div>
        </div>
    </div>
    <div class="stat-card warning">
        <div class="stat-icon"><?= IconHelper::svg('trash', '22') ?></div>
        <div>
            <div class="stat-label">Trong thùng rác</div>
            <div class="stat-value" id="stTrash">—</div>
        </div>
    </div>
</div>

<div class="card">
    <div class="toolbar">
        <div class="left">
            <div style="position:relative">
                <input type="text" id="search" class="form-control" placeholder="Tìm mã, tên bài học..." style="max-width:300px;padding-left:34px" aria-label="Tìm bài học">
                <span style="position:absolute;left:10px;top:50%;transform:translateY(-50%);color:var(--gray-400)"><?= IconHelper::svg('search', '16') ?></span>
            </div>
            <select id="filterTrangThai" class="form-select" style="max-width:160px" aria-label="Lọc trạng thái">
                <option value="-1">Tất cả trạng thái</option>
                <option value="1">Hoạt động</option>
                <option value="0">Khóa</option>
            </select>
            <select id="filterKhoa" class="form-select" style="max-width:220px" aria-label="Lọc theo khóa học">
                <option value="0">-- Chọn khóa học --</option>
                <?php foreach ($khoaList as $k): ?>
                    <option value="<?= $k['id'] ?>"><?= Helper::h(($k['ma_khoa_hoc'] ? $k['ma_khoa_hoc'].' - ' : '').$k['ten_khoa_hoc']) ?></option>
                <?php endforeach; ?>
            </select>
            <select id="filterChuongTrinh" class="form-select" style="max-width:240px" aria-label="Lọc theo chương trình" disabled>
                <option value="0">-- Chọn chương trình --</option>
            </select>
            <select id="filterDaXoa" class="form-select" style="max-width:150px" aria-label="Lọc thùng rác">
                <option value="0">Đang hoạt động</option>
                <option value="1">Thùng rác</option>
            </select>
        </div>
        <div class="right">
            <button type="button" class="btn" onclick="exportExcel()" title="Xuất Excel"><?= IconHelper::svg('download','16') ?> Xuất Excel</button>
            <?php if ($canAdd): ?>
                <button type="button" class="btn btn-primary" onclick="openCreate()" aria-label="Thêm bài học mới">
                    <?= IconHelper::svg('plus', '16') ?> Thêm bài học
                </button>
            <?php endif; ?>
        </div>
    </div>
    <div class="table-wrap" id="tableWrap" style="position:relative;min-height:220px">
        <table class="table">
            <thead>
                <tr>
                    <th style="width:50px" class="text-center">#</th>
                    <th style="width:120px">Mã bài</th>
                    <th>Tên bài học</th>
                    <th>Chương trình đào tạo</th>
                    <th class="text-center" style="width:120px">Trạng thái</th>
                    <th style="width:130px" class="text-right">Hành động</th>
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

<!-- MODAL FORM -->
<div class="modal-backdrop" id="modalForm" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
    <div class="modal modal-lg">
        <div class="modal-header">
            <h3 id="modalTitle">Thêm bài học</h3>
            <button type="button" class="close" onclick="closeModal()" aria-label="Đóng">&times;</button>
        </div>
        <form id="formMain">
            <div class="modal-body">
                <input type="hidden" name="id" id="f_id">
                <div class="form-row">
                    <div class="form-group">
                        <label for="f_ma">Mã bài học <span class="required">*</span></label>
                        <input type="text" name="ma_mon_hoc" id="f_ma" class="form-control" required maxlength="50" autocomplete="off">
                        <div class="form-error" id="err_ma"></div>
                    </div>
                    <div class="form-group">
                        <label for="f_trang_thai">Trạng thái</label>
                        <select name="trang_thai" id="f_trang_thai" class="form-select">
                            <option value="1">Hoạt động</option>
                            <option value="0">Khóa</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="f_ten">Tên bài học <span class="required">*</span></label>
                    <input type="text" name="ten_mon_hoc" id="f_ten" class="form-control" required maxlength="200" autocomplete="off">
                </div>
                <div class="form-row-3">
                    <div class="form-group">
                        <label for="f_slt">Số tiết lý thuyết</label>
                        <input type="number" name="so_tiet_ly_thuyet" id="f_slt" class="form-control" value="0" min="0" inputmode="numeric">
                    </div>
                    <div class="form-group">
                        <label for="f_sth">Số tiết thực hành</label>
                        <input type="number" name="so_tiet_thuc_hanh" id="f_sth" class="form-control" value="0" min="0" inputmode="numeric">
                    </div>
                    <div class="form-group">
                        <label for="f_tst">Tổng số tiết</label>
                        <input type="number" id="f_tst" class="form-control" value="0" readonly aria-describedby="f_tst_help">
                        <div class="form-error" id="f_tst_help" style="display:block;color:var(--gray-500)">Tự tính từ lý thuyết + thực hành</div>
                    </div>
                </div>
                <div class="form-group" style="max-width:240px">
                    <label for="f_stc">Số tín chỉ</label>
                    <input type="number" step="0.5" name="so_tin_chi" id="f_stc" class="form-control" value="0" min="0" inputmode="decimal">
                </div>
                <div class="form-group">
                    <label>Thuộc chương trình đào tạo <small class="text-muted">(gõ để tìm, 1 bài có thể thuộc nhiều CTĐT)</small></label>
                    <div class="ct-picker" id="ctPicker">
                        <div class="ct-picker-chips" id="ctChips"></div>
                        <div class="ct-picker-input-wrap">
                            <input type="text" id="ctSearch" class="ct-picker-input" placeholder="Gõ mã hoặc tên chương trình..." autocomplete="off">
                            <div class="ct-suggest" id="ctSuggest"></div>
                        </div>
                    </div>
                    <div class="form-error" style="display:block;color:var(--gray-500)">Gõ vào ô trên rồi chọn chương trình từ danh sách gợi ý. Bấm × để bỏ.</div>
                </div>
                <div class="form-group">
                    <label for="f_mo_ta">Mô tả</label>
                    <textarea name="mo_ta" id="f_mo_ta" class="form-control" rows="3" placeholder="Nội dung, phạm vi, ghi chú bài học..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn" onclick="closeModal()">Hủy</button>
                <button type="submit" class="btn btn-primary" id="btnSave">
                    <?= IconHelper::svg('save', '16') ?> Lưu
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: thêm nhanh 1 CTĐT cho 1 bài (nút + CTĐT ở bảng) -->
<div class="modal-backdrop" id="modalAddCt">
    <div class="modal" style="max-width:460px">
        <div class="modal-header">
            <h3>Thêm chương trình cho bài học</h3>
            <button type="button" class="close" onclick="$('#modalAddCt').removeClass('open')">&times;</button>
        </div>
        <div class="modal-body" style="min-height:420px;display:flex;flex-direction:column">
            <div class="text-muted" style="font-size:13px;margin-bottom:10px">Bài: <strong id="addCtBaiTen"></strong></div>
            <input type="hidden" id="addCtBaiId">
            <label style="font-size:13px;font-weight:500;margin-bottom:6px">Chọn chương trình đào tạo</label>
            <input type="text" id="addCtSearch" class="form-control" placeholder="Gõ mã hoặc tên chương trình..." autocomplete="off">
            <div class="ct-suggest-static" id="addCtSuggest"></div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn" onclick="$('#modalAddCt').removeClass('open')">Đóng</button>
        </div>
    </div>
</div>

<!-- Drawer: Khóa học gắn với môn này -->
<div class="drawer-backdrop" id="drawerKhoa">
    <div class="drawer">
        <div class="drawer-header">
            <div>
                <h3 id="drwTitle" style="margin:0">Khóa học gắn với bài</h3>
                <div id="drwSub" class="text-muted" style="font-size:12.5px;margin-top:2px"></div>
            </div>
            <button type="button" class="close" onclick="closeKhoaDrawer()">&times;</button>
        </div>
        <div class="drawer-body">
            <!-- Form thêm môn vào khóa -->
            <div style="background:#f8fafc;padding:12px;border-radius:8px;margin-bottom:14px;border:1px solid var(--gray-200)">
                <div style="font-weight:600;margin-bottom:8px;font-size:13.5px">Thêm bài này vào khóa học khác</div>
                <div style="display:flex;gap:8px;flex-wrap:wrap;align-items:center">
                    <select id="drwKhoaSelect" class="form-select" style="flex:1;min-width:200px"></select>
                    <label style="display:inline-flex;gap:6px;align-items:center;font-size:13px;white-space:nowrap">
                        <input type="checkbox" id="drwBatBuoc" checked> Bắt buộc
                    </label>
                    <button type="button" class="btn btn-primary btn-sm" id="btnAddToKhoa">+ Thêm</button>
                </div>
            </div>

            <div id="drwList" style="display:flex;flex-direction:column;gap:8px"></div>
            <div id="drwEmpty" style="display:none;text-align:center;color:var(--gray-500);padding:30px 16px;font-size:13px">
                Bài này chưa gắn với khóa học nào.
            </div>
        </div>
    </div>
</div>

<style>
    .btn-link-chip { border:0; cursor:pointer; }
    .btn-link-chip:hover { filter: brightness(0.95); }

    /* Cột Chương trình ở bảng bài học */
    .mh-ct-cell { display:flex; flex-wrap:wrap; gap:4px; align-items:center; }
    .mh-ct-tag { display:inline-block; padding:1px 8px; border-radius:10px; font-size:11.5px; font-weight:600;
                 background:var(--primary-light,#dbeafe); color:var(--primary-dark,#1e40af); border:1px solid #bfdbfe; }
    .mh-ct-tag-main { background:var(--gray-100,#f1f5f9); color:var(--gray-700,#334155); border-color:var(--gray-200,#e2e8f0); font-weight:500; }
    .mh-ct-more { font-size:11px; color:var(--gray-500); font-weight:600; }
    .mh-ct-add { border:1px dashed var(--gray-300); background:#fff; color:var(--gray-600); cursor:pointer;
                 font-size:11px; padding:1px 8px; border-radius:10px; }
    .mh-ct-add:hover { border-color:var(--primary); color:var(--primary); }

    /* CTĐT picker: chip + suggest */
    .ct-picker { border:1px solid var(--gray-300); border-radius:8px; padding:6px; background:#fff; }
    .ct-picker:focus-within { border-color:var(--primary); box-shadow:0 0 0 3px rgba(37,99,235,.12); }
    .ct-picker-chips { display:flex; flex-wrap:wrap; gap:6px; }
    .ct-picker-chips:not(:empty) { margin-bottom:6px; }
    .ct-chip { display:inline-flex; align-items:center; gap:6px; background:var(--primary-light,#dbeafe);
               color:var(--primary-dark,#1e40af); border:1px solid #bfdbfe; border-radius:14px;
               padding:3px 6px 3px 10px; font-size:12.5px; font-weight:500; }
    .ct-chip-x { border:0; background:rgba(0,0,0,.08); color:inherit; width:18px; height:18px; border-radius:50%;
                 cursor:pointer; line-height:1; font-size:14px; display:flex; align-items:center; justify-content:center; }
    .ct-chip-x:hover { background:#ef4444; color:#fff; }
    .ct-picker-input-wrap { position:relative; }
    .ct-picker-input { width:100%; border:0; outline:none; padding:6px 4px; font-size:14px; background:transparent; }
    .ct-suggest { position:absolute; left:0; right:0; top:100%; margin-top:4px; z-index:30;
                  background:#fff; border:1px solid var(--gray-200); border-radius:8px; box-shadow:0 8px 24px rgba(0,0,0,.12);
                  max-height:240px; overflow-y:auto; display:none; }
    .ct-suggest.open { display:block; }
    .ct-suggest-item { padding:8px 12px; font-size:13px; cursor:pointer; border-bottom:1px solid var(--gray-100); }
    .ct-suggest-item:last-child { border-bottom:0; }
    .ct-suggest-item:hover { background:var(--primary-light,#eff6ff); }
    .ct-suggest-empty { padding:10px 12px; font-size:12.5px; color:var(--gray-500); }
    /* List tĩnh trong modal "+ CTĐT" (cao, luôn hiện) */
    .ct-suggest-static { margin-top:8px; flex:1; min-height:300px; overflow-y:auto;
                         border:1px solid var(--gray-200); border-radius:8px; }
    .ct-suggest-static .ct-suggest-item { padding:9px 12px; font-size:13px; cursor:pointer; border-bottom:1px solid var(--gray-100); }
    .ct-suggest-static .ct-suggest-item:last-child { border-bottom:0; }
    .ct-suggest-static .ct-suggest-item:hover { background:var(--primary-light,#eff6ff); }
    .mh-khoa-row { display:flex; align-items:center; gap:10px; padding:10px 12px; border:1px solid var(--gray-200); border-radius:8px; background:#fff; }
    .mh-khoa-row:hover { border-color: var(--primary); }
    .mh-khoa-info { flex:1; min-width:0; }
    .mh-khoa-name { font-weight:600; color: var(--gray-800); }
    .mh-khoa-code { font-family: monospace; font-size:11.5px; color: var(--gray-500); margin-top:2px; }
    .mh-khoa-tag { font-size:11px; padding:2px 8px; border-radius:10px; font-weight:600; }
    .mh-khoa-tag.bb { background:#fef3c7; color:#92400e; }
    .mh-khoa-tag.tc { background:#e0f2fe; color:#075985; }
</style>

<script>
var URL = APP_BASE + 'GUI/DT_MonHoc/ajax_handler.php';
var CAN_EDIT = <?= $canEdit ? 'true' : 'false' ?>;
var CAN_DEL = <?= $canDel ? 'true' : 'false' ?>;
var state = { page: 1, pageSize: 20, search: '', daXoa: 0, trangThai: -1, chuongTrinhId: 0 };
function exportExcel(){ var p=new URLSearchParams({search:state.search||'',da_xoa:state.daXoa||0,trang_thai:(state.trangThai==-1?'':state.trangThai),chuong_trinh_id:state.chuongTrinhId||0}); window.location=APP_BASE+'GUI/DT_MonHoc/export.php?'+p.toString(); }

// Danh sách CTĐT cho picker gợi ý
var CT_LIST = <?= json_encode(array_map(function($c){ return ['id'=>(int)$c['id'],'ma'=>$c['ma_chuong_trinh'],'ten'=>$c['ten_chuong_trinh']]; }, $ctCombo), JSON_UNESCAPED_UNICODE) ?>;
var ctSelected = []; // [{id,ma,ten}] đang chọn trong form

var ICON_EDIT = '<?= addslashes(IconHelper::svg('edit', '14')) ?>';
var ICON_TRASH = '<?= addslashes(IconHelper::svg('trash', '14')) ?>';
var ICON_RESTORE = '<?= addslashes(IconHelper::svg('refresh', '14')) ?>';
var ICON_EMPTY = '<?= addslashes(IconHelper::svg('dashboard', '28')) ?>';
var ICON_BOOK_OPEN = '<?= addslashes(IconHelper::svg('book-open', '14')) ?>';
var ICON_PLUS = '<?= addslashes(IconHelper::svg('plus', '14')) ?>';

function loadStats() {
    APP.ajax(URL, {action: 'getStats'}).done(function (res) {
        if (!res.success) return;
        $('#stTotal').text(res.data.total);
        $('#stActive').text(res.data.active);
        $('#stInKhoa').text(res.data.in_khoa);
        $('#stTrash').text(res.data.trash);
    });
}

function load() {
    APP.showLoading('#tableWrap');
    APP.ajax(URL, {
        action: 'getPaged', page: state.page, pageSize: state.pageSize,
        search: state.search, da_xoa: state.daXoa, trang_thai: state.trangThai,
        chuong_trinh_id: state.chuongTrinhId
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
        $tb.append(
            '<tr><td colspan="6">' +
                '<div class="empty-state-pro">' +
                    '<div class="empty-icon">' + ICON_EMPTY + '</div>' +
                    '<h4>Chưa có bài học nào</h4>' +
                    '<p>' + (state.daXoa == 1 ? 'Thùng rác trống.' : (state.search ? 'Không khớp từ khóa "' + APP.escape(state.search) + '".' : 'Bắt đầu bằng cách thêm bài học đầu tiên.')) + '</p>' +
                    (state.daXoa == 0 && CAN_EDIT ? '<button class="btn btn-primary" onclick="openCreate()">+ Thêm bài học</button>' : '') +
                '</div>' +
            '</td></tr>'
        );
        return;
    }
    var stt = (state.page - 1) * state.pageSize;
    rows.forEach(function (r) {
        stt++;
        var tt = r.trang_thai == 1
            ? '<span class="chip chip-success"><span class="dot"></span>Hoạt động</span>'
            : '<span class="chip chip-muted"><span class="dot"></span>Khóa</span>';
        var dsCt = (r.ds_chuong_trinh || '').split('||').filter(Boolean).map(function (s) {
            var p = s.split('::'); return { ma: p[0] || '', ten: p[1] || '' };
        });
        var ctTxt = '<div class="mh-ct-cell">';
        if (dsCt.length) {
            // CTĐT đầu tiên: hiện rõ mã + tên; các CTĐT sau: chỉ mã
            ctTxt += '<span class="mh-ct-tag mh-ct-tag-main" title="' + APP.escape(dsCt[0].ten) + '">' +
                     APP.escape(dsCt[0].ma) + ' - ' + APP.escape(dsCt[0].ten) + '</span>';
            dsCt.slice(1, 6).forEach(function (c) {
                ctTxt += '<span class="mh-ct-tag" title="' + APP.escape(c.ma + ' - ' + c.ten) + '">' + APP.escape(c.ma) + '</span>';
            });
            if (dsCt.length > 6) ctTxt += '<span class="mh-ct-more">+' + (dsCt.length - 6) + '</span>';
        } else {
            ctTxt += '<span class="text-muted" style="font-size:12px">Chưa gắn</span>';
        }
        if (CAN_EDIT && state.daXoa == 0) {
            ctTxt += '<button class="mh-ct-add" title="Thêm chương trình cho bài này" onclick="openAddCt(' + r.id + ',\'' + APP.escape(r.ten_mon_hoc).replace(/'/g, "\\\'") + '\')">+ CTĐT</button>';
        }
        ctTxt += '</div>';
        var actions = '';
        if (state.daXoa == 0) {
            if (CAN_EDIT) actions += '<button class="btn btn-sm" title="Sửa" aria-label="Sửa bài học" onclick="openEdit(' + r.id + ')">' + ICON_EDIT + '</button>';
            if (CAN_DEL) actions += '<button class="btn btn-sm btn-danger" title="Chuyển thùng rác" aria-label="Chuyển vào thùng rác" onclick="trashItem(' + r.id + ')">' + ICON_TRASH + '</button>';
        } else {
            if (CAN_EDIT) actions += '<button class="btn btn-sm btn-success" onclick="restoreItem(' + r.id + ')">' + ICON_RESTORE + ' Khôi phục</button>';
            if (CAN_DEL) actions += '<button class="btn btn-sm btn-danger" onclick="deleteItem(' + r.id + ')">Xóa</button>';
        }
        $tb.append(
            '<tr>' +
                '<td class="text-center text-muted">' + stt + '</td>' +
                '<td><strong style="color:var(--gray-900)">' + APP.escape(r.ma_mon_hoc) + '</strong></td>' +
                '<td>' + APP.escape(r.ten_mon_hoc) +
                    (r.mo_ta ? '<div class="text-muted" style="font-size:12px;margin-top:2px">' + APP.escape((r.mo_ta || '').substring(0, 80)) + (r.mo_ta.length > 80 ? '…' : '') + '</div>' : '') +
                '</td>' +
                '<td>' + ctTxt + '</td>' +
                '<td class="text-center">' + tt + '</td>' +
                '<td><div class="actions">' + actions + '</div></td>' +
            '</tr>'
        );
    });
}

function renderInfo(p) {
    var from = (p.currentPage - 1) * p.pageSize + 1;
    var to = Math.min(from + p.pageSize - 1, p.totalRecords);
    $('#pageInfo').text(p.totalRecords ? 'Hiển thị ' + from + '–' + to + ' / ' + p.totalRecords : 'Không có bản ghi');
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

$('#filterTrangThai').on('change', function () { state.trangThai = parseInt(this.value, 10); state.page = 1; load(); });
$('#filterKhoa').on('change', function () {
    var kh = parseInt(this.value, 10) || 0;
    var $ct = $('#filterChuongTrinh').empty().append('<option value="0">-- Chọn chương trình --</option>').prop('disabled', true);
    state.chuongTrinhId = 0; state.page = 1; load();
    if (!kh) return;
    APP.ajax(URL, {action: 'getChuongTrinhTheoKhoa', khoa_hoc_id: kh}).done(function (res) {
        if (!res.success) return;
        var rows = res.data || [];
        if (!rows.length) { $ct.append('<option value="" disabled>(Khóa này chưa có chương trình)</option>'); return; }
        rows.forEach(function (c) { $ct.append('<option value="'+c.chuong_trinh_id+'">'+APP.escape((c.ma_chuong_trinh?c.ma_chuong_trinh+' - ':'')+(c.ten_chuong_trinh||''))+'</option>'); });
        $ct.prop('disabled', false);
    });
});
$('#filterChuongTrinh').on('change', function () { state.chuongTrinhId = parseInt(this.value, 10) || 0; state.page = 1; load(); });
$('#filterDaXoa').on('change', function () { state.daXoa = parseInt(this.value, 10) || 0; state.page = 1; load(); });

function recalcTongTiet() {
    var slt = parseInt($('#f_slt').val(), 10) || 0;
    var sth = parseInt($('#f_sth').val(), 10) || 0;
    $('#f_tst').val(slt + sth);
}
$('#f_slt, #f_sth').on('input', recalcTongTiet);

function openCreate() {
    $('#modalTitle').text('Thêm bài học');
    $('#formMain')[0].reset(); $('#f_id').val(''); $('#f_tst').val(0);
    setCtSelected([]);
    $('#modalForm').addClass('open');
    setTimeout(function () { $('#f_ma').trigger('focus'); }, 50);
}

function openEdit(id) {
    APP.ajax(URL, {action: 'getById', id: id}).done(function (res) {
        if (!res.success) { APP.toast(res.message, 'error'); return; }
        var e = res.data;
        $('#modalTitle').text('Sửa bài học');
        $('#f_id').val(e.id);
        $('#f_ma').val(e.ma_mon_hoc);
        $('#f_ten').val(e.ten_mon_hoc);
        $('#f_slt').val(e.so_tiet_ly_thuyet || 0);
        $('#f_sth').val(e.so_tiet_thuc_hanh || 0);
        $('#f_stc').val(e.so_tin_chi || 0);
        var ids = (res.data.chuong_trinh_ids || []).map(Number);
        setCtSelected(CT_LIST.filter(function (c) { return ids.indexOf(c.id) >= 0; }));
        $('#f_mo_ta').val(e.mo_ta || '');
        $('#f_trang_thai').val(e.trang_thai);
        recalcTongTiet();
        $('#modalForm').addClass('open');
        setTimeout(function () { $('#f_ten').trigger('focus'); }, 50);
    });
}

function closeModal() { $('#modalForm').removeClass('open'); $('#ctSuggest').removeClass('open').empty(); }

// ===== CTĐT picker (chip + suggest) =====
function setCtSelected(arr) { ctSelected = arr.slice(); renderCtChips(); }
function renderCtChips() {
    var $c = $('#ctChips').empty();
    ctSelected.forEach(function (c) {
        $c.append('<span class="ct-chip">' + APP.escape(c.ma) + ' - ' + APP.escape(c.ten) +
            '<button type="button" class="ct-chip-x" data-id="' + c.id + '">&times;</button></span>');
    });
}
$('#ctChips').on('click', '.ct-chip-x', function () {
    var id = parseInt($(this).data('id'), 10);
    ctSelected = ctSelected.filter(function (c) { return c.id !== id; });
    renderCtChips();
});
$('#ctSearch').on('input focus', function () {
    var q = ($(this).val() || '').toLowerCase().trim();
    var chosen = ctSelected.map(function (c) { return c.id; });
    var matches = CT_LIST.filter(function (c) {
        if (chosen.indexOf(c.id) >= 0) return false;
        if (!q) return true;
        return (c.ma + ' ' + c.ten).toLowerCase().indexOf(q) >= 0;
    }).slice(0, 12);
    var $s = $('#ctSuggest').empty();
    if (!matches.length) { $s.html('<div class="ct-suggest-empty">Không có chương trình phù hợp</div>').addClass('open'); return; }
    matches.forEach(function (c) {
        $s.append('<div class="ct-suggest-item" data-id="' + c.id + '"><strong>' + APP.escape(c.ma) + '</strong> - ' + APP.escape(c.ten) + '</div>');
    });
    $s.addClass('open');
});
$('#ctSuggest').on('mousedown', '.ct-suggest-item', function (e) {
    e.preventDefault();
    var id = parseInt($(this).data('id'), 10);
    var c = CT_LIST.filter(function (x) { return x.id === id; })[0];
    if (c) { ctSelected.push(c); renderCtChips(); }
    $('#ctSearch').val('').trigger('input').focus();
});
$(document).on('click', function (e) {
    if (!$(e.target).closest('#ctPicker').length) $('#ctSuggest').removeClass('open');
});

// ESC to close modal
$(document).on('keydown', function (e) {
    if (e.key === 'Escape' && $('#modalForm').hasClass('open')) closeModal();
});

$('#formMain').on('submit', function (e) {
    e.preventDefault();
    var $btn = $('#btnSave').prop('disabled', true);
    var data = $(this).serializeArray();
    data.push({name: 'action', value: $('#f_id').val() ? 'update' : 'insert'});
    ctSelected.forEach(function (c) { data.push({name: 'chuong_trinh_ids[]', value: c.id}); });
    APP.ajax(URL, data).done(function (res) {
        $btn.prop('disabled', false);
        if (res.success) { APP.toast(res.message, 'success'); closeModal(); load(); loadStats(); }
        else APP.toast(res.message, 'error');
    }).fail(function () { $btn.prop('disabled', false); });
});

function trashItem(id) {
    APP.confirm('Chuyển bài học này vào thùng rác?', function () {
        APP.ajax(URL, {action: 'trash', id: id}).done(function (res) {
            res.success ? (APP.toast(res.message, 'success'), load(), loadStats()) : APP.toast(res.message, 'error');
        });
    });
}
function restoreItem(id) {
    APP.ajax(URL, {action: 'restore', id: id}).done(function (res) {
        res.success ? (APP.toast(res.message, 'success'), load(), loadStats()) : APP.toast(res.message, 'error');
    });
}
function deleteItem(id) {
    APP.confirm('Xóa VĨNH VIỄN bài học này? Hành động không thể hoàn tác.', function () {
        APP.ajax(URL, {action: 'delete', id: id}).done(function (res) {
            res.success ? (APP.toast(res.message, 'success'), load(), loadStats()) : APP.toast(res.message, 'error');
        });
    }, {yesText: 'Xóa vĩnh viễn'});
}

// ===== Thêm nhanh 1 CTĐT cho 1 bài (nút + CTĐT ở bảng) =====
var addCtRow = { baiId: 0 };
function openAddCt(baiId, baiTen) {
    addCtRow.baiId = baiId;
    $('#addCtBaiId').val(baiId);
    $('#addCtBaiTen').text(baiTen);
    $('#addCtSearch').val('');
    $('#modalAddCt').addClass('open');
    renderAddCtSuggest('');
    setTimeout(function () { $('#addCtSearch').focus(); }, 80);
}
function renderAddCtSuggest(q) {
    q = (q || '').toLowerCase().trim();
    var matches = CT_LIST.filter(function (c) {
        if (!q) return true;
        return (c.ma + ' ' + c.ten).toLowerCase().indexOf(q) >= 0;
    });
    var $s = $('#addCtSuggest').empty();
    if (!matches.length) { $s.html('<div class="ct-suggest-empty">Không có chương trình phù hợp</div>'); return; }
    matches.forEach(function (c) {
        $s.append('<div class="ct-suggest-item" data-id="' + c.id + '"><strong>' + APP.escape(c.ma) + '</strong> - ' + APP.escape(c.ten) + '</div>');
    });
}
$('#addCtSearch').on('input', function () { renderAddCtSuggest($(this).val()); });
$('#addCtSuggest').on('mousedown', '.ct-suggest-item', function (e) {
    e.preventDefault();
    var ctId = parseInt($(this).data('id'), 10);
    APP.ajax(URL, {action: 'assignCt', mon_hoc_id: addCtRow.baiId, chuong_trinh_id: ctId}).done(function (res) {
        if (res.success) { APP.toast(res.message, 'success'); $('#modalAddCt').removeClass('open'); load(); loadStats(); }
        else APP.toast(res.message, 'error');
    });
});

// ====== Drawer: khóa học gắn với môn này ======
var drwState = { monHocId: 0, monHocTen: '', khoaCombo: null };

function openKhoaDrawer(monHocId, monHocTen) {
    drwState.monHocId = monHocId;
    drwState.monHocTen = monHocTen;
    $('#drwTitle').text('Khóa học gắn với bài');
    $('#drwSub').text(monHocTen);
    $('#drwList').html('');
    $('#drwEmpty').hide();
    $('#drawerKhoa').addClass('open').find('.drawer').addClass('open');
    loadKhoaCuaMon();
    ensureKhoaCombo();
}

function closeKhoaDrawer() {
    $('#drawerKhoa').removeClass('open').find('.drawer').removeClass('open');
}

function loadKhoaCuaMon() {
    APP.ajax(URL, {action: 'listKhoaCuaMon', mon_hoc_id: drwState.monHocId}).done(function (res) {
        if (!res.success) { APP.toast(res.message, 'error'); return; }
        var $l = $('#drwList').empty();
        var rows = res.data || [];
        if (!rows.length) { $('#drwEmpty').show(); return; }
        $('#drwEmpty').hide();
        rows.forEach(function (r) {
            $l.append(
                '<div class="mh-khoa-row">' +
                    '<div class="mh-khoa-info">' +
                        '<div class="mh-khoa-name">' + APP.escape(r.ten_chuong_trinh || '') + '</div>' +
                        '<div class="mh-khoa-code">' + APP.escape(r.ma_chuong_trinh || '') + ' · #' + r.thu_tu + '</div>' +
                    '</div>' +
                    '<span class="mh-khoa-tag ' + (parseInt(r.bat_buoc, 10) === 1 ? 'bb' : 'tc') + '">' +
                        (parseInt(r.bat_buoc, 10) === 1 ? 'Bắt buộc' : 'Tự chọn') + '</span>' +
                    '<button type="button" class="btn btn-sm btn-danger" title="Gỡ khỏi khóa" onclick="removeFromKhoa(' + r.id + ')">' + ICON_TRASH + '</button>' +
                '</div>'
            );
        });
    });
}

function ensureKhoaCombo() {
    if (drwState.khoaCombo) { renderKhoaCombo(); return; }
    APP.ajax(URL, {action: 'getKhoaCombo'}).done(function (res) {
        if (res.success) {
            drwState.khoaCombo = res.data || [];
            renderKhoaCombo();
        }
    });
}
function renderKhoaCombo() {
    var $s = $('#drwKhoaSelect').empty().append('<option value="">-- Chọn chương trình --</option>');
    (drwState.khoaCombo || []).forEach(function (k) {
        $s.append('<option value="' + k.id + '">' + APP.escape(k.ma_chuong_trinh + ' - ' + k.ten_chuong_trinh) + '</option>');
    });
}

$('#btnAddToKhoa').on('click', function () {
    var khoaId = parseInt($('#drwKhoaSelect').val(), 10);
    if (!khoaId) { APP.toast('Chọn chương trình', 'error'); return; }
    var bb = $('#drwBatBuoc').is(':checked') ? 1 : 0;
    APP.ajax(URL, {
        action: 'addMonToKhoa',
        mon_hoc_id: drwState.monHocId,
        chuong_trinh_id: khoaId,
        bat_buoc: bb
    }).done(function (res) {
        if (res.success) {
            APP.toast(res.message, 'success');
            $('#drwKhoaSelect').val('');
            loadKhoaCuaMon();
            load();  // refresh chip "X khóa" trong table
        } else APP.toast(res.message, 'error');
    });
});

function removeFromKhoa(id) {
    APP.confirm('Gỡ bài này khỏi khóa học?', function () {
        APP.ajax(URL, {action: 'removeMonKhoiKhoa', id: id}).done(function (res) {
            if (res.success) {
                APP.toast(res.message, 'success');
                loadKhoaCuaMon();
                load();
            } else APP.toast(res.message, 'error');
        });
    });
}

loadStats();
load();
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
