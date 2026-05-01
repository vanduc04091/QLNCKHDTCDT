<?php
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/../PUBLIC/Entities/NCKH_DeTai_DTO.php';

class NCKH_DeTai_DAL
{
    private static function selectSql(): string
    {
        return "SELECT dt.*,
                       cd.ma_cap_do, cd.ten_cap_do,
                       tl.ma_the_loai, tl.ten_the_loai,
                       kp.ten_khoa,
                       cn.ho_ten AS ho_ten_chu_nhiem, cn.ma_nv AS ma_nv_chu_nhiem,
                       tk.ho_ten AS ho_ten_thu_ky,
                       u1.tai_khoan AS tai_khoan_nguoi_tao,
                       u2.tai_khoan AS tai_khoan_nguoi_cap_nhat,
                       u3.tai_khoan AS tai_khoan_nguoi_xu_ly
                FROM NCKH_DE_TAI dt
                LEFT JOIN DM_NCKH_CAP_DO  cd ON cd.id = dt.cap_do_id
                LEFT JOIN DM_NCKH_THE_LOAI tl ON tl.id = dt.the_loai_id
                LEFT JOIN DM_KHOA_PHONG   kp ON kp.id = dt.khoa_phong_id
                LEFT JOIN DM_NHAN_VIEN    cn ON cn.id = dt.chu_nhiem_id
                LEFT JOIN DM_NHAN_VIEN    tk ON tk.id = dt.thu_ky_id
                LEFT JOIN DM_NGUOI_DUNG   u1 ON u1.id = dt.nguoi_tao
                LEFT JOIN DM_NGUOI_DUNG   u2 ON u2.id = dt.nguoi_cap_nhat
                LEFT JOIN DM_NGUOI_DUNG   u3 ON u3.id = dt.nguoi_xu_ly_duyet";
    }

    public static function insert(NCKH_DeTai_PUBLIC $e): int
    {
        $sql = "INSERT INTO NCKH_DE_TAI
                (ma_de_tai, ten_de_tai, nam, cap_do_id, the_loai_id, khoa_phong_id,
                 chu_nhiem_id, thu_ky_id, muc_tieu, tom_tat, tu_khoa,
                 ngay_bat_dau, ngay_ket_thuc_du_kien, ngay_nghiem_thu,
                 kinh_phi_du_toan, kinh_phi_thuc_te, nguon_kinh_phi,
                 quyet_dinh_phe_duyet, ngay_quyet_dinh,
                 ket_qua_xep_loai, diem_so, noi_dung_ung_dung,
                 ten_tap_chi, so_tap_chi, nam_xuat_ban, issn_doi, link_bai_bao,
                 phien_bao_ve, dia_diem_bao_ve, ngay_bao_ve,
                 quyet_dinh_cong_nhan, ngay_quyet_dinh_cong_nhan, ten_khoa_text,
                 trang_thai, trang_thai_duyet, ngay_tao, ngay_cap_nhat, nguoi_tao, nguoi_cap_nhat, da_xoa)
                VALUES (:ma, :ten, :nam, :cd, :tl, :kp,
                        :cn, :tk, :muc, :tt2, :tk2,
                        :nbd, :nkt, :nnt,
                        :kpd, :kpt, :nkp,
                        :qd, :nqd,
                        :xl, :ds, :nd,
                        :ttc, :stc, :nxb, :issn, :link,
                        :pbv, :ddbv, :ngbv,
                        :qdcn, :nqdcn, :tkt,
                        :tt, :ttd, NOW(), NOW(), :u1, :u2, 0)";
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute([
            ':ma' => $e->ma_de_tai, ':ten' => $e->ten_de_tai, ':nam' => $e->nam,
            ':cd' => $e->cap_do_id, ':tl' => $e->the_loai_id, ':kp' => $e->khoa_phong_id,
            ':cn' => $e->chu_nhiem_id, ':tk' => $e->thu_ky_id,
            ':muc' => $e->muc_tieu, ':tt2' => $e->tom_tat, ':tk2' => $e->tu_khoa,
            ':nbd' => $e->ngay_bat_dau, ':nkt' => $e->ngay_ket_thuc_du_kien, ':nnt' => $e->ngay_nghiem_thu,
            ':kpd' => $e->kinh_phi_du_toan, ':kpt' => $e->kinh_phi_thuc_te, ':nkp' => $e->nguon_kinh_phi,
            ':qd' => $e->quyet_dinh_phe_duyet, ':nqd' => $e->ngay_quyet_dinh,
            ':xl' => $e->ket_qua_xep_loai, ':ds' => $e->diem_so, ':nd' => $e->noi_dung_ung_dung,
            ':ttc' => $e->ten_tap_chi, ':stc' => $e->so_tap_chi, ':nxb' => $e->nam_xuat_ban,
            ':issn' => $e->issn_doi, ':link' => $e->link_bai_bao,
            ':pbv' => $e->phien_bao_ve, ':ddbv' => $e->dia_diem_bao_ve, ':ngbv' => $e->ngay_bao_ve,
            ':qdcn' => $e->quyet_dinh_cong_nhan, ':nqdcn' => $e->ngay_quyet_dinh_cong_nhan, ':tkt' => $e->ten_khoa_text,
            ':tt' => $e->trang_thai, ':ttd' => $e->trang_thai_duyet ?: 'Nhap',
            ':u1' => $e->nguoi_tao ?? 0, ':u2' => $e->nguoi_tao ?? 0,
        ]);
        return (int)Database::getConnection()->lastInsertId();
    }

