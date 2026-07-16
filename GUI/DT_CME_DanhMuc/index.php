<?php
require_once __DIR__ . '/../../bootstrap.php';

Helper::requireLogin();
if (!PhanQuyenHelper::hasQuyen('DT_CME_DanhMuc', PhanQuyenHelper::QUYEN_XEM)) {
    echo 'Bạn không có quyền truy cập chức năng này.'; exit;
}
$canAdd  = PhanQuyenHelper::hasQuyen('DT_CME_DanhMuc', PhanQuyenHelper::QUYEN_THEM);
$canEdit = PhanQuyenHelper::hasQuyen('DT_CME_DanhMuc', PhanQuyenHelper::QUYEN_SUA);
$canDel  = PhanQuyenHelper::hasQuyen('DT_CME_DanhMuc', PhanQuyenHelper::QUYEN_XOA);

$pageTitle = 'Danh mục quy đổi CME';
$activeMenu = 'DT_CME_DanhMuc';
require __DIR__ . '/../layouts/header.php';
?>
<div class="breadcrumb">
    <a href="<?= AppConfig::baseUrl('GUI/dashboard/index.php') ?>">Trang chủ</a>
    <span class="sep">›</span> Đào tạo y khoa liên tục
    <span class="sep">›</span> <span>Danh mục quy đổi</span>
</div>

<div class="card">
    <div class="cme-tabs">
        <button type="button" class="cme-tab active" data-tab="loai">Loại hình thức &amp; công thức</button>
        <button type="button" class="cme-tab" data-tab="nhom">Nhóm hình thức</button>
    </div>

    <!-- TAB LOẠI -->
    <div class="cme-pane active" data-pane="loai">
        <div class="toolbar">
            <div class="left">
                <input type="text" id="loaiSearch" class="form-control" placeholder="Tìm mã, tên loại..." style="max-width:280px">
                <select id="loaiFilterNhom" class="form-select" style="max-width:260px">
                    <option value="0">-- Tất cả nhóm --</option>
                </select>
                <select id="loaiFilterDaXoa" class="form-select" style="max-width:150px">
                    <option value="0">Đang dùng</option>
                    <option value="1">Thùng rác</option>
                </select>
            </div>
            <div class="right">
                <?php if ($canAdd): ?><button type="button" class="btn btn-primary" onclick="loaiCreate()">+ Thêm loại</button><?php endif; ?>
            </div>
        </div>
        <div class="table-wrap" id="loaiWrap" style="position:relative;min-height:200px">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width:44px" class="text-center">#</th>
                        <th style="width:140px">Mã</th>
                        <th>Tên loại hình thức</th>
                        <th style="width:240px">Nhóm</th>
                        <th style="width:120px">Kiểu quy đổi</th>
                        <th style="width:140px" class="text-right">Quy đổi</th>
                        <th style="width:120px" class="text-right">Hành động</th>
                    </tr>
                </thead>
                <tbody id="loaiTbody"></tbody>
            </table>
        </div>
        <div class="pagination-wrap"><div id="loaiInfo" class="text-muted">-</div><div id="loaiNav"></div></div>
    </div>

    <!-- TAB NHÓM -->
    <div class="cme-pane" data-pane="nhom">
        <div class="toolbar">
            <div class="left">
                <input type="text" id="nhomSearch" class="form-control" placeholder="Tìm mã, tên nhóm..." style="max-width:280px">
                <select id="nhomFilterDaXoa" class="form-select" style="max-width:150px">
                    <option value="0">Đang dùng</option>
                    <option value="1">Thùng rác</option>
                </select>
            </div>
            <div class="right">
                <?php if ($canAdd): ?><button type="button" class="btn btn-primary" onclick="nhomCreate()">+ Thêm nhóm</button><?php endif; ?>
            </div>
        </div>
        <div class="table-wrap" id="nhomWrap" style="position:relative;min-height:200px">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width:44px" class="text-center">#</th>
                        <th style="width:170px">Mã</th>
                        <th>Tên nhóm hình thức</th>
                        <th style="width:90px" class="text-center">Số loại</th>
                        <th style="width:80px" class="text-center">Thứ tự</th>
                        <th style="width:130px" class="text-right">Hành động</th>
                    </tr>
                </thead>
                <tbody id="nhomTbody"></tbody>
            </table>
        </div>
        <div class="pagination-wrap"><div id="nhomInfo" class="text-muted">-</div><div id="nhomNav"></div></div>
    </div>
</div>

