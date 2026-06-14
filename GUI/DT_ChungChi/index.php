<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../BUS/DM_HocVien_BUS.php';
require_once __DIR__ . '/../../BUS/DT_KhoaHocChuongTrinh_BUS.php';

Helper::requireLogin();
if (!PhanQuyenHelper::hasQuyen('DT_ChungChi', PhanQuyenHelper::QUYEN_XEM)) {
    echo 'Bạn không có quyền truy cập chức năng này.'; exit;
}
$canAdd  = PhanQuyenHelper::hasQuyen('DT_ChungChi', PhanQuyenHelper::QUYEN_THEM);
$canEdit = PhanQuyenHelper::hasQuyen('DT_ChungChi', PhanQuyenHelper::QUYEN_SUA);
$canDel  = PhanQuyenHelper::hasQuyen('DT_ChungChi', PhanQuyenHelper::QUYEN_XOA);

$hocVienList = DM_HocVien_BUS::getCombo();
$lopList     = DT_KhoaHocChuongTrinh_BUS::getCombo();

$pageTitle  = 'Chứng chỉ';
$activeMenu = 'DT_ChungChi';
require __DIR__ . '/../layouts/header.php';
?>
<div class="breadcrumb">
    <a href="<?= AppConfig::baseUrl('GUI/dashboard/index.php') ?>">Trang chủ</a>
    <span class="sep">›</span> Đào tạo
    <span class="sep">›</span> <span>Chứng chỉ</span>
</div>

<div class="hv-stats">
    <div class="hv-stat">
        <div class="hv-stat-icon hv-stat-blue"><?= IconHelper::svg('academic-cap', '22') ?></div>
        <div><div class="hv-stat-label">Tổng chứng chỉ</div><div class="hv-stat-value" id="stTotal">—</div></div>
    </div>
    <div class="hv-stat">
        <div class="hv-stat-icon hv-stat-green"><?= IconHelper::svg('check-circle', '22') ?></div>
        <div><div class="hv-stat-label">Đã cấp</div><div class="hv-stat-value" id="stDaCap">—</div></div>
    </div>
    <div class="hv-stat">
        <div class="hv-stat-icon hv-stat-orange"><?= IconHelper::svg('edit', '22') ?></div>
        <div><div class="hv-stat-label">Nháp</div><div class="hv-stat-value" id="stNhap">—</div></div>
    </div>
    <div class="hv-stat">
        <div class="hv-stat-icon hv-stat-red"><?= IconHelper::svg('x-circle', '22') ?></div>
        <div><div class="hv-stat-label">Thu hồi</div><div class="hv-stat-value" id="stThuHoi">—</div></div>
    </div>
</div>

<div class="card">
    <div class="cc-toolbar">
        <input type="text" id="search" class="cc-search form-control" placeholder="Tìm số CC, tên CC, học viên...">
        <select id="fHocVien" class="form-select" style="min-width:200px">
            <option value="0">Tất cả học viên</option>
            <?php foreach ($hocVienList as $hv): ?>
                <option value="<?= $hv['id'] ?>"><?= Helper::h($hv['ma_hoc_vien'] . ' - ' . $hv['ho_ten']) ?></option>
            <?php endforeach; ?>
        </select>
        <select id="fLop" class="form-select" style="min-width:180px">
            <option value="0">Tất cả chương trình</option>
            <?php foreach ($lopList as $l): ?>
                <option value="<?= $l['id'] ?>"><?= Helper::h($l['label']) ?></option>
            <?php endforeach; ?>
        </select>
        <select id="fLoai" class="form-select" style="width:140px">
            <option value="">Tất cả loại</option>
            <option value="Chứng chỉ">Chứng chỉ</option>
            <option value="Chứng nhận">Chứng nhận</option>
            <option value="Bằng">Bằng</option>
        </select>
        <select id="fTrangThai" class="form-select" style="width:140px">
            <option value="">Tất cả TT</option>
            <option value="0">Nháp</option>
            <option value="1">Đã cấp</option>
            <option value="2">Thu hồi</option>
        </select>
        <select id="fDX" class="form-select" style="width:120px">
            <option value="0">Đang dùng</option>
            <option value="1">Thùng rác</option>
        </select>
        <div class="cc-toolbar-spacer"></div>
        <?php if ($canAdd): ?>
            <button type="button" class="btn btn-primary" onclick="openCreate()">
                <?= IconHelper::svg('plus', '14') ?>
                Thêm chứng chỉ
            </button>
        <?php endif; ?>
    </div>

    <div id="ccGrid">
        <div class="cc-empty"><p>Đang tải...</p></div>
    </div>

    <div class="pagination-wrap" style="margin-top:18px">
        <div id="pageInfo" class="text-muted">-</div>
        <div id="pageNav"></div>
    </div>
