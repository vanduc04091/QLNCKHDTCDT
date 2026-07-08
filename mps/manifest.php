<?php
/**
 * Danh sách mẫu phiếu in cho học viên.
 * Mỗi mẫu: key (id), ten (hiển thị), file (HTML template trong folder mps/), mo_ta.
 * Thêm mẫu mới: tạo file .html trong mps/ với placeholder {{key}} rồi khai báo 1 dòng ở đây.
 */
return [
    [
        'key'   => 'phieu_nhan_xet_thuc_hanh',
        'ten'   => 'Phiếu nhận xét quá trình thực hành khám bệnh, chữa bệnh',
        'file'  => 'phieu_nhan_xet_thuc_hanh.html',
        'mo_ta' => 'Mẫu PNXTH — điền thông tin người thực hành từ hồ sơ học viên.',
    ],
    [
        'key'   => 'the_hoc_vien',
        'ten'   => 'Thẻ học viên',
        'file'  => 'the_hoc_vien.html',
        'mo_ta' => 'Thẻ học viên khổ ngang, có ảnh + mã HV. In rồi cắt theo viền.',
    ],
];
