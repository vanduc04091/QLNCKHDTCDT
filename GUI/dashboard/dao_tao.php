<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../BUS/DT_Dashboard_BUS.php';

Helper::requireLogin();

$kpi      = DT_Dashboard_BUS::getKpis();
$lichToi  = DT_Dashboard_BUS::getUpcomingSchedule(10);
$dkCho    = DT_Dashboard_BUS::getPendingRegistrations(5);
$lopFull  = DT_Dashboard_BUS::getTopFullClasses(5);
$dtPhanBo = DT_Dashboard_BUS::getHocVienByDoiTuong();
$dkTrend  = DT_Dashboard_BUS::getRegistrationTrend();

$base = AppConfig::baseUrl('');

$pageTitle  = 'Tổng quan đào tạo';
$activeMenu = 'DT_Dashboard';
require __DIR__ . '/../layouts/header.php';

// Helper: nhóm lịch theo ngày (label theo thứ)
$lichByNgay = [];
foreach ($lichToi as $l) {
    $key = $l['ngay_hoc'];
    if (!isset($lichByNgay[$key])) $lichByNgay[$key] = [];
    $lichByNgay[$key][] = $l;
}

// Tính max cho bar mini phân bố
$dtMax = 0;
foreach ($dtPhanBo as $r) $dtMax = max($dtMax, (int)$r['so_luong']);

// Tính max cho trend chart (đăng ký 30 ngày)
$trendMax = 0;
foreach ($dkTrend as $r) $trendMax = max($trendMax, (int)$r['so_luong']);
?>

<div class="breadcrumb">
    <a href="<?= AppConfig::baseUrl('GUI/dashboard/index.php') ?>">Trang chủ</a>
    <span class="sep">›</span> Đào tạo
    <span class="sep">›</span> <span>Tổng quan</span>
</div>

<!-- Hàng KPI -->
<div class="dt-kpi-row">
    <a href="<?= $base ?>GUI/DM_HocVien/index.php" class="dt-kpi dt-kpi-blue">
        <div class="dt-kpi-icon"><?= IconHelper::svg('users', '24') ?></div>
        <div class="dt-kpi-body">
            <div class="dt-kpi-label">Học viên hoạt động</div>
            <div class="dt-kpi-value"><?= number_format($kpi['hoc_vien']) ?></div>
        </div>
    </a>
    <a href="<?= $base ?>GUI/DT_KhoaHoc/index.php" class="dt-kpi dt-kpi-purple">
        <div class="dt-kpi-icon"><?= IconHelper::svg('book-open', '24') ?></div>
        <div class="dt-kpi-body">
            <div class="dt-kpi-label">Khóa học mở</div>
            <div class="dt-kpi-value"><?= number_format($kpi['khoa_hoc']) ?></div>
        </div>
    </a>
    <a href="<?= $base ?>GUI/DT_LopHoc/index.php" class="dt-kpi dt-kpi-green">
        <div class="dt-kpi-icon"><?= IconHelper::svg('school', '24') ?></div>
        <div class="dt-kpi-body">
            <div class="dt-kpi-label">Lớp đang học</div>
            <div class="dt-kpi-value"><?= number_format($kpi['lop_hoc']) ?></div>
        </div>
    </a>
    <a href="<?= $base ?>GUI/DT_DangKyKhoaHoc/index.php" class="dt-kpi <?= $kpi['dk_cho'] > 0 ? 'dt-kpi-amber dt-kpi-pulse' : 'dt-kpi-gray' ?>">
        <div class="dt-kpi-icon"><?= IconHelper::svg('clipboard-list', '24') ?></div>
        <div class="dt-kpi-body">
            <div class="dt-kpi-label">Đăng ký chờ duyệt</div>
            <div class="dt-kpi-value"><?= number_format($kpi['dk_cho']) ?></div>
        </div>
        <?php if ($kpi['dk_cho'] > 0): ?><span class="dt-kpi-badge">Cần xử lý</span><?php endif; ?>
    </a>
    <a href="<?= $base ?>GUI/DT_ChungChi/index.php" class="dt-kpi dt-kpi-cyan">
        <div class="dt-kpi-icon"><?= IconHelper::svg('graduation-cap', '24') ?></div>
        <div class="dt-kpi-body">
            <div class="dt-kpi-label">Chứng chỉ tháng này</div>
            <div class="dt-kpi-value"><?= number_format($kpi['cc_thang_nay']) ?></div>
        </div>
    </a>
    <a href="<?= $base ?>GUI/DT_LichHoc/index.php" class="dt-kpi dt-kpi-rose">
        <div class="dt-kpi-icon"><?= IconHelper::svg('calendar', '24') ?></div>
        <div class="dt-kpi-body">
            <div class="dt-kpi-label">Buổi học 7 ngày tới</div>
            <div class="dt-kpi-value"><?= number_format($kpi['buoi_7_ngay']) ?></div>
        </div>
    </a>
