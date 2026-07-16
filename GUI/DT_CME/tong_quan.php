<?php
require_once __DIR__ . '/../../bootstrap.php';

Helper::requireLogin();
if (!PhanQuyenHelper::hasQuyen('DT_CME', PhanQuyenHelper::QUYEN_XEM)) {
    echo 'Bạn không có quyền truy cập chức năng này.'; exit;
}
$pageTitle = 'Tổng quan CME';
$activeMenu = 'DT_CME_TongQuan';
$namNay = (int)date('Y');
require __DIR__ . '/../layouts/header.php';
?>
<div class="breadcrumb">
    <a href="<?= AppConfig::baseUrl('GUI/dashboard/index.php') ?>">Trang chủ</a>
    <span class="sep">›</span> Tổng quan
    <span class="sep">›</span> <span>Đào tạo y khoa liên tục</span>
</div>

<div class="card" style="margin-bottom:16px">
    <div class="toolbar">
        <div class="left">
            <label style="font-weight:600;font-size:13px;color:var(--gray-600);margin-right:6px">Năm:</label>
            <select id="filterNam" class="form-select" style="max-width:120px"></select>
        </div>
        <div class="right">
            <a class="btn" href="<?= AppConfig::baseUrl('GUI/DT_CME/index.php') ?>"><?= IconHelper::svg('bar-chart','16') ?> Sổ theo dõi</a>
            <a class="btn" href="<?= AppConfig::baseUrl('GUI/DT_CME/bao_cao.php') ?>"><?= IconHelper::svg('clipboard-list','16') ?> Báo cáo</a>
        </div>
    </div>
</div>

<div class="cme-kpis" id="kpis">
    <div class="cme-kpi kpi-a"><div class="ic"><?= IconHelper::svg('trending-up','22') ?></div><div><div class="n" id="kTong">0</div><div class="l">Tổng giờ tín chỉ</div></div></div>
    <div class="cme-kpi kpi-b"><div class="ic"><?= IconHelper::svg('user','22') ?></div><div><div class="n" id="kNv">0</div><div class="l">Nhân viên có ghi nhận</div></div></div>
    <div class="cme-kpi kpi-c"><div class="ic"><?= IconHelper::svg('clipboard-list','22') ?></div><div><div class="n" id="kBanGhi">0</div><div class="l">Số bản ghi</div></div></div>
    <div class="cme-kpi kpi-d"><div class="ic"><?= IconHelper::svg('check','22') ?></div><div><div class="n" id="kDat">0</div><div class="l" id="kDatLbl">Đạt ngưỡng (chu kỳ)</div></div></div>
</div>

<div class="cme-grid2">
    <div class="card">
        <div class="card-title">Theo nhóm hình thức</div>
        <div id="chartNhom" class="cme-bars"><div class="hv-pane-loading">Đang tải...</div></div>
    </div>
    <div class="card">
        <div class="card-title">Top nhân viên (giờ tín chỉ)</div>
        <div id="topNv"><div class="hv-pane-loading">Đang tải...</div></div>
    </div>
</div>

<div class="card">
    <div class="card-title">Theo khoa / phòng</div>
    <div class="table-wrap">
        <table class="table">
            <thead><tr><th style="width:44px" class="text-center">#</th><th>Khoa / Phòng</th>
                <th style="width:130px" class="text-center">Nhân viên</th>
                <th style="width:120px" class="text-center">Bản ghi</th>
                <th style="width:150px" class="text-right">Tổng giờ TC</th></tr></thead>
            <tbody id="khoaTbody"></tbody>
        </table>
    </div>
</div>

