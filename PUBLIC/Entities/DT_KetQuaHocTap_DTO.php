<?php
require_once __DIR__ . '/DT_KetQuaHocTap_PUBLIC.php';

class DT_KetQuaHocTap_DTO extends DT_KetQuaHocTap_PUBLIC
{
    public ?int $hoc_vien_id = null;
    public ?string $ma_hv = null;
    public ?string $ho_ten = null;
    public ?string $avatar = null;
    public ?int $lop_hoc_id = null;
    public ?string $ma_lop = null;
    public ?string $ten_lop = null;
    public ?string $ma_mon_hoc = null;
    public ?string $ten_mon_hoc = null;
}
