<?php
/**
 * dang_ky.php - Trang đăng ký khóa học công khai (không cần đăng nhập).
 * URL: <APP_URL>/GUI/public/dang_ky.php
 */
require_once __DIR__ . '/../../BUS/bootstrap.php';
require_once __DIR__ . '/../../BUS/DT_DangKyKhoaHoc_BUS.php';
require_once __DIR__ . '/../../BUS/DT_KhoaHoc_BUS.php';
require_once __DIR__ . '/../../PUBLIC/Common/CaptchaHelper.php';

SessionHelper::start();

$success = null;
$errorMsg = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $captcha = trim($_POST['captcha'] ?? '');
    $hp      = trim($_POST[CaptchaHelper::honeypotName()] ?? '');

    if (!CaptchaHelper::verify($captcha, $hp)) {
        $errorMsg = 'Mã xác thực không đúng hoặc đã hết hạn. Vui lòng thử lại.';
    } else {
        $ip = $_SERVER['REMOTE_ADDR'] ?? null;
        $res = DT_DangKyKhoaHoc_BUS::publicRegister(
            $_POST,
            $_FILES['cccd_file']    ?? null,
            $_FILES['bang_cap_file'] ?? null,
            $ip
        );
        if ($res['success']) {
            $success = $res['data'];
        } else {
            $errorMsg = $res['message'];
        }
    }
}

