-- ============================================================
-- Cập nhật danh mục Khoa/Phòng theo 'danh mục khoa phòng trung tâm 16.7.2026.xlsx'
-- Sinh tự động 16/07/2026 22:22 · DB: ql_nckh_dt_cdt
-- ⚠️ BACKUP DB trước khi chạy (mysqldump).
--
-- Cập nhật 49 khoa · Thêm mới 10 · Xóa mềm 26
-- Không nhân viên nào bị mất khoa. 4 CTĐT được chuyển sang khoa tương đương.
-- Chạy theo đúng thứ tự các bước bên dưới.
-- ============================================================

-- ---------- BƯỚC 1: Thêm mới 10 khoa ----------
INSERT INTO dm_khoa_phong (ma_khoa, ten_khoa, loai_don_vi, trang_thai, ngay_tao, ngay_cap_nhat, da_xoa)
SELECT 'PCN4', 'Trung tâm Đào tạo và Chỉ đạo tuyến', 'Khoa', 1, NOW(), NOW(), 0 FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM (SELECT ma_khoa FROM dm_khoa_phong WHERE ma_khoa='PCN4') t);
INSERT INTO dm_khoa_phong (ma_khoa, ten_khoa, loai_don_vi, trang_thai, ngay_tao, ngay_cap_nhat, da_xoa)
SELECT 'PCN7', 'Cơ sở hạ tầng và Trang thiết bị', 'Khoa', 1, NOW(), NOW(), 0 FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM (SELECT ma_khoa FROM dm_khoa_phong WHERE ma_khoa='PCN7') t);
INSERT INTO dm_khoa_phong (ma_khoa, ten_khoa, loai_don_vi, trang_thai, ngay_tao, ngay_cap_nhat, da_xoa)
SELECT 'PCN9', 'Quản lý chất lượng', 'Khoa', 1, NOW(), NOW(), 0 FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM (SELECT ma_khoa FROM dm_khoa_phong WHERE ma_khoa='PCN9') t);
INSERT INTO dm_khoa_phong (ma_khoa, ten_khoa, loai_don_vi, trang_thai, ngay_tao, ngay_cap_nhat, da_xoa)
SELECT 'PCN11', 'Tổ công tác xã hội', 'Khoa', 1, NOW(), NOW(), 0 FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM (SELECT ma_khoa FROM dm_khoa_phong WHERE ma_khoa='PCN11') t);
INSERT INTO dm_khoa_phong (ma_khoa, ten_khoa, loai_don_vi, trang_thai, ngay_tao, ngay_cap_nhat, da_xoa)
SELECT 'CLS16', 'Ngoại tổng hợp 2', 'Khoa', 1, NOW(), NOW(), 0 FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM (SELECT ma_khoa FROM dm_khoa_phong WHERE ma_khoa='CLS16') t);
INSERT INTO dm_khoa_phong (ma_khoa, ten_khoa, loai_don_vi, trang_thai, ngay_tao, ngay_cap_nhat, da_xoa)
SELECT 'CLS17', 'Ngoại tổng hợp 1', 'Khoa', 1, NOW(), NOW(), 0 FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM (SELECT ma_khoa FROM dm_khoa_phong WHERE ma_khoa='CLS17') t);
INSERT INTO dm_khoa_phong (ma_khoa, ten_khoa, loai_don_vi, trang_thai, ngay_tao, ngay_cap_nhat, da_xoa)
SELECT 'CLS37', 'Nội Tiết', 'Khoa', 1, NOW(), NOW(), 0 FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM (SELECT ma_khoa FROM dm_khoa_phong WHERE ma_khoa='CLS37') t);
INSERT INTO dm_khoa_phong (ma_khoa, ten_khoa, loai_don_vi, trang_thai, ngay_tao, ngay_cap_nhat, da_xoa)
SELECT 'CLS39', 'Huyết học lâm sàng', 'Khoa', 1, NOW(), NOW(), 0 FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM (SELECT ma_khoa FROM dm_khoa_phong WHERE ma_khoa='CLS39') t);
INSERT INTO dm_khoa_phong (ma_khoa, ten_khoa, loai_don_vi, trang_thai, ngay_tao, ngay_cap_nhat, da_xoa)
SELECT 'CLS40', 'Phẫu thuật tim mạch - lồng ngực', 'Khoa', 1, NOW(), NOW(), 0 FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM (SELECT ma_khoa FROM dm_khoa_phong WHERE ma_khoa='CLS40') t);
INSERT INTO dm_khoa_phong (ma_khoa, ten_khoa, loai_don_vi, trang_thai, ngay_tao, ngay_cap_nhat, da_xoa)
SELECT 'CLS51', 'Vi rút- Ký sinh trùng', 'Khoa', 1, NOW(), NOW(), 0 FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM (SELECT ma_khoa FROM dm_khoa_phong WHERE ma_khoa='CLS51') t);

