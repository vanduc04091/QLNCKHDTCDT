<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../BUS/NCKH_Dashboard_BUS.php';

Helper::requireLogin();
if (!PhanQuyenHelper::hasQuyen('NCKH_Dashboard', PhanQuyenHelper::QUYEN_XEM)
    && !PhanQuyenHelper::hasQuyen('NCKH_DeTai', PhanQuyenHelper::QUYEN_XEM)) {
    echo 'Bạn không có quyền truy cập.'; exit;
}

$nam = isset($_GET['nam']) ? (int)$_GET['nam'] : (int)date('Y');
$kpi = NCKH_Dashboard_BUS::getKpis($nam);
$byCapDo = NCKH_Dashboard_BUS::statsByCapDo($nam);
$byTheLoai = NCKH_Dashboard_BUS::statsByTheLoai($nam);
$byKhoa = NCKH_Dashboard_BUS::statsByKhoaPhong($nam, 8);
$upcoming = NCKH_Dashboard_BUS::getUpcomingDeadlines(8);
$overdue = NCKH_Dashboard_BUS::getOverdueReports(90);
$trend = NCKH_Dashboard_BUS::trend5Years();

$pageTitle = 'Tổng quan NCKH';
$activeMenu = 'NCKH_Dashboard';
require __DIR__ . '/../layouts/header.php';

