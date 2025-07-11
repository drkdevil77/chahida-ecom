<?php
// /admin/common/sidebar.php
?>
<aside id="sidebar" class="bg-gray-800 text-white w-64 min-h-screen p-4 fixed top-0 left-0 transform -translate-x-full md:translate-x-0 z-50 sidebar">
    <h2 class="text-2xl font-bold mb-10">Chahida BD</h2>
    <nav>
        <a href="index.php" class="flex items-center py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700">
            <i class="fas fa-tachometer-alt mr-3"></i> Dashboard
        </a>
        <a href="category.php" class="flex items-center py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700">
            <i class="fas fa-tags mr-3"></i> Categories
        </a>
        <a href="product.php" class="flex items-center py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700">
            <i class="fas fa-box mr-3"></i> Products
        </a>
        <a href="order.php" class="flex items-center py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700">
            <i class="fas fa-shopping-cart mr-3"></i> Orders
        </a>
        <a href="user.php" class="flex items-center py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700">
            <i class="fas fa-users mr-3"></i> Users
        </a>
        <a href="setting.php" class="flex items-center py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700">
            <i class="fas fa-cog mr-3"></i> Settings
        </a>
        <a href="logout.php" class="flex items-center py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700 mt-10">
            <i class="fas fa-sign-out-alt mr-3"></i> Logout
        </a>
    </nav>
</aside>
<div id="sidebar-overlay" class="fixed inset-0 bg-black opacity-50 hidden z-40 md:hidden" onclick="toggleSidebar()"></div>

<main class="flex-1 md:ml-64 p-4 pb-20"> <header class="md:hidden flex justify-between items-center mb-4 bg-white p-3 rounded-md shadow-sm">
        <button id="menu-button" onclick="toggleSidebar()">
            <i class="fas fa-bars text-2xl text-gray-700"></i>
        </button>
        <h1 class="text-xl font-bold text-purple-700">Admin Panel</h1>
        <div></div>
    </header>