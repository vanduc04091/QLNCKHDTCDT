<?php
class DM_GiangVien_PUBLIC
{
    public ?int $id = null;
    public string $ma_gv = '';
    public string $ho_ten = '';
    public ?string $ngay_sinh = null;
    public ?string $gioi_tinh = null;
    public ?string $email = null;
    public ?string $dien_thoai = null;
    public ?string $avatar = null;
    public ?string $hoc_vi = null;
    public ?string $hoc_ham = null;
    public ?string $chuyen_mon = null;
    public ?int $nhan_vien_id = null;
    public ?string $don_vi_cong_tac = null;
    public int $loai_gv = 1;        // 1 cơ hữu, 2 thỉnh giảng, 3 khách mời
    public int $trang_thai = 1;     // 1 hoạt động, 0 không hoạt động
    public ?string $ghi_chu = null;
    public ?string $ngay_tao = null;
    public ?string $ngay_cap_nhat = null;
    public ?int $nguoi_tao = null;
    public ?int $nguoi_cap_nhat = null;
    public int $da_xoa = 0;
}
