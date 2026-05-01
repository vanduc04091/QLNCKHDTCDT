<?php
require_once __DIR__ . '/DT_ChungChi_PUBLIC.php';

class DT_ChungChi_DTO extends DT_ChungChi_PUBLIC
{
    public ?string $ma_hoc_vien = null;
    public ?string $ho_ten_hoc_vien = null;
    public ?string $don_vi_cong_tac = null;
    public ?string $ma_lop = null;
    public ?string $ten_lop = null;
    public ?string $ma_khoa_hoc = null;
    public ?string $ten_khoa_hoc = null;
    public ?string $tai_khoan_nguoi_tao = null;
    public ?string $tai_khoan_nguoi_cap_nhat = null;
}
