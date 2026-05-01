<?php
require_once __DIR__ . '/DT_DiemDanh_PUBLIC.php';

class DT_DiemDanh_DTO extends DT_DiemDanh_PUBLIC
{
    // Học viên
    public ?int $hoc_vien_id = null;
    public ?string $ma_hv = null;
    public ?string $ho_ten = null;
    public ?string $avatar = null;
    public ?int $la_nhan_vien = 0;
    public ?string $ma_nv = null;
    public ?string $ten_doi_tuong = null;
    // Lớp
    public ?int $lop_hoc_id = null;
    public ?string $ma_lop = null;
    public ?string $ten_lop = null;
    // Lịch
    public ?string $tieu_de_buoi = null;
    public ?string $ngay_hoc = null;
    public ?string $gio_bat_dau_buoi = null;
    public ?string $gio_ket_thuc_buoi = null;
    public ?int $buoi_thu = null;
}
