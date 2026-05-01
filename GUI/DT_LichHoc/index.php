<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../BUS/DT_LopHoc_BUS.php';

Helper::requireLogin();
if (!PhanQuyenHelper::hasQuyen('DT_LichHoc', PhanQuyenHelper::QUYEN_XEM)) {
    echo 'Bạn không có quyền truy cập chức năng này.'; exit;
}
$canAdd = PhanQuyenHelper::hasQuyen('DT_LichHoc', PhanQuyenHelper::QUYEN_THEM);
$canEdit = PhanQuyenHelper::hasQuyen('DT_LichHoc', PhanQuyenHelper::QUYEN_SUA);
$canDel = PhanQuyenHelper::hasQuyen('DT_LichHoc', PhanQuyenHelper::QUYEN_XOA);

$lopList = DT_LopHoc_BUS::getPaged(1, 500, '', 0, 0, -1)['data'];

$pageTitle = 'Quản lý lịch học';
$activeMenu = 'DT_LichHoc';
require __DIR__ . '/../layouts/header.php';
?>
<div class="breadcrumb">
    <a href="<?= AppConfig::baseUrl('GUI/dashboard/index.php') ?>">Trang chủ</a>
    <span class="sep">›</span> Đào tạo
    <span class="sep">›</span> <span>Lịch học</span>
</div>

<!-- Stats -->
<div class="hv-stats">
    <div class="hv-stat">
        <div class="hv-stat-icon hv-stat-blue">
            <?= IconHelper::svg('calendar', '22') ?>
        </div>
        <div><div class="hv-stat-label">Tổng số buổi</div><div class="hv-stat-value" id="stTotal">—</div></div>
    </div>
    <div class="hv-stat">
        <div class="hv-stat-icon hv-stat-orange">
            <?= IconHelper::svg('clock', '22') ?>
        </div>
        <div><div class="hv-stat-label">Trong kỳ xem</div><div class="hv-stat-value" id="stRange">—</div></div>
    </div>
    <div class="hv-stat">
        <div class="hv-stat-icon hv-stat-green">
            <?= IconHelper::svg('check-circle', '22') ?>
        </div>
        <div><div class="hv-stat-label">Đã diễn ra</div><div class="hv-stat-value" id="stDone">—</div></div>
    </div>
    <div class="hv-stat">
        <div class="hv-stat-icon hv-stat-purple">
            <?= IconHelper::svg('alert-triangle', '22') ?>
        </div>
        <div><div class="hv-stat-label">Hoãn / Hủy</div><div class="hv-stat-value" id="stIssue">—</div></div>
    </div>
</div>

<!-- Main card -->
<div class="card">
    <!-- Toolbar: segmented view + navigator -->
    <div class="lh-toolbar">
        <div class="lh-toolbar-left">
            <div class="segmented" role="tablist" aria-label="Chế độ xem">
                <button type="button" class="seg-btn" data-view="month" role="tab" aria-selected="false">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    Tháng
                </button>
                <button type="button" class="seg-btn is-active" data-view="week" role="tab" aria-selected="true">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="3" y1="10" x2="21" y2="10"/><line x1="8" y1="14" x2="8" y2="20"/><line x1="16" y1="14" x2="16" y2="20"/></svg>
                    Tuần
                </button>
                <button type="button" class="seg-btn" data-view="list" role="tab" aria-selected="false">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
                    Danh sách
                </button>
            </div>
            <div class="lh-nav">
                <button type="button" class="btn btn-icon" id="btnPrev" aria-label="Kỳ trước">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
                </button>
                <button type="button" class="btn btn-sm" id="btnToday">Hôm nay</button>
                <button type="button" class="btn btn-icon" id="btnNext" aria-label="Kỳ sau">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                </button>
                <div class="lh-period" id="periodLabel">—</div>
            </div>
        </div>
        <div class="lh-toolbar-right">
            <?php if ($canAdd): ?>
                <button type="button" class="btn" onclick="openBulk()">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><path d="M9 14l2 2 4-4"/></svg>
                    Tạo hàng loạt
                </button>
                <button type="button" class="btn btn-primary" onclick="openCreate()">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    Thêm buổi học
                </button>
            <?php endif; ?>
        </div>
    </div>

    <!-- Filter row -->
    <div class="lh-filter">
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
            <label>Giảng viên</label>
            <select id="fGv" class="form-select"><option value="0">Tất cả giảng viên</option></select>
        </div>
        <div class="lh-filter-field">
            <label>Trạng thái</label>
            <select id="fTt" class="form-select">
                <option value="">Tất cả</option>
                <option value="0">Kế hoạch</option>
                <option value="1">Đã diễn ra</option>
                <option value="2">Hoãn</option>
                <option value="3">Hủy</option>
            </select>
        </div>
        <div class="lh-filter-field lh-filter-grow">
            <label>Tìm kiếm</label>
            <input type="text" id="fSearch" class="form-control" placeholder="Tiêu đề, phòng, lớp...">
        </div>
    </div>

    <!-- Legend -->
    <div class="lh-legend">
        <span class="lh-legend-item"><span class="lh-dot lh-dot-plan"></span> Kế hoạch</span>
        <span class="lh-legend-item"><span class="lh-dot lh-dot-done"></span> Đã diễn ra</span>
        <span class="lh-legend-item"><span class="lh-dot lh-dot-post"></span> Hoãn</span>
        <span class="lh-legend-item"><span class="lh-dot lh-dot-cancel"></span> Hủy</span>
    </div>

    <!-- Calendar views -->
    <div id="calMonth" class="lh-cal lh-cal-month" style="display:none"></div>
    <div id="calWeek" class="lh-cal lh-cal-week"></div>

    <!-- List view -->
    <div id="calList" style="display:none;padding:0 0 14px">
        <div class="table-wrap" id="listWrap" style="position:relative;min-height:200px">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width:44px" class="text-center">#</th>
                        <th style="width:110px">Ngày</th>
                        <th style="width:110px">Giờ</th>
                        <th>Tiêu đề / Lớp</th>
                        <th style="width:160px">Phòng</th>
                        <th style="width:180px">Giảng viên</th>
                        <th class="text-center" style="width:130px">Trạng thái</th>
                        <th class="text-right" style="width:140px">Hành động</th>
                    </tr>
                </thead>
                <tbody id="listTbody"></tbody>
            </table>
        </div>
        <div class="pagination-wrap">
            <div id="listPageInfo" class="text-muted">-</div>
            <div id="listPageNav"></div>
        </div>
    </div>
