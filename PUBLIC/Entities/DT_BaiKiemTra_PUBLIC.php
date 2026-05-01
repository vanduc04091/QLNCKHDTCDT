<?php
class DT_BaiKiemTra_PUBLIC
{
    public ?int $id = null;
    public string $ma_bkt = '';
    public string $tieu_de = '';
    public ?string $mo_ta = null;
    public int $loai_bkt = 1;       // 1 thường xuyên, 2 giữa kỳ, 3 cuối kỳ, 4 ôn tập
    public ?int $lop_hoc_id = null;
    public ?int $mon_hoc_id = null;
    public ?string $ngay_kiem_tra = null;
    public ?int $thoi_gian_lam_bai = null;
    public ?string $de_file_name = null;
    public ?string $de_file_goc = null;
    public ?int $de_file_size = null;
    public ?string $dap_an_file_name = null;
    public ?string $dap_an_file_goc = null;
    public ?int $dap_an_file_size = null;
    public int $cong_khai_dap_an = 0;
    public int $trang_thai = 1;
    public ?string $ghi_chu = null;
    public ?string $ngay_tao = null;
    public ?string $ngay_cap_nhat = null;
    public ?int $nguoi_tao = null;
    public ?int $nguoi_cap_nhat = null;
    public int $da_xoa = 0;
}
