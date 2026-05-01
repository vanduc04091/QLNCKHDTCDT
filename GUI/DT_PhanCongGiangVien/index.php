<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../BUS/DT_LopHoc_BUS.php';
require_once __DIR__ . '/../../BUS/DM_GiangVien_BUS.php';

Helper::requireLogin();
if (!PhanQuyenHelper::hasQuyen('DT_PhanCongGiangVien', PhanQuyenHelper::QUYEN_XEM)) {
    echo 'Bạn không có quyền truy cập chức năng này.'; exit;
}
$canAdd = PhanQuyenHelper::hasQuyen('DT_PhanCongGiangVien', PhanQuyenHelper::QUYEN_THEM);
$canEdit = PhanQuyenHelper::hasQuyen('DT_PhanCongGiangVien', PhanQuyenHelper::QUYEN_SUA);
$canDel = PhanQuyenHelper::hasQuyen('DT_PhanCongGiangVien', PhanQuyenHelper::QUYEN_XOA);

$lopList = DT_LopHoc_BUS::getPaged(1, 500, '', 0, 0, -1)['data'];
$gvList = DM_GiangVien_BUS::getCombo();

$pageTitle = 'Phân công giảng viên';
$activeMenu = 'DT_PhanCongGiangVien';
$avatarUrl = AppConfig::baseUrl('assets/uploads/giangvien/');
require __DIR__ . '/../layouts/header.php';
?>
<div class="breadcrumb">
    <a href="<?= AppConfig::baseUrl('GUI/dashboard/index.php') ?>">Trang chủ</a>
    <span class="sep">›</span> Đào tạo
    <span class="sep">›</span> <span>Phân công giảng viên</span>
</div>

<div class="hv-stats">
    <div class="hv-stat">
        <div class="hv-stat-icon hv-stat-blue">
            <?= IconHelper::svg('file-text', '22') ?>
        </div>
        <div><div class="hv-stat-label">Tổng phân công</div><div class="hv-stat-value" id="stTotal">—</div></div>
    </div>
    <div class="hv-stat">
        <div class="hv-stat-icon hv-stat-green">
            <?= IconHelper::svg('check', '22') ?>
        </div>
        <div><div class="hv-stat-label">Đang dạy</div><div class="hv-stat-value" id="stDoing">—</div></div>
    </div>
    <div class="hv-stat">
        <div class="hv-stat-icon hv-stat-orange">
            <?= IconHelper::svg('school', '22') ?>
        </div>
        <div><div class="hv-stat-label">Số GV</div><div class="hv-stat-value" id="stGV">—</div></div>
    </div>
    <div class="hv-stat">
        <div class="hv-stat-icon hv-stat-purple">
            <?= IconHelper::svg('users', '22') ?>
        </div>
        <div><div class="hv-stat-label">Số lớp</div><div class="hv-stat-value" id="stLop">—</div></div>
    </div>
</div>

