<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../BUS/DM_HocVien_BUS.php';

Helper::requireLogin();
if (!PhanQuyenHelper::hasQuyen('DT_HoSoHocVien', PhanQuyenHelper::QUYEN_XEM)) {
    echo 'Bạn không có quyền truy cập chức năng này.'; exit;
}
$canAdd  = PhanQuyenHelper::hasQuyen('DT_HoSoHocVien', PhanQuyenHelper::QUYEN_THEM);
$canEdit = PhanQuyenHelper::hasQuyen('DT_HoSoHocVien', PhanQuyenHelper::QUYEN_SUA);
$canDel  = PhanQuyenHelper::hasQuyen('DT_HoSoHocVien', PhanQuyenHelper::QUYEN_XOA);

$hocVienList = DM_HocVien_BUS::getCombo();

$pageTitle  = 'Hồ sơ học viên';
$activeMenu = 'DT_HoSoHocVien';
require __DIR__ . '/../layouts/header.php';
?>
<div class="breadcrumb">
    <a href="<?= AppConfig::baseUrl('GUI/dashboard/index.php') ?>">Trang chủ</a>
    <span class="sep">›</span> Đào tạo
    <span class="sep">›</span> <span>Hồ sơ học viên</span>
</div>

<div class="hv-stats">
    <div class="hv-stat">
        <div class="hv-stat-icon hv-stat-blue"><?= IconHelper::svg('clipboard-list', '22') ?></div>
        <div><div class="hv-stat-label">Tổng hồ sơ</div><div class="hv-stat-value" id="stTotal">—</div></div>
    </div>
    <div class="hv-stat">
        <div class="hv-stat-icon hv-stat-purple"><?= IconHelper::svg('users', '22') ?></div>
        <div><div class="hv-stat-label">Học viên có hồ sơ</div><div class="hv-stat-value" id="stHocVien">—</div></div>
    </div>
    <div class="hv-stat">
        <div class="hv-stat-icon hv-stat-green"><?= IconHelper::svg('download', '22') ?></div>
        <div><div class="hv-stat-label">Hồ sơ có file</div><div class="hv-stat-value" id="stCoFile">—</div></div>
    </div>
    <div class="hv-stat">
        <div class="hv-stat-icon hv-stat-orange"><?= IconHelper::svg('warning', '22') ?></div>
        <div><div class="hv-stat-label">Hết hạn</div><div class="hv-stat-value" id="stHetHan">—</div></div>
    </div>
</div>

<div class="hs-layout">
    <!-- LEFT: Student list -->
    <aside class="hs-side">
        <div class="hs-side-head">
            <h4>Học viên</h4>
            <input type="text" id="searchHV" class="form-control" placeholder="Tìm tên / mã học viên...">
        </div>
        <div class="hs-side-list" id="studentList">
            <div class="hs-empty-side">Đang tải...</div>
        </div>
    </aside>

    <!-- RIGHT: Hồ sơ list of selected student -->
    <section class="hs-main">
        <div class="hs-main-head">
            <div class="hs-main-title">
                <h3 id="mainTitle">Chọn một học viên ở bên trái</h3>
                <div class="hs-main-sub" id="mainSub">— Hoặc tìm hồ sơ trên toàn hệ thống bằng ô tìm kiếm</div>
            </div>
            <div class="hs-main-actions">
                <input type="text" id="searchHS" class="form-control" placeholder="Tìm tên hồ sơ, số hiệu..." style="min-width:220px">
                <select id="fLoai" class="form-select" style="min-width:160px">
                    <option value="">Tất cả loại</option>
                </select>
                <select id="fDX" class="form-select" style="width:130px">
                    <option value="0">Đang dùng</option>
                    <option value="1">Thùng rác</option>
                </select>
                <button type="button" class="btn" onclick="exportExcel()" title="Xuất Excel"><?= IconHelper::svg('download','16') ?> Xuất Excel</button>
                <?php if ($canAdd): ?>
                    <button type="button" class="btn btn-primary" onclick="openCreate()">
                        <?= IconHelper::svg('plus', '14') ?>
                        Thêm hồ sơ
                    </button>
                <?php endif; ?>
            </div>
        </div>
        <div class="hs-main-body" id="mainBody">
            <div class="hs-empty-main">
                <?= IconHelper::svg('clipboard-list', '56') ?>
                <h4>Chưa chọn học viên</h4>
                <p>Click một học viên ở bảng trái để xem các hồ sơ.</p>
            </div>
        </div>
    </section>
