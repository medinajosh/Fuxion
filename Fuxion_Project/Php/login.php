<?php
session_start();
include 'db_connection.php';

// Initialize an array to hold error messages
$errors = [];

// Handle logout if requested
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: login.php');
    exit();     
}

// Redirect to dashboard if already logged in
if (isset($_SESSION['username'])) {
    header('Location: dashboard.php');
    exit();
}

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Validate form input
    if (empty($username)) {
        $errors['username'] = "Username is required.";
    }
    if (empty($password)) {
        $errors['password'] = "Password is required.";
    }

    // Check if there are no errors before querying the database
    if (empty($errors)) {
        $sql = "SELECT * FROM users WHERE username = '$username'";
        $result = $conn->query($sql);
        
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            
            // Verify password
            if (password_verify($password, $user['password_hash'])) {
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                header('Location: dashboard.php');
                exit();
            } else {
                $errors['password'] = "Invalid password.";
            }
        } else {
            $errors['username'] = "No user found with that username.";
        }
    }
}

// Close the connection
$conn->close();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Geologica:wght@100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .container {
            display: flex; 
            align-items: flex-start; 
            width: 100%; 
            max-width: 1200px; 
        }

        .login-container {
            padding: 30px;
            border-radius: 10px;
            max-width: 550px;
            width: 100%;
            height: 500px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            margin-right: 200px; 
        }

        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
            font-size: 40px;
            font-family: "Geologica", sans-serif;
        }

        p {
            font-family: "Geologica", sans-serif;
            font-size: 18px;
        }

        form {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 2px solid #7A3808;
            border-radius: 15px;
            font-size: 16px;
            box-sizing: border-box;
            font-family: "Geologica", sans-serif;
        }

        input[type="submit"] {
            width: 100%;
            padding: 12px;
            background-color: #7A3808;
            color: #ffffff;
            border: none;
            border-radius: 15px;
            cursor: pointer;
            font-size: 20px;
            margin-top: 20px;
            font-family: "Geologica", sans-serif;
        }

        input[type="submit"]:hover {
            background-color: #B76931;
        }

        .password-container {
            position: relative; 
        }

        .eye-icon {
            position: absolute; 
            right: 10px; 
            top: 50%; 
            transform: translateY(-50%); 
            cursor: pointer; 
            color: #000000; 
            font-size: 18px; 
        }

        .signup-container p {
            margin-top: 20px;
            font-size: 16px;
        }

        .register-link {
            color: #3498db;
            text-decoration: none;
            font-weight: bold;
        }

        .register-link:hover {
            text-decoration: underline;
        }

        @media screen and (max-width: 500px) {
            .login-container {
                padding: 20px;
            }

            h2 {
                font-size: 20px;
            }

            input[type="submit"] {
                padding: 10px;
            }

            input[type="text"], input[type="password"] {
                padding: 10px;
            }
        }

        .design-container {
            background: #ecf0f1;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border-radius: 20px;
            width: 150%;
            max-width: 700px; 
            height: 600px;
            display: flex;
            flex-direction: column;
            align-items: center; 
            justify-content: center; 
            cursor: pointer;
        }

        .design-image {
            width: 100%;
            max-width: 400px;
            height: auto;
            border-radius: 8px;
        }

        .design-text {
            font-size: 16px;
            margin-top: 10px;
            color: #555;
            margin-left: 20px;
        }

        .error-message {
            color: red;
            font-size: 12px; 
            margin-top: 5px; 
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-container">
            <div class="welcome-message">
                <h2>Welcome!</h2>
                <p>Your satisfaction is our priority. Let us know how we can help!</p>
            </div>
            <h2>Login</h2>
            <form method="post" action="login.php">
                <div class="password-container">
                    <input type="text" name="username" placeholder="Username" required>
                    <?php if (isset($errors['username'])): ?>
                        <div class="error-message"><?= $errors['username']; ?></div>
                    <?php endif; ?>

                    <input type="password" id="password" name="password" placeholder="Password" required>
                    <?php if (isset($errors['password'])): ?>
                        <div class="error-message"><?= $errors['password']; ?></div>
                    <?php endif; ?>

                    <span id="togglePassword" class="eye-icon" onclick="togglePasswordVisibility()">
                        <i class="fas fa-eye"></i>
                    </span>
                    <input type="submit" name="login" value="Login">
                </div>  
            </form>

            <div class="signup-container">
                <p>
                    Don't you have an account? 
                    <a href="Registration.php" class="register-link">Register</a>
                </p>
            </div>
        </div>

        <div class="design-container">
            <img src="../Assets/mama.png" alt="Description of Image" class="design-image">
            <p class="design-text">There’s something about a cup of coffee that makes it a perfect time for a conversation.</p>
        </div>
    </div>

    <script>
        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('togglePassword').querySelector('i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text'; 
                toggleIcon.classList.remove('fa-eye'); 
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password'; 
                toggleIcon.classList.remove('fa-eye-slash'); 
                toggleIcon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>
