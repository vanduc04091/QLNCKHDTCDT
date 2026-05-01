<?php
require_once __DIR__ . '/DT_KhoaHocMonHoc_PUBLIC.php';

class DT_KhoaHocMonHoc_DTO extends DT_KhoaHocMonHoc_PUBLIC
{
    public ?string $ma_mon_hoc = null;
    public ?string $ten_mon_hoc = null;
    public ?int $so_tiet_ly_thuyet = 0;
    public ?int $so_tiet_thuc_hanh = 0;
    public ?int $tong_so_tiet = 0;
    public ?float $so_tin_chi = 0;
    public ?int $mon_trang_thai = 1;
}