</div>

<!-- Modal Form -->
<div class="modal-backdrop" id="modalForm">
    <div class="modal" style="max-width:680px">
        <div class="modal-header">
            <h3 id="modalTitle">Thêm hồ sơ học viên</h3>
            <button type="button" class="close" onclick="closeModal()">&times;</button>
        </div>
        <form id="formHS" enctype="multipart/form-data">
            <div class="modal-body">
                <input type="hidden" name="id" id="f_id">

                <div class="form-group">
                    <label>Học viên <span class="required">*</span></label>
                    <select name="hoc_vien_id" id="f_hv" class="form-select" required>
                        <option value="">-- Chọn học viên --</option>
                        <?php foreach ($hocVienList as $hv): ?>
                            <option value="<?= $hv['id'] ?>"><?= Helper::h($hv['ma_hoc_vien'] . ' - ' . $hv['ho_ten']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Loại hồ sơ <span class="required">*</span></label>
                        <input type="text" name="loai_ho_so" id="f_loai" class="form-control" required maxlength="100"
                               list="loaiHoSoList" placeholder="VD: CMND, Bằng cấp, Chứng chỉ...">
                        <datalist id="loaiHoSoList">
                            <option value="CMND/CCCD">
                            <option value="Hộ chiếu">
                            <option value="Bằng tốt nghiệp">
                            <option value="Chứng chỉ hành nghề">
                            <option value="Quyết định tuyển dụng">
                            <option value="Hợp đồng lao động">
                            <option value="Ảnh thẻ">
                            <option value="Lý lịch cá nhân">
                            <option value="Giấy khám sức khỏe">
                            <option value="Khác">
                        </datalist>
                    </div>
                    <div class="form-group">
                        <label>Tên hồ sơ <span class="required">*</span></label>
                        <input type="text" name="ten_ho_so" id="f_ten" class="form-control" required maxlength="300">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Số hiệu / Mã</label>
                        <input type="text" name="so_hieu" id="f_so" class="form-control" maxlength="100">
                    </div>
                    <div class="form-group">
                        <label>Nơi cấp</label>
                        <input type="text" name="noi_cap" id="f_noi" class="form-control" maxlength="200">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Ngày cấp</label>
                        <input type="date" name="ngay_cap" id="f_ncap" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Ngày hết hạn</label>
                        <input type="date" name="ngay_het_han" id="f_hhan" class="form-control">
                    </div>
                </div>

                <div class="form-group">
                    <label>File đính kèm</label>
                    <input type="file" name="ho_so_file" id="f_file" class="form-control"
                           accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.gif,.zip,.rar">
                    <div class="text-muted" style="font-size:12px;margin-top:4px">PDF, Word, JPG, PNG, ZIP · Tối đa 20MB</div>
                    <div id="currentFileInfo" style="display:none;margin-top:6px">
                        <span class="text-muted" style="font-size:12.5px">File hiện tại: </span>
                        <strong id="currentFileName">—</strong>
                        <span class="text-muted" id="currentFileSize"></span>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Trạng thái</label>
                        <select name="trang_thai" id="f_tt" class="form-select">
                            <option value="1">Hoạt động</option>
                            <option value="0">Không hoạt động</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Ghi chú</label>
                        <input type="text" name="ghi_chu" id="f_gc" class="form-control" maxlength="500">
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
                <h3 id="dTitle" style="margin:0">Chi tiết hồ sơ</h3>
                <div id="dSubtitle" class="text-muted" style="font-size:12.5px;margin-top:2px"></div>
            </div>
            <button type="button" class="close" onclick="closeDrawer()">&times;</button>
        </div>
        <div class="drawer-body" id="dBody"></div>
    </div>
</div>

<script>
var URL_AJAX = APP_BASE + 'GUI/DT_HoSoHocVien/ajax_handler.php';
var URL_DL   = APP_BASE + 'GUI/DT_HoSoHocVien/download.php';
var CAN_EDIT = <?= $canEdit ? 'true' : 'false' ?>;
var CAN_DEL  = <?= $canDel  ? 'true' : 'false' ?>;

// Toàn bộ học viên (server-render combo); + đếm hồ sơ tính sau
var ALL_STUDENTS = <?= json_encode(array_map(function($h){
    return ['id'=>(int)$h['id'], 'ma'=>(string)$h['ma_hoc_vien'], 'ho_ten'=>(string)$h['ho_ten']];
}, $hocVienList), JSON_UNESCAPED_UNICODE) ?>;

var ICON_EDIT     = '<?= addslashes(IconHelper::svg('edit', '13')) ?>';
var ICON_TRASH    = '<?= addslashes(IconHelper::svg('trash', '13')) ?>';
var ICON_EYE      = '<?= addslashes(IconHelper::svg('eye', '13')) ?>';
var ICON_DOWNLOAD = '<?= addslashes(IconHelper::svg('download', '13')) ?>';
var ICON_PLUS     = '<?= addslashes(IconHelper::svg('plus', '14')) ?>';
var ICON_EMPTY    = '<?= addslashes(IconHelper::svg('clipboard-list', '56')) ?>';

var state = {
    selectedHV: 0,           // 0 = chưa chọn (xem all)
    keywordHV: '',           // tìm trong sidebar
    keywordHS: '',           // tìm hồ sơ
    loai: '',
    daXoa: 0,
    countByHV: {},           // map hoc_vien_id -> số hồ sơ
    rowsAll: []              // hồ sơ hiện tại (theo filter)
};
function exportExcel(){ var p=new URLSearchParams({search:state.keywordHS||'',hoc_vien_id:state.selectedHV||0,loai_ho_so:state.loai||'',da_xoa:state.daXoa||0}); window.location=APP_BASE+'GUI/DT_HoSoHocVien/export.php?'+p.toString(); }

// ============ Stats ============
function loadStats(){
    APP.ajax(URL_AJAX, {action:'getStats'}).done(function(res){
        if (!res.success) return;
        $('#stTotal').text(res.data.total || 0);
        $('#stHocVien').text(res.data.so_hoc_vien || 0);
        $('#stCoFile').text(res.data.so_co_file || 0);
        $('#stHetHan').text(res.data.so_het_han || 0);
    });
}

// ============ Combo loại hồ sơ ============
function loadComboLoai(){
    APP.ajax(URL_AJAX, {action:'getComboLoai'}).done(function(res){
        if (!res.success) return;
        var html = '<option value="">Tất cả loại</option>';
        (res.data||[]).forEach(function(l){ html += '<option value="'+APP.escape(l)+'">'+APP.escape(l)+'</option>'; });
        $('#fLoai').html(html);
    });
}

// ============ Sidebar: render student list ============
function avatar(name){
    if (!name) return '?';
    var parts = name.trim().split(/\s+/);
    var last = parts[parts.length - 1] || '';
    return last.charAt(0).toUpperCase();
}

function renderStudentList(){
    var kw = state.keywordHV.toLowerCase();
    var list = ALL_STUDENTS.filter(function(h){
        if (!kw) return true;
        return (h.ho_ten||'').toLowerCase().indexOf(kw) !== -1
            || (h.ma||'').toLowerCase().indexOf(kw) !== -1;
    });
    if (!list.length){
        $('#studentList').html('<div class="hs-empty-side">Không tìm thấy</div>');
        return;
    }
    var html = '';
    list.forEach(function(h){
        var n = state.countByHV[h.id] || 0;
        html += '<div class="hs-student'+(state.selectedHV===h.id?' active':'')+'" data-id="'+h.id+'">'
              + '<div class="hs-student-avatar">'+APP.escape(avatar(h.ho_ten))+'</div>'
              + '<div class="hs-student-info">'
              + '<div class="hs-student-name">'+APP.escape(h.ho_ten)+'</div>'
              + '<div class="hs-student-meta">'+APP.escape(h.ma||'')+'</div>'
              + '</div>'
              + '<div class="hs-student-count">'+n+'</div>'
              + '</div>';
    });
    $('#studentList').html(html);
}

$(document).on('click', '.hs-student', function(){
    var id = parseInt($(this).data('id'), 10) || 0;
    if (state.selectedHV === id){
        state.selectedHV = 0;   // click lại để bỏ chọn
    } else {
        state.selectedHV = id;
    }
    renderStudentList();
    loadHoSo();
});

// ============ Right pane ============
function loadCounts(){
    // Lấy 500 hồ sơ đầu (đủ dùng) để đếm theo HV cho sidebar
    APP.ajax(URL_AJAX, {
        action:'getPaged', page:1, pageSize:500, da_xoa:0
    }).done(function(res){
        if (!res.success) return;
        var m = {};
        (res.data||[]).forEach(function(r){
            var id = parseInt(r.hoc_vien_id, 10) || 0;
            m[id] = (m[id] || 0) + 1;
        });
        state.countByHV = m;
        renderStudentList();
    });
}

function loadHoSo(){
    var hv = state.selectedHV;
    if (hv){
        var s = ALL_STUDENTS.find(function(x){ return x.id === hv; });
        if (s){
            $('#mainTitle').text(s.ho_ten);
            $('#mainSub').text(s.ma + ' · Tất cả hồ sơ của học viên này');
        }
    } else {
        $('#mainTitle').text('Tất cả hồ sơ');
        $('#mainSub').text('Hệ thống đang hiển thị tất cả hồ sơ. Chọn học viên ở bên trái để lọc.');
    }

    $('#mainBody').html('<div class="hs-empty-main"><div style="padding:20px">Đang tải...</div></div>');
    APP.ajax(URL_AJAX, {
        action:'getPaged', page:1, pageSize:200,
        da_xoa: state.daXoa,
        search: state.keywordHS,
        loai_ho_so: state.loai,
        hoc_vien_id: state.selectedHV || 0
    }).done(function(res){
        if (!res.success){ APP.toast(res.message,'error'); return; }
        state.rowsAll = res.data || [];
        renderHoSoCards();
    });
}

function fileExtCls(name){
    if (!name) return 'none';
    var e = (name.split('.').pop() || '').toLowerCase();
    if (e === 'pdf') return 'pdf';
    if (['doc','docx'].indexOf(e) !== -1) return 'doc';
    if (['jpg','jpeg','png','gif','webp','bmp'].indexOf(e) !== -1) return 'img';
    if (['zip','rar','7z'].indexOf(e) !== -1) return 'zip';
    return 'none';
}
function fileExtLabel(name){
    if (!name) return 'NoFile';
    return ((name.split('.').pop() || '').toUpperCase()).slice(0, 5);
}
function fmtBytes(b){
    if (!b) return ''; var u=['B','KB','MB','GB']; var i=0; b=parseFloat(b);
    while(b>=1024&&i<u.length-1){ b/=1024; i++; }
    return (b<10&&i>0?b.toFixed(1):Math.round(b))+' '+u[i];
}

function renderHoSoCards(){
    var rows = state.rowsAll;
    if (!rows.length){
        var msg = state.daXoa ? 'Thùng rác trống' : 'Chưa có hồ sơ nào';
        $('#mainBody').html(
            '<div class="hs-empty-main">'
            + ICON_EMPTY
            + '<h4>'+msg+'</h4>'
            + (state.daXoa ? '' : '<p>Click nút "Thêm hồ sơ" ở góc phải để bắt đầu.</p>')
            + '</div>');
        return;
    }
    var html = '<div class="hs-card-grid">';
    rows.forEach(function(r){
        var hasFile = !!r.duong_dan;
        var cls = fileExtCls(r.duong_dan);
        var lbl = fileExtLabel(r.duong_dan);
        var isHetHan = r.ngay_het_han && new Date(r.ngay_het_han) < new Date();
        var hetHanStr = isHetHan
            ? '<div class="hs-expired">Đã hết hạn '+APP.formatDate(r.ngay_het_han)+'</div>'
            : (r.ngay_het_han ? '<div>Hết hạn: '+APP.formatDate(r.ngay_het_han)+'</div>' : '');

        html += '<div class="hs-card">'
              + '<div class="hs-card-head">'
              + '<div class="hs-fileicon '+cls+'">'+lbl+'</div>'
              + '<div style="flex:1;min-width:0">'
              + '<div class="hs-card-title">'+APP.escape(r.ten_ho_so||'')+'</div>'
              + '<div><span class="hs-card-loai">'+APP.escape(r.loai_ho_so||'-')+'</span></div>'
              + '</div>'
              + '</div>'
              + '<div class="hs-card-meta">';

        // Khi đang xem all (selectedHV=0): hiện tên học viên
        if (!state.selectedHV) {
            html += '<div><strong>'+APP.escape(r.ho_ten_hoc_vien||'')+'</strong>'
                  + (r.ma_hv ? ' <span class="text-muted">('+APP.escape(r.ma_hv)+')</span>' : '')
                  + '</div>';
        }
        if (r.so_hieu) html += '<div>Số: <strong>'+APP.escape(r.so_hieu)+'</strong></div>';
        if (r.ngay_cap) html += '<div>Cấp: '+APP.formatDate(r.ngay_cap)+'</div>';
        html += hetHanStr;
        if (hasFile && r.kich_thuoc) html += '<div class="text-muted">'+fmtBytes(r.kich_thuoc)+'</div>';

        html += '</div>'
              + '<div class="hs-card-actions">';
        html += '<button type="button" class="btn btn-sm" onclick="openDetail('+r.id+')">'
              + ICON_EYE + ' Xem</button>';
        if (hasFile){
            html += '<a href="'+URL_DL+'?id='+r.id+'" target="_blank" class="btn btn-sm btn-primary" title="Tải file">'
                  + ICON_DOWNLOAD + ' Tải</a>';
        }
        if (state.daXoa == 0){
            if (CAN_EDIT) html += '<button type="button" class="btn btn-sm icon-only" title="Sửa" onclick="openEdit('+r.id+')">'+ICON_EDIT+'</button>';
            if (CAN_DEL)  html += '<button type="button" class="btn btn-sm btn-danger icon-only" title="Xóa" onclick="trashItem('+r.id+')">'+ICON_TRASH+'</button>';
        } else {
            if (CAN_EDIT) html += '<button type="button" class="btn btn-sm btn-success" onclick="restoreItem('+r.id+')">Khôi phục</button>';
            if (CAN_DEL)  html += '<button type="button" class="btn btn-sm btn-danger" onclick="deleteItem('+r.id+')">Xóa hẳn</button>';
        }
        html += '</div></div>';
    });
    html += '</div>';
    $('#mainBody').html(html);
}

// ============ Filters ============
$('#searchHV').on('input', APP.debounce(function(){
    state.keywordHV = $(this).val(); renderStudentList();
}, 200));
$('#searchHS').on('input', APP.debounce(function(){
    state.keywordHS = $(this).val(); loadHoSo();
}, 350));
$('#fLoai').on('change', function(){ state.loai = this.value; loadHoSo(); });
$('#fDX').on('change', function(){ state.daXoa = parseInt(this.value,10)||0; loadHoSo(); loadCounts(); });

// ============ Modal ============
function openCreate(){
    $('#modalTitle').text('Thêm hồ sơ học viên');
    $('#formHS')[0].reset();
    $('#f_id').val('');
    $('#currentFileInfo').hide();
    if (state.selectedHV) $('#f_hv').val(state.selectedHV);
    $('#btnSubmit').text('Lưu');
    $('#modalForm').addClass('open');
}
function openEdit(id){
    APP.ajax(URL_AJAX, {action:'getById', id:id}).done(function(res){
        if (!res.success){ APP.toast(res.message,'error'); return; }
        var r = res.data;
        $('#modalTitle').text('Sửa hồ sơ học viên');
        $('#f_id').val(r.id);
        $('#f_hv').val(r.hoc_vien_id);
        $('#f_loai').val(r.loai_ho_so);
        $('#f_ten').val(r.ten_ho_so);
        $('#f_so').val(r.so_hieu||'');
        $('#f_noi').val(r.noi_cap||'');
        $('#f_ncap').val(r.ngay_cap||'');
        $('#f_hhan').val(r.ngay_het_han||'');
        $('#f_tt').val(r.trang_thai);
        $('#f_gc').val(r.ghi_chu||'');
        $('#f_file').val('');
        if (r.duong_dan){
            $('#currentFileInfo').show();
            $('#currentFileName').text(r.duong_dan);
            $('#currentFileSize').text(r.kich_thuoc ? ' · '+fmtBytes(r.kich_thuoc) : '');
        } else {
            $('#currentFileInfo').hide();
        }
        $('#btnSubmit').text('Lưu thay đổi');
        $('#modalForm').addClass('open');
    });
}
function closeModal(){ $('#modalForm').removeClass('open'); }

$('#formHS').on('submit', function(e){
    e.preventDefault();
    var fd = new FormData(this);
    fd.append('action', $('#f_id').val() ? 'update' : 'insert');
    var $btn = $('#btnSubmit').prop('disabled', true).text('Đang lưu...');
    $.ajax({ url:URL_AJAX, type:'POST', data:fd, processData:false, contentType:false, dataType:'json', headers: window.CSRF_TOKEN ? {'X-CSRF-Token': window.CSRF_TOKEN} : {} })
        .done(function(res){
            $btn.prop('disabled', false).text('Lưu');
            if (res.success){
                APP.toast(res.message,'success'); closeModal();
                loadHoSo(); loadStats(); loadCounts(); loadComboLoai();
            } else APP.toast(res.message||'Lỗi','error');
        })
        .fail(function(xhr){
            $btn.prop('disabled', false).text('Lưu');
            APP.toast('Lỗi kết nối (HTTP '+(xhr.status||'?')+')','error');
        });
});

function trashItem(id){ APP.confirm('Chuyển hồ sơ vào thùng rác?',function(){ APP.ajax(URL_AJAX,{action:'trash',id:id}).done(function(res){ res.success?(APP.toast(res.message,'success'),loadHoSo(),loadStats(),loadCounts()):APP.toast(res.message,'error'); }); }); }
function restoreItem(id){ APP.ajax(URL_AJAX,{action:'restore',id:id}).done(function(res){ res.success?(APP.toast(res.message,'success'),loadHoSo(),loadStats(),loadCounts()):APP.toast(res.message,'error'); }); }
function deleteItem(id){ APP.confirm('Xóa VĨNH VIỄN hồ sơ này (kèm file)?',function(){ APP.ajax(URL_AJAX,{action:'delete',id:id}).done(function(res){ res.success?(APP.toast(res.message,'success'),loadHoSo(),loadStats(),loadCounts()):APP.toast(res.message,'error'); }); },{yesText:'Xóa vĩnh viễn'}); }

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
    $('#dTitle').text(r.ten_ho_so || '-');
    $('#dSubtitle').text((r.loai_ho_so||'') + (r.ma_hv ? ' · HV: '+r.ma_hv : ''));

    var isHetHan = r.ngay_het_han && new Date(r.ngay_het_han) < new Date();
    var html = '';

    // File preview block
    if (r.duong_dan){
        var ext = (r.duong_dan.split('.').pop() || '').toLowerCase();
        var url = URL_DL + '?id=' + r.id + '&inline=1';
        html += '<div class="hs-detail-preview">';
        if (['jpg','jpeg','png','gif','webp','bmp'].indexOf(ext) !== -1){
            html += '<img src="'+url+'" alt="preview">';
        } else if (ext === 'pdf'){
            html += '<iframe src="'+url+'" title="PDF preview"></iframe>';
        } else {
            html += '<div class="hs-noprev">Không có xem trước cho định dạng ('+APP.escape(ext.toUpperCase())+'). Tải file để xem.</div>';
        }
        html += '</div>';
        html += '<div style="display:flex;gap:8px;margin-bottom:14px">';
        html += '<a href="'+URL_DL+'?id='+r.id+'" class="btn btn-primary" target="_blank">'
              + ICON_DOWNLOAD + ' Tải xuống</a>';
        if (r.kich_thuoc) html += '<span class="text-muted" style="align-self:center;font-size:12px">'+fmtBytes(r.kich_thuoc)+'</span>';
        html += '</div>';
    }

    html += '<div class="lh-detail-grid">';
    html += dRow('Học viên', APP.escape((r.ma_hv||'')+' - '+(r.ho_ten_hoc_vien||'')));
    if (r.don_vi_cong_tac) html += dRow('Đơn vị', APP.escape(r.don_vi_cong_tac));
    html += dRow('Loại hồ sơ', '<span class="hs-card-loai">'+APP.escape(r.loai_ho_so||'-')+'</span>');
    html += dRow('Số hiệu', APP.escape(r.so_hieu||'-'));
    html += dRow('Nơi cấp', APP.escape(r.noi_cap||'-'));
    html += dRow('Ngày cấp', r.ngay_cap ? APP.formatDate(r.ngay_cap) : '-');
    html += dRow('Hết hạn', r.ngay_het_han
        ? '<span class="'+(isHetHan?'text-danger':'')+'">'+APP.formatDate(r.ngay_het_han)+(isHetHan?' <strong>(Đã hết hạn)</strong>':'')+'</span>'
        : '-');
    html += dRow('Trạng thái', parseInt(r.trang_thai,10)===1
        ? '<span class="badge badge-success">Hoạt động</span>'
        : '<span class="badge badge-secondary">Ngừng</span>');
    html += '</div>';

    if (r.ghi_chu) html += '<div class="lh-detail-block"><div class="lh-detail-label">Ghi chú</div><div>'+APP.escape(r.ghi_chu)+'</div></div>';

    html += '<div class="lh-detail-block text-muted" style="font-size:12px">';
    if (r.tai_khoan_nguoi_tao) html += 'Tạo bởi: '+APP.escape(r.tai_khoan_nguoi_tao);
    if (r.ngay_tao) html += ' · '+APP.escape(r.ngay_tao);
    html += '</div>';

    if (CAN_EDIT || CAN_DEL){
        html += '<div class="lh-detail-actions">';
        if (CAN_EDIT) html += '<button class="btn" onclick="openEdit('+r.id+');closeDrawer();">Sửa</button>';
        if (CAN_DEL)  html += '<button class="btn btn-danger" onclick="trashItem('+r.id+');closeDrawer();">Xóa</button>';
        html += '</div>';
    }

    $('#dBody').html(html);
}
function dRow(label,val){ return '<div class="lh-detail-row"><div class="lh-detail-label">'+label+'</div><div class="lh-detail-val">'+val+'</div></div>'; }

// ============ Init ============
renderStudentList();
loadStats();
loadComboLoai();
loadCounts();
loadHoSo();
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