-- ---------- BƯỚC 2: Chuyển CTĐT sang khoa tương đương (tránh mất liên kết) ----------
-- [K08] Khoa Nội Tiết - ĐTĐ  ->  Nội Tiết
UPDATE dt_chuong_trinh SET khoa_phong_id =
  (SELECT id FROM (SELECT id FROM dm_khoa_phong WHERE ten_khoa='Nội Tiết' AND da_xoa=0 ORDER BY id DESC LIMIT 1) x)
  WHERE khoa_phong_id = 24;
-- [K10] Khoa Nội Huyết Học Lâm Sàng  ->  Huyết học lâm sàng
UPDATE dt_chuong_trinh SET khoa_phong_id =
  (SELECT id FROM (SELECT id FROM dm_khoa_phong WHERE ten_khoa='Huyết học lâm sàng' AND da_xoa=0 ORDER BY id DESC LIMIT 1) x)
  WHERE khoa_phong_id = 35;
-- [K51] Trung Tâm Tim Mạch  ->  Nội tim mạch 1
UPDATE dt_chuong_trinh SET khoa_phong_id = 30 WHERE khoa_phong_id = 55;

-- ---------- BƯỚC 3: Cập nhật mã + tên cho 49 khoa đã có ----------
UPDATE dm_khoa_phong SET ma_khoa='PCN1', ten_khoa='Ban Giám đốc', ngay_cap_nhat=NOW() WHERE id=58;
UPDATE dm_khoa_phong SET ma_khoa='PCN2', ten_khoa='Tổ chức cán bộ', ngay_cap_nhat=NOW() WHERE id=59;
UPDATE dm_khoa_phong SET ma_khoa='PCN3', ten_khoa='Kế hoạch tổng hợp', ngay_cap_nhat=NOW() WHERE id=54;
UPDATE dm_khoa_phong SET ma_khoa='PCN5', ten_khoa='Tài chính kế toán', ngay_cap_nhat=NOW() WHERE id=21;
UPDATE dm_khoa_phong SET ma_khoa='PCN6', ten_khoa='Điều dưỡng', ngay_cap_nhat=NOW() WHERE id=86;
UPDATE dm_khoa_phong SET ma_khoa='PCN8', ten_khoa='Công nghệ thông tin', ngay_cap_nhat=NOW() WHERE id=20;
UPDATE dm_khoa_phong SET ma_khoa='PCN10', ten_khoa='Tổ hành chính quản trị', ngay_cap_nhat=NOW() WHERE id=84;
UPDATE dm_khoa_phong SET ma_khoa='CLS1', ten_khoa='Thăm dò chức năng', ngay_cap_nhat=NOW() WHERE id=52;
UPDATE dm_khoa_phong SET ma_khoa='CLS2', ten_khoa='Xquang', ngay_cap_nhat=NOW() WHERE id=51;
UPDATE dm_khoa_phong SET ma_khoa='CLS3', ten_khoa='Di truyền và Sinh học phân tử', ngay_cap_nhat=NOW() WHERE id=66;
UPDATE dm_khoa_phong SET ma_khoa='CLS5', ten_khoa='Vi sinh - Trung tâm xét nghiệm', ngay_cap_nhat=NOW() WHERE id=50;
UPDATE dm_khoa_phong SET ma_khoa='CLS6', ten_khoa='Huyết học - Trung tâm xét nghiệm', ngay_cap_nhat=NOW() WHERE id=19;
UPDATE dm_khoa_phong SET ma_khoa='CLS7', ten_khoa='Hóa Sinh - Trung tâm xét nghiệm', ngay_cap_nhat=NOW() WHERE id=49;
UPDATE dm_khoa_phong SET ma_khoa='CLS9', ten_khoa='Giải phẫu bệnh', ngay_cap_nhat=NOW() WHERE id=53;
UPDATE dm_khoa_phong SET ma_khoa='CLS10', ten_khoa='Kiểm soát nhiễm khuẩn', ngay_cap_nhat=NOW() WHERE id=16;
UPDATE dm_khoa_phong SET ma_khoa='CLS11', ten_khoa='Dược', ngay_cap_nhat=NOW() WHERE id=15;
UPDATE dm_khoa_phong SET ma_khoa='CLS12', ten_khoa='Khám bệnh', ngay_cap_nhat=NOW() WHERE id=27;
UPDATE dm_khoa_phong SET ma_khoa='CLS13', ten_khoa='Cấp cứu', ngay_cap_nhat=NOW() WHERE id=85;
UPDATE dm_khoa_phong SET ma_khoa='CLS14', ten_khoa='Gây mê hồi sức', ngay_cap_nhat=NOW() WHERE id=23;
UPDATE dm_khoa_phong SET ma_khoa='CLS18', ten_khoa='Ngoại Thận - Tiết niệu', ngay_cap_nhat=NOW() WHERE id=43;
UPDATE dm_khoa_phong SET ma_khoa='CLS19', ten_khoa='Ngoại tiêu hóa', ngay_cap_nhat=NOW() WHERE id=42;
UPDATE dm_khoa_phong SET ma_khoa='CLS20', ten_khoa='Phẫu thuật thần kinh cột sống', ngay_cap_nhat=NOW() WHERE id=25;
UPDATE dm_khoa_phong SET ma_khoa='CLS21', ten_khoa='Chấn thương - Chỉnh hình', ngay_cap_nhat=NOW() WHERE id=14;
UPDATE dm_khoa_phong SET ma_khoa='CLS22', ten_khoa='Phẫu thuật tạo hình thẩm mỹ', ngay_cap_nhat=NOW() WHERE id=12;
UPDATE dm_khoa_phong SET ma_khoa='CLS23', ten_khoa='Bỏng', ngay_cap_nhat=NOW() WHERE id=78;
UPDATE dm_khoa_phong SET ma_khoa='CLS24', ten_khoa='Phụ sản', ngay_cap_nhat=NOW() WHERE id=44;
UPDATE dm_khoa_phong SET ma_khoa='CLS25', ten_khoa='Trung tâm Hỗ trợ sinh sản', ngay_cap_nhat=NOW() WHERE id=61;
UPDATE dm_khoa_phong SET ma_khoa='CLS26', ten_khoa='Nhi - sơ sinh', ngay_cap_nhat=NOW() WHERE id=39;
UPDATE dm_khoa_phong SET ma_khoa='CLS27', ten_khoa='Tai mũi họng', ngay_cap_nhat=NOW() WHERE id=45;
UPDATE dm_khoa_phong SET ma_khoa='CLS28', ten_khoa='Răng hàm mặt', ngay_cap_nhat=NOW() WHERE id=46;
UPDATE dm_khoa_phong SET ma_khoa='CLS29', ten_khoa='Mắt', ngay_cap_nhat=NOW() WHERE id=47;
UPDATE dm_khoa_phong SET ma_khoa='CLS30', ten_khoa='Hồi sức tích cực', ngay_cap_nhat=NOW() WHERE id=56;
UPDATE dm_khoa_phong SET ma_khoa='CLS31', ten_khoa='Chống độc', ngay_cap_nhat=NOW() WHERE id=72;
UPDATE dm_khoa_phong SET ma_khoa='CLS32', ten_khoa='Hồi sức tích cực ngoại khoa', ngay_cap_nhat=NOW() WHERE id=57;
UPDATE dm_khoa_phong SET ma_khoa='CLS33', ten_khoa='Nội A - Lão khoa', ngay_cap_nhat=NOW() WHERE id=29;
UPDATE dm_khoa_phong SET ma_khoa='CLS34', ten_khoa='Nội tiêu hóa', ngay_cap_nhat=NOW() WHERE id=32;
UPDATE dm_khoa_phong SET ma_khoa='CLS35', ten_khoa='Nội Dị ứng - Hô hấp', ngay_cap_nhat=NOW() WHERE id=26;
UPDATE dm_khoa_phong SET ma_khoa='CLS36', ten_khoa='Dị ứng - Miễn dịch lâm sàng', ngay_cap_nhat=NOW() WHERE id=69;
UPDATE dm_khoa_phong SET ma_khoa='CLS38', ten_khoa='Nội Thận - Tiết niệu - Lọc máu', ngay_cap_nhat=NOW() WHERE id=34;
UPDATE dm_khoa_phong SET ma_khoa='CLS41', ten_khoa='Nội tim mạch 1', ngay_cap_nhat=NOW() WHERE id=30;
UPDATE dm_khoa_phong SET ma_khoa='CLS42', ten_khoa='Nội tim mạch 2', ngay_cap_nhat=NOW() WHERE id=31;
UPDATE dm_khoa_phong SET ma_khoa='CLS43', ten_khoa='Nội Cơ - xương-  khớp', ngay_cap_nhat=NOW() WHERE id=33;
UPDATE dm_khoa_phong SET ma_khoa='CLS44', ten_khoa='Thần kinh', ngay_cap_nhat=NOW() WHERE id=13;
UPDATE dm_khoa_phong SET ma_khoa='CLS45', ten_khoa='Trung tâm đột quỵ', ngay_cap_nhat=NOW() WHERE id=18;
UPDATE dm_khoa_phong SET ma_khoa='CLS46', ten_khoa='Da liễu', ngay_cap_nhat=NOW() WHERE id=37;
UPDATE dm_khoa_phong SET ma_khoa='CLS47', ten_khoa='Y học cổ truyền', ngay_cap_nhat=NOW() WHERE id=38;
UPDATE dm_khoa_phong SET ma_khoa='CLS48', ten_khoa='Phục hồi chức năng', ngay_cap_nhat=NOW() WHERE id=48;
UPDATE dm_khoa_phong SET ma_khoa='CLS49', ten_khoa='Dinh dưỡng', ngay_cap_nhat=NOW() WHERE id=17;
UPDATE dm_khoa_phong SET ma_khoa='CLS50', ten_khoa='Nhiễm khuẩn tổng hợp', ngay_cap_nhat=NOW() WHERE id=36;

