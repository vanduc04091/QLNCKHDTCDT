<?php
/**
 * IconHelper.php - SVG Icon utilities
 * Replaces emojis with scalable, accessible SVG icons
 * Based on Heroicons design system
 */

class IconHelper {
    
    /**
     * Generate inline SVG icon
     * @param string $name Icon name (search, edit, trash, key, logout, etc.)
     * @param string $size Icon size in pixels (16, 20, 24, 32, etc.) - default 20
     * @param string $class CSS class for styling
     * @param string $color Color hex code (default: currentColor)
     * @return string SVG HTML
     */
    public static function svg($name, $size = '20', $class = '', $color = 'currentColor') {
        $icons = self::getIcons();
        
        if (!isset($icons[$name])) {
            return '<!-- Icon not found: ' . htmlspecialchars($name) . ' -->';
        }
        
        $icon = $icons[$name];
        $viewBox = $icon['viewBox'] ?? '0 0 24 24';
        $path = $icon['path'];
        $strokeWidth = $icon['strokeWidth'] ?? '2';
        $fill = $icon['fill'] ?? 'none';
        
        $svg = sprintf(
            '<svg class="icon icon-%s %s" width="%s" height="%s" viewBox="%s" fill="%s" stroke="%s" stroke-width="%s" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">%s</svg>',
            htmlspecialchars($name),
            htmlspecialchars($class),
            htmlspecialchars($size),
            htmlspecialchars($size),
            htmlspecialchars($viewBox),
            htmlspecialchars($fill),
            htmlspecialchars($color),
            htmlspecialchars($strokeWidth),
            $path
        );
        
        return $svg;
    }
    
    /**
     * Generate SVG icon with label (for buttons and links)
     * @param string $name Icon name
     * @param string $label Text label
     * @param string $size Icon size
     * @param string $class CSS class
     * @return string HTML with icon and label
     */
    public static function withLabel($name, $label, $size = '18', $class = '') {
        $icon = self::svg($name, $size, '', 'currentColor');
        return '<span class="icon-label ' . htmlspecialchars($class) . '">' . $icon . ' ' . htmlspecialchars($label) . '</span>';
    }
    
    /**
     * Generate SVG icon for button (icon only)
     * @param string $name Icon name
     * @param string $title Tooltip/aria-label
     * @param string $size Icon size
     * @param string $class CSS class
     * @return string HTML
     */
    public static function button($name, $title = '', $size = '20', $class = '') {
        $icon = self::svg($name, $size, '', 'currentColor');
        $title = htmlspecialchars($title);
        return '<span class="icon-btn ' . htmlspecialchars($class) . '" role="button" title="' . $title . '" aria-label="' . $title . '">' . $icon . '</span>';
    }
    
    /**
     * Generate raw SVG string for use in JavaScript/templates
     * Used when you need to inject HTML into JS strings
     * @param string $name Icon name
     * @param string $size Icon size
     * @param string $class CSS class
     * @return string Raw SVG HTML (single quotes escaped)
     */
    public static function raw($name, $size = '18', $class = '') {
        $svg = self::svg($name, $size, $class, 'currentColor');
        // Escape single quotes for use in JavaScript strings
        return addslashes($svg);
    }
    
