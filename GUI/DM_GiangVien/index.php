<?php
require_once __DIR__ . '/../../bootstrap.php';

Helper::requireLogin();
if (!PhanQuyenHelper::hasQuyen('DM_GiangVien', PhanQuyenHelper::QUYEN_XEM)) {
    echo 'Bạn không có quyền truy cập chức năng này.'; exit;
}
$canAdd = PhanQuyenHelper::hasQuyen('DM_GiangVien', PhanQuyenHelper::QUYEN_THEM);
$canEdit = PhanQuyenHelper::hasQuyen('DM_GiangVien', PhanQuyenHelper::QUYEN_SUA);
$canDel = PhanQuyenHelper::hasQuyen('DM_GiangVien', PhanQuyenHelper::QUYEN_XOA);

$pageTitle = 'Giảng viên';
$activeMenu = 'DM_GiangVien';
$avatarUrl = AppConfig::baseUrl('assets/uploads/giangvien/');
require __DIR__ . '/../layouts/header.php';
?>
<div class="breadcrumb">
    <a href="<?= AppConfig::baseUrl('GUI/dashboard/index.php') ?>">Trang chủ</a>
    <span class="sep">›</span> Đào tạo
    <span class="sep">›</span> <span>Giảng viên</span>
</div>

<div class="hv-stats">
    <div class="hv-stat">
        <div class="hv-stat-icon hv-stat-blue">
            <?= IconHelper::svg('graduation-cap', '22') ?>
        </div>
        <div><div class="hv-stat-label">Tổng giảng viên</div><div class="hv-stat-value" id="stTotal">—</div></div>
    </div>
    <div class="hv-stat">
        <div class="hv-stat-icon hv-stat-green">
            <?= IconHelper::svg('users', '22') ?>
        </div>
        <div><div class="hv-stat-label">Cơ hữu</div><div class="hv-stat-value" id="stCoHuu">—</div></div>
    </div>
    <div class="hv-stat">
        <div class="hv-stat-icon hv-stat-orange">
            <?= IconHelper::svg('star', '22') ?>
        </div>
        <div><div class="hv-stat-label">Thỉnh giảng</div><div class="hv-stat-value" id="stThinh">—</div></div>
    </div>
    <div class="hv-stat">
        <div class="hv-stat-icon hv-stat-purple">
            <?= IconHelper::svg('upload', '22') ?>
        </div>
        <div><div class="hv-stat-label">Khách mời</div><div class="hv-stat-value" id="stKhach">—</div></div>
    </div>
</div>

<div class="card">
    <div class="toolbar">
        <div class="left">
            <input type="text" id="search" class="form-control" placeholder="Tìm mã, tên, email, chuyên môn..." style="max-width:320px">
            <select id="filterLoai" class="form-select" style="max-width:200px">
                <option value="0">Tất cả loại GV</option>
                <option value="1">Cơ hữu</option>
                <option value="2">Thỉnh giảng</option>
                <option value="3">Khách mời</option>
            </select>
            <select id="filterTT" class="form-select" style="max-width:180px">
                <option value="">Mọi trạng thái</option>
                <option value="1">Đang hoạt động</option>
                <option value="0">Ngừng hoạt động</option>
            </select>
            <select id="filterDX" class="form-select" style="max-width:160px">
                <option value="0">Đang dùng</option>
                <option value="1">Thùng rác</option>
            </select>
        </div>
        <div class="right">
            <button type="button" class="btn" onclick="exportExcel()" title="Xuất Excel"><?= IconHelper::svg('download','16') ?> Xuất Excel</button>
            <?php if ($canAdd): ?>
                <button type="button" class="btn btn-primary" onclick="openCreate()">+ Thêm giảng viên</button>
            <?php endif; ?>
        </div>
    </div>

    <div class="gv-grid" id="gvGrid"></div>

    <div class="pagination-wrap">
        <div id="pageInfo" class="text-muted">-</div>
        <div id="pageNav"></div>
    </div>
</div>

