<?php
/**
 * header.php - Layout chung phần trên + sidebar
 * Các trang khi require layout: đặt $pageTitle, $activeMenu trước khi require.
 */
if (!isset($pageTitle)) $pageTitle = AppConfig::APP_NAME;
if (!isset($activeMenu)) $activeMenu = '';
require_once __DIR__ . '/../../PUBLIC/Common/IconHelper.php';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= Helper::h($pageTitle) ?> · <?= Helper::h(AppConfig::APP_NAME) ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap">
<link rel="stylesheet" href="<?= AppConfig::baseUrl('assets/css/style.css') ?>">
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
var APP_BASE = "<?= AppConfig::baseUrl('') ?>";
var CSRF_TOKEN = "<?= Helper::h(SessionHelper::csrfToken()) ?>";
</script>
<script src="<?= AppConfig::baseUrl('assets/js/app.js') ?>"></script>
<script>
// Apply sidebar collapsed state SOM (truoc khi body render) de tranh nhay layout
(function () {
    try {
        if (localStorage.getItem('sidebarCollapsed') === '1') {
            document.documentElement.classList.add('sidebar-collapsed-init');
        }
    } catch (_) {}
})();
</script>
<style>
/* Apply ngay tu HTML element de tranh FOUC */
html.sidebar-collapsed-init .sidebar { transform: translateX(-100%); }
html.sidebar-collapsed-init .main { margin-left: 0; }
</style>
</head>
<body>
<div class="app-wrapper">
    <?php require __DIR__ . '/sidebar.php'; ?>

    <div class="main">
        <header class="topbar">
            <div class="d-flex" style="align-items:center;gap:14px">
                <button type="button" class="sidebar-toggle" id="sidebarToggle" aria-label="Ẩn/hiện menu" title="Ẩn/hiện menu">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <line x1="3" y1="6" x2="21" y2="6"></line>
                        <line x1="3" y1="12" x2="21" y2="12"></line>
                        <line x1="3" y1="18" x2="21" y2="18"></line>
                    </svg>
                </button>
                <div class="page-title"><?= Helper::h($pageTitle) ?></div>
            </div>
            <div class="user-menu">
                <div class="dropdown">
                    <div class="user-info d-flex" style="align-items:center;gap:10px;cursor:pointer;padding:6px 10px;border-radius:8px">
                        <div class="avatar"><?= Helper::h(mb_substr(SessionHelper::hoTen() ?: SessionHelper::taiKhoan(), 0, 1)) ?></div>
                        <div style="line-height:1.2">
                            <div style="font-weight:600;font-size:13px"><?= Helper::h(SessionHelper::hoTen() ?: SessionHelper::taiKhoan()) ?></div>
                            <div style="font-size:11.5px;color:var(--gray-500)"><?= Helper::h(SessionHelper::get('ten_nhom', '')) ?></div>
                        </div>
                    </div>
                    <div class="dropdown-menu">
                        <a href="<?= AppConfig::baseUrl('GUI/auth/change_password.php') ?>">
                            <?= IconHelper::svg('key', 18, 'icon', 'currentColor') ?> Đổi mật khẩu
                        </a>
                        <a href="<?= AppConfig::baseUrl('GUI/auth/logout.php') ?>" style="color:var(--danger)">
                            <?= IconHelper::svg('logout', 18, 'icon', 'currentColor') ?> Đăng xuất
                        </a>
                    </div>
                </div>
            </div>
        </header>

        <main class="content">
