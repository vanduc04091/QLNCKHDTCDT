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
    public ?string $ma_chuong_trinh = null;
    public ?string $ten_chuong_trinh = null;
    public ?int $chuong_trinh_id = null;
    public ?string $ma_khoa_hoc = null;
    public ?string $ten_khoa_hoc = null;
    public ?int $khoa_hoc_id = null;
}
