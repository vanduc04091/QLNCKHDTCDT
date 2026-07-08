# CLAUDE.md — Hướng dẫn cho AI Assistant maintain dự án QLNCDTCDT

> File này được Claude Code đọc tự động khi mở project. Mục tiêu: cung cấp đủ context để Claude hoặc dev mới bắt nhịp ngay.

---

## 1. Tổng quan

| Mục | Giá trị |
|---|---|
| **Tên** | QL NCKH - Đào tạo - Chỉ đạo tuyến |
| **Cho** | Bệnh viện Hữu nghị Đa khoa Nghệ An |
| **Phạm vi** | NCKH (đề tài/sáng kiến/bài báo), Đào tạo (khóa/lớp/HV/GV), Chỉ đạo tuyến (chưa làm) |
| **Stack** | PHP 7.4+/8.0, MariaDB 10.4+, jQuery 3.7, không framework |
| **Vị trí dev** | `d:\wwweb\QLNCDTCDT` (Windows + XAMPP) |
| **Domain dev** | `http://qldt.bv` (cấu hình `PUBLIC/Common/AppConfig.php::APP_URL`) |

## 2. Kiến trúc

### 2.1. Mô hình 3-tier
```
GUI/<Module>/         Presentation: index.php (HTML+JS) + ajax_handler.php
   │
BUS/<Module>_BUS.php  Business logic, validate, transaction
   │
DAL/<Module>_DAL.php  Data access (PDO + named placeholder)
   │
PUBLIC/Entities/      DTO (typed properties)
PUBLIC/Common/        Helpers (Session, PhanQuyen, Mail, Icon...)
```

### 2.2. Quy ước tên
- **Bảng DB**: `snake_case` — `dm_nhan_vien`, `nckh_de_tai`.
- **Class PHP**: PascalCase + suffix layer — `NCKH_DeTai_BUS`, `DM_NhanVien_DAL`.
- **File**: theo class — `BUS/NCKH_DeTai_BUS.php`.
- **Module key** (`dm_danh_sach_form.modules_tuong_ung`): `NCKH_DeTai`, `DT_LopHoc` — dùng làm key check quyền.

### 2.3. Entry point
- `index.php` (root) → redirect login hoặc dashboard.
- `bootstrap.php` → load AppConfig, helpers, DB, start session. **Mọi GUI/ajax_handler phải `require_once __DIR__ . '/../../bootstrap.php';` ở dòng đầu.**

---

## 3. Convention bắt buộc

### 3.1. Soft delete (KHÔNG DELETE thật)
- Luôn `UPDATE ... SET da_xoa = 1`.
- Mọi `SELECT` phải có `WHERE X.da_xoa = 0`.
- UNIQUE KEY luôn bao gồm `da_xoa` cuối: `UNIQUE (ma_x, da_xoa)` → cho phép tái dùng mã sau xoá.

### 3.2. Audit fields
Mọi bảng (trừ `dm_phan_quyen`) phải có:
`ngay_tao`, `ngay_cap_nhat`, `nguoi_tao`, `nguoi_cap_nhat`, `da_xoa`.
Set chúng từ `SessionHelper::userId()` ở BUS hoặc DAL.

### 3.3. PDO — KHÔNG reuse named placeholder
- `PDO::ATTR_EMULATE_PREPARES = false` (xem `DAL/database.php`) ⇒ một query không được dùng cùng `:name` 2 lần.
- Cần lặp giá trị (vd 5 LIKE search) → đặt `:s1, :s2, :s3, :s4, :s5` rồi bind từng giá trị.
- Đã từng gặp `HY093 Invalid parameter number` — luôn nhớ.

### 3.4. LIMIT / OFFSET
- An toàn để nội suy trực tiếp **chỉ khi** đã ép int qua `PaginationHelper::normalize(int, int)` hoặc cast `(int)` rõ ràng:
  ```php
  [$page, $pageSize, $offset] = PaginationHelper::normalize($page, $pageSize);
  $sql .= " LIMIT {$pageSize} OFFSET {$offset}"; // OK — đã type-safe
  ```
