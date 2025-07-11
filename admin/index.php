<?php
// /admin/index.php
include 'common/header.php';
include 'common/sidebar.php';

// Fetch stats
$total_users = $conn->query("SELECT COUNT(*) as count FROM users")->fetch_assoc()['count'];
$total_orders = $conn->query("SELECT COUNT(*) as count FROM orders")->fetch_assoc()['count'];
$total_revenue = $conn->query("SELECT SUM(total_amount) as sum FROM orders WHERE status = 'Delivered'")->fetch_assoc()['sum'] ?? 0;
$active_products = $conn->query("SELECT COUNT(*) as count FROM products WHERE stock > 0")->fetch_assoc()['count'];
$cancellations = $conn->query("SELECT COUNT(*) as count FROM orders WHERE status = 'Cancelled'")->fetch_assoc()['count'];
$shipments = $conn->query("SELECT COUNT(*) as count FROM orders WHERE status = 'Dispatched'")->fetch_assoc()['count'];
?>

<h1 class="text-3xl font-bold text-gray-800 mb-6">Dashboard</h1>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
    <div class="bg-white p-6 rounded-xl shadow-lg flex items-center justify-between transform hover:scale-105 transition-transform duration-300">
        <div>
            <div class="text-sm text-gray-500">Total Revenue</div>
            <div class="text-3xl font-bold text-gray-800">৳<?php echo number_format($total_revenue, 2); ?></div>
        </div>
        <div class="bg-green-100 text-green-600 rounded-full p-3">
            <i class="fas fa-bangladeshi-taka-sign fa-2x"></i>
        </div>
    </div>
    <div class="bg-white p-6 rounded-xl shadow-lg flex items-center justify-between transform hover:scale-105 transition-transform duration-300">
        <div>
            <div class="text-sm text-gray-500">Total Orders</div>
            <div class="text-3xl font-bold text-gray-800"><?php echo $total_orders; ?></div>
        </div>
        <div class="bg-amber-100 text-amber-600 rounded-full p-3">
            <i class="fas fa-shopping-cart fa-2x"></i>
        </div>
    </div>
    <div class="bg-white p-6 rounded-xl shadow-lg flex items-center justify-between transform hover:scale-105 transition-transform duration-300">
        <div>
            <div class="text-sm text-gray-500">Total Users</div>
            <div class="text-3xl font-bold text-gray-800"><?php echo $total_users; ?></div>
        </div>
        <div class="bg-teal-100 text-teal-600 rounded-full p-3">
            <i class="fas fa-users fa-2x"></i>
        </div>
    </div>
    <div class="bg-white p-6 rounded-xl shadow-lg flex items-center justify-between transform hover:scale-105 transition-transform duration-300">
        <div>
            <div class="text-sm text-gray-500">Active Products</div>
            <div class="text-3xl font-bold text-gray-800"><?php echo $active_products; ?></div>
        </div>
        <div class="bg-indigo-100 text-indigo-600 rounded-full p-3">
            <i class="fas fa-box fa-2x"></i>
        </div>
    </div>
    <div class="bg-white p-6 rounded-xl shadow-lg flex items-center justify-between transform hover:scale-105 transition-transform duration-300">
        <div>
            <div class="text-sm text-gray-500">Shipments</div>
            <div class="text-3xl font-bold text-gray-800"><?php echo $shipments; ?></div>
        </div>
        <div class="bg-orange-100 text-orange-600 rounded-full p-3">
            <i class="fas fa-truck fa-2x"></i>
        </div>
    </div>
    <div class="bg-white p-6 rounded-xl shadow-lg flex items-center justify-between transform hover:scale-105 transition-transform duration-300">
        <div>
            <div class="text-sm text-gray-500">Cancellations</div>
            <div class="text-3xl font-bold text-gray-800"><?php echo $cancellations; ?></div>
        </div>
        <div class="bg-red-100 text-red-600 rounded-full p-3">
            <i class="fas fa-times-circle fa-2x"></i>
        </div>
    </div>
</div>

<div class="mt-8">
    <h2 class="text-2xl font-bold text-gray-800 mb-4">Quick Actions</h2>
    <div class="flex flex-wrap gap-4">
        <a href="product.php" class="bg-green-600 text-white font-bold py-3 px-6 rounded-lg hover:bg-green-700 transition-colors">
            <i class="fas fa-plus mr-2"></i> Add Product
        </a>
        <a href="order.php" class="bg-gray-700 text-white font-bold py-3 px-6 rounded-lg hover:bg-gray-800 transition-colors">
            <i class="fas fa-list-alt mr-2"></i> Manage Orders
        </a>
         <a href="user.php" class="bg-gray-700 text-white font-bold py-3 px-6 rounded-lg hover:bg-gray-800 transition-colors">
            <i class="fas fa-user-cog mr-2"></i> Manage Users
        </a>
    </div>
</div>

<?php
include 'common/bottom.php';
?>