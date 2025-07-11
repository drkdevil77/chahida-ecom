<?php
// /admin/common/header.php
include '../common/config.php';

// Protect admin pages
$current_page = basename($_SERVER['PHP_SELF']);
if (!isset($_SESSION['admin_id']) && $current_page != 'login.php') {
    header('Location: login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chahida BD - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body {
            -webkit-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }
        .sidebar {
            transition: transform 0.3s ease-in-out;
        }
    </style>
</head>
<body class="bg-gray-100 font-sans">
    <div id="loading-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-[100] items-center justify-center hidden">
        <div class="w-16 h-16 border-4 border-dashed rounded-full animate-spin border-white"></div>
    </div>
    <div class="flex">