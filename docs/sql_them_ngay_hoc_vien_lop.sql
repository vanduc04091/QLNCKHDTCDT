-- Thêm khoảng thời gian học riêng cho từng học viên trong 1 (khóa + CTĐT)
-- Dùng cho điểm danh: chỉ hiện học viên có ngày học bao gồm ngày của buổi.
-- Để trống (NULL) = học toàn bộ (như cũ).
ALTER TABLE dt_hoc_vien_lop
  ADD COLUMN ngay_bat_dau DATE NULL AFTER ngay_ghi_danh,
  ADD COLUMN ngay_ket_thuc DATE NULL AFTER ngay_bat_dau;
