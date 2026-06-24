<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../BUS/DT_BaoCao_BUS.php';
require_once __DIR__ . '/../../BUS/DT_KhoaHoc_BUS.php';
require_once __DIR__ . '/../../BUS/DT_KhoaHocChuongTrinh_BUS.php';

Helper::requireLogin();
if (!PhanQuyenHelper::hasQuyen('DT_BaoCao', PhanQuyenHelper::QUYEN_XEM)) {
    echo 'Bạn không có quyền truy cập chức năng này.'; exit;
}

$khoaCombo = DT_KhoaHoc_BUS::getCombo();
$tongKe = DT_BaoCao_BUS::thongKeTong();

$pageTitle  = 'Báo cáo đào tạo';
$activeMenu = 'DT_BaoCao';
require __DIR__ . '/../layouts/header.php';
?>
<div class="breadcrumb">
    <a href="<?= AppConfig::baseUrl('GUI/dashboard/index.php') ?>">Trang chủ</a>
    <span class="sep">›</span> <span>Báo cáo</span>
</div>

<style>
.bc-tabs { display:flex; gap:4px; border-bottom:1px solid var(--gray-200); margin-bottom:14px; }
.bc-tab { background:none; border:none; padding:9px 16px; font-size:14px; font-weight:500;
          color:var(--gray-500); cursor:pointer; border-bottom:2px solid transparent; transition:all .12s; }
.bc-tab:hover { color:var(--gray-800); background:var(--gray-50); border-radius:6px 6px 0 0; }
.bc-tab.is-active { color:var(--primary); border-bottom-color:var(--primary); font-weight:600; }
.bc-toolbar { display:flex; flex-wrap:wrap; align-items:flex-end; gap:12px; margin-bottom:12px; }
.bc-toolbar .field { display:flex; flex-direction:column; gap:4px; }
.bc-toolbar .field label { font-size:12px; font-weight:500; color:var(--gray-500); }
.bc-toolbar .grow { flex:1; min-width:200px; }
.bc-toolbar .right { margin-left:auto; }
</style>

<!-- Thống kê tổng -->
<div class="hv-stats" style="margin-bottom:16px">
    <div class="hv-stat"><div class="hv-stat-icon hv-stat-blue"><?= IconHelper::svg('users','22') ?></div><div><div class="hv-stat-label">Học viên</div><div class="hv-stat-value"><?= $tongKe['hoc_vien'] ?></div></div></div>
    <div class="hv-stat"><div class="hv-stat-icon hv-stat-green"><?= IconHelper::svg('book-open','22') ?></div><div><div class="hv-stat-label">Khóa học</div><div class="hv-stat-value"><?= $tongKe['khoa_hoc'] ?></div></div></div>
    <div class="hv-stat"><div class="hv-stat-icon hv-stat-purple"><?= IconHelper::svg('users','22') ?></div><div><div class="hv-stat-label">Chương trình ĐT</div><div class="hv-stat-value"><?= $tongKe['ctdt'] ?></div></div></div>
    <div class="hv-stat"><div class="hv-stat-icon hv-stat-orange"><?= IconHelper::svg('academic-cap','22') ?></div><div><div class="hv-stat-label">Chứng chỉ đã cấp</div><div class="hv-stat-value"><?= $tongKe['chung_chi'] ?></div></div></div>
</div>

