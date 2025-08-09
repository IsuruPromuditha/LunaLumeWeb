<?php
include 'includes/header.php';
include 'includes/db_connect.php';

// Get the payment method from the query parameter
$method = isset($_GET['method']) && in_array($_GET['method'], ['store_pickup', 'cod']) ? $_GET['method'] : 'cod'; 
$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
$total = 0;

// Calculate total for display
foreach ($cart as $product_id => $sizes) {
    $stmt = $pdo->prepare("SELECT price FROM products WHERE id = ?");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($product) {
        foreach ($sizes as $size => $quantity) {
            $total += $product['price'] * $quantity;
        }
    }
}

if (empty($cart)) {
    header('Location: cart.php');
    exit;
}
?>

<div class="max-w-7xl mx-auto px-4 py-8">
    <section class="bg-white p-6 rounded-lg shadow-lg" data-aos="fade-up">
        <h2 class="text-2xl font-semibold text-gray-800 mb-4">Order Summary</h2>
        <p class="text-gray-600 mb-4">Total: LKR<?php echo number_format($total, 2); ?></p>
        <p class="text-gray-600 mb-6">Payment Method: <?php echo $method == 'store_pickup' ? 'Store Pickup' : 'Cash on Delivery'; ?></p>

        <form action="process.php" method="POST" class="space-y-6">
            <input type="hidden" name="action" value="checkout">
            <input type="hidden" name="payment_method" value="<?php echo htmlspecialchars($method); ?>">

            <div>
                <label for="customer_name" class="block text-gray-700 font-medium mb-2">Name</label>
                <input type="text" id="customer_name" name="customer_name" required class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 transition ease-in-out duration-300">
            </div>

            <div>
                <label for="customer_phone" class="block text-gray-700 font-medium mb-2">Phone</label>
                <input type="text" id="customer_phone" name="customer_phone" required class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 transition ease-in-out duration-300">
            </div>

            <div>
                <label for="customer_address" class="block text-gray-700 font-medium mb-2">Address</label>
                <textarea id="customer_address" name="customer_address" required class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 transition ease-in-out duration-300" rows="4"></textarea>
            </div>

            <button type="submit" class="w-full bg-blue-500 text-white py-3 px-6 rounded-lg hover:bg-blue-600 transition ease-in-out duration-300 font-semibold">Place Order</button>
        </form>
    </section>
</div>

<?php include 'includes/footer.php'; ?>