<!-- Modal Nhóm -->
<div class="modal-backdrop" id="modalNhom">
    <div class="modal">
        <div class="modal-header"><h3 id="nhomTitle">Thêm nhóm</h3><button type="button" class="close" onclick="$('#modalNhom').removeClass('open')">&times;</button></div>
        <form id="formNhom">
            <div class="modal-body">
                <input type="hidden" name="id" id="n_id">
                <div class="form-row">
                    <div class="form-group"><label>Mã nhóm</label><input type="text" name="ma_nhom" id="n_ma" class="form-control" maxlength="30" placeholder="Để trống → tự sinh"></div>
                    <div class="form-group"><label>Thứ tự</label><input type="number" name="thu_tu" id="n_thutu" class="form-control" value="0"></div>
                </div>
                <div class="form-group"><label>Tên nhóm <span class="required">*</span></label><input type="text" name="ten_nhom" id="n_ten" class="form-control" required maxlength="255"></div>
            </div>
            <div class="modal-footer"><button type="button" class="btn" onclick="$('#modalNhom').removeClass('open')">Hủy</button><button type="submit" class="btn btn-primary">Lưu</button></div>
        </form>
    </div>
</div>

<!-- Modal Loại -->
<div class="modal-backdrop" id="modalLoai">
    <div class="modal" style="max-width:640px">
        <div class="modal-header"><h3 id="loaiTitle">Thêm loại hình thức</h3><button type="button" class="close" onclick="$('#modalLoai').removeClass('open')">&times;</button></div>
        <form id="formLoai">
            <div class="modal-body">
                <input type="hidden" name="id" id="l_id">
                <div class="form-row">
                    <div class="form-group"><label>Nhóm hình thức <span class="required">*</span></label>
                        <select name="nhom_id" id="l_nhom" class="form-select" required></select>
                    </div>
                    <div class="form-group"><label>Mã loại</label><input type="text" name="ma_loai" id="l_ma" class="form-control" maxlength="40" placeholder="Để trống → tự sinh"></div>
                </div>
                <div class="form-group"><label>Tên loại hình thức <span class="required">*</span></label>
                    <input type="text" name="ten_loai" id="l_ten" class="form-control" required maxlength="300"></div>
                <div class="form-row-3">
                    <div class="form-group"><label>Kiểu quy đổi <span class="required">*</span></label>
                        <select name="kieu_quy_doi" id="l_kieu" class="form-select">
                            <option value="theo_tiet">Theo tiết (× hệ số)</option>
                            <option value="co_dinh">Cố định (giờ × số lượng)</option>
                            <option value="theo_nam">Theo năm (khoán/năm)</option>
                        </select>
                    </div>
                    <div class="form-group"><label id="l_gt_label">Giờ mỗi đơn vị <span class="required">*</span></label>
                        <input type="number" step="0.01" min="0" name="gia_tri_quy_doi" id="l_gt" class="form-control" value="1"></div>
                    <div class="form-group"><label>Đơn vị tính</label>
                        <input type="text" name="don_vi_tinh" id="l_dv" class="form-control" maxlength="40" placeholder="buổi, báo cáo, bài..."></div>
                </div>
                <div class="cme-formula-hint" id="l_hint"></div>
                <div class="form-row">
                    <div class="form-group"><label>Phòng phụ trách quy đổi</label>
                        <select name="khoa_phong_id" id="l_kp" class="form-select"><option value="">-- Không --</option></select></div>
                    <div class="form-group"><label>Thứ tự</label><input type="number" name="thu_tu" id="l_thutu" class="form-control" value="0"></div>
                </div>
            </div>
            <div class="modal-footer"><button type="button" class="btn" onclick="$('#modalLoai').removeClass('open')">Hủy</button><button type="submit" class="btn btn-primary">Lưu</button></div>
        </form>
    </div>
</div>

