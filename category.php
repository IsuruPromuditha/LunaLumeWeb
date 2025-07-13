<?php include 'includes/header.php'; ?>
<div class="max-w-7xl mx-auto px-4 py-8">

    <section class="relative overflow-hidden rounded-lg mb-12" data-aos="fade-up">
        <img src="./assets/images/banners/mainBanner.png" alt="Shop Banner" class="w-full h-[600px] object-cover">
        <div class="absolute inset-0 bg-black bg-opacity-50 flex flex-col items-center justify-center text-center text-white">
            <h1 class="text-5xl md:text-6xl font-bold mb-4" data-aos="fade-down">Shop Our Collections</h1>
            <p class="text-xl md:text-2xl mb-6" data-aos="fade-up" data-aos-delay="200">Discover the Latest Trends in Fashion</p>
        </div>
    </section>

    <div class="flex flex-col lg:flex-row gap-8">
        <div class="w-full lg:w-1/4" data-aos="fade-right">
            <h2 class="text-2xl font-semibold mb-6 text-gray-800">Categories</h2>
            <?php
            include 'includes/db_connect.php';

            $stmt = $pdo->query("SELECT * FROM categories WHERE parent_id IS NULL");
            $main_categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($main_categories as $main_category) {
                $sub_stmt = $pdo->prepare("SELECT * FROM categories WHERE parent_id = ?");
                $sub_stmt->execute([$main_category['id']]);
                $subcategories = $sub_stmt->fetchAll(PDO::FETCH_ASSOC);
            ?>
                <div x-data="{ open: false }" class="mb-4">
                    <button @click="open = !open" class="flex justify-between items-center w-full text-left py-2 px-4 rounded-lg bg-gray-100 hover:bg-gray-200 transition ease-in-out duration-300">
                        <a href="?category=<?= $main_category['id'] ?>" class="text-lg font-medium text-gray-800 hover:text-blue-500"><?= htmlspecialchars($main_category['name']) ?></a>
                        <span class="text-xl text-blue-500" x-text="open ? '-' : '+'"></span>
                    </button>
                    <div x-show="open" x-cloak class="ml-4 mt-2">
                        <?php foreach ($subcategories as $subcategory): ?>
                            <?php
                            $brand_stmt = $pdo->prepare("SELECT * FROM categories WHERE parent_id = ?");
                            $brand_stmt->execute([$subcategory['id']]);
                            $brands = $brand_stmt->fetchAll(PDO::FETCH_ASSOC);
                            ?>
                            <div x-data="{ subOpen: false }" class="mb-2">
                                <button @click="subOpen = !subOpen" class="flex justify-between items-center w-full text-left py-1 px-2 rounded-lg hover:bg-gray-100 transition ease-in-out duration-300">
                                    <a href="?category=<?= $subcategory['id'] ?>" class="text-base text-gray-600 hover:text-blue-500"><?= htmlspecialchars($subcategory['name']) ?></a>
                                    <span class="text-lg text-blue-500" x-text="subOpen ? '-' : '+'"></span>
                                </button>
                                <div x-show="subOpen" x-cloak class="ml-4 mt-1">
                                    <?php foreach ($brands as $brand): ?>
                                        <a href="?category=<?= $brand['id'] ?>" class="block py-1 text-sm text-gray-500 hover:text-blue-500 transition ease-in-out duration-300"><?= htmlspecialchars($brand['name']) ?></a>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php } ?>
        </div>

        <div class="w-full lg:w-3/4" data-aos="fade-left">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                <?php
                function getCategoryIds($pdo, $category_id) {
                    $category_ids = [$category_id];
                    // Fetch subcategories recursively
                    $stmt = $pdo->prepare("SELECT id FROM categories WHERE parent_id = ?");
                    $stmt->execute([$category_id]);
                    $subcategories = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    foreach ($subcategories as $subcategory) {
                        $category_ids = array_merge($category_ids, getCategoryIds($pdo, $subcategory['id']));
                    }
                    return $category_ids;
                }

                $category_id = isset($_GET['category']) ? (int)$_GET['category'] : null;
                $query = "SELECT * FROM products";
                $params = [];
                
                if ($category_id) {
                    $category_ids = getCategoryIds($pdo, $category_id);
                    $placeholders = implode(',', array_fill(0, count($category_ids), '?'));
                    $query .= " WHERE category_id IN ($placeholders)";
                    $params = $category_ids;
                }

                $stmt = $pdo->prepare($query);
                $stmt->execute($params);
                $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if (count($products) > 0) {
                    foreach ($products as $index => $row) {
                        $size_stmt = $pdo->prepare("SELECT size FROM product_sizes WHERE product_id = ? AND stock > 0");
                        $size_stmt->execute([$row['id']]);
                        $available_sizes = $size_stmt->fetchAll(PDO::FETCH_COLUMN);
                        ?>
                        <div class="bg-white p-4 rounded-lg shadow-lg transform transition ease-in-out duration-300 hover:scale-105 hover:shadow-xl" data-aos="zoom-in" data-aos-delay="<?php echo $index * 100; ?>">
                            <img src="assets/images/<?php echo htmlspecialchars($row['image']); ?>" alt="<?php echo htmlspecialchars($row['name']); ?>" class="w-full h-48 object-cover rounded-lg">
                            <h3 class="text-lg font-semibold mt-4 text-gray-800"><?php echo htmlspecialchars($row['name']); ?></h3>
                            <p class="text-gray-600 mt-1">$<?php echo number_format($row['price'], 2); ?></p>
                            <!-- Display Available Sizes -->
                            <?php if (!empty($available_sizes)) { ?>
                                <div class="mt-2 flex flex-wrap gap-2">
                                    <span class="text-sm font-medium text-gray-700">Available Sizes:</span>
                                    <?php foreach ($available_sizes as $size) { ?>
                                        <span class="inline-block bg-gray-100 text-gray-800 text-xs font-semibold px-2.5 py-0.5 rounded"><?php echo htmlspecialchars($size); ?></span>
                                    <?php } ?>
                                </div>
                            <?php } else { ?>
                                <div class="mt-2 text-sm text-red-600">No sizes available</div>
                            <?php } ?>
                            <a href="product.php?id=<?php echo $row['id']; ?>" class="mt-3 inline-block border-2 border-blue-500 text-blue-500 py-2 px-4 rounded-lg bg-transparent hover:bg-blue-500 hover:text-white transition ease-in-out duration-300 font-medium">View Details</a>
                        </div>
                        <?php
                    }
                } else {
                    echo '<p class="text-gray-600 text-center col-span-3">No products found in this category.</p>';
                }
                ?>
            </div>
        </div>
    </div>
</div>
<?php include 'includes/footer.php'; ?>