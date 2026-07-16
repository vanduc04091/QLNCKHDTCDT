-- ============================================================
-- Đính kèm file minh chứng (chứng chỉ PDF) cho bản ghi CME
-- Chạy trên DB ql_nckh_dt_cdt.
-- ============================================================
ALTER TABLE dt_cme_ghi_nhan
  ADD COLUMN minh_chung      VARCHAR(255) NULL COMMENT 'Tên file đã lưu trên server (assets/uploads/cme/)' AFTER ghi_chu,
  ADD COLUMN minh_chung_goc  VARCHAR(255) NULL COMMENT 'Tên file gốc khi upload'                          AFTER minh_chung,
  ADD COLUMN minh_chung_size INT          NULL COMMENT 'Dung lượng file (byte)'                           AFTER minh_chung_goc;
