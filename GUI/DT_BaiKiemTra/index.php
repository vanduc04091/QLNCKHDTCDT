<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../BUS/DT_KhoaHocChuongTrinh_BUS.php';
require_once __DIR__ . '/../../BUS/DT_KhoaHoc_BUS.php';

Helper::requireLogin();
if (!PhanQuyenHelper::hasQuyen('DT_BaiKiemTra', PhanQuyenHelper::QUYEN_XEM)) {
    echo 'Bạn không có quyền truy cập chức năng này.'; exit;
}
$canAdd = PhanQuyenHelper::hasQuyen('DT_BaiKiemTra', PhanQuyenHelper::QUYEN_THEM);
$canEdit = PhanQuyenHelper::hasQuyen('DT_BaiKiemTra', PhanQuyenHelper::QUYEN_SUA);
$canDel = PhanQuyenHelper::hasQuyen('DT_BaiKiemTra', PhanQuyenHelper::QUYEN_XOA);

$lopList = DT_KhoaHocChuongTrinh_BUS::getCombo();
$khoaList = DT_KhoaHoc_BUS::getCombo();

$pageTitle = 'Bài kiểm tra';
$activeMenu = 'DT_BaiKiemTra';
require __DIR__ . '/../layouts/header.php';
?>
<div class="breadcrumb">
    <a href="<?= AppConfig::baseUrl('GUI/dashboard/index.php') ?>">Trang chủ</a>
    <span class="sep">›</span> Đào tạo
    <span class="sep">›</span> <span>Bài kiểm tra</span>
</div>

<div class="hv-stats">
    <div class="hv-stat">
        <div class="hv-stat-icon hv-stat-blue">
            <?= IconHelper::svg('clipboard-list', '22') ?>
        </div>
        <div><div class="hv-stat-label">Tổng bài</div><div class="hv-stat-value" id="stTotal">—</div></div>
    </div>
    <div class="hv-stat">
        <div class="hv-stat-icon hv-stat-orange">
            <?= IconHelper::svg('clock', '22') ?>
        </div>
        <div><div class="hv-stat-label">Thường xuyên</div><div class="hv-stat-value" id="stTX">—</div></div>
    </div>
    <div class="hv-stat">
        <div class="hv-stat-icon hv-stat-purple">
            <?= IconHelper::svg('trending-up', '22') ?>
        </div>
        <div><div class="hv-stat-label">Giữa kỳ / Cuối kỳ</div><div class="hv-stat-value" id="stKy">—</div></div>
    </div>
    <div class="hv-stat">
        <div class="hv-stat-icon hv-stat-green">
            <?= IconHelper::svg('check', '22') ?>
        </div>
        <div><div class="hv-stat-label">Đã có đáp án</div><div class="hv-stat-value" id="stDA">—</div></div>
    </div>
</div>

