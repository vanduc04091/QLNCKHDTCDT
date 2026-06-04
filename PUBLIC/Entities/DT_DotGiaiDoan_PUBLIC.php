<?php
class DT_DotGiaiDoan_PUBLIC
{
    public ?int    $id = null;
    public int     $dot_id = 0;
    public string  $ten_giai_doan = '';
    public string  $hanh_vi = 'Submit'; // Submit | Review
    public string  $tu_ngay = '';
    public string  $den_ngay = '';
    public int     $thu_tu = 0;
    public ?string $ghi_chu = null;
    public ?string $ngay_tao = null;
    public ?string $ngay_cap_nhat = null;
    public ?int    $nguoi_tao = null;
    public ?int    $nguoi_cap_nhat = null;
    public int     $da_xoa = 0;
}