    public static function update(NCKH_DeTai_PUBLIC $e): int
    {
        $sql = "UPDATE NCKH_DE_TAI SET
                ma_de_tai=:ma, ten_de_tai=:ten, nam=:nam,
                cap_do_id=:cd, the_loai_id=:tl, khoa_phong_id=:kp,
                chu_nhiem_id=:cn, thu_ky_id=:tk,
                muc_tieu=:muc, tom_tat=:tt2, tu_khoa=:tk2,
                ngay_bat_dau=:nbd, ngay_ket_thuc_du_kien=:nkt, ngay_nghiem_thu=:nnt,
                kinh_phi_du_toan=:kpd, kinh_phi_thuc_te=:kpt, nguon_kinh_phi=:nkp,
                quyet_dinh_phe_duyet=:qd, ngay_quyet_dinh=:nqd,
                ket_qua_xep_loai=:xl, diem_so=:ds, noi_dung_ung_dung=:nd,
                ten_tap_chi=:ttc, so_tap_chi=:stc, nam_xuat_ban=:nxb, issn_doi=:issn, link_bai_bao=:link,
                phien_bao_ve=:pbv, dia_diem_bao_ve=:ddbv, ngay_bao_ve=:ngbv,
                quyet_dinh_cong_nhan=:qdcn, ngay_quyet_dinh_cong_nhan=:nqdcn, ten_khoa_text=:tkt,
                trang_thai=:tt,
                ngay_cap_nhat=NOW(), nguoi_cap_nhat=:u
                WHERE id=:id AND da_xoa=0";
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute([
            ':ma' => $e->ma_de_tai, ':ten' => $e->ten_de_tai, ':nam' => $e->nam,
            ':cd' => $e->cap_do_id, ':tl' => $e->the_loai_id, ':kp' => $e->khoa_phong_id,
            ':cn' => $e->chu_nhiem_id, ':tk' => $e->thu_ky_id,
            ':muc' => $e->muc_tieu, ':tt2' => $e->tom_tat, ':tk2' => $e->tu_khoa,
            ':nbd' => $e->ngay_bat_dau, ':nkt' => $e->ngay_ket_thuc_du_kien, ':nnt' => $e->ngay_nghiem_thu,
            ':kpd' => $e->kinh_phi_du_toan, ':kpt' => $e->kinh_phi_thuc_te, ':nkp' => $e->nguon_kinh_phi,
            ':qd' => $e->quyet_dinh_phe_duyet, ':nqd' => $e->ngay_quyet_dinh,
            ':xl' => $e->ket_qua_xep_loai, ':ds' => $e->diem_so, ':nd' => $e->noi_dung_ung_dung,
            ':ttc' => $e->ten_tap_chi, ':stc' => $e->so_tap_chi, ':nxb' => $e->nam_xuat_ban,
            ':issn' => $e->issn_doi, ':link' => $e->link_bai_bao,
            ':pbv' => $e->phien_bao_ve, ':ddbv' => $e->dia_diem_bao_ve, ':ngbv' => $e->ngay_bao_ve,
            ':qdcn' => $e->quyet_dinh_cong_nhan, ':nqdcn' => $e->ngay_quyet_dinh_cong_nhan, ':tkt' => $e->ten_khoa_text,
            ':tt' => $e->trang_thai,
            ':u' => $e->nguoi_cap_nhat ?? 0, ':id' => $e->id,
        ]);
        return $stmt->rowCount();
    }

