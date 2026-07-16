<?php
class DT_CmeLoai_PUBLIC
{
    public ?int $id = null;
    public int $nhom_id = 0;
    public string $ma_loai = '';
    public string $ten_loai = '';
    public string $kieu_quy_doi = 'co_dinh';   // theo_tiet | co_dinh | theo_nam
    public float $gia_tri_quy_doi = 1.0;
    public ?string $don_vi_tinh = null;
    public ?int $khoa_phong_id = null;
    public int $thu_tu = 0;
    public ?string $ngay_tao = null;
    public ?string $ngay_cap_nhat = null;
    public ?int $nguoi_tao = null;
    public ?int $nguoi_cap_nhat = null;
    public int $da_xoa = 0;
}
