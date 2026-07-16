-- ============================================================
-- Thêm cột chứng chỉ hành nghề cho dm_nhan_vien + XÓA SẠCH dữ liệu cũ
-- để nạp lại theo "Danh sách người hành nghề toàn viện chốt 16.7.2026".
-- CHẠY TRÊN DB ql_nckh_dt_cdt (phpMyAdmin).
--
-- ⚠️ CẢNH BÁO: phần 2 XÓA TOÀN BỘ nhân viên cũ + dữ liệu CME + gỡ liên kết
--    giảng viên/học viên. Hãy BACKUP DB trước khi chạy (mysqldump).
-- ============================================================

-- ---------- PHẦN 1: Thêm cột CCHN (an toàn, chạy trước) ----------
ALTER TABLE dm_nhan_vien
  ADD COLUMN pham_vi_hanh_nghe   VARCHAR(300) NULL COMMENT 'Phạm vi hành nghề'            AFTER chuyen_khoa,
  ADD COLUMN so_cchn             VARCHAR(50)  NULL COMMENT 'Số chứng chỉ hành nghề'       AFTER pham_vi_hanh_nghe,
  ADD COLUMN ngay_cap_cchn       DATE         NULL COMMENT 'Ngày cấp CCHN'                AFTER so_cchn,
  ADD COLUMN qd_bo_sung_pham_vi  VARCHAR(300) NULL COMMENT 'Quyết định bổ sung phạm vi'   AFTER ngay_cap_cchn,
  ADD COLUMN dieu_chinh_pham_vi  VARCHAR(300) NULL COMMENT 'Điều chỉnh phạm vi HĐCM trong CCHN' AFTER qd_bo_sung_pham_vi,
  ADD COLUMN ngay_dieu_chinh     DATE         NULL COMMENT 'Ngày điều chỉnh phạm vi'      AFTER dieu_chinh_pham_vi,
  ADD COLUMN chuyen_khoa_cap_nhat VARCHAR(300) NULL COMMENT 'Chuyên khoa cần cập nhật KTYK liên tục' AFTER ngay_dieu_chinh,
  ADD COLUMN khoa_phong_text     VARCHAR(200) NULL COMMENT 'Tên khoa/phòng gốc từ file import (khi chưa map được khoa_phong_id)' AFTER khoa_phong_id;

-- ---------- PHẦN 2: XÓA SẠCH dữ liệu cũ (CHẠY KHI ĐÃ BACKUP) ----------
-- Tắt kiểm tra FK để xóa theo thứ tự
SET FOREIGN_KEY_CHECKS = 0;

-- Xóa dữ liệu CME đang tham chiếu nhân viên
TRUNCATE TABLE dt_cme_ghi_nhan;

-- Gỡ liên kết nhân viên ở giảng viên & học viên (giữ bản ghi, chỉ bỏ trỏ tới NV cũ)
UPDATE dm_giang_vien SET nhan_vien_id = NULL WHERE nhan_vien_id IS NOT NULL;
UPDATE dm_hoc_vien   SET nhan_vien_id = NULL WHERE nhan_vien_id IS NOT NULL;

-- Xóa toàn bộ nhân viên cũ và reset AUTO_INCREMENT
TRUNCATE TABLE dm_nhan_vien;

SET FOREIGN_KEY_CHECKS = 1;

-- Sau khi chạy xong: vào màn Nhân viên → nút "Import Excel" để nạp danh sách mới.
