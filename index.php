<?php
// /index.php
include 'common/header.php';

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
?>

<div class="p-4">
    <header class="flex justify-between items-center mb-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Hi, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h1>
            <p class="text-gray-500">What are you looking for today?</p>
        </div>
        <div class="relative">
             <a href="profile.php"><i class="fas fa-user-circle text-3xl text-green-600"></i></a>
        </div>
    </header>

    <div class="relative mb-6">
        <input type="text" placeholder="Search for products..." class="w-full pl-10 pr-4 py-3 rounded-full bg-white shadow-md focus:outline-none focus:ring-2 focus:ring-green-500">
        <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
    </div>

    <section class="mb-6">
        <h2 class="text-xl font-semibold mb-3 text-gray-700">Categories</h2>
        <div class="flex space-x-4 overflow-x-auto pb-4">
            <?php
            $cat_result = $conn->query("SELECT * FROM categories LIMIT 8");
            if ($cat_result->num_rows > 0) {
                while ($cat = $cat_result->fetch_assoc()) {
            ?>
                    <a href="product.php?cat_id=<?php echo $cat['id']; ?>" class="flex-shrink-0 text-center">
                        <div class="w-16 h-16 bg-white rounded-full shadow-md flex items-center justify-center">
                            <img src="uploads/<?php echo htmlspecialchars($cat['image']); ?>" alt="<?php echo htmlspecialchars($cat['name']); ?>" class="w-10 h-10 object-cover">
                        </div>
                        <span class="mt-2 text-sm text-gray-600 block"><?php echo htmlspecialchars($cat['name']); ?></span>
                    </a>
            <?php
                }
            } else {
                echo "<p class='text-gray-500'>No categories found.</p>";
            }
            ?>
        </div>
    </section>

    <section>
        <h2 class="text-xl font-semibold mb-3 text-gray-700">Featured Products</h2>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
            <?php
            $prod_result = $conn->query("SELECT * FROM products ORDER BY RAND() LIMIT 8");
            if ($prod_result->num_rows > 0) {
                while ($product = $prod_result->fetch_assoc()) {
            ?>
                    <div class="bg-white rounded-lg shadow-md overflow-hidden">
                        <a href="product_detail.php?id=<?php echo $product['id']; ?>">
                            <img src="uploads/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="w-full h-32 object-cover">
                            <div class="p-3">
                                <h3 class="font-semibold text-gray-800 truncate"><?php echo htmlspecialchars($product['name']); ?></h3>
                                <p class="text-green-600 font-bold">৳<?php echo number_format($product['price']); ?></p>
                                <button class="mt-2 w-full bg-green-100 text-green-700 text-sm font-semibold py-1 rounded-lg hover:bg-green-200 transition">
                                    View Details
                                </button>
                            </div>
                        </a>
                    </div>
            <?php
                }
            } else {
                 echo "<p class='text-gray-500 col-span-2'>No products found. Please ask the admin to add some!</p>";
            }
            ?>
        </div>
    </section>
</div>

<?php include 'common/bottom.php'; ?>