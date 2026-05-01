<?php
class NCKH_NhacViec_PUBLIC
{
    public ?int    $id = null;
    public int     $de_tai_id = 0;
    public string  $loai_nhac = 'TienDo'; // TienDo/DeadLine/NghiemThu/Khac
    public string  $tieu_de = '';
    public ?string $noi_dung = null;
    public string  $ngay_nhac = '';
    public ?int    $nguoi_nhan_id = null;
    public int     $da_gui = 0;
    public ?string $ngay_gui = null;
    public ?string $ket_qua_gui = null;
    public ?string $ngay_tao = null;
    public ?string $ngay_cap_nhat = null;
    public ?int    $nguoi_tao = null;
    public ?int    $nguoi_cap_nhat = null;
    public int     $da_xoa = 0;
}
