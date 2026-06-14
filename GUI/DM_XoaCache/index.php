<?php
require_once __DIR__ . '/../../bootstrap.php';

Helper::requireLogin();
if (!PhanQuyenHelper::hasQuyen('DM_XoaCache', PhanQuyenHelper::QUYEN_XEM)) {
    echo 'Bạn không có quyền truy cập chức năng này.'; exit;
}
$canClear = PhanQuyenHelper::hasQuyen('DM_XoaCache', PhanQuyenHelper::QUYEN_XOA);

$pageTitle  = 'Xóa cache hệ thống';
$activeMenu = 'DM_XoaCache';
require __DIR__ . '/../layouts/header.php';
?>
<div class="breadcrumb">
    <a href="<?= AppConfig::baseUrl('GUI/dashboard/index.php') ?>">Trang chủ</a>
    <span class="sep">›</span> Hệ thống
    <span class="sep">›</span> <span>Xóa cache</span>
</div>

<div class="card" style="max-width:760px">
    <div style="padding:18px 20px">
        <h3 style="margin:0 0 6px">Xóa bộ nhớ đệm (cache)</h3>
        <p class="text-muted" style="margin:0 0 18px;font-size:13.5px">
            Dùng khi vừa thay đổi phân quyền, danh mục mà giao diện chưa cập nhật. Xóa cache buộc hệ thống đọc lại dữ liệu mới nhất từ cơ sở dữ liệu.
        </p>

        <?php if (!$canClear): ?>
            <div class="text-muted" style="padding:14px;border:1px dashed var(--gray-300);border-radius:8px">
                Bạn chỉ có quyền xem. Cần quyền <strong>Xóa</strong> trên chức năng này để thực hiện.
            </div>
        <?php else: ?>
        <div class="xc-list">
            <div class="xc-row">
                <div class="xc-info">
                    <div class="xc-title">Cache phân quyền</div>
                    <div class="xc-desc">Xóa ma trận quyền của tất cả nhóm. Dùng sau khi sửa phân quyền / gán form.</div>
                </div>
                <button type="button" class="btn btn-primary" onclick="clearCache('clearPhanQuyen', this)">Xóa</button>
            </div>
            <div class="xc-row">
                <div class="xc-info">
                    <div class="xc-title">Cache danh mục / combo</div>
                    <div class="xc-desc">Xóa cache các danh sách lựa chọn (bài học, chương trình, đối tượng, khoa/phòng, nhân viên, khóa học).</div>
                </div>
                <button type="button" class="btn btn-primary" onclick="clearCache('clearCombo', this)">Xóa</button>
            </div>
            <div class="xc-row">
                <div class="xc-info">
                    <div class="xc-title">Toàn bộ cache</div>
                    <div class="xc-desc">Xóa <strong>tất cả</strong> dữ liệu trong bộ nhớ đệm. An toàn nhưng trang sẽ tải chậm hơn ở lần truy cập kế tiếp.</div>
                </div>
                <button type="button" class="btn btn-danger" onclick="clearCache('clearAll', this)">Xóa tất cả</button>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<style>
.xc-list { display:flex; flex-direction:column; gap:10px; }
.xc-row { display:flex; align-items:center; justify-content:space-between; gap:16px;
          padding:14px 16px; border:1px solid var(--gray-200); border-radius:10px; }
.xc-title { font-weight:600; font-size:14px; }
.xc-desc { color:var(--gray-500); font-size:12.5px; margin-top:2px; }
.xc-row .btn { white-space:nowrap; }
</style>

<script>
var URL = APP_BASE + 'GUI/DM_XoaCache/ajax_handler.php';
function clearCache(action, btn){
    var msg = action === 'clearAll' ? 'Xóa TOÀN BỘ cache hệ thống?' : 'Xóa cache này?';
    APP.confirm(msg, function(){
        var $b = $(btn).prop('disabled', true);
        APP.ajax(URL, {action: action}).done(function(res){
            $b.prop('disabled', false);
            res.success ? APP.toast(res.message, 'success') : APP.toast(res.message, 'error');
        }).fail(function(){ $b.prop('disabled', false); });
    });
}
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
