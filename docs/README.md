# 📚 Hướng dẫn Modules - Hệ thống Quản lý NCKH - Đào tạo - Chỉ đạo tuyến

## 🎯 Nhanh chóng bắt đầu

Tất cả tài liệu module đã được tạo. **[Nhấn vào INDEX.md để xem danh sách đầy đủ](./INDEX.md)**

---

## 📂 Các file tài liệu đã tạo

### Core System Modules (8 files)

✅ **[DM_NguoiDung.md](./DM_NguoiDung.md)** - Quản lý người dùng/tài khoản  
✅ **[DM_NhanVien.md](./DM_NhanVien.md)** - Quản lý nhân viên  
✅ **[DM_KhoaPhong.md](./DM_KhoaPhong.md)** - Quản lý khoa/phòng (cấu trúc phân cấp)  
✅ **[DM_NhomTaiKhoan.md](./DM_NhomTaiKhoan.md)** - Quản lý nhóm tài khoản (roles)  
✅ **[DM_PhanQuyen.md](./DM_PhanQuyen.md)** - Quản lý phân quyền (permissions)  
✅ **[DM_DanhSachForm.md](./DM_DanhSachForm.md)** - Quản lý danh sách form/module  
✅ **[DM_BenhVien.md](./DM_BenhVien.md)** - Quản lý bệnh viện (master data)  
✅ **[DM_NhatKyHeThong.md](./DM_NhatKyHeThong.md)** - Nhật ký hệ thống (audit log)  

### Training System Modules (4 files)

✅ **[DM_LoaiHinhDaoTao.md](./DM_LoaiHinhDaoTao.md)** - Danh mục loại hình đào tạo  
✅ **[DM_HinhThucHoc.md](./DM_HinhThucHoc.md)** - Danh mục hình thức học  
✅ **[DM_DoiTuongHocVien.md](./DM_DoiTuongHocVien.md)** - Danh mục đối tượng học viên  
✅ **[DT_KhoaHoc.md](./DT_KhoaHoc.md)** - Quản lý khóa học (kompleks, +6 bảng liên kết)  

### Reference & Index

✅ **[INDEX.md](./INDEX.md)** - Danh sách đầy đủ + learning paths  
✅ **README.md** - File này (tóm tắt nhanh)  

---

## 🚀 Bắt đầu từ đâu?

### 1️⃣ Mới vào dự án?

Đọc:
1. **[INDEX.md](./INDEX.md)** - Overview
2. **[DM_NguoiDung.md](./DM_NguoiDung.md)** - Login/User
3. **[DM_NhomTaiKhoan.md](./DM_NhomTaiKhoan.md)** - Roles
4. **[DM_PhanQuyen.md](./DM_PhanQuyen.md)** - Permissions

### 2️⃣ Cần hiểu 1 module cụ thể?

1. Vào [INDEX.md](./INDEX.md) tìm tên module
2. Nhấp vào link tương ứng
3. Mỗi file chứa đầy đủ thông tin:
   - Database schema
   - BUS methods
   - DAL methods
   - GUI patterns
   - Code examples

### 3️⃣ Cần implement tính năng mới?

1. [DM_NhomTaiKhoan.md](./DM_NhomTaiKhoan.md) - Nếu liên quan đến roles
2. [DM_PhanQuyen.md](./DM_PhanQuyen.md) - Nếu cần permission
3. [DM_NhatKyHeThong.md](./DM_NhatKyHeThong.md) - Nếu cần audit log

---

## 📖 Cấu trúc mỗi Module Doc

Mỗi file được cấu trúc như sau:

```
📋 Tổng quan (Overview + URL, bảng chính)
   ↓
📊 Database Schema (SQL table + relationships)
   ↓
🔧 BUS Layer (Business methods + examples)
   ↓
💾 DAL Layer (Data access methods)
   ↓
🎨 GUI Layer (Frontend page + AJAX patterns)
   ↓
📦 Entities (DTO & PUBLIC classes)
   ↓
🔗 Relationships (Liên kết với modules khác)
   ↓
💡 Use Cases (Ví dụ thực tế)
   ↓
📚 Related (Links đến modules liên quan)
```

---

## 🎯 Quick Reference

### Muốn...