<!-- Modal form -->
<div class="modal-backdrop" id="modalForm">
    <div class="modal" style="max-width:820px">
        <div class="modal-header">
            <h3 id="modalTitle">Thêm giảng viên</h3>
            <button type="button" class="close" onclick="closeModal()">&times;</button>
        </div>
        <form id="formGV">
            <div class="modal-body">
                <input type="hidden" name="id" id="f_id">

                <div class="form-row">
                    <div class="form-group">
                        <label>Loại giảng viên <span class="required">*</span></label>
                        <select name="loai_gv" id="f_loai" class="form-select">
                            <option value="1">Cơ hữu (là nhân viên cơ quan)</option>
                            <option value="2">Thỉnh giảng (mời từ ngoài)</option>
                            <option value="3">Khách mời / Báo cáo viên</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Trạng thái</label>
                        <select name="trang_thai" id="f_tt" class="form-select">
                            <option value="1">Đang hoạt động</option>
                            <option value="0">Ngừng hoạt động</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Mã giảng viên <span class="required">*</span></label>
                        <input type="text" name="ma_gv" id="f_ma" class="form-control" required maxlength="50">
                    </div>
                    <div class="form-group">
                        <label>Họ và tên <span class="required">*</span></label>
                        <input type="text" name="ho_ten" id="f_hoten" class="form-control" required maxlength="150">
                    </div>
                </div>

                <div class="form-row-3">
                    <div class="form-group">
                        <label>Học hàm</label>
                        <select name="hoc_ham" id="f_hh" class="form-select">
                            <option value="">--</option>
                            <option>GS</option>
                            <option>PGS</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Học vị</label>
                        <select name="hoc_vi" id="f_hv" class="form-select">
                            <option value="">--</option>
                            <option>TS</option>
                            <option>ThS</option>
                            <option>BS CKII</option>
                            <option>BS CKI</option>
                            <option>BS</option>
                            <option>CN</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Giới tính</label>
                        <select name="gioi_tinh" id="f_gt" class="form-select">
                            <option value="">--</option>
                            <option>Nam</option>
                            <option>Nữ</option>
                            <option>Khác</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label>Chuyên môn / Lĩnh vực giảng dạy</label>
                    <input type="text" name="chuyen_mon" id="f_cm" class="form-control" maxlength="255" placeholder="VD: Tim mạch can thiệp, Hồi sức cấp cứu...">
                </div>

                <div class="gv-internal-row" id="rowInternal">
                    <div class="form-group">
                        <label>Nhân viên cơ quan (nếu là cơ hữu)</label>
                        <select name="nhan_vien_id" id="f_nv" class="form-select">
                            <option value="">-- Chọn nhân viên --</option>
                        </select>
                    </div>
                </div>

                <div class="gv-external-row" id="rowExternal" style="display:none">
                    <div class="form-group">
                        <label>Đơn vị công tác (ngoài cơ quan)</label>
                        <input type="text" name="don_vi_cong_tac" id="f_dvct" class="form-control" maxlength="255" placeholder="VD: BV Bạch Mai, ĐH Y Hà Nội...">
                    </div>
                </div>

                <div class="form-row-3">
                    <div class="form-group">
                        <label>Ngày sinh</label>
                        <input type="date" name="ngay_sinh" id="f_ns" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" id="f_em" class="form-control" maxlength="150">
                    </div>
                    <div class="form-group">
                        <label>Điện thoại</label>
                        <input type="tel" name="dien_thoai" id="f_dt" class="form-control" maxlength="30">
                    </div>
                </div>

                <div class="form-group">
                    <label>Ghi chú</label>
                    <textarea name="ghi_chu" id="f_gc" class="form-control" rows="2" maxlength="500"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn" onclick="closeModal()">Hủy</button>
                <button type="submit" class="btn btn-primary">Lưu</button>
            </div>
        </form>
    </div>
</div>

<!-- Drawer detail -->
<div class="drawer-backdrop" id="drawerDetail">
    <div class="drawer">
        <div class="drawer-header">
            <div>
                <h3 id="dTitle" style="margin:0">Chi tiết giảng viên</h3>
                <div id="dSubtitle" class="text-muted" style="font-size:12.5px;margin-top:2px"></div>
            </div>
            <button type="button" class="close" onclick="closeDrawer()">&times;</button>
        </div>
        <div class="drawer-body" id="dBody"></div>
    </div>
</div>

