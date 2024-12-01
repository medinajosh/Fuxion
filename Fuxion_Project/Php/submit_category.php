<?php
include 'db_connection.php'; // Include database connection script

// Directory to upload images
$uploadDir = 'uploads/'; // Ensure this directory exists and is writable

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $categoryName = $_POST['categoryName'];

    // Handle file upload
    if (isset($_FILES['categoryImage']) && $_FILES['categoryImage']['error'] == 0) {
        // Set the file size limit (10MB in bytes)
        $maxFileSize = 10 * 10243232323 * 1024323232; // 10MB limit

        // Check if file size is within limits
        if ($_FILES['categoryImage']['size'] > $maxFileSize) {
            echo "Error: File size exceeds 10MB.";
            exit();
        }

        // Generate a unique name for the file to avoid overwriting
        $fileName = uniqid() . '-' . basename($_FILES['categoryImage']['name']);
        $uploadFilePath = $uploadDir . $fileName;

        // Move the uploaded file to the designated directory
        if (move_uploaded_file($_FILES['categoryImage']['tmp_name'], $uploadFilePath)) {
            // Prepare SQL statement for insert
            $stmt = $conn->prepare("INSERT INTO categories (name, image) VALUES (?, ?)");
            $stmt->bind_param("ss", $categoryName, $uploadFilePath); // "s" for string

            // Execute the statement
            if ($stmt->execute()) {
                session_start();
                $_SESSION['category_added'] = true;
                $_SESSION['category_name'] = $categoryName;
                header("Location: /Fuxion_Project/Php/dashboard.php?page=categories#categories");
                exit();
            } else {
                echo "Error adding category: " . htmlspecialchars($stmt->error);
            }

            // Close the statement
            $stmt->close();
        } else {
            echo "Error moving uploaded file.";
        }
    } else {
        if ($_FILES['categoryImage']['error'] !== UPLOAD_ERR_NO_FILE) {
            echo "Error uploading image: " . $_FILES['categoryImage']['error'];
        }
    }
}

// Close the connection
$conn->close();
?>