    public static function trash(int $id, int $u): int
    {
        $stmt = Database::getConnection()->prepare("UPDATE NCKH_DE_TAI SET da_xoa=1, ngay_cap_nhat=NOW(), nguoi_cap_nhat=:u WHERE id=:id");
        $stmt->execute([':u' => $u, ':id' => $id]);
        return $stmt->rowCount();
    }

    public static function restore(int $id, int $u): int
    {
        $stmt = Database::getConnection()->prepare("UPDATE NCKH_DE_TAI SET da_xoa=0, ngay_cap_nhat=NOW(), nguoi_cap_nhat=:u WHERE id=:id");
        $stmt->execute([':u' => $u, ':id' => $id]);
        return $stmt->rowCount();
    }

    public static function delete(int $id): int
    {
        $stmt = Database::getConnection()->prepare("DELETE FROM NCKH_DE_TAI WHERE id=:id");
        $stmt->execute([':id' => $id]);
        return $stmt->rowCount();
    }

    public static function getById(int $id): ?NCKH_DeTai_DTO
    {
        $stmt = Database::getConnection()->prepare(self::selectSql() . " WHERE dt.id=:id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ? Database::hydrate($row, 'NCKH_DeTai_DTO') : null;
    }

    public static function getPaged(int $page, int $pageSize, array $filters = [], int $daXoa = 0): array
    {
        [$page, $pageSize, $offset] = PaginationHelper::normalize($page, $pageSize);
        $where = " WHERE dt.da_xoa=:dx ";
        $params = [':dx' => $daXoa];

        if (!empty($filters['search'])) {
            $kw = "%" . $filters['search'] . "%";
            $where .= " AND (dt.ma_de_tai LIKE :s1 OR dt.ten_de_tai LIKE :s2 OR dt.tu_khoa LIKE :s3
                          OR cn.ho_ten LIKE :s4 OR cn.ma_nv LIKE :s5) ";
            $params[':s1'] = $kw; $params[':s2'] = $kw; $params[':s3'] = $kw;
            $params[':s4'] = $kw; $params[':s5'] = $kw;
        }
        if (!empty($filters['nam']))         { $where .= " AND dt.nam=:nam ";              $params[':nam'] = (int)$filters['nam']; }
        if (!empty($filters['cap_do_id']))   { $where .= " AND dt.cap_do_id=:cd ";         $params[':cd']  = (int)$filters['cap_do_id']; }
        if (!empty($filters['the_loai_id'])) { $where .= " AND dt.the_loai_id=:tl ";       $params[':tl']  = (int)$filters['the_loai_id']; }
        if (!empty($filters['khoa_phong_id'])){ $where .= " AND dt.khoa_phong_id=:kp ";    $params[':kp']  = (int)$filters['khoa_phong_id']; }
        if (!empty($filters['chu_nhiem_id'])){ $where .= " AND dt.chu_nhiem_id=:cn ";      $params[':cn']  = (int)$filters['chu_nhiem_id']; }
        if (isset($filters['trang_thai']) && $filters['trang_thai'] !== '') {
            $where .= " AND dt.trang_thai=:tt "; $params[':tt'] = (int)$filters['trang_thai'];
        }
        if (!empty($filters['trang_thai_duyet'])) {
            $where .= " AND dt.trang_thai_duyet=:ttd "; $params[':ttd'] = $filters['trang_thai_duyet'];
        }
        if (!empty($filters['trang_thai_duyet_in'])) {
            $list = $filters['trang_thai_duyet_in']; // mảng
            $placeholders = [];
            foreach ($list as $i => $v) { $ph = ":ttd{$i}"; $placeholders[] = $ph; $params[$ph] = $v; }
            $where .= " AND dt.trang_thai_duyet IN (" . implode(',', $placeholders) . ") ";
        }
        if (!empty($filters['nguoi_tao_id'])) {
            $where .= " AND dt.nguoi_tao=:nt "; $params[':nt'] = (int)$filters['nguoi_tao_id'];
        }

        $countSql = "SELECT COUNT(*) FROM NCKH_DE_TAI dt
                     LEFT JOIN DM_NHAN_VIEN cn ON cn.id = dt.chu_nhiem_id" . $where;
        $stmt = Database::getConnection()->prepare($countSql);
        $stmt->execute($params);
        $total = (int)$stmt->fetchColumn();

        $sql = self::selectSql() . $where . " ORDER BY dt.nam DESC, dt.id DESC LIMIT {$pageSize} OFFSET {$offset}";
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute($params);
        return [
            'data' => $stmt->fetchAll(),
            'totalRecords' => $total,
            'totalPages' => PaginationHelper::totalPages($total, $pageSize),
        ];
    }

