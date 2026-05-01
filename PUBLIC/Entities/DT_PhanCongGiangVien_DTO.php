<?php
require_once __DIR__ . '/DT_PhanCongGiangVien_PUBLIC.php';

class DT_PhanCongGiangVien_DTO extends DT_PhanCongGiangVien_PUBLIC
{
    public ?string $ma_gv = null;
    public ?string $ho_ten_gv = null;
    public ?string $avatar_gv = null;
    public ?string $hoc_vi = null;
    public ?string $hoc_ham = null;
    public ?int $loai_gv = null;
    public ?string $ma_lop = null;
    public ?string $ten_lop = null;
    public ?string $ma_mon_hoc = null;
    public ?string $ten_mon_hoc = null;
}