- **Không** dùng giá trị từ `$_POST` trực tiếp vào LIMIT/OFFSET nếu chưa cast.

### 3.5. AJAX response
Mọi ajax_handler trả qua `ResponseHelper`:
- `ResponseHelper::success($message, $data)`
- `ResponseHelper::error($message, $code)`
- `ResponseHelper::paged($data, $page, $size, $total)`

**Không** dùng `die()`, `exit`, `echo json_encode(...)` trực tiếp.

### 3.6. Auth + Permission + CSRF ở mọi ajax_handler
Pattern chuẩn (sau khi đã bật CSRF toàn hệ thống):
```php
require_once __DIR__ . '/../../bootstrap.php';
Helper::requireAjaxCsrf();                                  // 1. Login + CSRF
$action = Helper::post('action', '');
switch ($action) {
    case 'getPaged':
        PhanQuyenHelper::requireQuyen('NCKH_DeTai',
            PhanQuyenHelper::QUYEN_XEM);                    // 2. Permission
        // ... business logic
}
```
- `Helper::requireAjaxCsrf()` đọc token từ header `X-CSRF-Token` (đã được `APP.ajax` tự gắn) hoặc POST `_csrf_token` / `_csrf`.
- `APP.ajax()` ở `assets/js/app.js` tự lấy `window.CSRF_TOKEN` (set trong `header.php`) → mọi request POST đều có CSRF.
- Action chỉ-đọc (`getPaged`, `getById`, `getCombo`...) **vẫn nên** dùng `requireAjaxCsrf` để đồng nhất, hoặc tối thiểu `requireAjaxLogin`.

### 3.7. Transaction cho multi-table writes
Khi 1 action ghi vào ≥ 2 bảng, **bắt buộc** bọc transaction:
```php
try {
    Database::beginTransaction();
    // ... write A, B, C
    Database::commit();
} catch (Throwable $ex) {
    Database::rollBack();
    return ['success' => false, 'message' => 'Lỗi: ' . $ex->getMessage()];
}
```
- Side effects ngoài DB (gửi mail, ghi log audit ở bảng độc lập) đặt **ngoài** try/catch để không kéo nhau rollback.
- Đã có sẵn ở: `DT_DangKyKhoaHoc_BUS::approve()`, `DM_NhomTaiKhoan_BUS::delete()`, `DT_KhoaHocMonHoc_BUS::move()`, `DT_LichHoc_BUS` (tạo lịch hàng loạt).

### 3.8. Frontend
- jQuery 3.7 + 1 file global `assets/js/app.js` chứa `APP.ajax/toast/confirm/escape/debounce/showLoading/formatDate/formatDateTime/renderPagination`.
- Mỗi module có 1 `index.php` chứa cả HTML + `<script>` inline.
- Modal: `.modal-backdrop > .modal`, class `.open` để hiện.
- Drawer chi tiết bên phải: `.dt-drawer` z-index `80` (thấp hơn modal-backdrop `100` để modal đè drawer).

### 3.9. Icon
- Mọi icon qua `IconHelper::svg('name', size, class, color)`. Không hardcode SVG.
- Trong JS, embed bằng `json_encode`:
  ```php
  var ICON_EDIT = <?= json_encode(IconHelper::svg('edit', 18, 'icon', 'currentColor')) ?>;
  ```

### 3.10. Output XSS
- Echo biến vào HTML qua `Helper::h($val)`.
- JS hiện text qua `APP.escape(val)`.

### 3.11. Color / UI tokens
- `--primary: #16a34a` (xanh lá theo logo BV).
- Sidebar nền xanh đen `#1e293b → #0f172a`, active border-left `#ec4899` (hồng).
- File CSS chính: `assets/css/style.css`. Biến CSS ở `:root`.