    public static function checkMaExists(string $ma, int $excludeId = 0): bool
    {
        $stmt = Database::getConnection()->prepare("SELECT COUNT(*) FROM NCKH_DE_TAI WHERE ma_de_tai=:m AND da_xoa=0 AND id<>:id");
        $stmt->execute([':m' => $ma, ':id' => $excludeId]);
        return (int)$stmt->fetchColumn() > 0;
    }

    /** Thống kê dashboard NCKH */
    public static function statsByYear(int $nam): array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare(
            "SELECT
                SUM(CASE WHEN trang_thai=0 THEN 1 ELSE 0 END) AS de_xuat,
                SUM(CASE WHEN trang_thai=1 THEN 1 ELSE 0 END) AS dang_thuc_hien,
                SUM(CASE WHEN trang_thai=2 THEN 1 ELSE 0 END) AS hoan_thanh,
                SUM(CASE WHEN trang_thai=3 THEN 1 ELSE 0 END) AS tam_dung,
                SUM(CASE WHEN trang_thai=4 THEN 1 ELSE 0 END) AS huy,
                COUNT(*) AS tong
             FROM NCKH_DE_TAI WHERE da_xoa=0 AND nam=:nam"
        );
        $stmt->execute([':nam' => $nam]);
        return $stmt->fetch() ?: [];
    }

    public static function statsByCapDo(int $nam): array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare(
            "SELECT cd.ten_cap_do AS ten, COUNT(dt.id) AS so_luong
             FROM DM_NCKH_CAP_DO cd
             LEFT JOIN NCKH_DE_TAI dt ON dt.cap_do_id=cd.id AND dt.da_xoa=0 AND dt.nam=:nam
             WHERE cd.da_xoa=0
             GROUP BY cd.id, cd.ten_cap_do, cd.thu_tu
             ORDER BY cd.thu_tu ASC"
        );
        $stmt->execute([':nam' => $nam]);
        return $stmt->fetchAll();
    }

    public static function statsByTheLoai(int $nam): array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare(
            "SELECT tl.ten_the_loai AS ten, COUNT(dt.id) AS so_luong
             FROM DM_NCKH_THE_LOAI tl
             LEFT JOIN NCKH_DE_TAI dt ON dt.the_loai_id=tl.id AND dt.da_xoa=0 AND dt.nam=:nam
             WHERE tl.da_xoa=0
             GROUP BY tl.id, tl.ten_the_loai, tl.thu_tu
             ORDER BY tl.thu_tu ASC"
        );
        $stmt->execute([':nam' => $nam]);
        return $stmt->fetchAll();
    }

    public static function statsByKhoaPhong(int $nam, int $limit = 10): array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare(
            "SELECT COALESCE(kp.ten_khoa, '(Chưa phân loại)') AS ten, COUNT(dt.id) AS so_luong
             FROM NCKH_DE_TAI dt
             LEFT JOIN DM_KHOA_PHONG kp ON kp.id = dt.khoa_phong_id
             WHERE dt.da_xoa=0 AND dt.nam=:nam
             GROUP BY dt.khoa_phong_id, kp.ten_khoa
             ORDER BY so_luong DESC LIMIT :lim"
        );
        $stmt->bindValue(':nam', $nam, PDO::PARAM_INT);
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /** Đề tài sắp đến hạn (ngay_ket_thuc_du_kien trong 30 ngày tới và chưa hoàn thành) */
    public static function getUpcomingDeadlines(int $limit = 10): array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare(
            "SELECT dt.id, dt.ma_de_tai, dt.ten_de_tai, dt.ngay_ket_thuc_du_kien,
                    dt.trang_thai, cn.ho_ten AS ho_ten_chu_nhiem
             FROM NCKH_DE_TAI dt
             LEFT JOIN DM_NHAN_VIEN cn ON cn.id = dt.chu_nhiem_id
             WHERE dt.da_xoa=0 AND dt.trang_thai IN (0,1)
               AND dt.ngay_ket_thuc_du_kien IS NOT NULL
               AND dt.ngay_ket_thuc_du_kien BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)
             ORDER BY dt.ngay_ket_thuc_du_kien ASC LIMIT :lim"
        );
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /** Workflow: chuyển sang ChoDuyet */
    public static function setSubmitted(int $id, int $u): int
    {
        $stmt = Database::getConnection()->prepare(
            "UPDATE NCKH_DE_TAI SET trang_thai_duyet='ChoDuyet', ngay_gui_duyet=NOW(),
             ngay_cap_nhat=NOW(), nguoi_cap_nhat=:u
             WHERE id=:id AND da_xoa=0 AND trang_thai_duyet IN ('Nhap','TuChoi')"
        );
        $stmt->execute([':u' => $u, ':id' => $id]);
        return $stmt->rowCount();
    }

    /** Workflow: duyệt */
    public static function setApproved(int $id, int $u): int
    {
        $stmt = Database::getConnection()->prepare(
            "UPDATE NCKH_DE_TAI SET trang_thai_duyet='DaDuyet', ngay_xu_ly_duyet=NOW(),
             nguoi_xu_ly_duyet=:u1, ly_do_tu_choi=NULL,
             ngay_cap_nhat=NOW(), nguoi_cap_nhat=:u2
             WHERE id=:id AND da_xoa=0 AND trang_thai_duyet='ChoDuyet'"
        );
        $stmt->execute([':u1' => $u, ':u2' => $u, ':id' => $id]);
        return $stmt->rowCount();
    }

    /** Workflow: từ chối */
    public static function setRejected(int $id, int $u, string $lyDo): int
    {
        $stmt = Database::getConnection()->prepare(
            "UPDATE NCKH_DE_TAI SET trang_thai_duyet='TuChoi', ngay_xu_ly_duyet=NOW(),
             nguoi_xu_ly_duyet=:u1, ly_do_tu_choi=:ld,
             ngay_cap_nhat=NOW(), nguoi_cap_nhat=:u2
             WHERE id=:id AND da_xoa=0 AND trang_thai_duyet='ChoDuyet'"
        );
        $stmt->execute([':u1' => $u, ':u2' => $u, ':ld' => $lyDo, ':id' => $id]);
        return $stmt->rowCount();
    }

    /** Đếm hàng đợi duyệt */
    public static function countPending(): int
    {
        return (int)Database::getConnection()->query(
            "SELECT COUNT(*) FROM NCKH_DE_TAI WHERE da_xoa=0 AND trang_thai_duyet='ChoDuyet'"
        )->fetchColumn();
    }

    /** Tìm theo cá nhân: chủ nhiệm, thư ký, hoặc thành viên */
    public static function findByNhanVien(int $nhanVienId): array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare(
            "SELECT DISTINCT dt.id, dt.ma_de_tai, dt.ten_de_tai, dt.nam, dt.trang_thai,
                    cd.ten_cap_do, tl.ten_the_loai,
                    CASE
                      WHEN dt.chu_nhiem_id=:nv THEN 'Chủ nhiệm'
                      WHEN dt.thu_ky_id=:nv THEN 'Thư ký'
                      ELSE 'Thành viên'
                    END AS vai_tro
             FROM NCKH_DE_TAI dt
             LEFT JOIN DM_NCKH_CAP_DO  cd ON cd.id = dt.cap_do_id
             LEFT JOIN DM_NCKH_THE_LOAI tl ON tl.id = dt.the_loai_id
             LEFT JOIN NCKH_THANH_VIEN tv ON tv.de_tai_id=dt.id AND tv.da_xoa=0
             WHERE dt.da_xoa=0
               AND (dt.chu_nhiem_id=:nv OR dt.thu_ky_id=:nv OR tv.nhan_vien_id=:nv)
             ORDER BY dt.nam DESC, dt.id DESC"
        );
        $stmt->execute([':nv' => $nhanVienId]);
        return $stmt->fetchAll();
    }
}