<div class="card">
    <div class="lh-toolbar">
        <div class="lh-toolbar-left">
            <div class="segmented" role="tablist" aria-label="Nhóm theo">
                <button type="button" class="seg-btn is-active" data-group="lop" role="tab" aria-selected="true">
                    <?= IconHelper::svg('file-text', '16') ?>
                    Theo lớp
                </button>
                <button type="button" class="seg-btn" data-group="gv" role="tab" aria-selected="false">
                    <?= IconHelper::svg('user', '16') ?>
                    Theo giảng viên
                </button>
            </div>
        </div>
        <div class="lh-toolbar-right">
            <?php if ($canAdd): ?>
                <button type="button" class="btn" onclick="openBulk()">
                    <?= IconHelper::svg('plus', '16') ?>
                    Phân công nhiều môn
                </button>
                <button type="button" class="btn btn-primary" onclick="openCreate()">
                    + Thêm phân công
                </button>
            <?php endif; ?>
        </div>
    </div>

    <div class="lh-filter">
        <div class="lh-filter-field">
            <label>Lớp học</label>
            <select id="fLop" class="form-select">
                <option value="0">Tất cả lớp</option>
                <?php foreach ($lopList as $l): ?>
                    <option value="<?= $l['id'] ?>"><?= Helper::h($l['ma_lop'] . ' - ' . $l['ten_lop']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="lh-filter-field">
            <label>Giảng viên</label>
            <select id="fGV" class="form-select">
                <option value="0">Tất cả giảng viên</option>
                <?php foreach ($gvList as $g): ?>
                    <option value="<?= $g['id'] ?>"><?= Helper::h($g['ma_gv'] . ' - ' . $g['ho_ten']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="lh-filter-field">
            <label>Vai trò</label>
            <select id="fVT" class="form-select">
                <option value="">Tất cả vai trò</option>
                <option value="1">Chính</option>
                <option value="2">Phụ</option>
                <option value="3">Trợ giảng</option>
            </select>
        </div>
        <div class="lh-filter-field">
            <label>Trạng thái</label>
            <select id="fTT" class="form-select">
                <option value="">Tất cả</option>
                <option value="0">Dự kiến</option>
                <option value="1">Đang dạy</option>
                <option value="2">Hoàn thành</option>
                <option value="3">Hủy</option>
            </select>
        </div>
    </div>

    <div class="pc-content" id="pcContent" style="padding:0 18px 18px"></div>
</div>

<!-- Modal phân công đơn -->
<div class="modal-backdrop" id="modalForm">
    <div class="modal" style="max-width:760px">
        <div class="modal-header">
            <h3 id="modalTitle">Phân công giảng viên</h3>
            <button type="button" class="close" onclick="closeModal()">&times;</button>
        </div>
        <form id="formPC">
            <div class="modal-body">
                <input type="hidden" name="id" id="f_id">
                <input type="hidden" name="force_conflict" id="f_force" value="0">

                <div class="form-row">
                    <div class="form-group">
                        <label>Giảng viên <span class="required">*</span></label>
                        <select name="giang_vien_id" id="f_gv" class="form-select" required>
                            <option value="">-- Chọn giảng viên --</option>
                            <?php foreach ($gvList as $g): ?>
                                <option value="<?= $g['id'] ?>"><?= Helper::h(($g['hoc_ham']?$g['hoc_ham'].'.':'').($g['hoc_vi']?$g['hoc_vi'].' ':'').$g['ho_ten']) ?> (<?= Helper::h($g['ma_gv']) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Vai trò <span class="required">*</span></label>
                        <select name="vai_tro" id="f_vt" class="form-select">
                            <option value="1">Giảng viên chính</option>
                            <option value="2">Giảng viên phụ</option>
                            <option value="3">Trợ giảng</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Lớp học <span class="required">*</span></label>
                        <select name="lop_hoc_id" id="f_lop" class="form-select" required>
                            <option value="">-- Chọn lớp --</option>
                            <?php foreach ($lopList as $l): ?>
                                <option value="<?= $l['id'] ?>"><?= Helper::h($l['ma_lop'] . ' - ' . $l['ten_lop']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Môn học</label>
                        <select name="mon_hoc_id" id="f_mon" class="form-select">
                            <option value="">Phụ trách cả lớp</option>
                        </select>
                    </div>
                </div>

                <div class="form-row-3">
                    <div class="form-group">
                        <label>Số tiết phân công</label>
                        <input type="number" name="so_tiet_phan_cong" id="f_st" class="form-control" min="0">
                    </div>
                    <div class="form-group">
                        <label>Từ ngày</label>
                        <input type="date" name="tu_ngay" id="f_tn" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Đến ngày</label>
                        <input type="date" name="den_ngay" id="f_dn" class="form-control">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Trạng thái</label>
                        <select name="trang_thai" id="f_tt" class="form-select">
                            <option value="0">Dự kiến</option>
                            <option value="1">Đang dạy</option>
                            <option value="2">Hoàn thành</option>
                            <option value="3">Hủy</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Ghi chú</label>
                        <input type="text" name="ghi_chu" id="f_gc" class="form-control" maxlength="500">
                    </div>
                </div>

                <div id="conflictBox" class="lh-conflict" style="display:none"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn" onclick="closeModal()">Hủy</button>
                <button type="submit" class="btn btn-primary" id="btnSubmit">Lưu</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Bulk -->
<div class="modal-backdrop" id="modalBulk">
    <div class="modal" style="max-width:680px">
        <div class="modal-header">
            <h3>Phân công giảng viên cho nhiều môn</h3>
            <button type="button" class="close" onclick="closeBulk()">&times;</button>
        </div>
        <form id="formBulk">
            <div class="modal-body">
                <div class="form-row">
                    <div class="form-group">
                        <label>Giảng viên <span class="required">*</span></label>
                        <select name="giang_vien_id" id="b_gv" class="form-select" required>
                            <option value="">-- Chọn giảng viên --</option>
                            <?php foreach ($gvList as $g): ?>
                                <option value="<?= $g['id'] ?>"><?= Helper::h(($g['hoc_ham']?$g['hoc_ham'].'.':'').($g['hoc_vi']?$g['hoc_vi'].' ':'').$g['ho_ten']) ?> (<?= Helper::h($g['ma_gv']) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Vai trò</label>
                        <select name="vai_tro" id="b_vt" class="form-select">
                            <option value="1">Giảng viên chính</option>
                            <option value="2">Giảng viên phụ</option>
                            <option value="3">Trợ giảng</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label>Lớp học <span class="required">*</span></label>
                    <select name="lop_hoc_id" id="b_lop" class="form-select" required>
                        <option value="">-- Chọn lớp --</option>
                        <?php foreach ($lopList as $l): ?>
                            <option value="<?= $l['id'] ?>"><?= Helper::h($l['ma_lop'] . ' - ' . $l['ten_lop']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Chọn các môn (lớp này có)</label>
                    <div id="bMonHocList" class="pc-mon-list">
                        <div class="text-muted" style="padding:14px;text-align:center;font-size:13px">Chọn lớp để xem danh sách môn</div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn" onclick="closeBulk()">Hủy</button>
                <button type="submit" class="btn btn-primary">Phân công</button>
            </div>
        </form>
    </div>
</div>

<script>
var URL = APP_BASE + 'GUI/DT_PhanCongGiangVien/ajax_handler.php';
var AVATAR_URL = <?= json_encode($avatarUrl) ?>;
var CAN_EDIT = <?= $canEdit?'true':'false' ?>;
var CAN_DEL = <?= $canDel?'true':'false' ?>;
var ICON_EDIT = '<?= addslashes(IconHelper::svg('edit', '14')) ?>';
var ICON_TRASH = '<?= addslashes(IconHelper::svg('trash', '14')) ?>';
var ICON_FILE_TEXT = '<?= addslashes(IconHelper::svg('file-text', '14')) ?>';
var ICON_EMPTY = '<?= addslashes(IconHelper::svg('search', '40')) ?>';
var ICON_WARNING = '<?= addslashes(IconHelper::svg('alert-triangle', '16')) ?>';
var state = { group:'lop', filter:{lop_hoc_id:0,giang_vien_id:0,vai_tro:'',trang_thai:'',search:''}, items:[], monByLop:{} };
var VT_TXT = {1:'Chính',2:'Phụ',3:'Trợ giảng'};
var TT_TXT = {0:'Dự kiến',1:'Đang dạy',2:'Hoàn thành',3:'Hủy'};
var LOAI_TXT = {1:'Cơ hữu',2:'Thỉnh giảng',3:'Khách mời'};

function initials(name){ if(!name) return '?'; var p=name.trim().split(/\s+/); return p.length===1?p[0].substr(0,2).toUpperCase():(p[p.length-2][0]+p[p.length-1][0]).toUpperCase(); }
function colorFromName(name){ var c=['#2563eb','#7c3aed','#db2777','#dc2626','#ea580c','#d97706','#16a34a','#0891b2','#4f46e5','#0284c7']; var h=0; for(var i=0;i<(name||'').length;i++) h=(h*31+name.charCodeAt(i))&0xffff; return c[h%c.length]; }
function gvAvatar(g, size){ size=size||36; if (g.avatar_gv) return '<div class="hv-av" style="width:'+size+'px;height:'+size+'px"><img src="'+AVATAR_URL+APP.escape(g.avatar_gv)+'"></div>'; return '<div class="hv-av hv-av-initials" style="width:'+size+'px;height:'+size+'px;background:'+colorFromName(g.ho_ten_gv)+';font-size:11.5px">'+APP.escape(initials(g.ho_ten_gv))+'</div>'; }
function vtBadge(v){ var t=parseInt(v,10); var cls=t===1?'main':(t===2?'sub':'asst'); return '<span class="gv-vt gv-vt-'+cls+'">'+APP.escape(VT_TXT[t]||'')+'</span>'; }
function ttBadge(t){ var cls=['plan','done','done','cancel'][parseInt(t,10)]||'plan'; return '<span class="lh-badge lh-badge-'+cls+'">'+APP.escape(TT_TXT[t]||'')+'</span>'; }

// ============ Stats + Load ============
function loadStats(){
    APP.ajax(URL,{action:'getStats'}).done(function(res){
        if (!res.success) return;
        $('#stTotal').text(res.data.total||0);
        $('#stDoing').text(res.data.dang_day||0);
        $('#stGV').text(res.data.so_gv||0);
        $('#stLop').text(res.data.so_lop||0);
    });
}

function load(){
    APP.showLoading('#pcContent');
    APP.ajax(URL, $.extend({action:'getList'}, state.filter)).done(function(res){
        APP.hideLoading('#pcContent');
        if (!res.success){ APP.toast(res.message,'error'); return; }
        state.items = res.data || [];
        render();
    });
}

function render(){
    if (!state.items.length){
        $('#pcContent').html('<div class="empty-state" style="padding:60px 20px"><div class="icon">' + ICON_EMPTY + '</div>Chưa có phân công nào</div>');
        return;
    }
    if (state.group === 'lop') renderByLop();
    else renderByGV();
}

function renderByLop(){
    var groups = {};
    state.items.forEach(function(it){
        var k = it.lop_hoc_id;
        if (!groups[k]) groups[k] = { ma:it.ma_lop, ten:it.ten_lop, items:[] };
        groups[k].items.push(it);
    });
    var html = '';
    Object.values(groups).forEach(function(g){
        html += '<div class="pc-group">';
        html += '<div class="pc-group-head">' + ICON_FILE_TEXT + ' '+APP.escape(g.ma||'')+' · '+APP.escape(g.ten||'')+' <span class="pc-count">'+g.items.length+'</span></div>';
        html += '<div class="pc-rows">';
        g.items.forEach(function(it){ html += rowHtml(it, true); });
        html += '</div></div>';
    });
    $('#pcContent').html(html);
}

function renderByGV(){
    var groups = {};
    state.items.forEach(function(it){
        var k = it.giang_vien_id;
        if (!groups[k]) groups[k] = { ma:it.ma_gv, ho_ten:it.ho_ten_gv, avatar:it.avatar_gv, hoc_ham:it.hoc_ham, hoc_vi:it.hoc_vi, loai:it.loai_gv, items:[] };
        groups[k].items.push(it);
    });
    var html = '';
    Object.values(groups).forEach(function(g){
        var title = '';
        if (g.hoc_ham) title += g.hoc_ham + '. ';
        if (g.hoc_vi) title += g.hoc_vi + ' ';
        title += g.ho_ten;
        html += '<div class="pc-group">';
        html += '<div class="pc-group-head pc-group-gv">';
        var av = g.avatar ? '<div class="hv-av" style="width:32px;height:32px"><img src="'+AVATAR_URL+APP.escape(g.avatar)+'"></div>' : '<div class="hv-av hv-av-initials" style="width:32px;height:32px;background:'+colorFromName(g.ho_ten)+';font-size:11.5px">'+APP.escape(initials(g.ho_ten))+'</div>';
        html += av + '<div style="flex:1"><div style="font-weight:600;font-size:14px">'+APP.escape(title)+'</div><div class="text-muted" style="font-size:11.5px">'+APP.escape(g.ma||'')+(g.loai?' · '+APP.escape(LOAI_TXT[g.loai]||''):'')+'</div></div>';
        html += '<span class="pc-count">'+g.items.length+'</span></div>';
        html += '<div class="pc-rows">';
        g.items.forEach(function(it){ html += rowHtml(it, false); });
        html += '</div></div>';
    });
    $('#pcContent').html(html);
}

function rowHtml(it, showGV){
    var monTxt = it.ten_mon_hoc ? APP.escape(it.ma_mon_hoc + ' - ' + it.ten_mon_hoc) : '<span class="text-muted">Phụ trách cả lớp</span>';
    var leftHtml = '';
    if (showGV){
        var title = '';
        if (it.hoc_ham) title += it.hoc_ham + '. ';
        if (it.hoc_vi) title += it.hoc_vi + ' ';
        title += it.ho_ten_gv;
        leftHtml = '<div class="pc-row-left">'+gvAvatar(it,36)+'<div style="min-width:0"><div class="pc-gv-name">'+APP.escape(title)+'</div><div class="pc-gv-meta">'+APP.escape(it.ma_gv||'')+'</div></div></div>';
    } else {
        leftHtml = '<div class="pc-row-left"><div class="pc-gv-name">'+monTxt+'</div></div>';
    }
    var midHtml = showGV ? '<div class="pc-row-mid">'+monTxt+'</div>' : '';
    var actions = '';
    if (CAN_EDIT) actions += '<button class="btn btn-sm" onclick="openEdit('+it.id+')" title="Sửa">' + ICON_EDIT + '</button>';
    if (CAN_DEL) actions += '<button class="btn btn-sm btn-danger" onclick="deleteItem('+it.id+')" title="Xóa">' + ICON_TRASH + '</button>';
    var soTiet = it.so_tiet_phan_cong ? '<span class="pc-tiet">'+it.so_tiet_phan_cong+' tiết</span>' : '';
    return '<div class="pc-row">'+leftHtml+midHtml+'<div class="pc-row-right">'+vtBadge(it.vai_tro)+ttBadge(it.trang_thai)+soTiet+'<div class="actions">'+actions+'</div></div></div>';
}

// ============ Toolbar ============
$('.segmented').on('click', '.seg-btn', function(){
    var g = $(this).data('group'); if (g===state.group) return;
    state.group = g;
    $('.seg-btn').removeClass('is-active').attr('aria-selected','false');
    $(this).addClass('is-active').attr('aria-selected','true');
    render();
});
$('#fLop').on('change', function(){ state.filter.lop_hoc_id=parseInt(this.value,10)||0; load(); });
$('#fGV').on('change', function(){ state.filter.giang_vien_id=parseInt(this.value,10)||0; load(); });
$('#fVT').on('change', function(){ state.filter.vai_tro=this.value; load(); });
$('#fTT').on('change', function(){ state.filter.trang_thai=this.value; load(); });

// ============ Mon học theo lớp ============
function loadMonByLop(lopId, $select, includeAll){
    if (!lopId){ $select.html('<option value="">Phụ trách cả lớp</option>'); return $.Deferred().resolve().promise(); }
    if (state.monByLop[lopId]){ fillMon($select, state.monByLop[lopId], includeAll); return $.Deferred().resolve().promise(); }
    return APP.ajax(URL, {action:'getMonHocByLop', lop_hoc_id: lopId}).done(function(res){
        if (!res.success) return;
        state.monByLop[lopId] = res.data || [];
        fillMon($select, state.monByLop[lopId], includeAll);
    });
}
function fillMon($select, list, includeAll){
    var html = includeAll ? '<option value="">Phụ trách cả lớp</option>' : '';
    (list||[]).forEach(function(m){ html += '<option value="'+m.id+'">'+APP.escape(m.ma_mon_hoc+' - '+m.ten_mon_hoc)+'</option>'; });
    $select.html(html);
}

// ============ Modal Form ============
$('#f_lop').on('change', function(){ loadMonByLop(parseInt(this.value,10), $('#f_mon'), true); });

function openCreate(){
    $('#modalTitle').text('Thêm phân công');
    $('#formPC')[0].reset();
    $('#f_id').val(''); $('#f_force').val('0');
    $('#f_vt').val('1'); $('#f_tt').val('0');
    $('#f_mon').html('<option value="">Phụ trách cả lớp</option>');
    $('#conflictBox').hide().empty();
    $('#btnSubmit').text('Lưu');
    $('#modalForm').addClass('open');
}

function openEdit(id){
    APP.ajax(URL,{action:'getById',id:id}).done(function(res){
        if (!res.success){ APP.toast(res.message,'error'); return; }
        var e = res.data;
        $('#modalTitle').text('Sửa phân công');
        $('#f_id').val(e.id); $('#f_force').val('0');
        $('#f_gv').val(e.giang_vien_id);
        $('#f_lop').val(e.lop_hoc_id);
        $('#f_vt').val(e.vai_tro);
        $('#f_st').val(e.so_tiet_phan_cong||'');
        $('#f_tn').val(e.tu_ngay||''); $('#f_dn').val(e.den_ngay||'');
        $('#f_tt').val(e.trang_thai); $('#f_gc').val(e.ghi_chu||'');
        loadMonByLop(parseInt(e.lop_hoc_id,10), $('#f_mon'), true).then(function(){
            $('#f_mon').val(e.mon_hoc_id||'');
        });
        $('#conflictBox').hide().empty();
        $('#btnSubmit').text('Lưu thay đổi');
        $('#modalForm').addClass('open');
    });
}
function closeModal(){ $('#modalForm').removeClass('open'); }

$('#formPC').on('submit', function(e){
    e.preventDefault();
    var data = $(this).serializeArray();
    data.push({name:'action', value: $('#f_id').val()?'update':'insert'});
    var $btn = $('#btnSubmit').prop('disabled',true);
    APP.ajax(URL, data).done(function(res){
        $btn.prop('disabled',false);
        if (res.success){ APP.toast(res.message,'success'); closeModal(); load(); loadStats(); }
        else if (res.data && res.data.conflicts){ showConflicts(res.data.conflicts, res.message); }
        else APP.toast(res.message,'error');
    }).fail(function(){ $btn.prop('disabled',false); APP.toast('Lỗi kết nối','error'); });
});

function showConflicts(list, msg){
    var html = '<div class="lh-conflict-title">' + ICON_WARNING + ' '+APP.escape(msg)+'</div>';
    html += '<ul class="lh-conflict-list">';
    list.forEach(function(c){
        html += '<li><strong>'+APP.escape(c.ho_ten_gv||'')+'</strong> ('+APP.escape(c.ma_gv||'')+') · '+(c.ten_mon_hoc?APP.escape(c.ma_mon_hoc+' - '+c.ten_mon_hoc):'cả lớp')+' · '+APP.escape(VT_TXT[c.vai_tro]||'')+'</li>';
    });
    html += '</ul>';
    html += '<div class="lh-conflict-actions"><button type="button" class="btn btn-sm btn-warning" onclick="forceSubmit()">Lưu đè (chấp nhận trùng)</button></div>';
    $('#conflictBox').html(html).show();
}
function forceSubmit(){ $('#f_force').val('1'); $('#formPC').trigger('submit'); }

function deleteItem(id){
    APP.confirm('Xóa phân công này?',function(){
        APP.ajax(URL,{action:'delete',id:id}).done(function(res){
            res.success?(APP.toast(res.message,'success'),load(),loadStats()):APP.toast(res.message,'error');
        });
    });
}

// ============ Bulk ============
$('#b_lop').on('change', function(){ renderBulkMon(parseInt(this.value,10)); });

function renderBulkMon(lopId){
    var $w = $('#bMonHocList');
    if (!lopId){ $w.html('<div class="text-muted" style="padding:14px;text-align:center;font-size:13px">Chọn lớp để xem danh sách môn</div>'); return; }
    $w.html('<div style="padding:14px;text-align:center"><span class="spinner"></span></div>');
    APP.ajax(URL,{action:'getMonHocByLop',lop_hoc_id:lopId}).done(function(res){
        if (!res.success) return;
        state.monByLop[lopId] = res.data || [];
        if (!res.data.length){ $w.html('<div class="text-muted" style="padding:14px;text-align:center;font-size:13px">Lớp này chưa liên kết môn nào (qua khóa học)</div>'); return; }
        var html = '';
        res.data.forEach(function(m){
            html += '<label class="lh-chip-check"><input type="checkbox" name="mon_hoc_ids[]" value="'+m.id+'"> <span>'+APP.escape(m.ma_mon_hoc)+' - '+APP.escape(m.ten_mon_hoc)+'</span></label>';
        });
        $w.html(html);
    });
}

function openBulk(){
    $('#formBulk')[0].reset();
    $('#bMonHocList').html('<div class="text-muted" style="padding:14px;text-align:center;font-size:13px">Chọn lớp để xem danh sách môn</div>');
    $('#modalBulk').addClass('open');
}
function closeBulk(){ $('#modalBulk').removeClass('open'); }

$('#formBulk').on('submit', function(e){
    e.preventDefault();
    var data = $(this).serializeArray();
    data.push({name:'action', value:'bulkAssign'});
    APP.ajax(URL, data, {traditional:true}).done(function(res){
        if (res.success){ APP.toast(res.message,'success'); closeBulk(); load(); loadStats(); }
        else APP.toast(res.message,'error');
    });
});

// Init
load(); loadStats();
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