### 3.12. Phân quyền Admin
- Cờ `dm_nhom_tai_khoan.la_admin = 1` ⇒ full quyền (skip check matrix).
- **KHÔNG hardcode `id === 1`** — đã refactor sang `PhanQuyenHelper::isAdminNhom($nhomId)`.
- Khi seed/restore DB phải đảm bảo nhóm Admin có `la_admin = 1`.

---

## 4. Tasks thường gặp

### 4.1. Thêm module CRUD mới
1. Thiết kế bảng DB (id + nghiệp vụ + audit + da_xoa, UNIQUE bao gồm da_xoa).
2. Tạo Entity DTO ở `PUBLIC/Entities/<Tên>_PUBLIC.php` (typed properties).
3. Tạo DAL: `selectSql()`, `getById()`, `getPaged($filter, $page, $size)`, `insert()`, `update()`, `softDelete()`.
4. Tạo BUS validate + gọi DAL, trả `['success'=>bool, 'message'=>str, 'data'=>...]`. Bọc transaction nếu multi-table.
5. Tạo `GUI/<Tên>/index.php` + `ajax_handler.php` (mở đầu bằng `Helper::requireAjaxCsrf()`).
6. Khai báo form trong `dm_danh_sach_form` (key + tên), gán quyền tại `dm_phan_quyen`.
7. Thêm link vào `GUI/layouts/sidebar.php`.

### 4.2. Thêm cột vào bảng có sẵn
1. ALTER TABLE qua phpMyAdmin hoặc qua PHP script (vì MySQL CLI Windows hay sai encoding với DEFAULT có Unicode).
2. Thêm property vào DTO.
3. Cập nhật `selectSql()`, `insert()`, `update()` ở DAL.
4. Cập nhật form ở GUI (input + JS load/save).
5. Cập nhật BUS nếu có validate.

### 4.3. Sửa quyền cho nhóm
- UI: `GUI/DM_PhanQuyen/index.php` (ma trận).
- Hoặc UPDATE `dm_phan_quyen` rồi `MemcachedHelper::deleteByPrefix('phan_quyen:')` để clear cache.

### 4.4. Bật / tắt một nhóm thành Admin
```sql
UPDATE dm_nhom_tai_khoan SET la_admin = 1 WHERE id = X;
```
Sau đó: `MemcachedHelper::deleteByPrefix('phan_quyen:')`.

---

## 5. Helpers

| Helper | Mô tả |
|---|---|
| `Helper::h($val)` | Escape HTML |
| `Helper::post('key', $default)` / `postInt` / `postStr` | Lấy POST |
| `Helper::requireLogin()` | Redirect login nếu chưa đăng nhập |
| `Helper::requireAjaxLogin()` | Trả 401 JSON nếu chưa đăng nhập |
| **`Helper::requireAjaxCsrf()`** | Login + verify CSRF — dùng cho **mọi ajax_handler** |
| `SessionHelper::userId() / nhomTaiKhoanId() / taiKhoan() / hoTen()` | Đọc info user |
| `SessionHelper::csrfToken() / verifyCsrf($t)` | CSRF token |
| `PhanQuyenHelper::hasQuyen($key, $quyen) / requireQuyen()` | Check quyền |
| `PhanQuyenHelper::isAdminNhom($nhomId)` | Check nhóm có cờ la_admin |
| `ResponseHelper::success / error / paged / json` | JSON response |
| `Database::beginTransaction / commit / rollBack` | Transaction |
| `Database::hydrate($row, $class)` | Map row → DTO |
| `MemcachedHelper::get / set / delete / deleteByPrefix` | Cache |
| `MailHelper::sendSmtp(...)` | SMTP qua fsockopen (no PHPMailer) |
| `IconHelper::svg($name, $size, $class, $color)` | Render SVG |
| `PaginationHelper::normalize($page, $size)` | Trả `[page, size, offset]` đã clamp |

