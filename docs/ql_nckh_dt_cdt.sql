-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 14, 2026 at 05:10 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ql_nckh_dt_cdt`
--

-- --------------------------------------------------------

--
-- Table structure for table `dm_benh_vien`
--

CREATE TABLE `dm_benh_vien` (
  `id` int(11) NOT NULL,
  `ma_benh_vien` varchar(50) NOT NULL,
  `ten_benh_vien` varchar(300) NOT NULL,
  `dia_chi` varchar(500) DEFAULT NULL,
  `dien_thoai` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `cap_benh_vien` varchar(20) DEFAULT 'TuyenTinh',
  `hang_benh_vien` varchar(50) DEFAULT NULL,
  `giam_doc` varchar(100) DEFAULT NULL,
  `dien_thoai_giam_doc` varchar(20) DEFAULT NULL,
  `la_benh_vien_chinh` tinyint(4) DEFAULT 0,
  `ngay_ky_hop_tac` date DEFAULT NULL,
  `trang_thai` tinyint(4) DEFAULT 1,
  `ngay_tao` datetime DEFAULT current_timestamp(),
  `ngay_cap_nhat` datetime DEFAULT current_timestamp(),
  `nguoi_tao` int(11) DEFAULT NULL,
  `nguoi_cap_nhat` int(11) DEFAULT NULL,
  `da_xoa` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `dm_benh_vien`
--

INSERT INTO `dm_benh_vien` (`id`, `ma_benh_vien`, `ten_benh_vien`, `dia_chi`, `dien_thoai`, `email`, `cap_benh_vien`, `hang_benh_vien`, `giam_doc`, `dien_thoai_giam_doc`, `la_benh_vien_chinh`, `ngay_ky_hop_tac`, `trang_thai`, `ngay_tao`, `ngay_cap_nhat`, `nguoi_tao`, `nguoi_cap_nhat`, `da_xoa`) VALUES
(1, 'BVHNDK', 'Bệnh viện HNĐK Nghệ An', 'TP. Vinh, Nghệ An', NULL, NULL, 'TuyenTinh', NULL, NULL, NULL, 1, NULL, 1, '2026-04-20 17:44:05', '2026-04-20 17:44:05', NULL, NULL, 0),
(2, 'BVTP', 'Bệnh viện thành phố', NULL, NULL, NULL, 'TuyenHuyen', 'I', 'Nguyễn Phúc Lộc', NULL, 0, NULL, 1, '2026-04-21 22:30:15', '2026-04-21 22:30:35', 1, 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `dm_cau_hinh`
--

CREATE TABLE `dm_cau_hinh` (
  `ma_cau_hinh` varchar(50) NOT NULL,
  `gia_tri` text DEFAULT NULL,
  `mo_ta` varchar(255) DEFAULT NULL,
  `ngay_cap_nhat` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `dm_cau_hinh`
--

INSERT INTO `dm_cau_hinh` (`ma_cau_hinh`, `gia_tri`, `mo_ta`, `ngay_cap_nhat`) VALUES
('MAIL_ENABLED', '0', '1=Bật gửi mail, 0=Tắt (chỉ log)', '2026-04-26 21:35:45'),
('PUBLIC_BASE_URL', 'http://qldt.bv', 'URL gốc cho link tra cứu trong email', '2026-04-26 21:35:45'),
('SMTP_FROM_NAME', 'QL NCKH - Đào tạo - Chỉ đạo tuyến', 'Tên người gửi', '2026-04-26 21:35:45'),
('SMTP_HOST', '', 'SMTP server (vd: smtp.gmail.com)', '2026-04-26 21:35:45'),
('SMTP_PASS', '', 'App password', '2026-04-26 21:35:45'),
('SMTP_PORT', '587', 'SMTP port (587=TLS, 465=SSL)', '2026-04-26 21:35:45'),
('SMTP_SECURE', 'tls', 'tls hoặc ssl', '2026-04-26 21:35:45'),
('SMTP_USER', '', 'Email gửi', '2026-04-26 21:35:45');

-- --------------------------------------------------------

--
-- Table structure for table `dm_danh_sach_form`
--

CREATE TABLE `dm_danh_sach_form` (
  `id` int(11) NOT NULL,
  `modules_tuong_ung` varchar(50) NOT NULL,
  `ten_form` varchar(100) NOT NULL,
  `ngay_tao` datetime NOT NULL,
  `ngay_cap_nhat` datetime NOT NULL,
  `nguoi_tao` int(11) NOT NULL DEFAULT 0,
  `nguoi_cap_nhat` int(11) NOT NULL DEFAULT 0,
  `da_xoa` int(11) NOT NULL DEFAULT 0,
  `form_cha_id` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `dm_danh_sach_form`
--

INSERT INTO `dm_danh_sach_form` (`id`, `modules_tuong_ung`, `ten_form`, `ngay_tao`, `ngay_cap_nhat`, `nguoi_tao`, `nguoi_cap_nhat`, `da_xoa`, `form_cha_id`) VALUES
(1, 'Dashboard', 'Trang tổng quan', '2026-04-20 17:44:05', '2026-04-20 17:44:05', 0, 0, 0, 0),
(2, 'DM_NguoiDung', 'Người dùng', '2026-04-20 17:44:05', '2026-04-20 17:44:05', 0, 0, 0, 0),
(3, 'DM_NhomTaiKhoan', 'Nhóm tài khoản', '2026-04-20 17:44:05', '2026-04-20 17:44:05', 0, 0, 0, 0),
(4, 'DM_PhanQuyen', 'Phân quyền', '2026-04-20 17:44:05', '2026-04-20 17:44:05', 0, 0, 0, 0),
(5, 'DM_DanhSachForm', 'Danh sách form', '2026-04-20 17:44:05', '2026-04-20 17:44:05', 0, 0, 0, 0),
(6, 'DM_NhanVien', 'Nhân viên', '2026-04-20 17:44:05', '2026-04-20 17:44:05', 0, 0, 0, 0),
(7, 'DM_KhoaPhong', 'Khoa / Phòng', '2026-04-20 17:44:05', '2026-04-20 17:44:05', 0, 0, 0, 0),
(8, 'DM_BenhVien', 'Bệnh viện', '2026-04-20 17:44:05', '2026-04-20 17:44:05', 0, 0, 0, 0),
(9, 'DT_MonHoc', 'Bài học', '2026-04-22 17:43:01', '2026-04-22 17:43:01', 0, 0, 0, 0),
(11, 'DM_HocVien', 'Học viên', '2026-04-23 17:11:19', '2026-04-23 17:11:19', 0, 0, 0, 0),
(12, 'DT_ChuongTrinh', 'Chuong trýnh dÓo t?o', '2026-04-24 17:02:08', '2026-06-12 09:21:32', 0, 1, 0, 0),
(13, 'DT_HocVienLop', 'Học viên - Lớp', '2026-04-24 17:02:08', '2026-04-24 17:02:08', 0, 0, 0, 0),
(14, 'DT_LichHoc', 'Lịch học', '2026-04-24 21:57:15', '2026-04-24 21:57:15', 0, 0, 0, 0),
(15, 'DT_DiemDanh', 'Điểm danh', '2026-04-25 10:02:14', '2026-04-25 10:02:14', 0, 0, 0, 0),
(16, 'DT_KetQuaHocTap', 'Kết quả học tập', '2026-04-25 10:02:14', '2026-04-25 10:02:14', 0, 0, 0, 0),
(17, 'DM_GiangVien', 'Giảng viên', '2026-04-25 16:27:08', '2026-04-25 16:27:08', 0, 0, 0, 0),
(18, 'DT_PhanCongGiangVien', 'Phân công giảng viên', '2026-04-25 16:27:08', '2026-04-25 16:27:08', 0, 0, 0, 0),
(19, 'DT_TaiLieu', 'Tài liệu', '2026-04-25 21:10:08', '2026-04-25 21:10:08', 0, 0, 0, 0),
(20, 'DT_BaiKiemTra', 'Bài kiểm tra', '2026-04-26 06:40:29', '2026-04-26 06:40:29', 0, 0, 0, 0),
(21, 'DT_HoSoHocVien', 'Hồ sơ học viên', '2026-04-26 07:05:37', '2026-04-26 23:18:02', 1, 1, 0, 0),
(22, 'DT_ChungChi', 'Chứng chỉ', '2026-04-26 07:05:37', '2026-04-26 23:18:13', 1, 1, 0, 0),
(23, 'DT_DangKyKhoaHoc', 'Đăng ký khóa học', '2026-04-26 21:35:45', '2026-04-26 21:35:45', 0, 0, 0, 0),
(24, 'DM_CauHinh', 'Cấu hình hệ thống', '2026-04-26 21:53:12', '2026-04-26 21:53:12', 0, 0, 0, 0),
(25, 'DM_LoaiHinhDaoTao', 'Loại hình đào tạo', '2026-04-27 09:53:37', '2026-04-27 09:53:37', 0, 0, 0, 0),
(26, 'DM_HinhThucHoc', 'Hình thức học', '2026-04-27 09:53:37', '2026-04-27 09:53:37', 0, 0, 0, 0),
(27, 'DM_DoiTuongHocVien', 'Đối tượng học viên', '2026-04-27 09:53:37', '2026-04-27 09:53:37', 0, 0, 0, 0),
(28, 'NCKH_Dashboard', 'Tổng quan NCKH', '2026-04-27 16:55:31', '2026-04-27 16:55:31', 1, 1, 0, 0),
(29, 'NCKH_DeTai', 'Đề tài NCKH', '2026-04-27 16:55:31', '2026-04-27 16:55:31', 1, 1, 0, 0),
(30, 'NCKH_TienDo', 'Tiến độ NCKH', '2026-04-27 16:55:31', '2026-04-27 16:55:31', 1, 1, 0, 0),
(31, 'NCKH_TaiLieu', 'Tài liệu NCKH', '2026-04-27 16:55:31', '2026-04-27 16:55:31', 1, 1, 0, 0),
(32, 'NCKH_ThanhVien', 'Thành viên đề tài', '2026-04-27 16:55:31', '2026-04-27 16:55:31', 1, 1, 0, 0),
(33, 'NCKH_NhacViec', 'Nhắc việc NCKH', '2026-04-27 16:55:31', '2026-04-27 16:55:31', 1, 1, 0, 0),
(34, 'DM_NCKH_CapDo', 'Cấp độ NCKH', '2026-04-27 16:55:31', '2026-04-27 16:55:31', 1, 1, 0, 0),
(35, 'DM_NCKH_TheLoai', 'Thể loại NCKH', '2026-04-27 16:55:31', '2026-04-27 16:55:31', 1, 1, 0, 0),
(36, 'NCKH_HoiDong', 'Hội đồng đề tài NCKH', '2026-04-28 22:28:44', '2026-04-28 22:28:44', 1, 1, 0, 0),
(37, 'NCKH_DeTaiCuaToi', 'Đề tài của tôi', '2026-04-29 17:12:51', '2026-04-29 17:12:51', 1, 1, 0, 0),
(38, 'NCKH_DuyetDeTai', 'Duyệt đề tài NCKH', '2026-04-29 17:12:51', '2026-04-29 17:12:51', 1, 1, 0, 0),
(39, 'NCKH_DotDangKy', 'Đợt đăng ký đề tài NCKH', '2026-05-04 17:02:30', '2026-05-04 17:02:30', 0, 0, 0, 0),
(40, 'DT_DotDangKy', 'Đợt đăng ký khóa học', '2026-05-12 22:57:02', '2026-05-12 22:57:02', 0, 0, 0, 0),
(41, 'DT_KhoaHoc', 'Khoá học', '2026-06-11 20:44:00', '2026-06-11 20:44:44', 1, 1, 0, 0),
(42, 'DT_ChuongTrinhMonHoc', 'Bài học của chương trình', '2026-06-11 21:04:00', '2026-06-11 21:04:00', 0, 0, 0, 0),
(43, 'DM_XoaCache', 'Xóa cache hệ thống', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, 0, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `dm_doi_tuong_hoc_vien`
--

CREATE TABLE `dm_doi_tuong_hoc_vien` (
  `id` int(11) NOT NULL,
  `ma_doi_tuong` varchar(20) NOT NULL,
  `ten_doi_tuong` varchar(200) NOT NULL,
  `mo_ta` varchar(500) DEFAULT NULL,
  `thu_tu` int(11) DEFAULT 0,
  `trang_thai` tinyint(4) DEFAULT 1,
  `ngay_tao` datetime DEFAULT current_timestamp(),
  `ngay_cap_nhat` datetime DEFAULT current_timestamp(),
  `nguoi_tao` int(11) DEFAULT NULL,
  `nguoi_cap_nhat` int(11) DEFAULT NULL,
  `da_xoa` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `dm_doi_tuong_hoc_vien`
--

INSERT INTO `dm_doi_tuong_hoc_vien` (`id`, `ma_doi_tuong`, `ten_doi_tuong`, `mo_ta`, `thu_tu`, `trang_thai`, `ngay_tao`, `ngay_cap_nhat`, `nguoi_tao`, `nguoi_cap_nhat`, `da_xoa`) VALUES
(1, 'BS', 'Bác sĩ', 'Bác sĩ điều trị', 1, 1, '2026-04-21 23:03:36', '2026-04-21 23:04:04', 1, 1, 0),
(2, 'DD', 'Điều dưỡng', 'Điều dưỡng các khoa lâm sàng', 2, 1, '2026-04-21 23:03:36', '2026-04-21 23:03:36', 1, 1, 0),
(3, 'KTV', 'Kỹ thuật viên', 'KTV xét nghiệm, CĐHA, vật lý trị liệu...', 3, 1, '2026-04-21 23:03:36', '2026-04-21 23:03:36', 1, 1, 0),
(4, 'NHS', 'Nữ hộ sinh', 'Nữ hộ sinh sản phụ khoa', 4, 1, '2026-04-21 23:03:36', '2026-04-21 23:03:36', 1, 1, 0),
(5, 'DSI', 'Dược sĩ', 'Dược sĩ lâm sàng / nhà thuốc', 5, 1, '2026-04-21 23:03:36', '2026-04-21 23:03:36', 1, 1, 0),
(6, 'QL', 'Cán bộ quản lý y tế', 'Lãnh đạo bệnh viện, trưởng khoa/phòng', 6, 1, '2026-04-21 23:03:36', '2026-04-21 23:03:36', 1, 1, 0),
(7, 'SV', 'Sinh viên y khoa', 'Sinh viên các trường ĐH Y', 7, 1, '2026-04-21 23:03:36', '2026-04-21 23:03:36', 1, 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `dm_giang_vien`
--

CREATE TABLE `dm_giang_vien` (
  `id` int(11) NOT NULL,
  `ma_gv` varchar(50) NOT NULL,
  `ho_ten` varchar(150) NOT NULL,
  `ngay_sinh` date DEFAULT NULL,
  `gioi_tinh` varchar(10) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `dien_thoai` varchar(30) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `hoc_vi` varchar(50) DEFAULT NULL,
  `hoc_ham` varchar(50) DEFAULT NULL,
  `chuyen_mon` varchar(255) DEFAULT NULL,
  `nhan_vien_id` int(11) DEFAULT NULL,
  `don_vi_cong_tac` varchar(255) DEFAULT NULL,
  `loai_gv` tinyint(4) NOT NULL DEFAULT 1,
  `trang_thai` tinyint(4) NOT NULL DEFAULT 1,
  `ghi_chu` varchar(500) DEFAULT NULL,
  `ngay_tao` datetime DEFAULT current_timestamp(),
  `ngay_cap_nhat` datetime DEFAULT current_timestamp(),
  `nguoi_tao` int(11) DEFAULT NULL,
  `nguoi_cap_nhat` int(11) DEFAULT NULL,
  `da_xoa` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `dm_giang_vien`
--

INSERT INTO `dm_giang_vien` (`id`, `ma_gv`, `ho_ten`, `ngay_sinh`, `gioi_tinh`, `email`, `dien_thoai`, `avatar`, `hoc_vi`, `hoc_ham`, `chuyen_mon`, `nhan_vien_id`, `don_vi_cong_tac`, `loai_gv`, `trang_thai`, `ghi_chu`, `ngay_tao`, `ngay_cap_nhat`, `nguoi_tao`, `nguoi_cap_nhat`, `da_xoa`) VALUES
(1, 'GV-NV001', 'Nguyễn Văn An', NULL, 'Nam', 'an.nguyen@bvhnda.vn', '0912345001', NULL, 'BS CKII', NULL, 'Nội khoa tổng quát', 1, NULL, 1, 1, NULL, '2026-04-25 16:27:08', '2026-04-25 16:27:08', 1, 1, 0),
(2, 'GV-NV002', 'Trần Thị Bình', NULL, 'Nữ', 'binh.tran@bvhnda.vn', '0912345002', NULL, 'BS CKI', NULL, 'Cấp cứu - BLS/ACLS', 2, NULL, 1, 1, NULL, '2026-04-25 16:27:08', '2026-04-25 16:27:08', 1, 1, 0),
(3, 'GV-NV003', 'Lê Văn Cường', NULL, 'Nam', 'cuong.le@bvhnda.vn', '0912345003', NULL, 'BS', NULL, 'Điều dưỡng lâm sàng', 3, NULL, 1, 1, NULL, '2026-04-25 16:27:08', '2026-04-25 16:27:08', 1, 1, 0),
(4, 'GV-NV004', 'Phạm Thị Dung', NULL, 'Nữ', 'dung.pham@bvhnda.vn', '0912345004', NULL, 'TS', NULL, 'Y học cơ sở', 4, NULL, 1, 1, NULL, '2026-04-25 16:27:08', '2026-04-25 16:27:08', 1, 1, 0),
(5, 'GV-NV005', 'Hoàng Văn Em', NULL, 'Nam', 'em.hoang@bvhnda.vn', '0912345005', NULL, 'BS CKII', NULL, 'Nội khoa tổng quát', 5, NULL, 1, 1, NULL, '2026-04-25 16:27:08', '2026-04-25 16:27:08', 1, 1, 0),
(6, 'GV-NV007', 'Đỗ Thị Hà', NULL, 'Nữ', 'ha.do@bvhnda.vn', '0912345007', NULL, 'BS CKI', NULL, 'Cấp cứu - BLS/ACLS', 7, NULL, 1, 1, NULL, '2026-04-25 16:27:08', '2026-04-25 16:27:08', 1, 1, 0),
(7, 'GV-NV008', 'Bùi Văn Khánh', NULL, 'Nam', 'khanh.bui@bvhnda.vn', '0912345008', NULL, 'BS', NULL, 'Điều dưỡng lâm sàng', 8, NULL, 1, 1, NULL, '2026-04-25 16:27:08', '2026-04-25 16:27:08', 1, 1, 0),
(8, 'GV-NV009', 'Ngô Thị Linh', NULL, 'Nữ', 'linh.ngo@bvhnda.vn', '0912345009', NULL, 'TS', NULL, 'Y học cơ sở', 9, NULL, 1, 1, NULL, '2026-04-25 16:27:08', '2026-04-25 16:27:08', 1, 1, 0),
(9, 'GV-NV010', 'Đặng Văn Minh', NULL, 'Nam', 'minh.dang@bvhnda.vn', '0912345010', NULL, 'BS CKII', NULL, 'Nội khoa tổng quát', 10, NULL, 1, 1, NULL, '2026-04-25 16:27:08', '2026-04-25 16:27:08', 1, 1, 0),
(10, 'GVN-0001', 'GS.TS. Nguyễn Văn A', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'BV Bạch Mai', 2, 1, NULL, '2026-04-25 16:27:08', '2026-04-25 16:27:08', 1, 1, 0),
(11, 'GVN-EXP-001', 'GS.TS Nguyễn Lân Hiếu', NULL, 'Nam', NULL, NULL, NULL, 'TS', 'GS', 'Tim mạch can thiệp', NULL, 'BV Đại học Y Hà Nội', 2, 1, NULL, '2026-04-25 16:27:21', '2026-04-25 16:27:21', 1, 1, 0),
(12, 'GVN-EXP-002', 'PGS.TS Phạm Quang Vinh', NULL, 'Nam', NULL, NULL, NULL, 'TS', 'PGS', 'Hồi sức cấp cứu', NULL, 'BV Bạch Mai', 2, 1, NULL, '2026-04-25 16:27:21', '2026-04-25 16:27:21', 1, 1, 0),
(13, 'GVN-EXP-003', 'TS.BS Trần Bình Giang', NULL, 'Nam', NULL, NULL, NULL, 'TS', NULL, 'Phẫu thuật nội soi', NULL, 'BV Việt Đức', 2, 1, NULL, '2026-04-25 16:27:21', '2026-04-25 16:27:21', 1, 1, 0),
(14, 'GVN-EXP-004', 'PGS.TS Lưu Thị Hồng', NULL, 'Nữ', NULL, NULL, NULL, 'TS', 'PGS', 'Sản phụ khoa', NULL, 'BV Phụ sản TW', 2, 1, NULL, '2026-04-25 16:27:21', '2026-04-25 16:27:21', 1, 1, 0),
(15, 'GVN-EXP-005', 'TS Nguyễn Văn Khải', NULL, 'Nam', NULL, NULL, NULL, 'TS', NULL, 'Quản lý chất lượng bệnh viện', NULL, 'Cục QLKCB - Bộ Y tế', 3, 1, NULL, '2026-04-25 16:27:21', '2026-04-25 16:27:21', 1, 1, 0),
(16, 'GVN-EXP-006', 'BS CKII Hoàng Anh Tuấn', NULL, 'Nam', NULL, NULL, NULL, 'BS CKII', NULL, 'Chẩn đoán hình ảnh', NULL, 'BV K Trung ương', 2, 1, NULL, '2026-04-25 16:27:21', '2026-04-25 16:27:21', 1, 1, 0),
(17, 'GV_0001', 'Trần thị BÌnh', NULL, 'Nữ', NULL, NULL, NULL, 'ThS', 'GS', NULL, 2, NULL, 1, 1, NULL, '2026-04-25 21:23:08', '2026-04-25 21:23:08', 1, 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `dm_hinh_thuc_hoc`
--

CREATE TABLE `dm_hinh_thuc_hoc` (
  `id` int(11) NOT NULL,
  `ma_hinh_thuc` varchar(20) NOT NULL,
  `ten_hinh_thuc` varchar(100) NOT NULL,
  `mo_ta` varchar(500) DEFAULT NULL,
  `thu_tu` int(11) DEFAULT 0,
  `trang_thai` tinyint(4) DEFAULT 1,
  `ngay_tao` datetime DEFAULT current_timestamp(),
  `ngay_cap_nhat` datetime DEFAULT current_timestamp(),
  `nguoi_tao` int(11) DEFAULT NULL,
  `nguoi_cap_nhat` int(11) DEFAULT NULL,
  `da_xoa` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `dm_hinh_thuc_hoc`
--

INSERT INTO `dm_hinh_thuc_hoc` (`id`, `ma_hinh_thuc`, `ten_hinh_thuc`, `mo_ta`, `thu_tu`, `trang_thai`, `ngay_tao`, `ngay_cap_nhat`, `nguoi_tao`, `nguoi_cap_nhat`, `da_xoa`) VALUES
(1, 'TT', 'Trực tiếp tại lớp', 'Học viên có mặt tại địa điểm đào tạo', 1, 1, '2026-04-21 23:03:36', '2026-04-21 23:03:36', 1, 1, 0),
(2, 'ONLINE', 'Trực tuyến (Online)', 'Học qua nền tảng video conference', 2, 1, '2026-04-21 23:03:36', '2026-04-21 23:03:36', 1, 1, 0),
(3, 'HYBRID', 'Kết hợp (Hybrid)', 'Kết hợp trực tiếp và trực tuyến', 3, 1, '2026-04-21 23:03:36', '2026-04-21 23:03:36', 1, 1, 0),
(4, 'ELEARN', 'E-learning tự học', 'Học viên tự học qua hệ thống LMS', 4, 1, '2026-04-21 23:03:36', '2026-04-21 23:03:36', 1, 1, 0),
(5, 'THUC', 'Đi lâm sàng', 'Học viên thực hành lâm sàng tại khoa/phòng', 5, 1, '2026-04-21 23:03:36', '2026-04-21 23:04:28', 1, 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `dm_hoc_vien`
--

CREATE TABLE `dm_hoc_vien` (
  `id` int(11) NOT NULL,
  `ma_hv` varchar(50) NOT NULL,
  `ho_ten` varchar(200) NOT NULL,
  `ngay_sinh` date DEFAULT NULL,
  `gioi_tinh` varchar(10) DEFAULT NULL,
  `dien_thoai` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `cccd` varchar(20) DEFAULT NULL,
  `dia_chi` varchar(250) DEFAULT NULL,
  `don_vi_cong_tac` varchar(200) DEFAULT NULL,
  `chuc_vu` varchar(100) DEFAULT NULL,
  `doi_tuong_id` int(11) DEFAULT NULL,
  `la_nhan_vien` tinyint(4) NOT NULL DEFAULT 0,
  `nhan_vien_id` int(11) DEFAULT NULL,
  `avatar` varchar(250) DEFAULT NULL,
  `ghi_chu` text DEFAULT NULL,
  `trang_thai` tinyint(4) NOT NULL DEFAULT 1,
  `ngay_tao` datetime DEFAULT current_timestamp(),
  `ngay_cap_nhat` datetime DEFAULT current_timestamp(),
  `nguoi_tao` int(11) DEFAULT NULL,
  `nguoi_cap_nhat` int(11) DEFAULT NULL,
  `da_xoa` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `dm_hoc_vien`
--

INSERT INTO `dm_hoc_vien` (`id`, `ma_hv`, `ho_ten`, `ngay_sinh`, `gioi_tinh`, `dien_thoai`, `email`, `cccd`, `dia_chi`, `don_vi_cong_tac`, `chuc_vu`, `doi_tuong_id`, `la_nhan_vien`, `nhan_vien_id`, `avatar`, `ghi_chu`, `trang_thai`, `ngay_tao`, `ngay_cap_nhat`, `nguoi_tao`, `nguoi_cap_nhat`, `da_xoa`) VALUES
(1, 'HV-NGOAI-001', 'Nguyễn Văn Đức', '1991-09-04', 'Nam', NULL, NULL, NULL, NULL, NULL, NULL, 1, 0, NULL, NULL, NULL, 1, '2026-06-14 16:43:37', '2026-06-14 16:43:37', 1, 1, 0),
(2, 'HV-1682', 'Chu Quang Lương', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 793, NULL, NULL, 1, '2026-06-14 21:14:43', '2026-06-14 21:15:29', 1, 1, 1),
(3, 'HV-1460', 'Cao Thị Huyền', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 1005, NULL, NULL, 1, '2026-06-14 21:15:19', '2026-06-14 21:15:27', 1, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `dm_khoa_phong`
--

CREATE TABLE `dm_khoa_phong` (
  `id` int(11) NOT NULL,
  `ma_khoa` varchar(50) NOT NULL,
  `ten_khoa` varchar(200) NOT NULL,
  `loai_don_vi` varchar(20) NOT NULL DEFAULT 'Khoa',
  `truong_khoa_id` int(11) DEFAULT NULL,
  `dien_thoai` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `chuyen_khoa` varchar(200) DEFAULT NULL,
  `so_giuong` int(11) DEFAULT NULL,
  `trang_thai` tinyint(4) DEFAULT 1,
  `ngay_tao` datetime DEFAULT current_timestamp(),
  `ngay_cap_nhat` datetime DEFAULT current_timestamp(),
  `nguoi_tao` int(11) DEFAULT NULL,
  `nguoi_cap_nhat` int(11) DEFAULT NULL,
  `da_xoa` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `dm_khoa_phong`
--

INSERT INTO `dm_khoa_phong` (`id`, `ma_khoa`, `ten_khoa`, `loai_don_vi`, `truong_khoa_id`, `dien_thoai`, `email`, `chuyen_khoa`, `so_giuong`, `trang_thai`, `ngay_tao`, `ngay_cap_nhat`, `nguoi_tao`, `nguoi_cap_nhat`, `da_xoa`) VALUES
(1, 'KHOA_NOI', 'Khoa Nội tổng hợp', 'Khoa', 1, '02383.111.001', 'noi@bvhnda.vn', 'Nội khoa', 60, 1, '2026-04-21 16:58:42', '2026-06-13 22:37:54', 1, 9, 1),
(2, 'KHOA_NGOAI', 'Khoa Ngoại tổng hợp', 'Khoa', 3, '02383.111.002', 'ngoai@bvhnda.vn', 'Ngoại khoa', 80, 1, '2026-04-21 16:58:42', '2026-06-13 22:37:51', 1, 9, 1),
(3, 'KHOA_SAN', 'Khoa Sản', 'Khoa', 6, '02383.111.003', 'san@bvhnda.vn', 'Sản phụ khoa', 50, 1, '2026-04-21 16:58:42', '2026-06-13 22:37:48', 1, 9, 1),
(4, 'KHOA_NHI', 'Khoa Nhi', 'Khoa', 8, '02383.111.004', 'nhi@bvhnda.vn', 'Nhi khoa', 45, 1, '2026-04-21 16:58:42', '2026-06-13 22:37:46', 1, 9, 1),
(5, 'KHOA_HSTC', 'Khoa Hồi sức tích cực', 'Khoa', 10, '02383.111.005', 'hstc@bvhnda.vn', 'Hồi sức cấp cứu', 20, 1, '2026-04-21 16:58:42', '2026-06-13 22:37:44', 1, 9, 1),
(6, 'KHOA_CDHA', 'Khoa Chẩn đoán hình ảnh', 'Khoa', 12, '02383.111.006', 'cdha@bvhnda.vn', 'CĐHA', NULL, 1, '2026-04-21 16:58:42', '2026-06-13 22:37:42', 1, 9, 1),
(7, 'PHONG_KHTH', 'Phòng Kế hoạch tổng hợp', 'Phong', 14, '02383.111.101', 'khth@bvhnda.vn', NULL, NULL, 1, '2026-04-21 16:58:42', '2026-06-13 22:37:40', 1, 9, 1),
(8, 'PHONG_TCCB', 'Phòng Tổ chức cán bộ', 'Phong', 16, '02383.111.102', 'tccb@bvhnda.vn', NULL, NULL, 1, '2026-04-21 16:58:42', '2026-06-13 22:37:38', 1, 9, 1),
(9, 'PHONG_DT', 'Phòng Đào tạo - CĐT', 'Phong', 18, '02383.111.103', 'daotao@bvhnda.vn', NULL, NULL, 1, '2026-04-21 16:58:42', '2026-06-13 22:37:36', 1, 9, 1),
(10, 'TTHT_NCKH', 'Trung tâm NCKH', 'TrungTam', NULL, '02383.111.201', 'nckh@bvhnda.vn', 'Nghiên cứu KH', NULL, 1, '2026-04-21 16:58:42', '2026-06-13 22:37:32', 1, 9, 1),
(11, 'PHONG_CNTT', 'Phòng Công nghệ Thông tin', 'Phong', 10, NULL, NULL, NULL, NULL, 1, '2026-04-21 22:29:29', '2026-06-13 22:37:21', 1, 9, 1),
(12, 'PTTM', 'Phẫu Thuật Tạo Hình Thẩm Mỹ', 'Khoa', NULL, NULL, NULL, NULL, 10, 1, '2026-05-22 08:52:15', '2026-05-22 08:52:15', 0, 0, 0),
(13, 'K141', 'Khoa Thần Kinh', 'Khoa', NULL, NULL, NULL, NULL, 92, 1, '2026-05-22 08:52:15', '2026-05-22 08:52:15', 0, 0, 0),
(14, 'K24', 'Khoa Chấn Thương - Chỉnh Hình', 'Khoa', NULL, NULL, NULL, NULL, 95, 1, '2026-05-22 08:52:15', '2026-05-22 08:52:15', 0, 0, 0),
(15, 'KHOA01', 'Khoa Dược', 'Khoa', NULL, NULL, NULL, NULL, NULL, 1, '2026-05-22 08:52:15', '2026-05-22 08:52:15', 0, 0, 0),
(16, 'KSNK', 'Khoa Kiểm Soát Nhiễm Khuẩn', 'Khoa', NULL, NULL, NULL, NULL, NULL, 1, '2026-05-22 08:52:15', '2026-05-22 08:52:15', 0, 0, 0),
(17, 'DIDU', 'Khoa Dinh Dưỡng', 'Khoa', NULL, NULL, NULL, NULL, NULL, 1, '2026-05-22 08:52:15', '2026-05-22 08:52:15', 0, 0, 0),
(18, 'K142', 'Trung Tâm Đột Quỵ', 'Khoa', NULL, NULL, NULL, NULL, 75, 1, '2026-05-22 08:52:15', '2026-05-22 08:52:15', 0, 0, 0),
(19, 'K36', 'Khoa Huyết Học', 'Khoa', NULL, NULL, NULL, NULL, NULL, 1, '2026-05-22 08:52:15', '2026-05-22 08:52:15', 0, 0, 0),
(20, 'PCNTT', 'Phòng Công Nghệ Thông Tin', 'Phòng', NULL, NULL, NULL, NULL, NULL, 1, '2026-05-22 08:52:15', '2026-05-22 08:52:15', 0, 0, 0),
(21, 'TCKT', 'Phòng Tài Chính Kế Toán', 'Phòng', NULL, NULL, NULL, NULL, NULL, 1, '2026-05-22 08:52:15', '2026-05-22 08:52:15', 0, 0, 0),
(22, 'PVT', 'Phòng CSHT&TTB', 'Phòng', NULL, NULL, NULL, NULL, NULL, 1, '2026-05-22 08:52:15', '2026-05-22 08:52:15', 0, 0, 0),
(23, 'K26', 'Khoa Gây Mê Hồi Sức', 'Khoa', NULL, NULL, NULL, NULL, NULL, 1, '2026-05-22 08:52:15', '2026-05-22 08:52:15', 0, 0, 0),
(24, 'K08', 'Khoa Nội Tiết - ĐTĐ', 'Khoa', NULL, NULL, NULL, NULL, 50, 1, '2026-05-22 08:52:15', '2026-05-22 08:52:15', 0, 0, 0),
(25, 'K20', 'Khoa Phẫu Thuật Thần Kinh Cột Sống', 'Khoa', NULL, NULL, NULL, NULL, 95, 1, '2026-05-22 08:52:15', '2026-05-22 08:52:15', 0, 0, 0),
(26, 'K50', 'Khoa Nội Dị Ứng - Hô Hấp', 'Khoa', NULL, NULL, NULL, NULL, 75, 1, '2026-05-22 08:52:15', '2026-05-22 08:52:15', 0, 0, 0),
(27, 'K01', 'Khoa Khám Bệnh', 'Khoa', NULL, NULL, NULL, NULL, NULL, 1, '2026-05-22 08:52:15', '2026-05-22 08:52:15', 0, 0, 0),
(28, 'NT', 'Nhà Thuốc', 'Khoa', NULL, NULL, NULL, NULL, NULL, 1, '2026-05-22 08:52:15', '2026-05-22 08:52:15', 0, 0, 0),
(29, 'K03', 'Khoa Nội A – Lão khoa', 'Khoa', NULL, NULL, NULL, NULL, 70, 1, '2026-05-22 08:52:15', '2026-05-22 08:52:15', 0, 0, 0),
(30, 'K041', 'Khoa Nội Tim Mạch 1', 'Khoa', NULL, NULL, NULL, NULL, 80, 1, '2026-05-22 08:52:15', '2026-05-22 08:52:15', 0, 0, 0),
(31, 'K042', 'Khoa Nội Tim Mạch 2', 'Khoa', NULL, NULL, NULL, NULL, 80, 1, '2026-05-22 08:52:15', '2026-05-22 08:52:15', 0, 0, 0),
(32, 'K05', 'Khoa Nội Tiêu Hóa', 'Khoa', NULL, NULL, NULL, NULL, 75, 1, '2026-05-22 08:52:15', '2026-05-22 08:52:15', 0, 0, 0),
(33, 'K06', 'Khoa Nội Cơ Xương Khớp', 'Khoa', NULL, NULL, NULL, NULL, 65, 1, '2026-05-22 08:52:15', '2026-05-22 08:52:15', 0, 0, 0),
(34, 'K07', 'Khoa Nội Thận - Tiết Niệu - Lọc Máu', 'Khoa', NULL, NULL, NULL, NULL, 60, 1, '2026-05-22 08:52:15', '2026-05-22 08:52:15', 0, 0, 0),
(35, 'K10', 'Khoa Nội Huyết Học Lâm Sàng', 'Khoa', NULL, NULL, NULL, NULL, 60, 1, '2026-05-22 08:52:15', '2026-05-22 08:52:15', 0, 0, 0),
(36, 'K11', 'Khoa Nhiễm Khuẩn Tổng Hợp', 'Khoa', NULL, NULL, NULL, NULL, 70, 1, '2026-05-22 08:52:15', '2026-05-22 08:52:15', 0, 0, 0),
(37, 'K13', 'Khoa Da Liễu', 'Khoa', NULL, NULL, NULL, NULL, 30, 1, '2026-05-22 08:52:15', '2026-05-22 08:52:15', 0, 0, 0),
(38, 'K16', 'Khoa Y Học Cổ Truyền', 'Khoa', NULL, NULL, NULL, NULL, 35, 1, '2026-05-22 08:52:15', '2026-05-22 08:52:15', 0, 0, 0),
(39, 'K18', 'Khoa Nhi - Sơ sinh', 'Khoa', NULL, NULL, NULL, NULL, 70, 1, '2026-05-22 08:52:15', '2026-05-22 08:52:15', 0, 0, 0),
(40, 'K19', 'Khoa Ngoại Tổng Hợp', 'Khoa', NULL, NULL, NULL, NULL, 90, 1, '2026-05-22 08:52:15', '2026-05-22 08:52:15', 0, 0, 0),
(41, 'K21', 'Khoa Phẫu Thuật TM Lồng Ngực', 'Khoa', NULL, NULL, NULL, NULL, 45, 1, '2026-05-22 08:52:15', '2026-05-22 08:52:15', 0, 0, 0),
(42, 'K22', 'Khoa Ngoại Tiêu Hóa', 'Khoa', NULL, NULL, NULL, NULL, 95, 1, '2026-05-22 08:52:15', '2026-05-22 08:52:15', 0, 0, 0),
(43, 'K23', 'Khoa Ngoại Thận - Tiết Niệu', 'Khoa', NULL, NULL, NULL, NULL, 80, 1, '2026-05-22 08:52:15', '2026-05-22 08:52:15', 0, 0, 0),
(44, 'K27', 'Khoa Phụ Sản', 'Khoa', NULL, NULL, NULL, NULL, 90, 1, '2026-05-22 08:52:15', '2026-05-22 08:52:15', 0, 0, 0),
(45, 'K28', 'Khoa Tai Mũi Họng', 'Khoa', NULL, NULL, NULL, NULL, 50, 1, '2026-05-22 08:52:15', '2026-05-22 08:52:15', 0, 0, 0),
(46, 'K29', 'Khoa Răng Hàm Mặt', 'Khoa', NULL, NULL, NULL, NULL, 35, 1, '2026-05-22 08:52:15', '2026-05-22 08:52:15', 0, 0, 0),
(47, 'K30', 'Khoa Mắt', 'Khoa', NULL, NULL, NULL, NULL, 30, 1, '2026-05-22 08:52:15', '2026-05-22 08:52:15', 0, 0, 0),
(48, 'K31', 'Khoa Phục Hồi Chức Năng', 'Khoa', NULL, NULL, NULL, NULL, 45, 1, '2026-05-22 08:52:15', '2026-05-22 08:52:15', 0, 0, 0),
(49, 'K37', 'Khoa Hóa Sinh', 'Khoa', NULL, NULL, NULL, NULL, NULL, 1, '2026-05-22 08:52:15', '2026-05-22 08:52:15', 0, 0, 0),
(50, 'K38', 'Khoa Vi Sinh', 'Khoa', NULL, NULL, NULL, NULL, NULL, 1, '2026-05-22 08:52:15', '2026-05-22 08:52:15', 0, 0, 0),
(51, 'K39', 'Khoa X-Quang', 'Khoa', NULL, NULL, NULL, NULL, NULL, 1, '2026-05-22 08:52:15', '2026-05-22 08:52:15', 0, 0, 0),
(52, 'K40', 'Khoa Thăm Dò Chức Năng', 'Khoa', NULL, NULL, NULL, NULL, NULL, 1, '2026-05-22 08:52:15', '2026-05-22 08:52:15', 0, 0, 0),
(53, 'K42', 'Khoa Giải Phẫu Bệnh', 'Khoa', NULL, NULL, NULL, NULL, NULL, 1, '2026-05-22 08:52:15', '2026-05-22 08:52:15', 0, 0, 0),
(54, 'KHTH', 'Kế Hoạch Tổng Hợp', 'Khoa', NULL, NULL, NULL, NULL, NULL, 1, '2026-05-22 08:52:15', '2026-05-22 08:52:15', 0, 0, 0),
(55, 'K51', 'Trung Tâm Tim Mạch', 'Trung tâm', NULL, NULL, NULL, NULL, NULL, 1, '2026-05-22 08:52:15', '2026-05-22 08:52:15', 0, 0, 0),
(56, 'K481', 'Khoa Hồi Sức Tích Cực', 'Khoa', NULL, NULL, NULL, NULL, 55, 1, '2026-05-22 08:52:15', '2026-05-22 08:52:15', 0, 0, 0),
(57, 'K482', 'Khoa Hồi Sức Tích Cực - Ngoại Khoa', 'Khoa', NULL, NULL, NULL, NULL, 45, 1, '2026-05-22 08:52:15', '2026-05-22 08:52:15', 0, 0, 0),
(58, 'BGD', 'Ban Giám đốc', 'Khoa', NULL, NULL, NULL, NULL, NULL, 1, '2026-05-22 08:52:15', '2026-05-22 08:52:15', 0, 0, 0),
(59, 'TCCB', 'Tổ Chức Cán Bộ', 'Khoa', NULL, NULL, NULL, NULL, NULL, 1, '2026-05-22 08:52:15', '2026-05-22 08:52:15', 0, 0, 0),
(60, 'DTCDT', 'Đào Tạo Chỉ Đạo Tuyến', 'Khoa', NULL, NULL, NULL, NULL, NULL, 1, '2026-05-22 08:52:15', '2026-05-22 08:52:15', 0, 0, 0),
(61, 'K272', 'Trung Tâm Hỗ Trợ Sinh Sản', 'Khoa', NULL, NULL, NULL, NULL, 8, 1, '2026-05-22 08:52:15', '2026-05-22 08:52:15', 0, 0, 0),
(62, 'K99.1', 'Khoa điều trị bệnh nhân COVID không triệu chứng', 'Khoa', NULL, NULL, NULL, NULL, NULL, 1, '2026-05-22 08:52:15', '2026-05-22 08:52:15', 0, 0, 0),
(63, 'DVSL', 'Đơn Vị Chẩn Đoán Trước Sinh Và Sơ Sinh', 'Đơn vị', NULL, NULL, NULL, NULL, NULL, 1, '2026-05-22 08:52:15', '2026-05-22 08:52:15', 0, 0, 0),
(64, 'K112', 'Khoa Virut- Ký sinh trùng', 'Khoa', NULL, NULL, NULL, NULL, 40, 1, '2026-05-22 08:52:15', '2026-05-22 08:52:15', 0, 0, 0),
(65, 'PQT', 'Phòng Quản Trị', 'Phòng', NULL, NULL, NULL, NULL, NULL, 1, '2026-05-22 08:52:15', '2026-05-22 08:52:15', 0, 0, 0),
(66, 'K46', 'Khoa Di truyền Và Sinh Học Phân Tử', 'Khoa', NULL, NULL, NULL, NULL, NULL, 1, '2026-05-22 08:52:15', '2026-05-22 08:52:15', 0, 0, 0),
(67, 'DTN', 'Đoàn Thanh Niên', 'Khoa', NULL, NULL, NULL, NULL, NULL, 1, '2026-05-22 08:52:15', '2026-05-22 08:52:15', 0, 0, 0),
(68, 'PTTHTM', 'Khoa Phẫu thuật tạo hình thẩm mỹ', 'Khoa', NULL, NULL, NULL, NULL, NULL, 1, '2026-05-22 08:52:15', '2026-05-22 08:52:15', 0, 0, 0),
(69, 'DUMD', 'Khoa Dị ứng - Miễn dịch lâm sàng', 'Khoa', NULL, NULL, NULL, NULL, 30, 1, '2026-05-22 08:52:15', '2026-05-22 08:52:15', 0, 0, 0),
(70, 'NTH', 'NT', 'Khoa', NULL, NULL, NULL, NULL, NULL, 1, '2026-05-22 08:52:15', '2026-05-22 08:52:15', 0, 0, 0),
(71, 'TTDVTH', 'Trung tâm dịch vụ tổng hợp', 'Trung tâm', NULL, NULL, NULL, NULL, NULL, 1, '2026-05-22 08:52:15', '2026-05-22 08:52:15', 0, 0, 0),
(72, 'K49', 'Khoa Chống Độc', 'Khoa', NULL, NULL, NULL, NULL, 35, 1, '2026-05-22 08:52:15', '2026-05-22 08:52:15', 0, 0, 0),
(73, 'KCLSBVDC', 'Khu Cận Lâm Sàng (BVDC)', 'Khoa', NULL, NULL, NULL, NULL, NULL, 1, '2026-05-22 08:52:15', '2026-05-22 08:52:15', 0, 0, 0),
(74, 'AL', 'Tủ trực công ty An Lộc', 'Khoa', NULL, NULL, NULL, NULL, NULL, 1, '2026-05-22 08:52:15', '2026-05-22 08:52:15', 0, 0, 0),
(75, 'VT01', 'Văn Thư', 'Khoa', NULL, NULL, NULL, NULL, NULL, 1, '2026-05-22 08:52:15', '2026-05-22 08:52:15', 0, 0, 0),
(76, 'TTHS', 'Trung tâm hồi sức tích cực số 1', 'Khoa', NULL, NULL, NULL, NULL, NULL, 1, '2026-05-22 08:52:15', '2026-05-22 08:52:15', 0, 0, 0),
(77, 'TSL', 'Tổ Sàng Lọc', 'Khoa', NULL, NULL, NULL, NULL, NULL, 1, '2026-05-22 08:52:15', '2026-05-22 08:52:15', 0, 0, 0),
(78, 'K25', 'Khoa Bỏng', 'Khoa', NULL, NULL, NULL, NULL, 35, 1, '2026-05-22 08:52:15', '2026-05-22 08:52:15', 0, 0, 0),
(79, 'K99.2', 'Khoa điều trị COVID Nhẹ và Trung Bình', 'Khoa', NULL, NULL, NULL, NULL, NULL, 1, '2026-05-22 08:52:15', '2026-05-22 08:52:15', 0, 0, 0),
(80, 'HCHC', 'Phòng Hành Chính - Hậu Cần (BVDC)', 'Phòng', NULL, NULL, NULL, NULL, NULL, 1, '2026-05-22 08:52:15', '2026-05-22 08:52:15', 0, 0, 0),
(81, 'KSNKBVDC', 'Khu Kiểm Soát Nhiễm Khuẩn (BVDC)', 'Khoa', NULL, NULL, NULL, NULL, NULL, 1, '2026-05-22 08:52:15', '2026-05-22 08:52:15', 0, 0, 0),
(82, 'KXNQT', 'Khoa Xét Nghiệm BVQT', 'Khoa', NULL, NULL, NULL, NULL, NULL, 1, '2026-05-22 08:52:15', '2026-05-22 08:52:15', 0, 0, 0),
(83, 'KYC', 'Đơn Vị CSSK Theo Yêu Cầu', 'Đơn vị', NULL, NULL, NULL, NULL, NULL, 1, '2026-05-22 08:52:15', '2026-05-22 08:52:15', 0, 0, 0),
(84, 'PHCQT', 'Phòng Hành Chính Quản Trị', 'Phòng', NULL, NULL, NULL, NULL, NULL, 1, '2026-05-22 08:52:15', '2026-05-22 08:52:15', 0, 0, 0),
(85, 'K02', 'Khoa Cấp Cứu', 'Khoa', NULL, NULL, NULL, NULL, 5, 1, '2026-05-22 08:52:15', '2026-05-22 08:52:15', 0, 0, 0),
(86, 'PDD', 'Phòng điều dưỡng', 'Phong', NULL, NULL, NULL, NULL, NULL, 1, '2026-06-13 22:29:52', '2026-06-13 22:29:52', 9, 9, 0);

-- --------------------------------------------------------

--
-- Table structure for table `dm_loai_hinh_dao_tao`
--

CREATE TABLE `dm_loai_hinh_dao_tao` (
  `id` int(11) NOT NULL,
  `ma_loai_hinh` varchar(20) NOT NULL,
  `ten_loai_hinh` varchar(200) NOT NULL,
  `mo_ta` varchar(500) DEFAULT NULL,
  `thu_tu` int(11) DEFAULT 0,
  `trang_thai` tinyint(4) DEFAULT 1,
  `ngay_tao` datetime DEFAULT current_timestamp(),
  `ngay_cap_nhat` datetime DEFAULT current_timestamp(),
  `nguoi_tao` int(11) DEFAULT NULL,
  `nguoi_cap_nhat` int(11) DEFAULT NULL,
  `da_xoa` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `dm_loai_hinh_dao_tao`
--

INSERT INTO `dm_loai_hinh_dao_tao` (`id`, `ma_loai_hinh`, `ten_loai_hinh`, `mo_ta`, `thu_tu`, `trang_thai`, `ngay_tao`, `ngay_cap_nhat`, `nguoi_tao`, `nguoi_cap_nhat`, `da_xoa`) VALUES
(1, 'CME', 'Đào tạo y khoa liên tục (CME)', 'Cập nhật kiến thức định kỳ cho cán bộ y tế', 1, 1, '2026-04-21 23:03:36', '2026-04-21 23:03:36', 1, 1, 0),
(2, 'TOT', 'Đào tạo giảng viên (TOT)', 'Training of Trainers - đào tạo người đào tạo lại', 2, 1, '2026-04-21 23:03:36', '2026-04-21 23:03:36', 1, 1, 0),
(3, 'CDT', 'Chỉ đạo tuyến', 'Chuyển giao kỹ thuật cho tuyến dưới', 3, 1, '2026-04-21 23:03:36', '2026-04-21 23:03:36', 1, 1, 0),
(4, 'CHUYEN', 'Đào tạo chuyên sâu', 'Đào tạo chuyên khoa định hướng / chuyên khoa I/II', 4, 1, '2026-04-21 23:03:36', '2026-04-21 23:03:36', 1, 1, 0),
(5, 'NGAN', 'Đào tạo ngắn hạn', 'Lớp tập huấn ngắn ngày', 5, 1, '2026-04-21 23:03:36', '2026-04-21 23:03:36', 1, 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `dm_nckh_cap_do`
--

CREATE TABLE `dm_nckh_cap_do` (
  `id` int(11) NOT NULL,
  `ma_cap_do` varchar(20) NOT NULL,
  `ten_cap_do` varchar(100) NOT NULL,
  `mo_ta` varchar(255) DEFAULT NULL,
  `thu_tu` int(11) NOT NULL DEFAULT 0,
  `trang_thai` tinyint(4) NOT NULL DEFAULT 1,
  `ngay_tao` datetime DEFAULT current_timestamp(),
  `ngay_cap_nhat` datetime DEFAULT current_timestamp(),
  `nguoi_tao` int(11) DEFAULT NULL,
  `nguoi_cap_nhat` int(11) DEFAULT NULL,
  `da_xoa` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `dm_nckh_cap_do`
--

INSERT INTO `dm_nckh_cap_do` (`id`, `ma_cap_do`, `ten_cap_do`, `mo_ta`, `thu_tu`, `trang_thai`, `ngay_tao`, `ngay_cap_nhat`, `nguoi_tao`, `nguoi_cap_nhat`, `da_xoa`) VALUES
(1, 'CS', 'Cơ sở', 'Đề tài cấp cơ sở (bệnh viện)', 1, 1, '2026-04-27 16:55:31', '2026-04-27 23:04:21', 1, 1, 0),
(2, 'TINH', 'Cấp tỉnh', 'Đề tài cấp tỉnh / sở', 2, 1, '2026-04-27 16:55:31', '2026-04-27 16:55:31', 1, 1, 0),
(3, 'QG', 'Cấp quốc gia', 'Đề tài cấp Bộ / Quốc gia', 3, 1, '2026-04-27 16:55:31', '2026-04-27 16:55:31', 1, 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `dm_nckh_the_loai`
--

CREATE TABLE `dm_nckh_the_loai` (
  `id` int(11) NOT NULL,
  `ma_the_loai` varchar(20) NOT NULL,
  `ten_the_loai` varchar(150) NOT NULL,
  `mo_ta` varchar(255) DEFAULT NULL,
  `thu_tu` int(11) NOT NULL DEFAULT 0,
  `trang_thai` tinyint(4) NOT NULL DEFAULT 1,
  `ngay_tao` datetime DEFAULT current_timestamp(),
  `ngay_cap_nhat` datetime DEFAULT current_timestamp(),
  `nguoi_tao` int(11) DEFAULT NULL,
  `nguoi_cap_nhat` int(11) DEFAULT NULL,
  `da_xoa` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `dm_nckh_the_loai`
--

INSERT INTO `dm_nckh_the_loai` (`id`, `ma_the_loai`, `ten_the_loai`, `mo_ta`, `thu_tu`, `trang_thai`, `ngay_tao`, `ngay_cap_nhat`, `nguoi_tao`, `nguoi_cap_nhat`, `da_xoa`) VALUES
(1, 'DETAI', 'Đề tài NCKH', 'Đề tài nghiên cứu khoa học', 1, 1, '2026-04-27 16:55:31', '2026-04-27 16:55:31', 1, 1, 0),
(2, 'SK', 'Sáng kiến / Sáng tạo', 'Sáng kiến cải tiến, giải pháp hữu ích', 2, 1, '2026-04-27 16:55:31', '2026-04-27 16:55:31', 1, 1, 0),
(3, 'HN', 'Báo cáo Hội nghị', 'Báo cáo trình bày tại hội nghị', 3, 1, '2026-04-27 16:55:31', '2026-04-27 23:04:31', 1, 1, 1),
(4, 'BAIBAO', 'Bài báo khoa học', 'Bài báo đăng tạp chí trong/ngoài nước', 4, 1, '2026-04-27 16:55:31', '2026-04-27 16:55:31', 1, 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `dm_nguoi_dung`
--

CREATE TABLE `dm_nguoi_dung` (
  `id` int(11) NOT NULL,
  `nhan_vien_id` int(11) DEFAULT NULL,
  `tai_khoan` varchar(50) NOT NULL,
  `mat_khau` varchar(255) NOT NULL,
  `nhom_tai_khoan_id` int(11) DEFAULT 0,
  `trang_thai` tinyint(4) DEFAULT 1,
  `lan_dang_nhap_cuoi` datetime DEFAULT NULL,
  `ngay_tao` datetime DEFAULT current_timestamp(),
  `ngay_cap_nhat` datetime DEFAULT current_timestamp(),
  `nguoi_tao` int(11) DEFAULT NULL,
  `nguoi_cap_nhat` int(11) DEFAULT NULL,
  `da_xoa` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `dm_nguoi_dung`
--

INSERT INTO `dm_nguoi_dung` (`id`, `nhan_vien_id`, `tai_khoan`, `mat_khau`, `nhom_tai_khoan_id`, `trang_thai`, `lan_dang_nhap_cuoi`, `ngay_tao`, `ngay_cap_nhat`, `nguoi_tao`, `nguoi_cap_nhat`, `da_xoa`) VALUES
(1, NULL, 'admin', '$2y$10$o7moD1j3ZFJHYuVzZDsaV.VheR0788K7Ck1mO2nsuorCOyiWc1qom', 1, 1, '2026-06-14 21:07:23', '2026-04-20 17:44:05', '2026-04-20 17:44:05', NULL, NULL, 0),
(2, 1, 'bs.an', '$2y$10$jvpYzIdqDcSemEdQlWGev.Z7CRKAttDuc4zEUGn2qN3fSwKQmgQgq', 3, 1, NULL, '2026-04-21 16:58:42', '2026-04-21 16:58:42', 1, 1, 0),
(3, 3, 'bs.cuong', '$2y$10$jvpYzIdqDcSemEdQlWGev.Z7CRKAttDuc4zEUGn2qN3fSwKQmgQgq', 3, 1, NULL, '2026-04-21 16:58:42', '2026-04-21 16:58:42', 1, 1, 0),
(4, 2, 'bs.binh', '$2y$10$jvpYzIdqDcSemEdQlWGev.Z7CRKAttDuc4zEUGn2qN3fSwKQmgQgq', 2, 1, NULL, '2026-04-21 16:58:42', '2026-04-21 16:58:42', 1, 1, 0),
(5, 15, 'cv.trang', '$2y$10$jvpYzIdqDcSemEdQlWGev.Z7CRKAttDuc4zEUGn2qN3fSwKQmgQgq', 4, 1, NULL, '2026-04-21 16:58:42', '2026-04-21 16:58:42', 1, 1, 0),
(6, 14, 'nckh.zung', '$2y$10$jvpYzIdqDcSemEdQlWGev.Z7CRKAttDuc4zEUGn2qN3fSwKQmgQgq', 5, 1, NULL, '2026-04-21 16:58:42', '2026-04-21 22:33:14', 1, 1, 0),
(7, 1, 'locxoai', '$2y$10$TBA/Ok5OSZfNVDv61rTeGu9F8bZC16YYtCa20BpwOaajHXmb6TOBW', 4, 1, '2026-04-27 09:54:03', '2026-04-21 22:33:42', '2026-04-27 09:49:04', 1, 1, 0),
(8, 8, 'khoa_cntt', '$2y$10$JDv4HGupyeCnJeYFPbvwy.qZYBkCsFByH4isA1cpFghI1yF4Cy8x2', 3, 1, '2026-05-04 20:32:09', '2026-04-29 20:27:10', '2026-04-29 20:27:10', 1, 1, 0),
(9, NULL, 'lena', '$2y$10$V/gGJsW7dcRKNJbZHHscJevx5VV5L2Q299obYXTKEwlA31Rrb89Z2', 4, 1, '2026-06-14 16:38:42', '2026-06-11 20:45:35', '2026-06-13 23:05:12', 1, 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `dm_nhan_vien`
--

CREATE TABLE `dm_nhan_vien` (
  `id` int(11) NOT NULL,
  `benh_vien_id` int(11) NOT NULL,
  `ma_nv` varchar(50) NOT NULL,
  `ho_ten` varchar(100) NOT NULL,
  `ngay_sinh` date DEFAULT NULL,
  `gioi_tinh` varchar(10) DEFAULT NULL,
  `chuc_danh` varchar(100) DEFAULT NULL,
  `khoa_phong_id` int(11) DEFAULT NULL,
  `trinh_do` varchar(100) DEFAULT NULL,
  `chuyen_khoa` varchar(200) DEFAULT NULL,
  `dien_thoai` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `dia_chi` varchar(300) DEFAULT NULL,
  `trang_thai` tinyint(4) DEFAULT 1,
  `ngay_tao` datetime DEFAULT current_timestamp(),
  `ngay_cap_nhat` datetime DEFAULT current_timestamp(),
  `nguoi_tao` int(11) DEFAULT NULL,
  `nguoi_cap_nhat` int(11) DEFAULT NULL,
  `da_xoa` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `dm_nhan_vien`
--

INSERT INTO `dm_nhan_vien` (`id`, `benh_vien_id`, `ma_nv`, `ho_ten`, `ngay_sinh`, `gioi_tinh`, `chuc_danh`, `khoa_phong_id`, `trinh_do`, `chuyen_khoa`, `dien_thoai`, `email`, `dia_chi`, `trang_thai`, `ngay_tao`, `ngay_cap_nhat`, `nguoi_tao`, `nguoi_cap_nhat`, `da_xoa`) VALUES
(1, 1, 'NV001', 'Nguyễn Văn An', '1975-03-12', 'Nam', 'Trưởng khoa', 1, 'Tiến sĩ y khoa', 'Nội tim mạch', '0912345001', 'an.nguyen@bvhnda.vn', 'TP. Vinh, Nghệ An', 1, '2026-04-21 16:58:42', '2026-04-21 16:58:42', 1, 1, 0),
(2, 1, 'NV002', 'Trần Thị Bình', '1982-07-25', 'Nữ', 'Bác sĩ', 1, 'Thạc sĩ', 'Nội tiêu hóa', '0912345002', 'binh.tran@bvhnda.vn', 'TP. Vinh, Nghệ An', 1, '2026-04-21 16:58:42', '2026-04-21 16:58:42', 1, 1, 0),
(3, 1, 'NV003', 'Lê Văn Cường', '1970-11-03', 'Nam', 'Trưởng khoa', 2, 'PGS. TS', 'Ngoại tiêu hóa', '0912345003', 'cuong.le@bvhnda.vn', 'TP. Vinh, Nghệ An', 1, '2026-04-21 16:58:42', '2026-04-21 16:58:42', 1, 1, 0),
(4, 1, 'NV004', 'Phạm Thị Dung', '1985-05-18', 'Nữ', 'Bác sĩ', 2, 'Bác sĩ CKI', 'Ngoại chấn thương', '0912345004', 'dung.pham@bvhnda.vn', 'TP. Vinh, Nghệ An', 1, '2026-04-21 16:58:42', '2026-04-21 16:58:42', 1, 1, 0),
(5, 1, 'NV005', 'Hoàng Văn Em', '1978-09-22', 'Nam', 'Bác sĩ', 2, 'Thạc sĩ', 'Ngoại tiết niệu', '0912345005', 'em.hoang@bvhnda.vn', 'TP. Vinh, Nghệ An', 1, '2026-04-21 16:58:42', '2026-04-21 16:58:42', 1, 1, 0),
(6, 1, 'NV006', 'Vũ Thị Giang', '1980-02-14', 'Nữ', 'Trưởng khoa', 3, 'Tiến sĩ', 'Sản phụ khoa', '0912345006', 'giang.vu@bvhnda.vn', 'TP. Vinh, Nghệ An', 1, '2026-04-21 16:58:42', '2026-04-21 16:58:42', 1, 1, 0),
(7, 1, 'NV007', 'Đỗ Thị Hà', '1988-06-30', 'Nữ', 'Bác sĩ', 3, 'Thạc sĩ', 'Sản phụ khoa', '0912345007', 'ha.do@bvhnda.vn', 'TP. Vinh, Nghệ An', 1, '2026-04-21 16:58:42', '2026-04-21 16:58:42', 1, 1, 0),
(8, 1, 'NV008', 'Bùi Văn Khánh', '1979-12-08', 'Nam', 'Trưởng khoa', 4, 'Tiến sĩ', 'Nhi khoa', '0912345008', 'khanh.bui@bvhnda.vn', 'TP. Vinh, Nghệ An', 1, '2026-04-21 16:58:42', '2026-04-21 16:58:42', 1, 1, 0),
(9, 1, 'NV009', 'Ngô Thị Linh', '1986-04-17', 'Nữ', 'Bác sĩ', 4, 'Thạc sĩ', 'Nhi sơ sinh', '0912345009', 'linh.ngo@bvhnda.vn', 'TP. Vinh, Nghệ An', 1, '2026-04-21 16:58:42', '2026-04-21 16:58:42', 1, 1, 0),
(10, 1, 'NV010', 'Đặng Văn Minh', '1972-08-05', 'Nam', 'Trưởng khoa', 5, 'PGS. TS', 'HSCC', '0912345010', 'minh.dang@bvhnda.vn', 'TP. Vinh, Nghệ An', 1, '2026-04-21 16:58:42', '2026-04-21 16:58:42', 1, 1, 0),
(11, 1, 'NV011', 'Trịnh Thị Nga', '1984-10-29', 'Nữ', 'Bác sĩ', 5, 'Thạc sĩ', 'Gây mê hồi sức', '0912345011', 'nga.trinh@bvhnda.vn', 'TP. Vinh, Nghệ An', 1, '2026-04-21 16:58:42', '2026-04-21 16:58:42', 1, 1, 0),
(12, 1, 'NV012', 'Lý Văn Phú', '1981-01-11', 'Nam', 'Trưởng khoa', 6, 'Tiến sĩ', 'CĐHA', '0912345012', 'phu.ly@bvhnda.vn', 'TP. Vinh, Nghệ An', 1, '2026-04-21 16:58:42', '2026-04-21 16:58:42', 1, 1, 0),
(13, 1, 'NV013', 'Phan Thị Quỳnh', '1990-03-07', 'Nữ', 'Kỹ thuật viên', 6, 'Cử nhân KTYH', 'CT - MRI', '0912345013', 'quynh.phan@bvhnda.vn', 'TP. Vinh, Nghệ An', 1, '2026-04-21 16:58:42', '2026-04-21 16:58:42', 1, 1, 0),
(14, 1, 'NV014', 'Cao Văn Sơn', '1974-06-21', 'Nam', 'Trưởng phòng', 7, 'Thạc sĩ QLYT', NULL, '0912345014', 'son.cao@bvhnda.vn', 'TP. Vinh, Nghệ An', 1, '2026-04-21 16:58:42', '2026-04-21 16:58:42', 1, 1, 0),
(15, 1, 'NV015', 'Dương Thị Trang', '1987-11-02', 'Nữ', 'Chuyên viên', 7, 'Cử nhân', NULL, '0912345015', 'trang.duong@bvhnda.vn', 'TP. Vinh, Nghệ An', 1, '2026-04-21 16:58:42', '2026-04-21 16:58:42', 1, 1, 0),
(16, 1, 'NV016', 'Hồ Văn Uy', '1976-09-15', 'Nam', 'Trưởng phòng', 8, 'Thạc sĩ', NULL, '0912345016', 'uy.ho@bvhnda.vn', 'TP. Vinh, Nghệ An', 1, '2026-04-21 16:58:42', '2026-04-21 16:58:42', 1, 1, 0),
(17, 1, 'NV017', 'Lương Thị Vân', '1989-07-28', 'Nữ', 'Chuyên viên', 8, 'Cử nhân', NULL, '0912345017', 'van.luong@bvhnda.vn', 'TP. Vinh, Nghệ An', 1, '2026-04-21 16:58:42', '2026-04-21 16:58:42', 1, 1, 0),
(18, 1, 'NV018', 'Mai Văn Xuân', '1973-04-09', 'Nam', 'Trưởng phòng', 9, 'Tiến sĩ', 'QL đào tạo', '0912345018', 'xuan.mai@bvhnda.vn', 'TP. Vinh, Nghệ An', 1, '2026-04-21 16:58:42', '2026-04-21 16:58:42', 1, 1, 0),
(19, 1, 'NV019', 'Tạ Thị Yến', '1991-12-24', 'Nữ', 'Chuyên viên', 9, 'Cử nhân', NULL, '0912345019', 'yen.ta@bvhnda.vn', 'TP. Vinh, Nghệ An', 1, '2026-04-21 16:58:42', '2026-04-21 16:58:42', 1, 1, 0),
(20, 1, 'NV020', 'Chu Văn Zũng', '1983-05-13', 'Nam', 'Nghiên cứu viên', 10, 'Thạc sĩ', 'Nghiên cứu LS', '0912345020', 'zung.chu@bvhnda.vn', 'TP. Vinh, Nghệ An', 1, '2026-04-21 16:58:42', '2026-04-21 16:58:42', 1, 1, 0),
(21, 1, 'NV021', 'Nguyễn Văn Đức', '1991-09-04', 'Nam', 'Nhân viên', 9, NULL, NULL, NULL, 'ducnvit@gmail.com', NULL, 0, '2026-04-21 22:27:44', '2026-04-21 22:36:26', 1, 1, 0),
(22, 1, '0003', 'Nguyễn Văn Hương', NULL, 'Nam', 'Giám đốc Bệnh viện', 58, 'Phó giáo sư-Tiến sỹ', 'Ngoại khoa', '0903222929', 'hoangdungnt93@gmail.com', 'Phường Hà Huy Tập-Tp Vinh- Nghệ An', 1, '2026-05-22 08:59:27', '2026-05-22 08:59:27', 0, 0, 0),
(23, 1, '0793', 'Nguyễn Ngọc Hòa', '1975-11-10', 'Nam', 'Phó Giám đốc Bệnh viện', 58, 'Tiến sĩ', 'Di truyền phóng xạ học', '0911106888', 'sytoan180881@gmail.com', 'Hưng lộc - TP Vinh', 1, '2026-05-22 08:59:27', '2026-05-22 08:59:27', 0, 0, 0),
(24, 1, '0757', 'Phạm Hồng Phương', NULL, 'Nam', 'Phó giám đốc Bệnh viện', 58, 'Tiến sĩ', 'Tim mạch', '0903258030', 'tuyethatccb@gmail.com', 'Khối Yên Sơn -Hà Huy Tập -TP Vinh', 1, '2026-05-22 08:59:27', '2026-05-22 08:59:27', 0, 0, 0),
(25, 1, '0657', 'Trịnh Xuân Nam', NULL, 'Nam', 'Phó giám đốc Bệnh viện, GĐ trung tâm ĐT và CĐT', 58, 'BSCK II', 'Hồi sức cấp cứu và chống độc', '0948520767', 'nguyenhatccb.bvdkna@gmail.com', 'Nghi Phú - Tp Vinh - Nghệ An', 1, '2026-05-22 08:59:27', '2026-05-22 08:59:27', 0, 0, 0),
(26, 1, '1316', 'Lương Thị Thu Hà', '1988-08-20', 'Nữ', NULL, NULL, 'Cử nhân TCNH', NULL, '0972342008', 'tranghr87@gmail.com', 'Khối yên Sơn, Hà Huy Tập , Tp Vinh', 1, '2026-05-22 08:59:27', '2026-05-22 08:59:27', 0, 0, 0),
(27, 1, '0662', 'Hồ Yên Ca', '1987-02-20', 'Nam', 'phó trưởng khoa Kiểm soát nhiễm khuẩn', 85, 'Thạc sĩ', 'Hồi sức cấp cứu', '0976190037', 'thuyngato171096@gmail.com', 'Số 7, Đinh Bạt Tuỵ, Khối 14, P. Trường Thi, Tp Vinh', 1, '2026-05-22 08:59:27', '2026-05-22 08:59:27', 0, 0, 0),
(28, 1, '1380', 'Phạm Phương Thảo', '1994-09-10', 'Nữ', NULL, NULL, 'Kỹ sư công nghệ đa phương tiện', NULL, '0963669606', 'Phuongdong3296@gmail.com', 'Hưng Lộc - TP Vinh - Nghệ An', 1, '2026-05-22 08:59:27', '2026-05-22 08:59:27', 0, 0, 0),
(29, 1, '1353', 'Chu Thị Khánh Ly', '1993-12-16', 'Nữ', NULL, NULL, 'CNQTKD', NULL, '0975163738', 'thuphanktqd52@gmail.com', 'Yên Hòa - Hà Huy Tập - Vinh - Nghệ An', 1, '2026-05-22 08:59:27', '2026-05-22 08:59:27', 0, 0, 0),
(30, 1, '1568', 'Lê Thị Mai Phương', '1989-06-16', 'Nữ', NULL, NULL, 'Cử nhân quản trị kinh doanh', NULL, '0948632513', NULL, 'Khối Xuân Bắc - Hưng Dũng - TP Vinh - Nghệ An', 1, '2026-05-22 08:59:27', '2026-05-22 08:59:27', 0, 0, 0),
(31, 1, '1315', 'Nguyễn Thị Minh An', '1985-07-27', 'Nữ', NULL, NULL, 'CN CĐPS', NULL, '0963028858', 'dinhnhungbvdkna@gmail.com', 'Thị trấn Diễn Châu - Diễn Châu - Nghệ An', 1, '2026-05-22 08:59:27', '2026-05-22 08:59:27', 0, 0, 0),
(32, 1, '1302', 'Nguyễn Thị Tình Nhàn', '1992-04-27', 'Nữ', NULL, NULL, 'Cử nhân y tế công cộng', NULL, '0989274592', 'nguyenthaomy2409@gmail.com', 'Vĩnh Thành - Yên Thành - Nghệ An', 1, '2026-05-22 08:59:27', '2026-05-22 08:59:27', 0, 0, 0),
(33, 1, '0647', 'Nguyễn Thị Thanh Hoài', '1973-10-21', 'Nữ', NULL, 54, 'ĐDTH', NULL, '0904244669', 'kimdung020977@gmail.com', 'Nguyễn Bỉnh Khiêm, Tân Lộc, Hưng Duũn', 1, '2026-05-22 08:59:27', '2026-05-22 08:59:27', 0, 0, 0),
(34, 1, '0064', 'Nguyễn Thị Hằng', '1971-03-15', 'Nữ', NULL, 21, 'CNKT', NULL, '0983561900', 'haiyenho208@gmail.com', 'Khối Yên Phúc A, Phường Hưng Bình, Tp Vinh, Nghệ An', 1, '2026-05-22 08:59:27', '2026-05-22 08:59:27', 0, 0, 0),
(35, 1, '0173', 'Doãn Thị Thu', '1982-06-01', 'Nữ', NULL, NULL, 'LĐ PT', NULL, '0984761861', 'anhtuanna81@gmail.com', NULL, 1, '2026-05-22 08:59:27', '2026-05-22 08:59:27', 0, 0, 0),
(36, 1, '0051', 'Nguyễn Thị Bích Toàn', '1985-02-17', 'Nữ', NULL, NULL, 'Thạc sỹ kinh tế', NULL, '0987431500', 'dr.hungthanhnguyen@gmail.com', 'Khối 12 - Phường Trung Đô - TP.Vinh - Nghệ An', 1, '2026-05-22 08:59:27', '2026-05-22 08:59:27', 0, 0, 0),
(37, 1, '044ttdv', 'Hoàng Thị Thùy Linh', NULL, 'nữ', NULL, NULL, 'Cử nhân kế toán', NULL, '0989216610', 'tranthuylinh00293@gmail.com', 'Bến Thuỷ, Tp Vinh, Nghệ An', 1, '2026-05-22 08:59:27', '2026-05-22 08:59:27', 0, 0, 0),
(38, 1, '047ttdv', 'Nguyễn Diệu Huyền', '1991-05-25', 'Nữ', NULL, NULL, 'Cử nhân Tài chính ngân hàng', NULL, NULL, 'camtra93bv@gmail.com', NULL, 1, '2026-05-22 08:59:27', '2026-05-22 08:59:27', 0, 0, 0),
(39, 1, '048ttdv', 'Nguyễn Thị Huyền A', '1985-02-08', 'Nam', NULL, NULL, 'Cử nhân kế toán', NULL, NULL, 'vothihao0101@gmail.com', NULL, 1, '2026-05-22 08:59:27', '2026-05-22 08:59:27', 0, 0, 0),
(40, 1, '213ttdv', 'Mai Thu Phương', '1987-08-27', 'Nữ', NULL, NULL, 'Cử nhân kinh tế', NULL, NULL, 'minhhuongkhth@gmail.com', NULL, 1, '2026-05-22 08:59:27', '2026-05-22 08:59:27', 0, 0, 0),
(41, 1, '229ttdv', 'Nguyễn Thị Trà My', '1993-01-05', 'Nữ', NULL, NULL, 'Cử nhân kế toán', NULL, NULL, 'tranvanduankhth@gmail.com', NULL, 1, '2026-05-22 08:59:27', '2026-05-22 08:59:27', 0, 0, 0),
(42, 1, '299ttdv', 'Nguyễn Thanh An', '1989-07-20', 'Nữ', NULL, NULL, 'Cử nhân kế toán', NULL, NULL, 'hoaithao1973@gmail.com', NULL, 1, '2026-05-22 08:59:27', '2026-05-22 08:59:27', 0, 0, 0),
(43, 1, '240ttdv', 'Nguyễn Thị Kiều Oanh', '1991-07-16', 'Nữ', NULL, NULL, 'Thạc sỹ, chuyên ngành Kế toán', NULL, NULL, 'thuong221180@gmail.com', NULL, 1, '2026-05-22 08:59:27', '2026-05-22 08:59:27', 0, 0, 0),
(44, 1, '045ttdv', 'Lê Thị Phương', '1992-09-05', 'Nữ', NULL, NULL, 'Cử nhân Tài chính ngân hàng', NULL, NULL, 'dr.tatngoc@gmail.com', NULL, 1, '2026-05-22 08:59:27', '2026-05-22 08:59:27', 0, 0, 0),
(45, 1, '220ttdv', 'Nguyễn Thị Mỹ Hạnh', '1993-07-16', 'Nữ', NULL, NULL, 'Cử nhân kế toán', NULL, NULL, 'chienbvna@gmail.com', NULL, 1, '2026-05-22 08:59:27', '2026-05-22 08:59:27', 0, 0, 0),
(46, 1, '0068', 'Phạm Thị Hoa', '1978-11-19', 'Nữ', NULL, 21, 'ĐH khác', 'Kế toán', '0983561514', NULL, 'Xuân Hùng - Hưng Lộc - TP Vinh', 1, '2026-05-22 08:59:27', '2026-05-22 08:59:27', 0, 0, 0),
(47, 1, '230ttdv', 'Trần Ngọc Hoàng', '1993-05-01', 'Nữ', NULL, NULL, 'Cao đẳng kế toán', NULL, NULL, 'hoha140391@gmail.com', NULL, 1, '2026-05-22 08:59:27', '2026-05-22 08:59:27', 0, 0, 0),
(48, 1, '0061', 'Nguyễn Thị Lam Hồng', '1973-02-14', 'Nữ', NULL, 21, 'ĐH khác', 'Kế toán', '0989187012', NULL, 'Khối Trung Đông - Hưng Dũng - TP Vinh', 1, '2026-05-22 08:59:27', '2026-05-22 08:59:27', 0, 0, 0),
(49, 1, '0052', 'Nguyễn Hà Giang', '1974-08-17', 'Nữ', NULL, 21, 'ĐH khác', 'Kế toán', '0911180838', 'dstranthidiu@gmail.com', 'Xuân Hùng - Hưng Lộc - TP Vinh', 1, '2026-05-22 08:59:27', '2026-05-22 08:59:27', 0, 0, 0),
(50, 1, '029ttdv', 'Nguyễn Thị Ngọc Lan', '1992-04-30', 'Nữ', NULL, NULL, 'CĐ kế toán', NULL, NULL, 'diemhuong0609@gmail.com', NULL, 1, '2026-05-22 08:59:27', '2026-05-22 08:59:27', 0, 0, 0),
(51, 1, '247ttdv', 'Phan Trần Lan Nhi', '1996-07-06', 'Nữ', NULL, NULL, 'Cử nhân luật', NULL, NULL, 'Dangthiquynhphuong1980@gmail.com', NULL, 1, '2026-05-22 08:59:27', '2026-05-22 08:59:27', 0, 0, 0),
(52, 1, '302ttdv', 'Nguyễn Quỳnh Trang A', '1988-05-23', 'Nữ', NULL, NULL, 'Cử nhân kinh tế', NULL, NULL, 'Kimngan114116@gmail.com', NULL, 1, '2026-05-22 08:59:27', '2026-05-22 08:59:27', 0, 0, 0),
(53, 1, '306ttdv', 'Nguyễn Quỳnh Trang B', '1990-10-28', 'Nữ', NULL, NULL, 'Cao đẳng kế toán', NULL, NULL, 'tranhoaithu0881@gmail.com', NULL, 1, '2026-05-22 08:59:27', '2026-05-22 08:59:27', 0, 0, 0),
(54, 1, '314ttdv', 'Trần Tuấn Anh', '1993-10-17', 'Nam', NULL, NULL, 'Cử nhân diều dưỡng', NULL, NULL, 'nabinbon81@gmail.com', NULL, 1, '2026-05-22 08:59:27', '2026-05-22 08:59:27', 0, 0, 0),
(55, 1, '174ttdv', 'Tống Thị Huyền', '1995-11-05', 'Nữ', NULL, NULL, 'Cao đẳng điều dưỡng', NULL, NULL, 'maithuyle1611@gmail.com', NULL, 1, '2026-05-22 08:59:27', '2026-05-22 08:59:27', 0, 0, 0),
(56, 1, '273ttdv', 'Nguyễn Thị Hân', '1995-07-17', 'Nữ', NULL, NULL, 'Cao đẳng điều dưỡng', NULL, NULL, 'hanguyen3152@gmail.com', NULL, 1, '2026-05-22 08:59:27', '2026-05-22 08:59:27', 0, 0, 0),
(57, 1, '279ttdv', 'Nguyễn Thị Ánh Phượng', '1997-03-17', 'Nữ', NULL, NULL, 'Cao đẳng điều dưỡng', NULL, NULL, 'nguyenquybong2016@gmail.com', NULL, 1, '2026-05-22 08:59:27', '2026-05-22 08:59:27', 0, 0, 0),
(58, 1, '288ttdv', 'Nguyễn Thị Thu', '1993-12-10', 'Nữ', NULL, NULL, 'Cao đẳng hộ sinh', NULL, NULL, 'duyeniu63@gmail.com', NULL, 1, '2026-05-22 08:59:27', '2026-05-22 08:59:27', 0, 0, 0),
(59, 1, '291ttdv', 'Nguyễn Thị Linh Chi', '1995-05-19', 'Nữ', NULL, NULL, 'Cao đẳng hộ sinh', NULL, NULL, NULL, NULL, 1, '2026-05-22 08:59:27', '2026-05-22 08:59:27', 0, 0, 0),
(60, 1, '292ttdv', 'Trần Thị Thùy Giang', '1993-06-12', 'nữ', NULL, NULL, 'Cao đẳng hộ sinh', NULL, NULL, 'buituyet.hndkna@gmail.com', NULL, 1, '2026-05-22 08:59:27', '2026-05-22 08:59:27', 0, 0, 0),
(61, 1, '322ttdv', 'Lê Thị Thanh Hương', '1999-02-26', 'Nữ', NULL, NULL, 'Cao đăng điều dưỡng', NULL, NULL, 'Hongnguyen17879@gmail.com', NULL, 1, '2026-05-22 08:59:27', '2026-05-22 08:59:27', 0, 0, 0),
(62, 1, '222ttdv', 'Nguyễn Thị Yến', '1998-03-16', 'Nữ', NULL, NULL, 'Cao đăng điều dưỡng', NULL, NULL, 'nguyenthamdkna@gmail.com', NULL, 1, '2026-05-22 08:59:27', '2026-05-22 08:59:27', 0, 0, 0),
(63, 1, '316ttdv', 'Nguyễn Thị Mai Sương', '1995-08-01', 'Nữ', NULL, NULL, 'Cử nhân điều dưỡng', NULL, NULL, 'mthangna@gmail.com', NULL, 1, '2026-05-22 08:59:27', '2026-05-22 08:59:27', 0, 0, 0),
(64, 1, '317ttdv', 'Lê Thị Phương Dung', '1997-01-27', 'Nữ', NULL, NULL, 'Cao đăng điều dưỡng', NULL, NULL, 'buidungbvhndkna@gmail.com', NULL, 1, '2026-05-22 08:59:27', '2026-05-22 08:59:27', 0, 0, 0),
(65, 1, '058ttdv', 'Nguyễn Thị Hoa', '1991-07-25', 'Nữ', NULL, NULL, 'Cao đăng điều dưỡng', NULL, NULL, 'Chuhuyen121284@gmail.com', NULL, 1, '2026-05-22 08:59:27', '2026-05-22 08:59:27', 0, 0, 0),
(66, 1, '301ttdv', 'Hà Thị Nhàn', '1987-10-10', 'Nam', NULL, NULL, 'Cao đăng điều dưỡng', NULL, NULL, 'Hmdung.16mar88@yahoo.com', NULL, 1, '2026-05-22 08:59:27', '2026-05-22 08:59:27', 0, 0, 0),
(67, 1, '295ttdv', 'Hồ Thị Huyền Mỹ', '1984-09-17', 'Nữ', NULL, NULL, 'Cử nhân kế toán', NULL, NULL, 'Huongtran2920@gmail.com', NULL, 1, '2026-05-22 08:59:27', '2026-05-22 08:59:27', 0, 0, 0),
(68, 1, '068ttdv', 'Thái Bá Tuấn Triều', '1992-05-27', 'Nữ', NULL, NULL, 'Cử nhân quản trị kinh doanh', NULL, NULL, 'Nguyenhienktnh@gmail.com', NULL, 1, '2026-05-22 08:59:27', '2026-05-22 08:59:27', 0, 0, 0),
(69, 1, '013ttdv', 'Nguyễn Thị Nga', '1986-10-05', 'Nữ', NULL, NULL, '12/12', NULL, NULL, 'minhhue0312@gmail.com', NULL, 1, '2026-05-22 08:59:27', '2026-05-22 08:59:27', 0, 0, 0),
(70, 1, '002ttdv', 'Hồ Thị Huyền', '1992-06-26', 'Nữ', NULL, NULL, '12/12', NULL, NULL, 'Tranhaiyen2603@gmail.com', NULL, 1, '2026-05-22 08:59:27', '2026-05-22 08:59:27', 0, 0, 0),
(71, 1, '008ttdv', 'Nguyễn Thị Hà', '1991-04-05', 'Nữ', NULL, NULL, '12/12', NULL, NULL, 'Tranthinganch@gmail.com', NULL, 1, '2026-05-22 08:59:27', '2026-05-22 08:59:27', 0, 0, 0),
(72, 1, '010ttdv', 'Nguyễn Thị Huyền D', '1996-02-04', 'Nữ', NULL, NULL, '12/12', NULL, NULL, 'hoangphuongthaodhv@gmail.com', NULL, 1, '2026-05-22 08:59:27', '2026-05-22 08:59:27', 0, 0, 0),
(73, 1, '012ttdv', 'Nguyễn Thị Hương Giang', '1993-07-08', 'Nữ', NULL, NULL, '12/12', NULL, NULL, 'nbngoc9391@gmail.com', NULL, 1, '2026-05-22 08:59:27', '2026-05-22 08:59:27', 0, 0, 0),
(74, 1, '015ttdv', 'Võ Thị Minh', '1990-03-12', 'Nữ', NULL, NULL, '12/12', NULL, NULL, 'nguyenthihang150370@gmail.com', NULL, 1, '2026-05-22 08:59:27', '2026-05-22 08:59:27', 0, 0, 0),
(75, 1, '202ttdv', 'Nguyễn Thị Hòa', '1994-08-24', 'Nữ', NULL, NULL, '12/12', NULL, NULL, 'lam.nth37@gmail.com', NULL, 1, '2026-05-22 08:59:27', '2026-05-22 08:59:27', 0, 0, 0),
(76, 1, '219ttdv', 'Vương Thị Dung', '1997-09-16', 'Nữ', NULL, NULL, '12/12', NULL, NULL, 'hoanganhnace96@gmail.com', NULL, 1, '2026-05-22 08:59:27', '2026-05-22 08:59:27', 0, 0, 0),
(77, 1, '227ttdv', 'Trần Thị Ngọc', '1991-06-04', 'Nữ', NULL, NULL, '12/12', NULL, NULL, 'hongtham2210.neu@gmail.com', NULL, 1, '2026-05-22 08:59:27', '2026-05-22 08:59:27', 0, 0, 0),
(78, 1, '231ttdv', 'Phạm Thị Thu Hương', '1995-05-01', 'Nữ', NULL, NULL, '12/12', NULL, NULL, 'tranthuylinh1993@gmail.com', NULL, 1, '2026-05-22 08:59:27', '2026-05-22 08:59:27', 0, 0, 0),
(79, 1, '245ttdv', 'Nguyễn Thị Thanh Hằng', '1994-11-16', 'Nữ', NULL, NULL, '12/12', NULL, NULL, 'hocamlyh@gmail.com', NULL, 1, '2026-05-22 08:59:27', '2026-05-22 08:59:27', 0, 0, 0),
(80, 1, '248ttdv', 'Nguyễn Thị Thuý Vui', '1985-04-25', 'Nữ', NULL, NULL, '12/12', NULL, NULL, 'buiduyennh1989@gmail.com', NULL, 1, '2026-05-22 08:59:27', '2026-05-22 08:59:27', 0, 0, 0),
(81, 1, '214ttdv', 'Nguyễn Thị Nhung', '1998-02-12', 'Nữ', NULL, NULL, '12/12', NULL, NULL, NULL, NULL, 1, '2026-05-22 08:59:27', '2026-05-22 08:59:27', 0, 0, 0),
(82, 1, '250ttdv', 'Nguyễn Thị Thắng', '1980-04-03', 'Nữ', NULL, NULL, '12/12', NULL, NULL, 'tienhungdkna@gmail.com', NULL, 1, '2026-05-22 08:59:27', '2026-05-22 08:59:27', 0, 0, 0),
(83, 1, '252ttdv', 'Hồ Thị Hà Trang', '1993-11-22', 'Nữ', NULL, NULL, '12/12', NULL, NULL, 'phamthihoa1911st@gmail.com', NULL, 1, '2026-05-22 08:59:27', '2026-05-22 08:59:27', 0, 0, 0),
(84, 1, '209ttdv', 'Võ Thị Tình', '1979-03-20', 'Nữ', NULL, NULL, '12/12', NULL, NULL, 'nguyenlamhong1973@gmail.com', NULL, 1, '2026-05-22 08:59:27', '2026-05-22 08:59:27', 0, 0, 0),
(85, 1, '256ttdv', 'Võ Thùy Linh', '1999-11-11', 'Nữ', NULL, NULL, '12/12', NULL, NULL, 'Thanhhoainguyen10091979@gmail.com', NULL, 1, '2026-05-22 08:59:27', '2026-05-22 08:59:27', 0, 0, 0),
(86, 1, '251ttdv', 'Nguyễn Thị Quyên', '1998-02-10', 'Nữ', NULL, NULL, '12/12', NULL, NULL, NULL, NULL, 1, '2026-05-22 08:59:27', '2026-05-22 08:59:27', 0, 0, 0),
(87, 1, '261ttdv', 'Đặng Thị Tuyết', '1989-11-20', 'Nữ', NULL, NULL, '12/12', NULL, NULL, 'doantrang041982@gmail.com', NULL, 1, '2026-05-22 08:59:27', '2026-05-22 08:59:27', 0, 0, 0),
(88, 1, '064ttdv', 'Phan Thị Phương', '1997-11-19', 'Nữ', NULL, NULL, '12/12', NULL, NULL, 'hagiangth6@gmail.com', NULL, 1, '2026-05-22 08:59:27', '2026-05-22 08:59:27', 0, 0, 0),
(89, 1, '305ttdv', 'Hoàng Đình Khang', '1992-08-03', 'Nữ', NULL, NULL, '12/12', NULL, NULL, 'Linh.thaolinh.kt@gmail.com', NULL, 1, '2026-05-22 08:59:27', '2026-05-22 08:59:27', 0, 0, 0),
(90, 1, '309ttdv', 'Nguyễn Thị Mỹ Lệ', '2001-12-25', 'Nữ', NULL, NULL, '12/12', NULL, NULL, 'phamgiang.na@gmail.com', NULL, 1, '2026-05-22 08:59:27', '2026-05-22 08:59:27', 0, 0, 0),
(91, 1, '323ttdv', 'Nguyễn Thị Thanh Hằng', '1994-11-16', 'nữ', NULL, NULL, '12/12', NULL, NULL, 'thecuongkct@gmai.com', NULL, 1, '2026-05-22 08:59:27', '2026-05-22 08:59:27', 0, 0, 0),
(92, 1, '324ttdv', 'Nguyễn Thị Loan', '1973-05-25', 'Nam', NULL, NULL, '12/12', NULL, NULL, 'hoainguyenpdd@gmail.com', NULL, 1, '2026-05-22 08:59:27', '2026-05-22 08:59:27', 0, 0, 0),
(93, 1, '325ttdv', 'Nguyễn Phương Thảo', '2000-08-14', 'nữ', NULL, NULL, '12/12', NULL, NULL, 'Nursingnhan1102@gmail.com', NULL, 1, '2026-05-22 08:59:27', '2026-05-22 08:59:27', 0, 0, 0),
(94, 1, '333ttdv', 'Đinh Thị Minh', '1980-11-30', 'Nữ', NULL, NULL, '12/12', NULL, NULL, 'lethanhtandkna@gmail.com', NULL, 1, '2026-05-22 08:59:27', '2026-05-22 08:59:27', 0, 0, 0),
(95, 1, '335ttdv', 'Nguyễn Thị Kim Cúc', '1991-09-28', 'Nữ', NULL, NULL, '12/12', NULL, NULL, 'hoangthapkts@gmail.com', NULL, 1, '2026-05-22 08:59:27', '2026-05-22 08:59:27', 0, 0, 0),
(96, 1, '007ttdv', 'Nguyễn Thanh Hải', '1985-07-01', 'Nữ', NULL, NULL, 'Cao đẳng kỹ thuật chế biến món ăn', NULL, NULL, 'vodinhxuan25031975@gmail.com', NULL, 1, '2026-05-22 08:59:27', '2026-05-22 08:59:27', 0, 0, 0),
(97, 1, '018ttdv', 'Dư Đức Mạnh', '1987-07-17', 'Nam', NULL, NULL, 'cao đẳng kỹ thuật chế biến món ăn', NULL, NULL, NULL, NULL, 1, '2026-05-22 08:59:27', '2026-05-22 08:59:27', 0, 0, 0),
(98, 1, '253ttdv', 'Phạm Văn Thành', '1965-07-30', 'Nam', NULL, NULL, '12/12', NULL, NULL, NULL, NULL, 1, '2026-05-22 08:59:27', '2026-05-22 08:59:27', 0, 0, 0),
(99, 1, '014ttdv', 'Phan Thị Hoài', '1990-06-23', 'Nam', NULL, NULL, '12/12', NULL, NULL, NULL, NULL, 1, '2026-05-22 08:59:27', '2026-05-22 08:59:27', 0, 0, 0),
(100, 1, '0018', 'Thái Thị Ngọc Mai', '1978-07-01', 'Nữ', NULL, 84, 'NV ĐM', NULL, '0902242099', 'drphanvanthang1988@gmail.com', 'Hưng Dũng-TP.Vinh-Nghệ An', 1, '2026-05-22 08:59:27', '2026-05-22 08:59:27', 0, 0, 0),
(101, 1, '0021', 'Võ Thị Thanh', NULL, 'Nữ', NULL, 84, 'HLYC', NULL, '01685037688', 'nguyenhungdung@gmail.com', NULL, 1, '2026-05-22 08:59:27', '2026-05-22 08:59:27', 0, 0, 0),
(102, 1, '025ttdv', 'Hoàng Thị Xuân', '1983-07-05', 'Nữ', NULL, NULL, '12/12', NULL, NULL, NULL, NULL, 1, '2026-05-22 08:59:27', '2026-05-22 08:59:27', 0, 0, 0),
(103, 1, '031ttdv', 'Nguyễn Văn Tài', '1981-09-20', 'Nam', NULL, NULL, '12/12', NULL, NULL, NULL, NULL, 1, '2026-05-22 08:59:27', '2026-05-22 08:59:27', 0, 0, 0),
(104, 1, '032ttdv', 'Phạm Sỹ Hòa', '1992-05-28', 'Nam', NULL, NULL, '12/12', NULL, NULL, NULL, NULL, 1, '2026-05-22 08:59:27', '2026-05-22 08:59:27', 0, 0, 0),
(105, 1, '0186', 'Nguyễn Thị Thu Hương', '1977-10-20', 'Nữ', 'Điều dưỡng trưởng', 52, 'Cử nhân điều dưỡng', NULL, '0989662739', NULL, 'Khối 12 P. Bến Thủy, Tp Vinh, Nghệ An', 1, '2026-05-22 08:59:27', '2026-05-22 08:59:27', 0, 0, 0),
(106, 1, '039ttdv', 'Trần Thị Minh', '1993-01-03', 'Nữ', NULL, NULL, '12/12', NULL, NULL, NULL, NULL, 1, '2026-05-22 08:59:27', '2026-05-22 08:59:27', 0, 0, 0),
(107, 1, '0229', 'Đậu Thị Hiền', NULL, 'Nữ', 'KTV trưởng', NULL, 'KTVTH', NULL, '0943172714', NULL, 'Xuân Bắc, Hưng Dũng, Nghệ An', 1, '2026-05-22 08:59:27', '2026-05-22 08:59:27', 0, 0, 0),
(108, 1, '041ttdv', 'Uông Thị Hậu', '1993-08-09', 'nữ', NULL, NULL, 'LĐPT', NULL, NULL, 'vanthuanx@gmail.com', NULL, 1, '2026-05-22 08:59:27', '2026-05-22 08:59:27', 0, 0, 0),
(109, 1, '210ttdv', 'Hoàng Thị Lệ Thúy', '1984-10-05', 'Nữ', NULL, NULL, '12/12', NULL, NULL, 'tienhung1661@gmail.com', NULL, 1, '2026-05-22 08:59:27', '2026-05-22 08:59:27', 0, 0, 0),
(110, 1, '221ttdv', 'Nguyễn Thị Tuyết', '1990-04-13', 'Nữ', NULL, NULL, '12/12', NULL, NULL, 'lananh0202.vcu@gmail.com', NULL, 1, '2026-05-22 08:59:27', '2026-05-22 08:59:27', 0, 0, 0),
(111, 1, '224ttdv', 'Nguyễn Thị Lan', '1991-06-02', 'Nữ', NULL, NULL, '12/12', NULL, NULL, 'theanh.tran@bvnghean.vn', NULL, 1, '2026-05-22 08:59:27', '2026-05-22 08:59:27', 0, 0, 0),
(112, 1, '225ttdv', 'Hồ Thị Thanh', '1987-06-26', 'Nam', NULL, NULL, '12/12', NULL, NULL, 'linhnguyen.vi9@gmail.com', NULL, 1, '2026-05-22 08:59:27', '2026-05-22 08:59:27', 0, 0, 0),
(113, 1, '233ttdv', 'Nguyễn Quốc Hồng', '1986-11-03', 'Nam', NULL, NULL, '12/12', NULL, NULL, 'thangbvdkna69@gmail.com', NULL, 1, '2026-05-22 08:59:27', '2026-05-22 08:59:27', 0, 0, 0),
(114, 1, '249ttdv', 'Đinh Thị Hiền', '1979-04-25', 'Nam', NULL, NULL, '12/12', NULL, NULL, NULL, NULL, 1, '2026-05-22 08:59:27', '2026-05-22 08:59:27', 0, 0, 0),
(115, 1, '303ttdv', 'Nguyễn Thị Tam', '1989-11-09', 'Nữ', NULL, NULL, '12/12', NULL, NULL, NULL, NULL, 1, '2026-05-22 08:59:27', '2026-05-22 08:59:27', 0, 0, 0),
(116, 1, '307ttdv', 'Bùi Thị Dung', '1979-12-17', 'Nữ', NULL, NULL, '12/12', NULL, NULL, NULL, NULL, 1, '2026-05-22 08:59:27', '2026-05-22 08:59:27', 0, 0, 0),
(117, 1, '313ttdv', 'Trần Thị Hương', '1983-09-19', 'Nữ', NULL, NULL, '12/12', NULL, NULL, 'nguyenquang107@gmail.com', NULL, 1, '2026-05-22 08:59:27', '2026-05-22 08:59:27', 0, 0, 0),
(118, 1, '315ttdv', 'Trương Đăng Thiện', '1981-04-12', 'Nữ', NULL, NULL, '12/12', NULL, NULL, 'Dung.nguyenduc07@gmail.com', NULL, 1, '2026-05-22 08:59:27', '2026-05-22 08:59:27', 0, 0, 0),
(119, 1, '328ttdv', 'Hà Thị Miên', '1980-10-20', 'Nữ', NULL, NULL, '12/12', NULL, NULL, 'bga.tbyt@gmail.com', NULL, 1, '2026-05-22 08:59:27', '2026-05-22 08:59:27', 0, 0, 0),
(120, 1, '036ttdv', 'Thái Thị Dung', '1993-01-24', 'nữ', NULL, NULL, 'Cao đăng điều dưỡng', NULL, NULL, 'hieubk020783@gmail.com', NULL, 1, '2026-05-22 08:59:27', '2026-05-22 08:59:27', 0, 0, 0),
(121, 1, '070ttdv', 'Trần Thị Thương', '1993-10-25', 'Nữ', NULL, NULL, '12/12', NULL, NULL, 'truongdkna@gmail.com', NULL, 1, '2026-05-22 08:59:27', '2026-05-22 08:59:27', 0, 0, 0),
(122, 1, '037ttdv', 'Trần Thị Hà Phương', '1990-12-04', 'Nữ', NULL, NULL, '12/12', NULL, NULL, 'taidkna@gmail.com', NULL, 1, '2026-05-22 08:59:27', '2026-05-22 08:59:27', 0, 0, 0),
(123, 1, '0230', 'Lê Thị Lanh', '1973-09-13', 'Nữ', NULL, NULL, 'CN CĐKTVXN', NULL, '0913563450', 'khanhrong81@gmail.com', 'Hưng Chính, Vinh, Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(124, 1, '217ttdv', 'Nguyễn Thị Vỹ', '1980-05-28', 'Nữ', NULL, NULL, '12/12', NULL, NULL, NULL, NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(125, 1, '242ttdv', 'Phạm Thị Vinh', '1986-08-08', 'Nữ', NULL, NULL, '12/12', NULL, NULL, 'tuanpham284@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(126, 1, '0262', 'Nguyễn Thị An', '1973-12-30', 'Nữ', NULL, 16, 'Cao đăng điều dưỡng', NULL, '0989865044', NULL, 'Khối Yên Hòa, P. Hà Huy Tập, Tp Vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(127, 1, '296ttdv', 'Nguyễn Thị Mừng', '1989-04-30', 'Nữ', NULL, NULL, '12/12', NULL, NULL, NULL, NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(128, 1, '319ttdv', 'Phạm Thị Bình', '1984-10-24', 'Nữ', NULL, NULL, '12/12', NULL, NULL, NULL, NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(129, 1, '330ttdv', 'Trần Thị Đức', '1980-10-15', 'Nữ', NULL, NULL, '12/12', NULL, NULL, 'tula.200898@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(130, 1, '334ttdv', 'Nguyễn Thị Duyên', '1982-07-29', 'Nữ', NULL, NULL, '12/12', NULL, NULL, 'Tranhuytoan@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(131, 1, '339ttdv', 'Nguyễn Thị Phước An', '1993-01-01', 'Nữ', NULL, NULL, NULL, NULL, NULL, 'Honglienktkc@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(132, 1, '006ttdv', 'Lưu Tuấn Anh', '1991-05-18', 'Nữ', NULL, NULL, 'Cao đăng điều dưỡng', NULL, NULL, 'Trangquynhbb@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(133, 1, '027ttdv', 'Nguyễn Thị Hoan', '1986-06-25', 'Nữ', NULL, NULL, NULL, NULL, NULL, 'tranhoangbk@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(134, 1, '051ttdv', 'Cao Thị Đắc', '1990-11-20', 'Nam', NULL, NULL, NULL, NULL, NULL, 'Caothang.na1@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(135, 1, '053ttdv', 'Cao Thị Thu Thủy', '1985-04-08', 'Nam', NULL, NULL, NULL, NULL, NULL, 'Thao89hui@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(136, 1, '056ttdv', 'Lưu Thị Bình', '1992-04-28', 'Nữ', NULL, NULL, NULL, NULL, NULL, 'phuthong1102@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(137, 1, '059ttdv', 'Nguyễn Thị Hồng Mỹ', '1994-04-04', 'nữ', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(138, 1, '066ttdv', 'Phan Thị Thư', '1989-05-20', 'Nam', NULL, NULL, NULL, NULL, NULL, 'ducngucdspna@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(139, 1, '067ttdv', 'Phùng Minh Tú', '1990-10-12', 'Nam', NULL, NULL, NULL, NULL, NULL, 'htthuong12@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(140, 1, '069ttdv', 'Trần Thị Hiếu', '1990-10-12', 'Nữ', NULL, NULL, NULL, NULL, NULL, 'phuonganhvo93@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(141, 1, '235ttdv', 'Nguyễn Thị Phương Anh', '1993-03-24', 'nữ', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(142, 1, '216ttdv', 'Đậu Đức Hường', '1989-04-10', 'Nam', NULL, NULL, NULL, NULL, NULL, 'vanchn22@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(143, 1, '236ttdv', 'Vũ Thị Sen', '1994-09-01', 'nữ', NULL, NULL, NULL, NULL, NULL, 'nguyenbalinh.vbn@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(144, 1, '065ttdv', 'Phan Thị Thi', '1993-03-19', 'nữ', NULL, NULL, NULL, NULL, NULL, 'minhnhuong411986@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(145, 1, '054ttdv', 'Hoàng Thị Nga', '1993-09-08', 'nữ', NULL, NULL, NULL, NULL, NULL, 'hahanana119@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(146, 1, '304ttdv', 'Nguyễn Trung Thông', '1993-08-10', 'Nam', NULL, NULL, NULL, NULL, NULL, 'hoangyencao.151@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(147, 1, '310ttdv', 'Hoàng Thị Nga', '1993-09-08', 'nữ', NULL, NULL, NULL, NULL, NULL, 'camtu1218@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(148, 1, '254ttdv', 'Trần Đức Thành', '1993-08-17', 'Nam', NULL, NULL, NULL, NULL, NULL, 'hoangthai10394@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(149, 1, '318ttdv', 'Đậu Thị Quyên', '1997-05-10', 'nữ', NULL, NULL, NULL, NULL, NULL, 'Nguyennhan4893@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(150, 1, '320ttdv', 'Hoàng Thị Hướng Dương', '1980-11-13', 'Nam', NULL, NULL, NULL, NULL, NULL, 'Leanh19895@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(151, 1, '329ttdv', 'Nguyễn Trọng Khánh', '1992-09-27', 'Nữ', NULL, NULL, NULL, NULL, NULL, 'huyen96@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(152, 1, '336ttdv', 'Nguyễn Cảnh Tiến', '1997-02-24', 'Nam', NULL, NULL, NULL, NULL, NULL, 'minhngoctdcn@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(153, 1, '337ttdv', 'Bùi Xuân Hoàng', '1991-08-06', 'Nam', NULL, NULL, NULL, NULL, NULL, 'vietnga26890@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(154, 1, '338ttdv', 'Lê Văn Mạnh', '2003-01-01', 'Nam', NULL, NULL, NULL, NULL, NULL, 'famyly.none@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(155, 1, '340ttdv', 'Phan Thị Thu Hằng', '2003-10-13', 'Nữ', NULL, NULL, NULL, NULL, NULL, 'itachi161192@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(156, 1, '074ttdv', 'Hồ Đình Bình', '1989-10-11', 'Nam', NULL, NULL, NULL, NULL, NULL, 'tieungunhi214@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(157, 1, '075ttdv', 'Hồ Sỹ Cường', '1983-06-20', 'Nam', NULL, NULL, 'CĐ KT', NULL, NULL, 'lehuycong75@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(158, 1, '0020', 'Phạm Thị Minh', '1971-02-10', 'Nữ', NULL, 16, 'Cao đăng điều dưỡng', NULL, '0915516507', NULL, 'Nhà số 7, hẻm 4, ngõ 25, Phùng Chí Kiên - Hà Huy Tập - TP Vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(159, 1, '079ttdv', 'Nguyễn Mạnh Hùng', '1991-11-08', 'Nam', NULL, NULL, NULL, NULL, NULL, 'Nguyenhongykv1992@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(160, 1, '080ttdv', 'Nguyễn Tiến Chánh', '1960-05-02', 'Nam', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(161, 1, '081ttdv', 'Nguyễn Thế Quế', '1987-10-11', 'Nữ', NULL, NULL, NULL, NULL, NULL, 'Mecom0812@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(162, 1, '0305', 'Nguyễn Thị Thu Hiền', '1973-07-12', 'Nữ', NULL, 15, 'CĐ dược', NULL, '0865473127', 'hoatran23091980@gmail.com', 'Khối 15- Phường Quang Trung -TP Vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(163, 1, '246ttdv', 'Lê Thế Tài', '1986-11-20', 'Nam', NULL, NULL, NULL, NULL, NULL, 'Huongxiu95@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(164, 1, '255ttdv', 'Nguyễn Văn Song', '1979-08-22', 'Nam', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(165, 1, '243ttdv', 'Phan Văn Hải', '1989-01-09', 'Nam', NULL, NULL, NULL, NULL, NULL, 'hoangthuong2189@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(166, 1, '298ttdv', 'Lê Văn Biên', '1984-02-15', 'Nam', NULL, NULL, NULL, NULL, NULL, 'anhthaiqk4@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(167, 1, '326ttdv', 'Trần Quốc Đạt', '1993-11-24', 'Nam', NULL, NULL, NULL, NULL, NULL, 'hoangthao1181981@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(168, 1, '327ttdv', 'Bùi Xuân Công', '1969-03-06', 'Nam', NULL, NULL, NULL, NULL, NULL, 'Nguyenthuydkna@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(169, 1, '331ttdv', 'Cao Thanh Sơn', '1986-12-17', 'Nam', NULL, NULL, NULL, NULL, NULL, 'minhtriet.dth@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(170, 1, '1591', 'Nguyễn Thùy Dung', '1993-09-03', 'Nữ', NULL, 59, 'Cử nhân chính trị học', NULL, '0945053666', 'phamthiminh@gmail.com', 'Xóm 16- Nghi phú - tpVinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(171, 1, '1050', 'Nguyễn Sỹ Toàn', '1981-08-18', 'Nam', NULL, 59, 'CN LS', NULL, '0919904408', NULL, NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(172, 1, '0008', 'Nguyễn Thị Tuyết', '1981-05-06', 'Nữ', 'Trưởng phòng Tổ chức cán bộ', 59, 'Thạc sỹ', NULL, '0914789298', 'nguyenngocnn201088@gmail.com', 'K1- P Đội Cung - TP Vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(173, 1, '0009', 'Nguyễn Thị Hà', '1986-03-01', 'Nữ', NULL, 59, 'CN KT', NULL, '0912376555', NULL, 'K12-Quán Bàu-Vinh - Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(174, 1, '0011', 'Nguyễn Thị Huyền Trang', '1987-06-04', 'Nữ', NULL, 59, 'CN QTKD', NULL, '0943663566', NULL, 'Hưng lộc - TP Vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(175, 1, '1555', 'Nguyễn Thúy Nga', '1996-10-17', 'Nữ', 'VP trưởng', 59, 'Cử nhân luật', NULL, '0966454456', NULL, NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(176, 1, '1557', 'Võ Phương Đông', '1996-02-03', 'Nam', NULL, 59, 'Cử nhân luật kinh tế', NULL, '0946565686', 'thilamnguyen08@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(177, 1, '1207', 'Phan Thị Thu', '1992-11-10', 'Nữ', NULL, 59, 'CNQTKD', NULL, '0962220979', NULL, NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(178, 1, '1936', 'Hồ Hải Yến', '1982-05-11', 'Nữ', NULL, 59, 'Thạc sỹ', NULL, NULL, 'thienthuy0201@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(179, 1, '0031', 'Đinh Thị Nhung', '1981-06-10', 'Nữ', 'VP Trưởng', 54, 'CN TH', NULL, '0947072011', NULL, 'Nghi Đức, Tp Vinh, Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(180, 1, '0032', 'Nguyễn Thị Yến', '1983-04-19', 'Nữ', NULL, 54, 'CN KH', NULL, '0912569583', 'nguyenhaitruong1992@gmail.com', 'X1, Diễn Minh, Diễn Châu, Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(181, 1, '0979', 'Tống Thị Kiều Oanh', '1984-09-24', 'Nữ', NULL, 54, 'CN văn hóa', NULL, '0948147157', 'thuylinhkhd0601@gmail.com', 'Hưng Lộc - TP Vinh - Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(182, 1, '1379', 'Nguyễn Thị Thanh Mai', '1989-11-06', 'Nữ', NULL, 54, 'Cử nhân QTKD', NULL, '0943347875', 'd.thuchuyen@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(183, 1, '0659', 'Lê Anh Tuấn', '1981-04-11', 'Nam', 'Phó phòng, PTĐH phòng Kế hoạch tổng hợp', 54, 'BSCK I', 'Hồi sức cấp cứu', '977738869', 'nguyenphuongthao9497@gmail.com', 'Quang Thành - Yên Thành - Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(184, 1, '1357', 'Nguyễn Thanh Hưng', '1992-12-18', 'Nam', NULL, 54, 'Bác sĩ', NULL, '0363861352', NULL, NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(185, 1, '1442', 'Trần Thị Thùy Linh', '1993-04-18', 'Nữ', NULL, 54, 'Bác sĩ', NULL, '0961518108', NULL, NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(186, 1, '1443', 'Nguyễn Thị Cẩm Trà', '1993-04-12', 'Nữ', NULL, 54, 'Bác sĩ', NULL, '0909584536', 'bichngocbuitran@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(187, 1, '1356', 'Võ Thị Hảo', '1987-01-01', 'Nữ', NULL, 54, 'CN kinh tế', NULL, '0986728780', 'lethitam0502@gmail.com', 'Vinh Tân-TP.Vinh-Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(188, 1, '0034', 'Nguyễn Minh Hường', '1986-12-02', 'Nữ', NULL, 54, 'Cao đăng điều dưỡng', NULL, '0911443566', NULL, 'Xuân Trang, Nghi Đức, Tp Vinh, Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(189, 1, '0885', 'Trần Văn Duẩn', '1985-02-15', 'Nam', NULL, 54, 'Cao đăng điều dưỡng', NULL, '0975414323', 'honglam6099@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(190, 1, '0292', 'Lê Thị Hồng Lam', '1974-11-03', 'Nữ', NULL, 15, 'DSĐH', NULL, '0982327071', 'doantrang.nth@gmail.com0949751991', 'Khối Tân Tiến - Hưng Dũng - TP Vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(191, 1, '1314', 'Nguyễn Thị Hồng Thương', '1980-11-22', 'Nữ', NULL, NULL, 'CN sinh học', NULL, '0988208083', 'huyenkhoaduoc@gmail.com', 'Nghi Kim - Vinh - Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(192, 1, '0961', 'Nguyễn Tất Ngọc', '1986-06-26', 'Nam', 'Phó Giám đốc Trung tâm Đào tạo - Chỉ đạo tuyến', NULL, 'BSCK II', 'Ngoại - Thần kinh và Sọ não', '0912888959', NULL, NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(193, 1, '0989', 'Đinh Văn Chiến', '1979-09-14', 'Nam', 'Phó Giám đốc Trung tâm Đào tạo - Chỉ đạo tuyến', NULL, 'Tiến sĩ', 'Ngoại khoa', '0963311668', 'doanduoc83@gmail.com', 'khối 1- Phường Vinh Tân - TP Vinh - Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(194, 1, '1558', 'Thái Thị Lê Na', '1996-05-22', 'Nữ', NULL, NULL, 'Cử nhân quản trị kinh doanh', NULL, '01694396187', 'vomaivan04111980@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(195, 1, '1243', 'Hồ Thị Thu Hà', '1991-05-14', 'Nữ', NULL, NULL, 'CN ĐD', NULL, '0962522266', NULL, 'nghi kim-tp vinh-nghệ an', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(196, 1, '0035', 'Hoàng Đình Sơn', '1986-08-12', 'Nam', NULL, NULL, 'Cao đăng điều dưỡng', NULL, '0945282268', 'hong28403@gmail.com', 'Phong Thịnh- Thanh Chương - Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(197, 1, '1008', 'Phạm Thị Hồng Hạnh', '1984-10-12', 'Nữ', 'Phó phòng Hành chính quản trị', NULL, 'Thạc sĩ', NULL, '0912108884', NULL, NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(198, 1, '0045', 'Nguyễn Thị Diễm Hương', '1979-09-06', 'Nữ', NULL, 21, 'CN KT', NULL, '943156979', 'hovinh2210@gmail.com', 'Tân Phúc - Hưng Dũng - TP Vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(199, 1, '0980', 'Đặng Thị Quỳnh Phương', '1980-12-20', 'Nữ', NULL, 21, 'CN kế toán', NULL, '0967547568', 'hagiang7486@gmail.com', 'Phường Hưng Dũng - TP Vinh - Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(200, 1, '1197', 'Trần Thị Kim Ngân', '1990-04-11', 'Nữ', NULL, 21, 'Cử nhân TCNH', NULL, '0962762467', 'hoaithu9959@gmail.com', 'Mỹ Hạ - Hưng Lộc - Vinh - Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(201, 1, '1363', 'Trần Hoài Thu', '1981-08-11', 'Nữ', NULL, 21, 'Cử nhân kế toán', NULL, '903455550', 'nguyenduyen1612@gmail.com', 'Yên Hòa - Hà Huy Tập - Vinh - Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(202, 1, '1381', 'Lê Thị Na', '1981-09-23', 'Nữ', NULL, 21, 'Cử nhân kế toán', NULL, '0973885033', NULL, NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(203, 1, '0053', 'Lê Thị Thuý Mai', '1983-11-16', 'Nữ', NULL, 21, 'ĐH khác', 'Kế toán', '0983566210', 'lengabalan@gmail.com', 'Khối 13 - P Lê Lợi - TP Vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(204, 1, '1592', 'Nguyễn Thị Thanh Hà', '1996-02-05', 'Nữ', NULL, 21, 'Cử nhân tài chính ngân hàng', NULL, '0336232723', 'lethikienbvhndkna@gmail.com', 'Xóm Mỹ thượng- xã Hưng Lộc- TP vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(205, 1, '1590', 'Nguyễn Thị Quý', '1993-12-02', 'Nữ', NULL, 76, 'CNKT', NULL, '0968735386', NULL, 'Xuân liên- Nghi xuân- Hà Tĩnh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(206, 1, '1594', 'Nguyễn Thị Duyên', '1994-01-17', 'Nữ', NULL, 21, 'Cử nhân tài chính ngân hàng', NULL, '0329263450', 'giangduocna@gmail.com', 'Thái Sơn - Đô Lương- Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(207, 1, '1939', 'Phạm Văn Thạch', '1979-11-19', 'Nam', 'Phó trưởng phòng Tài chính kế toán, phụ trách điều hành phòng', 21, 'Thạc sỹ quản trị kinh doanh', NULL, '0978558558', NULL, 'Khối 16 phường Lê Lợi, thành phố vinh, nghệ an', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(208, 1, '0046', 'Bùi Thị Tuyết', '1986-10-21', 'Nữ', 'VP Trưởng', 21, 'Thạc sỹ kinh tế', NULL, '0985377880', 'lqtuan2007@gmail.com', 'Số 47 Tân phúc - Hưng Phúc - TP Vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(209, 1, '0044', 'Nguyễn Thị Hồng Nguyên', '1979-08-17', 'Nữ', NULL, 21, 'CN KT', NULL, '0912726736', 'nguyenthanhhoabvdkna@gmail.com', 'Khối Quang Phúc - Hưng Phúc - TP Vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(210, 1, '0047', 'Nguyễn Thị Thẩm', '1986-08-24', 'Nữ', NULL, 21, 'Thạc sỹ', 'Quản lý kinh tế', '0916522253', 'hienluong6573@gmail.com', 'Khối Bình Yên - Hưng Bình - TP Vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(211, 1, '0048', 'Nguyễn Minh Thắng', '1987-02-19', 'Nam', 'Phó phòng Tài chính kế toán; Trưởng đơn vị kiểm toán nội bộ', 21, 'CN KT', NULL, '0905677555', 'thanhduong.dkh@gmail.com', 'Số 102 Đặng Thái Thân- TP Vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(212, 1, '0049', 'Bùi Thị Thùy Dung', '1988-06-24', 'Nữ', NULL, 21, 'CN KT', NULL, '0947082345', 'thanhhuyen30121987@gmail.com', 'Xóm 14 - Nghi Phú - TP Vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(213, 1, '0050', 'Chu Thị Thanh Huyền', '1984-12-12', 'Nữ', NULL, 21, 'Thạc sỹ', 'Kế toán, kiểm toán và phân tích', '0902252324', 'hangdkna@gmail.com', 'SN 10, Ngách 2, ngõ 58 Đội Cung - TP Vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(214, 1, '0886', 'Hà Thị Mỹ Dung', '1988-03-16', 'Nữ', NULL, 21, 'Cử nhân kinh tế, chuyên ngành Quản trị kinh doanh', NULL, '0972944553', 'lehanh501@gmai.com', 'Khối 1 - Phường Vinh Tân - TP Vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(215, 1, '0887', 'Trần Thị Hương', '1989-03-01', 'Nữ', NULL, 21, 'Cử nhân kinh tế, chuyên ngành Tài chính ngân hàng', NULL, '0986885507', 'luongbalan@gmail.com', 'Khối 13- Phường Hà Huy Tập- Thành Phố Vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(216, 1, '0888', 'Nguyễn Thị Hiền', '1984-12-08', 'Nữ', NULL, 21, 'Cử nhân kinh tế, chuyên ngành Tài chính ngân hàng', NULL, '0949081284', 'daothihang1995@gmail.com', 'Hưng Lộc - TP Vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(217, 1, '0889', 'Nguyễn Thị Minh Huệ', '1986-12-03', 'Nữ', NULL, 21, 'CNKT', NULL, '0943065666', NULL, 'Khối 16 - Phường hưng bình - Thành phố Vinh - Nan', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(218, 1, '1005', 'Trần Thị Hải Yến', '1987-03-26', 'Nữ', NULL, 21, 'Cử nhân tài chính ngân hàng', NULL, '0982254070', 'maiphuongnguyen74@gmail.com', 'Khối Trung Hòa - Phường Hà Huy Tập - TP Vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(219, 1, '1067', 'Trần Thị Bích Ngân', '1982-12-22', 'Nữ', NULL, 21, 'Cử nhân kinh tế', NULL, '0916769295', 'tangthuytt@gmail.com', 'Lê Lợi - Vinh - Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(220, 1, '1081', 'Hoàng Phương Thảo', '1991-02-06', 'Nữ', NULL, 21, 'Cử nhân tài chính ngân hàng', NULL, '0989674715', 'Lehuonggiang2310@gmail.com', 'Hưng Thông - Hưng Nguyên - Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(221, 1, '1193', 'Nguyễn Bùi Ngọc', '1991-03-09', 'Nữ', NULL, 21, 'Cử nhân kinh tế', NULL, '0966959986', 'Lehaily239@gmail.com', 'X20-Nghi Phú - Vinh -Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(222, 1, '0296', 'Nguyễn Thị Kim Dung', '1977-09-02', 'Nữ', 'KTV trưởng', 15, 'CĐ dược', NULL, '0913312780', 'luyen2712@gmail.com', 'P Cửa Nam - Tp Vinh Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(223, 1, '0907', 'Nguyễn Thị Hồng Lam', '1989-08-05', 'Nữ', NULL, 21, 'Cử nhân quản trị kinh doanh', NULL, '0986955821', NULL, 'Khối 6 - Phường Hà Huy Tập - Tp Vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(224, 1, '1550', 'Hoàng Ngọc Anh', '1996-11-20', 'Nữ', NULL, 21, 'Cử nhân kế toán', NULL, '0989217868', NULL, 'Hưng thông - hưng nguyên - nghệ an', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(225, 1, '1551', 'Trần Hồng Thắm', '1995-10-22', 'Nữ', NULL, 21, 'Cử nhân kế toán, chuyên ngành kiểm toán', NULL, '0969503495', NULL, 'Lê Mao - TP Vinh-Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(226, 1, '1552', 'Trần Thùy Linh', '1993-12-21', 'Nữ', NULL, 21, 'Cử nhân kinh tế, chuyên ngành kinh tế và quản lý công', NULL, '0858797568', NULL, 'Nghi hương - cửa lò - nghệ an', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(227, 1, '1553', 'Hồ Thị Cẩm Ly', '1993-01-13', 'Nữ', NULL, 21, 'Cử nhân kế toán', NULL, '0971534555', NULL, 'Khu đô thị Vinaconex 9, Xóm 2 Nghi phú, tp vinh, nghệ an', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(228, 1, '1554', 'Bùi Thị Duyên', '1989-12-16', 'Nữ', NULL, 21, 'Cử nhân tài chính ngân hàng', NULL, '0356082881', NULL, 'K12- Quán Bàu-TP Vinh- Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(229, 1, '1006', 'Nguyễn Khánh Hùng', '1989-11-12', 'Nam', 'Phó giám đốc trung tâm dịch vụ, Trưởng đơn vị quản lý đấu thầu', 21, 'Thạc sĩ kinh tế', NULL, '0989216610', NULL, 'Bến Thuỷ, Tp Vinh, Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(230, 1, '0062', 'Trần Tiến Hùng', '1981-07-15', 'Nam', NULL, 21, 'ĐH khác', 'Kế toán', '0915839482', NULL, 'Khối 1 - P Hà Huy Tập - TP Vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(231, 1, '0294', 'Nguyễn Thị Thanh Thủy', '1973-08-14', 'Nữ', 'Phó khoa Dược', 15, 'DSCKI', NULL, '0979799498', 'Khiconcuabome@gmail.com', 'Khối Tân Tiến - Hưng Dũng - TP Vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(232, 1, '0295', 'Nguyễn Mai Phương', '1974-05-24', 'Nữ', NULL, 15, 'DSCKI', NULL, '0912921974', 'huongtra050387@gmail.com', 'Mẫu Đơn - Hưng Lộc - TP Vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(233, 1, '0067', 'Nguyễn Thị Thanh Hoài', '1979-09-10', 'Nữ', NULL, 21, 'ĐH khác', 'Kế toán', '0947481497', NULL, 'Khôi 5- Phường Lê Lợi - TP Vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(234, 1, '0908', 'Nguyễn Thị Tú', '1986-08-10', 'Nữ', NULL, 21, 'ĐH khác', 'Kế toán', '0988800918', NULL, 'Khối 14 - Phường Hà Huy Tập - Tp Vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(235, 1, '0066', 'Nguyễn Thị Đoan Trang', '1982-12-04', 'Nữ', NULL, 21, 'ĐH khác', 'Kế toán', '0967568596', 'quynhhoa511.hup@gmail.com', 'Khối 10 - Trung Đô - TP Vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(236, 1, '0353', 'Nguyễn Thị Minh Hạnh', '1976-11-24', 'Nữ', 'Trưởng khoa Khám bệnh', 27, 'BSCK II', 'BSCKI Da liễu', '0904778271', 'caomaihuong11111991@gmail.com', 'Số nha 96 Herman, Yên Vinh, Hưng Phúc, Tp Vinh, Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(237, 1, '0072', 'Phạm Thị Giang', '1982-12-15', 'Nữ', 'Trưởng phòng Điều dưỡng', NULL, 'CN ĐD', NULL, '0968145365', NULL, 'SN 28, ngõ 501 Tôn Thất Tùng - TP Vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(238, 1, '0506', 'Phạm Thế Cường', '1984-01-13', 'Nam', 'Phó phòng, phụ trách phòng Điều dưỡng', NULL, 'Thạc sỹ QLBV', NULL, '0943125124', 'letram1984@gmail.com', 'X 11 - Hưng Lộc -TP Vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(239, 1, '0772', 'Nguyễn Thị Hoài', '1987-12-03', 'Nữ', 'Điều dưỡng trưởng khối nội', NULL, 'CN ĐD', NULL, '0945607688', 'drnguyenthanhchung@gmai.com', 'số 96 đường tuệ tĩnh- \nkhối trung hòa- p.hà \nHuy tập- tp.vinh- nghệ\nan', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(240, 1, '0895', 'Lê Thị Nhàn', '1988-01-06', 'Nữ', 'PT,ĐH công tác điều dưỡng khối ngoại', NULL, 'Cử nhân điều dưỡng', NULL, '0942673688', 'huyenmatna@gmail.com', 'Xã quỳnh trang- TX Hoàng mai- nghệ an', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(241, 1, '0083', 'Lê Thị Thanh Tân', '1981-11-16', 'Nữ', NULL, 84, 'CN QTKD', NULL, '0914895919', 'dinhhanh2384@gmail.com', 'Tân Hùng, Hưng Lộc, Tp Vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(242, 1, '1382', 'Hoàng Đình Thập', '1979-05-06', 'Nam', NULL, 84, 'Kiến trúc sư', NULL, '0915085368', NULL, 'Quang Trung-TP.Vinh-Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(243, 1, '0268', 'Võ Đình Xuân', '1975-03-25', 'Nam', NULL, 84, 'TC XD', NULL, '0346554665', NULL, 'Nghi Ân-TP.Vinh-Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(244, 1, '0084', 'Hoàng Văn Thắng', NULL, 'Nam', NULL, 84, 'ĐH khác', NULL, '0912318225', 'kimdungle108@gmail.com', 'Khôối Xuân Nam, P. hưng Dũng, Tp Vinh, Nghệ an', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(245, 1, '0254', 'Nguyễn Đình Tứ', NULL, 'Nam', NULL, 84, 'y Công', NULL, '0912064250', 'lehaua4k46@gmail.com', 'Hưng Dũng - TP Vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(246, 1, '0099', 'Hồ Phi Định', NULL, 'Nam', NULL, 84, 'Bảo vệ', NULL, '0912590788', 'bschuhoangbvdkna@gmail.com', 'P. Hưng Dũng, Tp Vinh, Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(247, 1, '0105', 'Nguyễn Quốc Tiến', '1977-12-17', 'Nam', NULL, 84, 'Lái xe', NULL, '0912626447', NULL, 'P. Hà Huy Tập, Tp Vinh, Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(248, 1, '0107', 'Nguyễn Hữu Thành', '1984-02-08', 'Nam', NULL, 84, 'Lái xe', NULL, '0948778268', 'Linhhmu9999@gmail.com', 'Khối Xuân nam, P. hưng Dũng, Tp Vinh, Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0);
INSERT INTO `dm_nhan_vien` (`id`, `benh_vien_id`, `ma_nv`, `ho_ten`, `ngay_sinh`, `gioi_tinh`, `chuc_danh`, `khoa_phong_id`, `trinh_do`, `chuyen_khoa`, `dien_thoai`, `email`, `dia_chi`, `trang_thai`, `ngay_tao`, `ngay_cap_nhat`, `nguoi_tao`, `nguoi_cap_nhat`, `da_xoa`) VALUES
(249, 1, '1062', 'Nguyễn Đức Dũng', '1981-04-05', 'Nam', NULL, 84, 'Lái xe', NULL, '0979268381', 'bsgiangnguyen@gmail.com', 'Nghi Ân-TP.Vinh-Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(250, 1, '0990', 'Trần Tuấn Anh', '1978-10-10', 'Nam', NULL, 84, 'Lái xe', NULL, '0913536377', 'linhhieutien@gmail.com', 'Khôí 3 - Phường Bến Thủy - TP Vinh - Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(251, 1, '0370', 'Nguyễn Minh Phương', '1975-09-06', 'Nữ', NULL, 27, 'Cao đăng điều dưỡng', NULL, '0983772917', 'vuongquynh1980@gmail.com', 'Khối 10, Quán Bàu, Vinh, Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(252, 1, '0460', 'Phan Thị Thảo', '1975-03-15', 'Nữ', NULL, 27, 'Cao đăng điều dưỡng', NULL, '0915315608', NULL, 'Khối Minh Phúc, Phường Hưng Phúc, Tp Vinh, Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(253, 1, '0910', 'Hà Thị Nam', '1992-06-18', 'Nữ', NULL, 84, 'Công nhân', NULL, '0963821345', 'hoangthanhtrung0304@gmail.com', 'Diễn Trung-Diễn Châu-Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(254, 1, '1806', 'Lê Văn Hùng', '1990-01-11', 'Nam', NULL, 84, 'Cử nhân giáo dục thể chất', NULL, '0904770812', 'vananh.yhanoi@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(255, 1, '1807', 'Võ Văn Thuấn', '1986-12-20', 'Nam', NULL, 84, 'Trung cấp kỹ thuật điện', NULL, '975575276', 'lenyle.2010@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(256, 1, '1561', 'Đoàn Thị Lan Anh', '1990-02-02', 'Nữ', NULL, 84, 'Cử nhân quản trị kinh doanh', NULL, '0975326487', 'nguyenha26594@gmail.com', 'Hưng Bình-TP.Vinh-Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(257, 1, '0063', 'Trần Thế Anh', '1978-02-26', 'Nam', 'Vp trưởng', 84, 'ĐH khác', 'kế toán', '0988228877', 'nguyenvan211991@gmail.com', 'Khối Bình Phúc - Hưng Phúc - TP Vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(258, 1, '1560', 'Nguyễn Phúc Linh', '1993-06-29', 'Nam', NULL, 84, 'Kiến trúc sư', NULL, '0987789013', 'Drlynguyen@gmail.com', 'Hưng Dũng-TP.Vinh-Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(259, 1, '0088', 'Nguyễn Văn Thắng', NULL, 'Nam', 'Phó phòng, phụ trách phòng Hành chính quản trị', 84, 'Kỹ sư xây dựng', NULL, '0983849729', 'huulong.hmu@gmail.com', 'Khối Yên Vinh, P. Hưng Phúc, Tp Vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(260, 1, '0103', 'Ngô Viết Hùng', '1973-03-23', 'Nam', NULL, 84, 'Lái xe', NULL, '0913352427', 'lequangnhatdhytb@gmail.com', 'Xóm Mỹ Thắng, Hưng Lộc,Tp Vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(261, 1, '0252', 'Võ Văn Huỳnh', NULL, 'Nam', NULL, 84, 'Lái xe', NULL, '0913350765', NULL, 'Xóm 6 - Hưng Lộc - TP Vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(262, 1, '1919', 'Cao Ngọc Hiền', '1976-10-20', 'Nam', NULL, 84, 'Trung cấp', NULL, NULL, 'Bsmhanh@gmail.com', 'Nghi Ân-TP.Vinh-Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(263, 1, '0112', 'Nguyễn Việt Quang', '1984-07-10', 'Nam', 'Phó phòng Vật tư thiết bị', NULL, 'Kỹ sư', NULL, '0904625637', 'bshoxuandiem@gmail.com', 'Xóm15,Nghi Phú, Thành Phố Vinh, Tỉnh Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(264, 1, '0890', 'Nguyễn Đức Dũng', '1988-07-10', 'Nam', NULL, NULL, 'Kỹ sư', NULL, '0948352868', 'nguyentatthang336@gmail.com', 'Hưng Dũng - TP Vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(265, 1, '0113', 'Bùi Gia Anh', '1987-03-21', 'Nam', NULL, NULL, 'Kỹ sư', NULL, '0982344443', 'thuyngan.nd02@gmail.com', 'Xóm Trung Thành -Hưng Đông - TP Vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(266, 1, '0114', 'Nguyễn Chí Hiếu', '1983-07-02', 'Nam', NULL, NULL, 'CĐ KT', NULL, '0912727883', 'gianghmu1102@gmail.com', 'Nghi Ân -TP Vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(267, 1, '0116', 'Bùi Phi Trường', '1983-07-08', 'Nam', NULL, NULL, 'CĐ KT', NULL, '0941889229', NULL, 'Khối 9 - Bến Thuỷ - TP Vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(268, 1, '0909', 'Nguyễn Đức Tài', '1982-12-25', 'Nam', 'Văn phòng trưởng', NULL, 'CĐ KT', NULL, '0888088837', NULL, '66 Hồng bàng,Khối Tân Phong, Phường Lê Lợi, Thành phố Vinh, Tỉnh Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(269, 1, '0122', 'Nguyễn Trường Giang', NULL, 'Nam', NULL, NULL, 'KTV', NULL, '0946816963', 'nguyencaotuong777@gmail.com', 'K3, P Hà Huy Tập, Tp Vinh, Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(270, 1, '0119', 'Thái Khắc Hùng', NULL, 'Nam', NULL, NULL, 'NV KT', NULL, '0912448987', NULL, 'số 1 ngõ 87 đường Lê Viết Thuật -TP Vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(271, 1, '0445', 'Phạm Văn Anh', '1984-01-02', 'Nam', 'Trưởng phòng Vật tư thiết bị', NULL, 'BSCK II', 'Ngoại - Tiêu hóa', '0399660089', NULL, 'Khối 12 - P Trường Thi - TP Vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(272, 1, '1934', 'Đặng Hữu Tuấn', '1985-02-13', 'Nam', NULL, NULL, 'Trung cấp', NULL, NULL, 'thanhhoanbvhndkna@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(273, 1, '0014', 'Trần Huy Toản', '1983-06-27', 'Nam', 'Phó phòng, phụ trách phòng Công nghệ thông tin', 20, 'KS TH', NULL, '0974683777', NULL, 'Khối Xuân Bắc, Phường Hưng Dũng, Tp Vinh, Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(274, 1, '0012', 'Trần Thị Hồng Liên', '1979-09-10', 'Nữ', NULL, 20, 'CN TH', NULL, '0983831009', NULL, 'Số 6, Ngỗ 65, Đường Nguyễn Gia Thiều, Tp Vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(275, 1, '0013', 'Nguyễn Thị Quỳnh Trang', '1982-10-27', 'Nữ', NULL, 20, 'CN TH', NULL, '0983440607', NULL, 'Khối Tân Hoà, P. Hà Huy Tập, Tp Vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(276, 1, '1080', 'Trần Vũ Hoàng', '1987-08-15', 'Nam', NULL, 20, 'Kỹ sư điện tử viễn thông', NULL, '976451077', NULL, 'Khối 8 - Phường Lê Lợi - Thành phố Vinh - Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(277, 1, '1089', 'Cao Xuân Thắng', '1980-03-19', 'Nam', NULL, 20, 'CN TH', NULL, '0946505678', NULL, 'Số nhà 44-  Phùng Khắc Khoan - Phường Hưng Dũng - Thành phố Vinh - Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(278, 1, '0983', 'Nguyễn Thị Thảo', '1990-11-02', 'Nữ', NULL, 20, 'Kỹ sư CNTT', NULL, '0982729575', 'phanthithao75@gmail.com', 'Diễn Kỷ - Diễn Châu - Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(279, 1, '1575', 'Nguyễn Đình Phú', '1994-05-09', 'Nam', NULL, 20, 'Kỹ sư công nghệ thông tin', NULL, '0328133649', 'diepdiep0402@gmail.com', 'Số nhà 75 - Khối 6 - Phường Hồng Sơn - Thành phố Vinh - Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(280, 1, '1932', 'Nguyễn Hải Đăng', '1991-04-25', 'Nam', NULL, 20, 'Kỹ sư công nghệ thông tin', NULL, NULL, NULL, NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(281, 1, '1562', 'Nguyễn Đức Ngữ', '1984-11-21', 'Nam', NULL, 20, 'Thạc sỹ công nghệ thông tin', NULL, '0976449942', NULL, 'Khối Trung Định - Phường  Hưng Dũng - Thành phố Vinh - Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(282, 1, '1563', 'Hoàng Thị Thương', '1989-12-15', 'Nữ', NULL, 20, 'Kỹ sư tin học ứng dụng', NULL, '0396872709', NULL, 'Hà Huy Tập - Thành phố Vinh - Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(283, 1, '1564', 'Võ Thị Phương Anh', '1993-03-05', 'Nữ', NULL, 20, 'Kỹ sư công nghệ thông tin', NULL, '0375964266', NULL, 'Xóm 18 - Nghi Phú - Thành phố Vinh - Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(284, 1, '1804', 'Nguyễn Hồng Quân', '1990-10-28', 'Nam', NULL, 20, 'Kỹ sư khoa học vi tính', NULL, '915577264', NULL, NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(285, 1, '0026', 'Nguyễn Thị Hồng Vân', '1986-03-21', 'Nữ', NULL, 52, 'Thạc sĩ', 'Nội khoa', '0983473807', 'mongthisen93@gmail.com', 'Xóm 4B, Hưng Đạo, Hưng Nguyên Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(286, 1, '1324', 'Nguyễn Bá Linh', '1991-01-24', 'Nam', NULL, 52, 'Bác sĩ', NULL, '0961885330', NULL, NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(287, 1, '0830', 'Lê Thị Nhường', '1986-03-10', 'Nữ', NULL, 52, 'BSCK I', 'Chẩn đoán hình ảnh', '0916263885', NULL, 'Xóm 2, Hưng Thông, Hưng Nguyên, Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(288, 1, '1297', 'Phạm Văn Hoàn', '1991-05-16', 'Nam', 'Bí thư Đoàn thanh niên', 52, 'Thạc sĩ', 'Nội khoa', '0965668336', 'havinh.ha@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(289, 1, '1298', 'Cao Thị Hoàng Yến', '1991-01-25', 'Nữ', NULL, 52, 'Bác sĩ', NULL, '0946543058', NULL, NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(290, 1, '1437', 'Nguyễn Cẩm Tú', '1994-04-07', 'Nữ', NULL, 52, 'Bác sĩ', NULL, '0982410149', 'thanhha130890@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(291, 1, '1652', 'Hoàng Thị Thái', '1994-03-10', 'Nữ', NULL, 52, 'Bác sĩ', NULL, '0968527746', 'tranthiduyen02091993@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(292, 1, '1653', 'Nguyễn Văn Nhân', '1993-08-04', 'Nam', NULL, 52, 'Bác sĩ', NULL, '0383421397', 'minhquan28022110@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(293, 1, '1654', 'Lê Trung Anh', '1995-08-19', 'Nam', NULL, 52, 'Bác sĩ', NULL, '0964241839', NULL, NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(294, 1, '1886', 'Trần Thị Huyền', '1996-03-05', 'Nữ', NULL, 52, 'Bác sĩ', NULL, '0964221577', NULL, NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(295, 1, '0025', 'Nguyễn Thị Minh Ngọc', '1985-07-29', 'Nữ', 'Phó khoa Thăm dò chức năng', 52, 'Thạc sĩ', 'Chẩn đoán hình ảnh', '0986562826', NULL, 'Vinh Quang, Hưng Bình, Vinh, Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(296, 1, '1124', 'Bùi Thị Việt Nga', '1990-08-26', 'Nữ', NULL, 52, 'Thạc sĩ', 'Sản phụ khoa', '0986807896', 'ngocthuongcl@gmail.com', 'P Hưng Bình - TP Vinh - Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(297, 1, '1123', 'Bùi Văn Hưng', '1990-02-17', 'Nam', NULL, 52, 'BSCK I', 'Sản phụ khoa', '0988331637', NULL, 'Lê lợi -TP Vinh -Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(298, 1, '1339', 'Trịnh Văn Thân', '1992-10-22', 'Nam', NULL, 52, 'Thạc sĩ', 'Nội khoa', '0987707383', NULL, 'Phú Thành- Yên Thành- Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(299, 1, '0930', 'Tăng Đình Quang', '1987-05-14', 'Nam', NULL, 52, 'Thạc sĩ', 'Nội khoa', '0979875458', NULL, NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(300, 1, '1436', 'Nguyễn Huy Hoàng', '1994-04-21', 'Nam', NULL, 52, 'Bác sĩ', NULL, '09778486760975556787', 'thuy22091977@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(301, 1, '0179', 'Lê Huy Công', '1975-12-29', 'Nam', 'Trưởng khoa Thăm dò chức năng', 52, 'BSCK II', 'Chẩn đoán hình ảnh', '0904727829', NULL, 'Xóm Xuân Hùng, Hưng Lộc, Tp Vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(302, 1, '1445', 'Thái Thị Như Hảo', '1993-06-22', 'Nữ', NULL, 52, 'CN ĐD', NULL, '0913996889', NULL, 'Nghi Phú - tp vinh- Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(303, 1, '1701', 'Nguyễn Thị Hồng', '1992-10-29', 'Nữ', NULL, 52, 'Cử nhân điều dưỡng', NULL, '09844840230949323192', 'maianh260986@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(304, 1, '1463', 'Nguyễn Thị Nga', '1995-10-30', 'Nữ', NULL, 52, 'CN ĐD', NULL, '0965954726', NULL, NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(305, 1, '1284', 'Nguyễn Thị Quỳnh', '1991-11-03', 'Nữ', NULL, 52, 'CN ĐD', NULL, '0983262024', 'tragiangkhth@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(306, 1, '0609', 'Biện Thị Tuyết', '1976-06-18', 'Nữ', NULL, 27, 'Cao đăng điều dưỡng', NULL, '0985773366', 'vinha2k39@gmail.com', 'Khối Văn Tiến - P Hưng Dũng - TP Vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(307, 1, '1723', 'Nguyễn Thị Hương', '1995-05-22', 'Nữ', NULL, 52, 'Cao đăng điều dưỡng', NULL, '0964907789', NULL, NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(308, 1, '1717', 'Nguyễn Thị Thiện', '1994-05-20', 'Nữ', NULL, 52, 'Cao đăng điều dưỡng', NULL, '0975141914', NULL, NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(309, 1, '0194', 'Hoàng Thị Thương', '1989-01-02', 'Nữ', NULL, 52, 'Cao đăng điều dưỡng', NULL, '0936218327', 'Drphuongho@gmail.com', 'Khối 12, P. Hà Huy Tập. Tp Vinh, Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(310, 1, '0698', 'Văn Thị Luyến', '1987-10-05', 'Nữ', NULL, 52, 'Cao đăng điều dưỡng', NULL, '0983514969', 'nguyenthikhanhtram82@gmail.com', 'Xóm Xuân Trung, Hưng Dũng, Tp Vinh, Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(311, 1, '0238', 'Hoàng Thị Phương Thảo', '1981-08-11', 'Nữ', NULL, NULL, 'Cao đăng điều dưỡng', NULL, '0975382683', 'ngothanhbs@gmail.com', 'Xóm Ngũ Lộc, Hưng Lộc, TP Vinh, Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(312, 1, '0195', 'Hoàng Văn Thông', '1988-10-26', 'Nam', NULL, 52, 'Cao đăng điều dưỡng', NULL, '01686311150', 'tranthinganyb@gmail.com', 'Xã Hưng Yên Nam, Hưng Nguyên, Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(313, 1, '0193', 'Võ Thị Thủy', '1984-10-20', 'Nữ', NULL, 52, 'Cao đăng điều dưỡng', NULL, '0977289527', 'hoanglien0979799220@gmail.com', 'Xóm Xuân Hùng, Hưng Lộc, Tp Vinh, Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(314, 1, '1534', 'Nguyễn Thị Huyền', '1990-01-01', 'Nữ', NULL, 17, 'Cao đăng điều dưỡng', NULL, '0349730430', 'tathuyoag@gmail.com', 'Tràng Sơn- Huyện Đô Lương- Tỉnh Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(315, 1, '0191', 'Nguyễn Thị Hường', '1979-12-01', 'Nữ', NULL, 52, 'CN CĐPS', NULL, '0971771332', NULL, 'Khối Trung Tiến, Phường Hưng Dũng, Tp Vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(316, 1, '0196', 'Nguyễn Thị Lan Hương', '1986-06-22', 'Nữ', NULL, 52, 'CN CĐPS', NULL, '0989661060', 'nhatle150695@gmail.com', 'Xóm 15, Nghi Phú, Tp Vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(317, 1, '1907', 'Bùi Thị Lan', '1996-09-09', 'Nữ', NULL, 52, 'Cử nhân kỹ thuật hình ảnh y học', NULL, '0378576893', NULL, NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(318, 1, '1908', 'Nguyễn Thị Loan', '1996-06-08', 'Nữ', NULL, 52, 'Cử nhân kỹ thuật hình ảnh y học', NULL, '0967174158', 'obstetrics2010@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(319, 1, '0202', 'Nguyễn Minh Tiến', '1981-06-20', 'Nam', NULL, NULL, 'BSCK I', 'Chẩn đoán hình ảnh', '0985755678', 'Dr.trung86@gmail.com', 'Hà huy tập- Vinh- Nghệ an', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(320, 1, '1582', 'Nguyễn Cảnh Cương', '1983-09-16', 'Nam', 'Phó trưởng khoa Xquang', NULL, 'BSCK II', 'Chẩn đoán hình ảnh', '0987788101', 'phanvanhieu110296@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(321, 1, '0201', 'Lê Vũ Quang', NULL, 'Nam', NULL, NULL, 'Bác sĩ', NULL, '0946653828', 'bs.caohungspk@gmail.com', 'Hưng Dũng, Tp Vinh, Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(322, 1, '0905', 'Trần Tất Thắng', '1987-02-14', 'Nam', NULL, NULL, 'Bác sĩ', NULL, '0976460313', 'phuonghoangdongphuong93@gmail.com', 'Nghi ân- Vinh-Nghệ an', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(323, 1, '1016', 'Đậu Lệ Thủy', '1989-12-12', 'Nữ', NULL, NULL, 'Thạc sĩ', 'Chẩn đoán hình ảnh', '0868121289', NULL, 'Nghĩa Thuận - Thái Hòa - Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(324, 1, '1115', 'Hoàng Thị Quyên', '1990-07-10', 'Nữ', NULL, NULL, 'Bác sĩ', NULL, '0973633406', 'thuphuong.dkhnna@gmail.com', 'Nghi phú-Vinh-Nghệ an', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(325, 1, '1655', 'Phạm Minh Dũng', '1993-06-28', 'Nam', NULL, NULL, 'Bác sĩ', NULL, '0984888477', 'thaonguyenbvdkna@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(326, 1, '1656', 'Nguyễn Quốc Huy', '1993-08-09', 'Nam', NULL, NULL, 'Bác sĩ', NULL, '0911509678', NULL, NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(327, 1, '1662', 'Vũ Thị Phương', '1994-08-05', 'Nữ', NULL, NULL, 'Bác sĩ', NULL, '0329575220', NULL, NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(328, 1, '1914', 'Lê Thiên Mai', '1996-06-16', 'Nữ', NULL, NULL, 'Bác sĩ', NULL, '0962806109', NULL, NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(329, 1, '1811', 'Trần Thị Mỹ', '1994-02-02', 'Nữ', NULL, NULL, 'Bác sĩ', NULL, '0978315127', NULL, NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(330, 1, '0200', 'Lê Thanh Quỳnh', '1971-01-24', 'Nam', 'Trưởng khoa Xquang', NULL, 'BSCK II', 'ThS chẩn đoán hình ảnh', '0913032678', NULL, 'Khối Xuân Tiến, Hưng Dũng', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(331, 1, '0209', 'Hoàng Đình Thông', '1977-12-25', 'Nam', NULL, NULL, 'Cao đăng điều dưỡng', NULL, '0977969609', 'phuongyta93@gmail.com', 'Hưng Lộc, Tp Vinh, Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(332, 1, '0210', 'Trần Văn Tuấn', '1979-04-30', 'Nam', NULL, NULL, 'Cao đăng điều dưỡng', NULL, '0913895987', NULL, 'P. Hưng Phúc, Tp Vinh, Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(333, 1, '0204', 'Chu Sỹ Hiệp', '1971-05-17', 'Nam', 'KTV trưởng', NULL, 'CN KTV', NULL, '0983263448', NULL, 'Khối 10, P. Đức Thuận, Tx Hồng Lĩnh, Hà Tĩnh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(334, 1, '1293', 'Chu Văn Mạnh', '1991-01-14', 'Nam', NULL, NULL, 'CN KTVXQ', NULL, '0988647709', NULL, 'Nghĩa binh-Tân kỳ-Nghệ an', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(335, 1, '1294', 'Trần Minh Nghĩa', '1992-04-06', 'Nam', NULL, NULL, 'CN KTVXQ', NULL, '0962900588', NULL, 'Nghi phú-Vinh-Nghệ an', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(336, 1, '1791', 'Nguyễn Văn Bảo', '1997-04-29', 'Nam', NULL, NULL, 'Cử nhân kỹ thuật hình ảnh y học', NULL, '0964114256', NULL, NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(337, 1, '1906', 'Nguyễn Thị Thương', '1997-09-24', 'Nữ', NULL, NULL, 'Cử nhân kỹ thuật hình ảnh y học', NULL, '0335108697', NULL, NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(338, 1, '1909', 'Nguyễn Nghĩa Long', '1992-02-10', 'Nam', NULL, NULL, 'Cử nhân kỹ thuật hình ảnh y học', NULL, '0974171884', NULL, NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(339, 1, '1295', 'Phạm Văn Vỵ', '1993-06-06', 'Nam', NULL, NULL, 'CN CĐKTVXQ', NULL, '0398093777', NULL, NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(340, 1, '1797', 'Lê Hồng Quân', '1995-09-08', 'Nam', NULL, NULL, 'Cao đẳng Kỹ thuật y học', NULL, '0862239267', NULL, NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(341, 1, '0207', 'Phạm Văn Hải', '1988-09-10', 'Nam', NULL, NULL, 'CN CĐKTV XQ', NULL, '0988073228', NULL, 'Bến Thuỷ, Tp Vinh, Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(342, 1, '0918', 'Trần Xuân Dũng', '1987-09-14', 'Nam', NULL, NULL, 'CN CĐKTV XQ', NULL, '0984665265', NULL, 'Hòa tiến-Hưng lộc-Vinh-Nghệ an', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(343, 1, '1075', 'Phùng Quang Hưng', '1990-11-30', 'Nam', NULL, NULL, 'CN CĐKTV XQ', NULL, '0973355366', NULL, 'Hà huy tập- Vinh- Nghệ an', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(344, 1, '1076', 'Trần Văn Đức', '1991-01-07', 'Nam', NULL, NULL, 'CN CĐKTV XQ', NULL, '0982431061', NULL, 'Nghi Phú- TP Vinh- Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(345, 1, '1077', 'Tạ Xuân Tình', '1992-03-12', 'Nam', NULL, NULL, 'CN CĐKTV XQ', NULL, '0979987828', NULL, 'Thọ thành- Yên thành- Nghệ an', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(346, 1, '1078', 'Hồ Trung Thành', '1989-10-17', 'Nam', NULL, NULL, 'CN CĐKTV XQ', NULL, '0945063666', NULL, 'Quỳnh hậu- Quỳnh lưu- Nghệ an', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(347, 1, '1079', 'Nguyễn Văn Trường', '1991-04-21', 'Nam', NULL, NULL, 'CN CĐKTV XQ', NULL, '0913245586', NULL, 'Châu bình- Quỳ châu- Nghệ an', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(348, 1, '1090', 'Trần Văn Dũng', '1990-10-20', 'Nam', NULL, NULL, 'CN CĐKTV XQ', NULL, '0986113612', 'thuynhitrinhle244@gmail.com', 'Nghi Phú- TP Vinh- Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(349, 1, '1544', 'Lê Công Kiên', '1997-04-20', 'Nam', NULL, NULL, 'CN CĐKTV XQ', NULL, '0369976517', 'thuyhien2607@gmail.com', 'Đại thành-Yên Thành-Nghệ an', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(350, 1, '0211', 'Hoàng Văn Tuyến', '1982-08-20', 'Nam', NULL, NULL, 'Cao đẳng kỹ thuật viên hình ảnh', NULL, '0974404828', 'ngocsan28@gmail.com', 'Số nhà 120B, Ngỗ 120 đường Nguyễn Sỹ Sách, Tp Vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(351, 1, '1798', 'Lê Hoàng Phong', '1992-06-29', 'Nam', NULL, NULL, 'Cao đẳng kỹ thuật viên hình ảnh', NULL, '09826277720349078282', 'anhbs0106@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(352, 1, '1911', 'Nguyễn Thị Quỳnh Lam', '1991-05-26', 'Nữ', NULL, NULL, 'Cao đẳng kỹ thuật viên hình ảnh', NULL, '0338700087', 'ledangquang1987@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(353, 1, '1912', 'Nguyễn Tuấn Anh', '1994-11-15', 'Nam', NULL, NULL, 'Cao đẳng kỹ thuật viên hình ảnh', NULL, '0987874564', 'Trangbeo.196@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(354, 1, '1360', 'Nguyễn Văn Hùng', '1995-05-11', 'Nam', NULL, NULL, 'Cao đẳng kỹ thuật viên hình ảnh', NULL, '0977724446', 'doanhoang2008@gmail.com', 'Hưng lộc-Vinh- Nghệ an', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(355, 1, '0212', 'Đàm Ngọc Đại', '1983-04-27', 'Nam', NULL, NULL, 'Cao đẳng kỹ thuật viên hình ảnh', NULL, '0985705157', 'nguyenhuyentrang4196@gmail.com', 'Yên Hội, Đỗ Thành, Yên Thành, Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(356, 1, '1910', 'Trần Đăng Tuấn', '1990-10-01', 'Nam', NULL, NULL, 'Cao đẳng kỹ thuật y học', NULL, '0979578969', 'Thanhhuyenbvhndkna@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(357, 1, '0214', 'Nguyễn Bá Yến', NULL, 'Nam', NULL, NULL, 'Cao đăng điều dưỡng', NULL, '0913161567', 'camlinh.rose@gmail.com', 'Xóm 11, Hưng Lộc, Tp Vinh, Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(358, 1, '1347', 'Nguyễn Thanh Hoàng', '1994-05-20', 'Nam', NULL, 66, 'Cử nhân công nghệ sinh học', NULL, '0947693866', 'THANHVAN61291@GMAIL.COM', 'Khối 8 - Phường Trung Đô - Thành phố Vinh - Tỉnh Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(359, 1, '1209', 'Võ Thị Phương Nhung', '1991-01-22', 'Nữ', NULL, 66, 'CN CĐKTV XN', NULL, '0973617667', 'Tranthidieu020287@gmail.com', 'Xóm 14 - Xã Nghi Phú - Thành phố Vinh - Tỉnh Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(360, 1, '1066', 'Nguyễn Văn Phúc', '1989-01-20', 'Nam', 'Phó khoa, PTĐH khoa Di truyền và Sinh học phân tử', 66, 'BSCK I', 'Xét nghiệm Y học', '0978957399', 'minhtam882016@gmail.com', 'Xóm 12 - Xã Nghi Phú -Thành phố Vinh - Tỉnh Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(361, 1, '1307', 'Bùi Thị Dung', '1990-01-18', 'Nữ', NULL, 66, 'Bác sĩ', NULL, '0966247398', NULL, 'Thôn Đồng tâm- Xã Thượng Ninh- Huyện Như xuân-Tỉnh Thanh hóa', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(362, 1, '1350', 'Tạ Thị Thư', '1994-06-15', 'Nữ', NULL, 66, 'ĐH khác', NULL, '0367431655', 'PHUONGOANHIVF@GMAIL.COM', 'Xóm 1 - Xã Diễn Lợi - Huyện Diễn Châu - Tỉnh Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(363, 1, '1546', 'Nguyễn Thị Kim Thúy', '1995-02-10', 'Nữ', NULL, 66, 'Cử nhân công nghệ sinh học', NULL, '0348469482', 'ngocmai.balan@gmail.com', 'Phường Hưng Đông-Thành phố Vinh - Tỉnh Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(364, 1, '1545', 'Đặng Thị Ngọc Ánh', '1996-10-25', 'Nữ', 'Phụ trách điều hành công tác kỹ thuật viên trưởng', 66, 'CN KTV XN', NULL, '0983563592', 'Phantuyendkt@gmail.com', 'Nghi Kim - Tp Vinh - Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(365, 1, '0925', 'Lê Thị Nga', '1988-03-12', 'Nữ', NULL, 66, 'CN CĐKTVXN', NULL, '0915954709', 'Quynhanhdung2020@gmail.com', 'thạch Ngọc, Thạch Hà, Hà Tĩnh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(366, 1, '0932', 'Trần Anh Đào', '1989-01-10', 'Nam', NULL, NULL, 'Thạc sỹ SH', NULL, '0989459586', 'Thaiduykien19311992@gmail.com', 'Xóm 11, Xã Ngọc Sơn - Huyện Quỳnh Lưu - Tỉnh Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(367, 1, '0239', 'Nguyễn Thị Giang', '1986-10-13', 'Nữ', NULL, NULL, 'Cao đăng điều dưỡng', NULL, '0976256502', 'Xetnghiemdakhoa2105@gmail.com', 'Khối Yên Hoà, P. Hà Huy Tập. Tp Vinh, Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(368, 1, '1901', 'Trần Thị Hiền', '1997-05-14', 'Nữ', NULL, NULL, 'Cử nhân xét nghiệm', NULL, '0969025806', 'nguyenhong.sh90@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(369, 1, '0224', 'Nguyễn Võ Dũng', '1986-09-25', 'Nam', 'Phó khoa, PTĐH khoa Vi sinh', NULL, 'Thạc sĩ sinh học', NULL, '0979889259', NULL, 'Khối 2, TT Đức Thọ, Đức Thọ, Hà Tĩnh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(370, 1, '0225', 'Hoàng Thị Hà', '1984-12-27', 'Nữ', 'Phụ trách công tác kĩ thuật viên trưởng', NULL, 'Thạc sĩ sinh học', NULL, '0984721222', 'nguyenthuyan12091990@gmail.com', 'Xóm 1, Đặng Sơn, Đô Lương, Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(371, 1, '1897', 'Nguyễn Thị Hoài', '1998-01-06', 'Nữ', NULL, NULL, 'Cử nhân xét nghiệm', NULL, '0976983227', 'tranthuyhahmu@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(372, 1, '1903', 'Nguyễn Thị Thanh', '1993-10-01', 'Nữ', NULL, NULL, 'Cao đẳng xét nghiệm y học', NULL, '0963404471', 'phamthuy47@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(373, 1, '0235', 'Nguyễn Thị Huyền Thương', '1986-02-06', 'Nữ', NULL, NULL, 'CĐ KTVXN', NULL, '0949054777', 'phutran.hmu@gmail.com', 'Khối 14- Phường Lê Lợi - Thành phố  Vinh -Tỉnh Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(374, 1, '1549', 'Tôn Thị Thùy Vân', '1994-09-05', 'Nữ', NULL, NULL, 'CN CĐKTV XN', NULL, '0979597596', 'dinhtuan211yt@gmail.com', 'Khối Tân Tiến- Phường Hưng Dũng- Thành phố Vinh - Tỉnh Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(375, 1, '1004', 'Lê Thị Mỹ', '1991-05-01', 'Nữ', NULL, NULL, 'CĐ KTVXN', NULL, '0942654258', 'lethimai1311996@gmail.com', 'Quán Bàu - TP Vinh - Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(376, 1, '1204', 'Kiều Thị Hằng', '1990-11-10', 'Nữ', NULL, NULL, 'CN CĐKTVXN', NULL, '0918164776', 'trananhthai1112@gmail.com', 'Hưng Phúc- Vinh- Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(377, 1, '1331', 'Trần Thị Hiếu', '1990-09-02', 'Nữ', NULL, NULL, 'Bác sĩ', NULL, '0941253638', 'daophuonglinh90@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(378, 1, '1063', 'Trương Văn Lợi', '1989-07-27', 'Nam', NULL, NULL, 'BSCK I', 'Xét nghiệm Y học', '0976869558', NULL, 'Nghi Xuân - Nghi Lộc - Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(379, 1, '1095', 'Nguyễn Thị Loan Anh', '1990-01-21', 'Nữ', NULL, NULL, 'Bác sĩ', NULL, '973476603', NULL, 'Phường Vinh Tân-Tp Vinh-Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(380, 1, '1621', 'Lô Thị Huệ', '1992-12-09', 'Nữ', NULL, NULL, 'Bác sĩ', NULL, '0978021893', NULL, NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(381, 1, '0218', 'Phạm Hồng Thái', '1975-08-28', 'Nam', 'Giám đốc Trung tâm Xét nghiệm', NULL, 'BSCK II', 'Hóa sinh', '0983834377', 'kieutrang1991.kt@gmail.com', 'Trường Tiến, Hưng Bình, Tp Vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(382, 1, '1788', 'Nguyễn Thị Thanh Loan', '1997-06-05', 'Nữ', NULL, NULL, 'Cử nhân xét nghiệm y học', NULL, '0976222769', NULL, NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(383, 1, '1900', 'Trần Thị Hải Yến', '1997-09-26', 'Nữ', NULL, NULL, 'Cử nhân xét nghiệm', NULL, '0868822138', 'hienphan271092@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(384, 1, '0232', 'Phan Thị Trang', '1983-06-20', 'Nữ', NULL, NULL, 'CN KTV', NULL, '0989821682', NULL, 'Khối 16. p TRường Thi, Tp Vinh, Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(385, 1, '0924', 'Nguyễn Văn Nhân', '1988-02-01', 'Nam', 'KTV trưởng Khoa và Trung tâm', NULL, 'Thạc sĩ sinh học', NULL, '0986711078', 'ngocnguyenhung@gmail.com', 'Xóm Kim chi - Nghi Ân - Tp Vinh - Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(386, 1, '1547', 'Nguyễn Thị Thái', '1996-03-20', 'Nữ', NULL, NULL, 'CN KTVXN', NULL, '0985105528', 'phanthanhhung159@gmail.com', 'Thạch Hội - Thạch Hà - Hà Tĩnh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(387, 1, '1785', 'Nguyễn Thị Phượng', '1996-02-08', 'Nữ', NULL, NULL, 'Cử nhân xét nghiệm y học', NULL, '0963634729', 'ngocnguyen31051994@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(388, 1, '1899', 'Trần Thúy Quỳnh', '1997-02-06', 'Nữ', NULL, NULL, 'Cử nhân xét nghiệm', NULL, '0816163886', 'phanducchinh.tmh@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(389, 1, '1904', 'Phan Sỹ Giáp', '1994-05-19', 'Nam', NULL, NULL, 'Cao đẳng xét nghiệm y học', NULL, '0348811721', 'drhoainamle@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(390, 1, '1289', 'Hồ Thị Minh Nguyệt', '1993-08-28', 'Nữ', NULL, NULL, 'CN CĐKTVXN', NULL, '0977600256', 'bs.buigiang@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(391, 1, '1362', 'Lê Thị Ly Na', '1994-12-16', 'Nữ', NULL, NULL, 'CN CĐKTVXN', NULL, '0911173777', 'daungoctrieu1962@gmail.com', 'Kim Liên , Nam Đàn , Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(392, 1, '1288', 'Thái Thùy Linh', '1993-09-03', 'Nữ', NULL, NULL, 'CN CĐKTVXN', NULL, '3639151415', 'Thuhangpham968@gmail.com', 'Xóm 18 - Xã Nghi Liên - TP Vinh - Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(393, 1, '1290', 'Nguyễn Thị Ngọc Anh', '1991-09-18', 'Nữ', NULL, NULL, 'CN CĐKTVXN', NULL, '0969051300', 'kimanhcuong@gmail.com', 'Xã Hưng Lộc -TP Vinh- Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(394, 1, '1292', 'Nguyễn Thị Lương', '1992-10-20', 'Nữ', NULL, NULL, 'CN CĐKTVXN', NULL, '0356777288', 'thanhtuphuoc@gmail.com', 'Hưng Tây Hưng Nguyên Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(395, 1, '1285', 'Nguyễn Thị Diệu Hoa', '1992-04-22', 'Nữ', NULL, NULL, 'CĐ KTVXN', NULL, '0822239823', 'thuydungdhy2015@gmail.com', 'TP Vinh - Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(396, 1, '0926', 'Nguyễn Sỹ Quyết', '1987-04-01', 'Nam', NULL, NULL, 'CĐ KTVXN', NULL, '0977811229', 'Phanthihuyen268@gmail.com', 'K.Xuân Tiến-Phường Hưng Dũng-TP Vinh- Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(397, 1, '1287', 'Nguyễn Cảnh Bách', '1990-01-27', 'Nam', NULL, NULL, 'CN CĐKTVXN', NULL, '0396908842', NULL, 'Phường Hưng Dũng- Tp Vinh -Nghệ an', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(398, 1, '1211', 'Nguyễn Thị Trang Nhung', '1991-04-26', 'Nữ', NULL, NULL, 'CĐ KTVXN', NULL, '0349736391', NULL, 'Hưng Dũng - TP Vinh - Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(399, 1, '0236', 'Nguyễn Thị Kiều Oanh', '1988-10-26', 'Nữ', NULL, NULL, 'CĐ KTVXN', NULL, '0916422610', NULL, 'Xuân Bắc, Hưng Dũng, Nghệ an', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(400, 1, '1000', 'Phạm Thị Huế', '1990-06-10', 'Nữ', NULL, NULL, 'CĐ KTVXN', NULL, '0966904408', NULL, 'Nghi Phú - Tp Vinh - Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(401, 1, '1905', 'Nguyễn Thị Ngọc', '1993-12-10', 'Nữ', NULL, NULL, 'Cao đẳng xét nghiệm y học', NULL, '0973182110', NULL, NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(402, 1, '1439', 'Nguyễn Thị Lan Anh', '1993-11-20', 'Nữ', NULL, NULL, 'Bác sĩ', NULL, '0988298735', NULL, 'Đường mai lão bạng, nghi phú TP Vinh Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(403, 1, '1595', 'Hồ Thị Hiệp', '1979-11-16', 'Nữ', 'Phó khoa, PTĐH khoa Hóa sinh', NULL, 'BSCK I', 'Hóa sinh y học', '0916473000', NULL, 'Khối 2 Thị Trấn Quán Hành Nghi Lộc Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(404, 1, '1812', 'Nguyễn Thị Trang', '1996-01-02', 'Nữ', NULL, NULL, 'Bác sĩ', NULL, '0865367895', NULL, NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(405, 1, '0223', 'Đặng Thị Thu Trang', '1986-04-01', 'Nữ', NULL, 56, 'CN ĐD', NULL, '917308586', 'thuchang.na@gmail.com', 'Khối Yên Hoà, P. Hà Huy Tập. Tp Vinh, Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(406, 1, '1581', 'Nguyễn Thị Thuận', '1982-04-04', 'Nữ', NULL, NULL, 'CN ĐD', NULL, '0972434234', 'tdg.hmu@gmail.com', 'Trường Thi - Tp Vinh - Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(407, 1, '1787', 'Nguyễn Tống Khánh Linh', '1997-02-11', 'Nữ', NULL, NULL, 'Cử nhân xét nghiệm y học', NULL, '0964192492', 'Drsonbalan@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(408, 1, '0226', 'Phạm Thị Hương', '1988-01-02', 'Nữ', NULL, NULL, 'Thạc sĩ sinh học', NULL, '0977276108', 'drphamhuong@gmail.com', 'Số nhà 12 ngõ 34, Đường Phùng Khắc Khoan, Tp Vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(409, 1, '0227', 'Hoàng Thị Minh Thư', '1989-06-15', 'Nữ', NULL, NULL, 'Thạc sĩ sinh học', NULL, '0973841131', 'anhthucphan@gmail.com', 'Xã Diễn Thịnh, Diễn Châu, Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(410, 1, '1790', 'Hoàng Thị Lý', '1997-11-10', 'Nữ', NULL, NULL, 'Cử nhân xét nghiệm y học', NULL, '0987047448', 'tangvan@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(411, 1, '1898', 'Hoàng Thị Thuý Hiền', '1998-06-05', 'Nữ', NULL, NULL, 'Cử nhân xét nghiệm', NULL, '0949910958', 'lephuocan@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(412, 1, '1902', 'Hoàng Thị Quý', '1997-05-27', 'Nữ', NULL, NULL, 'Cử nhân xét nghiệm', NULL, '0965406562', 'vyvy15071997@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(413, 1, '1548', 'Phạm Thị Phương', '1996-01-23', 'Nữ', NULL, NULL, 'CN  KTV', NULL, '0364190648', 'ngocthuyphan20081985@gmail.com', 'Xóm 10 Nghi Đức TP Vinh Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(414, 1, '1896', 'Bùi Thị Hà', '1994-06-21', 'Nữ', NULL, NULL, 'Cử nhân xét nghiệm', NULL, '0343329236', NULL, NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(415, 1, '0231', 'Hoàng Thị Phương', '1981-10-28', 'Nữ', NULL, NULL, 'CN KTV', NULL, '0946039776', NULL, 'Khối Tân Lộc, P. Hưng Dũng, Tp Vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(416, 1, '0368', 'Nguyễn Thị Bích Thảo', '1976-10-04', 'Nữ', NULL, 27, 'Cao đăng điều dưỡng', NULL, '0978976662', 'hkute75@gmail.com', 'Xóm 13, Xã Nghi Phú, Tp Vinh, Nghệ an', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(417, 1, '1365', 'Sầm Thái Ngân', '1996-02-16', 'Nữ', NULL, NULL, 'CN CĐKTVXN', NULL, '0966787796', 'nguyenhuyentrangtckn@gmail.com', 'Khối 12 - Hà Huy Tập - Vinh - Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(418, 1, '0832', 'Lê Thị Hương', NULL, 'Nữ', NULL, 27, 'Cao đăng điều dưỡng', NULL, '0915445887', 'nguyenthimai200594@gmail.com', 'Khối Xuân Bắc, Hưng Dũng, To Vinh, Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(419, 1, '0234', 'Nguyễn Lê Thành Chung', '1983-03-26', 'Nam', NULL, NULL, 'CN KTV', NULL, '0946257069', 'nghia.leanh69@gmail.com', 'Khối Xuân Bắc, Hưng dũng, Vinh, Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(420, 1, '1796', 'Đào Việt Hà', '1996-12-07', 'Nữ', NULL, NULL, 'Cao đẳng xét nghiệm y học', NULL, '0968826028', NULL, NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(421, 1, '0244', 'Nguyễn Tài Tiến', '1979-09-21', 'Nam', NULL, 53, 'BSCK II', 'Giải phẫu bệnh', '0982936345', NULL, 'Khối 13, Phường Đông vĩnh, Tp Vinh, Nghệ an', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(422, 1, '1441', 'Nguyễn Thị Phương Thảo', '1994-08-10', 'Nữ', NULL, 53, 'Bác sĩ', NULL, '0966048850', 'nguyennam20071989@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(423, 1, '1017', 'Lê Văn Hưng', '1989-09-20', 'Nam', 'Phó khoa, PTDH khoa Giải phẫu bệnh', 53, 'BSCK I', 'Giải phẫu bệnh', '0973047740', 'vuongphuongdung88@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(424, 1, '1317', 'Đinh Thị Thùy', '1986-06-27', 'Nữ', NULL, 53, 'BSCK I', 'Giải phẫu bệnh', '01697163866', 'drnguyennga1709@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(425, 1, '0246', 'Nguyễn Ngọc Tân', '1982-08-02', 'Nam', NULL, 53, 'Cao đăng điều dưỡng', NULL, '0912376292', 'vinh21996@gmail.com', 'Xóm 15 - Nghi Kim - TP Vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(426, 1, '1511', 'Lê Thanh Tài', '1994-05-02', 'Nam', NULL, 53, 'Cao đăng điều dưỡng', NULL, '0986570307', 'lelamtra70@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(427, 1, '1291', 'Bùi Ngọc Hiếu', '1991-12-20', 'Nam', 'Phụ trách KTV trưởng', 53, 'CN KTVXN', NULL, '0968011221', 'dinhngatron71@gmail.com', 'Xóm 14 - Xã Nghi Phú -TP Vinh Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(428, 1, '1786', 'Phan Thị Trang', '1997-12-15', 'Nữ', NULL, 53, 'Cử nhân xét nghiệm y học', NULL, '0359437488', 'nguyenletan72@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(429, 1, '1073', 'Nguyễn Thị Thúy Vân', '1992-03-07', 'Nữ', NULL, 53, 'CN CĐKTVXN', NULL, '0964696092', NULL, NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(430, 1, '1792', 'Biện Văn Giáp', '1994-04-18', 'Nam', NULL, 53, 'Cao đẳng xét nghiệm y học', NULL, '0978287137', 'hoangtonhu76@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(431, 1, '1793', 'Trần Thị Chín', '1993-06-24', 'Nữ', NULL, 53, 'Cao đẳng xét nghiệm y học', NULL, '0395254009', 'quynhanhmattinh88@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(432, 1, '1795', 'Phạm Thị Lý', '1994-11-12', 'Nữ', NULL, 53, 'Cao đẳng xét nghiệm y học', NULL, '0334440767', NULL, NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(433, 1, '0387', 'Nguyễn Thị Thu Thủy', '1977-09-22', 'Nữ', NULL, 27, 'CN CĐPS', NULL, '0915770819', 'chomotngaynang221296@gmail.com', 'K2, Phường Quán Bàu, Tp Vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(434, 1, '1370', 'Thái Hoàng Long', '1988-07-31', 'Nam', NULL, 16, 'Cử nhân KHMT', NULL, '0973730863', 'nhinsaobang@gmail.com', 'Trường Thi - TP Vinh - Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(435, 1, '0233', 'Hồ Thị Huyền Phương', '1976-02-20', 'Nữ', NULL, 27, 'CĐ KTVXN', NULL, '0986662246', 'phamtuan.yhn@gmail.com', 'Tân Tiến, Hưng Dũng, Nghệ an', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(436, 1, '0157', 'Lương Thị Tuyết', '1983-05-07', 'Nữ', NULL, 16, 'LĐ PT', NULL, '0857957235', 'ngovanthiet1989@gmail.com', 'Nghi Thái - Nghi Lộc - Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(437, 1, '0375', 'Lê Thị Thu Hiền', '1977-12-11', 'Nữ', NULL, 27, 'Cao đăng điều dưỡng', NULL, '0903444789', 'Bongbang8810@gmail.com', 'Hà Huy Tập, Tp Vinh, Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(438, 1, '0997', 'Bùi Văn Dược', '1989-02-08', 'Nam', NULL, 16, 'Kỹ sư', NULL, '0945087789', 'conghoandhy@gmail.com', 'Hưng Dũng - Vinh - Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(439, 1, '0169', 'Nguyễn Thị Hiền', '1984-10-08', 'Nữ', NULL, 16, 'LĐ PT', NULL, '0975499527', 'vankhanhnguyen291@gmail.com', 'Nghi Diên - Nghi Lộc - Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(440, 1, '0170', 'Nguyễn Thị Phương', '1985-08-17', 'Nữ', NULL, 16, 'LĐ PT', NULL, '0399400485', 'Dungbjthu@gmail.com', 'Nghi Thái - Nghi Lộc - Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(441, 1, '0628', 'Nguyễn Thị Thanh Vân', NULL, 'Nữ', NULL, 27, 'ĐDTH', NULL, '0943170568', NULL, 'K 4 - P Trường Thi - TP vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(442, 1, '0555', 'Võ Thị Thu Phương', '1976-09-09', 'Nữ', 'NHS trưởng', 44, 'CN ĐD', 'Cử nhân điều dưỡng phụ sản', '0976438530', NULL, 'Khối Trung Nghĩa, P. Đông Vĩnh, Tp Vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(443, 1, '0039', 'Nguyễn Thị Thảo', '1977-05-10', 'Nữ', NULL, 44, 'CN CĐPS', NULL, '09034334252', NULL, 'Khối 5, Đội Cung, Vinh, Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(444, 1, '0168', 'Phan Thị Trúc', '1981-05-10', 'Nữ', NULL, 16, 'LĐ PT', NULL, '0968757538', 'ThanhHuyenNguyen09111995@gmail.com', 'Nghi Phú - Vinh - Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(445, 1, '0590', 'Trần Thị Nguyệt', '1974-11-07', 'Nữ', NULL, 44, 'CN CĐPS', NULL, '0977054801', NULL, 'Mỹ Thượng - Hưng Lộc - TP Vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(446, 1, '0595', 'Nguyễn Thị Thúy Hải', '1976-10-16', 'Nữ', NULL, 44, 'CN CĐPS', NULL, '0343177997', 'nhutron94@gmail.com', 'Xóm 19 - Nghi Phú - TP Vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(447, 1, '0160', 'Trần Thị Kiều Dung', '1983-10-01', 'Nữ', NULL, 16, 'LĐ PT', NULL, '0368323553', 'tranthanhhmu@gmail.com', 'Quán Bàu - Vinh - Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(448, 1, '0270', 'Hồ Quốc Chung', '1979-07-05', 'Nam', NULL, 16, 'NV KT', NULL, '0977312242', 'nguyenducphuckhoacc@gmail.com', 'Xóm 11, xã Hưng Lộc, Tp Vinh, Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(449, 1, '0272', 'Nguyễn Quốc Huy', NULL, 'Nam', NULL, 16, 'NV KT', NULL, '0911298512', NULL, '27 Tôn Thất Tùng, Khối Xuân Bắc, Hưng Dũng, Tp Vinh, Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(450, 1, '0167', 'Phạm Thị Lan', '1982-04-22', 'Nữ', NULL, 16, 'LĐ PT', NULL, '0978620619', NULL, 'Hưng Lộc - TP Vinh - Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(451, 1, '0589', 'Nguyễn Thị Hạnh', '1974-04-18', 'Nữ', NULL, 44, 'CN CĐPS', NULL, '915233488', 'nguyenquanghuyen.dd5b@gmail.com', 'Xóm 19, Nghi Phú, Tp Vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(452, 1, '0363', 'Thái Thị Thanh Huyền', '1978-03-09', 'Nữ', 'NHS trưởng', 61, 'CN NHS', NULL, '0945753939', NULL, 'K3, P. Hà Huy Tập, Tp Vinh, Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(453, 1, '0400', 'Phan Anh Trâm', '1977-06-01', 'Nam', 'Trưởng khoa Kiểm soát nhiễm khuẩn', 16, 'BSCK II', 'Gây mê hồi sức', '945457666', NULL, 'Xóm Ngũ Lộc, Xã Hưng Lộc, Tp Vinh, Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(454, 1, '1683', 'Bùi Thị Mai', '1988-12-06', 'Nữ', NULL, 16, 'Cử nhân điều dưỡng', NULL, '09478310780374659552', NULL, NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(455, 1, '1695', 'Nguyễn Thị Bé Nhi', '1997-05-19', 'Nữ', NULL, 16, 'Cử nhân điều dưỡng', NULL, '0385758706', NULL, NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(456, 1, '0838', 'Nguyễn Thị Nga', '1989-08-28', 'Nữ', NULL, 16, 'Cao đăng điều dưỡng', NULL, '0985509433', 'truongthiduyen01041993@gmail.com', 'Xóm Nam Bình, Vân Diên, Nam Đàn, Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(457, 1, '0037', 'Lê Thị Hoài Thơ', '1981-10-18', 'Nữ', NULL, 16, 'ĐDTH', NULL, '09428225670981610789', NULL, 'Sô 19, ngoc 2 hẻm 2 , khối Yên Bình, P, Hưng Phúc, Tp Vinh, Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(458, 1, '0038', 'Nguyễn Thị Tuyết Nhung', '1990-11-08', 'Nữ', NULL, 16, 'Cao đăng điều dưỡng', NULL, '0946929789', NULL, 'Xuân Hùng, Hưng Lộc, Tp Vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(459, 1, '0603', 'Phạm Thị Thu Hằng', NULL, 'Nữ', NULL, 45, 'BSCK I', 'Tai mũi họng', '0912221895', NULL, 'Khối 23, phường Hưng Bình, Tp Vinh, Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(460, 1, '1772', 'Hoàng Thị Duyên', '1994-02-07', 'Nữ', NULL, 16, 'Cao đăng điều dưỡng', NULL, '0353352681', NULL, NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(461, 1, '0674', 'Nguyễn Thị Ngọc', '1988-10-20', 'Nữ', 'Điều dưỡng trưởng', 16, 'Cử nhân điều dưỡng', NULL, '0934527909', 'ngothilananh0889@gmail.com', 'Nhà số 2, ngõ 12, đường Tân Hùng, Hưng lộc, Tp Vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(462, 1, '0696', 'Trần Thị Lan Anh', '1980-04-07', 'Nữ', NULL, 16, 'Cao đăng điều dưỡng', NULL, '0913796406', NULL, 'Phường Đội Cung, Tp Vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(463, 1, '1523', 'Lê Thị Minh', '1989-12-05', 'Nữ', NULL, 16, 'Cao đăng điều dưỡng', NULL, '973164350', NULL, 'Diễn Đoài - Diễn Châu - Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(464, 1, '1233', 'Nguyễn Thị Thu Hoài', '1990-08-24', 'Nữ', NULL, 16, 'Cao đăng điều dưỡng', NULL, '0972781030', NULL, 'Hưng Lộc- Tpvinh- Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(465, 1, '1751', 'Nguyễn Thị Lam', '1997-12-09', 'Nữ', NULL, 16, 'Cao đăng điều dưỡng', NULL, '0987784925', 'anhthi3995@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(466, 1, '1512', 'Cao Cự Tùng', '1993-03-05', 'Nam', NULL, 72, 'Cao đăng điều dưỡng', NULL, '01663467477', NULL, NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(467, 1, '0510', 'Cao Thị Thủy', '1983-01-02', 'Nữ', NULL, 16, 'Cao đăng điều dưỡng', NULL, '0977337067', NULL, 'Xóm 9, Hưng Lộc, Tp Vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(468, 1, '1783', 'Nguyễn Thị Giang', '1994-06-17', 'Nữ', NULL, 16, 'Cao đẳng hộ sinh', NULL, '0386101373', NULL, NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(469, 1, '1354', 'Nguyễn Hải Trường', '1993-02-16', 'Nam', NULL, 16, 'CNKH\nMT', NULL, '0945841138', 'Nguyendinhquyen1987@gmail.com', 'Hà Huy Tập - TP Vinh- Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(470, 1, '1570', 'Hoàng Thị Thùy Linh', '1994-01-06', 'Nữ', NULL, 16, 'Kỹ sư khoa học môi trường', NULL, '0366402635', 'Chuvanhau2@gmail.com', 'Vinh Tân - Vinh - Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(471, 1, '1571', 'Đàm Thị Thục Huyền', '1995-03-01', 'Nữ', NULL, 16, 'Cử nhân khoa học môi trường', NULL, '0944105595', NULL, 'Bến Thủy - Vinh - Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(472, 1, '1803', 'Nguyễn Thị Phương Thảo', '1997-04-09', 'Nữ', NULL, 15, 'DSĐH', NULL, '0799009048', NULL, NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(473, 1, '0311', 'Trần Thị Minh Hải', '1982-09-22', 'Nữ', NULL, 15, 'CĐ dược', NULL, '0985520991', NULL, 'Xóm 12 -Nghi Phú - TP vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(474, 1, '0314', 'Hoàng Văn Thái', NULL, 'Nam', NULL, 15, 'D.tá', NULL, '01687100576', NULL, 'Hưng Lộc - TP Vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(475, 1, '1388', 'Bùi Thị Bích Ngọc', '1983-07-17', 'Nữ', NULL, 15, 'DSĐH', 'Quản lý và cung ứng thuốc', '0916539386', NULL, 'P. Hưng Bình - Thành phố Vinh - Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(476, 1, '0916', 'Lê Thị Tâm', '1986-02-05', 'Nữ', NULL, 15, 'DSĐH', 'Quản lý và cung ứng thuốc', '0911788682', NULL, 'SN 42 Yên Dũng Thượng - P.Hưng Dũng - TP . Vinh Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(477, 1, '0604', 'Chu Thị Kim Anh', '1976-05-15', 'Nữ', 'PTK, Phó khoa Tai mũi họng', 45, 'BSCK II', 'Tai mũi họng', '983459789', NULL, 'Hưng phúc -TP Vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(478, 1, '0613', 'Trần Thị Nhã', '1970-05-15', 'Nữ', NULL, 45, 'Cao đăng điều dưỡng', NULL, '0904496565', 'drtrang1988@gmail.com', 'Phường Hưng Dũng -TP Vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(479, 1, '0435', 'Trần Thị Lý', '1971-08-02', 'Nữ', NULL, 45, 'CN CĐPS', NULL, '0985033829', 'vanhienmai929694@gmail.com', 'Khu TT Bệnh viện, xóm 11, xã Hưng Lộc, Tp Vinh, Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(480, 1, '0298', 'Phan Thị Thanh Huyền', '1983-08-07', 'Nữ', NULL, 15, 'CĐ dược', NULL, '0916630998', 'quoctuanktk@gmail.com', 'Xuân Bắc - Hưng Dũng - TP Vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(481, 1, '0300', 'Trịnh Thị Quỳnh Nga', '1982-10-28', 'Nữ', NULL, 15, 'CĐ dược', NULL, '0937387398', 'nguyenthihanh10081983@gmail.com', 'Mỹ Thượng - Hưng Lộc - TP Vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(482, 1, '0301', 'Nguyễn Xuân Đoàn', '1983-02-20', 'Nam', NULL, 15, 'CĐ dược', NULL, '0914507905', NULL, 'Hưng Lộc - TP Vinh- Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0);
INSERT INTO `dm_nhan_vien` (`id`, `benh_vien_id`, `ma_nv`, `ho_ten`, `ngay_sinh`, `gioi_tinh`, `chuc_danh`, `khoa_phong_id`, `trinh_do`, `chuyen_khoa`, `dien_thoai`, `email`, `dia_chi`, `trang_thai`, `ngay_tao`, `ngay_cap_nhat`, `nguoi_tao`, `nguoi_cap_nhat`, `da_xoa`) VALUES
(483, 1, '0302', 'Võ Mai Vân', '1980-11-04', 'Nữ', NULL, 15, 'CĐ dược', NULL, '0914376996', 'lehoa260588@gmail.com', '127 Nguyễn Phong Sắc, phường Hưng Dũng, Tp Vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(484, 1, '0303', 'Võ Thị Thanh Loan', '1986-01-31', 'Nữ', NULL, 15, 'CĐ dược', NULL, '0906667598', NULL, 'Nghi Kim - TP Vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(485, 1, '0304', 'Nguyễn Thị Thu Hồng', '1983-08-18', 'Nữ', NULL, 15, 'CĐ dược', NULL, '0943306777', NULL, 'Hưng Lộc - TP Vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(486, 1, '0306', 'Nguyễn Thị Thu Hà', '1987-09-03', 'Nữ', NULL, 15, 'CĐ dược', NULL, '0976423836', NULL, 'Xóm 11 - Hưng Lộc - TP Vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(487, 1, '0307', 'Hồ Thị Vinh', '1987-10-10', 'Nữ', NULL, 15, 'CĐ dược', NULL, '978282367', 'fiyannguyen@gmail.com', 'Khối 10 -Lê Lợi - TP Vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(488, 1, '0308', 'Dư Thị Hà Giang', '1986-04-07', 'Nữ', NULL, 15, 'CĐ dược', NULL, '0979770486', 'lequochuy98ht@gmail.com', 'P. Hưng Bình - Thành phố Vinh - Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(489, 1, '0310', 'Trương Thị Hoài Thu', '1987-11-01', 'Nữ', NULL, 15, 'CĐ dược', NULL, '0965377359', 'nguyenhongnl.ykv@gmail.com', 'P.Hưng Dũng- TP Vinh - Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(490, 1, '0891', 'Nguyễn Thị Bá Duyên', '1987-12-16', 'Nữ', NULL, 15, 'CĐ dược', NULL, '0973259759', 'ngocnh2010@gmail.com', 'Phường Vinh Tân - Thành phố Vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(491, 1, '0892', 'Nguyễn Thị Hải Yến', '1990-10-01', 'Nữ', NULL, 15, 'CĐ dược', NULL, '0942302290', 'yhanoi2000@gmail.com', 'P. Quán Bàu - Thành phố Vinh - Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(492, 1, '0299', 'Lê Thị Hằng Nga', '1986-06-15', 'Nữ', NULL, 15, 'CĐ dược', NULL, '0982820678', 'letienvien@gmail.com', '220 - P Hà Huy Tập -TP Vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(493, 1, '0972', 'Lê Thị Kiên', '1989-05-01', 'Nữ', NULL, 15, 'CĐ dược', NULL, '0976448884', 'tuananh01021994@gmail.com', 'P.Hưng Bình- TP Vinh - Nghệ an', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(494, 1, '0975', 'Lương Thị Thu Hòa', '1991-08-27', 'Nữ', NULL, 15, 'CĐ dược', NULL, '0979856207', 'nguyentrongtoan1003@gmail.com', 'P.Trung Đô - Thành phố Vinh - Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(495, 1, '0973', 'Nguyễn Thị Giang', '1990-12-02', 'Nữ', NULL, 15, 'CĐ dược', NULL, '0988436366', 'tiensisinhhoctheky21@gmail.com', 'Phường Vinh Tân - Thành phố Vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(496, 1, '1010', 'Trần Thị Nhung', '1984-08-26', 'Nữ', NULL, 15, 'CĐ dược', NULL, '0968658584', 'gloryvn1996@gmail.com', 'Xóm 7 - Diễn Liên - Diễn Châu - Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(497, 1, '0284', 'Lương Quốc Tuấn', '1970-04-01', 'Nam', 'Trưởng khoa Dược', 15, 'DSCK II', NULL, '0913522150', 'Tranthily6989@gmail.com', '225 Nguyễn Văn Cừ - P Hưng Bình - TP Vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(498, 1, '0029', 'Nguyễn Thị Thanh Hoa', '1988-08-15', 'Nữ', NULL, 15, 'DSCK I', NULL, '0917125222', 'Thaik55khmt@gmail.com', '57 Đường Phùng Khắc Khoan, Hồng Lĩnh, Hà Tĩnh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(499, 1, '0352', 'Lê Thị Phước An', NULL, 'Nữ', 'Trưởng khoa Răng hàm mặt', 46, 'BSCK I', 'Răng hàm mặt', '0982312969', 'nguyenbichngoc051286@gmail.com', 'Khối Yên Sơn - P Hà Huy Tập - TP vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(500, 1, '0978', 'Dương Thị Thanh', '1989-04-30', 'Nữ', NULL, 15, 'Thạc sỹ dược', NULL, '0987353222', 'nguyenthithuy.1004.ykv@gmail.com', 'Khối Trung Hòa - Phường Lê Mao - TP Vinh - Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(501, 1, '0287', 'Hoàng Thị Thanh Huyền', '1987-12-30', 'Nữ', NULL, 15, 'Thạc sỹ dược', NULL, '0986299633', 'suriphuonganh30@gmail.com', 'Phường Trường Thi- TP Vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(502, 1, '0288', 'Trần Thị Thu Hằng', '1984-08-21', 'Nữ', NULL, 15, 'DSCK I', NULL, '0942838484', 'vanvuive0312@gmail.com', 'K1 - Quỳnh Giát - Quỳnh Lưu -NA', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(503, 1, '0290', 'Lê Thị Mỹ Hạnh', '1987-05-02', 'Nữ', NULL, 15, 'DSCK I', NULL, '0988310205', 'danghoai25091994@gmail.com', 'P. Hưng Dũng - Thành phố Vinh - Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(504, 1, '0291', 'Nguyễn Thị Lương', '1988-07-20', 'Nữ', NULL, 15, 'DSCK I', NULL, '0934420788', 'ddquangluong@gmail.com', 'Thị trấn Hưng Nguyên - Hưng Nguyên - Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(505, 1, '1444', 'Đào Thị Hằng', '1995-08-27', 'Nữ', NULL, 15, 'DSĐH', NULL, '0974069929', 'nguyenthilan4812@gmail.com', 'Kim Liên- Nam Đàn - Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(506, 1, '1198', 'Nguyễn Thị Hồng Lê', '1992-03-24', 'Nữ', NULL, 15, 'Thạc sĩ dược', NULL, '374062686', NULL, 'Nghi Đức - Thành phố Vinh - Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(507, 1, '0022', 'Lê Thị Thanh Trà', '1970-04-18', 'Nữ', 'Trưởng khoa Mắt, Giám đốc Trung tâm Dịch vụ tổng hợp', 47, 'BSCK II', 'Mắt', '0948887789', 'Huyenledc@gmail.com', 'Nghi Phú, Tp Vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(508, 1, '1799', 'Tăng Thị Thúy', '1996-07-22', 'Nữ', NULL, 15, 'Dược sĩ đại học', NULL, '0328549396', NULL, NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(509, 1, '1312', 'Lê Thị Hương Giang', '1993-07-25', 'Nữ', NULL, 15, 'DSĐH', NULL, '973209909', 'nguyendiepha123@gmail.com', 'K8-Bến Thủy - Vinh - Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(510, 1, '1938', 'Lê Thị Hải Lý', '1999-03-23', 'Nữ', NULL, 15, 'DSĐH', NULL, '0989971536', 'tranphucicupro@gmail.com', 'Phường Quang trung- Thành phố Vinh- Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(511, 1, '1917', 'Hồ Hải Yến', '1997-08-20', 'Nữ', NULL, 15, 'DSĐH', NULL, '0942008597', 'hhadiep37@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(512, 1, '1920', 'Hồ Thị Quyên', '1976-10-20', 'Nữ', NULL, 15, 'CĐ dược', NULL, '0373335083', 'Buithithuha586@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(513, 1, '1921', 'Nguyễn Thị Hà Lê', '1995-08-01', 'Nữ', NULL, 15, 'Dược sĩ đại học', NULL, '0978677232', 'anhngochqy@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(514, 1, '1922', 'Nguyễn Diệu Hoa', '1993-02-26', 'Nữ', NULL, 15, 'CĐ dược', NULL, '0986111639', 'sieuviet23@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(515, 1, '1923', 'Hoàng Minh Châu', '1993-02-26', 'Nữ', NULL, 15, 'Trung cấp Dược', NULL, '0913041413', 'tranthigiang2021997@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(516, 1, '1924', 'Nguyễn Thị Thảo', '1985-05-19', 'Nữ', NULL, 15, 'CĐ dược', NULL, '0971449727', 'bs.vothuhuyen@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(517, 1, '1925', 'Hồ Thị Vân', '1994-01-14', 'Nữ', NULL, 15, 'CĐ dược', NULL, '0326086269', NULL, NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(518, 1, '1926', 'Nguyễn Thị Quỳnh Anh', '1971-12-06', 'Nữ', NULL, 15, 'CĐ dược', NULL, '0966977444', NULL, NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(519, 1, '1927', 'Lưu Thị Ngoan', '1992-04-29', 'Nữ', NULL, 15, 'CĐ dược', NULL, '0984616429', NULL, NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(520, 1, '1928', 'Đỗ Phương Thảo', '1996-07-05', 'Nữ', NULL, 15, 'Dược sĩ đại học', NULL, '0976322930', NULL, NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(521, 1, '1929', 'Nguyễn Thị Phương Linh', '1996-07-05', 'Nữ', NULL, 15, 'Cao đẳng Dược', NULL, '0974149790', 'tranthuhoai211@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(522, 1, '1259', 'Lê Thị Huyền', '1993-10-08', 'Nữ', NULL, 27, 'Cao đăng điều dưỡng', NULL, '0966746333', 'taitdykv@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(523, 1, '0936', 'Võ Thanh Ngọc', '1988-06-29', 'Nam', NULL, NULL, 'BSCK I', 'Ngoại lồng ngực', '0943766883', NULL, NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(524, 1, '0360', 'Lê Thị Hương Trầm', '1984-03-03', 'Nữ', NULL, 27, 'BSCK I', 'Tim mạch', '0912753737', 'bsdang90@gmail.com', 'Khối Tân Tién -P.Lê Mao - Vinh-NA', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(525, 1, '0829', 'Nguyễn Thành Chung', '1980-12-10', 'Nam', NULL, 27, 'BSCK II', 'Nội tiêu hóa', '0913065626', 'cobemobifone.le@gmail.com', 'Đức Lâm - Đức Thọ - Hà Tĩnh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(526, 1, '0637', 'Trần Thị Thanh Huyền', '1980-02-21', 'Nữ', NULL, 47, 'BSCK I', 'Mắt', '0983243658', NULL, 'Hà Huy Tập - TP Vinh - Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(527, 1, '0759', 'Võ Đình Hạnh', '1983-10-23', 'Nam', NULL, 27, 'BSCK I', 'Nội khoa', '0983501823', NULL, 'Phú Thành - yên Thành -Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(528, 1, '1579', 'Nguyễn Trung Hướng', '1965-01-27', 'Nam', NULL, 27, 'BSCK I', 'Răng hàm mặt', '0915698252', NULL, 'Hưng Dũng - TP Vinh - Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(529, 1, '1580', 'Trần Văn Vinh', '1963-03-18', 'Nam', NULL, 27, 'BSCK I', 'Tai mũi họng', '0978805548', 'souldontcry@gmail.com', 'Xóm Lam Dinh - Thanh Giang - Thanh Chương - Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(530, 1, '1385', 'Lê Thị Kim Dung', '1986-08-01', 'Nữ', NULL, 27, 'BSCK I', 'Huyết học truyền máu', '0974284828', 'phuong.ptt3110@gmail.com', 'Tổ dân phố 5 Bắc Hồng- Tx Hồng Lĩnh- Hà Tĩnh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(531, 1, '1814', 'Lê Văn Hậu', '1996-12-10', 'Nam', NULL, 27, 'Bác sĩ', NULL, '0985761701', 'nguyenthiduyen959595@gamil.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(532, 1, '0934', 'Chu Xuân Hoàng', '1988-12-24', 'Nam', NULL, 43, 'Thạc sĩ', 'Ngoại khoa', '0918064668', 'hieuhaphuong@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(533, 1, '0729', 'Hoàng Thị Thùy', '1987-02-12', 'Nữ', NULL, 27, 'Thạc sĩ', 'Nội khoa', '0947568919', 'ngoluc.cc@gmail.com', 'Khối 3, P Bến Thuỷ, Tp Vinh, Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(534, 1, '1368', 'Phạm Văn Linh', '1993-05-11', 'Nam', NULL, NULL, 'Bác sĩ', NULL, '0984360241', NULL, 'Hậu Thành - Yên Thành - Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(535, 1, '0928', 'Nguyễn Thị Trà Giang', '1988-03-05', 'Nữ', NULL, 27, 'Bác sĩ', NULL, '946748847', 'tranthaianhhoang@gmail.com', 'Phường quán bàu-TP vinh- Nghệ an', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(536, 1, '0797', 'Nguyễn Thị Mỹ Linh', '1982-01-03', 'Nữ', NULL, 27, 'Thạc sĩ', 'Thần kinh', '0974746778', 'vohienbvdk@gmail.com', 'K13 - Phường Hưng Dũng - TP Vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(537, 1, '1029', 'Phan Văn Thắng', '1988-04-11', 'Nam', NULL, 42, 'Bác sỹ nội trú', 'Ngoại khoa', '0967358622', 'baothoa100991@gmail.com', 'Hà Huy Tập- TP Vinh- Nghệ an', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(538, 1, '1808', 'Nguyễn Hùng Dũng', '1987-01-08', 'Nam', NULL, 25, 'BSCK I', 'Ngoại thần kinh - sọ não', '0986622448', 'Yenec324@gmail.com', 'Số 2 - Trần Phú (chung cứ Erowindow), Phường Hồng Sơn', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(539, 1, '1599', 'Hoàng Thành Trung', '1983-04-03', 'Nam', NULL, 14, 'BSCK I', 'Ngoại khoa', '0989819737', NULL, NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(540, 1, '0941', 'Đinh Thị Vân Anh', '1988-10-05', 'Nữ', NULL, 44, 'BSCK I', 'Sản phụ khoa', '0974276988', NULL, 'P Quán Bàu -TP Vinh- Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(541, 1, '1037', 'Lê Thị Lê Ny', '1989-10-02', 'Nữ', NULL, 27, 'Thạc sĩ', 'Nội khoa', '0967799904', NULL, 'Nghi Ân - Tp Vinh - Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(542, 1, '1879', 'Nguyễn Thị Mai Nhi', '1995-02-25', 'Nữ', NULL, 27, 'Bác sĩ', NULL, '0966002648', NULL, NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(543, 1, '1608', 'Nguyễn Thị Hà', '1994-09-26', 'Nữ', NULL, 27, 'Bác sĩ', NULL, '0988520804', NULL, NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(544, 1, '1128', 'Nguyễn Thị Vân', '1991-01-02', 'Nữ', NULL, 27, 'BSCK I', 'Nội khoa', '389990623', NULL, 'phường Lê Lợi-TP Vinh-Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(545, 1, '0951', 'Nguyễn Thị Lý', '1986-10-14', 'Nữ', NULL, 27, 'Thạc sĩ', 'Nội khoa', '0942223696', NULL, 'Lê Mao -TP Vinh - Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(546, 1, '0982', 'Nguyễn Hữu Long', '1988-02-20', 'Nam', NULL, 30, 'Thạc sĩ', 'Tim mạch', '0972471858', NULL, 'Nam Anh - Nam Đàn - Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(547, 1, '0956', 'Lê Quang Nhật', '1985-10-26', 'Nam', NULL, 27, 'BSCK I', 'Thần kinh', '978278349', NULL, 'Nghi Kim - TP Vinh - Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(548, 1, '0867', 'Hồ Công Mệnh', '1970-10-15', 'Nam', 'Phó khoa Y học cổ truyền', 27, 'BSCK II', 'Y học cổ truyền', '0989125369', 'na.bvnghean@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(549, 1, '0644', 'Đinh Thị Thúy Nga', '1971-10-30', 'Nữ', 'Điều dưỡng trưởng', 47, 'CN ĐD', NULL, '0914349595', NULL, 'Khối Xuân Bắc, Hưng Dũng, Tp Vinh, Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(550, 1, '0602', 'Hồ Xuân Điềm', NULL, 'Nam', 'Phó khoa Khám bệnh', 27, 'BSCK I', 'Tai mũi họng', '0912046715', NULL, 'Phường Hưng Dũng - TP Vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(551, 1, '0178', 'Nguyễn Tất Thắng', '1975-12-08', 'Nam', NULL, 27, 'BSCK I', 'Chẩn đoán hình ảnh', '0913523856', NULL, 'Lê Mao - TP Vinh - Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(552, 1, '0074', 'Trương Thị Ngân', '1987-10-30', 'Nữ', 'Điều dưỡng trưởng', 27, 'Thạc sĩ sinh học', NULL, '0975662475', NULL, 'Khối Tân Yên - Hưng Bình TP Vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(553, 1, '1241', 'Nguyễn Thị Giang', '1992-02-11', 'Nữ', NULL, 27, 'CN ĐD', NULL, '0369171520', NULL, 'Quán Bàu - Tp Vinh - Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(554, 1, '0456', 'Nguyễn Thị Phương Chi', '1989-04-28', 'Nữ', NULL, 27, 'CN ĐD', NULL, '0962863813', NULL, 'Đường Đinh Lễ, Hưng Dũng, Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(555, 1, '1532', 'Nguyễn Lê Nữ Huyền Trâm', '1993-01-29', 'Nữ', NULL, 72, 'Cao đăng điều dưỡng', NULL, '0945797929', NULL, 'Nghi Phú - TP Vinh - Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(556, 1, '0670', 'Nguyễn Cao Tưởng', '1986-09-02', 'Nữ', NULL, 27, 'Cao đăng điều dưỡng', NULL, '0904438777', NULL, 'Thôn 1, Xuân Hồng, Nghi Xuân, Hà Tĩnh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(557, 1, '0633', 'Nguyễn Thị Lý', '1988-06-18', 'Nữ', NULL, 27, 'Cao đăng điều dưỡng', NULL, '0968770660', 'oanhviet1992@gmail.com', 'Khối Tân Vinh - P Lê Mao - TP Vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(558, 1, '1157', 'Nguyễn Thị Loan', '1991-06-16', 'Nữ', NULL, 27, 'Cao đăng điều dưỡng', NULL, '0982642980', 'caoducthuong94@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(559, 1, '0487', 'Đinh Thị Kim Thư', '1983-07-27', 'Nữ', NULL, 27, 'Cao đăng điều dưỡng', NULL, '0968005778', NULL, 'P 819, Nhà C2, chung cư Đội Cung', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(560, 1, '0425', 'Nguyễn Thị Lệ Tân', '1972-07-20', 'Nữ', NULL, 47, 'Cao đăng điều dưỡng', NULL, '0931369568', 'tranthitamtnt@gamil.com', 'K12. P. Quang Trung, Tp.vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(561, 1, '0371', 'Nguyễn Thanh Hoàn', '1989-06-27', 'Nữ', NULL, 27, 'Cao đăng điều dưỡng', NULL, '0976630689', NULL, 'Trung Đô - Tp Vinh - Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(562, 1, '1191', 'Nguyễn Thị Đào', '1990-05-24', 'Nữ', NULL, 27, 'Cao đăng điều dưỡng', NULL, '0943169688', 'phanmanhcuongna@gmail.com', 'Nghi Phú - Tp Vinh - Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(563, 1, '0898', 'Hoàng Thị Lê', '1988-02-21', 'Nữ', NULL, 27, 'Cao đăng điều dưỡng', NULL, '0976845467', 'tuananhtran.utc@gmail.com', 'Nghi Tân - Cửa Lò - Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(564, 1, '0852', 'Biện Thị Phương Thảo', '1989-08-01', 'Nữ', NULL, 56, 'Cao đăng điều dưỡng', NULL, '0918837858', 'hoangoanh14121998@gmail.com', 'Khối Xuân Bắc, P. Hưng Dũng, Tp Vinh, Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(565, 1, '0334', 'Mai Thị Thanh Thuỷ', '1988-11-20', 'Nữ', NULL, 27, 'Cao đăng điều dưỡng', NULL, '0978513063', NULL, 'Hưng Dũng - TP Vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(566, 1, '0228', 'Nguyễn Thị Thu', '1987-09-12', 'Nữ', NULL, 27, 'Cao đăng điều dưỡng', NULL, '0973871207', 'dr.hangnguyen@gmail.com', 'Quỳnh Vinh, Quỳnh Lưu, Nghệ an', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(567, 1, '1185', 'Trần Thị Thu Hiền', '1988-10-18', 'Nữ', NULL, 27, 'Cao đăng điều dưỡng', NULL, '0988368035', 'phuongthanh.dkna@gmail.com', 'Bến Thủy - TP Vinh - Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(568, 1, '0640', 'Hoàng Tố Như', '1976-09-01', 'Nữ', NULL, 47, 'Cao đăng điều dưỡng', NULL, '0915109757', NULL, 'Khối Xuân Đông, P. Hưng Dũng, Tp Vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(569, 1, '1525', 'Dư Thùy Trang', '1993-02-04', 'Nữ', NULL, 27, 'Cao đăng điều dưỡng', NULL, '0941936765', 'yennth92@gmail.com', 'Hưng Phúc - Tp Vinh - Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(570, 1, '0970', 'Bùi Thị Lài', '1991-02-19', 'Nữ', NULL, 27, 'Cao đăng điều dưỡng', NULL, '0975410609', 'dieulinhmd92@gmail.com', 'Nghi Phú - Tp Vinh - Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(571, 1, '1177', 'Võ Thị Dung', '1987-08-27', 'Nữ', NULL, 27, 'Cao đăng điều dưỡng', NULL, '0973356787', 'drdung2016.hmu@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(572, 1, '1750', 'Quế Thị Hương', '1994-06-30', 'Nữ', NULL, 27, 'Cao đăng điều dưỡng', NULL, '0352848447', 'Chienthang.hmu@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(573, 1, '1578', 'Nguyễn Thị Thùy Linh', '1990-08-18', 'Nữ', NULL, 27, 'Cao đăng điều dưỡng', NULL, '0976467009', 'thanhdinh2505@gmail.com', 'phường Lê Lợi-TP Vinh-Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(574, 1, '0844', 'Nguyễn Thị Tú Linh', '1978-08-10', 'Nữ', 'Phụ trách điều hành công tác điều dưỡng', NULL, 'Cử nhân điều dưỡng', NULL, '972465009', 'phamphuchai1994@gmail.com', 'Khối Xuân Nam, Hưng Dũng, Tp Vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(575, 1, '1542', 'Nguyễn Thị Sen', '1993-04-03', 'Nữ', NULL, 27, 'Cao đăng điều dưỡng', NULL, '0364155493', 'Dr.tranthuhien@gmail.com', 'Nghi Phú - Tp Vinh - Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(576, 1, '0641', 'Cao Thị Hạnh', '1988-08-30', 'Nữ', NULL, 27, 'Cao đăng điều dưỡng', NULL, '0919483036', 'nguyenthigiang113@gmail.com', 'Nhà số 3, Khôíi Xuân Bắc, P. Hưng Dũng, Tp Vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(577, 1, '0338', 'Phan Thị Thành', '1974-05-03', 'Nữ', 'Điều dưỡng trưởng', 85, 'CN ĐD', NULL, '0916045172', 'phamnghia23061996@gmail.com', 'Khối 5 - Phường Hồng Sơn - TP Vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(578, 1, '0775', 'Hà Thị Vinh', '1987-11-10', 'Nữ', NULL, 27, 'Cao đăng điều dưỡng', NULL, '0975648516', NULL, '127 Nguyễn Phong Sắc -TP Vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(579, 1, '0384', 'Bùi Thị Ánh', '1990-11-12', 'Nữ', NULL, 27, 'Cao đăng điều dưỡng', NULL, '0913700045', NULL, 'Phường Hưng Phúc - Vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(580, 1, '0383', 'Nguyễn Thị Thanh Hà', '1990-08-13', 'Nữ', NULL, 27, 'Cao đăng điều dưỡng', NULL, '0978620621', 'nganguyen.bvdk@gmail.com', 'Khối 3 _ Hà Huy Tập - Vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(581, 1, '1213', 'Trần Thị Duyên', '1993-09-02', 'Nữ', NULL, 27, 'Cao đăng điều dưỡng', NULL, '0967461900', 'thuynguyen1015@gmail.com', 'x15 nghi phú tp vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(582, 1, '0468', 'Nguyễn Thị Huyền', '1985-03-13', 'Nữ', NULL, 27, 'Cao đăng điều dưỡng', NULL, '0984744555', 'tulinh@gmail.com', 'Chung cư C1, Đội cung, Tp Vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(583, 1, '1172', 'Trần Thị Thơm', '1991-10-01', 'Nữ', NULL, 27, 'Cao đăng điều dưỡng', NULL, '0856603555', 'nguyenhangnabl89@gmail.com', 'Hưng Thành - Hưng Nguyên - Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(584, 1, '1518', 'Nguyễn Thị Mai Hương', '1997-08-08', 'Nữ', NULL, 27, 'Cao đăng điều dưỡng', NULL, '0967858897', 'thuyhainoilk@gmail.com', 'Nghi Phú - TP Vinh - Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(585, 1, '0330', 'Nguyễn Thị Phương Hoa', '1976-12-05', 'Nữ', NULL, 85, 'Cao đăng điều dưỡng', NULL, '0978014489', 'drtrananhgmhs@gmail.com', 'Khối Xuân Bắc - Hưng Dũng - TP Vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(586, 1, '0372', 'Phạm Thị Ngọc Thương', '1989-12-21', 'Nữ', NULL, 27, 'CN CĐPS', NULL, '0979941289', 'trangdiep10@gmail.com', 'Nghi Tân - Cửa Lò - Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(587, 1, '1069', 'Nguyễn Thị Vân Anh', '1989-11-09', 'Nữ', NULL, 27, 'CN CĐPS', NULL, '0988819490', 'lephuong13031986@gmail.com', 'Bến Thủy - TP Vinh - Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(588, 1, '1100', 'Phạm Thị Thuận', '1990-11-15', 'Nữ', NULL, 27, 'CN CĐPS', NULL, '0396909591', 'trangtran96.IP@gmail.com', 'Hưng Phúc - Tp Vinh - Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(589, 1, '0386', 'Trần Thị Tố Oanh', '1980-08-05', 'Nữ', NULL, 27, 'CN CĐPS', NULL, '0912884616', 'lilyserena170596@gmail.com', 'Xóm 13A, Nghi Kim, Tp Vinh, Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(590, 1, '0721', 'Trần Thị Hồng Thanh', '1978-11-28', 'Nữ', 'Phó khoa Nội tiêu hóa', 32, 'BSCK II', 'Nội tiêu hóa', '0948359658', NULL, NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(591, 1, '0388', 'Hồ Thị Minh Hảo', '1983-12-02', 'Nữ', NULL, 27, 'CN CĐPS', NULL, '0977854315', 'vanmuoimuoi1208@gmail.com', 'P. Hưng Bình, Tp Vinh, Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(592, 1, '0364', 'Lê Dạ Mai Sương', '1983-08-27', 'Nữ', NULL, 72, 'CN CĐPS', NULL, '0942274324', 'Drnguyenhuutan1984@gmail.com', 'Hưng Dũng, Tp Vinh, Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(593, 1, '0369', 'Trần Thị Mai Anh', '1986-09-26', 'Nữ', NULL, 27, 'CN CĐPS', NULL, '0987022486', 'dr.thaibinhduong@gmail.com', 'Nghi Ân - TP Vinh - Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(594, 1, '0077', 'Trần Thị Phương Thảo', '1975-03-17', 'Nữ', 'Điều dưỡng trưởng', 32, 'Cử nhân điều dưỡng', NULL, '0983297012', NULL, NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(595, 1, '0917', 'Nguyễn Thị Trà Giang', '1989-08-06', 'Nữ', NULL, 27, 'CN CĐKTV', NULL, '0917228689', 'daohuong92.kthp@gmail.com', 'Nghi Phú - TP Vinh - Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(596, 1, '0374', 'Đặng Lệ Thủy', '1975-07-20', 'Nữ', NULL, 26, 'Cao đăng điều dưỡng', NULL, '0985727969', 'dr.banhthihongvinh@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(597, 1, '0073', 'Trần Thị Thu Hằng', '1974-10-26', 'Nữ', 'Điều dưỡng trưởng', NULL, 'Thạc sĩ sinh học', NULL, '0949379696', NULL, 'SN 8/21, Đường Nguyễn Du - TP Vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(598, 1, '1231', 'Trần Thị Sang', '1987-10-18', 'Nữ', NULL, 44, 'CN CĐPS', NULL, '0966512768', NULL, 'giang sơn đông - đô lương - nghệ an', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(599, 1, '0988', 'Hồ Thị Phượng', '1987-08-02', 'Nữ', NULL, 27, 'Bác sỹ nội trú', 'Sản phụ khoa', '0369426241', 'maivanhuy060196@gmail.com', 'Sơn Hải - Quỳnh Lưu - Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(600, 1, '0359', 'Nguyễn Thị Khánh Trâm', '1982-12-22', 'Nữ', NULL, 44, 'BSCK I', 'Sản phụ khoa', '0987822205', 'Nguyenthihoana96@gmail.com', 'Hà Huy Tập -TP Vinh- Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(601, 1, '0554', 'Ngô Thị Thanh', '1986-04-03', 'Nữ', NULL, 44, 'Thạc sĩ', 'Sản phụ khoa', '0915587115', 'namhqn@gmail.com', 'Hưng Lộc - TP Vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(602, 1, '1096', 'Trần Thị Ngân', '1990-12-12', 'Nữ', NULL, 44, 'Bác sĩ', NULL, '0971454756', 'bsngocanh87@gmail.com', 'Hưng Lộc -TP Vinh- Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(603, 1, '1018', 'Hoàng Thị Liên', '1989-04-26', 'Nữ', NULL, 44, 'BSCK I', 'Sản phụ khoa', '0979799220', 'giangsonhoang95@gmail.com', 'Hưng Chính -TP Vinh- Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(604, 1, '1122', 'Tạ Thị Thủy', '1990-06-24', 'Nữ', NULL, 44, 'Bác sĩ', NULL, '0389985180', 'phucnguyenpvn@gmail.com', 'Hưng Dũng -TP Vinh -Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(605, 1, '1649', 'Mạnh Trọng Bằng', '1993-11-13', 'Nam', NULL, 44, 'Bác sĩ', NULL, '986583323', 'ngoxuan262@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(606, 1, '1842', 'Trần Thị Lệ', '1995-06-15', 'Nữ', NULL, 44, 'Bác sĩ', NULL, '0972549012', 'thaolinh1608@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(607, 1, '0942', 'Phan Thanh Sơn', '1987-12-10', 'Nam', NULL, 44, 'Thạc sĩ', 'Sản phụ khoa', '0976125438', 'thanhccdkna@gmail.com', 'Lê Lợi - Vinh -  Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(608, 1, '0545', 'Đinh Văn Sinh', '1977-02-08', 'Nam', 'Trưởng khoa Phụ sản', 44, 'BSCK II', 'Sản phụ khoa', '0983575529', 'hahuyenphuong94@gmail.com', 'Hưng lộc - TP Vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(609, 1, '1648', 'Lê Quang Nam', '1993-10-16', 'Nam', NULL, 44, 'Bác sĩ', NULL, '0348568166', 'linhtalinhtinh131194@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(610, 1, '1424', 'Bùi Thị Hải Yến', '1994-12-08', 'Nữ', NULL, 44, 'Bác sĩ', NULL, '0329504435', 'phanhuong1097@gmail.com', 'Hà Huy Tập, TP vinh, Nghệ an', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(611, 1, '0028', 'Võ Tá Trung', '1986-10-10', 'Nam', NULL, 44, 'Thạc sĩ', 'Sản phụ khoa', '0988443566', NULL, 'K 23, Hưng Bình, Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(612, 1, '1840', 'Phan Văn Hiếu', '1996-02-11', 'Nam', NULL, 44, 'Bác sĩ', NULL, '0357156845', 'nguyenhaykv@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(613, 1, '0943', 'Cao Xuân Hùng', '1988-09-18', 'Nam', NULL, 44, 'Thạc sĩ', 'Sản phụ khoa', '918819899', 'phanhoa091216@gmail.com', 'Hà Huy Tập- TP Vinh -Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(614, 1, '1841', 'Nguyễn Phùng Hưng', '1993-04-23', 'Nam', NULL, 44, 'Bác sĩ', NULL, '0973036345', NULL, NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(615, 1, '0541', 'Nguyễn Lâm Thắng', '1970-09-02', 'Nam', NULL, 44, 'Thạc sĩ', 'Sản phụ khoa', '0917307700', 'buiminhluong@gmail.com', 'Kim Liên - Nam Đàn - Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(616, 1, '0769', 'Nguyễn Thị Hoàn', '1973-12-24', 'Nữ', 'Điều dưỡng trưởng', 30, 'Cử nhân điều dưỡng', NULL, '0983316525', 'thaiquyetpha@gmai.com', 'Tổ 6 -Xóm 3A - Nghi Kim -TP Vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(617, 1, '0689', 'Hồ Thị Thu Hà', '1976-09-06', 'Nữ', NULL, 30, 'Cao đăng điều dưỡng', NULL, '0917303727', 'datlinh209@gmail.com', 'Khối Tân Lâm, P. Hưng Dũng, Tp Vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(618, 1, '0784', 'Nguyễn Thị Hiền', '1981-06-12', 'Nữ', NULL, 44, 'CN CĐPS', NULL, '0979001926', 'ngocmaibvhndk@gmail.com', 'Hà Huy Tập - Vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(619, 1, '1107', 'Trần Thị Thuận', '1989-06-20', 'Nữ', NULL, 44, 'CN CĐPS', NULL, '0916522603', NULL, 'Hưng Dũng - TP Vinh-  Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(620, 1, '1110', 'Hoàng Thị Bích Thủy', '1986-05-20', 'Nữ', NULL, 44, 'CN CĐPS', NULL, '0974750742', NULL, 'Hưng Lộc -TP Vinh -Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(621, 1, '1270', 'Nguyễn Thị Hồng Nhung', '1993-12-05', 'Nữ', NULL, 44, 'CN CĐPS', NULL, '0346325925', 'nguyenbaochau1207@gmail.com', 'Hưng Lộc- TP Vinh- Nghệ an', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(622, 1, '0858', 'Trần Thị Long', '1982-05-15', 'Nữ', NULL, 44, 'CN CĐPS', NULL, '0968679626', 'huuthangemergencynghean@gmail.com', 'Trường thi - Thành phố Vinh - Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(623, 1, '1271', 'Trần Thị Phương', '1993-05-10', 'Nữ', NULL, 44, 'CN CĐPS', NULL, '0979214797', NULL, 'Diễn Hùng Diễn Châu Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(624, 1, '1234', 'Nguyễn Thị Hường', '1993-09-06', 'Nữ', NULL, 56, 'CN CĐPS', NULL, '0356529268', NULL, 'Nghi Phong-Nghi Lộc -Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(625, 1, '0768', 'Phan Thị Quỳnh Nga', '1974-09-06', 'Nữ', 'Điều dưỡng trưởng', 31, 'Cử nhân điều dưỡng', NULL, '0916984230', 'haiautsl@gmail.com', 'Xuân Tiến - Hưng Dũng -TP Vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(626, 1, '0819', 'Hữu Thị Hà', '1978-04-10', 'Nữ', NULL, 13, 'Cao đăng điều dưỡng', NULL, '0934353627', 'phamtrale@gmail.com', 'Khối Hải Bằng 2 - P Nghi Hoà - TX Cửa lò -Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(627, 1, '0596', 'Nguyễn Thị Quỳnh Anh', '1984-05-01', 'Nữ', NULL, 44, 'CN CĐPS', NULL, '0982970228', 'Hvan507.cc@gmail.com', 'Hưng lộc - Vinh-  Nghệ an', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(628, 1, '1109', 'Phan Thúy Hằng', '1991-05-03', 'Nữ', NULL, 44, 'CN CĐPS', NULL, '0972983592', 'dauhieudinh87@gmail.com', 'P Hà huy tập- TP Vinh-Nghệ an', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(629, 1, '1251', 'Cao Thị Hoài', '1991-07-23', 'Nữ', NULL, 44, 'CN CĐPS', NULL, '0349737619', NULL, 'Diễn Thọ - Diễn Châu - Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(630, 1, '1782', 'Nguyễn Thị Trang', '1995-04-05', 'Nữ', NULL, 44, 'Cao đẳng hộ sinh', NULL, '0359199021', 'nguyenthiha12121980@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(631, 1, '1784', 'Phan Thị Oanh', '1990-08-03', 'Nữ', NULL, 44, 'Cao đẳng điều dưỡng phụ sản', NULL, '0974323890', NULL, NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(632, 1, '1113', 'Hồ Thị Ánh', '1990-05-12', 'Nữ', NULL, 57, 'CN CĐPS', NULL, '0396966057', 'khanhly24031995@gmail.com', 'Nghi Trung - Nghi Lộc- Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(633, 1, '0389', 'Nguyễn Thị Vinh', '1980-03-09', 'Nữ', NULL, 44, 'CN CĐPS', NULL, '0985851615', NULL, 'Xóm 3, Hưng Lợi, Hưng Nguyên, Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(634, 1, '0849', 'Hoàng Thị Thu Hiền', '1976-06-18', 'Nữ', 'Phó khoa, PTĐH khoa Da liễu', 37, 'BSCK I', 'Da liễu', '0981449886', 'hoangkimtuan.pttk@gmail.com', 'Xóm 2 - Nghi Phú -TP Vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(635, 1, '1103', 'Hoàng Thị Quỳnh', '1991-08-02', 'Nữ', NULL, 44, 'CN CĐPS', NULL, '0972594011', 'landang566@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(636, 1, '1108', 'Nguyễn Thị Châu', '1990-03-16', 'Nữ', NULL, 44, 'CN CĐPS', NULL, '0975168787', NULL, 'Vinh Tân - Vinh - Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(637, 1, '1273', 'Nguyễn Thị Nguyệt', '1990-08-03', 'Nữ', NULL, 44, 'CN CĐPS', NULL, '0971433890', 'nguyenvanphuicu@gmail.com', 'Yên Hồ-Đức Thọ -Hà tĩnh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(638, 1, '0792', 'Lục Thị Minh Giang', '1983-11-16', 'Nữ', NULL, 56, 'CN CĐPS', NULL, '0974806616', NULL, 'Hưng Lộc - TP Vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(639, 1, '1931', 'Lê Hồ Minh Tuấn', NULL, 'Nam', NULL, 44, 'Bác sĩ', NULL, '0941904162', NULL, 'K10, TT Cầu Giát, Quỳnh Lưu, Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(640, 1, '1361', 'Nguyễn Thúy Hiền', '1995-07-26', 'Nữ', NULL, 61, 'CN sư phạm sinh học', NULL, '0932254456', NULL, 'Hưng Dũng- TP Vinh- Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(641, 1, '0551', 'Đoàn Thị Ngọc', '1984-06-28', 'Nữ', NULL, 61, 'BSCK I', 'Sản phụ khoa', '0984157368', NULL, 'Phường Hà Huy Tập -TP Vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(642, 1, '0544', 'Hoàng Ngọc Anh', '1978-06-01', 'Nam', 'Giám đốc Trung tâm Hỗ trợ sinh sản', 61, 'BSCK II', 'Sản phụ khoa', '0975480777', NULL, 'Nghi Phú- TP Vinh- Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(643, 1, '1352', 'Lê Đăng Quang', '1987-12-15', 'Nam', 'Phó Giám đốc Trung tâm Hỗ trợ sinh sản', 61, 'Bác sỹ nội trú', 'Sản phụ khoa', '0976063862', NULL, 'Quán Bàu- TP Vinh- Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(644, 1, '1099', 'Hồ Thị Huyền Trang', '1990-06-19', 'Nữ', NULL, 61, 'Bác sĩ', NULL, '0967450990', NULL, 'Phúc Vinh-Vinh Tân- Vinh-Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(645, 1, '1889', 'Đoàn Văn Hoàng', '1996-08-20', 'Nam', NULL, 61, 'Bác sĩ', NULL, '0962992796', 'suquantu95@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(646, 1, '1890', 'Nguyễn Thị Thủy Tuyên', '1996-03-10', 'Nữ', NULL, 61, 'Bác sĩ', NULL, '0392113647', 'caominh251096@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(647, 1, '1891', 'Nguyễn Huyền Trang', '1996-01-04', 'Nữ', NULL, 61, 'Bác sĩ', NULL, '962900541', NULL, NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(648, 1, '0692', 'Lê Thị Tường Vân', '1974-07-04', 'Nữ', NULL, 76, 'Cao đăng điều dưỡng', NULL, '0942026364', 'locpttk@gmail.com', 'Xuân Nam - Hưng Dũng - Tp Vinh - Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(649, 1, '0566', 'Trần Cẩm Linh', '1987-11-05', 'Nữ', NULL, 61, 'CN CĐPS', NULL, '0904918963', NULL, 'K 6- Bến Thuỷ - TP Vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(650, 1, '1104', 'Hoàng Thị Thanh', '1991-12-06', 'Nữ', NULL, 61, 'CN CĐPS', NULL, '0949568230', 'hoangoanhychmu034@gmail.com', 'Hưng Bình - Vinh - Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(651, 1, '0563', 'Trần Thị Diệu', '1987-02-02', 'Nữ', NULL, 61, 'CN CĐPS', NULL, '0946015828', NULL, 'Khối 7 - TT Tân kỳ -Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(652, 1, '0570', 'Phạm Thị Minh Tâm', '1987-04-22', 'Nữ', NULL, 61, 'CN CĐPS', NULL, '0978969468', NULL, 'Cửa nam- Vinh- Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(653, 1, '1269', 'Chu Thị Huyền', '1993-11-25', 'Nữ', NULL, 61, 'CN CĐPS', NULL, '943701561', NULL, 'Hà Huy Tập TP Vinh Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(654, 1, '1101', 'Ngô Phương Oanh', '1989-07-20', 'Nữ', NULL, 61, 'CN CĐPS', NULL, '0374659173', NULL, 'Đông Vĩnh- Vinh- Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(655, 1, '1106', 'Nguyễn Thị Ngọc Mai', '1989-11-20', 'Nữ', NULL, 72, 'CN CĐPS', NULL, '0986298088', NULL, 'Ngũ Lộc- Hưng Lộc- TP Vinh- Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(656, 1, '0258', 'Phan Thị Tuyền', '1988-10-10', 'Nữ', NULL, 61, 'CN CĐPS', NULL, '0901742434', NULL, 'Khối 7, phường Điiuh Cung, Tp Vinh, Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(657, 1, '0564', 'Dương Thị Quỳnh Anh', '1987-06-01', 'Nữ', NULL, 56, 'CN CĐPS', NULL, '0975464636', NULL, 'Xóm Xuân Bắc, Hưng Dũng, Vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(658, 1, '1114', 'Quế Thị Thương', '1992-12-07', 'Nữ', NULL, 61, 'CN CĐPS', NULL, '0978049991', NULL, 'Nghi Kim- TP Vinh- Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(659, 1, '1789', 'Thái Duy Kiên', '1992-03-19', 'Nam', NULL, 61, 'Thạc sĩ sinh học', NULL, '0963166115', NULL, NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(660, 1, '1349', 'Hoàng Thị Thanh Dịu', '1993-05-21', 'Nữ', NULL, 61, 'Cử nhân xét nghiệm y học', NULL, '0978356444', NULL, 'Xóm 20 - Xã Nghi Phú - Vinh - Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(661, 1, '0931', 'Nguyễn Thị Hồng', '1990-11-12', 'Nữ', NULL, 61, 'Thạc sĩ sinh học', NULL, '0979067685', NULL, 'P Hưng Phúc - Tp Vinh - Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(662, 1, '1266', 'Nguyễn Thị Yến', '1991-05-15', 'Nữ', NULL, 39, 'CN CĐPS', NULL, '0971370035', NULL, 'xóm 9-Hậu Thành-Yên Thành-Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(663, 1, '1094', 'Nguyễn Thị Thúy An', '1990-09-12', 'Nữ', NULL, 39, 'BSCK I', 'Nhi khoa', '0965019878', NULL, 'xã Nam Phúc - huyện Nam Đàn - tỉnh Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(664, 1, '1032', 'Trần Thị Thúy Hà', '1989-01-28', 'Nữ', 'Trưởng khoa Nhi - Sơ sinh', 39, 'BSCK II', 'Nhi - Hô hấp', '0966251357', NULL, 'phường Hưng Phúc - TP Vinh - tỉnh Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(665, 1, '1121', 'Phạm Thị Thanh Thủy', '1990-05-02', 'Nữ', NULL, 27, 'BSCK I', 'Nhi khoa', '0383717576', NULL, 'phường Hà Huy Tập - tp Vinh - tỉnh Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(666, 1, '1330', 'Trần Văn Phú', '1992-03-16', 'Nam', NULL, 39, 'Bác sĩ', NULL, '0982719496', 'lenhathuy78@gmail.com', 'xã Thanh Lộc - huyện Can Lộc - tỉnh Hà Tĩnh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(667, 1, '1631', 'Nguyễn Đình Tuấn', '1995-11-02', 'Nam', NULL, 39, 'Bác sĩ', NULL, '0988720691', 'drlexuanvung@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(668, 1, '1883', 'Lê Thị Mai', '1996-01-13', 'Nữ', NULL, 39, 'Bác sĩ', NULL, '0392548099', 'phannhung2212@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(669, 1, '1097', 'Trần Thị Anh Thái', '1989-11-12', 'Nữ', NULL, 39, 'Bác sĩ', NULL, '973431628', 'drminhcuong@gmail.com', 'phường Hưng Dũng- tp Vinh- tỉnh Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(670, 1, '1085', 'Đào Thị Phương Linh', '1990-06-25', 'Nữ', 'Điều dưỡng trưởng', 39, 'CN ĐD', NULL, '0986821195', 'Tuanhjumikul@gmail.com', 'phường Hà Huy Tập - tp  Vinh - tỉnh  Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(671, 1, '1086', 'Nguyễn Thị Hồng Nhung', '1989-07-02', 'Nữ', NULL, 39, 'CN ĐD', NULL, '0973842283', 'nguyenthihiep19101996@gmail.com', 'xã Xuân Lâm - huyện Nam Đàn - tỉnh Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(672, 1, '1272', 'Lưu Thị Ngọc', '1991-01-28', 'Nữ', NULL, 39, 'CN ĐD', NULL, '0972771115', 'nguyenky94na@gmail.com', 'xã Thanh Liên - huyện Thanh Chương - tỉnh Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(673, 1, '1274', 'Nguyễn Thị Thúy Phương', '1993-12-17', 'Nữ', NULL, 39, 'Cao đăng điều dưỡng', NULL, '0987855693', 'hoangphutai95@gmail.com', 'xã Hưng Lộc - tp Vinh - tỉnh Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(674, 1, '1087', 'Nguyễn Thị Kiều Trang', '1991-07-12', 'Nữ', NULL, 39, 'Cao đăng điều dưỡng', NULL, '0349745410', 'nguyentronghieu120996@gmail.com', 'phường Bến Thủy - Tp Vinh - tỉnh Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(675, 1, '1088', 'Lê Thị Phượng', '1992-01-21', 'Nữ', NULL, 39, 'Cao đăng điều dưỡng', NULL, '0984041752', 'dr.hainguyenvinh@gmail.com', 'phường Cửa Nam - Tp Vinh - tỉnh Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(676, 1, '1278', 'Phan Thị Hiền', '1992-10-27', 'Nữ', NULL, 39, 'Cao đăng điều dưỡng', NULL, '0985963047', 'thuandinhmai1989@gmail.com', 'xã Quỳnh Diễn - huyện Quỳnh Lưu - tỉnh Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(677, 1, '1111', 'Nguyễn Thị Thùy', '1991-08-30', 'Nữ', NULL, 39, 'CN CĐPS', NULL, '0943664416', 'phamtramyeuqui@gmail.com', 'phường Hưng Dũng - tp Vinh - tỉnh Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(678, 1, '0605', 'Nguyễn Ngọc Hùng', '1983-03-28', 'Nam', NULL, 45, 'Thạc sĩ', 'Tai mũi họng', '0912351059', 'phunganhngoc0605@gmail.com', 'Hà Huy Tập - Vinh - Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(679, 1, '1033', 'Phan Thanh Hưng', '1988-09-15', 'Nam', NULL, 45, 'Thạc sĩ', 'Tai mũi họng', '0365477008', NULL, 'Diễn Vạn- Diễn Châu- Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(680, 1, '1428', 'Nguyễn Thị Ngọc', '1994-06-01', 'Nữ', NULL, 45, 'Bác sĩ', NULL, '0969639466', NULL, 'Hưng Dũng-Vinh -Nghệ an', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(681, 1, '0606', 'Phan Đức Chính', '1987-08-09', 'Nam', NULL, 45, 'Thạc sĩ', 'Tai mũi họng', '0915659229', NULL, 'Khối Tân Thành, Phường  Lê Mao, Tp Vinh, Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(682, 1, '0944', 'Lê Hoài Nam', '1988-12-22', 'Nam', NULL, 45, 'Thạc sĩ', 'Tai mũi họng', '0949538581', 'pnthang291295@gmail.com', 'Hưng Bình - Vinh - Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(683, 1, '0361', 'Bùi Thị Hồng Giang', '1980-07-18', 'Nữ', 'Phó trưởng khoa Tai mũi họng', 45, 'BSCK II', 'Mũi họng', '914622828', NULL, 'Ns 13, ngõ 12, Phạm Kinh Vỹ, K6, P. Bến Thuỷ, Tp Vinh, Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(684, 1, '0458', 'Võ Thị Tâm', '1975-04-01', 'Nữ', 'Điều dưỡng trưởng', 37, 'CN CĐPS', NULL, '0977531993', 'hothile28091985@gmail.com', 'Khối Hưng phúc, P. Hưng Phúc, Vinh , Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(685, 1, '0424', 'Chu Thị Lan Anh', NULL, 'Nữ', NULL, 23, 'ĐDTH', NULL, '0946077727', 'bsdk.vietthanh@gmail.com', 'Khối Xuân Bắc - P.Hưng Dũng -TP.Vinh - Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(686, 1, '0457', 'Nguyễn Thị Thanh Tú', '1987-07-03', 'Nữ', 'Phụ trách điều hành ĐDT', 45, 'CN ĐD', NULL, '0987636868', 'ngoduckyna@gmail.com', 'Phường hưng Dũng, Tp Vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(687, 1, '1222', 'Nguyễn Thị Thùy Dung', '1990-05-21', 'Nữ', NULL, 45, 'CN ĐD', NULL, '0979335461', 'Lesangmd@gmail.com', 'Nghi Trường - Nghi Lộc -Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(688, 1, '1446', 'Phan Thị Huyền', '1993-08-26', 'Nữ', NULL, 45, 'CN ĐD', NULL, '0388127103', 'tramhara92@gmail.com', 'Diễn Kim - Diễn Châu - Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(689, 1, '0427', 'Hồ Thị Hằng', '1977-02-04', 'Nữ', NULL, 23, 'Cao đăng điều dưỡng', NULL, '0949367017', 'thongmedical@gmail.com', 'Số nhà 34 đường Nguyễn Đức Cảnh, K 20, Phường Hưng Bình, Tp Vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(690, 1, '0717', 'Hồ Thị Hoa', '1989-02-11', 'Nữ', NULL, 45, 'Cao đăng điều dưỡng', NULL, '0982824727', 'volinh271284@gmail.com', 'Xóm 12, Xã Hưng Lộc, Tp Vinh, Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(691, 1, '0616', 'Vũ Thị Vân', '1986-01-24', 'Nữ', NULL, 45, 'Cao đăng điều dưỡng', NULL, '0981449868', 'hothihoaithuong1984@gmail.com', 'Hưng Dũng -Vinh - Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(692, 1, '0615', 'Nguyễn Thị Chung', '1988-06-06', 'Nữ', NULL, 45, 'Cao đăng điều dưỡng', NULL, '0918191595', 'quocan.hmu.2912@gmail.com', 'Trường Thi-Vinh-Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(693, 1, '1230', 'Phan Thị Thanh Huyền', '1992-09-03', 'Nữ', NULL, 45, 'Cao đăng điều dưỡng', NULL, '0971724625', 'bepnhung.vmu@gmail.com', 'Nghi Phú-  Vinh- Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(694, 1, '0610', 'Nguyễn Thị Minh', '1986-11-10', 'Nữ', NULL, 56, 'CN CĐPS', NULL, '0988372373', 'thaothao101096@gmail.com', 'Hà Huy Tập - Vinh - Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(695, 1, '0430', 'Hoàng Thị Thủy', '1974-08-24', 'Nữ', NULL, 23, 'Cao đăng điều dưỡng', NULL, '0914623679', 'pthao576@gmail.com', 'Số nhaà 16/51 Phuờng Phan Chu Trinh, Khối 3, Phường Đội Cung, Tp Vinh, Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(696, 1, '0624', 'Nguyễn Thị Phương Thảo', '1980-10-03', 'Nữ', NULL, 46, 'BSCK I', 'Răng hàm mặt', '0903405405', 'dangthihang.090892@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(697, 1, '1426', 'Nguyễn Thục Hằng', '1994-05-14', 'Nữ', NULL, 46, 'Bác sĩ', NULL, '0972674017', 'hang0977969775@gmail.com', 'khối 10 - phường hồng sơn tp vinh nghệ an', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(698, 1, '1320', 'Đậu Đức Thành', '1991-11-28', 'Nam', NULL, 46, 'Thạc sĩ', 'Răng hàm mặt', '0962418668', NULL, 'Nghĩa Thuật- TX Thái hòa nghệ an', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(699, 1, '0623', 'Nguyễn Thanh Sơn', '1983-12-25', 'Nam', 'Phó trưởng khoa Răng hàm mặt, kiêm nhiệm tại Trung tâm Đào tạo - Chỉ đạo tuyến', 46, 'BSCK I', 'Răng hàm mặt', '0972114777', NULL, 'Khối 7 - Trường Thi - TP Vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(700, 1, '1845', 'Phạm Thị Hường', '1989-10-20', 'Nữ', NULL, 46, 'Bác sĩ', NULL, '0986471788', 'n.hien17796@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(701, 1, '0622', 'Phan Thị Thục Anh', '1979-01-07', 'Nữ', 'Phó khoa Răng hàm mặt', 46, 'BSCK II', 'Răng hàm mặt', '0982454899', NULL, 'Xuân Hùng - Hưng Lộc -TP Vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(702, 1, '1427', 'Tăng Thị Vân', '1994-07-16', 'Nữ', NULL, 46, 'Bác sĩ', NULL, '0329211403', 'ntkimdung1810@gmail.com', 'diễn hạnh diễn châu nghệ an', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(703, 1, '0426', 'Nguyễn Thị Kiều Vân', '1978-02-27', 'Nữ', NULL, 23, 'Cao đăng điều dưỡng', NULL, '0946402366', NULL, 'Khối Yên Hòa - P. Hà Huy Tập -TP. Vinh - Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(704, 1, '1699', 'Trần Thị Vi', '1997-07-15', 'Nữ', NULL, 46, 'Cử nhân điều dưỡng', NULL, '0388600850', 'lemanhhabvhndk@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(705, 1, '0668', 'Phan Thị Ngọc Thủy', '1985-08-20', 'Nữ', 'Điều dưỡng trưởng', 46, 'Cử nhân điều dưỡng', NULL, '0975425550', NULL, 'Nhà số 8, ngõ A5, Đường nguyễn Phong Sắc', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(706, 1, '0630', 'Trần Thị Thu Hiền', '1980-11-11', 'Nữ', NULL, 46, 'Cao đăng điều dưỡng', NULL, '0912361238', 'lesonbvdkna@gmail.com', 'sn 14 ngõ b2 phường hưng phúc - tp vinh - nghệ an', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(707, 1, '0632', 'Dương Thị Hiền', '1985-05-19', 'Nữ', NULL, 46, 'Cao đăng điều dưỡng', NULL, '0986031307', 'bslinh1988@gmail.com', 'Xóm Mậu Lam-Hưng Lộc -TP Vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(708, 1, '1539', 'Phan Thị Thanh Nhàn', '1997-07-01', 'Nữ', NULL, 46, 'Cao đăng điều dưỡng', NULL, '01689492781', NULL, NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(709, 1, '0627', 'Nguyễn Thị Huyền Trang', '1987-06-01', 'Nữ', NULL, 46, 'Cao đăng điều dưỡng', NULL, '0974853844', 'trandung007@gmail.com', 'Khối Xuân Nam - Hưng Dũng - TP Vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(710, 1, '0629', 'Lê Thị Thu Hà', '1981-01-23', 'Nữ', NULL, 46, 'Cao đăng điều dưỡng', NULL, '0976111368', 'haiblach@gmail.com', 'K 15- Phường Quang Trung - TP Vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(711, 1, '1818', 'Lê Anh Nghĩa', '1996-09-06', 'Nam', NULL, 47, 'Bác sĩ', NULL, '0915839777', 'dungbuidoctor96@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(712, 1, '0027', 'Nguyễn Văn Độ', '1986-12-31', 'Nam', 'Phó khoa Mắt', 47, 'Thạc sĩ', 'Mắt', '0904624877', 'duongthihong156@gmail.com', 'sn 15 , Ngõ 1 , trường tiến , Hưng Bình', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(713, 1, '0639', 'Nguyễn Thị Vân Anh', '1988-01-21', 'Nữ', NULL, 47, 'BSCK I', 'Nhãn khoa', '0904981087', 'nguyenhuutinh1111@gmail.com', 'Xuân Hùng, Hưng Lộc, Tp Vinh, Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(714, 1, '1035', 'Nguyễn Văn Nam', '1989-07-20', 'Nam', NULL, 47, 'Thạc sĩ', 'Nhãn khoa', '0947686992', 'Drmanhbvdkna@gmail.com', 'yên vinh,hưng đông, vinh, nghệ an', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(715, 1, '1036', 'Vương Thị Phương Dung', '1988-06-29', 'Nữ', NULL, 47, 'Bác sĩ', NULL, '0931361388', 'tuoanhts@gmail.com', 'K. Tân Yên, Hưng Bình, tp. Vinh, NA', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0);
INSERT INTO `dm_nhan_vien` (`id`, `benh_vien_id`, `ma_nv`, `ho_ten`, `ngay_sinh`, `gioi_tinh`, `chuc_danh`, `khoa_phong_id`, `trinh_do`, `chuyen_khoa`, `dien_thoai`, `email`, `dia_chi`, `trang_thai`, `ngay_tao`, `ngay_cap_nhat`, `nguoi_tao`, `nguoi_cap_nhat`, `da_xoa`) VALUES
(716, 1, '0638', 'Nguyễn Thị Nga', '1986-09-17', 'Nữ', NULL, 27, 'BSCK I', 'Nhãn khoa', '0984456948', 'trathanh432@gmail.com', 'Nhà số 10, Kiết 87, Đường Nguyễn Viết Thuật, Tp Vinh, Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(717, 1, '1846', 'Nguyễn Cảnh Vinh', '1996-09-21', 'Nam', NULL, 47, 'Bác sĩ', NULL, '0985001207', 'phuong90xx@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(718, 1, '0419', 'Trần Thị Thúy Hằng', '1978-07-14', 'Nữ', NULL, 23, 'Cao đăng điều dưỡng', NULL, '0919895567', 'andanhang78@gmail.com', 'Khối TRường Phúc, P. Hưng Phúc, Tp Vinh, Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(719, 1, '0438', 'Nguyễn Thị Thanh', '1972-12-27', 'Nữ', NULL, 23, 'Cao đăng điều dưỡng', NULL, '0942521352', NULL, 'Tân Hùng, Hưng Lộc, Tp Vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(720, 1, '0423', 'Nguyễn Thị An', '1971-11-11', 'Nữ', NULL, 23, 'Cao đăng điều dưỡng', NULL, '0912832962', NULL, 'Khối 16 - P. Hà Huy Tập - TP Vinh - Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(721, 1, '1726', 'Hoàng Hữu Trọng', '1995-09-27', 'Nam', NULL, 47, 'Cao đăng điều dưỡng', NULL, '0981846072', 'hoaithuong.cdy@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(722, 1, '0999', 'Lê Thị Chung', '1975-09-30', 'Nữ', 'Trưởng khoa Y học cổ truyền', 38, 'BSCK II', 'Y học cổ truyền', '0913355639', 'chungco304@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(723, 1, '0652', 'Bùi Thị Quỳnh Anh', '1988-10-10', 'Nữ', NULL, 47, 'Cao đăng điều dưỡng', NULL, '0904720788', 'thienhoangthi90@gmail.com', 'P.Quang Trung , TP Vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(724, 1, '1516', 'Trần Thị Ngọc Lan', '1994-11-01', 'Nữ', NULL, 47, 'Cao đăng điều dưỡng', NULL, '0974266839', NULL, 'xóm 24 nghi phú,tp vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(725, 1, '0651', 'Lê Thị Vân', '1979-05-25', 'Nữ', NULL, 47, 'Cao đăng điều dưỡng', NULL, '0931393768', NULL, 'Khối Xuân Bắc, Hưng Dũng, Tp Vinh, Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(726, 1, '1432', 'Đào Quang Duy', '1994-12-12', 'Nam', NULL, 85, 'Bác sĩ', NULL, '0983705668', NULL, 'Lăng Thành - Yên Thành - Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(727, 1, '1606', 'Cao Viết Thắng', '1992-05-20', 'Nam', NULL, 85, 'Bác sĩ', NULL, '0367381958', NULL, NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(728, 1, '1024', 'Ngô Văn Thiết', '1989-08-19', 'Nam', 'Phó trưởng khoa Chống độc', 72, 'BSCK I', 'Hồi sức cấp cứu', '0389478670', NULL, 'Nghi Phú - Tp Vinh - Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(729, 1, '1139', 'Lưu Văn Hậu', '1990-11-02', 'Nam', NULL, 56, 'Thạc sĩ', 'Hồi sức cấp cứu và chống độc', '0983849222', NULL, 'Vinh Tân - Tp Vinh - Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(730, 1, '1403', 'Dương Công Hoàn', '1993-12-10', 'Nam', NULL, 56, 'Bác sĩ', NULL, '0934749229', NULL, 'Hưng Hòa - Tp Vinh - Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(731, 1, '1406', 'Nguyễn Thị Vân Khánh', '1994-01-29', 'Nữ', NULL, 56, 'Bác sĩ', NULL, '0858711098', 'phanlam.nghean@gmail.com', 'Nghi Phú - Tp Vinh - Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(732, 1, '1407', 'Lữ Thủy Dung', '1993-09-02', 'Nữ', NULL, 56, 'Bác sĩ', NULL, '0353351092', 'hohai021092@gmail.com', 'Châu Hạnh - Quỳ Châu - Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(733, 1, '1616', 'Nguyễn Thị Kim Chi', '1995-11-17', 'Nữ', NULL, 56, 'Bác sĩ', NULL, '0988199720', 'thangdakhoa103@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(734, 1, '1618', 'Nguyễn Thanh Tuấn', '1995-10-18', 'Nam', NULL, 56, 'Bác sĩ', NULL, '0382911492', 'phamanhtuan@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(735, 1, '1848', 'Phạm Văn Phương', '1993-07-20', 'Nam', NULL, 56, 'Bác sĩ', NULL, '0386238084', 'oanhheo2303@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(736, 1, '1850', 'Nguyễn Thị Thanh Huyền', '1995-11-08', 'Nữ', NULL, 56, 'Bác sĩ', NULL, '0988896523', 'nguyenhuyenvmu@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(737, 1, '0901', 'Trần Phương', '1986-08-19', 'Nam', 'Phó khoa Hồi sức tích cực', 36, 'Thạc sĩ', 'Hồi sức cấp cứu', '0977461929', 'sanguyenthi2695@gmail.com', 'X6- Nghi phú- Vinh- Nghệ an', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(738, 1, '1847', 'Trần Đình Tuấn', '1996-05-13', 'Nam', NULL, 56, 'Bác sĩ', NULL, '0972186530', 'drbathaina@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(739, 1, '0947', 'Trần Văn Thảnh', '1986-08-08', 'Nam', 'Phó trưởng khoa Hồi sức tích cực', 56, 'BSCK I', 'Hồi sức cấp cứu', '986860975', 'nhungtran26061996@gmail.com', 'Quán Bàu - Tp Vinh - Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(740, 1, '0319', 'Nguyễn Đức Phúc', '1970-02-02', 'Nam', 'Trưởng khoa Hồi sức tích cực', 56, 'Tiến sĩ', 'Hồi sức cấp cứu', '0913001780', 'tuuyen4192@gmail.com', 'P.Quang Trung -TP Vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(741, 1, '1470', 'Lê Thị Huệ', '1994-02-01', 'Nữ', NULL, 56, 'CN ĐD', NULL, '0974829723', 'yenlinh21032014@gmail.com', 'Nghi Phú - Tp Vinh - Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(742, 1, '1663', 'Nguyễn Thị Bích Hiền', '1992-06-07', 'Nữ', NULL, 56, 'Cử nhân điều dưỡng', NULL, '0837202785', NULL, NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(743, 1, '1276', 'Mai Thị Huyền', '1992-04-28', 'Nữ', NULL, NULL, 'CN ĐD', NULL, '0373105860', 'tranhuyhieu318@gmail.com', 'Hưng Lộc- Thành Phố Vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(744, 1, '1674', 'Nguyễn Thị Mỹ Ly', '1996-09-03', 'Nữ', NULL, 56, 'Cử nhân điều dưỡng', NULL, '0354552307', 'buithiyen3010@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(745, 1, '1694', 'Hoàng Thị Lệ', '1994-07-22', 'Nữ', NULL, 56, 'Cử nhân điều dưỡng', NULL, '0978046425', NULL, NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(746, 1, '0365', 'Nguyễn Thùy Dung', '1987-08-29', 'Nữ', NULL, 27, 'Cao đăng điều dưỡng', NULL, '0985503567', 'vongan101088@icloud.com', 'Xóm 15, xã Nghi Phú, Tp Vinh, Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(747, 1, '0432', 'Đặng Thị Hoàng Anh', '1988-09-10', 'Nữ', NULL, 23, 'Cao đăng điều dưỡng', NULL, '0772221119', 'nguyenthithutrang@gmail.com', 'Hưng Dũng, Vinh, Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(748, 1, '1486', 'Trương Thị Duyên', '1993-04-01', 'Nữ', NULL, 45, 'Cao đăng điều dưỡng', NULL, '0975079871', 'tranthithanhphuong121094@gmail.com', 'Hưng Lộc - Vinh -Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(749, 1, '0648', 'Nguyễn Thị Oanh', '1982-01-10', 'Nữ', NULL, 56, 'Cao đăng điều dưỡng', NULL, '0912724060', 'cutrung20815@gmail.com', 'Xóm 12, Nghi Phú, Tp Vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(750, 1, '1228', 'Trần Thị Hường', '1993-05-16', 'Nữ', NULL, 56, 'Cao đăng điều dưỡng', NULL, '0372328115', NULL, 'Hưng Chính - Tp Vinh - Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(751, 1, '1082', 'Nguyễn Văn Tuấn', '1986-02-08', 'Nam', NULL, 56, 'Cao đăng điều dưỡng', NULL, '0902257558', NULL, 'Nghi Ân - Tp Vinh - Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(752, 1, '1152', 'Trần Thị Phú', '1991-07-08', 'Nữ', NULL, 56, 'Cao đăng điều dưỡng', NULL, '0936459778', NULL, 'Nghĩa Thuận - Thị xã Thái Hoà - Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(753, 1, '1153', 'Ngô Thị Lan Anh', '1989-08-27', 'Nữ', NULL, 56, 'Cao đăng điều dưỡng', NULL, '0936056663', NULL, 'Hoà Tiến - Hưng Lộc - Tp Vinh - Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(754, 1, '1154', 'Lê Thị Diện', '1988-11-02', 'Nữ', NULL, 56, 'Cao đăng điều dưỡng', NULL, '0916884885', NULL, 'Nghi Phong - Nghi Lộc - Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(755, 1, '0672', 'Hồ Văn Quý', '1985-10-20', 'Nam', NULL, 56, 'Cao đăng điều dưỡng', NULL, '0984009175', 'loohoangoc106@gmail.com', 'Hưng Dũng, Tp Vinh, Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(756, 1, '0740', 'Hồ Thị Lý', '1988-01-26', 'Nữ', NULL, 56, 'Cao đăng điều dưỡng', NULL, '0399256830', NULL, 'K8, Thị trấn Hưng Nguyên, Hưng Nguyên, Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(757, 1, '1488', 'Đậu Thị Anh', '1995-09-03', 'Nữ', NULL, 56, 'Cao đăng điều dưỡng', NULL, '0382764574', NULL, 'Diễn Lộc - Diễn Châu - Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(758, 1, '1703', 'Lê Thị Chung Thủy', '1995-04-29', 'Nữ', NULL, 56, 'Cao đăng điều dưỡng', NULL, '0353057277', 'huyenthuong0503@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(759, 1, '1780', 'Lê Thị Hoài Phương', '1994-04-06', 'Nữ', NULL, 56, 'Cao đăng điều dưỡng', NULL, '0975209394', NULL, NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(760, 1, '1708', 'Phạm Thị Thu Hà', '1991-01-29', 'Nữ', NULL, 56, 'Cao đăng điều dưỡng', NULL, '0816189896', NULL, NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(761, 1, '0675', 'Nguyễn Đình Quyền', '1987-09-15', 'Nam', 'Điều dưỡng trưởng', 56, 'Cử nhân điều dưỡng', NULL, '0985323086', NULL, 'Xóm 11, Xã Hưng Lộc, Tp Vinh, Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(762, 1, '0677', 'Chu Văn Hậu', '1988-10-25', 'Nam', NULL, 56, 'Cao đăng điều dưỡng', NULL, '0974595238', NULL, 'Hưng Lộc - Tp Vinh - Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(763, 1, '1765', 'Đậu Văn Ngân', '1992-07-10', 'Nam', NULL, 56, 'Cao đăng điều dưỡng', NULL, '0383390977', NULL, NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(764, 1, '1515', 'Hồ Anh Tuấn', '1995-09-20', 'Nam', NULL, 56, 'Cao đăng điều dưỡng', NULL, '0988894773', NULL, 'Nghi Phú - tp vinh- Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(765, 1, '0869', 'Trần Thị Mai Trang', '1986-11-14', 'Nữ', NULL, 38, 'Cao đăng điều dưỡng', NULL, '0368280380', 'khacnghiem.hmu@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(766, 1, '1236', 'Phạm Thị Nga', '1993-01-04', 'Nữ', NULL, 32, 'Cao đăng điều dưỡng', NULL, '0911524020', 'bstamanh@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(767, 1, '1498', 'Thái Thị Diệu', '1995-07-22', 'Nữ', NULL, NULL, 'Cao đăng điều dưỡng', NULL, '0981122395', 'mthuutien@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(768, 1, '1752', 'Nguyễn Thị Hoài Linh', '1998-05-28', 'Nữ', NULL, 30, 'Cao đăng điều dưỡng', NULL, '393238708', '01665296092t@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(769, 1, '0712', 'Trần Thị Hoa', '1980-09-23', 'Nữ', NULL, 56, 'Cao đăng điều dưỡng', NULL, '0975149422', 'vuvantinhbs@gamil.com', 'Khối Yên Sơn, P. Hà Huy Tập, Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(770, 1, '0971', 'Đoàn Thị Trang', '1991-06-09', 'Nữ', NULL, 31, 'Cao đăng điều dưỡng', NULL, '0949751991', 'trananhdhy@gmail.com', 'P.Vinh Tan- Vinh- NA', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(771, 1, '1068', 'Nguyễn Thị Luyến', '1990-12-27', 'Nữ', NULL, 31, 'Cao đăng điều dưỡng', NULL, '0981663430', 'Bslehoa@gmail.com', 'Hưng Xuân - HN  -NA', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(772, 1, '0822', 'Đinh Quốc Tuấn', '1988-03-14', 'Nam', NULL, 13, 'Cao đăng điều dưỡng', NULL, '0982037044', 'hckhai.17nt141@huemed-univ.edu.vn', '45 Tôn Thất Tùng - Khối Xuân Bắc -Hưng Dũng - TP Vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(773, 1, '0799', 'Nguyễn Thị Hạnh', '1983-08-10', 'Nữ', NULL, 18, 'CN ĐD', NULL, '0977360198', 'bachgia123@gmail.com', 'Khối Trung Đông - Hưng Dũng - TP Vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(774, 1, '1489', 'Lê Thị Hoa', '1994-01-14', 'Nữ', NULL, 56, 'Cao đăng điều dưỡng', NULL, '0359195461', 'nguyenvanbao22394@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(775, 1, '0739', 'Lê Thị Hoa', '1988-08-10', 'Nữ', NULL, NULL, 'Cao đăng điều dưỡng', NULL, '0384933678', 'Hoxuanlinhdtdcna@gmail.com', 'Xóm 11, Quang Sơn, Đô Lương, Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(776, 1, '1524', 'Nguyễn Thị Hương Giang', '1997-07-26', 'Nữ', NULL, 32, 'Cao đăng điều dưỡng', NULL, '0943198628', 'Vanderthang@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(777, 1, '1105', 'Đặng Thị Nhung', '1991-07-12', 'Nữ', NULL, 44, 'CN CĐPS', NULL, '0968979191', 'Dr.Hung1207@gmail.com', 'Xuân Hòa- Nam Đàn- Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(778, 1, '1940', 'Nguyễn Hữu Việt Anh', '1989-08-25', 'Nam', NULL, 56, 'Thạc sĩ', 'Hồi sức cấp cứu', NULL, 'hoangdao1193@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(779, 1, '1647', 'Nguyễn Hồng Ngọc', '1993-10-20', 'Nam', NULL, 85, 'Bác sĩ', NULL, '0988715979', 'tuananh.nguyen181983@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(780, 1, '0661', 'Nguyễn Văn Thủy', '1984-08-22', 'Nam', 'Trưởng khoa Chống độc', 72, 'Thạc sĩ', 'Hồi sức cấp cứu', '0975752533', 'tranminh210293@gmail.com', 'Diễn Hồng, Diễn Châu, Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(781, 1, '1325', 'Lê Tiến Viện', '1992-03-27', 'Nam', NULL, 72, 'Bác sĩ', NULL, '0931804301', 'tranthinga06061990@gmail.com', 'Xuân bắc- Hưng Dũng-TP Vinh-Nghệ an', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(782, 1, '1405', 'Trần Tuấn Anh', '1994-02-01', 'Nam', NULL, 72, 'Bác sĩ', NULL, '0965505137', 'nguyenhoancttm@gmail.com', 'Hưng Bình - Tp Vinh - Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(783, 1, '1201', 'Nguyễn Trọng Toàn', '1989-03-10', 'Nam', NULL, 72, 'Bác sĩ', NULL, '0393119401', 'luucongthanh1989@gmail.com', 'Số nhà 2 - ngõ 231 - Hà huy tập - Vinh - Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(784, 1, '1617', 'Nguyễn Văn Điều', '1993-09-07', 'Nam', NULL, 72, 'Bác sĩ', NULL, '0921809394', 'hcmv86@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(785, 1, '1849', 'Nguyễn Thị Vinh', '1996-11-27', 'Nữ', NULL, 72, 'Bác sĩ', NULL, '0962026963', 'Ntdung145@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(786, 1, '0665', 'Trần Thị Lý', '1989-09-06', 'Nữ', 'Điều dưỡng trưởng', 72, 'Thạc sĩ sinh học', NULL, '0976585174', NULL, 'Hưng Phú, Hưng Nguyên, Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(787, 1, '1151', 'Lê Thị Lương', '1989-10-01', 'Nữ', NULL, 72, 'CN ĐD', NULL, '0931381989', 'thuongthuong9121995@gmail.com', 'Nghi Công Nam - Nghi Lộc - Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(788, 1, '1083', 'Nguyễn Thị Thu Hà', '1989-05-01', 'Nữ', NULL, 72, 'CN ĐD', NULL, '0902163608', 'lethithuy20041980@gmail.com', 'Hưng Dũng - Tp Vinh - Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(789, 1, '1453', 'Nguyễn Thị Thúy', '1994-02-20', 'Nữ', NULL, 72, 'CN ĐD', NULL, '0339616194', NULL, NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(790, 1, '1469', 'Đậu Thị Phương Anh', '1994-12-30', 'Nữ', NULL, 72, 'CN ĐD', NULL, '0359712419', 'Huyloicardio@gmail.com', 'Tùng Ảnh - Đức Thọ - Hà Tĩnh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(791, 1, '1678', 'Nguyễn Thị Thúy Vân', '1996-12-03', 'Nữ', NULL, 72, 'Cử nhân điều dưỡng', NULL, '0974178031', NULL, NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(792, 1, '1451', 'Đặng Thị Hoài', '1994-09-25', 'Nữ', NULL, 72, 'CN ĐD', NULL, '03659720335', 'dr.buinguyenduc@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(793, 1, '1682', 'Chu Quang Lương', '1994-05-08', 'Nam', NULL, 72, 'Cử nhân điều dưỡng', NULL, '0355686158', 'phanquancardiologist254@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(794, 1, '1481', 'Nguyễn Thị Lân', '1994-08-20', 'Nữ', NULL, 72, 'CN ĐD', NULL, '0359900128', 'bsductai09@gmail.com', 'Nhân Thành - Yên Thành - Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(795, 1, '1530', 'Nguyễn Thị My', '1995-02-22', 'Nữ', NULL, 72, 'Cao đăng điều dưỡng', NULL, '0363567376', 'quanghung2711188@gmail.com', 'Diễn Hạnh - Diễn Châu - Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(796, 1, '0367', 'Nguyễn Thị Hương Trà', '1987-03-05', 'Nữ', NULL, 27, 'Cao đăng điều dưỡng', NULL, '0982816979', 'bsthanh9422@gmail.com', 'Nghi Thái - Nghi Lộc - Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(797, 1, '0366', 'Trần Thị Ngọc Vân', '1987-02-03', 'Nữ', NULL, 27, 'Cao đăng điều dưỡng', NULL, '0917444634', 'nguyennhumanh1281@gmail.com', 'Nghi Phú - TP Vinh - Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(798, 1, '0785', 'Nguyễn Thị Diệp Hà', '1982-02-06', 'Nữ', NULL, 72, 'Cao đăng điều dưỡng', NULL, '0983883861', 'nguyen.quynhtrang.07@gmail.com', 'Hưng Tây - Hưng Nguyên -Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(799, 1, '1238', 'Trần Tiến Phúc', '1992-01-18', 'Nam', NULL, 72, 'Cao đăng điều dưỡng', NULL, '0988792197', 'hoaitrandhy@gmail.com', 'Hưng Lộc - Tp Vinh - Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(800, 1, '1509', 'Hoàng Hà Diệp', '1995-03-27', 'Nữ', NULL, 72, 'Cao đăng điều dưỡng', NULL, '0337217697', 'drphamdiem95@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(801, 1, '0484', 'Phan Thị Thúy Hòa', '1986-09-21', 'Nữ', NULL, 72, 'Cao đăng điều dưỡng', NULL, '0973051109', 'drmanhbinh@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(802, 1, '1767', 'Cao Thị Sương', '1994-08-20', 'Nữ', NULL, 72, 'Cao đăng điều dưỡng', NULL, '0344268943', 'leanbichha184@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(803, 1, '0571', 'Bùi Thị Thu Hà', '1988-09-14', 'Nữ', NULL, 61, 'CN CĐPS', NULL, '0399403586', 'quangnam2610@gmail.com', 'Nghi Thái - Nghi Lộc - Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(804, 1, '0658', 'Lương Mạnh Hùng', '1980-11-06', 'Nam', 'Trưởng khoa Hồi sức tích cực ngoại khoa', 57, 'Thạc sĩ', 'Hồi sức cấp cứu', '0977969909', 'dangminhthu260890@gmail.com', 'Xóm 17, Nghi Phú, Tp Vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(805, 1, '0324', 'Nguyễn Đình Hiệp', '1982-05-10', 'Nam', 'Phó khoa Hồi sức tích cực ngoại khoa', 57, 'BSCK I', 'Hồi sức cấp cứu', '0915670086', 'chudutdut@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(806, 1, '0977', 'Nguyễn Thanh Phương', '1987-10-04', 'Nữ', NULL, 57, 'BSCK I', 'Nội khoa', '0987065965', 'Baokhanh187@gmail.com', 'Trung Lương - Hồng Lĩnh - Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(807, 1, '1203', 'Trần Thị Thu Hoài', '1991-01-21', 'Nữ', NULL, 57, 'Thạc sĩ', 'Hồi sức cấp cứu và chống độc', '0356258898', 'ms.dieuhang95@gmail.com', 'Hưng Lộc- Tpvinh- Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(808, 1, '1619', 'Hoàng Ngọc Huy', '1995-07-28', 'Nam', NULL, 57, 'Bác sĩ', NULL, '0915423992', 'phuonglee979916@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(809, 1, '1851', 'Trần Văn Công', '1995-02-25', 'Nam', NULL, 57, 'Bác sĩ', NULL, '0363522961', 'thehoa134@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(810, 1, '1852', 'Trương Đình Tài', '1995-07-05', 'Nam', NULL, 57, 'Bác sĩ', NULL, '0968002137', 'nguyenungtm2@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(811, 1, '1620', 'Trần Ngọc Nhật', '1995-05-10', 'Nam', NULL, 57, 'Bác sĩ', NULL, '0986453166', 'nguyenthaotc1992@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(812, 1, '1098', 'Phan Anh Đặng', '1990-02-06', 'Nam', NULL, 57, 'BSCK I', 'Hồi sức cấp cứu', '979519754', NULL, 'Hưng Bình -TP vinh -Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(813, 1, '1691', 'Lê Thị Thái Hà', '1995-10-25', 'Nữ', NULL, 25, 'Cử nhân điều dưỡng', NULL, '03991271250812402878', 'tranminhdoan9630@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(814, 1, '1667', 'Nguyễn Thị Mai', '1993-06-04', 'Nữ', NULL, 45, 'Cử nhân điều dưỡng', NULL, '0984099824', 'thanhsonjk98@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(815, 1, '0664', 'Hoàng Hữu Thọ', '1982-12-03', 'Nam', NULL, 57, 'Thạc sĩ sinh học', NULL, '0984199908', 'Bsngacxk@gmail.com', 'Xóm 17, Nghi Phú, Tp Vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(816, 1, '1702', 'Nguyễn Thị Lam', '1993-02-01', 'Nữ', NULL, 57, 'Cử nhân điều dưỡng', NULL, '0329733274', 'drthaichuong84@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(817, 1, '1458', 'Nguyễn Thị Thùy Linh', '1994-03-20', 'Nữ', NULL, 48, 'CN ĐD', NULL, '0966823667', 'Bslethanh.na@gmail.com', 'Bến Thủy-TP Vinh-Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(818, 1, '0192', 'Phạm Thị Thu Phương', '1985-10-31', 'Nữ', NULL, 57, 'Cao đăng điều dưỡng', NULL, '0942914456', 'Hoaitham.hmu@gmail.com', 'Xuân Đông, Hưng Dũng, Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(819, 1, '1761', 'Nguyễn Thị Duyên', '1995-02-22', 'Nữ', NULL, 52, 'Cao đăng điều dưỡng', NULL, '0342056255', 'Trinhlekhanhlinh@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(820, 1, '0190', 'Nguyễn Thi Thu Hà', '1983-09-21', 'Nữ', NULL, 52, 'Cao đăng điều dưỡng', NULL, '0975134656', 'nguyenthithuy21071994@gmail.com', 'Khối 9, Hà Huy Tập, Tp Vinh, Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(821, 1, '0346', 'Ngô Thế Lực', '1983-02-03', 'Nam', NULL, 53, 'Cao đăng điều dưỡng', NULL, '0945297678', 'phuongthanh8696@gmail.com', 'Nghi Phú - TP Vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(822, 1, '1148', 'Nguyễn Thị Hồng Phúc', '1989-10-06', 'Nữ', NULL, 57, 'Cao đăng điều dưỡng', NULL, '0975421003', 'dr.thuydung0605@gmail.com', 'X0ms 23 - Nghi Phú - TP.Vinh - Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(823, 1, '1538', 'Trần Thái Anh Hoàng', '1992-10-20', 'Nam', NULL, 25, 'Cao đăng điều dưỡng', NULL, '0854323456', 'trangphan.tp94@gmail.com', 'Hưng Lợi - Hưng Nguyên- Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(824, 1, '1160', 'Võ Thị Hiền', '1990-06-20', 'Nữ', NULL, 14, 'Cao đăng điều dưỡng', NULL, '0973568770', 'khanhan2208@gmail.com', 'Xóm 7- Xã Nghi Phú- tp Vinh - Nghệ an', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(825, 1, '1756', 'Phạm Thị Thoa', '1991-09-10', 'Nữ', NULL, 57, 'Cao đăng điều dưỡng', NULL, '0344337029', NULL, NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(826, 1, '1540', 'Nguyễn Thị Hải Yến', '1997-02-21', 'Nữ', NULL, NULL, 'Cao đăng điều dưỡng', NULL, '0346296693', NULL, 'Lê Lợi -Vinh - Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(827, 1, '1208', 'Phạm Thị Huyền Trang', '1992-08-19', 'Nữ', NULL, 57, 'Cao đăng điều dưỡng', NULL, '0352198894', NULL, 'Bến Thuỷ - Tp Vinh - Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(828, 1, '0923', 'Phạm Văn Đồng', '1987-11-12', 'Nam', 'Điều dưỡng trưởng khoa và Điều dưỡng trưởng khối ngoại', 57, 'Cử nhân điều dưỡng', NULL, '0917552666', 'nguyenlich.dbs@gmail.com', 'số nhà 51b - đường nguyễn Thái Học - Thành Phố vinh-Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(829, 1, '0812', 'Vũ Thành Hưng', '1981-08-14', 'Nam', NULL, 57, 'Cao đăng điều dưỡng', NULL, '0915567568', 'caoxuan0902@gmail.com', 'Mậu Lâm - Hưng Lộc - TP Vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(830, 1, '1187', 'Nguyễn Thị Thùy Dung', '1991-08-05', 'Nữ', NULL, 57, 'Cao đăng điều dưỡng', NULL, '0349746991', 'hoanglienson281086@gmail.com', 'Nghi Thái - Nghi Lộc -Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(831, 1, '1161', 'Lê Văn Út', '1988-01-03', 'Nam', NULL, 57, 'Cao đăng điều dưỡng', NULL, '0984201628', NULL, 'Nghi kim - Tp vinh- Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(832, 1, '1232', 'Hồ Thị Hiền', '1992-10-17', 'Nữ', NULL, 57, 'Cao đăng điều dưỡng', NULL, '0987198895', 'hoangthanhcxk@gmail.com', 'Nghi Phú - tp vinh- Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(833, 1, '0671', 'Phạm Trọng Hùng', '1986-09-26', 'Nam', NULL, 57, 'Cao đăng điều dưỡng', NULL, '0936309227', 'hoalong1262@gmail.com', 'Xóm 37, đường Đặng Như Mai, Hưng Lộc, Tp Vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(834, 1, '1507', 'Lê Thị Yến', '1987-01-06', 'Nữ', NULL, 57, 'Cao đăng điều dưỡng', NULL, '0889530540', 'tranhao120981@gmail.com', 'Lê Mao-TP Vinh-Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(835, 1, '0673', 'Hồ Thị Huyền', '1983-03-27', 'Nữ', NULL, 57, 'Cao đăng điều dưỡng', NULL, '0944009805', 'binhlevan80@gmail.com', 'Ngách 5, ngõ A, Nguyễn Sỹ Sách, Tp Vinh, Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(836, 1, '0678', 'Trần Thị Lê Na', '1988-10-30', 'Nữ', NULL, 57, 'Cao đăng điều dưỡng', NULL, '0904892515', 'trongvi97@gmail.com', 'Phường Bắc Hồng, Thị xã Hồng Lĩnh, Hà Tĩnh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(837, 1, '1501', 'Cao Thị Mai Hương', '1991-11-11', 'Nữ', NULL, 57, 'Cao đăng điều dưỡng', NULL, '0973737545', 'Thinhqd9a@gmail.com', 'Lê Mao-TP Vinh-Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(838, 1, '1706', 'Nguyễn Thị Mỹ', '1995-10-18', 'Nữ', NULL, 57, 'Cao đăng điều dưỡng', NULL, '0965275885', 'bshodungna@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(839, 1, '1712', 'Võ Thị Tú Linh', '1995-08-20', 'Nữ', NULL, 57, 'Cao đăng điều dưỡng', NULL, '0352469069', 'tuantranganhna@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(840, 1, '1738', 'Chu Thị Thùy Linh', '1997-04-18', 'Nữ', NULL, 57, 'Cao đăng điều dưỡng', NULL, '0983506372', NULL, NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(841, 1, '1743', 'Nguyễn Thị Phương', '1996-06-28', 'Nữ', NULL, 57, 'Cao đăng điều dưỡng', NULL, '0962398331', 'Drletrang0408@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(842, 1, '1768', 'Nguyễn Quang Thịnh', '1996-07-17', 'Nam', NULL, 57, 'Cao đăng điều dưỡng', NULL, '0523061652', NULL, NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(843, 1, '1775', 'Phạm Thị Lam', '1995-09-15', 'Nữ', NULL, 57, 'Cao đăng điều dưỡng', NULL, '0964494205', NULL, NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(844, 1, '1536', 'Đặng Thị Thùy Linh', '1995-05-29', 'Nữ', NULL, 57, 'Cao đăng điều dưỡng', NULL, '0333173098', 'vuonggiadung@gmail.com', 'Tiên Điền -Nghi Xuân -Hà Tĩnh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(845, 1, '1176', 'Nguyễn Thị Oanh', '1991-01-28', 'Nữ', NULL, 37, 'Cao đăng điều dưỡng', NULL, '0976585508', 'phuongdang9196@gmail.com', 'Hưng Lộc - TP Vinh - Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(846, 1, '1781', 'Cao Đức Thưởng', '1994-04-02', 'Nam', NULL, 57, 'Cao đăng điều dưỡng', NULL, '0978866936', 'nguyenhuongdlna@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(847, 1, '1279', 'Trương Thị Thu Trang', '1988-03-01', 'Nữ', NULL, 44, 'CN CĐPS', NULL, '0966340228', 'trinhthiquynh1995@gmail.com', 'Nghi Đức - Vinh -  Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(848, 1, '0565', 'Vương Thị Quỳnh', '1983-02-02', 'Nữ', NULL, 61, 'CN CĐPS', NULL, '0985638840', 'tam.hmu.92@gmail.com', 'Xuân Bắc - Hưng Dũng - TP Vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(849, 1, '0871', 'Nguyễn Thị Phương', '1981-07-22', 'Nữ', NULL, 38, 'Cao đăng điều dưỡng', NULL, '0915677770', 'tranthiyen26111994@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(850, 1, '1918', 'Phan Mạnh Cường', '1995-05-14', 'Nam', NULL, 57, 'Bác sĩ', NULL, '0336060003', NULL, NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(851, 1, '0323', 'Trần Bá Biên', '1977-06-04', 'Nam', 'Trưởng khoa Nội A - Lão khoa', NULL, 'BSCK II', 'BSCKI Nội khoa', '0973410137', 'levinh051291@gmail.com', 'Hưng Dũng - TP Vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(852, 1, '1026', 'Nguyễn Thị Hằng', '1988-04-07', 'Nữ', NULL, NULL, 'BSCK I', 'Nội khoa', '0945275319', NULL, 'Nghi Phú - Tp Vinh - Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(853, 1, '0950', 'Nguyễn Thị Phương Thanh', '1988-08-15', 'Nữ', NULL, NULL, 'Thạc sĩ', 'Nội khoa', '0973979158', 'phanhong061088@gmail.com', 'Nghi Phú - Tp Vinh - Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(854, 1, '1422', 'Đào Như Quỳnh', '1993-06-18', 'Nữ', NULL, NULL, 'Bác sĩ NT', 'Nội khoa', '966693689', 'thanhnhatktk@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(855, 1, '1596', 'Nguyễn Thị Hải Yến', '1992-02-17', 'Nữ', 'Phó khoa Nội A - Lão khoa', NULL, 'Bác sĩ nội trú chuyên ngành Tim mạch', 'Nội khoa', '0399138159', NULL, 'Hưng Dong - TP Vinh - Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(856, 1, '1916', 'Nguyễn Thị Diệu Linh', '1992-06-16', 'Nữ', NULL, NULL, 'Bác sĩ', NULL, '0963816692', NULL, NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(857, 1, '1332', 'Nguyễn Thị Thùy Dung', '1992-03-17', 'Nữ', NULL, NULL, 'Bác sĩ', NULL, '0339773228', NULL, 'Hưng Đông - Tp Vinh - Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(858, 1, '1322', 'Nguyễn Chiến Thắng', '1992-03-31', 'Nam', NULL, NULL, 'Bác sĩ', NULL, '097595316', NULL, 'Nam Hồng - Hồng Lĩnh - Hà Tĩnh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(859, 1, '1344', 'Đinh Thị Tuyết Thanh', '1992-05-25', 'Nữ', NULL, NULL, 'Bác sĩ', NULL, '0368944239', NULL, 'Hưng Dũng - Tp Vinh - Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(860, 1, '1632', 'Phan Thành Vinh', '1995-10-23', 'Nam', NULL, NULL, 'Bác sĩ', NULL, '0915209159', NULL, NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(861, 1, '0765', 'Trần Thị Thu Hiền', '1983-10-04', 'Nữ', NULL, NULL, 'Thạc sĩ', 'Tim mạch', '0986189099', NULL, 'Số 35, Đường Ngô Văn Sở, P. Lê Mao, Tp Vinh, Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(862, 1, '1475', 'Nguyễn Thị Giang', '1996-12-09', 'Nữ', NULL, NULL, 'CN ĐD', NULL, '0982549912', NULL, 'Nghi Diên - Nghi Lộc - Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(863, 1, '1455', 'Võ Thị Nữ Hoàng', '1996-02-16', 'Nữ', NULL, NULL, 'CN ĐD', NULL, '0388013002', 'tiep0501@gmail.com', 'Nghi Lâm - Nghi Lộc - Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(864, 1, '1700', 'Võ Thị Thanh Tú', '1997-10-10', 'Nữ', NULL, NULL, 'Cử nhân điều dưỡng', NULL, '0333583416', 'drlequangtoan@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(865, 1, '0714', 'Nguyễn Thị Xuân', '1987-10-25', 'Nữ', NULL, NULL, 'Cao đăng điều dưỡng', NULL, '0985011638', 'longnt@live.com', 'Hưng Dũng - TP Vinh - Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(866, 1, '0715', 'Nguyễn Thị Nga', '1984-08-24', 'Nữ', NULL, NULL, 'ĐDTH', NULL, '0913472884', 'thanhtung04101985@gmail.com', 'Số 16, Đường Hàm Nghi, Tp Vinh, Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(867, 1, '0706', 'Hoàng Thủy Nguyên', '1988-10-15', 'Nữ', NULL, NULL, 'Cao đăng điều dưỡng', NULL, '0977738386', 'daothanhluu92@gmail.com', 'Số 15B, Đường Bùi Huy BÍch, Hưng Lộc, Tp Vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(868, 1, '0355', 'Nguyễn Quỳnh Anh', '1978-03-01', 'Nữ', 'Phó khoa, PTĐH khoa Dinh dưỡng', 17, 'BSCK II', 'Lão khoa', '0936292489', 'quynhanh29278@gmail.com', 'Trung Định, P. Hưng Dũng, Tp Vinh, Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(869, 1, '0708', 'Nguyễn Thị Hằng', '1989-12-16', 'Nữ', NULL, NULL, 'Cao đăng điều dưỡng', NULL, '0988722867', 'nguyenhienan0704@gmail.com', 'Xóm 5, Nghi Ân, Nghi Lộc, Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(870, 1, '1013', 'Nguyễn Thị Thúy Hải', '1993-08-30', 'Nữ', NULL, NULL, 'ĐDTH', NULL, '0355153631', 'nguyentho171094@gmail.com', 'Diễn Phong - Diễn Châu - Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(871, 1, '1740', 'Nguyễn Thị Mai', '1993-05-20', 'Nữ', NULL, NULL, 'Cao đăng điều dưỡng', NULL, '0949945605', 'athai4718@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(872, 1, '1758', 'Nguyễn Thị Huyền Trang', '1996-08-11', 'Nữ', NULL, NULL, 'Cao đăng điều dưỡng', NULL, '0355626608', 'kieuduong269@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(873, 1, '0705', 'Lê Thị Phương', '1986-03-13', 'Nữ', NULL, NULL, 'CN ĐD', NULL, '0945543433', 'truongdinhthong.hmu@gmail.com', 'Khối 5, P Bến Thủy, Tp Vinh, Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(874, 1, '1815', 'Trần Thị Trang', '1996-04-23', 'Nữ', NULL, 85, 'Bác sĩ', NULL, '0366643639', NULL, NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(875, 1, '1816', 'Hoàng Thị Ly', '1996-05-17', 'Nữ', NULL, 85, 'Bác sĩ', NULL, '0943624238', 'nguyenthihongttdq@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(876, 1, '1817', 'Lưu Thị Ngọc Yến', '1996-12-22', 'Nữ', NULL, 85, 'Bác sĩ', NULL, '0963752912', 'giangsusu2503@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(877, 1, '1813', 'Hồ Thị Vân', '1993-08-12', 'Nữ', NULL, 85, 'Bác sĩ', NULL, '0966787494', 'trahoang08041997@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(878, 1, '0326', 'Nguyễn Hữu Tân', '1984-07-27', 'Nam', 'Trưởng khoa Cấp cứu', 85, 'BSCK II', 'Lão khoa', '0912999388', 'thuongnguyenykv@gmail.com', 'Xuân Bắc - Hưng Dũng - NA', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(879, 1, '1093', 'Thái Bình Dương', '1989-11-23', 'Nam', NULL, 85, 'Bác sĩ', NULL, '0977110275', 'baokhanh.0706@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(880, 1, '0991', 'Phạm Hữu Tuấn', '1988-11-23', 'Nam', NULL, 85, 'Thạc sĩ', 'Hồi sức cấp cứu', '0989885944', NULL, 'Nam Thanh - Nam Đàn - Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(881, 1, '1390', 'Đào Thị Hương', '1992-10-08', 'Nữ', NULL, 85, 'Bác sĩ', NULL, '0986342328', 'ngoisaohivong1201@gmail.com', 'Xóm 14, Xã Nghi Đức , TP Vinh, Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(882, 1, '1301', 'Bùi Thị Phương', '1991-05-15', 'Nữ', NULL, 85, 'Bác sĩ', NULL, '0367783637', NULL, 'Xóm 9 - Nghi phú - Nghệ an', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(883, 1, '1604', 'Phạm Văn Quyền', '1995-10-18', 'Nam', NULL, 85, 'Bác sĩ', NULL, '0982286137', 'ylinh.mulennho1993@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(884, 1, '1605', 'Thái Thị Linh', '1995-03-06', 'Nữ', NULL, 85, 'Bác sĩ', NULL, '0352054694', 'thuhuong1985bvna@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(885, 1, '1819', 'Mai Văn Huy', '1996-01-06', 'Nam', NULL, 85, 'Bác sĩ', NULL, '0378708118', 'thocoi1988@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(886, 1, '1821', 'Nguyễn Thị Hoa', '1996-03-24', 'Nữ', NULL, 85, 'Bác sĩ', NULL, '0983078445', 'hoaichung59@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(887, 1, '1820', 'Nguyễn Văn Tuấn', '1993-01-31', 'Nam', NULL, 85, 'Bác sĩ', NULL, '0389538238', NULL, NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(888, 1, '0329', 'Đặng Ngọc Anh', '1987-06-10', 'Nam', 'Phó khoa Cấp cứu', 85, 'BSCK I', 'Hồi sức cấp cứu', '0949100687', 'tranhuongtk82@gmail.com', 'TT Thanh Chương - Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(889, 1, '1828', 'Nguyễn Hữu Hoàng', '1995-03-15', 'Nam', NULL, 43, 'Bác sĩ', NULL, '0378672654', 'ngatran@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(890, 1, '1857', 'Nguyễn Văn Phúc', '1996-01-01', 'Nam', NULL, 26, 'Bác sĩ', NULL, '0359573168', 'dungnguyen1985@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(891, 1, '1861', 'Ngô Thị Xuân', '1996-02-26', 'Nữ', NULL, NULL, 'Bác sĩ', NULL, '0972704821', 'phanlecau1986@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(892, 1, '1894', 'Nguyễn Thảo Linh', '1995-08-16', 'Nữ', NULL, NULL, 'Bác sĩ', NULL, '0966580538', 'nguyennhatthanh882@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(893, 1, '1447', 'Hà Huyền Phương', '1994-10-06', 'Nữ', NULL, 85, 'CN ĐD', NULL, '0965894580', 'dauanhtien461@gmai.com', 'xóm 23-nghi phú-vinh-nghệ an', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(894, 1, '1464', 'Cao Thị Linh', '1994-11-13', 'Nữ', NULL, 85, 'CN ĐD', NULL, '0963100341', 'truong.my747@gmail.com', 'TT. Hưng Nguyên - Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(895, 1, '1668', 'Phan Thị Hương', '1997-10-10', 'Nữ', NULL, 85, 'Cử nhân điều dưỡng', NULL, '0985876320', 'phanthihanh0804@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(896, 1, '1681', 'Nguyễn Thị Huế', '1996-09-26', 'Nữ', NULL, 85, 'Cử nhân điều dưỡng', NULL, '0968335236', 'ninjjah3@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(897, 1, '1461', 'Nguyễn Thị Hà', '1995-02-22', 'Nữ', NULL, 85, 'CN ĐD', NULL, '0966977063', 'trangngat95@gmail.com', 'hưng dũng-vinh-nghệ an', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(898, 1, '0349', 'Phan Thị Hòa', '1985-05-10', 'Nữ', NULL, 85, 'Cao đăng điều dưỡng', NULL, '0914551189', 'hoadl86@gmail.com', 'Xóm Yên Bình - Hưng Đông - TP Vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(899, 1, '1282', 'Phan Văn Hùng', '1992-06-10', 'Nam', NULL, 85, 'Cao đăng điều dưỡng', NULL, '0943705833', 'Minhhue100195@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(900, 1, '0331', 'Bùi Trọng Minh', '1985-05-15', 'Nam', NULL, 85, 'Cao đăng điều dưỡng', NULL, '0982117235', 'Drthaohuong@gmail.com', 'Số 9, Phùng Khắc Khoan, TP Vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(901, 1, '0337', 'Hồ Thị Xuân', '1989-03-29', 'Nữ', NULL, 85, 'Cao đăng điều dưỡng', NULL, '0984456803', 'hiendl76@gmail.com', 'SN 11- BắcTiến - Hưng Dũng- Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(902, 1, '0913', 'Cao Thị Thanh', '1989-01-10', 'Nữ', NULL, 85, 'Cao đăng điều dưỡng', NULL, '0912998502', 'viethai10t@gmail.com', 'Trường Tiến -Hưng Bình', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(903, 1, '0336', 'Hồ Thị Ngọc Mai', '1988-12-20', 'Nữ', NULL, 85, 'Cao đăng điều dưỡng', NULL, '0984053363', 'hanamthu28@gmail.com', 'Khối 17 - P Hà Huy Tập - TP Vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(904, 1, '1217', 'Nguyễn Văn Phong', '1991-06-04', 'Nam', NULL, 85, 'Cao đăng điều dưỡng', NULL, '0392966964', 'thuythuong121212@gmail.com', 'X3 -Nghi Phú-Vinh-NA', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(905, 1, '0911', 'Bùi Thị Qúy', '1987-10-12', 'Nữ', NULL, 85, 'Cao đăng điều dưỡng', NULL, '0392674878', 'tuongvan471974@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(906, 1, '0912', 'Nguyễn Thị Nhàn', '1990-05-25', 'Nữ', NULL, 85, 'Cao đăng điều dưỡng', NULL, '0987660420', 'vothitam7575@gmail.com', 'Số 10 Trịnh Hoài Đức - P. Lê Mao. Tp Vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(907, 1, '0333', 'Nguyễn Hữu Thắng', '1985-08-15', 'Nam', NULL, 85, 'Cao đăng điều dưỡng', NULL, '0989649085', 'dungle64@gmail.com', 'Xóm Tân Hùng- Hưng Lộc - TP Vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(908, 1, '0459', 'Nguyễn Thi Vinh', '1976-10-28', 'Nữ', NULL, 43, 'CN ĐD', NULL, '0984545358', NULL, 'Khối Yên Hoà, Phường Hà Huy Tập, Tp Vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(909, 1, '1531', 'Nguyễn Thị An', '1996-01-22', 'Nữ', NULL, 85, 'Cao đăng điều dưỡng', NULL, '0383089204', 'nlbang93@gmail.com', 'Lê Mao - Vinh - Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(910, 1, '0631', 'Lê Thị Thúy Vinh', '1981-09-02', 'Nữ', NULL, 85, 'Cao đăng điều dưỡng', NULL, '0942329369', 'khactu83@gmail.com', 'Khối 4 - P Hồng Sơn - TP Vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(911, 1, '1514', 'Nguyễn Quỳnh Như', '1994-04-12', 'Nữ', NULL, 85, 'Cao đăng điều dưỡng', NULL, '0383563804', 'lemytrang233@gmail.com', 'Hưng Bình-Vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(912, 1, '0345', 'Hồ Hải Vân', '1987-08-29', 'Nam', NULL, 85, 'Cao đăng điều dưỡng', NULL, '0915125222', 'ngoc93bs@gmail.com', 'Khối Văn Trung - Hưng Dũng - TP Vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(913, 1, '0347', 'Đậu Viết Định', '1987-01-02', 'Nam', NULL, 85, 'Cao đăng điều dưỡng', NULL, '0978184687', 'phongvank31@gmail.com', 'Số 21, đường Tân Hùng - Hưng Lộc - TP Vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(914, 1, '0348', 'Dương Thị Xinh', '1989-02-12', 'Nữ', NULL, 85, 'Cao đăng điều dưỡng', NULL, '0968128958', NULL, 'Hưng Lĩnh - Hưng Nguyên -Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(915, 1, '0344', 'Trần Thị Ngọc Hà', '1980-12-12', 'Nữ', NULL, 85, 'Cao đăng điều dưỡng', NULL, '0972753631', NULL, 'Khối 15 - P. Lê Lợi - TP Vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(916, 1, '0260', 'Phạm Thị Cẩm Quyên', '1989-02-03', 'Nữ', NULL, 85, 'Cao đăng điều dưỡng', NULL, '0942780987', NULL, 'Khối Xuân Bắc, Phường Hưng Dũng, Tp Vinh, Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(917, 1, '1733', 'Nguyễn Thị Khánh Ly', '1995-03-24', 'Nữ', NULL, 85, 'Cao đăng điều dưỡng', NULL, '0986129159', 'maithehuuydh@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(918, 1, '1759', 'Nguyễn Thị Hải Yến', '1995-04-10', 'Nữ', NULL, 85, 'Cao đăng điều dưỡng', NULL, '0356599577', 'hoangdinhkien996@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(919, 1, '1527', 'Nguyễn Quang Huyến', '1993-01-19', 'Nam', NULL, 85, 'Cao đăng điều dưỡng', NULL, '0972745242', 'maihuongykvinh@gmail.com', 'phường hà huy tập-tp vinh-nghệ an', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(920, 1, '1732', 'Đặng Thị Lan', '1996-02-23', 'Nữ', NULL, 85, 'Cao đăng điều dưỡng', NULL, '0987325945', 'caohau95@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(921, 1, '1543', 'Lê Thị Phương', '1997-12-06', 'Nữ', NULL, 85, 'Cao đăng điều dưỡng', NULL, '0979800040', 'ghcva13@gmail.com', 'hà huy tập-vinh-nghệ an', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(922, 1, '0676', 'Nguyễn Văn Phú', '1988-09-14', 'Nam', NULL, 85, 'Cao đăng điều dưỡng', NULL, '0941808789', 'nguyenthituyet111995@gmail.com', 'x10 - nghi phú - vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(923, 1, '1070', 'Hà Thị Tâm', '1991-06-10', 'Nữ', NULL, 85, 'CN CĐPS', NULL, '0967946691', NULL, 'xóm 9 -nghi phong-nghi lộc -nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(924, 1, '1915', 'Nguyễn Đăng Kiên', '1994-05-10', 'Nam', NULL, 85, 'Bác sĩ', NULL, '0904773768', 'thanghovan74@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(925, 1, '0512', 'Nguyễn Thị Lộc', '1976-07-19', 'Nữ', 'Điều dưỡng trưởng', 25, 'Cử nhân điều dưỡng', NULL, '0943273827', 'locpttk@gmail.com', 'Khối 1 - Phường Trường Thi - TP Vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(926, 1, '0180', 'Trần Xuân Hưng', '1982-02-13', 'Nam', 'Trưởng khoa Nội tiêu hóa', 32, 'BSCK II', 'Nội tiêu hóa', '0902244868', NULL, 'Khối 15, P. hà Huy Tập, Tp Vinh, Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(927, 1, '1129', 'Trần Thị Thanh Hiền', '1990-06-22', 'Nữ', NULL, 32, 'Thạc sĩ', 'Nội khoa', '0978027186', 'thuhuongbaobinh@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(928, 1, '1423', 'Nguyễn Thị Anh Tú', '1994-08-16', 'Nữ', NULL, 32, 'Bác sĩ NT', 'Nội khoa', '984073371', NULL, NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(929, 1, '1634', 'Nguyễn Đình Đức', '1995-06-29', 'Nam', NULL, 32, 'Bác sĩ', NULL, '0349407382', NULL, NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(930, 1, '1635', 'Lưu Tuấn Anh', '1993-12-16', 'Nam', NULL, 32, 'Bác sĩ', NULL, '09791325380359870969', NULL, NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(931, 1, '1853', 'Nguyễn Thị Ngọc', '1995-12-03', 'Nữ', NULL, 32, 'Bác sĩ', NULL, '0984501567', NULL, NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(932, 1, '1854', 'Cao Quang Minh', '1996-10-25', 'Nam', NULL, 32, 'Bác sĩ', NULL, '0367182952', 'anhvinhbui89@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(933, 1, '1131', 'Chu Thị Nhung', '1989-11-20', 'Nữ', NULL, 32, 'Bác sĩ', NULL, '0973934415', 'xnbinh@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(934, 1, '0726', 'Tăng Thị Hậu', '1986-07-07', 'Nữ', NULL, 32, 'BSCK I', 'Nội khoa', '0978115370', 'hoangtiephndk@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(935, 1, '0730', 'Nguyễn Thị Thu Trang', '1984-09-19', 'Nữ', NULL, 32, 'Thạc sĩ', 'Nội khoa', '0943533828', NULL, NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(936, 1, '1855', 'Hoàng Thị Oanh', '1993-06-20', 'Nữ', NULL, 32, 'Thạc sĩ, Bác sĩ nội trú Nội khoa', 'Nội khoa', '0857572162', NULL, NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(937, 1, '1670', 'Nguyễn Thị Hằng', '1993-03-17', 'Nữ', NULL, 32, 'Cử nhân điều dưỡng', NULL, '0386901585', 'lenhattan.gmhs@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(938, 1, '1235', 'Bùi Thị Nhụy', '1992-03-24', 'Nữ', NULL, 32, 'CN ĐD', NULL, '01692672840', NULL, NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(939, 1, '0755', 'Hoàng Thị Hiên', '1986-05-20', 'Nữ', NULL, 32, 'Cao đăng điều dưỡng', NULL, '0986455035', NULL, 'Khôối Tân Phượng, Phường vinh Tân, Tp Vinh, Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(940, 1, '0732', 'Trần Thị Hảo', '1985-06-14', 'Nữ', NULL, 32, 'Cao đăng điều dưỡng', NULL, '0946302226', NULL, NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(941, 1, '0735', 'Nguyễn Thị Hồng', '1985-06-07', 'Nữ', NULL, 32, 'Cao đăng điều dưỡng', NULL, '0976444501', NULL, 'Xóm 7, Xuân Hùng, Hưng Lộc, Tp Vinh, Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(942, 1, '0736', 'Trần Thị Trà', '1988-05-17', 'Nữ', NULL, 32, 'Cao đăng điều dưỡng', NULL, '0978466778', NULL, 'Nhà số 1, Ngách 53, Lê Viết Thuật, Xuân Hùng, Hưng Lộc, Tp Vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(943, 1, '0748', 'Lê Thị Thu Thủy', '1979-03-19', 'Nữ', NULL, 32, 'Cao đăng điều dưỡng', NULL, '01275166999', NULL, 'Khôối Trung Đông, P. Hưng Dũng, Tp Vinh, Nghệ an', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(944, 1, '0751', 'Nguyễn Thị Tâm', '1981-10-17', 'Nữ', NULL, 32, 'Cao đăng điều dưỡng', NULL, '0964577667', NULL, 'PK1, P. hà Huy Tập, Tp Vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(945, 1, '0752', 'Đào Thị Thanh Hoa', '1985-04-02', 'Nữ', NULL, 32, 'Cao đăng điều dưỡng', NULL, '0985888914', NULL, 'Xóm 11, Hưng Lộc, Tp Vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(946, 1, '0523', 'Nguyễn Thị Lương', '1978-05-20', 'Nữ', NULL, 25, 'Cao đăng điều dưỡng', NULL, '0973444977', 'tuanluong1977@gmail.com', 'X 10 - Nghi Liên - Tp Vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(947, 1, '1746', 'Đặng Khánh Vân', '1993-02-06', 'Nữ', NULL, 32, 'Cao đăng điều dưỡng', NULL, '0941437999', NULL, NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(948, 1, '1736', 'Trương Thị Uyên', '1997-01-08', 'Nữ', NULL, 32, 'Cao đăng điều dưỡng', NULL, '0987629365', NULL, NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(949, 1, '1739', 'Nguyễn Thị Lộc', '1991-10-17', 'Nữ', NULL, 32, 'Cao đăng điều dưỡng', NULL, '0353486867', NULL, NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(950, 1, '1755', 'Trần Thị Thêm', '1991-12-05', 'Nữ', NULL, 32, 'Cao đăng điều dưỡng', NULL, '0966367591', NULL, NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(951, 1, '0738', 'Phạm Quỳnh Giang', '1988-07-12', 'Nữ', NULL, 56, 'Cao đăng điều dưỡng', NULL, '0979836826', NULL, 'Diễn Kỷ, Diễn Châu, Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(952, 1, '0177', 'Lê Nhật Huy', '1978-09-20', 'Nam', 'Trưởng khoa Nội Dị ứng - hô hấp', 26, 'Tiến sĩ', 'Nội khoa', '0905788988', NULL, 'Khối 13, P. Trường Thi, Tp Vinh, Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(953, 1, '1200', 'Lê Xuân Vựng', '1991-03-01', 'Nam', NULL, 26, 'Thạc sĩ', 'Nội khoa', '0941468555', NULL, NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(954, 1, '1396', 'Phan Thị Hồng Nhung', '1993-12-22', 'Nữ', NULL, 26, 'Bác sĩ', NULL, '0985664449', NULL, NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0);
INSERT INTO `dm_nhan_vien` (`id`, `benh_vien_id`, `ma_nv`, `ho_ten`, `ngay_sinh`, `gioi_tinh`, `chuc_danh`, `khoa_phong_id`, `trinh_do`, `chuyen_khoa`, `dien_thoai`, `email`, `dia_chi`, `trang_thai`, `ngay_tao`, `ngay_cap_nhat`, `nguoi_tao`, `nguoi_cap_nhat`, `da_xoa`) VALUES
(955, 1, '1612', 'Quế Minh Cương', '1992-10-22', 'Nam', NULL, 26, 'Bác sĩ', NULL, '0986798518', NULL, NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(956, 1, '1609', 'Lê Thị Tú Anh', '1995-12-26', 'Nữ', NULL, 26, 'Bác sĩ', NULL, '0358246557', NULL, NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(957, 1, '1859', 'Nguyễn Thị Hiệp', '1996-10-19', 'Nữ', NULL, 26, 'Bác sĩ', NULL, '0333755147', NULL, NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(958, 1, '1611', 'Nguyễn Đình Kỳ', '1994-06-10', 'Nam', NULL, 26, 'Bác sĩ', NULL, '0855856115', 'myduyen3294@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(959, 1, '1856', 'Hoàng Phú Tài', '1995-12-01', 'Nam', NULL, 26, 'Bác sĩ', NULL, '0962575818', NULL, NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(960, 1, '1858', 'Nguyễn Trọng Hiếu', '1996-09-12', 'Nam', NULL, 26, 'Bác sĩ', NULL, '0967038737', NULL, NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(961, 1, '0181', 'Nguyễn Vĩnh Hải', '1981-03-07', 'Nam', 'Phó khoa Dị ứng hô hấp', 26, 'BSCK II', 'Nội - Hô hấp', '0865183716', 'vanlinh2410@gmail.com', 'Khối Liên Cơ, Hưng bình, Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(962, 1, '1168', 'Đinh Thị Mai Thuận', '1989-12-30', 'Nữ', 'Điều dưỡng trưởng', 26, 'CN ĐD', NULL, '0389927460', NULL, NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(963, 1, '1687', 'Phạm Thị Trâm', '1994-10-08', 'Nữ', NULL, 26, 'Cử nhân điều dưỡng', NULL, '0345290508', NULL, NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(964, 1, '1457', 'Phùng Anh Ngọc', '1994-02-01', 'Nữ', NULL, NULL, 'CN ĐD', NULL, '0395670605', NULL, 'Xóm 15 xã Nghi Phú- Thành Phố Vinh- Tình Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(965, 1, '1165', 'Đường Tiến Dũng', '1991-06-10', 'Nam', NULL, 26, 'Cao đăng điều dưỡng', NULL, '0988109912', NULL, NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(966, 1, '1167', 'Đàm Thị Hiền', '1990-12-20', 'Nữ', NULL, 26, 'Cao đăng điều dưỡng', NULL, '0989690283', NULL, NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(967, 1, '0017', 'Trần Thị Điệp', '1989-07-01', 'Nữ', NULL, 26, 'Cao đăng điều dưỡng', NULL, '0989672776', NULL, 'Tôn Thất Tùng, Tp Vinh, Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(968, 1, '1517', 'Phan Nguyễn Thúy Hằng', '1995-12-29', 'Nữ', NULL, 26, 'Cao đăng điều dưỡng', NULL, '0941169935', 'andanhang78@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(969, 1, '1541', 'Lê Thị Thu Huyền', '1994-12-24', 'Nữ', NULL, 26, 'Cao đăng điều dưỡng', NULL, '0962770097', NULL, NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(970, 1, '1753', 'Bùi Thị Lý', '1994-12-02', 'Nữ', NULL, 26, 'Cao đăng điều dưỡng', NULL, '0385840817', NULL, NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(971, 1, '1754', 'Hoàng Thị Năm', '1994-02-02', 'Nữ', NULL, 26, 'Cao đăng điều dưỡng', NULL, '0377825595', NULL, NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(972, 1, '0789', 'Nguyễn Thị Tố Hạnh', NULL, 'Nữ', NULL, 14, 'Cao đăng điều dưỡng', NULL, '0912634466', 'tohanhhongson@gmail.com', 'SN 75 - K6- Phường Hồng Sơn -TP Vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(973, 1, '0762', 'Ngô Đức Kỷ', '1981-03-13', 'Nam', 'Trưởng khoa Nội tiết', NULL, 'Tiến sĩ', 'Nội khoa', '0936758595', 'thanh.promise87@gmail.com', 'Nhà số 7 Ngõ 24 Đường Trần Quốc Toản, Xóm 18 Nghi Phú, Vinh, Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(974, 1, '1091', 'Lê Đình Sáng', '1987-01-17', 'Nam', 'Phụ trách phòng, phó phòng Quản lý chất lượng', NULL, 'Thạc sỹ QLBV', 'Quản lý bệnh viện', '0973910357', 'long1994@gmail.com', 'Nghi Phú- TP Vinh- Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(975, 1, '1351', 'Phạm Thị Thanh Trâm', '1992-05-26', 'Nữ', NULL, NULL, 'Bác sĩ', NULL, '0964239700', 'chungco304@gmail.com', 'Nghi Đức- TP Vinh- Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(976, 1, '0327', 'Nguyễn Thị Hoài Trang', '1986-02-01', 'Nữ', NULL, NULL, 'BSCK II', 'Nội - Nội tiết', '0978153083', NULL, 'K12 - Phường Trường Thi - TP Vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(977, 1, '1001', 'Võ Tuyết Linh', '1985-01-06', 'Nữ', NULL, NULL, 'BSCK I', 'Nội khoa', '0382790888', 'nguyenthiha3444@gmail.com', 'phòng 11-01 tòa B - chung cư golden city 6a- số 8- Lí Tự Trọng kéo dài', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(978, 1, '0725', 'Hồ Thị Hoài Thương', '1984-03-15', 'Nữ', 'Phó khoa Nội tiết', NULL, 'BSCK II', 'Nội - Nội tiết', '0984852366', NULL, 'Đường Lưu Đức An - Khối 14 - Hà Huy Tập - Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(979, 1, '1345', 'Nguyễn Quốc An', '1992-12-29', 'Nam', NULL, NULL, 'Thạc sĩ', 'Nội khoa', '0975767112', NULL, 'phường hưng dũng- tp vinh- nghệ an', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(980, 1, '1633', 'Nguyễn Thị Nhung', '1994-11-20', 'Nữ', NULL, NULL, 'Bác sĩ', NULL, '0977937059', NULL, NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(981, 1, '1862', 'Nguyễn Thị Thảo', '1996-10-10', 'Nữ', NULL, NULL, 'Bác sĩ', NULL, '0364507496', 'quynhtrang02061983@gmail.com', NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(982, 1, '1860', 'Đặng Thị Mai', '1994-09-13', 'Nữ', NULL, NULL, 'Bác sĩ', NULL, '0889840644', NULL, NULL, 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(983, 1, '1237', 'Đặng Thị Hằng', '1992-08-09', 'Nữ', NULL, NULL, 'CN ĐD', NULL, '0932391393', NULL, 'Nghi Phú- TP Vinh- Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(984, 1, '0741', 'Cao Thị Hằng', '1989-11-03', 'Nữ', NULL, NULL, 'Cao đăng điều dưỡng', NULL, '0977969775', 'tuananhbv87@gmail.com', 'Tân Hoà, P. Nghi Hoà, Tx Cửa Lò, Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(985, 1, '1171', 'Phan Thị Thìn', '1989-10-10', 'Nữ', NULL, NULL, 'Cao đăng điều dưỡng', NULL, '0963182333', 'lemaianhvn@gmail.com', 'hưng đông-tp vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(986, 1, '0750', 'Dương Thị Hoa Mai', '1982-06-17', 'Nữ', NULL, NULL, 'Cao đăng điều dưỡng', NULL, '0982822495', 'Tramxoan90@gmail.com', 'Xóm 7,Hưng Lộc - Vinh', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(987, 1, '1529', 'Nguyễn Thị Hiền', '1996-07-17', 'Nữ', NULL, NULL, 'Cao đăng điều dưỡng', NULL, '0968651895', 'thaobichle49@gmail.com', 'Hưng Đạo-Hưng Nguyên- Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(988, 1, '1218', 'Nguyễn Thị Mai', '1988-06-10', 'Nữ', NULL, NULL, 'Cao đăng điều dưỡng', NULL, '0989674426', 'minh10031996@gmail.com', 'Khối 13 - Phường Hà Huy Tập - Thành phố Vinh - Tỉnh Nghệ An', 1, '2026-05-22 08:59:28', '2026-05-22 08:59:28', 0, 0, 0),
(989, 1, '1216', 'Nguyễn Thị Kim Dung', '1993-10-18', 'Nữ', NULL, NULL, 'Cao đăng điều dưỡng', NULL, '0989686785', 'buithiluan03@gmail.com', 'Nghi hương - Cửa lò - Nghệ an', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(990, 1, '0851', 'Nguyễn Thị Bích Ngọc', '1986-12-05', 'Nữ', 'Điều dưỡng trưởng', NULL, 'Cử nhân điều dưỡng', NULL, '0904929737', 'thuhang91.bvdkna@gmail.com', 'Đại Lộc, Đông Vĩnh, Vinh, Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(991, 1, '0699', 'Lê Mạnh Hà', '1976-09-17', 'Nam', NULL, 34, 'NV KT', NULL, '0942697676', 'letainguyen.1384@gmail.com', 'Khối 3, Phường Cửa Nam, Tp Vinh, Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(992, 1, '1584', 'Trần Cao Thông', '1997-05-07', 'Nam', NULL, 34, 'CĐ KTV XN', NULL, '0968876372', 'phamthithuhien221089@gmail.com', 'Diễn Phong - Diễn Châu - Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(993, 1, '0720', 'Lê Thanh Sơn', '1974-12-22', 'Nam', NULL, 34, 'BSCK I', 'Nội khoa', '0915232858', 'vongocmai260789@gmail.com', 'AN 20, ngõ 33, Hải Thượng Lãn ông, Tp Vinh', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(994, 1, '0969', 'Nguyễn Thị Thùy Linh', '1988-03-08', 'Nữ', 'Trưởng khoa Nội thận - tiết niệu - lọc máu', 34, 'BSCK II', 'Nội - Thận tiết niệu', '0396935383', 'nguyentailam04032019@gmail.com', 'xóm 9 Nghi Phú-tp Vinh-Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(995, 1, '1064', 'Nguyễn Thị Đoan Trang', '1989-10-23', 'Nữ', NULL, 34, 'Bác sĩ', NULL, '0904211898', 'ngothuyvan810@gmail.com', 'P. Hưng Bình, Tp Vinh, Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(996, 1, '1342', 'Trần Văn Dũng', '1991-07-02', 'Nam', NULL, 34, 'BSCK I', 'Nội khoa', '0989790847', 'dangthu5356@gmail.com', 'Chung cư golden city 6A,Đường Lý Tự Trọng,tp Vinh', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(997, 1, '1421', 'Ngô Văn Hải', '1994-10-21', 'Nam', NULL, 34, 'Bác sĩ', NULL, '0393874234', 'thamnguyen.110788@gmail.com', 'Nghi Phú, TP Vinh, Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(998, 1, '1863', 'Bùi Thị Dung', '1996-11-29', 'Nữ', NULL, 34, 'Bác sĩ', NULL, '0348594996', 'vvansysinh@icloud.com', NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(999, 1, '1864', 'Dương Thị Hồng', '1996-05-01', 'Nữ', NULL, 34, 'Bác sĩ', NULL, '0969606299', 'linhchi089@gmail.com', NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1000, 1, '1865', 'Nguyễn Hữu Tình', '1996-07-01', 'Nam', NULL, 34, 'Bác sĩ', NULL, '0364289562', 'hoavo8196@gmail.com', NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1001, 1, '0453', 'Nguyễn Cảnh Mạnh', '1985-03-01', 'Nam', 'Phó khoa', 34, 'Thạc sĩ', 'Ngoại khoa', '0962587929', 'ctabvna@gmail.com', 'Số nhà 06 - Ngõ 120 - Đường Lý Tự Trọng - Phường Hà Huy Tập - Vinh - Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1002, 1, '1265', 'Thái Thị Tú Oanh', '1992-10-19', 'Nữ', NULL, 34, 'CN ĐD', NULL, '0372929262', 'quynhanh29278@gmail.com', 'Tường Sơn- Anh Sơn- Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1003, 1, '0695', 'Hoàng Thị Thành', '1981-04-19', 'Nữ', 'Điều dưỡng trưởng', 34, 'Thạc sĩ sinh học', NULL, '0949438585', 'phamthuha.dhyhp@gmail.com', 'Khối 7, phường Quang Trung, Tp Vinh, Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1004, 1, '1254', 'Lê Thị Phương', '1991-02-25', 'Nữ', NULL, 34, 'CN ĐD', NULL, '0356260355', 'nguyenthom8989@gmail.com', 'x3- nghi kim- thành phố vinh', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1005, 1, '1460', 'Cao Thị Huyền', '1992-10-23', 'Nữ', NULL, 34, 'CN ĐD', NULL, '0866942806', 'haiht0310@gmail.com', 'Diễn Phú- Diễn Châu- Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1006, 1, '1686', 'Nguyễn Công Thuận', '1996-02-11', 'Nam', NULL, 34, 'Cử nhân điều dưỡng', NULL, '0346948676', 'nghiemhuong0710@gmail.com', NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1007, 1, '1262', 'Trần Thị Tâm', '1992-08-21', 'Nữ', NULL, 34, 'Cao đăng điều dưỡng', NULL, '0342728692', 'hmudiepanh88@gmail.com', 'chung cư Bảo Sơn-khối vinh tiến-p.hưng bình-tp Vinh-Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1008, 1, '1173', 'Nguyễn Thị Thương', '1990-09-25', 'Nữ', NULL, 34, 'Cao đăng điều dưỡng', NULL, '0965251357', 'nguyenthivan10109388@gmail.com', 'NGũ Lộc -Hưng Lộc -TP vinh- nghệ an', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1009, 1, '1174', 'Lê Thị Thủy', '1990-12-19', 'Nữ', NULL, 34, 'Cao đăng điều dưỡng', NULL, '0365566403', 'hoangquynhhmu@gmail.com', 'xóm Phú Điển- Vĩnh Thành- yên Thành- Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1010, 1, '1175', 'Hoàng Thị Thiện', '1990-10-24', 'Nữ', NULL, 34, 'Cao đăng điều dưỡng', NULL, '0378443471', 'kienicu2016@gmail.com', 'Xóm Bình Thái-Nghĩa Bình -Nghĩa Đàn-Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1011, 1, '1264', 'Nguyễn Thị Thảo', '1993-11-29', 'Nữ', NULL, 34, 'Cao đăng điều dưỡng', NULL, '0355769803', NULL, 'Hưng Tiến -Hưng Nguyên -Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1012, 1, '0697', 'Lê Thị Liên', '1987-08-05', 'Nữ', NULL, 34, 'Cao đăng điều dưỡng', NULL, '0856056777', 'vuvantinhytb@gmail.com', 'Xóm 9B, xã Hưng Long, Huyện Hưng Nguyên, Tỉnh Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1013, 1, '0679', 'Đậu Thị Thanh Tâm', '1986-06-10', 'Nữ', NULL, 34, 'Cao đăng điều dưỡng', NULL, '0949794358', 'minhhieuhmu0211@gmail.com', 'Khối Trung Đông, Hưng Dững, Tp Vinh', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1014, 1, '0694', 'Nguyễn Thị Lĩnh', '1981-06-22', 'Nữ', NULL, 34, 'Cao đăng điều dưỡng', NULL, '0963293595', 'BShoangthai@gmail.com', 'Xóm 11, Xã Hưng Lộc, Tp Vinh, Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1015, 1, '1513', 'Cao Thị Hải Yến', '1992-12-15', 'Nữ', NULL, 34, 'Cao đăng điều dưỡng', NULL, '0348249669', 'nganle2601@gmail.com', 'xóm kim chi- Nghi ân -TP Vinh- Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1016, 1, '1281', 'Nguyễn Thị Ngọc', '1992-03-10', 'Nữ', NULL, 34, 'Cao đăng điều dưỡng', NULL, '0348821108', NULL, 'Xóm 22-Nghi phú -TP vinh-Nghệ an', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1017, 1, '1261', 'Trần Thị Thu Trang', '1993-11-28', 'Nữ', NULL, 34, 'Cao đăng điều dưỡng', NULL, '0942609255', NULL, 'khối 9-phường Quán Bàu-tp vinh-Nghệ AN', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1018, 1, '0952', 'Phan Thị Lam', '1988-10-07', 'Nữ', 'Phó trưởng khoa Huyết học lâm sàng, phụ trách điều hành khoa', NULL, 'BSCK I', 'Huyết học truyền máu', '0973469229', 'drloan.dkna@gmail.com', 'ktx 15 tầng Đại học y Hà Nội', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1019, 1, '1338', 'Hồ Thị Lệ Hải', '1992-10-02', 'Nữ', NULL, NULL, 'Bác sĩ', NULL, '097542382', 'tamynguyenphan@gmail.com', 'TT Cầu Giát - Quỳnh Lưu - Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1020, 1, '1318', 'Cù Nam Thắng', '1980-03-10', 'Nam', 'Phó trưởng khoa Huyết học lâm sàng', NULL, 'Thạc sĩ', 'Huyết học truyền máu', '0972371111', 'hoangthihiep9@gmail.com', 'số nhà 17 Trung Yên - Yên Hòa- Hà Huy Tập - Tp Vinh- Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1021, 1, '1622', 'Phạm Thị Hường', '1995-09-10', 'Nữ', NULL, NULL, 'Bác sĩ NT', 'Huyết học - truyền máu', '0358254089', 'dangbatoa@gmail.com', NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1022, 1, '1866', 'Hoàng Thị Kiều Oanh', '1995-03-23', 'Nữ', NULL, NULL, 'Bác sĩ', NULL, '0987895855', 'nttrangdhy1016@gmail.com', NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1023, 1, '1867', 'Nguyễn Thị Huyền', '1996-10-20', 'Nữ', NULL, NULL, 'Bác sĩ', NULL, '0967481285', NULL, NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1024, 1, '1868', 'Nguyễn Thị Ngọc', '1995-08-03', 'Nữ', NULL, NULL, 'Bác sĩ', NULL, '0988354585', 'ngocdiepmiss1403@gmail.com', NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1025, 1, '1136', 'Nguyễn Bá Thái', '1991-10-11', 'Nam', NULL, NULL, 'Thạc sĩ', 'Ung thư', '0389949238', NULL, NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1026, 1, '1676', 'Trần Thị Nhung', '1995-05-11', 'Nữ', NULL, NULL, 'Cử nhân điều dưỡng', NULL, '0358814212', 'huyenkhoalethi1991@gmail.com', NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1027, 1, '0976', 'Thái Thị Tú Uyên', '1992-01-04', 'Nữ', NULL, 56, 'Cao đăng điều dưỡng', NULL, '0987555092', NULL, 'Xóm Mẫu Đơn- Hưng Lộc- Tp Vinh - Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1028, 1, '1255', 'Phan Thị Yên', '1990-06-02', 'Nữ', NULL, 78, 'Cao đăng điều dưỡng', NULL, '0986965014', NULL, 'Xóm Kiều Thắng Lợi- Xuân Đan - Nghi Xuân- Hà Tĩnh', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1029, 1, '1252', 'Tô Thị Hường', '1991-06-28', 'Nữ', NULL, NULL, 'Cao đăng điều dưỡng', NULL, '0355241506', 'nguyenphuongthao270895@gmail.com', 'chung cư Sao Nghệ 28 b Nguyễn sỹ Sách- Hưng Bình- Tp Vinh- Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1030, 1, '0464', 'Trần Văn Huy', '1986-06-16', 'Nam', 'Điều dưỡng trưởng', NULL, 'Cử nhân điều dưỡng', NULL, '0936335999', NULL, 'Nghi Thạch, Nghi Lộc, Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1031, 1, '1737', 'Bùi Thị Yến', '1987-10-30', 'Nữ', NULL, 57, 'Cao đăng điều dưỡng', NULL, '0368337786', NULL, NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1032, 1, '1502', 'Lê Thị Huyền', '1991-05-21', 'Nữ', NULL, NULL, 'CN ĐD', NULL, '0962263119', NULL, 'Hưng Đông- Tp Vinh- Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1033, 1, '1256', 'Võ Thị Ngân', '1988-10-10', 'Nữ', NULL, NULL, 'Cao đăng điều dưỡng', NULL, '0934523884', 'tranhuyenlinh2401@gmail.com', 'Xóm 2 Kim Mỹ- Xã Nghi Ân- Thành Phố Vinh- Tình Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1034, 1, '1257', 'Nguyễn Thị Thu Trang', '1992-01-12', 'Nữ', NULL, NULL, 'Cao đăng điều dưỡng', NULL, '0373720706', NULL, 'Khối Vinh Quang- Hưng Bình- Tp Vinh- Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1035, 1, '1716', 'Trần Thị Phương', '1994-10-12', 'Nữ', NULL, NULL, 'Cao đăng điều dưỡng', NULL, '0359631440', 'phamhaiyen23091993@gmail.com', NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1036, 1, '0974', 'Nguyễn Thị Hoa', '1989-12-29', 'Nữ', NULL, NULL, 'Cao đăng điều dưỡng', NULL, '0979855513', NULL, 'chung cư Golden city 6A- Lý tự trọng- Nghi Phú- Tp Vinh- Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1037, 1, '0447', 'Nguyễn Hữu Nam', '1979-02-20', 'Nam', 'Trưởng khoa Phẫu thuật tim mạch lồng ngực', NULL, 'BSCK I', 'Ngoại khoa', '0914548616', NULL, 'Xóm 13 - Phúc Lộc - Nghi Lộc - TP Vinh', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1038, 1, '0450', 'Phạm Văn Chung', '1983-09-02', 'Nam', 'Phó giám đốc trung tâm tim mạch', NULL, 'BSCK II', 'Ngoại - Tim mạch', '0912102152', NULL, NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1039, 1, '0996', 'Nguyễn Văn Việt', '1986-05-14', 'Nam', NULL, NULL, 'Bác sĩ', NULL, '0975617939', NULL, 'Quỳnh Lập - Quỳnh Lưu - Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1040, 1, '1306', 'Nguyễn Quốc Hưng', '1991-08-10', 'Nam', NULL, NULL, 'Bác sĩ', NULL, '0968720828', NULL, NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1041, 1, '1629', 'Nguyễn Quang Đức', '1995-07-23', 'Nam', NULL, NULL, 'Bác sĩ', NULL, '0946106366', 'dr.lehue0610@gmail.com', NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1042, 1, '1832', 'Lô Thị Hoa', '1995-06-10', 'Nữ', NULL, NULL, 'Bác sĩ', NULL, '0987953628', NULL, NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1043, 1, '0454', 'Hồ Thái Phúc', '1983-06-09', 'Nam', NULL, 27, 'BSCK I', 'Ngoại Lồng ngực', '0983990683', NULL, NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1044, 1, '1416', 'Hồ Trọng Dũng', '1990-11-11', 'Nam', NULL, NULL, 'Bác sĩ', NULL, '0973473252', NULL, NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1045, 1, '1478', 'Nguyễn Thị Huyền Thương', '1995-03-05', 'Nữ', NULL, NULL, 'CN ĐD', NULL, '0974138131', NULL, 'Nghi Phú - Tp Vinh - Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1046, 1, '0155', 'Phạm Thị Mỹ Khanh', NULL, 'Nữ', NULL, NULL, 'LĐ PT', NULL, '0949875725', 'kieuoanhbvdkna@gmail.com', NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1047, 1, '1520', 'Hồ Mỹ Niệm', '1993-11-21', 'Nữ', NULL, 57, 'CN ĐD', NULL, '01639747297', 'tr.khoaphan@gmail.com', NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1048, 1, '1728', 'Đinh Thị Hạnh', '1998-04-04', 'Nữ', NULL, NULL, 'Cao đăng điều dưỡng', NULL, '09694075940848378668', NULL, NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1049, 1, '1778', 'Cung Thị Nhung', '1997-02-20', 'Nữ', NULL, 56, 'Cao đăng điều dưỡng', NULL, '0975443705', 'dr.hungnguyenduc@gmail.com', NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1050, 1, '0469', 'Hoàng Thị Tuyết', '1988-09-05', 'Nữ', NULL, NULL, 'Cao đăng điều dưỡng', NULL, '0988971903', 'nguyenhailinh37@gmail.com', 'Hưng Lộc, Tp Vinh, Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1051, 1, '0790', 'Lại Trung Kiên', '1987-06-18', 'Nam', NULL, NULL, 'Cao đăng điều dưỡng', NULL, '0918544645', 'tuoanhtran.ykv@gmail.com', 'SN 15, ngõ 30, đường Tuệ Tĩnh,Hưng Dũng - TP Vinh', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1052, 1, '1303', 'Nguyễn Khắc Nghiêm', '1991-12-17', 'Nam', NULL, 27, 'Thạc sĩ', 'Nội Tim mạch', '0943479728', 'tranhuyentrang1819@gmail.com', 'Hưng Dũng - TP Vinh - Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1053, 1, '0764', 'Phan Việt Tâm Anh', '1976-11-28', 'Nam', 'Giám đốc Trung tâm Tim mạch, Trưởng khoa Nội Tim mạch 1', 30, 'BSCK II', 'Nội - Tim mạch', '0982030226', 'nguyentiendat7101983@gmail.com', 'Xóm Mẫu Đơn -Hưng Lộc -TP Vinh', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1054, 1, '1337', 'Hồ Hữu Tiến', '1992-10-01', 'Nam', NULL, 30, 'Bác sĩ', NULL, '815015115', 'Simbavmmu@gmail.com', 'X 14, Xã Nghi Phú , TP Vinh, Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1055, 1, '1637', 'Nguyễn Văn Thái', '1993-04-25', 'Nam', NULL, 30, 'Bác sĩ', NULL, '01665296092', 'drbuihoan84@gmail.com', NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1056, 1, '0767', 'Vũ Văn Tình', '1982-08-19', 'Nam', 'Phó khoa Nội Tim mạch 1', 30, 'Thạc sĩ', 'Truyền nhiễm', '0949428777', 'drhaihai@gmail.com', 'Hưng Dũng - Vinh', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1057, 1, '1638', 'Lê Trần Anh', '1994-04-27', 'Nam', NULL, 30, 'Bác sĩ', NULL, '0346570209', NULL, NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1058, 1, '0903', 'Lê Thị Thanh Hòa', '1986-06-15', 'Nữ', NULL, 30, 'Thạc sĩ', 'Tim mạch', '0986199022', 'Huyen261196@gmail.com', NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1059, 1, '1869', 'Hoàng Công Khải', '1993-12-19', 'Nam', NULL, 30, 'Bác sỹ nội trú, nội khoa', 'Nội khoa', '0349510199', 'tramlien@gmail.com', NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1060, 1, '1870', 'Phạm Tất Bách', '1996-01-08', 'Nam', NULL, 30, 'Bác sĩ', NULL, '0854908100', 'nguyenthuydkna@gmail.com', NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1061, 1, '1871', 'Nguyễn Văn Bảo', '1994-03-22', 'Nam', NULL, 30, 'Bác sĩ', NULL, '0979330224', 'luthikimchi92@gmail.com', NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1062, 1, '1639', 'Hồ Xuân Linh', '1993-03-30', 'Nam', NULL, 30, 'Bác sĩ', NULL, '0963654007', NULL, NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1063, 1, '1433', 'Văn Nam Thắng', '1994-03-18', 'Nam', NULL, 30, 'Bác sĩ', NULL, '0977807408', 'Nguyenthivan170896@gmail.com', 'Bến Thủy - Vinh', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1064, 1, '0949', 'Nguyễn Thanh Hưng', '1988-12-07', 'Nam', NULL, 30, 'Thạc sĩ', 'Nội tim mạch', '0979255257', NULL, 'P.Le Lợi -Vinh - Nghe An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1065, 1, '1679', 'Nguyễn Văn Đạo', '1994-09-11', 'Nam', NULL, 30, 'Cử nhân điều dưỡng', NULL, '0982480079', NULL, NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1066, 1, '1454', 'Trần Thị Hiền', '1994-02-03', 'Nữ', NULL, 30, 'CN ĐD', NULL, '359753315', 'tranthihoa201710@gmail.com', 'Mã Thành- Yên Thành', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1067, 1, '1495', 'Cao Thị Trang', '1995-12-10', 'Nữ', NULL, 30, 'Cao đăng điều dưỡng', NULL, '0981670489', 'hanhbep97@gmail.com', 'Diễn Phúc Diễn Châu', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1068, 1, '0786', 'Nguyễn Thị Hợi', '1983-03-01', 'Nữ', NULL, 30, 'Cao đăng điều dưỡng', NULL, '0945640962', 'nguyenanhthoa1912@gmail.com', 'Phường Hưng Dũng -TP Vinh', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1069, 1, '0773', 'Lê Thị Quý Hòa', '1987-06-05', 'Nữ', NULL, 30, 'Cao đăng điều dưỡng', NULL, '0946325687', NULL, 'Số nhà 48, khối Xuân Tiến, P. Hưng Dũng, Tp Vinh', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1070, 1, '1249', 'Trần Thị Minh', '1993-02-21', 'Nữ', NULL, 30, 'Cao đăng điều dưỡng', NULL, '0976356956', 'hanghasau.90@gmail.com', NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1071, 1, '1283', 'Trần Thị Nga', '1990-06-06', 'Nữ', NULL, 56, 'Cao đăng điều dưỡng', NULL, '0973464935', 'maisimkna@gmail.com', 'Hưng Bình-Vinh', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1072, 1, '1149', 'Lưu Công Thành', '1989-09-14', 'Nam', NULL, 30, 'Cao đăng điều dưỡng', NULL, '0979781601', NULL, 'Nghi Kim - TP Vinh', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1073, 1, '0776', 'Bành Thị Ngọc Mỹ', '1987-12-10', 'Nữ', NULL, 36, 'Cao đăng điều dưỡng', NULL, '0988400807', 'tnntlx@gmail.com', 'Nhà số 6, ngách 7, ngõ 6, đường Tân Hùng, Tp Vinh, Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1074, 1, '0770', 'Nguyễn Trọng Dũng', '1986-05-14', 'Nam', NULL, 30, 'Cao đăng điều dưỡng', NULL, '0973639739', NULL, 'Phúc Thọ - Nghi Lộc -Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1075, 1, '1485', 'Nguyễn Thị Mai', '1994-11-05', 'Nữ', NULL, 30, 'Cao đăng điều dưỡng', NULL, '0985673655', NULL, 'Quỳnh Yên- Quỳnh Lưu', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1076, 1, '1721', 'Trần Thị Thương', '1995-12-09', 'Nữ', NULL, 30, 'Cao đăng điều dưỡng', NULL, '0349959520', NULL, NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1077, 1, '0787', 'Lê Thị Thúy', '1980-04-20', 'Nữ', NULL, 30, 'Cao đăng điều dưỡng', NULL, '0912832828', NULL, 'X 16- Hưng Lộc -TPVinh', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1078, 1, '262ttdv', 'Đặng Thị Hiền Anh', '1975-03-06', 'Nữ', NULL, NULL, 'Cử nhân kinh tế', NULL, NULL, NULL, NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1079, 1, '0763', 'Nguyễn Huy Lợi', '1978-12-26', 'Nam', 'Trưởng khoa Nội Tim mạch 2', 31, 'Thạc sĩ', 'Tim mạch', '0915099223', 'ducnguyen1317@gmail.com', 'P. Vinh Tân-Vinh -Nghẹ an', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1080, 1, '0761', 'Phạm Nữ Vân Nga', '1982-07-03', 'Nữ', 'Phó trưởng khoa Khám bệnh', 31, 'Thạc sĩ', 'Nội tim mạch', '0917848008', NULL, 'Trung Đô Vinh', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1081, 1, '1199', 'Bùi Nguyên Đức', '1992-04-17', 'Nam', NULL, NULL, 'Thạc sĩ', 'Nội khoa', '0975415424', 'tuanpmmd@gmail.com', 'Trung Đô - Vinh', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1082, 1, '1434', 'Phan Hồng Quân', '1994-04-25', 'Nam', NULL, 31, 'Bác sĩ', NULL, '0385250494', NULL, 'Hưng Dũng - Tp Vinh - Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1083, 1, '0766', 'Lê Đức Tài', '1986-03-23', 'Nam', 'Phó khoa Nội Tim mạch 2', 31, 'Thạc sĩ', 'Tim mạch', '0989855041', NULL, 'K.Minh Phúc - Hưng Phúc - TP Vinh - NA', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1084, 1, '1809', 'Phạm Đức Quang', '1996-08-18', 'Nam', NULL, 31, 'Bác sĩ', NULL, '0936093568', 'tranvanloc270296@gmail.com', 'Số 4 - Ngõ 112-  Đường Hải Thượng Lãn Ông - Khối Yên Sơn - Phường Hà Huy Tập - Vinh - Nghệ AN', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1085, 1, '1435', 'Nguyễn Tiến Thành', '1994-02-02', 'Nam', NULL, 31, 'Bác sĩ', NULL, '989069294', NULL, 'Nam Hòng - Hòng Lĩnh -Hà Tĩnh', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1086, 1, '1640', 'Nguyễn Như Mạnh', '1994-08-12', 'Nam', NULL, 31, 'Bác sĩ', NULL, '0397407178', NULL, NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1087, 1, '1641', 'Nguyễn Thị Quỳnh Trang', '1995-07-16', 'Nữ', NULL, 31, 'Bác sĩ', NULL, '0985715695', 'nguyenvanthuy2510@gmail.com', NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1088, 1, '1884', 'Trần Thị Hoài', '1995-07-18', 'Nữ', NULL, 31, 'Bác sĩ', NULL, '0358236809', NULL, NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1089, 1, '1885', 'Phạm Thị Diễm', '1995-08-11', 'Nữ', NULL, 31, 'Bác sĩ', NULL, '0942820421', NULL, NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1090, 1, '0257', 'Mạnh Trọng Bình', '1986-07-16', 'Nam', NULL, 31, 'Bác sỹ nội trú', 'Tim mạch', '0935509596', NULL, 'P.Hưng phúc - Tp vinh- NA', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1091, 1, '0760', 'Lê Thị Hà', '1979-04-18', 'Nữ', NULL, 31, 'BSCK I', 'Nội khoa', '0946706828', NULL, 'K2 - Phường Bến Thuỷ -TP Vinh', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1092, 1, '1280', 'Nguyễn Quang Nam', '1993-10-26', 'Nam', NULL, 31, 'CN ĐD', NULL, '982551597', NULL, 'Nghi Phú - Vinh -Nghẹ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1093, 1, '1484', 'Hồ Thị Thu Hương', '1994-09-03', 'Nữ', NULL, 56, 'CN ĐD', NULL, '0399250721', NULL, 'P.Hưng Dũng -Vinh-NA', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1094, 1, '1465', 'Đậu Thị Hiền Chi', '1994-10-30', 'Nữ', NULL, 31, 'CN ĐD', NULL, '0986356645', NULL, 'Xã Hưng Lộc- TP Vinh- NA', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1095, 1, '1741', 'Nguyễn Thị Ánh Nguyệt', '1997-11-06', 'Nữ', NULL, 31, 'Cao đăng điều dưỡng', NULL, '0971013994', 'phanhaphuongcuong@gmail.com', NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1096, 1, '1718', 'Nguyễn Thị Hằng', '1997-09-09', 'Nữ', NULL, 31, 'Cao đăng điều dưỡng', NULL, '0389680864', NULL, NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1097, 1, '1186', 'Đặng Minh Thu', '1990-08-26', 'Nữ', NULL, 31, 'Cao đăng điều dưỡng', NULL, '0392379900', 'hangmin181998@gmail.com', 'Nghi Phú - Vinh-NA', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1098, 1, '0774', 'Lê Thị Sinh', '1986-09-10', 'Nữ', NULL, 31, 'Cao đăng điều dưỡng', NULL, '09116793840393264252', NULL, 'Xóm 10, Nghi Phú, Tp Vinh', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1099, 1, '1533', 'Chu Thị Hằng', '1995-02-25', 'Nữ', NULL, 31, 'Cao đăng điều dưỡng', NULL, '038508644', NULL, 'P Trường Thi- TP Vinh- NA', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1100, 1, '050ttdv', 'Trần Thị Huệ', '1971-07-06', 'Nữ', NULL, NULL, 'công nhân', NULL, NULL, NULL, NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1101, 1, '1763', 'Nguyễn Diệu Hằng', '1995-08-26', 'Nữ', NULL, 31, 'Cao đăng điều dưỡng', NULL, '0981953768', 'nhatdc205@gmail.com', NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1102, 1, '1766', 'Lê Thị Phương', '1997-10-19', 'Nữ', NULL, 31, 'Cao đăng điều dưỡng', NULL, '0366048477', 'phong010498@gmail.com', NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1103, 1, '0771', 'Nguyễn Thế Hoà', '1987-09-12', 'Nam', NULL, 31, 'Cao đăng điều dưỡng', NULL, '0946759789', 'levantu12a3yha@gmail.com', 'Số nhà 7, ngõ 2 đường Tân Hùng - P Hưng Lộc -TP Vinh', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1104, 1, '0791', 'Nguyễn Thị Ưng', '1987-12-25', 'Nữ', NULL, 69, 'Cao đăng điều dưỡng', NULL, '0976007284', 'hotuananh725@gmail.com', 'Xóm 1, Xã Tào Sơn, Anh Sơn, Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1105, 1, '1158', 'Nguyễn Thị Thảo', '1992-07-15', 'Nữ', NULL, 31, 'Cao đăng điều dưỡng', NULL, '0963628115', 'bsphamquochoang@gmail.com', 'Nghi Liên - Vinh -NA', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1106, 1, '1503', 'Hoàng Thị Trà', '1994-06-25', 'Nữ', NULL, 31, 'Cao đăng điều dưỡng', NULL, '0981573567', 'honghanh2904yhn@gmail.com', 'P. Hà Huy Tạp -Vinh - NA', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1107, 1, '0955', 'Nguyễn Thị Hằng Nga', '1988-09-14', 'Nữ', NULL, 33, 'Thạc sĩ', 'Nội khoa', '0983088149', NULL, 'Trường Thi -TP Vinh - Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1108, 1, '0024', 'Thái Văn Chương', '1984-05-04', 'Nam', 'Trưởng khoa Nội Cơ xương khớp', 33, 'BSCK II', 'Nội khoa', '0975525244', 'nguyenthucucdhyh1510@gmail.com', 'Khối 2, Thị trấn tân Kỳ, Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1109, 1, '0704', 'Lê Thị Thanh', '1987-09-04', 'Nữ', 'Phó khoa Nội Cơ xương khớp', 33, 'Thạc sĩ', 'Nội khoa', '0975459535', NULL, 'Số 6, Tuệ Tĩnh, Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1110, 1, '1419', 'Nguyễn Thị Hoài Thắm', '1994-04-24', 'Nữ', NULL, 33, 'Bác sĩ', NULL, '0359629029', 'tranthidung9101987@gmail.com', 'Thượng Sơn- Đô Lương- Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1111, 1, '1418', 'Trịnh Lê Khánh Linh', '1994-03-26', 'Nữ', NULL, 33, 'Bác sĩ NT', 'Nội khoa', '852474900', 'trangnho221191@gmail.com', 'Nghĩa Hội -Nghĩa Đàn -Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1112, 1, '1420', 'Nguyễn Thị Thúy', '1994-07-21', 'Nữ', NULL, 33, 'Bác sĩ', NULL, '0988874611', 'hasan2019@gmail.com', 'Thái Sơn -Đô lương -Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1113, 1, '1872', 'Hồ Thị Phương Thanh', '1996-06-08', 'Nữ', NULL, 33, 'Bác sĩ', NULL, '0969658474', 'vothithoa88@gmail.com', NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1114, 1, '0998', 'Lê Thị Thùy Dung', '1987-05-06', 'Nữ', NULL, 33, 'Thạc sĩ', 'Nội khoa', '0987644603', 'Ngoctu19972201@gmail.com', 'Phường Hồng Sơn- TP Vinh-Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1115, 1, '1467', 'Phan Thị Yến Trang', '1994-10-03', 'Nữ', NULL, 33, 'CN ĐD', NULL, '0344993537', 'drthanh2007@gmail.com', 'Quán Bàu- Tpvinh- Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1116, 1, '1227', 'Vương Khánh An', '1992-08-22', 'Nữ', NULL, 33, 'CN ĐD', NULL, '0398088128', NULL, 'Hưng Phúc -TP Vinh - Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1117, 1, '0685', 'Lê Thị Thu Phương', '1981-09-11', 'Nữ', NULL, 33, 'Cao đăng điều dưỡng', NULL, '0977509097', NULL, 'Xóm 5, Hưng thịnh, Hưng Nguyên, Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1118, 1, '1526', 'Đàm Thị Thủy', '1993-08-10', 'Nữ', NULL, 33, 'Cao đăng điều dưỡng', NULL, '0349371625', 'oanh.xeu@gmail.com', 'nghi phú-vinh-nghệ an', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1119, 1, '1494', 'Nguyễn Thị Ngọc Mai', '1994-04-14', 'Nữ', NULL, 33, 'Cao đăng điều dưỡng', NULL, '0974143905', 'thuyduc02011996@gmail.com', 'Nghi Kim- TP Vinh -Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1120, 1, '0667', 'Nguyễn Thị Lịch', '1984-08-19', 'Nữ', NULL, 33, 'Cao đăng điều dưỡng', NULL, '0977018057', NULL, 'P. Hưng Bình, Tp Vinh, Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1121, 1, '1147', 'Cao Thị Xuân', '1985-02-09', 'Nữ', NULL, 33, 'Cao đăng điều dưỡng', NULL, '0989416799', NULL, 'Nghi Đức -Tp Vinh - Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1122, 1, '0733', 'Trần Đình Sơn', '1986-10-18', 'Nam', 'Điều dưỡng trưởng', 33, 'Cử nhân điều dưỡng', NULL, '0989649823', 'nguyenhana050596@gmail.com', 'Xóm 3, Nam Giang, Nam Đàn, Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1123, 1, '1770', 'Hoàng Thị Trang', '1993-04-17', 'Nữ', NULL, 33, 'Cao đăng điều dưỡng', NULL, '0363353408', NULL, NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1124, 1, '1229', 'Hoàng Thị Thành', '1992-07-13', 'Nữ', NULL, 33, 'Cao đăng điều dưỡng', NULL, '0981454222', NULL, 'Hưng Phúc - TP Vinh - Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1125, 1, '0680', 'Trần Thị Hoa', '1983-04-17', 'Nữ', NULL, 33, 'Cao đăng điều dưỡng', NULL, '0988506129', 'phanlena2011@gmail.com', 'Xóm 6, Nghi Phú, Tp Vinh, Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1126, 1, '0749', 'Trần Thị Hào', '1981-09-12', 'Nữ', NULL, 33, 'Cao đăng điều dưỡng', NULL, '0964409932', NULL, 'Số 4, K4, P. Trung Đô, Tp Vinh', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1127, 1, '0795', 'Lê Văn Bình', '1981-10-10', 'Nam', 'Phó khoa Thần kinh', 13, 'BSCK II', 'Thạc sĩ Nội thần kinh', '0983749666', 'Hoanganhytb1805@gmail.com', 'Quỳnh Yên - Quỳnh Lưu - Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1128, 1, '0794', 'Nguyễn Văn Long', '1976-07-15', 'Nam', 'Trưởng khoa Thần kinh', 13, 'BSCK II', 'Thần kinh', '0973126768', NULL, 'Hưng Đông - Tp Vinh - N.An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1129, 1, '1321', 'Hồ Quang Thịnh', '1992-08-31', 'Nam', NULL, 13, 'Bác sĩ', NULL, '348761817', 'phongcanhka@gmail.com', 'Hưng Đông- Tp Vinh- Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1130, 1, '1132', 'Hồ Công Dũng', '1989-04-13', 'Nam', NULL, 13, 'Thạc sĩ', 'Nội khoa', '0904955009', 'truongnguyen64@gmail.com', 'Hà Huy Tập - TP Vinh - Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1131, 1, '0362', 'Lê Na', '1984-06-21', 'Nữ', NULL, 13, 'Thạc sĩ', 'Nội thần kinh', '0979161262', 'sonhai86@gmail.com', 'Xóm 2, hưng Chính, Vinh, Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1132, 1, '1801', 'Nguyễn Thị Nhung', '1995-06-30', 'Nữ', NULL, 13, 'Bác sĩ', NULL, '0399892205', 'huyhoadau@gmail.com', NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1133, 1, '1431', 'Lê Thị Huyền Trang', '1993-08-04', 'Nữ', NULL, 13, 'Bác sĩ', NULL, '0353475467', 'toiyeuvietnam.taqhop@gmail.com', 'Nghi Phú - Tp Vinh - Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1134, 1, '1657', 'Lê Thị Hà An', '1993-09-03', 'Nữ', NULL, 13, 'Bác sĩ', NULL, '0397424348', 'yeutinhoc1325@gmail.com', NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1135, 1, '1658', 'Phan Thị Quỳnh Chi', '1994-12-24', 'Nữ', NULL, 13, 'Bác sĩ', NULL, '0387031572', 'vovanchung93@gmail.com', NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1136, 1, '1874', 'Vương Đình Dũng', '1996-04-13', 'Nam', NULL, 13, 'Bác sĩ', NULL, '0399928581', 'vanquanydhue@gmail.com', NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1137, 1, '1875', 'Đặng Mai Phương', '1996-02-02', 'Nữ', NULL, 13, 'Bác sĩ', NULL, '0962427033', 'chutien8694@gmail.com', NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1138, 1, '1430', 'Nguyễn Thị Hương', '1994-10-20', 'Nữ', NULL, 13, 'Bác sĩ', NULL, '0359661315', 'hotruongthang0407@gmail.com', 'Nghi Phú - TP Vinh- Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1139, 1, '1873', 'Trịnh Thị Quỳnh', '1995-04-10', 'Nữ', NULL, 13, 'Bác sĩ', NULL, '0359394186', NULL, NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1140, 1, '1277', 'Phạm Thị Tâm', '1992-11-23', 'Nữ', NULL, 13, 'CN ĐD', NULL, '0367390783', 'ngocbangna@gmail.com', 'Đông Vĩnh- TP Vinh- Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1141, 1, '1456', 'Trần Thị Yến', '1994-11-26', 'Nữ', NULL, 13, 'CN ĐD', NULL, '0326735438', NULL, 'Thanh lĩnh- Thanh Chương- Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1142, 1, '1690', 'Phạm Thị Hải Giang', '1994-08-10', 'Nữ', NULL, 13, 'Cử nhân điều dưỡng', NULL, '0971415699', 'Ngaykv14081995@gamil.com', NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1143, 1, '1697', 'Phan Thị Thơm', '1994-07-06', 'Nữ', NULL, 13, 'Cử nhân điều dưỡng', NULL, '0359126043', NULL, NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1144, 1, '1672', 'Trịnh Thị Huyền', '1997-11-16', 'Nữ', NULL, 13, 'Cử nhân điều dưỡng', NULL, '0374738457', 'Chingoc0o0@gmail.com', NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1145, 1, '1166', 'Lê Thị Vinh', '1991-03-18', 'Nữ', NULL, 13, 'Cao đăng điều dưỡng', NULL, '0987238319', NULL, 'Nghi Hải - Cửa Lò - Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1146, 1, '1184', 'Hồ Thị Thủy', '1992-03-21', 'Nữ', NULL, 13, 'Cao đăng điều dưỡng', NULL, '0989116667', NULL, 'Trung Đô - TP Vinh - Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1147, 1, '0804', 'Phan Thị Hồng', '1988-10-06', 'Nữ', NULL, 13, 'CN ĐD', NULL, '0983649624', NULL, 'P Hà Huy Tập - TP Vinh', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1148, 1, '0821', 'Dương Thanh Nhật', '1988-12-25', 'Nữ', 'Điều dưỡng trưởng', 13, 'Cử nhân điều dưỡng', NULL, '0975474157', 'ngaykvinh@gmail.com', 'Nghi Liên - TP Vinh - Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1149, 1, '0813', 'Hoàng Thị Thường', '1982-11-28', 'Nữ', NULL, 13, 'Cao đăng điều dưỡng', NULL, '0989640906', NULL, 'Mậu Lâm - Hưng Lộc - TP Vinh', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1150, 1, '0815', 'Phan Hoàng Thạch Quỳnh', '1984-08-27', 'Nữ', NULL, 13, 'Cao đăng điều dưỡng', NULL, '0987444303', 'hoangdieuly7895@gmail.com', '79 Trường Tiến - Hưng Bình - TP Vinh', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1151, 1, '028ttdv', 'Nguyễn Thị Hoàng Yến', '1975-01-01', 'Nữ', NULL, NULL, 'CĐ KT', NULL, NULL, NULL, NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1152, 1, '1720', 'Lê Thị Tâm', '1993-04-12', 'Nữ', NULL, 13, 'Cao đăng điều dưỡng', NULL, '09864531830356260355', 'nguyenthilyti230295@gmaiulo.com', NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1153, 1, '1779', 'Nguyễn Thị Cẩm Nhung', '1998-03-26', 'Nữ', NULL, 13, 'Cao đăng điều dưỡng', NULL, '0837687998', 'ngocngoainieu@gmail.com', NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1154, 1, '1492', 'Trần Thị Uyên', '1992-11-15', 'Nữ', NULL, 13, 'Cao đăng điều dưỡng', NULL, '0987466551', NULL, 'Diễn Cát - Diễn Châu - Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1155, 1, '1762', 'Chu Thị Thanh Hà', '1994-09-04', 'Nữ', NULL, 13, 'Cao đăng điều dưỡng', NULL, '0379009053', '0989818567abc@gmail.com', NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1156, 1, '0820', 'Nguyễn Thị Hương', '1986-04-08', 'Nữ', NULL, 13, 'Cao đăng điều dưỡng', NULL, '0972980806', 'nguyenthian170887@gmail.com', 'Hưng Dũng - Tp Vinh - Nghệ AN', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1157, 1, '1367', 'Đinh Văn Tiệp', '1993-01-05', 'Nam', NULL, 18, 'Bác sĩ', NULL, '1638887313', 'Caothientho12101992@gmail.com', NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1158, 1, '0796', 'Lê Quang Toàn', '1981-10-20', 'Nam', 'Giám đốcTrung tâm Đột quỵ', 18, 'BSCK II', 'Lão khoa', '983021656', 'bskhoa115@gmail.com', 'Phường Nam Hồng - TX Hồng Lĩnh -HT', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1159, 1, '0957', 'Nguyễn Thanh Long', '1988-04-25', 'Nam', NULL, 18, 'Bác sĩ', NULL, '0977313063', 'tanghuycuong@hotmail.com', NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1160, 1, '0798', 'Nguyễn Thanh Tùng', '1985-10-04', 'Nam', NULL, 18, 'Thạc sĩ', 'Thần kinh', '0935669770', 'monitor.k39b@gmail.com', NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1161, 1, '1598', 'Đào Thanh Lưu', '1992-04-25', 'Nam', NULL, 18, 'Bác sĩ nội trú Nội khoa', 'Nội khoa', '0988125492', 'Phamdinhthinh0102@gmail.com', '35 Nguyễn Tiến Tài - Hưng Bình - Vinh - Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1162, 1, '1660', 'Phạm Phúc Hải', '1994-12-05', 'Nam', NULL, 18, 'Bác sĩ', NULL, '0974961889', 'luuducson95@gmail.com', NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1163, 1, '1661', 'Nguyễn Hiền Trang', '1995-04-06', 'Nữ', NULL, 18, 'Bác sĩ', NULL, '0339750432', 'Thinhnguyen1319@gmail.com', NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1164, 1, '1887', 'Nguyễn Thị Thơ', '1994-10-17', 'Nữ', NULL, 18, 'Bác sĩ', NULL, '0762856666', 'phamnguyenhann@gmail.com', NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1165, 1, '1888', 'Đặng Quang Thịnh', '1996-02-20', 'Nam', NULL, 18, 'Bác sĩ', NULL, '0942383614', 'hieuhamhochoi183@gmail.com', NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1166, 1, '0663', 'Kiều Văn Dương', '1987-07-26', 'Nam', 'Phó khoa', 18, 'Thạc sĩ', 'Nội khoa', '0976697009', 'Xongbadia91@gmail.com', NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1167, 1, '1429', 'Trương Đình Thống', '1994-08-24', 'Nam', NULL, 18, 'Bác sĩ', NULL, '01655779486', 'doanphongle1973@gmail.com', NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1168, 1, '1692', 'Nguyễn Thị Ngọc Hân', '1995-06-15', 'Nữ', NULL, 18, 'Cử nhân điều dưỡng', NULL, '0358221438', 'thilam9262@gmail.com', NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1169, 1, '1181', 'Nguyễn Thị Hồng', '1990-05-06', 'Nữ', 'Điều dưỡng trưởng', 18, 'CN ĐD', NULL, '0363360628', 'hoangmaithuy1994hoanglinh@gmail.com', NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1170, 1, '1472', 'Đặng Thị Giang', '1995-03-25', 'Nữ', NULL, 18, 'CN ĐD', NULL, '01692653503', 'dinhhanght96@gmail.com', NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1171, 1, '1677', 'Hoàng Thị Trà', '1997-04-08', 'Nữ', NULL, 18, 'Cử nhân điều dưỡng', NULL, '0334525884', NULL, NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1172, 1, '1490', 'Nguyễn Thị Thường', '1994-03-01', 'Nữ', NULL, 18, 'Cao đăng điều dưỡng', NULL, '0983756432', 'levantu12a3yha@gmail.com', NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1173, 1, '0800', 'Nguyễn Thị Trang', '1987-02-20', 'Nữ', NULL, 18, 'CN ĐD', NULL, '0972942800', NULL, 'Quỳnh Diễn - Quỳnh Lưu - Nghệ an', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1174, 1, '0802', 'Chu Thị Huế', '1988-05-08', 'Nữ', NULL, 18, 'Cao đăng điều dưỡng', NULL, '0982417927', 'nguyenthiha@gmail.com', 'X5 - Diễn Tháp - Diễn Châu -Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1175, 1, '0803', 'Lê Thị Vinh', '1989-07-14', 'Nữ', NULL, 18, 'CN ĐD', NULL, '0948648789', NULL, 'Khối Yên Hoà - P Hà Huy Tập - TP Vinh', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1176, 1, '1535', 'Hồ Thị Hương Giang', '1995-10-13', 'Nữ', NULL, 18, 'Cao đăng điều dưỡng', NULL, '963247741', NULL, NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1177, 1, '1715', 'Nguyễn Hoàng Yến Linh', '1993-05-19', 'Nữ', NULL, 18, 'Cao đăng điều dưỡng', NULL, '0338853886', 'thaole221292@gmail.com', NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1178, 1, '0650', 'Bùi Thị Thu Hường', '1985-06-12', 'Nữ', NULL, 18, 'Cao đăng điều dưỡng', NULL, '0915812685', NULL, 'P. Hưng Dũng, Tp Vinh, Nghệ an', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1179, 1, '0805', 'Võ Thị Thơ', '1988-03-07', 'Nữ', NULL, 18, 'Cao đăng điều dưỡng', NULL, '01674660453', 'hanhtrana123@gmail.com', 'X5 - Hưng Chính - Hưng Nguyên - TP Vinh', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1180, 1, '1180', 'Võ Thị Hoài Chung', '1991-09-05', 'Nữ', NULL, 18, 'Cao đăng điều dưỡng', NULL, '01656262025', NULL, NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1181, 1, '1182', 'Phạm Thị Hương', '1990-07-16', 'Nữ', NULL, 18, 'Cao đăng điều dưỡng', NULL, '01696912254', NULL, NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1182, 1, '0816', 'Trần Thị Hương', '1982-06-21', 'Nữ', NULL, 18, 'Cao đăng điều dưỡng', NULL, '0977560035', NULL, 'Xóm 16 - Hưng Lộc - TP Vinh', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1183, 1, '0814', 'Trần Thị Nga', '1981-10-16', 'Nữ', NULL, 18, 'Cao đăng điều dưỡng', NULL, '0985620377', NULL, 'Tân lâm - Hưng Dũng - TP Vinh', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1184, 1, '0823', 'Nguyễn Thị Dung', '1985-01-11', 'Nữ', NULL, 18, 'Cao đăng điều dưỡng', NULL, '0912253789', NULL, NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1185, 1, '1771', 'Phan Lễ Cầu', '1986-10-08', 'Nam', NULL, 18, 'Cao đăng điều dưỡng', NULL, '0966512769', 'Duyenmeo25011993@gmail.com', NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1186, 1, '0817', 'Nguyễn Nhật Thành', '1982-09-13', 'Nam', NULL, 18, 'Cao đăng điều dưỡng', NULL, '0974337899', 'drkien.pttk@gmail.com', 'Xóm 2 - Nghi Ân - TP Vinh', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1187, 1, '1493', 'Phạm Thị Nghĩa', '1996-06-23', 'Nữ', NULL, 18, 'Cao đăng điều dưỡng', NULL, '0963922875', 'phamtrongnam.pttk@gmail.com', NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1188, 1, '1084', 'Đậu Anh Tiến', '1988-11-24', 'Nam', NULL, 18, 'Cao đăng điều dưỡng', NULL, '0964797768', 'drthanh.hmu@gmail.com', NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1189, 1, '1776', 'Trương Thị Mỹ', '1996-09-24', 'Nữ', NULL, 18, 'Cao đăng điều dưỡng', NULL, '0964910982', 'quocphong201@gmail.com', NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1190, 1, '1878', 'Lê Thị Huyền Trang', '1995-11-04', 'Nữ', NULL, 37, 'Bác sĩ', NULL, '0979768153', 'vinhhienqy@gmail.com', NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1191, 1, '0850', 'Phan Thị Hòa', '1987-10-09', 'Nữ', NULL, 37, 'BSCK I', 'Da liễu', '0983857486', 'bsducpttkna@gmail.com', 'Hưng Lộc, Tp Vinh, Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1192, 1, '1394', 'Đậu Thị Minh Huệ', '1994-12-10', 'Nữ', NULL, 37, 'Bác sĩ', NULL, '0359795235', 'nguyentrantien0605@gmail.com', 'Xóm 1- Diễn Tháp - Diễn Châu - Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1193, 1, '1395', 'Chu Thị Hương Thảo', '1994-09-05', 'Nữ', NULL, 37, 'Bác sĩ', NULL, '0975677469', 'drviet.vn@gmail.com', 'xóm 2 Diễn Tháp - Diễn Châu -nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0);
INSERT INTO `dm_nhan_vien` (`id`, `benh_vien_id`, `ma_nv`, `ho_ten`, `ngay_sinh`, `gioi_tinh`, `chuc_danh`, `khoa_phong_id`, `trinh_do`, `chuyen_khoa`, `dien_thoai`, `email`, `dia_chi`, `trang_thai`, `ngay_tao`, `ngay_cap_nhat`, `nguoi_tao`, `nguoi_cap_nhat`, `da_xoa`) VALUES
(1194, 1, '023ttdv', 'Hoàng Thị Hường', '1977-03-01', 'Nữ', NULL, NULL, '12/12', NULL, NULL, NULL, NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1195, 1, '0856', 'Phạm Thị Thu Hà', '1982-10-21', 'Nữ', NULL, 37, 'Cao đăng điều dưỡng', NULL, '0988943467', 'laithihuyentrang13232@gmail.com', 'Hưng Lộc, Tp Vinh', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1196, 1, '0857', 'Nguyễn Thị Thủy', '1989-05-19', 'Nữ', NULL, 37, 'Cao đăng điều dưỡng', NULL, '0981095368', 'hd3424719@gmail.com', 'Xóm 16, xã Hưng Lộc, Tp Vinh, Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1197, 1, '024ttdv', 'Hoàng Thị Thanh Tú', '1978-08-11', 'Nữ', NULL, NULL, '12/12', NULL, NULL, 'nguyenhuuthanh19840208@gmail.com', NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1198, 1, '035ttdv', 'Tống Thị Thắm', '1956-10-20', 'Nữ', NULL, NULL, '12/12', NULL, NULL, NULL, NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1199, 1, '0399', 'Lê Văn Dũng', '1976-08-10', 'Nam', 'Phó khoa Gây mê hồi sức', 23, 'BSCK I', 'Gây mê hồi sức', '0912512353', 'hanhpttk89@gmail.com', 'Trung Đô - TP. Vinh - Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1200, 1, '0404', 'Trần Tú Anh', '1985-04-12', 'Nam', NULL, 23, 'Thạc sĩ', 'Gây mê hồi sức', '986422248', 'thaihoahndk@gmail.com', 'xóm 14- Nghi Phú- Vinh - Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1201, 1, '1408', 'Nguyễn Lương Bằng', '1994-10-13', 'Nam', NULL, 23, 'Bác sĩ', NULL, '0989768784', 'hanctchpro@gmail.com', 'Nghi Phú -tp vinh -Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1202, 1, '0402', 'Nguyễn Khắc Tú', '1983-07-09', 'Nam', NULL, 23, 'BSCK I', 'Gây mê hồi sức', '0935056568', 'diepkhue2017@gmail.com', 'Hưng Dũng, Tp Vinh, Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1203, 1, '1400', 'Lê Thị Mỹ Trang', '1994-02-12', 'Nữ', NULL, 23, 'Bác sĩ', NULL, '0918893333', 'khanhpttk@gmail.com', 'Ngõ 9 Bùi Dương Lịch- TP Vinh', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1204, 1, '1401', 'Đậu Văn Ngọc', '1993-10-19', 'Nam', NULL, 23, 'Bác sĩ', NULL, '0975764862', 'hoaru1986@gmail.com', 'xóm 18, nghi phú, Tp vinh', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1205, 1, '1092', 'Trần Hữu Hiếu', '1987-01-01', 'Nam', NULL, 23, 'Thạc sĩ', 'Gây mê hồi sức', '979272270', 'duyduc2309@gmail.com', 'Quang Tiến - TX Thái Hòa - Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1206, 1, '1614', 'Nguyễn Huy Đạt', '1994-05-20', 'Nam', NULL, 23, 'Bác sĩ', NULL, '0974020520', 'namhqn@gmail.com', NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1207, 1, '1615', 'Đỗ Ngọc Trọng', '1994-06-03', 'Nam', NULL, 23, 'Bác sĩ', NULL, '09753101840977611955', 'namp95799@gmail.com', NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1208, 1, '1650', 'Phạm Thị Thu Uyên', '1995-12-26', 'Nữ', NULL, 23, 'Bác sĩ', NULL, '0398484837', 'nguyenthilieu120789@gmail.com', NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1209, 1, '1822', 'Mai Thế Hữu', '1996-12-02', 'Nam', NULL, 23, 'Bác sĩ', NULL, '0972751634', 'tuanluong1977@gmail.com', NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1210, 1, '1823', 'Hoàng Đình Kiên', '1996-04-19', 'Nam', NULL, 23, 'Bác sĩ', NULL, '0372026226', 'quynhanhbui.908@gmail.com', NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1211, 1, '1824', 'Trần Thị Mai Hương', '1996-08-10', 'Nữ', NULL, 23, 'Bác sĩ', NULL, '0978240046', 'tohanhhongson@gmail.com', NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1212, 1, '1825', 'Cao Văn Hậu', '1995-04-15', 'Nam', NULL, 23, 'Bác sĩ', NULL, '0968796369', NULL, NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1213, 1, '1826', 'Dương Đình Hiếu', '1995-11-26', 'Nam', NULL, 23, 'Bác sĩ', NULL, '0968428795', 'vanhuylinh77882015@mail.com', NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1214, 1, '1827', 'Nguyễn Thị Tuyết', '1995-11-11', 'Nữ', NULL, 23, 'Bác sĩ', NULL, '03531772060989189506', 'tommydr87@gmail.com', NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1215, 1, '1118', 'Võ Thế Trung', '1990-10-29', 'Nam', NULL, 23, 'Thạc sĩ', 'Gây mê hồi sức', '0974275531', NULL, 'Xuân Yên - Nghi Xuân - Hà Tĩnh', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1216, 1, '0401', 'Hồ Viết Thắng', '1974-05-02', 'Nam', 'Trưởng khoa Gây mê hồi sức', 23, 'BSCK II', 'Ths Gây mê hồi sức', '0912037986', 'hunghovan94na@gmail.com', 'Khối Trung Hòa- P.Hà Huy Tập - TP.Vinh', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1217, 1, '0900', 'Trần Thị Thu', '1990-12-04', 'Nữ', NULL, 23, 'Cao đẳng Điều dưỡng', NULL, '0982807626', 'dangduong92@gmail.com', 'Nghi Trung - Nghi Lộc - Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1218, 1, '0508', 'Phan Bùi Thịnh', '1989-09-12', 'Nam', NULL, 23, 'CN ĐD', NULL, '0914120989', NULL, 'Hưng Khánh- Hưng Nguyên - Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1219, 1, '0405', 'Nguyễn Thị Thu Hương', '1988-02-06', 'Nữ', NULL, 23, 'CN ĐD', NULL, '383858662', 'duyducmai96@gmail.com', 'Xóm 10 - Nghi Liên - TP Vinh - Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1220, 1, '1477', 'Nguyễn Thị Tuyết Hoài', '1996-08-19', 'Nữ', NULL, 23, 'CN ĐD', NULL, '0967120429', 'hmcuong1123@gmail.com', 'Sơn Phố - Hương Sơn - Hà Tĩnh', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1221, 1, '1258', 'Lê Văn Hưng', '1989-01-08', 'Nam', NULL, 57, 'CN ĐD', NULL, '0989455080', 'khanhnguyenphan94bkdu@gmail.com', 'Xã Nghi Lâm - Nghi Lộc - Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1222, 1, '1673', 'Đinh Thị Liệu', '1993-06-23', 'Nữ', NULL, 23, 'Cử nhân điều dưỡng', NULL, '0944070817', 'drtuankyanh1996@gmail.com', NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1223, 1, '1669', 'Nguyễn Thị Kim Cúc', '1995-07-14', 'Nữ', NULL, 23, 'Cử nhân điều dưỡng', NULL, '0969776803', 'dr.ducvuong@gmail.com', NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1224, 1, '0075', 'Bùi Anh Vinh', '1989-03-07', 'Nam', NULL, 23, 'CN ĐD', NULL, '0979324689', 'trandungbina1994@gmail.com', 'Vân Diên  - Nam Đàn - Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1225, 1, '0406', 'Trần Xuân Bình', '1982-08-17', 'Nam', 'KTV trưởng', 23, 'Cử nhân điều dưỡng', NULL, '0979197555', 'quoctruongub.na@gmail.com', 'Xóm 12 - Xã Nghi Kim - TP Vinh', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1226, 1, '0407', 'Hoàng Việt Tiệp', '1985-07-06', 'Nam', NULL, 23, 'Cao đăng điều dưỡng', NULL, '0982822188', 'Thanhnguyen10.93@gmail.com', 'Xóm Mẫu Đơn - Hưng Lộc - TP.Vinh - Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1227, 1, '0408', 'Nguyễn Trọng Võ', '1987-09-19', 'Nam', NULL, 23, 'Cao đăng điều dưỡng', NULL, '0975768178', 'hoangngan22.83@gmail.com', 'Nghi Phú, Vinh, Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1228, 1, '0409', 'Phan Thị Hoài', '1986-07-13', 'Nữ', NULL, 23, 'Cao đăng điều dưỡng', NULL, '0975266118', 'habaobinh16021991@gmail.com', 'Hưng Tây - Hưng Nguyên- Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1229, 1, '0411', 'Lê Nhật Tân', '1986-12-05', 'Nam', NULL, 23, 'Cao đăng điều dưỡng', NULL, '0912122171', 'Tamnguyenthuy84@gmail.com', 'Tân Phúc - Vinh Tân - Tp Vinh', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1230, 1, '0413', 'Bùi Thị Thu Hiền', '1988-11-19', 'Nữ', NULL, 23, 'Cao đăng điều dưỡng', NULL, '0968270549', 'buihoai.18894@gmai.com', 'xóm 10-nghi phú-tpvinh-nghệ an', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1231, 1, '0894', 'Nguyễn Thị Châu', '1988-09-10', 'Nữ', NULL, 23, 'Cao đăng điều dưỡng', NULL, '0974457083', 'nhatmah101087@gmai.com', 'Phường Lê Mao, Vinh, Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1232, 1, '0897', 'Lê Thị Linh', '1988-09-02', 'Nữ', NULL, 23, 'Cao đăng điều dưỡng', NULL, '0987732204', 'anhson22091993@gmail.com', 'P.Hưng Bình - TP Vinh - Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1233, 1, '0899', 'Nguyễn Thị Hồng Nhung', '1989-11-01', 'Nữ', NULL, 23, 'Cao đăng điều dưỡng', NULL, '0985306919', NULL, 'P.Hưng Bình - TP Vinh - Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1234, 1, '0919', 'Đặng Bá Sỹ', '1989-02-24', 'Nam', NULL, 23, 'Cao đăng điều dưỡng', NULL, '0374660677', NULL, 'Nghi Liên - TP.Vinh- Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1235, 1, '0920', 'Ngô Trí Nhân', '1987-04-05', 'Nam', NULL, 23, 'Cao đăng điều dưỡng', NULL, '0368711217', 'tophuongthaotk123@gmail.com', 'P.Hưng Dũng - TP.Vinh -  Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1236, 1, '0896', 'Phan Thị Giang', '1989-07-20', 'Nữ', NULL, 23, 'Cao đăng điều dưỡng', NULL, '916186456', NULL, 'P. Hưng Bình - TP Vinh - Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1237, 1, '1215', 'Phùng Thị Huyền Trang', '1993-07-02', 'Nữ', NULL, 23, 'Cao đăng điều dưỡng', NULL, '0399881702', NULL, 'Xóm 2 - Nghi Kim - Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1238, 1, '0421', 'Trần Xuân Huy', '1978-06-10', 'Nam', NULL, 23, 'Cao đăng điều dưỡng', NULL, '0915832777', 'Drchuongnp@gmail.com', 'Xuân Hùng, Hưng Lộc, Tp Vinh, Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1239, 1, '0261', 'Nguyễn Thị Hóa', '1986-07-24', 'Nữ', NULL, 23, 'Cao đăng điều dưỡng', NULL, '0975704034', 'tranquoctruong4@gmail.com', 'Tân Sơn, Đô Lương, Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1240, 1, '040ttdv', 'Trần Thị Thu Hường', '1964-01-15', 'Nữ', NULL, NULL, 'CĐ KT', NULL, NULL, NULL, NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1241, 1, '030ttdv', 'Nguyễn Thị Vân', '1971-05-29', 'Nữ', NULL, NULL, 'CĐ KT', NULL, NULL, NULL, NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1242, 1, '0428', 'Nguyễn Thị Nga', '1980-02-02', 'Nữ', NULL, 23, 'Cao đăng điều dưỡng', NULL, '0977557667', 'bong.hong.tang.em.xch@gmail.com', 'Hưng Tây, Hưng Nguyên, Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1243, 1, '0429', 'Nguyễn Thị Hảo', '1982-03-10', 'Nữ', NULL, 23, 'Cao đăng điều dưỡng', NULL, '0942786879', 'dongoc5390@gmail.com', 'Xóm Ngũ Lộc, Xã Hưng Lộc, Tp Vinh, Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1244, 1, '026ttdv', 'Lê Thị Thủy', '1973-10-27', 'Nữ', NULL, NULL, '12/12', NULL, NULL, NULL, NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1245, 1, '0433', 'Nguyễn Thị An', '1986-12-22', 'Nữ', NULL, 23, 'Cao đăng điều dưỡng', NULL, '0972337417', 'lequangdao866@gmail.com', 'Xóm 6, Nghi Liên, Tp Vinh, Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1246, 1, '0434', 'Nguyễn Thị Hường', '1990-05-20', 'Nữ', NULL, 23, 'Cao đăng điều dưỡng', NULL, '0981710456', 'nguyenthihuong25031995@gmail.com', 'Xóm 15 - Nghi Phú - TP Vinh -  Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1247, 1, '0431', 'Trần Văn Phú', '1987-10-05', 'Nam', NULL, 23, 'Cao đăng điều dưỡng', NULL, '0968530367', 'nhungdinhh1994@gmail.com', 'Châu Nhân - Hưng Châu - Hưng Nguyên - Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1248, 1, '1522', 'Lê Văn Sáng', '1992-10-15', 'Nam', NULL, 23, 'Cao đăng điều dưỡng', NULL, '0966256386', 'anhtun260297@gmail.com', 'Hùng sơn - Anh sơn - Nghệ an', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1249, 1, '1248', 'Nguyễn Thị Hiệp', '1993-11-12', 'Nữ', NULL, 23, 'Cao đăng điều dưỡng', NULL, '0976465156', NULL, 'Nam Cát - Nam Đàn - Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1250, 1, '1585', 'Nguyễn Thị Duyên', '1994-02-03', 'Nữ', NULL, 23, 'Cao đăng điều dưỡng', NULL, '0965349832', 'Dr.hvchau@gmail.com', 'Nghi Phú - TP Vinh - Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1251, 1, '1777', 'Nguyễn Thị Hằng Nga', '1994-02-04', 'Nữ', NULL, 23, 'Cao đăng điều dưỡng', NULL, '0983546168', 'whiteeaglebym@gmail.com', NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1252, 1, '1747', 'Bùi Thị Yến', '1993-06-10', 'Nữ', NULL, 23, 'Cao đăng điều dưỡng', NULL, '0961707205', NULL, NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1253, 1, '0921', 'Nguyễn Văn Lĩnh', '1989-10-24', 'Nam', NULL, 23, 'Cao đăng điều dưỡng', NULL, '0915902226', 'vanlinh2410@gmail.com', 'Xóm Kim Phúc, xã Nghi Ân, TP Vinh, Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1254, 1, '1268', 'Cao Xuân Tư', '1991-04-08', 'Nam', NULL, 23, 'Cao đăng điều dưỡng', NULL, '0918485262', NULL, 'Nghi Phú - TP.Vinh - Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1255, 1, '0410', 'Nguyễn Thị Hằng', '1985-06-03', 'Nữ', NULL, 23, 'Cao đăng điều dưỡng', NULL, '0919516066', NULL, 'Khối 6, Phường Bến Thuỷ, Tp Vinh, Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1256, 1, '0412', 'Trần Đình Quang', '1986-03-25', 'Nam', NULL, 23, 'Cao đăng điều dưỡng', NULL, '0947287965', NULL, 'Thanh Tiến, Hưng Bình, Tp Vinh', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1257, 1, '073ttdv', 'Cao Thị Anh', '1978-02-20', 'Nữ', NULL, NULL, NULL, NULL, NULL, 'dinhquanghmu@gmail.com', NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1258, 1, '0439', 'Nguyễn Thị Huyền', '1986-10-20', 'Nữ', NULL, 23, 'CN CĐPS', NULL, '0967332777', NULL, 'Xuân Hùng, Hưng Lộc, Tp Vinh, Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1259, 1, '076ttdv', 'Lê Thị Hồng Hạnh', '1969-09-15', 'Nữ', NULL, NULL, NULL, NULL, NULL, 'nhuhaovmu@gmail.com', NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1260, 1, '084ttdv', 'Trần Thị Dung', '1965-02-27', 'Nữ', NULL, NULL, NULL, NULL, NULL, 'chanhuong2111@gmail.com', NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1261, 1, '0162', 'Tạ Thị Lương', '1975-01-28', 'Nữ', NULL, 16, 'LĐ PT', NULL, '0394565085', 'levankhoamat@gmail.com', 'Hưng Lộc - TP Vinh - Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1262, 1, '0959', 'Nguyễn Gia Anh', '1986-09-02', 'Nam', NULL, 38, 'Thạc sĩ', 'Y học cổ truyền', '0943069959', NULL, NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1263, 1, '1329', 'Bành Thị Hồng Vinh', '1992-09-22', 'Nữ', NULL, 38, 'Bác sĩ', NULL, '0979792928', 'dr.banhthihongvinh@gmail.com', NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1264, 1, '0960', 'Hồ Thị Thành', '1986-03-08', 'Nữ', NULL, 38, 'Bác sĩ', NULL, '979286696', 'thanh.promise87@gmail.com', NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1265, 1, '1880', 'Nguyễn Quang Long', '1994-02-05', 'Nam', NULL, 38, 'Bác sĩ', NULL, '0913011508', 'long1994@gmail.com', NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1266, 1, '0165', 'Vương Thị Loan', NULL, 'Nữ', NULL, 16, 'LĐ PT', NULL, '0373883589', NULL, 'Hưng Dũng - TP Vinh - Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1267, 1, '1476', 'Nguyễn Thị Dung', '1993-03-25', 'Nữ', NULL, 38, 'CN ĐD', NULL, '01685569634', NULL, NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1268, 1, '0611', 'Nguyễn Thị Hà', '1987-06-16', 'Nữ', 'Điều dưỡng trưởng', 38, 'Thạc sĩ sinh học', NULL, '0334303444', 'nguyenthiha3444@gmail.com', NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1269, 1, '1263', 'Ngô Thị Thanh', '1992-06-24', 'Nữ', NULL, 38, 'CN ĐD', NULL, '0949863777', NULL, NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1270, 1, '0863', 'Trần Thị Sâm', '1979-08-18', 'Nữ', NULL, 38, 'Cao đăng điều dưỡng', NULL, '0979606383', NULL, 'Xóm 14 -Nghi Phong - Nghi Lộc - Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1271, 1, '0870', 'Đoàn Vinh Thủy', '1980-04-20', 'Nam', NULL, 38, 'Cao đăng điều dưỡng', NULL, '0916641980', NULL, 'Xóm 1 - Nghi Phú - TP Vinh', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1272, 1, '0865', 'Nguyễn Thị Quỳnh Trang', '1983-06-02', 'Nữ', NULL, 38, 'Cao đăng điều dưỡng', NULL, '0914662768', 'quynhtrang02061983@gmail.com', 'K12 - Phường Hà Huy Tập - TP Vinh', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1273, 1, '0868', 'Phạm Thị Nhung', '1985-03-01', 'Nữ', NULL, 38, 'Cao đăng điều dưỡng', NULL, '0985383393', NULL, NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1274, 1, '0866', 'Phan Thị Thanh Thảo', '1987-07-23', 'Nữ', NULL, 38, 'Cao đăng điều dưỡng', NULL, '0901711878', NULL, 'Xóm 2B - Hưng Đạo - Hưng Nguyên -Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1275, 1, '0968', 'Nguyễn Tuấn anh', '1987-09-23', 'Nam', 'Trưởng khoa Phục hồi chức năng', 48, 'Thạc sĩ', 'Ths Y học cổ truyền', '0987546887', 'tuananhbv87@gmail.com', 'Hưng Đông - TP Vinh', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1276, 1, '1048', 'Lê Mai Anh', '1989-01-22', 'Nữ', NULL, 48, 'Bác sĩ', NULL, '0964137333', 'lemaianhvn@gmail.com', 'Nghi Phú -TP Vinh-Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1277, 1, '1138', 'Nguyễn Thị Ngọc Trâm', '1990-02-01', 'Nữ', NULL, 48, 'Bác sĩ', NULL, '0916076362', 'Tramxoan90@gmail.com', 'Hưng Lộc-TP Vinh-Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1278, 1, '1651', 'Lê Thị Bích Thảo', '1995-09-04', 'Nữ', NULL, 48, 'Bác sĩ', NULL, '0983730444', 'thaobichle49@gmail.com', NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1279, 1, '1882', 'Nguyễn Thị Minh', '1996-03-10', 'Nữ', NULL, 48, 'Bác sĩ', NULL, '0363535089', 'minh10031996@gmail.com', NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1280, 1, '1459', 'Bùi Thị Luận', '1993-11-10', 'Nữ', NULL, 48, 'CN ĐD', NULL, '0359783660', 'buithiluan03@gmail.com', 'Giang Sơn tây-Đô Lương- Nghệ an', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1281, 1, '1150', 'Nguyễn Thị Thu Hằng', '1991-08-29', 'Nữ', NULL, 48, 'Cao đăng điều dưỡng', NULL, '0373286345', 'thuhang91.bvdkna@gmail.com', 'Nghi Công Bắc-Nghi Lộc-Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1282, 1, '0693', 'Nguyễn Thị Như Lê', '1984-03-01', 'Nữ', NULL, 48, 'Cao đăng điều dưỡng', NULL, '0915425222', 'letainguyen.1384@gmail.com', 'Khôi  Xuân Bắc - Hưng Dũng - TP Vinh', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1283, 1, '0878', 'Phạm Thị Thu Hiền', '1989-10-22', 'Nữ', NULL, 48, 'Cao đăng điều dưỡng', NULL, '0379812808', 'phamthithuhien221089@gmail.com', 'Xóm 2, Hưng Lợi, Hưng Nguyện, Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1284, 1, '1311', 'Võ Ngọc Mai', '1989-07-26', 'Nữ', NULL, 48, 'Cao đăng điều dưỡng', NULL, '0366322194', 'vongocmai260789@gmail.com', 'Hưng Lộc-TPVinh-Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1285, 1, '0874', 'Nguyễn Tài Thành', '1987-12-12', 'Nam', 'Phụ trách điều hành ĐDT', 48, 'Cử nhân điều dưỡng', NULL, '0965823586', 'nguyentailam04032019@gmail.com', 'Đông Vĩnh, Tp Vinh, Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1286, 1, '0879', 'Ngô Thúy Vân', '1990-10-08', 'Nữ', NULL, 48, 'Cao đăng điều dưỡng', NULL, '0969880977', 'ngothuyvan810@gmail.com', 'Vĩnh Quang, Đông Vĩnh, Tp Vinh, Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1287, 1, '1735', 'Đặng Thị Thư', '1996-06-22', 'Nữ', NULL, 48, 'Cao đăng điều dưỡng', NULL, '0369162800', 'dangthu5356@gmail.com', NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1288, 1, '1178', 'Nguyễn Thị Thắm', '1988-07-11', 'Nữ', NULL, 56, 'CN VLTL', NULL, '0977356118', 'thamnguyen.110788@gmail.com', 'Hưng Đông-TP Vinh-Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1289, 1, '0880', 'Văn Sỹ Sinh', NULL, 'Nam', NULL, 48, 'KTVTH', NULL, '0975494428', 'vvansysinh@icloud.com', 'Khối Liên Cơ, P. Hưng Bình, Tp Vinh', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1290, 1, '1573', 'Cao Tuấn Anh', '1993-08-05', 'Nam', NULL, 17, 'Kỹ sư CNTP', NULL, '0915935999', 'ctabvna@gmail.com', 'Hưng Lộc - Thành phố Vinh- Tỉnh Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1291, 1, '0154', 'Bành Thị Thu Hằng', '1973-07-06', 'Nữ', NULL, 16, 'LĐ PT', NULL, '0858364888', NULL, 'Hưng Dũng - Vinh - Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1292, 1, '1613', 'Phạm Thị Thu Hà', '1995-08-22', 'Nữ', NULL, 17, 'Bác sĩ', NULL, '0971534228', 'phamthuha.dhyhp@gmail.com', NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1293, 1, '1023', 'Nguyễn Thị Thơm', '1989-06-17', 'Nữ', 'Phó trưởng khoa Dinh dưỡng', 17, 'Thạc sĩ', 'Dinh dưỡng', '0943282333', 'nguyenthom8989@gmail.com', 'Thanh Hương- Thanh chương- Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1294, 1, '0507', 'Hoàng Thị Hải', '1986-10-03', 'Nữ', 'Điều dưỡng trưởng', 17, 'Thạc sĩ sinh học', NULL, '0977286406', 'haiht0310@gmail.com', 'Nghi Ân - Thành phố Vinh- Tỉnh Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1295, 1, '0455', 'Nghiêm Thị Thu Hường', '1985-10-07', 'Nữ', NULL, 17, 'CN ĐD', NULL, '0942349585', 'nghiemhuong0710@gmail.com', 'K 10, P Quang Trung, Tp Vinh, Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1296, 1, '0076', 'Nguyễn Thị Diệp', '1988-06-04', 'Nữ', NULL, 17, 'CN ĐD', NULL, '0383823297', 'hmudiepanh88@gmail.com', 'Nghi Công Bắc- Nghi Lộc - Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1297, 1, '1528', 'Nguyễn Thị Văn', '1993-10-10', 'Nữ', NULL, 57, 'Cao đăng điều dưỡng', NULL, '0354228212', 'nguyenthivan10109388@gmail.com', 'Nghi liên- TP Vinh- Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1298, 1, '1913', 'Hoàng Thị Quỳnh', '1996-07-09', 'Nữ', NULL, 17, 'Cử nhân dinh dưỡng', NULL, '0981217299', 'hoangquynhhmu@gmail.com', NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1299, 1, '0660', 'Nguyễn Trung Kiên', '1983-06-20', 'Nam', 'Phó khoa hồi sức ngoại khoa', 57, 'BSCK I', 'Hồi sức cấp cứu', '098355526', 'kienicu2016@gmail.com', 'Hưng Dũng - Thành Phố Vinh- Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1300, 1, '0948', 'Nguyễn Xuân Quảng', '1986-09-02', 'Nam', 'Phó khoa Nhiễm khuẩn tổng hợp', 36, 'BSCK I', 'Hồi sức cấp cứu', '0974336340', NULL, NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1301, 1, '1137', 'Vũ Văn Tình', '1990-10-08', 'Nam', NULL, 36, 'BSCK I', 'Truyền nhiễm và các bệnh nhiệt đới', '0979573994', 'vuvantinhytb@gmail.com', 'Xóm 5 - Xã Quỳnh Hưng - Huyện Quỳnh Lưu - Tỉnh Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1302, 1, '1876', 'Nguyễn Thị Minh Hiếu', '1996-11-02', 'Nữ', NULL, 36, 'Bác sĩ', NULL, '0398145869', 'minhhieuhmu0211@gmail.com', NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1303, 1, '0981', 'Hoàng Văn Thái', '1983-10-05', 'Nam', 'Phó khoa Nhiễm khuẩn tổng hợp', 56, 'BSCK I', 'Cấp cứu đa khoa', '0982578360', 'BShoangthai@gmail.com', 'Khối I- TT Yên Thành - Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1304, 1, '1336', 'Lê Thị Ngân', '1992-11-19', 'Nữ', NULL, 36, 'Bác sĩ', NULL, '0986039513', 'nganle2601@gmail.com', NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1305, 1, '1572', 'Nguyễn Thị Tuyết Mai', '1994-08-19', 'Nữ', NULL, 36, 'Bác sĩ', NULL, '0343307545', NULL, NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1306, 1, '1389', 'Trịnh Thị Thảo', '1994-06-30', 'Nữ', NULL, 36, 'Bác sĩ', NULL, '0967253006', NULL, NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1307, 1, '1602', 'Lương Thị Loan', '1994-08-07', 'Nữ', NULL, 36, 'Bác sĩ', NULL, '0829422281', 'drloan.dkna@gmail.com', NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1308, 1, '1877', 'Nguyễn Thị Hiền', '1995-01-04', 'Nữ', NULL, 36, 'Bác sĩ', NULL, '0359203420', 'tamynguyenphan@gmail.com', NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1309, 1, '1046', 'Hoàng Thị Hiệp', '1989-06-26', 'Nữ', NULL, 36, 'BSCK I', 'Nội khoa', '0385516971', 'hoangthihiep9@gmail.com', 'Xóm Quang Trung - Xã Nghi Diên - Huyện Nghi Lộc - Tỉnh Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1310, 1, '1328', 'Đặng Bá Tỏa', '1992-08-17', 'Nam', NULL, 36, 'Thạc sĩ', 'Truyền nhiễm và các bệnh nhiệt đới', '0363862729', 'dangbatoa@gmail.com', 'Xã Mỹ Sơn - Huyện Đô Lương - Thành phố Vinh - Tỉnh Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1311, 1, '1601', 'Nguyễn Thị Trang', '1993-10-10', 'Nữ', NULL, 36, 'Bác sĩ', NULL, '0338702893', 'nttrangdhy1016@gmail.com', NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1312, 1, '0826', 'Hà Phúc Hòa', '1979-10-02', 'Nam', 'Trưởng khoa Nhiễm khuẩn tổng hợp', 36, 'BSCK II', 'Truyền nhiễm và các bệnh nhiệt đới', '0913355554', NULL, 'Tân Tiến, Hưng Dũng, Tp Vinh', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1313, 1, '0828', 'Lê Thị Hoa', '1985-01-08', 'Nữ', NULL, 36, 'BSCK II', 'Nội tiêu hóa', '0946901255', 'ngocdiepmiss1403@gmail.com', 'K. Tân Lâm, Hưng Dũng, Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1314, 1, '1245', 'Phạm Thị Ngọc', '1989-09-16', 'Nữ', NULL, 36, 'CN ĐD', NULL, '0979202506', NULL, NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1315, 1, '1220', 'Lê Thị Huyền', '1991-12-01', 'Nữ', NULL, 36, 'CN ĐD', NULL, '01649746155', 'huyenkhoalethi1991@gmail.com', NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1316, 1, '1684', 'Đậu Thị Quỳnh', '1992-03-02', 'Nữ', NULL, 36, 'Cử nhân điều dưỡng', NULL, '0387739270', NULL, NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1317, 1, '1666', 'Lê Thị Trang', '1994-06-09', 'Nữ', NULL, 36, 'Cử nhân điều dưỡng', NULL, '0976006796', NULL, NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1318, 1, '1482', 'Nguyễn Phương Thảo', '1995-08-27', 'Nữ', NULL, 36, 'CN ĐD', NULL, '0986643037', 'nguyenphuongthao270895@gmail.com', 'Khối Tân Hợp - Phường Hưng Dũng - Thành phố Vinh - Tỉnh Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1319, 1, '1704', 'Bùi Thị Quỳnh Trang', '1994-11-13', 'Nữ', NULL, 36, 'Cao đăng điều dưỡng', NULL, '0984181170', NULL, NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1320, 1, '1497', 'Nguyễn Thị Hậu', '1992-04-01', 'Nữ', NULL, 36, 'Cao đăng điều dưỡng', NULL, '01693182037', NULL, NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1321, 1, '0332', 'Nguyễn Thế Lợi', '1987-01-27', 'Nam', 'Phụ trách điều hành ĐDT', 36, 'Cao đăng điều dưỡng', NULL, '0978585847', NULL, 'Khối Tân Yên - Hưng Bình - TP Vinh', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1322, 1, '1491', 'Trần Thị Huyền Linh', '1996-01-24', 'Nữ', NULL, 36, 'Cao đăng điều dưỡng', NULL, '0978200096', 'tranhuyenlinh2401@gmail.com', 'Xã Diễn Trường - Huyện Diễn Châu - Tỉnh Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1323, 1, '1537', 'Lưu Thị Thạo', '1994-01-30', 'Nữ', NULL, 36, 'Cao đăng điều dưỡng', NULL, '0358993577', NULL, NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1324, 1, '1240', 'Phạm Thị Hải Yến', '1993-09-23', 'Nữ', NULL, 36, 'Cao đăng điều dưỡng', NULL, '0336319976', 'phamhaiyen23091993@gmail.com', 'Nghi Công Nam - Nghi Lộc - Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1325, 1, '1800', 'Nguyễn Thị Duyên', '1993-01-29', 'Nữ', NULL, 36, 'Cao đăng điều dưỡng', NULL, '0941861113', NULL, NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1326, 1, '0846', 'Tô Thị Lê', '1980-04-01', 'Nữ', NULL, 36, 'Cao đăng điều dưỡng', NULL, '0978293858', NULL, 'Xóm 6, Thị trấn Quán Hành', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1327, 1, '0833', 'Phan Thị Thủy', '1985-05-05', 'Nữ', NULL, 36, 'Cử nhân điều dưỡng', NULL, '01683964645', NULL, NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1328, 1, '0837', 'Cao Thị Mến', '1988-01-24', 'Nữ', NULL, 36, 'Cao đăng điều dưỡng', NULL, '0983569743', NULL, NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1329, 1, '1769', 'Cao Thị Hồng Thúy', '1996-06-27', 'Nữ', NULL, 36, 'Cao đăng điều dưỡng', NULL, '0385752386', NULL, NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1330, 1, '1334', 'Lê Thị Huệ', '1991-05-16', 'Nữ', NULL, NULL, 'Bác sĩ', NULL, '0976971400', 'dr.lehue0610@gmail.com', 'Xóm 17 - Xã Nghi Phú - Thành phố Vinh - Tỉnh Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1331, 1, '1468', 'Dương Thị Sương', '1993-10-26', 'Nữ', NULL, NULL, 'CN ĐD', NULL, '0358992069', NULL, 'Xã Xuân Lâm - Huyện Nam Đàn - Tỉnh Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1332, 1, '1709', 'Lê Thị Hồng', '1994-01-04', 'Nữ', NULL, NULL, 'Cao đăng điều dưỡng', NULL, '0388369900', NULL, NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1333, 1, '1219', 'Lê Thị Nga', '1993-12-12', 'Nữ', NULL, NULL, 'Cao đăng điều dưỡng', NULL, '0377398205', NULL, 'Xóm 15 - Xã Phúc Thành - Huyện Yên Thành - Tỉnh Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1334, 1, '1145', 'Nguyễn Thị Dung', '1989-05-21', 'Nữ', NULL, NULL, 'Cao đăng điều dưỡng', NULL, '0932486246', NULL, 'Xã Nghi Ân - Thành phố Vinh - Tỉnh Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1335, 1, '0835', 'Nguyễn Hồ Mỹ Hà', '1987-11-14', 'Nữ', NULL, NULL, 'Cao đăng điều dưỡng', NULL, '0971351487', NULL, 'Khối 13, P. Cửa Nam, Tp Vinh, Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1336, 1, '1030', 'Phan Ngọc Khóa', '1980-10-26', 'Nam', 'Trưởng khoa Phẫu thuật thẩm mỹ', NULL, 'BSCK II', 'Chấn thương chỉnh hình', '989566299', 'tr.khoaphan@gmail.com', '215 lê lợi tp vinh', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1337, 1, '1049', 'Lê Trọng Tiến', '1988-10-05', 'Nam', NULL, NULL, 'Thạc sĩ', 'Phẫu thuật tạo hình', '945018678', NULL, 'Số nhà 16 Đinh Công Tráng-Lê Mao-Vinh-NA', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1338, 1, '1607', 'Nguyễn Đức Hùng', '1995-06-27', 'Nam', NULL, NULL, 'Bác sĩ', NULL, '0368195158', 'dr.hungnguyenduc@gmail.com', NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1339, 1, '1466', 'Nguyễn Thị Hải', '1994-04-20', 'Nữ', 'Phụ trách điều hành Đ DT', NULL, 'CN ĐD', NULL, '0986297971', 'nguyenhailinh37@gmail.com', 'hoàng mai nghệ an', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1340, 1, '1224', 'Trần Thị Tú Oanh', '1993-04-21', 'Nữ', NULL, NULL, 'Cao đăng điều dưỡng', NULL, '0346532547', 'tuoanhtran.ykv@gmail.com', 'phúc thành- yên thành- nghệ an', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1341, 1, '1194', 'Trần Thị Huyền Trang', '1991-10-20', 'Nữ', NULL, 76, 'Cử nhân TCNH', NULL, '0987441157', 'tranhuyentrang1819@gmail.com', 'Xuân Bắc - Hưng Dũng - Vinh - Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1342, 1, '1239', 'Nguyễn Tiến Đạt', '1983-10-07', 'Nam', NULL, 76, 'CN ĐD', NULL, '0966045458', 'nguyentiendat7101983@gmail.com', NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1343, 1, '1404', 'Đậu Giang Sơn', '1993-06-24', 'Nam', NULL, NULL, 'Bác sĩ', NULL, '0938397209', 'Simbavmmu@gmail.com', 'Xuân Hà - Nghi Xuân - Hà Tĩnh', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1344, 1, '0827', 'Bùi Tiến Hoàn', '1984-11-03', 'Nam', 'Phó khoa Vi rút - Ký sinh trùng', 76, 'Thạc sĩ', 'Truyền nhiễm và các bệnh nhiệt đới', '0913734684', 'drbuihoan84@gmail.com', 'Khối 14 - Phường Trường Thi - Thành phố Vinh - Tỉnh Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1345, 1, '1603', 'Nguyễn Đình Hải', '1991-11-20', 'Nam', NULL, NULL, 'Bác sĩ', NULL, '0984635800', 'drhaihai@gmail.com', NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1346, 1, '1600', 'Lê Đức An', '1995-06-22', 'Nam', NULL, NULL, 'Bác sĩ', NULL, '0352555282', NULL, NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1347, 1, '1892', 'Nguyễn Thị Huyền', '1996-11-26', 'Nữ', NULL, NULL, 'Bác sĩ', NULL, '0965664237', 'Huyen261196@gmail.com', NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1348, 1, '0825', 'Quế Anh Trâm', '1970-12-05', 'Nam', 'Giám đốc Trung tâm Bệnh nhiệt đới, Trưởng khoa vi rút - Ký sinh trùng', 76, 'Tiến sĩ', 'Truyền nhiễm và các bệnh nhiệt đới', '0904568569', 'tramlien@gmail.com', 'Khối 5 - Phường Hà Huy Tập - Thành phố Vinh - Tỉnh Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1349, 1, '0731', 'Nguyễn Thị Thủy', '1989-01-28', 'Nữ', NULL, 76, 'Thạc sĩ sinh học', NULL, '0975636196', 'nguyenthuydkna@gmail.com', 'Nghi Phú - TP Vinh - Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1350, 1, '1448', 'Lữ Thị Kim Chi', '1992-01-13', 'Nữ', NULL, 31, 'CN ĐD', NULL, '0916299323', 'luthikimchi92@gmail.com', 'Nghi Phú- Vinh-NA', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1351, 1, '1698', 'Võ Thị Hoài Thu', '1997-03-01', 'Nữ', NULL, NULL, 'Cử nhân điều dưỡng', NULL, '0981628943', NULL, NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1352, 1, '1483', 'Nguyễn Thị Vân', '1996-08-17', 'Nữ', NULL, 30, 'CN ĐD', NULL, '0969950792', 'Nguyenthivan170896@gmail.com', 'Nghi  Lộc - Nghệ an', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1353, 1, '1183', 'Nguyễn Thị Phương Mai', '1990-03-15', 'Nữ', NULL, 27, 'Cao đăng điều dưỡng', NULL, '0989839541', NULL, NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1354, 1, '1267', 'Lê Thị Khánh Ly', '1992-08-29', 'Nữ', NULL, 76, 'Cao đăng điều dưỡng', NULL, '0971448881', NULL, 'Trung Định - P. Hưng Dũng - TP.Vinh - Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1355, 1, '1729', 'Trần Thị Hoa', '1995-07-17', 'Nữ', NULL, 76, 'Cao đăng điều dưỡng', NULL, '0981119445', 'tranthihoa201710@gmail.com', NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1356, 1, '1722', 'Nguyễn Thị Hạnh', '1997-10-22', 'Nữ', NULL, 25, 'Cao đăng điều dưỡng', NULL, '0396930842', 'hanhbep97@gmail.com', NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1357, 1, '1744', 'Nguyễn Thị Thủy', '1993-05-25', 'Nữ', NULL, 14, 'Cao đăng điều dưỡng', NULL, '0377151979', 'nguyenanhthoa1912@gmail.com', NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1358, 1, '0649', 'Nguyễn Thị Vân Giang', '1985-11-30', 'Nữ', NULL, 47, 'Cao đăng điều dưỡng', NULL, '0915687090', NULL, 'K2 P quán bàu vinh', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1359, 1, '1159', 'Nguyễn Thị Hằng', '1990-07-27', 'Nữ', NULL, 56, 'Cao đăng điều dưỡng', NULL, '0981451269', 'hanghasau.90@gmail.com', 'K.Xuan Trung-Hưng Dũng -Vinh', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1360, 1, '0713', 'Nguyễn Thị Mai Sim', '1982-05-10', 'Nữ', NULL, 76, 'Cao đăng điều dưỡng', NULL, '0977661006', 'maisimkna@gmail.com', 'Khối Xuân nam, Phường Hưng Dũng, TP Vinh, Nghệ AN', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1361, 1, '1188', 'Phan Thị Thái Quyết', '1989-04-13', 'Nữ', NULL, 76, 'Cao đăng điều dưỡng', NULL, '0973434859', 'thaiquyetpha@gmai.com', 'Nghi Pjhus - Vinh', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1362, 1, '1169', 'Nguyễn Thị Ngọc Hoa', '1991-10-09', 'Nữ', NULL, 76, 'Cao đăng điều dưỡng', NULL, '0983095589', NULL, 'Nghi Phong - Vinh', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1363, 1, '1499', 'Nguyễn Thị Thu Thủy', '1994-02-25', 'Nữ', NULL, 76, 'Cao đăng điều dưỡng', NULL, '0976862248', 'tnntlx@gmail.com', 'Xóm 1- Quỳnh Lâm - Quỳnh Lưu - Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1364, 1, '1705', 'Đinh Thị Hòe', '1997-01-15', 'Nữ', NULL, 76, 'Cao đăng điều dưỡng', NULL, '0973410137', NULL, NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1365, 1, '1226', 'Bùi Thị Huyền Linh', '1992-05-08', 'Nữ', NULL, 76, 'Cao đăng điều dưỡng', NULL, '0399407151', NULL, 'Khối Xuân Bắc - Phường Hưng Dũng - Thành phố Vinh - Tỉnh Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1366, 1, '1707', 'Nguyễn Thị Tuyến', '1991-10-11', 'Nữ', NULL, 76, 'Cao đăng điều dưỡng', NULL, '0968563829', NULL, NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1367, 1, '0845', 'Nguyễn Thị Hiền', '1979-06-13', 'Nữ', NULL, 76, 'Cao đăng điều dưỡng', NULL, '01633583416', NULL, 'Phúc Sơn - Anh Sơn -Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1368, 1, '0836', 'Nguyễn Thị Diệu Linh', '1988-09-18', 'Nữ', 'Điều dưỡng trưởng', 76, 'Cử nhân điều dưỡng', NULL, '0975707434', 'datlinh209@gmail.com', 'Xóm 15 - Xã Nghi Phú - Thành phố Vinh - Tỉnh Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1369, 1, '0847', 'Phan Thị Hồng', '1988-07-07', 'Nữ', NULL, 76, 'Cao đăng điều dưỡng', NULL, '0976298887', 'ducnguyen1317@gmail.com', 'Khối Trường Tiến, P. Hưng Bình, Tp Vinh', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1370, 1, '1025', 'Nguyễn Huy Toàn', '1984-09-12', 'Nam', 'Phó khoa Ngoại tổng hợp', NULL, 'Thạc sỹ y học, Bác sỹ nội trú', 'Ngoại khoa', '0946254777', NULL, NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1371, 1, '1120', 'Phạm Minh Tuấn', '1990-10-19', 'Nam', NULL, NULL, 'Thạc sĩ', 'Ngoại khoa', '0981248115', 'tuanpmmd@gmail.com', 'Nghi Phú - Tp Vinh - Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1372, 1, '1343', 'Trần Hồng Quân', '1992-01-09', 'Nam', NULL, NULL, 'Bác sĩ', NULL, '01659729265', NULL, NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1373, 1, '1335', 'Trần Xuân Công', '1992-11-16', 'Nam', NULL, NULL, 'Bác sĩ', NULL, '0962441831', NULL, NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1374, 1, '1895', 'Trần Văn Lộc', '1996-02-27', 'Nam', NULL, NULL, 'Bác sĩ', NULL, '0969131652', 'tranvanloc270296@gmail.com', NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1375, 1, '1417', 'Trần Đạt Bảo Thành', '1994-12-14', 'Nam', NULL, NULL, 'Bác sĩ', NULL, '0868983846', NULL, NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1376, 1, '0938', 'Trần Văn Thông', '1988-10-30', 'Nam', NULL, NULL, 'Thạc sĩ', 'Ngoại khoa', '0969939468', NULL, NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1377, 1, '1574', 'Nguyễn Văn Thủy', '1990-10-25', 'Nam', NULL, NULL, 'bác sĩ NT', 'Ngoại Tiêu hóa', '0349730265', 'nguyenvanthuy2510@gmail.com', 'Số 19/3/Tân Phúc, Hưng Phúc, Vinh, Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1378, 1, '0443', 'Lê Anh Xuân', '1971-07-29', 'Nam', 'Trưởng khoa Ngoại tổng hợp', NULL, 'BSCK II', 'Ths Ngoại khoa', '0912336036', NULL, 'K19 - Nghi Phú - TP Vinh', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1379, 1, '1693', 'Nguyễn Thị Thu Hiền', '1997-07-02', 'Nữ', NULL, NULL, 'Cử nhân điều dưỡng', NULL, '0965262221', NULL, NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1380, 1, '0463', 'Nguyễn Thị Thúy Duyên', '1986-11-07', 'Nữ', NULL, NULL, 'Cao đăng điều dưỡng', NULL, '0987182220', NULL, 'Xuân Trung, Hưng Dũng, Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1381, 1, '0486', 'Nguyễn Thị Thủy', '1985-11-25', 'Nữ', NULL, NULL, 'Cao đăng điều dưỡng', NULL, '01658916920', NULL, NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1382, 1, '0927', 'Nguyễn Thị Hồng Thanh', '1986-02-25', 'Nữ', NULL, NULL, 'Cao đăng điều dưỡng', NULL, '0968013573', NULL, 'Nghi Phú - TP Vinh', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1383, 1, '0489', 'Trần Thị Thủy', '1986-06-20', 'Nữ', NULL, NULL, 'Cao đăng điều dưỡng', NULL, '01672284337', NULL, NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1384, 1, '1500', 'Trần Thu Như', '1997-05-06', 'Nữ', NULL, NULL, 'Cao đăng điều dưỡng', NULL, '0981490336', NULL, NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1385, 1, '0461', 'Phan Hà Phương', '1983-11-08', 'Nữ', 'Điều dưỡng trưởng', NULL, 'Cử nhân điều dưỡng', NULL, '0979287512', 'phanhaphuongcuong@gmail.com', 'Xóm Mậu Lâm, Hưng Lộc, TP Vinh', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1386, 1, '1724', 'Nguyễn Thị Lê', '1996-06-12', 'Nữ', NULL, NULL, 'Cao đăng điều dưỡng', NULL, '03977362380973785207', NULL, NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1387, 1, '1727', 'Lê Thị Hằng', '1998-08-01', 'Nữ', NULL, NULL, 'Cao đăng điều dưỡng', NULL, '0346575577', 'hangmin181998@gmail.com', NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1388, 1, '1760', 'Phan Tuyết Ánh', '1997-01-08', 'Nữ', NULL, NULL, 'Cao đăng điều dưỡng', NULL, '0962300329', NULL, NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1389, 1, '1774', 'Bùi Thị Hạnh', '1996-07-11', 'Nữ', NULL, NULL, 'Cao đăng điều dưỡng', NULL, '0989076503', NULL, NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1390, 1, '1202', 'Lê Văn Tú', '1988-07-09', 'Nam', NULL, NULL, 'Bác sĩ', NULL, '0984084989', 'levantu12a3yha@gmail.com', 'Nghi Kim - Tp Vinh - Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1391, 1, '1373', 'Hồ Duy Tuấn Anh', '1993-10-17', 'Nam', NULL, NULL, 'Bác sĩ', NULL, '0389931793', 'hotuananh725@gmail.com', 'Hưng Dũng- TP Vinh- Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1392, 1, '1414', 'Phạm Quốc Hoàng', '1991-10-01', 'Nam', NULL, NULL, 'Bác sĩ', NULL, '0942582538', 'bsphamquochoang@gmail.com', 'số nhà 25 ngõ 53 Nguyễn Tuấn Thiện', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1393, 1, '1047', 'Lê Thị Hồng Hạnh', '1989-04-29', 'Nữ', NULL, 27, 'Thạc sĩ', 'Ung thư', '0916306916', 'honghanh2904yhn@gmail.com', NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1394, 1, '1630', 'Đặng Thị Huyền Trang', '1994-06-15', 'Nữ', NULL, NULL, 'Bác sĩ', NULL, '0342731594', NULL, NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1395, 1, '1007', 'Trần Huy Kính', '1989-10-01', 'Nam', NULL, NULL, 'Thạc sĩ', 'Ung thư', '0984556712', 'thkinh@gmail.com', 'Hà Huy Tập- Vinh-Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1396, 1, '0449', 'Phan Minh Ngọc', '1977-10-08', 'Nam', 'Phó khoa Ngoại Tổng hợp, PTĐH Ngoại tổng hợp 1', NULL, 'BSCK II', 'Ngoại - Tiêu hóa', '0986070009', NULL, NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1397, 1, '1212', 'Nguyễn Thị Thu Cúc', '1990-10-15', 'Nữ', 'Phụ trách công tác điều dưỡng', NULL, 'CN ĐD', NULL, '0973264177', 'nguyenthucucdhyh1510@gmail.com', 'TP Vinh - Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1398, 1, '1680', 'Nguyễn Thị Hằng', '1996-10-15', 'Nữ', NULL, NULL, 'Cử nhân điều dưỡng', NULL, '0337485747', NULL, NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1399, 1, '1190', 'Trần Thị Dung', '1987-08-05', 'Nữ', NULL, NULL, 'CN ĐD', NULL, '0982211687', 'tranthidung9101987@gmail.com', 'Xóm 12-Nghi Kim-TP Vinh-Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1400, 1, '1189', 'Nguyễn Thị Huyền Trang', '1991-12-22', 'Nữ', NULL, NULL, 'Cao đăng điều dưỡng', NULL, '0977968012', 'trangnho221191@gmail.com', 'Xuân hồng - Nghi xuân - Hà Tĩnh', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1401, 1, '1253', 'Tạ Thị Khánh Huyền', '1992-07-16', 'Nữ', NULL, NULL, 'Cao đăng điều dưỡng', NULL, '0373081419', 'hasan2019@gmail.com', 'Nghi Phú - Tp Vinh - Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1402, 1, '1714', 'Võ Thị Thoa', '1990-08-10', 'Nữ', NULL, NULL, 'Cao đăng điều dưỡng', NULL, '0988576031', 'vothithoa88@gmail.com', NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1403, 1, '0702', 'Trần Nhật Thành', '1983-08-28', 'Nam', 'Phó trưởng khoa, Phụ trách điều hành khoa Dị ứng - Miễn dịch lâm sàng', 69, 'BSCK II', 'Dị ứng - Miễn dịch lâm sàng', '0976617881', 'drthanh2007@gmail.com', 'P. Bắc Hồng, Tx Hồng Lĩnh, Tỉnh Hà Tĩnh', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1404, 1, '1299', 'Hoàng Danh Tân', '1991-10-10', 'Nam', NULL, 69, 'Bác sĩ', NULL, '0979029590', NULL, NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1405, 1, '1397', 'Nguyễn Thị Tuyết', '1993-11-20', 'Nữ', NULL, 69, 'Bác sĩ', NULL, '0393232642', NULL, NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1406, 1, '1398', 'Nguyễn Thị Oanh', '1994-09-02', 'Nữ', NULL, 69, 'Bác sĩ', NULL, '0349591688', 'oanh.xeu@gmail.com', NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1407, 1, '1610', 'Lê Thị Hồng Thúy', '1995-11-12', 'Nữ', NULL, 69, 'Bác sĩ', NULL, '0968078211', 'thuyduc02011996@gmail.com', NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1408, 1, '1452', 'Bùi Thị Minh', '1994-10-04', 'Nữ', NULL, 69, 'CN ĐD', NULL, '0379673864', NULL, NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1409, 1, '1671', 'Nguyễn Thị Hòa', '1995-04-20', 'Nữ', 'Phụ trách điều hành công tác điều dưỡng trưởng', 69, 'CN ĐD', NULL, '963405112', NULL, NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1410, 1, '1675', 'Nguyễn Thị Hà Na', '1996-05-05', 'Nữ', NULL, 31, 'Cử nhân điều dưỡng', NULL, '0368757215', 'nguyenhana050596@gmail.com', NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1411, 1, '0753', 'Nguyễn Diệu Linh', '1985-07-18', 'Nữ', NULL, 69, 'Cao đăng điều dưỡng', NULL, '0362662115', NULL, 'Khôối Xuân Bắc, p. Hưng Dũng, Tp Vinh, Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1412, 1, '1730', 'Nguyễn Thị Hoan', '1997-05-19', 'Nữ', NULL, 69, 'Cao đăng điều dưỡng', NULL, '0359604487', NULL, NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1413, 1, '0033', 'Trần Thị Phương Thúy', '1987-04-10', 'Nữ', NULL, 69, 'Cao đăng điều dưỡng', NULL, '0888883737', 'phanlena2011@gmail.com', 'SN 59, Khối Văn Tiến, P. Hưng Dũng, Tp Vinh', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1414, 1, '0914', 'Nguyễn Thị Thùy Dung', '1990-05-21', 'Nữ', NULL, 69, 'Cao đăng điều dưỡng', NULL, '0349737268', NULL, NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1415, 1, '1810', 'Luyện Đức Hoàng Anh', '1994-05-18', 'Nam', NULL, 43, 'bác sĩ NT', 'Ngoại khoa', '0911168676', 'Hoanganhytb1805@gmail.com', NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1416, 1, '0451', 'Lê Huy Ngọc', '1980-06-06', 'Nam', 'Phó khoa Ngoại thận - tiết niệu', 43, 'Thạc sĩ', 'Ngoại khoa', '0983375855', NULL, NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1417, 1, '0937', 'Nguyễn Cảnh Phong', '1988-03-30', 'Nam', NULL, 43, 'Thạc sĩ', 'Ngoại khoa', '0983928911', 'phongcanhka@gmail.com', 'Cửa Nam - TP Vinh - Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1418, 1, '1210', 'Nguyễn Văn Trường', '1990-12-03', 'Nam', NULL, 43, 'Thạc sĩ', 'Ngoại khoa', '0981179559', 'truongnguyen64@gmail.com', NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1419, 1, '0904', 'Hồ Văn Hoàng', '1985-11-14', 'Nam', 'Phó trưởng khoa Ngoại thận - Tiết niệu', 43, 'bác sĩ NT', 'Ngoại khoa', '0942841706', 'sonhai86@gmail.com', NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1420, 1, '1366', 'Nguyễn Văn Huy', '1992-10-01', 'Nam', NULL, 43, 'Bác sĩ', NULL, '0986700277', 'huyhoadau@gmail.com', NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1421, 1, '1412', 'Tạ Lê Quỳnh', '1994-09-10', 'Nam', NULL, 43, 'Bác sĩ', NULL, NULL, 'toiyeuvietnam.taqhop@gmail.com', NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1422, 1, '1623', 'Nguyễn Quốc Hòa', '1995-02-11', 'Nam', NULL, 43, 'Bác sĩ', NULL, '0866009594', 'yeutinhoc1325@gmail.com', NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1423, 1, '1829', 'Võ Văn Chung', '1993-02-03', 'Nam', NULL, 43, 'Thạc sĩ', 'Ngoại khoa', '0969463168', 'vovanchung93@gmail.com', NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1424, 1, '1410', 'Phạm Văn Quân', '1994-10-20', 'Nam', NULL, 43, 'Bác sĩ', NULL, '0971181702', 'vanquanydhue@gmail.com', NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1425, 1, '1411', 'Chu Văn Tiến', '1994-06-08', 'Nam', NULL, 43, 'Bác sĩ', NULL, '01277169899', 'chutien8694@gmail.com', NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1426, 1, '0448', 'Hồ Trường Thắng', '1980-01-20', 'Nam', NULL, 27, 'Thạc sĩ', 'Ngoại khoa', '0987179916', 'hotruongthang0407@gmail.com', 'P. Hà Huy Tập, Tp Vinh', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1427, 1, '1624', 'Hoàng Quang Định', '1995-09-01', 'Nam', NULL, 43, 'Bác sĩ', NULL, '0978827097', NULL, NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1428, 1, '0442', 'Lê Ngọc Bằng', '1970-10-08', 'Nam', 'Trưởng khoa Ngoại thận - tiết niệu', 43, 'BSCK II', 'Ngoại - Tiết niệu', '0912479269', 'ngocbangna@gmail.com', 'Khối Tân Hoà, P Hà Huy Tập, Tp Vinh , Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1429, 1, '1462', 'Hoàng Diệu Ly', '1995-08-07', 'Nữ', NULL, 43, 'CN ĐD', NULL, '0988549769', NULL, NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1430, 1, '1664', 'Lô Thị Nga', '1995-08-14', 'Nữ', NULL, 43, 'Cử nhân điều dưỡng', NULL, '0375810095', 'Ngaykv14081995@gamil.com', NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1431, 1, '1689', 'Mai Thị Hồng Duyên', '1997-02-08', 'Nữ', NULL, 43, 'Cử nhân điều dưỡng', NULL, '0971084034', NULL, NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0);
INSERT INTO `dm_nhan_vien` (`id`, `benh_vien_id`, `ma_nv`, `ho_ten`, `ngay_sinh`, `gioi_tinh`, `chuc_danh`, `khoa_phong_id`, `trinh_do`, `chuyen_khoa`, `dien_thoai`, `email`, `dia_chi`, `trang_thai`, `ngay_tao`, `ngay_cap_nhat`, `nguoi_tao`, `nguoi_cap_nhat`, `da_xoa`) VALUES
(1432, 1, '1521', 'Nguyễn Thị Tú Anh', '1996-06-08', 'Nữ', NULL, 43, 'Cao đăng điều dưỡng', NULL, '01647507868', 'Chingoc0o0@gmail.com', NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1433, 1, '0485', 'Hồ Văn Trung', '1987-03-31', 'Nam', NULL, 43, 'Cao đăng điều dưỡng', NULL, '01698916640', NULL, 'Xóm 11, Xã hưng Lộc, Tp Vinh, Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1434, 1, '0490', 'Nguyễn Thị Thu Mai', '1987-08-12', 'Nữ', NULL, 43, 'Cao đăng điều dưỡng', NULL, '0974135254', NULL, 'Xóm 14, Nghi Phú, TP Vinh, Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1435, 1, '0466', 'Lê Thị Thu Hoài', '1988-03-24', 'Nữ', NULL, 43, 'Cao đăng điều dưỡng', NULL, '0917324866', NULL, 'Hưng Dũng, Tp Vinh, Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1436, 1, '0171', 'Nguyễn Thị Minh', '1970-12-13', 'Nữ', NULL, 16, 'LĐ PT', NULL, '0377061756', NULL, 'Nghi Lâm - Nghi Lộc - Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1437, 1, '0156', 'Nguyễn Thị Thử', '1975-06-02', 'Nữ', NULL, 16, 'LĐ PT', NULL, '0943168172', 'phamp213@gmail.com', 'Hưng Dũng - Vinh - Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1438, 1, '1242', 'Trần Thị Mai Linh', '1991-07-04', 'Nữ', NULL, 43, 'Cao đăng điều dưỡng', NULL, '0975734986', 'hoangdieuly7895@gmail.com', NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1439, 1, '0471', 'Lê Phạm Trà', '1988-08-06', 'Nam', 'Điều dưỡng trưởng', 43, 'Cử nhân điều dưỡng', NULL, '0915065102', 'phamtrale@gmail.com', 'Nghi Hưng, Nghi Lộc, Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1440, 1, '1713', 'Nguyễn Thị Lý', '1994-05-24', 'Nữ', NULL, 43, 'Cao đăng điều dưỡng', NULL, '0981912042', 'nguyenthilyti230295@gmaiulo.com', NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1441, 1, '1156', 'Nguyễn Thị Nga', '1990-03-22', 'Nữ', NULL, 43, 'Cao đăng điều dưỡng', NULL, '01649736401', 'ngocngoainieu@gmail.com', NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1442, 1, '0482', 'Nguyễn Thị Thu Hiền', '1984-09-13', 'Nữ', NULL, 43, 'CN ĐD', NULL, '01237504527', NULL, 'Khối Xuân nam, Phường Hưng Dũng, TP Vinh, Nghệ AN', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1443, 1, '0483', 'Nguyễn Đình Chinh', '1983-09-12', 'Nam', NULL, 43, 'Cao đăng điều dưỡng', NULL, '0989818567', '0989818567abc@gmail.com', 'Xóm Xuân Hoa, Xã Nghi Đức, Tp Vinh', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1444, 1, '0488', 'Nguyễn Thị An', '1987-08-17', 'Nữ', NULL, 43, 'Cao đăng điều dưỡng', NULL, '0972942567', 'nguyenthian170887@gmail.com', 'Mãu Lâm, Hưng Lộc, Tp Vinh, Nghệ an', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1445, 1, '1164', 'Cao Thị Thiên Thơ', '1992-10-12', 'Nữ', NULL, 42, 'ĐDTH', NULL, '0964969700', 'Caothientho12101992@gmail.com', 'Hưng Dũng - Tp Vinh- Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1446, 1, '0446', 'Đặng Đình Khoa', '1993-05-23', 'Nam', 'Phó phòng Kế hoạch tổng hợp', 42, 'Thạc sĩ', 'Ngoại khoa', '0945614832', 'bskhoa115@gmail.com', 'Số nhà 12, ngõ 128, Lê Hồng Phong, Tp Vinh, Nghệ an', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1447, 1, '0444', 'Tăng Huy Cường', '1977-12-08', 'Nam', 'Trưởng khoa Ngoại tiêu hóa', 42, 'BSCK II', 'Ngoại - Tiêu hóa', '904535886', 'tanghuycuong@hotmail.com', 'K4 - Trường Thi - Vinh - Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1448, 1, '0939', 'Nguyễn Tiến Thành', '1988-05-10', 'Nam', NULL, 27, 'Thạc sĩ', 'Ngoại khoa', '0945100588', 'monitor.k39b@gmail.com', 'Nghi Ân - Vinh - Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1449, 1, '1413', 'Phạm Đình Thịnh', '1994-01-02', 'Nam', NULL, 42, 'Bác sĩ', NULL, '0387037453', 'Phamdinhthinh0102@gmail.com', 'Tăng Thành - Yên Thành - Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1450, 1, '1627', 'Lưu Đức Sơn', '1995-07-01', 'Nam', NULL, 42, 'Bác sĩ', NULL, '0335827292', 'luuducson95@gmail.com', NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1451, 1, '1626', 'Nguyễn Sỹ Thịnh', '1995-10-24', 'Nam', NULL, 42, 'Bác sĩ', NULL, '0974864908', 'Thinhnguyen1319@gmail.com', NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1452, 1, '1830', 'Phạm Nguyên Hãn', '1996-08-24', 'Nam', NULL, 42, 'Bác sĩ', NULL, '0967206999', 'phamnguyenhann@gmail.com', NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1453, 1, '1831', 'Nguyễn Đình Hiếu', '1996-03-18', 'Nam', NULL, 42, 'Bác sĩ', NULL, '0963188391', 'hieuhamhochoi183@gmail.com', NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1454, 1, '1625', 'Xồng Bá Dìa', '1991-08-19', 'Nam', NULL, 42, 'Bác sĩ', NULL, '0367442165', 'Xongbadia91@gmail.com', NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1455, 1, '0441', 'Đoàn Phong Lê', '1973-04-02', 'Nam', 'Phó khoa Ngoại tiêu hóa', 42, 'BSCK II', 'Ngoại - Tiêu hóa', '0912124088', 'doanphongle1973@gmail.com', 'SN 7 - K. Quang Tiến - Hưng Bình - TP Vinh', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1456, 1, '1471', 'Lương Thị Lam', '1992-08-02', 'Nữ', NULL, 42, 'CN ĐD', NULL, '0838139693', 'thilam9262@gmail.com', 'xã Nghi Phú-TP Vinh-Tỉnh Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1457, 1, '1473', 'Hoàng Thị Mai Thùy', '1994-06-10', 'Nữ', NULL, 42, 'CN ĐD', NULL, '0982893717', 'hoangmaithuy1994hoanglinh@gmail.com', 'Xóm 13- Xã Nghi Phú- Tp Ving- Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1458, 1, '1480', 'Đinh Thị Hằng', '1996-01-06', 'Nữ', NULL, 42, 'CN ĐD', NULL, '0394047011', 'dinhhanght96@gmail.com', 'Xã Nghi Phú-tp Vinh-Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1459, 1, '1665', 'Lê Thị Trà', '1997-12-21', 'Nữ', NULL, 42, 'Cử nhân điều dưỡng', NULL, '0366753748', NULL, NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1460, 1, '1696', 'Trần Thị Quỳnh Thơ', '1994-09-01', 'Nữ', NULL, 42, 'Cử nhân điều dưỡng', NULL, '0346789456', 'levantu12a3yha@gmail.com', NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1461, 1, '0465', 'Hoàng Thị Sáng', '1986-02-05', 'Nữ', NULL, 42, 'Cao đăng điều dưỡng', NULL, '0388125286', NULL, 'Ngõ 34, đường Phùng Khắc Khoan, Tp Vinh, Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1462, 1, '0669', 'Nguyễn Thị Hà', '1985-03-06', 'Nữ', 'Phụ trách điều hành công tác điều dưỡng trưởng', 42, 'Cao đăng điều dưỡng', NULL, '0904585126', 'nguyenthiha@gmail.com', 'Xóm 14, Xã Hưng Lộc, Tp Vinh, Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1463, 1, '0462', 'Hồ Thị Ái Miên', '1985-07-25', 'Nữ', NULL, 42, 'Cao đăng điều dưỡng', NULL, '0971528785', NULL, 'Xóm 11 Hưng Lộc, Tp Vinh, Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1464, 1, '1734', 'Lương Thị Tâm', '1994-08-24', 'Nữ', NULL, 42, 'Cao đăng điều dưỡng', NULL, '0968121597', NULL, NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1465, 1, '1487', 'Lê Phương Thảo', '1992-12-22', 'Nữ', NULL, 42, 'Cao đăng điều dưỡng', NULL, '0904998186', 'thaole221292@gmail.com', 'Nghi Đức - Vinh - Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1466, 1, '1764', 'Nguyễn Lê Na', '1997-05-18', 'Nữ', NULL, 42, 'Cao đăng điều dưỡng', NULL, '0393238708', NULL, NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1467, 1, '1510', 'Trần Thị Mỹ Hạnh', '1996-09-22', 'Nữ', NULL, 42, 'Cao đăng điều dưỡng', NULL, '0984514708', 'hanhtrana123@gmail.com', 'Kim Liên-Nam Đàn- Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1468, 1, '1710', 'Trần Thị Hương', '1992-06-05', 'Nữ', NULL, 42, 'Cao đăng điều dưỡng', NULL, '09457396750366400647', NULL, NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1469, 1, '1162', 'Lê Thị Lam Trà', '1990-06-19', 'Nữ', NULL, 42, 'Cao đăng điều dưỡng', NULL, '0987891906', NULL, 'nghi thái- nghi lộc -nghệ an', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1470, 1, '1711', 'Bùi Thị Hằng', '1995-02-11', 'Nữ', NULL, 42, 'Cao đăng điều dưỡng', NULL, '0914590162', NULL, NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1471, 1, '0915', 'Nguyễn Thị Hằng', '1991-09-25', 'Nữ', NULL, 42, 'Cao đăng điều dưỡng', NULL, '0966450278', NULL, 'Đông Vĩnh - Tp Vinh - Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1472, 1, '0499', 'Trần Trung Kiên', '1985-05-17', 'Nam', 'Phó khoa Phẫu thuật thần kinh cột sống', 25, 'BSCK II', 'Ngoại - Thần kinh và Sọ não', '0985797029', 'drkien.pttk@gmail.com', 'Hưng Lộc - TP Vinh - Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1473, 1, '0962', 'Phạm Trọng Nam', '1985-05-12', 'Nam', NULL, 25, 'Thạc sĩ', 'Ngoại khoa', '0981961862', 'phamtrongnam.pttk@gmail.com', 'Hưng Dũng - TP Vinh - Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1474, 1, '1133', 'Ngô Văn Thành', '1989-12-09', 'Nam', NULL, 25, 'Thạc sĩ', 'Ngoại khoa', '0974577767', 'drthanh.hmu@gmail.com', 'Hưng Lộc - TP Vinh - Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1475, 1, '1833', 'Trương Quốc Phong', '1996-12-18', 'Nam', NULL, 25, 'Bác sĩ', NULL, '0389064228', 'quocphong201@gmail.com', NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1476, 1, '1645', 'Phạm Ngọc Hoàng', '1994-07-02', 'Nam', NULL, 25, 'Bác sĩ', NULL, '0971513198', 'phamhoangbsi@gmail.com', NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1477, 1, '0500', 'Nguyễn Hoàng Dương', '1985-02-02', 'Nam', NULL, 27, 'BSCK II', 'Ngoại - Thần kinh và Sọ não', '0945545368', 'nhduong.dkna@gmail.com', 'X 15- Nghi Phú -TP Vinh', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1478, 1, '1643', 'Nguyễn Vinh Hiển', '1994-01-25', 'Nam', NULL, 25, 'Bác sĩ', NULL, '0987999787', 'vinhhienqy@gmail.com', NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1479, 1, '1364', 'Nguyễn Đình Đức', '1992-10-16', 'Nam', NULL, 25, 'Bác sĩ', NULL, '374005260', 'bsducpttkna@gmail.com', 'Minh Sơn - Đô Lương - Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1480, 1, '1646', 'Nguyễn Trần Tiến', '1995-05-06', 'Nam', NULL, 25, 'Bác sĩ', NULL, '09047819950773396999', 'nguyentrantien0605@gmail.com', NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1481, 1, '0502', 'Nguyễn Hồng Việt', '1985-03-08', 'Nam', NULL, 25, 'Thạc sĩ', 'Ngoại thần kinh - sọ não', '0978007102', 'drviet.vn@gmail.com', 'Hà Huy Tập - TP Vinh - Nghệ AN', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1482, 1, '0496', 'Hoàng Kim Tuấn', '1979-06-07', 'Nam', 'Phó khoa Phẫu thuật thần kinh cột sống', 25, 'Thạc sĩ', 'Ngoại Thần kinh và Sọ não', '0902007679', 'hoangkimtuan.pttk@gmail.com', 'K1 Quán Bàu - TP Vinh - Nghệ AN', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1483, 1, '0494', 'Hoàng Hoa Thám', NULL, 'Nam', 'Trưởng khoa Phẫu thuật thần kinh cột sống', 25, 'BSCK II', 'Ngoại thần kinh - sọ não', '0912828448', 'drtham.2012@gmail.com', 'Xóm 12, Hưng Lộc, Tp Vinh, Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1484, 1, '1260', 'Lại Thị Huyền Trang', '1990-02-09', 'Nữ', NULL, 57, 'CN ĐD', NULL, '0349870348', 'laithihuyentrang13232@gmail.com', 'Nghi Phú - TP Vinh - Nghệ AN', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1485, 1, '1479', 'Dương Thị Huyền', '1991-11-12', 'Nữ', NULL, 25, 'CN ĐD', NULL, '0349733724', 'hd3424719@gmail.com', 'Vĩnh Sơn - Anh Sơn - Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1486, 1, '0283', 'Nguyễn Thị Thúy Ngân', '1977-11-20', 'Nữ', NULL, 16, 'LĐ PT', NULL, '0944584542', 'bstranphuong@gmail.com', 'Khối Văn Trung, P. Hưng Dũng, Tp Vinh', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1487, 1, '0509', 'Hồ Thị Lê', '1985-09-28', 'Nữ', NULL, 25, 'Cao đăng điều dưỡng', NULL, '0979298108', 'hothile28091985@gmail.com', 'Xuân Nam - Hưng Dũng -TP Vinh', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1488, 1, '0529', 'Nguyễn Thị Hạnh', '1989-08-25', 'Nữ', NULL, 25, 'Cao đăng điều dưỡng', NULL, '0335856907', 'hanhpttk89@gmail.com', 'X 16 - Diễn Lộc - Diễn Châu -Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1489, 1, '1214', 'Thái Thị Hoa', '1992-12-26', 'Nữ', NULL, 76, 'Cao đăng điều dưỡng', NULL, '0966051945', 'thaihoahndk@gmail.com', 'Hưng Lộc - TP Vinh - Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1490, 1, '0530', 'Hoàng Thị Nguyệt', '1988-11-15', 'Nữ', NULL, 25, 'Cao đăng điều dưỡng', NULL, '0962854238', 'hanctchpro@gmail.com', 'Diễn Phú - Diễn Châu - Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1491, 1, '0531', 'Nguyễn Thị Thu Thủy', '1989-11-20', 'Nữ', NULL, 57, 'Cao đăng điều dưỡng', NULL, '0983700376', 'diepkhue2017@gmail.com', 'Xóm 19 - Nghi Văn - Nghi Lộc -Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1492, 1, '0528', 'Nguyễn Văn Khanh', '1988-12-20', 'Nam', NULL, 25, 'Cao đăng điều dưỡng', NULL, '0972942503', 'khanhpttk@gmail.com', 'X7 - Thanh Chi - Thanh Chương -Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1493, 1, '0525', 'Nguyễn Thị Hòa', '1986-02-10', 'Nữ', NULL, 25, 'Cao đăng điều dưỡng', NULL, '0949941234', 'hoaru1986@gmail.com', 'Xuân Bắc - Hưng Dũng -TP Vinh', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1494, 1, '1179', 'Tô Duy Tài Đức', '1991-09-23', 'Nam', NULL, 25, 'Cao đăng điều dưỡng', NULL, '0978183586', 'duyduc2309@gmail.com', 'Hà Huy Tập - TP Vinh - Nghệ AN', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1495, 1, '1223', 'Đậu Thị Hương', '1993-04-28', 'Nữ', NULL, 25, 'Cao đăng điều dưỡng', NULL, '0365687103', 'namhqn@gmail.com', 'Hưng Lộc - TP Vinh - Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1496, 1, '1719', 'Đoàn Thị Nam', '1997-10-20', 'Nữ', NULL, 25, 'Cao đăng điều dưỡng', NULL, '0329834798', 'namp95799@gmail.com', NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1497, 1, '0532', 'Nguyễn Thị Liễu', '1989-07-12', 'Nữ', NULL, 25, 'Cao đăng điều dưỡng', NULL, '0989493023', 'nguyenthilieu120789@gmail.com', 'SN 15/6 Nguyễn Du, K11 Bến Thuỷ, TP Vinh', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1498, 1, '0172', 'Nguyễn Thị Thắng', '1972-10-05', 'Nữ', NULL, 16, 'LĐ PT', NULL, '0914646449', 'maithanhhuyen92@gmail.com', 'Đông Vĩnh - Vinh - Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1499, 1, '0174', 'Trần Thùy Lam', '1975-10-19', 'Nữ', NULL, 16, 'LĐ PT', NULL, '0948609536', NULL, 'Hưng Phúc - Vinh - Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1500, 1, '1225', 'Trần Văn Khánh', '1990-12-14', 'Nam', NULL, 14, 'CN ĐD', NULL, '0976963189', NULL, NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1501, 1, '0993', 'Văn Huy Linh', '1988-07-05', 'Nam', NULL, 14, 'BSCK I', 'Ngoại khoa', '0979884228', 'vanhuylinh77882015@mail.com', 'Quỳnh Lộc - Quỳnh Lưu - Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1502, 1, '1384', 'Trần Văn Quân', '1987-08-17', 'Nam', 'Phó trưởng khoa Chấn thương - Chỉnh hình', 14, 'BSCK II', 'Chấn thương chỉnh hình', '0904775788', 'tommydr87@gmail.com', 'Khối 2, Quán Bàu, TP Vinh, Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1503, 1, '0987', 'Trần Cương', '1988-05-07', 'Nam', NULL, 27, 'BSCK II', 'Chấn thương chỉnh hình', '0978263334', NULL, NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1504, 1, '1392', 'Hồ Văn Hưng', '1994-10-15', 'Nam', NULL, 14, 'Bác sĩ', NULL, '0385346634', 'hunghovan94na@gmail.com', 'Quỳnh Lương, Quỳnh Lưu, Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1505, 1, '1358', 'Đặng Phi Dương', '1992-06-30', 'Nam', NULL, 14, 'Bác sĩ', NULL, '0339152542', 'dangduong92@gmail.com', 'P. Trường Thi, Vinh, Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1506, 1, '1327', 'Nguyễn Kim Nghĩa', '1991-10-09', 'Nam', NULL, 14, 'BSCK I', 'Ngoại khoa', '0985050910', NULL, 'Trường Sơn, Đức Thọ, Hà Tĩnh', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1507, 1, '1834', 'Mai Duy Đức', '1996-03-20', 'Nam', NULL, 14, 'Bác sĩ', NULL, '0965159963', 'duyducmai96@gmail.com', NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1508, 1, '1836', 'Hoàng Mạnh Cường', '1996-04-05', 'Nam', NULL, 14, 'Bác sĩ', NULL, '0969032766', 'hmcuong1123@gmail.com', NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1509, 1, '1839', 'Nguyễn Phan Khánh', '1995-01-30', 'Nam', NULL, 14, 'Bác sĩ', NULL, '0986469992', 'khanhnguyenphan94bkdu@gmail.com', NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1510, 1, '1838', 'Nguyễn Anh Tuấn', '1996-03-16', 'Nam', NULL, 14, 'Bác sĩ', NULL, '0979602397', 'drtuankyanh1996@gmail.com', NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1511, 1, '0495', 'Nguyễn Đức Vương', NULL, 'Nam', 'Trưởng khoa Chấn thương chỉnh hình', 14, 'Tiến sĩ', 'Ngoại khoa', '0966767888', 'dr.ducvuong@gmail.com', 'Khối Yên Hoà, Phường Hà Huy Tập, Tp Vinh', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1512, 1, '1688', 'Trần Thị Dung', '1993-11-09', 'Nữ', NULL, 57, 'Cử nhân điều dưỡng', NULL, '0965580903', 'trandungbina1994@gmail.com', NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1513, 1, '1071', 'Nguyễn Quốc Trường', '1990-05-08', 'Nam', NULL, 14, 'CN ĐD', NULL, '902125679', 'quoctruongub.na@gmail.com', 'Nghi vạn - Nghi lộc - Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1514, 1, '1504', 'Nguyễn Thị Thanh', '1993-10-14', 'Nữ', NULL, 14, 'Cao đăng điều dưỡng', NULL, '0985258487', 'Thanhnguyen10.93@gmail.com', NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1515, 1, '0666', 'Hoàng Thị Kim Ngân', '1983-02-02', 'Nữ', NULL, 14, 'Cao đăng điều dưỡng', NULL, '0973470666', 'hoangngan22.83@gmail.com', 'Xóm Xuân Trung, Nghi Đức, Tp Vinh, Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1516, 1, '1146', 'Nguyễn Thị Thu Hà', '1991-02-16', 'Nữ', NULL, 14, 'Cao đăng điều dưỡng', NULL, '0945194517', 'habaobinh16021991@gmail.com', 'Xóm 2 Nghi Liên tp vinh', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1517, 1, '0877', 'Lê Thị Tâm', '1984-03-09', 'Nữ', NULL, 14, 'Cao đăng điều dưỡng', NULL, '0973646545', 'Tamnguyenthuy84@gmail.com', 'Khối 17 Phường Trường Thi, Tp Vinh, Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1518, 1, '1505', 'Bùi Thị Hoài', '1994-08-18', 'Nữ', NULL, 14, 'Cao đăng điều dưỡng', NULL, '0978835669', 'buihoai.18894@gmai.com', 'Nghi Thái Nghi lộc', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1519, 1, '0533', 'Nguyễn Thị Nhật', '1987-10-10', 'Nữ', NULL, 14, 'Cao đăng điều dưỡng', NULL, '0948089588', 'nhatmah101087@gmai.com', 'K 10 -TT Hưng Nguyên -Hưng Nguyên -Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1520, 1, '1519', 'Nguyễn Thế Anh Sơn', '1993-09-22', 'Nam', NULL, 14, 'Cao đăng điều dưỡng', NULL, '0988939836', 'anhson22091993@gmail.com', 'khhois 11 - phường quán bàu', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1521, 1, '0185', 'Phạm Thị Thanh Thủy', '1989-12-19', 'Nữ', NULL, 14, 'Cao đăng điều dưỡng', NULL, '01692859448', NULL, 'Khối 7, Trung Đô, Tp Vinh', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1522, 1, '1773', 'Nguyễn Thị Hà', '1995-11-28', 'Nữ', NULL, 14, 'Cao đăng điều dưỡng', NULL, '0969420769', NULL, NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1523, 1, '1221', 'Tô Phương Thảo', '1991-01-05', 'Nữ', NULL, 14, 'Cao đăng điều dưỡng', NULL, '0969012489', 'tophuongthaotk123@gmail.com', 'K14 Phường Trường Thi', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1524, 1, '1745', 'Nguyễn Thị Thu Trang', '1995-06-19', 'Nữ', NULL, 14, 'Cao đăng điều dưỡng', NULL, '0833440201', NULL, NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1525, 1, '0513', 'Đậu Hoàng Mạnh', '1984-11-10', 'Nam', NULL, 14, 'Cao đăng điều dưỡng', NULL, '0984373398', NULL, 'hưng đạo hưng nguyên', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1526, 1, '1930', 'Nguyễn Phan Chương', '1994-08-15', 'Nam', NULL, 14, 'bác sĩ NT', 'Ngoại khoa', NULL, 'Drchuongnp@gmail.com', NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1527, 1, '1835', 'Nguyễn Việt Thành', '1995-06-22', 'Nam', NULL, 78, 'Bác sĩ', NULL, '0972624265', 'bsdk.vietthanh@gmail.com', NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1528, 1, '1597', 'Trịnh Văn Thông', '1980-10-10', 'Nam', 'Trưởng khoa Bỏng', 78, 'Tiến sĩ', 'Ngoại khoa', '0968223068', 'thongmedical@gmail.com', '76 Trần Thủ Độ - Vinh - Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1529, 1, '1393', 'Đinh Xuân Chương', '1994-05-21', 'Nam', NULL, 78, 'Bác sĩ', NULL, '0946943567', 'bong.hong.tang.em.xch@gmail.com', 'Xuân Hòa , Nam Đàn , Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1530, 1, '1450', 'Đỗ Thị Ngọc', '1990-03-05', 'Nữ', 'Phụ trách điều hành công tác điều dưỡng trưởng', 78, 'CN ĐD', NULL, '0368606247', 'dongoc5390@gmail.com', 'trung hòa , hà huy tập', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1531, 1, '1685', 'Phan Thị Thảo', '1995-10-02', 'Nữ', NULL, 78, 'Cử nhân điều dưỡng', NULL, '0964517268', 'pthao576@gmail.com', NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1532, 1, '0642', 'Lê Quang Đạo', '1986-07-30', 'Nam', NULL, 78, 'Cao đăng điều dưỡng', NULL, '0949355567', 'lequangdao866@gmail.com', 'K14, P. Bến Thuỷ, Tp Vinh, Nghệ An', 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1533, 1, '1731', 'Nguyễn Thị Hường', '1995-03-25', 'Nữ', NULL, 78, 'Cao đăng điều dưỡng', NULL, '0868006885', 'nguyenthihuong25031995@gmail.com', NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1534, 1, '1742', 'Đinh Thị Hồng Nhung', '1994-12-12', 'Nữ', NULL, 78, 'Cao đăng điều dưỡng', NULL, '0966730863', 'nhungdinhh1994@gmail.com', NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1535, 1, '1748', 'Hoàng Thị Ngọc Ánh', '1997-02-20', 'Nữ', NULL, 78, 'Cao đăng điều dưỡng', NULL, '0359802515', 'anhtun260297@gmail.com', NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0),
(1536, 1, '1250', 'Lã Thị Phương Thảo', '1993-12-23', 'Nữ', NULL, 78, 'Cao đăng điều dưỡng', NULL, '0981473654', NULL, NULL, 1, '2026-05-22 08:59:29', '2026-05-22 08:59:29', 0, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `dm_nhat_ky_he_thong`
--

CREATE TABLE `dm_nhat_ky_he_thong` (
  `id` int(11) NOT NULL,
  `nguoi_dung_id` int(11) DEFAULT NULL,
  `module` varchar(20) DEFAULT NULL,
  `hanh_dong` varchar(200) NOT NULL,
  `bang_lien_quan` varchar(100) DEFAULT NULL,
  `id_lien_quan` int(11) DEFAULT NULL,
  `noi_dung_thay_doi` text DEFAULT NULL,
  `dia_chi_ip` varchar(50) DEFAULT NULL,
  `thoi_gian` datetime DEFAULT current_timestamp(),
  `ngay_tao` datetime DEFAULT current_timestamp(),
  `ngay_cap_nhat` datetime DEFAULT current_timestamp(),
  `nguoi_tao` int(11) DEFAULT NULL,
  `nguoi_cap_nhat` int(11) DEFAULT NULL,
  `da_xoa` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `dm_nhat_ky_he_thong`
--

INSERT INTO `dm_nhat_ky_he_thong` (`id`, `nguoi_dung_id`, `module`, `hanh_dong`, `bang_lien_quan`, `id_lien_quan`, `noi_dung_thay_doi`, `dia_chi_ip`, `thoi_gian`, `ngay_tao`, `ngay_cap_nhat`, `nguoi_tao`, `nguoi_cap_nhat`, `da_xoa`) VALUES
(1, 1, 'HeThong', 'Đăng nhập: admin', 'DM_NGUOI_DUNG', 1, NULL, '127.0.0.1', '2026-04-20 17:45:05', '2026-04-20 17:45:05', '2026-04-20 17:45:05', NULL, NULL, 0),
(2, 1, 'HeThong', 'Đăng nhập: admin', 'DM_NGUOI_DUNG', 1, NULL, '127.0.0.1', '2026-04-21 16:57:49', '2026-04-21 16:57:49', '2026-04-21 16:57:49', NULL, NULL, 0),
(3, 1, 'HeThong', 'Khởi tạo dữ liệu test', NULL, NULL, NULL, '127.0.0.1', '2026-04-21 16:58:42', '2026-04-21 16:58:42', '2026-04-21 16:58:42', NULL, NULL, 0),
(4, 1, 'HeThong', 'Cấu hình phân quyền các nhóm', NULL, NULL, NULL, '127.0.0.1', '2026-04-21 16:58:42', '2026-04-21 16:58:42', '2026-04-21 16:58:42', NULL, NULL, 0),
(5, 1, 'HeThong', 'Đăng nhập: admin', 'DM_NGUOI_DUNG', 1, NULL, '127.0.0.1', '2026-04-21 19:35:37', '2026-04-21 19:35:37', '2026-04-21 19:35:37', NULL, NULL, 0),
(6, 1, 'HeThong', 'Đăng nhập: admin', 'DM_NGUOI_DUNG', 1, NULL, '127.0.0.1', '2026-04-21 22:26:50', '2026-04-21 22:26:50', '2026-04-21 22:26:50', NULL, NULL, 0),
(7, 1, 'HeThong', 'Thêm NV: Nguyễn Văn Đức', 'DM_NHAN_VIEN', 21, NULL, '127.0.0.1', '2026-04-21 22:27:44', '2026-04-21 22:27:44', '2026-04-21 22:27:44', NULL, NULL, 0),
(8, 1, 'HeThong', 'Thêm khoa/phòng: Phòng Công nghệ thông tin', 'DM_KHOA_PHONG', 11, NULL, '127.0.0.1', '2026-04-21 22:29:29', '2026-04-21 22:29:29', '2026-04-21 22:29:29', NULL, NULL, 0),
(9, 1, 'HeThong', 'Thêm BV: Bệnh viện thành phố', 'DM_BENH_VIEN', 2, NULL, '127.0.0.1', '2026-04-21 22:30:15', '2026-04-21 22:30:15', '2026-04-21 22:30:15', NULL, NULL, 0),
(10, 1, 'HeThong', 'Sửa người dùng: nckh.zung', 'DM_NGUOI_DUNG', 6, NULL, '127.0.0.1', '2026-04-21 22:33:14', '2026-04-21 22:33:14', '2026-04-21 22:33:14', NULL, NULL, 0),
(11, 1, 'HeThong', 'Thêm người dùng: locxoai', 'DM_NGUOI_DUNG', 7, NULL, '127.0.0.1', '2026-04-21 22:33:42', '2026-04-21 22:33:42', '2026-04-21 22:33:42', NULL, NULL, 0),
(12, 1, 'HeThong', 'Xóa tạm NV id=21', 'DM_NHAN_VIEN', 21, NULL, '127.0.0.1', '2026-04-21 22:36:03', '2026-04-21 22:36:03', '2026-04-21 22:36:03', NULL, NULL, 0),
(13, 1, 'HeThong', 'Cập nhật phân quyền nhóm id=2', 'DM_PHAN_QUYEN', 2, NULL, '127.0.0.1', '2026-04-21 22:38:00', '2026-04-21 22:38:00', '2026-04-21 22:38:00', NULL, NULL, 0),
(14, 1, 'HeThong', 'Sửa người dùng: locxoai', 'DM_NGUOI_DUNG', 7, NULL, '127.0.0.1', '2026-04-21 22:38:47', '2026-04-21 22:38:47', '2026-04-21 22:38:47', NULL, NULL, 0),
(15, 1, 'HeThong', 'Xóa 0 log cũ hơn 90 ngày', 'DM_NHAT_KY_HE_THONG', NULL, NULL, '127.0.0.1', '2026-04-21 22:39:43', '2026-04-21 22:39:43', '2026-04-21 22:39:43', NULL, NULL, 0),
(16, 1, 'DM_DoiTuongHocVien', 'Cập nhật đối tượng: Bác sĩ', 'DM_DOI_TUONG_HOC_VIEN', 1, NULL, '127.0.0.1', '2026-04-21 23:04:04', '2026-04-21 23:04:04', '2026-04-21 23:04:04', NULL, NULL, 0),
(17, 1, 'DM_HinhThucHoc', 'Cập nhật hình thức: Đi lâm sàng', 'DM_HINH_THUC_HOC', 5, NULL, '127.0.0.1', '2026-04-21 23:04:28', '2026-04-21 23:04:28', '2026-04-21 23:04:28', NULL, NULL, 0),
(18, 1, 'DT_KhoaHoc', 'Cập nhật khóa học: Sản phụ khoa cập nhật', 'DT_KHOA_HOC', 8, NULL, '127.0.0.1', '2026-04-21 23:05:18', '2026-04-21 23:05:18', '2026-04-21 23:05:18', NULL, NULL, 0),
(19, 1, 'HeThong', 'Đăng nhập: admin', 'DM_NGUOI_DUNG', 1, NULL, '127.0.0.1', '2026-04-22 17:18:23', '2026-04-22 17:18:23', '2026-04-22 17:18:23', NULL, NULL, 0),
(20, 1, 'DT_KhoaHocMonHoc', 'Thêm môn \'An toàn người bệnh\' vào khóa \'Sản phụ khoa cập nhật\'', 'DT_KHOA_HOC_MON_HOC', 25, NULL, '127.0.0.1', '2026-04-22 17:52:19', '2026-04-22 17:52:19', '2026-04-22 17:52:19', NULL, NULL, 0),
(21, 1, 'DT_KhoaHocMonHoc', 'Thêm môn \'Cắt túi mật nội soi\' vào khóa \'Sản phụ khoa cập nhật\'', 'DT_KHOA_HOC_MON_HOC', 26, NULL, '127.0.0.1', '2026-04-22 17:52:25', '2026-04-22 17:52:25', '2026-04-22 17:52:25', NULL, NULL, 0),
(22, 1, 'DT_KhoaHocMonHoc', 'Thêm môn \'Hồi sinh tim phổi cơ bản người lớn\' vào khóa \'Sản phụ khoa cập nhật\'', 'DT_KHOA_HOC_MON_HOC', 27, NULL, '127.0.0.1', '2026-04-22 17:52:29', '2026-04-22 17:52:29', '2026-04-22 17:52:29', NULL, NULL, 0),
(23, 1, 'HeThong', 'Đăng nhập: admin', 'DM_NGUOI_DUNG', 1, NULL, '127.0.0.1', '2026-04-22 20:27:33', '2026-04-22 20:27:33', '2026-04-22 20:27:33', NULL, NULL, 0),
(24, 1, 'HeThong', 'Đăng nhập: admin', 'DM_NGUOI_DUNG', 1, NULL, '127.0.0.1', '2026-04-23 17:12:47', '2026-04-23 17:12:47', '2026-04-23 17:12:47', NULL, NULL, 0),
(25, 1, 'HeThong', 'Đăng nhập: admin', 'DM_NGUOI_DUNG', 1, NULL, '127.0.0.1', '2026-04-23 20:32:21', '2026-04-23 20:32:21', '2026-04-23 20:32:21', NULL, NULL, 0),
(26, 1, 'HeThong', 'Sửa HV: Vũ Thị Lan', 'DM_HOC_VIEN', 12, NULL, '127.0.0.1', '2026-04-23 20:45:36', '2026-04-23 20:45:36', '2026-04-23 20:45:36', NULL, NULL, 0),
(27, 1, 'HeThong', 'Đăng nhập: admin', 'DM_NGUOI_DUNG', 1, NULL, '127.0.0.1', '2026-04-23 22:36:28', '2026-04-23 22:36:28', '2026-04-23 22:36:28', NULL, NULL, 0),
(28, 1, 'HeThong', 'Đăng nhập: admin', 'DM_NGUOI_DUNG', 1, NULL, '127.0.0.1', '2026-04-24 16:58:00', '2026-04-24 16:58:00', '2026-04-24 16:58:00', NULL, NULL, 0),
(29, 1, 'HeThong', 'Đăng nhập: admin', 'DM_NGUOI_DUNG', 1, NULL, '127.0.0.1', '2026-04-24 17:09:20', '2026-04-24 17:09:20', '2026-04-24 17:09:20', NULL, NULL, 0),
(30, 1, 'HeThong', 'Đăng nhập: admin', 'DM_NGUOI_DUNG', 1, NULL, '127.0.0.1', '2026-04-24 17:20:26', '2026-04-24 17:20:26', '2026-04-24 17:20:26', NULL, NULL, 0),
(31, 1, 'HeThong', 'Đăng nhập: admin', 'DM_NGUOI_DUNG', 1, NULL, '127.0.0.1', '2026-04-24 20:58:59', '2026-04-24 20:58:59', '2026-04-24 20:58:59', NULL, NULL, 0),
(32, 1, 'HeThong', 'Đăng nhập: admin', 'DM_NGUOI_DUNG', 1, NULL, '127.0.0.1', '2026-04-24 21:01:38', '2026-04-24 21:01:38', '2026-04-24 21:01:38', NULL, NULL, 0),
(33, 1, 'HeThong', 'Đăng nhập: admin', 'DM_NGUOI_DUNG', 1, NULL, '127.0.0.1', '2026-04-24 21:48:18', '2026-04-24 21:48:18', '2026-04-24 21:48:18', NULL, NULL, 0),
(34, 1, 'HeThong', 'Đăng xuất', 'DM_NGUOI_DUNG', 1, NULL, '127.0.0.1', '2026-04-24 21:50:42', '2026-04-24 21:50:42', '2026-04-24 21:50:42', NULL, NULL, 0),
(35, 1, 'HeThong', 'Đăng nhập: admin', 'DM_NGUOI_DUNG', 1, NULL, '127.0.0.1', '2026-04-24 21:50:50', '2026-04-24 21:50:50', '2026-04-24 21:50:50', NULL, NULL, 0),
(36, 1, 'HeThong', 'Đăng nhập: admin', 'DM_NGUOI_DUNG', 1, NULL, '127.0.0.1', '2026-04-24 21:51:24', '2026-04-24 21:51:24', '2026-04-24 21:51:24', NULL, NULL, 0),
(37, 1, 'HeThong', 'Đăng nhập: admin', 'DM_NGUOI_DUNG', 1, NULL, '127.0.0.1', '2026-04-24 21:52:25', '2026-04-24 21:52:25', '2026-04-24 21:52:25', NULL, NULL, 0),
(38, 1, 'HeThong', 'Đăng nhập: admin', 'DM_NGUOI_DUNG', 1, NULL, '127.0.0.1', '2026-04-24 21:55:08', '2026-04-24 21:55:08', '2026-04-24 21:55:08', NULL, NULL, 0),
(39, 1, 'HeThong', 'Đăng nhập: admin', 'DM_NGUOI_DUNG', 1, NULL, '127.0.0.1', '2026-04-24 21:57:57', '2026-04-24 21:57:57', '2026-04-24 21:57:57', NULL, NULL, 0),
(40, 1, 'HeThong', 'Đăng nhập: admin', 'DM_NGUOI_DUNG', 1, NULL, '127.0.0.1', '2026-04-24 22:12:57', '2026-04-24 22:12:57', '2026-04-24 22:12:57', NULL, NULL, 0),
(41, 1, 'HeThong', 'Đăng nhập: admin', 'DM_NGUOI_DUNG', 1, NULL, '127.0.0.1', '2026-04-24 22:14:28', '2026-04-24 22:14:28', '2026-04-24 22:14:28', NULL, NULL, 0),
(42, 1, 'HeThong', 'Tạo hàng loạt 10 buổi cho lớp id=9', 'DT_LICH_HOC', 9, NULL, '127.0.0.1', '2026-04-24 22:20:41', '2026-04-24 22:20:41', '2026-04-24 22:20:41', NULL, NULL, 0),
(43, 1, 'HeThong', 'Thêm buổi học: aaaccccc (2026-04-24)', 'DT_LICH_HOC', 51, NULL, '127.0.0.1', '2026-04-24 22:20:59', '2026-04-24 22:20:59', '2026-04-24 22:20:59', NULL, NULL, 0),
(44, 1, 'HeThong', 'Đăng nhập: admin', 'DM_NGUOI_DUNG', 1, NULL, '127.0.0.1', '2026-04-25 10:02:39', '2026-04-25 10:02:39', '2026-04-25 10:02:39', NULL, NULL, 0),
(45, 1, 'HeThong', 'Khởi tạo điểm danh buổi id=34 (10 hv)', 'DT_DIEM_DANH', 34, NULL, '127.0.0.1', '2026-04-25 10:02:48', '2026-04-25 10:02:48', '2026-04-25 10:02:48', NULL, NULL, 0),
(46, 1, 'HeThong', 'Lưu điểm danh buổi id=34 (10 dòng cập nhật)', 'DT_DIEM_DANH', 34, NULL, '127.0.0.1', '2026-04-25 10:03:02', '2026-04-25 10:03:02', '2026-04-25 10:03:02', NULL, NULL, 0),
(47, 1, 'HeThong', 'Khởi tạo điểm danh buổi id=50 (10 hv)', 'DT_DIEM_DANH', 50, NULL, '127.0.0.1', '2026-04-25 10:03:04', '2026-04-25 10:03:04', '2026-04-25 10:03:04', NULL, NULL, 0),
(48, 1, 'HeThong', 'Khởi tạo điểm danh buổi id=37 (8 hv)', 'DT_DIEM_DANH', 37, NULL, '127.0.0.1', '2026-04-25 10:07:46', '2026-04-25 10:07:46', '2026-04-25 10:07:46', NULL, NULL, 0),
(49, 1, 'HeThong', 'Khởi tạo điểm danh buổi id=36 (8 hv)', 'DT_DIEM_DANH', 36, NULL, '127.0.0.1', '2026-04-25 10:07:47', '2026-04-25 10:07:47', '2026-04-25 10:07:47', NULL, NULL, 0),
(50, 1, 'HeThong', 'Khởi tạo điểm danh buổi id=35 (8 hv)', 'DT_DIEM_DANH', 35, NULL, '127.0.0.1', '2026-04-25 10:07:48', '2026-04-25 10:07:48', '2026-04-25 10:07:48', NULL, NULL, 0),
(51, 1, 'HeThong', 'Đăng nhập: admin', 'DM_NGUOI_DUNG', 1, NULL, '127.0.0.1', '2026-04-25 21:10:37', '2026-04-25 21:10:37', '2026-04-25 21:10:37', NULL, NULL, 0),
(52, 1, 'HeThong', 'Thêm tài liệu: aaa', 'DT_TAI_LIEU', 17, NULL, '127.0.0.1', '2026-04-25 21:11:38', '2026-04-25 21:11:38', '2026-04-25 21:11:38', NULL, NULL, 0),
(53, 1, 'HeThong', 'Phân công GV id=7 cho lớp id=10', 'DT_PHAN_CONG_GIANG_VIEN', 66, NULL, '127.0.0.1', '2026-04-25 21:20:47', '2026-04-25 21:20:47', '2026-04-25 21:20:47', NULL, NULL, 0),
(54, 1, 'HeThong', 'Thêm giảng viên: Trần thị BÌnh', 'DM_GIANG_VIEN', 17, NULL, '127.0.0.1', '2026-04-25 21:23:08', '2026-04-25 21:23:08', '2026-04-25 21:23:08', NULL, NULL, 0),
(55, 1, 'HeThong', 'Đăng nhập: admin', 'DM_NGUOI_DUNG', 1, NULL, '127.0.0.1', '2026-04-26 06:40:46', '2026-04-26 06:40:46', '2026-04-26 06:40:46', NULL, NULL, 0),
(56, 1, 'HeThong', 'Đăng nhập: admin', 'DM_NGUOI_DUNG', 1, NULL, '127.0.0.1', '2026-04-26 07:07:05', '2026-04-26 07:07:05', '2026-04-26 07:07:05', NULL, NULL, 0),
(57, 1, 'DaoTao', 'Thêm chứng chỉ: CC_0001 - CC abc', 'DT_CHUNG_CHI', 1, NULL, '127.0.0.1', '2026-04-26 07:26:25', '2026-04-26 07:26:25', '2026-04-26 07:26:25', NULL, NULL, 0),
(58, 1, 'DaoTao', 'Thêm hồ sơ học viên: cccc', 'DT_HO_SO_HOC_VIEN', 1, NULL, '127.0.0.1', '2026-04-26 07:26:54', '2026-04-26 07:26:54', '2026-04-26 07:26:54', NULL, NULL, 0),
(59, 1, 'DaoTao', 'Sửa hồ sơ học viên id=1: cccc', 'DT_HO_SO_HOC_VIEN', 1, NULL, '127.0.0.1', '2026-04-26 07:27:06', '2026-04-26 07:27:06', '2026-04-26 07:27:06', NULL, NULL, 0),
(60, 1, 'HeThong', 'Xóa 0 log cũ hơn 90 ngày', 'DM_NHAT_KY_HE_THONG', NULL, NULL, '127.0.0.1', '2026-04-26 07:28:38', '2026-04-26 07:28:38', '2026-04-26 07:28:38', NULL, NULL, 0),
(61, 1, 'HeThong', 'Xóa 0 log cũ hơn 90 ngày', 'DM_NHAT_KY_HE_THONG', NULL, NULL, '127.0.0.1', '2026-04-26 07:28:41', '2026-04-26 07:28:41', '2026-04-26 07:28:41', NULL, NULL, 0),
(62, 1, 'HeThong', 'Xóa 0 log cũ hơn 7 ngày', 'DM_NHAT_KY_HE_THONG', NULL, NULL, '127.0.0.1', '2026-04-26 07:28:58', '2026-04-26 07:28:58', '2026-04-26 07:28:58', NULL, NULL, 0),
(63, 1, 'HeThong', 'Xóa 0 log cũ hơn 7 ngày', 'DM_NHAT_KY_HE_THONG', NULL, NULL, '127.0.0.1', '2026-04-26 07:29:02', '2026-04-26 07:29:02', '2026-04-26 07:29:02', NULL, NULL, 0),
(64, 1, 'HeThong', 'Đăng nhập: admin', 'DM_NGUOI_DUNG', 1, NULL, '127.0.0.1', '2026-04-26 10:35:22', '2026-04-26 10:35:22', '2026-04-26 10:35:22', NULL, NULL, 0),
(65, 1, 'HeThong', 'Đăng nhập: admin', 'DM_NGUOI_DUNG', 1, NULL, '127.0.0.1', '2026-04-26 12:52:22', '2026-04-26 12:52:22', '2026-04-26 12:52:22', NULL, NULL, 0),
(66, 1, 'HeThong', 'Đăng nhập: admin', 'DM_NGUOI_DUNG', 1, NULL, '127.0.0.1', '2026-04-26 13:29:55', '2026-04-26 13:29:55', '2026-04-26 13:29:55', NULL, NULL, 0),
(67, 1, 'HeThong', 'Đăng nhập: admin', 'DM_NGUOI_DUNG', 1, NULL, '127.0.0.1', '2026-04-26 13:35:07', '2026-04-26 13:35:07', '2026-04-26 13:35:07', NULL, NULL, 0),
(68, 1, 'DaoTao', 'Sửa chứng chỉ id=1: CC_0001', 'DT_CHUNG_CHI', 1, NULL, '127.0.0.1', '2026-04-26 13:36:03', '2026-04-26 13:36:03', '2026-04-26 13:36:03', NULL, NULL, 0),
(69, 1, 'DaoTao', 'Cấp chứng chỉ id=1: CC_0001', 'DT_CHUNG_CHI', 1, NULL, '127.0.0.1', '2026-04-26 13:36:10', '2026-04-26 13:36:10', '2026-04-26 13:36:10', NULL, NULL, 0),
(70, 1, 'HeThong', 'Đăng nhập: admin', 'DM_NGUOI_DUNG', 1, NULL, '127.0.0.1', '2026-04-26 21:36:27', '2026-04-26 21:36:27', '2026-04-26 21:36:27', NULL, NULL, 0),
(71, 1, 'HeThong', 'Thêm HV: Nguyễn Văn Lộc', 'DM_HOC_VIEN', 13, NULL, '127.0.0.1', '2026-04-26 21:45:01', '2026-04-26 21:45:01', '2026-04-26 21:45:01', NULL, NULL, 0),
(72, 1, 'DaoTao', 'Duyệt đăng ký id=1: Nguyễn Văn Lộc', 'DT_DANG_KY_KHOA_HOC', 1, NULL, '127.0.0.1', '2026-04-26 21:45:01', '2026-04-26 21:45:01', '2026-04-26 21:45:01', NULL, NULL, 0),
(73, 1, 'DT_KhoaHocMonHoc', 'Thêm môn \'Lượng giá kết quả học tập\' vào khóa \'Cấp cứu cơ bản (BLS)\'', 'DT_KHOA_HOC_MON_HOC', 28, NULL, '127.0.0.1', '2026-04-26 22:56:13', '2026-04-26 22:56:13', '2026-04-26 22:56:13', NULL, NULL, 0),
(74, 1, 'HeThong', 'Khởi tạo điểm danh buổi id=26 (12 hv)', 'DT_DIEM_DANH', 26, NULL, '127.0.0.1', '2026-04-26 23:02:40', '2026-04-26 23:02:40', '2026-04-26 23:02:40', NULL, NULL, 0),
(75, 1, 'HeThong', 'Khởi tạo điểm danh buổi id=25 (12 hv)', 'DT_DIEM_DANH', 25, NULL, '127.0.0.1', '2026-04-26 23:02:42', '2026-04-26 23:02:42', '2026-04-26 23:02:42', NULL, NULL, 0),
(76, 1, 'HeThong', 'Khởi tạo điểm danh buổi id=24 (12 hv)', 'DT_DIEM_DANH', 24, NULL, '127.0.0.1', '2026-04-26 23:02:42', '2026-04-26 23:02:42', '2026-04-26 23:02:42', NULL, NULL, 0),
(77, 1, 'HeThong', 'Đăng nhập: admin', 'DM_NGUOI_DUNG', 1, NULL, '127.0.0.1', '2026-04-27 09:44:07', '2026-04-27 09:44:07', '2026-04-27 09:44:07', NULL, NULL, 0),
(78, 1, 'HeThong', 'Reset mật khẩu người dùng id=7', 'DM_NGUOI_DUNG', 7, NULL, '127.0.0.1', '2026-04-27 09:48:35', '2026-04-27 09:48:35', '2026-04-27 09:48:35', NULL, NULL, 0),
(79, 1, 'HeThong', 'Sửa người dùng: locxoai', 'DM_NGUOI_DUNG', 7, NULL, '127.0.0.1', '2026-04-27 09:49:04', '2026-04-27 09:49:04', '2026-04-27 09:49:04', NULL, NULL, 0),
(80, 1, 'HeThong', 'Cập nhật phân quyền nhóm id=4', 'DM_PHAN_QUYEN', 4, NULL, '127.0.0.1', '2026-04-27 09:50:11', '2026-04-27 09:50:11', '2026-04-27 09:50:11', NULL, NULL, 0),
(81, 1, 'HeThong', 'Đăng xuất', 'DM_NGUOI_DUNG', 1, NULL, '127.0.0.1', '2026-04-27 09:50:15', '2026-04-27 09:50:15', '2026-04-27 09:50:15', NULL, NULL, 0),
(82, 7, 'HeThong', 'Đăng nhập: locxoai', 'DM_NGUOI_DUNG', 7, NULL, '127.0.0.1', '2026-04-27 09:50:22', '2026-04-27 09:50:22', '2026-04-27 09:50:22', NULL, NULL, 0),
(83, 7, 'HeThong', 'Đăng xuất', 'DM_NGUOI_DUNG', 7, NULL, '127.0.0.1', '2026-04-27 09:51:00', '2026-04-27 09:51:00', '2026-04-27 09:51:00', NULL, NULL, 0),
(84, 1, 'HeThong', 'Đăng nhập: admin', 'DM_NGUOI_DUNG', 1, NULL, '127.0.0.1', '2026-04-27 09:51:03', '2026-04-27 09:51:03', '2026-04-27 09:51:03', NULL, NULL, 0),
(85, 1, 'HeThong', 'Cập nhật phân quyền nhóm id=4', 'DM_PHAN_QUYEN', 4, NULL, '127.0.0.1', '2026-04-27 09:53:49', '2026-04-27 09:53:49', '2026-04-27 09:53:49', NULL, NULL, 0),
(86, 1, 'HeThong', 'Đăng xuất', 'DM_NGUOI_DUNG', 1, NULL, '127.0.0.1', '2026-04-27 09:53:52', '2026-04-27 09:53:52', '2026-04-27 09:53:52', NULL, NULL, 0),
(87, 7, 'HeThong', 'Đăng nhập: locxoai', 'DM_NGUOI_DUNG', 7, NULL, '127.0.0.1', '2026-04-27 09:54:03', '2026-04-27 09:54:03', '2026-04-27 09:54:03', NULL, NULL, 0),
(88, 1, 'HeThong', 'Đăng nhập: admin', 'DM_NGUOI_DUNG', 1, NULL, '127.0.0.1', '2026-04-27 16:55:48', '2026-04-27 16:55:48', '2026-04-27 16:55:48', NULL, NULL, 0),
(89, 1, 'HeThong', 'Đăng nhập: admin', 'DM_NGUOI_DUNG', 1, NULL, '127.0.0.1', '2026-04-27 22:43:39', '2026-04-27 22:43:39', '2026-04-27 22:43:39', NULL, NULL, 0),
(90, 1, 'HeThong', 'Đăng nhập: admin', 'DM_NGUOI_DUNG', 1, NULL, '127.0.0.1', '2026-04-27 22:47:39', '2026-04-27 22:47:39', '2026-04-27 22:47:39', NULL, NULL, 0),
(91, 1, 'NCKH_TaiLieu', 'Thêm tài liệu: thumb_03_Xuất hiện hình thức lừa đảo mới với tỷ l.png', 'NCKH_TAI_LIEU', 9, NULL, '127.0.0.1', '2026-04-27 23:02:17', '2026-04-27 23:02:17', '2026-04-27 23:02:17', NULL, NULL, 0),
(92, 1, 'DM_NCKH_CapDo', 'Cập nhật cấp độ: Cơ sở 1', 'DM_NCKH_CAP_DO', 1, NULL, '127.0.0.1', '2026-04-27 23:04:17', '2026-04-27 23:04:17', '2026-04-27 23:04:17', NULL, NULL, 0),
(93, 1, 'DM_NCKH_CapDo', 'Cập nhật cấp độ: Cơ sở', 'DM_NCKH_CAP_DO', 1, NULL, '127.0.0.1', '2026-04-27 23:04:21', '2026-04-27 23:04:21', '2026-04-27 23:04:21', NULL, NULL, 0),
(94, 1, 'HeThong', 'Đăng xuất', 'DM_NGUOI_DUNG', 1, NULL, '127.0.0.1', '2026-04-27 23:10:55', '2026-04-27 23:10:55', '2026-04-27 23:10:55', NULL, NULL, 0),
(95, 1, 'HeThong', 'Đăng nhập: admin', 'DM_NGUOI_DUNG', 1, NULL, '127.0.0.1', '2026-04-27 23:11:22', '2026-04-27 23:11:22', '2026-04-27 23:11:22', NULL, NULL, 0),
(96, 1, 'HeThong', 'Đăng xuất', 'DM_NGUOI_DUNG', 1, NULL, '127.0.0.1', '2026-04-27 23:14:13', '2026-04-27 23:14:13', '2026-04-27 23:14:13', NULL, NULL, 0),
(97, 1, 'HeThong', 'Đăng nhập: admin', 'DM_NGUOI_DUNG', 1, NULL, '127.0.0.1', '2026-04-27 23:20:33', '2026-04-27 23:20:33', '2026-04-27 23:20:33', NULL, NULL, 0),
(98, 1, 'HeThong', 'Đăng nhập: admin', 'DM_NGUOI_DUNG', 1, NULL, '127.0.0.1', '2026-04-27 23:20:46', '2026-04-27 23:20:46', '2026-04-27 23:20:46', NULL, NULL, 0),
(99, 1, 'HeThong', 'Đăng nhập: admin', 'DM_NGUOI_DUNG', 1, NULL, '127.0.0.1', '2026-04-27 23:23:47', '2026-04-27 23:23:47', '2026-04-27 23:23:47', NULL, NULL, 0),
(100, 1, 'HeThong', 'Đăng xuất', 'DM_NGUOI_DUNG', 1, NULL, '127.0.0.1', '2026-04-27 23:24:29', '2026-04-27 23:24:29', '2026-04-27 23:24:29', NULL, NULL, 0),
(101, 1, 'HeThong', 'Đăng nhập: admin', 'DM_NGUOI_DUNG', 1, NULL, '127.0.0.1', '2026-04-27 23:24:31', '2026-04-27 23:24:31', '2026-04-27 23:24:31', NULL, NULL, 0),
(102, 1, 'HeThong', 'Đăng nhập: admin', 'DM_NGUOI_DUNG', 1, NULL, '127.0.0.1', '2026-04-27 23:27:51', '2026-04-27 23:27:51', '2026-04-27 23:27:51', NULL, NULL, 0),
(103, 1, 'HeThong', 'Đăng nhập: admin', 'DM_NGUOI_DUNG', 1, NULL, '127.0.0.1', '2026-04-27 23:28:38', '2026-04-27 23:28:38', '2026-04-27 23:28:38', NULL, NULL, 0),
(104, 1, 'HeThong', 'Đăng nhập: admin', 'DM_NGUOI_DUNG', 1, NULL, '127.0.0.1', '2026-04-27 23:31:13', '2026-04-27 23:31:13', '2026-04-27 23:31:13', NULL, NULL, 0),
(105, 1, 'HeThong', 'Đăng nhập: admin', 'DM_NGUOI_DUNG', 1, NULL, '127.0.0.1', '2026-04-27 23:37:58', '2026-04-27 23:37:58', '2026-04-27 23:37:58', NULL, NULL, 0),
(106, 1, 'HeThong', 'Đăng xuất', 'DM_NGUOI_DUNG', 1, NULL, '127.0.0.1', '2026-04-27 23:41:12', '2026-04-27 23:41:12', '2026-04-27 23:41:12', NULL, NULL, 0),
(107, 1, 'HeThong', 'Đăng nhập: admin', 'DM_NGUOI_DUNG', 1, NULL, '127.0.0.1', '2026-04-27 23:43:16', '2026-04-27 23:43:16', '2026-04-27 23:43:16', NULL, NULL, 0),
(108, 1, 'HeThong', 'Đăng nhập: admin', 'DM_NGUOI_DUNG', 1, NULL, '127.0.0.1', '2026-04-28 22:27:02', '2026-04-28 22:27:02', '2026-04-28 22:27:02', NULL, NULL, 0),
(109, 1, 'HeThong', 'Đăng nhập: admin', 'DM_NGUOI_DUNG', 1, NULL, '127.0.0.1', '2026-04-29 20:21:36', '2026-04-29 20:21:36', '2026-04-29 20:21:36', NULL, NULL, 0),
(110, 1, 'NCKH_DeTai', 'Thêm đề tài: Test đề ya', 'NCKH_DE_TAI', 23, NULL, '127.0.0.1', '2026-04-29 20:22:53', '2026-04-29 20:22:53', '2026-04-29 20:22:53', NULL, NULL, 0),
(111, 1, 'NCKH_TaiLieu', 'Thêm tài liệu: thumb_04_Bác sĩ 99 tuổi nhưng cơ thể như người 40.png', 'NCKH_TAI_LIEU', 10, NULL, '127.0.0.1', '2026-04-29 20:23:37', '2026-04-29 20:23:37', '2026-04-29 20:23:37', NULL, NULL, 0),
(112, 1, 'NCKH_TaiLieu', 'Thêm tài liệu: thumb_03_Xuất hiện hình thức lừa đảo mới với tỷ l.png', 'NCKH_TAI_LIEU', 11, NULL, '127.0.0.1', '2026-04-29 20:23:42', '2026-04-29 20:23:42', '2026-04-29 20:23:42', NULL, NULL, 0),
(113, 1, 'NCKH_DeTai', 'Gửi duyệt đề tài: Test đề ya', 'NCKH_DE_TAI', 23, NULL, '127.0.0.1', '2026-04-29 20:23:49', '2026-04-29 20:23:49', '2026-04-29 20:23:49', NULL, NULL, 0),
(114, 1, 'HeThong', 'Thêm người dùng: khoa_cntt', 'DM_NGUOI_DUNG', 8, NULL, '127.0.0.1', '2026-04-29 20:27:10', '2026-04-29 20:27:10', '2026-04-29 20:27:10', NULL, NULL, 0),
(115, 1, 'HeThong', 'Cập nhật phân quyền nhóm id=3', 'DM_PHAN_QUYEN', 3, NULL, '127.0.0.1', '2026-04-29 20:27:51', '2026-04-29 20:27:51', '2026-04-29 20:27:51', NULL, NULL, 0),
(116, 1, 'HeThong', 'Đăng xuất', 'DM_NGUOI_DUNG', 1, NULL, '127.0.0.1', '2026-04-29 20:27:55', '2026-04-29 20:27:55', '2026-04-29 20:27:55', NULL, NULL, 0),
(117, 8, 'HeThong', 'Đăng nhập: khoa_cntt', 'DM_NGUOI_DUNG', 8, NULL, '127.0.0.1', '2026-04-29 20:28:02', '2026-04-29 20:28:02', '2026-04-29 20:28:02', NULL, NULL, 0),
(118, 8, 'NCKH_DeTai', 'Thêm đề tài: Đề tài phòng cntt', 'NCKH_DE_TAI', 24, NULL, '127.0.0.1', '2026-04-29 20:28:37', '2026-04-29 20:28:37', '2026-04-29 20:28:37', NULL, NULL, 0),
(119, 8, 'NCKH_DeTai', 'Gửi duyệt đề tài: Đề tài phòng cntt', 'NCKH_DE_TAI', 24, NULL, '127.0.0.1', '2026-04-29 20:28:58', '2026-04-29 20:28:58', '2026-04-29 20:28:58', NULL, NULL, 0),
(120, 8, 'HeThong', 'Đăng xuất', 'DM_NGUOI_DUNG', 8, NULL, '127.0.0.1', '2026-04-29 20:29:18', '2026-04-29 20:29:18', '2026-04-29 20:29:18', NULL, NULL, 0),
(121, 1, 'HeThong', 'Đăng nhập: admin', 'DM_NGUOI_DUNG', 1, NULL, '127.0.0.1', '2026-04-29 20:29:20', '2026-04-29 20:29:20', '2026-04-29 20:29:20', NULL, NULL, 0),
(122, 1, 'HeThong', 'Đăng nhập: admin', 'DM_NGUOI_DUNG', 1, NULL, '127.0.0.1', '2026-04-30 08:45:37', '2026-04-30 08:45:37', '2026-04-30 08:45:37', NULL, NULL, 0),
(123, 1, 'NCKH_DeTai', 'Duyệt đề tài: Đề tài phòng cntt', 'NCKH_DE_TAI', 24, NULL, '127.0.0.1', '2026-04-30 08:48:07', '2026-04-30 08:48:07', '2026-04-30 08:48:07', NULL, NULL, 0),
(124, 1, 'NCKH_DeTai', 'Duyệt đề tài: Test đề ya', 'NCKH_DE_TAI', 23, NULL, '127.0.0.1', '2026-04-30 08:48:28', '2026-04-30 08:48:28', '2026-04-30 08:48:28', NULL, NULL, 0),
(125, 1, 'NCKH_DeTai', 'Cập nhật đề tài: Đề tài phòng cntt', 'NCKH_DE_TAI', 24, NULL, '127.0.0.1', '2026-04-30 08:49:23', '2026-04-30 08:49:23', '2026-04-30 08:49:23', NULL, NULL, 0),
(126, 1, 'HeThong', 'Đăng nhập: admin', 'DM_NGUOI_DUNG', 1, NULL, '127.0.0.1', '2026-05-01 12:26:03', '2026-05-01 12:26:03', '2026-05-01 12:26:03', NULL, NULL, 0),
(127, 1, 'NCKH_DeTai', 'Xóa mềm đề tài #23', 'NCKH_DE_TAI', 23, NULL, '127.0.0.1', '2026-05-01 12:26:45', '2026-05-01 12:26:45', '2026-05-01 12:26:45', NULL, NULL, 0),
(128, 1, 'NCKH_DeTai', 'Xóa mềm đề tài #24', 'NCKH_DE_TAI', 24, NULL, '127.0.0.1', '2026-05-01 12:26:46', '2026-05-01 12:26:46', '2026-05-01 12:26:46', NULL, NULL, 0),
(129, 1, 'HeThong', 'Đăng xuất', 'DM_NGUOI_DUNG', 1, NULL, '127.0.0.1', '2026-05-01 12:35:46', '2026-05-01 12:35:46', '2026-05-01 12:35:46', NULL, NULL, 0),
(130, 1, 'HeThong', 'Đăng nhập: admin', 'DM_NGUOI_DUNG', 1, NULL, '127.0.0.1', '2026-05-01 12:36:00', '2026-05-01 12:36:00', '2026-05-01 12:36:00', NULL, NULL, 0),
(131, 1, 'HeThong', 'Đăng nhập: admin', 'DM_NGUOI_DUNG', 1, NULL, '127.0.0.1', '2026-05-01 12:38:39', '2026-05-01 12:38:39', '2026-05-01 12:38:39', NULL, NULL, 0),
(132, 1, 'HeThong', 'Đăng nhập: admin', 'DM_NGUOI_DUNG', 1, NULL, '127.0.0.1', '2026-05-01 16:27:09', '2026-05-01 16:27:09', '2026-05-01 16:27:09', NULL, NULL, 0),
(133, 1, 'HeThong', 'Đăng nhập: admin', 'DM_NGUOI_DUNG', 1, NULL, '127.0.0.1', '2026-05-01 16:32:50', '2026-05-01 16:32:50', '2026-05-01 16:32:50', NULL, NULL, 0),
(134, 1, 'DT_KhoaHocMonHoc', 'Thêm môn \'Kỹ thuật thắt nút và khâu nội soi\' vào khóa \'Sản phụ khoa cập nhật\'', 'DT_KHOA_HOC_MON_HOC', 29, NULL, '127.0.0.1', '2026-05-01 16:35:16', '2026-05-01 16:35:16', '2026-05-01 16:35:16', NULL, NULL, 0),
(135, 1, 'HeThong', 'Đăng nhập: admin', 'DM_NGUOI_DUNG', 1, NULL, '127.0.0.1', '2026-05-04 20:27:36', '2026-05-04 20:27:36', '2026-05-04 20:27:36', NULL, NULL, 0),
(136, 1, 'HeThong', 'Đăng xuất', 'DM_NGUOI_DUNG', 1, NULL, '127.0.0.1', '2026-05-04 20:29:11', '2026-05-04 20:29:11', '2026-05-04 20:29:11', NULL, NULL, 0),
(137, 8, 'HeThong', 'Đăng nhập: khoa_cntt', 'DM_NGUOI_DUNG', 8, NULL, '127.0.0.1', '2026-05-04 20:29:14', '2026-05-04 20:29:14', '2026-05-04 20:29:14', NULL, NULL, 0),
(138, 8, 'HeThong', 'Đăng xuất', 'DM_NGUOI_DUNG', 8, NULL, '127.0.0.1', '2026-05-04 20:30:16', '2026-05-04 20:30:16', '2026-05-04 20:30:16', NULL, NULL, 0),
(139, 1, 'HeThong', 'Đăng nhập: admin', 'DM_NGUOI_DUNG', 1, NULL, '127.0.0.1', '2026-05-04 20:30:19', '2026-05-04 20:30:19', '2026-05-04 20:30:19', NULL, NULL, 0),
(140, 1, 'HeThong', 'Đăng xuất', 'DM_NGUOI_DUNG', 1, NULL, '127.0.0.1', '2026-05-04 20:32:02', '2026-05-04 20:32:02', '2026-05-04 20:32:02', NULL, NULL, 0),
(141, 8, 'HeThong', 'Đăng nhập: khoa_cntt', 'DM_NGUOI_DUNG', 8, NULL, '127.0.0.1', '2026-05-04 20:32:09', '2026-05-04 20:32:09', '2026-05-04 20:32:09', NULL, NULL, 0),
(142, 8, 'NCKH_DeTai', 'Thêm đề tài: test đề', 'NCKH_DE_TAI', 25, NULL, '127.0.0.1', '2026-05-04 20:32:27', '2026-05-04 20:32:27', '2026-05-04 20:32:27', NULL, NULL, 0),
(143, 8, 'NCKH_DeTai', 'Gửi duyệt đề tài: test đề', 'NCKH_DE_TAI', 25, NULL, '127.0.0.1', '2026-05-04 22:16:08', '2026-05-04 22:16:08', '2026-05-04 22:16:08', NULL, NULL, 0),
(144, 1, 'HeThong', 'Đăng nhập: admin', 'DM_NGUOI_DUNG', 1, NULL, '127.0.0.1', '2026-05-05 07:21:35', '2026-05-05 07:21:35', '2026-05-05 07:21:35', NULL, NULL, 0),
(145, 1, 'HeThong', 'Đăng nhập: admin', 'DM_NGUOI_DUNG', 1, NULL, '127.0.0.1', '2026-05-05 17:19:53', '2026-05-05 17:19:53', '2026-05-05 17:19:53', NULL, NULL, 0),
(146, 1, 'NCKH_DeTai', 'Duyệt đề tài: test đề', 'NCKH_DE_TAI', 25, NULL, '127.0.0.1', '2026-05-05 17:20:32', '2026-05-05 17:20:32', '2026-05-05 17:20:32', NULL, NULL, 0),
(147, 1, 'HeThong', 'Cập nhật phân quyền nhóm id=4', 'DM_PHAN_QUYEN', 4, NULL, '127.0.0.1', '2026-05-05 17:22:09', '2026-05-05 17:22:09', '2026-05-05 17:22:09', NULL, NULL, 0),
(148, 1, 'HeThong', 'Đăng nhập: admin', 'DM_NGUOI_DUNG', 1, NULL, '127.0.0.1', '2026-05-13 22:17:02', '2026-05-13 22:17:02', '2026-05-13 22:17:02', NULL, NULL, 0),
(149, 1, 'DT_KhoaHoc', 'Cập nhật khóa học: Sản phụ khoa cập nhật', 'DT_KHOA_HOC', 8, NULL, '127.0.0.1', '2026-05-13 22:18:45', '2026-05-13 22:18:45', '2026-05-13 22:18:45', NULL, NULL, 0),
(150, 1, 'HeThong', 'Thêm HV: Nguyeenx Vawn Duc', 'DM_HOC_VIEN', 14, NULL, '127.0.0.1', '2026-05-13 22:21:30', '2026-05-13 22:21:30', '2026-05-13 22:21:30', NULL, NULL, 0),
(151, 1, 'DaoTao', 'Duyệt đăng ký id=2: Nguyeenx Vawn Duc (tạo HV mới)', 'DT_DANG_KY_KHOA_HOC', 2, NULL, '127.0.0.1', '2026-05-13 22:21:30', '2026-05-13 22:21:30', '2026-05-13 22:21:30', NULL, NULL, 0),
(152, 1, 'HeThong', 'Đăng nhập: admin', 'DM_NGUOI_DUNG', 1, NULL, '127.0.0.1', '2026-05-19 11:06:12', '2026-05-19 11:06:12', '2026-05-19 11:06:12', NULL, NULL, 0),
(153, 1, 'HeThong', 'Đăng nhập: admin', 'DM_NGUOI_DUNG', 1, NULL, '127.0.0.1', '2026-05-19 16:47:06', '2026-05-19 16:47:06', '2026-05-19 16:47:06', NULL, NULL, 0),
(154, 1, 'HeThong', 'Đăng nhập: admin', 'DM_NGUOI_DUNG', 1, NULL, '127.0.0.1', '2026-05-22 09:00:52', '2026-05-22 09:00:52', '2026-05-22 09:00:52', NULL, NULL, 0),
(155, 1, 'HeThong', 'Đăng nhập: admin', 'DM_NGUOI_DUNG', 1, NULL, '127.0.0.1', '2026-06-04 09:33:33', '2026-06-04 09:33:33', '2026-06-04 09:33:33', NULL, NULL, 0),
(156, 1, 'HeThong', 'Đăng nhập: admin', 'DM_NGUOI_DUNG', 1, NULL, '127.0.0.1', '2026-06-04 09:50:02', '2026-06-04 09:50:02', '2026-06-04 09:50:02', NULL, NULL, 0),
(157, 1, 'HeThong', 'Đăng nhập: admin', 'DM_NGUOI_DUNG', 1, NULL, '127.0.0.1', '2026-06-11 20:43:05', '2026-06-11 20:43:05', '2026-06-11 20:43:05', NULL, NULL, 0),
(158, 1, 'HeThong', 'Thêm form: Khoá học', 'DM_DANH_SACH_FORM', 41, NULL, '127.0.0.1', '2026-06-11 20:44:00', '2026-06-11 20:44:00', '2026-06-11 20:44:00', NULL, NULL, 0),
(159, 1, 'HeThong', 'Cập nhật phân quyền nhóm id=4', 'DM_PHAN_QUYEN', 4, NULL, '127.0.0.1', '2026-06-11 20:45:01', '2026-06-11 20:45:01', '2026-06-11 20:45:01', NULL, NULL, 0),
(160, 1, 'HeThong', 'Đăng xuất', 'DM_NGUOI_DUNG', 1, NULL, '127.0.0.1', '2026-06-11 20:45:08', '2026-06-11 20:45:08', '2026-06-11 20:45:08', NULL, NULL, 0),
(161, 1, 'HeThong', 'Đăng nhập: admin', 'DM_NGUOI_DUNG', 1, NULL, '127.0.0.1', '2026-06-11 20:45:15', '2026-06-11 20:45:15', '2026-06-11 20:45:15', NULL, NULL, 0),
(162, 1, 'HeThong', 'Thêm người dùng: lena', 'DM_NGUOI_DUNG', 9, NULL, '127.0.0.1', '2026-06-11 20:45:35', '2026-06-11 20:45:35', '2026-06-11 20:45:35', NULL, NULL, 0),
(163, 1, 'HeThong', 'Đăng xuất', 'DM_NGUOI_DUNG', 1, NULL, '127.0.0.1', '2026-06-11 20:45:43', '2026-06-11 20:45:43', '2026-06-11 20:45:43', NULL, NULL, 0),
(164, 9, 'HeThong', 'Đăng nhập: lena', 'DM_NGUOI_DUNG', 9, NULL, '127.0.0.1', '2026-06-11 20:45:46', '2026-06-11 20:45:46', '2026-06-11 20:45:46', NULL, NULL, 0),
(165, 9, 'HeThong', 'Đăng xuất', 'DM_NGUOI_DUNG', 9, NULL, '127.0.0.1', '2026-06-11 20:46:18', '2026-06-11 20:46:18', '2026-06-11 20:46:18', NULL, NULL, 0),
(166, 1, 'HeThong', 'Đăng nhập: admin', 'DM_NGUOI_DUNG', 1, NULL, '127.0.0.1', '2026-06-11 20:46:21', '2026-06-11 20:46:21', '2026-06-11 20:46:21', NULL, NULL, 0),
(167, 1, 'HeThong', 'Cập nhật phân quyền nhóm id=4', 'DM_PHAN_QUYEN', 4, NULL, '127.0.0.1', '2026-06-11 20:48:15', '2026-06-11 20:48:15', '2026-06-11 20:48:15', NULL, NULL, 0),
(168, 1, 'HeThong', 'Cập nhật phân quyền nhóm id=4', 'DM_PHAN_QUYEN', 4, NULL, '127.0.0.1', '2026-06-11 20:48:17', '2026-06-11 20:48:17', '2026-06-11 20:48:17', NULL, NULL, 0),
(169, 1, 'HeThong', 'Đăng xuất', 'DM_NGUOI_DUNG', 1, NULL, '127.0.0.1', '2026-06-11 20:48:21', '2026-06-11 20:48:21', '2026-06-11 20:48:21', NULL, NULL, 0),
(170, 9, 'HeThong', 'Đăng nhập: lena', 'DM_NGUOI_DUNG', 9, NULL, '127.0.0.1', '2026-06-11 20:48:23', '2026-06-11 20:48:23', '2026-06-11 20:48:23', NULL, NULL, 0),
(171, 9, 'HeThong', 'Đăng xuất', 'DM_NGUOI_DUNG', 9, NULL, '127.0.0.1', '2026-06-11 20:48:55', '2026-06-11 20:48:55', '2026-06-11 20:48:55', NULL, NULL, 0),
(172, 1, 'HeThong', 'Đăng nhập: admin', 'DM_NGUOI_DUNG', 1, NULL, '127.0.0.1', '2026-06-11 20:48:57', '2026-06-11 20:48:57', '2026-06-11 20:48:57', NULL, NULL, 0),
(173, 1, 'HeThong', 'Cập nhật phân quyền nhóm id=4', 'DM_PHAN_QUYEN', 4, NULL, '127.0.0.1', '2026-06-11 20:49:20', '2026-06-11 20:49:20', '2026-06-11 20:49:20', NULL, NULL, 0),
(174, 1, 'HeThong', 'Đăng xuất', 'DM_NGUOI_DUNG', 1, NULL, '127.0.0.1', '2026-06-11 20:49:41', '2026-06-11 20:49:41', '2026-06-11 20:49:41', NULL, NULL, 0),
(175, 9, 'HeThong', 'Đăng nhập: lena', 'DM_NGUOI_DUNG', 9, NULL, '127.0.0.1', '2026-06-11 20:49:43', '2026-06-11 20:49:43', '2026-06-11 20:49:43', NULL, NULL, 0),
(176, 1, 'HeThong', 'Thêm HV: Nguyễn Test', 'DM_HOC_VIEN', 15, NULL, '0.0.0.0', '2026-06-11 20:53:06', '2026-06-11 20:53:06', '2026-06-11 20:53:06', NULL, NULL, 0),
(177, 9, 'HeThong', 'Thêm HV: Nguyễn Văn Đức', 'DM_HOC_VIEN', 16, NULL, '127.0.0.1', '2026-06-11 20:58:25', '2026-06-11 20:58:25', '2026-06-11 20:58:25', NULL, NULL, 0),
(178, 9, 'HeThong', 'Đăng nhập: lena', 'DM_NGUOI_DUNG', 9, NULL, '127.0.0.1', '2026-06-12 09:13:26', '2026-06-12 09:13:26', '2026-06-12 09:13:26', NULL, NULL, 0),
(179, 9, 'HeThong', 'Đăng xuất', 'DM_NGUOI_DUNG', 9, NULL, '127.0.0.1', '2026-06-12 09:21:14', '2026-06-12 09:21:14', '2026-06-12 09:21:14', NULL, NULL, 0),
(180, 1, 'HeThong', 'Đăng nhập: admin', 'DM_NGUOI_DUNG', 1, NULL, '127.0.0.1', '2026-06-12 09:21:16', '2026-06-12 09:21:16', '2026-06-12 09:21:16', NULL, NULL, 0),
(184, 1, 'HeThong', 'Đăng nhập: admin', 'DM_NGUOI_DUNG', 1, NULL, '127.0.0.1', '2026-06-13 09:46:59', '2026-06-13 09:46:59', '2026-06-13 09:46:59', NULL, NULL, 0),
(185, 1, 'HeThong', 'Đăng xuất', 'DM_NGUOI_DUNG', 1, NULL, '127.0.0.1', '2026-06-13 09:47:38', '2026-06-13 09:47:38', '2026-06-13 09:47:38', NULL, NULL, 0),
(186, 9, 'HeThong', 'Đăng nhập: lena', 'DM_NGUOI_DUNG', 9, NULL, '127.0.0.1', '2026-06-13 09:47:41', '2026-06-13 09:47:41', '2026-06-13 09:47:41', NULL, NULL, 0),
(187, 1, 'HeThong', 'Thêm chương trình: CTĐT test', 'DT_CHUONG_TRINH', 13, NULL, '0.0.0.0', '2026-06-13 12:19:54', '2026-06-13 12:19:54', '2026-06-13 12:19:54', NULL, NULL, 0),
(188, 1, 'DT_ChuongTrinh', 'Gắn khóa #1 vào CTĐT #13', 'DT_KHOA_HOC_CHUONG_TRINH', 21, NULL, '0.0.0.0', '2026-06-13 12:19:54', '2026-06-13 12:19:54', '2026-06-13 12:19:54', NULL, NULL, 0),
(189, 1, 'DT_ChuongTrinh', 'Gỡ liên kết khóa-CTĐT #21', 'DT_KHOA_HOC_CHUONG_TRINH', 21, NULL, '0.0.0.0', '2026-06-13 12:19:54', '2026-06-13 12:19:54', '2026-06-13 12:19:54', NULL, NULL, 0),
(190, 9, 'HeThong', 'Đăng nhập: lena', 'DM_NGUOI_DUNG', 9, NULL, '127.0.0.1', '2026-06-13 12:21:28', '2026-06-13 12:21:28', '2026-06-13 12:21:28', NULL, NULL, 0),
(191, 9, 'HeThong', 'Thêm chương trình: Giải phẫu bệnh cơ bản', 'DT_CHUONG_TRINH', 14, NULL, '127.0.0.1', '2026-06-13 12:21:54', '2026-06-13 12:21:54', '2026-06-13 12:21:54', NULL, NULL, 0),
(192, 9, 'HeThong', 'Thêm chương trình: Chấn thương Chỉnh hình cơ bản', 'DT_CHUONG_TRINH', 15, NULL, '127.0.0.1', '2026-06-13 12:22:16', '2026-06-13 12:22:16', '2026-06-13 12:22:16', NULL, NULL, 0),
(193, 9, 'HeThong', 'Thêm chương trình: Chẩn đoán và điều trị các bệnh Nội tiết', 'DT_CHUONG_TRINH', 16, NULL, '127.0.0.1', '2026-06-13 12:23:00', '2026-06-13 12:23:00', '2026-06-13 12:23:00', NULL, NULL, 0),
(194, 9, 'HeThong', 'Thêm chương trình: Kỹ thuật viên Nội soi Tiêu hóa', 'DT_CHUONG_TRINH', 17, NULL, '127.0.0.1', '2026-06-13 12:24:42', '2026-06-13 12:24:42', '2026-06-13 12:24:42', NULL, NULL, 0),
(195, 9, 'HeThong', 'Thêm chương trình: Siêu âm cơ bản', 'DT_CHUONG_TRINH', 18, NULL, '127.0.0.1', '2026-06-13 12:25:20', '2026-06-13 12:25:20', '2026-06-13 12:25:20', NULL, NULL, 0),
(196, 9, 'HeThong', 'Thêm chương trình: Nội soi tiêu hóa cơ bản', 'DT_CHUONG_TRINH', 19, NULL, '127.0.0.1', '2026-06-13 12:25:39', '2026-06-13 12:25:39', '2026-06-13 12:25:39', NULL, NULL, 0),
(197, 9, 'HeThong', 'Thêm chương trình: Nội soi can thiệp đường tiêu hóa', 'DT_CHUONG_TRINH', 20, NULL, '127.0.0.1', '2026-06-13 12:26:02', '2026-06-13 12:26:02', '2026-06-13 12:26:02', NULL, NULL, 0),
(198, 9, 'HeThong', 'Thêm chương trình: Nội soi đại tràng', 'DT_CHUONG_TRINH', 21, NULL, '127.0.0.1', '2026-06-13 12:27:33', '2026-06-13 12:27:33', '2026-06-13 12:27:33', NULL, NULL, 0),
(199, 9, 'HeThong', 'Thêm chương trình: Phẫu thuật Nội soi cơ bản', 'DT_CHUONG_TRINH', 22, NULL, '127.0.0.1', '2026-06-13 12:27:59', '2026-06-13 12:27:59', '2026-06-13 12:27:59', NULL, NULL, 0),
(200, 9, 'HeThong', 'Thêm chương trình: Hóa sinh cơ bản', 'DT_CHUONG_TRINH', 23, NULL, '127.0.0.1', '2026-06-13 12:28:26', '2026-06-13 12:28:26', '2026-06-13 12:28:26', NULL, NULL, 0),
(201, 9, 'HeThong', 'Thêm chương trình: Chẩn đoán và điều trị bệnh đột quỵ não', 'DT_CHUONG_TRINH', 24, NULL, '127.0.0.1', '2026-06-13 12:28:48', '2026-06-13 12:28:48', '2026-06-13 12:28:48', NULL, NULL, 0),
(202, 9, 'HeThong', 'Đăng nhập: lena', 'DM_NGUOI_DUNG', 9, NULL, '127.0.0.1', '2026-06-13 19:30:14', '2026-06-13 19:30:14', '2026-06-13 19:30:14', NULL, NULL, 0),
(203, 1, 'HeThong', 'Thêm chương trình: Chẩn đoán và điều trị suy tim', 'DT_CHUONG_TRINH', 25, NULL, '0.0.0.0', '2026-06-13 22:27:59', '2026-06-13 22:27:59', '2026-06-13 22:27:59', NULL, NULL, 0),
(204, 1, 'HeThong', 'Thêm chương trình: Hướng dẫn đọc Điện tâm đồ', 'DT_CHUONG_TRINH', 26, NULL, '0.0.0.0', '2026-06-13 22:27:59', '2026-06-13 22:27:59', '2026-06-13 22:27:59', NULL, NULL, 0),
(205, 1, 'HeThong', 'Thêm chương trình: Điện não đồ', 'DT_CHUONG_TRINH', 27, NULL, '0.0.0.0', '2026-06-13 22:27:59', '2026-06-13 22:27:59', '2026-06-13 22:27:59', NULL, NULL, 0),
(206, 1, 'HeThong', 'Thêm chương trình: An toàn người bệnh', 'DT_CHUONG_TRINH', 28, NULL, '0.0.0.0', '2026-06-13 22:27:59', '2026-06-13 22:27:59', '2026-06-13 22:27:59', NULL, NULL, 0),
(207, 1, 'HeThong', 'Thêm chương trình: Sử dụng thuốc an toàn hợp lý cho Điều dưỡng', 'DT_CHUONG_TRINH', 29, NULL, '0.0.0.0', '2026-06-13 22:27:59', '2026-06-13 22:27:59', '2026-06-13 22:27:59', NULL, NULL, 0),
(208, 1, 'HeThong', 'Thêm chương trình: Tăng cường năng lực quản lý Điều dưỡng', 'DT_CHUONG_TRINH', 30, NULL, '0.0.0.0', '2026-06-13 22:27:59', '2026-06-13 22:27:59', '2026-06-13 22:27:59', NULL, NULL, 0),
(209, 1, 'HeThong', 'Thêm chương trình: Gây mê hồi sức cơ bản', 'DT_CHUONG_TRINH', 31, NULL, '0.0.0.0', '2026-06-13 22:27:59', '2026-06-13 22:27:59', '2026-06-13 22:27:59', NULL, NULL, 0),
(210, 1, 'HeThong', 'Thêm chương trình: Gây mê hồi sức trong phẫu thuật nội soi', 'DT_CHUONG_TRINH', 32, NULL, '0.0.0.0', '2026-06-13 22:27:59', '2026-06-13 22:27:59', '2026-06-13 22:27:59', NULL, NULL, 0),
(211, 1, 'HeThong', 'Thêm chương trình: X.quang cơ bản', 'DT_CHUONG_TRINH', 33, NULL, '0.0.0.0', '2026-06-13 22:27:59', '2026-06-13 22:27:59', '2026-06-13 22:27:59', NULL, NULL, 0),
(212, 1, 'HeThong', 'Thêm chương trình: Cắt lớp vi tính cơ bản', 'DT_CHUONG_TRINH', 34, NULL, '0.0.0.0', '2026-06-13 22:27:59', '2026-06-13 22:27:59', '2026-06-13 22:27:59', NULL, NULL, 0),
(213, 1, 'HeThong', 'Thêm chương trình: Khám và điều trị các bệnh TMH cơ bản', 'DT_CHUONG_TRINH', 35, NULL, '0.0.0.0', '2026-06-13 22:27:59', '2026-06-13 22:27:59', '2026-06-13 22:27:59', NULL, NULL, 0),
(214, 1, 'HeThong', 'Thêm chương trình: Khám nội soi tai mũi họng', 'DT_CHUONG_TRINH', 36, NULL, '0.0.0.0', '2026-06-13 22:27:59', '2026-06-13 22:27:59', '2026-06-13 22:27:59', NULL, NULL, 0),
(215, 1, 'HeThong', 'Thêm chương trình: Mở khí quản', 'DT_CHUONG_TRINH', 37, NULL, '0.0.0.0', '2026-06-13 22:27:59', '2026-06-13 22:27:59', '2026-06-13 22:27:59', NULL, NULL, 0),
(216, 1, 'HeThong', 'Thêm chương trình: Đào tạo điều dưỡng chuyên ngành tai mũi họng', 'DT_CHUONG_TRINH', 38, NULL, '0.0.0.0', '2026-06-13 22:27:59', '2026-06-13 22:27:59', '2026-06-13 22:27:59', NULL, NULL, 0),
(217, 1, 'HeThong', 'Thêm chương trình: Điều dưỡng nha khoa', 'DT_CHUONG_TRINH', 39, NULL, '0.0.0.0', '2026-06-13 22:27:59', '2026-06-13 22:27:59', '2026-06-13 22:27:59', NULL, NULL, 0),
(218, 1, 'HeThong', 'Thêm chương trình: Kỹ thuật cấp cứu cơ bản - Bác sĩ', 'DT_CHUONG_TRINH', 40, NULL, '0.0.0.0', '2026-06-13 22:27:59', '2026-06-13 22:27:59', '2026-06-13 22:27:59', NULL, NULL, 0),
(219, 1, 'HeThong', 'Thêm chương trình: Kỹ thuật cấp cứu cơ bản - Điều dưỡng', 'DT_CHUONG_TRINH', 41, NULL, '0.0.0.0', '2026-06-13 22:27:59', '2026-06-13 22:27:59', '2026-06-13 22:27:59', NULL, NULL, 0),
(220, 1, 'HeThong', 'Thêm chương trình: Phẫu thuật Phaco', 'DT_CHUONG_TRINH', 42, NULL, '0.0.0.0', '2026-06-13 22:27:59', '2026-06-13 22:27:59', '2026-06-13 22:27:59', NULL, NULL, 0),
(221, 1, 'HeThong', 'Thêm chương trình: Chụp mạch huỳnh quang đáy mắt', 'DT_CHUONG_TRINH', 43, NULL, '0.0.0.0', '2026-06-13 22:27:59', '2026-06-13 22:27:59', '2026-06-13 22:27:59', NULL, NULL, 0),
(222, 1, 'HeThong', 'Thêm chương trình: Kiểm soát nhiễm khuẩn', 'DT_CHUONG_TRINH', 44, NULL, '0.0.0.0', '2026-06-13 22:27:59', '2026-06-13 22:27:59', '2026-06-13 22:27:59', NULL, NULL, 0),
(223, 1, 'HeThong', 'Thêm chương trình: Cấp cứu ngừng tuần hoàn', 'DT_CHUONG_TRINH', 45, NULL, '0.0.0.0', '2026-06-13 22:27:59', '2026-06-13 22:27:59', '2026-06-13 22:27:59', NULL, NULL, 0),
(224, 1, 'HeThong', 'Thêm chương trình: Chẩn đoán và điều trị chấn thương sọ não', 'DT_CHUONG_TRINH', 46, NULL, '0.0.0.0', '2026-06-13 22:27:59', '2026-06-13 22:27:59', '2026-06-13 22:27:59', NULL, NULL, 0),
(225, 1, 'HeThong', 'Thêm chương trình: Tiêm an toàn và quản lý chất rắn y tế', 'DT_CHUONG_TRINH', 47, NULL, '0.0.0.0', '2026-06-13 22:27:59', '2026-06-13 22:27:59', '2026-06-13 22:27:59', NULL, NULL, 0),
(226, 1, 'HeThong', 'Thêm chương trình: Kỹ năng tư vấn và Giáo dục sức khỏe', 'DT_CHUONG_TRINH', 48, NULL, '0.0.0.0', '2026-06-13 22:27:59', '2026-06-13 22:27:59', '2026-06-13 22:27:59', NULL, NULL, 0),
(227, 1, 'HeThong', 'Thêm chương trình: Quản lý điều dưỡng', 'DT_CHUONG_TRINH', 49, NULL, '0.0.0.0', '2026-06-13 22:27:59', '2026-06-13 22:27:59', '2026-06-13 22:27:59', NULL, NULL, 0),
(228, 1, 'HeThong', 'Thêm chương trình: Nội soi phế quản ống mềm', 'DT_CHUONG_TRINH', 50, NULL, '0.0.0.0', '2026-06-13 22:27:59', '2026-06-13 22:27:59', '2026-06-13 22:27:59', NULL, NULL, 0),
(229, 1, 'HeThong', 'Thêm chương trình: Chăm sóc bệnh nhân hồi sức sau mổ', 'DT_CHUONG_TRINH', 51, NULL, '0.0.0.0', '2026-06-13 22:27:59', '2026-06-13 22:27:59', '2026-06-13 22:27:59', NULL, NULL, 0),
(230, 1, 'HeThong', 'Thêm chương trình: Chăm sóc bệnh nhân thở máy', 'DT_CHUONG_TRINH', 52, NULL, '0.0.0.0', '2026-06-13 22:27:59', '2026-06-13 22:27:59', '2026-06-13 22:27:59', NULL, NULL, 0),
(231, 1, 'HeThong', 'Thêm chương trình: Thông khí nhân tạo cơ bản', 'DT_CHUONG_TRINH', 53, NULL, '0.0.0.0', '2026-06-13 22:27:59', '2026-06-13 22:27:59', '2026-06-13 22:27:59', NULL, NULL, 0),
(232, 1, 'HeThong', 'Thêm chương trình: Kỹ thuật xét nghiệm Huyết học - Truyền máu cơ bản', 'DT_CHUONG_TRINH', 54, NULL, '0.0.0.0', '2026-06-13 22:27:59', '2026-06-13 22:27:59', '2026-06-13 22:27:59', NULL, NULL, 0),
(233, 1, 'HeThong', 'Thêm chương trình: Chẩn đoán và điều trị các bệnh lý Cơ xương khớp cơ bản', 'DT_CHUONG_TRINH', 55, NULL, '0.0.0.0', '2026-06-13 22:27:59', '2026-06-13 22:27:59', '2026-06-13 22:27:59', NULL, NULL, 0),
(234, 1, 'HeThong', 'Thêm chương trình: Kỹ thuật vật lý trị liệu- phục hồi chức năng cơ bản', 'DT_CHUONG_TRINH', 56, NULL, '0.0.0.0', '2026-06-13 22:27:59', '2026-06-13 22:27:59', '2026-06-13 22:27:59', NULL, NULL, 0),
(235, 1, 'HeThong', 'Thêm chương trình: Khúc xạ cơ bản', 'DT_CHUONG_TRINH', 57, NULL, '0.0.0.0', '2026-06-13 22:27:59', '2026-06-13 22:27:59', '2026-06-13 22:27:59', NULL, NULL, 0),
(236, 1, 'HeThong', 'Thêm chương trình: Phẫu thuật gan mật cơ bản', 'DT_CHUONG_TRINH', 58, NULL, '0.0.0.0', '2026-06-13 22:27:59', '2026-06-13 22:27:59', '2026-06-13 22:27:59', NULL, NULL, 0),
(237, 1, 'HeThong', 'Thêm chương trình: Kỹ thuật lọc máu thận nhân tạo cơ bản - Điều dưỡng - KTV', 'DT_CHUONG_TRINH', 59, NULL, '0.0.0.0', '2026-06-13 22:27:59', '2026-06-13 22:27:59', '2026-06-13 22:27:59', NULL, NULL, 0),
(238, 1, 'HeThong', 'Thêm chương trình: Kỹ thuật lọc máu thận nhân tạo cơ bản - Bác sỹ', 'DT_CHUONG_TRINH', 60, NULL, '0.0.0.0', '2026-06-13 22:27:59', '2026-06-13 22:27:59', '2026-06-13 22:27:59', NULL, NULL, 0),
(239, 1, 'HeThong', 'Thêm chương trình: Kỹ thuật xét nghiệm vi sinh cơ bản', 'DT_CHUONG_TRINH', 61, NULL, '0.0.0.0', '2026-06-13 22:27:59', '2026-06-13 22:27:59', '2026-06-13 22:27:59', NULL, NULL, 0),
(240, 1, 'HeThong', 'Thêm chương trình: Kỹ thuật xoa bóp bấm huyệt', 'DT_CHUONG_TRINH', 62, NULL, '0.0.0.0', '2026-06-13 22:27:59', '2026-06-13 22:27:59', '2026-06-13 22:27:59', NULL, NULL, 0),
(241, 1, 'HeThong', 'Thêm chương trình: Kỹ thuật châm cứu cơ bản', 'DT_CHUONG_TRINH', 63, NULL, '0.0.0.0', '2026-06-13 22:27:59', '2026-06-13 22:27:59', '2026-06-13 22:27:59', NULL, NULL, 0),
(242, 1, 'HeThong', 'Thêm chương trình: Đọc phim MRI cơ bản', 'DT_CHUONG_TRINH', 64, NULL, '0.0.0.0', '2026-06-13 22:27:59', '2026-06-13 22:27:59', '2026-06-13 22:27:59', NULL, NULL, 0),
(243, 1, 'HeThong', 'Thêm chương trình: Chẩn đoán và điều trị viêm gan', 'DT_CHUONG_TRINH', 65, NULL, '0.0.0.0', '2026-06-13 22:27:59', '2026-06-13 22:27:59', '2026-06-13 22:27:59', NULL, NULL, 0),
(244, 1, 'HeThong', 'Thêm chương trình: Tiêm khớp cơ bản', 'DT_CHUONG_TRINH', 66, NULL, '0.0.0.0', '2026-06-13 22:27:59', '2026-06-13 22:27:59', '2026-06-13 22:27:59', NULL, NULL, 0),
(245, 1, 'HeThong', 'Thêm chương trình: Chẩn đoán và điều trị một một số bệnh đường tiêu hóa thường gặp', 'DT_CHUONG_TRINH', 67, NULL, '0.0.0.0', '2026-06-13 22:27:59', '2026-06-13 22:27:59', '2026-06-13 22:27:59', NULL, NULL, 0),
(246, 1, 'HeThong', 'Thêm chương trình: Chẩn đoán và điều trị các bệnh lý Huyết học – Truyền máu', 'DT_CHUONG_TRINH', 68, NULL, '0.0.0.0', '2026-06-13 22:27:59', '2026-06-13 22:27:59', '2026-06-13 22:27:59', NULL, NULL, 0),
(247, 1, 'HeThong', 'Thêm chương trình: Kỹ thuật viên Gây mê hồi sức', 'DT_CHUONG_TRINH', 69, NULL, '0.0.0.0', '2026-06-13 22:27:59', '2026-06-13 22:27:59', '2026-06-13 22:27:59', NULL, NULL, 0),
(248, 1, 'HeThong', 'Thêm chương trình: Dụng cụ viên Gây mê hồi sức', 'DT_CHUONG_TRINH', 70, NULL, '0.0.0.0', '2026-06-13 22:27:59', '2026-06-13 22:27:59', '2026-06-13 22:27:59', NULL, NULL, 0),
(249, 1, 'HeThong', 'Thêm chương trình: Thực hành tốt bán lẻ thuốc', 'DT_CHUONG_TRINH', 71, NULL, '0.0.0.0', '2026-06-13 22:27:59', '2026-06-13 22:27:59', '2026-06-13 22:27:59', NULL, NULL, 0),
(250, 1, 'HeThong', 'Thêm chương trình: Bảo quản và cấp phát thuốc', 'DT_CHUONG_TRINH', 72, NULL, '0.0.0.0', '2026-06-13 22:27:59', '2026-06-13 22:27:59', '2026-06-13 22:27:59', NULL, NULL, 0),
(251, 1, 'HeThong', 'Thêm chương trình: Kỹ năng tìm kiếm và phân giải thông tin thuốc', 'DT_CHUONG_TRINH', 73, NULL, '0.0.0.0', '2026-06-13 22:27:59', '2026-06-13 22:27:59', '2026-06-13 22:27:59', NULL, NULL, 0),
(252, 1, 'HeThong', 'Thêm chương trình: Sử dụng thuốc trên các đối tượng đặc biệt', 'DT_CHUONG_TRINH', 74, NULL, '0.0.0.0', '2026-06-13 22:27:59', '2026-06-13 22:27:59', '2026-06-13 22:27:59', NULL, NULL, 0),
(253, 1, 'HeThong', 'Thêm chương trình: Thực hành khai thác bệnh án và phân tích ca lâm sàng', 'DT_CHUONG_TRINH', 75, NULL, '0.0.0.0', '2026-06-13 22:27:59', '2026-06-13 22:27:59', '2026-06-13 22:27:59', NULL, NULL, 0),
(254, 1, 'HeThong', 'Thêm chương trình: Kỹ thuật khai thông mạch não bằng điều trị thuốc tiêu sợi huyết trong nhồi máu não cấp', 'DT_CHUONG_TRINH', 76, NULL, '0.0.0.0', '2026-06-13 22:27:59', '2026-06-13 22:27:59', '2026-06-13 22:27:59', NULL, NULL, 0),
(255, 1, 'HeThong', 'Thêm chương trình: Phục hồi chức năng cơ bản', 'DT_CHUONG_TRINH', 77, NULL, '0.0.0.0', '2026-06-13 22:27:59', '2026-06-13 22:27:59', '2026-06-13 22:27:59', NULL, NULL, 0),
(256, 1, 'HeThong', 'Thêm chương trình: Hồi sức cấp cứu cơ bản', 'DT_CHUONG_TRINH', 78, NULL, '0.0.0.0', '2026-06-13 22:27:59', '2026-06-13 22:27:59', '2026-06-13 22:27:59', NULL, NULL, 0),
(257, 9, 'HeThong', 'Đăng nhập: lena', 'DM_NGUOI_DUNG', 9, NULL, '127.0.0.1', '2026-06-13 22:28:35', '2026-06-13 22:28:35', '2026-06-13 22:28:35', NULL, NULL, 0),
(258, 9, 'HeThong', 'Thêm khoa/phòng: Phòng điều dưỡng', 'DM_KHOA_PHONG', 86, NULL, '127.0.0.1', '2026-06-13 22:29:52', '2026-06-13 22:29:52', '2026-06-13 22:29:52', NULL, NULL, 0),
(259, 9, 'DT_MonHoc', 'Thêm môn học: Bài 1: Phân loại bệnh nhân cấp cứu theo mức độ ưu tiên', 'DT_MON_HOC', 25, NULL, '127.0.0.1', '2026-06-13 22:54:04', '2026-06-13 22:54:04', '2026-06-13 22:54:04', NULL, NULL, 0),
(260, 9, 'DT_MonHoc', 'Thêm môn học: Bài 2: Hồi sinh tim phổi cơ bản', 'DT_MON_HOC', 26, NULL, '127.0.0.1', '2026-06-13 22:54:28', '2026-06-13 22:54:28', '2026-06-13 22:54:28', NULL, NULL, 0),
(261, 9, 'DT_MonHoc', 'Cập nhật môn học: Bài 2: Hồi sinh tim phổi cơ bản', 'DT_MON_HOC', 26, NULL, '127.0.0.1', '2026-06-13 22:54:36', '2026-06-13 22:54:36', '2026-06-13 22:54:36', NULL, NULL, 0),
(262, 9, 'DT_MonHoc', 'Thêm môn học: Bài 3: Cấp cứu phản vệ', 'DT_MON_HOC', 27, NULL, '127.0.0.1', '2026-06-13 22:54:52', '2026-06-13 22:54:52', '2026-06-13 22:54:52', NULL, NULL, 0),
(263, 9, 'DT_MonHoc', 'Thêm môn học: Bài 4: Chẩn đoán và xử trí ngộ độc thuốc tê', 'DT_MON_HOC', 28, NULL, '127.0.0.1', '2026-06-13 22:55:05', '2026-06-13 22:55:05', '2026-06-13 22:55:05', NULL, NULL, 0),
(264, 9, 'DT_MonHoc', 'Chuyển môn học #1 vào thùng rác', 'DT_MON_HOC', 1, NULL, '127.0.0.1', '2026-06-13 22:55:13', '2026-06-13 22:55:13', '2026-06-13 22:55:13', NULL, NULL, 0),
(265, 9, 'DT_MonHoc', 'Chuyển môn học #2 vào thùng rác', 'DT_MON_HOC', 2, NULL, '127.0.0.1', '2026-06-13 22:55:15', '2026-06-13 22:55:15', '2026-06-13 22:55:15', NULL, NULL, 0),
(266, 9, 'DT_MonHoc', 'Chuyển môn học #3 vào thùng rác', 'DT_MON_HOC', 3, NULL, '127.0.0.1', '2026-06-13 22:55:16', '2026-06-13 22:55:16', '2026-06-13 22:55:16', NULL, NULL, 0),
(267, 9, 'DT_MonHoc', 'Chuyển môn học #4 vào thùng rác', 'DT_MON_HOC', 4, NULL, '127.0.0.1', '2026-06-13 22:55:18', '2026-06-13 22:55:18', '2026-06-13 22:55:18', NULL, NULL, 0),
(268, 9, 'DT_MonHoc', 'Chuyển môn học #5 vào thùng rác', 'DT_MON_HOC', 5, NULL, '127.0.0.1', '2026-06-13 22:55:20', '2026-06-13 22:55:20', '2026-06-13 22:55:20', NULL, NULL, 0),
(269, 9, 'DT_MonHoc', 'Chuyển môn học #6 vào thùng rác', 'DT_MON_HOC', 6, NULL, '127.0.0.1', '2026-06-13 22:55:21', '2026-06-13 22:55:21', '2026-06-13 22:55:21', NULL, NULL, 0),
(270, 9, 'DT_MonHoc', 'Chuyển môn học #7 vào thùng rác', 'DT_MON_HOC', 7, NULL, '127.0.0.1', '2026-06-13 22:55:23', '2026-06-13 22:55:23', '2026-06-13 22:55:23', NULL, NULL, 0),
(271, 9, 'DT_MonHoc', 'Chuyển môn học #8 vào thùng rác', 'DT_MON_HOC', 8, NULL, '127.0.0.1', '2026-06-13 22:55:25', '2026-06-13 22:55:25', '2026-06-13 22:55:25', NULL, NULL, 0),
(272, 1, 'DT_MonHoc', 'Thêm môn học: Bài 5: Định hướng chẩn đoán và xử trí người bệnh đau ngực cấp', 'DT_MON_HOC', 29, NULL, '0.0.0.0', '2026-06-13 22:59:32', '2026-06-13 22:59:32', '2026-06-13 22:59:32', NULL, NULL, 0),
(273, 1, 'DT_MonHoc', 'Thêm môn học: Bài 6: Định hướng chẩn đoán và xử trí đau bụng cấp ở người lớn', 'DT_MON_HOC', 30, NULL, '0.0.0.0', '2026-06-13 22:59:32', '2026-06-13 22:59:32', '2026-06-13 22:59:32', NULL, NULL, 0),
(274, 1, 'DT_MonHoc', 'Thêm môn học: Bài 7: Chẩn đoán và xử trí bệnh nhân xuất huyết tiêu hóa cao', 'DT_MON_HOC', 31, NULL, '0.0.0.0', '2026-06-13 22:59:32', '2026-06-13 22:59:32', '2026-06-13 22:59:32', NULL, NULL, 0),
(275, 1, 'DT_MonHoc', 'Thêm môn học: Bài 8: Định hướng chẩn đoán và xử trí trước tình trạng ngất', 'DT_MON_HOC', 32, NULL, '0.0.0.0', '2026-06-13 22:59:32', '2026-06-13 22:59:32', '2026-06-13 22:59:32', NULL, NULL, 0),
(276, 1, 'DT_MonHoc', 'Thêm môn học: Bài 9: Định hướng chẩn đoán và xử trí người bệnh hôn mê', 'DT_MON_HOC', 33, NULL, '0.0.0.0', '2026-06-13 22:59:32', '2026-06-13 22:59:32', '2026-06-13 22:59:32', NULL, NULL, 0),
(277, 1, 'DT_MonHoc', 'Thêm môn học: Bài 10: Định hướng chẩn đoán và xử trí cấp cứu trước một trường hợp ngộ độc', 'DT_MON_HOC', 34, NULL, '0.0.0.0', '2026-06-13 22:59:32', '2026-06-13 22:59:32', '2026-06-13 22:59:32', NULL, NULL, 0),
(278, 1, 'DT_MonHoc', 'Thêm môn học: Bài 11: Định hướng chẩn đoán và xử trí trước tình trạng đau đầu cấp', 'DT_MON_HOC', 35, NULL, '0.0.0.0', '2026-06-13 22:59:32', '2026-06-13 22:59:32', '2026-06-13 22:59:32', NULL, NULL, 0),
(279, 1, 'DT_MonHoc', 'Thêm môn học: Bài 12: Chẩn đoán và xử trí cơn hen phế quản nặng', 'DT_MON_HOC', 36, NULL, '0.0.0.0', '2026-06-13 22:59:32', '2026-06-13 22:59:32', '2026-06-13 22:59:32', NULL, NULL, 0),
(280, 1, 'DT_MonHoc', 'Thêm môn học: Bài 13: Chẩn đoán và xử trí đợt cấp bệnh phổi tắc nghẽn mãn tính', 'DT_MON_HOC', 37, NULL, '0.0.0.0', '2026-06-13 22:59:32', '2026-06-13 22:59:32', '2026-06-13 22:59:32', NULL, NULL, 0),
(281, 1, 'DT_MonHoc', 'Thêm môn học: Bài 14: Định hướng chẩn đoán và xử trí khó thở cấp ở người lớn', 'DT_MON_HOC', 38, NULL, '0.0.0.0', '2026-06-13 22:59:32', '2026-06-13 22:59:32', '2026-06-13 22:59:32', NULL, NULL, 0);
INSERT INTO `dm_nhat_ky_he_thong` (`id`, `nguoi_dung_id`, `module`, `hanh_dong`, `bang_lien_quan`, `id_lien_quan`, `noi_dung_thay_doi`, `dia_chi_ip`, `thoi_gian`, `ngay_tao`, `ngay_cap_nhat`, `nguoi_tao`, `nguoi_cap_nhat`, `da_xoa`) VALUES
(282, 1, 'DT_MonHoc', 'Thêm môn học: Bài 15: Đánh giá và xử trí ban đầu một bệnh nhân đa chấn thương', 'DT_MON_HOC', 39, NULL, '0.0.0.0', '2026-06-13 22:59:32', '2026-06-13 22:59:32', '2026-06-13 22:59:32', NULL, NULL, 0),
(283, 1, 'DT_MonHoc', 'Thêm môn học: Bài 16: Tiếp cận và xử trí ban đầu một bệnh nhân sốc', 'DT_MON_HOC', 40, NULL, '0.0.0.0', '2026-06-13 22:59:32', '2026-06-13 22:59:32', '2026-06-13 22:59:32', NULL, NULL, 0),
(284, 1, 'DT_MonHoc', 'Thêm môn học: Bài 17: Sốc điện cấp cứu', 'DT_MON_HOC', 41, NULL, '0.0.0.0', '2026-06-13 22:59:32', '2026-06-13 22:59:32', '2026-06-13 22:59:32', NULL, NULL, 0),
(285, 1, 'DT_MonHoc', 'Thêm môn học: Bài 18: Phân tích kết quả khí máu động mạch', 'DT_MON_HOC', 42, NULL, '0.0.0.0', '2026-06-13 22:59:32', '2026-06-13 22:59:32', '2026-06-13 22:59:32', NULL, NULL, 0),
(286, 1, 'DT_MonHoc', 'Thêm môn học: Bài 19: Cài đặt ban đầu các thông số máy thở', 'DT_MON_HOC', 43, NULL, '0.0.0.0', '2026-06-13 22:59:32', '2026-06-13 22:59:32', '2026-06-13 22:59:32', NULL, NULL, 0),
(287, 1, 'DT_MonHoc', 'Thêm môn học: Bài 20: Quy trình kỹ thuật đặt nội khí quản cấp cứu', 'DT_MON_HOC', 44, NULL, '0.0.0.0', '2026-06-13 22:59:32', '2026-06-13 22:59:32', '2026-06-13 22:59:32', NULL, NULL, 0),
(288, 1, 'DT_MonHoc', 'Thêm môn học: Bài 21: Quy trình kỹ thuật đặt catheter tĩnh mạch trung tâm nhiều nòng dưới hướng dẫn siêu âm', 'DT_MON_HOC', 45, NULL, '0.0.0.0', '2026-06-13 22:59:32', '2026-06-13 22:59:32', '2026-06-13 22:59:32', NULL, NULL, 0),
(289, 1, 'DT_MonHoc', 'Thêm môn học: Bài 22: Quy trình kỹ thuật đặt catheter tĩnh mạch trung tâm nhiều nòng', 'DT_MON_HOC', 46, NULL, '0.0.0.0', '2026-06-13 22:59:32', '2026-06-13 22:59:32', '2026-06-13 22:59:32', NULL, NULL, 0),
(290, 1, 'DT_MonHoc', 'Thêm môn học: Khai giảng, kiểm tra đầu vào', 'DT_MON_HOC', 47, NULL, '0.0.0.0', '2026-06-13 22:59:32', '2026-06-13 22:59:32', '2026-06-13 22:59:32', NULL, NULL, 0),
(291, 1, 'DT_MonHoc', 'Thêm môn học: Kiểm tra giữa khóa học', 'DT_MON_HOC', 48, NULL, '0.0.0.0', '2026-06-13 22:59:32', '2026-06-13 22:59:32', '2026-06-13 22:59:32', NULL, NULL, 0),
(292, 1, 'DT_MonHoc', 'Thêm môn học: Thi kết thúc khóa học', 'DT_MON_HOC', 49, NULL, '0.0.0.0', '2026-06-13 22:59:32', '2026-06-13 22:59:32', '2026-06-13 22:59:32', NULL, NULL, 0),
(293, 1, 'DT_MonHoc', 'Thêm môn học: Tổng kết khóa học và nhận chứng chỉ', 'DT_MON_HOC', 50, NULL, '0.0.0.0', '2026-06-13 22:59:32', '2026-06-13 22:59:32', '2026-06-13 22:59:32', NULL, NULL, 0),
(294, 9, 'DT_MonHoc', 'Chuyển môn học #9 vào thùng rác', 'DT_MON_HOC', 9, NULL, '127.0.0.1', '2026-06-13 22:59:35', '2026-06-13 22:59:35', '2026-06-13 22:59:35', NULL, NULL, 0),
(295, 9, 'DT_MonHoc', 'Chuyển môn học #10 vào thùng rác', 'DT_MON_HOC', 10, NULL, '127.0.0.1', '2026-06-13 22:59:49', '2026-06-13 22:59:49', '2026-06-13 22:59:49', NULL, NULL, 0),
(296, 9, 'DT_MonHoc', 'Chuyển môn học #11 vào thùng rác', 'DT_MON_HOC', 11, NULL, '127.0.0.1', '2026-06-13 22:59:57', '2026-06-13 22:59:57', '2026-06-13 22:59:57', NULL, NULL, 0),
(297, 9, 'DT_MonHoc', 'Chuyển môn học #12 vào thùng rác', 'DT_MON_HOC', 12, NULL, '127.0.0.1', '2026-06-13 22:59:59', '2026-06-13 22:59:59', '2026-06-13 22:59:59', NULL, NULL, 0),
(298, 9, 'DT_MonHoc', 'Chuyển môn học #13 vào thùng rác', 'DT_MON_HOC', 13, NULL, '127.0.0.1', '2026-06-13 23:00:02', '2026-06-13 23:00:02', '2026-06-13 23:00:02', NULL, NULL, 0),
(299, 9, 'DT_MonHoc', 'Chuyển môn học #14 vào thùng rác', 'DT_MON_HOC', 14, NULL, '127.0.0.1', '2026-06-13 23:00:04', '2026-06-13 23:00:04', '2026-06-13 23:00:04', NULL, NULL, 0),
(300, 9, 'DT_MonHoc', 'Chuyển môn học #15 vào thùng rác', 'DT_MON_HOC', 15, NULL, '127.0.0.1', '2026-06-13 23:00:07', '2026-06-13 23:00:07', '2026-06-13 23:00:07', NULL, NULL, 0),
(301, 9, 'DT_MonHoc', 'Chuyển môn học #16 vào thùng rác', 'DT_MON_HOC', 16, NULL, '127.0.0.1', '2026-06-13 23:00:09', '2026-06-13 23:00:09', '2026-06-13 23:00:09', NULL, NULL, 0),
(302, 9, 'DT_MonHoc', 'Chuyển môn học #17 vào thùng rác', 'DT_MON_HOC', 17, NULL, '127.0.0.1', '2026-06-13 23:00:11', '2026-06-13 23:00:11', '2026-06-13 23:00:11', NULL, NULL, 0),
(303, 9, 'DT_MonHoc', 'Chuyển môn học #18 vào thùng rác', 'DT_MON_HOC', 18, NULL, '127.0.0.1', '2026-06-13 23:00:13', '2026-06-13 23:00:13', '2026-06-13 23:00:13', NULL, NULL, 0),
(304, 9, 'DT_MonHoc', 'Chuyển môn học #19 vào thùng rác', 'DT_MON_HOC', 19, NULL, '127.0.0.1', '2026-06-13 23:00:16', '2026-06-13 23:00:16', '2026-06-13 23:00:16', NULL, NULL, 0),
(305, 9, 'DT_MonHoc', 'Chuyển môn học #20 vào thùng rác', 'DT_MON_HOC', 20, NULL, '127.0.0.1', '2026-06-13 23:00:18', '2026-06-13 23:00:18', '2026-06-13 23:00:18', NULL, NULL, 0),
(306, 9, 'DT_MonHoc', 'Chuyển môn học #21 vào thùng rác', 'DT_MON_HOC', 21, NULL, '127.0.0.1', '2026-06-13 23:00:21', '2026-06-13 23:00:21', '2026-06-13 23:00:21', NULL, NULL, 0),
(307, 9, 'DT_MonHoc', 'Chuyển môn học #22 vào thùng rác', 'DT_MON_HOC', 22, NULL, '127.0.0.1', '2026-06-13 23:00:24', '2026-06-13 23:00:24', '2026-06-13 23:00:24', NULL, NULL, 0),
(308, 9, 'DT_MonHoc', 'Chuyển môn học #23 vào thùng rác', 'DT_MON_HOC', 23, NULL, '127.0.0.1', '2026-06-13 23:00:26', '2026-06-13 23:00:26', '2026-06-13 23:00:26', NULL, NULL, 0),
(309, 9, 'DT_MonHoc', 'Chuyển môn học #24 vào thùng rác', 'DT_MON_HOC', 24, NULL, '127.0.0.1', '2026-06-13 23:00:29', '2026-06-13 23:00:29', '2026-06-13 23:00:29', NULL, NULL, 0),
(310, 9, 'HeThong', 'Đăng xuất', 'DM_NGUOI_DUNG', 9, NULL, '127.0.0.1', '2026-06-13 23:02:40', '2026-06-13 23:02:40', '2026-06-13 23:02:40', NULL, NULL, 0),
(311, 1, 'HeThong', 'Đăng nhập: admin', 'DM_NGUOI_DUNG', 1, NULL, '127.0.0.1', '2026-06-13 23:02:42', '2026-06-13 23:02:42', '2026-06-13 23:02:42', NULL, NULL, 0),
(312, 1, 'HeThong', 'Cập nhật phân quyền nhóm id=4', 'DM_PHAN_QUYEN', 4, NULL, '127.0.0.1', '2026-06-13 23:03:06', '2026-06-13 23:03:06', '2026-06-13 23:03:06', NULL, NULL, 0),
(313, 1, 'HeThong', 'Đăng xuất', 'DM_NGUOI_DUNG', 1, NULL, '127.0.0.1', '2026-06-13 23:03:09', '2026-06-13 23:03:09', '2026-06-13 23:03:09', NULL, NULL, 0),
(314, 9, 'HeThong', 'Đăng nhập: lena', 'DM_NGUOI_DUNG', 9, NULL, '127.0.0.1', '2026-06-13 23:03:11', '2026-06-13 23:03:11', '2026-06-13 23:03:11', NULL, NULL, 0),
(315, 9, 'HeThong', 'Đăng xuất', 'DM_NGUOI_DUNG', 9, NULL, '127.0.0.1', '2026-06-13 23:03:37', '2026-06-13 23:03:37', '2026-06-13 23:03:37', NULL, NULL, 0),
(316, 1, 'HeThong', 'Đăng nhập: admin', 'DM_NGUOI_DUNG', 1, NULL, '127.0.0.1', '2026-06-13 23:03:39', '2026-06-13 23:03:39', '2026-06-13 23:03:39', NULL, NULL, 0),
(317, 1, 'HeThong', 'Sửa người dùng: lena', 'DM_NGUOI_DUNG', 9, NULL, '127.0.0.1', '2026-06-13 23:03:53', '2026-06-13 23:03:53', '2026-06-13 23:03:53', NULL, NULL, 0),
(318, 1, 'DT_ChuongTrinhMonHoc', 'Thêm môn \'Bài 1: Phân loại bệnh nhân cấp cứu theo mức độ ưu tiên\' vào CTĐT \'Kỹ thuật cấp cứu cơ bản - Bác sĩ\'', 'DT_CHUONG_TRINH_MON_HOC', 2, NULL, '127.0.0.1', '2026-06-13 23:04:08', '2026-06-13 23:04:08', '2026-06-13 23:04:08', NULL, NULL, 0),
(319, 1, 'HeThong', 'Đăng xuất', 'DM_NGUOI_DUNG', 1, NULL, '127.0.0.1', '2026-06-13 23:04:13', '2026-06-13 23:04:13', '2026-06-13 23:04:13', NULL, NULL, 0),
(320, 9, 'HeThong', 'Đăng nhập: lena', 'DM_NGUOI_DUNG', 9, NULL, '127.0.0.1', '2026-06-13 23:04:14', '2026-06-13 23:04:14', '2026-06-13 23:04:14', NULL, NULL, 0),
(321, 9, 'HeThong', 'Đăng xuất', 'DM_NGUOI_DUNG', 9, NULL, '127.0.0.1', '2026-06-13 23:04:37', '2026-06-13 23:04:37', '2026-06-13 23:04:37', NULL, NULL, 0),
(322, 1, 'HeThong', 'Đăng nhập: admin', 'DM_NGUOI_DUNG', 1, NULL, '127.0.0.1', '2026-06-13 23:04:39', '2026-06-13 23:04:39', '2026-06-13 23:04:39', NULL, NULL, 0),
(323, 1, 'HeThong', 'Cập nhật phân quyền nhóm id=4', 'DM_PHAN_QUYEN', 4, NULL, '127.0.0.1', '2026-06-13 23:04:53', '2026-06-13 23:04:53', '2026-06-13 23:04:53', NULL, NULL, 0),
(324, 1, 'HeThong', 'Cập nhật phân quyền nhóm id=4', 'DM_PHAN_QUYEN', 4, NULL, '127.0.0.1', '2026-06-13 23:04:54', '2026-06-13 23:04:54', '2026-06-13 23:04:54', NULL, NULL, 0),
(325, 1, 'HeThong', 'Sửa người dùng: lena', 'DM_NGUOI_DUNG', 9, NULL, '127.0.0.1', '2026-06-13 23:05:08', '2026-06-13 23:05:08', '2026-06-13 23:05:08', NULL, NULL, 0),
(326, 1, 'HeThong', 'Sửa người dùng: lena', 'DM_NGUOI_DUNG', 9, NULL, '127.0.0.1', '2026-06-13 23:05:12', '2026-06-13 23:05:12', '2026-06-13 23:05:12', NULL, NULL, 0),
(327, 1, 'HeThong', 'Cập nhật phân quyền nhóm id=4', 'DM_PHAN_QUYEN', 4, NULL, '127.0.0.1', '2026-06-13 23:05:28', '2026-06-13 23:05:28', '2026-06-13 23:05:28', NULL, NULL, 0),
(328, 1, 'HeThong', 'Đăng xuất', 'DM_NGUOI_DUNG', 1, NULL, '127.0.0.1', '2026-06-13 23:05:37', '2026-06-13 23:05:37', '2026-06-13 23:05:37', NULL, NULL, 0),
(329, 9, 'HeThong', 'Đăng nhập: lena', 'DM_NGUOI_DUNG', 9, NULL, '127.0.0.1', '2026-06-13 23:05:40', '2026-06-13 23:05:40', '2026-06-13 23:05:40', NULL, NULL, 0),
(330, 9, 'DT_ChuongTrinhMonHoc', 'Thêm môn \'Tổng kết khóa học và nhận chứng chỉ\' vào CTĐT \'Kỹ thuật cấp cứu cơ bản - Bác sĩ\'', 'DT_CHUONG_TRINH_MON_HOC', 3, NULL, '127.0.0.1', '2026-06-13 23:10:29', '2026-06-13 23:10:29', '2026-06-13 23:10:29', NULL, NULL, 0),
(331, 9, 'DT_ChuongTrinhMonHoc', 'Thêm môn \'Thi kết thúc khóa học\' vào CTĐT \'Kỹ thuật cấp cứu cơ bản - Bác sĩ\'', 'DT_CHUONG_TRINH_MON_HOC', 4, NULL, '127.0.0.1', '2026-06-13 23:10:43', '2026-06-13 23:10:43', '2026-06-13 23:10:43', NULL, NULL, 0),
(332, 9, 'DT_ChuongTrinhMonHoc', 'Thêm môn \'Bài 10: Định hướng chẩn đoán và xử trí cấp cứu trước một trường hợp ngộ độc\' vào CTĐT \'Kỹ thuật cấp cứu cơ bản - Bác sĩ\'', 'DT_CHUONG_TRINH_MON_HOC', 5, NULL, '127.0.0.1', '2026-06-13 23:11:24', '2026-06-13 23:11:24', '2026-06-13 23:11:24', NULL, NULL, 0),
(333, 9, 'DT_ChuongTrinhMonHoc', 'Thêm môn \'Bài 11: Định hướng chẩn đoán và xử trí trước tình trạng đau đầu cấp\' vào CTĐT \'Kỹ thuật cấp cứu cơ bản - Bác sĩ\'', 'DT_CHUONG_TRINH_MON_HOC', 6, NULL, '127.0.0.1', '2026-06-13 23:12:25', '2026-06-13 23:12:25', '2026-06-13 23:12:25', NULL, NULL, 0),
(334, 9, 'DT_ChuongTrinhMonHoc', 'Thêm môn \'Bài 12: Chẩn đoán và xử trí cơn hen phế quản nặng\' vào CTĐT \'Kỹ thuật cấp cứu cơ bản - Bác sĩ\'', 'DT_CHUONG_TRINH_MON_HOC', 7, NULL, '127.0.0.1', '2026-06-13 23:12:27', '2026-06-13 23:12:27', '2026-06-13 23:12:27', NULL, NULL, 0),
(335, 9, 'DT_ChuongTrinhMonHoc', 'Thêm môn \'Bài 13: Chẩn đoán và xử trí đợt cấp bệnh phổi tắc nghẽn mãn tính\' vào CTĐT \'Kỹ thuật cấp cứu cơ bản - Bác sĩ\'', 'DT_CHUONG_TRINH_MON_HOC', 8, NULL, '127.0.0.1', '2026-06-13 23:12:30', '2026-06-13 23:12:30', '2026-06-13 23:12:30', NULL, NULL, 0),
(336, 9, 'DT_ChuongTrinhMonHoc', 'Thêm môn \'Bài 14: Định hướng chẩn đoán và xử trí khó thở cấp ở người lớn\' vào CTĐT \'Kỹ thuật cấp cứu cơ bản - Bác sĩ\'', 'DT_CHUONG_TRINH_MON_HOC', 9, NULL, '127.0.0.1', '2026-06-13 23:12:34', '2026-06-13 23:12:34', '2026-06-13 23:12:34', NULL, NULL, 0),
(337, 9, 'DT_ChuongTrinhMonHoc', 'Thêm môn \'Bài 15: Đánh giá và xử trí ban đầu một bệnh nhân đa chấn thương\' vào CTĐT \'Kỹ thuật cấp cứu cơ bản - Bác sĩ\'', 'DT_CHUONG_TRINH_MON_HOC', 10, NULL, '127.0.0.1', '2026-06-13 23:12:37', '2026-06-13 23:12:37', '2026-06-13 23:12:37', NULL, NULL, 0),
(338, 9, 'DT_ChuongTrinhMonHoc', 'Thêm môn \'Bài 16: Tiếp cận và xử trí ban đầu một bệnh nhân sốc\' vào CTĐT \'Kỹ thuật cấp cứu cơ bản - Bác sĩ\'', 'DT_CHUONG_TRINH_MON_HOC', 11, NULL, '127.0.0.1', '2026-06-13 23:12:39', '2026-06-13 23:12:39', '2026-06-13 23:12:39', NULL, NULL, 0),
(339, 9, 'DT_ChuongTrinhMonHoc', 'Thêm môn \'Bài 17: Sốc điện cấp cứu\' vào CTĐT \'Kỹ thuật cấp cứu cơ bản - Bác sĩ\'', 'DT_CHUONG_TRINH_MON_HOC', 12, NULL, '127.0.0.1', '2026-06-13 23:12:42', '2026-06-13 23:12:42', '2026-06-13 23:12:42', NULL, NULL, 0),
(340, 9, 'DT_ChuongTrinhMonHoc', 'Thêm môn \'Bài 18: Phân tích kết quả khí máu động mạch\' vào CTĐT \'Kỹ thuật cấp cứu cơ bản - Bác sĩ\'', 'DT_CHUONG_TRINH_MON_HOC', 13, NULL, '127.0.0.1', '2026-06-13 23:12:44', '2026-06-13 23:12:44', '2026-06-13 23:12:44', NULL, NULL, 0),
(341, 9, 'DT_ChuongTrinhMonHoc', 'Thêm môn \'Bài 19: Cài đặt ban đầu các thông số máy thở\' vào CTĐT \'Kỹ thuật cấp cứu cơ bản - Bác sĩ\'', 'DT_CHUONG_TRINH_MON_HOC', 14, NULL, '127.0.0.1', '2026-06-13 23:12:48', '2026-06-13 23:12:48', '2026-06-13 23:12:48', NULL, NULL, 0),
(342, 9, 'DT_ChuongTrinhMonHoc', 'Thêm môn \'Kiểm tra giữa khóa học\' vào CTĐT \'Kỹ thuật cấp cứu cơ bản - Bác sĩ\'', 'DT_CHUONG_TRINH_MON_HOC', 15, NULL, '127.0.0.1', '2026-06-13 23:12:59', '2026-06-13 23:12:59', '2026-06-13 23:12:59', NULL, NULL, 0),
(343, 9, 'DT_ChuongTrinhMonHoc', 'Thêm môn \'Khai giảng, kiểm tra đầu vào\' vào CTĐT \'Kỹ thuật cấp cứu cơ bản - Bác sĩ\'', 'DT_CHUONG_TRINH_MON_HOC', 16, NULL, '127.0.0.1', '2026-06-13 23:13:04', '2026-06-13 23:13:04', '2026-06-13 23:13:04', NULL, NULL, 0),
(344, 9, 'DT_ChuongTrinhMonHoc', 'Thêm môn \'Bài 2: Hồi sinh tim phổi cơ bản\' vào CTĐT \'Kỹ thuật cấp cứu cơ bản - Bác sĩ\'', 'DT_CHUONG_TRINH_MON_HOC', 17, NULL, '127.0.0.1', '2026-06-13 23:13:17', '2026-06-13 23:13:17', '2026-06-13 23:13:17', NULL, NULL, 0),
(345, 9, 'DT_ChuongTrinhMonHoc', 'Thêm môn \'Bài 20: Quy trình kỹ thuật đặt nội khí quản cấp cứu\' vào CTĐT \'Kỹ thuật cấp cứu cơ bản - Bác sĩ\'', 'DT_CHUONG_TRINH_MON_HOC', 18, NULL, '127.0.0.1', '2026-06-13 23:13:32', '2026-06-13 23:13:32', '2026-06-13 23:13:32', NULL, NULL, 0),
(346, 9, 'DT_ChuongTrinhMonHoc', 'Thêm môn \'Bài 22: Quy trình kỹ thuật đặt catheter tĩnh mạch trung tâm nhiều nòng\' vào CTĐT \'Kỹ thuật cấp cứu cơ bản - Bác sĩ\'', 'DT_CHUONG_TRINH_MON_HOC', 19, NULL, '127.0.0.1', '2026-06-13 23:13:43', '2026-06-13 23:13:43', '2026-06-13 23:13:43', NULL, NULL, 0),
(347, 9, 'DT_ChuongTrinhMonHoc', 'Thêm môn \'Bài 21: Quy trình kỹ thuật đặt catheter tĩnh mạch trung tâm nhiều nòng dưới hướng dẫn siêu âm\' vào CTĐT \'Kỹ thuật cấp cứu cơ bản - Bác sĩ\'', 'DT_CHUONG_TRINH_MON_HOC', 20, NULL, '127.0.0.1', '2026-06-13 23:14:02', '2026-06-13 23:14:02', '2026-06-13 23:14:02', NULL, NULL, 0),
(348, 9, 'HeThong', 'Đăng xuất', 'DM_NGUOI_DUNG', 9, NULL, '127.0.0.1', '2026-06-13 23:27:07', '2026-06-13 23:27:07', '2026-06-13 23:27:07', NULL, NULL, 0),
(349, 1, 'HeThong', 'Đăng nhập: admin', 'DM_NGUOI_DUNG', 1, NULL, '127.0.0.1', '2026-06-13 23:27:09', '2026-06-13 23:27:09', '2026-06-13 23:27:09', NULL, NULL, 0),
(350, 1, 'HeThong', 'Xóa cache danh mục/combo', 'CACHE', 0, NULL, '127.0.0.1', '2026-06-13 23:27:26', '2026-06-13 23:27:26', '2026-06-13 23:27:26', NULL, NULL, 0),
(351, 1, 'HeThong', 'Xóa cache phân quyền', 'CACHE', 0, NULL, '127.0.0.1', '2026-06-13 23:27:28', '2026-06-13 23:27:28', '2026-06-13 23:27:28', NULL, NULL, 0),
(352, 1, 'DT_MonHoc', 'Thêm môn học: Bài test thứ tự', 'DT_MON_HOC', 51, NULL, '0.0.0.0', '2026-06-13 23:33:02', '2026-06-13 23:33:02', '2026-06-13 23:33:02', NULL, NULL, 0),
(353, 1, 'DT_MonHoc', 'Cập nhật môn học: Bài 3: Cấp cứu phản vệ', 'DT_MON_HOC', 27, NULL, '127.0.0.1', '2026-06-13 23:35:04', '2026-06-13 23:35:04', '2026-06-13 23:35:04', NULL, NULL, 0),
(354, 1, 'DT_MonHoc', 'Cập nhật môn học: Bài 5: Định hướng chẩn đoán và xử trí người bệnh đau ngực cấp', 'DT_MON_HOC', 29, NULL, '127.0.0.1', '2026-06-13 23:35:23', '2026-06-13 23:35:23', '2026-06-13 23:35:23', NULL, NULL, 0),
(355, 1, 'DT_MonHoc', 'Cập nhật môn học: Bài 4: Chẩn đoán và xử trí ngộ độc thuốc tê', 'DT_MON_HOC', 28, NULL, '127.0.0.1', '2026-06-13 23:35:36', '2026-06-13 23:35:36', '2026-06-13 23:35:36', NULL, NULL, 0),
(356, 1, 'DT_MonHoc', 'Cập nhật môn học: Bài 6: Định hướng chẩn đoán và xử trí đau bụng cấp ở người lớn', 'DT_MON_HOC', 30, NULL, '127.0.0.1', '2026-06-13 23:35:47', '2026-06-13 23:35:47', '2026-06-13 23:35:47', NULL, NULL, 0),
(357, 1, 'DT_MonHoc', 'Cập nhật môn học: Bài 7: Chẩn đoán và xử trí bệnh nhân xuất huyết tiêu hóa cao', 'DT_MON_HOC', 31, NULL, '127.0.0.1', '2026-06-13 23:35:56', '2026-06-13 23:35:56', '2026-06-13 23:35:56', NULL, NULL, 0),
(358, 1, 'DT_MonHoc', 'Cập nhật môn học: Bài 8: Định hướng chẩn đoán và xử trí trước tình trạng ngất', 'DT_MON_HOC', 32, NULL, '127.0.0.1', '2026-06-13 23:36:06', '2026-06-13 23:36:06', '2026-06-13 23:36:06', NULL, NULL, 0),
(359, 1, 'DT_MonHoc', 'Cập nhật môn học: Bài 9: Định hướng chẩn đoán và xử trí người bệnh hôn mê', 'DT_MON_HOC', 33, NULL, '127.0.0.1', '2026-06-13 23:36:21', '2026-06-13 23:36:21', '2026-06-13 23:36:21', NULL, NULL, 0),
(360, 1, 'DT_MonHoc', 'Cập nhật môn học: Tổng kết khóa học và nhận chứng chỉ', 'DT_MON_HOC', 50, NULL, '127.0.0.1', '2026-06-13 23:36:33', '2026-06-13 23:36:33', '2026-06-13 23:36:33', NULL, NULL, 0),
(361, 1, 'DT_MonHoc', 'Cập nhật môn học: Thi kết thúc khóa học', 'DT_MON_HOC', 49, NULL, '127.0.0.1', '2026-06-13 23:36:40', '2026-06-13 23:36:40', '2026-06-13 23:36:40', NULL, NULL, 0),
(362, 1, 'DT_MonHoc', 'Cập nhật môn học: Bài 10: Định hướng chẩn đoán và xử trí cấp cứu trước một trường hợp ngộ độc', 'DT_MON_HOC', 34, NULL, '127.0.0.1', '2026-06-13 23:36:47', '2026-06-13 23:36:47', '2026-06-13 23:36:47', NULL, NULL, 0),
(363, 1, 'DT_MonHoc', 'Cập nhật môn học: Bài 11: Định hướng chẩn đoán và xử trí trước tình trạng đau đầu cấp', 'DT_MON_HOC', 35, NULL, '127.0.0.1', '2026-06-13 23:36:52', '2026-06-13 23:36:52', '2026-06-13 23:36:52', NULL, NULL, 0),
(364, 1, 'DT_MonHoc', 'Cập nhật môn học: Bài 12: Chẩn đoán và xử trí cơn hen phế quản nặng', 'DT_MON_HOC', 36, NULL, '127.0.0.1', '2026-06-13 23:36:57', '2026-06-13 23:36:57', '2026-06-13 23:36:57', NULL, NULL, 0),
(365, 1, 'DT_MonHoc', 'Cập nhật môn học: Bài 13: Chẩn đoán và xử trí đợt cấp bệnh phổi tắc nghẽn mãn tính', 'DT_MON_HOC', 37, NULL, '127.0.0.1', '2026-06-13 23:37:03', '2026-06-13 23:37:03', '2026-06-13 23:37:03', NULL, NULL, 0),
(366, 1, 'DT_MonHoc', 'Cập nhật môn học: Bài 14: Định hướng chẩn đoán và xử trí khó thở cấp ở người lớn', 'DT_MON_HOC', 38, NULL, '127.0.0.1', '2026-06-13 23:37:12', '2026-06-13 23:37:12', '2026-06-13 23:37:12', NULL, NULL, 0),
(367, 1, 'DT_MonHoc', 'Cập nhật môn học: Bài 15: Đánh giá và xử trí ban đầu một bệnh nhân đa chấn thương', 'DT_MON_HOC', 39, NULL, '127.0.0.1', '2026-06-13 23:37:16', '2026-06-13 23:37:16', '2026-06-13 23:37:16', NULL, NULL, 0),
(368, 1, 'DT_MonHoc', 'Cập nhật môn học: Bài 16: Tiếp cận và xử trí ban đầu một bệnh nhân sốc', 'DT_MON_HOC', 40, NULL, '127.0.0.1', '2026-06-13 23:37:24', '2026-06-13 23:37:24', '2026-06-13 23:37:24', NULL, NULL, 0),
(369, 1, 'DT_MonHoc', 'Cập nhật môn học: Bài 17: Sốc điện cấp cứu', 'DT_MON_HOC', 41, NULL, '127.0.0.1', '2026-06-13 23:37:29', '2026-06-13 23:37:29', '2026-06-13 23:37:29', NULL, NULL, 0),
(370, 1, 'DT_MonHoc', 'Cập nhật môn học: Bài 18: Phân tích kết quả khí máu động mạch', 'DT_MON_HOC', 42, NULL, '127.0.0.1', '2026-06-13 23:37:34', '2026-06-13 23:37:34', '2026-06-13 23:37:34', NULL, NULL, 0),
(371, 1, 'DT_MonHoc', 'Cập nhật môn học: Bài 19: Cài đặt ban đầu các thông số máy thở', 'DT_MON_HOC', 43, NULL, '127.0.0.1', '2026-06-13 23:37:40', '2026-06-13 23:37:40', '2026-06-13 23:37:40', NULL, NULL, 0),
(372, 1, 'DT_MonHoc', 'Cập nhật môn học: Kiểm tra giữa khóa học', 'DT_MON_HOC', 48, NULL, '127.0.0.1', '2026-06-13 23:37:49', '2026-06-13 23:37:49', '2026-06-13 23:37:49', NULL, NULL, 0),
(373, 1, 'DT_MonHoc', 'Cập nhật môn học: Bài 20: Quy trình kỹ thuật đặt nội khí quản cấp cứu', 'DT_MON_HOC', 44, NULL, '127.0.0.1', '2026-06-13 23:37:57', '2026-06-13 23:37:57', '2026-06-13 23:37:57', NULL, NULL, 0),
(374, 1, 'DT_MonHoc', 'Cập nhật môn học: Bài 21: Quy trình kỹ thuật đặt catheter tĩnh mạch trung tâm nhiều nòng dưới hướng dẫn siêu âm', 'DT_MON_HOC', 45, NULL, '127.0.0.1', '2026-06-13 23:38:10', '2026-06-13 23:38:10', '2026-06-13 23:38:10', NULL, NULL, 0),
(375, 1, 'DT_MonHoc', 'Cập nhật môn học: Bài 22: Quy trình kỹ thuật đặt catheter tĩnh mạch trung tâm nhiều nòng', 'DT_MON_HOC', 46, NULL, '127.0.0.1', '2026-06-13 23:38:18', '2026-06-13 23:38:18', '2026-06-13 23:38:18', NULL, NULL, 0),
(376, 1, 'DT_MonHoc', 'Cập nhật môn học: Khai giảng, kiểm tra đầu vào', 'DT_MON_HOC', 47, NULL, '127.0.0.1', '2026-06-13 23:38:24', '2026-06-13 23:38:24', '2026-06-13 23:38:24', NULL, NULL, 0),
(377, 1, 'DT_MonHoc', 'Cập nhật môn học: Bài 2: Hồi sinh tim phổi cơ bản', 'DT_MON_HOC', 26, NULL, '127.0.0.1', '2026-06-13 23:39:04', '2026-06-13 23:39:04', '2026-06-13 23:39:04', NULL, NULL, 0),
(378, 1, 'HeThong', 'Xóa tạm chương trình id=13', 'DT_CHUONG_TRINH', 13, NULL, '127.0.0.1', '2026-06-13 23:39:59', '2026-06-13 23:39:59', '2026-06-13 23:39:59', NULL, NULL, 0),
(379, 1, 'HeThong', 'Đăng nhập: admin', 'DM_NGUOI_DUNG', 1, NULL, '127.0.0.1', '2026-06-14 08:46:59', '2026-06-14 08:46:59', '2026-06-14 08:46:59', NULL, NULL, 0),
(380, 1, 'HeThong', 'Xóa tạm HV id=16', 'DM_HOC_VIEN', 16, NULL, '127.0.0.1', '2026-06-14 08:47:44', '2026-06-14 08:47:44', '2026-06-14 08:47:44', NULL, NULL, 0),
(381, 1, 'HeThong', 'Xóa tạm HV id=15', 'DM_HOC_VIEN', 15, NULL, '127.0.0.1', '2026-06-14 08:47:46', '2026-06-14 08:47:46', '2026-06-14 08:47:46', NULL, NULL, 0),
(382, 1, 'HeThong', 'Xóa tạm HV id=14', 'DM_HOC_VIEN', 14, NULL, '127.0.0.1', '2026-06-14 08:47:47', '2026-06-14 08:47:47', '2026-06-14 08:47:47', NULL, NULL, 0),
(383, 1, 'HeThong', 'Xóa tạm HV id=13', 'DM_HOC_VIEN', 13, NULL, '127.0.0.1', '2026-06-14 08:47:49', '2026-06-14 08:47:49', '2026-06-14 08:47:49', NULL, NULL, 0),
(384, 1, 'HeThong', 'Xóa tạm HV id=12', 'DM_HOC_VIEN', 12, NULL, '127.0.0.1', '2026-06-14 08:47:51', '2026-06-14 08:47:51', '2026-06-14 08:47:51', NULL, NULL, 0),
(385, 1, 'HeThong', 'Xóa tạm HV id=11', 'DM_HOC_VIEN', 11, NULL, '127.0.0.1', '2026-06-14 08:47:53', '2026-06-14 08:47:53', '2026-06-14 08:47:53', NULL, NULL, 0),
(386, 1, 'HeThong', 'Xóa tạm HV id=10', 'DM_HOC_VIEN', 10, NULL, '127.0.0.1', '2026-06-14 08:47:55', '2026-06-14 08:47:55', '2026-06-14 08:47:55', NULL, NULL, 0),
(387, 1, 'HeThong', 'Xóa tạm HV id=9', 'DM_HOC_VIEN', 9, NULL, '127.0.0.1', '2026-06-14 08:47:57', '2026-06-14 08:47:57', '2026-06-14 08:47:57', NULL, NULL, 0),
(388, 1, 'HeThong', 'Xóa tạm HV id=8', 'DM_HOC_VIEN', 8, NULL, '127.0.0.1', '2026-06-14 08:47:59', '2026-06-14 08:47:59', '2026-06-14 08:47:59', NULL, NULL, 0),
(389, 1, 'HeThong', 'Xóa tạm HV id=7', 'DM_HOC_VIEN', 7, NULL, '127.0.0.1', '2026-06-14 08:48:00', '2026-06-14 08:48:00', '2026-06-14 08:48:00', NULL, NULL, 0),
(390, 1, 'HeThong', 'Xóa tạm HV id=6', 'DM_HOC_VIEN', 6, NULL, '127.0.0.1', '2026-06-14 08:48:02', '2026-06-14 08:48:02', '2026-06-14 08:48:02', NULL, NULL, 0),
(391, 1, 'HeThong', 'Xóa tạm HV id=5', 'DM_HOC_VIEN', 5, NULL, '127.0.0.1', '2026-06-14 08:48:04', '2026-06-14 08:48:04', '2026-06-14 08:48:04', NULL, NULL, 0),
(392, 1, 'HeThong', 'Xóa tạm HV id=4', 'DM_HOC_VIEN', 4, NULL, '127.0.0.1', '2026-06-14 08:48:05', '2026-06-14 08:48:05', '2026-06-14 08:48:05', NULL, NULL, 0),
(393, 1, 'HeThong', 'Xóa tạm HV id=3', 'DM_HOC_VIEN', 3, NULL, '127.0.0.1', '2026-06-14 08:48:07', '2026-06-14 08:48:07', '2026-06-14 08:48:07', NULL, NULL, 0),
(394, 1, 'HeThong', 'Xóa tạm HV id=2', 'DM_HOC_VIEN', 2, NULL, '127.0.0.1', '2026-06-14 08:48:08', '2026-06-14 08:48:08', '2026-06-14 08:48:08', NULL, NULL, 0),
(395, 1, 'HeThong', 'Xóa tạm HV id=1', 'DM_HOC_VIEN', 1, NULL, '127.0.0.1', '2026-06-14 08:48:11', '2026-06-14 08:48:11', '2026-06-14 08:48:11', NULL, NULL, 0),
(396, 1, 'HeThong', 'Đăng nhập: admin', 'DM_NGUOI_DUNG', 1, NULL, '127.0.0.1', '2026-06-14 08:48:49', '2026-06-14 08:48:49', '2026-06-14 08:48:49', NULL, NULL, 0),
(397, 1, 'HeThong', 'Thêm HV: Nguyễn Văn Đức', 'DM_HOC_VIEN', 17, NULL, '127.0.0.1', '2026-06-14 08:50:29', '2026-06-14 08:50:29', '2026-06-14 08:50:29', NULL, NULL, 0),
(398, 1, 'DT_KhoaHoc', 'Thêm khóa học: Khoá học tháng 9 2026', 'DT_KHOA_HOC', 9, NULL, '127.0.0.1', '2026-06-14 08:54:56', '2026-06-14 08:54:56', '2026-06-14 08:54:56', NULL, NULL, 0),
(399, 1, 'DT_ChuongTrinh', 'Gắn khóa #9 vào CTĐT #14', 'DT_KHOA_HOC_CHUONG_TRINH', 22, NULL, '127.0.0.1', '2026-06-14 08:55:32', '2026-06-14 08:55:32', '2026-06-14 08:55:32', NULL, NULL, 0),
(400, 1, 'DT_ChuongTrinh', 'Gắn khóa #9 vào CTĐT #15', 'DT_KHOA_HOC_CHUONG_TRINH', 23, NULL, '127.0.0.1', '2026-06-14 08:55:42', '2026-06-14 08:55:42', '2026-06-14 08:55:42', NULL, NULL, 0),
(401, 1, 'HeThong', 'Xóa toàn bộ cache hệ thống', 'CACHE', 0, NULL, '127.0.0.1', '2026-06-14 09:02:09', '2026-06-14 09:02:09', '2026-06-14 09:02:09', NULL, NULL, 0),
(402, 1, 'DT_ChuongTrinh', 'Gắn khóa #9 vào CTĐT #40', 'DT_KHOA_HOC_CHUONG_TRINH', 24, NULL, '127.0.0.1', '2026-06-14 09:06:25', '2026-06-14 09:06:25', '2026-06-14 09:06:25', NULL, NULL, 0),
(403, 1, 'HeThong', 'Xóa tạm BKT id=14', 'DT_BAI_KIEM_TRA', 14, NULL, '127.0.0.1', '2026-06-14 09:09:17', '2026-06-14 09:09:17', '2026-06-14 09:09:17', NULL, NULL, 0),
(404, 1, 'HeThong', 'Xóa tạm BKT id=13', 'DT_BAI_KIEM_TRA', 13, NULL, '127.0.0.1', '2026-06-14 09:09:18', '2026-06-14 09:09:18', '2026-06-14 09:09:18', NULL, NULL, 0),
(405, 1, 'HeThong', 'Xóa tạm BKT id=12', 'DT_BAI_KIEM_TRA', 12, NULL, '127.0.0.1', '2026-06-14 09:09:20', '2026-06-14 09:09:20', '2026-06-14 09:09:20', NULL, NULL, 0),
(406, 1, 'HeThong', 'Xóa tạm BKT id=11', 'DT_BAI_KIEM_TRA', 11, NULL, '127.0.0.1', '2026-06-14 09:09:21', '2026-06-14 09:09:21', '2026-06-14 09:09:21', NULL, NULL, 0),
(407, 1, 'HeThong', 'Xóa tạm BKT id=10', 'DT_BAI_KIEM_TRA', 10, NULL, '127.0.0.1', '2026-06-14 09:09:23', '2026-06-14 09:09:23', '2026-06-14 09:09:23', NULL, NULL, 0),
(408, 1, 'HeThong', 'Xóa tạm BKT id=9', 'DT_BAI_KIEM_TRA', 9, NULL, '127.0.0.1', '2026-06-14 09:09:24', '2026-06-14 09:09:24', '2026-06-14 09:09:24', NULL, NULL, 0),
(409, 1, 'HeThong', 'Xóa tạm BKT id=8', 'DT_BAI_KIEM_TRA', 8, NULL, '127.0.0.1', '2026-06-14 09:09:25', '2026-06-14 09:09:25', '2026-06-14 09:09:25', NULL, NULL, 0),
(410, 1, 'HeThong', 'Xóa tạm BKT id=7', 'DT_BAI_KIEM_TRA', 7, NULL, '127.0.0.1', '2026-06-14 09:09:27', '2026-06-14 09:09:27', '2026-06-14 09:09:27', NULL, NULL, 0),
(411, 1, 'HeThong', 'Xóa tạm BKT id=6', 'DT_BAI_KIEM_TRA', 6, NULL, '127.0.0.1', '2026-06-14 09:09:28', '2026-06-14 09:09:28', '2026-06-14 09:09:28', NULL, NULL, 0),
(412, 1, 'HeThong', 'Xóa tạm BKT id=5', 'DT_BAI_KIEM_TRA', 5, NULL, '127.0.0.1', '2026-06-14 09:09:30', '2026-06-14 09:09:30', '2026-06-14 09:09:30', NULL, NULL, 0),
(413, 1, 'HeThong', 'Xóa tạm BKT id=4', 'DT_BAI_KIEM_TRA', 4, NULL, '127.0.0.1', '2026-06-14 09:09:32', '2026-06-14 09:09:32', '2026-06-14 09:09:32', NULL, NULL, 0),
(414, 1, 'HeThong', 'Xóa tạm BKT id=3', 'DT_BAI_KIEM_TRA', 3, NULL, '127.0.0.1', '2026-06-14 09:09:33', '2026-06-14 09:09:33', '2026-06-14 09:09:33', NULL, NULL, 0),
(415, 1, 'HeThong', 'Xóa tạm BKT id=2', 'DT_BAI_KIEM_TRA', 2, NULL, '127.0.0.1', '2026-06-14 09:09:35', '2026-06-14 09:09:35', '2026-06-14 09:09:35', NULL, NULL, 0),
(416, 1, 'HeThong', 'Xóa tạm BKT id=1', 'DT_BAI_KIEM_TRA', 1, NULL, '127.0.0.1', '2026-06-14 09:09:37', '2026-06-14 09:09:37', '2026-06-14 09:09:37', NULL, NULL, 0),
(417, 1, 'HeThong', 'Xóa tạm BKT id=15', 'DT_BAI_KIEM_TRA', 15, NULL, '127.0.0.1', '2026-06-14 09:09:38', '2026-06-14 09:09:38', '2026-06-14 09:09:38', NULL, NULL, 0),
(418, 1, 'HeThong', 'Xóa tạm BKT id=16', 'DT_BAI_KIEM_TRA', 16, NULL, '127.0.0.1', '2026-06-14 09:09:40', '2026-06-14 09:09:40', '2026-06-14 09:09:40', NULL, NULL, 0),
(419, 1, 'HeThong', 'Xóa tạm BKT id=17', 'DT_BAI_KIEM_TRA', 17, NULL, '127.0.0.1', '2026-06-14 09:09:42', '2026-06-14 09:09:42', '2026-06-14 09:09:42', NULL, NULL, 0),
(420, 1, 'HeThong', 'Xóa tạm BKT id=18', 'DT_BAI_KIEM_TRA', 18, NULL, '127.0.0.1', '2026-06-14 09:09:43', '2026-06-14 09:09:43', '2026-06-14 09:09:43', NULL, NULL, 0),
(421, 1, 'HeThong', 'Xóa tạm BKT id=19', 'DT_BAI_KIEM_TRA', 19, NULL, '127.0.0.1', '2026-06-14 09:09:45', '2026-06-14 09:09:45', '2026-06-14 09:09:45', NULL, NULL, 0),
(422, 1, 'HeThong', 'Xóa tạm BKT id=21', 'DT_BAI_KIEM_TRA', 21, NULL, '127.0.0.1', '2026-06-14 09:09:46', '2026-06-14 09:09:46', '2026-06-14 09:09:46', NULL, NULL, 0),
(423, 1, 'HeThong', 'Xóa tạm BKT id=33', 'DT_BAI_KIEM_TRA', 33, NULL, '127.0.0.1', '2026-06-14 09:09:56', '2026-06-14 09:09:56', '2026-06-14 09:09:56', NULL, NULL, 0),
(424, 1, 'HeThong', 'Xóa tạm BKT id=32', 'DT_BAI_KIEM_TRA', 32, NULL, '127.0.0.1', '2026-06-14 09:10:01', '2026-06-14 09:10:01', '2026-06-14 09:10:01', NULL, NULL, 0),
(425, 1, 'HeThong', 'Xóa tạm BKT id=31', 'DT_BAI_KIEM_TRA', 31, NULL, '127.0.0.1', '2026-06-14 09:10:02', '2026-06-14 09:10:02', '2026-06-14 09:10:02', NULL, NULL, 0),
(426, 1, 'HeThong', 'Xóa tạm BKT id=30', 'DT_BAI_KIEM_TRA', 30, NULL, '127.0.0.1', '2026-06-14 09:10:04', '2026-06-14 09:10:04', '2026-06-14 09:10:04', NULL, NULL, 0),
(427, 1, 'HeThong', 'Xóa tạm BKT id=29', 'DT_BAI_KIEM_TRA', 29, NULL, '127.0.0.1', '2026-06-14 09:10:06', '2026-06-14 09:10:06', '2026-06-14 09:10:06', NULL, NULL, 0),
(428, 1, 'HeThong', 'Xóa tạm BKT id=28', 'DT_BAI_KIEM_TRA', 28, NULL, '127.0.0.1', '2026-06-14 09:10:07', '2026-06-14 09:10:07', '2026-06-14 09:10:07', NULL, NULL, 0),
(429, 1, 'HeThong', 'Xóa tạm BKT id=27', 'DT_BAI_KIEM_TRA', 27, NULL, '127.0.0.1', '2026-06-14 09:10:09', '2026-06-14 09:10:09', '2026-06-14 09:10:09', NULL, NULL, 0),
(430, 1, 'HeThong', 'Xóa tạm BKT id=26', 'DT_BAI_KIEM_TRA', 26, NULL, '127.0.0.1', '2026-06-14 09:10:13', '2026-06-14 09:10:13', '2026-06-14 09:10:13', NULL, NULL, 0),
(431, 1, 'HeThong', 'Xóa tạm BKT id=25', 'DT_BAI_KIEM_TRA', 25, NULL, '127.0.0.1', '2026-06-14 09:10:15', '2026-06-14 09:10:15', '2026-06-14 09:10:15', NULL, NULL, 0),
(432, 1, 'HeThong', 'Xóa tạm BKT id=24', 'DT_BAI_KIEM_TRA', 24, NULL, '127.0.0.1', '2026-06-14 09:10:17', '2026-06-14 09:10:17', '2026-06-14 09:10:17', NULL, NULL, 0),
(433, 1, 'HeThong', 'Xóa tạm BKT id=23', 'DT_BAI_KIEM_TRA', 23, NULL, '127.0.0.1', '2026-06-14 09:10:18', '2026-06-14 09:10:18', '2026-06-14 09:10:18', NULL, NULL, 0),
(434, 1, 'HeThong', 'Xóa tạm BKT id=22', 'DT_BAI_KIEM_TRA', 22, NULL, '127.0.0.1', '2026-06-14 09:10:21', '2026-06-14 09:10:21', '2026-06-14 09:10:21', NULL, NULL, 0),
(435, 1, 'HeThong', 'Xóa tạm BKT id=20', 'DT_BAI_KIEM_TRA', 20, NULL, '127.0.0.1', '2026-06-14 09:10:22', '2026-06-14 09:10:22', '2026-06-14 09:10:22', NULL, NULL, 0),
(436, 1, 'HeThong', 'Xóa toàn bộ cache hệ thống', 'CACHE', 0, NULL, '127.0.0.1', '2026-06-14 09:17:51', '2026-06-14 09:17:51', '2026-06-14 09:17:51', NULL, NULL, 0),
(437, 1, 'HeThong', 'Xóa toàn bộ cache hệ thống', 'CACHE', 0, NULL, '127.0.0.1', '2026-06-14 09:19:38', '2026-06-14 09:19:38', '2026-06-14 09:19:38', NULL, NULL, 0),
(438, 1, 'HeThong', 'Xóa tạm chương trình id=1', 'DT_CHUONG_TRINH', 1, NULL, '127.0.0.1', '2026-06-14 09:19:43', '2026-06-14 09:19:43', '2026-06-14 09:19:43', NULL, NULL, 0),
(439, 1, 'HeThong', 'Xóa tạm chương trình id=2', 'DT_CHUONG_TRINH', 2, NULL, '127.0.0.1', '2026-06-14 09:19:45', '2026-06-14 09:19:45', '2026-06-14 09:19:45', NULL, NULL, 0),
(440, 1, 'HeThong', 'Xóa tạm chương trình id=3', 'DT_CHUONG_TRINH', 3, NULL, '127.0.0.1', '2026-06-14 09:19:47', '2026-06-14 09:19:47', '2026-06-14 09:19:47', NULL, NULL, 0),
(441, 1, 'HeThong', 'Xóa tạm chương trình id=4', 'DT_CHUONG_TRINH', 4, NULL, '127.0.0.1', '2026-06-14 09:19:48', '2026-06-14 09:19:48', '2026-06-14 09:19:48', NULL, NULL, 0),
(442, 1, 'HeThong', 'Xóa tạm chương trình id=5', 'DT_CHUONG_TRINH', 5, NULL, '127.0.0.1', '2026-06-14 09:19:50', '2026-06-14 09:19:50', '2026-06-14 09:19:50', NULL, NULL, 0),
(443, 1, 'HeThong', 'Xóa tạm chương trình id=6', 'DT_CHUONG_TRINH', 6, NULL, '127.0.0.1', '2026-06-14 09:19:51', '2026-06-14 09:19:51', '2026-06-14 09:19:51', NULL, NULL, 0),
(444, 1, 'HeThong', 'Xóa tạm chương trình id=7', 'DT_CHUONG_TRINH', 7, NULL, '127.0.0.1', '2026-06-14 09:19:53', '2026-06-14 09:19:53', '2026-06-14 09:19:53', NULL, NULL, 0),
(445, 1, 'HeThong', 'Xóa tạm chương trình id=8', 'DT_CHUONG_TRINH', 8, NULL, '127.0.0.1', '2026-06-14 09:19:55', '2026-06-14 09:19:55', '2026-06-14 09:19:55', NULL, NULL, 0),
(446, 1, 'HeThong', 'Xóa tạm chương trình id=9', 'DT_CHUONG_TRINH', 9, NULL, '127.0.0.1', '2026-06-14 09:19:56', '2026-06-14 09:19:56', '2026-06-14 09:19:56', NULL, NULL, 0),
(447, 1, 'HeThong', 'Xóa tạm chương trình id=10', 'DT_CHUONG_TRINH', 10, NULL, '127.0.0.1', '2026-06-14 09:19:57', '2026-06-14 09:19:57', '2026-06-14 09:19:57', NULL, NULL, 0),
(448, 1, 'DT_ChuongTrinh', 'Gắn khóa #9 vào CTĐT #11', 'DT_KHOA_HOC_CHUONG_TRINH', 25, NULL, '0.0.0.0', '2026-06-14 15:29:22', '2026-06-14 15:29:22', '2026-06-14 15:29:22', NULL, NULL, 0),
(449, 1, 'DT_ChuongTrinh', 'Gỡ liên kết khóa-CTĐT #25', 'DT_KHOA_HOC_CHUONG_TRINH', 25, NULL, '0.0.0.0', '2026-06-14 15:29:22', '2026-06-14 15:29:22', '2026-06-14 15:29:22', NULL, NULL, 0),
(450, 9, 'HeThong', 'Đăng nhập: lena', 'DM_NGUOI_DUNG', 9, NULL, '127.0.0.1', '2026-06-14 16:38:42', '2026-06-14 16:38:42', '2026-06-14 16:38:42', NULL, NULL, 0),
(451, 9, 'HeThong', 'Đăng xuất', 'DM_NGUOI_DUNG', 9, NULL, '127.0.0.1', '2026-06-14 16:39:37', '2026-06-14 16:39:37', '2026-06-14 16:39:37', NULL, NULL, 0),
(452, 1, 'HeThong', 'Đăng nhập: admin', 'DM_NGUOI_DUNG', 1, NULL, '127.0.0.1', '2026-06-14 16:39:39', '2026-06-14 16:39:39', '2026-06-14 16:39:39', NULL, NULL, 0),
(453, 1, 'HeThong', 'Xóa toàn bộ cache hệ thống', 'CACHE', 0, NULL, '127.0.0.1', '2026-06-14 16:39:49', '2026-06-14 16:39:49', '2026-06-14 16:39:49', NULL, NULL, 0),
(454, 1, 'HeThong', 'Đăng nhập: admin', 'DM_NGUOI_DUNG', 1, NULL, '127.0.0.1', '2026-06-14 16:42:13', '2026-06-14 16:42:13', '2026-06-14 16:42:13', NULL, NULL, 0),
(455, 1, 'HeThong', 'Thêm HV: Nguyễn Văn Đức', 'DM_HOC_VIEN', 1, NULL, '127.0.0.1', '2026-06-14 16:43:37', '2026-06-14 16:43:37', '2026-06-14 16:43:37', NULL, NULL, 0),
(456, 1, 'HeThong', 'Đăng nhập: admin', 'DM_NGUOI_DUNG', 1, NULL, '127.0.0.1', '2026-06-14 16:47:51', '2026-06-14 16:47:51', '2026-06-14 16:47:51', NULL, NULL, 0),
(457, 1, 'HeThong', 'Thêm buổi học: Phân loại bệnh nhân cấp cứu theo mức độ ưu tiên (2026-06-14)', 'DT_LICH_HOC', 1, NULL, '127.0.0.1', '2026-06-14 17:49:06', '2026-06-14 17:49:06', '2026-06-14 17:49:06', NULL, NULL, 0),
(458, 1, 'HeThong', 'Khởi tạo điểm danh buổi id=1 (1 hv)', 'DT_DIEM_DANH', 1, NULL, '127.0.0.1', '2026-06-14 17:49:16', '2026-06-14 17:49:16', '2026-06-14 17:49:16', NULL, NULL, 0),
(459, 1, 'DT_KhoaHoc', 'Thêm khóa học: Khóa test ngày', 'DT_KHOA_HOC', 10, NULL, '0.0.0.0', '2026-06-14 17:58:57', '2026-06-14 17:58:57', '2026-06-14 17:58:57', NULL, NULL, 0),
(460, 1, 'HeThong', 'Đăng nhập: admin', 'DM_NGUOI_DUNG', 1, NULL, '127.0.0.1', '2026-06-14 21:07:23', '2026-06-14 21:07:23', '2026-06-14 21:07:23', NULL, NULL, 0),
(461, 1, 'HeThong', 'Thêm tài liệu: TEST', 'DT_TAI_LIEU', 1, NULL, '127.0.0.1', '2026-06-14 21:10:32', '2026-06-14 21:10:32', '2026-06-14 21:10:32', NULL, NULL, 0),
(462, 1, 'HeThong', 'Thêm HV: Chu Quang Lương', 'DM_HOC_VIEN', 2, NULL, '127.0.0.1', '2026-06-14 21:14:43', '2026-06-14 21:14:43', '2026-06-14 21:14:43', NULL, NULL, 0),
(463, 1, 'HeThong', 'Thêm HV: Cao Thị Huyền', 'DM_HOC_VIEN', 3, NULL, '127.0.0.1', '2026-06-14 21:15:19', '2026-06-14 21:15:19', '2026-06-14 21:15:19', NULL, NULL, 0),
(464, 1, 'HeThong', 'Xóa tạm HV id=3', 'DM_HOC_VIEN', 3, NULL, '127.0.0.1', '2026-06-14 21:15:27', '2026-06-14 21:15:27', '2026-06-14 21:15:27', NULL, NULL, 0),
(465, 1, 'HeThong', 'Xóa tạm HV id=2', 'DM_HOC_VIEN', 2, NULL, '127.0.0.1', '2026-06-14 21:15:29', '2026-06-14 21:15:29', '2026-06-14 21:15:29', NULL, NULL, 0),
(466, 1, 'DaoTao', 'Thêm hồ sơ học viên: 34', 'DT_HO_SO_HOC_VIEN', 2, NULL, '127.0.0.1', '2026-06-14 21:18:25', '2026-06-14 21:18:25', '2026-06-14 21:18:25', NULL, NULL, 0),
(467, 1, 'DaoTao', 'Xóa tạm hồ sơ id=1', 'DT_HO_SO_HOC_VIEN', 1, NULL, '127.0.0.1', '2026-06-14 21:18:37', '2026-06-14 21:18:37', '2026-06-14 21:18:37', NULL, NULL, 0),
(468, 1, 'DT_ChuongTrinh', 'Gắn khóa #9 vào CTĐT #16', 'DT_KHOA_HOC_CHUONG_TRINH', 26, NULL, '127.0.0.1', '2026-06-14 21:20:37', '2026-06-14 21:20:37', '2026-06-14 21:20:37', NULL, NULL, 0),
(469, 1, 'DT_ChuongTrinh', 'Gắn khóa #9 vào CTĐT #17', 'DT_KHOA_HOC_CHUONG_TRINH', 27, NULL, '127.0.0.1', '2026-06-14 21:20:40', '2026-06-14 21:20:40', '2026-06-14 21:20:40', NULL, NULL, 0),
(470, 1, 'DT_ChuongTrinh', 'Gắn khóa #9 vào CTĐT #18', 'DT_KHOA_HOC_CHUONG_TRINH', 28, NULL, '127.0.0.1', '2026-06-14 21:20:42', '2026-06-14 21:20:42', '2026-06-14 21:20:42', NULL, NULL, 0),
(471, 1, 'DT_ChuongTrinh', 'Gắn khóa #9 vào CTĐT #19', 'DT_KHOA_HOC_CHUONG_TRINH', 29, NULL, '127.0.0.1', '2026-06-14 21:20:45', '2026-06-14 21:20:45', '2026-06-14 21:20:45', NULL, NULL, 0),
(472, 1, 'DT_ChuongTrinh', 'Gắn khóa #9 vào CTĐT #20', 'DT_KHOA_HOC_CHUONG_TRINH', 30, NULL, '127.0.0.1', '2026-06-14 21:20:47', '2026-06-14 21:20:47', '2026-06-14 21:20:47', NULL, NULL, 0),
(473, 1, 'DT_ChuongTrinh', 'Gắn khóa #9 vào CTĐT #21', 'DT_KHOA_HOC_CHUONG_TRINH', 31, NULL, '127.0.0.1', '2026-06-14 21:20:50', '2026-06-14 21:20:50', '2026-06-14 21:20:50', NULL, NULL, 0),
(474, 1, 'DT_ChuongTrinh', 'Gắn khóa #9 vào CTĐT #22', 'DT_KHOA_HOC_CHUONG_TRINH', 32, NULL, '127.0.0.1', '2026-06-14 21:20:53', '2026-06-14 21:20:53', '2026-06-14 21:20:53', NULL, NULL, 0),
(475, 1, 'DT_ChuongTrinh', 'Gắn khóa #9 vào CTĐT #23', 'DT_KHOA_HOC_CHUONG_TRINH', 33, NULL, '127.0.0.1', '2026-06-14 21:20:55', '2026-06-14 21:20:55', '2026-06-14 21:20:55', NULL, NULL, 0),
(476, 1, 'DT_ChuongTrinh', 'Gắn khóa #9 vào CTĐT #24', 'DT_KHOA_HOC_CHUONG_TRINH', 34, NULL, '127.0.0.1', '2026-06-14 21:20:58', '2026-06-14 21:20:58', '2026-06-14 21:20:58', NULL, NULL, 0),
(477, 1, 'DT_ChuongTrinh', 'Gắn khóa #9 vào CTĐT #25', 'DT_KHOA_HOC_CHUONG_TRINH', 35, NULL, '127.0.0.1', '2026-06-14 21:21:00', '2026-06-14 21:21:00', '2026-06-14 21:21:00', NULL, NULL, 0),
(478, 1, 'DT_ChuongTrinh', 'Gắn khóa #9 vào CTĐT #26', 'DT_KHOA_HOC_CHUONG_TRINH', 36, NULL, '127.0.0.1', '2026-06-14 21:21:03', '2026-06-14 21:21:03', '2026-06-14 21:21:03', NULL, NULL, 0),
(479, 1, 'DT_ChuongTrinh', 'Gắn khóa #9 vào CTĐT #27', 'DT_KHOA_HOC_CHUONG_TRINH', 37, NULL, '127.0.0.1', '2026-06-14 21:21:06', '2026-06-14 21:21:06', '2026-06-14 21:21:06', NULL, NULL, 0),
(480, 1, 'DT_ChuongTrinh', 'Gắn khóa #9 vào CTĐT #28', 'DT_KHOA_HOC_CHUONG_TRINH', 38, NULL, '127.0.0.1', '2026-06-14 21:21:08', '2026-06-14 21:21:08', '2026-06-14 21:21:08', NULL, NULL, 0),
(481, 1, 'DT_ChuongTrinh', 'Gắn khóa #9 vào CTĐT #29', 'DT_KHOA_HOC_CHUONG_TRINH', 39, NULL, '127.0.0.1', '2026-06-14 21:21:11', '2026-06-14 21:21:11', '2026-06-14 21:21:11', NULL, NULL, 0),
(482, 1, 'DT_ChuongTrinh', 'Gắn khóa #9 vào CTĐT #30', 'DT_KHOA_HOC_CHUONG_TRINH', 40, NULL, '127.0.0.1', '2026-06-14 21:21:13', '2026-06-14 21:21:13', '2026-06-14 21:21:13', NULL, NULL, 0),
(483, 1, 'DT_ChuongTrinh', 'Gắn khóa #9 vào CTĐT #31', 'DT_KHOA_HOC_CHUONG_TRINH', 41, NULL, '127.0.0.1', '2026-06-14 21:21:15', '2026-06-14 21:21:15', '2026-06-14 21:21:15', NULL, NULL, 0),
(484, 1, 'DT_ChuongTrinh', 'Gắn khóa #9 vào CTĐT #32', 'DT_KHOA_HOC_CHUONG_TRINH', 42, NULL, '127.0.0.1', '2026-06-14 21:21:17', '2026-06-14 21:21:17', '2026-06-14 21:21:17', NULL, NULL, 0),
(485, 1, 'DT_ChuongTrinh', 'Gắn khóa #9 vào CTĐT #33', 'DT_KHOA_HOC_CHUONG_TRINH', 43, NULL, '127.0.0.1', '2026-06-14 21:21:22', '2026-06-14 21:21:22', '2026-06-14 21:21:22', NULL, NULL, 0),
(486, 1, 'DT_ChuongTrinh', 'Gắn khóa #9 vào CTĐT #34', 'DT_KHOA_HOC_CHUONG_TRINH', 44, NULL, '127.0.0.1', '2026-06-14 21:21:28', '2026-06-14 21:21:28', '2026-06-14 21:21:28', NULL, NULL, 0),
(487, 1, 'DT_ChuongTrinh', 'Gắn khóa #9 vào CTĐT #35', 'DT_KHOA_HOC_CHUONG_TRINH', 45, NULL, '127.0.0.1', '2026-06-14 21:21:32', '2026-06-14 21:21:32', '2026-06-14 21:21:32', NULL, NULL, 0),
(488, 1, 'DT_ChuongTrinh', 'Gắn khóa #9 vào CTĐT #36', 'DT_KHOA_HOC_CHUONG_TRINH', 46, NULL, '127.0.0.1', '2026-06-14 21:21:50', '2026-06-14 21:21:50', '2026-06-14 21:21:50', NULL, NULL, 0),
(489, 1, 'DT_ChuongTrinh', 'Gắn khóa #9 vào CTĐT #37', 'DT_KHOA_HOC_CHUONG_TRINH', 47, NULL, '127.0.0.1', '2026-06-14 21:21:53', '2026-06-14 21:21:53', '2026-06-14 21:21:53', NULL, NULL, 0),
(490, 1, 'DT_ChuongTrinh', 'Gắn khóa #9 vào CTĐT #39', 'DT_KHOA_HOC_CHUONG_TRINH', 48, NULL, '127.0.0.1', '2026-06-14 21:21:57', '2026-06-14 21:21:57', '2026-06-14 21:21:57', NULL, NULL, 0),
(491, 1, 'DT_ChuongTrinh', 'Gắn khóa #9 vào CTĐT #41', 'DT_KHOA_HOC_CHUONG_TRINH', 49, NULL, '127.0.0.1', '2026-06-14 21:22:08', '2026-06-14 21:22:08', '2026-06-14 21:22:08', NULL, NULL, 0),
(492, 1, 'DT_ChuongTrinh', 'Gắn khóa #9 vào CTĐT #42', 'DT_KHOA_HOC_CHUONG_TRINH', 50, NULL, '127.0.0.1', '2026-06-14 21:22:12', '2026-06-14 21:22:12', '2026-06-14 21:22:12', NULL, NULL, 0),
(493, 1, 'DT_ChuongTrinh', 'Gắn khóa #9 vào CTĐT #43', 'DT_KHOA_HOC_CHUONG_TRINH', 51, NULL, '127.0.0.1', '2026-06-14 21:22:15', '2026-06-14 21:22:15', '2026-06-14 21:22:15', NULL, NULL, 0),
(494, 1, 'DT_ChuongTrinh', 'Gắn khóa #9 vào CTĐT #44', 'DT_KHOA_HOC_CHUONG_TRINH', 52, NULL, '127.0.0.1', '2026-06-14 21:22:20', '2026-06-14 21:22:20', '2026-06-14 21:22:20', NULL, NULL, 0),
(495, 1, 'DT_ChuongTrinh', 'Gắn khóa #9 vào CTĐT #45', 'DT_KHOA_HOC_CHUONG_TRINH', 53, NULL, '127.0.0.1', '2026-06-14 21:22:24', '2026-06-14 21:22:24', '2026-06-14 21:22:24', NULL, NULL, 0),
(496, 1, 'DT_ChuongTrinh', 'Gắn khóa #9 vào CTĐT #46', 'DT_KHOA_HOC_CHUONG_TRINH', 54, NULL, '127.0.0.1', '2026-06-14 22:02:09', '2026-06-14 22:02:09', '2026-06-14 22:02:09', NULL, NULL, 0),
(497, 1, 'DT_ChuongTrinh', 'Gắn khóa #9 vào CTĐT #47', 'DT_KHOA_HOC_CHUONG_TRINH', 55, NULL, '127.0.0.1', '2026-06-14 22:02:12', '2026-06-14 22:02:12', '2026-06-14 22:02:12', NULL, NULL, 0),
(498, 1, 'DT_ChuongTrinh', 'Gắn khóa #9 vào CTĐT #48', 'DT_KHOA_HOC_CHUONG_TRINH', 56, NULL, '127.0.0.1', '2026-06-14 22:02:15', '2026-06-14 22:02:15', '2026-06-14 22:02:15', NULL, NULL, 0),
(499, 1, 'DT_ChuongTrinh', 'Gắn khóa #9 vào CTĐT #49', 'DT_KHOA_HOC_CHUONG_TRINH', 57, NULL, '127.0.0.1', '2026-06-14 22:02:19', '2026-06-14 22:02:19', '2026-06-14 22:02:19', NULL, NULL, 0),
(500, 1, 'DT_ChuongTrinh', 'Gắn khóa #9 vào CTĐT #50', 'DT_KHOA_HOC_CHUONG_TRINH', 58, NULL, '127.0.0.1', '2026-06-14 22:02:22', '2026-06-14 22:02:22', '2026-06-14 22:02:22', NULL, NULL, 0),
(501, 1, 'DT_ChuongTrinh', 'Gắn khóa #9 vào CTĐT #51', 'DT_KHOA_HOC_CHUONG_TRINH', 59, NULL, '127.0.0.1', '2026-06-14 22:02:26', '2026-06-14 22:02:26', '2026-06-14 22:02:26', NULL, NULL, 0),
(502, 1, 'DT_ChuongTrinh', 'Gắn khóa #9 vào CTĐT #52', 'DT_KHOA_HOC_CHUONG_TRINH', 60, NULL, '127.0.0.1', '2026-06-14 22:02:37', '2026-06-14 22:02:37', '2026-06-14 22:02:37', NULL, NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `dm_nhom_tai_khoan`
--

CREATE TABLE `dm_nhom_tai_khoan` (
  `id` int(11) NOT NULL,
  `ma_nhom` varchar(10) NOT NULL,
  `ten_nhom` varchar(50) NOT NULL,
  `mo_ta` varchar(500) DEFAULT NULL,
  `trang_thai` tinyint(4) NOT NULL DEFAULT 1,
  `ngay_tao` datetime NOT NULL,
  `ngay_cap_nhat` datetime NOT NULL,
  `nguoi_tao` int(11) NOT NULL DEFAULT 0,
  `nguoi_cap_nhat` int(11) NOT NULL DEFAULT 0,
  `da_xoa` int(11) NOT NULL DEFAULT 0,
  `la_admin` tinyint(4) NOT NULL DEFAULT 0 COMMENT 'C? admin = full quy?n'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `dm_nhom_tai_khoan`
--

INSERT INTO `dm_nhom_tai_khoan` (`id`, `ma_nhom`, `ten_nhom`, `mo_ta`, `trang_thai`, `ngay_tao`, `ngay_cap_nhat`, `nguoi_tao`, `nguoi_cap_nhat`, `da_xoa`, `la_admin`) VALUES
(1, 'ADMIN', 'Quản trị viên', NULL, 1, '2026-04-20 17:44:05', '2026-04-21 22:32:02', 0, 1, 0, 1),
(2, 'USER', 'Người dùng', NULL, 1, '2026-04-20 17:44:05', '2026-04-21 22:32:59', 0, 1, 0, 0),
(3, 'TRUONGKHOA', 'Trưởng khoa/phòng', NULL, 1, '2026-04-21 16:58:42', '2026-04-21 16:58:42', 1, 1, 0, 0),
(4, 'CVDAOTAO', 'Chuyên viên Đào Tạo', '', 1, '2026-04-21 16:58:42', '2026-04-27 09:51:32', 1, 1, 0, 0),
(5, 'NCKH', 'Nghiên cứu khoa học', NULL, 1, '2026-04-21 16:58:42', '2026-04-21 16:58:42', 1, 1, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `dm_phan_quyen`
--

CREATE TABLE `dm_phan_quyen` (
  `id` int(11) NOT NULL,
  `nhom_tai_khoan_id` int(11) NOT NULL,
  `danh_sach_form_id` int(11) NOT NULL,
  `quyen_xem` int(11) DEFAULT 0,
  `quyen_them` int(11) DEFAULT 0,
  `quyen_sua` int(11) DEFAULT 0,
  `quyen_xoa` int(11) DEFAULT 0,
  `quyen_duyet` int(11) DEFAULT 0,
  `ngay_tao` datetime DEFAULT current_timestamp(),
  `ngay_cap_nhat` datetime DEFAULT NULL,
  `nguoi_tao` int(11) DEFAULT NULL,
  `nguoi_cap_nhat` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `dm_phan_quyen`
--

INSERT INTO `dm_phan_quyen` (`id`, `nhom_tai_khoan_id`, `danh_sach_form_id`, `quyen_xem`, `quyen_them`, `quyen_sua`, `quyen_xoa`, `quyen_duyet`, `ngay_tao`, `ngay_cap_nhat`, `nguoi_tao`, `nguoi_cap_nhat`) VALUES
(1, 1, 1, 1, 1, 1, 1, 1, '2026-04-20 17:44:05', '2026-04-20 17:44:05', 1, 1),
(2, 1, 2, 1, 1, 1, 1, 1, '2026-04-20 17:44:05', '2026-04-20 17:44:05', 1, 1),
(3, 1, 3, 1, 1, 1, 1, 1, '2026-04-20 17:44:05', '2026-04-20 17:44:05', 1, 1),
(4, 1, 4, 1, 1, 1, 1, 1, '2026-04-20 17:44:05', '2026-04-20 17:44:05', 1, 1),
(5, 1, 5, 1, 1, 1, 1, 1, '2026-04-20 17:44:05', '2026-04-20 17:44:05', 1, 1),
(6, 1, 6, 1, 1, 1, 1, 1, '2026-04-20 17:44:05', '2026-04-20 17:44:05', 1, 1),
(7, 1, 7, 1, 1, 1, 1, 1, '2026-04-20 17:44:05', '2026-04-20 17:44:05', 1, 1),
(8, 1, 8, 1, 1, 1, 1, 1, '2026-04-20 17:44:05', '2026-04-20 17:44:05', 1, 1),
(16, 2, 1, 1, 0, 0, 0, 0, '2026-04-21 16:58:42', '2026-04-23 17:11:22', 1, 1),
(17, 2, 6, 1, 0, 0, 0, 0, '2026-04-21 16:58:42', '2026-04-23 17:11:22', 1, 1),
(18, 2, 7, 1, 0, 0, 0, 0, '2026-04-21 16:58:42', '2026-04-23 17:11:22', 1, 1),
(19, 3, 1, 0, 0, 0, 0, 0, '2026-04-21 16:58:42', '2026-04-29 20:27:51', 1, 1),
(20, 3, 6, 0, 0, 0, 0, 0, '2026-04-21 16:58:42', '2026-04-29 20:27:51', 1, 1),
(21, 3, 7, 0, 0, 0, 0, 0, '2026-04-21 16:58:42', '2026-04-29 20:27:51', 1, 1),
(22, 3, 2, 0, 0, 0, 0, 0, '2026-04-21 16:58:42', '2026-04-29 20:27:51', 1, 1),
(23, 4, 1, 1, 0, 0, 0, 0, '2026-04-21 16:58:42', '2026-06-13 23:05:28', 1, 1),
(24, 4, 6, 1, 1, 1, 1, 0, '2026-04-21 16:58:42', '2026-06-13 23:05:28', 1, 1),
(25, 4, 7, 1, 1, 1, 1, 0, '2026-04-21 16:58:42', '2026-06-13 23:05:28', 1, 1),
(26, 4, 2, 0, 0, 0, 0, 0, '2026-04-21 16:58:42', '2026-06-13 23:05:28', 1, 1),
(27, 4, 5, 0, 0, 0, 0, 0, '2026-04-21 16:58:42', '2026-06-13 23:05:28', 1, 1),
(28, 5, 1, 1, 0, 0, 0, 0, '2026-04-21 16:58:42', '2026-04-23 17:11:22', 1, 1),
(29, 5, 6, 1, 0, 0, 0, 0, '2026-04-21 16:58:42', '2026-04-23 17:11:22', 1, 1),
(37, 3, 8, 0, 0, 0, 0, 0, '2026-04-21 19:35:32', '2026-04-29 20:27:51', 1, 1),
(42, 4, 8, 0, 0, 0, 0, 0, '2026-04-21 19:35:32', '2026-06-13 23:05:28', 1, 1),
(47, 2, 2, 1, 0, 1, 0, 0, '2026-04-21 22:37:51', '2026-04-21 22:38:00', 1, 1),
(48, 2, 3, 1, 0, 1, 0, 0, '2026-04-21 22:37:51', '2026-04-21 22:38:00', 1, 1),
(49, 2, 4, 1, 0, 1, 0, 0, '2026-04-21 22:37:51', '2026-04-21 22:38:00', 1, 1),
(50, 2, 5, 1, 0, 1, 0, 0, '2026-04-21 22:37:51', '2026-04-21 22:38:00', 1, 1),
(51, 2, 8, 1, 0, 1, 0, 0, '2026-04-21 22:37:51', '2026-04-21 22:38:00', 1, 1),
(97, 1, 9, 1, 1, 1, 1, 1, '2026-04-22 17:43:01', '2026-04-22 17:43:01', 1, 1),
(99, 1, 11, 1, 1, 1, 1, 1, '2026-04-23 17:11:19', '2026-04-23 17:11:19', 1, 1),
(116, 1, 13, 1, 1, 1, 1, 1, '2026-04-24 17:02:08', '2026-04-24 17:02:08', 1, 1),
(117, 1, 12, 1, 1, 1, 1, 1, '2026-04-24 17:02:08', '2026-04-24 17:02:08', 1, 1),
(119, 1, 14, 1, 1, 1, 1, 1, '2026-04-24 21:57:15', '2026-04-24 21:57:15', 1, 1),
(120, 1, 15, 1, 1, 1, 1, 1, '2026-04-25 10:02:14', '2026-04-25 10:02:14', 1, 1),
(121, 1, 16, 1, 1, 1, 1, 1, '2026-04-25 10:02:14', '2026-04-25 10:02:14', 1, 1),
(123, 1, 17, 1, 1, 1, 1, 1, '2026-04-25 16:27:08', '2026-04-25 16:27:08', 1, 1),
(124, 1, 18, 1, 1, 1, 1, 1, '2026-04-25 16:27:08', '2026-04-25 16:27:08', 1, 1),
(126, 1, 19, 1, 1, 1, 1, 1, '2026-04-25 21:10:08', '2026-04-25 21:10:08', 1, 1),
(127, 1, 20, 1, 1, 1, 1, 1, '2026-04-26 06:40:29', '2026-04-26 06:40:29', 1, 1),
(128, 1, 23, 1, 1, 1, 1, 1, '2026-04-26 21:35:45', '2026-04-26 21:35:45', 1, 1),
(129, 1, 24, 1, 1, 1, 1, 1, '2026-04-26 21:53:12', '2026-04-26 21:53:12', 1, 1),
(132, 4, 3, 0, 0, 0, 0, 0, '2026-04-27 09:50:11', '2026-06-13 23:05:28', 1, 1),
(133, 4, 4, 0, 0, 0, 0, 0, '2026-04-27 09:50:11', '2026-06-13 23:05:28', 1, 1),
(138, 4, 9, 1, 1, 1, 1, 0, '2026-04-27 09:50:11', '2026-06-13 23:05:28', 1, 1),
(140, 4, 11, 1, 1, 1, 1, 0, '2026-04-27 09:50:11', '2026-06-13 23:05:28', 1, 1),
(141, 4, 12, 1, 1, 1, 1, 0, '2026-04-27 09:50:11', '2026-06-13 23:05:28', 1, 1),
(142, 4, 13, 1, 1, 1, 1, 0, '2026-04-27 09:50:11', '2026-06-13 23:05:28', 1, 1),
(143, 4, 14, 1, 1, 1, 1, 0, '2026-04-27 09:50:11', '2026-06-13 23:05:28', 1, 1),
(144, 4, 15, 1, 1, 1, 1, 0, '2026-04-27 09:50:11', '2026-06-13 23:05:28', 1, 1),
(145, 4, 16, 1, 1, 1, 1, 0, '2026-04-27 09:50:11', '2026-06-13 23:05:28', 1, 1),
(146, 4, 17, 1, 1, 1, 1, 0, '2026-04-27 09:50:11', '2026-06-13 23:05:28', 1, 1),
(147, 4, 18, 1, 1, 1, 1, 0, '2026-04-27 09:50:11', '2026-06-13 23:05:28', 1, 1),
(148, 4, 19, 1, 1, 1, 1, 0, '2026-04-27 09:50:11', '2026-06-13 23:05:28', 1, 1),
(149, 4, 20, 1, 1, 1, 1, 0, '2026-04-27 09:50:11', '2026-06-13 23:05:28', 1, 1),
(150, 4, 21, 1, 1, 1, 1, 0, '2026-04-27 09:50:11', '2026-06-13 23:05:28', 1, 1),
(151, 4, 22, 1, 1, 1, 1, 0, '2026-04-27 09:50:11', '2026-06-13 23:05:28', 1, 1),
(152, 4, 23, 1, 1, 1, 1, 0, '2026-04-27 09:50:11', '2026-06-13 23:05:28', 1, 1),
(153, 4, 24, 0, 0, 0, 0, 0, '2026-04-27 09:50:11', '2026-06-13 23:05:28', 1, 1),
(154, 1, 27, 1, 1, 1, 1, 1, '2026-04-27 09:53:37', '2026-04-27 09:53:37', 1, 1),
(155, 1, 26, 1, 1, 1, 1, 1, '2026-04-27 09:53:37', '2026-04-27 09:53:37', 1, 1),
(156, 1, 25, 1, 1, 1, 1, 1, '2026-04-27 09:53:37', '2026-04-27 09:53:37', 1, 1),
(181, 4, 25, 1, 1, 1, 1, 0, '2026-04-27 09:53:49', '2026-06-13 23:05:28', 1, 1),
(182, 4, 26, 1, 1, 1, 1, 0, '2026-04-27 09:53:49', '2026-06-13 23:05:28', 1, 1),
(183, 4, 27, 1, 1, 1, 1, 0, '2026-04-27 09:53:49', '2026-06-13 23:05:28', 1, 1),
(184, 1, 28, 1, 1, 1, 1, 1, '2026-04-27 16:55:31', '2026-04-27 16:55:31', 1, 1),
(185, 1, 29, 1, 1, 1, 1, 1, '2026-04-27 16:55:31', '2026-04-27 16:55:31', 1, 1),
(186, 1, 30, 1, 1, 1, 1, 1, '2026-04-27 16:55:31', '2026-04-27 16:55:31', 1, 1),
(187, 1, 31, 1, 1, 1, 1, 1, '2026-04-27 16:55:31', '2026-04-27 16:55:31', 1, 1),
(188, 1, 32, 1, 1, 1, 1, 1, '2026-04-27 16:55:31', '2026-04-27 16:55:31', 1, 1),
(189, 1, 33, 1, 1, 1, 1, 1, '2026-04-27 16:55:31', '2026-04-27 16:55:31', 1, 1),
(190, 1, 34, 1, 1, 1, 1, 1, '2026-04-27 16:55:31', '2026-04-27 16:55:31', 1, 1),
(191, 1, 35, 1, 1, 1, 1, 1, '2026-04-27 16:55:31', '2026-04-27 16:55:31', 1, 1),
(200, 1, 36, 1, 1, 1, 1, 1, '2026-04-28 22:28:44', '2026-04-28 22:28:44', 1, 1),
(201, 1, 37, 1, 1, 1, 1, 1, '2026-04-29 17:12:51', '2026-04-29 17:12:51', 1, 1),
(202, 2, 37, 1, 1, 1, 1, 0, '2026-04-29 17:12:51', '2026-04-29 17:12:51', 1, 1),
(203, 3, 37, 1, 1, 1, 1, 0, '2026-04-29 17:12:51', '2026-04-29 20:27:51', 1, 1),
(204, 4, 37, 1, 1, 1, 1, 0, '2026-04-29 17:12:51', '2026-06-13 23:05:28', 1, 1),
(205, 5, 37, 1, 1, 1, 1, 0, '2026-04-29 17:12:51', '2026-04-29 17:12:51', 1, 1),
(206, 1, 38, 1, 1, 1, 1, 1, '2026-04-29 17:12:51', '2026-04-29 17:12:51', 1, 1),
(209, 3, 3, 0, 0, 0, 0, 0, '2026-04-29 20:27:51', '2026-04-29 20:27:51', 1, 1),
(210, 3, 4, 0, 0, 0, 0, 0, '2026-04-29 20:27:51', '2026-04-29 20:27:51', 1, 1),
(211, 3, 5, 0, 0, 0, 0, 0, '2026-04-29 20:27:51', '2026-04-29 20:27:51', 1, 1),
(215, 3, 9, 0, 0, 0, 0, 0, '2026-04-29 20:27:51', '2026-04-29 20:27:51', 1, 1),
(217, 3, 11, 0, 0, 0, 0, 0, '2026-04-29 20:27:51', '2026-04-29 20:27:51', 1, 1),
(218, 3, 12, 0, 0, 0, 0, 0, '2026-04-29 20:27:51', '2026-04-29 20:27:51', 1, 1),
(219, 3, 13, 0, 0, 0, 0, 0, '2026-04-29 20:27:51', '2026-04-29 20:27:51', 1, 1),
(220, 3, 14, 0, 0, 0, 0, 0, '2026-04-29 20:27:51', '2026-04-29 20:27:51', 1, 1),
(221, 3, 15, 0, 0, 0, 0, 0, '2026-04-29 20:27:51', '2026-04-29 20:27:51', 1, 1),
(222, 3, 16, 0, 0, 0, 0, 0, '2026-04-29 20:27:51', '2026-04-29 20:27:51', 1, 1),
(223, 3, 17, 0, 0, 0, 0, 0, '2026-04-29 20:27:51', '2026-04-29 20:27:51', 1, 1),
(224, 3, 18, 0, 0, 0, 0, 0, '2026-04-29 20:27:51', '2026-04-29 20:27:51', 1, 1),
(225, 3, 19, 0, 0, 0, 0, 0, '2026-04-29 20:27:51', '2026-04-29 20:27:51', 1, 1),
(226, 3, 20, 0, 0, 0, 0, 0, '2026-04-29 20:27:51', '2026-04-29 20:27:51', 1, 1),
(227, 3, 21, 0, 0, 0, 0, 0, '2026-04-29 20:27:51', '2026-04-29 20:27:51', 1, 1),
(228, 3, 22, 0, 0, 0, 0, 0, '2026-04-29 20:27:51', '2026-04-29 20:27:51', 1, 1),
(229, 3, 23, 0, 0, 0, 0, 0, '2026-04-29 20:27:51', '2026-04-29 20:27:51', 1, 1),
(230, 3, 24, 0, 0, 0, 0, 0, '2026-04-29 20:27:51', '2026-04-29 20:27:51', 1, 1),
(231, 3, 25, 0, 0, 0, 0, 0, '2026-04-29 20:27:51', '2026-04-29 20:27:51', 1, 1),
(232, 3, 26, 0, 0, 0, 0, 0, '2026-04-29 20:27:51', '2026-04-29 20:27:51', 1, 1),
(233, 3, 27, 0, 0, 0, 0, 0, '2026-04-29 20:27:51', '2026-04-29 20:27:51', 1, 1),
(234, 3, 28, 0, 0, 0, 0, 0, '2026-04-29 20:27:51', '2026-04-29 20:27:51', 1, 1),
(235, 3, 29, 0, 0, 0, 0, 0, '2026-04-29 20:27:51', '2026-04-29 20:27:51', 1, 1),
(236, 3, 30, 0, 0, 0, 0, 0, '2026-04-29 20:27:51', '2026-04-29 20:27:51', 1, 1),
(237, 3, 31, 0, 0, 0, 0, 0, '2026-04-29 20:27:51', '2026-04-29 20:27:51', 1, 1),
(238, 3, 32, 0, 0, 0, 0, 0, '2026-04-29 20:27:51', '2026-04-29 20:27:51', 1, 1),
(239, 3, 33, 0, 0, 0, 0, 0, '2026-04-29 20:27:51', '2026-04-29 20:27:51', 1, 1),
(240, 3, 34, 0, 0, 0, 0, 0, '2026-04-29 20:27:51', '2026-04-29 20:27:51', 1, 1),
(241, 3, 35, 0, 0, 0, 0, 0, '2026-04-29 20:27:51', '2026-04-29 20:27:51', 1, 1),
(242, 3, 36, 0, 0, 0, 0, 0, '2026-04-29 20:27:51', '2026-04-29 20:27:51', 1, 1),
(244, 3, 38, 0, 0, 0, 0, 0, '2026-04-29 20:27:51', '2026-04-29 20:27:51', 1, 1),
(245, 1, 39, 1, 1, 1, 1, 1, '2026-05-04 17:02:45', NULL, 0, NULL),
(246, 2, 39, 1, 0, 0, 0, 0, '2026-05-04 17:02:45', NULL, 0, NULL),
(247, 3, 39, 1, 0, 0, 0, 0, '2026-05-04 17:02:45', NULL, 0, NULL),
(248, 4, 39, 0, 0, 0, 0, 0, '2026-05-04 17:02:45', '2026-06-13 23:05:28', 0, 1),
(249, 5, 39, 1, 0, 0, 0, 0, '2026-05-04 17:02:45', NULL, 0, NULL),
(277, 4, 28, 0, 0, 0, 0, 0, '2026-05-05 17:22:09', '2026-06-13 23:05:28', 1, 1),
(278, 4, 29, 0, 0, 0, 0, 0, '2026-05-05 17:22:09', '2026-06-13 23:05:28', 1, 1),
(279, 4, 30, 0, 0, 0, 0, 0, '2026-05-05 17:22:09', '2026-06-13 23:05:28', 1, 1),
(280, 4, 31, 0, 0, 0, 0, 0, '2026-05-05 17:22:09', '2026-06-13 23:05:28', 1, 1),
(281, 4, 32, 0, 0, 0, 0, 0, '2026-05-05 17:22:09', '2026-06-13 23:05:28', 1, 1),
(282, 4, 33, 0, 0, 0, 0, 0, '2026-05-05 17:22:09', '2026-06-13 23:05:28', 1, 1),
(283, 4, 34, 0, 0, 0, 0, 0, '2026-05-05 17:22:09', '2026-06-13 23:05:28', 1, 1),
(284, 4, 35, 0, 0, 0, 0, 0, '2026-05-05 17:22:09', '2026-06-13 23:05:28', 1, 1),
(285, 4, 36, 0, 0, 0, 0, 0, '2026-05-05 17:22:09', '2026-06-13 23:05:28', 1, 1),
(287, 4, 38, 0, 0, 0, 0, 0, '2026-05-05 17:22:09', '2026-06-13 23:05:28', 1, 1),
(289, 1, 40, 1, 1, 1, 1, 1, '2026-05-12 22:57:02', NULL, 0, NULL),
(290, 2, 40, 1, 0, 0, 0, 0, '2026-05-12 22:57:02', NULL, 0, NULL),
(291, 3, 40, 1, 0, 0, 0, 0, '2026-05-12 22:57:02', NULL, 0, NULL),
(292, 4, 40, 1, 1, 1, 1, 0, '2026-05-12 22:57:02', '2026-06-13 23:05:28', 0, 1),
(293, 5, 40, 1, 0, 0, 0, 0, '2026-05-12 22:57:02', NULL, 0, NULL),
(334, 4, 41, 1, 1, 1, 1, 0, '2026-06-11 20:45:01', '2026-06-13 23:05:28', 1, 1),
(458, 1, 42, 1, 1, 1, 1, 1, '2026-06-11 21:04:00', NULL, 0, NULL),
(459, 2, 42, 1, 0, 0, 0, 0, '2026-06-11 21:04:00', NULL, 0, NULL),
(460, 3, 42, 1, 0, 0, 0, 0, '2026-06-11 21:04:00', NULL, 0, NULL),
(461, 4, 42, 1, 1, 1, 1, 1, '2026-06-11 21:04:01', '2026-06-13 23:05:28', 0, 1),
(462, 5, 42, 1, 0, 0, 0, 0, '2026-06-11 21:04:01', NULL, 0, NULL),
(627, 1, 43, 1, 1, 1, 1, 0, '2026-06-13 23:12:16', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `dt_bai_kiem_tra`
--

CREATE TABLE `dt_bai_kiem_tra` (
  `id` int(11) NOT NULL,
  `ma_bkt` varchar(50) NOT NULL,
  `tieu_de` varchar(255) NOT NULL,
  `mo_ta` text DEFAULT NULL,
  `loai_bkt` tinyint(4) NOT NULL DEFAULT 1,
  `khoa_hoc_chuong_trinh_id` int(11) DEFAULT NULL,
  `mon_hoc_id` int(11) DEFAULT NULL,
  `ngay_kiem_tra` date DEFAULT NULL,
  `thoi_gian_lam_bai` int(11) DEFAULT NULL,
  `de_file_name` varchar(255) DEFAULT NULL,
  `de_file_goc` varchar(255) DEFAULT NULL,
  `de_file_size` bigint(20) DEFAULT NULL,
  `dap_an_file_name` varchar(255) DEFAULT NULL,
  `dap_an_file_goc` varchar(255) DEFAULT NULL,
  `dap_an_file_size` bigint(20) DEFAULT NULL,
  `cong_khai_dap_an` tinyint(4) NOT NULL DEFAULT 0,
  `trang_thai` tinyint(4) NOT NULL DEFAULT 1,
  `ghi_chu` varchar(500) DEFAULT NULL,
  `ngay_tao` datetime DEFAULT current_timestamp(),
  `ngay_cap_nhat` datetime DEFAULT current_timestamp(),
  `nguoi_tao` int(11) DEFAULT NULL,
  `nguoi_cap_nhat` int(11) DEFAULT NULL,
  `da_xoa` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dt_chung_chi`
--

CREATE TABLE `dt_chung_chi` (
  `id` int(11) NOT NULL,
  `hoc_vien_id` int(11) NOT NULL,
  `khoa_hoc_chuong_trinh_id` int(11) NOT NULL,
  `so_chung_chi` varchar(100) NOT NULL,
  `ten_chung_chi` varchar(300) NOT NULL,
  `loai_chung_chi` varchar(100) NOT NULL DEFAULT 'Chứng chỉ',
  `xep_loai_tot_nghiep` varchar(50) DEFAULT NULL,
  `diem_trung_binh` decimal(4,1) DEFAULT NULL,
  `ngay_cap` date NOT NULL,
  `ngay_het_han` date DEFAULT NULL,
  `nguoi_ky` varchar(200) DEFAULT NULL,
  `chuc_vu_nguoi_ky` varchar(200) DEFAULT NULL,
  `noi_cap` varchar(300) DEFAULT NULL,
  `duong_dan_file` varchar(500) DEFAULT NULL,
  `ghi_chu` varchar(500) DEFAULT NULL,
  `trang_thai` tinyint(4) NOT NULL DEFAULT 0,
  `ngay_tao` datetime NOT NULL DEFAULT current_timestamp(),
  `ngay_cap_nhat` datetime NOT NULL DEFAULT current_timestamp(),
  `nguoi_tao` int(11) DEFAULT NULL,
  `nguoi_cap_nhat` int(11) DEFAULT NULL,
  `da_xoa` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dt_chuong_trinh`
--

CREATE TABLE `dt_chuong_trinh` (
  `id` int(11) NOT NULL,
  `ma_chuong_trinh` varchar(50) NOT NULL,
  `ten_chuong_trinh` varchar(200) NOT NULL,
  `thu_tu` int(11) NOT NULL DEFAULT 0,
  `thoi_luong` varchar(100) DEFAULT NULL,
  `khoa_phong_id` int(11) DEFAULT NULL,
  `doi_tuong_id` int(11) DEFAULT NULL,
  `so_luong_toi_da` int(11) NOT NULL DEFAULT 30,
  `mo_ta` text DEFAULT NULL,
  `ngay_tao` datetime DEFAULT current_timestamp(),
  `ngay_cap_nhat` datetime DEFAULT current_timestamp(),
  `nguoi_tao` int(11) DEFAULT NULL,
  `nguoi_cap_nhat` int(11) DEFAULT NULL,
  `da_xoa` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `dt_chuong_trinh`
--

INSERT INTO `dt_chuong_trinh` (`id`, `ma_chuong_trinh`, `ten_chuong_trinh`, `thu_tu`, `thoi_luong`, `khoa_phong_id`, `doi_tuong_id`, `so_luong_toi_da`, `mo_ta`, `ngay_tao`, `ngay_cap_nhat`, `nguoi_tao`, `nguoi_cap_nhat`, `da_xoa`) VALUES
(11, 'LOP_HUY_01', 'Lớp hủy do thiếu học viên', 65, NULL, NULL, NULL, 15, 'Lớp học test tự động — Lớp hủy do thiếu học viên', '2026-04-24 17:08:45', '2026-06-14 09:08:09', 1, 1, 0),
(14, 'ĐTLT_1', 'Giải phẫu bệnh cơ bản', 1, '03 tháng', 53, 1, 30, NULL, '2026-06-13 12:21:54', '2026-06-14 09:04:23', 9, 1, 0),
(15, 'ĐTLT_2', 'Chấn thương Chỉnh hình cơ bản', 2, '3 tháng', 14, 1, 30, NULL, '2026-06-13 12:22:16', '2026-06-14 09:04:23', 9, 1, 0),
(16, 'ĐTLT_3', 'Chẩn đoán và điều trị các bệnh Nội tiết', 3, '3 tháng', 24, 1, 30, NULL, '2026-06-13 12:23:00', '2026-06-14 09:04:23', 9, 1, 0),
(17, 'ĐTLT_4', 'Kỹ thuật viên Nội soi Tiêu hóa', 4, '3 tháng', 52, 3, 30, NULL, '2026-06-13 12:24:42', '2026-06-14 09:04:23', 9, 1, 0),
(18, 'ĐTLT_5', 'Siêu âm cơ bản', 5, '3 tháng', 52, 1, 30, NULL, '2026-06-13 12:25:20', '2026-06-14 09:04:23', 9, 1, 0),
(19, 'ĐTLT_6', 'Nội soi tiêu hóa cơ bản', 6, '3 tháng', 52, 1, 30, NULL, '2026-06-13 12:25:39', '2026-06-14 09:04:23', 9, 1, 0),
(20, 'ĐTLT_7', 'Nội soi can thiệp đường tiêu hóa', 7, '3 tháng', 52, 1, 30, NULL, '2026-06-13 12:26:02', '2026-06-14 09:04:23', 9, 1, 0),
(21, 'ĐTLT_8', 'Nội soi đại tràng', 8, '3 tháng', 52, 1, 30, NULL, '2026-06-13 12:27:33', '2026-06-14 09:04:23', 9, 1, 0),
(22, 'ĐTLT_9', 'Phẫu thuật Nội soi cơ bản', 9, '3 tháng', 42, 1, 30, NULL, '2026-06-13 12:27:59', '2026-06-14 09:04:23', 9, 1, 0),
(23, 'ĐTLT_10', 'Hóa sinh cơ bản', 10, '2 tháng', 49, 1, 30, NULL, '2026-06-13 12:28:26', '2026-06-14 09:04:23', 9, 1, 0),
(24, 'ĐTLT_11', 'Chẩn đoán và điều trị bệnh đột quỵ não', 11, '1 tháng', 13, 2, 30, NULL, '2026-06-13 12:28:48', '2026-06-14 09:04:23', 9, 1, 0),
(25, 'ĐTLT_12', 'Chẩn đoán và điều trị suy tim', 12, '01 tháng', 55, 1, 30, NULL, '2026-06-13 22:27:59', '2026-06-14 09:04:23', 1, 1, 0),
(26, 'ĐTLT_13', 'Hướng dẫn đọc Điện tâm đồ', 13, '01 tháng', 55, 1, 30, NULL, '2026-06-13 22:27:59', '2026-06-14 09:04:23', 1, 1, 0),
(27, 'ĐTLT_14', 'Điện não đồ', 14, '1.5 tháng', 13, 1, 30, NULL, '2026-06-13 22:27:59', '2026-06-14 09:04:23', 1, 1, 0),
(28, 'ĐTLT_15', 'An toàn người bệnh', 15, '07 ngày', 86, 1, 30, NULL, '2026-06-13 22:27:59', '2026-06-14 09:04:23', 1, 9, 0),
(29, 'ĐTLT_16', 'Sử dụng thuốc an toàn hợp lý cho Điều dưỡng', 16, '05 ngày', 86, 2, 30, NULL, '2026-06-13 22:27:59', '2026-06-14 09:04:23', 1, 1, 0),
(30, 'ĐTLT_17', 'Tăng cường năng lực quản lý Điều dưỡng', 17, '07 ngày', 86, 2, 30, NULL, '2026-06-13 22:27:59', '2026-06-14 09:04:23', 1, 1, 0),
(31, 'ĐTLT_18', 'Gây mê hồi sức cơ bản', 18, '06 tháng', 56, 1, 30, NULL, '2026-06-13 22:27:59', '2026-06-14 09:04:23', 1, 1, 0),
(32, 'ĐTLT_19', 'Gây mê hồi sức trong phẫu thuật nội soi', 19, '03 tháng', 23, 1, 30, NULL, '2026-06-13 22:27:59', '2026-06-14 09:04:23', 1, 1, 0),
(33, 'ĐTLT_20', 'X.quang cơ bản', 20, '03 tháng', 51, 1, 30, NULL, '2026-06-13 22:27:59', '2026-06-14 09:04:23', 1, 1, 0),
(34, 'ĐTLT_21', 'Cắt lớp vi tính cơ bản', 21, '03 tháng', 51, 1, 30, NULL, '2026-06-13 22:27:59', '2026-06-14 09:04:23', 1, 1, 0),
(35, 'ĐTLT_22', 'Khám và điều trị các bệnh TMH cơ bản', 22, '03 tháng', 45, 1, 30, NULL, '2026-06-13 22:27:59', '2026-06-14 09:04:23', 1, 1, 0),
(36, 'ĐTLT_23', 'Khám nội soi tai mũi họng', 23, '03 tháng', 45, 1, 30, NULL, '2026-06-13 22:27:59', '2026-06-14 09:04:23', 1, 1, 0),
(37, 'ĐTLT_24', 'Mở khí quản', 24, '01 tháng', 45, 1, 30, NULL, '2026-06-13 22:27:59', '2026-06-14 09:04:23', 1, 1, 0),
(38, 'ĐTLT_25', 'Đào tạo điều dưỡng chuyên ngành tai mũi họng', 25, '03 tháng', 45, 2, 30, NULL, '2026-06-13 22:27:59', '2026-06-14 09:04:23', 1, 1, 0),
(39, 'ĐTLT_26', 'Điều dưỡng nha khoa', 26, '03 tháng', 46, 2, 30, NULL, '2026-06-13 22:27:59', '2026-06-14 09:04:23', 1, 1, 0),
(40, 'ĐTLT_27', 'Kỹ thuật cấp cứu cơ bản - Bác sĩ', 27, '03 tháng', 85, 1, 30, NULL, '2026-06-13 22:27:59', '2026-06-14 09:04:23', 1, 1, 0),
(41, 'ĐTLT_28', 'Kỹ thuật cấp cứu cơ bản - Điều dưỡng', 28, '03 tháng', 85, 2, 30, NULL, '2026-06-13 22:27:59', '2026-06-14 09:04:23', 1, 1, 0),
(42, 'ĐTLT_29', 'Phẫu thuật Phaco', 29, '03 tháng', 47, 1, 30, NULL, '2026-06-13 22:27:59', '2026-06-14 09:04:23', 1, 1, 0),
(43, 'ĐTLT_30', 'Chụp mạch huỳnh quang đáy mắt', 30, '1,5 tháng', 47, 1, 30, NULL, '2026-06-13 22:27:59', '2026-06-14 09:04:23', 1, 1, 0),
(44, 'ĐTLT_31', 'Kiểm soát nhiễm khuẩn', 31, '05 ngày', 16, 1, 30, NULL, '2026-06-13 22:27:59', '2026-06-14 09:04:23', 1, 1, 0),
(45, 'ĐTLT_32', 'Cấp cứu ngừng tuần hoàn', 32, '07 ngày', 85, 1, 30, NULL, '2026-06-13 22:27:59', '2026-06-14 09:04:23', 1, 1, 0),
(46, 'ĐTLT_33', 'Chẩn đoán và điều trị chấn thương sọ não', 33, '03 tháng', 25, 1, 30, NULL, '2026-06-13 22:27:59', '2026-06-14 09:04:23', 1, 1, 0),
(47, 'ĐTLT_34', 'Tiêm an toàn và quản lý chất rắn y tế', 34, '04 ngày', 86, 2, 30, NULL, '2026-06-13 22:27:59', '2026-06-14 09:04:23', 1, 1, 0),
(48, 'ĐTLT_35', 'Kỹ năng tư vấn và Giáo dục sức khỏe', 35, '05 ngày', 86, 2, 30, NULL, '2026-06-13 22:27:59', '2026-06-14 09:04:23', 1, 1, 0),
(49, 'ĐTLT_36', 'Quản lý điều dưỡng', 36, '20 ngày', 86, 2, 30, NULL, '2026-06-13 22:27:59', '2026-06-14 09:04:23', 1, 9, 0),
(50, 'ĐTLT_37', 'Nội soi phế quản ống mềm', 37, '03 tháng', 26, 1, 30, NULL, '2026-06-13 22:27:59', '2026-06-14 09:04:23', 1, 1, 0),
(51, 'ĐTLT_38', 'Chăm sóc bệnh nhân hồi sức sau mổ', 38, '03 tháng', 57, 2, 30, NULL, '2026-06-13 22:27:59', '2026-06-14 09:04:23', 1, 1, 0),
(52, 'ĐTLT_39', 'Chăm sóc bệnh nhân thở máy', 39, '03 tháng', 57, 2, 30, NULL, '2026-06-13 22:27:59', '2026-06-14 09:04:23', 1, 1, 0),
(53, 'ĐTLT_40', 'Thông khí nhân tạo cơ bản', 40, '03 tháng', 57, 1, 30, NULL, '2026-06-13 22:27:59', '2026-06-14 09:04:23', 1, 1, 0),
(54, 'ĐTLT_41', 'Kỹ thuật xét nghiệm Huyết học - Truyền máu cơ bản', 41, '03 tháng', 19, 1, 30, NULL, '2026-06-13 22:27:59', '2026-06-14 09:04:23', 1, 1, 0),
(55, 'ĐTLT_42', 'Chẩn đoán và điều trị các bệnh lý Cơ xương khớp cơ bản', 42, '03 tháng', 33, 1, 30, NULL, '2026-06-13 22:27:59', '2026-06-14 09:04:23', 1, 1, 0),
(56, 'ĐTLT_43', 'Kỹ thuật vật lý trị liệu- phục hồi chức năng cơ bản', 43, '03 tháng', 48, 2, 30, NULL, '2026-06-13 22:27:59', '2026-06-14 09:04:23', 1, 1, 0),
(57, 'ĐTLT_44', 'Khúc xạ cơ bản', 44, '03 tháng', 47, 1, 30, NULL, '2026-06-13 22:27:59', '2026-06-14 09:04:23', 1, 1, 0),
(58, 'ĐTLT_45', 'Phẫu thuật gan mật cơ bản', 45, '03 tháng', 42, 1, 30, NULL, '2026-06-13 22:27:59', '2026-06-14 09:04:23', 1, 1, 0),
(59, 'ĐTLT_46', 'Kỹ thuật lọc máu thận nhân tạo cơ bản - Điều dưỡng - KTV', 46, '06 tháng', 34, 2, 30, NULL, '2026-06-13 22:27:59', '2026-06-14 09:04:23', 1, 1, 0),
(60, 'ĐTLT_47', 'Kỹ thuật lọc máu thận nhân tạo cơ bản - Bác sỹ', 47, '03 tháng', 34, 1, 30, NULL, '2026-06-13 22:27:59', '2026-06-14 09:04:23', 1, 1, 0),
(61, 'ĐTLT_48', 'Kỹ thuật xét nghiệm vi sinh cơ bản', 48, '03 tháng', 50, 1, 30, NULL, '2026-06-13 22:27:59', '2026-06-14 09:04:23', 1, 1, 0),
(62, 'ĐTLT_49', 'Kỹ thuật xoa bóp bấm huyệt', 49, '03 tháng', 38, 1, 30, NULL, '2026-06-13 22:27:59', '2026-06-14 09:04:23', 1, 1, 0),
(63, 'ĐTLT_50', 'Kỹ thuật châm cứu cơ bản', 50, '03 tháng', 38, 1, 30, NULL, '2026-06-13 22:27:59', '2026-06-14 09:04:23', 1, 1, 0),
(64, 'ĐTLT_51', 'Đọc phim MRI cơ bản', 51, '03 tháng', 51, 1, 30, NULL, '2026-06-13 22:27:59', '2026-06-14 09:04:23', 1, 1, 0),
(65, 'ĐTLT_52', 'Chẩn đoán và điều trị viêm gan', 52, '03 tháng', 36, 1, 30, NULL, '2026-06-13 22:27:59', '2026-06-14 09:04:23', 1, 1, 0),
(66, 'ĐTLT_53', 'Tiêm khớp cơ bản', 53, '03 tháng', 33, 1, 30, NULL, '2026-06-13 22:27:59', '2026-06-14 09:04:23', 1, 1, 0),
(67, 'ĐTLT_54', 'Chẩn đoán và điều trị một một số bệnh đường tiêu hóa thường gặp', 54, '03 tháng', 32, 1, 30, NULL, '2026-06-13 22:27:59', '2026-06-14 09:04:23', 1, 1, 0),
(68, 'ĐTLT_55', 'Chẩn đoán và điều trị các bệnh lý Huyết học – Truyền máu', 55, '03 tháng', 35, 1, 30, NULL, '2026-06-13 22:27:59', '2026-06-14 09:04:23', 1, 1, 0),
(69, 'ĐTLT_56', 'Kỹ thuật viên Gây mê hồi sức', 56, '03 tháng', 56, 2, 30, NULL, '2026-06-13 22:27:59', '2026-06-14 09:04:23', 1, 1, 0),
(70, 'ĐTLT_57', 'Dụng cụ viên Gây mê hồi sức', 57, '03 tháng', 56, 2, 30, NULL, '2026-06-13 22:27:59', '2026-06-14 09:04:23', 1, 1, 0),
(71, 'ĐTLT_58', 'Thực hành tốt bán lẻ thuốc', 58, '03 ngày', 15, 5, 30, NULL, '2026-06-13 22:27:59', '2026-06-14 09:04:23', 1, 1, 0),
(72, 'ĐTLT_59', 'Bảo quản và cấp phát thuốc', 59, '02 ngày', 15, 5, 30, NULL, '2026-06-13 22:27:59', '2026-06-14 09:04:23', 1, 1, 0),
(73, 'ĐTLT_60', 'Kỹ năng tìm kiếm và phân giải thông tin thuốc', 60, '03 ngày', 15, 5, 30, NULL, '2026-06-13 22:27:59', '2026-06-14 09:04:23', 1, 1, 0),
(74, 'ĐTLT_61', 'Sử dụng thuốc trên các đối tượng đặc biệt', 61, '02 ngày', 15, 5, 30, NULL, '2026-06-13 22:27:59', '2026-06-14 09:04:23', 1, 1, 0),
(75, 'ĐTLT_62', 'Thực hành khai thác bệnh án và phân tích ca lâm sàng', 62, '02 ngày', 15, 5, 30, NULL, '2026-06-13 22:27:59', '2026-06-14 09:04:23', 1, 1, 0),
(76, 'ĐTLT_63', 'Kỹ thuật khai thông mạch não bằng điều trị thuốc tiêu sợi huyết trong nhồi máu não cấp', 63, '03 tháng', 18, 1, 30, NULL, '2026-06-13 22:27:59', '2026-06-14 09:04:23', 1, 1, 0),
(77, 'ĐTLT_64', 'Phục hồi chức năng cơ bản', 64, '09 tháng', 48, 1, 30, NULL, '2026-06-13 22:27:59', '2026-06-14 09:04:23', 1, 1, 0),
(78, 'ĐTLT_65', 'Hồi sức cấp cứu cơ bản', 65, '03 tháng', 56, 1, 30, NULL, '2026-06-13 22:27:59', '2026-06-14 09:04:23', 1, 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `dt_chuong_trinh_mon_hoc`
--

CREATE TABLE `dt_chuong_trinh_mon_hoc` (
  `id` int(11) NOT NULL,
  `chuong_trinh_id` int(11) NOT NULL,
  `mon_hoc_id` int(11) NOT NULL,
  `thu_tu` int(11) NOT NULL DEFAULT 0,
  `bat_buoc` tinyint(4) NOT NULL DEFAULT 1,
  `trang_thai` tinyint(4) NOT NULL DEFAULT 1,
  `ngay_tao` datetime DEFAULT current_timestamp(),
  `ngay_cap_nhat` datetime DEFAULT current_timestamp(),
  `nguoi_tao` int(11) DEFAULT NULL,
  `nguoi_cap_nhat` int(11) DEFAULT NULL,
  `da_xoa` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `dt_chuong_trinh_mon_hoc`
--

INSERT INTO `dt_chuong_trinh_mon_hoc` (`id`, `chuong_trinh_id`, `mon_hoc_id`, `thu_tu`, `bat_buoc`, `trang_thai`, `ngay_tao`, `ngay_cap_nhat`, `nguoi_tao`, `nguoi_cap_nhat`, `da_xoa`) VALUES
(2, 40, 25, 1, 1, 1, '2026-06-13 23:04:08', '2026-06-13 23:04:08', 1, 1, 0),
(3, 40, 50, 2, 1, 1, '2026-06-13 23:10:29', '2026-06-13 23:10:29', 9, 9, 0),
(4, 40, 49, 3, 1, 1, '2026-06-13 23:10:43', '2026-06-13 23:10:43', 9, 9, 0),
(5, 40, 34, 4, 1, 1, '2026-06-13 23:11:24', '2026-06-13 23:11:24', 9, 9, 0),
(6, 40, 35, 5, 1, 1, '2026-06-13 23:12:25', '2026-06-13 23:12:25', 9, 9, 0),
(7, 40, 36, 6, 1, 1, '2026-06-13 23:12:27', '2026-06-13 23:12:27', 9, 9, 0),
(8, 40, 37, 7, 1, 1, '2026-06-13 23:12:30', '2026-06-13 23:12:30', 9, 9, 0),
(9, 40, 38, 8, 1, 1, '2026-06-13 23:12:34', '2026-06-13 23:12:34', 9, 9, 0),
(10, 40, 39, 9, 1, 1, '2026-06-13 23:12:37', '2026-06-13 23:12:37', 9, 9, 0),
(11, 40, 40, 10, 1, 1, '2026-06-13 23:12:39', '2026-06-13 23:12:39', 9, 9, 0),
(12, 40, 41, 11, 1, 1, '2026-06-13 23:12:42', '2026-06-13 23:12:42', 9, 9, 0),
(13, 40, 42, 12, 1, 1, '2026-06-13 23:12:44', '2026-06-13 23:12:44', 9, 9, 0),
(14, 40, 43, 13, 1, 1, '2026-06-13 23:12:48', '2026-06-13 23:12:48', 9, 9, 0),
(15, 40, 48, 14, 1, 1, '2026-06-13 23:12:59', '2026-06-13 23:12:59', 9, 9, 0),
(16, 40, 47, 15, 1, 1, '2026-06-13 23:13:04', '2026-06-13 23:13:04', 9, 9, 0),
(17, 40, 26, 16, 1, 1, '2026-06-13 23:13:17', '2026-06-13 23:13:17', 9, 9, 0),
(18, 40, 44, 17, 1, 1, '2026-06-13 23:13:32', '2026-06-13 23:13:32', 9, 9, 0),
(19, 40, 46, 18, 1, 1, '2026-06-13 23:13:43', '2026-06-13 23:13:43', 9, 9, 0),
(20, 40, 45, 19, 1, 1, '2026-06-13 23:14:02', '2026-06-13 23:14:02', 9, 9, 0);

-- --------------------------------------------------------

--
-- Table structure for table `dt_dang_ky_khoa_hoc`
--

CREATE TABLE `dt_dang_ky_khoa_hoc` (
  `id` int(11) NOT NULL,
  `ma_tra_cuu` varchar(32) NOT NULL,
  `khoa_hoc_id` int(11) NOT NULL,
  `ho_ten` varchar(150) NOT NULL,
  `ngay_sinh` date DEFAULT NULL,
  `gioi_tinh` varchar(10) DEFAULT NULL,
  `cccd` varchar(20) NOT NULL,
  `dien_thoai` varchar(30) DEFAULT NULL,
  `email` varchar(150) NOT NULL,
  `dia_chi` varchar(300) DEFAULT NULL,
  `don_vi_cong_tac` varchar(200) DEFAULT NULL,
  `chuc_vu` varchar(150) DEFAULT NULL,
  `cccd_file` varchar(255) DEFAULT NULL,
  `bang_cap_file` varchar(255) DEFAULT NULL,
  `ly_do_dang_ky` text DEFAULT NULL,
  `trang_thai` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0=ChoDuyet, 1=DaDuyet, 2=TuChoi',
  `ly_do_xu_ly` varchar(500) DEFAULT NULL,
  `ngay_xu_ly` datetime DEFAULT NULL,
  `nguoi_xu_ly` int(11) DEFAULT NULL,
  `hoc_vien_id` int(11) DEFAULT NULL,
  `khoa_hoc_chuong_trinh_id` int(11) DEFAULT NULL,
  `ip_dang_ky` varchar(45) DEFAULT NULL,
  `ngay_tao` datetime DEFAULT current_timestamp(),
  `ngay_cap_nhat` datetime DEFAULT current_timestamp(),
  `nguoi_tao` int(11) DEFAULT NULL,
  `nguoi_cap_nhat` int(11) DEFAULT NULL,
  `da_xoa` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dt_diem_danh`
--

CREATE TABLE `dt_diem_danh` (
  `id` int(11) NOT NULL,
  `lich_hoc_id` int(11) NOT NULL,
  `hoc_vien_lop_id` int(11) NOT NULL,
  `trang_thai` tinyint(4) NOT NULL DEFAULT 1,
  `gio_vao` time DEFAULT NULL,
  `gio_ra` time DEFAULT NULL,
  `ghi_chu` varchar(250) DEFAULT NULL,
  `ngay_tao` datetime DEFAULT current_timestamp(),
  `ngay_cap_nhat` datetime DEFAULT current_timestamp(),
  `nguoi_tao` int(11) DEFAULT NULL,
  `nguoi_cap_nhat` int(11) DEFAULT NULL,
  `da_xoa` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `dt_diem_danh`
--

INSERT INTO `dt_diem_danh` (`id`, `lich_hoc_id`, `hoc_vien_lop_id`, `trang_thai`, `gio_vao`, `gio_ra`, `ghi_chu`, `ngay_tao`, `ngay_cap_nhat`, `nguoi_tao`, `nguoi_cap_nhat`, `da_xoa`) VALUES
(1, 1, 1, 1, NULL, NULL, NULL, '2026-06-14 17:49:16', '2026-06-14 17:49:16', 1, 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `dt_dot_dang_ky`
--

CREATE TABLE `dt_dot_dang_ky` (
  `id` int(11) NOT NULL,
  `ten_dot` varchar(255) NOT NULL,
  `nam` smallint(6) NOT NULL,
  `tu_ngay` date NOT NULL,
  `den_ngay` date NOT NULL,
  `mo_ta` text DEFAULT NULL,
  `trang_thai` tinyint(4) NOT NULL DEFAULT 1 COMMENT '1=HoatDong, 0=Khoa',
  `ngay_tao` datetime DEFAULT current_timestamp(),
  `ngay_cap_nhat` datetime DEFAULT current_timestamp(),
  `nguoi_tao` int(11) DEFAULT NULL,
  `nguoi_cap_nhat` int(11) DEFAULT NULL,
  `da_xoa` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `dt_dot_dang_ky`
--

INSERT INTO `dt_dot_dang_ky` (`id`, `ten_dot`, `nam`, `tu_ngay`, `den_ngay`, `mo_ta`, `trang_thai`, `ngay_tao`, `ngay_cap_nhat`, `nguoi_tao`, `nguoi_cap_nhat`, `da_xoa`) VALUES
(1, 'Đợt 1 - 2026', 2026, '2026-02-01', '2026-06-05', NULL, 1, '2026-05-13 22:17:45', '2026-05-13 22:19:27', 1, 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `dt_dot_giai_doan`
--

CREATE TABLE `dt_dot_giai_doan` (
  `id` int(11) NOT NULL,
  `dot_id` int(11) NOT NULL,
  `ten_giai_doan` varchar(255) NOT NULL,
  `hanh_vi` enum('Submit','Review') NOT NULL DEFAULT 'Submit',
  `tu_ngay` datetime NOT NULL,
  `den_ngay` datetime NOT NULL,
  `thu_tu` int(11) NOT NULL DEFAULT 0,
  `ghi_chu` varchar(500) DEFAULT NULL,
  `ngay_tao` datetime DEFAULT current_timestamp(),
  `ngay_cap_nhat` datetime DEFAULT current_timestamp(),
  `nguoi_tao` int(11) DEFAULT NULL,
  `nguoi_cap_nhat` int(11) DEFAULT NULL,
  `da_xoa` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `dt_dot_giai_doan`
--

INSERT INTO `dt_dot_giai_doan` (`id`, `dot_id`, `ten_giai_doan`, `hanh_vi`, `tu_ngay`, `den_ngay`, `thu_tu`, `ghi_chu`, `ngay_tao`, `ngay_cap_nhat`, `nguoi_tao`, `nguoi_cap_nhat`, `da_xoa`) VALUES
(1, 1, 'Đăng ký thành viên', 'Submit', '2026-05-11 22:19:00', '2026-05-16 22:19:00', 0, NULL, '2026-05-13 22:19:56', '2026-05-13 22:19:56', 1, 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `dt_hoc_vien_lop`
--

CREATE TABLE `dt_hoc_vien_lop` (
  `id` int(11) NOT NULL,
  `khoa_hoc_chuong_trinh_id` int(11) NOT NULL,
  `hoc_vien_id` int(11) NOT NULL,
  `ngay_ghi_danh` date DEFAULT NULL,
  `trang_thai` tinyint(4) NOT NULL DEFAULT 1,
  `diem_tong_ket` decimal(4,1) DEFAULT NULL,
  `xep_loai` varchar(30) DEFAULT NULL,
  `ghi_chu` varchar(250) DEFAULT NULL,
  `ngay_tao` datetime DEFAULT current_timestamp(),
  `ngay_cap_nhat` datetime DEFAULT current_timestamp(),
  `nguoi_tao` int(11) DEFAULT NULL,
  `nguoi_cap_nhat` int(11) DEFAULT NULL,
  `da_xoa` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `dt_hoc_vien_lop`
--

INSERT INTO `dt_hoc_vien_lop` (`id`, `khoa_hoc_chuong_trinh_id`, `hoc_vien_id`, `ngay_ghi_danh`, `trang_thai`, `diem_tong_ket`, `xep_loai`, `ghi_chu`, `ngay_tao`, `ngay_cap_nhat`, `nguoi_tao`, `nguoi_cap_nhat`, `da_xoa`) VALUES
(1, 24, 1, '2026-06-14', 1, NULL, NULL, NULL, '2026-06-14 16:43:45', '2026-06-14 16:43:45', 1, 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `dt_ho_so_hoc_vien`
--

CREATE TABLE `dt_ho_so_hoc_vien` (
  `id` int(11) NOT NULL,
  `hoc_vien_id` int(11) NOT NULL,
  `loai_ho_so` varchar(100) NOT NULL,
  `ten_ho_so` varchar(300) NOT NULL,
  `so_hieu` varchar(100) DEFAULT NULL,
  `ngay_cap` date DEFAULT NULL,
  `noi_cap` varchar(200) DEFAULT NULL,
  `ngay_het_han` date DEFAULT NULL,
  `duong_dan` varchar(500) DEFAULT NULL,
  `kich_thuoc` bigint(20) DEFAULT NULL,
  `ghi_chu` varchar(500) DEFAULT NULL,
  `trang_thai` tinyint(4) NOT NULL DEFAULT 1,
  `ngay_tao` datetime NOT NULL DEFAULT current_timestamp(),
  `ngay_cap_nhat` datetime NOT NULL DEFAULT current_timestamp(),
  `nguoi_tao` int(11) DEFAULT NULL,
  `nguoi_cap_nhat` int(11) DEFAULT NULL,
  `da_xoa` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `dt_ho_so_hoc_vien`
--

INSERT INTO `dt_ho_so_hoc_vien` (`id`, `hoc_vien_id`, `loai_ho_so`, `ten_ho_so`, `so_hieu`, `ngay_cap`, `noi_cap`, `ngay_het_han`, `duong_dan`, `kich_thuoc`, `ghi_chu`, `trang_thai`, `ngay_tao`, `ngay_cap_nhat`, `nguoi_tao`, `nguoi_cap_nhat`, `da_xoa`) VALUES
(1, 5, 'CMND/CCCD', 'cccc', NULL, NULL, NULL, NULL, '20260426_072706_c10feacf5b8e.jpg', 50889, NULL, 1, '2026-04-26 07:26:54', '2026-06-14 21:18:37', 1, 1, 1),
(2, 1, 'CMND/CCCD', '34', NULL, NULL, NULL, NULL, '20260614_211825_cd95336e21a7.jpg', 50889, NULL, 1, '2026-06-14 21:18:25', '2026-06-14 21:18:25', 1, 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `dt_ket_qua_hoc_tap`
--

CREATE TABLE `dt_ket_qua_hoc_tap` (
  `id` int(11) NOT NULL,
  `hoc_vien_lop_id` int(11) NOT NULL,
  `mon_hoc_id` int(11) DEFAULT NULL,
  `diem_thuong_xuyen` decimal(4,1) DEFAULT NULL,
  `diem_giua_ky` decimal(4,1) DEFAULT NULL,
  `diem_cuoi_ky` decimal(4,1) DEFAULT NULL,
  `diem_tong_ket` decimal(4,1) DEFAULT NULL,
  `xep_loai` varchar(30) DEFAULT NULL,
  `dat` tinyint(4) DEFAULT NULL,
  `nhan_xet` varchar(500) DEFAULT NULL,
  `ngay_tao` datetime DEFAULT current_timestamp(),
  `ngay_cap_nhat` datetime DEFAULT current_timestamp(),
  `nguoi_tao` int(11) DEFAULT NULL,
  `nguoi_cap_nhat` int(11) DEFAULT NULL,
  `da_xoa` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `dt_ket_qua_hoc_tap`
--

INSERT INTO `dt_ket_qua_hoc_tap` (`id`, `hoc_vien_lop_id`, `mon_hoc_id`, `diem_thuong_xuyen`, `diem_giua_ky`, `diem_cuoi_ky`, `diem_tong_ket`, `xep_loai`, `dat`, `nhan_xet`, `ngay_tao`, `ngay_cap_nhat`, `nguoi_tao`, `nguoi_cap_nhat`, `da_xoa`) VALUES
(226, 1, NULL, 10.0, 8.0, 8.0, 8.4, 'Giỏi', 1, NULL, '2026-06-14 21:08:06', '2026-06-14 21:08:06', 1, 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `dt_khoa_hoc`
--

CREATE TABLE `dt_khoa_hoc` (
  `id` int(11) NOT NULL,
  `ma_khoa_hoc` varchar(50) NOT NULL,
  `ten_khoa_hoc` varchar(200) NOT NULL,
  `mo_ta` text DEFAULT NULL,
  `muc_tieu` text DEFAULT NULL,
  `loai_hinh_dao_tao_id` int(11) DEFAULT NULL,
  `hinh_thuc_hoc_id` int(11) DEFAULT NULL,
  `doi_tuong_hoc_vien_id` int(11) DEFAULT NULL,
  `dot_dang_ky_id` int(11) DEFAULT NULL,
  `dieu_kien` varchar(200) DEFAULT NULL,
  `ngay_bat_dau` date DEFAULT NULL,
  `ngay_ket_thuc` date DEFAULT NULL,
  `trang_thai` tinyint(4) NOT NULL DEFAULT 1,
  `ngay_tao` datetime DEFAULT current_timestamp(),
  `ngay_cap_nhat` datetime DEFAULT current_timestamp(),
  `nguoi_tao` int(11) DEFAULT NULL,
  `nguoi_cap_nhat` int(11) DEFAULT NULL,
  `da_xoa` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `dt_khoa_hoc`
--

INSERT INTO `dt_khoa_hoc` (`id`, `ma_khoa_hoc`, `ten_khoa_hoc`, `mo_ta`, `muc_tieu`, `loai_hinh_dao_tao_id`, `hinh_thuc_hoc_id`, `doi_tuong_hoc_vien_id`, `dot_dang_ky_id`, `dieu_kien`, `ngay_bat_dau`, `ngay_ket_thuc`, `trang_thai`, `ngay_tao`, `ngay_cap_nhat`, `nguoi_tao`, `nguoi_cap_nhat`, `da_xoa`) VALUES
(9, 'KHOAHOC_092026', 'Khoá học tháng 9 2026', '', '', 1, 2, NULL, NULL, '', NULL, NULL, 1, '2026-06-14 08:54:56', '2026-06-14 08:54:56', 1, 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `dt_khoa_hoc_chuong_trinh`
--

CREATE TABLE `dt_khoa_hoc_chuong_trinh` (
  `id` int(11) NOT NULL,
  `khoa_hoc_id` int(11) NOT NULL,
  `chuong_trinh_id` int(11) NOT NULL,
  `ngay_tao` datetime DEFAULT current_timestamp(),
  `ngay_cap_nhat` datetime DEFAULT current_timestamp(),
  `nguoi_tao` int(11) DEFAULT NULL,
  `nguoi_cap_nhat` int(11) DEFAULT NULL,
  `da_xoa` int(11) NOT NULL DEFAULT 0,
  `ngay_bat_dau` date DEFAULT NULL,
  `ngay_ket_thuc` date DEFAULT NULL,
  `dia_diem` varchar(200) DEFAULT NULL,
  `giao_vien_id` int(11) DEFAULT NULL,
  `giao_vien_ngoai` varchar(200) DEFAULT NULL,
  `trang_thai` tinyint(4) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `dt_khoa_hoc_chuong_trinh`
--

INSERT INTO `dt_khoa_hoc_chuong_trinh` (`id`, `khoa_hoc_id`, `chuong_trinh_id`, `ngay_tao`, `ngay_cap_nhat`, `nguoi_tao`, `nguoi_cap_nhat`, `da_xoa`, `ngay_bat_dau`, `ngay_ket_thuc`, `dia_diem`, `giao_vien_id`, `giao_vien_ngoai`, `trang_thai`) VALUES
(22, 9, 14, '2026-06-14 08:55:32', '2026-06-14 08:55:32', 1, 1, 0, NULL, NULL, NULL, NULL, NULL, 0),
(23, 9, 15, '2026-06-14 08:55:42', '2026-06-14 08:55:42', 1, 1, 0, NULL, NULL, NULL, NULL, NULL, 0),
(24, 9, 40, '2026-06-14 09:06:25', '2026-06-14 09:06:25', 1, 1, 0, NULL, NULL, NULL, NULL, NULL, 0),
(25, 9, 11, '2026-06-14 15:29:22', '2026-06-14 15:29:22', 1, 1, 1, NULL, NULL, 'Test', NULL, NULL, 0),
(26, 9, 16, '2026-06-14 21:20:37', '2026-06-14 21:20:37', 1, 1, 0, NULL, NULL, NULL, NULL, NULL, 0),
(27, 9, 17, '2026-06-14 21:20:40', '2026-06-14 21:20:40', 1, 1, 0, NULL, NULL, NULL, NULL, NULL, 0),
(28, 9, 18, '2026-06-14 21:20:42', '2026-06-14 21:20:42', 1, 1, 0, NULL, NULL, NULL, NULL, NULL, 0),
(29, 9, 19, '2026-06-14 21:20:45', '2026-06-14 21:20:45', 1, 1, 0, NULL, NULL, NULL, NULL, NULL, 0),
(30, 9, 20, '2026-06-14 21:20:47', '2026-06-14 21:20:47', 1, 1, 0, NULL, NULL, NULL, NULL, NULL, 0),
(31, 9, 21, '2026-06-14 21:20:50', '2026-06-14 21:20:50', 1, 1, 0, NULL, NULL, NULL, NULL, NULL, 0),
(32, 9, 22, '2026-06-14 21:20:53', '2026-06-14 21:20:53', 1, 1, 0, NULL, NULL, NULL, NULL, NULL, 0),
(33, 9, 23, '2026-06-14 21:20:55', '2026-06-14 21:20:55', 1, 1, 0, NULL, NULL, NULL, NULL, NULL, 0),
(34, 9, 24, '2026-06-14 21:20:58', '2026-06-14 21:20:58', 1, 1, 0, NULL, NULL, NULL, NULL, NULL, 0),
(35, 9, 25, '2026-06-14 21:21:00', '2026-06-14 21:21:00', 1, 1, 0, NULL, NULL, NULL, NULL, NULL, 0),
(36, 9, 26, '2026-06-14 21:21:03', '2026-06-14 21:21:03', 1, 1, 0, NULL, NULL, NULL, NULL, NULL, 0),
(37, 9, 27, '2026-06-14 21:21:06', '2026-06-14 21:21:06', 1, 1, 0, NULL, NULL, NULL, NULL, NULL, 0),
(38, 9, 28, '2026-06-14 21:21:08', '2026-06-14 21:21:08', 1, 1, 0, NULL, NULL, NULL, NULL, NULL, 0),
(39, 9, 29, '2026-06-14 21:21:11', '2026-06-14 21:21:11', 1, 1, 0, NULL, NULL, NULL, NULL, NULL, 0),
(40, 9, 30, '2026-06-14 21:21:13', '2026-06-14 21:21:13', 1, 1, 0, NULL, NULL, NULL, NULL, NULL, 0),
(41, 9, 31, '2026-06-14 21:21:15', '2026-06-14 21:21:15', 1, 1, 0, NULL, NULL, NULL, NULL, NULL, 0),
(42, 9, 32, '2026-06-14 21:21:17', '2026-06-14 21:21:17', 1, 1, 0, NULL, NULL, NULL, NULL, NULL, 0),
(43, 9, 33, '2026-06-14 21:21:22', '2026-06-14 21:21:22', 1, 1, 0, NULL, NULL, NULL, NULL, NULL, 0),
(44, 9, 34, '2026-06-14 21:21:28', '2026-06-14 21:21:28', 1, 1, 0, NULL, NULL, NULL, NULL, NULL, 0),
(45, 9, 35, '2026-06-14 21:21:32', '2026-06-14 21:21:32', 1, 1, 0, NULL, NULL, NULL, NULL, NULL, 0),
(46, 9, 36, '2026-06-14 21:21:50', '2026-06-14 21:21:50', 1, 1, 0, NULL, NULL, NULL, NULL, NULL, 0),
(47, 9, 37, '2026-06-14 21:21:53', '2026-06-14 21:21:53', 1, 1, 0, NULL, NULL, NULL, NULL, NULL, 0),
(48, 9, 39, '2026-06-14 21:21:57', '2026-06-14 21:21:57', 1, 1, 0, NULL, NULL, NULL, NULL, NULL, 0),
(49, 9, 41, '2026-06-14 21:22:08', '2026-06-14 21:22:08', 1, 1, 0, NULL, NULL, NULL, NULL, NULL, 0),
(50, 9, 42, '2026-06-14 21:22:12', '2026-06-14 21:22:12', 1, 1, 0, NULL, NULL, NULL, NULL, NULL, 0),
(51, 9, 43, '2026-06-14 21:22:15', '2026-06-14 21:22:15', 1, 1, 0, NULL, NULL, NULL, NULL, NULL, 0),
(52, 9, 44, '2026-06-14 21:22:20', '2026-06-14 21:22:20', 1, 1, 0, NULL, NULL, NULL, NULL, NULL, 0),
(53, 9, 45, '2026-06-14 21:22:24', '2026-06-14 21:22:24', 1, 1, 0, NULL, NULL, NULL, NULL, NULL, 0),
(54, 9, 46, '2026-06-14 22:02:09', '2026-06-14 22:02:09', 1, 1, 0, NULL, NULL, NULL, NULL, NULL, 0),
(55, 9, 47, '2026-06-14 22:02:12', '2026-06-14 22:02:12', 1, 1, 0, NULL, NULL, NULL, NULL, NULL, 0),
(56, 9, 48, '2026-06-14 22:02:15', '2026-06-14 22:02:15', 1, 1, 0, NULL, NULL, NULL, NULL, NULL, 0),
(57, 9, 49, '2026-06-14 22:02:19', '2026-06-14 22:02:19', 1, 1, 0, NULL, NULL, NULL, NULL, NULL, 0),
(58, 9, 50, '2026-06-14 22:02:22', '2026-06-14 22:02:22', 1, 1, 0, NULL, NULL, NULL, NULL, NULL, 0),
(59, 9, 51, '2026-06-14 22:02:26', '2026-06-14 22:02:26', 1, 1, 0, NULL, NULL, NULL, NULL, NULL, 0),
(60, 9, 52, '2026-06-14 22:02:37', '2026-06-14 22:02:37', 1, 1, 0, NULL, NULL, NULL, NULL, NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `dt_lich_hoc`
--

CREATE TABLE `dt_lich_hoc` (
  `id` int(11) NOT NULL,
  `khoa_hoc_chuong_trinh_id` int(11) NOT NULL,
  `buoi_thu` int(11) NOT NULL DEFAULT 1,
  `tieu_de` varchar(200) NOT NULL,
  `noi_dung` text DEFAULT NULL,
  `mon_hoc_id` int(11) DEFAULT NULL,
  `ngay_hoc` date NOT NULL,
  `gio_bat_dau` time NOT NULL,
  `gio_ket_thuc` time NOT NULL,
  `phong_hoc` varchar(150) DEFAULT NULL,
  `giang_vien_id` int(11) DEFAULT NULL,
  `giang_vien_ngoai` varchar(200) DEFAULT NULL,
  `trang_thai` tinyint(4) NOT NULL DEFAULT 0,
  `ghi_chu` varchar(500) DEFAULT NULL,
  `ngay_tao` datetime DEFAULT current_timestamp(),
  `ngay_cap_nhat` datetime DEFAULT current_timestamp(),
  `nguoi_tao` int(11) DEFAULT NULL,
  `nguoi_cap_nhat` int(11) DEFAULT NULL,
  `da_xoa` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `dt_lich_hoc`
--

INSERT INTO `dt_lich_hoc` (`id`, `khoa_hoc_chuong_trinh_id`, `buoi_thu`, `tieu_de`, `noi_dung`, `mon_hoc_id`, `ngay_hoc`, `gio_bat_dau`, `gio_ket_thuc`, `phong_hoc`, `giang_vien_id`, `giang_vien_ngoai`, `trang_thai`, `ghi_chu`, `ngay_tao`, `ngay_cap_nhat`, `nguoi_tao`, `nguoi_cap_nhat`, `da_xoa`) VALUES
(1, 24, 1, 'Phân loại bệnh nhân cấp cứu theo mức độ ưu tiên', NULL, NULL, '2026-06-14', '07:30:00', '11:00:00', NULL, NULL, NULL, 0, NULL, '2026-06-14 17:49:06', '2026-06-14 17:49:06', 1, 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `dt_mon_hoc`
--

CREATE TABLE `dt_mon_hoc` (
  `id` int(11) NOT NULL,
  `ma_mon_hoc` varchar(50) NOT NULL,
  `ten_mon_hoc` varchar(200) NOT NULL,
  `thu_tu` int(11) NOT NULL DEFAULT 0,
  `chuong_trinh_id` int(11) DEFAULT NULL,
  `mo_ta` text DEFAULT NULL,
  `so_tiet_ly_thuyet` int(11) NOT NULL DEFAULT 0,
  `so_tiet_thuc_hanh` int(11) NOT NULL DEFAULT 0,
  `tong_so_tiet` int(11) NOT NULL DEFAULT 0,
  `so_tin_chi` decimal(5,1) NOT NULL DEFAULT 0.0,
  `trang_thai` tinyint(4) NOT NULL DEFAULT 1,
  `ngay_tao` datetime DEFAULT current_timestamp(),
  `ngay_cap_nhat` datetime DEFAULT current_timestamp(),
  `nguoi_tao` int(11) DEFAULT NULL,
  `nguoi_cap_nhat` int(11) DEFAULT NULL,
  `da_xoa` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `dt_mon_hoc`
--

INSERT INTO `dt_mon_hoc` (`id`, `ma_mon_hoc`, `ten_mon_hoc`, `thu_tu`, `chuong_trinh_id`, `mo_ta`, `so_tiet_ly_thuyet`, `so_tiet_thuc_hanh`, `tong_so_tiet`, `so_tin_chi`, `trang_thai`, `ngay_tao`, `ngay_cap_nhat`, `nguoi_tao`, `nguoi_cap_nhat`, `da_xoa`) VALUES
(1, 'MH_BLS01', 'Hồi sinh tim phổi cơ bản người lớn', 0, NULL, 'Kỹ thuật CPR, ép tim ngoài lồng ngực, thổi ngạt cho người lớn theo chuẩn AHA 2020.', 2, 4, 6, 0.5, 1, '2026-04-22 17:50:27', '2026-06-13 22:55:13', 1, 9, 1),
(2, 'MH_BLS02', 'Hồi sinh tim phổi trẻ em và sơ sinh', 0, NULL, 'Điều chỉnh kỹ thuật CPR cho trẻ em và sơ sinh, xử trí dị vật đường thở.', 2, 4, 6, 0.5, 1, '2026-04-22 17:50:27', '2026-06-13 22:55:15', 1, 9, 1),
(3, 'MH_BLS03', 'Sử dụng máy AED', 0, NULL, 'Nhận biết rối loạn nhịp sốc được, thao tác an toàn với máy AED.', 2, 4, 6, 0.5, 1, '2026-04-22 17:50:27', '2026-06-13 22:55:16', 1, 9, 1),
(4, 'MH_BLS04', 'Quản lý đường thở cơ bản', 0, NULL, 'Thủ thuật mở đường thở, bóp bóng qua mặt nạ, đặt canul hầu họng.', 2, 4, 6, 0.5, 1, '2026-04-22 17:50:27', '2026-06-13 22:55:18', 1, 9, 1),
(5, 'MH_ACLS01', 'Phân tích điện tâm đồ cấp cứu', 0, NULL, 'Nhận biết các rối loạn nhịp nguy hiểm: rung thất, nhịp nhanh thất, vô tâm thu, PEA.', 4, 6, 10, 0.8, 1, '2026-04-22 17:50:27', '2026-06-13 22:55:20', 1, 9, 1),
(6, 'MH_ACLS02', 'Xử trí ngừng tuần hoàn nâng cao', 0, NULL, 'Thuật toán ACLS, thuốc cấp cứu, khử rung, tạo nhịp qua da.', 4, 10, 14, 1.2, 1, '2026-04-22 17:50:27', '2026-06-13 22:55:21', 1, 9, 1),
(7, 'MH_ACLS03', 'Xử trí sau hồi sinh', 0, NULL, 'Hạ thân nhiệt, ổn định huyết động, chuyển khoa hồi sức.', 4, 8, 12, 1.0, 1, '2026-04-22 17:50:27', '2026-06-13 22:55:23', 1, 9, 1),
(8, 'MH_PTNS01', 'Nguyên lý và thiết bị phẫu thuật nội soi', 0, NULL, 'Cấu tạo dàn máy nội soi, dụng cụ, nguyên lý hình ảnh và khí CO2.', 4, 8, 12, 0.8, 1, '2026-04-22 17:50:27', '2026-06-13 22:55:25', 1, 9, 1),
(9, 'MH_PTNS02', 'Kỹ thuật thắt nút và khâu nội soi', 0, NULL, 'Thực hành thắt nút trong cơ thể, ngoài cơ thể, khâu mô trên mô hình.', 2, 16, 18, 1.5, 1, '2026-04-22 17:50:27', '2026-06-13 22:59:35', 1, 9, 1),
(10, 'MH_PTNS03', 'Cắt túi mật nội soi', 0, NULL, 'Quy trình cắt túi mật nội soi, xử trí tai biến thường gặp.', 4, 20, 24, 2.0, 1, '2026-04-22 17:50:27', '2026-06-13 22:59:49', 1, 9, 1),
(11, 'MH_PTNS04', 'Cắt ruột thừa nội soi', 0, NULL, 'Chỉ định, kỹ thuật cắt ruột thừa nội soi.', 2, 12, 14, 1.2, 1, '2026-04-22 17:50:27', '2026-06-13 22:59:57', 1, 9, 1),
(12, 'MH_PTNS05', 'Xử trí tai biến trong phẫu thuật nội soi', 0, NULL, 'Nhận biết và xử trí tổn thương mạch máu, thủng tạng, bỏng điện.', 4, 8, 12, 1.0, 1, '2026-04-22 17:50:27', '2026-06-13 22:59:59', 1, 9, 1),
(13, 'MH_DDHS01', 'Theo dõi bệnh nhân nặng', 0, NULL, 'Theo dõi dấu hiệu sinh tồn nâng cao, điểm cảnh báo sớm (EWS).', 6, 20, 26, 1.8, 1, '2026-04-22 17:50:27', '2026-06-13 23:00:02', 1, 9, 1),
(14, 'MH_DDHS02', 'Chăm sóc bệnh nhân thở máy', 0, NULL, 'Hút đờm kín, chăm sóc canul, phòng ngừa VAP.', 6, 24, 30, 2.2, 1, '2026-04-22 17:50:27', '2026-06-13 23:00:04', 1, 9, 1),
(15, 'MH_DDHS03', 'Chăm sóc bệnh nhân lọc máu liên tục (CRRT)', 0, NULL, 'Vận hành máy CRRT, thay dịch, phát hiện sự cố.', 6, 24, 30, 2.2, 1, '2026-04-22 17:50:27', '2026-06-13 23:00:07', 1, 9, 1),
(16, 'MH_DDHS04', 'Dinh dưỡng cho bệnh nhân hồi sức', 0, NULL, 'Dinh dưỡng qua sonde, qua đường tĩnh mạch trung tâm.', 6, 28, 34, 1.8, 1, '2026-04-22 17:50:27', '2026-06-13 23:00:09', 1, 9, 1),
(17, 'MH_SA01', 'Vật lý siêu âm và vận hành máy', 0, NULL, 'Nguyên lý sóng âm, các chế độ máy siêu âm.', 6, 8, 14, 1.0, 1, '2026-04-22 17:50:27', '2026-06-13 23:00:11', 1, 9, 1),
(18, 'MH_SA02', 'Siêu âm bụng tổng quát', 0, NULL, 'Khảo sát gan mật, thận, tuỵ, lách, ống tiêu hóa.', 8, 16, 24, 2.0, 1, '2026-04-22 17:50:27', '2026-06-13 23:00:13', 1, 9, 1),
(19, 'MH_SA03', 'Siêu âm sản phụ khoa cơ bản', 0, NULL, 'Siêu âm thai các quý, khảo sát tử cung phần phụ.', 6, 16, 22, 2.0, 1, '2026-04-22 17:50:27', '2026-06-13 23:00:16', 1, 9, 1),
(20, 'MH_QLCL01', 'Tổng quan hệ thống quản lý chất lượng bệnh viện', 0, NULL, 'JCI, ISO 9001, Bộ tiêu chí chất lượng BV Việt Nam.', 6, 0, 6, 0.5, 1, '2026-04-22 17:50:27', '2026-06-13 23:00:18', 1, 9, 1),
(21, 'MH_QLCL02', 'An toàn người bệnh', 0, NULL, 'Văn hóa an toàn, báo cáo sự cố, phân tích nguyên nhân gốc.', 6, 0, 6, 0.7, 1, '2026-04-22 17:50:27', '2026-06-13 23:00:21', 1, 9, 1),
(22, 'MH_QLCL03', 'Cải tiến chất lượng (PDCA, 5S)', 0, NULL, 'Công cụ cải tiến chất lượng, triển khai dự án cải tiến.', 4, 0, 4, 0.8, 1, '2026-04-22 17:50:27', '2026-06-13 23:00:24', 1, 9, 1),
(23, 'MH_TOT01', 'Phương pháp giảng dạy lâm sàng', 0, NULL, 'Mô hình giảng dạy bên giường bệnh, case-based learning.', 8, 8, 16, 1.3, 1, '2026-04-22 17:50:27', '2026-06-13 23:00:26', 1, 9, 1),
(24, 'MH_TOT02', 'Lượng giá kết quả học tập', 0, NULL, 'OSCE, Mini-CEX, DOPS, viết câu hỏi trắc nghiệm chất lượng.', 8, 8, 16, 1.5, 1, '2026-04-22 17:50:27', '2026-06-13 23:00:29', 1, 9, 1),
(25, 'CCCB_01', 'Bài 1: Phân loại bệnh nhân cấp cứu theo mức độ ưu tiên', 1, 40, '', 4, 20, 24, 0.0, 1, '2026-06-13 22:54:04', '2026-06-13 23:33:17', 9, 1, 0),
(26, 'CCCB_02', 'Bài 2: Hồi sinh tim phổi cơ bản', 2, 40, '', 4, 20, 24, 0.0, 1, '2026-06-13 22:54:28', '2026-06-13 23:39:04', 9, 1, 0),
(27, 'CCCB_03', 'Bài 3: Cấp cứu phản vệ', 3, 40, '', 4, 20, 24, 0.0, 1, '2026-06-13 22:54:52', '2026-06-13 23:35:04', 9, 1, 0),
(28, 'CCCB_04', 'Bài 4: Chẩn đoán và xử trí ngộ độc thuốc tê', 4, 40, '', 4, 20, 24, 0.0, 1, '2026-06-13 22:55:05', '2026-06-13 23:35:36', 9, 1, 0),
(29, 'CCCB_05', 'Bài 5: Định hướng chẩn đoán và xử trí người bệnh đau ngực cấp', 5, 40, '', 4, 20, 24, 0.0, 1, '2026-06-13 22:59:32', '2026-06-13 23:35:23', 1, 1, 0),
(30, 'CCCB_06', 'Bài 6: Định hướng chẩn đoán và xử trí đau bụng cấp ở người lớn', 6, 40, '', 4, 20, 24, 0.0, 1, '2026-06-13 22:59:32', '2026-06-13 23:35:47', 1, 1, 0),
(31, 'CCCB_07', 'Bài 7: Chẩn đoán và xử trí bệnh nhân xuất huyết tiêu hóa cao', 7, 40, '', 4, 20, 24, 0.0, 1, '2026-06-13 22:59:32', '2026-06-13 23:35:56', 1, 1, 0),
(32, 'CCCB_08', 'Bài 8: Định hướng chẩn đoán và xử trí trước tình trạng ngất', 8, 40, '', 4, 20, 24, 0.0, 1, '2026-06-13 22:59:32', '2026-06-13 23:36:06', 1, 1, 0),
(33, 'CCCB_09', 'Bài 9: Định hướng chẩn đoán và xử trí người bệnh hôn mê', 9, 40, '', 4, 20, 24, 0.0, 1, '2026-06-13 22:59:32', '2026-06-13 23:36:21', 1, 1, 0),
(34, 'CCCB_10', 'Bài 10: Định hướng chẩn đoán và xử trí cấp cứu trước một trường hợp ngộ độc', 10, 40, '', 4, 20, 24, 0.0, 1, '2026-06-13 22:59:32', '2026-06-13 23:36:47', 1, 1, 0),
(35, 'CCCB_11', 'Bài 11: Định hướng chẩn đoán và xử trí trước tình trạng đau đầu cấp', 11, 40, '', 4, 20, 24, 0.0, 1, '2026-06-13 22:59:32', '2026-06-13 23:36:52', 1, 1, 0),
(36, 'CCCB_12', 'Bài 12: Chẩn đoán và xử trí cơn hen phế quản nặng', 12, 40, '', 4, 20, 24, 0.0, 1, '2026-06-13 22:59:32', '2026-06-13 23:36:57', 1, 1, 0),
(37, 'CCCB_13', 'Bài 13: Chẩn đoán và xử trí đợt cấp bệnh phổi tắc nghẽn mãn tính', 13, 40, '', 4, 20, 24, 0.0, 1, '2026-06-13 22:59:32', '2026-06-13 23:37:03', 1, 1, 0),
(38, 'CCCB_14', 'Bài 14: Định hướng chẩn đoán và xử trí khó thở cấp ở người lớn', 14, 40, '', 4, 20, 24, 0.0, 1, '2026-06-13 22:59:32', '2026-06-13 23:37:12', 1, 1, 0),
(39, 'CCCB_15', 'Bài 15: Đánh giá và xử trí ban đầu một bệnh nhân đa chấn thương', 15, 40, '', 8, 20, 28, 0.0, 1, '2026-06-13 22:59:32', '2026-06-13 23:37:16', 1, 1, 0),
(40, 'CCCB_16', 'Bài 16: Tiếp cận và xử trí ban đầu một bệnh nhân sốc', 16, 40, '', 4, 20, 24, 0.0, 1, '2026-06-13 22:59:32', '2026-06-13 23:37:24', 1, 1, 0),
(41, 'CCCB_17', 'Bài 17: Sốc điện cấp cứu', 17, 40, '', 4, 20, 24, 0.0, 1, '2026-06-13 22:59:32', '2026-06-13 23:37:29', 1, 1, 0),
(42, 'CCCB_18', 'Bài 18: Phân tích kết quả khí máu động mạch', 18, 40, '', 8, 24, 32, 0.0, 1, '2026-06-13 22:59:32', '2026-06-13 23:37:34', 1, 1, 0),
(43, 'CCCB_19', 'Bài 19: Cài đặt ban đầu các thông số máy thở', 19, 40, '', 8, 20, 28, 0.0, 1, '2026-06-13 22:59:32', '2026-06-13 23:37:40', 1, 1, 0),
(44, 'CCCB_20', 'Bài 20: Quy trình kỹ thuật đặt nội khí quản cấp cứu', 20, 40, '', 4, 20, 24, 0.0, 1, '2026-06-13 22:59:32', '2026-06-13 23:37:57', 1, 1, 0),
(45, 'CCCB_21', 'Bài 21: Quy trình kỹ thuật đặt catheter tĩnh mạch trung tâm nhiều nòng dưới hướng dẫn siêu âm', 21, 40, '', 4, 20, 24, 0.0, 1, '2026-06-13 22:59:32', '2026-06-13 23:38:10', 1, 1, 0),
(46, 'CCCB_22', 'Bài 22: Quy trình kỹ thuật đặt catheter tĩnh mạch trung tâm nhiều nòng', 22, 40, '', 4, 20, 24, 0.0, 1, '2026-06-13 22:59:32', '2026-06-13 23:38:18', 1, 1, 0),
(47, 'CCCB_23', 'Khai giảng, kiểm tra đầu vào', 23, 40, '', 4, 0, 4, 0.0, 1, '2026-06-13 22:59:32', '2026-06-13 23:38:24', 1, 1, 0),
(48, 'CCCB_24', 'Kiểm tra giữa khóa học', 24, 40, '', 2, 2, 4, 0.0, 1, '2026-06-13 22:59:32', '2026-06-13 23:37:49', 1, 1, 0),
(49, 'CCCB_25', 'Thi kết thúc khóa học', 25, 40, '', 4, 8, 12, 0.0, 1, '2026-06-13 22:59:32', '2026-06-13 23:36:40', 1, 1, 0),
(50, 'CCCB_26', 'Tổng kết khóa học và nhận chứng chỉ', 26, 40, '', 0, 0, 0, 0.0, 1, '2026-06-13 22:59:32', '2026-06-13 23:36:33', 1, 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `dt_phan_cong_giang_vien`
--

CREATE TABLE `dt_phan_cong_giang_vien` (
  `id` int(11) NOT NULL,
  `giang_vien_id` int(11) NOT NULL,
  `khoa_hoc_chuong_trinh_id` int(11) NOT NULL,
  `mon_hoc_id` int(11) DEFAULT NULL,
  `vai_tro` tinyint(4) NOT NULL DEFAULT 1,
  `so_tiet_phan_cong` int(11) DEFAULT NULL,
  `tu_ngay` date DEFAULT NULL,
  `den_ngay` date DEFAULT NULL,
  `trang_thai` tinyint(4) NOT NULL DEFAULT 0,
  `ghi_chu` varchar(500) DEFAULT NULL,
  `ngay_tao` datetime DEFAULT current_timestamp(),
  `ngay_cap_nhat` datetime DEFAULT current_timestamp(),
  `nguoi_tao` int(11) DEFAULT NULL,
  `nguoi_cap_nhat` int(11) DEFAULT NULL,
  `da_xoa` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dt_tai_lieu`
--

CREATE TABLE `dt_tai_lieu` (
  `id` int(11) NOT NULL,
  `ma_tai_lieu` varchar(50) NOT NULL,
  `tieu_de` varchar(255) NOT NULL,
  `mo_ta` text DEFAULT NULL,
  `loai_tai_lieu` tinyint(4) NOT NULL DEFAULT 1,
  `dinh_dang` varchar(20) DEFAULT NULL,
  `file_name` varchar(255) DEFAULT NULL,
  `file_goc` varchar(255) DEFAULT NULL,
  `file_size` bigint(20) DEFAULT NULL,
  `link_ngoai` varchar(500) DEFAULT NULL,
  `tac_gia` varchar(200) DEFAULT NULL,
  `nam_xuat_ban` int(11) DEFAULT NULL,
  `nha_xuat_ban` varchar(200) DEFAULT NULL,
  `khoa_hoc_id` int(11) DEFAULT NULL,
  `khoa_hoc_chuong_trinh_id` int(11) DEFAULT NULL,
  `mon_hoc_id` int(11) DEFAULT NULL,
  `cong_khai` tinyint(4) NOT NULL DEFAULT 0,
  `bat_buoc` tinyint(4) NOT NULL DEFAULT 0,
  `luot_tai` int(11) NOT NULL DEFAULT 0,
  `luot_xem` int(11) NOT NULL DEFAULT 0,
  `trang_thai` tinyint(4) NOT NULL DEFAULT 1,
  `ghi_chu` varchar(500) DEFAULT NULL,
  `ngay_tao` datetime DEFAULT current_timestamp(),
  `ngay_cap_nhat` datetime DEFAULT current_timestamp(),
  `nguoi_tao` int(11) DEFAULT NULL,
  `nguoi_cap_nhat` int(11) DEFAULT NULL,
  `da_xoa` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `dt_tai_lieu`
--

INSERT INTO `dt_tai_lieu` (`id`, `ma_tai_lieu`, `tieu_de`, `mo_ta`, `loai_tai_lieu`, `dinh_dang`, `file_name`, `file_goc`, `file_size`, `link_ngoai`, `tac_gia`, `nam_xuat_ban`, `nha_xuat_ban`, `khoa_hoc_id`, `khoa_hoc_chuong_trinh_id`, `mon_hoc_id`, `cong_khai`, `bat_buoc`, `luot_tai`, `luot_xem`, `trang_thai`, `ghi_chu`, `ngay_tao`, `ngay_cap_nhat`, `nguoi_tao`, `nguoi_cap_nhat`, `da_xoa`) VALUES
(1, 'TL_0001', 'TEST', '123', 1, 'jpg', '20260614_211032_e2c179a54e60.jpg', 'sodo.JPG', 50889, NULL, NULL, NULL, NULL, 9, 23, 29, 0, 0, 0, 0, 1, NULL, '2026-06-14 21:10:32', '2026-06-14 21:10:32', 1, 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `nckh_de_tai`
--

CREATE TABLE `nckh_de_tai` (
  `id` int(11) NOT NULL,
  `ma_de_tai` varchar(50) NOT NULL,
  `ten_de_tai` varchar(500) NOT NULL,
  `nam` smallint(6) NOT NULL,
  `cap_do_id` int(11) NOT NULL,
  `the_loai_id` int(11) NOT NULL,
  `khoa_phong_id` int(11) DEFAULT NULL,
  `dot_dang_ky_id` int(11) DEFAULT NULL,
  `chu_nhiem_id` int(11) NOT NULL,
  `thu_ky_id` int(11) DEFAULT NULL,
  `muc_tieu` text DEFAULT NULL,
  `tom_tat` text DEFAULT NULL,
  `tu_khoa` varchar(255) DEFAULT NULL,
  `ngay_bat_dau` date DEFAULT NULL,
  `ngay_ket_thuc_du_kien` date DEFAULT NULL,
  `ngay_nghiem_thu` date DEFAULT NULL,
  `kinh_phi_du_toan` decimal(15,2) DEFAULT NULL,
  `kinh_phi_thuc_te` decimal(15,2) DEFAULT NULL,
  `nguon_kinh_phi` varchar(150) DEFAULT NULL,
  `quyet_dinh_phe_duyet` varchar(100) DEFAULT NULL,
  `ngay_quyet_dinh` date DEFAULT NULL,
  `ket_qua_xep_loai` enum('XuatSac','Gioi','Kha','TrungBinhKha','Dat','KhongDat') DEFAULT NULL,
  `diem_so` decimal(4,2) DEFAULT NULL,
  `noi_dung_ung_dung` text DEFAULT NULL,
  `ten_tap_chi` varchar(255) DEFAULT NULL,
  `so_tap_chi` varchar(50) DEFAULT NULL,
  `nam_xuat_ban` smallint(6) DEFAULT NULL,
  `issn_doi` varchar(100) DEFAULT NULL,
  `link_bai_bao` varchar(500) DEFAULT NULL,
  `trang_thai` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0=DeXuat, 1=DangThucHien, 2=HoanThanh, 3=TamDung, 4=Huy',
  `ngay_tao` datetime DEFAULT current_timestamp(),
  `ngay_cap_nhat` datetime DEFAULT current_timestamp(),
  `nguoi_tao` int(11) DEFAULT NULL,
  `nguoi_cap_nhat` int(11) DEFAULT NULL,
  `da_xoa` int(11) NOT NULL DEFAULT 0,
  `phien_bao_ve` varchar(50) DEFAULT NULL COMMENT 'VD: Phiên 1 - Từ 8h00',
  `dia_diem_bao_ve` varchar(255) DEFAULT NULL COMMENT 'Hội trường / VP khoa',
  `ngay_bao_ve` date DEFAULT NULL,
  `quyet_dinh_cong_nhan` varchar(100) DEFAULT NULL COMMENT 'Số QĐ công nhận/nghiệm thu',
  `ngay_quyet_dinh_cong_nhan` date DEFAULT NULL,
  `ten_khoa_text` varchar(255) DEFAULT NULL COMMENT 'Tên khoa nguyên gốc khi không match FK',
  `trang_thai_duyet` enum('Nhap','ChoDuyet','DaDuyet','TuChoi') NOT NULL DEFAULT 'Nhap' COMMENT 'Trạng thái duyệt do admin xử lý',
  `ngay_gui_duyet` datetime DEFAULT NULL,
  `ngay_xu_ly_duyet` datetime DEFAULT NULL,
  `nguoi_xu_ly_duyet` int(11) DEFAULT NULL,
  `ly_do_tu_choi` varchar(1000) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `nckh_de_tai`
--

INSERT INTO `nckh_de_tai` (`id`, `ma_de_tai`, `ten_de_tai`, `nam`, `cap_do_id`, `the_loai_id`, `khoa_phong_id`, `dot_dang_ky_id`, `chu_nhiem_id`, `thu_ky_id`, `muc_tieu`, `tom_tat`, `tu_khoa`, `ngay_bat_dau`, `ngay_ket_thuc_du_kien`, `ngay_nghiem_thu`, `kinh_phi_du_toan`, `kinh_phi_thuc_te`, `nguon_kinh_phi`, `quyet_dinh_phe_duyet`, `ngay_quyet_dinh`, `ket_qua_xep_loai`, `diem_so`, `noi_dung_ung_dung`, `ten_tap_chi`, `so_tap_chi`, `nam_xuat_ban`, `issn_doi`, `link_bai_bao`, `trang_thai`, `ngay_tao`, `ngay_cap_nhat`, `nguoi_tao`, `nguoi_cap_nhat`, `da_xoa`, `phien_bao_ve`, `dia_diem_bao_ve`, `ngay_bao_ve`, `quyet_dinh_cong_nhan`, `ngay_quyet_dinh_cong_nhan`, `ten_khoa_text`, `trang_thai_duyet`, `ngay_gui_duyet`, `ngay_xu_ly_duyet`, `nguoi_xu_ly_duyet`, `ly_do_tu_choi`) VALUES
(1, 'DT-2026-01', 'Nghiên cứu hiệu quả điều trị suy tim mạn bằng thuốc ức chế SGLT2', 2026, 1, 1, 1, NULL, 1, 2, 'Đánh giá hiệu quả lâm sàng và an toàn của Dapagliflozin trên bệnh nhân suy tim phân suất tống máu giảm.', 'Nghiên cứu thuần tập trên 120 bệnh nhân suy tim mạn nhập viện điều trị tại khoa Tim mạch.', 'suy tim, SGLT2, Dapagliflozin', '2026-01-15', '2026-06-11', NULL, 50000000.00, NULL, 'Ngân sách bệnh viện', 'QĐ-NCKH-2026/01', '2026-01-10', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-04-27 23:00:50', '2026-04-27 23:00:50', 1, 1, 0, NULL, NULL, NULL, NULL, NULL, NULL, 'DaDuyet', NULL, NULL, NULL, NULL),
(2, 'DT-2026-02', 'Sáng kiến quy trình cấp cứu đột quỵ thiếu máu não cục bộ trong giờ vàng', 2026, 1, 2, 3, NULL, 3, NULL, 'Rút ngắn thời gian Door-to-Needle xuống dưới 45 phút.', NULL, 'đột quỵ, giờ vàng, tPA', '2026-02-01', '2026-05-07', NULL, 15000000.00, NULL, 'Khoa cấp cứu', 'QĐ-SK-2026/02', '2026-01-25', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-04-27 23:00:50', '2026-04-27 23:00:50', 1, 1, 0, NULL, NULL, NULL, NULL, NULL, NULL, 'DaDuyet', NULL, NULL, NULL, NULL),
(3, 'DT-2026-03', 'Bài báo: Đặc điểm lâm sàng và cận lâm sàng của viêm phổi do COVID-19 ở trẻ em', 2026, 2, 4, 2, NULL, 4, 5, NULL, 'Tổng hợp dữ liệu 200 ca trẻ em COVID-19 nhập viện 2022-2024.', 'COVID-19, trẻ em, viêm phổi', '2026-03-01', '2026-12-31', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Tạp chí Y học Việt Nam', '548', 2026, '1859-1868', 'https://example.org/yhvn/548', 0, '2026-04-27 23:00:50', '2026-04-27 23:00:50', 1, 1, 0, NULL, NULL, NULL, NULL, NULL, NULL, 'DaDuyet', NULL, NULL, NULL, NULL),
(4, 'DT-2026-04', 'Khảo sát mức độ hài lòng của người bệnh nội trú năm 2026', 2026, 1, 1, 1, NULL, 2, 6, 'Đánh giá chất lượng dịch vụ qua bộ câu hỏi 30 mục.', NULL, 'hài lòng, chất lượng, bệnh viện', '2026-01-01', '2026-03-28', '2026-04-12', 20000000.00, 18500000.00, NULL, 'QĐ-NCKH-2026/04', '2026-01-05', 'XuatSac', 9.20, 'Áp dụng để cải tiến quy trình tiếp đón và điều dưỡng ở 4 khoa.', NULL, NULL, NULL, NULL, NULL, 2, '2026-04-27 23:00:50', '2026-04-27 23:00:50', 1, 1, 0, NULL, NULL, NULL, NULL, NULL, NULL, 'DaDuyet', NULL, NULL, NULL, NULL),
(5, 'DT-2025-01', 'Báo cáo Hội nghị: Ứng dụng AI hỗ trợ chẩn đoán hình ảnh X-quang phổi', 2025, 3, 3, 4, NULL, 3, NULL, NULL, 'Trình bày tại Hội nghị Chẩn đoán hình ảnh Việt Nam 2025.', 'AI, X-quang phổi, chẩn đoán hình ảnh', '2025-06-01', '2025-12-15', '2025-12-20', NULL, NULL, NULL, NULL, NULL, 'Kha', 8.50, NULL, NULL, NULL, NULL, NULL, NULL, 2, '2026-04-27 23:00:50', '2026-04-27 23:00:50', 1, 1, 0, NULL, NULL, NULL, NULL, NULL, NULL, 'DaDuyet', NULL, NULL, NULL, NULL),
(6, 'DT-2025-02', 'Sáng kiến mã hóa hồ sơ bệnh án điện tử theo chuẩn HL7-FHIR', 2025, 2, 2, 4, NULL, 6, NULL, 'Thiết lập hệ thống chuyển đổi HSBA sang định dạng FHIR R4.', NULL, 'HL7, FHIR, EMR', '2025-03-01', '2025-12-31', NULL, 80000000.00, NULL, 'Quỹ phát triển', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 3, '2026-04-27 23:00:50', '2026-04-27 23:00:50', 1, 1, 0, NULL, NULL, NULL, NULL, NULL, NULL, 'DaDuyet', NULL, NULL, NULL, NULL),
(7, 'DT-2026-05', 'Đánh giá hiệu quả phục hồi chức năng thăng bằng trên bệnh nhân đột quỵ não điều trị nội khoa tại Bệnh viện Hữu nghị Đa khoa Nghệ An', 2026, 1, 1, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-04-28 22:28:56', '2026-04-28 22:28:56', 1, 1, 0, 'Phiên 1 - Từ 8h00', 'Hội trường 3', NULL, NULL, NULL, 'Phục hồi chức năng', 'DaDuyet', NULL, NULL, NULL, NULL),
(8, 'DT-2026-06', 'Đánh giá kết quả điều trị phục hồi chức năng vận động cho bệnh nhân chấn thương sọ não tại khoa phục hồi chức năng Bệnh viện Hữu nghị Đa khoa Nghệ An', 2026, 1, 1, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-04-28 22:28:56', '2026-04-28 22:28:56', 1, 1, 0, 'Phiên 1 - Từ 8h00', 'Hội trường 3', NULL, NULL, NULL, 'Phục hồi chức năng', 'DaDuyet', NULL, NULL, NULL, NULL),
(9, 'DT-2026-07', 'Đánh giá kết quả phục hồi chức năng khớp vai ở người bệnh cấy máy tạo nhịp tim vĩnh viễn tại Bệnh viện hữu nghị đa khoa Nghệ An năm 2026', 2026, 1, 1, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-04-28 22:28:56', '2026-04-28 22:28:56', 1, 1, 0, 'Phiên 1 - Từ 8h00', 'Hội trường 3', NULL, NULL, NULL, 'Phục hồi chức năng', 'DaDuyet', NULL, NULL, NULL, NULL),
(10, 'DT-2026-08', 'Nghiên cứu chất lượng giấc ngủ và một số yếu tố liên quan ở bệnh nhân đa u tủy xương tại bệnh viện hữu nghị đa khoa Nghệ An', 2026, 1, 1, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-04-28 22:28:56', '2026-04-28 22:28:56', 1, 1, 0, 'Phiên 1 - Từ 8h00', 'VP Khoa thăm dò chức năng', NULL, NULL, NULL, 'Huyết học lâm sàng', 'DaDuyet', NULL, NULL, NULL, NULL),
(11, 'DT-2026-09', 'Khảo sát mức độ nhạy cảm kháng sinh Ceftazidim/avibactam của Pseudomonas aeruginosa phân lập được tại bệnh viện Hữu Nghị Đa khoa Nghệ An', 2026, 1, 1, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-04-28 22:28:56', '2026-04-28 22:28:56', 1, 1, 0, 'Phiên 1 - Từ 8h00', 'VP Khoa thăm dò chức năng', NULL, NULL, NULL, 'Vi sinh - Trung tâm xét nghiệm', 'DaDuyet', NULL, NULL, NULL, NULL),
(12, 'DT-2026-10', 'Khảo sát tỷ lệ dương tính của chỉ số Hbc IgM và Hbc ToTal ở bệnh nhân nhiễm viêm gan B', 2026, 1, 1, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-04-28 22:28:56', '2026-04-28 22:28:56', 1, 1, 0, 'Phiên 1 - Từ 8h00', 'VP Khoa thăm dò chức năng', NULL, NULL, NULL, 'Vi sinh - Trung tâm xét nghiệm', 'DaDuyet', NULL, NULL, NULL, NULL),
(13, 'DT-2026-11', 'Đánh giá phẫu thuật tái tạo thành ống tai và bít lấp hốc mổ chũm tại bệnh viện HNĐK Nghệ An', 2026, 1, 1, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-04-28 22:28:56', '2026-04-28 22:28:56', 1, 1, 0, 'Phiên 1 - Từ 8h00', 'VP Khoa thăm dò chức năng', NULL, NULL, NULL, 'Tai mũi họng', 'DaDuyet', NULL, NULL, NULL, NULL),
(14, 'DT-2026-12', 'Kết quả điều trị áp xe quanh amidan tại bệnh viện HNĐK Nghệ An', 2026, 1, 1, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-04-28 22:28:56', '2026-04-28 22:28:56', 1, 1, 0, 'Phiên 2 - Từ 13h30', 'VP Khoa thăm dò chức năng', NULL, NULL, NULL, 'Tai mũi họng', 'DaDuyet', NULL, NULL, NULL, NULL),
(15, 'DT-2026-13', 'Kết quả của phương pháp hút trực tiếp (Adapt) trong điều trị nhồi máu não cấp do tắc mạch lớn tại Bệnh viện Hữu nghị Đa khoa Nghệ An', 2026, 1, 1, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-04-28 22:28:56', '2026-04-28 22:28:56', 1, 1, 0, 'Phiên 2 - Từ 13h30', 'VP Khoa thăm dò chức năng', NULL, NULL, NULL, 'Trung tâm đột quỵ', 'DaDuyet', NULL, NULL, NULL, NULL),
(16, 'DT-2026-14', 'Nghiên cứu kết quả điều trị tiêu sợi huyết đường tĩnh mạch ở bệnh nhân nhồi máu não cấp ở cửa sổ mở rộng 4,5h đến 9h tại Bệnh viện Hữu nghị Đa khoa Nghệ An', 2026, 1, 1, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-04-28 22:28:56', '2026-04-28 22:28:56', 1, 1, 0, 'Phiên 2 - Từ 13h30', 'VP Khoa thăm dò chức năng', NULL, NULL, NULL, 'Trung tâm đột quỵ', 'DaDuyet', NULL, NULL, NULL, NULL),
(17, 'DT-2026-15', 'Nghiên cứu đặc điểm lâm sàng, cận lâm sàng và tính kháng kháng sinh của bệnh nhân nhiễm Streptococcus suis tại Bệnh viện Hữu Nghị đa khoa Nghệ An giai đoạn 2024-2026', 2026, 1, 1, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-04-28 22:28:56', '2026-04-28 22:28:56', 1, 1, 0, 'Phiên 2 - Từ 13h30', 'VP Khoa thăm dò chức năng', NULL, NULL, NULL, 'Vi sinh - Trung tâm xét nghiệm', 'DaDuyet', NULL, NULL, NULL, NULL),
(18, 'DT-2026-16', 'Đánh giá kết quả phẫu thuật điều trị ung thư dạ dày ở người cao tuổi tại Bệnh viện Hữu nghị Đa khoa Nghệ An', 2026, 1, 1, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-04-28 22:28:56', '2026-04-28 22:28:56', 1, 1, 0, 'Phiên 1 - Từ 8h00', 'VP khoa Ngoại tổng hợp 2', NULL, NULL, NULL, 'Ngoại tiêu hóa', 'DaDuyet', NULL, NULL, NULL, NULL),
(19, 'DT-2026-17', 'Kết quả phẫu thuật tán sỏi qua da đường hầm tiêu chuẩn điều trị sỏi thận kích thước > 2cm dưới hướng dẫn siêu âm tại BV HNĐK NA', 2026, 1, 1, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-04-28 22:28:56', '2026-04-28 22:28:56', 1, 1, 0, 'Phiên 1 - Từ 8h00', 'VP khoa Ngoại tổng hợp 2', NULL, NULL, NULL, 'Ngoại Thận - Tiết niệu', 'DaDuyet', NULL, NULL, NULL, NULL),
(20, 'DT-2026-18', 'Đánh giá kết quả phẫu thuật nội soi cắt u vỏ tuyến thượng thận lành tính tại bệnh viện Hữu Nghị Đa Khoa Nghệ An', 2026, 1, 1, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-04-28 22:28:56', '2026-04-28 22:28:56', 1, 1, 0, 'Phiên 1 - Từ 8h00', 'VP khoa Ngoại tổng hợp 2', NULL, NULL, NULL, 'Ngoại Thận - Tiết niệu', 'DaDuyet', NULL, NULL, NULL, NULL),
(21, 'DT-2026-19', 'Vai trò của cắt lớp vi tính trong chẩn đoán, phân độ và định hướng xử trí chấn thương gan theo phân loại AAST2018', 2026, 1, 1, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-04-28 22:28:56', '2026-04-28 22:28:56', 1, 1, 0, 'Phiên 1 - Từ 8h00', 'VP khoa Ngoại tổng hợp 2', NULL, NULL, NULL, 'Xquang', 'DaDuyet', NULL, NULL, NULL, NULL),
(22, 'DT-2026-20', 'Thực trạng công tác chăm sóc dinh dưỡng cho người bệnh đái tháo đường típ 2 điều trị tại khoa Nội tiết - Bệnh viện hữu nghị đa khoa Nghệ An năm 2026', 2026, 1, 1, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-04-28 22:28:56', '2026-04-28 22:28:56', 1, 1, 0, 'Phiên 2 - Từ 13h30', 'VP khoa Ngoại tổng hợp 2', NULL, NULL, NULL, 'Nội tiết', 'DaDuyet', NULL, NULL, NULL, NULL),
(23, 'DT-2026-021', 'Test đề ya', 2026, 1, 1, 6, NULL, 21, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, '2026-04-29 20:22:53', '2026-05-01 12:26:45', 1, 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, 'DaDuyet', '2026-04-29 20:23:49', '2026-04-30 08:48:28', 1, NULL),
(24, 'DT-2026-022', 'Đề tài phòng cntt', 2026, 1, 1, 11, NULL, 21, 7, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, '2026-04-29 20:28:37', '2026-05-01 12:26:46', 8, 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, 'DaDuyet', '2026-04-29 20:28:58', '2026-04-30 08:48:07', 1, NULL),
(25, 'ádasdasd', 'test đề', 2026, 1, 1, 5, 1, 21, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, '2026-05-04 20:32:27', '2026-05-05 17:20:32', 8, 1, 0, NULL, NULL, NULL, NULL, NULL, NULL, 'DaDuyet', '2026-05-04 22:16:08', '2026-05-05 17:20:32', 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `nckh_dot_dang_ky`
--

CREATE TABLE `nckh_dot_dang_ky` (
  `id` int(11) NOT NULL,
  `ten_dot` varchar(255) NOT NULL,
  `nam` smallint(6) NOT NULL,
  `tu_ngay` date NOT NULL,
  `den_ngay` date NOT NULL,
  `mo_ta` text DEFAULT NULL,
  `trang_thai` tinyint(4) NOT NULL DEFAULT 1 COMMENT '1=HoatDong, 0=Khoa',
  `ngay_tao` datetime DEFAULT current_timestamp(),
  `ngay_cap_nhat` datetime DEFAULT current_timestamp(),
  `nguoi_tao` int(11) DEFAULT NULL,
  `nguoi_cap_nhat` int(11) DEFAULT NULL,
  `da_xoa` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `nckh_dot_dang_ky`
--

INSERT INTO `nckh_dot_dang_ky` (`id`, `ten_dot`, `nam`, `tu_ngay`, `den_ngay`, `mo_ta`, `trang_thai`, `ngay_tao`, `ngay_cap_nhat`, `nguoi_tao`, `nguoi_cap_nhat`, `da_xoa`) VALUES
(1, 'Đợt 1', 2026, '2026-05-04', '2026-05-08', NULL, 1, '2026-05-04 20:28:44', '2026-05-04 20:28:44', 1, 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `nckh_dot_giai_doan`
--

CREATE TABLE `nckh_dot_giai_doan` (
  `id` int(11) NOT NULL,
  `dot_id` int(11) NOT NULL,
  `ten_giai_doan` varchar(255) NOT NULL,
  `hanh_vi` enum('Submit','Edit','Review') NOT NULL DEFAULT 'Submit',
  `tu_ngay` datetime NOT NULL,
  `den_ngay` datetime NOT NULL,
  `thu_tu` int(11) NOT NULL DEFAULT 0,
  `ghi_chu` varchar(500) DEFAULT NULL,
  `ngay_tao` datetime DEFAULT current_timestamp(),
  `ngay_cap_nhat` datetime DEFAULT current_timestamp(),
  `nguoi_tao` int(11) DEFAULT NULL,
  `nguoi_cap_nhat` int(11) DEFAULT NULL,
  `da_xoa` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `nckh_dot_giai_doan`
--

INSERT INTO `nckh_dot_giai_doan` (`id`, `dot_id`, `ten_giai_doan`, `hanh_vi`, `tu_ngay`, `den_ngay`, `thu_tu`, `ghi_chu`, `ngay_tao`, `ngay_cap_nhat`, `nguoi_tao`, `nguoi_cap_nhat`, `da_xoa`) VALUES
(1, 1, 'Đăng ký thành viên', 'Submit', '2026-05-04 00:00:00', '2026-06-05 00:00:00', 0, NULL, '2026-05-04 20:31:46', '2026-05-04 20:31:46', 1, 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `nckh_hoi_dong`
--

CREATE TABLE `nckh_hoi_dong` (
  `id` int(11) NOT NULL,
  `de_tai_id` int(11) NOT NULL,
  `ho_ten` varchar(150) NOT NULL,
  `chuc_danh_hoc_vi` varchar(50) DEFAULT NULL COMMENT 'BSCKII., ThS., TS., BSNT....',
  `nhan_vien_id` int(11) DEFAULT NULL,
  `ten_khoa_text` varchar(255) DEFAULT NULL COMMENT 'Tên khoa nguyên gốc',
  `khoa_phong_id` int(11) DEFAULT NULL,
  `vai_tro_hd` enum('ChuTich','ThuKy','PhanBien1','PhanBien2','ThanhVien') NOT NULL DEFAULT 'ThanhVien',
  `thu_tu` int(11) NOT NULL DEFAULT 0,
  `ghi_chu` varchar(500) DEFAULT NULL,
  `ngay_tao` datetime DEFAULT current_timestamp(),
  `ngay_cap_nhat` datetime DEFAULT current_timestamp(),
  `nguoi_tao` int(11) DEFAULT NULL,
  `nguoi_cap_nhat` int(11) DEFAULT NULL,
  `da_xoa` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `nckh_hoi_dong`
--

INSERT INTO `nckh_hoi_dong` (`id`, `de_tai_id`, `ho_ten`, `chuc_danh_hoc_vi`, `nhan_vien_id`, `ten_khoa_text`, `khoa_phong_id`, `vai_tro_hd`, `thu_tu`, `ghi_chu`, `ngay_tao`, `ngay_cap_nhat`, `nguoi_tao`, `nguoi_cap_nhat`, `da_xoa`) VALUES
(1, 7, 'Trần Văn Quân', 'BSCKII.', NULL, 'Chấn thương - chỉnh hình', NULL, 'ChuTich', 0, NULL, '2026-04-28 22:28:56', '2026-04-28 22:28:56', 1, 1, 0),
(2, 7, 'Nguyễn Ngọc Sơn', 'BSNT.', NULL, 'Bỏng', NULL, 'ThuKy', 1, NULL, '2026-04-28 22:28:56', '2026-04-28 22:28:56', 1, 1, 0),
(3, 7, 'Trần Cương', 'BSCK2.', NULL, 'Chấn thương - chỉnh hình', NULL, 'PhanBien1', 2, NULL, '2026-04-28 22:28:56', '2026-04-28 22:28:56', 1, 1, 0),
(4, 7, 'Phan Ngọc Khóa', 'BSCK2.', NULL, 'Phẫu thuật thẩm mỹ', NULL, 'PhanBien2', 3, NULL, '2026-04-28 22:28:56', '2026-04-28 22:28:56', 1, 1, 0),
(5, 7, 'Nguyễn Phan Chương', 'BSNT.', NULL, 'Chấn thương - chỉnh hình', NULL, 'ThanhVien', 4, NULL, '2026-04-28 22:28:56', '2026-04-28 22:28:56', 1, 1, 0),
(6, 8, 'Trần Văn Quân', 'BSCKII.', NULL, 'Chấn thương - chỉnh hình', NULL, 'ChuTich', 0, NULL, '2026-04-28 22:28:56', '2026-04-28 22:28:56', 1, 1, 0),
(7, 8, 'Nguyễn Ngọc Sơn', 'BSNT.', NULL, 'Bỏng', NULL, 'ThuKy', 1, NULL, '2026-04-28 22:28:56', '2026-04-28 22:28:56', 1, 1, 0),
(8, 8, 'Trần Cương', 'BSCK2.', NULL, 'Chấn thương - chỉnh hình', NULL, 'PhanBien1', 2, NULL, '2026-04-28 22:28:56', '2026-04-28 22:28:56', 1, 1, 0),
(9, 8, 'Phan Ngọc Khóa', 'BSCK2.', NULL, 'Phẫu thuật thẩm mỹ', NULL, 'PhanBien2', 3, NULL, '2026-04-28 22:28:56', '2026-04-28 22:28:56', 1, 1, 0),
(10, 8, 'Nguyễn Phan Chương', 'BSNT.', NULL, 'Chấn thương - chỉnh hình', NULL, 'ThanhVien', 4, NULL, '2026-04-28 22:28:56', '2026-04-28 22:28:56', 1, 1, 0),
(11, 9, 'Trần Văn Quân', 'BSCKII.', NULL, 'Chấn thương - chỉnh hình', NULL, 'ChuTich', 0, NULL, '2026-04-28 22:28:56', '2026-04-28 22:28:56', 1, 1, 0),
(12, 9, 'Nguyễn Ngọc Sơn', 'BSNT.', NULL, 'Bỏng', NULL, 'ThuKy', 1, NULL, '2026-04-28 22:28:56', '2026-04-28 22:28:56', 1, 1, 0),
(13, 9, 'Trần Cương', 'BSCK2.', NULL, 'Chấn thương - chỉnh hình', NULL, 'PhanBien1', 2, NULL, '2026-04-28 22:28:56', '2026-04-28 22:28:56', 1, 1, 0),
(14, 9, 'Phan Ngọc Khóa', 'BSCK2.', NULL, 'Phẫu thuật thẩm mỹ', NULL, 'PhanBien2', 3, NULL, '2026-04-28 22:28:56', '2026-04-28 22:28:56', 1, 1, 0),
(15, 9, 'Nguyễn Phan Chương', 'BSNT.', NULL, 'Chấn thương - chỉnh hình', NULL, 'ThanhVien', 4, NULL, '2026-04-28 22:28:56', '2026-04-28 22:28:56', 1, 1, 0),
(16, 10, 'Nguyễn Thanh Long', 'TS.', NULL, 'Trung tâm đột quỵ', NULL, 'ChuTich', 0, NULL, '2026-04-28 22:28:56', '2026-04-28 22:28:56', 1, 1, 0),
(17, 10, 'Phùng Đức Lâm', 'ThS.', NULL, 'Trung tâm đột quỵ', NULL, 'ThuKy', 1, NULL, '2026-04-28 22:28:56', '2026-04-28 22:28:56', 1, 1, 0),
(18, 10, 'Phạm Thế Cường', 'ThS.', NULL, 'Điều dưỡng', NULL, 'PhanBien1', 2, NULL, '2026-04-28 22:28:56', '2026-04-28 22:28:56', 1, 1, 0),
(19, 10, 'Lê Xuân Vựng', 'ThS.', NULL, 'Nội Dị ứng - Hô hấp', NULL, 'PhanBien2', 3, NULL, '2026-04-28 22:28:56', '2026-04-28 22:28:56', 1, 1, 0),
(20, 10, 'Hoàng Danh Tân', 'ThS.', NULL, 'Dị ứng - Miễn dịch lâm sàng', NULL, 'ThanhVien', 4, NULL, '2026-04-28 22:28:56', '2026-04-28 22:28:56', 1, 1, 0),
(21, 11, 'Nguyễn Thanh Long', 'TS.', NULL, 'Trung tâm đột quỵ', NULL, 'ChuTich', 0, NULL, '2026-04-28 22:28:56', '2026-04-28 22:28:56', 1, 1, 0),
(22, 11, 'Phùng Đức Lâm', 'ThS.', NULL, 'Trung tâm đột quỵ', NULL, 'ThuKy', 1, NULL, '2026-04-28 22:28:56', '2026-04-28 22:28:56', 1, 1, 0),
(23, 11, 'Phạm Thế Cường', 'ThS.', NULL, 'Điều dưỡng', NULL, 'PhanBien1', 2, NULL, '2026-04-28 22:28:56', '2026-04-28 22:28:56', 1, 1, 0),
(24, 11, 'Lê Xuân Vựng', 'ThS.', NULL, 'Nội Dị ứng - Hô hấp', NULL, 'PhanBien2', 3, NULL, '2026-04-28 22:28:56', '2026-04-28 22:28:56', 1, 1, 0),
(25, 11, 'Hoàng Danh Tân', 'ThS.', NULL, 'Dị ứng - Miễn dịch lâm sàng', NULL, 'ThanhVien', 4, NULL, '2026-04-28 22:28:56', '2026-04-28 22:28:56', 1, 1, 0),
(26, 12, 'Nguyễn Thanh Long', 'TS.', NULL, 'Trung tâm đột quỵ', NULL, 'ChuTich', 0, NULL, '2026-04-28 22:28:56', '2026-04-28 22:28:56', 1, 1, 0),
(27, 12, 'Phùng Đức Lâm', 'ThS.', NULL, 'Trung tâm đột quỵ', NULL, 'ThuKy', 1, NULL, '2026-04-28 22:28:56', '2026-04-28 22:28:56', 1, 1, 0),
(28, 12, 'Phạm Thế Cường', 'ThS.', NULL, 'Điều dưỡng', NULL, 'PhanBien1', 2, NULL, '2026-04-28 22:28:56', '2026-04-28 22:28:56', 1, 1, 0),
(29, 12, 'Lê Xuân Vựng', 'ThS.', NULL, 'Nội Dị ứng - Hô hấp', NULL, 'PhanBien2', 3, NULL, '2026-04-28 22:28:56', '2026-04-28 22:28:56', 1, 1, 0),
(30, 12, 'Hoàng Danh Tân', 'ThS.', NULL, 'Dị ứng - Miễn dịch lâm sàng', NULL, 'ThanhVien', 4, NULL, '2026-04-28 22:28:56', '2026-04-28 22:28:56', 1, 1, 0),
(31, 13, 'Nguyễn Thanh Long', 'TS.', NULL, 'Trung tâm đột quỵ', NULL, 'ChuTich', 0, NULL, '2026-04-28 22:28:56', '2026-04-28 22:28:56', 1, 1, 0),
(32, 13, 'Phùng Đức Lâm', 'ThS.', NULL, 'Trung tâm đột quỵ', NULL, 'ThuKy', 1, NULL, '2026-04-28 22:28:56', '2026-04-28 22:28:56', 1, 1, 0),
(33, 13, 'Lê Xuân Vựng', 'ThS.', NULL, 'Nội Dị ứng - Hô hấp', NULL, 'PhanBien1', 2, NULL, '2026-04-28 22:28:56', '2026-04-28 22:28:56', 1, 1, 0),
(34, 13, 'Nguyễn Thị Hoài Trang', 'BSCK2.', NULL, 'Khám bệnh', NULL, 'PhanBien2', 3, NULL, '2026-04-28 22:28:56', '2026-04-28 22:28:56', 1, 1, 0),
(35, 13, 'Hoàng Danh Tân', 'ThS.', NULL, 'Dị ứng - Miễn dịch lâm sàng', NULL, 'ThanhVien', 4, NULL, '2026-04-28 22:28:56', '2026-04-28 22:28:56', 1, 1, 0),
(36, 14, 'Nguyễn Thanh Long', 'TS.', NULL, 'Trung tâm đột quỵ', NULL, 'ChuTich', 0, NULL, '2026-04-28 22:28:56', '2026-04-28 22:28:56', 1, 1, 0),
(37, 14, 'Phùng Đức Lâm', 'ThS.', NULL, 'Trung tâm đột quỵ', NULL, 'ThuKy', 1, NULL, '2026-04-28 22:28:56', '2026-04-28 22:28:56', 1, 1, 0),
(38, 14, 'Lê Xuân Vựng', 'ThS.', NULL, 'Nội Dị ứng - Hô hấp', NULL, 'PhanBien1', 2, NULL, '2026-04-28 22:28:56', '2026-04-28 22:28:56', 1, 1, 0),
(39, 14, 'Nguyễn Thị Hoài Trang', 'BSCK2.', NULL, 'Khám bệnh', NULL, 'PhanBien2', 3, NULL, '2026-04-28 22:28:56', '2026-04-28 22:28:56', 1, 1, 0),
(40, 14, 'Hoàng Danh Tân', 'ThS.', NULL, 'Dị ứng - Miễn dịch lâm sàng', NULL, 'ThanhVien', 4, NULL, '2026-04-28 22:28:56', '2026-04-28 22:28:56', 1, 1, 0),
(41, 15, 'Nguyễn Thanh Long', 'TS.', NULL, 'Trung tâm đột quỵ', NULL, 'ChuTich', 0, NULL, '2026-04-28 22:28:56', '2026-04-28 22:28:56', 1, 1, 0),
(42, 15, 'Phùng Đức Lâm', 'ThS.', NULL, 'Trung tâm đột quỵ', NULL, 'ThuKy', 1, NULL, '2026-04-28 22:28:56', '2026-04-28 22:28:56', 1, 1, 0),
(43, 15, 'Lê Xuân Vựng', 'ThS.', NULL, 'Nội Dị ứng - Hô hấp', NULL, 'PhanBien1', 2, NULL, '2026-04-28 22:28:56', '2026-04-28 22:28:56', 1, 1, 0),
(44, 15, 'Nguyễn Thị Hoài Trang', 'BSCK2.', NULL, 'Khám bệnh', NULL, 'PhanBien2', 3, NULL, '2026-04-28 22:28:56', '2026-04-28 22:28:56', 1, 1, 0),
(45, 15, 'Hoàng Danh Tân', 'ThS.', NULL, 'Dị ứng - Miễn dịch lâm sàng', NULL, 'ThanhVien', 4, NULL, '2026-04-28 22:28:56', '2026-04-28 22:28:56', 1, 1, 0),
(46, 16, 'Nguyễn Thanh Long', 'TS.', NULL, 'Trung tâm đột quỵ', NULL, 'ChuTich', 0, NULL, '2026-04-28 22:28:56', '2026-04-28 22:28:56', 1, 1, 0),
(47, 16, 'Phùng Đức Lâm', 'ThS.', NULL, 'Trung tâm đột quỵ', NULL, 'ThuKy', 1, NULL, '2026-04-28 22:28:56', '2026-04-28 22:28:56', 1, 1, 0),
(48, 16, 'Lê Xuân Vựng', 'ThS.', NULL, 'Nội Dị ứng - Hô hấp', NULL, 'PhanBien1', 2, NULL, '2026-04-28 22:28:56', '2026-04-28 22:28:56', 1, 1, 0),
(49, 16, 'Nguyễn Thị Hoài Trang', 'BSCK2.', NULL, 'Khám bệnh', NULL, 'PhanBien2', 3, NULL, '2026-04-28 22:28:56', '2026-04-28 22:28:56', 1, 1, 0),
(50, 16, 'Hoàng Danh Tân', 'ThS.', NULL, 'Dị ứng - Miễn dịch lâm sàng', NULL, 'ThanhVien', 4, NULL, '2026-04-28 22:28:56', '2026-04-28 22:28:56', 1, 1, 0),
(51, 17, 'Nguyễn Thanh Long', 'TS.', NULL, 'Trung tâm đột quỵ', NULL, 'ChuTich', 0, NULL, '2026-04-28 22:28:56', '2026-04-28 22:28:56', 1, 1, 0),
(52, 17, 'Phùng Đức Lâm', 'ThS.', NULL, 'Trung tâm đột quỵ', NULL, 'ThuKy', 1, NULL, '2026-04-28 22:28:56', '2026-04-28 22:28:56', 1, 1, 0),
(53, 17, 'Lê Xuân Vựng', 'ThS.', NULL, 'Nội Dị ứng - Hô hấp', NULL, 'PhanBien1', 2, NULL, '2026-04-28 22:28:56', '2026-04-28 22:28:56', 1, 1, 0),
(54, 17, 'Nguyễn Thị Hoài Trang', 'BSCK2.', NULL, 'Khám bệnh', NULL, 'PhanBien2', 3, NULL, '2026-04-28 22:28:56', '2026-04-28 22:28:56', 1, 1, 0),
(55, 17, 'Hoàng Danh Tân', 'ThS.', NULL, 'Dị ứng - Miễn dịch lâm sàng', NULL, 'ThanhVien', 4, NULL, '2026-04-28 22:28:56', '2026-04-28 22:28:56', 1, 1, 0),
(56, 18, 'Nguyễn Huy Toàn', 'TS.', NULL, 'Ngoại tổng hợp 2', NULL, 'ChuTich', 0, NULL, '2026-04-28 22:28:56', '2026-04-28 22:28:56', 1, 1, 0),
(57, 18, 'Trần Hồng Quân', 'ThS.', NULL, 'Ngoại tổng hợp 2', NULL, 'ThuKy', 1, NULL, '2026-04-28 22:28:56', '2026-04-28 22:28:56', 1, 1, 0),
(58, 18, 'Võ Văn Chung', 'ThS.', NULL, 'Ngoại Thận - Tiết niệu', NULL, 'PhanBien1', 2, NULL, '2026-04-28 22:28:56', '2026-04-28 22:28:56', 1, 1, 0),
(59, 18, 'Nguyễn Văn Trường', 'ThS.', NULL, 'Ngoại Thận - Tiết niệu', NULL, 'PhanBien2', 3, NULL, '2026-04-28 22:28:56', '2026-04-28 22:28:56', 1, 1, 0),
(60, 18, 'Trần Xuân Công', 'ThS.', NULL, 'Ngoại tổng hợp 2', NULL, 'ThanhVien', 4, NULL, '2026-04-28 22:28:56', '2026-04-28 22:28:56', 1, 1, 0),
(61, 19, 'Nguyễn Huy Toàn', 'TS.', NULL, 'Ngoại tổng hợp 2', NULL, 'ChuTich', 0, NULL, '2026-04-28 22:28:56', '2026-04-28 22:28:56', 1, 1, 0),
(62, 19, 'Trần Hồng Quân', 'ThS.', NULL, 'Ngoại tổng hợp 2', NULL, 'ThuKy', 1, NULL, '2026-04-28 22:28:56', '2026-04-28 22:28:56', 1, 1, 0),
(63, 19, 'Võ Văn Chung', 'ThS.', NULL, 'Ngoại Thận - Tiết niệu', NULL, 'PhanBien1', 2, NULL, '2026-04-28 22:28:56', '2026-04-28 22:28:56', 1, 1, 0),
(64, 19, 'Nguyễn Văn Trường', 'ThS.', NULL, 'Ngoại Thận - Tiết niệu', NULL, 'PhanBien2', 3, NULL, '2026-04-28 22:28:56', '2026-04-28 22:28:56', 1, 1, 0),
(65, 19, 'Trần Xuân Công', 'ThS.', NULL, 'Ngoại tổng hợp 2', NULL, 'ThanhVien', 4, NULL, '2026-04-28 22:28:56', '2026-04-28 22:28:56', 1, 1, 0),
(66, 20, 'Nguyễn Huy Toàn', 'TS.', NULL, 'Ngoại tổng hợp 2', NULL, 'ChuTich', 0, NULL, '2026-04-28 22:28:56', '2026-04-28 22:28:56', 1, 1, 0),
(67, 20, 'Trần Hồng Quân', 'ThS.', NULL, 'Ngoại tổng hợp 2', NULL, 'ThuKy', 1, NULL, '2026-04-28 22:28:56', '2026-04-28 22:28:56', 1, 1, 0),
(68, 20, 'Võ Văn Chung', 'ThS.', NULL, 'Ngoại Thận - Tiết niệu', NULL, 'PhanBien1', 2, NULL, '2026-04-28 22:28:56', '2026-04-28 22:28:56', 1, 1, 0),
(69, 20, 'Nguyễn Văn Trường', 'ThS.', NULL, 'Ngoại Thận - Tiết niệu', NULL, 'PhanBien2', 3, NULL, '2026-04-28 22:28:56', '2026-04-28 22:28:56', 1, 1, 0),
(70, 20, 'Trần Xuân Công', 'ThS.', NULL, 'Ngoại tổng hợp 2', NULL, 'ThanhVien', 4, NULL, '2026-04-28 22:28:56', '2026-04-28 22:28:56', 1, 1, 0),
(71, 21, 'Nguyễn Huy Toàn', 'TS.', NULL, 'Ngoại tổng hợp 2', NULL, 'ChuTich', 0, NULL, '2026-04-28 22:28:56', '2026-04-28 22:28:56', 1, 1, 0),
(72, 21, 'Trần Hồng Quân', 'ThS.', NULL, 'Ngoại tổng hợp 2', NULL, 'ThuKy', 1, NULL, '2026-04-28 22:28:56', '2026-04-28 22:28:56', 1, 1, 0),
(73, 21, 'Võ Văn Chung', 'ThS.', NULL, 'Ngoại Thận - Tiết niệu', NULL, 'PhanBien1', 2, NULL, '2026-04-28 22:28:56', '2026-04-28 22:28:56', 1, 1, 0),
(74, 21, 'Nguyễn Văn Trường', 'ThS.', NULL, 'Ngoại Thận - Tiết niệu', NULL, 'PhanBien2', 3, NULL, '2026-04-28 22:28:56', '2026-04-28 22:28:56', 1, 1, 0),
(75, 21, 'Trần Xuân Công', 'ThS.', NULL, 'Ngoại tổng hợp 2', NULL, 'ThanhVien', 4, NULL, '2026-04-28 22:28:56', '2026-04-28 22:28:56', 1, 1, 0),
(76, 22, 'Nguyễn Huy Toàn', 'TS.', NULL, 'Ngoại tổng hợp 2', NULL, 'ChuTich', 0, NULL, '2026-04-28 22:28:56', '2026-04-28 22:28:56', 1, 1, 0),
(77, 22, 'Trần Hồng Quân', 'ThS.', NULL, 'Ngoại tổng hợp 2', NULL, 'ThuKy', 1, NULL, '2026-04-28 22:28:56', '2026-04-28 22:28:56', 1, 1, 0),
(78, 22, 'Võ Văn Chung', 'ThS.', NULL, 'Ngoại Thận - Tiết niệu', NULL, 'PhanBien1', 2, NULL, '2026-04-28 22:28:56', '2026-04-28 22:28:56', 1, 1, 0),
(79, 22, 'Nguyễn Văn Trường', 'ThS.', NULL, 'Ngoại Thận - Tiết niệu', NULL, 'PhanBien2', 3, NULL, '2026-04-28 22:28:56', '2026-04-28 22:28:56', 1, 1, 0),
(80, 22, 'Trần Xuân Công', 'ThS.', NULL, 'Ngoại tổng hợp 2', NULL, 'ThanhVien', 4, NULL, '2026-04-28 22:28:56', '2026-04-29 22:14:55', 1, 1, 1),
(81, 23, 'Ngô Phúc Hưng', NULL, NULL, NULL, NULL, 'PhanBien1', 0, NULL, '2026-04-29 20:23:23', '2026-04-29 20:23:23', 1, 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `nckh_nhac_viec`
--

CREATE TABLE `nckh_nhac_viec` (
  `id` int(11) NOT NULL,
  `de_tai_id` int(11) NOT NULL,
  `loai_nhac` enum('TienDo','DeadLine','NghiemThu','Khac') NOT NULL DEFAULT 'TienDo',
  `tieu_de` varchar(255) NOT NULL,
  `noi_dung` text DEFAULT NULL,
  `ngay_nhac` datetime NOT NULL,
  `nguoi_nhan_id` int(11) DEFAULT NULL,
  `da_gui` tinyint(4) NOT NULL DEFAULT 0,
  `ngay_gui` datetime DEFAULT NULL,
  `ket_qua_gui` varchar(500) DEFAULT NULL,
  `ngay_tao` datetime DEFAULT current_timestamp(),
  `ngay_cap_nhat` datetime DEFAULT current_timestamp(),
  `nguoi_tao` int(11) DEFAULT NULL,
  `nguoi_cap_nhat` int(11) DEFAULT NULL,
  `da_xoa` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `nckh_nhac_viec`
--

INSERT INTO `nckh_nhac_viec` (`id`, `de_tai_id`, `loai_nhac`, `tieu_de`, `noi_dung`, `ngay_nhac`, `nguoi_nhan_id`, `da_gui`, `ngay_gui`, `ket_qua_gui`, `ngay_tao`, `ngay_cap_nhat`, `nguoi_tao`, `nguoi_cap_nhat`, `da_xoa`) VALUES
(1, 1, 'TienDo', 'Đã đến kỳ báo cáo tiến độ', 'Vui lòng cập nhật báo cáo tiến độ quý mới nhất cho đề tài.', '2026-04-27 17:00:50', 1, 0, NULL, NULL, '2026-04-27 23:00:50', '2026-04-27 23:00:50', 1, 1, 0),
(2, 1, 'DeadLine', 'Sắp hết hạn nghiệm thu', 'Đề tài cần được nghiệm thu trong tháng tới.', '2026-05-07 18:00:50', 1, 0, NULL, NULL, '2026-04-27 23:00:50', '2026-04-27 23:00:50', 1, 1, 0),
(3, 2, 'TienDo', 'Đã đến kỳ báo cáo tiến độ', 'Vui lòng cập nhật báo cáo tiến độ quý mới nhất cho đề tài.', '2026-04-27 17:00:50', 3, 0, NULL, NULL, '2026-04-27 23:00:50', '2026-04-27 23:00:50', 1, 1, 0),
(4, 2, 'DeadLine', 'Sắp hết hạn nghiệm thu', 'Đề tài cần được nghiệm thu trong tháng tới.', '2026-05-07 18:00:50', 3, 0, NULL, NULL, '2026-04-27 23:00:50', '2026-04-27 23:00:50', 1, 1, 0),
(5, 3, 'Khac', 'Hoàn thiện đề cương', 'Cần bổ sung đề cương chi tiết để gửi Hội đồng phê duyệt.', '2026-04-30 18:00:50', 4, 0, NULL, NULL, '2026-04-27 23:00:50', '2026-04-27 23:00:50', 1, 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `nckh_tai_lieu`
--

CREATE TABLE `nckh_tai_lieu` (
  `id` int(11) NOT NULL,
  `de_tai_id` int(11) NOT NULL,
  `loai_tai_lieu` enum('DeCuong','QuyetDinh','BienBan','BaoCao','FileGoc','Khac') NOT NULL DEFAULT 'Khac',
  `ten_tai_lieu` varchar(255) NOT NULL,
  `ten_file_goc` varchar(255) DEFAULT NULL,
  `ten_file_luu` varchar(255) DEFAULT NULL,
  `kich_thuoc` int(11) DEFAULT NULL,
  `mime_type` varchar(100) DEFAULT NULL,
  `mo_ta` varchar(500) DEFAULT NULL,
  `ngay_tao` datetime DEFAULT current_timestamp(),
  `ngay_cap_nhat` datetime DEFAULT current_timestamp(),
  `nguoi_tao` int(11) DEFAULT NULL,
  `nguoi_cap_nhat` int(11) DEFAULT NULL,
  `da_xoa` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `nckh_tai_lieu`
--

INSERT INTO `nckh_tai_lieu` (`id`, `de_tai_id`, `loai_tai_lieu`, `ten_tai_lieu`, `ten_file_goc`, `ten_file_luu`, `kich_thuoc`, `mime_type`, `mo_ta`, `ngay_tao`, `ngay_cap_nhat`, `nguoi_tao`, `nguoi_cap_nhat`, `da_xoa`) VALUES
(1, 1, 'DeCuong', 'Đề cương chi tiết', 'de_cuong.pdf', 'placeholder_1.pdf', 123456, 'application/pdf', 'Đề cương được Hội đồng phê duyệt.', '2026-04-27 23:00:50', '2026-04-27 23:00:50', 1, 1, 0),
(2, 2, 'DeCuong', 'Đề cương chi tiết', 'de_cuong.pdf', 'placeholder_2.pdf', 123456, 'application/pdf', 'Đề cương được Hội đồng phê duyệt.', '2026-04-27 23:00:50', '2026-04-27 23:00:50', 1, 1, 0),
(3, 3, 'DeCuong', 'Đề cương chi tiết', 'de_cuong.pdf', 'placeholder_3.pdf', 123456, 'application/pdf', 'Đề cương được Hội đồng phê duyệt.', '2026-04-27 23:00:50', '2026-04-27 23:00:50', 1, 1, 0),
(4, 4, 'DeCuong', 'Đề cương chi tiết', 'de_cuong.pdf', 'placeholder_4.pdf', 123456, 'application/pdf', 'Đề cương được Hội đồng phê duyệt.', '2026-04-27 23:00:50', '2026-04-27 23:00:50', 1, 1, 0),
(5, 4, 'BienBan', 'Biên bản nghiệm thu', 'bien_ban_nt.pdf', 'placeholder_bb_4.pdf', 234567, 'application/pdf', 'Hội đồng nghiệm thu cấp cơ sở.', '2026-04-27 23:00:50', '2026-04-27 23:00:50', 1, 1, 0),
(6, 5, 'DeCuong', 'Đề cương chi tiết', 'de_cuong.pdf', 'placeholder_5.pdf', 123456, 'application/pdf', 'Đề cương được Hội đồng phê duyệt.', '2026-04-27 23:00:50', '2026-04-27 23:00:50', 1, 1, 0),
(7, 5, 'BienBan', 'Biên bản nghiệm thu', 'bien_ban_nt.pdf', 'placeholder_bb_5.pdf', 234567, 'application/pdf', 'Hội đồng nghiệm thu cấp cơ sở.', '2026-04-27 23:00:50', '2026-04-27 23:00:50', 1, 1, 0),
(8, 6, 'DeCuong', 'Đề cương chi tiết', 'de_cuong.pdf', 'placeholder_6.pdf', 123456, 'application/pdf', 'Đề cương được Hội đồng phê duyệt.', '2026-04-27 23:00:50', '2026-04-27 23:00:50', 1, 1, 0),
(9, 4, 'Khac', 'thumb_03_Xuất hiện hình thức lừa đảo mới với tỷ l.png', 'thumb_03_Xuất hiện hình thức lừa đảo mới với tỷ l.png', 'nckh_4_20260427230216_98b2c296.png', 731411, 'image/png', NULL, '2026-04-27 23:02:17', '2026-04-27 23:02:17', 1, 1, 0),
(10, 23, 'DeCuong', 'thumb_04_Bác sĩ 99 tuổi nhưng cơ thể như người 40.png', 'thumb_04_Bác sĩ 99 tuổi nhưng cơ thể như người 40.png', 'nckh_23_20260429202337_c9e819cc.png', 1592630, 'image/png', NULL, '2026-04-29 20:23:37', '2026-04-29 20:23:37', 1, 1, 0),
(11, 23, 'Khac', 'thumb_03_Xuất hiện hình thức lừa đảo mới với tỷ l.png', 'thumb_03_Xuất hiện hình thức lừa đảo mới với tỷ l.png', 'nckh_23_20260429202342_6cca3278.png', 731411, 'image/png', NULL, '2026-04-29 20:23:42', '2026-04-29 20:23:42', 1, 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `nckh_thanh_vien`
--

CREATE TABLE `nckh_thanh_vien` (
  `id` int(11) NOT NULL,
  `de_tai_id` int(11) NOT NULL,
  `nhan_vien_id` int(11) DEFAULT NULL,
  `ho_ten_ngoai` varchar(150) DEFAULT NULL,
  `don_vi_ngoai` varchar(255) DEFAULT NULL,
  `vai_tro` varchar(100) NOT NULL DEFAULT 'Thành viên',
  `phan_tram_dong_gop` decimal(5,2) DEFAULT NULL,
  `ghi_chu` varchar(500) DEFAULT NULL,
  `ngay_tao` datetime DEFAULT current_timestamp(),
  `ngay_cap_nhat` datetime DEFAULT current_timestamp(),
  `nguoi_tao` int(11) DEFAULT NULL,
  `nguoi_cap_nhat` int(11) DEFAULT NULL,
  `da_xoa` int(11) NOT NULL DEFAULT 0,
  `ma_nv_text` varchar(50) DEFAULT NULL COMMENT 'Mã NV gốc (khi NV chưa import)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `nckh_thanh_vien`
--

INSERT INTO `nckh_thanh_vien` (`id`, `de_tai_id`, `nhan_vien_id`, `ho_ten_ngoai`, `don_vi_ngoai`, `vai_tro`, `phan_tram_dong_gop`, `ghi_chu`, `ngay_tao`, `ngay_cap_nhat`, `nguoi_tao`, `nguoi_cap_nhat`, `da_xoa`, `ma_nv_text`) VALUES
(1, 1, 2, NULL, NULL, 'Thành viên', 30.00, NULL, '2026-04-27 23:00:50', '2026-04-27 23:00:50', 1, 1, 0, NULL),
(2, 1, 3, NULL, NULL, 'Thành viên', 25.00, NULL, '2026-04-27 23:00:50', '2026-04-27 23:00:50', 1, 1, 0, NULL),
(3, 2, 1, NULL, NULL, 'Thành viên', 30.00, NULL, '2026-04-27 23:00:50', '2026-04-27 23:00:50', 1, 1, 0, NULL),
(4, 2, 2, NULL, NULL, 'Thành viên', 25.00, NULL, '2026-04-27 23:00:50', '2026-04-27 23:00:50', 1, 1, 0, NULL),
(5, 3, 1, NULL, NULL, 'Thành viên', 30.00, NULL, '2026-04-27 23:00:50', '2026-04-27 23:00:50', 1, 1, 0, NULL),
(6, 3, 2, NULL, NULL, 'Thành viên', 25.00, NULL, '2026-04-27 23:00:50', '2026-04-27 23:00:50', 1, 1, 0, NULL),
(7, 3, NULL, 'GS.TS. Nguyễn Cộng tác', 'Trường ĐH Y Hà Nội', 'Cố vấn', 10.00, 'Người ngoài bệnh viện', '2026-04-27 23:00:50', '2026-04-27 23:00:50', 1, 1, 0, NULL),
(8, 4, 1, NULL, NULL, 'Thành viên', 30.00, NULL, '2026-04-27 23:00:50', '2026-04-27 23:00:50', 1, 1, 0, NULL),
(9, 4, 3, NULL, NULL, 'Thành viên', 25.00, NULL, '2026-04-27 23:00:50', '2026-04-27 23:00:50', 1, 1, 0, NULL),
(10, 5, 1, NULL, NULL, 'Thành viên', 30.00, NULL, '2026-04-27 23:00:50', '2026-04-27 23:00:50', 1, 1, 0, NULL),
(11, 5, 2, NULL, NULL, 'Thành viên', 25.00, NULL, '2026-04-27 23:00:50', '2026-04-27 23:00:50', 1, 1, 0, NULL),
(12, 5, NULL, 'GS.TS. Nguyễn Cộng tác', 'Trường ĐH Y Hà Nội', 'Cố vấn', 10.00, 'Người ngoài bệnh viện', '2026-04-27 23:00:50', '2026-04-27 23:00:50', 1, 1, 0, NULL),
(13, 6, 1, NULL, NULL, 'Thành viên', 30.00, NULL, '2026-04-27 23:00:50', '2026-04-27 23:00:50', 1, 1, 0, NULL),
(14, 6, 2, NULL, NULL, 'Thành viên', 25.00, NULL, '2026-04-27 23:00:50', '2026-04-27 23:00:50', 1, 1, 0, NULL),
(15, 6, NULL, 'GS.TS. Nguyễn Cộng tác', 'Trường ĐH Y Hà Nội', 'Cố vấn', 10.00, 'Người ngoài bệnh viện', '2026-04-27 23:00:50', '2026-04-27 23:00:50', 1, 1, 0, NULL),
(16, 4, 19, NULL, NULL, 'Thành viên', NULL, NULL, '2026-04-27 23:02:42', '2026-04-27 23:02:42', 1, 1, 0, NULL),
(17, 7, NULL, 'Lê Mai Anh', NULL, 'Chủ nhiệm', NULL, NULL, '2026-04-28 22:28:56', '2026-04-28 22:28:56', 1, 1, 0, '1048'),
(18, 7, NULL, 'Ngô Thúy Vân', NULL, 'Thư ký', NULL, NULL, '2026-04-28 22:28:56', '2026-04-28 22:28:56', 1, 1, 0, '0879'),
(19, 7, NULL, 'Nguyễn Thị Ngọc Trâm', NULL, 'Thành viên', NULL, NULL, '2026-04-28 22:28:56', '2026-04-28 22:28:56', 1, 1, 0, '1138'),
(20, 8, NULL, 'Nguyễn Thị Minh', NULL, 'Chủ nhiệm', NULL, NULL, '2026-04-28 22:28:56', '2026-04-28 22:28:56', 1, 1, 0, '1882'),
(21, 8, NULL, 'Nguyễn Thị Thu Hằng', NULL, 'Thư ký', NULL, NULL, '2026-04-28 22:28:56', '2026-04-28 22:28:56', 1, 1, 0, '1150'),
(22, 8, NULL, 'Lê Mai Anh', NULL, 'Thành viên', NULL, NULL, '2026-04-28 22:28:56', '2026-04-28 22:28:56', 1, 1, 0, '1048'),
(23, 9, NULL, 'Võ Thị Hòa', NULL, 'Chủ nhiệm', NULL, NULL, '2026-04-28 22:28:56', '2026-04-28 22:28:56', 1, 1, 0, '1969'),
(24, 9, NULL, 'Nguyễn Tuấn Anh', NULL, 'Thư ký', NULL, NULL, '2026-04-28 22:28:56', '2026-04-28 22:28:56', 1, 1, 0, '968'),
(25, 9, NULL, 'Nguyễn Hữu Long', NULL, 'Thành viên', NULL, NULL, '2026-04-28 22:28:56', '2026-04-28 22:28:56', 1, 1, 0, '982'),
(26, 10, NULL, 'Nguyễn Thị Hoa', NULL, 'Chủ nhiệm', NULL, NULL, '2026-04-28 22:28:56', '2026-04-28 22:28:56', 1, 1, 0, '974'),
(27, 10, NULL, 'Thái Thị Tú Uyên', NULL, 'Thư ký', NULL, NULL, '2026-04-28 22:28:56', '2026-04-28 22:28:56', 1, 1, 0, '976'),
(28, 10, NULL, 'Cù Nam Thắng', NULL, 'Thành viên', NULL, NULL, '2026-04-28 22:28:56', '2026-04-28 22:28:56', 1, 1, 0, '1318'),
(29, 11, NULL, 'Trần Thị Hiền', NULL, 'Chủ nhiệm', NULL, NULL, '2026-04-28 22:28:56', '2026-04-28 22:28:56', 1, 1, 0, '1901'),
(30, 11, NULL, 'Bùi Trọng Sáng', NULL, 'Thư ký', NULL, NULL, '2026-04-28 22:28:56', '2026-04-28 22:28:56', 1, 1, 0, '2152'),
(31, 11, NULL, 'Nguyễn Thị Kiều Oanh', NULL, 'Thành viên', NULL, NULL, '2026-04-28 22:28:56', '2026-04-28 22:28:56', 1, 1, 0, '0236'),
(32, 12, NULL, 'Trần Thị Hải', NULL, 'Chủ nhiệm', NULL, NULL, '2026-04-28 22:28:56', '2026-04-28 22:28:56', 1, 1, 0, '2156'),
(33, 12, NULL, 'Trần Thị Vân Quỳnh', NULL, 'Thư ký', NULL, NULL, '2026-04-28 22:28:56', '2026-04-28 22:28:56', 1, 1, 0, '2059'),
(34, 12, NULL, 'Nguyễn Thị Thanh', NULL, 'Thành viên', NULL, NULL, '2026-04-28 22:28:56', '2026-04-28 22:28:56', 1, 1, 0, '1903'),
(35, 13, NULL, 'Chu Thị Kim Anh', NULL, 'Chủ nhiệm', NULL, NULL, '2026-04-28 22:28:56', '2026-04-28 22:28:56', 1, 1, 0, '0604'),
(36, 13, NULL, 'Lê Hoài Nam', NULL, 'Thư ký', NULL, NULL, '2026-04-28 22:28:56', '2026-04-28 22:28:56', 1, 1, 0, '0944'),
(37, 13, NULL, 'Phan Thanh Hưng', NULL, 'Thành viên', NULL, NULL, '2026-04-28 22:28:56', '2026-04-28 22:28:56', 1, 1, 0, '1033'),
(38, 14, NULL, 'Phan Thanh Hưng', NULL, 'Chủ nhiệm', NULL, NULL, '2026-04-28 22:28:56', '2026-04-28 22:28:56', 1, 1, 0, '1033'),
(39, 14, NULL, 'Nguyễn Thị Ngọc', NULL, 'Thư ký', NULL, NULL, '2026-04-28 22:28:56', '2026-04-28 22:28:56', 1, 1, 0, '1428'),
(40, 14, NULL, 'Lê Hoài Nam', NULL, 'Thành viên', NULL, NULL, '2026-04-28 22:28:56', '2026-04-28 22:28:56', 1, 1, 0, '0944'),
(41, 15, NULL, 'Lê Quang Toàn', NULL, 'Chủ nhiệm', NULL, NULL, '2026-04-28 22:28:56', '2026-04-28 22:28:56', 1, 1, 0, '0796'),
(42, 15, NULL, 'Đinh Văn Tiệp', NULL, 'Thư ký', NULL, NULL, '2026-04-28 22:28:56', '2026-04-28 22:28:56', 1, 1, 0, '1367'),
(43, 15, NULL, 'Nguyễn Nhật Thành', NULL, 'Thành viên', NULL, NULL, '2026-04-28 22:28:56', '2026-04-28 22:28:56', 1, 1, 0, '0817'),
(44, 16, NULL, 'Nguyễn Thị Mỹ Linh', NULL, 'Chủ nhiệm', NULL, NULL, '2026-04-28 22:28:56', '2026-04-28 22:28:56', 1, 1, 0, '797'),
(45, 16, NULL, 'Lê Quang Toàn', NULL, 'Thư ký', NULL, NULL, '2026-04-28 22:28:56', '2026-04-28 22:28:56', 1, 1, 0, '796'),
(46, 16, NULL, 'Đào Thanh Lưu', NULL, 'Thành viên', NULL, NULL, '2026-04-28 22:28:56', '2026-04-28 22:28:56', 1, 1, 0, '1598'),
(47, 17, NULL, 'Nguyễn Thị Tuyết Mai', NULL, 'Chủ nhiệm', NULL, NULL, '2026-04-28 22:28:56', '2026-04-28 22:28:56', 1, 1, 0, '1572'),
(48, 17, NULL, 'Nguyễn Võ Dũng', NULL, 'Thư ký', NULL, NULL, '2026-04-28 22:28:56', '2026-04-28 22:28:56', 1, 1, 0, '0224'),
(49, 17, NULL, 'Nguyễn Thị Quỳnh Nhung', NULL, 'Thành viên', NULL, NULL, '2026-04-28 22:28:56', '2026-04-28 22:28:56', 1, 1, 0, '2060'),
(50, 18, NULL, 'Đinh Văn Chiến', NULL, 'Chủ nhiệm', NULL, NULL, '2026-04-28 22:28:56', '2026-04-28 22:28:56', 1, 1, 0, '989'),
(51, 18, NULL, 'Nguyễn Văn Hương', NULL, 'Thư ký', NULL, NULL, '2026-04-28 22:28:56', '2026-04-28 22:28:56', 1, 1, 0, '3'),
(52, 18, NULL, 'Nguyễn Đình Hiếu', NULL, 'Thành viên', NULL, NULL, '2026-04-28 22:28:56', '2026-04-28 22:28:56', 1, 1, 0, '1831'),
(53, 19, NULL, 'Lê Huy Ngọc', NULL, 'Chủ nhiệm', NULL, NULL, '2026-04-28 22:28:56', '2026-04-28 22:28:56', 1, 1, 0, '451'),
(54, 19, NULL, 'Phạm Văn Quân', NULL, 'Thư ký', NULL, NULL, '2026-04-28 22:28:56', '2026-04-28 22:28:56', 1, 1, 0, '1410'),
(55, 19, NULL, 'Nguyễn Cảnh Phong', NULL, 'Thành viên', NULL, NULL, '2026-04-28 22:28:56', '2026-04-28 22:28:56', 1, 1, 0, '937'),
(56, 20, NULL, 'Hồ Văn Hoàng', NULL, 'Chủ nhiệm', NULL, NULL, '2026-04-28 22:28:56', '2026-04-28 22:28:56', 1, 1, 0, '0904'),
(57, 20, NULL, 'Nguyễn Văn Huy', NULL, 'Thư ký', NULL, NULL, '2026-04-28 22:28:56', '2026-04-28 22:28:56', 1, 1, 0, '1366'),
(58, 20, NULL, 'Chu Xuân Hoàng', NULL, 'Thành viên', NULL, NULL, '2026-04-28 22:28:56', '2026-04-28 22:28:56', 1, 1, 0, '0934'),
(59, 21, NULL, 'Nguyễn Quốc Huy', NULL, 'Chủ nhiệm', NULL, NULL, '2026-04-28 22:28:56', '2026-04-28 22:28:56', 1, 1, 0, '1656'),
(60, 21, NULL, 'Trần Anh Sơn', NULL, 'Thư ký', NULL, NULL, '2026-04-28 22:28:56', '2026-04-28 22:28:56', 1, 1, 0, 'Y khoa Vinh'),
(61, 21, NULL, 'Nguyễn Thị Phương Nhi', NULL, 'Thành viên', NULL, NULL, '2026-04-28 22:28:56', '2026-04-28 22:28:56', 1, 1, 0, '2057'),
(62, 22, NULL, 'Nguyễn Thị Bích Ngọc', NULL, 'Chủ nhiệm', NULL, NULL, '2026-04-28 22:28:56', '2026-04-28 22:28:56', 1, 1, 0, '851'),
(63, 22, NULL, 'Cao Thị Hằng', NULL, 'Thư ký', NULL, NULL, '2026-04-28 22:28:56', '2026-04-28 22:28:56', 1, 1, 0, '741'),
(64, 22, NULL, 'Đặng Thị Hằng', NULL, 'Thành viên', NULL, NULL, '2026-04-28 22:28:56', '2026-04-28 22:28:56', 1, 1, 0, '1237'),
(65, 23, 21, NULL, NULL, 'Thành viên', NULL, NULL, '2026-04-29 20:23:00', '2026-04-29 20:23:00', 1, 1, 0, NULL),
(66, 23, 13, NULL, NULL, 'Thành viên', NULL, NULL, '2026-04-29 20:23:03', '2026-04-29 20:23:03', 1, 1, 0, NULL),
(67, 24, 18, NULL, NULL, 'Thành viên', NULL, NULL, '2026-04-29 20:28:43', '2026-04-29 20:28:43', 8, 8, 0, NULL),
(68, 24, 9, NULL, NULL, 'Thành viên', NULL, NULL, '2026-04-29 20:28:49', '2026-04-29 20:28:49', 8, 8, 0, NULL),
(69, 25, 18, NULL, NULL, 'Thành viên', NULL, NULL, '2026-05-04 22:15:57', '2026-05-04 22:15:57', 8, 8, 0, NULL),
(70, 25, 14, NULL, NULL, 'Thành viên', NULL, NULL, '2026-05-04 22:16:04', '2026-05-04 22:16:04', 8, 8, 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `nckh_tien_do`
--

CREATE TABLE `nckh_tien_do` (
  `id` int(11) NOT NULL,
  `de_tai_id` int(11) NOT NULL,
  `ky_bao_cao` varchar(50) NOT NULL,
  `ngay_bao_cao` date NOT NULL,
  `phan_tram_hoan_thanh` tinyint(4) NOT NULL DEFAULT 0,
  `cong_viec_da_lam` text DEFAULT NULL,
  `cong_viec_tiep_theo` text DEFAULT NULL,
  `kho_khan_vuong_mac` text DEFAULT NULL,
  `nguoi_bao_cao_id` int(11) DEFAULT NULL,
  `ngay_tao` datetime DEFAULT current_timestamp(),
  `ngay_cap_nhat` datetime DEFAULT current_timestamp(),
  `nguoi_tao` int(11) DEFAULT NULL,
  `nguoi_cap_nhat` int(11) DEFAULT NULL,
  `da_xoa` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `nckh_tien_do`
--

INSERT INTO `nckh_tien_do` (`id`, `de_tai_id`, `ky_bao_cao`, `ngay_bao_cao`, `phan_tram_hoan_thanh`, `cong_viec_da_lam`, `cong_viec_tiep_theo`, `kho_khan_vuong_mac`, `nguoi_bao_cao_id`, `ngay_tao`, `ngay_cap_nhat`, `nguoi_tao`, `nguoi_cap_nhat`, `da_xoa`) VALUES
(1, 1, 'Q1/2026', '2026-01-27', 30, 'Hoàn thành đề cương, IRB phê duyệt, tuyển 30 BN.', 'Tiếp tục tuyển BN, theo dõi tháng 1-3.', NULL, 1, '2026-04-27 23:00:50', '2026-04-27 23:00:50', 1, 1, 0),
(2, 1, 'Q2/2026', '2026-03-28', 60, 'Đã tuyển 70 BN, hoàn thành nửa nhập liệu.', 'Tuyển nốt 50 BN, bắt đầu phân tích.', 'Một số BN bỏ theo dõi - cần liên hệ lại.', 1, '2026-04-27 23:00:50', '2026-04-27 23:00:50', 1, 1, 0),
(3, 2, 'Q1/2026', '2026-01-27', 30, 'Hoàn thành đề cương, IRB phê duyệt, tuyển 30 BN.', 'Tiếp tục tuyển BN, theo dõi tháng 1-3.', NULL, 3, '2026-04-27 23:00:50', '2026-04-27 23:00:50', 1, 1, 0),
(4, 2, 'Q2/2026', '2026-03-28', 60, 'Đã tuyển 70 BN, hoàn thành nửa nhập liệu.', 'Tuyển nốt 50 BN, bắt đầu phân tích.', 'Một số BN bỏ theo dõi - cần liên hệ lại.', 3, '2026-04-27 23:00:50', '2026-04-27 23:00:50', 1, 1, 0),
(5, 4, 'Q1', '2025-10-09', 35, 'Hoàn thành thu thập số liệu.', 'Phân tích.', NULL, 2, '2026-04-27 23:00:50', '2026-04-27 23:00:50', 1, 1, 0),
(6, 4, 'Q2', '2026-01-27', 75, 'Phân tích thống kê xong.', 'Viết báo cáo.', NULL, 2, '2026-04-27 23:00:50', '2026-04-27 23:00:50', 1, 1, 0),
(7, 4, 'Tổng kết', '2026-04-12', 100, 'Nghiệm thu, công bố nội bộ.', NULL, NULL, 2, '2026-04-27 23:00:50', '2026-04-27 23:00:50', 1, 1, 0),
(8, 5, 'Q1', '2025-10-09', 35, 'Hoàn thành thu thập số liệu.', 'Phân tích.', NULL, 3, '2026-04-27 23:00:50', '2026-04-27 23:00:50', 1, 1, 0),
(9, 5, 'Q2', '2026-01-27', 75, 'Phân tích thống kê xong.', 'Viết báo cáo.', NULL, 3, '2026-04-27 23:00:50', '2026-04-27 23:00:50', 1, 1, 0),
(10, 5, 'Tổng kết', '2026-04-12', 100, 'Nghiệm thu, công bố nội bộ.', NULL, NULL, 3, '2026-04-27 23:00:50', '2026-04-27 23:00:50', 1, 1, 0),
(11, 6, 'Q1/2025', '2025-10-29', 20, 'Bắt đầu khảo sát chuẩn HL7-FHIR.', 'Đợi nguồn lực và phối hợp với CNTT.', 'Thiếu nhân sự CNTT chuyên môn FHIR — đề xuất tạm dừng.', 6, '2026-04-27 23:00:50', '2026-04-27 23:00:50', 1, 1, 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `dm_benh_vien`
--
ALTER TABLE `dm_benh_vien`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UQ_DM_BENH_VIEN_ma` (`ma_benh_vien`,`da_xoa`),
  ADD KEY `idx_DM_BENH_VIEN_trang_thai` (`trang_thai`,`da_xoa`);

--
-- Indexes for table `dm_cau_hinh`
--
ALTER TABLE `dm_cau_hinh`
  ADD PRIMARY KEY (`ma_cau_hinh`);

--
-- Indexes for table `dm_danh_sach_form`
--
ALTER TABLE `dm_danh_sach_form`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IX_DanhSachForm_ModulesTuongUng` (`modules_tuong_ung`);

--
-- Indexes for table `dm_doi_tuong_hoc_vien`
--
ALTER TABLE `dm_doi_tuong_hoc_vien`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UQ_DM_DOI_TUONG_HOC_VIEN_ma` (`ma_doi_tuong`);

--
-- Indexes for table `dm_giang_vien`
--
ALTER TABLE `dm_giang_vien`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UQ_GV_ma` (`ma_gv`,`da_xoa`),
  ADD KEY `idx_GV_nv` (`nhan_vien_id`,`da_xoa`),
  ADD KEY `idx_GV_loai` (`loai_gv`,`da_xoa`),
  ADD KEY `idx_GV_tt` (`trang_thai`,`da_xoa`);

--
-- Indexes for table `dm_hinh_thuc_hoc`
--
ALTER TABLE `dm_hinh_thuc_hoc`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UQ_DM_HINH_THUC_HOC_ma` (`ma_hinh_thuc`);

--
-- Indexes for table `dm_hoc_vien`
--
ALTER TABLE `dm_hoc_vien`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UQ_DM_HOC_VIEN_ma` (`ma_hv`,`da_xoa`),
  ADD UNIQUE KEY `UQ_DM_HOC_VIEN_nv` (`nhan_vien_id`,`da_xoa`),
  ADD KEY `idx_DM_HOC_VIEN_trang_thai` (`trang_thai`,`da_xoa`),
  ADD KEY `idx_DM_HOC_VIEN_doi_tuong` (`doi_tuong_id`,`da_xoa`),
  ADD KEY `idx_HV_cccd` (`cccd`,`da_xoa`);

--
-- Indexes for table `dm_khoa_phong`
--
ALTER TABLE `dm_khoa_phong`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_DM_KHOA_PHONG_trang_thai` (`trang_thai`,`da_xoa`);

--
-- Indexes for table `dm_loai_hinh_dao_tao`
--
ALTER TABLE `dm_loai_hinh_dao_tao`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UQ_DM_LOAI_HINH_DAO_TAO_ma` (`ma_loai_hinh`);

--
-- Indexes for table `dm_nckh_cap_do`
--
ALTER TABLE `dm_nckh_cap_do`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UQ_NCKH_CD_ma` (`ma_cap_do`,`da_xoa`);

--
-- Indexes for table `dm_nckh_the_loai`
--
ALTER TABLE `dm_nckh_the_loai`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UQ_NCKH_TL_ma` (`ma_the_loai`,`da_xoa`);

--
-- Indexes for table `dm_nguoi_dung`
--
ALTER TABLE `dm_nguoi_dung`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UQ_DM_NGUOI_DUNG_tai_khoan` (`tai_khoan`,`da_xoa`),
  ADD KEY `idx_DM_NGUOI_DUNG_trang_thai` (`trang_thai`,`da_xoa`);

--
-- Indexes for table `dm_nhan_vien`
--
ALTER TABLE `dm_nhan_vien`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UQ_DM_NHAN_VIEN` (`benh_vien_id`,`ma_nv`,`da_xoa`),
  ADD KEY `idx_DM_NHAN_VIEN_ho_ten` (`ho_ten`),
  ADD KEY `idx_DM_NHAN_VIEN_khoa_phong` (`khoa_phong_id`);

--
-- Indexes for table `dm_nhat_ky_he_thong`
--
ALTER TABLE `dm_nhat_ky_he_thong`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_DM_NHAT_KY_module` (`module`),
  ADD KEY `idx_DM_NHAT_KY_nguoi_dung` (`nguoi_dung_id`),
  ADD KEY `idx_DM_NHAT_KY_thoi_gian` (`thoi_gian`);

--
-- Indexes for table `dm_nhom_tai_khoan`
--
ALTER TABLE `dm_nhom_tai_khoan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `dm_phan_quyen`
--
ALTER TABLE `dm_phan_quyen`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UQ_DM_PHAN_QUYEN` (`nhom_tai_khoan_id`,`danh_sach_form_id`);

--
-- Indexes for table `dt_bai_kiem_tra`
--
ALTER TABLE `dt_bai_kiem_tra`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UQ_BKT_ma` (`ma_bkt`,`da_xoa`),
  ADD KEY `idx_BKT_mon` (`mon_hoc_id`,`da_xoa`),
  ADD KEY `idx_BKT_loai` (`loai_bkt`,`da_xoa`),
  ADD KEY `idx_BAI_KIEM_TRA_khct` (`khoa_hoc_chuong_trinh_id`,`da_xoa`);

--
-- Indexes for table `dt_chung_chi`
--
ALTER TABLE `dt_chung_chi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_CC_hoc_vien` (`hoc_vien_id`),
  ADD KEY `idx_CC_so` (`so_chung_chi`),
  ADD KEY `idx_CC_trang_thai` (`trang_thai`,`da_xoa`),
  ADD KEY `idx_CHUNG_CHI_khct` (`khoa_hoc_chuong_trinh_id`,`da_xoa`);

--
-- Indexes for table `dt_chuong_trinh`
--
ALTER TABLE `dt_chuong_trinh`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UQ_DT_CHUONG_TRINH_ma` (`ma_chuong_trinh`,`da_xoa`),
  ADD KEY `idx_DT_LOP_HOC_tt` (`da_xoa`),
  ADD KEY `idx_CT_khoa_phong` (`khoa_phong_id`),
  ADD KEY `idx_CT_doituong` (`doi_tuong_id`);

--
-- Indexes for table `dt_chuong_trinh_mon_hoc`
--
ALTER TABLE `dt_chuong_trinh_mon_hoc`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UQ_LMH` (`chuong_trinh_id`,`mon_hoc_id`,`da_xoa`),
  ADD KEY `idx_LMH_lop` (`chuong_trinh_id`,`da_xoa`),
  ADD KEY `idx_LMH_mon` (`mon_hoc_id`,`da_xoa`),
  ADD KEY `idx_LMH_thu_tu` (`chuong_trinh_id`,`thu_tu`);

--
-- Indexes for table `dt_dang_ky_khoa_hoc`
--
ALTER TABLE `dt_dang_ky_khoa_hoc`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UQ_DK_ma` (`ma_tra_cuu`),
  ADD KEY `idx_DK_kh` (`khoa_hoc_id`,`da_xoa`),
  ADD KEY `idx_DK_tt` (`trang_thai`,`da_xoa`),
  ADD KEY `idx_DK_cccd` (`cccd`,`da_xoa`),
  ADD KEY `idx_DK_email` (`email`,`da_xoa`),
  ADD KEY `FK_DK_HocVien` (`hoc_vien_id`),
  ADD KEY `idx_DANG_KY_KHOA_HOC_khct` (`khoa_hoc_chuong_trinh_id`,`da_xoa`);

--
-- Indexes for table `dt_diem_danh`
--
ALTER TABLE `dt_diem_danh`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UQ_DD_lichhv` (`lich_hoc_id`,`hoc_vien_lop_id`,`da_xoa`),
  ADD KEY `idx_DD_lich` (`lich_hoc_id`,`da_xoa`),
  ADD KEY `idx_DD_hvl` (`hoc_vien_lop_id`,`da_xoa`),
  ADD KEY `idx_DD_tt` (`trang_thai`,`da_xoa`);

--
-- Indexes for table `dt_dot_dang_ky`
--
ALTER TABLE `dt_dot_dang_ky`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_dtdot_nam` (`nam`,`da_xoa`),
  ADD KEY `idx_dtdot_tt` (`trang_thai`,`da_xoa`),
  ADD KEY `idx_dtdot_tg` (`tu_ngay`,`den_ngay`);

--
-- Indexes for table `dt_dot_giai_doan`
--
ALTER TABLE `dt_dot_giai_doan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_dtgd_dot` (`dot_id`,`da_xoa`),
  ADD KEY `idx_dtgd_hv` (`hanh_vi`,`da_xoa`),
  ADD KEY `idx_dtgd_tg` (`tu_ngay`,`den_ngay`);

--
-- Indexes for table `dt_hoc_vien_lop`
--
ALTER TABLE `dt_hoc_vien_lop`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UQ_DT_HVL` (`khoa_hoc_chuong_trinh_id`,`hoc_vien_id`,`da_xoa`),
  ADD KEY `idx_DT_HVL_hv` (`hoc_vien_id`,`da_xoa`),
  ADD KEY `idx_HOC_VIEN_LOP_khct` (`khoa_hoc_chuong_trinh_id`,`da_xoa`);

--
-- Indexes for table `dt_ho_so_hoc_vien`
--
ALTER TABLE `dt_ho_so_hoc_vien`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_HSOHV_hoc_vien` (`hoc_vien_id`),
  ADD KEY `idx_HSOHV_loai` (`loai_ho_so`),
  ADD KEY `idx_HSOHV_trang_thai` (`trang_thai`,`da_xoa`);

--
-- Indexes for table `dt_ket_qua_hoc_tap`
--
ALTER TABLE `dt_ket_qua_hoc_tap`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UQ_KQ_hvl_mon` (`hoc_vien_lop_id`,`mon_hoc_id`,`da_xoa`),
  ADD KEY `idx_KQ_hvl` (`hoc_vien_lop_id`,`da_xoa`),
  ADD KEY `idx_KQ_mon` (`mon_hoc_id`,`da_xoa`);

--
-- Indexes for table `dt_khoa_hoc`
--
ALTER TABLE `dt_khoa_hoc`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UQ_DT_KHOA_HOC_ma` (`ma_khoa_hoc`),
  ADD KEY `idx_DT_KHOA_HOC_loai_hinh` (`loai_hinh_dao_tao_id`),
  ADD KEY `idx_DT_KHOA_HOC_hinh_thuc` (`hinh_thuc_hoc_id`),
  ADD KEY `idx_DT_KHOA_HOC_doi_tuong` (`doi_tuong_hoc_vien_id`),
  ADD KEY `idx_KH_dot` (`dot_dang_ky_id`,`da_xoa`);

--
-- Indexes for table `dt_khoa_hoc_chuong_trinh`
--
ALTER TABLE `dt_khoa_hoc_chuong_trinh`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UQ_KHCT` (`khoa_hoc_id`,`chuong_trinh_id`,`da_xoa`),
  ADD KEY `idx_KHCT_kh` (`khoa_hoc_id`,`da_xoa`),
  ADD KEY `idx_KHCT_ct` (`chuong_trinh_id`,`da_xoa`),
  ADD KEY `idx_KHCT_gv` (`giao_vien_id`);

--
-- Indexes for table `dt_lich_hoc`
--
ALTER TABLE `dt_lich_hoc`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_lh_ngay` (`ngay_hoc`,`da_xoa`),
  ADD KEY `idx_lh_gv` (`giang_vien_id`,`ngay_hoc`,`da_xoa`),
  ADD KEY `idx_lh_phong` (`phong_hoc`,`ngay_hoc`,`da_xoa`),
  ADD KEY `FK_LichHoc_MonHoc` (`mon_hoc_id`),
  ADD KEY `idx_LICH_HOC_khct` (`khoa_hoc_chuong_trinh_id`,`da_xoa`);

--
-- Indexes for table `dt_mon_hoc`
--
ALTER TABLE `dt_mon_hoc`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UQ_DT_MON_HOC_ma` (`ma_mon_hoc`,`da_xoa`),
  ADD KEY `idx_DT_MON_HOC_trang_thai` (`trang_thai`,`da_xoa`),
  ADD KEY `idx_MH_ct` (`chuong_trinh_id`);

--
-- Indexes for table `dt_phan_cong_giang_vien`
--
ALTER TABLE `dt_phan_cong_giang_vien`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UQ_PC_unique` (`giang_vien_id`,`khoa_hoc_chuong_trinh_id`,`mon_hoc_id`,`vai_tro`,`da_xoa`),
  ADD KEY `idx_PC_gv` (`giang_vien_id`,`da_xoa`),
  ADD KEY `idx_PC_mon` (`mon_hoc_id`,`da_xoa`),
  ADD KEY `idx_PHAN_CONG_GIANG_VIEN_khct` (`khoa_hoc_chuong_trinh_id`,`da_xoa`);

--
-- Indexes for table `dt_tai_lieu`
--
ALTER TABLE `dt_tai_lieu`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UQ_TL_ma` (`ma_tai_lieu`,`da_xoa`),
  ADD KEY `idx_TL_loai` (`loai_tai_lieu`,`da_xoa`),
  ADD KEY `idx_TL_kh` (`khoa_hoc_id`,`da_xoa`),
  ADD KEY `idx_TL_mon` (`mon_hoc_id`,`da_xoa`),
  ADD KEY `idx_TL_tt` (`trang_thai`,`da_xoa`),
  ADD KEY `idx_TAI_LIEU_khct` (`khoa_hoc_chuong_trinh_id`,`da_xoa`);

--
-- Indexes for table `nckh_de_tai`
--
ALTER TABLE `nckh_de_tai`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UQ_DT_ma` (`ma_de_tai`,`da_xoa`),
  ADD KEY `idx_DT_nam` (`nam`,`da_xoa`),
  ADD KEY `idx_DT_capdo` (`cap_do_id`,`da_xoa`),
  ADD KEY `idx_DT_theloai` (`the_loai_id`,`da_xoa`),
  ADD KEY `idx_DT_khoa` (`khoa_phong_id`,`da_xoa`),
  ADD KEY `idx_DT_chunhiem` (`chu_nhiem_id`,`da_xoa`),
  ADD KEY `idx_DT_tt` (`trang_thai`,`da_xoa`),
  ADD KEY `FK_DT_ThuKy` (`thu_ky_id`),
  ADD KEY `idx_DT_duyet` (`trang_thai_duyet`,`da_xoa`),
  ADD KEY `idx_DT_dot` (`dot_dang_ky_id`,`da_xoa`);

--
-- Indexes for table `nckh_dot_dang_ky`
--
ALTER TABLE `nckh_dot_dang_ky`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_dot_nam` (`nam`,`da_xoa`),
  ADD KEY `idx_dot_tt` (`trang_thai`,`da_xoa`),
  ADD KEY `idx_dot_thoi_gian` (`tu_ngay`,`den_ngay`);

--
-- Indexes for table `nckh_dot_giai_doan`
--
ALTER TABLE `nckh_dot_giai_doan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_gd_dot` (`dot_id`,`da_xoa`),
  ADD KEY `idx_gd_hanh_vi` (`hanh_vi`,`da_xoa`),
  ADD KEY `idx_gd_thoi_gian` (`tu_ngay`,`den_ngay`);

--
-- Indexes for table `nckh_hoi_dong`
--
ALTER TABLE `nckh_hoi_dong`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_HD_dt` (`de_tai_id`,`da_xoa`),
  ADD KEY `idx_HD_nv` (`nhan_vien_id`,`da_xoa`),
  ADD KEY `FK_HD_Khoa` (`khoa_phong_id`);

--
-- Indexes for table `nckh_nhac_viec`
--
ALTER TABLE `nckh_nhac_viec`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_NV_dt` (`de_tai_id`,`da_xoa`),
  ADD KEY `idx_NV_ngay` (`ngay_nhac`,`da_gui`,`da_xoa`),
  ADD KEY `FK_NV_Nguoi` (`nguoi_nhan_id`);

--
-- Indexes for table `nckh_tai_lieu`
--
ALTER TABLE `nckh_tai_lieu`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_TL_dt` (`de_tai_id`,`da_xoa`),
  ADD KEY `idx_TL_loai` (`loai_tai_lieu`,`da_xoa`);

--
-- Indexes for table `nckh_thanh_vien`
--
ALTER TABLE `nckh_thanh_vien`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_TV_dt` (`de_tai_id`,`da_xoa`),
  ADD KEY `idx_TV_nv` (`nhan_vien_id`,`da_xoa`);

--
-- Indexes for table `nckh_tien_do`
--
ALTER TABLE `nckh_tien_do`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_TD_dt` (`de_tai_id`,`da_xoa`),
  ADD KEY `idx_TD_ngay` (`ngay_bao_cao`,`da_xoa`),
  ADD KEY `FK_TD_NguoiBC` (`nguoi_bao_cao_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `dm_benh_vien`
--
ALTER TABLE `dm_benh_vien`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `dm_danh_sach_form`
--
ALTER TABLE `dm_danh_sach_form`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `dm_doi_tuong_hoc_vien`
--
ALTER TABLE `dm_doi_tuong_hoc_vien`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `dm_giang_vien`
--
ALTER TABLE `dm_giang_vien`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `dm_hinh_thuc_hoc`
--
ALTER TABLE `dm_hinh_thuc_hoc`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `dm_hoc_vien`
--
ALTER TABLE `dm_hoc_vien`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `dm_khoa_phong`
--
ALTER TABLE `dm_khoa_phong`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=87;

--
-- AUTO_INCREMENT for table `dm_loai_hinh_dao_tao`
--
ALTER TABLE `dm_loai_hinh_dao_tao`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `dm_nckh_cap_do`
--
ALTER TABLE `dm_nckh_cap_do`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `dm_nckh_the_loai`
--
ALTER TABLE `dm_nckh_the_loai`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `dm_nguoi_dung`
--
ALTER TABLE `dm_nguoi_dung`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `dm_nhan_vien`
--
ALTER TABLE `dm_nhan_vien`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1537;

--
-- AUTO_INCREMENT for table `dm_nhat_ky_he_thong`
--
ALTER TABLE `dm_nhat_ky_he_thong`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=503;

--
-- AUTO_INCREMENT for table `dm_nhom_tai_khoan`
--
ALTER TABLE `dm_nhom_tai_khoan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `dm_phan_quyen`
--
ALTER TABLE `dm_phan_quyen`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=628;

--
-- AUTO_INCREMENT for table `dt_bai_kiem_tra`
--
ALTER TABLE `dt_bai_kiem_tra`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dt_chung_chi`
--
ALTER TABLE `dt_chung_chi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `dt_chuong_trinh`
--
ALTER TABLE `dt_chuong_trinh`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=79;

--
-- AUTO_INCREMENT for table `dt_chuong_trinh_mon_hoc`
--
ALTER TABLE `dt_chuong_trinh_mon_hoc`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `dt_dang_ky_khoa_hoc`
--
ALTER TABLE `dt_dang_ky_khoa_hoc`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dt_diem_danh`
--
ALTER TABLE `dt_diem_danh`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `dt_dot_dang_ky`
--
ALTER TABLE `dt_dot_dang_ky`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `dt_dot_giai_doan`
--
ALTER TABLE `dt_dot_giai_doan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `dt_hoc_vien_lop`
--
ALTER TABLE `dt_hoc_vien_lop`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `dt_ho_so_hoc_vien`
--
ALTER TABLE `dt_ho_so_hoc_vien`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `dt_ket_qua_hoc_tap`
--
ALTER TABLE `dt_ket_qua_hoc_tap`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=227;

--
-- AUTO_INCREMENT for table `dt_khoa_hoc`
--
ALTER TABLE `dt_khoa_hoc`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `dt_khoa_hoc_chuong_trinh`
--
ALTER TABLE `dt_khoa_hoc_chuong_trinh`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT for table `dt_lich_hoc`
--
ALTER TABLE `dt_lich_hoc`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `dt_mon_hoc`
--
ALTER TABLE `dt_mon_hoc`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT for table `dt_phan_cong_giang_vien`
--
ALTER TABLE `dt_phan_cong_giang_vien`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dt_tai_lieu`
--
ALTER TABLE `dt_tai_lieu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `nckh_de_tai`
--
ALTER TABLE `nckh_de_tai`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `nckh_dot_dang_ky`
--
ALTER TABLE `nckh_dot_dang_ky`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `nckh_dot_giai_doan`
--
ALTER TABLE `nckh_dot_giai_doan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `nckh_hoi_dong`
--
ALTER TABLE `nckh_hoi_dong`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=82;

--
-- AUTO_INCREMENT for table `nckh_nhac_viec`
--
ALTER TABLE `nckh_nhac_viec`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `nckh_tai_lieu`
--
ALTER TABLE `nckh_tai_lieu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `nckh_thanh_vien`
--
ALTER TABLE `nckh_thanh_vien`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

--
-- AUTO_INCREMENT for table `nckh_tien_do`
--
ALTER TABLE `nckh_tien_do`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `dm_giang_vien`
--
ALTER TABLE `dm_giang_vien`
  ADD CONSTRAINT `FK_GV_NhanVien` FOREIGN KEY (`nhan_vien_id`) REFERENCES `dm_nhan_vien` (`id`);

--
-- Constraints for table `dm_hoc_vien`
--
ALTER TABLE `dm_hoc_vien`
  ADD CONSTRAINT `FK_HOC_VIEN_DoiTuong` FOREIGN KEY (`doi_tuong_id`) REFERENCES `dm_doi_tuong_hoc_vien` (`id`),
  ADD CONSTRAINT `FK_HOC_VIEN_NhanVien` FOREIGN KEY (`nhan_vien_id`) REFERENCES `dm_nhan_vien` (`id`);

--
-- Constraints for table `dt_bai_kiem_tra`
--
ALTER TABLE `dt_bai_kiem_tra`
  ADD CONSTRAINT `FK_BAI_KIEM_TRA_KHCT` FOREIGN KEY (`khoa_hoc_chuong_trinh_id`) REFERENCES `dt_khoa_hoc_chuong_trinh` (`id`),
  ADD CONSTRAINT `FK_BKT_MonHoc` FOREIGN KEY (`mon_hoc_id`) REFERENCES `dt_mon_hoc` (`id`);

--
-- Constraints for table `dt_chung_chi`
--
ALTER TABLE `dt_chung_chi`
  ADD CONSTRAINT `FK_CC_HOC_VIEN` FOREIGN KEY (`hoc_vien_id`) REFERENCES `dm_hoc_vien` (`id`),
  ADD CONSTRAINT `FK_CHUNG_CHI_KHCT` FOREIGN KEY (`khoa_hoc_chuong_trinh_id`) REFERENCES `dt_khoa_hoc_chuong_trinh` (`id`);

--
-- Constraints for table `dt_chuong_trinh`
--
ALTER TABLE `dt_chuong_trinh`
  ADD CONSTRAINT `FK_CT_DoiTuong` FOREIGN KEY (`doi_tuong_id`) REFERENCES `dm_doi_tuong_hoc_vien` (`id`),
  ADD CONSTRAINT `FK_CT_KhoaPhong` FOREIGN KEY (`khoa_phong_id`) REFERENCES `dm_khoa_phong` (`id`);

--
-- Constraints for table `dt_chuong_trinh_mon_hoc`
--
ALTER TABLE `dt_chuong_trinh_mon_hoc`
  ADD CONSTRAINT `FK_CTMH_ChuongTrinh` FOREIGN KEY (`chuong_trinh_id`) REFERENCES `dt_chuong_trinh` (`id`),
  ADD CONSTRAINT `FK_LMH_Mon` FOREIGN KEY (`mon_hoc_id`) REFERENCES `dt_mon_hoc` (`id`);

--
-- Constraints for table `dt_dang_ky_khoa_hoc`
--
ALTER TABLE `dt_dang_ky_khoa_hoc`
  ADD CONSTRAINT `FK_DANG_KY_KHOA_HOC_KHCT` FOREIGN KEY (`khoa_hoc_chuong_trinh_id`) REFERENCES `dt_khoa_hoc_chuong_trinh` (`id`),
  ADD CONSTRAINT `FK_DK_HocVien` FOREIGN KEY (`hoc_vien_id`) REFERENCES `dm_hoc_vien` (`id`),
  ADD CONSTRAINT `FK_DK_KhoaHoc` FOREIGN KEY (`khoa_hoc_id`) REFERENCES `dt_khoa_hoc` (`id`);

--
-- Constraints for table `dt_diem_danh`
--
ALTER TABLE `dt_diem_danh`
  ADD CONSTRAINT `FK_DD_HVL` FOREIGN KEY (`hoc_vien_lop_id`) REFERENCES `dt_hoc_vien_lop` (`id`),
  ADD CONSTRAINT `FK_DD_LichHoc` FOREIGN KEY (`lich_hoc_id`) REFERENCES `dt_lich_hoc` (`id`);

--
-- Constraints for table `dt_dot_giai_doan`
--
ALTER TABLE `dt_dot_giai_doan`
  ADD CONSTRAINT `FK_DTGD_Dot` FOREIGN KEY (`dot_id`) REFERENCES `dt_dot_dang_ky` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `dt_hoc_vien_lop`
--
ALTER TABLE `dt_hoc_vien_lop`
  ADD CONSTRAINT `FK_HOC_VIEN_LOP_KHCT` FOREIGN KEY (`khoa_hoc_chuong_trinh_id`) REFERENCES `dt_khoa_hoc_chuong_trinh` (`id`),
  ADD CONSTRAINT `FK_HVL_HocVien` FOREIGN KEY (`hoc_vien_id`) REFERENCES `dm_hoc_vien` (`id`);

--
-- Constraints for table `dt_ho_so_hoc_vien`
--
ALTER TABLE `dt_ho_so_hoc_vien`
  ADD CONSTRAINT `FK_HSHV_HOC_VIEN` FOREIGN KEY (`hoc_vien_id`) REFERENCES `dm_hoc_vien` (`id`);

--
-- Constraints for table `dt_ket_qua_hoc_tap`
--
ALTER TABLE `dt_ket_qua_hoc_tap`
  ADD CONSTRAINT `FK_KQ_HVL` FOREIGN KEY (`hoc_vien_lop_id`) REFERENCES `dt_hoc_vien_lop` (`id`),
  ADD CONSTRAINT `FK_KQ_MonHoc` FOREIGN KEY (`mon_hoc_id`) REFERENCES `dt_mon_hoc` (`id`);

--
-- Constraints for table `dt_khoa_hoc`
--
ALTER TABLE `dt_khoa_hoc`
  ADD CONSTRAINT `FK_KH_Dot` FOREIGN KEY (`dot_dang_ky_id`) REFERENCES `dt_dot_dang_ky` (`id`);

--
-- Constraints for table `dt_khoa_hoc_chuong_trinh`
--
ALTER TABLE `dt_khoa_hoc_chuong_trinh`
  ADD CONSTRAINT `FK_KHCT_ChuongTrinh` FOREIGN KEY (`chuong_trinh_id`) REFERENCES `dt_chuong_trinh` (`id`),
  ADD CONSTRAINT `FK_KHCT_GiaoVien` FOREIGN KEY (`giao_vien_id`) REFERENCES `dm_nhan_vien` (`id`),
  ADD CONSTRAINT `FK_KHCT_KhoaHoc` FOREIGN KEY (`khoa_hoc_id`) REFERENCES `dt_khoa_hoc` (`id`);

--
-- Constraints for table `dt_lich_hoc`
--
ALTER TABLE `dt_lich_hoc`
  ADD CONSTRAINT `FK_LICH_HOC_KHCT` FOREIGN KEY (`khoa_hoc_chuong_trinh_id`) REFERENCES `dt_khoa_hoc_chuong_trinh` (`id`),
  ADD CONSTRAINT `FK_LichHoc_GiangVien` FOREIGN KEY (`giang_vien_id`) REFERENCES `dm_nhan_vien` (`id`),
  ADD CONSTRAINT `FK_LichHoc_MonHoc` FOREIGN KEY (`mon_hoc_id`) REFERENCES `dt_mon_hoc` (`id`);

--
-- Constraints for table `dt_mon_hoc`
--
ALTER TABLE `dt_mon_hoc`
  ADD CONSTRAINT `FK_MH_ChuongTrinh` FOREIGN KEY (`chuong_trinh_id`) REFERENCES `dt_chuong_trinh` (`id`);

--
-- Constraints for table `dt_phan_cong_giang_vien`
--
ALTER TABLE `dt_phan_cong_giang_vien`
  ADD CONSTRAINT `FK_PC_GV` FOREIGN KEY (`giang_vien_id`) REFERENCES `dm_giang_vien` (`id`),
  ADD CONSTRAINT `FK_PC_Mon` FOREIGN KEY (`mon_hoc_id`) REFERENCES `dt_mon_hoc` (`id`),
  ADD CONSTRAINT `FK_PHAN_CONG_GIANG_VIEN_KHCT` FOREIGN KEY (`khoa_hoc_chuong_trinh_id`) REFERENCES `dt_khoa_hoc_chuong_trinh` (`id`);

--
-- Constraints for table `dt_tai_lieu`
--
ALTER TABLE `dt_tai_lieu`
  ADD CONSTRAINT `FK_TAI_LIEU_KHCT` FOREIGN KEY (`khoa_hoc_chuong_trinh_id`) REFERENCES `dt_khoa_hoc_chuong_trinh` (`id`),
  ADD CONSTRAINT `FK_TL_KhoaHoc` FOREIGN KEY (`khoa_hoc_id`) REFERENCES `dt_khoa_hoc` (`id`),
  ADD CONSTRAINT `FK_TL_MonHoc` FOREIGN KEY (`mon_hoc_id`) REFERENCES `dt_mon_hoc` (`id`);

--
-- Constraints for table `nckh_de_tai`
--
ALTER TABLE `nckh_de_tai`
  ADD CONSTRAINT `FK_DT_CapDo` FOREIGN KEY (`cap_do_id`) REFERENCES `dm_nckh_cap_do` (`id`),
  ADD CONSTRAINT `FK_DT_ChuNhiem` FOREIGN KEY (`chu_nhiem_id`) REFERENCES `dm_nhan_vien` (`id`),
  ADD CONSTRAINT `FK_DT_Dot` FOREIGN KEY (`dot_dang_ky_id`) REFERENCES `nckh_dot_dang_ky` (`id`),
  ADD CONSTRAINT `FK_DT_Khoa` FOREIGN KEY (`khoa_phong_id`) REFERENCES `dm_khoa_phong` (`id`),
  ADD CONSTRAINT `FK_DT_TheLoai` FOREIGN KEY (`the_loai_id`) REFERENCES `dm_nckh_the_loai` (`id`),
  ADD CONSTRAINT `FK_DT_ThuKy` FOREIGN KEY (`thu_ky_id`) REFERENCES `dm_nhan_vien` (`id`);

--
-- Constraints for table `nckh_dot_giai_doan`
--
ALTER TABLE `nckh_dot_giai_doan`
  ADD CONSTRAINT `FK_GD_Dot` FOREIGN KEY (`dot_id`) REFERENCES `nckh_dot_dang_ky` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `nckh_hoi_dong`
--
ALTER TABLE `nckh_hoi_dong`
  ADD CONSTRAINT `FK_HD_DeTai` FOREIGN KEY (`de_tai_id`) REFERENCES `nckh_de_tai` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_HD_Khoa` FOREIGN KEY (`khoa_phong_id`) REFERENCES `dm_khoa_phong` (`id`),
  ADD CONSTRAINT `FK_HD_NhanVien` FOREIGN KEY (`nhan_vien_id`) REFERENCES `dm_nhan_vien` (`id`);

--
-- Constraints for table `nckh_nhac_viec`
--
ALTER TABLE `nckh_nhac_viec`
  ADD CONSTRAINT `FK_NV_DeTai` FOREIGN KEY (`de_tai_id`) REFERENCES `nckh_de_tai` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_NV_Nguoi` FOREIGN KEY (`nguoi_nhan_id`) REFERENCES `dm_nhan_vien` (`id`);

--
-- Constraints for table `nckh_tai_lieu`
--
ALTER TABLE `nckh_tai_lieu`
  ADD CONSTRAINT `FK_TL_DeTai` FOREIGN KEY (`de_tai_id`) REFERENCES `nckh_de_tai` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `nckh_thanh_vien`
--
ALTER TABLE `nckh_thanh_vien`
  ADD CONSTRAINT `FK_TV_DeTai` FOREIGN KEY (`de_tai_id`) REFERENCES `nckh_de_tai` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_TV_NhanVien` FOREIGN KEY (`nhan_vien_id`) REFERENCES `dm_nhan_vien` (`id`);

--
-- Constraints for table `nckh_tien_do`
--
ALTER TABLE `nckh_tien_do`
  ADD CONSTRAINT `FK_TD_DeTai` FOREIGN KEY (`de_tai_id`) REFERENCES `nckh_de_tai` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_TD_NguoiBC` FOREIGN KEY (`nguoi_bao_cao_id`) REFERENCES `dm_nhan_vien` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
