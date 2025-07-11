<?php
// /common/bottom.php
?>
    <div class="h-16"></div> <nav class="fixed bottom-0 left-0 right-0 bg-white shadow-lg flex justify-around p-2 z-40">
        <a href="index.php" class="text-center text-gray-700 hover:text-green-600">
            <i class="fas fa-home text-2xl"></i>
            <span class="block text-xs">Home</span>
        </a>
        <a href="cart.php" class="text-center text-gray-700 hover:text-green-600 relative">
            <i class="fas fa-shopping-cart text-2xl"></i>
            <span class="block text-xs">Cart</span>
            <?php
            if (isset($_SESSION['cart'])) {
                $cart_count = count($_SESSION['cart']);
                if ($cart_count > 0) {
                    echo "<span class='absolute top-0 right-0 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center'>$cart_count</span>";
                }
            }
            ?>
        </a>
        <a href="profile.php" class="text-center text-gray-700 hover:text-green-600">
            <i class="fas fa-user text-2xl"></i>
            <span class="block text-xs">Profile</span>
        </a>
    </nav>

    <script>
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