### Frontend (`APP` namespace)
| Method | Mô tả |
|---|---|
| `APP.ajax(url, data, opts)` | $.ajax wrapper, **tự gắn CSRF**, handle 401 redirect |
| `APP.toast(msg, type)` | Notification (success/error/warning/info) |
| `APP.confirm(msg, onYes, opts)` | Modal xác nhận |
| `APP.escape(str)` | Escape HTML |
| `APP.debounce(fn, ms)` | Debounce |
| `APP.formatDate / formatDateTime` | Hiện dd/mm/Y [H:i] |
| `APP.renderPagination(info)` | Render UI phân trang |
| `APP.showLoading / hideLoading` | Overlay loading |

---

## 6. Run / Test

- Web server: XAMPP Apache + MariaDB ở `C:\xampp`.
- DB credentials dev: user `root`, pass rỗng (xem `AppConfig.php`).
- Cron nhắc việc thủ công: `php cron_nckh_nhac_viec.php` (cần task scheduler ở prod).
- Không có unit test framework. Test thủ công qua browser.
- **Khi cần ALTER TABLE có DEFAULT chứa Unicode**: chạy qua PHP script (`Database::getConnection()->exec(...)`), KHÔNG dùng `mysql.exe` CLI vì Windows console encoding sẽ phá UTF-8.

---

## 7. Security state hiện tại

| Hạng mục | Trạng thái |
|---|---|
| CSRF | ✅ Đã bật toàn hệ thống qua `Helper::requireAjaxCsrf()` + `APP.ajax` tự gắn header |
| SQL injection | ✅ PDO + named placeholder + `EMULATE_PREPARES=false` |
| Soft delete | ✅ Convention enforce |
| Permission RBAC | ✅ `dm_phan_quyen` + cờ `la_admin` |
| Bcrypt password | ✅ `password_hash` + `password_verify`, cost 10 (nên tăng 12 ở prod) |
| File upload | ✅ Whitelist ext + tên random, `.htaccess` chặn PHP cho cả `assets/uploads/` |
| XSS | ✅ Mọi output qua `Helper::h()` / `APP.escape()` |
| Audit log | ✅ `dm_nhat_ky_he_thong` |

**Còn cần làm trước deploy production**: xem `docs/SECURITY.md` (rate limit login, đổi DB password, set `APP_DEBUG=false`, HTTPS, cookie `secure`, security headers).

---

## 8. Files / Folders quan trọng

| Path | Vai trò |
|---|---|
| `bootstrap.php` | Entry bootstrap |
| `index.php` | Root redirect |
| `cron_nckh_nhac_viec.php` | Cron gửi mail nhắc việc |
| `PUBLIC/Common/` | Helpers chung |
| `PUBLIC/Entities/` | DTOs |
| `assets/css/style.css` | CSS chính |
| `assets/js/app.js` | JS chung (APP namespace, tự gắn CSRF) |
| `assets/uploads/.htaccess` | Chặn PHP execution toàn cây upload |
| `assets/uploads/<sub>/` | Upload files mỗi module |
| `GUI/layouts/header.php` | Layout (sidebar/topbar/CSRF token JS) |
| `GUI/layouts/sidebar.php` | Sidebar menu |
| `GUI/layouts/footer.php` | Layout footer + script toggle sidebar |
| `docs/database.md` | Schema documentation chi tiết |
| `docs/SECURITY.md` | Security review + checklist deploy |
| `docs/de_xuat_phan_mem.md` | Tài liệu yêu cầu gốc |

---

## 9. Kim chỉ nam khi sửa code

