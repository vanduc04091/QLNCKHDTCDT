# DATABASE.md — Schema QLNCDTCDT

Tài liệu hệ cơ sở dữ liệu của hệ thống **Quản lý NCKH - Đào tạo - Chỉ đạo tuyến**.

- **DBMS**: MariaDB / MySQL 5.7+
- **Charset**: `utf8mb4` / `utf8mb4_unicode_ci`
- **Engine**: InnoDB (full FK + transaction support)
- **Database name**: `ql_nckh_dt_cdt`

## Mục lục
- [1. Quy ước chung](#1-quy-ước-chung)
- [2. Phân nhóm bảng](#2-phân-nhóm-bảng)
- [3. Module Hệ thống & Danh mục dùng chung](#3-module-hệ-thống--danh-mục-dùng-chung)
- [4. Module Đào tạo (DT_*)](#4-module-đào-tạo-dt_)
- [5. Module NCKH (NCKH_*)](#5-module-nckh-nckh_)
- [6. Sơ đồ quan hệ chính](#6-sơ-đồ-quan-hệ-chính)
- [7. Conventions audit & soft delete](#7-conventions-audit--soft-delete)
- [8. Workflow đặc biệt](#8-workflow-đặc-biệt)

---

## 1. Quy ước chung

### Tên bảng / cột
- Tên bảng: `snake_case`, prefix theo nhóm: `DM_*` (danh mục), `DT_*` (đào tạo), `NCKH_*` (nghiên cứu khoa học).
- Cột tiếng Việt không dấu: `ho_ten`, `ngay_tao`, `trang_thai`...

### Cột chuẩn audit (gần như mọi bảng)
| Cột | Kiểu | Ý nghĩa |
|---|---|---|
| `id` | `INT AUTO_INCREMENT` PK | Khoá chính |
| `ngay_tao` | `DATETIME DEFAULT CURRENT_TIMESTAMP` | Thời điểm tạo bản ghi |
| `ngay_cap_nhat` | `DATETIME DEFAULT CURRENT_TIMESTAMP` | Thời điểm cập nhật cuối |
| `nguoi_tao` | `INT NULL` (FK → `dm_nguoi_dung.id` ngầm định) | User tạo |
| `nguoi_cap_nhat` | `INT NULL` | User cập nhật cuối |
| `da_xoa` | `INT DEFAULT 0` | Soft-delete flag (0=active, 1=deleted) |

### Convention soft-delete
- **Không bao giờ DELETE thật**, chỉ `UPDATE ... SET da_xoa = 1`.
- Mọi UNIQUE KEY đều bao gồm `da_xoa` ở cuối → cho phép tái sử dụng mã sau khi xoá.
- Mọi `SELECT` phải kèm `WHERE da_xoa = 0` (xem `<Module>_DAL::selectSql()`).

### `trang_thai` (tinyint)
- Phổ biến: `1 = Hoạt động`, `0 = Ngừng/Khoá`.
- Một số bảng dùng workflow nhiều giá trị (xem comment ở từng bảng).

---

## 2. Phân nhóm bảng

| Nhóm | Số bảng | Bảng |
|---|---|---|
| **Hệ thống** | 5 | `dm_nguoi_dung`, `dm_nhom_tai_khoan`, `dm_phan_quyen`, `dm_danh_sach_form`, `dm_nhat_ky_he_thong` |
| **Danh mục dùng chung** | 8 | `dm_benh_vien`, `dm_khoa_phong`, `dm_nhan_vien`, `dm_hoc_vien`, `dm_giang_vien`, `dm_doi_tuong_hoc_vien`, `dm_loai_hinh_dao_tao`, `dm_hinh_thuc_hoc` |
| **Cấu hình** | 1 | `dm_cau_hinh` |
| **Danh mục NCKH** | 2 | `dm_nckh_cap_do`, `dm_nckh_the_loai` |
| **Đào tạo (DT_*)** | 13 | `dt_khoa_hoc`, `dt_mon_hoc`, `dt_khoa_hoc_mon_hoc`, `dt_lop_hoc`, `dt_hoc_vien_lop`, `dt_lich_hoc`, `dt_diem_danh`, `dt_phan_cong_giang_vien`, `dt_bai_kiem_tra`, `dt_ket_qua_hoc_tap`, `dt_chung_chi`, `dt_dang_ky_khoa_hoc`, `dt_ho_so_hoc_vien`, `dt_tai_lieu` |
| **NCKH** | 6 | `nckh_de_tai`, `nckh_thanh_vien`, `nckh_hoi_dong`, `nckh_tien_do`, `nckh_tai_lieu`, `nckh_nhac_viec` |
| **Tổng** | **35** | |

---

## 3. Module Hệ thống & Danh mục dùng chung

### 3.1. `dm_nhom_tai_khoan` — Nhóm quyền
- `id`, `ma_nhom`, `ten_nhom`, `mo_ta`, `trang_thai`.
- **Quy ước**: `id = 1` ⇒ Admin (full quyền — hardcode trong `PhanQuyenHelper::hasQuyen()`).

### 3.2. `dm_nguoi_dung` — Tài khoản đăng nhập
- `tai_khoan` UNIQUE, `mat_khau` (bcrypt — `password_hash()`), `nhom_tai_khoan_id`, `nhan_vien_id`.
- `lan_dang_nhap_cuoi` lưu DATETIME login gần nhất.

### 3.3. `dm_danh_sach_form` — Khai báo form/module
- `modules_tuong_ung` (key chuỗi, ví dụ `NCKH_DeTai`, `DT_LopHoc`) — dùng làm key check quyền.
- `ten_form`, `form_cha_id` (cây nhóm form trong sidebar/cấu hình quyền).

### 3.4. `dm_phan_quyen` — Ma trận quyền
- `(nhom_tai_khoan_id, danh_sach_form_id)` UNIQUE.
- 4 cột boolean: `quyen_xem`, `quyen_them`, `quyen_sua`, `quyen_xoa`.
- Cache theo nhóm: key `phan_quyen:nhom:{id}` qua `MemcachedHelper`.

### 3.5. `dm_nhat_ky_he_thong` — Audit log
- `nguoi_dung_id`, `module`, `hanh_dong`, `bang_lien_quan`, `id_lien_quan`, `noi_dung_thay_doi` (TEXT/JSON), `dia_chi_ip`.

### 3.6. `dm_benh_vien` — Đơn vị
- `ma_benh_vien` UNIQUE, `cap_benh_vien` (TuyenTinh/TuyenHuyen…), `la_benh_vien_chinh` (đơn vị chủ quản chỉ đạo tuyến).

### 3.7. `dm_khoa_phong` — Khoa phòng
- `loai_don_vi` ('Khoa', 'Phòng', 'Trung tâm'…), `truong_khoa_id` → `dm_nhan_vien.id`.

### 3.8. `dm_nhan_vien` — Cán bộ bệnh viện (HR)
- `ma_nv` UNIQUE per `benh_vien_id`, `khoa_phong_id`.
- Là **anchor** cho: chủ nhiệm đề tài, thư ký, người báo cáo tiến độ, hội đồng, người nhận nhắc việc, giảng viên, học viên (qua `nhan_vien_id` ở `dm_giang_vien` và `dm_hoc_vien`).

### 3.9. `dm_hoc_vien`, `dm_giang_vien`
- Có thể đứng độc lập (người ngoài) **hoặc** liên kết `nhan_vien_id` (nếu là cán bộ nội bộ).
- `dm_hoc_vien.la_nhan_vien` = 1 nếu học viên là cán bộ.

### 3.10. `dm_cau_hinh` — Key/Value config
- PK: `ma_cau_hinh` (string), `gia_tri` TEXT.
- Dùng cho SMTP, brand, ngưỡng nhắc việc, v.v.

---

## 4. Module Đào tạo (DT_*)

### 4.1. `dt_khoa_hoc` — Khoá học (chương trình)
- `ma_khoa_hoc` UNIQUE, `loai_hinh_dao_tao_id`, `hinh_thuc_hoc_id`, `doi_tuong_hoc_vien_id`.
- `so_tiet_ly_thuyet`, `so_tiet_thuc_hanh`, `tong_so_tiet`, `so_tin_chi`.

### 4.2. `dt_mon_hoc` — Học phần
- `ma_mon_hoc` UNIQUE, số tiết / tín chỉ.

### 4.3. `dt_khoa_hoc_mon_hoc` — N:N khoá học × môn học
- `(khoa_hoc_id, mon_hoc_id)` + `thu_tu`, `bat_buoc`.

### 4.4. `dt_lop_hoc` — Lớp cụ thể (instance của khoá học)
- `ma_lop` UNIQUE, `khoa_hoc_id` (FK), `ngay_bat_dau`, `ngay_ket_thuc`, `so_luong_toi_da`, `dia_diem`.
- ⚠ Có 2 cột giảng viên: `giao_vien_id` (cũ) và `giang_vien_id_new` (mới). Đang trong quá trình migrate; xem [#technical-debt].

### 4.5. `dt_hoc_vien_lop` — N:N học viên × lớp
- `(lop_hoc_id, hoc_vien_id)` UNIQUE.
- `ngay_ghi_danh`, `diem_tong_ket`, `xep_loai`.

### 4.6. `dt_lich_hoc` — Buổi học
- `lop_hoc_id`, `buoi_thu`, `ngay_hoc`, `gio_bat_dau`, `gio_ket_thuc`, `phong_hoc`.
- `giang_vien_id` / `giang_vien_id_new` (cùng vấn đề migrate như `dt_lop_hoc`).
- `giang_vien_ngoai` (text) — giảng viên không có trong hệ thống.

### 4.7. `dt_diem_danh` — Điểm danh
- `(lich_hoc_id, hoc_vien_lop_id)` UNIQUE.
- `trang_thai`: 1=Có mặt, 2=Vắng có phép, 3=Vắng không phép, 4=Đi muộn (xem `Constants.php`).

### 4.8. `dt_phan_cong_giang_vien` — Phân công giảng dạy
- `(giang_vien_id, lop_hoc_id, mon_hoc_id, vai_tro)` UNIQUE.
- `vai_tro`: 1=Phụ trách chính, 2=Phụ giảng, …

### 4.9. `dt_bai_kiem_tra` — Đề kiểm tra
- `loai_bkt`: 1=Thường xuyên, 2=Giữa kỳ, 3=Cuối kỳ.
- File đề + đáp án (`de_file_name`, `dap_an_file_name`), `cong_khai_dap_an` flag.

### 4.10. `dt_ket_qua_hoc_tap` — Điểm theo môn
- `(hoc_vien_lop_id, mon_hoc_id)` UNIQUE.
- `diem_thuong_xuyen`, `diem_giua_ky`, `diem_cuoi_ky`, `diem_tong_ket`, `xep_loai`, `dat`.

### 4.11. `dt_chung_chi` — Chứng chỉ
- `hoc_vien_id`, `lop_hoc_id`, `so_chung_chi`, `ngay_cap`, `ngay_het_han`.
- `duong_dan_file` (file scan).

### 4.12. `dt_dang_ky_khoa_hoc` — Đăng ký từ ngoài (public form)
- `ma_tra_cuu` UNIQUE (mã tra cứu cho người đăng ký).
- `trang_thai`: 0=ChoDuyet, 1=DaDuyet, 2=TuChoi.
- Khi duyệt → tự tạo `dm_hoc_vien` + `dt_hoc_vien_lop` (set `hoc_vien_id`, `lop_hoc_id`).

### 4.13. `dt_ho_so_hoc_vien` — Hồ sơ đính kèm của HV
- `loai_ho_so` (CCCD, Bằng cấp, Chứng chỉ…), `duong_dan` file.

### 4.14. `dt_tai_lieu` — Tài liệu khoá/lớp/môn
- `loai_tai_lieu`: 1=Giáo trình, 2=Bài giảng, …
- Có thể gắn với `khoa_hoc_id`, `lop_hoc_id`, hoặc `mon_hoc_id`.
- `cong_khai`: 1 ⇒ cho phép xem không cần đăng nhập (hiện chưa expose ra public).

---

## 5. Module NCKH (NCKH_*)

### 5.1. `dm_nckh_cap_do` — Cấp độ đề tài (Cơ sở/Tỉnh/Bộ…)
### 5.2. `dm_nckh_the_loai` — Thể loại (Đề tài / Sáng kiến / Bài báo)

### 5.3. `nckh_de_tai` — Đề tài (bảng trung tâm)

**Phân vùng cột theo nhóm**:

| Nhóm | Cột |
|---|---|
| Định danh | `ma_de_tai` UNIQUE, `ten_de_tai`, `nam`, `tu_khoa` |
| Phân loại | `cap_do_id`, `the_loai_id`, `khoa_phong_id`, `ten_khoa_text` (text gốc khi không match FK) |
| Nhân sự | `chu_nhiem_id` (FK NV — bắt buộc), `thu_ky_id` |
| Nội dung | `muc_tieu`, `tom_tat`, `noi_dung_ung_dung` |
| Lịch | `ngay_bat_dau`, `ngay_ket_thuc_du_kien`, `ngay_nghiem_thu` |
| Kinh phí | `kinh_phi_du_toan`, `kinh_phi_thuc_te`, `nguon_kinh_phi` |
| Quyết định | `quyet_dinh_phe_duyet`, `ngay_quyet_dinh`, `quyet_dinh_cong_nhan`, `ngay_quyet_dinh_cong_nhan` |
| Lịch bảo vệ | `phien_bao_ve`, `dia_diem_bao_ve`, `ngay_bao_ve` |
| Kết quả | `ket_qua_xep_loai` ENUM(`XuatSac`,`Gioi`,`Kha`,`TrungBinhKha`,`Dat`,`KhongDat`), `diem_so` |
| Bài báo (nếu là `the_loai = BaiBao`) | `ten_tap_chi`, `so_tap_chi`, `nam_xuat_ban`, `issn_doi`, `link_bai_bao` |
| Trạng thái thực hiện | `trang_thai`: 0=DeXuat, 1=DangThucHien, 2=HoanThanh, 3=TamDung, 4=Huy |
| **Workflow duyệt** | `trang_thai_duyet` ENUM(`Nhap`,`ChoDuyet`,`DaDuyet`,`TuChoi`), `ngay_gui_duyet`, `ngay_xu_ly_duyet`, `nguoi_xu_ly_duyet`, `ly_do_tu_choi` |
| Audit | `da_xoa`, `ngay_tao`, `ngay_cap_nhat`, `nguoi_tao`, `nguoi_cap_nhat` |

**Index**: theo `nam`, `cap_do_id`, `the_loai_id`, `khoa_phong_id`, `chu_nhiem_id`, `trang_thai`, `trang_thai_duyet`.

**Quy tắc nghiệp vụ**:
- Module **NCKH_DeTai** (admin) chỉ hiển thị đề tài có `trang_thai_duyet='DaDuyet'`.
- Module **NCKH_DeTaiCuaToi** (nhân viên) chỉ hiển thị đề tài có `nguoi_tao = SessionHelper::userId()`.
- Module **NCKH_DuyetDeTai** (admin) là queue của các đề tài `ChoDuyet/DaDuyet/TuChoi`.

### 5.4. `nckh_thanh_vien` — Thành viên đề tài
- `de_tai_id` (FK CASCADE), `nhan_vien_id` (NULLable — cho người ngoài).
- `ho_ten_ngoai`, `don_vi_ngoai`, `ma_nv_text` (mã NV gốc khi chưa import).
- `vai_tro` (string tự do: 'Chủ nhiệm', 'Thư ký', 'Thành viên'…), `phan_tram_dong_gop`.

### 5.5. `nckh_hoi_dong` — Hội đồng nghiệm thu
- `de_tai_id` (FK CASCADE), `ho_ten`, `chuc_danh_hoc_vi` (BSCKII., ThS., TS., …).
- `nhan_vien_id` (nullable), `khoa_phong_id`, `ten_khoa_text`.
- `vai_tro_hd` ENUM: `ChuTich`, `ThuKy`, `PhanBien1`, `PhanBien2`, `ThanhVien`.

### 5.6. `nckh_tien_do` — Báo cáo tiến độ
- `de_tai_id` (FK CASCADE), `ky_bao_cao`, `ngay_bao_cao`, `phan_tram_hoan_thanh` (0-100).
- `cong_viec_da_lam`, `cong_viec_tiep_theo`, `kho_khan_vuong_mac`.

### 5.7. `nckh_tai_lieu` — File đính kèm đề tài
- `de_tai_id` (FK CASCADE).
- `loai_tai_lieu` ENUM: `DeCuong`, `QuyetDinh`, `BienBan`, `BaoCao`, `FileGoc`, `Khac`.
- `ten_file_goc`, `ten_file_luu` (random hash), `kich_thuoc`, `mime_type`.
- File vật lý: `assets/uploads/nckh/`.

### 5.8. `nckh_nhac_viec` — Nhắc việc
- `de_tai_id` (FK CASCADE), `loai_nhac` ENUM (`TienDo`,`DeadLine`,`NghiemThu`,`Khac`).
- `ngay_nhac` DATETIME, `nguoi_nhan_id` (NV).
- `da_gui` (0/1), `ngay_gui`, `ket_qua_gui` (log của cron `cron_nckh_nhac_viec.php`).

---

## 6. Sơ đồ quan hệ chính

### Hệ thống quyền
```
dm_nhom_tai_khoan ─┬─< dm_phan_quyen >─ dm_danh_sach_form
                   └─< dm_nguoi_dung >─ dm_nhan_vien
```

### Đào tạo (chuỗi học vụ)
```
dt_khoa_hoc ──< dt_lop_hoc ──< dt_hoc_vien_lop >── dm_hoc_vien
                  │                  │
                  ├──< dt_lich_hoc ──< dt_diem_danh
                  ├──< dt_phan_cong_giang_vien >── dm_giang_vien
                  └──< dt_bai_kiem_tra
                  
dt_hoc_vien_lop ──< dt_ket_qua_hoc_tap >── dt_mon_hoc
dt_hoc_vien_lop ──── dt_chung_chi (1:1 — khi tốt nghiệp)
dt_dang_ky_khoa_hoc ── dt_khoa_hoc / dm_hoc_vien (sau khi duyệt)
```

### NCKH (đề tài là trung tâm)
```
dm_nhan_vien (chủ nhiệm) ─┐
dm_khoa_phong ────────────┤
dm_nckh_cap_do ───────────┼─→ nckh_de_tai ─┬─< nckh_thanh_vien
dm_nckh_the_loai ─────────┘                ├─< nckh_hoi_dong
                                           ├─< nckh_tien_do
                                           ├─< nckh_tai_lieu
                                           └─< nckh_nhac_viec
                                                   (FK CASCADE)
```

---

## 7. Conventions audit & soft delete

### Khi `INSERT`
```php
INSERT INTO X (... , ngay_tao, nguoi_tao, da_xoa)
VALUES (... , NOW(), :user, 0)
```

### Khi `UPDATE`
```php
UPDATE X SET ..., ngay_cap_nhat = NOW(), nguoi_cap_nhat = :user WHERE id = :id AND da_xoa = 0
```

### Khi xoá (soft)
```php
UPDATE X SET da_xoa = 1, ngay_cap_nhat = NOW(), nguoi_cap_nhat = :user WHERE id = :id
```

### Khi `SELECT`
```php
SELECT ... FROM X WHERE X.da_xoa = 0 AND ...
```

### UNIQUE với soft-delete
Để cho phép tái sử dụng mã sau khi xoá:
```sql
UNIQUE KEY UQ_X_ma (ma_x, da_xoa)
```

---

## 8. Workflow đặc biệt

### 8.1. Workflow duyệt đề tài NCKH (4 trạng thái)
```
[Nhap] ──submit──→ [ChoDuyet] ──approve──→ [DaDuyet]
   ▲                  │
   │                  └──reject(ly_do)──→ [TuChoi] ──reset──→ [Nhap]
   │
   └── Nhân viên có thể sửa khi ở trạng thái Nhap hoặc TuChoi
```
Helper: `NCKH_DeTai_BUS::submitForReview / approveSubmission / rejectSubmission` + `canEditByOwner($id, $u)`.

### 8.2. Workflow đăng ký khoá học (3 trạng thái)
```
[ChoDuyet=0] ──duyet──→ [DaDuyet=1] (tự tạo HV + ghi danh lớp)
              ──tu_choi──→ [TuChoi=2]
```

### 8.3. Cron nhắc việc
- File: `cron_nckh_nhac_viec.php` (chạy bằng task scheduler).
- Quét `nckh_nhac_viec` với `da_gui=0 AND ngay_nhac <= NOW()`, gửi email qua `MailHelper::sendSmtp()`, set `da_gui=1`, ghi `ket_qua_gui`.

---

## 9. Migrations / Seeds đã chạy

| File | Mục đích |
|---|---|
| `upgrade_nckh.php` (đã xoá khỏi disk) | v1: tạo 7 bảng NCKH + seed danh mục |
| `upgrade_nckh_v2.php` (đã xoá) | v2: thêm cột lịch bảo vệ, QĐ công nhận, mở rộng ENUM xếp loại 6 mức, tạo `nckh_hoi_dong` |
| `upgrade_nckh_v3.php` (đã xoá) | v3: thêm 5 cột workflow duyệt, backfill `DaDuyet` cho legacy, seed form + quyền |
| `seed_nckh_real.php` (đã xoá) | Seed 20 đề tài thật từ Excel + thành viên + hội đồng |

> **⚠ Khuyến cáo**: Khi cần re-deploy, tạo lại các file migration bằng cách dump schema từ DB hiện tại và lưu vào `docs/migrations/`. Hiện tại không có migration framework chính thức.

---

## 10. Technical Debt

1. **Trùng cột `giang_vien_id` / `giang_vien_id_new`** ở `dt_lop_hoc` và `dt_lich_hoc` — đang dở dang chuyển từ `dm_nhan_vien` sang `dm_giang_vien`. Cần dứt khoát:
   - Quyết định cột nào là canonical.
   - Backfill dữ liệu từ cột cũ → cột mới (hoặc ngược lại).
   - Drop cột không dùng.

2. **`dt_chung_chi.loai_chung_chi`** mặc định lưu chuỗi mojibake `'Chß╗®ng chß╗ë'` (lỗi encoding cũ). Cần update toàn bộ → `'Chứng chỉ'` rồi đổi default.

3. **Hardcoded admin id = 1** ở `PhanQuyenHelper::hasQuyen()` (line 48). Nên thay bằng cờ `dm_nhom_tai_khoan.la_admin` để linh hoạt.

4. **Không có FK** từ các cột `nguoi_tao` / `nguoi_cap_nhat` → `dm_nguoi_dung.id`, dù logic ngầm định. Có thể cố ý để tránh ràng buộc khi xoá user.

5. **`dm_phan_quyen` không có cột `da_xoa`** — phá vỡ convention. Khi xoá phân quyền là DELETE thật.

6. **Không có index FULLTEXT** cho các cột text dài (`ten_de_tai`, `tom_tat`, `tieu_de` đề kiểm tra) — search hiện đang dùng `LIKE %...%` (không scale).
