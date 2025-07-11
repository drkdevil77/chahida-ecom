<?php
// /login.php
include 'common/config.php';

// --- Logic for Login & Signup ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    $response = [];

    // --- SIGNUP ---
    if ($_POST['action'] == 'signup') {
        $name = $_POST['name'];
        $phone = $_POST['phone'];
        $email = $_POST['email'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO users (name, phone, email, password) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $phone, $email, $password);

        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = "Signup successful! Please login.";
        } else {
            $response['success'] = false;
            $response['message'] = "Error: " . $stmt->error;
        }
        $stmt->close();
    }

    // --- LOGIN ---
    if ($_POST['action'] == 'login') {
        $email = $_POST['email'];
        $password = $_POST['password'];

        $stmt = $conn->prepare("SELECT id, name, password FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $response['success'] = true;
                $response['redirect'] = 'index.php';
            } else {
                $response['success'] = false;
                $response['message'] = "Incorrect password.";
            }
        } else {
            $response['success'] = false;
            $response['message'] = "No user found with this email.";
        }
        $stmt->close();
    }

    echo json_encode($response);
    $conn->close();
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login / Signup - Chahida BD</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="w-full max-w-md p-6">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-green-600">Chahida BD</h1>
        </div>

        <div class="bg-white rounded-lg shadow-xl p-6">
            <div class="flex border-b">
                <button id="login-tab" class="flex-1 py-2 text-center font-semibold text-green-600 border-b-2 border-green-600">Login</button>
                <button id="signup-tab" class="flex-1 py-2 text-center font-semibold text-gray-500">Sign Up</button>
            </div>

            <div id="login-form-container">
                <form id="login-form" class="mt-6">
                    <input type="hidden" name="action" value="login">
                    <div class="mb-4">
                        <label class="block text-gray-700">Email</label>
                        <input type="email" name="email" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-600" required>
                    </div>
                    <div class="mb-6">
                        <label class="block text-gray-700">Password</label>
                        <input type="password" name="password" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-600" required>
                    </div>
                    <button type="submit" class="w-full bg-green-600 text-white py-2 rounded-lg hover:bg-green-700 transition">Login</button>
                    <p id="login-error" class="text-red-500 text-sm mt-2"></p>
                </form>
            </div>

            <div id="signup-form-container" class="hidden">
                <form id="signup-form" class="mt-6">
                    <input type="hidden" name="action" value="signup">
                    <div class="mb-4">
                        <label class="block text-gray-700">Name</label>
                        <input type="text" name="name" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-600" required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700">Phone</label>
                        <input type="tel" name="phone" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-600" required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700">Email</label>
                        <input type="email" name="email" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-600" required>
                    </div>
                    <div class="mb-6">
                        <label class="block text-gray-700">Password</label>
                        <input type="password" name="password" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-600" required>
                    </div>
                    <button type="submit" class="w-full bg-green-600 text-white py-2 rounded-lg hover:bg-green-700 transition">Sign Up</button>
                     <p id="signup-message" class="text-sm mt-2"></p>
                </form>
            </div>
        </div>
    </div>

    <script>
        const loginTab = document.getElementById('login-tab');
        const signupTab = document.getElementById('signup-tab');
        const loginForm = document.getElementById('login-form-container');
        const signupForm = document.getElementById('signup-form-container');

        loginTab.addEventListener('click', () => {
            loginForm.classList.remove('hidden');
            signupForm.classList.add('hidden');
            loginTab.classList.add('text-green-600', 'border-green-600');
            signupTab.classList.remove('text-green-600', 'border-green-600');
            signupTab.classList.add('text-gray-500');
        });

        signupTab.addEventListener('click', () => {
            signupForm.classList.remove('hidden');
            loginForm.classList.add('hidden');
            signupTab.classList.add('text-green-600', 'border-green-600');
            loginTab.classList.remove('text-green-600', 'border-green-600');
            loginTab.classList.add('text-gray-500');
        });

        // AJAX for forms
        document.getElementById('login-form').addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            const response = await fetch('login.php', {
                method: 'POST',
                body: formData
            });
            const result = await response.json();
            if (result.success) {
                window.location.href = result.redirect;
            } else {
                document.getElementById('login-error').textContent = result.message;
            }
        });

        document.getElementById('signup-form').addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            const response = await fetch('login.php', {
                method: 'POST',
                body: formData
            });
            const result = await response.json();
            const signupMessage = document.getElementById('signup-message');
            signupMessage.textContent = result.message;
            if (result.success) {
                signupMessage.classList.add('text-green-500');
                signupMessage.classList.remove('text-red-500');
                e.target.reset();
            } else {
                 signupMessage.classList.add('text-red-500');
                 signupMessage.classList.remove('text-green-500');
            }
        });
    </script>
</body>
</html>