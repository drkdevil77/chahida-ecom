<?php
// /order.php
include 'common/header.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch Active Orders (Placed, Dispatched)
$active_orders_res = $conn->query("
    SELECT o.*, oi.product_id, p.name as product_name, p.image as product_image
    FROM orders o
    JOIN (SELECT order_id, MIN(product_id) as product_id FROM order_items GROUP BY order_id) as first_item ON o.id = first_item.order_id
    JOIN order_items oi ON o.id = oi.order_id AND first_item.product_id = oi.product_id
    JOIN products p ON oi.product_id = p.id
    WHERE o.user_id = $user_id AND o.status IN ('Placed', 'Dispatched')
    ORDER BY o.created_at DESC
");


// Fetch Order History (Delivered, Cancelled)
$history_orders_res = $conn->query("
    SELECT o.*, oi.product_id, p.name as product_name, p.image as product_image
    FROM orders o
    JOIN (SELECT order_id, MIN(product_id) as product_id FROM order_items GROUP BY order_id) as first_item ON o.id = first_item.order_id
    JOIN order_items oi ON o.id = oi.order_id AND first_item.product_id = oi.product_id
    JOIN products p ON oi.product_id = p.id
    WHERE o.user_id = $user_id AND o.status IN ('Delivered', 'Cancelled')
    ORDER BY o.created_at DESC
");

function getStatusClass($status) {
    switch ($status) {
        case 'Placed': return 'text-blue-600';
        case 'Dispatched': return 'text-orange-500';
        case 'Delivered': return 'text-green-600';
        case 'Cancelled': return 'text-red-600';
        default: return 'text-gray-600';
    }
}
?>
<div class="p-4 pb-20"> <div class="flex items-center mb-4">
        <a href="profile.php" class="text-xl"><i class="fas fa-arrow-left"></i></a>
        <h1 class="text-xl font-bold text-gray-800 mx-auto">My Orders</h1>
        <div class="w-6"></div> </div>

    <div class="flex border-b mb-4">
        <button id="active-tab" onclick="showTab('active')" class="flex-1 py-2 text-center font-semibold text-green-600 border-b-2 border-green-600">Active Orders</button>
        <button id="history-tab" onclick="showTab('history')" class="flex-1 py-2 text-center font-semibold text-gray-500">Order History</button>
    </div>
    
    <?php if (isset($_GET['success'])): ?>
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
      <strong class="font-bold">Success!</strong>
      <span class="block sm:inline">Your order has been placed successfully.</span>
    </div>
    <?php endif; ?>

    <div id="active-content" class="space-y-4">
        <?php if($active_orders_res->num_rows > 0): while($order = $active_orders_res->fetch_assoc()): ?>
        <div class="bg-white rounded-lg shadow-md p-4">
            <div class="flex justify-between items-start">
                <div>
                    <span class="font-bold text-gray-800">Order #<?php echo $order['id']; ?></span>
                    <span class="block text-sm text-gray-500">Placed on: <?php echo date('d M Y', strtotime($order['created_at'])); ?></span>
                </div>
                <span class="font-bold text-lg <?php echo getStatusClass($order['status']); ?>"><?php echo $order['status']; ?></span>
            </div>
            <div class="flex items-center mt-3 border-t pt-3">
                <img src="uploads/<?php echo $order['product_image']; ?>" class="w-16 h-16 rounded-md object-cover">
                <div class="ml-3 flex-grow">
                    <p class="font-semibold text-gray-700"><?php echo $order['product_name']; ?>...</p>
                    <p class="text-sm text-gray-500">+ more items</p>
                </div>
                 <p class="text-lg font-bold text-gray-800">৳<?php echo number_format($order['total_amount']); ?></p>
            </div>
            
            <div class="mt-4">
                <div class="flex justify-between items-center text-xs text-center">
                    <div class="w-1/3">
                        <div class="relative mb-2">
                            <div class="w-8 h-8 mx-auto rounded-full flex items-center justify-center <?php echo in_array($order['status'], ['Placed', 'Dispatched', 'Delivered']) ? 'bg-green-500 text-white' : 'bg-gray-300'; ?>">
                                <i class="fas fa-check"></i>
                            </div>
                        </div>
                        <div class="text-gray-600 font-semibold">Placed</div>
                    </div>
                    <div class="w-1/3">
                        <div class="relative mb-2">
                             <div class="absolute w-full h-1 bg-gray-300 top-1/2 transform -translate-y-1/2"></div>
                             <div class="absolute w-full h-1 <?php echo in_array($order['status'], ['Dispatched', 'Delivered']) ? 'bg-green-500' : 'bg-gray-300'; ?> top-1/2 transform -translate-y-1/2"></div>
                            <div class="w-8 h-8 mx-auto rounded-full flex items-center justify-center relative z-10 <?php echo in_array($order['status'], ['Dispatched', 'Delivered']) ? 'bg-green-500 text-white' : 'bg-gray-300'; ?>">
                                <i class="fas fa-truck"></i>
                            </div>
                        </div>
                        <div class="text-gray-600 font-semibold">Dispatched</div>
                    </div>
                    <div class="w-1/3">
                        <div class="relative mb-2">
                             <div class="absolute w-full h-1 bg-gray-300 top-1/2 transform -translate-y-1/2"></div>
                             <div class="absolute w-1/2 h-1 <?php echo $order['status'] == 'Delivered' ? 'bg-green-500' : 'bg-gray-300'; ?> left-0 top-1/2 transform -translate-y-1/2"></div>
                            <div class="w-8 h-8 mx-auto rounded-full flex items-center justify-center relative z-10 <?php echo $order['status'] == 'Delivered' ? 'bg-green-500 text-white' : 'bg-gray-300'; ?>">
                                <i class="fas fa-box-open"></i>
                            </div>
                        </div>
                        <div class="text-gray-600 font-semibold">Delivered</div>
                    </div>
                </div>
            </div>
        </div>
        <?php endwhile; else: ?>
        <p class="text-center text-gray-500 pt-10">No active orders found.</p>
        <?php endif; ?>
    </div>

    <div id="history-content" class="space-y-4 hidden">
        <?php if($history_orders_res->num_rows > 0): while($order = $history_orders_res->fetch_assoc()): ?>
         <div class="bg-white rounded-lg shadow-md p-4 opacity-80">
            <div class="flex justify-between items-start">
                <div>
                    <span class="font-bold text-gray-800">Order #<?php echo $order['id']; ?></span>
                    <span class="block text-sm text-gray-500">Placed on: <?php echo date('d M Y', strtotime($order['created_at'])); ?></span>
                </div>
                <span class="font-bold text-lg <?php echo getStatusClass($order['status']); ?>"><?php echo $order['status']; ?></span>
            </div>
            <div class="flex items-center mt-3 border-t pt-3">
                <img src="uploads/<?php echo $order['product_image']; ?>" class="w-16 h-16 rounded-md object-cover">
                <div class="ml-3 flex-grow">
                    <p class="font-semibold text-gray-700"><?php echo $order['product_name']; ?>...</p>
                    <p class="text-sm text-gray-500">+ more items</p>
                </div>
                 <p class="text-lg font-bold text-gray-800">৳<?php echo number_format($order['total_amount']); ?></p>
            </div>
        </div>
        <?php endwhile; else: ?>
        <p class="text-center text-gray-500 pt-10">Your order history is empty.</p>
        <?php endif; ?>
    </div>

</div>

<script>
    const activeTab = document.getElementById('active-tab');
    const historyTab = document.getElementById('history-tab');
    const activeContent = document.getElementById('active-content');
    const historyContent = document.getElementById('history-content');

    function showTab(tabName) {
        if (tabName === 'active') {
            activeContent.classList.remove('hidden');
            historyContent.classList.add('hidden');
            activeTab.classList.add('text-green-600', 'border-green-600');
            historyTab.classList.remove('text-green-600', 'border-green-600');
        } else {
            historyContent.classList.remove('hidden');
            activeContent.classList.add('hidden');
            historyTab.classList.add('text-green-600', 'border-green-600');
            activeTab.classList.remove('text-green-600', 'border-green-600');
        }
    }
</script>

<?php include 'common/bottom.php'; ?>