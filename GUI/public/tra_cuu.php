<?php
/**
 * tra_cuu.php - Tra cứu thông tin học viên bằng CCCD.
 * URL: <APP_URL>/GUI/public/tra_cuu.php
 * Yêu cầu: CCCD + math captcha (chống scrape).
 *
 * Tra ra mọi học viên có CCCD trùng (kể cả HV được nhập trực tiếp, không qua flow đăng ký).
 * Nếu nhiều mảnh dữ liệu (đăng ký + HV chính thức) → cho phép chọn.
 */
require_once __DIR__ . '/../../BUS/bootstrap.php';
require_once __DIR__ . '/../../BUS/DT_DangKyKhoaHoc_BUS.php';
require_once __DIR__ . '/../../BUS/DM_HocVien_BUS.php';
require_once __DIR__ . '/../../BUS/DT_HocVienLop_BUS.php';
require_once __DIR__ . '/../../BUS/DT_HoSoHocVien_BUS.php';
require_once __DIR__ . '/../../BUS/DT_ChungChi_BUS.php';
require_once __DIR__ . '/../../PUBLIC/Common/CaptchaHelper.php';

SessionHelper::start();

$cccd     = trim($_REQUEST['cccd'] ?? '');
$selectHv = (int)($_REQUEST['hv'] ?? 0);
$selectDk = (int)($_REQUEST['dk'] ?? 0);
$verified = false;
$errorMsg = null;
$candidates = [];     // [{type:'hv'|'dk', id, label, status}]
$payload = null;      // dữ liệu render khi đã chọn 1 candidate

// Rate limit: 8 lần verify / 15 phút / IP
$rlKey = 'rl_tra_cuu_' . md5($_SERVER['REMOTE_ADDR'] ?? '');
$rl = $_SESSION[$rlKey] ?? ['count' => 0, 'reset' => time() + 900];
if ($rl['reset'] < time()) $rl = ['count' => 0, 'reset' => time() + 900];

// Helper: kiểm tra session đã verify CCCD này chưa
function isCccdVerified(string $cccd): bool {
    return isset($_SESSION['tra_cuu_cccd_ok']) && $_SESSION['tra_cuu_cccd_ok'] === $cccd;
}

// Helper: build candidates từ CCCD (HV chính thức + đơn đăng ký)
function findCandidates(string $cccd): array {
    $list = [];
    foreach (DM_HocVien_BUS::findByCccd($cccd) as $hv) {
        $list[] = [
            'type'   => 'hv',
            'id'     => (int)$hv['id'],
            'label'  => ($hv['ma_hv'] ?? '') . ' - ' . ($hv['ho_ten'] ?? ''),
            'sub'    => $hv['don_vi_cong_tac'] ?? '',
            'status' => 'Học viên',
        ];
    }
    // Đơn đăng ký chưa duyệt hoặc bị từ chối (đã duyệt thì hoc_vien_id link sang HV ở trên rồi)
    $pdo = Database::getConnection();
    $stmt = $pdo->prepare(
        "SELECT dk.id, dk.ho_ten, dk.trang_thai, kh.ten_khoa_hoc, dk.hoc_vien_id
         FROM DT_DANG_KY_KHOA_HOC dk
         LEFT JOIN DT_KHOA_HOC kh ON kh.id = dk.khoa_hoc_id
         WHERE dk.cccd=:c AND dk.da_xoa=0 AND (dk.hoc_vien_id IS NULL OR dk.trang_thai<>1)
         ORDER BY dk.id DESC"
    );
    $stmt->execute([':c' => $cccd]);
    foreach ($stmt->fetchAll() as $dk) {
        $tt = (int)$dk['trang_thai'];
        $list[] = [
            'type'   => 'dk',
            'id'     => (int)$dk['id'],
            'label'  => 'Đơn đăng ký: ' . ($dk['ho_ten'] ?? ''),
            'sub'    => $dk['ten_khoa_hoc'] ?? '',
            'status' => $tt === 0 ? 'Chờ duyệt' : ($tt === 2 ? 'Bị từ chối' : 'Đã duyệt'),
        ];
    }
    return $list;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' || ($cccd !== '' && isCccdVerified($cccd))) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $rl['count']++;
        $_SESSION[$rlKey] = $rl;

        if ($rl['count'] > 8) {
            $errorMsg = 'Bạn đã thử quá nhiều lần. Vui lòng thử lại sau 15 phút.';
        } else if ($cccd === '' || !preg_match('/^\d{9,12}$/', $cccd)) {
            $errorMsg = 'CCCD không hợp lệ (9-12 chữ số)';
        } else if (!isCccdVerified($cccd)) {
            // Lần đầu nhập: cần qua captcha
            $captcha = trim($_POST['captcha'] ?? '');
            $hp      = trim($_POST[CaptchaHelper::honeypotName()] ?? '');
            if (!CaptchaHelper::verify($captcha, $hp)) {
                $errorMsg = 'Mã xác thực sai hoặc đã hết hạn. Thử lại.';
            } else {
                $_SESSION['tra_cuu_cccd_ok'] = $cccd;
            }
        }
    }

    // Nếu chưa lỗi → tìm candidates
    if (!$errorMsg && isCccdVerified($cccd)) {
        $candidates = findCandidates($cccd);
        if (!$candidates) {
            $errorMsg = 'Không tìm thấy thông tin học viên với CCCD này.';
        } else if (count($candidates) === 1 && !$selectHv && !$selectDk) {
            // Chỉ 1 → auto chọn
            $only = $candidates[0];
            if ($only['type'] === 'hv') $selectHv = $only['id'];
            else $selectDk = $only['id'];
        }
    }
}

