<?php
require_once __DIR__ . '/../DAL/DT_LichHoc_DAL.php';
require_once __DIR__ . '/../DAL/DT_LopHoc_DAL.php';
require_once __DIR__ . '/../DAL/DM_NhatKyHeThong_DAL.php';

class DT_LichHoc_BUS
{
    const MODULE_KEY = 'DT_LichHoc';

    public static function insert(DT_LichHoc_PUBLIC $e, bool $forceConflict = false): array
    {
        $v = self::validate($e);
        if (!$v['success']) return $v;

        if (!$forceConflict) {
            $conflicts = DT_LichHoc_DAL::findConflicts(
                $e->ngay_hoc, $e->gio_bat_dau, $e->gio_ket_thuc,
                $e->phong_hoc, $e->giang_vien_id, null
            );
            if ($conflicts) {
                return [
                    'success' => false,
                    'message' => 'Trùng lịch với ' . count($conflicts) . ' buổi khác (phòng hoặc giảng viên).',
                    'data' => ['conflicts' => self::formatConflicts($conflicts)]
                ];
            }
        }

        if ($e->buoi_thu <= 0) {
            $e->buoi_thu = DT_LichHoc_DAL::getMaxBuoi($e->lop_hoc_id) + 1;
        }
        $id = DT_LichHoc_DAL::insert($e);
        MemcachedHelper::deleteByPrefix('dt_lich_hoc:');
        DM_NhatKyHeThong_DAL::log($e->nguoi_tao ?? 0, Constants::MODULE_HE_THONG,
            "Thêm buổi học: {$e->tieu_de} ({$e->ngay_hoc})", 'DT_LICH_HOC', $id);
        return ['success' => true, 'message' => 'Tạo buổi học thành công', 'data' => ['id' => $id]];
    }

    public static function update(DT_LichHoc_PUBLIC $e, bool $forceConflict = false): array
    {
        if (!$e->id) return ['success' => false, 'message' => 'Thiếu ID'];
        $v = self::validate($e);
        if (!$v['success']) return $v;

        if (!$forceConflict) {
            $conflicts = DT_LichHoc_DAL::findConflicts(
                $e->ngay_hoc, $e->gio_bat_dau, $e->gio_ket_thuc,
                $e->phong_hoc, $e->giang_vien_id, $e->id
            );
            if ($conflicts) {
                return [
                    'success' => false,
                    'message' => 'Trùng lịch với ' . count($conflicts) . ' buổi khác (phòng hoặc giảng viên).',
                    'data' => ['conflicts' => self::formatConflicts($conflicts)]
                ];
            }
        }

        DT_LichHoc_DAL::update($e);
        MemcachedHelper::deleteByPrefix('dt_lich_hoc:');
        return ['success' => true, 'message' => 'Cập nhật buổi học thành công'];
    }

    public static function updateTrangThai(int $id, int $tt, int $u): array
    {
        if ($tt < 0 || $tt > 3) return ['success' => false, 'message' => 'Trạng thái không hợp lệ'];
        DT_LichHoc_DAL::updateTrangThai($id, $tt, $u);
        MemcachedHelper::deleteByPrefix('dt_lich_hoc:');
        return ['success' => true, 'message' => 'Đã cập nhật trạng thái'];
    }

    public static function trash(int $id, int $u): array
    {
        DT_LichHoc_DAL::trash($id, $u);
        MemcachedHelper::deleteByPrefix('dt_lich_hoc:');
        DM_NhatKyHeThong_DAL::log($u, Constants::MODULE_HE_THONG, "Xóa tạm buổi học id={$id}", 'DT_LICH_HOC', $id);
        return ['success' => true, 'message' => 'Đã chuyển vào thùng rác'];
    }

    public static function restore(int $id, int $u): array
    {
        DT_LichHoc_DAL::restore($id, $u);
        MemcachedHelper::deleteByPrefix('dt_lich_hoc:');
        return ['success' => true, 'message' => 'Đã khôi phục'];
    }

