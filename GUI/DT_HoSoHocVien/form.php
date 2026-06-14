<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../BUS/DT_HoSoHocVien_BUS.php';
require_once __DIR__ . '/../../BUS/DM_HocVien_BUS.php';

Helper::requireLogin();
$id = Helper::get('id', 0);
$isEdit = $id > 0;
$action = $isEdit ? PhanQuyenHelper::QUYEN_SUA : PhanQuyenHelper::QUYEN_THEM;
PhanQuyenHelper::requireQuyen('DT_HoSoHocVien', $action);

$e = null;
if ($isEdit) {
    $e = DT_HoSoHocVien_BUS::getById($id);
    if (!$e) { echo 'Không tìm thấy hồ sơ'; exit; }
}

$pageTitle = $isEdit ? 'Sửa hồ sơ học viên' : 'Thêm hồ sơ học viên';
$activeMenu = 'DT_HoSoHocVien';
require __DIR__ . '/../layouts/header.php';
?>

<div class="breadcrumb">
    <a href="<?= AppConfig::baseUrl('GUI/dashboard/index.php') ?>">Trang chủ</a>
    <span class="sep">›</span> Đào tạo
    <span class="sep">›</span> <a href="index.php">Hồ sơ học viên</a>
    <span class="sep">›</span> <span><?= htmlspecialchars($pageTitle) ?></span>
</div>

<div class="card" style="max-width:800px">
    <h3><?= htmlspecialchars($pageTitle) ?></h3>
    <form id="form" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label>Học viên <span class="required">*</span></label>
            <select name="hoc_vien_id" id="hocVienSelect" class="form-select" required>
                <option value="">-- Chọn học viên --</option>
            </select>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Loại hồ sơ <span class="required">*</span></label>
                <input type="text" name="loai_ho_so" class="form-control" list="loaiHoSoList"
                    value="<?= htmlspecialchars($e->loai_ho_so ?? '') ?>" required placeholder="VD: Chứng minh nhân dân">
                <datalist id="loaiHoSoList"></datalist>
            </div>
            <div class="form-group">
                <label>Tên hồ sơ <span class="required">*</span></label>
                <input type="text" name="ten_ho_so" class="form-control" 
                    value="<?= htmlspecialchars($e->ten_ho_so ?? '') ?>" required placeholder="VD: CMND số 123456789">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Số hiệu</label>
                <input type="text" name="so_hieu" class="form-control" 
                    value="<?= htmlspecialchars($e->so_hieu ?? '') ?>" placeholder="Số hiệu hồ sơ">
            </div>
            <div class="form-group">
                <label>Nơi cấp</label>
                <input type="text" name="noi_cap" class="form-control" 
                    value="<?= htmlspecialchars($e->noi_cap ?? '') ?>" placeholder="VD: Công an TP">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Ngày cấp</label>
                <input type="date" name="ngay_cap" class="form-control" 
                    value="<?= htmlspecialchars($e->ngay_cap ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Ngày hết hạn</label>
                <input type="date" name="ngay_het_han" class="form-control" 
                    value="<?= htmlspecialchars($e->ngay_het_han ?? '') ?>">
            </div>
        </div>

        <div class="form-group">
            <label>Tệp đính kèm</label>
            <input type="file" name="ho_so_file" class="form-control" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.gif,.zip,.rar">
            <small class="form-text">Tối đa 20MB. Cho phép: PDF, DOC, DOCX, JPG, PNG, GIF, ZIP, RAR</small>
            <?php if ($isEdit && $e->duong_dan): ?>
                <small class="d-block mt-2">File hiện tại: <strong><?= htmlspecialchars($e->duong_dan) ?></strong> (<?= DT_HoSoHocVien_BUS::formatBytes($e->kich_thuoc ?? 0) ?>)</small>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label>Trạng thái</label>
            <select name="trang_thai" class="form-select">
                <option value="1" <?= ($e->trang_thai ?? 1) == 1 ? 'selected' : '' ?>>Hoạt động</option>
                <option value="0" <?= ($e->trang_thai ?? 1) == 0 ? 'selected' : '' ?>>Ngừng hoạt động</option>
            </select>
        </div>

        <div class="form-group">
            <label>Ghi chú</label>
            <textarea name="ghi_chu" class="form-control" rows="3"><?= htmlspecialchars($e->ghi_chu ?? '') ?></textarea>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">💾 Lưu</button>
            <a href="index.php" class="btn btn-secondary">❌ Hủy</a>
        </div>
    </form>
</div>

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

// Load loại hồ sơ
fetch('ajax_handler.php', {
    method: 'POST',
    headers: window.CSRF_TOKEN ? {'X-CSRF-Token': window.CSRF_TOKEN} : {},
    body: new FormData((() => {
        const fd = new FormData();
        fd.append('action', 'getComboLoai');
        return fd;
    })())
})
.then(r => r.json())
.then(d => {
    const dl = document.getElementById('loaiHoSoList');
    if (d.data && Array.isArray(d.data)) {
        d.data.forEach(loai => {
            const opt = document.createElement('option');
            opt.value = loai;
            dl.appendChild(opt);
        });
    }
});
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
