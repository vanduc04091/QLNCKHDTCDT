<?php
require_once __DIR__ . '/DT_MonHoc_PUBLIC.php';

class DT_MonHoc_DTO extends DT_MonHoc_PUBLIC
{
    public ?string $tai_khoan_nguoi_tao = null;
    public ?string $tai_khoan_nguoi_cap_nhat = null;
    public ?int $so_khoa_hoc_su_dung = 0;
}
