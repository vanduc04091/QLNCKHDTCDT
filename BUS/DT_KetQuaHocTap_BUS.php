<?php
require_once __DIR__ . '/../DAL/DT_KetQuaHocTap_DAL.php';
require_once __DIR__ . '/../DAL/DM_NhatKyHeThong_DAL.php';

class DT_KetQuaHocTap_BUS
{
    const MODULE_KEY = 'DT_KetQuaHocTap';

    const W_TX = 0.2;
    const W_GK = 0.3;
    const W_CK = 0.5;

    public static function getByLop(int $lopId): array
    {
        return DT_KetQuaHocTap_DAL::getByLop($lopId);
    }

    public static function getMonHocByLop(int $lopId): array
    {
        return DT_KetQuaHocTap_DAL::getMonHocByLop($lopId);
    }

    /** Nhập 1 bản ghi điểm cho 1 học viên-môn */
    public static function saveOne(array $input, int $userId): array
    {
        $hvlId = (int)($input['hoc_vien_lop_id'] ?? 0);
        if ($hvlId <= 0) return ['success' => false, 'message' => 'Thiếu học viên'];
        $monId = !empty($input['mon_hoc_id']) ? (int)$input['mon_hoc_id'] : null;

        $dtx = self::parseDiem($input['diem_thuong_xuyen'] ?? null);
        $dgk = self::parseDiem($input['diem_giua_ky'] ?? null);
        $dck = self::parseDiem($input['diem_cuoi_ky'] ?? null);

        // Validate
        foreach (['TX'=>$dtx, 'GK'=>$dgk, 'CK'=>$dck] as $k => $v) {
            if ($v !== null && ($v < 0 || $v > 10)) {
                return ['success' => false, 'message' => "Điểm {$k} phải trong khoảng 0-10"];
            }
        }

        // Tính tổng kết
        $dtk = self::tinhTongKet($dtx, $dgk, $dck);
        $xl = $dtk !== null ? self::xepLoai($dtk) : null;
        $dat = $dtk !== null ? ($dtk >= 5.0 ? 1 : 0) : null;

        $e = new DT_KetQuaHocTap_PUBLIC();
        $e->hoc_vien_lop_id = $hvlId;
        $e->mon_hoc_id = $monId;
        $e->diem_thuong_xuyen = $dtx;
        $e->diem_giua_ky = $dgk;
        $e->diem_cuoi_ky = $dck;
        $e->diem_tong_ket = $dtk;
        $e->xep_loai = $xl;
        $e->dat = $dat;
        $e->nhan_xet = isset($input['nhan_xet']) ? trim($input['nhan_xet']) ?: null : null;

        $id = DT_KetQuaHocTap_DAL::upsert($e, $userId);
        return ['success' => true, 'message' => 'Đã lưu điểm', 'data' => [
            'id' => $id, 'diem_tong_ket' => $dtk, 'xep_loai' => $xl, 'dat' => $dat
        ]];
    }

    /** Lưu nhiều bản ghi cùng lúc (một ô hoặc cả bảng) */
    public static function saveBulk(array $rows, int $userId): array
    {
        if (!$rows) return ['success' => false, 'message' => 'Không có dữ liệu'];
        $count = 0; $lopId = 0;
        foreach ($rows as $r) {
            $res = self::saveOne($r, $userId);
            if ($res['success']) $count++;
            if (!empty($r['lop_hoc_id'])) $lopId = (int)$r['lop_hoc_id'];
        }
        if ($lopId > 0) DT_KetQuaHocTap_DAL::syncTongKetHvl($lopId, $userId);
        DM_NhatKyHeThong_DAL::log($userId, Constants::MODULE_HE_THONG,
            "Cập nhật {$count} dòng điểm cho lớp id={$lopId}", 'DT_KET_QUA_HOC_TAP', $lopId);
        return ['success' => true, 'message' => "Đã lưu {$count} dòng điểm"];
    }

    public static function recalc(int $lopId, int $userId): array
    {
        $n = DT_KetQuaHocTap_DAL::syncTongKetHvl($lopId, $userId);
        return ['success' => true, 'message' => "Đã tính lại tổng kết cho {$n} học viên"];
    }

    public static function delete(int $id): array
    {
        DT_KetQuaHocTap_DAL::delete($id);
        return ['success' => true, 'message' => 'Đã xóa bản ghi điểm'];
    }

    public static function statsByLop(int $lopId): array
    {
        return DT_KetQuaHocTap_DAL::statsByLop($lopId);
    }

    // ---------- Helpers ----------
    private static function parseDiem($v): ?float
    {
        if ($v === null || $v === '' || $v === false) return null;
        if (!is_numeric($v)) return null;
        return round((float)$v, 1);
    }

    private static function tinhTongKet(?float $tx, ?float $gk, ?float $ck): ?float
    {
        if ($tx === null && $gk === null && $ck === null) return null;
        // Nếu chỉ có 1-2 điểm: tính theo tỉ lệ các trọng số có mặt
        $sum = 0; $w = 0;
        if ($tx !== null) { $sum += $tx * self::W_TX; $w += self::W_TX; }
        if ($gk !== null) { $sum += $gk * self::W_GK; $w += self::W_GK; }
        if ($ck !== null) { $sum += $ck * self::W_CK; $w += self::W_CK; }
        if ($w <= 0) return null;
        return round($sum / $w, 1);
    }

    public static function xepLoai(float $d): string
    {
        if ($d >= 9.0) return 'Xuất sắc';
        if ($d >= 8.0) return 'Giỏi';
        if ($d >= 6.5) return 'Khá';
        if ($d >= 5.0) return 'Trung bình';
        if ($d >= 3.5) return 'Yếu';
        return 'Kém';
    }
}
