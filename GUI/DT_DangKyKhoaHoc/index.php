<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../BUS/DT_KhoaHoc_BUS.php';

Helper::requireLogin();
if (!PhanQuyenHelper::hasQuyen('DT_DangKyKhoaHoc', PhanQuyenHelper::QUYEN_XEM)) {
    echo 'Bạn không có quyền truy cập chức năng này.'; exit;
}
$canEdit = PhanQuyenHelper::hasQuyen('DT_DangKyKhoaHoc', PhanQuyenHelper::QUYEN_SUA);
$canDel  = PhanQuyenHelper::hasQuyen('DT_DangKyKhoaHoc', PhanQuyenHelper::QUYEN_XOA);

$khoaHocList = DT_KhoaHoc_BUS::getCombo();

$pageTitle  = 'Đăng ký khóa học';
$activeMenu = 'DT_DangKyKhoaHoc';
require __DIR__ . '/../layouts/header.php';

$publicDangKyUrl = AppConfig::baseUrl('GUI/public/dang_ky.php');
$publicTraCuuUrl = AppConfig::baseUrl('GUI/public/tra_cuu.php');
?>
<div class="breadcrumb">
    <a href="<?= AppConfig::baseUrl('GUI/dashboard/index.php') ?>">Trang chủ</a>
    <span class="sep">›</span> Đào tạo
    <span class="sep">›</span> <span>Đăng ký khóa học</span>
</div>

<div class="hv-stats">
    <div class="hv-stat">
        <div class="hv-stat-icon hv-stat-blue"><?= IconHelper::svg('clipboard-list', '22') ?></div>
        <div><div class="hv-stat-label">Tổng đơn</div><div class="hv-stat-value" id="stTotal">—</div></div>
    </div>
    <div class="hv-stat">
        <div class="hv-stat-icon hv-stat-orange"><?= IconHelper::svg('clock-history', '22') ?></div>
        <div><div class="hv-stat-label">Chờ duyệt</div><div class="hv-stat-value" id="stCho">—</div></div>
    </div>
    <div class="hv-stat">
        <div class="hv-stat-icon hv-stat-green"><?= IconHelper::svg('check-circle', '22') ?></div>
        <div><div class="hv-stat-label">Đã duyệt</div><div class="hv-stat-value" id="stDuyet">—</div></div>
    </div>
    <div class="hv-stat">
        <div class="hv-stat-icon hv-stat-red"><?= IconHelper::svg('x-circle', '22') ?></div>
        <div><div class="hv-stat-label">Từ chối</div><div class="hv-stat-value" id="stTuChoi">—</div></div>
    </div>
</div>

<div class="card" style="background:#eff6ff;border:1px solid #bfdbfe;margin-bottom:14px">
    <div style="display:flex;gap:14px;align-items:center;flex-wrap:wrap;padding:14px 20px">
        <div style="flex:1;min-width:240px">
            <strong style="color:#1d4ed8">Liên kết công khai cho học viên:</strong>
        </div>
        <div style="display:flex;gap:8px;flex-wrap:wrap">
            <input type="text" class="form-control" readonly value="<?= htmlspecialchars($publicDangKyUrl) ?>" id="urlDK" style="min-width:300px;font-size:12px;font-family:monospace">
            <button class="btn btn-sm" onclick="copyUrl('urlDK')">Copy đăng ký</button>
            <input type="text" class="form-control" readonly value="<?= htmlspecialchars($publicTraCuuUrl) ?>" id="urlTC" style="min-width:300px;font-size:12px;font-family:monospace">
            <button class="btn btn-sm" onclick="copyUrl('urlTC')">Copy tra cứu</button>
        </div>
    </div>
</div>

