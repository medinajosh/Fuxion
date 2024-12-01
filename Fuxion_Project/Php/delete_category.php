<?php
include 'db_connection.php';

if (isset($_POST['id'])) {
    $categoryId = intval($_POST['id']); // Sanitize input
    $stmt = $conn->prepare("DELETE FROM categories WHERE id = ?");
    $stmt->bind_param("i", $categoryId);

    if ($stmt->execute()) {
        // Redirect back to the categories page after deletion
        header("Location: /Fuxion_Project/Php/dashboard.php?page=categories#categories");
        exit();
    } else {
        echo "Error deleting category: " . htmlspecialchars($stmt->error);
    }

    $stmt->close();
    $conn->close();
    exit(); // Prevent further output
}
