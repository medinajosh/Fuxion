<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

// Handle logout
if (isset($_GET['action']) && $_GET['action'] == 'logout') {
    session_destroy();
    header('Location: login.php');
    exit();
}

// Get the current page from the query parameter or default to 'home'
$current_page = isset($_GET['page']) ? $_GET['page'] : 'home';

// Example metrics (you can replace this with actual data fetching)
$metrics = [
    'Orders' => "",
    'Sales' => "",
    'Categories' => "",
    'Products' => "",
];

// Include database connection and fetch category and product data
include 'fetch_total_category.php'; // Ensure this file fetches categories with product counts

// Default values for Gross and Net Sales
$gross_sales_data = [1500, 1600, 1300, 1400]; // Replace with actual data if needed
$net_sales_data = [1000, 1200, 900, 1100]; // Replace with actual data if needed
$labels_sales = ['January', 'February', 'March', 'April']; // Example month labels
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="../Css/Fuxion.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Geologica:wght@100..900&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .chart-container {
            position: relative;
            height: 200px;
            width: 400px;
            margin: 20px auto;
        }   
    </style>
</head>
<body>
<div class="navs">
    <div class="logo-container">
        <img src="../Assets/sda.png" alt="logo" class="logo">
    </div>
    <div class="nav-padding">
        <ul>
            <li><a href="?page=home" class="nav-link <?php echo ($current_page == 'home') ? 'active' : ''; ?>"><i class="fas fa-home" style="margin-right:12px;"></i> Dashboard</a></li>
            <li><a href="?page=categories" class="nav-link <?php echo ($current_page == 'categories') ? 'active' : ''; ?>"><i class="fas fa-tags" style="margin-right:12px;"></i> Categories</a></li>
            <li><a href="?page=products" class="nav-link <?php echo ($current_page == 'products') ? 'active' : ''; ?>"><i class="fas fa-box" style="margin-right:12px;"></i> Products</a></li>
            <li><a href="?page=orders" class="nav-link <?php echo ($current_page == 'orders') ? 'active' : ''; ?>"><i class="fas fa-shopping-cart" style="margin-right:12px;"></i> Orders</a></li>
            <li><a href="?page=placeholder" class="nav-link <?php echo ($current_page == 'placeholder') ? 'active' : ''; ?>"><i class="fas fa-chart-line" style="margin-right:12px;"></i> User Management</a></li>
            <li><a href="#" class="nav-link" id="logoutLink" onclick="openLogoutModal()"><i class="fas fa-sign-out-alt" style="margin-right:12px;"></i> Logout</a></li>
        </ul>
    </div>
</div>

