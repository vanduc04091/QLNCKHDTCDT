<?php
require_once __DIR__ . '/../../bootstrap.php';

Helper::requireLogin();
if (!PhanQuyenHelper::hasQuyen('NCKH_DeTaiCuaToi', PhanQuyenHelper::QUYEN_XEM)) {
    echo 'Bạn không có quyền truy cập chức năng này.'; exit;
}

$pageTitle = 'Đề tài của tôi';
$activeMenu = 'NCKH_DeTaiCuaToi';
require __DIR__ . '/../layouts/header.php';
?>
<style>
/* Tabs trạng thái */
.dtcm-tabs { display:flex; gap:4px; flex-wrap:wrap; padding:0; margin:0 0 16px; border-bottom:1px solid #e2e8f0; }
.dtcm-tab { padding:10px 16px; background:none; border:none; border-bottom:3px solid transparent; cursor:pointer; font-size:14px; color:#64748b; display:flex; align-items:center; gap:8px; transition:color .2s, border-color .2s; }
.dtcm-tab:hover { color:#0f172a; }
.dtcm-tab.active { color:#2563eb; border-bottom-color:#2563eb; font-weight:600; }
.dtcm-tab .badge-count { background:#e2e8f0; color:#475569; font-size:11px; font-weight:600; padding:2px 8px; border-radius:99px; min-width:20px; text-align:center; }
.dtcm-tab.active .badge-count { background:#dbeafe; color:#1d4ed8; }

/* Card đề tài */
.dtcm-grid { display:grid; gap:14px; }
.dtcm-card { background:#fff; border:1px solid #e2e8f0; border-radius:10px; padding:16px 18px; transition:box-shadow .2s; }
.dtcm-card:hover { box-shadow:0 4px 12px rgba(0,0,0,.06); }
.dtcm-card-head { display:flex; justify-content:space-between; gap:12px; align-items:flex-start; margin-bottom:8px; }
.dtcm-card-title { font-size:15px; font-weight:600; color:#0f172a; line-height:1.4; flex:1; min-width:0; }
.dtcm-card-meta { display:flex; gap:14px; flex-wrap:wrap; font-size:12px; color:#64748b; }
.dtcm-card-meta .item { display:inline-flex; align-items:center; gap:5px; }
.dtcm-card-actions { display:flex; gap:8px; flex-wrap:wrap; margin-top:14px; padding-top:12px; border-top:1px dashed #e2e8f0; }

/* Status badge */
.tt-duyet { display:inline-flex; align-items:center; gap:6px; padding:4px 10px; border-radius:99px; font-size:12px; font-weight:600; line-height:1; flex:0 0 auto; }
.tt-duyet svg { flex:0 0 auto; }
.tt-Nhap { background:#f1f5f9; color:#475569; }
.tt-ChoDuyet { background:#fef3c7; color:#92400e; }
.tt-DaDuyet { background:#dcfce7; color:#166534; }
.tt-TuChoi { background:#fee2e2; color:#991b1b; }

/* Banner lý do từ chối */
.dtcm-rejected { background:#fef2f2; border-left:3px solid #dc2626; padding:10px 14px; margin-top:10px; border-radius:6px; font-size:13px; color:#991b1b; }
.dtcm-rejected strong { color:#7f1d1d; }

/* Empty state */
.dtcm-empty { text-align:center; padding:60px 20px; background:#fff; border:2px dashed #e2e8f0; border-radius:12px; }
.dtcm-empty svg { color:#94a3b8; margin-bottom:12px; }
.dtcm-empty .title { font-size:16px; font-weight:600; color:#0f172a; margin-bottom:4px; }
.dtcm-empty .desc { font-size:13px; color:#64748b; margin-bottom:20px; }

/* Hero panel + CTA */
.dtcm-hero { background:linear-gradient(135deg,#2563eb,#7c3aed); color:#fff; padding:20px 24px; border-radius:12px; margin-bottom:20px; display:flex; justify-content:space-between; align-items:center; gap:16px; flex-wrap:wrap; }
.dtcm-hero h2 { margin:0 0 4px; font-size:18px; }
.dtcm-hero p { margin:0; font-size:13px; opacity:.9; }
.dtcm-hero .btn-cta { background:#fff; color:#2563eb; font-weight:600; }
.dtcm-hero .btn-cta:hover { background:#eff6ff; }
</style>

<div class="breadcrumb">
    <a href="<?= AppConfig::baseUrl('GUI/dashboard/index.php') ?>">Trang chủ</a>
    <span class="sep">›</span> Nghiên cứu khoa học
    <span class="sep">›</span> <span>Đề tài của tôi</span>
</div>

<div class="dtcm-hero">
    <div>
        <h2>Đề tài của tôi</h2>
        <p>Tạo nháp đề tài, bổ sung thành viên / hội đồng / tài liệu, sau đó gửi cho quản trị viên duyệt.</p>
    </div>
    <a class="btn btn-cta" href="<?= AppConfig::baseUrl('GUI/NCKH_DeTaiCuaToi/wizard.php') ?>">+ Tạo đề tài mới</a>
</div>

<div class="card" style="padding:18px">
    <div class="dtcm-tabs" id="tabs">
        <button class="dtcm-tab active" data-tab="all">Tất cả <span class="badge-count" id="cnt-all">0</span></button>
        <button class="dtcm-tab" data-tab="nhap">Nháp <span class="badge-count" id="cnt-Nhap">0</span></button>
        <button class="dtcm-tab" data-tab="cho">Chờ duyệt <span class="badge-count" id="cnt-ChoDuyet">0</span></button>
        <button class="dtcm-tab" data-tab="duyet">Đã duyệt <span class="badge-count" id="cnt-DaDuyet">0</span></button>
        <button class="dtcm-tab" data-tab="tuchoi">Từ chối <span class="badge-count" id="cnt-TuChoi">0</span></button>
    </div>

    <div style="display:flex;gap:8px;margin-bottom:14px">
        <input type="text" id="search" class="form-control" placeholder="Tìm theo mã / tên / từ khóa..." style="max-width:380px">
    </div>

    <div id="listWrap" style="position:relative;min-height:200px">
        <div class="dtcm-grid" id="cards"></div>
        <div id="emptyState"></div>
    </div>
    <div class="pagination-wrap">
        <div id="pageInfo" class="text-muted">-</div>
        <div id="pageNav"></div>
    </div>
</div>

<script>
var URL = APP_BASE + 'GUI/NCKH_DeTaiCuaToi/ajax_handler.php';
var WIZARD_URL = APP_BASE + 'GUI/NCKH_DeTaiCuaToi/wizard.php';
var TT_DUYET_NAMES = {Nhap:'Nháp', ChoDuyet:'Chờ duyệt', DaDuyet:'Đã duyệt', TuChoi:'Từ chối'};
var ICON_EDIT = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>';
var ICON_TRASH = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>';
var ICON_SEND = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>';
var ICON_CHECK = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>';
var ICON_CLOCK = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>';
var ICON_FILE = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>';

var state = { tab: 'all', page: 1, pageSize: 12, search: '' };

function statusIcon(code) {
    if (code === 'DaDuyet') return ICON_CHECK;
    if (code === 'TuChoi') return '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>';
    if (code === 'ChoDuyet') return ICON_CLOCK;
    return ICON_FILE;
}

function loadCounts() {
    APP.ajax(URL, {action:'getMyCounts'}).done(function (r) {
        if (!r.success) return;
        ['all','Nhap','ChoDuyet','DaDuyet','TuChoi'].forEach(function (k) {
            $('#cnt-' + k).text(r.data[k] || 0);
        });
    });
}

function loadList() {
    APP.showLoading('#listWrap');
    APP.ajax(URL, {action:'getMyList', tab:state.tab, page:state.page, pageSize:state.pageSize, search:state.search}).done(function (res) {
        APP.hideLoading('#listWrap');
        if (!res.success) { APP.toast(res.message,'error'); return; }
        renderCards(res.data);
        renderInfo(res.pagination);
    });
}

function renderCards(rows) {
    var $cards = $('#cards').empty();
    var $empty = $('#emptyState').empty();
    if (!rows.length) {
        $empty.html(emptyHtml());
        return;
    }
    rows.forEach(function (r) {
        var ttd = r.trang_thai_duyet || 'Nhap';
        var ttdHtml = '<span class="tt-duyet tt-' + ttd + '">' + statusIcon(ttd) + (TT_DUYET_NAMES[ttd] || ttd) + '</span>';
        var btns = '';
        if (ttd === 'Nhap' || ttd === 'TuChoi') {
            btns += '<a class="btn btn-sm btn-primary" href="' + WIZARD_URL + '?id=' + r.id + '">' + ICON_EDIT + ' Tiếp tục soạn</a>';
            btns += '<button class="btn btn-sm" onclick="submitDt(' + r.id + ')">' + ICON_SEND + ' Gửi duyệt</button>';
            btns += '<button class="btn btn-sm btn-danger" onclick="deleteDt(' + r.id + ')">' + ICON_TRASH + ' Xóa</button>';
        } else if (ttd === 'ChoDuyet') {
            btns += '<a class="btn btn-sm" href="' + WIZARD_URL + '?id=' + r.id + '&view=1">Xem</a>';
            btns += '<span class="text-muted" style="font-size:12px;align-self:center">Đang chờ quản trị viên xử lý</span>';
        } else if (ttd === 'DaDuyet') {
            btns += '<a class="btn btn-sm" href="' + WIZARD_URL + '?id=' + r.id + '&view=1">Xem</a>';
        }

        var rejBlock = '';
        if (ttd === 'TuChoi' && r.ly_do_tu_choi) {
            rejBlock = '<div class="dtcm-rejected"><strong>Lý do từ chối:</strong> ' + APP.escape(r.ly_do_tu_choi) + '</div>';
        }

        var khoa = r.ten_khoa || r.ten_khoa_text || '-';

        $cards.append(
            '<div class="dtcm-card">' +
                '<div class="dtcm-card-head">' +
                    '<div class="dtcm-card-title">' + APP.escape(r.ten_de_tai) + '</div>' +
                    ttdHtml +
                '</div>' +
                '<div class="dtcm-card-meta">' +
                    '<span class="item"><strong>' + APP.escape(r.ma_de_tai) + '</strong></span>' +
                    '<span class="item">📅 ' + r.nam + '</span>' +
                    '<span class="item">' + APP.escape(r.ten_cap_do || '-') + '</span>' +
                    '<span class="item">' + APP.escape(r.ten_the_loai || '-') + '</span>' +
                    '<span class="item">' + APP.escape(khoa) + '</span>' +
                '</div>' +
                rejBlock +
                '<div class="dtcm-card-actions">' + btns + '</div>' +
            '</div>'
        );
    });
}

function emptyHtml() {
    var msg = state.tab === 'all'
        ? { t:'Bạn chưa có đề tài nào', d:'Bắt đầu bằng cách tạo đề tài đầu tiên của bạn.', cta:true }
        : { t:'Không có đề tài ở mục này', d:'Thử chuyển sang tab khác hoặc tìm kiếm.', cta:false };
    var ctaBtn = msg.cta ? '<a class="btn btn-primary" href="' + WIZARD_URL + '">+ Tạo đề tài mới</a>' : '';
    return '<div class="dtcm-empty">' +
        '<svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>' +
        '<div class="title">' + msg.t + '</div>' +
        '<div class="desc">' + msg.d + '</div>' +
        ctaBtn +
        '</div>';
}

function renderInfo(p) {
    if (!p || !p.totalRecords) { $('#pageInfo').text(''); $('#pageNav').empty(); return; }
    var from = (p.currentPage - 1) * p.pageSize + 1;
    var to = Math.min(from + p.pageSize - 1, p.totalRecords);
    $('#pageInfo').text('Hiển thị ' + from + '-' + to + ' / ' + p.totalRecords);
    $('#pageNav').html(APP.renderPagination(p));
}

$('#tabs').on('click', '.dtcm-tab', function () {
    $('.dtcm-tab').removeClass('active'); $(this).addClass('active');
    state.tab = $(this).data('tab'); state.page = 1;
    loadList();
});
$('#pageNav').on('click', 'button[data-p]', function () {
    var p = parseInt($(this).data('p'),10); if(!p||p===state.page)return; state.page=p; loadList();
});
$('#search').on('input', APP.debounce(function () { state.search = $(this).val(); state.page = 1; loadList(); }, 400));

function submitDt(id) {
    APP.confirm('Gửi đề tài này cho quản trị viên duyệt? Sau khi gửi bạn sẽ không sửa được cho đến khi có phản hồi.', function () {
        APP.ajax(URL, {action:'submit', id:id}).done(function (r) {
            r.success ? (APP.toast(r.message,'success'), loadCounts(), loadList())
                      : APP.toast(r.message,'error');
        });
    }, {yesText:'Gửi duyệt'});
}
function deleteDt(id) {
    APP.confirm('Xóa đề tài nháp này? Hành động này không thể hoàn tác.', function () {
        APP.ajax(URL, {action:'deleteDraft', id:id}).done(function (r) {
            r.success ? (APP.toast(r.message,'success'), loadCounts(), loadList())
                      : APP.toast(r.message,'error');
        });
    }, {yesText:'Xóa'});
}

loadCounts(); loadList();
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
