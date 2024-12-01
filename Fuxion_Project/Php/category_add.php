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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $categoryName = $_POST['categoryName'];
    $categoryImage = $_FILES['categoryImage'];

    // Image upload path
    $targetDir = "uploads/";
    $targetFile = $targetDir . basename($categoryImage["name"]);

    // Validate and upload image
    if (move_uploaded_file($categoryImage["tmp_name"], $targetFile)) {
        // Prepare SQL to insert data into the categories table
        $sql = "INSERT INTO categories (name, image) VALUES ('$categoryName', '$targetFile')";

        // Execute query
        if ($conn->query($sql) === TRUE) {
            echo "New category added successfully.";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    } else {
        echo "There was an error uploading the image.";
    }
}

// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Category</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Modal Container */
.custom-modal {
    display: none;
    position: fixed;
    z-index: 1;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.6); /* Semi-transparent background */
    justify-content: center;
    align-items: center;
}

/* Modal Content */
.modal-category-content {
    background-color: #fff;
    padding: 20px;
    border-radius: 10px;
    width: 400px;
    max-width: 90%;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1); /* Soft shadow for depth */
    position: relative;
}

/* Close Button */
.modal-category-content .close {
    position: absolute;
    top: 10px;
    right: 15px;
    font-size: 24px;
    cursor: pointer;
    color: #333;
}

/* Modal Title */
.modal-category-content h2 {
    text-align: center;
    color: #333;
    font-family: 'Arial', sans-serif;
    font-size: 24px;
    margin-bottom: 20px;
    font-weight: bold;
}

/* Form Label */
.modal-category-content label {
    display: block;
    font-size: 14px;
    margin-bottom: 8px;
    color: #555;
    font-family: 'Arial', sans-serif;
}

/* Input Fields */
.modal-category-content input[type="text"],
.modal-category-content input[type="file"] {
    width: 100%;
    padding: 10px;
    margin-bottom: 20px;
    border: 1px solid #ccc;
    border-radius: 6px;
    font-size: 14px;
    font-family: 'Arial', sans-serif;
    box-sizing: border-box;
    transition: border-color 0.3s ease;
}

/* Input Focus Effect */
.modal-category-content input[type="text"]:focus,
.modal-category-content input[type="file"]:focus {
    border-color: #66afe9;
    outline: none;
}

/* Submit Button */
.modal-category-content button[type="submit"] {
    background-color: #4CAF50;
    color: white;
    font-size: 16px;
    font-family: 'Arial', sans-serif;
    padding: 12px 20px;
    border: none;
    border-radius: 6px;
    width: 100%;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

/* Submit Button Hover Effect */
.modal-category-content button[type="submit"]:hover {
    background-color: #45a049;
}

/* Modal Responsive Design */
@media (max-width: 600px) {
    .modal-category-content {
        width: 90%;
    }
}

    </style>
</head>
<body>

<!-- Button to open the modal -->

<!-- Modal Structure -->
<div id="categoryModal" class="custom-modal">
    <div class="modal-category-content">
        <span class="close" onclick="closeCategoryModal()">&times;</span>
        <h2>Add New Category</h2>
        <form id="categoryForm" action="submit_category.php" method="post" enctype="multipart/form-data">
            <label for="categoryName">Category Name:</label>
            <input type="text" id="categoryName" name="categoryName" required>
            
            <label for="categoryImage">Choose an image:</label>
            <input type="file" id="categoryImage" name="categoryImage" accept="image/*" required>
            
            <button type="submit">Submit</button>
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

</script>
</body>
</html>
