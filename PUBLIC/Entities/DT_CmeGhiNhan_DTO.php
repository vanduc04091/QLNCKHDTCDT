<?php
require_once __DIR__ . '/DT_CmeGhiNhan_PUBLIC.php';

class DT_CmeGhiNhan_DTO extends DT_CmeGhiNhan_PUBLIC
{
    public ?string $ma_nv = null;
    public ?string $ho_ten_nhan_vien = null;
    public ?string $ten_khoa_phong = null;
    public ?string $ma_loai = null;
    public ?string $ten_loai = null;
    public ?string $kieu_quy_doi = null;
    public ?string $don_vi_tinh = null;
    public ?int $nhom_id = null;
    public ?string $ten_nhom = null;
    public ?string $tai_khoan_nguoi_tao = null;
    public ?string $tai_khoan_nguoi_cap_nhat = null;
}
