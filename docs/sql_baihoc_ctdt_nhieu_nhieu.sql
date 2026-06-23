-- =====================================================================
-- Chuyển quan hệ Bài học <-> CTĐT từ 1:N sang N:N
-- (1 bài học thuộc nhiều CTĐT; 1 CTĐT gồm nhiều bài học)
-- Dùng lại bảng nối dt_chuong_trinh_mon_hoc.
-- =====================================================================

-- 1) Backfill: với mỗi bài đang gán 1 CTĐT (dt_mon_hoc.chuong_trinh_id)
--    mà CHƯA có dòng trong bảng nối -> thêm vào, giữ thu_tu hiện có.
INSERT INTO dt_chuong_trinh_mon_hoc
    (chuong_trinh_id, mon_hoc_id, thu_tu, bat_buoc, trang_thai,
     ngay_tao, ngay_cap_nhat, nguoi_tao, nguoi_cap_nhat, da_xoa)
SELECT mh.chuong_trinh_id, mh.id, COALESCE(mh.thu_tu, 0), 1, 1,
       NOW(), NOW(), 1, 1, 0
FROM dt_mon_hoc mh
WHERE mh.chuong_trinh_id IS NOT NULL
  AND mh.da_xoa = 0
  AND NOT EXISTS (
      SELECT 1 FROM dt_chuong_trinh_mon_hoc km
      WHERE km.mon_hoc_id = mh.id
        AND km.chuong_trinh_id = mh.chuong_trinh_id
        AND km.da_xoa = 0
  );

-- 2) (Tùy chọn) Bỏ cột chuong_trinh_id và thu_tu khỏi dt_mon_hoc vì giờ
--    quan hệ + thứ tự nằm ở bảng nối. Giữ lại cũng không sao (code không
--    còn dùng), nhưng nếu muốn dọn sạch thì chạy:
-- ALTER TABLE dt_mon_hoc DROP FOREIGN KEY FK_MH_ChuongTrinh;
-- ALTER TABLE dt_mon_hoc DROP COLUMN chuong_trinh_id;
-- ALTER TABLE dt_mon_hoc DROP COLUMN thu_tu;
