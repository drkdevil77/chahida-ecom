<?php
// /admin/order_detail.php

// This entire PHP block MUST be at the very top of the file, before any HTML.
include '../common/config.php';

// --- AJAX handler for status update ---
if (isset($_POST['action']) && $_POST['action'] == 'update_status') {
    header('Content-Type: application/json');
    $order_id = $_POST['order_id'];
    $status = $_POST['status'];
    
    $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $order_id);
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Status updated successfully!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update status.']);
    }
    exit; // Stop the script after sending the JSON response
}


$order_id = $_GET['id'];
// Fetch order details
$order_sql = "SELECT o.*, u.name, u.email, u.phone 
              FROM orders o JOIN users u ON o.user_id = u.id 
              WHERE o.id = $order_id";
$order_res = $conn->query($order_sql);
$order = $order_res->fetch_assoc();

// Fetch order items
$items_sql = "SELECT oi.*, p.name as product_name, p.image as product_image 
              FROM order_items oi JOIN products p ON oi.product_id = p.id 
              WHERE oi.order_id = $order_id";
$items_res = $conn->query($items_sql);


// Start HTML output only after the AJAX block is finished
include 'common/header.php';
include 'common/sidebar.php';
?>

<a href="order.php" class="text-green-600 mb-4 inline-block"><i class="fas fa-arrow-left"></i> Back to Orders</a>
<h1 class="text-3xl font-bold text-gray-800 mb-6">Order Details #<?php echo $order_id; ?></h1>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-xl font-semibold mb-4">Items Ordered</h2>
        <div class="space-y-4">
            <?php while($item = $items_res->fetch_assoc()): ?>
            <div class="flex items-center border-b pb-4">
                <img src="../uploads/<?php echo $item['product_image']; ?>" class="w-20 h-20 rounded-lg object-cover">
                <div class="ml-4 flex-grow">
                    <h3 class="font-semibold"><?php echo $item['product_name']; ?></h3>
                    <p class="text-sm text-gray-600">Price: ৳<?php echo number_format($item['price']); ?></p>
                    <p class="text-sm text-gray-600">Quantity: <?php echo $item['quantity']; ?></p>
                </div>
                <div class="font-bold text-lg">
                    ৳<?php echo number_format($item['price'] * $item['quantity']); ?>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
        <div class="text-right mt-4">
             <span class="text-gray-600">Total Amount:</span>
             <span class="font-bold text-2xl text-green-700">৳<?php echo number_format($order['total_amount']); ?></span>
        </div>
    </div>
    
    <div class="lg:col-span-1 space-y-6">
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-xl font-semibold mb-4">Customer Details</h2>
            <p><strong>Name:</strong> <?php echo $order['name']; ?></p>
            <p><strong>Email:</strong> <?php echo $order['email']; ?></p>
            <p><strong>Phone:</strong> <?php echo $order['phone']; ?></p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-xl font-semibold mb-4">Update Status</h2>
            <form id="status-form">
                <input type="hidden" name="action" value="update_status">
                <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
                <select name="status" id="status-select" class="w-full p-2 border rounded">
                    <option value="Placed" <?php if($order['status']=='Placed') echo 'selected'; ?>>Placed</option>
                    <option value="Dispatched" <?php if($order['status']=='Dispatched') echo 'selected'; ?>>Dispatched</option>
                    <option value="Delivered" <?php if($order['status']=='Delivered') echo 'selected'; ?>>Delivered</option>
                    <option value="Cancelled" <?php if($order['status']=='Cancelled') echo 'selected'; ?>>Cancelled</option>
                </select>
                <button type="submit" class="mt-4 w-full bg-green-600 text-white py-2 rounded hover:bg-green-700">Update</button>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('status-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    document.getElementById('loading-overlay').style.display = 'flex';
    const formData = new FormData(e.target);
    
    try {
        const response = await fetch('order_detail.php', {
            method: 'POST',
            body: formData
        });
        const result = await response.json();
        
        document.getElementById('loading-overlay').style.display = 'none';
        alert(result.message);
        
        if(result.success) {
            location.reload();
        }
    } catch(error) {
        document.getElementById('loading-overlay').style.display = 'none';
        alert('An error occurred. Please check the browser console for details.');
        console.error('Update Error:', error);
    }
});
</script>

<?php include 'common/bottom.php'; ?>