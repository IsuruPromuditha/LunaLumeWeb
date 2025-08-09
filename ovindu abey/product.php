<?php
include 'includes/header.php';
include 'includes/db_connect.php';

if (!isset($_GET['id'])) {
    header('Location: category.php');
    exit;
}

$product_id = (int)$_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    header('Location: category.php');
    exit;
}

// Fetch available sizes with stock > 0
$stmt_sizes = $pdo->prepare("SELECT size, stock FROM product_sizes WHERE product_id = ? AND stock > 0");
$stmt_sizes->execute([$product_id]);
$available_sizes = $stmt_sizes->fetchAll(PDO::FETCH_ASSOC);
?>
<div class="max-w-7xl mx-auto px-4 py-8">


    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-12">
        <div data-aos="zoom-in">
            <img src="assets/images/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="w-full h-96 object-cover rounded-lg shadow-lg">
        </div>
        <div class="flex flex-col justify-center" data-aos="fade-left">
            <h1 class="text-4xl font-bold text-gray-800"><?php echo htmlspecialchars($product['name']); ?></h1>
            <p class="text-3xl text-gray-600 mt-3">LKR<?php echo number_format($product['price'], 2); ?></p>
            <p class="mt-6 text-gray-600 leading-relaxed"><?php echo htmlspecialchars($product['description']); ?></p>
            <form action="process.php" method="POST" class="mt-8">
                <input type="hidden" name="action" value="add_to_cart">
                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                <?php if (!empty($available_sizes)): ?>
                    <div class="mb-6">
                        <label class="block text-gray-700 font-medium mb-2">Available Sizes & Quantity</label>
                        <div class="grid grid-cols-2 gap-4">
                            <?php foreach ($available_sizes as $size): ?>
                                <div class="flex items-center space-x-2">
                                    <input type="checkbox" name="sizes[]" value="<?php echo htmlspecialchars($size['size']); ?>" class="size-checkbox h-4 w-4 text-blue-500">
                                    <label class="text-gray-700 w-8"><?php echo htmlspecialchars($size['size']); ?></label>
                                    <input type="number" name="quantity_<?php echo htmlspecialchars($size['size']); ?>" value="0" min="0" max="<?php echo $size['stock']; ?>" placeholder="Qty" class="w-20 p-1 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 transition ease-in-out duration-300" disabled>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php else: ?>
                    <p class="text-red-500 mb-6">Out of Stock</p>
                <?php endif; ?>
                <button type="submit" class="border-2 border-blue-500 text-blue-500 py-3 px-6 rounded-lg bg-transparent hover:bg-blue-500 hover:text-white transition ease-in-out duration-300 font-semibold <?php echo empty($available_sizes) ? 'opacity-50 cursor-not-allowed' : ''; ?>" <?php echo empty($available_sizes) ? 'disabled' : ''; ?>>Add to Cart</button>
            </form>
        </div>
    </div>

    <!-- Related Items Section -->
    <section class="mb-12" data-aos="fade-up">
        <h2 class="text-3xl font-bold text-center mb-8">Explore Similar Items</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
            <?php
            $related_stmt = $pdo->prepare("SELECT * FROM products WHERE category_id = ? AND id != ? LIMIT 4");
            $related_stmt->execute([$product['category_id'], $product['id']]);
            $related_products = $related_stmt->fetchAll(PDO::FETCH_ASSOC);

            if (count($related_products) > 0) {
                foreach ($related_products as $index => $related_product) {
                    echo '
                    <div class="bg-white p-4 rounded-lg shadow-lg transform transition ease-in-out duration-300 hover:scale-105 hover:shadow-xl" data-aos="zoom-in" data-aos-delay="' . ($index * 100) . '">
                        <img src="assets/images/' . htmlspecialchars($related_product['image']) . '" alt="' . htmlspecialchars($related_product['name']) . '" class="w-full h-48 object-cover rounded-lg">
                        <h3 class="text-lg font-semibold mt-4 text-gray-800">' . htmlspecialchars($related_product['name']) . '</h3>
                        <p class="text-gray-600 mt-1">$' . number_format($related_product['price'], 2) . '</p>
                        <a href="product.php?id=' . $related_product['id'] . '" class="mt-3 inline-block border-2 border-blue-500 text-blue-500 py-2 px-4 rounded-lg bg-transparent hover:bg-blue-500 hover:text-white transition ease-in-out duration-300 font-medium">View Details</a>
                    </div>';
                }
            } else {
                echo '<p class="text-gray-600 text-center col-span-4">No related products found.</p>';
            }
            ?>
        </div>
    </section>
</div>
<?php include 'includes/footer.php'; ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Enable/disable quantity input based on checkbox state
        document.querySelectorAll('.size-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const quantityInput = document.querySelector(`input[name="quantity_${this.value}"]`);
                quantityInput.disabled = !this.checked;
                if (!this.checked) {
                    quantityInput.value = '0';
                }
            });
        });
    });
</script>