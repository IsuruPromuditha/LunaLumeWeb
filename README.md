# 🛍️ Luna~Lume — Modern PHP E-Commerce Platform

Luna~Lume (formerly Kings Clothing) is a full-featured, responsive e-commerce web application built with native PHP, MySQL, and Tailwind CSS. The platform features dynamic product inventory management, size and stock tracking, category browsing, sleek scroll animations, and an integrated AI webchat assistant.

---

## ✨ Features

- **Hero & Promotional Banners:** Interactive high-impact promotional sections and call-to-actions.
- **Dynamic Featured Products:** Displays real-time products fetched directly from the database using PDO.
- **Stock & Size Availability:** Real-time checking for size variations (`product_sizes`) and automated "Out of Stock" status badges.
- **Category Navigation:** Top-level category browsing with interactive image hover effects.
- **AI Chatbot Support:** Integrated Botpress Webchat widget for customer support and automated assistance.
- **Smooth AOS Animations:** Scroll-triggered entrance effects using the Animate On Scroll (AOS) library.
- **Fully Responsive UI:** Built with Tailwind CSS for mobile, tablet, and desktop viewports.

---

## 🛠️ Tech Stack

- **Backend:** PHP 8.x (PDO)
- **Database:** MySQL
- **Frontend & Styling:** Tailwind CSS, HTML5, JavaScript
- **Animations:** AOS (Animate On Scroll)
- **AI Integration:** Botpress Webchat API

---

## 📁 Project Structure

```text
├── assets/
│   ├── images/          # Product and category media assets
│   └── banners/         # Promotional hero banners
├── includes/
│   ├── db_connect.php   # Database connection configuration (PDO)
│   ├── header.php       # Shared site header & navigation
│   └── footer.php       # Shared site footer
├── category.php         # Category filter page
├── product.php          # Detailed single-product view
├── index.php            # Main homepage
└── README.md            # Project documentation
