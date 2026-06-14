<?php
require_once __DIR__ . '/DT_BaiKiemTra_PUBLIC.php';

class DT_BaiKiemTra_DTO extends DT_BaiKiemTra_PUBLIC
{
    public ?int $khoa_hoc_id = null;
    public ?string $ma_khoa_hoc = null;
    public ?string $ten_khoa_hoc = null;
    public ?string $ma_lop = null;
    public ?string $ten_lop = null;
    public ?string $ma_mon_hoc = null;
    public ?string $ten_mon_hoc = null;
    public ?string $tai_khoan_nguoi_tao = null;
}
