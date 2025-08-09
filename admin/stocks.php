<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: index.php');
    exit;
}
include '../includes/db_connect.php';

// Handle sorting and filtering
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'name_asc';
$order_by = 'p.name ASC';
if ($sort === 'name_desc') {
    $order_by = 'p.name DESC';
} elseif ($sort === 'stock_asc') {
    $order_by = 'ps.stock ASC';
} elseif ($sort === 'stock_desc') {
    $order_by = 'ps.stock DESC';
}

// Fetch products with categories and stock levels
$query = "
    SELECT p.id, p.name, p.description, p.price, p.category_id, p.image, c.name AS category_name, 
           ps.size, ps.stock, ps.updated_at
    FROM products p
    LEFT JOIN categories c ON p.category_id = c.id
    LEFT JOIN product_sizes ps ON p.id = ps.product_id
";
if ($filter === 'low_stock') {
    $query .= " WHERE ps.stock < 10 AND ps.stock IS NOT NULL";
}
$query .= " ORDER BY $order_by";

$stmt = $pdo->query($query);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Organize data by product
$product_data = [];
foreach ($products as $row) {
    $product_id = $row['id'];
    if (!isset($product_data[$product_id])) {
        $product_data[$product_id] = [
            'name' => $row['name'],
            'category_name' => $row['category_name'] ?: 'Uncategorized',
            'sizes' => []
        ];
    }
    if ($row['size']) {
        $product_data[$product_id]['sizes'][] = [
            'size' => $row['size'],
            'stock' => $row['stock'],
            'updated_at' => $row['updated_at']
        ];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Stock Levels</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            AOS.init({ duration: 1000, once: true });

            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('error')) {
                const error = urlParams.get('error');
                if (error === 'database_error') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Database Error',
                        text: 'An error occurred while retrieving stock levels. Please try again.',
                        confirmButtonColor: '#3B82F6',
                    });
                }
            }

            <?php if (empty($product_data)): ?>
                Swal.fire({
                    icon: 'info',
                    title: 'No Products',
                    text: 'No products found in the database.',
                    confirmButtonColor: '#3B82F6',
                });
            <?php endif; ?>

            // Function to download stock report as PDF
            document.getElementById('downloadReport').addEventListener('click', function() {
                const { jsPDF } = window.jspdf;
                const table = document.getElementById('stockTable');
                if (!table) return;
                
                // Create new PDF document
                const doc = new jsPDF();
                
                // Add title
                const now = new Date();
                const dateStr = now.toISOString().split('T')[0];
                const timeStr = now.toTimeString().split(' ')[0];
                
                doc.setFontSize(18);
                doc.text('Stock Level Report', 105, 15, { align: 'center' });
                
                doc.setFontSize(12);
                doc.text(`Generated on: ${dateStr} ${timeStr}`, 105, 25, { align: 'center' });
                
                // Add filter information
                const filterText = "<?php echo $filter === 'low_stock' ? 'Showing low stock items only (< 10)' : 'Showing all stock items'; ?>";
                doc.setFontSize(10);
                doc.text(filterText, 105, 35, { align: 'center' });
                
                // Prepare the data for table
                const tableData = [];
                const tableRows = table.querySelectorAll('tbody tr');
                
                tableRows.forEach(row => {
                    const rowData = [];
                    const cells = row.querySelectorAll('td');
                    cells.forEach(cell => {
                        rowData.push(cell.textContent.trim());
                    });
                    tableData.push(rowData);
                });
                
                // Set column headers
                const headers = [
                    'Product Name', 
                    'Category', 
                    'Size', 
                    'Quantity', 
                    'Stock Updated'
                ];
                
                // Add table to PDF
                doc.autoTable({
                    head: [headers],
                    body: tableData,
                    startY: 40,
                    styles: { fontSize: 10 },
                    columnStyles: {
                        0: { cellWidth: 50 },
                        1: { cellWidth: 40 },
                        2: { cellWidth: 20 },
                        3: { cellWidth: 20 },
                        4: { cellWidth: 40 }
                    },
                    didDrawCell: function(data) {
                        // Highlight low stock items
                        if (data.section === 'body' && data.column.index === 3 && parseInt(data.cell.raw) < 10) {
                            doc.setTextColor(255, 0, 0);
                        } else {
                            doc.setTextColor(0, 0, 0);
                        }
                    }
                });
                
                // Add footer
                const pageCount = doc.internal.getNumberOfPages();
                for (let i = 1; i <= pageCount; i++) {
                    doc.setPage(i);
                    doc.setFontSize(8);
                    doc.text(`Page ${i} of ${pageCount}`, 105, doc.internal.pageSize.height - 10, { align: 'center' });
                }
                
                // Generate filename
                const filename = `stock_report_${dateStr}.pdf`;
                
                // Save the PDF
                doc.save(filename);
                
                // Show success message
                Swal.fire({
                    icon: 'success',
                    title: 'Report Downloaded',
                    text: `Stock report has been downloaded as ${filename}`,
                    confirmButtonColor: '#3B82F6',
                });
            });
        });
    </script>
