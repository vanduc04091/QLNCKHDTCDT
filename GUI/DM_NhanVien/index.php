<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../BUS/DM_KhoaPhong_BUS.php';

Helper::requireLogin();
if (!PhanQuyenHelper::hasQuyen('DM_NhanVien', PhanQuyenHelper::QUYEN_XEM)) {
    echo 'Bạn không có quyền truy cập chức năng này.'; exit;
}

$canAdd = PhanQuyenHelper::hasQuyen('DM_NhanVien', PhanQuyenHelper::QUYEN_THEM);
$canEdit = PhanQuyenHelper::hasQuyen('DM_NhanVien', PhanQuyenHelper::QUYEN_SUA);
$canDel = PhanQuyenHelper::hasQuyen('DM_NhanVien', PhanQuyenHelper::QUYEN_XOA);

$khoaCombo = DM_KhoaPhong_BUS::getCombo();

$pageTitle = 'Quản lý nhân viên';
$activeMenu = 'DM_NhanVien';
require __DIR__ . '/../layouts/header.php';
?>

<div class="breadcrumb">
    <a href="<?= AppConfig::baseUrl('GUI/dashboard/index.php') ?>">Trang chủ</a>
    <span class="sep">›</span> Danh mục
    <span class="sep">›</span> <span>Nhân viên</span>
</div>

