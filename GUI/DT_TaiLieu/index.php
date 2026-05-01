<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../BUS/DT_KhoaHoc_BUS.php';
require_once __DIR__ . '/../../BUS/DT_LopHoc_BUS.php';

Helper::requireLogin();
if (!PhanQuyenHelper::hasQuyen('DT_TaiLieu', PhanQuyenHelper::QUYEN_XEM)) {
    echo 'Bạn không có quyền truy cập chức năng này.'; exit;
}
$canAdd = PhanQuyenHelper::hasQuyen('DT_TaiLieu', PhanQuyenHelper::QUYEN_THEM);
$canEdit = PhanQuyenHelper::hasQuyen('DT_TaiLieu', PhanQuyenHelper::QUYEN_SUA);
$canDel = PhanQuyenHelper::hasQuyen('DT_TaiLieu', PhanQuyenHelper::QUYEN_XOA);

$khoaList = DT_KhoaHoc_BUS::getCombo();
$lopList = DT_LopHoc_BUS::getPaged(1, 500, '', 0, 0, -1)['data'];

$pageTitle = 'Tài liệu';
$activeMenu = 'DT_TaiLieu';
require __DIR__ . '/../layouts/header.php';
?>
<div class="breadcrumb">
    <a href="<?= AppConfig::baseUrl('GUI/dashboard/index.php') ?>">Trang chủ</a>
    <span class="sep">›</span> Đào tạo
    <span class="sep">›</span> <span>Tài liệu</span>
</div>

<div class="hv-stats">
    <div class="hv-stat">
        <div class="hv-stat-icon hv-stat-blue">
            <?= IconHelper::svg('file-text', '22') ?>
        </div>
        <div><div class="hv-stat-label">Tổng tài liệu</div><div class="hv-stat-value" id="stTotal">—</div></div>
    </div>
    <div class="hv-stat">
        <div class="hv-stat-icon hv-stat-green">
            <?= IconHelper::svg('download', '22') ?>
        </div>
        <div><div class="hv-stat-label">Lượt tải</div><div class="hv-stat-value" id="stTai">—</div></div>
    </div>
    <div class="hv-stat">
        <div class="hv-stat-icon hv-stat-orange">
            <?= IconHelper::svg('eye', '22') ?>
        </div>
        <div><div class="hv-stat-label">Lượt xem</div><div class="hv-stat-value" id="stXem">—</div></div>
    </div>
    <div class="hv-stat">
        <div class="hv-stat-icon hv-stat-purple">
            <?= IconHelper::svg('activity', '22') ?>
        </div>
        <div><div class="hv-stat-label">Dung lượng</div><div class="hv-stat-value" id="stSize">—</div></div>
    </div>
</div>

