<?php
require_once __DIR__ . '/DT_HocVienLop_PUBLIC.php';

class DT_HocVienLop_DTO extends DT_HocVienLop_PUBLIC
{
    public ?string $ma_hv = null;
    public ?string $ho_ten = null;
    public ?string $gioi_tinh = null;
    public ?string $avatar = null;
    public ?int $la_nhan_vien = 0;
    public ?string $ma_nv = null;
    public ?string $don_vi_cong_tac = null;
    public ?string $ten_doi_tuong = null;
    public ?string $ten_lop = null;
    public ?string $ma_lop = null;
}
