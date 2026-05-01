<?php
require_once __DIR__ . '/../DAL/NCKH_TaiLieu_DAL.php';
require_once __DIR__ . '/../DAL/DM_NhatKyHeThong_DAL.php';

class NCKH_TaiLieu_BUS
{
    const MODULE_KEY = 'NCKH_TaiLieu';
    const ALLOWED_EXT = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'jpg', 'jpeg', 'png', 'zip', 'rar', '7z'];
    const MAX_SIZE = 20971520; // 20MB

    public static function insert(NCKH_TaiLieu_PUBLIC $e): array
    {
        if ($e->de_tai_id <= 0) return ['success' => false, 'message' => 'Thiếu đề tài'];
        if (trim($e->ten_tai_lieu) === '') return ['success' => false, 'message' => 'Tên tài liệu không được để trống'];
        $id = NCKH_TaiLieu_DAL::insert($e);
        DM_NhatKyHeThong_DAL::log($e->nguoi_tao ?? 0, self::MODULE_KEY, "Thêm tài liệu: {$e->ten_tai_lieu}", 'NCKH_TAI_LIEU', $id);
        return ['success' => true, 'message' => 'Đã thêm tài liệu', 'data' => ['id' => $id]];
    }

    public static function update(NCKH_TaiLieu_PUBLIC $e): array
    {
        if (!$e->id) return ['success' => false, 'message' => 'Thiếu ID'];
        NCKH_TaiLieu_DAL::update($e);
        return ['success' => true, 'message' => 'Cập nhật thành công'];
    }

    /** Xóa mềm + xóa file vật lý */
    public static function trash(int $id, int $u, string $uploadDir): array
    {
        $tl = NCKH_TaiLieu_DAL::getById($id);
        if ($tl && $tl->ten_file_luu) {
            $path = rtrim($uploadDir, '/\\') . '/' . $tl->ten_file_luu;
            if (file_exists($path)) @unlink($path);
        }
        NCKH_TaiLieu_DAL::trash($id, $u);
        return ['success' => true, 'message' => 'Đã xóa tài liệu'];
    }

    public static function getById(int $id): ?NCKH_TaiLieu_DTO { return NCKH_TaiLieu_DAL::getById($id); }
    public static function getByDeTai(int $deTaiId, string $loai = ''): array { return NCKH_TaiLieu_DAL::getByDeTai($deTaiId, $loai); }

    public static function loaiText(string $code): string
    {
        return [
            'DeCuong'    => 'Đề cương',
            'QuyetDinh'  => 'Quyết định',
            'BienBan'    => 'Biên bản',
            'BaoCao'     => 'Báo cáo',
            'FileGoc'    => 'File gốc',
            'Khac'       => 'Khác',
        ][$code] ?? $code;
    }
}
