-- ============================================================
-- Tách quyền cho mục "Đào tạo" (dashboard tổng quan đào tạo) khỏi quyền `Dashboard`
-- để nhóm chỉ dùng CME không nhìn thấy mục này.
-- Chạy trên DB ql_nckh_dt_cdt.
-- ============================================================

-- 1) Khai báo form mới
INSERT INTO dm_danh_sach_form (modules_tuong_ung, ten_form, ngay_tao, ngay_cap_nhat, da_xoa)
SELECT 'DT_Dashboard', 'Tổng quan đào tạo', NOW(), NOW(), 0
WHERE NOT EXISTS (SELECT 1 FROM dm_danh_sach_form WHERE modules_tuong_ung = 'DT_Dashboard');

-- 2) Cấp quyền XEM cho các nhóm ĐANG có quyền xem "Báo cáo đào tạo" (DT_BaoCao)
--    => giữ nguyên trải nghiệm cho các nhóm làm Đào tạo. Nhóm CME sẽ KHÔNG được cấp.
INSERT INTO dm_phan_quyen (nhom_tai_khoan_id, danh_sach_form_id, quyen_xem, quyen_them, quyen_sua, quyen_xoa, quyen_duyet, ngay_tao, ngay_cap_nhat)
SELECT pq.nhom_tai_khoan_id,
       (SELECT id FROM dm_danh_sach_form WHERE modules_tuong_ung = 'DT_Dashboard' LIMIT 1),
       1, 0, 0, 0, 0, NOW(), NOW()
FROM dm_phan_quyen pq
JOIN dm_danh_sach_form f ON f.id = pq.danh_sach_form_id
WHERE f.modules_tuong_ung = 'DT_BaoCao' AND pq.quyen_xem = 1
  AND NOT EXISTS (
      SELECT 1 FROM dm_phan_quyen p2
      JOIN dm_danh_sach_form f2 ON f2.id = p2.danh_sach_form_id
      WHERE f2.modules_tuong_ung = 'DT_Dashboard' AND p2.nhom_tai_khoan_id = pq.nhom_tai_khoan_id
  );

-- Sau khi chạy: vào Hệ thống → Xóa cache (hoặc màn Phân quyền) để làm mới cache phân quyền.
-- Muốn nhóm nào xem mục "Đào tạo" thì tích quyền Xem cho form "Tổng quan đào tạo" ở màn Phân quyền.