$khoaHocList = DT_KhoaHoc_BUS::getCombo();
$captchaQ = CaptchaHelper::generate();
$honeypotName = CaptchaHelper::honeypotName();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Đăng ký khóa học - <?= htmlspecialchars(AppConfig::APP_NAME) ?></title>
    <link rel="stylesheet" href="<?= AppConfig::baseUrl('assets/css/style.css') ?>">
    <style>
        body { background: linear-gradient(135deg, #eff6ff, #f0f9ff); min-height: 100vh; padding: 40px 16px; }
        .pub-wrap { max-width: 760px; margin: 0 auto; }
        .pub-card { background:#fff; border-radius:14px; box-shadow:0 4px 24px rgba(15,23,42,.08); padding: 28px; }
        .pub-head { text-align:center; margin-bottom: 24px; padding-bottom: 18px; border-bottom: 1px solid var(--gray-200); }
        .pub-head h1 { margin:0 0 6px; font-size: 22px; color: var(--gray-800); }
        .pub-head p { margin:0; color: var(--gray-500); font-size: 13.5px; }
        .pub-card .form-group { margin-bottom: 14px; }
        .pub-card label { font-weight: 600; font-size: 13px; }
        .pub-row { display:grid; grid-template-columns: 1fr 1fr; gap: 14px; }
        .pub-row-3 { display:grid; grid-template-columns: 1fr 1fr 1fr; gap: 14px; }
        .pub-section { background: var(--gray-50); border-left: 3px solid var(--primary); padding: 12px 14px; margin: 18px 0 12px; font-weight: 600; color: var(--gray-700); border-radius: 0 6px 6px 0; }
        .pub-captcha { background:#fef3c7; padding: 14px; border-radius: 8px; display:flex; align-items:center; gap: 14px; margin: 14px 0; }
        .pub-captcha .q { font-size: 18px; font-weight: 700; color: #92400e; font-family: 'Consolas', monospace; padding: 6px 14px; background:#fff; border-radius:6px; min-width: 100px; text-align:center; }
        .pub-captcha input { flex:1; height: 38px; }
        .pub-honeypot { position: absolute; left: -9999px; top: -9999px; }
        .pub-foot { text-align:center; margin-top: 16px; font-size: 12.5px; color: var(--gray-500); }
        .pub-foot a { color: var(--primary); }
        .alert { padding: 12px 14px; border-radius: 8px; margin-bottom: 14px; font-size: 14px; }
        .alert-error { background:#fee2e2; color:#991b1b; border:1px solid #fecaca; }
        .pub-success-box { background: #dcfce7; border: 2px solid #86efac; border-radius: 12px; padding: 24px; text-align:center; }
        .pub-success-box .ok-ic { width:64px;height:64px;border-radius:50%;background:#16a34a;color:#fff;display:inline-flex;align-items:center;justify-content:center;margin-bottom:12px; }
        .pub-success-box h2 { color: #166534; margin: 0 0 8px; }
        .pub-success-box .ma-tra-cuu { font-family:'Consolas',monospace; font-size: 22px; font-weight: 800; color:#1d4ed8; padding: 10px 18px; background:#fff; border-radius:8px; display:inline-block; letter-spacing: 1px; margin: 8px 0; user-select: all; }
        .btn-copy { padding: 4px 10px; font-size: 12px; }
        .file-hint { color: var(--gray-500); font-size: 11.5px; margin-top: 3px; }
    </style>
</head>
<body>
<div class="pub-wrap">
    <div class="pub-card">
        <div class="pub-head">
            <h1>Đăng ký khóa học</h1>
            <p><?= htmlspecialchars(AppConfig::APP_NAME) ?></p>
        </div>

        <?php if ($success): ?>
            <div class="pub-success-box">
                <div class="ok-ic">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                </div>
                <h2>Đăng ký thành công!</h2>
                <p>Vui lòng <strong>lưu lại mã tra cứu</strong> sau để theo dõi trạng thái xét duyệt và xem lịch học, điểm danh, kết quả sau này:</p>
                <div class="ma-tra-cuu" id="maTC"><?= htmlspecialchars($success['ma_tra_cuu']) ?></div>
                <div><button type="button" class="btn btn-sm btn-primary btn-copy" onclick="copyMa()">Sao chép mã</button></div>
                <p style="margin-top:14px;font-size:13px;color:#374151">Một email xác nhận đã được gửi đến: <strong><?= htmlspecialchars($success['email']) ?></strong></p>
                <p style="margin-top:8px;font-size:12.5px;color:#6b7280">(Nếu không thấy email, kiểm tra hộp Spam hoặc liên hệ quản trị viên)</p>
                <div style="margin-top:18px;display:flex;gap:10px;justify-content:center">
                    <a href="<?= AppConfig::baseUrl('GUI/public/tra_cuu.php?ma=' . urlencode($success['ma_tra_cuu'])) ?>" class="btn btn-primary">Tra cứu trạng thái</a>
                    <a href="<?= AppConfig::baseUrl('GUI/public/dang_ky.php') ?>" class="btn">Đăng ký khóa khác</a>
                </div>
            </div>
            <script>
                function copyMa(){
                    var t = document.getElementById('maTC').innerText;
                    if (navigator.clipboard) navigator.clipboard.writeText(t).then(function(){ alert('Đã sao chép: ' + t); });
                    else { var el=document.createElement('textarea');el.value=t;document.body.appendChild(el);el.select();document.execCommand('copy');document.body.removeChild(el); alert('Đã sao chép: ' + t); }
                }
            </script>
        <?php else: ?>

            <?php if ($errorMsg): ?>
                <div class="alert alert-error"><?= htmlspecialchars($errorMsg) ?></div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data" autocomplete="off">
                <!-- Honeypot (ẩn — bot sẽ điền) -->
                <div class="pub-honeypot">
                    <label>Website</label>
                    <input type="text" name="<?= htmlspecialchars($honeypotName) ?>" tabindex="-1" autocomplete="off">
                </div>

                <div class="pub-section">1. Thông tin cá nhân</div>

                <div class="form-group">
                    <label>Họ và tên <span class="required">*</span></label>
                    <input type="text" name="ho_ten" class="form-control" required maxlength="150"
                           value="<?= htmlspecialchars($_POST['ho_ten'] ?? '') ?>">
                </div>

                <div class="pub-row-3">
                    <div class="form-group">
                        <label>Ngày sinh</label>
                        <input type="date" name="ngay_sinh" class="form-control"
                               value="<?= htmlspecialchars($_POST['ngay_sinh'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label>Giới tính</label>
                        <select name="gioi_tinh" class="form-select">
                            <option value="">--</option>
                            <option value="Nam"  <?= ($_POST['gioi_tinh'] ?? '')==='Nam'?'selected':'' ?>>Nam</option>
                            <option value="Nữ"   <?= ($_POST['gioi_tinh'] ?? '')==='Nữ'?'selected':'' ?>>Nữ</option>
                            <option value="Khác" <?= ($_POST['gioi_tinh'] ?? '')==='Khác'?'selected':'' ?>>Khác</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>CCCD <span class="required">*</span></label>
                        <input type="text" name="cccd" class="form-control" required pattern="\d{9,12}" maxlength="12"
                               placeholder="9-12 chữ số" value="<?= htmlspecialchars($_POST['cccd'] ?? '') ?>">
                    </div>
                </div>

                <div class="pub-row">
                    <div class="form-group">
                        <label>Số điện thoại</label>
                        <input type="text" name="dien_thoai" class="form-control" maxlength="20"
                               value="<?= htmlspecialchars($_POST['dien_thoai'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label>Email <span class="required">*</span></label>
                        <input type="email" name="email" class="form-control" required maxlength="150"
                               value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label>Địa chỉ</label>
                    <input type="text" name="dia_chi" class="form-control" maxlength="300"
                           value="<?= htmlspecialchars($_POST['dia_chi'] ?? '') ?>">
                </div>

                <div class="pub-row">
                    <div class="form-group">
                        <label>Đơn vị công tác</label>
                        <input type="text" name="don_vi_cong_tac" class="form-control" maxlength="200"
                               value="<?= htmlspecialchars($_POST['don_vi_cong_tac'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label>Chức vụ</label>
                        <input type="text" name="chuc_vu" class="form-control" maxlength="150"
                               value="<?= htmlspecialchars($_POST['chuc_vu'] ?? '') ?>">
                    </div>
                </div>

                <div class="pub-section">2. Khóa học đăng ký</div>

                <div class="form-group">
                    <label>Khóa học <span class="required">*</span></label>
                    <select name="khoa_hoc_id" class="form-select" required>
                        <option value="">-- Chọn khóa học --</option>
                        <?php foreach ($khoaHocList as $kh): ?>
                            <option value="<?= $kh['id'] ?>" <?= (int)($_POST['khoa_hoc_id'] ?? 0)===(int)$kh['id']?'selected':'' ?>>
                                <?= htmlspecialchars($kh['ma_khoa_hoc'] . ' - ' . $kh['ten_khoa_hoc']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Lý do / mong muốn khi đăng ký</label>
                    <textarea name="ly_do_dang_ky" class="form-control" rows="3" maxlength="1000"><?= htmlspecialchars($_POST['ly_do_dang_ky'] ?? '') ?></textarea>
                </div>

                <div class="pub-section">3. File đính kèm (không bắt buộc)</div>

                <div class="pub-row">
                    <div class="form-group">
                        <label>Ảnh / scan CCCD</label>
                        <input type="file" name="cccd_file" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                        <div class="file-hint">PDF/JPG/PNG · Tối đa 5MB</div>
                    </div>
                    <div class="form-group">
                        <label>Bằng cấp / chứng chỉ liên quan</label>
                        <input type="file" name="bang_cap_file" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                        <div class="file-hint">PDF/JPG/PNG · Tối đa 5MB</div>
                    </div>
                </div>

                <div class="pub-section">4. Xác thực</div>

                <div class="pub-captcha">
                    <div class="q"><?= htmlspecialchars($captchaQ) ?></div>
                    <input type="number" name="captcha" class="form-control" required placeholder="Nhập kết quả phép tính" autocomplete="off">
                </div>

                <div style="display:flex;justify-content:space-between;align-items:center;margin-top:18px;gap:10px;flex-wrap:wrap">
                    <a href="<?= AppConfig::baseUrl('GUI/public/tra_cuu.php') ?>" class="btn">Đã có mã? Tra cứu →</a>
                    <button type="submit" class="btn btn-primary" style="padding: 10px 28px; font-size: 14px;">Gửi đăng ký</button>
                </div>
            </form>
        <?php endif; ?>
    </div>

    <div class="pub-foot">
        © <?= date('Y') ?> <?= htmlspecialchars(AppConfig::APP_NAME) ?>
    </div>
</div>
</body>
</html>