-- ---------- BƯỚC 4: Xóa mềm 26 khoa không còn trong danh sách ----------
--   [PVT] Phòng CSHT&TTB
--   [K08] Khoa Nội Tiết - ĐTĐ
--   [NT] Nhà Thuốc
--   [K10] Khoa Nội Huyết Học Lâm Sàng
--   [K19] Khoa Ngoại Tổng Hợp
--   [K21] Khoa Phẫu Thuật TM Lồng Ngực
--   [K51] Trung Tâm Tim Mạch
--   [DTCDT] Đào Tạo Chỉ Đạo Tuyến
--   [K99.1] Khoa điều trị bệnh nhân COVID không triệu chứng
--   [DVSL] Đơn Vị Chẩn Đoán Trước Sinh Và Sơ Sinh
--   [K112] Khoa Virut- Ký sinh trùng
--   [PQT] Phòng Quản Trị
--   [DTN] Đoàn Thanh Niên
--   [PTTHTM] Khoa Phẫu thuật tạo hình thẩm mỹ
--   [NTH] NT
--   [TTDVTH] Trung tâm dịch vụ tổng hợp
--   [KCLSBVDC] Khu Cận Lâm Sàng (BVDC)
--   [AL] Tủ trực công ty An Lộc
--   [VT01] Văn Thư
--   [TTHS] Trung tâm hồi sức tích cực số 1
--   [TSL] Tổ Sàng Lọc
--   [K99.2] Khoa điều trị COVID Nhẹ và Trung Bình
--   [HCHC] Phòng Hành Chính - Hậu Cần (BVDC)
--   [KSNKBVDC] Khu Kiểm Soát Nhiễm Khuẩn (BVDC)
--   [KXNQT] Khoa Xét Nghiệm BVQT
--   [KYC] Đơn Vị CSSK Theo Yêu Cầu
UPDATE dm_khoa_phong SET da_xoa=1, ngay_cap_nhat=NOW() WHERE id IN (22,24,28,35,40,41,55,60,62,63,64,65,67,68,70,71,73,74,75,76,77,79,80,81,82,83);