<div class="card">
    <div class="lh-toolbar">
        <div class="lh-toolbar-left" style="flex:1">
            <input type="text" id="search" class="form-control" placeholder="Tìm mã, tiêu đề..." style="max-width:280px">
        </div>
        <div class="lh-toolbar-right">
            <button type="button" class="btn" onclick="exportExcel()" title="Xuất Excel"><?= IconHelper::svg('download','16') ?> Xuất Excel</button>
            <?php if ($canAdd): ?>
                <button type="button" class="btn btn-primary" onclick="openCreate()">
                    <?= IconHelper::svg('plus', '16') ?>
                    Thêm bài kiểm tra
                </button>
            <?php endif; ?>
        </div>
    </div>

    <div class="lh-filter">
        <div class="lh-filter-field">
            <label>Chương trình đào tạo</label>
            <select id="fLop" class="form-select">
                <option value="0">Tất cả chương trình</option>
                <?php foreach ($lopList as $l): ?>
                    <option value="<?= $l['id'] ?>"><?= Helper::h($l['label']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="lh-filter-field">
            <label>Loại bài</label>
            <select id="fLoai" class="form-select">
                <option value="0">Tất cả loại</option>
                <option value="1">Thường xuyên</option>
                <option value="2">Giữa kỳ</option>
                <option value="3">Cuối kỳ</option>
                <option value="4">Ôn tập</option>
            </select>
        </div>
        <div class="lh-filter-field">
            <label>Trạng thái</label>
            <select id="fTT" class="form-select">
                <option value="">Tất cả</option>
                <option value="0">Nháp</option>
                <option value="1">Đang dùng</option>
                <option value="2">Lưu trữ</option>
            </select>
        </div>
    </div>

    <div class="table-wrap" id="bktWrap" style="position:relative;min-height:200px;padding:0 18px 8px">
        <table class="table">
            <thead>
                <tr>
                    <th style="width:44px" class="text-center">#</th>
                    <th style="width:130px">Mã</th>
                    <th>Tiêu đề</th>
                    <th style="width:140px">Loại</th>
                    <th style="width:200px">Chương trình</th>
                    <th style="width:120px">Ngày KT</th>
                    <th style="width:90px" class="text-center">Đề</th>
                    <th style="width:110px" class="text-center">Đáp án</th>
                    <th style="width:120px" class="text-center">Trạng thái</th>
                    <th class="text-right" style="width:140px">Hành động</th>
                </tr>
            </thead>
            <tbody id="tbody"></tbody>
        </table>
    </div>
    <div class="pagination-wrap">
        <div id="pageInfo" class="text-muted">-</div>
        <div id="pageNav"></div>
    </div>
</div>

<!-- Modal Form -->
<div class="modal-backdrop" id="modalForm">
    <div class="modal" style="max-width:780px">
        <div class="modal-header">
            <h3 id="modalTitle">Thêm bài kiểm tra</h3>
            <button type="button" class="close" onclick="closeModal()">&times;</button>
        </div>
        <form id="formBKT" enctype="multipart/form-data">
            <div class="modal-body">
                <input type="hidden" name="id" id="f_id">

                <div class="form-row">
                    <div class="form-group">
                        <label>Mã bài kiểm tra <span class="required">*</span></label>
                        <input type="text" name="ma_bkt" id="f_ma" class="form-control" required maxlength="50">
                    </div>
                    <div class="form-group">
                        <label>Loại bài <span class="required">*</span></label>
                        <select name="loai_bkt" id="f_loai" class="form-select">
                            <option value="1">Thường xuyên</option>
                            <option value="2">Giữa kỳ</option>
                            <option value="3">Cuối kỳ</option>
                            <option value="4">Ôn tập</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label>Tiêu đề <span class="required">*</span></label>
                    <input type="text" name="tieu_de" id="f_td" class="form-control" required maxlength="255">
                </div>

                <div class="form-group">
                    <label>Mô tả</label>
                    <textarea name="mo_ta" id="f_mt" class="form-control" rows="2" maxlength="1000"></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Khóa học <span class="required">*</span></label>
                        <select id="f_khoa" class="form-select">
                            <option value="">-- Chọn khóa học --</option>
                            <?php foreach ($khoaList as $k): ?>
                                <option value="<?= $k['id'] ?>"><?= Helper::h(($k['ma_khoa_hoc'] ? $k['ma_khoa_hoc'].' - ' : '').$k['ten_khoa_hoc']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Chương trình đào tạo <span class="required">*</span></label>
                        <select name="lop_hoc_id" id="f_lop" class="form-select" disabled>
                            <option value="">-- Chọn chương trình --</option>
                        </select>
                    </div>
                </div>

                <div class="form-row-3">
                    <div class="form-group">
                        <label>Ngày kiểm tra</label>
                        <input type="date" name="ngay_kiem_tra" id="f_nkt" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Thời gian (phút)</label>
                        <input type="number" name="thoi_gian_lam_bai" id="f_tglb" class="form-control" min="0">
                    </div>
                    <div class="form-group">
                        <label>Trạng thái</label>
                        <select name="trang_thai" id="f_tt" class="form-select">
                            <option value="0">Nháp</option>
                            <option value="1" selected>Đang dùng</option>
                            <option value="2">Lưu trữ</option>
                        </select>
                    </div>
                </div>

                <div class="bkt-upload-row">
                    <div class="bkt-upload-card">
                        <div class="bkt-upload-label">
                            <?= IconHelper::svg('file-text', '14') ?>
                            File đề kiểm tra
                        </div>
                        <input type="file" name="de_file" id="f_de" hidden accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.txt,.zip,.rar,.png,.jpg,.jpeg">
                        <button type="button" class="btn btn-block" onclick="document.getElementById('f_de').click()">
                            <?= IconHelper::svg('download', '14') ?>
                            Chọn file đề
                        </button>
                        <div class="bkt-upload-info" id="deInfo"></div>
                    </div>
                    <div class="bkt-upload-card bkt-upload-secret">
                        <div class="bkt-upload-label">
                            <?= IconHelper::svg('lock', '14') ?>
                            File đáp án
                        </div>
                        <input type="file" name="dap_an_file" id="f_ap" hidden accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.txt,.zip,.rar,.png,.jpg,.jpeg">
                        <button type="button" class="btn btn-block" onclick="document.getElementById('f_ap').click()">
                            <?= IconHelper::svg('download', '14') ?>
                            Chọn file đáp án
                        </button>
                        <div class="bkt-upload-info" id="apInfo"></div>
                    </div>
                </div>
                <div class="bkt-hint">Định dạng: PDF/DOC/PPT/XLS/TXT/ZIP/RAR/PNG/JPG · Tối đa 30MB mỗi file</div>

                <label class="tl-flag-toggle" style="margin-top:14px">
                    <input type="checkbox" name="cong_khai_dap_an" id="f_cdk" value="1">
                    <span class="tl-flag-text"><strong>Công khai đáp án</strong><small>Cho phép user thường xem & tải file đáp án</small></span>
                </label>

                <div class="form-group" style="margin-top:14px">
                    <label>Ghi chú</label>
                    <input type="text" name="ghi_chu" id="f_gc" class="form-control" maxlength="500">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn" onclick="closeModal()">Hủy</button>
                <button type="submit" class="btn btn-primary" id="btnSubmit">Lưu</button>
            </div>
        </form>
    </div>
</div>

<script>
var URL_AJAX = APP_BASE + 'GUI/DT_BaiKiemTra/ajax_handler.php';
var URL_DOWNLOAD = APP_BASE + 'GUI/DT_BaiKiemTra/download.php';
var CAN_EDIT = <?= $canEdit?'true':'false' ?>;
var CAN_DEL = <?= $canDel?'true':'false' ?>;
var ICON_EDIT = '<?= addslashes(IconHelper::svg('edit', '14')) ?>';
var ICON_TRASH = '<?= addslashes(IconHelper::svg('trash', '14')) ?>';
var ICON_FILE_TEXT = '<?= addslashes(IconHelper::svg('file-text', '14')) ?>';
var ICON_DOWNLOAD = '<?= addslashes(IconHelper::svg('download', '14')) ?>';
var ICON_LOCK = '<?= addslashes(IconHelper::svg('lock', '14')) ?>';
var ICON_UNLOCK = '<?= addslashes(IconHelper::svg('unlock', '14')) ?>';
var ICON_EMPTY = '<?= addslashes(IconHelper::svg('search', '40')) ?>';
var ICON_DOWNLOAD_SM = '<?= addslashes(IconHelper::svg('download', '13')) ?>';
var state = { page:1, pageSize:20, daXoa:0, search:'', filter:{lop:0, loai:0, tt:''} };
function exportExcel(){ var p=new URLSearchParams({search:state.search||'',da_xoa:state.daXoa||0,lop_hoc_id:state.filter.lop||0,loai_bkt:state.filter.loai||0,trang_thai:state.filter.tt||''}); window.location=APP_BASE+'GUI/DT_BaiKiemTra/export.php?'+p.toString(); }
var LOAI_TXT = {1:'Thường xuyên', 2:'Giữa kỳ', 3:'Cuối kỳ', 4:'Ôn tập'};
var TT_TXT = {0:'Nháp', 1:'Đang dùng', 2:'Lưu trữ'};

// =========== Helpers ===========
function fmtBytes(b){ if(!b) return ''; var u=['B','KB','MB','GB']; var i=0; b=parseFloat(b); while(b>=1024&&i<u.length-1){b/=1024;i++;} return (b<10&&i>0?b.toFixed(1):Math.round(b))+' '+u[i]; }
function loaiBadge(l){ var t=parseInt(l,10); var cls=t===2?'mid':(t===3?'final':(t===4?'rev':'reg')); return '<span class="bkt-loai bkt-loai-'+cls+'">'+APP.escape(LOAI_TXT[t]||'')+'</span>'; }
function ttBadge(t){ var cls=['draft','active','archive'][parseInt(t,10)]||'draft'; return '<span class="bkt-tt bkt-tt-'+cls+'">'+APP.escape(TT_TXT[t]||'')+'</span>'; }

// =========== Load ===========
function loadStats(){
    APP.ajax(URL_AJAX,{action:'getStats'}).done(function(res){
        if (!res.success) return;
        $('#stTotal').text(res.data.total||0);
        $('#stTX').text(res.data.thuong_xuyen||0);
        $('#stKy').text((parseInt(res.data.giua_ky,10)||0)+(parseInt(res.data.cuoi_ky,10)||0));
        $('#stDA').text(res.data.co_dap_an||0);
    });
}

function load(){
    APP.showLoading('#bktWrap');
    APP.ajax(URL_AJAX, {
        action:'getPaged', page:state.page, pageSize:state.pageSize,
        da_xoa:state.daXoa, search:state.search,
        lop_hoc_id:state.filter.lop,
        loai_bkt:state.filter.loai, trang_thai:state.filter.tt
    }).done(function(res){
        APP.hideLoading('#bktWrap');
        if (!res.success){ APP.toast(res.message,'error'); return; }
        renderRows(res.data);
        renderPager(res.pagination);
    });
}

function renderRows(rows){
    var $tb = $('#tbody').empty();
    if (!rows.length){
        $tb.append('<tr><td colspan="10"><div class="empty-state" style="padding:40px"><div class="icon">' + ICON_EMPTY + '</div>Không có bài kiểm tra nào</div></td></tr>');
        return;
    }
    var stt = (state.page-1)*state.pageSize;
    rows.forEach(function(r){
        stt++;
        var lopMon = '';
        if (r.ma_lop) lopMon += '<div style="font-size:12.5px;font-weight:500">'+APP.escape(r.ma_lop)+'</div>';
        if (r.ten_lop) lopMon += '<div class="text-muted" style="font-size:11px">'+APP.escape(r.ten_lop)+'</div>';
        if (r.ten_mon_hoc) lopMon += '<div class="text-muted" style="font-size:11px">📚 '.replace('📚','')+APP.escape(r.ma_mon_hoc||'')+'</div>';
        if (!lopMon) lopMon = '<span class="text-muted">-</span>';

        var deCell = r.de_file_name
            ? '<a class="bkt-file-link" href="'+URL_DOWNLOAD+'?id='+r.id+'&kind=de" title="Tải đề: '+APP.escape(r.de_file_goc||'')+'">' + ICON_DOWNLOAD + ' '+(r.de_file_size?fmtBytes(r.de_file_size):'')+'</a>'
            : '<span class="text-muted">—</span>';
        var apCell = r.dap_an_file_name
            ? '<a class="bkt-file-link bkt-file-secret" href="'+URL_DOWNLOAD+'?id='+r.id+'&kind=dap_an" title="Tải đáp án: '+APP.escape(r.dap_an_file_goc||'')+'">'+(parseInt(r.cong_khai_dap_an,10)===1?ICON_UNLOCK:ICON_LOCK)+' '+(r.dap_an_file_size?fmtBytes(r.dap_an_file_size):'')+'</a>'
            : '<span class="text-muted">—</span>';

        var actions = '';
        if (state.daXoa==0){
            if (CAN_EDIT) actions += '<button class="btn btn-sm" onclick="openEdit('+r.id+')" title="Sửa">' + ICON_EDIT + '</button> ';
            if (CAN_DEL) actions += '<button class="btn btn-sm btn-danger" onclick="trashItem('+r.id+')" title="Xóa">' + ICON_TRASH + '</button>';
        } else {
            if (CAN_EDIT) actions += '<button class="btn btn-sm btn-success" onclick="restoreItem('+r.id+')">Khôi phục</button> ';
            if (CAN_DEL) actions += '<button class="btn btn-sm btn-danger" onclick="deleteItem('+r.id+')">Xóa vĩnh viễn</button>';
        }

        var ngayKT = r.ngay_kiem_tra ? APP.formatDate(r.ngay_kiem_tra) : '<span class="text-muted">—</span>';
        var tgLB = r.thoi_gian_lam_bai ? '<div class="text-muted" style="font-size:11px">'+r.thoi_gian_lam_bai+' phút</div>' : '';

        $tb.append(
            '<tr>'+
                '<td class="text-center">'+stt+'</td>'+
                '<td><strong>'+APP.escape(r.ma_bkt||'')+'</strong></td>'+
                '<td><div style="font-weight:500">'+APP.escape(r.tieu_de||'')+'</div>'+(r.mo_ta?'<div class="text-muted" style="font-size:11.5px;margin-top:2px">'+APP.escape(r.mo_ta).substring(0,80)+(r.mo_ta.length>80?'...':'')+'</div>':'')+'</td>'+
                '<td>'+loaiBadge(r.loai_bkt)+'</td>'+
                '<td>'+lopMon+'</td>'+
                '<td>'+ngayKT+tgLB+'</td>'+
                '<td class="text-center">'+deCell+'</td>'+
                '<td class="text-center">'+apCell+'</td>'+
                '<td class="text-center">'+ttBadge(r.trang_thai)+'</td>'+
                '<td><div class="actions">'+actions+'</div></td>'+
            '</tr>'
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
$('#fLop').on('change',function(){state.filter.lop=parseInt(this.value,10)||0;state.page=1;load();});
$('#fLoai').on('change',function(){state.filter.loai=parseInt(this.value,10)||0;state.page=1;load();});
$('#fTT').on('change',function(){state.filter.tt=this.value;state.page=1;load();});

// =========== Modal: chọn khóa -> nạp CTĐT ===========
// preselectCt: nếu có, sẽ set sau khi nạp xong (dùng cho openEdit)
function loadCTtheoKhoa(khoaId, preselectCt){
    var $ct = $('#f_lop').empty().append('<option value="">-- Chọn chương trình --</option>').prop('disabled', true);
    if (!khoaId) return;
    APP.ajax(URL_AJAX,{action:'getChuongTrinhTheoKhoa', khoa_hoc_id:khoaId}).done(function(res){
        if (!res.success) return;
        var rows = res.data||[];
        if (!rows.length){ $ct.append('<option value="" disabled>(Khóa này chưa có chương trình)</option>'); return; }
        rows.forEach(function(c){ $ct.append('<option value="'+c.id+'">'+APP.escape((c.ma_chuong_trinh?c.ma_chuong_trinh+' - ':'')+(c.ten_chuong_trinh||''))+'</option>'); });
        $ct.prop('disabled', false);
        if (preselectCt) $ct.val(preselectCt);
    });
}
$('#f_khoa').on('change', function(){ loadCTtheoKhoa(parseInt(this.value,10)||0); });

function setFileInfo($input, $info){
    var f = $input[0].files && $input[0].files[0];
    if (f){
        $info.html('<div class="bkt-file-chosen"><strong>'+APP.escape(f.name)+'</strong><span>'+fmtBytes(f.size)+'</span><button type="button" class="bkt-clear" title="Bỏ chọn">×</button></div>');
    } else {
        $info.empty();
    }
}
$('#f_de').on('change', function(){ setFileInfo($(this), $('#deInfo')); });
$('#f_ap').on('change', function(){ setFileInfo($(this), $('#apInfo')); });
$(document).on('click', '#deInfo .bkt-clear', function(){ $('#f_de').val(''); $('#deInfo').empty(); });
$(document).on('click', '#apInfo .bkt-clear', function(){ $('#f_ap').val(''); $('#apInfo').empty(); });

function showCurrent($info, fileGoc, fileName, kind, id, size){
    if (!fileName){ $info.empty(); return; }
    var html = '<div class="bkt-file-current">';
    html += '<a href="'+URL_DOWNLOAD+'?id='+id+'&kind='+kind+'" class="bkt-file-link">' + ICON_DOWNLOAD_SM + ' '+APP.escape(fileGoc||fileName)+'</a>';
    html += '<span class="text-muted" style="font-size:11.5px">'+(size?fmtBytes(size):'')+'</span>';
    if (CAN_EDIT) html += '<button type="button" class="bkt-clear-server" data-kind="'+kind+'" title="Gỡ file này">Gỡ</button>';
    html += '</div>';
    $info.html(html);
}

$(document).on('click', '.bkt-clear-server', function(){
    var kind = $(this).data('kind');
    var id = parseInt($('#f_id').val(), 10);
    if (!id) return;
    APP.confirm('Gỡ file '+(kind==='de'?'đề':'đáp án')+' khỏi bài kiểm tra?', function(){
        APP.ajax(URL_AJAX, {action:'clearFile', id:id, field:kind}).done(function(res){
            if (res.success){
                APP.toast(res.message,'success');
                if (kind==='de') $('#deInfo').empty(); else $('#apInfo').empty();
                load();
            } else APP.toast(res.message,'error');
        });
    });
});

function openCreate(){
    $('#modalTitle').text('Thêm bài kiểm tra');
    $('#formBKT')[0].reset();
    $('#f_id').val('');
    $('#f_khoa').val('');
    $('#f_lop').empty().append('<option value="">-- Chọn chương trình --</option>').prop('disabled', true);
    $('#deInfo').empty(); $('#apInfo').empty();
    $('#f_tt').val('1');
    $('#btnSubmit').text('Lưu');
    $('#modalForm').addClass('open');
}

function openEdit(id){
    APP.ajax(URL_AJAX,{action:'getById', id:id}).done(function(res){
        if (!res.success){ APP.toast(res.message,'error'); return; }
        var b = res.data;
        $('#modalTitle').text('Sửa bài kiểm tra');
        $('#formBKT')[0].reset();
        $('#f_id').val(b.id);
        $('#f_ma').val(b.ma_bkt);
        $('#f_loai').val(b.loai_bkt);
        $('#f_td').val(b.tieu_de);
        $('#f_mt').val(b.mo_ta||'');
        // Chọn khóa rồi nạp CTĐT thuộc khóa, sau đó chọn đúng CTĐT
        $('#f_khoa').val(b.khoa_hoc_id||'');
        loadCTtheoKhoa(parseInt(b.khoa_hoc_id,10)||0, b.lop_hoc_id||'');
        $('#f_nkt').val(b.ngay_kiem_tra||'');
        $('#f_tglb').val(b.thoi_gian_lam_bai||'');
        $('#f_tt').val(b.trang_thai);
        $('#f_cdk').prop('checked', parseInt(b.cong_khai_dap_an,10)===1);
        $('#f_gc').val(b.ghi_chu||'');
        showCurrent($('#deInfo'), b.de_file_goc, b.de_file_name, 'de', b.id, b.de_file_size);
        showCurrent($('#apInfo'), b.dap_an_file_goc, b.dap_an_file_name, 'dap_an', b.id, b.dap_an_file_size);
        $('#btnSubmit').text('Lưu thay đổi');
        $('#modalForm').addClass('open');
    });
}
function closeModal(){ $('#modalForm').removeClass('open'); }

$('#formBKT').on('submit', function(e){
    e.preventDefault();
    var fd = new FormData(this);
    fd.append('action', $('#f_id').val()?'update':'insert');
    if (!fd.has('cong_khai_dap_an')) fd.append('cong_khai_dap_an', '0');
    var $btn = $('#btnSubmit').prop('disabled', true).text('Đang lưu...');
    $.ajax({ url: URL_AJAX, type:'POST', data: fd, processData:false, contentType:false, dataType:'json', headers: window.CSRF_TOKEN ? {'X-CSRF-Token': window.CSRF_TOKEN} : {} })
        .done(function(res){
            $btn.prop('disabled', false).text('Lưu');
            if (res.success){ APP.toast(res.message,'success'); closeModal(); load(); loadStats(); }
            else APP.toast(res.message||'Lỗi','error');
        })
        .fail(function(xhr){
            $btn.prop('disabled', false).text('Lưu');
            APP.toast('Lỗi kết nối (HTTP '+(xhr.status||'?')+')','error');
        });
});

function trashItem(id){ APP.confirm('Chuyển bài kiểm tra vào thùng rác?', function(){ APP.ajax(URL_AJAX,{action:'trash',id:id}).done(function(res){ res.success?(APP.toast(res.message,'success'),load(),loadStats()):APP.toast(res.message,'error'); }); }); }
function restoreItem(id){ APP.ajax(URL_AJAX,{action:'restore',id:id}).done(function(res){ res.success?(APP.toast(res.message,'success'),load(),loadStats()):APP.toast(res.message,'error'); }); }
function deleteItem(id){ APP.confirm('Xóa VĨNH VIỄN bài kiểm tra (kèm file)?', function(){ APP.ajax(URL_AJAX,{action:'delete',id:id}).done(function(res){ res.success?(APP.toast(res.message,'success'),load(),loadStats()):APP.toast(res.message,'error'); }); },{yesText:'Xóa vĩnh viễn'}); }

// Init
load(); loadStats();
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
