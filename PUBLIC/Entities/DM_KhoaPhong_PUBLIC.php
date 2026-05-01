<?php
class DM_KhoaPhong_PUBLIC
{
    public ?int $id = null;
    public string $ma_khoa = '';
    public string $ten_khoa = '';
    public string $loai_don_vi = 'Khoa';
    public ?int $truong_khoa_id = null;
    public ?string $dien_thoai = null;
    public ?string $email = null;
    public ?string $chuyen_khoa = null;
    public ?int $so_giuong = null;
    public ?int $trang_thai = 1;
    public ?string $ngay_tao = null;
    public ?string $ngay_cap_nhat = null;
    public ?int $nguoi_tao = null;
    public ?int $nguoi_cap_nhat = null;
    public int $da_xoa = 0;
}