</head>

<body class="bg-gray-100">
    <?php include './includes/nav.php'; ?>
    <div class="max-w-7xl mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-6 text-center" data-aos="fade-down">Manage Stock Levels</h1>

        <div class="mb-6 flex flex-col sm:flex-row sm:justify-between sm:items-center" data-aos="fade-down">
            <form action="stocks.php" method="GET" class="flex items-center space-x-3 mb-4 sm:mb-0">
                <label for="filter" class="text-gray-700 font-medium">Filter by Stock:</label>
                <select name="filter" id="filter" class="p-2 border rounded-lg text-gray-700 focus:outline-none focus:border-blue-500">
                    <option value="all" <?php echo $filter == 'all' ? 'selected' : ''; ?>>All</option>
                    <option value="low_stock" <?php echo $filter == 'low_stock' ? 'selected' : ''; ?>>Low Stock (< 10)</option>
                </select>
                <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded-lg hover:bg-blue-600 transition ease-in-out duration-300">Filter</button>
            </form>
            
            <!-- Download PDF Report Button -->
            <button id="downloadReport" class="bg-green-500 text-white py-2 px-4 rounded-lg hover:bg-green-600 transition ease-in-out duration-300 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Download PDF Report
            </button>
        </div>

        <div class="overflow-x-auto" data-aos="fade-up">
            <table id="stockTable" class="w-full border-collapse bg-white shadow-lg rounded-lg">
                <thead>
                    <tr class="bg-gray-200 text-gray-700">
                        <th class="p-3 text-left font-semibold">
                            <a href="?sort=<?php echo $sort === 'name_asc' ? 'name_desc' : 'name_asc'; ?>&filter=<?php echo $filter; ?>" class="text-gray-700 hover:text-blue-500">Product Name</a>
                        </th>
                        <th class="p-3 text-left font-semibold">Category</th>
                        <th class="p-3 text-left font-semibold">Size</th>
                        <th class="p-3 text-left font-semibold">
                            <a href="?sort=<?php echo $sort === 'stock_asc' ? 'stock_desc' : 'stock_asc'; ?>&filter=<?php echo $filter; ?>" class="text-gray-700 hover:text-blue-500">Quantity</a>
                        </th>
                        <th class="p-3 text-left font-semibold">Stock Updated</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($product_data)): ?>
                        <tr>
                            <td colspan="5" class="p-3 text-gray-600 text-center">No products found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($product_data as $product_id => $product): ?>
                            <?php
                            $sizes = $product['sizes'];
                            if (empty($sizes)) {
                                echo '
                                <tr class="border-b hover:bg-gray-50 transition ease-in-out duration-300">
                                    <td class="p-3 text-gray-800">' . htmlspecialchars($product['name']) . '</td>
                                    <td class="p-3 text-gray-800">' . htmlspecialchars($product['category_name']) . '</td>
                                    <td class="p-3 text-gray-800">No sizes</td>
                                    <td class="p-3 text-gray-800">0</td>
                                    <td class="p-3 text-gray-800">-</td>
                                </tr>';
                            } else {
                                foreach ($sizes as $index => $size) {
                                    $stock = $size['stock'];
                                    $stock_class = $stock < 10 ? 'text-red-600 font-bold' : 'text-gray-800';
                                    $updated_at = $size['updated_at'] ? date('Y-m-d H:i:s', strtotime($size['updated_at'])) : '-';
                                    echo '
                                    <tr class="border-b hover:bg-gray-50 transition ease-in-out duration-300">
                                        <td class="p-3 text-gray-800">' . ($index === 0 ? htmlspecialchars($product['name']) : '') . '</td>
                                        <td class="p-3 text-gray-800">' . ($index === 0 ? htmlspecialchars($product['category_name']) : '') . '</td>
                                        <td class="p-3 text-gray-800">' . htmlspecialchars($size['size']) . '</td>
                                        <td class="p-3 ' . $stock_class . '">' . $stock . '</td>
                                        <td class="p-3 text-gray-800">' . $updated_at . '</td>
                                    </tr>';
                                }
                            }
                            ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>