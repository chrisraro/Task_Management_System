<?php
session_start();
include('db.php');

$errors = [];
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username       = trim($_POST['username']);
    $password       = trim($_POST['password']);
    $confirm_pass   = trim($_POST['confirm_password']);
    // For demonstration, we let users register as "employee" only.
    $role           = 'employee';  

    if ($password !== $confirm_pass) {
        $errors[] = "Passwords do not match.";
    }
    
    if(empty($errors)) {
        // Check if username already exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            $errors[] = "Username already exists.";
        } else {
            // Hash the password before storing
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
            if ($stmt->execute([$username, $hashedPassword, $role])) {
                header("Location: index.php");
                exit;
            } else {
                $errors[] = "Error registering user.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register - Task Management System</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="register-container">
    <h2>Register</h2>
    <?php
    if(!empty($errors)) {
        foreach ($errors as $error) {
            echo "<p class='error'>$error</p>";
        }
    }
    ?>
    <form method="post" action="register.php">
        <label for="username">Username:</label>
        <input type="text" name="username" id="username" required />
        
        <label for="password">Password:</label>
        <input type="password" name="password" id="password" required />
        
        <label for="confirm_password">Confirm Password:</label>
        <input type="password" name="confirm_password" id="confirm_password" required />
        
        <input type="submit" value="Register" />
    </form>
    <p>Already have an account? <a href="index.php">Login Here</a></p>
</div>
</body>
</html>