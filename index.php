<?php include 'includes/header.php'; ?>
<div class="max-w-7xl mx-auto px-4 py-8">

    <section class="relative overflow-hidden rounded-lg mb-12" data-aos="fade-up">
        <img src="./assets/images/banners/mainBanner.png" alt="Kings Clothing Banner" class="w-full h-[600px] object-cover">
        <div class="absolute inset-0 bg-black bg-opacity-50 flex flex-col items-center justify-center text-center text-white">
            <h1 class="text-5xl md:text-6xl font-bold mb-4" data-aos="fade-down">Luna~Lume</h1>
            <p class="text-xl md:text-2xl mb-6" data-aos="fade-up" data-aos-delay="200">Discover Premium Fashion for Every Style</p>
            <a href="category.php" class="inline-block border-2 border-blue-500 text-blue-500 py-3 px-6 rounded-lg bg-transparent hover:bg-blue-500 hover:text-white transition ease-in-out duration-300 font-semibold" data-aos="zoom-in" data-aos-delay="400">Shop Now</a>
        </div>
    </section>

    <section class="mb-12" data-aos="fade-up">
        <h2 class="text-3xl font-semibold text-center mb-8" data-aos="fade-up">Featured Products</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            <?php
            include 'includes/db_connect.php';
            $stmt = $pdo->query("SELECT * FROM products ORDER BY id DESC LIMIT 4");
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $stmt_sizes = $pdo->prepare("SELECT size FROM product_sizes WHERE product_id = ? AND stock > 0");
                $stmt_sizes->execute([$row['id']]);
                $available_sizes = $stmt_sizes->fetchAll(PDO::FETCH_COLUMN);
                
                echo '
                <div class="bg-white p-4 rounded-lg shadow-lg transform transition ease-in-out duration-300 hover:scale-105 hover:shadow-xl" data-aos="zoom-in" data-aos-delay="' . (100 * $row['id']) . '">
                    <img src="assets/images/' . htmlspecialchars($row['image']) . '" alt="' . htmlspecialchars($row['name']) . '" class="w-full h-64 object-cover rounded-lg">
                    <h3 class="text-lg font-semibold mt-4">' . htmlspecialchars($row['name']) . '</h3>
                    <p class="text-gray-600 mt-1">LKR' . number_format($row['price'], 2) . '</p>
                    <div class="flex flex-wrap gap-2 mt-2">';
                
                if (!empty($available_sizes)) {
                    foreach ($available_sizes as $size) {
                        echo '
                        <span class="inline-block bg-gray-200 text-gray-800 text-xs font-semibold rounded-full px-3 py-1">' . htmlspecialchars($size) . '</span>';
                    }
                } else {
                    echo '
                    <span class="inline-block bg-red-100 text-red-800 text-xs font-semibold rounded-full px-3 py-1">Out of Stock</span>';
                }
                
                echo '
                    </div>
                    <a href="product.php?id=' . $row['id'] . '" class="mt-3 inline-block border-2 border-blue-500 text-blue-500 py-2 px-4 rounded-lg bg-transparent hover:bg-blue-500 hover:text-white transition ease-in-out duration-300 font-medium">View Details</a>
                </div>';
            }
            ?>
        </div>
    </section>

    <section class="mb-12" data-aos="fade-up">
        <h2 class="text-3xl font-semibold text-center mb-8" data-aos="fade-up">Shop by Category</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
            <?php
            $stmt = $pdo->query("SELECT * FROM categories WHERE parent_id IS NULL LIMIT 3");
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo '
                <a href="category.php?category=' . $row['id'] . '" class="relative group overflow-hidden rounded-lg" data-aos="zoom-in">
                    <img src="assets/images/' . htmlspecialchars($row['name']) . '.jpg" alt="' . htmlspecialchars($row['name']) . '" class="w-full h-64 object-cover transform group-hover:scale-110 transition ease-in-out duration-500">
                    <div class="absolute inset-0 bg-black bg-opacity-40 flex items-center justify-center">
                        <h3 class="text-2xl font-bold text-white" data-aos="fade-up" data-aos-delay="200">' . htmlspecialchars($row['name']) . '</h3>
                    </div>
                </a>';
            }
            ?>
        </div>
    </section>

   
        </div>
    </section>

    <section class="mb-12" data-aos="fade-up">
        <div class="relative text-white rounded-lg overflow-hidden" data-aos="zoom-in">
            <img src="./assets/images/banners/promoBanner.jpg" alt="Promotion" class="w-full h-80 object-cover opacity-50">
            <div class="absolute inset-0 flex flex-col items-center justify-center text-center">
                <h2 class="text-3xl md:text-4xl font-bold mb-4" data-aos="fade-up">New Arrivals!</h2>
                <p class="text-lg md:text-xl mb-6" data-aos="fade-up" data-aos-delay="200">Get 20% off on all new collections this month!</p>
                <a href="category.php" class="inline-block border-2 border-white text-white py-3 px-6 rounded-lg bg-transparent hover:bg-white hover:text-blue-600 transition ease-in-out duration-300 font-semibold" data-aos="zoom-in" data-aos-delay="400">Explore Now</a>
            </div>
        </div>
    </section>

</div>

<script src="https://cdn.botpress.cloud/webchat/v3.2/inject.js" defer></script>
<script src="https://files.bpcontent.cloud/2025/07/07/17/20250707173426-L3AK212R.js" defer></script>

<?php include 'includes/footer.php'; ?>