<div class="card">
    <div class="toolbar">
        <div class="left">
            <input type="text" id="search" class="form-control" placeholder="Tìm mã NV, họ tên, SĐT..." style="max-width:320px">
            <select id="filterKhoa" class="form-select" style="max-width:220px">
                <option value="0">-- Tất cả khoa/phòng --</option>
                <?php foreach ($khoaCombo as $k): ?>
                    <option value="<?= $k['id'] ?>"><?= Helper::h($k['ten_khoa']) ?></option>
                <?php endforeach; ?>
            </select>
            <select id="filterDaXoa" class="form-select" style="max-width:160px">
                <option value="0">Đang hoạt động</option>
                <option value="1">Thùng rác</option>
            </select>
        </div>
        <div class="right">
            <button type="button" class="btn" onclick="exportExcel()" title="Xuất Excel"><?= IconHelper::svg('download','16') ?> Xuất Excel</button>
            <?php if ($canAdd): ?>
                <button type="button" class="btn" onclick="openImport()" title="Import người hành nghề từ Excel"><?= IconHelper::svg('upload','16') ?> Import Excel</button>
                <button type="button" class="btn btn-primary" onclick="openCreate()">+ Thêm nhân viên</button>
            <?php endif; ?>
        </div>
    </div>

    <?php if ($canAdd): ?>
    <!-- Modal Import Excel -->
    <div class="modal-backdrop" id="modalImport">
        <div class="modal" style="max-width:680px">
            <div class="modal-header"><h3>Import người hành nghề từ Excel</h3>
                <button type="button" class="close" onclick="$('#modalImport').removeClass('open')">&times;</button></div>
            <div class="modal-body">
                <div class="imp-note">
                    <div>File theo mẫu <strong>Danh sách người hành nghề toàn viện</strong> (.xlsx) — 13 cột: MNV, Họ tên, K/P/TT, Văn bằng, Ngày sinh, Phạm vi hành nghề, Số CCHN, Ngày cấp CCHN, QĐ bổ sung, Điều chỉnh phạm vi, Ngày điều chỉnh, Chuyên khoa cập nhật.</div>
                    <ul>
                        <li>Khoa/phòng được <strong>tự khớp theo tên</strong>; khớp không được thì vẫn thêm NV và <span class="imp-hl">giữ nguyên tên khoa gốc</span> để gán tay sau.</li>
                        <li>Mã NV <strong>trùng</strong> (trong file hoặc đã có trong DB) sẽ bị <strong>bỏ qua</strong>.</li>
                    </ul>
                </div>
                <div class="form-group" style="margin-top:12px">
                    <label>Chọn file Excel (.xlsx)</label>
                    <input type="file" id="impFile" class="form-control" accept=".xlsx">
                </div>
                <div id="impResult" style="display:none"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn" onclick="$('#modalImport').removeClass('open')">Đóng</button>
                <button type="button" class="btn btn-primary" id="btnImport"><?= IconHelper::svg('upload','16') ?> Bắt đầu import</button>
            </div>
        </div>
    </div>
    <style>
        .imp-note { background:#f8fafc; border:1px solid var(--gray-200); border-radius:8px; padding:12px 14px; font-size:12.5px; color:var(--gray-700); }
        .imp-note ul { margin:8px 0 0; padding-left:18px; } .imp-note li { margin:3px 0; }
        .imp-hl { color:#92400e; font-weight:600; }
        .imp-sum { display:flex; gap:10px; margin:12px 0 8px; flex-wrap:wrap; }
        .imp-chip { padding:6px 12px; border-radius:8px; font-weight:600; font-size:12.5px; }
        .imp-chip.ok { background:#dcfce7; color:#166534; } .imp-chip.warn { background:#fef3c7; color:#92400e; }
        .imp-chip.skip { background:#e2e8f0; color:#475569; } .imp-chip.err { background:#fee2e2; color:#991b1b; }
        .imp-tbl { max-height:280px; overflow:auto; border:1px solid var(--gray-200); border-radius:8px; margin-top:6px; }
        .imp-tbl table { width:100%; border-collapse:collapse; font-size:12px; }
        .imp-tbl th, .imp-tbl td { padding:5px 8px; border-bottom:1px solid var(--gray-100); text-align:left; }
        .imp-tbl tr.ok_note td { background:#fffbeb; } .imp-tbl tr.bo_qua td { background:#f8fafc; color:#64748b; }
        .imp-tbl tr.loi td { background:#fef2f2; color:#991b1b; }
    </style>
    <?php endif; ?>
    <style>
        .nv-section-title { font-size:13px; font-weight:700; color:var(--primary,#16a34a); margin:18px 0 10px;
            padding-top:12px; border-top:1px solid var(--gray-200); }
        .nv-name-link { color:var(--primary,#16a34a); cursor:pointer; font-weight:600; }
        .nv-name-link:hover { text-decoration:underline; }
        .nv-khoa-raw { color:#92400e; background:#fef3c7; padding:1px 7px; border-radius:5px; font-size:12px; }
        /* Drawer xem thông tin */
        .nvv-hero { display:flex; gap:14px; align-items:center; padding:16px; border-radius:12px;
            background:linear-gradient(135deg,#16a34a,#0f766e); color:#fff; margin-bottom:16px; }
        .nvv-ava { width:52px; height:52px; border-radius:50%; background:rgba(255,255,255,.2);
            border:1px solid rgba(255,255,255,.5); display:grid; place-items:center; font-weight:800; font-size:17px; flex:0 0 auto; }
        .nvv-hero .nm { font-size:18px; font-weight:800; line-height:1.2; }
        .nvv-hero .sub { font-size:12.5px; opacity:.92; margin-top:3px; }
        .nvv-sec { font-size:12px; font-weight:700; color:var(--gray-500); text-transform:uppercase;
            letter-spacing:.04em; margin:18px 0 8px; }
        .nvv-grid { display:grid; grid-template-columns:repeat(2,1fr); gap:1px; background:var(--gray-200);
            border:1px solid var(--gray-200); border-radius:8px; overflow:hidden; }
        .nvv-item { background:#fff; padding:9px 12px; display:flex; flex-direction:column; gap:2px; }
        .nvv-item.full { grid-column:1/-1; }
        .nvv-lbl { font-size:11px; color:var(--gray-500); text-transform:uppercase; letter-spacing:.02em; }
        .nvv-val { font-size:13px; color:var(--gray-800); font-weight:500; word-break:break-word; }
        @media (max-width:560px){ .nvv-grid { grid-template-columns:1fr; } }
    </style>

    <!-- Drawer: Xem thông tin nhân viên -->
    <div class="drawer-backdrop" id="drawerView">
        <div class="drawer" style="max-width:680px">
            <div class="drawer-header">
                <div><h3 style="margin:0">Thông tin nhân viên</h3>
                    <div id="nvvSub" class="text-muted" style="font-size:12.5px;margin-top:2px"></div></div>
                <button type="button" class="close" onclick="$('#drawerView').removeClass('open').find('.drawer').removeClass('open')">&times;</button>
            </div>
            <div class="drawer-body" id="nvvBody"><div class="hv-pane-loading">Đang tải...</div></div>
        </div>
    </div>
    <div class="table-wrap" id="tableWrap" style="position:relative;min-height:200px">
        <table class="table">
            <thead>
                <tr>
                    <th style="width:50px" class="text-center">#</th>
                    <th style="width:110px">Mã NV</th>
                    <th>Họ tên</th>
                    <th>Chức danh</th>
                    <th>Khoa/Phòng</th>
                    <th>Điện thoại</th>
                    <th>Email</th>
                    <th class="text-center" style="width:110px">Trạng thái</th>
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

<div class="modal-backdrop" id="modalForm">
    <div class="modal" style="max-width:820px">
        <div class="modal-header">
            <h3 id="modalTitle">Thêm nhân viên</h3>
            <button type="button" class="close" onclick="closeModal()">&times;</button>
        </div>
        <form id="formNV">
            <div class="modal-body">
                <input type="hidden" name="id" id="f_id">
                <input type="hidden" name="benh_vien_id" value="1">
                <div class="form-row">
                    <div class="form-group">
                        <label>Mã NV <span class="required">*</span></label>
                        <input type="text" name="ma_nv" id="f_ma_nv" class="form-control" required maxlength="50">
                    </div>
                    <div class="form-group">
                        <label>Họ tên <span class="required">*</span></label>
                        <input type="text" name="ho_ten" id="f_ho_ten" class="form-control" required maxlength="200">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Ngày sinh</label>
                        <input type="date" name="ngay_sinh" id="f_ngay_sinh" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Giới tính</label>
                        <select name="gioi_tinh" id="f_gioi_tinh" class="form-select">
                            <option value="">--</option>
                            <option value="Nam">Nam</option>
                            <option value="Nữ">Nữ</option>
                            <option value="Khác">Khác</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Trạng thái</label>
                        <select name="trang_thai" id="f_trang_thai" class="form-select">
                            <option value="1">Đang làm</option>
                            <option value="0">Nghỉ việc</option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Khoa/Phòng</label>
                        <select name="khoa_phong_id" id="f_khoa_phong_id" class="form-select">
                            <option value="">-- Chọn --</option>
                            <?php foreach ($khoaCombo as $k): ?>
                                <option value="<?= $k['id'] ?>"><?= Helper::h($k['ten_khoa']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Chức danh</label>
                        <input type="text" name="chuc_danh" id="f_chuc_danh" class="form-control" maxlength="100">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Trình độ</label>
                        <input type="text" name="trinh_do" id="f_trinh_do" class="form-control" maxlength="100">
                    </div>
                    <div class="form-group">
                        <label>Chuyên khoa</label>
                        <input type="text" name="chuyen_khoa" id="f_chuyen_khoa" class="form-control" maxlength="200">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Điện thoại</label>
                        <input type="text" name="dien_thoai" id="f_dien_thoai" class="form-control" maxlength="20">
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" id="f_email" class="form-control" maxlength="100">
                    </div>
                </div>
                <div class="form-group">
                    <label>Địa chỉ</label>
                    <input type="text" name="dia_chi" id="f_dia_chi" class="form-control" maxlength="250">
                </div>

                <div class="nv-section-title">Chứng chỉ hành nghề (CCHN)</div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Số CCHN</label>
                        <input type="text" name="so_cchn" id="f_so_cchn" class="form-control" maxlength="50">
                    </div>
                    <div class="form-group">
                        <label>Ngày cấp CCHN</label>
                        <input type="date" name="ngay_cap_cchn" id="f_ngay_cap_cchn" class="form-control">
                    </div>
                </div>
                <div class="form-group">
                    <label>Phạm vi hành nghề</label>
                    <input type="text" name="pham_vi_hanh_nghe" id="f_pham_vi_hanh_nghe" class="form-control" maxlength="300">
                </div>
                <div class="form-group">
                    <label>Quyết định bổ sung phạm vi</label>
                    <input type="text" name="qd_bo_sung_pham_vi" id="f_qd_bo_sung_pham_vi" class="form-control" maxlength="300">
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Điều chỉnh phạm vi HĐCM trong CCHN</label>
                        <input type="text" name="dieu_chinh_pham_vi" id="f_dieu_chinh_pham_vi" class="form-control" maxlength="300">
                    </div>
                    <div class="form-group">
                        <label>Ngày điều chỉnh phạm vi</label>
                        <input type="date" name="ngay_dieu_chinh" id="f_ngay_dieu_chinh" class="form-control">
                    </div>
                </div>
                <div class="form-group">
                    <label>Chuyên khoa cần cập nhật KTYK liên tục</label>
                    <input type="text" name="chuyen_khoa_cap_nhat" id="f_chuyen_khoa_cap_nhat" class="form-control" maxlength="300">
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
var URL = APP_BASE + 'GUI/DM_NhanVien/ajax_handler.php';
var CAN_EDIT = <?= $canEdit?'true':'false' ?>;
var CAN_DEL = <?= $canDel?'true':'false' ?>;
var state = { page: 1, pageSize: 20, search: '', daXoa: 0, khoaId: 0 };
function exportExcel(){ var p=new URLSearchParams({search:state.search||'',da_xoa:state.daXoa||0,khoa_phong_id:state.khoaId||0}); window.location=APP_BASE+'GUI/DM_NhanVien/export.php?'+p.toString(); }

var ICON_EDIT = '<?= addslashes(IconHelper::svg('edit', '18')) ?>';
var ICON_TRASH = '<?= addslashes(IconHelper::svg('trash', '18')) ?>';
var ICON_VIEW = '<?= addslashes(IconHelper::svg('eye', '18')) ?>';
var ICON_EMPTY = '<?= addslashes(IconHelper::svg('dashboard', '40')) ?>';
var ICON_UPLOAD = '<?= addslashes(IconHelper::svg('upload', '16')) ?>';

// ===== Import Excel =====
function openImport(){
    $('#impFile').val(''); $('#impResult').hide().empty();
    $('#btnImport').prop('disabled',false).html(ICON_UPLOAD+' Bắt đầu import');
    $('#modalImport').addClass('open');
}
$(document).on('click','#btnImport',function(){
    var f=$('#impFile')[0].files[0];
    if(!f){ APP.toast('Chưa chọn file','error'); return; }
    if(!/\.xlsx$/i.test(f.name)){ APP.toast('Chỉ hỗ trợ file .xlsx','error'); return; }
    var fd=new FormData(); fd.append('action','import'); fd.append('file',f);
    $('#btnImport').prop('disabled',true).text('Đang import...');
    $('#impResult').hide().empty();
    $.ajax({ url:URL, type:'POST', data:fd, processData:false, contentType:false, dataType:'json',
        headers: window.CSRF_TOKEN ? {'X-CSRF-Token':window.CSRF_TOKEN} : {} })
    .done(function(res){
        $('#btnImport').prop('disabled',false).html(ICON_UPLOAD+' Bắt đầu import');
        if(!res.success){ APP.toast(res.message||'Import lỗi','error'); return; }
        var s=res.data.summary, rows=res.data.rows||[];
        var h='<div class="imp-sum">'+
            '<span class="imp-chip ok">Đã thêm: '+s.them+'</span>'+
            (s.khong_map_khoa?'<span class="imp-chip warn">Chưa map khoa: '+s.khong_map_khoa+'</span>':'')+
            (s.bo_qua?'<span class="imp-chip skip">Bỏ qua: '+s.bo_qua+'</span>':'')+
            (s.loi?'<span class="imp-chip err">Lỗi: '+s.loi+'</span>':'')+'</div>';
        // chỉ hiện các dòng cần chú ý (không phải ok thuần) để gọn
        var noti=rows.filter(function(r){ return r.trang_thai!=='ok'; });
        if(noti.length){
            h+='<div class="imp-tbl"><table><thead><tr><th>STT</th><th>Mã</th><th>Họ tên</th><th>Ghi chú</th></tr></thead><tbody>';
            noti.slice(0,300).forEach(function(r){
                h+='<tr class="'+r.trang_thai+'"><td>'+APP.escape(String(r.stt||''))+'</td><td>'+APP.escape(r.ma||'')+'</td><td>'+APP.escape(r.ten||'')+'</td><td>'+APP.escape(r.ghi_chu||'')+'</td></tr>';
            });
            h+='</tbody></table></div>';
            if(noti.length>300) h+='<div class="text-muted" style="font-size:11.5px;margin-top:6px">… và '+(noti.length-300)+' dòng khác</div>';
        } else {
            h+='<div class="text-muted" style="font-size:12.5px">Tất cả bản ghi đã thêm thành công.</div>';
        }
        $('#impResult').html(h).show();
        APP.toast(res.message,'success');
        load(); if(typeof loadStats==='function') loadStats();
    })
    .fail(function(){ $('#btnImport').prop('disabled',false).html(ICON_UPLOAD+' Bắt đầu import'); APP.toast('Lỗi máy chủ khi import','error'); });
});

function load() {
    APP.showLoading('#tableWrap');
    APP.ajax(URL, {
        action: 'getPaged',
        page: state.page, pageSize: state.pageSize,
        search: state.search, da_xoa: state.daXoa,
        khoa_phong_id: state.khoaId
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
        $tb.append('<tr><td colspan="9"><div class="empty-state"><div class="icon">' + ICON_EMPTY + '</div>Không có dữ liệu</div></td></tr>');
        return;
    }
    var stt = (state.page - 1) * state.pageSize;
    rows.forEach(function (r) {
        stt++;
        var tt = r.trang_thai == 1
            ? '<span class="badge badge-success">Đang làm</span>'
            : '<span class="badge badge-danger">Nghỉ việc</span>';
        var actions = '';
        if (state.daXoa == 0) {
            actions += '<button class="btn btn-sm" title="Xem thông tin" onclick="openView(' + r.id + ')">' + ICON_VIEW + '</button>';
            if (CAN_EDIT) actions += '<button class="btn btn-sm" title="Sửa" onclick="openEdit(' + r.id + ')">' + ICON_EDIT + '</button>';
            if (CAN_DEL) actions += '<button class="btn btn-sm btn-danger" title="Xóa" onclick="trashItem(' + r.id + ')">' + ICON_TRASH + '</button>';
        } else {
            if (CAN_EDIT) actions += '<button class="btn btn-sm btn-success" onclick="restoreItem(' + r.id + ')">↺ Khôi phục</button>';
            if (CAN_DEL) actions += '<button class="btn btn-sm btn-danger" onclick="deleteItem(' + r.id + ')">Xóa</button>';
        }
        $tb.append(
            '<tr>' +
                '<td class="text-center">' + stt + '</td>' +
                '<td><strong>' + APP.escape(r.ma_nv) + '</strong></td>' +
                '<td><span class="nv-name-link" onclick="openView(' + r.id + ')" title="Xem thông tin">' + APP.escape(r.ho_ten) + '</span></td>' +
                '<td>' + APP.escape(r.chuc_danh || '-') + '</td>' +
                '<td>' + (r.ten_khoa_phong ? APP.escape(r.ten_khoa_phong)
                        : (r.khoa_phong_text ? '<span class="nv-khoa-raw" title="Chưa gán khoa trong danh mục">' + APP.escape(r.khoa_phong_text) + '</span>' : '-')) + '</td>' +
                '<td>' + APP.escape(r.dien_thoai || '-') + '</td>' +
                '<td>' + APP.escape(r.email || '-') + '</td>' +
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

$('#filterKhoa').on('change', function () { state.khoaId = parseInt(this.value, 10) || 0; state.page = 1; load(); });
$('#filterDaXoa').on('change', function () { state.daXoa = parseInt(this.value, 10) || 0; state.page = 1; load(); });

function openCreate() {
    $('#modalTitle').text('Thêm nhân viên');
    $('#formNV')[0].reset();
    $('#f_id').val('');
    $('#modalForm').addClass('open');
}
// ===== Xem thông tin đầy đủ =====
function initials(name){
    var p=(name||'').trim().split(/\s+/);
    if(p.length===1) return (p[0]||'NV').substr(0,2).toUpperCase();
    return (p[p.length-2].charAt(0)+p[p.length-1].charAt(0)).toUpperCase();
}
function openView(id) {
    $('#nvvBody').html('<div class="hv-pane-loading">Đang tải...</div>');
    $('#nvvSub').text('');
    $('#drawerView').addClass('open').find('.drawer').addClass('open');
    APP.ajax(URL, {action: 'getById', id: id}).done(function (res) {
        if (!res.success) { $('#nvvBody').html('<div class="empty-state">' + APP.escape(res.message) + '</div>'); return; }
        var e = res.data;
        var gtMap = {M:'Nam', F:'Nữ'};
        var khoa = e.ten_khoa_phong || e.khoa_phong_text || '';
        $('#nvvSub').text((e.ma_nv||'') + (khoa ? ' · ' + khoa : ''));

        function row(lbl, val, full){
            return '<div class="nvv-item'+(full?' full':'')+'"><span class="nvv-lbl">'+lbl+'</span>'
                 + '<span class="nvv-val">'+(val ? APP.escape(String(val)) : '—')+'</span></div>';
        }
        var h = '';
        // Hero
        h += '<div class="nvv-hero"><div class="nvv-ava">'+APP.escape(initials(e.ho_ten))+'</div>'
           + '<div><div class="nm">'+APP.escape(e.ho_ten||'')+'</div>'
           + '<div class="sub">'+APP.escape(e.ma_nv||'')+(e.chuc_danh?' · '+APP.escape(e.chuc_danh):'')
           + ' · '+(e.trang_thai==1?'Đang làm':'Nghỉ việc')+'</div></div></div>';

        // Thông tin cơ bản
        h += '<div class="nvv-sec">Thông tin cơ bản</div><div class="nvv-grid">'
           + row('Mã nhân viên', e.ma_nv)
           + row('Họ và tên', e.ho_ten)
           + row('Ngày sinh', e.ngay_sinh ? APP.formatDate(e.ngay_sinh) : '')
           + row('Giới tính', gtMap[e.gioi_tinh] || e.gioi_tinh)
           + row('Khoa / Phòng', e.ten_khoa_phong || (e.khoa_phong_text ? e.khoa_phong_text + ' (chưa gán danh mục)' : ''))
           + row('Chức danh', e.chuc_danh)
           + row('Văn bằng / Trình độ', e.trinh_do)
           + row('Chuyên khoa', e.chuyen_khoa)
           + '</div>';

        // Chứng chỉ hành nghề
        h += '<div class="nvv-sec">Chứng chỉ hành nghề (CCHN)</div><div class="nvv-grid">'
           + row('Số CCHN', e.so_cchn)
           + row('Ngày cấp CCHN', e.ngay_cap_cchn ? APP.formatDate(e.ngay_cap_cchn) : '')
           + row('Phạm vi hành nghề', e.pham_vi_hanh_nghe, true)
           + row('Quyết định bổ sung phạm vi', e.qd_bo_sung_pham_vi, true)
           + row('Điều chỉnh phạm vi HĐCM', e.dieu_chinh_pham_vi)
           + row('Ngày điều chỉnh', e.ngay_dieu_chinh ? APP.formatDate(e.ngay_dieu_chinh) : '')
           + row('Chuyên khoa cần cập nhật KTYK', e.chuyen_khoa_cap_nhat, true)
           + '</div>';

        // Liên hệ
        h += '<div class="nvv-sec">Liên hệ</div><div class="nvv-grid">'
           + row('Điện thoại', e.dien_thoai)
           + row('Email', e.email)
           + row('Địa chỉ', e.dia_chi, true)
           + '</div>';

        if (CAN_EDIT) h += '<div style="margin-top:16px"><button class="btn btn-sm btn-primary" onclick="$(\'#drawerView\').removeClass(\'open\').find(\'.drawer\').removeClass(\'open\'); openEdit('+e.id+')">Sửa thông tin</button></div>';
        $('#nvvBody').html(h);
    });
}

function openEdit(id) {
    APP.ajax(URL, {action: 'getById', id: id}).done(function (res) {
        if (!res.success) { APP.toast(res.message, 'error'); return; }
        var e = res.data;
        $('#modalTitle').text('Sửa nhân viên');
        $('#f_id').val(e.id);
        $('#f_ma_nv').val(e.ma_nv);
        $('#f_ho_ten').val(e.ho_ten);
        $('#f_ngay_sinh').val(e.ngay_sinh || '');
        $('#f_gioi_tinh').val(e.gioi_tinh || '');
        $('#f_chuc_danh').val(e.chuc_danh || '');
        $('#f_khoa_phong_id').val(e.khoa_phong_id || '');
        $('#f_trinh_do').val(e.trinh_do || '');
        $('#f_chuyen_khoa').val(e.chuyen_khoa || '');
        $('#f_dien_thoai').val(e.dien_thoai || '');
        $('#f_email').val(e.email || '');
        $('#f_dia_chi').val(e.dia_chi || '');
        $('#f_so_cchn').val(e.so_cchn || '');
        $('#f_ngay_cap_cchn').val(e.ngay_cap_cchn || '');
        $('#f_pham_vi_hanh_nghe').val(e.pham_vi_hanh_nghe || '');
        $('#f_qd_bo_sung_pham_vi').val(e.qd_bo_sung_pham_vi || '');
        $('#f_dieu_chinh_pham_vi').val(e.dieu_chinh_pham_vi || '');
        $('#f_ngay_dieu_chinh').val(e.ngay_dieu_chinh || '');
        $('#f_chuyen_khoa_cap_nhat').val(e.chuyen_khoa_cap_nhat || '');
        $('#f_trang_thai').val(e.trang_thai);
        $('#modalForm').addClass('open');
    });
}
function closeModal() { $('#modalForm').removeClass('open'); }

$('#formNV').on('submit', function (e) {
    e.preventDefault();
    var data = $(this).serializeArray();
    data.push({name: 'action', value: $('#f_id').val() ? 'update' : 'insert'});
    APP.ajax(URL, data).done(function (res) {
        if (res.success) { APP.toast(res.message, 'success'); closeModal(); load(); }
        else APP.toast(res.message, 'error');
    });
});

function trashItem(id) {
    APP.confirm('Chuyển nhân viên này vào thùng rác?', function () {
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
    APP.confirm('Xóa VĨNH VIỄN nhân viên này?', function () {
        APP.ajax(URL, {action: 'delete', id: id}).done(function (res) {
            res.success ? (APP.toast(res.message, 'success'), load()) : APP.toast(res.message, 'error');
        });
    }, {yesText: 'Xóa vĩnh viễn'});
}

load();
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
