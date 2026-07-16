<?php
require_once __DIR__ . '/../../bootstrap.php';

Helper::requireLogin();
if (!PhanQuyenHelper::hasQuyen('DT_CME', PhanQuyenHelper::QUYEN_XEM)) {
    echo 'Bạn không có quyền truy cập chức năng này.'; exit;
}
require_once __DIR__ . '/../../BUS/DT_Cme_BUS.php';
$pageTitle = 'Báo cáo CME';
$activeMenu = 'DT_CME_BaoCao';
$namNay = (int)date('Y');
$nguong = DT_Cme_BUS::getNguong();
require __DIR__ . '/../layouts/header.php';
?>
<div class="breadcrumb">
    <a href="<?= AppConfig::baseUrl('GUI/dashboard/index.php') ?>">Trang chủ</a>
    <span class="sep">›</span> Tổng quan
    <span class="sep">›</span> <span>Báo cáo đào tạo YKLT</span>
</div>

<div class="card">
    <div class="bc-tabs">
        <button type="button" class="bc-tab active" data-tab="nv">Theo nhân viên</button>
        <button type="button" class="bc-tab" data-tab="nhom">Theo nhóm hình thức</button>
        <button type="button" class="bc-tab" data-tab="khoa">Theo khoa / phòng</button>
    </div>

    <div class="bc-toolbar">
        <label>Năm:</label>
        <select id="filterNam" class="form-select" style="max-width:120px"></select>
        <span id="khoaWrap" style="display:inline-flex;align-items:center;gap:6px">
            <label>Khoa/phòng:</label>
            <select id="filterKhoa" class="form-select" style="max-width:220px"><option value="0">-- Tất cả --</option></select>
        </span>
        <button type="button" class="btn btn-primary btn-sm" id="btnExport"><?= IconHelper::svg('download','15') ?> Xuất Excel</button>
    </div>

    <div class="table-wrap" id="bcWrap" style="position:relative;min-height:220px">
        <table class="table" id="bcTable">
            <thead id="bcHead"></thead>
            <tbody id="bcBody"></tbody>
        </table>
    </div>
</div>

<!-- Drawer chi tiết bản ghi của 1 nhân viên -->
<div class="drawer-backdrop" id="drawerNv">
    <div class="drawer" style="max-width:640px">
        <div class="drawer-header">
            <div><h3 id="nvTitle" style="margin:0">Chi tiết ghi nhận</h3>
                <div id="nvSub" class="text-muted" style="font-size:12.5px;margin-top:2px"></div></div>
            <button type="button" class="close" onclick="$('#drawerNv').removeClass('open').find('.drawer').removeClass('open')">&times;</button>
        </div>
        <div class="drawer-body" id="nvBody"><div class="hv-pane-loading">Đang tải...</div></div>
    </div>
</div>