1. **Soft delete > delete** — không xoá thật.
2. **Named placeholder không reuse** — `:s1, :s2…` khi cần lặp.
3. **`requireAjaxCsrf()` ở MỌI ajax_handler** — không skip dù chỉ là action read-only.
4. **`Helper::h()` mọi output** — không echo biến trực tiếp.
5. **`IconHelper::svg()` mọi icon** — không hardcode SVG.
6. **Transaction cho ≥ 2 table writes** trong cùng action.
7. **Đọc 1 module hiện có** (ví dụ `NCKH_DeTai`) trước khi viết module mới — convention nhất quán.
8. **Đừng tự ý refactor lớn** — user prefer pragmatic. Sửa đúng cái cần.
9. **Đừng tạo file thừa** (docs/note/plan riêng) trừ khi user yêu cầu.

---

## 10. Lưu ý khi làm việc với AI Assistant

- User dùng tiếng Việt; code/identifier không dấu.
- User prefer terse: làm đúng, không narrate, không comment dư.
- Nhiều bước → dùng TodoWrite, mark completed ngay khi xong từng bước.
- Tìm code: dùng Grep/Glob, không `find`/`grep` qua Bash.
- Trên Windows + Git Bash: dùng forward slash trong path.
- ALTER TABLE Unicode default: PHP script, không CLI mysql.
- MySQL binary: `/c/xampp/mysql/bin/mysql.exe` và `mysqldump.exe`.
- PHP CLI: `/c/xampp/php/php.exe`.

---

## 11. Roadmap

- ✅ **Phần 1: NCKH** — đề tài/sáng kiến/bài báo, workflow duyệt 4 trạng thái, dashboard, cron mail.
- ✅ **Hardening** — CSRF, htaccess uploads, admin flag, transactions, schema cleanup (xem changelog).
- ⏳ **Phần 2: Mở rộng đào tạo** — báo cáo BCĐT, in chứng chỉ hàng loạt.
- ⏳ **Phần 3: Chỉ đạo tuyến** — quản lý đoàn công tác, hợp tác BV tuyến dưới.
- ⏳ **Phần 4: Mobile/PWA** (nếu cần).

Xem chi tiết: `docs/de_xuat_phan_mem.md`.

---

## 12. Changelog quan trọng (cho người maintain sau)

### 2026-07-08 — Import học viên từ Excel
- ✅ **`ExcelHelper::readRows($path)`** — đọc sheet1 của `.xlsx` bằng `ZipArchive` (sharedStrings + inlineStr + số/ngày serial). Trả mảng dòng theo chỉ số cột (A=0…). Lưu ý: ô tự đóng `<c .../>` phải khớp TRƯỚC ô có nội dung trong regex, nếu không sẽ nuốt ô kế tiếp (đã fix).
- ✅ **`DM_HocVien_BUS::importExcel($path,$userId)`** — import theo mẫu `docs/Mẫu danh sách nhập thông tin học viên.xlsx` (21 cột). Match đối tượng theo tên/mã, match cặp (Khóa, CTĐT) qua mã trước `" - "` → `khct.id` rồi ghi danh vào `dt_hoc_vien_lop` kèm ngày BĐ/KT. HV trùng CCCD/SĐT → bỏ qua. Không tìm thấy cặp khóa/CTĐT → vẫn tạo HV, `enroll=notfound` (bảng kết quả tô vàng). Mỗi dòng bọc transaction riêng.
- ✅ **Cột mới `dm_hoc_vien`**: `trinh_do_chuyen_mon`, `cccd_ngay_cap`, `cccd_noi_cap`, `truong_dao_tao`, `nam_tot_nghiep` (SQL tự chạy: `docs/sql_them_cot_import_hoc_vien.sql`). DTO/DAL insert/update đã cập nhật.
- ✅ **GUI**: nút "Import Excel" + modal (chọn file, bảng kết quả tô màu theo trạng thái), action `import` ở ajax_handler (upload multipart, whitelist `.xlsx` ≤5MB), `GUI/DM_HocVien/tai_mau_import.php` tải file mẫu.
- ✅ **Form/View/Export cột mới**: form thêm/sửa + tab "Thông tin" (chỉ xem) trong drawer HV hiển thị đủ 5 cột mới. **Export HV xuất theo đúng cấu trúc file mẫu** (21 cột, mỗi ghi danh 1 dòng — 1 HV nhiều CTĐT lặp nhiều dòng, HV chưa ghi danh để trống phần học vụ; Khóa/CTĐT dạng `MÃ - Tên` để import lại được). Lấy ghi danh gộp 1 truy vấn: `DT_HocVienLop_DAL::getEnrollmentsForExport($ids)`.
- ✅ **Danh sách HV**: subquery `so_ghi_danh` → HV chưa ghi danh CTĐT nào tô **vàng nhạt** + tag "chưa ghi danh" (cả table & card view, không áp dụng thùng rác).

