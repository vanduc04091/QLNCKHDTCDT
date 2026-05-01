<?php
require_once __DIR__ . '/NCKH_ThanhVien_PUBLIC.php';

class NCKH_ThanhVien_DTO extends NCKH_ThanhVien_PUBLIC
{
    public ?string $ho_ten_nv = null;
    public ?string $ma_nv = null;
    public ?string $chuc_danh = null;
    public ?string $ten_khoa_phong = null;
}
