<?php
// /profile.php
include 'common/header.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$message = '';

// --- Handle Profile Update ---
if (isset($_POST['update_profile'])) {
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $stmt = $conn->prepare("UPDATE users SET name = ?, phone = ? WHERE id = ?");
    $stmt->bind_param("ssi", $name, $phone, $user_id);
    if ($stmt->execute()) {
        $_SESSION['user_name'] = $name; // Update session name
        $message = "<div class='bg-green-100 text-green-700 p-3 rounded-lg mb-4'>Profile updated successfully!</div>";
    } else {
        $message = "<div class='bg-red-100 text-red-700 p-3 rounded-lg mb-4'>Error updating profile.</div>";
    }
}

// --- Handle Password Change ---
if (isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    
    $res = $conn->query("SELECT password FROM users WHERE id = $user_id");
    $user = $res->fetch_assoc();

    if (password_verify($current_password, $user['password'])) {
        $hashed_new_password = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->bind_param("si", $hashed_new_password, $user_id);
        if ($stmt->execute()) {
             $message = "<div class='bg-green-100 text-green-700 p-3 rounded-lg mb-4'>Password changed successfully!</div>";
        } else {
             $message = "<div class='bg-red-100 text-red-700 p-3 rounded-lg mb-4'>Error changing password.</div>";
        }
    } else {
        $message = "<div class='bg-red-100 text-red-700 p-3 rounded-lg mb-4'>Incorrect current password.</div>";
    }
}

$res = $conn->query("SELECT * FROM users WHERE id = $user_id");
$user = $res->fetch_assoc();
?>
<div class="p-4 pb-20">
    <div class="flex items-center mb-6">
        <a href="index.php" class="text-xl"><i class="fas fa-arrow-left"></i></a>
        <h1 class="text-xl font-bold text-gray-800 mx-auto">My Profile</h1>
        <div class="w-6"></div>
    </div>
    
    <?php echo $message; ?>

    <div class="bg-white p-4 rounded-lg shadow-md mb-6">
        <a href="order.php" class="flex justify-between items-center w-full text-left py-2">
            <div class="flex items-center">
                <i class="fas fa-box text-green-600 w-6 text-center mr-3 fa-lg"></i>
                <span class="font-semibold text-gray-700">My Orders</span>
            </div>
            <i class="fas fa-chevron-right text-gray-400"></i>
        </a>
    </div>

    <div class="bg-white p-6 rounded-lg shadow-md mb-6">
        <h2 class="text-lg font-semibold mb-4">Edit Profile</h2>
        <form method="POST">
            <div class="mb-4">
                <label class="block text-gray-700">Name</label>
                <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-600">
            </div>
            <div class="mb-4">
                <label class="block text-gray-700">Email</label>
                <input type="email" value="<?php echo htmlspecialchars($user['email']); ?>" class="w-full px-4 py-2 border rounded-lg bg-gray-100" readonly>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700">Phone</label>
                <input type="tel" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-600">
            </div>
            <button type="submit" name="update_profile" class="w-full bg-green-600 text-white py-2 rounded-lg hover:bg-green-700">Save Changes</button>
        </form>
    </div>

    <div class="bg-white p-6 rounded-lg shadow-md mb-6">
        <h2 class="text-lg font-semibold mb-4">Change Password</h2>
        <form method="POST">
            <div class="mb-4">
                <label class="block text-gray-700">Current Password</label>
                <input type="password" name="current_password" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-600" required>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700">New Password</label>
                <input type="password" name="new_password" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-600" required>
            </div>
            <button type="submit" name="change_password" class="w-full bg-gray-800 text-white py-2 rounded-lg hover:bg-gray-900">Update Password</button>
        </form>
    </div>
    
    <div class="text-center">
        <a href="logout.php" class="text-red-500 font-semibold hover:underline">
            <i class="fas fa-sign-out-alt mr-2"></i>Logout
        </a>
    </div>

</div>

<?php include 'common/bottom.php'; ?>