<style>
    .cme-tabs { display:flex; gap:4px; border-bottom:2px solid var(--gray-200); margin-bottom:16px; }
    .cme-tab { border:none; background:none; padding:10px 18px; font-size:14px; font-weight:600; color:var(--gray-500);
        cursor:pointer; border-bottom:2px solid transparent; margin-bottom:-2px; }
    .cme-tab.active { color:var(--primary); border-bottom-color:var(--primary); }
    .cme-pane { display:none; } .cme-pane.active { display:block; }
    .cme-kieu { display:inline-block; font-size:11.5px; font-weight:700; padding:2px 8px; border-radius:5px; }
    .cme-kieu.theo_tiet { background:#e7f5ec; color:#0f7a38; }
    .cme-kieu.co_dinh { background:#e6edfd; color:#2563eb; }
    .cme-kieu.theo_nam { background:#fbf1de; color:#b7791f; }
    .cme-quydoi { font-family:ui-monospace,Menlo,monospace; color:#d1367f; font-weight:600; }
    .cme-formula-hint { font-size:12.5px; color:var(--gray-600); background:#f8fafc; border:1px solid var(--gray-200);
        border-radius:8px; padding:9px 12px; margin:2px 0 4px; }
</style>

<script>
var URL = APP_BASE + 'GUI/DT_CME_DanhMuc/ajax_handler.php';
var CAN_EDIT = <?= $canEdit?'true':'false' ?>, CAN_DEL = <?= $canDel?'true':'false' ?>;
var ICON_EDIT = '<?= addslashes(IconHelper::svg('edit','16')) ?>';
var ICON_TRASH = '<?= addslashes(IconHelper::svg('trash','16')) ?>';
var ICON_RESTORE = '<?= addslashes(IconHelper::svg('refresh','16')) ?>';
var nhomCombo = [], kpCombo = [];
var sN = { page:1, pageSize:20, search:'', daXoa:0 };
var sL = { page:1, pageSize:20, search:'', daXoa:0, nhomId:0 };

var KIEU_LABEL = { theo_tiet:'Theo tiết', co_dinh:'Cố định', theo_nam:'Theo năm' };

// ---- Tabs ----
$('.cme-tab').on('click', function(){
    $('.cme-tab').removeClass('active'); $(this).addClass('active');
    var t = $(this).data('tab');
    $('.cme-pane').removeClass('active'); $('.cme-pane[data-pane="'+t+'"]').addClass('active');
    if (t === 'nhom') loadNhom(); else loadLoai();
});

// ================= NHÓM =================
function loadNhom(){
    APP.showLoading('#nhomWrap');
    APP.ajax(URL, {action:'nhomGetPaged', page:sN.page, pageSize:sN.pageSize, search:sN.search, da_xoa:sN.daXoa})
    .done(function(res){
        APP.hideLoading('#nhomWrap');
        if(!res.success){ APP.toast(res.message,'error'); return; }
        var $tb=$('#nhomTbody').empty();
        if(!res.data.length){ $tb.append('<tr><td colspan="6"><div class="empty-state">Không có nhóm</div></td></tr>'); }
        var stt=(sN.page-1)*sN.pageSize;
        res.data.forEach(function(r){
            stt++;
            var act='';
            if(sN.daXoa==0){
                if(CAN_EDIT) act+='<button class="btn btn-sm" title="Sửa" onclick="nhomEdit('+r.id+')">'+ICON_EDIT+'</button>';
                if(CAN_DEL)  act+='<button class="btn btn-sm btn-danger" title="Xóa" onclick="nhomTrash('+r.id+')">'+ICON_TRASH+'</button>';
            } else if(CAN_EDIT){ act+='<button class="btn btn-sm btn-success" title="Khôi phục" onclick="nhomRestore('+r.id+')">'+ICON_RESTORE+'</button>'; }
            $tb.append('<tr><td class="text-center">'+stt+'</td><td><strong>'+APP.escape(r.ma_nhom)+'</strong></td>'+
                '<td>'+APP.escape(r.ten_nhom)+'</td><td class="text-center">'+(r.so_loai||0)+'</td>'+
                '<td class="text-center">'+(r.thu_tu||0)+'</td><td><div class="actions">'+act+'</div></td></tr>');
        });
        renderPager('#nhomInfo','#nhomNav',res.pagination,sN);
    });
}
function nhomCreate(){ $('#formNhom')[0].reset(); $('#n_id').val(''); $('#nhomTitle').text('Thêm nhóm'); $('#modalNhom').addClass('open'); }
function nhomEdit(id){
    APP.ajax(URL,{action:'nhomGetById',id:id}).done(function(res){
        if(!res.success){APP.toast(res.message,'error');return;}
        var e=res.data; $('#n_id').val(e.id); $('#n_ma').val(e.ma_nhom); $('#n_ten').val(e.ten_nhom);
        $('#n_thutu').val(e.thu_tu||0); $('#nhomTitle').text('Sửa nhóm'); $('#modalNhom').addClass('open');
    });
}
$('#formNhom').on('submit',function(e){
    e.preventDefault();
    var data=$(this).serializeArray(); data.push({name:'action',value:$('#n_id').val()?'nhomUpdate':'nhomInsert'});
    APP.ajax(URL,data).done(function(res){
        if(res.success){ APP.toast(res.message,'success'); $('#modalNhom').removeClass('open'); loadNhom(); loadNhomCombo(); }
        else APP.toast(res.message,'error');
    });
});
function nhomTrash(id){ APP.confirm('Xóa nhóm này? (các loại thuộc nhóm vẫn giữ)', function(){ APP.ajax(URL,{action:'nhomTrash',id:id}).done(function(res){ res.success?(APP.toast(res.message,'success'),loadNhom()):APP.toast(res.message,'error'); }); }); }
function nhomRestore(id){ APP.ajax(URL,{action:'nhomRestore',id:id}).done(function(res){ res.success?(APP.toast(res.message,'success'),loadNhom()):APP.toast(res.message,'error'); }); }

$('#nhomSearch').on('input', APP.debounce(function(){ sN.search=$(this).val(); sN.page=1; loadNhom(); },400));
$('#nhomFilterDaXoa').on('change', function(){ sN.daXoa=parseInt(this.value,10)||0; sN.page=1; loadNhom(); });

// ================= LOẠI =================
function loadLoai(){
    APP.showLoading('#loaiWrap');
    APP.ajax(URL, {action:'loaiGetPaged', page:sL.page, pageSize:sL.pageSize, search:sL.search, da_xoa:sL.daXoa, nhom_id:sL.nhomId})
    .done(function(res){
        APP.hideLoading('#loaiWrap');
        if(!res.success){ APP.toast(res.message,'error'); return; }
        var $tb=$('#loaiTbody').empty();
        if(!res.data.length){ $tb.append('<tr><td colspan="7"><div class="empty-state">Không có loại nào</div></td></tr>'); }
        var stt=(sL.page-1)*sL.pageSize;
        res.data.forEach(function(r){
            stt++;
            var qd = quyDoiText(r);
            var act='';
            if(sL.daXoa==0){
                if(CAN_EDIT) act+='<button class="btn btn-sm" title="Sửa" onclick="loaiEdit('+r.id+')">'+ICON_EDIT+'</button>';
                if(CAN_DEL)  act+='<button class="btn btn-sm btn-danger" title="Xóa" onclick="loaiTrash('+r.id+')">'+ICON_TRASH+'</button>';
            } else if(CAN_EDIT){ act+='<button class="btn btn-sm btn-success" title="Khôi phục" onclick="loaiRestore('+r.id+')">'+ICON_RESTORE+'</button>'; }
            $tb.append('<tr><td class="text-center">'+stt+'</td><td><strong>'+APP.escape(r.ma_loai)+'</strong></td>'+
                '<td>'+APP.escape(r.ten_loai)+'</td><td>'+APP.escape(r.ten_nhom||'-')+'</td>'+
                '<td><span class="cme-kieu '+r.kieu_quy_doi+'">'+(KIEU_LABEL[r.kieu_quy_doi]||r.kieu_quy_doi)+'</span></td>'+
                '<td class="text-right"><span class="cme-quydoi">'+qd+'</span></td>'+
                '<td><div class="actions">'+act+'</div></td></tr>');
        });
        renderPager('#loaiInfo','#loaiNav',res.pagination,sL);
    });
}
function quyDoiText(r){
    var g = parseFloat(r.gia_tri_quy_doi);
    var gv = (g % 1 === 0) ? g.toFixed(0) : g.toString();
    var dv = r.don_vi_tinh || '';
    if(r.kieu_quy_doi==='theo_tiet') return gv+' giờ/tiết';
    if(r.kieu_quy_doi==='theo_nam')  return gv+' giờ/năm';
    return gv+' giờ/'+(dv||'lần');
}
function loaiCreate(){
    $('#formLoai')[0].reset(); $('#l_id').val(''); $('#l_gt').val(1); $('#l_kieu').val('co_dinh');
    fillNhomSelect('#l_nhom', sL.nhomId||''); fillKpSelect();
    updateKieuHint(); $('#loaiTitle').text('Thêm loại hình thức'); $('#modalLoai').addClass('open');
}
function loaiEdit(id){
    APP.ajax(URL,{action:'loaiGetById',id:id}).done(function(res){
        if(!res.success){APP.toast(res.message,'error');return;}
        var e=res.data;
        fillNhomSelect('#l_nhom', e.nhom_id); fillKpSelect(e.khoa_phong_id);
        $('#l_id').val(e.id); $('#l_ma').val(e.ma_loai); $('#l_ten').val(e.ten_loai);
        $('#l_kieu').val(e.kieu_quy_doi); $('#l_gt').val(e.gia_tri_quy_doi); $('#l_dv').val(e.don_vi_tinh||'');
        $('#l_thutu').val(e.thu_tu||0);
        updateKieuHint(); $('#loaiTitle').text('Sửa loại hình thức'); $('#modalLoai').addClass('open');
    });
}
$('#l_kieu').on('change', updateKieuHint);
function updateKieuHint(){
    var k=$('#l_kieu').val();
    if(k==='theo_tiet'){ $('#l_gt_label').html('Hệ số (mặc định 1)'); $('#l_hint').html('Giờ tín chỉ = <b>số tiết</b> × hệ số. VD 1 tiết = 1 giờ.'); $('#l_dv').val($('#l_dv').val()||'tiết'); }
    else if(k==='theo_nam'){ $('#l_gt_label').html('Giờ mỗi năm <span class="required">*</span>'); $('#l_hint').html('Giờ tín chỉ = <b>giá trị</b> (khoán trọn năm, bỏ qua số lượng). VD 24 giờ/năm.'); $('#l_dv').val($('#l_dv').val()||'năm'); }
    else { $('#l_gt_label').html('Giờ mỗi đơn vị <span class="required">*</span>'); $('#l_hint').html('Giờ tín chỉ = <b>số lượng</b> × giá trị. VD báo cáo viên 2 giờ/báo cáo.'); }
}
$('#formLoai').on('submit',function(e){
    e.preventDefault();
    var data=$(this).serializeArray(); data.push({name:'action',value:$('#l_id').val()?'loaiUpdate':'loaiInsert'});
    APP.ajax(URL,data).done(function(res){
        if(res.success){ APP.toast(res.message,'success'); $('#modalLoai').removeClass('open'); loadLoai(); }
        else APP.toast(res.message,'error');
    });
});
function loaiTrash(id){ APP.confirm('Xóa loại này?', function(){ APP.ajax(URL,{action:'loaiTrash',id:id}).done(function(res){ res.success?(APP.toast(res.message,'success'),loadLoai()):APP.toast(res.message,'error'); }); }); }
function loaiRestore(id){ APP.ajax(URL,{action:'loaiRestore',id:id}).done(function(res){ res.success?(APP.toast(res.message,'success'),loadLoai()):APP.toast(res.message,'error'); }); }

$('#loaiSearch').on('input', APP.debounce(function(){ sL.search=$(this).val(); sL.page=1; loadLoai(); },400));
$('#loaiFilterNhom').on('change', function(){ sL.nhomId=parseInt(this.value,10)||0; sL.page=1; loadLoai(); });
$('#loaiFilterDaXoa').on('change', function(){ sL.daXoa=parseInt(this.value,10)||0; sL.page=1; loadLoai(); });

// ---- Combos ----
function loadNhomCombo(){
    APP.ajax(URL,{action:'getNhomCombo'}).done(function(res){
        if(!res.success)return; nhomCombo=res.data||[];
        var $f=$('#loaiFilterNhom').empty().append('<option value="0">-- Tất cả nhóm --</option>');
        nhomCombo.forEach(function(n){ $f.append('<option value="'+n.id+'">'+APP.escape(n.ten_nhom)+'</option>'); });
    });
}
function fillNhomSelect(sel, val){
    var $s=$(sel).empty().append('<option value="">-- Chọn nhóm --</option>');
    nhomCombo.forEach(function(n){ $s.append('<option value="'+n.id+'">'+APP.escape(n.ma_nhom+' — '+n.ten_nhom)+'</option>'); });
    if(val) $s.val(val);
}
function fillKpSelect(val){
    var $s=$('#l_kp').empty().append('<option value="">-- Không --</option>');
    kpCombo.forEach(function(k){ $s.append('<option value="'+k.id+'">'+APP.escape(k.ten_khoa)+'</option>'); });
    if(val) $s.val(val);
}
function loadKpCombo(){ APP.ajax(URL,{action:'getKhoaPhongCombo'}).done(function(res){ if(res.success) kpCombo=res.data||[]; }); }

function renderPager(infoSel, navSel, p, st){
    var from=(p.currentPage-1)*p.pageSize+1, to=Math.min(from+p.pageSize-1,p.totalRecords);
    $(infoSel).text(p.totalRecords?('Hiển thị '+from+'-'+to+' / '+p.totalRecords):'Không có bản ghi');
    $(navSel).html(APP.renderPagination(p));
    $(navSel).off('click').on('click','button[data-p]',function(){ var np=parseInt($(this).data('p'),10); if(!np||np===st.page)return; st.page=np; (st===sN?loadNhom():loadLoai()); });
}

// init
loadNhomCombo(); loadKpCombo(); loadLoai();
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
