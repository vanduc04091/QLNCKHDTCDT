<?php
require_once __DIR__ . '/DM_GiangVien_PUBLIC.php';

class DM_GiangVien_DTO extends DM_GiangVien_PUBLIC
{
    public ?string $ma_nv = null;
    public ?string $ten_khoa_phong = null;
    public ?int $so_lop_phan_cong = 0;
    public ?int $so_buoi_da_day = 0;
    public ?string $tai_khoan_nguoi_tao = null;
}
