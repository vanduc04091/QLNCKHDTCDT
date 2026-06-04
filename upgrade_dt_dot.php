<?php
/**
 * Migration: Đợt đăng ký khóa học (Đào tạo)
 *  - dt_dot_dang_ky
 *  - dt_dot_giai_doan
 *  - dt_khoa_hoc.dot_dang_ky_id
 *  - Seed form + quyền
 */
require_once __DIR__ . '/bootstrap.php';

$pdo = Database::getConnection();
echo "=== Migration: Đợt đăng ký khóa học (Đào tạo) ===\n\n";

try {
    // 1. dt_dot_dang_ky
    $pdo->exec("CREATE TABLE IF NOT EXISTS dt_dot_dang_ky (
        id INT AUTO_INCREMENT PRIMARY KEY,
        ten_dot VARCHAR(255) NOT NULL,
        nam SMALLINT NOT NULL,
        tu_ngay DATE NOT NULL,
        den_ngay DATE NOT NULL,
        mo_ta TEXT DEFAULT NULL,
        trang_thai TINYINT NOT NULL DEFAULT 1 COMMENT '1=HoatDong, 0=Khoa',
        ngay_tao DATETIME DEFAULT CURRENT_TIMESTAMP,
        ngay_cap_nhat DATETIME DEFAULT CURRENT_TIMESTAMP,
        nguoi_tao INT DEFAULT NULL,
        nguoi_cap_nhat INT DEFAULT NULL,
        da_xoa INT NOT NULL DEFAULT 0,
        KEY idx_dtdot_nam (nam, da_xoa),
        KEY idx_dtdot_tt (trang_thai, da_xoa),
        KEY idx_dtdot_tg (tu_ngay, den_ngay)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    echo "[OK] Table dt_dot_dang_ky\n";

    // 2. dt_dot_giai_doan (chỉ Submit + Review)
    $pdo->exec("CREATE TABLE IF NOT EXISTS dt_dot_giai_doan (
        id INT AUTO_INCREMENT PRIMARY KEY,
        dot_id INT NOT NULL,
        ten_giai_doan VARCHAR(255) NOT NULL,
        hanh_vi ENUM('Submit','Review') NOT NULL DEFAULT 'Submit',
        tu_ngay DATETIME NOT NULL,
        den_ngay DATETIME NOT NULL,
        thu_tu INT NOT NULL DEFAULT 0,
        ghi_chu VARCHAR(500) DEFAULT NULL,
        ngay_tao DATETIME DEFAULT CURRENT_TIMESTAMP,
        ngay_cap_nhat DATETIME DEFAULT CURRENT_TIMESTAMP,
        nguoi_tao INT DEFAULT NULL,
        nguoi_cap_nhat INT DEFAULT NULL,
        da_xoa INT NOT NULL DEFAULT 0,
        KEY idx_dtgd_dot (dot_id, da_xoa),
        KEY idx_dtgd_hv (hanh_vi, da_xoa),
        KEY idx_dtgd_tg (tu_ngay, den_ngay),
        CONSTRAINT FK_DTGD_Dot FOREIGN KEY (dot_id) REFERENCES dt_dot_dang_ky(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    echo "[OK] Table dt_dot_giai_doan\n";

    // 3. dt_khoa_hoc.dot_dang_ky_id
    $stmt = $pdo->query("SHOW COLUMNS FROM dt_khoa_hoc LIKE 'dot_dang_ky_id'");
    if (!$stmt->fetch()) {
        $pdo->exec("ALTER TABLE dt_khoa_hoc
            ADD COLUMN dot_dang_ky_id INT DEFAULT NULL AFTER doi_tuong_hoc_vien_id,
            ADD KEY idx_KH_dot (dot_dang_ky_id, da_xoa),
            ADD CONSTRAINT FK_KH_Dot FOREIGN KEY (dot_dang_ky_id) REFERENCES dt_dot_dang_ky(id)");
        echo "[OK] Added dt_khoa_hoc.dot_dang_ky_id\n";
    } else {
        echo "[SKIP] dt_khoa_hoc.dot_dang_ky_id existed\n";
    }

    // 4. Seed form DT_DotDangKy
    $stmt = $pdo->prepare("SELECT id FROM dm_danh_sach_form WHERE modules_tuong_ung = :k");
    $stmt->execute([':k' => 'DT_DotDangKy']);
    $formId = (int)$stmt->fetchColumn();
    if (!$formId) {
        $pdo->exec("INSERT INTO dm_danh_sach_form (modules_tuong_ung, ten_form, ngay_tao, ngay_cap_nhat, nguoi_tao, nguoi_cap_nhat, da_xoa)
                    VALUES ('DT_DotDangKy', 'Đợt đăng ký khóa học', NOW(), NOW(), 0, 0, 0)");
        $formId = (int)$pdo->lastInsertId();
        echo "[OK] Seed form DT_DotDangKy id={$formId}\n";
    } else {
        echo "[SKIP] Form DT_DotDangKy existed (id={$formId})\n";
    }

    // 5. Cấp quyền: admin CRUD + duyệt, các nhóm khác chỉ xem
    $nhoms = $pdo->query("SELECT id, la_admin FROM dm_nhom_tai_khoan WHERE da_xoa = 0")->fetchAll();
    foreach ($nhoms as $n) {
        $exists = $pdo->prepare("SELECT id FROM dm_phan_quyen WHERE nhom_tai_khoan_id=:n AND danh_sach_form_id=:f");
        $exists->execute([':n' => $n['id'], ':f' => $formId]);
        if (!$exists->fetchColumn()) {
            $a = (int)$n['la_admin'] === 1 ? 1 : 0;
            $pdo->prepare("INSERT INTO dm_phan_quyen (nhom_tai_khoan_id, danh_sach_form_id, quyen_xem, quyen_them, quyen_sua, quyen_xoa, quyen_duyet, ngay_tao, nguoi_tao)
                           VALUES (:n, :f, :v, :a1, :a2, :a3, :a4, NOW(), 0)")
                ->execute([':n' => $n['id'], ':f' => $formId, ':v' => 1, ':a1' => $a, ':a2' => $a, ':a3' => $a, ':a4' => $a]);
        }
    }
    echo "[OK] Seed quyền DT_DotDangKy cho " . count($nhoms) . " nhóm\n";

    MemcachedHelper::deleteByPrefix('phan_quyen:');
    echo "\n=== HOÀN TẤT ===\n";
} catch (Throwable $ex) {
    echo "[FAIL] " . $ex->getMessage() . "\n";
    exit(1);
}
