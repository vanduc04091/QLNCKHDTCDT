<?php
require_once __DIR__ . '/DT_CmeNhom_PUBLIC.php';

class DT_CmeNhom_DTO extends DT_CmeNhom_PUBLIC
{
    public ?int $so_loai = null;               // số loại thuộc nhóm
    public ?string $tai_khoan_nguoi_tao = null;
    public ?string $tai_khoan_nguoi_cap_nhat = null;
}
