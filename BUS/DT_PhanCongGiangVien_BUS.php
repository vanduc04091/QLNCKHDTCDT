<?php
require_once __DIR__ . '/../DAL/DT_PhanCongGiangVien_DAL.php';
require_once __DIR__ . '/../DAL/DM_NhatKyHeThong_DAL.php';

class DT_PhanCongGiangVien_BUS
{
    const MODULE_KEY = 'DT_PhanCongGiangVien';

    public static function insert(DT_PhanCongGiangVien_PUBLIC $e, bool $forceConflict = false): array
    {
        $v = self::validate($e);
        if (!$v['success']) return $v;

        if (!$forceConflict && (int)$e->vai_tro === 1) {
            $cf = DT_PhanCongGiangVien_DAL::findConflicts($e->giang_vien_id, $e->lop_hoc_id, $e->mon_hoc_id, 1, null);
            if ($cf) {
                return [
                    'success' => false,
                    'message' => 'Đã có giảng viên khác giữ vai trò CHÍNH cho lớp/môn này.',
                    'data' => ['conflicts' => self::formatConflicts($cf)],
                ];
            }
        }

        $id = DT_PhanCongGiangVien_DAL::insert($e);
        DM_NhatKyHeThong_DAL::log($e->nguoi_tao ?? 0, Constants::MODULE_HE_THONG,
            "Phân công GV id={$e->giang_vien_id} cho lớp id={$e->lop_hoc_id}",
            'DT_PHAN_CONG_GIANG_VIEN', $id);
        return ['success' => true, 'message' => 'Phân công thành công', 'data' => ['id' => $id]];
    }

    public static function update(DT_PhanCongGiangVien_PUBLIC $e, bool $forceConflict = false): array
    {
        if (!$e->id) return ['success' => false, 'message' => 'Thiếu ID'];
        $v = self::validate($e);
        if (!$v['success']) return $v;

        if (!$forceConflict && (int)$e->vai_tro === 1) {
            $cf = DT_PhanCongGiangVien_DAL::findConflicts($e->giang_vien_id, $e->lop_hoc_id, $e->mon_hoc_id, 1, $e->id);
            if ($cf) {
                return [
                    'success' => false,
                    'message' => 'Đã có giảng viên khác giữ vai trò CHÍNH cho lớp/môn này.',
                    'data' => ['conflicts' => self::formatConflicts($cf)],
                ];
            }
        }

        DT_PhanCongGiangVien_DAL::update($e);
        return ['success' => true, 'message' => 'Cập nhật phân công thành công'];
    }

    public static function delete(int $id): array
    {
        DT_PhanCongGiangVien_DAL::delete($id);
        return ['success' => true, 'message' => 'Đã xóa phân công'];
    }

    public static function getById(int $id): ?DT_PhanCongGiangVien_DTO { return DT_PhanCongGiangVien_DAL::getById($id); }
    public static function getList(array $opts = []): array { return DT_PhanCongGiangVien_DAL::getList($opts); }
    public static function getStats(): array { return DT_PhanCongGiangVien_DAL::getStats(); }

    /** Bulk: phân công nhiều môn cho 1 GV cùng lớp */
    public static function bulkAssign(int $gvId, int $lopId, array $monIds, int $vaiTro, int $userId): array
    {
        if ($gvId <= 0 || $lopId <= 0) return ['success' => false, 'message' => 'Thiếu giảng viên hoặc lớp'];
        if (!$monIds) return ['success' => false, 'message' => 'Chưa chọn môn nào'];
        if ($vaiTro < 1 || $vaiTro > 3) return ['success' => false, 'message' => 'Vai trò không hợp lệ'];

        $created = 0; $skipped = 0; $conflicts = [];
        foreach ($monIds as $monId) {
            $monId = (int)$monId;
            if ($vaiTro === 1) {
                $cf = DT_PhanCongGiangVien_DAL::findConflicts($gvId, $lopId, $monId ?: null, 1, null);
                if ($cf) { $skipped++; $conflicts[] = $monId; continue; }
            }
            $e = new DT_PhanCongGiangVien_PUBLIC();
            $e->giang_vien_id = $gvId;
            $e->lop_hoc_id = $lopId;
            $e->mon_hoc_id = $monId ?: null;
            $e->vai_tro = $vaiTro;
            $e->trang_thai = 0;
            $e->nguoi_tao = $userId;
            try {
                DT_PhanCongGiangVien_DAL::insert($e);
                $created++;
            } catch (PDOException $ex) {
                // Trùng UNIQUE: bỏ qua
                $skipped++;
            }
        }
        $msg = "Đã phân công {$created} môn";
        if ($skipped > 0) $msg .= " (bỏ qua {$skipped} do trùng)";
        return ['success' => true, 'message' => $msg, 'data' => ['created' => $created, 'skipped' => $skipped, 'conflicts' => $conflicts]];
    }

    private static function validate(DT_PhanCongGiangVien_PUBLIC $e): array
    {
        if ($e->giang_vien_id <= 0) return ['success' => false, 'message' => 'Chưa chọn giảng viên'];
        if ($e->lop_hoc_id <= 0) return ['success' => false, 'message' => 'Chưa chọn lớp'];
        if ($e->vai_tro < 1 || $e->vai_tro > 3) return ['success' => false, 'message' => 'Vai trò không hợp lệ'];
        if ($e->trang_thai < 0 || $e->trang_thai > 3) return ['success' => false, 'message' => 'Trạng thái không hợp lệ'];
        if ($e->tu_ngay && $e->den_ngay && $e->tu_ngay > $e->den_ngay) {
            return ['success' => false, 'message' => 'Từ ngày phải trước Đến ngày'];
        }
        if ($e->so_tiet_phan_cong !== null && $e->so_tiet_phan_cong < 0) {
            return ['success' => false, 'message' => 'Số tiết không hợp lệ'];
        }
        return ['success' => true];
    }

    private static function formatConflicts(array $rows): array
    {
        $out = [];
        foreach ($rows as $r) {
            $out[] = [
                'id' => $r['id'],
                'ma_gv' => $r['ma_gv'],
                'ho_ten_gv' => $r['ho_ten_gv'],
                'ma_lop' => $r['ma_lop'],
                'ten_lop' => $r['ten_lop'],
                'ten_mon_hoc' => $r['ten_mon_hoc'],
                'vai_tro' => $r['vai_tro'],
            ];
        }
        return $out;
    }
}
