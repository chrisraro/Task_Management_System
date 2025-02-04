<?php
// reports.php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') {
  header("Location: index.php");
  exit;
}
include('db.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Accomplishment Reports - Admin</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
  <!-- jQuery -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container-fluid">
      <a class="navbar-brand" href="dashboard.php">Task Manager - Admin</a>
      <div class="d-flex">
        <a href="dashboard.php" class="btn btn-secondary me-2">Dashboard</a>
        <a href="logout.php" class="btn btn-danger">Logout</a>
      </div>
    </div>
  </nav>
  <div class="container my-4">
    <h3>Accomplishment Reports</h3>
    <div id="reportsContainer">
      <?php
      $stmt = $pdo->query("SELECT * FROM tasks WHERE status = 'to be approve' ORDER BY created_at DESC");
      $reports = $stmt->fetchAll();
      if ($reports) {
          echo '<div class="table-responsive"><table class="table table-bordered table-hover">';
          echo '<thead class="table-light"><tr>
                  <th>Task ID</th>
                  <th>Title</th>
                  <th>Description</th>
                  <th>Report</th>
                  <th>Documentation</th>
                  <th>Action</th>
                </tr></thead><tbody>';
          foreach ($reports as $r) {
              echo '<tr>';
              echo '<td>' . htmlspecialchars($r['id']) . '</td>';
              echo '<td>' . htmlspecialchars($r['title']) . '</td>';
              echo '<td>' . htmlspecialchars($r['description']) . '</td>';
              echo '<td>' . nl2br(htmlspecialchars($r['accomplishment_report'])) . '</td>';
              echo '<td>';
              if (!empty($r['documentation'])) {
                  echo '<a href="'.$r['documentation'].'" target="_blank">View File</a>';
              } else {
                  echo 'No file';
              }
              echo '</td>';
              echo '<td><button class="btn btn-sm btn-success approve-report" data-id="'.$r['id'].'">Approve</button></td>';
              echo '</tr>';
          }
          echo '</tbody></table></div>';
      } else {
          echo '<p>No reports pending approval.</p>';
      }
      ?>
    </div>
  </div>
  <!-- Bootstrap JS Bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
  $(document).ready(function(){
    $('.approve-report').click(function(){
      var taskId = $(this).data('id');
      if(confirm("Approve this report?")){
        $.ajax({
          url: 'ajax.php',
          method: 'POST',
          data: { action: 'approve_report', task_id: taskId },
          success: function(response){
            alert(response);
            location.reload();
          }
        });
      }
    });
  });
  </script>
</body>
</html>