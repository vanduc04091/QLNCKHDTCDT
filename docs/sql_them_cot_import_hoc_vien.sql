-- ============================================================
-- Thêm cột phục vụ Import học viên từ Excel (mẫu danh sách nhập HV)
-- Chạy trên DB ql_nckh_dt_cdt
-- ============================================================
ALTER TABLE dm_hoc_vien
  ADD COLUMN trinh_do_chuyen_mon VARCHAR(150) NULL COMMENT 'Trình độ chuyên môn' AFTER gioi_tinh,
  ADD COLUMN cccd_ngay_cap DATE NULL COMMENT 'Ngày cấp CCCD' AFTER cccd,
  ADD COLUMN cccd_noi_cap VARCHAR(200) NULL COMMENT 'Nơi cấp CCCD' AFTER cccd_ngay_cap,
  ADD COLUMN truong_dao_tao VARCHAR(200) NULL COMMENT 'Trường đào tạo' AFTER dia_chi,
  ADD COLUMN nam_tot_nghiep SMALLINT NULL COMMENT 'Năm tốt nghiệp' AFTER truong_dao_tao;
