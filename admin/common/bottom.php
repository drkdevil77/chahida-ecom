<?php
// /admin/common/bottom.php
?>
</main> </div> <script>
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebar-overlay');

    function toggleSidebar() {
        sidebar.classList.toggle('-translate-x-full');
        overlay.classList.toggle('hidden');
    }

    // Disable Right-Click, Text Selection, Zoom
    document.addEventListener('contextmenu', event => event.preventDefault());
    document.addEventListener('keydown', function (e) {
        if ((e.ctrlKey && e.key === '+') || (e.ctrlKey && e.key === '-')) {
            e.preventDefault();
        }
    });
    window.addEventListener('wheel', e => {
        if (e.ctrlKey) {
            e.preventDefault();
        }
    }, { passive: false });
</script>
</body>
</html>