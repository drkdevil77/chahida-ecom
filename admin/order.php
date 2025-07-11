<?php
// /admin/order.php
include 'common/header.php';
include 'common/sidebar.php';
?>

<h1 class="text-3xl font-bold text-gray-800 mb-6">Order Management</h1>

<div class="bg-white p-6 rounded-lg shadow-md">
    <div class="overflow-x-auto">
        <table class="min-w-full bg-white">
            <thead class="bg-gray-800 text-white">
                <tr>
                    <th class="py-3 px-4 uppercase font-semibold text-sm">Order ID</th>
                    <th class="py-3 px-4 uppercase font-semibold text-sm">User</th>
                    <th class="py-3 px-4 uppercase font-semibold text-sm">Amount</th>
                    <th class="py-3 px-4 uppercase font-semibold text-sm">Status</th>
                    <th class="py-3 px-4 uppercase font-semibold text-sm">Date</th>
                    <th class="py-3 px-4 uppercase font-semibold text-sm">Actions</th>
                </tr>
            </thead>
            <tbody class="text-gray-700">
                <?php
                $sql = "SELECT o.id, u.name as user_name, o.total_amount, o.status, o.created_at 
                        FROM orders o JOIN users u ON o.user_id = u.id 
                        ORDER BY o.created_at DESC";
                $orders_res = $conn->query($sql);
                while ($order = $orders_res->fetch_assoc()):
                ?>
                <tr class="border-b">
                    <td class="py-3 px-4 text-center">#<?php echo $order['id']; ?></td>
                    <td class="py-3 px-4"><?php echo htmlspecialchars($order['user_name']); ?></td>
                    <td class="py-3 px-4 text-center">৳<?php echo number_format($order['total_amount']); ?></td>
                    <td class="py-3 px-4 text-center">
                        <span class="px-2 py-1 font-semibold leading-tight text-sm rounded-full
                            <?php if($order['status'] == 'Placed') echo 'bg-blue-100 text-blue-800';
                                  elseif($order['status'] == 'Dispatched') echo 'bg-orange-100 text-orange-800';
                                  elseif($order['status'] == 'Delivered') echo 'bg-green-100 text-green-800';
                                  else echo 'bg-red-100 text-red-800'; ?>">
                            <?php echo $order['status']; ?>
                        </span>
                    </td>
                    <td class="py-3 px-4 text-center"><?php echo date('d M Y', strtotime($order['created_at'])); ?></td>
                    <td class="py-3 px-4 text-center">
                        <a href="order_detail.php?id=<?php echo $order['id']; ?>" class="text-green-600 hover:text-green-900">View Details</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'common/bottom.php'; ?>