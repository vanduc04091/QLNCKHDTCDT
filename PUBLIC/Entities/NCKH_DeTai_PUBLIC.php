<?php
class NCKH_DeTai_PUBLIC
{
    public ?int    $id = null;
    public string  $ma_de_tai = '';
    public string  $ten_de_tai = '';
    public int     $nam = 0;
    public int     $cap_do_id = 0;
    public int     $the_loai_id = 0;
    public ?int    $khoa_phong_id = null;
    public int     $chu_nhiem_id = 0;
    public ?int    $thu_ky_id = null;

    public ?string $muc_tieu = null;
    public ?string $tom_tat = null;
    public ?string $tu_khoa = null;

    public ?string $ngay_bat_dau = null;
    public ?string $ngay_ket_thuc_du_kien = null;
    public ?string $ngay_nghiem_thu = null;

    public ?float  $kinh_phi_du_toan = null;
    public ?float  $kinh_phi_thuc_te = null;
    public ?string $nguon_kinh_phi = null;

    public ?string $quyet_dinh_phe_duyet = null;
    public ?string $ngay_quyet_dinh = null;

    public ?string $ket_qua_xep_loai = null; // XuatSac/Gioi/Kha/TrungBinhKha/Dat/KhongDat
    public ?float  $diem_so = null;
    public ?string $noi_dung_ung_dung = null;

    // Lịch bảo vệ đề cương
    public ?string $phien_bao_ve = null;
    public ?string $dia_diem_bao_ve = null;
    public ?string $ngay_bao_ve = null;

    // QĐ công nhận / nghiệm thu
    public ?string $quyet_dinh_cong_nhan = null;
    public ?string $ngay_quyet_dinh_cong_nhan = null;

    // Tên khoa nguyên gốc khi không match được FK
    public ?string $ten_khoa_text = null;

    // Bài báo
    public ?string $ten_tap_chi = null;
    public ?string $so_tap_chi = null;
    public ?int    $nam_xuat_ban = null;
    public ?string $issn_doi = null;
    public ?string $link_bai_bao = null;

    public int     $trang_thai = 0;  // 0=DeXuat,1=DangThucHien,2=HoanThanh,3=TamDung,4=Huy

    // Workflow gửi duyệt
    public string  $trang_thai_duyet = 'Nhap'; // Nhap/ChoDuyet/DaDuyet/TuChoi
    public ?string $ngay_gui_duyet = null;
    public ?string $ngay_xu_ly_duyet = null;
    public ?int    $nguoi_xu_ly_duyet = null;
    public ?string $ly_do_tu_choi = null;

    public ?string $ngay_tao = null;
    public ?string $ngay_cap_nhat = null;
    public ?int    $nguoi_tao = null;
    public ?int    $nguoi_cap_nhat = null;
    public int     $da_xoa = 0;
}
