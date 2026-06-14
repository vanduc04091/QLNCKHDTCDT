<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../BUS/DT_ChungChi_BUS.php';
require_once __DIR__ . '/../../BUS/DM_HocVien_BUS.php';
require_once __DIR__ . '/../../BUS/DT_KhoaHocChuongTrinh_BUS.php';

Helper::requireLogin();
$id = Helper::get('id', 0);
$isEdit = $id > 0;
$action = $isEdit ? PhanQuyenHelper::QUYEN_SUA : PhanQuyenHelper::QUYEN_THEM;
PhanQuyenHelper::requireQuyen('DT_ChungChi', $action);

$e = null;
if ($isEdit) {
    $e = DT_ChungChi_BUS::getById($id);
    if (!$e) { echo 'Không tìm thấy chứng chỉ'; exit; }
}

$pageTitle = $isEdit ? 'Sửa chứng chỉ' : 'Thêm chứng chỉ';
$activeMenu = 'DT_ChungChi';
require __DIR__ . '/../layouts/header.php';
?>

<div class="breadcrumb">
    <a href="<?= AppConfig::baseUrl('GUI/dashboard/index.php') ?>">Trang chủ</a>
    <span class="sep">›</span> Đào tạo
    <span class="sep">›</span> <a href="index.php">Chứng chỉ</a>
    <span class="sep">›</span> <span><?= htmlspecialchars($pageTitle) ?></span>
</div>

<div class="card" style="max-width:900px">
    <h3><?= htmlspecialchars($pageTitle) ?></h3>
    <form id="form" method="POST" enctype="multipart/form-data">
        <fieldset>
            <legend>Thông tin học viên</legend>
            <div class="form-row">
                <div class="form-group">
                    <label>Học viên <span class="required">*</span></label>
                    <select name="hoc_vien_id" id="hocVienSelect" class="form-select" required>
                        <option value="">-- Chọn học viên --</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Lớp học <span class="required">*</span></label>
                    <select name="lop_hoc_id" id="lopHocSelect" class="form-select" required>
                        <option value="">-- Chọn lớp học --</option>
                    </select>
                </div>
            </div>
        </fieldset>

        <fieldset>
            <legend>Thông tin chứng chỉ</legend>
            <div class="form-row">
                <div class="form-group">
                    <label>Số chứng chỉ <span class="required">*</span></label>
                    <input type="text" name="so_chung_chi" class="form-control" 
                        value="<?= htmlspecialchars($e->so_chung_chi ?? '') ?>" required placeholder="VD: CC-2024-001">
                </div>
                <div class="form-group">
                    <label>Tên chứng chỉ <span class="required">*</span></label>
                    <input type="text" name="ten_chung_chi" class="form-control" 
                        value="<?= htmlspecialchars($e->ten_chung_chi ?? '') ?>" required placeholder="VD: Chứng chỉ hoàn thành khóa học">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Loại chứng chỉ</label>
                    <input type="text" name="loai_chung_chi" class="form-control" 
                        value="<?= htmlspecialchars($e->loai_chung_chi ?? 'Chứng chỉ') ?>" placeholder="VD: Chứng chỉ">
                </div>
                <div class="form-group">
                    <label>Xếp loại tốt nghiệp</label>
                    <select name="xep_loai_tot_nghiep" class="form-select">
                        <option value="">-- Chọn --</option>
                        <option value="Xuất sắc" <?= ($e->xep_loai_tot_nghiep ?? '') === 'Xuất sắc' ? 'selected' : '' ?>>Xuất sắc</option>
                        <option value="Giỏi" <?= ($e->xep_loai_tot_nghiep ?? '') === 'Giỏi' ? 'selected' : '' ?>>Giỏi</option>
                        <option value="Khá" <?= ($e->xep_loai_tot_nghiep ?? '') === 'Khá' ? 'selected' : '' ?>>Khá</option>
                        <option value="Trung bình" <?= ($e->xep_loai_tot_nghiep ?? '') === 'Trung bình' ? 'selected' : '' ?>>Trung bình</option>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Điểm trung bình</label>
                    <input type="number" name="diem_trung_binh" class="form-control" step="0.1" min="0" max="10"
                        value="<?= htmlspecialchars($e->diem_trung_binh ?? '') ?>" placeholder="VD: 8.5">
                </div>
                <div class="form-group">
                    <label>Ngày cấp <span class="required">*</span></label>
                    <input type="date" name="ngay_cap" class="form-control" 
                        value="<?= htmlspecialchars($e->ngay_cap ?? '') ?>" required>
                </div>
            </div>

            <div class="form-group">
                <label>Ngày hết hạn</label>
                <input type="date" name="ngay_het_han" class="form-control" 
                    value="<?= htmlspecialchars($e->ngay_het_han ?? '') ?>">
            </div>
        </fieldset>

        <fieldset>
            <legend>Thông tin cấp chứng chỉ</legend>
            <div class="form-row">
                <div class="form-group">
                    <label>Người ký</label>
                    <input type="text" name="nguoi_ky" class="form-control" 
                        value="<?= htmlspecialchars($e->nguoi_ky ?? '') ?>" placeholder="VD: Thầy Nguyễn Văn A">
                </div>
                <div class="form-group">
                    <label>Chức vụ người ký</label>
                    <input type="text" name="chuc_vu_nguoi_ky" class="form-control" 
                        value="<?= htmlspecialchars($e->chuc_vu_nguoi_ky ?? '') ?>" placeholder="VD: Giáo dục viên">
                </div>
            </div>

            <div class="form-group">
                <label>Nơi cấp</label>
                <input type="text" name="noi_cap" class="form-control" 
                    value="<?= htmlspecialchars($e->noi_cap ?? '') ?>" placeholder="VD: Trường đại học ABC">
            </div>

            <div class="form-group">
                <label>Tệp chứng chỉ (PDF/JPG)</label>
                <input type="file" name="chung_chi_file" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                <small class="form-text">Tối đa 20MB. Cho phép: PDF, JPG, PNG</small>
                <?php if ($isEdit && $e->duong_dan_file): ?>
                    <small class="d-block mt-2">File hiện tại: <strong><?= htmlspecialchars($e->duong_dan_file) ?></strong></small>
                <?php endif; ?>
            </div>
        </fieldset>

        <fieldset>
            <legend>Quản lý</legend>
            <div class="form-row">
                <div class="form-group">
                    <label>Trạng thái</label>
                    <select name="trang_thai" class="form-select">
                        <option value="0" <?= ($e->trang_thai ?? 0) == 0 ? 'selected' : '' ?>>Nháp</option>
                        <option value="1" <?= ($e->trang_thai ?? 0) == 1 ? 'selected' : '' ?>>Đã cấp</option>
                        <option value="2" <?= ($e->trang_thai ?? 0) == 2 ? 'selected' : '' ?>>Thu hồi</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label>Ghi chú</label>
                <textarea name="ghi_chu" class="form-control" rows="3"><?= htmlspecialchars($e->ghi_chu ?? '') ?></textarea>
            </div>
        </fieldset>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">💾 Lưu</button>
            <a href="index.php" class="btn btn-secondary">❌ Hủy</a>
        </div>
    </form>
