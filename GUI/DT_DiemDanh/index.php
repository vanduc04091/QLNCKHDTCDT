<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../BUS/DT_KhoaHocChuongTrinh_BUS.php';

Helper::requireLogin();
if (!PhanQuyenHelper::hasQuyen('DT_DiemDanh', PhanQuyenHelper::QUYEN_XEM)) {
    echo 'Bạn không có quyền truy cập chức năng này.'; exit;
}
$canEdit = PhanQuyenHelper::hasQuyen('DT_DiemDanh', PhanQuyenHelper::QUYEN_SUA);

$lopList = DT_KhoaHocChuongTrinh_BUS::getCombo();

$pageTitle = 'Điểm danh';
$activeMenu = 'DT_DiemDanh';
$avatarUrl = AppConfig::baseUrl('assets/uploads/hocvien/');
require __DIR__ . '/../layouts/header.php';
?>
<div class="breadcrumb">
    <a href="<?= AppConfig::baseUrl('GUI/dashboard/index.php') ?>">Trang chủ</a>
    <span class="sep">›</span> Đào tạo
    <span class="sep">›</span> <span>Điểm danh</span>
</div>

<!-- Stats (cập nhật theo buổi đang chọn) -->
<div class="hv-stats">
    <div class="hv-stat">
        <div class="hv-stat-icon hv-stat-blue">
            <?= IconHelper::svg('users', '22') ?>
        </div>
        <div><div class="hv-stat-label">Tổng học viên</div><div class="hv-stat-value" id="stTotal">—</div></div>
    </div>
    <div class="hv-stat">
        <div class="hv-stat-icon hv-stat-green">
            <?= IconHelper::svg('check', '22') ?>
        </div>
        <div><div class="hv-stat-label">Có mặt</div><div class="hv-stat-value" id="stPresent">—</div></div>
    </div>
    <div class="hv-stat">
        <div class="hv-stat-icon hv-stat-orange">
            <?= IconHelper::svg('clock', '22') ?>
        </div>
        <div><div class="hv-stat-label">Muộn / Có phép</div><div class="hv-stat-value" id="stLate">—</div></div>
    </div>
    <div class="hv-stat">
        <div class="hv-stat-icon hv-stat-purple">
            <?= IconHelper::svg('x', '22') ?>
        </div>
        <div><div class="hv-stat-label">Vắng</div><div class="hv-stat-value" id="stAbsent">—</div></div>
    </div>
</div>

