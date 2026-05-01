<?php
class DT_ChungChi_PUBLIC
{
    public ?int    $id = null;
    public int     $hoc_vien_id = 0;
    public int     $lop_hoc_id = 0;
    public string  $so_chung_chi = '';
    public string  $ten_chung_chi = '';
    public string  $loai_chung_chi = 'Chứng chỉ';
    public ?string $xep_loai_tot_nghiep = null;
    public ?float  $diem_trung_binh = null;
    public string  $ngay_cap = '';
    public ?string $ngay_het_han = null;
    public ?string $nguoi_ky = null;
    public ?string $chuc_vu_nguoi_ky = null;
    public ?string $noi_cap = null;
    public ?string $duong_dan_file = null;
    public ?string $ghi_chu = null;
    public int     $trang_thai = 0;
    public ?string $ngay_tao = null;
    public ?string $ngay_cap_nhat = null;
    public ?int    $nguoi_tao = null;
    public ?int    $nguoi_cap_nhat = null;
    public int     $da_xoa = 0;
}
