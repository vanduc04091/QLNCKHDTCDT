<?php
require_once __DIR__ . '/../DAL/DM_PhanQuyen_DAL.php';
require_once __DIR__ . '/../DAL/DM_NhatKyHeThong_DAL.php';

class DM_PhanQuyen_BUS
{
    const MODULE_KEY = 'DM_PhanQuyen';

    public static function getMatrixByNhom(int $nhomId): array
    {
        if ($nhomId <= 0) return [];
        return DM_PhanQuyen_DAL::getMatrixByNhom($nhomId);
    }

    /**
     * Lưu ma trận phân quyền.
     * $permissions: [ form_id => ['xem'=>0/1,'them'=>...,'sua'=>...,'xoa'=>...] ]
     */
    public static function saveMatrix(int $nhomId, array $permissions, int $u): array
    {
        if ($nhomId <= 0) return ['success' => false, 'message' => 'Chưa chọn nhóm'];

        try {
            Database::beginTransaction();
            foreach ($permissions as $formId => $p) {
                $formId = (int)$formId;
                if ($formId <= 0) continue;
                $xem = !empty($p['xem']) ? 1 : 0;
                $them = !empty($p['them']) ? 1 : 0;
                $sua = !empty($p['sua']) ? 1 : 0;
                $xoa = !empty($p['xoa']) ? 1 : 0;
                $duyet = !empty($p['duyet']) ? 1 : 0;
                // Nếu có bất kỳ quyền thêm/sửa/xóa/duyệt thì mặc định phải có quyền xem
                if (($them || $sua || $xoa || $duyet) && !$xem) $xem = 1;
                DM_PhanQuyen_DAL::upsert($nhomId, $formId, $xem, $them, $sua, $xoa, $duyet, $u);
            }
            Database::commit();
            PhanQuyenHelper::clearCache($nhomId);
            DM_NhatKyHeThong_DAL::log($u, Constants::MODULE_HE_THONG, "Cập nhật phân quyền nhóm id={$nhomId}", 'DM_PHAN_QUYEN', $nhomId);
            return ['success' => true, 'message' => 'Lưu phân quyền thành công'];
        } catch (Throwable $ex) {
            Database::rollBack();
            return ['success' => false, 'message' => 'Lỗi: ' . $ex->getMessage()];
        }
    }

    public static function grantAllToNhom(int $nhomId, int $u): array
    {
        DM_PhanQuyen_DAL::grantAllToNhom($nhomId, $u);
        PhanQuyenHelper::clearCache($nhomId);
        return ['success' => true, 'message' => 'Đã cấp toàn quyền'];
    }
}
