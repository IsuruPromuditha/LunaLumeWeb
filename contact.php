<?php include 'includes/header.php'; ?>
<div class="max-w-7xl mx-auto px-4 py-8">
    <!-- Banner Section -->
    <section class="relative overflow-hidden rounded-lg mb-12" data-aos="fade-up">
        <img src="./assets/images/banners/mainBanner.png" alt="Contact Banner" class="w-full h-[600px] object-cover">
        <div class="absolute inset-0 bg-black bg-opacity-50 flex flex-col items-center justify-center text-center text-white">
            <h1 class="text-5xl md:text-6xl font-bold mb-4" data-aos="fade-down">Get in Touch</h1>
            <p class="text-xl md:text-2xl mb-6" data-aos="fade-up" data-aos-delay="200">We’re here to assist you with all your fashion needs!</p>
        </div>
    </section>

    <?php if (isset($_GET['message']) || isset($_GET['error'])): ?>
        <div id="toast" class="fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg transition-opacity duration-500 opacity-100 max-w-xs">
            <?php if (isset($_GET['message']) && $_GET['message'] === 'sent'): ?>
                <div class="bg-green-500 text-white p-4 rounded-lg flex items-center">
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span>Message sent successfully!</span>
                </div>
            <?php elseif (isset($_GET['error'])): ?>
                <div class="bg-red-500 text-white p-4 rounded-lg flex items-center">
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    <span>
                        <?php
                        echo htmlspecialchars($_GET['error'] === 'invalid_input' ? 'Please fill all fields correctly.' : 
                            ($_GET['error'] === 'database_error' ? 'Failed to send message. Try again later.' : 'An error occurred.'));
                        ?>
                    </span>
                </div>
            <?php endif; ?>
        </div>
        <script>
            setTimeout(() => {
                const toast = document.getElementById('toast');
                if (toast) {
                    toast.classList.add('opacity-0');
                    setTimeout(() => toast.remove(), 500);
                }
            }, 3000);
        </script>
    <?php endif; ?>

    <section class="mb-12">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <div class="order-1 lg:order-1" data-aos="fade-right">
                <h2 class="text-3xl font-bold text-center mb-8">Visit Our Store</h2>
                <iframe 
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3153.086338110832!2d-122.41941568468145!3d37.77492927975971!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x8085808f5c2b7d67%3A0x9b3b3d1d1d1d1d1d!2sKings%20Clothing!5e0!3m2!1sen!2sus!4v1698771234567!5m2!1sen!2sus" 
                    width="100%" 
                    height="450" 
                    style="border:0;" 
                    allowfullscreen="" 
                    loading="lazy" 
                    referrerpolicy="no-referrer-when-downgrade">
                </iframe>
            </div>
            <div class="order-2 lg:order-2" data-aos="fade-left">
                <h2 class="text-3xl font-bold text-center mb-8">Contact Us</h2>
                <form action="process.php" method="POST" class="w-full bg-white p-6 rounded-lg shadow-lg">
                    <input type="hidden" name="action" value="contact">
                    <div class="mb-4">
                        <label class="block text-gray-700 font-medium mb-2">Name</label>
                        <input type="text" name="name" class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 transition ease-in-out duration-300" required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 font-medium mb-2">Email</label>
                        <input type="email" name="email" class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 transition ease-in-out duration-300" required>
                    </div>
                    <div class="mb-6">
                        <label class="block text-gray-700 font-medium mb-2">Message</label>
                        <textarea name="message" class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 transition ease-in-out duration-300 h-32 resize-y" required></textarea>
                    </div>
                    <div class="flex justify-center">
                        <button type="submit" class="bg-blue-500 text-white py-3 px-6 rounded-lg hover:bg-blue-600 transition ease-in-out duration-300 font-semibold">Send Message</button>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <section class="mb-12" data-aos="fade-up">
        <h2 class="text-3xl font-bold text-center mb-8">Reach Us Directly</h2>
        <div class="flex justify-center space-x-8">
            <div class="flex items-center">
                <i class="fas fa-phone-alt text-blue-500 text-2xl mr-3"></i>
                <span class="text-lg text-gray-700">+94 71 330 7710</span>
            </div>
            <div class="flex items-center">
                <i class="fas fa-phone-alt text-blue-500 text-2xl mr-3"></i>
                <span class="text-lg text-gray-700">+94 77 499 5719</span>
            </div>
        </div>
    </section>

    <section class="mb-12" data-aos="fade-up">
        <h2 class="text-3xl font-bold text-center mb-8">Frequently Asked Questions</h2>
        <p class="text-center text-gray-600 mb-8">Here’s what our customers usually ask us while shopping</p>
        <div class="max-w-3xl mx-auto">
            <div x-data="{ open: false }" class="border-b border-gray-200 py-4">
                <button @click="open = !open" class="flex justify-between items-center w-full text-left">
                    <span class="text-lg font-semibold text-gray-800">What payment methods can I use?</span>
                    <span class="text-2xl text-blue-500" x-text="open ? '-' : '+'"></span>
                </button>
                <div x-show="open" x-cloak class="mt-2 text-gray-600">
                    We offer a variety of payment methods, including major providers such as Mastercard, Visa, American Express, and all major international cards. Additionally, we accept various local payment methods, including Koko and MintPay.
                </div>
            </div>
            <div x-data="{ open: false }" class="border-b border-gray-200 py-4">
                <button @click="open = !open" class="flex justify-between items-center w-full text-left">
                    <span class="text-lg font-semibold text-gray-800">Can I purchase items with another currency?</span>
                    <span class="text-2xl text-blue-500" x-text="open ? '-' : '+'"></span>
                </button>
                <div x-show="open" x-cloak class="mt-2 text-gray-600">
                    At this time, we only accept payments in USD. However, you can use international cards, and your bank will handle the currency conversion for you.
                </div>
            </div>
            <div x-data="{ open: false }" class="border-b border-gray-200 py-4">
                <button @click="open = !open" class="flex justify-between items-center w-full text-left">
                    <span class="text-lg font-semibold text-gray-800">Can I make changes to my order after it has been placed?</span>
                    <span class="text-2xl text-blue-500" x-text="open ? '-' : '+'"></span>
                </button>
                <div x-show="open" x-cloak class="mt-2 text-gray-600">
                    Once an order is placed, changes may be possible within the first hour. Please contact our support team at +1 (555) 123-4567 or via the contact form to request modifications.
                </div>
            </div>
            <div x-data="{ open: false }" class="border-b border-gray-200 py-4">
                <button @click="open = !open" class="flex justify-between items-center w-full text-left">
                    <span class="text-lg font-semibold text-gray-800">Do you offer e-gift cards for international customers?</span>
                    <span class="text-2xl text-blue-500" x-text="open ? '-' : '+'"></span>
                </button>
                <div x-show="open" x-cloak class="mt-2 text-gray-600">
                    Yes, we offer e-gift cards that can be used by international customers. You can purchase them directly from our website under the "Gift Cards" section.
                </div>
            </div>
            <div x-data="{ open: false }" class="border-b border-gray-200 py-4">
                <button @click="open = !open" class="flex justify-between items-center w-full text-left">
                    <span class="text-lg font-semibold text-gray-800">How do I set up a subscription order?</span>
                    <span class="text-2xl text-blue-500" x-text="open ? '-' : '+'"></span>
                </button>
                <div x-show="open" x-cloak class="mt-2 text-gray-600">
                    To set up a subscription order, visit the "Subscriptions" page on our website, select your preferred items, and choose the subscription frequency that suits you best.
                </div>
            </div>
            <div x-data="{ open: false }" class="border-b border-gray-200 py-4">
                <button @click="open = !open" class="flex justify-between items-center w-full text-left">
                    <span class="text-lg font-semibold text-gray-800">How do I return my items?</span>
                    <span class="text-2xl text-blue-500" x-text="open ? '-' : '+'"></span>
                </button>
                <div x-show="open" x-cloak class="mt-2 text-gray-600">
                    To return items, please visit our "Returns" page for detailed instructions. Items must be returned within 30 days of purchase, in their original condition, with tags attached.
                </div>
            </div>
        </div>
    </section>
</div>
<?php include 'includes/footer.php'; ?>