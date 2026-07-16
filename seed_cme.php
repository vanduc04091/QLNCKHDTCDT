<?php
/**
 * seed_cme.php — Nạp danh mục quy đổi CME (5 nhóm + ~20 loại) + cấu hình ngưỡng.
 * Chạy 1 lần sau khi đã tạo bảng: php seed_cme.php
 * An toàn chạy lại: dùng ma_nhom / ma_loai làm khóa, bỏ qua nếu đã có.
 */
require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/DAL/DM_CauHinh_DAL.php';

$db = Database::getConnection();

function nhomId(PDO $db, string $ma, string $ten, int $thuTu): int
{
    $st = $db->prepare("SELECT id FROM DT_CME_NHOM WHERE ma_nhom=:m AND da_xoa=0");
    $st->execute([':m' => $ma]);
    $id = $st->fetchColumn();
    if ($id) return (int)$id;
    $st = $db->prepare("INSERT INTO DT_CME_NHOM (ma_nhom, ten_nhom, thu_tu, ngay_tao, ngay_cap_nhat, nguoi_tao, da_xoa)
                        VALUES (:m, :t, :tt, NOW(), NOW(), 1, 0)");
    $st->execute([':m' => $ma, ':t' => $ten, ':tt' => $thuTu]);
    return (int)$db->lastInsertId();
}

function loai(PDO $db, int $nhomId, string $ma, string $ten, string $kieu, float $gt, string $dv, int $thuTu): void
{
    $st = $db->prepare("SELECT id FROM DT_CME_LOAI WHERE ma_loai=:m AND da_xoa=0");
    $st->execute([':m' => $ma]);
    if ($st->fetchColumn()) { echo "  · bỏ qua (đã có): $ma\n"; return; }
    $st = $db->prepare("INSERT INTO DT_CME_LOAI (nhom_id, ma_loai, ten_loai, kieu_quy_doi, gia_tri_quy_doi, don_vi_tinh, thu_tu, ngay_tao, ngay_cap_nhat, nguoi_tao, da_xoa)
                        VALUES (:n, :m, :t, :k, :g, :d, :tt, NOW(), NOW(), 1, 0)");
    $st->execute([':n' => $nhomId, ':m' => $ma, ':t' => $ten, ':k' => $kieu, ':g' => $gt, ':d' => $dv, ':tt' => $thuTu]);
    echo "  + $ma ($kieu, $gt/$dv)\n";
}

echo "== Nạp nhóm ==\n";
$n1 = nhomId($db, 'CME_DAOTAO', 'Khóa đào tạo, bồi dưỡng ngắn hạn, hội nghị, hội thảo về y khoa', 1);
$n2 = nhomId($db, 'CME_QUYTRINH', 'Tham gia soạn thảo quy trình chuyên môn', 2);
$n3 = nhomId($db, 'CME_VBPL', 'Tham gia soạn thảo văn bản quy phạm pháp luật ban hành quy trình chuyên môn', 3);
$n4 = nhomId($db, 'CME_NCKH', 'Nghiên cứu khoa học, giảng dạy về y khoa', 4);
$n5 = nhomId($db, 'CME_KHAC', 'Tự cập nhật kiến thức y khoa và các hình thức khác', 5);
echo "  nhóm: $n1 $n2 $n3 $n4 $n5\n";

echo "== Nạp loại ==\n";
// Nhóm 1
loai($db, $n1, 'DT_KHOAHOC',       'Khóa đào tạo, bồi dưỡng ngắn hạn (học viên/giảng viên)', 'theo_tiet', 1.0, 'tiết', 1);
loai($db, $n1, 'HT_CHUTRI',        'Hội nghị, hội thảo — Chủ trì', 'co_dinh', 2.0, 'buổi', 2);
loai($db, $n1, 'HT_BAOCAOVIEN',    'Hội nghị, hội thảo — Báo cáo viên (kể cả chuẩn bị báo cáo)', 'co_dinh', 2.0, 'báo cáo', 3);
loai($db, $n1, 'HT_DAIBIEU',       'Hội nghị, hội thảo — Đại biểu', 'co_dinh', 1.5, 'buổi', 4);
// Nhóm 2
loai($db, $n2, 'QT_TRUONGPHO',     'Soạn thảo quy trình — Trưởng/Phó ban hoặc tổ soạn thảo', 'co_dinh', 5.0, 'tài liệu', 1);
loai($db, $n2, 'QT_THANHVIEN',     'Soạn thảo quy trình — Thành viên ban soạn thảo/biên tập', 'co_dinh', 2.0, 'tài liệu', 2);
// Nhóm 3
loai($db, $n3, 'VBPL_TRUONGPHO',   'Soạn thảo VBQPPL — Trưởng/Phó ban hoặc tổ soạn thảo', 'co_dinh', 5.0, 'văn bản', 1);
loai($db, $n3, 'VBPL_THANHVIEN',   'Soạn thảo VBQPPL — Thành viên ban soạn thảo/biên tập', 'co_dinh', 3.0, 'văn bản', 2);
// Nhóm 4
loai($db, $n4, 'NCKH_CHUTRI_CAOCAP','Nhiệm vụ KH&CN/sáng kiến cấp Nhà nước/Bộ/Tỉnh — Chủ trì/thư ký', 'co_dinh', 12.0, 'nhiệm vụ', 1);
loai($db, $n4, 'NCKH_CHUTRI_COSO', 'Nhiệm vụ KH&CN/sáng kiến cấp cơ sở — Chủ trì/thư ký', 'co_dinh', 8.0, 'nhiệm vụ', 2);
loai($db, $n4, 'NCKH_THANHVIEN',   'Nhiệm vụ KH&CN/sáng kiến các cấp — Thành viên', 'co_dinh', 4.0, 'nhiệm vụ', 3);
loai($db, $n4, 'BB_QT_C1',         'Bài báo khoa học quốc tế — Tác giả chính/chịu trách nhiệm', 'co_dinh', 8.0, 'bài', 4);
loai($db, $n4, 'BB_QT_C2',         'Bài báo khoa học quốc tế — Tác giả thứ hai trở đi', 'co_dinh', 2.0, 'bài', 5);
loai($db, $n4, 'BB_TN_C1',         'Bài báo khoa học trong nước — Tác giả chính/chịu trách nhiệm', 'co_dinh', 4.0, 'bài', 6);
loai($db, $n4, 'BB_TN_C2',         'Bài báo khoa học trong nước — Tác giả thứ hai trở đi', 'co_dinh', 1.0, 'bài', 7);
loai($db, $n4, 'GIANGDAY',         'Giảng dạy về y khoa (kể cả chuẩn bị bài giảng)', 'theo_tiet', 1.0, 'tiết', 8);
// Nhóm 5
loai($db, $n5, 'HD_LUANVAN',       'Hướng dẫn luận văn', 'co_dinh', 4.0, 'luận văn', 1);
loai($db, $n5, 'HD_LUANAN',        'Hướng dẫn luận án', 'theo_nam', 4.0, 'năm', 2);
loai($db, $n5, 'HDONG_CHUTICH',    'Tham gia hội đồng — Chủ tịch', 'co_dinh', 3.0, 'hội đồng', 3);
loai($db, $n5, 'HDONG_THUKY',      'Tham gia hội đồng — Thư ký/phản biện', 'co_dinh', 2.0, 'hội đồng', 4);
loai($db, $n5, 'HDONG_THANHVIEN',  'Tham gia hội đồng — Thành viên', 'co_dinh', 1.0, 'hội đồng', 5);
loai($db, $n5, 'SHCM_CABENH',      'Sinh hoạt chuyên môn — Hội chẩn/phân tích ca bệnh', 'co_dinh', 1.0, 'ca bệnh', 6);
loai($db, $n5, 'HOC_VANBANG',      'Đang học khóa đào tạo cấp văn bằng (trong/ngoài nước)', 'theo_nam', 24.0, 'năm', 7);
loai($db, $n5, 'HOC_CHUNGCHI',     'Đang học khóa đào tạo cấp chứng chỉ chuyên khoa/kỹ thuật', 'theo_tiet', 1.0, 'tiết', 8);

echo "== Cấu hình ngưỡng ==\n";
if (DM_CauHinh_DAL::get('CME_NGUONG_GIO') === null) {
    DM_CauHinh_DAL::set('CME_NGUONG_GIO', '24', 'CME: số giờ tín chỉ tối thiểu mỗi chu kỳ');
    echo "  + CME_NGUONG_GIO = 24\n";
} else echo "  · CME_NGUONG_GIO đã có\n";
if (DM_CauHinh_DAL::get('CME_CHU_KY_NAM') === null) {
    DM_CauHinh_DAL::set('CME_CHU_KY_NAM', '1', 'CME: số năm 1 chu kỳ tính ngưỡng');
    echo "  + CME_CHU_KY_NAM = 1\n";
} else echo "  · CME_CHU_KY_NAM đã có\n";

echo "\n== XONG ==\n";
$tongNhom = (int)$db->query("SELECT COUNT(*) FROM DT_CME_NHOM WHERE da_xoa=0")->fetchColumn();
$tongLoai = (int)$db->query("SELECT COUNT(*) FROM DT_CME_LOAI WHERE da_xoa=0")->fetchColumn();
echo "Nhóm: $tongNhom | Loại: $tongLoai\n";