    public static function delete(int $id): array
    {
        try {
            DT_LichHoc_DAL::delete($id);
            MemcachedHelper::deleteByPrefix('dt_lich_hoc:');
            return ['success' => true, 'message' => 'Đã xóa vĩnh viễn'];
        } catch (Throwable $ex) {
            return ['success' => false, 'message' => 'Không thể xóa: dữ liệu đang được tham chiếu'];
        }
    }

    /**
     * Tạo lịch hàng loạt theo quy tắc lặp.
     * @param array $tpl cấu trúc buổi mẫu (tieu_de, gio_bat_dau, gio_ket_thuc, phong_hoc, giang_vien_id, giang_vien_ngoai, mon_hoc_id, noi_dung)
     * @param string $from ngày bắt đầu (Y-m-d)
     * @param string $to ngày kết thúc (Y-m-d)
     * @param string $pattern 'daily' | 'weekly' | 'custom'
     * @param array $weekdays danh sách thứ trong tuần (0=CN..6=T7) nếu pattern=custom
     * @param int $maxBuoi tối đa số buổi sinh ra (an toàn: 60)
     */
    public static function bulkGenerate(
        int $lopId, array $tpl, string $from, string $to,
        string $pattern, array $weekdays, int $userId,
        bool $forceConflict = false
    ): array {
        if ($lopId <= 0) return ['success' => false, 'message' => 'Chưa chọn lớp học'];
        $lop = DT_LopHoc_DAL::getById($lopId);
        if (!$lop) return ['success' => false, 'message' => 'Lớp học không tồn tại'];

        $tpl['tieu_de'] = trim($tpl['tieu_de'] ?? '') ?: $lop->ten_lop;
        if (empty($tpl['gio_bat_dau']) || empty($tpl['gio_ket_thuc'])) {
            return ['success' => false, 'message' => 'Thiếu giờ bắt đầu/kết thúc'];
        }
        if (strtotime($tpl['gio_bat_dau']) >= strtotime($tpl['gio_ket_thuc'])) {
            return ['success' => false, 'message' => 'Giờ bắt đầu phải trước giờ kết thúc'];
        }

        try {
            $start = new DateTime($from);
            $end = new DateTime($to);
        } catch (Throwable $ex) {
            return ['success' => false, 'message' => 'Ngày không hợp lệ'];
        }
        if ($start > $end) return ['success' => false, 'message' => 'Từ ngày phải trước Đến ngày'];

        $dates = [];
        $maxBuoi = 60;
        for ($d = clone $start; $d <= $end && count($dates) < $maxBuoi; $d->modify('+1 day')) {
            $dow = (int)$d->format('w'); // 0=CN
            $match = false;
            if ($pattern === 'daily') $match = true;
            elseif ($pattern === 'weekly') $match = in_array($dow, $weekdays ?: [(int)$start->format('w')], true);
            elseif ($pattern === 'custom') $match = in_array($dow, $weekdays, true);
            if ($match) $dates[] = $d->format('Y-m-d');
        }
        if (!$dates) return ['success' => false, 'message' => 'Không có ngày nào phù hợp với quy tắc'];

        $startBuoi = DT_LichHoc_DAL::getMaxBuoi($lopId);
        $conflictSummary = [];
        $created = 0; $skipped = 0;

        try {
            Database::beginTransaction();
            foreach ($dates as $i => $ngay) {
                if (!$forceConflict) {
                    $cf = DT_LichHoc_DAL::findConflicts(
                        $ngay, $tpl['gio_bat_dau'], $tpl['gio_ket_thuc'],
                        $tpl['phong_hoc'] ?? null, !empty($tpl['giang_vien_id']) ? (int)$tpl['giang_vien_id'] : null,
                        null
                    );
                    if ($cf) { $skipped++; $conflictSummary[$ngay] = count($cf); continue; }
                }
                $e = new DT_LichHoc_PUBLIC();
                $e->lop_hoc_id = $lopId;
                $e->buoi_thu = $startBuoi + $i + 1;
                $e->tieu_de = $tpl['tieu_de'];
                $e->noi_dung = $tpl['noi_dung'] ?? null;
                $e->mon_hoc_id = !empty($tpl['mon_hoc_id']) ? (int)$tpl['mon_hoc_id'] : null;
                $e->ngay_hoc = $ngay;
                $e->gio_bat_dau = $tpl['gio_bat_dau'];
                $e->gio_ket_thuc = $tpl['gio_ket_thuc'];
                $e->phong_hoc = $tpl['phong_hoc'] ?? null;
                $e->giang_vien_id = !empty($tpl['giang_vien_id']) ? (int)$tpl['giang_vien_id'] : null;
                $e->giang_vien_ngoai = $tpl['giang_vien_ngoai'] ?? null;
                $e->trang_thai = 0;
                $e->nguoi_tao = $userId;
                DT_LichHoc_DAL::insert($e);
                $created++;
            }
            Database::commit();
        } catch (Throwable $ex) {
            Database::rollBack();
            return ['success' => false, 'message' => 'Lỗi tạo lịch hàng loạt: ' . $ex->getMessage()];
        }
        MemcachedHelper::deleteByPrefix('dt_lich_hoc:');
        DM_NhatKyHeThong_DAL::log($userId, Constants::MODULE_HE_THONG,
            "Tạo hàng loạt {$created} buổi cho lớp id={$lopId}", 'DT_LICH_HOC', $lopId);

        $msg = "Đã tạo {$created} buổi";
        if ($skipped > 0) $msg .= " ({$skipped} bỏ qua do trùng lịch)";
        return ['success' => true, 'message' => $msg, 'data' => ['created' => $created, 'skipped' => $skipped, 'conflicts' => $conflictSummary]];
    }

