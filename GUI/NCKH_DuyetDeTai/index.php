<?php
require_once __DIR__ . '/../../bootstrap.php';

Helper::requireLogin();
if (!PhanQuyenHelper::hasQuyen('NCKH_DuyetDeTai', PhanQuyenHelper::QUYEN_XEM)) {
    echo 'Bạn không có quyền truy cập chức năng này.'; exit;
}
$canApprove = PhanQuyenHelper::hasQuyen('NCKH_DuyetDeTai', PhanQuyenHelper::QUYEN_SUA);

$pageTitle = 'Duyệt đề tài NCKH';
$activeMenu = 'NCKH_DuyetDeTai';
require __DIR__ . '/../layouts/header.php';
?>
<style>
.dq-tabs { display:flex; gap:4px; flex-wrap:wrap; padding:0; margin:0 0 16px; border-bottom:1px solid #e2e8f0; }
.dq-tab { padding:10px 16px; background:none; border:none; border-bottom:3px solid transparent; cursor:pointer; font-size:14px; color:#64748b; display:flex; align-items:center; gap:8px; }
.dq-tab:hover { color:#0f172a; }
.dq-tab.active { color:#2563eb; border-bottom-color:#2563eb; font-weight:600; }
.dq-tab .badge-count { background:#e2e8f0; color:#475569; font-size:11px; font-weight:600; padding:2px 8px; border-radius:99px; }
.dq-tab.active .badge-count { background:#dbeafe; color:#1d4ed8; }
.dq-tab.cho .badge-count { background:#fef3c7; color:#92400e; }
.dq-tab.cho.active .badge-count { background:#fde68a; color:#78350f; }

.tt-duyet { display:inline-flex; align-items:center; gap:6px; padding:4px 10px; border-radius:99px; font-size:12px; font-weight:600; }
.tt-ChoDuyet { background:#fef3c7; color:#92400e; }
.tt-DaDuyet { background:#dcfce7; color:#166534; }
.tt-TuChoi { background:#fee2e2; color:#991b1b; }

/* Drawer chi tiết */
.dq-drawer { position:fixed; top:0; right:-960px; width:min(960px, 96vw); height:100vh; background:#fff; box-shadow:-8px 0 32px rgba(0,0,0,.15); transition:right .3s ease; z-index:80; display:flex; flex-direction:column; }
.dq-drawer.open { right:0; }
.dq-drawer-head { padding:16px 20px; border-bottom:1px solid #e2e8f0; display:flex; justify-content:space-between; align-items:center; gap:12px; }
.dq-drawer-body { flex:1; overflow:auto; padding:18px 20px; }
.dq-drawer-footer { padding:14px 20px; border-top:1px solid #e2e8f0; display:flex; gap:8px; justify-content:flex-end; background:#f8fafc; }
.dq-section { margin-bottom:18px; }
.dq-section h4 { margin:0 0 8px; font-size:14px; color:#2563eb; font-weight:600; }
.dq-info-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(280px,1fr)); gap:6px 20px; }
.dq-info-grid .row { display:flex; gap:6px; padding:6px 0; border-bottom:1px dashed #e2e8f0; font-size:13px; }
.dq-info-grid .row .lbl { color:#64748b; min-width:130px; }
.dq-info-grid .row .val { color:#0f172a; flex:1; }
.dq-sub { background:#f8fafc; padding:10px 14px; border-radius:8px; margin-bottom:8px; border:1px solid #e2e8f0; }
.dq-sub .name { font-weight:600; color:#0f172a; font-size:13px; }
.dq-sub .meta { font-size:12px; color:#64748b; margin-top:2px; }
</style>

<div class="breadcrumb">
    <a href="<?= AppConfig::baseUrl('GUI/dashboard/index.php') ?>">Trang chủ</a>
    <span class="sep">›</span> Nghiên cứu khoa học
    <span class="sep">›</span> <span>Duyệt đề tài</span>
</div>

<div class="card" style="padding:18px">
    <div class="dq-tabs" id="tabs">
        <button class="dq-tab cho active" data-tab="cho">Chờ duyệt <span class="badge-count" id="cnt-ChoDuyet">0</span></button>
        <button class="dq-tab" data-tab="duyet">Đã duyệt <span class="badge-count" id="cnt-DaDuyet">0</span></button>
        <button class="dq-tab" data-tab="tuchoi">Từ chối <span class="badge-count" id="cnt-TuChoi">0</span></button>
        <button class="dq-tab" data-tab="all">Tất cả</button>
    </div>

    <div style="display:flex;gap:8px;margin-bottom:14px;flex-wrap:wrap">
        <input type="text" id="search" class="form-control" placeholder="Tìm theo mã / tên / chủ nhiệm..." style="max-width:380px">
        <select id="fNam" class="form-select" style="width:120px"><option value="">- Năm -</option></select>
        <select id="fDot" class="form-select" style="width:240px"><option value="">- Mọi đợt -</option></select>
    </div>

    <div class="table-wrap" id="tableWrap" style="position:relative;min-height:200px">
        <table class="table">
            <thead>
                <tr>
                    <th style="width:50px" class="text-center">#</th>
                    <th style="width:120px">Mã</th>
                    <th>Tên đề tài</th>
                    <th style="width:80px" class="text-center">Năm</th>
                    <th style="width:120px">Cấp / Thể loại</th>
                    <th style="width:170px">Chủ nhiệm</th>
                    <th style="width:140px">Người gửi</th>
                    <th style="width:120px" class="text-center">Trạng thái</th>
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

<!-- Drawer chi tiết -->
<div class="dq-drawer" id="drawer">
    <div class="dq-drawer-head">
        <div>
            <h3 id="dr_title" style="margin:0;font-size:16px"></h3>
            <div id="dr_subtitle" style="color:#64748b;font-size:12px;margin-top:2px"></div>
        </div>
        <button type="button" class="btn" onclick="closeDrawer()">×</button>
    </div>
    <div class="dq-drawer-body" id="dr_body"></div>
    <div class="dq-drawer-footer" id="dr_footer"></div>
</div>

<!-- Modal từ chối -->
<div class="modal-backdrop" id="modalReject">
    <div class="modal" style="max-width:520px">
        <div class="modal-header"><h3>Từ chối đề tài</h3><button type="button" class="close" onclick="$('#modalReject').removeClass('open')">&times;</button></div>
        <form id="formReject">
            <div class="modal-body">
                <input type="hidden" id="rj_id">
                <div class="form-group">
                    <label>Lý do từ chối <span class="required">*</span></label>
                    <textarea id="rj_lyDo" class="form-control" rows="4" required placeholder="Nêu lý do để nhân viên có thể chỉnh sửa và gửi lại..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn" onclick="$('#modalReject').removeClass('open')">Hủy</button>
                <button type="submit" class="btn btn-danger">Từ chối</button>
            </div>
        </form>
    </div>
</div>

<script>
var URL = APP_BASE + 'GUI/NCKH_DuyetDeTai/ajax_handler.php';
var DOWNLOAD_URL = APP_BASE + 'GUI/NCKH_DeTai/download.php';
var CAN_APPROVE = <?= $canApprove ? 'true' : 'false' ?>;
var TT_DUYET = {ChoDuyet:'Chờ duyệt', DaDuyet:'Đã duyệt', TuChoi:'Từ chối'};
var HD_VAITRO = {ChuTich:'Chủ tịch', ThuKy:'Thư ký', PhanBien1:'Phản biện 1', PhanBien2:'Phản biện 2', ThanhVien:'Thành viên'};
var LOAI_TL = {DeCuong:'Đề cương', QuyetDinh:'Quyết định', BienBan:'Biên bản', BaoCao:'Báo cáo', FileGoc:'File gốc', Khac:'Khác'};
var state = { tab: 'cho', page:1, pageSize:20, search:'', nam:0, dotId:0 };
var currentId = 0;

function fillYears() {
    var y = new Date().getFullYear();
    for (var i = y + 1; i >= y - 5; i--) $('#fNam').append('<option value="' + i + '">' + i + '</option>');
}

function loadCounts() {
    APP.ajax(URL, {action:'getCounts'}).done(function (r) {
        if (!r.success) return;
        ['ChoDuyet','DaDuyet','TuChoi'].forEach(function (k) { $('#cnt-' + k).text(r.data[k] || 0); });
    });
}

function load() {
    APP.showLoading('#tableWrap');
    APP.ajax(URL, {action:'getQueue', tab:state.tab, page:state.page, pageSize:state.pageSize, search:state.search, nam:state.nam, dot_dang_ky_id:state.dotId}).done(function (res) {
        APP.hideLoading('#tableWrap');
        if (!res.success) { APP.toast(res.message,'error'); return; }
        renderRows(res.data); renderInfo(res.pagination);
    });
}

function renderRows(rows) {
    var $tb = $('#tbody').empty();
    if (!rows.length) { $tb.append('<tr><td colspan="9"><div class="empty-state">Không có đề tài</div></td></tr>'); return; }
    var stt = (state.page - 1) * state.pageSize;
    rows.forEach(function (r) {
        stt++;
        var ttd = r.trang_thai_duyet || '';
        var ttdHtml = '<span class="tt-duyet tt-' + ttd + '">' + (TT_DUYET[ttd] || ttd) + '</span>';
        var khoaCap = APP.escape(r.ten_cap_do || '-') + '<div class="text-muted" style="font-size:11px">' + APP.escape(r.ten_the_loai || '') + '</div>';
        var actions = '<button class="btn btn-sm btn-primary" onclick="openDrawer(' + r.id + ')">Xem</button>';
        $tb.append(
            '<tr>' +
                '<td class="text-center">' + stt + '</td>' +
                '<td><strong>' + APP.escape(r.ma_de_tai) + '</strong></td>' +
                '<td><a href="javascript:;" onclick="openDrawer(' + r.id + ')" style="color:#0f172a;font-weight:500;text-decoration:none">' + APP.escape(r.ten_de_tai) + '</a>' +
                    (r.ngay_gui_duyet ? '<div class="text-muted" style="font-size:11px;margin-top:2px">Gửi: ' + APP.formatDateTime(r.ngay_gui_duyet) + '</div>' : '') +
                '</td>' +
                '<td class="text-center">' + r.nam + '</td>' +
                '<td>' + khoaCap + '</td>' +
                '<td>' + APP.escape(r.ho_ten_chu_nhiem || '-') + '</td>' +
                '<td>' + APP.escape(r.tai_khoan_nguoi_tao || '-') + '</td>' +
                '<td class="text-center">' + ttdHtml + '</td>' +
                '<td class="text-right">' + actions + '</td>' +
            '</tr>'
        );
    });
}

function renderInfo(p) {
    if (!p || !p.totalRecords) { $('#pageInfo').text(''); $('#pageNav').empty(); return; }
    var from = (p.currentPage - 1) * p.pageSize + 1;
    var to = Math.min(from + p.pageSize - 1, p.totalRecords);
    $('#pageInfo').text('Hiển thị ' + from + '-' + to + ' / ' + p.totalRecords);
    $('#pageNav').html(APP.renderPagination(p));
}

$('#tabs').on('click', '.dq-tab', function () {
    $('.dq-tab').removeClass('active'); $(this).addClass('active');
    state.tab = $(this).data('tab'); state.page = 1; load();
});
$('#pageNav').on('click', 'button[data-p]', function () { var p=parseInt($(this).data('p'),10); if(!p||p===state.page)return; state.page=p; load(); });
$('#search').on('input', APP.debounce(function () { state.search=$(this).val(); state.page=1; load(); }, 400));
$('#fNam').on('change', function () { state.nam = parseInt(this.value,10) || 0; state.page=1; load(); });
$('#fDot').on('change', function () { state.dotId = parseInt(this.value,10) || 0; state.page=1; load(); });

function loadDotCombo() {
    APP.ajax(URL, {action:'getComboDot'}).done(function (r) {
        if (!r.success) return;
        $.each(r.data || [], function (_, x) {
            $('#fDot').append('<option value="' + x.id + '">' + APP.escape(x.ten_dot + ' (' + x.nam + ')') + '</option>');
        });
    });
}

function openDrawer(id) {
    currentId = id; $('#drawer').addClass('open');
    $('#dr_body').html('<div class="text-center text-muted" style="padding:40px">Đang tải...</div>');
    APP.ajax(URL, {action:'getDetail', id:id}).done(function (r) {
        if (!r.success) { APP.toast(r.message,'error'); return; }
        renderDetail(r.data);
    });
}
function closeDrawer() { $('#drawer').removeClass('open'); currentId = 0; }

function renderDetail(d) {
    var e = d.de_tai;
    $('#dr_title').text(e.ten_de_tai);
    $('#dr_subtitle').html('<strong>' + APP.escape(e.ma_de_tai) + '</strong> · ' + e.nam +
        ' · <span class="tt-duyet tt-' + e.trang_thai_duyet + '">' + (TT_DUYET[e.trang_thai_duyet]||e.trang_thai_duyet) + '</span>');

    var html = '';
    // Banner lý do từ chối nếu có
    if (e.trang_thai_duyet === 'TuChoi' && e.ly_do_tu_choi) {
        html += '<div style="background:#fef2f2;border-left:3px solid #dc2626;padding:10px 14px;margin-bottom:14px;border-radius:6px;color:#991b1b;font-size:13px"><strong>Lý do từ chối:</strong> ' + APP.escape(e.ly_do_tu_choi) + '</div>';
    }

    html += '<div class="dq-section"><h4>Thông tin chính</h4><div class="dq-info-grid">' +
        row('Cấp độ', APP.escape(e.ten_cap_do || '-')) +
        row('Thể loại', APP.escape(e.ten_the_loai || '-')) +
        row('Khoa/Phòng', APP.escape(e.ten_khoa || e.ten_khoa_text || '-')) +
        row('Chủ nhiệm', APP.escape(e.ho_ten_chu_nhiem || '-')) +
        row('Thư ký', APP.escape(e.ho_ten_thu_ky || '-')) +
        row('Năm', e.nam) +
        row('Bắt đầu', APP.formatDate(e.ngay_bat_dau)) +
        row('Dự kiến KT', APP.formatDate(e.ngay_ket_thuc_du_kien)) +
        row('Kinh phí dự toán', e.kinh_phi_du_toan ? Number(e.kinh_phi_du_toan).toLocaleString('vi-VN') + ' đ' : '-') +
        row('Nguồn KP', APP.escape(e.nguon_kinh_phi || '-')) +
        row('Người gửi', APP.escape(e.tai_khoan_nguoi_tao || '-')) +
        row('Ngày gửi', APP.formatDateTime(e.ngay_gui_duyet)) +
        '</div></div>';

    if (e.muc_tieu) html += '<div class="dq-section"><h4>Mục tiêu</h4><div style="white-space:pre-wrap;font-size:13px">' + APP.escape(e.muc_tieu) + '</div></div>';
    if (e.tom_tat) html += '<div class="dq-section"><h4>Tóm tắt</h4><div style="white-space:pre-wrap;font-size:13px">' + APP.escape(e.tom_tat) + '</div></div>';
    if (e.tu_khoa) html += '<div class="dq-section"><h4>Từ khóa</h4><div style="font-size:13px">' + APP.escape(e.tu_khoa) + '</div></div>';

    // Thành viên
    html += '<div class="dq-section"><h4>Nhóm nghiên cứu (' + (d.thanh_vien || []).length + ')</h4>';
    if (!(d.thanh_vien || []).length) html += '<div class="text-muted" style="font-size:13px">Chưa có thành viên.</div>';
    (d.thanh_vien || []).forEach(function (t) {
        var ten = t.ho_ten_nv || t.ho_ten_ngoai || '(Không tên)';
        var ma = t.ma_nv || t.ma_nv_text || '';
        var donVi = t.nhan_vien_id ? (t.ten_khoa_phong || t.chuc_danh || '') : (t.don_vi_ngoai || 'Người ngoài');
        html += '<div class="dq-sub"><div class="name">' + APP.escape(ten) + (ma ? ' <span class="text-muted">[' + APP.escape(ma) + ']</span>' : '') + '</div><div class="meta">' + APP.escape(donVi) + ' · ' + APP.escape(t.vai_tro) + '</div></div>';
    });
    html += '</div>';

    // Hội đồng
    html += '<div class="dq-section"><h4>Hội đồng (' + (d.hoi_dong || []).length + ')</h4>';
    if (!(d.hoi_dong || []).length) html += '<div class="text-muted" style="font-size:13px">Chưa có hội đồng.</div>';
    (d.hoi_dong || []).forEach(function (h) {
        var ten = (h.chuc_danh_hoc_vi ? h.chuc_danh_hoc_vi + ' ' : '') + h.ho_ten;
        var donVi = h.ten_khoa_phong || h.ten_khoa_text || '';
        html += '<div class="dq-sub"><div class="name">' + APP.escape(ten) + '</div><div class="meta"><span class="badge">' + (HD_VAITRO[h.vai_tro_hd]||h.vai_tro_hd) + '</span>' + (donVi ? ' · ' + APP.escape(donVi) : '') + '</div></div>';
    });
    html += '</div>';

    // Tài liệu
    html += '<div class="dq-section"><h4>Tài liệu (' + (d.tai_lieu || []).length + ')</h4>';
    if (!(d.tai_lieu || []).length) html += '<div class="text-muted" style="font-size:13px">Chưa có tài liệu.</div>';
    (d.tai_lieu || []).forEach(function (t) {
        var size = t.kich_thuoc ? (t.kich_thuoc/1024/1024).toFixed(2) + ' MB' : '';
        html += '<div class="dq-sub" style="display:flex;justify-content:space-between;align-items:center;gap:10px">' +
            '<div><div class="name">' + APP.escape(t.ten_tai_lieu) + '</div><div class="meta"><span class="badge">' + (LOAI_TL[t.loai_tai_lieu]||t.loai_tai_lieu) + '</span> ' + APP.escape(t.ten_file_goc || '') + (size ? ' · ' + size : '') + '</div></div>' +
            '<a class="btn btn-sm" target="_blank" href="' + DOWNLOAD_URL + '?id=' + t.id + '">Tải</a>' +
            '</div>';
    });
    html += '</div>';

    $('#dr_body').html(html);

    // Footer actions
    var fHtml = '';
    if (e.trang_thai_duyet === 'ChoDuyet' && CAN_APPROVE) {
        fHtml = '<button class="btn btn-danger" onclick="openReject(' + e.id + ')">✕ Từ chối</button>' +
                '<button class="btn btn-success" onclick="approve(' + e.id + ')">✓ Duyệt</button>';
    } else {
        fHtml = '<button class="btn" onclick="closeDrawer()">Đóng</button>';
    }
    $('#dr_footer').html(fHtml);
}

function row(l, v) { return '<div class="row"><div class="lbl">' + l + '</div><div class="val">' + v + '</div></div>'; }

function approve(id) {
    APP.confirm('Duyệt đề tài này?', function () {
        APP.ajax(URL, {action:'approve', id:id}).done(function (r) {
            if (r.success) { APP.toast(r.message,'success'); closeDrawer(); loadCounts(); load(); }
            else APP.toast(r.message,'error');
        });
    }, {yesText:'Duyệt'});
}
function openReject(id) {
    $('#rj_id').val(id); $('#rj_lyDo').val(''); $('#modalReject').addClass('open');
    setTimeout(function () { $('#rj_lyDo').focus(); }, 200);
}
$('#formReject').on('submit', function (e) {
    e.preventDefault();
    var id = $('#rj_id').val(); var lyDo = $('#rj_lyDo').val().trim();
    if (!lyDo) return;
    APP.ajax(URL, {action:'reject', id:id, ly_do:lyDo}).done(function (r) {
        if (r.success) { APP.toast(r.message,'success'); $('#modalReject').removeClass('open'); closeDrawer(); loadCounts(); load(); }
        else APP.toast(r.message,'error');
    });
});

fillYears(); loadDotCombo(); loadCounts(); load();
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
