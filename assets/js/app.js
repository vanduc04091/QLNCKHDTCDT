/**
 * app.js - Helper dùng chung cho toàn hệ thống
 */
var APP = (function ($) {
    'use strict';

    /* ============ TOAST ============ */
    function ensureToastContainer() {
        var c = document.querySelector('.toast-container');
        if (!c) {
            c = document.createElement('div');
            c.className = 'toast-container';
            document.body.appendChild(c);
        }
        return c;
    }

    function toast(message, type) {
        type = type || 'info';
        var c = ensureToastContainer();
        var icon = {success: '✓', error: '✕', warning: '⚠', info: 'ℹ'}[type] || 'ℹ';
        var el = document.createElement('div');
        el.className = 'toast ' + type;
        el.innerHTML = '<span class="icon">' + icon + '</span><span>' + escapeHtml(message) + '</span>';
        c.appendChild(el);
        setTimeout(function () {
            el.style.transition = 'opacity .3s, transform .3s';
            el.style.opacity = 0;
            el.style.transform = 'translateX(100%)';
            setTimeout(function () { el.remove(); }, 300);
        }, 3500);
    }

    /* ============ CONFIRM DIALOG ============ */
    function confirmDialog(message, onYes, opts) {
        opts = opts || {};
        var title = opts.title || 'Xác nhận';
        var yesText = opts.yesText || 'Đồng ý';
        var noText = opts.noText || 'Hủy';
        var yesClass = opts.yesClass || 'btn-danger';

        var bd = document.createElement('div');
        bd.className = 'modal-backdrop open';
        bd.innerHTML =
            '<div class="modal" style="max-width:440px">' +
                '<div class="modal-header"><h3>' + escapeHtml(title) + '</h3>' +
                '<button class="close" type="button">&times;</button></div>' +
                '<div class="modal-body">' + escapeHtml(message) + '</div>' +
                '<div class="modal-footer">' +
                    '<button class="btn" data-a="no">' + escapeHtml(noText) + '</button>' +
                    '<button class="btn ' + yesClass + '" data-a="yes">' + escapeHtml(yesText) + '</button>' +
                '</div>' +
            '</div>';
        document.body.appendChild(bd);
        function close() { bd.remove(); }
        bd.querySelector('[data-a=yes]').onclick = function () { close(); onYes && onYes(); };
        bd.querySelector('[data-a=no]').onclick = close;
        bd.querySelector('.close').onclick = close;
        bd.addEventListener('click', function (e) { if (e.target === bd) close(); });
    }

    /* ============ AJAX ============ */
    function ajax(url, data, options) {
        options = options || {};
        var headers = options.headers || {};
        if (typeof window.CSRF_TOKEN !== 'undefined' && window.CSRF_TOKEN) {
            headers['X-CSRF-Token'] = window.CSRF_TOKEN;
        }
        return $.ajax({
            url: url,
            type: options.type || 'POST',
            dataType: 'json',
            data: data,
            headers: headers
        }).fail(function (xhr) {
            var msg = 'Lỗi kết nối máy chủ';
            try {
                var res = JSON.parse(xhr.responseText);
                if (res && res.message) msg = res.message;
            } catch (e) {}
            if (xhr.status === 401) {
                toast('Phiên đăng nhập đã hết hạn. Đang chuyển về trang đăng nhập...', 'warning');
                setTimeout(function () { location.href = 'login.php'; }, 1500);
                return;
            }
            toast(msg, 'error');
        });
    }

    /* ============ LOADING ============ */
    function showLoading(selector) {
        var $el = $(selector);
        if (!$el.find('.loading-overlay').length) {
            $el.css('position', 'relative').append('<div class="loading-overlay"><div class="spinner"></div></div>');
        }
        $el.find('.loading-overlay').addClass('show');
    }
    function hideLoading(selector) {
        $(selector).find('.loading-overlay').removeClass('show');
    }

    /* ============ UTILITIES ============ */
    function escapeHtml(str) {
        if (str === null || str === undefined) return '';
        return String(str)
            .replace(/&/g, '&amp;').replace(/</g, '&lt;')
            .replace(/>/g, '&gt;').replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function debounce(fn, wait) {
        var t;
        return function () {
            var ctx = this, args = arguments;
            clearTimeout(t);
            t = setTimeout(function () { fn.apply(ctx, args); }, wait);
        };
    }

    function formatDateTime(str) {
        if (!str) return '';
        var d = new Date(str.replace(' ', 'T'));
        if (isNaN(d)) return str;
        var pad = function (n) { return n < 10 ? '0' + n : n; };
        return pad(d.getDate()) + '/' + pad(d.getMonth() + 1) + '/' + d.getFullYear()
            + ' ' + pad(d.getHours()) + ':' + pad(d.getMinutes());
    }

    function formatDate(str) {
        if (!str) return '';
        var d = new Date(str.replace(' ', 'T'));
        if (isNaN(d)) return str;
        var pad = function (n) { return n < 10 ? '0' + n : n; };
        return pad(d.getDate()) + '/' + pad(d.getMonth() + 1) + '/' + d.getFullYear();
    }

    /* ============ PAGINATION RENDER ============ */
    function renderPagination(info, onClick) {
        if (!info) return '';
        var cur = info.currentPage, total = info.totalPages;
        if (total <= 1) return '<div></div>';
        var html = '<ul class="pagination">';
        html += '<li><button ' + (cur <= 1 ? 'disabled' : '') + ' data-p="' + (cur - 1) + '">‹</button></li>';
        var start = Math.max(1, cur - 2);
        var end = Math.min(total, cur + 2);
        if (start > 1) {
            html += '<li><button data-p="1">1</button></li>';
            if (start > 2) html += '<li><button disabled>...</button></li>';
        }
        for (var i = start; i <= end; i++) {
            html += '<li class="' + (i === cur ? 'active' : '') + '"><button data-p="' + i + '">' + i + '</button></li>';
        }
        if (end < total) {
            if (end < total - 1) html += '<li><button disabled>...</button></li>';
            html += '<li><button data-p="' + total + '">' + total + '</button></li>';
        }
        html += '<li><button ' + (cur >= total ? 'disabled' : '') + ' data-p="' + (cur + 1) + '">›</button></li>';
        html += '</ul>';
        return html;
    }

    /* ============ SIDEBAR TOGGLE (MOBILE) ============ */
    $(document).on('click', '.sidebar-toggle', function () {
        $('.sidebar').toggleClass('show');
    });

    /* ============ USER DROPDOWN ============ */
    $(document).on('click', '.user-menu .avatar, .user-menu .user-info', function (e) {
        e.stopPropagation();
        $(this).closest('.dropdown').toggleClass('open');
    });
    $(document).on('click', function () { $('.user-menu .dropdown').removeClass('open'); });

    return {
        toast: toast,
        confirm: confirmDialog,
        ajax: ajax,
        showLoading: showLoading,
        hideLoading: hideLoading,
        escape: escapeHtml,
        debounce: debounce,
        formatDate: formatDate,
        formatDateTime: formatDateTime,
        renderPagination: renderPagination
    };
})(jQuery);