</div>

<!-- Modal Form -->
<div class="modal-backdrop" id="modalForm">
    <div class="modal" style="max-width:760px">
        <div class="modal-header">
            <h3 id="modalTitle">Thêm chứng chỉ</h3>
            <button type="button" class="close" onclick="closeModal()">&times;</button>
        </div>
        <form id="formCC" enctype="multipart/form-data">
            <div class="modal-body">
                <input type="hidden" name="id" id="f_id">

                <div class="form-row">
                    <div class="form-group">
                        <label>Học viên <span class="required">*</span></label>
                        <select name="hoc_vien_id" id="f_hv" class="form-select" required>
                            <option value="">-- Chọn học viên --</option>
                            <?php foreach ($hocVienList as $hv): ?>
                                <option value="<?= $hv['id'] ?>"><?= Helper::h($hv['ma_hoc_vien'] . ' - ' . $hv['ho_ten']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Chương trình đào tạo <span class="required">*</span></label>
                        <select name="lop_hoc_id" id="f_lop" class="form-select" required>
                            <option value="">-- Chọn chương trình --</option>
                            <?php foreach ($lopList as $l): ?>
                                <option value="<?= $l['id'] ?>"><?= Helper::h($l['label']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Số chứng chỉ <span class="required">*</span></label>
                        <input type="text" name="so_chung_chi" id="f_so" class="form-control" required maxlength="100">
                    </div>
                    <div class="form-group">
                        <label>Loại</label>
                        <select name="loai_chung_chi" id="f_loai" class="form-select">
                            <option value="Chứng chỉ">Chứng chỉ</option>
                            <option value="Chứng nhận">Chứng nhận</option>
                            <option value="Bằng">Bằng</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label>Tên chứng chỉ <span class="required">*</span></label>
                    <input type="text" name="ten_chung_chi" id="f_ten" class="form-control" required maxlength="300">
                </div>

                <div class="form-row-3">
                    <div class="form-group">
                        <label>Điểm trung bình</label>
                        <input type="number" name="diem_trung_binh" id="f_dtb" class="form-control" min="0" max="10" step="0.1" placeholder="0.0 - 10.0">
                    </div>
                    <div class="form-group">
                        <label>Xếp loại</label>
                        <select name="xep_loai_tot_nghiep" id="f_xl" class="form-select">
                            <option value="">-- Chọn xếp loại --</option>
                            <option value="Xuất sắc">Xuất sắc</option>
                            <option value="Giỏi">Giỏi</option>
                            <option value="Khá">Khá</option>
                            <option value="Trung bình">Trung bình</option>
                            <option value="Yếu">Yếu</option>
                            <option value="Không đạt">Không đạt</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Trạng thái</label>
                        <select name="trang_thai" id="f_tt" class="form-select">
                            <option value="0">Nháp</option>
                            <option value="1">Đã cấp</option>
                            <option value="2">Thu hồi</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Ngày cấp <span class="required">*</span></label>
                        <input type="date" name="ngay_cap" id="f_ncap" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Ngày hết hạn</label>
                        <input type="date" name="ngay_het_han" id="f_hhan" class="form-control">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Người ký</label>
                        <input type="text" name="nguoi_ky" id="f_nky" class="form-control" maxlength="200">
                    </div>
                    <div class="form-group">
                        <label>Chức vụ người ký</label>
                        <input type="text" name="chuc_vu_nguoi_ky" id="f_cvnky" class="form-control" maxlength="200">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Nơi cấp</label>
                        <input type="text" name="noi_cap" id="f_noi" class="form-control" maxlength="300">
                    </div>
                    <div class="form-group">
                        <label>Ghi chú</label>
                        <input type="text" name="ghi_chu" id="f_gc" class="form-control" maxlength="500">
                    </div>
                </div>

                <div class="form-group">
                    <label>File chứng chỉ (PDF, JPG, PNG)</label>
                    <input type="file" name="chung_chi_file" id="f_file" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                    <div class="text-muted" style="font-size:12px;margin-top:4px">PDF hoặc ảnh JPG/PNG · Tối đa 20MB</div>
                    <div id="currentFileInfo" style="display:none;margin-top:6px">
                        <span class="text-muted" style="font-size:12.5px">File hiện tại: </span>
                        <strong id="currentFileName">—</strong>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn" onclick="closeModal()">Hủy</button>
                <button type="submit" class="btn btn-primary" id="btnSubmit">Lưu</button>
            </div>
        </form>
    </div>
</div>

<!-- Drawer Detail -->
<div class="drawer-backdrop" id="drawerDetail">
    <div class="drawer">
        <div class="drawer-header">
            <div>
                <h3 id="dTitle" style="margin:0">Chi tiết chứng chỉ</h3>
                <div id="dSubtitle" class="text-muted" style="font-size:12.5px;margin-top:2px"></div>
            </div>
            <button type="button" class="close" onclick="closeDrawer()">&times;</button>
        </div>
        <div class="drawer-body" id="dBody"></div>
    </div>
</div>

<script>
var URL_AJAX = APP_BASE + 'GUI/DT_ChungChi/ajax_handler.php';
var URL_DL   = APP_BASE + 'GUI/DT_ChungChi/download.php';
var CAN_EDIT = <?= $canEdit ? 'true' : 'false' ?>;
var CAN_DEL  = <?= $canDel  ? 'true' : 'false' ?>;

var ICON_EDIT      = '<?= addslashes(IconHelper::svg('edit', '13')) ?>';
var ICON_TRASH     = '<?= addslashes(IconHelper::svg('trash', '13')) ?>';
var ICON_DOWNLOAD  = '<?= addslashes(IconHelper::svg('download', '14')) ?>';
var ICON_EMPTY     = '<?= addslashes(IconHelper::svg('academic-cap', '56')) ?>';
var ICON_PDF_THUMB = '<?= addslashes(IconHelper::svg('clipboard-list', '56')) ?>';
var ICON_CC_THUMB  = '<?= addslashes(IconHelper::svg('academic-cap', '56')) ?>';

var state = { page:1, pageSize:<?= AppConfig::DEFAULT_PAGE_SIZE ?>, daXoa:0,
              search:'', loai:'', hocVienId:0, lopId:0, trangThai:'' };

var TT_LABELS = {0:'Nháp', 1:'Đã cấp', 2:'Thu hồi'};
var TT_CLS    = {0:'tt-nhap', 1:'tt-dacap', 2:'tt-thuhoi'};

// Mapping xếp loại -> class màu
function xlClass(xl){
    if (!xl) return null;
    if (xl === 'Xuất sắc') return 'xl-xs';
    if (xl === 'Giỏi') return 'xl-good';
    if (xl === 'Khá') return 'xl-mid';
    if (xl === 'Trung bình') return 'xl-mid';
    if (xl === 'Yếu' || xl === 'Không đạt') return 'xl-low';
    return 'xl-mid';
}

function avatar(name){
    if (!name) return '?';
    var parts = name.trim().split(/\s+/);
    var last = parts[parts.length - 1] || '';
    return last.charAt(0).toUpperCase();
}

// ============ Stats ============
function loadStats(){
    APP.ajax(URL_AJAX, {action:'getStats'}).done(function(res){
        if (!res.success) return;
        $('#stTotal').text(res.data.total || 0);
        $('#stDaCap').text(res.data.so_da_cap || 0);
        $('#stNhap').text(res.data.so_nhap || 0);
        $('#stThuHoi').text(res.data.so_thu_hoi || 0);
    });
}

// ============ Load list ============
function load(){
    $('#ccGrid').html('<div class="cc-empty"><p>Đang tải...</p></div>');
    APP.ajax(URL_AJAX, {
        action:'getPaged', page:state.page, pageSize:state.pageSize, da_xoa:state.daXoa,
        search:state.search, loai_chung_chi:state.loai, hoc_vien_id:state.hocVienId,
        lop_hoc_id:state.lopId, trang_thai:state.trangThai
    }).done(function(res){
        if (!res.success){ APP.toast(res.message,'error'); return; }
        renderGrid(res.data);
        renderPager(res.pagination);
    });
}

function isImage(name){
    if (!name) return false;
    var e = (name.split('.').pop() || '').toLowerCase();
    return ['jpg','jpeg','png','gif','webp','bmp'].indexOf(e) !== -1;
}
function isPdf(name){
    return !!name && name.toLowerCase().endsWith('.pdf');
}

function renderGrid(rows){
    if (!rows.length){
        var msg = state.daXoa ? 'Thùng rác trống' : 'Chưa có chứng chỉ nào';
        $('#ccGrid').html(
            '<div class="cc-empty">'
            + ICON_EMPTY
            + '<h4>'+msg+'</h4>'
            + (state.daXoa ? '' : '<p>Click "Thêm chứng chỉ" để bắt đầu.</p>')
            + '</div>');
        return;
    }
    var html = '<div class="cc-grid">';
    rows.forEach(function(r){
        var tt = parseInt(r.trang_thai, 10);
        var ttCls = TT_CLS[tt] || 'tt-nhap';
        var ttLbl = TT_LABELS[tt] || tt;

        // Thumbnail
        var thumb = '';
        if (isImage(r.duong_dan_file)){
            thumb = '<img src="'+URL_DL+'?id='+r.id+'&inline=1" alt="thumb" loading="lazy">';
        } else if (isPdf(r.duong_dan_file)){
            thumb = '<span class="cc-card-thumb-icon" style="color:#dc2626">'+ICON_PDF_THUMB+'</span>';
        } else {
            thumb = '<span class="cc-card-thumb-icon">'+ICON_CC_THUMB+'</span>';
        }

        var xlc = xlClass(r.xep_loai_tot_nghiep);
        var xlBadge = r.xep_loai_tot_nghiep
            ? '<span class="cc-card-xl '+(xlc||'')+'">'+APP.escape(r.xep_loai_tot_nghiep)+'</span>'
            : '';

        var actions = '';
        if (state.daXoa == 0){
            if (CAN_EDIT){
                actions += '<button type="button" class="btn btn-sm icon-only" title="Sửa" onclick="event.stopPropagation();openEdit('+r.id+')">'+ICON_EDIT+'</button>';
                if (tt === 0) actions += '<button type="button" class="btn btn-sm btn-success" onclick="event.stopPropagation();capCC('+r.id+')">Cấp</button>';
                if (tt === 1) actions += '<button type="button" class="btn btn-sm btn-warning" onclick="event.stopPropagation();thuHoiCC('+r.id+')">Thu hồi</button>';
            }
            if (CAN_DEL) actions += '<button type="button" class="btn btn-sm btn-danger icon-only" title="Xóa" onclick="event.stopPropagation();trashItem('+r.id+')">'+ICON_TRASH+'</button>';
        } else {
            if (CAN_EDIT) actions += '<button type="button" class="btn btn-sm btn-success" onclick="event.stopPropagation();restoreItem('+r.id+')">Khôi phục</button>';
            if (CAN_DEL)  actions += '<button type="button" class="btn btn-sm btn-danger" onclick="event.stopPropagation();deleteItem('+r.id+')">Xóa hẳn</button>';
        }

        html += '<div class="cc-card" onclick="openDetail('+r.id+')">'
              + '<div class="cc-card-thumb">'
              + thumb
              + '<span class="cc-card-tt '+ttCls+'">'+ttLbl+'</span>'
              + '</div>'
              + '<div class="cc-card-body">'
              + '<div class="cc-card-so">'+APP.escape(r.so_chung_chi||'')+'</div>'
              + '<div class="cc-card-name">'+APP.escape(r.ten_chung_chi||'')+'</div>'
              + (xlBadge ? xlBadge : '')
              + '<div class="cc-card-meta">'
              + (r.diem_trung_binh !== null && r.diem_trung_binh !== undefined ? '<span class="meta-item">Điểm: <strong>'+parseFloat(r.diem_trung_binh).toFixed(1)+'</strong></span>' : '')
              + (r.ngay_cap ? '<span class="meta-item">Cấp: '+APP.formatDate(r.ngay_cap)+'</span>' : '')
              + '</div>'
              + '<div class="cc-card-hv">'
              + '<div class="cc-card-avatar">'+APP.escape(avatar(r.ho_ten_hoc_vien||''))+'</div>'
              + '<div style="flex:1;min-width:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">'
              + '<strong>'+APP.escape(r.ho_ten_hoc_vien||'')+'</strong>'
              + (r.ma_lop ? ' <span class="text-muted" style="font-size:11px">· '+APP.escape(r.ma_lop)+'</span>' : '')
              + '</div>'
              + '</div>'
              + '</div>'
              + (actions ? '<div class="cc-card-actions">'+actions+'</div>' : '')
              + '</div>';
    });
    html += '</div>';
    $('#ccGrid').html(html);
}

function renderPager(p){
    var from = (p.currentPage-1)*p.pageSize + 1;
    var to = Math.min(from+p.pageSize-1, p.totalRecords);
    $('#pageInfo').text(p.totalRecords ? 'Hiển thị '+from+'-'+to+' / '+p.totalRecords : 'Không có bản ghi');
    $('#pageNav').html(APP.renderPagination(p));
}

$('#pageNav').on('click','button[data-p]',function(){ var p=parseInt($(this).data('p'),10); if(!p||p===state.page) return; state.page=p; load(); });
$('#search').on('input', APP.debounce(function(){ state.search=$(this).val(); state.page=1; load(); }, 350));
$('#fHocVien').on('change', function(){ state.hocVienId=parseInt(this.value,10)||0; state.page=1; load(); });
$('#fLop').on('change', function(){ state.lopId=parseInt(this.value,10)||0; state.page=1; load(); });
$('#fLoai').on('change', function(){ state.loai=this.value; state.page=1; load(); });
$('#fTrangThai').on('change', function(){ state.trangThai=this.value; state.page=1; load(); });
$('#fDX').on('change', function(){ state.daXoa=parseInt(this.value,10)||0; state.page=1; load(); });

// ============ Modal ============
function openCreate(){
    $('#modalTitle').text('Thêm chứng chỉ');
    $('#formCC')[0].reset();
    $('#f_id').val('');
    $('#currentFileInfo').hide();
    $('#btnSubmit').text('Lưu');
    $('#modalForm').addClass('open');
}
function openEdit(id){
    APP.ajax(URL_AJAX, {action:'getById', id:id}).done(function(res){
        if (!res.success){ APP.toast(res.message,'error'); return; }
        var r = res.data;
        $('#modalTitle').text('Sửa chứng chỉ');
        $('#f_id').val(r.id);
        $('#f_hv').val(r.hoc_vien_id);
        $('#f_lop').val(r.lop_hoc_id);
        $('#f_so').val(r.so_chung_chi);
        $('#f_loai').val(r.loai_chung_chi);
        $('#f_ten').val(r.ten_chung_chi);
        $('#f_dtb').val(r.diem_trung_binh !== null ? r.diem_trung_binh : '');
        $('#f_xl').val(r.xep_loai_tot_nghiep||'');
        $('#f_tt').val(r.trang_thai);
        $('#f_ncap').val(r.ngay_cap||'');
        $('#f_hhan').val(r.ngay_het_han||'');
        $('#f_nky').val(r.nguoi_ky||'');
        $('#f_cvnky').val(r.chuc_vu_nguoi_ky||'');
        $('#f_noi').val(r.noi_cap||'');
        $('#f_gc').val(r.ghi_chu||'');
        $('#f_file').val('');
        if (r.duong_dan_file){
            $('#currentFileInfo').show();
            $('#currentFileName').text(r.duong_dan_file);
        } else {
            $('#currentFileInfo').hide();
        }
        $('#btnSubmit').text('Lưu thay đổi');
        $('#modalForm').addClass('open');
    });
}
function closeModal(){ $('#modalForm').removeClass('open'); }

$('#formCC').on('submit', function(e){
    e.preventDefault();
    var fd = new FormData(this);
    fd.append('action', $('#f_id').val() ? 'update' : 'insert');
    var $btn = $('#btnSubmit').prop('disabled', true).text('Đang lưu...');
    $.ajax({ url:URL_AJAX, type:'POST', data:fd, processData:false, contentType:false, dataType:'json', headers: window.CSRF_TOKEN ? {'X-CSRF-Token': window.CSRF_TOKEN} : {} })
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

function capCC(id){ APP.confirm('Xác nhận cấp chứng chỉ này?',function(){ APP.ajax(URL_AJAX,{action:'capChungChi',id:id}).done(function(res){ res.success?(APP.toast(res.message,'success'),load(),loadStats()):APP.toast(res.message,'error'); }); }); }
function thuHoiCC(id){ APP.confirm('Xác nhận thu hồi chứng chỉ này?',function(){ APP.ajax(URL_AJAX,{action:'thuHoiChungChi',id:id}).done(function(res){ res.success?(APP.toast(res.message,'success'),load(),loadStats()):APP.toast(res.message,'error'); }); }); }
function trashItem(id){ APP.confirm('Chuyển chứng chỉ vào thùng rác?',function(){ APP.ajax(URL_AJAX,{action:'trash',id:id}).done(function(res){ res.success?(APP.toast(res.message,'success'),load(),loadStats()):APP.toast(res.message,'error'); }); }); }
function restoreItem(id){ APP.ajax(URL_AJAX,{action:'restore',id:id}).done(function(res){ res.success?(APP.toast(res.message,'success'),load(),loadStats()):APP.toast(res.message,'error'); }); }
function deleteItem(id){ APP.confirm('Xóa VĨNH VIỄN chứng chỉ này (kèm file)?',function(){ APP.ajax(URL_AJAX,{action:'delete',id:id}).done(function(res){ res.success?(APP.toast(res.message,'success'),load(),loadStats()):APP.toast(res.message,'error'); }); },{yesText:'Xóa vĩnh viễn'}); }

// ============ Drawer Detail ============
function openDetail(id){
    $('#drawerDetail').addClass('open').find('.drawer').addClass('open');
    $('#dTitle').text('Đang tải...'); $('#dSubtitle').text('');
    $('#dBody').html('<div style="padding:30px;text-align:center;color:var(--gray-500)">Đang tải...</div>');
    APP.ajax(URL_AJAX, {action:'getById', id:id}).done(function(res){
        if (!res.success){ $('#dBody').html('<div style="padding:20px;color:#b91c1c">'+APP.escape(res.message||'')+'</div>'); return; }
        renderDetail(res.data);
    });
}
function closeDrawer(){ $('#drawerDetail').removeClass('open').find('.drawer').removeClass('open'); }

function renderDetail(r){
    var tt = parseInt(r.trang_thai, 10);
    $('#dTitle').text(r.ten_chung_chi || '-');
    $('#dSubtitle').text((r.so_chung_chi||'') + (r.ma_hv ? ' · HV: '+r.ma_hv : ''));

    var html = '';

    // File preview
    if (r.duong_dan_file){
        var url = URL_DL + '?id=' + r.id + '&inline=1';
        html += '<div class="cc-detail-preview">';
        if (isImage(r.duong_dan_file)){
            html += '<img src="'+url+'" alt="preview">';
        } else if (isPdf(r.duong_dan_file)){
            html += '<iframe src="'+url+'" title="PDF preview"></iframe>';
        } else {
            html += '<div class="cc-noprev">Không có xem trước. Tải file để xem.</div>';
        }
        html += '</div>';
        html += '<div style="margin-bottom:14px">'
              + '<a href="'+URL_DL+'?id='+r.id+'" class="btn btn-primary" target="_blank">'
              + ICON_DOWNLOAD + ' Tải xuống</a>'
              + '</div>';
    }

    var xlc = xlClass(r.xep_loai_tot_nghiep);

    html += '<div class="lh-detail-grid">';
    html += dRow('Học viên', APP.escape((r.ma_hv||'')+' - '+(r.ho_ten_hoc_vien||'')));
    if (r.don_vi_cong_tac) html += dRow('Đơn vị', APP.escape(r.don_vi_cong_tac));
    html += dRow('Chương trình đào tạo', APP.escape((r.ma_lop||'')+' - '+(r.ten_lop||'')));
    if (r.ten_khoa_hoc) html += dRow('Khóa học', APP.escape(r.ten_khoa_hoc));
    html += dRow('Số chứng chỉ', '<code>'+APP.escape(r.so_chung_chi||'-')+'</code>');
    html += dRow('Loại', APP.escape(r.loai_chung_chi||'-'));
    html += dRow('Điểm TB', (r.diem_trung_binh !== null && r.diem_trung_binh !== undefined) ? '<strong>'+parseFloat(r.diem_trung_binh).toFixed(1)+'</strong>' : '-');
    html += dRow('Xếp loại', r.xep_loai_tot_nghiep
        ? '<span class="cc-card-xl '+(xlc||'')+'">'+APP.escape(r.xep_loai_tot_nghiep)+'</span>'
        : '-');
    html += dRow('Ngày cấp', r.ngay_cap ? APP.formatDate(r.ngay_cap) : '-');
    html += dRow('Hết hạn', r.ngay_het_han ? APP.formatDate(r.ngay_het_han) : '-');
    html += dRow('Người ký', APP.escape(r.nguoi_ky||'-'));
    html += dRow('Chức vụ', APP.escape(r.chuc_vu_nguoi_ky||'-'));
    html += dRow('Nơi cấp', APP.escape(r.noi_cap||'-'));
    html += dRow('Trạng thái', '<span class="cc-card-tt '+(TT_CLS[tt]||'tt-nhap')+'" style="position:static">'+(TT_LABELS[tt]||tt)+'</span>');
    html += '</div>';

    if (r.ghi_chu) html += '<div class="lh-detail-block"><div class="lh-detail-label">Ghi chú</div><div>'+APP.escape(r.ghi_chu)+'</div></div>';

    html += '<div class="lh-detail-block text-muted" style="font-size:12px">';
    if (r.tai_khoan_nguoi_tao) html += 'Tạo bởi: '+APP.escape(r.tai_khoan_nguoi_tao);
    if (r.ngay_tao) html += ' · '+APP.escape(r.ngay_tao);
    html += '</div>';

    if (CAN_EDIT || CAN_DEL){
        html += '<div class="lh-detail-actions">';
        if (CAN_EDIT){
            html += '<button class="btn" onclick="openEdit('+r.id+');closeDrawer();">Sửa</button>';
            if (tt === 0) html += '<button class="btn btn-success" onclick="capCC('+r.id+');closeDrawer();">Cấp chứng chỉ</button>';
            if (tt === 1) html += '<button class="btn btn-warning" onclick="thuHoiCC('+r.id+');closeDrawer();">Thu hồi</button>';
        }
        if (CAN_DEL) html += '<button class="btn btn-danger" onclick="trashItem('+r.id+');closeDrawer();">Xóa</button>';
        html += '</div>';
    }

    $('#dBody').html(html);
}
function dRow(label,val){ return '<div class="lh-detail-row"><div class="lh-detail-label">'+label+'</div><div class="lh-detail-val">'+val+'</div></div>'; }

// Init
load(); loadStats();
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