</div>

<!-- Quick actions -->
<div class="dt-quick">
    <span class="dt-quick-label">Thao tác nhanh:</span>
    <a href="<?= $base ?>GUI/DT_KhoaHoc/index.php" class="btn btn-sm">+ Khóa</a>
    <a href="<?= $base ?>GUI/DT_LopHoc/index.php" class="btn btn-sm">+ Lớp</a>
    <a href="<?= $base ?>GUI/DM_HocVien/index.php" class="btn btn-sm">+ Học viên</a>
    <a href="<?= $base ?>GUI/DT_LichHoc/index.php" class="btn btn-sm">+ Lịch học</a>
    <a href="<?= $base ?>GUI/DT_DangKyKhoaHoc/index.php" class="btn btn-sm btn-primary">Duyệt đăng ký</a>
    <a href="<?= AppConfig::baseUrl('GUI/public/dang_ky.php') ?>" class="btn btn-sm" target="_blank">Link đăng ký công khai ↗</a>
</div>

<!-- Hàng giữa: Lịch + Đăng ký chờ -->
<div class="dt-grid-2">
    <!-- Lịch học -->
    <div class="card">
        <div class="dt-card-head">
            <h3><?= IconHelper::svg('calendar', '18') ?> Lịch học sắp tới (7 ngày)</h3>
            <a href="<?= $base ?>GUI/DT_LichHoc/index.php" class="dt-card-link">Xem tất cả →</a>
        </div>
        <?php if (!$lichToi): ?>
            <div class="dt-empty">Không có buổi học nào trong 7 ngày tới.</div>
        <?php else: ?>
            <div class="dt-lich-list">
                <?php
                $weekDays = ['Chủ nhật','Thứ 2','Thứ 3','Thứ 4','Thứ 5','Thứ 6','Thứ 7'];
                foreach ($lichByNgay as $ngay => $rows):
                    $ts = strtotime($ngay);
                    $dow = $weekDays[(int)date('w', $ts)];
                    $isToday = (date('Y-m-d') === $ngay);
                ?>
                    <div class="dt-lich-day-head <?= $isToday ? 'today' : '' ?>">
                        <?= $dow ?> · <?= date('d/m', $ts) ?>
                        <?php if ($isToday): ?><span class="dt-today-tag">Hôm nay</span><?php endif; ?>
                    </div>
                    <?php foreach ($rows as $r):
                        $gv = $r['ten_giang_vien'] ?: ($r['giang_vien_ngoai'] ?: '-');
                    ?>
                        <div class="dt-lich-item">
                            <div class="dt-lich-time"><?= substr((string)$r['gio_bat_dau'],0,5) ?>–<?= substr((string)$r['gio_ket_thuc'],0,5) ?></div>
                            <div class="dt-lich-info">
                                <div class="dt-lich-title">
                                    <strong><?= Helper::h($r['ma_lop'] ?? '?') ?></strong> · Buổi #<?= (int)$r['buoi_thu'] ?>
                                    <?php if ($r['ten_mon_hoc']): ?>· <?= Helper::h($r['ten_mon_hoc']) ?><?php endif; ?>
                                </div>
                                <div class="dt-lich-sub">
                                    GV: <?= Helper::h($gv) ?>
                                    <?php if ($r['phong_hoc']): ?> · 🏠 <?= Helper::h($r['phong_hoc']) ?><?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Đăng ký chờ duyệt -->
    <div class="card">
        <div class="dt-card-head">
            <h3><?= IconHelper::svg('clipboard-list', '18') ?> Đăng ký chờ duyệt</h3>
            <a href="<?= $base ?>GUI/DT_DangKyKhoaHoc/index.php" class="dt-card-link">Xem tất cả →</a>
        </div>
        <?php if (!$dkCho): ?>
            <div class="dt-empty">Không có đăng ký nào đang chờ. 🎉</div>
        <?php else: ?>
            <div class="dt-dk-list">
                <?php foreach ($dkCho as $d): ?>
                    <a href="<?= $base ?>GUI/DT_DangKyKhoaHoc/index.php" class="dt-dk-item">
                        <div class="dt-dk-info">
                            <div class="dt-dk-name"><?= Helper::h($d['ho_ten']) ?></div>
                            <div class="dt-dk-meta">
                                <?= Helper::h($d['ten_khoa_hoc'] ?? '-') ?>
                            </div>
                            <div class="dt-dk-meta-sub">
                                <?= Helper::h($d['email']) ?>
                                <?php if ($d['dien_thoai']): ?> · <?= Helper::h($d['dien_thoai']) ?><?php endif; ?>
                            </div>
                        </div>
                        <div class="dt-dk-time">
                            <?= date('d/m H:i', strtotime($d['ngay_tao'])) ?>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Hàng dưới: Lớp gần đầy + Phân bố HV -->