// Build payload cho render
if (!$errorMsg && isCccdVerified($cccd)) {
    if ($selectHv > 0) {
        $hv = DM_HocVien_BUS::getById($selectHv);
        if ($hv && $hv->cccd === $cccd) {
            $verified = true;
            $payload = ['type' => 'hv', 'hv' => $hv];
            $payload['overview']  = DT_HocVienLop_BUS::getOverview($selectHv);
            $payload['ho_so']     = DT_HoSoHocVien_BUS::getByHocVien($selectHv);
            $payload['chung_chi'] = DT_ChungChi_BUS::getByHocVien($selectHv);
            $payload['lop']       = DT_HocVienLop_BUS::getByHocVien($selectHv);
        } else {
            $errorMsg = 'Không tìm thấy học viên';
        }
    } else if ($selectDk > 0) {
        $dk = DT_DangKyKhoaHoc_BUS::getById($selectDk);
        if ($dk && $dk->cccd === $cccd) {
            $verified = true;
            $payload = ['type' => 'dk', 'dk' => $dk];
        } else {
            $errorMsg = 'Không tìm thấy đơn đăng ký';
        }
    }
}

$captchaQ = (!isCccdVerified($cccd)) ? CaptchaHelper::generate() : null;
$honeypotName = CaptchaHelper::honeypotName();

