-- =====================================================================
-- DỌN cột chết trên dt_mon_hoc sau khi chuyển Bài học <-> CTĐT sang N:N.
-- Quan hệ + thứ tự giờ nằm hoàn toàn ở bảng nối dt_chuong_trinh_mon_hoc.
-- Cột chuong_trinh_id / thu_tu trên dt_mon_hoc KHÔNG còn code nào dùng.
--
-- An toàn: chỉ chạy khi đã backfill bảng nối xong
-- (xem docs/sql_baihoc_ctdt_nhieu_nhieu.sql).
-- Nếu muốn giữ để rollback thì BỎ QUA file này — hệ thống vẫn chạy đúng.
-- =====================================================================

-- 1) Bỏ index thừa của cột chết (nếu còn)
ALTER TABLE dt_mon_hoc DROP INDEX idx_MH_ct;

-- 2) Bỏ 2 cột chết
ALTER TABLE dt_mon_hoc DROP COLUMN chuong_trinh_id;
ALTER TABLE dt_mon_hoc DROP COLUMN thu_tu;
