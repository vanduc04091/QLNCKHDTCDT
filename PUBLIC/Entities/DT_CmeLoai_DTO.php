<?php
require_once __DIR__ . '/DT_CmeLoai_PUBLIC.php';

class DT_CmeLoai_DTO extends DT_CmeLoai_PUBLIC
{
    public ?string $ma_nhom = null;
    public ?string $ten_nhom = null;
    public ?string $ten_khoa_phong = null;     // phòng phụ trách
    public ?string $tai_khoan_nguoi_tao = null;
    public ?string $tai_khoan_nguoi_cap_nhat = null;
}
