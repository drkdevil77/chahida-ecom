<?php
// /admin/category.php

// This entire PHP block MUST be at the very top of the file, before any HTML.
include '../common/config.php';

// --- AJAX API Endpoint ---
if (isset($_GET['action'])) {
    header('Content-Type: application/json');
    $action = $_GET['action'];
    $response = ['success' => false, 'message' => 'An error occurred.'];

    // --- FETCH ALL CATEGORIES ---
    if ($action == 'fetch') {
        $result = $conn->query("SELECT * FROM categories ORDER BY id DESC");
        $categories = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $categories[] = $row;
            }
        }
        $response = ['success' => true, 'categories' => $categories];
    }
    
    // --- ADD/UPDATE CATEGORY ---
    elseif ($action == 'save' && $_SERVER['REQUEST_METHOD'] == 'POST') {
        $id = $_POST['id'];
        $name = $_POST['name'];
        $image_name = $_POST['existing_image'] ?? '';

        // Handle file upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $target_dir = "../uploads/";
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0777, true);
            }
            $image_name = time() . '_' . basename($_FILES["image"]["name"]);
            $target_file = $target_dir . $image_name;
            move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);
        }

        if (empty($id)) { // ADD
            $stmt = $conn->prepare("INSERT INTO categories (name, image) VALUES (?, ?)");
            $stmt->bind_param("ss", $name, $image_name);
            $message = 'Category added successfully!';
        } else { // UPDATE
            $stmt = $conn->prepare("UPDATE categories SET name = ?, image = ? WHERE id = ?");
            $stmt->bind_param("ssi", $name, $image_name, $id);
            $message = 'Category updated successfully!';
        }

        if ($stmt->execute()) {
            $response = ['success' => true, 'message' => $message];
        } else {
            $response['message'] = $stmt->error;
        }
    }
    
    // --- DELETE CATEGORY ---
    elseif ($action == 'delete' && isset($_POST['id'])) {
        $id = $_POST['id'];
        $stmt = $conn->prepare("DELETE FROM categories WHERE id = ?");
        $stmt->bind_param("i", $id);
        if($stmt->execute()){
            $response = ['success' => true, 'message' => 'Category deleted.'];
        }
    }

    echo json_encode($response);
    exit; // Stop the script after sending the JSON response
}

// Start HTML output only after the AJAX block is finished
include 'common/header.php';
include 'common/sidebar.php';
?>

<h1 class="text-3xl font-bold text-gray-800 mb-6">Manage Categories</h1>

<div id="category-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 items-center justify-center hidden">
    <div class="bg-white p-6 rounded-lg shadow-xl w-full max-w-md">
        <h2 id="modal-title" class="text-2xl font-bold mb-4">Add Category</h2>
        <form id="category-form">
            <input type="hidden" name="id" id="category-id">
            <input type="hidden" name="existing_image" id="existing-image">
            <div class="mb-4">
                <label for="name" class="block text-gray-700">Category Name</label>
                <input type="text" name="name" id="category-name" class="w-full p-2 border rounded" required>
            </div>
            <div class="mb-4">
                <label for="image" class="block text-gray-700">Image</label>
                <input type="file" name="image" id="category-image" accept="image/*" class="w-full p-2 border rounded">
                <img id="image-preview" src="#" alt="Image Preview" class="mt-2 h-20 hidden rounded">
            </div>
            <div class="flex justify-end space-x-4">
                <button type="button" onclick="closeModal()" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Save</button>
            </div>
        </form>
    </div>
</div>

<div class="bg-white p-6 rounded-lg shadow-md">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-semibold">Category List</h2>
        <button onclick="openModal()" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700"><i class="fas fa-plus"></i> Add New</button>
    </div>
    <div id="category-list" class="space-y-3">
        </div>
</div>

<script>
    const modal = document.getElementById('category-modal');
    const form = document.getElementById('category-form');
    const modalTitle = document.getElementById('modal-title');
    const categoryId = document.getElementById('category-id');
    const categoryName = document.getElementById('category-name');
    const categoryImage = document.getElementById('category-image');
    const existingImage = document.getElementById('existing-image');
    const imagePreview = document.getElementById('image-preview');
    const categoryList = document.getElementById('category-list');
    const loadingOverlay = document.getElementById('loading-overlay');

    function openModal(category = null) {
        form.reset();
        imagePreview.classList.add('hidden');
        if (category) {
            modalTitle.textContent = 'Edit Category';
            categoryId.value = category.id;
            categoryName.value = category.name;
            existingImage.value = category.image;
            if (category.image) {
                imagePreview.src = `../uploads/${category.image}`;
                imagePreview.classList.remove('hidden');
            }
        } else {
            modalTitle.textContent = 'Add Category';
        }
        modal.style.display = 'flex';
    }

    function closeModal() {
        modal.style.display = 'none';
    }

    async function fetchCategories() {
        loadingOverlay.style.display = 'flex';
        try {
            const response = await fetch('category.php?action=fetch');
            const data = await response.json();
            if (data.success) {
                categoryList.innerHTML = '';
                if(data.categories.length > 0) {
                    data.categories.forEach(cat => {
                        const catElement = `
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border">
                                <div class="flex items-center">
                                    <img src="../uploads/${cat.image ? cat.image : 'placeholder.jpg'}" class="w-12 h-12 rounded-md object-cover mr-4">
                                    <span class="font-medium">${cat.name}</span>
                                </div>
                                <div class="space-x-2">
                                    <button onclick='openModal(${JSON.stringify(cat)})' class="text-blue-500 hover:text-blue-700"><i class="fas fa-edit"></i></button>
                                    <button onclick="deleteCategory(${cat.id})" class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></button>
                                </div>
                            </div>
                        `;
                        categoryList.innerHTML += catElement;
                    });
                } else {
                    categoryList.innerHTML = '<p class="text-center text-gray-500">No categories found. Click "Add New" to start.</p>';
                }
            }
        } catch(error) {
            console.error("Fetch Error:", error);
            categoryList.innerHTML = '<p class="text-center text-red-500">Failed to load categories. Check console for errors.</p>';
        } finally {
            loadingOverlay.style.display = 'none';
        }
    }

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        loadingOverlay.style.display = 'flex';
        const formData = new FormData(form);
        
        try {
            const response = await fetch('category.php?action=save', {
                method: 'POST',
                body: formData
            });
            const result = await response.json();
            
            alert(result.message);
            if (result.success) {
                closeModal();
                fetchCategories();
            }
        } catch(error) {
            console.error("Save Error:", error);
            alert("An error occurred while saving. Please check the console.");
        } finally {
            loadingOverlay.style.display = 'none';
        }
    });

    async function deleteCategory(id) {
        if (!confirm('Are you sure? This may also delete related products.')) return;
        loadingOverlay.style.display = 'flex';
        const formData = new FormData();
        formData.append('id', id);

        try {
            const response = await fetch('category.php?action=delete', {
                method: 'POST',
                body: formData
            });
            const result = await response.json();

            alert(result.message);
            if (result.success) {
                fetchCategories();
            }
        } catch(error) {
            console.error("Delete Error:", error);
            alert("An error occurred while deleting. Please check the console.");
        } finally {
            loadingOverlay.style.display = 'none';
        }
    }

    // Initial load
    document.addEventListener('DOMContentLoaded', fetchCategories);
</script>

<?php include 'common/bottom.php'; ?>