<div class="content">
    <!-- Logout Modal -->
    <div id="logoutModal" class="modal">
        <div class="modal-content">
            <h2>Confirm Logout</h2>
            <p>Are you sure you want to log out?</p>
            <form id="logoutForm" action="logout.php" method="POST">
                <button type="submit" class="confirm-logout">Logout</button>
                <button type="button" class="cancel-logout" onclick="closeLogoutModal()">Cancel</button>
            </form>
        </div>
    </div>

    <section id="home" style="display: <?php echo ($current_page == 'home') ? 'block' : 'none'; ?>;">
        <h1>Welcome to the Dashboard</h1>
        <div class="metrics">
            <div class="metric" style="display:none">
                  <h1>Gross Sales</h1>
                <div class="chart-container">
                    <canvas id="grossSalesChart" ></canvas>
                </div>
            </div>
            <div class="metric"  style="display:none">
                      <h1>Net Sales</h1>
                <div class="chart-container">
                </div>
            </div>
            <div class="metric">
                <h1>Products Types</h1>
                <div class="chart-container">
                    <canvas id="productsPerCategoryChart"></canvas>
                </div>
            </div>
            <div class="metric">
                <h1>Category Types</h1>
                <div class="chart-container">
                    <canvas id="categoriesChart"></canvas>
                </div>
            </div>
        </div>
    </section>

    <script>
        const categoriesData = <?php echo json_encode($categories); ?>;
        const labels = categoriesData.map(cat => cat.name);
        const dataCounts = categoriesData.map(cat => cat.count);

        // Bar Chart for Gross Sales
        const grossSalesChart = new Chart(document.getElementById('grossSalesChart'), {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($labels_sales); ?>,
                datasets: [{
                    label: 'Gross Sales',
                    data: <?php echo json_encode($gross_sales_data); ?>,
                    backgroundColor: '#36A2EB',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                },
                plugins: {
                    legend: {
                        display: true
                    },
                    tooltip: {
                        callbacks: {
                            label: function(tooltipItem) {
                                return tooltipItem.label + ': $' + tooltipItem.raw;
                            }
                        }
                    }
                }
            }
        });

        // Bar Chart for Net Sales
        const netSalesChart = new Chart(document.getElementById('netSalesChart'), {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($labels_sales); ?>,
                datasets: [{
                    label: 'Net Sales',
                    data: <?php echo json_encode($net_sales_data); ?>,
                    backgroundColor: '#FF6384',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                },
                plugins: {
                    legend: {
                        display: true
                    },
                    tooltip: {
                        callbacks: {
                            label: function(tooltipItem) {
                                return tooltipItem.label + ': $' + tooltipItem.raw;
                            }
                        }
                    }
                }
            }
        });

        // Bar Chart for Number of Products per Category
        const productsPerCategoryChart = new Chart(document.getElementById('productsPerCategoryChart'), {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Number of Products',
                    data: dataCounts,
                    backgroundColor: '#4BC0C0',
                    borderColor: '#36A2EB',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                },
                plugins: {
                    legend: {
                        display: true
                    },
                    tooltip: {
                        callbacks: {
                            label: function(tooltipItem) {
                                return tooltipItem.label + ': ' + tooltipItem.raw;
                            }
                        }
                    }
                }
            }
        });

        // Doughnut Chart for Category Distribution
        const donutChart = new Chart(document.getElementById('categoriesChart'), {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Category Distribution',
                    data: dataCounts,
                    backgroundColor: [
                        '#36A2EB',
                        '#FF6384',
                        '#FFCE56',
                        '#4BC0C0',
                        '#9966FF',
                        '#FF9F40',
                    ],
                    hoverBackgroundColor: [
                        '#36A2EB',
                        '#FF6384',
                        '#FFCE56',
                        '#4BC0C0',
                        '#9966FF',
                        '#FF9F40',
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(tooltipItem) {
                                return tooltipItem.label + ': ' + tooltipItem.raw;
                            }
                        }
                    }
                }
            }
        });

        function openLogoutModal() {
            document.getElementById('logoutModal').style.display = 'block';
        }

        function closeLogoutModal() {
            document.getElementById('logoutModal').style.display = 'none';
        }
    </script>

<?php
include 'db_connection.php'; // Include database connection script

// Directory to upload images
$uploadDir = 'uploads/'; // Ensure this directory exists and is writable

// Set the file size limit (10MB in bytes)
$maxFileSize = 10 * 1024 * 1024; // 10MB limit

// Function to handle file upload
function uploadImage($file) {
    global $uploadDir, $maxFileSize;

    // Check if file size is within limits
    if ($file['size'] > $maxFileSize) {
        return "Error: File size exceeds 10MB.";
    }

    // Generate a unique name for the file to avoid overwriting
    $fileName = uniqid() . '-' . basename($file['name']);
    $uploadFilePath = $uploadDir . $fileName;

    // Move the uploaded file to the designated directory
    if (move_uploaded_file($file['tmp_name'], $uploadFilePath)) {
        return $uploadFilePath; // Return the file path if successful
    } else {
        return "Error moving uploaded file.";
    }
}

