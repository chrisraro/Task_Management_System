<?php
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
    <link rel="stylesheet" href="css/style.css">
    <!-- Include jQuery from CDN -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="nav-left">
            <a href="dashboard.php">Home</a>
        </div>
        <div class="nav-center">
            <span id="digitalClock"></span>
        </div>
        <div class="nav-right">
            <span>Welcome, <?php echo htmlspecialchars($user['username']); ?></span>
            <a class="logout-btn" href="logout.php">Logout</a>
        </div>
    </nav>

<div class="container">
    <?php if ($user['role'] == 'admin'): ?>
    <!-- Admin view: Form to add a new task -->
    <div class="task-form">
        <h3>Add New Task</h3>
        <form id="addTaskForm">
            <label>Title:</label>
            <input type="text" name="title" id="taskTitle" required />
            
            <label>Description:</label>
            <textarea name="description" id="taskDescription" rows="3"></textarea>
            
            <label>Assign To (Employee ID):</label>
            <input type="number" name="assigned_to" id="assignedTo" required />
            
            <input type="hidden" name="action" value="add_task" />
            <button type="submit" class="btn">Add Task</button>
        </form>
    </div>
    <?php endif; ?>
    
    <!-- Tasks List -->
    <div class="task-list">
        <h3>Tasks List</h3>
        <div id="tasksContainer">
            <!-- Tasks will be loaded via AJAX -->
        </div>
    </div>
</div>

<!-- Edit Task Modal -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <span id="closeModal" class="close">&times;</span>
        <h3>Edit Task</h3>
        <form id="editTaskForm">
            <input type="hidden" name="task_id" id="editTaskId" />
            <label>Title:</label>
            <input type="text" name="title" id="editTaskTitle" required />
            
            <label>Description:</label>
            <textarea name="description" id="editTaskDescription" rows="3"></textarea>
            
            <label>Assign To (Employee ID):</label>
            <input type="number" name="assigned_to" id="editAssignedTo" required />
            
            <input type="hidden" name="action" value="update_task" />
            <button type="submit" class="btn">Update Task</button>
        </form>
    </div>
</div>

<!-- Include external JavaScript files -->
<script src="js/clock.js"></script>
<script src="js/script.js"></script>
</body>
</html>