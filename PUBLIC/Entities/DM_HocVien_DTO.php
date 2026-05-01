<?php
require_once __DIR__ . '/DM_HocVien_PUBLIC.php';

class DM_HocVien_DTO extends DM_HocVien_PUBLIC
{
    public ?string $ten_doi_tuong = null;
    public ?string $ma_doi_tuong = null;
    public ?string $ma_nv = null;
    public ?string $ten_khoa_phong = null;
    public ?string $ten_benh_vien = null;
    public ?string $tai_khoan_nguoi_tao = null;
    public ?string $tai_khoan_nguoi_cap_nhat = null;
}
