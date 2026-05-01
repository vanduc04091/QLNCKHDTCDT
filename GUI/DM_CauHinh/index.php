<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../BUS/DM_CauHinh_BUS.php';

Helper::requireLogin();
if (!PhanQuyenHelper::hasQuyen('DM_CauHinh', PhanQuyenHelper::QUYEN_XEM)) {
    echo 'Bạn không có quyền truy cập chức năng này.'; exit;
}
$canEdit = PhanQuyenHelper::hasQuyen('DM_CauHinh', PhanQuyenHelper::QUYEN_SUA);

$schema = DM_CauHinh_BUS::schema();
$values = DM_CauHinh_BUS::getAllForUi();

$pageTitle  = 'Cấu hình hệ thống';
$activeMenu = 'DM_CauHinh';
require __DIR__ . '/../layouts/header.php';
?>
<div class="breadcrumb">
    <a href="<?= AppConfig::baseUrl('GUI/dashboard/index.php') ?>">Trang chủ</a>
    <span class="sep">›</span> Hệ thống
    <span class="sep">›</span> <span>Cấu hình</span>
</div>

<div class="ch-layout">
    <!-- Sidebar tabs -->
    <aside class="ch-side">
        <div class="ch-side-head">Nhóm cấu hình</div>
        <ul class="ch-tabs" id="chTabs">
            <?php $first = true; foreach ($schema as $gKey => $g): ?>
                <li class="ch-tab <?= $first ? 'active' : '' ?>" data-tab="<?= htmlspecialchars($gKey) ?>">
                    <span class="ch-tab-label"><?= htmlspecialchars($g['label']) ?></span>
                </li>
            <?php $first = false; endforeach; ?>
        </ul>
    </aside>

    <!-- Main content -->
    <section class="ch-main">
        <form id="formCauHinh" autocomplete="off">
            <?php $first = true; foreach ($schema as $gKey => $g): ?>
                <div class="ch-pane <?= $first ? 'active' : '' ?>" data-pane="<?= htmlspecialchars($gKey) ?>">
                    <div class="ch-pane-head">
                        <h3><?= htmlspecialchars($g['label']) ?></h3>
                        <?php if (!empty($g['desc'])): ?>
                            <p class="text-muted" style="margin:4px 0 0;font-size:13px"><?= htmlspecialchars($g['desc']) ?></p>
                        <?php endif; ?>
                    </div>

                    <div class="ch-fields">
                        <?php foreach ($g['fields'] as $fKey => $f): $val = $values[$fKey] ?? ''; ?>
                            <div class="ch-field">
                                <label class="ch-field-label"><?= htmlspecialchars($f['label']) ?>
                                    <code class="ch-field-key"><?= htmlspecialchars($fKey) ?></code>
                                </label>
                                <div class="ch-field-input">
                                    <?php if ($f['type'] === 'toggle'): ?>
                                        <label class="ch-toggle">
                                            <input type="checkbox" name="<?= htmlspecialchars($fKey) ?>" value="1" <?= ((string)$val === '1') ? 'checked' : '' ?> <?= $canEdit ? '' : 'disabled' ?>>
                                            <span class="ch-toggle-slider"></span>
                                            <span class="ch-toggle-text"><?= ((string)$val === '1') ? 'Bật' : 'Tắt' ?></span>
                                        </label>
                                    <?php elseif ($f['type'] === 'select'): ?>
                                        <select name="<?= htmlspecialchars($fKey) ?>" class="form-select" <?= $canEdit ? '' : 'disabled' ?>>
                                            <?php foreach ($f['options'] as $oVal => $oLabel): ?>
                                                <option value="<?= htmlspecialchars($oVal) ?>" <?= (string)$val === (string)$oVal ? 'selected' : '' ?>><?= htmlspecialchars($oLabel) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    <?php elseif ($f['type'] === 'password'): ?>
                                        <div class="ch-pwd-wrap">
                                            <input type="password" name="<?= htmlspecialchars($fKey) ?>" class="form-control"
                                                   placeholder="<?= ((string)$val !== '' && strpos((string)$val, '••') !== false) ? '(Đã có giá trị — để trống nếu không đổi)' : 'Nhập giá trị' ?>"
                                                   <?= $canEdit ? '' : 'disabled' ?>>
                                            <button type="button" class="ch-pwd-toggle" onclick="togglePwd(this)" tabindex="-1">Hiện</button>
                                        </div>
                                    <?php elseif ($f['type'] === 'textarea'): ?>
                                        <textarea name="<?= htmlspecialchars($fKey) ?>" class="form-control" rows="3" <?= $canEdit ? '' : 'disabled' ?>><?= htmlspecialchars($val) ?></textarea>
                                    <?php else: ?>
                                        <input type="<?= $f['type'] === 'number' ? 'number' : 'text' ?>"
                                               name="<?= htmlspecialchars($fKey) ?>" class="form-control"
                                               value="<?= htmlspecialchars($val) ?>"
                                               placeholder="<?= htmlspecialchars($f['placeholder'] ?? '') ?>"
                                               <?= $canEdit ? '' : 'disabled' ?>>
                                    <?php endif; ?>
                                    <?php if (!empty($f['help'])): ?>
                                        <div class="ch-field-help"><?= htmlspecialchars($f['help']) ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <?php if ($gKey === 'smtp'): ?>
                        <div class="ch-test-box">
                            <h4 style="margin:0 0 8px">Gửi mail kiểm tra</h4>
                            <p class="text-muted" style="font-size:12.5px;margin:0 0 10px">Lưu cấu hình rồi gửi mail thử để xác nhận SMTP hoạt động.</p>
                            <div style="display:flex;gap:8px;flex-wrap:wrap">
                                <input type="email" id="testEmail" class="form-control" placeholder="Email nhận (vd: admin@bv.com)" style="flex:1;min-width:240px" <?= $canEdit ? '' : 'disabled' ?>>
                                <button type="button" class="btn btn-primary" id="btnTestMail" onclick="testMail()" <?= $canEdit ? '' : 'disabled' ?>>Gửi mail test</button>
                            </div>
                            <div id="testResult" style="margin-top:10px;font-size:13px"></div>
                        </div>
                    <?php endif; ?>
                </div>
            <?php $first = false; endforeach; ?>

            <?php if ($canEdit): ?>
                <div class="ch-actions">
                    <button type="button" class="btn" onclick="window.location.reload()">Hủy thay đổi</button>
                    <button type="submit" class="btn btn-primary" id="btnSave">Lưu cấu hình</button>
                </div>
            <?php else: ?>
                <div class="ch-actions" style="color:var(--gray-500);font-size:13px">
                    Bạn không có quyền sửa cấu hình. Liên hệ quản trị viên.
                </div>
            <?php endif; ?>
        </form>
    </section>
