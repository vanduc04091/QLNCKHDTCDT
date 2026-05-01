<?php
require_once __DIR__ . '/DM_NhanVien_PUBLIC.php';

class DM_NhanVien_DTO extends DM_NhanVien_PUBLIC
{
    public ?string $ten_benh_vien = null;
    public ?string $ten_khoa_phong = null;
    public ?string $loai_don_vi = null;
    public ?string $gioi_tinh_text = null;
    public ?string $trang_thai_text = null;
    public ?string $tai_khoan_nguoi_tao = null;
    public ?string $tai_khoan_nguoi_cap_nhat = null;
}
