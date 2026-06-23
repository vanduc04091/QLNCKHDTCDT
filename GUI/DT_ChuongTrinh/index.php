<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../BUS/DT_KhoaHoc_BUS.php';
require_once __DIR__ . '/../../BUS/DT_MonHoc_BUS.php';
require_once __DIR__ . '/../../BUS/DM_NhanVien_BUS.php';
require_once __DIR__ . '/../../BUS/DM_KhoaPhong_BUS.php';
require_once __DIR__ . '/../../BUS/DM_DoiTuongHocVien_BUS.php';

Helper::requireLogin();
if (!PhanQuyenHelper::hasQuyen('DT_ChuongTrinh', PhanQuyenHelper::QUYEN_XEM)) {
    echo 'Bạn không có quyền truy cập chức năng này.'; exit;
}

$canAdd = PhanQuyenHelper::hasQuyen('DT_ChuongTrinh', PhanQuyenHelper::QUYEN_THEM);
$canEdit = PhanQuyenHelper::hasQuyen('DT_ChuongTrinh', PhanQuyenHelper::QUYEN_SUA);
$canDel = PhanQuyenHelper::hasQuyen('DT_ChuongTrinh', PhanQuyenHelper::QUYEN_XOA);

$khoaCombo = DT_KhoaHoc_BUS::getCombo();
$monCombo = DT_MonHoc_BUS::getCombo();
$nvCombo = DM_NhanVien_BUS::getCombo();
$khoaPhongCombo = DM_KhoaPhong_BUS::getCombo();
$doiTuongCombo = DM_DoiTuongHocVien_BUS::getCombo();

$pageTitle = 'Quản lý chương trình đào tạo';
$activeMenu = 'DT_ChuongTrinh';
require __DIR__ . '/../layouts/header.php';
?>

<div class="breadcrumb">
    <a href="<?= AppConfig::baseUrl('GUI/dashboard/index.php') ?>">Trang chủ</a>
    <span class="sep">›</span> Đào tạo
    <span class="sep">›</span> <span>Chương trình đào tạo</span>
</div>

<div class="hv-stats">
    <div class="hv-stat">
        <div class="hv-stat-icon hv-stat-blue"><?= IconHelper::svg('school', '22') ?></div>
        <div><div class="hv-stat-label">Tổng chương trình</div><div class="hv-stat-value" id="stTotal">—</div></div>
    </div>
    <div class="hv-stat">
        <div class="hv-stat-icon hv-stat-green"><?= IconHelper::svg('book-open', '22') ?></div>
        <div><div class="hv-stat-label">Có gắn khóa học</div><div class="hv-stat-value" id="stCoKhoa">—</div></div>
    </div>
    <div class="hv-stat">
        <div class="hv-stat-icon hv-stat-purple"><?= IconHelper::svg('list', '22') ?></div>
        <div><div class="hv-stat-label">Có gắn bài học</div><div class="hv-stat-value" id="stCoMon">—</div></div>
    </div>
</div>

<div class="card">
    <div class="toolbar">
        <div class="left">
            <input type="text" id="search" class="form-control" placeholder="Tìm mã, tên chương trình, thời lượng..." style="max-width:320px">
            <select id="filterKhoa" class="form-select" style="max-width:240px">
                <option value="0">-- Tất cả khóa học --</option>
                <?php foreach ($khoaCombo as $k): ?>
                    <option value="<?= $k['id'] ?>"><?= Helper::h($k['ma_khoa_hoc'] . ' - ' . $k['ten_khoa_hoc']) ?></option>
                <?php endforeach; ?>
            </select>
            <select id="filterDoiTuong" class="form-select" style="max-width:220px">
                <option value="0">-- Tất cả đối tượng --</option>
                <?php foreach ($doiTuongCombo as $dt): ?>
                    <option value="<?= $dt['id'] ?>"><?= Helper::h($dt['ten_doi_tuong']) ?></option>
                <?php endforeach; ?>
            </select>
            <select id="filterDaXoa" class="form-select" style="max-width:160px">
                <option value="0">Đang hoạt động</option>
                <option value="1">Thùng rác</option>
            </select>
        </div>
        <div class="right">
            <?php if ($canAdd): ?>
                <button type="button" class="btn btn-primary" onclick="openCreate()">+ Thêm chương trình</button>
            <?php endif; ?>
        </div>
    </div>
    <div class="table-wrap" id="tableWrap" style="position:relative;min-height:240px">
        <table class="table">
            <thead>
                <tr>
                    <th style="width:50px" class="text-center">#</th>
                    <th style="width:55px" class="text-center">TT</th>
                    <th style="width:130px">Mã CTĐT</th>
                    <th>Tên chương trình</th>
                    <th style="width:120px">Thời lượng</th>
                    <th>Khoa phụ trách</th>
                    <th style="width:150px">Đối tượng</th>
                    <th class="text-center" style="width:80px">Khóa</th>
                    <th class="text-center" style="width:80px">Bài</th>
                    <th class="text-right" style="width:150px">Hành động</th>
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

