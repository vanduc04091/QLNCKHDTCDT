<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../BUS/DT_LopHoc_BUS.php';

Helper::requireLogin();
if (!PhanQuyenHelper::hasQuyen('DT_KetQuaHocTap', PhanQuyenHelper::QUYEN_XEM)) {
    echo 'Bạn không có quyền truy cập chức năng này.'; exit;
}
$canEdit = PhanQuyenHelper::hasQuyen('DT_KetQuaHocTap', PhanQuyenHelper::QUYEN_SUA);

$lopList = DT_LopHoc_BUS::getPaged(1, 500, '', 0, 0, -1)['data'];

$pageTitle = 'Kết quả học tập';
$activeMenu = 'DT_KetQuaHocTap';
$avatarUrl = AppConfig::baseUrl('assets/uploads/hocvien/');
require __DIR__ . '/../layouts/header.php';
?>
<div class="breadcrumb">
    <a href="<?= AppConfig::baseUrl('GUI/dashboard/index.php') ?>">Trang chủ</a>
    <span class="sep">›</span> Đào tạo
    <span class="sep">›</span> <span>Kết quả học tập</span>
</div>

<!-- Stats -->
<div class="hv-stats">
    <div class="hv-stat">
        <div class="hv-stat-icon hv-stat-blue">
            <?= IconHelper::svg('users', '22') ?>
        </div>
        <div><div class="hv-stat-label">Học viên</div><div class="hv-stat-value" id="stHv">—</div></div>
    </div>
    <div class="hv-stat">
        <div class="hv-stat-icon hv-stat-green">
            <?= IconHelper::svg('check', '22') ?>
        </div>
        <div><div class="hv-stat-label">Tỉ lệ đạt</div><div class="hv-stat-value" id="stDat">—</div></div>
    </div>
    <div class="hv-stat">
        <div class="hv-stat-icon hv-stat-purple">
            <?= IconHelper::svg('bar-chart', '22') ?>
        </div>
        <div><div class="hv-stat-label">Điểm TB lớp</div><div class="hv-stat-value" id="stAvg">—</div></div>
    </div>
    <div class="hv-stat">
        <div class="hv-stat-icon hv-stat-orange">
            <?= IconHelper::svg('star', '22') ?>
        </div>
        <div><div class="hv-stat-label">Xuất sắc / Giỏi</div><div class="hv-stat-value" id="stTop">—</div></div>
    </div>
</div>

