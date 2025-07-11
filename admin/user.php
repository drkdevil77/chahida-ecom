<?php
// /admin/user.php
include 'common/header.php';
include 'common/sidebar.php';

if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];
    $conn->query("DELETE FROM users WHERE id = $id");
    echo "<script>alert('User deleted!'); window.location.href='user.php';</script>";
    exit;
}
?>

<h1 class="text-3xl font-bold text-gray-800 mb-6">User Management</h1>

<div class="bg-white p-6 rounded-lg shadow-md">
    <div class="overflow-x-auto">
        <table class="min-w-full bg-white">
            <thead class="bg-gray-800 text-white">
                <tr>
                    <th class="py-3 px-4">User ID</th>
                    <th class="py-3 px-4">Name</th>
                    <th class="py-3 px-4">Email</th>
                    <th class="py-3 px-4">Phone</th>
                    <th class="py-3 px-4">Joined On</th>
                    <th class="py-3 px-4">Actions</th>
                </tr>
            </thead>
            <tbody class="text-gray-700">
                <?php
                $users_res = $conn->query("SELECT * FROM users ORDER BY created_at DESC");
                while ($user = $users_res->fetch_assoc()):
                ?>
                <tr class="border-b">
                    <td class="py-3 px-4 text-center">#<?php echo $user['id']; ?></td>
                    <td class="py-3 px-4"><?php echo htmlspecialchars($user['name']); ?></td>
                    <td class="py-3 px-4"><?php echo htmlspecialchars($user['email']); ?></td>
                    <td class="py-3 px-4 text-center"><?php echo htmlspecialchars($user['phone']); ?></td>
                    <td class="py-3 px-4 text-center"><?php echo date('d M Y', strtotime($user['created_at'])); ?></td>
                    <td class="py-3 px-4 text-center">
                        <a href="user.php?delete_id=<?php echo $user['id']; ?>" onclick="return confirm('Are you sure? This will delete the user and all their orders.')" class="text-red-500 hover:text-red-700">
                            <i class="fas fa-trash"></i> Delete
                        </a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'common/bottom.php'; ?>