<div class="dt-grid-2">
    <!-- Lớp gần đầy -->
    <div class="card">
        <div class="dt-card-head">
            <h3><?= IconHelper::svg('school', '18') ?> Lớp gần đầy nhất</h3>
            <a href="<?= $base ?>GUI/DT_LopHoc/index.php" class="dt-card-link">Xem tất cả →</a>
        </div>
        <?php if (!$lopFull): ?>
            <div class="dt-empty">Chưa có lớp nào.</div>
        <?php else: ?>
            <div class="dt-lop-list">
                <?php foreach ($lopFull as $l):
                    $max = (int)$l['so_luong_toi_da'];
                    $cur = (int)$l['so_hv'];
                    $pct = $max > 0 ? min(100, round(($cur / $max) * 100)) : 0;
                    $pctCls = $pct >= 100 ? 'full' : ($pct >= 80 ? 'warn' : '');
                ?>
                    <div class="dt-lop-item">
                        <div class="dt-lop-info">
                            <div class="dt-lop-name">
                                <strong><?= Helper::h($l['ma_lop']) ?></strong> · <?= Helper::h($l['ten_lop']) ?>
                            </div>
                            <div class="dt-lop-sub"><?= Helper::h($l['ten_khoa_hoc'] ?? '-') ?></div>
                        </div>
                        <div class="dt-lop-bar-wrap">
                            <div class="dt-lop-bar <?= $pctCls ?>"><div class="dt-lop-bar-fill" style="width:<?= $pct ?>%"></div></div>
                            <div class="dt-lop-bar-text"><?= $cur ?>/<?= $max ?> (<?= $pct ?>%)</div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Phân bố HV theo đối tượng -->
    <div class="card">
        <div class="dt-card-head">
            <h3><?= IconHelper::svg('bar-chart', '18') ?> Phân bố học viên theo đối tượng</h3>
            <a href="<?= $base ?>GUI/DM_DoiTuongHocVien/index.php" class="dt-card-link">Quản lý →</a>
        </div>
        <?php if (!$dtPhanBo): ?>
            <div class="dt-empty">Chưa có dữ liệu.</div>
        <?php else: ?>
            <div class="dt-bar-list">
                <?php foreach ($dtPhanBo as $r):
                    $sl = (int)$r['so_luong'];
                    $w = $dtMax > 0 ? round(($sl / $dtMax) * 100) : 0;
                ?>
                    <div class="dt-bar-row">
                        <div class="dt-bar-label" title="<?= Helper::h($r['ten']) ?>"><?= Helper::h($r['ten']) ?></div>
                        <div class="dt-bar-track"><div class="dt-bar-fill" style="width:<?= $w ?>%"></div></div>
                        <div class="dt-bar-val"><?= number_format($sl) ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Trend đăng ký 30 ngày -->
