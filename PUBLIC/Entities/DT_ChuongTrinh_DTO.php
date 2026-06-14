<?php
require_once __DIR__ . '/DT_ChuongTrinh_PUBLIC.php';

class DT_ChuongTrinh_DTO extends DT_ChuongTrinh_PUBLIC
{
    public ?string $ten_khoa_phong = null;
    public ?string $ten_doi_tuong = null;
    public ?int $so_hoc_vien = 0;
    public ?int $so_khoa_hoc = 0;   // số khóa học gắn (N:N)
    public ?int $so_mon_hoc = 0;    // số môn học gắn
    public ?string $tai_khoan_nguoi_tao = null;
    public ?string $tai_khoan_nguoi_cap_nhat = null;
}
