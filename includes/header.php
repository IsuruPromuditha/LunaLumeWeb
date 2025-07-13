<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kings Clothing</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            AOS.init({
                duration: 1000,
                once: true
            });
        });
    </script>
</head>
<body class="bg-gray-100">
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between">
                <div class="flex space-x-7">
                    <div>
                        <a href="index.php" class="flex items-center py-4 px-2">
                            <i class="fas fa-crown text-2xl text-gray-500 hover:text-blue-500 transition duration-300 mr-2"></i>
                            <span class="font-semibold text-gray-500 text-lg hover:text-blue-500 transition duration-300">LunaLume</span>
                        </a>
                    </div>
                </div>
                <div class="flex items-center justify-center flex-grow">
                    <div class="hidden md:flex items-center space-x-6">
                        <a href="index.php" class="py-4 px-2 text-gray-500 hover:text-blue-500 transition duration-300">Home</a>
                        <a href="about.php" class="py-4 px-2 text-gray-500 hover:text-blue-500 transition duration-300">About</a>
                        <a href="contact.php" class="py-4 px-2 text-gray-500 hover:text-blue-500 transition duration-300">Contact</a>
                        <a href="category.php" class="py-4 px-2 text-gray-500 hover:text-blue-500 transition duration-300">Shop</a>
                    </div>
                </div>
                <div class="flex items-center">
                    <a href="cart.php" class="py-2 px-2 text-gray-500 hover:text-blue-500 relative">
                        <i class="fas fa-shopping-cart text-xl"></i>
                        <?php if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0): ?>
                            <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center">
                                <?php echo count($_SESSION['cart']); ?>
                            </span>
                        <?php endif; ?>
                    </a>
                </div>
            </div>
        </div>
    </nav>
</body>
</html>