<!-- Layout 2 cột: bên trái danh sách buổi, bên phải danh sách học viên -->
<div class="dd-layout">
    <div class="card dd-sidebar">
        <div class="dd-sidebar-header">
            <div class="form-group" style="margin:0">
                <label>Chương trình đào tạo</label>
                <select id="fLop" class="form-select">
                    <option value="">-- Chọn chương trình để xem các buổi --</option>
                    <?php foreach ($lopList as $l): ?>
                        <option value="<?= $l['id'] ?>"><?= Helper::h($l['label']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="dd-lich-list" id="lichList">
            <div class="empty-state" style="padding:30px 16px;font-size:13px">
                <div class="icon"><?= IconHelper::svg('calendar', '36') ?></div>
                Chọn một lớp để bắt đầu
            </div>
        </div>
    </div>

    <div class="card dd-main">
        <div class="dd-main-header" id="sessionHeader" style="display:none">
            <div>
                <h3 id="sessionTitle" style="margin:0 0 2px;font-size:16px">—</h3>
                <div class="text-muted" id="sessionMeta" style="font-size:12.5px"></div>
            </div>
            <div class="dd-actions">
                <input type="text" id="fSearch" class="form-control" placeholder="Tìm học viên..." style="max-width:220px">
                <?php if ($canEdit): ?>
                <button type="button" class="btn" id="btnAllPresent" title="Đánh dấu tất cả có mặt">
                    <?= IconHelper::svg('check', '16') ?>
                    Tất cả có mặt
                </button>
                <button type="button" class="btn btn-primary" id="btnSave">Lưu điểm danh</button>
                <?php endif; ?>
            </div>
        </div>

        <div class="dd-progress" id="ddProgress" style="display:none">
            <div class="dd-bar"><div class="dd-bar-fill" id="ddBarFill" style="width:0%"></div></div>
            <div class="dd-progress-text" id="ddProgressText">—</div>
        </div>

        <div class="dd-grid" id="ddGrid"></div>
    </div>
</div>

<!-- Drawer lịch sử điểm danh của 1 học viên -->
<div class="drawer-backdrop" id="drawerHist">
    <div class="drawer">
        <div class="drawer-header">
            <div>
                <h3 id="histTitle" style="margin:0">Lịch sử điểm danh</h3>
                <div id="histSubtitle" class="text-muted" style="font-size:12.5px;margin-top:2px"></div>
            </div>
            <button type="button" class="close" onclick="closeHist()">&times;</button>
        </div>
        <div class="drawer-body" id="histBody"></div>
    </div>
</div>

<script>
var URL = APP_BASE + 'GUI/DT_DiemDanh/ajax_handler.php';
var AVATAR_URL = <?= json_encode($avatarUrl) ?>;
var CAN_EDIT = <?= $canEdit?'true':'false' ?>;
var ICON_CHECK = '<?= addslashes(IconHelper::svg('check', '11')) ?>';
var ICON_CLOCK = '<?= addslashes(IconHelper::svg('clock', '14')) ?>';
var ICON_CALENDAR = '<?= addslashes(IconHelper::svg('calendar', '36')) ?>';
var ICON_USERS = '<?= addslashes(IconHelper::svg('users', '40')) ?>';
var state = { lopId: 0, lichId: 0, items: [], search: '' };
var WD_VI = ['CN','T2','T3','T4','T5','T6','T7'];

// ================== Helpers ==================
function initials(name){ if(!name) return '?'; var p=name.trim().split(/\s+/); return p.length===1?p[0].substr(0,2).toUpperCase():(p[p.length-2][0]+p[p.length-1][0]).toUpperCase(); }
function colorFromName(name){ var c=['#2563eb','#7c3aed','#db2777','#dc2626','#ea580c','#d97706','#16a34a','#0891b2','#4f46e5','#0284c7']; var h=0; for(var i=0;i<(name||'').length;i++) h=(h*31+name.charCodeAt(i))&0xffff; return c[h%c.length]; }
function hvAvatar(hv, size){ size=size||40; if (hv.avatar) return '<div class="hv-av" style="width:'+size+'px;height:'+size+'px"><img src="'+AVATAR_URL+APP.escape(hv.avatar)+'"></div>'; return '<div class="hv-av hv-av-initials" style="width:'+size+'px;height:'+size+'px;background:'+colorFromName(hv.ho_ten)+'">'+APP.escape(initials(hv.ho_ten))+'</div>'; }
function parseYmd(s){ if(!s) return null; var p=String(s).substring(0,10).split('-'); return new Date(+p[0],+p[1]-1,+p[2]); }
function statusLabel(tt){ switch(parseInt(tt,10)){case 1:return 'Có mặt';case 2:return 'Muộn';case 3:return 'Có phép';case 0:return 'Vắng';default:return '';} }
function statusCls(tt){ switch(parseInt(tt,10)){case 1:return 'present';case 2:return 'late';case 3:return 'excused';case 0:return 'absent';default:return '';} }

// ================== Lịch của lớp ==================
$('#fLop').on('change', function(){
    state.lopId = parseInt(this.value, 10) || 0;
    state.lichId = 0;
    $('#sessionHeader,#ddProgress').hide();
    $('#ddGrid').empty();
    if (!state.lopId) { $('#lichList').html('<div class="empty-state" style="padding:30px 16px;font-size:13px"><div class="icon">' + ICON_CALENDAR + '</div>Chọn một lớp để bắt đầu</div>'); return; }
    loadLichByLop();
});

function loadLichByLop() {
    APP.showLoading('#lichList');
    APP.ajax(URL, {action:'lichByLop', lop_hoc_id: state.lopId}).done(function(res){
        APP.hideLoading('#lichList');
        if (!res.success) { APP.toast(res.message,'error'); return; }
        renderLichList(res.data);
    });
}

function renderLichList(rows) {
    var $w = $('#lichList').empty();
    if (!rows.length) {
        $w.html('<div class="empty-state" style="padding:30px 16px;font-size:13px"><div class="icon">' + ICON_CALENDAR + '</div>Lớp này chưa có buổi học nào</div>');
        return;
    }
    rows.forEach(function(r){
        var d = parseYmd(r.ngay_hoc);
        var dateCls = ''; var today = new Date(); today.setHours(0,0,0,0);
        if (d && d.getTime() === today.getTime()) dateCls = 'is-today';
        else if (d && d < today) dateCls = 'is-past';
        else dateCls = 'is-future';
        var ttCls = ['plan','done','post','cancel'][parseInt(r.trang_thai,10)] || '';
        $w.append(
            '<button type="button" class="dd-lich-item '+dateCls+'" data-id="'+r.id+'">'+
                '<div class="dd-lich-date"><div class="dd-lich-day">'+d.getDate()+'</div><div class="dd-lich-month">'+WD_VI[d.getDay()]+' · Th'+(d.getMonth()+1)+'</div></div>'+
                '<div class="dd-lich-body">'+
                    '<div class="dd-lich-title">Buổi '+(r.buoi_thu||'?')+': '+APP.escape(r.tieu_de||'')+'</div>'+
                    '<div class="dd-lich-meta">'+(r.gio_bat_dau||'').substring(0,5)+' – '+(r.gio_ket_thuc||'').substring(0,5)+(r.so_diem_danh>0?' · <span class="dd-check-done">' + ICON_CHECK + ' Đã điểm danh</span>':'')+'</div>'+
                '</div>'+
            '</button>'
        );
    });
}

$('#lichList').on('click', '.dd-lich-item', function(){
    var id = parseInt($(this).data('id'), 10);
    $('.dd-lich-item').removeClass('is-active');
    $(this).addClass('is-active');
    openSession(id);
});

// ================== Phiên điểm danh ==================
function openSession(lichId) {
    state.lichId = lichId;
    APP.showLoading('#ddGrid');
    APP.ajax(URL, {action:'openSession', lich_hoc_id: lichId}).done(function(res){
        APP.hideLoading('#ddGrid');
        if (!res.success) { APP.toast(res.message,'error'); return; }
        state.items = res.data.items || [];
        renderSessionHeader(res.data.lich);
        renderGrid();
        updateStats(res.data.stats);
    });
}

function renderSessionHeader(lich) {
    var d = parseYmd(lich.ngay_hoc);
    var dstr = d ? (WD_VI[d.getDay()] + ', ' + d.getDate() + '/' + (d.getMonth()+1) + '/' + d.getFullYear()) : '';
    $('#sessionTitle').text('Buổi ' + (lich.buoi_thu||'?') + ': ' + lich.tieu_de);
    var meta = [];
    if (dstr) meta.push(dstr);
    meta.push((lich.gio_bat_dau||'').substring(0,5) + ' – ' + (lich.gio_ket_thuc||'').substring(0,5));
    if (lich.ten_lop) meta.push(lich.ma_lop + ' · ' + lich.ten_lop);
    if (lich.phong_hoc) meta.push(lich.phong_hoc);
    $('#sessionMeta').text(meta.join(' · '));
    $('#sessionHeader,#ddProgress').css('display','');
}

function renderGrid() {
    var q = (state.search || '').trim().toLowerCase();
    var filtered = q ? state.items.filter(function(it){
        return (it.ho_ten||'').toLowerCase().indexOf(q) >= 0
            || (it.ma_hv||'').toLowerCase().indexOf(q) >= 0;
    }) : state.items;
    var $g = $('#ddGrid').empty();
    if (!filtered.length) {
        $g.html('<div class="empty-state" style="padding:40px 20px"><div class="icon">' + ICON_USERS + '</div>Không có học viên nào</div>');
        return;
    }
    filtered.forEach(function(it){
        var tt = parseInt(it.trang_thai, 10);
        var dtChip = it.ten_doi_tuong ? '<span class="hv-chip hv-chip-purple">'+APP.escape(it.ten_doi_tuong)+'</span>' : '';
        var nvChip = it.la_nhan_vien == 1 ? '<span class="hv-chip hv-chip-blue">NV '+APP.escape(it.ma_nv||'')+'</span>' : '<span class="hv-chip hv-chip-gray">Ngoài</span>';
        var buttons = '';
        [[1,'Có mặt'],[2,'Muộn'],[3,'Có phép'],[0,'Vắng']].forEach(function(o){
            var active = tt === o[0] ? ' is-active' : '';
            buttons += '<button type="button" class="dd-opt dd-opt-'+statusCls(o[0])+active+'" data-tt="'+o[0]+'" '+(CAN_EDIT?'':'disabled')+'>'+APP.escape(o[1])+'</button>';
        });
        $g.append(
            '<div class="dd-card dd-card-'+statusCls(tt)+'" data-hvl="'+it.hoc_vien_lop_id+'">'+
                '<div class="dd-card-head">'+
                    hvAvatar(it, 44) +
                    '<div class="dd-card-info">'+
                        '<div class="dd-card-name">'+APP.escape(it.ho_ten)+'</div>'+
                        '<div class="dd-card-meta">'+APP.escape(it.ma_hv||'')+'</div>'+
                        '<div class="hv-chips">'+nvChip+' '+dtChip+'</div>'+
                    '</div>'+
                    '<button type="button" class="btn btn-sm dd-card-hist" title="Lịch sử" onclick="openHist('+it.hoc_vien_lop_id+",'"+APP.escape(it.ho_ten)+"','"+APP.escape(it.ma_hv||'')+'\')">' + ICON_CLOCK + '</button>'+
                '</div>'+
                '<div class="dd-card-opts">'+buttons+'</div>'+
            '</div>'
        );
    });
    recalcProgress();
}

$('#ddGrid').on('click', '.dd-opt', function(){
    if (!CAN_EDIT) return;
    var $b = $(this);
    var tt = parseInt($b.data('tt'), 10);
    var $card = $b.closest('.dd-card');
    var hvl = parseInt($card.data('hvl'), 10);
    // update state
    state.items.forEach(function(it){ if (parseInt(it.hoc_vien_lop_id,10)===hvl) it.trang_thai = tt; });
    // update UI
    $card.attr('class', 'dd-card dd-card-'+statusCls(tt));
    $card.find('.dd-opt').removeClass('is-active');
    $b.addClass('is-active');
    recalcProgress();
});

$('#fSearch').on('input', APP.debounce(function(){ state.search = $(this).val(); renderGrid(); }, 200));

$('#btnAllPresent').on('click', function(){
    state.items.forEach(function(it){ it.trang_thai = 1; });
    renderGrid();
});

$('#btnSave').on('click', function(){
    if (!state.lichId) return;
    var payload = { action:'saveBulk', lich_hoc_id: state.lichId };
    var items = state.items.map(function(it, i){
        return {
            hoc_vien_lop_id: it.hoc_vien_lop_id,
            trang_thai: it.trang_thai,
            gio_vao: it.gio_vao || '',
            gio_ra: it.gio_ra || '',
            ghi_chu: it.ghi_chu || '',
        };
    });
    // Phải gửi qua $_POST['items'][i][...]
    var form = new FormData();
    form.append('action', 'saveBulk');
    form.append('lich_hoc_id', state.lichId);
    items.forEach(function(it, i){
        Object.keys(it).forEach(function(k){
            form.append('items['+i+']['+k+']', it[k] == null ? '' : it[k]);
        });
    });
    var $b = $(this).prop('disabled', true).text('Đang lưu...');
    $.ajax({ url: URL, type:'POST', data: form, processData:false, contentType:false, dataType:'json', headers: window.CSRF_TOKEN ? {'X-CSRF-Token': window.CSRF_TOKEN} : {} })
        .done(function(res){
            $b.prop('disabled', false).text('Lưu điểm danh');
            if (res.success) { APP.toast(res.message,'success'); loadLichByLop(); }
            else APP.toast(res.message || 'Lỗi','error');
        })
        .fail(function(){
            $b.prop('disabled', false).text('Lưu điểm danh');
            APP.toast('Lỗi kết nối','error');
        });
});

function updateStats(s) {
    $('#stTotal').text(s.total || 0);
    $('#stPresent').text(s.co_mat || 0);
    $('#stLate').text((parseInt(s.muon,10)||0) + (parseInt(s.vang_phep,10)||0));
    $('#stAbsent').text(s.vang || 0);
}

function recalcProgress() {
    var total = state.items.length;
    var present = 0, late = 0, excused = 0, absent = 0;
    state.items.forEach(function(it){
        var t = parseInt(it.trang_thai,10);
        if (t===1) present++; else if (t===2) late++; else if (t===3) excused++; else if (t===0) absent++;
    });
    updateStats({total:total, co_mat:present, muon:late, vang_phep:excused, vang:absent});
    var pct = total ? Math.round(((present+late)/total)*100) : 0;
    $('#ddBarFill').css('width', pct + '%');
    $('#ddProgressText').text(present+' có mặt + '+late+' muộn / '+total+' · '+pct+'% tham gia');
}

// ================== History drawer ==================
function openHist(hvlId, hoTen, maHv) {
    $('#drawerHist').addClass('open').find('.drawer').addClass('open');
    $('#histTitle').text(hoTen);
    $('#histSubtitle').text(maHv);
    $('#histBody').html('<div style="padding:30px;text-align:center;color:var(--gray-500)">Đang tải...</div>');
    APP.ajax(URL, {action:'historyByHvl', hvl_id: hvlId}).done(function(res){
        if (!res.success) { $('#histBody').html('<div style="padding:20px;color:#b91c1c">'+APP.escape(res.message||'')+'</div>'); return; }
        renderHist(res.data);
    });
}
function closeHist(){ $('#drawerHist').removeClass('open').find('.drawer').removeClass('open'); }

function renderHist(data) {
    var s = data.stats;
    var total = parseInt(s.total,10) || 0;
    var pct = total ? Math.round(((parseInt(s.co_mat,10)+parseInt(s.muon,10))/total)*100) : 0;
    var html = '<div class="dd-hist-summary">';
    html += '<div class="dd-hist-stat"><span class="dd-dot dd-dot-present"></span>Có mặt<strong>'+(s.co_mat||0)+'</strong></div>';
    html += '<div class="dd-hist-stat"><span class="dd-dot dd-dot-late"></span>Muộn<strong>'+(s.muon||0)+'</strong></div>';
    html += '<div class="dd-hist-stat"><span class="dd-dot dd-dot-excused"></span>Có phép<strong>'+(s.vang_phep||0)+'</strong></div>';
    html += '<div class="dd-hist-stat"><span class="dd-dot dd-dot-absent"></span>Vắng<strong>'+(s.vang||0)+'</strong></div>';
    html += '</div>';
    html += '<div class="dd-hist-rate">Tỉ lệ tham gia: <strong>'+pct+'%</strong></div>';

    html += '<div class="dd-hist-list">';
    (data.items||[]).forEach(function(it){
        var d = parseYmd(it.ngay_hoc);
        var dstr = d ? (d.getDate()+'/'+(d.getMonth()+1)+'/'+d.getFullYear()) : '';
        html += '<div class="dd-hist-row">';
        html += '<div class="dd-hist-date">'+dstr+'<div class="dd-hist-dow">'+(d?WD_VI[d.getDay()]:'')+'</div></div>';
        html += '<div class="dd-hist-info">';
        html += '<div class="dd-hist-title">Buổi '+(it.buoi_thu||'?')+': '+APP.escape(it.tieu_de_buoi||'')+'</div>';
        html += '<div class="dd-hist-time">'+(it.gio_bat_dau_buoi||'').substring(0,5)+' – '+(it.gio_ket_thuc_buoi||'').substring(0,5)+'</div>';
        html += '</div>';
        html += '<div class="dd-hist-status dd-hist-status-'+statusCls(it.trang_thai)+'">'+statusLabel(it.trang_thai)+'</div>';
        html += '</div>';
    });
    html += '</div>';
    if (!(data.items||[]).length) html = '<div style="padding:20px;text-align:center;color:var(--gray-500)">Chưa có dữ liệu điểm danh</div>' + html;

    $('#histBody').html(html);
}
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
