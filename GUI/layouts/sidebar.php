<?php
/**
 * sidebar.php - Menu trái. $activeMenu để highlight.
 */
if (!isset($activeMenu)) $activeMenu = '';
$base = AppConfig::baseUrl('');

// Lấy ma trận phân quyền (nhóm Admin id=1 sẽ có hết)
$nhomId = SessionHelper::nhomTaiKhoanId();
$isAdmin = ($nhomId === 1);

function _canSee($moduleKey) {
    global $isAdmin;
    if ($isAdmin) return true;
    return PhanQuyenHelper::hasQuyen($moduleKey, PhanQuyenHelper::QUYEN_XEM);
}

$menu = [
    ['group' => 'Tổng quan', 'key' => 'tong-quan', 'icon' => 'dashboard', 'items' => [
        ['key' => 'NCKH_Dashboard', 'icon' => 'trending-up', 'label' => 'Nghiên cứu khoa học', 'url' => $base . 'GUI/dashboard/nckh.php', 'module' => 'NCKH_Dashboard'],
        ['key' => 'DT_Dashboard', 'icon' => 'bar-chart', 'label' => 'Đào tạo', 'url' => $base . 'GUI/dashboard/dao_tao.php', 'module' => 'DT_Dashboard'],
        ['key' => 'DT_BaoCao', 'icon' => 'clipboard-list', 'label' => 'Báo cáo đào tạo', 'url' => $base . 'GUI/BaoCao/index.php', 'module' => 'DT_BaoCao'],
        ['key' => 'DT_CME_TongQuan', 'icon' => 'trending-up', 'label' => 'Đào tạo y khoa liên tục', 'url' => $base . 'GUI/DT_CME/tong_quan.php', 'module' => 'DT_CME'],
        ['key' => 'DT_CME_BaoCao', 'icon' => 'clipboard-list', 'label' => 'Báo cáo đào tạo YKLT', 'url' => $base . 'GUI/DT_CME/bao_cao.php', 'module' => 'DT_CME'],
    ]],
    ['group' => 'Nghiên cứu khoa học', 'key' => 'nckh', 'icon' => 'star', 'items' => [
        ['key' => 'NCKH_DotDangKy',   'icon' => 'calendar',      'label' => 'Đợt đăng ký',        'url' => $base . 'GUI/NCKH_DotDangKy/index.php',   'module' => 'NCKH_DotDangKy'],
        ['key' => 'NCKH_DeTaiCuaToi', 'icon' => 'user-check',    'label' => 'Đề tài của tôi',     'url' => $base . 'GUI/NCKH_DeTaiCuaToi/index.php', 'module' => 'NCKH_DeTaiCuaToi'],
        ['key' => 'NCKH_DuyetDeTai',  'icon' => 'check',         'label' => 'Duyệt đề tài',       'url' => $base . 'GUI/NCKH_DuyetDeTai/index.php',  'module' => 'NCKH_DuyetDeTai'],
        ['key' => 'NCKH_DeTai',       'icon' => 'star',          'label' => 'Đề tài / Sáng kiến', 'url' => $base . 'GUI/NCKH_DeTai/index.php',       'module' => 'NCKH_DeTai'],
        ['key' => 'NCKH_NhacViec',    'icon' => 'alert-triangle','label' => 'Nhắc việc',          'url' => $base . 'GUI/NCKH_NhacViec/index.php',    'module' => 'NCKH_NhacViec'],
        ['key' => 'DM_NCKH_CapDo',    'icon' => 'bar-chart',     'label' => 'Cấp độ',             'url' => $base . 'GUI/DM_NCKH_CapDo/index.php',    'module' => 'DM_NCKH_CapDo'],
        ['key' => 'DM_NCKH_TheLoai',  'icon' => 'book',          'label' => 'Thể loại',           'url' => $base . 'GUI/DM_NCKH_TheLoai/index.php',  'module' => 'DM_NCKH_TheLoai'],
    ]],
    ['group' => 'Đào tạo', 'key' => 'dao-tao', 'icon' => 'academic-cap', 'items' => [
        ['key' => 'DT_DotDangKy', 'icon' => 'calendar', 'label' => 'Đợt đăng ký', 'url' => $base . 'GUI/DT_DotDangKy/index.php', 'module' => 'DT_DotDangKy'],
        ['key' => 'DT_KhoaHoc', 'icon' => 'book-open', 'label' => 'Khóa học', 'url' => $base . 'GUI/DT_KhoaHoc/index.php', 'module' => 'DT_KhoaHoc'],
        ['key' => 'DT_ChuongTrinh',  'icon' => 'users', 'label' => 'Chương trình đào tạo',  'url' => $base . 'GUI/DT_ChuongTrinh/index.php',  'module' => 'DT_ChuongTrinh'],
        ['key' => 'DT_MonHoc',  'icon' => 'book', 'label' => 'Bài học',  'url' => $base . 'GUI/DT_MonHoc/index.php',  'module' => 'DT_MonHoc'],
        ['key' => 'DT_DangKyKhoaHoc', 'icon' => 'clipboard-list', 'label' => 'Đăng ký khóa học', 'url' => $base . 'GUI/DT_DangKyKhoaHoc/index.php', 'module' => 'DT_DangKyKhoaHoc'],
        ['key' => 'DT_LichHoc', 'icon' => 'clock-history', 'label' => 'Lịch học', 'url' => $base . 'GUI/DT_LichHoc/index.php', 'module' => 'DT_LichHoc'],
        ['key' => 'DT_DiemDanh', 'icon' => 'clipboard-list', 'label' => 'Điểm danh', 'url' => $base . 'GUI/DT_DiemDanh/index.php', 'module' => 'DT_DiemDanh'],
        ['key' => 'DT_KetQuaHocTap', 'icon' => 'academic-cap', 'label' => 'Kết quả học tập', 'url' => $base . 'GUI/DT_KetQuaHocTap/index.php', 'module' => 'DT_KetQuaHocTap'],
        ['key' => 'DT_TaiLieu', 'icon' => 'book', 'label' => 'Tài liệu', 'url' => $base . 'GUI/DT_TaiLieu/index.php', 'module' => 'DT_TaiLieu'],
        ['key' => 'DT_BaiKiemTra', 'icon' => 'clipboard-list', 'label' => 'Bài kiểm tra', 'url' => $base . 'GUI/DT_BaiKiemTra/index.php', 'module' => 'DT_BaiKiemTra'],
        ['key' => 'DM_GiangVien', 'icon' => 'academic-cap', 'label' => 'Giảng viên', 'url' => $base . 'GUI/DM_GiangVien/index.php', 'module' => 'DM_GiangVien'],
        ['key' => 'DT_PhanCongGiangVien', 'icon' => 'clipboard-list', 'label' => 'Phân công giảng viên', 'url' => $base . 'GUI/DT_PhanCongGiangVien/index.php', 'module' => 'DT_PhanCongGiangVien'],
        ['key' => 'DM_HocVien', 'icon' => 'user-badge', 'label' => 'Học viên', 'url' => $base . 'GUI/DM_HocVien/index.php', 'module' => 'DM_HocVien'],
        ['key' => 'DT_HoSoHocVien', 'icon' => 'clipboard-list', 'label' => 'Hồ sơ học viên', 'url' => $base . 'GUI/DT_HoSoHocVien/index.php', 'module' => 'DT_HoSoHocVien'],
        ['key' => 'DT_ChungChi', 'icon' => 'academic-cap', 'label' => 'Chứng chỉ', 'url' => $base . 'GUI/DT_ChungChi/index.php', 'module' => 'DT_ChungChi'],
    ]],
    ['group' => 'Đào tạo y khoa liên tục', 'key' => 'cme', 'icon' => 'trending-up', 'items' => [
        ['key' => 'DT_CME', 'icon' => 'bar-chart', 'label' => 'Theo dõi tín chỉ', 'url' => $base . 'GUI/DT_CME/index.php', 'module' => 'DT_CME'],
        ['key' => 'DT_CME_DanhMuc', 'icon' => 'book', 'label' => 'Danh mục quy đổi', 'url' => $base . 'GUI/DT_CME_DanhMuc/index.php', 'module' => 'DT_CME_DanhMuc'],
    ]],
    ['group' => 'Danh mục đào tạo', 'key' => 'dm-dao-tao', 'icon' => 'presentation', 'items' => [
        ['key' => 'DM_LoaiHinhDaoTao', 'icon' => 'academic-cap', 'label' => 'Loại hình đào tạo', 'url' => $base . 'GUI/DM_LoaiHinhDaoTao/index.php', 'module' => 'DM_LoaiHinhDaoTao'],
        ['key' => 'DM_HinhThucHoc', 'icon' => 'presentation', 'label' => 'Hình thức học', 'url' => $base . 'GUI/DM_HinhThucHoc/index.php', 'module' => 'DM_HinhThucHoc'],
        ['key' => 'DM_DoiTuongHocVien', 'icon' => 'users', 'label' => 'Đối tượng học viên', 'url' => $base . 'GUI/DM_DoiTuongHocVien/index.php', 'module' => 'DM_DoiTuongHocVien'],
    ]],
    ['group' => 'Danh mục chung', 'key' => 'danh-muc', 'icon' => 'clipboard-list', 'items' => [
        ['key' => 'DM_BenhVien', 'icon' => 'hospital', 'label' => 'Bệnh viện', 'url' => $base . 'GUI/DM_BenhVien/index.php', 'module' => 'DM_BenhVien'],
        ['key' => 'DM_KhoaPhong', 'icon' => 'building-office', 'label' => 'Khoa / Phòng', 'url' => $base . 'GUI/DM_KhoaPhong/index.php', 'module' => 'DM_KhoaPhong'],
        ['key' => 'DM_NhanVien', 'icon' => 'user', 'label' => 'Nhân viên', 'url' => $base . 'GUI/DM_NhanVien/index.php', 'module' => 'DM_NhanVien'],
    ]],
    ['group' => 'Hệ thống', 'key' => 'he-thong', 'icon' => 'key', 'items' => [
        ['key' => 'DM_NguoiDung', 'icon' => 'user', 'label' => 'Người dùng', 'url' => $base . 'GUI/DM_NguoiDung/index.php', 'module' => 'DM_NguoiDung'],
        ['key' => 'DM_NhomTaiKhoan', 'icon' => 'users', 'label' => 'Nhóm tài khoản', 'url' => $base . 'GUI/DM_NhomTaiKhoan/index.php', 'module' => 'DM_NhomTaiKhoan'],
        ['key' => 'DM_PhanQuyen', 'icon' => 'key', 'label' => 'Phân quyền', 'url' => $base . 'GUI/DM_PhanQuyen/index.php', 'module' => 'DM_PhanQuyen'],
        ['key' => 'DM_DanhSachForm', 'icon' => 'clipboard-list', 'label' => 'Danh sách form', 'url' => $base . 'GUI/DM_DanhSachForm/index.php', 'module' => 'DM_DanhSachForm'],
        ['key' => 'DM_NhatKyHeThong', 'icon' => 'clock-history', 'label' => 'Nhật ký hệ thống', 'url' => $base . 'GUI/DM_NhatKyHeThong/index.php', 'module' => 'DM_NhatKyHeThong'],
        ['key' => 'DM_CauHinh', 'icon' => 'settings', 'label' => 'Cấu hình', 'url' => $base . 'GUI/DM_CauHinh/index.php', 'module' => 'DM_CauHinh'],
        ['key' => 'DM_XoaCache', 'icon' => 'refresh', 'label' => 'Xóa cache', 'url' => $base . 'GUI/DM_XoaCache/index.php', 'module' => 'DM_XoaCache'],
    ]],
];

