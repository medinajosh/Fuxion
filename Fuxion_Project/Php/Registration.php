<?php
// Include the database connection
include 'db_connection.php';

// Initialize a message variable
$message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role']; // Capture the role input

    // Validate form input
    if (empty($username) || empty($email) || empty($password) || empty($role)) {
        $message = "All fields are required.";
    } else {
        // Now that inputs are validated, check if the user already exists (by username or email)
        $checkUserQuery = "SELECT * FROM users WHERE username='$username' OR email='$email'";
        $result = $conn->query($checkUserQuery);

        if ($result->num_rows > 0) {
            $message = "User with this username or email already exists. Please use a different one.";
        } else {
            // Hash the password for security
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Insert user data into the database, including the role
            $sql = "INSERT INTO users (username, email, password_hash, role) VALUES ('$username', '$email', '$hashed_password', '$role')";
            
            if ($conn->query($sql) === TRUE) {
                $message = "Registration successful! You can now <a href='login.php'>login</a>.";
            } else {
                $message = "Error: " . $sql . "<br>" . $conn->error;
            }
        }
    }
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Geologica:wght@100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <title>Registration</title>
    <style>
        body {
            font-family: 'Geologica', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .wrapper {
            display: flex;
            flex-direction: row;
            align-items: center;
            justify-content: flex-end; /* Change this to flex-end */
            width: 100%;
            max-width: 1200px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            height: 80%;
        }

        .image-container {
            flex: 1;
            text-align: center;
            padding-right: 20px;
        }

        .image-container img {
            width: 100%;
            max-width: 400px;
            height: auto;
            border-radius: 10px;
        }

        .register-container {
            flex: 1;
            padding: 20px;
            position: relative; /* Keep this for absolute positioning */
        }

        .register-container h2 {
            font-size: 32px;
            margin-bottom: 20px;
        }

        .register-container input[type="text"],
        .register-container input[type="email"],
        .register-container input[type="password"],
        .register-container select {
            width: 60%; /* Set the same width for all input fields and select */
            padding: 12px;
            margin-bottom: 15px;
            border: 1px solid #7A3808;
            border-radius: 15px;
            font-size: 16px;
            box-sizing: border-box; /* Ensure padding doesn't affect the width */
        }
    
        .register-container input[type="submit"] {
            background-color: #7A3808;
            color: white;
            border: none;
            cursor: pointer;
            width: 60%; /* Keep the submit button same size for a uniform look */
            padding: 12px;
            margin-bottom: 15px;
            border-radius: 15px;
            font-size: 16px;
        }

        .register-container input[type="submit"]:hover {
            background-color: #B76931;
        }

        /* Styling for the message */
        .message {
            margin-top: 15px;
            font-size: 16px;
            color: #333;
        }

        .message a {
            color: #3498db;
            text-decoration: none;
            font-weight: bold;
        }

        .message a:hover {
            text-decoration: underline;
        }

        @media (max-width: 768px) {
            .wrapper {
                flex-direction: column;
                width: 90%;
                padding: 10px;
            }

            .image-container {
                padding-right: 0;
                margin-bottom: 20px;
            }
        }

        @media (max-width: 480px) {
            .register-container input,
            .register-container select {
                padding: 10px;
                font-size: 14px;
            }

            .register-container h2 {
                font-size: 28px;
            }
        }

        .back-icon {
            position: absolute; /* Changed to absolute */
            top: 10px; /* Adjust as necessary */
            left: 1%; /* Adjust as necessary */
            text-decoration: none;
            color: #000; /* Change color as needed */
            font-size: 18px; /* Adjust icon size as needed */
        }

        .back-icon:hover {
            color: #7A3808; /* Change color on hover */
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <!-- Image container on the left side -->
        <div class="image-container">
        <a href="login.php" class="back-icon">
                <i class="fas fa-arrow-left"></i>
            </a>
            <img src="../Assets/frend.png" alt="Registration Image">
            <p style="font-size: 18px;">I like my coffee like I like myself: strong, sweet, and too hot for you.</p>
        </div>

        <!-- Registration form container on the right side -->
        <div class="register-container">
            <!-- Back Icon -->
        

            <h2 style="margin-left: 8px;">Create an Account</h2>
            <form method="post" action="Registration.php">
                <input type="text" name="username" placeholder="Username" required>
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password" required>
                <select name="role" required>
                    <option value="">Select Role</option>
                    <option value="cashier">Cashier</option>
                    <option value="admin">Admin</option>
                </select>
                <input type="submit" value="Register">
            </form>

            <!-- Display the success or error message here --> 
            <?php if (!empty($message)): ?>
                <div class="message"><?php echo $message; ?></div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
