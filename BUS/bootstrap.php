<?php
/**
 * Bootstrap chung cho BUS & GUI: load config + các helper cơ bản.
 */
require_once __DIR__ . '/../PUBLIC/Common/AppConfig.php';
require_once __DIR__ . '/../PUBLIC/Common/Constants.php';
require_once __DIR__ . '/../PUBLIC/Common/Helper.php';
require_once __DIR__ . '/../PUBLIC/Common/SessionHelper.php';
require_once __DIR__ . '/../PUBLIC/Common/MemcachedHelper.php';
require_once __DIR__ . '/../PUBLIC/Common/PaginationHelper.php';
require_once __DIR__ . '/../PUBLIC/Common/ResponseHelper.php';
require_once __DIR__ . '/../DAL/database.php';
require_once __DIR__ . '/../PUBLIC/Common/PhanQuyenHelper.php';
require_once __DIR__ . '/../DAL/DM_NhatKyHeThong_DAL.php';

date_default_timezone_set(AppConfig::APP_TIMEZONE);

if (AppConfig::APP_DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
}

SessionHelper::start();