// Handle form submission for adding categories
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['categoryName'])) {
    $categoryName = $_POST['categoryName'];

    // Handle file upload
    if (isset($_FILES['categoryImage']) && $_FILES['categoryImage']['error'] == 0) {
        $uploadResult = uploadImage($_FILES['categoryImage']);
        if (strpos($uploadResult, 'Error') === false) {
            // Insert category into the database
            $stmt = $conn->prepare("INSERT INTO categories (name, image) VALUES (?, ?)");
            $stmt->bind_param("ss", $categoryName, $uploadResult);
            if ($stmt->execute()) {
                $_SESSION['category_added'] = true;
                $_SESSION['category_name'] = $categoryName;
            } else {
                echo "Error adding category: " . htmlspecialchars($stmt->error);
            }
            $stmt->close();
        } else {
            echo $uploadResult; // Display the error message
        }
    } else {
        if ($_FILES['categoryImage']['error'] !== UPLOAD_ERR_NO_FILE) {
            echo "Error uploading image: " . $_FILES['categoryImage']['error'];
        }
    }
}

// Handle form submission for adding products
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['productName']) && isset($_POST['categoryId'])) {
    $productName = $_POST['productName'];
    $categoryId = intval($_POST['categoryId']);
    $categoryType = $_POST['categoryType'];
    $price = $_POST['price']; // Get the price from the form

    // Handle file upload for product image
    if (isset($_FILES['productImage']) && $_FILES['productImage']['error'] == 0) {
        $uploadResult = uploadImage($_FILES['productImage']);
        if (strpos($uploadResult, 'Error') === false) {
            // Insert product into the database
            $stmt = $conn->prepare("INSERT INTO products (name, category_id, image, category_type, price) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sissd", $productName, $categoryId, $uploadResult, $categoryType, $price);
            if ($stmt->execute()) {
                $_SESSION['product_added'] = true; // Set session flag
            } else {
                echo "Error adding product: " . htmlspecialchars($stmt->error);
            }
            $stmt->close();
        } else {
            echo $uploadResult; // Display the error message
        }
    } else {
        if ($_FILES['productImage']['error'] !== UPLOAD_ERR_NO_FILE) {
            echo "Error uploading image: " . $_FILES['productImage']['error'];
        }
    }
}

// Handle form submission for editing products
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['editProductId'])) {
    $editProductId = intval($_POST['editProductId']);
    $productName = $_POST['productName'];
    $categoryType = $_POST['categoryType'];
    $price = $_POST['price'];
    $imagePath = null;

    // Check if a new image is uploaded
    if (isset($_FILES['productImage']) && $_FILES['productImage']['error'] == 0) {
        $uploadResult = uploadImage($_FILES['productImage']);
        if (strpos($uploadResult, 'Error') === false) {
            $imagePath = $uploadResult; // Use the new image path
        } else {
            echo $uploadResult; // Display the error message
        }
    }

    // Update product in the database
    $stmt = $conn->prepare("UPDATE products SET name=?,category_type=?, price=? " . ($imagePath ? ", image=?" : "") . " WHERE id=?");
    if ($imagePath) {
        $stmt->bind_param("sissi", $productName,  $categoryType, $price, $imagePath, $editProductId);
    } else {
        $stmt->bind_param("sisi", $productName, $categoryType, $price, $editProductId);
    }
    if ($stmt->execute()) {
        $_SESSION['product_updated'] = true;
    } else {
        echo "Error updating product: " . htmlspecialchars($stmt->error);
    }
    $stmt->close();
}

// Fetch categories and products for display
$resultCategories = $conn->query("SELECT id, name, image FROM categories");
$resultProducts = $conn->query("SELECT p.id, p.name AS productName, p.image AS productImage, c.id AS category_id, c.name AS categoryName, p.price FROM products p JOIN categories c ON p.category_id = c.id");

$conn->close();
?>

