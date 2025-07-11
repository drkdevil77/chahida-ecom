<?php
// /admin/setting.php
include 'common/header.php';

$message = '';
if (isset($_POST['update_settings'])) {
    $username = $_POST['username'];
    $admin_id = $_SESSION['admin_id'];
    
    // Update username
    $stmt_user = $conn->prepare("UPDATE admin SET username = ? WHERE id = ?");
    $stmt_user->bind_param("si", $username, $admin_id);
    $stmt_user->execute();
    $_SESSION['admin_username'] = $username;
    
    // Update password if provided
    if (!empty($_POST['new_password'])) {
        $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
        $stmt_pass = $conn->prepare("UPDATE admin SET password = ? WHERE id = ?");
        $stmt_pass->bind_param("si", $new_password, $admin_id);
        $stmt_pass->execute();
    }
    
    $message = "<div class='bg-green-100 text-green-700 p-3 rounded-lg mb-4'>Settings updated successfully!</div>";
}

$admin_info = $conn->query("SELECT * FROM admin WHERE id = {$_SESSION['admin_id']}")->fetch_assoc();

include 'common/sidebar.php';
?>

<h1 class="text-3xl font-bold text-gray-800 mb-6">Admin Settings</h1>

<div class="max-w-lg mx-auto bg-white p-6 rounded-lg shadow-md">
    <?php echo $message; ?>
    <form method="POST">
        <div class="mb-4">
            <label class="block text-gray-700">Username</label>
            <input type="text" name="username" value="<?php echo htmlspecialchars($admin_info['username']); ?>" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-600" required>
        </div>
        <div class="mb-4">
            <label class="block text-gray-700">New Password (leave blank to keep current)</label>
            <input type="password" name="new_password" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-600">
        </div>
        <button type="submit" name="update_settings" class="w-full bg-green-600 text-white py-2 rounded-lg hover:bg-green-700">Save Settings</button>
    </form>
</div>

<?php include 'common/bottom.php'; ?>