### 2026-06-24 — Export Excel + Báo cáo
- ✅ **`ExcelHelper`** (`PUBLIC/Common/ExcelHelper.php`, nạp ở bootstrap): xuất `.xlsx` OOXML tự viết bằng `ZipArchive` (không cần thư viện). API: `ExcelHelper::downloadOne($file,$sheet,$headers,$rows)` hoặc `download($file,$sheets[])` (mỗi sheet có `name/title/headers/rows`). Header in đậm nền xám, hỗ trợ tiếng Việt (inlineStr), số → kiểu number.
- ✅ **Export danh sách**: mỗi module 1 file `GUI/<Module>/export.php` (GET, check `requireLogin` + quyền XEM, đọc filter qua query string trùng tham số màn list, gọi `getPaged(1,100000,...)`). Nút "Xuất Excel" gọi `window.location=export.php?<filter>` (KHÔNG qua APP.ajax vì là tải file). Đã thêm cho 18 module: HV, Khóa, CTĐT, Bài học, Lịch học, Chứng chỉ, Tài liệu, Bài kiểm tra, Đăng ký, Hồ sơ HV, Kết quả học tập, Đợt đăng ký, Nhân viên, Khoa/Phòng, Giảng viên, Đối tượng HV, Loại hình ĐT, Hình thức học. (Điểm danh/Phân công GV không phải list phẳng → nằm trong Báo cáo.)
- ✅ **Trang Báo cáo** (`GUI/BaoCao/`, module key `DT_BaoCao`, form id 44, menu nhóm Tổng quan): `BUS/DT_BaoCao_BUS` 3 báo cáo — `theoKhoaCtdt($kh,$from,$to)` (số HV/đạt/điểm TB/chứng chỉ mỗi khóa+CTĐT, lọc theo `khct.ngay_bat_dau`), `dsHocVienKetQua($khct,$from,$to)` (bảng điểm + chuyên cần, lọc `hvl.ngay_ghi_danh`), `thongKeTong($from,$to)` (chỉ tiêu thời gian lọc theo khoảng, danh mục giữ tổng tồn). Tab dùng CSS `.bc-tab`, toolbar `.bc-toolbar` có ô từ/đến ngày. Xuất Excel qua `GUI/BaoCao/export.php?loai=khoa|hv|tong&from=&to=`.

