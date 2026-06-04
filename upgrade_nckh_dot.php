<?php
/**
 * Migration: Đợt đăng ký đề tài NCKH
 *  - nckh_dot_dang_ky
 *  - nckh_dot_giai_doan
 *  - nckh_de_tai.dot_dang_ky_id
 *  - Seed form + quyền cho NCKH_DotDangKy
 */
require_once __DIR__ . '/bootstrap.php';

$pdo = Database::getConnection();

echo "=== Migration: Đợt đăng ký đề tài NCKH ===\n\n";

try {
    // 1. nckh_dot_dang_ky
    $pdo->exec("CREATE TABLE IF NOT EXISTS nckh_dot_dang_ky (
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
        KEY idx_dot_nam (nam, da_xoa),
        KEY idx_dot_tt (trang_thai, da_xoa),
        KEY idx_dot_thoi_gian (tu_ngay, den_ngay)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    echo "[OK] Table nckh_dot_dang_ky\n";

    // 2. nckh_dot_giai_doan
    $pdo->exec("CREATE TABLE IF NOT EXISTS nckh_dot_giai_doan (
        id INT AUTO_INCREMENT PRIMARY KEY,
        dot_id INT NOT NULL,
        ten_giai_doan VARCHAR(255) NOT NULL,
        hanh_vi ENUM('Submit','Edit','Review') NOT NULL DEFAULT 'Submit',
        tu_ngay DATETIME NOT NULL,
        den_ngay DATETIME NOT NULL,
        thu_tu INT NOT NULL DEFAULT 0,
        ghi_chu VARCHAR(500) DEFAULT NULL,
        ngay_tao DATETIME DEFAULT CURRENT_TIMESTAMP,
        ngay_cap_nhat DATETIME DEFAULT CURRENT_TIMESTAMP,
        nguoi_tao INT DEFAULT NULL,
        nguoi_cap_nhat INT DEFAULT NULL,
        da_xoa INT NOT NULL DEFAULT 0,
        KEY idx_gd_dot (dot_id, da_xoa),
        KEY idx_gd_hanh_vi (hanh_vi, da_xoa),
        KEY idx_gd_thoi_gian (tu_ngay, den_ngay),
        CONSTRAINT FK_GD_Dot FOREIGN KEY (dot_id) REFERENCES nckh_dot_dang_ky(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    echo "[OK] Table nckh_dot_giai_doan\n";

    // 3. nckh_de_tai.dot_dang_ky_id
    $stmt = $pdo->query("SHOW COLUMNS FROM nckh_de_tai LIKE 'dot_dang_ky_id'");
    if (!$stmt->fetch()) {
        $pdo->exec("ALTER TABLE nckh_de_tai
            ADD COLUMN dot_dang_ky_id INT DEFAULT NULL AFTER khoa_phong_id,
            ADD KEY idx_DT_dot (dot_dang_ky_id, da_xoa),
            ADD CONSTRAINT FK_DT_Dot FOREIGN KEY (dot_dang_ky_id) REFERENCES nckh_dot_dang_ky(id)");
        echo "[OK] Added nckh_de_tai.dot_dang_ky_id\n";
    } else {
        echo "[SKIP] nckh_de_tai.dot_dang_ky_id existed\n";
    }

    // 4. Seed form NCKH_DotDangKy
    $stmt = $pdo->prepare("SELECT id FROM dm_danh_sach_form WHERE modules_tuong_ung = :k");
    $stmt->execute([':k' => 'NCKH_DotDangKy']);
    $formId = (int)$stmt->fetchColumn();
    if (!$formId) {
        $pdo->exec("INSERT INTO dm_danh_sach_form (modules_tuong_ung, ten_form, ngay_tao, ngay_cap_nhat, nguoi_tao, nguoi_cap_nhat, da_xoa)
                    VALUES ('NCKH_DotDangKy', 'Đợt đăng ký đề tài NCKH', NOW(), NOW(), 0, 0, 0)");
        $formId = (int)$pdo->lastInsertId();
        echo "[OK] Seed form NCKH_DotDangKy id={$formId}\n";
    } else {
        echo "[SKIP] Form NCKH_DotDangKy existed (id={$formId})\n";
    }

    // 5. Cấp quyền: chỉ Admin (la_admin=1) tự động qua hasQuyen, nhưng vẫn seed quyền xem cho các nhóm có liên quan
    // Mặc định chỉ admin có CRUD, các nhóm khác chỉ xem
    $nhoms = $pdo->query("SELECT id, la_admin FROM dm_nhom_tai_khoan WHERE da_xoa = 0")->fetchAll();
    foreach ($nhoms as $n) {
        $exists = $pdo->prepare("SELECT id FROM dm_phan_quyen WHERE nhom_tai_khoan_id=:n AND danh_sach_form_id=:f");
        $exists->execute([':n' => $n['id'], ':f' => $formId]);
        if (!$exists->fetchColumn()) {
            $isAdmin = (int)$n['la_admin'] === 1 ? 1 : 0;
            $pdo->prepare("INSERT INTO dm_phan_quyen (nhom_tai_khoan_id, danh_sach_form_id, quyen_xem, quyen_them, quyen_sua, quyen_xoa, ngay_tao, nguoi_tao)
                           VALUES (:n, :f, :v, :a1, :a2, :a3, NOW(), 0)")
                ->execute([':n' => $n['id'], ':f' => $formId, ':v' => 1, ':a1' => $isAdmin, ':a2' => $isAdmin, ':a3' => $isAdmin]);
        }
    }
    echo "[OK] Seed quyền NCKH_DotDangKy cho " . count($nhoms) . " nhóm\n";

    MemcachedHelper::deleteByPrefix('phan_quyen:');

    echo "\n=== HOÀN TẤT ===\n";
} catch (Throwable $ex) {
    echo "[FAIL] " . $ex->getMessage() . "\n";
    echo $ex->getTraceAsString() . "\n";
    exit(1);
}
