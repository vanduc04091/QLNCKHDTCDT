<?php
class NCKH_TaiLieu_PUBLIC
{
    public ?int    $id = null;
    public int     $de_tai_id = 0;
    public string  $loai_tai_lieu = 'Khac'; // DeCuong/QuyetDinh/BienBan/BaoCao/FileGoc/Khac
    public string  $ten_tai_lieu = '';
    public ?string $ten_file_goc = null;
    public ?string $ten_file_luu = null;
    public ?int    $kich_thuoc = null;
    public ?string $mime_type = null;
    public ?string $mo_ta = null;
    public ?string $ngay_tao = null;
    public ?string $ngay_cap_nhat = null;
    public ?int    $nguoi_tao = null;
    public ?int    $nguoi_cap_nhat = null;
    public int     $da_xoa = 0;
}