function ttDangKyLabel(int $tt): array {
    return [0 => ['Chờ duyệt','badge-warning'], 1 => ['Đã duyệt','badge-success'], 2 => ['Từ chối','badge-danger']][$tt] ?? ['?','badge-secondary'];
}
function ttDiemDanhLabel(int $tt): array {
    return [0=>['Vắng KP','#dc2626'],1=>['Có mặt','#16a34a'],2=>['Đi muộn','#0891b2'],3=>['Vắng CP','#ca8a04']][$tt] ?? ['—','#64748b'];
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tra cứu - <?= htmlspecialchars(AppConfig::APP_NAME) ?></title>
    <link rel="stylesheet" href="<?= AppConfig::baseUrl('assets/css/style.css') ?>">
    <style>
        body { background: linear-gradient(135deg, #eff6ff, #f0f9ff); min-height: 100vh; padding: 30px 16px; }
        .pub-wrap { max-width: 1000px; margin: 0 auto; }
        .pub-card { background:#fff; border-radius:14px; box-shadow:0 4px 24px rgba(15,23,42,.08); padding: 24px; }
        .pub-head { text-align:center; margin-bottom: 20px; }
        .pub-head h1 { margin:0 0 6px; font-size: 22px; color: var(--gray-800); }
        .pub-head p { margin:0; color: var(--gray-500); font-size: 13.5px; }
        .login-form { max-width: 460px; margin: 0 auto; }
        .login-form .form-group { margin-bottom: 14px; }
        .alert { padding: 12px 14px; border-radius: 8px; margin-bottom: 14px; font-size: 14px; }
        .alert-error { background:#fee2e2; color:#991b1b; }
        .alert-info { background:#eff6ff; color:#1d4ed8; border-left:3px solid #3b82f6; }
        .pub-honeypot { position:absolute; left:-9999px; top:-9999px; }
        .pub-captcha { background:#fef3c7; padding:14px; border-radius:8px; display:flex; align-items:center; gap:14px; margin: 6px 0 14px; }
        .pub-captcha .q { font-size:18px; font-weight:700; color:#92400e; font-family: monospace; padding:6px 14px; background:#fff; border-radius:6px; min-width:90px; text-align:center; }

        .info-banner { background:#eff6ff; border-left:3px solid var(--primary); padding:12px 14px; border-radius:0 6px 6px 0; margin-bottom:16px; }
        .info-banner h2 { margin:0 0 4px; font-size:17px; color:var(--gray-800); }
        .info-banner p { margin:0; font-size:13px; color:var(--gray-600); }

        .candidate-list { display:flex; flex-direction:column; gap:8px; margin-bottom:14px; }
        .candidate-item { padding:12px 14px; border:1px solid var(--gray-200); border-radius:8px; background:#fff; cursor:pointer; display:flex; align-items:center; gap:12px; text-decoration:none; color:inherit; }
        .candidate-item:hover { border-color:var(--primary); background:#f8fafc; }
        .candidate-info { flex:1; min-width:0; }
        .candidate-label { font-weight:600; color:var(--gray-800); }
        .candidate-sub { font-size:12.5px; color:var(--gray-500); margin-top:2px; }
        .candidate-status { font-size:11px; padding:3px 10px; border-radius:10px; font-weight:600; }
        .candidate-status.t-hv { background:#dcfce7; color:#166534; }
        .candidate-status.t-dk { background:#fef3c7; color:#92400e; }

        .tab-row { display:flex; gap:6px; border-bottom:2px solid var(--gray-200); margin-bottom:18px; flex-wrap:wrap; }
        .tab-btn { padding:10px 16px; background:transparent; border:0; border-bottom:2px solid transparent; cursor:pointer; font-size:13.5px; font-weight:600; color:var(--gray-500); margin-bottom:-2px; }
        .tab-btn.active { color:var(--primary); border-bottom-color:var(--primary); }
        .tab-pane { display:none; }
        .tab-pane.active { display:block; }

        .info-grid { display:grid; grid-template-columns:1fr 1fr; gap:8px 18px; margin:12px 0; }
        .info-cell { padding:6px 0; border-bottom:1px solid var(--gray-100); }
        .info-cell .lbl { color:var(--gray-500); font-size:12px; display:block; }
        .info-cell .val { color:var(--gray-800); font-size:14px; font-weight:500; }

        .stat-row { display:grid; grid-template-columns:repeat(auto-fit, minmax(140px,1fr)); gap:12px; margin-bottom:18px; }
        .stat-cell { background:var(--gray-50); padding:14px; border-radius:8px; text-align:center; border-left:3px solid var(--primary); }
        .stat-cell .num { font-size:24px; font-weight:800; color:var(--gray-800); font-variant-numeric:tabular-nums; }
        .stat-cell .lbl { font-size:12px; color:var(--gray-500); margin-top:2px; }

        .table-portal { width:100%; border-collapse:collapse; font-size:13.5px; }
        .table-portal th { text-align:left; padding:8px 10px; background:var(--gray-50); font-weight:600; font-size:12.5px; color:var(--gray-600); text-transform:uppercase; letter-spacing:.3px; }
        .table-portal td { padding:9px 10px; border-bottom:1px solid var(--gray-100); }
        .empty-mini { padding:30px 16px; text-align:center; color:var(--gray-400); font-size:13px; }
        .pub-foot { text-align:center; margin-top:16px; font-size:12.5px; color:var(--gray-500); }

        .cc-mini-grid { display:grid; grid-template-columns:repeat(auto-fill, minmax(240px,1fr)); gap:12px; }
        .cc-mini { background:#fff; border:1px solid var(--gray-200); border-radius:8px; padding:12px; }
        .cc-mini .so { font-family:monospace; font-size:12px; color:var(--gray-500); }
        .cc-mini .ten { font-weight:600; margin:4px 0; line-height:1.4; }
        .cc-mini .meta { font-size:12px; color:var(--gray-500); }
    </style>
</head>
<body>
<div class="pub-wrap">
    <div class="pub-card">
        <div class="pub-head">
            <h1>Tra cứu thông tin học viên</h1>
            <p><?= htmlspecialchars(AppConfig::APP_NAME) ?></p>
        </div>

        <?php if (!$verified): ?>
            <?php if ($errorMsg): ?>
                <div class="alert alert-error"><?= htmlspecialchars($errorMsg) ?></div>
            <?php endif; ?>

            <?php if (!empty($candidates) && count($candidates) > 1): ?>
                <div class="alert alert-info">Tìm thấy <?= count($candidates) ?> kết quả với CCCD này. Vui lòng chọn:</div>
                <div class="candidate-list">
                    <?php foreach ($candidates as $c): ?>
                        <a class="candidate-item" href="?cccd=<?= urlencode($cccd) ?>&<?= $c['type'] === 'hv' ? 'hv' : 'dk' ?>=<?= (int)$c['id'] ?>">
                            <div class="candidate-info">
                                <div class="candidate-label"><?= htmlspecialchars($c['label']) ?></div>
                                <?php if ($c['sub']): ?><div class="candidate-sub"><?= htmlspecialchars($c['sub']) ?></div><?php endif; ?>
                            </div>
                            <span class="candidate-status t-<?= $c['type'] ?>"><?= htmlspecialchars($c['status']) ?></span>
                        </a>
                    <?php endforeach; ?>
                </div>
                <div style="text-align:center;margin-top:14px">
                    <a href="<?= AppConfig::baseUrl('GUI/public/tra_cuu.php') ?>" class="btn">Tra cứu CCCD khác</a>
                </div>
            <?php else: ?>
                <form method="POST" class="login-form" autocomplete="off">
                    <div class="pub-honeypot">
                        <label>Website</label>
                        <input type="text" name="<?= htmlspecialchars($honeypotName) ?>" tabindex="-1" autocomplete="off">
                    </div>
                    <div class="form-group">
                        <label>Số CCCD <span class="required">*</span></label>
                        <input type="text" name="cccd" class="form-control" required pattern="\d{9,12}" maxlength="12"
                               placeholder="9-12 chữ số" value="<?= htmlspecialchars($cccd) ?>" autocomplete="off">
                    </div>
                    <?php if ($captchaQ): ?>
                    <div class="pub-captcha">
                        <div class="q"><?= htmlspecialchars($captchaQ) ?></div>
                        <input type="number" name="captcha" class="form-control" required placeholder="Nhập kết quả" autocomplete="off">
                    </div>
                    <?php endif; ?>
                    <button type="submit" class="btn btn-primary" style="width:100%;padding:10px">Tra cứu</button>
                    <div style="text-align:center;margin-top:14px;font-size:13px;color:var(--gray-500)">
                        Chưa có thông tin? <a href="<?= AppConfig::baseUrl('GUI/public/dang_ky.php') ?>" style="color:var(--primary)">Đăng ký khóa học</a>
                    </div>
                </form>
            <?php endif; ?>

        <?php elseif ($payload && $payload['type'] === 'dk'): ?>
            <?php
            $dk = $payload['dk'];
            [$ttLbl, $ttCls] = ttDangKyLabel((int)$dk->trang_thai);
            ?>
            <div class="info-banner">
                <h2><?= htmlspecialchars($dk->ho_ten) ?>
                    <span class="badge <?= $ttCls ?>" style="margin-left:8px;font-size:12px"><?= $ttLbl ?></span>
                </h2>
                <p>Đơn đăng ký · <?= htmlspecialchars($dk->ten_khoa_hoc ?? '') ?></p>
            </div>

            <?php if ((int)$dk->trang_thai === 0): ?>
                <div class="alert" style="background:#fef3c7;color:#92400e;border:1px solid #fde68a">
                    Đăng ký <strong>đang chờ duyệt</strong>. Khi được duyệt, bạn sẽ tra cứu được lịch học, điểm danh và kết quả.
                </div>
            <?php elseif ((int)$dk->trang_thai === 2): ?>
                <div class="alert alert-error">
                    <strong>Đăng ký đã bị từ chối.</strong>
                    <?php if ($dk->ly_do_xu_ly): ?><br>Lý do: <?= nl2br(htmlspecialchars($dk->ly_do_xu_ly)) ?><?php endif; ?>
                </div>
            <?php endif; ?>

            <div class="info-grid">
                <div class="info-cell"><span class="lbl">Mã tra cứu</span><span class="val" style="font-family:monospace"><?= htmlspecialchars($dk->ma_tra_cuu) ?></span></div>
                <div class="info-cell"><span class="lbl">Ngày đăng ký</span><span class="val"><?= htmlspecialchars($dk->ngay_tao ?? '-') ?></span></div>
                <div class="info-cell"><span class="lbl">CCCD</span><span class="val">***<?= substr($dk->cccd, -4) ?></span></div>
                <div class="info-cell"><span class="lbl">Email</span><span class="val"><?= htmlspecialchars($dk->email) ?></span></div>
                <div class="info-cell"><span class="lbl">Điện thoại</span><span class="val"><?= htmlspecialchars($dk->dien_thoai ?? '-') ?></span></div>
                <div class="info-cell"><span class="lbl">Đơn vị công tác</span><span class="val"><?= htmlspecialchars($dk->don_vi_cong_tac ?? '-') ?></span></div>
            </div>

            <div style="text-align:center;margin-top:18px">
                <a href="?cccd=<?= urlencode($cccd) ?>" class="btn">← Quay lại danh sách</a>
                <a href="<?= AppConfig::baseUrl('GUI/public/tra_cuu.php') ?>" class="btn">Tra CCCD khác</a>
            </div>

        <?php elseif ($payload && $payload['type'] === 'hv'): ?>
            <?php
            $hv = $payload['hv'];
            $ov = $payload['overview'];
            $hoSo = $payload['ho_so'];
            $chungChi = $payload['chung_chi'];
            $lopList = $payload['lop'];
            $st = $ov['diem_danh_stats'] ?? [];
            ?>
            <div class="info-banner">
                <h2><?= htmlspecialchars($hv->ho_ten) ?>
                    <span class="badge badge-success" style="margin-left:8px;font-size:12px">Học viên</span>
                </h2>
                <p>Mã: <strong><?= htmlspecialchars($hv->ma_hv) ?></strong>
                   <?php if ($hv->don_vi_cong_tac): ?> · <?= htmlspecialchars($hv->don_vi_cong_tac) ?><?php endif; ?>
                </p>
            </div>

            <div class="tab-row">
                <button type="button" class="tab-btn active" data-tab="info">Thông tin</button>
                <button type="button" class="tab-btn" data-tab="lop">Lớp học (<?= count($lopList) ?>)</button>
                <button type="button" class="tab-btn" data-tab="khoa">Khóa học (<?= count($ov['khoa_hoc'] ?? []) ?>)</button>
                <button type="button" class="tab-btn" data-tab="mon">Môn học (<?= count($ov['mon_hoc'] ?? []) ?>)</button>
                <button type="button" class="tab-btn" data-tab="lich">Lịch học (<?= count($ov['lich_hoc'] ?? []) ?>)</button>
                <button type="button" class="tab-btn" data-tab="dd">Điểm danh</button>
                <button type="button" class="tab-btn" data-tab="diem">Bảng điểm (<?= count($ov['ket_qua'] ?? []) ?>)</button>
                <button type="button" class="tab-btn" data-tab="hoso">Hồ sơ (<?= count($hoSo) ?>)</button>
                <button type="button" class="tab-btn" data-tab="cc">Chứng chỉ (<?= count($chungChi) ?>)</button>
            </div>

            <div class="tab-pane active" data-pane="info">
                <div class="info-grid">
                    <div class="info-cell"><span class="lbl">Mã học viên</span><span class="val" style="font-family:monospace"><?= htmlspecialchars($hv->ma_hv) ?></span></div>
                    <div class="info-cell"><span class="lbl">Họ tên</span><span class="val"><?= htmlspecialchars($hv->ho_ten) ?></span></div>
                    <div class="info-cell"><span class="lbl">Ngày sinh</span><span class="val"><?= htmlspecialchars($hv->ngay_sinh ?? '-') ?></span></div>
                    <div class="info-cell"><span class="lbl">Giới tính</span><span class="val"><?= htmlspecialchars($hv->gioi_tinh ?? '-') ?></span></div>
                    <div class="info-cell"><span class="lbl">CCCD</span><span class="val">***<?= substr((string)$hv->cccd, -4) ?></span></div>
                    <div class="info-cell"><span class="lbl">Email</span><span class="val"><?= htmlspecialchars($hv->email ?? '-') ?></span></div>
                    <div class="info-cell"><span class="lbl">Điện thoại</span><span class="val"><?= htmlspecialchars($hv->dien_thoai ?? '-') ?></span></div>
                    <div class="info-cell"><span class="lbl">Đơn vị công tác</span><span class="val"><?= htmlspecialchars($hv->don_vi_cong_tac ?? '-') ?></span></div>
                    <div class="info-cell"><span class="lbl">Chức vụ</span><span class="val"><?= htmlspecialchars($hv->chuc_vu ?? '-') ?></span></div>
                </div>
            </div>

            <div class="tab-pane" data-pane="lop">
                <?php if (!$lopList): ?>
                    <div class="empty-mini">Chưa ghi danh vào lớp nào</div>
                <?php else: ?>
                    <table class="table-portal">
                        <thead><tr><th>Mã lớp</th><th>Tên lớp</th><th>Khóa</th><th>Thời gian</th><th>Trạng thái lớp</th></tr></thead>
                        <tbody>
                        <?php foreach ($lopList as $l):
                            $lopTtMap = [0=>'Chờ KG',1=>'Đang học',2=>'Tạm hoãn',3=>'Kết thúc'];
                            $tg = ($l['ngay_bat_dau'] ?? '?') . ' → ' . ($l['ngay_ket_thuc'] ?? '?');
                        ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($l['ma_lop']) ?></strong></td>
                                <td><?= htmlspecialchars($l['ten_lop']) ?></td>
                                <td class="text-muted"><?= htmlspecialchars($l['ten_khoa_hoc'] ?? '-') ?></td>
                                <td class="text-muted"><?= htmlspecialchars($tg) ?></td>
                                <td><?= htmlspecialchars($lopTtMap[(int)$l['lop_trang_thai']] ?? '?') ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>

            <div class="tab-pane" data-pane="khoa">
                <?php if (!$ov['khoa_hoc']): ?>
                    <div class="empty-mini">Chưa có khóa học</div>
                <?php else: ?>
                    <table class="table-portal">
                        <thead><tr><th>Mã khóa</th><th>Tên khóa</th><th>Tổng tiết</th><th>Tín chỉ</th></tr></thead>
                        <tbody>
                        <?php foreach ($ov['khoa_hoc'] as $k): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($k['ma_khoa_hoc']) ?></strong></td>
                                <td><?= htmlspecialchars($k['ten_khoa_hoc']) ?></td>
                                <td><?= (int)$k['tong_so_tiet'] ?></td>
                                <td><?= (float)$k['so_tin_chi'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>

            <div class="tab-pane" data-pane="mon">
                <?php if (!$ov['mon_hoc']): ?>
                    <div class="empty-mini">Chưa có môn học</div>
                <?php else: ?>
                    <table class="table-portal">
                        <thead><tr><th>Mã môn</th><th>Tên môn</th><th>Khóa</th><th>Tiết</th><th>TC</th><th>Loại</th></tr></thead>
                        <tbody>
                        <?php foreach ($ov['mon_hoc'] as $m): ?>
                            <tr>
                                <td style="font-family:monospace;font-size:12px"><?= htmlspecialchars($m['ma_mon_hoc']) ?></td>
                                <td><?= htmlspecialchars($m['ten_mon_hoc']) ?></td>
                                <td class="text-muted"><?= htmlspecialchars($m['ten_khoa_hoc'] ?? '-') ?></td>
                                <td><?= (int)$m['tong_so_tiet'] ?></td>
                                <td><?= (float)$m['so_tin_chi'] ?></td>
                                <td><?= (int)$m['bat_buoc'] === 1 ? 'Bắt buộc' : 'Tự chọn' ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>

            <div class="tab-pane" data-pane="lich">
                <?php if (!$ov['lich_hoc']): ?>
                    <div class="empty-mini">Chưa có lịch học</div>
                <?php else: ?>
                    <table class="table-portal">
                        <thead><tr><th>Buổi</th><th>Ngày</th><th>Giờ</th><th>Lớp / Môn</th><th>GV</th><th>Phòng</th></tr></thead>
                        <tbody>
                        <?php foreach ($ov['lich_hoc'] as $r): $gv = $r['ten_giang_vien'] ?: ($r['giang_vien_ngoai'] ?? '-'); ?>
                            <tr>
                                <td>#<?= htmlspecialchars($r['buoi_thu'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($r['ngay_hoc']) ?></td>
                                <td><?= htmlspecialchars(substr((string)$r['gio_bat_dau'],0,5)) ?> - <?= htmlspecialchars(substr((string)$r['gio_ket_thuc'],0,5)) ?></td>
                                <td><?= htmlspecialchars($r['ma_lop']) ?>
                                    <?php if ($r['ten_mon_hoc']): ?><div class="text-muted" style="font-size:11.5px"><?= htmlspecialchars($r['ten_mon_hoc']) ?></div><?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($gv) ?></td>
                                <td><?= htmlspecialchars($r['phong_hoc'] ?? '-') ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>

            <div class="tab-pane" data-pane="dd">
                <div class="stat-row">
                    <div class="stat-cell" style="border-left-color:#16a34a"><div class="num"><?= (int)($st['co_mat'] ?? 0) ?></div><div class="lbl">Có mặt</div></div>
                    <div class="stat-cell" style="border-left-color:#0891b2"><div class="num"><?= (int)($st['muon'] ?? 0) ?></div><div class="lbl">Đi muộn</div></div>
                    <div class="stat-cell" style="border-left-color:#ca8a04"><div class="num"><?= (int)($st['vang_cp'] ?? 0) ?></div><div class="lbl">Vắng có phép</div></div>
                    <div class="stat-cell" style="border-left-color:#dc2626"><div class="num"><?= (int)($st['vang_kp'] ?? 0) ?></div><div class="lbl">Vắng không phép</div></div>
                    <div class="stat-cell"><div class="num"><?= (int)($st['tong'] ?? 0) ?></div><div class="lbl">Tổng buổi</div></div>
                </div>
                <?php if (!$ov['diem_danh_detail']): ?>
                    <div class="empty-mini">Chưa có dữ liệu điểm danh</div>
                <?php else: ?>
                    <table class="table-portal">
                        <thead><tr><th>Ngày</th><th>Buổi</th><th>Lớp / Môn</th><th>Trạng thái</th><th>Giờ vào</th></tr></thead>
                        <tbody>
                        <?php foreach ($ov['diem_danh_detail'] as $r): [$lbl,$col]=ttDiemDanhLabel((int)$r['trang_thai']); ?>
                            <tr>
                                <td><?= htmlspecialchars($r['ngay_hoc'] ?? '-') ?></td>
                                <td>#<?= htmlspecialchars($r['buoi_thu'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($r['ma_lop'] ?? '-') ?>
                                    <?php if ($r['ten_mon_hoc']): ?><div class="text-muted" style="font-size:11.5px"><?= htmlspecialchars($r['ten_mon_hoc']) ?></div><?php endif; ?>
                                </td>
                                <td><span style="color:<?= $col ?>;font-weight:600"><?= htmlspecialchars($lbl) ?></span></td>
                                <td class="text-muted"><?= htmlspecialchars(substr((string)($r['gio_vao'] ?? ''),0,5)) ?: '-' ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>

            <div class="tab-pane" data-pane="diem">
                <?php if (!$ov['ket_qua']): ?>
                    <div class="empty-mini">Chưa có bảng điểm</div>
                <?php else: ?>
                    <table class="table-portal">
                        <thead><tr><th>Mã môn</th><th>Tên môn</th><th>TX</th><th>GK</th><th>CK</th><th>TK</th><th>Xếp loại</th><th>Đạt</th></tr></thead>
                        <tbody>
                        <?php foreach ($ov['ket_qua'] as $r): ?>
                            <tr>
                                <td style="font-family:monospace;font-size:12px"><?= htmlspecialchars($r['ma_mon_hoc'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($r['ten_mon_hoc'] ?? '-') ?>
                                    <div class="text-muted" style="font-size:11px"><?= htmlspecialchars($r['ma_lop'] ?? '') ?></div>
                                </td>
                                <td><?= $r['diem_thuong_xuyen'] !== null ? number_format((float)$r['diem_thuong_xuyen'], 1) : '-' ?></td>
                                <td><?= $r['diem_giua_ky'] !== null ? number_format((float)$r['diem_giua_ky'], 1) : '-' ?></td>
                                <td><?= $r['diem_cuoi_ky'] !== null ? number_format((float)$r['diem_cuoi_ky'], 1) : '-' ?></td>
                                <td><strong><?= $r['diem_tong_ket'] !== null ? number_format((float)$r['diem_tong_ket'], 1) : '-' ?></strong></td>
                                <td><?= htmlspecialchars($r['xep_loai'] ?? '-') ?></td>
                                <td>
                                    <?php if ($r['dat'] === null || $r['dat'] === ''): ?>-
                                    <?php elseif ((int)$r['dat'] === 1): ?><span style="color:#16a34a;font-weight:600">Đạt</span>
                                    <?php else: ?><span style="color:#dc2626;font-weight:600">Chưa đạt</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>

            <div class="tab-pane" data-pane="hoso">
                <?php if (!$hoSo): ?>
                    <div class="empty-mini">Chưa có hồ sơ</div>
                <?php else: ?>
                    <table class="table-portal">
                        <thead><tr><th>Loại</th><th>Tên</th><th>Số hiệu</th><th>Ngày cấp</th><th>Hết hạn</th></tr></thead>
                        <tbody>
                        <?php foreach ($hoSo as $h):
                            $hetHan = $h['ngay_het_han'] && strtotime($h['ngay_het_han']) < time();
                        ?>
                            <tr>
                                <td><?= htmlspecialchars($h['loai_ho_so']) ?></td>
                                <td><?= htmlspecialchars($h['ten_ho_so']) ?></td>
                                <td class="text-muted"><?= htmlspecialchars($h['so_hieu'] ?? '-') ?></td>
                                <td class="text-muted"><?= htmlspecialchars($h['ngay_cap'] ?? '-') ?></td>
                                <td class="<?= $hetHan ? 'text-danger' : 'text-muted' ?>"><?= htmlspecialchars($h['ngay_het_han'] ?? '-') ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>

            <div class="tab-pane" data-pane="cc">
                <?php if (!$chungChi): ?>
                    <div class="empty-mini">Chưa có chứng chỉ</div>
                <?php else: ?>
                    <div class="cc-mini-grid">
                    <?php foreach ($chungChi as $c): ?>
                        <div class="cc-mini">
                            <div class="so"><?= htmlspecialchars($c['so_chung_chi']) ?></div>
                            <div class="ten"><?= htmlspecialchars($c['ten_chung_chi']) ?></div>
                            <div class="meta">
                                <?= htmlspecialchars($c['loai_chung_chi'] ?? '') ?>
                                <?php if ($c['xep_loai_tot_nghiep']): ?> · <?= htmlspecialchars($c['xep_loai_tot_nghiep']) ?><?php endif; ?>
                            </div>
                            <div class="meta">Cấp: <?= htmlspecialchars($c['ngay_cap']) ?></div>
                            <?php if (!empty($c['ten_lop'])): ?>
                                <div class="meta">Lớp: <?= htmlspecialchars(($c['ma_lop'] ?? '') . ' - ' . $c['ten_lop']) ?></div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div style="margin-top:20px;text-align:center;display:flex;gap:8px;justify-content:center;flex-wrap:wrap">
                <?php if (count($candidates) > 1): ?>
                    <a href="?cccd=<?= urlencode($cccd) ?>" class="btn">← Quay lại danh sách</a>
                <?php endif; ?>
                <a href="<?= AppConfig::baseUrl('GUI/public/tra_cuu.php') ?>" class="btn">Tra CCCD khác</a>
            </div>

            <script>
            (function(){
                var btns = document.querySelectorAll('.tab-btn');
                var panes = document.querySelectorAll('.tab-pane');
                btns.forEach(function(b){
                    b.addEventListener('click', function(){
                        btns.forEach(function(x){ x.classList.remove('active'); });
                        panes.forEach(function(x){ x.classList.remove('active'); });
                        b.classList.add('active');
                        var t = b.getAttribute('data-tab');
                        var p = document.querySelector('.tab-pane[data-pane="'+t+'"]');
                        if (p) p.classList.add('active');
                    });
                });
            })();
            </script>
        <?php endif; ?>
    </div>

    <div class="pub-foot">
        © <?= date('Y') ?> <?= htmlspecialchars(AppConfig::APP_NAME) ?>
    </div>
</div>
</body>
</html>
