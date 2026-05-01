# SECURITY.md — Pre-deployment Security Review

> Ngày review: 2026-05-01
> Đối tượng: hệ thống QLNCDTCDT (PHP 7.4+/MariaDB) trước khi đưa lên domain thật.

## Đánh giá tổng quan

| Hạng mục | Trạng thái |
|---|---|
| Cấu hình app | ⚠ Cần sửa trước deploy |
| Auth (login/session) | ⚠ Có nhược điểm cần fix |
| CSRF | ❌ **CHƯA CÓ** (chỉ login + change_password có) |
| SQL Injection | ✅ An toàn (PDO + placeholder) |
| XSS | ✅ Hầu hết OK, 1 chỗ nhỏ |
| File upload | ⚠ 4/7 thư mục thiếu `.htaccess` |
| Permission (RBAC) | ✅ Đầy đủ |
| Logging / Audit | ✅ Có `dm_nhat_ky_he_thong` |
| Server hardening | ⚠ Cần check khi deploy |

---

## 🔴 CRITICAL — Phải fix TRƯỚC khi deploy

### C1. CSRF không bật cho ajax_handler (33 file bị ảnh hưởng)

**Vấn đề**: Tất cả 33 file `GUI/<module>/ajax_handler.php` đều **không gọi** `SessionHelper::verifyCsrf()`. Chỉ `login.php` và `change_password.php` có CSRF.

Hệ quả: kẻ tấn công có thể tạo trang web độc hại, dụ admin đã đăng nhập click vào → trình duyệt tự gửi cookie session → server thực hiện thao tác (xoá đề tài, đổi quyền…) thay mặt nạn nhân.

**Cách fix (chỉ làm 1 lần ở 1 helper)**:

Thêm vào `Helper::requireAjaxLogin()` hoặc tạo `Helper::requireAjaxCsrf()`:

```php
public static function requireAjaxCsrf(): void
{
    self::requireAjaxLogin();
    // Đọc CSRF từ header X-CSRF-Token hoặc POST
    $token = $_SERVER['HTTP_X_CSRF_TOKEN']
          ?? ($_POST[AppConfig::CSRF_TOKEN_NAME] ?? '');
    if (!SessionHelper::verifyCsrf($token)) {
        ResponseHelper::error('Phiên làm việc đã hết hạn. Vui lòng tải lại trang.', 419);
    }
}
```

Trong `app.js`, sửa `APP.ajax()` để **tự động gắn CSRF** vào mọi request:
```js
APP.ajax = function(url, data) {
    return $.ajax({
        url: url,
        method: 'POST',
        dataType: 'json',
        data: data,
        headers: { 'X-CSRF-Token': window.CSRF_TOKEN }
    });
};
```

`CSRF_TOKEN` đã có sẵn trong `header.php` (line 20). Sau đó trong từng ajax_handler, đổi `Helper::requireAjaxLogin()` → `Helper::requireAjaxCsrf()`.

### C2. Credentials hardcode + DEBUG=true

```php
// PUBLIC/Common/AppConfig.php
const DB_USER = 'root';
const DB_PASS = '';            // ⚠ Mật khẩu rỗng
const APP_DEBUG = true;        // ⚠ Lộ stack trace ra production
const APP_URL = 'http://qldt.bv';   // ⚠ Vẫn dev URL
```

**Khi deploy**:
1. Tạo user MySQL riêng với chỉ quyền `SELECT, INSERT, UPDATE, DELETE` trên DB này (không GRANT ALL).
2. Đổi `DB_PASS` thành mật khẩu mạnh.
3. Set `APP_DEBUG = false` (sẽ không hiển thị PDO error message — đã có code nhánh false trong `database.php`).
4. Đổi `APP_URL` sang HTTPS domain thật.
5. **Không commit** `AppConfig.php` lên git public — hoặc tách secrets ra file `.env`/`config.local.php`.

### C3. Thư mục upload thiếu `.htaccess` block PHP (3 thư mục)

Đã có `.htaccess`: `baikiemtra`, `dangky`, `nckh`, `tailieu`.

**Thiếu**: `chungchi`, `hocvien`, `hoso_hocvien`.

Hậu quả: nếu kẻ tấn công upload được file `.php` (vd: bypass extension check), file sẽ được Apache thực thi → RCE.

**Fix**: tạo `.htaccess` ở 3 thư mục thiếu, nội dung:
```apache
<FilesMatch "\.(php|phtml|php3|php4|php5|php7|phps|pht|phar)$">
    Require all denied
</FilesMatch>
RemoveHandler .php .phtml .phar
RemoveType .php .phtml .phar
php_flag engine off
```

**Tốt hơn**: thay vì rải `.htaccess` ở từng folder, đặt 1 file `.htaccess` ở `assets/uploads/` chặn toàn bộ extension nguy hiểm cho cả cây con.

### C4. Không có rate limit / chống brute-force login

