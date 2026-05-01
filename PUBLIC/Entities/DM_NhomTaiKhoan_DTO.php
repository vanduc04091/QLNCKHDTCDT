<?php
require_once __DIR__ . '/DM_NhomTaiKhoan_PUBLIC.php';

class DM_NhomTaiKhoan_DTO extends DM_NhomTaiKhoan_PUBLIC
{
    public ?int $so_nguoi_dung = 0;
    public ?string $tai_khoan_nguoi_tao = null;
    public ?string $tai_khoan_nguoi_cap_nhat = null;
}
