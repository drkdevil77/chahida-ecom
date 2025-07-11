<?php
// /admin/product.php
include 'common/header.php';

$edit_product = null;
if (isset($_GET['edit_id'])) {
    $id = $_GET['edit_id'];
    $result = $conn->query("SELECT * FROM products WHERE id = $id");
    if ($result->num_rows > 0) {
        $edit_product = $result->fetch_assoc();
    }
}

if (isset($_POST['save_product'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $cat_id = $_POST['cat_id'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $image_name = $_POST['existing_image'] ?? '';

    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "../uploads/";
        $image_name = time() . '_' . basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $image_name;
        move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);
    }
    
    if (empty($id)) { // ADD
        $stmt = $conn->prepare("INSERT INTO products (name, cat_id, description, price, stock, image) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sisdis", $name, $cat_id, $description, $price, $stock, $image_name);
    } else { // UPDATE
        $stmt = $conn->prepare("UPDATE products SET name=?, cat_id=?, description=?, price=?, stock=?, image=? WHERE id=?");
        $stmt->bind_param("sisdisi", $name, $cat_id, $description, $price, $stock, $image_name, $id);
    }

    if ($stmt->execute()) {
        echo "<script>alert('Product saved successfully!'); window.location.href='product.php';</script>";
    } else {
        echo "<script>alert('Error: " . $stmt->error . "');</script>";
    }
    exit;
}

if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];
    $conn->query("DELETE FROM products WHERE id = $id");
    echo "<script>alert('Product deleted!'); window.location.href='product.php';</script>";
    exit;
}

include 'common/sidebar.php';
?>

<h1 class="text-3xl font-bold text-gray-800 mb-6"><?php echo $edit_product ? 'Edit Product' : 'Manage Products'; ?></h1>

<div class="bg-white p-6 rounded-lg shadow-md mb-8">
    <h2 class="text-xl font-semibold mb-4"><?php echo $edit_product ? 'Update Product Details' : 'Add a New Product'; ?></h2>
    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?php echo $edit_product['id'] ?? ''; ?>">
        <input type="hidden" name="existing_image" value="<?php echo $edit_product['image'] ?? ''; ?>">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <input type="text" name="name" placeholder="Product Name" value="<?php echo $edit_product['name'] ?? ''; ?>" class="w-full p-2 border rounded" required>
            <select name="cat_id" class="w-full p-2 border rounded" required>
                <option value="">Select Category</option>
                <?php
                $cats = $conn->query("SELECT * FROM categories");
                while ($cat = $cats->fetch_assoc()) {
                    $selected = (isset($edit_product) && $edit_product['cat_id'] == $cat['id']) ? 'selected' : '';
                    echo "<option value='{$cat['id']}' $selected>{$cat['name']}</option>";
                }
                ?>
            </select>
            <input type="number" step="0.01" name="price" placeholder="Price" value="<?php echo $edit_product['price'] ?? ''; ?>" class="w-full p-2 border rounded" required>
            <input type="number" name="stock" placeholder="Stock" value="<?php echo $edit_product['stock'] ?? ''; ?>" class="w-full p-2 border rounded" required>
            <textarea name="description" placeholder="Description" rows="3" class="w-full p-2 border rounded md:col-span-2"><?php echo $edit_product['description'] ?? ''; ?></textarea>
            <div class="md:col-span-2">
                 <label class="block text-sm font-medium text-gray-700">Product Image</label>
                 <input type="file" name="image" class="w-full p-2 border rounded mt-1">
                 <?php if (isset($edit_product['image'])): ?>
                    <img src="../uploads/<?php echo $edit_product['image']; ?>" class="h-20 mt-2 rounded">
                 <?php endif; ?>
            </div>
        </div>
        <div class="mt-4 flex space-x-2">
            <button type="submit" name="save_product" class="px-6 py-2 bg-green-600 text-white rounded hover:bg-green-700">Save Product</button>
            <?php if ($edit_product): ?>
            <a href="product.php" class="px-6 py-2 bg-gray-300 rounded hover:bg-gray-400">Cancel Edit</a>
            <?php endif; ?>
        </div>
    </form>
</div>


<div class="bg-white p-6 rounded-lg shadow-md">
    <h2 class="text-xl font-semibold mb-4">Product List</h2>
    <div class="overflow-x-auto">
        <table class="min-w-full bg-white">
            <thead class="bg-gray-800 text-white">
                <tr>
                    <th class="py-2 px-4">Image</th>
                    <th class="py-2 px-4">Name</th>
                    <th class="py-2 px-4">Price</th>
                    <th class="py-2 px-4">Stock</th>
                    <th class="py-2 px-4">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $products = $conn->query("SELECT * FROM products ORDER BY id DESC");
                while($p = $products->fetch_assoc()):
                ?>
                <tr class="border-b text-center">
                    <td class="py-2 px-4"><img src="../uploads/<?php echo $p['image']; ?>" class="h-12 w-12 object-cover rounded mx-auto"></td>
                    <td class="py-2 px-4 font-medium"><?php echo htmlspecialchars($p['name']); ?></td>
                    <td class="py-2 px-4">৳<?php echo number_format($p['price']); ?></td>
                    <td class="py-2 px-4"><?php echo $p['stock']; ?></td>
                    <td class="py-2 px-4 space-x-2">
                        <a href="product.php?edit_id=<?php echo $p['id']; ?>" class="text-blue-500 hover:text-blue-700"><i class="fas fa-edit"></i></a>
                        <a href="product.php?delete_id=<?php echo $p['id']; ?>" onclick="return confirm('Are you sure?')" class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'common/bottom.php'; ?>