<script>
var URL = APP_BASE + 'GUI/DM_GiangVien/ajax_handler.php';
var AVATAR_URL = <?= json_encode($avatarUrl) ?>;
var CAN_EDIT = <?= $canEdit?'true':'false' ?>;
var CAN_DEL = <?= $canDel?'true':'false' ?>;
var state = { page:1, pageSize:20, search:'', loaiGv:0, trangThai:'', daXoa:0 };
function exportExcel(){ var p=new URLSearchParams({search:state.search||'',da_xoa:state.daXoa||0,loai_gv:state.loaiGv||0,trang_thai:state.trangThai||''}); window.location=APP_BASE+'GUI/DM_GiangVien/export.php?'+p.toString(); }
var nvLoaded = false;
var LOAI_TXT = {1:'Cơ hữu',2:'Thỉnh giảng',3:'Khách mời'};

var ICON_EDIT = '<?= addslashes(IconHelper::svg('edit', '14')) ?>';
var ICON_TRASH = '<?= addslashes(IconHelper::svg('trash', '14')) ?>';
var ICON_DETAIL = '<?= addslashes(IconHelper::svg('eye', '14')) ?>';
var ICON_EMPTY = '<?= addslashes(IconHelper::svg('dashboard', '40')) ?>';
var ICON_BOOK_OPEN = '<?= addslashes(IconHelper::svg('book-open', '13')) ?>';
var ICON_BUILDING = '<?= addslashes(IconHelper::svg('building', '13')) ?>';
var ICON_FILE_TEXT = '<?= addslashes(IconHelper::svg('file-text', '12')) ?>';
var ICON_CHECK = '<?= addslashes(IconHelper::svg('check', '12')) ?>';
var ICON_GRADUATION = '<?= addslashes(IconHelper::svg('graduation-cap', '22')) ?>';
var ICON_USERS = '<?= addslashes(IconHelper::svg('users', '22')) ?>';
var ICON_STAR = '<?= addslashes(IconHelper::svg('star', '22')) ?>';
var ICON_UPLOAD = '<?= addslashes(IconHelper::svg('upload', '22')) ?>';

// ============ Helpers ============
function initials(name){ if(!name) return '?'; var p=name.trim().split(/\s+/); return p.length===1?p[0].substr(0,2).toUpperCase():(p[p.length-2][0]+p[p.length-1][0]).toUpperCase(); }
function colorFromName(name){ var c=['#2563eb','#7c3aed','#db2777','#dc2626','#ea580c','#d97706','#16a34a','#0891b2','#4f46e5','#0284c7']; var h=0; for(var i=0;i<(name||'').length;i++) h=(h*31+name.charCodeAt(i))&0xffff; return c[h%c.length]; }
function gvAvatar(g, size){ size=size||56; if (g.avatar) return '<div class="hv-av" style="width:'+size+'px;height:'+size+'px"><img src="'+AVATAR_URL+APP.escape(g.avatar)+'"></div>'; return '<div class="hv-av hv-av-initials" style="width:'+size+'px;height:'+size+'px;background:'+colorFromName(g.ho_ten)+'">'+APP.escape(initials(g.ho_ten))+'</div>'; }
function loaiBadge(l){ var t=parseInt(l,10); var cls=t===1?'gv-badge-internal':(t===2?'gv-badge-visit':'gv-badge-guest'); return '<span class="gv-badge '+cls+'">'+APP.escape(LOAI_TXT[t]||'')+'</span>'; }
function titleLine(g){ var p=[]; if(g.hoc_ham) p.push(g.hoc_ham); if(g.hoc_vi) p.push(g.hoc_vi); return p.join('. '); }

// ============ Load ============
function loadStats(){
    APP.ajax(URL,{action:'getStats'}).done(function(res){
        if (!res.success) return;
        $('#stTotal').text(res.data.total||0);
        $('#stCoHuu').text(res.data.co_huu||0);
        $('#stThinh').text(res.data.thinh_giang||0);
        $('#stKhach').text(res.data.khach_moi||0);
    });
}

function load(){
    APP.showLoading('#gvGrid');
    APP.ajax(URL, {
        action:'getPaged', page:state.page, pageSize:state.pageSize,
        search:state.search, da_xoa:state.daXoa, loai_gv:state.loaiGv, trang_thai:state.trangThai
    }).done(function(res){
        APP.hideLoading('#gvGrid');
        if (!res.success){ APP.toast(res.message,'error'); return; }
        renderGrid(res.data);
        renderPager(res.pagination);
    });
}