</div>

<!-- ================== Modal Form Buổi học ================== -->
<div class="modal-backdrop" id="modalForm">
    <div class="modal" style="max-width:760px">
        <div class="modal-header">
            <h3 id="modalTitle">Thêm buổi học</h3>
            <button type="button" class="close" onclick="closeModal()">&times;</button>
        </div>
        <form id="formBuoi">
            <div class="modal-body">
                <input type="hidden" name="id" id="f_id">
                <input type="hidden" name="force_conflict" id="f_force" value="0">

                <div class="form-row">
                    <div class="form-group">
                        <label>Lớp học <span class="required">*</span></label>
                        <select name="lop_hoc_id" id="f_lop" class="form-select" required>
                            <option value="">-- Chọn lớp --</option>
                            <?php foreach ($lopList as $l): ?>
                                <option value="<?= $l['id'] ?>"><?= Helper::h($l['ma_lop'] . ' - ' . $l['ten_lop']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group" style="max-width:140px">
                        <label>Buổi thứ</label>
                        <input type="number" name="buoi_thu" id="f_buoi" class="form-control" min="0" value="0" title="Để 0 để tự tăng">
                    </div>
                </div>

                <div class="form-group">
                    <label>Tiêu đề <span class="required">*</span></label>
                    <input type="text" name="tieu_de" id="f_tieu_de" class="form-control" required maxlength="200">
                </div>

                <div class="form-row-3">
                    <div class="form-group">
                        <label>Ngày học <span class="required">*</span></label>
                        <input type="date" name="ngay_hoc" id="f_ngay" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Giờ bắt đầu <span class="required">*</span></label>
                        <input type="time" name="gio_bat_dau" id="f_gbd" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Giờ kết thúc <span class="required">*</span></label>
                        <input type="time" name="gio_ket_thuc" id="f_gkt" class="form-control" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Phòng học</label>
                        <input type="text" name="phong_hoc" id="f_phong" class="form-control" maxlength="150" placeholder="VD: Giảng đường A - Tầng 2">
                    </div>
                    <div class="form-group">
                        <label>Môn học</label>
                        <select name="mon_hoc_id" id="f_mon" class="form-select"><option value="">--</option></select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Giảng viên (nhân viên)</label>
                        <select name="giang_vien_id" id="f_gv" class="form-select"><option value="">--</option></select>
                    </div>
                    <div class="form-group">
                        <label>Giảng viên ngoài</label>
                        <input type="text" name="giang_vien_ngoai" id="f_gvn" class="form-control" maxlength="200">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Trạng thái</label>
                        <select name="trang_thai" id="f_tt" class="form-select">
                            <option value="0">Kế hoạch</option>
                            <option value="1">Đã diễn ra</option>
                            <option value="2">Hoãn</option>
                            <option value="3">Hủy</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Ghi chú</label>
                        <input type="text" name="ghi_chu" id="f_gc" class="form-control" maxlength="500">
                    </div>
                </div>

                <div class="form-group">
                    <label>Nội dung buổi học</label>
                    <textarea name="noi_dung" id="f_nd" class="form-control" rows="2" placeholder="Mô tả ngắn nội dung bài học"></textarea>
                </div>

                <div id="conflictBox" class="lh-conflict" style="display:none"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn" onclick="closeModal()">Hủy</button>
                <button type="submit" class="btn btn-primary" id="btnSubmitBuoi">Lưu</button>
            </div>
        </form>
    </div>
</div>

<!-- ================== Modal Bulk Generator ================== -->
<div class="modal-backdrop" id="modalBulk">
    <div class="modal" style="max-width:820px">
        <div class="modal-header">
            <h3>Tạo lịch hàng loạt</h3>
            <button type="button" class="close" onclick="closeBulk()">&times;</button>
        </div>
        <form id="formBulk">
            <div class="modal-body">
                <input type="hidden" name="force_conflict" id="b_force" value="0">
                <div class="lh-bulk-step">
                    <div class="lh-step-num">1</div>
                    <div class="lh-step-title">Chọn lớp và khoảng thời gian</div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Lớp học <span class="required">*</span></label>
                        <select name="lop_hoc_id" id="b_lop" class="form-select" required>
                            <option value="">-- Chọn lớp --</option>
                            <?php foreach ($lopList as $l): ?>
                                <option value="<?= $l['id'] ?>"><?= Helper::h($l['ma_lop'] . ' - ' . $l['ten_lop']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Môn học (tùy chọn)</label>
                        <select name="mon_hoc_id" id="b_mon" class="form-select"><option value="">--</option></select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Từ ngày <span class="required">*</span></label>
                        <input type="date" name="from" id="b_from" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Đến ngày <span class="required">*</span></label>
                        <input type="date" name="to" id="b_to" class="form-control" required>
                    </div>
                </div>

                <div class="lh-bulk-step">
                    <div class="lh-step-num">2</div>
                    <div class="lh-step-title">Quy tắc lặp</div>
                </div>
                <div class="lh-pattern">
                    <label class="lh-radio">
                        <input type="radio" name="pattern" value="daily"> <span>Hàng ngày</span>
                    </label>
                    <label class="lh-radio">
                        <input type="radio" name="pattern" value="weekly" checked> <span>Hàng tuần (cùng thứ với "Từ ngày")</span>
                    </label>
                    <label class="lh-radio">
                        <input type="radio" name="pattern" value="custom"> <span>Chọn thứ trong tuần</span>
                    </label>
                </div>
                <div class="lh-weekdays" id="bWeekdays">
                    <?php
                    $wdLabels = [1=>'T2',2=>'T3',3=>'T4',4=>'T5',5=>'T6',6=>'T7',0=>'CN'];
                    foreach ($wdLabels as $v => $lbl): ?>
                        <label class="lh-chip-check">
                            <input type="checkbox" name="weekdays[]" value="<?= $v ?>"> <span><?= $lbl ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>

                <div class="lh-bulk-step">
                    <div class="lh-step-num">3</div>
                    <div class="lh-step-title">Thông tin buổi học</div>
                </div>
                <div class="form-group">
                    <label>Tiêu đề (nếu để trống sẽ dùng tên lớp)</label>
                    <input type="text" name="tieu_de" id="b_tieu_de" class="form-control" maxlength="200">
                </div>
                <div class="form-row-3">
                    <div class="form-group">
                        <label>Giờ bắt đầu <span class="required">*</span></label>
                        <input type="time" name="gio_bat_dau" id="b_gbd" class="form-control" required value="07:30">
                    </div>
                    <div class="form-group">
                        <label>Giờ kết thúc <span class="required">*</span></label>
                        <input type="time" name="gio_ket_thuc" id="b_gkt" class="form-control" required value="11:00">
                    </div>
                    <div class="form-group">
                        <label>Phòng học</label>
                        <input type="text" name="phong_hoc" id="b_phong" class="form-control" maxlength="150">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Giảng viên (nhân viên)</label>
                        <select name="giang_vien_id" id="b_gv" class="form-select"><option value="">--</option></select>
                    </div>
                    <div class="form-group">
                        <label>Giảng viên ngoài</label>
                        <input type="text" name="giang_vien_ngoai" id="b_gvn" class="form-control" maxlength="200">
                    </div>
                </div>
                <div class="form-group">
                    <label>Nội dung mặc định</label>
                    <textarea name="noi_dung" id="b_nd" class="form-control" rows="2"></textarea>
                </div>
                <div id="bulkPreview" class="lh-bulk-preview" style="display:none"></div>
            </div>
            <div class="modal-footer">
                <label style="flex:1;display:flex;align-items:center;gap:8px;font-size:13px;color:var(--gray-600)">
                    <input type="checkbox" id="b_skipConflict" checked> Bỏ qua buổi trùng lịch
                </label>
                <button type="button" class="btn" onclick="closeBulk()">Hủy</button>
                <button type="submit" class="btn btn-primary">Tạo lịch</button>
            </div>
        </form>
    </div>
</div>

<!-- ================== Drawer Detail ================== -->
<div class="drawer-backdrop" id="drawerDetail">
    <div class="drawer">
        <div class="drawer-header">
            <div>
                <h3 id="dTitle" style="margin:0">Chi tiết buổi học</h3>
                <div id="dSubtitle" class="text-muted" style="font-size:12.5px;margin-top:2px"></div>
            </div>
            <button type="button" class="close" onclick="closeDrawer()">&times;</button>
        </div>
        <div class="drawer-body" id="dBody"></div>
    </div>
</div>

<script>
var URL = APP_BASE + 'GUI/DT_LichHoc/ajax_handler.php';
var CAN_ADD = <?= $canAdd?'true':'false' ?>;
var CAN_EDIT = <?= $canEdit?'true':'false' ?>;
var CAN_DEL = <?= $canDel?'true':'false' ?>;

var state = {
    view: 'week',
    anchor: new Date(),            // Mốc hiện tại (ngày đầu tiên của kỳ đang xem)
    filter: { lop_hoc_id: 0, giang_vien_id: 0, trang_thai: '', search: '' },
    listPage: 1, listPageSize: 20,
    events: [],                    // Cache buổi học trong kỳ
};
var nvLoaded = false, monLoaded = false;
var WD_VI = ['CN','T2','T3','T4','T5','T6','T7'];
var MONTH_VI = ['Tháng 1','Tháng 2','Tháng 3','Tháng 4','Tháng 5','Tháng 6','Tháng 7','Tháng 8','Tháng 9','Tháng 10','Tháng 11','Tháng 12'];

// ============== Helpers ==============
function pad(n){ return n<10?'0'+n:''+n; }
function ymd(d){ return d.getFullYear()+'-'+pad(d.getMonth()+1)+'-'+pad(d.getDate()); }
function parseYmd(s){
    if (!s) return new Date(NaN);
    // Tolerant: chấp nhận "YYYY-MM-DD" hoặc "YYYY-MM-DD HH:MM:SS"
    var core = String(s).substring(0, 10).split('-');
    return new Date(+core[0], +core[1]-1, +core[2]);
}
function safeDow(s){ var d = parseYmd(s); return isNaN(d.getTime()) ? '' : WD_VI[d.getDay()]; }
function startOfWeek(d){
    // Tuần bắt đầu Thứ 2
    var x = new Date(d); var dow = x.getDay(); var diff = (dow === 0 ? -6 : 1-dow);
    x.setDate(x.getDate()+diff); x.setHours(0,0,0,0); return x;
}
function addDays(d,n){ var x=new Date(d); x.setDate(x.getDate()+n); return x; }
function isSameDay(a,b){ return a.getFullYear()===b.getFullYear() && a.getMonth()===b.getMonth() && a.getDate()===b.getDate(); }
function todayD(){ var t=new Date(); t.setHours(0,0,0,0); return t; }

function statusClass(tt) {
    switch(parseInt(tt,10)){ case 0: return 'plan'; case 1: return 'done'; case 2: return 'post'; case 3: return 'cancel'; default: return 'plan'; }
}
function statusText(tt) {
    switch(parseInt(tt,10)){ case 0: return 'Kế hoạch'; case 1: return 'Đã diễn ra'; case 2: return 'Hoãn'; case 3: return 'Hủy'; default: return ''; }
}
function statusIcon(tt) {
    switch(parseInt(tt,10)){
        case 0: return '<svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>';
        case 1: return '<svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>';
        case 2: return '<svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><rect x="6" y="4" width="4" height="16"/><rect x="14" y="4" width="4" height="16"/></svg>';
        case 3: return '<svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>';
    }
    return '';
}

function computeRange() {
    if (state.view === 'month') {
        var s = new Date(state.anchor.getFullYear(), state.anchor.getMonth(), 1);
        var e = new Date(state.anchor.getFullYear(), state.anchor.getMonth()+1, 0);
        // Mở rộng đến đầu tuần và cuối tuần để lấp grid 6 hàng × 7 cột
        var gridStart = startOfWeek(s);
        var gridEnd = addDays(gridStart, 41);
        return { from: ymd(gridStart), to: ymd(gridEnd), label: MONTH_VI[state.anchor.getMonth()]+' '+state.anchor.getFullYear(), gridStart: gridStart };
    } else if (state.view === 'week') {
        var ws = startOfWeek(state.anchor);
        var we = addDays(ws, 6);
        return { from: ymd(ws), to: ymd(we), label: 'Tuần ' + ymd(ws) + ' → ' + ymd(we), gridStart: ws };
    } else {
        var s = new Date(state.anchor.getFullYear(), state.anchor.getMonth(), 1);
        var e = new Date(state.anchor.getFullYear(), state.anchor.getMonth()+1, 0);
        return { from: ymd(s), to: ymd(e), label: MONTH_VI[state.anchor.getMonth()]+' '+state.anchor.getFullYear() };
    }
}

// ============== Load data ==============
function loadEvents() {
    var r = computeRange();
    $('#periodLabel').text(r.label);
    if (state.view === 'list') { loadList(); return; }
    var $wrap = state.view === 'month' ? $('#calMonth') : $('#calWeek');
    APP.showLoading($wrap);
    APP.ajax(URL, $.extend({
        action:'getRange', from:r.from, to:r.to
    }, state.filter)).done(function(res){
        APP.hideLoading($wrap);
        if (!res.success) { APP.toast(res.message,'error'); return; }
        state.events = res.data || [];
        state.view === 'month' ? renderMonth(r) : renderWeek(r);
    });
    loadStats();
}

function loadStats() {
    var r = computeRange();
    APP.ajax(URL, {action:'getStats', from:r.from, to:r.to}).done(function(res){
        if (!res.success) return;
        $('#stTotal').text(res.data.total||0);
        $('#stRange').text(res.data.trong_ky||0);
        $('#stDone').text(res.data.da_day||0);
        $('#stIssue').text((parseInt(res.data.hoan,10)||0) + (parseInt(res.data.huy,10)||0));
    });
}

// ============== Render MONTH ==============
function renderMonth(r) {
    var grouped = {};
    state.events.forEach(function(ev){
        (grouped[ev.ngay_hoc] = grouped[ev.ngay_hoc] || []).push(ev);
    });
    var html = '<div class="lh-month-head">';
    ['T2','T3','T4','T5','T6','T7','CN'].forEach(function(d){ html += '<div class="lh-month-day">'+d+'</div>'; });
    html += '</div><div class="lh-month-grid">';
    var today = todayD();
    var curMonth = state.anchor.getMonth();
    for (var i=0; i<42; i++) {
        var d = addDays(r.gridStart, i);
        var k = ymd(d);
        var out = d.getMonth() !== curMonth ? ' is-out' : '';
        var tod = isSameDay(d, today) ? ' is-today' : '';
        html += '<div class="lh-month-cell'+out+tod+'" data-date="'+k+'">';
        html += '<div class="lh-month-num">'+d.getDate()+'</div>';
        html += '<div class="lh-month-events">';
        var items = grouped[k] || [];
        items.slice(0,3).forEach(function(ev){
            html += pillHtml(ev, true);
        });
        if (items.length > 3) {
            html += '<button type="button" class="lh-more" data-date="'+k+'">+'+(items.length-3)+' khác</button>';
        }
        html += '</div></div>';
    }
    html += '</div>';
    $('#calMonth').html(html);
}

function pillHtml(ev, compact) {
    var cls = statusClass(ev.trang_thai);
    var time = (ev.gio_bat_dau||'').substring(0,5);
    var safeTitle = APP.escape(ev.tieu_de);
    var lopTxt = ev.ma_lop ? APP.escape(ev.ma_lop) : '';
    var html = '<button type="button" class="lh-pill lh-pill-'+cls+'" data-id="'+ev.id+'" title="'+safeTitle+'">';
    html += '<span class="lh-pill-time">'+time+'</span>';
    html += '<span class="lh-pill-title">'+safeTitle+'</span>';
    if (!compact && lopTxt) html += '<span class="lh-pill-lop">'+lopTxt+'</span>';
    html += '</button>';
    return html;
}

// ============== Render WEEK ==============
function renderWeek(r) {
    var grouped = {};
    state.events.forEach(function(ev){
        (grouped[ev.ngay_hoc] = grouped[ev.ngay_hoc] || []).push(ev);
    });
    var today = todayD();
    var html = '<div class="lh-week-grid">';
    for (var i=0; i<7; i++) {
        var d = addDays(r.gridStart, i);
        var k = ymd(d);
        var tod = isSameDay(d, today) ? ' is-today' : '';
        html += '<div class="lh-week-col'+tod+'" data-date="'+k+'">';
        html += '<div class="lh-week-head"><span class="lh-week-dow">'+WD_VI[d.getDay()]+'</span><span class="lh-week-num">'+d.getDate()+'</span></div>';
        html += '<div class="lh-week-body">';
        (grouped[k]||[]).forEach(function(ev){
            var cls = statusClass(ev.trang_thai);
            html += '<button type="button" class="lh-card lh-card-'+cls+'" data-id="'+ev.id+'">';
            html += '<div class="lh-card-time">'+(ev.gio_bat_dau||'').substring(0,5)+' – '+(ev.gio_ket_thuc||'').substring(0,5)+'</div>';
            html += '<div class="lh-card-title">'+APP.escape(ev.tieu_de)+'</div>';
            if (ev.ma_lop) html += '<div class="lh-card-meta">'+APP.escape(ev.ma_lop)+(ev.ten_lop?' · '+APP.escape(ev.ten_lop):'')+'</div>';
            if (ev.phong_hoc) html += '<div class="lh-card-meta"><svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:-1px"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg> '+APP.escape(ev.phong_hoc)+'</div>';
            html += '</button>';
        });
        html += '</div></div>';
    }
    html += '</div>';
    $('#calWeek').html(html);
}

// ============== Render LIST ==============
function loadList() {
    APP.showLoading('#listWrap');
    var r = computeRange();
    APP.ajax(URL, $.extend({
        action:'getPaged', page:state.listPage, pageSize:state.listPageSize,
        from:r.from, to:r.to
    }, state.filter)).done(function(res){
        APP.hideLoading('#listWrap');
        if (!res.success) { APP.toast(res.message,'error'); return; }
        renderListRows(res.data);
        renderListPager(res.pagination);
    });
    loadStats();
}

function renderListRows(rows) {
    var $tb = $('#listTbody').empty();
    if (!rows.length) {
        $tb.append('<tr><td colspan="8"><div class="empty-state"><div class="icon"><svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></div>Không có buổi học nào</div></td></tr>');
        return;
    }
    var stt = (state.listPage-1)*state.listPageSize;
    rows.forEach(function(r){
        stt++;
        var cls = statusClass(r.trang_thai);
        var gv = r.ten_giang_vien ? APP.escape(r.ten_giang_vien) : (r.giang_vien_ngoai?APP.escape(r.giang_vien_ngoai)+' <span class="hv-chip hv-chip-gray" style="font-size:10.5px">Ngoài</span>':'-');
        var actions = '<button class="btn btn-sm" title="Chi tiết" onclick="openDetail('+r.id+')"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg></button>';
        if (CAN_EDIT) actions += ' <button class="btn btn-sm" title="Sửa" onclick="openEdit('+r.id+')"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg></button>';
        if (CAN_DEL) actions += ' <button class="btn btn-sm btn-danger" title="Xóa" onclick="trashItem('+r.id+')"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg></button>';
        $tb.append(
            '<tr>'+
                '<td class="text-center">'+stt+'</td>'+
                '<td>'+APP.formatDate(r.ngay_hoc)+'<div class="text-muted" style="font-size:11.5px">'+WD_VI[parseYmd(r.ngay_hoc).getDay()]+'</div></td>'+
                '<td>'+(r.gio_bat_dau||'').substring(0,5)+' – '+(r.gio_ket_thuc||'').substring(0,5)+'</td>'+
                '<td><div style="font-weight:500">'+APP.escape(r.tieu_de)+'</div><div class="text-muted" style="font-size:11.5px">'+(r.ma_lop?APP.escape(r.ma_lop)+' · '+APP.escape(r.ten_lop||''):'')+'</div></td>'+
                '<td>'+(r.phong_hoc?APP.escape(r.phong_hoc):'-')+'</td>'+
                '<td>'+gv+'</td>'+
                '<td class="text-center"><span class="lh-badge lh-badge-'+cls+'">'+statusIcon(r.trang_thai)+' '+statusText(r.trang_thai)+'</span></td>'+
                '<td><div class="actions">'+actions+'</div></td>'+
            '</tr>'
        );
    });
}

function renderListPager(p) {
    var from = (p.currentPage-1)*p.pageSize + 1;
    var to = Math.min(from+p.pageSize-1, p.totalRecords);
    $('#listPageInfo').text(p.totalRecords ? 'Hiển thị '+from+'-'+to+' / '+p.totalRecords : 'Không có bản ghi');
    $('#listPageNav').html(APP.renderPagination(p));
}

$('#listPageNav').on('click', 'button[data-p]', function(){
    var p = parseInt($(this).data('p'),10); if (!p||p===state.listPage) return;
    state.listPage = p; loadList();
});

// ============== Interactions: view switch, nav ==============
$('.segmented').on('click', '.seg-btn', function(){
    var v = $(this).data('view');
    if (v === state.view) return;
    state.view = v;
    $('.seg-btn').removeClass('is-active').attr('aria-selected','false');
    $(this).addClass('is-active').attr('aria-selected','true');
    $('#calMonth,#calWeek,#calList').hide();
    if (v==='month') $('#calMonth').show();
    else if (v==='week') $('#calWeek').show();
    else $('#calList').show();
    state.listPage = 1;
    loadEvents();
});

$('#btnPrev').on('click', function(){
    if (state.view === 'week') state.anchor = addDays(state.anchor, -7);
    else state.anchor = new Date(state.anchor.getFullYear(), state.anchor.getMonth()-1, 1);
    loadEvents();
});
$('#btnNext').on('click', function(){
    if (state.view === 'week') state.anchor = addDays(state.anchor, 7);
    else state.anchor = new Date(state.anchor.getFullYear(), state.anchor.getMonth()+1, 1);
    loadEvents();
});
$('#btnToday').on('click', function(){ state.anchor = new Date(); loadEvents(); });

$('#fLop,#fGv,#fTt').on('change', function(){
    state.filter.lop_hoc_id = parseInt($('#fLop').val(),10) || 0;
    state.filter.giang_vien_id = parseInt($('#fGv').val(),10) || 0;
    state.filter.trang_thai = $('#fTt').val();
    state.listPage = 1; loadEvents();
});
$('#fSearch').on('input', APP.debounce(function(){
    state.filter.search = $(this).val();
    state.listPage = 1; loadEvents();
}, 350));

// Click pill / card → open detail
$('#calMonth,#calWeek').on('click', '.lh-pill, .lh-card', function(){
    var id = parseInt($(this).data('id'),10); if (id) openDetail(id);
});
// Click +X khác → chuyển view tuần quanh ngày đó
$('#calMonth').on('click', '.lh-more', function(e){
    e.stopPropagation();
    state.anchor = parseYmd($(this).data('date'));
    state.view = 'week';
    $('.seg-btn').removeClass('is-active').attr('aria-selected','false');
    $('.seg-btn[data-view="week"]').addClass('is-active').attr('aria-selected','true');
    $('#calMonth,#calList').hide(); $('#calWeek').show();
    loadEvents();
});
// Click vào ô ngày trống → mở form thêm buổi với ngày đó
$('#calMonth').on('click', '.lh-month-cell', function(e){
    if (e.target.closest('.lh-pill, .lh-more')) return;
    if (!CAN_ADD) return;
    var d = $(this).data('date'); if (!d) return;
    openCreate(d);
});

// ============== Combos loader ==============
function ensureCombosAsync() {
    var jobs = [];
    if (!nvLoaded) jobs.push(APP.ajax(URL, {action:'getComboNhanVien'}).done(function(res){
        if (!res.success) return;
        var html = '<option value="">--</option>';
        (res.data||[]).forEach(function(n){ html += '<option value="'+n.id+'">'+APP.escape(n.ma_nv)+' - '+APP.escape(n.ho_ten)+'</option>'; });
        $('#f_gv,#b_gv').html(html);
        $('#fGv').html('<option value="0">Tất cả giảng viên</option>' + html.replace('<option value="">--</option>',''));
        nvLoaded = true;
    }));
    if (!monLoaded) jobs.push(APP.ajax(URL, {action:'getComboMonHoc'}).done(function(res){
        if (!res.success) return;
        var html = '<option value="">--</option>';
        (res.data||[]).forEach(function(m){ html += '<option value="'+m.id+'">'+APP.escape(m.ma_mon_hoc)+' - '+APP.escape(m.ten_mon_hoc)+'</option>'; });
        $('#f_mon,#b_mon').html(html);
        monLoaded = true;
    }));
    return $.when.apply($, jobs);
}

// ============== Modal: Create / Edit ==============
function openCreate(presetDate) {
    ensureCombosAsync().then(function(){
        $('#modalTitle').text('Thêm buổi học');
        $('#formBuoi')[0].reset();
        $('#f_id').val(''); $('#f_force').val('0');
        $('#f_buoi').val(0);
        $('#f_tt').val('0');
        $('#f_ngay').val(presetDate || ymd(new Date()));
        $('#f_gbd').val('07:30'); $('#f_gkt').val('11:00');
        $('#conflictBox').hide().empty();
        $('#btnSubmitBuoi').text('Lưu');
        $('#modalForm').addClass('open');
    });
}

function openEdit(id) {
    ensureCombosAsync().then(function(){
        APP.ajax(URL, {action:'getById', id:id}).done(function(res){
            if (!res.success) { APP.toast(res.message,'error'); return; }
            var e = res.data;
            $('#modalTitle').text('Sửa buổi học');
            $('#f_id').val(e.id); $('#f_force').val('0');
            $('#f_lop').val(e.lop_hoc_id);
            $('#f_buoi').val(e.buoi_thu);
            $('#f_tieu_de').val(e.tieu_de);
            $('#f_ngay').val(e.ngay_hoc);
            $('#f_gbd').val((e.gio_bat_dau||'').substring(0,5));
            $('#f_gkt').val((e.gio_ket_thuc||'').substring(0,5));
            $('#f_phong').val(e.phong_hoc||'');
            $('#f_mon').val(e.mon_hoc_id||'');
            $('#f_gv').val(e.giang_vien_id||'');
            $('#f_gvn').val(e.giang_vien_ngoai||'');
            $('#f_tt').val(e.trang_thai);
            $('#f_gc').val(e.ghi_chu||'');
            $('#f_nd').val(e.noi_dung||'');
            $('#conflictBox').hide().empty();
            $('#btnSubmitBuoi').text('Lưu thay đổi');
            $('#modalForm').addClass('open');
        });
    });
}
function closeModal(){ $('#modalForm').removeClass('open'); }

$('#formBuoi').on('submit', function(e){
    e.preventDefault();
    var data = $(this).serializeArray();
    data.push({name:'action', value: $('#f_id').val() ? 'update' : 'insert'});
    var $btn = $('#btnSubmitBuoi').prop('disabled', true);
    APP.ajax(URL, data).done(function(res){
        $btn.prop('disabled', false);
        if (res.success) {
            APP.toast(res.message,'success'); closeModal(); loadEvents();
        } else if (res.data && res.data.conflicts) {
            showConflicts(res.data.conflicts, res.message);
        } else {
            APP.toast(res.message,'error');
        }
    }).fail(function(){ $btn.prop('disabled', false); });
});

function showConflicts(list, msg) {
    var html = '<div class="lh-conflict-title"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg> '+APP.escape(msg)+'</div>';
    html += '<ul class="lh-conflict-list">';
    list.forEach(function(c){
        html += '<li><strong>'+APP.formatDate(c.ngay_hoc)+' '+c.gio_bat_dau+'–'+c.gio_ket_thuc+'</strong>: '+APP.escape(c.tieu_de)+(c.ma_lop?' ('+APP.escape(c.ma_lop)+')':'')+(c.phong_hoc?' · '+APP.escape(c.phong_hoc):'')+'</li>';
    });
    html += '</ul>';
    html += '<div class="lh-conflict-actions"><button type="button" class="btn btn-sm btn-warning" onclick="forceSubmit()">Lưu đè (bỏ qua cảnh báo)</button></div>';
    $('#conflictBox').html(html).show();
}
function forceSubmit(){
    $('#f_force').val('1');
    $('#formBuoi').trigger('submit');
}

// ============== Modal: Bulk ==============
function openBulk() {
    ensureCombosAsync().then(function(){
        $('#formBulk')[0].reset();
        $('#b_force').val('0');
        $('input[name=pattern][value=weekly]').prop('checked', true);
        var t = new Date();
        $('#b_from').val(ymd(t));
        $('#b_to').val(ymd(addDays(t, 30)));
        $('#b_gbd').val('07:30'); $('#b_gkt').val('11:00');
        $('#b_skipConflict').prop('checked', true);
        $('#bulkPreview').hide();
        updateWeekdayState();
        $('#modalBulk').addClass('open');
    });
}
function closeBulk(){ $('#modalBulk').removeClass('open'); }

function updateWeekdayState() {
    var p = $('input[name=pattern]:checked').val();
    $('#bWeekdays').toggleClass('is-disabled', p !== 'custom');
    $('#bWeekdays input[type=checkbox]').prop('disabled', p !== 'custom');
}
$('input[name=pattern]').on('change', updateWeekdayState);

$('#formBulk').on('submit', function(e){
    e.preventDefault();
    var skip = $('#b_skipConflict').prop('checked');
    var data = $(this).serializeArray();
    data.push({name:'action', value:'bulkGenerate'});
    // Nếu KHÔNG bỏ qua thì force = 1 (chấp nhận chèn dù trùng)
    data = data.filter(function(d){ return d.name !== 'force_conflict'; });
    data.push({name:'force_conflict', value: skip ? '0' : '1'});
    APP.ajax(URL, data, {traditional:true}).done(function(res){
        if (res.success) {
            APP.toast(res.message,'success');
            closeBulk();
            loadEvents();
        } else {
            APP.toast(res.message,'error');
        }
    });
});

// ============== Drawer: Detail ==============
function openDetail(id) {
    $('#drawerDetail').addClass('open').find('.drawer').addClass('open');
    $('#dTitle').text('Đang tải...');
    $('#dSubtitle').html('');
    $('#dBody').html('<div style="padding:30px;text-align:center;color:var(--gray-500)">Đang tải...</div>');

    APP.ajax(URL, {action:'getById', id:id}).done(function(res){
        if (!res.success) {
            $('#dBody').html('<div style="padding:30px;text-align:center;color:var(--gray-500)">'+APP.escape(res.message || 'Không tải được dữ liệu')+'</div>');
            return;
        }
        try {
            renderDetail(res.data);
        } catch (err) {
            console.error('renderDetail error:', err, res.data);
            $('#dBody').html('<div style="padding:20px;color:#b91c1c;font-size:13px">Lỗi hiển thị chi tiết: '+APP.escape(err.message || String(err))+'</div>');
        }
    }).fail(function(xhr){
        $('#dBody').html('<div style="padding:30px;text-align:center;color:#b91c1c">Lỗi tải dữ liệu (HTTP '+(xhr.status||'?')+')</div>');
    });
}

function renderDetail(e) {
    if (!e || typeof e !== 'object') { throw new Error('Dữ liệu rỗng'); }
    var cls = statusClass(e.trang_thai);
    $('#dTitle').text(e.tieu_de || '(Không có tiêu đề)');
    $('#dSubtitle').html((e.ma_lop?APP.escape(e.ma_lop)+' · ':'')+APP.escape(e.ten_lop||''));

    var dow = safeDow(e.ngay_hoc);
    var ngayTxt = APP.formatDate(e.ngay_hoc) + (dow ? ' · ' + dow : '');
    var gbd = (e.gio_bat_dau||'').substring(0,5);
    var gkt = (e.gio_ket_thuc||'').substring(0,5);
    var monTxt = '-';
    if (e.ten_mon_hoc) monTxt = APP.escape((e.ma_mon_hoc ? e.ma_mon_hoc + ' - ' : '') + e.ten_mon_hoc);
    var gv = '-';
    if (e.ten_giang_vien) gv = APP.escape(e.ten_giang_vien);
    else if (e.giang_vien_ngoai) gv = APP.escape(e.giang_vien_ngoai) + ' <span class="hv-chip hv-chip-gray">Ngoài</span>';

    var body = '';
    body += '<div class="lh-detail-status lh-detail-status-'+cls+'">'+statusIcon(e.trang_thai)+' '+statusText(e.trang_thai)+'</div>';
    body += '<div class="lh-detail-grid">';
    body += dRow('Ngày', ngayTxt);
    body += dRow('Giờ', (gbd||'-')+' – '+(gkt||'-'));
    body += dRow('Phòng', e.phong_hoc ? APP.escape(e.phong_hoc) : '-');
    body += dRow('Buổi thứ', e.buoi_thu != null ? e.buoi_thu : '-');
    body += dRow('Môn học', monTxt);
    body += dRow('Giảng viên', gv);
    body += '</div>';

    if (e.noi_dung) body += '<div class="lh-detail-block"><div class="lh-detail-label">Nội dung</div><div>'+APP.escape(e.noi_dung).replace(/\n/g,'<br>')+'</div></div>';
    if (e.ghi_chu)  body += '<div class="lh-detail-block"><div class="lh-detail-label">Ghi chú</div><div>'+APP.escape(e.ghi_chu)+'</div></div>';

    if (CAN_EDIT) {
        body += '<div class="lh-detail-block"><div class="lh-detail-label">Đổi trạng thái nhanh</div><div class="lh-status-picker">';
        [0,1,2,3].forEach(function(tt){
            var active = parseInt(e.trang_thai,10)===tt ? ' is-active' : '';
            body += '<button type="button" class="lh-status-btn lh-status-'+statusClass(tt)+active+'" onclick="quickStatus('+e.id+','+tt+')">'+statusIcon(tt)+' '+statusText(tt)+'</button>';
        });
        body += '</div></div>';
    }

    body += '<div class="lh-detail-actions">';
    if (CAN_EDIT) body += '<button class="btn btn-primary" onclick="openEdit('+e.id+');closeDrawer();"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg> Sửa</button>';
    if (CAN_DEL) body += '<button class="btn btn-danger" onclick="trashItem('+e.id+', true)"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"/></svg> Xóa</button>';
    body += '</div>';

    $('#dBody').html(body);
}
function closeDrawer(){ $('#drawerDetail').removeClass('open').find('.drawer').removeClass('open'); }

function dRow(label, val) {
    return '<div class="lh-detail-row"><div class="lh-detail-label">'+label+'</div><div class="lh-detail-val">'+val+'</div></div>';
}

function quickStatus(id, tt) {
    APP.ajax(URL, {action:'updateTrangThai', id:id, trang_thai:tt}).done(function(res){
        if (res.success) { APP.toast(res.message,'success'); openDetail(id); loadEvents(); }
        else APP.toast(res.message,'error');
    });
}

function trashItem(id, fromDrawer) {
    APP.confirm('Xóa buổi học này?', function(){
        APP.ajax(URL, {action:'trash', id:id}).done(function(res){
            if (res.success) {
                APP.toast(res.message,'success');
                if (fromDrawer) closeDrawer();
                loadEvents();
            } else APP.toast(res.message,'error');
        });
    });
}

// ============== Init ==============
loadEvents();
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
