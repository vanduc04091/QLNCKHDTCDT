<?php
require_once __DIR__ . '/DT_KhoaHoc_PUBLIC.php';

class DT_KhoaHoc_DTO extends DT_KhoaHoc_PUBLIC
{
    public ?string $ten_loai_hinh = null;
    public ?string $ten_hinh_thuc = null;
    public ?string $ten_doi_tuong = null;
    public ?string $tai_khoan_nguoi_tao = null;
    public ?string $tai_khoan_nguoi_cap_nhat = null;
}
