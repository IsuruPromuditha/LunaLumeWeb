<?php
include 'includes/header.php';
include 'includes/db_connect.php';

$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
$total = 0;

$migrated_cart = [];
foreach ($cart as $product_id => $entry) {
    if (is_array($entry)) {
        $migrated_cart[$product_id] = $entry;
    } else {
        $migrated_cart[$product_id] = ['N/A' => $entry];
    }
}
$_SESSION['cart'] = $migrated_cart;
$cart = $migrated_cart;
?>

<div class="max-w-7xl mx-auto px-4 py-8">
    <?php if (empty($cart)): ?>
        <p class="mt-4 text-gray-600 text-center" data-aos="fade-up">Your cart is empty.</p>
    <?php else: ?>
        <div class="overflow-x-auto" data-aos="fade-up">
            <table class="w-full mt-6 border-collapse bg-white shadow-lg rounded-lg">
                <thead>
                    <tr class="bg-gray-200 text-gray-700">
                        <th class="p-3 text-left font-semibold">Product</th>
                        <th class="p-3 text-left font-semibold">Size</th>
                        <th class="p-3 text-left font-semibold">Price</th>
                        <th class="p-3 text-left font-semibold">Quantity</th>
                        <th class="p-3 text-left font-semibold">Total</th>
                        <th class="p-3 text-left font-semibold"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($cart as $product_id => $sizes) {
                        $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
                        $stmt->execute([$product_id]);
                        $product = $stmt->fetch(PDO::FETCH_ASSOC);
                        if ($product) {
                            foreach ($sizes as $size => $quantity) {
                                $subtotal = $product['price'] * $quantity;
                                $total += $subtotal;
                                echo '
                                <tr class="border-b hover:bg-gray-50 transition ease-in-out duration-300">
                                    <td class="p-3 text-gray-800 flex items-center space-x-3">
                                        <img src="assets/images/' . htmlspecialchars($product['image']) . '" alt="' . htmlspecialchars($product['name']) . '" class="w-12 h-12 object-cover rounded-lg">
                                        <span>' . htmlspecialchars($product['name']) . '</span>
                                    </td>
                                    <td class="p-3 text-gray-800">' . htmlspecialchars($size) . '</td>
                                    <td class="p-3 text-gray-800">LKR' . number_format($product['price'], 2) . '</td>
                                    <td class="p-3 text-gray-800">' . $quantity . '</td>
                                    <td class="p-3 text-gray-800">LKR' . number_format($subtotal, 2) . '</td>
                                    <td class="p-3">
                                        <form action="process.php" method="POST">
                                            <input type="hidden" name="action" value="remove_from_cart">
                                            <input type="hidden" name="product_id" value="' . $product['id'] . '">
                                            <input type="hidden" name="size" value="' . htmlspecialchars($size) . '">
                                            <button type="submit" class="text-red-500 hover:text-red-700 font-medium transition ease-in-out duration-300">Remove</button>
                                        </form>
                                    </td>
                                </tr>';
                            }
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <div class="mt-6 flex justify-end" data-aos="fade-left">
            <p class="text-xl font-semibold text-gray-800">Total: LKR<?php echo number_format($total, 2); ?></p>
        </div>
    <?php endif; ?>

    <?php if (!empty($cart)): ?>
        <section class="mt-12 bg-white p-6 rounded-lg shadow-lg" data-aos="fade-up">
            <h2 class="text-2xl font-semibold text-gray-800 mb-4">Payment Options</h2>
            <p class="text-gray-600 mb-6">Our payment gateway is currently unavailable. However, you can still reserve your order with the following options:</p>
            <div class="flex flex-col sm:flex-row gap-4">
                <a href="checkout.php?method=store_pickup" class="border-2 border-blue-500 text-blue-500 py-3 px-6 rounded-lg bg-transparent hover:bg-blue-500 hover:text-white transition ease-in-out duration-300 font-semibold text-center">Reserve for Store Pickup</a>
                <a href="checkout.php?method=cod" class="border-2 border-blue-500 text-blue-500 py-3 px-6 rounded-lg bg-transparent hover:bg-blue-500 hover:text-white transition ease-in-out duration-300 font-semibold text-center">Cash on Delivery</a>
            </div>
        </section>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>