<?php
// dashboard.php
session_start();
if (!isset($_SESSION['user'])) {
  header('Location: index.php');
  exit;
}
$user = $_SESSION['user'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Dashboard - Task Management System</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Optional custom CSS -->
  <link rel="stylesheet" href="css/style.css">
  <!-- jQuery -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container-fluid">
      <a class="navbar-brand" href="dashboard.php">Task Manager</a>
      <div class="collapse navbar-collapse justify-content-center">
        <span id="digitalClock" class="text-white"></span>
      </div>
      <div class="d-flex">
        <span class="navbar-text me-3">Welcome, <?php echo htmlspecialchars($user['username']); ?></span>
        <a href="logout.php" class="btn btn-danger">Logout</a>
      </div>
    </div>
  </nav>

  <div class="container my-4">
    <?php if ($user['role'] == 'admin'): ?>
      <!-- Two-Column Layout: Row with 4:8 ratio -->
      <div class="row">
        <!-- Left Column: Add Task Form -->
        <div class="col-md-4">
          <div class="card mb-4">
            <div class="card-header">Add New Task</div>
            <div class="card-body">
              <form id="addTaskForm">
                <div class="mb-3">
                  <label for="taskTitle" class="form-label">Title:</label>
                  <input type="text" name="title" id="taskTitle" class="form-control" required>
                </div>
                <div class="mb-3">
                  <label for="taskDescription" class="form-label">Description:</label>
                  <textarea name="description" id="taskDescription" class="form-control" rows="3"></textarea>
                </div>
                <div class="mb-3">
                  <label for="assignedTo" class="form-label">Assign To (Employee ID):</label>
                  <input type="number" name="assigned_to" id="assignedTo" class="form-control" required>
                </div>
                <input type="hidden" name="action" value="add_task">
                <div class="d-grid">
                  <button type="submit" class="btn btn-primary">Add Task</button>
                </div>
              </form>
            </div>
          </div>
        </div>
        <!-- Right Column: Task List -->
        <div class="col-md-8">
          <div class="card mb-4">
            <div class="card-header">Tasks List</div>
            <div class="card-body" id="tasksContainer">
              <!-- Tasks will be loaded via AJAX -->
            </div>
          </div>
        </div>
      </div>
      <!-- Employee Pool Section -->
      <div class="card">
        <div class="card-header">Employee Pool</div>
        <div class="card-body" id="employeePoolContainer">
          <!-- Employee pool data will be loaded via AJAX -->
        </div>
      </div>
    <?php else: ?>
      <!-- For employees, you might show only their tasks -->
      <div class="card">
        <div class="card-header">My Tasks</div>
        <div class="card-body" id="tasksContainer">
          <!-- Tasks loaded via AJAX for the employee -->
        </div>
      </div>
    <?php endif; ?>
  </div>

  <!-- Edit Task Modal (Bootstrap Modal) -->
  <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <form id="editTaskForm">
          <div class="modal-header">
            <h5 class="modal-title" id="editModalLabel">Edit Task</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <input type="hidden" name="task_id" id="editTaskId">
            <div class="mb-3">
              <label for="editTaskTitle" class="form-label">Title:</label>
              <input type="text" name="title" id="editTaskTitle" class="form-control" required>
            </div>
            <div class="mb-3">
              <label for="editTaskDescription" class="form-label">Description:</label>
              <textarea name="description" id="editTaskDescription" class="form-control" rows="3"></textarea>
            </div>
            <div class="mb-3">
              <label for="editAssignedTo" class="form-label">Assign To (Employee ID):</label>
              <input type="number" name="assigned_to" id="editAssignedTo" class="form-control" required>
            </div>
            <input type="hidden" name="action" value="update_task">
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">Update Task</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Bootstrap JS Bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <!-- Custom JS Files -->
  <script src="js/clock.js"></script>
  <script src="js/script.js"></script>
</body>
</html>
