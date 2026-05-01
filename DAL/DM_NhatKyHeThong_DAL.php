<?php
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/../PUBLIC/Entities/DM_NhatKyHeThong_DTO.php';

class DM_NhatKyHeThong_DAL
{
    public static function log(int $nguoiDungId, string $module, string $hanhDong, ?string $bang = null, ?int $idLienQuan = null, ?string $noiDung = null): void
    {
        try {
            $sql = "INSERT INTO DM_NHAT_KY_HE_THONG (nguoi_dung_id, module, hanh_dong, bang_lien_quan, id_lien_quan, noi_dung_thay_doi, dia_chi_ip, thoi_gian, ngay_tao, ngay_cap_nhat)
                    VALUES (:u, :m, :hd, :b, :i, :nd, :ip, NOW(), NOW(), NOW())";
            $stmt = Database::getConnection()->prepare($sql);
            $stmt->execute([
                ':u' => $nguoiDungId ?: null,
                ':m' => $module,
                ':hd' => mb_substr($hanhDong, 0, 200),
                ':b' => $bang,
                ':i' => $idLienQuan,
                ':nd' => $noiDung,
                ':ip' => Helper::getClientIp(),
            ]);
        } catch (Throwable $e) {
            // Không cho log làm gãy luồng chính
        }
    }

    private static function selectSql(): string
    {
        return "SELECT nk.*, u.tai_khoan, nv.ho_ten
                FROM DM_NHAT_KY_HE_THONG nk
                LEFT JOIN DM_NGUOI_DUNG u ON u.id = nk.nguoi_dung_id
                LEFT JOIN DM_NHAN_VIEN nv ON nv.id = u.nhan_vien_id";
    }

    public static function getById(int $id): ?DM_NhatKyHeThong_DTO
    {
        $stmt = Database::getConnection()->prepare(self::selectSql() . " WHERE nk.id=:id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ? Database::hydrate($row, 'DM_NhatKyHeThong_DTO') : null;
    }

    public static function getPaged(int $page, int $pageSize, string $search = '', string $module = '', int $nguoiDungId = 0, string $fromDate = '', string $toDate = ''): array
    {
        [$page, $pageSize, $offset] = PaginationHelper::normalize($page, $pageSize);
        $where = " WHERE 1=1 ";
        $params = [];
        if ($search !== '') {
            $where .= " AND (nk.hanh_dong LIKE :s OR nk.bang_lien_quan LIKE :s OR nk.dia_chi_ip LIKE :s OR u.tai_khoan LIKE :s) ";
            $params[':s'] = "%{$search}%";
        }
        if ($module !== '') {
            $where .= " AND nk.module=:m ";
            $params[':m'] = $module;
        }
        if ($nguoiDungId > 0) {
            $where .= " AND nk.nguoi_dung_id=:nd ";
            $params[':nd'] = $nguoiDungId;
        }
        if ($fromDate !== '') {
            $where .= " AND nk.thoi_gian >= :fd ";
            $params[':fd'] = $fromDate . ' 00:00:00';
        }
        if ($toDate !== '') {
            $where .= " AND nk.thoi_gian <= :td ";
            $params[':td'] = $toDate . ' 23:59:59';
        }

        $stmt = Database::getConnection()->prepare("SELECT COUNT(*) FROM DM_NHAT_KY_HE_THONG nk LEFT JOIN DM_NGUOI_DUNG u ON u.id=nk.nguoi_dung_id" . $where);
        $stmt->execute($params);
        $total = (int)$stmt->fetchColumn();

        $sql = self::selectSql() . $where . " ORDER BY nk.thoi_gian DESC, nk.id DESC LIMIT {$pageSize} OFFSET {$offset}";
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute($params);
        return [
            'data' => $stmt->fetchAll(),
            'totalRecords' => $total,
            'totalPages' => PaginationHelper::totalPages($total, $pageSize),
        ];
    }

    public static function getModuleList(): array
    {
        $sql = "SELECT DISTINCT module FROM DM_NHAT_KY_HE_THONG WHERE module IS NOT NULL AND module <> '' ORDER BY module";
        return Database::getConnection()->query($sql)->fetchAll(PDO::FETCH_COLUMN);
    }

    /** Xóa các log cũ hơn N ngày. Trả về số dòng bị xóa. */
    public static function purgeOlderThan(int $days): int
    {
        if ($days <= 0) return 0;
        $stmt = Database::getConnection()->prepare("DELETE FROM DM_NHAT_KY_HE_THONG WHERE thoi_gian < DATE_SUB(NOW(), INTERVAL :d DAY)");
        $stmt->bindValue(':d', $days, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount();
    }
}
