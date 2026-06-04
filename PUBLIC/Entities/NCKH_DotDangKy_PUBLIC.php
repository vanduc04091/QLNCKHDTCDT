<?php
class NCKH_DotDangKy_PUBLIC
{
    public ?int    $id = null;
    public string  $ten_dot = '';
    public int     $nam = 0;
    public string  $tu_ngay = '';
    public string  $den_ngay = '';
    public ?string $mo_ta = null;
    public int     $trang_thai = 1; // 1=HoatDong, 0=Khoa
    public ?string $ngay_tao = null;
    public ?string $ngay_cap_nhat = null;
    public ?int    $nguoi_tao = null;
    public ?int    $nguoi_cap_nhat = null;
    public int     $da_xoa = 0;
}
