<?php
// Database connection
$servername = "localhost";
$username = "root";  // Your MySQL username
$password = "";      // Your MySQL password
$dbname = "registration_db";  // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch categories
$sql = "SELECT * FROM categories"; // Adjust the query if you need specific fields
$result = $conn->query($sql);

$categories = [];
if ($result->num_rows > 0) {
    // Fetch all categories
    while($row = $result->fetch_assoc()) {
        $categories[] = $row; // Store each category in an array
    }
}

// Close the database connection
$conn->close();

// Convert categories to JSON format and output
header('Content-Type: application/json'); // Set the content type to JSON
echo json_encode($categories);
?>