`GUI/auth/login.php` không giới hạn số lần thử sai. Kẻ tấn công có thể bruteforce password.

**Fix tối thiểu**:
1. Thêm bảng `dm_login_attempt(ip, tai_khoan, thoi_gian)` hoặc lưu vào `dm_nhat_ky_he_thong` với `module='login_fail'`.
2. Đếm số lần thất bại 15 phút gần nhất theo `(ip, tai_khoan)`. Nếu > 5 → từ chối + delay 1-2s.
3. Có CaptchaHelper sẵn rồi (`PUBLIC/Common/CaptchaHelper.php`) — bật captcha sau 3 lần fail.

### C5. `Helper::getClientIp()` tin tưởng `HTTP_X_FORWARDED_FOR`

```php
foreach (['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR'] as $key) {
```

Nếu app **không** đứng sau reverse proxy, `HTTP_X_FORWARDED_FOR` có thể bị client giả mạo → spoof IP trong audit log.

**Fix**:
- Nếu có CDN/proxy (Cloudflare, nginx): chỉ chấp nhận `X-Forwarded-For` từ list IP proxy tin cậy.
- Nếu **không** có proxy: chỉ dùng `REMOTE_ADDR`. Bỏ 2 header kia.

---

## 🟠 HIGH — Nên fix sớm

### H1. Cookie session chưa bật `secure` (HTTPS-only)

`SessionHelper::start()` không set `'secure' => true`. Khi triển khai HTTPS, cookie session vẫn có thể truyền qua HTTP → MITM cookie hijack.

**Fix**:
```php
session_set_cookie_params([
    'lifetime' => AppConfig::SESSION_LIFETIME,
    'path' => '/',
    'httponly' => true,
    'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
    'samesite' => 'Lax',
]);
```

### H2. Hardcoded admin id = 1

`PhanQuyenHelper::hasQuyen()` line 48: `if ($nhomId === 1) return true;`. Nếu thứ tự seed nhóm thay đổi (ví dụ khôi phục từ DB khác), id=1 có thể là nhóm khác → admin thật không có quyền, hoặc nhóm khác bỗng có full quyền.

**Fix**: thêm cột `dm_nhom_tai_khoan.la_admin TINYINT DEFAULT 0`, set =1 cho nhóm Admin. Đổi check thành `if ($nhom['la_admin'] === 1) return true;`.

### H3. Mật khẩu BCRYPT cost = 10 (hơi thấp ở 2026)

`PASSWORD_COST = 10` (≈ 100ms hash). Khuyến nghị 2024+: **cost 12** (≈ 250-400ms — vẫn chấp nhận được cho login, tăng đáng kể chi phí brute).

```php
const PASSWORD_COST = 12;
```

Lưu ý: hash hiện tại vẫn verify được — chỉ ảnh hưởng password mới tạo.

### H4. Logout không xoá cookie (chỉ destroy session server)

Cần kiểm tra `GUI/auth/logout.php` có gọi `SessionHelper::destroy()` (đã set xoá cookie). Đã có code trong helper, OK.

### H5. Một số file PHP chỉnh sửa quá lớn

| File | Dòng |
|---|---|
| `GUI/DM_HocVien/index.php` | 1002 |
| `GUI/NCKH_DeTai/index.php` | 962 |
| `GUI/DT_LichHoc/index.php` | 926 |

Khó bảo trì, dễ lỗi khi có 2 dev sửa cùng. Nên tách JS ra file riêng `assets/js/<module>.js` (hoặc inline `<template>`).

### H6. Không transaction cho thao tác multi-table

Các action như "duyệt đăng ký khoá học" cần ghi vào nhiều bảng (`dt_dang_ky_khoa_hoc`, `dm_hoc_vien`, `dt_hoc_vien_lop`) nhưng không bọc trong transaction. Nếu lỗi giữa chừng → state inconsistent.

**Fix**: bọc bằng `Database::beginTransaction() / commit() / rollBack()` ở BUS layer.

---

## 🟡 MEDIUM — Hardening khuyến nghị

### M1. SQL Injection ở LIMIT/OFFSET

DAL hiện nội suy trực tiếp:
```php
$sql .= " ORDER BY id DESC LIMIT {$pageSize} OFFSET {$offset}";
```

Mặc dù `PaginationHelper::normalize()` ép int nên **không thực sự exploit được**, đây vẫn là bad practice. Nên đổi sang:
```php
$sql .= " ORDER BY id DESC LIMIT :lim OFFSET :off";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':lim', (int)$pageSize, PDO::PARAM_INT);
$stmt->bindValue(':off', (int)$offset, PDO::PARAM_INT);
```

### M2. XSS nhỏ ở `change_password.php`

Line 37-38: `<?= $msgType ?>` không qua `Helper::h()`. Hiện tại `$msgType` được set từ chuỗi literal nên an toàn, nhưng dễ vô tình unsafe trong tương lai.

### M3. File upload check chưa kiểm MIME thật

