<?php
class DT_PhanCongGiangVien_PUBLIC
{
    public ?int $id = null;
    public int $giang_vien_id = 0;
    public int $lop_hoc_id = 0;
    public ?int $mon_hoc_id = null;
    public int $vai_tro = 1;            // 1 chính, 2 phụ, 3 trợ giảng
    public ?int $so_tiet_phan_cong = null;
    public ?string $tu_ngay = null;
    public ?string $den_ngay = null;
    public int $trang_thai = 0;         // 0 dự kiến, 1 đang dạy, 2 hoàn thành, 3 hủy
    public ?string $ghi_chu = null;
    public ?string $ngay_tao = null;
    public ?string $ngay_cap_nhat = null;
    public ?int $nguoi_tao = null;
    public ?int $nguoi_cap_nhat = null;
    public int $da_xoa = 0;
}
