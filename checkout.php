<?php
// /checkout.php
include 'common/config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if (empty($_SESSION['cart'])) {
    header('Location: cart.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$user_res = $conn->query("SELECT * FROM users WHERE id = $user_id");
$user_data = $user_res->fetch_assoc();

// --- Process Order ---
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $conn->begin_transaction();
    try {
        // 1. Calculate total amount again on server-side
        $total_amount = 0;
        $product_ids = implode(',', array_keys($_SESSION['cart']));
        $sql = "SELECT id, price, stock FROM products WHERE id IN ($product_ids)";
        $result = $conn->query($sql);
        $products_data = [];
        while ($row = $result->fetch_assoc()) {
            $products_data[$row['id']] = $row;
        }

        foreach ($_SESSION['cart'] as $pid => $item) {
            // Check stock availability
            if ($item['quantity'] > $products_data[$pid]['stock']) {
                throw new Exception("Product #$pid is out of stock!");
            }
            $total_amount += $products_data[$pid]['price'] * $item['quantity'];
        }

        // 2. Create Order
        $stmt_order = $conn->prepare("INSERT INTO orders (user_id, total_amount, status) VALUES (?, ?, 'Placed')");
        $stmt_order->bind_param("id", $user_id, $total_amount);
        $stmt_order->execute();
        $order_id = $stmt_order->insert_id;

        // 3. Insert Order Items and Update Stock
        $stmt_items = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
        $stmt_stock = $conn->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");

        foreach ($_SESSION['cart'] as $pid => $item) {
            $price = $products_data[$pid]['price'];
            $quantity = $item['quantity'];
            
            $stmt_items->bind_param("iiid", $order_id, $pid, $quantity, $price);
            $stmt_items->execute();

            $stmt_stock->bind_param("ii", $quantity, $pid);
            $stmt_stock->execute();
        }

        // 4. Commit transaction
        $conn->commit();

        // 5. Clear cart and redirect
        unset($_SESSION['cart']);
        header("Location: order.php?success=1&order_id=" . $order_id);
        exit();

    } catch (Exception $e) {
        $conn->rollback();
        // Handle error, e.g., show a message
        die("Order failed: " . $e->getMessage());
    }
}

include 'common/header.php';
?>
<div class="p-4">
    <div class="flex items-center mb-6">
        <a href="cart.php" class="text-xl"><i class="fas fa-arrow-left"></i></a>
        <h1 class="text-xl font-bold text-gray-800 mx-auto">Checkout</h1>
        <div class="w-6"></div> </div>

    <form method="POST" class="bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-lg font-semibold mb-4 border-b pb-2">Shipping Information</h2>
        <div class="mb-4">
            <label class="block text-gray-700">Full Name</label>
            <input type="text" value="<?php echo htmlspecialchars($user_data['name']); ?>" class="w-full px-4 py-2 border rounded-lg bg-gray-100" readonly>
        </div>
        <div class="mb-4">
            <label class="block text-gray-700">Phone Number</label>
            <input type="text" value="<?php echo htmlspecialchars($user_data['phone']); ?>" class="w-full px-4 py-2 border rounded-lg bg-gray-100" readonly>
        </div>
        <div class="mb-4">
            <label class="block text-gray-700">Shipping Address</label>
            <textarea rows="3" placeholder="Enter your full address" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-600" required></textarea>
        </div>

        <h2 class="text-lg font-semibold mt-6 mb-4 border-b pb-2">Payment Method</h2>
        <div class="bg-green-50 border border-green-200 p-4 rounded-lg flex items-center">
            <input type="radio" id="cod" name="payment_method" value="COD" class="h-4 w-4 text-green-600 border-gray-300 focus:ring-green-500" checked>
            <label for="cod" class="ml-3 block text-sm font-medium text-gray-700">
                <i class="fas fa-money-bill-wave mr-2"></i> Cash on Delivery (COD)
            </label>
        </div>
        <p class="text-xs text-gray-500 mt-2">Pay with cash upon delivery of your order.</p>
        
        <div class="fixed bottom-16 left-0 right-0 bg-white p-4 shadow-lg border-t">
            <button type="submit" class="w-full bg-green-600 text-white font-bold py-3 rounded-lg hover:bg-green-700 transition">
                Place Order
            </button>
        </div>
    </form>
</div>
<div class="h-24"></div> <?php include 'common/bottom.php'; ?>