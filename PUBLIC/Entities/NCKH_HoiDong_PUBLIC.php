<?php
class NCKH_HoiDong_PUBLIC
{
    public ?int    $id = null;
    public int     $de_tai_id = 0;
    public string  $ho_ten = '';
    public ?string $chuc_danh_hoc_vi = null;  // BSCKII., ThS., TS....
    public ?int    $nhan_vien_id = null;
    public ?string $ten_khoa_text = null;
    public ?int    $khoa_phong_id = null;
    public string  $vai_tro_hd = 'ThanhVien'; // ChuTich/ThuKy/PhanBien1/PhanBien2/ThanhVien
    public int     $thu_tu = 0;
    public ?string $ghi_chu = null;
    public ?string $ngay_tao = null;
    public ?string $ngay_cap_nhat = null;
    public ?int    $nguoi_tao = null;
    public ?int    $nguoi_cap_nhat = null;
    public int     $da_xoa = 0;
}
