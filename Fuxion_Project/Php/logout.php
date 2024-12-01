<?php
session_start();

// Destroy the session to log out the user
session_destroy();

// Redirect to login.php
header("Location: login.php");
exit();
?>