</div>

<style>
fieldset { border: 1px solid #ddd; border-radius: 4px; padding: 15px; margin: 15px 0; }
legend { padding: 0 10px; font-weight: bold; color: #333; }
</style>

<script>
document.getElementById('form').addEventListener('submit', async function(e) {
    e.preventDefault();
    const fd = new FormData(this);
    fd.append('action', '<?= $isEdit ? "update" : "insert" ?>');
    <?php if ($isEdit): ?>
    fd.append('id', <?= $id ?>);
    <?php endif; ?>
    
    APP.showLoading();
    try {
        const res = await fetch('ajax_handler.php', { method: 'POST', body: fd, headers: window.CSRF_TOKEN ? {'X-CSRF-Token': window.CSRF_TOKEN} : {} });
        const data = await res.json();
        APP.hideLoading();
        alert(data.message);
        if (data.success) window.location.href = 'index.php';
    } catch(e) {
        APP.hideLoading();
        alert('Lỗi: ' + e.message);
    }
});

// Load học viên
fetch('ajax_handler.php', {
    method: 'POST',
    headers: window.CSRF_TOKEN ? {'X-CSRF-Token': window.CSRF_TOKEN} : {},
    body: new FormData((() => {
        const fd = new FormData();
        fd.append('action', 'getComboHocVien');
        return fd;
    })())
})
.then(r => r.json())
.then(d => {
    const sel = document.getElementById('hocVienSelect');
    if (d.data && Array.isArray(d.data)) {
        d.data.forEach(hv => {
            const opt = document.createElement('option');
            opt.value = hv.id;
            opt.textContent = `${hv.ma_hoc_vien} - ${hv.ho_ten}`;
            if (<?= $id ?> > 0 && hv.id === <?= $e->hoc_vien_id ?? 0 ?>) opt.selected = true;
            sel.appendChild(opt);
        });
    }
});

// Load lớp học
fetch('ajax_handler.php', {
    method: 'POST',
    headers: window.CSRF_TOKEN ? {'X-CSRF-Token': window.CSRF_TOKEN} : {},
    body: new FormData((() => {
        const fd = new FormData();
        fd.append('action', 'getComboLop');
        return fd;
    })())
})
.then(r => r.json())
.then(d => {
    const sel = document.getElementById('lopHocSelect');
    if (d.data && Array.isArray(d.data)) {
        d.data.forEach(lop => {
            const opt = document.createElement('option');
            opt.value = lop.id;
            opt.textContent = lop.label || `${lop.ma_lop} - ${lop.ten_lop}`;
            if (<?= $id ?> > 0 && lop.id === <?= $e->lop_hoc_id ?? 0 ?>) opt.selected = true;
            sel.appendChild(opt);
        });
    }
});
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
