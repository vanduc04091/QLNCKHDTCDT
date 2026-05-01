<?php
class DT_DiemDanh_PUBLIC
{
    public ?int $id = null;
    public int $lich_hoc_id = 0;
    public int $hoc_vien_lop_id = 0;
    public int $trang_thai = 1;     // 0 vắng / 1 có mặt / 2 muộn / 3 vắng phép
    public ?string $gio_vao = null;
    public ?string $gio_ra = null;
    public ?string $ghi_chu = null;
    public ?string $ngay_tao = null;
    public ?string $ngay_cap_nhat = null;
    public ?int $nguoi_tao = null;
    public ?int $nguoi_cap_nhat = null;
    public int $da_xoa = 0;
}
