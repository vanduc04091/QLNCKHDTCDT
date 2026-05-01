<?php
require_once __DIR__ . '/DT_HoSoHocVien_PUBLIC.php';

class DT_HoSoHocVien_DTO extends DT_HoSoHocVien_PUBLIC
{
    public ?string $ma_hoc_vien = null;
    public ?string $ho_ten_hoc_vien = null;
    public ?string $don_vi_cong_tac = null;
    public ?string $tai_khoan_nguoi_tao = null;
    public ?string $tai_khoan_nguoi_cap_nhat = null;
}
