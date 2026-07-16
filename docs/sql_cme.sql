-- ============================================================
-- Module: Theo dõi tín chỉ CME (cập nhật kiến thức y khoa liên tục)
-- Chạy trên DB ql_nckh_dt_cdt (phpMyAdmin). KHÔNG chạy seed danh mục ở đây —
-- danh mục nạp bằng PHP script: php seed_cme.php
-- ============================================================

-- 1) Nhóm hình thức (5 nhóm theo bảng quy đổi)
CREATE TABLE IF NOT EXISTS dt_cme_nhom (
  id INT AUTO_INCREMENT PRIMARY KEY,
  ma_nhom       VARCHAR(30)  NOT NULL,
  ten_nhom      VARCHAR(255) NOT NULL,
  thu_tu        INT NOT NULL DEFAULT 0,
  ngay_tao      DATETIME DEFAULT CURRENT_TIMESTAMP,
  ngay_cap_nhat DATETIME DEFAULT CURRENT_TIMESTAMP,
  nguoi_tao     INT NULL,
  nguoi_cap_nhat INT NULL,
  da_xoa        INT NOT NULL DEFAULT 0,
  UNIQUE KEY UQ_CME_NHOM (ma_nhom, da_xoa)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2) Loại hoạt động + công thức quy đổi (~20 loại)
CREATE TABLE IF NOT EXISTS dt_cme_loai (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nhom_id         INT NOT NULL,
  ma_loai         VARCHAR(40)  NOT NULL,
  ten_loai        VARCHAR(300) NOT NULL,
  kieu_quy_doi    ENUM('theo_tiet','co_dinh','theo_nam') NOT NULL DEFAULT 'co_dinh',
  gia_tri_quy_doi DECIMAL(6,2) NOT NULL DEFAULT 1.00,   -- hệ số / giờ-mỗi-đơn-vị / giờ-mỗi-năm
  don_vi_tinh     VARCHAR(40)  NULL,                    -- nhãn: buổi, báo cáo, bài, nhiệm vụ, năm...
  khoa_phong_id   INT NULL,                             -- phòng phụ trách quy đổi
  thu_tu          INT NOT NULL DEFAULT 0,
  ngay_tao        DATETIME DEFAULT CURRENT_TIMESTAMP,
  ngay_cap_nhat   DATETIME DEFAULT CURRENT_TIMESTAMP,
  nguoi_tao       INT NULL,
  nguoi_cap_nhat  INT NULL,
  da_xoa          INT NOT NULL DEFAULT 0,
  UNIQUE KEY UQ_CME_LOAI (ma_loai, da_xoa),
  KEY idx_CME_LOAI_nhom (nhom_id, da_xoa),
  CONSTRAINT FK_CME_LOAI_Nhom FOREIGN KEY (nhom_id) REFERENCES dt_cme_nhom(id),
  CONSTRAINT FK_CME_LOAI_KhoaPhong FOREIGN KEY (khoa_phong_id) REFERENCES dm_khoa_phong(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3) Sổ ghi nhận hoạt động CME của nhân viên
CREATE TABLE IF NOT EXISTS dt_cme_ghi_nhan (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nhan_vien_id  INT NOT NULL,
  loai_id       INT NOT NULL,
  nam           SMALLINT NOT NULL,
  ten_hoat_dong VARCHAR(400) NULL,
  vai_tro       VARCHAR(100) NULL,
  so_luong      DECIMAL(7,2) NOT NULL DEFAULT 1.00,   -- số tiết / số lần / số bài
  gio_tin_chi   DECIMAL(7,2) NOT NULL DEFAULT 0.00,   -- kết quả quy đổi (snapshot khi lưu)
  ngay_bat_dau  DATE NULL,
  ngay_ket_thuc DATE NULL,
  ghi_chu       VARCHAR(500) NULL,
  ngay_tao      DATETIME DEFAULT CURRENT_TIMESTAMP,
  ngay_cap_nhat DATETIME DEFAULT CURRENT_TIMESTAMP,
  nguoi_tao     INT NULL,
  nguoi_cap_nhat INT NULL,
  da_xoa        INT NOT NULL DEFAULT 0,
  KEY idx_CME_GN_nv  (nhan_vien_id, nam, da_xoa),
  KEY idx_CME_GN_loai (loai_id, da_xoa),
  KEY idx_CME_GN_nam (nam, da_xoa),
  CONSTRAINT FK_CME_GN_NhanVien FOREIGN KEY (nhan_vien_id) REFERENCES dm_nhan_vien(id),
  CONSTRAINT FK_CME_GN_Loai      FOREIGN KEY (loai_id)      REFERENCES dt_cme_loai(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4) Khai báo 2 form vào danh sách form (để gán quyền)
INSERT INTO dm_danh_sach_form (modules_tuong_ung, ten_form, ngay_tao, ngay_cap_nhat, da_xoa)
SELECT 'DT_CME_DanhMuc', 'Danh mục quy đổi CME', NOW(), NOW(), 0
WHERE NOT EXISTS (SELECT 1 FROM dm_danh_sach_form WHERE modules_tuong_ung='DT_CME_DanhMuc');

INSERT INTO dm_danh_sach_form (modules_tuong_ung, ten_form, ngay_tao, ngay_cap_nhat, da_xoa)
SELECT 'DT_CME', 'Theo dõi tín chỉ CME', NOW(), NOW(), 0
WHERE NOT EXISTS (SELECT 1 FROM dm_danh_sach_form WHERE modules_tuong_ung='DT_CME');

-- Ghi chú: ngưỡng giờ tín chỉ lưu ở DM_CAU_HINH (key CME_NGUONG_GIO, CME_CHU_KY_NAM)
-- được seed tự động bởi php seed_cme.php nên KHÔNG cần thêm ở đây.