### 2026-06-12 — Chuyển "Lớp học" → "Chương trình đào tạo" (CTĐT)
- ✅ Bảng `dt_lop_hoc` → `dt_chuong_trinh` (`ma_lop`/`ten_lop` → `ma_chuong_trinh`/`ten_chuong_trinh`; thêm `thoi_luong`, `khoa_phong_id`; bỏ `khoa_hoc_id`). Class/file/GUI `DT_LopHoc*` → `DT_ChuongTrinh*`. MODULE_KEY `DT_LopHoc` → `DT_ChuongTrinh`.
- ✅ Khóa học ↔ CTĐT là **N:N** qua bảng mới `dt_khoa_hoc_chuong_trinh` (`id` = "ngữ cảnh học vụ"). Bridge: `DT_KhoaHocChuongTrinh_DAL/BUS` (`getCombo()` label "Khóa — CTĐT").
- ✅ **Mọi bảng học vụ** (`dt_hoc_vien_lop`, `dt_lich_hoc`, `dt_phan_cong_giang_vien`, `dt_bai_kiem_tra`, `dt_chung_chi`, `dt_tai_lieu`, `dt_dang_ky_khoa_hoc`) đổi cột `lop_hoc_id` → `khoa_hoc_chuong_trinh_id` (FK → `dt_khoa_hoc_chuong_trinh.id`). DTO/PUBLIC giữ field PHP `lop_hoc_id` (alias) + SELECT alias `ma_lop`/`ten_lop` để giảm sửa GUI.
- ✅ Môn gắn theo CTĐT: `dt_lop_hoc_mon_hoc` → `dt_chuong_trinh_mon_hoc`. Module `DT_KhoaHocMonHoc` (trỏ bảng ma `dt_khoa_hoc_mon_hoc` KHÔNG tồn tại → là **lỗi gốc khiến module Môn học không xem/thêm được**) đã xóa, viết mới `DT_ChuongTrinhMonHoc`. UI gán môn chuyển sang màn CTĐT (drawer 2 tab: gắn khóa N:N + gắn môn); màn Khóa học chỉ xem CTĐT áp dụng (read-only).
- ✅ Migration backfill 1-1 giữ nguyên dữ liệu thật. Backup: `backup_truoc_ctdt_*.sql`.

### 2026-06-15 — Bài học ↔ CTĐT trở lại N:N
- ✅ **Đảo quan hệ về N:N**: 1 bài thuộc nhiều CTĐT, 1 CTĐT nhiều bài — dùng lại bảng nối `dt_chuong_trinh_mon_hoc` (thứ tự `thu_tu` + `bat_buoc` theo từng cặp). Cột `dt_mon_hoc.chuong_trinh_id`/`thu_tu` KHÔNG còn dùng (giữ để rollback). SQL backfill: `docs/sql_baihoc_ctdt_nhieu_nhieu.sql`.
- ✅ `DT_MonHoc_BUS::insert/update` nhận thêm `array $chuongTrinhIds` → `syncChuongTrinh()` thêm/gỡ cặp. `getByChuongTrinh/getChuaGanCombo/assignToChuongTrinh/unassign/move` chuyển sang gọi `DT_ChuongTrinhMonHoc_DAL` (id thao tác là id bảng nối). `getChuongTrinhIds()` để preset multi-select.
- ✅ Màn **Bài học**: form chọn CTĐT đổi từ select đơn → **multi-select** `chuong_trinh_ids[]`; bỏ cột TT/ô Thứ tự; cột Chương trình hiện "N CTĐT". `getById` trả thêm `chuong_trinh_ids`.
- ✅ Màn **CTĐT** tab Bài học + `getOverview`/`getMonHocByLop`/subquery `so_mon_hoc`/`getStats.co_mon` đọc qua bảng nối.

### 2026-06-13 — Bài học thuộc 1 CTĐT (1:N) + thứ tự
- ✅ **Đổi quan hệ bài học↔CTĐT từ N:N → 1:N**: thêm `dt_mon_hoc.chuong_trinh_id` (FK → `dt_chuong_trinh`, nullable). Backfill từ `dt_chuong_trinh_mon_hoc` (mỗi bài ≤1 CTĐT nên không mất dữ liệu). Bảng nối `dt_chuong_trinh_mon_hoc` còn lại nhưng code KHÔNG dùng nữa (giữ để rollback).
- ✅ **Thêm cột `thu_tu`** cho `dt_mon_hoc` và `dt_chuong_trinh`. Mọi combo + `getPaged` sort theo `thu_tu ASC, id ASC`. Bài insert với thu_tu=0 + có CTĐT → tự xếp cuối (`getMaxThuTuByChuongTrinh + 1`).
- ✅ Màn **Bài học**: form thêm/sửa có chọn CTĐT (tùy chọn) + nhập thứ tự; bảng có cột TT + cột Chương trình; filter theo CTĐT. Bỏ drawer gán N:N cũ + các action `addMonToKhoa`/`removeMonKhoiKhoa`.
- ✅ Màn **CTĐT** tab "Bài học": đổi từ gắn/gỡ N:N → danh sách bài thuộc CTĐT (theo thu_tu) + nút ↑/↓ (`DT_MonHoc_BUS::move` hoán đổi thu_tu). `mon_list` trả `getByChuongTrinh`.
- ✅ `getOverview`/`getMonHocByLop`/subquery `so_mon_hoc`/`getStats.co_mon` đọc bài qua `dt_mon_hoc.chuong_trinh_id` thay vì bảng nối.

