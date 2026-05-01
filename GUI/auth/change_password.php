<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../BUS/DM_NguoiDung_BUS.php';
Helper::requireLogin();

$msg = ''; $msgType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrf = Helper::post('_csrf', '');
    if (!SessionHelper::verifyCsrf($csrf)) {
        $msg = 'Phiên làm việc đã hết hạn'; $msgType = 'danger';
    } else {
        $old = (string)Helper::post('mat_khau_cu', '');
        $new = (string)Helper::post('mat_khau_moi', '');
        $confirm = (string)Helper::post('mat_khau_xac_nhan', '');
        $res = DM_NguoiDung_BUS::changePassword(SessionHelper::userId(), $old, $new, $confirm);
        $msg = $res['message'];
        $msgType = $res['success'] ? 'success' : 'danger';
    }
}

$pageTitle = 'Đổi mật khẩu';
$activeMenu = 'change_password';
require __DIR__ . '/../layouts/header.php';
?>

<div class="breadcrumb">
    <a href="<?= AppConfig::baseUrl('GUI/dashboard/index.php') ?>">Trang chủ</a>
    <span class="sep">›</span>
    <span>Đổi mật khẩu</span>
</div>

<div class="card" style="max-width:520px">
    <div class="card-header"><h2>Đổi mật khẩu</h2></div>
    <div class="card-body">
        <?php if ($msg): ?>
            <div class="alert alert-<?= $msgType ?>" style="padding:10px;border-radius:8px;margin-bottom:14px;
                 background:<?= $msgType==='success'?'#dcfce7':'#fee2e2' ?>;color:<?= $msgType==='success'?'#166534':'#991b1b' ?>">
                <?= Helper::h($msg) ?>
            </div>
        <?php endif; ?>

        <form method="post" autocomplete="off">
            <input type="hidden" name="_csrf" value="<?= Helper::h(SessionHelper::csrfToken()) ?>">
            <div class="form-group">
                <label>Mật khẩu hiện tại <span class="required">*</span></label>
                <input type="password" name="mat_khau_cu" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Mật khẩu mới <span class="required">*</span></label>
                <input type="password" name="mat_khau_moi" class="form-control" required minlength="6">
            </div>
            <div class="form-group">
                <label>Xác nhận mật khẩu mới <span class="required">*</span></label>
                <input type="password" name="mat_khau_xac_nhan" class="form-control" required minlength="6">
            </div>
            <div style="display:flex;gap:8px;justify-content:flex-end">
                <a href="<?= AppConfig::baseUrl('GUI/dashboard/index.php') ?>" class="btn">Hủy</a>
                <button type="submit" class="btn btn-primary">Đổi mật khẩu</button>
            </div>
        </form>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