<div class="card">
    <div class="lh-toolbar">
        <div class="lh-toolbar-left" style="flex:1">
            <input type="text" id="search" class="form-control" placeholder="Tìm theo mã, tiêu đề, tác giả..." style="max-width:320px">
            <select id="fSortBy" class="form-select" style="max-width:160px">
                <option value="newest">Mới nhất</option>
                <option value="download">Tải nhiều nhất</option>
                <option value="view">Xem nhiều nhất</option>
                <option value="name">Tên A-Z</option>
            </select>
        </div>
        <div class="lh-toolbar-right">
            <?php if ($canAdd): ?>
                <button type="button" class="btn btn-primary" onclick="openCreate()">
                    <?= IconHelper::svg('plus', '16') ?>
                    Thêm tài liệu
                </button>
            <?php endif; ?>
        </div>
    </div>

    <div class="lh-filter">
        <div class="lh-filter-field">
            <label>Loại tài liệu</label>
            <select id="fLoai" class="form-select">
                <option value="">Tất cả loại</option>
                <option value="1">Giáo trình</option>
                <option value="2">Bài giảng</option>
                <option value="3">Tài liệu tham khảo</option>
                <option value="4">Đề thi / Bài tập</option>
                <option value="5">Video</option>
                <option value="6">Khác</option>
            </select>
        </div>
        <div class="lh-filter-field">
            <label>Khóa học</label>
            <select id="fKH" class="form-select">
                <option value="0">Tất cả khóa</option>
                <?php foreach ($khoaList as $k): ?>
                    <option value="<?= $k['id'] ?>"><?= Helper::h($k['ma_khoa_hoc'] . ' - ' . $k['ten_khoa_hoc']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="lh-filter-field">
            <label>Lớp học</label>
            <select id="fLop" class="form-select">
                <option value="0">Tất cả lớp</option>
                <?php foreach ($lopList as $l): ?>
                    <option value="<?= $l['id'] ?>"><?= Helper::h($l['ma_lop'] . ' - ' . $l['ten_lop']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="lh-filter-field">
            <label>Môn học</label>
            <select id="fMon" class="form-select">
                <option value="0">Tất cả môn</option>
            </select>
        </div>
    </div>

    <div class="tl-quickfilter">
        <label class="tl-quickfilter-chip"><input type="checkbox" id="fBatBuoc"> <span>Chỉ bắt buộc</span></label>
        <label class="tl-quickfilter-chip"><input type="checkbox" id="fCongKhai"> <span>Chỉ công khai</span></label>
        <label class="tl-quickfilter-chip">
            <select id="fDX" class="tl-quickfilter-select">
                <option value="0">Đang dùng</option>
                <option value="1">Thùng rác</option>
            </select>
        </label>
    </div>

    <div class="tl-grid" id="tlGrid"></div>

    <div class="pagination-wrap">
        <div id="pageInfo" class="text-muted">-</div>
        <div id="pageNav"></div>
    </div>
</div>

<!-- Modal Form -->
<div class="modal-backdrop" id="modalForm">
    <div class="modal" style="max-width:780px">
        <div class="modal-header">
            <h3 id="modalTitle">Thêm tài liệu</h3>
            <button type="button" class="close" onclick="closeModal()">&times;</button>
        </div>
        <form id="formTL" enctype="multipart/form-data">
            <div class="modal-body">
                <input type="hidden" name="id" id="f_id">

                <div class="form-row">
                    <div class="form-group">
                        <label>Mã tài liệu <span class="required">*</span></label>
                        <input type="text" name="ma_tai_lieu" id="f_ma" class="form-control" required maxlength="50">
                    </div>
                    <div class="form-group">
                        <label>Loại tài liệu <span class="required">*</span></label>
                        <select name="loai_tai_lieu" id="f_loai" class="form-select">
                            <option value="1">Giáo trình</option>
                            <option value="2">Bài giảng</option>
                            <option value="3">Tài liệu tham khảo</option>
                            <option value="4">Đề thi / Bài tập</option>
                            <option value="5">Video</option>
                            <option value="6">Khác</option>
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

                <!-- Tabs nguồn tài liệu -->
                <div class="tl-source-tabs">
                    <button type="button" class="tl-tab is-active" data-tab="file">Upload file</button>
                    <button type="button" class="tl-tab" data-tab="link">Link ngoài</button>
                </div>

                <div class="tl-tab-panel" data-panel="file">
                    <div class="tl-dropzone" id="dropzone">
                        <input type="file" name="tai_lieu_file" id="f_file" hidden accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.txt,.zip,.rar,.png,.jpg,.jpeg,.gif,.mp4,.mp3,.webm">
                        <div class="tl-dropzone-empty" id="dzEmpty">
                            ' + ICON_UPLOAD_40 + '
                            <div class="tl-dz-title">Kéo thả file vào đây hoặc <button type="button" class="tl-dz-pick" onclick="document.getElementById('f_file').click()">chọn từ máy</button></div>
                            <div class="tl-dz-hint">Cho phép: PDF, Word, PPT, Excel, MP4, ZIP… · Tối đa 50MB</div>
                        </div>
                        <div class="tl-dropzone-file" id="dzFile" style="display:none">
                            <div class="tl-file-icon" id="dzIcon"></div>
                            <div class="tl-file-info">
                                <div class="tl-file-name" id="dzName">—</div>
                                <div class="tl-file-meta" id="dzMeta">—</div>
                            </div>
                            <button type="button" class="btn btn-sm" onclick="clearFile()">Đổi file</button>
                        </div>
                    </div>
                    <div class="tl-current-file" id="currentFile" style="display:none">
                        <span class="text-muted" style="font-size:12.5px">File hiện tại:</span>
                        <strong id="currentFileName">—</strong>
                        <span class="text-muted" id="currentFileSize"></span>
                    </div>
                </div>

                <div class="tl-tab-panel" data-panel="link" style="display:none">
                    <div class="form-group">
                        <label>Link ngoài (Google Drive, YouTube, URL trực tiếp...)</label>
                        <input type="url" name="link_ngoai" id="f_ln" class="form-control" placeholder="https://...">
                        <div class="tl-link-hint" id="linkHint" style="display:none"></div>
                    </div>
                </div>

                <div class="form-row-3" style="margin-top:14px">
                    <div class="form-group">
                        <label>Tác giả</label>
                        <input type="text" name="tac_gia" id="f_tg" class="form-control" maxlength="200">
                    </div>
                    <div class="form-group">
                        <label>Năm xuất bản</label>
                        <input type="number" name="nam_xuat_ban" id="f_nxb" class="form-control" min="1900" max="2100">
                    </div>
                    <div class="form-group">
                        <label>Nhà xuất bản</label>
                        <input type="text" name="nha_xuat_ban" id="f_nhaxb" class="form-control" maxlength="200">
                    </div>
                </div>

                <fieldset class="tl-scope">
                    <legend>Phạm vi áp dụng (chọn 1 hoặc để trống)</legend>
                    <div class="form-row-3">
                        <div class="form-group">
                            <label>Khóa học</label>
                            <select name="khoa_hoc_id" id="f_kh" class="form-select">
                                <option value="">--</option>
                                <?php foreach ($khoaList as $k): ?>
                                    <option value="<?= $k['id'] ?>"><?= Helper::h($k['ma_khoa_hoc'] . ' - ' . $k['ten_khoa_hoc']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Lớp học</label>
                            <select name="lop_hoc_id" id="f_lop" class="form-select">
                                <option value="">--</option>
                                <?php foreach ($lopList as $l): ?>
                                    <option value="<?= $l['id'] ?>"><?= Helper::h($l['ma_lop'] . ' - ' . $l['ten_lop']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Môn học</label>
                            <select name="mon_hoc_id" id="f_mon" class="form-select">
                                <option value="">--</option>
                            </select>
                        </div>
                    </div>
                </fieldset>

                <div class="tl-flags">
                    <label class="tl-flag-toggle">
                        <input type="checkbox" name="bat_buoc" id="f_bb" value="1">
                        <span class="tl-flag-text"><strong>Bắt buộc</strong><small>Học viên cần đọc/xem</small></span>
                    </label>
                    <label class="tl-flag-toggle">
                        <input type="checkbox" name="cong_khai" id="f_ck" value="1">
                        <span class="tl-flag-text"><strong>Công khai</strong><small>Cho phép học viên xem</small></span>
                    </label>
                    <label class="tl-flag-toggle">
                        <input type="checkbox" name="trang_thai" id="f_tt" value="1" checked>
                        <span class="tl-flag-text"><strong>Hoạt động</strong><small>Hiển thị trong danh sách</small></span>
                    </label>
                </div>

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

<!-- Drawer Detail -->
<div class="drawer-backdrop" id="drawerDetail">
    <div class="drawer">
        <div class="drawer-header">
            <div>
                <h3 id="dTitle" style="margin:0">Chi tiết tài liệu</h3>
                <div id="dSubtitle" class="text-muted" style="font-size:12.5px;margin-top:2px"></div>
            </div>
            <button type="button" class="close" onclick="closeDrawer()">&times;</button>
        </div>
        <div class="drawer-body" id="dBody"></div>
    </div>
</div>

<script>
var URL_AJAX = APP_BASE + 'GUI/DT_TaiLieu/ajax_handler.php';
var URL_DOWNLOAD = APP_BASE + 'GUI/DT_TaiLieu/download.php';
var CAN_EDIT = <?= $canEdit?'true':'false' ?>;
var CAN_DEL = <?= $canDel?'true':'false' ?>;

var state = { page:1, pageSize:24, daXoa:0, search:'', sortBy:'newest',
              filter:{loai:0, kh:0, lop:0, mon:0, batBuoc:0, congKhai:0} };
var monLoaded = false;

var LOAI_TXT = {1:'Giáo trình', 2:'Bài giảng', 3:'Tham khảo', 4:'Đề thi/Bài tập', 5:'Video', 6:'Khác'};

var ICON_FILE_TEXT = '<?= addslashes(IconHelper::svg('file-text', '22')) ?>';
var ICON_DOWNLOAD = '<?= addslashes(IconHelper::svg('download', '22')) ?>';
var ICON_EYE = '<?= addslashes(IconHelper::svg('eye', '22')) ?>';
var ICON_ACTIVITY = '<?= addslashes(IconHelper::svg('activity', '22')) ?>';
var ICON_ADD = '<?= addslashes(IconHelper::svg('plus', '16')) ?>';
var ICON_EMPTY = '<?= addslashes(IconHelper::svg('file-text', '40')) ?>';
var ICON_GRADUATION = '<?= addslashes(IconHelper::svg('graduation-cap', '11')) ?>';
var ICON_USERS = '<?= addslashes(IconHelper::svg('users', '11')) ?>';
var ICON_BOOK_OPEN = '<?= addslashes(IconHelper::svg('book-open', '11')) ?>';
var ICON_STAR = '<?= addslashes(IconHelper::svg('star', '10')) ?>';
var ICON_DOWNLOAD_SM = '<?= addslashes(IconHelper::svg('download', '14')) ?>';
var ICON_EXTERNAL_LINK = '<?= addslashes(IconHelper::svg('external-link', '14')) ?>';
var ICON_DETAIL = '<?= addslashes(IconHelper::svg('eye', '14')) ?>';
var ICON_EDIT = '<?= addslashes(IconHelper::svg('edit', '14')) ?>';
var ICON_TRASH = '<?= addslashes(IconHelper::svg('trash', '14')) ?>';
var ICON_USER = '<?= addslashes(IconHelper::svg('user', '11')) ?>';
var ICON_EYE_SM = '<?= addslashes(IconHelper::svg('eye', '11')) ?>';
var ICON_UPLOAD_40 = '<?= addslashes(IconHelper::svg('upload', '40')) ?>';

// ============ Helpers ============
function fmtBytes(b){
    if (!b) return '';
    var u=['B','KB','MB','GB']; var i=0; b=parseFloat(b);
    while(b>=1024 && i<u.length-1){ b/=1024; i++; }
    return (b<10&&i>0?b.toFixed(1):Math.round(b)) + ' ' + u[i];
}
function formatIcon(df){
    var d = (df||'').toLowerCase();
    var data = {
        pdf:    {cls:'pdf', label:'PDF'},
        doc:    {cls:'doc', label:'DOC'}, docx:{cls:'doc', label:'DOC'},
        xls:    {cls:'xls', label:'XLS'}, xlsx:{cls:'xls', label:'XLS'},
        ppt:    {cls:'ppt', label:'PPT'}, pptx:{cls:'ppt', label:'PPT'},
        txt:    {cls:'txt', label:'TXT'},
        zip:    {cls:'zip', label:'ZIP'}, rar:{cls:'zip', label:'RAR'},
        png:    {cls:'img', label:'IMG'}, jpg:{cls:'img', label:'IMG'}, jpeg:{cls:'img', label:'IMG'}, gif:{cls:'img', label:'IMG'},
        mp4:    {cls:'video', label:'MP4'}, webm:{cls:'video', label:'WEBM'}, mp3:{cls:'video', label:'MP3'},
        youtube:{cls:'video', label:'YT'}, gdrive:{cls:'gdrive', label:'GD'}, gdocs:{cls:'doc', label:'DOCS'},
        link:   {cls:'link', label:'LINK'}
    }[d] || {cls:'other', label:(d?d.toUpperCase():'?')};
    return '<div class="tl-fmt tl-fmt-'+data.cls+'">'+APP.escape(data.label)+'</div>';
}
function ytThumb(url){
    var m = url.match(/(?:youtu\.be\/|youtube\.com\/(?:watch\?v=|embed\/|v\/))([\w-]{11})/);
    return m ? 'https://img.youtube.com/vi/'+m[1]+'/mqdefault.jpg' : null;
}

// ============ Stats / Load ============
function loadStats(){
    APP.ajax(URL_AJAX,{action:'getStats'}).done(function(res){
        if (!res.success) return;
        $('#stTotal').text(res.data.total||0);
        $('#stTai').text(res.data.tong_tai||0);
        $('#stXem').text(res.data.tong_xem||0);
        $('#stSize').text(fmtBytes(res.data.tong_dung_luong||0) || '—');
    });
}

function load(){
    APP.showLoading('#tlGrid');
    APP.ajax(URL_AJAX, {
        action:'getPaged', page:state.page, pageSize:state.pageSize,
        da_xoa:state.daXoa, search:state.search, sort_by:state.sortBy,
        loai_tai_lieu:state.filter.loai,
        khoa_hoc_id:state.filter.kh, lop_hoc_id:state.filter.lop, mon_hoc_id:state.filter.mon,
        bat_buoc:state.filter.batBuoc, cong_khai:state.filter.congKhai
    }).done(function(res){
        APP.hideLoading('#tlGrid');
        if (!res.success){ APP.toast(res.message,'error'); return; }
        renderGrid(res.data);
        renderPager(res.pagination);
    });
}

function renderGrid(rows){
    var $g = $('#tlGrid').empty();
    if (!rows.length){
        $g.html('<div class="empty-state" style="padding:60px 20px;grid-column:1/-1"><div class="icon">' + ICON_EMPTY + '</div>Không có tài liệu nào</div>');
        return;
    }
    rows.forEach(function(t){
        var df = t.dinh_dang || '';
        var thumb = '';
        if (df==='youtube' && t.link_ngoai){
            var ty = ytThumb(t.link_ngoai);
            if (ty) thumb = '<img src="'+ty+'" loading="lazy" alt="">';
            else thumb = formatIcon(df);
        } else if (['png','jpg','jpeg','gif'].indexOf(df)>=0 && t.file_name){
            thumb = '<img src="'+APP_BASE+'assets/uploads/tailieu/'+APP.escape(t.file_name)+'" loading="lazy" alt="">';
        } else {
            thumb = formatIcon(df);
        }

        var scope = '';
        if (t.ten_khoa_hoc) scope += '<span class="tl-scope-chip tl-scope-kh" title="Khóa học">' + ICON_GRADUATION + ' '+APP.escape(t.ma_khoa_hoc||'')+'</span>';
        if (t.ten_lop) scope += '<span class="tl-scope-chip tl-scope-lop" title="Lớp học">' + ICON_USERS + ' '+APP.escape(t.ma_lop||'')+'</span>';
        if (t.ten_mon_hoc) scope += '<span class="tl-scope-chip tl-scope-mon" title="Môn học">' + ICON_BOOK_OPEN + ' '+APP.escape(t.ma_mon_hoc||'')+'</span>';

        var flags = '';
        if (parseInt(t.bat_buoc,10)===1) flags += '<span class="tl-flag tl-flag-required">' + ICON_STAR + ' Bắt buộc</span>';
        if (parseInt(t.cong_khai,10)===1) flags += '<span class="tl-flag tl-flag-public">Công khai</span>';

        var actions = '';
        if (state.daXoa==0){
            // Tải/Xem button
            if (t.file_name) {
                actions += '<a class="btn btn-sm btn-primary tl-dl" href="'+URL_DOWNLOAD+'?id='+t.id+'" onclick="bumpDownload('+t.id+')" title="Tải xuống">' + ICON_DOWNLOAD_SM + ' Tải</a>';
            } else if (t.link_ngoai) {
                actions += '<a class="btn btn-sm btn-primary" href="'+APP.escape(t.link_ngoai)+'" target="_blank" rel="noopener" onclick="bumpDownload('+t.id+')" title="Mở link">' + ICON_EXTERNAL_LINK + ' Mở</a>';
            }
            actions += '<button class="btn btn-sm" onclick="openDetail('+t.id+')" title="Chi tiết">' + ICON_DETAIL + '</button>';
            if (CAN_EDIT) actions += '<button class="btn btn-sm" onclick="openEdit('+t.id+')" title="Sửa">' + ICON_EDIT + '</button>';
            if (CAN_DEL) actions += '<button class="btn btn-sm btn-danger" onclick="trashItem('+t.id+')" title="Xóa">' + ICON_TRASH + '</button>';
        } else {
            if (CAN_EDIT) actions += '<button class="btn btn-sm btn-success" onclick="restoreItem('+t.id+')">Khôi phục</button>';
            if (CAN_DEL) actions += '<button class="btn btn-sm btn-danger" onclick="deleteItem('+t.id+')">Xóa vĩnh viễn</button>';
        }

        $g.append(
            '<div class="tl-card" data-id="'+t.id+'">'+
                '<div class="tl-card-thumb">'+thumb+'</div>'+
                '<div class="tl-card-body">'+
                    '<div class="tl-card-loai">'+APP.escape(LOAI_TXT[t.loai_tai_lieu]||'')+'</div>'+
                    '<div class="tl-card-title" title="'+APP.escape(t.tieu_de||'')+'">'+APP.escape(t.tieu_de||'')+'</div>'+
                    (t.tac_gia?'<div class="tl-card-author">' + ICON_USER + ' '+APP.escape(t.tac_gia)+(t.nam_xuat_ban?' · '+t.nam_xuat_ban:'')+'</div>':'')+
                    (scope?'<div class="tl-card-scope">'+scope+'</div>':'')+
                    (flags?'<div class="tl-card-flags">'+flags+'</div>':'')+
                '</div>'+
                '<div class="tl-card-foot">'+
                    '<div class="tl-card-stats"><span title="Lượt xem">' + ICON_EYE_SM + ' '+(t.luot_xem||0)+'</span><span title="Lượt tải">' + ICON_DOWNLOAD_SM + ' '+(t.luot_tai||0)+'</span>'+(t.file_size?'<span title="Dung lượng">'+fmtBytes(t.file_size)+'</span>':'')+'</div>'+
                    '<div class="actions">'+actions+'</div>'+
                '</div>'+
            '</div>'
        );
    });
}

function renderPager(p){
    var from = (p.currentPage-1)*p.pageSize + 1;
    var to = Math.min(from+p.pageSize-1, p.totalRecords);
    $('#pageInfo').text(p.totalRecords?'Hiển thị '+from+'-'+to+' / '+p.totalRecords:'Không có bản ghi');
    $('#pageNav').html(APP.renderPagination(p));
}

$('#pageNav').on('click','button[data-p]',function(){ var p=parseInt($(this).data('p'),10); if(!p||p===state.page) return; state.page=p; load(); });
$('#search').on('input', APP.debounce(function(){ state.search=$(this).val(); state.page=1; load(); }, 350));
$('#fSortBy').on('change', function(){ state.sortBy=this.value; state.page=1; load(); });
$('#fLoai').on('change', function(){ state.filter.loai=parseInt(this.value,10)||0; state.page=1; load(); });
$('#fKH').on('change', function(){ state.filter.kh=parseInt(this.value,10)||0; state.page=1; load(); });
$('#fLop').on('change', function(){ state.filter.lop=parseInt(this.value,10)||0; state.page=1; load(); });
$('#fMon').on('change', function(){ state.filter.mon=parseInt(this.value,10)||0; state.page=1; load(); });
$('#fBatBuoc').on('change', function(){ state.filter.batBuoc=this.checked?1:0; state.page=1; load(); });
$('#fCongKhai').on('change', function(){ state.filter.congKhai=this.checked?1:0; state.page=1; load(); });
$('#fDX').on('change', function(){ state.daXoa=parseInt(this.value,10)||0; state.page=1; load(); });

// Load mon combo cho filter top
APP.ajax(URL_AJAX,{action:'getComboMonHoc'}).done(function(res){
    if (!res.success) return;
    var html = '<option value="0">Tất cả môn</option>';
    (res.data||[]).forEach(function(m){ html += '<option value="'+m.id+'">'+APP.escape(m.ma_mon_hoc+' - '+m.ten_mon_hoc)+'</option>'; });
    $('#fMon').html(html);
});

// ============ Modal upload ============
var currentFile = null;

function ensureMonForForm(cb){
    if (monLoaded){ cb && cb(); return; }
    APP.ajax(URL_AJAX,{action:'getComboMonHoc'}).done(function(res){
        if (!res.success) return;
        var html = '<option value="">--</option>';
        (res.data||[]).forEach(function(m){ html += '<option value="'+m.id+'">'+APP.escape(m.ma_mon_hoc+' - '+m.ten_mon_hoc)+'</option>'; });
        $('#formTL #f_mon').html(html);
        monLoaded = true; cb && cb();
    });
}

$('.tl-source-tabs').on('click', '.tl-tab', function(){
    var t = $(this).data('tab');
    $('.tl-tab').removeClass('is-active');
    $(this).addClass('is-active');
    $('.tl-tab-panel').hide();
    $('.tl-tab-panel[data-panel="'+t+'"]').show();
});

// Dropzone events
var $dz = $('#dropzone');
$dz.on('dragover', function(e){ e.preventDefault(); $dz.addClass('is-drag'); });
$dz.on('dragleave drop', function(){ $dz.removeClass('is-drag'); });
$dz.on('drop', function(e){
    e.preventDefault();
    var f = e.originalEvent.dataTransfer.files[0];
    if (f) handleFile(f);
});
$('#f_file').on('change', function(){
    if (this.files && this.files[0]) handleFile(this.files[0]);
});
function handleFile(f){
    if (f.size > 50*1024*1024){ APP.toast('File quá lớn (tối đa 50MB)','error'); return; }
    currentFile = f;
    var ext = (f.name.split('.').pop()||'').toLowerCase();
    $('#dzIcon').html(formatIcon(ext));
    $('#dzName').text(f.name);
    $('#dzMeta').text(fmtBytes(f.size) + ' · ' + ext.toUpperCase());
    $('#dzEmpty').hide(); $('#dzFile').css('display','flex');
    // gán file vào input để form submit gửi đi
    var dt = new DataTransfer();
    dt.items.add(f);
    $('#f_file')[0].files = dt.files;
}
function clearFile(){
    currentFile = null;
    $('#f_file').val('');
    $('#dzEmpty').show(); $('#dzFile').hide();
}

// Link auto-detect
$('#f_ln').on('input', APP.debounce(function(){
    var v = $(this).val().trim();
    if (!v){ $('#linkHint').hide(); return; }
    var label = 'Link';
    if (v.indexOf('youtube.com')>=0 || v.indexOf('youtu.be')>=0) label = 'YouTube';
    else if (v.indexOf('drive.google.com')>=0) label = 'Google Drive';
    else if (v.indexOf('docs.google.com')>=0) label = 'Google Docs';
    else if (/\.pdf$/i.test(v)) label = 'PDF trực tiếp';
    $('#linkHint').show().text('Hệ thống nhận diện: ' + label);
}, 250));

function openCreate(){
    ensureMonForForm();
    $('#modalTitle').text('Thêm tài liệu');
    $('#formTL')[0].reset();
    $('#f_id').val('');
    clearFile();
    $('#currentFile').hide();
    $('#linkHint').hide();
    $('.tl-tab').removeClass('is-active'); $('.tl-tab[data-tab="file"]').addClass('is-active');
    $('.tl-tab-panel').hide(); $('.tl-tab-panel[data-panel="file"]').show();
    $('#f_tt').prop('checked', true);
    $('#btnSubmit').text('Lưu');
    $('#modalForm').addClass('open');
}

function openEdit(id){
    ensureMonForForm(function(){
        APP.ajax(URL_AJAX,{action:'getById', id:id}).done(function(res){
            if (!res.success){ APP.toast(res.message,'error'); return; }
            var t = res.data;
            $('#modalTitle').text('Sửa tài liệu');
            $('#f_id').val(t.id);
            $('#f_ma').val(t.ma_tai_lieu);
            $('#f_loai').val(t.loai_tai_lieu);
            $('#f_td').val(t.tieu_de);
            $('#f_mt').val(t.mo_ta||'');
            $('#f_ln').val(t.link_ngoai||'');
            $('#f_tg').val(t.tac_gia||'');
            $('#f_nxb').val(t.nam_xuat_ban||'');
            $('#f_nhaxb').val(t.nha_xuat_ban||'');
            $('#f_kh').val(t.khoa_hoc_id||'');
            $('#formTL #f_lop').val(t.lop_hoc_id||'');
            $('#formTL #f_mon').val(t.mon_hoc_id||'');
            $('#f_bb').prop('checked', parseInt(t.bat_buoc,10)===1);
            $('#f_ck').prop('checked', parseInt(t.cong_khai,10)===1);
            $('#f_tt').prop('checked', parseInt(t.trang_thai,10)===1);
            $('#f_gc').val(t.ghi_chu||'');
            clearFile();
            if (t.file_name){
                $('#currentFile').show();
                $('#currentFileName').text(t.file_goc || t.file_name);
                $('#currentFileSize').text(t.file_size?'· '+fmtBytes(t.file_size):'');
            } else {
                $('#currentFile').hide();
            }
            // Tab mặc định: file nếu có file, link nếu có link
            var tab = t.link_ngoai && !t.file_name ? 'link' : 'file';
            $('.tl-tab').removeClass('is-active'); $('.tl-tab[data-tab="'+tab+'"]').addClass('is-active');
            $('.tl-tab-panel').hide(); $('.tl-tab-panel[data-panel="'+tab+'"]').show();
            $('#linkHint').hide();
            if (t.link_ngoai) $('#f_ln').trigger('input');
            $('#btnSubmit').text('Lưu thay đổi');
            $('#modalForm').addClass('open');
        });
    });
}
function closeModal(){ $('#modalForm').removeClass('open'); }

$('#formTL').on('submit', function(e){
    e.preventDefault();
    var fd = new FormData(this);
    fd.append('action', $('#f_id').val()?'update':'insert');
    // Đảm bảo checkbox unchecked vẫn gửi 0
    ['bat_buoc','cong_khai','trang_thai'].forEach(function(k){
        if (!fd.has(k)) fd.append(k, '0');
    });
    var $btn = $('#btnSubmit').prop('disabled', true).text('Đang lưu...');
    $.ajax({ url: URL_AJAX, type:'POST', data: fd, processData:false, contentType:false, dataType:'json' })
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

function trashItem(id){ APP.confirm('Chuyển tài liệu vào thùng rác?',function(){ APP.ajax(URL_AJAX,{action:'trash',id:id}).done(function(res){ res.success?(APP.toast(res.message,'success'),load(),loadStats()):APP.toast(res.message,'error'); }); }); }
function restoreItem(id){ APP.ajax(URL_AJAX,{action:'restore',id:id}).done(function(res){ res.success?(APP.toast(res.message,'success'),load(),loadStats()):APP.toast(res.message,'error'); }); }
function deleteItem(id){ APP.confirm('Xóa VĨNH VIỄN tài liệu này (kèm file)?',function(){ APP.ajax(URL_AJAX,{action:'delete',id:id}).done(function(res){ res.success?(APP.toast(res.message,'success'),load(),loadStats()):APP.toast(res.message,'error'); }); },{yesText:'Xóa vĩnh viễn'}); }

function bumpDownload(id){ APP.ajax(URL_AJAX,{action:'incDownload',id:id}); /* fire-and-forget */ }

// ============ Drawer ============
function openDetail(id){
    $('#drawerDetail').addClass('open').find('.drawer').addClass('open');
    $('#dTitle').text('Đang tải...'); $('#dSubtitle').text('');
    $('#dBody').html('<div style="padding:30px;text-align:center;color:var(--gray-500)">Đang tải...</div>');
    APP.ajax(URL_AJAX,{action:'getById',id:id}).done(function(res){
        if (!res.success){ $('#dBody').html('<div style="padding:20px;color:#b91c1c">'+APP.escape(res.message||'')+'</div>'); return; }
        renderDetail(res.data);
    });
}
function closeDrawer(){ $('#drawerDetail').removeClass('open').find('.drawer').removeClass('open'); }

function renderDetail(t){
    $('#dTitle').text(t.tieu_de||'-');
    var sub = [LOAI_TXT[t.loai_tai_lieu]||''];
    if (t.ma_tai_lieu) sub.unshift(t.ma_tai_lieu);
    $('#dSubtitle').text(sub.join(' · '));

    var html = '';
    // Preview
    var df = (t.dinh_dang||'').toLowerCase();
    if (df === 'pdf' && t.file_name) {
        html += '<iframe class="tl-preview-frame" src="'+URL_DOWNLOAD+'?id='+t.id+'&inline=1" title="Xem PDF"></iframe>';
    } else if (df === 'youtube' && t.link_ngoai) {
        var m = (t.link_ngoai).match(/(?:youtu\.be\/|youtube\.com\/(?:watch\?v=|embed\/|v\/))([\w-]{11})/);
        if (m) html += '<div class="tl-preview-yt"><iframe src="https://www.youtube.com/embed/'+m[1]+'" allowfullscreen></iframe></div>';
    } else if (['png','jpg','jpeg','gif'].indexOf(df)>=0 && t.file_name) {
        html += '<div class="tl-preview-img"><img src="'+APP_BASE+'assets/uploads/tailieu/'+APP.escape(t.file_name)+'" alt=""></div>';
    } else {
        html += '<div class="tl-preview-icon">'+formatIcon(df||'link')+'</div>';
    }

    // Action buttons
    html += '<div class="tl-detail-actions">';
    if (t.file_name) html += '<a class="btn btn-primary" href="'+URL_DOWNLOAD+'?id='+t.id+'" onclick="bumpDownload('+t.id+')">' + ICON_DOWNLOAD_SM + ' Tải xuống</a>';
    if (t.link_ngoai) html += '<a class="btn" href="'+APP.escape(t.link_ngoai)+'" target="_blank" rel="noopener" onclick="bumpDownload('+t.id+')">' + ICON_EXTERNAL_LINK + ' Mở link</a>';
    html += '</div>';

    if (t.mo_ta) html += '<div class="lh-detail-block"><div class="lh-detail-label">Mô tả</div><div>'+APP.escape(t.mo_ta).replace(/\n/g,'<br>')+'</div></div>';

    html += '<div class="lh-detail-grid">';
    html += dRow('Tác giả', t.tac_gia ? APP.escape(t.tac_gia) : '-');
    html += dRow('Năm xuất bản', t.nam_xuat_ban || '-');
    html += dRow('NXB', t.nha_xuat_ban ? APP.escape(t.nha_xuat_ban) : '-');
    html += dRow('Định dạng', (df||'-').toUpperCase());
    html += dRow('Dung lượng', t.file_size ? fmtBytes(t.file_size) : '-');
    html += dRow('Lượt xem', t.luot_xem || 0);
    html += dRow('Lượt tải', t.luot_tai || 0);
    html += '</div>';

    var scopeParts = [];
    if (t.ten_khoa_hoc) scopeParts.push('Khóa: <strong>'+APP.escape(t.ma_khoa_hoc+' - '+t.ten_khoa_hoc)+'</strong>');
    if (t.ten_lop) scopeParts.push('Lớp: <strong>'+APP.escape(t.ma_lop+' - '+t.ten_lop)+'</strong>');
    if (t.ten_mon_hoc) scopeParts.push('Môn: <strong>'+APP.escape(t.ma_mon_hoc+' - '+t.ten_mon_hoc)+'</strong>');
    if (scopeParts.length) html += '<div class="lh-detail-block"><div class="lh-detail-label">Phạm vi</div><div>'+scopeParts.join('<br>')+'</div></div>';

    var flagParts = [];
    if (parseInt(t.bat_buoc,10)===1) flagParts.push('<span class="tl-flag tl-flag-required">Bắt buộc</span>');
    if (parseInt(t.cong_khai,10)===1) flagParts.push('<span class="tl-flag tl-flag-public">Công khai</span>');
    if (flagParts.length) html += '<div class="lh-detail-block"><div class="lh-detail-label">Cờ</div><div style="display:flex;gap:6px">'+flagParts.join('')+'</div></div>';

    if (t.ghi_chu) html += '<div class="lh-detail-block"><div class="lh-detail-label">Ghi chú</div><div>'+APP.escape(t.ghi_chu)+'</div></div>';

    if (CAN_EDIT || CAN_DEL){
        html += '<div class="lh-detail-actions">';
        if (CAN_EDIT) html += '<button class="btn" onclick="openEdit('+t.id+');closeDrawer();">Sửa</button>';
        if (CAN_DEL) html += '<button class="btn btn-danger" onclick="trashItem('+t.id+');closeDrawer();">Xóa</button>';
        html += '</div>';
    }

    $('#dBody').html(html);
}
function dRow(label,val){ return '<div class="lh-detail-row"><div class="lh-detail-label">'+label+'</div><div class="lh-detail-val">'+val+'</div></div>'; }

// Init
load(); loadStats();
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