<div class="card">
    <div class="kq-toolbar">
        <div class="kq-toolbar-left">
            <div class="form-group" style="margin:0;min-width:280px">
                <label>Lớp học</label>
                <select id="fLop" class="form-select">
                    <option value="">-- Chọn lớp --</option>
                    <?php foreach ($lopList as $l): ?>
                        <option value="<?= $l['id'] ?>"><?= Helper::h($l['ma_lop'] . ' - ' . $l['ten_lop']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group" style="margin:0;min-width:180px">
                <label>Lọc xếp loại</label>
                <select id="fXL" class="form-select">
                    <option value="">Tất cả</option>
                    <option value="Xuất sắc">Xuất sắc</option>
                    <option value="Giỏi">Giỏi</option>
                    <option value="Khá">Khá</option>
                    <option value="Trung bình">Trung bình</option>
                    <option value="Yếu">Yếu</option>
                    <option value="Kém">Kém</option>
                </select>
            </div>
            <div class="form-group" style="margin:0;min-width:200px">
                <label>Tìm học viên</label>
                <input type="text" id="fSearch" class="form-control" placeholder="Tên hoặc mã HV...">
            </div>
        </div>
        <div class="kq-toolbar-right">
            <?php if ($canEdit): ?>
            <button type="button" class="btn" id="btnRecalc">
                <?= IconHelper::svg('refresh-cw', '16') ?>
                Tính lại tổng kết
            </button>
            <?php endif; ?>
            <button type="button" class="btn" id="btnExport" title="Xuất bảng điểm">
                <?= IconHelper::svg('download', '16') ?>
                Xuất Excel
            </button>
        </div>
    </div>

    <div class="kq-legend">
        <span>Công thức: <strong>Tổng kết = TX×0.2 + GK×0.3 + CK×0.5</strong></span>
        <span class="kq-xl kq-xl-xs">Xuất sắc ≥ 9.0</span>
        <span class="kq-xl kq-xl-gioi">Giỏi ≥ 8.0</span>
        <span class="kq-xl kq-xl-kha">Khá ≥ 6.5</span>
        <span class="kq-xl kq-xl-tb">TB ≥ 5.0</span>
        <span class="kq-xl kq-xl-yeu">Yếu &lt; 5.0</span>
    </div>

    <div class="table-wrap" id="kqWrap" style="position:relative;min-height:240px;overflow-x:auto">
        <table class="table kq-table" id="kqTable">
            <thead>
                <tr id="kqThead"></tr>
            </thead>
            <tbody id="kqTbody"></tbody>
        </table>
    </div>
</div>

<!-- Modal nhập điểm 1 HV-môn -->
<div class="modal-backdrop" id="modalScore">
    <div class="modal" style="max-width:520px">
        <div class="modal-header">
            <h3 id="msTitle">Nhập điểm</h3>
            <button type="button" class="close" onclick="closeScore()">&times;</button>
        </div>
        <form id="formScore">
            <div class="modal-body">
                <input type="hidden" name="hoc_vien_lop_id" id="ms_hvl">
                <input type="hidden" name="mon_hoc_id" id="ms_mon">

                <div id="ms_summary" style="display:flex;gap:12px;align-items:center;padding:12px;background:var(--gray-50);border-radius:var(--radius);margin-bottom:14px"></div>

                <div class="form-row-3">
                    <div class="form-group">
                        <label>Thường xuyên <small style="color:var(--gray-500)">(20%)</small></label>
                        <input type="number" name="diem_thuong_xuyen" id="ms_tx" class="form-control" min="0" max="10" step="0.1">
                    </div>
                    <div class="form-group">
                        <label>Giữa kỳ <small style="color:var(--gray-500)">(30%)</small></label>
                        <input type="number" name="diem_giua_ky" id="ms_gk" class="form-control" min="0" max="10" step="0.1">
                    </div>
                    <div class="form-group">
                        <label>Cuối kỳ <small style="color:var(--gray-500)">(50%)</small></label>
                        <input type="number" name="diem_cuoi_ky" id="ms_ck" class="form-control" min="0" max="10" step="0.1">
                    </div>
                </div>

                <div class="kq-preview" id="msPreview">
                    <div class="kq-preview-label">Tổng kết</div>
                    <div class="kq-preview-value" id="msPreviewVal">—</div>
                    <div class="kq-preview-xl" id="msPreviewXl"></div>
                </div>

                <div class="form-group">
                    <label>Nhận xét</label>
                    <textarea name="nhan_xet" id="ms_nx" class="form-control" rows="2" maxlength="500"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn" onclick="closeScore()">Hủy</button>
                <button type="submit" class="btn btn-primary">Lưu điểm</button>
            </div>
        </form>
    </div>
</div>

<script>
var URL = APP_BASE + 'GUI/DT_KetQuaHocTap/ajax_handler.php';
var AVATAR_URL = <?= json_encode($avatarUrl) ?>;
var CAN_EDIT = <?= $canEdit?'true':'false' ?>;
var ICON_EMPTY = '<?= addslashes(IconHelper::svg('search', '40')) ?>';
var state = { lopId: 0, monHoc: [], rows: [], grouped: {}, filter: { xl:'', search:'' } };

// ============ Helpers ============
function initials(name){ if(!name) return '?'; var p=name.trim().split(/\s+/); return p.length===1?p[0].substr(0,2).toUpperCase():(p[p.length-2][0]+p[p.length-1][0]).toUpperCase(); }
function colorFromName(name){ var c=['#2563eb','#7c3aed','#db2777','#dc2626','#ea580c','#d97706','#16a34a','#0891b2','#4f46e5','#0284c7']; var h=0; for(var i=0;i<(name||'').length;i++) h=(h*31+name.charCodeAt(i))&0xffff; return c[h%c.length]; }
function avatar(hv){ if (hv.avatar) return '<div class="hv-av" style="width:32px;height:32px"><img src="'+AVATAR_URL+APP.escape(hv.avatar)+'"></div>'; return '<div class="hv-av hv-av-initials" style="width:32px;height:32px;background:'+colorFromName(hv.ho_ten)+';font-size:11.5px">'+APP.escape(initials(hv.ho_ten))+'</div>'; }
function avatarLg(hv){ if (hv.avatar) return '<div class="hv-av" style="width:44px;height:44px"><img src="'+AVATAR_URL+APP.escape(hv.avatar)+'"></div>'; return '<div class="hv-av hv-av-initials" style="width:44px;height:44px;background:'+colorFromName(hv.ho_ten)+'">'+APP.escape(initials(hv.ho_ten))+'</div>'; }

function xlClass(xl){
    switch(xl){
        case 'Xuất sắc': return 'xs';
        case 'Giỏi': return 'gioi';
        case 'Khá': return 'kha';
        case 'Trung bình': return 'tb';
        case 'Yếu': case 'Kém': return 'yeu';
        default: return 'na';
    }
}
function fmtDiem(d){ return (d===null||d===undefined||d==='') ? '' : parseFloat(d).toFixed(1); }
function computeTongKet(tx, gk, ck){
    var W={tx:0.2, gk:0.3, ck:0.5}; var sum=0, w=0;
    function add(v,k){ if(v!==null && v!=='' && !isNaN(v)){ sum+=parseFloat(v)*W[k]; w+=W[k]; } }
    add(tx,'tx'); add(gk,'gk'); add(ck,'ck');
    if (w<=0) return null;
    return Math.round((sum/w)*10)/10;
}
function xepLoai(d){
    if (d>=9) return 'Xuất sắc'; if (d>=8) return 'Giỏi';
    if (d>=6.5) return 'Khá'; if (d>=5) return 'Trung bình';
    if (d>=3.5) return 'Yếu'; return 'Kém';
}

// ============ Load ============
$('#fLop').on('change', function(){
    state.lopId = parseInt(this.value,10) || 0;
    if (!state.lopId) { resetUI(); return; }
    loadData();
});
$('#fXL,#fSearch').on('input change', APP.debounce(function(){
    state.filter.xl = $('#fXL').val();
    state.filter.search = $('#fSearch').val();
    renderTable();
}, 200));

function resetUI() {
    $('#kqThead').empty(); $('#kqTbody').empty();
    ['#stHv','#stDat','#stAvg','#stTop'].forEach(function(id){ $(id).text('—'); });
}

function loadData() {
    APP.showLoading('#kqWrap');
    APP.ajax(URL, {action:'load', lop_hoc_id: state.lopId}).done(function(res){
        APP.hideLoading('#kqWrap');
        if (!res.success) { APP.toast(res.message,'error'); return; }
        state.monHoc = res.data.mon_hoc || [];
        state.rows = res.data.rows || [];
        groupRows();
        updateStats(res.data.stats);
        renderTable();
    });
}

function groupRows() {
    // Gom theo hoc_vien_lop_id
    var g = {};
    state.rows.forEach(function(r){
        var k = r.hoc_vien_lop_id;
        if (!g[k]) {
            g[k] = {
                hoc_vien_lop_id: k,
                hoc_vien_id: r.hoc_vien_id,
                ma_hv: r.ma_hv, ho_ten: r.ho_ten, avatar: r.avatar,
                diem_lop: r.diem_lop, xep_loai_lop: r.xep_loai_lop,
                scores: {} // mon_hoc_id -> {dtx,dgk,dck,dtk,xl,dat,nhan_xet,kq_id}
            };
        }
        if (r.kq_id) {
            g[k].scores[r.mon_hoc_id || '0'] = {
                kq_id: r.kq_id,
                mon_hoc_id: r.mon_hoc_id,
                dtx: r.diem_thuong_xuyen, dgk: r.diem_giua_ky, dck: r.diem_cuoi_ky,
                dtk: r.diem_tong_ket, xl: r.xep_loai, dat: r.dat, nhan_xet: r.nhan_xet
            };
        }
    });
    state.grouped = g;
}

function updateStats(s) {
    if (!s) return;
    $('#stHv').text(s.so_hoc_vien || 0);
    var total = (parseInt(s.so_dat,10)||0) + (parseInt(s.so_khong_dat,10)||0);
    var pct = total ? Math.round(((parseInt(s.so_dat,10)||0)/total)*100) : 0;
    $('#stDat').text(pct + '%');
    $('#stAvg').text(s.diem_tb ? parseFloat(s.diem_tb).toFixed(1) : '—');
    $('#stTop').text((parseInt(s.xs,10)||0) + (parseInt(s.gioi,10)||0));
}

function renderTable() {
    // Header
    var thead = '<th style="width:44px" class="text-center">#</th><th style="min-width:220px">Học viên</th>';
    if (state.monHoc.length) {
        state.monHoc.forEach(function(m){
            thead += '<th class="text-center kq-th-mon" title="'+APP.escape(m.ten_mon_hoc||'')+'"><div class="kq-mon-code">'+APP.escape(m.ma_mon_hoc||'')+'</div><div class="kq-mon-name">'+APP.escape(m.ten_mon_hoc||'')+'</div></th>';
        });
    } else {
        thead += '<th class="text-center">Điểm</th>';
    }
    thead += '<th class="text-center" style="width:100px">TB lớp</th><th class="text-center" style="width:120px">Xếp loại</th>';
    $('#kqThead').html(thead);

    // Body
    var $tb = $('#kqTbody').empty();
    var list = Object.values(state.grouped);
    // Filter
    var q = (state.filter.search||'').toLowerCase();
    var xlFilter = state.filter.xl;
    list = list.filter(function(g){
        if (q && (g.ho_ten||'').toLowerCase().indexOf(q) < 0 && (g.ma_hv||'').toLowerCase().indexOf(q) < 0) return false;
        if (xlFilter && g.xep_loai_lop !== xlFilter) return false;
        return true;
    });
    list.sort(function(a,b){ return (a.ho_ten||'').localeCompare(b.ho_ten||'', 'vi'); });

    if (!list.length) {
        var colspan = 3 + (state.monHoc.length || 1) + 1;
        $tb.append('<tr><td colspan="'+colspan+'"><div class="empty-state" style="padding:40px"><div class="icon">' + ICON_EMPTY + '</div>Không có dữ liệu</div></td></tr>');
        return;
    }

    list.forEach(function(g, i){
        var tr = '<tr>';
        tr += '<td class="text-center">'+(i+1)+'</td>';
        tr += '<td><div class="kq-hv">'+avatar(g)+'<div><div class="kq-hv-name">'+APP.escape(g.ho_ten||'')+'</div><div class="kq-hv-code">'+APP.escape(g.ma_hv||'')+'</div></div></div></td>';
        if (state.monHoc.length) {
            state.monHoc.forEach(function(m){
                var s = g.scores[m.id];
                tr += cellScore(g.hoc_vien_lop_id, m.id, s);
            });
        } else {
            // Trường hợp lớp không có môn liên kết: 1 cột điểm duy nhất (mon_hoc_id = null)
            var s = g.scores['0'] || g.scores[null];
            tr += cellScore(g.hoc_vien_lop_id, null, s);
        }
        var dtb = g.diem_lop !== null && g.diem_lop !== undefined ? parseFloat(g.diem_lop).toFixed(1) : '—';
        var xl = g.xep_loai_lop || '';
        tr += '<td class="text-center"><strong>'+dtb+'</strong></td>';
        tr += '<td class="text-center">'+(xl?'<span class="kq-badge kq-badge-'+xlClass(xl)+'">'+APP.escape(xl)+'</span>':'—')+'</td>';
        tr += '</tr>';
        $tb.append(tr);
    });
}

function cellScore(hvlId, monId, s) {
    if (s && s.dtk !== null && s.dtk !== undefined && s.dtk !== '') {
        var xl = s.xl || xepLoai(parseFloat(s.dtk));
        return '<td class="text-center kq-cell"><button type="button" class="kq-score kq-score-'+xlClass(xl)+'" onclick="openScore('+hvlId+','+(monId||'null')+')" '+(CAN_EDIT?'':'disabled')+'>'+parseFloat(s.dtk).toFixed(1)+'</button></td>';
    }
    return '<td class="text-center kq-cell"><button type="button" class="kq-score kq-score-empty" onclick="openScore('+hvlId+','+(monId||'null')+')" '+(CAN_EDIT?'':'disabled')+'>+</button></td>';
}

// ============ Modal nhập điểm ============
window.openScore = function(hvlId, monId) {
    var g = state.grouped[hvlId]; if (!g) return;
    var s = g.scores[monId || '0'] || g.scores[monId] || {};
    var mon = state.monHoc.find(function(m){ return m.id == monId; });
    $('#ms_hvl').val(hvlId);
    $('#ms_mon').val(monId || '');
    $('#ms_summary').html(
        avatarLg(g) +
        '<div>'+
            '<div style="font-weight:600">'+APP.escape(g.ho_ten||'')+'</div>'+
            '<div class="text-muted" style="font-size:12.5px">'+APP.escape(g.ma_hv||'')+(mon?' · '+APP.escape(mon.ma_mon_hoc+' - '+mon.ten_mon_hoc):'')+'</div>'+
        '</div>'
    );
    $('#ms_tx').val(s.dtx != null ? s.dtx : '');
    $('#ms_gk').val(s.dgk != null ? s.dgk : '');
    $('#ms_ck').val(s.dck != null ? s.dck : '');
    $('#ms_nx').val(s.nhan_xet || '');
    updatePreview();
    $('#modalScore').addClass('open');
    setTimeout(function(){ $('#ms_tx').focus(); }, 100);
};
function closeScore(){ $('#modalScore').removeClass('open'); }

$('#ms_tx,#ms_gk,#ms_ck').on('input', updatePreview);

function updatePreview() {
    var tx = $('#ms_tx').val(), gk = $('#ms_gk').val(), ck = $('#ms_ck').val();
    var d = computeTongKet(tx, gk, ck);
    if (d === null) { $('#msPreviewVal').text('—'); $('#msPreviewXl').text(''); $('#msPreview').attr('class','kq-preview'); return; }
    $('#msPreviewVal').text(d.toFixed(1));
    var xl = xepLoai(d);
    $('#msPreviewXl').html('<span class="kq-badge kq-badge-'+xlClass(xl)+'">'+xl+'</span>' + (d>=5?' · <span style="color:#16a34a">Đạt</span>':' · <span style="color:#dc2626">Chưa đạt</span>'));
    $('#msPreview').attr('class', 'kq-preview is-filled');
}

$('#formScore').on('submit', function(e){
    e.preventDefault();
    var data = $(this).serializeArray();
    data.push({name:'action', value:'saveOne'});
    APP.ajax(URL, data).done(function(res){
        if (res.success) { APP.toast(res.message,'success'); closeScore(); loadData(); }
        else APP.toast(res.message,'error');
    });
});

// ============ Actions ============
$('#btnRecalc').on('click', function(){
    if (!state.lopId) { APP.toast('Chưa chọn lớp','warning'); return; }
    APP.ajax(URL, {action:'recalc', lop_hoc_id: state.lopId}).done(function(res){
        if (res.success) { APP.toast(res.message,'success'); loadData(); }
        else APP.toast(res.message,'error');
    });
});

$('#btnExport').on('click', function(){
    if (!state.lopId) { APP.toast('Chưa chọn lớp','warning'); return; }
    // Export đơn giản: tạo CSV từ bảng
    var rows = [];
    var header = ['STT','Mã HV','Họ tên'];
    state.monHoc.forEach(function(m){ header.push(m.ma_mon_hoc + ' - ' + m.ten_mon_hoc); });
    header.push('TB lớp', 'Xếp loại');
    rows.push(header);

    Object.values(state.grouped).forEach(function(g, i){
        var row = [i+1, g.ma_hv||'', g.ho_ten||''];
        if (state.monHoc.length) {
            state.monHoc.forEach(function(m){ var s=g.scores[m.id]; row.push(s && s.dtk!=null ? parseFloat(s.dtk).toFixed(1) : ''); });
        } else {
            var s = g.scores['0']; row.push(s && s.dtk!=null ? parseFloat(s.dtk).toFixed(1) : '');
        }
        row.push(g.diem_lop!=null ? parseFloat(g.diem_lop).toFixed(1) : '', g.xep_loai_lop||'');
        rows.push(row);
    });
    var csv = rows.map(function(r){ return r.map(function(c){ c=String(c||''); return c.indexOf(',')>=0||c.indexOf('"')>=0||c.indexOf('\n')>=0?'"'+c.replace(/"/g,'""')+'"':c; }).join(','); }).join('\n');
    var blob = new Blob(['﻿'+csv], {type:'text/csv;charset=utf-8;'});
    var a = document.createElement('a');
    a.href = window.URL.createObjectURL(blob);
    a.download = 'bang-diem-lop-'+state.lopId+'.csv';
    document.body.appendChild(a); a.click(); document.body.removeChild(a);
});
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
