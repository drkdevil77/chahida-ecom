<?php
// /product_detail.php
include 'common/header.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// --- Handle Add to Cart ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_to_cart'])) {
    $p_id = $_POST['product_id'];
    $p_qty = $_POST['quantity'];

    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // Check if product already in cart
    if (isset($_SESSION['cart'][$p_id])) {
        $_SESSION['cart'][$p_id]['quantity'] += $p_qty;
    } else {
        $_SESSION['cart'][$p_id] = ['quantity' => $p_qty];
    }
    
    // Use JS to show a success message as we cannot redirect and show a message easily
    echo "<script>alert('Product added to cart!'); window.location.href='cart.php';</script>";
    exit();
}


$sql = "SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.cat_id = c.id WHERE p.id = $product_id";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    echo "Product not found.";
    exit;
}

$product = $result->fetch_assoc();
?>

<div class="bg-white min-h-screen">
    <div class="fixed top-0 left-0 right-0 bg-white bg-opacity-80 backdrop-blur-sm z-30 p-4 flex items-center shadow-sm">
        <a href="javascript:history.back()" class="text-xl"><i class="fas fa-arrow-left"></i></a>
        <h1 class="text-xl font-bold text-gray-800 mx-auto">Product Details</h1>
    </div>

    <div class="pt-20"> <div class="relative">
            <img id="main-image" src="uploads/<?php echo htmlspecialchars($product['image']); ?>" class="w-full h-64 object-cover">
            </div>

        <div class="p-4">
            <span class="text-sm text-green-600 font-semibold"><?php echo htmlspecialchars($product['category_name']); ?></span>
            <h2 class="text-2xl font-bold text-gray-800 mt-1"><?php echo htmlspecialchars($product['name']); ?></h2>
            <div class="flex justify-between items-center mt-2">
                <p class="text-3xl font-bold text-green-600">৳<?php echo number_format($product['price']); ?></p>
                <span class="px-3 py-1 text-sm rounded-full <?php echo $product['stock'] > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                    <?php echo $product['stock'] > 0 ? 'In Stock' : 'Out of Stock'; ?>
                </span>
            </div>

            <div class="mt-6">
                <h3 class="font-semibold text-gray-800 mb-2">Description</h3>
                <p class="text-gray-600 text-sm leading-relaxed">
                    <?php echo nl2br(htmlspecialchars($product['description'])); ?>
                </p>
            </div>
        </div>
    </div>

    <div class="fixed bottom-0 left-0 right-0 bg-white p-4 shadow-lg border-t">
        <form method="POST" action="">
            <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
            <input type="hidden" name="add_to_cart" value="1">
            <div class="flex items-center justify-between">
                <div class="flex items-center border rounded-lg">
                    <button type="button" onclick="changeQty(-1)" class="px-3 py-2 text-lg font-bold">-</button>
                    <input id="quantity-input" name="quantity" type="text" value="1" readonly class="w-12 text-center border-l border-r">
                    <button type="button" onclick="changeQty(1)" class="px-3 py-2 text-lg font-bold">+</button>
                </div>
                <button type="submit" class="flex-1 ml-4 bg-green-600 text-white font-bold py-3 rounded-lg hover:bg-green-700 transition">
                    <i class="fas fa-cart-plus mr-2"></i> Add to Cart
                </button>
            </div>
        </form>
    </div>
</div>
<div class="h-24"></div> <script>
    const maxStock = <?php echo $product['stock']; ?>;
    const qtyInput = document.getElementById('quantity-input');

    function changeQty(amount) {
        let currentQty = parseInt(qtyInput.value);
        let newQty = currentQty + amount;

        if (newQty < 1) newQty = 1;
        if (newQty > maxStock) newQty = maxStock;

        qtyInput.value = newQty;
    }
</script>

<?php $conn->close(); ?>