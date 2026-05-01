<?php
require_once __DIR__ . '/DM_NguoiDung_PUBLIC.php';

class DM_NguoiDung_DTO extends DM_NguoiDung_PUBLIC
{
    public ?string $ho_ten = null;
    public ?string $ma_nv = null;
    public ?string $chuc_danh = null;
    public ?string $khoa_phong_text = null;
    public ?string $ten_nhom = null;
    public ?string $ma_nhom = null;
    public ?string $tai_khoan_nguoi_tao = null;
    public ?string $tai_khoan_nguoi_cap_nhat = null;
    public ?string $trang_thai_text = null;
}