function renderGrid(rows){
    var $g = $('#gvGrid').empty();
    if (!rows.length){
        $g.html('<div class="empty-state" style="padding:60px"><div class="icon">' + ICON_EMPTY + '</div>Không có giảng viên nào</div>');
        return;
    }
    rows.forEach(function(g){
        var actions = '';
        if (state.daXoa==0){
            actions += '<button class="btn btn-sm" onclick="openDetail('+g.id+')" title="Chi tiết">' + ICON_DETAIL + '</button>';
            if (CAN_EDIT) actions += '<button class="btn btn-sm" onclick="openEdit('+g.id+')" title="Sửa">' + ICON_EDIT + '</button>';
            if (CAN_DEL) actions += '<button class="btn btn-sm btn-danger" onclick="trashItem('+g.id+')" title="Xóa">' + ICON_TRASH + '</button>';
        } else {
            if (CAN_EDIT) actions += '<button class="btn btn-sm btn-success" onclick="restoreItem('+g.id+')">Khôi phục</button>';
            if (CAN_DEL) actions += '<button class="btn btn-sm btn-danger" onclick="deleteItem('+g.id+')">Xóa vĩnh viễn</button>';
        }

        var tt = parseInt(g.trang_thai,10);
        var ttDot = tt===1 ? '<span class="gv-dot gv-dot-on" title="Đang hoạt động"></span>' : '<span class="gv-dot gv-dot-off" title="Ngừng hoạt động"></span>';

        var don_vi = g.loai_gv == 1
            ? (g.ten_khoa_phong ? APP.escape(g.ten_khoa_phong) : '-')
            : (g.don_vi_cong_tac ? APP.escape(g.don_vi_cong_tac) : '-');

        $g.append(
            '<div class="gv-card" data-id="'+g.id+'">'+
                '<div class="gv-card-head">'+
                    gvAvatar(g, 56)+
                    '<div class="gv-card-info">'+
                        '<div class="gv-card-name">'+ttDot+APP.escape(g.ho_ten||'')+'</div>'+
                        (titleLine(g)?'<div class="gv-card-title">'+APP.escape(titleLine(g))+'</div>':'')+
                        '<div class="gv-card-code">'+APP.escape(g.ma_gv||'')+'</div>'+
                    '</div>'+
                '</div>'+
                '<div class="gv-card-meta">'+
                    (g.chuyen_mon?'<div class="gv-meta-row" title="Chuyên môn">' + ICON_BOOK_OPEN + APP.escape(g.chuyen_mon)+'</div>':'')+
                    '<div class="gv-meta-row" title="Đơn vị">' + ICON_BUILDING + don_vi+'</div>'+
                '</div>'+
                '<div class="gv-card-foot">'+
                    '<div class="gv-card-badges">'+loaiBadge(g.loai_gv)+'<span class="gv-stat-mini" title="Số phân công">' + ICON_FILE_TEXT + ' '+(g.so_lop_phan_cong||0)+'</span><span class="gv-stat-mini" title="Buổi đã dạy">' + ICON_CHECK + ' '+(g.so_buoi_da_day||0)+'</span></div>'+
                    '<div class="actions">'+actions+'</div>'+
                '</div>'+
            '</div>'
        );
    });
}

function renderPager(p){
    var from=(p.currentPage-1)*p.pageSize+1;
    var to=Math.min(from+p.pageSize-1,p.totalRecords);
    $('#pageInfo').text(p.totalRecords?'Hiển thị '+from+'-'+to+' / '+p.totalRecords:'Không có bản ghi');
    $('#pageNav').html(APP.renderPagination(p));
}
$('#pageNav').on('click','button[data-p]',function(){ var p=parseInt($(this).data('p'),10); if(!p||p===state.page) return; state.page=p; load(); });
$('#search').on('input', APP.debounce(function(){state.search=$(this).val();state.page=1;load();},350));
$('#filterLoai').on('change',function(){state.loaiGv=parseInt(this.value,10)||0;state.page=1;load();});
$('#filterTT').on('change',function(){state.trangThai=this.value;state.page=1;load();});
$('#filterDX').on('change',function(){state.daXoa=parseInt(this.value,10)||0;state.page=1;load();});