</div>

<style>
    .ch-layout { display: grid; grid-template-columns: 240px 1fr; gap: 16px; align-items: stretch; min-height: calc(100vh - 220px); }
    .ch-side { background:#fff; border:1px solid var(--gray-200); border-radius:10px; padding: 12px 0; }
    .ch-side-head { padding: 8px 16px 12px; font-size: 12px; font-weight:700; color: var(--gray-500); text-transform: uppercase; letter-spacing: .5px; border-bottom: 1px solid var(--gray-100); }
    .ch-tabs { list-style:none; padding:6px 0; margin:0; }
    .ch-tab { padding: 11px 16px; cursor:pointer; font-size: 13.5px; font-weight: 600; color: var(--gray-700); border-left: 3px solid transparent; transition: background .12s ease; }
    .ch-tab:hover { background: var(--gray-50); }
    .ch-tab.active { background: #eff6ff; border-left-color: var(--primary); color: var(--primary); }

    .ch-main { background:#fff; border:1px solid var(--gray-200); border-radius:10px; padding: 22px 26px; }
    .ch-pane { display: none; }
    .ch-pane.active { display: block; }
    .ch-pane-head { margin-bottom: 18px; padding-bottom: 14px; border-bottom: 1px solid var(--gray-100); }
    .ch-pane-head h3 { margin: 0; font-size: 17px; color: var(--gray-800); }

    .ch-fields { display: flex; flex-direction: column; gap: 14px; }
    .ch-field { display: grid; grid-template-columns: 220px 1fr; gap: 14px; align-items: start; padding: 8px 0; }
    .ch-field-label { font-weight: 600; font-size: 13.5px; color: var(--gray-700); padding-top: 7px; }
    .ch-field-key { display:block; font-family: 'Consolas', monospace; font-size: 11px; color: var(--gray-400); font-weight: 400; margin-top: 2px; background: transparent; padding: 0; }
    .ch-field-input { min-width: 0; }
    .ch-field-input .form-control, .ch-field-input .form-select { max-width: 420px; }
    .ch-field-help { font-size: 12px; color: var(--gray-500); margin-top: 5px; max-width: 480px; }

    .ch-toggle { display: inline-flex; align-items: center; gap: 10px; cursor: pointer; }
    .ch-toggle input { display: none; }
    .ch-toggle-slider { position: relative; width: 40px; height: 22px; background: var(--gray-300); border-radius: 11px; transition: background .15s ease; }
    .ch-toggle-slider::after { content: ''; position: absolute; left: 2px; top: 2px; width: 18px; height: 18px; background: #fff; border-radius: 50%; transition: transform .15s ease; }
    .ch-toggle input:checked + .ch-toggle-slider { background: #16a34a; }
    .ch-toggle input:checked + .ch-toggle-slider::after { transform: translateX(18px); }
    .ch-toggle-text { font-size: 13px; color: var(--gray-700); }

    .ch-pwd-wrap { display: flex; gap: 6px; align-items: center; max-width: 420px; }
    .ch-pwd-wrap input { flex: 1; }
    .ch-pwd-toggle { padding: 6px 10px; background: var(--gray-100); border: 1px solid var(--gray-300); border-radius: 6px; font-size: 12px; cursor: pointer; }
    .ch-pwd-toggle:hover { background: var(--gray-200); }

    .ch-test-box { margin-top: 22px; padding: 16px; background: #fef3c7; border-left: 3px solid #ca8a04; border-radius: 0 8px 8px 0; }
    .ch-test-box h4 { color: #92400e; }

    .ch-actions { margin-top: 24px; padding-top: 18px; border-top: 1px solid var(--gray-100); display: flex; gap: 10px; justify-content: flex-end; }

    @media (max-width: 800px) {
        .ch-layout { grid-template-columns: 1fr; }
        .ch-side { min-height: auto; }
        .ch-tabs { display: flex; overflow-x: auto; padding: 0; }
        .ch-tab { border-left: none; border-bottom: 3px solid transparent; white-space: nowrap; }
        .ch-tab.active { border-bottom-color: var(--primary); border-left: none; }
        .ch-field { grid-template-columns: 1fr; gap: 4px; }
        .ch-field-label { padding-top: 0; }
    }
</style>

<script>
var URL_AJAX = APP_BASE + 'GUI/DM_CauHinh/ajax_handler.php';
var CAN_EDIT = <?= $canEdit ? 'true' : 'false' ?>;

// Tabs
$(document).on('click', '.ch-tab', function(){
    var t = $(this).data('tab');
    $('.ch-tab').removeClass('active');
    $(this).addClass('active');
    $('.ch-pane').removeClass('active');
    $('.ch-pane[data-pane="'+t+'"]').addClass('active');
});

// Toggle text live
$(document).on('change', '.ch-toggle input[type="checkbox"]', function(){
    $(this).siblings('.ch-toggle-text').text(this.checked ? 'Bật' : 'Tắt');
});

function togglePwd(btn){
    var inp = btn.previousElementSibling;
    if (inp.type === 'password'){ inp.type = 'text'; btn.textContent = 'Ẩn'; }
    else { inp.type = 'password'; btn.textContent = 'Hiện'; }
}

// Save
$('#formCauHinh').on('submit', function(e){
    e.preventDefault();
    if (!CAN_EDIT) return;
    var fd = new FormData(this);
    fd.append('action', 'save');
    // Đảm bảo các checkbox không tick gửi giá trị '0'
    $('input[type="checkbox"]', this).each(function(){
        if (!this.checked && !fd.has(this.name)) fd.append(this.name, '0');
    });
    var $btn = $('#btnSave').prop('disabled', true).text('Đang lưu...');
    $.ajax({ url: URL_AJAX, type:'POST', data: fd, processData:false, contentType:false, dataType:'json' })
        .done(function(res){
            $btn.prop('disabled', false).text('Lưu cấu hình');
            if (res.success){ APP.toast(res.message, 'success'); }
            else APP.toast(res.message || 'Lỗi', 'error');
        })
        .fail(function(xhr){
            $btn.prop('disabled', false).text('Lưu cấu hình');
            APP.toast('Lỗi kết nối (HTTP '+(xhr.status||'?')+')', 'error');
        });
});

// Test mail
function testMail(){
    var email = $('#testEmail').val().trim();
    if (!email){ APP.toast('Nhập email nhận', 'error'); $('#testEmail').focus(); return; }
    var $btn = $('#btnTestMail').prop('disabled', true).text('Đang gửi...');
    $('#testResult').html('');
    APP.ajax(URL_AJAX, { action:'testMail', email: email }).done(function(res){
        $btn.prop('disabled', false).text('Gửi mail test');
        var cls = res.success ? 'color:#15803d;background:#dcfce7' : 'color:#991b1b;background:#fee2e2';
        $('#testResult').html('<div style="padding:8px 12px;border-radius:6px;'+cls+'">'+APP.escape(res.message)+'</div>');
    }).fail(function(){
        $btn.prop('disabled', false).text('Gửi mail test');
        $('#testResult').html('<div style="padding:8px 12px;border-radius:6px;color:#991b1b;background:#fee2e2">Lỗi kết nối</div>');
    });
}
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