<section id="categories" style="display: <?php echo ($current_page == 'categories') ? 'block' : 'none'; ?>;">
    <div class="unique-container">
        <h1>Categories</h1>
        <div class="button-container">
            <button id="addCategoryBtn" onclick="openCategoryModal()">Add New Category</button>
        </div>

        <div id="categoryModal" class="modal" style="display: none;z-index:1001!important;">
            <div class="modal-content" style="width:500px">
                <span class="close" onclick="closeCategoryModal()" style="margin-left:470px">&times;</span>
                <h2>Add New Category</h2>
                <form id="categoryForm" action="" method="post" enctype="multipart/form-data">
                    <label for="categoryName" style="text-align:left">Category Name</label>
                    <input type="text" id="categoryName" name="categoryName" required>
                    <label for="categoryImage" style="text-align:left">Choose an image</label>
                    <input type="file" id="categoryImage" name="categoryImage" accept="image/*" required>
                    <button type="submit">Submit</button>
                </form>
            </div>
        </div>

        <div id="categoryList">
            <table class="category-table">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Category Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $resultCategories->fetch_assoc()): ?>
                    <tr id="category-<?= $row['id'] ?>">
                        <td>
                            <img style="width:150px;height:130px" src="<?= htmlspecialchars($row['image']) ?>" class="category-image" alt="<?= htmlspecialchars($row['name']) ?>">
                        </td>
                        <td>
                            <h3><?= htmlspecialchars($row['name']) ?></h3>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <!-- Add Product Button -->
                                <button class="action-button add-product" onclick="openProductModal(<?= $row['id'] ?>, '<?= htmlspecialchars($row['name']) ?>')">
                                    Add Product
                                </button>

                                <!-- Remove Category Button -->
                                <form action="delete_category.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                    <button type="submit" class="action-button remove-category" onclick="return confirm('Are you sure you want to delete this category?');">
                                        Remove
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

    </div>
</section>

<section id="products" style="display: <?php echo ($current_page == 'products') ? 'block' : 'none'; ?>;">
<h2>Products</h2>
        <div style="display: flex; align-items: center; justify-content: space-between; width: 100%;">
            <label for="categoryFilter" style="margin-right: 10px;">Filter by Category:</label>
            <select id="categoryFilter" onchange="filterProducts()">
                <option value="">All Categories</option>
                <?php
                $resultCategories->data_seek(0); // Reset the pointer to the start
                while ($category = $resultCategories->fetch_assoc()): ?>
                    <option value="<?= $category['id'] ?>"><?= htmlspecialchars($category['name']) ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <table id="productsTable">
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Image</th>
                    <th style="width:100px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($product = $resultProducts->fetch_assoc()): ?>
                    <tr data-category-id="<?= $product['category_id'] ?>">
                        <td><?= htmlspecialchars($product['productName']) ?></td>
                        <td><?= htmlspecialchars($product['categoryName']) ?></td>
                        <td><?= htmlspecialchars($product['price']) ?></td>
                        <td>
                            <img style="width:50px;height:50px;" src="<?= htmlspecialchars($product['productImage']) ?>" alt="<?= htmlspecialchars($product['productName']) ?>">
                        </td>
                        <td style="width:100px;display:flex;align-content:left;">
                            <button  style="background-color:transparent;margin-left:-10px" onclick="openEditProductModal(<?= $product['id'] ?>, '<?= htmlspecialchars($product['productName']) ?>', <?= $product['category_id'] ?>, '<?= htmlspecialchars($product['categoryName']) ?>', <?= $product['price'] ?>, '<?= htmlspecialchars($product['productImage']) ?>')">  
                            <img src="../Assets/delete.png" style="width:25px;height:25px;"></button>
                            <form action="delete_product.php" method="POST" style="display:inline;">
                                <input type="hidden" name="id" value="<?= $product['id'] ?>">
                                <button type="submit" class="cancel-icon"  style="margin-top:25px;"onclick="return confirm('Are you sure you want to delete this product?');">
                                    <img src="../Assets/delete_icon.png" style="width:25px;height:25px;margin-left:-65px;">
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

</section>

