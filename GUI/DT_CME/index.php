<?php
require_once __DIR__ . '/../../bootstrap.php';

Helper::requireLogin();
if (!PhanQuyenHelper::hasQuyen('DT_CME', PhanQuyenHelper::QUYEN_XEM)) {
    echo 'Bạn không có quyền truy cập chức năng này.'; exit;
}
$canAdd  = PhanQuyenHelper::hasQuyen('DT_CME', PhanQuyenHelper::QUYEN_THEM);
$canEdit = PhanQuyenHelper::hasQuyen('DT_CME', PhanQuyenHelper::QUYEN_SUA);
$canDel  = PhanQuyenHelper::hasQuyen('DT_CME', PhanQuyenHelper::QUYEN_XOA);

$pageTitle = 'Theo dõi tín chỉ CME';
$activeMenu = 'DT_CME';
$namNay = (int)date('Y');
require __DIR__ . '/../layouts/header.php';
?>
<div class="breadcrumb">
    <a href="<?= AppConfig::baseUrl('GUI/dashboard/index.php') ?>">Trang chủ</a>
    <span class="sep">›</span> Đào tạo y khoa liên tục
    <span class="sep">›</span> <span>Theo dõi tín chỉ</span>
</div>

<div class="cme-stats" id="cmeStats">
    <div class="cme-stat"><div class="n" id="stTong">0</div><div class="l">Tổng giờ tín chỉ</div></div>
    <div class="cme-stat"><div class="n" id="stNv">0</div><div class="l">Nhân viên có ghi nhận</div></div>
    <div class="cme-stat"><div class="n" id="stBanGhi">0</div><div class="l">Số bản ghi</div></div>
    <div class="cme-stat cme-stat-year"><div class="n" id="stNam"><?= $namNay ?></div><div class="l">Năm đang xem</div></div>
</div>

<div class="card">
    <div class="toolbar">
        <div class="left">
            <input type="text" id="search" class="form-control" placeholder="Tìm NV, hoạt động, loại..." style="max-width:260px">
            <select id="filterNam" class="form-select" style="max-width:120px"></select>
            <select id="filterKhoa" class="form-select" style="max-width:200px"><option value="0">-- Tất cả khoa/phòng --</option></select>
            <select id="filterNhom" class="form-select" style="max-width:210px"><option value="0">-- Tất cả nhóm --</option></select>
            <select id="filterDaXoa" class="form-select" style="max-width:140px">
                <option value="0">Đang dùng</option><option value="1">Thùng rác</option>
            </select>
        </div>
        <div class="right">
            <button type="button" class="btn" onclick="exportExcel()" title="Xuất Excel"><?= IconHelper::svg('download','16') ?> Xuất Excel</button>
            <?php if ($canAdd): ?><button type="button" class="btn btn-primary" onclick="openCreate()">+ Ghi nhận CME</button><?php endif; ?>
        </div>
    </div>
    <div class="table-wrap" id="tableWrap" style="position:relative;min-height:220px">
        <table class="table">
            <thead>
                <tr>
                    <th style="width:44px" class="text-center">#</th>
                    <th style="width:150px">Nhân viên</th>
                    <th>Hoạt động</th>
                    <th style="width:240px">Loại hình thức</th>
                    <th style="width:100px">Vai trò</th>
                    <th style="width:110px" class="text-center">Số lượng</th>
                    <th style="width:90px" class="text-right">Giờ TC</th>
                    <th style="width:60px" class="text-center">Năm</th>
                    <th style="width:170px" class="text-right">Hành động</th>
                </tr>
            </thead>
            <tbody id="tbody"></tbody>
        </table>
    </div>
    <div class="pagination-wrap"><div id="pageInfo" class="text-muted">-</div><div id="pageNav"></div></div>
</div>