<div class="card">
    <div class="bc-tabs">
        <button type="button" class="bc-tab is-active" data-tab="khoa" onclick="switchTab('khoa')">Theo khóa / CTĐT</button>
        <button type="button" class="bc-tab" data-tab="hv" onclick="switchTab('hv')">Danh sách HV + kết quả</button>
        <button type="button" class="bc-tab" data-tab="tong" onclick="switchTab('tong')">Thống kê tổng</button>
    </div>

    <!-- Tab 1: theo khóa/CTĐT -->
    <div class="bc-pane" id="paneKhoa">
        <div class="bc-toolbar">
            <div class="field grow">
                <label>Khóa học</label>
                <select id="fKhoa" class="form-select">
                    <option value="0">-- Tất cả khóa học --</option>
                    <?php foreach ($khoaCombo as $k): ?>
                        <option value="<?= $k['id'] ?>"><?= Helper::h($k['ma_khoa_hoc'].' - '.$k['ten_khoa_hoc']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="field"><label>Khai giảng từ</label><input type="date" id="fKhoaFrom" class="form-control"></div>
            <div class="field"><label>đến</label><input type="date" id="fKhoaTo" class="form-control"></div>
            <div class="right"><button type="button" class="btn btn-primary" onclick="exportKhoa()"><?= IconHelper::svg('download','16') ?> Xuất Excel</button></div>
        </div>
        <div class="table-wrap" id="bcKhoaWrap" style="position:relative;min-height:200px">
            <table class="table">
                <thead><tr>
                    <th class="text-center" style="width:50px">#</th>
                    <th>Khóa học</th><th>Chương trình đào tạo</th>
                    <th class="text-center">Số HV</th><th class="text-center">Đạt</th>
                    <th class="text-center">Không đạt</th><th class="text-center">Điểm TB</th>
                    <th class="text-center">Chứng chỉ</th>
                </tr></thead>
                <tbody id="bcKhoaBody"></tbody>
            </table>
        </div>
    </div>

    <!-- Tab 2: DS HV + kết quả -->
    <div class="bc-pane" id="paneHv" style="display:none">
        <div class="bc-toolbar">
            <div class="field"><label>Khóa học</label>
                <select id="fKhoaHv" class="form-select" style="min-width:220px">
                    <option value="">-- Chọn khóa học --</option>
                    <?php foreach ($khoaCombo as $k): ?>
                        <option value="<?= $k['id'] ?>"><?= Helper::h($k['ma_khoa_hoc'].' - '.$k['ten_khoa_hoc']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="field"><label>Chương trình</label>
                <select id="fKhct" class="form-select" style="min-width:240px" disabled>
                    <option value="">-- Chọn chương trình --</option>
                </select>
            </div>
            <div class="field"><label>Ghi danh từ</label><input type="date" id="fHvFrom" class="form-control"></div>
            <div class="field"><label>đến</label><input type="date" id="fHvTo" class="form-control"></div>
            <div class="right"><button type="button" class="btn btn-primary" onclick="exportHv()"><?= IconHelper::svg('download','16') ?> Xuất Excel</button></div>
        </div>
        <div class="table-wrap" id="bcHvWrap" style="position:relative;min-height:200px">
            <table class="table">
                <thead><tr>
                    <th class="text-center" style="width:50px">#</th>
                    <th>Mã HV</th><th>Họ tên</th><th>Đơn vị</th>
                    <th class="text-center">TX</th><th class="text-center">GK</th><th class="text-center">CK</th>
                    <th class="text-center">Tổng kết</th><th class="text-center">Xếp loại</th>
                    <th class="text-center">Chuyên cần</th><th class="text-center">Đạt</th>
                </tr></thead>
                <tbody id="bcHvBody"></tbody>
            </table>
        </div>
    </div>

    <!-- Tab 3: thống kê tổng -->
    <div class="bc-pane" id="paneTong" style="display:none">
        <div class="bc-toolbar">
            <div class="field"><label>Từ ngày</label><input type="date" id="fTongFrom" class="form-control"></div>
            <div class="field"><label>Đến ngày</label><input type="date" id="fTongTo" class="form-control"></div>
            <div class="field"><label>&nbsp;</label><button type="button" class="btn" onclick="loadTong()">Áp dụng</button></div>
            <div class="text-muted" style="font-size:11.5px;align-self:center;max-width:300px">Khoảng thời gian áp dụng cho: ghi danh, chứng chỉ, đăng ký, buổi học. Các danh mục là tổng hiện có.</div>
        </div>
        <table class="table" style="max-width:520px" id="bcTongTable">
            <tbody>
                <tr><td>Tổng học viên</td><td class="text-right"><strong id="tk_hoc_vien"><?= $tongKe['hoc_vien'] ?></strong></td></tr>
                <tr><td>Tổng khóa học</td><td class="text-right"><strong id="tk_khoa_hoc"><?= $tongKe['khoa_hoc'] ?></strong></td></tr>
                <tr><td>Tổng chương trình đào tạo</td><td class="text-right"><strong id="tk_ctdt"><?= $tongKe['ctdt'] ?></strong></td></tr>
                <tr><td>Tổng bài học</td><td class="text-right"><strong id="tk_bai_hoc"><?= $tongKe['bai_hoc'] ?></strong></td></tr>
                <tr><td>Lượt ghi danh</td><td class="text-right"><strong id="tk_ghi_danh"><?= $tongKe['ghi_danh'] ?></strong></td></tr>
                <tr><td>Buổi học đã lên lịch</td><td class="text-right"><strong id="tk_lich_hoc"><?= $tongKe['lich_hoc'] ?></strong></td></tr>
                <tr><td>Chứng chỉ đã cấp</td><td class="text-right"><strong id="tk_chung_chi"><?= $tongKe['chung_chi'] ?></strong></td></tr>
                <tr><td>Đơn đăng ký</td><td class="text-right"><strong id="tk_dang_ky"><?= $tongKe['dang_ky'] ?></strong></td></tr>
            </tbody>
        </table>
        <button type="button" class="btn btn-primary" onclick="exportTong()"><?= IconHelper::svg('download','16') ?> Xuất Excel</button>
    </div>
</div>

<script>
var URL = APP_BASE + 'GUI/BaoCao/ajax_handler.php';
function switchTab(t){
    $('.bc-tab').removeClass('is-active'); $('.bc-tab[data-tab="'+t+'"]').addClass('is-active');
    $('#paneKhoa').toggle(t==='khoa'); $('#paneHv').toggle(t==='hv'); $('#paneTong').toggle(t==='tong');
    if (t==='khoa') loadKhoa();
}

// ===== Tab theo khóa/CTĐT =====
function loadKhoa(){
    APP.showLoading('#bcKhoaWrap');
    APP.ajax(URL, {action:'theoKhoa', khoa_hoc_id: $('#fKhoa').val()||0, from:$('#fKhoaFrom').val(), to:$('#fKhoaTo').val()}).done(function(res){
        APP.hideLoading('#bcKhoaWrap');
        if(!res.success) return;
        var $b=$('#bcKhoaBody').empty(), rows=res.data||[];
        if(!rows.length){ $b.append('<tr><td colspan="8" class="text-center text-muted" style="padding:24px">Không có dữ liệu</td></tr>'); return; }
        rows.forEach(function(r,i){
            $b.append('<tr>'+
                '<td class="text-center">'+(i+1)+'</td>'+
                '<td>'+APP.escape(r.ma_khoa_hoc||'')+'<div class="text-muted" style="font-size:11px">'+APP.escape(r.ten_khoa_hoc||'')+'</div></td>'+
                '<td>'+APP.escape(r.ma_chuong_trinh||'')+'<div class="text-muted" style="font-size:11px">'+APP.escape(r.ten_chuong_trinh||'')+'</div></td>'+
                '<td class="text-center">'+(r.so_hv||0)+'</td>'+
                '<td class="text-center" style="color:#16a34a">'+(r.so_dat||0)+'</td>'+
                '<td class="text-center" style="color:#dc2626">'+(r.so_khong_dat||0)+'</td>'+
                '<td class="text-center"><strong>'+(r.diem_tb!=null?r.diem_tb:'—')+'</strong></td>'+
                '<td class="text-center">'+(r.so_chung_chi||0)+'</td>'+
            '</tr>');
        });
    });
}
$('#fKhoa, #fKhoaFrom, #fKhoaTo').on('change', loadKhoa);
function exportKhoa(){ var p=new URLSearchParams({loai:'khoa',khoa_hoc_id:$('#fKhoa').val()||0,from:$('#fKhoaFrom').val()||'',to:$('#fKhoaTo').val()||''}); window.location=APP_BASE+'GUI/BaoCao/export.php?'+p.toString(); }

// ===== Tab DS HV + kết quả (chọn khóa -> CTĐT) =====
$('#fKhoaHv').on('change', function(){
    var khoaId=this.value;
    var $ct=$('#fKhct').empty().append('<option value="">-- Chọn chương trình --</option>').prop('disabled',true);
    $('#bcHvBody').empty();
    if(!khoaId) return;
    APP.ajax(URL, {action:'ctTheoKhoa', khoa_hoc_id:khoaId}).done(function(res){
        if(!res.success) return;
        (res.data||[]).forEach(function(c){ $ct.append('<option value="'+c.id+'">'+APP.escape((c.ma_chuong_trinh?c.ma_chuong_trinh+' - ':'')+(c.ten_chuong_trinh||''))+'</option>'); });
        $ct.prop('disabled',false);
    });
});
$('#fKhct, #fHvFrom, #fHvTo').on('change', loadHv);
function loadHv(){
    var khct=$('#fKhct').val(); var $b=$('#bcHvBody').empty();
    if(!khct) return;
    APP.showLoading('#bcHvWrap');
    APP.ajax(URL, {action:'dsHv', khct_id:khct, from:$('#fHvFrom').val(), to:$('#fHvTo').val()}).done(function(res){
        APP.hideLoading('#bcHvWrap');
        if(!res.success) return;
        var rows=res.data||[];
        if(!rows.length){ $b.append('<tr><td colspan="11" class="text-center text-muted" style="padding:24px">Chưa có học viên</td></tr>'); return; }
        rows.forEach(function(r,i){
            var cc = (r.tong_buoi>0)? Math.round(r.co_mat/r.tong_buoi*100)+'%' : '—';
            var dat = r.dat==null?'—':(parseInt(r.dat,10)===1?'<span style="color:#16a34a;font-weight:600">Đạt</span>':'<span style="color:#dc2626;font-weight:600">Chưa</span>');
            var f=function(x){ return (x==null||x==='')?'—':parseFloat(x).toFixed(1); };
            $b.append('<tr>'+
                '<td class="text-center">'+(i+1)+'</td>'+
                '<td>'+APP.escape(r.ma_hv||'')+'</td><td>'+APP.escape(r.ho_ten||'')+'</td>'+
                '<td class="text-muted">'+APP.escape(r.don_vi_cong_tac||'')+'</td>'+
                '<td class="text-center">'+f(r.diem_thuong_xuyen)+'</td>'+
                '<td class="text-center">'+f(r.diem_giua_ky)+'</td>'+
                '<td class="text-center">'+f(r.diem_cuoi_ky)+'</td>'+
                '<td class="text-center"><strong>'+f(r.diem_tong_ket)+'</strong></td>'+
                '<td class="text-center">'+APP.escape(r.xep_loai||'—')+'</td>'+
                '<td class="text-center">'+cc+'</td>'+
                '<td class="text-center">'+dat+'</td>'+
            '</tr>');
        });
    });
}
function exportHv(){
    var khct=$('#fKhct').val();
    if(!khct){ APP.toast('Chọn chương trình đào tạo','error'); return; }
    var p=new URLSearchParams({loai:'hv',khct_id:khct,from:$('#fHvFrom').val()||'',to:$('#fHvTo').val()||''});
    window.location=APP_BASE+'GUI/BaoCao/export.php?'+p.toString();
}

// ===== Tab thống kê tổng =====
function loadTong(){
    APP.ajax(URL, {action:'thongKe', from:$('#fTongFrom').val(), to:$('#fTongTo').val()}).done(function(res){
        if(!res.success) return;
        var d=res.data||{};
        ['hoc_vien','khoa_hoc','ctdt','bai_hoc','ghi_danh','lich_hoc','chung_chi','dang_ky'].forEach(function(k){
            $('#tk_'+k).text(d[k]!=null?d[k]:0);
        });
    });
}
function exportTong(){ var p=new URLSearchParams({loai:'tong',from:$('#fTongFrom').val()||'',to:$('#fTongTo').val()||''}); window.location=APP_BASE+'GUI/BaoCao/export.php?'+p.toString(); }

loadKhoa();
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
