<?php
session_start();
require_once '../database/pdo.php';

if (isset($_SESSION['admin_authenticated']) && $_SESSION['admin_authenticated'] === true) {
    header("Location: dashboard.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | FindMyPet</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.8.0/fonts/remixicon.css" rel="stylesheet" />
    <style>
  
        body { 
            background-color: #f8fafc; 
            font-family: 'Poppins', sans-serif; 
        }
        .btn-primary { 
            background-color: #1f7a5f; 
        }
        .btn-primary:hover { 
            background-color: #165c47; 
        }
        .heading-color {
            color: #1f7a5f;
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen">

   
    <div class="w-full max-w-md bg-[#ffffff] rounded-xl shadow-lg p-8">
        
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold heading-color mb-3">Welcome Back</h1>
            <p class="text-gray-600 text-base">Login to access the administrative dashboard and manage the platform.</p>
        </div>

        <!-- Alert Messages -->
        <?php include '../includes/alert.php'; ?>
    
        <!-- Form -->
        <form method="POST" action="admin_code.php" class="space-y-6">
            
            <div>
                <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Email Address</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">
                        <i class="ri-mail-line text-lg"></i>
                    </span>
                    <input type="email" name="email" id="email" required 
                           placeholder="Enter your email"
                           class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1f7a5f] focus:border-[#1f7a5f] outline-none transition placeholder-gray-400" >
                </div>
            </div>

            <div>
                <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">Password</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">
                        <i class="ri-lock-line text-lg"></i>
                    </span>
                    <input type="password" name="password" id="password" required 
                           placeholder="Enter your password"
                           class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1f7a5f] focus:border-[#1f7a5f] outline-none transition placeholder-gray-400" >
                </div>
            </div>

            <button type="submit" name="btn_login" class="w-full btn-primary text-white font-semibold py-3.5 rounded-lg shadow-md hover:shadow-lg transition duration-200">
                Login
            </button>
        </form>
    </div>

</body>
</html>