### 2026-06-13 — Tách trường học vụ CTĐT sang bảng nối + fix HY093
- 🐞 **Fix HY093 khi thêm/sửa CTĐT**: `DT_ChuongTrinh_DAL::insert` reuse placeholder `:u` 2 lần (`:u, :u`) — với `EMULATE_PREPARES=false` gây "Invalid parameter number". Đổi thành `:u1, :u2`.
- ✅ **Bỏ khỏi `dt_chuong_trinh`** các cột: `ngay_bat_dau`, `ngay_ket_thuc`, `dia_diem`, `giao_vien_id`, `giao_vien_ngoai`, `trang_thai`. Các trường này **phụ thuộc ngữ cảnh khóa cụ thể** → chuyển sang bảng nối `dt_khoa_hoc_chuong_trinh` (mỗi cặp khóa+CTĐT có lịch học vụ riêng). Không backfill (dữ liệu cũ là test).
- ✅ **Thêm `dt_chuong_trinh.doi_tuong_id`** (FK → `dm_doi_tuong_hoc_vien`): đối tượng học viên của CTĐT. Combo + filter + cột bảng + form.
- ✅ Bảng nối thêm `updateInfo()` + action `khoa_update`; drawer tab "Khóa học áp dụng" giờ là modal nhập/sửa ngày/địa điểm/GVCN/trạng thái cho từng cặp khóa.
- ✅ `getStats` CTĐT đổi từ đếm theo trạng thái → đếm `co_khoa`/`co_mon`. Dashboard `getKpis`/`getTopFullClasses` bỏ điều kiện `trang_thai` trên CTĐT. `getByHocVien`/`getByKhoaHoc`/`PhanCongGiangVien` đọc ngày/trạng thái từ `khct` thay vì `ct`/`lop`.

### 2026-05-01 — Hardening trước deploy
- ✅ Thêm `Helper::requireAjaxCsrf()` + `APP.ajax` tự gắn `X-CSRF-Token`. Replace toàn bộ 33 `ajax_handler` từ `requireAjaxLogin()` → `requireAjaxCsrf()`.
- ✅ Đặt `assets/uploads/.htaccess` chung chặn PHP execution cho cả cây uploads (thay vì rải mỗi subdir).
- ✅ Thêm cột `dm_nhom_tai_khoan.la_admin`, refactor `PhanQuyenHelper::hasQuyen()` từ `id===1` → `isAdminNhom()`.
- ✅ Drop `dt_lop_hoc.giang_vien_id_new` và `dt_lich_hoc.giang_vien_id_new` (cột tàn dư của lần migrate dở). Sửa subquery trong `DM_GiangVien_DAL` về cột canonical `giang_vien_id` qua bridge `dm_giang_vien.nhan_vien_id`.
- ✅ Fix DEFAULT của `dt_chung_chi.loai_chung_chi` (mojibake `Chß╗®ng chß╗ë` → `Chứng chỉ`) qua PHP script.
- ✅ Bọc transaction: `DT_DangKyKhoaHoc_BUS::approve`, `DM_NhomTaiKhoan_BUS::delete`, `DT_KhoaHocMonHoc_BUS::move`, `DT_LichHoc_BUS` createBatch.