<!-- Modal ghi nhận -->
<div class="modal-backdrop" id="modalForm">
    <div class="modal" style="max-width:660px">
        <div class="modal-header"><h3 id="modalTitle">Ghi nhận hoạt động CME</h3><button type="button" class="close" onclick="closeModal()">&times;</button></div>
        <form id="formMain" enctype="multipart/form-data">
            <div class="modal-body">
                <input type="hidden" name="id" id="f_id">
                <div class="form-row">
                    <div class="form-group"><label>Nhân viên <span class="required">*</span></label>
                        <input type="hidden" name="nhan_vien_id" id="f_nv">
                        <div class="nv-picker" id="nvPicker">
                            <div class="nv-chip" id="nvChip" style="display:none">
                                <span id="nvChipText"></span>
                                <button type="button" class="nv-chip-x" onclick="clearNv()" title="Bỏ chọn">&times;</button>
                            </div>
                            <input type="text" id="nvSearch" class="form-control" autocomplete="off" placeholder="Gõ tên hoặc mã nhân viên để tìm...">
                            <div class="nv-suggest" id="nvSuggest"></div>
                        </div>
                    </div>
                    <div class="form-group"><label>Năm <span class="required">*</span></label>
                        <input type="number" name="nam" id="f_nam" class="form-control" min="2000" max="2100" value="<?= $namNay ?>" required></div>
                </div>
                <div class="form-row">
                    <div class="form-group"><label>Nhóm hình thức</label>
                        <select id="f_nhom" class="form-select"><option value="0">-- Tất cả nhóm --</option></select></div>
                    <div class="form-group"><label>Loại hình thức <span class="required">*</span></label>
                        <select name="loai_id" id="f_loai" class="form-select" required></select></div>
                </div>
                <div class="form-group"><label>Tên hoạt động cụ thể</label>
                    <input type="text" name="ten_hoat_dong" id="f_hd" class="form-control" maxlength="400" placeholder="VD: Hội thảo Tim mạch can thiệp 2026"></div>
                <div class="form-row-3">
                    <div class="form-group"><label>Vai trò</label>
                        <input type="text" name="vai_tro" id="f_vaitro" class="form-control" maxlength="100" placeholder="Học viên/Báo cáo viên..."></div>
                    <div class="form-group"><label id="f_sl_label">Số lượng</label>
                        <input type="number" step="0.5" min="0" name="so_luong" id="f_sl" class="form-control" value="1"></div>
                    <div class="form-group"><label>Giờ tín chỉ (tự tính)</label>
                        <input type="text" id="f_gio_preview" class="form-control" readonly style="font-weight:700;color:#d1367f;background:#fbe8f1;border-color:#f3c4dc"></div>
                </div>
                <div class="cme-formula-hint" id="f_hint">Chọn loại hình thức để xem cách quy đổi.</div>
                <div class="form-row">
                    <div class="form-group"><label>Từ ngày</label><input type="date" name="ngay_bat_dau" id="f_nbd" class="form-control"></div>
                    <div class="form-group"><label>Đến ngày</label><input type="date" name="ngay_ket_thuc" id="f_nkt" class="form-control"></div>
                </div>
                <div class="form-group"><label>Ghi chú</label><textarea name="ghi_chu" id="f_gc" class="form-control" rows="2" maxlength="500"></textarea></div>

                <div class="form-group">
                    <label>Minh chứng (chứng chỉ PDF / ảnh)</label>
                    <input type="hidden" name="remove_minh_chung" id="f_remove_mc" value="0">
                    <div id="mcCurrent" class="mc-current" style="display:none">
                        <span class="mc-ic"><?= IconHelper::svg('file-text','16') ?></span>
                        <a href="#" target="_blank" id="mcLink" class="mc-name"></a>
                        <span class="mc-size" id="mcSize"></span>
                        <button type="button" class="mc-x" onclick="removeMinhChung()" title="Gỡ file">&times;</button>
                    </div>
                    <input type="file" name="minh_chung_file" id="f_mc" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                    <div class="text-muted" style="font-size:11.5px;margin-top:4px">Chấp nhận PDF, JPG, PNG — tối đa 10MB.</div>
                </div>
            </div>
            <div class="modal-footer"><button type="button" class="btn" onclick="closeModal()">Hủy</button><button type="submit" class="btn btn-primary">Lưu ghi nhận</button></div>
        </form>
    </div>
</div>

<!-- Drawer: Chi tiết bản ghi CME -->
<div class="drawer-backdrop" id="drawerCt">
    <div class="drawer" style="max-width:600px">
        <div class="drawer-header">
            <div><h3 style="margin:0">Chi tiết ghi nhận CME</h3>
                <div id="ctSub" class="text-muted" style="font-size:12.5px;margin-top:2px"></div></div>
            <button type="button" class="close" onclick="$('#drawerCt').removeClass('open').find('.drawer').removeClass('open')">&times;</button>
        </div>
        <div class="drawer-body" id="ctBody"><div class="hv-pane-loading">Đang tải...</div></div>
    </div>
</div>

<!-- Modal đính kèm nhanh minh chứng -->
<div class="modal-backdrop" id="modalMc">
    <div class="modal" style="max-width:520px">
        <div class="modal-header"><h3>Minh chứng</h3>
            <button type="button" class="close" onclick="$('#modalMc').removeClass('open')">&times;</button></div>
        <div class="modal-body">
            <div id="mcInfo" class="text-muted" style="font-size:12.5px;margin-bottom:10px"></div>
            <div id="mcHas" class="mc-current" style="display:none">
                <span class="mc-ic"><?= IconHelper::svg('file-text','16') ?></span>
                <a href="#" target="_blank" id="mcHasLink" class="mc-name"></a>
                <span class="mc-size" id="mcHasSize"></span>
            </div>
            <div class="form-group" style="margin-top:8px">
                <label id="mcPickLabel">Chọn file minh chứng</label>
                <input type="file" id="mcFile" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                <div class="text-muted" style="font-size:11.5px;margin-top:4px">PDF, JPG, PNG — tối đa 10MB.</div>
            </div>
        </div>
        <div class="modal-footer" style="justify-content:space-between">
            <button type="button" class="btn btn-danger btn-sm" id="btnMcGo" style="display:none">Gỡ minh chứng</button>
            <div style="margin-left:auto;display:flex;gap:8px">
                <button type="button" class="btn" onclick="$('#modalMc').removeClass('open')">Hủy</button>
                <button type="button" class="btn btn-primary" id="btnMcSave">Lưu</button>
            </div>
        </div>
    </div>
</div>

<!-- Drawer sổ theo dõi theo NV -->
<div class="drawer-backdrop" id="drawerSo">
    <div class="drawer" style="max-width:640px">
        <div class="drawer-header">
            <div><h3 id="soTitle" style="margin:0">Sổ theo dõi CME</h3>
                <div id="soSub" class="text-muted" style="font-size:12.5px;margin-top:2px"></div></div>
            <button type="button" class="close" onclick="$('#drawerSo').removeClass('open').find('.drawer').removeClass('open')">&times;</button>
        </div>
        <div class="drawer-body" id="soBody"><div class="hv-pane-loading">Đang tải...</div></div>
    </div>
</div>

