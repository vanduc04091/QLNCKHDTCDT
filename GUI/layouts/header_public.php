<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? Helper::h($pageTitle) : 'Trang chủ' ?> · <?= Helper::h(AppConfig::APP_NAME) ?></title>
    <link rel="stylesheet" href="<?= AppConfig::baseUrl('assets/css/style.css') ?>">
    <link rel="stylesheet" href="<?= AppConfig::baseUrl('assets/css/home.css') ?>">
    <link rel="icon" type="image/png" href="<?= AppConfig::baseUrl('assets/images/logo_bv.png') ?>">
</head>
<body>
    <header class="public-header">
        <nav class="navbar">
            <div class="navbar-brand">
                <div class="logo">QL</div>
                <div>
                    <div class="brand-name">QL NCKH-ĐT-CĐT</div>
                    <div class="brand-tag">v<?= AppConfig::APP_VERSION ?></div>
                </div>
            </div>
            <div class="navbar-menu">
                <?php if (SessionHelper::isLoggedIn()): ?>
                    <a href="<?= AppConfig::baseUrl('GUI/dashboard/index.php') ?>" class="nav-link">
                        <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2 2z"></path>
                            <path d="M8 5a2 2 0 012-2h4a2 2 0 012 2v2H8V5z"></path>
                        </svg>
                        Dashboard
                    </a>
                    <a href="<?= AppConfig::baseUrl('GUI/auth/logout.php') ?>" class="nav-link logout">
                        <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                        Đăng xuất
                    </a>
                <?php else: ?>
                    <a href="#features" class="nav-link">Tính năng</a>
                    <a href="<?= AppConfig::baseUrl('GUI/auth/login.php') ?>" class="nav-link login">
                        <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                        </svg>
                        Đăng nhập
                    </a>
                <?php endif; ?>
            </div>
        </nav>
    </header>

    <main>