// ============ Modal ============
function ensureNvCombo(cb){
    if (nvLoaded){ cb&&cb(); return; }
    APP.ajax(URL,{action:'getComboNhanVien'}).done(function(res){
        if (!res.success) return;
        var $s = $('#f_nv').empty().append('<option value="">-- Chọn nhân viên --</option>');
        (res.data||[]).forEach(function(n){ $s.append('<option value="'+n.id+'">'+APP.escape(n.ma_nv)+' - '+APP.escape(n.ho_ten)+'</option>'); });
        nvLoaded = true; cb&&cb();
    });
}

function toggleLoaiRow(){
    var l = parseInt($('#f_loai').val(),10);
    $('#rowInternal').toggle(l===1);
    $('#rowExternal').toggle(l!==1);
}
$('#f_loai').on('change', toggleLoaiRow);

function openCreate(){
    ensureNvCombo();
    $('#modalTitle').text('Thêm giảng viên');
    $('#formGV')[0].reset();
    $('#f_id').val('');
    $('#f_loai').val('1'); $('#f_tt').val('1');
    toggleLoaiRow();
    $('#modalForm').addClass('open');
}
function openEdit(id){
    ensureNvCombo(function(){
        APP.ajax(URL,{action:'getById', id:id}).done(function(res){
            if (!res.success){ APP.toast(res.message,'error'); return; }
            var g = res.data;
            $('#modalTitle').text('Sửa giảng viên');
            $('#f_id').val(g.id);
            $('#f_ma').val(g.ma_gv); $('#f_hoten').val(g.ho_ten);
            $('#f_loai').val(g.loai_gv); $('#f_tt').val(g.trang_thai);
            $('#f_hh').val(g.hoc_ham||''); $('#f_hv').val(g.hoc_vi||''); $('#f_gt').val(g.gioi_tinh||'');
            $('#f_cm').val(g.chuyen_mon||'');
            $('#f_nv').val(g.nhan_vien_id||''); $('#f_dvct').val(g.don_vi_cong_tac||'');
            $('#f_ns').val(g.ngay_sinh||''); $('#f_em').val(g.email||''); $('#f_dt').val(g.dien_thoai||'');
            $('#f_gc').val(g.ghi_chu||'');
            toggleLoaiRow();
            $('#modalForm').addClass('open');
        });
    });
}
function closeModal(){ $('#modalForm').removeClass('open'); }

$('#formGV').on('submit', function(e){
    e.preventDefault();
    var data = $(this).serializeArray();
    data.push({name:'action', value: $('#f_id').val()?'update':'insert'});
    APP.ajax(URL, data).done(function(res){
        if (res.success){ APP.toast(res.message,'success'); closeModal(); load(); loadStats(); }
        else APP.toast(res.message,'error');
    });
});

function trashItem(id){ APP.confirm('Chuyển GV vào thùng rác?',function(){ APP.ajax(URL,{action:'trash',id:id}).done(function(res){ res.success?(APP.toast(res.message,'success'),load(),loadStats()):APP.toast(res.message,'error'); }); }); }
function restoreItem(id){ APP.ajax(URL,{action:'restore',id:id}).done(function(res){ res.success?(APP.toast(res.message,'success'),load(),loadStats()):APP.toast(res.message,'error'); }); }
function deleteItem(id){ APP.confirm('Xóa VĨNH VIỄN?',function(){ APP.ajax(URL,{action:'delete',id:id}).done(function(res){ res.success?(APP.toast(res.message,'success'),load(),loadStats()):APP.toast(res.message,'error'); }); },{yesText:'Xóa vĩnh viễn'}); }

// ============ Drawer ============
function openDetail(id){
    $('#drawerDetail').addClass('open').find('.drawer').addClass('open');
    $('#dTitle').text('Đang tải...'); $('#dSubtitle').text(''); $('#dBody').html('<div style="padding:30px;text-align:center;color:var(--gray-500)">Đang tải...</div>');
    APP.ajax(URL,{action:'getDetail',id:id}).done(function(res){
        if (!res.success){ $('#dBody').html('<div style="padding:20px;color:#b91c1c">'+APP.escape(res.message||'')+'</div>'); return; }
        renderDetail(res.data);
    });
}
function closeDrawer(){ $('#drawerDetail').removeClass('open').find('.drawer').removeClass('open'); }