<?php if ($dkTrend): ?>
<div class="card">
    <div class="dt-card-head">
        <h3><?= IconHelper::svg('trending-up', '18') ?> Đăng ký 30 ngày qua</h3>
        <span class="text-muted" style="font-size:12.5px">Tổng: <?= array_sum(array_column($dkTrend, 'so_luong')) ?> đơn</span>
    </div>
    <div class="dt-trend">
        <?php foreach ($dkTrend as $t):
            $sl = (int)$t['so_luong'];
            $h = $trendMax > 0 ? round(($sl / $trendMax) * 100) : 0;
        ?>
            <div class="dt-trend-col" title="<?= Helper::h($t['ngay']) ?>: <?= $sl ?> đơn">
                <div class="dt-trend-bar" style="height:<?= max(2, $h) ?>%"></div>
                <div class="dt-trend-day"><?= date('d/m', strtotime($t['ngay'])) ?></div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<style>
    /* ====== Dashboard Đào tạo ====== */
    .dt-kpi-row { display:grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap:12px; margin-bottom:14px; }
    .dt-kpi { display:flex; align-items:center; gap:12px; padding:14px 16px; border-radius:10px; background:#fff; border:1px solid var(--gray-200); text-decoration:none; color:inherit; transition: transform .12s ease, box-shadow .15s ease; position:relative; }
    .dt-kpi:hover { transform: translateY(-2px); box-shadow: 0 4px 14px rgba(15,23,42,.08); }
    .dt-kpi-icon { width:46px; height:46px; border-radius:10px; display:flex; align-items:center; justify-content:center; flex-shrink:0; color:#fff; }
    .dt-kpi-body { flex:1; min-width:0; }
    .dt-kpi-label { font-size:12px; color:var(--gray-500); text-transform:uppercase; letter-spacing:.4px; }
    .dt-kpi-value { font-size:24px; font-weight:800; color:var(--gray-800); font-variant-numeric:tabular-nums; margin-top:2px; }
    .dt-kpi-blue   .dt-kpi-icon { background:linear-gradient(135deg,#60a5fa,#3b82f6); }
    .dt-kpi-purple .dt-kpi-icon { background:linear-gradient(135deg,#a78bfa,#8b5cf6); }
    .dt-kpi-green  .dt-kpi-icon { background:linear-gradient(135deg,#4ade80,#16a34a); }
    .dt-kpi-amber  .dt-kpi-icon { background:linear-gradient(135deg,#fbbf24,#d97706); }
    .dt-kpi-cyan   .dt-kpi-icon { background:linear-gradient(135deg,#22d3ee,#0891b2); }
    .dt-kpi-rose   .dt-kpi-icon { background:linear-gradient(135deg,#fb7185,#e11d48); }
    .dt-kpi-gray   .dt-kpi-icon { background:linear-gradient(135deg,#cbd5e1,#94a3b8); }
    .dt-kpi-pulse  { border-color:#f59e0b; box-shadow: 0 0 0 0 rgba(245,158,11,.4); animation: dtPulse 2s infinite; }
    @keyframes dtPulse { 0% { box-shadow: 0 0 0 0 rgba(245,158,11,.4); } 70% { box-shadow: 0 0 0 8px rgba(245,158,11,0); } 100% { box-shadow: 0 0 0 0 rgba(245,158,11,0); } }
    .dt-kpi-badge { position:absolute; top:8px; right:10px; background:#dc2626; color:#fff; font-size:10.5px; font-weight:700; padding:2px 8px; border-radius:10px; }

    .dt-quick { display:flex; flex-wrap:wrap; align-items:center; gap:8px; padding:10px 14px; background:#f8fafc; border:1px solid var(--gray-200); border-radius:10px; margin-bottom:14px; }
    .dt-quick-label { font-size:12.5px; color:var(--gray-600); font-weight:600; }

    .dt-grid-2 { display:grid; grid-template-columns: 1fr 1fr; gap:14px; margin-bottom:14px; }

    .dt-card-head { display:flex; align-items:center; justify-content:space-between; padding:12px 16px; border-bottom:1px solid var(--gray-100); }
    .dt-card-head h3 { margin:0; font-size:14.5px; color:var(--gray-700); display:inline-flex; align-items:center; gap:8px; }
    .dt-card-link { font-size:12.5px; color:var(--primary); text-decoration:none; }
    .dt-card-link:hover { text-decoration:underline; }
    .dt-empty { padding:30px 16px; text-align:center; color:var(--gray-400); font-size:13px; }

    /* Lịch */
    .dt-lich-list { padding:8px 16px 14px; }
    .dt-lich-day-head { font-size:11.5px; font-weight:700; color:var(--gray-500); text-transform:uppercase; letter-spacing:.5px; margin:10px 0 6px; padding-bottom:4px; border-bottom:1px dashed var(--gray-200); }
    .dt-lich-day-head.today { color:var(--primary); }
    .dt-today-tag { background:var(--primary); color:#fff; font-size:10px; padding:1px 7px; border-radius:8px; margin-left:6px; text-transform:none; letter-spacing:0; }
    .dt-lich-item { display:flex; gap:12px; padding:8px 0; border-bottom:1px solid var(--gray-50); }
    .dt-lich-item:last-child { border-bottom:none; }
    .dt-lich-time { font-family:'Consolas',monospace; font-size:12.5px; color:var(--gray-700); font-weight:600; min-width:90px; }
    .dt-lich-info { flex:1; min-width:0; }
    .dt-lich-title { font-size:13.5px; color:var(--gray-800); }
    .dt-lich-sub { font-size:11.5px; color:var(--gray-500); margin-top:2px; }

    /* Đăng ký */
    .dt-dk-list { padding:6px 10px 12px; }
    .dt-dk-item { display:flex; gap:10px; align-items:flex-start; padding:10px 6px; border-bottom:1px solid var(--gray-100); text-decoration:none; color:inherit; }
    .dt-dk-item:hover { background:var(--gray-50); }
    .dt-dk-item:last-child { border-bottom:none; }
    .dt-dk-info { flex:1; min-width:0; }
    .dt-dk-name { font-weight:600; color:var(--gray-800); font-size:13.5px; }
    .dt-dk-meta { font-size:12px; color:var(--gray-600); margin-top:2px; }
    .dt-dk-meta-sub { font-size:11.5px; color:var(--gray-500); margin-top:2px; }
    .dt-dk-time { font-size:11px; color:var(--gray-400); white-space:nowrap; }

    /* Lớp gần đầy */
    .dt-lop-list { padding:8px 16px 14px; }
    .dt-lop-item { display:flex; gap:10px; align-items:center; padding:10px 0; border-bottom:1px solid var(--gray-50); }
    .dt-lop-item:last-child { border-bottom:none; }
    .dt-lop-info { flex:1; min-width:0; }
    .dt-lop-name { font-size:13.5px; color:var(--gray-800); }
    .dt-lop-sub { font-size:11.5px; color:var(--gray-500); margin-top:2px; }
    .dt-lop-bar-wrap { width:160px; flex-shrink:0; }
    .dt-lop-bar { width:100%; height:6px; background:var(--gray-200); border-radius:3px; overflow:hidden; }
    .dt-lop-bar-fill { height:100%; background:#3b82f6; transition: width .3s ease; }
    .dt-lop-bar.warn .dt-lop-bar-fill { background:#f59e0b; }
    .dt-lop-bar.full .dt-lop-bar-fill { background:#dc2626; }
    .dt-lop-bar-text { font-size:11px; color:var(--gray-500); margin-top:3px; text-align:right; font-variant-numeric:tabular-nums; }

    /* Bar mini phân bố */
    .dt-bar-list { padding:14px 16px; display:flex; flex-direction:column; gap:8px; }
    .dt-bar-row { display:grid; grid-template-columns:140px 1fr 50px; gap:10px; align-items:center; }
    .dt-bar-label { font-size:12.5px; color:var(--gray-700); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
    .dt-bar-track { height:14px; background:var(--gray-100); border-radius:7px; overflow:hidden; }
    .dt-bar-fill { height:100%; background:linear-gradient(90deg,#60a5fa,#3b82f6); border-radius:7px; }
    .dt-bar-val { font-size:12.5px; font-weight:700; color:var(--gray-700); text-align:right; font-variant-numeric:tabular-nums; }

    /* Trend 30 ngày */
    .dt-trend { display:flex; gap:2px; align-items:flex-end; padding:14px 16px; height:140px; }
    .dt-trend-col { flex:1; min-width:0; display:flex; flex-direction:column; align-items:center; gap:4px; cursor:default; }
    .dt-trend-bar { width:80%; background:linear-gradient(180deg,#60a5fa,#3b82f6); border-radius:3px 3px 0 0; min-height:2px; transition: opacity .15s ease; }
    .dt-trend-col:hover .dt-trend-bar { opacity:.75; }
    .dt-trend-day { font-size:9.5px; color:var(--gray-500); writing-mode:vertical-rl; transform:rotate(180deg); white-space:nowrap; }

    @media (max-width: 900px) {
        .dt-grid-2 { grid-template-columns: 1fr; }
        .dt-bar-row { grid-template-columns:110px 1fr 40px; }
        .dt-lop-bar-wrap { width:120px; }
    }
</style>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
