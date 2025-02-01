<?php   
session_start();
include('db.php');

$errors = [];
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $username     = trim($_POST['username']);
  $password     = trim($_POST['password']);
  $confirm_pass = trim($_POST['confirm_password']);
  $role         = 'employee';  // For demonstration, registration is for employees only.

  if ($password !== $confirm_pass) {
      $errors[] = "Passwords do not match.";
  }

  if(empty($errors)) {
      $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
      $stmt->execute([$username]);
      if ($stmt->fetch()) {
          $errors[] = "Username already exists.";
      } else {
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
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
</head>
<body class="bg-light">
  <div class="container">
    <div class="row justify-content-center mt-5">
      <div class="col-md-4">
        <div class="card shadow-sm">
          <div class="card-body">
            <h3 class="card-title text-center mb-4">Register</h3>
            <?php
            if(!empty($errors)) {
              foreach ($errors as $error) {
                  echo "<div class='alert alert-danger'>$error</div>";
              }
            }
            ?>
            <form method="post" action="register.php">
              <div class="mb-3">
                <label for="username" class="form-label">Username:</label>
                <input type="text" name="username" id="username" class="form-control" required>
              </div>
              <div class="mb-3">
                <label for="password" class="form-label">Password:</label>
                <input type="password" name="password" id="password" class="form-control" required>
              </div>
              <div class="mb-3">
                <label for="confirm_password" class="form-label">Confirm Password:</label>
                <input type="password" name="confirm_password" id="confirm_password" class="form-control" required>
              </div>
              <div class="d-grid">
                <input type="submit" value="Register" class="btn btn-primary">
              </div>
            </form>
            <p class="mt-3 text-center">Already have an account? <a href="index.php">Login Here</a></p>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Bootstrap JS Bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>