function renderDetail(d){
    var g = d.gv; var pc = d.phan_cong || [];
    $('#dTitle').text(g.ho_ten);
    var sub = []; if (titleLine(g)) sub.push(titleLine(g)); sub.push(g.ma_gv);
    $('#dSubtitle').text(sub.join(' · '));

    var html = '';
    html += '<div style="display:flex;gap:14px;align-items:center;padding:14px;background:var(--gray-50);border-radius:var(--radius);margin-bottom:14px">'+
        gvAvatar(g, 64) +
        '<div style="flex:1;min-width:0">'+
            '<div style="font-weight:600;font-size:15px">'+APP.escape(g.ho_ten)+'</div>'+
            (titleLine(g)?'<div class="text-muted" style="font-size:12.5px">'+APP.escape(titleLine(g))+'</div>':'')+
            '<div style="margin-top:6px">'+loaiBadge(g.loai_gv)+'</div>'+
        '</div>'+
    '</div>';

    html += '<div class="lh-detail-grid">';
    html += dRow('Mã', APP.escape(g.ma_gv));
    html += dRow('Chuyên môn', g.chuyen_mon ? APP.escape(g.chuyen_mon) : '-');
    html += dRow('Đơn vị', g.loai_gv==1 ? (g.ten_khoa_phong?APP.escape(g.ten_khoa_phong):'-') : (g.don_vi_cong_tac?APP.escape(g.don_vi_cong_tac):'-'));
    html += dRow('Email', g.email?APP.escape(g.email):'-');
    html += dRow('Điện thoại', g.dien_thoai?APP.escape(g.dien_thoai):'-');
    html += dRow('Giới tính', g.gioi_tinh?APP.escape(g.gioi_tinh):'-');
    html += '</div>';

    if (g.ghi_chu) html += '<div class="lh-detail-block"><div class="lh-detail-label">Ghi chú</div><div>'+APP.escape(g.ghi_chu)+'</div></div>';

    html += '<div class="lh-detail-block"><div class="lh-detail-label">Phân công ('+pc.length+')</div>';
    if (pc.length){
        html += '<div class="gv-pc-list">';
        var VT_TXT = {1:'Chính', 2:'Phụ', 3:'Trợ giảng'};
        var TT_TXT = {0:'Dự kiến',1:'Đang dạy',2:'Hoàn thành',3:'Hủy'};
        pc.forEach(function(p){
            var vtCls = parseInt(p.vai_tro,10)===1?'main':(parseInt(p.vai_tro,10)===2?'sub':'asst');
            var ttCls = ['plan','done','done','cancel'][parseInt(p.trang_thai,10)] || 'plan';
            html += '<div class="gv-pc-row">'+
                '<div class="gv-pc-info">'+
                    '<div style="font-weight:600;font-size:13.5px">'+APP.escape(p.ma_lop)+(p.ten_lop?' · '+APP.escape(p.ten_lop):'')+'</div>'+
                    (p.ten_mon_hoc?'<div class="text-muted" style="font-size:12px">'+APP.escape(p.ma_mon_hoc+' - '+p.ten_mon_hoc)+'</div>':'<div class="text-muted" style="font-size:12px">Phụ trách cả lớp</div>')+
                '</div>'+
                '<span class="gv-vt gv-vt-'+vtCls+'">'+APP.escape(VT_TXT[p.vai_tro]||'')+'</span>'+
                '<span class="lh-badge lh-badge-'+ttCls+'">'+APP.escape(TT_TXT[p.trang_thai]||'')+'</span>'+
            '</div>';
        });
        html += '</div>';
    } else {
        html += '<div style="padding:14px;background:var(--gray-50);border-radius:var(--radius);color:var(--gray-500);font-size:13px">Chưa có phân công nào</div>';
    }
    html += '</div>';

    html += '<div class="lh-detail-actions">';
    if (CAN_EDIT) html += '<button class="btn btn-primary" onclick="openEdit('+g.id+');closeDrawer();">Sửa hồ sơ</button>';
    html += '</div>';

    $('#dBody').html(html);
}
function dRow(label,val){ return '<div class="lh-detail-row"><div class="lh-detail-label">'+label+'</div><div class="lh-detail-val">'+val+'</div></div>'; }

// Init
load(); loadStats();
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