<!-- ================== Modal Form CTĐT ================== -->
<div class="modal-backdrop" id="modalForm">
    <div class="modal" style="max-width:820px">
        <div class="modal-header">
            <h3 id="modalTitle">Thêm chương trình đào tạo</h3>
            <button type="button" class="close" onclick="closeModal()">&times;</button>
        </div>
        <form id="formCT">
            <div class="modal-body">
                <input type="hidden" name="id" id="f_id">
                <div class="form-row">
                    <div class="form-group">
                        <label>Mã chương trình <span class="required">*</span></label>
                        <input type="text" name="ma_chuong_trinh" id="f_ma" class="form-control" required maxlength="50">
                    </div>
                    <div class="form-group">
                        <label>Tên chương trình <span class="required">*</span></label>
                        <input type="text" name="ten_chuong_trinh" id="f_ten" class="form-control" required maxlength="200">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Thời lượng</label>
                        <input type="text" name="thoi_luong" id="f_thoi_luong" class="form-control" placeholder="VD: 3 tháng / 120 tiết" maxlength="100">
                    </div>
                    <div class="form-group">
                        <label>Khoa phụ trách giảng dạy</label>
                        <select name="khoa_phong_id" id="f_khoa_phong" class="form-select">
                            <option value="">-- Chọn khoa/phòng --</option>
                            <?php foreach ($khoaPhongCombo as $kp): ?>
                                <option value="<?= $kp['id'] ?>"><?= Helper::h($kp['ten_khoa']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Đối tượng học viên</label>
                        <select name="doi_tuong_id" id="f_doi_tuong" class="form-select">
                            <option value="">-- Chọn đối tượng --</option>
                            <?php foreach ($doiTuongCombo as $dt): ?>
                                <option value="<?= $dt['id'] ?>"><?= Helper::h($dt['ten_doi_tuong']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Số lượng tối đa</label>
                        <input type="number" name="so_luong_toi_da" id="f_sl" class="form-control" value="30" min="1">
                    </div>
                </div>
                <div class="form-group" style="max-width:220px">
                    <label>Thứ tự</label>
                    <input type="number" name="thu_tu" id="f_thu_tu" class="form-control" value="0" min="0">
                </div>
                <div class="form-group">
                    <label>Mô tả</label>
                    <textarea name="mo_ta" id="f_mt" class="form-control" rows="2" maxlength="1000"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn" onclick="closeModal()">Hủy</button>
                <button type="submit" class="btn btn-primary">Lưu</button>
            </div>
        </form>
    </div>
</div>

<!-- ================== Modal gắn/sửa khóa học vào CTĐT ================== -->
<div class="modal-backdrop" id="modalKhoa">
    <div class="modal" style="max-width:720px">
        <div class="modal-header">
            <h3 id="modalKhoaTitle">Gắn khóa học</h3>
            <button type="button" class="close" onclick="closeKhoaModal()">&times;</button>
        </div>
        <form id="formKhoa">
            <div class="modal-body">
                <input type="hidden" name="id" id="k_id">
                <div class="form-group" id="k_khoa_wrap">
                    <label>Khóa học <span class="required">*</span></label>
                    <select name="khoa_hoc_id" id="k_khoa" class="form-select">
                        <option value="">-- Chọn khóa học --</option>
                        <?php foreach ($khoaCombo as $k): ?>
                            <option value="<?= $k['id'] ?>"><?= Helper::h($k['ma_khoa_hoc'] . ' - ' . $k['ten_khoa_hoc']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Ngày bắt đầu</label>
                        <input type="date" name="ngay_bat_dau" id="k_nbd" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Ngày kết thúc</label>
                        <input type="date" name="ngay_ket_thuc" id="k_nkt" class="form-control">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Địa điểm</label>
                        <input type="text" name="dia_diem" id="k_dd" class="form-control" maxlength="200">
                    </div>
                    <div class="form-group">
                        <label>Trạng thái</label>
                        <select name="trang_thai" id="k_tt" class="form-select">
                            <option value="0">Chờ khai giảng</option>
                            <option value="1">Đang học</option>
                            <option value="2">Đã kết thúc</option>
                            <option value="3">Đã hủy</option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Giáo viên chủ nhiệm</label>
                        <select name="giao_vien_id" id="k_gv" class="form-select">
                            <option value="">-- Chọn --</option>
                            <?php foreach ($nvCombo as $nv): ?>
                                <option value="<?= $nv['id'] ?>"><?= Helper::h(($nv['ma_nv'] ?? '') . ' - ' . ($nv['ho_ten'] ?? '')) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>GVCN ngoài (nếu có)</label>
                        <input type="text" name="giao_vien_ngoai" id="k_gvn" class="form-control" maxlength="200">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn" onclick="closeKhoaModal()">Hủy</button>
                <button type="submit" class="btn btn-primary">Lưu</button>
            </div>
        </form>
    </div>
</div>

<style>
.dt-tabs { display:flex; gap:4px; border-bottom:1px solid var(--gray-200); margin-bottom:14px; }
.dt-tab { background:none; border:none; padding:8px 14px; font-size:13.5px; font-weight:500; color:var(--gray-500); cursor:pointer; border-bottom:2px solid transparent; }
.dt-tab.is-active { color:var(--primary); border-bottom-color:var(--primary); }
.drawer-header { display:flex; justify-content:space-between; align-items:flex-start; }
</style>
<!-- ================== Drawer chi tiết: gắn khóa + gắn môn ================== -->
<div class="drawer-backdrop" id="drawerBackdrop" onclick="closeDrawer(event)"></div>
<div class="drawer" id="ctDrawer">
    <div class="drawer-header">
        <div>
            <h3 id="drwTitle" style="margin:0">Chi tiết chương trình</h3>
            <div id="drwSub" class="text-muted" style="font-size:12.5px;margin-top:2px"></div>
        </div>
        <button type="button" class="close" onclick="closeDrawer()">&times;</button>
    </div>
    <div class="drawer-body">
        <div class="dt-tabs">
            <button type="button" class="dt-tab is-active" data-tab="khoa" onclick="switchTab('khoa')">Khóa học áp dụng</button>
            <button type="button" class="dt-tab" data-tab="mon" onclick="switchTab('mon')">Bài học</button>
        </div>

        <!-- Tab khóa học -->
        <div class="dt-tabpane" id="paneKhoa">
            <?php if ($canEdit): ?>
            <div style="margin-bottom:10px">
                <button type="button" class="btn btn-primary" onclick="openKhoaForm()">+ Gắn khóa học</button>
                <span class="text-muted" style="font-size:12px;margin-left:6px">Mỗi khóa gắn vào CTĐT có lịch học vụ riêng (ngày, địa điểm, GVCN, trạng thái).</span>
            </div>
            <?php endif; ?>
            <div id="khoaList"></div>
        </div>

        <!-- Tab bài học -->
        <div class="dt-tabpane" id="paneMon" style="display:none">
            <?php if ($canEdit): ?>
            <div style="background:#f8fafc;padding:12px;border-radius:8px;margin-bottom:12px;border:1px solid var(--gray-200)">
                <div style="font-weight:600;margin-bottom:8px;font-size:13.5px">Thêm bài học vào chương trình</div>
                <div style="display:flex;gap:8px;flex-wrap:wrap;align-items:center">
                    <select id="monAddSelect" class="form-select" style="flex:1;min-width:240px">
                        <option value="">-- Chọn bài học --</option>
                    </select>
                    <button type="button" class="btn btn-primary btn-sm" onclick="addMon()">Thêm bài</button>
                </div>
                <div class="text-muted" style="font-size:11.5px;margin-top:6px">Chỉ hiện bài học chưa thuộc chương trình nào (mỗi bài chỉ thuộc 1 CTĐT).</div>
            </div>
            <?php endif; ?>
            <div id="monSummary" class="text-muted" style="font-size:12.5px;margin-bottom:8px"></div>
            <div id="monList"></div>
        </div>
    </div>
</div>

<script>
var URL = APP_BASE + 'GUI/DT_ChuongTrinh/ajax_handler.php';
var CAN_ADD = <?= $canAdd?'true':'false' ?>, CAN_EDIT = <?= $canEdit?'true':'false' ?>, CAN_DEL = <?= $canDel?'true':'false' ?>;
var state = { page:1, pageSize:20, search:'', khoaId:0, doiTuongId:0, daXoa:0 };
var currentCT = null;

var ICON_DETAIL = '<?= addslashes(IconHelper::svg('eye', '15')) ?>';
var ICON_EDIT = '<?= addslashes(IconHelper::svg('edit', '15')) ?>';
var ICON_TRASH = '<?= addslashes(IconHelper::svg('trash', '15')) ?>';
var ICON_RESTORE = '<?= addslashes(IconHelper::svg('refresh', '15')) ?>';

// Trạng thái học vụ của cặp (khóa+CTĐT)
var TT_LABEL = {0:'Chờ khai giảng',1:'Đang học',2:'Đã kết thúc',3:'Đã hủy'};
var TT_CLS = {0:'badge-warning',1:'badge-info',2:'badge-success',3:'badge-danger'};

function loadStats(){
    APP.ajax(URL, {action:'getStats'}).done(function(res){
        if(!res.success) return;
        $('#stTotal').text(res.data.total||0);
        $('#stCoKhoa').text(res.data.co_khoa||0);
        $('#stCoMon').text(res.data.co_mon||0);
    });
}

function load(){
    APP.showLoading('#tableWrap');
    APP.ajax(URL, {
        action:'getPaged', page:state.page, pageSize:state.pageSize,
        search:state.search, khoa_hoc_id:state.khoaId, doi_tuong_id:state.doiTuongId, da_xoa:state.daXoa
    }).done(function(res){
        APP.hideLoading('#tableWrap');
        if(!res.success){ APP.toast(res.message,'error'); return; }
        renderRows(res.data||[]);
        var p = res.pagination || {};
        var from = ((p.currentPage-1)*p.pageSize)+1, to = Math.min(p.currentPage*p.pageSize, p.totalRecords);
        $('#pageInfo').text(p.totalRecords ? ('Hiển thị ' + from + '–' + to + ' / ' + p.totalRecords) : 'Không có bản ghi');
        $('#pageNav').html(APP.renderPagination(p));
    });
}
$('#pageNav').on('click', 'button[data-p]', function(){
    var p = parseInt($(this).data('p'),10);
    if(!p || p===state.page) return;
    state.page = p; load();
});

function renderRows(rows){
    var $b = $('#tbody').empty();
    if(!rows.length){ $b.append('<tr><td colspan="10" class="text-center text-muted" style="padding:30px">Không có dữ liệu</td></tr>'); return; }
    rows.forEach(function(r, i){
        var acts = '';
        if (state.daXoa==1){
            if (CAN_EDIT) acts += '<button class="btn btn-sm btn-success" title="Khôi phục" onclick="restoreItem('+r.id+')">'+ICON_RESTORE+'</button> ';
            if (CAN_DEL) acts += '<button class="btn btn-sm btn-danger" title="Xóa vĩnh viễn" onclick="deleteItem('+r.id+')">'+ICON_TRASH+'</button>';
        } else {
            acts += '<button class="btn btn-sm" title="Chi tiết" onclick="openDrawer('+r.id+')">'+ICON_DETAIL+'</button> ';
            if (CAN_EDIT) acts += '<button class="btn btn-sm" title="Sửa" onclick="openEdit('+r.id+')">'+ICON_EDIT+'</button> ';
            if (CAN_DEL) acts += '<button class="btn btn-sm btn-danger" title="Xóa" onclick="trashItem('+r.id+')">'+ICON_TRASH+'</button>';
        }
        $b.append('<tr>'+
            '<td class="text-center">'+((state.page-1)*state.pageSize+i+1)+'</td>'+
            '<td class="text-center" style="font-weight:600">'+(r.thu_tu||0)+'</td>'+
            '<td><strong>'+APP.escape(r.ma_chuong_trinh||'')+'</strong></td>'+
            '<td>'+APP.escape(r.ten_chuong_trinh||'')+'</td>'+
            '<td>'+APP.escape(r.thoi_luong||'—')+'</td>'+
            '<td>'+APP.escape(r.ten_khoa_phong||'—')+'</td>'+
            '<td>'+APP.escape(r.ten_doi_tuong||'—')+'</td>'+
            '<td class="text-center">'+(r.so_khoa_hoc||0)+'</td>'+
            '<td class="text-center">'+(r.so_mon_hoc||0)+'</td>'+
            '<td><div class="actions">'+acts+'</div></td>'+
        '</tr>');
    });
}

// ============ Form CTĐT ============
function openCreate(){
    document.getElementById('formCT').reset();
    $('#f_id').val(''); $('#f_sl').val(30); $('#f_thu_tu').val(0);
    $('#modalTitle').text('Thêm chương trình đào tạo');
    $('#modalForm').addClass('open');
}
function openEdit(id){
    APP.ajax(URL, {action:'getById', id:id}).done(function(res){
        if(!res.success){ APP.toast(res.message,'error'); return; }
        var e = res.data;
        $('#f_id').val(e.id); $('#f_ma').val(e.ma_chuong_trinh); $('#f_ten').val(e.ten_chuong_trinh);
        $('#f_thoi_luong').val(e.thoi_luong||''); $('#f_khoa_phong').val(e.khoa_phong_id||'');
        $('#f_doi_tuong').val(e.doi_tuong_id||''); $('#f_sl').val(e.so_luong_toi_da||30);
        $('#f_thu_tu').val(e.thu_tu||0);
        $('#f_mt').val(e.mo_ta||'');
        $('#modalTitle').text('Sửa chương trình đào tạo');
        $('#modalForm').addClass('open');
    });
}
function closeModal(){ $('#modalForm').removeClass('open'); }

$('#formCT').on('submit', function(e){
    e.preventDefault();
    var data = $(this).serializeArray().reduce(function(a,f){ a[f.name]=f.value; return a; },{});
    data.action = $('#f_id').val() ? 'update' : 'insert';
    APP.ajax(URL, data).done(function(res){
        if(res.success){ APP.toast(res.message,'success'); closeModal(); load(); loadStats(); }
        else APP.toast(res.message,'error');
    });
});

function trashItem(id){ APP.confirm('Chuyển chương trình này vào thùng rác?', function(){ APP.ajax(URL,{action:'trash',id:id}).done(function(res){ res.success?(APP.toast(res.message,'success'),load(),loadStats()):APP.toast(res.message,'error'); }); }); }
function restoreItem(id){ APP.ajax(URL,{action:'restore',id:id}).done(function(res){ res.success?(APP.toast(res.message,'success'),load(),loadStats()):APP.toast(res.message,'error'); }); }
function deleteItem(id){ APP.confirm('Xóa VĨNH VIỄN chương trình này?', function(){ APP.ajax(URL,{action:'delete',id:id}).done(function(res){ res.success?(APP.toast(res.message,'success'),load(),loadStats()):APP.toast(res.message,'error'); }); }, {yesText:'Xóa vĩnh viễn'}); }

// ============ Drawer chi tiết ============
function openDrawer(id){
    APP.ajax(URL, {action:'getById', id:id}).done(function(res){
        if(!res.success){ APP.toast(res.message,'error'); return; }
        currentCT = res.data;
        $('#drwTitle').text(res.data.ten_chuong_trinh||'');
        $('#drwSub').text((res.data.ma_chuong_trinh||'') + (res.data.ten_khoa_phong?(' · '+res.data.ten_khoa_phong):''));
        $('#drawerBackdrop').addClass('open'); $('#ctDrawer').addClass('open');
        switchTab('khoa');
    });
}
function closeDrawer(ev){ if(ev && ev.target!==document.getElementById('drawerBackdrop')) return; $('#drawerBackdrop').removeClass('open'); $('#ctDrawer').removeClass('open'); }
function switchTab(t){
    $('.dt-tab').removeClass('is-active'); $('.dt-tab[data-tab="'+t+'"]').addClass('is-active');
    $('#paneKhoa').toggle(t==='khoa'); $('#paneMon').toggle(t==='mon');
    if(t==='khoa') loadKhoa(); else loadMon();
}

var khoaRows = [];
function loadKhoa(){
    APP.ajax(URL,{action:'khoa_list', chuong_trinh_id:currentCT.id}).done(function(res){
        if(!res.success) return;
        khoaRows = res.data||[];
        var $l=$('#khoaList').empty();
        if(!khoaRows.length){ $l.html('<div class="text-muted" style="padding:16px">Chưa gắn khóa học nào.</div>'); return; }
        khoaRows.forEach(function(r){
            var tt = parseInt(r.trang_thai,10);
            var meta = [];
            if(r.ngay_bat_dau) meta.push(APP.formatDate(r.ngay_bat_dau)+(r.ngay_ket_thuc?(' → '+APP.formatDate(r.ngay_ket_thuc)):''));
            else if(r.ngay_ket_thuc) meta.push('… → '+APP.formatDate(r.ngay_ket_thuc));
            if(r.dia_diem) meta.push(APP.escape(r.dia_diem));
            var gv = r.ten_giao_vien || r.giao_vien_ngoai || '';
            if(gv) meta.push('GVCN: '+APP.escape(gv));
            $l.append('<div class="pc-row" style="display:flex;justify-content:space-between;align-items:flex-start;gap:10px;padding:10px;border:1px solid var(--gray-200);border-radius:8px;margin-bottom:6px">'+
                '<div style="flex:1">'+
                    '<div><strong>'+APP.escape(r.ma_khoa_hoc||'')+'</strong> - '+APP.escape(r.ten_khoa_hoc||'')+
                        ' <span class="badge '+(TT_CLS[tt]||'')+'">'+(TT_LABEL[tt]||'')+'</span></div>'+
                    (meta.length?'<div class="text-muted" style="font-size:12px;margin-top:3px">'+meta.join(' · ')+'</div>':'')+
                '</div>'+
                (CAN_EDIT?'<div style="white-space:nowrap">'+
                    '<button class="btn btn-sm" onclick="editKhoa('+r.id+')">Sửa</button> '+
                    '<button class="btn btn-sm btn-danger" onclick="removeKhoa('+r.id+')">Gỡ</button></div>':'')+
            '</div>');
        });
    });
}
function openKhoaForm(){
    document.getElementById('formKhoa').reset();
    $('#k_id').val(''); $('#k_tt').val(0);
    $('#k_khoa_wrap').show(); $('#k_khoa').prop('disabled', false);
    $('#modalKhoaTitle').text('Gắn khóa học');
    $('#modalKhoa').addClass('open');
}
function editKhoa(id){
    var r = khoaRows.filter(function(x){ return parseInt(x.id,10)===id; })[0];
    if(!r) return;
    document.getElementById('formKhoa').reset();
    $('#k_id').val(r.id);
    $('#k_khoa').val(r.khoa_hoc_id); $('#k_khoa').prop('disabled', true); $('#k_khoa_wrap').show();
    $('#k_nbd').val(r.ngay_bat_dau||''); $('#k_nkt').val(r.ngay_ket_thuc||'');
    $('#k_dd').val(r.dia_diem||''); $('#k_tt').val(parseInt(r.trang_thai,10)||0);
    $('#k_gv').val(r.giao_vien_id||''); $('#k_gvn').val(r.giao_vien_ngoai||'');
    $('#modalKhoaTitle').text('Sửa thông tin khóa: '+(r.ma_khoa_hoc||''));
    $('#modalKhoa').addClass('open');
}
function closeKhoaModal(){ $('#modalKhoa').removeClass('open'); }
$('#formKhoa').on('submit', function(e){
    e.preventDefault();
    var isEdit = !!$('#k_id').val();
    if(!isEdit && !parseInt($('#k_khoa').val(),10)){ APP.toast('Chọn khóa học','error'); return; }
    var data = {
        action: isEdit ? 'khoa_update' : 'khoa_add',
        id: $('#k_id').val(),
        chuong_trinh_id: currentCT.id,
        khoa_hoc_id: $('#k_khoa').val(),
        ngay_bat_dau: $('#k_nbd').val(), ngay_ket_thuc: $('#k_nkt').val(),
        dia_diem: $('#k_dd').val(), trang_thai: $('#k_tt').val(),
        giao_vien_id: $('#k_gv').val(), giao_vien_ngoai: $('#k_gvn').val()
    };
    APP.ajax(URL, data).done(function(res){
        if(res.success){ APP.toast(res.message,'success'); closeKhoaModal(); loadKhoa(); load(); }
        else APP.toast(res.message,'error');
    });
});
function removeKhoa(id){ APP.confirm('Gỡ khóa học này khỏi chương trình?', function(){ APP.ajax(URL,{action:'khoa_remove', id:id}).done(function(res){ res.success?(APP.toast(res.message,'success'),loadKhoa(),load()):APP.toast(res.message,'error'); }); }); }

function loadMon(){
    if(CAN_EDIT) ensureMonCombo();
    APP.ajax(URL,{action:'mon_list', chuong_trinh_id:currentCT.id}).done(function(res){
        if(!res.success) return;
        var items=res.data||[];
        var tongTiet=0, tongTC=0;
        items.forEach(function(r){ tongTiet+=parseInt(r.tong_so_tiet,10)||0; tongTC+=parseFloat(r.so_tin_chi)||0; });
        $('#monSummary').text('Tổng: '+items.length+' bài · '+tongTiet+' tiết · '+tongTC+' tín chỉ');
        var $l=$('#monList').empty();
        if(!items.length){ $l.html('<div class="text-muted" style="padding:16px">Chưa có bài học nào thuộc chương trình này.</div>'); return; }
        items.forEach(function(r){
            $l.append('<div class="pc-row" style="display:flex;justify-content:space-between;align-items:center;padding:8px 10px;border:1px solid var(--gray-200);border-radius:8px;margin-bottom:6px">'+
                '<div><span class="badge badge-info" style="margin-right:6px">'+(r.thu_tu||0)+'</span><strong>'+APP.escape(r.ma_mon_hoc||'')+'</strong> - '+APP.escape(r.ten_mon_hoc||'')+
                    ' <span class="text-muted" style="font-size:11px">('+(r.tong_so_tiet||0)+' tiết · '+(r.so_tin_chi||0)+' tc)</span></div>'+
                (CAN_EDIT?'<div style="white-space:nowrap"><button class="btn btn-sm" title="Lên" onclick="moveMon('+r.id+',\'up\')">↑</button> <button class="btn btn-sm" title="Xuống" onclick="moveMon('+r.id+',\'down\')">↓</button> <button class="btn btn-sm btn-danger" title="Bỏ khỏi chương trình" onclick="removeMon('+r.id+')">Gỡ</button></div>':'')+
            '</div>');
        });
    });
}
var monComboLoaded=false;
function ensureMonCombo(){
    APP.ajax(URL,{action:'mon_combo', chuong_trinh_id:currentCT.id}).done(function(res){
        if(!res.success) return;
        var $s=$('#monAddSelect').empty().append('<option value="">-- Chọn bài học --</option>');
        (res.data||[]).forEach(function(m){
            $s.append('<option value="'+m.id+'">'+APP.escape((m.ma_mon_hoc?m.ma_mon_hoc+' - ':'')+(m.ten_mon_hoc||''))+'</option>');
        });
        monComboLoaded=true;
    });
}
function addMon(){
    var monId=parseInt($('#monAddSelect').val(),10);
    if(!monId){ APP.toast('Chọn bài học','error'); return; }
    APP.ajax(URL,{action:'mon_add', chuong_trinh_id:currentCT.id, mon_hoc_id:monId}).done(function(res){
        if(res.success){ APP.toast(res.message,'success'); ensureMonCombo(); loadMon(); load(); }
        else APP.toast(res.message,'error');
    });
}
function moveMon(id,dir){ APP.ajax(URL,{action:'mon_move', id:id, dir:dir}).done(function(res){ res.success?(loadMon(),load()):APP.toast(res.message,'error'); }); }
function removeMon(id){ APP.confirm('Bỏ bài học này khỏi chương trình?', function(){ APP.ajax(URL,{action:'mon_remove', id:id}).done(function(res){ res.success?(APP.toast(res.message,'success'),ensureMonCombo(),loadMon(),load()):APP.toast(res.message,'error'); }); }); }

// ============ Filters ============
$('#search').on('input', APP.debounce(function(){ state.search=this.value; state.page=1; load(); }, 350));
$('#filterKhoa').on('change', function(){ state.khoaId=parseInt(this.value,10)||0; state.page=1; load(); });
$('#filterDoiTuong').on('change', function(){ state.doiTuongId=parseInt(this.value,10)||0; state.page=1; load(); });
$('#filterDaXoa').on('change', function(){ state.daXoa=parseInt(this.value,10)||0; state.page=1; load(); });

loadStats(); load();
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
