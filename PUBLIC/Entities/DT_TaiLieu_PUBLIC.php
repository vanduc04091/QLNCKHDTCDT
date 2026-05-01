<?php
class DT_TaiLieu_PUBLIC
{
    public ?int $id = null;
    public string $ma_tai_lieu = '';
    public string $tieu_de = '';
    public ?string $mo_ta = null;
    public int $loai_tai_lieu = 1;   // 1 giáo trình, 2 bài giảng, 3 tham khảo, 4 đề thi/bài tập, 5 video, 6 khác
    public ?string $dinh_dang = null;
    public ?string $file_name = null;
    public ?string $file_goc = null;
    public ?int $file_size = null;
    public ?string $link_ngoai = null;
    public ?string $tac_gia = null;
    public ?int $nam_xuat_ban = null;
    public ?string $nha_xuat_ban = null;
    public ?int $khoa_hoc_id = null;
    public ?int $lop_hoc_id = null;
    public ?int $mon_hoc_id = null;
    public int $cong_khai = 0;
    public int $bat_buoc = 0;
    public int $luot_tai = 0;
    public int $luot_xem = 0;
    public int $trang_thai = 1;
    public ?string $ghi_chu = null;
    public ?string $ngay_tao = null;
    public ?string $ngay_cap_nhat = null;
    public ?int $nguoi_tao = null;
    public ?int $nguoi_cap_nhat = null;
    public int $da_xoa = 0;
}
