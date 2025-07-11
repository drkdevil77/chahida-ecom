<?php
// /product.php
include 'common/header.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$cat_id = isset($_GET['cat_id']) ? intval($_GET['cat_id']) : 0;
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'new';
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

$where_clause = '';
$order_by_clause = 'ORDER BY created_at DESC';

if ($cat_id > 0) {
    $where_clause .= " WHERE cat_id = $cat_id";
}
if (!empty($search)) {
    $where_clause .= ($where_clause == '' ? ' WHERE' : ' AND') . " name LIKE '%$search%'";
}

if ($sort == 'price_asc') {
    $order_by_clause = 'ORDER BY price ASC';
} elseif ($sort == 'price_desc') {
    $order_by_clause = 'ORDER BY price DESC';
}

$sql = "SELECT * FROM products $where_clause $order_by_clause";
$products_result = $conn->query($sql);

$category_name = 'All Products';
if ($cat_id > 0) {
    $cat_res = $conn->query("SELECT name FROM categories WHERE id = $cat_id");
    if ($cat_res->num_rows > 0) {
        $category_name = $cat_res->fetch_assoc()['name'];
    }
}
?>
<div class="p-4">
    <div class="flex items-center mb-4">
        <a href="index.php" class="text-xl"><i class="fas fa-arrow-left"></i></a>
        <h1 class="text-xl font-bold text-gray-800 mx-auto"><?php echo htmlspecialchars($category_name); ?></h1>
    </div>

    <div class="flex justify-between items-center bg-white p-2 rounded-lg shadow-sm mb-4">
        <span class="text-gray-600">Filters:</span>
        <form id="filter-form" class="flex space-x-2">
            <input type="hidden" name="cat_id" value="<?php echo $cat_id; ?>">
            <select name="sort" onchange="document.getElementById('filter-form').submit();" class="border-none bg-gray-100 rounded p-1 text-sm focus:ring-0">
                <option value="new" <?php if ($sort == 'new') echo 'selected'; ?>>Sort by Newest</option>
                <option value="price_asc" <?php if ($sort == 'price_asc') echo 'selected'; ?>>Price: Low to High</option>
                <option value="price_desc" <?php if ($sort == 'price_desc') echo 'selected'; ?>>Price: High to Low</option>
            </select>
        </form>
    </div>

    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
        <?php
        if ($products_result->num_rows > 0) {
            while ($product = $products_result->fetch_assoc()) {
        ?>
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <a href="product_detail.php?id=<?php echo $product['id']; ?>">
                        <img src="uploads/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="w-full h-32 object-cover">
                        <div class="p-3">
                            <h3 class="font-semibold text-gray-800 truncate"><?php echo htmlspecialchars($product['name']); ?></h3>
                            <p class="text-green-600 font-bold">৳<?php echo number_format($product['price']); ?></p>
                            <span class="text-xs text-gray-500"><?php echo $product['stock'] > 0 ? 'In Stock' : 'Out of Stock'; ?></span>
                        </div>
                    </a>
                </div>
        <?php
            }
        } else {
            echo "<p class='text-gray-500 col-span-full text-center mt-8'>No products found in this category.</p>";
        }
        ?>
    </div>
</div>

<?php include 'common/bottom.php'; ?>