<?php
/**
 * PaginationHelper - Tính offset, limit, tổng trang
 */
class PaginationHelper
{
    /** Trần pageSize cho phân trang thường (chặn pageSize khổng lồ gửi từ POST). */
    const MAX_PAGE_SIZE = 500;

    /** Trần cho luồng XUẤT EXCEL — chỉ dùng qua normalizeExport(). */
    const MAX_EXPORT_SIZE = 200000;

    /**
     * Chuẩn hóa page/pageSize cho màn danh sách.
     * LUÔN kẹp ở MAX_PAGE_SIZE vì pageSize có thể đến từ POST của người dùng.
     */
    public static function normalize(int $page, int $pageSize): array
    {
        $page = max(1, $page);
        $pageSize = max(1, min(self::MAX_PAGE_SIZE, $pageSize));
        $offset = ($page - 1) * $pageSize;
        return [$page, $pageSize, $offset];
    }

    /**
     * Chuẩn hóa cho luồng XUẤT EXCEL (lấy toàn bộ bản ghi).
     * Chỉ được gọi từ code server (file export.php), KHÔNG nhận pageSize từ input.
     */
    public static function normalizeExport(int $page, int $pageSize): array
    {
        $page = max(1, $page);
        $pageSize = max(1, min(self::MAX_EXPORT_SIZE, $pageSize));
        $offset = ($page - 1) * $pageSize;
        return [$page, $pageSize, $offset];
    }

    public static function totalPages(int $totalRecords, int $pageSize): int
    {
        if ($pageSize <= 0) return 0;
        return (int)ceil($totalRecords / $pageSize);
    }
}
