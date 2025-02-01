<?php
session_start();
if(isset($_SESSION['user'])){
    header('Location: dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Task Management System - Login</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="login-container">
    <h2>Login</h2>
    <?php
    if(isset($_GET['error'])){
        echo '<p class="error">Invalid username or password.</p>';
    }
    ?>
    <form method="post" action="login.php">
        <label for="username">Username:</label>
        <input type="text" name="username" id="username" required />
        
        <label for="password">Password:</label>
        <input type="password" name="password" id="password" required />
        
        <input type="submit" value="Login" />
    </form>
    <p>Don't have an account? <a href="register.php">Register Here</a></p>
</div>
</body>
</html>