function nckh_tt_text(int $tt): string
{
    return [0=>'Đề xuất',1=>'Đang thực hiện',2=>'Hoàn thành',3=>'Tạm dừng',4=>'Hủy'][$tt] ?? '-';
}
function nckh_max_so_luong(array $rows): int
{
    $vals = array_map(fn($r) => (int)$r['so_luong'], $rows);
    return $vals ? max($vals) : 1;
}
?>
<style>
.nk-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(220px,1fr)); gap:14px; margin-bottom:16px; }
.nk-card { background:#fff; padding:16px; border-radius:10px; box-shadow:0 1px 3px rgba(0,0,0,.06); display:flex; align-items:center; gap:14px; }
.nk-card .ico { width:46px; height:46px; border-radius:10px; display:flex; align-items:center; justify-content:center; color:#fff; flex:0 0 auto; }
.nk-card .lbl { color:#64748b; font-size:12px; text-transform:uppercase; letter-spacing:.3px; }
.nk-card .val { font-size:24px; font-weight:700; color:#0f172a; line-height:1.1; }
.nk-2col { display:grid; grid-template-columns:repeat(auto-fit,minmax(360px,1fr)); gap:16px; }
.nk-block { background:#fff; padding:18px; border-radius:10px; box-shadow:0 1px 3px rgba(0,0,0,.06); margin-bottom:16px; }
.nk-block h3 { margin:0 0 12px; font-size:14px; font-weight:600; color:#0f172a; display:flex; justify-content:space-between; align-items:center; }
.nk-bar { display:grid; grid-template-columns:140px 1fr 50px; gap:8px; align-items:center; padding:6px 0; font-size:13px; }
.nk-bar .name { color:#334155; }
.nk-bar .track { background:#e2e8f0; height:12px; border-radius:6px; overflow:hidden; }
.nk-bar .track > span { display:block; height:100%; background:linear-gradient(90deg,#3b82f6,#22c55e); }
.nk-bar .num { text-align:right; color:#475569; font-weight:600; }
.nk-list .item { display:flex; gap:10px; padding:8px 0; border-bottom:1px dashed #e2e8f0; align-items:start; }
.nk-list .item:last-child { border-bottom:0; }
.nk-list .ic { width:32px; flex:0 0 32px; height:32px; border-radius:8px; background:#fef3c7; color:#92400e; display:flex; align-items:center; justify-content:center; font-weight:700; }
.nk-list .item.danger .ic { background:#fee2e2; color:#991b1b; }
.nk-list .meta { font-size:12px; color:#64748b; }
.nk-trend { display:flex; gap:8px; align-items:flex-end; height:140px; padding:6px 0; }
.nk-trend .col { flex:1; display:flex; flex-direction:column; align-items:center; gap:6px; }
.nk-trend .bar { width:100%; max-width:50px; background:linear-gradient(180deg,#2563eb,#60a5fa); border-radius:6px 6px 0 0; min-height:4px; transition:height .3s; position:relative; }
.nk-trend .bar .lbl { position:absolute; top:-18px; left:50%; transform:translateX(-50%); font-size:11px; color:#475569; font-weight:600; }
.nk-trend .yr { font-size:12px; color:#64748b; }
</style>

<div class="breadcrumb">
    <a href="<?= AppConfig::baseUrl('GUI/dashboard/index.php') ?>">Trang chủ</a>
    <span class="sep">›</span> <span>Tổng quan NCKH</span>
</div>

<div class="card" style="padding:14px 18px;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:8px">
    <div>
        <div style="font-size:13px;color:#64748b">Tổng quan đề tài / sáng kiến</div>
        <div style="font-size:18px;font-weight:700;color:#0f172a">Năm <?= $nam ?></div>
    </div>
    <form method="get" style="display:flex;gap:8px;align-items:center">
        <label class="text-muted" style="font-size:13px">Chọn năm:</label>
        <select name="nam" class="form-select" style="width:120px" onchange="this.form.submit()">
            <?php for ($y = (int)date('Y') + 1; $y >= (int)date('Y') - 6; $y--): ?>
                <option value="<?= $y ?>" <?= $y === $nam ? 'selected':'' ?>><?= $y ?></option>
            <?php endfor; ?>
        </select>
    </form>
</div>

<!-- KPI -->
<div class="nk-grid">
    <a href="<?= AppConfig::baseUrl('GUI/NCKH_DeTai/index.php?nam=' . $nam) ?>" class="nk-card" style="text-decoration:none">
        <div class="ico" style="background:linear-gradient(135deg,#2563eb,#60a5fa)"><?= IconHelper::svg('star', 24, 'icon', '#fff') ?></div>
        <div><div class="lbl">Tổng đề tài</div><div class="val"><?= $kpi['tong'] ?></div></div>
    </a>
    <div class="nk-card">
        <div class="ico" style="background:linear-gradient(135deg,#f59e0b,#fbbf24)"><?= IconHelper::svg('clock', 24, 'icon', '#fff') ?></div>
        <div><div class="lbl">Đề xuất</div><div class="val"><?= $kpi['de_xuat'] ?></div></div>
    </div>
    <div class="nk-card">
        <div class="ico" style="background:linear-gradient(135deg,#3b82f6,#06b6d4)"><?= IconHelper::svg('play-circle', 24, 'icon', '#fff') ?></div>
        <div><div class="lbl">Đang thực hiện</div><div class="val"><?= $kpi['dang_thuc_hien'] ?></div></div>
    </div>
    <div class="nk-card">
        <div class="ico" style="background:linear-gradient(135deg,#10b981,#22c55e)"><?= IconHelper::svg('check', 24, 'icon', '#fff') ?></div>
        <div><div class="lbl">Hoàn thành</div><div class="val"><?= $kpi['hoan_thanh'] ?></div></div>
    </div>
    <div class="nk-card">
        <div class="ico" style="background:linear-gradient(135deg,#64748b,#94a3b8)"><?= IconHelper::svg('alert-triangle', 24, 'icon', '#fff') ?></div>
        <div><div class="lbl">Tạm dừng / Hủy</div><div class="val"><?= ($kpi['tam_dung'] + $kpi['huy']) ?></div></div>
    </div>
</div>

<!-- Phân bố cấp độ + thể loại -->
<div class="nk-2col">
    <div class="nk-block">
        <h3>Phân bố theo cấp độ</h3>
        <?php
        $maxCD = nckh_max_so_luong($byCapDo);
        foreach ($byCapDo as $r): $w = (int)$r['so_luong'] / $maxCD * 100; ?>
            <div class="nk-bar">
                <div class="name"><?= Helper::h($r['ten']) ?></div>
                <div class="track"><span style="width:<?= $w ?>%"></span></div>
                <div class="num"><?= (int)$r['so_luong'] ?></div>
            </div>
        <?php endforeach; ?>
        <?php if (empty($byCapDo)): ?><div class="empty-state">Không có dữ liệu</div><?php endif; ?>
    </div>

    <div class="nk-block">
        <h3>Phân bố theo thể loại</h3>
        <?php
        $maxTL = nckh_max_so_luong($byTheLoai);
        foreach ($byTheLoai as $r): $w = (int)$r['so_luong'] / $maxTL * 100; ?>
            <div class="nk-bar">
                <div class="name"><?= Helper::h($r['ten']) ?></div>
                <div class="track"><span style="width:<?= $w ?>%;background:linear-gradient(90deg,#8b5cf6,#ec4899)"></span></div>
                <div class="num"><?= (int)$r['so_luong'] ?></div>
            </div>
        <?php endforeach; ?>
        <?php if (empty($byTheLoai)): ?><div class="empty-state">Không có dữ liệu</div><?php endif; ?>
    </div>
</div>

<!-- Top khoa phòng + sắp đến hạn -->
<div class="nk-2col">
    <div class="nk-block">
        <h3>Top khoa/phòng có nhiều đề tài</h3>
        <?php
        $maxKP = nckh_max_so_luong($byKhoa);
        foreach ($byKhoa as $r): $w = (int)$r['so_luong'] / $maxKP * 100; ?>
            <div class="nk-bar">
                <div class="name"><?= Helper::h($r['ten']) ?></div>
                <div class="track"><span style="width:<?= $w ?>%;background:linear-gradient(90deg,#0ea5e9,#22c55e)"></span></div>
                <div class="num"><?= (int)$r['so_luong'] ?></div>
            </div>
        <?php endforeach; ?>
        <?php if (empty($byKhoa)): ?><div class="empty-state">Không có dữ liệu</div><?php endif; ?>
    </div>

    <div class="nk-block">
        <h3>Sắp đến hạn (30 ngày tới)</h3>
        <div class="nk-list">
            <?php foreach ($upcoming as $r): ?>
                <div class="item">
                    <div class="ic"><?= date('d', strtotime($r['ngay_ket_thuc_du_kien'])) ?></div>
                    <div style="flex:1">
                        <div><strong><?= Helper::h($r['ten_de_tai']) ?></strong></div>
                        <div class="meta">[<?= Helper::h($r['ma_de_tai']) ?>] &middot; <?= Helper::h($r['ho_ten_chu_nhiem'] ?? '') ?> &middot; HSD: <?= date('d/m/Y', strtotime($r['ngay_ket_thuc_du_kien'])) ?> &middot; <?= nckh_tt_text((int)$r['trang_thai']) ?></div>
                    </div>
                </div>
            <?php endforeach; ?>
            <?php if (empty($upcoming)): ?><div class="empty-state">Không có đề tài sắp đến hạn</div><?php endif; ?>
        </div>
    </div>
</div>

<!-- Quá hạn báo cáo + xu hướng 5 năm -->
<div class="nk-2col">
    <div class="nk-block">
        <h3>Đề tài chậm báo cáo tiến độ (>90 ngày)</h3>
        <div class="nk-list">
            <?php foreach (array_slice($overdue, 0, 8) as $r): ?>
                <div class="item danger">
                    <div class="ic">!</div>
                    <div style="flex:1">
                        <div><strong><?= Helper::h($r['ten_de_tai']) ?></strong></div>
                        <div class="meta">[<?= Helper::h($r['ma_de_tai']) ?>] &middot; <?= Helper::h($r['ho_ten_chu_nhiem'] ?? '-') ?>
                        &middot; Báo cáo cuối: <?= $r['lan_cuoi'] ? date('d/m/Y', strtotime($r['lan_cuoi'])) : 'Chưa có' ?></div>
                    </div>
                </div>
            <?php endforeach; ?>
            <?php if (empty($overdue)): ?><div class="empty-state">Tất cả đề tài đều có báo cáo trong 90 ngày qua</div><?php endif; ?>
        </div>
    </div>

    <div class="nk-block">
        <h3>Xu hướng 5 năm</h3>
        <?php $maxTr = nckh_max_so_luong($trend); ?>
        <div class="nk-trend">
            <?php foreach ($trend as $r): $h = (int)$r['so_luong'] / $maxTr * 100; ?>
                <div class="col">
                    <div class="bar" style="height:<?= max($h, 4) ?>%"><span class="lbl"><?= (int)$r['so_luong'] ?></span></div>
                    <div class="yr"><?= (int)$r['nam'] ?></div>
                </div>
            <?php endforeach; ?>
            <?php if (empty($trend)): ?><div class="empty-state" style="width:100%">Không có dữ liệu</div><?php endif; ?>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
