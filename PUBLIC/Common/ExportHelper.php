<?php
/**
 * ExportHelper — tiện ích cho các file export.php
 *
 * Vì PaginationHelper::normalize() kẹp pageSize ở 500 (để chặn pageSize khổng lồ
 * gửi từ POST), nên gọi getPaged(1, 100000, ...) chỉ lấy được 500 dòng đầu.
 * fetchAll() dưới đây lặp từng trang 500 và gộp lại => xuất đủ dữ liệu mà
 * KHÔNG cần nới trần phân trang (giữ nguyên bảo mật).
 *
 * Dùng:
 *   $rows = ExportHelper::fetchAll(fn($page, $size) =>
 *       DM_NhanVien_BUS::getPaged($page, $size, $search, $daXoa, $kp));
 */
class ExportHelper
{
    const PAGE_SIZE = 500;
    const MAX_ROWS  = 200000;   // chặn vòng lặp vô hạn / file quá lớn

    /**
     * @param callable $fetcher fn(int $page, int $pageSize): array{data: array, totalRecords: int}
     * @return array toàn bộ bản ghi
     */
    public static function fetchAll(callable $fetcher): array
    {
        $all = [];
        $page = 1;
        do {
            $res = $fetcher($page, self::PAGE_SIZE);
            $data = $res['data'] ?? [];
            if (!$data) break;
            foreach ($data as $row) $all[] = $row;

            $total = (int)($res['totalRecords'] ?? count($all));
            if (count($all) >= $total || count($all) >= self::MAX_ROWS) break;
            $page++;
        } while (true);

        return $all;
    }
}