<!-- Modal for adding products -->
<div id="productModal" class="modal" style="display: none;z-index:1001!important;"">
    <div class="modal-content" style="width:500px;z-index:1001!important;">
        <span class="close" onclick="closeProductModal()" style="margin-left:470px">&times;</span>
        <h2>Add Product to <span id="selectedCategoryName"></span></h2>
        <form id="productForm" action="add_product.php" method="post" enctype="multipart/form-data">
            <input type="hidden" id="categoryId" name="categoryId">
            <label for="productName" style="text-align:left">Product Name</label>
            <input type="text" id="productName" name="productName" required>
            <label for="categoryType" style="text-align:left">Category Type</label>
            <input type="text" id="categoryType" name="categoryType" required>
            <label for="price" style="text-align:left">Price</label>
            
            <input type="number" id="Price" name="price" step="0.01" required>
            <label for="productImage" style="text-align:left">Choose an image</label>
            <input type="file" id="productImage" name="productImage" accept="image/*" required>
            <button type="submit">Add Product</button>
        </form>
    </div>
</div>

<!-- Modal for editing products -->
<div id="editProductModal" class="modal" style="display: none; z-index:1001!important;"">
    <div class="modal-content" style="width:500px;margin-top:50px;">
        <span class="close" onclick="closeEditProductModal()" style="margin-left:470px">&times;</span>
        <h2>Edit Product</h2>
        <form id="editProductForm" action="" method="post" enctype="multipart/form-data">
            <input type="hidden" id="editProductId" name="editProductId">
            <label for="editProductName" style="text-align:left">Product Name</label>
            <input type="text" id="editProductName" name="productName" required>
            <label for="editCategoryType" style="text-align:left">Category Type</label>
            <input type="text" id="editCategoryType" name="categoryType" required>
            <label for="editPrice" style="text-align:left">Price</label>
            <input type="number" id="editPrice" name="price" step="0.01" required>
            <label for="editProductImage" style="text-align:left">Choose an image (optional)</label>
            <input type="file" id="editProductImage" name="productImage" accept="image/*">
            <button type="submit">Update Product</button>
        </form>
    </div>
</div>

<script>
function openCategoryModal() {
    document.getElementById('categoryModal').style.display = 'block';
}

function closeCategoryModal() {
    document.getElementById('categoryModal').style.display = 'none';
}

function openProductModal(categoryId, categoryName) {
    document.getElementById('categoryId').value = categoryId;
    document.getElementById('selectedCategoryName').innerText = categoryName;
    document.getElementById('categoryType').value = categoryName; // Customize this as needed
    document.getElementById('productModal').style.display = 'block';
}

function closeProductModal() {
    document.getElementById('productModal').style.display = 'none';
}

function openEditProductModal(id, name, categoryId, categoryName, price, image) {
    document.getElementById('editProductId').value = id;
    document.getElementById('editProductName').value = name;
    document.getElementById('editCategoryType').value = categoryName; // Assuming category type is the same as category name
    document.getElementById('editPrice').value = price;
    document.getElementById('editProductModal').style.display = 'block';
}

function closeEditProductModal() {
    document.getElementById('editProductModal').style.display = 'none';
}

