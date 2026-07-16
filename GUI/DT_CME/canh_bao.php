<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../BUS/DT_Cme_BUS.php';

Helper::requireLogin();
if (!PhanQuyenHelper::hasQuyen('DT_CME', PhanQuyenHelper::QUYEN_XEM)) {
    echo 'Bạn không có quyền truy cập chức năng này.'; exit;
}
$pageTitle = 'Cảnh báo tín chỉ CME';
$activeMenu = 'DT_CME_CanhBao';
$namNay = (int)date('Y');
$nguong = DT_Cme_BUS::getNguong();
require __DIR__ . '/../layouts/header.php';
?>
<div class="breadcrumb">
    <a href="<?= AppConfig::baseUrl('GUI/dashboard/index.php') ?>">Trang chủ</a>
    <span class="sep">›</span> Đào tạo y khoa liên tục
    <span class="sep">›</span> <span>Cảnh báo</span>
</div>

<div class="cb-note">
    <?= IconHelper::svg('alert-triangle', '18') ?>
    <div>Danh sách nhân viên <strong>chưa đạt tối thiểu <span id="cbNguong"><?= Helper::h((string)$nguong['gio']) ?></span> giờ tín chỉ</strong>
        <span id="cbChuKy">/ <?= (int)$nguong['chu_ky_nam'] ?> năm</span>.
        Ngưỡng thay đổi tại <a href="<?= AppConfig::baseUrl('GUI/DM_CauHinh/index.php') ?>">Hệ thống → Cấu hình → Đào tạo y khoa liên tục (CME)</a>.
    </div>
</div>

<div class="cme-kpis">
    <div class="cme-kpi kpi-a"><div class="ic"><?= IconHelper::svg('user','22') ?></div>
        <div><div class="n" id="kTong">0</div><div class="l">Tổng nhân viên đang làm</div></div></div>
    <div class="cme-kpi kpi-ok"><div class="ic"><?= IconHelper::svg('check','22') ?></div>
        <div><div class="n" id="kDat">0</div><div class="l">Đã đạt ngưỡng</div></div></div>
    <div class="cme-kpi kpi-warn"><div class="ic"><?= IconHelper::svg('alert-triangle','22') ?></div>
        <div><div class="n" id="kChua">0</div><div class="l">Chưa đạt ngưỡng</div></div></div>
    <div class="cme-kpi kpi-danger"><div class="ic"><?= IconHelper::svg('x','22') ?></div>
        <div><div class="n" id="kZero">0</div><div class="l">Chưa ghi nhận hoạt động nào</div></div></div>
</div>

<div class="card">
    <div class="toolbar">
        <div class="left">
            <input type="text" id="search" class="form-control" placeholder="Tìm mã NV, họ tên..." style="max-width:220px">
            <select id="filterNam" class="form-select" style="max-width:110px"></select>
            <select id="filterKhoa" class="form-select" style="max-width:210px"><option value="0">-- Tất cả khoa/phòng --</option></select>
            <select id="filterTrangThai" class="form-select" style="max-width:210px">
                <option value="">-- Tất cả chưa đạt --</option>
                <option value="chua_ghi_nhan">Chưa ghi nhận hoạt động nào</option>
                <option value="thieu_nhieu">Thiếu nhiều (dưới 50%)</option>
                <option value="sap_dat">Sắp đạt (từ 50%)</option>
            </select>
        </div>
        <div class="right">
            <button type="button" class="btn" onclick="exportExcel()" title="Xuất danh sách"><?= IconHelper::svg('download','16') ?> Xuất Excel</button>
        </div>
    </div>
    <div class="table-wrap" id="tableWrap" style="position:relative;min-height:220px">
        <table class="table">
            <thead>
                <tr>
                    <th style="width:44px" class="text-center">#</th>
                    <th style="width:110px">Mã NV</th>
                    <th>Họ tên</th>
                    <th style="width:230px">Khoa / Phòng</th>
                    <th style="width:80px" class="text-center">Số HĐ</th>
                    <th style="width:200px">Tiến độ</th>
                    <th style="width:110px" class="text-right">Còn thiếu</th>
                </tr>
            </thead>
            <tbody id="tbody"></tbody>
        </table>
    </div>
    <div class="pagination-wrap"><div id="pageInfo" class="text-muted">-</div><div id="pageNav"></div></div>
