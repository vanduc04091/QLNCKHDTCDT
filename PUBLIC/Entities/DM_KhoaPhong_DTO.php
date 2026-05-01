<?php
require_once __DIR__ . '/DM_KhoaPhong_PUBLIC.php';

class DM_KhoaPhong_DTO extends DM_KhoaPhong_PUBLIC
{
    public ?string $ten_truong_khoa = null;
    public ?int $so_nhan_vien = 0;
    public ?string $loai_don_vi_text = null;
    public ?string $trang_thai_text = null;
    public ?string $tai_khoan_nguoi_tao = null;
    public ?string $tai_khoan_nguoi_cap_nhat = null;
}