<style>
    /* .card không có padding sẵn -> thêm cho các khối con */
    .bc-tabs { display:flex; gap:4px; border-bottom:2px solid var(--gray-200); margin:0 0 14px; padding:14px 20px 0; }
    .bc-tab { border:none; background:none; padding:10px 18px; font-size:14px; font-weight:600; color:var(--gray-500); cursor:pointer; border-bottom:2px solid transparent; margin-bottom:-2px; }
    .bc-tab.active { color:var(--primary); border-bottom-color:var(--primary); }
    .bc-toolbar { display:flex; align-items:center; gap:10px; flex-wrap:wrap; margin-bottom:14px; padding:0 20px; }
    .bc-toolbar label { font-size:13px; font-weight:600; color:var(--gray-600); }
    .cme-gio { font-family:ui-monospace,Menlo,monospace; font-weight:700; color:#d1367f; }
    .bc-badge { display:inline-block; font-size:11.5px; font-weight:700; padding:2px 9px; border-radius:100px; }
    .bc-badge.dat { background:#dcfce7; color:#166534; } .bc-badge.chua { background:#fef3c7; color:#92400e; }
    .bc-nv-row { cursor:pointer; transition:background .12s; }
    .bc-nv-row:hover { background:#f0fdf4; }
    .nvd-sum { display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;
        padding:14px 16px; background:#f8fafc; border:1px solid var(--gray-200); border-radius:10px; margin-bottom:14px; }
    .nvd-sum-n { font-size:24px; font-weight:800; font-family:ui-monospace,Menlo,monospace; color:#16a34a; }
    .nvd-sum-l { font-size:12.5px; color:var(--gray-500); }
    .nvd-table { font-size:13px; } .nvd-table tfoot .nvd-total td { border-top:2px solid var(--gray-300); background:#f8fafc; }
</style>

<script>
var URL = APP_BASE + 'GUI/DT_CME/ajax_handler.php';
var NAM_NAY = <?= $namNay ?>;
var NGUONG_GIO = <?= (float)$nguong['gio'] ?>;
var st = { tab:'nv', nam:NAM_NAY, khoa:0 };
function fmtGio(n){ n=parseFloat(n)||0; return (n%1===0)?n.toFixed(0):n.toFixed(1); }

$('.bc-tab').on('click', function(){
    $('.bc-tab').removeClass('active'); $(this).addClass('active');
    st.tab=$(this).data('tab');
    $('#khoaWrap').toggle(st.tab==='nv');
    loadReport();
});
$('#filterNam').on('change', function(){ st.nam=parseInt(this.value,10)||NAM_NAY; loadReport(); });
$('#filterKhoa').on('change', function(){ st.khoa=parseInt(this.value,10)||0; loadReport(); });
$('#btnExport').on('click', function(){
    var p=new URLSearchParams({loai:st.tab,nam:st.nam||0,khoa_phong_id:st.khoa||0});
    window.location=APP_BASE+'GUI/DT_CME/bao_cao_export.php?'+p.toString();
});

function loadReport(){
    APP.showLoading('#bcWrap');
    if(st.tab==='nhom') return loadNhom();
    if(st.tab==='khoa') return loadKhoa();
    return loadNv();
}

function loadNv(){
    APP.ajax(URL,{action:'baoCaoTheoNhanVien',nam:st.nam,khoa_phong_id:st.khoa}).done(function(res){
        APP.hideLoading('#bcWrap');
        if(!res.success){APP.toast(res.message,'error');return;}
        $('#bcHead').html('<tr><th style="width:44px" class="text-center">#</th><th style="width:130px">Mã NV</th><th>Họ tên</th>'+
            '<th>Khoa / Phòng</th><th style="width:110px" class="text-center">Bản ghi</th>'+
            '<th style="width:140px" class="text-right">Tổng giờ TC</th><th style="width:120px" class="text-center">Đạt ngưỡng</th></tr>');
        var $b=$('#bcBody').empty(); var stt=0;
        if(!res.data.length){ $b.append('<tr><td colspan="7"><div class="empty-state">Chưa có dữ liệu</div></td></tr>'); }
        res.data.forEach(function(r){
            stt++;
            var badge = (parseFloat(r.tong_gio)>=NGUONG_GIO) ? '<span class="bc-badge dat">Đạt</span>' : '<span class="bc-badge chua">Chưa</span>';
            $b.append('<tr class="bc-nv-row" onclick="openNvDetail('+r.nhan_vien_id+',\''+APP.escape(r.ho_ten||'').replace(/\x27/g,"\\\x27")+'\')" title="Xem chi tiết bản ghi">'+
                '<td class="text-center">'+stt+'</td><td><strong>'+APP.escape(r.ma_nv||'')+'</strong></td>'+
                '<td>'+APP.escape(r.ho_ten||'')+'</td><td>'+APP.escape(r.ten_khoa_phong||'-')+'</td>'+
                '<td class="text-center">'+(r.so_ban_ghi||0)+'</td>'+
                '<td class="text-right"><span class="cme-gio">'+fmtGio(r.tong_gio)+'</span></td>'+
                '<td class="text-center">'+badge+'</td></tr>');
        });
    });
}

// Drawer chi tiết bản ghi 1 nhân viên
function openNvDetail(nvId, hoTen){
    $('#nvTitle').text(hoTen||'Chi tiết ghi nhận');
    $('#nvSub').text('Năm '+st.nam);
    $('#nvBody').html('<div class="hv-pane-loading">Đang tải...</div>');
    $('#drawerNv').addClass('open').find('.drawer').addClass('open');
    APP.ajax(URL,{action:'soTheoDoi',nhan_vien_id:nvId,nam:st.nam}).done(function(res){
        if(!res.success){ $('#nvBody').html('<div class="empty-state">'+APP.escape(res.message)+'</div>'); return; }
        var d=res.data, nv=d.nhan_vien;
        $('#nvSub').text((nv.ma_nv||'')+' · Năm '+d.nam);
        var h='';
        h+='<div class="nvd-sum"><div><span class="nvd-sum-n">'+fmtGio(d.tong_gio_nam)+'</span> <span class="nvd-sum-l">giờ tín chỉ năm '+d.nam+'</span></div>';
        h+='<span class="bc-badge '+(parseFloat(d.tong_gio_chu_ky)>=NGUONG_GIO?'dat':'chua')+'">'+(parseFloat(d.tong_gio_chu_ky)>=NGUONG_GIO?'Đạt ngưỡng':'Chưa đạt')+' ('+fmtGio(d.tong_gio_chu_ky)+'/'+fmtGio(NGUONG_GIO)+')</span></div>';

        h+='<table class="table nvd-table"><thead><tr><th style="width:40px" class="text-center">#</th><th>Hoạt động</th>'+
           '<th style="width:140px">Loại</th><th style="width:60px" class="text-center">SL</th>'+
           '<th style="width:90px" class="text-right">Giờ TC</th></tr></thead><tbody>';
        if(!d.hoat_dong.length){ h+='<tr><td colspan="5"><div class="empty-state" style="padding:24px">Không có bản ghi nào</div></td></tr>'; }
        var i=0;
        d.hoat_dong.forEach(function(a){
            i++;
            h+='<tr><td class="text-center">'+i+'</td>'+
               '<td>'+APP.escape(a.ten_hoat_dong||'-')+(a.vai_tro?'<div class="text-muted" style="font-size:11px">'+APP.escape(a.vai_tro)+'</div>':'')+'</td>'+
               '<td>'+APP.escape(a.ten_loai||'-')+'</td>'+
               '<td class="text-center">'+fmtGio(a.so_luong)+'</td>'+
               '<td class="text-right"><span class="cme-gio">'+fmtGio(a.gio_tin_chi)+'</span></td></tr>';
        });
        h+='</tbody><tfoot><tr class="nvd-total"><td colspan="4" class="text-right"><strong>Tổng cộng</strong></td>'+
           '<td class="text-right"><strong class="cme-gio">'+fmtGio(d.tong_gio_nam)+'</strong></td></tr></tfoot></table>';
        $('#nvBody').html(h);
    });
}
function loadNhom(){
    APP.ajax(URL,{action:'baoCaoTheoNhom',nam:st.nam}).done(function(res){
        APP.hideLoading('#bcWrap');
        if(!res.success){APP.toast(res.message,'error');return;}
        $('#bcHead').html('<tr><th style="width:44px" class="text-center">#</th><th>Nhóm hình thức</th>'+
            '<th style="width:130px" class="text-center">Số bản ghi</th><th style="width:160px" class="text-right">Tổng giờ TC</th></tr>');
        var $b=$('#bcBody').empty(); var stt=0;
        if(!res.data.length){ $b.append('<tr><td colspan="4"><div class="empty-state">Chưa có dữ liệu</div></td></tr>'); }
        res.data.forEach(function(r){
            stt++;
            $b.append('<tr><td class="text-center">'+stt+'</td><td>'+APP.escape(r.ten_nhom)+'</td>'+
                '<td class="text-center">'+(r.so_ban_ghi||0)+'</td>'+
                '<td class="text-right"><span class="cme-gio">'+fmtGio(r.tong_gio)+'</span></td></tr>');
        });
    });
}
function loadKhoa(){
    APP.ajax(URL,{action:'baoCaoTheoKhoa',nam:st.nam}).done(function(res){
        APP.hideLoading('#bcWrap');
        if(!res.success){APP.toast(res.message,'error');return;}
        $('#bcHead').html('<tr><th style="width:44px" class="text-center">#</th><th>Khoa / Phòng</th>'+
            '<th style="width:120px" class="text-center">Nhân viên</th><th style="width:110px" class="text-center">Bản ghi</th>'+
            '<th style="width:160px" class="text-right">Tổng giờ TC</th></tr>');
        var $b=$('#bcBody').empty(); var stt=0;
        if(!res.data.length){ $b.append('<tr><td colspan="5"><div class="empty-state">Chưa có dữ liệu</div></td></tr>'); }
        res.data.forEach(function(r){
            stt++;
            $b.append('<tr><td class="text-center">'+stt+'</td><td>'+APP.escape(r.ten_khoa||'(Chưa gán khoa)')+'</td>'+
                '<td class="text-center">'+(r.so_nhan_vien||0)+'</td><td class="text-center">'+(r.so_ban_ghi||0)+'</td>'+
                '<td class="text-right"><span class="cme-gio">'+fmtGio(r.tong_gio)+'</span></td></tr>');
        });
    });
}

function initCombos(){
    APP.ajax(URL,{action:'getNamCombo'}).done(function(r){
        var years=(r.success&&r.data&&r.data.length)?r.data.map(Number):[];
        if(years.indexOf(NAM_NAY)<0) years.unshift(NAM_NAY);
        var $f=$('#filterNam').empty();
        years.forEach(function(y){ $f.append('<option value="'+y+'">'+y+'</option>'); });
        $f.val(st.nam);
    });
    APP.ajax(URL,{action:'getKhoaPhongCombo'}).done(function(r){
        if(!r.success)return; var $f=$('#filterKhoa');
        (r.data||[]).forEach(function(k){ $f.append('<option value="'+k.id+'">'+APP.escape(k.ten_khoa)+'</option>'); });
    });
}

initCombos(); loadReport();
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
