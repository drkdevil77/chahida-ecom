<?php
// /cart.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include 'common/config.php';

// --- AJAX handler for cart actions ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    $response = ['success' => false];
    $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;

    if ($product_id > 0 && isset($_SESSION['cart'][$product_id])) {
        // --- REMOVE ITEM ---
        if ($_POST['action'] == 'remove') {
            unset($_SESSION['cart'][$product_id]);
            $response['success'] = true;
        }
        // --- UPDATE QUANTITY ---
        elseif ($_POST['action'] == 'update') {
            $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;
            if ($quantity > 0) {
                // Check against stock
                $res = $conn->query("SELECT stock FROM products WHERE id = $product_id");
                $product = $res->fetch_assoc();
                if ($quantity <= $product['stock']) {
                    $_SESSION['cart'][$product_id]['quantity'] = $quantity;
                    $response['success'] = true;
                } else {
                    $response['message'] = 'Quantity exceeds available stock!';
                }
            } else {
                // If quantity is 0 or less, remove it
                unset($_SESSION['cart'][$product_id]);
                $response['success'] = true;
            }
        }
    }

    // --- Recalculate total for response ---
    $total = 0;
    if (!empty($_SESSION['cart'])) {
        $product_ids = implode(',', array_keys($_SESSION['cart']));
        $cart_products_result = $conn->query("SELECT id, price FROM products WHERE id IN ($product_ids)");
        $products_data = [];
        while($row = $cart_products_result->fetch_assoc()) {
            $products_data[$row['id']] = $row;
        }
        foreach ($_SESSION['cart'] as $pid => $item) {
            $total += $products_data[$pid]['price'] * $item['quantity'];
        }
    }
    $response['total'] = '৳' . number_format($total);
    $response['cart_count'] = count($_SESSION['cart']);

    echo json_encode($response);
    exit();
}

include 'common/header.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$cart_items = [];
$total_amount = 0;
if (!empty($_SESSION['cart'])) {
    $product_ids = implode(',', array_keys($_SESSION['cart']));
    $sql = "SELECT id, name, price, image, stock FROM products WHERE id IN ($product_ids)";
    $result = $conn->query($sql);
    while ($row = $result->fetch_assoc()) {
        $row['quantity'] = $_SESSION['cart'][$row['id']]['quantity'];
        $cart_items[] = $row;
        $total_amount += $row['price'] * $row['quantity'];
    }
}
?>
<div class="p-4">
    <div class="flex items-center mb-6">
        <a href="javascript:history.back()" class="text-xl"><i class="fas fa-arrow-left"></i></a>
        <h1 class="text-xl font-bold text-gray-800 mx-auto">My Cart</h1>
        <div class="w-6"></div> </div>

    <div id="cart-container">
        <?php if (empty($cart_items)): ?>
            <div class="text-center py-20">
                <i class="fas fa-shopping-cart text-6xl text-gray-300"></i>
                <h2 class="mt-4 text-xl font-semibold text-gray-700">Your Cart is Empty</h2>
                <p class="text-gray-500 mt-2">Looks like you haven't added anything to your cart yet.</p>
                <a href="index.php" class="mt-6 inline-block bg-green-600 text-white px-6 py-2 rounded-lg font-semibold hover:bg-green-700">Shop Now</a>
            </div>
        <?php else: ?>
            <div class="space-y-4">
                <?php foreach ($cart_items as $item): ?>
                    <div id="item-<?php echo $item['id']; ?>" class="flex items-center bg-white p-3 rounded-lg shadow-sm">
                        <img src="uploads/<?php echo htmlspecialchars($item['image']); ?>" class="w-20 h-20 rounded-md object-cover">
                        <div class="flex-grow ml-4">
                            <h3 class="font-semibold text-gray-800"><?php echo htmlspecialchars($item['name']); ?></h3>
                            <p class="text-green-600 font-bold">৳<?php echo number_format($item['price']); ?></p>
                            <div class="flex items-center mt-2">
                                <label class="text-sm mr-2">Qty:</label>
                                <input type="number" value="<?php echo $item['quantity']; ?>" min="1" max="<?php echo $item['stock']; ?>" 
                                       onchange="updateCart(<?php echo $item['id']; ?>, this.value)"
                                       class="w-16 border rounded px-2 py-1">
                            </div>
                        </div>
                        <button onclick="removeFromCart(<?php echo $item['id']; ?>)" class="text-gray-400 hover:text-red-500 ml-2">
                            <i class="fas fa-trash-alt text-lg"></i>
                        </button>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="fixed bottom-16 left-0 right-0 bg-white p-4 shadow-lg border-t <?php echo empty($cart_items) ? 'hidden' : ''; ?>" id="cart-footer">
    <div class="flex justify-between items-center mb-4">
        <span class="text-gray-600 font-semibold">Total Amount:</span>
        <span id="total-amount" class="text-2xl font-bold text-green-600">৳<?php echo number_format($total_amount); ?></span>
    </div>
    <a href="checkout.php" class="block w-full text-center bg-green-600 text-white font-bold py-3 rounded-lg hover:bg-green-700 transition">
        Proceed to Checkout
    </a>
</div>
<div class="h-40"></div> <script>
    const loadingOverlay = document.getElementById('loading-overlay');

    async function handleCartAction(action, productId, quantity = null) {
        loadingOverlay.style.display = 'flex';
        const formData = new FormData();
        formData.append('action', action);
        formData.append('product_id', productId);
        if (quantity !== null) {
            formData.append('quantity', quantity);
        }

        const response = await fetch('cart.php', { method: 'POST', body: formData });
        const result = await response.json();
        
        if(result.success) {
            document.getElementById('total-amount').textContent = result.total;
            // Update cart count in bottom nav (optional, requires more DOM manipulation)
        } else if (result.message) {
            alert(result.message);
            // Optionally revert the quantity input
        }
        loadingOverlay.style.display = 'none';
        return result;
    }

    async function removeFromCart(productId) {
        if (!confirm('Are you sure you want to remove this item?')) return;

        const result = await handleCartAction('remove', productId);
        if (result.success) {
            const itemElement = document.getElementById('item-' + productId);
            itemElement.style.transition = 'opacity 0.5s';
            itemElement.style.opacity = '0';
            setTimeout(() => {
                itemElement.remove();
                if (result.cart_count === 0) {
                   location.reload(); // Reload to show empty cart message
                }
            }, 500);
        }
    }

    async function updateCart(productId, quantity) {
        await handleCartAction('update', productId, quantity);
    }
</script>

<?php include 'common/bottom.php'; ?>