Hiện chỉ whitelist extension (`pdf, doc, jpg…`) qua `pathinfo()`. Kẻ tấn công có thể đổi tên `shell.php` → `shell.pdf` rồi serve qua URL — nhưng `.htaccess` đã chặn PHP execution nên thực ra OK.

**Hardening thêm**: dùng `finfo_file()` để check MIME thật:
```php
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime = finfo_file($finfo, $tmp);
if (!in_array($mime, ['application/pdf','image/jpeg','image/png',...])) reject();
```

### M4. Headers bảo mật chưa set

Trang HTML (`header.php`) thiếu các security header. Khuyến nghị thêm vào `bootstrap.php` (hoặc Apache `.htaccess` root):
```php
header('X-Frame-Options: SAMEORIGIN');           // chống clickjacking
header('X-Content-Type-Options: nosniff');       // chống MIME sniff
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Permissions-Policy: geolocation=(), microphone=(), camera=()');
// Khi đã ổn định, bật CSP:
// header("Content-Security-Policy: default-src 'self'; script-src 'self' https://code.jquery.com 'unsafe-inline'; style-src 'self' 'unsafe-inline'");
```

### M5. Cron mail không có lock

`cron_nckh_nhac_viec.php` chạy không có cờ chống chạy chồng — nếu cron trùng (interval ngắn, mail SMTP chậm), có thể gửi 2 lần. Thêm file lock:
```php
$lock = fopen(__DIR__ . '/cron.lock', 'c');
if (!flock($lock, LOCK_EX | LOCK_NB)) { exit('locked'); }
// ... main logic
flock($lock, LOCK_UN);
```

### M6. Không có giới hạn kích thước form / số lượng item

Có thể bị DoS bằng cách POST mảng `thanh_vien[]` 100,000 phần tử. Set `php.ini`:
```ini
post_max_size = 50M
upload_max_filesize = 25M
max_input_vars = 5000
```

---

## 🟢 LOW — Nice to have

- L1. **HTTPS bắt buộc** ở production (Let's Encrypt). Force redirect HTTP → HTTPS qua `.htaccess`.
- L2. **`.htaccess` chặn truy cập trực tiếp các thư mục code**: `BUS/`, `DAL/`, `PUBLIC/`, `docs/` — chỉ public `assets/`, `GUI/`, `index.php`.
- L3. **Tách secrets** sang `.env` (parse bằng `parse_ini_file`) — cho phép commit code lên git.
- L4. **Backup tự động**: cron `mysqldump` hàng ngày, retain 7 ngày.
- L5. **Disable directory listing** trong Apache (`Options -Indexes`).
- L6. **Log error PHP** vào file riêng (không hiển thị) khi `APP_DEBUG=false`.

---

## ✅ Đã làm đúng

- PDO + named placeholder + `EMULATE_PREPARES=false` (chống SQL injection cơ bản).
- Bcrypt hash mật khẩu (`password_hash` + `password_verify`).
- Soft delete + audit log (`dm_nhat_ky_he_thong`).
- Permission RBAC qua `dm_phan_quyen` ở mọi action ghi.
- Output dùng `Helper::h()` (htmlspecialchars) gần như mọi nơi.
- Login dùng `session_regenerate_id(true)` để chống session fixation.
- File upload sinh tên random + lưu cạnh `.htaccess` block PHP (4/7 thư mục).
- CORS không bật bừa bãi (chỉ same-origin).
- Cookie session có `httponly` + `samesite=Lax`.

---

## Checklist deploy

Trước khi đưa lên domain thật, theo thứ tự:

- [ ] **C1**: Bật CSRF check ở mọi ajax_handler (qua `Helper::requireAjaxCsrf()`).
- [ ] **C2**: Tạo DB user riêng, đổi `DB_PASS`, set `APP_DEBUG=false`, đổi `APP_URL`.
- [ ] **C3**: Thêm `.htaccess` cho `chungchi`, `hocvien`, `hoso_hocvien`, hoặc 1 file root cho `assets/uploads/`.
- [ ] **C4**: Bật rate limit + captcha cho login.
- [ ] **C5**: Sửa `Helper::getClientIp()` chỉ trust `REMOTE_ADDR` nếu không có proxy.
- [ ] **H1**: Set cookie session `secure: true` (sau khi có HTTPS).
- [ ] **H2**: Đổi check admin từ `id===1` → cờ `la_admin`.
- [ ] Cài SSL (Let's Encrypt), force HTTPS.
- [ ] `.htaccess` root chặn truy cập `BUS/`, `DAL/`, `docs/`.
- [ ] Set headers bảo mật (X-Frame-Options, X-Content-Type-Options...).
- [ ] Bật error log riêng, không display.
- [ ] Cron `mysqldump` backup hàng ngày.
- [ ] Disable Apache directory listing.

Nếu thiếu thời gian: **làm tối thiểu C1-C5 + HTTPS + backup**. Phần H/M có thể làm dần sau khi đã live.