    /**
     * Get all available icons
     */
    private static function getIcons() {
        return [
            // UI Navigation & Status
            'search' => [
                'viewBox' => '0 0 24 24',
                'path' => '<circle cx="11" cy="11" r="8"></circle><path d="m21 21-4.35-4.35"></path>',
                'strokeWidth' => '2',
            ],
            'edit' => [
                'viewBox' => '0 0 24 24',
                'path' => '<path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>',
                'strokeWidth' => '2',
            ],
            'trash' => [
                'viewBox' => '0 0 24 24',
                'path' => '<polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line>',
                'strokeWidth' => '2',
            ],
            'key' => [
                'viewBox' => '0 0 24 24',
                'path' => '<circle cx="7.5" cy="15.5" r="5.5"></circle><path d="M21 2l-9.6 9.6"></path><path d="m15.5 7l3.5-3.5"></path>',
                'strokeWidth' => '2',
            ],
            'logout' => [
                'viewBox' => '0 0 24 24',
                'path' => '<path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line>',
                'strokeWidth' => '2',
            ],
            'dashboard' => [
                'viewBox' => '0 0 24 24',
                'path' => '<rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect>',
                'strokeWidth' => '2',
            ],
            'checkmark' => [
                'viewBox' => '0 0 24 24',
                'path' => '<polyline points="20 6 9 17 4 12"></polyline>',
                'strokeWidth' => '2',
            ],
            'warning' => [
                'viewBox' => '0 0 24 24',
                'path' => '<path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3.05h16.94a2 2 0 0 0 1.71-3.05L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line>',
                'strokeWidth' => '2',
            ],
            'check-circle' => [
                'viewBox' => '0 0 24 24',
                'path' => '<path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline>',
                'strokeWidth' => '2',
            ],
            'x-circle' => [
                'viewBox' => '0 0 24 24',
                'path' => '<circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line>',
                'strokeWidth' => '2',
            ],
            'menu' => [
                'viewBox' => '0 0 24 24',
                'path' => '<line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="18" x2="21" y2="18"></line>',
                'strokeWidth' => '2',
            ],
            'close' => [
                'viewBox' => '0 0 24 24',
                'path' => '<line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line>',
                'strokeWidth' => '2',
            ],
            'plus' => [
                'viewBox' => '0 0 24 24',
                'path' => '<line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line>',
                'strokeWidth' => '2',
            ],
            'chevron-down' => [
                'viewBox' => '0 0 24 24',
                'path' => '<polyline points="6 9 12 15 18 9"></polyline>',
                'strokeWidth' => '2',
            ],
            'chevron-up' => [
                'viewBox' => '0 0 24 24',
                'path' => '<polyline points="18 15 12 9 6 15"></polyline>',
                'strokeWidth' => '2',
            ],
            'arrow-right' => [
                'viewBox' => '0 0 24 24',
                'path' => '<line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline>',
                'strokeWidth' => '2',
            ],
            'arrow-left' => [
                'viewBox' => '0 0 24 24',
                'path' => '<line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline>',
                'strokeWidth' => '2',
            ],
            'filter' => [
                'viewBox' => '0 0 24 24',
                'path' => '<polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>',
                'strokeWidth' => '2',
            ],
            'download' => [
                'viewBox' => '0 0 24 24',
                'path' => '<path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line>',
                'strokeWidth' => '2',
            ],
            'refresh' => [
                'viewBox' => '0 0 24 24',
                'path' => '<polyline points="23 4 23 10 17 10"></polyline><polyline points="1 20 1 14 7 14"></polyline><path d="M3.51 9a9 9 0 0 1 14.85-3.36M20.49 15a9 9 0 0 1-14.85 3.36"></path>',
                'strokeWidth' => '2',
            ],
            'settings' => [
                'viewBox' => '0 0 24 24',
                'path' => '<circle cx="12" cy="12" r="3"></circle><path d="M12 1v6m0 6v6M4.22 4.22l4.24 4.24m0 5.08l-4.24 4.24M1 12h6m6 0h6m-2.78-7.78l-4.24 4.24m0 5.08l4.24 4.24"></path>',
                'strokeWidth' => '2',
            ],
            'user' => [
                'viewBox' => '0 0 24 24',
                'path' => '<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle>',
                'strokeWidth' => '2',
            ],
            'users' => [
                'viewBox' => '0 0 24 24',
                'path' => '<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path>',
                'strokeWidth' => '2',
            ],
            'save' => [
                'viewBox' => '0 0 24 24',
                'path' => '<path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline>',
                'strokeWidth' => '2',
            ],
            // Danh sách form - clipboard kèm gạch dòng
            'clipboard-list' => [
                'viewBox' => '0 0 24 24',
                'path' => '<rect x="8" y="3" width="8" height="4" rx="1"></rect><path d="M16 5h2a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2h2"></path><line x1="9" y1="12" x2="15" y2="12"></line><line x1="9" y1="16" x2="15" y2="16"></line>',
                'strokeWidth' => '2',
            ],
            // Bệnh viện - tòa nhà có dấu cộng
            'hospital' => [
                'viewBox' => '0 0 24 24',
                'path' => '<path d="M3 21h18"></path><path d="M5 21V7l7-4 7 4v14"></path><line x1="12" y1="10" x2="12" y2="16"></line><line x1="9" y1="13" x2="15" y2="13"></line>',
                'strokeWidth' => '2',
            ],
            // Khoa / Phòng - tòa nhà văn phòng nhiều cửa sổ
            'building-office' => [
                'viewBox' => '0 0 24 24',
                'path' => '<rect x="4" y="3" width="16" height="18" rx="1"></rect><line x1="9" y1="8" x2="9" y2="8"></line><line x1="15" y1="8" x2="15" y2="8"></line><line x1="9" y1="12" x2="9" y2="12"></line><line x1="15" y1="12" x2="15" y2="12"></line><path d="M10 21v-4h4v4"></path>',
                'strokeWidth' => '2',
            ],
            // Loại hình đào tạo - mũ tốt nghiệp
            'academic-cap' => [
                'viewBox' => '0 0 24 24',
                'path' => '<path d="M12 4L1 9l11 5 11-5-11-5z"></path><path d="M5 11v6c0 1 3 3 7 3s7-2 7-3v-6"></path>',
                'strokeWidth' => '2',
            ],
            // Hình thức học - màn hình trình chiếu
            'presentation' => [
                'viewBox' => '0 0 24 24',
                'path' => '<rect x="2" y="4" width="20" height="12" rx="1"></rect><line x1="12" y1="16" x2="12" y2="20"></line><line x1="8" y1="20" x2="16" y2="20"></line><polyline points="7 11 10 8 13 11 17 7"></polyline>',
                'strokeWidth' => '2',
            ],
            // Khóa học - cuộn sách mở
            'book-open' => [
                'viewBox' => '0 0 24 24',
                'path' => '<path d="M2 6h7a3 3 0 0 1 3 3v12a2 2 0 0 0-2-2H2z"></path><path d="M22 6h-7a3 3 0 0 0-3 3v12a2 2 0 0 1 2-2h8z"></path>',
                'strokeWidth' => '2',
            ],
            // Môn học - sách đóng
            'book' => [
                'viewBox' => '0 0 24 24',
                'path' => '<path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>',
                'strokeWidth' => '2',
            ],
            // Nhật ký hệ thống - đồng hồ có mũi tên lùi
            'clock-history' => [
                'viewBox' => '0 0 24 24',
                'path' => '<path d="M3 12a9 9 0 1 0 3-6.7"></path><polyline points="3 4 3 10 9 10"></polyline><polyline points="12 7 12 12 15 14"></polyline>',
                'strokeWidth' => '2',
            ],
            'user-badge' => [
                'viewBox' => '0 0 24 24',
                'path' => '<rect x="3" y="4" width="18" height="16" rx="2"></rect><circle cx="9" cy="11" r="2.5"></circle><path d="M5.5 17c.8-2 2-3 3.5-3s2.7 1 3.5 3"></path><line x1="15" y1="10" x2="19" y2="10"></line><line x1="15" y1="13" x2="18" y2="13"></line>',
                'strokeWidth' => '2',
            ],
            // Mắt - xem
            'eye' => [
                'viewBox' => '0 0 24 24',
                'path' => '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle>',
                'strokeWidth' => '2',
            ],
            // Ngôi sao - đánh giá / yêu thích
            'star' => [
                'viewBox' => '0 0 24 24',
                'path' => '<polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>',
                'strokeWidth' => '2',
            ],
            // Tải lên
            'upload' => [
                'viewBox' => '0 0 24 24',
                'path' => '<path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line>',
                'strokeWidth' => '2',
            ],
            // Mũ tốt nghiệp - chi tiết hơn academic-cap
            'graduation-cap' => [
                'viewBox' => '0 0 24 24',
                'path' => '<path d="M22 10v6"></path><path d="M2 10l10-5 10 5-10 5-10-5z"></path><path d="M6 12v5c3 3 9 3 12 0v-5"></path>',
                'strokeWidth' => '2',
            ],
            // Tích đơn - check (khác checkmark ở stroke nhẹ)
            'check' => [
                'viewBox' => '0 0 24 24',
                'path' => '<polyline points="20 6 9 17 4 12"></polyline>',
                'strokeWidth' => '2.5',
            ],
            // Biểu đồ cột
            'bar-chart' => [
                'viewBox' => '0 0 24 24',
                'path' => '<line x1="12" y1="20" x2="12" y2="10"></line><line x1="18" y1="20" x2="18" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line><line x1="3" y1="20" x2="21" y2="20"></line>',
                'strokeWidth' => '2',
            ],
            // Đồng hồ
            'clock' => [
                'viewBox' => '0 0 24 24',
                'path' => '<circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline>',
                'strokeWidth' => '2',
            ],
            // Dấu nhân - đóng (khác close ở stroke đậm hơn)
            'x' => [
                'viewBox' => '0 0 24 24',
                'path' => '<line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line>',
                'strokeWidth' => '2.5',
            ],
            // Lịch
            'calendar' => [
                'viewBox' => '0 0 24 24',
                'path' => '<rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line>',
                'strokeWidth' => '2',
            ],
            // Cảnh báo tam giác
            'alert-triangle' => [
                'viewBox' => '0 0 24 24',
                'path' => '<path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3.05h16.94a2 2 0 0 0 1.71-3.05L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line>',
                'strokeWidth' => '2',
            ],
            // Play tròn - phát video / bắt đầu
            'play-circle' => [
                'viewBox' => '0 0 24 24',
                'path' => '<circle cx="12" cy="12" r="10"></circle><polygon points="10 8 16 12 10 16 10 8"></polygon>',
                'strokeWidth' => '2',
            ],
            // Trường học - tòa nhà có cờ
            'school' => [
                'viewBox' => '0 0 24 24',
                'path' => '<path d="M3 21h18"></path><path d="M5 21V8l7-5 7 5v13"></path><path d="M9 21v-6h6v6"></path><path d="M12 3v5"></path>',
                'strokeWidth' => '2',
            ],
            // Tòa nhà 2 - cụm 2 tòa cao thấp (khác hospital ở dấu cộng, khác building-office ở 2 khối)
            'building-2' => [
                'viewBox' => '0 0 24 24',
                'path' => '<path d="M6 22V4a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v18Z"></path><path d="M6 12H4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h2"></path><path d="M18 9h2a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2h-2"></path><path d="M10 6h4"></path><path d="M10 10h4"></path><path d="M10 14h4"></path><path d="M10 18h4"></path>',
                'strokeWidth' => '2',
            ],
            // Người dùng có dấu tích - đã xác minh / đã duyệt
            'user-check' => [
                'viewBox' => '0 0 24 24',
                'path' => '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><polyline points="16 11 18 13 22 9"></polyline>',
                'strokeWidth' => '2',
            ],
            // Lưới ô vuông - chuyển sang dạng grid
            'grid' => [
                'viewBox' => '0 0 24 24',
                'path' => '<rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect>',
                'strokeWidth' => '2',
            ],
            // Danh sách - 3 dòng kẻ + chấm bullet
            'list' => [
                'viewBox' => '0 0 24 24',
                'path' => '<line x1="8" y1="6" x2="21" y2="6"></line><line x1="8" y1="12" x2="21" y2="12"></line><line x1="8" y1="18" x2="21" y2="18"></line><line x1="3" y1="6" x2="3.01" y2="6"></line><line x1="3" y1="12" x2="3.01" y2="12"></line><line x1="3" y1="18" x2="3.01" y2="18"></line>',
                'strokeWidth' => '2',
            ],
            // Đường xu hướng đi lên - tăng trưởng / thống kê
            'trending-up' => [
                'viewBox' => '0 0 24 24',
                'path' => '<polyline points="22 7 13.5 15.5 8.5 10.5 2 17"></polyline><polyline points="16 7 22 7 22 13"></polyline>',
                'strokeWidth' => '2',
            ],
            // Sóng nhịp - hoạt động / pulse
            'activity' => [
                'viewBox' => '0 0 24 24',
                'path' => '<polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>',
                'strokeWidth' => '2',
            ],
            // Văn bản - tài liệu có dòng kẻ
            'file-text' => [
                'viewBox' => '0 0 24 24',
                'path' => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline>',
                'strokeWidth' => '2',
            ],
        ];
    }
}
?>