function filterProducts() {
    var filterValue = document.getElementById('categoryFilter').value;
    var products = document.querySelectorAll('#productsTable tbody tr');

    products.forEach(function(product) {
        if (filterValue === "" || product.getAttribute('data-category-id') === filterValue) {
            product.style.display = ""; // Show the product
        } else {
            product.style.display = "none"; // Hide the product
        }
    });
}
</script>



        <section id="orders" style="display: <?php echo ($current_page == 'orders') ? 'block' : 'none'; ?>;">
        <div class="order-container">
        <div class="header-container">
            <h1>Order History</h1>
            <input type="text" class="search-bar" placeholder="Search orders...">
        </div>
        <p>View and manage your orders below. Here are your recent orders, including their statuses and total amounts:</p>
        <table>
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer Number</th>
                    <th>Order Date</th>
                    <th>Status</th>
                    <th>Total</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>#001</td>
                    <td>001</td>
                    <td>October 1, 2024</td>
                    <td>Shipped</td>
                    <td>₱150.00</td>
                    <td>
                        <button class="action-button">View Details</button>
                        <button class="action-button">Cancel Order</button>
                    </td>
                </tr>
                <tr>
                    <td>#002</td>
                    <td>002</td>
                    <td>September 30, 2024</td>
                    <td>Processing</td>
                    <td>₱200.00</td>
                    <td>
                        <button class="action-button">View Details</button>
                        <button class="action-button">Cancel Order</button>
                    </td>
                </tr>
                <tr>
                    <td>#003</td>
                    <td>003</td>
                    <td>September 28, 2024</td>
                    <td>Delivered</td>
                    <td>₱75.00</td>
                    <td>
                        <button class="action-button">View Details</button>
                        <button class="action-button">Reorder</button>
                    </td>
                </tr>
                <tr>
                    <td>#004</td>
                    <td>004</td>
                    <td>September 25, 2024</td>
                    <td>Cancelled</td>
                    <td>₱50.00</td>
                    <td>
                        <button class="action-button">View Details</button>
                        <button class="action-button" disabled>Order Cancelled</button>
                    </td>
                </tr>
                <!-- Additional orders can be added here -->
            </tbody>
        </table>
    </div>

        </section>



        <section id="sales" style="display: <?php echo ($current_page == 'sales') ? 'block' : 'none'; ?>;">
        <h1>Sales Dashboard</h1>
        <p>Analyze your sales data and performance with the visual representation of daily, weekly, and monthly sales trends. Make informed business decisions by keeping track of key performance indicators (KPIs) and sales forecasts.</p>

        <!-- Filter section to select time range -->
        <div class="sales-filter">
            <label for="timeRange">Select Time Range:</label>
            <select id="timeRange">
                <option value="daily">Daily</option>
                <option value="weekly">Weekly</option>
                <option value="monthly">Monthly</option>
            </select>
            <button type="button" id="filterButton">Apply Filter</button>
        </div>

        <!-- Sales chart container -->
        <div class="sales-chart">
            <canvas id="salesChart"></canvas>
        </div>

        <!-- Key Performance Indicators (KPIs) section -->
        <div class="sales-kpis">
            <h3>Key Performance Indicators</h3>
            <ul>
                <li>Total Sales: <span id="totalSales">$0.00</span></li>
                <li>Average Order Value: <span id="averageOrderValue">$0.00</span></li>
                <li>Total Transactions: <span id="totalTransactions">0</span></li>
            </ul>
        </div>

        <!-- Sales Summary section -->
        <div class="sales-summary">
            <h3>Sales Summary</h3>
            <p>Review your sales performance for the selected period. You can compare daily, weekly, or monthly sales to get insights into the overall trends.</p>
            <ul>
                <li>Highest Sales Day: <span id="highestSalesDay">N/A</span></li>
                <li>Lowest Sales Day: <span id="lowestSalesDay">N/A</span></li>
                <li>Overall Sales Growth: <span id="salesGrowth">0%</span></li>
            </ul>
        </div>
    </section>

<section id="placeholder" style="display: <?php echo ($current_page == 'placeholder') ? 'block' : 'none'; ?>;">
    <?php
include 'db_connection.php';

// Fetch users from the database
$sql = "SELECT * FROM users_mobile";
$result = $conn->query($sql);

$users = []; // Initialize the $users array
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row; // Populate the $users array
    }
}
$conn->close();
?>

      <h1>User Management</h1>
    <table>
        <thead>
            <tr>
 
                <th>Username</th>
                <th>Email</th>
                <th style="width:100px;">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($users)): ?>
                <?php foreach ($users as $user): ?>
                <tr>
             
                    <td><?php echo $user['username']; ?></td>
                    <td><?php echo $user['email']; ?></td>
                    <td style="align-content:center;display:flex;justify-content:space-evenly;width:100px;">
                        <a href="edit_user.php?id=<?php echo $user['id']; ?>"><img src="../Assets/delete.png" style="width:25px;height:25px;"></a>
                        <a href="delete_user.php?id=<?php echo $user['id']; ?>" onclick="return confirm('Are you sure you want to delete this user?');"><img src="../Assets/delete_icon.png" style="width:25px;height:25px;"</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4">No users found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</section>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <script src="../Script/Dashboard.js"></script>

    </body>
    </html>