<div class="card">
    <div class="lh-toolbar">
        <div class="lh-toolbar-left" style="flex:1">
            <input type="text" id="search" class="form-control" placeholder="Tìm tên, email, CCCD, mã tra cứu..." style="max-width:280px">
            <select id="fKhoa" class="form-select" style="max-width:240px">
                <option value="0">Tất cả khóa học</option>
                <?php foreach ($khoaHocList as $kh): ?>
                    <option value="<?= $kh['id'] ?>"><?= Helper::h($kh['ma_khoa_hoc'] . ' - ' . $kh['ten_khoa_hoc']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="lh-toolbar-right">
            <select id="fTrangThai" class="form-select" style="max-width:160px">
                <option value="">Tất cả trạng thái</option>
                <option value="0">Chờ duyệt</option>
                <option value="1">Đã duyệt</option>
                <option value="2">Từ chối</option>
            </select>
            <select id="fDX" class="form-select" style="max-width:140px">
                <option value="0">Đang dùng</option>
                <option value="1">Thùng rác</option>
            </select>
            <button type="button" class="btn" onclick="exportExcel()" title="Xuất Excel"><?= IconHelper::svg('download','16') ?> Xuất Excel</button>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table" id="dkTable">
            <thead>
                <tr>
                    <th style="width:46px">#</th>
                    <th>Mã tra cứu</th>
                    <th>Họ tên</th>
                    <th>Email / SĐT</th>
                    <th>CCCD</th>
                    <th>Khóa đăng ký</th>
                    <th>Ngày đăng ký</th>
                    <th>Trạng thái</th>
                    <th style="width:160px">Thao tác</th>
                </tr>
            </thead>
            <tbody id="dkBody"></tbody>
        </table>
    </div>
    <div class="empty-state" id="emptyState" style="display:none;padding:60px 20px">
        <?= IconHelper::svg('clipboard-list', '40') ?>
        Chưa có đơn đăng ký nào
    </div>
    <div class="pagination-wrap">
        <div id="pageInfo" class="text-muted">-</div>
        <div id="pageNav"></div>
    </div>
</div>

<!-- Drawer Detail -->
<div class="drawer-backdrop" id="drawerDetail">
    <div class="drawer">
        <div class="drawer-header">
            <div>
                <h3 id="dTitle" style="margin:0">Chi tiết đăng ký</h3>
                <div id="dSubtitle" class="text-muted" style="font-size:12.5px;margin-top:2px"></div>
            </div>
            <button type="button" class="close" onclick="closeDrawer()">&times;</button>
        </div>
        <div class="drawer-body" id="dBody"></div>
    </div>
</div>

<!-- Modal Approve -->
<div class="modal-backdrop" id="modalApprove">
    <div class="modal" style="max-width:520px">
        <div class="modal-header">
            <h3>Duyệt đăng ký</h3>
            <button type="button" class="close" onclick="closeApprove()">&times;</button>
        </div>
        <div class="modal-body" style="max-height:70vh;overflow-y:auto">
            <!-- Banner trùng (xuất hiện nếu có CCCD/SĐT trùng HV cũ) -->
            <div id="dupBanner" style="display:none;background:#fef3c7;border-left:3px solid #ca8a04;padding:12px 14px;border-radius:0 6px 6px 0;margin-bottom:14px">
                <strong style="color:#92400e">Phát hiện học viên có sẵn trùng thông tin:</strong>
                <div id="dupList" style="margin-top:8px;display:flex;flex-direction:column;gap:6px"></div>
            </div>

            <!-- Lựa chọn cách xử lý HV -->
            <div class="form-group">
                <label>Phương thức tạo học viên</label>
                <div class="approve-mode-list">
                    <label class="approve-mode">
                        <input type="radio" name="approve_mode" value="new" checked>
                        <div>
                            <div class="m-title">Tạo học viên mới</div>
                            <div class="m-sub">Mặc định. Tạo bản ghi mới trong DM_HOC_VIEN.</div>
                        </div>
                    </label>
                    <label class="approve-mode" id="modeLinkWrap" style="display:none">
                        <input type="radio" name="approve_mode" value="link">
                        <div style="flex:1">
                            <div class="m-title">Liên kết với HV có sẵn</div>
                            <div class="m-sub">Dùng khi xác định đăng ký này thuộc HV đã tồn tại (CCCD/SĐT trùng).</div>
                            <select id="aLinkHv" class="form-select" disabled style="margin-top:6px"></select>
                        </div>
                    </label>
                    <label class="approve-mode">
                        <input type="radio" name="approve_mode" value="nhanvien">
                        <div style="flex:1">
                            <div class="m-title">Tạo HV mới + Là nhân viên</div>
                            <div class="m-sub">Đánh dấu HV là nhân viên nội bộ, link sang DM_NHAN_VIEN.</div>
                            <select id="aNhanVien" class="form-select" disabled style="margin-top:6px">
                                <option value="">-- Đang tải nhân viên... --</option>
                            </select>
                        </div>
                    </label>
                </div>
            </div>

            <div class="form-group">
                <label>Ghi danh vào lớp (tùy chọn)</label>
                <select id="aLop" class="form-select">
                    <option value="">-- Chỉ duyệt, chưa ghi danh lớp --</option>
                </select>
                <div class="text-muted" style="font-size:12px;margin-top:4px">Có thể ghi danh sau trong module Lớp học.</div>
            </div>
            <div class="form-group">
                <label>Ghi chú</label>
                <textarea id="aNote" class="form-control" rows="2" maxlength="500" placeholder="Ghi chú (không bắt buộc)"></textarea>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn" onclick="closeApprove()">Hủy</button>
            <button type="button" class="btn btn-success" id="btnDoApprove" onclick="doApprove()">Xác nhận duyệt</button>
        </div>
    </div>
</div>

<style>
    .approve-mode-list { display:flex; flex-direction:column; gap:8px; }
    .approve-mode { display:flex; gap:10px; padding:10px 12px; border:1px solid var(--gray-200); border-radius:8px; cursor:pointer; align-items:flex-start; }
    .approve-mode:hover { border-color: var(--primary); }
    .approve-mode input[type="radio"] { margin-top:3px; }
    .approve-mode .m-title { font-weight:600; color: var(--gray-800); }
    .approve-mode .m-sub { font-size:12px; color: var(--gray-500); margin-top:2px; }
    .dup-item { padding:8px 10px; background:#fff; border:1px solid #fde68a; border-radius:6px; font-size:13px; }
    .dup-item strong { color:var(--gray-800); }
    .dup-item .by { font-size:11px; color:#92400e; font-weight:600; margin-left:6px; }
</style>

<!-- Modal Reject -->
<div class="modal-backdrop" id="modalReject">
    <div class="modal" style="max-width:480px">
        <div class="modal-header">
            <h3>Từ chối đăng ký</h3>
            <button type="button" class="close" onclick="closeReject()">&times;</button>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <label>Lý do từ chối <span class="required">*</span></label>
                <textarea id="rNote" class="form-control" rows="3" maxlength="500" required placeholder="Nhập lý do để gửi mail thông báo cho học viên"></textarea>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn" onclick="closeReject()">Hủy</button>
            <button type="button" class="btn btn-danger" id="btnDoReject" onclick="doReject()">Xác nhận từ chối</button>
        </div>
    </div>
</div>

<script>
var URL_AJAX = APP_BASE + 'GUI/DT_DangKyKhoaHoc/ajax_handler.php';
var URL_DL   = APP_BASE + 'GUI/DT_DangKyKhoaHoc/download.php';
var CAN_EDIT = <?= $canEdit ? 'true' : 'false' ?>;
var CAN_DEL  = <?= $canDel  ? 'true' : 'false' ?>;

var ICON_EDIT     = '<?= addslashes(IconHelper::svg('edit', '13')) ?>';
var ICON_TRASH    = '<?= addslashes(IconHelper::svg('trash', '13')) ?>';
var ICON_EYE      = '<?= addslashes(IconHelper::svg('eye', '13')) ?>';
var ICON_CHECK    = '<?= addslashes(IconHelper::svg('check-circle', '13')) ?>';
var ICON_X        = '<?= addslashes(IconHelper::svg('x-circle', '13')) ?>';
var ICON_DOWNLOAD = '<?= addslashes(IconHelper::svg('download', '13')) ?>';

var state = { page:1, pageSize:<?= AppConfig::DEFAULT_PAGE_SIZE ?>, daXoa:0,
              search:'', trangThai:'', khoaId:0, currentId:0, currentRecord:null };
function exportExcel(){ var p=new URLSearchParams({search:state.search||'',da_xoa:state.daXoa||0,khoa_hoc_id:state.khoaId||0,trang_thai:state.trangThai||''}); window.location=APP_BASE+'GUI/DT_DangKyKhoaHoc/export.php?'+p.toString(); }

var TT_LABEL = {0:'Chờ duyệt', 1:'Đã duyệt', 2:'Từ chối'};
var TT_BADGE = {0:'badge-warning', 1:'badge-success', 2:'badge-danger'};

function loadStats(){
    APP.ajax(URL_AJAX, {action:'getStats'}).done(function(res){
        if (!res.success) return;
        $('#stTotal').text(res.data.total || 0);
        $('#stCho').text(res.data.so_cho || 0);
        $('#stDuyet').text(res.data.so_duyet || 0);
        $('#stTuChoi').text(res.data.so_tu_choi || 0);
    });
}

function load(){
    APP.showLoading('#dkBody');
    APP.ajax(URL_AJAX, {
        action:'getPaged', page:state.page, pageSize:state.pageSize, da_xoa:state.daXoa,
        search:state.search, trang_thai:state.trangThai, khoa_hoc_id:state.khoaId
    }).done(function(res){
        APP.hideLoading('#dkBody');
        if (!res.success){ APP.toast(res.message,'error'); return; }
        renderTable(res.data);
        renderPager(res.pagination);
    });
}

function renderTable(rows){
    var $body = $('#dkBody').empty();
    if (!rows.length){ $('#emptyState').show(); return; }
    $('#emptyState').hide();
    var offset = (state.page - 1) * state.pageSize;
    rows.forEach(function(r, i){
        var tt = parseInt(r.trang_thai, 10);
        var ttBadge = '<span class="badge '+(TT_BADGE[tt]||'badge-secondary')+'">'+(TT_LABEL[tt]||tt)+'</span>';

        var actions = '<button class="btn btn-sm icon-only" title="Chi tiết" onclick="openDetail('+r.id+')">'+ICON_EYE+'</button>';
        if (state.daXoa == 0 && CAN_EDIT && tt === 0){
            actions += ' <button class="btn btn-sm btn-success" title="Duyệt" onclick="openApprove('+r.id+')">'+ICON_CHECK+' Duyệt</button>';
            actions += ' <button class="btn btn-sm btn-danger icon-only" title="Từ chối" onclick="openReject('+r.id+')">'+ICON_X+'</button>';
        }
        if (state.daXoa == 0 && CAN_DEL){
            actions += ' <button class="btn btn-sm icon-only" title="Xóa" onclick="trashItem('+r.id+')">'+ICON_TRASH+'</button>';
        }

        $body.append('<tr>'
            + '<td>'+(offset+i+1)+'</td>'
            + '<td><code style="font-size:11px">'+APP.escape(r.ma_tra_cuu||'')+'</code></td>'
            + '<td><strong>'+APP.escape(r.ho_ten||'')+'</strong>'
                + (r.don_vi_cong_tac ? '<br><span class="text-muted" style="font-size:11.5px">'+APP.escape(r.don_vi_cong_tac)+'</span>' : '')
                + '</td>'
            + '<td><div>'+APP.escape(r.email||'')+'</div>'
                + (r.dien_thoai ? '<div class="text-muted" style="font-size:12px">'+APP.escape(r.dien_thoai)+'</div>' : '')
                + '</td>'
            + '<td style="font-family:monospace;font-size:12px">'+APP.escape(r.cccd||'')+'</td>'
            + '<td>'+APP.escape(r.ten_khoa_hoc||'-')
                + (r.ten_lop ? '<br><span class="text-muted" style="font-size:11.5px">→ '+APP.escape(r.ma_lop+' - '+r.ten_lop)+'</span>' : '')
                + '</td>'
            + '<td class="text-muted" style="font-size:12.5px">'+APP.escape(r.ngay_tao||'-')+'</td>'
            + '<td>'+ttBadge+'</td>'
            + '<td><div class="actions">'+actions+'</div></td>'
            + '</tr>');
    });
}

function renderPager(p){
    var from = (p.currentPage-1)*p.pageSize + 1;
    var to = Math.min(from+p.pageSize-1, p.totalRecords);
    $('#pageInfo').text(p.totalRecords ? 'Hiển thị '+from+'-'+to+' / '+p.totalRecords : 'Không có bản ghi');
    $('#pageNav').html(APP.renderPagination(p));
}

$('#pageNav').on('click','button[data-p]',function(){ var p=parseInt($(this).data('p'),10); if(!p||p===state.page) return; state.page=p; load(); });
$('#search').on('input', APP.debounce(function(){ state.search=$(this).val(); state.page=1; load(); }, 350));
$('#fKhoa').on('change', function(){ state.khoaId=parseInt(this.value,10)||0; state.page=1; load(); });
$('#fTrangThai').on('change', function(){ state.trangThai=this.value; state.page=1; load(); });
$('#fDX').on('change', function(){ state.daXoa=parseInt(this.value,10)||0; state.page=1; load(); });

// ============ Drawer Detail ============
function openDetail(id){
    state.currentId = id;
    $('#drawerDetail').addClass('open').find('.drawer').addClass('open');
    $('#dTitle').text('Đang tải...'); $('#dSubtitle').text('');
    $('#dBody').html('<div style="padding:30px;text-align:center;color:var(--gray-500)">Đang tải...</div>');
    APP.ajax(URL_AJAX, {action:'getById', id:id}).done(function(res){
        if (!res.success){ $('#dBody').html('<div style="padding:20px;color:#b91c1c">'+APP.escape(res.message||'')+'</div>'); return; }
        state.currentRecord = res.data;
        renderDetail(res.data);
    });
}
function closeDrawer(){ $('#drawerDetail').removeClass('open').find('.drawer').removeClass('open'); }

function renderDetail(r){
    var tt = parseInt(r.trang_thai, 10);
    $('#dTitle').text(r.ho_ten || '-');
    $('#dSubtitle').text((r.ma_tra_cuu||'') + ' · ' + (r.ten_khoa_hoc||''));

    var html = '';

    // Banner trạng thái
    if (tt === 0){
        html += '<div class="alert" style="background:#fef3c7;color:#92400e;padding:10px 14px;border-radius:6px;margin-bottom:14px">Đơn này đang <strong>chờ duyệt</strong>.</div>';
    } else if (tt === 1){
        html += '<div class="alert" style="background:#dcfce7;color:#166534;padding:10px 14px;border-radius:6px;margin-bottom:14px">Đã duyệt'
              + (r.tai_khoan_nguoi_xu_ly ? ' bởi <strong>'+APP.escape(r.tai_khoan_nguoi_xu_ly)+'</strong>' : '')
              + (r.ngay_xu_ly ? ' lúc '+APP.escape(r.ngay_xu_ly) : '') + '.</div>';
    } else if (tt === 2){
        html += '<div class="alert" style="background:#fee2e2;color:#991b1b;padding:10px 14px;border-radius:6px;margin-bottom:14px">'
              + 'Đã từ chối' + (r.tai_khoan_nguoi_xu_ly ? ' bởi <strong>'+APP.escape(r.tai_khoan_nguoi_xu_ly)+'</strong>' : '')
              + (r.ly_do_xu_ly ? '<br>Lý do: '+APP.escape(r.ly_do_xu_ly) : '') + '</div>';
    }

    html += '<div class="lh-detail-grid">';
    html += dRow('Mã tra cứu', '<code>'+APP.escape(r.ma_tra_cuu||'-')+'</code>');
    html += dRow('Trạng thái', '<span class="badge '+(TT_BADGE[tt]||'badge-secondary')+'">'+(TT_LABEL[tt]||tt)+'</span>');
    html += dRow('Họ tên', APP.escape(r.ho_ten||'-'));
    html += dRow('Ngày sinh', APP.escape(r.ngay_sinh||'-'));
    html += dRow('Giới tính', APP.escape(r.gioi_tinh||'-'));
    html += dRow('CCCD', APP.escape(r.cccd||'-'));
    html += dRow('Email', APP.escape(r.email||'-'));
    html += dRow('Điện thoại', APP.escape(r.dien_thoai||'-'));
    html += dRow('Đơn vị công tác', APP.escape(r.don_vi_cong_tac||'-'));
    html += dRow('Chức vụ', APP.escape(r.chuc_vu||'-'));
    html += dRow('Địa chỉ', APP.escape(r.dia_chi||'-'));
    html += dRow('Khóa học', APP.escape(r.ten_khoa_hoc||'-'));
    if (r.ten_lop) html += dRow('Lớp ghi danh', APP.escape(r.ma_lop + ' - ' + r.ten_lop));
    if (r.hoc_vien_id) html += dRow('Học viên ID', '#'+r.hoc_vien_id + (r.ma_hv ? ' (' + APP.escape(r.ma_hv) + ')' : ''));
    html += dRow('Ngày đăng ký', APP.escape(r.ngay_tao||'-'));
    if (r.ip_dang_ky) html += dRow('IP đăng ký', APP.escape(r.ip_dang_ky));
    html += '</div>';

    if (r.ly_do_dang_ky){
        html += '<div class="lh-detail-block"><div class="lh-detail-label">Lý do đăng ký</div><div>'+APP.escape(r.ly_do_dang_ky)+'</div></div>';
    }

    // File đính kèm
    if (r.cccd_file || r.bang_cap_file){
        html += '<div class="lh-detail-block"><div class="lh-detail-label">File đính kèm</div><div style="display:flex;gap:8px;flex-wrap:wrap">';
        if (r.cccd_file){
            html += '<a class="btn btn-sm" target="_blank" href="'+URL_DL+'?id='+r.id+'&kind=cccd&inline=1">'+ICON_EYE+' Xem CCCD</a>';
            html += '<a class="btn btn-sm btn-primary" href="'+URL_DL+'?id='+r.id+'&kind=cccd">'+ICON_DOWNLOAD+' Tải CCCD</a>';
        }
        if (r.bang_cap_file){
            html += '<a class="btn btn-sm" target="_blank" href="'+URL_DL+'?id='+r.id+'&kind=bc&inline=1">'+ICON_EYE+' Xem bằng cấp</a>';
            html += '<a class="btn btn-sm btn-primary" href="'+URL_DL+'?id='+r.id+'&kind=bc">'+ICON_DOWNLOAD+' Tải bằng cấp</a>';
        }
        html += '</div></div>';
    }

    // Action
    if (tt === 0 && CAN_EDIT){
        html += '<div class="lh-detail-actions">';
        html += '<button class="btn btn-success" onclick="closeDrawer();openApprove('+r.id+')">'+ICON_CHECK+' Duyệt đơn</button>';
        html += '<button class="btn btn-danger" onclick="closeDrawer();openReject('+r.id+')">'+ICON_X+' Từ chối</button>';
        html += '</div>';
    }

    $('#dBody').html(html);
}
function dRow(label,val){ return '<div class="lh-detail-row"><div class="lh-detail-label">'+label+'</div><div class="lh-detail-val">'+val+'</div></div>'; }

// ============ Approve ============
var nvComboLoaded = false;

function openApprove(id){
    state.currentId = id;
    $('#aNote').val('');
    $('#aLop').html('<option value="">-- Đang tải lớp... --</option>');
    $('#dupBanner').hide();
    $('#dupList').empty();
    $('#modeLinkWrap').hide();
    $('#aLinkHv').empty().prop('disabled', true);
    $('input[name="approve_mode"][value="new"]').prop('checked', true);
    syncApproveMode();
    $('#modalApprove').addClass('open');

    // Load lớp theo khóa của đơn
    APP.ajax(URL_AJAX, {action:'getById', id:id}).done(function(res){
        if (!res.success || !res.data) return;
        var dk = res.data;
        if (dk.khoa_hoc_id) {
            APP.ajax(URL_AJAX, {action:'getLopByKhoa', khoa_hoc_id: dk.khoa_hoc_id}).done(function(r2){
                var html = '<option value="">-- Chỉ duyệt, chưa ghi danh lớp --</option>';
                if (r2.success && r2.data && r2.data.length){
                    r2.data.forEach(function(l){
                        html += '<option value="'+l.id+'">'+APP.escape(l.ma_chuong_trinh+' - '+l.ten_chuong_trinh)+'</option>';
                    });
                } else {
                    html += '<option value="" disabled>(Khóa này chưa có chương trình đào tạo nào)</option>';
                }
                $('#aLop').html(html);
            });
        } else {
            $('#aLop').html('<option value="">-- Chỉ duyệt, chưa ghi danh lớp --</option>');
        }
    });

    // Scan trùng CCCD/SDT
    APP.ajax(URL_AJAX, {action:'scanDuplicates', id:id}).done(function(res){
        if (!res.success || !res.data || !res.data.matches || !res.data.matches.length) return;
        var matches = res.data.matches;
        var $list = $('#dupList').empty();
        var $sel = $('#aLinkHv').empty().append('<option value="">-- Chọn HV để liên kết --</option>');
        matches.forEach(function(m){
            var hv = m.hv;
            var byTags = m.matched_by.map(function(b){ return b === 'cccd' ? 'CCCD' : 'SĐT'; }).join(' + ');
            $list.append('<div class="dup-item"><strong>'+APP.escape(hv.ma_hv+' - '+hv.ho_ten)+'</strong>'
                + '<span class="by">trùng '+byTags+'</span>'
                + (hv.don_vi_cong_tac ? '<div class="text-muted" style="font-size:11.5px;margin-top:2px">'+APP.escape(hv.don_vi_cong_tac)+'</div>' : '')
                + '</div>');
            $sel.append('<option value="'+hv.id+'">'+APP.escape(hv.ma_hv+' - '+hv.ho_ten)+' (trùng '+byTags+')</option>');
        });
        $('#dupBanner').show();
        $('#modeLinkWrap').show();
    });

    // Load NV combo (lazy)
    if (!nvComboLoaded) {
        APP.ajax(URL_AJAX, {action:'getNhanVienCombo'}).done(function(res){
            if (!res.success) return;
            var $s = $('#aNhanVien').empty().append('<option value="">-- Chọn nhân viên --</option>');
            (res.data || []).forEach(function(n){
                $s.append('<option value="'+n.id+'">'+APP.escape((n.ma_nv||'') + ' - ' + (n.ho_ten||''))+'</option>');
            });
            nvComboLoaded = true;
        });
    }
}

function syncApproveMode() {
    var mode = $('input[name="approve_mode"]:checked').val();
    $('#aLinkHv').prop('disabled', mode !== 'link');
    $('#aNhanVien').prop('disabled', mode !== 'nhanvien');
}
$(document).on('change', 'input[name="approve_mode"]', syncApproveMode);

function closeApprove(){ $('#modalApprove').removeClass('open'); }

function doApprove(){
    var mode = $('input[name="approve_mode"]:checked').val();
    var payload = {
        action: 'approve',
        id: state.currentId,
        lop_hoc_id: $('#aLop').val() || 0,
        ghi_chu: $('#aNote').val(),
        existing_hv_id: 0,
        la_nhan_vien: 0,
        nhan_vien_id: 0
    };
    if (mode === 'link') {
        var hvId = parseInt($('#aLinkHv').val(), 10) || 0;
        if (!hvId) { APP.toast('Chọn học viên để liên kết', 'error'); return; }
        payload.existing_hv_id = hvId;
    } else if (mode === 'nhanvien') {
        var nvId = parseInt($('#aNhanVien').val(), 10) || 0;
        if (!nvId) { APP.toast('Chọn nhân viên', 'error'); return; }
        payload.la_nhan_vien = 1;
        payload.nhan_vien_id = nvId;
    }
    var $btn = $('#btnDoApprove').prop('disabled', true).text('Đang xử lý...');
    APP.ajax(URL_AJAX, payload).done(function(res){
        $btn.prop('disabled', false).text('Xác nhận duyệt');
        if (res.success){
            APP.toast(res.message, 'success');
            closeApprove(); load(); loadStats();
        } else APP.toast(res.message, 'error');
    });
}

// ============ Reject ============
function openReject(id){
    state.currentId = id;
    $('#rNote').val('');
    $('#modalReject').addClass('open');
}
function closeReject(){ $('#modalReject').removeClass('open'); }
function doReject(){
    var note = $('#rNote').val().trim();
    if (!note){ APP.toast('Vui lòng nhập lý do từ chối', 'error'); return; }
    var $btn = $('#btnDoReject').prop('disabled', true).text('Đang xử lý...');
    APP.ajax(URL_AJAX, {action:'reject', id: state.currentId, ly_do: note})
        .done(function(res){
            $btn.prop('disabled', false).text('Xác nhận từ chối');
            if (res.success){
                APP.toast(res.message, 'success');
                closeReject(); load(); loadStats();
            } else APP.toast(res.message, 'error');
        });
}

function trashItem(id){
    APP.confirm('Chuyển đơn này vào thùng rác?', function(){
        APP.ajax(URL_AJAX, {action:'trash', id:id}).done(function(res){
            res.success ? (APP.toast(res.message,'success'), load(), loadStats()) : APP.toast(res.message,'error');
        });
    });
}

function copyUrl(elId){
    var inp = document.getElementById(elId);
    inp.select(); inp.setSelectionRange(0, 99999);
    if (navigator.clipboard) navigator.clipboard.writeText(inp.value).then(function(){ APP.toast('Đã sao chép','success'); });
    else { document.execCommand('copy'); APP.toast('Đã sao chép','success'); }
}

// Init
load(); loadStats();
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
