<?php
class DT_DangKyKhoaHoc_PUBLIC
{
    public ?int    $id = null;
    public string  $ma_tra_cuu = '';
    public int     $khoa_hoc_id = 0;

    public string  $ho_ten = '';
    public ?string $ngay_sinh = null;
    public ?string $gioi_tinh = null;
    public string  $cccd = '';
    public ?string $dien_thoai = null;
    public string  $email = '';
    public ?string $dia_chi = null;
    public ?string $don_vi_cong_tac = null;
    public ?string $chuc_vu = null;

    public ?string $cccd_file = null;
    public ?string $bang_cap_file = null;

    public ?string $ly_do_dang_ky = null;
    public int     $trang_thai = 0;     // 0 chờ duyệt, 1 đã duyệt, 2 từ chối
    public ?string $ly_do_xu_ly = null;
    public ?string $ngay_xu_ly = null;
    public ?int    $nguoi_xu_ly = null;

    public ?int    $hoc_vien_id = null;
    public ?int    $lop_hoc_id = null;

    public ?string $ip_dang_ky = null;

    public ?string $ngay_tao = null;
    public ?string $ngay_cap_nhat = null;
    public ?int    $nguoi_tao = null;
    public ?int    $nguoi_cap_nhat = null;
    public int     $da_xoa = 0;
}
