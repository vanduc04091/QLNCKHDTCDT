        </main>
    </div>
</div>

<script>
(function () {
    var $btn = document.getElementById('sidebarToggle');
    if (!$btn) return;

    // Sync class tu html init -> body de transition CSS hoat dong
    if (document.documentElement.classList.contains('sidebar-collapsed-init')) {
        document.body.classList.add('sidebar-collapsed');
        document.documentElement.classList.remove('sidebar-collapsed-init');
    }

    $btn.addEventListener('click', function () {
        var collapsed = document.body.classList.toggle('sidebar-collapsed');
        try { localStorage.setItem('sidebarCollapsed', collapsed ? '1' : '0'); } catch (_) {}
    });
})();
</script>

</body>
</html>
