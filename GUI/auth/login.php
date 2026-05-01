<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../BUS/DM_NguoiDung_BUS.php';

// Nếu đã đăng nhập → chuyển đến dashboard
if (SessionHelper::isLoggedIn()) {
    header('Location: ' . AppConfig::baseUrl('GUI/dashboard/index.php'));
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrf = Helper::post('_csrf', '');
    if (!SessionHelper::verifyCsrf($csrf)) {
        $error = 'Phiên làm việc đã hết hạn. Vui lòng thử lại.';
    } else {
        $taiKhoan = Helper::postStr('tai_khoan');
        $matKhau = (string)Helper::post('mat_khau', '');
        $result = DM_NguoiDung_BUS::login($taiKhoan, $matKhau);
        if ($result['success']) {
            SessionHelper::login($result['data']);
            header('Location: ' . AppConfig::baseUrl('GUI/dashboard/index.php'));
            exit;
        }
        $error = $result['message'];
    }
}

$csrfToken = SessionHelper::csrfToken();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Đăng nhập · <?= Helper::h(AppConfig::APP_NAME) ?></title>
<link rel="stylesheet" href="<?= AppConfig::baseUrl('assets/css/login.css') ?>">
</head>
<body>
<div class="login-wrap">
    <div class="login-header">
        <div class="logo">
            <img src="<?= AppConfig::baseUrl('assets/images/logo_bv.png') ?>" alt="BV HNĐK Nghệ An">
        </div>
        <h1>Đăng nhập hệ thống</h1>
        <p>Quản lý NCKH - Đào tạo - Chỉ đạo tuyến</p>
    </div>
    <div class="login-body">
        <?php if ($error): ?>
            <div class="alert alert-danger"><?= Helper::h($error) ?></div>
        <?php endif; ?>

        <form method="post" autocomplete="off">
            <input type="hidden" name="_csrf" value="<?= Helper::h($csrfToken) ?>">

            <div class="form-group">
                <label for="tai_khoan">Tài khoản</label>
                <input type="text" id="tai_khoan" name="tai_khoan" class="form-control"
                       required autofocus placeholder="Nhập tài khoản"
                       value="<?= Helper::h(Helper::post('tai_khoan', '')) ?>">
            </div>

            <div class="form-group">
                <label for="mat_khau">Mật khẩu</label>
                <div class="input-group">
                    <input type="password" id="mat_khau" name="mat_khau" class="form-control"
                           required placeholder="Nhập mật khẩu">
                    <button type="button" class="toggle-pass" onclick="togglePass()">Hiện</button>
                </div>
            </div>

            <button type="submit" class="btn-login">Đăng nhập</button>
        </form>
    </div>
    <div class="login-footer">
        © <?= date('Y') ?> · <?= Helper::h(AppConfig::APP_NAME) ?> · v<?= AppConfig::APP_VERSION ?>
    </div>
</div>

<script>
function togglePass() {
    var el = document.getElementById('mat_khau');
    var btn = document.querySelector('.toggle-pass');
    if (el.type === 'password') { el.type = 'text'; btn.textContent = 'Ẩn'; }
    else { el.type = 'password'; btn.textContent = 'Hiện'; }
}
</script>
</body>
</html>
