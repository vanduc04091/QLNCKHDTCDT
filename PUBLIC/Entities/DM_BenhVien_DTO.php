<?php
require_once __DIR__ . '/DM_BenhVien_PUBLIC.php';

class DM_BenhVien_DTO extends DM_BenhVien_PUBLIC
{
    public ?string $cap_benh_vien_text = null;
    public ?int $so_khoa_phong = 0;
    public ?int $so_nhan_vien = 0;
    public ?string $tai_khoan_nguoi_tao = null;
    public ?string $tai_khoan_nguoi_cap_nhat = null;
}
