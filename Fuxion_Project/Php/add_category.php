<?php
// Database connection configuration
$servername = "localhost";
$username = "root";  // Replace with your MySQL username
$password = "";      // Replace with your MySQL password
$dbname = "registration_db";  // Replace with your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Connection failed: ' . $conn->connect_error]));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $categoryName = $_POST['categoryName'];
    $categoryImage = $_FILES['categoryImage'];

    // Validate input
    if (empty($categoryName) || empty($categoryImage['name'])) {
        echo json_encode(['success' => false, 'message' => 'Please fill all fields.']);
        exit;
    }

    // Handle file upload
    $targetDir = "uploads/"; // Ensure this directory exists and is writable
    $targetFile = $targetDir . basename($categoryImage['name']);
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    // Check file size and type
    if ($categoryImage['size'] > 500000) {
        echo json_encode(['success' => false, 'message' => 'File is too large.']);
        exit;
    }
    if (!in_array($imageFileType, ['jpg', 'png', 'jpeg', 'gif'])) {
        echo json_encode(['success' => false, 'message' => 'Only JPG, JPEG, PNG & GIF files are allowed.']);
        exit;
    }

    // Move the uploaded file
    if (move_uploaded_file($categoryImage['tmp_name'], $targetFile)) {
        // Prepare and bind
        $stmt = $conn->prepare("INSERT INTO categories (name, image) VALUES (?, ?)");
        $stmt->bind_param("ss", $categoryName, $targetFile);
        
        // Execute the statement
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Category saved successfully!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Database error: ' . $stmt->error]);
        }
        
        // Close the statement
        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'File upload error.']);
    }
}

// Close the database connection
$conn->close();
?>