</div>

<style>
    .cb-note { display:flex; gap:10px; align-items:flex-start; background:#fef3c7; border:1px solid #fcd34d;
        color:#78350f; border-radius:10px; padding:12px 14px; font-size:13px; margin-bottom:16px; }
    .cb-note svg { flex:0 0 auto; margin-top:1px; }
    .cb-note a { color:#92400e; font-weight:700; text-decoration:underline; }
    .cme-kpis { display:grid; grid-template-columns:repeat(auto-fit,minmax(210px,1fr)); gap:14px; margin-bottom:16px; }
    .cme-kpi { background:#fff; border:1px solid var(--gray-200); border-radius:12px; padding:16px; display:flex; gap:13px; align-items:center; }
    .cme-kpi .ic { width:44px; height:44px; border-radius:12px; display:grid; place-items:center; color:#fff; flex:0 0 auto; }
    .cme-kpi.kpi-a .ic { background:#2563eb; } .cme-kpi.kpi-ok .ic { background:#16a34a; }
    .cme-kpi.kpi-warn .ic { background:#d97706; } .cme-kpi.kpi-danger .ic { background:#dc2626; }
    .cme-kpi .n { font-size:24px; font-weight:800; font-family:ui-monospace,Menlo,monospace; line-height:1; color:var(--gray-800); }
    .cme-kpi .l { font-size:12px; color:var(--gray-500); margin-top:5px; }
    .cb-nv-link { color:var(--primary); cursor:pointer; font-weight:600; }
    .cb-nv-link:hover { text-decoration:underline; }
    .cb-bar { height:8px; background:var(--gray-100); border-radius:5px; overflow:hidden; }
    .cb-bar > span { display:block; height:100%; border-radius:5px; }
    .cb-bar-txt { font-size:11px; color:var(--gray-500); margin-top:3px; font-family:ui-monospace,Menlo,monospace; }
    .cb-thieu { font-family:ui-monospace,Menlo,monospace; font-weight:700; color:#b91c1c; }
    .cb-zero td { background:#fef2f2; }
    .cb-tag0 { display:inline-block; font-size:10.5px; font-weight:700; padding:1px 7px; border-radius:5px;
        background:#fee2e2; color:#991b1b; margin-left:6px; }
</style>

<script>
var URL = APP_BASE + 'GUI/DT_CME/ajax_handler.php';
var NAM_NAY = <?= $namNay ?>;
var st = { page: 1, pageSize: 20, nam: NAM_NAY, khoa: 0, search: '', trangThai: '' };
function fmtGio(n){ n=parseFloat(n)||0; return (n%1===0)?n.toFixed(0):n.toFixed(1); }

function exportExcel(){
    var p = new URLSearchParams({nam: st.nam||0, khoa_phong_id: st.khoa||0,
                                 search: st.search||'', trang_thai: st.trangThai||''});
    window.location = APP_BASE + 'GUI/DT_CME/canh_bao_export.php?' + p.toString();
}

function load(){
    APP.showLoading('#tableWrap');
    APP.ajax(URL, {action:'canhBao', page:st.page, pageSize:st.pageSize, nam:st.nam,
                   khoa_phong_id:st.khoa, search:st.search, trang_thai:st.trangThai}).done(function(res){
        APP.hideLoading('#tableWrap');
        if(!res.success){ APP.toast(res.message,'error'); return; }
        var d = res.data, tk = d.thong_ke;

        $('#kTong').text(tk.tong_nv||0);
        $('#kDat').text(tk.so_dat||0);
        $('#kChua').text(tk.so_chua_dat||0);
        $('#kZero').text(tk.so_chua_ghi_nhan||0);
        $('#cbNguong').text(fmtGio(d.nguong.gio));
        $('#cbChuKy').text(d.tu_nam === d.den_nam ? ('trong năm ' + d.nam) : ('/ ' + d.nguong.chu_ky_nam + ' năm (chu kỳ ' + d.tu_nam + '–' + d.den_nam + ')'));

        var rows = d.data || [];
        var $tb = $('#tbody').empty();
        if(!rows.length){
            var msg = (st.search || st.khoa || st.trangThai)
                ? 'Không có nhân viên nào khớp bộ lọc.'
                : '🎉 Tất cả nhân viên đều đã đạt ngưỡng.';
            $tb.append('<tr><td colspan="7"><div class="empty-state" style="padding:40px">'+msg+'</div></td></tr>');
            $('#pageInfo').text('Không có bản ghi'); $('#pageNav').empty();
            return;
        }
        var stt = (st.page - 1) * st.pageSize;
        rows.forEach(function(r){
            stt++;
            var pct = parseInt(r.phan_tram,10)||0;
            var color = pct === 0 ? '#dc2626' : (pct < 50 ? '#d97706' : '#16a34a');
            var zero = (parseInt(r.so_ban_ghi,10)||0) === 0;
            $tb.append('<tr'+(zero?' class="cb-zero"':'')+'>'+
                '<td class="text-center">'+stt+'</td>'+
                '<td><strong>'+APP.escape(r.ma_nv||'')+'</strong></td>'+
                '<td><span class="cb-nv-link" onclick="xemSo('+r.nhan_vien_id+')">'+APP.escape(r.ho_ten||'')+'</span>'+
                    (zero?'<span class="cb-tag0">chưa ghi nhận</span>':'')+'</td>'+
                '<td>'+APP.escape(r.ten_khoa_phong||'-')+'</td>'+
                '<td class="text-center">'+(r.so_ban_ghi||0)+'</td>'+
                '<td><div class="cb-bar"><span style="width:'+pct+'%;background:'+color+'"></span></div>'+
                    '<div class="cb-bar-txt">'+fmtGio(r.tong_gio)+' / '+fmtGio(d.nguong.gio)+' giờ · '+pct+'%</div></td>'+
                '<td class="text-right"><span class="cb-thieu">'+fmtGio(r.con_thieu)+' giờ</span></td>'+
            '</tr>');
        });
        // Phân trang
        var total = d.totalRecords || 0;
        var from = (st.page - 1) * st.pageSize + 1;
        var to = Math.min(from + rows.length - 1, total);
        $('#pageInfo').text('Hiển thị ' + from + '-' + to + ' / ' + total + ' nhân viên chưa đạt');
        $('#pageNav').html(APP.renderPagination({
            currentPage: st.page, pageSize: st.pageSize,
            totalRecords: total, totalPages: d.totalPages || 1
        }));
    });
}
$('#pageNav').on('click', 'button[data-p]', function(){
    var p = parseInt($(this).data('p'), 10);
    if(!p || p === st.page) return;
    st.page = p; load();
});

// Mở sổ theo dõi của NV ở màn Theo dõi tín chỉ
function xemSo(nvId){
    window.open(APP_BASE + 'GUI/DT_CME/index.php?nhan_vien_id=' + nvId + '&nam=' + st.nam, '_blank');
}

$('#search').on('input', APP.debounce(function(){ st.search = $(this).val(); st.page = 1; load(); }, 400));
$('#filterNam').on('change', function(){ st.nam = parseInt(this.value,10)||NAM_NAY; st.page = 1; load(); });
$('#filterKhoa').on('change', function(){ st.khoa = parseInt(this.value,10)||0; st.page = 1; load(); });
$('#filterTrangThai').on('change', function(){ st.trangThai = this.value; st.page = 1; load(); });

function initCombos(){
    APP.ajax(URL,{action:'getNamCombo'}).done(function(r){
        var years = (r.success && r.data && r.data.length) ? r.data.map(Number) : [];
        if(years.indexOf(NAM_NAY) < 0) years.unshift(NAM_NAY);
        var $f = $('#filterNam').empty();
        years.forEach(function(y){ $f.append('<option value="'+y+'">'+y+'</option>'); });
        $f.val(st.nam);
    });
    APP.ajax(URL,{action:'getKhoaPhongCombo'}).done(function(r){
        if(!r.success) return;
        var $f = $('#filterKhoa');
        (r.data||[]).forEach(function(k){ $f.append('<option value="'+k.id+'">'+APP.escape(k.ten_khoa)+'</option>'); });
    });
}

initCombos(); load();
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
