<?php
include 'db_connection.php';

// Fetch metrics data (example)
$metrics = [
    'Orders' => 150,
    'Sales' => 3000,
    'Products' => 500,
    'Categories' => 20 // This can be derived from the categories table as well
];

// Fetch categories and product counts
$query = "SELECT c.id, c.name, COUNT(p.id) AS product_count
          FROM categories c
          LEFT JOIN products p ON c.id = p.category_id
          GROUP BY c.id";

$result = $conn->query($query);
$categories = [];
$totalProducts = 0;

while ($row = $result->fetch_assoc()) {
    $categories[] = [
        'name' => $row['name'],
        'count' => (int)$row['product_count']
    ];
    $totalProducts += $row['product_count'];
}

// Close the database connection
$conn->close();
?>