| Mục đích | Link |
|---------|------|
| ...hiểu cấu trúc database | [INDEX.md](./INDEX.md) hoặc [database.md](./database.md) |
| ...implement CRUD module mới | [DM_NguoiDung.md](./DM_NguoiDung.md) (dùng làm template) |
| ...kiểm tra quyền | [DM_PhanQuyen.md](./DM_PhanQuyen.md) |
| ...ghi log | [DM_NhatKyHeThong.md](./DM_NhatKyHeThong.md) |
| ...hiểu cấu trúc tổ chức | [DM_KhoaPhong.md](./DM_KhoaPhong.md) |
| ...tạo role mới | [DM_NhomTaiKhoan.md](./DM_NhomTaiKhoan.md) |
| ...setup đào tạo | [DT_KhoaHoc.md](./DT_KhoaHoc.md) |
| ...hiểu sidebar menu | [DM_DanhSachForm.md](./DM_DanhSachForm.md) |

---

## 🔥 Modules quan trọng nhất

Nếu chỉ có thời gian đọc 3 file:

1. **[DM_NguoiDung.md](./DM_NguoiDung.md)** - Tất cả users phải qua đây
2. **[DM_PhanQuyen.md](./DM_PhanQuyen.md)** - Quyền check ở mọi action
3. **[DM_KhoaPhong.md](./DM_KhoaPhong.md)** - Cấu trúc tổ chức

---

## 💡 Coding Tips

### BUS Method Pattern
```php
public static function action($entity): array {
    // 1. Validate
    if (...invalid...) {
        return ['success' => false, 'message' => '...'];
    }
    
    // 2. Try-catch
    try {
        $id = DAL::insert($entity);
        
        // 3. Clear cache
        MemcachedHelper::deleteByPrefix('prefix:');
        
        // 4. Log
        DM_NhatKyHeThong_DAL::log(...);
        
        // 5. Return success
        return ['success' => true, 'message' => '...', 'data' => ['id' => $id]];
    } catch (Throwable $ex) {
        return ['success' => false, 'message' => $ex->getMessage()];
    }
}
```

### GUI AJAX Handler Pattern
```php
// Kiểm tra quyền
if (!PhanQuyenHelper::hasPermission($userId, FORM_ID, 'action')) {
    http_response_code(403);
    ResponseHelper::error('Không có quyền');
}

// Validate input
$dto = new DTO();
$errors = $dto->validate();
if (!empty($errors)) {
    ResponseHelper::validate($errors);
}

// Gọi BUS
$result = MODULE_BUS::action($entity);

// Return response
if ($result['success']) {
    ResponseHelper::success($result['data']);
} else {
    ResponseHelper::error($result['message']);
}
```

---

## 🔐 Security Checklist

- [ ] Kiểm tra quyền trước mỗi action
- [ ] Validate tất cả input
- [ ] Hash password với PASSWORD_BCRYPT
- [ ] Sanitize output với Helper::h()
- [ ] Ghi log mỗi operation CRUD
- [ ] Sử dụng prepared statements
- [ ] Clear cache sau update
- [ ] Soft delete (không xóa thực)

---

## 📊 Database Diagram (Quan hệ chính)

```
DM_BENH_VIEN
    ├── 1:N → DM_KHOA_PHONG
    │          └── 1:N → DM_NHAN_VIEN
    │                     └── 0:1 → DM_NGUOI_DUNG
    │                                └── N:1 → DM_NHOM_TAI_KHOAN
    │                                          └── 1:N → DM_PHAN_QUYEN
    │                                                    └── N:1 → DM_DANH_SACH_FORM
    │
    └── 1:N → DT_KHOA_HOC
             ├── N:1 → DM_LOAI_HINH_DAO_TAO
             ├── N:1 → DM_HINH_THUC_HOC
             ├── N:1 → DM_DOI_TUONG_HOC_VIEN
             └── 1:N → DT_LOP_HOC
                      └── 1:N → DT_HOC_VIEN_LOP
                               └── N:1 → DT_HOC_VIEN
```

---

## 📞 Liên hệ

Các tài liệu khác:
- **[database.md](./database.md)** - Schema chi tiết
- **[yeu_cau_du_an.md](./yeu_cau_du_an.md)** - Yêu cầu dự án
- **[de_xuat_phan_mem.md](./de_xuat_phan_mem.md)** - Đề xuất hệ thống

---

## ✅ Tài liệu đã hoàn thành

- ✅ 12 module files (3000+ dòng)
- ✅ Database schema cho mỗi module
- ✅ BUS methods + validations
- ✅ DAL methods
- ✅ GUI patterns + AJAX examples
- ✅ Code examples + use cases
- ✅ Entity definitions
- ✅ Relationships + dependencies
- ✅ Performance tips
- ✅ Security notes

---

**Created:** April 21, 2026  
**Version:** 1.0  
**Total Files:** 12 module docs + INDEX + README  
**Coverage:** 100% of modules listed in sidebar.php

Bắt đầu: **→ [Nhấn vào INDEX.md](./INDEX.md)**