<style>
    .cme-kpis { display:grid; grid-template-columns:repeat(auto-fit,minmax(220px,1fr)); gap:14px; margin-bottom:16px; }
    .cme-kpi { background:#fff; border:1px solid var(--gray-200); border-radius:12px; padding:18px; display:flex; gap:14px; align-items:center; }
    .cme-kpi .ic { width:46px; height:46px; border-radius:12px; display:grid; place-items:center; color:#fff; flex:0 0 auto; }
    .cme-kpi.kpi-a .ic { background:#16a34a; } .cme-kpi.kpi-b .ic { background:#2563eb; }
    .cme-kpi.kpi-c .ic { background:#d1367f; } .cme-kpi.kpi-d .ic { background:#b7791f; }
    .cme-kpi .n { font-size:26px; font-weight:800; font-family:ui-monospace,Menlo,monospace; line-height:1; color:var(--gray-800); }
    .cme-kpi .l { font-size:12.5px; color:var(--gray-500); margin-top:5px; }
    .cme-grid2 { display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:16px; }
    @media (max-width:900px){ .cme-grid2 { grid-template-columns:1fr; } }
    /* .card không có padding sẵn -> tự thêm cho nội dung CME */
    .card-title { font-size:14px; font-weight:700; color:var(--gray-700); padding:16px 20px 0; margin:0 0 14px; }
    .cme-bars, #topNv { padding:0 20px 18px; }
    .card > .table-wrap { margin:0; }
    .cme-bars { display:flex; flex-direction:column; gap:10px; }
    .cme-bar-row .top { display:flex; justify-content:space-between; font-size:12.5px; margin-bottom:4px; }
    .cme-bar-row .top .g { font-family:ui-monospace,Menlo,monospace; font-weight:700; color:#0f7a38; }
    .cme-bar-track { height:9px; background:var(--gray-100); border-radius:6px; overflow:hidden; }
    .cme-bar-track > span { display:block; height:100%; background:linear-gradient(90deg,#16a34a,#0f766e); border-radius:6px; }
    .top-nv-row { display:flex; align-items:center; gap:12px; padding:9px 0; border-bottom:1px solid var(--gray-100); }
    .top-nv-row:last-child { border-bottom:none; }
    .top-nv-rank { width:26px; height:26px; border-radius:8px; background:var(--gray-100); color:var(--gray-600); font-weight:700; font-size:12.5px; display:grid; place-items:center; flex:0 0 auto; }
    .top-nv-row:nth-child(1) .top-nv-rank { background:#fef3c7; color:#b7791f; }
    .top-nv-info { flex:1; min-width:0; } .top-nv-info .nm { font-weight:600; font-size:13.5px; }
    .top-nv-info .kp { font-size:11.5px; color:var(--gray-500); }
    .top-nv-gio { font-family:ui-monospace,Menlo,monospace; font-weight:700; color:#d1367f; }
    .cme-gio { font-family:ui-monospace,Menlo,monospace; font-weight:700; color:#d1367f; }
</style>

<script>
var URL = APP_BASE + 'GUI/DT_CME/ajax_handler.php';
var NAM_NAY = <?= $namNay ?>;
var curNam = NAM_NAY;
function fmtGio(n){ n=parseFloat(n)||0; return (n%1===0)?n.toFixed(0):n.toFixed(1); }

function loadNamCombo(){
    APP.ajax(URL,{action:'getNamCombo'}).done(function(r){
        var years=(r.success&&r.data&&r.data.length)?r.data.map(Number):[];
        if(years.indexOf(NAM_NAY)<0) years.unshift(NAM_NAY);
        var $f=$('#filterNam').empty();
        years.forEach(function(y){ $f.append('<option value="'+y+'">'+y+'</option>'); });
        $f.val(curNam);
    });
}
$('#filterNam').on('change', function(){ curNam=parseInt(this.value,10)||NAM_NAY; loadAll(); });

function loadAll(){
    APP.ajax(URL,{action:'tongQuan',nam:curNam}).done(function(res){
        if(!res.success){ APP.toast(res.message,'error'); return; }
        var d=res.data;
        // KPI
        $('#kTong').text(fmtGio(d.stats.tong_gio));
        $('#kNv').text(d.stats.so_nhan_vien||0);
        $('#kBanGhi').text(d.stats.so_ban_ghi||0);
        $('#kDat').text(d.so_nv_dat+' / '+d.so_nv_chu_ky);
        $('#kDatLbl').text('Đạt '+fmtGio(d.nguong.gio)+' giờ (chu kỳ '+d.tu_nam+'–'+d.nam+')');

        // Theo nhóm — bar
        var maxG=0; d.theo_nhom.forEach(function(n){ if(+n.tong_gio>maxG) maxG=+n.tong_gio; });
        var $c=$('#chartNhom').empty();
        if(!d.theo_nhom.length){ $c.html('<div class="text-muted" style="font-size:13px">Chưa có dữ liệu.</div>'); }
        d.theo_nhom.forEach(function(n){
            var pct=maxG>0?Math.round(n.tong_gio/maxG*100):0;
            $c.append('<div class="cme-bar-row"><div class="top"><span>'+APP.escape(n.ten_nhom)+' ('+n.so_ban_ghi+')</span>'+
                '<span class="g">'+fmtGio(n.tong_gio)+' giờ</span></div>'+
                '<div class="cme-bar-track"><span style="width:'+pct+'%"></span></div></div>');
        });

        // Top NV
        var $t=$('#topNv').empty();
        if(!d.top_nv.length){ $t.html('<div class="text-muted" style="font-size:13px">Chưa có dữ liệu.</div>'); }
        d.top_nv.forEach(function(nv,i){
            $t.append('<div class="top-nv-row"><div class="top-nv-rank">'+(i+1)+'</div>'+
                '<div class="top-nv-info"><div class="nm">'+APP.escape(nv.ho_ten)+'</div>'+
                '<div class="kp">'+APP.escape(nv.ten_khoa_phong||'-')+'</div></div>'+
                '<div class="top-nv-gio">'+fmtGio(nv.tong_gio)+' giờ</div></div>');
        });

        // Theo khoa
        var $k=$('#khoaTbody').empty(); var stt=0;
        if(!d.theo_khoa.length){ $k.append('<tr><td colspan="5"><div class="empty-state">Chưa có dữ liệu</div></td></tr>'); }
        d.theo_khoa.forEach(function(k){
            stt++;
            $k.append('<tr><td class="text-center">'+stt+'</td><td>'+APP.escape(k.ten_khoa||'(Chưa gán khoa)')+'</td>'+
                '<td class="text-center">'+(k.so_nhan_vien||0)+'</td><td class="text-center">'+(k.so_ban_ghi||0)+'</td>'+
                '<td class="text-right"><span class="cme-gio">'+fmtGio(k.tong_gio)+'</span></td></tr>');
        });
    });
}

loadNamCombo(); loadAll();
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