    public static function getById(int $id): ?DT_LichHoc_DTO { return DT_LichHoc_DAL::getById($id); }
    public static function getByRange(string $from, string $to, array $opts = []): array
    {
        return DT_LichHoc_DAL::getByRange($from, $to, $opts);
    }
    public static function getPaged(int $p, int $s, array $opts = [], int $dx = 0): array
    {
        return DT_LichHoc_DAL::getPaged($p, $s, $opts, $dx);
    }
    public static function getStats(string $from, string $to): array
    {
        return DT_LichHoc_DAL::getStats($from, $to);
    }

    private static function validate(DT_LichHoc_PUBLIC $e): array
    {
        $e->tieu_de = trim($e->tieu_de);
        if ($e->lop_hoc_id <= 0) return ['success' => false, 'message' => 'Chưa chọn lớp học'];
        if ($e->tieu_de === '') return ['success' => false, 'message' => 'Tiêu đề không được để trống'];
        if (!$e->ngay_hoc || !strtotime($e->ngay_hoc)) return ['success' => false, 'message' => 'Ngày học không hợp lệ'];
        if (!$e->gio_bat_dau || !$e->gio_ket_thuc) return ['success' => false, 'message' => 'Thiếu giờ bắt đầu / kết thúc'];
        if (strtotime($e->gio_bat_dau) >= strtotime($e->gio_ket_thuc)) {
            return ['success' => false, 'message' => 'Giờ bắt đầu phải trước giờ kết thúc'];
        }
        if ($e->trang_thai < 0 || $e->trang_thai > 3) return ['success' => false, 'message' => 'Trạng thái không hợp lệ'];
        return ['success' => true];
    }

    private static function formatConflicts(array $rows): array
    {
        $out = [];
        foreach ($rows as $r) {
            $out[] = [
                'id' => $r['id'],
                'ngay_hoc' => $r['ngay_hoc'],
                'gio_bat_dau' => substr($r['gio_bat_dau'], 0, 5),
                'gio_ket_thuc' => substr($r['gio_ket_thuc'], 0, 5),
                'tieu_de' => $r['tieu_de'],
                'phong_hoc' => $r['phong_hoc'],
                'ten_lop' => $r['ten_lop'] ?? '',
                'ma_lop' => $r['ma_lop'] ?? '',
                'ten_giang_vien' => $r['ten_giang_vien'] ?? '',
            ];
        }
        return $out;
    }
}