<style>
    .cme-stats { display:grid; grid-template-columns:repeat(auto-fit,minmax(160px,1fr)); gap:1px; background:var(--gray-200);
        border:1px solid var(--gray-200); border-radius:10px; overflow:hidden; margin-bottom:16px; }
    .cme-stat { background:#fff; padding:16px 18px; }
    .cme-stat .n { font-size:24px; font-weight:700; color:var(--primary); font-family:ui-monospace,Menlo,monospace; line-height:1; }
    .cme-stat .l { font-size:12px; color:var(--gray-500); margin-top:6px; }
    .cme-stat-year .n { color:#b7791f; }
    .cme-kieu { display:inline-block; font-size:11px; font-weight:700; padding:1px 7px; border-radius:5px; }
    .cme-kieu.theo_tiet { background:#e7f5ec; color:#0f7a38; }
    .cme-kieu.co_dinh { background:#e6edfd; color:#2563eb; }
    .cme-kieu.theo_nam { background:#fbf1de; color:#b7791f; }
    .cme-gio { font-family:ui-monospace,Menlo,monospace; font-weight:700; color:#d1367f; }
    .cme-nv-link { color:var(--primary); cursor:pointer; font-weight:600; }
    .cme-nv-link:hover { text-decoration:underline; }
    .cme-formula-hint { font-size:12.5px; color:var(--gray-600); background:#f8fafc; border:1px solid var(--gray-200); border-radius:8px; padding:9px 12px; }
    /* Typeahead chọn nhân viên */
    .nv-picker { position:relative; }
    .nv-chip { display:inline-flex; align-items:center; gap:8px; background:var(--primary-tint,#e7f5ec); color:#0f7a38;
        border:1px solid #b6e2c6; border-radius:8px; padding:7px 10px; font-size:13.5px; font-weight:600; margin-bottom:6px; max-width:100%; }
    .nv-chip-x { border:none; background:none; color:#0f7a38; font-size:17px; line-height:1; cursor:pointer; padding:0 2px; }
    .nv-chip-x:hover { color:#b91c1c; }
    .nv-suggest { position:absolute; left:0; right:0; top:100%; z-index:20; background:#fff; border:1px solid var(--gray-200);
        border-radius:8px; box-shadow:0 6px 20px rgba(0,0,0,.12); margin-top:4px; max-height:280px; overflow-y:auto; display:none; }
    .nv-suggest.open { display:block; }
    .nv-sug-item { padding:9px 12px; cursor:pointer; border-bottom:1px solid var(--gray-100); font-size:13px; }
    .nv-sug-item:last-child { border-bottom:none; }
    .nv-sug-item:hover, .nv-sug-item.active { background:#f0fdf4; }
    .nv-sug-item .nm { font-weight:600; color:var(--gray-800); }
    .nv-sug-item .meta { font-size:11.5px; color:var(--gray-500); margin-top:1px; }
    .nv-sug-empty { padding:12px; text-align:center; color:var(--gray-500); font-size:12.5px; }
    /* Minh chứng */
    .mc-current { display:flex; align-items:center; gap:8px; padding:8px 10px; border:1px solid #b6e2c6;
        background:#e7f5ec; border-radius:8px; margin-bottom:6px; }
    .mc-current .mc-ic { color:#0f7a38; display:inline-flex; flex:0 0 auto; }
    .mc-name { color:#0f7a38; font-weight:600; font-size:13px; text-decoration:underline; flex:1;
        overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
    .mc-size { font-size:11.5px; color:var(--gray-500); flex:0 0 auto; }
    .mc-x { border:none; background:none; color:#0f7a38; font-size:18px; line-height:1; cursor:pointer; padding:0 2px; }
    .mc-x:hover { color:#b91c1c; }
    .cme-mc-link { color:var(--primary); display:inline-flex; }
    .cme-mc-link:hover { opacity:.7; }
    /* Nút đính kèm ở cột Hành động: nổi bật khi ĐÃ có minh chứng */
    .btn-mc-has { background:#e7f5ec !important; border-color:#b6e2c6 !important; color:#0f7a38 !important; }
    .btn-mc-has:hover { background:#d6efe0 !important; }
    /* Drawer chi tiết bản ghi */
    .ct-hero { padding:16px 18px; border-radius:12px; background:linear-gradient(135deg,#16a34a,#0f766e);
        color:#fff; margin-bottom:16px; }
    .ct-hero .big { font-size:30px; font-weight:800; font-family:ui-monospace,Menlo,monospace; line-height:1; }
    .ct-hero .big .unit { font-size:14px; font-weight:600; font-family:inherit; opacity:.9; }
    .ct-hero .who { font-size:13px; opacity:.95; margin-top:7px; }
    .ct-sec { font-size:12px; font-weight:700; color:var(--gray-500); text-transform:uppercase;
        letter-spacing:.04em; margin:18px 0 8px; }
    .ct-grid { display:grid; grid-template-columns:repeat(2,1fr); gap:1px; background:var(--gray-200);
        border:1px solid var(--gray-200); border-radius:8px; overflow:hidden; }
    .ct-item { background:#fff; padding:9px 12px; display:flex; flex-direction:column; gap:2px; }
    .ct-item.full { grid-column:1/-1; }
    .ct-lbl { font-size:11px; color:var(--gray-500); text-transform:uppercase; letter-spacing:.02em; }
    .ct-val { font-size:13px; color:var(--gray-800); font-weight:500; word-break:break-word; }
    .ct-nomc { padding:12px; border:1px dashed var(--gray-300); border-radius:8px; color:var(--gray-500);
        font-size:12.5px; text-align:center; }
    .ct-nomc a { color:var(--primary); font-weight:600; }
    .ct-note { padding:10px 12px; background:#f8fafc; border:1px solid var(--gray-200); border-radius:8px;
        font-size:12.5px; color:var(--gray-700); white-space:pre-wrap; }
    .ct-meta { margin-top:16px; padding-top:12px; border-top:1px solid var(--gray-200);
        font-size:11.5px; color:var(--gray-500); line-height:1.6; }
    @media (max-width:560px){ .ct-grid { grid-template-columns:1fr; } }
    /* Sổ theo dõi */
    .so-hero { padding:18px; background:linear-gradient(135deg,#16a34a,#0f766e); color:#fff; border-radius:12px; margin-bottom:16px; }
    .so-hero .big { font-size:34px; font-weight:800; font-family:ui-monospace,Menlo,monospace; line-height:1; }
    .so-hero .unit { font-size:14px; opacity:.9; }
    .so-hero .cycle { font-size:12.5px; opacity:.9; margin-top:6px; }
    .so-bar { height:10px; background:rgba(255,255,255,.25); border-radius:6px; overflow:hidden; margin-top:12px; }
    .so-bar > span { display:block; height:100%; background:#fff; }
    .so-badge { display:inline-block; font-size:12px; font-weight:700; padding:3px 10px; border-radius:100px; margin-top:10px; }
    .so-badge.dat { background:#dcfce7; color:#166534; } .so-badge.chua { background:#fef3c7; color:#92400e; }
    .so-sec-title { font-size:13px; font-weight:700; color:var(--gray-700); margin:18px 0 8px; }
    .so-nhom { display:flex; justify-content:space-between; padding:9px 12px; border:1px solid var(--gray-200); border-radius:8px; margin-bottom:6px; font-size:13px; }
    .so-nhom .g { font-family:ui-monospace,Menlo,monospace; font-weight:700; color:#0f7a38; }
    .so-item { padding:9px 12px; border-bottom:1px solid var(--gray-100); font-size:13px; }
    .so-item:last-child { border-bottom:none; }
    .so-item .meta { color:var(--gray-500); font-size:11.5px; margin-top:2px; }
</style>

<script>
var URL = APP_BASE + 'GUI/DT_CME/ajax_handler.php';
var CAN_EDIT = <?= $canEdit?'true':'false' ?>, CAN_DEL = <?= $canDel?'true':'false' ?>;
var ICON_EDIT = '<?= addslashes(IconHelper::svg('edit','16')) ?>';
var ICON_TRASH = '<?= addslashes(IconHelper::svg('trash','16')) ?>';
var ICON_RESTORE = '<?= addslashes(IconHelper::svg('refresh','16')) ?>';
var ICON_FILE = '<?= addslashes(IconHelper::svg('file-text','15')) ?>';
var ICON_CLIP = '<?= addslashes(IconHelper::svg('paperclip','16')) ?>';
var ICON_VIEW = '<?= addslashes(IconHelper::svg('eye','16')) ?>';
var NAM_NAY = <?= $namNay ?>;
var state = { page:1, pageSize:20, search:'', nam:NAM_NAY, khoa:0, nhom:0, daXoa:0 };
var loaiComboAll=[], nhomCombo=[];
var KIEU_LABEL={theo_tiet:'Theo tiết',co_dinh:'Cố định',theo_nam:'Theo năm'};

function fmtGio(n){ n=parseFloat(n)||0; return (n%1===0)?n.toFixed(0):n.toString(); }

function exportExcel(){
    var p=new URLSearchParams({search:state.search||'',nam:state.nam||0,khoa_phong_id:state.khoa||0,nhom_id:state.nhom||0,da_xoa:state.daXoa||0});
    window.location=APP_BASE+'GUI/DT_CME/export.php?'+p.toString();
}

function loadStats(){
    APP.ajax(URL,{action:'getStats',nam:state.nam}).done(function(res){
        if(!res.success)return; var d=res.data;
        $('#stTong').text(fmtGio(d.tong_gio)); $('#stNv').text(d.so_nhan_vien||0);
        $('#stBanGhi').text(d.so_ban_ghi||0); $('#stNam').text(state.nam);
    });
}

function load(){
    APP.showLoading('#tableWrap');
    APP.ajax(URL,{action:'getPaged',page:state.page,pageSize:state.pageSize,search:state.search,
        nam:state.nam,khoa_phong_id:state.khoa,nhom_id:state.nhom,da_xoa:state.daXoa})
    .done(function(res){
        APP.hideLoading('#tableWrap');
        if(!res.success){APP.toast(res.message,'error');return;}
        var $tb=$('#tbody').empty();
        if(!res.data.length){ $tb.append('<tr><td colspan="9"><div class="empty-state">Chưa có ghi nhận nào</div></td></tr>'); }
        var stt=(state.page-1)*state.pageSize;
        res.data.forEach(function(r){
            stt++;
            var act='';
            if(state.daXoa==0){
                act+='<button class="btn btn-sm" title="Xem chi tiết" onclick="openChiTiet('+r.id+')">'+ICON_VIEW+'</button>';
                if(CAN_EDIT) act+='<button class="btn btn-sm'+(r.minh_chung?' btn-mc-has':'')+'" title="'
                    +(r.minh_chung?'Minh chứng: '+APP.escape(r.minh_chung_goc||'')+' — bấm để đổi/gỡ':'Đính kèm minh chứng')
                    +'" onclick="openMc('+r.id+')">'+ICON_CLIP+'</button>';
                if(CAN_EDIT) act+='<button class="btn btn-sm" title="Sửa" onclick="openEdit('+r.id+')">'+ICON_EDIT+'</button>';
                if(CAN_DEL)  act+='<button class="btn btn-sm btn-danger" title="Xóa" onclick="trashItem('+r.id+')">'+ICON_TRASH+'</button>';
            } else if(CAN_EDIT){ act+='<button class="btn btn-sm btn-success" title="Khôi phục" onclick="restoreItem('+r.id+')">'+ICON_RESTORE+'</button>'; }
            var sl = fmtGio(r.so_luong) + (r.don_vi_tinh ? ' '+APP.escape(r.don_vi_tinh) : '');
            var mc = r.minh_chung
                ? ' <a class="cme-mc-link" href="'+APP_BASE+'GUI/DT_CME/tai_minh_chung.php?id='+r.id+'" target="_blank" title="Xem minh chứng: '+APP.escape(r.minh_chung_goc||'')+'" onclick="event.stopPropagation()">'+ICON_FILE+'</a>'
                : '';
            $tb.append('<tr>'+
                '<td class="text-center">'+stt+'</td>'+
                '<td><span class="cme-nv-link" onclick="openSo('+r.nhan_vien_id+','+r.nam+')">'+APP.escape(r.ho_ten_nhan_vien||'')+'</span>'+
                    '<div class="text-muted" style="font-size:11px">'+APP.escape(r.ma_nv||'')+'</div></td>'+
                '<td>'+APP.escape(r.ten_hoat_dong||'-')+mc+'</td>'+
                '<td>'+APP.escape(r.ten_loai||'-')+'</td>'+
                '<td>'+APP.escape(r.vai_tro||'-')+'</td>'+
                '<td class="text-center">'+sl+'</td>'+
                '<td class="text-right"><span class="cme-gio">'+fmtGio(r.gio_tin_chi)+'</span></td>'+
                '<td class="text-center">'+r.nam+'</td>'+
                '<td><div class="actions">'+act+'</div></td></tr>');
        });
        renderInfo(res.pagination);
    });
}
function renderInfo(p){
    var from=(p.currentPage-1)*p.pageSize+1, to=Math.min(from+p.pageSize-1,p.totalRecords);
    $('#pageInfo').text(p.totalRecords?('Hiển thị '+from+'-'+to+' / '+p.totalRecords):'Không có bản ghi');
    $('#pageNav').html(APP.renderPagination(p));
}
$('#pageNav').on('click','button[data-p]',function(){ var p=parseInt($(this).data('p'),10); if(!p||p===state.page)return; state.page=p; load(); });

// Filters
$('#search').on('input', APP.debounce(function(){ state.search=$(this).val(); state.page=1; load(); },400));
$('#filterNam').on('change', function(){ state.nam=parseInt(this.value,10)||NAM_NAY; state.page=1; load(); loadStats(); });
$('#filterKhoa').on('change', function(){ state.khoa=parseInt(this.value,10)||0; state.page=1; load(); });
$('#filterNhom').on('change', function(){ state.nhom=parseInt(this.value,10)||0; state.page=1; load(); });
$('#filterDaXoa').on('change', function(){ state.daXoa=parseInt(this.value,10)||0; state.page=1; load(); });

// ===== Modal ghi nhận =====
function openCreate(){
    $('#formMain')[0].reset(); $('#f_id').val(''); $('#f_nam').val(state.nam||NAM_NAY);
    $('#f_gio_preview').val(''); $('#f_hint').text('Chọn loại hình thức để xem cách quy đổi.');
    clearNv(); $('#f_nhom').val('0'); fillLoaiSelect(0,'');
    showMinhChung(null);
    $('#modalTitle').text('Ghi nhận hoạt động CME'); $('#modalForm').addClass('open');
}
function openEdit(id){
    APP.ajax(URL,{action:'getById',id:id}).done(function(res){
        if(!res.success){APP.toast(res.message,'error');return;}
        var e=res.data;
        setNv(e.nhan_vien_id, e.ho_ten_nhan_vien, e.ma_nv, e.ten_khoa_phong);
        $('#f_nhom').val(e.nhom_id||0); fillLoaiSelect(e.nhom_id||0, e.loai_id);
        $('#f_id').val(e.id); $('#f_nam').val(e.nam); $('#f_hd').val(e.ten_hoat_dong||'');
        $('#f_vaitro').val(e.vai_tro||''); $('#f_sl').val(e.so_luong); $('#f_nbd').val(e.ngay_bat_dau||'');
        $('#f_nkt').val(e.ngay_ket_thuc||''); $('#f_gc').val(e.ghi_chu||'');
        showMinhChung(e);
        tinhThu();
        $('#modalTitle').text('Sửa ghi nhận CME'); $('#modalForm').addClass('open');
    });
}

// ===== Typeahead chọn nhân viên =====
function setNv(id, hoTen, maNv, khoa){
    $('#f_nv').val(id||'');
    if(id){
        $('#nvChipText').text((hoTen||'')+(maNv?(' ('+maNv+')'):'')+(khoa?(' — '+khoa):''));
        $('#nvChip').show(); $('#nvSearch').hide().val('');
    } else { $('#nvChip').hide(); $('#nvSearch').show(); }
    $('#nvSuggest').removeClass('open').empty();
}
function clearNv(){ setNv('',''); $('#nvSearch').show().val('').focus(); }

var nvSugTimer=null;
$('#nvSearch').on('input', function(){
    var q=$(this).val().trim();
    clearTimeout(nvSugTimer);
    if(q.length<1){ $('#nvSuggest').removeClass('open').empty(); return; }
    nvSugTimer=setTimeout(function(){ doSearchNv(q); }, 300);
});
$('#nvSearch').on('focus', function(){ var q=$(this).val().trim(); if(q.length>=1) doSearchNv(q); });
function doSearchNv(q){
    APP.ajax(URL,{action:'searchNhanVien',q:q,khoa_phong_id:0}).done(function(res){
        if(!res.success)return;
        var $s=$('#nvSuggest').empty().addClass('open');
        if(!res.data.length){ $s.html('<div class="nv-sug-empty">Không tìm thấy nhân viên phù hợp</div>'); return; }
        res.data.forEach(function(nv){
            var $it=$('<div class="nv-sug-item"><div class="nm">'+APP.escape(nv.ho_ten)+'</div>'+
                '<div class="meta">'+APP.escape(nv.ma_nv||'')+(nv.ten_khoa_phong?(' · '+APP.escape(nv.ten_khoa_phong)):'')+'</div></div>');
            $it.on('click', function(){ setNv(nv.id, nv.ho_ten, nv.ma_nv, nv.ten_khoa_phong); });
            $s.append($it);
        });
    });
}
// đóng suggest khi click ngoài
$(document).on('click', function(e){
    if(!$(e.target).closest('#nvPicker').length) $('#nvSuggest').removeClass('open');
});
function closeModal(){ $('#modalForm').removeClass('open'); }

$('#f_nhom').on('change', function(){ fillLoaiSelect(parseInt(this.value,10)||0,''); tinhThu(); });
$('#f_loai, #f_sl').on('change input', APP.debounce(tinhThu, 250));

function tinhThu(){
    var loaiId=parseInt($('#f_loai').val(),10)||0;
    if(!loaiId){ $('#f_gio_preview').val(''); return; }
    APP.ajax(URL,{action:'tinhThu',loai_id:loaiId,so_luong:$('#f_sl').val()||0}).done(function(res){
        if(!res.success)return; var d=res.data;
        $('#f_gio_preview').val(fmtGio(d.gio_tin_chi)+' giờ');
        var lbl=(d.kieu_quy_doi==='theo_nam')?'Số lượng (bỏ qua — khoán năm)':'Số lượng ('+(d.don_vi_tinh||'lần')+')';
        $('#f_sl_label').text(lbl);
        var hint='';
        if(d.kieu_quy_doi==='theo_tiet') hint='Theo tiết: '+fmtGio(d.gia_tri_quy_doi)+' giờ × số tiết.';
        else if(d.kieu_quy_doi==='theo_nam') hint='Theo năm: khoán '+fmtGio(d.gia_tri_quy_doi)+' giờ/năm (không phụ thuộc số lượng).';
        else hint='Cố định: '+fmtGio(d.gia_tri_quy_doi)+' giờ × số lượng.';
        $('#f_hint').text(hint);
    });
}

$('#formMain').on('submit', function(e){
    e.preventDefault();
    if(!$('#f_nv').val()){ APP.toast('Chưa chọn nhân viên','warning'); $('#nvSearch').show().focus(); return; }
    // Dùng FormData vì có file đính kèm
    var fd = new FormData(this);
    fd.append('action', $('#f_id').val() ? 'update' : 'insert');
    var $btn = $(this).find('button[type=submit]').prop('disabled', true);
    $.ajax({ url: URL, type:'POST', data: fd, processData:false, contentType:false, dataType:'json',
        headers: window.CSRF_TOKEN ? {'X-CSRF-Token': window.CSRF_TOKEN} : {} })
    .done(function(res){
        $btn.prop('disabled', false);
        if(res.success){ APP.toast(res.message,'success'); closeModal(); load(); loadStats(); }
        else APP.toast(res.message,'error');
    })
    .fail(function(){ $btn.prop('disabled', false); APP.toast('Lỗi máy chủ','error'); });
});

// ===== Xem chi tiết bản ghi =====
function openChiTiet(id){
    $('#ctBody').html('<div class="hv-pane-loading">Đang tải...</div>');
    $('#ctSub').text('');
    $('#drawerCt').addClass('open').find('.drawer').addClass('open');
    APP.ajax(URL, {action:'getById', id:id}).done(function(res){
        if(!res.success){ $('#ctBody').html('<div class="empty-state">'+APP.escape(res.message)+'</div>'); return; }
        var e = res.data;
        $('#ctSub').text('Năm ' + e.nam + ' · ' + (e.ma_nv||''));

        function row(lbl, val, full){
            return '<div class="ct-item'+(full?' full':'')+'"><span class="ct-lbl">'+lbl+'</span>'
                 + '<span class="ct-val">'+(val ? APP.escape(String(val)) : '—')+'</span></div>';
        }
        var kieuTxt = KIEU_LABEL[e.kieu_quy_doi] || e.kieu_quy_doi || '';
        var h = '';
        // Hero: giờ tín chỉ
        h += '<div class="ct-hero"><div class="big">'+fmtGio(e.gio_tin_chi)+'<span class="unit"> giờ tín chỉ</span></div>'
           + '<div class="who">'+APP.escape(e.ho_ten_nhan_vien||'')+(e.ten_khoa_phong?' · '+APP.escape(e.ten_khoa_phong):'')+'</div></div>';

        h += '<div class="ct-sec">Hoạt động</div><div class="ct-grid">'
           + row('Tên hoạt động', e.ten_hoat_dong, true)
           + row('Nhóm hình thức', e.ten_nhom, true)
           + row('Loại hình thức', e.ten_loai, true)
           + row('Vai trò', e.vai_tro)
           + row('Số lượng', fmtGio(e.so_luong) + (e.don_vi_tinh ? ' ' + e.don_vi_tinh : ''))
           + row('Kiểu quy đổi', kieuTxt)
           + row('Giờ tín chỉ', fmtGio(e.gio_tin_chi) + ' giờ')
           + row('Năm kê khai', e.nam)
           + row('Thời gian', (e.ngay_bat_dau ? APP.formatDate(e.ngay_bat_dau) : '') +
                 (e.ngay_ket_thuc ? ' → ' + APP.formatDate(e.ngay_ket_thuc) : ''), true)
           + '</div>';

        h += '<div class="ct-sec">Nhân viên</div><div class="ct-grid">'
           + row('Mã NV', e.ma_nv)
           + row('Họ tên', e.ho_ten_nhan_vien)
           + row('Khoa / Phòng', e.ten_khoa_phong, true)
           + '</div>';

        // Minh chứng
        h += '<div class="ct-sec">Minh chứng</div>';
        if (e.minh_chung) {
            h += '<div class="mc-current"><span class="mc-ic">'+ICON_FILE+'</span>'
               + '<a class="mc-name" target="_blank" href="'+APP_BASE+'GUI/DT_CME/tai_minh_chung.php?id='+e.id+'">'
               + APP.escape(e.minh_chung_goc||e.minh_chung)+'</a>'
               + '<span class="mc-size">'+(e.minh_chung_size?fmtSize(e.minh_chung_size):'')+'</span>'
               + '<a class="btn btn-sm" style="flex:0 0 auto" target="_blank" href="'+APP_BASE+'GUI/DT_CME/tai_minh_chung.php?id='+e.id+'&tai=1">Tải về</a></div>';
        } else {
            h += '<div class="ct-nomc">Chưa có minh chứng đính kèm.'
               + (CAN_EDIT ? ' <a href="#" onclick="closeCt(); openMc('+e.id+'); return false;">Đính kèm ngay</a>' : '')
               + '</div>';
        }

        if (e.ghi_chu) h += '<div class="ct-sec">Ghi chú</div><div class="ct-note">'+APP.escape(e.ghi_chu)+'</div>';

        // Footer info + hành động
        h += '<div class="ct-meta">Tạo bởi '+APP.escape(e.tai_khoan_nguoi_tao||'—')
           + (e.ngay_tao ? ' lúc '+APP.formatDateTime(e.ngay_tao) : '')
           + (e.tai_khoan_nguoi_cap_nhat ? '<br>Cập nhật bởi '+APP.escape(e.tai_khoan_nguoi_cap_nhat)
              + (e.ngay_cap_nhat ? ' lúc '+APP.formatDateTime(e.ngay_cap_nhat) : '') : '')
           + '</div>';

        var btns = '<button class="btn btn-sm" onclick="closeCt(); openSo('+e.nhan_vien_id+','+e.nam+')">Sổ theo dõi của NV</button>';
        if (CAN_EDIT) btns += ' <button class="btn btn-sm btn-primary" onclick="closeCt(); openEdit('+e.id+')">Sửa</button>';
        h += '<div style="margin-top:16px;display:flex;gap:8px;flex-wrap:wrap">'+btns+'</div>';

        $('#ctBody').html(h);
    });
}
function closeCt(){ $('#drawerCt').removeClass('open').find('.drawer').removeClass('open'); }

// ===== Đính kèm nhanh minh chứng (từ cột Hành động) =====
var mcCtx = { id: 0 };
function openMc(id){
    mcCtx.id = id;
    $('#mcFile').val('');
    $('#mcHas').hide(); $('#btnMcGo').hide();
    $('#mcInfo').text('Đang tải...');
    $('#modalMc').addClass('open');
    APP.ajax(URL, {action:'getById', id:id}).done(function(res){
        if(!res.success){ APP.toast(res.message,'error'); $('#modalMc').removeClass('open'); return; }
        var e = res.data;
        $('#mcInfo').html('<b>'+APP.escape(e.ho_ten_nhan_vien||'')+'</b> — '+APP.escape(e.ten_hoat_dong||e.ten_loai||'')
                          +' · <span class="cme-gio">'+fmtGio(e.gio_tin_chi)+' giờ</span>');
        if(e.minh_chung){
            $('#mcHasLink').text(e.minh_chung_goc||e.minh_chung)
                           .attr('href', APP_BASE+'GUI/DT_CME/tai_minh_chung.php?id='+e.id);
            $('#mcHasSize').text(e.minh_chung_size ? fmtSize(e.minh_chung_size) : '');
            $('#mcHas').show(); $('#btnMcGo').show();
            $('#mcPickLabel').text('Chọn file khác để thay thế');
        } else {
            $('#mcPickLabel').text('Chọn file minh chứng');
        }
    });
}
$('#btnMcSave').on('click', function(){
    var f = $('#mcFile')[0].files[0];
    if(!f){ APP.toast('Chưa chọn file','warning'); return; }
    var fd = new FormData();
    fd.append('action','capNhatMinhChung'); fd.append('id', mcCtx.id); fd.append('minh_chung_file', f);
    var $b=$(this).prop('disabled',true).text('Đang tải lên...');
    $.ajax({url:URL, type:'POST', data:fd, processData:false, contentType:false, dataType:'json',
        headers: window.CSRF_TOKEN ? {'X-CSRF-Token':window.CSRF_TOKEN} : {}})
    .done(function(res){
        $b.prop('disabled',false).text('Lưu');
        if(res.success){ APP.toast(res.message,'success'); $('#modalMc').removeClass('open'); load(); }
        else APP.toast(res.message,'error');
    })
    .fail(function(){ $b.prop('disabled',false).text('Lưu'); APP.toast('Lỗi máy chủ','error'); });
});
$('#btnMcGo').on('click', function(){
    APP.confirm('Gỡ file minh chứng của bản ghi này?', function(){
        APP.ajax(URL, {action:'capNhatMinhChung', id:mcCtx.id, go:1}).done(function(res){
            if(res.success){ APP.toast(res.message,'success'); $('#modalMc').removeClass('open'); load(); }
            else APP.toast(res.message,'error');
        });
    });
});

// ===== Minh chứng =====
function removeMinhChung(){
    $('#f_remove_mc').val('1');
    $('#mcCurrent').hide();
    $('#f_mc').show().val('');
}
function fmtSize(b){
    b = parseInt(b,10)||0;
    if(b < 1024) return b+' B';
    if(b < 1048576) return (b/1024).toFixed(1)+' KB';
    return (b/1048576).toFixed(1)+' MB';
}
function showMinhChung(e){
    $('#f_remove_mc').val('0'); $('#f_mc').val('');
    if(e && e.minh_chung){
        $('#mcLink').text(e.minh_chung_goc || e.minh_chung)
                    .attr('href', APP_BASE + 'GUI/DT_CME/tai_minh_chung.php?id=' + e.id);
        $('#mcSize').text(e.minh_chung_size ? fmtSize(e.minh_chung_size) : '');
        $('#mcCurrent').show(); $('#f_mc').hide();
    } else {
        $('#mcCurrent').hide(); $('#f_mc').show();
    }
}
function trashItem(id){ APP.confirm('Chuyển ghi nhận này vào thùng rác?', function(){ APP.ajax(URL,{action:'trash',id:id}).done(function(res){ res.success?(APP.toast(res.message,'success'),load(),loadStats()):APP.toast(res.message,'error'); }); }); }
function restoreItem(id){ APP.ajax(URL,{action:'restore',id:id}).done(function(res){ res.success?(APP.toast(res.message,'success'),load(),loadStats()):APP.toast(res.message,'error'); }); }

// ===== Sổ theo dõi (drawer) =====
function openSo(nvId, nam){
    $('#soBody').html('<div class="hv-pane-loading">Đang tải...</div>');
    $('#drawerSo').addClass('open').find('.drawer').addClass('open');
    APP.ajax(URL,{action:'soTheoDoi',nhan_vien_id:nvId,nam:nam||state.nam}).done(function(res){
        if(!res.success){ $('#soBody').html('<div class="empty-state">'+APP.escape(res.message)+'</div>'); return; }
        var d=res.data, nv=d.nhan_vien;
        $('#soTitle').text(nv.ho_ten); $('#soSub').text((nv.ma_nv||'')+' · Năm '+d.nam);
        var h='';
        h+='<div class="so-hero"><div class="big">'+fmtGio(d.tong_gio_chu_ky)+' <span class="unit">/ '+fmtGio(d.nguong.gio)+' giờ tín chỉ</span></div>';
        h+='<div class="cycle">Chu kỳ '+d.tu_nam+'–'+d.den_nam+' ('+d.nguong.chu_ky_nam+' năm) · Riêng năm '+d.nam+': <b>'+fmtGio(d.tong_gio_nam)+'</b> giờ</div>';
        h+='<div class="so-bar"><span style="width:'+d.phan_tram+'%"></span></div>';
        h+='<span class="so-badge '+(d.dat?'dat':'chua')+'">'+(d.dat?'✓ Đạt ngưỡng':d.phan_tram+'% — chưa đạt')+'</span></div>';

        h+='<div class="so-sec-title">Theo nhóm hình thức (năm '+d.nam+')</div>';
        if(!d.theo_nhom.length){ h+='<div class="text-muted" style="font-size:13px">Chưa có ghi nhận trong năm.</div>'; }
        d.theo_nhom.forEach(function(n){ h+='<div class="so-nhom"><span>'+APP.escape(n.ten_nhom)+' ('+n.so_ban_ghi+')</span><span class="g">'+fmtGio(n.gio)+' giờ</span></div>'; });

        h+='<div class="so-sec-title">Hoạt động chi tiết (năm '+d.nam+')</div>';
        if(!d.hoat_dong.length){ h+='<div class="text-muted" style="font-size:13px">Không có.</div>'; }
        d.hoat_dong.forEach(function(a){
            var mc = a.minh_chung
                ? ' <a class="cme-mc-link" href="'+APP_BASE+'GUI/DT_CME/tai_minh_chung.php?id='+a.id+'" target="_blank" title="Xem minh chứng">'+ICON_FILE+'</a>'
                : '';
            h+='<div class="so-item"><div><b>'+APP.escape(a.ten_hoat_dong||a.ten_loai||'-')+'</b>'+mc+' — <span class="cme-gio">'+fmtGio(a.gio_tin_chi)+' giờ</span></div>'+
               '<div class="meta">'+APP.escape(a.ten_loai||'')+(a.vai_tro?(' · '+APP.escape(a.vai_tro)):'')+' · SL '+fmtGio(a.so_luong)+'</div></div>';
        });
        $('#soBody').html(h);
    });
}

// ===== Combos =====
function fillLoaiSelect(nhomId, val){
    var $s=$('#f_loai').empty().append('<option value="">-- Chọn loại --</option>');
    loaiComboAll.forEach(function(l){
        if(nhomId && parseInt(l.nhom_id,10)!==nhomId) return;
        $s.append('<option value="'+l.id+'">'+APP.escape(l.ten_loai)+'</option>');
    });
    if(val) $s.val(val);
}
function initCombos(){
    APP.ajax(URL,{action:'getLoaiCombo'}).done(function(r){ if(r.success){ loaiComboAll=r.data||[]; fillLoaiSelect(0,''); } });
    APP.ajax(URL,{action:'getNhomCombo'}).done(function(r){
        if(!r.success)return; nhomCombo=r.data||[];
        var $ff=$('#filterNhom'), $fn=$('#f_nhom');
        nhomCombo.forEach(function(n){
            $ff.append('<option value="'+n.id+'">'+APP.escape(n.ten_nhom)+'</option>');
            $fn.append('<option value="'+n.id+'">'+APP.escape(n.ten_nhom)+'</option>');
        });
    });
    APP.ajax(URL,{action:'getKhoaPhongCombo'}).done(function(r){
        if(!r.success)return; var $f=$('#filterKhoa');
        (r.data||[]).forEach(function(k){ $f.append('<option value="'+k.id+'">'+APP.escape(k.ten_khoa)+'</option>'); });
    });
    APP.ajax(URL,{action:'getNamCombo'}).done(function(r){
        var years=(r.success&&r.data&&r.data.length)?r.data.map(Number):[];
        if(years.indexOf(NAM_NAY)<0) years.unshift(NAM_NAY);
        var $f=$('#filterNam').empty();
        years.forEach(function(y){ $f.append('<option value="'+y+'">'+y+'</option>'); });
        $f.val(state.nam);
    });
}

initCombos(); load(); loadStats();
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
