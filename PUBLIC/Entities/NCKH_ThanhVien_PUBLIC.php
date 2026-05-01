<?php
class NCKH_ThanhVien_PUBLIC
{
    public ?int    $id = null;
    public int     $de_tai_id = 0;
    public ?int    $nhan_vien_id = null;
    public ?string $ho_ten_ngoai = null;
    public ?string $don_vi_ngoai = null;
    public string  $vai_tro = 'Thành viên';
    public ?string $ma_nv_text = null;  // Mã NV gốc nếu chưa match FK
    public ?float  $phan_tram_dong_gop = null;
    public ?string $ghi_chu = null;
    public ?string $ngay_tao = null;
    public ?string $ngay_cap_nhat = null;
    public ?int    $nguoi_tao = null;
    public ?int    $nguoi_cap_nhat = null;
    public int     $da_xoa = 0;
}
