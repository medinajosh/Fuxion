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

// Handle form submission for adding products
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['productName']) && isset($_POST['categoryId']) && isset($_POST['price'])) {
    $productName = $_POST['productName'];
    $categoryId = intval($_POST['categoryId']);
    $categoryType = $_POST['categoryType'];
    $price = floatval($_POST['price']); // Ensure price is a float

    // Handle file upload for product image
    if (isset($_FILES['productImage']) && $_FILES['productImage']['error'] == 0) {
        $uploadResult = uploadImage($_FILES['productImage']);
        if (strpos($uploadResult, 'Error') === false) {
            // Insert product into the database
            $stmt = $conn->prepare("INSERT INTO products (name, category_id, image, category_type, price) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sissd", $productName, $categoryId, $uploadResult, $categoryType, $price);
            if ($stmt->execute()) {
                $_SESSION['product_added'] = true; // Set session flag
                // Redirect to avoid re-submission on refresh
                header("Location: " . $_SERVER['HTTP_REFERER']); // Redirect back to the referring page
                exit();
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

$conn->close();
?>
