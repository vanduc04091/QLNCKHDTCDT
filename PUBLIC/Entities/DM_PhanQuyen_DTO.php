<?php
require_once __DIR__ . '/DM_PhanQuyen_PUBLIC.php';

class DM_PhanQuyen_DTO extends DM_PhanQuyen_PUBLIC
{
    public ?string $ten_nhom = null;
    public ?string $ma_nhom = null;
    public ?string $ten_form = null;
    public ?string $modules_tuong_ung = null;
    public int $form_cha_id = 0;
}