// Xác định group nào đang chứa menu active (để mặc định mở khi tải trang có $activeMenu)
$activeGroupKey = '';
foreach ($menu as $g) {
    foreach ($g['items'] as $it) {
        if ($it['key'] === $activeMenu) { $activeGroupKey = $g['key']; break 2; }
    }
}
// Group mặc định mở khi không có active: Đào tạo + NCKH
$defaultOpenKeys = ['dao-tao', 'nckh'];
?>
<aside class="sidebar">
    <div class="sidebar-brand">
        <div class="logo">
            <img src="<?= AppConfig::baseUrl('assets/images/logo_bv.png') ?>" alt="BV HNĐK Nghệ An">
        </div>
        <div>
            <div>QL NCKH-ĐT-CĐT</div>
            <div style="font-size:11px;font-weight:400;color:#94a3b8">v<?= AppConfig::APP_VERSION ?></div>
        </div>
    </div>
    <ul class="sidebar-menu" id="sidebarMenu">
        <?php foreach ($menu as $group): ?>
            <?php
                $visible = array_values(array_filter($group['items'], function ($it) {
                    return _canSee($it['module']);
                }));
                if (empty($visible)) continue;

                // Mở mặc định: Đào tạo + NCKH; cộng thêm group đang chứa menu active
                $isOpen = in_array($group['key'], $defaultOpenKeys, true)
                       || ($activeGroupKey !== '' && $group['key'] === $activeGroupKey);
            ?>
            <li class="menu-group <?= $isOpen ? 'open' : '' ?>" data-group="<?= Helper::h($group['key']) ?>">
                <button type="button" class="menu-group-toggle" aria-expanded="<?= $isOpen?'true':'false' ?>">
                    <span class="menu-group-icon"><?= IconHelper::svg($group['icon'] ?? 'folder', 16, 'icon', 'currentColor') ?></span>
                    <span class="menu-group-label"><?= Helper::h($group['group']) ?></span>
                    <span class="menu-group-caret">
                        <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
                    </span>
                </button>
                <ul class="menu-group-items">
                    <?php foreach ($visible as $item): ?>
                        <li>
                            <a href="<?= Helper::h($item['url']) ?>" class="<?= $activeMenu === $item['key'] ? 'active' : '' ?>">
                                <?= isset($item['icon']) ? IconHelper::svg($item['icon'], 18, 'icon', 'currentColor') : '' ?>
                                <?= $item['label'] ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </li>
        <?php endforeach; ?>
    </ul>
</aside>
<script>
(function(){
    var menu = document.getElementById('sidebarMenu');
    if (!menu) return;
    menu.addEventListener('click', function(e){
        var btn = e.target.closest('.menu-group-toggle');
        if (!btn) return;
        var grp = btn.parentElement;
        // Toggle group (không đóng các nhóm khác)
        var willOpen = !grp.classList.contains('open');
        grp.classList.toggle('open', willOpen);
        btn.setAttribute('aria-expanded', willOpen ? 'true' : 'false');
    });
})();
</script>
