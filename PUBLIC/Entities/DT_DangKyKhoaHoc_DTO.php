<?php
require_once __DIR__ . '/DT_DangKyKhoaHoc_PUBLIC.php';

class DT_DangKyKhoaHoc_DTO extends DT_DangKyKhoaHoc_PUBLIC
{
    // JOIN fields
    public ?string $ma_khoa_hoc = null;
    public ?string $ten_khoa_hoc = null;
    public ?string $ma_hv = null;
    public ?string $ho_ten_hoc_vien = null;
    public ?string $ma_lop = null;
    public ?string $ten_lop = null;
    public ?string $tai_khoan_nguoi_xu_ly = null;
}
