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
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Optional custom CSS -->
  <link rel="stylesheet" href="css/style.css">
</head>
<body class="bg-light">
  <div class="container">
    <div class="row justify-content-center mt-5">
      <div class="col-md-4">
        <div class="card shadow-sm">
          <div class="card-body">
            <h3 class="card-title text-center mb-4">Login</h3>
            <?php
            if(isset($_GET['error'])){
              echo '<div class="alert alert-danger" role="alert">Invalid username or password.</div>';
            }
            ?>
            <form method="post" action="login.php">
              <div class="mb-3">
                <label for="username" class="form-label">Username:</label>
                <input type="text" name="username" id="username" class="form-control" required>
              </div>
              <div class="mb-3">
                <label for="password" class="form-label">Password:</label>
                <input type="password" name="password" id="password" class="form-control" required>
              </div>
              <div class="d-grid">
                <input type="submit" value="Login" class="btn btn-primary">
              </div>
            </form>
            <p class="mt-3 text-center">Don't have an account? <a href="register.php">Register Here</a></p>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Bootstrap JS Bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>