<?php
session_start();
include('db.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $username = trim($_POST['username']);
  $password = trim($_POST['password']);

  $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
  $stmt->execute([$username]);
  $user = $stmt->fetch();

  if ($user && password_verify($password, $user['password'])) {
      $_SESSION['user'] = [
          'id'       => $user['id'],
          'username' => $user['username'],
          'role'     => $user['role']
      ];
      header('Location: dashboard.php');
      exit;
  } else {
      header('Location: index.php?error=1');
      exit;
  }
} else {
  header('Location: index.php');
  exit;
}
?>