-- ---------- BƯỚC 5: Gán khoa cho nhân viên import chưa map được (121 NV) ----------
-- Các khoa này giờ đã có trong danh mục mới nên map được tự động.
UPDATE dm_nhan_vien SET khoa_phong_id = 91 WHERE khoa_phong_id IS NULL AND khoa_phong_text = 'Ngoại tổng hợp 2' AND da_xoa = 0;
UPDATE dm_nhan_vien SET khoa_phong_id = 94 WHERE khoa_phong_id IS NULL AND khoa_phong_text = 'Huyết học lâm sàng' AND da_xoa = 0;
UPDATE dm_nhan_vien SET khoa_phong_id = 96 WHERE khoa_phong_id IS NULL AND khoa_phong_text = 'Vi rút- Ký sinh trùng' AND da_xoa = 0;
UPDATE dm_nhan_vien SET khoa_phong_id = 95 WHERE khoa_phong_id IS NULL AND khoa_phong_text = 'Phẫu thuật tim mạch - lồng ngực' AND da_xoa = 0;
UPDATE dm_nhan_vien SET khoa_phong_id = 93 WHERE khoa_phong_id IS NULL AND khoa_phong_text = 'Nội Tiết' AND da_xoa = 0;
UPDATE dm_nhan_vien SET khoa_phong_id = 92 WHERE khoa_phong_id IS NULL AND khoa_phong_text = 'Ngoại tổng hợp 1' AND da_xoa = 0;
UPDATE dm_nhan_vien SET khoa_phong_id = 87 WHERE khoa_phong_id IS NULL AND khoa_phong_text = 'Trung tâm Đào tạo và Chỉ đạo tuyến' AND da_xoa = 0;
UPDATE dm_nhan_vien SET khoa_phong_id = 89 WHERE khoa_phong_id IS NULL AND khoa_phong_text = 'Quản lý chất lượng' AND da_xoa = 0;
UPDATE dm_nhan_vien SET khoa_phong_id = 90 WHERE khoa_phong_id IS NULL AND khoa_phong_text = 'Tổ công tác xã hội' AND da_xoa = 0;
UPDATE dm_nhan_vien SET khoa_phong_id = 88 WHERE khoa_phong_id IS NULL AND khoa_phong_text = 'Cơ sở hạ tầng và Trang thiết bị' AND da_xoa = 0;

-- Còn 3 NV có tên khoa lạ (Chuyên gia, Z.Dị Ứng Hô Hấp, z.Hồi sức tích cực nội khoa)
-- -> gán tay trên giao diện Nhân viên nếu cần.

-- Xong! Sau khi chạy: vào Hệ thống → Xóa cache để làm mới combo khoa/phòng.
