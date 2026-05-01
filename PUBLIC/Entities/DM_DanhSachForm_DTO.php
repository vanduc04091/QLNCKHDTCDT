<?php
require_once __DIR__ . '/DM_DanhSachForm_PUBLIC.php';

class DM_DanhSachForm_DTO extends DM_DanhSachForm_PUBLIC
{
    public ?string $ten_form_cha = null;
    public ?string $tai_khoan_nguoi_tao = null;
    public ?string $tai_khoan_nguoi_cap_nhat = null;
}
