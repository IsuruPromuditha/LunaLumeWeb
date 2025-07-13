<footer class="bg-gray-800 text-white py-6">
    <div class="max-w-7xl mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div>
                <h3 class="text-lg font-semibold">LunaLume</h3>
                <p class="mt-2">Premium clothing for everyone.</p>
            </div>
            <div>
                <h3 class="text-lg font-semibold">Quick Links</h3>
                <ul class="mt-2 space-y-2">
                    <li><a href="index.php" class="hover:text-blue-300">Home</a></li>
                    <li><a href="about.php" class="hover:text-blue-300">About</a></li>
                    <li><a href="contact.php" class="hover:text-blue-300">Contact</a></li>
                </ul>
            </div>
            <div>
                <h3 class="text-lg font-semibold">Contact Us</h3>
                <p class="mt-2">Email: support@lunalume.com</p>
                <p>Phone: +94 77 499 5719</p>
                <p>Phone: +94 71 330 7710</p>
            </div>
        </div>

        <div class="mt-8 border-t border-gray-700 pt-8">
            <?php if (isset($_GET['message']) && $_GET['message'] === 'subscribed') { ?>
                <p class="text-center text-green-400 mb-4">Thank you for subscribing!</p>
            <?php } ?>
            <h2 class="text-2xl font-bold text-center mb-6" data-aos="fade-up">Join Our Newsletter</h2>
            <p class="text-center text-lg mb-6 max-w-2xl mx-auto" data-aos="fade-up" data-aos-delay="200">Stay updated with the latest trends, exclusive offers, and more!</p>
            <form action="process.php" method="POST" class="max-w-md mx-auto flex flex-col sm:flex-row gap-4">
                <input type="hidden" name="action" value="subscribe">
                <input type="hidden" name="redirect" value="<?php echo htmlspecialchars(basename($_SERVER['PHP_SELF'])); ?>">
                <div class="relative flex-grow">
                    <i class="fas fa-envelope absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    <input type="email" name="email" placeholder="Enter your email" class="w-full pl-12 p-3 rounded-full bg-gray-700 text-white border border-gray-600 focus:outline-none focus:border-blue-500" required>
                </div>
                <button type="submit" class="border-2 border-blue-500 text-blue-500 py-3 px-8 rounded-full bg-transparent hover:bg-blue-500 hover:text-white font-semibold">Subscribe</button>
            </form>
        </div>
    </div>
</footer>
</body>
</html>