<?php
require_once __DIR__ . '/DT_LopHoc_PUBLIC.php';

class DT_LopHoc_DTO extends DT_LopHoc_PUBLIC
{
    public ?string $ma_khoa_hoc = null;
    public ?string $ten_khoa_hoc = null;
    public ?string $ten_giao_vien = null;
    public ?string $ma_giao_vien = null;
    public ?int $so_hoc_vien = 0;
    public ?string $tai_khoan_nguoi_tao = null;
    public ?string $tai_khoan_nguoi_cap_nhat = null;
}
