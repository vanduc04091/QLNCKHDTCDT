    </main>

    <footer class="public-footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-brand">
                    <div class="logo">QL</div>
                    <div>
                        <div class="brand-name">QL NCKH-ĐT-CĐT</div>
                        <div class="brand-tag">Nền tảng quản lý hiện đại</div>
                    </div>
                </div>
                <div class="footer-links">
                    <div class="footer-section">
                        <h4>Tính năng</h4>
                        <ul>
                            <li><a href="#features">Đào tạo</a></li>
                            <li><a href="#features">Nghiên cứu khoa học</a></li>
                            <li><a href="#features">Quản lý nhân sự</a></li>
                            <li><a href="#features">Hệ thống</a></li>
                        </ul>
                    </div>
                    <div class="footer-section">
                        <h4>Hỗ trợ</h4>
                        <ul>
                            <li><a href="mailto:support@example.com">Liên hệ</a></li>
                            <li><a href="#features">Hướng dẫn</a></li>
                            <li><a href="#features">FAQ</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; <?= date('Y') ?> <?= Helper::h(AppConfig::APP_NAME) ?>. Tất cả quyền được bảo lưu.</p>
                <div class="footer-version">Phiên bản v<?= AppConfig::APP_VERSION ?></div>
            </div>
        </div>
    </footer>

    <script>
        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Navbar scroll effect
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.public-header');
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });
    </script>
</body>
</html>