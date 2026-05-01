<?php
require_once __DIR__ . '/DT_TaiLieu_PUBLIC.php';

class DT_TaiLieu_DTO extends DT_TaiLieu_PUBLIC
{
    public ?string $ma_khoa_hoc = null;
    public ?string $ten_khoa_hoc = null;
    public ?string $ma_lop = null;
    public ?string $ten_lop = null;
    public ?string $ma_mon_hoc = null;
    public ?string $ten_mon_hoc = null;
    public ?string $tai_khoan_nguoi_tao = null;
}
